<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\OrderCreated;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Pack;
use App\Models\User;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\PaymentTransactionLogger;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class OrderService
{
    public function __construct(
        private readonly OrderNumberService $orderNumbers,
        private readonly StockService $stock,
        private readonly ShippingService $shipping,
        private readonly CartService $cartService,
        private readonly PaymentGatewayFactory $gateways,
        private readonly LoyaltyService $loyalty,
        private readonly PackService $packs,
        private readonly InvoiceService $invoices,
        private readonly PaymentTransactionLogger $paymentLog,
    ) {}

    /**
     * Persist an order (and its items) from the given cart inside a single
     * DB transaction: snapshots product data, decrements stock, applies any
     * requested points redemption and clears the cart. Throws and rolls back
     * entirely if any item is out of stock.
     */
    public function createFromCart(?User $user, Cart $cart, Address $address, PaymentMethod $paymentMethod, ?string $notes = null, int $pointsRequested = 0): Order
    {
        return DB::transaction(function () use ($user, $cart, $address, $paymentMethod, $notes, $pointsRequested) {
            $items = $cart->items()->with('product')->get();

            if ($items->isEmpty()) {
                throw new RuntimeException('Le panier est vide.');
            }

            $subtotal = $items->sum(fn ($item) => $item->product->displayPrice() * $item->quantity);
            $shippingCost = $this->shipping->calculate($subtotal);
            $packDiscount = $this->packs->cartDiscount($cart);

            // La fidélité ne s'applique qu'aux comptes clients : un invité n'a pas de solde de points.
            $redemption = $user
                ? $this->loyalty->previewRedemption($user, $subtotal, $pointsRequested)
                : ['points' => 0, 'discount' => 0.0];

            $order = Order::create([
                'user_id' => $user?->id,
                'guest_token' => $user ? null : Order::currentGuestToken(),
                'address_id' => $address->id,
                'order_number' => $this->orderNumbers->generate(),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Pending,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount' => 0,
                'points_used' => $redemption['points'],
                'points_discount' => $redemption['discount'],
                'pack_discount' => $packDiscount,
                'total' => $subtotal + $shippingCost - $redemption['discount'] - $packDiscount,
                'notes' => $notes,
            ]);

            foreach ($items as $item) {
                $product = $item->product;
                $unitPrice = $product->displayPrice();

                $this->stock->decrement($product, $item->quantity, comment: "Commande {$order->order_number}");

                $order->items()->create([
                    'product_id' => $product->id,
                    'pack_id' => $item->pack_id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'total_price' => $unitPrice * $item->quantity,
                ]);
            }

            $packIds = $items->pluck('pack_id')->filter()->unique();

            foreach ($packIds as $packId) {
                $pack = Pack::query()->find($packId);

                if ($pack) {
                    $this->packs->recordSale($pack, $this->packs->completeSetsInCart($pack, $cart));
                }
            }

            $order->statusHistories()->create([
                'status' => OrderStatus::Pending,
                'note' => 'Commande créée.',
            ]);

            try {
                $result = $this->gateways->make($paymentMethod)->charge($order);
                $this->paymentLog->record($order, $paymentMethod, $result);
            } catch (Throwable $e) {
                $this->paymentLog->recordFailure($order, $paymentMethod, $e);

                throw $e;
            }

            if ($user) {
                $this->loyalty->applyRedemption($order, $user, $redemption['points'], $redemption['discount']);
            }

            $this->cartService->clear($cart);

            $order->setRelation('user', $user);
            event(new OrderCreated($order));

            return $order->fresh(['items', 'address']);
        });
    }

    public function updateStatus(Order $order, OrderStatus $status, ?Admin $admin = null, ?string $note = null): void
    {
        DB::transaction(function () use ($order, $status, $admin, $note): void {
            $order->update(['status' => $status]);

            $order->statusHistories()->create([
                'admin_id' => $admin?->id,
                'status' => $status,
                'note' => $note,
            ]);

            if ($status === OrderStatus::Delivered) {
                $this->loyalty->awardForOrder($order);
            }

            if ($status === OrderStatus::Confirmed) {
                $this->invoices->generateForOrder($order, $admin);
            }
        });
    }
}

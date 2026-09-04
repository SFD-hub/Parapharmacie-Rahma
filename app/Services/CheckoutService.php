<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use RuntimeException;

/**
 * Orchestrates the checkout use-case: validates the cart is ready to be
 * turned into an order, then delegates the actual persistence to
 * OrderService.
 */
class CheckoutService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orders,
    ) {}

    /**
     * @throws RuntimeException|InsufficientStockException|ProductExpiredException
     */
    public function ensureCartIsReady(): void
    {
        $cart = $this->cartService->current();

        if ($cart->items()->doesntExist()) {
            throw new RuntimeException('Votre panier est vide.');
        }

        foreach ($cart->items()->with('product')->get() as $item) {
            if ($item->product->isExpired()) {
                throw new ProductExpiredException($item->product);
            }

            if ($item->quantity > $item->product->stock) {
                throw new InsufficientStockException($item->product);
            }
        }
    }

    public function placeOrder(?User $user, Address $address, PaymentMethod $paymentMethod, ?string $notes = null, int $pointsRequested = 0): Order
    {
        $this->ensureCartIsReady();

        return $this->orders->createFromCart($user, $this->cartService->current(), $address, $paymentMethod, $notes, $pointsRequested);
    }
}

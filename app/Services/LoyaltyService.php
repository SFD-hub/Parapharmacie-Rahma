<?php

namespace App\Services;

use App\Enums\LoyaltyMovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\OrderCreated;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Models\LoyaltyGift;
use App\Models\LoyaltyMovement;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Central service for the loyalty program: earning, redemption (discount or
 * gift) and balance reads. All loyalty business logic lives here — nothing
 * elsewhere should compute points directly. New strategies (bonus points,
 * multipliers, campaigns, manual attribution, expiration) can be added as
 * new methods/movement types without touching callers.
 */
class LoyaltyService
{
    public function __construct(
        private readonly LoyaltySettingsService $settings,
        private readonly StockService $stock,
        private readonly OrderNumberService $orderNumbers,
        private readonly NotificationService $notifications,
    ) {}

    public function isEnabled(): bool
    {
        return $this->settings->all()['enabled'];
    }

    /**
     * A user's current point balance: the sum of every ledger movement.
     * Always derived from the movements themselves (source of truth) rather
     * than a cached counter, so it can never drift out of sync.
     */
    public function balance(User $user): int
    {
        return (int) $user->loyaltyMovements()->sum('points');
    }

    public function monetaryValue(int $points): float
    {
        return round($points * $this->settings->all()['point_value'], 2);
    }

    /**
     * Clamp a requested points redemption against the user's balance and the
     * configured maximum percentage of the order subtotal.
     *
     * @return array{points: int, discount: float}
     */
    public function previewRedemption(User $user, float $orderSubtotal, int $requestedPoints): array
    {
        $settings = $this->settings->all();

        if (! $settings['enabled'] || ! $settings['mode']->allowsDiscount() || $requestedPoints <= 0 || $settings['point_value'] <= 0) {
            return ['points' => 0, 'discount' => 0.0];
        }

        $balance = $this->balance($user);
        $maxDiscount = $orderSubtotal * $settings['max_usage_percentage'] / 100;
        $maxPointsByValue = (int) floor($maxDiscount / $settings['point_value']);

        $points = max(0, min($requestedPoints, $balance, $maxPointsByValue));

        return ['points' => $points, 'discount' => round($points * $settings['point_value'], 2)];
    }

    public function maxRedeemablePoints(User $user, float $orderSubtotal): int
    {
        return $this->previewRedemption($user, $orderSubtotal, PHP_INT_MAX)['points'];
    }

    /**
     * Record a discount redemption against an order. Must be called from
     * within the same DB transaction as the order's creation.
     */
    public function applyRedemption(Order $order, User $user, int $points, float $discount): ?LoyaltyMovement
    {
        if ($points <= 0) {
            return null;
        }

        $movement = LoyaltyMovement::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => LoyaltyMovementType::RedeemedDiscount,
            'points' => -$points,
            'amount' => $discount,
            'description' => "Réduction appliquée à la commande {$order->order_number}.",
        ]);

        $this->notifications->notify(
            $user,
            'loyalty.points_used',
            'Points de fidélité utilisés',
            "Vous avez utilisé {$points} points (-".number_format($discount, 0, ',', ' ')." FCFA) sur la commande {$order->order_number}.",
            ['order_id' => $order->id, 'points' => $points, 'amount' => $discount],
        );

        return $movement;
    }

    /**
     * Award points for a delivered order. Guards against double-awarding
     * (points_earned starts null, set once) and against orders entirely
     * paid with points. Computed on the order's total, which already
     * excludes any points-based discount — i.e. the amount actually paid.
     * Must be called from within the same transaction as the status update.
     */
    public function awardForOrder(Order $order): ?LoyaltyMovement
    {
        if ($order->user_id === null) {
            return null;
        }

        if (! $this->isEnabled() || $order->points_earned !== null || $order->status !== OrderStatus::Delivered) {
            return null;
        }

        if ($order->payment_method === PaymentMethod::Points) {
            $order->update(['points_earned' => 0]);

            return null;
        }

        $settings = $this->settings->all();
        $eligibleAmount = (float) $order->total;

        $points = $settings['amount_per_point'] > 0
            ? (int) floor($eligibleAmount / $settings['amount_per_point']) * $settings['points_per_amount']
            : 0;

        $order->update(['points_earned' => $points]);

        $movement = null;

        if ($points > 0) {
            $movement = LoyaltyMovement::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => LoyaltyMovementType::Earned,
                'points' => $points,
                'amount' => $eligibleAmount,
                'description' => "Points gagnés sur la commande {$order->order_number}.",
            ]);

            $this->notifications->notify(
                $order->user,
                'loyalty.points_earned',
                'Points de fidélité gagnés',
                "Vous avez gagné {$points} points grâce à la commande {$order->order_number}.",
                ['order_id' => $order->id, 'points' => $points],
            );
        }

        $this->awardPackBonuses($order);
        $this->notifyIfThresholdReached($order->user);

        return $movement;
    }

    /**
     * Flat loyalty bonus configured on any pack present in the order (once
     * per distinct pack, regardless of how many sets were bought) — the
     * pack marketing feature's natural hook into the loyalty ledger.
     */
    private function awardPackBonuses(Order $order): void
    {
        $packItems = $order->items()->whereNotNull('pack_id')->with('pack')->get()->unique('pack_id');

        foreach ($packItems as $item) {
            $pack = $item->pack;

            if (! $pack || $pack->loyalty_bonus_points <= 0) {
                continue;
            }

            LoyaltyMovement::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'type' => LoyaltyMovementType::Earned,
                'points' => $pack->loyalty_bonus_points,
                'description' => "Bonus fidélité pour le pack « {$pack->name} » (commande {$order->order_number}).",
            ]);

            $this->notifications->notify(
                $order->user,
                'loyalty.points_earned',
                'Points bonus obtenus',
                "Vous avez gagné {$pack->loyalty_bonus_points} points bonus grâce au pack « {$pack->name} ».",
                ['order_id' => $order->id, 'pack_id' => $pack->id, 'points' => $pack->loyalty_bonus_points],
            );
        }
    }

    /**
     * Exchange points for a gift: creates a zero-payment order for the
     * underlying product (reusing the existing order/stock pipeline — no
     * duplicate product, no parallel fulfillment system), decrements stock
     * and records the redemption movement.
     *
     * @throws RuntimeException|InsufficientStockException
     */
    public function redeemGift(User $user, LoyaltyGift $gift): Order
    {
        $settings = $this->settings->all();

        if (! $settings['enabled'] || ! $settings['mode']->allowsGifts()) {
            throw new RuntimeException('Le programme de fidélité ne permet pas les échanges de cadeaux actuellement.');
        }

        if (! $gift->is_active) {
            throw new RuntimeException("Ce cadeau n'est plus disponible.");
        }

        if ($this->balance($user) < $gift->points_cost) {
            throw new RuntimeException("Vous n'avez pas assez de points pour ce cadeau.");
        }

        if ($gift->product->isExpired()) {
            throw new ProductExpiredException($gift->product);
        }

        return DB::transaction(function () use ($user, $gift) {
            $product = $gift->product;

            $this->stock->decrement($product, 1, comment: "Échange cadeau fidélité « {$gift->product->name} »");

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $user->addresses()->orderByDesc('is_default')->value('id'),
                'order_number' => $this->orderNumbers->generate(),
                'status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Paid,
                'payment_method' => PaymentMethod::Points,
                'subtotal' => 0,
                'shipping_cost' => 0,
                'discount' => 0,
                'points_used' => $gift->points_cost,
                'points_discount' => 0,
                'points_earned' => 0,
                'total' => 0,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => 0,
                'quantity' => 1,
                'total_price' => 0,
            ]);

            $order->statusHistories()->create([
                'status' => OrderStatus::Pending,
                'note' => 'Commande créée via échange de points de fidélité.',
            ]);

            LoyaltyMovement::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'loyalty_gift_id' => $gift->id,
                'type' => LoyaltyMovementType::RedeemedGift,
                'points' => -$gift->points_cost,
                'description' => "Échange contre le cadeau « {$product->name} ».",
            ]);

            $this->notifications->notify(
                $user,
                'loyalty.gift_redeemed',
                'Cadeau obtenu',
                "Vous avez échangé {$gift->points_cost} points contre « {$product->name} ».",
                ['order_id' => $order->id, 'gift_id' => $gift->id],
            );

            $order->setRelation('user', $user);
            event(new OrderCreated($order));

            return $order;
        });
    }

    /**
     * Simple one-time threshold notification. A configurable set of
     * thresholds/tiers can replace this later without changing callers.
     */
    private function notifyIfThresholdReached(User $user): void
    {
        $threshold = 100;
        $balance = $this->balance($user);

        if ($balance < $threshold) {
            return;
        }

        $alreadyNotified = $user->notifications()->where('type', 'loyalty.threshold_reached')->exists();

        if ($alreadyNotified) {
            return;
        }

        $this->notifications->notify(
            $user,
            'loyalty.threshold_reached',
            'Seuil de fidélité atteint',
            "Bravo, vous avez atteint {$threshold} points de fidélité !",
            ['threshold' => $threshold, 'balance' => $balance],
        );
    }
}

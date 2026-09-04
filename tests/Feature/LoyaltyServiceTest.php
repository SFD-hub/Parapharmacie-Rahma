<?php

namespace Tests\Feature;

use App\Enums\LoyaltyMovementType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Models\Address;
use App\Models\Admin;
use App\Models\LoyaltyGift;
use App\Models\LoyaltyMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\LoyaltyService;
use App\Services\LoyaltySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class LoyaltyServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): LoyaltyService
    {
        return app(LoyaltyService::class);
    }

    public function test_balance_is_the_sum_of_the_users_movements(): void
    {
        $user = User::factory()->create();
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 100]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => -30]);
        LoyaltyMovement::factory()->create(['user_id' => User::factory(), 'points' => 500]);

        $this->assertSame(70, $this->service()->balance($user));
    }

    public function test_monetary_value_uses_the_configured_point_value(): void
    {
        app(LoyaltySettingsService::class)->update(['point_value' => '10']);

        $this->assertSame(1000.0, $this->service()->monetaryValue(100));
    }

    public function test_preview_redemption_is_clamped_to_balance(): void
    {
        $user = User::factory()->create();
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 50]);

        $preview = $this->service()->previewRedemption($user, 100000, 500);

        $this->assertSame(50, $preview['points']);
    }

    public function test_preview_redemption_is_clamped_to_max_usage_percentage(): void
    {
        app(LoyaltySettingsService::class)->update(['point_value' => '10', 'max_usage_percentage' => '10']);

        $user = User::factory()->create();
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 1000]);

        // 10% of 10000 = 1000 FCFA max discount, at 10 FCFA/point => 100 points max.
        $preview = $this->service()->previewRedemption($user, 10000, 1000);

        $this->assertSame(100, $preview['points']);
        $this->assertSame(1000.0, $preview['discount']);
    }

    public function test_preview_redemption_returns_zero_when_program_disabled(): void
    {
        app(LoyaltySettingsService::class)->update(['enabled' => '0']);

        $user = User::factory()->create();
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 500]);

        $preview = $this->service()->previewRedemption($user, 10000, 100);

        $this->assertSame(0, $preview['points']);
        $this->assertSame(0.0, $preview['discount']);
    }

    public function test_apply_redemption_creates_a_movement_and_a_notification(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->service()->applyRedemption($order, $user, 50, 250.0);

        $this->assertDatabaseHas('loyalty_movements', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => LoyaltyMovementType::RedeemedDiscount->value,
            'points' => -50,
            'amount' => 250.0,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $user->id,
            'notifiable_type' => User::class,
            'type' => 'loyalty.points_used',
        ]);
    }

    public function test_award_for_order_computes_points_on_the_amount_actually_paid(): void
    {
        app(LoyaltySettingsService::class)->update(['amount_per_point' => '100', 'points_per_amount' => '1']);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 15000,
            'points_earned' => null,
        ]);

        $this->service()->awardForOrder($order);

        // 15000 / 100 = 150 points earned.
        $this->assertSame(150, $order->fresh()->points_earned);
        $this->assertDatabaseHas('loyalty_movements', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => LoyaltyMovementType::Earned->value,
            'points' => 150,
        ]);
    }

    public function test_award_for_order_never_runs_twice_for_the_same_order(): void
    {
        app(LoyaltySettingsService::class)->update(['amount_per_point' => '100', 'points_per_amount' => '1']);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 10000,
            'points_earned' => null,
        ]);

        $this->service()->awardForOrder($order);
        $this->service()->awardForOrder($order->fresh());

        $this->assertSame(1, LoyaltyMovement::where('order_id', $order->id)->count());
    }

    public function test_award_for_order_does_nothing_for_non_delivered_orders(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Confirmed,
            'total' => 10000,
            'points_earned' => null,
        ]);

        $this->service()->awardForOrder($order);

        $this->assertNull($order->fresh()->points_earned);
        $this->assertDatabaseCount('loyalty_movements', 0);
    }

    public function test_award_for_order_grants_nothing_for_orders_paid_entirely_with_points(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::Delivered,
            'payment_method' => PaymentMethod::Points,
            'total' => 0,
            'points_earned' => null,
        ]);

        $this->service()->awardForOrder($order);

        $this->assertSame(0, $order->fresh()->points_earned);
        $this->assertDatabaseCount('loyalty_movements', 0);
    }

    public function test_redeem_gift_creates_an_order_decrements_stock_and_records_a_movement(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 5]);
        $gift = LoyaltyGift::factory()->create(['product_id' => $product->id, 'points_cost' => 100]);

        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 150]);

        $order = $this->service()->redeemGift($user, $gift);

        $this->assertSame(PaymentMethod::Points, $order->payment_method);
        $this->assertSame(PaymentStatus::Paid, $order->payment_status);
        $this->assertSame(100, $order->points_used);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 1]);
        $this->assertSame(4, $product->fresh()->stock);
        $this->assertDatabaseHas('loyalty_movements', [
            'user_id' => $user->id,
            'loyalty_gift_id' => $gift->id,
            'type' => LoyaltyMovementType::RedeemedGift->value,
            'points' => -100,
        ]);
        $this->assertSame(50, $this->service()->balance($user));
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'order.created',
        ]);
    }

    public function test_redeem_gift_fails_when_balance_is_insufficient(): void
    {
        $user = User::factory()->create();
        $gift = LoyaltyGift::factory()->create(['points_cost' => 200]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 50]);

        $this->expectException(RuntimeException::class);

        $this->service()->redeemGift($user, $gift);
    }

    public function test_redeem_gift_fails_for_an_inactive_gift(): void
    {
        $user = User::factory()->create();
        $gift = LoyaltyGift::factory()->create(['points_cost' => 50, 'is_active' => false]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 500]);

        $this->expectException(RuntimeException::class);

        $this->service()->redeemGift($user, $gift);
    }

    public function test_redeem_gift_fails_when_the_product_has_no_stock(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 0]);
        $gift = LoyaltyGift::factory()->create(['product_id' => $product->id, 'points_cost' => 50]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 500]);

        $this->expectException(InsufficientStockException::class);

        $this->service()->redeemGift($user, $gift);
    }

    public function test_redeem_gift_fails_when_the_product_has_expired(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 5, 'expiry_date' => now()->subDay()]);
        $gift = LoyaltyGift::factory()->create(['product_id' => $product->id, 'points_cost' => 50]);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 500]);

        $this->expectException(ProductExpiredException::class);

        $this->service()->redeemGift($user, $gift);
    }
}

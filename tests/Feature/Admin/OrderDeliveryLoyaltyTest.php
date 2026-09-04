<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Admin;
use App\Models\Order;
use App\Services\LoyaltySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderDeliveryLoyaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_marking_an_order_as_delivered_awards_loyalty_points(): void
    {
        app(LoyaltySettingsService::class)->update(['amount_per_point' => '100', 'points_per_amount' => '1']);

        $admin = Admin::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 20000,
            'points_earned' => null,
        ]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Delivered->value,
        ]);

        $order->refresh();
        $this->assertSame(200, $order->points_earned);
        $this->assertDatabaseHas('loyalty_movements', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'points' => 200,
        ]);
    }

    public function test_moving_an_already_delivered_order_back_and_forth_does_not_award_points_twice(): void
    {
        app(LoyaltySettingsService::class)->update(['amount_per_point' => '100', 'points_per_amount' => '1']);

        $admin = Admin::factory()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 10000,
            'points_earned' => null,
        ]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), ['status' => OrderStatus::Delivered->value]);
        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), ['status' => OrderStatus::Confirmed->value]);
        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), ['status' => OrderStatus::Delivered->value]);

        $this->assertSame(1, $order->loyaltyMovements()->count());
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Admin;
use App\Models\LoyaltyMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackLoyaltyBonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivering_an_order_awards_the_packs_loyalty_bonus_once(): void
    {
        $admin = Admin::factory()->create();
        $pack = Pack::factory()->create(['loyalty_bonus_points' => 30]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 0,
            'points_earned' => null,
        ]);
        OrderItem::factory()->create(['order_id' => $order->id, 'pack_id' => $pack->id]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Delivered->value,
        ]);

        $this->assertDatabaseHas('loyalty_movements', [
            'order_id' => $order->id,
            'points' => 30,
        ]);
    }

    public function test_pack_bonus_is_awarded_once_even_with_multiple_units(): void
    {
        $admin = Admin::factory()->create();
        $pack = Pack::factory()->create(['loyalty_bonus_points' => 15]);
        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'payment_method' => PaymentMethod::CashOnDelivery,
            'total' => 0,
            'points_earned' => null,
        ]);
        OrderItem::factory()->create(['order_id' => $order->id, 'pack_id' => $pack->id]);
        OrderItem::factory()->create(['order_id' => $order->id, 'pack_id' => $pack->id]);

        $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Delivered->value,
        ]);

        $this->assertSame(1, LoyaltyMovement::where('order_id', $order->id)->where('points', 15)->count());
    }
}

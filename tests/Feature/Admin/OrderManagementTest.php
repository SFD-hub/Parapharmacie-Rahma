<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));
    }

    public function test_client_cannot_access_admin_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_order_list(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['order_number' => 'CMD-VISIBLE']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_admin_can_search_orders_by_order_number(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-FINDME']);
        Order::factory()->create(['order_number' => 'CMD-OTHER']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index', ['search' => 'FINDME']));

        $response->assertSee('CMD-FINDME')->assertDontSee('CMD-OTHER');
    }

    public function test_admin_can_search_orders_by_customer_name(): void
    {
        $admin = Admin::factory()->create();
        $customer = User::factory()->create(['name' => 'Awa Diop']);
        Order::factory()->create(['user_id' => $customer->id, 'order_number' => 'CMD-AWA']);
        Order::factory()->create(['order_number' => 'CMD-OTHER']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index', ['search' => 'Awa Diop']));

        $response->assertSee('CMD-AWA')->assertDontSee('CMD-OTHER');
    }

    public function test_admin_can_filter_orders_by_status(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-PENDING', 'status' => OrderStatus::Pending]);
        Order::factory()->create(['order_number' => 'CMD-DELIVERED', 'status' => OrderStatus::Delivered]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index', ['status' => OrderStatus::Delivered->value]));

        $response->assertSee('CMD-DELIVERED')->assertDontSee('CMD-PENDING');
    }

    public function test_admin_can_filter_orders_awaiting_preparation(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-PENDING', 'status' => OrderStatus::Pending]);
        Order::factory()->create(['order_number' => 'CMD-CONFIRMED', 'status' => OrderStatus::Confirmed]);
        Order::factory()->create(['order_number' => 'CMD-DELIVERED', 'status' => OrderStatus::Delivered]);
        Order::factory()->create(['order_number' => 'CMD-CANCELLED', 'status' => OrderStatus::Cancelled]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index', ['status' => 'to_prepare']));

        $response->assertSee('CMD-PENDING')
            ->assertSee('CMD-CONFIRMED')
            ->assertDontSee('CMD-DELIVERED')
            ->assertDontSee('CMD-CANCELLED');
    }

    public function test_admin_can_view_order_detail(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number);
    }

    public function test_admin_can_update_order_status_and_it_is_recorded_in_history(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => OrderStatus::Confirmed->value,
            'note' => 'Paiement vérifié.',
        ]);

        $response->assertRedirect();
        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);

        $history = $order->statusHistories()->first();
        $this->assertSame(OrderStatus::Confirmed, $history->status);
        $this->assertSame($admin->id, $history->admin_id);
        $this->assertSame('Paiement vérifié.', $history->note);
    }

    public function test_updating_status_requires_a_valid_enum_value(): void
    {
        $admin = Admin::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.orders.updateStatus', $order), [
            'status' => 'not-a-real-status',
        ]);

        $response->assertSessionHasErrors('status');
    }
}

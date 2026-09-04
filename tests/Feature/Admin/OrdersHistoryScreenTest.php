<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersHistoryScreenTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.orders.history'))->assertRedirect(route('admin.login'));
    }

    public function test_only_terminal_orders_are_shown(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-DELIVERED', 'status' => OrderStatus::Delivered]);
        Order::factory()->create(['order_number' => 'CMD-CANCELLED', 'status' => OrderStatus::Cancelled]);
        Order::factory()->create(['order_number' => 'CMD-REFUNDED', 'status' => OrderStatus::Refunded]);
        Order::factory()->create(['order_number' => 'CMD-PENDING', 'status' => OrderStatus::Pending]);
        Order::factory()->create(['order_number' => 'CMD-CONFIRMED', 'status' => OrderStatus::Confirmed]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.history'));

        $response->assertOk()
            ->assertSee('CMD-DELIVERED')
            ->assertSee('CMD-CANCELLED')
            ->assertSee('CMD-REFUNDED')
            ->assertDontSee('CMD-PENDING')
            ->assertDontSee('CMD-CONFIRMED');
    }

    public function test_no_validate_button_is_shown_for_terminal_orders(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Delivered]);

        $this->actingAs($admin, 'admin')->get(route('admin.orders.history'))->assertDontSee('Valider');
    }

    public function test_search_and_status_filter_work(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-A', 'status' => OrderStatus::Delivered]);
        Order::factory()->create(['order_number' => 'CMD-B', 'status' => OrderStatus::Cancelled]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.history', ['status' => OrderStatus::Cancelled->value]));

        $response->assertSee('CMD-B')->assertDontSee('CMD-A');
    }
}

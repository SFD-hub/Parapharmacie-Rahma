<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersPendingScreenTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.orders.pending'))->assertRedirect(route('admin.login'));
    }

    public function test_only_orders_needing_action_are_shown(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-PENDING', 'status' => OrderStatus::Pending]);
        Order::factory()->create(['order_number' => 'CMD-CONFIRMED', 'status' => OrderStatus::Confirmed]);
        Order::factory()->create(['order_number' => 'CMD-DELIVERED', 'status' => OrderStatus::Delivered]);
        Order::factory()->create(['order_number' => 'CMD-CANCELLED', 'status' => OrderStatus::Cancelled]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.pending'));

        $response->assertOk()
            ->assertSee('CMD-PENDING')
            ->assertSee('CMD-CONFIRMED')
            ->assertDontSee('CMD-DELIVERED')
            ->assertDontSee('CMD-CANCELLED');
    }

    public function test_row_shows_phone_item_count_and_payment_method(): void
    {
        $admin = Admin::factory()->create();
        $address = Address::factory()->create(['phone' => '77 999 88 77']);
        $order = Order::factory()->create(['status' => OrderStatus::Pending, 'address_id' => $address->id]);
        OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.orders.pending'));

        $response->assertSee('77 999 88 77')->assertSee('3');
    }

    public function test_search_filters_by_order_number(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['order_number' => 'CMD-FINDME', 'status' => OrderStatus::Pending]);
        Order::factory()->create(['order_number' => 'CMD-OTHER', 'status' => OrderStatus::Pending]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.pending', ['search' => 'FINDME']));

        $response->assertSee('CMD-FINDME')->assertDontSee('CMD-OTHER');
    }

    public function test_pending_order_shows_a_validate_button(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($admin, 'admin')->get(route('admin.orders.pending'))->assertSee('Valider');
    }

    public function test_confirmed_order_does_not_show_a_validate_button_but_offers_invoice_generation(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Confirmed]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.pending'))
            ->assertDontSee('Valider')
            ->assertSee('Générer la facture');
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.customers.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_the_customer_list_with_orders_and_spend(): void
    {
        $admin = Admin::factory()->create();
        $customer = User::factory()->create(['name' => 'Aminata Ba']);
        Order::factory()->create(['user_id' => $customer->id, 'status' => OrderStatus::Delivered, 'total' => 15000]);
        Order::factory()->create(['user_id' => $customer->id, 'status' => OrderStatus::Cancelled, 'total' => 99999]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.customers.index'));

        $response->assertOk()->assertSee('Aminata Ba')->assertSee('15 000');
    }

    public function test_admin_can_search_customers_by_name_or_email(): void
    {
        $admin = Admin::factory()->create();
        User::factory()->create(['name' => 'Client Cible', 'email' => 'cible@example.com']);
        User::factory()->create(['name' => 'Client Autre', 'email' => 'autre@example.com']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.customers.index', ['search' => 'Cible']));

        $response->assertSee('Client Cible')->assertDontSee('Client Autre');
    }

    public function test_admin_can_view_a_customer_profile_with_history_and_stats(): void
    {
        $admin = Admin::factory()->create();
        $customer = User::factory()->create(['name' => 'Aminata Ba']);
        Order::factory()->create(['user_id' => $customer->id, 'status' => OrderStatus::Delivered, 'total' => 15000]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.customers.show', $customer));

        $response->assertOk()->assertSee('Aminata Ba')->assertSee('15 000');
    }

    public function test_admin_can_save_internal_notes_for_a_customer(): void
    {
        $admin = Admin::factory()->create();
        $customer = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->patch(route('admin.customers.notes.update', $customer), ['admin_notes' => 'Préfère être livré le matin.']);

        $response->assertRedirect();
        $this->assertSame('Préfère être livré le matin.', $customer->fresh()->admin_notes);
    }
}

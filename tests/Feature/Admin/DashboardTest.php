<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\StockMovementType;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_shows_the_headline_figures(): void
    {
        $admin = Admin::factory()->create();
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 25000]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Chiffre d', false)
            ->assertSee('25 000')
            ->assertSee('Produits les plus vendus')
            ->assertSee('À surveiller');
    }

    public function test_dashboard_shows_a_stockout_forecast_for_low_stock_products(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['name' => 'Sérum Visage', 'stock' => 4]);
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => StockMovementType::Sale,
            'quantity' => -14,
            'created_at' => now()->subDays(2),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk()->assertSee('Sérum Visage')->assertSee('Rupture probable dans');
    }
}

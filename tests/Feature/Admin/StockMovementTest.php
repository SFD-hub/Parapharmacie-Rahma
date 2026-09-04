<?php

namespace Tests\Feature\Admin;

use App\Enums\StockMovementType;
use App\Models\Admin;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.stock.movements.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_the_movement_history(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['name' => 'Crème Hydratante']);
        StockMovement::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin, 'admin')->get(route('admin.stock.movements.index'))
            ->assertOk()
            ->assertSee('Crème Hydratante');
    }

    public function test_admin_can_filter_movements_by_type(): void
    {
        $admin = Admin::factory()->create();
        $sale = Product::factory()->create();
        $loss = Product::factory()->create();
        StockMovement::factory()->create(['product_id' => $sale->id, 'type' => StockMovementType::Sale, 'comment' => 'Marqueur vente']);
        StockMovement::factory()->create(['product_id' => $loss->id, 'type' => StockMovementType::Loss, 'comment' => 'Marqueur perte']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.stock.movements.index', ['type' => StockMovementType::Loss->value]));

        $response->assertSee('Marqueur perte')->assertDontSee('Marqueur vente');
    }

    public function test_create_screen_excludes_the_sale_type(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.stock.movements.create'));

        $response->assertOk()->assertDontSee(StockMovementType::Sale->label());
        $response->assertSee(StockMovementType::ManualAdjustment->label());
    }

    public function test_admin_can_record_a_manual_stock_movement(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.stock.movements.store'), [
            'product_id' => $product->id,
            'type' => StockMovementType::Loss->value,
            'quantity' => -3,
            'comment' => 'Produit endommagé',
        ]);

        $response->assertRedirect(route('admin.stock.movements.index'));
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'admin_id' => $admin->id,
            'type' => StockMovementType::Loss->value,
            'quantity' => -3,
            'comment' => 'Produit endommagé',
        ]);
    }

    public function test_manual_movement_creation_rejects_the_sale_type(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.stock.movements.store'), [
            'product_id' => $product->id,
            'type' => StockMovementType::Sale->value,
            'quantity' => -1,
        ]);

        $response->assertSessionHasErrors('type');
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_manual_movement_creation_rejects_a_zero_quantity(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.stock.movements.store'), [
            'product_id' => $product->id,
            'type' => StockMovementType::ManualAdjustment->value,
            'quantity' => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    }
}

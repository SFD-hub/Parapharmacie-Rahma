<?php

namespace Tests\Feature;

use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Admin;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): StockService
    {
        return app(StockService::class);
    }

    public function test_decrement_reduces_stock_and_records_a_sale_movement(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->service()->decrement($product, 3, StockMovementType::Sale, 'Commande CMD-1');

        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => StockMovementType::Sale->value,
            'quantity' => -3,
            'comment' => 'Commande CMD-1',
        ]);
    }

    public function test_decrement_throws_when_stock_is_insufficient(): void
    {
        $product = Product::factory()->create(['stock' => 2]);

        $this->expectException(InsufficientStockException::class);

        $this->service()->decrement($product, 5);
    }

    public function test_decrement_notifies_admins_when_stock_crosses_into_out_of_stock(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->service()->decrement($product, 1);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'stock.out_of_stock',
        ]);
    }

    public function test_decrement_notifies_admins_when_stock_crosses_into_low_stock(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 6]);

        $this->service()->decrement($product, 1);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'stock.low_stock',
        ]);
    }

    public function test_decrement_does_not_notify_again_when_already_low_stock(): void
    {
        Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 4]);

        $this->service()->decrement($product, 1);

        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_adjust_applies_a_positive_delta_and_records_a_movement(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $applied = $this->service()->adjust($product, 5, StockMovementType::ManualAdjustment, 'Réassort');

        $this->assertSame(5, $applied);
        $this->assertSame(15, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => StockMovementType::ManualAdjustment->value,
            'quantity' => 5,
            'comment' => 'Réassort',
        ]);
    }

    public function test_adjust_clamps_at_zero_when_delta_would_go_negative(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $applied = $this->service()->adjust($product, -10, StockMovementType::Loss);

        $this->assertSame(-3, $applied);
        $this->assertSame(0, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => -3,
        ]);
    }

    public function test_adjust_skips_recording_a_movement_when_the_applied_delta_is_zero(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $applied = $this->service()->adjust($product, -5, StockMovementType::Loss);

        $this->assertSame(0, $applied);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_adjust_records_the_acting_admin_on_the_movement(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->service()->adjust($product, -2, StockMovementType::Breakage, 'Casse', $admin->id);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'admin_id' => $admin->id,
            'type' => StockMovementType::Breakage->value,
            'quantity' => -2,
        ]);
    }

    public function test_correct_to_sets_the_stock_to_the_counted_value(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $applied = $this->service()->correctTo($product, 7, 'Inventaire du 01/08/2026');

        $this->assertSame(-3, $applied);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => StockMovementType::ManualAdjustment->value,
            'quantity' => -3,
            'comment' => 'Inventaire du 01/08/2026',
        ]);
    }

    public function test_correct_to_ignores_the_previous_stock_reading_and_uses_a_fresh_lock(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $stale = Product::find($product->id);

        // A concurrent sale drops the real stock to 8 after $stale was read.
        $this->service()->decrement($product, 2);

        // Correcting based on the stale in-memory instance must still land
        // on the counted value (7), not "stale(10) -> 7" blindly applied on
        // top of the already-changed row.
        $applied = $this->service()->correctTo($stale, 7);

        $this->assertSame(-1, $applied);
        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_correct_to_skips_recording_a_movement_when_the_counted_value_matches_current_stock(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $applied = $this->service()->correctTo($product, 10);

        $this->assertSame(0, $applied);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_correct_to_clamps_at_zero_for_a_negative_counted_value(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $applied = $this->service()->correctTo($product, -3);

        $this->assertSame(-5, $applied);
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_stock_movements_are_ordered_most_recent_first_on_the_product(): void
    {
        $product = Product::factory()->create(['stock' => 20]);

        $first = StockMovement::factory()->create(['product_id' => $product->id, 'created_at' => now()->subDay()]);
        $second = StockMovement::factory()->create(['product_id' => $product->id, 'created_at' => now()]);

        $this->assertSame($second->id, $product->stockMovements()->first()->id);
        $this->assertSame($first->id, $product->stockMovements()->get()->last()->id);
    }
}

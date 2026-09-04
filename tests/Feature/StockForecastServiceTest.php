<?php

namespace Tests\Feature;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockForecastService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockForecastServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): StockForecastService
    {
        return app(StockForecastService::class);
    }

    public function test_returns_zero_when_the_product_is_already_out_of_stock(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->assertSame(0, $this->service()->daysUntilStockout($product));
    }

    public function test_returns_null_when_there_is_no_recent_sales_history(): void
    {
        $product = Product::factory()->create(['stock' => 20]);

        $this->assertNull($this->service()->daysUntilStockout($product));
    }

    public function test_estimates_days_remaining_from_the_average_daily_sale_rate(): void
    {
        $product = Product::factory()->create(['stock' => 28]);

        // 28 units sold over the 28-day lookback window => 1 unit/day => 28 days left.
        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => StockMovementType::Sale,
            'quantity' => -28,
            'created_at' => now()->subDays(5),
        ]);

        $this->assertSame(28, $this->service()->daysUntilStockout($product));
    }

    public function test_ignores_sales_outside_the_lookback_window(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => StockMovementType::Sale,
            'quantity' => -100,
            'created_at' => now()->subDays(60),
        ]);

        $this->assertNull($this->service()->daysUntilStockout($product, 28));
    }

    public function test_ignores_non_sale_movements(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        StockMovement::factory()->create([
            'product_id' => $product->id,
            'type' => StockMovementType::Loss,
            'quantity' => -50,
            'created_at' => now()->subDays(2),
        ]);

        $this->assertNull($this->service()->daysUntilStockout($product));
    }
}

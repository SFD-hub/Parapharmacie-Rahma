<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): DashboardService
    {
        return app(DashboardService::class);
    }

    public function test_total_revenue_excludes_cancelled_and_refunded_orders(): void
    {
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 10000]);
        Order::factory()->create(['status' => OrderStatus::Pending, 'total' => 5000]);
        Order::factory()->create(['status' => OrderStatus::Cancelled, 'total' => 99999]);
        Order::factory()->create(['status' => OrderStatus::Refunded, 'total' => 99999]);

        $this->assertSame(15000.0, $this->service()->totalRevenue());
    }

    public function test_headline_counts(): void
    {
        Order::factory()->count(3)->create();
        Product::factory()->count(4)->create();

        $this->assertSame(3, $this->service()->ordersCount());
        $this->assertSame(User::query()->count(), $this->service()->customersCount());
        $this->assertSame(4, $this->service()->productsCount());
    }

    public function test_revenue_series_for_the_current_month_sums_orders_by_day(): void
    {
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 1000, 'created_at' => now()->startOfMonth()]);
        Order::factory()->create(['status' => OrderStatus::Delivered, 'total' => 2000, 'created_at' => now()->startOfMonth()]);

        $series = $this->service()->revenueSeries('month');

        $key = now()->startOfMonth()->format('d/m');
        $index = array_search($key, $series['labels'], true);

        $this->assertNotFalse($index);
        $this->assertSame(3000.0, $series['data'][$index]);
    }

    public function test_revenue_series_zero_fills_every_bucket_in_the_range(): void
    {
        $series = $this->service()->revenueSeries('today');

        $this->assertNotEmpty($series['labels']);
        $this->assertSame(array_fill(0, count($series['labels']), 0.0), $series['data']);
    }

    public function test_best_selling_products_are_ranked_by_total_quantity_sold(): void
    {
        $topProduct = Product::factory()->create(['name' => 'Produit populaire']);
        $lowProduct = Product::factory()->create(['name' => 'Produit discret']);

        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $topProduct->id, 'quantity' => 10]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $lowProduct->id, 'quantity' => 1]);

        $best = $this->service()->bestSellingProducts(5);
        $worst = $this->service()->worstSellingProducts(5);

        $this->assertSame($topProduct->id, $best->first()->product_id);
        $this->assertSame($lowProduct->id, $worst->first()->product_id);
    }

    public function test_best_selling_products_ignore_cancelled_orders(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create(['status' => OrderStatus::Cancelled]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 50]);

        $this->assertCount(0, $this->service()->bestSellingProducts(5));
    }

    public function test_alerts_reports_the_expected_counters(): void
    {
        Order::factory()->create(['status' => OrderStatus::Pending]);
        Order::factory()->create(['status' => OrderStatus::Confirmed]);
        Product::factory()->create(['stock' => 0]);
        Product::factory()->create(['stock' => 3]);
        Product::factory()->create(['stock' => 20, 'expiry_date' => now()->subDay()]);
        Product::factory()->create(['stock' => 20, 'expiry_date' => now()->addDays(5)]);

        $alerts = $this->service()->alerts();

        $this->assertSame(1, $alerts['pendingOrders']);
        $this->assertSame(1, $alerts['outOfStock']);
        $this->assertSame(1, $alerts['lowStock']);
    }
}

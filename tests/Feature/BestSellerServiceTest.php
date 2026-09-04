<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\BestSellerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BestSellerServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): BestSellerService
    {
        return app(BestSellerService::class);
    }

    public function test_marks_the_top_selling_products_as_best_sellers(): void
    {
        $top = Product::factory()->create(['is_best_seller' => false]);
        $low = Product::factory()->create(['is_best_seller' => false]);

        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $top->id, 'quantity' => 20]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $low->id, 'quantity' => 1]);

        $this->service()->recompute(limit: 1);

        $this->assertTrue($top->fresh()->is_best_seller);
        $this->assertFalse($low->fresh()->is_best_seller);
    }

    public function test_removes_the_flag_from_products_that_are_no_longer_top_sellers(): void
    {
        $formerBestSeller = Product::factory()->create(['is_best_seller' => true]);
        $newBestSeller = Product::factory()->create(['is_best_seller' => false]);

        $order = Order::factory()->create(['status' => OrderStatus::Delivered]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $newBestSeller->id, 'quantity' => 15]);

        $this->service()->recompute(limit: 1);

        $this->assertFalse($formerBestSeller->fresh()->is_best_seller);
        $this->assertTrue($newBestSeller->fresh()->is_best_seller);
    }

    public function test_ignores_cancelled_and_refunded_orders(): void
    {
        $product = Product::factory()->create(['is_best_seller' => false]);

        $cancelled = Order::factory()->create(['status' => OrderStatus::Cancelled]);
        OrderItem::factory()->create(['order_id' => $cancelled->id, 'product_id' => $product->id, 'quantity' => 50]);

        $this->service()->recompute(limit: 5);

        $this->assertFalse($product->fresh()->is_best_seller);
    }
}

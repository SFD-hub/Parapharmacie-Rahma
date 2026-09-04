<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\RecentlyViewedService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentlyViewedServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracking_a_product_makes_it_appear_first(): void
    {
        $service = app(RecentlyViewedService::class);
        $productA = Product::factory()->create(['is_active' => true]);
        $productB = Product::factory()->create(['is_active' => true]);

        $service->track($productA);
        $service->track($productB);

        $products = $service->products();

        $this->assertSame([$productB->id, $productA->id], $products->pluck('id')->all());
    }

    public function test_tracking_the_same_product_again_moves_it_to_front_without_duplicating(): void
    {
        $service = app(RecentlyViewedService::class);
        $productA = Product::factory()->create(['is_active' => true]);
        $productB = Product::factory()->create(['is_active' => true]);

        $service->track($productA);
        $service->track($productB);
        $service->track($productA);

        $products = $service->products();

        $this->assertSame([$productA->id, $productB->id], $products->pluck('id')->all());
        $this->assertCount(2, $products);
    }

    public function test_products_excludes_the_given_product(): void
    {
        $service = app(RecentlyViewedService::class);
        $productA = Product::factory()->create(['is_active' => true]);
        $productB = Product::factory()->create(['is_active' => true]);

        $service->track($productA);
        $service->track($productB);

        $products = $service->products($productB);

        $this->assertSame([$productA->id], $products->pluck('id')->all());
    }

    public function test_it_is_capped_to_the_configured_maximum(): void
    {
        $service = app(RecentlyViewedService::class);
        $products = Product::factory()->count(10)->create(['is_active' => true]);

        foreach ($products as $product) {
            $service->track($product);
        }

        $this->assertLessThanOrEqual(8, $service->products()->count());
    }
}

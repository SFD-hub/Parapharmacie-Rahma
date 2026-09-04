<?php

namespace Tests\Feature;

use App\Models\Pack;
use App\Models\PackItem;
use App\Models\Product;
use App\Services\CartService;
use App\Services\PackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PackServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): PackService
    {
        return app(PackService::class);
    }

    public function test_base_price_sums_products_at_their_display_price(): void
    {
        $productA = Product::factory()->create(['price' => 5000, 'sale_price' => null]);
        $productB = Product::factory()->create(['price' => 3000, 'sale_price' => null]);

        $pack = Pack::factory()->create();
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $productA->id, 'quantity' => 2]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $productB->id, 'quantity' => 1]);

        $pack->load('items.product');

        $this->assertSame(13000.0, $this->service()->basePrice($pack));
    }

    public function test_savings_is_the_difference_between_base_price_and_sale_price(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'sale_price' => null]);
        $pack = Pack::factory()->create(['sale_price' => 7000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);
        $pack->load('items.product');

        $this->assertSame(3000.0, $this->service()->savings($pack));
    }

    public function test_pack_is_unavailable_outside_its_date_window(): void
    {
        $future = Pack::factory()->create(['starts_at' => now()->addDay()]);
        $past = Pack::factory()->create(['ends_at' => now()->subDay()]);
        $current = Pack::factory()->create(['starts_at' => now()->subDay(), 'ends_at' => now()->addDay()]);

        $this->assertFalse($this->service()->isAvailable($future));
        $this->assertFalse($this->service()->isAvailable($past));
        $this->assertTrue($this->service()->isAvailable($current));
    }

    public function test_pack_is_unavailable_once_sold_out(): void
    {
        $pack = Pack::factory()->create(['max_sales' => 5, 'sales_count' => 5]);

        $this->assertFalse($this->service()->isAvailable($pack));
    }

    public function test_add_to_cart_creates_a_tagged_cart_item_per_product(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $pack = Pack::factory()->create();
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 2]);
        $pack->load('items.product');

        $cartService = app(CartService::class);
        $this->service()->addToCart($pack, $cartService);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'pack_id' => $pack->id,
            'quantity' => 2,
        ]);
    }

    public function test_add_to_cart_throws_when_pack_is_unavailable(): void
    {
        $pack = Pack::factory()->create(['is_active' => false]);
        $pack->load('items.product');

        $this->expectException(RuntimeException::class);

        $this->service()->addToCart($pack, app(CartService::class));
    }

    public function test_cart_discount_applies_for_a_complete_set(): void
    {
        $product = Product::factory()->create(['price' => 5000, 'sale_price' => null, 'stock' => 10]);
        $pack = Pack::factory()->create(['sale_price' => 4000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);
        $pack->load('items.product');

        $cartService = app(CartService::class);
        $this->service()->addToCart($pack, $cartService);
        $cart = $cartService->current();

        $this->assertSame(1000.0, $this->service()->cartDiscount($cart));
    }

    public function test_cart_discount_scales_with_multiple_complete_sets(): void
    {
        $product = Product::factory()->create(['price' => 5000, 'sale_price' => null, 'stock' => 10]);
        $pack = Pack::factory()->create(['sale_price' => 4000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);
        $pack->load('items.product');

        $cartService = app(CartService::class);
        $this->service()->addToCart($pack, $cartService);
        $this->service()->addToCart($pack, $cartService);
        $cart = $cartService->current();

        $this->assertSame(2000.0, $this->service()->cartDiscount($cart));
        $this->assertSame(2, $this->service()->completeSetsInCart($pack, $cart));
    }

    public function test_cart_discount_ignores_an_incomplete_set(): void
    {
        $productA = Product::factory()->create(['price' => 5000, 'sale_price' => null, 'stock' => 10]);
        $productB = Product::factory()->create(['price' => 3000, 'sale_price' => null, 'stock' => 10]);
        $pack = Pack::factory()->create(['sale_price' => 6000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $productA->id, 'quantity' => 1]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $productB->id, 'quantity' => 1]);
        $pack->load('items.product');

        $cartService = app(CartService::class);
        $cart = $cartService->current();

        // Only add productA tagged with the pack — the set is incomplete.
        $cartService->add($productA, 1, $pack->id);

        $this->assertSame(0.0, $this->service()->cartDiscount($cart));
    }

    public function test_record_sale_increments_the_counter(): void
    {
        $pack = Pack::factory()->create(['sales_count' => 2]);

        $this->service()->recordSale($pack, 3);

        $this->assertSame(5, $pack->fresh()->sales_count);
    }
}

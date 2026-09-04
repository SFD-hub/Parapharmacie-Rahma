<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_is_successful(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_inactive_product_returns_404(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_product_page_shows_related_products_from_same_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Related product']);
        Product::factory()->create(['name' => 'Unrelated product']);

        $this->get(route('products.show', $product))
            ->assertSee('Related product')
            ->assertDontSee('Unrelated product');
    }

    public function test_out_of_stock_product_shows_unavailable_state(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->get(route('products.show', $product))
            ->assertSee('Indisponible')
            ->assertSee('Rupture de stock');
    }

    public function test_expired_product_shows_unavailable_state(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'expiry_date' => now()->subDay()]);

        $this->get(route('products.show', $product))
            ->assertSee('Indisponible')
            ->assertSee('Produit expiré');
    }

    public function test_discounted_product_shows_sale_badge_and_struck_price(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'sale_price' => 8000]);

        $this->get(route('products.show', $product))
            ->assertSee('8 000 FCFA')
            ->assertSee('10 000 FCFA');
    }
}

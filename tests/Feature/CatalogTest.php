<?php

namespace Tests\Feature;

use App\Livewire\Catalog\ProductCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_page_is_successful(): void
    {
        $this->get(route('shop.index'))->assertOk();
    }

    public function test_catalog_only_shows_active_products(): void
    {
        Product::factory()->create(['name' => 'Active product', 'is_active' => true]);
        Product::factory()->create(['name' => 'Inactive product', 'is_active' => false]);

        Livewire::test(ProductCatalog::class)
            ->assertSee('Active product')
            ->assertDontSee('Inactive product');
    }

    public function test_search_filters_products_by_name(): void
    {
        Product::factory()->create(['name' => 'Sérum Vitamine C']);
        Product::factory()->create(['name' => 'Crème hydratante']);

        Livewire::test(ProductCatalog::class)
            ->set('search', 'Vitamine')
            ->assertSee('Sérum Vitamine C')
            ->assertDontSee('Crème hydratante');
    }

    public function test_search_matches_category_name(): void
    {
        $category = Category::factory()->create(['name' => 'Cheveux']);
        Product::factory()->create(['name' => 'Shampooing doux', 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Autre produit']);

        Livewire::test(ProductCatalog::class)
            ->set('search', 'Cheveux')
            ->assertSee('Shampooing doux')
            ->assertDontSee('Autre produit');
    }

    public function test_category_filter_scopes_results(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        Product::factory()->create(['name' => 'Product A', 'category_id' => $categoryA->id]);
        Product::factory()->create(['name' => 'Product B', 'category_id' => $categoryB->id]);

        Livewire::test(ProductCatalog::class)
            ->set('category', $categoryA->slug)
            ->assertSee('Product A')
            ->assertDontSee('Product B');
    }

    public function test_brand_filter_scopes_results(): void
    {
        $brandA = Brand::factory()->create();
        $brandB = Brand::factory()->create();
        Product::factory()->create(['name' => 'Product A', 'brand_id' => $brandA->id]);
        Product::factory()->create(['name' => 'Product B', 'brand_id' => $brandB->id]);

        Livewire::test(ProductCatalog::class)
            ->set('brands', [$brandA->slug])
            ->assertSee('Product A')
            ->assertDontSee('Product B');
    }

    public function test_price_range_filter(): void
    {
        Product::factory()->create(['name' => 'Cheap product', 'price' => 1000]);
        Product::factory()->create(['name' => 'Expensive product', 'price' => 50000]);

        Livewire::test(ProductCatalog::class)
            ->set('maxPrice', 5000)
            ->assertSee('Cheap product')
            ->assertDontSee('Expensive product');
    }

    public function test_on_sale_filter(): void
    {
        Product::factory()->create(['name' => 'On sale product', 'price' => 10000, 'sale_price' => 8000]);
        Product::factory()->create(['name' => 'Regular product', 'price' => 10000, 'sale_price' => null]);

        Livewire::test(ProductCatalog::class)
            ->set('onSale', true)
            ->assertSee('On sale product')
            ->assertDontSee('Regular product');
    }

    public function test_in_stock_filter(): void
    {
        Product::factory()->create(['name' => 'In stock product', 'stock' => 10]);
        Product::factory()->create(['name' => 'Out of stock product', 'stock' => 0]);

        Livewire::test(ProductCatalog::class)
            ->set('inStock', true)
            ->assertSee('In stock product')
            ->assertDontSee('Out of stock product');
    }

    public function test_sort_by_price_ascending_orders_results(): void
    {
        Product::factory()->create(['name' => 'Expensive', 'price' => 50000]);
        Product::factory()->create(['name' => 'Cheap', 'price' => 1000]);

        $html = Livewire::test(ProductCatalog::class)->set('sort', 'price_asc')->html();

        $this->assertLessThan(strpos($html, 'Expensive'), strpos($html, 'Cheap'));
    }

    public function test_reset_filters_clears_all_state(): void
    {
        Livewire::test(ProductCatalog::class)
            ->set('search', 'test')
            ->set('onSale', true)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('onSale', false);
    }

    public function test_empty_state_is_shown_when_no_products_match(): void
    {
        Product::factory()->create(['name' => 'Existing product']);

        Livewire::test(ProductCatalog::class)
            ->set('search', 'nonexistentproductxyz')
            ->assertSee('Aucun résultat');
    }

    public function test_catalog_paginates_results(): void
    {
        Product::factory()
            ->count(13)
            ->sequence(fn ($sequence) => ['name' => "Product {$sequence->index}", 'price' => ($sequence->index + 1) * 1000])
            ->create();

        Livewire::test(ProductCatalog::class)
            ->set('sort', 'price_asc')
            ->assertSee('Product 0')
            ->assertSee('Product 11')
            ->assertDontSee('Product 12');
    }

    public function test_changing_a_filter_resets_pagination_to_the_first_page(): void
    {
        Product::factory()
            ->count(13)
            ->sequence(fn ($sequence) => ['name' => "Product {$sequence->index}", 'price' => ($sequence->index + 1) * 1000])
            ->create();

        Livewire::test(ProductCatalog::class)
            ->set('sort', 'price_asc')
            ->call('nextPage')
            ->assertSee('Product 12')
            ->set('sort', 'price_desc')
            ->assertSee('Product 12')
            ->assertDontSee('Product 0');
    }
}

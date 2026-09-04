<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMarketingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_shows_its_complementary_products(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $complement = Product::factory()->create(['is_active' => true, 'name' => 'Sérum complémentaire']);
        $product->complements()->attach($complement->id, ['position' => 0]);

        $this->get(route('products.show', $product))->assertSee('Sérum complémentaire');
    }

    public function test_inactive_complements_are_not_shown(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $complement = Product::factory()->create(['is_active' => false, 'name' => 'Produit masqué']);
        $product->complements()->attach($complement->id, ['position' => 0]);

        $this->get(route('products.show', $product))->assertDontSee('Produit masqué');
    }

    public function test_viewing_a_product_adds_it_to_recently_viewed_shown_on_another_product(): void
    {
        $viewed = Product::factory()->create(['is_active' => true, 'name' => 'Produit vu récemment']);
        $other = Product::factory()->create(['is_active' => true]);

        $this->get(route('products.show', $viewed));
        $response = $this->get(route('products.show', $other));

        $response->assertSee('Produit vu récemment');
    }

    public function test_the_currently_viewed_product_does_not_appear_in_its_own_recently_viewed_section(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->get(route('products.show', $product));
        $response = $this->get(route('products.show', $product));

        $response->assertOk()->assertDontSee('Récemment consultés');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_successful(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
    }

    public function test_homepage_displays_only_active_new_and_best_seller_products(): void
    {
        Product::factory()->create(['name' => 'Nouveau Visible', 'is_new' => true]);
        Product::factory()->create(['name' => 'Vente Visible', 'is_best_seller' => true]);
        Product::factory()->create(['name' => 'Ni Nouveau Ni Vendu', 'is_new' => false, 'is_best_seller' => false]);
        Product::factory()->create(['name' => 'Nouveau Inactif', 'is_new' => true, 'is_active' => false]);

        $response = $this->get(route('home'));

        $response->assertSee('Nouveau Visible');
        $response->assertSee('Vente Visible');
        $response->assertDontSee('Ni Nouveau Ni Vendu');
        $response->assertDontSee('Nouveau Inactif');
    }

    public function test_homepage_does_not_issue_one_query_per_product(): void
    {
        Product::factory()->count(8)->create(['is_new' => true])->each(function (Product $product): void {
            $product->images()->create(['path' => 'demo.svg', 'is_primary' => true, 'position' => 1]);
        });

        DB::enableQueryLog();
        $this->get(route('home'));
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertLessThan(20, $queryCount, 'Homepage should not issue one query per product (N+1).');
    }
}

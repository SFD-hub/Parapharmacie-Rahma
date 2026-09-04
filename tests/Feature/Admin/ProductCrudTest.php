<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_product_list(): void
    {
        $admin = Admin::factory()->create();
        Product::factory()->create(['name' => 'Sensibio H2O']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Sensibio H2O');
    }

    public function test_admin_can_filter_products_by_category(): void
    {
        $admin = Admin::factory()->create();
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        Product::factory()->create(['name' => 'Product A', 'category_id' => $categoryA->id]);
        Product::factory()->create(['name' => 'Product B', 'category_id' => $categoryB->id]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index', ['category' => $categoryA->id]));

        $response->assertSee('Product A')->assertDontSee('Product B');
    }

    public function test_admin_can_filter_products_by_low_stock(): void
    {
        $admin = Admin::factory()->create();
        Product::factory()->create(['name' => 'Low stock product', 'stock' => 2]);
        Product::factory()->create(['name' => 'Well stocked product', 'stock' => 50]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index', ['stock' => 'low']));

        $response->assertSee('Low stock product')->assertDontSee('Well stocked product');
    }

    public function test_admin_can_create_a_product(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Nouveau produit',
            'price' => 9900,
            'stock' => 10,
            'is_active' => '1',
        ]);

        $product = Product::firstWhere('name', 'Nouveau produit');
        $this->assertNotNull($product);
        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertDatabaseHas('products', ['name' => 'Nouveau produit', 'slug' => 'nouveau-produit']);
    }

    public function test_product_creation_requires_category_brand_and_price(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'Incomplet',
        ]);

        $response->assertSessionHasErrors(['category_id', 'brand_id', 'price', 'stock']);
    }

    public function test_sale_price_must_be_lower_than_price(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Produit promo invalide',
            'price' => 5000,
            'sale_price' => 6000,
            'stock' => 5,
        ]);

        $response->assertSessionHasErrors('sale_price');
    }

    public function test_admin_can_update_a_product(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['name' => 'Old name']);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => 'New name',
            'price' => $product->price,
            'stock' => $product->stock,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New name']);
    }

    public function test_unchecking_a_flag_actually_clears_it(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['is_featured' => true]);

        $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            // is_featured intentionally omitted, simulating an unchecked checkbox
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_featured' => false]);
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.products.toggle', $product));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_a_product(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin')->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}

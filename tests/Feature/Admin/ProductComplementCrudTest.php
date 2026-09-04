<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductComplementCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $product = Product::factory()->create();

        $this->get(route('admin.products.complements.index', $product))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_associate_a_complementary_product(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $complement = Product::factory()->create(['name' => 'Sérum complémentaire']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.complements.store', $product), [
            'complementary_product_id' => $complement->id,
        ]);

        $response->assertRedirect(route('admin.products.complements.index', $product));
        $this->assertDatabaseHas('product_complements', ['product_id' => $product->id, 'complementary_product_id' => $complement->id]);
    }

    public function test_a_product_cannot_be_its_own_complement(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.complements.store', $product), [
            'complementary_product_id' => $product->id,
        ]);

        $response->assertSessionHasErrors('complementary_product_id');
    }

    public function test_the_same_complement_cannot_be_associated_twice(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $complement = Product::factory()->create();
        $product->complements()->attach($complement->id, ['position' => 0]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.complements.store', $product), [
            'complementary_product_id' => $complement->id,
        ]);

        $response->assertSessionHasErrors('complementary_product_id');
    }

    public function test_admin_can_remove_an_association(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $complement = Product::factory()->create();
        $product->complements()->attach($complement->id, ['position' => 0]);

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.products.complements.destroy', [$product, $complement]));

        $response->assertRedirect(route('admin.products.complements.index', $product));
        $this->assertDatabaseMissing('product_complements', ['product_id' => $product->id, 'complementary_product_id' => $complement->id]);
    }
}

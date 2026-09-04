<?php

namespace Tests\Feature\Admin;

use App\Livewire\Catalog\ProductCatalog;
use App\Models\Admin;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrandCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.brands.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_brand_list(): void
    {
        $admin = Admin::factory()->create();
        Brand::factory()->create(['name' => 'Bioderma']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.brands.index'))
            ->assertOk()
            ->assertSee('Bioderma');
    }

    public function test_admin_can_create_a_brand(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.brands.store'), [
            'name' => 'Avène',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.brands.index'));
        $this->assertDatabaseHas('brands', ['name' => 'Avène', 'is_active' => true]);
    }

    public function test_brand_creation_requires_a_name(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.brands.store'), []);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_a_brand(): void
    {
        $admin = Admin::factory()->create();
        $brand = Brand::factory()->create(['name' => 'Old']);

        $this->actingAs($admin, 'admin')->put(route('admin.brands.update', $brand), [
            'name' => 'New',
            'is_active' => '1',
        ])->assertRedirect(route('admin.brands.index'));

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'New']);
    }

    public function test_admin_can_toggle_brand_status(): void
    {
        $admin = Admin::factory()->create();
        $brand = Brand::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.brands.toggle', $brand));

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_a_brand(): void
    {
        $admin = Admin::factory()->create();
        $brand = Brand::factory()->create();

        $this->actingAs($admin, 'admin')->delete(route('admin.brands.destroy', $brand))
            ->assertRedirect(route('admin.brands.index'));

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    public function test_creating_a_brand_invalidates_the_public_catalog_cache(): void
    {
        $admin = Admin::factory()->create();

        // Warm the public catalog's cached brand list before the new brand exists.
        Livewire::test(ProductCatalog::class)->assertDontSee('Nouvelle marque');

        $this->actingAs($admin, 'admin')->post(route('admin.brands.store'), [
            'name' => 'Nouvelle marque',
            'is_active' => '1',
        ]);

        Livewire::test(ProductCatalog::class)->assertSee('Nouvelle marque');
    }
}

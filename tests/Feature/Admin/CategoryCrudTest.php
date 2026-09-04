<?php

namespace Tests\Feature\Admin;

use App\Livewire\Catalog\ProductCatalog;
use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('admin.login'));
    }

    public function test_client_cannot_access_admin_category_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.categories.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_category_list(): void
    {
        $admin = Admin::factory()->create();
        Category::factory()->create(['name' => 'Visage']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee('Visage');
    }

    public function test_admin_can_search_categories(): void
    {
        $admin = Admin::factory()->create();
        Category::factory()->create(['name' => 'Visage']);
        Category::factory()->create(['name' => 'Cheveux']);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.categories.index', ['search' => 'Visage']));

        $response->assertSee('Visage')->assertDontSee('Cheveux');
    }

    public function test_admin_can_create_a_category(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), [
            'name' => 'Soins du corps',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Soins du corps',
            'slug' => 'soins-du-corps',
            'is_active' => true,
        ]);
    }

    public function test_category_creation_requires_a_name(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), []);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_duplicate_category_names_receive_a_unique_slug(): void
    {
        $admin = Admin::factory()->create();
        Category::factory()->create(['name' => 'Visage', 'slug' => 'visage']);

        $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), [
            'name' => 'Visage',
            'is_active' => '1',
        ]);

        $this->assertDatabaseHas('categories', ['slug' => 'visage-1']);
    }

    public function test_admin_can_update_a_category(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create(['name' => 'Old name']);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.categories.update', $category), [
            'name' => 'New name',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New name']);
    }

    public function test_category_cannot_be_its_own_parent(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->put(route('admin.categories.update', $category), [
            'name' => $category->name,
            'parent_id' => $category->id,
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('parent_id');
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.categories.toggle', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_a_category(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_creating_a_category_invalidates_the_public_catalog_cache(): void
    {
        $admin = Admin::factory()->create();

        // Warm the public catalog's cached category list before the new category exists.
        Livewire::test(ProductCatalog::class)->assertDontSee('Nouvelle catégorie');

        $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), [
            'name' => 'Nouvelle catégorie',
            'is_active' => '1',
        ]);

        Livewire::test(ProductCatalog::class)->assertSee('Nouvelle catégorie');
    }
}

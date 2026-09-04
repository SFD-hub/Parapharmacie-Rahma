<?php

namespace Tests\Feature\Admin;

use App\Enums\PackBadge;
use App\Models\Admin;
use App\Models\Pack;
use App\Models\PackItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.marketing.packs.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_pack_list(): void
    {
        $admin = Admin::factory()->create();
        $pack = Pack::factory()->create(['name' => 'Pack visage']);

        $this->actingAs($admin, 'admin')->get(route('admin.marketing.packs.index'))
            ->assertOk()
            ->assertSee('Pack visage');
    }

    public function test_admin_can_create_a_pack_with_items(): void
    {
        $admin = Admin::factory()->create();
        $productA = Product::factory()->create();
        $productB = Product::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.marketing.packs.store'), [
            'name' => 'Pack découverte',
            'sale_price' => 15000,
            'badge' => PackBadge::New->value,
            'is_active' => '1',
            'items' => [
                ['product_id' => $productA->id, 'quantity' => 1],
                ['product_id' => $productB->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('admin.marketing.packs.index'));
        $this->assertDatabaseHas('packs', ['name' => 'Pack découverte', 'sale_price' => 15000]);
        $pack = Pack::where('name', 'Pack découverte')->first();
        $this->assertSame(2, $pack->items()->count());
        $this->assertDatabaseHas('pack_items', ['pack_id' => $pack->id, 'product_id' => $productB->id, 'quantity' => 2]);
    }

    public function test_pack_creation_requires_at_least_one_item(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.marketing.packs.store'), [
            'name' => 'Pack vide',
            'sale_price' => 1000,
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_admin_can_update_a_packs_items(): void
    {
        $admin = Admin::factory()->create();
        $original = Product::factory()->create();
        $replacement = Product::factory()->create();
        $pack = Pack::factory()->create();
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $original->id]);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.marketing.packs.update', $pack), [
            'name' => $pack->name,
            'sale_price' => $pack->sale_price,
            'items' => [
                ['product_id' => $replacement->id, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect(route('admin.marketing.packs.index'));
        $this->assertDatabaseMissing('pack_items', ['pack_id' => $pack->id, 'product_id' => $original->id]);
        $this->assertDatabaseHas('pack_items', ['pack_id' => $pack->id, 'product_id' => $replacement->id]);
    }

    public function test_admin_can_toggle_pack_status(): void
    {
        $admin = Admin::factory()->create();
        $pack = Pack::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.marketing.packs.toggle', $pack));

        $this->assertDatabaseHas('packs', ['id' => $pack->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_a_pack(): void
    {
        $admin = Admin::factory()->create();
        $pack = Pack::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.marketing.packs.destroy', $pack));

        $response->assertRedirect(route('admin.marketing.packs.index'));
        $this->assertDatabaseMissing('packs', ['id' => $pack->id]);
    }

    public function test_deleting_a_pack_does_not_delete_its_products(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $pack = Pack::factory()->create();
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id]);

        $this->actingAs($admin, 'admin')->delete(route('admin.marketing.packs.destroy', $pack));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}

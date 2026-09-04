<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\LoyaltyGift;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyGiftCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get(route('admin.loyalty.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_gift_list(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create(['name' => 'Baume à lèvres']);
        LoyaltyGift::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin, 'admin')->get(route('admin.loyalty.index'))
            ->assertOk()
            ->assertSee('Baume à lèvres');
    }

    public function test_admin_can_create_a_gift(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.loyalty.gifts.store'), [
            'product_id' => $product->id,
            'points_cost' => 120,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.loyalty.index', ['tab' => 'gifts']));
        $this->assertDatabaseHas('loyalty_gifts', ['product_id' => $product->id, 'points_cost' => 120, 'is_active' => true]);
    }

    public function test_gift_creation_requires_an_existing_product(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.loyalty.gifts.store'), [
            'product_id' => 999999,
            'points_cost' => 50,
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_admin_can_update_a_gift(): void
    {
        $admin = Admin::factory()->create();
        $gift = LoyaltyGift::factory()->create(['points_cost' => 50]);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.loyalty.gifts.update', $gift), [
            'product_id' => $gift->product_id,
            'points_cost' => 200,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.loyalty.index', ['tab' => 'gifts']));
        $this->assertDatabaseHas('loyalty_gifts', ['id' => $gift->id, 'points_cost' => 200]);
    }

    public function test_admin_can_toggle_gift_status(): void
    {
        $admin = Admin::factory()->create();
        $gift = LoyaltyGift::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')->patch(route('admin.loyalty.gifts.toggle', $gift));

        $this->assertDatabaseHas('loyalty_gifts', ['id' => $gift->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_a_gift(): void
    {
        $admin = Admin::factory()->create();
        $gift = LoyaltyGift::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.loyalty.gifts.destroy', $gift));

        $response->assertRedirect(route('admin.loyalty.index', ['tab' => 'gifts']));
        $this->assertDatabaseMissing('loyalty_gifts', ['id' => $gift->id]);
    }

    public function test_deleting_a_gift_does_not_delete_the_underlying_product(): void
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $gift = LoyaltyGift::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin, 'admin')->delete(route('admin.loyalty.gifts.destroy', $gift));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}

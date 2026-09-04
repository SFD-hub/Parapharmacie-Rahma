<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_a_product_to_the_cart(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 2]);
    }

    public function test_adding_the_same_product_twice_increments_quantity(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 3]);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 5]);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_adding_a_product_caps_the_quantity_to_available_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 10]);

        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'quantity' => 3]);
    }

    public function test_adding_an_out_of_stock_product_fails_gracefully(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $response = $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['product_id' => $product->id]);
    }

    public function test_guest_cart_persists_across_requests(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $this->get(route('cart.index'))->assertSee($product->name);
    }

    public function test_user_can_update_the_quantity_of_a_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->patch(route('cart.update', $item), ['quantity' => 4])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 4]);
    }

    public function test_updating_quantity_beyond_stock_is_capped(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->patch(route('cart.update', $item), ['quantity' => 99]);

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 5]);
    }

    public function test_setting_quantity_to_zero_removes_the_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->actingAs($user)->patch(route('cart.update', $item), ['quantity' => 0]);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_user_can_remove_a_cart_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->actingAs($user)->delete(route('cart.destroy', $item))->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_user_can_clear_the_entire_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

        $this->actingAs($user)->delete(route('cart.clear'))->assertRedirect();

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_user_cannot_modify_another_users_cart_item(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $owner->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->actingAs($intruder)
            ->patch(route('cart.update', $item), ['quantity' => 5])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete(route('cart.destroy', $item))
            ->assertForbidden();
    }

    public function test_cart_is_restored_when_the_user_logs_back_in(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);
        $this->post(route('logout'));

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $this->get(route('cart.index'))->assertSee($product->name);
    }

    public function test_guest_cart_is_merged_into_the_users_cart_on_login(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $userCart = Cart::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($userCart);
        $this->assertSame(2, $userCart->items()->where('product_id', $product->id)->first()?->quantity);
    }

    public function test_guest_cart_merge_sums_quantities_and_respects_stock(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $product = Product::factory()->create(['stock' => 3]);

        $userCart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $userCart->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 2]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $this->assertSame(3, $userCart->items()->where('product_id', $product->id)->first()?->quantity);
        $this->assertDatabaseCount('carts', 1);
    }
}

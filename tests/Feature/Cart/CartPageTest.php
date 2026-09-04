<?php

namespace Tests\Feature\Cart;

use App\Livewire\Cart\CartPage;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_quantity_of_an_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user);

        Livewire::test(CartPage::class)
            ->call('updateQuantity', $item->id, 3)
            ->assertDispatched('cart-updated');

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);
    }

    public function test_it_removes_an_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $this->actingAs($user);

        Livewire::test(CartPage::class)->call('removeItem', $item->id);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_it_clears_the_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

        $this->actingAs($user);

        Livewire::test(CartPage::class)->call('clearCart');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_it_cannot_touch_an_item_from_another_cart(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $owner->id]);
        $item = CartItem::factory()->create(['cart_id' => $cart->id, 'quantity' => 1]);

        $this->actingAs($intruder);

        Livewire::test(CartPage::class)->call('updateQuantity', $item->id, 5);

        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 1]);
    }

    public function test_it_shows_an_empty_state_when_the_cart_has_no_items(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CartPage::class)->assertSee('Votre panier est vide');
    }
}

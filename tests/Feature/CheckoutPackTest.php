<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Pack;
use App\Models\PackItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\PackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPackTest extends TestCase
{
    use RefreshDatabase;

    public function test_placing_an_order_with_a_pack_applies_the_bundle_discount_and_tags_the_order_item(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10, 'price' => 5000, 'sale_price' => null]);
        $pack = Pack::factory()->create(['sale_price' => 4000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 1]);
        $pack->load('items.product');

        $this->actingAs($user);
        app(PackService::class)->addToCart($pack, app(CartService::class));

        $response = $this->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = $user->orders()->first();
        $response->assertRedirect(route('checkout.confirmation', $order));

        $this->assertSame(1000.0, (float) $order->pack_discount);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'pack_id' => $pack->id,
        ]);
        $this->assertSame(1, $pack->fresh()->sales_count);
    }

    public function test_stock_is_still_decremented_for_products_bought_via_a_pack(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10, 'price' => 5000, 'sale_price' => null]);
        $pack = Pack::factory()->create(['sale_price' => 4000]);
        PackItem::factory()->create(['pack_id' => $pack->id, 'product_id' => $product->id, 'quantity' => 2]);
        $pack->load('items.product');

        $this->actingAs($user);
        app(PackService::class)->addToCart($pack, app(CartService::class));

        $this->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertSame(8, $product->fresh()->stock);
    }
}

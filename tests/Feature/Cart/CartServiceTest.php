<?php

namespace Tests\Feature\Cart;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_totals_computes_subtotal_discount_and_shipping(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'sale_price' => 8000, 'stock' => 10]);

        $cartService = app(CartService::class);
        $cartService->add($product, 2);

        $totals = $cartService->totals($cartService->current());

        $this->assertSame(16000.0, $totals->subtotal);
        $this->assertSame(4000.0, $totals->discount);
        $this->assertSame(4900.0, $totals->shippingCost);
        $this->assertSame(20900.0, $totals->total);
    }

    public function test_shipping_is_free_above_the_threshold(): void
    {
        $product = Product::factory()->create(['price' => 60000, 'sale_price' => null, 'stock' => 10]);

        $cartService = app(CartService::class);
        $cartService->add($product, 1);

        $totals = $cartService->totals($cartService->current());

        $this->assertSame(0.0, $totals->shippingCost);
        $this->assertTrue($totals->qualifiesForFreeShipping());
    }

    public function test_add_throws_when_product_is_out_of_stock(): void
    {
        $product = Product::factory()->create(['stock' => 0]);

        $this->expectException(InsufficientStockException::class);

        app(CartService::class)->add($product, 1);
    }

    public function test_add_throws_when_product_has_expired(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'expiry_date' => now()->subDay()]);

        $this->expectException(ProductExpiredException::class);

        app(CartService::class)->add($product, 1);
    }

    public function test_add_still_allows_a_product_expiring_today(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'expiry_date' => now()]);

        $item = app(CartService::class)->add($product, 1);

        $this->assertSame(1, $item->quantity);
    }

    public function test_update_quantity_removes_the_item_if_the_product_expired_in_the_meantime(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $cartService = app(CartService::class);
        $item = $cartService->add($product, 2);

        $product->update(['expiry_date' => now()->subDay()]);

        $cartService->updateQuantity($item->fresh(), 5);

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_count_returns_the_sum_of_quantities(): void
    {
        $productA = Product::factory()->create(['stock' => 10]);
        $productB = Product::factory()->create(['stock' => 10]);

        $cartService = app(CartService::class);
        $cartService->add($productA, 2);
        $cartService->add($productB, 3);

        $this->assertSame(5, $cartService->count());
    }

    public function test_guest_and_authenticated_carts_are_isolated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        // A guest adding to the cart must not affect the (unrelated) user's cart.
        $this->post(route('cart.store'), ['product_id' => $product->id, 'quantity' => 1]);

        $userCart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

        $this->assertSame(0, $userCart->items()->count());
        $this->assertDatabaseCount('carts', 2);
    }

    public function test_browsing_the_site_does_not_create_an_empty_cart_row(): void
    {
        $this->get(route('home'));
        $this->get(route('shop.index'));

        $this->assertDatabaseCount('carts', 0);
    }

    public function test_find_returns_null_when_no_cart_exists_yet(): void
    {
        $this->assertNull(app(CartService::class)->find());
        $this->assertSame(0, app(CartService::class)->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\LoyaltyMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\LoyaltySettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutLoyaltyTest extends TestCase
{
    use RefreshDatabase;

    private function checkout(User $user, Product $product): void
    {
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);
    }

    public function test_checkout_page_shows_the_loyalty_balance(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $this->checkout($user, $product);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 300]);

        $this->actingAs($user)->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('300 pts');
    }

    public function test_placing_an_order_with_points_applies_a_discount_and_records_a_movement(): void
    {
        app(LoyaltySettingsService::class)->update(['point_value' => '10', 'max_usage_percentage' => '100']);

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10, 'price' => 10000, 'sale_price' => null]);
        $this->checkout($user, $product);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 100]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
            'points_used' => 100,
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $response->assertRedirect(route('checkout.confirmation', $order));

        $this->assertSame(100, $order->points_used);
        $this->assertSame(1000.0, (float) $order->points_discount);
        $this->assertSame((float) $order->subtotal + (float) $order->shipping_cost - 1000.0, (float) $order->total);
        $this->assertDatabaseHas('loyalty_movements', [
            'user_id' => $user->id,
            'order_id' => $order->id,
            'points' => -100,
        ]);
    }

    public function test_points_usage_is_clamped_to_the_users_balance(): void
    {
        app(LoyaltySettingsService::class)->update(['point_value' => '10', 'max_usage_percentage' => '100']);

        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10, 'price' => 100000, 'sale_price' => null]);
        $this->checkout($user, $product);
        LoyaltyMovement::factory()->create(['user_id' => $user->id, 'points' => 20]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
            'points_used' => 500,
        ]);

        $order = Order::where('user_id', $user->id)->first();

        $this->assertSame(20, $order->points_used);
    }

    public function test_points_cannot_be_used_as_a_selectable_payment_method(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $this->checkout($user, $product);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'points',
        ]);

        $response->assertSessionHasErrors('payment_method');
    }
}

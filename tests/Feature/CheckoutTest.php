<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentTransactionStatus;
use App\Exceptions\InsufficientStockException;
use App\Models\Address;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_place_an_order_without_an_account(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'sale_price' => null, 'stock' => 10]);

        $this->post(route('cart.store'), ['product_id' => $product->id]);

        $this->post(route('checkout.addresses.store'), [
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'address_line1' => '12 Rue de Thiès',
            'city' => 'Thiès',
            'postal_code' => '21000',
            'country' => 'Sénégal',
        ]);

        $address = Address::query()->whereNull('user_id')->first();
        $this->assertNotNull($address);
        $this->assertNotNull($address->guest_token);

        $response = $this->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertNotNull($order->guest_token);
        $response->assertRedirect(route('checkout.confirmation', $order));

        $this->get(route('checkout.confirmation', $order))->assertOk();
    }

    public function test_guest_cannot_view_another_guests_order_confirmation(): void
    {
        $order = Order::factory()->create(['user_id' => null, 'guest_token' => 'someone-elses-token']);

        $this->get(route('checkout.confirmation', $order))->assertForbidden();
    }

    public function test_checkout_redirects_to_cart_when_empty(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_checkout_page_shows_cart_items_and_addresses(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($address->address_line1);
    }

    public function test_user_can_add_a_new_address_from_checkout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('checkout.addresses.store'), [
            'first_name' => 'Awa',
            'last_name' => 'Diop',
            'address_line1' => '12 Rue de Thiès',
            'city' => 'Thiès',
            'postal_code' => '21000',
            'country' => 'Sénégal',
        ])->assertRedirect(route('checkout.index'));

        $this->assertDatabaseHas('addresses', ['user_id' => $user->id, 'first_name' => 'Awa']);
    }

    public function test_selected_address_is_preserved_after_a_validation_failure(): void
    {
        $user = User::factory()->create();
        Address::factory()->create(['user_id' => $user->id]);
        $secondAddress = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        // Fails validation because payment_method is missing, but selects the
        // second address rather than the first.
        $this->actingAs($user)->from(route('checkout.index'))->post(route('checkout.store'), [
            'address_id' => $secondAddress->id,
        ])->assertSessionHasErrors('payment_method');

        $response = $this->get(route('checkout.index'));

        $response->assertOk();
        preg_match_all('/name="address_id"\s+value="(\d+)"\s*(checked)?/', $response->getContent(), $matches, PREG_SET_ORDER);
        $checkedIds = array_values(array_filter($matches, fn ($m) => ! empty($m[2])));

        $this->assertCount(1, $checkedIds);
        $this->assertSame((string) $secondAddress->id, $checkedIds[0][1]);
    }

    public function test_user_cannot_place_an_order_using_another_users_address(): void
    {
        $user = User::factory()->create();
        $otherAddress = Address::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $otherAddress->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $response->assertSessionHasErrors('address_id');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_user_can_place_an_order_successfully(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10000, 'sale_price' => null, 'stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
            'notes' => 'Livrer après 18h',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.confirmation', $order));

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame($address->id, $order->address_id);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame(PaymentStatus::Pending, $order->payment_status);
        $this->assertSame(20000.0, (float) $order->subtotal);
    }

    public function test_placing_an_order_snapshots_product_data_on_order_items(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['name' => 'Sérum Vitamine C', 'sku' => 'SVC-001', 'price' => 5000, 'stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 3]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'wave',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Sérum Vitamine C',
            'product_sku' => 'SVC-001',
            'unit_price' => 5000,
            'quantity' => 3,
            'total_price' => 15000,
        ]);
    }

    public function test_placing_an_order_decrements_stock(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 4]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertSame(6, $product->fresh()->stock);
    }

    public function test_placing_an_order_notifies_admins(): void
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = Order::first();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => Admin::class,
            'type' => 'order.created',
        ]);
        $this->assertSame($order->id, Admin::find($admin->id)->notifications()->first()->data['order_id']);
    }

    public function test_placing_an_order_logs_a_payment_transaction(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = Order::first();

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'provider' => PaymentMethod::CashOnDelivery->value,
            'status' => PaymentTransactionStatus::Pending->value,
        ]);
    }

    public function test_placing_an_order_clears_the_cart(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertSame(0, $cart->fresh()->items()->count());
    }

    public function test_placing_an_order_records_an_initial_status_history_entry(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $order = Order::first();
        $this->assertSame(1, $order->statusHistories()->count());
        $this->assertSame(OrderStatus::Pending, $order->statusHistories()->first()->status);
    }

    public function test_order_creation_is_rejected_and_rolled_back_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 1]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        // Bypass CartService's own capping to simulate a stock change between
        // "add to cart" and "checkout" (e.g. another customer bought the last units).
        CartItem::query()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 5]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame(5, $cart->fresh()->items()->first()->quantity);
    }

    public function test_checkout_is_rejected_when_a_cart_item_expired_after_it_was_added(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);

        // The product expires while it's already sitting in the cart.
        $product->update(['expiry_date' => now()->subDay()]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_order_transaction_rolls_back_entirely_when_a_later_item_lacks_stock(): void
    {
        // Exercises OrderService::createFromCart()'s DB transaction directly:
        // the first item has enough stock and would be processed successfully,
        // but the second item does not — nothing from either item should persist.
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $okProduct = Product::factory()->create(['stock' => 10]);
        $shortProduct = Product::factory()->create(['stock' => 1]);
        $cart = Cart::factory()->create(['user_id' => $user->id]);
        CartItem::query()->create(['cart_id' => $cart->id, 'product_id' => $okProduct->id, 'quantity' => 2]);
        CartItem::query()->create(['cart_id' => $cart->id, 'product_id' => $shortProduct->id, 'quantity' => 5]);

        try {
            app(OrderService::class)->createFromCart($user, $cart, $address, PaymentMethod::CashOnDelivery);
            $this->fail('Expected InsufficientStockException was not thrown.');
        } catch (InsufficientStockException) {
            // expected
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(10, $okProduct->fresh()->stock, 'The first item\'s stock must be rolled back too.');
        $this->assertSame(1, $shortProduct->fresh()->stock);
        $this->assertSame(2, $cart->fresh()->items()->count(), 'The cart must not be cleared on failure.');
    }
}

<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\ValueObjects\CartTotals;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart.guest_token';

    private ?Cart $resolvedCart = null;

    public function __construct(
        private readonly ShippingService $shipping,
        private readonly PackService $packs,
    ) {}

    /**
     * Resolve the current cart: the authenticated user's cart, or a
     * session-bound guest cart, creating either lazily on first use.
     */
    public function current(): Cart
    {
        if ($this->resolvedCart) {
            return $this->resolvedCart;
        }

        if (auth()->check()) {
            return $this->resolvedCart = Cart::query()->firstOrCreate(['user_id' => auth()->id()]);
        }

        $token = session(self::SESSION_KEY);
        $cart = $token ? Cart::query()->where('guest_token', $token)->first() : null;

        if (! $cart) {
            $token = (string) Str::uuid();
            session([self::SESSION_KEY => $token]);
            $cart = Cart::query()->create(['guest_token' => $token]);
        }

        return $this->resolvedCart = $cart;
    }

    /**
     * Look up the current cart without creating one. Used by passive,
     * every-page displays (header mini-cart, nav badge) so simply browsing
     * the site never persists an empty cart row.
     */
    public function find(): ?Cart
    {
        if ($this->resolvedCart) {
            return $this->resolvedCart;
        }

        if (auth()->check()) {
            return Cart::query()->where('user_id', auth()->id())->first();
        }

        $token = session(self::SESSION_KEY);

        return $token ? Cart::query()->where('guest_token', $token)->first() : null;
    }

    /**
     * Add a product to the cart, capping the resulting quantity to the
     * available stock. Pass $packId to tag the line as originating from a
     * pack purchase (kept as its own line, separate from any standalone
     * quantity of the same product) so pack pricing and stats stay traceable.
     *
     * @throws InsufficientStockException|ProductExpiredException
     */
    public function add(Product $product, int $quantity = 1, ?int $packId = null): CartItem
    {
        if ($product->isExpired()) {
            throw new ProductExpiredException($product);
        }

        $cart = $this->current();
        $existing = $cart->items()->where('product_id', $product->id)->where('pack_id', $packId)->first();
        $desired = min(($existing?->quantity ?? 0) + max(1, $quantity), max(0, $product->stock));

        if ($desired <= 0) {
            throw new InsufficientStockException($product);
        }

        return $cart->items()->updateOrCreate(
            ['product_id' => $product->id, 'pack_id' => $packId],
            ['quantity' => $desired],
        );
    }

    /**
     * Set a cart item's quantity, capped to the available stock. Removes the
     * item entirely when the resulting quantity is zero or less.
     */
    public function updateQuantity(CartItem $item, int $quantity): void
    {
        $availableStock = $item->product->isExpired() ? 0 : $item->product->stock;
        $quantity = min($quantity, max(0, $availableStock));

        if ($quantity <= 0) {
            $this->remove($item);

            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function count(?Cart $cart = null): int
    {
        $cart ??= $this->find();

        return $cart ? (int) $cart->items()->sum('quantity') : 0;
    }

    public function totals(Cart $cart): CartTotals
    {
        $subtotal = 0.0;
        $discount = 0.0;

        foreach ($cart->items()->with('product')->get() as $item) {
            $product = $item->product;
            $subtotal += $product->displayPrice() * $item->quantity;

            if ($product->hasDiscount()) {
                $discount += ((float) $product->price - (float) $product->sale_price) * $item->quantity;
            }
        }

        $shippingCost = $this->shipping->calculate($subtotal);
        $packDiscount = $this->packs->cartDiscount($cart);

        return new CartTotals(
            subtotal: $subtotal,
            discount: $discount,
            packDiscount: $packDiscount,
            shippingCost: $shippingCost,
            total: $subtotal + $shippingCost - $packDiscount,
            freeShippingThreshold: $this->shipping->freeShippingThreshold(),
        );
    }

    /**
     * Merge the guest cart (identified by the session token) into the given
     * user's cart, summing quantities and respecting stock limits. Called
     * when a guest logs in.
     */
    public function mergeGuestCartIntoUser(User $user): void
    {
        $token = session(self::SESSION_KEY);
        $guestCart = $token ? Cart::query()->where('guest_token', $token)->first() : null;

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items()->with('product')->get() as $guestItem) {
            $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();
            $availableStock = $guestItem->product->isExpired() ? 0 : $guestItem->product->stock;
            $quantity = min(
                ($existing?->quantity ?? 0) + $guestItem->quantity,
                max(0, $availableStock),
            );

            if ($quantity > 0) {
                $userCart->items()->updateOrCreate(
                    ['product_id' => $guestItem->product_id],
                    ['quantity' => $quantity],
                );
            }
        }

        $guestCart->delete();
        session()->forget(self::SESSION_KEY);
        $this->resolvedCart = null;
    }
}

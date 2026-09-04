<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductExpiredException;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function store(AddToCartRequest $request, CartService $cartService): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));

        try {
            $cartService->add($product, (int) $request->validated('quantity', 1));
        } catch (InsufficientStockException|ProductExpiredException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem, CartService $cartService): RedirectResponse
    {
        $this->authorizeCartItem($cartItem, $cartService);

        $cartService->updateQuantity($cartItem, (int) $request->validated('quantity'));

        return back()->with('success', 'Panier mis à jour.');
    }

    public function destroy(CartItem $cartItem, CartService $cartService): RedirectResponse
    {
        $this->authorizeCartItem($cartItem, $cartService);

        $cartService->remove($cartItem);

        return back()->with('success', 'Produit retiré du panier.');
    }

    public function clear(CartService $cartService): RedirectResponse
    {
        $cartService->clear($cartService->current());

        return back()->with('success', 'Panier vidé.');
    }

    private function authorizeCartItem(CartItem $cartItem, CartService $cartService): void
    {
        abort_unless($cartItem->cart_id === $cartService->current()->id, 403);
    }
}

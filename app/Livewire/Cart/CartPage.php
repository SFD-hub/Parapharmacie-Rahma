<?php

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.web', ['title' => 'Panier', 'back' => '/boutique', 'search' => false])]
class CartPage extends Component
{
    public function updateQuantity(int $itemId, int $quantity, CartService $cartService): void
    {
        $item = $cartService->current()->items()->find($itemId);

        if ($item) {
            $cartService->updateQuantity($item, $quantity);
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $itemId, CartService $cartService): void
    {
        $item = $cartService->current()->items()->find($itemId);

        if ($item) {
            $cartService->remove($item);
        }

        $this->dispatch('cart-updated');
    }

    public function clearCart(CartService $cartService): void
    {
        $cartService->clear($cartService->current());

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $cartService = app(CartService::class);
        $cart = $cartService->current();

        $items = $cart->items()
            ->with([
                'product.category:id,name',
                'product.images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1),
            ])
            ->get();

        return view('livewire.cart.cart-page', [
            'items' => $items,
            'totals' => $cartService->totals($cart),
        ]);
    }
}

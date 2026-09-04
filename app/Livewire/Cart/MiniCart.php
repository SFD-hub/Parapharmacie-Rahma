<?php

namespace App\Livewire\Cart;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class MiniCart extends Component
{
    #[On('cart-updated')]
    public function refresh(): void
    {
        // Re-rendering on this event is enough: render() below always
        // re-fetches the cart's current state.
    }

    public function render()
    {
        $cartService = app(CartService::class);
        $cart = $cartService->find();

        $items = $cart
            ? $cart->items()
                ->with(['product.images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1)])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        return view('livewire.cart.mini-cart', [
            'items' => $items,
            'count' => $cartService->count($cart),
            'totals' => $cart ? $cartService->totals($cart) : null,
        ]);
    }
}

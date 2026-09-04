<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use App\Services\CartService;
use App\Services\PackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class PackController extends Controller
{
    public function index(): View
    {
        $packs = Pack::query()
            ->available()
            ->with(['items.product' => fn ($query) => $query->with([
                'brand:id,name',
                'images' => fn ($query) => $query->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1),
            ])])
            ->latest()
            ->paginate(9);

        return view('web.packs.index', compact('packs'));
    }

    public function show(Pack $pack, PackService $packService): View
    {
        abort_unless($packService->isAvailable($pack), 404);

        $pack->load('items.product.images');

        return view('web.packs.show', [
            'pack' => $pack,
            'basePrice' => $packService->basePrice($pack),
            'savings' => $packService->savings($pack),
        ]);
    }

    public function addToCart(Pack $pack, PackService $packService, CartService $cartService): RedirectResponse
    {
        $pack->load('items.product');

        try {
            $packService->addToCart($pack, $cartService);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')->with('success', 'Le pack a été ajouté à votre panier.');
    }
}

<?php

namespace App\Http\Controllers\Admin\Loyalty;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoyaltyGift\StoreLoyaltyGiftRequest;
use App\Http\Requests\Admin\LoyaltyGift\UpdateLoyaltyGiftRequest;
use App\Models\LoyaltyGift;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GiftController extends Controller
{
    public function create(): View
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.loyalty.gifts.create', compact('products'));
    }

    public function store(StoreLoyaltyGiftRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        LoyaltyGift::create($data);

        return redirect()->route('admin.loyalty.index', ['tab' => 'gifts'])->with('success', 'Cadeau créé avec succès.');
    }

    public function edit(LoyaltyGift $gift): View
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.loyalty.gifts.edit', compact('gift', 'products'));
    }

    public function update(UpdateLoyaltyGiftRequest $request, LoyaltyGift $gift): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $gift->update($data);

        return redirect()->route('admin.loyalty.index', ['tab' => 'gifts'])->with('success', 'Cadeau mis à jour avec succès.');
    }

    public function destroy(LoyaltyGift $gift): RedirectResponse
    {
        $gift->delete();

        return redirect()->route('admin.loyalty.index', ['tab' => 'gifts'])->with('success', 'Cadeau supprimé avec succès.');
    }

    public function toggle(LoyaltyGift $gift): RedirectResponse
    {
        $gift->update(['is_active' => ! $gift->is_active]);

        return back()->with('success', 'Statut mis à jour avec succès.');
    }
}

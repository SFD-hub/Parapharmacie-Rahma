<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyGift;
use App\Services\LoyaltyService;
use App\Services\LoyaltySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class LoyaltyController extends Controller
{
    public function index(LoyaltyService $loyaltyService, LoyaltySettingsService $loyaltySettings): View
    {
        $user = auth()->user();
        $settings = $loyaltySettings->all();
        $balance = $loyaltyService->balance($user);

        $gifts = $settings['mode']->allowsGifts()
            ? LoyaltyGift::query()->active()->with([
                'product:id,name,slug,stock',
                'product.images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1),
            ])->get()
            : collect();

        $recentMovements = $user->loyaltyMovements()->with('order:id,order_number')->latest()->take(5)->get();

        return view('web.account.loyalty.index', [
            'settings' => $settings,
            'balance' => $balance,
            'balanceValue' => $loyaltyService->monetaryValue($balance),
            'gifts' => $gifts,
            'recentMovements' => $recentMovements,
        ]);
    }

    public function history(LoyaltyService $loyaltyService): View
    {
        $movements = auth()->user()->loyaltyMovements()
            ->with('order:id,order_number')
            ->latest()
            ->paginate(15);

        return view('web.account.loyalty.history', [
            'movements' => $movements,
            'balance' => $loyaltyService->balance(auth()->user()),
        ]);
    }

    public function redeemGift(LoyaltyGift $gift, LoyaltyService $loyaltyService): RedirectResponse
    {
        try {
            $loyaltyService->redeemGift(auth()->user(), $gift);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('account.loyalty.index')->with('success', 'Cadeau échangé avec succès ! Retrouvez-le dans vos commandes.');
    }
}

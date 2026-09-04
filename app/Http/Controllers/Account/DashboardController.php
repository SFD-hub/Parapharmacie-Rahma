<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\LoyaltyService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(LoyaltyService $loyaltyService): View
    {
        $user = auth()->user();

        $orders = $user->orders()
            ->with(['items.product.images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1)])
            ->latest()
            ->take(3)
            ->get();

        return view('web.account.dashboard', [
            'user' => $user,
            'orders' => $orders,
            'loyaltyBalance' => $loyaltyService->balance($user),
        ]);
    }
}

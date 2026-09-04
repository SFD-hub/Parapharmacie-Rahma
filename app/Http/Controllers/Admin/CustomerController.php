<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = User::query()
            ->withCount('orders')
            ->withSum(['orders as total_spent' => fn ($q) => $q->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded])], 'total')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($inner) use ($request) {
                    $term = '%'.$request->string('search').'%';
                    $inner->where('name', 'like', $term)->orWhere('email', 'like', $term);
                }),
            );

        $customers = $this->applySort($query, $request, ['name', 'created_at'], 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => User::query()->count(),
            'new_this_month' => User::query()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'with_orders' => User::query()->whereHas('orders')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    public function show(User $customer, LoyaltyService $loyaltyService): View
    {
        $customer->load(['addresses' => fn ($q) => $q->orderByDesc('is_default')]);

        $orders = $customer->orders()
            ->withCount('items')
            ->latest()
            ->take(10)
            ->get(['id', 'order_number', 'status', 'total', 'created_at']);

        $stats = [
            'orders_count' => $customer->orders()->count(),
            'total_spent' => (float) $customer->orders()->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded])->sum('total'),
            'loyalty_balance' => $loyaltyService->balance($customer),
        ];

        return view('admin.customers.show', ['customer' => $customer, 'orders' => $orders, 'stats' => $stats]);
    }

    public function updateNotes(Request $request, User $customer): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['nullable', 'string', 'max:5000']]);

        $customer->update($data);

        return back()->with('success', 'Notes mises à jour avec succès.');
    }
}

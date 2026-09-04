<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()->orders()
            ->select(['id', 'order_number', 'status', 'payment_status', 'total', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('web.account.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items', 'address', 'statusHistories']);

        return view('web.account.orders.show', compact('order'));
    }
}

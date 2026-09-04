<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentTransactionStatus;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = PaymentTransaction::query()
            ->with(['order:id,order_number,user_id', 'order.user:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));

        $payments = $this->applySort($query, $request, ['amount', 'created_at'], 'created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentTransactionStatus::cases(),
        ]);
    }
}

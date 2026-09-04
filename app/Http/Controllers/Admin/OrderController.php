<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    use SortsResults;

    /**
     * @var array<int, OrderStatus>
     */
    private const TO_TREAT_STATUSES = [OrderStatus::Pending, OrderStatus::Confirmed];

    /**
     * @var array<int, OrderStatus>
     */
    private const HISTORY_STATUSES = [OrderStatus::Delivered, OrderStatus::Cancelled, OrderStatus::Refunded];

    public function index(Request $request): View
    {
        $query = Order::query()
            ->select(['id', 'user_id', 'order_number', 'status', 'payment_status', 'total', 'created_at'])
            ->with('user:id,name,email')
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($inner) use ($request) {
                    $term = '%'.$request->string('search').'%';
                    $inner->where('order_number', 'like', $term)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
                }),
            )
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->string('status')->toString();

                // Virtual filter: every order not yet delivered/cancelled/refunded.
                if ($status === 'to_prepare') {
                    return $q->whereIn('status', self::TO_TREAT_STATUSES);
                }

                return $q->where('status', $status);
            });

        $orders = $this->applySort($query, $request, ['order_number', 'total', 'created_at'], 'created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function pending(Request $request): View
    {
        $orders = $this->scopedOrdersQuery($request, self::TO_TREAT_STATUSES)
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.pending', [
            'orders' => $orders,
            'statuses' => self::TO_TREAT_STATUSES,
            'paymentMethods' => PaymentMethod::cases(),
            'stats' => $this->statusStats(self::TO_TREAT_STATUSES),
        ]);
    }

    public function history(Request $request): View
    {
        $orders = $this->scopedOrdersQuery($request, self::HISTORY_STATUSES)
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.history', [
            'orders' => $orders,
            'statuses' => self::HISTORY_STATUSES,
            'paymentMethods' => PaymentMethod::cases(),
            'stats' => $this->statusStats(self::HISTORY_STATUSES),
        ]);
    }

    /**
     * Shared base query for the "à traiter" and "historique" screens: same
     * search/sort/pagination behaviour as index(), restricted to a fixed set
     * of statuses and eager-loading what their richer row display needs
     * (phone, item count, payment method, invoice).
     *
     * @param  array<int, OrderStatus>  $statuses
     */
    private function scopedOrdersQuery(Request $request, array $statuses): Builder
    {
        $query = Order::query()
            ->whereIn('status', $statuses)
            ->withCount('items')
            ->with(['user:id,name,email', 'address:id,phone', 'invoice'])
            ->when(
                $request->filled('search'),
                fn ($q) => $q->where(function ($inner) use ($request) {
                    $term = '%'.$request->string('search').'%';
                    $inner->where('order_number', 'like', $term)
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
                }),
            )
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', $request->string('status')->toString()),
            )
            ->when(
                $request->filled('payment_method'),
                fn ($q) => $q->where('payment_method', $request->string('payment_method')->toString()),
            );

        return $this->applySort($query, $request, ['order_number', 'total', 'created_at'], 'created_at', 'desc');
    }

    /**
     * Per-status counts and total amount for the fixed status set of a
     * screen, used to render its stat cards. Deliberately ignores the
     * current search/filter query string so the cards always reflect the
     * whole "à traiter"/"historique" scope, not just the filtered rows.
     *
     * @param  array<int, OrderStatus>  $statuses
     * @return array<string, int|float>
     */
    private function statusStats(array $statuses): array
    {
        $rows = Order::query()
            ->whereIn('status', $statuses)
            ->selectRaw('status, count(*) as count, sum(total) as total')
            ->groupBy('status')
            ->get()
            ->keyBy(fn ($row) => $row->status->value);

        $stats = ['total_count' => 0, 'total_amount' => 0.0];

        foreach ($statuses as $status) {
            $count = (int) ($rows->get($status->value)->count ?? 0);
            $amount = (float) ($rows->get($status->value)->total ?? 0);
            $stats[$status->value] = $count;
            $stats[$status->value.'_amount'] = $amount;
            $stats['total_count'] += $count;
            $stats['total_amount'] += $amount;
        }

        return $stats;
    }

    public function show(Order $order): View
    {
        $order->load(['items', 'address', 'user', 'statusHistories.admin', 'invoice']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $orderService->updateStatus(
            $order,
            OrderStatus::from($request->validated('status')),
            auth('admin')->user(),
            $request->validated('note'),
        );

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}

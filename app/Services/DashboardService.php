<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentTransactionStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Centralizes every read used by the owner's dashboard: headline figures,
 * the revenue chart series, best/worst sellers and the alert counters.
 * Kept read-only and side-effect free on purpose.
 */
class DashboardService
{
    /**
     * Orders that represent a real, non-cancelled sale.
     */
    private function sellableOrders()
    {
        return Order::query()->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded]);
    }

    public function totalRevenue(): float
    {
        return (float) $this->sellableOrders()->sum('total');
    }

    public function ordersCount(): int
    {
        return Order::query()->count();
    }

    /**
     * Orders not yet delivered/cancelled/refunded — i.e. still needing
     * action (mirrors OrderController::TO_TREAT_STATUSES).
     */
    public function ordersToTreatCount(): int
    {
        return Order::query()->whereIn('status', [OrderStatus::Pending, OrderStatus::Confirmed])->count();
    }

    public function customersCount(): int
    {
        return User::query()->count();
    }

    public function productsCount(): int
    {
        return Product::query()->count();
    }

    /**
     * Percentage change of the current month's revenue vs. the previous
     * month's. Null when the previous month had no revenue to compare to.
     */
    public function revenueChangePercent(): ?float
    {
        $current = (float) $this->sellableOrders()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        $previousMonth = now()->subMonthNoOverflow();
        $previous = (float) $this->sellableOrders()
            ->whereBetween('created_at', [$previousMonth->copy()->startOfMonth(), $previousMonth->copy()->endOfMonth()])
            ->sum('total');

        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function newCustomersThisMonth(): int
    {
        return User::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }

    public function pendingPaymentsCount(): int
    {
        return PaymentTransaction::query()->where('status', PaymentTransactionStatus::Pending)->count();
    }

    /**
     * @return array{labels: array<int, string>, data: array<int, float>}
     */
    public function revenueSeries(string $period, ?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        [$from, $to, $groupBy] = $this->resolveRange($period, $from, $to);

        $buckets = collect();
        $cursor = $from->copy();

        while ($cursor->lessThanOrEqualTo($to)) {
            $buckets[$this->bucketKey($cursor, $groupBy)] = 0.0;
            $cursor = $this->advance($cursor, $groupBy);
        }

        $orders = $this->sellableOrders()
            ->whereBetween('created_at', [$from, $to])
            ->get(['total', 'created_at']);

        foreach ($orders as $order) {
            $key = $this->bucketKey($order->created_at, $groupBy);
            $buckets[$key] = ($buckets[$key] ?? 0) + (float) $order->total;
        }

        return ['labels' => $buckets->keys()->all(), 'data' => $buckets->values()->all()];
    }

    /**
     * Percentage change of a revenue series' total vs. the immediately
     * preceding period of the same length. Null when that prior period had
     * no revenue to compare to.
     */
    public function revenueSeriesChangePercent(string $period, ?CarbonInterface $from = null, ?CarbonInterface $to = null): ?float
    {
        [$rangeFrom, $rangeTo] = $this->resolveRange($period, $from, $to);

        $current = (float) $this->sellableOrders()->whereBetween('created_at', [$rangeFrom, $rangeTo])->sum('total');

        $lengthInSeconds = $rangeFrom->diffInSeconds($rangeTo);
        $previousTo = $rangeFrom->copy()->subSecond();
        $previousFrom = $previousTo->copy()->subSeconds($lengthInSeconds);

        $previous = (float) $this->sellableOrders()->whereBetween('created_at', [$previousFrom, $previousTo])->sum('total');

        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function periodComparisonLabel(string $period): string
    {
        return match ($period) {
            'today' => 'hier',
            'week' => 'la semaine dernière',
            'days7' => 'les 7 jours précédents',
            'days30' => 'les 30 jours précédents',
            'year' => "l'année dernière",
            'custom' => 'la période précédente',
            default => 'le mois dernier',
        };
    }

    public function bestSellingProducts(int $limit = 5): Collection
    {
        return $this->rankedProducts('desc', $limit);
    }

    public function worstSellingProducts(int $limit = 5): Collection
    {
        return $this->rankedProducts('asc', $limit);
    }

    private function rankedProducts(string $direction, int $limit): Collection
    {
        return OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total_price) as total_revenue')
            ->whereNotNull('product_id')
            ->whereHas('order', fn ($q) => $q->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded]))
            ->groupBy('product_id')
            ->orderBy('total_sold', $direction)
            ->with(['product' => fn ($query) => $query->select(['id', 'name', 'slug', 'stock'])->with([
                'images' => fn ($query) => $query->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1),
            ])])
            ->take($limit)
            ->get();
    }

    /**
     * @return array{pendingOrders: int, outOfStock: int, lowStock: int}
     */
    public function alerts(): array
    {
        return [
            'pendingOrders' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'outOfStock' => Product::query()->where('stock', '<=', 0)->count(),
            'lowStock' => Product::query()->whereBetween('stock', [1, 5])->count(),
        ];
    }

    /**
     * @return array{0: CarbonInterface, 1: CarbonInterface, 2: string}
     */
    private function resolveRange(string $period, ?CarbonInterface $from, ?CarbonInterface $to): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay(), 'hour'],
            'week' => [now()->startOfWeek(), now()->endOfWeek(), 'day'],
            'days7' => [now()->subDays(6)->startOfDay(), now()->endOfDay(), 'day'],
            'days30' => [now()->subDays(29)->startOfDay(), now()->endOfDay(), 'day'],
            'year' => [now()->startOfYear(), now()->endOfYear(), 'month'],
            'custom' => [
                ($from ?? now()->subDays(30))->copy()->startOfDay(),
                ($to ?? now())->copy()->endOfDay(),
                'day',
            ],
            default => [now()->startOfMonth(), now()->endOfMonth(), 'day'],
        };
    }

    private function bucketKey(CarbonInterface $date, string $groupBy): string
    {
        return match ($groupBy) {
            'hour' => $date->format('H:00'),
            'month' => Carbon::instance($date)->translatedFormat('M Y'),
            default => $date->format('d/m'),
        };
    }

    private function advance(CarbonInterface $date, string $groupBy): CarbonInterface
    {
        return match ($groupBy) {
            'hour' => $date->copy()->addHour(),
            'month' => $date->copy()->addMonthNoOverflow(),
            default => $date->copy()->addDay(),
        };
    }
}

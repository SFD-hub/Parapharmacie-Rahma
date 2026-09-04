<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\DashboardService;
use App\Services\StockForecastService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboard, StockForecastService $forecast): View
    {
        $lowStockProducts = Product::query()
            ->whereBetween('stock', [1, 5])
            ->orderBy('stock')
            ->with(['images' => fn ($query) => $query->select(['id', 'product_id', 'path', 'position'])->orderBy('position')->limit(1)])
            ->take(5)
            ->get(['id', 'name', 'slug', 'stock']);

        return view('admin.dashboard', [
            'revenue' => $dashboard->totalRevenue(),
            'revenueChangePercent' => $dashboard->revenueChangePercent(),
            'ordersCount' => $dashboard->ordersCount(),
            'ordersToTreatCount' => $dashboard->ordersToTreatCount(),
            'customersCount' => $dashboard->customersCount(),
            'newCustomersThisMonth' => $dashboard->newCustomersThisMonth(),
            'productsCount' => $dashboard->productsCount(),
            'bestSellers' => $dashboard->bestSellingProducts(),
            'worstSellers' => $dashboard->worstSellingProducts(),
            'alerts' => $dashboard->alerts() + ['pendingPayments' => $dashboard->pendingPaymentsCount()],
            'lowStockForecasts' => $lowStockProducts->mapWithKeys(
                fn ($product) => [$product->id => $forecast->daysUntilStockout($product)],
            ),
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}

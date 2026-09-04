<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;

/**
 * Simple stockout estimate based on recent sales velocity — no AI, just an
 * average of the last few weeks of sales. "This product will likely run out
 * of stock in N days."
 */
class StockForecastService
{
    /**
     * Estimated number of days of stock remaining, or null if there isn't
     * enough recent sales history to make an estimate. Defaults to the
     * configurable `stock.forecast_lookback_days` value (see config/stock.php).
     */
    public function daysUntilStockout(Product $product, ?int $lookbackDays = null): ?int
    {
        $lookbackDays ??= (int) config('stock.forecast_lookback_days', 28);

        if ($product->stock <= 0) {
            return 0;
        }

        $unitsSold = abs((int) StockMovement::query()
            ->where('product_id', $product->id)
            ->where('type', StockMovementType::Sale)
            ->where('created_at', '>=', now()->subDays($lookbackDays))
            ->sum('quantity'));

        if ($unitsSold <= 0) {
            return null;
        }

        $dailyRate = $unitsSold / $lookbackDays;

        return (int) floor($product->stock / $dailyRate);
    }
}

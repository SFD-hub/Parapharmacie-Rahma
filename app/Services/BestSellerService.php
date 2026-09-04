<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Models\Product;

/**
 * Keeps Product::is_best_seller in sync with real sales — it is never set
 * by hand from the admin form (see StoreProductRequest), only recomputed
 * here, scheduled daily (see routes/console.php).
 */
class BestSellerService
{
    /**
     * Mark the top-selling products (by total quantity sold, across every
     * non-cancelled/refunded order, all time) as best sellers, and clear the
     * flag from every other product.
     */
    public function recompute(int $limit = 10): void
    {
        $bestSellerIds = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->whereNotNull('product_id')
            ->whereHas('order', fn ($query) => $query->whereNotIn('status', [OrderStatus::Cancelled, OrderStatus::Refunded]))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->pluck('product_id');

        Product::query()
            ->where('is_best_seller', true)
            ->whereNotIn('id', $bestSellerIds)
            ->update(['is_best_seller' => false]);

        Product::query()
            ->whereIn('id', $bestSellerIds)
            ->update(['is_best_seller' => true]);
    }
}

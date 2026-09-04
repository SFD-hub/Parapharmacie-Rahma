<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Tracks the most recently viewed products in the visitor's session (works
 * for guests and authenticated clients alike, with no DB writes on passive
 * page views). Most-recently-viewed first, no duplicates, capped to
 * MAX_ITEMS — change that single constant to reconfigure the limit.
 */
class RecentlyViewedService
{
    private const MAX_ITEMS = 8;

    private const SESSION_KEY = 'recently_viewed_products';

    public function track(Product $product): void
    {
        $ids = $this->ids();

        $ids = collect($ids)
            ->reject(fn (int $id) => $id === $product->id)
            ->prepend($product->id)
            ->take(self::MAX_ITEMS)
            ->values()
            ->all();

        Session::put(self::SESSION_KEY, $ids);
    }

    /**
     * Resolve the tracked ids to active products, preserving MRU order, and
     * excluding the given product (typically "the one currently viewed").
     */
    public function products(?Product $except = null): Collection
    {
        $ids = collect($this->ids())
            ->reject(fn (int $id) => $except && $id === $except->id)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->with(['images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1)])
            ->get()
            ->keyBy('id');

        return $ids->map(fn (int $id) => $products->get($id))->filter()->values();
    }

    /**
     * @return array<int, int>
     */
    private function ids(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}

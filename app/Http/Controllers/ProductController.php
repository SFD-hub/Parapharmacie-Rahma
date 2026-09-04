<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\RecentlyViewedService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product, RecentlyViewedService $recentlyViewed): View
    {
        abort_unless($product->is_active, 404);

        $product->load([
            'brand:id,name,slug',
            'category:id,name,slug',
            'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('position'),
            'complements' => fn ($query) => $query->where('is_active', true)->with([
                'images' => fn ($q) => $q->orderByDesc('is_primary')->limit(1),
            ]),
            'packs' => fn ($query) => $query->active()->with('items.product'),
        ]);
        $product->loadCount('approvedReviews as reviews_count');
        $product->loadAvg('approvedReviews as reviews_avg_rating', 'rating');

        $recentlyViewedProducts = $recentlyViewed->products($product);
        $recentlyViewed->track($product);

        $related = Product::query()
            ->select(['id', 'category_id', 'brand_id', 'name', 'slug', 'price', 'sale_price', 'stock', 'is_new', 'is_best_seller'])
            ->with([
                'brand:id,name',
                'images' => fn ($query) => $query->orderByDesc('is_primary')->limit(1),
            ])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->take(4)
            ->get();

        $reviews = $product->approvedReviews()->take(20)->get();

        $myReview = auth()->check()
            ? Review::query()->where('user_id', auth()->id())->where('product_id', $product->id)->first()
            : null;

        return view('web.products.show', compact(
            'product', 'related', 'recentlyViewedProducts', 'reviews', 'myReview',
        ));
    }
}

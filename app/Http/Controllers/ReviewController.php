<?php

namespace App\Http\Controllers;

use App\Enums\ReviewStatus;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        Review::query()->create([
            'user_id' => $user?->id,
            'product_id' => $product->id,
            'name' => $user?->name ?? $request->string('name'),
            'rating' => $request->integer('rating'),
            'comment' => $request->string('comment')->trim()->value() ?: null,
            'status' => ReviewStatus::Pending,
        ]);

        return back()->with('success', 'Merci pour votre avis ! Il sera visible après validation par notre équipe.');
    }
}

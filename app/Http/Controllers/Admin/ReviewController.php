<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Concerns\SortsResults;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    use SortsResults;

    public function index(Request $request): View
    {
        $query = Review::query()
            ->with(['product:id,name,slug'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));

        $reviews = $this->applySort($query, $request, ['rating', 'created_at'], 'created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'statuses' => ReviewStatus::cases(),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => ReviewStatus::Approved]);

        return back()->with('success', 'Avis approuvé et publié.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => ReviewStatus::Rejected]);

        return back()->with('success', 'Avis rejeté.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Avis supprimé.');
    }
}

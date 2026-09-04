<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'product_id', 'name', 'rating', 'comment', 'status'])]
class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved);
    }
}

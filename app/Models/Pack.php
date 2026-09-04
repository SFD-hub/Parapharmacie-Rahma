<?php

namespace App\Models;

use App\Enums\PackBadge;
use Database\Factories\PackFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'description', 'image', 'sale_price', 'badge',
    'is_featured_home', 'is_featured_recommendations', 'loyalty_bonus_points',
    'starts_at', 'ends_at', 'max_sales', 'sales_count', 'is_active',
])]
class Pack extends Model
{
    /** @use HasFactory<PackFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'badge' => PackBadge::class,
            'sale_price' => 'decimal:2',
            'is_featured_home' => 'boolean',
            'is_featured_recommendations' => 'boolean',
            'loyalty_bonus_points' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'max_sales' => 'integer',
            'sales_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Active, within its optional date window, and not sold out.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('max_sales')->orWhereColumn('sales_count', '<', 'max_sales'));
    }
}

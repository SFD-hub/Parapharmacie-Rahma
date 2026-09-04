<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id', 'brand_id', 'name', 'slug', 'sku', 'short_description', 'description',
    'ingredients', 'usage_instructions', 'price', 'sale_price', 'stock', 'expiry_date', 'is_active',
    'is_featured', 'is_new', 'is_best_seller', 'is_loyalty_featured', 'meta_title', 'meta_description',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_loyalty_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Manually curated cross-sell suggestions shown on this product's page.
     */
    public function complements(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_complements', 'product_id', 'complementary_product_id')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    public function packs(): BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'pack_items')->withPivot('quantity');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->approved();
    }

    public function hasDiscount(): bool
    {
        return ! is_null($this->sale_price) && (float) $this->sale_price < (float) $this->price;
    }

    public function discountPercent(): ?int
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        return (int) round(((float) $this->price - (float) $this->sale_price) / (float) $this->price * 100);
    }

    public function displayPrice(): float
    {
        return $this->hasDiscount() ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * True once the expiry date has fully passed. Matches scopeExpired()'s
     * date-only comparison: a product expiring "today" is still sellable
     * through the end of that day.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->lt(now()->startOfDay());
    }

    public function stockStatus(): string
    {
        return match (true) {
            $this->stock <= 0 => 'out_of_stock',
            $this->stock <= 5 => 'low_stock',
            default => 'in_stock',
        };
    }

    /**
     * Search by name/short description. Uses a MySQL fulltext index when
     * available and falls back to a LIKE-based search otherwise (e.g. SQLite
     * in the test suite).
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if ($query->getConnection()->getDriverName() === 'mysql') {
            return $query->whereFullText(['name', 'short_description'], $term);
        }

        return $query->where(function (Builder $inner) use ($term) {
            $inner->where('name', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%");
        });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('expiry_date')->where('expiry_date', '<', now()->toDateString());
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }
}

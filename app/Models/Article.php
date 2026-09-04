<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'admin_id', 'article_category_id', 'title', 'slug', 'excerpt', 'content', 'cover_image',
    'is_published', 'published_at', 'meta_title', 'meta_description',
])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    /**
     * Products recommended at the end of the article to address the
     * problem it covers.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('position')->orderByPivot('position');
    }

    /**
     * Estimated reading time in minutes, derived from the actual word count
     * (average adult reading speed: ~200 words/minute).
     */
    public function readingTime(): int
    {
        return max(1, (int) round(str_word_count(strip_tags((string) $this->content)) / 200));
    }

    /**
     * Search by title/excerpt. Uses a MySQL fulltext index when available and
     * falls back to a LIKE-based search otherwise (e.g. SQLite in tests).
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if ($query->getConnection()->getDriverName() === 'mysql') {
            return $query->whereFullText(['title', 'excerpt'], $term);
        }

        return $query->where(function (Builder $inner) use ($term) {
            $inner->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%");
        });
    }
}

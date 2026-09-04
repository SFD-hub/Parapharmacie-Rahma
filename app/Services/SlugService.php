<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Generate a slug from the given value, unique within the model's table.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function unique(string $modelClass, string $value, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $suffix = 1;

        while (
            $modelClass::query()
                ->where($column, $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * Resolve the slug to persist when creating a record: the user-provided
     * value if any, otherwise derived from the fallback (typically the name).
     *
     * @param  class-string<Model>  $modelClass
     */
    public function forCreate(string $modelClass, ?string $providedSlug, string $fallback): string
    {
        return $this->unique($modelClass, $providedSlug ?: $fallback);
    }

    /**
     * Resolve the slug to persist when updating a record: regenerate only if
     * the user provided a new value, otherwise keep the current slug as is.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function forUpdate(string $modelClass, ?string $providedSlug, string $currentSlug, int $ignoreId): string
    {
        return $providedSlug ? $this->unique($modelClass, $providedSlug, $ignoreId) : $currentSlug;
    }
}

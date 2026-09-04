<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait SortsResults
{
    /**
     * Apply a whitelisted sort column/direction from the request query string.
     *
     * @param  array<int, string>  $sortable
     */
    protected function applySort(
        Builder $query,
        Request $request,
        array $sortable,
        string $default = 'created_at',
        string $defaultDirection = 'desc',
    ): Builder {
        $column = $request->string('sort')->value();
        $direction = $request->string('direction')->value() === 'asc' ? 'asc' : 'desc';

        if (! in_array($column, $sortable, true)) {
            $column = $default;
            $direction = $defaultDirection;
        }

        return $query->orderBy($column, $direction);
    }
}

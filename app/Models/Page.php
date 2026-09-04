<?php

namespace App\Models;

use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'slug', 'content', 'meta_title', 'meta_description', 'is_active'])]
class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}

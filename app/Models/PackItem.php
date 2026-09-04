<?php

namespace App\Models;

use Database\Factories\PackItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pack_id', 'product_id', 'quantity'])]
class PackItem extends Model
{
    /** @use HasFactory<PackItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

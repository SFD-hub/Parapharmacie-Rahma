<?php

namespace App\Models;

use App\Enums\LoyaltyMovementType;
use Database\Factories\LoyaltyMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'order_id', 'loyalty_gift_id', 'admin_id', 'type', 'points', 'amount', 'description', 'metadata'])]
class LoyaltyMovement extends Model
{
    /** @use HasFactory<LoyaltyMovementFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => LoyaltyMovementType::class,
            'points' => 'integer',
            'amount' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(LoyaltyGift::class, 'loyalty_gift_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}

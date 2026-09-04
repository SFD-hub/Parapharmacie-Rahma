<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'guest_token', 'address_id', 'order_number', 'status', 'payment_status',
    'payment_method', 'subtotal', 'shipping_cost', 'discount', 'points_used',
    'points_discount', 'points_earned', 'pack_discount', 'total', 'notes',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    private const GUEST_TOKEN_SESSION_KEY = 'checkout.guest_token';

    /**
     * Identifies a guest's own addresses/orders across the checkout flow —
     * same pattern as Conversation::currentGuestToken() and CartService's
     * guest cart token: a random value persisted in the session.
     */
    public static function currentGuestToken(): string
    {
        $token = session(self::GUEST_TOKEN_SESSION_KEY);

        if (! $token) {
            $token = (string) Str::uuid();
            session([self::GUEST_TOKEN_SESSION_KEY => $token]);
        }

        return $token;
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'points_discount' => 'decimal:2',
            'pack_discount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function loyaltyMovements(): HasMany
    {
        return $this->hasMany(LoyaltyMovement::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}

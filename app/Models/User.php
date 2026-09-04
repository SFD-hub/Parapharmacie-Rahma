<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'admin_notes'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function supportConversation(): HasOne
    {
        return $this->hasOne(SupportConversation::class);
    }

    public function loyaltyMovements(): HasMany
    {
        return $this->hasMany(LoyaltyMovement::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * In-app notifications (overrides Notifiable's own relation, which
     * targets Laravel's channel-based database notifications instead).
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')->latest();
    }
}

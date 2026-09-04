<?php

namespace App\Models;

use Database\Factories\SupportConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'guest_token', 'guest_name', 'status', 'last_message_at'])]
class SupportConversation extends Model
{
    /** @use HasFactory<SupportConversationFactory> */
    use HasFactory;

    private const GUEST_TOKEN_SESSION_KEY = 'support.guest_token';

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->orderBy('created_at');
    }

    public function displayName(): string
    {
        return $this->user->name ?? $this->guest_name ?? 'Invité';
    }

    /**
     * Identifies an anonymous visitor's support thread across requests —
     * same pattern as Cart's and Order's guest tokens: a UUID stored in the
     * session, matched against the conversation's guest_token column.
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
}

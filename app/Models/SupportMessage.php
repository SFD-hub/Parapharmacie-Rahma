<?php

namespace App\Models;

use Database\Factories\SupportMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['support_conversation_id', 'admin_id', 'content', 'read_at'])]
class SupportMessage extends Model
{
    /** @use HasFactory<SupportMessageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SupportConversation::class, 'support_conversation_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * null admin_id = written by the customer, non-null = an admin's reply.
     */
    public function isFromAdmin(): bool
    {
        return $this->admin_id !== null;
    }
}

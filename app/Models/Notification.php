<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['notifiable_type', 'notifiable_id', 'type', 'title', 'message', 'data', 'read_at'])]
class Notification extends Model
{
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}

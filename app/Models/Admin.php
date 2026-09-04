<?php

namespace App\Models;

use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}

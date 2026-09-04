<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeGuestCartOnLogin
{
    public function __construct(private readonly CartService $cartService) {}

    /**
     * When a client logs in, fold whatever guest cart their browser session
     * was tracking into their persistent account cart.
     */
    public function handle(Login $event): void
    {
        if ($event->guard !== 'web' || ! $event->user instanceof User) {
            return;
        }

        $this->cartService->mergeGuestCartIntoUser($event->user);
    }
}

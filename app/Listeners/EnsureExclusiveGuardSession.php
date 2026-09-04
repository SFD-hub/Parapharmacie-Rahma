<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class EnsureExclusiveGuardSession
{
    /**
     * The guards that are mutually exclusive within a single browser session.
     */
    protected const GUARDS = ['web', 'admin'];

    /**
     * Log out every other guard when one guard successfully authenticates,
     * so a browser can only be signed in as a client or as an administrator, never both.
     */
    public function handle(Login $event): void
    {
        foreach (self::GUARDS as $guard) {
            if ($guard === $event->guard) {
                continue;
            }

            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
            }
        }
    }
}

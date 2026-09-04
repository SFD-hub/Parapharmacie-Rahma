<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is('admin/*') ? route('admin.login') : route('login'),
        );

        $middleware->redirectUsersTo(
            fn (Request $request) => $request->is('admin/*') ? route('admin.dashboard') : route('dashboard'),
        );

        // Payment provider webhooks are called by external servers with no
        // CSRF token — signature verification (App\Services\Payment\PaymentWebhookService)
        // is what authenticates them instead.
        $middleware->validateCsrfTokens(except: ['webhooks/payments/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

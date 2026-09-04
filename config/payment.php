<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active payment provider
    |--------------------------------------------------------------------------
    |
    | "manual" (default) routes every payment method to ManualPaymentGateway,
    | exactly as today: the order is created with a pending payment status
    | for an admin to confirm once payment is actually received. Switching
    | this to anything else activates the dedicated gateways below (Wave,
    | Orange Money, Stripe) for the payment methods that have one — no other
    | part of the checkout flow needs to change.
    |
    */
    'provider' => env('PAYMENT_PROVIDER', 'manual'),

    'wave' => [
        'key' => env('WAVE_API_KEY'),
        'secret' => env('WAVE_SECRET'),
    ],

    'orange' => [
        'key' => env('ORANGE_API_KEY'),
        'secret' => env('ORANGE_SECRET'),
    ],

    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];

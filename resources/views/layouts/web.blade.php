<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ ($title ?? null) ? $title.' - '.config('app.name') : config('app.name') }}</title>
        @if ($description ?? null)
            <meta name="description" content="{{ $description }}">
        @endif
        <link rel="icon" href="{{ $faviconUrl ?? '/favicon.ico' }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:600i,700i&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased" x-data="{ mobileMenuOpen: false }">
        <x-web.header :back="$back ?? null" :search="$search ?? true" />
        <x-web.mobile-menu />

        <main class="pb-20">
            {{ $slot }}
        </main>

        <x-web.footer />

        <x-web.mobile-nav />
        <x-web.support-bubble />

        @livewireScripts
    </body>
</html>

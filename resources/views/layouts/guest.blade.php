<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 py-10">
            <a href="/" class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="h-10 w-10 text-primary-600" fill="none">
                    <g fill="currentColor">
                        <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(0 20 20)" />
                        <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(72 20 20)" />
                        <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(144 20 20)" />
                        <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(216 20 20)" />
                        <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(288 20 20)" />
                    </g>
                    <circle cx="20" cy="20" r="3.5" fill="white" />
                </svg>
                <span class="leading-tight">
                    <span class="block text-lg font-bold tracking-wide text-gray-900">RAHMANE</span>
                    <span class="block text-[10px] font-medium tracking-[0.2em] text-gray-500">PARAPHARMACIE</span>
                </span>
            </a>

            <div class="mt-8 w-full overflow-hidden rounded-2xl border border-gray-100 bg-white px-6 py-8 shadow-lg sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' - Administration' : 'Administration' }}</title>
        <link rel="icon" href="{{ $faviconUrl ?? '/favicon.ico' }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
        <div class="flex min-h-screen">
            <aside class="hidden w-64 shrink-0 flex-col overflow-y-auto bg-gray-900 lg:sticky lg:top-0 lg:flex lg:h-screen">
                <x-admin.sidebar-brand />
                <x-admin.sidebar-nav />
            </aside>

            <div
                x-show="sidebarOpen"
                x-cloak
                class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                x-show="sidebarOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-64 overflow-y-auto bg-gray-900 shadow-xl lg:hidden"
            >
                <x-admin.sidebar-brand>
                    <button type="button" class="rounded-full p-2 text-gray-400 hover:bg-white/10 hover:text-white" @click="sidebarOpen = false">
                        <x-icon name="x-mark" class="h-5 w-5" />
                    </button>
                </x-admin.sidebar-brand>
                <x-admin.sidebar-nav />
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex min-h-16 flex-wrap items-center gap-x-6 gap-y-3 border-b border-gray-100 bg-white px-4 py-3 shadow-sm sm:px-6">
                    <button type="button" class="p-2 text-gray-700 lg:hidden" @click="sidebarOpen = true" aria-label="Ouvrir le menu">
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>

                    <div class="min-w-0">
                        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="mt-0.5 text-sm text-gray-500">{{ $subtitle }}</p>
                        @endif
                    </div>

                    @if ($search)
                        <div class="order-last hidden w-full lg:order-none lg:block lg:max-w-md lg:flex-1">
                            <div class="relative">
                                <input
                                    type="search"
                                    placeholder="Rechercher un produit, une commande..."
                                    class="w-full rounded-full border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            </div>
                        </div>
                    @endif

                    <div class="ml-auto flex items-center gap-3">
                        <a href="{{ route('admin.notifications.index') }}" class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700" aria-label="Notifications">
                            <x-icon name="bell" class="h-5 w-5" />
                            @if ($adminUnreadNotificationsCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-primary-600 px-1 text-[10px] font-semibold text-white">
                                    {{ $adminUnreadNotificationsCount > 9 ? '9+' : $adminUnreadNotificationsCount }}
                                </span>
                            @endif
                        </a>

                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                            <button type="button" class="flex items-center gap-2 rounded-full border-l border-gray-100 pl-3 hover:opacity-80" @click="open = !open">
                                <x-ds.avatar :name="auth('admin')->user()?->name ?? ''" size="sm" />
                                <span class="hidden text-left leading-tight sm:block">
                                    <span class="block text-sm font-semibold text-gray-900">{{ auth('admin')->user()?->name }}</span>
                                    <span class="block text-xs text-gray-500">Admin</span>
                                </span>
                                <x-icon name="chevron-down" class="hidden h-4 w-4 text-gray-400 sm:block" />
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute right-0 z-40 mt-2 w-44 rounded-xl border border-gray-100 bg-white p-1.5 shadow-lg"
                            >
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-primary-600">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6">
                    @if (session('success'))
                        <div class="mb-4 flex items-center gap-2 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            <x-icon name="check" class="h-4 w-4 shrink-0" />
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                            <x-icon name="exclamation" class="h-4 w-4 shrink-0" />
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

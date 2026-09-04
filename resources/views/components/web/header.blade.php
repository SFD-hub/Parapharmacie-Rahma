@props(['back' => null, 'search' => true])

<div class="sticky top-0 z-40 lg:static">
    <x-web.announcement-bar />

    @if ($search)
        <div class="bg-white px-4 pb-3 pt-3 sm:px-6 lg:hidden">
            <x-web.search-form id="mobile-search" />
        </div>
    @endif
</div>

<header class="border-b border-gray-100 bg-white lg:sticky lg:top-0 lg:z-30">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative flex h-28 items-center justify-between gap-4 lg:grid lg:grid-cols-[1fr_auto_1fr] lg:items-center lg:gap-6">
            @if ($back)
                <a href="{{ $back }}" class="-ml-1 p-2 text-gray-700" aria-label="Retour">
                    <x-icon name="chevron-left" class="h-6 w-6" />
                </a>

                <div class="absolute left-1/2 -translate-x-1/2">
                    <x-web.logo />
                </div>

                <div class="flex items-center gap-1 sm:gap-2">
                    <livewire:cart.mini-cart />
                </div>
            @else
                <div class="flex items-center gap-2 lg:hidden">
                    <button
                        type="button"
                        class="-ml-1 p-2 text-gray-700 lg:hidden"
                        @click="mobileMenuOpen = true"
                        aria-label="Ouvrir le menu"
                    >
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>
                </div>

                <div class="absolute left-1/2 -translate-x-1/2 lg:hidden">
                    <x-web.logo />
                </div>

                <x-web.search-form id="desktop-search" class="hidden max-w-sm lg:flex" />

                <div class="hidden lg:flex lg:justify-center">
                    <x-web.logo />
                </div>

                <div class="flex items-center gap-2 sm:gap-3 lg:justify-self-end lg:gap-6">

                    <a href="{{ $accountRoute }}" class="hidden items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700 lg:flex">
                        <x-icon name="user" class="h-5 w-5" />
                        {{ auth()->check() ? 'Mon compte' : 'Se connecter' }}
                    </a>

                    @auth
                        <a
                            href="{{ route('account.notifications.index') }}"
                            class="relative p-2 text-gray-700 hover:text-primary-600"
                            aria-label="Notifications"
                        >
                            <x-icon name="bell" class="h-6 w-6" />
                            @if ($unreadNotificationsCount > 0)
                                <span class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary-600 text-[10px] font-semibold text-white">
                                    {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                                </span>
                            @endif
                        </a>
                    @endauth

                    <div class="flex items-center gap-1.5">
                        <span class="hidden text-sm font-semibold text-primary-600 lg:inline">Panier</span>
                        <livewire:cart.mini-cart />
                    </div>
                </div>
            @endif
        </div>

        @unless ($back)
            <nav class="hidden border-t border-gray-100 py-2.5 lg:block" aria-label="Catégories">
                <ul class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                    @foreach ($menuCategories as $category)
                        <li
                            class="relative"
                            @if (! empty($category['children']))
                                x-data="{ open: false }"
                                @mouseenter="open = true"
                                @mouseleave="open = false"
                            @endif
                        >
                            <a
                                href="{{ route('shop.index', ['category' => $category['slug']]) }}"
                                class="block whitespace-nowrap py-1 text-xs font-semibold uppercase tracking-wide {{ $category['slug'] === 'promotion' ? 'text-primary-600 hover:text-primary-700' : 'text-gray-700 hover:text-primary-600' }}"
                            >
                                {{ $category['name'] }}
                            </a>

                            @if (! empty($category['children']))
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition.opacity
                                    class="absolute left-0 top-full z-40 w-56 rounded-xl border border-gray-100 bg-white p-2 shadow-lg"
                                >
                                    @foreach ($category['children'] as $child)
                                        <a
                                            href="{{ route('shop.index', ['category' => $child['slug']]) }}"
                                            class="block whitespace-normal rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-blush-50 hover:text-primary-600"
                                        >
                                            {{ $child['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        @endunless
    </div>
</header>

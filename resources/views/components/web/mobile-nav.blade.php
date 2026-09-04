@php
    $navItems = [
        ['label' => 'Accueil', 'icon' => 'home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Boutique', 'icon' => 'bag', 'href' => route('shop.index'), 'active' => request()->routeIs('shop.index')],
        ['label' => 'Mon Panier', 'icon' => 'cart', 'href' => route('cart.index'), 'active' => request()->routeIs('cart.index')],
        ['label' => 'Mon Compte', 'icon' => 'user', 'href' => $accountRoute, 'active' => request()->routeIs(['dashboard', 'login'])],
    ];
@endphp

<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-100 bg-white">
    <div class="mx-auto grid max-w-7xl grid-cols-4 lg:px-6">
        @foreach ($navItems as $item)
            <a
                href="{{ $item['href'] }}"
                class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] transition-colors hover:text-primary-600 {{ $item['active'] ? 'text-primary-600' : 'text-gray-800' }}"
            >
                <span class="relative">
                    <x-icon name="{{ $item['icon'] }}" class="h-6 w-6" />
                    @if ($item['icon'] === 'cart' && ($cartCount ?? 0) > 0)
                        <span class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary-600 text-[10px] font-semibold text-white">
                            {{ $cartCount }}
                        </span>
                    @endif
                </span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>

<x-web-layout title="Mon compte" description="Gérez vos informations et vos commandes." :search="false">
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900">Mon compte</h1>
        <p class="mt-1 text-sm text-gray-600">Gérez vos informations et vos commandes.</p>

        <a
            href="{{ route('profile.edit') }}"
            class="mt-6 flex items-center gap-4 rounded-2xl bg-blush-100 p-5"
        >
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blush-200 text-primary-600">
                <x-icon name="user" class="h-7 w-7" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="font-bold text-gray-900">{{ $user->name }}</p>
                <p class="truncate text-sm text-gray-500">{{ $user->email }}</p>
                @if ($loyaltyBalance > 0)
                    <span class="mt-1.5 inline-flex items-center rounded-full bg-white px-2.5 py-1 text-xs font-medium text-primary-600">
                        Cliente fidèle
                    </span>
                @endif
            </div>
            <x-icon name="chevron-right" class="h-5 w-5 shrink-0 text-gray-400" />
        </a>

        @if ($orders->isNotEmpty())
            <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blush-100 text-primary-600">
                            <x-icon name="bag" class="h-4 w-4" />
                        </span>
                        <h2 class="text-sm font-bold text-gray-900">Mes commandes</h2>
                    </div>
                    <a href="{{ route('account.orders.index') }}" class="flex items-center gap-1 text-sm font-medium text-primary-600 hover:text-primary-700">
                        Voir toutes
                        <x-icon name="chevron-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-3 divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        <a
                            href="{{ route('account.orders.show', $order) }}"
                            class="flex items-center gap-3 py-3 first:pt-0 last:pb-0"
                        >
                            <div class="flex shrink-0 -space-x-3">
                                @forelse ($order->items->take(2) as $item)
                                    <div class="h-11 w-11 overflow-hidden rounded-full border-2 border-white bg-gray-50">
                                        @if ($item->product?->images->first())
                                            <img src="{{ $item->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                                        @endif
                                    </div>
                                @empty
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full border-2 border-white bg-gray-50 text-gray-300">
                                        <x-icon name="bag" class="h-4 w-4" />
                                    </div>
                                @endforelse
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900">Commande #{{ $order->order_number }}</p>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-xs text-gray-400">{{ $order->created_at->translatedFormat('d M Y') }}</span>
                                    <x-order-status-badge :status="$order->status" />
                                </div>
                            </div>

                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-400" />
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @php
            $menuItems = [
                ['icon' => 'user', 'label' => 'Informations personnelles', 'href' => route('profile.edit')],
                ['icon' => 'map-pin', 'label' => 'Adresses de livraison', 'href' => route('account.addresses.index')],
            ];
        @endphp

        <div class="mt-6 divide-y divide-gray-100 rounded-2xl border border-gray-100 bg-white">
            @foreach ($menuItems as $item)
                <a href="{{ $item['href'] }}" class="flex items-center gap-3 p-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blush-100 text-primary-600">
                        <x-icon name="{{ $item['icon'] }}" class="h-4 w-4" />
                    </span>
                    <span class="flex-1 text-sm font-medium text-gray-900">{{ $item['label'] }}</span>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-400" />
                </a>
            @endforeach
        </div>

        <a href="{{ route('pages.contact') }}" class="mt-6 flex items-center gap-3 rounded-2xl border border-gray-100 bg-white p-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blush-100 text-primary-600">
                <x-icon name="chat" class="h-4 w-4" />
            </span>
            <span class="flex-1">
                <span class="block text-sm font-semibold text-gray-900">Besoin d'aide ?</span>
                <span class="block text-xs text-gray-500">Notre équipe est là pour vous accompagner.</span>
            </span>
            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-400" />
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-6">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-gray-100 bg-white p-4 text-sm font-semibold text-primary-600 hover:bg-gray-50"
            >
                <x-icon name="arrow-right" class="h-4 w-4" />
                Se déconnecter
            </button>
        </form>
    </div>
</x-web-layout>

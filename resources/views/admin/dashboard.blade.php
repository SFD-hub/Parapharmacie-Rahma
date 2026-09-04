<x-admin-layout title="Tableau de bord" subtitle="Suivi de votre activité en temps réel">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <a
            href="{{ route('admin.orders.index') }}"
            class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg"
        >
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white">
                    <x-icon name="chart-bar" class="h-7 w-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">Chiffre d'affaires</p>
                    <p class="mt-0.5 truncate text-base font-bold text-gray-900">{{ number_format($revenue, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
            @if ($revenueChangePercent !== null)
                <p class="flex items-center gap-1 whitespace-nowrap text-xs font-semibold {{ $revenueChangePercent >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                    <x-icon name="{{ $revenueChangePercent >= 0 ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 shrink-0" />
                    {{ $revenueChangePercent >= 0 ? '+' : '' }}{{ number_format($revenueChangePercent, 1, ',', ' ') }}% vs mois dernier
                </p>
            @endif
        </a>

        <a
            href="{{ route('admin.orders.pending') }}"
            class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-300 [animation-delay:75ms] hover:-translate-y-1 hover:shadow-lg"
        >
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-info-600 text-white">
                    <x-icon name="cart" class="h-7 w-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">Commandes</p>
                    <p class="mt-0.5 truncate text-base font-bold text-gray-900">{{ $ordersToTreatCount }}</p>
                </div>
            </div>
            <p class="flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-info-600">
                <x-icon name="clock" class="h-3.5 w-3.5 shrink-0" />
                Total des commandes : {{ $ordersCount }}
            </p>
        </a>

        <a
            href="{{ route('admin.customers.index') }}"
            class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-300 [animation-delay:150ms] hover:-translate-y-1 hover:shadow-lg"
        >
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-success-600 text-white">
                    <x-icon name="user" class="h-7 w-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">Clients</p>
                    <p class="mt-0.5 truncate text-base font-bold text-gray-900">{{ $customersCount }}</p>
                </div>
            </div>
            <p class="flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-success-600">
                <x-icon name="chevron-up" class="h-3.5 w-3.5 shrink-0" />
                +{{ $newCustomersThisMonth }} nouveaux ce mois
            </p>
        </a>

        <a
            href="{{ route('admin.products.index') }}"
            class="animate-fade-in-up flex flex-col gap-3 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-300 [animation-delay:225ms] hover:-translate-y-1 hover:shadow-lg"
        >
            <div class="flex items-center gap-4">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-warning-600 text-white">
                    <x-icon name="archive" class="h-7 w-7" />
                </span>
                <div class="min-w-0">
                    <p class="text-sm text-gray-500">Produits</p>
                    <p class="mt-0.5 truncate text-base font-bold text-gray-900">{{ $productsCount }}</p>
                </div>
            </div>
            <p class="flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-warning-600">
                <x-icon name="exclamation" class="h-3.5 w-3.5 shrink-0" />
                {{ $alerts['outOfStock'] }} en rupture de stock
            </p>
        </a>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <livewire:admin.revenue-chart />
        </div>

        <x-ds.card>
            <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                <x-icon name="chart-bar" class="h-5 w-5 text-primary-600" />
                À surveiller
            </h2>

            <div class="mt-4 space-y-2.5">
                <a href="{{ route('admin.orders.pending') }}" class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 transition hover:border-primary-200 hover:bg-primary-50/40">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-warning-50 text-warning-600">
                        <x-icon name="cart" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0 flex-1">
                        @if ($alerts['pendingOrders'] > 0)
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ $alerts['pendingOrders'] }} commande{{ $alerts['pendingOrders'] > 1 ? 's' : '' }} en attente</span>
                            <span class="block truncate text-xs text-gray-500">À valider rapidement</span>
                        @else
                            <span class="block truncate text-sm font-semibold text-gray-900">Aucune commande en attente</span>
                            <span class="block truncate text-xs text-gray-500">Toutes les commandes sont traitées</span>
                        @endif
                    </span>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-300" />
                </a>

                <a href="{{ route('admin.products.index', ['stock' => 'out']) }}" class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 transition hover:border-primary-200 hover:bg-primary-50/40">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-danger-50 text-danger-600">
                        <x-icon name="exclamation" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0 flex-1">
                        @if ($alerts['outOfStock'] > 0)
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ $alerts['outOfStock'] }} produit{{ $alerts['outOfStock'] > 1 ? 's' : '' }} en rupture</span>
                            <span class="block truncate text-xs text-gray-500">Réapprovisionnement nécessaire</span>
                        @else
                            <span class="block truncate text-sm font-semibold text-gray-900">Aucun produit en rupture</span>
                            <span class="block truncate text-xs text-gray-500">Tout est en stock</span>
                        @endif
                    </span>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-300" />
                </a>

                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 transition hover:border-primary-200 hover:bg-primary-50/40">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-info-50 text-info-600">
                        <x-icon name="tag" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0 flex-1">
                        @if ($alerts['pendingPayments'] > 0)
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ $alerts['pendingPayments'] }} paiement{{ $alerts['pendingPayments'] > 1 ? 's' : '' }} non validé{{ $alerts['pendingPayments'] > 1 ? 's' : '' }}</span>
                            <span class="block truncate text-xs text-gray-500">Vérifiez les paiements</span>
                        @else
                            <span class="block truncate text-sm font-semibold text-gray-900">Aucun paiement en attente</span>
                            <span class="block truncate text-xs text-gray-500">Tous les paiements sont validés</span>
                        @endif
                    </span>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-300" />
                </a>

                <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="flex items-center gap-3 rounded-xl border border-gray-100 p-3 transition hover:border-primary-200 hover:bg-primary-50/40">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                        <x-icon name="archive" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0 flex-1">
                        @if ($alerts['lowStock'] > 0)
                            <span class="block truncate text-sm font-semibold text-gray-900">{{ $alerts['lowStock'] }} produit{{ $alerts['lowStock'] > 1 ? 's' : '' }} en stock faible</span>
                            <span class="block truncate text-xs text-gray-500">Pensez au réapprovisionnement</span>
                        @else
                            <span class="block truncate text-sm font-semibold text-gray-900">Aucun produit en stock faible</span>
                            <span class="block truncate text-xs text-gray-500">Niveaux de stock corrects</span>
                        @endif
                    </span>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-300" />
                </a>
            </div>
        </x-ds.card>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-ds.card>
            <div class="flex items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                    <span class="text-orange-500">🔥</span>
                    Produits les plus vendus
                </h2>
                <a href="{{ route('admin.products.index') }}" class="shrink-0 text-sm font-semibold text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>

            @if ($bestSellers->isEmpty())
                <div class="flex flex-col items-center gap-2 py-10 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 text-gray-300">
                        <x-icon name="box" class="h-6 w-6" />
                    </span>
                    <p class="text-sm text-gray-400">Aucune vente pour le moment.</p>
                </div>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-16">
                            <col class="w-28">
                            <col class="w-16">
                        </colgroup>
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                <th class="pb-2 font-medium">Produit</th>
                                <th class="pb-2 font-medium">Ventes</th>
                                <th class="pb-2 font-medium">CA</th>
                                <th class="pb-2 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($bestSellers as $rank => $entry)
                                <tr>
                                    <td class="py-3 pr-2">
                                        <a href="{{ route('admin.products.edit', $entry->product_id) }}" class="flex min-w-0 items-center gap-3 hover:text-primary-600">
                                            <span class="h-9 w-9 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                                                @if ($entry->product?->images->first())
                                                    <img src="{{ $entry->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                                                @else
                                                    <span class="flex h-full w-full items-center justify-center text-gray-300">
                                                        <x-icon name="box" class="h-4 w-4" />
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="min-w-0 truncate font-medium text-gray-900">{{ $entry->product?->name ?? 'Produit supprimé' }}</span>
                                        </a>
                                    </td>
                                    <td class="py-3 text-gray-600">{{ $entry->total_sold }}</td>
                                    <td class="truncate py-3 text-gray-600">{{ number_format($entry->total_revenue, 0, ',', ' ') }} FCFA</td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex whitespace-nowrap rounded-full bg-primary-50 px-2 py-1 text-xs font-semibold text-primary-700">Top {{ $rank + 1 }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ds.card>

        <x-ds.card>
            <div class="flex items-center justify-between gap-3">
                <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                    <x-icon name="chart-bar" class="h-5 w-5 text-gray-400" />
                    Produits les moins vendus
                </h2>
                <a href="{{ route('admin.products.index') }}" class="shrink-0 text-sm font-semibold text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>

            @if ($worstSellers->isEmpty())
                <div class="flex flex-col items-center gap-2 py-10 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 text-gray-300">
                        <x-icon name="box" class="h-6 w-6" />
                    </span>
                    <p class="text-sm text-gray-400">Aucune vente pour le moment.</p>
                </div>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col>
                            <col class="w-16">
                            <col class="w-16">
                        </colgroup>
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wide text-gray-400">
                                <th class="pb-2 font-medium">Produit</th>
                                <th class="pb-2 font-medium">Ventes</th>
                                <th class="pb-2 font-medium">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($worstSellers as $entry)
                                <tr>
                                    <td class="py-3 pr-2">
                                        <a href="{{ route('admin.products.edit', $entry->product_id) }}" class="flex min-w-0 items-center gap-3 hover:text-primary-600">
                                            <span class="h-9 w-9 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                                                @if ($entry->product?->images->first())
                                                    <img src="{{ $entry->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                                                @else
                                                    <span class="flex h-full w-full items-center justify-center text-gray-300">
                                                        <x-icon name="box" class="h-4 w-4" />
                                                    </span>
                                                @endif
                                            </span>
                                            <span class="min-w-0 truncate font-medium text-gray-900">{{ $entry->product?->name ?? 'Produit supprimé' }}</span>
                                        </a>
                                    </td>
                                    <td class="py-3 text-gray-600">{{ $entry->total_sold }}</td>
                                    <td class="py-3">
                                        @if ($entry->product && $entry->product->stock <= 5)
                                            <span class="inline-flex rounded-full bg-warning-50 px-2.5 py-1 text-xs font-semibold text-warning-700">Faible</span>
                                        @else
                                            <span class="text-gray-600">{{ $entry->product?->stock ?? '—' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ds.card>
    </div>

    @if ($lowStockProducts->isNotEmpty())
        <x-ds.card class="mt-8">
            <h2 class="text-sm font-semibold text-gray-900">Prévision de rupture</h2>
            <ul class="mt-4 divide-y divide-gray-100">
                @foreach ($lowStockProducts as $product)
                    <li class="flex items-center gap-3 py-3">
                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                            @if ($product->images->first())
                                <img src="{{ $product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-gray-300">
                                    <x-icon name="box" class="h-5 w-5" />
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.products.edit', $product) }}" class="block truncate text-sm font-medium text-gray-900 hover:text-primary-600">
                                {{ $product->name }}
                            </a>
                            <p class="text-xs text-gray-500">Stock actuel : {{ $product->stock }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-orange-50 px-3 py-1 text-right text-xs font-semibold text-orange-700">
                            @if ($lowStockForecasts[$product->id] === null)
                                Stock : {{ $product->stock }}
                            @else
                                Rupture probable dans {{ $lowStockForecasts[$product->id] }} jour{{ $lowStockForecasts[$product->id] > 1 ? 's' : '' }}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </x-ds.card>
    @endif

    <p class="mt-10 text-center text-xs text-gray-400">
        &copy; {{ now()->year }} Rahmane Parapharmacie. Tous droits réservés.
    </p>
</x-admin-layout>

<div x-data="{ filtersOpen: false }">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Boutique</h1>
        <p class="mt-1 text-sm text-gray-500">Découvrez nos soins sélectionnés pour votre beauté.</p>

        @if ($loyaltyOnly && $loyaltySettings)
            <div class="mt-6 rounded-3xl border border-primary-100 bg-gradient-to-br from-blush-50 to-primary-50 p-6 sm:p-8">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white">
                        <x-icon name="gift" class="h-6 w-6" />
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Achetez ces produits et gagnez des points de fidélité !</h2>
                        <p class="mt-1 text-sm text-gray-600">Tous les produits ci-dessous participent à notre programme de fidélité — profitez-en pour cumuler des points à chaque commande.</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/70 p-4">
                        <p class="text-sm font-bold text-gray-900">🛍️ Comment gagner des points ?</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">
                            Pour chaque tranche de <span class="font-semibold text-primary-700">{{ number_format($loyaltySettings['amount_per_point'], 0, ',', ' ') }} FCFA</span> dépensés sur votre commande, vous gagnez <span class="font-semibold text-primary-700">{{ $loyaltySettings['points_per_amount'] }} point{{ $loyaltySettings['points_per_amount'] > 1 ? 's' : '' }}</span> de fidélité, automatiquement crédités dès que votre commande est livrée.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/70 p-4">
                        <p class="text-sm font-bold text-gray-900">🎁 Comment utiliser vos points ?</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-600">
                            Chaque point vaut <span class="font-semibold text-primary-700">{{ number_format($loyaltySettings['point_value'], 0, ',', ' ') }} FCFA</span> de réduction. Utilisez-les lors de votre prochain achat, ou échangez-les contre des cadeaux depuis votre espace <span class="font-semibold">Mon compte → Fidélité</span>.
                        </p>
                    </div>
                </div>

                <a href="{{ route('account.loyalty.index') }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700">
                    Voir mon solde de points
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>
            </div>
        @endif

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="relative inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50"
                    @click="filtersOpen = true"
                >
                    <x-icon name="filter" class="h-4 w-4" />
                    Filtres
                    @if ($category || $brands !== [] || $minPrice !== null || $maxPrice !== null || $onSale || $isNew || $bestSeller || $inStock)
                        <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                    @endif
                </button>
                <p class="text-sm text-gray-500">{{ $products->total() }} produit{{ $products->total() > 1 ? 's' : '' }} trouvé{{ $products->total() > 1 ? 's' : '' }}</p>
            </div>

            <label class="text-right">
                <span class="block text-xs text-gray-500">Trier par</span>
                <x-web.product-sort />
            </label>
        </div>

        @if ($search !== '' || $category || $brands !== [] || $minPrice !== null || $maxPrice !== null || $onSale || $isNew || $bestSeller || $inStock)
            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($search !== '')
                    <x-web.filter-chip :label="'« '.$search.' »'" remove-action="$set('search', '')" />
                @endif

                @if ($category)
                    <x-web.filter-chip :label="$categories->firstWhere('slug', $category)['name'] ?? $category" remove-action="removeCategory" />
                @endif

                @foreach ($brands as $brandSlug)
                    <x-web.filter-chip
                        :label="$brandsList->firstWhere('slug', $brandSlug)['name'] ?? $brandSlug"
                        :remove-action="\"removeBrand('{$brandSlug}')\""
                    />
                @endforeach

                @if ($minPrice !== null || $maxPrice !== null)
                    <x-web.filter-chip :label="($minPrice ?? 0).' - '.($maxPrice ?? '∞').' FCFA'" remove-action="removePriceRange" />
                @endif

                @if ($onSale)
                    <x-web.filter-chip label="Promotions" remove-action="$set('onSale', false)" />
                @endif

                @if ($isNew)
                    <x-web.filter-chip label="Nouveautés" remove-action="$set('isNew', false)" />
                @endif

                @if ($bestSeller)
                    <x-web.filter-chip label="Meilleures ventes" remove-action="$set('bestSeller', false)" />
                @endif

                @if ($inStock)
                    <x-web.filter-chip label="En stock" remove-action="$set('inStock', false)" />
                @endif

                <button type="button" wire:click="resetFilters" class="text-xs font-medium text-primary-600 hover:text-primary-700">
                    Tout effacer
                </button>
            </div>
        @endif

        <div class="mt-6" wire:loading.class="opacity-50" wire:target="search, category, brands, minPrice, maxPrice, onSale, isNew, bestSeller, inStock, sort">
            @if ($products->isEmpty())
                <x-empty-state title="Aucun résultat" description="Essayez d'ajuster vos filtres ou votre recherche." icon="search">
                    <button type="button" wire:click="resetFilters" class="mt-2 text-sm font-medium text-primary-600 hover:text-primary-700">
                        Réinitialiser les filtres
                    </button>
                </x-empty-state>
            @else
                <x-web.product-grid :products="$products" :columns="4" />
            @endif
        </div>

        <x-pagination :paginator="$products" :livewire="true" />
    </div>

    <div x-show="filtersOpen" x-cloak class="fixed inset-0 z-40" role="dialog" aria-modal="true">
        <div
            class="fixed inset-0 bg-gray-900/40"
            @click="filtersOpen = false"
            x-show="filtersOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div
            class="fixed inset-y-0 right-0 flex w-full max-w-xs flex-col overflow-y-auto bg-white shadow-xl"
            x-show="filtersOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
        >
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4">
                <p class="text-sm font-semibold text-gray-900">Filtres</p>
                <button type="button" class="rounded-full p-2 text-gray-500 hover:bg-gray-100" @click="filtersOpen = false" aria-label="Fermer">
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>

            <div class="p-4">
                <x-web.product-filter :brands-list="$brandsList" />
            </div>
        </div>
    </div>
</div>

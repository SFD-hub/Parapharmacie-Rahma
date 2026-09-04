<x-web-layout :title="$product->name" :description="$product->short_description" :back="route('shop.index')">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <x-web.product-gallery :product="$product" />

            <div>
                @if ($product->brand)
                    <a
                        href="{{ route('shop.index', ['brand' => $product->brand->slug]) }}"
                        class="text-xs font-semibold uppercase tracking-wide text-primary-600"
                    >
                        {{ $product->brand->name }}
                    </a>
                @endif

                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $product->name }}</h1>

                <div class="mt-4 flex items-center gap-2">
                    <x-web.product-price :product="$product" size="lg" />
                    @if ($product->hasDiscount())
                        <span class="rounded-md bg-red-50 px-1.5 py-0.5 text-xs font-semibold text-red-600">
                            -{{ $product->discountPercent() }}%
                        </span>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="mt-4 text-sm leading-relaxed text-gray-600">{{ $product->short_description }}</p>
                @endif

                @php $stockStatus = $product->stockStatus(); $isExpired = $product->isExpired(); @endphp

                <p class="mt-6 text-sm font-medium text-gray-700">Quantité</p>
                <form
                    action="{{ route('cart.store') }}"
                    method="POST"
                    class="mt-2"
                    x-data="{ quantity: 1, max: {{ max((int) $product->stock, 1) }} }"
                >
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="quantity">

                    <div class="flex items-center gap-3">
                        <div class="flex shrink-0 items-center gap-3 rounded-full border border-gray-200 px-1 py-1">
                            <button
                                type="button"
                                @click="quantity = Math.max(1, quantity - 1)"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-50"
                                aria-label="Diminuer la quantité"
                            >
                                <x-icon name="minus" class="h-3.5 w-3.5" />
                            </button>
                            <span class="w-4 text-center text-sm font-semibold" x-text="quantity"></span>
                            <button
                                type="button"
                                @click="quantity = Math.min(max, quantity + 1)"
                                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 hover:bg-gray-50"
                                aria-label="Augmenter la quantité"
                            >
                                <x-icon name="plus" class="h-3.5 w-3.5" />
                            </button>
                        </div>

                        <button
                            type="submit"
                            @disabled($stockStatus === 'out_of_stock' || $isExpired)
                            class="flex flex-1 items-center justify-center gap-2 rounded-full bg-primary-600 py-3.5 text-sm font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400"
                        >
                            <x-icon name="cart" class="h-4 w-4" />
                            {{ $stockStatus === 'out_of_stock' || $isExpired ? 'Indisponible' : 'Ajouter au panier' }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 flex items-center justify-between text-sm">
                    @if ($isExpired)
                        <span class="flex items-center gap-1.5 text-red-600">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Produit expiré
                        </span>
                    @elseif ($stockStatus === 'in_stock')
                        <span class="flex items-center gap-1.5 text-emerald-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            En stock
                        </span>
                    @elseif ($stockStatus === 'low_stock')
                        <span class="flex items-center gap-1.5 text-amber-600">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Stock faible
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 text-red-600">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            Rupture de stock
                        </span>
                    @endif

                    @if (! $isExpired && $stockStatus !== 'out_of_stock')
                        <span class="text-gray-500">
                            Plus que <span class="font-semibold text-gray-700">{{ $product->stock }}</span> article{{ $product->stock > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                <div class="mt-8 divide-y divide-gray-100 border-t border-gray-100" x-data="{ open: 'description' }">
                    @if ($product->description)
                        <div class="py-4">
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 text-left"
                                @click="open = open === 'description' ? null : 'description'"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blush-100 text-primary-600">
                                    <x-icon name="document-text" class="h-4 w-4" />
                                </span>
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-gray-900">Description</span>
                                    <span class="block text-xs text-gray-500">Découvrez les bienfaits et les résultats de ce produit.</span>
                                </span>
                                <span class="h-4 w-4 shrink-0 text-gray-400 transition" :class="open === 'description' ? 'rotate-180' : ''">
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </span>
                            </button>
                            <div x-show="open === 'description'" class="mt-3 pl-12 text-sm leading-relaxed text-gray-600">
                                {{ $product->description }}
                            </div>
                        </div>
                    @endif

                    @if ($product->ingredients)
                        <div class="py-4">
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 text-left"
                                @click="open = open === 'ingredients' ? null : 'ingredients'"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blush-100 text-primary-600">
                                    <x-icon name="clipboard" class="h-4 w-4" />
                                </span>
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-gray-900">Ingrédients</span>
                                    <span class="block text-xs text-gray-500">Voir la liste complète des ingrédients.</span>
                                </span>
                                <span class="h-4 w-4 shrink-0 text-gray-400 transition" :class="open === 'ingredients' ? 'rotate-180' : ''">
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </span>
                            </button>
                            <div x-show="open === 'ingredients'" class="mt-3 pl-12 text-sm leading-relaxed text-gray-600">
                                {{ $product->ingredients }}
                            </div>
                        </div>
                    @endif

                    @if ($product->usage_instructions)
                        <div class="py-4">
                            <button
                                type="button"
                                class="flex w-full items-center gap-3 text-left"
                                @click="open = open === 'usage' ? null : 'usage'"
                            >
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blush-100 text-primary-600">
                                    <x-icon name="check" class="h-4 w-4" />
                                </span>
                                <span class="flex-1">
                                    <span class="block text-sm font-semibold text-gray-900">Conseils d'utilisation</span>
                                    <span class="block text-xs text-gray-500">Comment utiliser ce produit pour de meilleurs résultats.</span>
                                </span>
                                <span class="h-4 w-4 shrink-0 text-gray-400 transition" :class="open === 'usage' ? 'rotate-180' : ''">
                                    <x-icon name="chevron-down" class="h-4 w-4" />
                                </span>
                            </button>
                            <div x-show="open === 'usage'" class="mt-3 pl-12 text-sm leading-relaxed text-gray-600">
                                {{ $product->usage_instructions }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <section class="mt-16" x-data="{ reviewModalOpen: {{ $errors->any() ? 'true' : 'false' }} }">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Avis clients</h2>
                    @if ($product->reviews_count > 0)
                        <div class="mt-1 flex items-center gap-2">
                            <x-web.star-rating :rating="$product->reviews_avg_rating ?? 0" />
                            <span class="text-sm text-gray-500">{{ number_format($product->reviews_avg_rating ?? 0, 1) }} · {{ $product->reviews_count }} avis</span>
                        </div>
                    @endif
                </div>

                <button
                    type="button"
                    @click="reviewModalOpen = true"
                    class="flex shrink-0 items-center gap-2 rounded-full bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
                >
                    <x-icon name="star-outline" class="h-4 w-4" />
                    {{ $myReview ? 'Modifier mon avis' : 'Donner mon avis' }}
                </button>
            </div>

            @if ($reviews->isEmpty())
                <x-empty-state
                    title="Aucun avis pour le moment"
                    description="Soyez le premier à partager votre expérience sur ce produit."
                    icon="star-outline"
                />
            @else
                <x-web.carousel :autoplay="false">
                    @foreach ($reviews as $review)
                        <x-web.review-card :review="$review" />
                    @endforeach
                </x-web.carousel>
            @endif

            <div x-show="reviewModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-gray-900/50" @click="reviewModalOpen = false"></div>

                <div
                    x-show="reviewModalOpen"
                    x-transition
                    class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
                    @click.outside="reviewModalOpen = false"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900">{{ $myReview ? 'Modifier mon avis' : 'Donner mon avis' }}</h3>
                        <button type="button" @click="reviewModalOpen = false" class="p-1 text-gray-400 hover:text-gray-600" aria-label="Fermer">
                            <x-icon name="x-mark" class="h-5 w-5" />
                        </button>
                    </div>

                    <form method="POST" action="{{ route('products.reviews.store', $product) }}" class="mt-4 space-y-4">
                        @csrf
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-700">Votre note</p>
                            <x-web.star-rating-input name="rating" :value="old('rating', $myReview->rating ?? 0)" />
                            @error('rating')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @guest
                            <div>
                                <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Votre nom</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endguest
                        <div>
                            <label for="comment" class="mb-2 block text-sm font-medium text-gray-700">Votre commentaire (optionnel)</label>
                            <textarea
                                id="comment"
                                name="comment"
                                rows="4"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                            >{{ old('comment', $myReview->comment ?? '') }}</textarea>
                            @error('comment')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-full bg-primary-600 py-3 text-sm font-semibold text-white hover:bg-primary-700">
                            Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </section>

        @if ($product->complements->isNotEmpty())
            <section class="mt-16">
                <h2 class="mb-4 text-lg font-bold text-gray-900 sm:text-xl">Complète bien votre routine</h2>
                <x-web.product-grid :products="$product->complements" />
            </section>
        @endif

        @if ($related->isNotEmpty())
            <section class="mt-16">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Produits similaires</h2>
                    @if ($product->category)
                        <a
                            href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                            class="flex items-center gap-1 text-sm font-medium text-primary-600"
                        >
                            Voir tout
                            <x-icon name="chevron-right" class="h-4 w-4" />
                        </a>
                    @endif
                </div>
                <x-web.product-grid :products="$related" />
            </section>
        @endif

        @if ($recentlyViewedProducts->isNotEmpty())
            <section class="mt-16">
                <h2 class="mb-4 text-lg font-bold text-gray-900 sm:text-xl">Récemment consultés</h2>
                <x-web.product-grid :products="$recentlyViewedProducts" />
            </section>
        @endif
    </div>
</x-web-layout>

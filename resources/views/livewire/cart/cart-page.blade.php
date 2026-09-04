<div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Mon panier ({{ $items->sum('quantity') }})</h1>
    <p class="mt-1 text-sm text-gray-500">Vérifiez vos articles avant de passer commande.</p>

    @if ($items->isEmpty())
        <x-empty-state
            title="Votre panier est vide"
            description="Découvrez notre catalogue pour commencer vos achats."
            icon="cart"
        >
            <a
                href="{{ route('shop.index') }}"
                class="mt-2 inline-flex items-center gap-2 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
            >
                Découvrir la boutique
            </a>
        </x-empty-state>
    @else
        @unless ($totals->qualifiesForFreeShipping())
            <div class="mt-6 rounded-2xl bg-blush-100 p-4">
                <p class="flex items-center gap-1.5 text-sm text-gray-700">
                    <x-icon name="truck" class="h-4 w-4 shrink-0 text-primary-600" />
                    Plus que
                    <strong class="text-primary-600">{{ number_format($totals->amountRemainingForFreeShipping(), 0, ',', ' ') }} FCFA</strong>
                    pour bénéficier de la livraison offerte !
                </p>
                <div class="mt-2 flex items-center gap-3">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-white">
                        <div
                            class="h-full rounded-full bg-primary-600"
                            style="width: {{ min(100, (int) round($totals->subtotal / $totals->freeShippingThreshold * 100)) }}%"
                        ></div>
                    </div>
                    <span class="shrink-0 text-xs text-gray-500">
                        {{ number_format($totals->subtotal, 0, ',', ' ') }} / {{ number_format($totals->freeShippingThreshold, 0, ',', ' ') }} FCFA
                    </span>
                </div>
            </div>
        @endunless

        <div class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                @foreach ($items as $item)
                    <div wire:key="cart-item-{{ $item->id }}" class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4">
                        <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-50">
                            @if ($item->product->images->first())
                                <img src="{{ $item->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    @if ($item->product->brand)
                                        <p class="text-sm font-bold text-gray-900">{{ $item->product->brand->name }}</p>
                                    @endif
                                    <a href="{{ route('products.show', $item->product) }}" class="block truncate text-sm text-gray-600">
                                        {{ $item->product->name }}
                                    </a>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeItem({{ $item->id }})"
                                    class="shrink-0 p-1.5 text-primary-500 hover:text-primary-700"
                                    aria-label="Retirer du panier"
                                >
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>

                            @if ($item->product->category)
                                <span class="mt-2 inline-flex items-center rounded-full bg-blush-100 px-2.5 py-1 text-xs font-medium text-primary-600">
                                    {{ $item->product->category->name }}
                                </span>
                            @endif

                            <div class="mt-3 flex items-end justify-between gap-2">
                                <p class="text-sm font-bold text-primary-600">
                                    {{ number_format($item->unitPrice(), 0, ',', ' ') }} FCFA
                                </p>

                                <div class="flex flex-col items-end gap-1.5">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50"
                                            aria-label="Diminuer la quantité"
                                        >
                                            <x-icon name="minus" class="h-3.5 w-3.5" />
                                        </button>
                                        <span class="w-6 text-center text-sm font-medium">{{ $item->quantity }}</span>
                                        <button
                                            type="button"
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            @disabled($item->quantity >= $item->product->stock)
                                            class="flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                            aria-label="Augmenter la quantité"
                                        >
                                            <x-icon name="plus" class="h-3.5 w-3.5" />
                                        </button>
                                    </div>
                                    <p class="text-sm font-bold text-primary-600">
                                        {{ number_format($item->total(), 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <button
                    type="button"
                    wire:click="clearCart"
                    wire:confirm="Vider tout le panier ?"
                    class="text-sm font-medium text-gray-500 hover:text-red-600"
                >
                    Vider le panier
                </button>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 lg:sticky lg:top-24 lg:self-start">
                <h2 class="text-sm font-semibold text-gray-900">Résumé de la commande</h2>

                <x-totals-summary
                    :subtotal="$totals->subtotal"
                    :discount="$totals->discount"
                    :packDiscount="$totals->packDiscount"
                    :shipping="$totals->shippingCost"
                    :total="$totals->total"
                    class="mt-4"
                />

                <a
                    href="{{ route('checkout.index') }}"
                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-full bg-primary-600 py-3 text-center text-sm font-semibold text-white hover:bg-primary-700"
                >
                    Passer la commande
                    <x-icon name="chevron-right" class="h-4 w-4" />
                </a>

                <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-gray-500">
                    <x-icon name="shield" class="h-3.5 w-3.5 shrink-0" />
                    Paiement 100% sécurisé
                </p>
            </div>
        </div>
    @endif
</div>

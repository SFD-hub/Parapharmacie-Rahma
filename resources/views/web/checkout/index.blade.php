<x-web-layout title="Finaliser ma commande">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Finaliser ma commande</h1>

        <form id="new-address-form" method="POST" action="{{ route('checkout.addresses.store') }}"></form>

        <div
            class="mt-6 grid grid-cols-1 gap-8 lg:grid-cols-3"
            x-data="{
                usePoints: false,
                points: 0,
                get discount() { return Math.round(this.points * {{ (float) $loyaltyPointValue }}); },
            }"
        >
            <form id="place-order-form" method="POST" action="{{ route('checkout.store') }}" class="space-y-6 lg:col-span-2">
                @csrf

                <div class="rounded-2xl border border-gray-100 bg-white p-5" x-data="{ mode: '{{ $addresses->isEmpty() ? 'new' : 'existing' }}' }">
                    <h2 class="text-sm font-semibold text-gray-900">Adresse de livraison</h2>

                    @if ($addresses->isNotEmpty())
                        <div class="mt-4 space-y-2" x-show="mode === 'existing'">
                            @foreach ($addresses as $address)
                                <label class="flex items-start gap-3 rounded-xl border border-gray-200 p-3 text-sm has-[:checked]:border-primary-600 has-[:checked]:bg-blush-50">
                                    <input
                                        type="radio"
                                        name="address_id"
                                        value="{{ $address->id }}"
                                        {{ old('address_id', $addresses->first()?->id) == $address->id ? 'checked' : '' }}
                                        class="mt-1 border-gray-300 text-primary-600 focus:ring-primary-500"
                                    >
                                    <span>
                                        <span class="block font-medium text-gray-900">{{ $address->first_name }} {{ $address->last_name }}</span>
                                        <span class="block text-gray-500">{{ $address->address_line1 }}, {{ $address->city }}, {{ $address->country }}</span>
                                        @if ($address->phone)
                                            <span class="block text-gray-400">{{ $address->phone }}</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <button type="button" @click="mode = 'new'" x-show="mode === 'existing'" class="mt-3 text-sm font-medium text-primary-600 hover:text-primary-700">
                            + Ajouter une nouvelle adresse
                        </button>
                    @endif

                    <div x-show="mode === 'new'" @if ($addresses->isNotEmpty()) x-cloak @endif class="mt-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <x-text-input form="new-address-form" name="first_name" placeholder="Prénom" class="w-full" value="{{ old('first_name') }}" />
                            <x-text-input form="new-address-form" name="last_name" placeholder="Nom" class="w-full" value="{{ old('last_name') }}" />
                        </div>
                        <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-1" />

                        <x-text-input form="new-address-form" name="phone" placeholder="Téléphone (optionnel)" class="w-full" value="{{ old('phone') }}" />

                        <x-text-input form="new-address-form" name="address_line1" placeholder="Adresse" class="w-full" value="{{ old('address_line1') }}" />
                        <x-input-error :messages="$errors->get('address_line1')" class="mt-1" />

                        <x-text-input form="new-address-form" name="address_line2" placeholder="Complément d'adresse (optionnel)" class="w-full" value="{{ old('address_line2') }}" />

                        <div class="grid grid-cols-2 gap-3">
                            <x-text-input form="new-address-form" name="city" placeholder="Ville" class="w-full" value="{{ old('city') }}" />
                            <x-text-input form="new-address-form" name="postal_code" placeholder="Code postal" class="w-full" value="{{ old('postal_code') }}" />
                        </div>
                        <x-input-error :messages="$errors->get('city')" class="mt-1" />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-1" />

                        <x-text-input form="new-address-form" name="country" placeholder="Pays" class="w-full" value="{{ old('country', 'Sénégal') }}" />
                        <x-input-error :messages="$errors->get('country')" class="mt-1" />

                        <button type="submit" form="new-address-form" class="rounded-full bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                            Enregistrer cette adresse
                        </button>

                        @if ($addresses->isNotEmpty())
                            <button type="button" @click="mode = 'existing'" class="ml-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                                Annuler
                            </button>
                        @endif
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Méthode de paiement</h2>
                    <div class="mt-4 space-y-2">
                        @foreach ($paymentMethods as $method)
                            <label class="flex items-center gap-3 rounded-xl border border-gray-200 p-3 text-sm has-[:checked]:border-primary-600 has-[:checked]:bg-blush-50">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="{{ $method->value }}"
                                    {{ old('payment_method') === $method->value || (! old('payment_method') && $loop->first) ? 'checked' : '' }}
                                    class="border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                {{ $method->label() }}
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                </div>

                @if ($loyaltyEnabled && $loyaltyBalance > 0)
                    <div class="rounded-2xl border border-gray-100 bg-white p-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-900">Points de fidélité</h2>
                            <span class="text-xs text-gray-400">Solde : {{ $loyaltyBalance }} pts</span>
                        </div>

                        @if ($loyaltyMaxPoints > 0)
                            <label class="mt-3 flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" x-model="usePoints" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                Utiliser mes points pour réduire ma commande
                            </label>

                            <div x-show="usePoints" x-cloak class="mt-3 space-y-2">
                                <input type="range" min="0" max="{{ $loyaltyMaxPoints }}" x-model.number="points" class="w-full accent-primary-600">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span x-text="points + ' points'"></span>
                                    <span class="font-semibold text-primary-600" x-text="'-' + discount.toLocaleString('fr-FR') + ' FCFA'"></span>
                                </div>
                                <p class="text-xs text-gray-400">Jusqu'à {{ $loyaltyMaxPoints }} points utilisables sur cette commande.</p>
                            </div>
                        @else
                            <p class="mt-2 text-xs text-gray-400">Pas assez de points pour une réduction sur cette commande.</p>
                        @endif

                        <input type="hidden" name="points_used" :value="usePoints ? points : 0">
                    </div>
                @endif

                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <x-input-label for="notes" value="Notes de commande (optionnel)" />
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    >{{ old('notes') }}</textarea>
                </div>
            </form>

            <div class="rounded-2xl border border-gray-100 bg-white p-5 lg:sticky lg:top-24 lg:self-start">
                <h2 class="text-sm font-semibold text-gray-900">Résumé de la commande</h2>

                <ul class="mt-4 space-y-3 divide-y divide-gray-100 text-sm">
                    @foreach ($items as $item)
                        <li class="flex items-center gap-3 pt-3 first:pt-0">
                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                                @if ($item->product->images->first())
                                    <img src="{{ $item->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900">{{ $item->product->name }}</p>
                                <p class="text-gray-500">{{ $item->quantity }} × {{ number_format($item->unitPrice(), 0, ',', ' ') }} FCFA</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                    <div class="flex items-center justify-between text-gray-600">
                        <dt>Sous-total</dt>
                        <dd>{{ number_format($totals->subtotal, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @if ($totals->discount > 0)
                        <div class="flex items-center justify-between text-emerald-600">
                            <dt>Remise</dt>
                            <dd>-{{ number_format($totals->discount, 0, ',', ' ') }} FCFA</dd>
                        </div>
                    @endif
                    @if ($totals->packDiscount > 0)
                        <div class="flex items-center justify-between text-emerald-600">
                            <dt>Économie pack</dt>
                            <dd>-{{ number_format($totals->packDiscount, 0, ',', ' ') }} FCFA</dd>
                        </div>
                    @endif
                    <template x-if="usePoints && points > 0">
                        <div class="flex items-center justify-between text-primary-600">
                            <dt>Réduction fidélité</dt>
                            <dd x-text="'-' + discount.toLocaleString('fr-FR') + ' FCFA'"></dd>
                        </div>
                    </template>
                    <div class="flex items-center justify-between text-gray-600">
                        <dt>Livraison</dt>
                        <dd>{{ $totals->shippingCost > 0 ? number_format($totals->shippingCost, 0, ',', ' ').' FCFA' : 'Offerte' }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-base font-bold text-gray-900">
                        <dt>Total</dt>
                        <dd x-text="Math.max(0, {{ (float) $totals->total }} - (usePoints ? discount : 0)).toLocaleString('fr-FR') + ' FCFA'"></dd>
                    </div>
                </dl>

                <button type="submit" form="place-order-form" class="mt-5 w-full rounded-full bg-primary-600 py-3 text-center text-sm font-semibold text-white hover:bg-primary-700">
                    Confirmer la commande
                </button>
            </div>
        </div>
    </div>
</x-web-layout>

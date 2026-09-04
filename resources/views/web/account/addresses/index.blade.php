<x-web-layout title="Adresses de livraison" :back="route('dashboard')" :search="false">
    <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Adresses de livraison</h1>
        <p class="mt-1 text-sm text-gray-600">Vos adresses enregistrées lors de vos commandes.</p>

        @if ($addresses->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="Aucune adresse enregistrée"
                    description="Vos adresses apparaîtront ici après votre première commande."
                    icon="map-pin"
                />
            </div>
        @else
            <div class="mt-6 space-y-3">
                @foreach ($addresses as $address)
                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $address->first_name }} {{ $address->last_name }}
                                    @if ($address->is_default)
                                        <span class="ml-1 rounded-full bg-blush-100 px-2 py-0.5 text-[11px] font-medium text-primary-600">Par défaut</span>
                                    @endif
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $address->address_line1 }}
                                    @if ($address->address_line2)
                                        , {{ $address->address_line2 }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500">{{ $address->postal_code }} {{ $address->city }}, {{ $address->country }}</p>
                                @if ($address->phone)
                                    <p class="mt-1 text-sm text-gray-400">{{ $address->phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-web-layout>

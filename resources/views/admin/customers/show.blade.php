<x-admin-layout :title="$customer->name">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="mb-1 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <x-icon name="chevron-left" class="h-4 w-4" /> Clients
            </a>
            <h1 class="text-lg font-semibold text-gray-900">{{ $customer->name }}</h1>
            <p class="text-sm text-gray-500">
                {{ $customer->email }}
                @if ($customer->phone)
                    · {{ $customer->phone }}
                @endif
                · Client depuis le {{ $customer->created_at->translatedFormat('d M Y') }}
            </p>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-admin.stat-card icon="bag" label="Commandes" :value="$stats['orders_count']" color="blue" />
        <x-admin.stat-card icon="chart-bar" label="Total dépensé" :value="number_format($stats['total_spent'], 0, ',', ' ').' FCFA'" color="emerald" />
        <x-admin.stat-card icon="gift" label="Points fidélité" :value="$stats['loyalty_balance']" color="indigo" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ds.card>
                <h2 class="text-sm font-semibold text-gray-900">Commandes récentes</h2>

                @if ($orders->isEmpty())
                    <p class="mt-3 text-sm text-gray-400">Ce client n'a pas encore passé de commande.</p>
                @else
                    <ul class="mt-3 divide-y divide-gray-100 text-sm">
                        @foreach ($orders as $order)
                            <li>
                                <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between gap-3 py-3 hover:bg-blush-50/40">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                        <p class="text-xs text-gray-400">{{ $order->created_at->translatedFormat('d M Y') }} · {{ $order->items_count }} article(s)</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <x-order-status-badge :status="$order->status" />
                                        <span class="font-semibold text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ds.card>

            <x-ds.card>
                <h2 class="text-sm font-semibold text-gray-900">Notes internes</h2>
                <p class="mt-1 text-xs text-gray-400">Visibles uniquement par l'équipe, jamais par le client.</p>
                <form method="POST" action="{{ route('admin.customers.notes.update', $customer) }}" class="mt-3">
                    @csrf
                    @method('PATCH')
                    <textarea
                        name="admin_notes"
                        rows="4"
                        placeholder="Ex. préfère être livré le matin, allergique aux parfums forts..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    >{{ old('admin_notes', $customer->admin_notes) }}</textarea>
                    <x-input-error :messages="$errors->get('admin_notes')" class="mt-2" />
                    <x-ds.button type="submit" size="sm" class="mt-3">Enregistrer les notes</x-ds.button>
                </form>
            </x-ds.card>
        </div>

        <div class="space-y-6">
            <x-ds.card>
                <h2 class="text-sm font-semibold text-gray-900">Adresses</h2>
                @if ($customer->addresses->isEmpty())
                    <p class="mt-3 text-sm text-gray-400">Aucune adresse enregistrée.</p>
                @else
                    <ul class="mt-3 space-y-3 text-sm">
                        @foreach ($customer->addresses as $address)
                            <li class="rounded-lg border border-gray-100 p-3">
                                <p class="font-medium text-gray-900">
                                    {{ $address->first_name }} {{ $address->last_name }}
                                    @if ($address->is_default)
                                        <span class="ml-1 rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-600">Par défaut</span>
                                    @endif
                                </p>
                                <p class="text-gray-500">{{ $address->address_line1 }}@if ($address->address_line2), {{ $address->address_line2 }}@endif</p>
                                <p class="text-gray-500">{{ $address->city }}@if ($address->postal_code), {{ $address->postal_code }}@endif</p>
                                <p class="text-gray-500">{{ $address->phone }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ds.card>
        </div>
    </div>
</x-admin-layout>

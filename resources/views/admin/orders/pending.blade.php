<x-admin-layout title="Commandes">
    <x-admin.orders.tabs active="pending" />

    <div class="mb-6 grid grid-cols-3 gap-3">
        <x-admin.stat-card icon="clipboard" label="À traiter" :value="$stats['total_count']" color="blue" />
        <x-admin.stat-card icon="clock" label="En attente" :value="$stats['pending']" color="amber" />
        <x-admin.stat-card icon="check" label="Confirmées" :value="$stats['confirmed']" color="indigo" />
    </div>

    <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
        <div class="relative w-full sm:w-64">
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="N° commande, client..."
                class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500"
            />
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        </div>

        <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Tous statuts</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <select name="payment_method" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Tous paiements</option>
            @foreach ($paymentMethods as $method)
                <option value="{{ $method->value }}" @selected(request('payment_method') === $method->value)>{{ $method->label() }}</option>
            @endforeach
        </select>

        <x-ds.button type="submit" variant="secondary" size="md">Filtrer</x-ds.button>

        @if (request()->anyFilled(['search', 'status', 'payment_method']))
            <a href="{{ route('admin.orders.pending') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
        @endif
    </form>

    {{-- Desktop / tablet: table --}}
    <div class="hidden overflow-hidden rounded-2xl border border-gray-100 bg-white md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3"><x-admin.sortable-th column="order_number" label="Commande" /></th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Téléphone</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Date" /></th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="total" label="Montant" /></th>
                        <th class="px-4 py-3">Paiement</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Articles</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
                        <tr
                            onclick="window.location='{{ route('admin.orders.show', $order) }}'"
                            class="cursor-pointer transition hover:bg-blush-50/40"
                        >
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->address?->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->payment_method?->label() }}</td>
                            <td class="px-4 py-3"><x-order-status-badge :status="$order->status" /></td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->items_count }}</td>
                            <td class="px-4 py-3" onclick="event.stopPropagation()">
                                <div class="flex flex-wrap items-center justify-end gap-2 text-right">
                                    <x-admin.orders.validate-button :order="$order" />
                                    <x-ds.button href="{{ route('admin.orders.show', $order) }}" variant="secondary" size="sm">Voir</x-ds.button>
                                </div>
                                <x-admin.orders.invoice-actions :order="$order" :compact="true" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-empty-state
                                    title="Aucune commande à traiter"
                                    description="Toutes les commandes en attente ou confirmées apparaîtront ici."
                                    icon="archive"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile: cards --}}
    <div class="space-y-3 md:hidden">
        @forelse ($orders as $order)
            <div onclick="window.location='{{ route('admin.orders.show', $order) }}'" class="cursor-pointer rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                    <x-order-status-badge :status="$order->status" />
                </div>
                <p class="mt-1 text-sm text-gray-500">{{ $order->user?->name ?? '—' }} · {{ $order->address?->phone ?? '—' }}</p>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="text-gray-400">{{ $order->created_at->translatedFormat('d M Y') }} · {{ $order->items_count }} article(s)</span>
                    <span class="font-semibold text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="mt-3 flex items-center gap-2" onclick="event.stopPropagation()">
                    <x-admin.orders.validate-button :order="$order" />
                </div>
            </div>
        @empty
            <x-empty-state
                title="Aucune commande à traiter"
                description="Toutes les commandes en attente ou confirmées apparaîtront ici."
                icon="archive"
            />
        @endforelse
    </div>

    <x-pagination :paginator="$orders" />
</x-admin-layout>

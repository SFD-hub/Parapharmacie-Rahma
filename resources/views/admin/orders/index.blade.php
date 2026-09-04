<x-admin-layout title="Commandes">
    <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-2">
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

            <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Filtrer
            </button>

            @if (request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3"><x-admin.sortable-th column="order_number" label="Commande" /></th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="total" label="Total" /></th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Date" /></th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $order->user?->name ?? '—' }}
                                @if ($order->user)
                                    <span class="block text-xs text-gray-400">{{ $order->user->email }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3"><x-order-status-badge :status="$order->status" /></td>
                            <td class="px-4 py-3 text-gray-500">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    title="Aucune commande"
                                    description="Ajustez vos filtres ou attendez les premières commandes."
                                    icon="archive"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$orders" />
</x-admin-layout>

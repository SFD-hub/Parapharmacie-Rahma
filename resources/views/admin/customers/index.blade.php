<x-admin-layout title="Clients">
    <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
        <x-admin.stat-card icon="user" label="Clients" :value="$stats['total']" color="blue" />
        <x-admin.stat-card icon="sparkles" label="Nouveaux ce mois" :value="$stats['new_this_month']" color="emerald" />
        <x-admin.stat-card icon="bag" label="Ont déjà commandé" :value="$stats['with_orders']" color="indigo" />
    </div>

    <form method="GET" class="mb-4 flex items-center gap-2">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Rechercher par nom ou email..."
            class="w-full max-w-sm rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
        >
        <x-ds.button type="submit" variant="secondary" size="md">Rechercher</x-ds.button>
        @if (request()->filled('search'))
            <a href="{{ route('admin.customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3"><x-admin.sortable-th column="name" label="Client" /></th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Commandes</th>
                        <th class="px-4 py-3">Total dépensé</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Inscrit le" /></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $customer)
                        <tr
                            onclick="window.location='{{ route('admin.customers.show', $customer) }}'"
                            class="cursor-pointer transition hover:bg-blush-50/40"
                        >
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $customer->email }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $customer->orders_count }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ number_format($customer->total_spent ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3 text-gray-500">{{ $customer->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state
                                    title="Aucun client"
                                    description="Aucun client ne correspond à votre recherche."
                                    icon="user"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$customers" />
</x-admin-layout>

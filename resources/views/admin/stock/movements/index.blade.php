<x-admin-layout title="Mouvements de stock">
    <div class="mb-4 flex items-center justify-end">
        <a
            href="{{ route('admin.stock.movements.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouveau mouvement
        </a>
    </div>

    <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-2">
            <select name="product" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tous produits</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>

            <select name="type" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tous types</option>
                @foreach ($types as $type)
                    <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <span class="text-sm text-gray-400">→</span>
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">

            <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Filtrer
            </button>

            @if (request()->anyFilled(['product', 'type', 'from', 'to']))
                <a href="{{ route('admin.stock.movements.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Produit</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="quantity" label="Quantité" /></th>
                        <th class="px-4 py-3">Utilisateur</th>
                        <th class="px-4 py-3">Commentaire</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Date" /></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($movements as $movement)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $movement->product->name }}</td>
                            <td class="px-4 py-3"><x-stock-movement-badge :type="$movement->type" /></td>
                            <td class="px-4 py-3 font-semibold {{ $movement->quantity >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $movement->admin?->name ?? 'Système' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $movement->comment ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $movement->created_at->translatedFormat('d M Y à H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    title="Aucun mouvement"
                                    description="Ajustez vos filtres ou enregistrez un premier mouvement."
                                    icon="archive"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$movements" />
</x-admin-layout>

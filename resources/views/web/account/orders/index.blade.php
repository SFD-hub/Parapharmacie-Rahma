<x-web-layout title="Mes commandes">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Mes commandes</h1>

        @if ($orders->isEmpty())
            <x-empty-state
                title="Aucune commande"
                description="Vous n'avez pas encore passé de commande."
                icon="archive"
            >
                <a
                    href="{{ route('shop.index') }}"
                    class="mt-2 inline-flex items-center gap-2 rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
                >
                    Découvrir la boutique
                </a>
            </x-empty-state>
        @else
            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Commande</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $order->created_at->translatedFormat('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <x-order-status-badge :status="$order->status" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-900">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('account.orders.show', $order) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <x-pagination :paginator="$orders" />
        @endif
    </div>
</x-web-layout>

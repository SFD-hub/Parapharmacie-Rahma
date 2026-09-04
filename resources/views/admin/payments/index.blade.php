<x-admin-layout title="Paiements">
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
        <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Tous statuts</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
            Filtrer
        </button>

        @if (request()->filled('status'))
            <a href="{{ route('admin.payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Commande</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Méthode de paiement</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="amount" label="Montant" /></th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Date" /></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @if ($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="hover:text-primary-600">
                                        #{{ $payment->order->order_number }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->order?->user?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->provider?->label() ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $payment->status->color() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $payment->created_at->translatedFormat('d M Y à H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    title="Aucun paiement"
                                    description="Ajustez vos filtres ou revenez plus tard."
                                    icon="tag"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$payments" />
</x-admin-layout>

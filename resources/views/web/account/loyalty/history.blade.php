<x-web-layout title="Historique de mes points">
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <a href="{{ route('account.loyalty.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <x-icon name="chevron-left" class="h-4 w-4" />
            Ma fidélité
        </a>

        <div class="mt-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Historique de mes points</h1>
            <span class="text-sm text-gray-500">Solde : <span class="font-semibold text-primary-600">{{ $balance }} pts</span></span>
        </div>

        @if ($movements->isEmpty())
            <x-empty-state
                title="Aucun mouvement"
                description="Vos points gagnés et utilisés apparaîtront ici."
                icon="gift"
            />
        @else
            <div class="mt-6 overflow-hidden rounded-2xl border border-gray-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Points</th>
                                <th class="px-4 py-3">Commande</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3"><x-loyalty-movement-badge :type="$movement->type" /></td>
                                    <td class="px-4 py-3 font-semibold {{ $movement->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->points >= 0 ? '+' : '' }}{{ $movement->points }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->order?->order_number ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->created_at->translatedFormat('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <x-pagination :paginator="$movements" />
        @endif
    </div>
</x-web-layout>

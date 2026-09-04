<x-web-layout title="Ma fidélité">
    <div class="mx-auto max-w-4xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Ma fidélité</h1>

        @if (! $settings['enabled'])
            <div class="mt-8">
                <x-empty-state
                    title="Programme de fidélité indisponible"
                    description="Le programme de fidélité n'est pas actif pour le moment."
                    icon="gift"
                />
            </div>
        @else
            <div class="mt-6 rounded-3xl bg-blush-100 p-6 text-center">
                <p class="text-sm text-gray-600">Votre solde</p>
                <p class="mt-1 text-4xl font-bold text-primary-600">{{ $balance }} pts</p>
                <p class="mt-1 text-sm text-gray-500">≈ {{ number_format($balanceValue, 0, ',', ' ') }} FCFA</p>
                <a href="{{ route('account.loyalty.history') }}" class="mt-4 inline-block text-sm font-medium text-primary-600 hover:text-primary-700">
                    Voir mon historique complet →
                </a>
            </div>

            @if ($gifts->isNotEmpty())
                <div class="mt-10">
                    <h2 class="mb-4 text-lg font-bold text-gray-900">Cadeaux disponibles</h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($gifts as $gift)
                            @php $image = $gift->product->images->first(); @endphp
                            <div class="flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white">
                                <div class="aspect-square bg-gray-50">
                                    @if ($image)
                                        <img src="{{ $image->path }}" alt="{{ $gift->product->name }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-gray-300">
                                            <x-icon name="gift" class="h-10 w-10" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col gap-2 p-4">
                                    <p class="line-clamp-2 text-sm font-medium text-gray-900">{{ $gift->product->name }}</p>
                                    <p class="text-sm font-semibold text-primary-600">{{ $gift->points_cost }} pts</p>

                                    <form method="POST" action="{{ route('account.loyalty.redeemGift', $gift) }}" class="mt-auto">
                                        @csrf
                                        <button
                                            type="submit"
                                            @disabled($balance < $gift->points_cost || $gift->product->stock <= 0)
                                            onclick="return confirm('Échanger {{ $gift->points_cost }} points contre ce cadeau ?');"
                                            class="w-full rounded-full bg-primary-600 py-2 text-xs font-semibold text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400"
                                        >
                                            @if ($gift->product->stock <= 0)
                                                Rupture de stock
                                            @elseif ($balance < $gift->points_cost)
                                                Points insuffisants
                                            @else
                                                Échanger
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-10">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Derniers mouvements</h2>
                    <a href="{{ route('account.loyalty.history') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Tout voir</a>
                </div>

                @if ($recentMovements->isEmpty())
                    <x-empty-state
                        title="Aucun mouvement"
                        description="Vos points gagnés et utilisés apparaîtront ici."
                        icon="gift"
                    />
                @else
                    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($recentMovements as $movement)
                                <li class="flex items-center justify-between gap-3 p-4">
                                    <div>
                                        <x-loyalty-movement-badge :type="$movement->type" />
                                        <p class="mt-1 text-xs text-gray-400">{{ $movement->created_at->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="font-semibold {{ $movement->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->points >= 0 ? '+' : '' }}{{ $movement->points }} pts
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-web-layout>

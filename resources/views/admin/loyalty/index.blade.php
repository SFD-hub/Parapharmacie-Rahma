<x-admin-layout title="Fidélité">
    <x-ds.tabs :items="['gifts' => 'Cadeaux', 'history' => 'Historique', 'settings' => 'Paramètres']" :active="$activeTab">
        <x-ds.tab name="gifts">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" class="flex w-full max-w-sm items-center gap-2">
                    <input type="hidden" name="tab" value="gifts">
                    <div class="relative flex-1">
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Rechercher un produit..."
                            class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    </div>
                    @if (request()->filled('search'))
                        <a href="{{ route('admin.loyalty.index', ['tab' => 'gifts']) }}" class="text-sm text-gray-500 hover:text-gray-700">Effacer</a>
                    @endif
                </form>

                <x-ds.button href="{{ route('admin.loyalty.gifts.create') }}" size="md">
                    <x-icon name="plus" class="h-4 w-4" />
                    Nouveau cadeau
                </x-ds.button>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Produit</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Coût en points</th>
                                <th class="px-4 py-3">Statut</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($gifts as $gift)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $gift->product->name }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $gift->product->stock }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $gift->points_cost }} pts</td>
                                    <td class="px-4 py-3">
                                        <form method="POST" action="{{ route('admin.loyalty.gifts.toggle', $gift) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit">
                                                <x-admin.status-badge :active="$gift->is_active" />
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.loyalty.gifts.edit', $gift) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                                <x-icon name="pencil" class="h-4 w-4" />
                                            </a>
                                            <x-admin.delete-button :action="route('admin.loyalty.gifts.destroy', $gift)" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <x-empty-state
                                            title="Aucun cadeau"
                                            description="Associez un produit du catalogue pour créer votre premier cadeau."
                                            icon="gift"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-pagination :paginator="$gifts" />
        </x-ds.tab>

        <x-ds.tab name="history">
            <form method="GET" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 flex-wrap items-center gap-2">
                    <input type="hidden" name="tab" value="history">

                    <div class="relative w-full sm:w-56">
                        <input
                            type="search"
                            name="user"
                            value="{{ request('user') }}"
                            placeholder="Client (nom, email)..."
                            class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500"
                        />
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                    </div>

                    <input
                        type="search"
                        name="order"
                        value="{{ request('order') }}"
                        placeholder="N° commande..."
                        class="w-full rounded-lg border-gray-200 py-2 px-3 text-sm focus:border-primary-500 focus:ring-primary-500 sm:w-40"
                    />

                    <select name="type" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Tous types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <span class="text-sm text-gray-400">→</span>
                    <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">

                    <x-ds.button type="submit" variant="secondary" size="md">Filtrer</x-ds.button>

                    @if (request()->anyFilled(['user', 'order', 'type', 'from', 'to']))
                        <a href="{{ route('admin.loyalty.index', ['tab' => 'history']) }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
                    @endif
                </div>
            </form>

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Client</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Points</th>
                                <th class="px-4 py-3">Commande</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900">{{ $movement->user->name }}</span>
                                        <span class="block text-xs text-gray-400">{{ $movement->user->email }}</span>
                                    </td>
                                    <td class="px-4 py-3"><x-loyalty-movement-badge :type="$movement->type" /></td>
                                    <td class="px-4 py-3 font-semibold {{ $movement->points >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $movement->points >= 0 ? '+' : '' }}{{ $movement->points }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->order?->order_number ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->description ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $movement->created_at->translatedFormat('d M Y à H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <x-empty-state
                                            title="Aucun mouvement"
                                            description="Ajustez vos filtres ou attendez les premiers points gagnés."
                                            icon="gift"
                                        />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-pagination :paginator="$movements" />
        </x-ds.tab>

        <x-ds.tab name="settings">
            <form method="POST" action="{{ route('admin.loyalty.settings.update') }}" class="max-w-2xl">
                @csrf
                @method('PUT')

                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            type="checkbox"
                            name="enabled"
                            value="1"
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            @checked(old('enabled', $settings['enabled']))
                        >
                        Programme de fidélité activé
                    </label>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="amount_per_point" value="Montant nécessaire pour gagner des points (FCFA)" />
                            <x-text-input id="amount_per_point" type="number" step="1" name="amount_per_point" class="mt-1 w-full" value="{{ old('amount_per_point', $settings['amount_per_point']) }}" required />
                            <x-input-error :messages="$errors->get('amount_per_point')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="points_per_amount" value="Nombre de points attribués" />
                            <x-text-input id="points_per_amount" type="number" step="1" name="points_per_amount" class="mt-1 w-full" value="{{ old('points_per_amount', $settings['points_per_amount']) }}" required />
                            <x-input-error :messages="$errors->get('points_per_amount')" class="mt-2" />
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">
                        Exemple : « {{ $settings['points_per_amount'] }} point(s) pour chaque {{ number_format($settings['amount_per_point'], 0, ',', ' ') }} FCFA dépensés ».
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="point_value" value="Valeur monétaire d'un point (FCFA)" />
                            <x-text-input id="point_value" type="number" step="0.01" name="point_value" class="mt-1 w-full" value="{{ old('point_value', $settings['point_value']) }}" required />
                            <x-input-error :messages="$errors->get('point_value')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="max_usage_percentage" value="Utilisation maximale sur une commande (%)" />
                            <x-text-input id="max_usage_percentage" type="number" step="1" name="max_usage_percentage" class="mt-1 w-full" value="{{ old('max_usage_percentage', $settings['max_usage_percentage']) }}" required />
                            <x-input-error :messages="$errors->get('max_usage_percentage')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="mode" value="Mode de fonctionnement" />
                        <select id="mode" name="mode" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                            @foreach ($modes as $mode)
                                <option value="{{ $mode->value }}" @selected(old('mode', $settings['mode']->value) === $mode->value)>{{ $mode->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('mode')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-ds.button type="submit">Enregistrer</x-ds.button>
                </div>
            </form>
        </x-ds.tab>
    </x-ds.tabs>
</x-admin-layout>

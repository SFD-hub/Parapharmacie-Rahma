@props(['brandsList'])

<div class="space-y-6">
    <div class="border-b border-gray-100 pb-6">
        <p class="mb-3 text-sm font-semibold text-gray-900">Marques</p>
        <div class="max-h-48 space-y-2.5 overflow-y-auto pr-1">
            @foreach ($brandsList as $brand)
                <label class="flex items-center gap-2.5 text-sm text-gray-600">
                    <input type="checkbox" wire:model.live="brands" value="{{ $brand['slug'] }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    {{ $brand['name'] }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="border-b border-gray-100 pb-6">
        <p class="mb-3 text-sm font-semibold text-gray-900">Prix (FCFA)</p>
        <div class="flex items-center gap-2">
            <input
                type="number"
                wire:model.live.debounce.500ms="minPrice"
                placeholder="Min"
                class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
            />
            <span class="text-gray-400">–</span>
            <input
                type="number"
                wire:model.live.debounce.500ms="maxPrice"
                placeholder="Max"
                class="w-full rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
            />
        </div>
    </div>

    <div class="space-y-2.5">
        <p class="mb-1 text-sm font-semibold text-gray-900">Autres critères</p>
        <label class="flex items-center gap-2.5 text-sm text-gray-600">
            <input type="checkbox" wire:model.live="onSale" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Promotions
        </label>
        <label class="flex items-center gap-2.5 text-sm text-gray-600">
            <input type="checkbox" wire:model.live="isNew" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Nouveautés
        </label>
        <label class="flex items-center gap-2.5 text-sm text-gray-600">
            <input type="checkbox" wire:model.live="bestSeller" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Meilleures ventes
        </label>
        <label class="flex items-center gap-2.5 text-sm text-gray-600">
            <input type="checkbox" wire:model.live="inStock" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            En stock uniquement
        </label>
    </div>

    <button type="button" wire:click="resetFilters" class="text-sm font-medium text-primary-600 hover:text-primary-700">
        Réinitialiser les filtres
    </button>
</div>

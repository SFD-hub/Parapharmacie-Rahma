<div class="relative">
    <input
        type="search"
        wire:model.live.debounce.400ms="search"
        placeholder="Rechercher un produit, une marque, une catégorie..."
        class="w-full rounded-full border-gray-200 bg-gray-50 py-3 pl-5 pr-12 text-sm focus:border-primary-500 focus:ring-primary-500"
    />
    <span class="pointer-events-none absolute inset-y-0 right-0 flex w-12 items-center justify-center text-gray-400">
        <x-icon name="search" class="h-4 w-4" />
    </span>
</div>

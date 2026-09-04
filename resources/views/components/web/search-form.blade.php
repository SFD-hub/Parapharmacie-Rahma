@props(['id' => 'search'])

<form action="{{ route('shop.index') }}" method="GET" {{ $attributes }}>
    <label for="{{ $id }}" class="sr-only">Rechercher</label>
    <div class="relative w-full">
        <input
            id="{{ $id }}"
            type="search"
            name="q"
            placeholder="Rechercher un produit, une marque..."
            class="w-full rounded-full border-primary-300 bg-gray-50 py-2.5 pl-4 pr-12 text-sm focus:border-primary-500 focus:ring-primary-500"
        />
        <button type="submit" class="absolute inset-y-0 right-0 m-1 flex w-10 items-center justify-center rounded-full bg-primary-600 text-white">
            <span class="sr-only">Rechercher</span>
            <x-icon name="search" class="h-4 w-4" />
        </button>
    </div>
</form>

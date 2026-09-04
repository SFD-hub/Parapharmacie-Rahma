<x-admin-layout title="Produits">
    <form method="GET" class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative w-full sm:w-64">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Nom ou SKU..."
                    class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500"
                />
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            </div>

            <select name="category" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Toutes catégories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <select name="brand" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Toutes marques</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>

            <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tous statuts</option>
                <option value="active" @selected(request('status') === 'active')>Actif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactif</option>
            </select>

            <select name="stock" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Tout stock</option>
                <option value="low" @selected(request('stock') === 'low')>Stock faible</option>
                <option value="out" @selected(request('stock') === 'out')>Rupture</option>
            </select>

            <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Filtrer
            </button>

            @if (request()->anyFilled(['search', 'category', 'brand', 'status', 'stock']))
                <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
            @endif
        </div>

        <a
            href="{{ route('admin.products.create') }}"
            class="inline-flex shrink-0 items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouveau produit
        </a>
    </form>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white">
            <x-empty-state
                title="Aucun produit"
                description="Ajustez vos filtres ou créez votre premier produit."
                icon="archive"
            />
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $product)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                    <div class="relative flex aspect-square items-center justify-center overflow-hidden bg-gray-50">
                        @if ($product->images->first())
                            <img src="{{ $product->images->first()->path }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="archive" class="h-8 w-8 text-gray-300" />
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ number_format($product->displayPrice(), 0, ',', ' ') }} FCFA</p>
                        <div class="mt-2 flex items-center justify-center gap-2">
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <a href="{{ route('admin.stock.movements.create', ['product_id' => $product->id]) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Ajouter du stock">
                                <x-icon name="plus" class="h-4 w-4" />
                            </a>
                            <x-admin.delete-button :action="route('admin.products.destroy', $product)" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$products" />
</x-admin-layout>

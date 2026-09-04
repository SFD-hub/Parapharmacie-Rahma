<x-admin-layout :title="'Produits complémentaires - '.$product->name">
    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
        <x-icon name="chevron-left" class="h-4 w-4" />
        {{ $product->name }}
    </a>

    <h1 class="mt-4 text-lg font-semibold text-gray-900">Produits complémentaires</h1>
    <p class="mt-1 text-sm text-gray-500">Ces suggestions apparaîtront automatiquement sur la fiche de « {{ $product->name }} ».</p>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 lg:col-span-2">
            <h2 class="text-sm font-semibold text-gray-900">Associés actuellement</h2>

            @if ($product->complements->isEmpty())
                <x-empty-state
                    title="Aucun produit complémentaire"
                    description="Ajoutez une association depuis le formulaire ci-contre."
                    icon="tag"
                />
            @else
                <ul class="mt-4 divide-y divide-gray-100 text-sm">
                    @foreach ($product->complements as $complement)
                        <li class="flex items-center justify-between py-3">
                            <span class="font-medium text-gray-900">{{ $complement->name }}</span>
                            <form method="POST" action="{{ route('admin.products.complements.destroy', [$product, $complement]) }}" onsubmit="return confirm('Retirer cette association ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Retirer</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-900">Ajouter une association</h2>
            <form method="POST" action="{{ route('admin.products.complements.store', $product) }}" class="mt-4">
                @csrf
                <select name="complementary_product_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach ($availableProducts as $available)
                        <option value="{{ $available->id }}">{{ $available->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('complementary_product_id')" class="mt-2" />

                <x-primary-button class="mt-4">Ajouter</x-primary-button>
            </form>
        </div>
    </div>
</x-admin-layout>

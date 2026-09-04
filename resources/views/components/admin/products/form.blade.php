@props(['product' => null, 'categories', 'brands'])

<div class="space-y-6">
    <x-ds.card>
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input id="name" name="name" class="mt-1 w-full" value="{{ old('name', $product?->name) }}" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="short_description" value="Résumé du produit" />
            <textarea
                id="short_description"
                name="short_description"
                rows="2"
                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >{{ old('short_description', $product?->short_description) }}</textarea>
            <x-input-error :messages="$errors->get('short_description')" class="mt-2" />
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="category_id" value="Catégorie" />
                <select id="category_id" name="category_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Sélectionner...</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="brand_id" value="Marque" />
                <select id="brand_id" name="brand_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
                    <option value="">Sélectionner...</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
            </div>
        </div>
    </x-ds.card>

    <x-ds.card>
        <x-input-label value="Description complète" />
        <div class="mt-1">
            <x-admin.rich-text-editor name="description" :value="$product?->description" />
        </div>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </x-ds.card>

    <x-ds.card>
        <div>
            <x-input-label for="ingredients" value="Ingrédients" />
            <textarea
                id="ingredients"
                name="ingredients"
                rows="4"
                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >{{ old('ingredients', $product?->ingredients) }}</textarea>
            <x-input-error :messages="$errors->get('ingredients')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="usage_instructions" value="Conseils d'utilisation" />
            <textarea
                id="usage_instructions"
                name="usage_instructions"
                rows="4"
                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >{{ old('usage_instructions', $product?->usage_instructions) }}</textarea>
            <x-input-error :messages="$errors->get('usage_instructions')" class="mt-2" />
        </div>
    </x-ds.card>

    <x-ds.card>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="price" value="Prix (FCFA)" />
                <x-text-input id="price" type="number" step="1" min="0" name="price" class="mt-1 w-full" value="{{ old('price', $product?->price) }}" required />
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="sale_price" value="Prix promotionnel (optionnel)" />
                <x-text-input id="sale_price" type="number" step="1" min="0" name="sale_price" class="mt-1 w-full" value="{{ old('sale_price', $product?->sale_price) }}" />
                <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="stock" value="Stock" />
                <x-text-input id="stock" type="number" min="0" name="stock" class="mt-1 w-full" value="{{ old('stock', $product?->stock ?? 0) }}" required />
                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="expiry_date" value="Date de péremption (optionnel)" />
                <x-text-input id="expiry_date" type="date" name="expiry_date" class="mt-1 w-full" value="{{ old('expiry_date', $product?->expiry_date?->format('Y-m-d')) }}" />
                <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
            </div>
        </div>
    </x-ds.card>

    <x-ds.card>
        <div class="space-y-3">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" @checked(old('is_active', $product?->is_active ?? true))>
                Afficher ce produit sur le site
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" @checked(old('is_featured', $product?->is_featured ?? false))>
                Produit vedette
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_new" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" @checked(old('is_new', $product?->is_new ?? false))>
                Nouveauté
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_loyalty_featured" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" @checked(old('is_loyalty_featured', $product?->is_loyalty_featured ?? false))>
                Mis en avant dans "Points de fidélité"
            </label>
            @if ($product?->is_best_seller)
                <p class="flex items-center gap-2 text-sm text-gray-500">
                    <x-icon name="sparkles" class="h-4 w-4 text-amber-500" />
                    Meilleure vente <span class="text-xs text-gray-400">(calculé automatiquement à partir des ventes)</span>
                </p>
            @endif
        </div>
    </x-ds.card>

    <details class="group rounded-2xl border border-gray-100 bg-white p-5">
        <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-gray-500">
            Réglages avancés
            <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
        </summary>

        <div class="mt-4">
            <x-input-label for="meta_title" value="Titre SEO" />
            <x-text-input id="meta_title" name="meta_title" class="mt-1 w-full" value="{{ old('meta_title', $product?->meta_title) }}" />
            <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="meta_description" value="Description SEO" />
            <textarea
                id="meta_description"
                name="meta_description"
                rows="2"
                class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >{{ old('meta_description', $product?->meta_description) }}</textarea>
            <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
        </div>
    </details>

    <div class="flex items-center gap-3">
        <x-ds.button type="submit">{{ $product ? 'Mettre à jour' : 'Créer le produit' }}</x-ds.button>
        <x-ds.button variant="ghost" href="{{ route('admin.products.index') }}">Annuler</x-ds.button>
    </div>
</div>

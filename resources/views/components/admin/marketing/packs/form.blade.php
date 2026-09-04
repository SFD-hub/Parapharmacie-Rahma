@props(['pack' => null, 'products', 'badges'])

@php
    $itemsData = old('items', $pack?->items->map(fn ($i) => [
        'product_id' => (string) $i->product_id,
        'name' => $i->product->name,
        'price' => (float) $i->product->displayPrice(),
        'quantity' => $i->quantity,
    ])->values()->all() ?? []);
    $productsData = $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->displayPrice()])->values()->all();
@endphp

<div
    x-data="{
        products: {{ \Illuminate\Support\Js::from($productsData) }},
        items: {{ \Illuminate\Support\Js::from($itemsData) }},
        search: '',
        salePrice: {{ (float) old('sale_price', $pack?->sale_price ?? 0) }},
        get filteredProducts() {
            if (! this.search) return [];
            const term = this.search.toLowerCase();
            return this.products
                .filter((p) => p.name.toLowerCase().includes(term) && ! this.items.some((i) => i.product_id == p.id))
                .slice(0, 6);
        },
        addProduct(product) {
            this.items.push({ product_id: product.id, name: product.name, price: product.price, quantity: 1 });
            this.search = '';
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        get basePrice() {
            return this.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
        },
        get savings() {
            return Math.max(0, this.basePrice - (this.salePrice || 0));
        },
    }"
>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-ds.card>
                <div>
                    <x-input-label for="name" value="Nom" />
                    <x-text-input id="name" name="name" class="mt-1 w-full" value="{{ old('name', $pack?->name) }}" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-5">
                    <x-input-label for="description" value="Description" />
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    >{{ old('description', $pack?->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </x-ds.card>

            <x-ds.card>
                <h2 class="text-sm font-semibold text-gray-900">Produits inclus</h2>

                <div class="relative mt-3">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Rechercher un produit à ajouter..."
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                    <div
                        x-show="filteredProducts.length > 0"
                        x-cloak
                        class="absolute z-10 mt-1 w-full overflow-hidden rounded-lg border border-gray-100 bg-white shadow-lg"
                    >
                        <template x-for="product in filteredProducts" :key="product.id">
                            <button
                                type="button"
                                @click="addProduct(product)"
                                class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-blush-50"
                            >
                                <span x-text="product.name"></span>
                                <span class="text-xs text-gray-400" x-text="product.price.toLocaleString('fr-FR') + ' FCFA'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="mt-4 space-y-2">
                    <template x-for="(item, index) in items" :key="item.product_id">
                        <div class="flex items-center gap-2 rounded-lg border border-gray-100 p-2.5">
                            <span class="flex-1 truncate text-sm text-gray-900" x-text="item.name"></span>
                            <input
                                type="number"
                                x-model.number="item.quantity"
                                min="1"
                                class="w-16 rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                            >
                            <span class="w-24 shrink-0 text-right text-xs text-gray-400" x-text="(item.price * item.quantity).toLocaleString('fr-FR') + ' FCFA'"></span>
                            <button type="button" @click="removeItem(index)" class="p-1.5 text-gray-400 hover:text-danger-600" aria-label="Retirer">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                            <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                        </div>
                    </template>

                    <p x-show="items.length === 0" class="text-sm text-gray-400">Aucun produit ajouté pour le moment.</p>
                </div>

                <x-input-error :messages="$errors->get('items')" class="mt-2" />
            </x-ds.card>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-primary-100 bg-blush-50 p-5">
                <p class="text-sm font-semibold text-gray-900">Aperçu du pack</p>
                <div class="mt-3 space-y-1.5 text-sm">
                    <div class="flex items-center justify-between text-gray-500">
                        <span>Valeur réelle</span>
                        <span x-text="basePrice.toLocaleString('fr-FR') + ' FCFA'"></span>
                    </div>
                    <div class="flex items-center justify-between font-semibold text-gray-900">
                        <span>Prix du pack</span>
                        <span x-text="(salePrice || 0).toLocaleString('fr-FR') + ' FCFA'"></span>
                    </div>
                    <div class="flex items-center justify-between font-semibold text-success-600">
                        <span>Économie client</span>
                        <span x-text="savings.toLocaleString('fr-FR') + ' FCFA'"></span>
                    </div>
                </div>
            </div>

            <x-ds.card>
                <div>
                    <x-input-label for="sale_price" value="Prix promotionnel du pack (FCFA)" />
                    <x-text-input id="sale_price" type="number" step="0.01" name="sale_price" class="mt-1 w-full" x-model.number="salePrice" required />
                    <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
                </div>

                <label class="mt-5 flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        @checked(old('is_active', $pack?->is_active ?? true))
                    >
                    Afficher ce pack sur le site
                </label>
            </x-ds.card>

            <x-ds.card>
                <p class="text-sm font-semibold text-gray-900">Marketing</p>

                <div class="mt-4">
                    <x-input-label for="badge" value="Badge" />
                    <select id="badge" name="badge" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Aucun</option>
                        @foreach ($badges as $badge)
                            <option value="{{ $badge->value }}" @selected(old('badge', $pack?->badge?->value) === $badge->value)>{{ $badge->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('badge')" class="mt-2" />
                </div>

                <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="is_featured_home"
                        value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        @checked(old('is_featured_home', $pack?->is_featured_home ?? false))
                    >
                    Mettre en avant sur la page d'accueil
                </label>

                <label class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="is_featured_recommendations"
                        value="1"
                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        @checked(old('is_featured_recommendations', $pack?->is_featured_recommendations ?? false))
                    >
                    Mettre en avant dans les recommandations
                </label>

                <div class="mt-4">
                    <x-input-label for="loyalty_bonus_points" value="Bonus de points fidélité" />
                    <x-text-input id="loyalty_bonus_points" type="number" name="loyalty_bonus_points" class="mt-1 w-full" value="{{ old('loyalty_bonus_points', $pack?->loyalty_bonus_points ?? 0) }}" />
                    <x-input-error :messages="$errors->get('loyalty_bonus_points')" class="mt-2" />
                </div>
            </x-ds.card>

            <details class="group rounded-2xl border border-gray-100 bg-white p-5">
                <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-gray-500">
                    Disponibilité (optionnel)
                    <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
                </summary>

                <div class="mt-4">
                    <x-input-label for="starts_at" value="Date de début" />
                    <x-text-input id="starts_at" type="datetime-local" name="starts_at" class="mt-1 w-full" value="{{ old('starts_at', $pack?->starts_at?->format('Y-m-d\TH:i')) }}" />
                    <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="ends_at" value="Date de fin" />
                    <x-text-input id="ends_at" type="datetime-local" name="ends_at" class="mt-1 w-full" value="{{ old('ends_at', $pack?->ends_at?->format('Y-m-d\TH:i')) }}" />
                    <x-input-error :messages="$errors->get('ends_at')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="max_sales" value="Limite de ventes (optionnel)" />
                    <x-text-input id="max_sales" type="number" name="max_sales" class="mt-1 w-full" value="{{ old('max_sales', $pack?->max_sales) }}" />
                    @if ($pack)
                        <p class="mt-1 text-xs text-gray-400">{{ $pack->sales_count }} vente(s) enregistrée(s).</p>
                    @endif
                    <x-input-error :messages="$errors->get('max_sales')" class="mt-2" />
                </div>
            </details>

            <x-ds.card>
                <x-admin.image-upload name="image" label="Ajouter une image" :current="$pack?->image" />
            </x-ds.card>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <x-ds.button type="submit">{{ $pack ? 'Mettre à jour' : 'Créer le pack' }}</x-ds.button>
        <x-ds.button variant="ghost" href="{{ route('admin.marketing.packs.index') }}">Annuler</x-ds.button>
    </div>
</div>

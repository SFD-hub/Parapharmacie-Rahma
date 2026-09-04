@props(['gift' => null, 'products'])

<div class="max-w-xl rounded-2xl border border-gray-100 bg-white p-5">
    <div>
        <x-input-label for="product_id" value="Produit" />
        <select id="product_id" name="product_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
            <option value="">Sélectionner un produit</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $gift?->product_id) == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="points_cost" value="Coût en points" />
        <x-text-input id="points_cost" type="number" name="points_cost" class="mt-1 w-full" value="{{ old('points_cost', $gift?->points_cost) }}" required />
        <x-input-error :messages="$errors->get('points_cost')" class="mt-2" />
    </div>

    <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
            @checked(old('is_active', $gift?->is_active ?? true))
        >
        Cadeau actif
    </label>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $gift ? 'Mettre à jour' : 'Créer le cadeau' }}</x-primary-button>
    <a href="{{ route('admin.loyalty.index', ['tab' => 'gifts']) }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
</div>

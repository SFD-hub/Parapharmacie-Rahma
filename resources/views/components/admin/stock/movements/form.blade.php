@props(['products', 'types'])

<div class="max-w-xl rounded-2xl border border-gray-100 bg-white p-5">
    <div>
        <x-input-label for="product_id" value="Produit" />
        <select id="product_id" name="product_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
            <option value="">Sélectionner un produit</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', request('product_id')) == $product->id)>{{ $product->name }} (stock actuel : {{ $product->stock }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="type" value="Type de mouvement" />
        <select id="type" name="type" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500" required>
            <option value="">Sélectionner un type</option>
            @foreach ($types as $type)
                <option value="{{ $type->value }}" @selected(old('type', request('product_id') ? 'manual_adjustment' : null) === $type->value)>{{ $type->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="quantity" value="Quantité (positive pour ajouter, négative pour retirer)" />
        <x-text-input id="quantity" type="number" name="quantity" class="mt-1 w-full" value="{{ old('quantity') }}" required />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="comment" value="Commentaire (optionnel)" />
        <textarea
            id="comment"
            name="comment"
            rows="2"
            class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
        >{{ old('comment') }}</textarea>
        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Enregistrer le mouvement</x-primary-button>
    <a href="{{ route('admin.stock.movements.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
</div>

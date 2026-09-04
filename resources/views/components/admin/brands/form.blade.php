@props(['brand' => null])

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <x-ds.card>
            <div>
                <x-input-label for="name" value="Nom" />
                <x-text-input id="name" name="name" class="mt-1 w-full" value="{{ old('name', $brand?->name) }}" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-5">
                <x-input-label for="description" value="Description" />
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                >{{ old('description', $brand?->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
        </x-ds.card>
    </div>

    <div class="space-y-6">
        <x-ds.card>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    @checked(old('is_active', $brand?->is_active ?? true))
                >
                Afficher cette marque sur le site
            </label>
        </x-ds.card>

        <x-ds.card>
            <x-admin.image-upload name="logo" label="Ajouter le logo" :current="$brand?->logo" />
        </x-ds.card>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-ds.button type="submit">{{ $brand ? 'Mettre à jour' : 'Créer la marque' }}</x-ds.button>
    <x-ds.button variant="ghost" href="{{ route('admin.brands.index') }}">Annuler</x-ds.button>
</div>

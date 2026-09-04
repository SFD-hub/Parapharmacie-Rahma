@props(['page' => null])

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-5">
            <div>
                <x-input-label for="title" value="Titre" />
                <x-text-input id="title" name="title" class="mt-1 w-full" value="{{ old('title', $page?->title) }}" required />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="slug" value="Slug (optionnel, généré automatiquement si vide)" />
                <x-text-input id="slug" name="slug" class="mt-1 w-full" value="{{ old('slug', $page?->slug) }}" />
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="content" value="Contenu" />
                <textarea
                    id="content"
                    name="content"
                    rows="14"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    required
                >{{ old('content', $page?->content) }}</textarea>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-5">
            <p class="text-sm font-semibold text-gray-900">Référencement (SEO)</p>

            <div class="mt-4">
                <x-input-label for="meta_title" value="Titre SEO" />
                <x-text-input id="meta_title" name="meta_title" class="mt-1 w-full" value="{{ old('meta_title', $page?->meta_title) }}" />
                <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="meta_description" value="Description SEO" />
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="2"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                >{{ old('meta_description', $page?->meta_description) }}</textarea>
                <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-100 bg-white p-5">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    @checked(old('is_active', $page?->is_active ?? true))
                >
                Page publiée
            </label>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $page ? 'Mettre à jour' : 'Créer la page' }}</x-primary-button>
    <a href="{{ route('admin.pages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuler</a>
</div>

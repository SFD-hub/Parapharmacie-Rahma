@props(['article' => null, 'categories'])

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <x-ds.card>
            <div>
                <x-input-label for="title" value="Titre" />
                <x-text-input id="title" name="title" class="mt-1 w-full" value="{{ old('title', $article?->title) }}" required autofocus />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-5">
                <x-input-label for="excerpt" value="Résumé de l'article" />
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="2"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                >{{ old('excerpt', $article?->excerpt) }}</textarea>
                <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
            </div>

            <div class="mt-5">
                <x-input-label value="Contenu" />
                <div class="mt-1">
                    <x-admin.rich-text-editor name="content" :value="$article?->content" />
                </div>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>
        </x-ds.card>

        <details class="group rounded-2xl border border-gray-100 bg-white p-5">
            <summary class="flex cursor-pointer items-center justify-between text-sm font-semibold text-gray-500">
                Réglages avancés
                <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
            </summary>

            <div class="mt-4">
                <x-input-label for="meta_title" value="Titre SEO" />
                <x-text-input id="meta_title" name="meta_title" class="mt-1 w-full" value="{{ old('meta_title', $article?->meta_title) }}" />
                <x-input-error :messages="$errors->get('meta_title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="meta_description" value="Description SEO" />
                <textarea
                    id="meta_description"
                    name="meta_description"
                    rows="2"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                >{{ old('meta_description', $article?->meta_description) }}</textarea>
                <x-input-error :messages="$errors->get('meta_description')" class="mt-2" />
            </div>
        </details>
    </div>

    <div class="space-y-6">
        <x-ds.card>
            <x-input-label for="article_category_id" value="Catégorie" />
            <select id="article_category_id" name="article_category_id" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="">Aucune</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('article_category_id', $article?->article_category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('article_category_id')" class="mt-2" />

            <div class="mt-2">
                <x-text-input
                    id="new_category_name"
                    name="new_category_name"
                    class="mt-1 w-full"
                    placeholder="Ou créer une nouvelle catégorie..."
                    value="{{ old('new_category_name') }}"
                />
                <p class="mt-1 text-xs text-gray-400">Si rempli, une nouvelle catégorie sera créée et remplacera le choix ci-dessus.</p>
                <x-input-error :messages="$errors->get('new_category_name')" class="mt-2" />
            </div>

            <div class="mt-5">
                <x-input-label for="published_at" value="Date de publication" />
                <x-text-input
                    id="published_at"
                    type="datetime-local"
                    name="published_at"
                    class="mt-1 w-full"
                    value="{{ old('published_at', $article?->published_at?->format('Y-m-d\TH:i')) }}"
                />
                <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
            </div>

            <label class="mt-5 flex items-center gap-2 text-sm text-gray-700">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    @checked(old('is_published', $article?->is_published ?? false))
                >
                Publié
            </label>
        </x-ds.card>

        <x-ds.card>
            <x-admin.image-upload name="cover_image" label="Image mise en avant" :current="$article?->cover_image" />
        </x-ds.card>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-ds.button type="submit">{{ $article ? 'Mettre à jour' : "Créer l'article" }}</x-ds.button>
    <x-ds.button variant="ghost" href="{{ route('admin.articles.index') }}">Annuler</x-ds.button>
</div>

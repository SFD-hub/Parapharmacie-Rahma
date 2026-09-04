<x-admin-layout title="Articles">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-admin.search-bar placeholder="Rechercher un article..." />
        <a
            href="{{ route('admin.articles.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouvel article
        </a>
    </div>

    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="rounded-full px-3 py-1.5 text-xs font-medium {{ ! request()->filled('status') ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600' }}">Tous</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'published']) }}" class="rounded-full px-3 py-1.5 text-xs font-medium {{ request('status') === 'published' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600' }}">Publiés</a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}" class="rounded-full px-3 py-1.5 text-xs font-medium {{ request('status') === 'draft' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600' }}">Brouillons</a>
    </div>

    @if ($articles->isEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white">
            <x-empty-state
                title="Aucun article"
                description="Créez votre premier article pour alimenter le blog."
                icon="newspaper"
            />
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            @foreach ($articles as $article)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                    <div class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-gray-50">
                        @if ($article->cover_image)
                            <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                        @else
                            <x-icon name="newspaper" class="h-8 w-8 text-gray-300" />
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $article->title }}</p>
                        <div class="mt-2 flex items-center justify-center gap-2">
                            <a href="{{ route('admin.articles.edit', $article) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <x-admin.delete-button :action="route('admin.articles.destroy', $article)" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$articles" />
</x-admin-layout>

<x-web-layout title="Conseils beauté" description="Découvrez nos conseils beauté, routines et astuces pour prendre soin de vous." :back="route('home')" :search="false">
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900">
            Conseils <span class="text-primary-600">beauté</span>
        </h1>
        <p class="mt-1 text-sm text-gray-600">Des conseils d'experts pour prendre soin de vous au quotidien.</p>

        <form method="GET" class="mt-6">
            @if (request()->filled('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Rechercher un article, un sujet..."
                    class="w-full rounded-full border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm focus:border-primary-500 focus:ring-primary-500"
                />
                <span class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-gray-400">
                    <x-icon name="search" class="h-4 w-4" />
                </span>
            </div>
        </form>

        @if ($categories->isNotEmpty())
            <div class="mt-4 flex gap-2 overflow-x-auto pb-1">
                <a
                    href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                    class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-medium {{ ! request()->filled('category') ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600' }}"
                >
                    Tous
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}"
                        class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-medium {{ request('category') === $category->slug ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($articles->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="Aucun article trouvé"
                    description="Modifiez votre recherche ou revenez plus tard pour découvrir de nouveaux articles."
                    icon="newspaper"
                />
            </div>
        @else
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3">
                @foreach ($articles as $article)
                    <x-web.article-card :article="$article" />
                @endforeach
            </div>

            <x-pagination :paginator="$articles" />
        @endif
    </div>
</x-web-layout>

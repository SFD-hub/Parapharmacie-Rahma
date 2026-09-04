<x-web-layout :title="$article->title" :description="$article->meta_description ?? $article->excerpt">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="mb-4 flex flex-wrap items-center gap-1 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Accueil</a>
            <x-icon name="chevron-right" class="h-3 w-3" />
            <a href="{{ route('blog.index') }}" class="hover:text-primary-600">Conseils beauté</a>
            @if ($article->category)
                <x-icon name="chevron-right" class="h-3 w-3" />
                <a href="{{ route('blog.index', ['category' => $article->category->slug]) }}" class="hover:text-primary-600">
                    {{ $article->category->name }}
                </a>
            @endif
        </nav>

        @if ($article->cover_image)
            <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="aspect-[16/9] w-full rounded-2xl object-cover">
        @endif

        <h1 class="mt-6 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $article->title }}</h1>

        @if ($article->published_at)
            <p class="mt-2 text-xs text-gray-400">{{ $article->published_at->translatedFormat('d M Y') }}</p>
        @endif

        <div class="rich-text mt-6 text-sm leading-relaxed text-gray-700">
            {!! $article->content !!}
        </div>

        @if ($article->products->isNotEmpty())
            <div class="mt-10 border-t border-gray-100 pt-8">
                <h2 class="text-base font-bold text-gray-900">Produits recommandés pour ce problème</h2>
                <div class="mt-4">
                    <x-web.product-grid :products="$article->products" :columns="3" />
                </div>
            </div>
        @endif
    </div>

    @if ($similar->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h2 class="mb-4 text-lg font-bold text-gray-900 sm:text-xl">Articles similaires</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($similar as $item)
                    <x-web.article-card :article="$item" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($recent->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <h2 class="mb-4 text-lg font-bold text-gray-900 sm:text-xl">Articles récents</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach ($recent as $item)
                    <x-web.article-card :article="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-web-layout>

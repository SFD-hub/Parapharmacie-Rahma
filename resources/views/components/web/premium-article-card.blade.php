@props(['article'])

<div {{ $attributes->class(['group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-lg']) }}>
    <a href="{{ route('blog.show', $article) }}" class="relative block aspect-[4/3] overflow-hidden bg-gray-100">
        @if ($article->category)
            <div class="absolute left-3 top-3 z-10">
                <x-ds.badge color="primary" variant="solid">{{ $article->category->name }}</x-ds.badge>
            </div>
        @endif

        @if ($article->cover_image)
            <img src="{{ $article->cover_image }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <x-icon name="sparkles" class="h-10 w-10" />
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-2 p-5">
        <a href="{{ route('blog.show', $article) }}" class="line-clamp-2 text-base font-semibold text-gray-900 hover:text-primary-600">
            {{ $article->title }}
        </a>

        @if ($article->excerpt)
            <p class="line-clamp-2 text-sm text-gray-500">{{ $article->excerpt }}</p>
        @endif

        <a href="{{ route('blog.show', $article) }}" class="mt-auto inline-flex items-center gap-1.5 pt-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
            Lire l'article
            <x-icon name="arrow-right" class="h-4 w-4" />
        </a>
    </div>
</div>

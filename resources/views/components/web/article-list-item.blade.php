@props(['article'])

<a href="{{ route('blog.show', $article) }}" class="group flex gap-4 rounded-2xl border border-gray-100 bg-white p-3">
    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100">
        @if ($article->cover_image)
            <img
                src="{{ $article->cover_image }}"
                alt="{{ $article->title }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
            >
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <x-icon name="sparkles" class="h-8 w-8" />
            </div>
        @endif
    </div>

    <div class="min-w-0 flex-1 py-0.5">
        @if ($article->category)
            <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-600">{{ $article->category->name }}</p>
        @endif

        <p class="mt-0.5 line-clamp-2 text-sm font-bold text-gray-900 group-hover:text-primary-600">
            {{ $article->title }}
        </p>

        @if ($article->excerpt)
            <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $article->excerpt }}</p>
        @endif

        <p class="mt-2 text-xs text-gray-400">
            {{ $article->readingTime() }} min de lecture
            @if ($article->published_at)
                · {{ $article->published_at->translatedFormat('d M Y') }}
            @endif
        </p>
    </div>

    <div class="flex shrink-0 items-center">
        <x-icon name="chevron-right" class="h-4 w-4 text-gray-300" />
    </div>
</a>

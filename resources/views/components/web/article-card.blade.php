@props(['article'])

<a href="{{ route('blog.show', $article) }}" class="group block">
    <div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-gray-100">
        @if ($article->category)
            <div class="absolute left-2 top-2 z-10">
                <x-ds.badge color="primary" variant="solid">{{ $article->category->name }}</x-ds.badge>
            </div>
        @endif

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
    <p class="mt-2 line-clamp-2 text-sm font-semibold text-gray-900 group-hover:text-primary-600">
        {{ $article->title }}
    </p>
    @if ($article->published_at)
        <p class="mt-1 text-xs text-gray-400">
            {{ $article->published_at->translatedFormat('d M Y') }}
        </p>
    @endif
</a>

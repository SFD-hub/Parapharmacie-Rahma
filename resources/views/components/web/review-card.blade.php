@props(['review'])

<div class="w-72 shrink-0 snap-start rounded-2xl border border-gray-100 bg-white p-4 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
    <x-web.star-rating :rating="$review->rating" />

    @if ($review->comment)
        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $review->comment }}</p>
    @endif

    <div class="mt-4 flex items-center gap-2">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blush-100 text-xs font-semibold text-primary-600">
            {{ Str::of($review->name)->substr(0, 1)->upper() }}
        </span>
        <div class="min-w-0">
            <p class="truncate text-xs font-semibold text-gray-900">{{ $review->name }}</p>
            <p class="text-[11px] text-gray-400">{{ $review->created_at->translatedFormat('d M Y') }}</p>
        </div>
    </div>
</div>

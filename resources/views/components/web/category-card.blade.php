@props(['category'])

<a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="flex flex-col items-center gap-2 text-center">
    <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-blush-100 sm:h-20 sm:w-20">
        @if ($category->image)
            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-full w-full object-cover">
        @else
            <x-icon name="sparkles" class="h-7 w-7 text-primary-600" />
        @endif
    </span>
    <span class="text-xs font-medium text-gray-700 sm:text-sm">{{ $category->name }}</span>
</a>

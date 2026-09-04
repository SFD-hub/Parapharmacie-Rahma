@props(['pack'])

@php
    $image = $pack->image ?? $pack->items->first()?->product->images->first()?->path;
    $productNames = $pack->items->map(fn ($item) => $item->product->name)->take(3);
@endphp

<a href="{{ route('packs.show', $pack) }}" class="group flex flex-col overflow-hidden">
    <div class="relative aspect-square overflow-hidden bg-gray-50">
        @if ($pack->badge)
            <span class="absolute left-2 top-2 z-10 rounded-full px-2 py-1 text-xs font-semibold {{ $pack->badge->color() }}">
                {{ $pack->badge->label() }}
            </span>
        @endif

        @if ($image)
            <img src="{{ $image }}" alt="{{ $pack->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <x-icon name="box" class="h-10 w-10" />
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1 p-3">
        <p class="line-clamp-2 text-sm font-medium text-gray-900">{{ $pack->name }}</p>
        <p class="line-clamp-1 text-xs text-gray-400">{{ $productNames->implode(' + ') }}</p>
        <div class="mt-auto pt-1 text-sm font-bold text-primary-600">
            {{ number_format($pack->sale_price, 0, ',', ' ') }} FCFA
        </div>
    </div>
</a>

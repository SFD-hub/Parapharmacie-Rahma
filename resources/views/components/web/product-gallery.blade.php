@props(['product'])

@php
    $images = $product->images;
    $isExpired = $product->isExpired();
    $outOfStock = $product->stockStatus() === 'out_of_stock';
@endphp

<div x-data="{ active: 0 }" class="flex flex-col gap-3">
    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50">
        <div class="absolute left-3 top-3 z-10">
            @if ($isExpired)
                <x-web.product-badge type="expired" />
            @elseif ($outOfStock)
                <x-web.product-badge type="out-of-stock" />
            @elseif ($product->hasDiscount())
                <x-web.product-badge type="sale" :value="$product->discountPercent()" />
            @elseif ($product->is_best_seller)
                <x-web.product-badge type="best-seller" />
            @elseif ($product->is_new)
                <x-web.product-badge type="new" />
            @endif
        </div>

        @forelse ($images as $index => $image)
            <img
                x-show="active === {{ $index }}"
                src="{{ $image->path }}"
                alt="{{ $product->name }}"
                class="h-full w-full object-cover transition duration-300"
            >
        @empty
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <x-icon name="bag" class="h-16 w-16" />
            </div>
        @endforelse

        @if ($images->count() > 1)
            <div class="absolute inset-x-0 bottom-3 z-10 flex justify-center gap-1.5">
                @foreach ($images as $index => $image)
                    <button
                        type="button"
                        @click="active = {{ $index }}"
                        class="h-1.5 rounded-full transition"
                        :class="active === {{ $index }} ? 'w-5 bg-primary-600' : 'w-1.5 bg-white/80'"
                        aria-label="Image {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</div>

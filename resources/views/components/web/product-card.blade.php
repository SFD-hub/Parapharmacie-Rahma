@props(['product'])

@php
    $image = $product->images->first();
    $isExpired = $product->isExpired();
    $outOfStock = $product->stockStatus() === 'out_of_stock';
    $unavailable = $outOfStock || $isExpired;
@endphp

<div {{ $attributes->class(['group relative flex flex-col overflow-hidden']) }}>
    <div class="relative aspect-square overflow-hidden bg-gray-50">
        <div class="absolute left-2 top-2 z-10">
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

        <a href="{{ route('products.show', $product) }}">
            @if ($image)
                <img
                    src="{{ $image->path }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105 {{ $unavailable ? 'opacity-50' : '' }}"
                    loading="lazy"
                >
            @else
                <div class="flex h-full w-full items-center justify-center text-gray-300">
                    <x-icon name="bag" class="h-10 w-10" />
                </div>
            @endif
        </a>
    </div>

    <div class="flex flex-1 flex-col items-center gap-1 p-3 text-center">
        @if ($product->brand)
            <span class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ $product->brand->name }}</span>
        @endif

        <a href="{{ route('products.show', $product) }}" class="line-clamp-2 text-sm font-medium text-gray-900">
            {{ $product->name }}
        </a>

        <div class="mt-auto pt-1">
            <x-web.product-price :product="$product" />
        </div>

        <form action="{{ route('cart.store') }}" method="POST" class="mt-2 w-full">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button
                type="submit"
                @disabled($unavailable)
                class="flex w-full items-center justify-center gap-1.5 rounded-full border border-primary-600 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-600 hover:text-white disabled:cursor-not-allowed disabled:border-gray-200 disabled:text-gray-400 disabled:hover:bg-transparent"
            >
                @unless ($unavailable)
                    <x-icon name="cart" class="h-3.5 w-3.5" />
                @endunless
                {{ $unavailable ? 'Indisponible' : 'Ajouter au panier' }}
            </button>
        </form>
    </div>
</div>

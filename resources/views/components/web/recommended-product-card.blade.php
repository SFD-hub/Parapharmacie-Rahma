@props(['product'])

@php
    $image = $product->images->first();
    $isExpired = $product->isExpired();
    $outOfStock = $product->stockStatus() === 'out_of_stock';
    $unavailable = $outOfStock || $isExpired;
@endphp

<div class="flex flex-col">
    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50">
        <div class="absolute left-2 top-2 z-10">
            @if ($isExpired)
                <x-web.product-badge type="expired" />
            @elseif ($outOfStock)
                <x-web.product-badge type="out-of-stock" />
            @elseif ($product->hasDiscount())
                <x-web.product-badge type="sale" :value="$product->discountPercent()" />
            @endif
        </div>

        <a href="{{ route('products.show', $product) }}">
            @if ($image)
                <img
                    src="{{ $image->path }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-contain p-6 {{ $unavailable ? 'opacity-50' : '' }}"
                    loading="lazy"
                >
            @else
                <div class="flex h-full w-full items-center justify-center text-gray-300">
                    <x-icon name="bag" class="h-12 w-12" />
                </div>
            @endif
        </a>
    </div>

    <div class="mt-3">
        @if ($product->brand)
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">{{ $product->brand->name }}</p>
        @endif

        <a href="{{ route('products.show', $product) }}" class="mt-0.5 block text-sm font-semibold text-gray-900">
            {{ $product->name }}
        </a>

        @if ($product->short_description)
            <p class="mt-0.5 text-xs text-gray-500">{{ $product->short_description }}</p>
        @endif

        <p class="mt-2 flex items-center gap-1.5 text-[11px] text-primary-600">
            <x-icon name="shield" class="h-3.5 w-3.5 shrink-0" />
            Recommandé par notre pharmacien
        </p>

        <div class="mt-2">
            <x-web.product-price :product="$product" />
        </div>

        <form action="{{ route('cart.store') }}" method="POST" class="mt-2">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button
                type="submit"
                @disabled($unavailable)
                class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 transition hover:text-primary-700 disabled:cursor-not-allowed disabled:text-gray-400"
            >
                <x-icon name="cart" class="h-4 w-4" />
                {{ $unavailable ? 'Indisponible' : 'Ajouter au panier' }}
            </button>
        </form>
    </div>
</div>

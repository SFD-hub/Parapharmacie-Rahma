@props(['pack'])

@php
    $image = $pack->image ?? $pack->items->first()?->product->images->first()?->path;
    $basePrice = (float) $pack->items->sum(fn ($item) => $item->product->displayPrice() * $item->quantity);
    $discountPercent = $basePrice > 0 ? (int) round((($basePrice - $pack->sale_price) / $basePrice) * 100) : 0;

    $shortDescription = match ($pack->id) {
        1 => 'Nettoyer, purifier et apaiser en trois gestes essentiels.',
        2 => 'Nettoyant, protection solaire et soin réparateur en un geste.',
        3 => 'Un duo nettoyant et hydratant pour peaux sèches.',
        4 => 'Sérum matifiant et ampoule hydratante pour peaux à imperfections.',
        5 => 'Spray apaisant et soin réparateur pour peaux sensibles.',
        6 => 'Huile multi-usage et baume lèvres, les incontournables Nuxe.',
        7 => 'Sérum anti-taches et soin hydratant pour un teint éclatant.',
        default => '',
    };
@endphp

<div {{ $attributes->class(['group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white transition duration-300 hover:-translate-y-1 hover:shadow-lg']) }}>
    <a href="{{ route('packs.show', $pack) }}" class="relative block aspect-square overflow-hidden bg-gray-50">
        @if ($discountPercent > 0)
            <span class="absolute left-2 top-2 z-10">
                <x-web.product-badge type="sale" :value="$discountPercent" />
            </span>
        @endif

        @if ($image)
            <img src="{{ $image }}" alt="{{ $pack->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-300">
                <x-icon name="box" class="h-10 w-10" />
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col items-center gap-1 p-4 text-center">
        <a href="{{ route('packs.show', $pack) }}" class="line-clamp-2 text-sm font-semibold text-gray-900 group-hover:text-primary-600">
            {{ $pack->name }}
        </a>

        @if ($shortDescription !== '')
            <p class="line-clamp-2 text-xs text-gray-500">{{ $shortDescription }}</p>
        @endif

        <div class="mt-auto pt-2">
            <span class="text-sm font-bold text-primary-600">{{ number_format($pack->sale_price, 0, ',', ' ') }} FCFA</span>
        </div>

        <form action="{{ route('packs.addToCart', $pack) }}" method="POST" class="mt-2 w-full">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center gap-1.5 rounded-full border border-primary-600 py-1.5 text-xs font-semibold text-primary-600 transition hover:bg-primary-600 hover:text-white"
            >
                <x-icon name="cart" class="h-3.5 w-3.5" />
                Ajouter au panier
            </button>
        </form>
    </div>
</div>

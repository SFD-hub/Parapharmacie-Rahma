<x-web-layout :title="$pack->name" :description="$pack->description">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-8">
        <nav class="mb-4 flex flex-wrap items-center gap-1 text-xs text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Accueil</a>
            <x-icon name="chevron-right" class="h-3 w-3" />
            <a href="{{ route('packs.index') }}" class="hover:text-primary-600">Packs</a>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <div class="aspect-square overflow-hidden rounded-2xl bg-gray-50">
                @php $image = $pack->image ?? $pack->items->first()?->product->images->first()?->path; @endphp
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $pack->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center text-gray-300">
                        <x-icon name="box" class="h-16 w-16" />
                    </div>
                @endif
            </div>

            <div>
                @if ($pack->badge)
                    <span class="inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $pack->badge->color() }}">
                        {{ $pack->badge->label() }}
                    </span>
                @endif

                <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ $pack->name }}</h1>

                @if ($pack->description)
                    <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $pack->description }}</p>
                @endif

                <div class="mt-4 flex items-baseline gap-3">
                    <span class="text-2xl font-bold text-primary-600">{{ number_format($pack->sale_price, 0, ',', ' ') }} FCFA</span>
                    <span class="text-sm text-gray-400 line-through">{{ number_format($basePrice, 0, ',', ' ') }} FCFA</span>
                </div>
                @if ($savings > 0)
                    <p class="mt-1 text-sm font-medium text-emerald-600">
                        Économisez {{ number_format($savings, 0, ',', ' ') }} FCFA en achetant ce pack
                    </p>
                @endif

                @if ($pack->loyalty_bonus_points > 0)
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-gray-500">
                        <x-icon name="gift" class="h-4 w-4 text-primary-600" />
                        +{{ $pack->loyalty_bonus_points }} points de fidélité bonus
                    </p>
                @endif

                <form method="POST" action="{{ route('packs.addToCart', $pack) }}" class="mt-6">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-full bg-primary-600 py-3.5 text-sm font-semibold text-white transition hover:bg-primary-700"
                    >
                        Ajouter le pack au panier
                    </button>
                </form>

                <div class="mt-8 border-t border-gray-100 pt-6">
                    <h2 class="text-sm font-semibold text-gray-900">Ce pack contient</h2>
                    <ul class="mt-3 divide-y divide-gray-100 text-sm">
                        @foreach ($pack->items as $item)
                            <li class="flex items-center gap-3 py-3">
                                <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                                    @if ($item->product->images->first())
                                        <img src="{{ $item->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <a href="{{ route('products.show', $item->product) }}" class="flex-1 font-medium text-gray-900 hover:text-primary-600">
                                    {{ $item->product->name }}
                                </a>
                                <span class="text-gray-500">× {{ $item->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-web-layout>

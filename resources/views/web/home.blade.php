<x-web-layout title="Accueil" description="Votre parapharmacie en ligne à Thiès : soins visage, corps, cheveux et conseils beauté personnalisés.">
    <x-web.hero-slider :slides="$heroSlides" :ratio="$heroSlidesRatio" />

    {{-- Slogan --}}
    <section class="bg-white pt-6 pb-2 sm:pt-8 sm:pb-3">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold leading-snug text-primary-600 sm:text-3xl lg:text-4xl">
                La Parapharmacie Rahma
            </h2>
            <p class="mt-1 text-2xl font-bold leading-snug text-gray-900 sm:text-3xl lg:text-4xl">
                Parapharmacie en ligne &amp; Soins cosmétiques
            </p>
        </div>
    </section>

    @php
        $universes = [
            ['title' => 'Soin visage', 'search' => 'visage', 'icon' => 'heart', 'image' => 'images/univers/soin_visage.jpg'],
            ['title' => 'Corps et bain', 'search' => 'corps', 'icon' => 'shield', 'image' => 'images/univers/corpetbain.jpg'],
            ['title' => 'Sport', 'search' => 'sport', 'icon' => 'sparkles', 'image' => 'images/univers/sport.jpg'],
            ['title' => 'Cheveux', 'search' => 'cheveux', 'icon' => 'sparkles', 'image' => 'images/univers/cheveux.jpg'],
            ['title' => 'Soin bébé', 'search' => 'bébé', 'icon' => 'gift', 'image' => 'images/univers/bebe.jpg'],
            ['title' => 'Bien-être & Compléments', 'search' => 'complément', 'icon' => 'box', 'image' => 'images/univers/complement_alimentaire.jpg'],
        ];
    @endphp

    {{-- Nos univers beauté --}}
    <section class="bg-white pt-2 pb-10 sm:pt-3 sm:pb-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">
                Nos univers beauté
            </h2>

            <div class="mt-4">
                <x-web.carousel>
                    @foreach ($universes as $universe)
                        <a
                            href="{{ route('shop.index', ['search' => $universe['search']]) }}"
                            class="group relative flex aspect-square w-60 shrink-0 snap-start flex-col justify-end overflow-hidden rounded-2xl bg-gray-100 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg sm:w-72"
                        >
                            @if (file_exists(public_path($universe['image'])))
                                <img
                                    src="{{ asset($universe['image']) }}?v={{ filemtime(public_path($universe['image'])) }}"
                                    alt="{{ $universe['title'] }}"
                                    class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    loading="lazy"
                                >
                            @else
                                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-blush-100 to-primary-100 text-primary-400">
                                    <x-icon name="{{ $universe['icon'] }}" class="h-10 w-10" />
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                            <div class="relative p-3 sm:p-4">
                                <p class="text-sm font-semibold text-white sm:text-base">{{ $universe['title'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </x-web.carousel>
            </div>
        </div>
    </section>

    {{-- Nouveautés --}}
    @if ($newProducts->isNotEmpty())
        <section class="bg-gray-50 pt-10 pb-2 sm:pt-14 sm:pb-3">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Nouveautés</h2>
                    <a href="{{ route('shop.index', ['isNew' => 1]) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-primary-600 transition hover:text-primary-700 sm:flex">
                        Voir tout
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-6">
                    <x-web.carousel>
                        @foreach ($newProducts as $product)
                            <x-web.premium-product-card :product="$product" class="w-56 shrink-0 snap-start sm:w-64" />
                        @endforeach
                    </x-web.carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- Nos Packs Beauté --}}
    @if ($packs->isNotEmpty())
        <section class="bg-gray-50 pt-2 pb-10 sm:pt-3 sm:pb-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Nos Packs Beauté</h2>
                    </div>
                    <a href="{{ route('packs.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-primary-600 transition hover:text-primary-700 sm:flex">
                        Voir tout
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-6">
                    <x-web.carousel>
                        @foreach ($packs as $pack)
                            <x-web.premium-pack-card :pack="$pack" class="w-56 shrink-0 snap-start sm:w-64" />
                        @endforeach
                    </x-web.carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- Nos meilleures ventes --}}
    @if ($bestSellers->isNotEmpty())
        <section class="bg-white py-10 sm:py-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Nos meilleures ventes</h2>
                    </div>
                    <a href="{{ route('shop.index', ['bestSeller' => 1]) }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-primary-600 transition hover:text-primary-700 sm:flex">
                        Voir tout
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-6">
                    <x-web.carousel>
                        @foreach ($bestSellers as $product)
                            <x-web.premium-product-card :product="$product" class="w-56 shrink-0 snap-start sm:w-64" />
                        @endforeach
                    </x-web.carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- Programme Fidélité --}}
    <section class="bg-gray-50 py-10 sm:py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('shop.index', ['fidelite' => 1]) }}" class="block overflow-hidden rounded-3xl">
                <img
                    src="{{ asset('images/banniere_fidelite.png') }}"
                    alt="Programme fidélité Rahmane Beauty"
                    class="h-full w-full object-cover"
                    loading="lazy"
                >
            </a>
        </div>
    </section>

    {{-- Top Marques --}}
    @if ($brands->isNotEmpty())
        <section class="bg-white pt-10 pb-2 sm:pt-14 sm:pb-3">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Top marques</h2>
                    <a href="{{ route('shop.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-primary-600 transition hover:text-primary-700 sm:flex">
                        Voir toutes les marques
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-6">
                    <x-web.carousel>
                        @foreach ($brands as $brand)
                            <x-web.brand-logo-card :brand="$brand" class="h-28 w-52 sm:h-32 sm:w-60" />
                        @endforeach
                    </x-web.carousel>
                </div>
            </div>
        </section>
    @endif

    {{-- Conseils Beauté --}}
    @if ($articles->isNotEmpty())
        <section class="bg-white pt-2 pb-10 sm:pt-3 sm:pb-14">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">Conseils Beauté</h2>
                        <p class="mt-1 text-sm text-gray-500">Nos derniers articles pour prendre soin de vous.</p>
                    </div>
                    <a href="{{ route('blog.index') }}" class="hidden shrink-0 items-center gap-1.5 text-sm font-semibold text-primary-600 transition hover:text-primary-700 sm:flex">
                        Voir tout
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="mt-6">
                    <x-web.carousel>
                        @foreach ($articles as $article)
                            <x-web.premium-article-card :article="$article" class="w-72 shrink-0 snap-start sm:w-80" />
                        @endforeach
                    </x-web.carousel>
                </div>
            </div>
        </section>
    @endif
</x-web-layout>

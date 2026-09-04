@php
    $socialLinks = collect([
        ['label' => 'Facebook', 'url' => $settings['social_facebook'], 'brand' => 'facebook', 'color' => 'bg-[#1877F2]'],
        ['label' => 'Instagram', 'url' => $settings['social_instagram'], 'brand' => 'instagram', 'color' => 'bg-[#E4405F]'],
        ['label' => 'TikTok', 'url' => $settings['social_tiktok'], 'brand' => 'tiktok', 'color' => 'bg-black'],
        ['label' => 'WhatsApp', 'url' => $settings['shop_whatsapp'] ? 'https://wa.me/'.preg_replace('/\D/', '', $settings['shop_whatsapp']) : null, 'brand' => 'whatsapp', 'color' => 'bg-[#25D366]'],
        ['label' => 'X', 'url' => $settings['social_x'], 'brand' => 'x', 'color' => 'bg-black'],
        ['label' => 'LinkedIn', 'url' => $settings['social_linkedin'], 'brand' => 'linkedin', 'color' => 'bg-[#0A66C2]'],
        ['label' => 'YouTube', 'url' => $settings['social_youtube'], 'brand' => 'youtube', 'color' => 'bg-[#FF0000]'],
    ])->filter(fn ($link) => filled($link['url']));

    $paymentMethods = [
        ['label' => 'Wave', 'image' => 'images/payment_wave.png', 'shape' => 'circle'],
        ['label' => 'Orange Money', 'image' => 'images/payment_orange_money.png', 'shape' => 'circle'],
        ['label' => 'Free Money', 'image' => 'images/payment_free_money.png', 'shape' => 'circle'],
        ['label' => 'Carte bancaire', 'image' => 'images/payment_carte_bancaire.png', 'shape' => 'card'],
    ];

    $address = collect([$settings['shop_address'], $settings['shop_city']])->filter()->implode(', ');
@endphp

<footer class="bg-blush-100 text-gray-900">
    <div class="mx-auto max-w-screen-2xl px-6 py-14 sm:px-8 lg:px-12 lg:py-20">
        <div class="grid grid-cols-1 gap-14 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Marque + description + contact --}}
            <div>
                <x-web.logo iconClass="h-10 w-10" imgClass="h-14" />

                <p class="mt-4 text-base leading-relaxed text-gray-600">
                    {{ $settings['shop_slogan'] ?: 'Votre parapharmacie en ligne de confiance au Sénégal.' }}
                </p>

                <ul class="mt-5 space-y-3 text-base">
                    @if ($settings['shop_phone'])
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white">
                                <x-icon name="phone" class="h-4 w-4" />
                            </span>
                            <a href="tel:{{ $settings['shop_phone'] }}" class="font-medium text-gray-800 transition hover:text-primary-600">{{ $settings['shop_phone'] }}</a>
                        </li>
                    @endif

                    @if ($settings['shop_email'])
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white">
                                <x-icon name="mail" class="h-4 w-4" />
                            </span>
                            <a href="mailto:{{ $settings['shop_email'] }}" class="break-all font-medium text-gray-800 transition hover:text-primary-600">{{ $settings['shop_email'] }}</a>
                        </li>
                    @endif

                    @if ($address)
                        <li class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white">
                                <x-icon name="map-pin" class="h-4 w-4" />
                            </span>
                            <span class="font-medium text-gray-800">{{ $address }}</span>
                        </li>
                    @endif
                </ul>

                <div class="mt-6 flex items-start gap-3 rounded-xl border border-primary-200 bg-white/60 p-4">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                        <x-icon name="shield" class="h-4 w-4" />
                    </span>
                    <p class="text-sm leading-relaxed text-gray-600">
                        Des produits <span class="font-semibold text-primary-600">authentiques</span>, sélectionnés avec soin pour votre bien-être.
                    </p>
                </div>
            </div>

            {{-- Catégories principales --}}
            <div>
                <p class="text-base font-bold uppercase tracking-wide text-gray-900">Catégories</p>
                <span class="mt-2 block h-0.5 w-8 rounded-full bg-primary-600"></span>

                <ul class="mt-5 space-y-1">
                    @foreach (array_slice($menuCategories, 0, 6) as $category)
                        <li>
                            <a
                                href="{{ route('shop.index', ['category' => $category['slug']]) }}"
                                class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600"
                            >
                                {{ $category['name'] }}
                                <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                            </a>
                        </li>
                    @endforeach
                </ul>

                <a
                    href="{{ route('shop.index') }}"
                    class="mt-5 inline-flex items-center gap-1.5 rounded-full border border-primary-600 px-5 py-2.5 text-sm font-semibold text-primary-600 transition hover:bg-primary-600 hover:text-white"
                >
                    Voir toutes les catégories
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            {{-- Informations --}}
            <div>
                <p class="text-base font-bold uppercase tracking-wide text-gray-900">Informations</p>
                <span class="mt-2 block h-0.5 w-8 rounded-full bg-primary-600"></span>

                <ul class="mt-5 space-y-1">
                    <li>
                        <a href="{{ route('pages.about') }}" class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            À propos
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.chat') }}" class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            Support
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.contact') }}" class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            Contact
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.faq') }}" class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            FAQ
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.terms') }}" class="group flex items-center justify-between gap-2 border-b border-primary-200/60 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            Conditions générales
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.privacy') }}" class="group flex items-center justify-between gap-2 py-3 text-base font-medium text-gray-800 transition hover:text-primary-600">
                            Politique de confidentialité
                            <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-primary-300 transition group-hover:translate-x-0.5 group-hover:text-primary-600" />
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Réseaux sociaux + moyens de paiement --}}
            <div>
                @if ($socialLinks->isNotEmpty())
                    <div>
                        <p class="text-base font-bold uppercase tracking-wide text-gray-900">Suivez-nous</p>
                        <span class="mt-2 block h-0.5 w-8 rounded-full bg-primary-600"></span>

                        <div class="mt-4 flex items-center gap-2.5">
                            @foreach ($socialLinks as $link)
                                <a
                                    href="{{ $link['url'] }}"
                                    target="_blank"
                                    rel="noopener"
                                    aria-label="{{ $link['label'] }}"
                                    class="flex h-11 w-11 items-center justify-center rounded-full {{ $link['color'] }} text-white shadow-sm transition hover:scale-105 hover:opacity-90"
                                >
                                    <x-brand-icon name="{{ $link['brand'] }}" class="h-5 w-5" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="{{ $socialLinks->isNotEmpty() ? 'mt-7' : '' }}">
                    <p class="text-base font-bold uppercase tracking-wide text-gray-900">Moyens de paiement</p>
                    <span class="mt-2 block h-0.5 w-8 rounded-full bg-primary-600"></span>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @foreach ($paymentMethods as $method)
                            <span class="inline-flex items-center gap-2 rounded-lg border border-primary-200 bg-white px-3.5 py-2 text-sm font-medium text-gray-700">
                                <img
                                    src="{{ asset($method['image']) }}"
                                    alt="{{ $method['label'] }}"
                                    class="{{ $method['shape'] === 'circle' ? 'h-6 w-6 rounded-full object-cover' : 'h-5 w-auto rounded' }}"
                                >
                                {{ $method['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-primary-200 pt-7 text-sm text-gray-500 sm:flex-row">
            <p class="flex items-center gap-1.5 order-2 sm:order-1">
                <x-icon name="heart" class="h-4 w-4 text-primary-400" />
                Prenez soin de vous, nous prenons soin de vous.
            </p>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="h-6 w-6 shrink-0 text-primary-300 order-1 sm:order-2" fill="none">
                <g fill="currentColor">
                    <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(0 20 20)" />
                    <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(72 20 20)" />
                    <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(144 20 20)" />
                    <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(216 20 20)" />
                    <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(288 20 20)" />
                </g>
                <circle cx="20" cy="20" r="3.5" fill="white" />
            </svg>

            <p class="order-3">&copy; {{ now()->year }} <span class="font-semibold text-primary-600">{{ $settings['shop_name'] }}</span>. Tous droits réservés.</p>
        </div>
    </div>
</footer>

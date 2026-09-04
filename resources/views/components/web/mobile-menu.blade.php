<div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-40 lg:hidden" role="dialog" aria-modal="true">
    <div
        class="fixed inset-0 bg-gray-900/40"
        @click="mobileMenuOpen = false"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <div
        class="fixed inset-y-0 left-0 flex w-full max-w-xs flex-col overflow-y-auto bg-white shadow-xl"
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4">
            <x-web.logo />
            <button
                type="button"
                class="rounded-full p-2 text-gray-500 hover:bg-gray-100"
                @click="mobileMenuOpen = false"
                aria-label="Fermer le menu"
            >
                <x-icon name="x-mark" class="h-5 w-5" />
            </button>
        </div>

        <nav class="flex-1 px-2 py-4">
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Catégories</p>
            <ul>
                @foreach ($menuCategories ?? [] as $category)
                    <li>
                        <a
                            href="{{ route('shop.index', ['category' => $category['slug']]) }}"
                            class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            {{ $category['name'] }}
                            <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="my-4 border-t border-gray-100"></div>

            <ul class="space-y-1">
                <li>
                    <a href="{{ route('packs.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Packs
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('blog.index') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Conseils beauté
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('support.chat') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Support
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.contact') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Contact
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.faq') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        FAQ
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.about') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        À propos
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.terms') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Conditions générales
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
                <li>
                    <a href="{{ route('pages.privacy') }}" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Politique de confidentialité
                        <x-icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mx-3 mb-4 flex items-center gap-3 rounded-xl bg-blush-100 p-4">
            <x-icon name="truck" class="h-8 w-8 shrink-0 text-primary-600" />
            <div>
                <p class="text-sm font-semibold text-gray-900">Livraison en moins de 24h à Thiès</p>
                <p class="text-xs text-gray-500">Rapide, sécurisée et fiable.</p>
            </div>
        </div>
    </div>
</div>

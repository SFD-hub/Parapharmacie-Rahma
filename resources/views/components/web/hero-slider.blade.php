@props(['slides', 'ratio' => '1719 / 915'])

{{-- Composant piloté par les données : pour ajouter une bannière, il suffit
     d'ajouter une entrée au tableau $slides (voir HomeController) avec
     image/badge/title/subtitle/description/primaryButton/secondaryButton,
     sans modifier ce fichier. --}}
<section
    class="relative overflow-hidden bg-gray-900"
    x-data="{
            active: 0,
            total: {{ count($slides) }},
            timer: null,
            start() {
                if (this.total <= 1) return;
                this.timer = setInterval(() => this.next(), 5000);
            },
            stop() {
                clearInterval(this.timer);
            },
            restart() {
                this.stop();
                this.start();
            },
            next() {
                this.active = (this.active + 1) % this.total;
            },
            prev() {
                this.active = (this.active - 1 + this.total) % this.total;
            },
            goTo(index) {
                this.active = index;
                this.restart();
            },
        }"
    x-init="start()"
    @mouseenter="stop()"
    @mouseleave="start()"
>
    {{-- Cadre unique partagé par toutes les bannières (nécessaire pour un
         fondu enchaîné propre : les slides se superposent exactement).
         Hauteur plafonnée pour que la bannière reste toujours visible en
         entier sans défilement, même sur un écran large ; les images (au
         format éventuellement différent les unes des autres) sont recadrées
         proprement par object-cover pour remplir ce cadre. --}}
    <div class="relative w-full" style="aspect-ratio: {{ $ratio }}; max-height: 560px;">
        @foreach ($slides as $index => $slide)
            <div
                x-show="active === {{ $index }}"
                x-cloak
                x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-700"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0"
            >
                <img
                    src="{{ $slide['image'] }}"
                    alt=""
                    x-bind:class="active === {{ $index }} ? 'scale-105' : 'scale-100'"
                    class="h-full w-full object-cover transition-transform duration-[6000ms] ease-out"
                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                >

                @if (! empty($slide['title']))
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent"></div>

                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full px-6 sm:px-10 lg:px-16">
                            <div class="max-w-md">
                                @if (! empty($slide['badge']))
                                    <span
                                        x-show="active === {{ $index }}"
                                        x-transition:enter="transition ease-out duration-500 delay-300"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="inline-flex items-center rounded-full bg-primary-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white"
                                    >
                                        {{ $slide['badge'] }}
                                    </span>
                                @endif

                                <h1
                                    x-show="active === {{ $index }}"
                                    x-transition:enter="transition ease-out duration-500 delay-500"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="mt-3 text-2xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl"
                                >
                                    {{ $slide['title'] }}
                                    @if (! empty($slide['subtitle']))
                                        <br>
                                        <span class="text-primary-400">{{ $slide['subtitle'] }}</span>
                                    @endif
                                </h1>

                                @if (! empty($slide['description']))
                                    <p
                                        x-show="active === {{ $index }}"
                                        x-transition:enter="transition ease-out duration-500 delay-700"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="mt-4 text-sm leading-relaxed text-white/90 sm:text-base"
                                    >
                                        {{ $slide['description'] }}
                                    </p>
                                @endif

                                @if (! empty($slide['primaryButton']) || ! empty($slide['secondaryButton']))
                                    <div
                                        x-show="active === {{ $index }}"
                                        x-transition:enter="transition ease-out duration-500 delay-1000"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="mt-6 flex flex-wrap items-center gap-3"
                                    >
                                        @if (! empty($slide['primaryButton']))
                                            <a
                                                href="{{ $slide['primaryButton']['href'] }}"
                                                class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary-700"
                                            >
                                                {{ $slide['primaryButton']['label'] }}
                                            </a>
                                        @endif

                                        @if (! empty($slide['secondaryButton']))
                                            <a
                                                href="{{ $slide['secondaryButton']['href'] }}"
                                                class="inline-flex items-center gap-2 rounded-full border border-white/70 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                                            >
                                                {{ $slide['secondaryButton']['label'] }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        @if (count($slides) > 1)
            <button
                type="button"
                @click="prev(); restart()"
                class="absolute left-3 top-1/2 z-10 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-700 transition hover:bg-white sm:flex"
                aria-label="Bannière précédente"
            >
                <x-icon name="chevron-left" class="h-5 w-5" />
            </button>
            <button
                type="button"
                @click="next(); restart()"
                class="absolute right-3 top-1/2 z-10 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/80 text-gray-700 transition hover:bg-white sm:flex"
                aria-label="Bannière suivante"
            >
                <x-icon name="chevron-right" class="h-5 w-5" />
            </button>

            <div class="absolute inset-x-0 bottom-4 z-10 flex justify-center gap-1.5">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        @click="goTo({{ $index }})"
                        class="h-1.5 rounded-full transition-all"
                        :class="active === {{ $index }} ? 'w-6 bg-white' : 'w-1.5 bg-white/50'"
                        aria-label="Aller à la bannière {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

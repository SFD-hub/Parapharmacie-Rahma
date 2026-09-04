@props(['autoplay' => true, 'interval' => 4500])

{{--
    Piste de défilement horizontal générique : le contenu (cartes produit,
    pack, logo...) est fourni par le slot, ce composant ne gère que le
    mécanisme (flèches, glisser-déposer souris, défilement auto, snap).
    Le tactile mobile utilise le défilement natif du navigateur, jamais
    intercepté, pour rester parfaitement fluide.
--}}
<div
    x-data="{
        canScrollLeft: false,
        canScrollRight: false,
        timer: null,
        dragging: false,
        startX: 0,
        scrollStart: 0,
        init() {
            this.updateArrows();
            this.$refs.track.addEventListener('scroll', () => this.updateArrows());
            new ResizeObserver(() => this.updateArrows()).observe(this.$refs.track);
            @if ($autoplay)
                this.start();
            @endif
        },
        updateArrows() {
            const el = this.$refs.track;
            this.canScrollLeft = el.scrollLeft > 4;
            this.canScrollRight = el.scrollLeft < el.scrollWidth - el.clientWidth - 4;
        },
        itemWidth() {
            const item = this.$refs.track.firstElementChild;
            return item ? item.getBoundingClientRect().width + 20 : this.$refs.track.clientWidth * 0.9;
        },
        scrollByStep(direction) {
            this.$refs.track.scrollBy({ left: direction * this.itemWidth(), behavior: 'smooth' });
        },
        start() {
            this.timer = setInterval(() => {
                if (this.canScrollRight) {
                    this.scrollByStep(1);
                } else {
                    this.$refs.track.scrollTo({ left: 0, behavior: 'smooth' });
                }
            }, {{ $interval }});
        },
        stop() {
            clearInterval(this.timer);
        },
        dragStart(event) {
            this.dragging = true;
            this.startX = event.pageX - this.$refs.track.offsetLeft;
            this.scrollStart = this.$refs.track.scrollLeft;
            this.stop();
        },
        dragMove(event) {
            if (! this.dragging) return;
            event.preventDefault();
            const x = event.pageX - this.$refs.track.offsetLeft;
            this.$refs.track.scrollLeft = this.scrollStart - (x - this.startX);
        },
        dragEnd() {
            if (! this.dragging) return;
            this.dragging = false;
            @if ($autoplay)
                this.start();
            @endif
        },
    }"
    @mouseenter="stop()"
    @mouseleave="dragEnd(); {{ $autoplay ? 'start()' : '' }}"
    class="relative"
>
    <div
        x-ref="track"
        x-on:mousedown="dragStart($event)"
        x-on:mousemove="dragMove($event)"
        x-on:mouseup="dragEnd()"
        x-bind:class="dragging ? 'cursor-grabbing select-none' : 'cursor-grab'"
        class="scrollbar-hide flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2"
    >
        {{ $slot }}
    </div>

    <button
        type="button"
        x-show="canScrollLeft"
        x-cloak
        x-on:click="scrollByStep(-1)"
        class="absolute -left-4 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-gray-700 shadow-lg transition hover:scale-105 hover:text-primary-600 sm:flex"
        aria-label="Précédent"
    >
        <x-icon name="chevron-left" class="h-5 w-5" />
    </button>

    <button
        type="button"
        x-show="canScrollRight"
        x-cloak
        x-on:click="scrollByStep(1)"
        class="absolute -right-4 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-gray-100 bg-white text-gray-700 shadow-lg transition hover:scale-105 hover:text-primary-600 sm:flex"
        aria-label="Suivant"
    >
        <x-icon name="chevron-right" class="h-5 w-5" />
    </button>
</div>

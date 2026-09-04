<div x-data="{ open: false }" @click.outside="open = false" class="relative">
    <button
        type="button"
        @click="open = !open"
        class="relative p-2 text-primary-600 hover:text-primary-700"
        aria-label="Panier"
    >
        <x-icon name="cart" class="h-6 w-6" />
        @if ($count > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-primary-600 text-[10px] font-semibold text-white">
                {{ $count }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 z-40 mt-2 w-80 rounded-2xl border border-gray-100 bg-white p-4 shadow-xl"
    >
        @if ($items->isEmpty())
            <p class="py-6 text-center text-sm text-gray-500">Votre panier est vide.</p>
        @else
            <div class="max-h-72 space-y-3 overflow-y-auto">
                @foreach ($items as $item)
                    <div wire:key="mini-cart-{{ $item->id }}" class="flex items-center gap-3">
                        <div class="h-12 w-12 shrink-0 overflow-hidden rounded-lg bg-gray-50">
                            @if ($item->product->images->first())
                                <img src="{{ $item->product->images->first()->path }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ number_format($item->unitPrice(), 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 text-sm font-semibold text-gray-900">
                <span>Sous-total</span>
                <span>{{ number_format($totals->subtotal, 0, ',', ' ') }} FCFA</span>
            </div>

            <a
                href="{{ route('cart.index') }}"
                class="mt-4 block w-full rounded-full border border-primary-600 py-2 text-center text-sm font-semibold text-primary-600 hover:bg-primary-50"
            >
                Voir mon panier
            </a>
        @endif
    </div>
</div>

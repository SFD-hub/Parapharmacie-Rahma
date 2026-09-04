<x-web-layout title="Commande confirmée">
    <div class="mx-auto max-w-2xl px-4 py-16 text-center sm:px-6 lg:px-8">
        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
            <x-icon name="check" class="h-8 w-8" />
        </span>

        <h1 class="mt-6 text-2xl font-bold text-gray-900">Merci pour votre commande !</h1>
        <p class="mt-2 text-sm text-gray-500">
            Votre commande <strong>{{ $order->order_number }}</strong> a bien été enregistrée.
        </p>

        <div class="mt-8 rounded-2xl border border-gray-100 bg-white p-6 text-left">
            <h2 class="text-sm font-semibold text-gray-900">Récapitulatif</h2>

            <ul class="mt-3 divide-y divide-gray-100 text-sm">
                @foreach ($order->items as $item)
                    <li class="flex items-center justify-between py-2">
                        <span>{{ $item->quantity }} × {{ $item->product_name }}</span>
                        <span>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 text-base font-bold text-gray-900">
                <span>Total</span>
                <span>{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a
                href="{{ route('account.orders.show', $order) }}"
                class="rounded-full border border-primary-600 px-5 py-2.5 text-sm font-semibold text-primary-600 hover:bg-primary-50"
            >
                Voir ma commande
            </a>
            <a
                href="{{ route('shop.index') }}"
                class="rounded-full bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700"
            >
                Continuer mes achats
            </a>
        </div>
    </div>
</x-web-layout>

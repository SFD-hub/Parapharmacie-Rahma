<x-web-layout :title="'Commande '.$order->order_number">
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
        <a href="{{ route('account.orders.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <x-icon name="chevron-left" class="h-4 w-4" />
            Mes commandes
        </a>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Commande {{ $order->order_number }}</h1>
            <x-order-status-badge :status="$order->status" />
        </div>
        <p class="mt-1 text-sm text-gray-500">Passée le {{ $order->created_at->translatedFormat('d M Y à H:i') }}</p>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Articles</h2>
                    <ul class="mt-3 divide-y divide-gray-100 text-sm">
                        @foreach ($order->items as $item)
                            <li class="flex items-center justify-between py-3">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                                    @if ($item->product_sku)
                                        <p class="text-xs text-gray-400">SKU : {{ $item->product_sku }}</p>
                                    @endif
                                    <p class="text-gray-500">{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($order->statusHistories->isNotEmpty())
                    <div class="rounded-2xl border border-gray-100 bg-white p-5">
                        <h2 class="text-sm font-semibold text-gray-900">Suivi de la commande</h2>
                        <ol class="mt-3 space-y-3 text-sm">
                            @foreach ($order->statusHistories as $history)
                                <li class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $history->status->label() }}</span>
                                    <span class="text-gray-400">{{ $history->created_at->translatedFormat('d M Y à H:i') }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Adresse de livraison</h2>
                    @if ($order->address)
                        <x-address-card :address="$order->address" />
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Paiement</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <dt>Méthode</dt>
                            <dd>{{ $order->payment_method?->label() }}</dd>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <dt>Statut</dt>
                            <dd>{{ $order->payment_status->label() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Montants</h2>
                    <x-totals-summary
                        :subtotal="$order->subtotal"
                        :discount="$order->discount"
                        :packDiscount="$order->pack_discount"
                        :pointsDiscount="$order->points_discount"
                        :shipping="$order->shipping_cost"
                        :total="$order->total"
                        class="mt-3"
                    />
                </div>

                @if ($order->points_used > 0 || $order->points_earned > 0)
                    <div class="rounded-2xl border border-gray-100 bg-white p-5">
                        <h2 class="text-sm font-semibold text-gray-900">Fidélité</h2>
                        <dl class="mt-3 space-y-2 text-sm">
                            @if ($order->points_used > 0)
                                <div class="flex justify-between text-gray-600">
                                    <dt>Points utilisés</dt>
                                    <dd>-{{ $order->points_used }} pts</dd>
                                </div>
                            @endif
                            @if ($order->points_earned > 0)
                                <div class="flex justify-between text-gray-600">
                                    <dt>Points gagnés</dt>
                                    <dd>+{{ $order->points_earned }} pts</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-web-layout>

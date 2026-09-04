<x-admin-layout :title="'Commande '.$order->order_number">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Commande {{ $order->order_number }}</h1>
            <p class="text-sm text-gray-500">Passée le {{ $order->created_at->translatedFormat('d M Y à H:i') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <x-order-status-badge :status="$order->status" />
            <x-admin.orders.validate-button :order="$order" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
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

                <x-totals-summary
                    :subtotal="$order->subtotal"
                    :discount="$order->discount"
                    :packDiscount="$order->pack_discount"
                    :pointsDiscount="$order->points_discount"
                    :shipping="$order->shipping_cost"
                    :total="$order->total"
                    class="mt-3 border-t border-gray-100 pt-3"
                />
            </div>

            @if ($order->points_used > 0 || $order->points_earned !== null)
                <div class="rounded-2xl border border-gray-100 bg-white p-5">
                    <h2 class="text-sm font-semibold text-gray-900">Fidélité</h2>
                    <dl class="mt-2 space-y-1 text-sm">
                        @if ($order->points_used > 0)
                            <div class="flex justify-between text-gray-600">
                                <dt>Points utilisés</dt>
                                <dd>{{ $order->points_used }} pts</dd>
                            </div>
                        @endif
                        @if ($order->points_earned !== null)
                            <div class="flex justify-between text-gray-600">
                                <dt>Points gagnés</dt>
                                <dd>{{ $order->points_earned }} pts</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-sm font-semibold text-gray-900">Historique des statuts</h2>
                <ol class="mt-3 space-y-3 text-sm">
                    @forelse ($order->statusHistories as $history)
                        <li class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-gray-900">{{ $history->status->label() }}</p>
                                @if ($history->note)
                                    <p class="text-gray-500">{{ $history->note }}</p>
                                @endif
                                @if ($history->admin)
                                    <p class="text-xs text-gray-400">Par {{ $history->admin->name }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 text-xs text-gray-400">{{ $history->created_at->translatedFormat('d M Y à H:i') }}</span>
                        </li>
                    @empty
                        <li class="text-gray-400">Aucun historique.</li>
                    @endforelse
                </ol>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-sm font-semibold text-gray-900">Client</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $order->user?->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->user?->email }}</p>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-sm font-semibold text-gray-900">Adresse de livraison</h2>
                @if ($order->address)
                    <x-address-card :address="$order->address" />
                @endif
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-sm font-semibold text-gray-900">Paiement</h2>
                <dl class="mt-2 space-y-1 text-sm">
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
                <h2 class="text-sm font-semibold text-gray-900">Facture</h2>
                @if ($order->invoice)
                    <p class="mt-2 text-sm text-gray-600">{{ $order->invoice->invoice_number }}</p>
                    <p class="text-xs text-gray-400">Générée le {{ $order->invoice->created_at->translatedFormat('d M Y à H:i') }}</p>
                    <div class="mt-3">
                        <x-admin.orders.invoice-actions :order="$order" />
                    </div>
                @else
                    <p class="mt-2 text-sm text-gray-500">Aucune facture générée pour cette commande.</p>
                    <div class="mt-3">
                        <x-admin.orders.invoice-actions :order="$order" />
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-5">
                <h2 class="text-sm font-semibold text-gray-900">Changer le statut</h2>
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="mt-3 space-y-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <textarea
                        name="note"
                        rows="2"
                        placeholder="Note (optionnel)"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
                    ></textarea>
                    <x-primary-button>Mettre à jour</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

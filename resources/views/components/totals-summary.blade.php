@props(['subtotal', 'discount' => 0, 'packDiscount' => 0, 'pointsDiscount' => 0, 'shipping', 'total'])

<dl {{ $attributes->class(['space-y-2 text-sm']) }}>
    <div class="flex items-center justify-between text-gray-600">
        <dt>Sous-total</dt>
        <dd>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</dd>
    </div>
    @if ($discount > 0)
        <div class="flex items-center justify-between text-emerald-600">
            <dt>Remise</dt>
            <dd>-{{ number_format($discount, 0, ',', ' ') }} FCFA</dd>
        </div>
    @endif
    @if ($packDiscount > 0)
        <div class="flex items-center justify-between text-emerald-600">
            <dt>Économie pack</dt>
            <dd>-{{ number_format($packDiscount, 0, ',', ' ') }} FCFA</dd>
        </div>
    @endif
    @if ($pointsDiscount > 0)
        <div class="flex items-center justify-between text-primary-600">
            <dt>Réduction fidélité</dt>
            <dd>-{{ number_format($pointsDiscount, 0, ',', ' ') }} FCFA</dd>
        </div>
    @endif
    <div class="flex items-center justify-between text-gray-600">
        <dt>Livraison</dt>
        <dd>{{ $shipping > 0 ? number_format($shipping, 0, ',', ' ').' FCFA' : 'Offerte' }}</dd>
    </div>
    <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-base font-bold text-gray-900">
        <dt>Total</dt>
        <dd>{{ number_format($total, 0, ',', ' ') }} FCFA</dd>
    </div>
</dl>

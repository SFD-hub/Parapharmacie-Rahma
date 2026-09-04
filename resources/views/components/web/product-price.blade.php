@props(['product', 'size' => 'sm'])

@php
    $textSize = $size === 'lg' ? 'text-2xl' : 'text-sm';
@endphp

<div class="flex items-baseline gap-2">
    <span class="{{ $textSize }} font-bold text-primary-600">
        {{ number_format($product->displayPrice(), 0, ',', ' ') }} FCFA
    </span>
    @if ($product->hasDiscount())
        <span class="text-xs text-gray-400 line-through">
            {{ number_format($product->price, 0, ',', ' ') }} FCFA
        </span>
    @endif
</div>

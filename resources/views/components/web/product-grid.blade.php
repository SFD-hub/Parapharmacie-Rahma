@props(['products', 'columns' => 4])

@php
    $colsClass = match ($columns) {
        3 => 'lg:grid-cols-3',
        5 => 'lg:grid-cols-5',
        default => 'lg:grid-cols-4',
    };
@endphp

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 {{ $colsClass }}">
    @foreach ($products as $product)
        <x-web.product-card :product="$product" wire:key="product-{{ $product->id }}" />
    @endforeach
</div>

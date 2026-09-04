@props(['type', 'value' => null])

@php
    $styles = [
        'sale' => 'bg-primary-600 text-white',
        'new' => 'bg-emerald-600 text-white',
        'best-seller' => 'bg-amber-500 text-white',
        'out-of-stock' => 'bg-gray-500 text-white',
        'expired' => 'bg-red-600 text-white',
    ];

    $labels = [
        'sale' => '-'.$value.'%',
        'new' => 'Nouveau',
        'best-seller' => 'Best-seller',
        'out-of-stock' => 'Rupture',
        'expired' => 'Expiré',
    ];
@endphp

<span class="rounded-full px-2 py-1 text-xs font-semibold {{ $styles[$type] ?? 'bg-gray-100 text-gray-600' }}">
    {{ $labels[$type] ?? '' }}
</span>

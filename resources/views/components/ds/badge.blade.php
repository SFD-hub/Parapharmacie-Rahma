@props(['color' => 'neutral', 'variant' => 'soft'])

@php
    // "soft" = pastille de statut (tableaux/listes), reprend la convention
    // déjà utilisée par order-status-badge/review-status-badge/etc.
    // "solid" = badge superposé sur une image (produit en promo, épuisé...),
    // reprend la convention de components/web/product-badge.blade.php.
    $soft = [
        'primary' => 'bg-primary-50 text-primary-700',
        'success' => 'bg-emerald-50 text-emerald-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger' => 'bg-red-50 text-red-700',
        'info' => 'bg-blue-50 text-blue-700',
        'neutral' => 'bg-gray-100 text-gray-500',
    ];

    $solid = [
        'primary' => 'bg-primary-600 text-white',
        'success' => 'bg-emerald-600 text-white',
        'warning' => 'bg-amber-500 text-white',
        'danger' => 'bg-red-600 text-white',
        'info' => 'bg-blue-600 text-white',
        'neutral' => 'bg-gray-500 text-white',
    ];

    $palette = $variant === 'solid' ? $solid : $soft;
    $classes = 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium '.($palette[$color] ?? $palette['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

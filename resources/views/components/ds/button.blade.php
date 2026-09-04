@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus-visible:ring-primary-500',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus-visible:ring-primary-500',
        'outline' => 'border border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white focus-visible:ring-primary-500',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus-visible:ring-danger-500',
        'ghost' => 'text-gray-600 hover:bg-gray-100 focus-visible:ring-primary-500',
    ];

    $sizes = [
        'sm' => 'gap-1.5 px-3 py-1.5 text-xs',
        'md' => 'gap-2 px-4 py-2 text-sm',
        'lg' => 'gap-2 px-5 py-2.5 text-base',
    ];

    $classes = 'inline-flex items-center justify-center rounded-full font-semibold transition '
        .'disabled:cursor-not-allowed disabled:opacity-50 '
        .'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

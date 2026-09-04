@props(['type' => 'info', 'icon' => null])

@php
    $styles = [
        'success' => ['bg-emerald-50 text-emerald-700', 'check'],
        'error' => ['bg-red-50 text-red-700', 'exclamation'],
        'warning' => ['bg-amber-50 text-amber-700', 'exclamation'],
        'info' => ['bg-blue-50 text-blue-700', 'clipboard'],
    ];

    [$colorClasses, $defaultIcon] = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-center gap-2 rounded-lg px-4 py-3 text-sm {$colorClasses}"]) }}>
    <x-icon :name="$icon ?? $defaultIcon" class="h-4 w-4 shrink-0" />
    <div>{{ $slot }}</div>
</div>

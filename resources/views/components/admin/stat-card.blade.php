@props(['icon', 'label', 'value', 'color' => 'gray', 'href' => null])

@php
    $palette = match ($color) {
        'blue' => ['border-blue-100', 'bg-blue-50/60', 'bg-blue-100', 'text-blue-600', 'text-blue-700'],
        'emerald' => ['border-emerald-100', 'bg-emerald-50/60', 'bg-emerald-100', 'text-emerald-600', 'text-emerald-700'],
        'amber' => ['border-amber-100', 'bg-amber-50/60', 'bg-amber-100', 'text-amber-600', 'text-amber-700'],
        'red' => ['border-red-100', 'bg-red-50/60', 'bg-red-100', 'text-red-600', 'text-red-700'],
        'indigo' => ['border-indigo-100', 'bg-indigo-50/60', 'bg-indigo-100', 'text-indigo-600', 'text-indigo-700'],
        default => ['border-gray-100', 'bg-gray-50/60', 'bg-gray-100', 'text-gray-600', 'text-gray-700'],
    };
    [$border, $bg, $iconBg, $iconText, $valueText] = $palette;
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => "flex items-center justify-between gap-3 rounded-xl border {$border} {$bg} p-4".($href ? ' transition duration-300 hover:-translate-y-0.5 hover:shadow-md' : '')]) }}>
    <div class="flex min-w-0 items-center gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $iconBg }} {{ $iconText }}">
            <x-icon name="{{ $icon }}" class="h-5 w-5" />
        </span>
        <span class="truncate text-sm font-medium text-gray-700">{{ $label }}</span>
    </div>
    <span class="shrink-0 text-xl font-bold {{ $valueText }}">{{ $value }}</span>
</{{ $tag }}>

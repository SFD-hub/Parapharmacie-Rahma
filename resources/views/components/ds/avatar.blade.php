@props(['name' => '', 'size' => 'md'])

@php
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-14 w-14 text-base',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full bg-primary-50 font-semibold text-primary-700 '.($sizes[$size] ?? $sizes['md'])]) }}>
    {{ $initials !== '' ? $initials : '?' }}
</span>

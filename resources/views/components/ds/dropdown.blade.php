@props(['align' => 'right', 'width' => '48'])

@php
    $alignmentClasses = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
    $widthClass = match ($width) {
        '48' => 'w-48',
        '60' => 'w-60',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 rounded-xl border border-gray-100 bg-white py-1 shadow-lg {{ $widthClass }} {{ $alignmentClasses }}"
        @click="open = false"
    >
        {{ $slot }}
    </div>
</div>

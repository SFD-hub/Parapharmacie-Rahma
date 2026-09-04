@props(['text'])

<span class="group relative inline-flex">
    {{ $slot }}
    <span
        role="tooltip"
        class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 -translate-x-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-2 py-1 text-xs text-white opacity-0 transition group-hover:opacity-100 group-focus-within:opacity-100"
    >
        {{ $text }}
    </span>
</span>

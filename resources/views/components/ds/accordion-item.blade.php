@props(['name', 'title'])

<div>
    <button
        type="button"
        class="flex w-full items-center justify-between px-5 py-4 text-left text-sm font-semibold text-gray-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
        @click="openItem = openItem === '{{ $name }}' ? null : '{{ $name }}'"
        :aria-expanded="openItem === '{{ $name }}'"
    >
        {{ $title }}
        <x-icon
            name="chevron-down"
            class="h-4 w-4 shrink-0 transition"
            x-bind:class="openItem === '{{ $name }}' ? 'rotate-180' : ''"
        />
    </button>
    <div x-show="openItem === '{{ $name }}'" x-cloak class="px-5 pb-4 text-sm text-gray-600">
        {{ $slot }}
    </div>
</div>

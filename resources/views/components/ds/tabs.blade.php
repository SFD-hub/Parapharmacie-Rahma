@props(['items' => [], 'active' => null])

@php
    $active ??= array_key_first($items);
@endphp

<div x-data="{ activeTab: @js($active) }">
    <div class="flex gap-1 border-b border-gray-100" role="tablist">
        @foreach ($items as $key => $label)
            <button
                type="button"
                role="tab"
                :aria-selected="activeTab === '{{ $key }}'"
                @click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="border-b-2 px-3 py-2 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $slot }}
    </div>
</div>

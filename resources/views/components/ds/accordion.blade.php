@props(['open' => null])

<div x-data="{ openItem: @js($open) }" class="divide-y divide-gray-100 rounded-2xl border border-gray-100 bg-white">
    {{ $slot }}
</div>

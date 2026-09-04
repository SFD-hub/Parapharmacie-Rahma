@props(['padding' => 'p-5'])

<div {{ $attributes->merge(['class' => "rounded-2xl border border-gray-100 bg-white {$padding} shadow-sm"]) }}>
    {{ $slot }}
</div>

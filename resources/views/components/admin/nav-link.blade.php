@props(['route', 'icon', 'activePattern' => null])

@php
    $patterns = collect(is_array($activePattern) ? $activePattern : [$activePattern ?? $route])
        ->map(fn ($pattern) => $pattern.'*')
        ->all();
    $active = request()->routeIs(...$patterns);
@endphp

<a
    href="{{ route($route) }}"
    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}"
>
    <x-icon :name="$icon" class="h-5 w-5" />
    {{ $slot }}
</a>

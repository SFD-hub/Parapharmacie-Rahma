@props(['column', 'label'])

@php
    $isActive = request('sort') === $column;
    $nextDirection = $isActive && request('direction') === 'asc' ? 'desc' : 'asc';
@endphp

<a
    href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection]) }}"
    class="inline-flex items-center gap-1 hover:text-gray-900"
>
    {{ $label }}
    @if ($isActive)
        <x-icon name="{{ request('direction') === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-primary-600" />
    @endif
</a>

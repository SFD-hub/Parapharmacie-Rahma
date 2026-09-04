@props(['title' => 'Aucun résultat', 'description' => null, 'icon' => 'search'])

<div class="flex flex-col items-center justify-center gap-3 py-16 text-center">
    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
        <x-icon :name="$icon" class="h-7 w-7" />
    </span>
    <p class="text-base font-semibold text-gray-900">{{ $title }}</p>
    @if ($description)
        <p class="max-w-sm text-sm text-gray-500">{{ $description }}</p>
    @endif
    {{ $slot ?? '' }}
</div>

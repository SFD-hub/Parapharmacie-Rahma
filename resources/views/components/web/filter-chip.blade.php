@props(['label', 'removeAction'])

<span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700">
    {{ $label }}
    <button type="button" wire:click="{{ $removeAction }}" class="text-gray-400 hover:text-gray-700" aria-label="Retirer ce filtre">
        <x-icon name="x-mark" class="h-3 w-3" />
    </button>
</span>

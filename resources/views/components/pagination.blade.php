@props(['paginator', 'livewire' => false])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-1 py-6" aria-label="Pagination">
        @if ($livewire)
            <button
                type="button"
                wire:click="previousPage"
                @if ($paginator->onFirstPage()) disabled @endif
                class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40"
            >
                <x-icon name="chevron-left" class="h-4 w-4" />
            </button>
        @else
            <a
                href="{{ $paginator->previousPageUrl() ?? '#' }}"
                class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 {{ $paginator->onFirstPage() ? 'pointer-events-none opacity-40' : 'hover:bg-gray-50' }}"
            >
                <x-icon name="chevron-left" class="h-4 w-4" />
            </a>
        @endif

        @for ($page = max(1, $paginator->currentPage() - 2); $page <= min($paginator->lastPage(), $paginator->currentPage() + 2); $page++)
            @if ($livewire)
                <button
                    type="button"
                    wire:click="gotoPage({{ $page }})"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium {{ $page === $paginator->currentPage() ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}"
                >
                    {{ $page }}
                </button>
            @else
                <a
                    href="{{ $paginator->url($page) }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium {{ $page === $paginator->currentPage() ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50' }}"
                >
                    {{ $page }}
                </a>
            @endif
        @endfor

        @if ($livewire)
            <button
                type="button"
                wire:click="nextPage"
                @if (! $paginator->hasMorePages()) disabled @endif
                class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40"
            >
                <x-icon name="chevron-right" class="h-4 w-4" />
            </button>
        @else
            <a
                href="{{ $paginator->nextPageUrl() ?? '#' }}"
                class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 text-gray-500 {{ ! $paginator->hasMorePages() ? 'pointer-events-none opacity-40' : 'hover:bg-gray-50' }}"
            >
                <x-icon name="chevron-right" class="h-4 w-4" />
            </a>
        @endif
    </nav>
@endif

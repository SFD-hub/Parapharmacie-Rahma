@props(['placeholder' => 'Rechercher...'])

<form method="GET" class="flex w-full max-w-sm items-center gap-2">
    @foreach (request()->except(['search', 'page']) as $key => $value)
        @foreach (Illuminate\Support\Arr::wrap($value) as $item)
            <input type="hidden" name="{{ $key }}{{ is_array($value) ? '[]' : '' }}" value="{{ $item }}">
        @endforeach
    @endforeach

    <div class="relative flex-1">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-lg border-gray-200 py-2 pl-9 pr-3 text-sm focus:border-primary-500 focus:ring-primary-500"
        />
        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
    </div>

    @if (request()->filled('search'))
        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-sm text-gray-500 hover:text-gray-700">
            Effacer
        </a>
    @endif
</form>

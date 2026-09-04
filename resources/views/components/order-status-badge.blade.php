@props(['status'])

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $status->color() }}">
    {{ $status->label() }}
</span>

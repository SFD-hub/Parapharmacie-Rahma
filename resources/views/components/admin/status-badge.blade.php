@props(['active', 'activeLabel' => 'Actif', 'inactiveLabel' => 'Inactif'])

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
    {{ $active ? $activeLabel : $inactiveLabel }}
</span>

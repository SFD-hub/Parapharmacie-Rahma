@props(['active'])

<div class="mb-6 flex gap-1 border-b border-gray-100">
    <a
        href="{{ route('admin.orders.pending') }}"
        class="border-b-2 px-4 py-2.5 text-sm font-semibold transition {{ $active === 'pending' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
    >
        À traiter
    </a>
    <a
        href="{{ route('admin.orders.history') }}"
        class="border-b-2 px-4 py-2.5 text-sm font-semibold transition {{ $active === 'history' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}"
    >
        Historique
    </a>
</div>

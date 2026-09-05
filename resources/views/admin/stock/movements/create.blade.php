<x-admin-layout title="Nouveau mouvement de stock" :back-url="route('admin.stock.movements.index')">
    <form method="POST" action="{{ route('admin.stock.movements.store') }}">
        @csrf

        <x-admin.stock.movements.form :products="$products" :types="$types" />
    </form>
</x-admin-layout>

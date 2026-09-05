<x-admin-layout title="Nouveau cadeau" :back-url="route('admin.loyalty.index')">
    <form method="POST" action="{{ route('admin.loyalty.gifts.store') }}">
        @csrf
        <x-admin.loyalty.gifts.form :products="$products" />
    </form>
</x-admin-layout>

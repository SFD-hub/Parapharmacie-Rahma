<x-admin-layout title="Nouveau cadeau">
    <form method="POST" action="{{ route('admin.loyalty.gifts.store') }}">
        @csrf
        <x-admin.loyalty.gifts.form :products="$products" />
    </form>
</x-admin-layout>

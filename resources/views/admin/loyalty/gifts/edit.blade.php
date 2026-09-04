<x-admin-layout :title="'Modifier '.$gift->product->name">
    <form method="POST" action="{{ route('admin.loyalty.gifts.update', $gift) }}">
        @csrf
        @method('PUT')
        <x-admin.loyalty.gifts.form :gift="$gift" :products="$products" />
    </form>
</x-admin-layout>

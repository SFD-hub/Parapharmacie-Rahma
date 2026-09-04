<x-admin-layout title="Nouveau produit">
    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        <x-admin.products.form :categories="$categories" :brands="$brands" />
    </form>
</x-admin-layout>

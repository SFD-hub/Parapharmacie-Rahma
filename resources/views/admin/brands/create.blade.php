<x-admin-layout title="Nouvelle marque" :back-url="route('admin.brands.index')">
    <form method="POST" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin.brands.form />
    </form>
</x-admin-layout>

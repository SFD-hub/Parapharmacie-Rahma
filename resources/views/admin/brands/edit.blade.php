<x-admin-layout :title="'Modifier '.$brand->name" :back-url="route('admin.brands.index')">
    <form method="POST" action="{{ route('admin.brands.update', $brand) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-admin.brands.form :brand="$brand" />
    </form>
</x-admin-layout>

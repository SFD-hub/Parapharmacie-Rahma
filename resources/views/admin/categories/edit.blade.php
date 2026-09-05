<x-admin-layout :title="'Modifier '.$category->name" :back-url="route('admin.categories.index')">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-admin.categories.form :category="$category" :parents="$parents" />
    </form>
</x-admin-layout>

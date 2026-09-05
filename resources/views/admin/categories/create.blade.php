<x-admin-layout title="Nouvelle catégorie" :back-url="route('admin.categories.index')">
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin.categories.form :parents="$parents" />
    </form>
</x-admin-layout>

<x-admin-layout title="Nouvelle catégorie">
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin.categories.form :parents="$parents" />
    </form>
</x-admin-layout>

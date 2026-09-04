<x-admin-layout title="Nouvel article">
    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
        @csrf
        <x-admin.articles.form :categories="$categories" />
    </form>
</x-admin-layout>

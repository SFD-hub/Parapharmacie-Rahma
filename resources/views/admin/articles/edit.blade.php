<x-admin-layout :title="'Modifier '.$article->title" :back-url="route('admin.articles.index')">
    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-admin.articles.form :article="$article" :categories="$categories" />
    </form>
</x-admin-layout>

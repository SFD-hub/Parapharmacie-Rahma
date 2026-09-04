<x-web-layout :title="$page->meta_title ?? $page->title" :description="$page->meta_description">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">{{ $page->title }}</h1>

        <div class="mt-6 whitespace-pre-line text-sm leading-relaxed text-gray-700">
            {{ $page->content }}
        </div>
    </div>
</x-web-layout>

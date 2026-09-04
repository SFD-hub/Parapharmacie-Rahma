<x-web-layout :title="$title">
    <section class="mx-auto flex max-w-xl flex-col items-center px-4 py-24 text-center sm:px-6">
        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-blush-100 text-primary-600">
            <x-icon name="sparkles" class="h-8 w-8" />
        </span>
        <h1 class="mt-6 text-2xl font-bold text-gray-900">{{ $title }}</h1>
        <p class="mt-2 text-sm text-gray-500">
            Cette page est en cours de construction et sera bientôt disponible.
        </p>
        <a
            href="{{ route('home') }}"
            class="mt-6 inline-flex items-center gap-2 rounded-full bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="chevron-left" class="h-4 w-4" />
            Retour à l'accueil
        </a>
    </section>
</x-web-layout>

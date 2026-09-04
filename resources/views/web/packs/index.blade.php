<x-web-layout title="Packs" description="Découvrez nos packs de produits à prix avantageux.">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900">Nos packs</h1>
        <p class="mt-1 text-sm text-gray-600">Des sélections de produits pensées pour vous, à prix réduit.</p>

        @if ($packs->isEmpty())
            <div class="mt-8">
                <x-empty-state
                    title="Aucun pack disponible"
                    description="Revenez bientôt pour découvrir nos offres groupées."
                    icon="box"
                />
            </div>
        @else
            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($packs as $pack)
                    <x-web.pack-card :pack="$pack" />
                @endforeach
            </div>

            <x-pagination :paginator="$packs" />
        @endif
    </div>
</x-web-layout>

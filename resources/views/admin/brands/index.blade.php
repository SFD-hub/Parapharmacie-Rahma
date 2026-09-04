<x-admin-layout title="Marques">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-admin.search-bar placeholder="Rechercher une marque..." />
        <a
            href="{{ route('admin.brands.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouvelle marque
        </a>
    </div>

    @if ($brands->isEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white">
            <x-empty-state
                title="Aucune marque"
                description="Créez votre première marque pour commencer à organiser le catalogue."
                icon="sparkles"
            />
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($brands as $brand)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                    <div class="flex h-32 items-center justify-center bg-gray-50 p-4">
                        @if ($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="h-full w-full object-contain">
                        @else
                            <x-icon name="sparkles" class="h-8 w-8 text-gray-300" />
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $brand->name }}</p>
                        <div class="mt-2 flex items-center justify-center gap-2">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <x-admin.delete-button :action="route('admin.brands.destroy', $brand)" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$brands" />
</x-admin-layout>

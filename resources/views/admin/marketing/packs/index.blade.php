<x-admin-layout title="Packs">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-admin.search-bar placeholder="Rechercher un pack..." />
        <a
            href="{{ route('admin.marketing.packs.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouveau pack
        </a>
    </div>

    @if ($packs->isEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white">
            <x-empty-state
                title="Aucun pack"
                description="Créez votre premier pack de produits."
                icon="gift"
            />
        </div>
    @else
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($packs as $pack)
                <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
                    <div class="flex h-32 items-center justify-center bg-gray-50 p-4">
                        @if ($pack->image)
                            <img src="{{ $pack->image }}" alt="{{ $pack->name }}" class="h-full w-full object-contain">
                        @else
                            <x-icon name="gift" class="h-8 w-8 text-gray-300" />
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $pack->name }}</p>
                        <div class="mt-2 flex items-center justify-center gap-2">
                            <a href="{{ route('admin.marketing.packs.edit', $pack) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <x-admin.delete-button :action="route('admin.marketing.packs.destroy', $pack)" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <x-pagination :paginator="$packs" />
</x-admin-layout>

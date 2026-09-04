<x-admin-layout title="Catégories">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <x-admin.search-bar placeholder="Rechercher une catégorie..." />
        <a
            href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700"
        >
            <x-icon name="plus" class="h-4 w-4" />
            Nouvelle catégorie
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3"><x-admin.sortable-th column="name" label="Nom" /></th>
                        <th class="px-4 py-3">Parent</th>
                        <th class="px-4 py-3">Produits</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="position" label="Position" /></th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($category->image)
                                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-8 w-8 rounded-lg object-cover">
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $category->parent?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $category->products_count }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $category->position }}</td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit">
                                        <x-admin.status-badge :active="$category->is_active" />
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="p-1.5 text-gray-400 hover:text-primary-600" aria-label="Modifier">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <x-admin.delete-button :action="route('admin.categories.destroy', $category)" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state
                                    title="Aucune catégorie"
                                    description="Créez votre première catégorie pour commencer à organiser le catalogue."
                                    icon="tag"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$categories" />
</x-admin-layout>

<x-admin-layout title="Avis clients" :search="false">
    <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
        <select name="status" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Tous statuts</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <button type="submit" class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
            Filtrer
        </button>

        @if (request()->filled('status'))
            <a href="{{ route('admin.reviews.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Réinitialiser</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Produit</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="rating" label="Note" /></th>
                        <th class="px-4 py-3">Commentaire</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3"><x-admin.sortable-th column="created_at" label="Date" /></th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($reviews as $review)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                @if ($review->product)
                                    <a href="{{ route('products.show', $review->product) }}" target="_blank" class="hover:text-primary-600">
                                        {{ $review->product->name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $review->name }}</td>
                            <td class="px-4 py-3">
                                <x-web.star-rating :rating="$review->rating" />
                            </td>
                            <td class="px-4 py-3 max-w-xs truncate text-gray-500">{{ $review->comment ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $review->status->color() }}">
                                    {{ $review->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $review->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($review->status !== \App\Enums\ReviewStatus::Approved)
                                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                                Approuver
                                            </button>
                                        </form>
                                    @endif
                                    @if ($review->status !== \App\Enums\ReviewStatus::Rejected)
                                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                Rejeter
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Supprimer définitivement cet avis ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-gray-100 px-2.5 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-200">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state
                                    title="Aucun avis"
                                    description="Ajustez vos filtres ou revenez plus tard."
                                    icon="star-outline"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-pagination :paginator="$reviews" />
</x-admin-layout>

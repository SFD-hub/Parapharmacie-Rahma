<x-admin-layout title="Notifications">
    @if ($notifications->isEmpty())
        <x-empty-state
            title="Aucune notification"
            description="Vous serez informé ici des nouvelles commandes, avis et alertes de stock."
            icon="bell"
        />
    @else
        <div class="space-y-3">
            @foreach ($notifications as $notification)
                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $notification->title }}</p>
                        <span class="shrink-0 text-xs text-gray-400">{{ $notification->created_at->translatedFormat('d M Y à H:i') }}</span>
                    </div>
                    @if ($notification->message)
                        <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <x-pagination :paginator="$notifications" />
    @endif
</x-admin-layout>

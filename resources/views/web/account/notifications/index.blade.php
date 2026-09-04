<x-web-layout title="Notifications">
    <div class="mx-auto max-w-2xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Notifications</h1>

        @if ($notifications->isEmpty())
            <x-empty-state
                title="Aucune notification"
                description="Vous serez informé ici de vos points de fidélité, avis et commandes."
                icon="bell"
            />
        @else
            <div class="mt-6 space-y-3">
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
    </div>
</x-web-layout>

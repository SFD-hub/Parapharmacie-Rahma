<div wire:poll.5s>
    <a href="{{ route('admin.support.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
        <x-icon name="chevron-left" class="h-4 w-4" /> Messages clients
    </a>

    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-900">{{ $conversation->displayName() }}</h1>
        @if ($conversation->status === 'open')
            <button type="button" wire:click="close" class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-200">
                Marquer comme résolu
            </button>
        @else
            <button type="button" wire:click="reopen" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-600 hover:bg-primary-100">
                Rouvrir
            </button>
        @endif
    </div>

    <div class="min-h-[50vh] space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
        @forelse ($messages as $chatMessage)
            <div class="flex {{ $chatMessage->isFromAdmin() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-2.5 text-sm {{ $chatMessage->isFromAdmin() ? 'bg-primary-600 text-white' : 'bg-white text-gray-700' }}">
                    <p class="leading-relaxed">{{ $chatMessage->content }}</p>
                    <p class="mt-1 text-[11px] {{ $chatMessage->isFromAdmin() ? 'text-primary-100' : 'text-gray-400' }}">
                        {{ $chatMessage->isFromAdmin() ? ($chatMessage->admin->name ?? 'Vous') : $conversation->displayName() }}
                        · {{ $chatMessage->created_at->translatedFormat('d M à H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <x-empty-state
                title="Aucun message"
                description="Ce fil ne contient aucun message pour le moment."
                icon="chat"
            />
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="mt-4 flex items-center gap-2">
        <input
            type="text"
            wire:model="content"
            placeholder="Écrire une réponse..."
            class="w-full rounded-full border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
        >
        <button
            type="submit"
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white hover:bg-primary-700"
            aria-label="Envoyer"
        >
            <x-icon name="arrow-right" class="h-5 w-5" />
        </button>
    </form>
    @error('content')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

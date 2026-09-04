<div>
    <div class="mb-4 flex flex-wrap items-center gap-2">
        <select wire:model.live="status" class="rounded-lg border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="">Tous statuts</option>
            <option value="open">Ouverts</option>
            <option value="closed">Résolus</option>
        </select>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white" wire:poll.5s>
        <div class="divide-y divide-gray-100">
            @forelse ($conversations as $conversation)
                <a href="{{ route('admin.support.show', $conversation) }}" class="flex items-center gap-3 p-4 hover:bg-gray-50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blush-100 text-sm font-semibold text-primary-600">
                        {{ Str::of($conversation->displayName())->substr(0, 1)->upper() }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $conversation->displayName() }}</p>
                            @if ($conversation->unread_count > 0)
                                <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-primary-600 px-1 text-[10px] font-semibold text-white">
                                    {{ $conversation->unread_count }}
                                </span>
                            @endif
                            @if ($conversation->status === 'closed')
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-500">Résolu</span>
                            @endif
                        </div>
                        <p class="truncate text-xs text-gray-400">{{ $conversation->last_message_at?->translatedFormat('d M à H:i') ?? '—' }}</p>
                    </div>
                    <x-icon name="chevron-right" class="h-4 w-4 shrink-0 text-gray-400" />
                </a>
            @empty
                <x-empty-state
                    title="Aucun message"
                    description="Les messages envoyés par vos clients apparaîtront ici."
                    icon="chat"
                />
            @endforelse
        </div>
    </div>

    <x-pagination :paginator="$conversations" :livewire="true" />
</div>

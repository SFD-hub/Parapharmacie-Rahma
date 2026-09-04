<div
    class="mx-auto flex h-[calc(100vh-11rem)] max-w-2xl flex-col px-4 py-6 sm:px-6 lg:px-8"
    wire:poll.5s
    x-data="{
        scrollToBottom() {
            this.$nextTick(() => {
                this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
            });
        },
    }"
    x-init="scrollToBottom(); Livewire.hook('morphed', () => scrollToBottom());"
>
    <div class="flex shrink-0 items-center gap-3">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white">
            <x-icon name="chat" class="h-5 w-5" />
        </span>
        <div>
            <h1 class="text-lg font-bold text-gray-900 sm:text-xl">Discutez avec nous</h1>
            <p class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Réponse sous 24h en moyenne
            </p>
        </div>
    </div>

    <div
        x-ref="messages"
        class="mt-4 flex-1 space-y-3 overflow-y-auto rounded-2xl border border-gray-100 bg-gray-50 p-4"
    >
        @forelse ($messages as $chatMessage)
            <div class="flex items-end gap-2 {{ $chatMessage->isFromAdmin() ? 'justify-start' : 'flex-row-reverse justify-start' }}">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[11px] font-semibold {{ $chatMessage->isFromAdmin() ? 'bg-primary-100 text-primary-600' : 'bg-blush-100 text-primary-600' }}">
                    {{ $chatMessage->isFromAdmin() ? 'R' : Str::of($conversation->displayName())->substr(0, 1)->upper() }}
                </span>
                <div class="max-w-[75%] rounded-2xl px-4 py-2.5 text-sm {{ $chatMessage->isFromAdmin() ? 'rounded-bl-md bg-white text-gray-700' : 'rounded-br-md bg-primary-600 text-white' }}">
                    <p class="leading-relaxed">{{ $chatMessage->content }}</p>
                    <p class="mt-1 text-[11px] {{ $chatMessage->isFromAdmin() ? 'text-gray-400' : 'text-primary-100' }}">
                        {{ $chatMessage->created_at->translatedFormat('d M à H:i') }}
                    </p>
                </div>
            </div>
        @empty
            <div class="flex h-full flex-col items-center justify-center gap-4 py-10 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-blush-100 text-primary-600">
                    <x-icon name="chat" class="h-7 w-7" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Comment pouvons-nous vous aider ?</p>
                    <p class="mt-1 max-w-xs text-sm text-gray-500">Écrivez votre message ci-dessous, ou choisissez un sujet pour commencer.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-2">
                    @foreach (['🚚 Livraison' => 'Bonjour, j\'ai une question sur la livraison.', '📦 Ma commande' => 'Bonjour, j\'ai une question sur ma commande.', '💊 Un produit' => 'Bonjour, j\'ai une question sur un produit.', '💡 Conseil' => 'Bonjour, j\'aimerais un conseil.'] as $label => $prefill)
                        <button
                            type="button"
                            wire:click="$set('content', {{ Js::from($prefill) }})"
                            class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-primary-300 hover:text-primary-600"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="mt-4 shrink-0 space-y-2">
        @unless (auth()->check() || $conversation->guest_name)
            <div>
                <input
                    type="text"
                    wire:model="name"
                    placeholder="Votre nom"
                    class="w-full rounded-full border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endunless

        <div class="flex items-center gap-2">
            <input
                type="text"
                wire:model="content"
                placeholder="Écrivez votre message..."
                class="w-full rounded-full border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500"
            >
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendMessage"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-60"
                aria-label="Envoyer"
            >
                <span wire:loading.remove wire:target="sendMessage">
                    <x-icon name="arrow-right" class="h-5 w-5" />
                </span>
                <span wire:loading wire:target="sendMessage" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
            </button>
        </div>
        @error('content')
            <p class="text-xs text-red-600">{{ $message }}</p>
        @enderror
    </form>
</div>

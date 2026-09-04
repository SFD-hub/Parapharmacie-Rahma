@unless (request()->routeIs('support.chat'))
    <a
        href="{{ route('support.chat') }}"
        class="fixed bottom-20 right-4 z-30 flex h-14 w-14 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition hover:scale-105 hover:bg-primary-700 lg:bottom-6"
        aria-label="Discuter avec nous"
    >
        <x-icon name="chat" class="h-6 w-6" />
        @if (($supportUnreadCount ?? 0) > 0)
            <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-semibold text-white">
                {{ $supportUnreadCount }}
            </span>
        @endif
    </a>
@endunless

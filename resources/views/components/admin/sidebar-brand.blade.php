<div class="flex h-16 items-center justify-between gap-2 border-b border-white/10 px-4">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="h-8 w-8 text-primary-400" fill="none">
            <g fill="currentColor">
                <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(0 20 20)" />
                <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(72 20 20)" />
                <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(144 20 20)" />
                <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(216 20 20)" />
                <ellipse cx="20" cy="9" rx="5" ry="9" transform="rotate(288 20 20)" />
            </g>
            <circle cx="20" cy="20" r="3.5" fill="white" />
        </svg>
        <span class="leading-tight">
            <span class="block text-base font-bold tracking-wide text-white">RAHMANE</span>
            <span class="block text-[9px] font-medium tracking-[0.2em] text-gray-400">ADMINISTRATION</span>
        </span>
    </a>

    {{ $slot ?? '' }}
</div>

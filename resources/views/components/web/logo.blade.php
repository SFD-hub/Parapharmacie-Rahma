@props(['iconClass' => 'h-8 w-8', 'imgClass' => 'h-14 lg:h-20'])

@php
    // Cache-buster : si le logo est remplacé plus tard (même nom de
    // fichier), les navigateurs qui l'avaient en cache voient bien la
    // nouvelle version au lieu de l'ancienne.
    $logoVersion = ($logoUrl ?? null) ? @filemtime(public_path(parse_url($logoUrl, PHP_URL_PATH))) : null;
@endphp

<a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2">
    @if ($logoUrl ?? null)
        <img src="{{ $logoUrl }}{{ $logoVersion ? '?v='.$logoVersion : '' }}" alt="{{ $shopName ?? 'Logo' }}" class="{{ $imgClass }} w-auto object-contain">
    @else
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="{{ $iconClass }} text-primary-600" fill="none">
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
            <span class="block text-base font-bold tracking-wide text-primary-600 sm:text-lg">RAHMANE</span>
            <span class="block text-[9px] font-medium tracking-[0.2em] text-primary-400 sm:text-[10px]">PARAPHARMACIE</span>
        </span>
    @endif
</a>

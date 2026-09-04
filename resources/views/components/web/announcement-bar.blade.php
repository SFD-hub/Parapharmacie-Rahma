@php
    $messages = [
        ['icon' => '🚚', 'text' => 'Livraison en moins de 24h partout à Thiès'],
        ['icon' => '🎁', 'text' => 'Livraison gratuite dès 50 000 FCFA d\'achat'],
        ['icon' => '💳', 'text' => 'Paiement à la livraison disponible'],
        ['icon' => '✅', 'text' => 'Produits 100% authentiques et garantis'],
    ];
@endphp

<div class="overflow-hidden bg-primary-600 py-2.5">
    <div class="flex w-max animate-marquee whitespace-nowrap">
        @for ($i = 0; $i < 2; $i++)
            <div class="flex items-center" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
                @foreach ($messages as $message)
                    <span class="flex items-center gap-2 px-32 text-xs font-semibold tracking-wide text-white sm:text-sm">
                        <span class="shrink-0">{{ $message['icon'] }}</span>
                        {{ $message['text'] }}
                    </span>
                @endforeach
            </div>
        @endfor
    </div>
</div>

<style>
    @keyframes marquee {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }
    .animate-marquee {
        animation: marquee 25s linear infinite;
    }
</style>

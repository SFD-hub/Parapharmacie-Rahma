<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm transition duration-300 hover:shadow-md">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-bold text-gray-900">Évolution du chiffre d'affaires</h2>
            <div class="mt-1 flex flex-wrap items-center gap-2">
                <p class="text-2xl font-bold text-gray-900">{{ number_format($total, 0, ',', ' ') }} FCFA</p>

                @if ($changePercent !== null)
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold {{ $changePercent >= 0 ? 'bg-success-50 text-success-700' : 'bg-danger-50 text-danger-700' }}">
                        {{ $changePercent >= 0 ? '+' : '' }}{{ number_format($changePercent, 1, ',', ' ') }}%
                    </span>
                    <span class="text-xs text-gray-400">par rapport à {{ $comparisonLabel }}</span>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-1">
            @foreach ($this->periods() as $value => $label)
                <button
                    type="button"
                    wire:click="$set('period', '{{ $value }}')"
                    class="rounded-full px-3 py-1.5 text-xs font-medium transition duration-200 {{ $period === $value ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div wire:loading.class="opacity-50" class="mt-6 transition-opacity">
        @if (array_sum($series['data']) <= 0)
            <div class="flex flex-col items-center gap-3 rounded-2xl bg-gray-50/60 py-14 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-300 shadow-sm">
                    <x-icon name="chart-bar" class="h-7 w-7" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Aucune vente sur cette période</p>
                    <p class="mt-1 text-xs text-gray-400">Les prochaines commandes apparaîtront ici automatiquement.</p>
                </div>
            </div>
        @else
            @php
                $data = $series['data'];
                $labels = $series['labels'];
                $count = count($data);

                $rawMax = max($data ?: [0]);
                $magnitude = $rawMax > 0 ? 10 ** floor(log10($rawMax)) : 1;
                $niceMax = max(1, ceil($rawMax / $magnitude) * $magnitude);
                if ($niceMax <= $rawMax) {
                    $niceMax += $magnitude;
                }

                $points = [];
                foreach ($data as $i => $value) {
                    $x = $count > 1 ? ($i / ($count - 1)) * 100 : 50;
                    $y = 95 - ($niceMax > 0 ? ($value / $niceMax) * 90 : 0);
                    $points[] = [$x, $y];
                }

                $linePath = collect($points)->map(fn ($p, $i) => ($i === 0 ? 'M' : 'L').round($p[0], 2).','.round($p[1], 2))->implode(' ');
                $areaPath = $linePath." L {$points[array_key_last($points)][0]},95 L {$points[0][0]},95 Z";

                $tickCount = 5;
                $yTicks = collect(range(0, $tickCount - 1))->map(fn ($i) => $niceMax * (($tickCount - 1 - $i) / ($tickCount - 1)));

                $labelStep = $count > 10 ? (int) ceil($count / 8) : 1;
            @endphp

            <div class="flex gap-3">
                <div class="flex h-64 flex-col justify-between py-1 text-right text-[11px] text-gray-400">
                    @foreach ($yTicks as $tick)
                        <span>{{ $tick >= 1000 ? number_format($tick / 1000, 0, ',', ' ').'K' : number_format($tick, 0, ',', ' ') }}</span>
                    @endforeach
                </div>

                <div class="min-w-0 flex-1">
                    <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-64 w-full overflow-visible">
                        <defs>
                            <linearGradient id="revenueAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#db2777" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#db2777" stop-opacity="0" />
                            </linearGradient>
                        </defs>

                        @foreach ($yTicks as $i => $tick)
                            <line x1="0" x2="100" y1="{{ 5 + ($i * 22.5) }}" y2="{{ 5 + ($i * 22.5) }}" stroke="#f3f4f6" stroke-width="0.5" vector-effect="non-scaling-stroke" />
                        @endforeach

                        <path d="{{ $areaPath }}" fill="url(#revenueAreaGradient)" />
                        <path d="{{ $linePath }}" fill="none" stroke="#db2777" stroke-width="1.5" vector-effect="non-scaling-stroke" stroke-linejoin="round" stroke-linecap="round" />

                        @if ($count <= 15)
                            @foreach ($points as $p)
                                <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="1.4" fill="#db2777" stroke="white" stroke-width="0.8" vector-effect="non-scaling-stroke" />
                            @endforeach
                        @endif
                    </svg>

                    <div class="mt-1 flex">
                        @foreach ($labels as $i => $label)
                            <span class="flex-1 truncate text-center text-[10px] text-gray-400 {{ $i % $labelStep !== 0 ? 'invisible' : '' }}">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

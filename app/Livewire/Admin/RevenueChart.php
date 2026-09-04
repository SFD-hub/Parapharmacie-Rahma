<?php

namespace App\Livewire\Admin;

use App\Services\DashboardService;
use Illuminate\Support\Carbon;
use Livewire\Component;

/**
 * Small embedded island (like MiniCart) showing the revenue evolution with
 * a period filter. Updates automatically on filter change — no page reload.
 */
class RevenueChart extends Component
{
    public string $period = 'month';

    public ?string $from = null;

    public ?string $to = null;

    /**
     * @return array<string, string>
     */
    public function periods(): array
    {
        return [
            'today' => "Aujourd'hui",
            'days7' => '7 jours',
            'days30' => '30 jours',
            'month' => 'Ce mois',
            'year' => 'Cette année',
        ];
    }

    public function render(DashboardService $dashboard)
    {
        $from = $this->period === 'custom' ? $this->parseDate($this->from) : null;
        $to = $this->period === 'custom' ? $this->parseDate($this->to) : null;

        $series = $dashboard->revenueSeries($this->period, $from, $to);

        return view('livewire.admin.revenue-chart', [
            'series' => $series,
            'total' => array_sum($series['data']),
            'changePercent' => $dashboard->revenueSeriesChangePercent($this->period, $from, $to),
            'comparisonLabel' => $dashboard->periodComparisonLabel($this->period),
        ]);
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}

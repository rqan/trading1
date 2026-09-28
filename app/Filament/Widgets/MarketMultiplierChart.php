<?php

namespace App\Filament\Widgets;

use App\Models\MarketMultiplierHistory;
use App\Models\Casting;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MarketMultiplierChart extends ChartWidget
{
    protected static ?string $heading = 'Market Trend (Highest Volume Casting)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $topCastingId = MarketMultiplierHistory::select('casting_id')
            ->groupBy('casting_id')
            ->orderByRaw('COUNT(*) DESC')
            ->value('casting_id');

        if (!$topCastingId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $casting = Casting::find($topCastingId);
        self::$heading = "Trend for: " . $casting->name;

        $histories = MarketMultiplierHistory::where('casting_id', $topCastingId)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at')
            ->get();

        $data = $histories->pluck('multiplier')->toArray();
        $labels = $histories->pluck('recorded_at')->map(fn (Carbon $date) => $date->format('M d H:i'))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Market Multiplier',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

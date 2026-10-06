<?php

namespace App\Livewire;

use App\Models\Casting;
use App\Models\Listing;
use App\Models\MarketMultiplierHistory;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CastingDetail extends Component
{
    public Casting $casting;

    public function mount(Casting $casting): void
    {
        $this->casting = $casting->load(['marketSignal', 'collectionItems']);
    }

    public function getTitle(): string
    {
        return "Nilai {$this->casting->name}";
    }

    /**
     * Data chart 30 hari terakhir untuk Chart.js di view.
     * Aggregate satu titik per hari (rata-rata) agar chart bersih.
     */
    public function getChartData(): array
    {
        $histories = MarketMultiplierHistory::where('casting_id', $this->casting->id)
            ->where('recorded_at', '>=', now()->subDays(30))
            ->orderBy('recorded_at')
            ->get()
            ->groupBy(fn ($h) => Carbon::parse($h->recorded_at)->format('Y-m-d'))
            ->map(fn ($group) => round($group->avg('multiplier'), 4));

        // Jika tidak ada data, kembalikan satu titik (multiplier saat ini)
        if ($histories->isEmpty()) {
            return [
                'labels' => [now()->format('d M')],
                'data' => [$this->casting->marketSignal?->current_multiplier ?? 1.0],
            ];
        }

        return [
            'labels' => $histories->keys()
                ->map(fn ($d) => Carbon::parse($d)->translatedFormat('d M'))
                ->toArray(),
            'data' => $histories->values()->map(fn ($m) => round(
                (float) $this->casting->base_value * (float) $m
            ))->toArray(),
        ];
    }

    /**
     * Listing aktif untuk casting ini (lewat collection items).
     */
    public function getActiveListings()
    {
        return Listing::with(['user', 'offeredItems.casting', 'requestedItems.casting'])
            ->where(function ($query) {
                $query->whereHas('offeredItems', fn ($q) => $q->where('casting_id', $this->casting->id))
                    ->orWhereHas('requestedItems', fn ($q) => $q->where('casting_id', $this->casting->id));
            })
            ->where('status', 'active')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $signal = $this->casting->marketSignal;
        $multiplier = (float) ($signal?->current_multiplier ?? 1.0);
        $baseValue = (float) $this->casting->base_value;

        $currentPrice = round($baseValue * $multiplier);
        $range = $this->casting->getPriceRange30d();
        $trend = $this->casting->trend;
        $chartData = $this->getChartData();
        $listings = $this->getActiveListings();

        return view('livewire.casting-detail', compact(
            'signal', 'multiplier', 'currentPrice', 'range', 'trend', 'chartData', 'listings'
        ));
    }
}

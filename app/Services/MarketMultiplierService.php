<?php

namespace App\Services;

use App\Models\Casting;
use App\Models\MarketSignal;
use App\Models\MarketMultiplierHistory;
use Illuminate\Support\Facades\Log;

class MarketMultiplierService
{
    public function getSensitivity(string $rarityTier): float
    {
        return match (strtolower($rarityTier)) {
            'sth' => 0.5,
            'rlc' => 0.6,
            'th' => 0.4,
            'premium' => 0.35,
            'mainline' => 0.25,
            default => 0.25,
        };
    }

    public function recalculateAllTargetMultipliers()
    {
        $castings = Casting::all();

        foreach ($castings as $casting) {
            $signal = MarketSignal::firstOrCreate(
                ['casting_id' => $casting->id],
                [
                    'active_listing_count' => 0,
                    'wishlist_count' => 0,
                    'current_multiplier' => 1.0,
                    'target_multiplier' => 1.0,
                ]
            );

            // Fetch actual counts
            $wishlists = \App\Models\Wishlist::where('casting_id', $casting->id)->count();
            $listings = \App\Models\Listing::where('casting_id', $casting->id)
                ->where('type', 'for_trade')
                ->where('status', 'active')
                ->count();

            // Including collection items listed for trade where casting is inferred
            $collectionListings = \App\Models\Listing::where('type', 'for_trade')
                ->where('status', 'active')
                ->whereHas('collectionItem', function($q) use ($casting) {
                    $q->where('casting_id', $casting->id);
                })
                ->count();

            $totalActiveListings = $listings + $collectionListings;

            $signal->wishlist_count = $wishlists;
            $signal->active_listing_count = $totalActiveListings;

            // Calculate Target Multiplier
            $sensitivity = $this->getSensitivity($casting->rarity_tier);
            $ratio = ($wishlists + 1) / ($totalActiveListings + 1);
            $rawMultiplier = 1 + (log($ratio) * $sensitivity);

            // Clamp between 0.5 and 3.0
            $target = max(0.5, min(3.0, $rawMultiplier));
            
            $signal->target_multiplier = $target;
            $signal->save();
        }
    }

    public function applyHourlySmoothing()
    {
        $signals = MarketSignal::all();
        $now = now();

        foreach ($signals as $signal) {
            $current = (float) $signal->current_multiplier;
            $target = (float) $signal->target_multiplier;

            // new = current + (target - current) * 0.2
            $new = $current + (($target - $current) * 0.2);
            
            // Anomaly Detection: Capping daily movement max 10%
            $startOfDayMultiplier = MarketMultiplierHistory::where('casting_id', $signal->casting_id)
                ->where('recorded_at', '>=', $now->copy()->startOfDay())
                ->orderBy('recorded_at', 'asc')
                ->value('multiplier') ?? $current;

            $maxAllowed = $startOfDayMultiplier * 1.10;
            $minAllowed = $startOfDayMultiplier * 0.90;

            $new = max($minAllowed, min($maxAllowed, $new));
            
            // Re-clamp just in case
            $new = max(0.5, min(3.0, $new));

            $signal->current_multiplier = $new;
            $signal->last_calculated_at = $now;
            $signal->save();

            // Save history
            MarketMultiplierHistory::create([
                'casting_id' => $signal->casting_id,
                'multiplier' => $new,
                'recorded_at' => $now,
            ]);
        }
    }

    public function applyMicroAdjustment(int $castingId, string $event, \App\Models\User $user = null)
    {
        // Anomaly Detection: Ignore signals from users < 7 days old or trust score < 60
        if ($user) {
            if ($user->created_at && $user->created_at->diffInDays(now()) < 7) {
                return;
            }
            if ($user->trust_score < 60) {
                return;
            }
        }

        $signal = MarketSignal::firstOrCreate(
            ['casting_id' => $castingId],
            ['current_multiplier' => 1.0, 'target_multiplier' => 1.0]
        );

        $adjustment = 0.0;
        if ($event === 'wishlist_added') {
            $adjustment = 0.005; // +0.5%
        } elseif ($event === 'trade_completed') {
            $adjustment = 0.01; // +1%
        } elseif ($event === 'listing_added') {
            $adjustment = -0.003; // -0.3%
        }

        // Apply adjustment to target multiplier
        $newTarget = $signal->target_multiplier * (1 + $adjustment);
        $signal->target_multiplier = max(0.5, min(3.0, $newTarget));
        $signal->save();
    }
}

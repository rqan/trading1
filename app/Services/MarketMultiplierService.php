<?php

namespace App\Services;

use App\Models\Casting;
use App\Models\Listing;
use App\Models\MarketMultiplierHistory;
use App\Models\MarketSignal;
use App\Models\User;
use App\Models\Wishlist;

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
            $wishlists = Wishlist::where('casting_id', $casting->id)->count();
            // Count active listings offering this casting
            $totalActiveListings = Listing::where('status', 'active')
                ->whereHas('offeredItems', function ($q) use ($casting) {
                    $q->where('casting_id', $casting->id);
                })
                ->count();

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
        $smoothingFactor = config('wflf.multipliers.smoothing_factor', 0.2);
        $dailyCap = config('wflf.multipliers.daily_cap', 0.10);

        foreach ($signals as $signal) {
            $current = (float) $signal->current_multiplier;
            $target = (float) $signal->target_multiplier;

            $new = $current + (($target - $current) * $smoothingFactor);

            $startOfDayMultiplier = MarketMultiplierHistory::where('casting_id', $signal->casting_id)
                ->where('recorded_at', '>=', $now->copy()->startOfDay())
                ->orderBy('recorded_at', 'asc')
                ->value('multiplier') ?? $current;

            $maxAllowed = $startOfDayMultiplier * (1 + $dailyCap);
            $minAllowed = $startOfDayMultiplier * (1 - $dailyCap);

            $new = max($minAllowed, min($maxAllowed, $new));

            $new = max(config('wflf.multipliers.min', 0.5), min(config('wflf.multipliers.max', 3.0), $new));

            $signal->current_multiplier = $new;
            $signal->last_calculated_at = $now;
            $signal->save();

            MarketMultiplierHistory::create([
                'casting_id' => $signal->casting_id,
                'multiplier' => $new,
                'recorded_at' => $now,
            ]);
        }
    }

    public function applyMicroAdjustment(int $castingId, string $event, ?User $user = null)
    {
        if ($user) {
            if ($user->created_at && $user->created_at->diffInDays(now()) < 7) {
                return;
            }
            if ($user->trust_score < config('wflf.trust_score.min_required', 60)) {
                return;
            }
        }

        $signal = MarketSignal::firstOrCreate(
            ['casting_id' => $castingId],
            ['current_multiplier' => 1.0, 'target_multiplier' => 1.0]
        );

        $adjustments = config('wflf.micro_adjustments', []);
        $adjustment = $adjustments[$event] ?? 0.0;

        $newTarget = $signal->target_multiplier * (1 + $adjustment);
        $signal->target_multiplier = max(config('wflf.multipliers.min', 0.5), min(config('wflf.multipliers.max', 3.0), $newTarget));
        $signal->save();
    }
}

<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\User;
use App\Models\Rating;

class TrustScoreService
{
    public function recalculate(User $user): void
    {
        $baseScore = config('wflf.trust_score.base', 50);

        if ($user->name && $user->email) {
            $baseScore += config('wflf.trust_score.profile_bonus', 10);
        }

        $completedTrades = Trade::where(function($q) use ($user) {
            $q->where('initiator_id', $user->id)->orWhere('receiver_id', $user->id);
        })->where('status', 'completed')->count();

        $cancelledTrades = Trade::where(function($q) use ($user) {
            $q->where('initiator_id', $user->id)->orWhere('receiver_id', $user->id);
        })->where('status', 'cancelled')->count();

        $baseScore += ($completedTrades * config('wflf.trust_score.trade_bonus', 2));
        $baseScore -= ($cancelledTrades * config('wflf.trust_score.cancel_penalty', 5));

        // Ratings
        $avgRating = Rating::where('reviewee_id', $user->id)->avg('score') ?? 0;
        if ($avgRating > 0) {
            // max +30 points for 5 star average
            $ratingBonus = ($avgRating / 5) * 30;
            $baseScore += $ratingBonus;
        }

        $finalScore = max(0, min(100, $baseScore));

        $user->trust_score = $finalScore;
        $user->save();
    }
}

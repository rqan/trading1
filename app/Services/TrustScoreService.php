<?php

namespace App\Services;

use App\Models\Trade;
use App\Models\User;
use App\Models\Rating;

class TrustScoreService
{
    public function recalculate(User $user): void
    {
        $baseScore = 50; // Starting score

        // Profile completeness (simple check: name, email)
        if ($user->name && $user->email) {
            $baseScore += 10;
        }

        // Trade ratio
        $completedTrades = Trade::where(function($q) use ($user) {
            $q->where('initiator_id', $user->id)->orWhere('receiver_id', $user->id);
        })->where('status', 'completed')->count();

        $cancelledTrades = Trade::where(function($q) use ($user) {
            $q->where('initiator_id', $user->id)->orWhere('receiver_id', $user->id);
        })->where('status', 'cancelled')->count();

        $baseScore += ($completedTrades * 2);
        $baseScore -= ($cancelledTrades * 5); // penalty for cancellations

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

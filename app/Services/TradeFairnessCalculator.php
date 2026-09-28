<?php

namespace App\Services;

use App\Models\Trade;

class TradeFairnessCalculator
{
    public function calculate(Trade $trade): array
    {
        $initiatorTotal = 0.0;
        $receiverTotal = 0.0;

        foreach ($trade->items as $item) {
            $value = $item->collectionItem->computed_value ?? 0;
            if ($item->user_id === $trade->initiator_id) {
                $initiatorTotal += $value;
            } elseif ($item->user_id === $trade->receiver_id) {
                $receiverTotal += $value;
            }
        }

        $difference = abs($initiatorTotal - $receiverTotal);
        $maxTotal = max($initiatorTotal, $receiverTotal) ?: 1.0; // avoid div by zero
        $percentageDiff = $difference / $maxTotal;

        $isFair = $percentageDiff <= 0.15;
        
        $loserId = null;
        $kickerValueNeeded = 0;

        if (!$isFair) {
            if ($initiatorTotal < $receiverTotal) {
                $loserId = $trade->initiator_id;
                $kickerValueNeeded = ($receiverTotal * 0.85) - $initiatorTotal;
            } else {
                $loserId = $trade->receiver_id;
                $kickerValueNeeded = ($initiatorTotal * 0.85) - $receiverTotal;
            }
        }

        return [
            'initiator_total' => $initiatorTotal,
            'receiver_total' => $receiverTotal,
            'is_fair' => $isFair,
            'percentage_diff' => $percentageDiff * 100, // as percentage
            'needs_kicker_from' => $loserId,
            'kicker_value_needed' => max(0, $kickerValueNeeded),
        ];
    }
}

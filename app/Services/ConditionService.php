<?php

namespace App\Services;

use App\Models\CollectionItem;
use App\Models\MarketSignal;

class ConditionService
{
    public function getCarConditionMultiplier(string $condition): float
    {
        return match (strtolower($condition)) {
            'sealed/moc', 'sealed' => 1.5,
            'mint loose' => 1.2,
            'good' => 1.0,
            'played' => 0.7,
            'damaged' => 0.4,
            default => 1.0,
        };
    }

    public function getCardConditionMultiplier(?string $cardCondition): float
    {
        if (!$cardCondition) {
            return 1.0;
        }

        return match (strtolower($cardCondition)) {
            'mint' => 1.0,
            'creased' => 0.9,
            'torn' => 0.8,
            'missing' => 0.7,
            default => 1.0,
        };
    }

    public function calculateComputedValue(CollectionItem $item): float
    {
        $baseValue = $item->casting->base_value;
        
        $signal = MarketSignal::where('casting_id', $item->casting_id)->first();
        $marketMultiplier = $signal ? $signal->current_multiplier : 1.0;

        $carMult = $this->getCarConditionMultiplier($item->condition);
        $cardMult = $this->getCardConditionMultiplier($item->card_condition);
        
        // Total condition multiplier is car * card
        $conditionMult = $carMult * $cardMult;

        return $baseValue * $marketMultiplier * $conditionMult;
    }

    public function updateComputedValue(CollectionItem $item): void
    {
        $item->computed_value = $this->calculateComputedValue($item);
        $item->save();
    }
}

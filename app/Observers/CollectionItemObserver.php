<?php

namespace App\Observers;

use App\Models\CollectionItem;
use App\Services\ConditionService;

class CollectionItemObserver
{
    protected ConditionService $conditionService;

    public function __construct(ConditionService $conditionService)
    {
        $this->conditionService = $conditionService;
    }

    public function creating(CollectionItem $collectionItem): void
    {
        $collectionItem->computed_value = $this->conditionService->calculateComputedValue($collectionItem);
    }

    public function updating(CollectionItem $collectionItem): void
    {
        if ($collectionItem->isDirty(['condition', 'card_condition'])) {
            $collectionItem->computed_value = $this->conditionService->calculateComputedValue($collectionItem);
        }
    }
}

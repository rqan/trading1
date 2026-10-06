<?php

namespace App\Observers;

use App\Models\Listing;

class ListingObserver
{
    public function created(Listing $listing): void
    {
        // Temporarily disabled due to new listing architecture where items are attached after listing creation.
        // Requires moving logic to ListingItem created event or a service class.
    }
}

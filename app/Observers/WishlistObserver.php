<?php

namespace App\Observers;

use App\Models\Casting;
use App\Models\Listing;
use App\Models\Wishlist;
use App\Notifications\TradeMatchNotification;
use App\Services\MarketMultiplierService;

class WishlistObserver
{
    public function created(Wishlist $wishlist): void
    {
        // Check if there are active for_trade listings for this casting
        $hasForTrade = Listing::where('status', 'active')
            ->where('user_id', '!=', $wishlist->user_id)
            ->whereHas('offeredItems', function ($q) use ($wishlist) {
                $q->where('casting_id', $wishlist->casting_id);
            })
            ->exists();

        if ($hasForTrade) {
            $casting = Casting::find($wishlist->casting_id);
            $wishlist->user->notify(new TradeMatchNotification($casting, 'someone_has_your_wishlist'));
        }

        // Apply micro adjustment: wishlist added +0.5%
        $service = app(MarketMultiplierService::class);
        $service->applyMicroAdjustment($wishlist->casting_id, 'wishlist_added', $wishlist->user);
    }
}

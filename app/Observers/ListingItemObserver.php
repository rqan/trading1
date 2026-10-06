<?php

namespace App\Observers;

use App\Models\Casting;
use App\Models\Listing;
use App\Models\ListingItem;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\TradeMatchNotification;
use App\Services\MarketMultiplierService;

class ListingItemObserver
{
    public function created(ListingItem $item): void
    {
        $listing = $item->listing;
        $castingId = $item->casting_id;

        if (! $castingId) {
            return;
        }

        if ($item->side === 'offered') {
            // Notify users who have this in their wishlist
            $wishlistUserIds = Wishlist::where('casting_id', $castingId)
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');

            // Notify users who have a requested item for this casting
            $lookingForUserIds = Listing::whereHas('requestedItems', function ($q) use ($castingId) {
                $q->where('casting_id', $castingId);
            })
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');

            $userIdsToNotify = $wishlistUserIds->merge($lookingForUserIds)->unique();

            $users = User::whereIn('id', $userIdsToNotify)->get();
            $casting = Casting::find($castingId);

            foreach ($users as $u) {
                $u->notify(new TradeMatchNotification($casting, 'someone_has_your_wishlist'));
            }
        } elseif ($item->side === 'requested') {
            // Notify users who have this offered
            $forTradeUserIds = Listing::whereHas('offeredItems', function ($q) use ($castingId) {
                $q->where('casting_id', $castingId);
            })
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');

            $users = User::whereIn('id', $forTradeUserIds)->get();
            $casting = Casting::find($castingId);

            foreach ($users as $u) {
                $u->notify(new TradeMatchNotification($casting, 'someone_wants_your_listing'));
            }
        }

        // Apply micro adjustment: new offered item -0.3%
        if ($item->side === 'offered') {
            $service = app(MarketMultiplierService::class);
            $service->applyMicroAdjustment($castingId, 'listing_added', $listing->user);
        }
    }
}

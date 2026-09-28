<?php

namespace App\Observers;

use App\Models\Listing;
use App\Models\User;
use App\Models\Wishlist;
use App\Notifications\TradeMatchNotification;

class ListingObserver
{
    public function created(Listing $listing): void
    {
        $castingId = $listing->type === 'for_trade' ? $listing->collectionItem->casting_id : $listing->casting_id;

        if (!$castingId) return;

        if ($listing->type === 'for_trade') {
            // Notify users who have this in their wishlist
            $wishlistUserIds = Wishlist::where('casting_id', $castingId)
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');

            // Notify users who have a looking_for listing
            $lookingForUserIds = Listing::where('type', 'looking_for')
                ->where('casting_id', $castingId)
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');

            $userIdsToNotify = $wishlistUserIds->merge($lookingForUserIds)->unique();
            
            $users = User::whereIn('id', $userIdsToNotify)->get();
            $casting = \App\Models\Casting::find($castingId);
            
            foreach ($users as $u) {
                $u->notify(new TradeMatchNotification($casting, 'someone_has_your_wishlist'));
            }
        } elseif ($listing->type === 'looking_for') {
            // Notify users who have this for trade
            $forTradeUserIds = Listing::where('type', 'for_trade')
                ->whereHas('collectionItem', function($q) use ($castingId) {
                    $q->where('casting_id', $castingId);
                })
                ->where('user_id', '!=', $listing->user_id)
                ->pluck('user_id');
                
            $users = User::whereIn('id', $forTradeUserIds)->get();
            $casting = \App\Models\Casting::find($castingId);
            
            foreach ($users as $u) {
                $u->notify(new TradeMatchNotification($casting, 'someone_wants_your_listing'));
            }
        }

        // Apply micro adjustment: new for_trade listing -0.3%
        if ($listing->type === 'for_trade') {
            $service = app(\App\Services\MarketMultiplierService::class);
            $service->applyMicroAdjustment($castingId, 'listing_added', $listing->user);
        }
    }
}

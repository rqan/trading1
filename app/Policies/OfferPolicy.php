<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offer $offer): bool
    {
        return $user->id === $offer->sender_id || $user->id === $offer->listing->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Listing $listing): Response
    {
        if ($user->id === $listing->user_id) {
            return Response::deny('Anda tidak bisa memberikan penawaran pada listing Anda sendiri.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model (e.g. to accept/counter).
     */
    public function update(User $user, Offer $offer): bool
    {
        return $user->id === $offer->listing->user_id;
    }

    /**
     * Determine whether the user can delete/withdraw the offer.
     */
    public function delete(User $user, Offer $offer): Response
    {
        if ($user->id === $offer->sender_id) {
            if ($offer->status !== 'pending') {
                return Response::deny('Hanya penawaran berstatus pending yang bisa ditarik kembali.');
            }

            return Response::allow();
        }

        return Response::deny('Anda bukan pemilik penawaran ini.');
    }
}

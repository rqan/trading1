<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'sender_id',
        'parent_offer_id',
        'status',
        'cash_addition',
        'notes',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parentOffer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'parent_offer_id');
    }

    public function counterOffers(): HasMany
    {
        return $this->hasMany(Offer::class, 'parent_offer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfferItem::class);
    }
}

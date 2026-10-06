<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'accepted_offer_id',
        'party_a_id',
        'party_b_id',
        'party_a_confirmed_at',
        'party_b_confirmed_at',
        'completion_method',
        'trade_status',

        // Legacy fields for backward compatibility
        'initiator_id',
        'receiver_id',
        'status',
        'tracking_initiator',
        'tracking_receiver',
        'photos_checked_initiator',
        'photos_checked_receiver',
        'accepted_initiator',
        'accepted_receiver',
    ];

    protected $casts = [
        'party_a_confirmed_at' => 'datetime',
        'party_b_confirmed_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function acceptedOffer(): BelongsTo
    {
        return $this->belongsTo(Offer::class, 'accepted_offer_id');
    }

    public function partyA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'party_a_id');
    }

    public function partyB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'party_b_id');
    }

    // Legacy relations
    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TradeMessage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TradeItem::class);
    }
}

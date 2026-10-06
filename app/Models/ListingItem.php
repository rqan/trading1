<?php

namespace App\Models;

use App\Observers\ListingItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(ListingItemObserver::class)]
class ListingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'side',
        'casting_id',
        'special_type',
        'quantity',
        'condition',
        'condition_notes',
        'cash_addition',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }
}

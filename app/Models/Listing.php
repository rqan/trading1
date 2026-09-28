<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ListingObserver;

#[ObservedBy(ListingObserver::class)]
class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'collection_item_id',
        'casting_id',
        'type',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function collectionItem(): BelongsTo
    {
        return $this->belongsTo(CollectionItem::class);
    }

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }
}

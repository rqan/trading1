<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ListingObserver;

#[ObservedBy(ListingObserver::class)]
class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'completion_method',
        'city',
        'visibility',
        'club_id',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ListingItem::class);
    }

    public function offeredItems(): HasMany
    {
        return $this->hasMany(ListingItem::class)->where('side', 'offered');
    }

    public function requestedItems(): HasMany
    {
        return $this->hasMany(ListingItem::class)->where('side', 'requested');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function trade(): HasOne
    {
        return $this->hasOne(Trade::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where(function($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }
}

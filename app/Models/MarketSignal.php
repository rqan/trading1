<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'casting_id',
        'active_listing_count',
        'wishlist_count',
        'trade_completed_30d',
        'view_count_7d',
        'current_multiplier',
        'target_multiplier',
        'last_calculated_at',
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketMultiplierHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'casting_id',
        'multiplier',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'casting_id',
        'condition',
        'card_condition',
        'photos',
        'status',
        'computed_value',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }
}

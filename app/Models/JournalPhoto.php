<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class JournalPhoto extends Model
{
    protected $fillable = ['journal_id', 'path', 'order'];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /** URL publik foto dari storage. */
    public function getUrlAttribute(): string
    {
        return route('photos.show', ['path' => basename($this->path)]);
    }
}

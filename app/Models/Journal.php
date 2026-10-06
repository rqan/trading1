<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'casting_id',
        'title',
        'acquisition_type',
        'acquired_at',
        'price',
        'story',
        'latitude',
        'longitude',
        'place_name',
        'place_address',
        'visibility',
        'show_in_feed',
    ];

    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'price' => 'integer',
            'show_in_feed' => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function casting(): BelongsTo
    {
        return $this->belongsTo(Casting::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(JournalPhoto::class)->orderBy('order');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(JournalReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(JournalComment::class)->latest();
    }

    // ── Scopes ──────────────────────────────────────────────

    /** Hanya jurnal publik. */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Jurnal yang boleh dilihat oleh $userId:
     * jurnal publik ATAU jurnal milik sendiri.
     */
    public function scopeVisibleTo(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $q) use ($userId) {
            $q->where('visibility', 'public')
                ->orWhere('user_id', $userId);
        });
    }

    /** Jurnal yang tampil di Community Feed (public + show_in_feed). */
    public function scopeInFeed(Builder $query): Builder
    {
        return $query->where('visibility', 'public')->where('show_in_feed', true);
    }

    // ── Helpers ─────────────────────────────────────────────

    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /** Foto sampul: foto pertama (order=0) atau null. */
    public function coverPhoto(): ?JournalPhoto
    {
        return $this->photos->first();
    }

    /** Label cara mendapatkan untuk tampilan. */
    public function acquisitionLabel(): string
    {
        return match ($this->acquisition_type) {
            'beli' => 'Beli',
            'hadiah' => 'Hadiah',
            'tukar' => 'Tukar',
            'temuan' => 'Temuan',
            default => ucfirst($this->acquisition_type),
        };
    }

    /** Jumlah foto saat ini (cached dari relasi jika sudah di-load). */
    public function photoCount(): int
    {
        return $this->photos->count();
    }

    /** Tautan Google Maps berdasarkan koordinat tersimpan. */
    public function googleMapsUrl(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}

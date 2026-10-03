<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Casting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'series',
        'year',
        'rarity_tier',
        'base_value',
        'image_url',
    ];

    /**
     * Gunakan slug sebagai route key untuk URL SEO-friendly.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Auto-generate slug saat name disimpan jika slug kosong.
     */
    protected static function booted(): void
    {
        static::saving(function (Casting $casting) {
            if (empty($casting->slug)) {
                $casting->slug = Str::slug($casting->name . '-' . $casting->year);
            }
        });
    }

    // ── Relasi ──────────────────────────────────────────────

    public function marketSignal(): HasOne
    {
        return $this->hasOne(MarketSignal::class);
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(MarketMultiplierHistory::class)->orderBy('recorded_at');
    }

    public function collectionItems(): HasMany
    {
        return $this->hasMany(CollectionItem::class);
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    // ── Computed helpers ────────────────────────────────────

    /**
     * Harga saat ini = base_value × current_multiplier.
     */
    public function getCurrentPriceAttribute(): float
    {
        $multiplier = $this->marketSignal?->current_multiplier ?? 1.0;
        return round((float) $this->base_value * (float) $multiplier);
    }

    /**
     * Harga 30 hari terakhir (min & max) untuk rentang harga.
     */
    public function getPriceRange30d(): array
    {
        $histories = MarketMultiplierHistory::where('casting_id', $this->id)
            ->where('recorded_at', '>=', now()->subDays(30))
            ->selectRaw('MIN(multiplier) as min_mult, MAX(multiplier) as max_mult')
            ->first();

        $min = round((float) $this->base_value * (float) ($histories?->min_mult ?? 1.0));
        $max = round((float) $this->base_value * (float) ($histories?->max_mult ?? 1.0));

        return ['min' => $min, 'max' => $max];
    }

    /**
     * Trend: 'up', 'down', atau 'stable' berdasarkan perbandingan 7 hari ini vs 7 hari sebelumnya.
     */
    public function getTrendAttribute(): string
    {
        $recent = MarketMultiplierHistory::where('casting_id', $this->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->avg('multiplier');

        $previous = MarketMultiplierHistory::where('casting_id', $this->id)
            ->whereBetween('recorded_at', [now()->subDays(14), now()->subDays(7)])
            ->avg('multiplier');

        if (!$recent || !$previous || abs($recent - $previous) < 0.02) {
            return 'stable';
        }

        return $recent > $previous ? 'up' : 'down';
    }
}

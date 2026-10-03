<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'password', 'is_admin', 'trust_score', 'is_banned', 'platform_role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true || in_array($this->platform_role, ['superadmin', 'moderator']);
    }

    // --- Role Helpers ---
    public function isSuperadmin(): bool
    {
        return $this->platform_role === 'superadmin' || $this->is_admin;
    }

    public function isModerator(): bool
    {
        return $this->platform_role === 'moderator';
    }

    public function isAtLeastModerator(): bool
    {
        return in_array($this->platform_role, ['superadmin', 'moderator']) || $this->is_admin;
    }

    // --- Club Relations ---
    public function ownedClubs()
    {
        return $this->hasMany(Club::class, 'founder_id');
    }

    public function clubMemberships()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function joinedClubs()
    {
        return $this->belongsToMany(Club::class, 'club_members')
                    ->withPivot('club_role', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Semua trade yang melibatkan user ini (sebagai initiator maupun receiver).
     */
    public function tradesAsInitiator()
    {
        return $this->hasMany(Trade::class, 'initiator_id');
    }

    public function tradesAsReceiver()
    {
        return $this->hasMany(Trade::class, 'receiver_id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    /**
     * Rating yang diterima user ini dari orang lain.
     */
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'reviewee_id');
    }

    /**
     * Jurnal Hot Wheels milik user ini.
     */
    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}

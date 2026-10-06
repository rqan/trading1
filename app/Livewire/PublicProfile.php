<?php

namespace App\Livewire;

use App\Models\CollectionItem;
use App\Models\Rating;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PublicProfile extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function getTitle(): string
    {
        return "Profil {$this->user->name}";
    }

    public function toggleFollow(): void
    {
        $me = Auth::user();

        if ($me->id === $this->user->id) {
            return;
        }

        if ($me->following()->where('followed_id', $this->user->id)->exists()) {
            $me->following()->detach($this->user->id);
        } else {
            $me->following()->attach($this->user->id);
        }
    }

    public function render()
    {
        $profileUser = $this->user->load(['followers', 'following']);

        // Koleksi publik (hanya item yang bukan traded)
        $collection = CollectionItem::with('casting')
            ->where('user_id', $this->user->id)
            ->whereNotIn('status', ['traded'])
            ->latest()
            ->limit(12)
            ->get();

        // Statistik trade
        $completedTrades = Trade::where(function ($q) {
            $q->where('initiator_id', $this->user->id)
                ->orWhere('receiver_id', $this->user->id);
        })->where('status', 'completed')->count();

        $activeTrades = Trade::where(function ($q) {
            $q->where('initiator_id', $this->user->id)
                ->orWhere('receiver_id', $this->user->id);
        })->whereIn('status', ['proposed', 'accepted', 'shipped'])->count();

        // Rating yang diterima
        $ratings = Rating::with('reviewer')
            ->where('reviewee_id', $this->user->id)
            ->latest()
            ->limit(5)
            ->get();

        $avgRating = Rating::where('reviewee_id', $this->user->id)->avg('score');

        // Apakah saya mengikuti user ini
        $isFollowing = Auth::user()->following()->where('followed_id', $this->user->id)->exists();

        return view('livewire.public-profile', [
            'profileUser' => $profileUser,
            'collection' => $collection,
            'completedTrades' => $completedTrades,
            'activeTrades' => $activeTrades,
            'ratings' => $ratings,
            'avgRating' => $avgRating,
            'isFollowing' => $isFollowing,
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Clubs - Daftar Club')]
class ClubList extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function joinClub($clubId)
    {
        $club = Club::findOrFail($clubId);
        $userId = Auth::id();

        if (! $club->members()->where('user_id', $userId)->exists()) {
            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $userId,
                'club_role' => 'member',
            ]);
        }
    }

    public function leaveClub($clubId)
    {
        $club = Club::findOrFail($clubId);
        $userId = Auth::id();

        // Founder cannot leave directly
        $member = ClubMember::where('club_id', $club->id)->where('user_id', $userId)->first();
        if ($member && $member->club_role !== 'founder') {
            $member->delete();
        }
    }

    public function render()
    {
        $clubs = Club::withCount('members')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate(12);

        $joinedClubIds = Auth::check() ? Auth::user()->clubMemberships()->pluck('club_id')->toArray() : [];

        return view('livewire.club-list', [
            'clubs' => $clubs,
            'joinedClubIds' => $joinedClubIds,
        ]);
    }
}

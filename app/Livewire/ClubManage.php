<?php

namespace App\Livewire;

use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ClubManage extends Component
{
    public Club $club;

    public $name;

    public $description;

    public $rules;

    public $is_private;

    public function mount(Club $club)
    {
        $this->club = $club;
        $this->authorizeAccess();

        $this->name = $club->name;
        $this->description = $club->description;
        $this->rules = $club->rules;
        $this->is_private = $club->is_private;
    }

    public function authorizeAccess()
    {
        $member = ClubMember::where('club_id', $this->club->id)->where('user_id', Auth::id())->first();
        if (! $member || ! in_array($member->club_role, ['founder', 'co_founder'])) {
            abort(403, 'Hanya Founder atau Co-founder yang dapat mengelola Club ini.');
        }
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rules' => 'nullable|string',
            'is_private' => 'boolean',
        ]);

        $this->club->update([
            'name' => $this->name,
            'description' => $this->description,
            'rules' => $this->rules,
            'is_private' => $this->is_private,
        ]);

        session()->flash('message', 'Profil Club berhasil diperbarui.');
    }

    public function removeMember($memberId)
    {
        $target = ClubMember::findOrFail($memberId);

        if ($target->club_id !== $this->club->id) {
            abort(403);
        }

        // Founder tidak bisa di-remove oleh siapa pun
        if ($target->club_role === 'founder') {
            return;
        }

        // Hanya founder yang bisa meremove co-founder
        $myRole = ClubMember::where('club_id', $this->club->id)->where('user_id', Auth::id())->first()->club_role;
        if ($target->club_role === 'co_founder' && $myRole !== 'founder') {
            return;
        }

        $target->delete();
    }

    public function render()
    {
        $members = ClubMember::with('user')
            ->where('club_id', $this->club->id)
            ->orderByRaw("CASE WHEN club_role = 'founder' THEN 1 WHEN club_role = 'co_founder' THEN 2 WHEN club_role = 'club_moderator' THEN 3 ELSE 4 END")
            ->get();

        return view('livewire.club-manage', [
            'members' => $members,
        ]);
    }
}

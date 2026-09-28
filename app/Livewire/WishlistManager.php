<?php

namespace App\Livewire;

use App\Models\Casting;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WishlistManager extends Component
{
    public $castings = [];
    public $selectedCasting = null;
    public $showAddModal = false;

    public function mount()
    {
        $this->castings = Casting::orderBy('name')->get();
    }

    public function add()
    {
        $this->validate([
            'selectedCasting' => 'required|exists:castings,id',
        ]);

        $exists = Wishlist::where('user_id', Auth::id())
            ->where('casting_id', $this->selectedCasting)
            ->exists();

        if ($exists) {
            session()->flash('error', 'This casting is already in your wishlist.');
            return;
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'casting_id' => $this->selectedCasting,
        ]);

        $this->reset(['selectedCasting', 'showAddModal']);
        session()->flash('message', 'Casting added to wishlist.');
    }

    public function postLookingFor($castingId)
    {
        $exists = \App\Models\Listing::where('user_id', Auth::id())
            ->where('casting_id', $castingId)
            ->where('type', 'looking_for')
            ->where('status', 'active')
            ->exists();

        if (!$exists) {
            \App\Models\Listing::create([
                'user_id' => Auth::id(),
                'casting_id' => $castingId,
                'type' => 'looking_for',
            ]);
            session()->flash('message', 'Listing Looking For created.');
        } else {
            session()->flash('error', 'You already have an active Looking For listing for this casting.');
        }
    }

    public function remove($id)
    {
        Wishlist::where('id', $id)->where('user_id', Auth::id())->delete();
    }

    public function render()
    {
        $wishlists = Wishlist::with('casting')->where('user_id', Auth::id())->latest()->get();

        return view('livewire.wishlist-manager', [
            'wishlists' => $wishlists,
        ]);
    }
}

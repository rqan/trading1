<?php

namespace App\Livewire;

use App\Models\Casting;
use App\Models\CollectionItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CollectionManager extends Component
{
    use WithFileUploads;

    public $castings = [];
    public $selectedCasting = null;
    public $condition = 'Sealed/MOC';
    public $cardCondition = 'Mint';
    public $photos = [];
    public $status = 'available';

    public $showAddModal = false;

    public function mount()
    {
        $this->castings = Casting::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate([
            'selectedCasting' => 'required|exists:castings,id',
            'condition' => 'required|string',
            'cardCondition' => 'nullable|string',
            'photos.*' => 'image|max:2048',
            'status' => 'required|string',
        ]);

        $photoPaths = [];
        foreach ($this->photos as $photo) {
            $photoPaths[] = $photo->store('collection-photos', 'public');
        }

        CollectionItem::create([
            'user_id' => Auth::id(),
            'casting_id' => $this->selectedCasting,
            'condition' => $this->condition,
            'card_condition' => $this->cardCondition,
            'photos' => $photoPaths,
            'status' => $this->status,
        ]);

        $this->reset(['selectedCasting', 'condition', 'cardCondition', 'photos', 'status', 'showAddModal']);
        $this->condition = 'Sealed/MOC';
        $this->cardCondition = 'Mint';
        $this->status = 'available';

        session()->flash('message', 'Item added to collection.');
    }

    public function deleteItem($id)
    {
        CollectionItem::where('id', $id)->where('user_id', Auth::id())->delete();
    }

    public function render()
    {
        $collection = CollectionItem::with('casting')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.collection-manager', [
            'collection' => $collection,
        ]);
    }
}

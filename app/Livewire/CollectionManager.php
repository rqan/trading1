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
    public $condition = 'Graded';
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
        $this->condition = 'Graded';
        $this->cardCondition = 'Mint';
        $this->status = 'available';

        session()->flash('message', 'Item added to collection.');
    }

    public function postForTrade($itemId)
    {
        $item = CollectionItem::where('id', $itemId)->where('user_id', Auth::id())->first();
        if (!$item) return;

        $exists = \App\Models\Listing::where('collection_item_id', $itemId)
            ->where('type', 'for_trade')
            ->where('status', 'active')
            ->exists();

        if (!$exists) {
            \App\Models\Listing::create([
                'user_id' => Auth::id(),
                'collection_item_id' => $itemId,
                'type' => 'for_trade',
            ]);
            $item->update(['status' => 'for_trade']);
            session()->flash('message', 'Item listed For Trade.');
        } else {
            session()->flash('error', 'This item is already listed For Trade.');
        }
    }

    public function deleteItem($id)
    {
        $item = CollectionItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) return;

        // Blokir: item yang sedang di-listing sebagai "for_trade" tidak boleh dihapus langsung
        if (in_array($item->status, ['for_trade', 'traded'])) {
            session()->flash('error', 'Item tidak dapat dihapus karena sedang aktif di pasar atau sudah selesai ditrade. Batalkan listing-nya terlebih dahulu.');
            return;
        }

        // Blokir: item yang sedang terkait dalam trade yang belum selesai tidak boleh dihapus
        $inActiveTrade = \App\Models\TradeItem::whereHas('trade', function ($q) {
            $q->whereNotIn('status', ['completed', 'cancelled']);
        })->where('collection_item_id', $id)->exists();

        if ($inActiveTrade) {
            session()->flash('error', 'Item tidak dapat dihapus karena sedang terkait dalam transaksi yang aktif.');
            return;
        }

        $item->delete();
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

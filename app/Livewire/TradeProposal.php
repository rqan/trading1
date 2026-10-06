<?php

namespace App\Livewire;

use App\Models\CollectionItem;
use App\Models\Listing;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\TradeMessage;
use App\Models\User;
use App\Notifications\TradeProposedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TradeProposal extends Component
{
    public $listingId;

    public bool $showModal = false;

    public bool $showConfirmation = false;

    public array $selectedItems = [];

    public string $note = '';

    /** @var Collection<int, CollectionItem>|null */
    public $myCollection = null;

    public function mount($listingId): void
    {
        $this->listingId = $listingId;
    }

    public function openModal(): void
    {
        $this->myCollection = CollectionItem::with('casting')
            ->where('user_id', Auth::id())
            ->where('status', 'available')
            ->latest()
            ->get();

        $this->reset(['selectedItems', 'note', 'showConfirmation']);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->showConfirmation = false;
    }

    public function reviewProposal(): void
    {
        $this->validate([
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*' => 'exists:collection_items,id',
            'note' => 'nullable|string|max:500',
        ], [
            'selectedItems.required' => 'Pilih minimal 1 item dari koleksi untuk ditawarkan.',
            'selectedItems.min' => 'Pilih minimal 1 item dari koleksi untuk ditawarkan.',
        ]);

        // Verify all selected items belong to the current user and are available
        $validCount = CollectionItem::where('user_id', Auth::id())
            ->where('status', 'available')
            ->whereIn('id', $this->selectedItems)
            ->count();

        if ($validCount !== count($this->selectedItems)) {
            session()->flash('error', 'Beberapa item yang dipilih tidak valid atau sudah tidak tersedia.');

            return;
        }

        $this->showConfirmation = true;
    }

    public function backToSelection(): void
    {
        $this->showConfirmation = false;
    }

    public function propose()
    {
        $listing = Listing::with('offeredItems')->findOrFail($this->listingId);

        if ($listing->user_id === Auth::id()) {
            session()->flash('error', "You can't trade with yourself.");

            return;
        }

        if ($listing->offeredItems->isEmpty()) {
            session()->flash('error', 'This listing does not offer any items for trade.');

            return;
        }

        // Re-validate selected items
        $this->validate([
            'selectedItems' => 'required|array|min:1',
            'selectedItems.*' => 'exists:collection_items,id',
            'note' => 'nullable|string|max:500',
        ]);

        // Create the trade
        $trade = Trade::create([
            'initiator_id' => Auth::id(),
            'receiver_id' => $listing->user_id,
            'listing_id' => $listing->id,
            'status' => 'proposed',
        ]);

        // Add all selected items from the initiator
        foreach ($this->selectedItems as $itemId) {
            TradeItem::create([
                'trade_id' => $trade->id,
                'user_id' => Auth::id(),
                'collection_item_id' => $itemId,
            ]);
        }

        // Save the optional note as the first trade message
        if (! empty(trim($this->note))) {
            TradeMessage::create([
                'trade_id' => $trade->id,
                'user_id' => Auth::id(),
                'message' => trim($this->note),
            ]);
        }

        // Notify the receiver
        $trade->load('initiator');
        $receiver = User::find($listing->user_id);
        $receiver?->notify(new TradeProposedNotification($trade));

        return redirect()->route('trade.room', ['trade' => $trade->id]);
    }

    public function render()
    {
        return view('livewire.trade-proposal');
    }
}

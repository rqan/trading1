<?php

namespace App\Livewire;

use App\Models\Listing;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\User;
use App\Notifications\TradeProposedNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TradeProposal extends Component
{
    public $listingId;

    public function mount($listingId)
    {
        $this->listingId = $listingId;
    }

    public function propose()
    {
        $listing = Listing::findOrFail($this->listingId);

        if ($listing->user_id === Auth::id()) {
            session()->flash('error', "You can't trade with yourself.");
            return;
        }

        if ($listing->type !== 'for_trade') {
            session()->flash('error', 'This listing is not for trade.');
            return;
        }

        // Create the trade
        $trade = Trade::create([
            'initiator_id' => Auth::id(),
            'receiver_id' => $listing->user_id,
            'status' => 'proposed',
        ]);

        // Add the receiver's listed item to the trade automatically
        TradeItem::create([
            'trade_id'           => $trade->id,
            'user_id'            => $listing->user_id,
            'collection_item_id' => $listing->collection_item_id,
        ]);

        // Notifikasi ke receiver bahwa ada trade baru yang diajukan
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

<?php

namespace App\Livewire;

use App\Events\TradeMessageSent;
use App\Events\TradeUpdated;
use App\Models\CollectionItem;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\TradeMessage;
use App\Services\TradeFairnessCalculator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class TradeRoom extends Component
{
    public Trade $trade;
    public $message = '';
    public $selectedItemToAdd = '';
    public $trackingNumber = '';

    public function mount(Trade $trade)
    {
        $this->trade = $trade;
        if (Auth::id() !== $trade->initiator_id && Auth::id() !== $trade->receiver_id) {
            abort(403);
        }
    }

    public function getFairnessProperty()
    {
        return (new TradeFairnessCalculator())->calculate($this->trade);
    }

    public function addItem()
    {
        if (!$this->selectedItemToAdd) return;

        $item = CollectionItem::where('id', $this->selectedItemToAdd)
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'traded')
            ->first();

        if ($item) {
            TradeItem::firstOrCreate([
                'trade_id' => $this->trade->id,
                'user_id' => Auth::id(),
                'collection_item_id' => $item->id,
            ]);
            $this->reset('selectedItemToAdd');
            TradeUpdated::dispatch($this->trade->id);
            $this->trade->refresh();
        }
    }

    public function removeItem($itemId)
    {
        TradeItem::where('trade_id', $this->trade->id)
            ->where('collection_item_id', $itemId)
            ->where('user_id', Auth::id())
            ->delete();
        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
    }

    public function sendMessage()
    {
        if (trim($this->message) === '') return;

        TradeMessage::create([
            'trade_id' => $this->trade->id,
            'user_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->reset('message');
        TradeMessageSent::dispatch($this->trade->id);
        $this->trade->refresh();
    }

    public function togglePhotosChecked()
    {
        if (Auth::id() === $this->trade->initiator_id) {
            $this->trade->photos_checked_initiator = !$this->trade->photos_checked_initiator;
        } else {
            $this->trade->photos_checked_receiver = !$this->trade->photos_checked_receiver;
        }
        $this->trade->save();
        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
    }

    public function acceptTrade()
    {
        $fairness = $this->getFairnessProperty();
        if (!$fairness['is_fair']) {
            session()->flash('error', 'Cannot accept. Trade is unfair (Diff > 15%). Add a kicker.');
            return;
        }

        $this->trade->update(['status' => 'accepted']);
        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
    }

    public function saveTracking()
    {
        if (Auth::id() === $this->trade->initiator_id) {
            $this->trade->update(['tracking_initiator' => $this->trackingNumber]);
        } else {
            $this->trade->update(['tracking_receiver' => $this->trackingNumber]);
        }
        
        if ($this->trade->tracking_initiator && $this->trade->tracking_receiver) {
            $this->trade->update(['status' => 'shipped']);
        }
        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
        session()->flash('message', 'Tracking saved.');
    }

    public function completeTrade()
    {
        $this->trade->update(['status' => 'completed']);
        // Mark all items as traded
        foreach ($this->trade->items as $tradeItem) {
            $tradeItem->collectionItem->update(['status' => 'traded']);
        }
        // Micro-adjustment for Market
        $service = app(\App\Services\MarketMultiplierService::class);
        foreach ($this->trade->items as $tradeItem) {
            $service->applyMicroAdjustment($tradeItem->collectionItem->casting_id, 'trade_completed', $this->trade->initiator); // both are completing it, we just pass one to validate trust score
        }

        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
    }

    #[On('echo-private:trades.{trade.id},TradeUpdated')]
    public function refreshTrade()
    {
        $this->trade->refresh();
    }

    #[On('echo-private:trades.{trade.id},TradeMessageSent')]
    public function refreshMessages()
    {
        $this->trade->refresh();
    }

    public function render()
    {
        $myItems = CollectionItem::where('user_id', Auth::id())
            ->where('status', '!=', 'traded')
            ->get();

        return view('livewire.trade-room', [
            'fairness' => $this->getFairnessProperty(),
            'myItems' => $myItems,
        ]);
    }
}

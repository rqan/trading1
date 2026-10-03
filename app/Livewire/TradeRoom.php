<?php

namespace App\Livewire;

use App\Events\TradeMessageSent;
use App\Events\TradeUpdated;
use App\Models\CollectionItem;
use App\Models\Trade;
use App\Models\TradeItem;
use App\Models\TradeMessage;
use App\Notifications\TradeAcceptedNotification;
use App\Notifications\TradeCompletedNotification;
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

        // Guard: item hanya bisa ditambah saat trade masih di tahap negosiasi
        if (!$this->trade->canBeModified()) {
            session()->flash('error', 'Item tidak dapat ditambah karena trade sudah dikunci.');
            return;
        }

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
        // Guard: item hanya bisa dihapus saat trade masih di tahap negosiasi
        if (!$this->trade->canBeModified()) {
            session()->flash('error', 'Item tidak dapat dihapus karena trade sudah dikunci.');
            return;
        }

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
        // Guard: hanya bisa menerima trade yang masih dalam status 'proposed'
        if ($this->trade->status !== 'proposed') {
            session()->flash('error', 'Trade ini tidak dalam status yang bisa diterima.');
            return;
        }

        $fairness = $this->getFairnessProperty();
        if (!$fairness['is_fair']) {
            session()->flash('error', 'Tidak dapat menerima. Trade tidak seimbang (Selisih > 15%). Tambahkan kicker.');
            return;
        }

        // Tandai persetujuan milik user yang sedang login
        if (Auth::id() === $this->trade->initiator_id) {
            $this->trade->update(['accepted_initiator' => true]);
        } else {
            $this->trade->update(['accepted_receiver' => true]);
        }

        $this->trade->refresh();

        // Jika kedua pihak sudah setuju, kunci trade ke status 'accepted'
        if ($this->trade->bothAccepted()) {
            $this->trade->update(['status' => 'accepted']);
            $this->trade->refresh();

            // Notifikasi kedua pihak: trade terkunci, saatnya kirim
            $notification = new TradeAcceptedNotification($this->trade);
            $this->trade->initiator->notify($notification);
            $this->trade->receiver->notify($notification);
        }

        TradeUpdated::dispatch($this->trade->id);
    }

    public function saveTracking()
    {
        // Guard: tracking hanya bisa diisi setelah trade resmi diterima kedua pihak
        if (!in_array($this->trade->status, ['accepted', 'shipped'])) {
            session()->flash('error', 'Nomor resi hanya bisa diisi setelah trade diterima.');
            return;
        }

        $this->validate(['trackingNumber' => 'required|string|max:100']);

        if (Auth::id() === $this->trade->initiator_id) {
            $this->trade->update(['tracking_initiator' => $this->trackingNumber]);
        } else {
            $this->trade->update(['tracking_receiver' => $this->trackingNumber]);
        }

        $this->trade->refresh();

        if ($this->trade->tracking_initiator && $this->trade->tracking_receiver) {
            $this->trade->update(['status' => 'shipped']);
        }
        TradeUpdated::dispatch($this->trade->id);
        $this->trade->refresh();
        session()->flash('message', 'Nomor resi berhasil disimpan.');
    }

    public function completeTrade()
    {
        // Guard: trade hanya bisa diselesaikan jika status sudah 'shipped'
        if ($this->trade->status !== 'shipped') {
            session()->flash('error', 'Trade hanya bisa diselesaikan setelah kedua resi diinput.');
            return;
        }

        $this->trade->update(['status' => 'completed']);
        // Mark all items as traded
        foreach ($this->trade->items as $tradeItem) {
            $tradeItem->collectionItem->update(['status' => 'traded']);
        }
        // Micro-adjustment for Market
        $service = app(\App\Services\MarketMultiplierService::class);
        foreach ($this->trade->items as $tradeItem) {
            $service->applyMicroAdjustment($tradeItem->collectionItem->casting_id, 'trade_completed', $this->trade->initiator);
        }

        // Notifikasi kedua pihak: trade selesai, ingatkan untuk rating
        $notification = new TradeCompletedNotification($this->trade);
        $this->trade->initiator->notify($notification);
        $this->trade->receiver->notify($notification);

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

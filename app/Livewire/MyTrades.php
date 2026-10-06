<?php

namespace App\Livewire;

use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('My Trades')]
class MyTrades extends Component
{
    use WithPagination;

    public string $filterStatus = '';

    protected $queryString = ['filterStatus'];

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function cancelTrade(int $tradeId): void
    {
        $trade = Trade::where('id', $tradeId)
            ->where(function ($q) {
                $q->where('initiator_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->whereIn('status', ['proposed'])
            ->firstOrFail();

        $trade->update([
            'status' => 'cancelled',
            'accepted_initiator' => false,
            'accepted_receiver' => false,
        ]);

        // Kembalikan status collection item jika trade dibatalkan
        foreach ($trade->items as $tradeItem) {
            if ($tradeItem->collectionItem && $tradeItem->collectionItem->status === 'for_trade') {
                // Biarkan status listing, hanya hapus keterikatan trade
            }
        }

        session()->flash('message', 'Trade berhasil dibatalkan.');
    }

    public function render()
    {
        $userId = Auth::id();

        $trades = Trade::with(['initiator', 'receiver', 'items.collectionItem.casting'])
            ->where(function ($q) use ($userId) {
                $q->where('initiator_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(10);

        return view('livewire.my-trades', ['trades' => $trades]);
    }
}

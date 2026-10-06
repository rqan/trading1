<?php

namespace App\Livewire;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('My Listings')]
class MyListings extends Component
{
    use WithPagination;

    public string $filterType = '';

    protected $queryString = ['filterType'];

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function cancelListing(int $listingId): void
    {
        $listing = Listing::where('id', $listingId)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->firstOrFail();

        // Jika listing for_trade, kembalikan status collection item ke 'available'
        if ($listing->type === 'for_trade' && $listing->collectionItem) {
            $listing->collectionItem->update(['status' => 'available']);
        }

        $listing->update(['status' => 'cancelled']);

        session()->flash('message', 'Listing berhasil dibatalkan.');
    }

    public function render()
    {
        $listings = Listing::with(['offeredItems.casting', 'requestedItems.casting'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('livewire.my-listings', ['listings' => $listings]);
    }
}

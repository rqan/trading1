<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Market – Jelajahi Listing')]
class ListingBrowser extends Component
{
    use WithPagination;

    public $search = '';

    public $filterType = ''; // for_trade or looking_for

    public $filterRarity = '';

    protected $queryString = ['search', 'filterType', 'filterRarity'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $listings = Listing::with(['user', 'offeredItems.casting', 'requestedItems.casting'])
            ->when($this->search, function ($query) {
                $query->where(function ($group) {
                    $group->whereHas('offeredItems.casting', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    })->orWhereHas('requestedItems.casting', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
                });
            })
            ->when($this->filterType, function ($query) {
                if ($this->filterType === 'for_trade') {
                    $query->has('offeredItems');
                } elseif ($this->filterType === 'looking_for') {
                    $query->has('requestedItems');
                }
            })
            ->when($this->filterRarity, function ($query) {
                $query->where(function ($group) {
                    $group->whereHas('offeredItems.casting', function ($q) {
                        $q->where('rarity_tier', $this->filterRarity);
                    })->orWhereHas('requestedItems.casting', function ($q) {
                        $q->where('rarity_tier', $this->filterRarity);
                    });
                });
            })
            ->where('status', 'active')
            ->latest()
            ->paginate(12);

        return view('livewire.listing-browser', [
            'listings' => $listings,
        ]);
    }
}

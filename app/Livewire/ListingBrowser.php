<?php

namespace App\Livewire;

use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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
        $listings = Listing::with(['user', 'collectionItem', 'casting', 'collectionItem.casting'])
            ->when($this->search, function ($query) {
                $query->whereHas('casting', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('collectionItem.casting', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->when($this->filterRarity, function ($query) {
                $query->whereHas('casting', function ($q) {
                    $q->where('rarity_tier', $this->filterRarity);
                })->orWhereHas('collectionItem.casting', function ($q) {
                    $q->where('rarity_tier', $this->filterRarity);
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

<?php

namespace App\Livewire;

use App\Models\Casting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Values – Daftar Nilai Casting')]
class ValuesList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'rarity')]
    public string $rarity = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'value_desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRarity(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Casting::with('marketSignal')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('series', 'like', "%{$this->search}%")
            )
            ->when($this->rarity, fn ($q) => $q->where('rarity_tier', $this->rarity)
            );

        // Sorting
        match ($this->sortBy) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'value_asc' => $query->orderBy('base_value', 'asc'),
            'value_desc' => $query->orderBy('base_value', 'desc'),
            'rarity' => $query->orderBy('rarity_tier', 'asc'),
            default => $query->orderBy('base_value', 'desc'),
        };

        $rarities = Casting::distinct()->orderBy('rarity_tier')->pluck('rarity_tier');

        return view('livewire.values-list', [
            'castings' => $query->paginate(24),
            'rarities' => $rarities,
        ]);
    }
}

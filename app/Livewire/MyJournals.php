<?php

namespace App\Livewire;

use App\Models\Journal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MyJournals extends Component
{
    public function render()
    {
        $journals = Journal::with(['photos', 'casting'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('livewire.my-journals', [
            'journals' => $journals,
        ]);
    }
}

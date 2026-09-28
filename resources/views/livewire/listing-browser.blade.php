<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Browse Listings') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white p-6 shadow rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search Casting</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Search by name...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Listing Type</label>
                    <select wire:model.live="filterType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Types</option>
                        <option value="for_trade">For Trade</option>
                        <option value="looking_for">Looking For</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Rarity</label>
                    <select wire:model.live="filterRarity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Rarities</option>
                        <option value="mainline">Mainline</option>
                        <option value="th">Treasure Hunt</option>
                        <option value="sth">Super Treasure Hunt</option>
                        <option value="premium">Premium</option>
                        <option value="rlc">RLC</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($listings as $listing)
                <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
                    <div class="mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $listing->type == 'for_trade' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $listing->type == 'for_trade' ? 'For Trade' : 'Looking For' }}
                        </span>
                    </div>
                    
                    @php
                        $castingData = $listing->type == 'for_trade' && $listing->collectionItem ? $listing->collectionItem->casting : $listing->casting;
                        $signal = \App\Models\MarketSignal::where('casting_id', $castingData?->id)->first();
                        $badge = '';
                        if ($signal) {
                            if ($signal->current_multiplier > 1.1 && $signal->target_multiplier >= $signal->current_multiplier) {
                                $badge = '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">🔥 Trending</span>';
                            } elseif ($signal->current_multiplier < 0.9 && $signal->target_multiplier <= $signal->current_multiplier) {
                                $badge = '<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">❄️ Cooling</span>';
                            }
                        }
                    @endphp
                    
                    <h3 class="font-bold text-lg">{{ $castingData?->name ?? 'Unknown' }} ({{ $castingData?->year ?? '-' }}) {!! $badge !!}</h3>
                    <p class="text-sm text-gray-600">By: {{ $listing->user->name }}</p>
                    
                    @if($listing->type == 'for_trade' && $listing->collectionItem)
                        <div class="mt-2 text-sm bg-gray-50 p-2 rounded">
                            <p><span class="font-semibold">Condition:</span> {{ $listing->collectionItem->condition }}</p>
                            <p><span class="font-semibold">Card:</span> {{ $listing->collectionItem->card_condition ?? '-' }}</p>
                        </div>
                        @if($listing->user_id !== auth()->id())
                            @livewire('trade-proposal', ['listingId' => $listing->id], key('proposal-'.$listing->id))
                        @endif
                    @endif
                    
                    @if($listing->notes)
                        <p class="mt-2 text-sm text-gray-500 italic">"{{ $listing->notes }}"</p>
                    @endif
                </div>
            @empty
                <div class="col-span-4 text-center text-gray-500 py-12 bg-white rounded-lg shadow">
                    No listings found matching your criteria.
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $listings->links() }}
        </div>
    </div>
</div>

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">My Collection</h2>
        <button wire:click="$set('showAddModal', true)" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Add Item</button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($collection as $item)
            <div class="bg-white p-4 shadow rounded-lg border border-gray-200">
                <h3 class="font-bold text-lg">{{ $item->casting->name }} ({{ $item->casting->year }})</h3>
                <p class="text-sm text-gray-600">Series: {{ $item->casting->series ?? '-' }} | Rarity: {{ ucfirst($item->casting->rarity_tier) }}</p>
                <div class="mt-2 text-sm">
                    <p><span class="font-semibold">Condition:</span> {{ $item->condition }}</p>
                    <p><span class="font-semibold">Card:</span> {{ $item->card_condition ?? '-' }}</p>
                    <p><span class="font-semibold">Status:</span> {{ ucfirst($item->status) }}</p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button wire:click="deleteItem({{ $item->id }})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-500 py-8">
                Your collection is empty.
            </div>
        @endforelse
    </div>

    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg mx-4 h-full md:h-auto overflow-y-auto">
                <h3 class="text-lg font-bold mb-4">Add to Collection</h3>
                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Casting</label>
                        <select wire:model="selectedCasting" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Select a casting...</option>
                            @foreach($castings as $casting)
                                <option value="{{ $casting->id }}">{{ $casting->name }} ({{ $casting->year }})</option>
                            @endforeach
                        </select>
                        @error('selectedCasting') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Condition</label>
                        <select wire:model="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Sealed/MOC">Sealed / MOC</option>
                            <option value="Mint Loose">Mint Loose</option>
                            <option value="Good">Good</option>
                            <option value="Played">Played</option>
                            <option value="Damaged">Damaged</option>
                        </select>
                        @error('condition') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Card/Blister Condition</label>
                        <input type="text" wire:model="cardCondition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="e.g. Mint, Creased">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Photos</label>
                        <input type="file" wire:model="photos" multiple class="mt-1 block w-full text-sm">
                        @error('photos.*') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="available">Available</option>
                            <option value="for_trade">For Trade</option>
                            <option value="not_for_trade">Not For Trade</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" wire:click="$set('showAddModal', false)" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

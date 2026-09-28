<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Trade Room #') }}{{ $trade->id }} ({{ ucfirst($trade->status) }})
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        <!-- Fairness Indicator -->
        <div class="bg-white p-6 shadow rounded-lg">
            <h3 class="font-bold text-lg mb-2">WFLF Calculator</h3>
            <div class="flex items-center justify-between">
                <div>
                    <p>Initiator Total: <strong>${{ number_format($fairness['initiator_total'], 2) }}</strong></p>
                    <p>Receiver Total: <strong>${{ number_format($fairness['receiver_total'], 2) }}</strong></p>
                </div>
                <div class="text-right">
                    @if($fairness['is_fair'])
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Fair Trade (Diff: {{ number_format($fairness['percentage_diff'], 1) }}%)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Unfair Trade (Diff: {{ number_format($fairness['percentage_diff'], 1) }}%)
                        </span>
                        <p class="text-sm text-red-600 mt-1">Needs Kicker worth ~${{ number_format($fairness['kicker_value_needed'], 2) }}</p>
                    @endif
                </div>
            </div>
            
            @if($trade->status === 'proposed')
                <div class="mt-4 border-t pt-4">
                    <button wire:click="acceptTrade" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50" @if(!$fairness['is_fair']) disabled @endif>
                        Accept Trade
                    </button>
                    @if(session()->has('error'))
                        <span class="text-red-500 text-sm ml-2">{{ session('error') }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Initiator Side -->
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-bold text-lg mb-4">{{ $trade->initiator->name }}'s Offer</h3>
                <ul class="space-y-2">
                    @foreach($trade->items->where('user_id', $trade->initiator_id) as $item)
                        <li class="flex justify-between items-center border-b pb-2">
                            <div>
                                <span class="font-semibold">{{ $item->collectionItem->casting->name }}</span>
                                <span class="text-xs text-gray-500">(${{ number_format($item->collectionItem->computed_value, 2) }})</span>
                            </div>
                            @if(auth()->id() === $trade->initiator_id && $trade->status === 'proposed')
                                <button wire:click="removeItem({{ $item->collectionItem->id }})" class="text-red-500 text-xs hover:underline">Remove</button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Receiver Side -->
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-bold text-lg mb-4">{{ $trade->receiver->name }}'s Offer</h3>
                <ul class="space-y-2">
                    @foreach($trade->items->where('user_id', $trade->receiver_id) as $item)
                        <li class="flex justify-between items-center border-b pb-2">
                            <div>
                                <span class="font-semibold">{{ $item->collectionItem->casting->name }}</span>
                                <span class="text-xs text-gray-500">(${{ number_format($item->collectionItem->computed_value, 2) }})</span>
                            </div>
                            @if(auth()->id() === $trade->receiver_id && $trade->status === 'proposed')
                                <button wire:click="removeItem({{ $item->collectionItem->id }})" class="text-red-500 text-xs hover:underline">Remove</button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @if($trade->status === 'proposed')
            <!-- Add Item -->
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-bold text-lg mb-2">Add Item to Offer</h3>
                <div class="flex gap-2">
                    <select wire:model="selectedItemToAdd" class="flex-1 rounded-md border-gray-300 shadow-sm">
                        <option value="">Select from your collection...</option>
                        @foreach($myItems as $myItem)
                            <option value="{{ $myItem->id }}">{{ $myItem->casting->name }} (${{ number_format($myItem->computed_value, 2) }})</option>
                        @endforeach
                    </select>
                    <button wire:click="addItem" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add</button>
                </div>
            </div>
        @endif

        @if(in_array($trade->status, ['accepted', 'shipped']))
            <!-- Shipping & Checklist -->
            <div class="bg-white p-6 shadow rounded-lg">
                <h3 class="font-bold text-lg mb-4">Shipping & Checklist</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold">My Tracking Number</p>
                        <div class="flex gap-2 mt-1">
                            <input type="text" wire:model="trackingNumber" class="flex-1 rounded-md border-gray-300 shadow-sm">
                            <button wire:click="saveTracking" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                        </div>
                        @if(session()->has('message'))
                            <p class="text-green-600 text-sm mt-1">{{ session('message') }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold">Photos Checked</p>
                        <label class="flex items-center mt-2 cursor-pointer">
                            <input type="checkbox" wire:click="togglePhotosChecked" class="rounded border-gray-300 text-blue-600 shadow-sm"
                                @if(auth()->id() === $trade->initiator_id)
                                    @if($trade->photos_checked_initiator) checked @endif
                                @else
                                    @if($trade->photos_checked_receiver) checked @endif
                                @endif
                            >
                            <span class="ml-2 text-sm text-gray-600">I have verified the condition via photos</span>
                        </label>
                    </div>
                </div>

                @if($trade->status === 'shipped')
                    <div class="mt-6 border-t pt-4">
                        <button wire:click="completeTrade" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Mark as Completed</button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Chat -->
        <div class="bg-white p-6 shadow rounded-lg">
            <h3 class="font-bold text-lg mb-4">Trade Chat</h3>
            <div class="h-64 overflow-y-auto border rounded p-4 mb-4 space-y-2 bg-gray-50">
                @foreach($trade->messages as $msg)
                    <div class="flex flex-col {{ $msg->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                        <span class="text-xs text-gray-500">{{ $msg->user->name }}</span>
                        <div class="px-3 py-2 rounded-lg {{ $msg->user_id === auth()->id() ? 'bg-blue-100' : 'bg-white border' }}">
                            {{ $msg->message }}
                        </div>
                    </div>
                @endforeach
            </div>
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" wire:model="message" class="flex-1 rounded-md border-gray-300 shadow-sm" placeholder="Type a message...">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Send</button>
            </form>
        </div>
    </div>
</div>

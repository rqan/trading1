<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Listings
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session()->has('message'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">
                {{ session('message') }}
            </div>
        @endif
        @if(session()->has('error'))
            <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50">
                {{ session('error') }}
            </div>
        @endif

        {{-- Header + CTA --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                @foreach(['' => 'Semua', 'for_trade' => 'For Trade', 'looking_for' => 'Looking For'] as $value => $label)
                    <button
                        wire:click="$set('filterType', '{{ $value }}')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                            {{ $filterType === $value
                                ? 'bg-orange-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="flex gap-2">
                <a href="{{ route('listings') }}"
                   class="px-4 py-2 text-sm rounded-md bg-white text-orange-600 border border-orange-600 hover:bg-orange-50 font-medium">
                    🔍 Cari Listing
                </a>
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 text-sm rounded-md bg-orange-600 text-white hover:bg-orange-700 font-medium">
                    + Tambah Listing
                </a>
            </div>
        </div>

        {{-- Grid Listing --}}
        @if($listings->isEmpty())
            <div class="bg-white shadow rounded-lg p-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="font-medium">Belum ada listing.</p>
                <p class="text-sm mt-1">
                    Buat listing dari
                    <a href="{{ route('dashboard') }}" class="text-orange-600 hover:underline">koleksi kamu</a>
                    atau tambah wishlist.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($listings as $listing)
                    @php
                        $isTrade = $listing->offeredItems->isNotEmpty();
                        $primaryItem = $isTrade ? $listing->offeredItems->first() : $listing->requestedItems->first();
                        $casting = $primaryItem?->casting;

                        $statusConfig = match($listing->status) {
                            'active'    => ['label' => '🟢 Aktif',    'color' => 'bg-green-100 text-green-800'],
                            'pending'   => ['label' => '⏳ Pending',  'color' => 'bg-yellow-100 text-yellow-800'],
                            'completed' => ['label' => '✅ Selesai',  'color' => 'bg-gray-100 text-gray-600'],
                            'cancelled' => ['label' => '❌ Dibatalkan','color' => 'bg-red-100 text-red-700'],
                            default     => ['label' => $listing->status, 'color' => 'bg-gray-100 text-gray-500'],
                        };

                        $typeConfig = match($isTrade) {
                            true  => ['label' => 'For Trade',   'color' => 'bg-green-50 text-green-700 border-green-200'],
                            false => ['label' => 'Looking For', 'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        };
                    @endphp

                    <div class="bg-white rounded-lg shadow border border-gray-200 flex flex-col">
                        {{-- Card Header --}}
                        <div class="p-4 border-b border-gray-100 flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $casting?->name ?? $listing->title ?? 'Unknown' }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $casting?->year ?? '' }} · {{ $casting?->series ?? '' }}</p>
                            </div>
                            <span class="inline-flex items-center shrink-0 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $typeConfig['color'] }}">
                                {{ $typeConfig['label'] }}
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-4 flex-1 space-y-2 text-sm">
                            @if($isTrade && $primaryItem)
                                <div class="flex justify-between text-gray-600">
                                    <span>Kondisi</span>
                                    <span class="font-medium">{{ $primaryItem->condition ?? 'Unknown' }}</span>
                                </div>
                                @if($primaryItem->condition_notes)
                                <div class="flex justify-between text-gray-600">
                                    <span>Notes</span>
                                    <span class="font-medium">{{ $primaryItem->condition_notes }}</span>
                                </div>
                                @endif
                            @endif

                            @if($listing->notes)
                                <p class="text-gray-500 italic text-xs">"{{ $listing->notes }}"</p>
                            @endif

                            <div class="flex justify-between text-gray-600 pt-1">
                                <span>Dibuat</span>
                                <span class="text-xs text-gray-400">{{ $listing->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Card Footer --}}
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['color'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                            @if($listing->status === 'active')
                                <button
                                    wire:click="cancelListing({{ $listing->id }})"
                                    wire:confirm="Yakin ingin membatalkan listing ini? Item akan dikembalikan ke koleksi."
                                    class="text-xs px-3 py-1 rounded-md bg-red-50 text-red-600 hover:bg-red-100 border border-red-200">
                                    Batalkan
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $listings->links() }}
            </div>
        @endif
    </div>
</div>

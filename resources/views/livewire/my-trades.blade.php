<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Trades
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if(session()->has('message'))
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50">
                {{ session('message') }}
            </div>
        @endif

        {{-- Filter Status --}}
        <div class="bg-white p-4 shadow rounded-lg">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Filter:</span>
                @foreach(['' => 'Semua', 'proposed' => 'Menunggu', 'accepted' => 'Disetujui', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $value => $label)
                    <button
                        wire:click="$set('filterStatus', '{{ $value }}')"
                        class="px-3 py-1 rounded-full text-sm font-medium transition-colors
                            {{ $filterStatus === $value
                                ? 'bg-orange-600 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Daftar Trade --}}
        @if($trades->isEmpty())
            <div class="bg-white shadow rounded-lg p-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 3M21 7.5H7.5"/>
                </svg>
                <p class="font-medium">Belum ada trade.</p>
                <p class="text-sm mt-1">Mulai dengan menjelajahi listing di <a href="{{ route('listings') }}" class="text-orange-600 hover:underline">Market</a>.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($trades as $trade)
                    @php
                        $isInitiator = auth()->id() === $trade->initiator_id;
                        $myRole = $isInitiator ? 'Initiator' : 'Receiver';
                        $partner = $isInitiator ? $trade->receiver : $trade->initiator;
                        $myItems = $trade->items->where('user_id', auth()->id());
                        $theirItems = $trade->items->where('user_id', $partner->id);

                        $statusConfig = [
                            'proposed'  => ['label' => '⏳ Menunggu Persetujuan', 'color' => 'bg-yellow-100 text-yellow-800'],
                            'accepted'  => ['label' => '✅ Disetujui – Siap Kirim',  'color' => 'bg-blue-100 text-blue-800'],
                            'shipped'   => ['label' => '📦 Dalam Pengiriman',         'color' => 'bg-indigo-100 text-indigo-800'],
                            'completed' => ['label' => '🎉 Selesai',                  'color' => 'bg-green-100 text-green-800'],
                            'cancelled' => ['label' => '❌ Dibatalkan',               'color' => 'bg-red-100 text-red-800'],
                        ][$trade->status] ?? ['label' => $trade->status, 'color' => 'bg-gray-100 text-gray-700'];
                    @endphp

                    <div class="bg-white shadow rounded-lg border border-gray-200 overflow-hidden">
                        {{-- Header Trade --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-sm text-gray-700">Trade #{{ $trade->id }}</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['color'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $trade->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($trade->status === 'proposed')
                                    <button
                                        wire:click="cancelTrade({{ $trade->id }})"
                                        wire:confirm="Yakin ingin membatalkan trade ini?"
                                        class="text-xs px-3 py-1 rounded-md bg-red-50 text-red-600 hover:bg-red-100 border border-red-200">
                                        Batalkan
                                    </button>
                                @endif
                                <a href="{{ route('trade.room', $trade->id) }}"
                                   class="text-xs px-3 py-1 rounded-md bg-orange-600 text-white hover:bg-orange-700">
                                    Buka Ruangan →
                                </a>
                            </div>
                        </div>

                        {{-- Body Trade --}}
                        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            {{-- Pihak Saya --}}
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Penawaran Saya ({{ $myRole }})</p>
                                @if($myItems->isEmpty())
                                    <p class="text-sm text-gray-400 italic">Belum ada item ditambahkan.</p>
                                @else
                                    <ul class="space-y-1">
                                        @foreach($myItems as $ti)
                                            <li class="text-sm text-gray-700">
                                                🚗 {{ $ti->collectionItem->casting->name ?? '—' }}
                                                <span class="text-xs text-gray-400">({{ $ti->collectionItem->condition ?? '' }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            {{-- Simbol ⇄ --}}
                            <div class="text-center text-2xl text-gray-300 font-bold hidden md:block">⇄</div>

                            {{-- Pihak Lawan --}}
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">
                                    Penawaran
                                    <a href="{{ route('user.profile', $partner->id) }}" class="text-orange-600 hover:underline">
                                        {{ $partner->name }}
                                    </a>
                                </p>
                                @if($theirItems->isEmpty())
                                    <p class="text-sm text-gray-400 italic">Belum ada item.</p>
                                @else
                                    <ul class="space-y-1">
                                        @foreach($theirItems as $ti)
                                            <li class="text-sm text-gray-700">
                                                🚗 {{ $ti->collectionItem->casting->name ?? '—' }}
                                                <span class="text-xs text-gray-400">({{ $ti->collectionItem->condition ?? '' }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        {{-- Tracking info (jika sudah shipped/completed) --}}
                        @if(in_array($trade->status, ['shipped', 'completed']))
                            <div class="px-5 py-3 bg-indigo-50 border-t border-indigo-100 text-xs text-indigo-700 flex flex-wrap gap-4">
                                <span>📦 Resi {{ $trade->initiator->name }}: <strong>{{ $trade->tracking_initiator ?? '—' }}</strong></span>
                                <span>📦 Resi {{ $trade->receiver->name }}: <strong>{{ $trade->tracking_receiver ?? '—' }}</strong></span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $trades->links() }}
            </div>
        @endif
    </div>
</div>

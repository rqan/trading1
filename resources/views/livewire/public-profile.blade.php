<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Profil: {{ $profileUser->name }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- ======================= --}}
        {{-- Kartu Profil Utama --}}
        {{-- ======================= --}}
        <div class="bg-white shadow rounded-xl p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">

                {{-- Avatar + Nama --}}
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-orange-100 flex items-center justify-center text-2xl font-bold text-orange-600 shrink-0">
                        {{ strtoupper(substr($profileUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $profileUser->name }}</h2>
                        <p class="text-sm text-gray-500">Bergabung {{ $profileUser->created_at->format('M Y') }}</p>
                        @if($profileUser->is_banned)
                            <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                🚫 Akun dibatasi
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Trust Score + Follow --}}
                <div class="flex flex-col items-end gap-3">
                    {{-- Trust Score --}}
                    @php
                        $score = $profileUser->trust_score ?? 100;
                        $scoreColor = $score >= 80 ? 'text-green-600 bg-green-50 border-green-200'
                            : ($score >= 50 ? 'text-yellow-600 bg-yellow-50 border-yellow-200'
                            : 'text-red-600 bg-red-50 border-red-200');
                        $scoreLabel = $score >= 80 ? '🌟 Terpercaya' : ($score >= 50 ? '⚠️ Perhatian' : '🚨 Berisiko');
                    @endphp
                    <div class="text-center px-4 py-2 rounded-lg border {{ $scoreColor }}">
                        <p class="text-2xl font-bold">{{ $score }}</p>
                        <p class="text-xs font-medium">Trust Score</p>
                        <p class="text-xs">{{ $scoreLabel }}</p>
                    </div>

                    {{-- Tombol Follow --}}
                    @if(auth()->id() !== $profileUser->id)
                        <button
                            wire:click="toggleFollow"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ $isFollowing
                                    ? 'bg-gray-100 text-gray-700 hover:bg-red-50 hover:text-red-600'
                                    : 'bg-orange-600 text-white hover:bg-orange-700' }}">
                            {{ $isFollowing ? 'Berhenti Ikuti' : '+ Ikuti' }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Statistik --}}
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800">{{ $completedTrades }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Trade Selesai</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800">{{ $activeTrades }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Trade Aktif</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800">{{ $profileUser->followers->count() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pengikut</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    @if($avgRating)
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($avgRating, 1) }} <span class="text-yellow-400 text-lg">★</span></p>
                    @else
                        <p class="text-2xl font-bold text-gray-400">—</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-0.5">Rating Rata-rata</p>
                </div>
            </div>
        </div>

        {{-- ======================= --}}
        {{-- Koleksi Publik --}}
        {{-- ======================= --}}
        <div class="bg-white shadow rounded-xl p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">🚗 Koleksi ({{ $collection->count() }} item)</h3>
            @if($collection->isEmpty())
                <p class="text-sm text-gray-400 italic">Pengguna ini belum memiliki koleksi yang bisa ditampilkan.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($collection as $item)
                        @php
                            $statusStyle = match($item->status) {
                                'for_trade'  => 'border-green-300 bg-green-50',
                                'available'  => 'border-gray-200 bg-white',
                                default      => 'border-gray-200 bg-gray-50',
                            };
                        @endphp
                        <div class="rounded-lg border {{ $statusStyle }} p-3">
                            <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $item->casting->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $item->casting->year }}</p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs text-gray-500">{{ $item->condition }}</span>
                                @if($item->status === 'for_trade')
                                    <span class="text-xs font-medium text-green-700 bg-green-100 px-1.5 py-0.5 rounded">
                                        For Trade
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ======================= --}}
        {{-- Rating & Ulasan --}}
        {{-- ======================= --}}
        <div class="bg-white shadow rounded-xl p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">⭐ Ulasan dari Trader Lain</h3>
            @if($ratings->isEmpty())
                <p class="text-sm text-gray-400 italic">Belum ada ulasan untuk pengguna ini.</p>
            @else
                <div class="space-y-4">
                    @foreach($ratings as $rating)
                        <div class="border border-gray-100 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="h-8 w-8 rounded-full bg-orange-100 flex items-center justify-center text-sm font-bold text-orange-600">
                                        {{ strtoupper(substr($rating->reviewer->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ $rating->reviewer->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $rating->score ? 'text-yellow-400' : 'text-gray-200' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="text-xs text-gray-500 ml-1">{{ $rating->score }}/5</span>
                                </div>
                            </div>
                            @if($rating->comment)
                                <p class="text-sm text-gray-600 italic">"{{ $rating->comment }}"</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">{{ $rating->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

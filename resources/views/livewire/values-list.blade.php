{{-- ===================================================
     Values List — Daftar Nilai Casting DCastTrade
     =================================================== --}}

<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">📊 Daftar Nilai Casting</h1>
                <p class="text-sm text-slate-500 mt-0.5">Harga referensi berdasarkan supply & demand komunitas, diperbarui otomatis.</p>
            </div>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap gap-3 items-center">

            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama casting atau series..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none">
                </div>
            </div>

            {{-- Filter Rarity --}}
            <select
                wire:model.live="rarity"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                <option value="">Semua Rarity</option>
                @foreach($rarities as $r)
                    <option value="{{ $r }}">{{ strtoupper($r) }}</option>
                @endforeach
            </select>

            {{-- Sort --}}
            <select
                wire:model.live="sortBy"
                class="px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                <option value="value_desc">Nilai ↓ Tertinggi</option>
                <option value="value_asc">Nilai ↑ Terendah</option>
                <option value="name_asc">Nama A–Z</option>
                <option value="name_desc">Nama Z–A</option>
                <option value="rarity">Per Rarity</option>
            </select>

            {{-- Reset --}}
            @if($search || $rarity)
                <button
                    wire:click="$set('search', ''); $set('rarity', '')"
                    class="text-sm text-slate-400 hover:text-orange-600 transition-colors">
                    × Reset
                </button>
            @endif

        </div>

        {{-- Grid Kartu Casting --}}
        @if($castings->isEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-16 text-center text-slate-400">
                <p class="text-4xl mb-3">🔍</p>
                <p class="font-medium text-slate-600">Tidak ada casting yang ditemukan</p>
                <p class="text-sm mt-1">Coba ubah filter atau kata kunci pencarian.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($castings as $casting)
                    @php
                        $multiplier = $casting->marketSignal?->current_multiplier ?? 1.0;
                        $currentPrice = round($casting->base_value * $multiplier);
                        $trend = $casting->trend;

                        $trendIcon  = match($trend) { 'up' => '▲', 'down' => '▼', default => '▬' };
                        $trendColor = match($trend) { 'up' => 'text-green-600', 'down' => 'text-red-500', default => 'text-slate-400' };

                        $rarityColor = match(strtolower($casting->rarity_tier ?? '')) {
                            'sth'      => 'bg-purple-100 text-purple-700',
                            'rlc'      => 'bg-yellow-100 text-yellow-700',
                            'th'       => 'bg-blue-100 text-blue-700',
                            'premium'  => 'bg-orange-100 text-orange-700',
                            'mainline' => 'bg-slate-100 text-slate-600',
                            default    => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <a
                        href="{{ $casting->slug ? route('casting.detail', $casting) : '#' }}"
                        class="group bg-white rounded-xl border border-slate-200 hover:border-orange-300 hover:shadow-md transition-all duration-150 overflow-hidden flex flex-col">

                        {{-- Gambar / Placeholder --}}
                        <div class="h-32 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center overflow-hidden">
                            @if($casting->image_url)
                                <img src="{{ $casting->image_url }}" alt="{{ $casting->name }}"
                                     class="h-full w-full object-contain p-2">
                            @else
                                <span class="text-4xl opacity-30">🚗</span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-3 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-1 mb-1">
                                    <h3 class="text-sm font-bold text-slate-800 group-hover:text-orange-600 leading-tight line-clamp-2">
                                        {{ $casting->name }}
                                    </h3>
                                    <span class="shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $rarityColor }}">
                                        {{ strtoupper($casting->rarity_tier ?? '-') }}
                                    </span>
                                </div>
                                @if($casting->series)
                                    <p class="text-xs text-slate-400 truncate">{{ $casting->series }} {{ $casting->year }}</p>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-slate-400">Nilai saat ini</p>
                                    <p class="text-base font-bold text-slate-900">
                                        Rp{{ number_format($currentPrice, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs {{ $trendColor }} font-semibold">{{ $trendIcon }} {{ ucfirst($trend) }}</p>
                                    <p class="text-[10px] text-slate-400">×{{ number_format($multiplier, 3) }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Paginasi --}}
            <div class="mt-4">
                {{ $castings->links() }}
            </div>
        @endif

        {{-- Info disclaimer --}}
        <p class="text-xs text-slate-400 text-center pb-4">
            💡 Nilai ini adalah <strong>harga referensi komunitas</strong> berdasarkan supply & demand aktual di DCastTrade.
            Bukan harga resmi. Diperbarui otomatis setiap jam.
        </p>

    </div>
</div>

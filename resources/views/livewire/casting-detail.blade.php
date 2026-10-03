{{-- ===================================================
     Casting Detail — Nilai & Histori Harga
     Inspirasinya: elvebredd.com (Values + Chart)
     =================================================== --}}

<div class="py-8">
    <div class="max-w-5xl mx-auto px-4 space-y-6">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-slate-400">
            <a href="{{ route('values') }}" class="hover:text-orange-600">Values</a>
            <span class="mx-2">›</span>
            <span class="text-slate-600 font-medium">{{ $casting->name }}</span>
        </nav>

        {{-- Hero Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="flex flex-col sm:flex-row gap-0">

                {{-- Gambar --}}
                <div class="sm:w-52 h-40 sm:h-auto bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center shrink-0">
                    @if($casting->image_url)
                        <img src="{{ $casting->image_url }}" alt="{{ $casting->name }}"
                             class="h-full w-full object-contain p-4">
                    @else
                        <span class="text-6xl opacity-20">🚗</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 p-6">
                    <div class="flex flex-wrap items-start gap-3 mb-2">
                        <h1 class="text-2xl font-bold text-slate-900">{{ $casting->name }}</h1>
                        @php
                            $rarityColor = match(strtolower($casting->rarity_tier ?? '')) {
                                'sth'      => 'bg-purple-100 text-purple-700',
                                'rlc'      => 'bg-yellow-100 text-yellow-700',
                                'th'       => 'bg-blue-100 text-blue-700',
                                'premium'  => 'bg-orange-100 text-orange-700',
                                'mainline' => 'bg-slate-100 text-slate-600',
                                default    => 'bg-slate-100 text-slate-600',
                            };
                        @endphp
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $rarityColor }}">
                            {{ strtoupper($casting->rarity_tier ?? 'N/A') }}
                        </span>
                    </div>

                    @if($casting->series)
                        <p class="text-slate-500 text-sm mb-4">{{ $casting->series }} · {{ $casting->year }}</p>
                    @endif

                    {{-- Price Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Nilai Saat Ini</p>
                            <p class="text-xl font-bold text-slate-900">
                                Rp{{ number_format($currentPrice, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Rentang 30 Hari</p>
                            <p class="text-sm font-semibold text-slate-700">
                                Rp{{ number_format($range['min'], 0, ',', '.') }}
                                <span class="text-slate-400">–</span>
                                Rp{{ number_format($range['max'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Market Multiplier</p>
                            <p class="text-sm font-bold
                                {{ $multiplier > 1.2 ? 'text-green-600' : ($multiplier < 0.8 ? 'text-red-500' : 'text-slate-700') }}">
                                ×{{ number_format($multiplier, 3) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Tren 7 Hari</p>
                            <p class="text-sm font-bold
                                {{ $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-500' : 'text-slate-500') }}">
                                @if($trend === 'up') ▲ Naik
                                @elseif($trend === 'down') ▼ Turun
                                @else ▬ Stabil
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Market Signal Info --}}
                    @if($signal)
                        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap gap-4 text-sm text-slate-500">
                            <span>📋 <strong>{{ $signal->active_listing_count }}</strong> listing aktif</span>
                            <span>❤️ <strong>{{ $signal->wishlist_count }}</strong> wishlist</span>
                            <span>📊 Basis: <strong>Rp{{ number_format($casting->base_value, 0, ',', '.') }}</strong></span>
                            @if($signal->last_calculated_at)
                                <span>🕐 Update: {{ $signal->last_calculated_at->diffForHumans() }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart Harga Historis --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-800">📈 Histori Harga (30 Hari Terakhir)</h2>
                <span class="text-xs text-slate-400">Dalam Rupiah · Diperbarui tiap jam</span>
            </div>

            @if(count($chartData['data']) <= 1)
                <div class="h-48 flex items-center justify-center text-slate-400 text-sm">
                    <div class="text-center">
                        <p class="text-3xl mb-2">📊</p>
                        <p>Belum ada data histori yang cukup.</p>
                        <p class="text-xs mt-1">Data akan terakumulasi seiring waktu.</p>
                    </div>
                </div>
            @else
                <div
                    x-data="{
                        init() {
                            const ctx = this.$refs.chartCanvas.getContext('2d');
                            const labels = {{ Js::from($chartData['labels']) }};
                            const data   = {{ Js::from($chartData['data']) }};

                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels,
                                    datasets: [{
                                        label: 'Harga (Rp)',
                                        data,
                                        borderColor: '#f97316',
                                        backgroundColor: 'rgba(249, 115, 22, 0.08)',
                                        borderWidth: 2,
                                        pointRadius: 3,
                                        pointBackgroundColor: '#f97316',
                                        fill: true,
                                        tension: 0.35,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: (ctx) => 'Rp' + ctx.raw.toLocaleString('id-ID')
                                            }
                                        }
                                    },
                                    scales: {
                                        y: {
                                            ticks: {
                                                callback: (val) => 'Rp' + val.toLocaleString('id-ID')
                                            },
                                            grid: { color: 'rgba(0,0,0,0.05)' }
                                        },
                                        x: {
                                            grid: { display: false }
                                        }
                                    }
                                }
                            });
                        }
                    }">
                    <div class="h-64">
                        <canvas x-ref="chartCanvas"></canvas>
                    </div>
                </div>
            @endif
        </div>

        {{-- Active Listings untuk Casting ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="font-bold text-slate-800 mb-4">
                🏷️ Listing Aktif untuk Casting Ini
                <span class="ml-2 text-sm font-normal text-slate-400">(maks. 10 terbaru)</span>
            </h2>

            @if($listings->isEmpty())
                <div class="py-8 text-center text-slate-400 text-sm">
                    <p class="text-2xl mb-2">📭</p>
                    <p>Belum ada listing aktif untuk casting ini.</p>
                    <a href="{{ route('listings') }}" class="mt-2 inline-block text-orange-600 hover:underline text-xs">
                        Lihat semua listing →
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($listings as $listing)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm shrink-0">
                                    {{ substr($listing->user->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">
                                        {{ $listing->user->name }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Kondisi: {{ ucfirst($listing->collectionItem->condition ?? 'N/A') }}
                                        · {{ $listing->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $listing->type === 'for_trade' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                    {{ $listing->type === 'for_trade' ? 'Trade' : 'Jual' }}
                                </span>
                                <a
                                    href="{{ route('listings') }}"
                                    class="text-xs text-orange-600 hover:underline font-medium">
                                    Lihat →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('listings') }}" class="text-sm text-orange-600 hover:underline">
                        Lihat semua listing di market →
                    </a>
                </div>
            @endif
        </div>

        {{-- Disclaimer --}}
        <p class="text-xs text-slate-400 text-center pb-4">
            ⚠️ Nilai ini adalah <strong>harga referensi</strong> berbasis data komunitas, bukan harga resmi.
            Harga aktual dapat berbeda tergantung kondisi item, negosiasi, dan kesepakatan antar pengguna.
        </p>

    </div>
</div>

@push('scripts')
    {{-- Chart.js dimuat hanya di halaman ini via jsDelivr (tidak perlu npm install) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
@endpush

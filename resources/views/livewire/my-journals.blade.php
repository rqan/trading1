<div class="max-w-4xl mx-auto py-8 px-4 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">📔 Jurnal Saya</h1>
        <a href="{{ route('journals.create') }}"
           wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            ✏️ Tulis jurnal
        </a>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    {{-- Daftar jurnal --}}
    @forelse ($journals as $journal)
        <a href="{{ route('journals.show', $journal) }}"
           wire:navigate
           class="flex gap-4 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">

            {{-- Foto sampul --}}
            <div class="w-28 h-28 flex-shrink-0 bg-gray-100">
                @if ($journal->coverPhoto())
                    <img src="{{ $journal->coverPhoto()->url }}"
                         alt="Foto sampul"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-3xl text-gray-300">🚗</div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 py-3 pr-4 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-semibold text-gray-900 truncate">{{ $journal->title }}</h2>
                    @if ($journal->visibility === 'private')
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 bg-gray-100 text-gray-500">🔒 Pribadi</span>
                    @elseif ($journal->show_in_feed)
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 bg-blue-100 text-blue-700">📢 Di Feed</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0 bg-green-100 text-green-700">🌐 Publik</span>
                    @endif
                </div>

                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                    <span>📅 {{ $journal->acquired_at->translatedFormat('d M Y') }}</span>
                    <span>🛒 {{ $journal->acquisitionLabel() }}</span>
                    @if ($journal->place_name)
                        <span>📍 {{ $journal->place_name }}</span>
                    @endif
                </div>

                @if ($journal->casting)
                    <p class="mt-1 text-xs text-gray-400">{{ $journal->casting->name }} ({{ $journal->casting->year }})</p>
                @endif
            </div>
        </a>

    @empty
        {{-- Empty state --}}
        <div class="text-center py-20 text-gray-400 space-y-3">
            <div class="text-6xl">📔</div>
            <p class="text-lg font-medium text-gray-600">Belum ada jurnal</p>
            <p class="text-sm">Abadikan momen pertama kali Anda mendapatkan Hot Wheels!</p>
            <a href="{{ route('journals.create') }}"
               wire:navigate
               class="inline-block mt-2 px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
                ✏️ Tulis jurnal pertama
            </a>
        </div>
    @endforelse

</div>

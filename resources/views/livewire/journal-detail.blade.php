<div
    x-data="journalDetailMap(@this)"
    x-init="init()"
    class="max-w-2xl mx-auto py-8 px-4 space-y-6"
>
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    {{-- ══ GALERI FOTO ══ --}}
    @php $photos = $journal->photos; @endphp
    @if ($photos->count())
        <div class="space-y-2">
            <div class="w-full aspect-video bg-gray-100 rounded-xl overflow-hidden">
                <img src="{{ $photos->first()->url }}"
                     alt="Foto utama"
                     class="w-full h-full object-cover">
            </div>
            @if ($photos->count() > 1)
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($photos->skip(1) as $photo)
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ $photo->url }}"
                                 alt="Foto jurnal"
                                 class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="w-full aspect-video bg-gray-100 rounded-xl flex items-center justify-center text-5xl text-gray-300">
            🚗
        </div>
    @endif

    {{-- ══ HEADER INFO ══ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
        <div class="flex items-start justify-between gap-3">
            <h1 class="text-xl font-bold text-gray-900 leading-tight">{{ $journal->title }}</h1>
            <span class="text-xs px-2 py-1 rounded-full flex-shrink-0
                {{ $journal->visibility === 'public' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $journal->visibility === 'public' ? '🌐 Publik' : '🔒 Pribadi' }}
            </span>
        </div>

        {{-- Chip info --}}
        <div class="flex flex-wrap gap-2 text-sm">
            <span class="px-3 py-1 bg-gray-100 rounded-full text-gray-700">
                📅 {{ $journal->acquired_at->translatedFormat('d M Y') }}
            </span>
            <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full">
                🛒 {{ $journal->acquisitionLabel() }}
            </span>
            @if ($journal->price && $journal->acquisition_type === 'beli')
                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full">
                    💰 Rp {{ number_format($journal->price, 0, ',', '.') }}
                </span>
            @endif
            @if ($journal->casting)
                <a href="{{ route('casting.detail', $journal->casting) }}"
                   wire:navigate
                   class="px-3 py-1 bg-yellow-50 text-yellow-700 rounded-full hover:bg-yellow-100 transition">
                    🚗 {{ $journal->casting->name }} ({{ $journal->casting->year }})
                </a>
            @endif
        </div>

        {{-- Pemilik (untuk jurnal publik yang dilihat orang lain) --}}
        @if (!$isOwner)
            <div class="text-sm text-gray-500">
                Oleh
                <a href="{{ route('user.profile', $journal->user) }}"
                   wire:navigate
                   class="font-medium text-gray-700 hover:underline">
                    {{ $journal->user->name }}
                </a>
            </div>
        @endif
    </div>

    {{-- ══ CERITA ══ --}}
    @if ($journal->story)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Cerita</h2>
            <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $journal->story }}</p>
        </div>
    @endif

    {{-- ══ LOKASI ══ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-3">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Lokasi</h2>
        @if ($journal->place_name)
            <p class="font-medium text-gray-800">{{ $journal->place_name }}</p>
        @endif
        @if ($journal->place_address)
            <p class="text-sm text-gray-500">{{ $journal->place_address }}</p>
        @endif

        <div id="detail-map" class="w-full h-48 rounded-lg border border-gray-200 z-0"></div>

        <a href="{{ $journal->googleMapsUrl() }}"
           target="_blank"
           rel="noopener"
           class="inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
            🗺️ Buka di Google Maps ↗
        </a>
    </div>

    {{-- ══ REAKSI ══ --}}
    @if ($journal->visibility === 'public' || $isOwner)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Reaksi</h2>
            <div class="flex flex-wrap gap-2">
                @foreach (\App\Livewire\JournalDetail::EMOJIS as $emoji)
                    @php $count = $reactionCounts[$emoji] ?? 0; @endphp
                    <button
                        wire:click="react('{{ $emoji }}')"
                        wire:key="react-{{ $emoji }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-sm transition
                            {{ $myReaction === $emoji
                                ? 'bg-red-50 border-red-400 text-red-700 font-semibold'
                                : 'border-gray-200 text-gray-600 hover:border-gray-400 hover:bg-gray-50' }}">
                        <span>{{ $emoji }}</span>
                        @if ($count > 0)
                            <span>{{ $count }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══ KOMENTAR ══ --}}
    @if ($journal->visibility === 'public' || $isOwner)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                Komentar ({{ $journal->comments->count() }})
            </h2>

            {{-- Form komentar baru --}}
            <form wire:submit.prevent="postComment" class="flex gap-2">
                <input type="text"
                       wire:model="newComment"
                       placeholder="Tulis komentar…"
                       maxlength="1000"
                       class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 disabled:opacity-50">
                    Kirim
                </button>
            </form>
            @error('newComment') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

            {{-- Daftar komentar --}}
            <div class="space-y-3">
                @forelse ($journal->comments as $comment)
                    <div class="flex gap-3" wire:key="comment-{{ $comment->id }}">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm font-bold text-gray-500">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between gap-2">
                                <span class="text-sm font-semibold text-gray-800">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-0.5">{{ $comment->content }}</p>
                        </div>
                        @if ($isOwner || $comment->user_id === auth()->id())
                            <button wire:click="deleteComment({{ $comment->id }})"
                                    wire:confirm="Hapus komentar ini?"
                                    class="text-gray-300 hover:text-red-500 text-xs flex-shrink-0 self-start">
                                ✕
                            </button>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ══ AKSI PEMILIK ══ --}}
    @if ($isOwner)
        <div class="flex gap-3 pb-4">
            <a href="{{ route('journals.edit', $journal) }}"
               wire:navigate
               class="flex-1 text-center py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition">
                ✏️ Edit
            </a>
            <button
                wire:click="delete"
                wire:confirm="Hapus jurnal ini? Semua foto akan ikut terhapus secara permanen."
                class="flex-1 py-2.5 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm font-medium transition">
                🗑️ Hapus
            </button>
        </div>
    @endif

</div>

@pushOnce('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endPushOnce

@pushOnce('scripts')
<script>
window._leafletReady = window._leafletReady || new Promise((resolve) => {
    if (typeof window.L !== 'undefined') { resolve(window.L); return; }
    const s = document.createElement('script');
    s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    s.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV/XN/WPeE=';
    s.crossOrigin = '';
    s.onload = () => resolve(window.L);
    document.head.appendChild(s);
});

function journalDetailMap() {
    return {
        map: null,

        async init() {
            await window._leafletReady;

            const lat = {{ $journal->latitude }};
            const lng = {{ $journal->longitude }};

            if (this.map) { this.map.remove(); this.map = null; }

            const el = document.getElementById('detail-map');
            if (!el) return;

            this.map = L.map(el, { zoomControl: true, dragging: true }).setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(this.map);

            L.marker([lat, lng]).addTo(this.map);

            setTimeout(() => this.map && this.map.invalidateSize(), 200);
        }
    };
}
</script>
@endPushOnce

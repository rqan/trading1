<div
    x-data="journalMap(@this)"
    x-init="init()"
    class="max-w-2xl mx-auto py-8 px-4 space-y-8"
>
    {{-- Header --}}
    <h1 class="text-2xl font-bold text-gray-800">
        {{ $journal ? '✏️ Edit Jurnal' : '✏️ Tulis Jurnal' }}
    </h1>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg space-y-1">
            @foreach ($errors->all() as $e)
                <p>• {{ $e }}</p>
            @endforeach
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">

        {{-- ══ BAGIAN 1: MOMENNYA ══════════════════════════════════ --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-base font-semibold text-gray-700 border-b pb-2">1. Momennya</h2>

            {{-- Judul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Judul <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       wire:model="title"
                       placeholder="mis. Treasure Hunt pertama!"
                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Casting (autocomplete) --}}
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Casting</label>
                <div class="flex gap-2">
                    <input type="text"
                           wire:model.live.debounce.300ms="castingSearch"
                           placeholder="Ketik nama casting..."
                           class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500"
                           autocomplete="off">
                    @if ($castingId)
                        <button type="button" wire:click="clearCasting"
                                class="px-3 py-2 text-xs text-gray-500 hover:text-red-600 border border-gray-300 rounded-lg">
                            ✕
                        </button>
                    @endif
                </div>
                @if ($castingId)
                    <p class="mt-1 text-xs text-green-600">✓ Casting terpilih (ID: {{ $castingId }})</p>
                @endif
                @if ($showCastingDropdown && count($castingSuggestions))
                    <ul class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg text-sm max-h-48 overflow-y-auto">
                        @foreach ($castingSuggestions as $c)
                            <li>
                                <button type="button"
                                        wire:click="selectCasting({{ $c['id'] }}, '{{ addslashes($c['name']) }}')"
                                        class="w-full text-left px-4 py-2 hover:bg-red-50">
                                    <span class="font-medium">{{ $c['name'] }}</span>
                                    <span class="text-gray-400 text-xs ml-1">({{ $c['year'] }}{{ $c['series'] ? ' · ' . $c['series'] : '' }})</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @error('castingId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Cara mendapatkan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cara Mendapatkan <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach (['beli' => '🛒 Beli', 'hadiah' => '🎁 Hadiah', 'tukar' => '🔄 Tukar', 'temuan' => '🔍 Temuan'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border text-sm transition
                            {{ $acquisitionType === $val ? 'bg-red-50 border-red-400 text-red-700 font-medium' : 'border-gray-300 text-gray-600 hover:border-gray-400' }}">
                            <input type="radio" wire:model.live="acquisitionType" value="{{ $val }}" class="sr-only">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('acquisitionType') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Tanggal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       wire:model="acquiredAt"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                @error('acquiredAt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Harga (hanya tampil saat beli) --}}
            @if ($acquisitionType === 'beli')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number"
                           wire:model="price"
                           min="0"
                           placeholder="mis. 15000"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                    @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            {{-- Cerita --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cerita</label>
                <textarea wire:model.live="story"
                          rows="4"
                          maxlength="2000"
                          placeholder="Ceritakan momen ini..."
                          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500 resize-none"></textarea>
                <p class="mt-1 text-xs text-gray-400 text-right">{{ strlen($story) }}/2000</p>
                @error('story') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </section>

        {{-- ══ BAGIAN 2: LOKASI ════════════════════════════════════ --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-700 border-b pb-2">2. Lokasi <span class="text-red-500">*</span></h2>

            {{-- Search box --}}
            <div x-data="{ query: '', results: [], loading: false, searched: false }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Tempat</label>
                <div class="flex gap-2">
                    <input type="text"
                           x-model="query"
                           @keydown.enter.prevent="searchPlace()"
                           placeholder="mis. Hypermart Kelapa Gading..."
                           class="flex-1 rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                    <button type="button"
                            @click="searchPlace()"
                            :disabled="loading"
                            class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm hover:bg-gray-200 disabled:opacity-50">
                        <span x-show="!loading">🔍</span>
                        <span x-show="loading">⏳</span>
                    </button>
                    <button type="button"
                            @click="useMyLocation()"
                            class="px-3 py-2 bg-blue-50 border border-blue-300 text-blue-600 rounded-lg text-sm hover:bg-blue-100"
                            title="Gunakan lokasi saya">
                        📍
                    </button>
                </div>

                {{-- Hasil pencarian --}}
                <template x-if="results.length > 0">
                    <ul class="mt-1 border border-gray-200 rounded-lg bg-white shadow text-sm max-h-44 overflow-y-auto">
                        <template x-for="(r, i) in results" :key="i">
                            <li>
                                <button type="button"
                                        @click="selectPlace(r); results = []"
                                        class="w-full text-left px-3 py-2 hover:bg-red-50">
                                    <span x-text="r.display_name" class="line-clamp-2"></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </template>
                <template x-if="searched && results.length === 0">
                    <p class="mt-1 text-xs text-gray-500">Tempat tidak ditemukan. Coba klik langsung di peta.</p>
                </template>
            </div>

            {{-- Peta Leaflet --}}
            <div id="journal-map" class="w-full h-64 rounded-lg border border-gray-200 z-0"></div>

            {{-- Koordinat tersimpan (info) --}}
            @if ($latitude && $longitude)
                <p class="text-xs text-green-600">✓ Koordinat: {{ number_format($latitude, 5) }}, {{ number_format($longitude, 5) }}</p>
            @endif
            @error('latitude') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

            {{-- Nama tempat & alamat (terisi otomatis, bisa diedit) --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Tempat</label>
                    <input type="text"
                           wire:model="placeName"
                           placeholder="Terisi otomatis dari peta"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text"
                           wire:model="placeAddress"
                           placeholder="Terisi otomatis dari peta"
                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </section>

        {{-- ══ BAGIAN 3: FOTO ══════════════════════════════════════ --}}
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-700 border-b pb-2">3. Foto <span class="text-gray-400 text-xs font-normal">(opsional, maks 3)</span></h2>

            {{-- Foto lama (mode edit) --}}
            @if (count($existingPhotos))
                <div class="grid grid-cols-3 gap-3">
                    @foreach ($existingPhotos as $i => $ep)
                        <div class="relative">
                            <img src="{{ $ep['url'] }}" class="w-full h-24 object-cover rounded-lg border
                                {{ $ep['delete'] ? 'opacity-30 border-red-400' : 'border-gray-200' }}">
                            <button type="button"
                                    wire:click="markPhotoDelete({{ $i }})"
                                    class="absolute top-1 right-1 text-xs rounded-full px-1.5 py-0.5 font-bold
                                        {{ $ep['delete'] ? 'bg-red-500 text-white' : 'bg-black/50 text-white hover:bg-red-500' }}">
                                {{ $ep['delete'] ? '↩' : '✕' }}
                            </button>
                            @if ($ep['delete'])
                                <span class="absolute bottom-1 left-1 text-xs bg-red-500 text-white rounded px-1">Akan dihapus</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Upload foto baru --}}
            @php
                $kept = collect($existingPhotos)->where('delete', false)->count();
                $slots = 3 - $kept;
            @endphp

            @if ($slots > 0)
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Tambah foto baru (maks {{ $slots }} lagi, maks 4 MB/foto)
                    </label>
                    <input type="file"
                           wire:model="newPhotos"
                           accept="image/*"
                           multiple
                           class="block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-lg file:border-0
                               file:text-sm file:font-semibold
                               file:bg-red-50 file:text-red-700
                               hover:file:bg-red-100">

                    @if ($photoWarning)
                        <p class="mt-1 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded px-2 py-1">
                            ⚠️ {{ $photoWarning }}
                        </p>
                    @endif

                    @error('newPhotos.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Pratinjau foto baru --}}
                @if (count($newPhotos))
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($newPhotos as $i => $photo)
                            <div class="relative">
                                <img src="{{ $photo->temporaryUrl() }}"
                                     class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                <button type="button"
                                        wire:click="removeNewPhoto({{ $i }})"
                                        class="absolute top-1 right-1 bg-black/50 text-white text-xs rounded-full px-1.5 py-0.5 hover:bg-red-500">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <p class="text-xs text-gray-500 italic">3 foto sudah terisi. Tandai foto lama untuk dihapus agar bisa menambah yang baru.</p>
            @endif
        </section>

        {{-- ══ VISIBILITAS & SIMPAN ════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Siapa yang bisa melihat?</label>
                <div class="space-y-2">

                    {{-- Pribadi --}}
                    <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition
                        {{ $visibility === 'private' ? 'bg-gray-50 border-gray-400' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="visibility" value="private" class="mt-0.5">
                        <div>
                            <span class="text-sm font-medium text-gray-800">🔒 Pribadi</span>
                            <p class="text-xs text-gray-500 mt-0.5">Hanya Anda yang bisa melihat.</p>
                        </div>
                    </label>

                    {{-- Publik --}}
                    <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition
                        {{ $visibility === 'public' ? 'bg-green-50 border-green-400' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="visibility" value="public" class="mt-0.5">
                        <div>
                            <span class="text-sm font-medium text-gray-800">🌐 Publik</span>
                            <p class="text-xs text-gray-500 mt-0.5">Pengguna lain bisa melihat via profil atau tautan langsung.</p>
                        </div>
                    </label>

                    {{-- Sub-opsi: tampilkan di feed (hanya muncul jika publik) --}}
                    @if ($visibility === 'public')
                        <div class="ml-6 mt-1 p-3 bg-blue-50 border border-blue-200 rounded-lg space-y-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       wire:model.live="showInFeed"
                                       class="rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-blue-800">📢 Tampilkan di Community Feed</span>
                            </label>
                            <p class="text-xs text-blue-600 pl-6">
                                Jika dicentang, jurnal akan muncul di feed komunitas sehingga lebih mudah ditemukan pengguna lain.
                            </p>

                            {{-- Peringatan privasi lokasi --}}
                            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded px-2 py-1.5 mt-2">
                                ⚠️ <strong>Perhatian:</strong> Jurnal publik menampilkan koordinat lokasi tepat Anda kepada semua pengguna. Hindari membagikan lokasi yang sensitif seperti rumah.
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="flex-1 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition disabled:opacity-60">
                    <span wire:loading.remove>💾 Simpan jurnal</span>
                    <span wire:loading>Menyimpan…</span>
                </button>
                @if ($journal)
                    <a href="{{ route('journals.show', $journal) }}"
                       wire:navigate
                       class="px-4 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">
                        Batal
                    </a>
                @else
                    <a href="{{ route('my.journals') }}"
                       wire:navigate
                       class="px-4 py-3 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 text-sm">
                        Batal
                    </a>
                @endif
            </div>
        </div>

    </form>
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

function journalMap(livewire) {
    return {
        map: null,
        marker: null,

        async init() {
            await window._leafletReady;

            const initLat  = {{ $latitude  ?? -6.2088 }};
            const initLng  = {{ $longitude ?? 106.8456 }};
            const hasCoord = {{ ($latitude && $longitude) ? 'true' : 'false' }};

            if (this.map) { this.map.remove(); this.map = null; }

            const el = document.getElementById('journal-map');
            if (!el) return;

            this.map = L.map(el).setView([initLat, initLng], hasCoord ? 15 : 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(this.map);

            if (hasCoord) {
                this.marker = L.marker([initLat, initLng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', (e) => this.onMarkerMove(e.target.getLatLng()));
            }

            this.map.on('click', (e) => this.onMapClick(e.latlng));

            setTimeout(() => this.map && this.map.invalidateSize(), 200);
        },

        onMapClick(latlng) {
            this.placeMarker(latlng.lat, latlng.lng);
            this.reverseGeocode(latlng.lat, latlng.lng);
        },

        onMarkerMove(latlng) {
            this.reverseGeocode(latlng.lat, latlng.lng);
        },

        placeMarker(lat, lng) {
            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);
                this.marker.on('dragend', (e) => this.onMarkerMove(e.target.getLatLng()));
            }
            this.map.setView([lat, lng], 16);
        },

        async reverseGeocode(lat, lng) {
            try {
                const res  = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`,
                    { headers: { 'Accept-Language': 'id' } }
                );
                const data = await res.json();
                livewire.call('setLocation', lat, lng,
                    data.name || data.display_name?.split(',')[0] || '',
                    data.display_name || ''
                );
            } catch {
                livewire.call('setLocation', lat, lng, '', '');
            }
        },

        async searchPlace() {
            const q = this.$data.query?.trim();
            if (!q) return;
            this.$data.loading  = true;
            this.$data.searched = false;
            try {
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5&countrycodes=id`,
                    { headers: { 'Accept-Language': 'id' } }
                );
                this.$data.results  = await res.json();
                this.$data.searched = true;
            } catch {
                this.$data.results  = [];
                this.$data.searched = true;
            } finally {
                this.$data.loading = false;
            }
        },

        selectPlace(result) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            this.placeMarker(lat, lng);
            livewire.call('setLocation', lat, lng,
                result.display_name?.split(',')[0] || '',
                result.display_name || ''
            );
        },

        useMyLocation() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung geolokasi. Silakan cari atau klik peta.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.placeMarker(pos.coords.latitude, pos.coords.longitude);
                    this.reverseGeocode(pos.coords.latitude, pos.coords.longitude);
                },
                () => {
                    alert('Izin lokasi ditolak. Silakan cari nama tempat atau klik peta secara manual.');
                }
            );
        },
    };
}
</script>
@endPushOnce


<x-layouts.static title="Tentang DCastTrade" description="DCastTrade adalah platform komunitas dan pertukaran diecast / Hot Wheels terpercaya di Indonesia.">

    <article class="max-w-none">

        {{-- Hero --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center h-20 w-20 rounded-2xl bg-orange-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-slate-900 mb-3">DCastTrade</h1>
            <p class="text-xl text-slate-500 max-w-lg mx-auto">
                Platform komunitas dan pertukaran <em>diecast</em> yang dibangun oleh kolektor, untuk kolektor.
            </p>
        </div>

        {{-- Misi --}}
        <div class="bg-white rounded-xl border border-slate-200 p-8 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">🎯 Misi Kami</h2>
            <p class="text-slate-600 leading-relaxed">
                DCastTrade hadir untuk memecahkan masalah klasik kolektor diecast: menemukan lawan trade yang tepat
                dan melakukan pertukaran secara aman. Kami percaya bahwa hobi barter diecast seharusnya menyenangkan,
                transparan, dan bebas dari rasa khawatir ditipu.
            </p>
            <p class="text-slate-600 leading-relaxed mt-3">
                Dengan sistem <strong>Trust Score</strong>, <strong>WFLF Calculator</strong> (fairness indicator),
                dan trade room real-time, setiap transaksi di DCastTrade bisa dilacak, diverifikasi, dan dilindungi.
            </p>
        </div>

        {{-- Fitur Unggulan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            @foreach([
                ['🤝', 'Trade Room Real-Time', 'Chat, tambah item, dan negosiasi langsung dengan lawan trade dalam satu ruangan yang ter-update secara instan.'],
                ['⚖️', 'WFLF Calculator', 'Sistem kalkulasi keadilan harga otomatis berdasarkan nilai pasar casting, mencegah trade yang tidak seimbang.'],
                ['⭐', 'Trust Score', 'Reputasi pengguna dibangun dari riwayat trade yang sukses. Semakin sering trade dengan baik, semakin tinggi skormu.'],
                ['🔔', 'Smart Matching', 'Dapatkan notifikasi otomatis saat ada pengguna yang punya casting yang kamu cari, atau mencari casting yang kamu punya.'],
                ['📦', 'Tracking Terintegrasi', 'Input nomor resi langsung di trade room. Trade berstatus shipped otomatis saat kedua pihak memasukkan resi.'],
                ['🌐', 'Komunitas Feed', 'Bagikan koleksi, tanya-jawab, dan ikuti kolektor lain dalam feed komunitas yang terintegrasi dengan data koleksimu.'],
            ] as [$icon, $title, $desc])
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="text-2xl mb-2">{{ $icon }}</div>
                    <h3 class="font-bold text-slate-800 mb-1">{{ $title }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        {{-- Tech Stack --}}
        <div class="bg-slate-800 rounded-xl p-6 mb-6 text-white">
            <h2 class="font-bold text-lg mb-3">🛠️ Dibangun dengan</h2>
            <div class="flex flex-wrap gap-2">
                @foreach(['Laravel 13', 'Livewire 3', 'Filament 3', 'Tailwind CSS 4', 'Alpine.js', 'Laravel Reverb (WebSocket)', 'SQLite / MySQL', 'Vite'] as $tech)
                    <span class="px-3 py-1 bg-slate-700 rounded-full text-sm text-slate-200">{{ $tech }}</span>
                @endforeach
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center py-8">
            <p class="text-slate-500 mb-4">Bergabunglah dengan komunitas kolektor diecast yang terus berkembang.</p>
            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('register') }}"
                   class="px-6 py-3 bg-orange-600 text-white font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                    Daftar Sekarang — Gratis
                </a>
                <a href="{{ route('aturan') }}"
                   class="px-6 py-3 bg-white text-slate-700 font-medium rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                    Baca Aturan
                </a>
            </div>
        </div>

    </article>

</x-layouts.static>

<x-app-layout>
    <x-slot name="title">Listing Diecast - DCastTrade</x-slot>

    <div class="space-y-6">
        <header class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Listing Terbaru</h1>
                <p class="text-slate-500 text-sm mt-1">Temukan diecast incaranmu hari ini.</p>
            </div>
            {{-- Tombol Filter dengan state Optimistic UI (Alpine) --}}
            <button class="min-h-[44px] px-4 py-2 bg-orange-100 text-orange-700 font-semibold rounded-lg active:scale-95 transition-transform">
                Filter
            </button>
        </header>

        {{-- Grid responsif: 2 kolom di mobile, 3 di tablet, 4 di desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {{-- Contoh 4 item. Di dunia nyata di-looping dengan data dari controller --}}
            @for ($i = 1; $i <= 4; $i++)
            <article class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                {{-- 
                    Container Aspect Ratio (Tailwind aspect-video atau pb-[100%]) 
                    Mencegah CLS (Cumulative Layout Shift) saat gambar dimuat 
                --}}
                <div class="relative w-full pb-[75%] bg-slate-100">
                    {{-- 
                        Gambar HANYA dimuat jika masuk viewport (loading="lazy").
                        Jika ini adalah elemen pertama di halaman (Hero), ganti lazy jadi fetchpriority="high"
                    --}}
                    <img 
                        src="https://placehold.co/400x300/e2e8f0/64748b?text=Diecast+{{ $i }}" 
                        alt="Foto Diecast {{ $i }}"
                        loading="{{ $i <= 2 ? 'eager' : 'lazy' }}"
                        decoding="async"
                        fetchpriority="{{ $i <= 2 ? 'high' : 'auto' }}"
                        width="400"
                        height="300"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                </div>
                
                <div class="p-3 flex-1 flex flex-col">
                    <h2 class="font-semibold text-slate-900 text-sm leading-tight line-clamp-2">Hotwheels Datsun 510 Wagon {{ $i }}</h2>
                    <p class="text-xs text-slate-500 mt-1">Kondisi: Card Mulus</p>
                    
                    <div class="mt-auto pt-3 flex items-center justify-between">
                        <span class="font-bold text-orange-600 text-sm">Est. Rp 150k</span>
                        {{-- Tombol Aksi --}}
                        @auth
                            <a href="/trade/{{ $i }}" class="min-h-[44px] min-w-[44px] flex items-center justify-center bg-slate-900 text-white rounded-lg px-3 text-xs font-medium hover:bg-slate-800 transition-colors">
                                Trade
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="min-h-[44px] min-w-[44px] flex items-center justify-center bg-slate-900 text-white rounded-lg px-3 text-xs font-medium hover:bg-slate-800 transition-colors">
                                Trade
                            </a>
                        @endauth
                    </div>
                </div>
            </article>
            @endfor
        </div>

        {{-- Skeleton Loading state untuk Pagination (Alpine.js) --}}
        <div x-data="{ loading: false }" class="mt-8 text-center">
            <button 
                @click="loading = true; setTimeout(() => loading = false, 1000)" 
                x-show="!loading"
                class="min-h-[44px] inline-flex items-center justify-center px-6 py-2 border border-slate-300 rounded-full text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors"
            >
                Muat Lebih Banyak
            </button>
            
            <div x-show="loading" style="display: none;" class="flex justify-center">
                {{-- SVG Spinner --}}
                <svg class="animate-spin h-6 w-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
</x-app-layout>

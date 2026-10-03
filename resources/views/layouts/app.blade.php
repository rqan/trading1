<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">

    {{-- Title dinamis: Livewire #[Title] → $title slot → fallback --}}
    <title>{{ isset($title) ? $title . ' – DCastTrade' : 'DCastTrade – Platform Diecast Indonesia' }}</title>

    {{-- Meta Description dinamis --}}
    <meta name="description" content="{{ $description ?? 'Temukan, tawarkan, dan tukar koleksi diecast & Hot Wheels dengan aman bersama komunitas DCastTrade.' }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, WhatsApp, Telegram preview) --}}
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="DCastTrade">
    <meta property="og:title"       content="{{ isset($title) ? $title . ' – DCastTrade' : 'DCastTrade – Platform Diecast Indonesia' }}">
    <meta property="og:description" content="{{ $description ?? 'Temukan, tawarkan, dan tukar koleksi diecast & Hot Wheels dengan aman.' }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:image"       content="{{ $ogImage ?? asset('og-default.png') }}">

    {{-- Twitter / X Card --}}
    <meta name="twitter:card"        content="summary">
    <meta name="twitter:title"       content="{{ isset($title) ? $title . ' – DCastTrade' : 'DCastTrade' }}">
    <meta name="twitter:description" content="{{ $description ?? 'Platform barter diecast / Hot Wheels terpercaya.' }}">

    {{-- Preconnect ke origin --}}
    <link rel="preconnect" href="{{ config('app.url') }}">

    {{-- PWA / Mobile --}}
    <meta name="theme-color" content="#ea580c">

    {{-- SYSTEM FONTS ONLY: Zero network request, CLS 0 --}}
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slot untuk style tambahan per-halaman (misal: Leaflet CSS di halaman jurnal) --}}
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 overflow-x-hidden">
    
    {{-- Notifikasi Offline Minimalis --}}
    <div x-data="{ offline: !navigator.onLine }" 
         @offline.window="offline = true" 
         @online.window="offline = false"
         x-show="offline" 
         style="display: none;"
         class="bg-red-600 text-white text-sm py-2 px-4 text-center fixed top-0 w-full z-50">
        Koneksi terputus. Beberapa fitur mungkin tidak berjalan.
    </div>

    {{-- Navbar Utama (Responsif, Mobile First) --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between gap-2">
            {{-- Logo --}}
            <a href="/" class="font-bold text-lg text-orange-600 tracking-tight flex items-center gap-2 min-h-[44px] shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
                DCastTrade
            </a>

            {{-- Nav Links (desktop) --}}
            @auth
            <div class="hidden sm:flex items-center gap-1 flex-1 justify-center">
                @php $cur = request()->path(); @endphp
                <a href="{{ route('values') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'values') || str_starts_with($cur,'casting') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    Values
                </a>
                <a href="{{ route('listings') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'listings') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    Market
                </a>
                <a href="{{ route('club.feed') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'community') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    Club
                </a>
                <a href="{{ route('my.trades') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'my-trades') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    My Trades
                </a>
                <a href="{{ route('my.listings') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'my-listings') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    My Listings
                </a>
                <a href="{{ route('my.journals') }}"
                   class="min-h-[44px] px-3 py-2 text-sm font-medium transition-colors
                          {{ str_starts_with($cur,'journals') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-600 hover:text-orange-600' }}">
                    📔 Jurnal
                </a>
            </div>

            {{-- Notification Bell (desktop) --}}
            <div class="hidden sm:flex items-center">
                @livewire('notification-bell')
            </div>

            {{-- User Dropdown (desktop) --}}
            <div class="hidden sm:block relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2 min-h-[44px] px-3 py-2 text-sm font-medium text-slate-600 hover:text-orange-600 transition-colors">
                    <span class="h-7 w-7 rounded-full bg-orange-100 flex items-center justify-center text-xs font-bold text-orange-700">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition
                     class="absolute right-0 mt-1 w-48 bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm z-50">
                    <a href="{{ route('user.profile', auth()->id()) }}"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Profil Saya
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Koleksi
                    </a>
                    <a href="{{ route('profile') }}"
                       class="block px-4 py-2 text-slate-700 hover:bg-orange-50 hover:text-orange-600">
                        Pengaturan
                    </a>
                    <hr class="my-1 border-slate-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hamburger mobile --}}
            <div class="sm:hidden" x-data="{ mobileOpen: false }">
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md text-slate-600 hover:text-orange-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div x-show="mobileOpen" x-transition
                     class="absolute top-14 left-0 right-0 bg-white border-b border-slate-200 shadow-md py-2 z-50">
                    <a href="{{ route('listings') }}" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">Market</a>
                    <a href="{{ route('club.feed') }}" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">Club</a>
                    <a href="{{ route('my.trades') }}" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">My Trades</a>
                    <a href="{{ route('my.listings') }}" class="block px-5 py-3 text-sm font-medium text-slate-700 hover:bg-orange-50">My Listings</a>
                    <hr class="my-1 border-slate-100">
                    <a href="{{ route('profile') }}" class="block px-5 py-3 text-sm text-slate-700 hover:bg-orange-50">Pengaturan</a>
                    <form method="POST" action="{{ route('logout') }}" class="px-5 py-2">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">Keluar</button>
                    </form>
                </div>
            </div>
            @else
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-slate-600 hover:text-orange-600">Masuk</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-orange-600 text-white rounded-lg hover:bg-orange-700">Daftar</a>
            </div>
            @endauth
        </div>
    </nav>

    {{-- Konten Utama --}}
    <main class="max-w-4xl mx-auto px-4 py-6 min-h-[80vh]">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200 mt-12">
        <div class="max-w-5xl mx-auto px-4 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 font-bold text-orange-600 text-lg mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                        DCastTrade
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">Platform komunitas dan pertukaran diecast / Hot Wheels terpercaya.</p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Aplikasi</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('values') }}" class="text-slate-600 hover:text-orange-600">Values</a></li>
                        <li><a href="{{ route('listings') }}" class="text-slate-600 hover:text-orange-600">Market</a></li>
                        <li><a href="{{ route('club.feed') }}" class="text-slate-600 hover:text-orange-600">Club</a></li>
                        <li><a href="{{ route('my.trades') }}" class="text-slate-600 hover:text-orange-600">My Trades</a></li>
                        <li><a href="{{ route('my.listings') }}" class="text-slate-600 hover:text-orange-600">My Listings</a></li>
                        <li><a href="{{ route('notifications') }}" class="text-slate-600 hover:text-orange-600">Notifikasi</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Info</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('tentang') }}" class="text-slate-600 hover:text-orange-600">Tentang DCastTrade</a></li>
                        <li><a href="{{ route('aturan') }}" class="text-slate-600 hover:text-orange-600">Aturan Komunitas</a></li>
                        <li><a href="{{ route('privasi') }}" class="text-slate-600 hover:text-orange-600">Kebijakan Privasi</a></li>
                        <li><a href="mailto:support@dcasttrade.com" class="text-slate-600 hover:text-orange-600">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400">
                <span>&copy; {{ date('Y') }} DCastTrade. Dibuat dengan ❤️ untuk komunitas kolektor diecast Indonesia.</span>
                <div class="flex gap-3">
                    <a href="{{ route('aturan') }}" class="hover:text-orange-600">Aturan</a>
                    <a href="{{ route('privasi') }}" class="hover:text-orange-600">Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Slot untuk script tambahan per-halaman (misal: Chart.js di casting detail) --}}
    @stack('scripts')

</body>
</html>

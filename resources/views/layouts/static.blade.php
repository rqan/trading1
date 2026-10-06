<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO: Google Search Console Verification -->
    <meta name="google-site-verification" content="YOUR_GOOGLE_SITE_VERIFICATION_CODE" />

    <title>{{ $title ?? 'DCastTrade' }}</title>
    <meta name="description" content="{{ $description ?? 'Platform pertukaran diecast / Hot Wheels terpercaya.' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="DCastTrade">
    <meta property="og:title"       content="{{ $title ?? 'DCastTrade' }}">
    <meta property="og:description" content="{{ $description ?? 'Platform pertukaran diecast / Hot Wheels terpercaya.' }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta name="twitter:card"       content="summary">
    <meta name="theme-color"        content="#ea580c">
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    </style>
    @vite(['resources/css/app.css'])

    {{-- SEO: JSON-LD Schema --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}#website",
          "url": "{{ url('/') }}",
          "name": "DCastTrade",
          "description": "Platform komunitas dan pertukaran diecast / Hot Wheels terpercaya di Indonesia.",
          "publisher": {
            "@id": "{{ url('/') }}#organization"
          }
        },
        {
          "@type": "Organization",
          "@id": "{{ url('/') }}#organization",
          "name": "DCastTrade",
          "url": "{{ url('/') }}",
          "logo": "{{ asset('logo.png') }}"
        }
      ]
    }
    </script>
</head>
<body class="bg-slate-50 text-slate-900">

    {{-- Navbar minimal --}}
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="font-bold text-lg text-orange-600 tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
                DCastTrade
            </a>
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-orange-600">← Kembali ke App</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-12">
        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 mt-16 py-8">
        <div class="max-w-4xl mx-auto px-4 flex flex-wrap justify-between items-center gap-4 text-sm text-slate-400">
            <span>© {{ date('Y') }} DCastTrade</span>
            <div class="flex gap-4">
                <a href="{{ route('aturan') }}" class="hover:text-orange-600">Aturan</a>
                <a href="{{ route('privasi') }}" class="hover:text-orange-600">Privasi</a>
                <a href="{{ route('tentang') }}" class="hover:text-orange-600">Tentang</a>
            </div>
        </div>
    </footer>
</body>
</html>

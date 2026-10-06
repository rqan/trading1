#!/bin/bash
set -e

# 1. Sitemap & 2. robots.txt & 20. llms.txt & 19. Search Console verification
echo "Generating robots.txt & llms.txt..."
cat << 'ROBOTS' > public/robots.txt
User-agent: *
Allow: /
Disallow: /admin
Disallow: /profile
Disallow: /dashboard
Disallow: /my-trades
Disallow: /my-listings
Disallow: /notifications
Disallow: /journals
Disallow: /trades

Sitemap: https://domainmu.com/sitemap.xml
ROBOTS

cat << 'LLMS' > public/llms.txt
Title: DCastTrade
Description: Platform komunitas dan pertukaran diecast / Hot Wheels terpercaya di Indonesia.
URLs:
- https://domainmu.com/ (Home)
- https://domainmu.com/values (Values / Daftar Nilai Casting)
- https://domainmu.com/clubs (Komunitas Club)
- https://domainmu.com/aturan (Aturan Komunitas)
- https://domainmu.com/privasi (Kebijakan Privasi)
- https://domainmu.com/tentang (Tentang Kami)
LLMS

# Create SitemapController
echo "Creating SitemapController..."
cat << 'SITEMAP' > app/Http/Controllers/SitemapController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            url('/'),
            url('/values'),
            url('/clubs'),
            url('/aturan'),
            url('/privasi'),
            url('/tentang'),
        ];
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url) . '</loc>';
            $xml .= '<changefreq>daily</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
SITEMAP

# Add sitemap route
if ! grep -q "sitemap.xml" routes/web.php; then
    echo "Route::get('sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index']);" >> routes/web.php
fi


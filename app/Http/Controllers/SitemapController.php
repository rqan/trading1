<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Club;
use App\Models\Casting;

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
        
        foreach (Club::all() as $club) {
            $urls[] = route('clubs.show', $club->slug);
        }

        foreach (Casting::all() as $casting) {
            $urls[] = route('casting.detail', $casting->slug);
        }

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

#!/bin/bash
set -e

# Insert google-site-verification and Organization/WebSite schema to app.blade.php
sed -i 's|<head>|<head>\n    <!-- SEO: Google Search Console Verification -->\n    <meta name="google-site-verification" content="YOUR_GOOGLE_SITE_VERIFICATION_CODE" />\n|g' resources/views/layouts/app.blade.php

# Append schema JSON-LD right before </head>
SCHEMA='
    {{-- SEO: JSON-LD Schema --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "WebSite",
          "@id": "{{ url("/") }}#website",
          "url": "{{ url("/") }}",
          "name": "DCastTrade",
          "description": "Platform komunitas dan pertukaran diecast / Hot Wheels terpercaya di Indonesia.",
          "publisher": {
            "@id": "{{ url("/") }}#organization"
          }
        },
        {
          "@type": "Organization",
          "@id": "{{ url("/") }}#organization",
          "name": "DCastTrade",
          "url": "{{ url("/") }}",
          "logo": "{{ asset("logo.png") }}"
        }
      ]
    }
    </script>
</head>'

sed -i "s|</head>|$SCHEMA|g" resources/views/layouts/app.blade.php


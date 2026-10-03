<x-layouts.static title="Kebijakan Privasi – DCastTrade" description="Informasi tentang bagaimana DCastTrade mengumpulkan, menggunakan, dan melindungi data pribadi Anda.">

    <article class="prose prose-slate max-w-none">

        <h1 class="text-3xl font-bold text-slate-900 mb-2">🔒 Kebijakan Privasi</h1>
        <p class="text-slate-500 text-sm mb-8">Terakhir diperbarui: Oktober 2026</p>

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">1. Data yang Kami Kumpulkan</h2>
                <p class="text-slate-700 text-sm mb-3">Kami hanya mengumpulkan data yang diperlukan untuk menjalankan layanan:</p>
                <ul class="space-y-1.5 text-slate-700 text-sm list-disc list-inside">
                    <li><strong>Akun:</strong> nama tampilan, alamat email, dan kata sandi (tersimpan terenkripsi).</li>
                    <li><strong>Koleksi:</strong> data diecast yang kamu masukkan secara sukarela (nama casting, kondisi, foto).</li>
                    <li><strong>Transaksi:</strong> riwayat trade, pesan trade, dan nomor resi pengiriman.</li>
                    <li><strong>Teknis:</strong> alamat IP, browser, dan log aktivitas untuk keamanan dan debugging.</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">2. Cara Kami Menggunakan Data</h2>
                <ul class="space-y-1.5 text-slate-700 text-sm list-disc list-inside">
                    <li>Menjalankan fitur platform: browse listing, trade room, notifikasi.</li>
                    <li>Menghitung Trust Score berdasarkan riwayat transaksi.</li>
                    <li>Mengirim notifikasi in-app tentang aktivitas yang relevan dengan akun kamu.</li>
                    <li>Mendeteksi dan mencegah penipuan atau penyalahgunaan platform.</li>
                    <li>Meningkatkan kualitas layanan melalui analisis agregat (anonim).</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">3. Berbagi Data dengan Pihak Ketiga</h2>
                <p class="text-slate-700 text-sm">
                    <strong>Kami tidak menjual data pribadi kamu.</strong> Data hanya dibagikan dalam kondisi berikut:
                </p>
                <ul class="space-y-1.5 text-slate-700 text-sm list-disc list-inside mt-3">
                    <li>Kepada pihak lawan trade kamu (nama dan informasi pengiriman yang disepakati bersama).</li>
                    <li>Penyedia infrastruktur cloud yang terikat perjanjian kerahasiaan.</li>
                    <li>Otoritas hukum jika diwajibkan oleh peraturan yang berlaku.</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">4. Keamanan Data</h2>
                <ul class="space-y-1.5 text-slate-700 text-sm list-disc list-inside">
                    <li>Kata sandi disimpan menggunakan bcrypt (tidak dapat dibaca balik).</li>
                    <li>Koneksi diamankan dengan HTTPS/TLS.</li>
                    <li>Akses database dibatasi hanya untuk sistem yang memerlukan.</li>
                    <li>Foto yang diunggah disimpan di storage terpisah dengan kontrol akses.</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">5. Hak Kamu atas Data</h2>
                <p class="text-slate-700 text-sm mb-3">Kamu memiliki hak untuk:</p>
                <ul class="space-y-1.5 text-slate-700 text-sm list-disc list-inside">
                    <li><strong>Mengakses</strong> data yang kami simpan tentang kamu melalui halaman Pengaturan.</li>
                    <li><strong>Memperbaiki</strong> data yang tidak akurat (nama, email) melalui halaman Profil.</li>
                    <li><strong>Menghapus</strong> akun kamu — hubungi kami dan kami akan menghapus data dalam 30 hari.</li>
                    <li><strong>Mengekspor</strong> riwayat trade kamu — tersedia atas permintaan.</li>
                </ul>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">6. Cookies</h2>
                <p class="text-slate-700 text-sm">
                    Kami menggunakan session cookie untuk autentikasi dan CSRF protection.
                    Kami tidak menggunakan cookie iklan atau pelacak pihak ketiga.
                </p>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-bold text-slate-800 mb-3">7. Hubungi Kami</h2>
                <p class="text-slate-700 text-sm">
                    Untuk pertanyaan seputar privasi, hubungi kami di:
                    <a href="mailto:privasi@dcasttrade.com" class="text-orange-600 hover:underline">privasi@dcasttrade.com</a>
                </p>
            </div>

        </div>

        <p class="mt-8 text-sm text-slate-500">
            Lihat juga <a href="{{ route('aturan') }}" class="text-orange-600 hover:underline">Aturan Komunitas</a> kami.
        </p>

    </article>

</x-layouts.static>

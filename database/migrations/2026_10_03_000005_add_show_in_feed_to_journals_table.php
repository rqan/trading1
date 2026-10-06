<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Hanya berlaku saat visibility = public.
            // true  → jurnal publik tampil di Community Feed
            // false → publik tapi hanya muncul di profil/link langsung
            $table->boolean('show_in_feed')->default(false)->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('show_in_feed');
        });
    }
};

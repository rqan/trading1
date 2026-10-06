<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan dua kolom boolean untuk mekanisme persetujuan ganda (dual-consensus).
     * Sebuah trade hanya bisa bergeser ke status 'accepted' jika KEDUA kolom ini true.
     */
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->boolean('accepted_initiator')->default(false)->after('photos_checked_receiver');
            $table->boolean('accepted_receiver')->default(false)->after('accepted_initiator');
        });
    }

    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['accepted_initiator', 'accepted_receiver']);
        });
    }
};

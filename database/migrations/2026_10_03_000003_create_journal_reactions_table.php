<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji', 10); // Satu emoji aktif per user per jurnal

            $table->timestamps();

            // Satu reaksi per user per jurnal (ganti-ganti emoji bukan tambah baris baru)
            $table->unique(['journal_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_reactions');
    }
};

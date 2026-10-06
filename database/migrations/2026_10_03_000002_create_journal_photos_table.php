<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedTinyInteger('order')->default(0); // Urutan tampil: 0, 1, 2
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_photos');
    }
};

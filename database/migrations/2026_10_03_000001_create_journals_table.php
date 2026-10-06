<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('casting_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->enum('acquisition_type', ['beli', 'hadiah', 'tukar', 'temuan']);
            $table->date('acquired_at');
            $table->unsignedInteger('price')->nullable(); // Rupiah, hanya relevan saat beli
            $table->text('story')->nullable();            // Maks 2.000 karakter (enforced di app)

            // Lokasi — wajib diisi via peta
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('place_name')->nullable();
            $table->string('place_address')->nullable();

            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};

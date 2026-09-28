<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_multiplier_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('casting_id')->constrained()->cascadeOnDelete();
            $table->decimal('multiplier', 8, 4);
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_multiplier_histories');
    }
};

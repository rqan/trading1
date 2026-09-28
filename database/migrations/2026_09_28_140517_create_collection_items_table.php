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
        Schema::create('collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('casting_id')->constrained()->cascadeOnDelete();
            $table->string('condition'); // Sealed/MOC, Mint Loose, Good, Played, Damaged
            $table->string('card_condition')->nullable(); // Mint, Creased, Torn, dll
            $table->json('photos'); // array of photo paths
            $table->string('status')->default('available'); // available, trading, traded
            $table->decimal('computed_value', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_items');
    }
};

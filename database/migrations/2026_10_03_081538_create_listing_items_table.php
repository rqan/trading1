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
        Schema::create('listing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->enum('side', ['offered', 'requested']);
            $table->foreignId('casting_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('special_type', ['offers', 'upgrade'])->nullable();
            $table->integer('quantity')->default(1);
            $table->enum('condition', ['mint', 'near_mint', 'good', 'loose', 'damaged'])->nullable();
            $table->text('condition_notes')->nullable();
            $table->decimal('cash_addition', 12, 2)->default(0); // for top ups
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_items');
    }
};

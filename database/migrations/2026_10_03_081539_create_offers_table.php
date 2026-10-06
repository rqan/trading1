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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            // Self-referencing foreign key for counter offers
            $table->foreignId('parent_offer_id')->nullable()->constrained('offers')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'countered', 'rejected', 'withdrawn', 'expired'])->default('pending');
            $table->decimal('cash_addition', 12, 2)->default(0); // If sender offers cash
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};

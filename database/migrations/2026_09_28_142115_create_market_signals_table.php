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
        Schema::create('market_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('casting_id')->constrained()->cascadeOnDelete();
            $table->integer('active_listing_count')->default(0);
            $table->integer('wishlist_count')->default(0);
            $table->integer('trade_completed_30d')->default(0);
            $table->integer('view_count_7d')->default(0);
            $table->decimal('current_multiplier', 8, 4)->default(1.0000);
            $table->decimal('target_multiplier', 8, 4)->default(1.0000);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_signals');
    }
};

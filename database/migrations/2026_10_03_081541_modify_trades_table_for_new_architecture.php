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
        // First drop foreign keys from trade_messages and trade_items if we are doing a hard alter
        // But since this is a new feature we can just add the new columns and make old ones nullable

        Schema::table('trades', function (Blueprint $table) {
            // New columns for My Listing redesign
            $table->foreignId('listing_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('accepted_offer_id')->nullable()->constrained('offers')->nullOnDelete();

            $table->foreignId('party_a_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('party_b_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('party_a_confirmed_at')->nullable();
            $table->timestamp('party_b_confirmed_at')->nullable();

            $table->string('completion_method')->nullable();

            // To deal with enum change, we add a new status column
            $table->string('trade_status')->default('pending'); // pending, completed, disputed, cancelled
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['listing_id']);
            $table->dropColumn('listing_id');

            $table->dropForeign(['accepted_offer_id']);
            $table->dropColumn('accepted_offer_id');

            $table->dropForeign(['party_a_id']);
            $table->dropColumn('party_a_id');

            $table->dropForeign(['party_b_id']);
            $table->dropColumn('party_b_id');

            $table->dropColumn('party_a_confirmed_at');
            $table->dropColumn('party_b_confirmed_at');
            $table->dropColumn('completion_method');
            $table->dropColumn('trade_status');
        });
    }
};

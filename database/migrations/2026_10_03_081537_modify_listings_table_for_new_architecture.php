<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // To keep the data clean, we will archive all active/pending listings to 'legacy' status
        DB::table('listings')->whereIn('status', ['active', 'pending'])->update(['status' => 'legacy']);

        Schema::table('listings', function (Blueprint $table) {
            // Drop old columns that are no longer needed on the main table
            $table->dropForeign(['collection_item_id']);
            $table->dropColumn('collection_item_id');

            $table->dropForeign(['casting_id']);
            $table->dropColumn('casting_id');

            $table->dropColumn('type');

            // Add new columns
            $table->string('title')->nullable()->after('user_id');
            $table->text('description')->nullable()->after('title');

            // Note: We keep status but we will enforce different enum values at the application level
            // 'active', 'negotiating', 'pending_confirmation', 'completed', 'expired', 'legacy', 'deleted'

            $table->enum('completion_method', ['cod', 'shipping', 'both'])->default('both')->after('status');
            $table->string('city')->nullable()->after('completion_method');

            $table->enum('visibility', ['public', 'club_only'])->default('public')->after('city');
            $table->foreignId('club_id')->nullable()->after('visibility')->constrained()->nullOnDelete();

            $table->timestamp('expires_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->foreignId('collection_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('casting_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('type', ['for_trade', 'looking_for'])->default('for_trade');

            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('completion_method');
            $table->dropColumn('city');
            $table->dropColumn('visibility');
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
            $table->dropColumn('expires_at');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('score')->unsigned(); // 1 to 5
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->unique(['trade_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};

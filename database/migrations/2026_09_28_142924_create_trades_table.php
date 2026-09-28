<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('initiator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['proposed', 'accepted', 'shipped', 'completed', 'cancelled'])->default('proposed');
            $table->string('tracking_initiator')->nullable();
            $table->string('tracking_receiver')->nullable();
            $table->boolean('photos_checked_initiator')->default(false);
            $table->boolean('photos_checked_receiver')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};

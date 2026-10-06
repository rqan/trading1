<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('castings', function (Blueprint $table) {
            // Slug untuk URL SEO-friendly: /casting/nissan-skyline-r34
            $table->string('slug')->nullable()->unique()->after('name');
            // Kolom opsional untuk gambar thumbnail casting
            $table->string('image_url')->nullable()->after('base_value');
        });
    }

    public function down(): void
    {
        Schema::table('castings', function (Blueprint $table) {
            $table->dropColumn(['slug', 'image_url']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->text('hero_text')->nullable();
            $table->string('hero_bg_image')->nullable();
            $table->string('hero_logo')->nullable();
            $table->string('featured_artist_title')->nullable();
            $table->text('featured_artist_description')->nullable();
            $table->string('featured_artist_image')->nullable();
            $table->json('art_slides')->nullable();
            $table->unsignedSmallInteger('upload_max_mb')->default(40); // حد رفع ملفات التوثيق بالميغابايت
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};

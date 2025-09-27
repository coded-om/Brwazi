<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('artwork_likes')) {
            Schema::create('artwork_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['artwork_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_likes');
    }
};

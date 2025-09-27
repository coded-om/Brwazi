<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('artwork_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained('artworks')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['artwork_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_tag');
    }
};

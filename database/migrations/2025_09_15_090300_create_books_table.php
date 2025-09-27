<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publisher_id')->constrained('publishers')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('isbn')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('language', 5)->default('ar');
            $table->string('type', 30)->nullable();
            $table->smallInteger('publish_year')->nullable();
            $table->smallInteger('pages')->nullable();
            $table->decimal('price_omr', 10, 3)->default(0);
            $table->decimal('compare_at_price_omr', 10, 3)->nullable();
            $table->string('cover_image_path')->nullable();
            $table->integer('stock')->default(0);
            $table->string('status', 20)->default('published');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

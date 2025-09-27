<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('literature_workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('presenter_name');
            $table->text('presenter_bio')->nullable();
            $table->string('presenter_avatar_path')->nullable();
            $table->string('genre')->nullable();
            $table->dateTime('starts_at');
            $table->integer('duration_minutes')->nullable();
            $table->integer('capacity')->nullable();
            $table->string('location')->nullable();
            $table->string('external_apply_url')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('submitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literature_workshops');
    }
};

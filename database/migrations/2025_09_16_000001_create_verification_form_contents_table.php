<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verification_form_contents', function (Blueprint $table) {
            $table->id();
            $table->string('form_type', 20); // visual | photo
            $table->json('terms')->nullable(); // array of strings
            $table->json('attachments')->nullable(); // array of strings
            $table->unsignedTinyInteger('works_min')->default(5);
            $table->unsignedTinyInteger('works_max')->default(10);
            $table->timestamps();
            $table->unique('form_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_form_contents');
    }
};

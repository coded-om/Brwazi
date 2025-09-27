<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('literature_workshop_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('literature_workshop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('whatsapp_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['literature_workshop_id', 'user_id'], 'lw_unique_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literature_workshop_registrations');
    }
};

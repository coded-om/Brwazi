<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id'); // ربط بالمحادثة
            $table->unsignedBigInteger('sender_id'); // المرسل
            $table->text('content')->nullable(); // محتوى النص
            $table->string('image_path')->nullable(); // مسار الصورة المرفقة
            $table->enum('type', ['text', 'image', 'artwork_request'])->default('text'); // نوع الرسالة
            $table->boolean('is_read')->default(false); // هل تم قراءة الرسالة
            $table->timestamp('read_at')->nullable(); // تاريخ القراءة
            $table->timestamps();

            // Foreign keys
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes لتسريع البحث
            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id']);
            $table->index(['is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

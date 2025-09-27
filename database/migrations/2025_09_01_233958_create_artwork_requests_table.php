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
        Schema::create('artwork_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id'); // طالب اللوحة
            $table->unsignedBigInteger('artist_id'); // الفنان المطلوب منه
            $table->unsignedBigInteger('message_id'); // ربط برسالة الطلب
            $table->string('title'); // عنوان الطلب
            $table->text('description'); // وصف تفصيلي للطلب
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->decimal('budget', 10, 2)->nullable(); // الميزانية المقترحة
            $table->date('deadline')->nullable(); // تاريخ التسليم المطلوب
            $table->json('reference_images')->nullable(); // صور مرجعية (مسارات ملفات)
            $table->text('artist_notes')->nullable(); // ملاحظات الفنان
            $table->text('requester_notes')->nullable(); // ملاحظات طالب اللوحة
            $table->timestamps();

            // Foreign keys
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('artist_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');

            // Indexes
            $table->index(['artist_id', 'status']);
            $table->index(['requester_id', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_requests');
    }
};

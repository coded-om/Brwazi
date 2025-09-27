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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user1_id'); // المستخدم الأول
            $table->unsignedBigInteger('user2_id'); // المستخدم الثاني
            $table->timestamp('last_message_at')->nullable(); // تاريخ آخر رسالة
            $table->timestamps();

            // Foreign keys
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');

            // Index لتسريع البحث
            $table->index(['user1_id', 'user2_id']);

            // التأكد من عدم وجود محادثة مكررة بين نفس المستخدمين
            $table->unique(['user1_id', 'user2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            // polymorphic target (could be post, artwork, user, message, etc.)
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->string('type'); // نوع البلاغ
            $table->string('reason')->nullable(); // سبب مختصر مخصص (optional override)
            $table->text('details')->nullable(); // تفاصيل إضافية
            $table->string('status')->default('pending');
            $table->foreignId('handled_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->text('notes')->nullable(); // ملاحظات داخلية للإدارة
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index(['status']);
            $table->index(['type']);
            // منع تكرار نفس البلاغ (نفس المستخدم على نفس الهدف بنفس النوع)
            $table->unique(['reporter_id', 'target_type', 'target_id', 'type'], 'reports_unique_user_target_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

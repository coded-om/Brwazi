<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('form_type'); // visual | photo
            $table->string('full_name');
            $table->date('birth_date');
            $table->string('gender', 10);
            $table->string('education');
            $table->string('address');
            $table->string('phone', 50);
            $table->string('email');
            $table->string('nationality', 50)->nullable();
            $table->json('specialties');
            $table->string('id_file_path');
            $table->string('avatar_file_path');
            $table->string('cv_file_path')->nullable();
            $table->json('works_files');
            $table->string('status', 20)->default('pending');
            $table->text('decision_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};

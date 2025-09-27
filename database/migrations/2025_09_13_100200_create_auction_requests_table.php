<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained('artworks')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending')->index(); // pending|approved|rejected
            $table->decimal('desired_start_price', 12, 2)->nullable();
            $table->timestamp('suggested_start_at')->nullable()->index();
            $table->unsignedInteger('suggested_duration')->nullable()->comment('Duration in minutes');
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['artwork_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_requests');
    }
};

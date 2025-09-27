<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('artwork_id')
                ->constrained('artworks')
                ->restrictOnDelete();

            // Lifecycle status: draft|scheduled|live|ended|canceled
            $table->string('status')->index();

            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable()->index();

            // Pricing
            $table->decimal('start_price', 12, 2);
            $table->decimal('bid_increment', 12, 2)->default(1.00);
            $table->decimal('reserve_price', 12, 2)->nullable();
            $table->decimal('buy_now_price', 12, 2)->nullable();

            // Highest bid tracking
            $table->unsignedBigInteger('highest_bid_id')->nullable(); // FK added in later migration
            $table->decimal('highest_bid_amount', 12, 2)->default(0);
            $table->unsignedInteger('bids_count')->default(0);

            // Admin approval context
            $table->foreignId('approved_by_admin_id')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            // Composite indexes for common queries
            $table->index(['status', 'starts_at']);
            $table->index(['status', 'ends_at']);
            $table->index(['artwork_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};

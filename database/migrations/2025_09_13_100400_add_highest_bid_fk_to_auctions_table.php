<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            // Add FK to bids.id with SET NULL on delete
            $table->foreign('highest_bid_id')
                ->references('id')->on('bids')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropForeign(['highest_bid_id']);
        });
    }
};

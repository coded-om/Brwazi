<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'title_snapshot')) {
                $table->string('title_snapshot')->after('artwork_id');
            }
            if (!Schema::hasColumn('order_items', 'image_snapshot')) {
                $table->string('image_snapshot')->nullable()->after('title_snapshot');
            }
            if (!Schema::hasColumn('order_items', 'price_snapshot')) {
                $table->unsignedInteger('price_snapshot')->after('image_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'price_snapshot'))
                $table->dropColumn('price_snapshot');
            if (Schema::hasColumn('order_items', 'image_snapshot'))
                $table->dropColumn('image_snapshot');
            if (Schema::hasColumn('order_items', 'title_snapshot'))
                $table->dropColumn('title_snapshot');
        });
    }
};

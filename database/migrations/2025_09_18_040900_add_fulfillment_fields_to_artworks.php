<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (!Schema::hasColumn('artworks', 'requires_shipping')) {
                $table->boolean('requires_shipping')->default(true)->after('status');
            }
            if (!Schema::hasColumn('artworks', 'fulfillment_by')) {
                $table->enum('fulfillment_by', ['artist', 'platform'])->default('artist')->after('requires_shipping');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (Schema::hasColumn('artworks', 'fulfillment_by'))
                $table->dropColumn('fulfillment_by');
            if (Schema::hasColumn('artworks', 'requires_shipping'))
                $table->dropColumn('requires_shipping');
        });
    }
};

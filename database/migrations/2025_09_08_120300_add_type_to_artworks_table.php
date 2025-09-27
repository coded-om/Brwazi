<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (!Schema::hasColumn('artworks', 'type')) {
                $table->string('type')->default('art')->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (Schema::hasColumn('artworks', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

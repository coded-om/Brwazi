<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (!Schema::hasColumn('artworks', 'likes_count')) {
                $table->unsignedBigInteger('likes_count')->default(0)->after('price');
            }
            if (!Schema::hasColumn('artworks', 'images_count')) {
                $table->unsignedInteger('images_count')->default(0)->after('likes_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (Schema::hasColumn('artworks', 'likes_count')) {
                $table->dropColumn('likes_count');
            }
            if (Schema::hasColumn('artworks', 'images_count')) {
                $table->dropColumn('images_count');
            }
        });
    }
};

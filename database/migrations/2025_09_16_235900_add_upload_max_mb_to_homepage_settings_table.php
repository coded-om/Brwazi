<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('homepage_settings', 'upload_max_mb')) {
                $table->unsignedSmallInteger('upload_max_mb')->default(40)->after('events');
            }
        });
    }

    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            if (Schema::hasColumn('homepage_settings', 'upload_max_mb')) {
                $table->dropColumn('upload_max_mb');
            }
        });
    }
};

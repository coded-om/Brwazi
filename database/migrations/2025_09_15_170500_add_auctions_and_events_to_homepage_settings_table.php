<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->string('auctions_title')->nullable()->after('art_slides');
            $table->text('auctions_subtitle')->nullable()->after('auctions_title');
            $table->json('events')->nullable()->after('auctions_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('homepage_settings', function (Blueprint $table) {
            $table->dropColumn(['auctions_title', 'auctions_subtitle', 'events']);
        });
    }
};

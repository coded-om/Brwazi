<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workshop_registrations', function (Blueprint $table) {
            // Allow multiple guests (null user_id) but enforce uniqueness for logged users
            $table->unique(['workshop_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('workshop_registrations', function (Blueprint $table) {
            $table->dropUnique(['workshop_registrations_workshop_id_user_id_unique']);
        });
    }
};

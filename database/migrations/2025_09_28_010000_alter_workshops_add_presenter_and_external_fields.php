<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->unsignedBigInteger('submitted_by_user_id')->nullable()->after('id');
            $table->boolean('is_approved')->default(false)->after('is_published');
            $table->string('external_apply_url')->nullable()->after('short_description');
            $table->text('presenter_bio')->nullable()->after('presenter_name');
            $table->string('presenter_avatar_path')->nullable()->after('presenter_bio');

            $table->foreign('submitted_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropForeign(['submitted_by_user_id']);
            $table->dropColumn([
                'submitted_by_user_id',
                'is_approved',
                'external_apply_url',
                'presenter_bio',
                'presenter_avatar_path',
            ]);
        });
    }
};

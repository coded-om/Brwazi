<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('bio');
            $table->string('email')->nullable()->after('phone');
            $table->text('notes')->nullable()->after('email'); // admin-only notes
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'notes']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Split name into first name and last name
            $table->string('fname')->nullable()->after('id');
            $table->string('lname')->nullable()->after('fname');

            // Add new profile fields
            $table->date('birthday')->nullable()->after('email');
            $table->string('country')->nullable()->after('birthday');
            $table->string('ProfileImage')->nullable()->after('country');
            $table->string('phone_number')->nullable()->after('ProfileImage');

            // Remove the old name field since we now have fname/lname
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add back the name field
            $table->string('name')->after('id');

            // Remove the new fields
            $table->dropColumn(['fname', 'lname', 'birthday', 'country', 'ProfileImage', 'phone_number']);
        });
    }
};

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
            $table->enum('tagline', [
                'مصوّر ضوئي و رسام رقمي',
                'مطوّر مواقع و تطبيقات',
                'مصمم جرافيك محترف',
                'كاتب محتوى إبداعي',
                'مسوّق رقمي خبير',
                'مترجم و محرر نصوص',
                'مدرّب و مستشار تقني',
                'رائد أعمال مبدع',
                'فنان و مبدع رقمي',
                'محاسب و مستشار مالي',
                'مهندس و مطوّر',
                'طبيب و مختص صحي',
                'معلّم و مربّي',
                'محامي و مستشار قانوني',
                'مؤثّر في وسائل التواصل'
            ])->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tagline');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tagline')) {
            // Convert ENUM to VARCHAR to support dynamic options from taglines table
            DB::statement("ALTER TABLE `users` MODIFY `tagline` VARCHAR(191) NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        // Revert back to the original ENUM list (may fail if data contains values outside this set)
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tagline')) {
            DB::statement("ALTER TABLE `users` MODIFY `tagline`
                ENUM(
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
                ) NULL");
        }
    }
};

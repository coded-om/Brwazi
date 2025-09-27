<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AuctionDemoSeeder::class,
            LiterarySeeder::class,
            ArtTaxonomySeeder::class,
            Gallery3DSettingSeeder::class,
        ]);

        \App\Models\HomepageSetting::firstOrCreate([], [
            'hero_text' => 'مرحبًا بك في برواز، حيث يلتقي الإبداع بالفُرَص.',
            'hero_bg_image' => 'imgs/pic/rec1.jpg',
            'hero_logo' => 'imgs/icons-color/logo-color-word.svg',
            'featured_artist_title' => 'بائعة الأكياس',
            'featured_artist_description' => 'لوحة فنية تشكيلية رائعة تجسد التراث العماني الأصيل...',
            'featured_artist_image' => 'imgs/pepole/artist-1.png',
            'art_slides' => [
                ['title' => 'بائعة الأكياس', 'description' => 'لوحة فنية تشكيلية رائعة...', 'image' => 'imgs/pic/img9.png'],
                ['title' => 'ليلة النجوم العربية', 'description' => 'لوحة فنية معاصرة...', 'image' => 'imgs/pic/img8.png'],
                ['title' => 'صرخة الحرية', 'description' => 'عمل فني تعبيري...', 'image' => 'imgs/pic/img10.png'],
            ],
            'events' => [
                ['title' => 'معرض الفن التشكيلي السنوي', 'description' => 'معرض يضم أعمال نخبة من الفنانين التشكيليين في المملكة', 'day' => 15, 'month' => 'أبريل', 'link' => '#'],
                ['title' => 'أسبوع الخط العربي', 'description' => 'فعالية تحتفي بفن الخط العربي وتطوره مع معرض لأعمال الخطاطين', 'day' => 23, 'month' => 'مايو', 'link' => '#'],
            ],
        ]);

        if (\App\Models\Tagline::count() === 0) {
            $defaults = [
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
                'مؤثّر في وسائل التواصل',
            ];
            foreach ($defaults as $i => $name) {
                \App\Models\Tagline::create([
                    'name' => $name,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}

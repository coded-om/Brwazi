<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArtTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'فن رقمي' => 'digital',
            'فن تقليدي' => 'traditional',
            'تصوير' => 'photography',
            'خط عربي' => 'calligraphy',
            'نحت' => 'sculpture',
            'فن مختلط' => 'mixed',
        ];
        foreach ($categories as $name => $slug) {
            \App\Models\ArtCategory::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'sort_order' => array_search($slug, array_values($categories)),
                'active' => true,
            ]);
        }

        $media = [
            'زيت' => 'oil',
            'أكريليك' => 'acrylic',
            'ألوان مائية' => 'watercolor',
            'فحم' => 'charcoal',
            'قلم رصاص' => 'pencil',
            'رقمي' => 'digital',
            'أخرى' => 'other',
        ];
        $i = 0;
        foreach ($media as $name => $slug) {
            \App\Models\ArtMedium::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'sort_order' => $i++,
                'active' => true,
            ]);
        }
    }
}

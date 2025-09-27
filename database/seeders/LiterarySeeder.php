<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publisher;

class LiterarySeeder extends Seeder
{
    public function run(): void
    {
        Publisher::firstOrCreate(
            ['name' => 'وزارة الثقافة'],
            [
                'type' => 'organization',
                'location' => 'مسقط',
                'founded_year' => 1970,
                'website' => null,
                'contact_email' => null,
                'is_active' => true,
            ]
        );
    }
}

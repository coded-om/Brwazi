<?php

namespace Database\Seeders;

use App\Models\Gallery3DSetting;
use Illuminate\Database\Seeder;

class Gallery3DSettingSeeder extends Seeder
{
    public function run(): void
    {
        Gallery3DSetting::current();
    }
}

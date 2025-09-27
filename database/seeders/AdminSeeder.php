<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find default admin user
        $admin1 = Admin::firstOrCreate(
            ['email' => 'admin@brwaze.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );

        // Create or find additional admin user
        $admin2 = Admin::firstOrCreate(
            ['email' => 'super@brwaze.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('super123'),
                'role' => 'super_admin'
            ]
        );

        // Update existing users if they don't have roles
        if (!$admin1->wasRecentlyCreated && !$admin1->role) {
            $admin1->update(['role' => 'admin']);
        }
        if (!$admin2->wasRecentlyCreated && !$admin2->role) {
            $admin2->update(['role' => 'super_admin']);
        }

        echo "✅ Admin users ready!\n";
        echo "Admin 1: admin@brwaze.com / admin123 " . ($admin1->wasRecentlyCreated ? "(NEW)" : "(EXISTS)") . "\n";
        echo "Admin 2: super@brwaze.com / super123 " . ($admin2->wasRecentlyCreated ? "(NEW)" : "(EXISTS)") . "\n";

        echo "\nTotal admins in database: " . Admin::count() . "\n";
    }
}

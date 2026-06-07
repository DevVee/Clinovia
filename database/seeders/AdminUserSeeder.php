<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@iccbi.edu.ph'],
            [
                'name'      => 'System Administrator',
                'password'  => Hash::make('Admin@2026!'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrator');

        // Demo Nurse
        $nurse = User::firstOrCreate(
            ['email' => 'nurse@iccbi.edu.ph'],
            [
                'name'      => 'Demo Nurse',
                'password'  => Hash::make('Nurse@2026!'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $nurse->assignRole('nurse');

        // Demo Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@iccbi.edu.ph'],
            [
                'name'      => 'Demo Staff',
                'password'  => Hash::make('Staff@2026!'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $staff->assignRole('staff');

        $this->command->info('Users seeded: admin@iccbi.edu.ph / Admin@2026!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // SECURITY FIX: Default credentials are now driven by .env so they are
        // never committed to version control in plaintext.
        // Set ADMIN_EMAIL / ADMIN_PASSWORD / NURSE_EMAIL / etc. before seeding.
        // Falls back to safe placeholder values that MUST be changed before go-live.

        // ── Administrator ─────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@clinovia.app')],
            [
                'name'               => env('ADMIN_NAME', 'System Administrator'),
                'password'           => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe@' . now()->year . '!')),
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );
        $admin->syncRoles('administrator');

        // ── Demo Nurse ────────────────────────────────────────────────────────
        $nurse = User::firstOrCreate(
            ['email' => env('NURSE_EMAIL', 'nurse@clinovia.app')],
            [
                'name'               => env('NURSE_NAME', 'Demo Nurse'),
                'password'           => Hash::make(env('NURSE_PASSWORD', 'ChangeMe@' . now()->year . '!')),
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );
        $nurse->syncRoles('nurse');

        // ── Demo Staff ────────────────────────────────────────────────────────
        $staff = User::firstOrCreate(
            ['email' => env('STAFF_EMAIL', 'staff@clinovia.app')],
            [
                'name'               => env('STAFF_NAME', 'Demo Staff'),
                'password'           => Hash::make(env('STAFF_PASSWORD', 'ChangeMe@' . now()->year . '!')),
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );
        $staff->syncRoles('staff');

        // ── Portfolio Viewer (read-only demo account) ─────────────────────────
        $viewer = User::firstOrCreate(
            ['email' => env('VIEWER_EMAIL', 'viewer@clinovia.app')],
            [
                'name'               => env('VIEWER_NAME', 'Portfolio Viewer'),
                'password'           => Hash::make(env('VIEWER_PASSWORD', 'Viewer@2026!')),
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );
        $viewer->syncRoles('viewer');

        $this->command->info('Seed users created. ⚠️  Change passwords immediately for production!');
        $this->command->warn('  Admin  : ' . env('ADMIN_EMAIL',  'admin@clinovia.app'));
        $this->command->warn('  Nurse  : ' . env('NURSE_EMAIL',  'nurse@clinovia.app'));
        $this->command->warn('  Staff  : ' . env('STAFF_EMAIL',  'staff@clinovia.app'));
        $this->command->warn('  Viewer : ' . env('VIEWER_EMAIL', 'viewer@clinovia.app'));
    }
}

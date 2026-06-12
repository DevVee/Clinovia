<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── Permissions ──────────────────────────────────────────────────────
        $permissions = [
            // User Management
            'manage-users', 'manage-roles', 'manage-settings',

            // Audit
            'view-audit-logs',

            // Patients
            'view-patients', 'create-patients', 'update-patients', 'delete-patients',

            // Appointments
            'view-appointments', 'create-appointments', 'update-appointments',
            'delete-appointments', 'approve-appointments',
            // CRITICAL-8 FIX: cancel-appointments is the correct permission for
            // cancelling. Previously it was defined but assigned to no role, and
            // AppointmentController::cancel() was checking approve-appointments
            // instead. Both are now corrected.
            'cancel-appointments',

            // Consultations
            'view-consultations', 'create-consultations', 'update-consultations', 'delete-consultations',

            // Medicines
            'view-medicines', 'create-medicines', 'update-medicines', 'delete-medicines',

            // Inventory
            'view-inventory', 'manage-inventory',

            // Dispensing
            'view-dispensing', 'create-dispensing',

            // Reports
            'view-reports', 'export-reports',

            // SMS
            'view-sms', 'send-sms',

            // AI
            'use-ai-assistant',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Roles ────────────────────────────────────────────────────────────

        // Administrator — full access
        $admin = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Nurse — full clinical operations
        $nurse = Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
        $nurse->syncPermissions([
            'view-patients', 'create-patients', 'update-patients',
            'view-appointments', 'create-appointments', 'update-appointments',
            'approve-appointments',
            'cancel-appointments',   // CRITICAL-8 FIX: nurses can cancel appointments
            'view-consultations', 'create-consultations', 'update-consultations',
            'view-medicines', 'create-medicines', 'update-medicines', 'delete-medicines',
            'view-inventory', 'manage-inventory',
            'view-dispensing', 'create-dispensing',
            'view-reports', 'export-reports',
            'view-sms', 'send-sms',
            'use-ai-assistant',
        ]);

        // Staff — limited read + create appointments
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'view-patients',
            'view-appointments', 'create-appointments',
            'view-consultations',
        ]);

        // Viewer — read-only portfolio demo account; no writes, no admin
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'view-patients',
            'view-appointments',
            'view-consultations',
            'view-medicines',
            'view-inventory',
            'view-dispensing',
            'view-reports',
            'use-ai-assistant',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}

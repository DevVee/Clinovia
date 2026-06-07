<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── General ─────────────────────────────────────────────────────
            ['key' => 'app_name',       'value' => 'Clinovia',                                                'type' => 'string',  'group' => 'general', 'description' => 'Application name shown in the navbar'],
            ['key' => 'app_short_name', 'value' => 'Clinovia',                                                'type' => 'string',  'group' => 'general', 'description' => 'Short application name'],
            ['key' => 'org_name',       'value' => 'Clinovia',  'type' => 'string',  'group' => 'general', 'description' => 'Full organization/school name'],
            ['key' => 'org_short_name', 'value' => 'Clinovia', 'type' => 'string',  'group' => 'general', 'description' => 'Short organization name/abbreviation'],

            // ── Clinic ───────────────────────────────────────────────────────
            ['key' => 'clinic_name',        'value' => 'Clinovia School Clinic',  'type' => 'string',  'group' => 'clinic',  'description' => 'Clinic name (used on reports)'],
            ['key' => 'clinic_address',     'value' => '',                         'type' => 'string',  'group' => 'clinic',  'description' => 'Clinic address'],
            ['key' => 'clinic_contact',     'value' => '',                         'type' => 'string',  'group' => 'clinic',  'description' => 'Clinic contact number'],
            ['key' => 'clinic_email',       'value' => 'clinic@clinovia.app',      'type' => 'string',  'group' => 'clinic',  'description' => 'Clinic email address'],
            ['key' => 'clinic_status_text', 'value' => 'Clinic Online',                                       'type' => 'string',  'group' => 'clinic',  'description' => 'Status text shown in the top bar'],

            // Patient categories (JSON list)
            ['key' => 'patient_categories', 'value' => '["college","senior_high","junior_high","elementary","kinder","daycare","teacher","employee","visitor","other"]', 'type' => 'json', 'group' => 'clinic', 'description' => 'Patient category options (JSON array)'],

            // Medicine units (JSON list)
            ['key' => 'medicine_units', 'value' => '["tablets","capsules","ml","vials","sachets","ampules","suppositories","drops","patches","other"]', 'type' => 'json', 'group' => 'clinic', 'description' => 'Medicine unit options (JSON array)'],

            // ── Academic ─────────────────────────────────────────────────────
            [
                'key'         => 'year_levels',
                'value'       => '["Kinder 1","Kinder 2","Grade 1","Grade 2","Grade 3","Grade 4","Grade 5","Grade 6","Grade 7","Grade 8","Grade 9","Grade 10","Grade 11","Grade 12","1st Year","2nd Year","3rd Year","4th Year","5th Year"]',
                'type'        => 'json',
                'group'       => 'academic',
                'description' => 'Year level options shown in patient forms',
            ],
            [
                'key'         => 'sections',
                'value'       => '["Section A","Section B","Section C","Section D","Block 1","Block 2","Block 3","Block 4"]',
                'type'        => 'json',
                'group'       => 'academic',
                'description' => 'Section options shown in patient forms',
            ],
            [
                'key'         => 'program_strands',
                'value'       => '["BSIT","BSCS","BSN","BSED","BSHM","BSBA","ABM","STEM","HUMSS","GAS","TVL","SPORTS","A&D"]',
                'type'        => 'json',
                'group'       => 'academic',
                'description' => 'Program/strand options shown in patient forms',
            ],

            // ── Notifications ────────────────────────────────────────────────
            ['key' => 'low_stock_threshold',    'value' => '10',  'type' => 'integer', 'group' => 'notifications', 'description' => 'Low stock alert threshold'],
            ['key' => 'expiry_warning_days',    'value' => '30',  'type' => 'integer', 'group' => 'notifications', 'description' => 'Days before expiry to show warning'],
            ['key' => 'max_daily_appointments', 'value' => '50',  'type' => 'integer', 'group' => 'notifications', 'description' => 'Maximum appointments per day'],

            // ── SMS ──────────────────────────────────────────────────────────
            ['key' => 'sms_enabled',            'value' => 'false',    'type' => 'boolean', 'group' => 'sms', 'description' => 'Enable SMS notifications'],
            ['key' => 'sms_sender_name',         'value' => 'CLINOVIA', 'type' => 'string',  'group' => 'sms', 'description' => 'SMS sender name (max 11 chars)'],
            ['key' => 'sms_log_guardian_enabled','value' => 'true',     'type' => 'boolean', 'group' => 'sms', 'description' => 'Allow guardian SMS from patient log'],

            // SMS Templates
            [
                'key'         => 'sms_template_approval',
                'value'       => 'Dear {name}, your appointment at the school clinic on {date} at {time} has been approved. Please arrive 10 minutes early. - Clinovia',
                'type'        => 'text',
                'group'       => 'sms',
                'description' => 'Sent when an appointment is approved. Variables: {name}, {date}, {time}',
            ],
            [
                'key'         => 'sms_template_cancellation',
                'value'       => 'Dear {name}, your appointment on {date} at the school clinic has been cancelled. Reason: {reason}. Please contact us to reschedule. - Clinovia',
                'type'        => 'text',
                'group'       => 'sms',
                'description' => 'Sent when an appointment is cancelled. Variables: {name}, {date}, {reason}',
            ],
            [
                'key'         => 'sms_template_clinic_log',
                'value'       => 'Dear {guardian}, your ward {name} visited the school clinic at {time} for {complaint}. Action taken: {treatment}. - Clinovia',
                'type'        => 'text',
                'group'       => 'sms',
                'description' => 'Sent to guardian when logging a patient visit. Variables: {guardian}, {name}, {time}, {complaint}, {treatment}',
            ],

            // ── AI Assistant ─────────────────────────────────────────────────
            ['key' => 'ai_model',   'value' => 'llama-3.3-70b-versatile', 'type' => 'string',  'group' => 'ai', 'description' => 'Groq model ID for Cobi AI'],
            ['key' => 'ai_enabled', 'value' => 'true',                    'type' => 'boolean', 'group' => 'ai', 'description' => 'Enable Cobi AI assistant'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('Clinovia settings seeded successfully.');
    }
}

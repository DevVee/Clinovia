<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $new = [
            // ── Academic dropdowns ──────────────────────────────────────────
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

            // ── SMS: clinic log guardian notification ───────────────────────
            [
                'key'         => 'sms_template_clinic_log',
                'value'       => 'Dear {guardian}, your ward {name} visited the school clinic at {time} for {complaint}. Action taken: {treatment}. - Clinovia',
                'type'        => 'text',
                'group'       => 'sms',
                'description' => 'Sent to guardian when a patient is logged. Variables: {guardian}, {name}, {time}, {complaint}, {treatment}',
            ],
            [
                'key'         => 'sms_log_guardian_enabled',
                'value'       => 'true',
                'type'        => 'boolean',
                'group'       => 'sms',
                'description' => 'Allow SMS notification to guardian when logging a patient visit',
            ],
        ];

        foreach ($new as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // ── Rebrand: update existing app_name / app_short_name ──────────────
        Setting::where('key', 'app_name')->update(['value' => 'Clinovia']);
        Setting::where('key', 'app_short_name')->update(['value' => 'Clinovia']);
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'year_levels', 'sections', 'program_strands',
            'sms_template_clinic_log', 'sms_log_guardian_enabled',
        ])->delete();

        Setting::where('key', 'app_name')->update(['value' => 'Smart School Clinic Management System']);
        Setting::where('key', 'app_short_name')->update(['value' => 'SSCMS']);
    }
};

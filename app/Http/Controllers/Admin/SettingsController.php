<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('manage-settings');

        // Group all settings; include new 'academic' group
        $settings = Setting::all()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorize('manage-settings');

        $request->validate([
            // Clinic / General
            'app_name'                  => ['nullable', 'string', 'max:100'],
            'org_name'                  => ['nullable', 'string', 'max:200'],
            'org_short_name'            => ['nullable', 'string', 'max:20'],
            'clinic_name'               => ['nullable', 'string', 'max:150'],
            'clinic_address'            => ['nullable', 'string', 'max:300'],
            'clinic_contact'            => ['nullable', 'string', 'max:30'],
            'clinic_email'              => ['nullable', 'email', 'max:100'],
            'clinic_status_text'        => ['nullable', 'string', 'max:50'],

            // Academic
            'year_levels'               => ['nullable', 'string'],
            'sections'                  => ['nullable', 'string'],
            'program_strands'           => ['nullable', 'string'],
            'patient_categories'        => ['nullable', 'string'],

            // Pharmacy / Notifications
            'medicine_units'            => ['nullable', 'string'],
            'low_stock_threshold'       => ['nullable', 'integer', 'min:1', 'max:9999'],
            'expiry_warning_days'       => ['nullable', 'integer', 'min:1', 'max:365'],
            'max_daily_appointments'    => ['nullable', 'integer', 'min:1', 'max:9999'],

            // SMS
            'sms_sender_name'           => ['nullable', 'string', 'max:11'],
            'sms_template_approval'     => ['nullable', 'string', 'max:320'],
            'sms_template_cancellation' => ['nullable', 'string', 'max:320'],
            'sms_template_clinic_log'   => ['nullable', 'string', 'max:320'],

            // AI
            'ai_model'                  => ['nullable', 'string', 'max:80'],
        ]);

        $data = $request->except(['_token']);

        // ── Boolean fields: unchecked = absent from POST → set to 'false' ──────
        $booleanKeys = Setting::where('type', 'boolean')->pluck('key')->all();
        foreach ($booleanKeys as $boolKey) {
            if (! array_key_exists($boolKey, $data)) {
                $data[$boolKey] = 'false';
            }
        }

        // ── JSON fields: validate + re-encode cleanly ────────────────────────
        $jsonKeys = ['year_levels', 'sections', 'program_strands', 'patient_categories', 'medicine_units'];
        foreach ($jsonKeys as $jk) {
            if (isset($data[$jk])) {
                $decoded = json_decode($data[$jk], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withErrors([$jk => "Invalid format for {$jk}. Please check your input."]);
                }
                $data[$jk] = json_encode(array_values(array_filter(array_map('trim', $decoded))));
            }
        }

        // ── Save each key that exists in the DB (guards against injection) ───
        foreach ($data as $key => $value) {
            if (Setting::where('key', $key)->exists()) {
                Setting::set($key, $value ?? '');
            }
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}

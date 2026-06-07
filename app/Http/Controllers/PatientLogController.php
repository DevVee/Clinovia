<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientLog;
use App\Models\Setting;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientLogController extends Controller
{
    public function __construct(private readonly SmsService $sms) {}

    /* ------------------------------------------------------------------ */
    /*  INDEX — Clinic Logbook                                              */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $this->authorize('view-consultations');

        $date   = $request->input('date', today()->toDateString());
        $search = $request->input('search', '');

        $query = PatientLog::with(['patient', 'loggedBy'])
            ->whereDate('log_date', $date);

        if ($search) {
            $query->whereHas('patient', fn ($q) => $q
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name',  'like', "%{$search}%")
                ->orWhere('patient_number', 'like', "%{$search}%")
            );
        }

        $logs = $query->orderBy('time_in', 'desc')->paginate(25)->withQueryString();

        $stats = [
            'today' => PatientLog::today()->count(),
            'week'  => PatientLog::whereBetween('log_date', [
                today()->startOfWeek(), today()->endOfWeek(),
            ])->count(),
            'month' => PatientLog::whereMonth('log_date', today()->month)
                ->whereYear('log_date', today()->year)
                ->count(),
        ];

        return view('patient-logs.index', compact('logs', 'date', 'search', 'stats'));
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE                                                              */
    /* ------------------------------------------------------------------ */
    public function create(Request $request)
    {
        $this->authorize('create-consultations');

        $patients = Patient::active()
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'patient_number',
                   'guardian_name', 'guardian_contact', 'category',
                   'section', 'year_level', 'program_strand']);

        $selectedPatient = $request->input('patient_id');

        return view('patient-logs.create', compact('patients', 'selectedPatient'));
    }

    /* ------------------------------------------------------------------ */
    /*  STORE                                                               */
    /* ------------------------------------------------------------------ */
    public function store(Request $request)
    {
        $this->authorize('create-consultations');

        $validated = $request->validate([
            'patient_id'      => ['required', 'exists:patients,id'],
            'log_date'        => ['required', 'date'],
            'time_in'         => ['required', 'date_format:H:i'],
            'time_out'        => ['nullable', 'date_format:H:i'],
            'chief_complaint' => ['required', 'string', 'max:500'],
            'vital_temp'      => ['nullable', 'numeric', 'between:30,45'],
            'vital_bp'        => ['nullable', 'string', 'max:20'],
            'vital_pulse'     => ['nullable', 'integer', 'between:1,300'],
            'vital_weight'    => ['nullable', 'numeric', 'between:1,300'],
            'vital_height'    => ['nullable', 'numeric', 'between:1,300'],
            'assessment'      => ['nullable', 'string', 'max:1000'],
            'treatment'       => ['nullable', 'string', 'max:500'],
            'disposition'     => ['required', 'in:rest_in_clinic,returned_to_class,sent_home,referred_to_hospital,further_observation'],
            'sms_guardian'    => ['nullable'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        // Build vitals JSON from individual fields
        $vitals = array_filter([
            'temperature'   => $request->filled('vital_temp')   ? $request->vital_temp   : null,
            'blood_pressure'=> $request->filled('vital_bp')     ? $request->vital_bp     : null,
            'pulse'         => $request->filled('vital_pulse')  ? $request->vital_pulse  : null,
            'weight'        => $request->filled('vital_weight') ? $request->vital_weight : null,
            'height'        => $request->filled('vital_height') ? $request->vital_height : null,
        ], fn ($v) => ! is_null($v));

        $log = PatientLog::create([
            'patient_id'      => $validated['patient_id'],
            'logged_by'       => auth()->id(),
            'log_date'        => $validated['log_date'],
            'time_in'         => $validated['time_in'],
            'time_out'        => $validated['time_out'] ?? null,
            'chief_complaint' => $validated['chief_complaint'],
            'vital_signs'     => $vitals ?: null,
            'assessment'      => $validated['assessment'] ?? null,
            'treatment'       => $validated['treatment'] ?? null,
            'disposition'     => $validated['disposition'],
            'sms_guardian'    => $request->boolean('sms_guardian'),
            'sms_sent'        => false,
            'notes'           => $validated['notes'] ?? null,
        ]);

        $smsSent = false;

        // ── Optionally notify guardian ────────────────────────────────────
        if ($request->boolean('sms_guardian')) {
            $patient = Patient::find($validated['patient_id']);
            $number  = $patient->guardian_contact ?? $patient->contact_number;

            if ($number) {
                $template = Setting::get(
                    'sms_template_clinic_log',
                    'Dear {guardian}, your ward {name} visited the school clinic at {time} for {complaint}. Action taken: {treatment}. - Clinovia'
                );

                $message = str_replace(
                    ['{guardian}', '{name}',              '{time}',                                                    '{complaint}',          '{treatment}'],
                    [$patient->guardian_name ?? 'Parent', $patient->first_name, Carbon::parse($log->time_in)->format('h:i A'), $log->chief_complaint, $log->treatment ?? 'Attended by clinic staff'],
                    $template
                );

                $smsLog  = $this->sms->send($number, $message, $patient->guardian_name ?? $patient->full_name, $log);
                $smsSent = $smsLog->status === 'sent';
                $log->update(['sms_sent' => $smsSent]);
            }
        }

        $msg = "Patient log for {$log->patient->full_name} saved.";
        if ($smsSent) $msg .= ' SMS sent to guardian ✓';

        return redirect()->route('patient-logs.index')->with('success', $msg);
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(PatientLog $patientLog)
    {
        $this->authorize('view-consultations');
        $patientLog->load(['patient', 'loggedBy']);

        return view('patient-logs.show', ['log' => $patientLog]);
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT                                                                */
    /* ------------------------------------------------------------------ */
    public function edit(PatientLog $patientLog)
    {
        $this->authorize('update-consultations');

        $patientLog->load('patient');

        $patients = Patient::active()
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'patient_number',
                   'guardian_name', 'guardian_contact', 'category',
                   'section', 'year_level', 'program_strand']);

        return view('patient-logs.edit', compact('patientLog', 'patients'));
    }

    /* ------------------------------------------------------------------ */
    /*  UPDATE                                                              */
    /* ------------------------------------------------------------------ */
    public function update(Request $request, PatientLog $patientLog)
    {
        $this->authorize('update-consultations');

        $validated = $request->validate([
            'patient_id'      => ['required', 'exists:patients,id'],
            'log_date'        => ['required', 'date'],
            'time_in'         => ['required', 'date_format:H:i'],
            'time_out'        => ['nullable', 'date_format:H:i'],
            'chief_complaint' => ['required', 'string', 'max:500'],
            'vital_temp'      => ['nullable', 'numeric', 'between:30,45'],
            'vital_bp'        => ['nullable', 'string', 'max:20'],
            'vital_pulse'     => ['nullable', 'integer', 'between:1,300'],
            'vital_weight'    => ['nullable', 'numeric', 'between:1,300'],
            'vital_height'    => ['nullable', 'numeric', 'between:1,300'],
            'assessment'      => ['nullable', 'string', 'max:1000'],
            'treatment'       => ['nullable', 'string', 'max:500'],
            'disposition'     => ['required', 'in:rest_in_clinic,returned_to_class,sent_home,referred_to_hospital,further_observation'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $vitals = array_filter([
            'temperature'    => $request->filled('vital_temp')   ? $request->vital_temp   : null,
            'blood_pressure' => $request->filled('vital_bp')     ? $request->vital_bp     : null,
            'pulse'          => $request->filled('vital_pulse')  ? $request->vital_pulse  : null,
            'weight'         => $request->filled('vital_weight') ? $request->vital_weight : null,
            'height'         => $request->filled('vital_height') ? $request->vital_height : null,
        ], fn ($v) => ! is_null($v));

        $patientLog->update([
            'patient_id'      => $validated['patient_id'],
            'log_date'        => $validated['log_date'],
            'time_in'         => $validated['time_in'],
            'time_out'        => $validated['time_out'] ?? null,
            'chief_complaint' => $validated['chief_complaint'],
            'vital_signs'     => $vitals ?: null,
            'assessment'      => $validated['assessment'] ?? null,
            'treatment'       => $validated['treatment'] ?? null,
            'disposition'     => $validated['disposition'],
            'notes'           => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('patient-logs.show', $patientLog)
            ->with('success', 'Log entry updated.');
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(PatientLog $patientLog)
    {
        $this->authorize('delete-consultations');
        $patientLog->delete();

        return redirect()->route('patient-logs.index')->with('success', 'Log entry removed.');
    }
}

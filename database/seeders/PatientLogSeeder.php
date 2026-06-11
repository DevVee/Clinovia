<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds patient_logs and sms_logs for demo purposes.
 * Safe to run even when patients/consultations already exist.
 */
class PatientLogSeeder extends Seeder
{
    private array $nurseIds        = [];
    private array $patientIds      = [];
    private array $minorPatientIds = [];
    private Carbon $today;

    private array $complaints = [
        'Headache, moderate severity',
        'Fever (38.2°C), generalized body malaise',
        'Stomachache, periumbilical pain',
        'Cough and colds, productive cough',
        'Dizziness, lightheadedness',
        'Nausea and vomiting, 2 episodes',
        'Wound on right knee, abrasion from fall',
        'Sore throat, mild difficulty swallowing',
        'Abdominal pain, crampy in nature',
        'Dysmenorrhea, severe cramping',
        'Allergic reaction, urticaria on arms',
        'Eye irritation, redness and watering',
        'Low back pain, dull aching',
        'Toothache, right lower molar',
        'Sprained left ankle from PE class',
        'Insect bite, left forearm, with erythema',
        'LBM, 3 episodes since morning',
        'Fainting episode, syncopal attack',
        'Heat exhaustion after PE class',
        'Blunt trauma to right shin from soccer',
        'Epistaxis, right nostril',
        'Hyperventilation, anxiety-related',
        'Migraine with aura, right-sided',
        'Asthma attack, mild wheeze',
        'Laceration on left palm, about 2 cm',
    ];

    private array $assessments = [
        'Patient appears in mild distress. Vital signs stable. BP 110/70, HR 82, Temp 37.0°C, RR 18.',
        'Patient febrile. BP 100/60, HR 98, Temp 38.5°C, RR 20. Skin warm and flushed.',
        'Abdomen soft with mild tenderness at epigastric area. Bowel sounds normoactive.',
        'Pharynx hyperemic. Tonsils slightly enlarged. No exudates noted.',
        'Wound cleaned and assessed. No signs of infection. Edges approximated.',
        'BP elevated at 150/90. Patient reports not taking maintenance medication today.',
        'Lungs clear to auscultation. No adventitious sounds. SpO2 98%.',
        'Alert and oriented x3. No focal neurological deficits.',
        'Skin: urticarial wheals, non-confluent, on bilateral upper extremities.',
        'Eyes: conjunctival injection bilateral. No discharge. Pupils equal and reactive.',
    ];

    private array $treatments = [
        'Administered Paracetamol 500mg tablet. Instructed to rest and increase fluid intake.',
        'Tepid sponge bath done. Paracetamol 500mg given. Advised to see physician if fever persists.',
        'Wound irrigated with NSS, cleaned with Betadine, dressed with sterile gauze.',
        'ORS given. Advised soft diet and increased fluid intake. Return if symptoms worsen.',
        'Ice pack applied to affected area. Advised to elevate and rest.',
        'Ibuprofen 400mg given for pain relief. Instructed to avoid strenuous activity.',
        'Mefenamic acid 500mg administered. Heating pad applied to abdomen.',
        'Cetirizine 10mg given for allergic reaction. Cold compress applied to site.',
        'Referred to clinic physician for further evaluation and management.',
        'Monitoring and observation for 30 minutes. Vital signs stable before discharge.',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        if (DB::table('patient_logs')->count() > 0) {
            $this->command->warn('Patient logs already exist. Skipping PatientLogSeeder.');
            return;
        }

        $this->today = Carbon::create(2026, 6, 8);

        $this->command->info('📋  Seeding patient daily logs & SMS logs…');

        // Load existing patients and nurses
        $this->patientIds = Patient::pluck('id')->toArray();

        if (empty($this->patientIds)) {
            $this->command->error('No patients found. Run DemoDataSeeder first.');
            return;
        }

        // Minor patients: categories with young patients
        $this->minorPatientIds = Patient::whereIn('category', [
            'daycare', 'kinder', 'elementary', 'junior_high',
        ])->pluck('id')->toArray();

        // Nurses
        $this->nurseIds = User::role('nurse')->pluck('id')->toArray();
        if (empty($this->nurseIds)) {
            // Fall back to any active user
            $this->nurseIds = User::where('is_active', true)->pluck('id')->toArray();
        }

        DB::transaction(function () {
            $this->seedPatientLogs();
            $this->command->info('  ✓ Patient daily logs seeded');

            $this->seedSmsLogs();
            $this->command->info('  ✓ SMS notification logs seeded');
        });

        $logCount = DB::table('patient_logs')->count();
        $smsCount = DB::table('sms_logs')->count();
        $this->command->info('');
        $this->command->info("🎉  Done! {$logCount} patient logs, {$smsCount} SMS records created.");
    }

    // ─── Patient Daily Logs ───────────────────────────────────────────────────

    private array $smsPendingFromLogs = [];

    private function seedPatientLogs(): void
    {
        $schoolStart = Carbon::create(2025, 8, 4);
        $schoolEnd   = $this->today->copy();

        $holidays = [
            '2025-08-25', '2025-11-01', '2025-11-02', '2025-11-30',
            '2025-12-08', '2025-12-22', '2025-12-23', '2025-12-24',
            '2025-12-25', '2025-12-26', '2025-12-29', '2025-12-30', '2025-12-31',
            '2026-01-01', '2026-01-29', '2026-03-31',
            '2026-04-02', '2026-04-03', '2026-04-09', '2026-05-01',
        ];

        $allLogs    = [];
        $smsPending = [];
        $current    = $schoolStart->copy();

        while ($current->lte($schoolEnd)) {
            if ($current->isWeekend() || in_array($current->toDateString(), $holidays)) {
                $current->addDay();
                continue;
            }

            // Seasonal volume: rainy/cold seasons see more clinic visits
            $month      = $current->month;
            $dailyCount = match (true) {
                in_array($month, [8, 9, 10]) => rand(5, 10), // Rainy season
                in_array($month, [1, 2])     => rand(4, 8),  // Cold season
                in_array($month, [12])       => rand(2, 5),  // Christmas partial
                default                      => rand(3, 7),  // Normal
            };

            // Mondays see a small bump (weekend catch-up)
            if ($current->isMonday()) {
                $dailyCount = (int) ($dailyCount * 1.15);
            }

            for ($i = 0; $i < $dailyCount; $i++) {
                $patientId   = $this->patientIds[array_rand($this->patientIds)];
                $nurseId     = $this->nurseIds[array_rand($this->nurseIds)];
                $complaint   = $this->complaints[array_rand($this->complaints)];
                $disposition = $this->randomDisposition();
                $isMinor     = in_array($patientId, $this->minorPatientIds);

                $timeInH  = rand(7, 15);
                $timeInM  = rand(0, 59);
                $timeIn   = sprintf('%02d:%02d:00', $timeInH, $timeInM);

                // 80% of patients get a time_out recorded
                $duration = rand(8, 65);
                $outH     = (int)(($timeInH * 60 + $timeInM + $duration) / 60);
                $outM     = ($timeInM + $duration) % 60;
                $timeOut  = rand(0, 4) > 0 ? sprintf('%02d:%02d:00', min($outH, 17), $outM) : null;

                // Guardian SMS: minors who are sent home / referred / under observation
                $needsSms    = $isMinor && in_array($disposition, ['sent_home', 'referred_to_hospital', 'further_observation']);
                $smsGuardian = $needsSms && rand(0, 3) > 0;   // 75% chance flagged
                $smsSent     = $smsGuardian && rand(0, 9) < 8; // 80% sent successfully

                $logTime = $current->copy()->setTime($timeInH, $timeInM)->toDateTimeString();

                $allLogs[] = [
                    'patient_id'      => $patientId,
                    'logged_by'       => $nurseId,
                    'log_date'        => $current->toDateString(),
                    'time_in'         => $timeIn,
                    'time_out'        => $timeOut,
                    'chief_complaint' => $complaint,
                    'vital_signs'     => json_encode($this->randomVitalSigns()),
                    'assessment'      => $this->assessments[array_rand($this->assessments)],
                    'treatment'       => $this->treatments[array_rand($this->treatments)],
                    'disposition'     => $disposition,
                    'sms_guardian'    => $smsGuardian ? 1 : 0,
                    'sms_sent'        => $smsSent     ? 1 : 0,
                    'notes'           => rand(0, 4) === 0
                        ? 'Patient monitored for 30 minutes before discharge.'
                        : null,
                    'created_at'      => $logTime,
                    'updated_at'      => $logTime,
                ];

                if ($smsGuardian) {
                    $smsPending[] = [
                        'patientId'   => $patientId,
                        'nurseId'     => $nurseId,
                        'disposition' => $disposition,
                        'complaint'   => $complaint,
                        'sent'        => $smsSent,
                        'date'        => $current->toDateString(),
                        'logTime'     => $logTime,
                    ];
                }
            }

            $current->addDay();
        }

        foreach (array_chunk($allLogs, 200) as $chunk) {
            DB::table('patient_logs')->insert($chunk);
        }

        $this->smsPendingFromLogs = $smsPending;
    }

    // ─── SMS Notification Logs ────────────────────────────────────────────────

    private function seedSmsLogs(): void
    {
        $smsRows = [];
        $adminId = User::role('administrator')->first()?->id ?? 1;

        // 1) Guardian notifications from patient log events
        foreach ($this->smsPendingFromLogs as $entry) {
            $patient = Patient::find($entry['patientId']);
            if (! $patient) {
                continue;
            }

            $guardianName   = $patient->guardian_name   ?? 'Parent/Guardian';
            $guardianNumber = $patient->guardian_contact ?? ('09' . rand(100000000, 999999999));
            $status         = $entry['sent'] ? 'sent' : 'failed';
            $sentAt         = $entry['sent']
                ? Carbon::parse($entry['logTime'])->addMinutes(rand(1, 12))->toDateTimeString()
                : null;

            $patientName = "{$patient->first_name} {$patient->last_name}";

            $message = match ($entry['disposition']) {
                'sent_home' =>
                    "Dear {$guardianName}, your child/ward {$patientName} was seen at the school clinic " .
                    "on {$entry['date']} due to: {$entry['complaint']}. " .
                    "They have been sent home for rest. Please contact the clinic for updates. — Clinovia School Clinic",

                'referred_to_hospital' =>
                    "URGENT: Dear {$guardianName}, your child/ward {$patientName} requires immediate " .
                    "medical attention and has been referred to a hospital. Please proceed immediately. " .
                    "— Clinovia School Clinic",

                default =>
                    "Dear {$guardianName}, {$patientName} is currently under observation at the school clinic " .
                    "({$entry['date']}). Complaint: {$entry['complaint']}. We will keep you updated. " .
                    "— Clinovia School Clinic",
            };

            $smsRows[] = [
                'recipient_number' => $guardianNumber,
                'recipient_name'   => $guardianName,
                'message'          => $message,
                'status'           => $status,
                'reference_id'     => $entry['patientId'],
                'reference_type'   => 'App\\Models\\Patient',
                'api_response'     => $entry['sent']
                    ? json_encode([
                        'message_id' => 'MSG-' . strtoupper(substr(md5(rand()), 0, 10)),
                        'status'     => 'delivered',
                      ])
                    : null,
                'sent_at'       => $sentAt,
                'error_message' => ! $entry['sent'] ? 'Network timeout. Message delivery failed.' : null,
                'created_by'    => $entry['nurseId'],
                'created_at'    => $entry['logTime'],
                'updated_at'    => $entry['logTime'],
            ];
        }

        // 2) Appointment reminder SMS — next 7 school days
        $this->command->line('      Generating appointment reminders…');
        for ($d = 1; $d <= 7; $d++) {
            $apptDate = $this->today->copy()->addDays($d);
            if ($apptDate->isWeekend()) {
                continue;
            }

            $reminderCount = rand(3, 7);
            for ($i = 0; $i < $reminderCount; $i++) {
                $patientId = $this->patientIds[array_rand($this->patientIds)];
                $patient   = Patient::find($patientId);
                if (! $patient) {
                    continue;
                }

                $number     = $patient->contact_number ?? ('09' . rand(100000000, 999999999));
                $name       = "{$patient->first_name} {$patient->last_name}";
                $status     = rand(0, 9) < 9 ? 'sent' : 'failed';
                $reminderAt = $this->today->copy()
                    ->subDays(rand(1, 2))
                    ->setTime(rand(8, 17), rand(0, 59))
                    ->toDateTimeString();

                $smsRows[] = [
                    'recipient_number' => $number,
                    'recipient_name'   => $name,
                    'message'          =>
                        "Dear {$name}, this is a reminder for your clinic appointment on " .
                        Carbon::parse($apptDate->toDateString())->format('F j, Y') .
                        ". Please arrive on time and bring your school ID. — Clinovia School Clinic",
                    'status'           => $status,
                    'reference_id'     => $patientId,
                    'reference_type'   => 'App\\Models\\Patient',
                    'api_response'     => $status === 'sent'
                        ? json_encode([
                            'message_id' => 'MSG-' . strtoupper(substr(md5(rand()), 0, 10)),
                            'status'     => 'delivered',
                          ])
                        : null,
                    'sent_at'       => $status === 'sent' ? $reminderAt : null,
                    'error_message' => $status === 'failed' ? 'Invalid recipient number.' : null,
                    'created_by'    => $adminId,
                    'created_at'    => $reminderAt,
                    'updated_at'    => $reminderAt,
                ];
            }
        }

        foreach (array_chunk($smsRows, 200) as $chunk) {
            DB::table('sms_logs')->insert($chunk);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function randomVitalSigns(): array
    {
        $tempVal   = rand(360, 389);                    // 36.0–38.9 °C
        $systolic  = rand(90, 145);
        $diastolic = max(55, $systolic - rand(30, 55));
        $pulse     = rand(58, 102);
        $weight    = rand(22, 88);
        $height    = rand(108, 178);
        $spo2      = rand(94, 100);

        return [
            'temperature'    => number_format($tempVal / 10, 1) . '°C',
            'blood_pressure' => "{$systolic}/{$diastolic} mmHg",
            'pulse_rate'     => "{$pulse} bpm",
            'weight'         => "{$weight} kg",
            'height'         => "{$height} cm",
            'oxygen_sat'     => "{$spo2}%",
        ];
    }

    /** Weighted disposition picker for a school clinic */
    private function randomDisposition(): string
    {
        $roll = rand(1, 100);
        return match (true) {
            $roll <= 52 => 'returned_to_class',   // 52% — most common outcome
            $roll <= 77 => 'rest_in_clinic',       // 25%
            $roll <= 91 => 'sent_home',            // 14%
            $roll <= 98 => 'further_observation',  //  7%
            default     => 'referred_to_hospital', //  2%
        };
    }
}

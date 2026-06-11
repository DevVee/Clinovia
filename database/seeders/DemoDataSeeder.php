<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\DispensingRecord;
use App\Models\InventoryTransaction;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    private array $nurseIds       = [];
    private array $patientIds     = [];
    private array $minorPatientIds = [];   // patients who are minors (need guardian SMS)
    private array $medicineMap    = [];    // id => current_quantity
    private Carbon $today;

    // ─── Filipino first names (M/F) ───────────────────────────────────────────
    private array $maleFirst = [
        'Juan', 'Jose', 'Michael', 'Mark', 'Paolo', 'Rodel', 'Angelo', 'Christian',
        'Daniel', 'Emmanuel', 'Francis', 'Gabriel', 'Hans', 'Ivan', 'James', 'Kevin',
        'Lance', 'Marco', 'Nathan', 'Oliver', 'Patrick', 'Ramon', 'Samuel', 'Tristan',
        'Victor', 'William', 'Adrian', 'Bryan', 'Carlos', 'Dennis', 'Eduardo', 'Felix',
        'Gerald', 'Harold', 'Jerome', 'Kenneth', 'Louie', 'Mario', 'Neil', 'Oscar',
        'Peter', 'Renz', 'Sherwin', 'Tonyo', 'Ulysses', 'Vince', 'Warren', 'Xavier',
    ];

    private array $femaleFirst = [
        'Maria', 'Ana', 'Kristine', 'Angelica', 'Jenna', 'Marites', 'Lovely', 'Precious',
        'Charlene', 'Diana', 'Elena', 'Fatima', 'Grace', 'Hannah', 'Isabel', 'Jasmine',
        'Karen', 'Liza', 'Monica', 'Nadia', 'Olivia', 'Patricia', 'Rachel', 'Sofia',
        'Tricia', 'Yvonne', 'Abigail', 'Bianca', 'Carla', 'Denise', 'Elaine', 'Francine',
        'Gloria', 'Hazel', 'Irene', 'Jenny', 'Katherine', 'Lourdes', 'Michelle', 'Nicole',
        'Pamela', 'Queenie', 'Riza', 'Sheila', 'Teresa', 'Uma', 'Veronica', 'Wilma',
    ];

    private array $lastNames = [
        'Santos', 'Reyes', 'Cruz', 'Garcia', 'Mendoza', 'Torres', 'Ramos', 'Aquino',
        'Bautista', 'Dela Cruz', 'Ramirez', 'Lopez', 'Gonzales', 'Diaz', 'Flores',
        'Salazar', 'Castillo', 'Villanueva', 'Morales', 'Navarro', 'Pascual', 'Lim',
        'Tan', 'Co', 'Sy', 'Uy', 'Go', 'Chua', 'Ocampo', 'Soriano', 'Fernandez',
        'Aguilar', 'Manalo', 'Macaraeg', 'Peralta', 'Tolentino', 'Espiritu', 'Macapagal',
        'Pangilinan', 'Guerrero', 'Magno', 'Robles', 'Santiago', 'Velasco', 'Zamora',
        'Almario', 'Baluyot', 'Cabrera', 'Delos Reyes', 'Evangelista',
    ];

    // ─── Consultation data ────────────────────────────────────────────────────
    private array $complaints = [
        // Common (high weight)
        'Headache, moderate severity',
        'Fever (38.2°C), generalized body malaise',
        'Stomachache, periumbilical pain',
        'Cough and colds, productive cough',
        'Dizziness, lightheadedness',
        'Nausea and vomiting, 2 episodes',
        'Wound on right knee, abrasion from fall',
        'Sore throat, mild difficulty swallowing',
        'Laceration on left palm, about 2 cm',
        'Abdominal pain, crampy in nature',
        // Moderate
        'Dysmenorrhea, severe cramping',
        'Allergic reaction, urticaria on arms',
        'Eye irritation, redness and watering',
        'Low back pain, dull aching',
        'Toothache, right lower molar',
        'Epistaxis, right nostril',
        'Sprained left ankle from PE class',
        'Insect bite, left forearm, with erythema',
        'Migraine with aura, right-sided',
        'LBM, 3 episodes since morning',
        // Less common
        'Fainting episode, syncopal attack',
        'Hypertension, BP 150/90 mmHg',
        'Asthma attack, mild wheeze',
        'Urinary tract infection symptoms',
        'Conjunctivitis, bilateral',
        'Wound infection, right thumb, with pus',
        'Blunt trauma to right shin from soccer',
        'Hyperventilation, anxiety-related',
        'Heat exhaustion after PE class',
        'Finger laceration, deep cut from glass',
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

    // ─── Medicine catalog ─────────────────────────────────────────────────────
    private array $medicineCatalog = [
        // Tablets
        ['name' => 'Paracetamol 500mg',       'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 2000, 'threshold' => 200, 'supplier' => 'United Pharma Inc.'],
        ['name' => 'Ibuprofen 400mg',          'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 1200, 'threshold' => 100, 'supplier' => 'United Pharma Inc.'],
        ['name' => 'Mefenamic Acid 500mg',     'cat' => 'Capsule',  'unit' => 'capsules', 'initial' => 800,  'threshold' => 80,  'supplier' => 'Pharex Health Corp.'],
        ['name' => 'Amoxicillin 500mg',        'cat' => 'Capsule',  'unit' => 'capsules', 'initial' => 600,  'threshold' => 60,  'supplier' => 'Pharex Health Corp.'],
        ['name' => 'Cetirizine 10mg',          'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 500,  'threshold' => 50,  'supplier' => 'United Pharma Inc.'],
        ['name' => 'Loratadine 10mg',          'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 400,  'threshold' => 40,  'supplier' => 'GlaxoSmithKline'],
        ['name' => 'Omeprazole 20mg',          'cat' => 'Capsule',  'unit' => 'capsules', 'initial' => 400,  'threshold' => 40,  'supplier' => 'Pharex Health Corp.'],
        ['name' => 'Domperidone 10mg',         'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 300,  'threshold' => 30,  'supplier' => 'Janssen Pharma'],
        ['name' => 'Loperamide 2mg',           'cat' => 'Capsule',  'unit' => 'capsules', 'initial' => 300,  'threshold' => 30,  'supplier' => 'United Pharma Inc.'],
        ['name' => 'Metformin 500mg',          'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 200,  'threshold' => 20,  'supplier' => 'Pharex Health Corp.'],
        ['name' => 'Amlodipine 5mg',           'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 200,  'threshold' => 20,  'supplier' => 'United Pharma Inc.'],
        ['name' => 'Vitamin C 500mg',          'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 1500, 'threshold' => 150, 'supplier' => 'Acebedo Pharma'],
        ['name' => 'Multivitamins (Centrum)',  'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 600,  'threshold' => 60,  'supplier' => 'Wyeth Philippines'],
        ['name' => 'Lagundi 300mg',            'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 500,  'threshold' => 50,  'supplier' => 'Pascual Pharma'],
        ['name' => 'Antacid (Kremil-S)',       'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 400,  'threshold' => 40,  'supplier' => 'Unilab Inc.'],
        ['name' => 'Zinc Sulfate 20mg',        'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 300,  'threshold' => 30,  'supplier' => 'Acebedo Pharma'],
        ['name' => 'Ferrous Sulfate 325mg',    'cat' => 'Tablet',   'unit' => 'tablets',  'initial' => 300,  'threshold' => 30,  'supplier' => 'Pharex Health Corp.'],
        // Syrups
        ['name' => 'Paracetamol Syrup 250mg/5mL','cat' => 'Syrup', 'unit' => 'bottles',  'initial' => 30,   'threshold' => 5,   'supplier' => 'Unilab Inc.'],
        ['name' => 'ORS (Hydrite)',            'cat' => 'Syrup',    'unit' => 'sachets',  'initial' => 150,  'threshold' => 20,  'supplier' => 'Acebedo Pharma'],
        ['name' => 'Lagundi Syrup 300mg/5mL',  'cat' => 'Syrup',   'unit' => 'bottles',  'initial' => 20,   'threshold' => 5,   'supplier' => 'Pascual Pharma'],
        // Topical / Ointments
        ['name' => 'Povidone-Iodine Solution 10%','cat' => 'Other','unit' => 'bottles',   'initial' => 40,   'threshold' => 5,   'supplier' => 'Mundipharma'],
        ['name' => 'Hydrogen Peroxide 3%',     'cat' => 'Other',    'unit' => 'bottles',  'initial' => 30,   'threshold' => 5,   'supplier' => 'Local Pharma'],
        ['name' => 'Neomycin + Polymyxin Ointment','cat' => 'Ointment','unit' => 'tubes', 'initial' => 25,   'threshold' => 5,   'supplier' => 'Pfizer'],
        ['name' => 'Hydrocortisone Cream 1%',  'cat' => 'Ointment', 'unit' => 'tubes',    'initial' => 20,   'threshold' => 5,   'supplier' => 'GlaxoSmithKline'],
        ['name' => 'Clotrimazole Cream 1%',    'cat' => 'Ointment', 'unit' => 'tubes',    'initial' => 15,   'threshold' => 3,   'supplier' => 'Sandoz'],
        // Drops
        ['name' => 'Artificial Tears (Systane)','cat' => 'Drops',  'unit' => 'bottles',   'initial' => 20,   'threshold' => 3,   'supplier' => 'Alcon Labs'],
        ['name' => 'Chloramphenicol Eye Drops', 'cat' => 'Drops',  'unit' => 'bottles',   'initial' => 15,   'threshold' => 3,   'supplier' => 'Pharex Health Corp.'],
        ['name' => 'Otrivin Nasal Drops',       'cat' => 'Drops',  'unit' => 'bottles',   'initial' => 15,   'threshold' => 3,   'supplier' => 'Novartis'],
        // Inhalers
        ['name' => 'Salbutamol Inhaler 100mcg', 'cat' => 'Inhaler','unit' => 'inhalers',  'initial' => 10,   'threshold' => 2,   'supplier' => 'GlaxoSmithKline'],
        // Supplies (as medicines)
        ['name' => 'Sterile Gauze 4x4',        'cat' => 'Other',   'unit' => 'pieces',    'initial' => 500,  'threshold' => 50,  'supplier' => 'Medilines Inc.'],
        ['name' => 'Elastic Bandage 3"',        'cat' => 'Other',   'unit' => 'pieces',    'initial' => 50,   'threshold' => 10,  'supplier' => 'Medilines Inc.'],
        ['name' => 'Adhesive Bandages (Box)',   'cat' => 'Other',   'unit' => 'boxes',     'initial' => 30,   'threshold' => 5,   'supplier' => 'J&J Philippines'],
    ];

    // ─── Appointment purposes ─────────────────────────────────────────────────
    private array $purposes = [
        'Annual physical examination',
        'Follow-up consultation',
        'Medical certificate request',
        'Pre-employment medical check',
        'Vaccination',
        'Blood pressure monitoring',
        'Wound dressing change',
        'Sports clearance',
        'Dental referral',
        'General check-up',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        if (Patient::count() > 0) {
            $this->command->warn('Demo patients already exist. Skipping DemoDataSeeder.');
            return;
        }

        $this->today = Carbon::create(2026, 6, 8);

        $this->command->info('🏥  Seeding Clinovia demo data (school year 2025–2026)…');

        DB::transaction(function () {
            $this->seedExtraNurses();
            $this->command->info('  ✓ Staff users ready');

            $this->seedMedicines();
            $this->command->info('  ✓ Medicines seeded (' . count($this->medicineMap) . ' items)');

            $this->seedPatients();
            $this->command->info('  ✓ Patients seeded (' . count($this->patientIds) . ' records)');

            $this->seedConsultationsAndDispensing();
            $this->command->info('  ✓ Consultations & dispensing records seeded');

            $this->seedAppointments();
            $this->command->info('  ✓ Appointments seeded');

            $this->seedPatientLogs();
            $this->command->info('  ✓ Patient daily logs seeded');

            $this->seedSmsLogs();
            $this->command->info('  ✓ SMS notification logs seeded');
        });

        $this->command->info('');
        $this->command->info('🎉  Demo data ready. Dashboard charts should now be populated!');
    }

    // ─── Staff ────────────────────────────────────────────────────────────────

    private function seedExtraNurses(): void
    {
        $nurses = [
            ['name' => 'RN Maria Lourdes Santos', 'email' => 'maria.santos@clinovia.app'],
            ['name' => 'RN Jose Antonio Reyes',   'email' => 'jose.reyes@clinovia.app'],
            ['name' => 'RN Kristine Villanueva',  'email' => 'kristine.v@clinovia.app'],
        ];

        foreach ($nurses as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles('nurse');
            $this->nurseIds[] = $user->id;
        }

        // Include any existing nurse accounts
        foreach (User::role('nurse')->pluck('id') as $id) {
            $this->nurseIds[] = $id;
        }
        $this->nurseIds = array_unique($this->nurseIds);
    }

    // ─── Medicines ────────────────────────────────────────────────────────────

    private function seedMedicines(): void
    {
        $categoryMap = MedicineCategory::pluck('id', 'name');
        $adminId     = User::role('administrator')->first()?->id ?? 1;
        $schoolStart = Carbon::create(2025, 8, 4);   // start of school year

        foreach ($this->medicineCatalog as $item) {
            $catId = $categoryMap[$item['cat']] ?? $categoryMap->first();

            // Stagger expiration dates realistically
            $expiration = Carbon::create(2027, rand(1, 12), rand(1, 28));

            $medicine = Medicine::firstOrCreate(
                ['name' => $item['name']],
                [
                    'category_id'        => $catId,
                    'unit'               => $item['unit'],
                    'quantity'           => $item['initial'],
                    'expiration_date'    => $expiration,
                    'batch_number'       => 'BN-' . strtoupper(substr(md5($item['name']), 0, 8)),
                    'supplier'           => $item['supplier'],
                    'low_stock_threshold'=> $item['threshold'],
                    'is_active'          => true,
                    'created_by'         => $adminId,
                ]
            );

            // Record the opening stock-in transaction
            InventoryTransaction::create([
                'medicine_id'      => $medicine->id,
                'transaction_type' => 'stock_in',
                'quantity'         => $item['initial'],
                'before_quantity'  => 0,
                'after_quantity'   => $item['initial'],
                'batch_number'     => $medicine->batch_number,
                'expiration_date'  => $expiration,
                'supplier'         => $item['supplier'],
                'notes'            => 'Opening stock — school year 2025-2026',
                'performed_by'     => $adminId,
                'created_at'       => $schoolStart,
                'updated_at'       => $schoolStart,
            ]);

            // Store current quantity (we'll decrement as we dispense)
            $this->medicineMap[$medicine->id] = $medicine->quantity;
        }
    }

    // ─── Patients ────────────────────────────────────────────────────────────

    private function seedPatients(): void
    {
        $adminId = User::role('administrator')->first()?->id ?? 1;

        // Distribution by category reflecting a typical school
        $distribution = [
            'college'     => 80,
            'senior_high' => 55,
            'junior_high' => 50,
            'elementary'  => 35,
            'kinder'      => 15,
            'daycare'     => 10,
            'teacher'     => 25,
            'employee'    => 15,
            'other'       => 5,
        ];

        $counter = 1;
        $usedNames = [];

        foreach ($distribution as $category => $count) {
            $programs = $this->programsByCategory($category);
            $sections = ['A', 'B', 'C', 'D'];

            for ($i = 0; $i < $count; $i++) {
                $sex       = rand(0, 1) ? 'male' : 'female';
                $firstName = $this->uniqueName($sex, $usedNames);
                $lastName  = $this->lastNames[array_rand($this->lastNames)];
                $usedNames[] = "$firstName $lastName";

                $isMinor   = in_array($category, ['kinder', 'daycare', 'elementary', 'junior_high']);
                $isStudent = ! in_array($category, ['teacher', 'employee', 'other']);

                $birthYear = match (true) {
                    $category === 'daycare'     => rand(2019, 2021),
                    $category === 'kinder'      => rand(2018, 2020),
                    $category === 'elementary'  => rand(2013, 2018),
                    $category === 'junior_high' => rand(2010, 2014),
                    $category === 'senior_high' => rand(2007, 2010),
                    $category === 'college'     => rand(2002, 2007),
                    default                     => rand(1975, 1998),
                };

                $program   = $programs[array_rand($programs)];
                $yearLevel = $isStudent ? $this->yearLevelForCategory($category) : null;

                $patient = Patient::create([
                    'patient_number'         => sprintf('%04d-%05d', 2025, $counter++),
                    'category'               => $category,
                    'first_name'             => $firstName,
                    'middle_name'            => rand(0, 3) > 0 ? $this->lastNames[array_rand($this->lastNames)] : null,
                    'last_name'              => $lastName,
                    'sex'                    => $sex,
                    'birthdate'              => Carbon::create($birthYear, rand(1, 12), rand(1, 28)),
                    'contact_number'         => '09' . rand(100000000, 999999999),
                    'email'                  => strtolower($firstName . '.' . str_replace(' ', '', $lastName)) . rand(1, 99) . '@email.com',
                    'address'                => $this->randomAddress(),
                    'year_level'             => $isStudent ? $yearLevel : null,
                    'program_strand'         => $isStudent ? $program : null,
                    'section'                => $isStudent ? ($sections[array_rand($sections)]) : null,
                    'guardian_name'          => $isMinor ? $this->femaleFirst[array_rand($this->femaleFirst)] . ' ' . $lastName : null,
                    'guardian_relationship'  => $isMinor ? (rand(0, 1) ? 'Mother' : 'Father') : null,
                    'guardian_contact'       => $isMinor ? '09' . rand(100000000, 999999999) : null,
                    'blood_type'             => $this->randomBloodType(),
                    'allergies'              => rand(0, 5) === 0 ? $this->randomAllergy() : null,
                    'medical_conditions'     => $this->randomCondition($category),
                    'is_active'              => true,
                    'created_by'             => $adminId,
                ]);

                $this->patientIds[] = $patient->id;

                if ($isMinor) {
                    $this->minorPatientIds[] = $patient->id;
                }
            }
        }
    }

    // ─── Consultations & Dispensing ───────────────────────────────────────────

    private function seedConsultationsAndDispensing(): void
    {
        // School year: Aug 4, 2025 – June 8, 2026
        $schoolStart = Carbon::create(2025, 8, 4);
        $schoolEnd   = $this->today->copy();

        // Philippine holidays to skip (school closed)
        $holidays = [
            '2025-08-25', // National Heroes Day
            '2025-11-01', // All Saints Day
            '2025-11-02', // All Souls Day
            '2025-11-30', // Bonifacio Day
            '2025-12-08', // Immaculate Conception
            '2025-12-22', '2025-12-23', '2025-12-24',
            '2025-12-25', '2025-12-26', '2025-12-29',
            '2025-12-30', '2025-12-31',
            '2026-01-01', // New Year
            '2026-01-29', // Chinese New Year
            '2026-03-31', // Eidul Fitr (approx)
            '2026-04-02', '2026-04-03',  // Holy Week
            '2026-04-09', // Araw ng Kagitingan
            '2026-05-01', // Labor Day
            '2026-06-12', // Independence Day (future)
        ];

        // ── PASS 1: build all consultation rows + paired dispensing data ─────────
        // We collect EVERYTHING in memory first, then insert consultations all at
        // once, then insert dispensing records — guaranteeing FK satisfaction.
        $allConsultations = [];
        $allDispensing    = [];
        $invTransactions  = [];
        $consultationId   = 0;

        $current = $schoolStart->copy();
        while ($current->lte($schoolEnd)) {
            if ($current->isWeekend() || in_array($current->toDateString(), $holidays)) {
                $current->addDay();
                continue;
            }

            $volume = $this->dailyConsultationVolume($current);

            for ($v = 0; $v < $volume; $v++) {
                $nurseId   = $this->nurseIds[array_rand($this->nurseIds)];
                $patientId = $this->patientIds[array_rand($this->patientIds)];
                $complaint = $this->complaints[array_rand($this->complaints)];
                $visitTime = sprintf('%02d:%02d:00', rand(7, 16), rand(0, 59));
                $now       = $current->toDateTimeString();

                $consultationId++;
                $allConsultations[] = [
                    'id'              => $consultationId,
                    'patient_id'      => $patientId,
                    'appointment_id'  => null,
                    'nurse_id'        => $nurseId,
                    'visit_date'      => $current->toDateString(),
                    'visit_time'      => $visitTime,
                    'chief_complaint' => $complaint,
                    'assessment'      => $this->assessments[array_rand($this->assessments)],
                    'diagnosis'       => $this->diagnosisForComplaint($complaint),
                    'treatment'       => $this->treatments[array_rand($this->treatments)],
                    'notes'           => rand(0, 3) === 0 ? 'Patient advised to rest for the remainder of the day.' : null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                    'deleted_at'      => null,
                ];

                // ~70% of consultations dispense at least one medicine
                if (rand(1, 10) <= 7 && ! empty($this->medicineMap)) {
                    $medId  = array_rand($this->medicineMap);
                    $qty    = $this->dispensingQtyForMedicine($medId);
                    $before = $this->medicineMap[$medId];
                    $after  = max(0, $before - $qty);

                    $this->medicineMap[$medId] = $after;

                    $allDispensing[] = [
                        'patient_id'      => $patientId,
                        'consultation_id' => $consultationId,   // guaranteed to be inserted before this is written
                        'medicine_id'     => $medId,
                        'quantity'        => $qty,
                        'dispensed_by'    => $nurseId,
                        'dispensed_at'    => $current->toDateString() . ' ' . $visitTime,
                        'remarks'         => null,
                        'created_at'      => $now,
                        'updated_at'      => $now,
                    ];

                    $invTransactions[] = [
                        'medicine_id'      => $medId,
                        'transaction_type' => 'dispensed',
                        'quantity'         => -$qty,
                        'before_quantity'  => $before,
                        'after_quantity'   => $after,
                        'reference_id'     => null,
                        'reference_type'   => null,
                        'batch_number'     => null,
                        'expiration_date'  => null,
                        'supplier'         => null,
                        'notes'            => 'Dispensed to patient via consultation',
                        'performed_by'     => $nurseId,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                }
            }

            $current->addDay();
        }

        // ── PASS 2: write to DB in chunks ────────────────────────────────────
        foreach (array_chunk($allConsultations, 200) as $chunk) {
            DB::table('consultations')->insert($chunk);
        }
        foreach (array_chunk($allDispensing, 200) as $chunk) {
            DB::table('dispensing_records')->insert($chunk);
        }
        foreach (array_chunk($invTransactions, 200) as $chunk) {
            DB::table('inventory_transactions')->insert($chunk);
        }

        // ── PASS 3: update medicine quantities ───────────────────────────────
        foreach ($this->medicineMap as $medId => $currentQty) {
            Medicine::where('id', $medId)->update(['quantity' => $currentQty]);
        }
    }

    // ─── Appointments ─────────────────────────────────────────────────────────

    private function seedAppointments(): void
    {
        $adminId = User::role('administrator')->first()?->id ?? 1;

        // ── Past appointments (Aug 2025 – yesterday) ──────────────────────────
        $pastStart = Carbon::create(2025, 8, 4);
        $yesterday = $this->today->copy()->subDay();
        $current   = $pastStart->copy();

        $bulk = [];
        while ($current->lte($yesterday)) {
            if ($current->isWeekend()) {
                $current->addDay();
                continue;
            }

            // 3-7 appointments per school day
            $count = rand(3, 7);
            for ($i = 0; $i < $count; $i++) {
                $patientId = $this->patientIds[array_rand($this->patientIds)];
                $status    = $this->weightedStatus();
                $hour      = rand(8, 15);
                $minute    = rand(0, 1) ? '00' : '30';
                $time      = sprintf('%02d:%s:00', $hour, $minute);
                $purpose   = $this->purposes[array_rand($this->purposes)];

                $bulk[] = [
                    'patient_id'       => $patientId,
                    'appointment_date' => $current->toDateString(),
                    'appointment_time' => $time,
                    'purpose'          => $purpose,
                    'status'           => $status,
                    'approved_by'      => in_array($status, ['approved', 'completed']) ? $adminId : null,
                    'approved_at'      => in_array($status, ['approved', 'completed'])
                                            ? $current->copy()->subDay()->toDateTimeString()
                                            : null,
                    'cancelled_reason' => $status === 'cancelled'
                                            ? $this->cancelReason()
                                            : null,
                    'created_by'       => $adminId,
                    'created_at'       => $current->copy()->subDays(rand(2, 7))->toDateTimeString(),
                    'updated_at'       => $current->toDateTimeString(),
                    'deleted_at'       => null,
                ];
            }

            if (count($bulk) >= 200) {
                DB::table('appointments')->insert($bulk);
                $bulk = [];
            }

            $current->addDay();
        }

        // ── Upcoming appointments (today + next 14 days) ──────────────────────
        $future = $this->today->copy();
        for ($d = 0; $d <= 14; $d++) {
            if ($future->isWeekend()) {
                $future->addDay();
                continue;
            }

            $count = rand(4, 8);
            for ($i = 0; $i < $count; $i++) {
                $patientId = $this->patientIds[array_rand($this->patientIds)];
                $status    = $future->isToday()
                                ? (rand(0, 1) ? 'approved' : 'pending')
                                : 'pending';
                $hour      = rand(8, 15);
                $minute    = rand(0, 1) ? '00' : '30';
                $time      = sprintf('%02d:%s:00', $hour, $minute);

                $bulk[] = [
                    'patient_id'       => $patientId,
                    'appointment_date' => $future->toDateString(),
                    'appointment_time' => $time,
                    'purpose'          => $this->purposes[array_rand($this->purposes)],
                    'status'           => $status,
                    'approved_by'      => $status === 'approved' ? $adminId : null,
                    'approved_at'      => $status === 'approved' ? now()->toDateTimeString() : null,
                    'cancelled_reason' => null,
                    'created_by'       => $adminId,
                    'created_at'       => $this->today->copy()->subDays(rand(1, 5))->toDateTimeString(),
                    'updated_at'       => now()->toDateTimeString(),
                    'deleted_at'       => null,
                ];
            }

            $future->addDay();
        }

        if (! empty($bulk)) {
            DB::table('appointments')->insert($bulk);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /** Daily consultation volume with seasonal variation */
    private function dailyConsultationVolume(Carbon $date): int
    {
        $month = $date->month;

        // Rainy season surge (Aug–Oct) and cold season (Dec–Feb)
        $base = match (true) {
            in_array($month, [8, 9, 10])  => rand(18, 28),  // Rainy season, high illness
            in_array($month, [1, 2])      => rand(16, 24),  // Cold season
            in_array($month, [12])        => rand(8,  14),  // Christmas break (partial)
            in_array($month, [3, 4])      => rand(10, 18),  // Exam season
            in_array($month, [5, 6])      => rand(12, 20),  // End of year
            default                       => rand(12, 20),  // Normal
        };

        // Mondays tend to be higher (weekend catch-up)
        if ($date->isMonday()) {
            $base = (int) ($base * 1.2);
        }

        return $base;
    }

    private function diagnosisForComplaint(string $complaint): string
    {
        $map = [
            'Headache'       => 'Tension-type headache, unspecified',
            'Fever'          => 'Fever, unspecified (R50.9)',
            'Stomachache'    => 'Acute gastroenteritis',
            'Cough'          => 'Acute upper respiratory tract infection',
            'Dizziness'      => 'Dizziness and giddiness (R42)',
            'Nausea'         => 'Nausea and vomiting (R11)',
            'Wound'          => 'Open wound, abrasion',
            'Laceration'     => 'Laceration requiring closure',
            'Sore throat'    => 'Acute pharyngitis',
            'Abdominal pain' => 'Abdominal pain, unspecified',
            'Dysmenorrhea'   => 'Primary dysmenorrhea (N94.4)',
            'Allergic'       => 'Allergic urticaria (L50.0)',
            'Eye irritation' => 'Conjunctival irritation',
            'Low back pain'  => 'Low back pain, unspecified (M54.5)',
            'Toothache'      => 'Toothache, unspecified (K08.8)',
            'Epistaxis'      => 'Epistaxis (R04.0)',
            'Sprained'       => 'Sprain of ankle (S93.4)',
            'Insect bite'    => 'Insect bite, superficial',
            'Migraine'       => 'Migraine with aura (G43.1)',
            'LBM'            => 'Acute gastroenteritis with diarrhea',
            'Fainting'       => 'Syncope and collapse (R55)',
            'Hypertension'   => 'Essential hypertension (I10)',
            'Asthma'         => 'Asthma, mild intermittent (J45.2)',
            'Urinary tract'  => 'Urinary tract infection, unspecified',
            'Conjunctivitis' => 'Acute conjunctivitis',
            'Wound infection'=> 'Infected wound with cellulitis',
            'Blunt trauma'   => 'Contusion of lower leg',
            'Hyperventilation'=> 'Hyperventilation (R06.4)',
            'Heat exhaustion'=> 'Heat exhaustion, unspecified',
            'Finger laceration'=> 'Laceration of finger',
        ];

        foreach ($map as $keyword => $diagnosis) {
            if (stripos($complaint, $keyword) !== false) {
                return $diagnosis;
            }
        }

        return 'Other/unspecified condition';
    }

    private function dispensingQtyForMedicine(int $medicineId): int
    {
        $med = Medicine::find($medicineId);
        if (! $med) {
            return 1;
        }

        return match ($med->unit) {
            'tablets', 'capsules' => rand(1, 6),
            'bottles'             => 1,
            'sachets'             => rand(1, 3),
            'pieces'              => rand(1, 4),
            'boxes'               => 1,
            'tubes'               => 1,
            'inhalers'            => 1,
            default               => rand(1, 2),
        };
    }

    private function weightedStatus(): string
    {
        // Realistic distribution for past appointments
        $roll = rand(1, 100);
        return match (true) {
            $roll <= 55  => 'completed',
            $roll <= 75  => 'approved',
            $roll <= 85  => 'cancelled',
            $roll <= 92  => 'no_show',
            default      => 'pending',
        };
    }

    private function cancelReason(): string
    {
        $reasons = [
            'Patient did not arrive and no prior notice given.',
            'Patient called to reschedule due to class schedule conflict.',
            'Patient notified clinic of illness and rescheduled.',
            'No available slot on requested date.',
            'Patient transferred to hospital for further care.',
        ];
        return $reasons[array_rand($reasons)];
    }

    private function programsByCategory(string $category): array
    {
        return match ($category) {
            'college'     => ['BSIT', 'BSBA', 'BSCRIM', 'BSED', 'BEED', 'BSN', 'BSCS', 'BSHM'],
            'senior_high' => ['ABM', 'HUMSS', 'STEM', 'GAS', 'TVL-ICT', 'TVL-HE'],
            'junior_high' => ['JHS'],
            'elementary'  => ['Elementary'],
            'kinder'      => ['Kinder'],
            'daycare'     => ['Daycare'],
            default       => ['N/A'],
        };
    }

    private function yearLevelForCategory(string $category): string
    {
        return match ($category) {
            'college'     => 'Year ' . rand(1, 4),
            'senior_high' => 'Grade ' . rand(11, 12),
            'junior_high' => 'Grade ' . rand(7, 10),
            'elementary'  => 'Grade ' . rand(1, 6),
            'kinder'      => 'Kinder ' . rand(1, 2),
            'daycare'     => 'Nursery',
            default       => 'N/A',
        };
    }

    private function randomBloodType(): string
    {
        $types = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];
        $weights = [28, 2, 22, 2, 4, 1, 36, 3, 2]; // Approx Filipino blood type distribution
        $roll = rand(1, array_sum($weights));
        $cumulative = 0;
        foreach ($types as $i => $type) {
            $cumulative += $weights[$i];
            if ($roll <= $cumulative) {
                return $type;
            }
        }
        return 'O+';
    }

    private function randomAllergy(): string
    {
        $allergies = [
            'Penicillin',
            'Sulfa drugs',
            'Aspirin',
            'Ibuprofen',
            'Seafood (shrimp, crab)',
            'Peanuts',
            'Dust mites',
            'Pollen',
            'Latex',
            'Amoxicillin',
        ];
        return $allergies[array_rand($allergies)];
    }

    private function randomCondition(string $category): ?string
    {
        // Employees/teachers have higher rates of chronic conditions
        if (in_array($category, ['teacher', 'employee'])) {
            $conditions = [
                null, null,  // 2/7 chance of no condition
                'Hypertension — on maintenance Amlodipine 5mg',
                'Type 2 Diabetes Mellitus — on Metformin 500mg',
                'Bronchial Asthma — uses Salbutamol PRN',
                'Hyperthyroidism — on Propylthiouracil',
                'Rheumatoid Arthritis',
            ];
            return $conditions[array_rand($conditions)];
        }

        // Students have mostly no conditions or minor ones
        if (rand(1, 10) === 1) {
            $studentConditions = [
                'Bronchial Asthma, mild intermittent',
                'Allergic rhinitis',
                'Atopic dermatitis',
                'Iron deficiency anemia',
                'Congenital heart disease, stable',
            ];
            return $studentConditions[array_rand($studentConditions)];
        }

        return null;
    }

    private function randomAddress(): string
    {
        $streets  = ['Rizal St.', 'Mabini St.', 'Bonifacio Ave.', 'Quezon Blvd.', 'Aguinaldo St.', 'Luna St.'];
        $barangays = ['Brgy. San Jose', 'Brgy. Poblacion', 'Brgy. Santo Niño', 'Brgy. Maligaya', 'Brgy. Bagong Silang'];
        $cities    = ['Quezon City', 'Caloocan', 'Pasig', 'Marikina', 'Valenzuela', 'Antipolo'];

        return rand(1, 99) . ' ' . $streets[array_rand($streets)] . ', '
             . $barangays[array_rand($barangays)] . ', '
             . $cities[array_rand($cities)];
    }

    private function uniqueName(string $sex, array $used): string
    {
        $pool = $sex === 'male' ? $this->maleFirst : $this->femaleFirst;
        $attempts = 0;
        do {
            $name = $pool[array_rand($pool)];
            $attempts++;
        } while (in_array($name, $used) && $attempts < 100);
        return $name;
    }

    // ─── Patient Daily Logs ───────────────────────────────────────────────────

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
        $smsPending = [];   // collect guardian-SMS records in parallel
        $current    = $schoolStart->copy();

        while ($current->lte($schoolEnd)) {
            if ($current->isWeekend() || in_array($current->toDateString(), $holidays)) {
                $current->addDay();
                continue;
            }

            // 3-8 walk-in visits per school day
            $dailyCount = rand(3, 8);

            for ($i = 0; $i < $dailyCount; $i++) {
                $patientId   = $this->patientIds[array_rand($this->patientIds)];
                $nurseId     = $this->nurseIds[array_rand($this->nurseIds)];
                $complaint   = $this->complaints[array_rand($this->complaints)];
                $disposition = $this->randomDisposition();
                $isMinor     = in_array($patientId, $this->minorPatientIds);

                $timeInH  = rand(7, 15);
                $timeInM  = rand(0, 59);
                $timeIn   = sprintf('%02d:%02d:00', $timeInH, $timeInM);
                $duration = rand(10, 60); // minutes in clinic
                $timeOut  = rand(0, 4) > 0   // 80% have a time_out
                    ? sprintf('%02d:%02d:00',
                        (int)(($timeInH * 60 + $timeInM + $duration) / 60),
                        ($timeInM + $duration) % 60)
                    : null;

                // Guardian SMS for minors who were sent home or referred to hospital
                $smsGuardian = $isMinor
                    && in_array($disposition, ['sent_home', 'referred_to_hospital', 'further_observation'])
                    && rand(0, 3) > 0;   // ~75% of qualifying cases
                $smsSent = $smsGuardian && rand(0, 9) < 8;  // 80% actually sent

                $now = $current->copy()->setTime($timeInH, $timeInM)->toDateTimeString();

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
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                if ($smsGuardian) {
                    $smsPending[] = [
                        'patientId'   => $patientId,
                        'nurseId'     => $nurseId,
                        'disposition' => $disposition,
                        'complaint'   => $complaint,
                        'sent'        => $smsSent,
                        'date'        => $current->toDateString(),
                        'now'         => $now,
                    ];
                }
            }

            $current->addDay();
        }

        foreach (array_chunk($allLogs, 200) as $chunk) {
            DB::table('patient_logs')->insert($chunk);
        }

        // Store pending SMS records for seedSmsLogs() to use
        $this->smsPendingFromLogs = $smsPending;
    }

    // ─── SMS Notification Logs ─────────────────────────────────────────────────

    /** @var array Populated by seedPatientLogs() */
    private array $smsPendingFromLogs = [];

    private function seedSmsLogs(): void
    {
        $smsRows = [];

        // 1) Guardian notifications generated from patient logs
        foreach ($this->smsPendingFromLogs as $entry) {
            $patient = Patient::find($entry['patientId']);
            if (! $patient) {
                continue;
            }

            $guardianName   = $patient->guardian_name   ?? 'Parent/Guardian';
            $guardianNumber = $patient->guardian_contact ?? ('09' . rand(100000000, 999999999));
            $status         = $entry['sent'] ? 'sent' : 'failed';
            $sentAt         = $entry['sent']
                ? Carbon::parse($entry['now'])->addMinutes(rand(1, 10))->toDateTimeString()
                : null;

            $message = match ($entry['disposition']) {
                'sent_home' =>
                    "Dear {$guardianName}, your child/ward {$patient->first_name} {$patient->last_name} " .
                    "was seen at the school clinic today ({$entry['date']}) due to: {$entry['complaint']}. " .
                    "They have been sent home for rest. Please contact the clinic for more information. — Clinovia School Clinic",
                'referred_to_hospital' =>
                    "URGENT: {$guardianName}, your child/ward {$patient->first_name} {$patient->last_name} " .
                    "requires immediate medical attention and has been referred to a hospital. " .
                    "Please proceed to the nearest hospital immediately. — Clinovia School Clinic",
                default =>
                    "Dear {$guardianName}, {$patient->first_name} {$patient->last_name} " .
                    "is currently under observation at the school clinic ({$entry['date']}). " .
                    "Complaint: {$entry['complaint']}. We will notify you of updates. — Clinovia School Clinic",
            };

            $smsRows[] = [
                'recipient_number' => $guardianNumber,
                'recipient_name'   => $guardianName,
                'message'          => $message,
                'status'           => $status,
                'reference_id'     => $entry['patientId'],
                'reference_type'   => 'App\\Models\\Patient',
                'api_response'     => $entry['sent']
                    ? json_encode(['message_id' => 'MSG-' . strtoupper(substr(md5(rand()), 0, 10)), 'status' => 'queued'])
                    : null,
                'sent_at'          => $sentAt,
                'error_message'    => ! $entry['sent']
                    ? 'Network timeout. Message delivery failed.'
                    : null,
                'created_by'       => $entry['nurseId'],
                'created_at'       => $entry['now'],
                'updated_at'       => $entry['now'],
            ];
        }

        // 2) Appointment reminder SMS (independent batch — upcoming appointments)
        $adminId = User::role('administrator')->first()?->id ?? 1;
        $upcomingDates = [];
        for ($d = 1; $d <= 7; $d++) {
            $date = $this->today->copy()->addDays($d);
            if (! $date->isWeekend()) {
                $upcomingDates[] = $date->toDateString();
            }
        }

        foreach ($upcomingDates as $apptDate) {
            $reminderCount = rand(3, 6);
            for ($i = 0; $i < $reminderCount; $i++) {
                $patientId = $this->patientIds[array_rand($this->patientIds)];
                $patient   = Patient::find($patientId);
                if (! $patient) {
                    continue;
                }

                $number   = $patient->contact_number ?? ('09' . rand(100000000, 999999999));
                $name     = $patient->first_name . ' ' . $patient->last_name;
                $status   = rand(0, 9) < 9 ? 'sent' : 'failed';
                $sentTime = $this->today->copy()->subDays(rand(1, 2))
                    ->setTime(rand(8, 16), rand(0, 59))
                    ->toDateTimeString();

                $smsRows[] = [
                    'recipient_number' => $number,
                    'recipient_name'   => $name,
                    'message'          => "Dear {$name}, this is a reminder for your clinic appointment on " .
                                         Carbon::parse($apptDate)->format('F j, Y') .
                                         ". Please arrive on time. — Clinovia School Clinic",
                    'status'           => $status,
                    'reference_id'     => $patientId,
                    'reference_type'   => 'App\\Models\\Patient',
                    'api_response'     => $status === 'sent'
                        ? json_encode(['message_id' => 'MSG-' . strtoupper(substr(md5(rand()), 0, 10)), 'status' => 'delivered'])
                        : null,
                    'sent_at'          => $status === 'sent' ? $sentTime : null,
                    'error_message'    => $status === 'failed' ? 'Invalid recipient number.' : null,
                    'created_by'       => $adminId,
                    'created_at'       => $sentTime,
                    'updated_at'       => $sentTime,
                ];
            }
        }

        foreach (array_chunk($smsRows, 200) as $chunk) {
            DB::table('sms_logs')->insert($chunk);
        }
    }

    // ─── Vital Signs Generator ────────────────────────────────────────────────

    private function randomVitalSigns(): array
    {
        $tempRaw  = rand(360, 389);                    // 36.0 – 38.9 °C
        $temp     = ($tempRaw / 10);
        $systolic = rand(90, 145);
        $diastolic = max(60, $systolic - rand(30, 55));
        $pulse    = rand(58, 102);
        $weight   = rand(22, 88);                      // kg
        $height   = rand(108, 178);                    // cm
        $spo2     = rand(94, 100);                     // %

        return [
            'temperature'    => number_format($temp, 1) . '°C',
            'blood_pressure' => "{$systolic}/{$diastolic} mmHg",
            'pulse_rate'     => "{$pulse} bpm",
            'weight'         => "{$weight} kg",
            'height'         => "{$height} cm",
            'oxygen_sat'     => "{$spo2}%",
        ];
    }

    // ─── Disposition Picker (weighted) ────────────────────────────────────────

    private function randomDisposition(): string
    {
        // Realistic distribution for a school clinic
        $roll = rand(1, 100);
        return match (true) {
            $roll <= 52 => 'returned_to_class',   // 52% — most common
            $roll <= 77 => 'rest_in_clinic',       // 25%
            $roll <= 91 => 'sent_home',            // 14%
            $roll <= 98 => 'further_observation',  //  7%
            default     => 'referred_to_hospital', //  2%
        };
    }
}

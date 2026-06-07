<?php

namespace Database\Seeders;

use App\Models\MedicineCategory;
use Illuminate\Database\Seeder;

class MedicineCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tablet',     'description' => 'Solid oral dosage form'],
            ['name' => 'Capsule',    'description' => 'Enclosed gelatin shell with medicine'],
            ['name' => 'Syrup',      'description' => 'Liquid oral preparation'],
            ['name' => 'Ointment',   'description' => 'Topical semi-solid preparation'],
            ['name' => 'Drops',      'description' => 'Liquid for eyes, ears, or nose'],
            ['name' => 'Injection',  'description' => 'Parenteral solution for injection'],
            ['name' => 'Powder',     'description' => 'Dry powder form'],
            ['name' => 'Patch',      'description' => 'Transdermal delivery system'],
            ['name' => 'Inhaler',    'description' => 'Respiratory delivery device'],
            ['name' => 'Suppository','description' => 'Rectal or vaginal delivery form'],
            ['name' => 'Other',      'description' => 'Other forms not listed above'],
        ];

        foreach ($categories as $category) {
            MedicineCategory::firstOrCreate(['name' => $category['name']], $category);
        }

        $this->command->info('Medicine categories seeded successfully.');
    }
}

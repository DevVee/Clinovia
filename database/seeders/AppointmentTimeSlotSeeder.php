<?php

namespace Database\Seeders;

use App\Models\AppointmentTimeSlot;
use Illuminate\Database\Seeder;

class AppointmentTimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 30-minute slots from 7:00 AM to 5:00 PM
        $start = strtotime('07:00');
        $end   = strtotime('17:00');

        for ($time = $start; $time <= $end; $time += 1800) {
            AppointmentTimeSlot::firstOrCreate(
                ['slot_time' => date('H:i:s', $time)],
                ['max_appointments' => 5, 'is_active' => true]
            );
        }

        $this->command->info('Appointment time slots seeded (07:00 - 17:00, every 30 min).');
    }
}

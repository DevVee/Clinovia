<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\DispensingRecord;
use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Support\Collection;

class ReportService
{
    // ─── Daily ───────────────────────────────────────────────────────────────

    public function dailyReport(string $date): array
    {
        $consultations = Consultation::with('patient', 'nurse')
            ->whereDate('visit_date', $date)
            ->orderBy('visit_time')
            ->get();

        $appointments = Appointment::with('patient')
            ->whereDate('appointment_date', $date)
            ->orderBy('appointment_time')
            ->get();

        $dispensed = DispensingRecord::with('patient', 'medicine')
            ->whereDate('dispensed_at', $date)
            ->get();

        return compact('consultations', 'appointments', 'dispensed', 'date');
    }

    // ─── Monthly ─────────────────────────────────────────────────────────────

    public function monthlyReport(int $year, int $month): array
    {
        $consultationsByDay = Consultation::selectRaw('DAY(visit_date) as day, COUNT(*) as total')
            ->whereYear('visit_date', $year)->whereMonth('visit_date', $month)
            ->groupBy('day')->orderBy('day')
            ->pluck('total', 'day');

        $byCategory = Patient::selectRaw('category, COUNT(DISTINCT consultations.patient_id) as total')
            ->join('consultations', 'patients.id', '=', 'consultations.patient_id')
            ->whereYear('consultations.visit_date', $year)
            ->whereMonth('consultations.visit_date', $month)
            ->groupBy('category')
            ->get();

        $totalConsultations = Consultation::whereYear('visit_date', $year)->whereMonth('visit_date', $month)->count();
        $totalPatients      = Consultation::whereYear('visit_date', $year)->whereMonth('visit_date', $month)->distinct('patient_id')->count();
        $totalAppointments  = Appointment::whereYear('appointment_date', $year)->whereMonth('appointment_date', $month)->count();

        return compact('year', 'month', 'consultationsByDay', 'byCategory', 'totalConsultations', 'totalPatients', 'totalAppointments');
    }

    // ─── Annual ──────────────────────────────────────────────────────────────

    public function annualReport(int $year): array
    {
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month'         => \Carbon\Carbon::create($year, $m, 1)->format('M'),
                'consultations' => Consultation::whereYear('visit_date', $year)->whereMonth('visit_date', $m)->count(),
                'appointments'  => Appointment::whereYear('appointment_date', $year)->whereMonth('appointment_date', $m)->count(),
            ];
        }

        $totalConsultations = array_sum(array_column($monthlyData, 'consultations'));
        $totalAppointments  = array_sum(array_column($monthlyData, 'appointments'));
        $totalPatients      = Consultation::whereYear('visit_date', $year)->distinct('patient_id')->count();

        return compact('year', 'monthlyData', 'totalConsultations', 'totalAppointments', 'totalPatients');
    }

    // ─── Medicine Usage ───────────────────────────────────────────────────────

    public function medicineUsageReport(string $from, string $to): array
    {
        $usage = DispensingRecord::selectRaw(
                'medicine_id, SUM(quantity) as total_dispensed, COUNT(*) as times_dispensed'
            )
            ->with('medicine.category')
            ->whereBetween('dispensed_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupBy('medicine_id')
            ->orderByDesc('total_dispensed')
            ->get();

        $totalDispensed = $usage->sum('total_dispensed');

        return compact('usage', 'totalDispensed', 'from', 'to');
    }

    // ─── Inventory Snapshot ───────────────────────────────────────────────────

    public function inventorySnapshot(): array
    {
        $medicines  = Medicine::with('category')->active()->orderBy('name')->get();
        $lowStock   = $medicines->filter(fn ($m) => $m->is_low_stock && $m->quantity > 0)->count();
        $outOfStock = $medicines->filter(fn ($m) => $m->quantity === 0)->count();
        $expiring   = $medicines->filter(fn ($m) => $m->is_expiring_soon)->count();
        $expired    = $medicines->filter(fn ($m) => $m->is_expired)->count();

        return compact('medicines', 'lowStock', 'outOfStock', 'expiring', 'expired');
    }

    // ─── Appointments Summary ─────────────────────────────────────────────────

    public function appointmentsReport(string $from, string $to): array
    {
        $appointments = Appointment::with('patient')
            ->whereBetween('appointment_date', [$from, $to])
            ->orderBy('appointment_date')
            ->get();

        $byStatus = $appointments->groupBy('status')->map->count();

        return compact('appointments', 'byStatus', 'from', 'to');
    }
}

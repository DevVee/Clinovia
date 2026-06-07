<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientLog;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients'        => Patient::active()->count(),
            // Clinic log (patient visits) — main metric
            'visits_today'          => PatientLog::today()->count(),
            'visits_month'          => PatientLog::whereYear('log_date', now()->year)
                                                  ->whereMonth('log_date', now()->month)
                                                  ->count(),
            'consultations_today'   => Consultation::today()->count(),
            'consultations_month'   => Consultation::whereYear('visit_date', now()->year)
                                                    ->whereMonth('visit_date', now()->month)
                                                    ->count(),
            'appointments_today'    => Appointment::today()->count(),
            'appointments_pending'  => Appointment::where('status', 'pending')->count(),
            'total_medicines'       => Medicine::active()->count(),
            'low_stock_medicines'   => Medicine::active()->lowStock()->count(),
            'expiring_medicines'    => Medicine::active()->expiringSoon(30)->count(),
        ];

        // Today's clinic log entries (recent)
        $todayClinicLog = PatientLog::with('patient')
            ->today()
            ->orderByDesc('time_in')
            ->limit(6)
            ->get();

        $todayAppointments = Appointment::with('patient')
            ->today()
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('appointment_time')
            ->limit(5)
            ->get();

        $lowStockMedicines = Medicine::with('category')
            ->active()->lowStock()
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        $expiringMedicines = Medicine::with('category')
            ->active()->expiringSoon(30)
            ->orderBy('expiration_date')
            ->limit(5)
            ->get();

        // DASHBOARD FIX: Audit log activity is sensitive — only show to admins.
        // Non-admin users see an empty collection so the widget is hidden in the view.
        $recentActivity = auth()->user()?->isAdmin()
            ? AuditLog::with('user')->latest()->limit(8)->get()
            : collect();

        // Monthly visits chart — last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'count' => Consultation::whereYear('visit_date', $month->year)
                                       ->whereMonth('visit_date', $month->month)
                                       ->count(),
            ];
        }

        return view('dashboard.index', compact(
            'stats', 'todayAppointments', 'todayClinicLog',
            'lowStockMedicines', 'expiringMedicines',
            'recentActivity', 'monthlyData'
        ));
    }
}

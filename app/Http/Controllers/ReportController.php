<?php

namespace App\Http\Controllers;

use App\Models\PatientLog;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $service) {}

    /* ------------------------------------------------------------------ */
    /*  MENU                                                                */
    /* ------------------------------------------------------------------ */
    public function index()
    {
        $this->authorize('view-reports');

        $stats = [
            'visits_today'   => PatientLog::today()->count(),
            'visits_month'   => PatientLog::whereMonth('log_date', now()->month)
                                          ->whereYear('log_date', now()->year)->count(),
            'visits_year'    => PatientLog::whereYear('log_date', now()->year)->count(),
        ];

        return view('reports.index', compact('stats'));
    }

    /* ------------------------------------------------------------------ */
    /*  DAILY                                                               */
    /* ------------------------------------------------------------------ */
    public function daily(Request $request)
    {
        $this->authorize('view-reports');
        $date = $request->get('date', today()->toDateString());
        $data = $this->service->dailyReport($date);
        return view('reports.daily', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  MONTHLY                                                             */
    /* ------------------------------------------------------------------ */
    public function monthly(Request $request)
    {
        $this->authorize('view-reports');
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);
        $data  = $this->service->monthlyReport($year, $month);
        return view('reports.monthly', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  ANNUAL                                                              */
    /* ------------------------------------------------------------------ */
    public function annual(Request $request)
    {
        $this->authorize('view-reports');
        $year = (int) $request->get('year', now()->year);
        $data = $this->service->annualReport($year);
        return view('reports.annual', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  MEDICINE USAGE                                                      */
    /* ------------------------------------------------------------------ */
    public function medicineUsage(Request $request)
    {
        $this->authorize('view-reports');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $data = $this->service->medicineUsageReport($from, $to);
        return view('reports.medicine-usage', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  INVENTORY SNAPSHOT                                                  */
    /* ------------------------------------------------------------------ */
    public function inventory()
    {
        $this->authorize('view-reports');
        $data = $this->service->inventorySnapshot();
        return view('reports.inventory', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  APPOINTMENTS                                                        */
    /* ------------------------------------------------------------------ */
    public function appointments(Request $request)
    {
        $this->authorize('view-reports');
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $data = $this->service->appointmentsReport($from, $to);
        return view('reports.appointments', $data);
    }

    /* ------------------------------------------------------------------ */
    /*  EXPORT — PDF or CSV                                                 */
    /* ------------------------------------------------------------------ */
    public function export(Request $request, string $type)
    {
        $this->authorize('export-reports');

        $format = $request->get('format', 'pdf');

        return match($type) {
            'daily'          => $this->exportDaily($request, $format),
            'monthly'        => $this->exportMonthly($request, $format),
            'annual'         => $this->exportAnnual($request, $format),
            'medicine-usage' => $this->exportMedicineUsage($request, $format),
            'inventory'      => $this->exportInventory($format),
            'appointments'   => $this->exportAppointments($request, $format),
            default          => back()->with('error', 'Unknown report type.'),
        };
    }

    /* ── Private export helpers ──────────────────────────────────────── */

    private function exportDaily(Request $request, string $format)
    {
        $date = $request->get('date', today()->toDateString());
        $data = $this->service->dailyReport($date);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.daily', $data)->setPaper('a4', 'portrait');
            return $pdf->download("daily-report-{$date}.pdf");
        }

        // CSV
        $rows[] = ['Patient', 'Chief Complaint', 'Visit Time', 'Nurse'];
        foreach ($data['consultations'] as $c) {
            $rows[] = [
                $c->patient->full_name ?? '',
                $c->chief_complaint ?? '',
                $c->visit_time ?? '',
                $c->nurse->name ?? '',
            ];
        }
        return $this->csvDownload("daily-report-{$date}.csv", $rows);
    }

    private function exportMonthly(Request $request, string $format)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);
        $data  = $this->service->monthlyReport($year, $month);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.monthly', $data)->setPaper('a4', 'portrait');
            return $pdf->download("monthly-report-{$year}-{$month}.pdf");
        }

        $rows[] = ['Day', 'Consultations'];
        foreach ($data['consultationsByDay'] as $day => $total) {
            $rows[] = [$day, $total];
        }
        return $this->csvDownload("monthly-report-{$year}-{$month}.csv", $rows);
    }

    private function exportAnnual(Request $request, string $format)
    {
        $year = (int) $request->get('year', now()->year);
        $data = $this->service->annualReport($year);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.annual', $data)->setPaper('a4', 'landscape');
            return $pdf->download("annual-report-{$year}.pdf");
        }

        $rows[] = ['Month', 'Consultations', 'Appointments'];
        foreach ($data['monthlyData'] as $row) {
            $rows[] = [$row['month'], $row['consultations'], $row['appointments']];
        }
        return $this->csvDownload("annual-report-{$year}.csv", $rows);
    }

    private function exportMedicineUsage(Request $request, string $format)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $data = $this->service->medicineUsageReport($from, $to);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.medicine-usage', $data)->setPaper('a4', 'portrait');
            return $pdf->download("medicine-usage-{$from}-{$to}.pdf");
        }

        $rows[] = ['Medicine', 'Category', 'Times Dispensed', 'Total Qty'];
        foreach ($data['usage'] as $u) {
            $rows[] = [
                $u->medicine->name ?? '',
                $u->medicine->category->name ?? '',
                $u->times_dispensed,
                $u->total_dispensed,
            ];
        }
        return $this->csvDownload("medicine-usage-{$from}-{$to}.csv", $rows);
    }

    private function exportInventory(string $format)
    {
        $data = $this->service->inventorySnapshot();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.inventory', $data)->setPaper('a4', 'portrait');
            return $pdf->download('inventory-snapshot-' . now()->format('Y-m-d') . '.pdf');
        }

        $rows[] = ['Medicine', 'Category', 'Quantity', 'Unit', 'Threshold', 'Expiry Date', 'Status'];
        foreach ($data['medicines'] as $m) {
            $status = $m->quantity === 0 ? 'Out of Stock' : ($m->is_low_stock ? 'Low Stock' : 'In Stock');
            $rows[] = [
                $m->name,
                $m->category->name ?? '',
                $m->quantity,
                $m->unit,
                $m->low_stock_threshold,
                $m->expiration_date ? $m->expiration_date->format('Y-m-d') : '',
                $status,
            ];
        }
        return $this->csvDownload('inventory-snapshot-' . now()->format('Y-m-d') . '.csv', $rows);
    }

    private function exportAppointments(Request $request, string $format)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $data = $this->service->appointmentsReport($from, $to);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.appointments', $data)->setPaper('a4', 'portrait');
            return $pdf->download("appointments-{$from}-{$to}.pdf");
        }

        $rows[] = ['Date', 'Patient', 'Purpose', 'Status'];
        foreach ($data['appointments'] as $a) {
            $rows[] = [
                $a->appointment_date->format('Y-m-d'),
                $a->patient->full_name ?? '',
                $a->purpose ?? '',
                $a->status,
            ];
        }
        return $this->csvDownload("appointments-{$from}-{$to}.csv", $rows);
    }

    /**
     * Stream a CSV response using fputcsv.
     */
    private function csvDownload(string $filename, array $rows)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

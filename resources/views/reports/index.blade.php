@extends('layouts.app')

@section('title', 'Reports')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Reports</h4>
    <p class="text-muted small mb-0">Generate, view, and export clinic reports</p>
</div>

{{-- ── Report Cards ────────────────────────────────────────────────────────── --}}
<div class="row g-4">
    @php
    $reports = [
        [
            'route' => 'patient-logs.index',
            'icon'  => 'bi-journal-medical',
            'color' => 'danger',
            'title' => 'Clinic Log',
            'desc'  => 'Daily patient visit logbook — who came in, complaint, treatment, disposition',
            'badge' => $stats['visits_today'] > 0 ? $stats['visits_today'] . ' today' : null,
        ],
        [
            'route' => 'reports.daily',
            'icon'  => 'bi-calendar-day',
            'color' => 'primary',
            'title' => 'Daily Report',
            'desc'  => 'Consultations, appointments, and dispensing for a specific day',
            'badge' => null,
        ],
        [
            'route' => 'reports.monthly',
            'icon'  => 'bi-calendar-month',
            'color' => 'success',
            'title' => 'Monthly Report',
            'desc'  => 'Month-by-month visit breakdown with category analysis',
            'badge' => null,
        ],
        [
            'route' => 'reports.annual',
            'icon'  => 'bi-bar-chart-line',
            'color' => 'info',
            'title' => 'Annual Report',
            'desc'  => 'Year-long trend of consultations and appointments',
            'badge' => null,
        ],
        [
            'route' => 'reports.medicine-usage',
            'icon'  => 'bi-capsule',
            'color' => 'warning',
            'title' => 'Medicine Usage',
            'desc'  => 'Top dispensed medicines for a date range',
            'badge' => null,
        ],
        [
            'route' => 'reports.inventory',
            'icon'  => 'bi-box-seam',
            'color' => 'secondary',
            'title' => 'Inventory Snapshot',
            'desc'  => 'Current stock levels, low stock, and expiring medicines',
            'badge' => null,
        ],
        [
            'route' => 'reports.appointments',
            'icon'  => 'bi-calendar-check',
            'color' => 'purple',
            'title' => 'Appointments Report',
            'desc'  => 'Appointment status breakdown for a date range',
            'badge' => null,
        ],
    ];
    @endphp

    @foreach($reports as $r)
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route($r['route']) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 report-card">
                <div class="card-body p-4 d-flex align-items-start gap-3">
                    <div class="report-icon bg-{{ $r['color'] }}-subtle text-{{ $r['color'] }} rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 position-relative">
                        <i class="bi {{ $r['icon'] }} fs-4"></i>
                        @if($r['badge'])
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem;">
                            {{ $r['badge'] }}
                        </span>
                        @endif
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-body">{{ $r['title'] }}</h6>
                        <p class="text-muted small mb-0">{{ $r['desc'] }}</p>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-4">
                    <span class="btn btn-{{ $r['color'] }} btn-sm">
                        <i class="bi bi-arrow-right me-1"></i>Generate
                    </span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

@push('styles')
<style>
.report-card { transition: transform .18s, box-shadow .18s; }
.report-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.10) !important; }
.report-icon { width: 52px; height: 52px; }

.bg-purple-subtle { background-color: #f3e8ff; }
.text-purple       { color: #7c3aed; }
.btn-purple        { background-color: #7c3aed; border-color: #7c3aed; color: #fff; }
.btn-purple:hover  { background-color: #6d28d9; border-color: #6d28d9; color: #fff; }
</style>
@endpush
@endsection

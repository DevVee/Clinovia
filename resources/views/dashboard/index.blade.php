@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ─── Welcome Banner ──────────────────────────────────────────────────────── --}}
<div class="welcome-banner">
    <div class="welcome-left">
        <h2 class="welcome-greeting">
            <span id="dashGreeting">Good Morning</span>,
            {{ explode(' ', auth()->user()->name)[0] }}!
        </h2>
        <p class="welcome-subtitle">
            Here's what's happening at the clinic today.
        </p>
    </div>
    <div class="welcome-date-badge">
        <i class="bi bi-calendar3"></i>
        {{ now()->format('l, F d, Y') }}
    </div>
</div>

{{-- ─── Stat Cards — 8 cards / 4×2 ───────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- 1. Clinic Visits Today --}}
    <div class="col-sm-6 col-xl-3" style="cursor:pointer;"
         onclick="window.location.href='{{ route('patient-logs.index') }}'">
        <div class="card stat-card stat-visits h-100">
            <div class="card-body">
                <div class="stat-icon icon-visits">
                    <i class="bi bi-journal-medical"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Clinic Visits Today</div>
                    <div class="stat-value">{{ $stats['visits_today'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-calendar3"></i> {{ $stats['visits_month'] }} this month
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Total Patients --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-patients h-100">
            <div class="card-body">
                <div class="stat-icon icon-patients">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-value">{{ number_format($stats['total_patients']) }}</div>
                    <div class="stat-trend trend-up">
                        <i class="bi bi-person-check-fill"></i> registered
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Consultations Today --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-consults-today h-100">
            <div class="card-body">
                <div class="stat-icon icon-consults-today">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Consultations Today</div>
                    <div class="stat-value">{{ $stats['consultations_today'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-activity"></i> clinic visits
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Consultations This Month --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-consults-month h-100">
            <div class="card-body">
                <div class="stat-icon icon-consults-month">
                    <i class="bi bi-clipboard-check-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Consultations This Month</div>
                    <div class="stat-value">{{ $stats['consultations_month'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-calendar3"></i> {{ now()->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Appointments Today --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-appointments h-100">
            <div class="card-body">
                <div class="stat-icon icon-appointments">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Appointments Today</div>
                    <div class="stat-value">{{ $stats['appointments_today'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-calendar-day"></i> scheduled
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. Active Medicines --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-medicines h-100">
            <div class="card-body">
                <div class="stat-icon icon-medicines">
                    <i class="bi bi-capsule-pill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Active Medicines</div>
                    <div class="stat-value">{{ $stats['total_medicines'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-box-seam-fill"></i> in inventory
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 7. Low Stock --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-low-stock h-100">
            <div class="card-body">
                <div class="stat-icon icon-low-stock">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Low Stock</div>
                    <div class="stat-value">{{ $stats['low_stock_medicines'] }}</div>
                    @if($stats['low_stock_medicines'] > 0)
                        <div class="stat-trend trend-down">
                            <i class="bi bi-arrow-down-circle-fill"></i> need restock
                        </div>
                    @else
                        <div class="stat-trend trend-up">
                            <i class="bi bi-shield-check-fill"></i> stock is good
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 8. Expiring Soon --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card stat-expiring h-100">
            <div class="card-body">
                <div class="stat-icon icon-expiring">
                    <i class="bi bi-calendar-x-fill"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Expiring Soon</div>
                    <div class="stat-value">{{ $stats['expiring_medicines'] }}</div>
                    @if($stats['expiring_medicines'] > 0)
                        <div class="stat-trend trend-down">
                            <i class="bi bi-clock-history"></i> within 30 days
                        </div>
                    @else
                        <div class="stat-trend trend-up">
                            <i class="bi bi-check-circle-fill"></i> none expiring
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>{{-- /.row stat cards --}}

{{-- ─── Today's Clinic Log (below cards, clean table) ─────────────────────── --}}
@can('view-consultations')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-journal-medical text-primary fs-5"></i>
            <span class="fw-semibold">Today's Clinic Log</span>
            <span class="badge bg-primary rounded-pill">{{ $stats['visits_today'] }}</span>
        </div>
        <div class="d-flex gap-2">
            @can('create-consultations')
            <a href="{{ route('patient-logs.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Log a Visit
            </a>
            @endcan
            <a href="{{ route('patient-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                View All
            </a>
        </div>
    </div>

    @if($todayClinicLog->isEmpty())
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-journal-x d-block mb-2 opacity-25" style="font-size:2.5rem;"></i>
        <p class="mb-0 fw-semibold">No patient visits logged today.</p>
        <p class="small mb-0">Use <strong>Log a Visit</strong> to record when a patient comes to the clinic.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Time In</th>
                    <th>Patient</th>
                    <th>Complaint</th>
                    <th>Treatment</th>
                    <th class="text-center">Disposition</th>
                    <th class="text-center">SMS</th>
                    <th class="pe-4 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($todayClinicLog as $log)
            <tr>
                <td class="ps-4 fw-semibold text-nowrap">
                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                </td>
                <td>
                    <div class="fw-semibold">{{ $log->patient->full_name }}</div>
                    <div class="text-muted" style="font-size:.73rem;">
                        {{ $log->patient->patient_number }}
                        @if($log->patient->section) · {{ $log->patient->section }} @endif
                    </div>
                </td>
                <td style="max-width:180px;">{{ Str::limit($log->chief_complaint, 50) }}</td>
                <td class="text-muted" style="max-width:160px;">
                    {{ $log->treatment ? Str::limit($log->treatment, 45) : '—' }}
                </td>
                <td class="text-center">
                    <span class="badge bg-{{ $log->disposition_color }}-subtle text-{{ $log->disposition_color }}-emphasis border border-{{ $log->disposition_color }}-subtle">
                        {{ $log->disposition_label }}
                    </span>
                </td>
                <td class="text-center">
                    @if($log->sms_guardian && $log->sms_sent)
                        <i class="bi bi-check2-circle text-success" title="SMS sent"></i>
                    @elseif($log->sms_guardian)
                        <i class="bi bi-x-circle text-danger" title="SMS failed"></i>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td class="pe-4 text-end">
                    <a href="{{ route('patient-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endcan

{{-- ─── Main Content Row ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Monthly Visits Chart --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Monthly Patient Visits</span>
                <small class="text-muted">Last 6 months</small>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="250"></canvas>
            </div>
        </div>
    </div>

    {{-- Today's Appointments --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-calendar-event me-2 text-success"></i>Today's Appointments</span>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($todayAppointments as $appt)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <div class="me-3 text-center" style="min-width:50px;">
                            <div class="fw-bold text-primary" style="font-size:.85rem;">{{ date('h:i', strtotime($appt->appointment_time)) }}</div>
                            <div class="text-muted" style="font-size:.65rem;">{{ date('A', strtotime($appt->appointment_time)) }}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.85rem;">{{ $appt->patient->full_name ?? '—' }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $appt->purpose }}</div>
                        </div>
                        <span class="badge bg-{{ $appt->status_badge }}">{{ ucfirst($appt->status) }}</span>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x d-block mb-2 opacity-25" style="font-size:2rem;"></i>
                        <small>No appointments for today</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ─── Alerts Row ───────────────────────────────────────────────────────────── --}}
<div class="row g-3">

    {{-- Low Stock Medicines --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Medicines</span>
                @can('view-medicines')
                <a href="{{ route('medicines.low-stock') }}" class="btn btn-sm btn-outline-warning">View All</a>
                @endcan
            </div>
            <div class="card-body p-0">
                @forelse($lowStockMedicines as $med)
                    <div class="d-flex align-items-center px-3 py-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:.85rem;">{{ $med->name }}</div>
                            <div class="text-muted" style="font-size:.75rem;">{{ $med->category->name ?? '—' }}</div>
                        </div>
                        <span class="badge bg-{{ $med->quantity == 0 ? 'danger' : 'warning text-dark' }}">
                            {{ $med->quantity }} {{ $med->unit }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle d-block mb-2 text-success opacity-50" style="font-size:2rem;"></i>
                        <small>All medicines are well-stocked</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <span class="fw-semibold"><i class="bi bi-activity me-2 text-info"></i>Recent Activity</span>
                @role('administrator')
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">Audit Log</a>
                @endrole
            </div>
            <div class="card-body p-0">
                @forelse($recentActivity as $log)
                    <div class="d-flex align-items-start px-3 py-2 border-bottom gap-2">
                        <span class="badge bg-{{ $log->action_badge }} mt-1" style="min-width:60px;">{{ $log->action }}</span>
                        <div class="flex-grow-1">
                            <div style="font-size:.8rem;">{{ $log->description }}</div>
                            <div class="text-muted" style="font-size:.7rem;">
                                {{ $log->user_name }} &bull; {{ $log->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted"><small>No recent activity</small></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('monthlyChart');
    if (!ctx) return;

    const labels = {!! json_encode(collect($monthlyData)->pluck('month')) !!};
    const data   = {!! json_encode(collect($monthlyData)->pluck('count')) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Patient Visits',
                data,
                backgroundColor: 'rgba(13,110,253,0.12)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (c) => ` ${c.raw} visits` } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f0f0f0' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush

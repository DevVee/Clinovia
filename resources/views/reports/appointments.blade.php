@extends('layouts.app')

@section('title', 'Appointments Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Appointments Report</h4>
        <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'appointments') }}?from={{ $from }}&to={{ $to }}&format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'appointments') }}?from={{ $from }}&to={{ $to }}&format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Date range --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.appointments') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-2"><button class="btn btn-primary btn-sm mt-3">Generate</button></div>
        </form>
    </div>
</div>

{{-- Status Summary --}}
<div class="row g-3 mb-4">
    @php
    $statusConfig = [
        'pending'   => ['warning', 'Clock'],
        'approved'  => ['success', 'Check Circle'],
        'completed' => ['primary', 'Check All'],
        'cancelled' => ['danger',  'X Circle'],
        'no_show'   => ['secondary','Slash Circle'],
    ];
    @endphp
    @foreach($statusConfig as $status => [$color, $label])
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-{{ $color }}">{{ $byStatus[$status] ?? 0 }}</div>
            <div class="text-muted small">{{ ucfirst($status) }}</div>
        </div>
    </div>
    @endforeach
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center py-3 border-primary">
            <div class="fs-3 fw-bold">{{ $appointments->count() }}</div>
            <div class="text-muted small">Total</div>
        </div>
    </div>
</div>

{{-- Appointments Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-calendar-check me-2"></i> Appointment List
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Purpose</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $a)
                    @php
                        $badge = match($a->status) {
                            'pending'   => 'warning',
                            'approved'  => 'success',
                            'completed' => 'primary',
                            'cancelled' => 'danger',
                            'no_show'   => 'secondary',
                            default     => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td>{{ $a->appointment_date->format('M d, Y') }}</td>
                        <td class="text-muted">{{ $a->appointment_time ? \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') : '—' }}</td>
                        <td class="fw-semibold">{{ $a->patient->full_name ?? '—' }}</td>
                        <td>{{ Str::limit($a->purpose, 50) }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}-emphasis">
                                {{ ucfirst(str_replace('_', ' ', $a->status)) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No appointments in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

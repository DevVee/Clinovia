@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Daily Report</h4>
        <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'daily') }}?date={{ $date }}&format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'daily') }}?date={{ $date }}&format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Date Picker --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.daily') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">Select Date</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary btn-sm">Generate</button>
            </div>
        </form>
    </div>
</div>

{{-- Summary Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-primary">{{ $consultations->count() }}</div>
            <div class="text-muted small">Consultations</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-success">{{ $appointments->count() }}</div>
            <div class="text-muted small">Appointments</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-warning">{{ $dispensed->count() }}</div>
            <div class="text-muted small">Medicines Dispensed</div>
        </div>
    </div>
</div>

{{-- Consultations --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-journal-medical text-primary me-2"></i> Consultations
        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $consultations->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Time</th><th>Patient</th><th>Chief Complaint</th><th>Nurse</th></tr>
                </thead>
                <tbody>
                    @forelse($consultations as $c)
                    <tr>
                        <td class="text-muted small">{{ $c->visit_time ? \Carbon\Carbon::parse($c->visit_time)->format('h:i A') : '—' }}</td>
                        <td class="fw-semibold">{{ $c->patient->full_name ?? '—' }}</td>
                        <td>{{ Str::limit($c->chief_complaint, 60) }}</td>
                        <td>{{ $c->nurse->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No consultations on this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Dispensed Medicines --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-capsule text-warning me-2"></i> Medicines Dispensed
        <span class="badge bg-warning-subtle text-warning-emphasis ms-1">{{ $dispensed->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Patient</th><th>Medicine</th><th class="text-center">Qty</th><th>By</th></tr>
                </thead>
                <tbody>
                    @forelse($dispensed as $d)
                    <tr>
                        <td class="fw-semibold">{{ $d->patient->full_name ?? '—' }}</td>
                        <td>{{ $d->medicine->name ?? '—' }}</td>
                        <td class="text-center">{{ $d->quantity }}</td>
                        <td>{{ $d->dispensedBy->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No medicines dispensed on this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

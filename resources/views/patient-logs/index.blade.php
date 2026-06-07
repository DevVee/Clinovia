@extends('layouts.app')

@section('title', 'Clinic Logbook')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-journal-medical me-2 text-primary"></i>Clinic Logbook
        </h4>
        <p class="text-muted small mb-0">Daily patient visit log &mdash; who came in, why, and what was done</p>
    </div>
    @can('create-consultations')
    <a href="{{ route('patient-logs.create') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
        <i class="bi bi-plus-circle-fill me-2"></i>Log a Patient Visit
    </a>
    @endcan
</div>

{{-- ── Stats Cards ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Today --}}
    <div class="col-sm-4">
        <div class="card stat-card stat-visits h-100">
            <div class="card-body">
                <div class="stat-icon icon-visits">
                    <i class="bi bi-journal-medical"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Today's Visits</div>
                    <div class="stat-value">{{ $stats['today'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-calendar3"></i> {{ now()->format('F d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- This Week --}}
    <div class="col-sm-4">
        <div class="card stat-card stat-consults-today h-100">
            <div class="card-body">
                <div class="stat-icon icon-consults-today">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">This Week</div>
                    <div class="stat-value">{{ $stats['week'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-calendar2-range"></i>
                        {{ now()->startOfWeek()->format('M d') }} – {{ now()->endOfWeek()->format('M d') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- This Month --}}
    <div class="col-sm-4">
        <div class="card stat-card stat-consults-month h-100">
            <div class="card-body">
                <div class="stat-icon icon-consults-month">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-label">This Month</div>
                    <div class="stat-value">{{ $stats['month'] }}</div>
                    <div class="stat-trend">
                        <i class="bi bi-bar-chart-line"></i> {{ now()->format('F Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Filter Bar ───────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('patient-logs.index') }}" class="row g-2 align-items-end">

            <div class="col-sm-4">
                <label class="form-label small mb-1 fw-semibold">Search Patient</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           class="form-control border-start-0"
                           placeholder="Name or patient number…">
                </div>
            </div>

            <div class="col-sm-3">
                <label class="form-label small mb-1 fw-semibold">Date</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="form-control" max="{{ today()->toDateString() }}">
            </div>

            <div class="col-sm-3 d-flex gap-2 align-items-end">
                <button class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill me-1"></i>Filter
                </button>
                @if($search || $date !== today()->toDateString())
                <a href="{{ route('patient-logs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>

            {{-- Quick date jumps --}}
            <div class="col-sm-2 text-end">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('patient-logs.index', ['date' => today()->subDay()->toDateString()]) }}"
                       class="btn btn-outline-secondary" title="Yesterday">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a href="{{ route('patient-logs.index') }}"
                       class="btn btn-outline-secondary {{ $date === today()->toDateString() ? 'active' : '' }}">
                        Today
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- ── Log Table ────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">

    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <span class="fw-semibold">
            <i class="bi bi-calendar3 me-2 text-primary"></i>
            {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}
        </span>
        <span class="badge text-bg-primary rounded-pill px-3">
            {{ $logs->total() }} {{ Str::plural('visit', $logs->total()) }}
        </span>
    </div>

    <div class="card-body p-0">
        @if ($logs->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-journal-x text-muted" style="font-size:3rem;opacity:.3;"></i>
                <p class="text-muted mt-3 mb-1 fw-semibold">No visits logged for this date.</p>
                <p class="text-muted small">When patients visit the clinic, they will appear here.</p>
                @can('create-consultations')
                <a href="{{ route('patient-logs.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i>Log a Visit Now
                </a>
                @endcan
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:90px;">Time In</th>
                        <th>Patient</th>
                        <th>Category</th>
                        <th>Complaint</th>
                        <th>Treatment</th>
                        <th class="text-center">Disposition</th>
                        <th class="text-center">SMS</th>
                        <th>Logged By</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($logs as $log)
                @php
                    $disp  = $log->disposition;
                    $color = $log->disposition_color;
                    $icon  = $log->disposition_icon;
                @endphp
                <tr>
                    <td class="ps-4 text-nowrap fw-semibold">
                        {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                        @if($log->time_out)
                        <div class="text-muted fw-normal" style="font-size:.75rem;">
                            out {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $log->patient->full_name }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $log->patient->patient_number }}
                            @if($log->patient->year_level)
                                · {{ $log->patient->year_level }}
                            @endif
                            @if($log->patient->section)
                                {{ $log->patient->section }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
                            {{ ucwords(str_replace('_', ' ', $log->patient->category)) }}
                        </span>
                    </td>
                    <td style="max-width:180px;">
                        <span title="{{ $log->chief_complaint }}">
                            {{ Str::limit($log->chief_complaint, 60) }}
                        </span>
                    </td>
                    <td style="max-width:160px;" class="text-muted">
                        {{ $log->treatment ? Str::limit($log->treatment, 50) : '—' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }}-emphasis border border-{{ $color }}-subtle">
                            <i class="bi {{ $icon }} me-1"></i>
                            {{ $log->disposition_label }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($log->sms_guardian)
                            @if($log->sms_sent)
                                <i class="bi bi-check2-circle text-success" title="SMS sent to guardian"></i>
                            @else
                                <i class="bi bi-x-circle text-danger" title="SMS failed"></i>
                            @endif
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-nowrap text-muted">{{ $log->loggedBy->name ?? '—' }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('patient-logs.show', $log) }}"
                               class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('update-consultations')
                            <a href="{{ route('patient-logs.edit', $log) }}"
                               class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('delete-consultations')
                            <button type="button" class="btn btn-outline-danger btn-delete-log"
                                    data-action="{{ route('patient-logs.destroy', $log) }}"
                                    data-name="{{ $log->patient->full_name }}"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-3 py-2 border-top">{{ $logs->links() }}</div>
        @endif
        @endif
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteLogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Remove Log Entry
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Remove the log entry for <strong id="deleteLogName"></strong>?
                This cannot be undone.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteLogForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete-log').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('deleteLogName').textContent = this.dataset.name;
        document.getElementById('deleteLogForm').action      = this.dataset.action;
        new bootstrap.Modal(document.getElementById('deleteLogModal')).show();
    });
});
</script>
@endpush

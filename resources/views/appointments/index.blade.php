@extends('layouts.app')

@section('title', 'Appointments')

@section('breadcrumb')
    <li class="breadcrumb-item active">Appointments</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Appointments</h4>
        <p class="text-muted mb-0 small">Manage patient appointment bookings</p>
    </div>
    @can('create-appointments')
    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus-fill me-1"></i>New Appointment
    </a>
    @endcan
</div>

{{-- Status Tab Pills --}}
<div class="d-flex gap-2 flex-wrap mb-3">
    @php
        $statusColors = [
            ''          => 'primary',
            'pending'   => 'warning',
            'approved'  => 'success',
            'completed' => 'info',
            'cancelled' => 'danger',
            'no_show'   => 'secondary',
        ];
        $statusNames = array_merge([''=>'All'], $statusLabels);
    @endphp
    @foreach ($statusNames as $val => $label)
    <a href="{{ route('appointments.index', array_merge($filters, ['status' => $val])) }}"
       class="btn btn-sm {{ $filters['status'] === $val ? 'btn-'.$statusColors[$val] : 'btn-outline-'.$statusColors[$val] }}">
        {{ $label }}
        @if (isset($counts[$val]))
            <span class="badge bg-white bg-opacity-25 ms-1 text-dark">{{ $counts[$val] }}</span>
        @elseif ($val === '')
            <span class="badge bg-white bg-opacity-25 ms-1 text-dark">{{ $counts->sum() }}</span>
        @endif
    </a>
    @endforeach
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('appointments.index') }}" class="row g-2 align-items-end">
            @if ($filters['status'])
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif

            <div class="col-md-5">
                <label class="form-label small mb-1">Search Patient</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Name or patient number…"
                           value="{{ $filters['search'] }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Date</label>
                <input type="date" name="date" class="form-control"
                       value="{{ $filters['date'] }}">
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if ($filters['search'] || $filters['date'])
                <a href="{{ route('appointments.index', ['status' => $filters['status']]) }}"
                   class="btn btn-outline-secondary" title="Clear">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($appointments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
                <p class="mt-2 mb-0">No appointments found.</p>
                @can('create-appointments')
                <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary mt-3">
                    <i class="bi bi-calendar-plus-fill me-1"></i>Book Appointment
                </a>
                @endcan
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Patient</th>
                        <th>Date & Time</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Booked By</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($appointments as $appt)
                <tr>
                    <td class="ps-4 text-muted small">{{ $appt->id }}</td>
                    <td>
                        <a href="{{ route('patients.show', $appt->patient) }}"
                           class="fw-semibold text-decoration-none">
                            {{ $appt->patient->full_name }}
                        </a>
                        <div class="small text-muted font-monospace">{{ $appt->patient->patient_number }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $appt->appointment_date->format('M d, Y') }}</div>
                        <div class="small text-muted">
                            {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                        </div>
                    </td>
                    <td>
                        <span title="{{ $appt->purpose }}">
                            {{ \Illuminate\Support\Str::limit($appt->purpose, 50) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $appt->status_badge }}-subtle text-{{ $appt->status_badge }}-emphasis rounded-pill">
                            {{ $statusLabels[$appt->status] ?? $appt->status }}
                        </span>
                    </td>
                    <td class="small text-muted">{{ $appt->createdBy->name ?? '—' }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('appointments.show', $appt) }}"
                               class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('approve', $appt)
                            <form method="POST" action="{{ route('appointments.approve', $appt) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-outline-success" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endcan
                            @can('cancel', $appt)
                            <button type="button" class="btn btn-outline-danger btn-cancel"
                                    data-action="{{ route('appointments.cancel', $appt) }}"
                                    title="Cancel">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted small">
                Showing {{ $appointments->firstItem() }}–{{ $appointments->lastItem() }}
                of {{ $appointments->total() }} appointments
            </div>
            {{ $appointments->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-x-circle-fill me-2"></i>Cancel Appointment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason for Cancellation <span class="text-danger">*</span></label>
                    <textarea name="cancelled_reason" class="form-control" rows="3"
                              placeholder="Provide a reason…" required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Cancel Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-cancel').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('cancelForm').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    });
});
</script>
@endpush

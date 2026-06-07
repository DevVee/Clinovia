@extends('layouts.app')

@section('title', 'Appointment #' . $appointment->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
    <li class="breadcrumb-item active">#{{ $appointment->id }}</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-calendar-event me-2 text-primary"></i>Appointment #{{ $appointment->id }}
        </h4>
        <p class="text-muted mb-0 small">
            Booked {{ $appointment->created_at->diffForHumans() }}
            @if ($appointment->createdBy) by {{ $appointment->createdBy->name }} @endif
        </p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-{{ $appointment->status_badge }}-subtle text-{{ $appointment->status_badge }}-emphasis rounded-pill fs-6 px-3 py-2">
            {{ \App\Models\Appointment::statusLabels()[$appointment->status] ?? $appointment->status }}
        </span>
        @can('update', $appointment)
        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-outline-secondary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endcan
    </div>
</div>

<div class="row g-4">

    {{-- Left: Appointment Details --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-calendar3 me-2 text-primary"></i>Appointment Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Date</dt>
                    <dd class="col-7 fw-semibold">
                        {{ $appointment->appointment_date->format('F d, Y') }}
                        <span class="text-muted fw-normal small">
                            ({{ $appointment->appointment_date->diffForHumans() }})
                        </span>
                    </dd>

                    <dt class="col-5 text-muted">Time</dt>
                    <dd class="col-7 fw-semibold">
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                    </dd>

                    <dt class="col-5 text-muted">Purpose</dt>
                    <dd class="col-7">{{ $appointment->purpose }}</dd>

                    @if ($appointment->notes)
                    <dt class="col-5 text-muted">Notes</dt>
                    <dd class="col-7">{{ $appointment->notes }}</dd>
                    @endif

                    @if ($appointment->isApproved() || $appointment->isCompleted())
                    <dt class="col-5 text-muted mt-3">Approved By</dt>
                    <dd class="col-7 mt-3">{{ $appointment->approvedBy->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Approved At</dt>
                    <dd class="col-7">{{ $appointment->approved_at?->format('M d, Y h:i A') ?? '—' }}</dd>
                    @endif

                    @if ($appointment->isCancelled())
                    <dt class="col-5 text-muted mt-3">Cancellation Reason</dt>
                    <dd class="col-7 mt-3">
                        <div class="alert alert-danger py-2 px-3 mb-0 small">
                            {{ $appointment->cancelled_reason }}
                        </div>
                    </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    {{-- Right: Patient Info --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-person-circle me-2 text-primary"></i>Patient
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:44px;height:44px;flex-shrink:0;font-size:1.2rem">
                        {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $appointment->patient->full_name }}</div>
                        <div class="small text-muted font-monospace">{{ $appointment->patient->patient_number }}</div>
                    </div>
                </div>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Category</dt>
                    <dd class="col-7">
                        {{ \App\Models\Patient::categoryLabels()[$appointment->patient->category] ?? $appointment->patient->category }}
                    </dd>

                    <dt class="col-5 text-muted">Age / Sex</dt>
                    <dd class="col-7">
                        {{ $appointment->patient->age }} yrs — {{ ucfirst($appointment->patient->sex) }}
                    </dd>

                    <dt class="col-5 text-muted">Contact</dt>
                    <dd class="col-7">{{ $appointment->patient->contact_number ?? '—' }}</dd>

                    @if ($appointment->patient->allergies)
                    <dt class="col-5 text-muted">Allergies</dt>
                    <dd class="col-7">
                        <span class="text-danger">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ $appointment->patient->allergies }}
                        </span>
                    </dd>
                    @endif
                </dl>
                <a href="{{ route('patients.show', $appointment->patient) }}"
                   class="btn btn-sm btn-outline-primary mt-3">
                    <i class="bi bi-person me-1"></i>View Full Profile
                </a>
            </div>
        </div>

        {{-- Linked Consultation --}}
        @if ($appointment->consultation)
        <div class="card border-0 shadow-sm border-start border-4 border-info">
            <div class="card-body small">
                <div class="fw-semibold mb-1">
                    <i class="bi bi-clipboard-pulse me-1 text-info"></i>Linked Consultation
                </div>
                <div>{{ \Illuminate\Support\Str::limit($appointment->consultation->chief_complaint, 80) }}</div>
                <a href="{{ route('consultations.show', $appointment->consultation) }}"
                   class="btn btn-sm btn-outline-info mt-2">View Consultation</a>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- Action Buttons --}}
@php $canAct = !$appointment->isCompleted() && !$appointment->isCancelled(); @endphp
@if ($canAct)
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-semibold py-3">
        <i class="bi bi-sliders me-2 text-primary"></i>Actions
    </div>
    <div class="card-body d-flex flex-wrap gap-2">

        @can('approve', $appointment)
        <form method="POST" action="{{ route('appointments.approve', $appointment) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Approve
            </button>
        </form>
        @endcan

        @can('complete', $appointment)
        <form method="POST" action="{{ route('appointments.complete', $appointment) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-all me-1"></i>Mark Completed
            </button>
        </form>
        @endcan

        @can('markNoShow', $appointment)
        <form method="POST" action="{{ route('appointments.no-show', $appointment) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-secondary">
                <i class="bi bi-person-slash me-1"></i>Mark No Show
            </button>
        </form>
        @endcan

        @can('cancel', $appointment)
        <button type="button" class="btn btn-danger btn-cancel"
                data-action="{{ route('appointments.cancel', $appointment) }}">
            <i class="bi bi-x-circle me-1"></i>Cancel
        </button>
        @endcan

        @can('create-consultations')
        @if ($appointment->isApproved() && !$appointment->consultation)
        <a href="{{ route('consultations.create', ['appointment_id' => $appointment->id, 'patient_id' => $appointment->patient_id]) }}"
           class="btn btn-outline-info">
            <i class="bi bi-clipboard-plus me-1"></i>Start Consultation
        </a>
        @endif
        @endcan

    </div>
</div>
@endif

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

@extends('layouts.app')

@section('title', 'Visit Log — ' . $log->patient->full_name)

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>Visit Log Detail
        </h4>
        <p class="text-muted small mb-0">
            {{ $log->log_date->format('l, F d, Y') }} &mdash; {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @can('update-consultations')
        <a href="{{ route('patient-logs.edit', $log) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endcan
        <a href="{{ route('patient-logs.index', ['date' => $log->log_date->toDateString()]) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Logbook
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- ── Patient Card ─────────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold bg-primary text-white">
                <i class="bi bi-person-badge-fill me-2"></i>Patient
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-0">{{ $log->patient->full_name }}</h5>
                <div class="text-muted small mb-2">{{ $log->patient->patient_number }}</div>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                        {{ ucwords(str_replace('_', ' ', $log->patient->category)) }}
                    </span>
                    @if($log->patient->year_level)
                    <span class="badge bg-primary-subtle text-primary-emphasis">
                        {{ $log->patient->year_level }}
                    </span>
                    @endif
                    @if($log->patient->section)
                    <span class="badge bg-info-subtle text-info-emphasis">
                        {{ $log->patient->section }}
                    </span>
                    @endif
                </div>
                <a href="{{ route('patients.show', $log->patient) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-person-lines-fill me-1"></i>View Full Patient Record
                </a>
            </div>
        </div>

        {{-- Guardian --}}
        @if($log->patient->guardian_name || $log->patient->guardian_contact)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-people-fill me-2 text-success"></i>Guardian
            </div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $log->patient->guardian_name ?? '—' }}</div>
                @if($log->patient->guardian_relationship)
                <div class="text-muted">{{ $log->patient->guardian_relationship }}</div>
                @endif
                @if($log->patient->guardian_contact)
                <div class="mt-1">
                    <i class="bi bi-telephone-fill me-1 text-success"></i>
                    {{ $log->patient->guardian_contact }}
                </div>
                @endif
                <div class="mt-2">
                    @if($log->sms_guardian)
                        @if($log->sms_sent)
                        <span class="badge bg-success-subtle text-success-emphasis">
                            <i class="bi bi-check2-circle me-1"></i>SMS Sent ✓
                        </span>
                        @else
                        <span class="badge bg-danger-subtle text-danger-emphasis">
                            <i class="bi bi-x-circle me-1"></i>SMS Failed
                        </span>
                        @endif
                    @else
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            <i class="bi bi-bell-slash me-1"></i>No SMS
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Time Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-clock me-2 text-primary"></i>Time
            </div>
            <div class="card-body small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Time In:</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</span>
                </div>
                @if($log->time_out)
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Time Out:</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</span>
                </div>
                @php
                    $duration = \Carbon\Carbon::parse($log->time_in)->diffInMinutes(\Carbon\Carbon::parse($log->time_out));
                @endphp
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Duration:</span>
                    <span class="fw-semibold">{{ $duration }} min</span>
                </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Logged by:</span>
                    <span class="fw-semibold">{{ $log->loggedBy->name ?? '—' }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Visit Details ────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Vitals --}}
        @if($log->vital_signs)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-thermometer-half me-2 text-warning"></i>Vital Signs
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    @if(!empty($log->vital_signs['temperature']))
                    <div class="col">
                        <div class="fs-4 fw-bold text-warning">{{ $log->vital_signs['temperature'] }}°C</div>
                        <div class="small text-muted">Temperature</div>
                    </div>
                    @endif
                    @if(!empty($log->vital_signs['blood_pressure']))
                    <div class="col">
                        <div class="fs-4 fw-bold text-danger">{{ $log->vital_signs['blood_pressure'] }}</div>
                        <div class="small text-muted">Blood Pressure</div>
                    </div>
                    @endif
                    @if(!empty($log->vital_signs['pulse']))
                    <div class="col">
                        <div class="fs-4 fw-bold text-primary">{{ $log->vital_signs['pulse'] }}</div>
                        <div class="small text-muted">Pulse (bpm)</div>
                    </div>
                    @endif
                    @if(!empty($log->vital_signs['weight']))
                    <div class="col">
                        <div class="fs-4 fw-bold text-success">{{ $log->vital_signs['weight'] }} kg</div>
                        <div class="small text-muted">Weight</div>
                    </div>
                    @endif
                    @if(!empty($log->vital_signs['height']))
                    <div class="col">
                        <div class="fs-4 fw-bold text-info">{{ $log->vital_signs['height'] }} cm</div>
                        <div class="small text-muted">Height</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Clinical Notes --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-file-medical-fill me-2 text-danger"></i>Clinical Notes
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-muted fw-semibold mb-1">Chief Complaint</div>
                    <div class="p-2 bg-danger-subtle rounded">{{ $log->chief_complaint }}</div>
                </div>
                @if($log->assessment)
                <div class="mb-3">
                    <div class="small text-muted fw-semibold mb-1">Assessment / Findings</div>
                    <div>{{ $log->assessment }}</div>
                </div>
                @endif
                @if($log->treatment)
                <div class="mb-3">
                    <div class="small text-muted fw-semibold mb-1">Treatment / Action Taken</div>
                    <div class="p-2 bg-success-subtle rounded">{{ $log->treatment }}</div>
                </div>
                @endif
                @if($log->notes)
                <div>
                    <div class="small text-muted fw-semibold mb-1">Additional Notes</div>
                    <div class="text-muted fst-italic">{{ $log->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Disposition --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-signpost-2 me-2 text-info"></i>Disposition
            </div>
            <div class="card-body">
                <span class="badge bg-{{ $log->disposition_color }}-subtle text-{{ $log->disposition_color }}-emphasis border border-{{ $log->disposition_color }}-subtle fs-6 px-3 py-2">
                    <i class="bi {{ $log->disposition_icon }} me-2"></i>
                    {{ $log->disposition_label }}
                </span>
            </div>
        </div>

    </div>

</div>

{{-- Action Buttons --}}
<div class="d-flex gap-2 mt-4 justify-content-end">
    @can('create-consultations')
    <a href="{{ route('patient-logs.create', ['patient_id' => $log->patient_id]) }}"
       class="btn btn-outline-primary">
        <i class="bi bi-journal-plus me-1"></i>Log Another Visit for This Patient
    </a>
    @endcan
    @can('delete-consultations')
    <form method="POST" action="{{ route('patient-logs.destroy', $log) }}"
          onsubmit="return confirm('Remove this log entry?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">
            <i class="bi bi-trash me-1"></i>Remove Entry
        </button>
    </form>
    @endcan
</div>

@endsection

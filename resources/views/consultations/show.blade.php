@extends('layouts.app')

@section('title', 'Consultation #' . $consultation->id)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-clipboard2-pulse-fill me-2 text-primary"></i>
            Consultation #{{ $consultation->id }}
        </h4>
        <p class="text-muted mb-0 small">
            {{ $consultation->visit_date->format('l, F d, Y') }}
            @if ($consultation->visit_time)
                at {{ \Carbon\Carbon::parse($consultation->visit_time)->format('h:i A') }}
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @can('update-consultations')
        <a href="{{ route('consultations.edit', $consultation) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endcan
        <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left ─────────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Patient Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-person-fill me-2 text-primary"></i>Patient
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;">
                        <i class="bi bi-person-fill text-primary fs-5"></i>
                    </div>
                    <div>
                        <a href="{{ route('patients.show', $consultation->patient) }}"
                           class="fw-bold text-decoration-none d-block">
                            {{ $consultation->patient->full_name }}
                        </a>
                        <div class="small text-muted font-monospace">{{ $consultation->patient->patient_number }}</div>
                        <div class="small text-muted">
                            {{ ucfirst($consultation->patient->category) }} &mdash;
                            {{ $consultation->patient->age }} yrs &mdash;
                            {{ ucfirst($consultation->patient->sex) }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('patients.show', $consultation->patient) }}"
                   class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-person-lines-fill me-1"></i>View Patient Profile
                </a>
            </div>
        </div>

        {{-- Visit Meta --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Visit Info
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3" style="width:45%;">Date</td>
                            <td class="fw-semibold">{{ $consultation->visit_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Time</td>
                            <td>{{ $consultation->visit_time
                                    ? \Carbon\Carbon::parse($consultation->visit_time)->format('h:i A')
                                    : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Nurse / Staff</td>
                            <td>{{ $consultation->nurse->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Appointment</td>
                            <td>
                                @if ($consultation->appointment)
                                    <a href="{{ route('appointments.show', $consultation->appointment) }}"
                                       class="badge bg-success-subtle text-success-emphasis text-decoration-none">
                                        #{{ $consultation->appointment->id }}
                                        {{ $consultation->appointment->appointment_date->format('M d') }}
                                    </a>
                                @else
                                    <span class="text-muted small">Walk-in</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Recorded</td>
                            <td class="small text-muted">{{ $consultation->created_at->diffForHumans() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── Right ────────────────────────────────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Clinical Notes --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-file-medical-fill me-2 text-primary"></i>Clinical Notes
            </div>
            <div class="card-body">

                <div class="mb-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.06em;">
                        Chief Complaint
                    </div>
                    <p class="mb-0">{{ $consultation->chief_complaint }}</p>
                </div>

                @if ($consultation->assessment)
                <div class="mb-4">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.06em;">
                        Assessment / Findings
                    </div>
                    <p class="mb-0">{{ $consultation->assessment }}</p>
                </div>
                @endif

                @if ($consultation->diagnosis || $consultation->treatment)
                <div class="row g-3 mb-0">
                    @if ($consultation->diagnosis)
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.06em;">
                            Diagnosis
                        </div>
                        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 fs-6">
                            {{ $consultation->diagnosis }}
                        </span>
                    </div>
                    @endif
                    @if ($consultation->treatment)
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.06em;">
                            Treatment / Medication
                        </div>
                        <p class="mb-0">{{ $consultation->treatment }}</p>
                    </div>
                    @endif
                </div>
                @endif

                @if ($consultation->notes)
                <div class="mt-4 pt-3 border-top">
                    <div class="text-muted small fw-bold text-uppercase mb-1" style="letter-spacing:.06em;">
                        Additional Notes
                    </div>
                    <p class="mb-0 text-muted">{{ $consultation->notes }}</p>
                </div>
                @endif

            </div>
        </div>

        {{-- Dispensed Medicines (Phase 6 populates this) --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-prescription2 me-2"></i>Dispensed Medicines</span>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                    {{ $consultation->dispensingRecords->count() }}
                </span>
            </div>
            <div class="card-body p-0">
                @forelse ($consultation->dispensingRecords as $rec)
                <div class="d-flex align-items-center px-3 py-2 border-bottom">
                    <i class="bi bi-capsule-pill text-primary me-3"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $rec->medicine->name ?? 'Unknown' }}</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary-emphasis">
                        {{ $rec->quantity }} {{ $rec->medicine->unit ?? '' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-prescription2 d-block mb-1 opacity-25" style="font-size:1.8rem;"></i>
                    <small>No medicines dispensed for this visit.</small>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

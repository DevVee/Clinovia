@extends('layouts.app')

@section('title', $patient->full_name . ' — Patient Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@section('content')

{{-- Header --}}
<div class="page-header d-flex align-items-start justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fs-4 fw-bold"
             style="width:56px;height:56px;flex-shrink:0;">
            {{ strtoupper(substr($patient->first_name, 0, 1)) }}
        </div>
        <div>
            <h4 class="mb-0">{{ $patient->full_name }}</h4>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="font-monospace text-primary small fw-semibold">{{ $patient->patient_number }}</span>
                <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
                    {{ \App\Models\Patient::categoryLabels()[$patient->category] ?? $patient->category }}
                </span>
                @if ($patient->is_active)
                    <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                @else
                    <span class="badge bg-danger-subtle text-danger rounded-pill">Inactive</span>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        @can('update-patients')
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endcan
        @can('create-appointments')
        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">
            <i class="bi bi-calendar-plus me-1"></i>Book Appointment
        </a>
        @endcan
    </div>
</div>

<div class="row g-4">

    {{-- ---- LEFT: Patient Details ---- --}}
    <div class="col-lg-4">

        {{-- Personal Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-person-circle me-2 text-primary"></i>Personal Information
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Sex</dt>
                    <dd class="col-7">{{ ucfirst($patient->sex) }}</dd>

                    <dt class="col-5 text-muted">Birthdate</dt>
                    <dd class="col-7">{{ $patient->birthdate->format('M d, Y') }}</dd>

                    <dt class="col-5 text-muted">Age</dt>
                    <dd class="col-7">{{ $patient->age }} years old</dd>

                    <dt class="col-5 text-muted">Contact</dt>
                    <dd class="col-7">{{ $patient->contact_number ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7" style="word-break:break-all">{{ $patient->email ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Address</dt>
                    <dd class="col-7">{{ $patient->address ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        {{-- Academic Info --}}
        @if ($patient->year_level || $patient->program_strand || $patient->section)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-mortarboard me-2 text-primary"></i>Academic Information
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Year / Grade</dt>
                    <dd class="col-7">{{ $patient->year_level ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Program / Strand</dt>
                    <dd class="col-7">{{ $patient->program_strand ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Section</dt>
                    <dd class="col-7">{{ $patient->section ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        @endif

        {{-- Guardian Info --}}
        @if ($patient->guardian_name)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-house-heart me-2 text-primary"></i>Guardian / Parent
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7">{{ $patient->guardian_name }}</dd>

                    <dt class="col-5 text-muted">Relationship</dt>
                    <dd class="col-7">{{ $patient->guardian_relationship ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Contact</dt>
                    <dd class="col-7">{{ $patient->guardian_contact ?? '—' }}</dd>

                    <dt class="col-5 text-muted">Address</dt>
                    <dd class="col-7">{{ $patient->guardian_address ?? '—' }}</dd>
                </dl>
            </div>
        </div>
        @endif

        {{-- Medical Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-heart-pulse me-2 text-danger"></i>Medical Information
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Blood Type</dt>
                    <dd class="col-7">
                        @if ($patient->blood_type)
                            <span class="badge bg-danger-subtle text-danger fw-semibold">{{ $patient->blood_type }}</span>
                        @else
                            <span class="text-muted">Unknown</span>
                        @endif
                    </dd>

                    <dt class="col-12 text-muted mt-2">Allergies</dt>
                    <dd class="col-12">
                        @if ($patient->allergies)
                            <div class="alert alert-warning py-2 px-3 mb-0 small">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $patient->allergies }}
                            </div>
                        @else
                            <span class="text-muted">None recorded</span>
                        @endif
                    </dd>

                    <dt class="col-12 text-muted mt-2">Medical Conditions</dt>
                    <dd class="col-12">{{ $patient->medical_conditions ?? '—' }}</dd>

                    @if ($patient->notes)
                    <dt class="col-12 text-muted mt-2">Notes</dt>
                    <dd class="col-12">{{ $patient->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Emergency Contact --}}
        @if ($patient->emergency_contact_name)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold py-3">
                <i class="bi bi-telephone-fill me-2 text-danger"></i>Emergency Contact
            </div>
            <div class="card-body small">
                <div class="fw-semibold">{{ $patient->emergency_contact_name }}</div>
                <div class="text-muted">{{ $patient->emergency_contact_number ?? '—' }}</div>
            </div>
        </div>
        @endif

    </div>

    {{-- ---- RIGHT: Health History Tabs ---- --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <ul class="nav nav-tabs" id="historyTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="visits-tab"
                                data-bs-toggle="tab" data-bs-target="#clinic-visits-pane"
                                type="button" role="tab">
                            <i class="bi bi-journal-medical me-1"></i>Clinic Visits
                            <span class="badge bg-primary ms-1">{{ $history['clinic_visits']->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appt-tab"
                                data-bs-toggle="tab" data-bs-target="#appointments-pane"
                                type="button" role="tab">
                            <i class="bi bi-calendar-check me-1"></i>Appointments
                            <span class="badge bg-primary ms-1">{{ $history['appointments']->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="consult-tab"
                                data-bs-toggle="tab" data-bs-target="#consultations-pane"
                                type="button" role="tab">
                            <i class="bi bi-clipboard-pulse me-1"></i>Consultations
                            <span class="badge bg-primary ms-1">{{ $history['consultations']->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="dispense-tab"
                                data-bs-toggle="tab" data-bs-target="#dispensing-pane"
                                type="button" role="tab">
                            <i class="bi bi-capsule me-1"></i>Dispensing
                            <span class="badge bg-primary ms-1">{{ $history['dispensing_records']->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <div class="tab-content">

                    {{-- Clinic Visits --}}
                    <div class="tab-pane fade show active" id="clinic-visits-pane" role="tabpanel">
                        @if ($history['clinic_visits']->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No clinic visits recorded.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Time In</th>
                                        <th>Complaint</th>
                                        <th class="text-center">Disposition</th>
                                        <th>Logged By</th>
                                        <th class="pe-4 text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($history['clinic_visits'] as $visit)
                                <tr>
                                    <td class="ps-4 fw-semibold">
                                        {{ $visit->log_date->format('M d, Y') }}
                                    </td>
                                    <td class="text-nowrap">
                                        {{ \Carbon\Carbon::parse($visit->time_in)->format('h:i A') }}
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($visit->chief_complaint, 60) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $visit->disposition_color }}-subtle text-{{ $visit->disposition_color }}-emphasis border border-{{ $visit->disposition_color }}-subtle rounded-pill">
                                            {{ $visit->disposition_label }}
                                        </span>
                                    </td>
                                    <td class="text-muted text-nowrap">{{ $visit->loggedBy->name ?? '—' }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('patient-logs.show', $visit) }}" class="btn btn-sm btn-outline-primary">
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

                    {{-- Appointments --}}
                    <div class="tab-pane fade" id="appointments-pane" role="tabpanel">
                        @if ($history['appointments']->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No appointments recorded.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date & Time</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($history['appointments'] as $appt)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</div>
                                        <div class="text-muted">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</div>
                                    </td>
                                    <td>{{ $appt->purpose }}</td>
                                    <td>
                                        <span class="badge bg-{{ $appt->status_badge }}-subtle text-{{ $appt->status_badge }}-emphasis rounded-pill">
                                            {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-outline-primary">
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

                    {{-- Consultations --}}
                    <div class="tab-pane fade" id="consultations-pane" role="tabpanel">
                        @if ($history['consultations']->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard2-x" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No consultations recorded.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Complaint</th>
                                        <th>Nurse</th>
                                        <th class="pe-4 text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($history['consultations'] as $consult)
                                <tr>
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($consult->visit_date)->format('M d, Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($consult->chief_complaint, 60) }}</td>
                                    <td>{{ $consult->nurse->name ?? '—' }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('consultations.show', $consult) }}" class="btn btn-sm btn-outline-primary">
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

                    {{-- Dispensing --}}
                    <div class="tab-pane fade" id="dispensing-pane" role="tabpanel">
                        @if ($history['dispensing_records']->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-capsule" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No medicines dispensed.</p>
                            </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Medicine</th>
                                        <th>Qty</th>
                                        <th>Dispensed By</th>
                                        <th class="pe-4 text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($history['dispensing_records'] as $rec)
                                <tr>
                                    <td class="ps-4">{{ \Carbon\Carbon::parse($rec->dispensed_at)->format('M d, Y') }}</td>
                                    <td>{{ $rec->medicine->name ?? '—' }}</td>
                                    <td>{{ $rec->quantity }}</td>
                                    <td>{{ $rec->dispensedBy->name ?? '—' }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('dispensing.show', $rec) }}" class="btn btn-sm btn-outline-primary">
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

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

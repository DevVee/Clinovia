@extends('layouts.app')

@section('title', 'Edit Consultation #' . $consultation->id)

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Consultation</h4>
        <p class="text-muted mb-0 small">
            {{ $consultation->patient->full_name }} &mdash;
            {{ $consultation->visit_date->format('M d, Y') }}
        </p>
    </div>
    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('consultations.update', $consultation) }}">
    @csrf @method('PUT')
    <div class="row g-4">

        {{-- ── Left: Visit Details ─────────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="bi bi-person-fill me-2 text-primary"></i>Visit Details
                </div>
                <div class="card-body">

                    {{-- Patient (locked) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient</label>
                        <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">
                        <input type="text" class="form-control bg-light" readonly
                               value="{{ $consultation->patient->full_name }} ({{ $consultation->patient->patient_number }})">
                        <div class="form-text">Patient cannot be changed after creation.</div>
                    </div>

                    {{-- Linked Appointment --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Linked Appointment</label>
                        <select name="appointment_id"
                                class="form-select @error('appointment_id') is-invalid @enderror">
                            <option value="">None — walk-in</option>
                            @foreach ($appointments as $appt)
                                <option value="{{ $appt->id }}"
                                    {{ old('appointment_id', $consultation->appointment_id) == $appt->id ? 'selected' : '' }}>
                                    {{ $appt->appointment_date->format('M d, Y') }}
                                    — {{ \Illuminate\Support\Str::limit($appt->purpose, 30) }}
                                    ({{ ucfirst($appt->status) }})
                                </option>
                            @endforeach
                        </select>
                        @error('appointment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Visit Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Visit Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="visit_date"
                               class="form-control @error('visit_date') is-invalid @enderror"
                               value="{{ old('visit_date', $consultation->visit_date->toDateString()) }}" required>
                        @error('visit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Visit Time --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Visit Time</label>
                        <input type="time" name="visit_time"
                               class="form-control @error('visit_time') is-invalid @enderror"
                               value="{{ old('visit_time', $consultation->visit_time
                                    ? \Carbon\Carbon::parse($consultation->visit_time)->format('H:i')
                                    : '') }}">
                        @error('visit_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Right: Clinical Notes ───────────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="bi bi-file-medical-fill me-2 text-primary"></i>Clinical Notes
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Chief Complaint <span class="text-danger">*</span>
                        </label>
                        <textarea name="chief_complaint" rows="3"
                                  class="form-control @error('chief_complaint') is-invalid @enderror"
                                  required>{{ old('chief_complaint', $consultation->chief_complaint) }}</textarea>
                        @error('chief_complaint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assessment / Findings</label>
                        <textarea name="assessment" rows="3"
                                  class="form-control @error('assessment') is-invalid @enderror"
                                  >{{ old('assessment', $consultation->assessment) }}</textarea>
                        @error('assessment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diagnosis</label>
                            <input type="text" name="diagnosis"
                                   class="form-control @error('diagnosis') is-invalid @enderror"
                                   value="{{ old('diagnosis', $consultation->diagnosis) }}">
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Treatment / Medication Given</label>
                            <input type="text" name="treatment"
                                   class="form-control @error('treatment') is-invalid @enderror"
                                   value="{{ old('treatment', $consultation->treatment) }}">
                            @error('treatment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Additional Notes</label>
                        <textarea name="notes" rows="2"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  >{{ old('notes', $consultation->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

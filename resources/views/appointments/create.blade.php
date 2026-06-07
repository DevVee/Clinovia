@extends('layouts.app')

@section('title', 'New Appointment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
    <li class="breadcrumb-item active">New Appointment</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-calendar-plus-fill me-2 text-primary"></i>New Appointment</h4>
        <p class="text-muted mb-0 small">Book a clinic appointment for a patient</p>
    </div>
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">
<form method="POST" action="{{ route('appointments.store') }}" novalidate>
    @csrf

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold py-3">
            <i class="bi bi-person-check me-2 text-primary"></i>Appointment Details
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patientSelect"
                            class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">— Search and select patient —</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ (old('patient_id', $selected) == $patient->id) ? 'selected' : '' }}>
                                {{ $patient->full_name }} — {{ $patient->patient_number }}
                                ({{ \App\Models\Patient::categoryLabels()[$patient->category] ?? $patient->category }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Appointment Date <span class="text-danger">*</span></label>
                    <input type="date" name="appointment_date"
                           class="form-control @error('appointment_date') is-invalid @enderror"
                           value="{{ old('appointment_date', now()->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}" required>
                    @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Time Slot <span class="text-danger">*</span></label>
                    <select name="appointment_time"
                            class="form-select @error('appointment_time') is-invalid @enderror" required>
                        <option value="">— Select time —</option>
                        @foreach ($timeSlots as $slot)
                            <option value="{{ $slot->slot_time }}"
                                {{ old('appointment_time') === $slot->slot_time ? 'selected' : '' }}>
                                {{ $slot->formatted_time }}
                                (max {{ $slot->max_appointments }} patients)
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Purpose of Visit <span class="text-danger">*</span></label>
                    <input type="text" name="purpose"
                           class="form-control @error('purpose') is-invalid @enderror"
                           placeholder="e.g. General check-up, Fever, Wound dressing…"
                           value="{{ old('purpose') }}" required>
                    @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Notes <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Additional information…">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
        <div class="card-footer bg-white border-top d-flex justify-content-between">
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-calendar-check me-1"></i>Book Appointment
            </button>
        </div>
    </div>

</form>
</div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Appointment #' . $appointment->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Appointments</a></li>
    <li class="breadcrumb-item"><a href="{{ route('appointments.show', $appointment) }}">#{{ $appointment->id }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Appointment</h4>
        <p class="text-muted mb-0 small">
            Appointment #{{ $appointment->id }} — {{ $appointment->patient->full_name }}
        </p>
    </div>
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

@if (!$appointment->isPending())
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Only <strong>pending</strong> appointments can be edited. This appointment is
    <strong>{{ \App\Models\Appointment::statusLabels()[$appointment->status] }}</strong>.
</div>
@endif

<div class="row justify-content-center">
<div class="col-lg-8">
<form method="POST" action="{{ route('appointments.update', $appointment) }}" novalidate>
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold py-3">
            <i class="bi bi-calendar-event me-2 text-primary"></i>Appointment Details
        </div>
        <div class="card-body">
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                    <select name="patient_id"
                            class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">— Select patient —</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}"
                                {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->full_name }} — {{ $patient->patient_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Appointment Date <span class="text-danger">*</span></label>
                    <input type="date" name="appointment_date"
                           class="form-control @error('appointment_date') is-invalid @enderror"
                           value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}"
                           required>
                    @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Time Slot <span class="text-danger">*</span></label>
                    <select name="appointment_time"
                            class="form-select @error('appointment_time') is-invalid @enderror" required>
                        <option value="">— Select time —</option>
                        @foreach ($timeSlots as $slot)
                            <option value="{{ $slot->slot_time }}"
                                {{ old('appointment_time', $appointment->appointment_time) === $slot->slot_time ? 'selected' : '' }}>
                                {{ $slot->formatted_time }} (max {{ $slot->max_appointments }} patients)
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Purpose of Visit <span class="text-danger">*</span></label>
                    <input type="text" name="purpose"
                           class="form-control @error('purpose') is-invalid @enderror"
                           value="{{ old('purpose', $appointment->purpose) }}" required>
                    @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Notes</label>
                    <textarea name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $appointment->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
        <div class="card-footer bg-white border-top d-flex justify-content-between">
            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary">
                <i class="bi bi-x-lg me-1"></i>Cancel
            </a>
            <button type="submit" class="btn btn-primary" {{ !$appointment->isPending() ? 'disabled' : '' }}>
                <i class="bi bi-check-lg me-1"></i>Save Changes
            </button>
        </div>
    </div>

</form>
</div>
</div>
@endsection

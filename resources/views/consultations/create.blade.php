@extends('layouts.app')

@section('title', 'New Consultation')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-clipboard2-plus-fill me-2 text-primary"></i>New Consultation</h4>
        <p class="text-muted mb-0 small">Record a patient clinic visit</p>
    </div>
    <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('consultations.store') }}">
    @csrf
    <div class="row g-4">

        {{-- ── Left: Visit Details ─────────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <i class="bi bi-person-fill me-2 text-primary"></i>Visit Details
                </div>
                <div class="card-body">

                    {{-- Patient --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Patient <span class="text-danger">*</span>
                        </label>
                        <select name="patient_id" id="patientSelect"
                                class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">Select patient…</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}"
                                    {{ old('patient_id', $selectedPatient) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->last_name }}, {{ $patient->first_name }}
                                    {{ $patient->middle_name ? mb_substr($patient->middle_name,0,1).'.' : '' }}
                                    — {{ $patient->patient_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Linked Appointment --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Linked Appointment
                            <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">optional</span>
                        </label>
                        <select name="appointment_id" id="appointmentSelect"
                                class="form-select @error('appointment_id') is-invalid @enderror">
                            <option value="">None — walk-in</option>
                        </select>
                        @error('appointment_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Will mark the appointment as Completed.</div>
                    </div>

                    {{-- Visit Date --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Visit Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="visit_date"
                               class="form-control @error('visit_date') is-invalid @enderror"
                               value="{{ old('visit_date', today()->toDateString()) }}" required>
                        @error('visit_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Visit Time --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Visit Time</label>
                        <input type="time" name="visit_time"
                               class="form-control @error('visit_time') is-invalid @enderror"
                               value="{{ old('visit_time', now()->format('H:i')) }}">
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
                                  placeholder="Primary reason for visit…" required
                                  >{{ old('chief_complaint') }}</textarea>
                        @error('chief_complaint')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Assessment / Findings</label>
                        <textarea name="assessment" rows="3"
                                  class="form-control @error('assessment') is-invalid @enderror"
                                  placeholder="Physical examination findings…"
                                  >{{ old('assessment') }}</textarea>
                        @error('assessment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diagnosis</label>
                            <input type="text" name="diagnosis"
                                   class="form-control @error('diagnosis') is-invalid @enderror"
                                   placeholder="e.g. Acute URTI"
                                   value="{{ old('diagnosis') }}">
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Treatment / Medication Given</label>
                            <input type="text" name="treatment"
                                   class="form-control @error('treatment') is-invalid @enderror"
                                   placeholder="e.g. Paracetamol 500mg"
                                   value="{{ old('treatment') }}">
                            @error('treatment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Additional Notes</label>
                        <textarea name="notes" rows="2"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="Follow-up, referrals, etc."
                                  >{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('consultations.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i>Save Consultation
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
const apptByPatient = @json($appointments->groupBy('patient_id'));
const apptSelect    = document.getElementById('appointmentSelect');
const oldAppt       = '{{ old('appointment_id') }}';

function populateAppointments(patientId) {
    apptSelect.innerHTML = '<option value="">None — walk-in</option>';
    (apptByPatient[patientId] || []).forEach(a => {
        const opt  = document.createElement('option');
        opt.value  = a.id;
        const d    = new Date(a.appointment_date + 'T00:00:00').toLocaleDateString('en-PH', {month:'short', day:'numeric', year:'numeric'});
        opt.textContent = d + ' — ' + (a.purpose || 'No purpose listed');
        if (String(a.id) === oldAppt) opt.selected = true;
        apptSelect.appendChild(opt);
    });
}

const patientSel = document.getElementById('patientSelect');
patientSel.addEventListener('change', function () { populateAppointments(this.value); });

// Init on load if patient pre-selected
if (patientSel.value) populateAppointments(patientSel.value);
</script>
@endpush

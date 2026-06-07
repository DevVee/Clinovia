@extends('layouts.app')

@section('title', 'Edit Log Entry')

@section('content')

@php
$vitals = $patientLog->vital_signs ?? [];
@endphp

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Log Entry
        </h4>
        <p class="text-muted small mb-0">
            {{ $patientLog->patient->full_name ?? '—' }} &mdash;
            {{ \Carbon\Carbon::parse($patientLog->log_date)->format('M d, Y') }}
        </p>
    </div>
    <a href="{{ route('patient-logs.show', $patientLog) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Log
    </a>
</div>

<form method="POST" action="{{ route('patient-logs.update', $patientLog) }}" id="logForm" novalidate>
@csrf @method('PUT')

<div class="row g-4">

    {{-- ── LEFT COLUMN: Who visited & When ─────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Patient --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-person-fill me-2 text-primary"></i>Who Visited?
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Patient <span class="text-danger">*</span>
                    </label>
                    <select name="patient_id" id="patientSelect"
                            class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">— Select patient —</option>
                        @foreach($patients as $p)
                        <option value="{{ $p->id }}"
                                data-guardian="{{ $p->guardian_name }}"
                                data-guardian-contact="{{ $p->guardian_contact }}"
                                data-category="{{ ucwords(str_replace('_', ' ', $p->category)) }}"
                                data-year="{{ $p->year_level }}"
                                data-section="{{ $p->section }}"
                                {{ old('patient_id', $patientLog->patient_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->last_name }}, {{ $p->first_name }}
                            {{ $p->middle_name ? mb_substr($p->middle_name,0,1).'.' : '' }}
                            — {{ $p->patient_number }}
                        </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Patient info preview --}}
                <div id="patientInfo" class="alert alert-light border small py-2 d-none">
                    <i class="bi bi-person-badge me-1 text-primary"></i>
                    <span id="patientInfoText"></span>
                </div>
            </div>
        </div>

        {{-- Date & Time --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-clock me-2 text-primary"></i>Date &amp; Time
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Visit Date <span class="text-danger">*</span></label>
                    <input type="date" name="log_date"
                           class="form-control @error('log_date') is-invalid @enderror"
                           value="{{ old('log_date', $patientLog->log_date->toDateString()) }}"
                           max="{{ today()->toDateString() }}" required>
                    @error('log_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-2">
                    <div class="col">
                        <label class="form-label fw-semibold">Time In <span class="text-danger">*</span></label>
                        <input type="time" name="time_in"
                               class="form-control @error('time_in') is-invalid @enderror"
                               value="{{ old('time_in', \Carbon\Carbon::parse($patientLog->time_in)->format('H:i')) }}" required>
                        @error('time_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col">
                        <label class="form-label fw-semibold">Time Out</label>
                        <input type="time" name="time_out"
                               class="form-control @error('time_out') is-invalid @enderror"
                               value="{{ old('time_out', $patientLog->time_out ? \Carbon\Carbon::parse($patientLog->time_out)->format('H:i') : '') }}">
                        @error('time_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Vitals --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="bi bi-thermometer-half me-2 text-warning"></i>Vital Signs</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                        data-bs-toggle="collapse" data-bs-target="#vitalsPanel">
                    <i class="bi bi-chevron-{{ $vitals ? 'up' : 'down' }}"></i>
                    {{ $vitals ? 'Recorded' : 'Optional' }}
                </button>
            </div>
            <div class="collapse {{ $vitals ? 'show' : '' }}" id="vitalsPanel">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Temp (°C)</label>
                            <input type="number" name="vital_temp" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="e.g. 37.2"
                                   value="{{ old('vital_temp', $vitals['temperature'] ?? '') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Blood Pressure</label>
                            <input type="text" name="vital_bp"
                                   class="form-control form-control-sm"
                                   placeholder="e.g. 120/80"
                                   value="{{ old('vital_bp', $vitals['blood_pressure'] ?? '') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Pulse (bpm)</label>
                            <input type="number" name="vital_pulse"
                                   class="form-control form-control-sm"
                                   placeholder="72"
                                   value="{{ old('vital_pulse', $vitals['pulse'] ?? '') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Weight (kg)</label>
                            <input type="number" name="vital_weight" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="50"
                                   value="{{ old('vital_weight', $vitals['weight'] ?? '') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Height (cm)</label>
                            <input type="number" name="vital_height" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="160"
                                   value="{{ old('vital_height', $vitals['height'] ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── RIGHT COLUMN: Clinical Details ──────────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Complaint --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-chat-left-text me-2 text-danger"></i>Why did they visit?
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <div class="form-label fw-semibold small">Quick Complaints</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['Headache','Fever','Stomachache','Toothache','Cough & Colds','Dizziness','Wound / Injury','Chest Pain','Vomiting','Diarrhea','Fainting','Eye Pain','Ear Pain','Allergic Reaction','Menstrual Cramps','Body Pain','Asthma Attack','High Blood Pressure'] as $c)
                        <button type="button" class="btn btn-sm btn-outline-secondary complaint-btn">{{ $c }}</button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="form-label fw-semibold">
                        Chief Complaint <span class="text-danger">*</span>
                    </label>
                    <textarea name="chief_complaint" id="chiefComplaint" rows="3"
                              class="form-control @error('chief_complaint') is-invalid @enderror"
                              placeholder="Describe the patient's main reason for visiting…"
                              required>{{ old('chief_complaint', $patientLog->chief_complaint) }}</textarea>
                    @error('chief_complaint')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Assessment & Treatment --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-clipboard2-pulse me-2 text-success"></i>What was done?
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nurse/Doctor's Assessment</label>
                    <textarea name="assessment" rows="2"
                              class="form-control @error('assessment') is-invalid @enderror"
                              placeholder="Physical findings, observations…">{{ old('assessment', $patientLog->assessment) }}</textarea>
                    @error('assessment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label fw-semibold">Treatment / Action Taken</label>
                    <textarea name="treatment" rows="2"
                              class="form-control @error('treatment') is-invalid @enderror"
                              placeholder="Medicines given, first aid done, advice given…">{{ old('treatment', $patientLog->treatment) }}</textarea>
                    @error('treatment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Disposition --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header fw-semibold">
                <i class="bi bi-signpost-2-fill me-2 text-primary"></i>Disposition
            </div>
            <div class="card-body">
                @php
                    $dispositions = [
                        'rest_in_clinic'       => ['label' => 'Rest in Clinic',          'icon' => 'bi-hospital',      'color' => 'info'],
                        'returned_to_class'    => ['label' => 'Returned to Class / Work', 'icon' => 'bi-mortarboard',   'color' => 'success'],
                        'sent_home'            => ['label' => 'Sent Home',                'icon' => 'bi-house-heart',   'color' => 'warning'],
                        'referred_to_hospital' => ['label' => 'Referred to Hospital',     'icon' => 'bi-hospital-fill', 'color' => 'danger'],
                        'further_observation'  => ['label' => 'Under Observation',        'icon' => 'bi-eye-fill',      'color' => 'secondary'],
                    ];
                @endphp
                <div class="row g-2">
                    @foreach($dispositions as $val => $d)
                    <div class="col-sm-4 col-6">
                        <input type="radio" class="btn-check" name="disposition"
                               id="disp_{{ $val }}" value="{{ $val }}"
                               {{ old('disposition', $patientLog->disposition) === $val ? 'checked' : '' }}>
                        <label class="btn btn-outline-{{ $d['color'] }} w-100 d-flex align-items-center gap-2 px-3 py-2 text-start"
                               for="disp_{{ $val }}" style="min-height:48px;">
                            <i class="bi {{ $d['icon'] }} fs-5 flex-shrink-0"></i>
                            <span class="small fw-semibold lh-sm">{{ $d['label'] }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
                @error('disposition')
                    <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Notes --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-sticky me-2 text-muted"></i>Additional Notes
                <span class="text-muted fw-normal small">(optional)</span>
            </div>
            <div class="card-body">
                <textarea name="notes" rows="2"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Any other notes, follow-up instructions…">{{ old('notes', $patientLog->notes) }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

    </div>

</div>{{-- /.row --}}

{{-- Footer Submit --}}
<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('patient-logs.show', $patientLog) }}" class="btn btn-outline-secondary px-4">
        Cancel
    </a>
    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
        <i class="bi bi-floppy-fill me-2"></i>Save Changes
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
const patientSelect = document.getElementById('patientSelect');
const patientInfo   = document.getElementById('patientInfo');
const patientInfoTx = document.getElementById('patientInfoText');

patientSelect.addEventListener('change', updatePatientInfo);

function updatePatientInfo() {
    const opt = patientSelect.selectedOptions[0];
    if (!opt || !opt.value) { patientInfo.classList.add('d-none'); return; }
    const cat = opt.dataset.category || '—';
    const yr  = opt.dataset.year     || '';
    const sec = opt.dataset.section  || '';
    patientInfoTx.textContent = `${cat}${yr ? ' · ' + yr : ''}${sec ? ' · ' + sec : ''}`;
    patientInfo.classList.remove('d-none');
}

document.querySelectorAll('.complaint-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const ta = document.getElementById('chiefComplaint');
        const existing = ta.value.trim();
        ta.value = existing ? existing + ', ' + this.textContent.trim() : this.textContent.trim();
    });
});

if (patientSelect.value) updatePatientInfo();
</script>
@endpush

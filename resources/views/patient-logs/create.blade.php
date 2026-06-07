@extends('layouts.app')

@section('title', 'Log a Patient Visit')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-journal-plus me-2 text-primary"></i>Log a Patient Visit
        </h4>
        <p class="text-muted small mb-0">Record a clinic visit. This is the main daily log for the school clinic.</p>
    </div>
    <a href="{{ route('patient-logs.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Logbook
    </a>
</div>

<form method="POST" action="{{ route('patient-logs.store') }}" id="logForm" novalidate>
@csrf

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
                        <option value="">— Search by name or patient no. —</option>
                        @foreach($patients as $p)
                        <option value="{{ $p->id }}"
                                data-guardian="{{ $p->guardian_name }}"
                                data-guardian-contact="{{ $p->guardian_contact }}"
                                data-category="{{ ucwords(str_replace('_', ' ', $p->category)) }}"
                                data-year="{{ $p->year_level }}"
                                data-section="{{ $p->section }}"
                                {{ old('patient_id', $selectedPatient) == $p->id ? 'selected' : '' }}>
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

                {{-- Guardian info --}}
                <div id="guardianInfo" class="d-none">
                    <div class="form-text fw-semibold text-success">
                        <i class="bi bi-people-fill me-1"></i>Guardian: <span id="guardianName">—</span>
                    </div>
                    <div class="form-text text-muted" id="guardianContact">No guardian contact on record.</div>
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
                           value="{{ old('log_date', today()->toDateString()) }}"
                           max="{{ today()->toDateString() }}" required>
                    @error('log_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-2">
                    <div class="col">
                        <label class="form-label fw-semibold">Time In <span class="text-danger">*</span></label>
                        <input type="time" name="time_in"
                               class="form-control @error('time_in') is-invalid @enderror"
                               value="{{ old('time_in', now()->format('H:i')) }}" required>
                        @error('time_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col">
                        <label class="form-label fw-semibold">Time Out</label>
                        <input type="time" name="time_out"
                               class="form-control @error('time_out') is-invalid @enderror"
                               value="{{ old('time_out') }}"
                               placeholder="optional">
                        @error('time_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Vitals (collapsible) --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="bi bi-thermometer-half me-2 text-warning"></i>Vital Signs</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0"
                        data-bs-toggle="collapse" data-bs-target="#vitalsPanel">
                    <i class="bi bi-chevron-down"></i> Optional
                </button>
            </div>
            <div class="collapse" id="vitalsPanel">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Temp (°C)</label>
                            <input type="number" name="vital_temp" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="e.g. 37.2"
                                   value="{{ old('vital_temp') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Blood Pressure</label>
                            <input type="text" name="vital_bp"
                                   class="form-control form-control-sm"
                                   placeholder="e.g. 120/80"
                                   value="{{ old('vital_bp') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Pulse (bpm)</label>
                            <input type="number" name="vital_pulse"
                                   class="form-control form-control-sm"
                                   placeholder="72"
                                   value="{{ old('vital_pulse') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Weight (kg)</label>
                            <input type="number" name="vital_weight" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="50"
                                   value="{{ old('vital_weight') }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Height (cm)</label>
                            <input type="number" name="vital_height" step="0.1"
                                   class="form-control form-control-sm"
                                   placeholder="160"
                                   value="{{ old('vital_height') }}">
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

                {{-- Quick complaint buttons --}}
                <div class="mb-2">
                    <div class="form-label fw-semibold small">Quick Complaints</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['Headache','Fever','Stomachache','Toothache','Cough & Colds','Dizziness','Wound / Injury','Chest Pain','Vomiting','Diarrhea','Fainting','Eye Pain','Ear Pain','Allergic Reaction','Menstrual Cramps','Body Pain','Asthma Attack','High Blood Pressure'] as $c)
                        <button type="button" class="btn btn-sm btn-outline-secondary complaint-btn">
                            {{ $c }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label fw-semibold">
                        Chief Complaint <span class="text-danger">*</span>
                    </label>
                    <textarea name="chief_complaint" id="chiefComplaint" rows="3"
                              class="form-control @error('chief_complaint') is-invalid @enderror"
                              placeholder="Describe the patient's main reason for visiting the clinic…"
                              required>{{ old('chief_complaint') }}</textarea>
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
                              placeholder="Physical findings, observations…">{{ old('assessment') }}</textarea>
                    @error('assessment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label fw-semibold">Treatment / Action Taken</label>
                    <textarea name="treatment" rows="2"
                              class="form-control @error('treatment') is-invalid @enderror"
                              placeholder="Medicines given, first aid done, advice given…">{{ old('treatment') }}</textarea>
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
                        'rest_in_clinic'       => ['label' => 'Rest in Clinic',         'icon' => 'bi-hospital',      'color' => 'info'],
                        'returned_to_class'    => ['label' => 'Returned to Class / Work','icon' => 'bi-mortarboard',   'color' => 'success'],
                        'sent_home'            => ['label' => 'Sent Home',               'icon' => 'bi-house-heart',   'color' => 'warning'],
                        'referred_to_hospital' => ['label' => 'Referred to Hospital',    'icon' => 'bi-hospital-fill', 'color' => 'danger'],
                        'further_observation'  => ['label' => 'Under Observation',       'icon' => 'bi-eye-fill',      'color' => 'secondary'],
                    ];
                @endphp
                <div class="row g-2">
                    @foreach($dispositions as $val => $d)
                    <div class="col-sm-4 col-6">
                        <input type="radio" class="btn-check" name="disposition"
                               id="disp_{{ $val }}" value="{{ $val }}"
                               {{ old('disposition', 'rest_in_clinic') === $val ? 'checked' : '' }}>
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

        {{-- SMS to Guardian --}}
        <div class="card border-0 shadow-sm mb-3" id="smsCard">
            <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="bi bi-phone me-2 text-info"></i>Notify Guardian via SMS</span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox"
                           name="sms_guardian" id="smsToggle" value="1"
                           {{ old('sms_guardian') ? 'checked' : '' }}>
                    <label class="form-check-label" for="smsToggle">Send SMS</label>
                </div>
            </div>
            <div class="collapse {{ old('sms_guardian') ? 'show' : '' }}" id="smsPanel">
                <div class="card-body">
                    <div id="smsGuardianPreview" class="alert alert-light border small">
                        <div class="mb-1 fw-semibold text-muted">SMS will be sent to guardian:</div>
                        <span id="smsPreviewText" class="fst-italic text-body">
                            Select a patient above to see the SMS preview.
                        </span>
                    </div>
                    <div class="form-text text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        The guardian's phone number on the patient record will be used.
                        If no guardian contact is on record, no SMS will be sent.
                    </div>
                </div>
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
                          placeholder="Any other notes, follow-up instructions…">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

    </div>

</div>{{-- /.row --}}

{{-- Footer Submit --}}
<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ route('patient-logs.index') }}" class="btn btn-outline-secondary px-4">
        Cancel
    </a>
    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
        <i class="bi bi-floppy-fill me-2"></i>Save Log Entry
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
const patients = {!! json_encode($patients->keyBy('id')) !!};

const patientSelect = document.getElementById('patientSelect');
const patientInfo   = document.getElementById('patientInfo');
const patientInfoTx = document.getElementById('patientInfoText');
const guardianInfo  = document.getElementById('guardianInfo');
const guardianName  = document.getElementById('guardianName');
const guardianCont  = document.getElementById('guardianContact');
const smsToggle     = document.getElementById('smsToggle');
const smsPanel      = document.getElementById('smsPanel');
const smsPreview    = document.getElementById('smsPreviewText');
const complaintTa   = document.getElementById('chiefComplaint');

// ── Patient change → update guardian info & SMS preview ──────────────────
patientSelect.addEventListener('change', updatePatientInfo);
complaintTa.addEventListener('input', updateSmsPreview);
document.querySelectorAll('input[name="treatment"]').forEach(el => el.addEventListener('input', updateSmsPreview));

function updatePatientInfo() {
    const id = patientSelect.value;
    if (! id) {
        patientInfo.classList.add('d-none');
        guardianInfo.classList.add('d-none');
        return;
    }

    const opt = patientSelect.selectedOptions[0];
    const cat  = opt.dataset.category || '—';
    const yr   = opt.dataset.year     || '';
    const sec  = opt.dataset.section  || '';

    patientInfoTx.textContent = `${cat}${yr ? ' · ' + yr : ''}${sec ? ' · ' + sec : ''}`;
    patientInfo.classList.remove('d-none');

    const gName = opt.dataset.guardian  || '';
    const gCont = opt.dataset.guardianContact || '';

    if (gName || gCont) {
        guardianName.textContent = gName || '—';
        guardianCont.textContent = gCont ? `📞 ${gCont}` : 'No contact number on record.';
        guardianInfo.classList.remove('d-none');
    } else {
        guardianInfo.classList.add('d-none');
    }

    updateSmsPreview();
}

function updateSmsPreview() {
    const opt = patientSelect.selectedOptions[0];
    if (!opt || !opt.value) return;

    const gName    = opt.dataset.guardian  || 'Parent/Guardian';
    const fName    = (opt.text.split(',')[1] || '').trim().split('—')[0].trim().split(' ')[0];
    const timeIn   = document.querySelector('[name="time_in"]').value;
    const timeFmt  = timeIn ? formatTime(timeIn) : '—';
    const complaint= complaintTa.value || '—';
    const treatment= document.querySelector('[name="treatment"]')?.value || 'Attended by clinic staff';

    smsPreview.textContent = `Dear ${gName}, your ward ${fName} visited the school clinic at ${timeFmt} for ${complaint}. Action taken: ${treatment}. - Clinovia`;
}

function formatTime(t) {
    const [h, m] = t.split(':');
    const ampm   = +h >= 12 ? 'PM' : 'AM';
    return `${(+h % 12) || 12}:${m} ${ampm}`;
}

// ── SMS toggle ────────────────────────────────────────────────────────────
smsToggle.addEventListener('change', function () {
    if (this.checked) {
        new bootstrap.Collapse(smsPanel, { show: true });
        updateSmsPreview();
    } else {
        new bootstrap.Collapse(smsPanel, { hide: true });
    }
});

// ── Quick complaint buttons ───────────────────────────────────────────────
document.querySelectorAll('.complaint-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const existing = complaintTa.value.trim();
        complaintTa.value = existing
            ? existing + ', ' + this.textContent.trim()
            : this.textContent.trim();
        complaintTa.dispatchEvent(new Event('input'));
    });
});

// ── Init on load (if old values present) ─────────────────────────────────
if (patientSelect.value) updatePatientInfo();
</script>
@endpush

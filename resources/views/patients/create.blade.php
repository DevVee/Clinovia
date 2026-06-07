@extends('layouts.app')

@section('title', 'New Patient')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2 text-primary"></i>New Patient</h4>
        <p class="text-muted mb-0 small">Register a new patient record — fill in each section then click Next</p>
    </div>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Patients
    </a>
</div>

<form method="POST" action="{{ route('patients.store') }}" id="patientForm" novalidate>
@csrf

<div class="card border-0 shadow-sm">

    {{-- Step progress indicator --}}
    <div class="card-header bg-white pt-4 pb-0 border-0">

        {{-- Progress bar --}}
        <div class="d-flex align-items-center gap-0 mb-4 px-2">
            @php
                $steps = [
                    ['id'=>'personal', 'label'=>'Personal', 'icon'=>'bi-person'],
                    ['id'=>'academic', 'label'=>'Academic', 'icon'=>'bi-mortarboard'],
                    ['id'=>'guardian', 'label'=>'Guardian', 'icon'=>'bi-house-heart'],
                    ['id'=>'medical',  'label'=>'Medical',  'icon'=>'bi-heart-pulse'],
                ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="wizard-step {{ $i === 0 ? 'active' : '' }}" data-step="{{ $step['id'] }}" id="wizard-step-{{ $step['id'] }}">
                <div class="wizard-dot"><i class="bi {{ $step['icon'] }}"></i></div>
                <div class="wizard-label">{{ $step['label'] }}</div>
            </div>
            @if(!$loop->last)
            <div class="wizard-line" id="wizard-line-{{ $step['id'] }}"></div>
            @endif
            @endforeach
        </div>

        {{-- Bootstrap tab nav (hidden — driven by wizard JS) --}}
        <ul class="nav nav-tabs d-none" id="patientTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal" type="button">Personal</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#academic" type="button">Academic</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#guardian" type="button">Guardian</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#medical"  type="button">Medical</button></li>
        </ul>
    </div>

    <div class="card-body px-4 py-3">
        <div class="tab-content" id="patientTabsContent">

            {{-- STEP 1: PERSONAL --}}
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                @if($errors->hasAny(['category','first_name','last_name','sex','birthdate','contact_number','email','address','emergency_contact_name','emergency_contact_number']))
                    <div class="alert alert-danger small py-2"><i class="bi bi-exclamation-triangle me-1"></i>Please fix the errors on this step before continuing.</div>
                @endif
                @include('patients.partials.form-personal')
            </div>

            {{-- STEP 2: ACADEMIC --}}
            <div class="tab-pane fade" id="academic" role="tabpanel">
                @include('patients.partials.form-academic')
            </div>

            {{-- STEP 3: GUARDIAN --}}
            <div class="tab-pane fade" id="guardian" role="tabpanel">
                @include('patients.partials.form-guardian')
            </div>

            {{-- STEP 4: MEDICAL --}}
            <div class="tab-pane fade" id="medical" role="tabpanel">
                @include('patients.partials.form-medical')
            </div>

        </div>
    </div>

    {{-- Wizard Navigation Footer --}}
    <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between px-4 py-3">

        {{-- Back button (hidden on step 1) --}}
        <button type="button" class="btn btn-outline-secondary" id="wizardBack" style="display:none!important;">
            <i class="bi bi-chevron-left me-1"></i>Back
        </button>
        <div id="wizardBackPlaceholder"></div>

        {{-- Step label --}}
        <span class="text-muted small fw-semibold" id="wizardStepLabel">Step 1 of 4 — Personal Information</span>

        {{-- Next / Save --}}
        <div>
            <button type="button" class="btn btn-primary px-4" id="wizardNext">
                Next <i class="bi bi-chevron-right ms-1"></i>
            </button>
            <button type="submit" class="btn btn-success px-5" id="wizardSave" style="display:none!important;">
                <i class="bi bi-floppy-fill me-1"></i>Save Patient
            </button>
        </div>

    </div>
</div>

</form>

@push('styles')
<style>
/* Address Picker */
.address-picker-wrap .input-group .form-select:disabled {
    background-color: #f8f9fa;
    color: #adb5bd;
    cursor: not-allowed;
}
.address-picker-wrap .input-group-text {
    border-right: 0;
}
.address-picker-wrap .form-select {
    border-radius: 0 6px 6px 0 !important;
}
#pat-preview, #grd-preview {
    font-size: .82rem;
    margin-top: .35rem;
}

/* Wizard */
.wizard-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex-shrink: 0;
}
.wizard-dot {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    border: 2px solid #dee2e6;
    transition: all .25s;
}
.wizard-step.active .wizard-dot,
.wizard-step.done .wizard-dot {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}
.wizard-step.done .wizard-dot { background: var(--bs-success); border-color: var(--bs-success); }
.wizard-label {
    font-size: .7rem;
    font-weight: 600;
    margin-top: 4px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.wizard-step.active .wizard-label { color: var(--bs-primary); }
.wizard-step.done  .wizard-label { color: var(--bs-success); }
.wizard-line {
    flex: 1;
    height: 2px;
    background: #dee2e6;
    margin: 0 6px;
    margin-bottom: 20px;
    transition: background .25s;
}
.wizard-line.done { background: var(--bs-success); }
</style>
@endpush

@push('scripts')
<script>
/* ── Philippine Address Picker (PSGC Cloud API) ──────────────────────────── */
(function () {
    const BASE = 'https://psgc.cloud/api';

    function buildPicker(prefix) {
        const sel = id => document.getElementById(prefix + '-' + id);
        const regionSel   = sel('region');
        const provinceSel = sel('province');
        const citySel     = sel('city');
        const barangaySel = sel('barangay');
        const streetIn    = sel('street');
        const hiddenIn    = sel('hidden');
        const preview     = sel('preview');
        const previewTxt  = sel('preview-text');

        if (!regionSel) return;   // picker not on this page

        // ── helpers ────────────────────────────────────────────────────────
        function resetSelect(el, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            el.disabled = true;
        }

        function populate(el, items, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            items.sort((a, b) => a.name.localeCompare(b.name))
                 .forEach(item => {
                     const opt = document.createElement('option');
                     opt.value = item.code;
                     opt.dataset.name = item.name;
                     opt.textContent = item.name;
                     el.appendChild(opt);
                 });
            el.disabled = false;
        }

        async function apiFetch(path) {
            try {
                const r = await fetch(BASE + path);
                if (!r.ok) throw new Error(r.status);
                return await r.json();
            } catch (e) {
                console.warn('PSGC API error:', path, e);
                return [];
            }
        }

        function setLoading(el, msg) {
            el.innerHTML = `<option value="">${msg}</option>`;
            el.disabled = true;
        }

        function compose() {
            const street   = (streetIn?.value || '').trim();
            const brgyOpt  = barangaySel.selectedOptions[0];
            const cityOpt  = citySel.selectedOptions[0];
            const provOpt  = provinceSel.selectedOptions[0];
            const regOpt   = regionSel.selectedOptions[0];

            const brgy  = brgyOpt?.dataset.name  || '';
            const city  = cityOpt?.dataset.name  || '';
            const prov  = provOpt?.dataset.name  || '';
            const reg   = regOpt?.dataset.name   || '';

            if (!brgy && !city) {
                // User hasn't selected anything — keep existing value untouched
                if (preview) preview.classList.add('d-none');
                return;
            }

            const parts = [street, brgy ? 'Brgy. ' + brgy : '', city, prov, reg]
                .filter(Boolean).join(', ');

            hiddenIn.value = parts;
            if (previewTxt) previewTxt.textContent = parts;
            if (preview) preview.classList.remove('d-none');
        }

        // ── Load regions on init ───────────────────────────────────────────
        (async () => {
            setLoading(regionSel, 'Loading regions…');
            regionSel.disabled = false;
            const regions = await apiFetch('/regions');
            populate(regionSel, regions, '— Region —');
        })();

        // ── Region → Provinces ─────────────────────────────────────────────
        regionSel.addEventListener('change', async function () {
            resetSelect(provinceSel, '— Province —');
            resetSelect(citySel,     '— City / Municipality —');
            resetSelect(barangaySel, '— Barangay —');
            if (!this.value) { compose(); return; }

            setLoading(provinceSel, 'Loading provinces…');
            const data = await apiFetch(`/regions/${this.value}/provinces`);
            populate(provinceSel, data, '— Province —');
            compose();
        });

        // ── Province → Cities + Municipalities ────────────────────────────
        provinceSel.addEventListener('change', async function () {
            resetSelect(citySel,     '— City / Municipality —');
            resetSelect(barangaySel, '— Barangay —');
            if (!this.value) { compose(); return; }

            setLoading(citySel, 'Loading cities…');
            const [cities, munis] = await Promise.all([
                apiFetch(`/provinces/${this.value}/cities`),
                apiFetch(`/provinces/${this.value}/municipalities`),
            ]);
            populate(citySel, [...cities, ...munis], '— City / Municipality —');
            compose();
        });

        // ── City/Municipality → Barangays ─────────────────────────────────
        citySel.addEventListener('change', async function () {
            resetSelect(barangaySel, '— Barangay —');
            if (!this.value) { compose(); return; }

            const opt      = this.selectedOptions[0];
            const isCity   = opt?.textContent?.toLowerCase().includes('city') ||
                             opt?.textContent?.toLowerCase().startsWith('city');

            setLoading(barangaySel, 'Loading barangays…');

            // Try city endpoint first; fall back to municipality
            let data = await apiFetch(`/cities/${this.value}/barangays`);
            if (!data.length) {
                data = await apiFetch(`/municipalities/${this.value}/barangays`);
            }
            populate(barangaySel, data, '— Barangay —');
            compose();
        });

        // ── Barangay / Street change → compose ────────────────────────────
        barangaySel.addEventListener('change', compose);
        if (streetIn) streetIn.addEventListener('input', compose);
    }

    // Init both pickers
    buildPicker('pat');
    buildPicker('grd');
})();
/* ── End Address Picker ──────────────────────────────────────────────────── */

(function () {
    const stepIds    = ['personal','academic','guardian','medical'];
    const stepLabels = ['Personal Information','Academic Information','Guardian / Parent','Medical Information'];
    let current      = 0;

    // If validation failed, jump to first tab with errors
    current = {{ \Illuminate\Support\Arr::first([
        $errors->hasAny(['category','first_name','last_name','sex','birthdate','contact_number','email','address','emergency_contact_name','emergency_contact_number']) ? 0 : null,
        $errors->hasAny(['year_level','program_strand','section']) ? 1 : null,
        $errors->hasAny(['guardian_name','guardian_relationship','guardian_contact','guardian_address']) ? 2 : null,
        $errors->hasAny(['blood_type','allergies','medical_conditions','notes']) ? 3 : null,
    ], fn($v) => $v !== null) ?? 0 }};

    const tabs  = stepIds.map(id => document.querySelector(`[data-bs-target="#${id}"]`));
    const panes = stepIds.map(id => document.getElementById(id));
    const steps = stepIds.map(id => document.getElementById(`wizard-step-${id}`));
    const lines = stepIds.slice(0,-1).map(id => document.getElementById(`wizard-line-${id}`));

    const btnBack  = document.getElementById('wizardBack');
    const btnNext  = document.getElementById('wizardNext');
    const btnSave  = document.getElementById('wizardSave');
    const lblStep  = document.getElementById('wizardStepLabel');
    const backPH   = document.getElementById('wizardBackPlaceholder');

    function go(idx) {
        // Activate tab
        tabs[idx].click();
        current = idx;
        render();
    }

    function render() {
        // Dots
        steps.forEach((s, i) => {
            s.classList.remove('active','done');
            if (i === current) s.classList.add('active');
            else if (i < current) s.classList.add('done');
        });
        // Lines
        lines.forEach((l, i) => {
            l.classList.toggle('done', i < current);
        });
        // Buttons
        const isFirst = current === 0;
        const isLast  = current === stepIds.length - 1;

        btnBack.style.setProperty('display', isFirst ? 'none' : 'inline-flex', 'important');
        backPH.style.display = isFirst ? 'block' : 'none';

        btnNext.style.setProperty('display', isLast ? 'none' : 'inline-flex', 'important');
        btnSave.style.setProperty('display', isLast ? 'inline-flex' : 'none', 'important');

        lblStep.textContent = `Step ${current + 1} of ${stepIds.length} — ${stepLabels[current]}`;
    }

    btnNext.addEventListener('click', () => { if (current < stepIds.length - 1) go(current + 1); });
    btnBack.addEventListener('click', () => { if (current > 0) go(current - 1); });

    // Init
    go(current);
})();
</script>
@endpush
@endsection

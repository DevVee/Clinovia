@extends('layouts.app')

@section('title', 'Edit Patient — ' . $patient->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patient->full_name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Patient
        </h4>
        <p class="text-muted mb-0 small">
            <span class="font-monospace">{{ $patient->patient_number }}</span> &mdash; {{ $patient->full_name }}
        </p>
    </div>
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Profile
    </a>
</div>

<form method="POST" action="{{ route('patients.update', $patient) }}" novalidate>
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-tab"
                            data-bs-toggle="tab" data-bs-target="#personal"
                            type="button" role="tab">
                        <i class="bi bi-person me-1"></i>Personal
                        @if ($errors->hasAny(['category','first_name','last_name','sex','birthdate','contact_number','email','address','emergency_contact_name','emergency_contact_number']))
                            <span class="badge bg-danger ms-1">!</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="academic-tab"
                            data-bs-toggle="tab" data-bs-target="#academic"
                            type="button" role="tab">
                        <i class="bi bi-mortarboard me-1"></i>Academic
                        @if ($errors->hasAny(['year_level','program_strand','section']))
                            <span class="badge bg-danger ms-1">!</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="guardian-tab"
                            data-bs-toggle="tab" data-bs-target="#guardian"
                            type="button" role="tab">
                        <i class="bi bi-house-heart me-1"></i>Guardian
                        @if ($errors->hasAny(['guardian_name','guardian_relationship','guardian_contact','guardian_address']))
                            <span class="badge bg-danger ms-1">!</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="medical-tab"
                            data-bs-toggle="tab" data-bs-target="#medical"
                            type="button" role="tab">
                        <i class="bi bi-heart-pulse me-1"></i>Medical
                        @if ($errors->hasAny(['blood_type','allergies','medical_conditions','notes']))
                            <span class="badge bg-danger ms-1">!</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                    @include('patients.partials.form-personal')
                </div>
                <div class="tab-pane fade" id="academic" role="tabpanel">
                    @include('patients.partials.form-academic')
                </div>
                <div class="tab-pane fade" id="guardian" role="tabpanel">
                    @include('patients.partials.form-guardian')
                </div>
                <div class="tab-pane fade" id="medical" role="tabpanel">
                    @include('patients.partials.form-medical')
                </div>
            </div>
        </div>

        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active"
                       id="isActive" value="1"
                       {{ old('is_active', $patient->is_active) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="isActive">Active Patient</label>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>

</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabsWithErrors = document.querySelectorAll('.nav-link .badge.bg-danger');
    if (tabsWithErrors.length > 0) {
        tabsWithErrors[0].closest('.nav-link').click();
    }
});

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

        if (!regionSel) return;

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
            const street  = (streetIn?.value || '').trim();
            const brgyOpt = barangaySel.selectedOptions[0];
            const cityOpt = citySel.selectedOptions[0];
            const provOpt = provinceSel.selectedOptions[0];
            const regOpt  = regionSel.selectedOptions[0];

            const brgy = brgyOpt?.dataset.name || '';
            const city = cityOpt?.dataset.name || '';
            const prov = provOpt?.dataset.name || '';
            const reg  = regOpt?.dataset.name  || '';

            if (!brgy && !city) {
                if (preview) preview.classList.add('d-none');
                return;
            }

            const parts = [street, brgy ? 'Brgy. ' + brgy : '', city, prov, reg]
                .filter(Boolean).join(', ');

            hiddenIn.value = parts;
            if (previewTxt) previewTxt.textContent = parts;
            if (preview) preview.classList.remove('d-none');
        }

        (async () => {
            setLoading(regionSel, 'Loading regions…');
            regionSel.disabled = false;
            const regions = await apiFetch('/regions');
            populate(regionSel, regions, '— Region —');
        })();

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

        citySel.addEventListener('change', async function () {
            resetSelect(barangaySel, '— Barangay —');
            if (!this.value) { compose(); return; }
            setLoading(barangaySel, 'Loading barangays…');
            let data = await apiFetch(`/cities/${this.value}/barangays`);
            if (!data.length) data = await apiFetch(`/municipalities/${this.value}/barangays`);
            populate(barangaySel, data, '— Barangay —');
            compose();
        });

        barangaySel.addEventListener('change', compose);
        if (streetIn) streetIn.addEventListener('input', compose);
    }

    buildPicker('pat');
    buildPicker('grd');
})();
/* ── End Address Picker ──────────────────────────────────────────────────── */
</script>
@endpush

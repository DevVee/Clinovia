{{-- Guardian / Parent Information Tab --}}
<div class="row g-3">

    <div class="col-12">
        <div class="alert alert-info alert-sm d-flex align-items-center gap-2 py-2" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            Required for minor students (Kinder, Daycare, Elementary, Junior High). Optional for others.
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Guardian / Parent Name</label>
        <input type="text" name="guardian_name"
               class="form-control @error('guardian_name') is-invalid @enderror"
               value="{{ old('guardian_name', $patient->guardian_name ?? '') }}">
        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Relationship</label>
        <input type="text" name="guardian_relationship" placeholder="e.g. Mother, Father, Legal Guardian"
               class="form-control @error('guardian_relationship') is-invalid @enderror"
               value="{{ old('guardian_relationship', $patient->guardian_relationship ?? '') }}">
        @error('guardian_relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Guardian Contact Number</label>
        <input type="text" name="guardian_contact" placeholder="09XXXXXXXXX"
               class="form-control @error('guardian_contact') is-invalid @enderror"
               value="{{ old('guardian_contact', $patient->guardian_contact ?? '') }}">
        @error('guardian_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        @include('patients.partials.address-picker', [
            'addrField'  => 'guardian_address',
            'addrValue'  => $patient->guardian_address ?? '',
            'addrLabel'  => 'Guardian Address',
            'addrPrefix' => 'grd',
        ])
        @error('guardian_address')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

</div>

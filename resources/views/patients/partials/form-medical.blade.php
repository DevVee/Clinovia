{{-- Medical Information Tab --}}
<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">Blood Type</label>
        <select name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
            <option value="">— Unknown —</option>
            @foreach ($bloodTypes as $bt)
                <option value="{{ $bt }}"
                    {{ old('blood_type', $patient->blood_type ?? '') === $bt ? 'selected' : '' }}>
                    {{ $bt }}
                </option>
            @endforeach
        </select>
        @error('blood_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">
            <i class="bi bi-exclamation-triangle text-warning me-1"></i>Known Allergies
        </label>
        <textarea name="allergies" rows="3"
                  class="form-control @error('allergies') is-invalid @enderror"
                  placeholder="List any known drug, food, or environmental allergies...">{{ old('allergies', $patient->allergies ?? '') }}</textarea>
        @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">
            <i class="bi bi-heart-pulse text-danger me-1"></i>Existing Medical Conditions
        </label>
        <textarea name="medical_conditions" rows="3"
                  class="form-control @error('medical_conditions') is-invalid @enderror"
                  placeholder="e.g. Asthma, Hypertension, Diabetes...">{{ old('medical_conditions', $patient->medical_conditions ?? '') }}</textarea>
        @error('medical_conditions')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Additional Notes</label>
        <textarea name="notes" rows="3"
                  class="form-control @error('notes') is-invalid @enderror"
                  placeholder="Any other relevant health information...">{{ old('notes', $patient->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>

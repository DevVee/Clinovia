{{-- Personal Information Tab --}}
<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="">— Select Category —</option>
            @foreach ($categoryLabels as $value => $label)
                <option value="{{ $value }}"
                    {{ old('category', $patient->category ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name"
               class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name', $patient->first_name ?? '') }}" required>
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Middle Name</label>
        <input type="text" name="middle_name"
               class="form-control @error('middle_name') is-invalid @enderror"
               value="{{ old('middle_name', $patient->middle_name ?? '') }}">
        @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name"
               class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name', $patient->last_name ?? '') }}" required>
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-2">
        <label class="form-label fw-semibold">Suffix</label>
        <input type="text" name="suffix" placeholder="Jr., Sr., III"
               class="form-control @error('suffix') is-invalid @enderror"
               value="{{ old('suffix', $patient->suffix ?? '') }}">
        @error('suffix')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Sex <span class="text-danger">*</span></label>
        <select name="sex" class="form-select @error('sex') is-invalid @enderror" required>
            <option value="">— Select —</option>
            <option value="male"   {{ old('sex', $patient->sex ?? '') === 'male'   ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('sex', $patient->sex ?? '') === 'female' ? 'selected' : '' }}>Female</option>
        </select>
        @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label fw-semibold">Birthdate <span class="text-danger">*</span></label>
        <input type="date" name="birthdate"
               class="form-control @error('birthdate') is-invalid @enderror"
               value="{{ old('birthdate', isset($patient->birthdate) ? $patient->birthdate->format('Y-m-d') : '') }}"
               max="{{ now()->subDay()->format('Y-m-d') }}" required>
        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Contact Number</label>
        <input type="text" name="contact_number" placeholder="09XXXXXXXXX"
               class="form-control @error('contact_number') is-invalid @enderror"
               value="{{ old('contact_number', $patient->contact_number ?? '') }}">
        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Email Address</label>
        <input type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $patient->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        @include('patients.partials.address-picker', [
            'addrField'  => 'address',
            'addrValue'  => $patient->address ?? '',
            'addrLabel'  => 'Home Address',
            'addrPrefix' => 'pat',
        ])
        @error('address')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Emergency Contact Name</label>
        <input type="text" name="emergency_contact_name"
               class="form-control @error('emergency_contact_name') is-invalid @enderror"
               value="{{ old('emergency_contact_name', $patient->emergency_contact_name ?? '') }}">
        @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Emergency Contact Number</label>
        <input type="text" name="emergency_contact_number" placeholder="09XXXXXXXXX"
               class="form-control @error('emergency_contact_number') is-invalid @enderror"
               value="{{ old('emergency_contact_number', $patient->emergency_contact_number ?? '') }}">
        @error('emergency_contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>

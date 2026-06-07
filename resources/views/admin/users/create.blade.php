@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Add New User</h4>
        <p class="text-muted small mb-0">Create a Clinovia account and assign a role</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-person-plus text-primary me-2"></i> User Details
            </div>
            <div class="card-body p-4">

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. Juan Dela Cruz"
                                   required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="user@email.com"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Password --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min 8 characters"
                                       required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       class="form-control"
                                       placeholder="Re-enter password"
                                       required>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Role & Status --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <label for="role" class="form-label fw-semibold small">Role <span class="text-danger">*</span></label>
                            <select id="role" name="role"
                                    class="form-select @error('role') is-invalid @enderror"
                                    required>
                                <option value="">— Select a role —</option>
                                @foreach($roles as $r)
                                <option value="{{ $r->name }}" @selected(old('role') === $r->name)>
                                    {{ ucfirst($r->name) }}
                                </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5 d-flex flex-column">
                            <label class="form-label fw-semibold small">Account Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', '1') == '1')>
                                <label class="form-check-label" for="is_active">Active (can sign in)</label>
                            </div>
                        </div>
                    </div>

                    {{-- Role description panel --}}
                    <div id="roleInfoPanel" class="alert alert-info py-2 small mb-4 d-none">
                        <i class="bi bi-info-circle me-1"></i>
                        <span id="roleInfoText"></span>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-person-plus me-1"></i> Create User
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const roleDescriptions = {
    administrator: 'Full system access — can manage users, roles, settings, and all clinical modules.',
    nurse: 'Clinical access — can manage patients, consultations, medicines, dispensing, and reports.',
    staff: 'Limited access — can view patients and create appointments only.',
};

document.getElementById('role').addEventListener('change', function () {
    const panel = document.getElementById('roleInfoPanel');
    const text  = document.getElementById('roleInfoText');
    const desc  = roleDescriptions[this.value];
    if (desc) {
        text.textContent = desc;
        panel.classList.remove('d-none');
    } else {
        panel.classList.add('d-none');
    }
});
</script>
@endpush
@endsection

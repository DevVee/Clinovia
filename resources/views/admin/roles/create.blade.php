@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Create New Role</h4>
        <p class="text-muted small mb-0">Define a role name and select which permissions it grants</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="row g-4">

        {{-- Role name card --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold">
                    <i class="bi bi-tag text-primary me-2"></i> Role Identity
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="name" class="form-label fw-semibold small">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. pharmacist"
                                   pattern="[a-z0-9_\-]+"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Lowercase letters, numbers, hyphens only (e.g. <code>head-nurse</code>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions matrix --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-key text-warning me-2"></i> Permissions</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleAll(true)">
                            <i class="bi bi-check-all me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAll(false)">
                            <i class="bi bi-x me-1"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($permissions as $group => $perms)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold small text-uppercase text-muted mb-2"
                                     style="font-size:.7rem;letter-spacing:.06em;">
                                    <i class="bi bi-folder me-1"></i>{{ str_replace('-', ' ', $group) }}
                                </div>
                                @foreach($perms as $perm)
                                <div class="form-check form-check-sm mb-1">
                                    <input class="form-check-input perm-check"
                                           type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perm->name }}"
                                           id="perm_{{ $loop->parent->index }}_{{ $loop->index }}"
                                           @checked(in_array($perm->name, old('permissions', [])))>
                                    <label class="form-check-label small"
                                           for="perm_{{ $loop->parent->index }}_{{ $loop->index }}">
                                        @php $verb = explode('-', $perm->name)[0]; @endphp
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis me-1"
                                              style="font-size:.65rem;">{{ $verb }}</span>
                                        {{ ucwords(str_replace('-', ' ', $perm->name)) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-plus-circle me-1"></i> Create Role
        </button>
    </div>
</form>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
}
</script>
@endpush
@endsection

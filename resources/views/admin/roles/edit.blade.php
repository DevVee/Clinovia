@extends('layouts.app')

@section('title', 'Edit Role — ' . ucfirst($role->name))

@section('content')
@php
    $isSystem = in_array($role->name, ['administrator', 'nurse', 'staff']);
    $roleColor = match($role->name) {
        'administrator' => 'danger',
        'nurse'         => 'success',
        'staff'         => 'info',
        default         => 'primary',
    };
    $currentPerms = $role->permissions->pluck('name')->toArray();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            Edit Role &mdash;
            <span class="badge text-bg-{{ $roleColor }}">{{ ucfirst($role->name) }}</span>
            @if($isSystem)
                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1 small">System Role</span>
            @endif
        </h4>
        <p class="text-muted small mb-0">
            {{ $role->permissions->count() }} permissions currently assigned
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

@if($isSystem)
<div class="alert alert-warning py-2 small mb-4">
    <i class="bi bi-shield-exclamation me-1"></i>
    <strong>System role:</strong> The role name cannot be changed. You may adjust its permissions.
</div>
@endif

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf @method('PUT')

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center justify-content-between">
            <span><i class="bi bi-key text-warning me-2"></i> Permissions for <strong>{{ $role->name }}</strong></span>
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
                @php
                    $groupChecked = collect($perms)->every(fn($p) => in_array($p->name, $currentPerms));
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="border rounded p-3 h-100 @if($groupChecked) border-{{ $roleColor }} bg-{{ $roleColor }}-subtle @endif"
                         style="transition:all .2s;">
                        {{-- Group header with "select group" checkbox --}}
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input type="checkbox"
                                   class="form-check-input group-toggle"
                                   id="grp_{{ $loop->index }}"
                                   data-group="{{ $loop->index }}"
                                   @checked($groupChecked)
                                   onclick="toggleGroup({{ $loop->index }}, this.checked)">
                            <label for="grp_{{ $loop->index }}"
                                   class="fw-semibold small text-uppercase text-muted mb-0"
                                   style="font-size:.7rem;letter-spacing:.06em;cursor:pointer;">
                                {{ str_replace('-', ' ', $group) }}
                            </label>
                        </div>

                        @foreach($perms as $perm)
                        <div class="form-check form-check-sm mb-1">
                            <input class="form-check-input perm-check grp-{{ $loop->parent->index }}"
                                   type="checkbox"
                                   name="permissions[]"
                                   value="{{ $perm->name }}"
                                   id="perm_{{ $loop->parent->index }}_{{ $loop->index }}"
                                   @checked(in_array($perm->name, old('permissions', $currentPerms)))
                                   onchange="syncGroupToggle({{ $loop->parent->index }})">
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

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-save me-1"></i> Save Permissions
        </button>
    </div>
</form>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
    document.querySelectorAll('.group-toggle').forEach(cb => cb.checked = checked);
    updateCardStyles();
}

function toggleGroup(index, checked) {
    document.querySelectorAll('.grp-' + index).forEach(cb => cb.checked = checked);
    updateCardStyles();
}

function syncGroupToggle(index) {
    const group  = document.querySelectorAll('.grp-' + index);
    const allOn  = [...group].every(cb => cb.checked);
    const toggle = document.getElementById('grp_' + index);
    if (toggle) toggle.checked = allOn;
    updateCardStyles();
}

function updateCardStyles() {
    // Highlight fully-selected groups
    document.querySelectorAll('[data-group]').forEach(toggle => {
        const idx   = toggle.dataset.group;
        const group = document.querySelectorAll('.grp-' + idx);
        const card  = toggle.closest('.border.rounded');
        if (!card) return;
        const allOn = [...group].every(cb => cb.checked);
        card.style.borderColor = allOn ? 'var(--bs-primary)' : '';
    });
}
</script>
@endpush
@endsection

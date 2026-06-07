@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Roles &amp; Permissions</h4>
        <p class="text-muted small mb-0">Manage SSCMS access roles and their permission sets</p>
    </div>
    @can('manage-roles')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-plus-circle me-1"></i> New Role
    </a>
    @endcan
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 small">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 small">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    @foreach($roles as $role)
    @php
        $isSystem = in_array($role->name, ['administrator', 'nurse', 'staff']);
        $roleColor = match($role->name) {
            'administrator' => ['bg' => 'danger',  'icon' => 'shield-fill-exclamation'],
            'nurse'         => ['bg' => 'success', 'icon' => 'heart-pulse-fill'],
            'staff'         => ['bg' => 'info',    'icon' => 'person-badge-fill'],
            default         => ['bg' => 'secondary','icon' => 'person-fill'],
        };

        // Group permissions by subject
        $permGroups = $role->permissions->groupBy(function($p) {
            $parts = explode('-', $p->name);
            array_shift($parts);
            return ucfirst(implode(' ', $parts));
        })->sortKeys();
    @endphp
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                {{-- Role header --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width:46px;height:46px;font-size:1.1rem;background:var(--gradient-{{ $roleColor['bg'] }});">
                        <i class="bi bi-{{ $roleColor['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-capitalize">{{ $role->name }}</div>
                        <div class="text-muted small">
                            {{ $role->users_count }} user{{ $role->users_count !== 1 ? 's' : '' }}
                            &nbsp;&bull;&nbsp;
                            {{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}
                        </div>
                    </div>
                    @if($isSystem)
                    <span class="badge bg-secondary-subtle text-secondary-emphasis ms-auto small">
                        <i class="bi bi-lock me-1"></i>System
                    </span>
                    @endif
                </div>

                {{-- Permission groups --}}
                <div class="mb-3" style="min-height:80px;">
                    @foreach($permGroups as $group => $perms)
                    <div class="mb-1">
                        <span class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;">
                            {{ $group }}
                        </span>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            @foreach($perms as $perm)
                            @php $verb = explode('-', $perm->name)[0]; @endphp
                            <span class="badge bg-{{ $roleColor['bg'] }}-subtle text-{{ $roleColor['bg'] }}-emphasis"
                                  style="font-size:.68rem;">{{ $verb }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                    @if($role->permissions->isEmpty())
                        <span class="text-muted small">No permissions assigned.</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 pt-2 border-top">
                    <a href="{{ route('admin.roles.edit', $role) }}"
                       class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-pencil me-1"></i> Edit Permissions
                    </a>
                    @if(! $isSystem)
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteRoleModal"
                            data-id="{{ $role->id }}"
                            data-name="{{ $role->name }}"
                            title="Delete role">
                        <i class="bi bi-trash"></i>
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-shield-x text-danger fs-1 d-block mb-3"></i>
                <h6 class="fw-bold mb-1">Delete Role?</h6>
                <p class="text-muted small mb-3">
                    Delete role <strong id="delRoleName"></strong>?
                    Users with this role will lose all associated permissions.
                </p>
                <form id="deleteRoleForm" method="POST">
                    @csrf @method('DELETE')
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm px-4">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('deleteRoleModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('delRoleName').textContent = btn.dataset.name;
    document.getElementById('deleteRoleForm').action = `/admin/roles/${btn.dataset.id}`;
});
</script>
@endpush
@endsection

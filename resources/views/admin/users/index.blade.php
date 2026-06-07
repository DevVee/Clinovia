@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">User Management</h4>
        <p class="text-muted small mb-0">Manage SSCMS accounts, roles, and access</p>
    </div>
    @can('manage-users')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm px-3">
        <i class="bi bi-person-plus me-1"></i> Add User
    </a>
    @endcan
</div>

{{-- Alerts --}}
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

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" placeholder="Search name or email…">
                </div>
            </div>
            <div class="col-sm-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected(request('role') === $role->name)>
                        {{ ucfirst($role->name) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="1" @selected(request('status') === '1')>Active</option>
                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
            <div class="col-auto ms-auto text-muted small align-self-center">
                {{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }} found
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th style="width:42px;"></th>
                        <th>Name / Email</th>
                        <th class="text-center" style="width:120px;">Role</th>
                        <th class="text-center" style="width:90px;">Status</th>
                        <th style="width:150px;">Last Login</th>
                        <th style="width:120px;">Created</th>
                        <th class="text-center" style="width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $role = $user->roles->first();
                        $roleColor = match($role?->name) {
                            'administrator' => 'danger',
                            'nurse'         => 'success',
                            'staff'         => 'info',
                            default         => 'secondary',
                        };
                        $initial = strtoupper(substr($user->name, 0, 1));
                        $avatarColors = ['danger','success','info','warning','primary','purple'];
                        $avatarColor  = $avatarColors[crc32($user->email) % count($avatarColors)];
                    @endphp
                    <tr>
                        {{-- Avatar --}}
                        <td>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:34px;height:34px;font-size:.8rem;background:var(--gradient-primary);">
                                {{ $initial }}
                            </div>
                        </td>

                        {{-- Name / Email --}}
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->email }}</small>
                        </td>

                        {{-- Role --}}
                        <td class="text-center">
                            @if($role)
                            <span class="badge text-bg-{{ $roleColor }}">{{ ucfirst($role->name) }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success-emphasis">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Active
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger-emphasis">
                                    <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Last login --}}
                        <td class="text-muted">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>

                        {{-- Created --}}
                        <td class="text-muted">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="btn btn-xs btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('manage-users')
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-xs btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <button type="button"
                                        class="btn btn-xs btn-outline-danger"
                                        title="Delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 opacity-30"></i>
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-3 py-2 border-top">{{ $users->links() }}</div>
        @endif
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-person-x-fill text-danger fs-1 d-block mb-3"></i>
                <h6 class="fw-bold mb-1">Delete User?</h6>
                <p class="text-muted small mb-3">
                    Are you sure you want to delete <strong id="delUserName"></strong>? This cannot be undone.
                </p>
                <form id="deleteForm" method="POST">
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
document.getElementById('deleteModal').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('delUserName').textContent = btn.dataset.name;
    document.getElementById('deleteForm').action = `/admin/users/${btn.dataset.id}`;
});
</script>
@endpush
@endsection

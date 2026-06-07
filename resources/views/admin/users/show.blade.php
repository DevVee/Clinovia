@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">User Profile</h4>
        <p class="text-muted small mb-0">Account details and permissions</p>
    </div>
    <div class="d-flex gap-2">
        @can('manage-users')
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @endcan
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">

    {{-- ── Left: Profile card ──────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body p-4">
                {{-- Avatar --}}
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3"
                     style="width:80px;height:80px;font-size:2rem;background:var(--gradient-primary);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                @php $role = $user->roles->first(); @endphp
                @if($role)
                @php $roleColor = match($role->name) {
                    'administrator' => 'danger',
                    'nurse'         => 'success',
                    'staff'         => 'info',
                    default         => 'secondary'
                }; @endphp
                <span class="badge text-bg-{{ $roleColor }} px-3 py-2 mb-3">
                    <i class="bi bi-shield-fill me-1"></i>{{ ucfirst($role->name) }}
                </span>
                @endif

                <div class="text-center mb-3">
                    @if($user->is_active)
                        <span class="badge bg-success-subtle text-success-emphasis">
                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Active Account
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger-emphasis">
                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Inactive Account
                        </span>
                    @endif
                </div>

                <hr>

                <div class="text-start small">
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Last Login</span>
                        <span class="fw-semibold">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Member Since</span>
                        <span class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Permissions</span>
                        <span class="fw-semibold">{{ $user->getAllPermissions()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right: Permissions + recent activity ──────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Permissions --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-key text-warning me-2"></i> Permissions via
                <span class="text-primary">{{ $role?->name ?? 'No Role' }}</span>
            </div>
            <div class="card-body">
                @php
                    $allPerms = $user->getAllPermissions()->pluck('name');
                    $grouped  = $allPerms->groupBy(function($p) {
                        $parts = explode('-', $p);
                        array_shift($parts);
                        return ucfirst(implode(' ', $parts));
                    })->sortKeys();
                @endphp

                @if($allPerms->isEmpty())
                    <p class="text-muted small mb-0">No permissions assigned.</p>
                @else
                <div class="row g-2">
                    @foreach($grouped as $group => $perms)
                    <div class="col-sm-6 col-md-4">
                        <div class="border rounded p-2" style="font-size:.78rem;">
                            <div class="fw-semibold text-muted text-uppercase mb-1"
                                 style="font-size:.68rem; letter-spacing:.05em;">
                                {{ $group }}
                            </div>
                            @foreach($perms as $perm)
                            @php $verb = explode('-', $perm)[0]; @endphp
                            <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1">
                                {{ $verb }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-clock-history text-info me-2"></i> Recent Activity
                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">Last 10</span>
            </div>
            <div class="card-body p-0">
                @if($recentLogs->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-journal-x d-block fs-3 opacity-30 mb-2"></i>
                        No activity recorded yet.
                    </div>
                @else
                <ul class="list-group list-group-flush small">
                    @foreach($recentLogs as $log)
                    @php
                        $actionColor = match($log->action) {
                            'created'   => 'success',
                            'updated'   => 'warning',
                            'deleted'   => 'danger',
                            'approved'  => 'primary',
                            'cancelled' => 'danger',
                            'logged_in','logged_out' => 'info',
                            default     => 'secondary',
                        };
                    @endphp
                    <li class="list-group-item d-flex align-items-start gap-2 py-2">
                        <span class="badge text-bg-{{ $actionColor }} mt-1">{{ $log->action }}</span>
                        <div class="flex-grow-1">
                            <div>{{ $log->description }}</div>
                            <div class="text-muted" style="font-size:.73rem;">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                                &nbsp;&bull;&nbsp; {{ $log->module }}
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

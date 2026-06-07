@extends('layouts.app')

@section('title', 'Edit User — ' . $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Edit User</h4>
        <p class="text-muted small mb-0">Update account details for <strong>{{ $user->name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i> View
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:38px;height:38px;font-size:.9rem;background:var(--gradient-primary);">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold">{{ $user->name }}</div>
                    <div class="text-muted" style="font-size:.78rem;">{{ $user->email }}</div>
                </div>
            </div>
            <div class="card-body p-4">

                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    {{-- Full Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-muted"></i></span>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
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
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Leave the password fields blank to keep the current password unchanged.
                    </p>

                    {{-- Password --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Leave blank to keep">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       class="form-control"
                                       placeholder="Leave blank to keep">
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
                                    required
                                    @if($user->id === auth()->id()) disabled @endif>
                                @foreach($roles as $r)
                                <option value="{{ $r->name }}"
                                    @selected(old('role', $user->roles->first()?->name) === $r->name)>
                                    {{ ucfirst($r->name) }}
                                </option>
                                @endforeach
                            </select>
                            @if($user->id === auth()->id())
                                <input type="hidden" name="role" value="{{ $user->roles->first()?->name }}">
                                <div class="form-text text-warning">
                                    <i class="bi bi-lock me-1"></i>You cannot change your own role.
                                </div>
                            @endif
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5 d-flex flex-column">
                            <label class="form-label fw-semibold small">Account Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox"
                                       id="is_active" name="is_active" value="1"
                                       @checked(old('is_active', $user->is_active))
                                       @if($user->id === auth()->id()) disabled @endif>
                                <label class="form-check-label" for="is_active">Active (can sign in)</label>
                            </div>
                            @if($user->id === auth()->id())
                                <input type="hidden" name="is_active" value="1">
                                <div class="form-text text-warning small">
                                    <i class="bi bi-lock me-1"></i>Cannot deactivate your own account.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

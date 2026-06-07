@extends('layouts.app')

@section('title', 'My Profile')

@push('styles')
<style>
/* ── Profile Page ───────────────────────────────────────────────────── */
.profile-page-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
    align-items: start;
}
@media (max-width: 900px) {
    .profile-page-grid { grid-template-columns: 1fr; }
}

/* Avatar Card */
.avatar-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    text-align: center;
}
.avatar-card-banner {
    height: 80px;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}
.avatar-card-body {
    padding: 0 1.5rem 1.5rem;
    position: relative;
}
.avatar-upload-wrapper {
    position: relative;
    display: inline-block;
    margin-top: -48px;
    margin-bottom: .75rem;
}
.profile-big-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    border: 4px solid var(--surface);
    object-fit: cover;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    display: block;
}
.avatar-upload-btn {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #4f46e5;
    color: #fff;
    border: 2px solid var(--surface);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .7rem;
    cursor: pointer;
    transition: background .2s, transform .2s;
}
.avatar-upload-btn:hover {
    background: #4338ca;
    transform: scale(1.1);
}
.profile-big-name {
    font-weight: 700;
    font-size: 1.05rem;
    margin-bottom: .1rem;
    color: var(--text-primary);
}
.profile-big-role {
    font-size: .78rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}
.profile-stat-row {
    display: flex;
    gap: .5rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}
.profile-stat {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: .35rem .75rem;
    font-size: .72rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: .35rem;
}
.profile-stat i { color: #4f46e5; }

/* Form cards */
.profile-form-card {
    background: var(--surface);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.profile-form-card-header {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: .6rem;
}
.profile-form-card-header .card-icon {
    width: 32px;
    height: 32px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    color: #fff;
}
.profile-form-card-header h5 {
    font-size: .92rem;
    font-weight: 700;
    margin: 0;
    color: var(--text-primary);
}
.profile-form-card-header p {
    font-size: .75rem;
    color: var(--text-muted);
    margin: 0;
}
.profile-form-card-body {
    padding: 1.25rem 1.4rem;
}

/* Danger zone */
.danger-card { border: 1px solid #fca5a5; }
.danger-card .profile-form-card-header { background: #fff5f5; }

/* Password strength bar */
.strength-bar {
    height: 4px;
    border-radius: 2px;
    background: var(--border);
    margin-top: .4rem;
    overflow: hidden;
    transition: all .3s;
}
.strength-bar-fill {
    height: 100%;
    border-radius: 2px;
    width: 0;
    transition: width .3s, background .3s;
}

/* Avatar preview in modal */
.avatar-drop-zone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-xl);
    padding: 2rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
}
.avatar-drop-zone:hover, .avatar-drop-zone.drag-over {
    border-color: #4f46e5;
    background: rgba(79,70,229,.05);
}
.avatar-preview-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto .75rem;
    display: block;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
}
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">My Profile</h4>
        <p class="text-muted small mb-0">Manage your account information and preferences</p>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 small mb-3">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Avatar upload error (shown outside modal so it's visible after redirect) --}}
@if($errors->has('avatar'))
<div class="alert alert-danger alert-dismissible fade show py-2 small mb-3">
    <i class="bi bi-exclamation-circle me-2"></i>
    <strong>Photo upload failed:</strong> {{ $errors->first('avatar') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->updatePassword->any())
<div class="alert alert-danger alert-dismissible fade show py-2 small mb-3">
    <i class="bi bi-exclamation-circle me-2"></i>
    @foreach($errors->updatePassword->all() as $err){{ $err }}@endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->userDeletion->any())
<div class="alert alert-danger alert-dismissible fade show py-2 small mb-3">
    <i class="bi bi-exclamation-circle me-2"></i>
    @foreach($errors->userDeletion->all() as $err){{ $err }}@endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="profile-page-grid">

    {{-- ─── Left: Avatar Card ──────────────────────────────────────────── --}}
    <div>
        <div class="avatar-card">
            <div class="avatar-card-banner"></div>
            <div class="avatar-card-body">
                {{-- Avatar --}}
                <div class="avatar-upload-wrapper">
                    <img src="{{ $user->avatarUrl() }}?v={{ $user->updated_at->timestamp }}"
                         alt="{{ $user->name }}"
                         class="profile-big-avatar"
                         id="bigAvatarImg">
                    <button type="button"
                            class="avatar-upload-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#avatarModal"
                            title="Change photo">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>

                <div class="profile-big-name">{{ $user->name }}</div>
                @php
                    $role = $user->roles->first();
                    $roleName = $role?->name ?? 'User';
                    $roleColor = match($roleName) {
                        'administrator' => ['bg' => 'danger',   'icon' => 'shield-fill-exclamation'],
                        'nurse'         => ['bg' => 'success',  'icon' => 'heart-pulse-fill'],
                        'staff'         => ['bg' => 'info',     'icon' => 'person-badge-fill'],
                        default         => ['bg' => 'primary',  'icon' => 'person-fill'],
                    };
                    $displayIcon = $role?->icon ?? $roleColor['icon'];
                @endphp
                <div class="profile-big-role">
                    <span class="badge"
                          style="background:linear-gradient(135deg,#4f46e5,#7c3aed);font-size:.72rem;">
                        <i class="bi bi-{{ $displayIcon }} me-1"></i>
                        {{ ucfirst($roleName) }}
                    </span>
                </div>


                <div class="profile-stat-row">
                    <div class="profile-stat">
                        <i class="bi bi-calendar3"></i>
                        Joined {{ $user->created_at->format('M Y') }}
                    </div>
                    @if($user->last_login_at)
                    <div class="profile-stat">
                        <i class="bi bi-clock"></i>
                        {{ $user->last_login_at->diffForHumans() }}
                    </div>
                    @endif
                </div>

                @if($user->avatar)
                <form method="POST" action="{{ route('profile.avatar.remove') }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="btn btn-outline-secondary btn-sm w-100"
                            onclick="return confirm('Remove your profile picture?')">
                        <i class="bi bi-trash me-1"></i> Remove Photo
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Right: Form Cards ───────────────────────────────────────────── --}}
    <div>

        {{-- Account Info --}}
        <div class="profile-form-card">
            <div class="profile-form-card-header">
                <div class="card-icon" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h5>Account Information</h5>
                    <p>Update your name and email address</p>
                </div>
            </div>
            <div class="profile-form-card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="profile-form-card">
            <div class="profile-form-card-header">
                <div class="card-icon" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h5>Change Password</h5>
                    <p>Use a strong, unique password</p>
                </div>
            </div>
            <div class="profile-form-card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Current Password</label>
                            <input type="password" name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">New Password</label>
                            <input type="password" name="password" id="newPassword"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            <div class="strength-bar mt-1">
                                <div class="strength-bar-fill" id="strengthBar"></div>
                            </div>
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-lock me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="profile-form-card danger-card">
            <div class="profile-form-card-header">
                <div class="card-icon" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h5 class="text-danger">Danger Zone</h5>
                    <p>Permanently delete your account</p>
                </div>
            </div>
            <div class="profile-form-card-body">
                <p class="small text-muted mb-3">
                    Once your account is deleted, all data will be permanently removed and cannot be recovered.
                    Please download any data you wish to keep before deleting.
                </p>
                <button type="button"
                        class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash3 me-1"></i> Delete My Account
                </button>
            </div>
        </div>

    </div>{{-- /right column --}}
</div>{{-- /grid --}}


{{-- ─── Avatar Upload Modal ──────────────────────────────────────────────── --}}
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-camera text-primary me-2"></i>Update Profile Photo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form method="POST"
                      action="{{ route('profile.avatar') }}"
                      enctype="multipart/form-data"
                      id="avatarForm">
                    @csrf

                    {{-- File input: visually hidden but NOT display:none so browsers include it in submit --}}
                    <input type="file" name="avatar" id="avatarInput"
                           accept="image/jpeg,image/png,image/webp,image/gif"
                           style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;">

                    <div class="avatar-drop-zone" id="avatarDropZone">
                        <img id="avatarPreview"
                             src="{{ $user->avatarUrl() }}"
                             class="avatar-preview-img mb-0">
                        <p class="text-muted small mb-1 mt-2">
                            <i class="bi bi-cloud-upload me-1"></i>
                            Drag &amp; drop or <strong>click to browse</strong>
                        </p>
                        <p class="text-muted" style="font-size:.72rem;">JPG, PNG, WebP, GIF · Max 15 MB</p>
                    </div>

                    @error('avatar')
                    <div class="alert alert-danger py-2 small mt-2 mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                    @enderror

                    <div class="d-flex gap-2 justify-content-end mt-3">
                        <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4" id="avatarSubmitBtn" disabled>
                            <i class="bi bi-upload me-1"></i> Upload Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ─── Delete Account Modal ─────────────────────────────────────────────── --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center py-4 px-4">
                <div class="mb-3" style="font-size:2.5rem;color:#dc2626;">
                    <i class="bi bi-person-x-fill"></i>
                </div>
                <h5 class="fw-bold mb-1">Delete Account?</h5>
                <p class="text-muted small mb-3">
                    This action is <strong>irreversible</strong>. Enter your password to confirm.
                </p>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf @method('DELETE')
                    <input type="password" name="password"
                           class="form-control mb-3 @error('password', 'userDeletion') is-invalid @enderror"
                           placeholder="Your password"
                           required>
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback d-block text-start mb-2">{{ $message }}</div>
                    @enderror
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm px-4">
                            <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {

    /* ── Password strength bar ───────────────────────────────────────── */
    const pwdInput = document.getElementById('newPassword');
    const bar      = document.getElementById('strengthBar');
    if (pwdInput && bar) {
        pwdInput.addEventListener('input', function () {
            const v = this.value;
            let score = 0;
            if (v.length >= 8)               score++;
            if (/[A-Z]/.test(v))             score++;
            if (/[0-9]/.test(v))             score++;
            if (/[^A-Za-z0-9]/.test(v))      score++;
            const w  = ['0%', '25%', '50%', '75%', '100%'];
            const bg = ['transparent', '#ef4444', '#f97316', '#eab308', '#22c55e'];
            bar.style.width      = w[score];
            bar.style.background = bg[score];
        });
    }

    /* ── Avatar drag-and-drop / file picker ──────────────────────────── */
    const dropZone   = document.getElementById('avatarDropZone');
    const fileInput  = document.getElementById('avatarInput');
    const preview    = document.getElementById('avatarPreview');
    const submitBtn  = document.getElementById('avatarSubmitBtn');

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (file) applyFile(file);
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files[0]) applyFile(fileInput.files[0]);
        });

        function applyFile(file) {
            // Transfer to real input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                submitBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    }
})();
</script>
@endpush

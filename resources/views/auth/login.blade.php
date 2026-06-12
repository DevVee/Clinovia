<x-guest-layout>
<div class="auth-split">

    {{-- ══════════════════════════════════════════════════════
         LEFT — Brand Panel
    ══════════════════════════════════════════════════════ --}}
    <div class="auth-panel-brand">
        <div class="auth-brand-deco auth-brand-deco--sm"></div>
        <div class="auth-brand-deco auth-brand-deco--md"></div>

        <div class="auth-brand-inner">

            {{-- Emblem --}}
            <div class="auth-brand-emblem">
                <img src="/clinovia-icon.svg" alt="Clinovia" style="width:54px;height:54px;display:block;">
            </div>

            {{-- Name & tagline --}}
            <div class="auth-brand-name">Clinovia</div>
            <p class="auth-brand-tagline">Smart School Clinic Management System</p>

            {{-- Feature highlights --}}
            <div class="auth-features">
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="auth-feature-text">
                        <strong>Patient Records</strong>
                        <span>Complete student & staff health profiles</span>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                    <div class="auth-feature-text">
                        <strong>Appointment Scheduling</strong>
                        <span>Smart slot management with SMS alerts</span>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="bi bi-capsule-pill"></i>
                    </div>
                    <div class="auth-feature-text">
                        <strong>Medicine Inventory</strong>
                        <span>Real-time stock tracking & expiry alerts</span>
                    </div>
                </div>
                <div class="auth-feature-item">
                    <div class="auth-feature-icon">
                        <i class="bi bi-bar-chart-fill"></i>
                    </div>
                    <div class="auth-feature-text">
                        <strong>Reports & Analytics</strong>
                        <span>Daily, monthly & annual PDF/CSV exports</span>
                    </div>
                </div>
            </div>

            {{-- Bottom footer --}}
            <p class="auth-brand-footer">
                &copy; {{ date('Y') }} Clinovia &mdash; All rights reserved
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         RIGHT — Login Form Panel
    ══════════════════════════════════════════════════════ --}}
    <div class="auth-panel-form">
        <div class="auth-form-inner">

            {{-- Mobile-only header --}}
            <div class="auth-mobile-header">
                <div class="auth-logo mb-3">
                    <i class="bi bi-heart-pulse-fill"></i>
                </div>
                <h5 class="fw-bold mb-0">Clinovia</h5>
                <p class="text-muted small">Smart School Clinic Management System</p>
            </div>

            {{-- Heading --}}
            <h2 class="auth-form-heading">Welcome back</h2>
            <p class="auth-form-sub">Sign in to your Clinovia account to continue</p>

            {{-- Session / Error Alerts --}}
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-4">
                    <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show py-2 small mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold small">Email Address</label>
                    <div class="input-group auth-form-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="you@email.com"
                               required
                               autofocus
                               autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold small mb-0">Password</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="small text-decoration-none"
                               style="color: var(--primary);">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="input-group auth-form-input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input id="password"
                               type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Enter your password"
                               required
                               autocomplete="current-password">
                        <button class="input-group-text border-start-0 bg-white"
                                type="button"
                                id="togglePwd"
                                style="cursor:pointer; border-color: hsl(210,20%,88%);"
                                title="Show/hide password">
                            <i class="bi bi-eye" id="pwdIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="form-check mb-4">
                    <input type="checkbox"
                           class="form-check-input"
                           id="remember_me"
                           name="remember">
                    <label class="form-check-label small" for="remember_me">
                        Keep me signed in for 30 days
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-auth-submit mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Clinovia
                </button>

                {{-- Divider info --}}
                <p class="text-center text-muted small mb-0">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i>
                    Secured access &mdash; Clinovia authorized personnel only
                </p>
            </form>

            {{-- ── Demo Quick-Login ────────────────────────────────────────────── --}}
            <div class="mt-4 pt-3" style="border-top:1px dashed hsl(210,20%,88%);">
                <p class="text-center mb-2" style="font-size:.72rem; color:var(--text-muted); letter-spacing:.03em;">
                    <i class="bi bi-play-circle-fill me-1" style="color:hsl(201,85%,39%);"></i>
                    <strong>Portfolio Demo</strong> — one-click sample login
                </p>
                <div class="d-grid gap-2">
                    <button type="button"
                            class="btn btn-sm"
                            style="border:1.5px solid hsl(201,85%,39%);color:hsl(201,85%,39%);border-radius:10px;font-size:.8rem;"
                            onclick="quickLogin('admin@clinovia.app','Admin@2026!')">
                        <i class="bi bi-shield-check me-1"></i>Admin Demo
                        <span class="ms-1 opacity-75" style="font-size:.7rem;">full access</span>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-success"
                            style="border-radius:10px;font-size:.8rem;"
                            onclick="quickLogin('viewer@clinovia.app','Viewer@2026!')">
                        <i class="bi bi-eye me-1"></i>Viewer Demo
                        <span class="ms-1 opacity-75" style="font-size:.7rem;">read-only</span>
                    </button>
                </div>
                <p class="text-center mt-2 mb-0" style="font-size:.68rem; color:var(--text-muted);">
                    <i class="bi bi-database me-1"></i>Pre-loaded sample data &mdash; no real patient records
                </p>
            </div>

            {{-- Footer --}}
            <hr class="mt-4 mb-3" style="border-color: hsl(210,20%,92%);">
            <p class="text-center mb-0" style="font-size:.75rem; color:var(--text-muted);">
                &copy; {{ date('Y') }} <strong>Clinovia</strong> &mdash; Smart School Clinic Management System
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn  = document.getElementById('togglePwd');
    const icon = document.getElementById('pwdIcon');
    const pwd  = document.getElementById('password');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type       = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
});

/* Quick-login: fills credentials and submits the login form */
function quickLogin(email, password) {
    document.getElementById('email').value    = email;
    document.getElementById('password').value = password;
    document.querySelector('form[method="POST"]').submit();
}
</script>
</x-guest-layout>

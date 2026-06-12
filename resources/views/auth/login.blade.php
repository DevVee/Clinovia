<x-guest-layout>
<div class="auth-split">

    {{-- ══════════════════════════════════════════════════════
         LEFT — Brand Panel
    ══════════════════════════════════════════════════════ --}}
    <div class="auth-panel-brand">

        {{-- Logo row --}}
        <div class="apb-top">
            <div class="apb-logo"><img src="/clinovia-icon.svg" alt="Clinovia" width="22" height="22" style="display:block;border-radius:6px"></div>
            <span class="apb-wordmark">Clinovia</span>
        </div>

        {{-- Headline + product-moment card --}}
        <div class="apb-body">
            <h2 class="apb-headline">Your clinic,<br>completely<br>organized.</h2>
            <p class="apb-sub">Patient records, appointments, medicines, and AI — one platform for your entire school clinic.</p>

            <div class="apb-card">
                <div class="apb-card-header">
                    <span class="apb-card-title">Today's Clinic</span>
                    <span class="apb-card-date">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="apb-stats">
                    <div class="apb-stat">
                        <div class="apb-stat-val">14</div>
                        <div class="apb-stat-lbl">Patients<br>Seen</div>
                    </div>
                    <div class="apb-stat">
                        <div class="apb-stat-val">8</div>
                        <div class="apb-stat-lbl">Appoint-<br>ments</div>
                    </div>
                    <div class="apb-stat">
                        <div class="apb-stat-val">3</div>
                        <div class="apb-stat-lbl">Medicines<br>Low</div>
                    </div>
                </div>
                <div class="apb-entries">
                    <div class="apb-entry">
                        <div class="apb-av" style="background:hsl(201,85%,42%)">MR</div>
                        <div class="apb-entry-info">
                            <span class="apb-entry-name">Maria Reyes</span>
                            <span class="apb-entry-sub">Check-up · 9:30 AM</span>
                        </div>
                        <span class="apb-badge apb-badge--green">Done</span>
                    </div>
                    <div class="apb-entry">
                        <div class="apb-av" style="background:hsl(265,58%,52%)">JC</div>
                        <div class="apb-entry-info">
                            <span class="apb-entry-name">Juan Cruz</span>
                            <span class="apb-entry-sub">Fever · 10:00 AM</span>
                        </div>
                        <span class="apb-badge apb-badge--blue">In Progress</span>
                    </div>
                    <div class="apb-entry">
                        <div class="apb-av" style="background:hsl(38,80%,48%)">AP</div>
                        <div class="apb-entry-info">
                            <span class="apb-entry-name">Ana Palma</span>
                            <span class="apb-entry-sub">Appointment · 11:00 AM</span>
                        </div>
                        <span class="apb-badge apb-badge--yel">Pending</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quote --}}
        <div class="apb-bottom">
            <p class="apb-quote">Clinovia cut our record-keeping time in half — and Cobi AI is genuinely useful for the entire clinic staff.</p>
            <span class="apb-quote-attr">— School Clinic Nurse, Metro Manila</span>
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
                    <img src="/clinovia-icon.svg" alt="Clinovia" width="48" height="48" style="border-radius:12px">
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

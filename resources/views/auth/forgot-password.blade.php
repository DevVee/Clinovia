<x-guest-layout>
<div class="auth-wrapper">
    <div class="auth-card card p-4 p-sm-5">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="auth-logo mb-3">
                <i class="bi bi-envelope-arrow-up-fill"></i>
            </div>
            <h4 class="fw-bold mb-1" style="font-family:'Poppins',sans-serif;">Forgot Password?</h4>
            <p class="text-muted small mb-1">Clinovia &mdash; Smart School Clinic</p>
            <p class="text-muted" style="font-size:.8rem;">
                Enter your email and we'll send a reset link.
            </p>
        </div>

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show py-2 small mb-3">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                           placeholder="you@email.com"
                           required
                           autofocus
                           autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-send me-2"></i>Send Reset Link
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}"
                   class="small text-decoration-none"
                   style="color:var(--primary);">
                    <i class="bi bi-arrow-left me-1"></i>Back to Sign In
                </a>
            </div>
        </form>

        <hr class="mt-4 mb-3">
        <p class="text-center text-muted mb-0" style="font-size:.72rem;">
            &copy; {{ date('Y') }} Clinovia. All rights reserved.
        </p>
    </div>
</div>
</x-guest-layout>

<x-guest-layout>
<div class="auth-wrapper">
    <div class="auth-card card p-4 p-sm-5">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="auth-logo mb-3">
                <i class="bi bi-key-fill"></i>
            </div>
            <h4 class="fw-bold mb-1" style="font-family:'Poppins',sans-serif;">Reset Password</h4>
            <p class="text-muted small">Clinovia &mdash; Smart School Clinic</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', $request->email) }}"
                           class="form-control @error('email') is-invalid @enderror"
                           required
                           autofocus
                           autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- New Password --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold small">New Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                    <input id="password"
                           type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required
                           autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required
                           autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3">
                <i class="bi bi-key me-2"></i>Reset Password
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

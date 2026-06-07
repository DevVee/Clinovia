<x-guest-layout>
<div class="auth-wrapper">
    <div class="auth-card card p-4 p-sm-5">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="auth-logo mb-3">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h4 class="fw-bold mb-1" style="font-family:'Poppins',sans-serif;">Confirm Password</h4>
            <p class="text-muted small">Clinovia &mdash; Smart School Clinic</p>
        </div>

        <p class="text-muted small text-center mb-4">
            This is a secure area. Please confirm your password before continuing.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                    <input id="password"
                           type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password"
                           required
                           autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-shield-check me-2"></i>Confirm &amp; Continue
            </button>
        </form>

        <hr class="mt-4 mb-3">
        <p class="text-center text-muted mb-0" style="font-size:.72rem;">
            &copy; {{ date('Y') }} Clinovia. All rights reserved.
        </p>
    </div>
</div>
</x-guest-layout>

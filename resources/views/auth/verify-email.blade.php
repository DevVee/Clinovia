<x-guest-layout>
<div class="auth-wrapper">
    <div class="auth-card card p-4 p-sm-5">

        {{-- Logo --}}
        <div class="text-center mb-4">
            <div class="auth-logo mb-3">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <h4 class="fw-bold mb-1" style="font-family:'Poppins',sans-serif;">Verify Email</h4>
            <p class="text-muted small">Clinovia &mdash; Smart School Clinic</p>
        </div>

        <p class="text-muted small mb-4 text-center">
            Before getting started, please verify your email address by clicking the link we sent you.
            If you didn't receive the email, request another below.
        </p>

        @if(session('status') === 'verification-link-sent')
            <div class="alert alert-success py-2 small mb-4">
                <i class="bi bi-check-circle me-2"></i>
                A new verification link has been sent to your email address.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-send me-2"></i>Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100 py-2 small">
                <i class="bi bi-box-arrow-right me-2"></i>Log Out
            </button>
        </form>

        <hr class="mt-4 mb-3">
        <p class="text-center text-muted mb-0" style="font-size:.72rem;">
            &copy; {{ date('Y') }} Clinovia. All rights reserved.
        </p>
    </div>
</div>
</x-guest-layout>

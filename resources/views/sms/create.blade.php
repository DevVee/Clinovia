@extends('layouts.app')

@section('title', 'Send SMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Send Manual SMS</h4>
        <p class="text-muted small mb-0">Send a custom message to a patient or contact</p>
    </div>
    <a href="{{ route('sms.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Logs
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                {{-- API Key Notice --}}
                @if(!config('semaphore.api_key'))
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>No API key configured.</strong>
                    Add <code>SEMAPHORE_API_KEY</code> to your <code>.env</code> file to enable SMS sending.
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('sms.send') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipient Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="recipient_number" value="{{ old('recipient_number') }}"
                            class="form-control @error('recipient_number') is-invalid @enderror"
                            placeholder="09XXXXXXXXX" maxlength="11" required>
                        <div class="form-text">11-digit Philippine mobile number (e.g. 09171234567)</div>
                        @error('recipient_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipient Name <span class="text-muted fw-normal">(Optional)</span></label>
                        <input type="text" name="recipient_name" value="{{ old('recipient_name') }}"
                            class="form-control @error('recipient_name') is-invalid @enderror"
                            placeholder="e.g. Juan dela Cruz" maxlength="150">
                        @error('recipient_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="msgInput" rows="4" maxlength="160"
                            class="form-control @error('message') is-invalid @enderror"
                            required placeholder="Type your message here…">{{ old('message') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            @error('message')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted ms-auto">
                                <span id="charCount">0</span>/160 characters
                            </small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('sms.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4"
                            @if(!config('semaphore.api_key')) disabled @endif>
                            <i class="bi bi-send me-1"></i> Send SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Sender info --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">
                <i class="bi bi-info-circle me-1"></i> SMS Info
            </div>
            <div class="card-body">
                <dl class="mb-0" style="font-size:.875rem;">
                    <dt class="text-muted small">Sender Name</dt>
                    <dd class="fw-semibold">{{ config('semaphore.sender_name', 'CLINOVIA') }}</dd>
                    <dt class="text-muted small">Provider</dt>
                    <dd class="fw-semibold">Semaphore.co</dd>
                    <dt class="text-muted small">Max Length</dt>
                    <dd class="fw-semibold">160 characters</dd>
                    <dt class="text-muted small">Format</dt>
                    <dd class="fw-semibold mb-0">11-digit PH number (09XXXXXXXXX)</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const msgInput  = document.getElementById('msgInput');
const charCount = document.getElementById('charCount');
function update() { charCount.textContent = msgInput.value.length; }
msgInput.addEventListener('input', update);
update();
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Dispense Medicine')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dispense Medicine</h4>
        <p class="text-muted small mb-0">Record medicine dispensed to a patient</p>
    </div>
    <a href="{{ route('dispensing.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('dispensing.store') }}">
                    @csrf

                    {{-- Step 1: Patient --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2">1. Select Patient</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patientSelect"
                            class="form-select @error('patient_id') is-invalid @enderror" required>
                            <option value="">— Select Patient —</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}"
                                @selected(old('patient_id', $selectedPatient) == $p->id)>
                                {{ $p->last_name }}, {{ $p->first_name }}
                                @if($p->middle_name) {{ $p->middle_name[0] }}. @endif
                                — {{ $p->patient_number }}
                            </option>
                            @endforeach
                        </select>
                        @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Step 2: Consultation (optional) --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2 mt-4">2. Link Consultation <span class="text-muted fw-normal">(Optional)</span></h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Consultation Visit</label>
                        <select name="consultation_id" id="consultSelect" class="form-select">
                            <option value="">— None (walk-in dispensing) —</option>
                        </select>
                    </div>

                    {{-- Step 3: Medicine --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2 mt-4">3. Select Medicine</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Medicine <span class="text-danger">*</span></label>
                        <select name="medicine_id" id="medicineSelect"
                            class="form-select @error('medicine_id') is-invalid @enderror" required>
                            <option value="">— Select Medicine —</option>
                            @foreach($medicines as $med)
                            <option value="{{ $med->id }}"
                                data-unit="{{ $med->unit }}"
                                data-qty="{{ $med->quantity }}"
                                data-low="{{ $med->low_stock_threshold }}"
                                @selected(old('medicine_id') == $med->id)>
                                {{ $med->name }} — {{ $med->quantity }} {{ $med->unit }}s available
                            </option>
                            @endforeach
                        </select>
                        @error('medicine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Stock indicator --}}
                    <div id="stockBar" class="mb-3 d-none">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Available Stock</span>
                            <span id="stockQty" class="fw-semibold"></span>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div id="stockProgress" class="progress-bar" role="progressbar" style="width:0%"></div>
                        </div>
                    </div>

                    {{-- Step 4: Quantity & Remarks --}}
                    <h6 class="fw-bold text-muted text-uppercase small mb-3 border-bottom pb-2 mt-4">4. Quantity & Remarks</h6>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="qtyInput" value="{{ old('quantity', 1) }}"
                                class="form-control @error('quantity') is-invalid @enderror"
                                min="1" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Remarks</label>
                            <textarea name="remarks" rows="2"
                                class="form-control @error('remarks') is-invalid @enderror"
                                placeholder="Dosage instructions, special notes…">{{ old('remarks') }}</textarea>
                            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('dispensing.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-capsule me-1"></i> Dispense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const consultations = @json($consultations->groupBy('patient_id'));
const patientSel  = document.getElementById('patientSelect');
const consultSel  = document.getElementById('consultSelect');
const medSel      = document.getElementById('medicineSelect');
const stockBar    = document.getElementById('stockBar');
const stockQty    = document.getElementById('stockQty');
const stockProg   = document.getElementById('stockProgress');
const qtyInput    = document.getElementById('qtyInput');

function populateConsultations(patientId) {
    consultSel.innerHTML = '<option value="">— None (walk-in dispensing) —</option>';
    (consultations[patientId] || []).forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        const d = new Date(c.visit_date + 'T00:00:00').toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
        opt.textContent = d + ' — ' + (c.chief_complaint ? c.chief_complaint.substring(0, 50) : 'Visit');
        if ('{{ old('consultation_id') }}' == c.id) opt.selected = true;
        consultSel.appendChild(opt);
    });
}

patientSel.addEventListener('change', function () {
    populateConsultations(this.value);
});

medSel.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (this.value) {
        const qty  = parseInt(opt.dataset.qty);
        const low  = parseInt(opt.dataset.low);
        const pct  = Math.min(100, Math.round((qty / Math.max(low * 3, qty, 1)) * 100));
        stockQty.textContent = qty + ' ' + opt.dataset.unit + 's';
        stockProg.style.width = pct + '%';
        stockProg.className   = 'progress-bar bg-' + (qty === 0 ? 'danger' : qty <= low ? 'warning' : 'success');
        qtyInput.max = qty;
        stockBar.classList.remove('d-none');
    } else {
        stockBar.classList.add('d-none');
        qtyInput.removeAttribute('max');
    }
});

// Initialise on load
if (patientSel.value) populateConsultations(patientSel.value);
if (medSel.value)     medSel.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

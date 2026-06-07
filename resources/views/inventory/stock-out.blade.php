@extends('layouts.app')

@section('title', 'Stock Out')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Stock Out</h4>
        <p class="text-muted small mb-0">Remove stock (damage, expiry, adjustment)</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Inventory
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <form method="POST" action="{{ route('inventory.stock-out') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Medicine <span class="text-danger">*</span></label>
                        <select name="medicine_id" id="medicineSelect"
                            class="form-select @error('medicine_id') is-invalid @enderror" required>
                            <option value="">— Select Medicine —</option>
                            @foreach($medicines as $med)
                            <option value="{{ $med->id }}"
                                data-unit="{{ $med->unit }}"
                                data-qty="{{ $med->quantity }}"
                                @selected(old('medicine_id', $selected) == $med->id)>
                                {{ $med->name }} ({{ number_format($med->quantity) }} {{ $med->unit }}s in stock)
                            </option>
                            @endforeach
                        </select>
                        @error('medicine_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div id="stockInfo" class="alert alert-warning d-none mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Available: <strong id="currentStock">0</strong> <span id="unitLabel">units</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Quantity to Remove <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}"
                                class="form-control @error('quantity') is-invalid @enderror"
                                min="1" id="qtyInput" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reason / Notes <span class="text-danger">*</span></label>
                            <textarea name="notes" rows="3"
                                class="form-control @error('notes') is-invalid @enderror"
                                required placeholder="e.g., Expired batch, damaged, annual adjustment…">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-dash-circle me-1"></i> Remove Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const medSel    = document.getElementById('medicineSelect');
const stockInfo = document.getElementById('stockInfo');
const qtyInput  = document.getElementById('qtyInput');

medSel.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    if (this.value) {
        const qty = parseInt(opt.dataset.qty);
        document.getElementById('currentStock').textContent = qty;
        document.getElementById('unitLabel').textContent    = opt.dataset.unit + 's';
        qtyInput.max = qty;
        stockInfo.classList.remove('d-none');
    } else {
        stockInfo.classList.add('d-none');
        qtyInput.removeAttribute('max');
    }
});
if (medSel.value) medSel.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

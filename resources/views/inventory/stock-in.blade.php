@extends('layouts.app')

@section('title', 'Stock In')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Stock In</h4>
        <p class="text-muted small mb-0">Add new stock to a medicine</p>
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

                <form method="POST" action="{{ route('inventory.stock-in') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Medicine <span class="text-danger">*</span></label>
                        <select name="medicine_id" class="form-select @error('medicine_id') is-invalid @enderror" required>
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

                    <div id="stockInfo" class="alert alert-info d-none mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Current stock: <strong id="currentStock">0</strong> <span id="unitLabel">units</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Quantity to Add <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" value="{{ old('quantity') }}"
                                class="form-control @error('quantity') is-invalid @enderror" min="1" required>
                            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Batch Number</label>
                            <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                                class="form-control @error('batch_number') is-invalid @enderror" maxlength="100">
                            @error('batch_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Expiration Date</label>
                            <input type="date" name="expiration_date" value="{{ old('expiration_date') }}"
                                class="form-control @error('expiration_date') is-invalid @enderror">
                            @error('expiration_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Supplier</label>
                            <input type="text" name="supplier" value="{{ old('supplier') }}"
                                class="form-control @error('supplier') is-invalid @enderror" maxlength="200">
                            @error('supplier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" rows="3"
                                class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-1"></i> Add Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const medSel = document.querySelector('[name="medicine_id"]');
medSel.addEventListener('change', function () {
    const opt = this.selectedOptions[0];
    const info = document.getElementById('stockInfo');
    if (this.value) {
        document.getElementById('currentStock').textContent = opt.dataset.qty;
        document.getElementById('unitLabel').textContent    = opt.dataset.unit + 's';
        info.classList.remove('d-none');
    } else {
        info.classList.add('d-none');
    }
});
if (medSel.value) medSel.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Edit Medicine')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Medicine</h4>
        <p class="text-muted mb-0 small">{{ $medicine->name }}</p>
    </div>
    <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('medicines.update', $medicine) }}">
    @csrf @method('PUT')
    <div class="row g-4">

        {{-- ── Left: Basic Info ────────────────────────────────────────────── --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <i class="bi bi-info-circle-fill me-2 text-primary"></i>Medicine Details
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Medicine Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $medicine->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Category <span class="text-danger">*</span>
                        </label>
                        <select name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select category…</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category_id', $medicine->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  >{{ old('description', $medicine->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Supplier / Manufacturer</label>
                        <input type="text" name="supplier"
                               class="form-control @error('supplier') is-invalid @enderror"
                               value="{{ old('supplier', $medicine->supplier) }}">
                        @error('supplier')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ── Right: Stock Info ───────────────────────────────────────────── --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">
                    <i class="bi bi-box-seam-fill me-2 text-primary"></i>Stock Information
                </div>
                <div class="card-body">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', $medicine->quantity) }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Unit <span class="text-danger">*</span>
                            </label>
                            <select name="unit"
                                    class="form-select @error('unit') is-invalid @enderror" required>
                                @foreach (['tablet','capsule','ml','vial','piece','box','bottle','sachet','other'] as $u)
                                    <option value="{{ $u }}"
                                        {{ old('unit', $medicine->unit) === $u ? 'selected' : '' }}>
                                        {{ ucfirst($u) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Low Stock Threshold <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="low_stock_threshold"
                               class="form-control @error('low_stock_threshold') is-invalid @enderror"
                               value="{{ old('low_stock_threshold', $medicine->low_stock_threshold) }}"
                               min="0" required>
                        @error('low_stock_threshold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expiration Date</label>
                            <input type="date" name="expiration_date"
                                   class="form-control @error('expiration_date') is-invalid @enderror"
                                   value="{{ old('expiration_date', $medicine->expiration_date?->toDateString()) }}">
                            @error('expiration_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Batch / Lot Number</label>
                            <input type="text" name="batch_number"
                                   class="form-control @error('batch_number') is-invalid @enderror"
                                   value="{{ old('batch_number', $medicine->batch_number) }}">
                            @error('batch_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" id="isActive" value="1"
                                   {{ old('is_active', $medicine->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActive">
                                Active (visible in inventory)
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('medicines.show', $medicine) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy-fill me-1"></i>Save Changes
        </button>
    </div>
</form>
@endsection

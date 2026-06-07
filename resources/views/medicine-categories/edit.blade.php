@extends('layouts.app')

@section('title', 'Edit Category — ' . $medicineCategory->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-tag-fill me-2 text-primary"></i>Edit Category
        </h4>
        <p class="text-muted small mb-0">{{ $medicineCategory->name }}</p>
    </div>
    <a href="{{ route('medicine-categories.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Categories
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('medicine-categories.update', $medicineCategory) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $medicineCategory->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Optional description…">{{ old('description', $medicineCategory->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('medicine-categories.index') }}" class="btn btn-outline-secondary flex-fill">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($medicineCategory->medicines_count ?? $medicineCategory->medicines()->count() > 0)
        <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle me-1"></i>
            This category has <strong>{{ $medicineCategory->medicines()->count() }}</strong> medicine(s) assigned to it.
        </div>
        @endif
    </div>
</div>
@endsection

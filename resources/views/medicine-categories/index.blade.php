@extends('layouts.app')

@section('title', 'Medicine Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-tags-fill me-2 text-primary"></i>Medicine Categories</h4>
        <p class="text-muted small mb-0">Organise medicines into categories for easier management</p>
    </div>
    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Medicines
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
    <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- ── Left: Category List ──────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-ul me-2"></i>All Categories</span>
                <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $categories->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($categories->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-tags" style="font-size:2.5rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                    No categories yet. Create one using the form.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Category</th>
                                <th>Description</th>
                                <th class="text-center">Medicines</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $cat->name }}</td>
                                <td class="text-muted small">{{ $cat->description ?: '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('medicines.index', ['category' => $cat->id]) }}"
                                       class="badge bg-primary-subtle text-primary-emphasis text-decoration-none">
                                        {{ $cat->medicines_count }}
                                    </a>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        @can('update-medicines')
                                        <a href="{{ route('medicine-categories.edit', $cat) }}"
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('delete-medicines')
                                        <button type="button"
                                                class="btn btn-outline-danger btn-delete"
                                                data-action="{{ route('medicine-categories.destroy', $cat) }}"
                                                data-label="{{ $cat->name }}"
                                                data-count="{{ $cat->medicines_count }}"
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Right: Add New Category ──────────────────────────────────── --}}
    @can('create-medicines')
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>New Category
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('medicine-categories.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="e.g. Analgesic" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Optional description…">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle-fill me-1"></i>Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endcan

</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-trash-fill me-2"></i>Delete Category?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted small" id="deleteModalBody">
                This action cannot be undone.
            </div>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        const count = parseInt(this.dataset.count);
        if (count > 0) {
            alert('Cannot delete "' + this.dataset.label + '" — it has ' + count + ' medicine(s) assigned. Reassign them first.');
            return;
        }
        document.getElementById('deleteForm').action = this.dataset.action;
        document.getElementById('deleteModalBody').textContent =
            'Delete category "' + this.dataset.label + '"? This cannot be undone.';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
@endpush
@endsection

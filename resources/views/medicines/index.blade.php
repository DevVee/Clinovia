@extends('layouts.app')

@section('title', 'Medicines')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-capsule-pill me-2 text-primary"></i>Medicine Inventory</h4>
        <p class="text-muted mb-0 small">Manage clinic medicines and stock levels</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('medicines.low-stock') }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>Low Stock
        </a>
        <a href="{{ route('medicines.expiring') }}" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-calendar-x-fill me-1"></i>Expiring
        </a>
        @can('view-medicines')
        <a href="{{ route('medicine-categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-tags me-1"></i>Categories
        </a>
        @endcan
        @can('create-medicines')
        <a href="{{ route('medicines.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i>Add Medicine
        </a>
        @endcan
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('medicines.index') }}" class="row g-2 align-items-end">

            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Medicine name…" value="{{ $filters['search'] }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $filters['category'] == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Stock</label>
                <select name="stock" class="form-select">
                    <option value="">All</option>
                    <option value="low" {{ $filters['stockFilter'] === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ $filters['stockFilter'] === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if ($filters['search'] || $filters['category'] || $filters['stockFilter'])
                <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary" title="Clear">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>

        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($medicines->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-capsule-pill" style="font-size:3rem;opacity:.3;"></i>
                <p class="mt-2 mb-0">No medicines found.</p>
                @can('create-medicines')
                <a href="{{ route('medicines.create') }}" class="btn btn-sm btn-primary mt-3">
                    <i class="bi bi-plus-circle-fill me-1"></i>Add Medicine
                </a>
                @endcan
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Medicine</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Unit</th>
                        <th>Expiration</th>
                        <th>Supplier</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($medicines as $med)
                @php
                    $stockBadge = match(true) {
                        $med->quantity === 0 => ['class' => 'danger',  'label' => 'Out of Stock'],
                        $med->is_low_stock   => ['class' => 'warning', 'label' => 'Low Stock'],
                        default              => ['class' => 'success', 'label' => 'In Stock'],
                    };
                    $expiryBadge = $med->expiration_date
                        ? match(true) {
                            $med->is_expired        => ['class' => 'danger',  'label' => 'Expired'],
                            $med->is_expiring_soon  => ['class' => 'warning', 'label' => $med->expiration_date->format('M d, Y')],
                            default                 => ['class' => 'secondary', 'label' => $med->expiration_date->format('M d, Y')],
                        }
                        : null;
                @endphp
                <tr>
                    <td class="ps-4">
                        <a href="{{ route('medicines.show', $med) }}"
                           class="fw-semibold text-decoration-none">
                            {{ $med->name }}
                        </a>
                        @if ($med->batch_number)
                        <div class="small text-muted font-monospace">Lot: {{ $med->batch_number }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $med->category->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $stockBadge['class'] }}-subtle text-{{ $stockBadge['class'] }}-emphasis fw-semibold"
                              style="font-size:.85rem;padding:.4em .8em;">
                            {{ number_format($med->quantity) }}
                        </span>
                        <span class="badge bg-{{ $stockBadge['class'] }}-subtle text-{{ $stockBadge['class'] }}-emphasis ms-1">
                            {{ $stockBadge['label'] }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ ucfirst($med->unit) }}</td>
                    <td>
                        @if ($expiryBadge)
                            <span class="badge bg-{{ $expiryBadge['class'] }}-subtle text-{{ $expiryBadge['class'] }}-emphasis">
                                {{ $expiryBadge['label'] }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $med->supplier ?? '—' }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('medicines.show', $med) }}"
                               class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('update-medicines')
                            <a href="{{ route('medicines.edit', $med) }}"
                               class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('delete-medicines')
                            <button type="button" class="btn btn-outline-danger btn-delete"
                                    data-action="{{ route('medicines.destroy', $med) }}"
                                    data-label="{{ $med->name }}"
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

        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-top">
            <div class="text-muted small">
                Showing {{ $medicines->firstItem() }}–{{ $medicines->lastItem() }}
                of {{ $medicines->total() }} medicines
            </div>
            {{ $medicines->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-trash-fill me-2"></i>Delete Medicine?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted small" id="deleteLabel">This action cannot be undone.</div>
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
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('deleteForm').action = this.dataset.action;
        document.getElementById('deleteLabel').textContent = 'Delete "' + this.dataset.label + '"?';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
@endpush

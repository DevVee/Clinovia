@extends('layouts.app')

@section('title', 'Low Stock Medicines')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Low Stock Medicines
        </h4>
        <p class="text-muted mb-0 small">Medicines at or below their minimum stock threshold</p>
    </div>
    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>All Medicines
    </a>
</div>

@if ($medicines->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-success">
            <i class="bi bi-shield-check-fill d-block mb-2" style="font-size:3rem;opacity:.5;"></i>
            <h5 class="mb-1">All Medicines are Well-Stocked</h5>
            <p class="text-muted mb-0">No medicines are below their minimum threshold.</p>
        </div>
    </div>
@else

    <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            <strong>{{ $medicines->total() }} medicine(s)</strong> are running low.
            Consider restocking these items through the Inventory module.
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Medicine</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Threshold</th>
                            <th>Unit</th>
                            <th>Expiration</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($medicines as $med)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('medicines.show', $med) }}"
                               class="fw-semibold text-decoration-none">
                                {{ $med->name }}
                            </a>
                            @if ($med->batch_number)
                            <div class="small text-muted font-monospace">{{ $med->batch_number }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $med->category->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="fw-bold fs-6 text-{{ $med->quantity === 0 ? 'danger' : 'warning' }}">
                                {{ $med->quantity }}
                            </span>
                            @if ($med->quantity === 0)
                                <span class="badge bg-danger-subtle text-danger-emphasis ms-1">Out of Stock</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Low</span>
                            @endif
                        </td>
                        <td class="text-muted">≤ {{ $med->low_stock_threshold }}</td>
                        <td class="text-muted small">{{ ucfirst($med->unit) }}</td>
                        <td>
                            @if ($med->expiration_date)
                                <span class="badge bg-{{ $med->is_expired ? 'danger' : 'secondary' }}-subtle
                                                      text-{{ $med->is_expired ? 'danger' : 'secondary' }}-emphasis">
                                    {{ $med->expiration_date->format('M d, Y') }}
                                    @if ($med->is_expired)(Expired)@endif
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('medicines.show', $med) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
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
        </div>
    </div>
@endif
@endsection

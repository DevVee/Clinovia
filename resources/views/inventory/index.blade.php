@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Inventory</h4>
        <p class="text-muted small mb-0">Current medicine stock levels</p>
    </div>
    <div class="d-flex gap-2">
        @can('manage-inventory')
        <a href="{{ route('inventory.stock-in.form') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Stock In
        </a>
        <a href="{{ route('inventory.stock-out.form') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-dash-circle me-1"></i> Stock Out
        </a>
        @endcan
        <a href="{{ route('inventory.transactions') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-clock-history me-1"></i> Ledger
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-5">
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm"
                    placeholder="Search medicine name…">
            </div>
            <div class="col-sm-4">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($category == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Unit</th>
                        <th>Expiry</th>
                        <th class="text-center">Status</th>
                        @can('manage-inventory')
                        <th class="text-center">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $med)
                    @php
                        $stockClass = match(true) {
                            $med->quantity === 0 => 'danger',
                            $med->is_low_stock   => 'warning',
                            default              => 'success',
                        };
                        $stockLabel = match(true) {
                            $med->quantity === 0 => 'Out of Stock',
                            $med->is_low_stock   => 'Low Stock',
                            default              => 'In Stock',
                        };
                        $expiryClass = '';
                        if ($med->expiration_date) {
                            if ($med->is_expired)           $expiryClass = 'text-danger fw-semibold';
                            elseif ($med->is_expiring_soon) $expiryClass = 'text-warning fw-semibold';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $med->name }}</div>
                            @if($med->batch_number)
                                <small class="text-muted">Batch: {{ $med->batch_number }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                {{ $med->category->name ?? '—' }}
                            </span>
                        </td>
                        <td class="text-center fw-bold fs-5">{{ number_format($med->quantity) }}</td>
                        <td class="text-center text-muted small">{{ ucfirst($med->unit) }}</td>
                        <td class="{{ $expiryClass }}">
                            {{ $med->expiration_date ? $med->expiration_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $stockClass }}-subtle text-{{ $stockClass }}-emphasis border border-{{ $stockClass }}-subtle">
                                {{ $stockLabel }}
                            </span>
                        </td>
                        @can('manage-inventory')
                        <td class="text-center">
                            <a href="{{ route('inventory.stock-in.form', ['medicine_id' => $med->id]) }}"
                               class="btn btn-xs btn-outline-success me-1" title="Stock In">
                                <i class="bi bi-plus"></i>
                            </a>
                            <a href="{{ route('inventory.stock-out.form', ['medicine_id' => $med->id]) }}"
                               class="btn btn-xs btn-outline-warning" title="Stock Out">
                                <i class="bi bi-dash"></i>
                            </a>
                        </td>
                        @endcan
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2 opacity-30"></i>
                            No medicines found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($medicines->hasPages())
        <div class="px-3 py-2 border-top">{{ $medicines->links() }}</div>
        @endif
    </div>
</div>
@endsection

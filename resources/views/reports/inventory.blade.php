@extends('layouts.app')

@section('title', 'Inventory Snapshot')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Inventory Snapshot</h4>
        <p class="text-muted small mb-0">Current stock as of {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'inventory') }}?format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'inventory') }}?format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-sm-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-primary">{{ $medicines->count() }}</div>
            <div class="text-muted small">Total Medicines</div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-warning">{{ $lowStock }}</div>
            <div class="text-muted small">Low Stock</div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-danger">{{ $outOfStock }}</div>
            <div class="text-muted small">Out of Stock</div>
        </div>
    </div>
    <div class="col-6 col-sm-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold" style="color:hsl(28,88%,52%)">{{ $expiring }}</div>
            <div class="text-muted small">Expiring Soon</div>
        </div>
    </div>
</div>

{{-- Inventory Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-box-seam me-2"></i> All Medicines
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Unit</th>
                        <th class="text-center">Threshold</th>
                        <th>Expiry</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicines as $m)
                    @php
                        $sc = match(true) {
                            $m->quantity === 0 => ['danger',  'Out of Stock'],
                            $m->is_low_stock   => ['warning', 'Low Stock'],
                            default            => ['success', 'In Stock'],
                        };
                        $ec = '';
                        if ($m->expiration_date) {
                            if ($m->is_expired)           $ec = 'text-danger fw-semibold';
                            elseif ($m->is_expiring_soon) $ec = 'text-warning fw-semibold';
                        }
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $m->name }}</td>
                        <td>{{ $m->category->name ?? '—' }}</td>
                        <td class="text-center fw-bold">{{ number_format($m->quantity) }}</td>
                        <td class="text-center text-muted">{{ ucfirst($m->unit) }}</td>
                        <td class="text-center text-muted">{{ $m->low_stock_threshold }}</td>
                        <td class="{{ $ec }}">
                            {{ $m->expiration_date ? $m->expiration_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $sc[0] }}-subtle text-{{ $sc[0] }}-emphasis">{{ $sc[1] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No medicines found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

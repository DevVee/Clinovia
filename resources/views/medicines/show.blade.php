@extends('layouts.app')

@section('title', $medicine->name)

@section('content')

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-capsule-pill me-2 text-primary"></i>
            {{ $medicine->name }}
        </h4>
        <p class="text-muted mb-0 small">{{ $medicine->category->name ?? '—' }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('update-medicines')
        <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endcan
        <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

@php
    $stockClass = match(true) {
        $medicine->quantity === 0   => 'danger',
        $medicine->is_low_stock     => 'warning',
        default                     => 'success',
    };
    $stockLabel = match(true) {
        $medicine->quantity === 0   => 'Out of Stock',
        $medicine->is_low_stock     => 'Low Stock',
        default                     => 'In Stock',
    };
@endphp

<div class="row g-4">

    {{-- ── Left: Medicine Info ─────────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Stock Status Card --}}
        <div class="card border-0 shadow-sm mb-4 border-{{ $stockClass }}" style="border-width: 1.5px !important;">
            <div class="card-body text-center py-4">
                <div class="display-4 fw-bold text-{{ $stockClass }}">
                    {{ number_format($medicine->quantity) }}
                </div>
                <div class="text-muted small mt-1">{{ ucfirst($medicine->unit) }}s in stock</div>
                <span class="badge bg-{{ $stockClass }}-subtle text-{{ $stockClass }}-emphasis mt-2 px-3 py-2">
                    {{ $stockLabel }}
                </span>
                <div class="mt-3 text-muted small">
                    Low stock alert at ≤ {{ $medicine->low_stock_threshold }} {{ $medicine->unit }}s
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Details
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted ps-3" style="width:45%;">Category</td>
                            <td>{{ $medicine->category->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Unit</td>
                            <td>{{ ucfirst($medicine->unit) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Expiration</td>
                            <td>
                                @if ($medicine->expiration_date)
                                    <span class="badge bg-{{ $medicine->is_expired ? 'danger' : ($medicine->is_expiring_soon ? 'warning' : 'secondary') }}-subtle
                                                          text-{{ $medicine->is_expired ? 'danger' : ($medicine->is_expiring_soon ? 'warning' : 'secondary') }}-emphasis">
                                        {{ $medicine->expiration_date->format('M d, Y') }}
                                        @if ($medicine->is_expired) (Expired) @elseif ($medicine->is_expiring_soon) (Soon) @endif
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Batch / Lot</td>
                            <td>{{ $medicine->batch_number ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Supplier</td>
                            <td>{{ $medicine->supplier ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Status</td>
                            <td>
                                <span class="badge bg-{{ $medicine->is_active ? 'success' : 'secondary' }}-subtle
                                                      text-{{ $medicine->is_active ? 'success' : 'secondary' }}-emphasis">
                                    {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Added By</td>
                            <td class="small">{{ $medicine->createdBy->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-3">Added</td>
                            <td class="small text-muted">{{ $medicine->created_at->diffForHumans() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── Right: Description + Inventory Transactions ─────────────────── --}}
    <div class="col-lg-8">

        @if ($medicine->description)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <i class="bi bi-file-text-fill me-2 text-primary"></i>Description
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $medicine->description }}</p>
            </div>
        </div>
        @endif

        {{-- Inventory Transactions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-arrow-left-right me-2"></i>Recent Stock Movements</span>
                @can('view-inventory')
                <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All Transactions
                </a>
                @endcan
            </div>
            <div class="card-body p-0">
                @forelse ($transactions as $txn)
                @php
                    $txnColor = match($txn->transaction_type) {
                        'stock_in'   => 'success',
                        'stock_out'  => 'warning',
                        'dispensed'  => 'primary',
                        'adjustment' => 'secondary',
                        default      => 'secondary',
                    };
                    $txnIcon = match($txn->transaction_type) {
                        'stock_in'   => 'bi-arrow-down-circle-fill',
                        'stock_out'  => 'bi-arrow-up-circle-fill',
                        'dispensed'  => 'bi-prescription2',
                        'adjustment' => 'bi-pencil-fill',
                        default      => 'bi-circle-fill',
                    };
                @endphp
                <div class="d-flex align-items-center px-3 py-2 border-bottom gap-3">
                    <i class="bi {{ $txnIcon }} text-{{ $txnColor }}" style="font-size:1.2rem;"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ ucfirst(str_replace('_',' ',$txn->transaction_type)) }}</div>
                        <div class="text-muted" style="font-size:.75rem;">
                            {{ $txn->notes ?? 'No notes' }}
                            &mdash; {{ $txn->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-{{ $txn->quantity > 0 ? 'success' : 'danger' }}">
                            {{ $txn->quantity > 0 ? '+' : '' }}{{ $txn->quantity }}
                        </div>
                        <div class="small text-muted">{{ $txn->before_quantity }} → {{ $txn->after_quantity }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-arrow-left-right d-block mb-1 opacity-25" style="font-size:1.8rem;"></i>
                    <small>No stock movements recorded yet.</small>
                    <div class="small mt-1">Stock movements will appear here after Phase 6 (Inventory) is complete.</div>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

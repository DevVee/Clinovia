@extends('layouts.app')

@section('title', 'Inventory Ledger')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Inventory Ledger</h4>
        <p class="text-muted small mb-0">All stock movements — in, out, dispensed, adjustments</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Inventory
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('inventory.transactions') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <input type="text" name="search" value="{{ $filters['search'] }}"
                    class="form-control form-control-sm" placeholder="Medicine name…">
            </div>
            <div class="col-sm-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="stock_in"    @selected($filters['type'] === 'stock_in')>Stock In</option>
                    <option value="stock_out"   @selected($filters['type'] === 'stock_out')>Stock Out</option>
                    <option value="dispensed"   @selected($filters['type'] === 'dispensed')>Dispensed</option>
                    <option value="adjustment"  @selected($filters['type'] === 'adjustment')>Adjustment</option>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}"
                    class="form-control form-control-sm" placeholder="Date from">
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}"
                    class="form-control form-control-sm" placeholder="Date to">
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('inventory.transactions') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Medicine</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Before</th>
                        <th class="text-center">After</th>
                        <th>Batch / Supplier</th>
                        <th>By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    @php
                        $typeCfg = match($tx->transaction_type) {
                            'stock_in'   => ['bg-success-subtle text-success-emphasis',  'bi-plus-circle',    'Stock In'],
                            'stock_out'  => ['bg-warning-subtle text-warning-emphasis',  'bi-dash-circle',    'Stock Out'],
                            'dispensed'  => ['bg-info-subtle text-info-emphasis',        'bi-capsule',        'Dispensed'],
                            'adjustment' => ['bg-secondary-subtle text-secondary-emphasis','bi-arrow-repeat', 'Adjustment'],
                            default      => ['bg-light text-muted',                      'bi-question-circle', ucfirst($tx->transaction_type)],
                        };
                    @endphp
                    <tr>
                        <td class="text-nowrap">
                            {{ $tx->created_at->format('M d, Y') }}<br>
                            <span class="text-muted">{{ $tx->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="fw-semibold">{{ $tx->medicine->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $typeCfg[0] }}">
                                <i class="bi {{ $typeCfg[1] }} me-1"></i>{{ $typeCfg[2] }}
                            </span>
                        </td>
                        <td class="text-center fw-bold
                            {{ in_array($tx->transaction_type, ['stock_in']) ? 'text-success' : 'text-danger' }}">
                            {{ in_array($tx->transaction_type, ['stock_in']) ? '+' : '-' }}{{ $tx->quantity }}
                        </td>
                        <td class="text-center text-muted">{{ $tx->before_quantity }}</td>
                        <td class="text-center fw-semibold">{{ $tx->after_quantity }}</td>
                        <td>
                            @if($tx->batch_number)<div>{{ $tx->batch_number }}</div>@endif
                            @if($tx->supplier)<small class="text-muted">{{ $tx->supplier }}</small>@endif
                        </td>
                        <td>{{ $tx->performedBy->name ?? '—' }}</td>
                        <td class="text-muted" style="max-width:200px;">
                            <span class="text-truncate d-inline-block" style="max-width:180px;"
                                title="{{ $tx->notes }}">{{ $tx->notes ?? '—' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-30"></i>
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-3 py-2 border-top">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection

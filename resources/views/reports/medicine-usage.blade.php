@extends('layouts.app')

@section('title', 'Medicine Usage Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Medicine Usage Report</h4>
        <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($from)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'medicine-usage') }}?from={{ $from }}&to={{ $to }}&format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'medicine-usage') }}?from={{ $from }}&to={{ $to }}&format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Date range picker --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.medicine-usage') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-2"><button class="btn btn-primary btn-sm mt-3">Generate</button></div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-warning">{{ number_format($totalDispensed) }}</div>
            <div class="text-muted small">Total Units Dispensed</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-primary">{{ $usage->count() }}</div>
            <div class="text-muted small">Unique Medicines</div>
        </div>
    </div>
</div>

{{-- Usage Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">
        <i class="bi bi-capsule text-warning me-2"></i> Medicine Dispensing Totals
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Category</th>
                        <th class="text-center">Times Dispensed</th>
                        <th class="text-center">Total Qty</th>
                        <th>Usage Bar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usage as $i => $u)
                    @php $pct = $totalDispensed > 0 ? round(($u->total_dispensed / $totalDispensed) * 100) : 0; @endphp
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $u->medicine->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                {{ $u->medicine->category->name ?? '—' }}
                            </span>
                        </td>
                        <td class="text-center">{{ $u->times_dispensed }}</td>
                        <td class="text-center fw-bold">{{ number_format($u->total_dispensed) }}</td>
                        <td style="min-width:140px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-fill" style="height:6px;">
                                    <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                                </div>
                                <small class="text-muted" style="width:32px;">{{ $pct }}%</small>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-capsule fs-2 d-block mb-2 opacity-30"></i>
                            No medicines dispensed in this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

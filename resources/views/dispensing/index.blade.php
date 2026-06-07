@extends('layouts.app')

@section('title', 'Dispensing Records')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dispensing Records</h4>
        <p class="text-muted small mb-0">All medicines dispensed to patients</p>
    </div>
    @can('create-dispensing')
    <a href="{{ route('dispensing.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-capsule me-1"></i> Dispense Medicine
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('dispensing.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-4">
                <input type="text" name="search" value="{{ $filters['search'] }}"
                    class="form-control form-control-sm" placeholder="Search patient name…">
            </div>
            <div class="col-sm-3">
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-3">
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('dispensing.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Patient</th>
                        <th>Medicine</th>
                        <th class="text-center">Qty</th>
                        <th>Dispensed At</th>
                        <th>Dispensed By</th>
                        <th>Remarks</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $rec->patient->full_name ?? '—' }}</div>
                            <small class="text-muted">{{ $rec->patient->patient_number ?? '' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $rec->medicine->name ?? '—' }}</div>
                            <small class="text-muted">{{ ucfirst($rec->medicine->unit ?? '') }}</small>
                        </td>
                        <td class="text-center fw-bold">{{ $rec->quantity }}</td>
                        <td class="text-nowrap">
                            {{ \Carbon\Carbon::parse($rec->dispensed_at)->format('M d, Y') }}<br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($rec->dispensed_at)->format('h:i A') }}</small>
                        </td>
                        <td>{{ $rec->dispensedBy->name ?? '—' }}</td>
                        <td class="text-muted small" style="max-width:180px;">
                            <span class="text-truncate d-inline-block" style="max-width:160px;"
                                title="{{ $rec->remarks }}">{{ $rec->remarks ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('dispensing.show', $rec) }}" class="btn btn-xs btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-capsule fs-2 d-block mb-2 opacity-30"></i>
                            No dispensing records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="px-3 py-2 border-top">{{ $records->links() }}</div>
        @endif
    </div>
</div>
@endsection

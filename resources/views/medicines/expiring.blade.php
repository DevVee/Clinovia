@extends('layouts.app')

@section('title', 'Expiring Medicines')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0">
            <i class="bi bi-calendar-x-fill me-2 text-danger"></i>Expiring Medicines
        </h4>
        <p class="text-muted mb-0 small">Medicines expiring within {{ $days }} days</p>
    </div>
    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>All Medicines
    </a>
</div>

{{-- Days Filter --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small fw-semibold me-1">Show expiring within:</span>
            @foreach ([7, 14, 30, 60, 90] as $d)
            <a href="{{ route('medicines.expiring', ['days' => $d]) }}"
               class="btn btn-sm {{ $days == $d ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $d }} days
            </a>
            @endforeach
        </div>
    </div>
</div>

@if ($medicines->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-success">
            <i class="bi bi-shield-check-fill d-block mb-2" style="font-size:3rem;opacity:.5;"></i>
            <h5 class="mb-1">No Medicines Expiring Soon</h5>
            <p class="text-muted mb-0">No medicines will expire within {{ $days }} days.</p>
        </div>
    </div>
@else

    <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-calendar-x-fill fs-5"></i>
        <div>
            <strong>{{ $medicines->total() }} medicine(s)</strong> expiring within {{ $days }} days.
            Remove or quarantine expired items and reorder as needed.
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
                            <th>Expiration Date</th>
                            <th>Days Left</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($medicines as $med)
                    @php
                        $daysLeft = now()->diffInDays($med->expiration_date, false);
                        $urgency  = $daysLeft <= 7 ? 'danger' : ($daysLeft <= 14 ? 'warning' : 'secondary');
                    @endphp
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
                            <span class="badge bg-{{ $urgency }}-subtle text-{{ $urgency }}-emphasis">
                                {{ $med->expiration_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold text-{{ $urgency }}">{{ $daysLeft }} days</span>
                        </td>
                        <td>
                            <span class="fw-semibold {{ $med->quantity === 0 ? 'text-danger' : '' }}">
                                {{ number_format($med->quantity) }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ ucfirst($med->unit) }}</td>
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

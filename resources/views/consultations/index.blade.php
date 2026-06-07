@extends('layouts.app')

@section('title', 'Consultations')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-clipboard2-pulse-fill me-2 text-primary"></i>Consultations</h4>
        <p class="text-muted mb-0 small">Medical visit records and clinical notes</p>
    </div>
    @can('create-consultations')
    <a href="{{ route('consultations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill me-1"></i>New Consultation
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('consultations.index') }}" class="row g-2 align-items-end">

            <div class="col-md-4">
                <label class="form-label small mb-1">Search Patient</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Name or patient number…"
                           value="{{ $filters['search'] }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Visit Date</label>
                <input type="date" name="date" class="form-control" value="{{ $filters['date'] }}">
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Nurse / Staff</label>
                <select name="nurse_id" class="form-select">
                    <option value="">All Nurses</option>
                    @foreach ($nurses as $nurse)
                        <option value="{{ $nurse->id }}" {{ $filters['nurseId'] == $nurse->id ? 'selected' : '' }}>
                            {{ $nurse->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if ($filters['search'] || $filters['date'] || $filters['nurseId'])
                <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary" title="Clear">
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
        @if ($consultations->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clipboard2-pulse" style="font-size:3rem;opacity:.3;"></i>
                <p class="mt-2 mb-0">No consultation records found.</p>
                @can('create-consultations')
                <a href="{{ route('consultations.create') }}" class="btn btn-sm btn-primary mt-3">
                    <i class="bi bi-plus-circle-fill me-1"></i>Record Consultation
                </a>
                @endcan
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Patient</th>
                        <th>Visit Date</th>
                        <th>Chief Complaint</th>
                        <th>Diagnosis</th>
                        <th>Nurse / Staff</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($consultations as $consult)
                <tr>
                    <td class="ps-4 text-muted small">{{ $consult->id }}</td>
                    <td>
                        <a href="{{ route('patients.show', $consult->patient) }}"
                           class="fw-semibold text-decoration-none">
                            {{ $consult->patient->full_name }}
                        </a>
                        <div class="small text-muted font-monospace">{{ $consult->patient->patient_number }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $consult->visit_date->format('M d, Y') }}</div>
                        @if ($consult->visit_time)
                        <div class="small text-muted">
                            {{ \Carbon\Carbon::parse($consult->visit_time)->format('h:i A') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span title="{{ $consult->chief_complaint }}">
                            {{ \Illuminate\Support\Str::limit($consult->chief_complaint, 55) }}
                        </span>
                    </td>
                    <td>
                        @if ($consult->diagnosis)
                            <span class="badge bg-info-subtle text-info-emphasis">
                                {{ \Illuminate\Support\Str::limit($consult->diagnosis, 35) }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $consult->nurse->name ?? '—' }}</td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('consultations.show', $consult) }}"
                               class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('update-consultations')
                            <a href="{{ route('consultations.edit', $consult) }}"
                               class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('delete-consultations')
                            <button type="button" class="btn btn-outline-danger btn-delete"
                                    data-action="{{ route('consultations.destroy', $consult) }}"
                                    data-label="Consultation #{{ $consult->id }}"
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
                Showing {{ $consultations->firstItem() }}–{{ $consultations->lastItem() }}
                of {{ $consultations->total() }} records
            </div>
            {{ $consultations->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-trash-fill me-2"></i>Delete Record?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted small" id="deleteLabel">
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
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('deleteForm').action = this.dataset.action;
        document.getElementById('deleteLabel').textContent = 'Delete ' + this.dataset.label + '?';
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
@endpush

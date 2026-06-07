@extends('layouts.app')

@section('title', 'Patients')

@section('breadcrumb')
    <li class="breadcrumb-item active">Patients</li>
@endsection

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Patients</h4>
        <p class="text-muted mb-0 small">Manage all patient records</p>
    </div>
    @can('create-patients')
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i>New Patient
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-2 align-items-end">

            <div class="col-md-4">
                <label class="form-label small mb-1">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                           placeholder="Name, patient no., contact…"
                           value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categoryLabels as $value => $label)
                        <option value="{{ $value }}"
                            {{ ($filters['category'] ?? '') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Sex</label>
                <select name="sex" class="form-select">
                    <option value="">All</option>
                    <option value="male"   {{ ($filters['sex'] ?? '') === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ ($filters['sex'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="is_active" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-funnel-fill"></i>
                </button>
                @if (array_filter($filters))
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary" title="Clear">
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
        @if ($patients->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2 mb-0">No patients found.</p>
                @can('create-patients')
                <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary mt-3">
                    <i class="bi bi-person-plus-fill me-1"></i>Add First Patient
                </a>
                @endcan
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Patient No.</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($patients as $patient)
                <tr>
                    <td class="ps-4">
                        <span class="font-monospace fw-semibold text-primary small">{{ $patient->patient_number }}</span>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                        @if ($patient->email)
                            <div class="small text-muted">{{ $patient->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
                            {{ $categoryLabels[$patient->category] ?? $patient->category }}
                        </span>
                    </td>
                    <td>{{ ucfirst($patient->sex) }}</td>
                    <td>{{ $patient->age }} yrs</td>
                    <td>{{ $patient->contact_number ?? '—' }}</td>
                    <td>
                        @if ($patient->is_active)
                            <span class="badge bg-success-subtle text-success rounded-pill">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger rounded-pill">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('patients.show', $patient) }}"
                               class="btn btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('update-patients')
                            <a href="{{ route('patients.edit', $patient) }}"
                               class="btn btn-outline-secondary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('delete-patients')
                            <button type="button" class="btn btn-outline-danger btn-confirm-delete"
                                    data-action="{{ route('patients.destroy', $patient) }}"
                                    data-name="{{ $patient->full_name }}"
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
                Showing {{ $patients->firstItem() }}–{{ $patients->lastItem() }}
                of {{ $patients->total() }} patients
            </div>
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Patient
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete
                <strong id="deletePatientName"></strong>?
                This action can be undone by an administrator.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-confirm-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('deletePatientName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>
@endpush

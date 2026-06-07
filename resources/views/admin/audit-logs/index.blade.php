@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Audit Logs</h4>
        <p class="text-muted small mb-0">All system activity — who did what, when</p>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="form-control form-control-sm" placeholder="Search description…">
            </div>
            <div class="col-sm-2">
                <select name="module" class="form-select form-select-sm">
                    <option value="">All Modules</option>
                    @foreach(['patients','appointments','consultations','medicines','inventory','dispensing','users','settings','auth'] as $mod)
                    <option value="{{ $mod }}" @selected(request('module') === $mod)>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <select name="action" class="form-select form-select-sm">
                    <option value="">All Actions</option>
                    @foreach(['created','updated','deleted','approved','cancelled','logged_in','logged_out','exported'] as $act)
                    <option value="{{ $act }}" @selected(request('action') === $act)>{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-1">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-2 d-flex gap-1">
                <button class="btn btn-primary btn-sm flex-fill">Filter</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
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
                        <th style="width:145px;">Date & Time</th>
                        <th>User</th>
                        <th class="text-center" style="width:90px;">Action</th>
                        <th class="text-center" style="width:100px;">Module</th>
                        <th>Description</th>
                        <th style="width:110px;">IP Address</th>
                        <th class="text-center" style="width:50px;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $actionColor = match($log->action) {
                            'created'    => 'success',
                            'updated'    => 'warning',
                            'deleted'    => 'danger',
                            'approved'   => 'primary',
                            'cancelled'  => 'danger',
                            'logged_in'  => 'info',
                            'logged_out' => 'secondary',
                            'exported'   => 'primary',
                            default      => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="text-nowrap">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span class="text-muted">{{ $log->created_at->format('h:i:s A') }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $log->user_name ?? 'System' }}</div>
                            @if($log->user)
                            <small class="text-muted">{{ $log->user->email }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $actionColor }}-subtle text-{{ $actionColor }}-emphasis">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="text-muted">{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-center">
                            @if($log->old_values || $log->new_values)
                            <button class="btn btn-xs btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#detailModal"
                                data-old="{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}"
                                data-new="{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}"
                                data-desc="{{ $log->description }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-30"></i>
                            No audit log entries found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-3 py-2 border-top">{{ $logs->links() }}</div>
        @endif
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="modalDesc">Audit Detail</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Before</label>
                        <pre id="oldValues" class="bg-danger-subtle text-danger-emphasis p-3 rounded small" style="max-height:320px; overflow-y:auto; white-space:pre-wrap;"></pre>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted text-uppercase">After</label>
                        <pre id="newValues" class="bg-success-subtle text-success-emphasis p-3 rounded small" style="max-height:320px; overflow-y:auto; white-space:pre-wrap;"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('detailModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('modalDesc').textContent  = btn.dataset.desc;
    document.getElementById('oldValues').textContent  = btn.dataset.old === 'null' ? '(none)' : btn.dataset.old;
    document.getElementById('newValues').textContent  = btn.dataset.new === 'null' ? '(none)' : btn.dataset.new;
});
</script>
@endpush
@endsection

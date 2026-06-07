@extends('layouts.app')

@section('title', 'SMS Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">SMS Logs</h4>
        <p class="text-muted small mb-0">All outgoing SMS messages</p>
    </div>
    @can('send-sms')
    <a href="{{ route('sms.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-send me-1"></i> Send Manual SMS
    </a>
    @endcan
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('sms.index') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <input type="text" name="search" value="{{ $filters['search'] }}"
                    class="form-control form-control-sm" placeholder="Number or recipient name…">
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="sent"    @selected($filters['status'] === 'sent')>Sent</option>
                    <option value="failed"  @selected($filters['status'] === 'failed')>Failed</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_from" value="{{ $filters['dateFrom'] }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-2">
                <input type="date" name="date_to" value="{{ $filters['dateTo'] }}"
                    class="form-control form-control-sm">
            </div>
            <div class="col-sm-3 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('sms.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Recipient</th>
                        <th>Message</th>
                        <th class="text-center">Status</th>
                        <th>Sent By</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $badge = match($log->status) {
                            'sent'    => 'success',
                            'failed'  => 'danger',
                            'pending' => 'warning',
                            default   => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td class="text-nowrap">
                            {{ $log->created_at->format('M d, Y') }}<br>
                            <span class="text-muted">{{ $log->created_at->format('h:i A') }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $log->recipient_number }}</div>
                            @if($log->recipient_name)
                            <small class="text-muted">{{ $log->recipient_name }}</small>
                            @endif
                        </td>
                        <td style="max-width:280px;">
                            <span title="{{ $log->message }}">{{ Str::limit($log->message, 80) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}-emphasis border border-{{ $badge }}-subtle">
                                <i class="bi bi-{{ $log->status === 'sent' ? 'check-circle' : ($log->status === 'failed' ? 'x-circle' : 'clock') }} me-1"></i>
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td>{{ $log->createdBy->name ?? 'System' }}</td>
                        <td class="text-muted text-danger" style="max-width:160px;">
                            @if($log->error_message)
                            <span title="{{ $log->error_message }}">{{ Str::limit($log->error_message, 50) }}</span>
                            @else
                            —
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-dots fs-2 d-block mb-2 opacity-30"></i>
                            No SMS logs found.
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
@endsection

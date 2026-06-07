@extends('layouts.app')

@section('title', 'Monthly Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Monthly Report</h4>
        <p class="text-muted small mb-0">
            {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'monthly') }}?year={{ $year }}&month={{ $month }}&format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'monthly') }}?year={{ $year }}&month={{ $month }}&format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.monthly') }}" class="row g-2 align-items-end">
            <div class="col-sm-3">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" @selected($month == $m)>
                        {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach(range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2"><button class="btn btn-primary btn-sm mt-3">Generate</button></div>
        </form>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-primary">{{ number_format($totalConsultations) }}</div>
            <div class="text-muted small">Total Consultations</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-success">{{ number_format($totalPatients) }}</div>
            <div class="text-muted small">Unique Patients</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="display-6 fw-bold text-info">{{ number_format($totalAppointments) }}</div>
            <div class="text-muted small">Appointments</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                Daily Consultations — {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
            </div>
            <div class="card-body">
                <canvas id="dailyChart" style="max-height:300px;"></canvas>
            </div>
        </div>
    </div>

    {{-- By Category --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">By Patient Category</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Category</th><th class="text-end">Count</th></tr></thead>
                    <tbody>
                        @forelse($byCategory as $cat)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $cat->category)) }}</td>
                            <td class="text-end fw-semibold">{{ $cat->total }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">No data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const daysInMonth = {{ \Carbon\Carbon::create($year, $month, 1)->daysInMonth }};
const labels = Array.from({length: daysInMonth}, (_, i) => i + 1);
const consultData = @json($consultationsByDay);
const dataArr = labels.map(d => consultData[d] || 0);

new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Consultations',
            data: dataArr,
            backgroundColor: 'hsl(201,85%,39%,0.75)',
            borderColor: 'hsl(201,85%,39%)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } },
        }
    }
});
</script>
@endpush
@endsection

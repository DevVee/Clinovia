@extends('layouts.app')

@section('title', 'Annual Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Annual Report</h4>
        <p class="text-muted small mb-0">Year {{ $year }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Reports
        </a>
        <a href="{{ route('reports.export', 'annual') }}?year={{ $year }}&format=pdf" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('reports.export', 'annual') }}?year={{ $year }}&format=csv" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
        </a>
    </div>
</div>

{{-- Year picker --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('reports.annual') }}" class="row g-2 align-items-end">
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
            <div class="text-muted small">Total Appointments</div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom fw-semibold">Monthly Trend — {{ $year }}</div>
    <div class="card-body">
        <canvas id="annualChart" style="max-height:320px;"></canvas>
    </div>
</div>

{{-- Monthly Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom fw-semibold">Month-by-Month Breakdown</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th class="text-center">Consultations</th>
                        <th class="text-center">Appointments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $row)
                    <tr>
                        <td class="fw-semibold">{{ $row['month'] }}</td>
                        <td class="text-center">{{ number_format($row['consultations']) }}</td>
                        <td class="text-center">{{ number_format($row['appointments']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td>Total</td>
                        <td class="text-center">{{ number_format($totalConsultations) }}</td>
                        <td class="text-center">{{ number_format($totalAppointments) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthly = @json($monthlyData);
const labels  = monthly.map(r => r.month);
new Chart(document.getElementById('annualChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Consultations',
                data: monthly.map(r => r.consultations),
                backgroundColor: 'hsl(201,85%,39%,0.75)',
                borderColor: 'hsl(201,85%,39%)',
                borderWidth: 1, borderRadius: 4,
            },
            {
                label: 'Appointments',
                data: monthly.map(r => r.appointments),
                backgroundColor: 'hsl(144,100%,39%,0.65)',
                borderColor: 'hsl(144,100%,39%)',
                borderWidth: 1, borderRadius: 4,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } },
        }
    }
});
</script>
@endpush
@endsection

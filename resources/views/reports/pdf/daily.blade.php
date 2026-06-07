<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a2e; margin: 0; padding: 24px; }
    h1   { font-size: 18px; margin: 0 0 4px; color: #0a3d62; }
    p    { margin: 0 0 16px; color: #555; font-size: 10px; }
    .header { border-bottom: 2px solid #0a3d62; padding-bottom: 8px; margin-bottom: 16px; }
    .meta   { font-size: 10px; color: #777; text-align: right; margin-top: -36px; }
    table   { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th      { background: #0a3d62; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td      { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; font-size: 10px; }
    tr:nth-child(even) td { background: #f7f9fc; }
    .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 6px; color: #0a3d62; }
    .stats { display: flex; gap: 16px; margin-bottom: 16px; }
    .stat  { background: #f0f4f8; border-radius: 6px; padding: 8px 16px; text-align: center; min-width: 80px; }
    .stat-num { font-size: 22px; font-weight: bold; color: #0a3d62; }
    .stat-lbl { font-size: 9px; color: #666; }
</style>
</head>
<body>
<div class="header">
    <h1>Clinovia — Daily Report</h1>
    <p>{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</p>
</div>
<div class="meta">Generated: {{ now()->format('M d, Y h:i A') }}</div>

<table style="width:auto; margin-bottom:20px;">
    <tr>
        <td style="border:none; padding:4px 16px 4px 0;">
            <div class="stat-num">{{ $consultations->count() }}</div>
            <div class="stat-lbl">Consultations</div>
        </td>
        <td style="border:none; padding:4px 16px 4px 0;">
            <div class="stat-num">{{ $appointments->count() }}</div>
            <div class="stat-lbl">Appointments</div>
        </td>
        <td style="border:none; padding:4px 16px 4px 0;">
            <div class="stat-num">{{ $dispensed->count() }}</div>
            <div class="stat-lbl">Dispensed</div>
        </td>
    </tr>
</table>

<div class="section-title">Consultations</div>
<table>
    <thead><tr><th>Time</th><th>Patient</th><th>Chief Complaint</th><th>Nurse</th></tr></thead>
    <tbody>
        @forelse($consultations as $c)
        <tr>
            <td>{{ $c->visit_time ? \Carbon\Carbon::parse($c->visit_time)->format('h:i A') : '—' }}</td>
            <td>{{ $c->patient->full_name ?? '—' }}</td>
            <td>{{ $c->chief_complaint }}</td>
            <td>{{ $c->nurse->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; color:#999;">No consultations.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="section-title">Medicines Dispensed</div>
<table>
    <thead><tr><th>Patient</th><th>Medicine</th><th>Qty</th><th>Dispensed By</th></tr></thead>
    <tbody>
        @forelse($dispensed as $d)
        <tr>
            <td>{{ $d->patient->full_name ?? '—' }}</td>
            <td>{{ $d->medicine->name ?? '—' }}</td>
            <td>{{ $d->quantity }}</td>
            <td>{{ $d->dispensedBy->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; color:#999;">No medicines dispensed.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>

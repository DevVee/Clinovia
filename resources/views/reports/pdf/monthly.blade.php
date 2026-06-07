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
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th    { background: #0a3d62; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td    { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
    tr:nth-child(even) td { background: #f7f9fc; }
    .section-title { font-size: 13px; font-weight: bold; margin: 12px 0 6px; color: #0a3d62; }
</style>
</head>
<body>
<div class="header">
    <h1>Clinovia — Monthly Report</h1>
    <p>{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</p>
</div>
<div class="meta">Generated: {{ now()->format('M d, Y h:i A') }}</div>

<table style="width:auto; margin-bottom:20px;">
    <tr>
        <td style="border:none; padding:4px 16px 4px 0;">Total Consultations: <strong>{{ $totalConsultations }}</strong></td>
        <td style="border:none; padding:4px 16px 4px 0;">Unique Patients: <strong>{{ $totalPatients }}</strong></td>
        <td style="border:none; padding:4px 16px 4px 0;">Appointments: <strong>{{ $totalAppointments }}</strong></td>
    </tr>
</table>

<div class="section-title">Daily Consultations</div>
<table>
    <thead><tr><th>Day</th><th>Consultations</th></tr></thead>
    <tbody>
        @foreach($consultationsByDay as $day => $count)
        <tr><td>{{ $day }}</td><td>{{ $count }}</td></tr>
        @endforeach
    </tbody>
</table>

<div class="section-title">By Patient Category</div>
<table>
    <thead><tr><th>Category</th><th>Count</th></tr></thead>
    <tbody>
        @foreach($byCategory as $cat)
        <tr><td>{{ ucfirst(str_replace('_', ' ', $cat->category)) }}</td><td>{{ $cat->total }}</td></tr>
        @endforeach
    </tbody>
</table>
</body>
</html>

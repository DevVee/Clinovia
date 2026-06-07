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
    tfoot td { font-weight: bold; background: #e8eef5; }
</style>
</head>
<body>
<div class="header">
    <h1>Clinovia — Annual Report</h1>
    <p>Year {{ $year }}</p>
</div>
<div class="meta">Generated: {{ now()->format('M d, Y h:i A') }}</div>

<p>
    Total Consultations: <strong>{{ $totalConsultations }}</strong> &nbsp;|&nbsp;
    Total Appointments: <strong>{{ $totalAppointments }}</strong> &nbsp;|&nbsp;
    Unique Patients: <strong>{{ $totalPatients }}</strong>
</p>

<table>
    <thead><tr><th>Month</th><th>Consultations</th><th>Appointments</th></tr></thead>
    <tbody>
        @foreach($monthlyData as $row)
        <tr>
            <td>{{ $row['month'] }}</td>
            <td>{{ $row['consultations'] }}</td>
            <td>{{ $row['appointments'] }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Total</td>
            <td>{{ $totalConsultations }}</td>
            <td>{{ $totalAppointments }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>

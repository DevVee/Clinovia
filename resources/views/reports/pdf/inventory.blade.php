<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; margin: 0; padding: 24px; }
    h1   { font-size: 18px; margin: 0 0 4px; color: #0a3d62; }
    p    { margin: 0 0 16px; color: #555; font-size: 10px; }
    .header { border-bottom: 2px solid #0a3d62; padding-bottom: 8px; margin-bottom: 16px; }
    .meta   { font-size: 10px; color: #777; text-align: right; margin-top: -36px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th    { background: #0a3d62; color: #fff; padding: 5px 6px; text-align: left; font-size: 9px; }
    td    { padding: 4px 6px; border-bottom: 1px solid #e5e5e5; }
    tr:nth-child(even) td { background: #f7f9fc; }
    .badge-danger   { color: #dc3545; font-weight: bold; }
    .badge-warning  { color: #c97a00; font-weight: bold; }
    .badge-success  { color: #198754; }
</style>
</head>
<body>
<div class="header">
    <h1>Clinovia — Inventory Snapshot</h1>
    <p>As of {{ now()->format('F d, Y h:i A') }}</p>
</div>
<div class="meta">
    Total: {{ $medicines->count() }} &nbsp;|&nbsp;
    Low Stock: {{ $lowStock }} &nbsp;|&nbsp;
    Out of Stock: {{ $outOfStock }} &nbsp;|&nbsp;
    Expiring: {{ $expiring }}
</div>
<br>
<table>
    <thead>
        <tr><th>Medicine</th><th>Category</th><th>Qty</th><th>Unit</th><th>Threshold</th><th>Expiry</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($medicines as $m)
        @php
            $status = $m->quantity === 0 ? 'Out of Stock' : ($m->is_low_stock ? 'Low Stock' : 'In Stock');
            $cls    = $m->quantity === 0 ? 'badge-danger' : ($m->is_low_stock ? 'badge-warning' : 'badge-success');
        @endphp
        <tr>
            <td>{{ $m->name }}</td>
            <td>{{ $m->category->name ?? '—' }}</td>
            <td>{{ $m->quantity }}</td>
            <td>{{ $m->unit }}</td>
            <td>{{ $m->low_stock_threshold }}</td>
            <td>{{ $m->expiration_date ? $m->expiration_date->format('M d, Y') : '—' }}</td>
            <td class="{{ $cls }}">{{ $status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Page Not Found | Clinovia</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body { background: hsl(210,18%,96%); display:flex; align-items:center; justify-content:center; min-height:100vh; }
.error-icon { font-size: 5rem; color: hsl(201,85%,39%); opacity:.7; }
.error-code { font-size: 6rem; font-weight: 900; color: hsl(201,85%,39%); line-height:1; }
</style>
</head>
<body>
<div class="text-center px-4">
    <div class="error-code">404</div>
    <i class="bi bi-search error-icon d-block mb-3"></i>
    <h2 class="fw-bold mb-2">Page Not Found</h2>
    <p class="text-muted mb-4">The page you're looking for doesn't exist or has been moved.<br>
        Double-check the URL or navigate using the links below.</p>
    <div class="d-flex gap-2 justify-content-center">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house me-1"></i> Dashboard
        </a>
    </div>
    <div class="mt-4 text-muted small">Clinovia — Smart School Clinic Management System</div>
</div>
</body>
</html>

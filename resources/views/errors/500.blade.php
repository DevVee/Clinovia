<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>500 — Server Error | Clinovia</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body { background: hsl(210,18%,96%); display:flex; align-items:center; justify-content:center; min-height:100vh; }
.error-icon { font-size: 5rem; color: hsl(28,88%,52%); opacity:.8; }
.error-code { font-size: 6rem; font-weight: 900; color: hsl(28,88%,52%); line-height:1; }
</style>
</head>
<body>
<div class="text-center px-4">
    <div class="error-code">500</div>
    <i class="bi bi-exclamation-triangle-fill error-icon d-block mb-3"></i>
    <h2 class="fw-bold mb-2">Something Went Wrong</h2>
    <p class="text-muted mb-4">An internal server error occurred.<br>
        Our team has been notified. Please try again in a moment.</p>
    <div class="d-flex gap-2 justify-content-center">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Try Again
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-house me-1"></i> Dashboard
        </a>
    </div>
    <div class="mt-4 text-muted small">Clinovia — Smart School Clinic Management System</div>
</div>
</body>
</html>

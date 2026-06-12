<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="5; url={{ route('login') }}">
<title>Session Expired | Clinovia</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
body {
    background: hsl(210,18%,96%);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}
.error-card {
    background: #fff;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    max-width: 460px;
    width: 100%;
    text-align: center;
    box-shadow: 0 8px 40px rgba(0,0,0,.08);
}
.error-icon-wrap {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, hsl(38,95%,52%), hsl(38,95%,40%));
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.25rem;
    box-shadow: 0 6px 20px rgba(245,158,11,.3);
}
.error-icon-wrap i { font-size: 2rem; color: #fff; }
.progress-bar-wrap {
    height: 4px;
    background: hsl(210,17%,91%);
    border-radius: 4px;
    overflow: hidden;
    margin: 1.25rem 0 1rem;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, hsl(201,85%,39%), hsl(265,58%,54%));
    border-radius: 4px;
    animation: countdown 5s linear forwards;
}
@keyframes countdown {
    from { width: 100%; }
    to   { width: 0%; }
}
</style>
</head>
<body>
<div class="error-card">
    <div class="error-icon-wrap">
        <i class="bi bi-clock-history"></i>
    </div>

    <h2 class="fw-bold mb-2" style="font-size:1.4rem;">Session Expired</h2>
    <p class="text-muted mb-0" style="font-size:.9rem; line-height:1.6;">
        Your session timed out or expired — this can happen after periods of
        inactivity or a server restart. Your data is safe.
    </p>

    <div class="progress-bar-wrap">
        <div class="progress-bar-fill"></div>
    </div>

    <p class="text-muted mb-3" style="font-size:.8rem;">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Redirecting you to the login page in <strong id="countdown">5</strong> seconds…
    </p>

    <a href="{{ route('login') }}" class="btn btn-primary w-100" style="border-radius:10px;">
        <i class="bi bi-box-arrow-in-right me-2"></i>Log In Now
    </a>

    <div class="mt-3 text-muted" style="font-size:.73rem;">
        Clinovia — Smart School Clinic Management System
    </div>
</div>

<script>
var count = 5;
var el = document.getElementById('countdown');
var t = setInterval(function () {
    count--;
    if (el) el.textContent = count;
    if (count <= 0) {
        clearInterval(t);
        window.location.href = '{{ route("login") }}';
    }
}, 1000);
</script>
</body>
</html>

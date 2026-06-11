<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Clinovia</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="/clinovia-icon.svg">
    <link rel="shortcut icon" href="/clinovia-icon.svg">

    {{-- Google Fonts: Inter (body) + Poppins (headings/brand) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons bundled locally via Vite (no CDN) --}}

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

{{-- ─────────────────────────────────────────────────────────────────────────── --}}
{{-- ①  TOP HEADER BAR  — 40px gradient bar: pulsing dot · live clock · school  --}}
{{-- ─────────────────────────────────────────────────────────────────────────── --}}
<div class="top-header">
    <div class="clinic-status">
        <span class="status-indicator"></span>
        <span class="status-text">{{ \App\Models\Setting::get('clinic_status_text', 'Clinic Online') }}</span>
        <span class="separator">•</span>
        <span id="currentTime"></span>
    </div>
    <div class="top-header-right d-none d-md-block">
        {{ \App\Models\Setting::get('clinic_name', 'School Clinic') }}
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────────────────── --}}
{{-- ②  MAIN NAVBAR  — 70px white bar: logo · brand · dark toggle · Cobi · user --}}
{{-- ─────────────────────────────────────────────────────────────────────────── --}}
<nav class="main-navbar">

    {{-- Mobile sidebar hamburger --}}
    <button class="sidebar-toggle-btn d-lg-none" id="mobileSidebarToggle" aria-label="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>

    {{-- Brand --}}
    <div class="navbar-brand-container">
        <div class="brand-logo">
            <img src="/clinovia-icon.svg" alt="Clinovia" style="width:34px;height:34px;display:block;">
        </div>
        <div class="brand-info">
            <h1 class="brand-title">Clinovia</h1>
            <span class="brand-subtitle">Smart School Clinic Management System</span>
        </div>
    </div>

    <div class="flex-grow-1"></div>

    {{-- Ask Cobi AI --}}
    <button class="ask-ai-btn" id="askAiBtn">
        <i class="bi bi-stars ask-ai-icon"></i>
        <span class="ask-ai-text">Ask Cobi</span>
    </button>

    {{-- Profile Section --}}
    <div class="profile-section">
        <div class="profile-greeting">
            <span class="greeting-text" id="greetingText">Good Morning</span>
            <span class="user-name">{{ auth()->user()->name }}</span>
        </div>

        <div class="dropdown">
            <button class="profile-trigger" id="profileDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <div class="profile-avatar">
                    <img src="{{ auth()->user()->avatarUrl() }}"
                         alt="{{ auth()->user()->name }}"
                         class="profile-avatar-img"
                         style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    <div class="online-indicator"></div>
                </div>
                <i class="bi bi-chevron-down dropdown-arrow"></i>
            </button>
            <ul class="dropdown-menu profile-menu dropdown-menu-end shadow-lg"
                aria-labelledby="profileDropdown">
                <li class="profile-header">
                    <div style="display:flex;align-items:center;gap:.65rem;">
                        <img src="{{ auth()->user()->avatarUrl() }}"
                             alt="{{ auth()->user()->name }}"
                             style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #e0e7ff;">
                        <div class="profile-info">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}</small>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person-circle me-2 text-primary"></i>My Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider m-0"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout-item">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- ─────────────────────────────────────────────────────────────────────────── --}}
{{-- PAGE BODY — sidebar (left) + main content (right)                          --}}
{{-- ─────────────────────────────────────────────────────────────────────────── --}}
<div class="page-body">

    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    {{-- ③  SIDEBAR  — white 260px · profile card · coloured nav icon squares   --}}
    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">

        {{-- Profile Card --}}
        <div class="sidebar-header">
            <a href="{{ route('profile.edit') }}" style="text-decoration:none;">
                <div class="profile-image-container">
                    <div class="sidebar-avatar" style="overflow:hidden;">
                        <img src="{{ auth()->user()->avatarUrl() }}"
                             alt="{{ auth()->user()->name }}"
                             style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                    </div>
                    <div class="status-dot"></div>
                </div>
            </a>
            <p class="sidebar-user-name">{{ auth()->user()->name }}</p>
            <small class="sidebar-user-role">
                {{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'User') }}
            </small>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            {{-- Main Menu --}}
            <div class="nav-section">Main Menu</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-fill nav-icon nav-icon-blue"></i>
                <span>Dashboard</span>
            </a>

            {{-- ── CLINIC LOG — main feature ───────────────────────────────── --}}
            @can('view-consultations')
            <div class="nav-section">Clinic Logbook</div>

            <a href="{{ route('patient-logs.index') }}"
               class="nav-link {{ request()->routeIs('patient-logs.*') ? 'active' : '' }}">
                <i class="bi bi-journal-medical nav-icon nav-icon-red"></i>
                <span>Daily Patient Log</span>
                @php $todayLogCount = \App\Models\PatientLog::today()->count(); @endphp
                @if($todayLogCount > 0)
                    <span class="ms-auto nav-badge">{{ $todayLogCount }}</span>
                @endif
            </a>
            @endcan

            {{-- Patient Care --}}
            @canany(['view-patients', 'view-appointments', 'view-consultations'])
            <div class="nav-section">Patient Care</div>
            @endcanany

            @can('view-patients')
            <a href="{{ route('patients.index') }}"
               class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="bi bi-person-lines-fill nav-icon nav-icon-green"></i>
                <span>Patient Records</span>
            </a>
            @endcan

            @can('view-appointments')
            <a href="{{ route('appointments.index') }}"
               class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill nav-icon nav-icon-purple"></i>
                <span>Appointments</span>
                @php $pendingCount = \App\Models\Appointment::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ms-auto nav-badge">{{ $pendingCount }}</span>
                @endif
            </a>
            @endcan

            @can('view-consultations')
            <a href="{{ route('consultations.index') }}"
               class="nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse-fill nav-icon nav-icon-blue"></i>
                <span>Consultations</span>
            </a>
            @endcan

            {{-- Pharmacy --}}
            @canany(['view-medicines', 'view-inventory', 'view-dispensing'])
            <div class="nav-section">Pharmacy</div>
            @endcanany

            @can('view-medicines')
            <a href="{{ route('medicines.index') }}"
               class="nav-link {{ request()->routeIs('medicines.*') && !request()->routeIs('medicine-categories.*') ? 'active' : '' }}">
                <i class="bi bi-capsule nav-icon nav-icon-orange"></i>
                <span>Medicines</span>
            </a>
            <a href="{{ route('medicine-categories.index') }}"
               class="nav-link nav-link-sub {{ request()->routeIs('medicine-categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags nav-icon"></i>
                <span>Categories</span>
            </a>
            @endcan

            @can('view-inventory')
            <a href="{{ route('inventory.index') }}"
               class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill nav-icon nav-icon-teal"></i>
                <span>Inventory</span>
            </a>
            @endcan

            @can('view-dispensing')
            <a href="{{ route('dispensing.index') }}"
               class="nav-link {{ request()->routeIs('dispensing.*') ? 'active' : '' }}">
                <i class="bi bi-prescription2 nav-icon nav-icon-indigo"></i>
                <span>Dispensing</span>
            </a>
            @endcan

            {{-- Analytics --}}
            @canany(['view-reports', 'view-sms', 'use-ai-assistant'])
            <div class="nav-section">Analytics</div>
            @endcanany

            @can('view-reports')
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill nav-icon nav-icon-primary"></i>
                <span>Reports</span>
            </a>
            @endcan

            @can('view-sms')
            <a href="{{ route('sms.index') }}"
               class="nav-link {{ request()->routeIs('sms.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots-fill nav-icon nav-icon-blue"></i>
                <span>SMS Logs</span>
            </a>
            @endcan

            @can('use-ai-assistant')
            <a href="{{ route('ai-assistant.index') }}"
               class="nav-link {{ request()->routeIs('ai-assistant.*') ? 'active' : '' }}">
                <i class="bi bi-stars nav-icon nav-icon-violet"></i>
                <span>Ask Cobi</span>
                <span class="ms-auto nav-badge nav-badge-ai">AI</span>
            </a>
            @endcan

            {{-- System --}}
            @role('administrator')
            <div class="nav-section">System</div>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill nav-icon nav-icon-slate"></i>
                <span>Users</span>
            </a>

            <a href="{{ route('admin.roles.index') }}"
               class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill nav-icon nav-icon-red"></i>
                <span>Roles &amp; Permissions</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill nav-icon nav-icon-slate"></i>
                <span>Settings</span>
            </a>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text nav-icon nav-icon-slate"></i>
                <span>Audit Logs</span>
            </a>
            @endrole

        </nav>
    </aside>

    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    {{-- ④  MAIN CONTENT AREA                                                   --}}
    {{-- ─────────────────────────────────────────────────────────────────────── --}}
    <div class="main-content">
        <main class="content-area">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-top py-3 px-4 mt-auto">
            <p class="mb-0" style="font-size:.72rem;">
                &copy; {{ date('Y') }} <strong>Clinovia</strong> &mdash; Smart School Clinic Management System
            </p>
        </footer>
    </div>

</div>{{-- .page-body --}}

{{-- Mobile sidebar overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

@stack('scripts')

<script>
(function () {
    'use strict';

    /* ── 1. Live clock in top bar ───────────────────────────────────────── */
    function tick() {
        var el = document.getElementById('currentTime');
        if (el) el.textContent = new Date().toLocaleTimeString('en-US', {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    setInterval(tick, 1000);
    tick();

    /* ── 2. Time-aware greeting ─────────────────────────────────────────── */
    (function () {
        var h = new Date().getHours();
        var g = h < 12 ? 'Good Morning' : h < 18 ? 'Good Afternoon' : 'Good Evening';
        ['greetingText', 'dashGreeting'].forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.textContent = g;
        });
    })();

    /* ── 3. Mobile sidebar toggle ───────────────────────────────────────── */
    var sidebar   = document.getElementById('sidebar');
    var overlay   = document.getElementById('sidebarOverlay');
    var mobileBtn = document.getElementById('mobileSidebarToggle');

    if (mobileBtn) {
        mobileBtn.addEventListener('click', function () {
            if (sidebar)  sidebar.classList.toggle('show');
            if (overlay)  overlay.classList.toggle('show');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function () {
            if (sidebar)  sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    /* ── 4. Auto-dismiss success alerts after 4 s ───────────────────────── */
    setTimeout(function () {
        document.querySelectorAll('.alert-success').forEach(function (el) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }
        });
    }, 4000);

    /* ── 5. Ask Cobi button ─────────────────────────────────────────────── */
    var askBtn = document.getElementById('askAiBtn');
    if (askBtn) {
        askBtn.addEventListener('click', function () {
            try { window.location.href = '{{ route("ai-assistant.index") }}'; } catch(e) {}
        });
    }

    /* ── 6. Keep-alive heartbeat — prevents Render free tier from sleeping ──
       Pings /ping every 10 minutes while this tab is open.
       Silent fetch — no UI effect, no auth required on that route.       */
    setInterval(function () {
        fetch('/ping', { method: 'GET', cache: 'no-store' }).catch(function () {});
    }, 10 * 60 * 1000);

})();
</script>
</body>
</html>

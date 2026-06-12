<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispensingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MedicineCategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

// ─── Keep-alive ping (no session/auth/DB overhead) ──────────────────────────
// Hit by: Render cron job, UptimeRobot, GitHub Actions heartbeat.
// Returns plain text 'pong' — no middleware stack, no session creation.
Route::get('/ping', fn () => response('pong', 200)->header('Content-Type', 'text/plain'));

// ─── Session keep-alive + CSRF token refresh ──────────────────────────────────
// Called by the frontend every 20 minutes from active browser tabs.
// Two purposes:
//   1. Extends the session lifetime (any auth request resets the session TTL)
//   2. Returns a fresh CSRF token so forms on long-lived tabs don't 419
// Must be auth-protected so it reads/touches the real session.
Route::middleware(['auth'])->get('/session/token', function () {
    return response()->json(['token' => csrf_token()]);
})->name('session.token');

// ─── Rich health check endpoint ───────────────────────────────────────────────
// Checks DB connectivity, cache, and storage writability.
// Excluded from session middleware to avoid creating ghost sessions.
// Use this URL in Render's healthCheckPath and as UptimeRobot alert monitor.
Route::get('/health', function () {
    $checks   = ['status' => 'ok'];
    $httpCode = 200;

    // Database connectivity
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks['database'] = 'ok';
    } catch (\Exception $e) {
        $checks['database']       = 'error';
        $checks['database_error'] = $e->getMessage();
        $httpCode = 503;
    }

    // Cache read/write
    try {
        \Illuminate\Support\Facades\Cache::put('_health_check', 1, 10);
        $checks['cache'] = \Illuminate\Support\Facades\Cache::get('_health_check') ? 'ok' : 'miss';
    } catch (\Exception $e) {
        $checks['cache'] = 'error';
        $httpCode = 503;
    }

    // Storage writable
    $storageOk = is_writable(storage_path('framework'));
    $checks['storage_writable'] = $storageOk ? 'ok' : 'error';
    if (! $storageOk) {
        $httpCode = 503;
    }

    // Runtime info (safe to expose — no secrets)
    $checks['session_driver'] = config('session.driver');
    $checks['cache_driver']   = config('cache.default');
    $checks['app_env']        = config('app.env');
    $checks['timestamp']      = now()->toIso8601String();

    if ($checks['status'] === 'ok' && $httpCode !== 200) {
        $checks['status'] = 'degraded';
    }

    return response()->json($checks, $httpCode);
})->withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class]);

// ─── Public: redirect to login ────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'check.active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Profile ──────────────────────────────────────────────────────────────
    Route::get('/profile',              [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',              [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar',      [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar',    [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile',           [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Patient Log (Clinic Logbook) ─────────────────────────────────────────
    Route::resource('patient-logs', PatientLogController::class)
         ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ─── Patients ─────────────────────────────────────────────────────────────
    Route::resource('patients', PatientController::class);

    Route::get('patients/{patient}/history', [PatientController::class, 'history'])
         ->name('patients.history');

    // MED-7 FIX: Restore soft-deleted patient (admin-only, withTrashed binding)
    Route::patch('patients/{patient}/restore', [PatientController::class, 'restore'])
         ->name('patients.restore')
         ->withTrashed();

    // ─── Appointments ─────────────────────────────────────────────────────────
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/approve',  [AppointmentController::class, 'approve'])
         ->name('appointments.approve');
    Route::patch('appointments/{appointment}/cancel',   [AppointmentController::class, 'cancel'])
         ->name('appointments.cancel');
    Route::patch('appointments/{appointment}/no-show',  [AppointmentController::class, 'noShow'])
         ->name('appointments.no-show');
    Route::patch('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
         ->name('appointments.complete');

    // ─── Consultations ────────────────────────────────────────────────────────
    Route::resource('consultations', ConsultationController::class);

    // ─── Medicines ────────────────────────────────────────────────────────────
    // Named routes BEFORE resource() to avoid route collision with {medicine} param
    Route::get('medicines/low-stock', [MedicineController::class, 'lowStock'])->name('medicines.low-stock');
    Route::get('medicines/expiring',  [MedicineController::class, 'expiring'])->name('medicines.expiring');
    Route::resource('medicines', MedicineController::class);

    // ─── Medicine Categories ──────────────────────────────────────────────────
    Route::resource('medicine-categories', MedicineCategoryController::class)
         ->only(['index', 'store', 'edit', 'update', 'destroy']);

    // ─── Inventory ────────────────────────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/',             [InventoryController::class, 'index'])->name('index');
        Route::get('/transactions', [InventoryController::class, 'transactions'])->name('transactions');
        Route::get('/stock-in',     [InventoryController::class, 'stockInForm'])->name('stock-in.form');
        Route::post('/stock-in',    [InventoryController::class, 'stockIn'])->name('stock-in');
        Route::get('/stock-out',    [InventoryController::class, 'stockOutForm'])->name('stock-out.form');
        Route::post('/stock-out',   [InventoryController::class, 'stockOut'])->name('stock-out');
    });

    // ─── Dispensing ───────────────────────────────────────────────────────────
    Route::resource('dispensing', DispensingController::class)
         ->only(['index', 'create', 'store', 'show']);

    // ─── Reports ──────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',               [ReportController::class, 'index'])->name('index');
        Route::get('/daily',          [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly',        [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/annual',         [ReportController::class, 'annual'])->name('annual');
        Route::get('/medicine-usage', [ReportController::class, 'medicineUsage'])->name('medicine-usage');
        Route::get('/inventory',      [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/appointments',   [ReportController::class, 'appointments'])->name('appointments');

        // HIGH-3 FIX: Rate-limit exports to prevent DoS / report-scraping.
        // 20 exports per minute per authenticated user is generous for legitimate use.
        Route::get('/export/{type}', [ReportController::class, 'export'])
             ->name('export')
             ->middleware('throttle:20,1');
    });

    // ─── SMS ──────────────────────────────────────────────────────────────────
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('/',    [SmsController::class, 'index'])->name('index');
        Route::get('/send', [SmsController::class, 'create'])->name('create');

        // HIGH-3 FIX: Limit manual SMS sends to prevent API credit abuse / spam.
        Route::post('/send', [SmsController::class, 'send'])
             ->name('send')
             ->middleware('throttle:15,1');
    });

    // ─── AI Assistant ─────────────────────────────────────────────────────────
    Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
        Route::get('/',      [AiAssistantController::class, 'index'])->name('index');
        Route::delete('/clear', [AiAssistantController::class, 'clear'])->name('clear');

        // HIGH-3 FIX: Limit AI chat to 30 messages/minute to protect Groq API quota.
        Route::post('/chat', [AiAssistantController::class, 'chat'])
             ->name('chat')
             ->middleware('throttle:30,1');
    });

    // ─── Admin ────────────────────────────────────────────────────────────────
    Route::middleware('role:administrator')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);

        Route::get('settings',  [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

require __DIR__.'/auth.php';

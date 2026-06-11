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

// ─── Keep-alive ping (public, no session/auth overhead) ──────────────────────
// Hit by the Render cron job + browser heartbeat to prevent free-tier sleep.
Route::get('/ping', fn () => response('pong', 200)->header('Content-Type', 'text/plain'));

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

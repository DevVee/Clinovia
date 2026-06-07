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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Route;

// ─── Public: redirect to login ────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware(['auth', 'check.active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Patient Log (Clinic Logbook) — main feature ──────────────────────────
    Route::resource('patient-logs', PatientLogController::class)
         ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ─── Patients ─────────────────────────────────────────────────────────────
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/history', [PatientController::class, 'history'])
         ->name('patients.history');

    // ─── Appointments ─────────────────────────────────────────────────────────
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/approve', [AppointmentController::class, 'approve'])
         ->name('appointments.approve');
    Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
         ->name('appointments.cancel');
    Route::patch('appointments/{appointment}/no-show', [AppointmentController::class, 'noShow'])
         ->name('appointments.no-show');
    Route::patch('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
         ->name('appointments.complete');

    // ─── Consultations ────────────────────────────────────────────────────────
    Route::resource('consultations', ConsultationController::class);

    // ─── Medicines ────────────────────────────────────────────────────────────
    Route::get('medicines/low-stock', [MedicineController::class, 'lowStock'])
         ->name('medicines.low-stock');
    Route::get('medicines/expiring', [MedicineController::class, 'expiring'])
         ->name('medicines.expiring');
    Route::resource('medicines', MedicineController::class);

    // ─── Medicine Categories ──────────────────────────────────────────────────
    Route::resource('medicine-categories', MedicineCategoryController::class)
         ->only(['index', 'store', 'edit', 'update', 'destroy']);

    // ─── Inventory ────────────────────────────────────────────────────────────
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/',              [InventoryController::class, 'index'])->name('index');
        Route::get('/transactions',  [InventoryController::class, 'transactions'])->name('transactions');
        Route::get('/stock-in',      [InventoryController::class, 'stockInForm'])->name('stock-in.form');
        Route::post('/stock-in',     [InventoryController::class, 'stockIn'])->name('stock-in');
        Route::get('/stock-out',     [InventoryController::class, 'stockOutForm'])->name('stock-out.form');
        Route::post('/stock-out',    [InventoryController::class, 'stockOut'])->name('stock-out');
    });

    // ─── Dispensing ───────────────────────────────────────────────────────────
    Route::resource('dispensing', DispensingController::class)->only(['index', 'create', 'store', 'show']);

    // ─── Reports ──────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',               [ReportController::class, 'index'])->name('index');
        Route::get('/daily',          [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly',        [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/annual',         [ReportController::class, 'annual'])->name('annual');
        Route::get('/medicine-usage', [ReportController::class, 'medicineUsage'])->name('medicine-usage');
        Route::get('/inventory',      [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/appointments',   [ReportController::class, 'appointments'])->name('appointments');
        Route::get('/export/{type}',  [ReportController::class, 'export'])->name('export');
    });

    // ─── SMS ──────────────────────────────────────────────────────────────────
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('/',    [SmsController::class, 'index'])->name('index');
        Route::get('/send',[SmsController::class, 'create'])->name('create');
        Route::post('/send',[SmsController::class, 'send'])->name('send');
    });

    // ─── AI Assistant ─────────────────────────────────────────────────────────
    Route::prefix('ai-assistant')->name('ai-assistant.')->group(function () {
        Route::get('/',     [AiAssistantController::class, 'index'])->name('index');
        Route::post('/chat',[AiAssistantController::class, 'chat'])->name('chat');
        Route::delete('/clear', [AiAssistantController::class, 'clear'])->name('clear');
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

@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-gear-fill me-2 text-primary"></i>Settings</h4>
        <p class="text-muted small mb-0">Customize how Clinovia works for your school clinic</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $err){{ $err }}<br>@endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" id="settingsForm">
@csrf

{{-- ── Tab Navigation ───────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
        <ul class="nav nav-tabs border-0" id="settingsTabs" role="tablist">

            <li class="nav-item">
                <button class="nav-link active px-4" id="tab-clinic-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-clinic" type="button">
                    <i class="bi bi-hospital me-1"></i> Clinic Info
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-4" id="tab-academic-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-academic" type="button">
                    <i class="bi bi-mortarboard me-1"></i> Academic
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-4" id="tab-pharmacy-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-pharmacy" type="button">
                    <i class="bi bi-capsule me-1"></i> Pharmacy
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-4" id="tab-sms-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-sms" type="button">
                    <i class="bi bi-phone me-1"></i> SMS
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link px-4" id="tab-ai-btn"
                        data-bs-toggle="tab" data-bs-target="#tab-ai" type="button">
                    <i class="bi bi-stars me-1"></i> Cobi AI
                </button>
            </li>

        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content pt-2" id="settingsTabsContent">

            {{-- ══════════════════════════════════════════════════════════════
                 TAB 1 — CLINIC INFO
            ══════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tab-clinic" role="tabpanel">
                @php
                    $general = $settings['general'] ?? collect();
                    $clinic  = $settings['clinic']  ?? collect();
                @endphp

                <h6 class="text-muted text-uppercase fw-semibold mb-3 mt-1" style="font-size:.75rem;letter-spacing:.05em;">
                    System Name
                </h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        @php $s = $general->firstWhere('key','app_name') @endphp
                        <label class="form-label fw-semibold">System Name</label>
                        <input type="text" name="app_name"
                               value="{{ old('app_name', $s?->value ?? 'Clinovia') }}"
                               class="form-control @error('app_name') is-invalid @enderror">
                        <div class="form-text">Shown in the browser title and top navigation bar.</div>
                        @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        @php $s = $general->firstWhere('key','org_name') @endphp
                        <label class="form-label fw-semibold">School / Organization Full Name</label>
                        <input type="text" name="org_name"
                               value="{{ old('org_name', $s?->value) }}"
                               class="form-control @error('org_name') is-invalid @enderror">
                        <div class="form-text">Full name of the school or institution.</div>
                        @error('org_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        @php $s = $general->firstWhere('key','org_short_name') @endphp
                        <label class="form-label fw-semibold">School Abbreviation</label>
                        <input type="text" name="org_short_name"
                               value="{{ old('org_short_name', $s?->value) }}"
                               class="form-control @error('org_short_name') is-invalid @enderror"
                               placeholder="e.g. Clinovia">
                        @error('org_short_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        @php $s = $clinic->firstWhere('key','clinic_status_text') @endphp
                        <label class="form-label fw-semibold">Clinic Status Text</label>
                        <input type="text" name="clinic_status_text"
                               value="{{ old('clinic_status_text', $s?->value ?? 'Clinic Online') }}"
                               class="form-control"
                               placeholder="e.g. Clinic Online">
                        <div class="form-text">Shown in the top status bar.</div>
                    </div>
                </div>

                <hr>

                <h6 class="text-muted text-uppercase fw-semibold mb-3 mt-3" style="font-size:.75rem;letter-spacing:.05em;">
                    Clinic Details
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        @php $s = $clinic->firstWhere('key','clinic_name') @endphp
                        <label class="form-label fw-semibold">Clinic Name</label>
                        <input type="text" name="clinic_name"
                               value="{{ old('clinic_name', $s?->value) }}"
                               class="form-control"
                               placeholder="e.g. Clinovia School Clinic">
                        <div class="form-text">Printed on reports and forms.</div>
                    </div>
                    <div class="col-md-3">
                        @php $s = $clinic->firstWhere('key','clinic_contact') @endphp
                        <label class="form-label fw-semibold">Clinic Phone</label>
                        <input type="text" name="clinic_contact"
                               value="{{ old('clinic_contact', $s?->value) }}"
                               class="form-control" placeholder="e.g. 0917-123-4567">
                    </div>
                    <div class="col-md-3">
                        @php $s = $clinic->firstWhere('key','clinic_email') @endphp
                        <label class="form-label fw-semibold">Clinic Email</label>
                        <input type="email" name="clinic_email"
                               value="{{ old('clinic_email', $s?->value) }}"
                               class="form-control" placeholder="clinic@school.edu">
                    </div>
                    <div class="col-12">
                        @php $s = $clinic->firstWhere('key','clinic_address') @endphp
                        <label class="form-label fw-semibold">Clinic Address</label>
                        <input type="text" name="clinic_address"
                               value="{{ old('clinic_address', $s?->value) }}"
                               class="form-control">
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 TAB 2 — ACADEMIC
            ══════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-academic" role="tabpanel">
                @php
                    $academic = $settings['academic'] ?? collect();
                    $clinicS  = $settings['clinic']   ?? collect();

                    $ylSetting  = $academic->firstWhere('key','year_levels');
                    $ylValues   = $ylSetting ? json_decode($ylSetting->value, true) : [];
                    $ylText     = implode("\n", (array) $ylValues);

                    $secSetting = $academic->firstWhere('key','sections');
                    $secValues  = $secSetting ? json_decode($secSetting->value, true) : [];
                    $secText    = implode("\n", (array) $secValues);

                    $pgSetting  = $academic->firstWhere('key','program_strands');
                    $pgValues   = $pgSetting ? json_decode($pgSetting->value, true) : [];
                    $pgText     = implode("\n", (array) $pgValues);

                    $catSetting = $clinicS->firstWhere('key','patient_categories');
                    $catValues  = $catSetting ? json_decode($catSetting->value, true) : [];
                    $catText    = implode("\n", (array) $catValues);
                @endphp

                <div class="alert alert-info small mb-4">
                    <i class="bi bi-lightbulb-fill me-1"></i>
                    <strong>These are dropdown choices</strong> that appear in the patient registration form.
                    Type each option on its own line. The system will offer these as options when adding or editing a patient.
                </div>

                <div class="row g-4">

                    {{-- Year Levels --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Year Levels / Grade Levels
                            <span class="badge bg-primary-subtle text-primary-emphasis ms-1">Used in patient forms</span>
                        </label>
                        <textarea id="year_levels_raw" rows="10"
                                  class="form-control font-monospace"
                                  placeholder="Grade 7&#10;Grade 8&#10;1st Year&#10;2nd Year"
                                  oninput="syncJson(this,'year_levels')">{{ $ylText }}</textarea>
                        <input type="hidden" name="year_levels" id="year_levels"
                               value="{{ old('year_levels', $ylSetting?->value) }}">
                        <div class="form-text">One year level per line, e.g. <em>Grade 7</em>, <em>1st Year College</em></div>
                    </div>

                    {{-- Sections --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Sections
                            <span class="badge bg-success-subtle text-success-emphasis ms-1">Used in patient forms</span>
                        </label>
                        <textarea id="sections_raw" rows="10"
                                  class="form-control font-monospace"
                                  placeholder="Section A&#10;Section B&#10;Block 1"
                                  oninput="syncJson(this,'sections')">{{ $secText }}</textarea>
                        <input type="hidden" name="sections" id="sections"
                               value="{{ old('sections', $secSetting?->value) }}">
                        <div class="form-text">One section per line, e.g. <em>Section A</em>, <em>Rizal</em>, <em>Block 2</em></div>
                    </div>

                    {{-- Programs / Strands --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Programs / Strands / Courses
                            <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Used in patient forms</span>
                        </label>
                        <textarea id="program_strands_raw" rows="10"
                                  class="form-control font-monospace"
                                  placeholder="BSIT&#10;BSCS&#10;ABM&#10;STEM"
                                  oninput="syncJson(this,'program_strands')">{{ $pgText }}</textarea>
                        <input type="hidden" name="program_strands" id="program_strands"
                               value="{{ old('program_strands', $pgSetting?->value) }}">
                        <div class="form-text">One program or strand per line, e.g. <em>BSIT</em>, <em>ABM</em>, <em>STEM</em></div>
                    </div>

                    {{-- Patient Categories --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Patient Categories
                            <span class="badge bg-info-subtle text-info-emphasis ms-1">Used in patient forms</span>
                        </label>
                        <textarea id="patient_categories_raw" rows="10"
                                  class="form-control font-monospace"
                                  placeholder="college&#10;senior_high&#10;teacher"
                                  oninput="syncJson(this,'patient_categories')">{{ $catText }}</textarea>
                        <input type="hidden" name="patient_categories" id="patient_categories"
                               value="{{ old('patient_categories', $catSetting?->value) }}">
                        <div class="form-text">
                            One category per line (lowercase, underscores instead of spaces).
                            Examples: <em>college</em>, <em>senior_high</em>, <em>teacher</em>, <em>employee</em>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 TAB 3 — PHARMACY
            ══════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-pharmacy" role="tabpanel">
                @php
                    $notifs  = $settings['notifications'] ?? collect();
                    $clinicS = $settings['clinic'] ?? collect();

                    $unitSetting = $clinicS->firstWhere('key','medicine_units');
                    $unitValues  = $unitSetting ? json_decode($unitSetting->value, true) : [];
                    $unitText    = implode("\n", (array) $unitValues);
                @endphp

                <div class="row g-4">

                    {{-- Medicine Units --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Medicine Units
                            <span class="badge bg-success-subtle text-success-emphasis ms-1">Used in medicine forms</span>
                        </label>
                        <textarea id="medicine_units_raw" rows="10"
                                  class="form-control font-monospace"
                                  placeholder="tablets&#10;capsules&#10;ml"
                                  oninput="syncJson(this,'medicine_units')">{{ $unitText }}</textarea>
                        <input type="hidden" name="medicine_units" id="medicine_units"
                               value="{{ old('medicine_units', $unitSetting?->value) }}">
                        <div class="form-text">One unit per line, e.g. <em>tablets</em>, <em>capsules</em>, <em>ml</em></div>
                    </div>

                    {{-- Thresholds --}}
                    <div class="col-md-6">
                        <h6 class="fw-semibold mb-3">Alert Settings</h6>

                        @php $s = $notifs->firstWhere('key','low_stock_threshold'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-box-seam-fill me-1 text-warning"></i>
                                Low Stock Alert — when to warn
                            </label>
                            <div class="input-group" style="max-width:220px;">
                                <input type="number" name="low_stock_threshold" min="1" max="9999"
                                       value="{{ old('low_stock_threshold', $s?->value ?? 10) }}"
                                       class="form-control @error('low_stock_threshold') is-invalid @enderror">
                                <span class="input-group-text">units left</span>
                            </div>
                            <div class="form-text">
                                When a medicine's stock falls to or below this number, a warning will appear.
                            </div>
                            @error('low_stock_threshold')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        @php $s = $notifs->firstWhere('key','expiry_warning_days'); @endphp
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-x-fill me-1 text-danger"></i>
                                Expiry Warning — how far ahead to check
                            </label>
                            <div class="input-group" style="max-width:220px;">
                                <input type="number" name="expiry_warning_days" min="1" max="365"
                                       value="{{ old('expiry_warning_days', $s?->value ?? 30) }}"
                                       class="form-control @error('expiry_warning_days') is-invalid @enderror">
                                <span class="input-group-text">days ahead</span>
                            </div>
                            <div class="form-text">
                                Medicines expiring within this many days will show a warning.
                            </div>
                            @error('expiry_warning_days')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        @php $s = $notifs->firstWhere('key','max_daily_appointments'); @endphp
                        <div>
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-check-fill me-1 text-primary"></i>
                                Maximum Appointments Per Day
                            </label>
                            <div class="input-group" style="max-width:220px;">
                                <input type="number" name="max_daily_appointments" min="1" max="9999"
                                       value="{{ old('max_daily_appointments', $s?->value ?? 50) }}"
                                       class="form-control @error('max_daily_appointments') is-invalid @enderror">
                                <span class="input-group-text">appointments</span>
                            </div>
                            <div class="form-text">
                                The system will warn when this limit is reached for the day.
                            </div>
                            @error('max_daily_appointments')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 TAB 4 — SMS
            ══════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-sms" role="tabpanel">
                @php
                    $sms = $settings['sms'] ?? collect();

                    $smsEnabled   = $sms->firstWhere('key','sms_enabled');
                    $smsSender    = $sms->firstWhere('key','sms_sender_name');
                    $tplApproval  = $sms->firstWhere('key','sms_template_approval');
                    $tplCancel    = $sms->firstWhere('key','sms_template_cancellation');
                    $tplLog       = $sms->firstWhere('key','sms_template_clinic_log');
                    $smsLogGuard  = $sms->firstWhere('key','sms_log_guardian_enabled');
                @endphp

                <div class="row g-4">

                    {{-- SMS Basic Settings --}}
                    <div class="col-md-5">
                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-sliders me-1 text-info"></i> SMS Connection
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Enable SMS Notifications</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="sms_enabled" value="true" id="chk_sms_enabled"
                                       @checked(old('sms_enabled', $smsEnabled?->value) === 'true')>
                                <label class="form-check-label" for="chk_sms_enabled">
                                    Send SMS to patients &amp; guardians
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sender Name</label>
                            <input type="text" name="sms_sender_name" maxlength="11"
                                   value="{{ old('sms_sender_name', $smsSender?->value ?? 'CLINOVIA') }}"
                                   class="form-control @error('sms_sender_name') is-invalid @enderror"
                                   placeholder="e.g. CLINOVIA (max 11 chars)">
                            <div class="form-text">This name appears as the sender on the recipient's phone. Maximum 11 characters.</div>
                            @error('sms_sender_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Guardian SMS on Patient Log</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="sms_log_guardian_enabled" value="true" id="chk_sms_log"
                                       @checked(old('sms_log_guardian_enabled', $smsLogGuard?->value ?? 'true') === 'true')>
                                <label class="form-check-label" for="chk_sms_log">
                                    Allow notifying guardians when logging a clinic visit
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-light border small mt-3">
                            <i class="bi bi-info-circle me-1"></i>
                            The <strong>Semaphore API key</strong> is stored securely in the server settings
                            (not shown here for safety).
                            @if(empty(config('semaphore.api_key')))
                                <strong class="text-danger d-block mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    API key not set — SMS will not be delivered until configured.
                                </strong>
                            @else
                                <span class="text-success d-block mt-1">
                                    <i class="bi bi-check-circle me-1"></i>Semaphore API key is active.
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- SMS Templates --}}
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-1">
                            <i class="bi bi-chat-square-text me-1 text-info"></i> SMS Message Templates
                        </h6>
                        <p class="text-muted small mb-3">
                            Customize the messages sent automatically. Use <code>{curly braces}</code>
                            for parts that are filled in automatically (patient name, date, etc.).
                        </p>

                        {{-- Appointment Approved --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                <span class="badge text-bg-success">Appointment Approved</span>
                                <span class="text-muted fw-normal small">Sent when a patient's appointment is confirmed</span>
                            </label>
                            <textarea id="sms_template_approval" name="sms_template_approval"
                                      rows="3" maxlength="320"
                                      class="form-control font-monospace small @error('sms_template_approval') is-invalid @enderror"
                                      oninput="countChars(this,'cnt_approval');updatePreview()">{{ old('sms_template_approval', $tplApproval?->value ?? 'Dear {name}, your appointment at the school clinic on {date} at {time} has been approved. Please arrive 10 minutes early. - Clinovia') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="form-text">Available: <code>{name}</code> <code>{date}</code> <code>{time}</code></div>
                                <span class="form-text" id="cnt_approval"></span>
                            </div>
                            @error('sms_template_approval')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Appointment Cancelled --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                <span class="badge text-bg-danger">Appointment Cancelled</span>
                                <span class="text-muted fw-normal small">Sent when an appointment is cancelled</span>
                            </label>
                            <textarea id="sms_template_cancellation" name="sms_template_cancellation"
                                      rows="3" maxlength="320"
                                      class="form-control font-monospace small @error('sms_template_cancellation') is-invalid @enderror"
                                      oninput="countChars(this,'cnt_cancel');updatePreview()">{{ old('sms_template_cancellation', $tplCancel?->value ?? 'Dear {name}, your appointment on {date} at the school clinic has been cancelled. Reason: {reason}. Please contact us to reschedule. - Clinovia') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="form-text">Available: <code>{name}</code> <code>{date}</code> <code>{reason}</code></div>
                                <span class="form-text" id="cnt_cancel"></span>
                            </div>
                            @error('sms_template_cancellation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Clinic Log Guardian --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                <span class="badge text-bg-primary">Guardian Notification</span>
                                <span class="text-muted fw-normal small">Sent to guardian when a student visits the clinic</span>
                            </label>
                            <textarea id="sms_template_clinic_log" name="sms_template_clinic_log"
                                      rows="3" maxlength="320"
                                      class="form-control font-monospace small @error('sms_template_clinic_log') is-invalid @enderror"
                                      oninput="countChars(this,'cnt_log');updatePreview()">{{ old('sms_template_clinic_log', $tplLog?->value ?? 'Dear {guardian}, your ward {name} visited the school clinic at {time} for {complaint}. Action taken: {treatment}. - Clinovia') }}</textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="form-text">Available: <code>{guardian}</code> <code>{name}</code> <code>{time}</code> <code>{complaint}</code> <code>{treatment}</code></div>
                                <span class="form-text" id="cnt_log"></span>
                            </div>
                            @error('sms_template_clinic_log')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Preview --}}
                        <div class="alert alert-light border small py-3">
                            <div class="fw-semibold mb-2"><i class="bi bi-phone me-1 text-info"></i>Live Preview (sample values)</div>
                            <div class="mb-1">
                                <span class="badge text-bg-success me-1">Approved</span>
                                <span id="preview_approval" class="fst-italic text-body"></span>
                            </div>
                            <div class="mb-1">
                                <span class="badge text-bg-danger me-1">Cancelled</span>
                                <span id="preview_cancel" class="fst-italic text-body"></span>
                            </div>
                            <div>
                                <span class="badge text-bg-primary me-1">Guardian</span>
                                <span id="preview_log" class="fst-italic text-body"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 TAB 5 — AI (COBI)
            ══════════════════════════════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tab-ai" role="tabpanel">
                @php
                    $ai = $settings['ai'] ?? collect();
                    $aiEnabled = $ai->firstWhere('key','ai_enabled');
                    $aiModel   = $ai->firstWhere('key','ai_model');
                @endphp

                <div class="row justify-content-center">
                    <div class="col-md-7">

                        <div class="text-center mb-4">
                            <div class="display-6 text-warning mb-2"><i class="bi bi-stars"></i></div>
                            <h5 class="fw-bold">Cobi — AI Assistant</h5>
                            <p class="text-muted small">
                                Cobi is a built-in AI assistant that can help clinic staff with quick questions,
                                health information, and clinic guidance. Powered by Groq's fast AI.
                            </p>
                        </div>

                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Cobi Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox"
                                               name="ai_enabled" value="true" id="chk_ai_enabled"
                                               @checked(old('ai_enabled', $aiEnabled?->value ?? 'true') === 'true')>
                                        <label class="form-check-label" for="chk_ai_enabled">
                                            Enable Cobi AI
                                        </label>
                                    </div>
                                    <div class="form-text">When turned off, Cobi is hidden from all users.</div>
                                </div>

                                <div>
                                    <label for="ai_model" class="form-label fw-semibold">AI Model</label>
                                    <select id="ai_model" name="ai_model" class="form-select">
                                        @foreach([
                                            'llama-3.3-70b-versatile' => 'Llama 3.3 70B — Best (Recommended)',
                                            'llama-3.1-70b-versatile' => 'Llama 3.1 70B',
                                            'llama3-70b-8192'         => 'Llama 3 70B',
                                            'llama3-8b-8192'          => 'Llama 3 8B — Fastest',
                                            'gemma2-9b-it'            => 'Gemma 2 9B',
                                            'mixtral-8x7b-32768'      => 'Mixtral 8x7B',
                                        ] as $id => $label)
                                        <option value="{{ $id }}" @selected(old('ai_model', $aiModel?->value) === $id)>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        The AI model controls how smart and fast Cobi responds.
                                        "Llama 3.3 70B" is the best choice for most clinics.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-1"></i>
                            The Groq API key is stored securely in the server settings (not shown here).
                            @if(empty(env('GROQ_API_KEY')))
                                <strong class="text-danger d-block mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Groq API key is not set — Cobi cannot respond until this is configured.
                                </strong>
                            @else
                                <span class="text-success d-block mt-1">
                                    <i class="bi bi-check-circle me-1"></i>Groq API key is active.
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        </div>{{-- /.tab-content --}}
    </div>

    {{-- ── Save Button ──────────────────────────────────────────────────────── --}}
    <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
            <i class="bi bi-floppy-fill me-2"></i>Save Settings
        </button>
    </div>

</div>{{-- /.card --}}

</form>

<script>
// ── Character counter for SMS fields ─────────────────────────────────────────
function countChars(el, counterId) {
    const len  = el.value.length;
    const msgs = Math.ceil(len / 160) || 1;
    const el2  = document.getElementById(counterId);
    if (el2) {
        el2.textContent = `${len}/320 chars · ${msgs} SMS`;
        el2.style.color = len > 160 ? 'var(--bs-warning)' : 'var(--bs-secondary)';
    }
}

// ── Live preview ──────────────────────────────────────────────────────────────
function updatePreview() {
    const fill = (tpl) => tpl
        .replace(/{name}/g,      'Maria')
        .replace(/{guardian}/g,  'Mrs. Santos')
        .replace(/{date}/g,      'June 7, 2026')
        .replace(/{time}/g,      '09:00 AM')
        .replace(/{reason}/g,    'Clinic closed')
        .replace(/{complaint}/g, 'Headache')
        .replace(/{treatment}/g, 'Rest + Paracetamol');

    const ids = [
        ['sms_template_approval',     'preview_approval'],
        ['sms_template_cancellation', 'preview_cancel'],
        ['sms_template_clinic_log',   'preview_log'],
    ];

    ids.forEach(([src, dst]) => {
        const srcEl = document.getElementById(src);
        const dstEl = document.getElementById(dst);
        if (srcEl && dstEl) dstEl.textContent = fill(srcEl.value);
    });
}

// ── JSON sync (textarea → hidden input) ──────────────────────────────────────
function syncJson(textarea, hiddenId) {
    const lines  = textarea.value.split('\n').map(l => l.trim()).filter(Boolean);
    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = JSON.stringify(lines);
}

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // SMS character counters
    ['sms_template_approval','sms_template_cancellation','sms_template_clinic_log'].forEach(id => {
        const el = document.getElementById(id);
        const counterMap = {
            'sms_template_approval':     'cnt_approval',
            'sms_template_cancellation': 'cnt_cancel',
            'sms_template_clinic_log':   'cnt_log',
        };
        if (el) countChars(el, counterMap[id]);
    });

    updatePreview();

    // Init JSON hidden fields from visible textareas
    [
        ['year_levels_raw',        'year_levels'],
        ['sections_raw',           'sections'],
        ['program_strands_raw',    'program_strands'],
        ['patient_categories_raw', 'patient_categories'],
        ['medicine_units_raw',     'medicine_units'],
    ].forEach(([rawId, hidId]) => {
        const el = document.getElementById(rawId);
        if (el) syncJson(el, hidId);
    });

    // Remember active tab across page reload
    const savedTab = sessionStorage.getItem('settingsTab');
    if (savedTab) {
        const btn = document.querySelector(`[data-bs-target="${savedTab}"]`);
        if (btn) btn.click();
    }

    document.querySelectorAll('#settingsTabs .nav-link').forEach(btn => {
        btn.addEventListener('shown.bs.tab', e => {
            sessionStorage.setItem('settingsTab', e.target.dataset.bsTarget);
        });
    });
});
</script>
@endsection

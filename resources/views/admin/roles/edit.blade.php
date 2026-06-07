@extends('layouts.app')

@section('title', 'Edit Role — ' . ucfirst($role->name))

@push('styles')
<style>
/* ── Icon Picker ─────────────────────────────────────────────────────── */
.icon-picker-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: .5rem;
    max-height: 280px;
    overflow-y: auto;
    padding: .25rem;
}
.icon-picker-grid::-webkit-scrollbar { width: 5px; }
.icon-picker-grid::-webkit-scrollbar-track { background: transparent; }
.icon-picker-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

.icon-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .2rem;
    padding: .55rem .25rem;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: border-color .15s, background .15s, transform .15s;
    font-size: 1.1rem;
    color: var(--text-secondary);
    text-align: center;
    user-select: none;
}
.icon-option:hover {
    border-color: #4f46e5;
    background: rgba(79,70,229,.06);
    color: #4f46e5;
    transform: translateY(-1px);
}
.icon-option.selected {
    border-color: #4f46e5;
    background: linear-gradient(135deg, rgba(79,70,229,.1), rgba(124,58,237,.1));
    color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,.15);
}
.icon-option span {
    font-size: .6rem;
    color: var(--text-muted);
    line-height: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
    text-align: center;
}
.icon-preview-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    border-radius: 50%;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
    transition: all .2s;
}
.icon-search-input {
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    padding: .4rem .8rem;
    font-size: .83rem;
    width: 100%;
    margin-bottom: .75rem;
    outline: none;
    transition: border-color .2s;
}
.icon-search-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
</style>
@endpush

@section('content')
@php
    $isSystem  = in_array($role->name, ['administrator', 'nurse', 'staff']);
    $roleColor = match($role->name) {
        'administrator' => 'danger',
        'nurse'         => 'success',
        'staff'         => 'info',
        default         => 'primary',
    };
    $gradientMap = [
        'danger'  => 'linear-gradient(135deg,#dc2626,#b91c1c)',
        'success' => 'linear-gradient(135deg,#059669,#047857)',
        'info'    => 'linear-gradient(135deg,#0891b2,#0e7490)',
        'primary' => 'linear-gradient(135deg,#4f46e5,#7c3aed)',
    ];
    $currentGradient = $gradientMap[$roleColor];
    $currentPerms    = $role->permissions->pluck('name')->toArray();
    $currentIcon     = old('icon', $role->icon ?? 'person-fill');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            Edit Role &mdash;
            <span class="badge text-bg-{{ $roleColor }}">{{ ucfirst($role->name) }}</span>
            @if($isSystem)
                <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1 small">System Role</span>
            @endif
        </h4>
        <p class="text-muted small mb-0">
            {{ $role->permissions->count() }} permissions currently assigned
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

@if($isSystem)
<div class="alert alert-warning py-2 small mb-4">
    <i class="bi bi-shield-exclamation me-1"></i>
    <strong>System role:</strong> The role name cannot be changed. You may adjust its icon and permissions.
</div>
@endif

<form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf @method('PUT')

    <div class="row g-4">

        {{-- Icon Picker --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center gap-3">
                    {{-- Live role icon preview --}}
                    <div class="icon-preview-badge" id="roleIconPreview"
                         style="background:{{ $currentGradient }};">
                        <i class="bi bi-{{ $currentIcon }}" id="previewIconEl"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.92rem;">{{ ucfirst($role->name) }}</div>
                        <div class="text-muted small">Choose an icon to represent this role</div>
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="icon-search-input" id="iconSearch" placeholder="&#xF52A; Search icons…">
                    <input type="hidden" name="icon" id="iconValue" value="{{ $currentIcon }}">

                    <div class="icon-picker-grid" id="iconGrid">
                        @php
                        $icons = [
                            ['shield-fill-check',      'Admin'],
                            ['heart-pulse-fill',        'Nurse'],
                            ['person-badge-fill',       'Staff'],
                            ['hospital-fill',           'Doctor'],
                            ['capsule',                 'Pharma'],
                            ['eyedropper',              'Lab'],
                            ['clipboard2-pulse-fill',   'Records'],
                            ['box-seam-fill',           'Inventory'],
                            ['chat-dots-fill',          'Reception'],
                            ['bar-chart-fill',          'Analyst'],
                            ['gear-fill',               'System'],
                            ['people-fill',             'Users'],
                            ['person-fill',             'Person'],
                            ['person-plus-fill',        'Add User'],
                            ['person-check-fill',       'Verified'],
                            ['lock-fill',               'Security'],
                            ['shield-lock-fill',        'Shield'],
                            ['shield-fill-exclamation', 'Warning'],
                            ['star-fill',               'Star'],
                            ['award-fill',              'Award'],
                            ['patch-check-fill',        'Certified'],
                            ['journal-medical',         'Medical'],
                            ['stethoscope',             'Steth'],
                            ['bandaid-fill',            'First Aid'],
                            ['thermometer-half',        'Temp'],
                            ['droplet-fill',            'Blood'],
                            ['activity',                'Activity'],
                            ['calendar-check-fill',     'Schedule'],
                            ['telephone-fill',          'Phone'],
                            ['envelope-fill',           'Email'],
                            ['printer-fill',            'Print'],
                            ['file-earmark-fill',       'Files'],
                            ['graph-up-arrow',          'Reports'],
                            ['currency-exchange',       'Finance'],
                            ['truck-flatbed',           'Logistics'],
                            ['tools',                   'Tech'],
                            ['cpu-fill',                'IT'],
                            ['building-fill-check',     'Facility'],
                            ['mortarboard-fill',        'Education'],
                            ['brightness-high-fill',    'General'],
                        ];
                        @endphp
                        @foreach($icons as [$icon, $label])
                        <div class="icon-option {{ $currentIcon === $icon ? 'selected' : '' }}"
                             data-icon="{{ $icon }}"
                             data-label="{{ strtolower($label) }}"
                             title="{{ $label }}"
                             onclick="selectIcon('{{ $icon }}', this)">
                            <i class="bi bi-{{ $icon }}"></i>
                            <span>{{ $label }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-key text-warning me-2"></i> Permissions for <strong>{{ $role->name }}</strong></span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleAll(true)">
                            <i class="bi bi-check-all me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAll(false)">
                            <i class="bi bi-x me-1"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($permissions as $group => $perms)
                        @php
                            $groupChecked = collect($perms)->every(fn($p) => in_array($p->name, $currentPerms));
                        @endphp
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="border rounded p-3 h-100 @if($groupChecked) border-{{ $roleColor }} bg-{{ $roleColor }}-subtle @endif"
                                 style="transition:all .2s;">
                                {{-- Group header with "select group" checkbox --}}
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <input type="checkbox"
                                           class="form-check-input group-toggle"
                                           id="grp_{{ $loop->index }}"
                                           data-group="{{ $loop->index }}"
                                           @checked($groupChecked)
                                           onclick="toggleGroup({{ $loop->index }}, this.checked)">
                                    <label for="grp_{{ $loop->index }}"
                                           class="fw-semibold small text-uppercase text-muted mb-0"
                                           style="font-size:.7rem;letter-spacing:.06em;cursor:pointer;">
                                        {{ str_replace('-', ' ', $group) }}
                                    </label>
                                </div>

                                @foreach($perms as $perm)
                                <div class="form-check form-check-sm mb-1">
                                    <input class="form-check-input perm-check grp-{{ $loop->parent->index }}"
                                           type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perm->name }}"
                                           id="perm_{{ $loop->parent->index }}_{{ $loop->index }}"
                                           @checked(in_array($perm->name, old('permissions', $currentPerms)))
                                           onchange="syncGroupToggle({{ $loop->parent->index }})">
                                    <label class="form-check-label small"
                                           for="perm_{{ $loop->parent->index }}_{{ $loop->index }}">
                                        @php $verb = explode('-', $perm->name)[0]; @endphp
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis me-1"
                                              style="font-size:.65rem;">{{ $verb }}</span>
                                        {{ ucwords(str_replace('-', ' ', $perm->name)) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-save me-1"></i> Save Changes
        </button>
    </div>
</form>

@push('scripts')
<script>
/* ── Icon picker ──────────────────────────────────────────────── */
function selectIcon(icon, el) {
    document.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('iconValue').value = icon;
    document.getElementById('previewIconEl').className = 'bi bi-' + icon;
}

/* Icon search filter */
document.getElementById('iconSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.icon-option').forEach(el => {
        const match = el.dataset.icon.includes(q) || el.dataset.label.includes(q);
        el.style.display = match ? '' : 'none';
    });
});

/* ── Permissions ─────────────────────────────────────────────── */
function toggleAll(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
    document.querySelectorAll('.group-toggle').forEach(cb => cb.checked = checked);
    updateCardStyles();
}

function toggleGroup(index, checked) {
    document.querySelectorAll('.grp-' + index).forEach(cb => cb.checked = checked);
    updateCardStyles();
}

function syncGroupToggle(index) {
    const group  = document.querySelectorAll('.grp-' + index);
    const allOn  = [...group].every(cb => cb.checked);
    const toggle = document.getElementById('grp_' + index);
    if (toggle) toggle.checked = allOn;
    updateCardStyles();
}

function updateCardStyles() {
    document.querySelectorAll('[data-group]').forEach(toggle => {
        const idx   = toggle.dataset.group;
        const group = document.querySelectorAll('.grp-' + idx);
        const card  = toggle.closest('.border.rounded');
        if (!card) return;
        const allOn = [...group].every(cb => cb.checked);
        card.style.borderColor = allOn ? 'var(--bs-primary)' : '';
    });
}
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Create Role')

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
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    font-size: 1.1rem;
    margin-right: .6rem;
    box-shadow: 0 2px 8px rgba(79,70,229,.3);
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Create New Role</h4>
        <p class="text-muted small mb-0">Define a role name, choose an icon, and select which permissions it grants</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Roles
    </a>
</div>

<form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="row g-4">

        {{-- Role identity card --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold">
                    <i class="bi bi-tag text-primary me-2"></i> Role Identity
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            {{-- Live preview --}}
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-preview-badge" id="roleIconPreview">
                                    <i class="bi bi-person-fill" id="previewIconEl"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold" id="previewRoleName" style="font-size:.9rem;color:var(--text-primary);">New Role</div>
                                    <div class="text-muted" style="font-size:.75rem;">Role preview</div>
                                </div>
                            </div>

                            <label for="name" class="form-label fw-semibold small">
                                Role Name <span class="text-danger">*</span>
                            </label>
                            <input id="name" type="text" name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g. pharmacist"
                                   pattern="[a-z0-9_\-]+"
                                   required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Lowercase letters, numbers, hyphens only (e.g. <code>head-nurse</code>)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Icon Picker card --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold">
                    <i class="bi bi-grid-3x3-gap text-warning me-2"></i> Choose Role Icon
                    <span class="text-muted fw-normal small ms-1">(optional)</span>
                </div>
                <div class="card-body">
                    <input type="text" class="icon-search-input" id="iconSearch" placeholder="&#xF52A; Search icons…">
                    <input type="hidden" name="icon" id="iconValue" value="{{ old('icon', 'person-fill') }}">

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
                        <div class="icon-option {{ old('icon', 'person-fill') === $icon ? 'selected' : '' }}"
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

        {{-- Permissions matrix --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom fw-semibold d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-key text-warning me-2"></i> Permissions</span>
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
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold small text-uppercase text-muted mb-2"
                                     style="font-size:.7rem;letter-spacing:.06em;">
                                    <i class="bi bi-folder me-1"></i>{{ str_replace('-', ' ', $group) }}
                                </div>
                                @foreach($perms as $perm)
                                <div class="form-check form-check-sm mb-1">
                                    <input class="form-check-input perm-check"
                                           type="checkbox"
                                           name="permissions[]"
                                           value="{{ $perm->name }}"
                                           id="perm_{{ $loop->parent->index }}_{{ $loop->index }}"
                                           @checked(in_array($perm->name, old('permissions', [])))>
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
            <i class="bi bi-plus-circle me-1"></i> Create Role
        </button>
    </div>
</form>

@push('scripts')
<script>
/* ── Icon picker ─────────────────────────────────────────────────── */
function selectIcon(icon, el) {
    document.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('iconValue').value = icon;
    document.getElementById('previewIconEl').className = 'bi bi-' + icon;
}

/* Role name live preview */
document.getElementById('name').addEventListener('input', function () {
    const v = this.value.trim();
    document.getElementById('previewRoleName').textContent = v
        ? v.replace(/-/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
        : 'New Role';
});

/* Icon search filter */
document.getElementById('iconSearch').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.icon-option').forEach(el => {
        const match = el.dataset.icon.includes(q) || el.dataset.label.includes(q);
        el.style.display = match ? '' : 'none';
    });
});

/* Permissions toggle */
function toggleAll(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
}
</script>
@endpush
@endsection

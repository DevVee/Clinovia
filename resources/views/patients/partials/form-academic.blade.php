{{-- Academic Information --}}
@php
    // Load from Settings (admin-configurable)
    $yearLevels    = \App\Models\Setting::get('year_levels',    []);
    $sectionOpts   = \App\Models\Setting::get('sections',       []);
    $programOpts   = \App\Models\Setting::get('program_strands',[]);

    // Helper: convert any string to Sentence case
    $sc = fn(string $v): string => mb_strtoupper(mb_substr($v,0,1)) . mb_strtolower(mb_substr($v,1));

    $currentYr  = old('year_level',    $patient->year_level    ?? '');
    $currentSec = old('section',       $patient->section       ?? '');
    $currentPg  = old('program_strand',$patient->program_strand ?? '');
@endphp

<div class="row g-3">

    <div class="col-12">
        <div class="alert alert-info d-flex align-items-center gap-2 py-2 small" role="alert">
            <i class="bi bi-info-circle-fill flex-shrink-0"></i>
            Fill in academic details for students. Leave blank for employees, visitors, or others.
            Options are managed in <strong>Settings → Academic</strong>.
        </div>
    </div>

    {{-- Year Level --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Year Level / Grade</label>
        <select name="year_level" class="form-select @error('year_level') is-invalid @enderror">
            <option value="">— Select year level —</option>
            @foreach($yearLevels as $yl)
                <option value="{{ $yl }}" {{ $currentYr === $yl ? 'selected' : '' }}>
                    {{ $sc($yl) }}
                </option>
            @endforeach
            {{-- If the saved value isn't in the list, show it anyway --}}
            @if($currentYr && !in_array($currentYr, $yearLevels))
                <option value="{{ $currentYr }}" selected>{{ $sc($currentYr) }}</option>
            @endif
        </select>
        @error('year_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Program / Strand --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Program / Strand / Course</label>
        <select name="program_strand" class="form-select @error('program_strand') is-invalid @enderror">
            <option value="">— Select program or strand —</option>
            @foreach($programOpts as $pg)
                <option value="{{ $pg }}" {{ $currentPg === $pg ? 'selected' : '' }}>
                    {{ $sc($pg) }}
                </option>
            @endforeach
            @if($currentPg && !in_array($currentPg, $programOpts))
                <option value="{{ $currentPg }}" selected>{{ $sc($currentPg) }}</option>
            @endif
        </select>
        @error('program_strand')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Section --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Section</label>
        <select name="section" class="form-select @error('section') is-invalid @enderror">
            <option value="">— Select section —</option>
            @foreach($sectionOpts as $sec)
                <option value="{{ $sec }}" {{ $currentSec === $sec ? 'selected' : '' }}>
                    {{ $sc($sec) }}
                </option>
            @endforeach
            @if($currentSec && !in_array($currentSec, $sectionOpts))
                <option value="{{ $currentSec }}" selected>{{ $sc($currentSec) }}</option>
            @endif
        </select>
        @error('section')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

</div>

{{--
    Philippine Address Picker (PSGC cascade)
    Props:
      $addrField   — form field name  ('address' | 'guardian_address')
      $addrValue   — current DB value (empty string for create)
      $addrLabel   — label text
      $addrPrefix  — unique prefix for DOM IDs (e.g. 'pat' | 'grd')
--}}
@php
    $addrValue  = old($addrField, $addrValue ?? '');
    $addrLabel  = $addrLabel ?? 'Address';
    $addrPrefix = $addrPrefix ?? 'addr';
@endphp

<div class="address-picker-wrap">

    {{-- Label + existing address hint --}}
    <label class="form-label fw-semibold">
        {{ $addrLabel }}
        <span class="badge bg-primary-subtle text-primary-emphasis ms-1" style="font-size:.65rem;letter-spacing:.02em;">
            <i class="bi bi-geo-alt-fill me-1"></i>PH Address Picker
        </span>
    </label>

    @if($addrValue)
    <div class="alert alert-light border small py-2 mb-2 d-flex align-items-start gap-2" id="{{ $addrPrefix }}-existing-alert">
        <i class="bi bi-info-circle text-primary mt-1 flex-shrink-0"></i>
        <div>
            <span class="text-muted">Current: </span>
            <strong id="{{ $addrPrefix }}-existing-text">{{ $addrValue }}</strong>
            <br>
            <span class="text-muted" style="font-size:.78rem;">Use the picker below to update, or leave dropdowns blank to keep the current address.</span>
        </div>
    </div>
    @endif

    <div class="row g-2">

        {{-- Street / House No. --}}
        <div class="col-12">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="bi bi-house-door"></i></span>
                <input type="text"
                       id="{{ $addrPrefix }}-street"
                       class="form-control"
                       placeholder="House/Block/Lot No., Street Name (optional)"
                       value="{{ $addrValue ? '' : old($addrField . '_street', '') }}">
            </div>
        </div>

        {{-- Region --}}
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted" style="font-size:.8rem;padding:.35rem .6rem;">
                    <i class="bi bi-map"></i>
                </span>
                <select id="{{ $addrPrefix }}-region" class="form-select" style="border-left:0;">
                    <option value="">— Region —</option>
                </select>
            </div>
        </div>

        {{-- Province --}}
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted" style="font-size:.8rem;padding:.35rem .6rem;">
                    <i class="bi bi-building"></i>
                </span>
                <select id="{{ $addrPrefix }}-province" class="form-select" style="border-left:0;" disabled>
                    <option value="">— Province —</option>
                </select>
            </div>
        </div>

        {{-- City / Municipality --}}
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted" style="font-size:.8rem;padding:.35rem .6rem;">
                    <i class="bi bi-buildings"></i>
                </span>
                <select id="{{ $addrPrefix }}-city" class="form-select" style="border-left:0;" disabled>
                    <option value="">— City / Municipality —</option>
                </select>
            </div>
        </div>

        {{-- Barangay --}}
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white text-muted" style="font-size:.8rem;padding:.35rem .6rem;">
                    <i class="bi bi-geo"></i>
                </span>
                <select id="{{ $addrPrefix }}-barangay" class="form-select" style="border-left:0;" disabled>
                    <option value="">— Barangay —</option>
                </select>
            </div>
        </div>

        {{-- Composed preview --}}
        <div class="col-12">
            <div id="{{ $addrPrefix }}-preview" class="form-text text-muted d-none">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                <span id="{{ $addrPrefix }}-preview-text"></span>
            </div>
        </div>

    </div>

    {{-- Hidden field submitted with the form --}}
    <input type="hidden" name="{{ $addrField }}" id="{{ $addrPrefix }}-hidden" value="{{ $addrValue }}">

</div>

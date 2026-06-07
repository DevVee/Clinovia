@extends('layouts.app')

@section('title', 'Dispensing Record')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dispensing Record #{{ $dispensing->id }}</h4>
        <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($dispensing->dispensed_at)->format('F d, Y — h:i A') }}</p>
    </div>
    <a href="{{ route('dispensing.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Records
    </a>
</div>

<div class="row g-4">
    {{-- Main info --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-capsule text-primary me-2"></i> Dispensing Details
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Medicine</dt>
                    <dd class="col-sm-8 fw-semibold fs-5 mb-2">{{ $dispensing->medicine->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Category</dt>
                    <dd class="col-sm-8 mb-2">{{ $dispensing->medicine->category->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Quantity</dt>
                    <dd class="col-sm-8 mb-2">
                        <span class="fs-4 fw-bold text-primary">{{ $dispensing->quantity }}</span>
                        <span class="text-muted">{{ $dispensing->medicine->unit ?? '' }}(s)</span>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Dispensed At</dt>
                    <dd class="col-sm-8 mb-2">{{ \Carbon\Carbon::parse($dispensing->dispensed_at)->format('M d, Y h:i A') }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Dispensed By</dt>
                    <dd class="col-sm-8 mb-2">{{ $dispensing->dispensedBy->name ?? '—' }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Remarks</dt>
                    <dd class="col-sm-8 mb-0">{{ $dispensing->remarks ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Patient & Consultation --}}
    <div class="col-lg-5">
        {{-- Patient --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-person text-success me-2"></i> Patient
            </div>
            <div class="card-body">
                @if($dispensing->patient)
                <div class="fw-bold">{{ $dispensing->patient->full_name }}</div>
                <div class="text-muted small">{{ $dispensing->patient->patient_number }}</div>
                <div class="text-muted small mt-1">{{ ucfirst($dispensing->patient->category) }}</div>
                <a href="{{ route('patients.show', $dispensing->patient) }}"
                   class="btn btn-outline-success btn-sm mt-2">
                    <i class="bi bi-person-lines-fill me-1"></i> View Profile
                </a>
                @else
                <span class="text-muted">Patient not found.</span>
                @endif
            </div>
        </div>

        {{-- Consultation --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom fw-semibold">
                <i class="bi bi-journal-medical text-info me-2"></i> Linked Consultation
            </div>
            <div class="card-body">
                @if($dispensing->consultation)
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($dispensing->consultation->visit_date)->format('M d, Y') }}</div>
                <div class="text-muted small mt-1">{{ Str::limit($dispensing->consultation->chief_complaint, 80) }}</div>
                <a href="{{ route('consultations.show', $dispensing->consultation) }}"
                   class="btn btn-outline-info btn-sm mt-2">
                    <i class="bi bi-eye me-1"></i> View Consultation
                </a>
                @else
                <span class="text-muted small">No consultation linked (walk-in dispensing).</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

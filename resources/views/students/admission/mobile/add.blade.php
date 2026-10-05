@php
$getstudents = Helper::getstudents();
$getgenders = Helper::getgender();
$classType = Helper::classType();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
$getSetting = Helper::getSetting();
$bloodGroupType = Helper::bloodGroupType();
$getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();
$currentSessionName = Session::get('session_name') ?? 'Current Session';
$setting = DB::table('settings')->whereNull('deleted_at')->first();

// Student fields settings from DB
$student_fields = DB::table('student_fields')->whereNull('deleted_at')->where('status', 0)->pluck('field_name');
$studentFields_new = DB::table('student_fields')->whereNull('deleted_at')->where('status', 0)->where('type', 'new_input')->get();
$student_fields_required = DB::table('student_fields')->whereNull('deleted_at')->pluck('required', 'field_name')->toArray();

$billCounterVal = !empty($BillCounter) ? $BillCounter : null;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE ADD ADMISSION STYLES
   - Designed to perfectly mirror the native mobile app experience of enquiryAdd
   - Glassmorphic Hero Card with Live Session & Token Counter
   - Fixed wizard navigation tracker on scroll
   - Touch-optimized segmented toggles & Class selector pills
   - Native bottom action dock for submission & cancel/reset
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-hero-badges {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}
.mob-token-pill {
    font-size: 9.5px;
    background: rgba(74, 222, 128, 0.18);
    border: 1px solid rgba(74, 222, 128, 0.35);
    color: #4ade80;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 800;
}
.mob-hero-desc {
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 9px;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    width: 100%;
}
.mob-act-btn-view {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.mob-act-btn-view:active {
    transform: scale(0.98);
}

/* 2. Interactive Form Step Progress Bar (Fixed below 48px Header) */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 10px;
}
.mob-step-tracker {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.mob-step-tracker.is-fixed {
    position: fixed !important;
    top: 54px !important;
    left: 8px !important;
    right: 8px !important;
    width: auto !important;
    z-index: 999 !important;
    margin: 0 !important;
    background: rgba(0, 24, 51, 0.97) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid #0284c7 !important;
    border-radius: 6px !important;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.45) !important;
}
.mob-step-nodes {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 6px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mob-step-nodes::-webkit-scrollbar {
    display: none;
}
.mob-step-node {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    user-select: none;
    padding: 2px 5px;
    border-radius: 4px;
    white-space: nowrap;
    transition: all .15s ease;
}
.mob-step-node:active {
    transform: scale(0.95);
}
.mob-step-node.active {
    color: #002C54;
    font-weight: 800;
}
.mob-step-node.completed {
    color: #16a34a;
}
.mob-step-tracker.is-fixed .mob-step-node {
    color: #94a3b8;
}
.mob-step-tracker.is-fixed .mob-step-node.active {
    color: #38bdf8;
}
.mob-step-tracker.is-fixed .mob-step-node.completed {
    color: #4ade80;
}
.mob-step-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: #64748b;
    font-weight: 800;
    transition: all .15s ease;
}
.mob-step-node.active .mob-step-dot {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-step-node.completed .mob-step-dot {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}
.mob-step-tracker.is-fixed .mob-step-dot {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
    color: #cbd5e1;
}
.mob-step-tracker.is-fixed .mob-step-node.active .mob-step-dot {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}
.mob-step-tracker.is-fixed .mob-step-node.completed .mob-step-dot {
    background: #16a34a;
    border-color: #4ade80;
    color: #ffffff;
}
.mob-progress-bar-bg {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.mob-step-tracker.is-fixed .mob-progress-bar-bg {
    background: rgba(255, 255, 255, 0.15);
}
.mob-progress-bar-fill {
    height: 100%;
    width: 20%;
    background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    border-radius: 2px;
    transition: width .25s ease;
}

/* 3. Mobile Form Section Cards */
.mob-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 10px 12px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
}
.mob-section-card.highlight-focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.28) !important;
    transform: translateY(-1px);
}
.mob-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 9px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-section-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-step-badge {
    background: #002C54;
    color: #ffffff;
    font-size: 9px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-section-status {
    font-size: 9.5px;
    font-weight: 700;
    color: #94a3b8;
}

/* Form Groups & Inputs */
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-label .req {
    color: #ef4444;
    font-weight: 800;
}
.mob-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.mob-input-prepend {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-right: none;
    color: #002C54;
    font-size: 11px;
    font-weight: 700;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    height: 32px;
    flex-shrink: 0;
}
.mob-input, .mob-select, .mob-textarea {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    transition: all .12s ease;
}
.mob-input-wrap .mob-input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.mob-textarea {
    height: 55px;
    padding: 6px 8px;
    resize: vertical;
}
.mob-input:focus, .mob-select:focus, .mob-textarea:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.1);
}
.mob-input.is-invalid, .mob-select.is-invalid {
    border-color: #ef4444;
}
.mob-invalid-msg, .error.invalid-feedback {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 700;
    margin-top: 2px;
    display: block;
}

/* Segmented Gender Control */
.mob-gender-segment {
    display: flex;
    gap: 6px;
    margin-top: 2px;
}
.mob-gender-btn {
    flex: 1;
    height: 32px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-gender-btn:active {
    transform: scale(0.96);
}
.mob-gender-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 2px 5px rgba(0, 44, 84, 0.2);
}

/* Quick Class Horizontal Pills */
.mob-class-pills-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 4px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-class-pills-scroll::-webkit-scrollbar {
    display: none;
}
.mob-class-pill {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
    flex-shrink: 0;
}
.mob-class-pill:active {
    transform: scale(0.95);
}
.mob-class-pill.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 1px 4px rgba(2, 132, 199, 0.25);
}

/* Compact Photo Upload Cards */
.mob-doc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}
@media (max-width: 360px) {
    .mob-doc-grid {
        grid-template-columns: 1fr;
    }
}
.mob-doc-card {
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 8px 6px;
    background: #f8fafc;
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 85px;
    cursor: pointer;
    transition: all .15s;
}
.mob-doc-card:hover {
    border-color: #0284c7;
    background: #f0f7ff;
}
.mob-doc-card.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #f0fdf4;
}
.mob-doc-card.has-error {
    border-color: #ef4444;
}
.mob-doc-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.mob-doc-icon {
    font-size: 16px;
    color: #002C54;
    margin-bottom: 2px;
}
.mob-doc-title {
    font-size: 10px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.mob-doc-desc {
    font-size: 8.5px;
    color: #64748b;
    margin: 0;
}
.mob-doc-preview {
    display: none;
    margin-top: 4px;
    position: relative;
    z-index: 3;
}
.mob-doc-preview img {
    max-height: 44px;
    max-width: 100%;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
}
.mob-doc-info {
    font-size: 8.5px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 2px;
}
.mob-doc-remove {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 8.5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-doc-error {
    font-size: 8.5px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    margin-bottom: 0;
}

/* 4. Pinned Bottom Action Dock */
.mob-submit-dock {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 8px;
}
.mob-btn-reset {
    width: 36px;
    height: 36px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-btn-submit {
    flex: 1;
    height: 36px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    transition: all .12s ease;
}
.mob-btn-submit:active {
    transform: scale(0.98);
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-user-plus text-primary"></i> New Student Admission
        </div>
        <div class="mob-hero-badges">
            <div class="mob-session-pill">
                <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
            </div>
            @if(!empty($billCounterVal))
                <div class="mob-token-pill" title="Next Admission Counter">
                    <i class="fa fa-hashtag"></i> {{ $billCounterVal }}
                </div>
            @endif
        </div>
    </div>
    <div class="mob-hero-desc">
        Fast regular student admission enrollment &amp; record creation
    </div>
    <div class="mob-actions-bar">
        <a href="{{ url('admissionView') }}" class="mob-act-btn mob-act-btn-view">
            <i class="fa fa-address-book-o"></i> View All Admissions List
        </a>
    </div>
</div>

{{-- 2. Interactive Form Step Progress Tracker --}}
<div class="mob-tracker-container" id="trackerContainer">
    <div class="mob-step-tracker" id="mobStepTracker">
        <div class="mob-step-nodes">
            <div class="mob-step-node active" id="trackerStep1">
                <span class="mob-step-dot" id="dotStep1">1</span>
                <span>Student</span>
            </div>
            <div class="mob-step-node" id="trackerStep2">
                <span class="mob-step-dot" id="dotStep2">2</span>
                <span>Address</span>
            </div>
            <div class="mob-step-node" id="trackerStep3">
                <span class="mob-step-dot" id="dotStep3">3</span>
                <span>Parents</span>
            </div>
            <div class="mob-step-node" id="trackerStep4">
                <span class="mob-step-dot" id="dotStep4">4</span>
                <span>Transport/Bank</span>
            </div>
            <div class="mob-step-node" id="trackerStep5">
                <span class="mob-step-dot" id="dotStep5">5</span>
                <span>Photos</span>
            </div>
        </div>
        <div class="mob-progress-bar-bg">
            <div class="mob-progress-bar-fill" id="formProgressFill"></div>
        </div>
    </div>
</div>

{{-- 3. Main Admission Form --}}
<form id="mob-admission-form" action="{{ url('admissionAdd') }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- Step 1: Student & Admission Details --}}
    <div class="mob-section-card" id="cardStep1">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">01</span> Student &amp; Academic Info
            </div>
            <span class="mob-section-status" id="statusStep1">Required fields</span>
        </div>

        {{-- Admission Number & Ledger No in 2 cols --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('admissionNo') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Admission No. @if(($student_fields_required['admissionNo'] ?? 1) == 0)<span class="req">*</span>@endif</span>
                    </label>
                    <div class="mob-input-wrap">
                        <span class="mob-input-prepend"><i class="fa fa-hashtag"></i></span>
                        <input type="text" 
                               class="mob-input" 
                               name="admissionNo" 
                               id="mob_admissionNo" 
                               placeholder="Adm No." 
                               value="{{ old('admissionNo', $billCounterVal ?? '') }}" 
                               onkeypress="return isNumber(event)">
                    </div>
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('ledger_no') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Ledger No @if(($student_fields_required['ledger_no'] ?? 1) == 0)<span class="req">*</span>@endif</span>
                    </label>
                    <input type="text" class="mob-input" name="ledger_no" id="mob_ledger_no" placeholder="Ledger No" value="{{ old('ledger_no') }}">
                </div>
            </div>
        </div>

        {{-- Student Full Name --}}
        <div class="mob-form-group {{ $student_fields->contains('first_name') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Student Full Name @if(($student_fields_required['first_name'] ?? 1) == 0)<span class="req">*</span>@endif</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-user"></i></span>
                <input type="text" 
                       class="mob-input @error('first_name') is-invalid @enderror" 
                       name="first_name" 
                       id="mob_first_name" 
                       placeholder="e.g. Aarav Sharma" 
                       value="{{ old('first_name') }}" 
                       onkeydown="return /[a-zA-Z ]/i.test(event.key)"
                       autocomplete="off">
            </div>
            @error('first_name')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mobile Number --}}
        <div class="mob-form-group {{ $student_fields->contains('mobile') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Mobile Number @if(($student_fields_required['mobile'] ?? 1) == 0)<span class="req">*</span>@endif</span>
                <span id="mobNumStatus" style="font-size:9px; color:#64748b; font-weight:700;">10 digits</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend">+91</span>
                <input type="tel" 
                       class="mob-input @error('mobile') is-invalid @enderror" 
                       name="mobile" 
                       id="mob_mobile" 
                       placeholder="10-digit mobile" 
                       value="{{ old('mobile') }}" 
                       maxlength="10" 
                       minlength="10" 
                       onkeypress="return isNumber(event)">
            </div>
            @error('mobile')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- 1-Tap Gender Segmented Selector --}}
        <div class="mob-form-group {{ $student_fields->contains('gender_id') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Gender @if(($student_fields_required['gender_id'] ?? 1) == 0)<span class="req">*</span>@endif</span>
            </label>
            <input type="hidden" name="gender_id" id="hidden_gender_id" value="{{ old('gender_id', 1) }}">
            <div class="mob-gender-segment">
                @if(!empty($getgenders))
                    @foreach($getgenders as $g)
                        @php
                            $gIcon = match(strtolower($g->name ?? '')) {
                                'male' => 'fa fa-male',
                                'female' => 'fa fa-female',
                                default => 'fa fa-user-o'
                            };
                            $isAct = (old('gender_id', 1) == $g->id);
                        @endphp
                        <button type="button" class="mob-gender-btn {{ $isAct ? 'active' : '' }}" data-id="{{ $g->id }}">
                            <i class="{{ $gIcon }}"></i> {{ $g->name ?? '' }}
                        </button>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Quick Class Horizontal Pills & Dropdown --}}
        <div class="mob-form-group {{ $student_fields->contains('class_type_id') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Class Applying For @if(($student_fields_required['class_type_id'] ?? 1) == 0)<span class="req">*</span>@endif</span>
                <span style="font-size:9px; color:#0284c7; font-weight:700;">Tap to select</span>
            </label>
            @if(!empty($classType) && count($classType) > 0)
                <div class="mob-class-pills-scroll">
                    @foreach($classType as $type)
                        <button type="button" class="mob-class-pill {{ (old('class_type_id') == $type->id) ? 'active' : '' }}" data-id="{{ $type->id }}">
                            {{ $type->name ?? '' }}
                        </button>
                    @endforeach
                </div>
            @endif
            <select class="mob-select" name="class_type_id" id="mob_class_type_id">
                <option value="">-- Select Class --</option>
                @if(!empty($classType))
                    @foreach($classType as $type)
                        <option value="{{ $type->id ?? '' }}" data-orderby="{{ $type->orderBy ?? '' }}" {{ (old('class_type_id') == $type->id) ? 'selected' : '' }}>
                            {{ $type->name ?? '' }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- Stream Subject (dynamic shown if class > 10) --}}
        <div class="mob-form-group" id="stream_subject_div" style="display:none;">
            <label class="mob-form-label">Stream Subject</label>
            <select class="mob-select" multiple id="stream_subject" name="stream_subject[]" style="height:auto; min-height:34px;">
                <option value="">Select</option>
            </select>
        </div>

        {{-- Date of Birth & Date of Admission --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('dob') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Date of Birth @if(($student_fields_required['dob'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="date" class="mob-input" name="dob" id="mob_dob" value="{{ old('dob') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('admission_date') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Adm. Date @if(($student_fields_required['admission_date'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="date" class="mob-input" name="admission_date" id="mob_admission_date" value="{{ old('admission_date', date('Y-m-d')) }}">
                </div>
            </div>
        </div>

        {{-- Aadhaar & Jan Aadhaar --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('aadhaar') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Aadhaar No. @if(($student_fields_required['aadhaar'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="aadhaar" id="mob_aadhaar" placeholder="12-digit Aadhaar" maxlength="12" value="{{ old('aadhaar') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('jan_aadhaar') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Jan Aadhaar @if(($student_fields_required['jan_aadhaar'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="jan_aadhaar" id="mob_jan_aadhaar" placeholder="10-digit Jan Aadhaar" maxlength="10" value="{{ old('jan_aadhaar') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
        </div>

        {{-- Category & Blood Group --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('category') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Category @if(($student_fields_required['category'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select" name="category" id="mob_category">
                        <option value="">Select</option>
                        @foreach(['OBC', 'GEN', 'SC', 'ST', 'BC', 'SBC', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ (old('category') == $cat || $cat == 'OBC') ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('blood_group') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Blood Group @if(($student_fields_required['blood_group'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select" name="blood_group" id="mob_blood_group">
                        <option value="">Select</option>
                        @if(!empty($bloodGroupType))
                            @foreach($bloodGroupType as $bg)
                                <option value="{{ $bg->name ?? '' }}" {{ (old('blood_group') == ($bg->name ?? '')) ? 'selected' : '' }}>{{ $bg->name ?? '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        {{-- Email & Medium --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('email') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Email @if(($student_fields_required['email'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="email" class="mob-input" name="email" id="mob_email" placeholder="example@mail.com" value="{{ old('email') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('medium') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Medium @if(($student_fields_required['medium'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select" name="medium" id="mob_medium">
                        <option value="">Select</option>
                        <option value="Hindi" {{ old('medium') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                        <option value="English" {{ old('medium') == 'English' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Religion & Caste --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('religion') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Religion @if(($student_fields_required['religion'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select" name="religion" id="mob_religion">
                        <option value="">Select</option>
                        @foreach(['Hindu', 'Islam', 'Sikh', 'Buddhism', 'Adivasi', 'Jain', 'Christianity', 'Other'] as $rel)
                            <option value="{{ $rel }}" {{ (old('religion') == $rel || $rel == 'Hindu') ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('caste_category') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Caste @if(($student_fields_required['caste_category'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="caste_category" id="mob_caste_category" placeholder="Caste" value="{{ old('caste_category') }}">
                </div>
            </div>
        </div>

        {{-- Custom / New Fields --}}
        @if(!empty($studentFields_new) && count($studentFields_new) > 0)
            @foreach($studentFields_new as $field)
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>{{ $field->field_label }} @if($field->required == 0)<span class="req">*</span>@endif</span>
                    </label>
                    @if($field->field_type == 'text' || $field->field_type == 'email' || $field->field_type == 'number')
                        <input type="{{ $field->field_type }}" name="{{ $field->field_name }}" class="mob-input" placeholder="{{ $field->field_label }}" value="{{ old($field->field_name, $field->default_value) }}">
                    @elseif($field->field_type == 'date')
                        <input type="date" name="{{ $field->field_name }}" class="mob-input" value="{{ old($field->field_name, $field->default_value) }}">
                    @elseif($field->field_type == 'dropdown')
                        <select name="{{ $field->field_name }}" class="mob-select">
                            <option value="">Select</option>
                            @foreach(explode(',', $field->default_value ?? '') as $option)
                                <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    {{-- Step 2: Address Details --}}
    <div class="mob-section-card {{ ($student_fields->intersect(['country','state','city','district','tehsil','village_city','address','pincode'])->isEmpty()) ? 'd-none' : '' }}" id="cardStep2">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">02</span> Residential Address
            </div>
            <span class="mob-section-status" id="statusStep2">Location details</span>
        </div>

        {{-- State & City --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('state') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">State @if(($student_fields_required['state'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select stateId" id="mob_state_id" name="state">
                        <option value="">Select State</option>
                        @if(!empty($getState))
                            @foreach($getState as $st)
                                <option value="{{ $st->id ?? ''}}" {{ ($st->id == ($getSetting->state_id ?? 13)) ? 'selected' : '' }}>{{ $st->name ?? ''}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('city') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">City @if(($student_fields_required['city'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select cityId" id="mob_city_id" name="city">
                        <option value="">Select City</option>
                        @if(!empty($getCity))
                            @foreach($getCity as $ct)
                                <option value="{{ $ct->id ?? ''}}" {{ ($ct->id == ($getSetting->city_id ?? '')) ? 'selected' : '' }}>{{ $ct->name ?? ''}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        {{-- District & Tehsil --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('district') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">District @if(($student_fields_required['district'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="district" id="mob_district" placeholder="District" value="{{ old('district') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('tehsil') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Tehsil @if(($student_fields_required['tehsil'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="tehsil" id="mob_tehsil" placeholder="Tehsil" value="{{ old('tehsil') }}">
                </div>
            </div>
        </div>

        {{-- Village / City & Pincode --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('village_city') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Village / Town @if(($student_fields_required['village_city'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="village_city" id="mob_village_city" placeholder="Village / Area" value="{{ old('village_city') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('pincode') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Pin Code @if(($student_fields_required['pincode'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="pincode" id="mob_pincode" placeholder="6-digit PIN" maxlength="6" value="{{ old('pincode') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
        </div>

        {{-- Full Residential Address --}}
        <div class="mob-form-group {{ $student_fields->contains('address') ? '' : 'd-none' }}">
            <label class="mob-form-label">Full Address @if(($student_fields_required['address'] ?? 1) == 0)<span class="req">*</span>@endif</label>
            <textarea class="mob-textarea" name="address" id="mob_address" placeholder="House no, Street name, Colony...">{{ old('address') }}</textarea>
        </div>
    </div>

    {{-- Step 3: Parents & Guardian Details --}}
    <div class="mob-section-card {{ ($student_fields->intersect(['father_name','father_mobile','father_aadhaar','father_occupation','mother_name','mother_mob','mother_aadhaar','mother_occupation','guardian_name','guardian_mobile'])->isEmpty()) ? 'd-none' : '' }}" id="cardStep3">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">03</span> Parents &amp; Guardian Info
            </div>
            <span class="mob-section-status" id="statusStep3">Required fields</span>
        </div>

        {{-- Father's Name --}}
        <div class="mob-form-group {{ $student_fields->contains('father_name') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Father's Name @if(($student_fields_required['father_name'] ?? 1) == 0)<span class="req">*</span>@endif</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-male"></i></span>
                <input type="text" 
                       class="mob-input @error('father_name') is-invalid @enderror" 
                       name="father_name" 
                       id="mob_father_name" 
                       placeholder="Father's full name" 
                       value="{{ old('father_name') }}" 
                       onkeydown="return /[a-zA-Z ]/i.test(event.key)">
            </div>
            @error('father_name')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Father's Mobile & Occupation --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('father_mobile') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Father Contact @if(($student_fields_required['father_mobile'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="tel" class="mob-input" name="father_mobile" id="mob_father_mobile" placeholder="10 digits" maxlength="10" value="{{ old('father_mobile') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('father_occupation') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Occupation @if(($student_fields_required['father_occupation'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="father_occupation" id="mob_father_occupation" placeholder="Occupation" value="{{ old('father_occupation') }}">
                </div>
            </div>
        </div>

        {{-- Mother's Name --}}
        <div class="mob-form-group {{ $student_fields->contains('mother_name') ? '' : 'd-none' }}">
            <label class="mob-form-label">
                <span>Mother's Name @if(($student_fields_required['mother_name'] ?? 1) == 0)<span class="req">*</span>@endif</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-female"></i></span>
                <input type="text" 
                       class="mob-input" 
                       name="mother_name" 
                       id="mob_mother_name" 
                       placeholder="Mother's full name" 
                       value="{{ old('mother_name') }}" 
                       onkeydown="return /[a-zA-Z ]/i.test(event.key)">
            </div>
        </div>

        {{-- Mother's Mobile & Occupation --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('mother_mob') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Mother Contact @if(($student_fields_required['mother_mob'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="tel" class="mob-input" name="mother_mob" id="mob_mother_mob" placeholder="10 digits" maxlength="10" value="{{ old('mother_mob') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('mother_occupation') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Occupation @if(($student_fields_required['mother_occupation'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="mother_occupation" id="mob_mother_occupation" placeholder="Occupation" value="{{ old('mother_occupation') }}">
                </div>
            </div>
        </div>

        {{-- Guardian Name & Mobile --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('guardian_name') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Guardian Name @if(($student_fields_required['guardian_name'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="guardian_name" id="mob_guardian_name" placeholder="Guardian name" value="{{ old('guardian_name') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('guardian_mobile') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Guardian Mobile @if(($student_fields_required['guardian_mobile'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="tel" class="mob-input" name="guardian_mobile" id="mob_guardian_mobile" placeholder="10 digits" maxlength="10" value="{{ old('guardian_mobile') }}" onkeypress="return isNumber(event)">
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: Transport & Bank Details --}}
    <div class="mob-section-card {{ ($student_fields->intersect(['transport','bus_number','bus_route','bank_name','bank_account','ifsc'])->isEmpty()) ? 'd-none' : '' }}" id="cardStep4">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">04</span> Transport &amp; Bank Details
            </div>
            <span class="mob-section-status">Optional / Facilities</span>
        </div>

        {{-- Transport Toggle & Bus No --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('transport') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Bus Facility @if(($student_fields_required['transport'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <select class="mob-select" name="transport" id="mob_transport">
                        <option value="No" {{ old('transport') == 'No' ? 'selected' : '' }}>No</option>
                        <option value="Yes" {{ old('transport') == 'Yes' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('bus_number') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Bus Number @if(($student_fields_required['bus_number'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="bus_number" id="mob_bus_number" placeholder="Bus No." value="{{ old('bus_number') }}">
                </div>
            </div>
        </div>

        {{-- Bank Name & Account No --}}
        <div class="row">
            <div class="col-6 pr-1 {{ $student_fields->contains('bank_name') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Bank Name @if(($student_fields_required['bank_name'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="bank_name" id="mob_bank_name" placeholder="Bank Name" value="{{ old('bank_name') }}">
                </div>
            </div>
            <div class="col-6 pl-1 {{ $student_fields->contains('bank_account') ? '' : 'd-none' }}">
                <div class="mob-form-group">
                    <label class="mob-form-label">Account No. @if(($student_fields_required['bank_account'] ?? 1) == 0)<span class="req">*</span>@endif</label>
                    <input type="text" class="mob-input" name="bank_account" id="mob_bank_account" placeholder="A/C Number" value="{{ old('bank_account') }}">
                </div>
            </div>
        </div>

        {{-- IFSC Code --}}
        <div class="mob-form-group {{ $student_fields->contains('ifsc') ? '' : 'd-none' }}">
            <label class="mob-form-label">IFSC Code @if(($student_fields_required['ifsc'] ?? 1) == 0)<span class="req">*</span>@endif</label>
            <input type="text" class="mob-input" name="ifsc" id="mob_ifsc" placeholder="e.g. SBIN0001234" value="{{ old('ifsc') }}">
        </div>
    </div>

    {{-- Step 5: Photographs Upload --}}
    <div class="mob-section-card {{ $student_fields->intersect(['student_img','father_img','mother_img'])->isEmpty() ? 'd-none' : '' }}" id="cardStep5">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">05</span> Upload Photographs
            </div>
            <span class="mob-section-status">JPG / PNG (Max 2MB)</span>
        </div>

        <div class="mob-doc-grid">
            {{-- Student Photo --}}
            <div class="mob-doc-card {{ $student_fields->contains('student_img') ? '' : 'd-none' }}" id="mob_card_student_img">
                <input type="file" class="mob-doc-input adm-doc-input" id="student_img" name="student_img" accept="image/png, image/jpg, image/jpeg" data-card="mob_card_student_img" data-error="mob_err_student_img">
                <i class="fa fa-user mob-doc-icon"></i>
                <p class="mob-doc-title">Student Photo @if(($student_fields_required['student_img'] ?? 1) == 0)<span class="req">*</span>@endif</p>
                <span class="mob-doc-desc">Tap to select</span>
                <div class="mob-doc-preview">
                    <img src="" alt="Preview">
                    <button type="button" class="mob-doc-remove adm-doc-remove" data-input="student_img"><i class="fa fa-times"></i></button>
                </div>
                <div class="mob-doc-info"></div>
                <p class="mob-doc-error" id="mob_err_student_img"></p>
            </div>

            {{-- Father Photo --}}
            <div class="mob-doc-card {{ $student_fields->contains('father_img') ? '' : 'd-none' }}" id="mob_card_father_img">
                <input type="file" class="mob-doc-input adm-doc-input" id="father_img" name="father_img" accept="image/png, image/jpg, image/jpeg" data-card="mob_card_father_img" data-error="mob_err_father_img">
                <i class="fa fa-male mob-doc-icon"></i>
                <p class="mob-doc-title">Father Photo @if(($student_fields_required['father_img'] ?? 1) == 0)<span class="req">*</span>@endif</p>
                <span class="mob-doc-desc">Tap to select</span>
                <div class="mob-doc-preview">
                    <img src="" alt="Preview">
                    <button type="button" class="mob-doc-remove adm-doc-remove" data-input="father_img"><i class="fa fa-times"></i></button>
                </div>
                <div class="mob-doc-info"></div>
                <p class="mob-doc-error" id="mob_err_father_img"></p>
            </div>

            {{-- Mother Photo --}}
            <div class="mob-doc-card {{ $student_fields->contains('mother_img') ? '' : 'd-none' }}" id="mob_card_mother_img">
                <input type="file" class="mob-doc-input adm-doc-input" id="mother_img" name="mother_img" accept="image/png, image/jpg, image/jpeg" data-card="mob_card_mother_img" data-error="mob_err_mother_img">
                <i class="fa fa-female mob-doc-icon"></i>
                <p class="mob-doc-title">Mother Photo @if(($student_fields_required['mother_img'] ?? 1) == 0)<span class="req">*</span>@endif</p>
                <span class="mob-doc-desc">Tap to select</span>
                <div class="mob-doc-preview">
                    <img src="" alt="Preview">
                    <button type="button" class="mob-doc-remove adm-doc-remove" data-input="mother_img"><i class="fa fa-times"></i></button>
                </div>
                <div class="mob-doc-info"></div>
                <p class="mob-doc-error" id="mob_err_mother_img"></p>
            </div>
        </div>
    </div>

    {{-- 4. Pinned Bottom Submit Action Dock --}}
    <div class="mob-submit-dock">
        <button type="reset" class="mob-btn-reset" id="btnMobReset" title="Reset Form">
            <i class="fa fa-refresh"></i>
        </button>
        <button type="submit" class="mob-btn-submit" id="btnMobSubmit">
            <i class="fa fa-check-circle"></i> Save &amp; Submit Admission
        </button>
    </div>

</form>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var baseUrl = "{{ url('/') }}";

    // 1. Gender Segment Selector
    $('.mob-gender-btn').on('click', function() {
        $('.mob-gender-btn').removeClass('active');
        $(this).addClass('active');
        $('#hidden_gender_id').val($(this).attr('data-id'));
        updateTracker();
    });

    // 2. Class Quick Pills Selector
    $('.mob-class-pill').on('click', function() {
        $('.mob-class-pill').removeClass('active');
        $(this).addClass('active');
        const classId = $(this).attr('data-id');
        $('#mob_class_type_id').val(classId).trigger('change');
        updateTracker();
    });

    $('#mob_class_type_id').on('change', function() {
        const val = $(this).val();
        $('.mob-class-pill').removeClass('active');
        if (val) {
            $(`.mob-class-pill[data-id="${val}"]`).addClass('active');
        }

        // Stream subject check (if class orderBy > 10)
        const orderBy = parseInt($(this).find('option:selected').attr('data-orderby')) || 0;
        if (orderBy > 10) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: baseUrl + '/getStreamSubjects',
                data: { class_type_id: val },
                success: function(data) {
                    var options = "";
                    $('#stream_subject').html("");
                    if (data && data.length) {
                        for(var i = 0; i < data.length; i++){
                            options += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
                        }
                    }
                    $('#stream_subject').html(options);
                    $('#stream_subject_div').show();
                }
            });
        } else {
            $('#stream_subject').html("");
            $('#stream_subject_div').hide();
        }
        updateTracker();
    });

    // 3. Mobile Number Validation Feedback
    $('#mob_mobile').on('input', function() {
        const len = $(this).val().length;
        if (len === 10) {
            $('#mobNumStatus').html('<span class="text-success"><i class="fa fa-check-circle"></i> Valid</span>');
        } else if (len > 0) {
            $('#mobNumStatus').html(`<span class="text-warning">${10 - len} digits left</span>`);
        } else {
            $('#mobNumStatus').text('10 digits');
        }
        updateTracker();
    });

    // 4. Fixed Tracker Lock on Scroll (Beneath 48px Header + 6px gap)
    function checkStickyTracker() {
        const $container = $('#trackerContainer');
        const $tracker = $('#mobStepTracker');
        if (!$container.length || !$tracker.length) return;

        const containerTop = $container.offset().top;
        const scrollY = $(window).scrollTop() || window.pageYOffset || document.documentElement.scrollTop || 0;
        const triggerPoint = containerTop - 54;

        if (scrollY >= triggerPoint && triggerPoint > 0) {
            if (!$tracker.hasClass('is-fixed')) {
                const h = $tracker.outerHeight();
                $container.css('min-height', h + 'px');
                $tracker.addClass('is-fixed');
            }
        } else {
            if ($tracker.hasClass('is-fixed')) {
                $tracker.removeClass('is-fixed');
                $container.css('min-height', '');
            }
        }
    }

    // 5. Wizard Node Touch Scroll
    function scrollToStep(cardSelector, stepNodeSelector) {
        const $card = $(cardSelector);
        if ($card.length) {
            const targetOffset = $card.offset().top - 112;

            $('html, body').stop().animate({
                scrollTop: Math.max(0, targetOffset)
            }, 300, function() {
                checkStickyTracker();
            });

            $('.mob-step-node').removeClass('active');
            $(stepNodeSelector).addClass('active');

            $card.addClass('highlight-focus');
            setTimeout(function() {
                $card.removeClass('highlight-focus');
            }, 900);
        }
    }

    $('#trackerStep1').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep1', '#trackerStep1');
    });
    $('#trackerStep2').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep2', '#trackerStep2');
    });
    $('#trackerStep3').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep3', '#trackerStep3');
    });
    $('#trackerStep4').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep4', '#trackerStep4');
    });
    $('#trackerStep5').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep5', '#trackerStep5');
    });

    // Auto update active wizard step on user scroll
    function onUserScroll() {
        checkStickyTracker();

        const scrollPos = ($(window).scrollTop() || window.pageYOffset || 0) + 120;
        const s1Top = $('#cardStep1').length ? $('#cardStep1').offset().top : 0;
        const s2Top = $('#cardStep2').length ? $('#cardStep2').offset().top : 0;
        const s3Top = $('#cardStep3').length ? $('#cardStep3').offset().top : 0;
        const s4Top = $('#cardStep4').length ? $('#cardStep4').offset().top : 0;
        const s5Top = $('#cardStep5').length ? $('#cardStep5').offset().top : 0;

        $('.mob-step-node').removeClass('active');
        if (s5Top && scrollPos >= s5Top) {
            $('#trackerStep5').addClass('active');
        } else if (s4Top && scrollPos >= s4Top) {
            $('#trackerStep4').addClass('active');
        } else if (s3Top && scrollPos >= s3Top) {
            $('#trackerStep3').addClass('active');
        } else if (s2Top && scrollPos >= s2Top) {
            $('#trackerStep2').addClass('active');
        } else {
            $('#trackerStep1').addClass('active');
        }
    }

    window.addEventListener('scroll', onUserScroll, { passive: true });
    window.addEventListener('touchmove', onUserScroll, { passive: true });
    $(window).on('resize orientationchange', checkStickyTracker);
    setTimeout(checkStickyTracker, 60);

    // 6. Dynamic Step Tracker Validation
    function updateTracker() {
        const nameFilled = ($('#mob_first_name').val() || '').trim().length > 0;
        const mobileFilled = ($('#mob_mobile').val() || '').trim().length === 10;
        const fatherFilled = ($('#mob_father_name').val() || '').trim().length > 0;
        const addressFilled = ($('#mob_address').val() || '').trim().length > 0 || ($('#mob_village_city').val() || '').trim().length > 0;

        let step1Ok = nameFilled;
        let step2Ok = addressFilled;
        let step3Ok = fatherFilled;

        // Step 1
        $('#trackerStep1').toggleClass('completed', step1Ok);
        $('#dotStep1').html(step1Ok ? '<i class="fa fa-check"></i>' : '1');
        $('#statusStep1').text(step1Ok ? '✓ Completed' : 'Required fields');
        $('#statusStep1').css('color', step1Ok ? '#16a34a' : '#94a3b8');

        // Step 2
        $('#trackerStep2').toggleClass('completed', step2Ok);
        $('#dotStep2').html(step2Ok ? '<i class="fa fa-check"></i>' : '2');
        $('#statusStep2').text(step2Ok ? '✓ Completed' : 'Location details');
        $('#statusStep2').css('color', step2Ok ? '#16a34a' : '#94a3b8');

        // Step 3
        $('#trackerStep3').toggleClass('completed', step3Ok);
        $('#dotStep3').html(step3Ok ? '<i class="fa fa-check"></i>' : '3');
        $('#statusStep3').text(step3Ok ? '✓ Completed' : 'Required fields');
        $('#statusStep3').css('color', step3Ok ? '#16a34a' : '#94a3b8');

        // Progress Fill
        let progress = 20;
        if (nameFilled) progress += 20;
        if (mobileFilled) progress += 20;
        if (fatherFilled) progress += 20;
        if (addressFilled) progress += 20;
        $('#formProgressFill').css('width', Math.min(100, progress) + '%');
    }

    $('input, textarea, select').on('input change', updateTracker);

    // 7. Document Upload & Previews
    var maxSize = 2 * 1024 * 1024; // 2MB

    function clearDoc(input) {
        var $card = $('#' + input.data('card'));
        input.val('');
        $card.removeClass('has-file has-error');
        $card.find('.mob-doc-preview').hide().find('img').attr('src', '');
        $card.find('.mob-doc-info').text('');
    }

    $(document).on('change', '.adm-doc-input', function () {
        var input = $(this);
        var $card = $('#' + input.data('card'));
        var $err = $('#' + input.data('error'));
        $err.html('');
        $card.removeClass('has-error');
        var file = this.files && this.files[0];
        if (!file) { clearDoc(input); return; }

        var ext = (file.name.split('.').pop() || '').toLowerCase();
        if (['png', 'jpg', 'jpeg'].indexOf(ext) === -1) {
            $err.html('Only JPG/PNG images allowed');
            clearDoc(input); $card.addClass('has-error'); return;
        }
        if (file.size > maxSize) {
            $err.html('Image size must be under 2MB');
            clearDoc(input); $card.addClass('has-error'); return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $card.find('.mob-doc-preview').show().find('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
        $card.addClass('has-file');
        $card.find('.mob-doc-info').text(file.name.length > 18 ? file.name.substring(0, 15) + '...' : file.name);
    });

    $(document).on('click', '.adm-doc-remove', function (e) {
        e.preventDefault(); e.stopPropagation();
        var input = $('#' + $(this).data('input'));
        clearDoc(input);
        $('#' + input.data('error')).html('');
    });

    $('#btnMobReset').on('click', function() {
        var form = document.getElementById('mob-admission-form');
        form.reset();
        $('.adm-doc-input').each(function () { clearDoc($(this)); });
        $('.mob-doc-error').html('');
        $('.mob-class-pill').removeClass('active');
        updateTracker();
    });

    // 8. Form Submission AJAX with Toastr / Fallback Feedback
    $('#mob-admission-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnMobSubmit');
        var formData = new FormData(this);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: $form.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function() {
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving Admission...');
                $('.mob-invalid-msg, .error.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');
            },
            success: function(response) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Admission added successfully');
                } else {
                    alert(response.message || 'Admission added successfully');
                }
                
                if (response.print_url) {
                    if (confirm('Admission Saved! Do you want to view/print admission receipt now?')) {
                        window.open(response.print_url, '_blank');
                    }
                }
                
                setTimeout(function() {
                    window.location.href = "{{ url('admissionView') }}";
                }, 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save &amp; Submit Admission');

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var firstErrorField = null;
                    $.each(xhr.responseJSON.errors, function(key, val) {
                        var $input = $form.find("[name='" + key + "']");
                        if (!$input.length) {
                            $input = $form.find("#mob_" + key);
                        }
                        if ($input.length) {
                            $input.addClass('is-invalid');
                            $input.closest('.mob-form-group').append('<span class="mob-invalid-msg">' + val[0] + '</span>');
                            if (!firstErrorField) firstErrorField = $input;
                        }
                    });

                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: Math.max(0, firstErrorField.offset().top - 120)
                        }, 300);
                    }

                    if (typeof toastr !== 'undefined') {
                        toastr.error('Please correct the highlighted errors.');
                    }
                } else {
                    var msg = xhr.responseJSON?.message || 'Failed to submit admission. Please try again.';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            }
        });
    });

    updateTracker();
});

function isNumber(evt) {
    var ch = String.fromCharCode(evt.which);
    if (!(/[0-9]/).test(ch)) {
        evt.preventDefault();
    }
}
</script>
@endsection

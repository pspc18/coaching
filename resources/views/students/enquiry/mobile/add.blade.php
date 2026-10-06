@php
$getTypeclass = Helper::classType();
$getCountry = Helper::getCountry();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getgenders = Helper::getgender();
$getSetting = Helper::getSetting();
$users = $users ?? [];
$references = $references ?? [];
$responses = $responses ?? [];
$currentSessionName = Session::get('session_name') ?? 'Current Session';
$billCounterVal = !empty($BillCounter->counter) ? ($BillCounter->counter + 1) : null;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - CREATIVE NATIVE MOBILE ADD ENQUIRY STYLES
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

/* Fast Action Bar (Single Prominent View Button) */
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
}
.mob-step-node {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    user-select: none;
    padding: 3px 6px;
    border-radius: 4px;
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
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8.5px;
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

/* Mobile Form Group */
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
    height: 60px;
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
.mob-invalid-msg {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 700;
    margin-top: 2px;
    display: block;
}

/* 4. Creative Element: 1-Tap Gender Segmented Control */
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

/* 5. Creative Element: Quick Horizontal Class Selector Pills */
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

/* 6. Creative Element: Discussion Preset Tags */
.mob-presets-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 5px;
}
.mob-preset-chip {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .1s ease;
}
.mob-preset-chip:active {
    background: #dbeafe;
    transform: scale(0.96);
}

/* 7. Pinned Bottom Action Dock */
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
            <i class="fa fa-user-plus text-primary"></i> New Student Enquiry
        </div>
        <div class="mob-hero-badges">
            <div class="mob-session-pill">
                <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
            </div>
            @if(!empty($billCounterVal))
                <div class="mob-token-pill" title="Next Registration Counter">
                    <i class="fa fa-hashtag"></i> {{ $billCounterVal }}
                </div>
            @endif
        </div>
    </div>
    <div class="mob-hero-desc">
        Fast walk-in student admission &amp; counseling registration
    </div>
    <div class="mob-actions-bar">
        <a href="{{ url('enquiryView') }}" class="mob-act-btn mob-act-btn-view">
            <i class="fa fa-address-book-o"></i> View All Enquiries List
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
                <span>Parents</span>
            </div>
            <div class="mob-step-node" id="trackerStep3">
                <span class="mob-step-dot" id="dotStep3">3</span>
                <span>Counseling</span>
            </div>
        </div>
        <div class="mob-progress-bar-bg">
            <div class="mob-progress-bar-fill" id="formProgressFill"></div>
        </div>
    </div>
</div>

{{-- 3. Main Form --}}
<form id="mob-enquiry-form" action="{{ url('enquiryAdd') }}" method="post">
    @csrf

    {{-- Step 1: Student Information --}}
    <div class="mob-section-card" id="cardStep1">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">01</span> Student Information
            </div>
            <span class="mob-section-status" id="statusStep1">Required fields</span>
        </div>

        {{-- Student Full Name --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Student Full Name <span class="req">*</span></span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-user"></i></span>
                <input type="text" 
                       class="mob-input @error('first_name') is-invalid @enderror" 
                       name="first_name" 
                       id="mob_first_name" 
                       placeholder="e.g. Aarav Sharma" 
                       value="{{ old('first_name') }}" 
                       required
                       autocomplete="off">
            </div>
            @error('first_name')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mobile Number --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Mobile Number <span class="req">*</span></span>
                <span id="mobNumStatus" style="font-size:9px; color:#64748b; font-weight:700;">10 digits</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend">+91</span>
                <input type="tel" 
                       class="mob-input @error('mobile') is-invalid @enderror" 
                       name="mobile" 
                       id="mob_mobile" 
                       placeholder="10-digit phone number" 
                       value="{{ old('mobile') }}" 
                       maxlength="10" 
                       minlength="10" 
                       onkeypress="return isNumber(event)" 
                       required>
            </div>
            @error('mobile')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- 1-Tap Gender Selector --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Gender <span class="req">*</span></span>
            </label>
            <input type="hidden" name="gender_id" id="hidden_gender_id" value="{{ old('gender_id', 1) }}" required>
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

        {{-- Quick Class Horizontal Pills --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Class Applying For</span>
                <span style="font-size:9px; color:#0284c7; font-weight:700;">Tap to select</span>
            </label>
            @if(!empty($getTypeclass) && count($getTypeclass) > 0)
                <div class="mob-class-pills-scroll">
                    @foreach($getTypeclass as $type)
                        <button type="button" class="mob-class-pill {{ (old('class_type_id') == $type->id) ? 'active' : '' }}" data-id="{{ $type->id }}">
                            {{ $type->name ?? '' }}
                        </button>
                    @endforeach
                </div>
            @endif
            <select class="mob-select" name="class_type_id" id="mob_class_type_id">
                <option value="">-- Select or Tap Above --</option>
                @if(!empty($getTypeclass))
                    @foreach($getTypeclass as $type)
                        <option value="{{ $type->id ?? '' }}" {{ (old('class_type_id') == $type->id) ? 'selected' : '' }}>
                            {{ $type->name ?? '' }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="row">
            {{-- Date of Birth --}}
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">Date of Birth</label>
                    <input type="date" class="mob-input" name="dob" id="mob_dob" value="{{ old('dob') }}">
                </div>
            </div>
            {{-- Email Address --}}
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">Email</label>
                    <input type="email" class="mob-input" name="email" id="mob_email" placeholder="example@mail.com" value="{{ old('email') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Parent & Family Details --}}
    <div class="mob-section-card" id="cardStep2">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">02</span> Parent &amp; Family Details
            </div>
            <span class="mob-section-status" id="statusStep2">Required fields</span>
        </div>

        {{-- Father's Name --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Father's Name <span class="req">*</span></span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-male"></i></span>
                <input type="text" 
                       class="mob-input @error('father_name') is-invalid @enderror" 
                       name="father_name" 
                       id="mob_father_name" 
                       placeholder="Father's full name" 
                       value="{{ old('father_name') }}" 
                       required>
            </div>
            @error('father_name')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mother's Name --}}
        <div class="mob-form-group">
            <label class="mob-form-label">Mother's Name</label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-female"></i></span>
                <input type="text" class="mob-input" name="mother_name" id="mob_mother_name" placeholder="Mother's full name" value="{{ old('mother_name') }}">
            </div>
        </div>

        <div class="row">
            {{-- No of Children --}}
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">No. of Children</label>
                    <input type="number" min="0" max="20" class="mob-input" name="no_of_child" id="mob_no_of_child" placeholder="e.g. 2" value="{{ old('no_of_child') }}">
                </div>
            </div>
            {{-- Location / Area --}}
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">Area / Location</label>
                    <input type="text" class="mob-input" name="address" id="mob_address" placeholder="Colony / Sector" value="{{ old('address') }}">
                </div>
            </div>
        </div>

        {{-- Previous School --}}
        <div class="mob-form-group">
            <label class="mob-form-label">Previous School Attended</label>
            <input type="text" class="mob-input" name="previous_school" id="mob_previous_school" placeholder="Previous institution name" value="{{ old('previous_school') }}">
        </div>
    </div>

    {{-- Step 3: Source, Counseling & Follow-up --}}
    <div class="mob-section-card" id="cardStep3">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">03</span> Source &amp; Counseling Notes
            </div>
            <span class="mob-section-status">Inquiry record</span>
        </div>

        {{-- Reference Source --}}
        <div class="mob-form-group">
            <label class="mob-form-label">Lead / Inquiry Source</label>
            <select class="mob-select" name="reference_id" id="mob_reference_id">
                <option value="">-- Select Source --</option>
                @if(!empty($references))
                    @foreach($references as $ref)
                        <option value="{{ $ref->id }}" {{ (old('reference_id') == $ref->id) ? 'selected' : '' }}>
                            {{ $ref->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="row">
            {{-- Assigned Counselor --}}
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">Counselor / Staff</label>
                    <select class="mob-select" name="assigned_by" id="mob_assigned_by">
                        <option value="">Select</option>
                        @if(!empty($users))
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ (old('assigned_by') == $u->id) ? 'selected' : '' }}>
                                    {{ $u->first_name ?? $u->userName }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            {{-- Response Stage --}}
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">Response Stage</label>
                    <select class="mob-select" name="response_id" id="mob_response_id">
                        <option value="">Select</option>
                        @if(!empty($responses))
                            @foreach($responses as $resp)
                                <option value="{{ $resp->id }}" {{ (old('response_id') == $resp->id) ? 'selected' : '' }}>
                                    {{ $resp->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        {{-- Discussion / Conversation with 1-Tap Presets --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Discussion / Remarks</span>
                <span style="font-size:9px; color:#1d4ed8; font-weight:700;">1-Tap Presets</span>
            </label>
            <div class="mob-presets-wrap">
                <span class="mob-preset-chip" data-text="Admission confirmed for upcoming session.">
                    <i class="fa fa-check-circle"></i> Admission Confirmed
                </span>
                <span class="mob-preset-chip" data-text="Visiting center tomorrow with student.">
                    <i class="fa fa-calendar"></i> Visiting Tomorrow
                </span>
                <span class="mob-preset-chip" data-text="Enquired about fee structure and installment options.">
                    <i class="fa fa-money"></i> Fee Query
                </span>
                <span class="mob-preset-chip" data-text="Requested callback on weekend.">
                    <i class="fa fa-phone"></i> Callback
                </span>
            </div>
            <textarea class="mob-textarea" name="response" id="mob_response" placeholder="Details of conversation with parent/student...">{{ old('response') }}</textarea>
        </div>

        {{-- Special Counselor Note --}}
        <div class="mob-form-group">
            <label class="mob-form-label">Special Counselor Note</label>
            <textarea class="mob-textarea" name="note" id="mob_note" placeholder="Special requirements, discounts, follow-up preferences...">{{ old('note') }}</textarea>
        </div>
    </div>

    {{-- 4. Pinned Bottom Submit Action Dock --}}
    <div class="mob-submit-dock">
        <button type="reset" class="mob-btn-reset" title="Reset Form">
            <i class="fa fa-refresh"></i>
        </button>
        <button type="submit" class="mob-btn-submit" id="btnMobSubmit">
            <i class="fa fa-check-circle"></i> Save &amp; Submit Enquiry
        </button>
    </div>

</form>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
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
        $('#mob_class_type_id').val(classId);
        updateTracker();
    });

    $('#mob_class_type_id').on('change', function() {
        const val = $(this).val();
        $('.mob-class-pill').removeClass('active');
        if (val) {
            $(`.mob-class-pill[data-id="${val}"]`).addClass('active');
        }
        updateTracker();
    });

    // 3. Discussion Preset Chips
    $('.mob-preset-chip').on('click', function() {
        const text = $(this).attr('data-text');
        const $textarea = $('#mob_response');
        const curr = $textarea.val().trim();
        if (curr) {
            $textarea.val(curr + ' ' + text);
        } else {
            $textarea.val(text);
        }
        updateTracker();
    });

    // 4. Mobile Number Validation Feedback
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

    // 5. Fixed Tracker Lock on Scroll (Beneath 48px Header + 6px gap)
    function checkStickyTracker() {
        const $container = $('#trackerContainer');
        const $tracker = $('#mobStepTracker');
        if (!$container.length || !$tracker.length) return;

        const containerTop = $container.offset().top;
        const scrollY = $(window).scrollTop() || window.pageYOffset || document.documentElement.scrollTop || 0;
        const triggerPoint = containerTop - 54; // 48px top bar height + 6px margin

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

    // 6. Wizard Node Touch Scroll to Respective Step
    function scrollToStep(cardSelector, stepNodeSelector) {
        const $card = $(cardSelector);
        if ($card.length) {
            const targetOffset = $card.offset().top - 112; // 48px top bar + 6px gap + ~48px tracker + 10px buffer

            $('html, body').stop().animate({
                scrollTop: Math.max(0, targetOffset)
            }, 300, function() {
                checkStickyTracker();
            });

            // Set active node
            $('.mob-step-node').removeClass('active');
            $(stepNodeSelector).addClass('active');

            // Visual highlight flash
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

    // Auto update active wizard step & fixed styling on user scroll
    function onUserScroll() {
        checkStickyTracker();

        const scrollPos = ($(window).scrollTop() || window.pageYOffset || 0) + 120;
        const s1Top = $('#cardStep1').length ? $('#cardStep1').offset().top : 0;
        const s2Top = $('#cardStep2').length ? $('#cardStep2').offset().top : 0;
        const s3Top = $('#cardStep3').length ? $('#cardStep3').offset().top : 0;

        $('.mob-step-node').removeClass('active');
        if (s3Top && scrollPos >= s3Top) {
            $('#trackerStep3').addClass('active');
        } else if (s2Top && scrollPos >= s2Top) {
            $('#trackerStep2').addClass('active');
        } else {
            $('#trackerStep1').addClass('active');
        }
    }

    window.addEventListener('scroll', onUserScroll, { passive: true });
    window.addEventListener('touchmove', onUserScroll, { passive: true });
    $(window).on('resize orientationchange', function() {
        checkStickyTracker();
    });

    // Initial check
    setTimeout(checkStickyTracker, 50);

    // 6. Dynamic Step Tracker Validation
    function updateTracker() {
        const nameFilled = $('#mob_first_name').val().trim().length > 0;
        const mobileFilled = $('#mob_mobile').val().trim().length === 10;
        const fatherFilled = $('#mob_father_name').val().trim().length > 0;
        const counselingFilled = $('#mob_response').val().trim().length > 0 || $('#mob_note').val().trim().length > 0;

        let step1Ok = nameFilled && mobileFilled;
        let step2Ok = fatherFilled;
        let step3Ok = counselingFilled;

        // Step 1 status
        $('#trackerStep1').toggleClass('completed', step1Ok);
        $('#dotStep1').html(step1Ok ? '<i class="fa fa-check"></i>' : '1');
        $('#statusStep1').text(step1Ok ? '✓ Completed' : 'Required fields');
        if (step1Ok) $('#statusStep1').css('color', '#16a34a'); else $('#statusStep1').css('color', '#94a3b8');

        // Step 2 status
        $('#trackerStep2').toggleClass('completed', step2Ok);
        $('#dotStep2').html(step2Ok ? '<i class="fa fa-check"></i>' : '2');
        $('#statusStep2').text(step2Ok ? '✓ Completed' : 'Required fields');
        if (step2Ok) $('#statusStep2').css('color', '#16a34a'); else $('#statusStep2').css('color', '#94a3b8');

        // Step 3 status
        $('#trackerStep3').toggleClass('completed', step3Ok);
        $('#dotStep3').html(step3Ok ? '<i class="fa fa-check"></i>' : '3');

        // Progress Fill
        let progress = 20;
        if (nameFilled) progress += 20;
        if (mobileFilled) progress += 20;
        if (fatherFilled) progress += 20;
        if (counselingFilled) progress += 20;
        $('#formProgressFill').css('width', progress + '%');
    }

    $('input, textarea, select').on('input change', updateTracker);

    // Form Submit Loading Feedback
    $('#mob-enquiry-form').on('submit', function() {
        const $btn = $('#btnMobSubmit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving Enquiry...');
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
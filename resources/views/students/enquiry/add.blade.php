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
@endphp

@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ADD STUDENT ENQUIRY (COMPACT SIGNATURE THEME)
   Aligned with addUser and Arise 2.0 Theme Guidelines:
   - Font family: Segoe UI, -apple-system, BlinkMacSystemFont, Roboto, sans-serif
   - Base font-size: 11.5px / 12px
   - Sharp border-radius: 2px throughout
   - Dark Navy Hero: #002C54 to #0f3460
   - Compact 29px inputs with #cbd5e1 border
   ========================================================================== */

.content-wrapper.enquiry-add-wrapper,
.enquiry-add-wrapper {
    background: #eef2f6 !important;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 8px 12px 24px;
    min-height: calc(100vh - 56px);
}
.enquiry-add-wrapper * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner */
.enquiry-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 8px;
}
.enquiry-hero-text {
    display: flex;
    flex-direction: column;
}
.enquiry-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.enquiry-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.enquiry-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 28px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    border-color: rgba(255,255,255,.6);
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
    font-weight: 700;
}
.dash-btn-primary:hover {
    background: #0369a1;
    border-color: #0369a1;
}
.dash-btn-outline-dark {
    background: #ffffff;
    color: #475569 !important;
    border-color: #cbd5e1;
}
.dash-btn-outline-dark:hover {
    background: #f8fafc;
    color: #0f172a !important;
}

/* 2. Sharp Form Cards */
.enquiry-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 8px;
}
.enquiry-card-header {
    padding: 6px 12px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.enquiry-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.enquiry-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.enquiry-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
.enquiry-card-body {
    padding: 10px 12px;
}

/* 3. Compact Form Controls & Groups */
.form-group-compact {
    margin-bottom: 8px;
}
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-label-compact .req-star {
    color: #ef4444;
    margin-left: 2px;
    font-weight: 700;
}
.form-label-compact .label-note {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 400;
}

.enquiry-add-wrapper .form-control {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 8px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.enquiry-add-wrapper textarea.form-control {
    height: 58px;
    padding: 5px 8px;
    resize: vertical;
}
.enquiry-add-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

.enquiry-add-wrapper .is-invalid,
.enquiry-add-wrapper .form-control.is-invalid {
    border-color: #ef4444 !important;
}
.enquiry-add-wrapper .invalid-feedback {
    font-size: 10px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    display: block;
}

/* Prepend & Addon Groups */
.input-action-group {
    display: flex;
    align-items: stretch;
    position: relative;
    width: 100%;
    flex-wrap: nowrap !important;
}
.input-action-group .form-control {
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
}
.input-action-group .form-control:not(:first-child) {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
.input-action-group .form-control:not(:last-child) {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
.input-action-prepend {
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
    border-top-left-radius: 2px;
    border-bottom-left-radius: 2px;
    height: 29px;
    flex-shrink: 0;
}

/* 4. Select2 Overrides */
.enquiry-add-wrapper .select2-container .select2-selection--single {
    height: 29px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    padding: 2px 6px !important;
}
.enquiry-add-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 23px !important;
    font-size: 11.5px !important;
    color: #0f172a !important;
    padding-left: 0 !important;
}
.enquiry-add-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 27px !important;
}

/* 5. Form Actions Pinned Strip */
.form-actions-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
    margin-top: 4px;
}
.form-actions-tip {
    font-size: 10.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
}
.form-actions-tip kbd {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    font-size: 9.5px;
    padding: 1px 4px;
    border-radius: 2px;
}
</style>
@endsection

@section('content')

<div class="content-wrapper enquiry-add-wrapper">
    
    {{-- 1. Hero Header Banner --}}
    <div class="enquiry-hero">
        <div class="enquiry-hero-text">
            <span class="enquiry-kicker"><i class="fa fa-address-book-o mr-1"></i> Admissions & Reception</span>
            <h1 class="enquiry-title">
                <i class="fa fa-user-plus text-primary"></i> {{ __('student.Students Enquiry') }}
            </h1>
            <p class="enquiry-subtitle">Register new student admission inquiries with complete personal, parent & counseling information</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('enquiryView') }}" class="dash-btn dash-btn-light">
                <i class="fa fa-eye"></i> {{ __('common.View') }} Enquiries
            </a>
            <a href="{{ url('reception_file') }}" class="dash-btn dash-btn-outline">
                <i class="fa fa-arrow-left"></i> {{ __('common.Back') }}
            </a>
        </div>
    </div>

    {{-- 2. Main Enquiry Form --}}
    <form id="form-submit" action="{{ url('enquiryAdd') }}" method="post" enctype="multipart/form-data">
        @csrf

        {{-- Row 1: Student Information & Parent Details (Full Page 2-Column Utilization) --}}
        <div class="row">
            
            {{-- Column Left: Step 1 Student Basic Details --}}
            <div class="col-lg-6 col-12 pr-lg-1">
                <div class="enquiry-card">
                    <div class="enquiry-card-header">
                        <div class="enquiry-card-title">
                            <span class="card-step">01</span>
                            <span>Student Basic Information</span>
                        </div>
                        <span class="enquiry-card-desc">Personal & academic details</span>
                    </div>
                    <div class="enquiry-card-body">
                        <div class="row">
                            
                            {{-- Student Full Name --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Full Name <span class="req-star">*</span></span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-user"></i></span>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               placeholder="Student full name" 
                                               value="{{ old('first_name') }}" 
                                               required
                                               autocomplete="off">
                                    </div>
                                    @error('first_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Student Mobile --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Mobile No. <span class="req-star">*</span></span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend">+91</span>
                                        <input type="tel" 
                                               class="form-control @error('mobile') is-invalid @enderror" 
                                               id="mobile" 
                                               name="mobile" 
                                               placeholder="10-digit mobile number" 
                                               value="{{ old('mobile') }}" 
                                               maxlength="10" 
                                               minlength="10" 
                                               onkeypress="return isNumber(event)" 
                                               required>
                                    </div>
                                    @error('mobile')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Gender <span class="req-star">*</span></span>
                                    </label>
                                    <select class="form-control select2 @error('gender_id') is-invalid @enderror" id="gender_id" name="gender_id" required>
                                        <option value="">-- Select Gender --</option>
                                        @if(!empty($getgenders))
                                            @foreach($getgenders as $g)
                                                <option value="{{ $g->id }}" {{ (old('gender_id') == $g->id) ? 'selected' : '' }}>
                                                    {{ $g->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('gender_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Date of Birth --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Date of Birth</span>
                                    </label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}">
                                </div>
                            </div>

                            {{-- Class Applying For --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Class Applying For</span>
                                    </label>
                                    <select class="form-control select2" id="class_type_id" name="class_type_id">
                                        <option value="">-- Select Class --</option>
                                        @if(!empty($getTypeclass))
                                            @foreach($getTypeclass as $type)
                                                <option value="{{ $type->id ?? '' }}" {{ (old('class_type_id') == $type->id) ? 'selected' : '' }}>
                                                    {{ $type->name ?? '' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            {{-- Email Address --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Email Address</span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-envelope-o"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="{{ old('email') }}">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Column Right: Step 2 Parent & Family Details --}}
            <div class="col-lg-6 col-12 pl-lg-1">
                <div class="enquiry-card">
                    <div class="enquiry-card-header">
                        <div class="enquiry-card-title">
                            <span class="card-step">02</span>
                            <span>Parent & Guardian Details</span>
                        </div>
                        <span class="enquiry-card-desc">Family background & contact</span>
                    </div>
                    <div class="enquiry-card-body">
                        <div class="row">
                            
                            {{-- Father's Name --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Father's Name <span class="req-star">*</span></span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-male"></i></span>
                                        <input type="text" 
                                               class="form-control @error('father_name') is-invalid @enderror" 
                                               id="father_name" 
                                               name="father_name" 
                                               placeholder="Father's full name" 
                                               value="{{ old('father_name') }}" 
                                               required>
                                    </div>
                                    @error('father_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Mother's Name --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Mother's Name</span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-female"></i></span>
                                        <input type="text" 
                                               class="form-control @error('mother_name') is-invalid @enderror" 
                                               id="mother_name" 
                                               name="mother_name" 
                                               placeholder="Mother's full name" 
                                               value="{{ old('mother_name') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- No of Children --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>No. of Children</span>
                                    </label>
                                    <input type="number" min="0" max="20" class="form-control" id="no_of_child" name="no_of_child" placeholder="e.g. 2" value="{{ old('no_of_child') }}">
                                </div>
                            </div>

                            {{-- Address / Location --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Address / Location</span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-map-marker"></i></span>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="Area, colony or city" value="{{ old('address') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Previous School --}}
                            <div class="col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Previous School Name</span>
                                    </label>
                                    <input type="text" class="form-control" id="previous_school" name="previous_school" placeholder="Previous school or institution attended" value="{{ old('previous_school') }}">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Row 2: Step 3 Source, Counseling & Follow-Up Details --}}
        <div class="row">
            <div class="col-12">
                <div class="enquiry-card">
                    <div class="enquiry-card-header">
                        <div class="enquiry-card-title">
                            <span class="card-step">03</span>
                            <span>Source, Counseling & Discussion Notes</span>
                        </div>
                        <span class="enquiry-card-desc">Lead source, assigned counselor & conversation records</span>
                    </div>
                    <div class="enquiry-card-body">
                        <div class="row">
                            
                            {{-- Reference Source --}}
                            <div class="col-md-4 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Reference / Lead Source</span>
                                    </label>
                                    <select class="form-control select2" id="reference_id" name="reference_id">
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
                            </div>

                            {{-- Assigned Staff / Counselor --}}
                            <div class="col-md-4 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Assigned Counselor / Staff</span>
                                    </label>
                                    <select class="form-control select2" id="assigned_by" name="assigned_by">
                                        <option value="">-- Select Staff --</option>
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
                            <div class="col-md-4 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Response Stage</span>
                                    </label>
                                    <select class="form-control select2" id="response_id" name="response_id">
                                        <option value="">-- Select Stage --</option>
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

                            {{-- Initial Discussion / Response Textarea --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Initial Response / Discussion</span>
                                        <span class="label-note">Walk-in inquiry conversation</span>
                                    </label>
                                    <textarea class="form-control" id="response" name="response" placeholder="Details of conversation with parent/student...">{{ old('response') }}</textarea>
                                </div>
                            </div>

                            {{-- Counselor Note Textarea --}}
                            <div class="col-md-6 col-12">
                                <div class="form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Counselor Note / Requirement</span>
                                        <span class="label-note">Special notes or follow-up preferences</span>
                                    </label>
                                    <textarea class="form-control" id="note" name="note" placeholder="Special requirements, discounts, or follow-up notes...">{{ old('note') }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Form Action Strip --}}
        <div class="form-actions-card">
            <div class="form-actions-tip">
                <i class="fa fa-info-circle text-primary"></i>
                <span>Use <kbd>Tab</kbd> to navigate fields quickly and press <kbd>Ctrl</kbd> + <kbd>S</kbd> to save.</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="reset" class="dash-btn dash-btn-outline-dark">
                    <i class="fa fa-refresh"></i> Reset
                </button>
                <button type="submit" class="dash-btn dash-btn-primary" id="btnSubmitEnquiry">
                    <i class="fa fa-check-circle"></i> Submit Enquiry
                </button>
            </div>
        </div>

    </form>

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Initialize Select2 dropdowns
    $('.select2').select2({
        width: '100%'
    });

    // Keyboard shortcut: Ctrl + S to save form
    $(document).keydown(function (e) {
        if ((e.ctrlKey || e.metaKey) && e.which === 83) {
            e.preventDefault();
            $('#form-submit').submit();
        }
    });

    // Disable double submission
    $('#form-submit').on('submit', function() {
        const $btn = $('#btnSubmitEnquiry');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
    });
});

function isNumber(evt) {
    var ch = String.fromCharCode(evt.which);
    if (!(/[0-9]/).test(ch)) {
        evt.preventDefault();
    }
}
</script>
@endsection
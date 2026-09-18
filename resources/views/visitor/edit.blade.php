@php
$getTypeclass = Helper::classType();
$currentDate = !empty($data->date) ? date('Y-m-d', strtotime($data->date)) : date('Y-m-d');
@endphp

@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EDIT VISITOR (FULL-SCREEN VIEWPORT UTILIZATION)
   Matches Dark Navy ERP Theme (#002C54) & High-Performance UI Guidelines
   ========================================================================== */

.visitor-form-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 10px;
    height: calc(100vh - var(--header-height, 56px) - 16px);
    min-height: 540px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.visitor-form-wrapper * {
    box-sizing: border-box;
}

.visitor-form-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

/* Top Hero Banner */
.visitor-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 6px;
    flex-shrink: 0;
}
.visitor-hero-text {
    display: flex;
    flex-direction: column;
}
.visitor-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.visitor-title {
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.visitor-subtitle {
    font-size: 10px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.visitor-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
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
    background: #e2e8f0 !important;
    color: #001f3d !important;
    border-color: #cbd5e1 !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.22) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}

/* Form Root Container filling vertical space */
.visitor-form-root {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
}

/* Row of Cards (Equal Height Flex Layout) */
.visitor-cards-row {
    display: flex;
    flex: 1 1 auto;
    min-height: 0;
    margin-left: -4px;
    margin-right: -4px;
    margin-bottom: 6px;
}
.visitor-cards-row > [class*="col-"] {
    padding-left: 4px;
    padding-right: 4px;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

/* Sharp Form Cards */
.visitor-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
    display: flex;
    flex-direction: column;
    height: 100%;
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
}
.visitor-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.visitor-card-title {
    font-size: 11.5px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.visitor-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.visitor-card-desc {
    font-size: 9.5px;
    color: #64748b;
    margin: 0;
}

.visitor-card-body {
    padding: 10px 12px;
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow-y: auto;
    min-height: 0;
}

/* Compact Form Controls */
.form-group-compact {
    margin-bottom: 8px;
}
.form-group-compact:last-child {
    margin-bottom: 0;
}
.form-label-compact {
    font-size: 10.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
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
    font-size: 9px;
    color: #64748b;
    font-weight: 400;
}

.visitor-form-wrapper .form-control {
    height: 29px;
    font-size: 11px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 8px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.visitor-form-wrapper textarea.form-control {
    height: 100%;
    min-height: 60px;
    resize: none;
}
.visitor-form-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12) !important;
}

/* Parallel Input Groups */
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
    min-width: 32px;
}

/* Quick Reason Chips */
.quick-tags-container {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 6px;
}
.quick-tag-chip {
    font-size: 9.5px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 2px;
    cursor: pointer;
    font-weight: 500;
    transition: all .15s ease-in-out;
    user-select: none;
}
.quick-tag-chip:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Info Banner in Card */
.visitor-info-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 2px;
    padding: 5px 8px;
    font-size: 9.5px;
    color: #166534;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

/* Sticky Action Bar at Bottom */
.form-submit-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    flex-shrink: 0;
}
.btn-submit-save {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3d;
    height: 29px;
    padding: 0 16px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s ease-in-out;
}
.btn-submit-save:hover {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-cancel {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
}
.btn-cancel:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
    border-color: #94a3b8 !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper visitor-form-wrapper">
    <div class="visitor-form-container">

        {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
        <div class="visitor-hero">
            <div class="visitor-hero-text">
                <span class="visitor-kicker"><i class="fa fa-address-book-o mr-1"></i> Reception &amp; Front Desk</span>
                <h1 class="visitor-title">
                    <i class="fa fa-edit text-info"></i> Edit Visitor Entry #{{ $data->id }}
                </h1>
                <p class="visitor-subtitle">Modify visitor record, tagged student association, identification or visit purpose</p>
            </div>
            <div class="visitor-hero-actions">
                <a href="{{ url('visitorView') }}" class="dash-btn dash-btn-light" title="View Visitors Directory">
                    <i class="fa fa-eye mr-1"></i> View Visitors
                </a>
            </div>
        </div>

        {{-- Validation & Session Messages --}}
        @if(session('message'))
            <div class="alert alert-success py-1 px-3 mb-2" style="font-size: 11px; border-radius: 2px; flex-shrink: 0;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
            </div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger py-1 px-3 mb-2" style="font-size: 11px; border-radius: 2px; flex-shrink: 0;">
                <strong><i class="fa fa-exclamation-triangle mr-1"></i> Please check form errors:</strong>
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Root --}}
        <form action="{{ url('visitorEdit', $data->id) }}" method="post" id="visitorEditForm" class="visitor-form-root">
            @csrf

            <div class="row g-2 visitor-cards-row">
                {{-- Left Column: Visitor & ID Info --}}
                <div class="col-lg-6">
                    <div class="visitor-card">
                        <div class="visitor-card-header">
                            <h2 class="visitor-card-title">
                                <span class="card-step">Step 1</span>
                                <i class="fa fa-user text-primary"></i> Visitor Details &amp; Identity
                            </h2>
                            <span class="visitor-card-desc">Primary contact &amp; ID proof</span>
                        </div>
                        <div class="visitor-card-body">
                            <div>
                                {{-- Visitor Name --}}
                                <div class="form-group-compact">
                                    <label class="form-label-compact" for="visitor_name">
                                        <span>Visitor Full Name <span class="req-star">*</span></span>
                                        <span class="label-note">Full Name</span>
                                    </label>
                                    <div class="input-action-group">
                                        <span class="input-action-prepend"><i class="fa fa-user"></i></span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="visitor_name" 
                                               name="visitor_name" 
                                               placeholder="Enter visitor full name..." 
                                               value="{{ old('visitor_name', $data->visitor_name) }}"
                                               required 
                                               autofocus>
                                    </div>
                                </div>

                                {{-- Visitor Mobile & Visit Date --}}
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="visitor_mobile">
                                                <span>Visitor Mobile <span class="req-star">*</span></span>
                                                <span class="label-note">10 Digits</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-phone"></i></span>
                                                <input type="tel" 
                                                       class="form-control" 
                                                       id="visitor_mobile" 
                                                       name="visitor_mobile" 
                                                       placeholder="e.g. 9876543210" 
                                                       value="{{ old('visitor_mobile', $data->visitor_mobile) }}" 
                                                       maxlength="10" 
                                                       minlength="10" 
                                                       onkeypress="return isNumber(event)"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="date">
                                                <span>Visit Date <span class="req-star">*</span></span>
                                                <span class="label-note">Entry Date</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-calendar"></i></span>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="date" 
                                                       name="date" 
                                                       value="{{ old('date', $currentDate) }}"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ID Name / Type & Number --}}
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="id_name">
                                                <span>ID Proof Type</span>
                                                <span class="label-note">e.g. Aadhaar, DL</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-id-badge"></i></span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="id_name" 
                                                       name="id_name" 
                                                       placeholder="Aadhaar / Voter / DL" 
                                                       value="{{ old('id_name', $data->id_name) }}"
                                                       list="idTypeList">
                                                <datalist id="idTypeList">
                                                    <option value="Aadhaar Card">
                                                    <option value="Driving License">
                                                    <option value="Voter ID Card">
                                                    <option value="PAN Card">
                                                    <option value="Parent ID Card">
                                                    <option value="Passport">
                                                </datalist>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="aadharNo">
                                                <span>ID / Aadhaar Number</span>
                                                <span class="label-note">Document No.</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-id-card-o"></i></span>
                                                <input type="text" 
                                                       class="form-control font-monospace" 
                                                       id="aadharNo" 
                                                       name="aadharNo" 
                                                       placeholder="Enter ID / Card no..." 
                                                       value="{{ old('aadharNo', $data->aadharNo) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="visitor-info-box">
                                <i class="fa fa-info-circle text-success"></i>
                                <span>Official photo ID proof number is recorded for school security and gate verification.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Student Tagging & Visit Purpose --}}
                <div class="col-lg-6">
                    <div class="visitor-card">
                        <div class="visitor-card-header">
                            <h2 class="visitor-card-title">
                                <span class="card-step">Step 2</span>
                                <i class="fa fa-graduation-cap text-info"></i> Visit Purpose &amp; Tagged Student
                            </h2>
                            <span class="visitor-card-desc">Tag student &amp; log reason</span>
                        </div>
                        <div class="visitor-card-body">
                            
                            <div>
                                <div class="row g-2">
                                    {{-- Student Name --}}
                                    <div class="col-sm-7">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="stu_name">
                                                <span>Student Name</span>
                                                <span class="label-note">To Meet / Relative</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-graduation-cap"></i></span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="stu_name" 
                                                       name="stu_name" 
                                                       placeholder="Enter student name..." 
                                                       value="{{ old('stu_name', $data->stu_name) }}">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Class Type --}}
                                    <div class="col-sm-5">
                                        <div class="form-group-compact">
                                            <label class="form-label-compact" for="class_type_id">
                                                <span>Student Class</span>
                                                <span class="label-note">Optional</span>
                                            </label>
                                            <div class="input-action-group">
                                                <span class="input-action-prepend"><i class="fa fa-building-o"></i></span>
                                                <select class="form-control" id="class_type_id" name="class_type_id">
                                                    <option value="">Select Class</option>
                                                    @if(!empty($getTypeclass))
                                                        @foreach($getTypeclass as $type)
                                                            <option value="{{ $type->id ?? '' }}" {{ ($type->id == old('class_type_id', $data->class_type_id)) ? 'selected' : '' }}>
                                                                {{ $type->name ?? '' }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Quick Reason Chips --}}
                                <div class="mt-1">
                                    <label class="form-label-compact mb-1">
                                        <span>Quick Reason Suggestions</span>
                                        <span class="label-note">Click to insert</span>
                                    </label>
                                    <div class="quick-tags-container">
                                        <span class="quick-tag-chip" onclick="addReason('Admission Inquiry')"><i class="fa fa-plus mr-1"></i>Admission Inquiry</span>
                                        <span class="quick-tag-chip" onclick="addReason('Fee Payment / Accounts')"><i class="fa fa-plus mr-1"></i>Fee Payment</span>
                                        <span class="quick-tag-chip" onclick="addReason('Meeting Class Teacher / Principal')"><i class="fa fa-plus mr-1"></i>Teacher Meeting</span>
                                        <span class="quick-tag-chip" onclick="addReason('Document Submission / TC')"><i class="fa fa-plus mr-1"></i>Doc Submission</span>
                                        <span class="quick-tag-chip" onclick="addReason('Official / Vendor Visit')"><i class="fa fa-plus mr-1"></i>Vendor Visit</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Remark / Purpose --}}
                            <div class="form-group-compact d-flex flex-column flex-grow-1 mt-1">
                                <label class="form-label-compact" for="remark">
                                    <span>Purpose of Visit / Detailed Remark</span>
                                    <span class="label-note">Specific details</span>
                                </label>
                                <textarea class="form-control flex-grow-1" 
                                          id="remark" 
                                          name="remark" 
                                          placeholder="Enter purpose of visit...">{{ old('remark', $data->remark) }}</textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Bottom Sticky Action Toolbar --}}
            <div class="form-submit-bar">
                <a href="{{ url('visitorView') }}" class="btn-cancel">
                    <i class="fa fa-arrow-left mr-1"></i> Cancel &amp; Back
                </a>
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn-submit-save" id="btnUpdateVisitor">
                        <i class="fa fa-check-circle mr-1"></i> Update Visitor Entry
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
@endsection

@section('scripts')
<script>
function isNumber(evt) {
    const ch = String.fromCharCode(evt.which);
    if (!(/[0-9]/).test(ch)) {
        evt.preventDefault();
        return false;
    }
    return true;
}

function addReason(reasonText) {
    const textarea = document.getElementById('remark');
    if (!textarea) return;
    const currentVal = textarea.value.trim();
    if (!currentVal) {
        textarea.value = reasonText;
    } else if (!currentVal.includes(reasonText)) {
        textarea.value = currentVal + ', ' + reasonText;
    }
    textarea.focus();
}

$(document).ready(function() {
    $('#visitorEditForm').on('submit', function() {
        const btn = $('#btnUpdateVisitor');
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Updating Entry...');
    });
});
</script>
@endsection
@php
$classType = Helper::classType();
$currentClassId = Session::get('class_type_id');
@endphp

@extends('layout.app')

@section('styles')
<!-- Quill.js Editor Styles (Free, Open-Source & Ultra-Fast) -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

<style>
/* ==========================================================================
   ARISE ERP - ADD HOMEWORK & DPP (COMPACT SIGNATURE THEME)
   Aligned with addUser, Dashboard, and User View guidelines:
   - Font family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif
   - Base font-size: 11.5px / 12px
   - Sharp border-radius: 2px throughout
   - Dark Navy Hero: #002C54 to #0f3460
   - Compact 29px inputs with #cbd5e1 border
   - Zero FOUC: Injected directly into head
   ========================================================================== */

.user-add-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 20px;
    min-height: calc(100vh - 56px);
}
.user-add-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner (Matching addUser & Dashboard Hero) */
.user-hero {
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
}
.user-hero-text {
    display: flex;
    flex-direction: column;
}
.user-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.user-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.user-subtitle {
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

/* Sharp Compact Form Cards (Matching .user-card in addUser) */
.user-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
}
.user-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.user-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.user-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.user-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
.user-card-body {
    padding: 8px 10px;
}

/* Compact Form Grid & Controls */
.form-group-compact {
    margin-bottom: 6px;
}
.form-label-compact {
    font-size: 11px;
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
    font-size: 9.5px;
    color: #64748b;
    font-weight: 400;
}

.form-control-compact,
.user-add-wrapper .form-control {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus,
.user-add-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

.user-add-wrapper .is-invalid,
.user-add-wrapper .form-control.is-invalid {
    border-color: #ef4444 !important;
    background-image: none !important;
}
.user-add-wrapper .invalid-feedback {
    font-size: 10px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    display: block;
}

/* Compact Select2 Styling (Sharp 2px radius) */
.user-add-wrapper .select2-container--default .select2-selection--single {
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    min-height: 29px !important;
    height: 29px;
    padding: 1px 6px;
}
.user-add-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 25px;
    font-size: 11.5px;
    color: #0f172a;
    padding-left: 2px;
}
.user-add-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 27px;
    right: 4px;
}
.user-add-wrapper .select2-container--default.select2-container--focus .select2-selection--single,
.user-add-wrapper .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

/* Quill WYSIWYG Container matching addUser compact cards */
.hw-quill-wrapper {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    overflow: hidden;
}
.hw-quill-wrapper .ql-toolbar.ql-snow {
    border: none;
    border-bottom: 1px solid #cbd5e1;
    background: #fafbfc;
    padding: 5px 8px;
}
.hw-quill-wrapper .ql-container.ql-snow {
    border: none;
    font-family: inherit;
    font-size: 12px;
    min-height: 180px;
    max-height: 320px;
    overflow-y: auto;
}
.ql-editor.ql-blank::before {
    color: #94a3b8;
    font-style: normal;
    font-size: 11.5px;
}

/* Compact Document Upload Cards (Exact 1:1 with addUser) */
.compact-doc-card {
    border: 1px dashed #cbd5e1;
    border-radius: 2px;
    padding: 8px 10px;
    background: #f8fafc;
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100px;
    cursor: pointer;
    transition: all .15s;
}
.compact-doc-card:hover {
    border-color: #002C54;
    background: #f0f7ff;
}
.compact-doc-card.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #f0fdf4;
}
.compact-doc-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.compact-doc-icon {
    font-size: 22px;
    color: #002C54;
    margin-bottom: 3px;
}
.compact-doc-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.compact-doc-desc {
    font-size: 10px;
    color: #64748b;
    margin: 1px 0 0;
}
.compact-doc-preview {
    display: none;
    margin-top: 4px;
    position: relative;
    z-index: 3;
}
.compact-doc-preview img {
    max-height: 48px;
    max-width: 100%;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
}
.compact-doc-info {
    font-size: 10px;
    font-weight: 600;
    color: #0369a1;
    margin-top: 3px;
    max-width: 95%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.compact-doc-remove {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 4;
}
.compact-doc-error {
    font-size: 10px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    margin-bottom: 0;
}

/* Bottom Action Footer Bar (Exact 1:1 with addUser) */
.user-footer-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.btn-compact-submit {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #002C54;
    height: 29px;
    padding: 0 18px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-submit:hover {
    background: #001f3d;
    border-color: #001f3d;
}
.btn-compact-submit:disabled {
    background: #94a3b8;
    border-color: #94a3b8;
    cursor: not-allowed;
}
.btn-compact-reset {
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
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-reset:hover {
    background: #e2e8f0;
    color: #1e293b !important;
}

/* Subject Loader */
.subject-loader {
    display: none;
    font-size: 10px;
    color: #0284c7;
    margin-top: 2px;
    font-weight: 600;
}

@media (max-width: 576px) {
    .user-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 8px 10px;
    }
    .dash-btn {
        flex: 1;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper user-add-wrapper">

    {{-- 1. Top Hero Banner (Sharp Dark Navy Theme - Matching addUser) --}}
    <div class="user-hero">
        <div class="user-hero-text">
            <span class="user-kicker"><i class="fa fa-graduation-cap mr-1"></i> Academic Hub</span>
            <h1 class="user-title"><i class="fa fa-flask mr-1"></i> Add Homework &amp; DPP</h1>
            <p class="user-subtitle">Assign daily practice problems, worksheets, and study material to student batches</p>
        </div>
        <div class="dash-hero-actions d-flex align-items-center gap-1">
            <a href="{{ url('homework/index') }}" class="dash-btn dash-btn-light" title="View Homework List">
                <i class="fa fa-list"></i> Homework List
            </a>
            <a href="{{ url('homework/dashboard') }}" class="dash-btn dash-btn-outline" title="Dashboard">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- 2. Form Content --}}
    <form id="form-submit" action="{{ url('homework/add') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Card 1: Academic & Assignment Schedule Details --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">01</span> Academic &amp; Batch Details
                </h3>
                <span class="user-card-desc">Target class, subject, category, and assignment timeline</span>
            </div>
            <div class="user-card-body">
                <div class="row">

                    {{-- Target Class / Batch --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="class_type_id">
                                <span>Class / Batch <span class="req-star">*</span></span>
                            </label>
                            <select class="form-control form-control-compact select2 @error('class_type_id') is-invalid @enderror" 
                                    id="class_type_id" 
                                    name="class_type_id" 
                                    required>
                                <option value="">{{ __('common.Select') }}</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id ?? '' }}" {{ (old('class_type_id', $currentClassId) == $type->id) ? 'selected' : '' }}>
                                            {{ $type->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('class_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Target Subject (Dynamically Loaded via AJAX) --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="subject_id">
                                <span>Subject <span class="req-star">*</span></span>
                            </label>
                            <select class="form-control form-control-compact select2 @error('subject') is-invalid @enderror" 
                                    id="subject_id" 
                                    name="subject" 
                                    required>
                                <option value="">-- First Select Class --</option>
                            </select>
                            <div class="subject-loader" id="subjectLoader">
                                <i class="fa fa-spinner fa-spin"></i> Loading subjects...
                            </div>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Category / DPP Type (Coaching Standard) --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="homework_type">
                                <span>Category / DPP Type <span class="req-star">*</span></span>
                            </label>
                            <select class="form-control form-control-compact select2" id="homework_type" name="homework_type">
                                <option value="DPP" {{ old('homework_type') == 'DPP' ? 'selected' : '' }}>DPP (Daily Practice Problem)</option>
                                <option value="Worksheet" {{ old('homework_type') == 'Worksheet' ? 'selected' : '' }}>Chapter Worksheet / Exercise</option>
                                <option value="PYQ Sheet" {{ old('homework_type') == 'PYQ Sheet' ? 'selected' : '' }}>PYQ (Previous Years Questions)</option>
                                <option value="Subjective" {{ old('homework_type') == 'Subjective' ? 'selected' : '' }}>Subjective Homework</option>
                                <option value="Revision" {{ old('homework_type') == 'Revision' ? 'selected' : '' }}>Revision &amp; Formula Notes</option>
                            </select>
                        </div>
                    </div>

                    {{-- Est. Duration / Max Marks --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="target_duration">
                                <span>Est. Duration / Marks <span class="label-note">(Optional)</span></span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-compact"
                                   id="target_duration"
                                   name="target_duration"
                                   value="{{ old('target_duration') }}"
                                   placeholder="e.g. 45 Mins / 25 Marks">
                        </div>
                    </div>

                    {{-- Homework Title / Topic --}}
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="title">
                                <span>Homework Title / Topic <span class="req-star">*</span></span>
                            </label>
                            <input type="text"
                                   class="form-control form-control-compact @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   placeholder="e.g. Thermodynamics: First Law & Work Done - Exercise 1.2"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Issue Date --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="homework_issue_date">
                                <span>Issue Date <span class="req-star">*</span></span>
                            </label>
                            <input type="date"
                                   class="form-control form-control-compact @error('homework_issue_date') is-invalid @enderror"
                                   id="homework_issue_date"
                                   name="homework_issue_date"
                                   value="{{ old('homework_issue_date', date('Y-m-d')) }}"
                                   required>
                            @error('homework_issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submission Date --}}
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="submission_date">
                                <span>Submission Deadline <span class="req-star">*</span></span>
                            </label>
                            <input type="date"
                                   class="form-control form-control-compact @error('submission_date') is-invalid @enderror"
                                   id="submission_date"
                                   name="submission_date"
                                   value="{{ old('submission_date') }}"
                                   required>
                            @error('submission_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Card 2: Instructions & Question Details (Free Quill Editor) --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">02</span> Instructions &amp; Question Details
                </h3>
                <span class="user-card-desc">Format guidelines, problem numbers, and formula references</span>
            </div>
            <div class="user-card-body">
                <div class="form-group form-group-compact mb-0">
                    <label class="form-label-compact mb-1">
                        <span>Instructions / Question Content <span class="req-star">*</span></span>
                        <span class="label-note">Rich Text Editor (Quill)</span>
                    </label>

                    {{-- Quill Container --}}
                    <div class="hw-quill-wrapper">
                        <div id="quill-editor">
                            {!! old('description') !!}
                        </div>
                    </div>
                    {{-- Hidden textarea synced on submit --}}
                    <textarea id="compose-textarea" name="description" style="display:none;">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Card 3: Document Upload & Notification Preferences --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">03</span> Attachment &amp; Student Alerts
                </h3>
                <span class="user-card-desc">Attach question PDF or reference file and configure alerts</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    
                    {{-- Left: File Upload Card (Exact 1:1 compact-doc-card with addUser) --}}
                    <div class="col-12 col-md-6 mb-2">
                        <label class="form-label-compact">
                            <span>Upload DPP Sheet / Question File <span class="label-note">(Optional)</span></span>
                        </label>
                        <div class="compact-doc-card" id="card_content_file">
                            <input type="file"
                                   name="content_file"
                                   id="content_file"
                                   class="compact-doc-input"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp"
                                   onchange="handleDocUpload(this, 'card_content_file', 'preview_content_file', 'info_content_file', 'error_content_file')">
                            <i class="fa fa-cloud-upload compact-doc-icon"></i>
                            <p class="compact-doc-title">Click or Drag &amp; Drop Question Document</p>
                            <p class="compact-doc-desc">PDF, DOCX, DOC, JPG, PNG (Max 10MB)</p>
                            <div class="compact-doc-preview" id="preview_content_file">
                                <img src="" alt="Preview" style="display:none;" id="img_content_file">
                                <div id="doc_icon_preview" style="display:none; font-size: 24px; color: #0284c7;">
                                    <i class="fa fa-file-pdf-o"></i>
                                </div>
                                <button type="button" class="compact-doc-remove" onclick="clearDocUpload('content_file', 'card_content_file', 'preview_content_file', 'info_content_file', 'error_content_file')" title="Remove">&times;</button>
                            </div>
                            <div class="compact-doc-info" id="info_content_file"></div>
                            <div class="compact-doc-error" id="error_content_file"></div>
                        </div>
                    </div>

                    {{-- Right: Notification & Publish Preferences --}}
                    <div class="col-12 col-md-6 mb-2">
                        <label class="form-label-compact">
                            <span>Publishing &amp; Notification Settings</span>
                        </label>
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:2px; padding:8px 10px;">
                            <div class="form-group form-group-compact mb-2">
                                <label class="form-label-compact" for="notify_students">
                                    <span>Instant Student / Parent Alert</span>
                                </label>
                                <select class="form-control form-control-compact" id="notify_students" name="notify_students">
                                    <option value="1" selected>Yes - Send WhatsApp &amp; Firebase App Alerts</option>
                                    <option value="0">No - Publish Silently (Without Alerts)</option>
                                </select>
                            </div>

                            <div class="form-group form-group-compact mb-0">
                                <label class="form-label-compact">
                                    <span>Online Portal Submission</span>
                                </label>
                                <select class="form-control form-control-compact" disabled>
                                    <option value="1">Enabled - Students can submit solved assignments online</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. Bottom Action Footer Bar (Exact 1:1 with addUser) --}}
        <div class="user-footer-bar mt-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size: 11px;">
                    <i class="fa fa-info-circle mr-1"></i> Fields marked with <span class="text-danger font-weight-bold">*</span> are required.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="reset" class="btn-compact-reset" id="btn_reset_form">
                    <i class="fa fa-refresh"></i> Reset
                </button>
                <button type="submit" class="btn-compact-submit btn-submit" id="btn_submit_hw">
                    <i class="fa fa-check"></i> Submit Homework
                </button>
            </div>
        </div>

    </form>
</div>

<!-- Quill.js Editor Script (Free, Open-Source & Ultra-Fast) -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
    var quill;

    /* Compact Document Upload Handler (Matching addUser) */
    function handleDocUpload(input, cardId, previewId, infoId, errorId) {
        const file = input.files && input.files[0];
        const card = document.getElementById(cardId);
        const previewWrap = document.getElementById(previewId);
        const info = document.getElementById(infoId);
        const error = document.getElementById(errorId);
        const imgEl = document.getElementById('img_content_file');
        const iconEl = document.getElementById('doc_icon_preview');

        if (error) error.innerText = '';

        if (!file) {
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        const allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp'];
        const ext = file.name.split('.').pop().toLowerCase();

        if (allowedExts.indexOf(ext) === -1) {
            if (error) error.innerText = 'PDF, DOC, DOCX, JPG, PNG only';
            input.value = '';
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        const maxBytes = 10 * 1024 * 1024; // 10MB
        if (file.size > maxBytes) {
            if (error) error.innerText = 'Max 10MB (' + (file.size / (1024 * 1024)).toFixed(1) + 'MB)';
            input.value = '';
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        if (card) card.classList.add('has-file');
        const sizeFormatted = file.size >= (1024 * 1024) 
            ? (file.size / (1024 * 1024)).toFixed(1) + 'MB' 
            : (file.size / 1024).toFixed(0) + 'KB';

        if (info) info.innerText = file.name + ' (' + sizeFormatted + ')';

        if (['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) !== -1) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (imgEl) {
                    imgEl.src = e.target.result;
                    imgEl.style.display = 'block';
                }
                if (iconEl) iconEl.style.display = 'none';
                if (previewWrap) previewWrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            if (imgEl) imgEl.style.display = 'none';
            if (iconEl) {
                var iconClass = 'fa-file-text-o';
                if (ext === 'pdf') iconClass = 'fa-file-pdf-o text-danger';
                else if (ext === 'doc' || ext === 'docx') iconClass = 'fa-file-word-o text-primary';
                iconEl.innerHTML = '<i class="fa ' + iconClass + '"></i>';
                iconEl.style.display = 'block';
            }
            if (previewWrap) previewWrap.style.display = 'block';
        }
    }

    function clearDocUpload(inputId, cardId, previewId, infoId, errorId) {
        const input = document.getElementById(inputId);
        if (input) input.value = '';
        const card = document.getElementById(cardId);
        if (card) card.classList.remove('has-file');
        const previewWrap = document.getElementById(previewId);
        if (previewWrap) previewWrap.style.display = 'none';
        const imgEl = document.getElementById('img_content_file');
        if (imgEl) { imgEl.src = ''; imgEl.style.display = 'none'; }
        const iconEl = document.getElementById('doc_icon_preview');
        if (iconEl) iconEl.style.display = 'none';
        const info = document.getElementById(infoId);
        if (info) info.innerText = '';
        const error = document.getElementById(errorId);
        if (error) error.innerText = '';
    }

    $(document).ready(function() {
        var baseUrl = "{{ url('/') }}";

        // 1. Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%'
            });
        }

        // 2. Initialize Quill.js Rich Text Editor (Snow Theme)
        quill = new Quill('#quill-editor', {
            theme: 'snow',
            placeholder: 'Enter detailed homework instructions, problem numbers, or reference book chapters...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'clean']
                ]
            }
        });

        // Real-time sync to hidden textarea
        quill.on('text-change', function() {
            var html = quill.root.innerHTML;
            $('#compose-textarea').val(html === '<p><br></p>' ? '' : html);
        });

        // 3. Dynamic Subject Loading
        function fetchSubjects(classId, selectedSubjectId) {
            if (!classId) {
                $('#subject_id').html('<option value="">-- First Select Class --</option>');
                if ($.fn.select2) $('#subject_id').trigger('change');
                return;
            }

            $('#subjectLoader').show();

            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl + '/subjectGetData/' + classId,
                type: 'GET',
                success: function(data) {
                    $('#subject_id').html(data);
                    if (selectedSubjectId) {
                        $('#subject_id').val(selectedSubjectId);
                    }
                    if ($.fn.select2) {
                        $('#subject_id').trigger('change');
                    }
                },
                error: function() {
                    $('#subject_id').html('<option value="">Error loading subjects</option>');
                },
                complete: function() {
                    $('#subjectLoader').hide();
                }
            });
        }

        $('#class_type_id').on('change', function() {
            var classId = $(this).val();
            fetchSubjects(classId, null);
        });

        // Auto-load if class pre-selected
        var initialClassId = $('#class_type_id').val();
        if (initialClassId) {
            fetchSubjects(initialClassId, "{{ old('subject') }}");
        }

        // 4. Form Submit Sync & Validation
        $('#form-submit').on('submit', function(e) {
            var editorHtml = quill.root.innerHTML.trim();
            var editorText = quill.getText().trim();

            if (!editorText && (editorHtml === '<p><br></p>' || editorHtml === '')) {
                if (window.toastr) {
                    toastr.error('Please enter homework instructions or description.');
                } else {
                    alert('Please enter homework instructions or description.');
                }
                quill.focus();
                e.preventDefault();
                return false;
            }

            $('#compose-textarea').val(editorHtml);

            var issueDate = $('#homework_issue_date').val();
            var subDate = $('#submission_date').val();
            if (issueDate && subDate && subDate < issueDate) {
                if (window.toastr) {
                    toastr.error('Submission deadline cannot be before issue date.');
                } else {
                    alert('Submission deadline cannot be before issue date.');
                }
                $('#submission_date').focus();
                e.preventDefault();
                return false;
            }
        });

        // 5. Reset Form Handler
        $('#btn_reset_form').on('click', function() {
            setTimeout(function() {
                if (quill) quill.setText('');
                $('#compose-textarea').val('');
                clearDocUpload('content_file', 'card_content_file', 'preview_content_file', 'info_content_file', 'error_content_file');
                if ($.fn.select2) {
                    $('.select2').trigger('change');
                }
            }, 50);
        });
    });
</script>
@endsection
@php
    $classType = Helper::classType();
    $currentClassId = Session::get('class_type_id');
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $isEdit = !empty($data);

    // Parse title & category if in edit mode
    $rawTitle = old('title', $data->title ?? '');
    $extractedType = old('homework_type', 'DPP');
    $displayTitle = $rawTitle;
    if ($isEdit && empty(old('homework_type'))) {
        if (preg_match('/^\[(.*?)\]\s*(.*)$/', $rawTitle, $matches)) {
            $extractedType = trim($matches[1]);
            $displayTitle = trim($matches[2]);
        }
    }

    $selectedClassId = old('class_type_id', $data->class_type_id ?? $currentClassId);
    $selectedSubjectId = old('subject', $data->subject ?? '');
    $targetDuration = old('target_duration', $data->target_duration ?? '');
    $maxMarks = old('max_marks', $data->max_marks ?? '');
    $allowLateSubmission = old('allow_late_submission', $data->allow_late_submission ?? 1);
    $issueDate = old('homework_issue_date', $data->homework_issue_date ?? date('Y-m-d'));
    $submissionDate = old('submission_date', $data->submission_date ?? '');
    $description = old('description', $data->description ?? '');
    $existingFile = $data->content_file ?? '';
    $formAction = $isEdit ? url('homework/edit/' . $data->id) : url('homework/add');
@endphp

@extends('layout.mobile_app')

@section('styles')
<!-- Quill.js Editor Styles (Free, Open-Source & Mobile Responsive) -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE HOMEWORK ADD / EDIT STYLES
   - Aligned with Arise ERP Mobile Design System (admissionAdd, expenseAdd, homework/index)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Glassmorphic Hero Card with Live Session Pill & Action Buttons
   - Dynamic 3-Step Wizard Navigation Tracker (Academic -> Content -> Attachment)
   - Interactive Form Controls (Pills, Clean Segmented Controls, Loaders)
   - Native Drag/Tap Document Upload Card with Preview & Clear
   - Pinned Bottom Action Dock (Cancel / Reset + Save / Submit with Loading Spinner)
   - Native Slide-Up Global Confirmation Modal (showMobileConfirm)
   ========================================================================== */

/* 1. Signature Glassmorphic Hero Card */
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
.mob-mode-pill {
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
    line-height: 1.35;
}

/* Fast Action Bar - 2 Column Full-Width Grid */
.mob-actions-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    width: 100%;
}
.mob-act-btn {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-act-btn:active {
    transform: scale(0.97);
}
.mob-act-btn-view {
    background: #0284c7;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    border: 1px solid #0284c7;
}
.mob-act-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

/* 2. Interactive Form Step Progress Bar */
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
    width: 33%;
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
.mob-input-wrap .mob-input, .mob-input-wrap .mob-select {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.mob-textarea {
    height: 70px;
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

/* Quick Class Chips Scroller */
.mob-class-pills-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 6px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-class-pills-scroll::-webkit-scrollbar {
    display: none;
}
.mob-class-pill {
    padding: 4px 9px;
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

/* Quick DPP Category Pills */
.mob-cat-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 3px;
}
.mob-cat-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 3px;
    background: #f8fafc;
    color: #475569;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: all .1s ease;
}
.mob-cat-pill:active {
    transform: scale(0.96);
}
.mob-cat-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Subject Loader */
.mob-subject-loader {
    display: none;
    font-size: 9.5px;
    color: #0284c7;
    margin-top: 3px;
    font-weight: 700;
}

/* 4. Rich Text Quill Editor in Mobile View */
.mob-quill-wrapper {
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #ffffff;
    overflow: hidden;
}
.mob-quill-wrapper .ql-toolbar.ql-snow {
    border: none;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 5px 6px;
}
.mob-quill-wrapper .ql-toolbar .ql-formats {
    margin-right: 4px;
}
.mob-quill-wrapper .ql-toolbar button {
    width: 24px;
    height: 24px;
    padding: 2px;
}
.mob-quill-wrapper .ql-container.ql-snow {
    border: none;
    font-family: inherit;
    font-size: 11.5px;
    min-height: 120px;
    max-height: 240px;
}
.mob-quill-wrapper .ql-editor {
    min-height: 120px;
    padding: 8px 10px;
    line-height: 1.45;
}
.mob-quill-wrapper .ql-editor.ql-blank::before {
    font-style: normal;
    color: #94a3b8;
    font-size: 11px;
    left: 10px;
}

/* 5. Compact Document Upload Card */
.mob-doc-card {
    border: 1.5px dashed #cbd5e1;
    border-radius: 4px;
    background: #f8fafc;
    padding: 10px 8px;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all .15s ease;
}
.mob-doc-card:hover, .mob-doc-card:active {
    border-color: #0284c7;
    background: #f0f7ff;
}
.mob-doc-card.has-file {
    border-color: #10b981;
    background: #f0fdf4;
}
.mob-doc-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}
.mob-doc-icon {
    font-size: 22px;
    color: #0284c7;
    margin-bottom: 2px;
}
.mob-doc-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.mob-doc-desc {
    font-size: 9.5px;
    color: #64748b;
    margin: 2px 0 0;
}
.mob-doc-preview {
    display: none;
    position: relative;
    margin-top: 6px;
    padding: 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
}
.mob-doc-preview img {
    max-height: 90px;
    max-width: 100%;
    border-radius: 3px;
    display: block;
    margin: 0 auto;
}
.mob-doc-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #ef4444;
    color: #fff;
    border: none;
    font-size: 12px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
}
.mob-doc-info {
    font-size: 10px;
    color: #10b981;
    font-weight: 700;
    margin-top: 4px;
    word-break: break-all;
}
.mob-doc-error {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 700;
    margin-top: 3px;
}

/* Existing Attachment Banner */
.mob-existing-file-banner {
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* 6. Pinned Bottom Action Dock */
.mob-action-dock {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 8px 10px;
    box-shadow: 0 4px 16px rgba(0, 24, 51, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 990;
}
.mob-dock-btn {
    height: 36px;
    padding: 0 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-dock-btn:active {
    transform: scale(0.97);
}
.mob-dock-btn-cancel {
    width: 36%;
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
}
.mob-dock-btn-submit {
    flex: 1;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.3);
}
.mob-dock-btn-submit:disabled {
    background: #94a3b8 !important;
    cursor: not-allowed;
    transform: none !important;
}

/* Extra bottom spacer to avoid dock covering form elements */
.mob-bottom-spacer {
    height: 70px;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            @if($isEdit)
                <i class="fa fa-pencil-square-o text-info"></i> Edit Homework &bull; #{{ $data->id }}
            @else
                <i class="fa fa-plus-circle text-info"></i> Create Assignment &amp; DPP
            @endif
        </div>
        <div class="mob-hero-badges">
            <div class="mob-session-pill">
                <i class="fa fa-calendar mr-1"></i> {{ $currentSessionName }}
            </div>
            @if($isEdit)
                <div class="mob-mode-pill">
                    <i class="fa fa-tag"></i> EDIT
                </div>
            @endif
        </div>
    </div>
    <div class="mob-hero-desc">
        @if($isEdit)
            Update assignment details, timeline, study materials, and student submission policies.
        @else
            Assign daily practice problems, worksheets, and study notes with instant app notifications.
        @endif
    </div>
    <div class="mob-actions-bar">
        <a href="{{ url('homework/index?layout=mobile') }}" class="mob-act-btn mob-act-btn-view">
            <i class="fa fa-list"></i> Homework Directory
        </a>
        @if($isEdit)
            <a href="{{ url('homework/add?layout=mobile') }}" class="mob-act-btn mob-act-btn-outline">
                <i class="fa fa-plus"></i> New Assignment
            </a>
        @else
            <a href="{{ url('dashboard') }}" class="mob-act-btn mob-act-btn-outline">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
        @endif
    </div>
</div>

{{-- 2. Interactive Form Step Progress Tracker --}}
<div class="mob-tracker-container" id="mobTrackerContainer">
    <div class="mob-step-tracker" id="mobStepTracker">
        <div class="mob-step-nodes">
            <div class="mob-step-node active" data-step="1" onclick="jumpToStep(1)">
                <span class="mob-step-dot">1</span>
                <span>Academic &amp; Batch</span>
            </div>
            <div class="mob-step-node" data-step="2" onclick="jumpToStep(2)">
                <span class="mob-step-dot">2</span>
                <span>Instructions</span>
            </div>
            <div class="mob-step-node" data-step="3" onclick="jumpToStep(3)">
                <span class="mob-step-dot">3</span>
                <span>File &amp; Alerts</span>
            </div>
        </div>
        <div class="mob-progress-bar-bg">
            <div class="mob-progress-bar-fill" id="mobProgressFill" style="width: 33.33%;"></div>
        </div>
    </div>
</div>

{{-- 3. Form Container --}}
<form id="mobHomeworkForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf

    {{-- Step 1: Academic & Batch Schedule Details --}}
    <div class="mob-section-card" id="step_card_1">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">STEP 1</span> Academic &amp; Batch Details
            </div>
            <div class="mob-section-status" id="step_status_1">In Progress</div>
        </div>

        {{-- Class Quick Chips --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Target Class / Batch <span class="req">*</span></span>
                <span class="text-muted" style="font-size: 8.5px; text-transform:none;">Tap to select</span>
            </label>
            <div class="mob-class-pills-scroll">
                @if(!empty($classType))
                    @foreach($classType as $type)
                        <button type="button" 
                                class="mob-class-pill {{ ($selectedClassId == $type->id) ? 'active' : '' }}" 
                                data-class-id="{{ $type->id }}" 
                                onclick="selectClassPill('{{ $type->id }}', this)">
                            {{ $type->name ?? '' }}
                        </button>
                    @endforeach
                @endif
            </div>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-graduation-cap"></i></span>
                <select class="mob-select @error('class_type_id') is-invalid @enderror" 
                        id="class_type_id" 
                        name="class_type_id" 
                        required>
                    <option value="">-- Choose Class / Batch --</option>
                    @if(!empty($classType))
                        @foreach($classType as $type)
                            <option value="{{ $type->id ?? '' }}" {{ ($selectedClassId == $type->id) ? 'selected' : '' }}>
                                {{ $type->name ?? '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            @error('class_type_id')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Subject Select --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Subject <span class="req">*</span></span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-book"></i></span>
                <select class="mob-select @error('subject') is-invalid @enderror" 
                        id="subject_id" 
                        name="subject" 
                        required>
                    @if(!empty($subjects) && count($subjects) > 0)
                        <option value="">-- Select Subject --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id ?? '' }}" {{ ($selectedSubjectId == $sub->id) ? 'selected' : '' }}>
                                {{ $sub->name ?? '' }}
                            </option>
                        @endforeach
                    @else
                        <option value="">-- First Select Class --</option>
                    @endif
                </select>
            </div>
            <div class="mob-subject-loader" id="mobSubjectLoader">
                <i class="fa fa-spinner fa-spin mr-1"></i> Loading class subjects...
            </div>
            @error('subject')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Assignment Category / DPP Type --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Category / DPP Type <span class="req">*</span></span>
            </label>
            <div class="mob-cat-pills">
                <button type="button" class="mob-cat-pill {{ ($extractedType == 'DPP') ? 'active' : '' }}" onclick="selectCatPill('DPP', this)">DPP</button>
                <button type="button" class="mob-cat-pill {{ ($extractedType == 'Worksheet') ? 'active' : '' }}" onclick="selectCatPill('Worksheet', this)">Worksheet</button>
                <button type="button" class="mob-cat-pill {{ ($extractedType == 'PYQ Sheet') ? 'active' : '' }}" onclick="selectCatPill('PYQ Sheet', this)">PYQ Sheet</button>
                <button type="button" class="mob-cat-pill {{ ($extractedType == 'Subjective') ? 'active' : '' }}" onclick="selectCatPill('Subjective', this)">Subjective</button>
                <button type="button" class="mob-cat-pill {{ ($extractedType == 'Revision') ? 'active' : '' }}" onclick="selectCatPill('Revision', this)">Revision</button>
            </div>
            <input type="hidden" id="homework_type" name="homework_type" value="{{ $extractedType }}">
        </div>

        {{-- Issue & Submission Deadlines (Side-by-Side) --}}
        <div class="row" style="margin: 0 -4px;">
            <div class="col-6" style="padding: 0 4px;">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Issue Date <span class="req">*</span></span>
                    </label>
                    <div class="mob-input-wrap">
                        <input type="date" 
                               class="mob-input @error('homework_issue_date') is-invalid @enderror" 
                               id="homework_issue_date" 
                               name="homework_issue_date" 
                               value="{{ $issueDate }}" 
                               required>
                    </div>
                    @error('homework_issue_date')
                        <span class="mob-invalid-msg">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-6" style="padding: 0 4px;">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Deadline <span class="req">*</span></span>
                    </label>
                    <div class="mob-input-wrap">
                        <input type="date" 
                               class="mob-input @error('submission_date') is-invalid @enderror" 
                               id="submission_date" 
                               name="submission_date" 
                               value="{{ $submissionDate }}" 
                               required>
                    </div>
                    @error('submission_date')
                        <span class="mob-invalid-msg">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Est. Duration & Max Marks (Side-by-Side) --}}
        <div class="row" style="margin: 0 -4px;">
            <div class="col-6" style="padding: 0 4px;">
                <div class="mob-form-group mb-0">
                    <label class="mob-form-label">
                        <span>Est. Duration</span>
                    </label>
                    <div class="mob-input-wrap">
                        <span class="mob-input-prepend"><i class="fa fa-clock-o"></i></span>
                        <input type="text" 
                               class="mob-input" 
                               id="target_duration" 
                               name="target_duration" 
                               value="{{ $targetDuration }}" 
                               placeholder="e.g. 45 Mins">
                    </div>
                </div>
            </div>
            <div class="col-6" style="padding: 0 4px;">
                <div class="mob-form-group mb-0">
                    <label class="mob-form-label">
                        <span>Max Marks</span>
                    </label>
                    <div class="mob-input-wrap">
                        <span class="mob-input-prepend"><i class="fa fa-star-o"></i></span>
                        <input type="number" 
                               step="1" 
                               min="0" 
                               max="500" 
                               class="mob-input" 
                               id="max_marks" 
                               name="max_marks" 
                               value="{{ $maxMarks }}" 
                               placeholder="e.g. 25">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Content & Question Details --}}
    <div class="mob-section-card" id="step_card_2">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">STEP 2</span> Instructions &amp; Question Content
            </div>
            <div class="mob-section-status" id="step_status_2">Pending</div>
        </div>

        {{-- Assignment Title / Topic --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Homework Title / Topic <span class="req">*</span></span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-pencil"></i></span>
                <input type="text" 
                       class="mob-input @error('title') is-invalid @enderror" 
                       id="title" 
                       name="title" 
                       value="{{ $displayTitle }}" 
                       placeholder="e.g. Thermodynamics: First Law & Work Done" 
                       required>
            </div>
            @error('title')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>

        {{-- Rich Text Editor (Quill.js) --}}
        <div class="mob-form-group mb-0">
            <label class="mob-form-label">
                <span>Instructions / Problems <span class="req">*</span></span>
                <span class="text-muted" style="font-size: 8.5px; text-transform:none;">Rich Text</span>
            </label>
            <div class="mob-quill-wrapper">
                <div id="mob-quill-editor">
                    {!! $description !!}
                </div>
            </div>
            <textarea id="mob_description_input" name="description" style="display:none;">{{ $description }}</textarea>
            @error('description')
                <span class="mob-invalid-msg">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Step 3: Attachment & Alert Preferences --}}
    <div class="mob-section-card" id="step_card_3">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">STEP 3</span> Attachment &amp; Student Alerts
            </div>
            <div class="mob-section-status" id="step_status_3">Pending</div>
        </div>

        {{-- Existing Attachment if editing --}}
        @if(!empty($existingFile))
            <div class="mob-existing-file-banner">
                <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                    <i class="fa fa-file-text-o text-primary mr-2" style="font-size: 16px; flex-shrink: 0;"></i>
                    <div style="min-width: 0; flex: 1;">
                        <span class="d-block font-weight-bold text-truncate" style="font-size: 10.5px; color: #002C54;" title="{{ $existingFile }}">
                            {{ $existingFile }}
                        </span>
                        <span class="text-muted" style="font-size: 9px;">Current Attachment</span>
                    </div>
                </div>
                <div class="ml-2">
                    <a href="{{ asset('schoolimage/homework/' . $existingFile) }}" target="_blank" class="mob-act-btn mob-act-btn-outline" style="height: 24px; padding: 0 8px; font-size: 10px; color: #0284c7 !important; border-color: #0284c7;">
                        <i class="fa fa-external-link"></i> View
                    </a>
                </div>
            </div>
        @endif

        {{-- Document Upload Card --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Upload Question File <span class="text-muted" style="font-weight:400; text-transform:none;">(Optional)</span></span>
            </label>
            <div class="mob-doc-card" id="mob_card_content_file">
                <input type="file" 
                       name="content_file" 
                       id="mob_content_file" 
                       class="mob-doc-input" 
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" 
                       onchange="handleMobDocUpload(this)">
                <i class="fa fa-cloud-upload mob-doc-icon"></i>
                <p class="mob-doc-title">Tap to Upload Document</p>
                <p class="mob-doc-desc">PDF, DOCX, DOC, JPG, PNG (Max 10MB)</p>
                <div class="mob-doc-preview" id="mob_preview_content_file">
                    <img src="" alt="Preview" style="display:none;" id="mob_img_preview">
                    <div id="mob_doc_icon_preview" style="display:none; font-size: 26px; color: #0284c7; padding: 6px 0;">
                        <i class="fa fa-file-pdf-o"></i>
                    </div>
                    <button type="button" class="mob-doc-remove" onclick="clearMobDocUpload()" title="Remove file">&times;</button>
                </div>
                <div class="mob-doc-info" id="mob_info_content_file"></div>
                <div class="mob-doc-error" id="mob_error_content_file"></div>
            </div>
        </div>

        {{-- Notification & Late Submission Policies --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Student / Parent App Alert</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-bell-o"></i></span>
                <select class="mob-select" id="notify_students" name="notify_students">
                    @if($isEdit)
                        <option value="0" selected>No - Update Silently (Routine Edit)</option>
                        <option value="1">Yes - Re-send Push Notification &amp; WhatsApp</option>
                    @else
                        <option value="1" selected>Yes - Send Push Notification &amp; WhatsApp</option>
                        <option value="0">No - Publish Silently (Without Alerts)</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="mob-form-group mb-0">
            <label class="mob-form-label">
                <span>Late Submission Policy</span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-calendar-times-o"></i></span>
                <select class="mob-select" id="allow_late_submission" name="allow_late_submission">
                    <option value="1" {{ ($allowLateSubmission == 1) ? 'selected' : '' }}>Allowed - Tagged as Late after deadline</option>
                    <option value="0" {{ ($allowLateSubmission == 0) ? 'selected' : '' }}>Strict - Block submission after deadline</option>
                </select>
            </div>
        </div>

    </div>

    {{-- Bottom Spacer to prevent dock collision --}}
    <div class="mob-bottom-spacer"></div>

    {{-- 4. Pinned Bottom Action Dock --}}
    <div class="mob-action-dock">
        <button type="button" class="mob-dock-btn mob-dock-btn-cancel" id="btnMobCancel">
            <i class="fa fa-times"></i> Cancel
        </button>
        <button type="submit" class="mob-dock-btn mob-dock-btn-submit" id="btnMobSubmit">
            @if($isEdit)
                <i class="fa fa-save"></i> <span>Update Homework</span>
            @else
                <i class="fa fa-check"></i> <span>Publish Homework</span>
            @endif
        </button>
    </div>

</form>

<!-- Quill.js Editor Script -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<script>
var quillMob;
var baseUrl = "{{ url('/') }}";
var isEditMode = {{ $isEdit ? 'true' : 'false' }};

/* Step Navigation Helper */
function jumpToStep(step) {
    var targetEl = document.getElementById('step_card_' + step);
    if (targetEl) {
        var topOffset = targetEl.getBoundingClientRect().top + window.pageYOffset - 110;
        window.scrollTo({ top: topOffset, behavior: 'smooth' });
    }
}

function updateStepTrackerState() {
    var scrollPos = window.pageYOffset + 140;
    var card1 = document.getElementById('step_card_1');
    var card2 = document.getElementById('step_card_2');
    var card3 = document.getElementById('step_card_3');

    var currentStep = 1;
    if (card3 && scrollPos >= card3.offsetTop) {
        currentStep = 3;
    } else if (card2 && scrollPos >= card2.offsetTop) {
        currentStep = 2;
    }

    $('.mob-step-node').removeClass('active completed');
    $('.mob-step-node').each(function() {
        var step = parseInt($(this).data('step'));
        if (step < currentStep) {
            $(this).addClass('completed');
        } else if (step === currentStep) {
            $(this).addClass('active');
        }
    });

    var fillPct = currentStep === 1 ? '33.33%' : (currentStep === 2 ? '66.66%' : '100%');
    $('#mobProgressFill').css('width', fillPct);
}

/* Class Pill Selection */
function selectClassPill(classId, btnEl) {
    $('.mob-class-pill').removeClass('active');
    $(btnEl).addClass('active');
    $('#class_type_id').val(classId).trigger('change');
}

/* Category Pill Selection */
function selectCatPill(cat, btnEl) {
    $('.mob-cat-pill').removeClass('active');
    $(btnEl).addClass('active');
    $('#homework_type').val(cat);
}

/* Dynamic Subject Loader */
function fetchSubjects(classId, selectedSubId) {
    if (!classId) {
        $('#subject_id').html('<option value="">-- First Select Class --</option>');
        return;
    }

    $('#mobSubjectLoader').show();

    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: baseUrl + '/subjectGetData/' + classId,
        type: 'GET',
        success: function(data) {
            $('#subject_id').html(data);
            if (selectedSubId) {
                $('#subject_id').val(selectedSubId);
            }
        },
        error: function() {
            $('#subject_id').html('<option value="">Error loading subjects</option>');
        },
        complete: function() {
            $('#mobSubjectLoader').hide();
        }
    });
}

/* Document Upload Handler */
function handleMobDocUpload(input) {
    var file = input.files && input.files[0];
    var card = document.getElementById('mob_card_content_file');
    var previewWrap = document.getElementById('mob_preview_content_file');
    var info = document.getElementById('mob_info_content_file');
    var error = document.getElementById('mob_error_content_file');
    var imgEl = document.getElementById('mob_img_preview');
    var iconEl = document.getElementById('mob_doc_icon_preview');

    if (error) error.innerText = '';

    if (!file) {
        clearMobDocUpload();
        return;
    }

    var allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp'];
    var ext = file.name.split('.').pop().toLowerCase();

    if (allowedExts.indexOf(ext) === -1) {
        if (error) error.innerText = 'PDF, DOC, DOCX, JPG, PNG only';
        input.value = '';
        clearMobDocUpload();
        return;
    }

    var maxBytes = 10 * 1024 * 1024; // 10MB
    if (file.size > maxBytes) {
        if (error) error.innerText = 'Max 10MB (' + (file.size / (1024 * 1024)).toFixed(1) + 'MB)';
        input.value = '';
        clearMobDocUpload();
        return;
    }

    if (card) card.classList.add('has-file');
    var sizeFormatted = file.size >= (1024 * 1024) 
        ? (file.size / (1024 * 1024)).toFixed(1) + 'MB' 
        : (file.size / 1024).toFixed(0) + 'KB';

    if (info) info.innerText = file.name + ' (' + sizeFormatted + ')';

    if (['jpg', 'jpeg', 'png', 'webp'].indexOf(ext) !== -1) {
        var reader = new FileReader();
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

function clearMobDocUpload() {
    var input = document.getElementById('mob_content_file');
    if (input) input.value = '';
    var card = document.getElementById('mob_card_content_file');
    if (card) card.classList.remove('has-file');
    var previewWrap = document.getElementById('mob_preview_content_file');
    if (previewWrap) previewWrap.style.display = 'none';
    var imgEl = document.getElementById('mob_img_preview');
    if (imgEl) { imgEl.src = ''; imgEl.style.display = 'none'; }
    var iconEl = document.getElementById('mob_doc_icon_preview');
    if (iconEl) iconEl.style.display = 'none';
    var info = document.getElementById('mob_info_content_file');
    if (info) info.innerText = '';
    var error = document.getElementById('mob_error_content_file');
    if (error) error.innerText = '';
}

$(document).ready(function() {
    // 1. Initialize Compact Mobile Quill Editor
    quillMob = new Quill('#mob-quill-editor', {
        theme: 'snow',
        placeholder: 'Enter assignment details, problem numbers, formula notes...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });

    quillMob.on('text-change', function() {
        var html = quillMob.root.innerHTML;
        $('#mob_description_input').val(html === '<p><br></p>' ? '' : html);
    });

    // 2. Class change event
    $('#class_type_id').on('change', function() {
        var cId = $(this).val();
        $('.mob-class-pill').removeClass('active');
        $('.mob-class-pill[data-class-id="' + cId + '"]').addClass('active');
        fetchSubjects(cId, null);
    });

    // Auto-fetch in add mode if class already selected
    var initClass = $('#class_type_id').val();
    if (!isEditMode && initClass) {
        fetchSubjects(initClass, "{{ old('subject') }}");
    }

    // 3. Scroll Tracker
    var trackerContainer = $('#mobTrackerContainer');
    var trackerEl = $('#mobStepTracker');
    if (trackerContainer.length) {
        var trackerOffsetTop = trackerContainer.offset().top - 54;
        $(window).on('scroll', function() {
            var scrollY = $(window).scrollTop();
            if (scrollY > trackerOffsetTop) {
                trackerEl.addClass('is-fixed');
            } else {
                trackerEl.removeClass('is-fixed');
            }
            updateStepTrackerState();
        });
    }

    // 4. Cancel / Back confirmation with global slide-up modal
    $('#btnMobCancel').on('click', function(e) {
        e.preventDefault();
        showMobileConfirm(
            'Leave Homework Page',
            'Are you sure you want to discard changes and go back to Homework Directory?',
            function() {
                window.location.href = "{{ url('homework/index?layout=mobile') }}";
            },
            '<i class="fa fa-sign-out"></i> Yes, Leave',
            false,
            'fa-sign-out text-danger'
        );
    });

    // 5. Form submission handling & validation
    $('#mobHomeworkForm').on('submit', function(e) {
        var classVal = $('#class_type_id').val();
        var subjectVal = $('#subject_id').val();
        var titleVal = $('#title').val().trim();
        var issueDate = $('#homework_issue_date').val();
        var subDate = $('#submission_date').val();

        if (!classVal) {
            alert('Please select Class / Batch.');
            jumpToStep(1);
            $('#class_type_id').focus();
            e.preventDefault();
            return false;
        }

        if (!subjectVal) {
            alert('Please select Subject.');
            jumpToStep(1);
            $('#subject_id').focus();
            e.preventDefault();
            return false;
        }

        if (!issueDate) {
            alert('Please select Issue Date.');
            jumpToStep(1);
            $('#homework_issue_date').focus();
            e.preventDefault();
            return false;
        }

        if (!subDate) {
            alert('Please select Submission Deadline.');
            jumpToStep(1);
            $('#submission_date').focus();
            e.preventDefault();
            return false;
        }

        if (issueDate && subDate && subDate < issueDate) {
            alert('Submission deadline cannot be earlier than issue date.');
            jumpToStep(1);
            $('#submission_date').focus();
            e.preventDefault();
            return false;
        }

        if (!titleVal) {
            alert('Please enter Homework Title / Topic.');
            jumpToStep(2);
            $('#title').focus();
            e.preventDefault();
            return false;
        }

        var editorHtml = quillMob.root.innerHTML.trim();
        var editorText = quillMob.getText().trim();
        if (!editorText && (editorHtml === '<p><br></p>' || editorHtml === '')) {
            alert('Please enter instructions or problem descriptions.');
            jumpToStep(2);
            quillMob.focus();
            e.preventDefault();
            return false;
        }

        $('#mob_description_input').val(editorHtml);

        // Disable button & show spinner
        $('#btnMobSubmit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    });
});
</script>
@endsection

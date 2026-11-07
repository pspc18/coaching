@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = Helper::classType();
    $search = $search ?? [];
    $requestedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $selectedSubjectIds = collect($selectedSubjectIds ?? old('subject_name_id', $search['subject_name_id'] ?? []))
        ->map(function ($id) {
            return (int) $id;
        })
        ->filter()
        ->values()
        ->all();
    $showMarksSection = (bool) ($showMarksSection ?? false);
    $subjects = $subjects ?? collect();
    $Allsubjects = $Allsubjects ?? collect();
    $data2 = $data2 ?? collect();
    $examlist = $examlist ?? collect();
    $selectedClassName = $classType->firstWhere('id', $requestedClassId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $selectedExamId)->exam_name ?? 'Exam Not Selected';
    $roleId = Session::get('role_id');
    $isPublished = $isPublished ?? false;
    $fillMinMaxMarksMap = $fillMinMaxMarksMap ?? collect();
    $existingMarksMap = $existingMarksMap ?? collect();
    $studentSubjectAssignments = $studentSubjectAssignments ?? [];
    $classOrderBy = $classOrderBy ?? 0;
    $currentSessionName = Session::get('session_name') ?? 'Current Session';

    $currentStep = $showMarksSection ? 2 : 1;
    $progressPercent = $showMarksSection ? 100 : 50;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FILL MARKS (MANUAL ENTRY)
   - Canonical Arise ERP Native Mobile Form Design System
   - Glassmorphic Hero Card with Live Session & Status Badges
   - Native Step Progress Tracker (Criteria Selection -> Student Scoring)
   - Touch-Friendly Quick Class & Subject Selector Pills
   - Symmetrical Student Evaluation Cards with Initial Avatars
   - Real-time Client-Side Fail/Pass Validation Feedback
   - Pinned Action Dock for Submission
   ========================================================================== */

.mob-form-page-wrap {
    margin: 0;
    padding: 0 0 calc(var(--bottom-nav-height, 52px) + 24px) 0;
    box-sizing: border-box;
}

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
    margin-bottom: 5px;
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
    line-height: 1.3;
}
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    height: 30px;
    padding: 0 10px;
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
    flex: 1;
}
.mob-act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-light:active {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(0.98);
}
.mob-act-btn-primary {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}

/* 2. Interactive Form Step Progress Tracker */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 8px;
}
.mob-step-tracker {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 7px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
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
    user-select: none;
    white-space: nowrap;
}
.mob-step-node.active {
    color: #002C54;
    font-weight: 800;
}
.mob-step-node.completed {
    color: #16a34a;
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
.mob-progress-bar-bg {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.mob-progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    border-radius: 2px;
    transition: width .25s ease;
}

/* 3. Mobile Form Section Cards */
.mob-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
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
    font-size: 11.5px;
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

/* Quick Class Horizontal Scroll Pills */
.mob-class-pills-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 5px;
    margin-bottom: 8px;
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

/* Form Groups & Inputs */
.mob-form-group {
    margin-bottom: 8px;
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
.mob-select, .mob-input {
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
.mob-input-wrap .mob-select, .mob-input-wrap .mob-input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.mob-select:focus, .mob-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.1);
}

/* Subject Filter Chips Box */
.mob-subjects-pill-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 3px;
    margin-bottom: 6px;
    max-height: 120px;
    overflow-y: auto;
    padding: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
}
.mob-sub-chip {
    padding: 3px 7px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-sub-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #001833;
}

/* 4. Exam KPI / Context Banner */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.mob-kpi-chip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    text-align: center;
}
.mob-kpi-chip .kpi-num {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.1;
}
.mob-kpi-chip .kpi-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}

/* 5. Passing Marks Configuration Box */
.mob-minmax-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
}
.mob-minmax-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-weight: 800;
    color: #002C54;
    cursor: pointer;
    user-select: none;
}
.mob-minmax-body {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #e2e8f0;
}
.mob-minmax-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 10.5px;
}

/* 6. Quick Codes Legend Bar */
.mob-legend {
    font-size: 9.5px;
    color: #475569;
    background: #f8fafc;
    padding: 6px 8px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}
.mob-legend-badge {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 1px 4px;
    border-radius: 2px;
    font-size: 9px;
    font-weight: 800;
    color: #002C54;
}

/* 7. Student Marks Entry Cards */
.mob-student-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-student-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 7px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-student-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-student-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 800;
    flex-shrink: 0;
}
.mob-student-name {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.mob-student-sub {
    font-size: 9.5px;
    color: #64748b;
}
.mob-student-adm {
    font-size: 10px;
    font-weight: 700;
    color: #002C54;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
}

/* Subject Scoring Grid Inside Student Card */
.mob-sub-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
}
.mob-sub-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sub-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
}
.mob-sub-input-wrap label {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 2px;
    display: block;
    text-align: center;
}
.mob-sub-input {
    width: 100%;
    height: 28px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    text-align: center;
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    font-family: inherit;
    transition: all .12s ease;
}
.mob-sub-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-sub-input.bg-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #f87171 !important;
}
.mob-sub-input.bg-warning {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}

/* 8. Bottom Action Docks */
.mob-submit-dock {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-top: 4px;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 6px;
}
/* Pinned Floating Bottom Action Dock strictly above footer */
.mob-fixed-scoring-dock {
    position: fixed !important;
    left: 8px !important;
    right: 8px !important;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px) !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 4px !important;
    padding: 8px 10px !important;
    margin: 0 !important;
    box-shadow: 0 4px 18px rgba(0, 20, 40, 0.22) !important;
    display: flex !important;
    gap: 8px !important;
    z-index: 998 !important;
}
.mob-form-page-wrap.has-fixed-dock {
    padding-bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 72px) !important;
}
.mob-btn-reset {
    height: 36px;
    padding: 0 12px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #334155;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    text-decoration: none !important;
    flex: 1;
    transition: all .12s ease;
}
.mob-btn-reset:active {
    background: #e2e8f0;
    transform: scale(0.98);
}
.mob-btn-submit {
    flex: 1.8;
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
.mob-btn-submit.btn-save {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
}

/* Flash Alerts */
.mob-alert {
    padding: 7px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-alert-danger {
    background: #fee2e2;
    border: 1px solid #f87171;
    color: #991b1b;
}
.mob-alert-success {
    background: #dcfce7;
    border: 1px solid #4ade80;
    color: #166534;
}
.mob-alert-warning {
    background: #fef9c3;
    border: 1px solid #fde047;
    color: #854d0e;
}
</style>
@endsection

@section('content')
<div class="mob-form-page-wrap {{ ($showMarksSection && $data2->isNotEmpty()) ? 'has-fixed-dock' : '' }}">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-pencil-square-o text-warning"></i> Manual Marks Entry
            </div>
            <div class="mob-hero-badges">
                <span class="mob-session-pill">
                    <i class="fa fa-calendar-check-o mr-1"></i> {{ $currentSessionName }}
                </span>
                <span class="mob-token-pill">
                    @if(!$showMarksSection) Step 1/2: Criteria @else Step 2/2: Scoring @endif
                </span>
            </div>
        </div>
        <div class="mob-hero-desc">
            @if(!$showMarksSection)
                Select class, examination, and subjects to evaluate and record marks.
            @else
                Scoring student evaluations for <b>{{ $selectedClassName }}</b> &bull; <b>{{ $selectedExamName }}</b>.
            @endif
        </div>
        <div class="mob-actions-bar">
            <a href="{{ url('fill-marks-by-excel?layout=mobile' . ($requestedClassId && $selectedExamId ? '&class_type_id='.$requestedClassId.'&exam_id='.$selectedExamId : '')) }}" class="mob-act-btn mob-act-btn-light">
                <i class="fa fa-file-excel-o text-success"></i> Bulk Excel Import
            </a>
            <a href="{{ url('examination_dashboard') }}" class="mob-act-btn mob-act-btn-light">
                <i class="fa fa-dashboard"></i> Exam Dashboard
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('error'))
        <div class="mob-alert mob-alert-danger">
            <i class="fa fa-exclamation-circle text-danger"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('success') || session('message'))
        <div class="mob-alert mob-alert-success">
            <i class="fa fa-check-circle text-success"></i>
            <span>{{ session('success') ?? session('message') }}</span>
        </div>
    @endif

    {{-- 2. Interactive Step Progress Tracker --}}
    <div class="mob-tracker-container">
        <div class="mob-step-tracker">
            <div class="mob-step-nodes">
                <div class="mob-step-node {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}">
                    <span class="mob-step-dot">
                        @if($currentStep > 1) <i class="fa fa-check"></i> @else 1 @endif
                    </span>
                    <span>1. Exam &amp; Subjects</span>
                </div>
                <div class="mob-step-node {{ $currentStep >= 2 ? 'active' : '' }}">
                    <span class="mob-step-dot">2</span>
                    <span>2. Student Scoring</span>
                </div>
            </div>
            <div class="mob-progress-bar-bg">
                <div class="mob-progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
    </div>

    {{-- =========================================================================
         STEP 1: TARGET CRITERIA SELECTION FORM
         ========================================================================= --}}
    <form method="post" action="{{ url('fill_marks') }}" id="mobSelectionForm">
        @csrf
        <input type="hidden" name="layout" value="mobile">

        <div class="mob-section-card">
            <div class="mob-section-header">
                <div class="mob-section-title">
                    <span class="mob-step-badge">01</span> Class &amp; Examination Criteria
                </div>
                <span class="mob-section-status">
                    {{ $requestedClassId && $selectedExamId ? 'Criteria Ready' : 'Required' }}
                </span>
            </div>

            {{-- Fast Quick Class Selector Horizontal Pills --}}
            <label class="mob-form-label">
                <span>Quick Select Class</span>
            </label>
            <div class="mob-class-pills-scroll">
                @foreach($classType as $class)
                    <button type="button" 
                            class="mob-class-pill {{ $requestedClassId === (int)$class->id ? 'active' : '' }}" 
                            data-id="{{ $class->id }}">
                        {{ $class->name }}
                    </button>
                @endforeach
            </div>

            {{-- Class Dropdown --}}
            <div class="mob-form-group">
                <label class="mob-form-label">
                    <span>Class / Course <span class="req">*</span></span>
                </label>
                <div class="mob-input-wrap">
                    <span class="mob-input-prepend"><i class="fa fa-graduation-cap"></i></span>
                    <select name="class_name" id="mob_class_type_id" class="mob-select" required>
                        <option value="">-- Select Class / Course --</option>
                        @foreach($classType as $class)
                            <option value="{{ $class->id }}" {{ $requestedClassId === (int)$class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Exam Dropdown --}}
            <div class="mob-form-group">
                <label class="mob-form-label">
                    <span>Examination <span class="req">*</span></span>
                </label>
                <div class="mob-input-wrap">
                    <span class="mob-input-prepend"><i class="fa fa-file-text-o"></i></span>
                    <select name="exam_id" id="mob_exam_id" class="mob-select" required>
                        <option value="">-- Select Exam --</option>
                        @foreach($examlist as $exam)
                            <option value="{{ $exam->exam_id }}" {{ $selectedExamId === (int)$exam->exam_id ? 'selected' : '' }}>
                                {{ $exam->exam_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Subject Multi-Select with Quick Tap Chips --}}
            <div class="mob-form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mob-form-label mb-0">
                        <span>Select Subject(s) <span class="req">*</span></span>
                    </label>
                    <input type="hidden" name="show_all_subjects" id="mob_show_all_subjects" value="0">
                    <button type="button" id="mobBtnSelectAllChips" style="background:none; border:none; color:#0284c7; font-size:9.5px; font-weight:800; cursor:pointer; padding:0;">
                        <i class="fa fa-check-square-o"></i> Select All
                    </button>
                </div>

                {{-- Interactive Subject Chips --}}
                <div class="mob-subjects-pill-wrap" id="mobSubjectChipsContainer">
                    @forelse($Allsubjects as $sub)
                        @php
                            $isSelected = empty($selectedSubjectIds) || in_array((int)$sub->id, $selectedSubjectIds, true);
                        @endphp
                        <span class="mob-sub-chip {{ $isSelected ? 'active' : '' }}" data-id="{{ $sub->id }}">
                            <i class="fa {{ $isSelected ? 'fa-check' : 'fa-plus' }}"></i> {{ $sub->name }}
                        </span>
                    @empty
                        <span style="font-size: 10px; color: #94a3b8; padding: 2px 4px;">Select class to view subjects</span>
                    @endforelse
                </div>

                {{-- Synced Native Hidden Multiple Select --}}
                <select name="subject_name_id[]" id="mob_subject_id" multiple="multiple" style="display: none;">
                    @foreach($Allsubjects as $sub)
                        <option value="{{ $sub->id }}" {{ (empty($selectedSubjectIds) || in_array((int)$sub->id, $selectedSubjectIds, true)) ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Load Students Action Button --}}
            <div class="mob-submit-dock" style="margin-bottom: 0; box-shadow: none; padding: 0; border: none;">
                <button type="button" class="mob-btn-reset" id="mobBtnResetCriteria">
                    <i class="fa fa-refresh"></i> Reset
                </button>
                <button type="submit" class="mob-btn-submit" id="btnLoadStudents">
                    <i class="fa fa-search"></i> Load Students for Scoring
                </button>
            </div>
        </div>
    </form>

    {{-- =========================================================================
         STEP 2: STUDENTS SCORING & MARKS ENTRY
         ========================================================================= --}}
    @if($showMarksSection)
        @if($subjects->isEmpty())
            <div class="mob-alert mob-alert-warning">
                <i class="fa fa-exclamation-triangle"></i>
                <span>No subjects found for this class/exam selection. Please configure subjects in Exam Setup.</span>
            </div>
        @else
            <form action="{{ url('fill_marks_submit') }}" method="post" id="mobFillMarksForm">
                @csrf
                <input type="hidden" name="class_type_id" value="{{ $requestedClassId }}">
                <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">

                {{-- Exam Context Overview Chips --}}
                <div class="mob-kpi-grid">
                    <div class="mob-kpi-chip">
                        <div class="kpi-num">{{ $data2->count() }}</div>
                        <div class="kpi-lbl">Students</div>
                    </div>
                    <div class="mob-kpi-chip">
                        <div class="kpi-num">{{ $subjects->count() }}</div>
                        <div class="kpi-lbl">Subjects</div>
                    </div>
                    <div class="mob-kpi-chip">
                        <div class="kpi-num" style="color: {{ $isPublished ? '#ef4444' : '#16a34a' }};">
                            <i class="fa {{ $isPublished ? 'fa-lock' : 'fa-pencil' }}"></i>
                        </div>
                        <div class="kpi-lbl">{{ $isPublished ? 'Published' : 'Editable' }}</div>
                    </div>
                </div>

                {{-- Collapsible Subject Passing Criteria (Max & Min Marks) --}}
                <div class="mob-minmax-card">
                    <div class="mob-minmax-head" id="toggleMinMaxHead">
                        <span><i class="fa fa-sliders text-warning mr-1"></i> Subject Passing Marks Configuration</span>
                        <span style="font-size: 10px; color: #0284c7;"><i class="fa fa-chevron-down" id="minMaxChevron"></i></span>
                    </div>

                    <div class="mob-minmax-body" id="minMaxBody" style="display: none;">
                        <div style="font-size: 9.5px; color: #64748b; margin-bottom: 6px;">
                            Configure maximum scoring ceiling and minimum passing marks per subject.
                        </div>

                        @foreach($subjects as $item)
                            @php
                                $old_value = $fillMinMaxMarksMap->get($item->id);
                                $maxMarks = $old_value->exam_maximum_marks ?? 100;
                                $minMarks = $old_value->exam_minimum_marks ?? 30;
                            @endphp
                            <div class="mob-minmax-row">
                                <div style="font-weight: 700; color: #002C54; flex: 1.2;">
                                    {{ $item->sub_name ?: $item->name }}
                                </div>
                                <input type="hidden" name="fill_min_max_marks_id[]" class="max_min" data-max_min="{{ $maxMarks }}" value="{{ $old_value->id ?? '' }}"/>
                                <input type="hidden" name="subject_id[]" value="{{ $item->id }}" />
                                <div style="display: flex; gap: 4px; flex: 1.5;">
                                    <div style="flex: 1;">
                                        <label style="font-size: 8px; color: #64748b; display: block; text-align: center; margin: 0;">Max</label>
                                        <input type="number" name="exam_maximum_marks[]" value="{{ $maxMarks }}" min="0" step="0.01" required
                                               class="mob-sub-input mob_max_input" id="mob_subject_{{ $item->id }}" placeholder="Max" />
                                    </div>
                                    <div style="flex: 1;">
                                        <label style="font-size: 8px; color: #64748b; display: block; text-align: center; margin: 0;">Min Pass</label>
                                        <input type="number" name="exam_minimum_marks[]" value="{{ $minMarks }}" min="0" step="0.01" required
                                               class="mob-sub-input mob_min_input" id="mob_minimum_marks_{{ $item->id }}" placeholder="Min" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Status Codes Quick Legend --}}
                <div class="mob-legend">
                    <strong>Codes:</strong>
                    <span class="mob-legend-badge">AB</span> Absent
                    <span class="mob-legend-badge">M</span> Medical
                    <span class="mob-legend-badge">JL</span> Late
                    <span class="mob-legend-badge">F</span> Fail
                </div>

                {{-- Student Evaluations Feed --}}
                <div class="mob-section-card" style="padding: 8px;">
                    <div class="mob-section-header" style="margin-bottom: 7px;">
                        <div class="mob-section-title">
                            <span class="mob-step-badge"><i class="fa fa-users"></i></span> Student Score Cards
                        </div>
                        <span class="badge badge-primary" style="font-size: 9.5px;">{{ $data2->count() }} Students</span>
                    </div>

                    @forelse($data2 as $key => $student)
                        <div class="mob-student-card">
                            <div class="mob-student-header">
                                <div class="mob-student-avatar-wrap">
                                    <div class="mob-student-avatar">
                                        {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="mob-student-name">
                                            {{ $key + 1 }}. {{ $student->first_name }} {{ $student->last_name }}
                                        </div>
                                        <div class="mob-student-sub">
                                            Father: {{ $student->father_name ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="mob-student-adm">
                                    <i class="fa fa-hashtag"></i> {{ $student->admissionNo }}
                                </span>
                            </div>
                            <input type="hidden" name="admission_id[]" value="{{ $student->id }}">

                            {{-- Subject-wise marks grid for this student --}}
                            @foreach($subjects as $item1)
                                @php
                                    $mapKey = $student->id . '_' . $item1->id;
                                    $old_marks = $existingMarksMap->get($mapKey);
                                    $minMaxConfig = $fillMinMaxMarksMap->get($item1->id);
                                    $minPassingMarks = $minMaxConfig->exam_minimum_marks ?? 30;

                                    $isEditable = ($classOrderBy <= 10) || !empty($studentSubjectAssignments[$student->id][$item1->id]);
                                    $notAssignedClass = $isEditable ? '' : 'bg-warning';

                                    $oldMarks = $old_marks->student_marks ?? '';
                                    $oldRMarks = $old_marks->r_marks ?? '';
                                    $oldWMarks = $old_marks->w_marks ?? '';
                                    $oldLMarks = $old_marks->l_marks ?? '';

                                    $displayMarks = $isEditable ? $oldMarks : '';
                                    $displayRMarks = $isEditable ? $oldRMarks : '';
                                    $displayWMarks = $isEditable ? $oldWMarks : '';
                                    $displayLMarks = $isEditable ? $oldLMarks : '';

                                    $isFailClass = '';
                                    if ($isEditable && is_numeric($oldMarks) && (float)$oldMarks < (float)$minPassingMarks) {
                                        $isFailClass = 'bg-danger';
                                    }
                                @endphp

                                <input type="hidden" name="fill_marks_id[]" value="{{ $old_marks->id ?? '' }}"/>

                                <div class="mob-sub-block">
                                    <div class="mob-sub-name">
                                        <span><i class="fa fa-book text-primary mr-1"></i> {{ $item1->sub_name ?: $item1->name }}</span>
                                        @if(!$isEditable)
                                            <span class="badge badge-warning" style="font-size: 8px;">Not Assigned</span>
                                        @endif
                                    </div>
                                    <div class="mob-sub-grid">
                                        <div class="mob-sub-input-wrap">
                                            <label>Right (R)</label>
                                            <input type="text" name="r_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="R" value="{{ $displayRMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label>Wrong (W)</label>
                                            <input type="text" name="w_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="W" value="{{ $displayWMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label>Left (L)</label>
                                            <input type="text" name="l_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="L" value="{{ $displayLMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label style="color:#002C54; font-weight:800;">Marks</label>
                                            <input type="text" name="student_marks[]" 
                                                   class="mob-sub-input mob_student_marks {{ $isFailClass }} {{ $notAssignedClass }}"
                                                   data-subject_id="{{ $item1->id }}" 
                                                   data-old_marks="{{ $oldMarks }}" 
                                                   placeholder="Score" 
                                                   value="{{ $displayMarks }}" 
                                                   oninput="this.value = this.value.toUpperCase()" 
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                    </div>
                                    <input type="hidden" name="check_null[]" value="{{ $oldMarks }}"/>
                                    <input type="hidden" name="subject_id_fill[]" value="{{ $item1->id }}"/>
                                    <input type="hidden" name="other_subject[]" value="{{ $item1->other_subject ?? '' }}"/>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size: 11px;">
                            <i class="fa fa-users text-muted mb-2" style="font-size: 28px; display: block;"></i>
                            No students registered for this class.
                        </div>
                    @endforelse
                </div>

                {{-- Bottom Fixed Submit Dock (Strictly pinned above mobile bottom footer) --}}
                @if($data2->isNotEmpty())
                    <div class="mob-submit-dock mob-fixed-scoring-dock">
                        <a href="{{ url('fill_marks?layout=mobile') }}" class="mob-btn-reset">
                            <i class="fa fa-refresh"></i> Reset Criteria
                        </a>
                        @if(!$isPublished || (int)$roleId === 1)
                            <button type="submit" class="mob-btn-submit btn-save" id="btnSaveMarksSubmit">
                                <i class="fa fa-check-circle"></i> Save Marks
                            </button>
                        @else
                            <button type="button" class="mob-btn-submit" style="background: #94a3b8; cursor: not-allowed;" disabled>
                                <i class="fa fa-lock"></i> Result Published
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        @endif
    @endif

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. Quick Class Horizontal Pills Sync
    $('.mob-class-pill').on('click', function() {
        $('.mob-class-pill').removeClass('active');
        $(this).addClass('active');
        var classId = $(this).data('id');
        $('#mob_class_type_id').val(classId).trigger('change');
    });

    // 2. Dynamic Exam & Subject Loader on Class Change
    $('#mob_class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#mob_exam_id');
        var $chipsBox = $('#mobSubjectChipsContainer');
        var $nativeSelect = $('#mob_subject_id');

        // Sync horizontal pill
        $('.mob-class-pill').removeClass('active');
        if (classId) {
            $('.mob-class-pill[data-id="' + classId + '"]').addClass('active');
        }

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            $chipsBox.html('<span style="font-size: 10px; color: #94a3b8; padding: 2px 4px;">Select class to view subjects</span>');
            $nativeSelect.html('');
            return;
        }

        $examSelect.html('<option value="">Loading exams...</option>');

        // Fetch Examinations
        $.ajax({
            url: "{{ url('examData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $examSelect.html(res);
            },
            error: function() {
                // Fallback to json endpoint
                $.ajax({
                    url: "{{ url('marks-import/exams-by-class') }}/" + classId,
                    type: "GET",
                    dataType: "json",
                    success: function(jsonRes) {
                        if (jsonRes.success && jsonRes.exams && jsonRes.exams.length > 0) {
                            var opts = '<option value="">-- Select Exam --</option>';
                            jsonRes.exams.forEach(function(item) {
                                opts += '<option value="' + item.exam_id + '">' + item.exam_name + '</option>';
                            });
                            $examSelect.html(opts);
                        } else {
                            $examSelect.html('<option value="">No exams assigned to this class</option>');
                        }
                    },
                    error: function() {
                        $examSelect.html('<option value="">-- Select Exam --</option>');
                    }
                });
            }
        });

        // Fetch Subjects
        $.ajax({
            url: "{{ url('subjectGetData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $nativeSelect.html(res);
                renderSubjectChipsFromSelect();
            }
        });
    });

    // 3. Render Subject Chips from Native Select
    function renderSubjectChipsFromSelect() {
        var $chipsBox = $('#mobSubjectChipsContainer');
        var $nativeSelect = $('#mob_subject_id');
        var html = '';

        $nativeSelect.find('option').each(function() {
            var id = $(this).val();
            var name = $(this).text().trim();
            if (id) {
                html += '<span class="mob-sub-chip active" data-id="' + id + '">' +
                        '<i class="fa fa-check"></i> ' + name +
                        '</span>';
                $(this).prop('selected', true);
            }
        });

        if (!html) {
            html = '<span style="font-size: 10px; color: #94a3b8; padding: 2px 4px;">No subjects found for this class</span>';
        }
        $chipsBox.html(html);
    }

    // 4. Subject Chip Click Toggle
    $(document).on('click', '.mob-sub-chip', function() {
        var subId = $(this).data('id');
        var $opt = $('#mob_subject_id option[value="' + subId + '"]');

        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).find('i').removeClass('fa-check').addClass('fa-plus');
            $opt.prop('selected', false);
        } else {
            $(this).addClass('active');
            $(this).find('i').removeClass('fa-plus').addClass('fa-check');
            $opt.prop('selected', true);
        }
    });

    // 5. Select All Subjects Toggle Button
    $('#mobBtnSelectAllChips').on('click', function() {
        var anyUnselected = $('.mob-sub-chip:not(.active)').length > 0;
        if (anyUnselected) {
            $('.mob-sub-chip').addClass('active');
            $('.mob-sub-chip i').removeClass('fa-plus').addClass('fa-check');
            $('#mob_subject_id option').prop('selected', true);
            $('#mob_show_all_subjects').val('1');
        } else {
            $('.mob-sub-chip').removeClass('active');
            $('.mob-sub-chip i').removeClass('fa-check').addClass('fa-plus');
            $('#mob_subject_id option').prop('selected', false);
            $('#mob_show_all_subjects').val('0');
        }
    });

    // 6. Reset Criteria Button
    $('#mobBtnResetCriteria').on('click', function() {
        window.location.href = "{{ url('fill_marks?layout=mobile') }}";
    });

    // 7. Toggle Passing Marks Configuration Accordion
    $('#toggleMinMaxHead').on('click', function() {
        var $body = $('#minMaxBody');
        var $chevron = $('#minMaxChevron');
        if ($body.is(':visible')) {
            $body.slideUp(150);
            $chevron.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $body.slideDown(150);
            $chevron.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    // 8. Real-time Client-Side Marks Input Validation
    $('.mob_student_marks').on('input', function() {
        $(this).removeClass('bg-danger');
        var subject_id = $(this).data('subject_id');
        var maximum_marks = parseFloat($('#mob_subject_' + subject_id).val()) || 100;
        var minimum_marks = parseFloat($('#mob_minimum_marks_' + subject_id).val()) || 0;
        var val = $(this).val().trim().toUpperCase();

        if (val === '') return;

        var allowedCodes = ['AB', 'M', 'JL', 'T', 'F', 'A', 'B', 'C', 'D'];
        if (allowedCodes.indexOf(val) !== -1) {
            return;
        }

        var num = parseFloat(val);
        if (isNaN(num)) {
            $(this).val('');
            return;
        }

        if (num > maximum_marks) {
            $(this).val(maximum_marks);
            num = maximum_marks;
        }

        if (num < minimum_marks) {
            $(this).addClass('bg-danger');
        }
    });

    // 9. Submit Loading Feedback
    $('#mobFillMarksForm').on('submit', function() {
        var $btn = $('#btnSaveMarksSubmit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving Marks...');
    });
});
</script>
@endsection

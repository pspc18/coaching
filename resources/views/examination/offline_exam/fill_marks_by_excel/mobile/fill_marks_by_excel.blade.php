@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $examlist = $examlist ?? collect();
    $subjects = $subjects ?? collect();
    $classTypeId = (int) ($search['class_type_id'] ?? 0);
    $examId = (int) ($search['exam_id'] ?? 0);
    $studentsCount = $studentsCount ?? 0;
    $selectedClassName = $classType->firstWhere('id', $classTypeId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $examId)->exam_name ?? 'Exam Not Selected';
    $mappingMode = !empty($importToken) && !empty($mappingHeaders);
    $currentSessionName = Session::get('session_name') ?? 'Current Session';

    // Step calculation
    $currentStep = 1;
    if ($classTypeId > 0 && $examId > 0) {
        $currentStep = 2;
    }
    if ($mappingMode) {
        $currentStep = 3;
    }
    $progressPercent = ($currentStep === 1) ? 33 : (($currentStep === 2) ? 66 : 100);
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FILL MARKS BY EXCEL
   - Canonical Arise ERP Native Mobile Form Design System
   - Glassmorphic Hero Card with Session & Status Badges
   - Native Step Progress Tracker (Select -> Upload -> Map & Import)
   - Touch-Friendly Class Quick-Pills & Input Prepends
   - One-Tap Prefilled Excel Roll Template Downloader
   - Modern Native File Dropzone & Mapping Grid
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

/* 2. Interactive Step Progress Tracker */
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

/* 5. Pre-filled Template Downloader Box */
.mob-template-card {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1px solid #86efac;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
}
.mob-template-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
}
.mob-template-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #166534;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-template-desc {
    font-size: 10px;
    color: #15803d;
    margin-bottom: 8px;
    line-height: 1.3;
}
.mob-btn-download {
    height: 32px;
    background: #16a34a;
    color: #ffffff !important;
    border: none;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none !important;
    width: 100%;
    box-shadow: 0 2px 5px rgba(22, 163, 74, 0.25);
    transition: all .12s ease;
}
.mob-btn-download:active {
    transform: scale(0.98);
}

/* 6. Upload Dropzone */
.mob-dropzone-box {
    border: 2px dashed #94a3b8;
    border-radius: 4px;
    padding: 16px 12px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all .15s ease;
    margin-bottom: 8px;
}
.mob-dropzone-box:active, .mob-dropzone-box.dragover {
    border-color: #0284c7;
    background: #f0f9ff;
}
.mob-dropzone-icon {
    font-size: 30px;
    color: #0284c7;
    margin-bottom: 5px;
}
.mob-dropzone-text {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 3px;
}
.mob-dropzone-sub {
    font-size: 9.5px;
    color: #64748b;
}
.mob-file-badge {
    display: none;
    background: #ecfdf5;
    border: 1px solid #10b981;
    color: #065f46;
    padding: 6px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 6px;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* 7. Guidelines Item */
.mob-guide-row {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 10.5px;
    color: #475569;
    margin-bottom: 6px;
    line-height: 1.3;
}
.mob-guide-row i {
    color: #0284c7;
    margin-top: 2px;
    font-size: 11px;
}

/* 8. Step 2 Mapping Subject Cards */
.mob-mapping-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
}
.mob-mapping-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 7px;
    padding-bottom: 4px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-mapping-card-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
@media (max-width: 340px) {
    .mob-mapping-card-grid {
        grid-template-columns: 1fr;
    }
}

/* 9. Pinned Bottom Action Dock */
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
.mob-btn-reset {
    height: 34px;
    padding: 0 12px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    text-decoration: none !important;
    flex: 1;
}
.mob-btn-reset:active {
    background: #e2e8f0;
}
.mob-btn-submit {
    flex: 2;
    height: 34px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12px;
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
</style>
@endsection

@section('content')
<div class="mob-form-page-wrap">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-file-excel-o text-success"></i> Excel Marks Import
            </div>
            <div class="mob-hero-badges">
                <span class="mob-session-pill">
                    <i class="fa fa-calendar-check-o mr-1"></i> {{ $currentSessionName }}
                </span>
                <span class="mob-token-pill">
                    @if(!$mappingMode)
                        Step {{ $currentStep }}/3
                    @else
                        Step 3/3: Mapping
                    @endif
                </span>
            </div>
        </div>
        <div class="mob-hero-desc">
            @if(!$mappingMode)
                Select class and exam, download student roll template or upload spreadsheet to import marks.
            @else
                Verify and map Excel columns to subject marks for <b>{{ $selectedClassName }}</b> &bull; <b>{{ $selectedExamName }}</b>.
            @endif
        </div>
        <div class="mob-actions-bar">
            <a href="{{ url('examination_dashboard') }}" class="mob-act-btn mob-act-btn-light">
                <i class="fa fa-dashboard"></i> Exam Dashboard
            </a>
            <a href="{{ url('fill_marks?layout=mobile' . ($classTypeId && $examId ? '&class_type_id='.$classTypeId.'&exam_id='.$examId : '')) }}" class="mob-act-btn mob-act-btn-light">
                <i class="fa fa-pencil-square-o"></i> Manual Marks Entry
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

    {{-- 2. Interactive Form Step Progress Tracker --}}
    <div class="mob-tracker-container">
        <div class="mob-step-tracker">
            <div class="mob-step-nodes">
                <div class="mob-step-node {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}">
                    <span class="mob-step-dot">
                        @if($currentStep > 1) <i class="fa fa-check"></i> @else 1 @endif
                    </span>
                    <span>1. Exam Select</span>
                </div>
                <div class="mob-step-node {{ $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : '' }}">
                    <span class="mob-step-dot">
                        @if($currentStep > 2) <i class="fa fa-check"></i> @else 2 @endif
                    </span>
                    <span>2. Upload File</span>
                </div>
                <div class="mob-step-node {{ $currentStep >= 3 ? 'active' : '' }}">
                    <span class="mob-step-dot">3</span>
                    <span>3. Map &amp; Import</span>
                </div>
            </div>
            <div class="mob-progress-bar-bg">
                <div class="mob-progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
            </div>
        </div>
    </div>

    @if(!$mappingMode)
        {{-- =========================================================================
             STEP 1 & 2: SELECTION & EXCEL FILE UPLOAD
             ========================================================================= --}}
        <form method="get" action="{{ url('fill-marks-by-excel') }}" id="mobSelectionForm">
            <input type="hidden" name="layout" value="mobile">

            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <span class="mob-step-badge">01</span> Target Class &amp; Examination
                    </div>
                    <span class="mob-section-status">
                        {{ $classTypeId && $examId ? 'Exam Selected' : 'Required Fields' }}
                    </span>
                </div>

                {{-- Fast Quick Class Selector Horizontal Pills --}}
                <label class="mob-form-label">
                    <span>Quick Select Class</span>
                </label>
                <div class="mob-class-pills-scroll">
                    @foreach($classType as $class)
                        <button type="button" 
                                class="mob-class-pill {{ $classTypeId === (int)$class->id ? 'active' : '' }}" 
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
                        <select name="class_type_id" id="mob_class_type_id" class="mob-select" required>
                            <option value="">-- Select Class / Course --</option>
                            @foreach($classType as $class)
                                <option value="{{ $class->id }}" {{ $classTypeId === (int)$class->id ? 'selected' : '' }}>
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
                                <option value="{{ $exam->exam_id }}" {{ $examId === (int)$exam->exam_id ? 'selected' : '' }}>
                                    {{ $exam->exam_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(!$classTypeId || !$examId)
                    <div class="mob-submit-dock" style="margin-bottom: 0; box-shadow: none; padding: 0; border: none;">
                        <button type="submit" class="mob-btn-submit" id="btnLoadExam">
                            <i class="fa fa-arrow-circle-down"></i> Load Exam &amp; Subjects
                        </button>
                    </div>
                @endif
            </div>
        </form>

        {{-- When Class & Exam are selected --}}
        @if($classTypeId && $examId)
            {{-- KPI Summary Chips --}}
            <div class="mob-kpi-grid">
                <div class="mob-kpi-chip">
                    <div class="kpi-num">{{ $studentsCount }}</div>
                    <div class="kpi-lbl">Students</div>
                </div>
                <div class="mob-kpi-chip">
                    <div class="kpi-num">{{ $subjects->count() }}</div>
                    <div class="kpi-lbl">Subjects</div>
                </div>
                <div class="mob-kpi-chip">
                    <div class="kpi-num" style="color: #16a34a;"><i class="fa fa-check-circle"></i></div>
                    <div class="kpi-lbl">Exam Ready</div>
                </div>
            </div>

            @if($subjects->isEmpty())
                <div class="mob-alert mob-alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>No subjects configured for this class/exam. Please configure subjects in Exam Setup.</span>
                </div>
            @else
                {{-- 1-Click Excel Roll Template Downloader Card --}}
                <div class="mob-template-card">
                    <div class="mob-template-head">
                        <div class="mob-template-title">
                            <i class="fa fa-download text-success"></i> Pre-filled Student Template
                        </div>
                        <span class="badge badge-success" style="font-size: 8.5px;">XLSX</span>
                    </div>
                    <div class="mob-template-desc">
                        Contains all {{ $studentsCount }} student admission numbers, student names, and subject columns configured for {{ $selectedClassName }}.
                    </div>
                    <a href="{{ route('marks.template.download', ['class_type_id' => $classTypeId, 'exam_id' => $examId]) }}" class="mob-btn-download">
                        <i class="fa fa-file-excel-o"></i> Download Excel Roll Template (.xlsx)
                    </a>
                </div>

                {{-- Step 2: Spreadsheet File Upload Form --}}
                <form method="post" action="{{ route('marks.mapping.prepare') }}" enctype="multipart/form-data" id="mobUploadForm">
                    @csrf
                    <input type="hidden" name="class_type_id" value="{{ $classTypeId }}">
                    <input type="hidden" name="exam_id" value="{{ $examId }}">

                    <div class="mob-section-card">
                        <div class="mob-section-header">
                            <div class="mob-section-title">
                                <span class="mob-step-badge">02</span> Upload Spreadsheet
                            </div>
                            <span class="mob-section-status text-primary">Step 2 of 3</span>
                        </div>

                        {{-- Modern File Dropzone --}}
                        <div class="mob-dropzone-box" id="mobDropzone" onclick="document.getElementById('mob_excel_file').click()">
                            <input type="file" name="excel_file" id="mob_excel_file" accept=".xlsx,.xls,.csv" style="display: none;" required>
                            <div class="mob-dropzone-icon">
                                <i class="fa fa-cloud-upload"></i>
                            </div>
                            <div class="mob-dropzone-text" id="dropzoneText">
                                Tap here to choose Excel file
                            </div>
                            <div class="mob-dropzone-sub">
                                Supports .xlsx, .xls, .csv format (Max 10MB)
                            </div>
                            <div class="mob-file-selected-badge" id="fileSelectedBadge">
                                <i class="fa fa-file-excel-o text-success"></i>
                                <span id="fileNameDisplay">file.xlsx</span>
                            </div>
                        </div>

                        {{-- Guidelines Item List --}}
                        <div class="mob-guide-row">
                            <i class="fa fa-check-circle text-success"></i>
                            <span>First row of Excel must contain header column names.</span>
                        </div>
                        <div class="mob-guide-row">
                            <i class="fa fa-check-circle text-success"></i>
                            <span>Student ID or Admission Number column is required for mapping.</span>
                        </div>
                        <div class="mob-guide-row">
                            <i class="fa fa-check-circle text-success"></i>
                            <span>In Step 3, you can easily map Max, Min, Right, Wrong or Marks Scored.</span>
                        </div>
                    </div>

                    {{-- Bottom Submit Dock --}}
                    <div class="mob-submit-dock">
                        <a href="{{ url('fill-marks-by-excel?layout=mobile') }}" class="mob-btn-reset">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        <button type="submit" class="mob-btn-submit" id="btnUploadSubmit">
                            <i class="fa fa-arrow-right"></i> Upload &amp; Map Columns
                        </button>
                    </div>
                </form>
            @endif
        @endif

    @else
        {{-- =========================================================================
             STEP 3: COLUMN MAPPING & VERIFICATION
             ========================================================================= --}}
        <form action="{{ route('marks.mapping.save') }}" method="POST" id="mobSaveMappingForm">
            @csrf
            <input type="hidden" name="import_token" value="{{ $importToken }}">

            {{-- Summary Card --}}
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <span class="mob-step-badge">03</span> Uploaded File Overview
                    </div>
                    <span class="badge badge-success" style="font-size: 9px;">{{ $uploadedRowCount ?? 0 }} Student Rows</span>
                </div>
                <div class="mob-kpi-grid">
                    <div class="mob-kpi-chip">
                        <div class="kpi-num">{{ count($mappingHeaders ?? []) }}</div>
                        <div class="kpi-lbl">Excel Cols</div>
                    </div>
                    <div class="mob-kpi-chip">
                        <div class="kpi-num">{{ $subjects->count() }}</div>
                        <div class="kpi-lbl">Subjects</div>
                    </div>
                    <div class="mob-kpi-chip">
                        <div class="kpi-num">{{ $uploadedRowCount ?? 0 }}</div>
                        <div class="kpi-lbl">Rows Found</div>
                    </div>
                </div>
                <div style="font-size: 10.5px; color: #475569; line-height: 1.4; padding-top: 4px; border-top: 1px dashed #e2e8f0;">
                    <div><strong>Target Class:</strong> {{ $selectedClassName }}</div>
                    <div><strong>Target Exam:</strong> {{ $selectedExamName }}</div>
                </div>
            </div>

            {{-- Candidate Identifier Column --}}
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <span class="mob-step-badge"><i class="fa fa-id-card-o"></i></span> Student Identifier Column
                    </div>
                    <span class="text-danger" style="font-size: 9.5px; font-weight: 700;">* Required</span>
                </div>
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Admission No. / Student ID Column <span class="req">*</span></span>
                    </label>
                    <div class="mob-input-wrap">
                        <span class="mob-input-prepend"><i class="fa fa-hashtag"></i></span>
                        <select name="candidate_column" class="mob-select" required>
                            <option value="">-- Select Excel Column with Admission No --</option>
                            @foreach($mappingHeaders as $columnIndex => $header)
                                <option value="{{ $columnIndex }}"
                                    {{ (string)($candidateColumn ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                    Column {{ $columnIndex + 1 }}: {{ $header }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Subject Column Mappings --}}
            <div class="mob-section-card">
                <div class="mob-section-header">
                    <div class="mob-section-title">
                        <span class="mob-step-badge"><i class="fa fa-book"></i></span> Subject Columns Mapping
                    </div>
                    <span class="mob-section-status">Verify matched cols</span>
                </div>

                @forelse($subjects as $subject)
                    <div class="mob-mapping-card">
                        <div class="mob-mapping-card-head">
                            <span><i class="fa fa-book text-primary mr-1"></i> {{ $subject->sub_name ?: $subject->name }}</span>
                            <span class="badge badge-secondary" style="font-size: 8.5px;">Subject ID: {{ $subject->id }}</span>
                        </div>

                        <div class="mob-mapping-card-grid">
                            @foreach([
                                'marks' => 'Marks Scored',
                                'maximum' => 'Max Marks',
                                'minimum' => 'Min Marks',
                                'r' => 'Right (R)',
                                'w' => 'Wrong (W)',
                                'l' => 'Left (L)'
                            ] as $type => $label)
                                <div class="mob-form-group">
                                    <label class="mob-form-label" style="font-size: 9.5px;">{{ $label }}</label>
                                    <select name="mapping[{{ $subject->id }}][{{ $type }}]" class="mob-select" style="font-size: 10.5px; height: 30px;">
                                        <option value="">-- None --</option>
                                        @foreach($mappingHeaders as $columnIndex => $header)
                                            <option value="{{ $columnIndex }}"
                                                {{ (string)($mappingDefaults[$subject->id][$type] ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                                Col {{ $columnIndex + 1 }}: {{ $header }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3" style="font-size: 11px;">
                        No subjects found for this class.
                    </div>
                @endforelse
            </div>

            {{-- Bottom Submit Dock --}}
            <div class="mob-submit-dock">
                <a href="{{ url('fill-marks-by-excel?class_type_id='.$classTypeId.'&exam_id='.$examId.'&layout=mobile') }}" class="mob-btn-reset">
                    <i class="fa fa-arrow-left"></i> Re-upload
                </a>
                <button type="submit" class="mob-btn-submit btn-save" id="btnSaveMapping">
                    <i class="fa fa-check-circle"></i> Save &amp; Import Marks
                </button>
            </div>
        </form>
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

    // 2. Dynamic Exam Loader on Class Change
    $('#mob_class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#mob_exam_id');

        // Sync horizontal pill
        $('.mob-class-pill').removeClass('active');
        if (classId) {
            $('.mob-class-pill[data-id="' + classId + '"]').addClass('active');
        }

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            return;
        }

        $examSelect.html('<option value="">Loading examinations...</option>');

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
    });

    // 3. Dropzone File Input Interaction
    $('#mob_excel_file').on('change', function() {
        if (this.files && this.files.length > 0) {
            var file = this.files[0];
            var sizeKb = (file.size / 1024).toFixed(1);
            $('#dropzoneText').text('Selected: ' + file.name);
            $('#fileNameDisplay').text(file.name + ' (' + sizeKb + ' KB)');
            $('#fileSelectedBadge').css('display', 'inline-flex');
            $('#mobDropzone').css('border-color', '#10b981').css('background', '#f0fdf4');
        } else {
            $('#dropzoneText').text('Tap here to choose Excel file');
            $('#fileSelectedBadge').hide();
            $('#mobDropzone').css('border-color', '#94a3b8').css('background', '#f8fafc');
        }
    });

    // 4. Form Submit Loading State
    $('#mobUploadForm').on('submit', function() {
        var $btn = $('#btnUploadSubmit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading Spreadsheet...');
    });

    $('#mobSaveMappingForm').on('submit', function() {
        var $btn = $('#btnSaveMapping');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving Mapped Marks...');
    });
});
</script>
@endsection

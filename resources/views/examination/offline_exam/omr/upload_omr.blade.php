@extends('layout.app')

@section('title', 'Upload & Evaluate OMR Sheets - AI Extractor - Arise ERP')

@php
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $requestedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $allExams = $allExams ?? collect();
    $selectedExam = $selectedExam ?? null;
    $recentScans = $recentScans ?? collect();
    $totalScannedToday = $totalScannedToday ?? 0;
    $avgAccuracy = $avgAccuracy ?? 0;
    $totalEvaluatedCount = $totalEvaluatedCount ?? 0;
    $extractorApiUrl = config('services.extractor.url', 'https://extractor.rusoft.in');
@endphp

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ==========================================================================
   ARISE ERP - AI OMR EXTRACTOR WORKSPACE THEME
   - Layout: Standard Arise .content-wrapper with left sidebar clearance
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   - Typography: 11.5px table text, 10.5px uppercase bold thead
   ========================================================================== */

.content-wrapper.omr-page {
    background: #eef2f6 !important;
}

.omr-page-layout {
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    padding: 6px 12px 24px 12px;
    background: #eef2f6;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    color: #0f172a;
    box-sizing: border-box;
    gap: 8px;
}

/* 1. Hero Header Banner */
.omr-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    flex-shrink: 0;
}
.omr-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #38bdf8;
    font-weight: 700;
    display: block;
    margin-bottom: 2px;
}
.omr-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.omr-subtitle {
    font-size: 10.5px;
    color: #cbd5e1;
    margin: 2px 0 0 0;
    line-height: 1.3;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    height: 28px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
}
.dash-btn-outline {
    background: rgba(255,255,255,0.12);
    border-color: rgba(255,255,255,0.3);
    color: #ffffff !important;
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.22);
    color: #ffffff !important;
    border-color: #ffffff;
}
.dash-btn-light {
    background: #ffffff;
    border-color: #ffffff;
    color: #002C54 !important;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}

/* 2. Fast KPI Strip */
.omr-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 2px;
    flex-shrink: 0;
}
@media (max-width: 992px) {
    .omr-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
.kpi-mini-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    border-left: 3px solid #002C54;
    transition: all 0.15s ease;
}
.kpi-mini-card.kpi-classes { border-left-color: #0284c7; }
.kpi-mini-card.kpi-published { border-left-color: #10b981; }
.kpi-mini-card.kpi-pending { border-left-color: #f59e0b; }
.kpi-mini-card .kpi-label {
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    letter-spacing: .04em;
    display: block;
}
.kpi-mini-card .kpi-value {
    font-size: 17px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}
.kpi-mini-card .kpi-sub {
    font-size: 9.5px;
    color: #94a3b8;
    font-weight: 600;
}

/* 3. Cards & Containers */
.omr-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}
.omr-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.omr-card-title {
    font-size: 12.5px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.omr-card-desc {
    font-size: 10.5px;
    color: #64748b;
    margin: 1px 0 0 0;
}
.omr-card-body {
    padding: 10px 12px;
}

/* Dropzone */
.omr-dropzone-box {
    border: 2px dashed #93c5fd;
    background: #f0f7ff;
    border-radius: 4px;
    padding: 16px 14px;
    text-align: center;
    cursor: pointer;
    transition: all .15s ease;
}
.omr-dropzone-box.active, .omr-dropzone-box:hover {
    border-color: #0284c7;
    background: #e0f2fe;
}
.omr-dropzone-box.disabled {
    cursor: not-allowed;
    opacity: 0.65;
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* Staging Table & Ledger Table */
.omr-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.omr-table th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 7px 10px;
    border: 1px solid #002C54;
    vertical-align: middle;
}
.omr-table td {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
}
.omr-table tr:hover td {
    background: #f8fafc;
}
.omr-table tr.row-unmatched td {
    background: #fffbeb !important;
}

/* Badges & Pills */
.badge-pill-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
}
.badge-staged {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.badge-matched {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.badge-unmatched {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-saved {
    background: #10b981;
    color: #ffffff;
}

/* Thumbnail preview */
.omr-thumb {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.omr-thumb:hover {
    transform: scale(1.15);
    border-color: #0284c7;
}

/* Staging Action Bar */
.staging-bar {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}

/* Progress Bar */
.omr-progress-container {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}
.omr-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #0284c7, #10b981);
    width: 0%;
    transition: width 0.25s ease;
}
</style>
@endsection

@section('content')
<div class="content-wrapper omr-page">
    <div class="omr-page-layout">

        {{-- 1. Hero Header Banner --}}
        <div class="omr-hero">
            <div>
                <span class="omr-kicker"><i class="fa fa-cogs mr-1"></i> Examination Control &bull; OMR Engine</span>
                <h1 class="omr-title">
                    <i class="fa fa-check-square-o"></i> Upload &amp; Evaluate OMR Sheets
                </h1>
                <p class="omr-subtitle">
                    Connected to AI Extractor API (<code>{{ $extractorApiUrl }}</code>). Upload sheets in bulk, review evaluations in staging list, and finalize marks into exam records.
                </p>
            </div>
            <div class="d-flex align-items-center" style="gap: 6px;">
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="Back to Examinations">
                    <i class="fa fa-arrow-left"></i> Exams List
                </a>
                <a href="{{ url('green_sheet_report') }}" class="dash-btn dash-btn-outline" title="View Green Sheet">
                    <i class="fa fa-file-text-o"></i> Green Sheet
                </a>
                <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-light" title="Exam Wise Report">
                    <i class="fa fa-bar-chart"></i> Reports
                </a>
            </div>
        </div>

        {{-- 2. Fast KPI Strip --}}
        <div class="omr-kpi-strip">
            <div class="kpi-mini-card kpi-classes">
                <div>
                    <span class="kpi-label">Today's Saved Scans</span>
                    <div class="kpi-value" id="kpi-today-scans">{{ number_format($totalScannedToday) }}</div>
                    <span class="kpi-sub">committed to exam</span>
                </div>
                <div style="color: #0284c7; font-size: 20px;">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
            </div>

            <div class="kpi-mini-card kpi-published">
                <div>
                    <span class="kpi-label">Total Saved Evaluations</span>
                    <div class="kpi-value text-success" id="kpi-total-scans">{{ number_format($totalEvaluatedCount) }}</div>
                    <span class="kpi-sub">all-time records</span>
                </div>
                <div style="color: #10b981; font-size: 20px;">
                    <i class="fa fa-archive"></i>
                </div>
            </div>

            <div class="kpi-mini-card kpi-pending">
                <div>
                    <span class="kpi-label">Target Examination</span>
                    <div class="kpi-value text-dark" id="kpi-exam-questions">{{ $selectedExam ? ($selectedExam->number_of_questions ?: 50) . ' Q' : '--' }}</div>
                    <span class="kpi-sub font-mono font-weight-bold" id="kpi-exam-code">{{ $selectedExam ? ($selectedExam->code ?: $selectedExam->exam_code) : 'None' }}</span>
                </div>
                <div style="color: #f59e0b; font-size: 20px;">
                    <i class="fa fa-leanpub"></i>
                </div>
            </div>

            <div class="kpi-mini-card">
                <div>
                    <span class="kpi-label">AI Extractor Engine</span>
                    <div class="kpi-value" style="color: #10b981; font-size: 14px;">
                        <i class="fa fa-circle text-success" style="font-size: 10px;"></i> Online
                    </div>
                    <span class="kpi-sub text-truncate" style="max-width: 140px; display: block;" title="{{ $extractorApiUrl }}">
                        {{ parse_url($extractorApiUrl, PHP_URL_HOST) ?: 'extractor.rusoft.in' }}
                    </span>
                </div>
                <div style="color: #002C54; font-size: 20px;">
                    <i class="fa fa-server"></i>
                </div>
            </div>
        </div>

        {{-- 3. Top Row: Step 1 (Select Exam) & Step 2 (Upload Dropzone) --}}
        <div class="row g-2">
            
            {{-- Card 1: Target Exam Selector --}}
            <div class="col-lg-5">
                <div class="omr-card" style="height: 100%;">
                    <div class="omr-card-header">
                        <div>
                            <h2 class="omr-card-title">
                                <i class="fa fa-leanpub text-primary"></i> 1. Select Target Examination
                            </h2>
                            <p class="omr-card-desc">Answer key, question count, and marking scheme will follow this exam.</p>
                        </div>
                        <span class="badge badge-primary px-2 py-1" style="font-size: 10px; background: #002C54;">Step 1</span>
                    </div>
                    <div class="omr-card-body py-2">
                        <label for="omr-exam-select" class="font-weight-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                            Examination <span class="text-danger">*</span>
                        </label>
                        <select id="omr-exam-select" class="form-control form-control-sm" style="font-weight: 600; font-size: 12px; height: 32px; border-color: #cbd5e1;">
                            <option value="">-- Choose an examination --</option>
                            @foreach($allExams as $ex)
                                @php
                                    $qCount = (int) ($ex->number_of_questions ?: 50);
                                    $correctM = (float) ($ex->marks_per_correct ?: 1.0);
                                    $incorrectM = (float) ($ex->marks_per_incorrect ?: 0.0);
                                    $keyCount = is_array($ex->answer_key) ? count($ex->answer_key) : 0;
                                    $eCode = $ex->code ?: ($ex->exam_code ?: 'EXAM-'.$ex->id);
                                    $isSelected = ($selectedExam && $selectedExam->id == $ex->id);
                                @endphp
                                <option value="{{ $ex->id }}"
                                    data-name="{{ $ex->name }}"
                                    data-code="{{ $eCode }}"
                                    data-questions="{{ $qCount }}"
                                    data-correct="{{ $correctM }}"
                                    data-incorrect="{{ $incorrectM }}"
                                    data-maxmarks="{{ $ex->exam_maximum_marks ?: ($qCount * $correctM) }}"
                                    data-class="{{ $ex->classType ? $ex->classType->name : 'All Classes' }}"
                                    data-classid="{{ $ex->class_type_id }}"
                                    data-keycount="{{ $keyCount }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $eCode }} — {{ $ex->name }} ({{ $qCount }} Q | +{{ $correctM }}/-{{ $incorrectM }} | {{ $ex->classType ? $ex->classType->name : 'All' }})
                                </option>
                            @endforeach
                        </select>

                        {{-- Selected Exam Info Pill --}}
                        <div id="omr-selected-exam-badge" class="{{ $selectedExam ? '' : 'd-none' }} mt-2 p-2 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center font-weight-bold">
                                <span id="omr-badge-title" class="text-dark">{{ $selectedExam ? $selectedExam->name : '' }}</span>
                                <span id="omr-badge-code" class="font-mono text-primary font-weight-bold">{{ $selectedExam ? ($selectedExam->code ?: $selectedExam->exam_code) : '' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted mt-1" style="font-size: 10.5px;">
                                <span id="omr-badge-questions"><i class="fa fa-list-ol mr-1"></i> {{ $selectedExam ? ($selectedExam->number_of_questions ?: 50) . ' Questions' : '' }}</span>
                                <span id="omr-badge-scheme" class="font-weight-bold text-success"><i class="fa fa-tag mr-1"></i> Marks: +{{ $selectedExam ? ($selectedExam->marks_per_correct ?: 1.0) : 1 }}/-{{ $selectedExam ? ($selectedExam->marks_per_incorrect ?: 0.0) : 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Bulk Upload Dropzone --}}
            <div class="col-lg-7">
                <div class="omr-card" style="height: 100%;">
                    <div class="omr-card-header">
                        <div>
                            <h2 class="omr-card-title">
                                <i class="fa fa-cloud-upload text-info"></i> 2. Upload OMR Sheets (Multiple / Bulk Supported)
                            </h2>
                            <p class="omr-card-desc">Select multiple image files at once (JPG, PNG, WebP). Files are sent to AI Extractor and staged below.</p>
                        </div>
                        <span class="badge badge-info px-2 py-1" style="font-size: 10px;">Step 2</span>
                    </div>
                    <div class="omr-card-body py-2">
                        <div id="omr-upload-dropzone" class="omr-dropzone-box {{ $selectedExam ? 'active' : 'disabled' }}" onclick="triggerFileInput();">
                            <i class="fa fa-cloud-upload text-primary" style="font-size: 28px; margin-bottom: 4px;"></i>
                            <div class="font-weight-bold text-dark" style="font-size: 12.5px;" id="omr-dropzone-title">
                                {{ $selectedExam ? 'Click or Drop OMR Sheet Image(s) Here' : 'Select an examination on the left first' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px; margin-top: 2px;">
                                Select 1, 10, or 50+ sheets at once. Data will be extracted and displayed in the staging table for review before saving.
                            </div>
                            <input type="file" id="omr-image-input" multiple accept="image/png,image/jpeg,image/webp" style="display: none;" {{ $selectedExam ? '' : 'disabled' }}>
                        </div>

                        {{-- Batch Progress Bar & Status Text --}}
                        <div id="omr-batch-progress-wrapper" class="mt-2 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 11px;">
                                <span id="omr-batch-status-text" class="font-weight-bold text-primary">
                                    <i class="fa fa-refresh fa-spin mr-1"></i> Extracting sheet 1 of 5...
                                </span>
                                <span id="omr-batch-percent-text" class="font-weight-bold text-dark">20%</span>
                            </div>
                            <div class="omr-progress-container">
                                <div id="omr-batch-progress-bar" class="omr-progress-bar"></div>
                            </div>
                        </div>

                        <div id="omr-status-box" class="mt-2 py-1 px-2 rounded font-weight-bold" style="background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 11px; color: #475569;">
                            <i class="fa fa-info-circle mr-1"></i> <span id="omr-status-text">Select an exam to enable sheet upload.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- 4. Staging / Preview Workspace (THE CORE REQUIREMENT)     --}}
        {{-- ======================================================== --}}
        <div class="omr-card mt-1">
            <div class="omr-card-header" style="background: #ffffff; border-bottom: 2px solid #002C54;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h2 class="omr-card-title" style="font-size: 14px;">
                        <i class="fa fa-table text-primary"></i> 3. Staged OMR Sheets Preview &amp; Verification
                    </h2>
                    <span class="badge badge-light px-2 py-1 border" id="badge-staged-total" style="font-size: 11px;">
                        Total Staged: <b id="count-staged-total">0</b>
                    </span>
                    <span class="badge badge-success px-2 py-1" id="badge-staged-matched" style="font-size: 11px;">
                        Matched: <b id="count-staged-matched">0</b>
                    </span>
                    <span class="badge badge-warning px-2 py-1 text-dark" id="badge-staged-unmatched" style="font-size: 11px;">
                        Needs Review: <b id="count-staged-unmatched">0</b>
                    </span>
                </div>
                
                {{-- Final Submit & Clear Buttons --}}
                <div class="d-flex align-items-center" style="gap: 6px;">
                    <button type="button" id="btn-clear-staged" class="btn btn-outline-danger btn-sm" style="font-size: 11px; font-weight: 600; padding: 4px 10px;" onclick="clearAllStaged();" disabled>
                        <i class="fa fa-trash-o mr-1"></i> Clear List
                    </button>
                    <button type="button" id="btn-final-submit" class="btn btn-success btn-sm px-3" style="font-size: 12px; font-weight: 700; height: 32px;" onclick="confirmFinalSubmit();" disabled>
                        <i class="fa fa-check-circle mr-1"></i> Final Submit &amp; Save Marks (<span id="btn-save-count">0</span>)
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="omr-table" id="omr-staging-table">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;">
                                <input type="checkbox" id="staged-select-all" checked onchange="toggleSelectAllStaged(this);">
                            </th>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="width: 50px; text-align: center;">Sheet</th>
                            <th style="min-width: 130px;">File Name</th>
                            <th style="min-width: 120px;">Detected Roll #</th>
                            <th style="min-width: 180px;">Student Match</th>
                            <th style="min-width: 110px; text-align: center;">Questions</th>
                            <th style="min-width: 130px; text-align: center;">Score &amp; Marks</th>
                            <th style="min-width: 90px; text-align: center;">Status</th>
                            <th style="min-width: 120px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="omr-staging-tbody">
                        <tr id="staging-empty-row">
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fa fa-file-image-o mr-1" style="font-size: 18px; color: #94a3b8; display: block; margin-bottom: 4px;"></i>
                                No sheets staged yet. Upload OMR sheets above to view extracted data and verify before final submit.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-top bg-light d-flex justify-content-between align-items-center text-muted" style="font-size: 11px;">
                <span>
                    <i class="fa fa-info-circle text-primary mr-1"></i> 
                    <b>Note:</b> Data is only staged in memory and will <u>NOT</u> be saved to database or student exam mark sheets until you click <b>"Final Submit &amp; Save Marks"</b>.
                </span>
                <span id="staging-summary-marks" class="font-weight-bold text-dark"></span>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- 5. Saved History Ledger (Previously Committed Records)   --}}
        {{-- ======================================================== --}}
        <div class="omr-card mt-2">
            <div class="omr-card-header">
                <div>
                    <h2 class="omr-card-title">
                        <i class="fa fa-history text-secondary"></i> 4. Saved Evaluations History &amp; Marks Ledger
                    </h2>
                    <p class="omr-card-desc">Records previously submitted and committed to exam marks in the database.</p>
                </div>
                <span id="omr-saved-count-badge" class="badge badge-primary px-2 py-1" style="font-size: 11px; background: #002C54;">
                    {{ $recentScans->count() }} Records Saved
                </span>
            </div>

            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="omr-table">
                    <thead>
                        <tr>
                            <th style="min-width: 130px;">Source File</th>
                            <th style="min-width: 160px;">Student Profile</th>
                            <th style="min-width: 140px;">Examination</th>
                            <th style="text-align: center; min-width: 70px;">Right</th>
                            <th style="text-align: center; min-width: 70px;">Wrong</th>
                            <th style="text-align: center; min-width: 70px;">Left</th>
                            <th style="min-width: 85px;">Total</th>
                            <th style="min-width: 85px;">Obtained</th>
                            <th style="min-width: 85px;">Percentage</th>
                            <th style="text-align: center; min-width: 60px;">Rank</th>
                            <th style="text-align: right; min-width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="omr-saved-tbody">
                        @forelse($recentScans as $scan)
                            @php
                                $stName = $scan->student ? ($scan->student->first_name . ' ' . $scan->student->last_name) : 'Student #' . $scan->unique_id;
                            @endphp
                            <tr id="scan-row-{{ $scan->id }}">
                                <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="{{ $scan->source_filename }}">
                                    <i class="fa fa-file-image-o text-muted mr-1"></i> {{ $scan->source_filename ?: 'omr_scan.png' }}
                                </td>
                                <td>
                                    <span class="font-mono font-weight-bold text-primary">{{ $scan->unique_id }}</span>
                                    <div class="text-muted small font-weight-bold">{{ $stName }}</div>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark">{{ $scan->exam ? $scan->exam->name : 'Exam #' . $scan->exam_id }}</span>
                                    <div class="text-muted small font-mono">{{ $scan->exam ? ($scan->exam->code ?: $scan->exam->exam_code) : '' }}</div>
                                </td>
                                <td class="text-center font-weight-bold text-success">{{ $scan->correct_answers }}</td>
                                <td class="text-center font-weight-bold text-danger">{{ $scan->incorrect_answers }}</td>
                                <td class="text-center text-muted">{{ $scan->blank_answers }}</td>
                                <td class="font-mono font-weight-bold">{{ number_format($scan->total_marks, 2) }}</td>
                                <td class="font-mono font-weight-bold text-success">{{ number_format($scan->obtained_marks, 2) }}</td>
                                <td>
                                    <span class="badge-pill-status badge-matched">{{ $scan->percentage }}%</span>
                                </td>
                                <td class="text-center font-weight-bold text-primary">#{{ $scan->rank ?: 1 }}</td>
                                <td class="text-right">
                                    <span class="badge-pill-status badge-saved" title="Marks recorded in student exam file">
                                        <i class="fa fa-check-circle"></i> Saved
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr id="omr-saved-empty-row">
                                <td colspan="11" class="text-center py-3 text-muted">
                                    <i class="fa fa-folder-open-o mr-1"></i> No saved evaluations found for this exam yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- ======================================================== --}}
{{-- MODAL 1: Visual Annotated OMR Sheet Inspection           --}}
{{-- ======================================================== --}}
<div class="modal fade" id="modalAnnotatedSheet" tabindex="-1" role="dialog" aria-labelledby="modalAnnotatedSheetLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 900px;" role="document">
        <div class="modal-content" style="border-radius: 4px; border: 1px solid #cbd5e1;">
            <div class="modal-header py-2 px-3" style="background: #002C54; color: #ffffff;">
                <h5 class="modal-title font-weight-bold" id="modalAnnotatedSheetLabel" style="font-size: 13px;">
                    <i class="fa fa-eye mr-1"></i> Annotated OMR Sheet Inspection
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center" style="background: #f8fafc; max-height: 80vh; overflow-y: auto;">
                <div id="modal-img-spinner" class="py-5 text-muted d-none">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2 font-weight-bold">Loading annotated preview...</p>
                </div>
                <img id="modal-annotated-img" src="" alt="Annotated OMR" style="max-width: 100%; height: auto; border: 1px solid #cbd5e1; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border-radius: 2px;">
                <div class="mt-2 text-muted small" id="modal-annotated-info"></div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                <span class="text-muted small"><i class="fa fa-info-circle mr-1"></i> Green circles indicate AI-detected bubble fills and registration bounding boxes.</span>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================== --}}
{{-- MODAL 2: Question-by-Question Answers Matrix             --}}
{{-- ======================================================== --}}
<div class="modal fade" id="modalAnswersGrid" tabindex="-1" role="dialog" aria-labelledby="modalAnswersGridLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 650px;" role="document">
        <div class="modal-content" style="border-radius: 4px; border: 1px solid #cbd5e1;">
            <div class="modal-header py-2 px-3" style="background: #002C54; color: #ffffff;">
                <h5 class="modal-title font-weight-bold" id="modalAnswersGridLabel" style="font-size: 13px;">
                    <i class="fa fa-list-ol mr-1"></i> Extracted Answers Breakdown
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2" style="max-height: 70vh; overflow-y: auto;">
                <div class="p-2 mb-2 rounded bg-light border d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                    <div>
                        <span class="text-muted">Student:</span> <b id="modal-ans-student">--</b>
                        <span class="text-muted ml-2">Roll #:</span> <b class="font-mono text-primary" id="modal-ans-roll">--</b>
                    </div>
                    <div>
                        <span class="text-muted">Score:</span> <b class="text-success" id="modal-ans-score">--</b>
                    </div>
                </div>
                <table class="omr-table" style="font-size: 11px;">
                    <thead>
                        <tr>
                            <th style="width: 20%;">#</th>
                            <th style="width: 25%; text-align: center;">Marked Bubble</th>
                            <th style="width: 25%; text-align: center;">Key Answer</th>
                            <th style="width: 30%; text-align: right;">Result</th>
                        </tr>
                    </thead>
                    <tbody id="modal-answers-tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer py-2 px-3 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const examSelect = document.getElementById('omr-exam-select');
    const fileInput = document.getElementById('omr-image-input');
    const dropzone = document.getElementById('omr-upload-dropzone');
    const dropzoneTitle = document.getElementById('omr-dropzone-title');
    const statusText = document.getElementById('omr-status-text');
    const statusBox = document.getElementById('omr-status-box');

    const badgeBox = document.getElementById('omr-selected-exam-badge');
    const badgeTitle = document.getElementById('omr-badge-title');
    const badgeCode = document.getElementById('omr-badge-code');
    const badgeQuestions = document.getElementById('omr-badge-questions');
    const badgeScheme = document.getElementById('omr-badge-scheme');

    const kpiQuestions = document.getElementById('kpi-exam-questions');
    const kpiCode = document.getElementById('kpi-exam-code');
    const kpiTodayScans = document.getElementById('kpi-today-scans');
    const kpiTotalScans = document.getElementById('kpi-total-scans');

    const progressWrapper = document.getElementById('omr-batch-progress-wrapper');
    const progressBar = document.getElementById('omr-batch-progress-bar');
    const progressStatusText = document.getElementById('omr-batch-status-text');
    const progressPercentText = document.getElementById('omr-batch-percent-text');

    const stagingTbody = document.getElementById('omr-staging-tbody');
    const countStagedTotal = document.getElementById('count-staged-total');
    const countStagedMatched = document.getElementById('count-staged-matched');
    const countStagedUnmatched = document.getElementById('count-staged-unmatched');
    const btnSaveCount = document.getElementById('btn-save-count');
    const btnFinalSubmit = document.getElementById('btn-final-submit');
    const btnClearStaged = document.getElementById('btn-clear-staged');
    const stagingSummaryMarks = document.getElementById('staging-summary-marks');

    const modalAnnotatedImg = document.getElementById('modal-annotated-img');
    const modalAnnotatedInfo = document.getElementById('modal-annotated-info');
    const modalAnswersTbody = document.getElementById('modal-answers-tbody');
    const modalAnsStudent = document.getElementById('modal-ans-student');
    const modalAnsRoll = document.getElementById('modal-ans-roll');
    const modalAnsScore = document.getElementById('modal-ans-score');

    // In-memory staged sheets store: Map<temp_id, sheetObject>
    const stagedSheets = new Map();
    let isProcessingBatch = false;

    // Helper: get selected option element
    const getSelectedExamOption = () => examSelect.options[examSelect.selectedIndex];

    // Trigger file chooser
    window.triggerFileInput = function () {
        if (!examSelect.value) {
            alert('Please select a target examination first.');
            examSelect.focus();
            return;
        }
        if (isProcessingBatch) return;
        fileInput.click();
    };

    // Update Exam details when dropdown changes
    examSelect.addEventListener('change', function () {
        const opt = getSelectedExamOption();
        if (this.value && opt) {
            badgeTitle.textContent = opt.dataset.name || '';
            badgeCode.textContent = opt.dataset.code || '';
            badgeQuestions.innerHTML = `<i class="fa fa-list-ol mr-1"></i> ${opt.dataset.questions || '50'} Questions`;
            badgeScheme.innerHTML = `<i class="fa fa-tag mr-1"></i> Marks: +${opt.dataset.correct || '1'}/-${opt.dataset.incorrect || '0'}`;
            badgeBox.classList.remove('d-none');

            kpiQuestions.textContent = `${opt.dataset.questions || '50'} Q`;
            kpiCode.textContent = opt.dataset.code || '';

            dropzone.classList.remove('disabled');
            dropzone.classList.add('active');
            dropzoneTitle.textContent = 'Click or Drop OMR Sheet Image(s) Here';
            fileInput.disabled = false;

            statusText.textContent = `Target Exam: ${opt.dataset.name}. Ready to upload OMR sheets.`;
            statusBox.style.background = '#f0fdf4';
            statusBox.style.borderColor = '#bbf7d0';
            statusBox.style.color = '#166534';
        } else {
            badgeBox.classList.add('d-none');
            kpiQuestions.textContent = '--';
            kpiCode.textContent = 'None';
            dropzone.classList.add('disabled');
            dropzone.classList.remove('active');
            dropzoneTitle.textContent = 'Select an examination on the left first';
            fileInput.disabled = true;

            statusText.textContent = 'Select an exam to enable sheet upload.';
            statusBox.style.background = '#f1f5f9';
            statusBox.style.borderColor = '#e2e8f0';
            statusBox.style.color = '#475569';
        }
    });

    // Drag and Drop handlers
    dropzone.addEventListener('dragover', function (e) {
        e.preventDefault();
        if (examSelect.value && !isProcessingBatch) {
            dropzone.style.borderColor = '#0284c7';
            dropzone.style.background = '#dbeafe';
        }
    });
    dropzone.addEventListener('dragleave', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#93c5fd';
        dropzone.style.background = '#f0f7ff';
    });
    dropzone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropzone.style.borderColor = '#93c5fd';
        dropzone.style.background = '#f0f7ff';
        if (!examSelect.value || isProcessingBatch) return;
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            processFilesQueue(Array.from(dt.files));
        }
    });

    // File input change handler
    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length) {
            processFilesQueue(Array.from(this.files));
        }
    });

    // Process files sequentially or in parallel batches
    async function processFilesQueue(files) {
        const examId = examSelect.value;
        if (!examId || !files.length) return;

        isProcessingBatch = true;
        fileInput.disabled = true;
        dropzone.classList.add('disabled');
        progressWrapper.classList.remove('d-none');

        // Remove empty state placeholder row if present
        document.getElementById('staging-empty-row')?.remove();

        const totalFiles = files.length;
        let completed = 0;
        let successful = 0;

        for (let i = 0; i < totalFiles; i++) {
            const file = files[i];
            const percent = Math.round(((i) / totalFiles) * 100);
            progressBar.style.width = `${percent}%`;
            progressPercentText.textContent = `${percent}%`;
            progressStatusText.innerHTML = `<i class="fa fa-refresh fa-spin mr-1"></i> Extracting sheet ${i + 1} of ${totalFiles}: <b>${escapeHtml(file.name)}</b>...`;

            // Create temporary staging row
            const tempRowId = 'temp-row-' + Math.random().toString(36).substr(2, 9);
            const tempRow = document.createElement('tr');
            tempRow.id = tempRowId;
            tempRow.className = 'bg-light';
            tempRow.innerHTML = `
                <td class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i></td>
                <td class="text-center font-weight-bold text-muted">${stagedSheets.size + 1}</td>
                <td class="text-center"><i class="fa fa-file-image-o text-muted"></i></td>
                <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</td>
                <td colspan="5" class="text-primary font-weight-bold">
                    <i class="fa fa-circle-o-notch fa-spin mr-1"></i> Connecting to AI Extractor API (https://extractor.rusoft.in)...
                </td>
                <td class="text-right text-muted"><span class="badge badge-light">Extracting</span></td>
            `;
            stagingTbody.appendChild(tempRow);

            try {
                const formData = new FormData();
                formData.append('exam_id', examId);
                formData.append('image', file);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch("{{ url('upload_omr/process_sheet') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                const data = await response.json();
                if (!response.ok || data.status === 'error') {
                    throw new Error(data.message || 'Extractor service failed.');
                }

                const sheet = data.sheet;
                sheet.selected = true; // default checked for final submit
                stagedSheets.set(sheet.temp_id, sheet);

                // Render real staged row
                renderStagedRow(sheet, tempRow);
                successful++;
            } catch (err) {
                tempRow.className = 'bg-light text-danger';
                tempRow.innerHTML = `
                    <td class="text-center text-danger"><i class="fa fa-times-circle"></i></td>
                    <td class="text-center font-weight-bold text-danger">${stagedSheets.size + 1}</td>
                    <td class="text-center"><i class="fa fa-file-image-o text-danger"></i></td>
                    <td class="font-weight-bold text-danger text-truncate" style="max-width: 140px;">${escapeHtml(file.name)}</td>
                    <td colspan="5" class="text-danger small font-weight-bold">
                        <i class="fa fa-exclamation-triangle mr-1"></i> ${escapeHtml(err.message)}
                    </td>
                    <td class="text-right">
                        <button class="btn btn-xs btn-outline-danger" onclick="document.getElementById('${tempRowId}')?.remove(); updateStagingCounters();">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;
            }

            completed++;
            updateStagingCounters();
        }

        // Finish batch
        progressBar.style.width = '100%';
        progressPercentText.textContent = '100%';
        progressStatusText.innerHTML = `<i class="fa fa-check-circle text-success mr-1"></i> Extracted ${successful} of ${totalFiles} sheets successfully! Review and finalize below.`;
        
        setTimeout(() => {
            progressWrapper.classList.add('d-none');
        }, 3000);

        fileInput.value = '';
        fileInput.disabled = false;
        dropzone.classList.remove('disabled');
        dropzone.classList.add('active');
        isProcessingBatch = false;
    }

    // Render a single staged sheet row
    function renderStagedRow(sheet, existingRow = null) {
        const row = existingRow || document.createElement('tr');
        row.id = `staged-row-${sheet.temp_id}`;
        row.className = sheet.student?.is_matched ? '' : 'row-unmatched';

        const thumbSrc = sheet.annotated_image_base64 || sheet.image_url;
        const student = sheet.student || {};
        const evalData = sheet.evaluation || {};
        const isMatched = !!student.is_matched;

        const studentHtml = isMatched ? `
            <div>
                <span class="badge-pill-status badge-matched">
                    <i class="fa fa-user"></i> ${escapeHtml(student.name)}
                </span>
                <div class="text-muted small mt-1 font-weight-bold">
                    Class: ${escapeHtml(student.class_name || 'N/A')} ${student.father_name ? '| S/O ' + escapeHtml(student.father_name) : ''}
                </div>
            </div>
        ` : `
            <div>
                <span class="badge-pill-status badge-unmatched">
                    <i class="fa fa-exclamation-triangle"></i> Unmatched Roll #
                </span>
                <div class="text-danger small font-italic mt-1">Check roll number or link student</div>
            </div>
        `;

        const scoreHtml = `
            <div>
                <span class="font-weight-bold text-success" style="font-size: 13px;">${evalData.obtained_marks}</span>
                <span class="text-muted font-mono small">/ ${evalData.total_marks}</span>
                <span class="badge badge-success ml-1 font-weight-bold" style="font-size: 9.5px;">${evalData.percentage}%</span>
                <div class="text-muted small mt-1">
                    <span class="text-success font-weight-bold">🟢 ${evalData.correct_answers}</span> &bull;
                    <span class="text-danger font-weight-bold">🔴 ${evalData.incorrect_answers}</span> &bull;
                    <span class="text-muted font-weight-bold">⚪ ${evalData.blank_answers}</span>
                </div>
            </div>
        `;

        row.innerHTML = `
            <td class="text-center">
                <input type="checkbox" class="staged-checkbox" data-temp-id="${sheet.temp_id}" ${sheet.selected !== false ? 'checked' : ''} onchange="toggleSheetSelect('${sheet.temp_id}', this.checked);">
            </td>
            <td class="text-center font-weight-bold text-muted staged-index"></td>
            <td class="text-center">
                <img src="${thumbSrc}" class="omr-thumb" alt="Sheet" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="Click to inspect annotated scan">
            </td>
            <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="${escapeHtml(sheet.source_filename)}">
                <i class="fa fa-file-image-o text-muted mr-1"></i> ${escapeHtml(sheet.source_filename)}
            </td>
            <td>
                <div class="input-group input-group-sm" style="max-width: 130px;">
                    <input type="text" class="form-control form-control-sm font-mono font-weight-bold text-primary roll-input" 
                           value="${escapeHtml(sheet.roll_number || '')}" 
                           placeholder="Enter Roll #"
                           style="height: 26px; font-size: 11.5px; border-color: #cbd5e1;"
                           onchange="handleRollNumberChange('${sheet.temp_id}', this.value);">
                </div>
            </td>
            <td id="student-cell-${sheet.temp_id}">
                ${studentHtml}
            </td>
            <td class="text-center">
                <div class="font-weight-bold text-dark">${sheet.summary?.attempted || 0} / ${sheet.summary?.total_questions || 50}</div>
                <div class="text-muted small">Blank: ${sheet.summary?.unattempted || 0}</div>
            </td>
            <td class="text-center">
                ${scoreHtml}
            </td>
            <td class="text-center">
                <span class="badge-pill-status badge-staged">
                    <i class="fa fa-clock-o"></i> Staged
                </span>
            </td>
            <td class="text-right" style="white-space: nowrap;">
                <button type="button" class="btn btn-xs btn-outline-primary mr-1" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="Inspect Annotated Sheet & Bubbles">
                    <i class="fa fa-eye"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-info mr-1" onclick="viewAnswersModal('${sheet.temp_id}');" title="View Question-by-Question Matrix">
                    <i class="fa fa-list"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeStagedSheet('${sheet.temp_id}');" title="Remove Sheet from Staging">
                    <i class="fa fa-trash-o"></i>
                </button>
            </td>
        `;

        if (!existingRow) {
            stagingTbody.appendChild(row);
        }
        reindexStagedRows();
    }

    // Reindex rows # column
    function reindexStagedRows() {
        const rows = stagingTbody.querySelectorAll('tr[id^="staged-row-"]');
        rows.forEach((r, idx) => {
            const idxCell = r.querySelector('.staged-index');
            if (idxCell) idxCell.textContent = (idx + 1);
        });
    }

    // Update Staging summary counters & enable/disable Final Submit button
    function updateStagingCounters() {
        const total = stagedSheets.size;
        let matched = 0;
        let unmatched = 0;
        let selectedCount = 0;
        let sumMarks = 0;

        stagedSheets.forEach(s => {
            if (s.student?.is_matched) matched++;
            else unmatched++;
            if (s.selected !== false) selectedCount++;
            sumMarks += parseFloat(s.evaluation?.obtained_marks || 0);
        });

        countStagedTotal.textContent = total;
        countStagedMatched.textContent = matched;
        countStagedUnmatched.textContent = unmatched;
        btnSaveCount.textContent = selectedCount;

        if (selectedCount > 0 && !isProcessingBatch) {
            btnFinalSubmit.disabled = false;
            btnFinalSubmit.className = 'btn btn-success btn-sm px-3 font-weight-bold';
        } else {
            btnFinalSubmit.disabled = true;
            btnFinalSubmit.className = 'btn btn-secondary btn-sm px-3';
        }

        btnClearStaged.disabled = (total === 0 || isProcessingBatch);

        if (total > 0) {
            const avg = (sumMarks / total).toFixed(2);
            stagingSummaryMarks.innerHTML = `Staged Average Score: <span class="text-success font-weight-bold">${avg} Marks</span>`;
        } else {
            stagingSummaryMarks.innerHTML = '';
            if (!stagingTbody.querySelector('#staging-empty-row')) {
                stagingTbody.innerHTML = `
                    <tr id="staging-empty-row">
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fa fa-file-image-o mr-1" style="font-size: 18px; color: #94a3b8; display: block; margin-bottom: 4px;"></i>
                            No sheets staged yet. Upload OMR sheets above to view extracted data and verify before final submit.
                        </td>
                    </tr>
                `;
            }
        }
    }

    // Toggle individual sheet selection
    window.toggleSheetSelect = function (tempId, isSelected) {
        const sheet = stagedSheets.get(tempId);
        if (sheet) {
            sheet.selected = isSelected;
            updateStagingCounters();
        }
    };

    // Toggle Select All
    window.toggleSelectAllStaged = function (masterCheckbox) {
        const isChecked = masterCheckbox.checked;
        stagedSheets.forEach(s => {
            s.selected = isChecked;
        });
        stagingTbody.querySelectorAll('.staged-checkbox').forEach(cb => {
            cb.checked = isChecked;
        });
        updateStagingCounters();
    };

    // Remove single sheet from staging
    window.removeStagedSheet = function (tempId) {
        stagedSheets.delete(tempId);
        document.getElementById(`staged-row-${tempId}`)?.remove();
        reindexStagedRows();
        updateStagingCounters();
    };

    // Clear all staged sheets
    window.clearAllStaged = function () {
        if (!confirm('Are you sure you want to clear all staged sheets? (Unsaved extractions will be discarded).')) return;
        stagedSheets.clear();
        stagingTbody.innerHTML = '';
        updateStagingCounters();
    };

    // Real-time roll number change & auto-lookup
    window.handleRollNumberChange = async function (tempId, newRoll) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;
        sheet.roll_number = newRoll.trim();

        const opt = getSelectedExamOption();
        const classTypeId = opt ? (opt.dataset.classid || 0) : 0;
        const cell = document.getElementById(`student-cell-${tempId}`);
        const row = document.getElementById(`staged-row-${tempId}`);

        if (cell) cell.innerHTML = '<span class="text-muted small"><i class="fa fa-spinner fa-spin mr-1"></i> Looking up student...</span>';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const res = await fetch("{{ url('upload_omr/lookup_student') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    roll_number: sheet.roll_number,
                    class_type_id: classTypeId
                })
            });
            const data = await res.json();
            if (data.status === 'success') {
                sheet.student = data.student;
                if (data.matched) {
                    if (row) row.className = '';
                    if (cell) {
                        cell.innerHTML = `
                            <div>
                                <span class="badge-pill-status badge-matched">
                                    <i class="fa fa-user"></i> ${escapeHtml(data.student.name)}
                                </span>
                                <div class="text-muted small mt-1 font-weight-bold">
                                    Class: ${escapeHtml(data.student.class_name || 'N/A')} ${data.student.father_name ? '| S/O ' + escapeHtml(data.student.father_name) : ''}
                                </div>
                            </div>
                        `;
                    }
                } else {
                    if (row) row.className = 'row-unmatched';
                    if (cell) {
                        cell.innerHTML = `
                            <div>
                                <span class="badge-pill-status badge-unmatched">
                                    <i class="fa fa-exclamation-triangle"></i> Unmatched Roll #
                                </span>
                                <div class="text-danger small font-italic mt-1">Check roll number or link student</div>
                            </div>
                        `;
                    }
                }
                updateStagingCounters();
            }
        } catch (e) {
            console.error('Lookup failed', e);
        }
    };

    // Open Modal: Annotated Sheet Inspection
    window.viewAnnotatedModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const src = sheet.annotated_image_base64 || sheet.image_url;
        modalAnnotatedImg.src = src;
        modalAnnotatedInfo.innerHTML = `
            <b>File:</b> ${escapeHtml(sheet.source_filename)} &bull; 
            <b>Roll #:</b> ${escapeHtml(sheet.roll_number || 'N/A')} &bull; 
            <b>Student:</b> ${escapeHtml(sheet.student?.name || 'Unregistered')} &bull; 
            <b>Score:</b> ${sheet.evaluation?.obtained_marks || 0} / ${sheet.evaluation?.total_marks || 0} (${sheet.evaluation?.percentage || 0}%)
        `;

        if (typeof $ !== 'undefined' && $('#modalAnnotatedSheet').modal) {
            $('#modalAnnotatedSheet').modal('show');
        } else {
            const modalEl = document.getElementById('modalAnnotatedSheet');
            if (modalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modalEl).show();
            }
        }
    };

    // Open Modal: Question-by-Question Matrix
    window.viewAnswersModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const answers = sheet.evaluation?.extracted_answers || [];
        modalAnsStudent.textContent = sheet.student?.name || 'Unregistered Student';
        modalAnsRoll.textContent = sheet.roll_number || 'N/A';
        modalAnsScore.textContent = `${sheet.evaluation?.obtained_marks || 0} / ${sheet.evaluation?.total_marks || 0} (${sheet.evaluation?.percentage || 0}%)`;

        let html = '';
        answers.forEach(item => {
            let resBadge = '';
            if (item.is_correct) {
                resBadge = '<span class="badge-pill-status badge-matched"><i class="fa fa-check"></i> Correct</span>';
            } else if (item.status === 'blank' || !item.answer) {
                resBadge = '<span class="badge-pill-status badge-staged">Left Blank</span>';
            } else {
                resBadge = '<span class="badge-pill-status badge-unmatched"><i class="fa fa-times"></i> Wrong</span>';
            }

            html += `
                <tr>
                    <td class="font-weight-bold text-dark">Q${item.question}</td>
                    <td class="text-center font-mono font-weight-bold ${item.is_correct ? 'text-success' : (item.answer ? 'text-danger' : 'text-muted')}">${item.answer || '—'}</td>
                    <td class="text-center font-mono font-weight-bold text-primary">${item.correct_answer || '—'}</td>
                    <td class="text-right">${resBadge}</td>
                </tr>
            `;
        });
        modalAnswersTbody.innerHTML = html;

        if (typeof $ !== 'undefined' && $('#modalAnswersGrid').modal) {
            $('#modalAnswersGrid').modal('show');
        } else {
            const modalEl = document.getElementById('modalAnswersGrid');
            if (modalEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Modal(modalEl).show();
            }
        }
    };

    // FINAL SUBMIT: Commit all staged and selected sheets into database & exam marks
    window.confirmFinalSubmit = async function () {
        const examId = examSelect.value;
        if (!examId) {
            alert('Please select an examination.');
            return;
        }

        const selectedSheets = [];
        stagedSheets.forEach(s => {
            if (s.selected !== false) {
                selectedSheets.push(s);
            }
        });

        if (!selectedSheets.length) {
            alert('No sheets selected for final submission.');
            return;
        }

        const examName = getSelectedExamOption()?.dataset.name || 'this examination';
        const msg = `You are about to save ${selectedSheets.length} evaluated OMR sheet(s) and upload student marks directly to "${examName}".\n\nAre you sure you want to proceed?`;
        if (!confirm(msg)) return;

        btnFinalSubmit.disabled = true;
        btnFinalSubmit.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving marks...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch("{{ url('upload_omr/finalize_save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    exam_id: parseInt(examId, 10),
                    sheets: selectedSheets
                }),
                credentials: 'same-origin'
            });

            const data = await response.json();
            if (!response.ok || data.status === 'error') {
                throw new Error(data.message || 'Failed to save final evaluation records.');
            }

            alert(`✅ ${data.message}`);

            // Prepend saved rows to Saved Ledger below
            const savedTbody = document.getElementById('omr-saved-tbody');
            document.getElementById('omr-saved-empty-row')?.remove();

            selectedSheets.forEach(s => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="${escapeHtml(s.source_filename)}">
                        <i class="fa fa-file-image-o text-muted mr-1"></i> ${escapeHtml(s.source_filename)}
                    </td>
                    <td>
                        <span class="font-mono font-weight-bold text-primary">${escapeHtml(s.roll_number)}</span>
                        <div class="text-muted small font-weight-bold">${escapeHtml(s.student?.name || 'Unregistered')}</div>
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark">${escapeHtml(examName)}</span>
                    </td>
                    <td class="text-center font-weight-bold text-success">${s.evaluation?.correct_answers || 0}</td>
                    <td class="text-center font-weight-bold text-danger">${s.evaluation?.incorrect_answers || 0}</td>
                    <td class="text-center text-muted">${s.evaluation?.blank_answers || 0}</td>
                    <td class="font-mono font-weight-bold">${s.evaluation?.total_marks || 0}</td>
                    <td class="font-mono font-weight-bold text-success">${s.evaluation?.obtained_marks || 0}</td>
                    <td>
                        <span class="badge-pill-status badge-matched">${s.evaluation?.percentage || 0}%</span>
                    </td>
                    <td class="text-center font-weight-bold text-primary">#1</td>
                    <td class="text-right">
                        <span class="badge-pill-status badge-saved">
                            <i class="fa fa-check-circle"></i> Saved
                        </span>
                    </td>
                `;
                savedTbody.prepend(tr);

                // Remove from memory
                stagedSheets.delete(s.temp_id);
                document.getElementById(`staged-row-${s.temp_id}`)?.remove();
            });

            // Update KPIs
            if (kpiTodayScans) {
                const currentToday = parseInt(kpiTodayScans.textContent.replace(/\D/g, '') || '0', 10);
                kpiTodayScans.textContent = (currentToday + selectedSheets.length);
            }
            if (kpiTotalScans) {
                const currentTotal = parseInt(kpiTotalScans.textContent.replace(/\D/g, '') || '0', 10);
                kpiTotalScans.textContent = (currentTotal + selectedSheets.length);
            }

            reindexStagedRows();
            updateStagingCounters();
        } catch (err) {
            alert(`❌ Error saving marks: ${err.message}`);
        } finally {
            btnFinalSubmit.disabled = false;
            btnFinalSubmit.innerHTML = `<i class="fa fa-check-circle mr-1"></i> Final Submit & Save Marks (${stagedSheets.size})`;
            updateStagingCounters();
        }
    };

    // Helper: HTML escape
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endsection

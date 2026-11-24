@extends('layout.app')

@section('title', 'Upload & Evaluate OMR Sheets - AI Extractor - Arise ERP')

@php
    $classType = $classType ?? Helper::classType();
    $batches = $batches ?? collect();
    $search = $search ?? [];
    $requestedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedBatchId = (int) ($search['batch_id'] ?? ($selectedBatchId ?? 0));
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $allExams = $allExams ?? collect();
    $selectedExam = $selectedExam ?? null;
    $students = $students ?? collect();
    $existingMarksMap = $existingMarksMap ?? collect();
    $existingOmrMap = $existingOmrMap ?? collect();
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
   - High contrast, clean white surfaces, crystal clear font visibility
   - Neutralize common.css dark overrides
   ========================================================================== */

/* 0. Neutralize common.css dark poisoning for .bg-light and card footers */
.omr-page .bg-light,
.omr-page-layout .bg-light,
.omr-card .bg-light,
.modal .bg-light {
    background-color: #f8fafc !important;
    color: #1e293b !important;
}

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
    border-radius: 3px;
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
    border-radius: 3px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    border-left: 3px solid #002C54;
    transition: all 0.15s ease;
}
.kpi-mini-card.kpi-classes { border-left-color: #0284c7; }
.kpi-mini-card.kpi-published { border-left-color: #16a34a; }
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
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}
.omr-card-header {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 9px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.omr-card-title {
    font-size: 13px;
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
    padding: 10px 14px;
    background: #ffffff;
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
    background: #f8fafc;
    border-color: #cbd5e1;
}

/* Staging Table & Ledger Table */
.omr-table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-size: 11.5px !important;
    background: #ffffff !important;
}
.omr-table th {
    background: #002C54 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 10.5px !important;
    text-transform: uppercase !important;
    letter-spacing: .04em !important;
    padding: 8px 10px !important;
    border: 1px solid #002C54 !important;
    vertical-align: middle !important;
}
.omr-table td {
    background: #ffffff !important;
    color: #0f172a !important;
    padding: 7px 10px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    border-right: 1px solid #f1f5f9 !important;
    vertical-align: middle !important;
}
.omr-table tbody tr:nth-child(even) td {
    background: #f8fafc !important;
}
.omr-table tbody tr:hover td {
    background: #f1f5f9 !important;
}
.omr-table tbody tr.row-unmatched td {
    background: #fffdf0 !important;
    border-top: 1px solid #fef08a !important;
    border-bottom: 1px solid #fef08a !important;
}

/* Thumbnail preview */
.omr-thumb {
    width: 38px;
    height: 38px;
    object-fit: cover;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: transform 0.15s ease;
    background: #ffffff;
}
.omr-thumb:hover {
    transform: scale(1.15);
    border-color: #0284c7;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* Roll number input */
.roll-input {
    background: #ffffff !important;
    color: #002C54 !important;
    border: 1.5px solid #94a3b8 !important;
    border-radius: 3px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    height: 28px !important;
    padding: 2px 8px !important;
    box-shadow: none !important;
}
.roll-input:focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2) !important;
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
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
}
.badge-matched {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac !important;
}
.badge-unmatched {
    background: #fef08a !important;
    color: #854d0e !important;
    border: 1px solid #facc15 !important;
}
.badge-saved {
    background: #16a34a !important;
    color: #ffffff !important;
}

/* Action Buttons Styling */
#btn-final-submit {
    background: #16a34a !important;
    border: 1px solid #15803d !important;
    color: #ffffff !important;
    font-size: 12px !important;
    font-weight: 700 !important;
    height: 32px !important;
    padding: 4px 14px !important;
    border-radius: 3px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
}
#btn-final-submit:hover:not([disabled]) {
    background: #15803d !important;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.4) !important;
}
#btn-final-submit[disabled], #btn-final-submit:disabled {
    background: #94a3b8 !important;
    border-color: #94a3b8 !important;
    color: #f1f5f9 !important;
    opacity: 0.65 !important;
    cursor: not-allowed !important;
    box-shadow: none !important;
}

#btn-clear-staged {
    background: #ffffff !important;
    border: 1px solid #ef4444 !important;
    color: #dc2626 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    height: 32px !important;
    padding: 4px 12px !important;
    border-radius: 3px !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
}
#btn-clear-staged:hover:not([disabled]) {
    background: #fee2e2 !important;
    color: #991b1b !important;
}
#btn-clear-staged[disabled], #btn-clear-staged:disabled {
    border-color: #cbd5e1 !important;
    color: #94a3b8 !important;
    background: #f8fafc !important;
    opacity: 0.6 !important;
    cursor: not-allowed !important;
}

/* Staging Footer Bar */
.omr-staging-footer {
    background: #ffffff !important;
    border-top: 1px solid #cbd5e1 !important;
    padding: 9px 14px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    font-size: 11.5px !important;
    color: #334155 !important;
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
    background: linear-gradient(90deg, #0284c7, #16a34a);
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
                <div style="color: #16a34a; font-size: 20px;">
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
                    <div class="kpi-value" style="color: #16a34a; font-size: 14px;">
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
                            <p class="omr-card-desc">Answer key, question count, and student roster will follow this exam.</p>
                        </div>
                        <span class="badge badge-primary px-2 py-1" style="font-size: 10px; background: #002C54;">Step 1</span>
                    </div>
                    <div class="omr-card-body py-2">
                        <div class="row g-2 mb-2">
                            {{-- 1. Target Course / Class --}}
                            <div class="col-sm-6">
                                <label for="omr-class-select" class="font-weight-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                    Target Course / Class <span class="text-danger">*</span>
                                </label>
                                <select id="omr-class-select" class="form-control form-control-sm" style="font-weight: 600; font-size: 12px; height: 32px; border-color: #cbd5e1; background: #ffffff;">
                                    <option value="">-- Choose Course / Class --</option>
                                    @foreach($classType as $ct)
                                        <option value="{{ $ct->id }}" {{ $requestedClassId == $ct->id ? 'selected' : '' }}>
                                            {{ $ct->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Target Batch --}}
                            <div class="col-sm-6">
                                <label for="omr-batch-select" class="font-weight-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                    Target Batch
                                </label>
                                <select id="omr-batch-select" class="form-control form-control-sm" style="font-weight: 600; font-size: 12px; height: 32px; border-color: #cbd5e1; background: #ffffff;">
                                    <option value="all" {{ empty($selectedBatchId) ? 'selected' : '' }}>-- All Batches --</option>
                                    @foreach($batches as $b)
                                        <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                                            {{ $b->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Examination (Full Width so Code & Name Fit Fully) --}}
                            <div class="col-12 mt-1">
                                <label for="omr-exam-select" class="font-weight-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase;">
                                    Examination <span class="text-danger">*</span>
                                </label>
                                <select id="omr-exam-select" class="form-control form-control-sm" style="font-weight: 600; font-size: 12px; height: 32px; border-color: #cbd5e1; background: #ffffff;">
                                    <option value="">-- Choose Examination --</option>
                                    @foreach($allExams as $ex)
                                        @php
                                            $qCount = (int) ($ex->number_of_questions ?: 50);
                                            $correctM = (float) ($ex->marks_per_correct ?: 1.0);
                                            $incorrectM = (float) ($ex->marks_per_incorrect ?: 0.0);
                                            $keyCount = is_array($ex->answer_key) ? count($ex->answer_key) : 0;
                                            $eCode = $ex->code ?: ($ex->exam_code ?: 'EXAM-'.$ex->id);
                                            $batchName = $ex->batch ? $ex->batch->name : 'All Batches';
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
                                            data-batchid="{{ $ex->batch_id ?: 0 }}"
                                            data-batchname="{{ $batchName }}"
                                            data-keycount="{{ $keyCount }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $eCode }} — {{ $ex->name }} ({{ $qCount }} Q &bull; {{ $batchName }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Selected Exam Info Pill --}}
                        <div id="omr-selected-exam-badge" class="{{ $selectedExam ? '' : 'd-none' }} mt-2 p-2 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11px;">
                            <div class="d-flex justify-content-between align-items-center font-weight-bold">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                    <span id="omr-badge-title" class="text-dark font-weight-bold" style="font-size: 12.5px;">{{ $selectedExam ? $selectedExam->name : '' }}</span>
                                    <span id="omr-badge-batch" class="badge badge-primary px-2 py-1" style="font-size: 9.5px; background: #002C54;">
                                        <i class="fa fa-users mr-1"></i>{{ $selectedExam && $selectedExam->batch ? $selectedExam->batch->name : (!empty($selectedBatchId) ? ($batches->firstWhere('id', $selectedBatchId)->name ?? 'Selected Batch') : 'All Batches') }}
                                    </span>
                                </div>
                                <span id="omr-badge-code" class="font-mono text-primary font-weight-bold" style="font-size: 12px;">{{ $selectedExam ? ($selectedExam->code ?: $selectedExam->exam_code) : '' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted mt-1" style="font-size: 10.5px;">
                                <span id="omr-badge-questions">
                                    <i class="fa fa-list-ol mr-1"></i> {{ $selectedExam ? ($selectedExam->number_of_questions ?: 50) . ' Questions' : '' }}
                                    @if($selectedExam && $selectedExam->exam_maximum_marks)
                                        &bull; Max: <b>{{ number_format($selectedExam->exam_maximum_marks, 2) }}</b> M
                                    @endif
                                </span>
                                @php
                                    $posMarks = $selectedExam ? (float)($selectedExam->marks_per_correct ?: 1.0) : 1.0;
                                    $negMarks = $selectedExam ? abs((float)($selectedExam->marks_per_incorrect ?: 0.0)) : 0.0;
                                @endphp
                                <span id="omr-badge-scheme" class="font-weight-bold text-success">
                                    <i class="fa fa-tag mr-1"></i> Marks: +{{ number_format($posMarks, 2) }} / -{{ number_format($negMarks, 2) }}
                                </span>
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

                        <div id="omr-status-box" class="mt-2 py-1 px-2 rounded font-weight-bold" style="{{ $selectedExam ? 'background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534;' : 'background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;' }} font-size: 11px;">
                            <i class="fa {{ $selectedExam ? 'fa-check-circle text-success' : 'fa-info-circle' }} mr-1"></i> 
                            <span id="omr-status-text">
                                @if($selectedExam)
                                    Target Exam: <b>{{ $selectedExam->name }}</b> ({{ $selectedExam->number_of_questions ?: 50 }} Q &bull; Code: {{ $selectedExam->code ?: ($selectedExam->exam_code ?: 'EXAM-'.$selectedExam->id) }}). <b>{{ $students->count() }}</b> enrolled student(s) loaded. Ready to upload OMR sheets.
                                @else
                                    Select an examination to enable sheet upload.
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- 4. Staging / Preview Workspace (THE CORE REQUIREMENT)     --}}
        {{-- ======================================================== --}}
        <div class="omr-card mt-1">
            <div class="omr-card-header" style="background: #ffffff; border-bottom: 2px solid #002C54; padding: 10px 14px;">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <h2 class="omr-card-title" style="font-size: 14px; font-weight: 700; color: #002C54;">
                        <i class="fa fa-users text-primary"></i> 3. Class Enrolled Students &amp; OMR Verification Matrix
                    </h2>
                    <span class="badge badge-light px-2 py-1 border" id="badge-enrolled-total" style="font-size: 11px; background: #ffffff; color: #0f172a; border-color: #cbd5e1;">
                        Enrolled: <b id="count-enrolled-total">{{ $students->count() }}</b>
                    </span>
                    <span class="badge badge-success px-2 py-1" id="badge-staged-matched" style="font-size: 11px; background: #16a34a;">
                        Scanned: <b id="count-staged-matched">{{ $existingOmrMap->count() }}</b>
                    </span>
                    <span class="badge badge-secondary px-2 py-1" id="badge-staged-pending" style="font-size: 11px; background: #64748b;">
                        Pending: <b id="count-staged-pending">{{ max(0, $students->count() - $existingOmrMap->count()) }}</b>
                    </span>
                    <span class="badge badge-warning px-2 py-1 text-dark" id="badge-staged-unmatched" style="font-size: 11px; background: #facc15;">
                        Needs Review: <b id="count-staged-unmatched">0</b>
                    </span>
                </div>
                
                {{-- Final Submit & Clear Buttons --}}
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" id="btn-clear-staged" class="btn btn-sm" disabled title="Clear scanned extractions">
                        <i class="fa fa-trash-o"></i> Clear Scans
                    </button>
                    <button type="button" id="btn-final-submit" class="btn btn-sm" disabled title="Final Submit and Commit marks to exam">
                        <i class="fa fa-check-circle"></i> Final Submit &amp; Save Marks (<span id="btn-save-count">0</span>)
                    </button>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="omr-table" id="omr-staging-table">
                    <thead>
                        <tr>
                            <th style="width: 34px; text-align: center;">
                                <input type="checkbox" id="staged-select-all" title="Select / Deselect all scanned">
                            </th>
                            <th style="width: 40px; text-align: center;">#</th>
                            <th style="min-width: 170px;">Enrolled Student Details</th>
                            <th style="min-width: 100px; text-align: center;">Roll # / Adm</th>
                            <th style="width: 55px; text-align: center;">Sheet</th>
                            <th style="min-width: 130px;">File Name</th>
                            <th style="min-width: 110px; text-align: center;">Detected Roll #</th>
                            <th style="min-width: 110px; text-align: center;">Questions</th>
                            <th style="min-width: 130px; text-align: center;">Score &amp; Marks</th>
                            <th style="min-width: 100px; text-align: center;">Status</th>
                            <th style="min-width: 100px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="omr-staging-tbody">
                        @if($students->isNotEmpty())
                            @php $sIdx = 1; @endphp
                            @foreach($students as $stu)
                                @php
                                    $hasOmr = isset($existingOmrMap[$stu->id]);
                                    $hasMarks = isset($existingMarksMap[$stu->id]);
                                    $omrRecord = $hasOmr ? $existingOmrMap[$stu->id] : null;
                                    $markRecord = $hasMarks ? $existingMarksMap[$stu->id] : null;
                                    $displayRoll = $stu->roll_no ?: ($stu->admissionNo ?: $stu->id);
                                    $rollClean = strtolower(trim((string)$displayRoll));
                                    $admClean = strtolower(trim((string)$stu->admissionNo));
                                    $cleanNumeric = preg_replace('/\D/', '', (string)$displayRoll);
                                    $isDone = $hasOmr || $hasMarks;
                                @endphp
                                <tr id="student-row-{{ $stu->id }}" 
                                    class="student-omr-row {{ $isDone ? 'table-success row-scanned' : '' }}" 
                                    data-student-id="{{ $stu->id }}" 
                                    data-batch-id="{{ $stu->batch_id }}"
                                    data-batch-name="{{ $stu->batch ? $stu->batch->name : '' }}"
                                    data-roll-no="{{ $rollClean }}" 
                                    data-admission-no="{{ $admClean }}" 
                                    data-numeric-roll="{{ $cleanNumeric }}"
                                    data-exam-roll-no="{{ strtolower(trim((string)$stu->exam_roll_no)) }}"
                                    data-student-name="{{ $stu->first_name }} {{ $stu->last_name }}">
                                    <td class="text-center" style="vertical-align: middle;">
                                        <input type="checkbox" class="staged-checkbox" data-student-id="{{ $stu->id }}" {{ $isDone ? 'checked' : '' }} onchange="toggleStudentSelect('{{ $stu->id }}', this.checked);">
                                    </td>
                                    <td class="text-center font-weight-bold text-muted staged-index" style="vertical-align: middle;">{{ $sIdx++ }}</td>
                                    
                                    {{-- Enrolled Student Details --}}
                                    <td style="vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-2">
                                                @if(!empty($stu->image))
                                                    <img src="{{ asset($stu->image) }}" class="rounded-circle border" style="width: 28px; height: 28px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-light border text-center font-weight-bold text-muted d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">
                                                        {{ strtoupper(substr($stu->first_name ?: 'S', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 12.5px; line-height: 1.2;">
                                                    {{ $stu->first_name }} {{ $stu->last_name }}
                                                </div>
                                                <div class="text-muted small" style="font-size: 10.5px;">
                                                    Adm: <b>{{ $stu->admissionNo ?: 'N/A' }}</b>
                                                    @if($stu->batch)
                                                        &bull; <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 9.5px; padding: 1px 5px;"><i class="fa fa-users mr-1"></i>{{ $stu->batch->name }}</span>
                                                    @endif
                                                    @if(!empty($stu->father_name))
                                                        &bull; S/O {{ $stu->father_name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Enrolled Roll / Admission --}}
                                    <td class="text-center" style="vertical-align: middle;">
                                        <span class="badge badge-light border text-dark font-mono font-weight-bold" style="font-size: 11px; padding: 2px 6px;">
                                            {{ $displayRoll }}
                                        </span>
                                    </td>

                                    {{-- Sheet / Image Column --}}
                                    <td class="text-center cell-sheet" style="vertical-align: middle;">
                                        @php
                                            $sheetUrl = null;
                                            if ($hasOmr) {
                                                $sheetUrl = $omrRecord->image_url;
                                                if (!$sheetUrl && !empty($omrRecord->stored_file)) {
                                                    $sheetUrl = asset($omrRecord->stored_file);
                                                }
                                                if (!$sheetUrl && !empty($omrRecord->source_filename)) {
                                                    $sheetUrl = file_exists(public_path('uploads/omr_scans/' . $omrRecord->source_filename))
                                                        ? asset('uploads/omr_scans/' . $omrRecord->source_filename)
                                                        : asset($omrRecord->source_filename);
                                                }
                                            }
                                        @endphp
                                        @if($hasOmr && !empty($sheetUrl))
                                            <img src="{{ $sheetUrl }}" 
                                                 class="omr-thumb" 
                                                 alt="Sheet" 
                                                 onclick="viewSavedOmrModal('{{ $omrRecord->id }}', this);" 
                                                 data-image-url="{{ $sheetUrl }}"
                                                 data-student-name="{{ $stu->first_name }} {{ $stu->last_name }}"
                                                 data-roll="{{ $displayRoll }}"
                                                 data-score="+{{ $omrRecord->obtained_marks }} / {{ $omrRecord->total_marks }} ({{ $omrRecord->percentage }}%)"
                                                 data-filename="{{ $omrRecord->source_filename ?: 'omr_sheet.jpg' }}"
                                                 title="Click to view sheet preview"
                                                 style="cursor: pointer;">
                                        @elseif($hasOmr)
                                            <div class="sheet-placeholder text-primary" style="font-size: 10px; cursor: pointer;" onclick="viewSavedOmrModal('{{ $omrRecord->id }}', this);" title="Click to view sheet preview">
                                                <i class="fa fa-file-image-o d-block mb-1 text-primary" style="font-size: 14px;"></i>
                                                <span class="badge badge-light border" style="font-size: 9px;">Sheet</span>
                                            </div>
                                        @else
                                            <div class="sheet-placeholder text-muted" style="font-size: 10px; opacity: 0.6;">
                                                <i class="fa fa-file-image-o d-block mb-1"></i> Waiting
                                            </div>
                                        @endif
                                    </td>

                                    {{-- File Name Column --}}
                                    <td class="cell-filename text-truncate" style="max-width: 130px; vertical-align: middle;" title="{{ $hasOmr ? $omrRecord->source_filename : 'Awaiting Scan' }}">
                                        <span class="file-name-text {{ $hasOmr ? 'text-dark font-weight-bold' : 'text-muted' }}" style="font-size: 11.5px;">
                                            <i class="fa {{ $hasOmr ? 'fa-file-image-o text-primary' : 'fa-clock-o text-muted' }} mr-1"></i>
                                            {{ $hasOmr ? ($omrRecord->source_filename ?: 'omr_scan.jpg') : 'Pending Scan' }}
                                        </span>
                                    </td>

                                    {{-- Detected Roll # Column --}}
                                    <td class="cell-detected-roll text-center" style="vertical-align: middle;">
                                        @if($hasOmr)
                                            <input type="text" class="form-control form-control-sm font-mono text-center roll-input" value="{{ $omrRecord->unique_id ?: $displayRoll }}" style="width: 95px; font-weight: 700; height: 28px; font-size: 12px; margin: 0 auto;" readonly>
                                        @else
                                            <span class="text-muted small font-mono detected-roll-placeholder">--</span>
                                        @endif
                                    </td>

                                    {{-- Questions Column --}}
                                    <td class="text-center cell-questions" style="vertical-align: middle;">
                                        @if($hasOmr)
                                            <div class="font-weight-bold text-dark" style="font-size: 12px;">{{ ($omrRecord->correct_answers + $omrRecord->incorrect_answers) }} / {{ $omrRecord->total_questions ?? ($selectedExam->number_of_questions ?? 50) }}</div>
                                            <div class="small font-weight-bold" style="font-size: 10px;">🟢 {{ $omrRecord->correct_answers }} 🔴 {{ $omrRecord->incorrect_answers }} ⚪ {{ $omrRecord->blank_answers }}</div>
                                        @else
                                            <span class="text-muted small">-- / {{ $selectedExam ? ($selectedExam->number_of_questions ?: 50) : 50 }}</span>
                                        @endif
                                    </td>

                                    {{-- Score & Marks Column --}}
                                    <td class="text-center cell-score" style="vertical-align: middle;">
                                        @if($hasOmr)
                                            <div>
                                                <span class="font-weight-bold text-success font-size-13">+{{ $omrRecord->obtained_marks }}</span>
                                                <span class="text-muted small"> / {{ $omrRecord->total_marks }}</span>
                                                <span class="badge badge-success ml-1 font-weight-bold" style="font-size: 9.5px;">{{ $omrRecord->percentage }}%</span>
                                            </div>
                                            @if(!empty($omrRecord->subject_breakdown) && is_array($omrRecord->subject_breakdown))
                                                <div class="d-flex flex-wrap justify-content-center mt-1" style="gap: 3px;">
                                                    @foreach($omrRecord->subject_breakdown as $sb)
                                                        @php $sShort = explode(' ', $sb['subject_name'] ?? 'Sub')[0]; @endphp
                                                        <span class="badge" style="font-size: 8.5px; padding: 1px 4px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;" title="{{ $sb['subject_name'] ?? '' }}: {{ $sb['obtained_marks'] ?? 0 }}/{{ $sb['max_marks'] ?? 0 }}">
                                                            {{ $sShort }}: +{{ $sb['obtained_marks'] ?? 0 }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @elseif($hasMarks)
                                            <div>
                                                <span class="font-weight-bold text-success font-size-13">+{{ $markRecord->student_marks }}</span>
                                                <span class="text-muted small"> / {{ $markRecord->exam_maximum_marks ?: ($selectedExam->exam_maximum_marks ?? 100) }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted font-weight-bold" style="color: #94a3b8;">-- / {{ $selectedExam ? ($selectedExam->exam_maximum_marks ?: 100) : 100 }}</span>
                                        @endif
                                    </td>

                                    {{-- Status Column --}}
                                    <td class="text-center cell-status" style="vertical-align: middle;">
                                        @if($hasOmr || $hasMarks)
                                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #16a34a !important; color: #ffffff !important; font-size: 10.5px;">
                                                <i class="fa fa-check-circle mr-1"></i> Saved
                                            </span>
                                        @else
                                            <span class="badge badge-secondary px-2 py-1 font-weight-bold status-badge" style="background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1 !important; font-size: 10.5px;">
                                                <i class="fa fa-clock-o mr-1"></i> Pending Scan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions Column --}}
                                    <td class="text-right cell-actions" style="vertical-align: middle; white-space: nowrap;">
                                        @if($hasOmr)
                                            <button type="button" 
                                                    class="btn btn-xs btn-outline-primary mr-1" 
                                                    style="padding: 2px 7px; font-size: 11px;" 
                                                    onclick="viewSavedOmrModal('{{ $omrRecord->id }}', this);" 
                                                    data-image-url="{{ $sheetUrl }}"
                                                    data-student-name="{{ $stu->first_name }} {{ $stu->last_name }}"
                                                    data-roll="{{ $displayRoll }}"
                                                    data-score="+{{ $omrRecord->obtained_marks }} / {{ $omrRecord->total_marks }} ({{ $omrRecord->percentage }}%)"
                                                    data-filename="{{ $omrRecord->source_filename ?: 'omr_sheet.jpg' }}"
                                                    title="Inspect OMR Scan">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-xs btn-outline-info mr-1" 
                                                    style="padding: 2px 7px; font-size: 11px;" 
                                                    onclick="viewSavedAnswersModal('{{ $omrRecord->id }}');" 
                                                    title="Question-by-Question Matrix">
                                                <i class="fa fa-list"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small pr-2">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="staging-empty-row">
                                <td colspan="11" class="text-center py-4 text-muted" style="background: #ffffff !important;">
                                    <i class="fa fa-users mr-1" style="font-size: 20px; color: #94a3b8; display: block; margin-bottom: 6px;"></i>
                                    <span style="font-size: 12px; font-weight: 600; color: #64748b;">No students found for this class. Select another class or exam above.</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Crisp, high-contrast footer notice --}}
            <div class="omr-staging-footer">
                <div style="color: #334155 !important; font-size: 11.5px !important; display: flex; align-items: center; gap: 6px;">
                    <i class="fa fa-info-circle text-primary" style="font-size: 14px;"></i>
                    <span><b>Notice:</b> Extracted data is currently staged in preview. Marks will <u>ONLY</u> be saved to student exam records after you click <b>"Final Submit &amp; Save Marks"</b>.</span>
                </div>
                <div id="staging-summary-marks" style="color: #0f172a !important; font-weight: 700 !important; font-size: 12px !important;"></div>
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
                            <tr id="scan-row-{{ $scan->id }}" data-unique-id="{{ $scan->unique_id }}">
                                <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="{{ $scan->source_filename }}">
                                    <div class="d-flex align-items-center">
                                        @if(!empty($scan->image_url))
                                            <img src="{{ $scan->image_url }}" 
                                                 class="omr-thumb mr-2" 
                                                 alt="Sheet" 
                                                 onclick="viewSavedOmrModal('{{ $scan->id }}', this);" 
                                                 data-image-url="{{ $scan->image_url }}"
                                                 data-student-name="{{ $stName }}"
                                                 data-roll="{{ $scan->unique_id }}"
                                                 data-score="+{{ $scan->obtained_marks }} / {{ $scan->total_marks }} ({{ $scan->percentage }}%)"
                                                 data-filename="{{ $scan->source_filename }}"
                                                 title="Click to view sheet preview" 
                                                 style="width: 26px; height: 26px; object-fit: cover; border-radius: 3px; cursor: pointer; flex-shrink: 0;">
                                        @else
                                            <i class="fa fa-file-image-o text-muted mr-1"></i>
                                        @endif
                                        <span class="text-truncate">{{ $scan->source_filename ?: 'omr_scan.png' }}</span>
                                    </div>
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
                                <td class="font-mono font-weight-bold text-success">
                                    {{ number_format($scan->obtained_marks, 2) }}
                                    @if(!empty($scan->subject_breakdown) && is_array($scan->subject_breakdown))
                                        <div class="d-flex flex-wrap justify-content-center mt-1" style="gap: 3px;">
                                            @foreach($scan->subject_breakdown as $sb)
                                                @php $sShort = explode(' ', $sb['subject_name'] ?? 'Sub')[0]; @endphp
                                                <span class="badge" style="font-size: 8.5px; padding: 1px 4px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;" title="{{ $sb['subject_name'] ?? '' }}: {{ $sb['obtained_marks'] ?? 0 }}/{{ $sb['max_marks'] ?? 0 }}">
                                                    {{ $sShort }}: +{{ $sb['obtained_marks'] ?? 0 }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-pill-status badge-matched">{{ $scan->percentage }}%</span>
                                </td>
                                <td class="text-center font-weight-bold text-primary">#{{ $scan->rank ?: 1 }}</td>
                                <td class="text-right" style="white-space: nowrap;">
                                    <span class="badge-pill-status badge-saved mr-1" title="Marks recorded in student exam file">
                                        <i class="fa fa-check-circle"></i> Saved
                                    </span>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-primary mr-1" 
                                            style="padding: 2px 6px; font-size: 10px;" 
                                            onclick="viewSavedOmrModal('{{ $scan->id }}', this);" 
                                            data-image-url="{{ $scan->image_url }}"
                                            data-student-name="{{ $stName }}"
                                            data-roll="{{ $scan->unique_id }}"
                                            data-score="+{{ $scan->obtained_marks }} / {{ $scan->total_marks }} ({{ $scan->percentage }}%)"
                                            data-filename="{{ $scan->source_filename }}"
                                            title="Inspect OMR Scan">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-info" 
                                            style="padding: 2px 6px; font-size: 10px;" 
                                            onclick="viewSavedAnswersModal('{{ $scan->id }}');" 
                                            title="Question Matrix">
                                        <i class="fa fa-list"></i>
                                    </button>
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
        <div class="modal-content" style="border-radius: 4px; border: 1px solid #cbd5e1; background: #ffffff;">
            <div class="modal-header py-2 px-3" style="background: #002C54; color: #ffffff;">
                <h5 class="modal-title font-weight-bold" id="modalAnnotatedSheetLabel" style="font-size: 13px; color: #ffffff;">
                    <i class="fa fa-eye mr-1"></i> Annotated OMR Sheet Inspection
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 text-center" style="background: #f8fafc; max-height: 80vh; overflow-y: auto;">
                <img id="modal-annotated-img" src="" alt="Scanned OMR Sheet" style="max-width: 100%; height: auto; border: 1px solid #cbd5e1; box-shadow: 0 2px 10px rgba(0,0,0,0.15); border-radius: 2px; background: #ffffff;" onerror="this.style.display='none'; const noImg = document.getElementById('modal-sheet-no-img'); if(noImg) noImg.style.display='block';" onload="this.style.display='inline-block'; const noImg = document.getElementById('modal-sheet-no-img'); if(noImg) noImg.style.display='none';">
                <div id="modal-sheet-no-img" style="display: none; padding: 40px;" class="text-muted">
                    <i class="fa fa-file-image-o fa-3x mb-2 text-muted" style="opacity: 0.5;"></i>
                    <div class="font-weight-bold">Sheet image scan file not found on server disk</div>
                    <div class="small text-muted">Evaluation answers, score, and subject breakdown are preserved in database.</div>
                </div>
                <div class="mt-2 text-dark font-weight-bold small" id="modal-annotated-info"></div>
            </div>
            <div class="modal-footer py-2 px-3 d-flex justify-content-between" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
                <span class="text-muted small"><i class="fa fa-info-circle text-primary mr-1"></i> Green circles indicate AI-detected bubble fills and registration bounding boxes.</span>
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
        <div class="modal-content" style="border-radius: 4px; border: 1px solid #cbd5e1; background: #ffffff;">
            <div class="modal-header py-2 px-3" style="background: #002C54; color: #ffffff;">
                <h5 class="modal-title font-weight-bold" id="modalAnswersGridLabel" style="font-size: 13px; color: #ffffff;">
                    <i class="fa fa-list-ol mr-1"></i> Extracted Answers Breakdown
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2" style="max-height: 70vh; overflow-y: auto; background: #ffffff;">
                <div class="p-2 mb-2 rounded border d-flex justify-content-between align-items-center" style="font-size: 11.5px; background: #f8fafc; border-color: #e2e8f0;">
                    <div>
                        <span class="text-muted">Student:</span> <b id="modal-ans-student" class="text-dark">--</b>
                        <span class="text-muted ml-2">Roll #:</span> <b class="font-mono text-primary" id="modal-ans-roll">--</b>
                    </div>
                    <div>
                        <span class="text-muted">Score:</span> <b class="text-success font-weight-bold" id="modal-ans-score">--</b>
                    </div>
                </div>

                {{-- Subject-Wise Performance Breakdown Card --}}
                <div class="mb-3 rounded border p-2" id="modal-subject-breakdown-wrapper" style="background: #f8fafc; border-color: #cbd5e1; display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="font-weight-bold" style="font-size: 11.5px; color: #002C54;">
                            <i class="fa fa-sitemap mr-1 text-primary"></i> Subject Section Performance
                        </span>
                        <span class="badge badge-light text-muted" style="font-size: 10px; border: 1px solid #cbd5e1;">Section-Wise Cut-offs</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0" id="modal-subject-breakdown-table" style="font-size: 11px; background: #ffffff;">
                            <thead style="background: #002C54; color: #ffffff;">
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center" style="width: 65px;">Range</th>
                                    <th class="text-center" style="width: 55px;">Right</th>
                                    <th class="text-center" style="width: 55px;">Wrong</th>
                                    <th class="text-center" style="width: 55px;">Left</th>
                                    <th class="text-center" style="width: 80px;">Obtained</th>
                                    <th class="text-center" style="width: 70px;">Max</th>
                                    <th class="text-center" style="width: 60px;">%</th>
                                </tr>
                            </thead>
                            <tbody id="modal-subject-breakdown-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <table class="omr-table" style="font-size: 11px;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">#</th>
                            <th style="width: 25%;">Subject</th>
                            <th style="width: 17%; text-align: center;">Marked Bubble</th>
                            <th style="width: 17%; text-align: center;">Key Answer</th>
                            <th style="width: 15%; text-align: center;">Marks</th>
                            <th style="width: 16%; text-align: right;">Result</th>
                        </tr>
                    </thead>
                    <tbody id="modal-answers-tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer py-2 px-3" style="background: #ffffff; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $allSavedMap = [];
    if (!empty($existingOmrMap)) {
        foreach ($existingOmrMap as $admId => $rec) {
            $allSavedMap[$rec->id] = $rec;
        }
    }
    if (!empty($recentScans)) {
        foreach ($recentScans as $sc) {
            $allSavedMap[$sc->id] = $sc;
        }
    }
@endphp
<script>
window.savedOmrRecords = @json($allSavedMap);
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('omr-class-select');
    const batchSelect = document.getElementById('omr-batch-select');
    const examSelect = document.getElementById('omr-exam-select');
    const fileInput = document.getElementById('omr-image-input');
    const dropzone = document.getElementById('omr-upload-dropzone');
    const dropzoneTitle = document.getElementById('omr-dropzone-title');
    const statusText = document.getElementById('omr-status-text');
    const statusBox = document.getElementById('omr-status-box');

    const badgeBox = document.getElementById('omr-selected-exam-badge');
    const badgeTitle = document.getElementById('omr-badge-title');
    const badgeBatch = document.getElementById('omr-badge-batch');
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
    const countEnrolledTotal = document.getElementById('count-enrolled-total');
    const countStagedMatched = document.getElementById('count-staged-matched');
    const countStagedPending = document.getElementById('count-staged-pending');
    const countStagedUnmatched = document.getElementById('count-staged-unmatched');
    const btnSaveCount = document.getElementById('btn-save-count');
    const btnFinalSubmit = document.getElementById('btn-final-submit');
    const btnClearStaged = document.getElementById('btn-clear-staged');
    const masterSelectCheckbox = document.getElementById('staged-select-all');
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
    const getSelectedExamOption = () => examSelect && examSelect.selectedIndex >= 0 ? examSelect.options[examSelect.selectedIndex] : null;

    // Trigger file chooser
    window.triggerFileInput = function () {
        if (!examSelect || !examSelect.value) {
            alert('Please select a target examination first.');
            if (examSelect) examSelect.focus();
            return;
        }
        if (isProcessingBatch) return;
        fileInput.click();
    };

    // 1. Course / Class dropdown change handler: reloads with ?class_type_id=...
    if (classSelect) {
        classSelect.addEventListener('change', function () {
            const classId = this.value;
            window.location.href = `{{ url('upload_omr') }}?class_type_id=${classId}`;
        });
    }

    // 2. Batch dropdown change handler: reloads with ?class_type_id=...&batch_id=...
    if (batchSelect) {
        batchSelect.addEventListener('change', function () {
            const batchVal = this.value;
            const classId = classSelect ? classSelect.value : '';
            const curExamOpt = getSelectedExamOption();
            let url = `{{ url('upload_omr') }}?`;
            if (classId) url += `class_type_id=${classId}&`;
            if (batchVal && batchVal !== 'all') url += `batch_id=${batchVal}&`;
            if (curExamOpt && curExamOpt.value) url += `exam_id=${curExamOpt.value}`;
            window.location.href = url.replace(/[?&]$/, '');
        });
    }

    // 3. Exam dropdown change handler: reloads with ?exam_id=...&class_type_id=...&batch_id=...
    if (examSelect) {
        examSelect.addEventListener('change', function () {
            const opt = getSelectedExamOption();
            if (this.value && opt) {
                const classId = opt.dataset.classid || (classSelect ? classSelect.value : '');
                const batchId = opt.dataset.batchid || (batchSelect ? batchSelect.value : '');
                let url = `{{ url('upload_omr') }}?exam_id=${this.value}`;
                if (classId) url += `&class_type_id=${classId}`;
                if (batchId && batchId !== '0' && batchId !== 'all') url += `&batch_id=${batchId}`;
                window.location.href = url;
            }
        });
    }

    // Drag and Drop handlers
    if (dropzone) {
        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            if (examSelect && examSelect.value && !isProcessingBatch) {
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
            if (!examSelect || !examSelect.value || isProcessingBatch) return;
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length) {
                processFilesQueue(Array.from(dt.files));
            }
        });
    }

    // File input change handler
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length) {
                processFilesQueue(Array.from(this.files));
            }
        });
    }

    // Match incoming extracted OMR sheet to one of the enrolled students in the table
    function findStudentRow(sheet) {
        // 1. By student ID from backend resolution
        if (sheet.student && sheet.student.id) {
            const row = document.getElementById(`student-row-${sheet.student.id}`);
            if (row) return row;
        }

        // 2. By detected roll number (exact string or trimmed numeric)
        const raw = (sheet.roll_number || '').toString().trim().toLowerCase();
        if (!raw) return null;
        const num = raw.replace(/\D/g, '');
        const trimmed = num.replace(/^0+/, '');

        const rows = document.querySelectorAll('tr.student-omr-row');
        for (const r of rows) {
            const rRoll = (r.dataset.rollNo || '').toLowerCase();
            const rAdm = (r.dataset.admissionNo || '').toLowerCase();
            const rExamRoll = (r.dataset.examRollNo || '').toLowerCase();
            const rNum = r.dataset.numericRoll || '';
            const rTrimmed = rNum.replace(/^0+/, '');

            if (rRoll === raw || rAdm === raw || rExamRoll === raw) return r;
            if (num && (rNum === num || rRoll === num || rAdm === num)) return r;
            if (trimmed && (rTrimmed === trimmed || rNum === trimmed)) return r;
        }
        return null;
    }

    // Attach extracted sheet data directly into an enrolled student's row
    function attachSheetToStudentRow(sheet, row) {
        const tempId = sheet.temp_id;
        const studentId = row.dataset.studentId;
        const thumbSrc = sheet.annotated_image_base64 || sheet.image_url;
        const evalData = sheet.evaluation || {};
        const maxMarks = getSelectedExamOption()?.dataset.maxmarks || evalData.total_marks || 100;

        row.dataset.tempId = tempId;
        row.classList.add('table-success', 'row-scanned');

        // Checkbox
        const cb = row.querySelector('.staged-checkbox');
        if (cb) {
            cb.checked = (sheet.selected !== false);
            cb.dataset.tempId = tempId;
        }

        // Sheet Thumbnail
        const cellSheet = row.querySelector('.cell-sheet');
        if (cellSheet) {
            cellSheet.innerHTML = `
                <img src="${thumbSrc}" class="omr-thumb" alt="Sheet" onclick="viewAnnotatedModal('${tempId}');" title="Click to inspect annotated scan">
            `;
        }

        // File Name
        const cellFilename = row.querySelector('.cell-filename');
        if (cellFilename) {
            cellFilename.setAttribute('title', sheet.source_filename);
            cellFilename.innerHTML = `
                <span class="file-name-text text-dark font-weight-bold" style="font-size: 11.5px;">
                    <i class="fa fa-file-image-o text-primary mr-1"></i> ${escapeHtml(sheet.source_filename)}
                </span>
            `;
        }

        // Detected Roll #
        const cellRoll = row.querySelector('.cell-detected-roll');
        if (cellRoll) {
            cellRoll.innerHTML = `
                <input type="text" class="form-control form-control-sm font-mono text-center roll-input" 
                       value="${escapeHtml(sheet.roll_number || '')}" 
                       style="width: 95px; font-weight: 700; height: 28px; font-size: 12px; margin: 0 auto;"
                       onchange="handleStagedRollChange('${tempId}', this.value);">
            `;
        }

        // Questions Breakdown
        const cellQuestions = row.querySelector('.cell-questions');
        if (cellQuestions) {
            cellQuestions.innerHTML = `
                <div class="font-weight-bold text-dark" style="font-size: 12px;">${sheet.summary?.attempted || (evalData.correct_answers + evalData.incorrect_answers)} / ${sheet.summary?.total_questions || evalData.total_questions || 50}</div>
                <div class="small font-weight-bold" style="font-size: 10px;">
                    <span style="color: #16a34a !important;">🟢 ${evalData.correct_answers || 0}</span> 
                    <span style="color: #dc2626 !important;">🔴 ${evalData.incorrect_answers || 0}</span> 
                    <span style="color: #64748b !important;">⚪ ${evalData.blank_answers || 0}</span>
                </div>
            `;
        }

        // Score & Marks
        const cellScore = row.querySelector('.cell-score');
        if (cellScore) {
            let pillsHtml = '';
            const sbList = evalData.subject_breakdown || [];
            if (Array.isArray(sbList) && sbList.length > 0) {
                pillsHtml = '<div class="d-flex flex-wrap justify-content-center mt-1" style="gap: 3px;">';
                sbList.forEach(sb => {
                    const shortName = (sb.subject_name || 'Sub').split(' ')[0];
                    pillsHtml += `<span class="badge" style="font-size: 8.5px; padding: 1px 4px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;" title="${escapeHtml(sb.subject_name)}: ${sb.obtained_marks}/${sb.max_marks}">${escapeHtml(shortName)}: +${sb.obtained_marks}</span>`;
                });
                pillsHtml += '</div>';
            }

            cellScore.innerHTML = `
                <div>
                    <span class="font-weight-bold text-success font-size-13">+${evalData.obtained_marks || 0}</span>
                    <span class="text-muted small"> / ${evalData.total_marks || maxMarks}</span>
                    <span class="badge badge-success ml-1 font-weight-bold px-2 py-1" style="background: #16a34a !important; color: #ffffff !important; font-size: 9.5px;">${evalData.percentage || 0}%</span>
                </div>
                ${pillsHtml}
            `;
        }

        // Status Badge
        const cellStatus = row.querySelector('.cell-status');
        if (cellStatus) {
            cellStatus.innerHTML = `
                <span class="badge badge-info px-2 py-1 font-weight-bold" style="background: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #7dd3fc !important; font-size: 10.5px;">
                    <i class="fa fa-clock-o mr-1"></i> Scanned
                </span>
            `;
        }

        // Actions
        const cellActions = row.querySelector('.cell-actions');
        if (cellActions) {
            cellActions.innerHTML = `
                <button type="button" class="btn btn-xs btn-outline-primary mr-1" style="padding: 2px 7px; font-size: 11px;" onclick="viewAnnotatedModal('${tempId}');" title="Inspect OMR Scan">
                    <i class="fa fa-eye"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-info mr-1" style="padding: 2px 7px; font-size: 11px;" onclick="viewAnswersModal('${tempId}');" title="Question-by-Question Matrix">
                    <i class="fa fa-list"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger" style="padding: 2px 7px; font-size: 11px;" onclick="resetStudentRow('${tempId}');" title="Reset scan">
                    <i class="fa fa-undo"></i>
                </button>
            `;
        }
    }

    // Render an extra warning row for sheets whose roll does not match any enrolled student
    function renderUnmatchedRow(sheet) {
        let row = document.getElementById(`staged-unmatched-${sheet.temp_id}`);
        if (!row) {
            row = document.createElement('tr');
            row.id = `staged-unmatched-${sheet.temp_id}`;
            row.className = 'row-unmatched';
            row.dataset.tempId = sheet.temp_id;
            stagingTbody.appendChild(row);
        }
        const thumbSrc = sheet.annotated_image_base64 || sheet.image_url;
        const evalData = sheet.evaluation || {};
        const maxMarks = getSelectedExamOption()?.dataset.maxmarks || evalData.total_marks || 100;

        row.innerHTML = `
            <td class="text-center" style="vertical-align: middle;">
                <input type="checkbox" class="staged-checkbox" data-temp-id="${sheet.temp_id}" ${sheet.selected !== false ? 'checked' : ''} onchange="toggleSheetSelect('${sheet.temp_id}', this.checked);">
            </td>
            <td class="text-center font-weight-bold text-warning" style="vertical-align: middle;">
                <i class="fa fa-exclamation-triangle" title="Unmatched student"></i>
            </td>
            <td style="vertical-align: middle;">
                <div class="badge badge-warning text-dark font-weight-bold px-2 py-1" style="background: #fef08a !important; color: #854d0e !important; border: 1px solid #facc15 !important; font-size: 11px;">
                    <i class="fa fa-exclamation-triangle mr-1"></i> Unmatched Roll #
                </div>
                <div class="text-danger small font-weight-bold mt-1" style="font-size: 10.5px;">
                    Student not found in this class
                </div>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <span class="badge badge-light border text-danger font-mono font-weight-bold" style="font-size: 10.5px; padding: 2px 6px;">
                    Not Enrolled
                </span>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <img src="${thumbSrc}" class="omr-thumb" alt="Sheet" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="Click to inspect scan">
            </td>
            <td class="cell-filename text-truncate" style="max-width: 130px; vertical-align: middle;" title="${escapeHtml(sheet.source_filename)}">
                <span class="file-name-text text-dark font-weight-bold" style="font-size: 11.5px;">
                    <i class="fa fa-file-image-o text-primary mr-1"></i> ${escapeHtml(sheet.source_filename)}
                </span>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <input type="text" class="form-control form-control-sm font-mono text-center roll-input" 
                       value="${escapeHtml(sheet.roll_number || '')}" 
                       style="width: 95px; font-weight: 700; height: 28px; font-size: 12px; margin: 0 auto; border-color: #f59e0b !important;"
                       placeholder="Enter Roll #"
                       onchange="handleUnmatchedRollChange('${sheet.temp_id}', this.value);">
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <div class="font-weight-bold text-dark" style="font-size: 12px;">${sheet.summary?.attempted || (evalData.correct_answers + evalData.incorrect_answers)} / ${sheet.summary?.total_questions || evalData.total_questions || 50}</div>
                <div class="small font-weight-bold" style="font-size: 10px;">
                    <span style="color: #16a34a !important;">🟢 ${evalData.correct_answers || 0}</span> 
                    <span style="color: #dc2626 !important;">🔴 ${evalData.incorrect_answers || 0}</span> 
                    <span style="color: #64748b !important;">⚪ ${evalData.blank_answers || 0}</span>
                </div>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <div>
                    <span class="font-weight-bold text-success font-size-13">+${evalData.obtained_marks || 0}</span>
                    <span class="text-muted small"> / ${evalData.total_marks || maxMarks}</span>
                    <span class="badge badge-success ml-1 font-weight-bold px-2 py-1" style="background: #16a34a !important; color: #ffffff !important; font-size: 9.5px;">${evalData.percentage || 0}%</span>
                </div>
            </td>
            <td class="text-center" style="vertical-align: middle;">
                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" style="background: #fef08a !important; color: #854d0e !important; border: 1px solid #facc15 !important; font-size: 10.5px;">
                    <i class="fa fa-warning mr-1"></i> Needs Review
                </span>
            </td>
            <td class="text-right" style="vertical-align: middle; white-space: nowrap;">
                <button type="button" class="btn btn-xs btn-outline-primary mr-1" style="padding: 2px 7px; font-size: 11px;" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="Inspect OMR Scan">
                    <i class="fa fa-eye"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-info mr-1" style="padding: 2px 7px; font-size: 11px;" onclick="viewAnswersModal('${sheet.temp_id}');" title="Question Matrix">
                    <i class="fa fa-list"></i>
                </button>
                <button type="button" class="btn btn-xs btn-outline-danger" style="padding: 2px 7px; font-size: 11px;" onclick="removeUnmatchedSheet('${sheet.temp_id}');" title="Remove sheet">
                    <i class="fa fa-trash-o"></i>
                </button>
            </td>
        `;
    }

    // Process uploaded files sequentially
    async function processFilesQueue(files) {
        const examId = examSelect ? examSelect.value : '';
        if (!examId || !files.length) return;

        isProcessingBatch = true;
        if (fileInput) fileInput.disabled = true;
        if (dropzone) dropzone.classList.add('disabled');
        if (progressWrapper) progressWrapper.classList.remove('d-none');
        updateStagingCounters();

        const totalFiles = files.length;
        let successful = 0;

        try {
            for (let i = 0; i < totalFiles; i++) {
                const file = files[i];
                const percent = Math.round(((i) / totalFiles) * 100);
                if (progressBar) progressBar.style.width = `${percent}%`;
                if (progressPercentText) progressPercentText.textContent = `${percent}%`;
                if (progressStatusText) {
                    progressStatusText.innerHTML = `<i class="fa fa-refresh fa-spin mr-1"></i> Extracting sheet ${i + 1} of ${totalFiles}: <b>${escapeHtml(file.name)}</b>...`;
                }

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

                    // Match with enrolled student row or append unmatched row
                    const matchedRow = findStudentRow(sheet);
                    if (matchedRow) {
                        attachSheetToStudentRow(sheet, matchedRow);
                    } else {
                        renderUnmatchedRow(sheet);
                    }

                    successful++;
                } catch (err) {
                    console.error('Extraction error for ' + file.name, err);
                    if (statusBox && statusText) {
                        statusText.innerHTML = `<span class="text-danger font-weight-bold"><i class="fa fa-exclamation-circle mr-1"></i> Error extracting ${escapeHtml(file.name)}: ${escapeHtml(err.message)}</span>`;
                    }
                }

                updateStagingCounters();
            }
        } finally {
            if (progressBar) progressBar.style.width = '100%';
            if (progressPercentText) progressPercentText.textContent = '100%';
            if (progressStatusText) {
                progressStatusText.innerHTML = `<i class="fa fa-check-circle text-success mr-1"></i> Processed ${successful} of ${totalFiles} sheets! Review evaluated marks in the matrix above and click "Final Submit &amp; Save Marks".`;
            }

            setTimeout(() => {
                if (progressWrapper) progressWrapper.classList.add('d-none');
            }, 3500);

            if (fileInput) {
                fileInput.value = '';
                fileInput.disabled = false;
            }
            if (dropzone) {
                dropzone.classList.remove('disabled');
                dropzone.classList.add('active');
            }
            isProcessingBatch = false;
            updateStagingCounters();
        }
    }

    // Reset a student's row back to "Pending Scan" and remove its staged sheet
    window.resetStudentRow = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;
        const row = document.querySelector(`tr.student-omr-row[data-temp-id="${tempId}"]`);
        if (row) {
            row.classList.remove('table-success', 'row-scanned');
            delete row.dataset.tempId;

            const cb = row.querySelector('.staged-checkbox');
            if (cb) {
                cb.checked = false;
                delete cb.dataset.tempId;
            }

            const cellSheet = row.querySelector('.cell-sheet');
            if (cellSheet) cellSheet.innerHTML = `<div class="sheet-placeholder text-muted" style="font-size: 10px; opacity: 0.6;"><i class="fa fa-file-image-o d-block mb-1"></i> Waiting</div>`;

            const cellFilename = row.querySelector('.cell-filename');
            if (cellFilename) cellFilename.innerHTML = `<span class="file-name-text text-muted" style="font-size: 11.5px;"><i class="fa fa-clock-o text-muted mr-1"></i> Pending Scan</span>`;

            const cellRoll = row.querySelector('.cell-detected-roll');
            if (cellRoll) cellRoll.innerHTML = `<span class="text-muted small font-mono detected-roll-placeholder">--</span>`;

            const cellQ = row.querySelector('.cell-questions');
            if (cellQ) cellQ.innerHTML = `<span class="text-muted small">-- / ${getSelectedExamOption()?.dataset.questions || 50}</span>`;

            const cellScore = row.querySelector('.cell-score');
            if (cellScore) cellScore.innerHTML = `<span class="text-muted font-weight-bold" style="color: #94a3b8;">-- / ${getSelectedExamOption()?.dataset.maxmarks || 100}</span>`;

            const cellStatus = row.querySelector('.cell-status');
            if (cellStatus) cellStatus.innerHTML = `<span class="badge badge-secondary px-2 py-1 font-weight-bold status-badge" style="background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1 !important; font-size: 10.5px;"><i class="fa fa-clock-o mr-1"></i> Pending Scan</span>`;

            const cellActions = row.querySelector('.cell-actions');
            if (cellActions) cellActions.innerHTML = `<span class="text-muted small pr-2">--</span>`;
        }
        stagedSheets.delete(tempId);
        updateStagingCounters();
    };

    // Remove an unmatched sheet row
    window.removeUnmatchedSheet = function (tempId) {
        stagedSheets.delete(tempId);
        document.getElementById(`staged-unmatched-${tempId}`)?.remove();
        updateStagingCounters();
    };

    // Handle roll number edit inside an enrolled student's row
    window.handleStagedRollChange = function (tempId, newRoll) {
        const sheet = stagedSheets.get(tempId);
        if (sheet) {
            sheet.roll_number = newRoll.trim();
        }
    };

    // Handle roll number edit inside an unmatched row: attempt re-match!
    window.handleUnmatchedRollChange = async function (tempId, newRoll) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;
        sheet.roll_number = newRoll.trim();

        // 1. Check if new roll matches an enrolled student row
        const row = findStudentRow(sheet);
        if (row) {
            document.getElementById(`staged-unmatched-${tempId}`)?.remove();
            attachSheetToStudentRow(sheet, row);
            updateStagingCounters();
            return;
        }

        // 2. Otherwise backend lookup
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
                    class_type_id: classSelect ? classSelect.value : 0
                })
            });
            const data = await res.json();
            if (data.status === 'success' && data.matched) {
                sheet.student = data.student;
                const recheckRow = findStudentRow(sheet);
                if (recheckRow) {
                    document.getElementById(`staged-unmatched-${tempId}`)?.remove();
                    attachSheetToStudentRow(sheet, recheckRow);
                }
            }
        } catch (e) {
            console.error(e);
        }
        updateStagingCounters();
    };

    // Update Staging summary counters & enable/disable Final Submit button
    function updateStagingCounters() {
        const enrolledRows = document.querySelectorAll('tr.student-omr-row');
        const totalEnrolled = enrolledRows.length;

        let scannedCount = 0;
        enrolledRows.forEach(r => {
            if (r.classList.contains('row-scanned') || r.dataset.tempId) {
                scannedCount++;
            }
        });

        const pendingCount = Math.max(0, totalEnrolled - scannedCount);
        const unmatchedCount = document.querySelectorAll('tr.row-unmatched').length;

        let readyToSaveCount = 0;
        let sumMarks = 0;
        stagedSheets.forEach(s => {
            if (s.selected !== false) {
                readyToSaveCount++;
                sumMarks += parseFloat(s.evaluation?.obtained_marks || 0);
            }
        });

        if (countEnrolledTotal) countEnrolledTotal.textContent = totalEnrolled;
        if (countStagedMatched) countStagedMatched.textContent = scannedCount;
        if (countStagedPending) countStagedPending.textContent = pendingCount;
        if (countStagedUnmatched) countStagedUnmatched.textContent = unmatchedCount;
        if (btnSaveCount) btnSaveCount.textContent = readyToSaveCount;

        // Enable or disable Final Submit button
        if (readyToSaveCount > 0 && !isProcessingBatch) {
            btnFinalSubmit.disabled = false;
            btnFinalSubmit.removeAttribute('disabled');
        } else {
            btnFinalSubmit.disabled = true;
            btnFinalSubmit.setAttribute('disabled', 'disabled');
        }

        // Enable or disable Clear button
        if (stagedSheets.size > 0 && !isProcessingBatch) {
            btnClearStaged.disabled = false;
            btnClearStaged.removeAttribute('disabled');
        } else {
            btnClearStaged.disabled = true;
            btnClearStaged.setAttribute('disabled', 'disabled');
        }

        if (stagedSheets.size > 0) {
            const avg = (sumMarks / Math.max(1, readyToSaveCount)).toFixed(2);
            stagingSummaryMarks.innerHTML = `Staged Average Score: <span class="font-weight-bold" style="color: #16a34a !important;">${avg} Marks</span> (${readyToSaveCount} of ${stagedSheets.size} Selected for Submit)`;
        } else {
            stagingSummaryMarks.innerHTML = '';
        }
    }

    // Toggle single sheet selection
    window.toggleSheetSelect = function (tempId, isSelected) {
        const sheet = stagedSheets.get(tempId);
        if (sheet) {
            sheet.selected = isSelected;
            updateStagingCounters();
        }
    };

    // Toggle student select checkbox
    window.toggleStudentSelect = function (studentId, isSelected) {
        const row = document.getElementById(`student-row-${studentId}`);
        if (row && row.dataset.tempId) {
            const sheet = stagedSheets.get(row.dataset.tempId);
            if (sheet) {
                sheet.selected = isSelected;
            }
        }
        updateStagingCounters();
    };

    // Master Select / Deselect All
    if (masterSelectCheckbox) {
        masterSelectCheckbox.addEventListener('change', function () {
            const isChecked = this.checked;
            stagedSheets.forEach(s => {
                s.selected = isChecked;
            });
            stagingTbody.querySelectorAll('.staged-checkbox').forEach(cb => {
                cb.checked = isChecked;
            });
            updateStagingCounters();
        });
    }

    // Clear all newly staged extractions
    window.clearAllStaged = function () {
        if (!stagedSheets.size) return;
        if (!confirm('Are you sure you want to clear all scanned extractions? (Unsaved marks will be discarded).')) return;

        stagedSheets.forEach((sheet, tempId) => {
            const row = document.querySelector(`tr.student-omr-row[data-temp-id="${tempId}"]`);
            if (row) {
                row.classList.remove('table-success', 'row-scanned');
                delete row.dataset.tempId;

                const cb = row.querySelector('.staged-checkbox');
                if (cb) {
                    cb.checked = false;
                    delete cb.dataset.tempId;
                }

                const cellSheet = row.querySelector('.cell-sheet');
                if (cellSheet) cellSheet.innerHTML = `<div class="sheet-placeholder text-muted" style="font-size: 10px; opacity: 0.6;"><i class="fa fa-file-image-o d-block mb-1"></i> Waiting</div>`;

                const cellFilename = row.querySelector('.cell-filename');
                if (cellFilename) cellFilename.innerHTML = `<span class="file-name-text text-muted" style="font-size: 11.5px;"><i class="fa fa-clock-o text-muted mr-1"></i> Pending Scan</span>`;

                const cellRoll = row.querySelector('.cell-detected-roll');
                if (cellRoll) cellRoll.innerHTML = `<span class="text-muted small font-mono detected-roll-placeholder">--</span>`;

                const cellQ = row.querySelector('.cell-questions');
                if (cellQ) cellQ.innerHTML = `<span class="text-muted small">-- / ${getSelectedExamOption()?.dataset.questions || 50}</span>`;

                const cellScore = row.querySelector('.cell-score');
                if (cellScore) cellScore.innerHTML = `<span class="text-muted font-weight-bold" style="color: #94a3b8;">-- / ${getSelectedExamOption()?.dataset.maxmarks || 100}</span>`;

                const cellStatus = row.querySelector('.cell-status');
                if (cellStatus) cellStatus.innerHTML = `<span class="badge badge-secondary px-2 py-1 font-weight-bold status-badge" style="background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1 !important; font-size: 10.5px;"><i class="fa fa-clock-o mr-1"></i> Pending Scan</span>`;

                const cellActions = row.querySelector('.cell-actions');
                if (cellActions) cellActions.innerHTML = `<span class="text-muted small pr-2">--</span>`;
            }
            document.getElementById(`staged-unmatched-${tempId}`)?.remove();
        });

        stagedSheets.clear();
        updateStagingCounters();
    };

    if (btnClearStaged) {
        btnClearStaged.addEventListener('click', function (e) {
            e.preventDefault();
            clearAllStaged();
        });
    }

    // Modal: Annotated Sheet Inspection
    window.viewAnnotatedModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const src = sheet.annotated_image_base64 || sheet.image_url;
        modalAnnotatedImg.src = src;
        modalAnnotatedInfo.innerHTML = `
            <b>File:</b> ${escapeHtml(sheet.source_filename)} &bull; 
            <b>Roll #:</b> ${escapeHtml(sheet.roll_number || 'N/A')} &bull; 
            <b>Student:</b> ${escapeHtml(sheet.student?.name || 'Unregistered')} &bull; 
            <b>Score:</b> +${sheet.evaluation?.obtained_marks || 0} / ${sheet.evaluation?.total_marks || 0} (${sheet.evaluation?.percentage || 0}%)
        `;

        if (typeof $ !== 'undefined' && $('#modalAnnotatedSheet').modal) {
            $('#modalAnnotatedSheet').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalAnnotatedSheet')).show();
        }
    };

    // Modal: Saved OMR Sheet Inspection
    window.viewSavedOmrModal = function (omrId, btnEl) {
        const rec = (window.savedOmrRecords && window.savedOmrRecords[omrId]) || null;
        let btn = btnEl;
        if (!btn && typeof event !== 'undefined' && event) {
            btn = event.currentTarget || (event.target ? event.target.closest('button, img, div') : null);
        }
        const tr = btn ? btn.closest('tr') : (document.querySelector(`[onclick*="viewSavedOmrModal('${omrId}')"]`)?.closest('tr') || document.getElementById(`scan-row-${omrId}`));
        const img = tr ? tr.querySelector('img.omr-thumb') : null;

        let imageUrl = btn?.dataset?.imageUrl || tr?.dataset?.imageUrl || (rec ? (rec.image_url || (rec.stored_file ? '/' + rec.stored_file.replace(/^\/+/, '') : null)) : null);
        if (!imageUrl && img && img.src && !img.src.includes('broken') && !img.src.endsWith('/')) {
            imageUrl = img.src;
        }

        const studentName = btn?.dataset?.studentName || tr?.dataset?.studentName || (rec?.student ? (rec.student.first_name + ' ' + (rec.student.last_name || '')) : 'Student');
        const rollNo = btn?.dataset?.roll || tr?.dataset?.rollNo || rec?.unique_id || '';
        const score = btn?.dataset?.score || (rec ? `+${rec.obtained_marks || 0} / ${rec.total_marks || 0} (${rec.percentage || 0}%)` : '');
        const filename = btn?.dataset?.filename || rec?.source_filename || '';

        if (modalAnnotatedImg) {
            if (imageUrl) {
                modalAnnotatedImg.src = imageUrl;
                modalAnnotatedImg.style.display = 'inline-block';
            } else {
                modalAnnotatedImg.src = '';
                modalAnnotatedImg.style.display = 'none';
            }
        }

        if (modalAnnotatedInfo) {
            modalAnnotatedInfo.innerHTML = `
                <div class="py-1">
                    <span class="badge badge-primary px-2 py-1 mr-1" style="background: #002C54; color: #fff;">Evaluation #${omrId}</span>
                    <span class="font-weight-bold text-dark font-size-13">${escapeHtml(studentName)}</span> 
                    ${rollNo ? `<span class="text-muted ml-1">(Roll: <b class="font-mono text-primary">${escapeHtml(rollNo)}</b>)</span>` : ''} 
                    ${score ? `&bull; Score: <span class="text-success font-weight-bold font-mono font-size-13">${score}</span>` : ''}
                    ${filename ? `<div class="text-muted mt-1 small font-mono"><i class="fa fa-file-image-o mr-1"></i>${escapeHtml(filename)}</div>` : ''}
                </div>
            `;
        }

        if (typeof $ !== 'undefined' && $('#modalAnnotatedSheet').modal) {
            $('#modalAnnotatedSheet').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalAnnotatedSheet')).show();
        }
    };

    // Modal: Saved Question-by-Question Matrix
    window.viewSavedAnswersModal = function (omrId) {
        const rec = (window.savedOmrRecords && window.savedOmrRecords[omrId]) || null;
        if (!rec) {
            alert('Evaluation record #' + omrId + ' not found in memory.');
            return;
        }

        let answers = rec.extracted_answers || [];
        if (typeof answers === 'string') {
            try { answers = JSON.parse(answers); } catch (e) { answers = []; }
        }

        let subBreakdown = rec.subject_breakdown || [];
        if (typeof subBreakdown === 'string') {
            try { subBreakdown = JSON.parse(subBreakdown); } catch (e) { subBreakdown = []; }
        }

        const stName = rec.student ? (rec.student.first_name + ' ' + (rec.student.last_name || '')) : ('Student #' + (rec.unique_id || ''));
        modalAnsStudent.textContent = stName;
        modalAnsRoll.textContent = rec.unique_id || 'N/A';
        modalAnsScore.textContent = `+${rec.obtained_marks || 0} / ${rec.total_marks || 0} (${rec.percentage || 0}%)`;

        // Subject breakdown
        const subWrapper = document.getElementById('modal-subject-breakdown-wrapper');
        const subTbody = document.getElementById('modal-subject-breakdown-tbody');

        if (subWrapper && subTbody) {
            if (Array.isArray(subBreakdown) && subBreakdown.length > 0) {
                let subHtml = '';
                subBreakdown.forEach(sb => {
                    const rangeText = (sb.from_q && sb.to_q) ? `Q${sb.from_q}-${sb.to_q}` : `${sb.total_questions || 0} Qs`;
                    subHtml += `
                        <tr>
                            <td class="font-weight-bold text-dark">${escapeHtml(sb.subject_name || 'Subject')}</td>
                            <td class="text-center font-mono small text-muted">${rangeText}</td>
                            <td class="text-center font-weight-bold text-success">${sb.correct_answers || 0}</td>
                            <td class="text-center font-weight-bold text-danger">${sb.incorrect_answers || 0}</td>
                            <td class="text-center text-muted">${sb.blank_answers || 0}</td>
                            <td class="text-center font-weight-bold font-mono text-primary">+${sb.obtained_marks_formatted || sb.obtained_marks || 0}</td>
                            <td class="text-center font-weight-bold font-mono text-muted">${sb.max_marks_formatted || sb.max_marks || 0}</td>
                            <td class="text-center font-weight-bold ${parseFloat(sb.percentage || 0) >= 40 ? 'text-success' : 'text-danger'}">${sb.percentage || 0}%</td>
                        </tr>
                    `;
                });
                subTbody.innerHTML = subHtml;
                subWrapper.style.display = 'block';
            } else {
                subWrapper.style.display = 'none';
            }
        }

        let html = '';
        answers.forEach(item => {
            let resBadge = '';
            let marksBadge = '';
            const mAwarded = (item.marks_awarded !== undefined) ? item.marks_awarded : (item.is_correct ? (item.marks_correct || 1) : (item.answer ? -(item.marks_incorrect || 0) : 0));

            if (item.is_correct) {
                resBadge = '<span class="badge badge-success font-weight-bold px-2 py-1" style="background: #dcfce7 !important; color: #15803d !important; border: 1px solid #86efac !important; font-size: 10px;"><i class="fa fa-check"></i> Correct</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-success" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 10px;">+${parseFloat(mAwarded).toFixed(2)}</span>`;
            } else if (item.status === 'blank' || !item.answer) {
                resBadge = '<span class="badge badge-light px-2 py-1 text-muted" style="border: 1px solid #cbd5e1; font-size: 10px;">Left Blank</span>';
                marksBadge = `<span class="badge font-mono text-muted" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 10px;">0.00</span>`;
            } else {
                resBadge = '<span class="badge badge-danger font-weight-bold px-2 py-1" style="background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fca5a5 !important; font-size: 10px;"><i class="fa fa-times"></i> Wrong</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-danger" style="background: #fef2f2; border: 1px solid #fca5a5; font-size: 10px;">${parseFloat(mAwarded).toFixed(2)}</span>`;
            }

            const subTag = item.subject_name ? `<span class="badge badge-light text-primary font-weight-bold text-truncate" style="max-width: 140px; font-size: 9.5px; border: 1px solid #cbd5e1;">${escapeHtml(item.subject_name)}</span>` : '<span class="text-muted small">--</span>';

            html += `
                <tr>
                    <td class="font-weight-bold text-dark">Q${item.question}</td>
                    <td>${subTag}</td>
                    <td class="text-center font-mono font-weight-bold ${item.is_correct ? 'text-success' : (item.answer ? 'text-danger' : 'text-muted')}">${item.answer || '—'}</td>
                    <td class="text-center font-mono font-weight-bold text-primary">${item.correct_answer || '—'}</td>
                    <td class="text-center">${marksBadge}</td>
                    <td class="text-right">${resBadge}</td>
                </tr>
            `;
        });
        modalAnswersTbody.innerHTML = html;

        if (typeof $ !== 'undefined' && $('#modalAnswersGrid').modal) {
            $('#modalAnswersGrid').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalAnswersGrid')).show();
        }
    };

    // Modal: Question-by-Question Matrix
    window.viewAnswersModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const answers = sheet.evaluation?.extracted_answers || [];
        modalAnsStudent.textContent = sheet.student?.name || 'Unregistered Student';
        modalAnsRoll.textContent = sheet.roll_number || 'N/A';
        modalAnsScore.textContent = `+${sheet.evaluation?.obtained_marks || 0} / ${sheet.evaluation?.total_marks || 0} (${sheet.evaluation?.percentage || 0}%)`;

        // Subject breakdown
        const subBreakdown = sheet.evaluation?.subject_breakdown || [];
        const subWrapper = document.getElementById('modal-subject-breakdown-wrapper');
        const subTbody = document.getElementById('modal-subject-breakdown-tbody');

        if (subWrapper && subTbody) {
            if (Array.isArray(subBreakdown) && subBreakdown.length > 0) {
                let subHtml = '';
                subBreakdown.forEach(sb => {
                    const rangeText = (sb.from_q && sb.to_q) ? `Q${sb.from_q}-${sb.to_q}` : `${sb.total_questions} Qs`;
                    subHtml += `
                        <tr>
                            <td class="font-weight-bold text-dark">${escapeHtml(sb.subject_name || 'Subject')}</td>
                            <td class="text-center font-mono small text-muted">${rangeText}</td>
                            <td class="text-center font-weight-bold text-success">${sb.correct_answers || 0}</td>
                            <td class="text-center font-weight-bold text-danger">${sb.incorrect_answers || 0}</td>
                            <td class="text-center text-muted">${sb.blank_answers || 0}</td>
                            <td class="text-center font-weight-bold font-mono text-primary">+${sb.obtained_marks_formatted || sb.obtained_marks || 0}</td>
                            <td class="text-center font-weight-bold font-mono text-muted">${sb.max_marks_formatted || sb.max_marks || 0}</td>
                            <td class="text-center font-weight-bold ${parseFloat(sb.percentage || 0) >= 40 ? 'text-success' : 'text-danger'}">${sb.percentage || 0}%</td>
                        </tr>
                    `;
                });
                subTbody.innerHTML = subHtml;
                subWrapper.style.display = 'block';
            } else {
                subWrapper.style.display = 'none';
            }
        }

        let html = '';
        answers.forEach(item => {
            let resBadge = '';
            let marksBadge = '';

            const mAwarded = (item.marks_awarded !== undefined) ? item.marks_awarded : (item.is_correct ? (item.marks_correct || 1) : (item.answer ? -(item.marks_incorrect || 0) : 0));

            if (item.is_correct) {
                resBadge = '<span class="badge badge-success font-weight-bold px-2 py-1" style="background: #dcfce7 !important; color: #15803d !important; border: 1px solid #86efac !important; font-size: 10px;"><i class="fa fa-check"></i> Correct</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-success" style="background: #f0fdf4; border: 1px solid #bbf7d0; font-size: 10px;">+${parseFloat(mAwarded).toFixed(2)}</span>`;
            } else if (item.status === 'blank' || !item.answer) {
                resBadge = '<span class="badge badge-light px-2 py-1 text-muted" style="border: 1px solid #cbd5e1; font-size: 10px;">Left Blank</span>';
                marksBadge = `<span class="badge font-mono text-muted" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 10px;">0.00</span>`;
            } else {
                resBadge = '<span class="badge badge-danger font-weight-bold px-2 py-1" style="background: #fee2e2 !important; color: #b91c1c !important; border: 1px solid #fca5a5 !important; font-size: 10px;"><i class="fa fa-times"></i> Wrong</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-danger" style="background: #fef2f2; border: 1px solid #fca5a5; font-size: 10px;">${parseFloat(mAwarded).toFixed(2)}</span>`;
            }

            const subTag = item.subject_name ? `<span class="badge badge-light text-primary font-weight-bold text-truncate" style="max-width: 140px; font-size: 9.5px; border: 1px solid #cbd5e1;">${escapeHtml(item.subject_name)}</span>` : '<span class="text-muted small">--</span>';

            html += `
                <tr>
                    <td class="font-weight-bold text-dark">Q${item.question}</td>
                    <td>${subTag}</td>
                    <td class="text-center font-mono font-weight-bold ${item.is_correct ? 'text-success' : (item.answer ? 'text-danger' : 'text-muted')}">${item.answer || '—'}</td>
                    <td class="text-center font-mono font-weight-bold text-primary">${item.correct_answer || '—'}</td>
                    <td class="text-center">${marksBadge}</td>
                    <td class="text-right">${resBadge}</td>
                </tr>
            `;
        });
        modalAnswersTbody.innerHTML = html;

        if (typeof $ !== 'undefined' && $('#modalAnswersGrid').modal) {
            $('#modalAnswersGrid').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalAnswersGrid')).show();
        }
    };

    // FINAL SUBMIT: Commit all staged and selected sheets into database & exam marks
    window.confirmFinalSubmit = async function () {
        const examId = examSelect ? examSelect.value : '';
        if (!examId) {
            alert('Please select an examination first.');
            if (examSelect) examSelect.focus();
            return;
        }

        const selectedSheets = [];
        stagedSheets.forEach(s => {
            if (s.selected !== false) {
                selectedSheets.push(s);
            }
        });

        if (!selectedSheets.length) {
            alert('No scanned sheets selected for final submission.');
            return;
        }

        const examName = getSelectedExamOption()?.dataset.name || 'this examination';
        const msg = `You are about to save ${selectedSheets.length} evaluated OMR sheet(s) and upload student marks directly to "${examName}".\n\nAre you sure you want to proceed?`;
        if (!confirm(msg)) return;

        btnFinalSubmit.disabled = true;
        btnFinalSubmit.setAttribute('disabled', 'disabled');
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

            // Update student rows to "Saved" status
            const savedTbody = document.getElementById('omr-saved-tbody');
            document.getElementById('omr-saved-empty-row')?.remove();
            let newlyAddedCount = 0;

            selectedSheets.forEach(s => {
                // If attached to an enrolled student row
                const stuRow = document.querySelector(`tr.student-omr-row[data-temp-id="${s.temp_id}"]`);
                if (stuRow) {
                    const statusCell = stuRow.querySelector('.cell-status');
                    if (statusCell) {
                        statusCell.innerHTML = `
                            <span class="badge badge-success px-2 py-1 font-weight-bold" style="background: #16a34a !important; color: #ffffff !important; font-size: 10.5px;">
                                <i class="fa fa-check-circle mr-1"></i> Saved
                            </span>
                        `;
                    }
                    delete stuRow.dataset.tempId;
                }

                // If unmatched row
                const unmatchedRow = document.getElementById(`staged-unmatched-${s.temp_id}`);
                if (unmatchedRow) {
                    unmatchedRow.remove();
                }

                // Update existing row OR Prepend new row to Saved Ledger (No Duplicate Rows!)
                if (savedTbody) {
                    const roll = String(s.roll_number || '').trim();
                    let existingRow = savedTbody.querySelector(`tr[data-unique-id="${CSS.escape(roll)}"]`);

                    const rowHtml = `
                        <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="${escapeHtml(s.source_filename)}">
                            <i class="fa fa-file-image-o text-muted mr-1"></i> ${escapeHtml(s.source_filename)}
                        </td>
                        <td>
                            <span class="font-mono font-weight-bold text-primary">${escapeHtml(s.roll_number)}</span>
                            <div class="text-muted small font-weight-bold">${escapeHtml(s.student?.name || 'Student #' + s.roll_number)}</div>
                        </td>
                        <td>
                            <span class="font-weight-bold text-dark">${escapeHtml(examName)}</span>
                        </td>
                        <td class="text-center font-weight-bold text-success">${s.evaluation?.correct_answers || 0}</td>
                        <td class="text-center font-weight-bold text-danger">${s.evaluation?.incorrect_answers || 0}</td>
                        <td class="text-center text-muted">${s.evaluation?.blank_answers || 0}</td>
                        <td class="font-mono font-weight-bold">${Number(s.evaluation?.total_marks || 0).toFixed(2)}</td>
                        <td class="font-mono font-weight-bold text-success">+${Number(s.evaluation?.obtained_marks || 0).toFixed(2)}</td>
                        <td>
                            <span class="badge-pill-status badge-matched">${s.evaluation?.percentage || 0}%</span>
                        </td>
                        <td class="text-center font-weight-bold text-primary">#1</td>
                        <td class="text-right">
                            <span class="badge-pill-status badge-saved" title="Marks updated in student exam file">
                                <i class="fa fa-check-circle"></i> Saved
                            </span>
                        </td>
                    `;

                    if (existingRow) {
                        // REPLACE & UPDATE existing row with pulse highlight
                        existingRow.innerHTML = rowHtml;
                        existingRow.style.transition = 'background-color 0.4s ease';
                        existingRow.style.backgroundColor = '#dcfce7';
                        setTimeout(() => { existingRow.style.backgroundColor = ''; }, 2000);
                    } else {
                        // PREPEND new record
                        const tr = document.createElement('tr');
                        tr.dataset.uniqueId = roll;
                        tr.innerHTML = rowHtml;
                        savedTbody.prepend(tr);
                        newlyAddedCount++;
                    }
                }

                stagedSheets.delete(s.temp_id);
            });

            // Update Saved Ledger badge & KPIs
            const savedCountBadge = document.getElementById('omr-saved-count-badge');
            if (savedCountBadge && savedTbody) {
                const totalLedgerRows = savedTbody.querySelectorAll('tr[data-unique-id]').length;
                savedCountBadge.textContent = `${totalLedgerRows} Records Saved`;
            }
            if (kpiTodayScans && newlyAddedCount > 0) {
                const currentToday = parseInt(kpiTodayScans.textContent.replace(/\D/g, '') || '0', 10);
                kpiTodayScans.textContent = (currentToday + newlyAddedCount);
            }
            if (kpiTotalScans && newlyAddedCount > 0) {
                const currentTotal = parseInt(kpiTotalScans.textContent.replace(/\D/g, '') || '0', 10);
                kpiTotalScans.textContent = (currentTotal + newlyAddedCount);
            }

        } catch (err) {
            alert(`❌ Error saving marks: ${err.message}`);
        } finally {
            btnFinalSubmit.innerHTML = `<i class="fa fa-check-circle mr-1"></i> Final Submit & Save Marks (${stagedSheets.size})`;
            updateStagingCounters();
        }
    };

    if (btnFinalSubmit) {
        btnFinalSubmit.addEventListener('click', function (e) {
            e.preventDefault();
            confirmFinalSubmit();
        });
    }

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

    // Initial counter sync on page load
    updateStagingCounters();
});
</script>
@endsection

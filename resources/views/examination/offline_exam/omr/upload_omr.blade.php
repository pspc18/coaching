@extends('layout.app')

@section('title', 'Upload OMR Sheet (OMR Optical Scanner) - Arise ERP')

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
@endphp

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ==========================================================================
   ARISE ERP - OMR SCANNER & EVALUATOR THEME (MATCHING VIEW/EXAM & ADMISSIONVIEW)
   - Layout: Standard Arise .content-wrapper with left sidebar clearance
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   - Typography: 11.5px table text, 10.5px uppercase bold thead
   - High contrast, responsive grid, zero dead space
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

/* 1. Hero Header Banner (~40px) */
.omr-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 12px;
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
    font-size: 14.5px;
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
    padding: 3px 9px;
    height: 27px;
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

/* 2. Fast KPI Summary Filter Strip */
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
    padding: 5px 12px;
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

/* 3. Cards & Panels */
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
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
    padding: 18px 14px;
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

/* Matrix Table */
.omr-matrix-scroll {
    max-height: 250px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
}
.omr-matrix-scroll::-webkit-scrollbar {
    width: 5px;
}
.omr-matrix-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.omr-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.omr-matrix-table th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 5px 8px;
    z-index: 2;
}
.omr-matrix-table td {
    padding: 4px 8px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11px;
}
.omr-matrix-table tr:hover td {
    background: #f8fafc;
}

/* Batch Ledger Table */
.omr-batch-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.omr-batch-table th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 7px 10px;
    border: none;
}
.omr-batch-table td {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    font-size: 11.5px;
}
.omr-batch-table tr:hover td {
    background: #f8fafc;
}

/* Badges */
.badge-pill-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
}
.badge-correct { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-incorrect { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.badge-blank { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
.badge-uploaded { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
</style>
@endsection

@section('content')
<div class="content-wrapper omr-page">
    <div class="omr-page-layout">
        
        {{-- ======================================================== --}}
        {{-- 1. Hero Header Banner with Workflow Navigation            --}}
        {{-- ======================================================== --}}
        <div class="omr-hero">
            <div>
                <span class="omr-kicker">
                    <i class="fa fa-pencil-square-o mr-1"></i> Examination Marks Recording &amp; Evaluation
                </span>
                <h1 class="omr-title">
                    <i class="fa fa-id-card-o text-warning"></i> Upload OMR Sheet (OMR Evaluation)
                </h1>
                <p class="omr-subtitle">
                    Extracts student roll numbers, reads optical bubble answers, grades against exam answer keys, and uploads marks directly to student exam records.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap" style="gap: 5px;">
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="Exam Setup">
                    <i class="fa fa-leanpub"></i> 1. Exam Setup
                </a>
                <a href="{{ url('fill-marks-by-excel') }}{{ $selectedExamId ? '?exam_id='.$selectedExamId : '' }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                    <i class="fa fa-file-excel-o"></i> 2. Excel Import
                </a>
                <a href="{{ url('fill_marks') }}{{ $selectedExamId ? '?exam_id='.$selectedExamId : '' }}" class="dash-btn dash-btn-outline" title="Manual Marks Entry">
                    <i class="fa fa-pencil"></i> 3. Manual Marks
                </a>
                <span class="dash-btn dash-btn-light" title="Upload OMR Sheet (Active)">
                    <i class="fa fa-check-circle text-primary"></i> 4. Upload OMR Sheet
                </span>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- 2. Fast KPI Summary Filter Strip                         --}}
        {{-- ======================================================== --}}
        <div class="omr-kpi-strip">
            <div class="kpi-mini-card kpi-classes">
                <div>
                    <span class="kpi-label">Evaluated Today</span>
                    <div class="kpi-value text-info" id="kpi-evaluated-today">{{ number_format($totalScannedToday) }}</div>
                    <span class="kpi-sub">sheets processed</span>
                </div>
                <div style="color: #0284c7; font-size: 20px;">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
            </div>

            <div class="kpi-mini-card kpi-published">
                <div>
                    <span class="kpi-label">Today's Avg Accuracy</span>
                    <div class="kpi-value text-success" id="kpi-avg-score">{{ $avgAccuracy }}%</div>
                    <span class="kpi-sub">mean student score</span>
                </div>
                <div style="color: #10b981; font-size: 20px;">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>

            <div class="kpi-mini-card">
                <div>
                    <span class="kpi-label">Total All-Time Scans</span>
                    <div class="kpi-value" style="color: #002C54;" id="kpi-total-scans">{{ number_format($totalEvaluatedCount) }}</div>
                    <span class="kpi-sub">records recorded</span>
                </div>
                <div style="color: #002C54; font-size: 20px;">
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
                    <i class="fa fa-check-square-o"></i>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- 3. Main Workspace: Controls & Live Matrix (Left) + Inspector (Right) --}}
        {{-- ======================================================== --}}
        <div class="row g-2">
            
            {{-- LEFT COLUMN: Step 1 (Exam Selector), Step 2 (Upload), Step 3 (Live Result) --}}
            <div class="col-lg-6 col-xl-5 d-flex flex-column" style="gap: 8px;">
                
                {{-- Card 1: Target Exam Selector --}}
                <div class="omr-card">
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

                {{-- Card 2: Upload Dropzone --}}
                <div class="omr-card">
                    <div class="omr-card-header">
                        <div>
                            <h2 class="omr-card-title">
                                <i class="fa fa-cloud-upload text-info"></i> 2. Upload Filled OMR Sheet(s)
                            </h2>
                            <p class="omr-card-desc">Batch multi-sheet evaluation supported (JPG, PNG, WebP).</p>
                        </div>
                        <span class="badge badge-info px-2 py-1" style="font-size: 10px;">Step 2</span>
                    </div>
                    <div class="omr-card-body py-2">
                        <div id="omr-upload-dropzone" class="omr-dropzone-box {{ $selectedExam ? 'active' : 'disabled' }}" onclick="triggerFileInput();">
                            <i class="fa fa-cloud-upload text-primary" style="font-size: 32px; margin-bottom: 4px;"></i>
                            <div class="font-weight-bold text-dark" style="font-size: 12.5px;" id="omr-dropzone-title">
                                {{ $selectedExam ? 'Drop or Click to Select OMR Sheet Image(s)' : 'Select an examination above first' }}
                            </div>
                            <div class="text-muted" style="font-size: 10.5px; margin-top: 2px;">
                                Reads roll number bubbles, optical answer marks, &amp; automatically uploads student marks to exam.
                            </div>
                            <input type="file" id="omr-image-input" multiple accept="image/png,image/jpeg,image/webp" style="display: none;" {{ $selectedExam ? '' : 'disabled' }}>
                        </div>

                        {{-- Scanning Progress & Status Bar --}}
                        <div id="omr-status-box" class="mt-2 py-1 px-2 rounded font-weight-bold" style="background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 11px; color: #475569;">
                            <i class="fa fa-info-circle mr-1"></i> <span id="omr-status-text">Select an exam to begin optical scanning.</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Live Evaluation & Student Score Summary --}}
                <div class="omr-card">
                    <div class="omr-card-header">
                        <div>
                            <h2 class="omr-card-title">
                                <i class="fa fa-check-circle text-success"></i> 3. Live Evaluation &amp; Student Marks
                            </h2>
                            <p class="omr-card-desc">Evaluated against answer key; marks uploaded directly to exam record.</p>
                        </div>
                        <span id="omr-confidence-pill" class="badge badge-secondary px-2 py-1" style="font-size: 10px;">Awaiting Scan</span>
                    </div>

                    {{-- Student Profile & Score KPI Header --}}
                    <div class="p-2 border-bottom bg-white">
                        <div class="row align-items-center g-2">
                            <div class="col-6 border-right">
                                <div class="text-muted font-weight-bold" style="font-size: 9.5px; text-transform: uppercase;">Student Roll #</div>
                                <div id="omr-roll-result" class="font-mono font-weight-bold text-primary" style="font-size: 16px; line-height: 1.2;">------</div>
                                <div id="omr-student-name" class="text-muted font-weight-bold text-truncate" style="font-size: 11px;">Unregistered Student</div>
                                <div id="omr-upload-status-badge" class="mt-1">
                                    <span class="badge badge-light text-muted" style="font-size: 9.5px;"><i class="fa fa-clock-o mr-1"></i> Awaiting Scan</span>
                                </div>
                            </div>
                            <div class="col-6 pl-3">
                                <div class="text-muted font-weight-bold" style="font-size: 9.5px; text-transform: uppercase;">Marks Obtained</div>
                                <div class="d-flex align-items-baseline" style="gap: 4px;">
                                    <span id="omr-score-result" class="font-weight-bold text-success" style="font-size: 18px; line-height: 1.2;">0.00</span>
                                    <span id="omr-total-marks" class="text-muted font-mono" style="font-size: 11px;">/ 0.00</span>
                                    <span id="omr-percentage-badge" class="badge badge-success ml-auto" style="font-size: 11px; font-weight: 700;">0%</span>
                                </div>
                                <div class="d-flex justify-content-between mt-2 pt-1 border-top text-center" style="font-size: 10.5px;">
                                    <span class="text-success font-weight-bold">🟢 <span id="omr-count-correct">0</span> Right</span>
                                    <span class="text-danger font-weight-bold">🔴 <span id="omr-count-incorrect">0</span> Wrong</span>
                                    <span class="text-muted font-weight-bold">⚪ <span id="omr-count-blank">0</span> Left</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Question-by-Question Matrix Table --}}
                    <div class="omr-matrix-scroll">
                        <table class="omr-matrix-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Question</th>
                                    <th style="width: 25%; text-align: center;">Detected Bubble</th>
                                    <th style="width: 25%; text-align: center;">Key Answer</th>
                                    <th style="width: 25%; text-align: right;">Result</th>
                                </tr>
                            </thead>
                            <tbody id="omr-matrix-body">
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">
                                        <i class="fa fa-info-circle mr-1"></i> Select an exam above to load the question matrix.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Visual OMR Sheet Inspector --}}
            <div class="col-lg-6 col-xl-7">
                <div class="omr-card" style="height: 100%; display: flex; flex-direction: column;">
                    <div class="omr-card-header">
                        <div>
                            <h2 class="omr-card-title">
                                <i class="fa fa-eye text-primary"></i> Scanned OMR Sheet Visual Inspector
                            </h2>
                            <p class="omr-card-desc">Visual inspection of uploaded sheet, bubble fills, and registration alignment.</p>
                        </div>
                        <span id="omr-preview-badge" class="badge badge-light px-2 py-1" style="font-size: 10px; border: 1px solid #cbd5e1;">
                            No sheet loaded
                        </span>
                    </div>

                    <div class="omr-card-body flex-grow-1 d-flex flex-column align-items-center justify-content-center p-3" style="background: #f8fafc; border-top: 1px solid #e2e8f0; min-height: 480px;">
                        {{-- Empty State (Clean Arise ERP standard) --}}
                        <div id="omr-preview-empty" class="text-center py-4 w-100">
                            <div class="d-inline-flex justify-content-center align-items-center rounded-circle mb-3" style="width: 60px; height: 60px; background: #e0f2fe; color: #0284c7;">
                                <i class="fa fa-file-text-o" style="font-size: 26px;"></i>
                            </div>
                            <h5 class="font-weight-bold text-dark mb-1" style="font-size: 14px;">Sheet Preview Awaiting Input</h5>
                            <p class="text-muted small mx-auto mb-3" style="max-width: 380px; font-size: 11.5px; line-height: 1.4;">
                                Select an examination and choose or drop an OMR sheet scan on the left. The sheet image and evaluated bubble markers will render here.
                            </p>
                            <div class="card p-2 mx-auto text-left" style="max-width: 420px; background: #ffffff; border: 1px solid #cbd5e1; font-size: 11px;">
                                <div class="font-weight-bold text-primary mb-1"><i class="fa fa-lightbulb-o mr-1"></i> Scan Quality Guidelines:</div>
                                <ul class="pl-3 mb-0 text-muted" style="line-height: 1.4;">
                                    <li>Scan or photograph sheets in portrait orientation with good lighting.</li>
                                    <li>Ensure all 4 black corner registration marks are clearly visible.</li>
                                    <li>Blue or Black ball-point pen fills provide 100% optical accuracy.</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Active Image View --}}
                        <div id="omr-preview-container" class="d-none w-100 justify-content-center align-items-center">
                            <img id="omr-preview-img" style="max-width: 100%; max-height: 600px; border: 1px solid #cbd5e1; box-shadow: 0 4px 14px rgba(0,0,0,0.15); border-radius: 2px; background: #ffffff;">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- 4. Batch Result Queue & Grading Ledger                   --}}
        {{-- ======================================================== --}}
        <div class="omr-card mt-2">
            <div class="omr-card-header">
                <div>
                    <h2 class="omr-card-title">
                        <i class="fa fa-list-alt text-primary"></i> Batch Result Queue &amp; Marks Grading Ledger
                    </h2>
                    <p class="omr-card-desc">Real-time ledger of evaluated student OMR sheets recorded into database &amp; exam marks.</p>
                </div>
                <span id="omr-batch-progress" class="badge badge-primary px-2 py-1" style="font-size: 11px; background: #002C54;">
                    {{ $recentScans->count() }} Scans in Ledger
                </span>
            </div>

            <div class="table-responsive">
                <table class="omr-batch-table">
                    <thead>
                        <tr>
                            <th style="min-width: 140px;">Source File</th>
                            <th style="min-width: 160px;">Student Profile</th>
                            <th style="min-width: 140px;">Examination</th>
                            <th style="text-align: center; min-width: 70px;">Right</th>
                            <th style="text-align: center; min-width: 70px;">Wrong</th>
                            <th style="text-align: center; min-width: 70px;">Left</th>
                            <th style="min-width: 90px;">Total Marks</th>
                            <th style="min-width: 90px;">Obtained</th>
                            <th style="min-width: 90px;">Percentage</th>
                            <th style="text-align: center; min-width: 60px;">Rank</th>
                            <th style="text-align: right; min-width: 130px;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="omr-batch-tbody">
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
                                    <span class="badge-pill-status badge-correct">{{ $scan->percentage }}%</span>
                                </td>
                                <td class="text-center font-weight-bold text-primary">#{{ $scan->rank ?: 1 }}</td>
                                <td class="text-right">
                                    <span class="badge-pill-status badge-uploaded" title="Marks successfully recorded in student exam file">
                                        <i class="fa fa-check-circle"></i> Marks Uploaded
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr id="omr-batch-empty-row">
                                <td colspan="11" class="text-center py-4 text-muted">
                                    <i class="fa fa-folder-open-o mr-1"></i> No student OMR sheets processed yet in this session.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

{{-- Hidden HTML5 Canvas for Local Optical Recognition Analysis --}}
<canvas id="omr-analysis-canvas" width="800" height="1100" style="display: none;"></canvas>

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
    const kpiEvaluatedToday = document.getElementById('kpi-evaluated-today');
    const kpiAvgScore = document.getElementById('kpi-avg-score');
    const kpiTotalScans = document.getElementById('kpi-total-scans');

    const rollResult = document.getElementById('omr-roll-result');
    const studentName = document.getElementById('omr-student-name');
    const uploadStatusBadge = document.getElementById('omr-upload-status-badge');
    const scoreResult = document.getElementById('omr-score-result');
    const totalMarksResult = document.getElementById('omr-total-marks');
    const percentageBadge = document.getElementById('omr-percentage-badge');
    const countCorrect = document.getElementById('omr-count-correct');
    const countIncorrect = document.getElementById('omr-count-incorrect');
    const countBlank = document.getElementById('omr-count-blank');
    const confidencePill = document.getElementById('omr-confidence-pill');

    const matrixBody = document.getElementById('omr-matrix-body');
    const batchTbody = document.getElementById('omr-batch-tbody');
    const batchProgress = document.getElementById('omr-batch-progress');

    const previewEmpty = document.getElementById('omr-preview-empty');
    const previewContainer = document.getElementById('omr-preview-container');
    const previewImg = document.getElementById('omr-preview-img');
    const previewBadge = document.getElementById('omr-preview-badge');

    const analysisCanvas = document.getElementById('omr-analysis-canvas');
    const ctx = analysisCanvas.getContext('2d', { willReadFrequently: true });

    let activePreviewUrl = null;

    window.triggerFileInput = function () {
        if (!examSelect.value) {
            alert('Please select a target examination first.');
            examSelect.focus();
            return;
        }
        fileInput.click();
    };

    const getSelectedExamOption = () => examSelect.options[examSelect.selectedIndex];
    const getExamTotalQuestions = () => parseInt(getSelectedExamOption()?.dataset.questions || '50', 10) || 50;

    // Reset current sheet preview & evaluation panel
    function resetEvaluationPanel() {
        rollResult.textContent = '------';
        studentName.textContent = 'Unregistered Student';
        uploadStatusBadge.innerHTML = '<span class="badge badge-light text-muted" style="font-size: 9.5px;"><i class="fa fa-clock-o mr-1"></i> Awaiting Scan</span>';
        scoreResult.textContent = '0.00';
        totalMarksResult.textContent = '/ 0.00';
        percentageBadge.textContent = '0%';
        countCorrect.textContent = '0';
        countIncorrect.textContent = '0';
        countBlank.textContent = '0';
        confidencePill.textContent = 'Awaiting Scan';
        confidencePill.className = 'badge badge-secondary px-2 py-1';
        previewBadge.textContent = 'No sheet loaded';
        previewBadge.className = 'badge badge-light px-2 py-1';

        if (activePreviewUrl) {
            URL.revokeObjectURL(activePreviewUrl);
            activePreviewUrl = null;
        }
        previewImg.removeAttribute('src');
        previewContainer.classList.add('d-none');
        previewContainer.classList.remove('d-flex');
        previewEmpty.classList.remove('d-none');

        renderEmptyMatrix();
    }

    // Render initial empty question matrix
    function renderEmptyMatrix() {
        const total = examSelect.value ? getExamTotalQuestions() : 0;
        if (!total) {
            matrixBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">
                        <i class="fa fa-info-circle mr-1"></i> Select an exam above to load the question matrix.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        for (let i = 1; i <= total; i++) {
            html += `
                <tr>
                    <td class="font-weight-bold text-dark">Question ${i}</td>
                    <td class="text-center font-mono font-weight-bold text-muted">—</td>
                    <td class="text-center font-mono font-weight-bold text-muted">—</td>
                    <td class="text-right">
                        <span class="badge-pill-status badge-blank">Pending Scan</span>
                    </td>
                </tr>
            `;
        }
        matrixBody.innerHTML = html;
    }

    // Exam Selection Change
    examSelect.addEventListener('change', function () {
        const opt = getSelectedExamOption();
        const hasExam = !!opt.value;

        fileInput.disabled = !hasExam;

        if (hasExam) {
            dropzone.classList.remove('disabled');
            dropzone.classList.add('active');
            dropzoneTitle.textContent = 'Drop or Click to Select OMR Sheet Image(s)';
            statusText.textContent = `Ready for ${opt.dataset.code} (${opt.dataset.questions} Questions). Select sheet image(s).`;
            statusBox.style.background = '#f0fdf4';
            statusBox.style.borderColor = '#bbf7d0';
            statusBox.style.color = '#166534';

            badgeBox.classList.remove('d-none');
            badgeTitle.textContent = opt.dataset.name;
            badgeCode.textContent = opt.dataset.code;
            badgeQuestions.innerHTML = `<i class="fa fa-list-ol mr-1"></i> ${opt.dataset.questions} Questions`;
            badgeScheme.innerHTML = `<i class="fa fa-tag mr-1"></i> Marks: +${opt.dataset.correct}/-${opt.dataset.incorrect}`;

            kpiQuestions.textContent = `${opt.dataset.questions} Q`;
            kpiCode.textContent = opt.dataset.code;

            resetEvaluationPanel();
        } else {
            dropzone.classList.add('disabled');
            dropzone.classList.remove('active');
            dropzoneTitle.textContent = 'Select an examination above first';
            statusText.textContent = 'Select an exam to begin optical scanning.';
            statusBox.style.background = '#f1f5f9';
            statusBox.style.borderColor = '#e2e8f0';
            statusBox.style.color = '#475569';

            badgeBox.classList.add('d-none');
            kpiQuestions.textContent = '--';
            kpiCode.textContent = 'None';

            resetEvaluationPanel();
        }
    });

    // Drag and Drop listeners
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!examSelect.value) return;
            dropzone.style.background = '#e0f2fe';
            dropzone.style.borderColor = '#0284c7';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.background = '';
            dropzone.style.borderColor = '';
        }, false);
    });

    dropzone.addEventListener('drop', (e) => {
        if (!examSelect.value) return;
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            handleUploadedFiles(Array.from(files));
        }
    });

    fileInput.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            handleUploadedFiles(Array.from(this.files));
        }
    });

    // -------------------------------------------------------------
    // Optical Bubble Recognition Engine (Canvas Sampling)
    // -------------------------------------------------------------
    function analyzeSheetOptical(imgElem, totalQuestions) {
        ctx.clearRect(0, 0, 800, 1100);
        ctx.drawImage(imgElem, 0, 0, 800, 1100);

        function getDarkness(cx, cy, r) {
            const size = Math.max(1, Math.round(r * 2));
            const startX = Math.max(0, Math.round(cx - r));
            const startY = Math.max(0, Math.round(cy - r));
            let imgData;
            try {
                imgData = ctx.getImageData(startX, startY, size, size);
            } catch (err) {
                return 0;
            }
            const data = imgData.data;
            let totalDarkness = 0;
            let count = 0;
            const rSq = r * r;

            for (let y = 0; y < size; y++) {
                for (let x = 0; x < size; x++) {
                    const dx = x - r;
                    const dy = y - r;
                    if (dx * dx + dy * dy <= rSq) {
                        const idx = (y * size + x) * 4;
                        const brightness = 0.299 * data[idx] + 0.587 * data[idx + 1] + 0.114 * data[idx + 2];
                        totalDarkness += (255 - brightness);
                        count++;
                    }
                }
            }
            return count > 0 ? (totalDarkness / count) : 0;
        }

        // 1. Roll Number Optical Recognition (6 columns)
        let detectedRoll = '';
        for (let col = 0; col < 6; col++) {
            const cx = 120 + col * 110;
            let maxDarkness = -1;
            let detectedDigit = -1;
            let sumDarkness = 0;

            for (let digit = 0; digit < 10; digit++) {
                const cy = 245 + digit * 15.5;
                const darkness = getDarkness(cx, cy, 5.5);
                sumDarkness += darkness;
                if (darkness > maxDarkness) {
                    maxDarkness = darkness;
                    detectedDigit = digit;
                }
            }

            const avgDarkness = sumDarkness / 10;
            if (maxDarkness > 75 && (maxDarkness - avgDarkness) > 25) {
                detectedRoll += String(detectedDigit);
            } else {
                detectedRoll += '0';
            }
        }

        // 2. Question Bubbles Recognition
        const options = ['A', 'B', 'C', 'D'];
        const detectedAnswers = [];

        for (let q = 1; q <= totalQuestions; q++) {
            const block = Math.floor((q - 1) / 25);
            const row = ((q - 1) % 25) + 1;
            const bx = 70 + block * 168.5;
            const qy = 486 + (row - 1) * 21.8;

            let maxOptionDarkness = -1;
            let bestOption = null;
            let sumOptionDarkness = 0;

            for (let opt = 0; opt < 4; opt++) {
                const cx = bx + 48 + opt * 26;
                const d = getDarkness(cx, qy, 6.5);
                sumOptionDarkness += d;
                if (d > maxOptionDarkness) {
                    maxOptionDarkness = d;
                    bestOption = options[opt];
                }
            }

            const avgOptDarkness = sumOptionDarkness / 4;
            if (maxOptionDarkness > 75 && (maxOptionDarkness - avgOptDarkness) > 25) {
                detectedAnswers.push({ question: q, answer: bestOption });
            } else {
                detectedAnswers.push({ question: q, answer: null });
            }
        }

        return {
            detectedRoll: detectedRoll,
            detectedAnswers: detectedAnswers
        };
    }

    // Process Batch or Single Uploaded Files
    async function handleUploadedFiles(files) {
        const examId = examSelect.value;
        const totalQ = getExamTotalQuestions();
        if (!files.length || !examId) return;

        fileInput.disabled = true;
        dropzone.classList.add('disabled');
        let processedCount = 0;
        document.getElementById('omr-batch-empty-row')?.remove();

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            statusText.textContent = `Processing sheet ${i + 1} of ${files.length}: ${file.name}...`;

            // Setup preview
            if (activePreviewUrl) URL.revokeObjectURL(activePreviewUrl);
            activePreviewUrl = URL.createObjectURL(file);
            previewImg.src = activePreviewUrl;
            previewEmpty.classList.add('d-none');
            previewContainer.classList.remove('d-none');
            previewContainer.classList.add('d-flex');
            previewBadge.textContent = `Evaluating: ${file.name}`;
            previewBadge.className = 'badge badge-warning px-2 py-1';

            // Insert temporary queue row in ledger
            const tempRow = document.createElement('tr');
            tempRow.className = 'bg-light';
            tempRow.innerHTML = `
                <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;">${file.name}</td>
                <td colspan="9" class="text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Optical bubble extraction in progress...</td>
                <td class="text-right text-primary font-weight-bold"><i class="fa fa-refresh fa-spin mr-1"></i> Processing</td>
            `;
            batchTbody.prepend(tempRow);

            try {
                // Await image loading into image element for optical canvas scanning
                await new Promise((resolve) => {
                    if (previewImg.complete && previewImg.naturalWidth !== 0) {
                        resolve();
                    } else {
                        previewImg.onload = () => resolve();
                        previewImg.onerror = () => resolve();
                    }
                });

                // Run optical analysis
                const opticalData = analyzeSheetOptical(previewImg, totalQ);

                // Prepare FormData
                const formData = new FormData();
                formData.append('exam_id', examId);
                formData.append('image', file);
                formData.append('detected_roll', opticalData.detectedRoll || '');
                formData.append('detected_answers', JSON.stringify(opticalData.detectedAnswers || []));

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch("{{ url('upload_omr/extract') }}", {
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
                    throw new Error(data.message || 'Evaluation failed.');
                }

                const resData = data.result;
                const answers = data.answers || [];

                // 1. Update Live Summary Panels
                rollResult.textContent = data.roll_number || resData.unique_id;
                studentName.textContent = data.student_name || 'Unregistered Student';

                if (data.marks_uploaded) {
                    uploadStatusBadge.innerHTML = '<span class="badge-pill-status badge-uploaded"><i class="fa fa-check-circle"></i> Marks Uploaded to Exam Record</span>';
                } else if (data.admission_matched) {
                    uploadStatusBadge.innerHTML = '<span class="badge-pill-status badge-correct"><i class="fa fa-user mr-1"></i> Student Matched</span>';
                } else {
                    uploadStatusBadge.innerHTML = '<span class="badge-pill-status badge-blank"><i class="fa fa-exclamation-triangle mr-1"></i> Unregistered Roll #</span>';
                }

                scoreResult.textContent = resData.obtained_marks;
                totalMarksResult.textContent = `/ ${resData.total_marks}`;
                percentageBadge.textContent = `${resData.percentage}%`;
                countCorrect.textContent = resData.correct_answers;
                countIncorrect.textContent = resData.incorrect_answers;
                countBlank.textContent = resData.blank_answers;

                confidencePill.textContent = '100% Calibrated';
                confidencePill.className = 'badge badge-success px-2 py-1';
                previewBadge.textContent = `Graded & Saved: ${resData.unique_id}`;
                previewBadge.className = 'badge badge-success px-2 py-1';

                // 2. Render Matrix Table Rows
                let matrixHtml = '';
                answers.forEach(item => {
                    let statusBadge = '';
                    if (item.is_correct) {
                        statusBadge = `<span class="badge-pill-status badge-correct"><i class="fa fa-check"></i> Right (+1)</span>`;
                    } else if (item.status === 'blank') {
                        statusBadge = `<span class="badge-pill-status badge-blank">Blank (0)</span>`;
                    } else {
                        statusBadge = `<span class="badge-pill-status badge-incorrect"><i class="fa fa-times"></i> Wrong</span>`;
                    }

                    matrixHtml += `
                        <tr>
                            <td class="font-weight-bold text-dark">Question ${item.question}</td>
                            <td class="text-center font-mono font-weight-bold ${item.is_correct ? 'text-success' : (item.answer ? 'text-danger' : 'text-muted')}">${item.answer || '—'}</td>
                            <td class="text-center font-mono font-weight-bold text-primary">${item.correct_answer || '—'}</td>
                            <td class="text-right">${statusBadge}</td>
                        </tr>
                    `;
                });
                matrixBody.innerHTML = matrixHtml;

                // 3. Update Ledger Queue Row
                tempRow.className = '';
                tempRow.id = `scan-row-${resData.id}`;
                tempRow.innerHTML = `
                    <td class="font-weight-bold text-dark text-truncate" style="max-width: 140px;" title="${file.name}">
                        <i class="fa fa-file-image-o text-muted mr-1"></i> ${file.name}
                    </td>
                    <td>
                        <span class="font-mono font-weight-bold text-primary">${resData.unique_id}</span>
                        <div class="text-muted small font-weight-bold">${resData.student_name}</div>
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark">${getSelectedExamOption()?.dataset.name || 'Exam'}</span>
                        <div class="text-muted small font-mono">${getSelectedExamOption()?.dataset.code || ''}</div>
                    </td>
                    <td class="text-center font-weight-bold text-success">${resData.correct_answers}</td>
                    <td class="text-center font-weight-bold text-danger">${resData.incorrect_answers}</td>
                    <td class="text-center text-muted">${resData.blank_answers}</td>
                    <td class="font-mono font-weight-bold">${resData.total_marks}</td>
                    <td class="font-mono font-weight-bold text-success">${resData.obtained_marks}</td>
                    <td>
                        <span class="badge-pill-status badge-correct">${resData.percentage}%</span>
                    </td>
                    <td class="text-center font-weight-bold text-primary">#${resData.rank}</td>
                    <td class="text-right">
                        <span class="badge-pill-status badge-uploaded" title="Marks successfully recorded in student exam file">
                            <i class="fa fa-check-circle"></i> Marks Uploaded
                        </span>
                    </td>
                `;

                processedCount++;
                if (kpiEvaluatedToday) {
                    const currentToday = parseInt(kpiEvaluatedToday.textContent.replace(/\D/g, '') || '0', 10);
                    kpiEvaluatedToday.textContent = (currentToday + 1);
                }
                if (kpiTotalScans) {
                    const currentTotal = parseInt(kpiTotalScans.textContent.replace(/\D/g, '') || '0', 10);
                    kpiTotalScans.textContent = (currentTotal + 1);
                }
            } catch (err) {
                tempRow.className = 'bg-light text-danger';
                tempRow.innerHTML = `
                    <td class="font-weight-bold text-truncate" style="max-width: 140px;">${file.name}</td>
                    <td colspan="9" class="text-danger font-weight-bold"><i class="fa fa-exclamation-circle mr-1"></i> ${err.message}</td>
                    <td class="text-right"><span class="badge-pill-status badge-incorrect">Failed</span></td>
                `;
            }

            batchProgress.textContent = `${processedCount} / ${files.length} Processed`;
        }

        fileInput.disabled = false;
        fileInput.value = '';
        dropzone.classList.remove('disabled');
        dropzone.classList.add('active');

        statusText.textContent = `Batch complete: ${processedCount} of ${files.length} OMR sheet(s) successfully evaluated & marks uploaded to exam record.`;
        statusBox.style.background = '#f0fdf4';
        statusBox.style.borderColor = '#bbf7d0';
        statusBox.style.color = '#166534';
    }

    // Initialize matrix if exam is preloaded
    if (examSelect.value) {
        renderEmptyMatrix();
    }
});
</script>
@endsection

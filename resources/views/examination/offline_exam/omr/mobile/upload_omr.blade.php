@extends('layout.mobile_app')

@section('title', 'Upload & Evaluate OMR Sheets - Arise ERP')

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

    $examSubjectMap = [];
    if (!empty($selectedExam->subject_question_ranges)) {
        $ranges = is_string($selectedExam->subject_question_ranges)
            ? json_decode($selectedExam->subject_question_ranges, true)
            : $selectedExam->subject_question_ranges;
        if (is_array($ranges)) {
            foreach ($ranges as $r) {
                if (!empty($r['subject_id']) && !empty($r['subject_name']) && !str_starts_with($r['subject_name'], 'Subject #')) {
                    $examSubjectMap[$r['subject_id']] = $r['subject_name'];
                }
            }
        }
    }

    $allSavedMap = [];
    foreach ($recentScans as $sc) {
        if (!empty($sc->id)) {
            $allSavedMap[$sc->id] = $sc;
        }
    }
@endphp

@section('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ==========================================================================
   ARISE ERP - MOBILE OMR EXTRACTOR WORKSPACE THEME
   - High contrast, touch-optimized card layout
   - Direct camera snap & live in-app viewfinder scanner
   - Pinned floating submit action dock
   ========================================================================== */

.mob-omr-wrapper {
    padding-bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 65px);
}

/* 1. Mobile Hero Banner */
.mob-omr-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-omr-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-omr-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-omr-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-omr-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 3px;
    text-align: center;
}
.mob-omr-kpi-tag {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-omr-kpi-val {
    font-size: 13px;
    font-weight: 900;
    line-height: 1;
}

/* Hero Quick Links */
.mob-omr-action-row {
    display: flex;
    gap: 6px;
}
.mob-hero-btn {
    flex: 1;
    height: 28px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-hero-white {
    background: #ffffff;
    color: #002C54;
    border: 1px solid #ffffff;
}
.btn-hero-glass {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. Step Cards */
.mob-step-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-step-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-step-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.mob-step-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    background: #e0f2fe;
    color: #0369a1;
}

/* Form Controls */
.mob-form-group {
    margin-bottom: 7px;
}
.mob-form-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
}
.mob-form-select {
    width: 100%;
    height: 34px;
    font-size: 12px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    padding: 4px 8px;
    font-weight: 600;
}
.mob-form-select:focus {
    outline: none;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1);
}

/* Exam Info Strip */
.mob-exam-badge-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 3px;
    padding: 7px 9px;
    margin-top: 6px;
}
.mob-exam-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
    margin-top: 4px;
}
.mob-exam-meta-item {
    font-size: 10.5px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* 3. Upload & Capture Action Hub */
.mob-capture-hub {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 4px;
    margin-bottom: 8px;
}
.mob-capture-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 10px 4px;
    border-radius: 4px;
    text-align: center;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    background: #ffffff;
}
.mob-capture-btn:active {
    transform: scale(0.98);
}
.btn-capture-camera {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
}
.btn-capture-live {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
}
.btn-capture-browse {
    background: #fdf2f8;
    border-color: #fbcfe8;
    color: #be185d;
}
.mob-capture-icon {
    font-size: 20px;
    margin-bottom: 4px;
}
.mob-capture-title {
    font-size: 11px;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-capture-subtitle {
    font-size: 8.5px;
    color: #64748b;
    line-height: 1;
}

/* Progress bar container */
.mob-progress-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px;
    margin-bottom: 8px;
}

/* 4. Verification Matrix (Card Layout) */
.mob-filter-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
}
.mob-tab-btn {
    padding: 4px 9px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
}
.mob-tab-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Student Verification Card */
.mob-student-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 9px;
    margin-bottom: 7px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    position: relative;
    transition: border-color 0.15s ease;
}
.mob-student-card.card-scanned {
    border-color: #7dd3fc;
    background: #f0f9ff;
}
.mob-student-card.card-saved {
    border-color: #86efac;
    background: #f0fdf4;
}
.mob-student-card.card-unmatched {
    border-color: #facc15;
    background: #fffbeb;
}
.mob-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-card-profile {
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-card-avatar {
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}
.mob-student-name {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    line-height: 1.2;
}
.mob-student-adm {
    font-size: 10px;
    color: #64748b;
    font-weight: 500;
}

/* Card Body Details */
.mob-card-body-details {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
}
.mob-sheet-thumb-img {
    width: 44px;
    height: 44px;
    object-fit: cover;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-sheet-thumb-placeholder {
    width: 44px;
    height: 44px;
    border-radius: 2px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    color: #94a3b8;
    flex-shrink: 0;
}

/* Card Actions */
.mob-card-foot-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 5px;
    border-top: 1px dashed #e2e8f0;
}
.mob-card-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-card-action {
    height: 26px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    cursor: pointer;
    border: 1px solid transparent;
}

/* 5. Sticky Floating Bottom Action Dock */
.mob-floating-action-dock {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    background: #ffffff;
    border: 1px solid #002C54;
    border-radius: 6px;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 25px rgba(0, 44, 84, 0.25), 0 2px 6px rgba(0,0,0,0.1);
    z-index: 1040;
}
.mob-dock-summary {
    display: flex;
    flex-direction: column;
}
.mob-dock-summary-count {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.1;
}
.mob-dock-summary-sub {
    font-size: 9.5px;
    color: #64748b;
}
.mob-dock-buttons {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-dock-clear {
    height: 32px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
}
.btn-dock-submit {
    height: 32px;
    padding: 0 14px;
    font-size: 11.5px;
    font-weight: 800;
    border-radius: 4px;
    background: #16a34a;
    color: #ffffff;
    border: none;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-dock-submit:disabled {
    background: #94a3b8;
    box-shadow: none;
    cursor: not-allowed;
    opacity: 0.7;
}

/* 6. Live In-App Camera Viewfinder Modal */
#modalLiveCamera .modal-dialog {
    margin: 0;
    height: 100vh;
    max-width: 100%;
}
#modalLiveCamera .modal-content {
    height: 100vh;
    border-radius: 0;
    background: #000000;
    color: #ffffff;
    display: flex;
    flex-direction: column;
}
.mob-camera-viewport {
    position: relative;
    flex: 1;
    background: #000000;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
#camera-stream-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.mob-camera-guide-frame {
    position: absolute;
    top: 5%;
    bottom: 5%;
    left: 8%;
    right: 8%;
    border: 2px dashed rgba(74, 222, 128, 0.85);
    border-radius: 6px;
    pointer-events: none;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.45);
}
.mob-camera-guide-corner {
    position: absolute;
    width: 16px;
    height: 16px;
    border-color: #22c55e;
    border-style: solid;
}
.mob-guide-tl { top: -2px; left: -2px; border-width: 3px 0 0 3px; }
.mob-guide-tr { top: -2px; right: -2px; border-width: 3px 3px 0 0; }
.mob-guide-bl { bottom: -2px; left: -2px; border-width: 0 0 3px 3px; }
.mob-guide-br { bottom: -2px; right: -2px; border-width: 0 3px 3px 0; }

.mob-camera-bottom-bar {
    background: rgba(0, 0, 0, 0.85);
    padding: 12px 16px calc(12px + var(--safe-bottom, 0px));
    display: flex;
    align-items: center;
    justify-content: space-around;
    z-index: 10;
}
.mob-shutter-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #ffffff;
    border: 4px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #002C54;
    font-size: 22px;
    cursor: pointer;
    transition: transform 0.1s ease;
}
.mob-shutter-btn:active {
    transform: scale(0.92);
    background: #4ade80;
}
.mob-camera-tool-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    cursor: pointer;
}

/* History Accordion */
.mob-history-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 9px;
    margin-bottom: 6px;
}
</style>
@endsection

@section('content')
<div class="px-2 pt-2 mob-omr-wrapper">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-omr-hero">
        <div class="mob-omr-hero-top">
            <div class="mob-omr-hero-title">
                <i class="fa fa-crosshairs text-info"></i> OMR Sheet Evaluator
            </div>
            <span class="badge badge-light text-dark font-weight-bold" style="font-size:9.5px; border-radius:2px; padding:2px 5px;">
                <i class="fa fa-bolt text-warning mr-1"></i> AI Powered
            </span>
        </div>

        <div class="mob-omr-kpi-grid">
            <div class="mob-omr-kpi-col">
                <div class="mob-omr-kpi-tag">Today</div>
                <div class="mob-omr-kpi-val" id="kpi-today-scans" style="color:#ffffff;">{{ $totalScannedToday }}</div>
            </div>
            <div class="mob-omr-kpi-col">
                <div class="mob-omr-kpi-tag">Accuracy</div>
                <div class="mob-omr-kpi-val" style="color:#4ade80;">{{ $avgAccuracy }}%</div>
            </div>
            <div class="mob-omr-kpi-col">
                <div class="mob-omr-kpi-tag">Evaluated</div>
                <div class="mob-omr-kpi-val" id="kpi-total-scans" style="color:#38bdf8;">{{ $totalEvaluatedCount }}</div>
            </div>
            <div class="mob-omr-kpi-col">
                <div class="mob-omr-kpi-tag">Enrolled</div>
                <div class="mob-omr-kpi-val" style="color:#facc15;">{{ count($students) }}</div>
            </div>
        </div>

        <div class="mob-omr-action-row">
            <a href="{{ url('view/exam?layout=mobile') }}" class="mob-hero-btn btn-hero-white">
                <i class="fa fa-list"></i> View Exams
            </a>
            <a href="{{ url('exam_wise_report') }}" class="mob-hero-btn btn-hero-glass">
                <i class="fa fa-bar-chart"></i> Exam Reports
            </a>
        </div>
    </div>

    {{-- 2. Step 1: Target Exam Selection --}}
    <div class="mob-step-card">
        <div class="mob-step-header">
            <h2 class="mob-step-title">
                <i class="fa fa-leanpub text-primary"></i> 1. Select Examination
            </h2>
            <span class="mob-step-pill">Step 1</span>
        </div>

        {{-- Course Selector --}}
        <div class="mob-form-group">
            <label class="mob-form-label"><i class="fa fa-graduation-cap text-primary mr-1"></i> Course / Class <span class="text-danger">*</span></label>
            <select id="omr-class-select" class="mob-form-select">
                <option value="">-- Choose Course --</option>
                @foreach($classType ?? [] as $cl)
                    <option value="{{ $cl->id }}" {{ $requestedClassId == $cl->id ? 'selected' : '' }}>
                        {{ $cl->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Batch Selector --}}
        <div class="mob-form-group">
            <label class="mob-form-label"><i class="fa fa-users text-warning mr-1"></i> Batch (Shift)</label>
            <select id="omr-batch-select" class="mob-form-select" {{ empty($requestedClassId) ? 'disabled' : '' }}>
                <option value="">-- All Batches (Course-Wide) --</option>
                @foreach($batches ?? [] as $b)
                    <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
                        {{ $b->name }} {{ !empty($b->shift) ? '('.$b->shift.')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Exam Selector --}}
        <div class="mob-form-group mb-1">
            <label class="mob-form-label"><i class="fa fa-book text-info mr-1"></i> Examination <span class="text-danger">*</span></label>
            <select id="omr-exam-select" class="mob-form-select">
                <option value="">-- Choose Examination --</option>
                @foreach($allExams ?? [] as $ex)
                    @php
                        $exCode = $ex->code ?: ($ex->exam_code ?: 'EXAM-'.$ex->id);
                        $exDateStr = !empty($ex->exam_date) ? date('d-m-Y', strtotime($ex->exam_date)) : '';
                        $rangesJson = is_string($ex->subject_question_ranges) ? $ex->subject_question_ranges : json_encode($ex->subject_question_ranges ?? []);
                    @endphp
                    <option value="{{ $ex->id }}" 
                            data-classid="{{ $ex->class_type_id }}"
                            data-batchid="{{ $ex->batch_id }}"
                            data-code="{{ $exCode }}"
                            data-name="{{ $ex->name }}"
                            data-date="{{ $exDateStr }}"
                            data-questions="{{ $ex->number_of_questions ?: 50 }}"
                            data-maxmarks="{{ $ex->exam_maximum_marks ?: 100 }}"
                            data-pos="{{ $ex->marks_per_question ?? 1 }}"
                            data-neg="{{ $ex->negative_marks_per_question ?? 0 }}"
                            data-ranges="{{ htmlspecialchars($rangesJson, ENT_QUOTES, 'UTF-8') }}"
                            {{ $selectedExamId == $ex->id ? 'selected' : '' }}>
                        {{ $ex->name }} ({{ $exDateStr ?: 'No Date' }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Exam Info Badge --}}
        @if($selectedExam)
            @php
                $curCode = $selectedExam->code ?: ($selectedExam->exam_code ?: 'EXAM-'.$selectedExam->id);
                $curDate = !empty($selectedExam->exam_date) ? date('d M Y', strtotime($selectedExam->exam_date)) : 'N/A';
                $curQuestions = $selectedExam->number_of_questions ?: 50;
                $curMarks = $selectedExam->exam_maximum_marks ?: 100;
                $curPos = $selectedExam->marks_per_question ?? 1;
                $curNeg = $selectedExam->negative_marks_per_question ?? 0;
            @endphp
            <div class="mob-exam-badge-card" id="omr-selected-exam-badge">
                <div class="d-flex align-items-center justify-content-between">
                    <strong class="text-success font-weight-bold" style="font-size:11.5px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ $selectedExam->name }}
                    </strong>
                    <span class="badge badge-success font-mono" style="font-size:9.5px;">{{ $curCode }}</span>
                </div>
                <div class="mob-exam-meta-grid">
                    <div class="mob-exam-meta-item">
                        <i class="fa fa-question-circle text-primary"></i> <b>{{ $curQuestions }}</b> Questions
                    </div>
                    <div class="mob-exam-meta-item">
                        <i class="fa fa-trophy text-warning"></i> <b>{{ $curMarks }}</b> Max Marks
                    </div>
                    <div class="mob-exam-meta-item">
                        <i class="fa fa-plus-circle text-success"></i> +{{ $curPos }} / -{{ $curNeg }} Marking
                    </div>
                    <div class="mob-exam-meta-item">
                        <i class="fa fa-calendar text-info"></i> {{ $curDate }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- 3. Step 2: Upload & Capture OMR Sheets Hub --}}
    <div class="mob-step-card">
        <div class="mob-step-header">
            <h2 class="mob-step-title">
                <i class="fa fa-cloud-upload text-info"></i> 2. Capture &amp; Upload Sheets
            </h2>
            <span class="mob-step-pill" style="background:#fce7f3; color:#9d174d;">Step 2</span>
        </div>

        <p class="text-muted mb-2" style="font-size:10.5px;">
            Snap OMR sheets with camera or browse files (PDF, JPG, PNG, WebP, HEIF). AI Extractor evaluates answers instantly.
        </p>

        {{-- Hidden File Inputs --}}
        {{-- Input 1: Native System Camera --}}
        <input type="file" id="mob-omr-camera-input" accept="image/*" capture="environment" style="display:none;">
        {{-- Input 2: Multiple File Selector / Gallery --}}
        <input type="file" id="mob-omr-file-input" accept="image/png,image/jpeg,image/webp,image/heic,image/heif,.heic,.heif,application/pdf,.pdf,image/*" multiple style="display:none;">

        {{-- 2 Touch Action Buttons (Live Scanner hidden as requested) --}}
        <div class="mob-capture-hub">
            {{-- Quick Camera Snap --}}
            <button type="button" class="mob-capture-btn btn-capture-camera" onclick="triggerCameraInput();">
                <i class="fa fa-camera mob-capture-icon"></i>
                <div class="mob-capture-title">Camera Snap</div>
                <div class="mob-capture-subtitle">Take single photo</div>
            </button>

            {{-- Live In-App Scanner (Temporarily hidden) --}}
            <button type="button" class="mob-capture-btn btn-capture-live" onclick="openLiveCameraModal();" style="display: none !important;">
                <i class="fa fa-video-camera mob-capture-icon"></i>
                <div class="mob-capture-title">Live Scanner</div>
                <div class="mob-capture-subtitle">Viewfinder batch</div>
            </button>

            {{-- Browse Files --}}
            <button type="button" class="mob-capture-btn btn-capture-browse" onclick="triggerFileInput();">
                <i class="fa fa-folder-open-o mob-capture-icon"></i>
                <div class="mob-capture-title">Browse Files</div>
                <div class="mob-capture-subtitle">PDF / Images</div>
            </button>
        </div>

        {{-- Batch Extraction Progress Bar --}}
        <div id="omr-batch-progress-wrapper" class="mob-progress-box d-none">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span id="omr-batch-status-text" class="font-weight-bold text-dark" style="font-size:10.5px;">
                    <i class="fa fa-refresh fa-spin text-primary mr-1"></i> Extracting sheets...
                </span>
                <span id="omr-batch-percent-text" class="font-weight-bold text-primary font-mono" style="font-size:11px;">0%</span>
            </div>
            <div class="progress" style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow:hidden;">
                <div id="omr-batch-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 0%;"></div>
            </div>
        </div>

        {{-- Status Notice --}}
        <div id="omr-status-box" style="font-size:10.5px;">
            <span id="omr-status-text" class="text-muted">
                <i class="fa fa-info-circle text-info mr-1"></i> Tap any option above to begin evaluating student OMR sheets.
            </span>
        </div>
    </div>

    {{-- 4. Step 3: Enrolled Students & OMR Verification Matrix --}}
    <div class="mob-step-card">
        <div class="mob-step-header">
            <h2 class="mob-step-title">
                <i class="fa fa-users text-primary"></i> 3. Verification Matrix
            </h2>
            <div class="d-flex align-items-center" style="gap:4px;">
                <span class="badge badge-light border text-dark" style="font-size:9.5px;">
                    Enrolled: <b id="count-enrolled-total">{{ count($students) }}</b>
                </span>
                <span class="badge badge-success" style="font-size:9.5px;">
                    Scanned: <b id="count-staged-matched">0</b>
                </span>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="mob-filter-tabs">
            <button type="button" class="mob-tab-btn active" data-filter="all">All ({{ count($students) }})</button>
            <button type="button" class="mob-tab-btn" data-filter="scanned">Scanned (<span id="tab-scanned-count">0</span>)</button>
            <button type="button" class="mob-tab-btn" data-filter="pending">Pending (<span id="tab-pending-count">{{ count($students) }}</span>)</button>
            <button type="button" class="mob-tab-btn" data-filter="review">Review (<span id="count-staged-unmatched">0</span>)</button>
        </div>

        {{-- Search Input --}}
        <div class="mb-2">
            <input type="text" id="mobStudentSearch" class="form-control form-control-sm" placeholder="Search student name, roll # or adm #..." style="font-size:11.5px; height:32px;">
        </div>

        {{-- Unmatched Staged Sheets Container (Warning Cards) --}}
        <div id="mob-unmatched-cards-container"></div>

        {{-- Enrolled Students Cards List --}}
        <div id="mob-students-cards-container">
            @forelse($students as $st)
                @php
                    $stuId = $st->id;
                    $stuName = trim(($st->first_name ?? '') . ' ' . ($st->last_name ?? ''));
                    $admNo = $st->admission_no ?? '';
                    $rollNo = $st->roll_no ?? '';
                    $examRollNo = $st->exam_roll_no ?? '';
                    $father = $st->father_name ?? '';
                    $batchName = $st->batch_name ?? '';

                    $exMark = $existingMarksMap[$stuId] ?? null;
                    $exOmr = $existingOmrMap[$stuId] ?? null;
                    $isSaved = !empty($exOmr);

                    $numericRoll = preg_replace('/\D/', '', $rollNo ?: $admNo);
                @endphp

                <div class="mob-student-card mob-student-item {{ $isSaved ? 'card-saved' : '' }}" 
                     id="student-card-{{ $stuId }}"
                     data-student-id="{{ $stuId }}"
                     data-student-name="{{ $stuName }}"
                     data-admission-no="{{ strtolower($admNo) }}"
                     data-roll-no="{{ strtolower($rollNo) }}"
                     data-exam-roll-no="{{ strtolower($examRollNo) }}"
                     data-numeric-roll="{{ $numericRoll }}"
                     data-status="{{ $isSaved ? 'saved' : 'pending' }}">
                    
                    {{-- Card Header: Checkbox, Avatar, Name, Status --}}
                    <div class="mob-card-head">
                        <div class="mob-card-profile">
                            <input type="checkbox" class="staged-checkbox" data-student-id="{{ $stuId }}" {{ $isSaved ? 'checked' : '' }} onchange="toggleStudentSelect('{{ $stuId }}', this.checked);">
                            <div class="mob-card-avatar">
                                {{ strtoupper(substr($stuName, 0, 1)) ?: 'S' }}
                            </div>
                            <div>
                                <div class="mob-student-name">{{ $stuName }}</div>
                                <div class="mob-student-adm">
                                    Adm: <b>{{ $admNo ?: '--' }}</b> &bull; Roll: <b>{{ $rollNo ?: '--' }}</b>
                                    @if($batchName) &bull; <span class="text-primary font-weight-bold">{{ $batchName }}</span>@endif
                                </div>
                            </div>
                        </div>
                        <div class="cell-status">
                            @if($isSaved)
                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size:10px;">
                                    <i class="fa fa-check-circle mr-1"></i> Saved
                                </span>
                            @else
                                <span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size:10px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;">
                                    <i class="fa fa-clock-o mr-1"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Body: Sheet Thumb, Roll, Questions, Marks --}}
                    <div class="mob-card-body-details">
                        <div class="cell-sheet">
                            @if($isSaved && !empty($exOmr->stored_file))
                                @php
                                    $imgUrl = asset($exOmr->stored_file);
                                @endphp
                                <img src="{{ $imgUrl }}" class="mob-sheet-thumb-img" alt="Scan" onclick="viewSavedOmrModal('{{ $exOmr->id }}', this);" data-image-url="{{ $imgUrl }}" title="Tap to inspect scan">
                            @else
                                <div class="mob-sheet-thumb-placeholder">
                                    <i class="fa fa-file-image-o mb-1"></i>
                                    <span>Empty</span>
                                </div>
                            @endif
                        </div>

                        <div style="flex:1; overflow:hidden;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="cell-detected-roll">
                                    <span class="text-muted" style="font-size:10px;">Detected: </span>
                                    <b class="font-mono" style="font-size:11px;">{{ $isSaved ? ($exOmr->unique_id ?: $rollNo) : '--' }}</b>
                                </div>
                                <div class="cell-questions">
                                    @if($isSaved)
                                        <b style="font-size:11px;">{{ ($exOmr->right_answer ?? 0) + ($exOmr->wrong_answer ?? 0) }} / {{ $exOmr->total_question ?? 50 }}</b>
                                        <span class="small font-weight-bold ml-1" style="font-size:9.5px;">
                                            <span class="text-success">🟢{{ $exOmr->right_answer ?? 0 }}</span> 
                                            <span class="text-danger">🔴{{ $exOmr->wrong_answer ?? 0 }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size:10.5px;">-- / {{ $selectedExam->number_of_questions ?? 50 }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="cell-score">
                                @if($isSaved)
                                    <div>
                                        <span class="font-weight-bold text-success" style="font-size:12.5px;">+{{ $exOmr->obtained_marks ?? 0 }}</span>
                                        <span class="text-muted small"> / {{ $exOmr->total_marks ?? 100 }}</span>
                                        <span class="badge badge-success ml-1 font-weight-bold" style="font-size:9.5px;">{{ $exOmr->percentage ?? 0 }}%</span>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size:11px;">Score: <b>-- / {{ $selectedExam->exam_maximum_marks ?? 100 }}</b></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer: Action Buttons --}}
                    <div class="mob-card-foot-actions">
                        <div class="cell-filename text-truncate" style="max-width:180px; font-size:10px; color:#64748b;">
                            @if($isSaved && !empty($exOmr->source_filename))
                                <i class="fa fa-file-image-o text-primary mr-1"></i> {{ $exOmr->source_filename }}
                            @else
                                <i class="fa fa-clock-o mr-1"></i> Waiting for sheet
                            @endif
                        </div>
                        <div class="mob-card-btns cell-actions">
                            @if($isSaved)
                                <button type="button" class="btn-card-action btn-outline-primary border" onclick="viewSavedOmrModal('{{ $exOmr->id }}', this);" title="Inspect Scan">
                                    <i class="fa fa-eye"></i> View
                                </button>
                                <button type="button" class="btn-card-action btn-outline-info border" onclick="viewSavedAnswersModal('{{ $exOmr->id }}');" title="Answers Breakdown">
                                    <i class="fa fa-list"></i>
                                </button>
                            @else
                                <button type="button" class="btn-card-action btn-outline-secondary border" onclick="triggerCameraInput();" title="Snap sheet">
                                    <i class="fa fa-camera"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="text-center text-muted py-4 bg-white rounded border">
                    <i class="fa fa-users mb-1" style="font-size:24px;"></i>
                    <div style="font-size:12px; font-weight:700;">No Enrolled Students Found</div>
                    <div style="font-size:10.5px;">Please choose a course with registered students.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 5. Step 4: Saved Evaluations History & Marks Ledger (Collapsible) --}}
    <div class="mob-step-card">
        <div class="mob-step-header" style="cursor:pointer;" onclick="$('#mob-saved-history-body').slideToggle(200);">
            <h2 class="mob-step-title">
                <i class="fa fa-history text-secondary"></i> 4. Saved Evaluations History
            </h2>
            <div class="d-flex align-items-center" style="gap:4px;">
                <span class="badge badge-primary" style="background:#002C54; font-size:9.5px;">
                    <span id="omr-saved-count-badge">{{ count($recentScans) }}</span> Records
                </span>
                <i class="fa fa-chevron-down text-muted" style="font-size:11px;"></i>
            </div>
        </div>

        <div id="mob-saved-history-body" style="display:none;">
            <div id="mob-saved-history-container">
                @forelse($recentScans as $sc)
                    @php
                        $stName = $sc->student ? trim($sc->student->first_name . ' ' . ($sc->student->last_name ?? '')) : ('Student #' . $sc->unique_id);
                        $scImg = !empty($sc->stored_file) ? asset($sc->stored_file) : '';
                    @endphp
                    <div class="mob-history-card" id="scan-card-{{ $sc->id }}" data-id="{{ $sc->id }}">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div>
                                <strong style="font-size:12px; color:#002C54;">{{ $stName }}</strong>
                                <span class="text-muted ml-1" style="font-size:10px;">(Roll: <b>{{ $sc->unique_id }}</b>)</span>
                            </div>
                            <span class="badge badge-success font-weight-bold" style="font-size:9.5px;">
                                Rank #{{ $sc->rank ?? 1 }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:11px;">
                            <div>
                                Score: <b class="text-success font-mono">+{{ $sc->obtained_marks }}</b> / {{ $sc->total_marks }} ({{ $sc->percentage }}%)
                            </div>
                            <div class="small font-weight-bold font-mono">
                                <span class="text-success">🟢{{ $sc->right_answer }}</span> 
                                <span class="text-danger">🔴{{ $sc->wrong_answer }}</span> 
                                <span class="text-muted">⚪{{ $sc->blank_answer }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-1 border-top" style="font-size:10px;">
                            <span class="text-muted text-truncate" style="max-width:180px;">
                                <i class="fa fa-file-image-o mr-1"></i> {{ $sc->source_filename ?: 'omr_scan.jpeg' }}
                            </span>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" onclick="viewSavedOmrModal('{{ $sc->id }}', this);" data-image-url="{{ $scImg }}">
                                    <i class="fa fa-eye"></i> Sheet
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 ml-1" onclick="viewSavedAnswersModal('{{ $sc->id }}');">
                                    <i class="fa fa-list"></i> Answers
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-3">
                        <i class="fa fa-history mb-1" style="font-size:20px;"></i>
                        <div style="font-size:11.5px; font-weight:700;">No Saved Evaluations Yet</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- 5. Pinned Floating Action Dock at Bottom --}}
<div class="mob-floating-action-dock" id="mobOmrFloatingDock">
    <div class="mob-dock-summary">
        <div class="mob-dock-summary-count">
            <span id="btn-save-count">0</span> Sheet(s) Ready
        </div>
        <div class="mob-dock-summary-sub" id="staging-summary-marks">
            Awaiting OMR scans
        </div>
    </div>
    <div class="mob-dock-buttons">
        <button type="button" class="btn-dock-clear" id="btn-clear-staged" onclick="clearAllStaged();" disabled>
            Clear
        </button>
        <button type="button" class="btn-dock-submit" id="btn-final-submit" onclick="confirmFinalSubmit();" disabled>
            <i class="fa fa-check-circle"></i> Save Marks
        </button>
    </div>
</div>

{{-- 6. Live In-App Camera Viewfinder Modal --}}
<div class="modal fade" id="modalLiveCamera" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Camera Top Header --}}
            <div class="d-flex align-items-center justify-content-between p-2 bg-dark text-white border-bottom border-secondary">
                <div class="d-flex align-items-center" style="gap:6px;">
                    <i class="fa fa-camera text-success"></i>
                    <span class="font-weight-bold" style="font-size:12.5px;">OMR Document Viewfinder</span>
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <span class="badge badge-success font-mono" style="font-size:10.5px;">
                        Captured: <b id="camera-captured-count">0</b>
                    </span>
                    <button type="button" class="close text-white p-1" onclick="closeLiveCameraModal();" aria-label="Close" style="opacity:1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            {{-- Camera Video Viewport with Alignment Reticle --}}
            <div class="mob-camera-viewport">
                <video id="camera-stream-video" autoplay playsinline muted></video>
                <div class="mob-camera-guide-frame">
                    <div class="mob-camera-guide-corner mob-guide-tl"></div>
                    <div class="mob-camera-guide-corner mob-guide-tr"></div>
                    <div class="mob-camera-guide-corner mob-guide-bl"></div>
                    <div class="mob-camera-guide-corner mob-guide-br"></div>
                </div>
                <canvas id="camera-capture-canvas" style="display:none;"></canvas>
            </div>

            {{-- Camera Bottom Controls --}}
            <div class="mob-camera-bottom-bar">
                {{-- Torch Toggle Button --}}
                <button type="button" class="mob-camera-tool-btn" id="btn-camera-torch" onclick="toggleCameraTorch();" title="Toggle Flash / Torch">
                    <i class="fa fa-bolt"></i>
                </button>

                {{-- Shutter Snap Button --}}
                <button type="button" class="mob-shutter-btn" id="btn-camera-shutter" onclick="snapCameraFrame();" title="Capture OMR Sheet">
                    <i class="fa fa-camera"></i>
                </button>

                {{-- Switch Front/Back Camera --}}
                <button type="button" class="mob-camera-tool-btn" id="btn-camera-flip" onclick="flipCameraFacing();" title="Flip Camera">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>

            {{-- Done & Process Bar --}}
            <div class="p-2 bg-dark text-center border-top border-secondary">
                <button type="button" class="btn btn-success btn-sm btn-block font-weight-bold" onclick="finishLiveScanning();" id="btn-finish-camera-scan">
                    <i class="fa fa-check-circle mr-1"></i> Done &amp; Process Batch (<span id="btn-camera-done-count">0</span>)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 7. Annotated Sheet Inspection Modal --}}
<div class="modal fade" id="modalAnnotatedSheet" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content" style="border-radius:4px; overflow:hidden;">
            <div class="modal-header py-2 px-3 bg-primary text-white" style="background:#002C54 !important;">
                <h6 class="modal-title font-weight-bold mb-0" style="font-size:12.5px;">
                    <i class="fa fa-file-image-o text-warning mr-1"></i> OMR Sheet Inspection
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2 text-center" style="background:#0f172a;">
                <div id="modal-annotated-info" class="text-white text-left p-1 mb-2 rounded font-mono" style="font-size:10.5px; background:rgba(255,255,255,0.08);"></div>
                <div style="max-height:70vh; overflow:auto;">
                    <img id="modal-annotated-img" src="" alt="OMR Sheet" style="max-width:100%; height:auto; border-radius:2px; box-shadow:0 4px 14px rgba(0,0,0,0.5);">
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- 8. Answers Breakdown Modal --}}
<div class="modal fade" id="modalAnswersGrid" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 96%;">
        <div class="modal-content" style="border-radius:4px; overflow:hidden;">
            <div class="modal-header py-2 px-3 bg-primary text-white" style="background:#002C54 !important;">
                <h6 class="modal-title font-weight-bold mb-0" style="font-size:12.5px;">
                    <i class="fa fa-list-ol text-info mr-1"></i> Extracted Answers Breakdown
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2" style="max-height:75vh; overflow-y:auto;">
                <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded bg-light border" style="font-size:11px;">
                    <div>
                        Student: <strong id="modal-ans-student" class="text-dark">Student</strong> &bull;
                        Roll: <b id="modal-ans-roll" class="font-mono text-primary">--</b>
                    </div>
                    <div id="modal-ans-score" class="font-weight-bold text-success font-mono">--</div>
                </div>

                {{-- Subject Section Performance --}}
                <div id="modal-subject-breakdown-wrapper" class="mb-2" style="display:none;">
                    <div class="font-weight-bold text-dark mb-1" style="font-size:11px;">
                        <i class="fa fa-sitemap text-primary mr-1"></i> Subject Section Performance:
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-1" style="font-size:10px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Subject</th>
                                    <th>Range</th>
                                    <th>Right</th>
                                    <th>Wrong</th>
                                    <th>Left</th>
                                    <th>Score</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody id="modal-subject-breakdown-tbody"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Questions Table --}}
                <div class="font-weight-bold text-dark mb-1" style="font-size:11px;">
                    <i class="fa fa-check-square-o text-success mr-1"></i> Question Details:
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered mb-0" style="font-size:10.5px;">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Marked</th>
                                <th>Key</th>
                                <th>Marks</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody id="modal-answers-tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
window.savedOmrRecords = @json($allSavedMap);
window.examSubjectMap = @json($examSubjectMap);

document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('omr-class-select');
    const batchSelect = document.getElementById('omr-batch-select');
    const examSelect = document.getElementById('omr-exam-select');
    const cameraInput = document.getElementById('mob-omr-camera-input');
    const fileInput = document.getElementById('mob-omr-file-input');

    const progressWrapper = document.getElementById('omr-batch-progress-wrapper');
    const progressBar = document.getElementById('omr-batch-progress-bar');
    const progressStatusText = document.getElementById('omr-batch-status-text');
    const progressPercentText = document.getElementById('omr-batch-percent-text');

    const countEnrolledTotal = document.getElementById('count-enrolled-total');
    const countStagedMatched = document.getElementById('count-staged-matched');
    const countStagedUnmatched = document.getElementById('count-staged-unmatched');
    const tabScannedCount = document.getElementById('tab-scanned-count');
    const tabPendingCount = document.getElementById('tab-pending-count');
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

    // Helper: get selected option
    const getSelectedExamOption = () => examSelect && examSelect.selectedIndex >= 0 ? examSelect.options[examSelect.selectedIndex] : null;

    // Notification helper
    function showToast(type, msg) {
        if (typeof toastr !== 'undefined') {
            toastr[type](msg);
        } else if (typeof notify !== 'undefined') {
            notify(type, msg);
        } else {
            alert(msg);
        }
    }

    // 1. Trigger Native Camera Snapshot
    window.triggerCameraInput = function () {
        if (!examSelect || !examSelect.value) {
            alert('Please choose a target examination first.');
            if (examSelect) examSelect.focus();
            return;
        }
        if (isProcessingBatch) return;
        cameraInput.click();
    };

    // 2. Trigger File/Gallery Picker
    window.triggerFileInput = function () {
        if (!examSelect || !examSelect.value) {
            alert('Please choose a target examination first.');
            if (examSelect) examSelect.focus();
            return;
        }
        if (isProcessingBatch) return;
        fileInput.click();
    };

    // Camera input change handler
    if (cameraInput) {
        cameraInput.addEventListener('change', function () {
            if (this.files && this.files.length) {
                processFilesQueue(Array.from(this.files));
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

    // Course select change
    if (classSelect) {
        classSelect.addEventListener('change', function () {
            const classId = this.value;
            window.location.href = `{{ url('upload_omr') }}?class_type_id=${classId}&layout=mobile`;
        });
    }

    // Batch select change
    if (batchSelect) {
        batchSelect.addEventListener('change', function () {
            const batchVal = this.value;
            const classId = classSelect ? classSelect.value : '';
            const curExamOpt = getSelectedExamOption();
            let url = `{{ url('upload_omr') }}?layout=mobile&`;
            if (classId) url += `class_type_id=${classId}&`;
            if (batchVal && batchVal !== 'all') url += `batch_id=${batchVal}&`;
            if (curExamOpt && curExamOpt.value) url += `exam_id=${curExamOpt.value}`;
            window.location.href = url.replace(/[?&]$/, '');
        });
    }

    // Exam select change
    if (examSelect) {
        examSelect.addEventListener('change', function () {
            const opt = getSelectedExamOption();
            if (this.value && opt) {
                const classId = opt.dataset.classid || (classSelect ? classSelect.value : '');
                const batchId = opt.dataset.batchid || (batchSelect ? batchSelect.value : '');
                let url = `{{ url('upload_omr') }}?layout=mobile&exam_id=${this.value}`;
                if (classId) url += `&class_type_id=${classId}`;
                if (batchId && batchId !== '0' && batchId !== 'all') url += `&batch_id=${batchId}`;
                window.location.href = url;
            }
        });
    }

    // =========================================================================
    // HTML5 LIVE CAMERA SCANNER VIEWFINDER
    // =========================================================================
    let cameraMediaStream = null;
    let cameraFacingMode = 'environment';
    let cameraCapturedBlobs = [];
    let isTorchOn = false;

    window.openLiveCameraModal = function () {
        if (!examSelect || !examSelect.value) {
            alert('Please choose a target examination first.');
            if (examSelect) examSelect.focus();
            return;
        }
        cameraCapturedBlobs = [];
        updateCameraCapturedCount();

        if (typeof $ !== 'undefined' && $('#modalLiveCamera').modal) {
            $('#modalLiveCamera').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalLiveCamera')).show();
        }

        startCameraStream();
    };

    window.closeLiveCameraModal = function () {
        stopCameraStream();
        if (typeof $ !== 'undefined') {
            $('#modalLiveCamera').modal('hide');
        }
    };

    async function startCameraStream() {
        stopCameraStream();
        const video = document.getElementById('camera-stream-video');
        if (!video) return;

        try {
            const constraints = {
                video: {
                    facingMode: { ideal: cameraFacingMode },
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: false
            };

            cameraMediaStream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = cameraMediaStream;
            video.play();
        } catch (err) {
            console.error('Camera stream error:', err);
            alert('Unable to access device camera. Please check camera permissions or use "Camera Snap".');
            closeLiveCameraModal();
        }
    }

    function stopCameraStream() {
        if (cameraMediaStream) {
            cameraMediaStream.getTracks().forEach(track => track.stop());
            cameraMediaStream = null;
        }
    }

    window.flipCameraFacing = function () {
        cameraFacingMode = (cameraFacingMode === 'environment') ? 'user' : 'environment';
        startCameraStream();
    };

    window.toggleCameraTorch = async function () {
        if (!cameraMediaStream) return;
        const track = cameraMediaStream.getVideoTracks()[0];
        if (!track) return;

        try {
            const capabilities = track.getCapabilities ? track.getCapabilities() : {};
            if (capabilities.torch) {
                isTorchOn = !isTorchOn;
                await track.applyConstraints({
                    advanced: [{ torch: isTorchOn }]
                });
                document.getElementById('btn-camera-torch').style.color = isTorchOn ? '#facc15' : '#ffffff';
            } else {
                showToast('info', 'Torch not supported on this camera device.');
            }
        } catch (e) {
            console.warn('Torch toggle error', e);
        }
    };

    window.snapCameraFrame = function () {
        const video = document.getElementById('camera-stream-video');
        const canvas = document.getElementById('camera-capture-canvas');
        if (!video || !canvas) return;

        const w = video.videoWidth || 1280;
        const h = video.videoHeight || 720;
        canvas.width = w;
        canvas.height = h;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, w, h);

        // Visual shutter flash effect
        video.style.opacity = '0.3';
        setTimeout(() => { video.style.opacity = '1'; }, 100);

        canvas.toBlob(function (blob) {
            if (!blob) return;
            const filename = `scan_omr_${Date.now()}_${cameraCapturedBlobs.length + 1}.jpg`;
            const file = new File([blob], filename, { type: 'image/jpeg' });
            cameraCapturedBlobs.push(file);
            updateCameraCapturedCount();
            showToast('success', `Captured sheet #${cameraCapturedBlobs.length}`);
        }, 'image/jpeg', 0.92);
    };

    function updateCameraCapturedCount() {
        const c = cameraCapturedBlobs.length;
        const el = document.getElementById('camera-captured-count');
        const el2 = document.getElementById('btn-camera-done-count');
        if (el) el.textContent = c;
        if (el2) el2.textContent = c;
    }

    window.finishLiveScanning = function () {
        closeLiveCameraModal();
        if (cameraCapturedBlobs.length > 0) {
            processFilesQueue(cameraCapturedBlobs);
            cameraCapturedBlobs = [];
        }
    };

    // =========================================================================
    // OMR EXTRACTION QUEUE & STUDENT MATCHING
    // =========================================================================

    function findStudentCard(sheet) {
        if (sheet.student && sheet.student.id) {
            const card = document.getElementById(`student-card-${sheet.student.id}`);
            if (card) return card;
        }

        const raw = (sheet.roll_number || '').toString().trim().toLowerCase();
        if (!raw) return null;
        const num = raw.replace(/\D/g, '');
        const trimmed = num.replace(/^0+/, '');

        const cards = document.querySelectorAll('.mob-student-card');
        for (const c of cards) {
            const rRoll = (c.dataset.rollNo || '').toLowerCase();
            const rAdm = (c.dataset.admissionNo || '').toLowerCase();
            const rExamRoll = (c.dataset.examRollNo || '').toLowerCase();
            const rNum = c.dataset.numericRoll || '';
            const rTrimmed = rNum.replace(/^0+/, '');

            if (rRoll === raw || rAdm === raw || rExamRoll === raw) return c;
            if (num && (rNum === num || rRoll === num || rAdm === num)) return c;
            if (trimmed && (rTrimmed === trimmed || rNum === trimmed)) return c;
        }
        return null;
    }

    function resolveSheetThumbSrc(sheet) {
        if (!sheet) return '';
        if (sheet.annotated_image_base64) {
            return sheet.annotated_image_base64.startsWith('data:') 
                ? sheet.annotated_image_base64 
                : 'data:image/jpeg;base64,' + sheet.annotated_image_base64;
        }
        return sheet.annotated_image_url || sheet.image_url || '';
    }

    function attachSheetToStudentCard(sheet, card) {
        const tempId = sheet.temp_id;
        const thumbSrc = resolveSheetThumbSrc(sheet);
        const evalData = sheet.evaluation || {};
        const maxMarks = getSelectedExamOption()?.dataset.maxmarks || evalData.total_marks || 100;

        card.dataset.tempId = tempId;
        card.dataset.status = 'scanned';
        card.classList.remove('card-saved', 'card-unmatched');
        card.classList.add('card-scanned');

        // Checkbox
        const cb = card.querySelector('.staged-checkbox');
        if (cb) {
            cb.checked = (sheet.selected !== false);
            cb.dataset.tempId = tempId;
        }

        // Status
        const cellStatus = card.querySelector('.cell-status');
        if (cellStatus) {
            cellStatus.innerHTML = `
                <span class="badge badge-info px-2 py-1 font-weight-bold" style="background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc; font-size:10px;">
                    <i class="fa fa-clock-o mr-1"></i> Scanned
                </span>
            `;
        }

        // Sheet Thumbnail
        const cellSheet = card.querySelector('.cell-sheet');
        if (cellSheet) {
            const isPdf = (sheet.source_filename || '').toLowerCase().endsWith('.pdf');
            if (thumbSrc) {
                cellSheet.innerHTML = `
                    <img src="${thumbSrc}" class="mob-sheet-thumb-img" alt="Scan" onclick="viewAnnotatedModal('${tempId}');" title="Tap to inspect scan">
                `;
            } else if (isPdf) {
                cellSheet.innerHTML = `
                    <div class="mob-sheet-thumb-img d-inline-flex align-items-center justify-content-center bg-light border text-danger" onclick="viewAnnotatedModal('${tempId}');" title="PDF Document" style="cursor: pointer; width: 34px; height: 34px; border-radius: 3px;">
                        <i class="fa fa-file-pdf-o" style="font-size: 18px;"></i>
                    </div>
                `;
            } else {
                cellSheet.innerHTML = `
                    <div class="mob-sheet-thumb-img d-inline-flex align-items-center justify-content-center bg-light border text-primary" onclick="viewAnnotatedModal('${tempId}');" title="Sheet" style="cursor: pointer; width: 34px; height: 34px; border-radius: 3px;">
                        <i class="fa fa-file-image-o" style="font-size: 16px;"></i>
                    </div>
                `;
            }
        }

        // Detected Roll
        const cellRoll = card.querySelector('.cell-detected-roll');
        if (cellRoll) {
            cellRoll.innerHTML = `
                <span class="text-muted" style="font-size:10px;">Detected: </span>
                <input type="text" class="form-control form-control-sm font-mono text-center d-inline-block font-weight-bold" 
                       value="${escapeHtml(sheet.roll_number || '')}" 
                       style="width: 80px; height: 24px; font-size: 11px; padding: 2px 4px;"
                       onchange="handleStagedRollChange('${tempId}', this.value);">
            `;
        }

        // Questions
        const cellQuestions = card.querySelector('.cell-questions');
        if (cellQuestions) {
            cellQuestions.innerHTML = `
                <b style="font-size:11px;">${sheet.summary?.attempted || (evalData.correct_answers + evalData.incorrect_answers)} / ${sheet.summary?.total_questions || evalData.total_questions || 50}</b>
                <span class="small font-weight-bold ml-1" style="font-size:9.5px;">
                    <span class="text-success">🟢${evalData.correct_answers || 0}</span> 
                    <span class="text-danger">🔴${evalData.incorrect_answers || 0}</span>
                </span>
            `;
        }

        // Score & Marks
        const cellScore = card.querySelector('.cell-score');
        if (cellScore) {
            let pillsHtml = '';
            const sbList = evalData.subject_breakdown || [];
            if (Array.isArray(sbList) && sbList.length > 0) {
                pillsHtml = '<div class="d-flex flex-wrap mt-1" style="gap: 3px;">';
                sbList.forEach(sb => {
                    const sFullName = resolveSubjectName(sb);
                    const shortName = sFullName.split(' ')[0];
                    pillsHtml += `<span class="badge" style="font-size: 8.5px; padding: 1px 4px; background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd;" title="${escapeHtml(sFullName)}: ${sb.obtained_marks}/${sb.max_marks}">${escapeHtml(shortName)}: +${sb.obtained_marks}</span>`;
                });
                pillsHtml += '</div>';
            }

            cellScore.innerHTML = `
                <div>
                    <span class="font-weight-bold text-success" style="font-size:12.5px;">+${evalData.obtained_marks || 0}</span>
                    <span class="text-muted small"> / ${evalData.total_marks || maxMarks}</span>
                    <span class="badge badge-success ml-1 font-weight-bold" style="font-size:9.5px;">${evalData.percentage || 0}%</span>
                </div>
                ${pillsHtml}
            `;
        }

        // Filename
        const cellFilename = card.querySelector('.cell-filename');
        if (cellFilename) {
            cellFilename.innerHTML = `<i class="fa fa-file-image-o text-primary mr-1"></i> ${escapeHtml(sheet.source_filename)}`;
        }

        // Action Buttons
        const cellActions = card.querySelector('.cell-actions');
        if (cellActions) {
            cellActions.innerHTML = `
                <button type="button" class="btn-card-action btn-outline-primary border" onclick="viewAnnotatedModal('${tempId}');" title="Inspect Scan">
                    <i class="fa fa-eye"></i> View
                </button>
                <button type="button" class="btn-card-action btn-outline-info border" onclick="viewAnswersModal('${tempId}');" title="Answers Breakdown">
                    <i class="fa fa-list"></i>
                </button>
                <button type="button" class="btn-card-action btn-outline-danger border" onclick="resetStudentCard('${tempId}');" title="Reset scan">
                    <i class="fa fa-undo"></i>
                </button>
            `;
        }
    }

    function renderUnmatchedCard(sheet) {
        let card = document.getElementById(`staged-unmatched-${sheet.temp_id}`);
        const container = document.getElementById('mob-unmatched-cards-container');
        if (!card) {
            card = document.createElement('div');
            card.id = `staged-unmatched-${sheet.temp_id}`;
            card.className = 'mob-student-card card-unmatched mb-2';
            card.dataset.tempId = sheet.temp_id;
            card.dataset.status = 'review';
            container.appendChild(card);
        }

        const thumbSrc = resolveSheetThumbSrc(sheet);
        const evalData = sheet.evaluation || {};
        const maxMarks = getSelectedExamOption()?.dataset.maxmarks || evalData.total_marks || 100;

        card.innerHTML = `
            <div class="mob-card-head">
                <div class="mob-card-profile">
                    <input type="checkbox" class="staged-checkbox" data-temp-id="${sheet.temp_id}" ${sheet.selected !== false ? 'checked' : ''} onchange="toggleSheetSelect('${sheet.temp_id}', this.checked);">
                    <div class="mob-card-avatar" style="background:#f59e0b;">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="mob-student-name text-warning">Unmatched Roll #</div>
                        <div class="mob-student-adm text-danger font-weight-bold">Student not found in this course/batch</div>
                    </div>
                </div>
                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold" style="font-size:10px;">
                    Needs Review
                </span>
            </div>

            <div class="mob-card-body-details">
                <div class="cell-sheet">
                    ${thumbSrc ? `<img src="${thumbSrc}" class="mob-sheet-thumb-img" alt="Scan" onclick="viewAnnotatedModal('${sheet.temp_id}');">` :
                      ((sheet.source_filename || '').toLowerCase().endsWith('.pdf') ? `<div class="mob-sheet-thumb-img d-inline-flex align-items-center justify-content-center bg-light border text-danger" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="PDF Document" style="cursor: pointer; width: 34px; height: 34px; border-radius: 3px;"><i class="fa fa-file-pdf-o" style="font-size: 18px;"></i></div>` : `<div class="mob-sheet-thumb-img d-inline-flex align-items-center justify-content-center bg-light border text-primary" onclick="viewAnnotatedModal('${sheet.temp_id}');" title="Sheet" style="cursor: pointer; width: 34px; height: 34px; border-radius: 3px;"><i class="fa fa-file-image-o" style="font-size: 16px;"></i></div>`)}
                </div>
                <div style="flex:1;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div>
                            <span class="text-muted" style="font-size:10px;">Roll #: </span>
                            <input type="text" class="form-control form-control-sm font-mono text-center d-inline-block font-weight-bold border-warning" 
                                   value="${escapeHtml(sheet.roll_number || '')}" 
                                   style="width: 80px; height: 24px; font-size: 11px; padding: 2px 4px;"
                                   placeholder="Roll #"
                                   onchange="handleUnmatchedRollChange('${sheet.temp_id}', this.value);">
                        </div>
                        <div class="font-weight-bold font-mono" style="font-size:11px;">
                            ${sheet.summary?.attempted || 0} / ${sheet.summary?.total_questions || 50} Qs
                        </div>
                    </div>
                    <div>
                        <span class="font-weight-bold text-success">+${evalData.obtained_marks || 0}</span> / ${evalData.total_marks || maxMarks}
                        <span class="badge badge-success ml-1 font-weight-bold" style="font-size:9.5px;">${evalData.percentage || 0}%</span>
                    </div>
                </div>
            </div>

            <div class="mob-card-foot-actions">
                <div class="text-truncate" style="max-width:180px; font-size:10px; color:#64748b;">
                    <i class="fa fa-file-image-o text-primary mr-1"></i> ${escapeHtml(sheet.source_filename)}
                </div>
                <div class="mob-card-btns">
                    <button type="button" class="btn-card-action btn-outline-primary border" onclick="viewAnnotatedModal('${sheet.temp_id}');">
                        <i class="fa fa-eye"></i> View
                    </button>
                    <button type="button" class="btn-card-action btn-outline-info border" onclick="viewAnswersModal('${sheet.temp_id}');">
                        <i class="fa fa-list"></i>
                    </button>
                    <button type="button" class="btn-card-action btn-outline-danger border" onclick="removeUnmatchedSheet('${sheet.temp_id}');">
                        <i class="fa fa-trash-o"></i>
                    </button>
                </div>
            </div>
        `;
    }

    async function processFilesQueue(files) {
        const examId = examSelect ? examSelect.value : '';
        if (!examId || !files.length) return;

        isProcessingBatch = true;
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
                    progressStatusText.innerHTML = `<i class="fa fa-refresh fa-spin mr-1"></i> Extracting ${i + 1}/${totalFiles}: <b>${escapeHtml(file.name)}</b>...`;
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
                    sheet.selected = true;
                    stagedSheets.set(sheet.temp_id, sheet);

                    const matchedCard = findStudentCard(sheet);
                    if (matchedCard) {
                        attachSheetToStudentCard(sheet, matchedCard);
                    } else {
                        renderUnmatchedCard(sheet);
                    }

                    successful++;
                } catch (err) {
                    console.error('Extraction error for ' + file.name, err);
                    showToast('error', `Error with ${file.name}: ${err.message}`);
                }

                updateStagingCounters();
            }
        } finally {
            if (progressBar) progressBar.style.width = '100%';
            if (progressPercentText) progressPercentText.textContent = '100%';
            if (progressStatusText) {
                progressStatusText.innerHTML = `<i class="fa fa-check-circle text-success mr-1"></i> Evaluated ${successful} of ${totalFiles} sheets! Review marks and tap "Save Marks".`;
            }

            setTimeout(() => {
                if (progressWrapper) progressWrapper.classList.add('d-none');
            }, 3000);

            if (cameraInput) cameraInput.value = '';
            if (fileInput) fileInput.value = '';
            isProcessingBatch = false;
            updateStagingCounters();
        }
    }

    window.resetStudentCard = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;
        const card = document.querySelector(`.mob-student-card[data-temp-id="${tempId}"]`);
        if (card) {
            card.classList.remove('card-scanned');
            delete card.dataset.tempId;
            card.dataset.status = 'pending';

            const cb = card.querySelector('.staged-checkbox');
            if (cb) {
                cb.checked = false;
                delete cb.dataset.tempId;
            }

            const cellStatus = card.querySelector('.cell-status');
            if (cellStatus) {
                cellStatus.innerHTML = `<span class="badge badge-secondary px-2 py-1 font-weight-bold" style="font-size:10px; background:#f1f5f9; color:#64748b; border:1px solid #cbd5e1;"><i class="fa fa-clock-o mr-1"></i> Pending</span>`;
            }

            const cellSheet = card.querySelector('.cell-sheet');
            if (cellSheet) {
                cellSheet.innerHTML = `<div class="mob-sheet-thumb-placeholder"><i class="fa fa-file-image-o mb-1"></i><span>Empty</span></div>`;
            }

            const cellRoll = card.querySelector('.cell-detected-roll');
            if (cellRoll) cellRoll.innerHTML = `<span class="text-muted" style="font-size:10px;">Detected: </span><b class="font-mono">--</b>`;

            const cellQ = card.querySelector('.cell-questions');
            if (cellQ) cellQ.innerHTML = `<span class="text-muted" style="font-size:10.5px;">-- / ${getSelectedExamOption()?.dataset.questions || 50}</span>`;

            const cellScore = card.querySelector('.cell-score');
            if (cellScore) cellScore.innerHTML = `<span class="text-muted" style="font-size:11px;">Score: <b>-- / ${getSelectedExamOption()?.dataset.maxmarks || 100}</b></span>`;

            const cellFilename = card.querySelector('.cell-filename');
            if (cellFilename) cellFilename.innerHTML = `<i class="fa fa-clock-o mr-1"></i> Waiting for sheet`;

            const cellActions = card.querySelector('.cell-actions');
            if (cellActions) cellActions.innerHTML = `<button type="button" class="btn-card-action btn-outline-secondary border" onclick="triggerCameraInput();"><i class="fa fa-camera"></i></button>`;
        }
        stagedSheets.delete(tempId);
        updateStagingCounters();
    };

    window.removeUnmatchedSheet = function (tempId) {
        stagedSheets.delete(tempId);
        document.getElementById(`staged-unmatched-${tempId}`)?.remove();
        updateStagingCounters();
    };

    window.handleStagedRollChange = function (tempId, newRoll) {
        const sheet = stagedSheets.get(tempId);
        if (sheet) sheet.roll_number = newRoll.trim();
    };

    window.handleUnmatchedRollChange = async function (tempId, newRoll) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;
        sheet.roll_number = newRoll.trim();

        const card = findStudentCard(sheet);
        if (card) {
            document.getElementById(`staged-unmatched-${tempId}`)?.remove();
            attachSheetToStudentCard(sheet, card);
            updateStagingCounters();
            showToast('success', `Re-matched sheet to student.`);
        }
    };

    function updateStagingCounters() {
        const totalEnrolled = document.querySelectorAll('.mob-student-card:not(.card-unmatched)').length;
        const scannedCount = document.querySelectorAll('.mob-student-card.card-scanned').length;
        const pendingCount = Math.max(0, totalEnrolled - scannedCount);
        const unmatchedCount = document.querySelectorAll('.mob-student-card.card-unmatched').length;

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
        if (countStagedUnmatched) countStagedUnmatched.textContent = unmatchedCount;
        if (tabScannedCount) tabScannedCount.textContent = scannedCount;
        if (tabPendingCount) tabPendingCount.textContent = pendingCount;
        if (btnSaveCount) btnSaveCount.textContent = readyToSaveCount;

        if (readyToSaveCount > 0 && !isProcessingBatch) {
            btnFinalSubmit.disabled = false;
            btnFinalSubmit.removeAttribute('disabled');
        } else {
            btnFinalSubmit.disabled = true;
            btnFinalSubmit.setAttribute('disabled', 'disabled');
        }

        if (stagedSheets.size > 0 && !isProcessingBatch) {
            btnClearStaged.disabled = false;
            btnClearStaged.removeAttribute('disabled');
        } else {
            btnClearStaged.disabled = true;
            btnClearStaged.setAttribute('disabled', 'disabled');
        }

        if (stagedSheets.size > 0) {
            const avg = (sumMarks / Math.max(1, readyToSaveCount)).toFixed(2);
            stagingSummaryMarks.innerHTML = `Avg Score: <b class="text-success">+${avg}</b> (${readyToSaveCount} Selected)`;
        } else {
            stagingSummaryMarks.innerHTML = 'Awaiting OMR scans';
        }
    }

    window.toggleSheetSelect = function (tempId, isSelected) {
        const sheet = stagedSheets.get(tempId);
        if (sheet) {
            sheet.selected = isSelected;
            updateStagingCounters();
        }
    };

    window.toggleStudentSelect = function (studentId, isSelected) {
        const card = document.getElementById(`student-card-${studentId}`);
        if (card && card.dataset.tempId) {
            const sheet = stagedSheets.get(card.dataset.tempId);
            if (sheet) sheet.selected = isSelected;
        }
        updateStagingCounters();
    };

    window.clearAllStaged = function () {
        if (!stagedSheets.size) return;
        if (!confirm('Clear all unsaved scanned extractions?')) return;

        stagedSheets.forEach((sheet, tempId) => {
            resetStudentCard(tempId);
            document.getElementById(`staged-unmatched-${tempId}`)?.remove();
        });
        stagedSheets.clear();
        updateStagingCounters();
    };

    // Modal: Inspect Scan
    window.viewAnnotatedModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const src = resolveSheetThumbSrc(sheet);
        modalAnnotatedImg.src = src;
        modalAnnotatedInfo.innerHTML = `
            <b>File:</b> ${escapeHtml(sheet.source_filename)} &bull; 
            <b>Roll #:</b> ${escapeHtml(sheet.roll_number || 'N/A')} &bull; 
            <b>Score:</b> +${sheet.evaluation?.obtained_marks || 0} (${sheet.evaluation?.percentage || 0}%)
        `;

        if (typeof $ !== 'undefined' && $('#modalAnnotatedSheet').modal) {
            $('#modalAnnotatedSheet').modal('show');
        }
    };

    window.viewSavedOmrModal = function (omrId, btnEl) {
        const rec = (window.savedOmrRecords && window.savedOmrRecords[omrId]) || null;
        let btn = btnEl;
        const img = btn ? btn.closest('.mob-card-body-details, .mob-history-card')?.querySelector('img.mob-sheet-thumb-img') : null;

        let imageUrl = btn?.dataset?.imageUrl || (rec ? (rec.image_url || (rec.stored_file ? '{{ url("/") }}/' + rec.stored_file.replace(/^\/+/, '') : null)) : null);
        if (!imageUrl && img && img.src) imageUrl = img.src;

        const studentName = rec?.student ? (rec.student.first_name + ' ' + (rec.student.last_name || '')) : 'Student';
        const rollNo = rec?.unique_id || '';
        const score = rec ? `+${rec.obtained_marks || 0} / ${rec.total_marks || 0} (${rec.percentage || 0}%)` : '';

        if (modalAnnotatedImg && imageUrl) {
            modalAnnotatedImg.src = imageUrl;
            modalAnnotatedImg.style.display = 'inline-block';
        }

        if (modalAnnotatedInfo) {
            modalAnnotatedInfo.innerHTML = `
                <span class="badge badge-primary px-2 py-1 mr-1" style="background:#002C54;">Saved #${omrId}</span>
                <b>${escapeHtml(studentName)}</b> (Roll: ${escapeHtml(rollNo)}) &bull; Score: <b class="text-success">${score}</b>
            `;
        }

        if (typeof $ !== 'undefined' && $('#modalAnnotatedSheet').modal) {
            $('#modalAnnotatedSheet').modal('show');
        }
    };

    function resolveSubjectName(sb, qNum) {
        let name = sb?.subject_name;
        if (name && !name.startsWith('Subject #') && name !== 'Subject' && name !== 'General') {
            return name;
        }
        const opt = getSelectedExamOption();
        if (opt && opt.dataset.ranges) {
            try {
                let ranges = opt.dataset.ranges;
                if (typeof ranges === 'string') ranges = JSON.parse(ranges);
                if (typeof ranges === 'string') ranges = JSON.parse(ranges);
                if (Array.isArray(ranges) && ranges.length > 0) {
                    if (sb && sb.subject_id) {
                        const found = ranges.find(r => String(r.subject_id) === String(sb.subject_id));
                        if (found && found.subject_name && !found.subject_name.startsWith('Subject #')) {
                            return found.subject_name;
                        }
                    }
                    if (sb && sb.from_q && sb.to_q) {
                        const found = ranges.find(r => parseInt(r.from_q, 10) === parseInt(sb.from_q, 10) && parseInt(r.to_q, 10) === parseInt(sb.to_q, 10));
                        if (found && found.subject_name && !found.subject_name.startsWith('Subject #')) {
                            return found.subject_name;
                        }
                    }
                    if (qNum) {
                        const found = ranges.find(r => parseInt(qNum, 10) >= parseInt(r.from_q, 10) && parseInt(qNum, 10) <= parseInt(r.to_q, 10));
                        if (found && found.subject_name && !found.subject_name.startsWith('Subject #')) {
                            return found.subject_name;
                        }
                    }
                }
            } catch (e) {}
        }
        if (sb && sb.subject_id && window.examSubjectMap && window.examSubjectMap[sb.subject_id]) {
            return window.examSubjectMap[sb.subject_id];
        }
        return name || `Subject #${sb?.subject_id || (qNum ? 'Q' + qNum : '')}`;
    }

    // Modal: Extracted Answers Breakdown
    window.viewAnswersModal = function (tempId) {
        const sheet = stagedSheets.get(tempId);
        if (!sheet) return;

        const answers = sheet.evaluation?.extracted_answers || [];
        modalAnsStudent.textContent = sheet.student?.name || 'Unregistered Student';
        modalAnsRoll.textContent = sheet.roll_number || 'N/A';
        modalAnsScore.textContent = `+${sheet.evaluation?.obtained_marks || 0} / ${sheet.evaluation?.total_marks || 0} (${sheet.evaluation?.percentage || 0}%)`;

        const subBreakdown = sheet.evaluation?.subject_breakdown || [];
        renderModalBreakdownContent(subBreakdown, answers);

        if (typeof $ !== 'undefined' && $('#modalAnswersGrid').modal) {
            $('#modalAnswersGrid').modal('show');
        }
    };

    window.viewSavedAnswersModal = function (omrId) {
        const rec = (window.savedOmrRecords && window.savedOmrRecords[omrId]) || null;
        if (!rec) return;

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

        renderModalBreakdownContent(subBreakdown, answers);

        if (typeof $ !== 'undefined' && $('#modalAnswersGrid').modal) {
            $('#modalAnswersGrid').modal('show');
        }
    };

    function renderModalBreakdownContent(subBreakdown, answers) {
        const subWrapper = document.getElementById('modal-subject-breakdown-wrapper');
        const subTbody = document.getElementById('modal-subject-breakdown-tbody');

        if (subWrapper && subTbody) {
            if (Array.isArray(subBreakdown) && subBreakdown.length > 0) {
                let subHtml = '';
                subBreakdown.forEach(sb => {
                    const sName = resolveSubjectName(sb);
                    const rangeText = (sb.from_q && sb.to_q) ? `Q${sb.from_q}-${sb.to_q}` : `${sb.total_questions || 0} Qs`;
                    subHtml += `
                        <tr>
                            <td class="font-weight-bold text-dark">${escapeHtml(sName)}</td>
                            <td class="font-mono text-muted">${rangeText}</td>
                            <td class="font-weight-bold text-success">${sb.correct_answers || 0}</td>
                            <td class="font-weight-bold text-danger">${sb.incorrect_answers || 0}</td>
                            <td class="text-muted">${sb.blank_answers || 0}</td>
                            <td class="font-weight-bold font-mono text-primary">+${sb.obtained_marks_formatted || sb.obtained_marks || 0}</td>
                            <td class="font-weight-bold ${parseFloat(sb.percentage || 0) >= 40 ? 'text-success' : 'text-danger'}">${sb.percentage || 0}%</td>
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
                resBadge = '<span class="badge badge-success font-weight-bold px-2 py-1"><i class="fa fa-check"></i> Right</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-success" style="background:#f0fdf4; border:1px solid #bbf7d0;">+${parseFloat(mAwarded).toFixed(2)}</span>`;
            } else if (item.status === 'blank' || !item.answer) {
                resBadge = '<span class="badge badge-light px-2 py-1 text-muted border">Blank</span>';
                marksBadge = `<span class="badge font-mono text-muted border">0.00</span>`;
            } else {
                resBadge = '<span class="badge badge-danger font-weight-bold px-2 py-1"><i class="fa fa-times"></i> Wrong</span>';
                marksBadge = `<span class="badge font-mono font-weight-bold text-danger" style="background:#fef2f2; border:1px solid #fca5a5;">${parseFloat(mAwarded).toFixed(2)}</span>`;
            }

            const qSubName = resolveSubjectName(item, item.question);
            const subTag = qSubName ? `<span class="badge badge-light text-primary font-weight-bold text-truncate" style="max-width:110px; font-size:9px; border:1px solid #cbd5e1;">${escapeHtml(qSubName)}</span>` : '<span class="text-muted small">--</span>';

            html += `
                <tr>
                    <td class="font-weight-bold text-dark">Q${item.question}</td>
                    <td>${subTag}</td>
                    <td class="font-mono font-weight-bold ${item.is_correct ? 'text-success' : (item.answer ? 'text-danger' : 'text-muted')}">${item.answer || '—'}</td>
                    <td class="font-mono font-weight-bold text-primary">${item.correct_answer || '—'}</td>
                    <td>${marksBadge}</td>
                    <td>${resBadge}</td>
                </tr>
            `;
        });
        modalAnswersTbody.innerHTML = html;
    }

    // FINAL SUBMIT: Save marks to database
    window.confirmFinalSubmit = async function () {
        const examId = examSelect ? examSelect.value : '';
        if (!examId) {
            alert('Please select an examination first.');
            return;
        }

        const selectedSheets = [];
        stagedSheets.forEach(s => {
            if (s.selected !== false) selectedSheets.push(s);
        });

        if (!selectedSheets.length) {
            alert('No scanned sheets selected for final submission.');
            return;
        }

        const examName = getSelectedExamOption()?.dataset.name || 'this examination';
        if (!confirm(`Save ${selectedSheets.length} evaluated OMR sheet(s) directly to "${examName}" marks?`)) return;

        btnFinalSubmit.disabled = true;
        btnFinalSubmit.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';

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
                throw new Error(data.message || 'Failed to save marks.');
            }

            showToast('success', data.message);

            // Update student cards on screen
            const recordsToApply = (data.saved_records && Array.isArray(data.saved_records) && data.saved_records.length > 0)
                ? data.saved_records
                : selectedSheets;

            recordsToApply.forEach(rec => {
                if (rec.id) {
                    window.savedOmrRecords = window.savedOmrRecords || {};
                    window.savedOmrRecords[rec.id] = rec;
                }

                const admId = rec.admission_id || (rec.student?.id);
                const roll = String(rec.unique_id || rec.roll_number || '').trim();
                const evalData = rec.evaluation || rec;

                let card = null;
                if (admId) card = document.getElementById(`student-card-${admId}`);
                if (!card && roll) card = findStudentCard({ roll_number: roll });
                if (!card && rec.temp_id) card = document.querySelector(`.mob-student-card[data-temp-id="${rec.temp_id}"]`);

                if (card) {
                    card.classList.remove('card-scanned', 'card-unmatched');
                    card.classList.add('card-saved');
                    card.dataset.status = 'saved';
                    delete card.dataset.tempId;

                    const cellStatus = card.querySelector('.cell-status');
                    if (cellStatus) {
                        cellStatus.innerHTML = `<span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size:10px;"><i class="fa fa-check-circle mr-1"></i> Saved</span>`;
                    }

                    const cellSheet = card.querySelector('.cell-sheet');
                    const imgUrl = rec.image_url || rec.annotated_image_url || rec.stored_file;
                    if (cellSheet && imgUrl) {
                        const fullImgUrl = imgUrl.startsWith('http') ? imgUrl : `{{ url('/') }}/${imgUrl.replace(/^\/+/, '')}`;
                        cellSheet.innerHTML = `<img src="${fullImgUrl}" class="mob-sheet-thumb-img" alt="Scan" onclick="viewSavedOmrModal('${rec.id}', this);" data-image-url="${fullImgUrl}">`;
                    }

                    const cellRoll = card.querySelector('.cell-detected-roll');
                    if (cellRoll) cellRoll.innerHTML = `<span class="text-muted" style="font-size:10px;">Detected: </span><b class="font-mono">${escapeHtml(roll)}</b>`;

                    const cellQ = card.querySelector('.cell-questions');
                    if (cellQ) {
                        const cCount = parseInt(evalData.correct_answers || 0, 10);
                        const iCount = parseInt(evalData.incorrect_answers || 0, 10);
                        const totalQ = (cCount + iCount + parseInt(evalData.blank_answers || 0, 10)) || (getSelectedExamOption()?.dataset.questions || 50);
                        cellQ.innerHTML = `<b style="font-size:11px;">${cCount + iCount} / ${totalQ}</b> <span class="small font-weight-bold ml-1"><span class="text-success">🟢${cCount}</span> <span class="text-danger">🔴${iCount}</span></span>`;
                    }

                    const cellScore = card.querySelector('.cell-score');
                    if (cellScore) {
                        cellScore.innerHTML = `
                            <div>
                                <span class="font-weight-bold text-success" style="font-size:12.5px;">+${parseFloat(evalData.obtained_marks || 0).toFixed(2)}</span>
                                <span class="text-muted small"> / ${evalData.total_marks || (getSelectedExamOption()?.dataset.maxmarks || 100)}</span>
                                <span class="badge badge-success ml-1 font-weight-bold" style="font-size:9.5px;">${evalData.percentage || 0}%</span>
                            </div>
                        `;
                    }

                    const cellActions = card.querySelector('.cell-actions');
                    if (cellActions) {
                        cellActions.innerHTML = `
                            <button type="button" class="btn-card-action btn-outline-primary border" onclick="viewSavedOmrModal('${rec.id}', this);"><i class="fa fa-eye"></i> View</button>
                            <button type="button" class="btn-card-action btn-outline-info border" onclick="viewSavedAnswersModal('${rec.id}');"><i class="fa fa-list"></i></button>
                        `;
                    }
                }

                if (rec.temp_id) {
                    document.getElementById(`staged-unmatched-${rec.temp_id}`)?.remove();
                }
            });

            stagedSheets.clear();
            updateStagingCounters();

            setTimeout(() => { window.location.reload(); }, 1200);
        } catch (err) {
            console.error('Final submit error', err);
            showToast('error', err.message || 'Error saving marks.');
        } finally {
            btnFinalSubmit.disabled = false;
            btnFinalSubmit.innerHTML = '<i class="fa fa-check-circle mr-1"></i> Save Marks';
        }
    };

    // Filter Tabs
    $('.mob-tab-btn').on('click', function () {
        $('.mob-tab-btn').removeClass('active');
        $(this).addClass('active');
        const filter = $(this).data('filter');

        $('.mob-student-card').each(function () {
            const status = $(this).data('status');
            if (filter === 'all') {
                $(this).show();
            } else if (filter === 'scanned') {
                $(this).toggle(status === 'scanned' || status === 'saved');
            } else if (filter === 'pending') {
                $(this).toggle(status === 'pending');
            } else if (filter === 'review') {
                $(this).toggle(status === 'review');
            }
        });
    });

    // Real-Time Student Search
    $('#mobStudentSearch').on('input keyup', function () {
        const q = $(this).val().toLowerCase().trim();
        $('.mob-student-card').each(function () {
            const name = ($(this).data('studentName') || '').toLowerCase();
            const adm = ($(this).data('admissionNo') || '').toLowerCase();
            const roll = ($(this).data('rollNo') || '').toLowerCase();
            if (q === '' || name.includes(q) || adm.includes(q) || roll.includes(q)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
</script>
@endsection

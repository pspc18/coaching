@extends('layout.app')

@section('content')
@php
  $formExam = $savedExam ?? null;
  $previewName = old('name', $formExam->name ?? 'Sample Examination');
  $previewCode = old('code', $formExam->code ?? $formExam->exam_code ?? ($examCode ?? ('EXAM-' . strtoupper(\Illuminate\Support\Str::random(6)))));
  $previewQuestions = (int) old('number_of_questions', $formExam->number_of_questions ?? 50);
  $previewInstituteName = $instituteName ?? (Helper::getSetting()->name ?? 'ARISE ERP INSTITUTE');
  $previewAnswerKey = old('answer_key', $formExam->answer_key ?? []);
  $previewSubjectId = old('subject_id', $formExam->subject_id ?? '');
  $previewClassId = old('class_type_id', $formExam->class_type_id ?? '');

  // Determine initial mode
  $initialQuestions = old('online_questions', $formExam->online_questions ?? []);
  if (is_string($initialQuestions)) {
      $initialQuestions = json_decode($initialQuestions, true) ?: [];
  }
  $initialMode = old('exam_creation_mode', !empty($initialQuestions) ? 'question_bank' : (!empty($formExam->question_paper_path) ? 'pdf_upload' : 'pdf_upload'));
@endphp

<style>
/* ==========================================================================
   ARISE ERP SIGNATURE THEME STYLING
   ========================================================================== */
.omr-page-wrapper {
    padding: 10px 14px 25px 14px;
    background: #f4f6f9;
    min-height: 100vh;
}

/* 1. Hero Header Banner */
.perf-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.18);
    margin-bottom: 10px;
}
.perf-hero-text {
    display: flex;
    flex-direction: column;
}
.perf-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .9;
    display: block;
    margin-bottom: 2px;
    color: #38bdf8;
    font-weight: 700;
}
.perf-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.perf-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .88;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.perf-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 28px;
    line-height: 1;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001833 !important;
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.3);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}
.dash-btn-emerald {
    background: #059669;
    color: #ffffff !important;
    border-color: #059669;
}
.dash-btn-emerald:hover {
    background: #047857;
    color: #ffffff !important;
}
.dash-btn-sky {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
}
.dash-btn-sky:hover {
    background: #0369a1;
    color: #ffffff !important;
}

/* Workflow Hero Navigation */
.workflow-hero-nav {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.workflow-hero-nav .dash-btn {
    height: 28px;
    font-size: 11px;
}
.workflow-hero-nav .dash-btn.active {
    background: #ffffff !important;
    color: #002C54 !important;
    border-color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}

/* Mode Selection Cards */
.mode-selector-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 12px;
}
.mode-option-card {
    background: #ffffff;
    border: 2px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 14px;
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    position: relative;
    user-select: none;
}
.mode-option-card:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}
.mode-option-card.active {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 2px 8px rgba(0, 44, 84, 0.12);
}
.mode-option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.mode-icon-circle {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    transition: all 0.18s ease;
}
.mode-option-card.active .mode-icon-circle {
    background: #002C54;
    color: #ffffff;
}
.mode-card-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 2px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mode-option-card.active .mode-card-title {
    color: #002C54;
}
.mode-card-desc {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
    line-height: 1.35;
}
.mode-badge-pill {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 1px 6px;
    border-radius: 10px;
    background: #e2e8f0;
    color: #475569;
}
.mode-option-card.active .mode-badge-pill {
    background: #002C54;
    color: #ffffff;
}

/* Theme Cards */
.theme-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 10px;
    overflow: hidden;
}
.theme-card-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}
.theme-card-header .card-title {
    font-size: 12px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin: 0 !important;
    color: #ffffff !important;
    line-height: 1.2 !important;
}
.theme-card-header .header-badge {
    font-size: 10px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 2px 8px;
    border-radius: 2px;
    color: #ffffff !important;
    font-weight: 700;
}
.theme-card-body {
    padding: 12px;
}

/* Form inputs */
.theme-form-group {
    margin-bottom: 10px;
}
.theme-form-group label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    display: block;
}
.theme-form-control {
    height: 29px;
    padding: 4px 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    color: #0f172a !important;
    background-color: #ffffff !important;
    width: 100%;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.theme-form-control:focus {
    border-color: #002C54 !important;
    outline: 0;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15) !important;
    background-color: #ffffff !important;
    color: #0f172a !important;
}
.theme-form-control[readonly],
.theme-form-control:disabled {
    background-color: #f1f5f9 !important;
    color: #002C54 !important;
    cursor: not-allowed;
    border-color: #cbd5e1 !important;
}
textarea.theme-form-control {
    height: auto !important;
}
.theme-hint {
    font-size: 10px;
    color: #64748b;
    margin-top: 2px;
}

/* Quick Question Chips */
.quick-chip-btn {
    display: inline-block;
    padding: 2px 8px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #f8fafc;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    margin-right: 3px;
    transition: all 0.12s ease;
}
.quick-chip-btn:hover, .quick-chip-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Answer Key Matrix */
.answer-key-box {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    overflow: hidden;
}
.answer-key-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 4px;
}
.ak-btn-group button {
    padding: 2px 6px;
    font-size: 9.5px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.12s;
}
.ak-btn-group button:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.answer-key-list {
    max-height: 270px;
    overflow-y: auto;
    padding: 4px 6px;
}
.ak-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 6px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11px;
}
.ak-row:last-child {
    border-bottom: none;
}
.ak-row:hover {
    background: #f8fafc;
}
.ak-qnum {
    font-weight: 700;
    color: #002C54;
    width: 36px;
    flex-shrink: 0;
}
.ak-options {
    display: flex;
    gap: 5px;
    flex: 1;
    justify-content: flex-end;
}
.ak-option-label {
    cursor: pointer;
    margin: 0;
}
.ak-option-label input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.ak-option-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 22px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    background: #ffffff;
    transition: all 0.12s ease;
    user-select: none;
}
.ak-option-label input:checked + .ak-option-pill {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.25);
}

/* Question Bank Selected Questions Cards */
.selected-q-card {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    padding: 8px 10px;
    margin-bottom: 8px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.selected-q-card:hover {
    border-color: #94a3b8;
    box-shadow: 0 2px 5px rgba(0,0,0,0.06);
}
.selected-q-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 4px;
    border-bottom: 1px dashed #e2e8f0;
}
.selected-q-badges {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.q-badge-num {
    background: #002C54;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 2px;
}
.q-badge-type {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
}
.q-badge-ans {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
}
.selected-q-text {
    font-size: 11.5px;
    color: #0f172a;
    line-height: 1.45;
    margin-bottom: 6px;
}
.selected-q-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    font-size: 11px;
}
.opt-pill-preview {
    padding: 3px 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    color: #334155;
}
.opt-pill-preview.is-correct {
    background: #ecfdf5;
    border-color: #10b981;
    color: #065f46;
    font-weight: 700;
}
.selected-q-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-q-action {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    padding: 2px 6px;
    font-size: 10px;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.12s;
}
.btn-q-action:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.btn-q-action.btn-q-delete:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
}

/* OMR Preview Canvas / SVG */
.omr-sheet-card {
    background: #e2e8f0;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px;
    text-align: center;
}
.omr-sheet-paper {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    display: inline-block;
    width: 100%;
    max-width: 630px;
    margin: 0 auto;
}

/* Printable Question Paper Layout */
.paper-preview-container {
    background: #525659;
    padding: 12px;
    border-radius: 2px;
    overflow-y: auto;
    max-height: calc(100vh - 180px);
}
.printable-exam-paper {
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    padding: 28px 32px;
    max-width: 780px;
    margin: 0 auto;
    font-family: "DejaVu Sans", "Segoe UI", Arial, sans-serif;
    color: #111827;
}
.exam-paper-header {
    text-align: center;
    border-bottom: 2px solid #002C54;
    padding-bottom: 10px;
    margin-bottom: 12px;
}
.exam-paper-institute {
    font-size: 17px;
    font-weight: 800;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0 0 2px;
}
.exam-paper-exam-name {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 6px;
}
.exam-paper-meta-table {
    width: 100%;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    border-collapse: collapse;
}
.exam-paper-meta-table td {
    padding: 2px 6px;
}
.exam-instructions-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    padding: 6px 10px;
    font-size: 10.5px;
    color: #475569;
    margin-bottom: 14px;
    border-radius: 2px;
}
.paper-question-block {
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
}
.paper-question-block:last-child {
    border-bottom: none;
}
.paper-q-statement {
    font-size: 11.5px;
    font-weight: 600;
    line-height: 1.45;
    margin-bottom: 6px;
    color: #0f172a;
}
.paper-q-num {
    font-weight: 800;
    color: #002C54;
    margin-right: 4px;
}
.paper-q-marks {
    float: right;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
}
.paper-q-options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px 14px;
    font-size: 11px;
    padding-left: 14px;
}
.paper-q-opt {
    color: #1e293b;
    line-height: 1.35;
}
.paper-q-opt-label {
    font-weight: 700;
    color: #002C54;
    margin-right: 4px;
}
.paper-q-diagram {
    max-height: 140px;
    display: block;
    margin: 6px 0;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 2px;
}

/* Modal styling */
.modal-header-theme {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 8px 14px;
}
.modal-header-theme .modal-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
}
.modal-filter-bar {
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.modal-questions-scroll {
    max-height: 60vh;
    overflow-y: auto;
    padding: 10px 12px;
}
.modal-q-item {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 10px;
    margin-bottom: 6px;
    background: #ffffff;
    transition: background 0.1s;
    cursor: pointer;
}
.modal-q-item:hover {
    background: #f8fafc;
}
.modal-q-item.selected {
    background: #f0f7ff;
    border-color: #0284c7;
}

/* Print Rules */
@media print {
    @page {
        size: A4 portrait;
        margin: 8mm;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    body * {
        visibility: hidden !important;
    }
    body.printing-omr #exam-omr-print-area,
    body.printing-omr #exam-omr-print-area * {
        visibility: visible !important;
    }
    body.printing-omr #exam-omr-print-area {
        position: absolute !important;
        left: 50% !important;
        top: 0 !important;
        width: 194mm !important;
        max-width: none !important;
        transform: translateX(-50%) !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    body.printing-paper #exam-paper-print-area,
    body.printing-paper #exam-paper-print-area * {
        visibility: visible !important;
    }
    body.printing-paper #exam-paper-print-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: none !important;
        border: 0 !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 10mm 12mm !important;
        background: #ffffff !important;
    }
}
</style>

<div class="content-wrapper">
    <div class="omr-page-wrapper" id="exam-builder-app">

        {{-- 1. Hero Header Banner --}}
        <div class="perf-hero">
            <div class="perf-hero-text">
                <span class="perf-kicker">
                    <i class="fa fa-graduation-cap"></i> Examination & OMR Sheet System
                </span>
                <h1 class="perf-title">
                    <i class="fa fa-pencil-square-o"></i> Offline Exam & OMR Sheet Creator
                </h1>
                <p class="perf-subtitle">
                    <span>Session: {{ Session::get('session_name') ?? date('Y') }}</span> &bull;
                    <span>Support for Question Paper PDF Attachment or Direct Question Bank Integration with OMR Sheet</span>
                </p>
            </div>
            <div class="perf-hero-actions">
                @php
                    $workflowClassId = $search['class_type_id'] ?? request('class_type_id') ?? request('class_name');
                    $workflowExamId = $search['exam_id'] ?? request('exam_id');
                    $workflowQuery = array_filter([
                        'class_type_id' => $workflowClassId,
                        'exam_id' => $workflowExamId,
                    ]);
                    $wfSteps = [
                        ['title' => '1. Exam Setup', 'url' => url('view/exam'), 'active' => true, 'icon' => 'fa fa-check-circle'],
                        ['title' => '2. Excel Import', 'url' => url('fill-marks-by-excel'), 'active' => false, 'icon' => 'fa fa-file-excel-o'],
                        ['title' => '3. Manual Marks', 'url' => url('fill_marks').($workflowQuery ? '?'.http_build_query(array_merge($workflowQuery, ['show_all_subjects' => 1])) : '?show_all_subjects=1'), 'active' => false, 'icon' => 'fa fa-pencil'],
                        ['title' => '4. Exam Report', 'url' => url('exam_wise_report').($workflowQuery ? '?'.http_build_query($workflowQuery) : ''), 'active' => false, 'icon' => 'fa fa-bar-chart'],
                    ];
                @endphp
                <div class="workflow-hero-nav">
                    @foreach($wfSteps as $step)
                        <a href="{{ $step['url'] }}" class="dash-btn {{ $step['active'] ? 'dash-btn-light active' : 'dash-btn-outline' }}" title="{{ $step['title'] }}">
                            <i class="{{ $step['icon'] }} mr-1"></i> {{ $step['title'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error') || (isset($errors) && $errors->any()))
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') ?? (isset($errors) ? $errors->first() : '') }}
                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Form Container --}}
        <form id="exam-create-form" action="{{ url('add/exam') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="action_type" id="form-action-type" value="save">
            <input type="hidden" name="selected_questions_json" id="selected_questions_json" value="{{ json_encode($initialQuestions, JSON_UNESCAPED_UNICODE) }}">

            {{-- 2-Mode Selector Cards --}}
            <div class="mode-selector-grid">
                {{-- Mode 1 Card --}}
                <label class="mode-option-card {{ $initialMode === 'pdf_upload' ? 'active' : '' }}" id="card-mode-pdf">
                    <input type="radio" name="exam_creation_mode" value="pdf_upload" {{ $initialMode === 'pdf_upload' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-file-pdf-o"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 1: Upload Question Paper (PDF) + OMR</span>
                            <span class="mode-badge-pill">Quick Offline</span>
                        </div>
                        <p class="mode-card-desc">
                            Total questions count dalo, Question Paper PDF upload karo, answer key set karo aur students ke liye Printable OMR Sheet generate karo.
                        </p>
                    </div>
                </label>

                {{-- Mode 2 Card --}}
                <label class="mode-option-card {{ $initialMode === 'question_bank' ? 'active' : '' }}" id="card-mode-qbank">
                    <input type="radio" name="exam_creation_mode" value="question_bank" {{ $initialMode === 'question_bank' ? 'checked' : '' }}>
                    <div class="mode-icon-circle">
                        <i class="fa fa-book"></i>
                    </div>
                    <div>
                        <div class="mode-card-title">
                            <span>Mode 2: Question Bank + OMR with Questions</span>
                            <span class="mode-badge-pill">Complete Paper</span>
                        </div>
                        <p class="mode-card-desc">
                            Question Bank se questions (MCQ/Descriptive with LaTeX Math) select karo, automatic answer key sync karo aur Question Paper + OMR Sheet dono print karo.
                        </p>
                    </div>
                </label>
            </div>

            {{-- Two-Column Layout: Left Form Details + Right Previews --}}
            <div class="row">
                {{-- Left Column: Parameters & Question Configuration --}}
                <div class="col-lg-5 col-xl-5">

                    {{-- Card 1: Common Examination Parameters --}}
                    <div class="theme-card">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-sliders text-warning"></i> Examination Parameters
                            </h3>
                            <span class="header-badge" id="exam-code-badge">{{ $previewCode }}</span>
                        </div>
                        <div class="theme-card-body">
                            {{-- Exam Name --}}
                            <div class="theme-form-group">
                                <label for="exam-name">Exam Name <span class="text-danger">*</span></label>
                                <input type="text" id="exam-name" name="name" class="theme-form-control @error('name') is-invalid @enderror" placeholder="e.g. NEET Weekly Test 01 / Term 1 Science" value="{{ $previewName }}" required maxlength="255">
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            <div class="row">
                                {{-- Target Class --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="class_type_id">Target Class <span class="text-danger">*</span></label>
                                        <select class="theme-form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" required>
                                            <option value="">-- Select Class --</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $type)
                                                    <option value="{{ $type->id }}" {{ (old('class_type_id', $previewClassId) == $type->id) ? 'selected' : '' }}>
                                                        {{ $type->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('class_type_id')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                {{-- Subject --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="subject_id">Subject <span class="text-muted">(Optional)</span></label>
                                        <select class="theme-form-control" id="subject_id" name="subject_id">
                                            <option value="">-- Select Subject --</option>
                                            @if(!empty($subjects))
                                                @foreach($subjects as $sub)
                                                    <option value="{{ $sub->id }}" {{ ($previewSubjectId == $sub->id) ? 'selected' : '' }}>
                                                        {{ $sub->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Examination Date --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="exam-date">Date of Exam <span class="text-danger">*</span></label>
                                        <input type="date" id="exam-date" name="exam_date" class="theme-form-control" value="{{ old('exam_date', (!empty($formExam) && !empty($formExam->exam_date)) ? (is_object($formExam->exam_date) ? $formExam->exam_date->format('Y-m-d') : $formExam->exam_date) : date('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                {{-- Exam Code --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="exam-code">Exam Code</label>
                                        <input type="text" id="exam-code" name="code" class="theme-form-control font-weight-bold" value="{{ $previewCode }}" readonly>
                                        <div class="theme-hint"><i class="fa fa-lock"></i> Auto-generated scanner code</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Correct Marks --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="marks-per-correct">Marks for Correct <span class="text-danger">*</span></label>
                                        <input type="number" id="marks-per-correct" name="marks_per_correct" class="theme-form-control" value="{{ old('marks_per_correct', $formExam->marks_per_correct ?? 1) }}" min="0" max="999" step="0.01" required>
                                    </div>
                                </div>

                                {{-- Negative Marks --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="marks-per-incorrect">Negative Marks</label>
                                        <input type="number" id="marks-per-incorrect" name="marks_per_incorrect" class="theme-form-control" value="{{ old('marks_per_incorrect', $formExam->marks_per_incorrect ?? 0) }}" min="-999" max="0" step="0.01">
                                        <div class="theme-hint">Use 0 for none, or -1, -0.25</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Description / Notes --}}
                            <div class="theme-form-group mb-0">
                                <label for="description">Instructions / Remarks</label>
                                <textarea id="description" name="description" rows="2" class="theme-form-control" placeholder="Optional examination notes or instructions...">{{ old('description', $formExam->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Pane 1: Mode 1 (PDF Upload + OMR Key) --}}
                    <div id="pane-mode-pdf-upload" style="{{ $initialMode === 'pdf_upload' ? '' : 'display: none;' }}">
                        {{-- Questions Count & PDF File --}}
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-file-pdf-o text-warning"></i> Question Paper PDF & Count
                                </h3>
                                <span class="header-badge" id="pdf-qcount-badge">{{ $previewQuestions }} Questions</span>
                            </div>
                            <div class="theme-card-body">
                                {{-- Number of Questions --}}
                                <div class="theme-form-group">
                                    <label for="exam-question-count">Total Questions <span class="text-danger">*</span></label>
                                    <input type="number" id="exam-question-count" name="number_of_questions" class="theme-form-control" value="{{ $previewQuestions }}" min="1" max="100">
                                </div>

                                {{-- Quick Question Count Chips --}}
                                <div class="mb-2">
                                    <span class="small font-weight-bold text-muted mr-1" style="font-size: 10px;">Quick Questions:</span>
                                    <span class="quick-chip-btn" data-count="20">20 Qs</span>
                                    <span class="quick-chip-btn" data-count="30">30 Qs</span>
                                    <span class="quick-chip-btn {{ $previewQuestions == 50 ? 'active' : '' }}" data-count="50">50 Qs</span>
                                    <span class="quick-chip-btn" data-count="75">75 Qs</span>
                                    <span class="quick-chip-btn" data-count="100">100 Qs</span>
                                </div>

                                {{-- Question Paper PDF --}}
                                <div class="theme-form-group mb-0">
                                    <label for="exam-question-paper">Question Paper <span class="text-muted font-weight-normal">(PDF up to 10MB)</span></label>
                                    <input type="file" id="exam-question-paper" name="question_paper" accept="application/pdf,.pdf" class="theme-form-control" style="height: 33px; padding: 3px;">
                                    @if(!empty($formExam->question_paper_path))
                                        <div class="mt-1">
                                            <a href="{{ asset($formExam->question_paper_path) }}" target="_blank" class="badge badge-primary py-1 px-2">
                                                <i class="fa fa-file-pdf-o mr-1"></i> View Attached Question Paper
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Answer Key Matrix (Mode 1) --}}
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-key text-warning"></i> Answer Key (OMR Options)
                                </h3>
                                <span class="header-badge" id="answer-key-progress">0 / {{ $previewQuestions }} Selected</span>
                            </div>
                            <div class="theme-card-body p-2">
                                <div class="answer-key-box">
                                    <div class="answer-key-toolbar">
                                        <span class="small font-weight-bold text-muted" style="font-size: 10px;">Select correct option:</span>
                                        <div class="ak-btn-group">
                                            <button type="button" data-fill="A">All A</button>
                                            <button type="button" data-fill="B">All B</button>
                                            <button type="button" data-fill="C">All C</button>
                                            <button type="button" data-fill="D">All D</button>
                                            <button type="button" data-fill="RANDOM">Random</button>
                                            <button type="button" data-fill="CLEAR">Clear</button>
                                        </div>
                                    </div>
                                    <div class="answer-key-list" id="exam-answer-key-grid">
                                        {{-- Rendered by JS --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pane 2: Mode 2 (Question Bank Questions) --}}
                    <div id="pane-mode-question-bank" style="{{ $initialMode === 'question_bank' ? '' : 'display: none;' }}">
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-list-ol text-warning"></i> Questions From Question Bank
                                </h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="header-badge" id="selected-questions-count-badge">0 Questions</span>
                                    <button type="button" id="btn-open-question-modal" class="dash-btn dash-btn-light" style="height: 22px; font-size: 10px;">
                                        <i class="fa fa-plus-circle"></i> Browse Question Bank
                                    </button>
                                </div>
                            </div>
                            <div class="theme-card-body p-2">
                                {{-- Empty state message --}}
                                <div id="qbank-empty-state" class="text-center py-4 text-muted" style="border: 2px dashed #cbd5e1; border-radius: 4px; background: #f8fafc;">
                                    <i class="fa fa-book fa-2x mb-2 text-secondary"></i>
                                    <p class="font-weight-bold mb-1" style="font-size: 12px; color: #334155;">No Questions Added Yet</p>
                                    <p class="small text-muted mb-3" style="font-size: 10.5px;">Click the button below to browse questions from your Question Bank and attach them to this exam.</p>
                                    <button type="button" id="btn-open-question-modal-empty" class="dash-btn dash-btn-sky">
                                        <i class="fa fa-search mr-1"></i> Browse Question Bank
                                    </button>
                                </div>

                                {{-- Selected Questions Container --}}
                                <div id="selected-questions-list" style="max-height: 480px; overflow-y: auto;">
                                    {{-- Rendered by JS --}}
                                </div>

                                {{-- Auto-derived Answer Key Review (Collapsible) --}}
                                <div class="mt-2 border-top pt-2">
                                    <a class="d-flex align-items-center justify-content-between text-decoration-none" data-toggle="collapse" href="#qbank-answer-key-collapse" role="button">
                                        <span class="small font-weight-bold text-primary" style="font-size: 11px;">
                                            <i class="fa fa-key mr-1"></i> View Auto-Derived Answer Key (<span id="qbank-key-count">0</span>)
                                        </span>
                                        <i class="fa fa-chevron-down text-muted small"></i>
                                    </a>
                                    <div class="collapse mt-2" id="qbank-answer-key-collapse">
                                        <div class="answer-key-box">
                                            <div class="answer-key-list" id="qbank-derived-answer-key-grid" style="max-height: 180px;">
                                                {{-- Derived answer key rows --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions Bar --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button type="button" id="btn-submit-save" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; flex:1; justify-content:center; height:32px;">
                            <i class="fa fa-save"></i> Save Exam
                        </button>
                        <button type="button" id="btn-submit-print-omr" class="dash-btn dash-btn-light" style="border: 1px solid #cbd5e1; height:32px;">
                            <i class="fa fa-print"></i> Save & Print OMR
                        </button>
                        <button type="button" id="btn-submit-print-paper" class="dash-btn dash-btn-sky" style="height:32px; {{ $initialMode === 'question_bank' ? '' : 'display: none;' }}">
                            <i class="fa fa-file-text-o"></i> Save & Print Paper
                        </button>
                        <button type="button" id="btn-submit-marks" class="dash-btn dash-btn-emerald" style="height:32px;">
                            <i class="fa fa-pencil"></i> Save & Fill Marks
                        </button>
                    </div>

                </div>

                {{-- Right Column: Live Previews (OMR Sheet & Question Paper) --}}
                <div class="col-lg-7 col-xl-7">

                    {{-- Mode 1 Right Panel: OMR Sheet Live Preview --}}
                    <div id="right-panel-omr" style="{{ $initialMode === 'pdf_upload' ? '' : 'display: none;' }}">
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-eye text-warning"></i> Printable OMR Sheet Preview
                                </h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="header-badge" id="exam-preview-count">{{ $previewQuestions }} Questions</span>
                                    <button type="button" class="dash-btn dash-btn-light py-0 px-2 btn-trigger-print-omr" style="height: 22px; font-size: 10px;">
                                        <i class="fa fa-print"></i> Print OMR Sheet
                                    </button>
                                </div>
                            </div>

                            <div class="theme-card-body p-2 omr-sheet-card">
                                <div class="omr-sheet-paper" id="exam-omr-print-area">
                                    <svg id="exam-omr-svg" viewBox="0 0 800 1100" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" class="d-block w-100 h-auto bg-white" aria-label="Printable exam OMR sheet">
                                        <rect width="800" height="1100" fill="white"/>
                                        <rect x="24" y="24" width="752" height="1052" fill="none" stroke="#111827" stroke-width="3"/>
                                        
                                        {{-- Corner Marks --}}
                                        @foreach([[35,35],[739,35],[35,1039],[739,1039]] as $mark)
                                            <rect x="{{ $mark[0] }}" y="{{ $mark[1] }}" width="26" height="26" fill="#000000"/>
                                        @endforeach

                                        {{-- School / Institute Header --}}
                                        <text x="400" y="70" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="24" font-weight="800" fill="#002C54">{{ mb_strtoupper($previewInstituteName) }}</text>
                                        <text x="400" y="94" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="600" fill="#475569">OPTICAL MARK RECOGNITION (OMR) ANSWER SHEET</text>
                                        
                                        {{-- Exam Meta --}}
                                        <text id="omr-preview-name" x="70" y="128" text-anchor="start" font-family="Arial, Helvetica, sans-serif" font-size="12" font-weight="700" fill="#0f172a">EXAM: {{ mb_strtoupper($previewName) }}</text>
                                        <text id="omr-preview-code" x="730" y="128" text-anchor="end" font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="700" fill="#002C54">EXAM CODE: {{ $previewCode }}</text>
                                        <line x1="70" y1="145" x2="730" y2="145" stroke="#002C54" stroke-width="2"/>

                                        {{-- Student Info Lines --}}
                                        <text x="75" y="174" font-family="Arial" font-size="13" font-weight="700">Student Name:</text>
                                        <line x1="185" y1="176" x2="470" y2="176" stroke="#94a3b8"/>
                                        <text x="500" y="174" font-family="Arial" font-size="13" font-weight="700">Date:</text>
                                        <line x1="545" y1="176" x2="720" y2="176" stroke="#94a3b8"/>

                                        {{-- Roll Number Matrix --}}
                                        <text x="70" y="202" font-family="Arial" font-size="13" font-weight="800" fill="#002C54">ROLL NUMBER (FILL ONE BUBBLE PER DIGIT)</text>
                                        @for($column=0;$column<6;$column++)
                                            @php($rx=70+$column*110)
                                            <rect x="{{ $rx }}" y="212" width="100" height="190" fill="none" stroke="#94a3b8" stroke-width="1"/>
                                            <text x="{{ $rx+50 }}" y="228" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700">DIGIT {{ $column+1 }}</text>
                                            @for($digit=0;$digit<10;$digit++)
                                                @php($ry=245+$digit*15.5)
                                                <text x="{{ $rx+24 }}" y="{{ $ry+3 }}" text-anchor="middle" font-family="Arial" font-size="8.5" fill="#334155">{{ $digit }}</text>
                                                <circle cx="{{ $rx+50 }}" cy="{{ $ry }}" r="6" fill="white" stroke="#000000" stroke-width="1.2"/>
                                                <line x1="{{ $rx+7 }}" y1="{{ $ry+7 }}" x2="{{ $rx+93 }}" y2="{{ $ry+7 }}" stroke="#f1f5f9" stroke-width="0.8"/>
                                            @endfor
                                        @endfor

                                        {{-- Question Matrix Header --}}
                                        <text id="omr-question-total" x="70" y="432" font-family="Arial" font-size="14" font-weight="800" fill="#002C54">ANSWERS — {{ $previewQuestions }} QUESTIONS</text>
                                        <text x="730" y="432" text-anchor="end" font-family="Arial" font-size="10" font-weight="700" fill="#64748b">DARKEN THE APPROPRIATE BUBBLE COMPLETELY</text>
                                        
                                        {{-- Dynamic Question Grid --}}
                                        <g id="omr-question-grid"></g>

                                        {{-- Instructions Footer --}}
                                        <text x="400" y="1042" text-anchor="middle" font-family="Arial" font-size="10" font-weight="700" fill="#0f172a">INSTRUCTIONS: Use BLUE or BLACK Ball Point Pen | Fill only one circle per question | Do not fold or tear</text>
                                        <text x="400" y="1060" text-anchor="middle" font-family="Arial" font-size="9.5" font-weight="600" fill="#475569">Keep sheet flat and clean | Ensure all 4 black corner squares remain clean &amp; visible for OMR Optical Scanner</text>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mode 2 Right Panel: Dual Tabs (Question Paper & OMR Sheet) --}}
                    <div id="right-panel-qbank" style="{{ $initialMode === 'question_bank' ? '' : 'display: none;' }}">
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <ul class="nav nav-pills" id="qbank-preview-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active py-1 px-3" id="tab-paper-btn" data-toggle="pill" href="#tab-paper-content" role="tab" style="font-size: 11px; font-weight: 700; color: #ffffff;">
                                            <i class="fa fa-file-text-o mr-1"></i> Question Paper Preview
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-1 px-3" id="tab-omr-btn" data-toggle="pill" href="#tab-omr-content" role="tab" style="font-size: 11px; font-weight: 700; color: #ffffff;">
                                            <i class="fa fa-th mr-1"></i> Matching OMR Sheet
                                        </a>
                                    </li>
                                </ul>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" id="btn-print-active-tab" class="dash-btn dash-btn-light py-0 px-2" style="height: 22px; font-size: 10px;">
                                        <i class="fa fa-print"></i> <span id="print-active-label">Print Question Paper</span>
                                    </button>
                                </div>
                            </div>

                            <div class="theme-card-body p-0">
                                <div class="tab-content">
                                    {{-- Tab 1: Printable Question Paper --}}
                                    <div class="tab-pane fade show active" id="tab-paper-content" role="tabpanel">
                                        <div class="paper-preview-container">
                                            <div class="printable-exam-paper" id="exam-paper-print-area">
                                                {{-- Paper Header --}}
                                                <div class="exam-paper-header">
                                                    <h2 class="exam-paper-institute">{{ mb_strtoupper($previewInstituteName) }}</h2>
                                                    <h3 class="exam-paper-exam-name" id="paper-preview-title">{{ $previewName }}</h3>
                                                    <table class="exam-paper-meta-table">
                                                        <tr>
                                                            <td style="text-align: left; width: 33%;"><strong>Class:</strong> <span id="paper-preview-class">--</span></td>
                                                            <td style="text-align: center; width: 34%;"><strong>Subject:</strong> <span id="paper-preview-subject">All Subjects</span></td>
                                                            <td style="text-align: right; width: 33%;"><strong>Date:</strong> <span id="paper-preview-date">{{ date('d M Y') }}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align: left;"><strong>Code:</strong> {{ $previewCode }}</td>
                                                            <td style="text-align: center;"><strong>Total Questions:</strong> <span id="paper-preview-total-q">0</span></td>
                                                            <td style="text-align: right;"><strong>Max. Marks:</strong> <span id="paper-preview-max-marks">0</span></td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                {{-- Instructions --}}
                                                <div class="exam-instructions-box">
                                                    <strong>General Instructions:</strong>
                                                    <ul class="mb-0 pl-3">
                                                        <li>All questions are compulsory. Fill answers in the accompanying OMR answer sheet.</li>
                                                        <li>Each correct answer carries <strong id="paper-correct-marks-label">1.0</strong> mark(s). Negative marking: <strong id="paper-negative-marks-label">0</strong>.</li>
                                                        <li>Use blue or black ball point pen only to darken the bubbles on the OMR sheet.</li>
                                                    </ul>
                                                </div>

                                                {{-- Questions List Container --}}
                                                <div id="exam-paper-questions-flow">
                                                    <div class="text-center py-4 text-muted">
                                                        <em>Please add questions from Question Bank to generate paper preview...</em>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tab 2: Matching OMR Sheet --}}
                                    <div class="tab-pane fade" id="tab-omr-content" role="tabpanel">
                                        <div class="omr-sheet-card p-2">
                                            {{-- Uses same SVG element or clone --}}
                                            <div class="text-center mb-1">
                                                <span class="small font-weight-bold text-muted">OMR Sheet configured automatically for <span id="qbank-omr-count-label">0</span> questions</span>
                                            </div>
                                            <div class="omr-sheet-paper" id="exam-omr-mode2-paper">
                                                {{-- Injected / mirrored from SVG --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>

{{-- 3. Question Bank Picker Modal --}}
<div class="modal fade" id="modal-question-bank" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="max-width: 95%;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1;">
            <div class="modal-header modal-header-theme">
                <h5 class="modal-title">
                    <i class="fa fa-book mr-1"></i> Select Questions from Question Bank
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Filter Bar --}}
            <div class="modal-filter-bar">
                <div style="min-width: 160px;">
                    <label class="small font-weight-bold mb-0 text-muted" style="font-size: 10px;">Class:</label>
                    <select id="modal-filter-class" class="theme-form-control">
                        <option value="">-- All Classes --</option>
                        @if(!empty($classType))
                            @foreach($classType as $type)
                                <option value="{{ $type->id }}" {{ $previewClassId == $type->id ? 'selected' : '' }}>
                                    {{ $type->name ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="min-width: 160px;">
                    <label class="small font-weight-bold mb-0 text-muted" style="font-size: 10px;">Subject:</label>
                    <select id="modal-filter-subject" class="theme-form-control">
                        <option value="">-- All Subjects --</option>
                        @if(!empty($subjects))
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ $previewSubjectId == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div style="min-width: 140px;">
                    <label class="small font-weight-bold mb-0 text-muted" style="font-size: 10px;">Question Type:</label>
                    <select id="modal-filter-type" class="theme-form-control">
                        <option value="">All Question Types</option>
                        <option value="1">1: Multiple Choice (MCQ)</option>
                        <option value="2">2: Numerical / Descriptive</option>
                    </select>
                </div>

                <div class="flex-grow-1" style="min-width: 180px;">
                    <label class="small font-weight-bold mb-0 text-muted" style="font-size: 10px;">Search Questions:</label>
                    <input type="text" id="modal-filter-search" class="theme-form-control" placeholder="Search by question text or formula...">
                </div>

                <div style="align-self: flex-end;">
                    <button type="button" id="modal-btn-refresh" class="dash-btn dash-btn-sky" style="height: 29px;">
                        <i class="fa fa-search mr-1"></i> Filter Questions
                    </button>
                </div>
            </div>

            {{-- Questions List Container --}}
            <div class="modal-questions-scroll" id="modal-questions-container">
                <div class="text-center py-5 text-muted" id="modal-loading-indicator">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                    <p class="mb-0" style="font-size: 12px;">Loading questions from Question Bank...</p>
                </div>
                <div id="modal-questions-list">
                    {{-- Dynamically populated via AJAX --}}
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer py-2 px-3 bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <label class="mb-0 small font-weight-bold" style="cursor: pointer;">
                        <input type="checkbox" id="modal-select-all-visible" class="mr-1"> Select All Visible
                    </label>
                    <span class="badge badge-info py-1 px-2" id="modal-selected-badge" style="font-size: 11px;">0 Selected</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="modal-btn-apply-selection" class="btn btn-sm btn-success font-weight-bold px-3">
                        <i class="fa fa-check mr-1"></i> Add Selected Questions (<span id="modal-apply-count">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KaTeX for LaTeX Math Rendering --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------
    // Core Elements & State
    // -------------------------------------------------------------
    const form = document.getElementById('exam-create-form');
    const nameInput = document.getElementById('exam-name');
    const codeInput = document.getElementById('exam-code');
    const classSelect = document.getElementById('class_type_id');
    const subjectSelect = document.getElementById('subject_id');
    const dateInput = document.getElementById('exam-date');
    const countInput = document.getElementById('exam-question-count');
    const marksCorrectInput = document.getElementById('marks-per-correct');
    const marksIncorrectInput = document.getElementById('marks-per-incorrect');

    const panePdf = document.getElementById('pane-mode-pdf-upload');
    const paneQBank = document.getElementById('pane-mode-question-bank');
    const rightPanelOmr = document.getElementById('right-panel-omr');
    const rightPanelQBank = document.getElementById('right-panel-qbank');
    const btnSubmitPaper = document.getElementById('btn-submit-print-paper');

    const grid = document.getElementById('omr-question-grid');
    const answerKeyGrid = document.getElementById('exam-answer-key-grid');
    const qbankAnswerKeyGrid = document.getElementById('qbank-derived-answer-key-grid');
    const selectedQList = document.getElementById('selected-questions-list');
    const qbankEmptyState = document.getElementById('qbank-empty-state');
    const selectedCountBadge = document.getElementById('selected-questions-count-badge');
    const qbankKeyCount = document.getElementById('qbank-key-count');
    const jsonInput = document.getElementById('selected_questions_json');

    // Initial Data
    let currentMode = "{{ $initialMode }}";
    const initialAnswerKey = @json($previewAnswerKey);
    let answerKey = Object.fromEntries(Object.entries(initialAnswerKey || {}).map(([q, ans]) => [String(q), ans]));
    
    let selectedQuestions = [];
    try {
        const rawJson = jsonInput.value || '[]';
        selectedQuestions = typeof rawJson === 'string' ? (JSON.parse(rawJson) || []) : (rawJson || []);
    } catch (e) {
        selectedQuestions = [];
    }

    // Helper: Render LaTeX math in element
    const renderMath = (element) => {
        if (!element || !window.renderMathInElement) return;
        try {
            window.renderMathInElement(element, {
                delimiters: [
                    { left: '$$', right: '$$', display: true },
                    { left: '$', right: '$', display: false },
                    { left: '\\(', right: '\\)', display: false },
                    { left: '\\[', right: '\\]', display: true },
                ],
                throwOnError: false,
            });
        } catch (e) {
            console.warn('Math rendering notice:', e);
        }
    };

    // -------------------------------------------------------------
    // Mode Switcher Logic
    // -------------------------------------------------------------
    const switchMode = (mode) => {
        currentMode = mode;
        const cardPdf = document.getElementById('card-mode-pdf');
        const cardQBank = document.getElementById('card-mode-qbank');

        if (mode === 'pdf_upload') {
            cardPdf.classList.add('active');
            cardQBank.classList.remove('active');
            cardPdf.querySelector('input').checked = true;

            panePdf.style.display = 'block';
            paneQBank.style.display = 'none';

            rightPanelOmr.style.display = 'block';
            rightPanelQBank.style.display = 'none';

            btnSubmitPaper.style.display = 'none';

            renderOMRPreview();
        } else {
            cardQBank.classList.add('active');
            cardPdf.classList.remove('active');
            cardQBank.querySelector('input').checked = true;

            panePdf.style.display = 'none';
            paneQBank.style.display = 'block';

            rightPanelOmr.style.display = 'none';
            rightPanelQBank.style.display = 'block';

            btnSubmitPaper.style.display = 'inline-flex';

            renderQBankExam();
        }
    };

    document.querySelectorAll('.mode-option-card').forEach(card => {
        card.addEventListener('click', function () {
            const input = this.querySelector('input[type="radio"]');
            if (input) {
                switchMode(input.value);
            }
        });
    });

    // -------------------------------------------------------------
    // Mode 1: Answer Key & OMR Sheet Generator
    // -------------------------------------------------------------
    const renderAnswerKey = (count) => {
        let html = '';
        for (let q = 1; q <= count; q++) {
            const currentVal = answerKey[String(q)] || '';
            html += `
            <div class="ak-row">
                <span class="ak-qnum">Q${q}</span>
                <div class="ak-options">
                    ${['A', 'B', 'C', 'D'].map(opt => `
                        <label class="ak-option-label">
                            <input type="radio" name="answer_key[${q}]" value="${opt}" ${currentVal === opt ? 'checked' : ''}>
                            <span class="ak-option-pill">${opt}</span>
                        </label>
                    `).join('')}
                </div>
            </div>`;
        }
        answerKeyGrid.innerHTML = html;

        answerKeyGrid.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const qNum = this.name.match(/\d+/)[0];
                answerKey[qNum] = this.value;
                updateAnswerProgress(count);
            });
        });

        updateAnswerProgress(count);
    };

    const updateAnswerProgress = (count) => {
        const selected = answerKeyGrid.querySelectorAll('input:checked').length;
        const progressEl = document.getElementById('answer-key-progress');
        if (progressEl) {
            progressEl.textContent = `${selected} / ${count} Selected`;
            progressEl.className = selected === count ? 'header-badge bg-success text-white' : 'header-badge';
        }
    };

    // Quick fill buttons
    document.querySelectorAll('.ak-btn-group button').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.fill;
            const count = Math.max(1, Math.min(100, parseInt(countInput.value, 10) || 50));
            const options = ['A', 'B', 'C', 'D'];

            for (let q = 1; q <= count; q++) {
                if (action === 'CLEAR') {
                    delete answerKey[String(q)];
                } else if (action === 'RANDOM') {
                    answerKey[String(q)] = options[Math.floor(Math.random() * options.length)];
                } else {
                    answerKey[String(q)] = action;
                }
            }
            renderAnswerKey(count);
        });
    });

    // Quick chips
    document.querySelectorAll('.quick-chip-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.quick-chip-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            countInput.value = this.dataset.count;
            renderOMRPreview();
        });
    });

    // SVG OMR Sheet Render
    const renderOMRPreview = () => {
        const count = Math.max(1, Math.min(100, parseInt(countInput?.value || '50', 10) || 50));
        const labels = ['A', 'B', 'C', 'D'];
        let svg = '';

        for (let block = 0; block < 4; block++) {
            const blockStart = block * 25 + 1;
            if (blockStart > count) continue;
            const blockEnd = Math.min(count, blockStart + 24);
            const bx = 70 + block * 170;

            svg += `<text x="${bx + 77.5}" y="456" text-anchor="middle" font-family="Arial" font-size="8.5" font-weight="700" fill="#002C54">QUESTIONS ${blockStart} - ${blockEnd}</text>`;
            svg += `<rect x="${bx}" y="444" width="155" height="576" fill="none" stroke="#94a3b8" stroke-width="1"/>`;
            svg += `<text x="${bx + 14}" y="471" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700">Q</text>`;
            labels.forEach((label, idx) => {
                svg += `<text x="${bx + 48 + idx * 26}" y="471" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700">${label}</text>`;
            });

            for (let row = 1; row <= 25; row++) {
                const question = block * 25 + row;
                const qy = 486 + (row - 1) * 21.8;
                if (question <= count) {
                    svg += `<text x="${bx + 14}" y="${qy + 3}" text-anchor="middle" font-family="Arial" font-size="8" font-weight="700">${question}</text>`;
                    labels.forEach((label, idx) => {
                        svg += `<circle cx="${bx + 48 + idx * 26}" cy="${qy}" r="7" fill="white" stroke="#000000" stroke-width="1.15"/>`;
                    });
                }
                svg += `<line x1="${bx + 4}" y1="${qy + 9}" x2="${bx + 151}" y2="${qy + 9}" stroke="#f1f5f9" stroke-width="0.8"/>`;
            }
        }

        if (grid) grid.innerHTML = svg;
        const nameVal = (nameInput?.value?.trim() || 'Untitled Examination').toUpperCase();
        const codeVal = codeInput?.value || 'EXAM-CODE';

        const omrName = document.getElementById('omr-preview-name');
        const omrCode = document.getElementById('omr-preview-code');
        const omrTotal = document.getElementById('omr-question-total');
        const previewBadge = document.getElementById('exam-preview-count');
        const pdfBadge = document.getElementById('pdf-qcount-badge');

        if (omrName) omrName.textContent = `EXAM: ${nameVal}`;
        if (omrCode) omrCode.textContent = `EXAM CODE: ${codeVal}`;
        if (omrTotal) omrTotal.textContent = `ANSWERS — ${count} QUESTIONS`;
        if (previewBadge) previewBadge.textContent = `${count} Questions`;
        if (pdfBadge) pdfBadge.textContent = `${count} Questions`;

        renderAnswerKey(count);

        // Mirror SVG to Mode 2 OMR tab
        const mode2OmrPaper = document.getElementById('exam-omr-mode2-paper');
        const omrSvg = document.getElementById('exam-omr-svg');
        if (mode2OmrPaper && omrSvg) {
            mode2OmrPaper.innerHTML = omrSvg.outerHTML;
        }
    };

    nameInput?.addEventListener('input', () => {
        renderOMRPreview();
        updatePaperHeader();
    });
    countInput?.addEventListener('input', renderOMRPreview);

    // -------------------------------------------------------------
    // Mode 2: Question Bank Integration & Paper Preview
    // -------------------------------------------------------------
    const updatePaperHeader = () => {
        const nameVal = nameInput?.value?.trim() || 'Untitled Examination';
        const classText = classSelect?.options[classSelect.selectedIndex]?.text || '--';
        const subText = subjectSelect?.options[subjectSelect.selectedIndex]?.text || 'All Subjects';
        const dateVal = dateInput?.value || '';
        const marksCorrect = parseFloat(marksCorrectInput?.value || 1.0) || 1.0;
        const marksIncorrect = parseFloat(marksIncorrectInput?.value || 0.0) || 0.0;
        const qCount = selectedQuestions.length;

        const pTitle = document.getElementById('paper-preview-title');
        const pClass = document.getElementById('paper-preview-class');
        const pSub = document.getElementById('paper-preview-subject');
        const pDate = document.getElementById('paper-preview-date');
        const pTotalQ = document.getElementById('paper-preview-total-q');
        const pMaxMarks = document.getElementById('paper-preview-max-marks');
        const pCorr = document.getElementById('paper-correct-marks-label');
        const pIncorr = document.getElementById('paper-negative-marks-label');

        if (pTitle) pTitle.textContent = nameVal;
        if (pClass) pClass.textContent = classText;
        if (pSub) pSub.textContent = subText;
        if (pDate) pDate.textContent = dateVal ? new Date(dateVal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '';
        if (pTotalQ) pTotalQ.textContent = qCount;
        if (pMaxMarks) pMaxMarks.textContent = (qCount * marksCorrect).toFixed(1);
        if (pCorr) pCorr.textContent = marksCorrect;
        if (pIncorr) pIncorr.textContent = marksIncorrect;

        const qbankOmrLabel = document.getElementById('qbank-omr-count-label');
        if (qbankOmrLabel) qbankOmrLabel.textContent = qCount;
    };

    marksCorrectInput?.addEventListener('input', updatePaperHeader);
    marksIncorrectInput?.addEventListener('input', updatePaperHeader);
    dateInput?.addEventListener('change', updatePaperHeader);

    // Dynamic Class to Subject Loading
    classSelect?.addEventListener('change', function () {
        const classId = this.value;
        updatePaperHeader();
        if (!classId) return;

        fetch(`{{ url('subjectGetData') }}/${classId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Select Subject --</option>';
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        html += `<option value="${item.id}">${item.name}</option>`;
                    });
                }
                subjectSelect.innerHTML = html;
                const modalSub = document.getElementById('modal-filter-subject');
                if (modalSub) modalSub.innerHTML = html;
            })
            .catch(err => console.warn('Subject load error:', err));
    });

    subjectSelect?.addEventListener('change', updatePaperHeader);

    // Render Mode 2 Workspace & Question Paper
    const renderQBankExam = () => {
        const count = selectedQuestions.length;
        selectedCountBadge.textContent = `${count} Questions`;
        qbankKeyCount.textContent = count;
        jsonInput.value = JSON.stringify(selectedQuestions);

        if (countInput) {
            countInput.value = count > 0 ? count : (countInput.value || 50);
        }

        if (count === 0) {
            qbankEmptyState.style.display = 'block';
            selectedQList.innerHTML = '';
            document.getElementById('exam-paper-questions-flow').innerHTML = `
                <div class="text-center py-4 text-muted">
                    <em>Please add questions from Question Bank to generate paper preview...</em>
                </div>`;
            qbankAnswerKeyGrid.innerHTML = '<div class="p-2 text-muted small text-center">No questions added yet.</div>';
            updatePaperHeader();
            renderOMRPreview();
            return;
        }

        qbankEmptyState.style.display = 'none';

        // 1. Build Left Column Cards
        let cardsHtml = '';
        let paperHtml = '';
        let keyHtml = '';

        selectedQuestions.forEach((q, idx) => {
            const qNum = idx + 1;
            const isMcq = (parseInt(q.question_type_id, 10) === 1);
            const correctLetter = q.correct_letter || (isMcq ? (['A','B','C','D','E'][parseInt(q.correct_ans, 10)] || q.correct_ans || 'A') : (q.correct_ans || ''));

            // Auto-sync into answerKey map
            answerKey[String(qNum)] = correctLetter;

            // Card
            cardsHtml += `
            <div class="selected-q-card" data-index="${idx}">
                <div class="selected-q-header">
                    <div class="selected-q-badges">
                        <span class="q-badge-num">Q${qNum}</span>
                        <span class="q-badge-type">${isMcq ? 'MCQ (4 Options)' : 'Descriptive / Numerical'}</span>
                        <span class="q-badge-ans">✓ Correct: ${correctLetter}</span>
                    </div>
                    <div class="selected-q-actions">
                        <button type="button" class="btn-q-action btn-q-up" title="Move Up" ${idx === 0 ? 'disabled' : ''}><i class="fa fa-arrow-up"></i></button>
                        <button type="button" class="btn-q-action btn-q-down" title="Move Down" ${idx === count - 1 ? 'disabled' : ''}><i class="fa fa-arrow-down"></i></button>
                        <button type="button" class="btn-q-action btn-q-delete" title="Remove"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
                <div class="selected-q-text math-content">${q.name || ''}</div>
                ${q.image ? `<img src="${q.image}" class="paper-q-diagram mb-1" alt="Diagram">` : ''}
                ${isMcq ? `
                    <div class="selected-q-options">
                        <div class="opt-pill-preview ${correctLetter === 'A' ? 'is-correct' : ''}"><strong>(A)</strong> <span class="math-content">${q.ans1 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'B' ? 'is-correct' : ''}"><strong>(B)</strong> <span class="math-content">${q.ans2 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'C' ? 'is-correct' : ''}"><strong>(C)</strong> <span class="math-content">${q.ans3 || '--'}</span></div>
                        <div class="opt-pill-preview ${correctLetter === 'D' ? 'is-correct' : ''}"><strong>(D)</strong> <span class="math-content">${q.ans4 || '--'}</span></div>
                    </div>
                ` : `
                    <div class="p-1 bg-light border rounded small font-weight-bold text-success">
                        Answer / Numerical Solution: ${q.correct_ans || 'Descriptive'}
                    </div>
                `}
            </div>`;

            // Paper Flow Block
            paperHtml += `
            <div class="paper-question-block">
                <div class="paper-q-statement">
                    <span class="paper-q-num">Q${qNum}.</span>
                    <span class="paper-q-marks">[${parseFloat(marksCorrectInput?.value || 1.0)} Mark]</span>
                    <span class="math-content">${q.name || ''}</span>
                </div>
                ${q.image ? `<img src="${q.image}" class="paper-q-diagram" alt="Diagram">` : ''}
                ${isMcq ? `
                    <div class="paper-q-options-grid">
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(A)</span> <span class="math-content">${q.ans1 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(B)</span> <span class="math-content">${q.ans2 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(C)</span> <span class="math-content">${q.ans3 || ''}</span></div>
                        <div class="paper-q-opt"><span class="paper-q-opt-label">(D)</span> <span class="math-content">${q.ans4 || ''}</span></div>
                    </div>
                ` : `
                    <div style="height: 48px; border: 1px dashed #cbd5e1; border-radius: 2px; margin-top: 4px; background: #fafafa; display: flex; align-items: center; padding-left: 8px; color: #94a3b8; font-size: 10px;">
                        [ Write answer / numerical solution here ]
                    </div>
                `}
            </div>`;

            // Answer Key row
            keyHtml += `
            <div class="ak-row">
                <span class="ak-qnum">Q${qNum}</span>
                <span class="badge badge-success font-weight-bold" style="font-size: 10px;">${correctLetter}</span>
            </div>`;
        });

        selectedQList.innerHTML = cardsHtml;
        document.getElementById('exam-paper-questions-flow').innerHTML = paperHtml;
        qbankAnswerKeyGrid.innerHTML = keyHtml;

        // Render KaTeX formulas
        renderMath(selectedQList);
        renderMath(document.getElementById('exam-paper-questions-flow'));

        // Bind Question Card Actions (Delete, Move Up, Move Down)
        selectedQList.querySelectorAll('.btn-q-delete').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                selectedQuestions.splice(idx, 1);
                renderQBankExam();
            });
        });

        selectedQList.querySelectorAll('.btn-q-up').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                if (idx > 0) {
                    const temp = selectedQuestions[idx];
                    selectedQuestions[idx] = selectedQuestions[idx - 1];
                    selectedQuestions[idx - 1] = temp;
                    renderQBankExam();
                }
            });
        });

        selectedQList.querySelectorAll('.btn-q-down').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = this.closest('.selected-q-card');
                const idx = parseInt(card.dataset.index, 10);
                if (idx < selectedQuestions.length - 1) {
                    const temp = selectedQuestions[idx];
                    selectedQuestions[idx] = selectedQuestions[idx + 1];
                    selectedQuestions[idx + 1] = temp;
                    renderQBankExam();
                }
            });
        });

        updatePaperHeader();
        renderOMRPreview();
    };

    // -------------------------------------------------------------
    // Question Bank Picker Modal Logic
    // -------------------------------------------------------------
    const modal = $('#modal-question-bank');
    let modalFetchedQuestions = [];
    let modalSelectedMap = {};

    const openQuestionModal = () => {
        // Pre-fill filter with current selection
        const targetClass = classSelect.value;
        const targetSub = subjectSelect.value;

        if (targetClass) document.getElementById('modal-filter-class').value = targetClass;
        if (targetSub) document.getElementById('modal-filter-subject').value = targetSub;

        // Sync existing selected IDs into map
        modalSelectedMap = {};
        selectedQuestions.forEach(q => {
            modalSelectedMap[q.id] = true;
        });

        modal.modal('show');
        loadModalQuestions();
    };

    document.getElementById('btn-open-question-modal')?.addEventListener('click', openQuestionModal);
    document.getElementById('btn-open-question-modal-empty')?.addEventListener('click', openQuestionModal);

    const loadModalQuestions = () => {
        const classId = document.getElementById('modal-filter-class').value;
        const subId = document.getElementById('modal-filter-subject').value;
        const typeId = document.getElementById('modal-filter-type').value;
        const search = document.getElementById('modal-filter-search').value;

        const loading = document.getElementById('modal-loading-indicator');
        const list = document.getElementById('modal-questions-list');

        loading.style.display = 'block';
        list.innerHTML = '';

        const params = new URLSearchParams({
            class_type_id: classId,
            subject_id: subId,
            question_type_id: typeId,
            search: search,
        });

        fetch(`{{ url('exam/get-bank-questions') }}?${params.toString()}`)
            .then(res => res.json())
            .then(res => {
                loading.style.display = 'none';
                if (!res.questions || res.questions.length === 0) {
                    list.innerHTML = `
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-folder-open-o fa-2x mb-2"></i>
                        <p class="mb-0 font-weight-bold">No questions found matching the selected filters.</p>
                        <p class="small">Try selecting another class or subject, or clearing search keywords.</p>
                    </div>`;
                    modalFetchedQuestions = [];
                    updateModalBadge();
                    return;
                }

                modalFetchedQuestions = res.questions;
                let html = '';

                modalFetchedQuestions.forEach(q => {
                    const isChecked = modalSelectedMap[q.id] ? 'checked' : '';
                    const isSelectedClass = modalSelectedMap[q.id] ? 'selected' : '';
                    const isMcq = (parseInt(q.question_type_id, 10) === 1);

                    html += `
                    <div class="modal-q-item ${isSelectedClass}" data-qid="${q.id}">
                        <div class="d-flex align-items-start gap-2">
                            <input type="checkbox" class="modal-q-checkbox mt-1" data-qid="${q.id}" ${isChecked} style="transform: scale(1.15);">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="q-badge-type">${isMcq ? 'MCQ (4 Options)' : 'Descriptive / Numerical'}</span>
                                    <span class="badge badge-secondary" style="font-size: 9px;">ID: #${q.id}</span>
                                    <span class="q-badge-ans">Correct: ${q.correct_letter}</span>
                                </div>
                                <div class="selected-q-text math-content">${q.name || ''}</div>
                                ${q.image ? `<img src="${q.image}" class="paper-q-diagram mb-1" alt="Diagram">` : ''}
                                ${isMcq ? `
                                    <div class="selected-q-options">
                                        <div class="opt-pill-preview ${q.correct_letter === 'A' ? 'is-correct' : ''}"><strong>(A)</strong> <span class="math-content">${q.ans1 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'B' ? 'is-correct' : ''}"><strong>(B)</strong> <span class="math-content">${q.ans2 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'C' ? 'is-correct' : ''}"><strong>(C)</strong> <span class="math-content">${q.ans3 || '--'}</span></div>
                                        <div class="opt-pill-preview ${q.correct_letter === 'D' ? 'is-correct' : ''}"><strong>(D)</strong> <span class="math-content">${q.ans4 || '--'}</span></div>
                                    </div>
                                ` : `
                                    <div class="small text-muted font-italic">Numerical / Descriptive Answer: <strong class="text-success">${q.correct_ans || ''}</strong></div>
                                `}
                            </div>
                        </div>
                    </div>`;
                });

                list.innerHTML = html;
                renderMath(list);
                bindModalCheckboxes();
                updateModalBadge();
            })
            .catch(err => {
                loading.style.display = 'none';
                list.innerHTML = `<div class="alert alert-danger py-2 px-3 small">Failed to load questions: ${err.message}</div>`;
            });
    };

    const bindModalCheckboxes = () => {
        document.querySelectorAll('.modal-q-item').forEach(item => {
            const cb = item.querySelector('.modal-q-checkbox');
            item.addEventListener('click', function (e) {
                if (e.target !== cb) {
                    cb.checked = !cb.checked;
                }
                const qid = cb.dataset.qid;
                if (cb.checked) {
                    modalSelectedMap[qid] = true;
                    item.classList.add('selected');
                } else {
                    delete modalSelectedMap[qid];
                    item.classList.remove('selected');
                }
                updateModalBadge();
            });
        });
    };

    const updateModalBadge = () => {
        const count = Object.keys(modalSelectedMap).length;
        document.getElementById('modal-selected-badge').textContent = `${count} Selected`;
        document.getElementById('modal-apply-count').textContent = count;
    };

    document.getElementById('modal-btn-refresh')?.addEventListener('click', loadModalQuestions);
    document.getElementById('modal-filter-class')?.addEventListener('change', loadModalQuestions);
    document.getElementById('modal-filter-subject')?.addEventListener('change', loadModalQuestions);
    document.getElementById('modal-filter-type')?.addEventListener('change', loadModalQuestions);

    document.getElementById('modal-select-all-visible')?.addEventListener('change', function () {
        const isChecked = this.checked;
        document.querySelectorAll('.modal-q-checkbox').forEach(cb => {
            cb.checked = isChecked;
            const qid = cb.dataset.qid;
            const parent = cb.closest('.modal-q-item');
            if (isChecked) {
                modalSelectedMap[qid] = true;
                parent.classList.add('selected');
            } else {
                delete modalSelectedMap[qid];
                parent.classList.remove('selected');
            }
        });
        updateModalBadge();
    });

    // Apply selection from modal
    document.getElementById('modal-btn-apply-selection')?.addEventListener('click', function () {
        // Build new selected questions list preserving order and appending newly selected
        const existingMap = {};
        selectedQuestions.forEach(q => existingMap[q.id] = q);

        const newSelection = [];

        // 1. Keep already selected if still checked
        selectedQuestions.forEach(q => {
            if (modalSelectedMap[q.id]) {
                newSelection.push(q);
            }
        });

        // 2. Add newly checked questions from fetched modal list
        modalFetchedQuestions.forEach(q => {
            if (modalSelectedMap[q.id] && !newSelection.some(x => x.id == q.id)) {
                newSelection.push(q);
            }
        });

        selectedQuestions = newSelection;
        renderQBankExam();
        modal.modal('hide');
    });

    // -------------------------------------------------------------
    // Print Handlers
    // -------------------------------------------------------------
    const printOmr = () => {
        document.body.classList.add('printing-omr');
        document.body.classList.remove('printing-paper');
        window.print();
        setTimeout(() => document.body.classList.remove('printing-omr'), 500);
    };

    const printPaper = () => {
        document.body.classList.add('printing-paper');
        document.body.classList.remove('printing-omr');
        window.print();
        setTimeout(() => document.body.classList.remove('printing-paper'), 500);
    };

    document.querySelectorAll('.btn-trigger-print-omr').forEach(btn => btn.addEventListener('click', printOmr));

    // Dual tab label update
    document.getElementById('tab-paper-btn')?.addEventListener('click', () => {
        document.getElementById('print-active-label').textContent = 'Print Question Paper';
    });
    document.getElementById('tab-omr-btn')?.addEventListener('click', () => {
        document.getElementById('print-active-label').textContent = 'Print OMR Sheet';
    });

    document.getElementById('btn-print-active-tab')?.addEventListener('click', function () {
        const isPaperTab = document.getElementById('tab-paper-btn').classList.contains('active');
        if (isPaperTab) {
            printPaper();
        } else {
            printOmr();
        }
    });

    // -------------------------------------------------------------
    // Form Submission Actions
    // -------------------------------------------------------------
    const actionInput = document.getElementById('form-action-type');

    document.getElementById('btn-submit-save')?.addEventListener('click', () => {
        actionInput.value = 'save';
        form.submit();
    });

    document.getElementById('btn-submit-print-omr')?.addEventListener('click', () => {
        actionInput.value = 'print_omr';
        form.submit();
    });

    document.getElementById('btn-submit-print-paper')?.addEventListener('click', () => {
        actionInput.value = 'print_paper';
        form.submit();
    });

    document.getElementById('btn-submit-marks')?.addEventListener('click', () => {
        actionInput.value = 'fill_marks';
        form.submit();
    });

    // -------------------------------------------------------------
    // Initial Render Sequence
    // -------------------------------------------------------------
    switchMode(currentMode);
    renderOMRPreview();
    if (selectedQuestions.length > 0) {
        renderQBankExam();
    }

    // Auto-trigger print if requested via query string
    @if(request()->boolean('print_omr') || request()->boolean('print'))
        window.setTimeout(printOmr, 450);
    @elseif(request()->boolean('print_paper'))
        window.setTimeout(printPaper, 450);
    @endif
});
</script>
@endsection

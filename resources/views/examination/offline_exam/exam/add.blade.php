@extends('layout.app')

@section('content')
@php
  $formExam = $savedExam ?? null;
  $previewName = old('name', $formExam->name ?? 'Sample Examination');
  $previewCode = old('code', $formExam->code ?? $formExam->exam_code ?? ($examCode ?? ('EXAM-' . strtoupper(\Illuminate\Support\Str::random(6)))));
  $previewQuestions = (int) old('number_of_questions', $formExam->number_of_questions ?? 50);
  $previewInstituteName = $instituteName ?? (Helper::getSetting()->name ?? 'ARISE ERP INSTITUTE');
  $previewAnswerKey = old('answer_key', $formExam->answer_key ?? []);
  $onlineQuestionsJson = old('online_questions_json', !empty($formExam->online_questions) ? json_encode($formExam->online_questions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '{}');
  $onlineEnabled = (bool) old('is_online', $formExam->is_online ?? false);
  $editingExam = $formExam;
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
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.18);
    margin-bottom: 8px;
}
.perf-hero-text {
    display: flex;
    flex-direction: column;
}
.perf-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .9;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.perf-title {
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.perf-subtitle {
    font-size: 10.5px;
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

/* Workflow Hero Navigation */
.hidden {
    display: none !important;
}
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

/* 2. Theme Card Standards */
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

/* CBT Mode Switcher Box */
.cbt-mode-card {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-left: 3px solid #002C54 !important;
    border-radius: 2px !important;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.04);
}
.cbt-mode-card .cbt-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54 !important;
    margin: 0;
    line-height: 1.3;
}
.cbt-mode-card .cbt-desc {
    font-size: 10px;
    color: #64748b !important;
    display: block;
    margin-top: 1px;
}
.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #002C54 !important;
    border-color: #002C54 !important;
}

/* Math Quick Insert Ribbon */
.math-ribbon-card {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    padding: 8px 10px;
    margin-bottom: 8px;
}
.math-ribbon-card .ribbon-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54 !important;
}
.math-symbol-btn {
    padding: 2px 7px;
    font-size: 10.5px;
    font-weight: 700;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.12s ease;
}
.math-symbol-btn:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Live Question Preview Box */
.preview-box-card {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    padding: 8px 10px;
    margin-bottom: 12px;
}
.preview-box-card .preview-header-title {
    font-size: 11px;
    font-weight: 700;
    color: #002C54 !important;
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
    max-height: 290px;
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
    #exam-omr-print-area, #exam-omr-print-area * {
        visibility: visible !important;
    }
    #exam-omr-print-area {
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
                    <i class="fa fa-pencil-square-o"></i> Create Exam & OMR Sheet Builder
                </h1>
                <p class="perf-subtitle">
                    <span>Session: {{ Session::get('session_name') ?? date('Y') }}</span> &bull;
                    <span>Configure assessment parameters, 4-option printable OMR sheet, answer keys and CBT math questions</span>
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
        @if(session('error') || $errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') ?? $errors->first() }}
                <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- 3. Two-Column Layout: Left Form + Right OMR Preview --}}
        <div class="row">
            {{-- Left Column: Exam Details & Answer Key --}}
            <div class="col-lg-5 col-xl-5">
                <form id="exam-create-form" action="{{ url('add/exam') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="action_type" id="form-action-type" value="save">

                    {{-- Card 1: Exam Settings --}}
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
                                                    <option value="{{ $type->id }}" {{ (old('class_type_id', $formExam->class_type_id ?? '') == $type->id) ? 'selected' : '' }}>
                                                        {{ $type->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('class_type_id')<span class="text-danger small">{{ $message }}</span>@enderror
                                    </div>
                                </div>

                                {{-- Examination Date --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="exam-date">Date of Exam <span class="text-danger">*</span></label>
                                        <input type="date" id="exam-date" name="exam_date" class="theme-form-control" value="{{ old('exam_date', (!empty($formExam) && !empty($formExam->exam_date)) ? (is_object($formExam->exam_date) ? $formExam->exam_date->format('Y-m-d') : $formExam->exam_date) : date('Y-m-d')) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Exam Code --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="exam-code">Exam Code</label>
                                        <input type="text" id="exam-code" name="code" class="theme-form-control font-weight-bold" value="{{ $previewCode }}" readonly>
                                        <div class="theme-hint"><i class="fa fa-lock"></i> Auto-generated scanner code</div>
                                    </div>
                                </div>

                                {{-- Number of Questions --}}
                                <div class="col-md-6">
                                    <div class="theme-form-group">
                                        <label for="exam-question-count">Total Questions <span class="text-danger">*</span></label>
                                        <input type="number" id="exam-question-count" name="number_of_questions" class="theme-form-control" value="{{ $previewQuestions }}" min="1" max="100" required>
                                    </div>
                                </div>
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

                            {{-- Question Paper PDF --}}
                            <div class="theme-form-group">
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

                            {{-- Online CBT Switch --}}
                            <div class="cbt-mode-card">
                                <div>
                                    <div class="cbt-title">
                                        <i class="fa fa-laptop mr-1 text-primary"></i> Convert Exam to Online CBT Mode
                                    </div>
                                    <span class="cbt-desc">Enable rich-text &amp; Math/LaTeX formula editor</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="exam-online-toggle" name="is_online" value="1" {{ $onlineEnabled ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="exam-online-toggle" style="cursor: pointer;"></label>
                                </div>
                            </div>
                            <input type="hidden" id="exam-online-questions-json" name="online_questions_json" value="{{ $onlineQuestionsJson }}">

                            {{-- Description / Notes --}}
                            <div class="theme-form-group mb-0">
                                <label for="description">Instructions / Remarks</label>
                                <textarea id="description" name="description" rows="2" class="theme-form-control" style="height: auto;" placeholder="Optional examination notes or instructions...">{{ old('description', $formExam->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Interactive Answer Key Matrix --}}
                    <div class="theme-card">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-key text-warning"></i> Answer Key (Correct Options)
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
                                    {{-- Dynamically rendered by JavaScript --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submission Actions --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button type="button" id="btn-submit-save" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; flex:1; justify-content:center; height:32px;">
                            <i class="fa fa-save"></i> Save Exam
                        </button>
                        <button type="button" id="btn-submit-print" class="dash-btn dash-btn-light" style="border: 1px solid #cbd5e1; height:32px;">
                            <i class="fa fa-print"></i> Save & Print OMR
                        </button>
                        <button type="button" id="btn-submit-marks" class="dash-btn dash-btn-emerald" style="height:32px;">
                            <i class="fa fa-pencil"></i> Save & Fill Marks
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Column: Live Printable OMR Sheet Preview & Online Question Builder --}}
            <div class="col-lg-7 col-xl-7">
                {{-- OMR Sheet Preview Card --}}
                <div class="theme-card {{ $onlineEnabled ? 'd-none' : '' }}" id="exam-omr-preview-panel" style="{{ $onlineEnabled ? 'display: none !important;' : '' }}">
                    <div class="theme-card-header">
                        <h3 class="card-title">
                            <i class="fa fa-eye text-warning"></i> Live Printable OMR Sheet Preview
                        </h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-badge" id="exam-preview-count">{{ $previewQuestions }} Questions</span>
                            <button type="button" id="exam-print-button" class="dash-btn dash-btn-light py-0 px-2" style="height: 22px; font-size: 10px;">
                                <i class="fa fa-print"></i> Print Sheet
                            </button>
                        </div>
                    </div>

                    <div class="theme-card-body p-2 omr-sheet-card">
                        <div class="omr-sheet-paper" id="exam-omr-print-area">
                            <svg id="exam-omr-svg" viewBox="0 0 800 1100" xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" class="d-block w-100 h-auto bg-white" aria-label="Printable exam OMR sheet">
                                <rect width="800" height="1100" fill="white"/>
                                <rect x="24" y="24" width="752" height="1052" fill="none" stroke="#111827" stroke-width="3"/>
                                
                                {{-- 4 High-contrast OMR Scanner Fiducial Corner Squares --}}
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
                                
                                {{-- Question Columns (Populated via JS) --}}
                                <g id="omr-question-grid"></g>

                                {{-- Instructions Footer --}}
                                <text x="400" y="1042" text-anchor="middle" font-family="Arial" font-size="10" font-weight="700" fill="#0f172a">INSTRUCTIONS: Use BLUE or BLACK Ball Point Pen | Fill only one circle per question | Do not fold or tear</text>
                                <text x="400" y="1060" text-anchor="middle" font-family="Arial" font-size="9.5" font-weight="600" fill="#475569">Keep sheet flat and clean | Ensure all 4 black corner squares remain clean &amp; visible for OMR Optical Scanner</text>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Online Question Builder Panel (Shown when Online CBT switch is active) --}}
                <div class="theme-card {{ $onlineEnabled ? '' : 'd-none' }}" id="exam-online-builder-panel" style="{{ $onlineEnabled ? '' : 'display: none !important;' }}">
                    <div class="theme-card-header">
                        <h3 class="card-title">
                            <i class="fa fa-superscript text-warning"></i> Online Question Editor (Math &amp; LaTeX)
                        </h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-badge" id="online-builder-progress">0 / {{ $previewQuestions }} questions</span>
                            <span class="header-badge bg-primary text-white" id="online-builder-position">Question 1 of {{ $previewQuestions }}</span>
                        </div>
                    </div>
                    <div class="theme-card-body">
                        {{-- Math Quick Insert Ribbon --}}
                        <div class="math-ribbon-card">
                            <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                <span class="ribbon-title">
                                    <i class="fa fa-calculator mr-1 text-primary"></i> Quick Math &amp; Science Symbols:
                                </span>
                                <span class="text-muted small font-italic" style="font-size: 10px;">LaTeX Supported: $x^2$, \sqrt{x}, \frac{a}{b}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1" id="math-quick-insert-ribbon">
                                <button type="button" data-insert-math="\frac{a}{b}" class="math-symbol-btn" title="Fraction">½ \frac{a}{b}</button>
                                <button type="button" data-insert-math="\sqrt{x}" class="math-symbol-btn" title="Square Root">√x \sqrt{x}</button>
                                <button type="button" data-insert-math="x^{2}" class="math-symbol-btn" title="Power">x²</button>
                                <button type="button" data-insert-math="x_{1}" class="math-symbol-btn" title="Subscript">x₁</button>
                                <button type="button" data-insert-math="\pm" class="math-symbol-btn" title="Plus Minus">±</button>
                                <button type="button" data-insert-math="\times" class="math-symbol-btn">×</button>
                                <button type="button" data-insert-math="\div" class="math-symbol-btn">÷</button>
                                <button type="button" data-insert-math="\pi" class="math-symbol-btn">π</button>
                                <button type="button" data-insert-math="\theta" class="math-symbol-btn">θ</button>
                                <button type="button" data-insert-math="\Delta" class="math-symbol-btn">Δ</button>
                                <button type="button" data-insert-math="\infty" class="math-symbol-btn">∞</button>
                                <button type="button" data-insert-math="\le" class="math-symbol-btn">≤</button>
                                <button type="button" data-insert-math="\ge" class="math-symbol-btn">≥</button>
                                <button type="button" data-insert-math="\ne" class="math-symbol-btn">≠</button>
                                <button type="button" data-insert-math="\rightarrow" class="math-symbol-btn">→</button>
                            </div>
                        </div>

                        {{-- Question Statement --}}
                        <div class="theme-form-group">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="mb-0">Question Statement <span class="badge badge-primary" id="online-question-number">Q1</span></label>
                                <span class="small font-weight-bold text-muted" id="online-question-saved-state">Not saved</span>
                            </div>
                            <textarea id="online-question-editor-textarea" class="theme-form-control" rows="3" placeholder="Type question statement or equation here..."></textarea>
                        </div>

                        {{-- Options A, B, C, D --}}
                        <div class="row">
                            @foreach(['A','B','C','D'] as $opt)
                                <div class="col-md-6 mb-2">
                                    <label class="small font-weight-bold mb-1">Option {{ $opt }}</label>
                                    <textarea id="online-option-{{ strtolower($opt) }}-textarea" class="theme-form-control" rows="2" placeholder="Option {{ $opt }}..."></textarea>
                                </div>
                            @endforeach
                        </div>

                        {{-- Real-Time Math & Question Preview --}}
                        <div class="preview-box-card">
                            <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                <span class="preview-header-title"><i class="fa fa-desktop mr-1 text-primary"></i> Student View Preview:</span>
                                <span class="badge badge-success d-none" id="preview-correct-badge">Correct: Option A</span>
                            </div>
                            <div id="live-question-preview-content" class="mt-2">
                                <div id="preview-question-text" class="p-2 bg-white border rounded mb-2 font-weight-bold" style="font-size: 12px; min-height: 30px; border-color: #cbd5e1 !important; color: #0f172a;">
                                    <em>Question statement preview will appear here...</em>
                                </div>
                                <div class="row" style="font-size: 11.5px;">
                                    <div class="col-6 mb-1"><div id="preview-option-a" class="p-1 border rounded bg-white" style="border-color: #cbd5e1 !important; color: #0f172a;"><strong>(A)</strong> <span class="opt-text text-muted">Empty</span></div></div>
                                    <div class="col-6 mb-1"><div id="preview-option-b" class="p-1 border rounded bg-white" style="border-color: #cbd5e1 !important; color: #0f172a;"><strong>(B)</strong> <span class="opt-text text-muted">Empty</span></div></div>
                                    <div class="col-6 mb-1"><div id="preview-option-c" class="p-1 border rounded bg-white" style="border-color: #cbd5e1 !important; color: #0f172a;"><strong>(C)</strong> <span class="opt-text text-muted">Empty</span></div></div>
                                    <div class="col-6 mb-1"><div id="preview-option-d" class="p-1 border rounded bg-white" style="border-color: #cbd5e1 !important; color: #0f172a;"><strong>(D)</strong> <span class="opt-text text-muted">Empty</span></div></div>
                                </div>
                            </div>
                        </div>

                        {{-- Navigator & Correct Answer --}}
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 border-top">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small font-weight-bold mb-0 mr-1">Correct Answer:</label>
                                <select id="online-correct-answer" class="theme-form-control font-weight-bold" style="width: 120px;">
                                    <option value="">-- Select --</option>
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" id="online-question-back" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Previous
                                </button>
                                <button type="button" id="online-question-save-next" class="btn btn-sm btn-primary">
                                    <i class="fa fa-save"></i> <span data-next-label>Save &amp; Next</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- KaTeX & MathJax CDN for dynamic formula rendering --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
<script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>
<script src="{{ asset('js/exam-online-question-builder.js') }}?v={{ time() }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('exam-name');
    const codeInput = document.getElementById('exam-code');
    const countInput = document.getElementById('exam-question-count');
    const grid = document.getElementById('omr-question-grid');
    const answerKeyGrid = document.getElementById('exam-answer-key-grid');
    const onlineToggle = document.getElementById('exam-online-toggle');
    const onlinePanel = document.getElementById('exam-online-builder-panel');
    const omrPanel = document.getElementById('exam-omr-preview-panel');

    const initialAnswerKey = @json($previewAnswerKey);
    const answerKey = Object.fromEntries(Object.entries(initialAnswerKey || {}).map(([q, ans]) => [String(q), ans]));

    // Online toggle handler - sync OMR preview & Online Question Builder
    const syncCbtPanels = (isOnline) => {
        if (isOnline) {
            if (omrPanel) {
                omrPanel.classList.add('d-none');
                omrPanel.style.display = 'none';
            }
            if (onlinePanel) {
                onlinePanel.classList.remove('d-none');
                onlinePanel.style.display = 'block';
            }
            const heroPrint = document.getElementById('btn-print-omr-hero');
            if (heroPrint) heroPrint.classList.add('d-none');
        } else {
            if (omrPanel) {
                omrPanel.classList.remove('d-none');
                omrPanel.style.display = 'block';
            }
            if (onlinePanel) {
                onlinePanel.classList.add('d-none');
                onlinePanel.style.display = 'none';
            }
            const heroPrint = document.getElementById('btn-print-omr-hero');
            if (heroPrint) heroPrint.classList.remove('d-none');
        }
    };

    if (onlineToggle) {
        onlineToggle.addEventListener('change', function () {
            syncCbtPanels(this.checked);
        });
        syncCbtPanels(onlineToggle.checked);
    }

    // Quick chips
    document.querySelectorAll('.quick-chip-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.quick-chip-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            countInput.value = this.dataset.count;
            renderPreview();
        });
    });

    // Answer Key rendering
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

        // Bind radio change listeners
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
            if (selected === count) {
                progressEl.className = 'header-badge bg-success text-white';
            } else {
                progressEl.className = 'header-badge';
            }
        }
    };

    // Quick fill answer key buttons
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

    // Render OMR SVG & Preview
    const renderPreview = () => {
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
        const codeBadge = document.getElementById('exam-code-badge');

        if (omrName) omrName.textContent = `EXAM: ${nameVal}`;
        if (omrCode) omrCode.textContent = `EXAM CODE: ${codeVal}`;
        if (omrTotal) omrTotal.textContent = `ANSWERS — ${count} QUESTIONS`;
        if (previewBadge) previewBadge.textContent = `${count} Questions`;
        if (codeBadge) codeBadge.textContent = codeVal;

        renderAnswerKey(count);
    };

    nameInput?.addEventListener('input', renderPreview);
    countInput?.addEventListener('input', renderPreview);

    // Initial render
    renderPreview();

    // Print button triggers
    document.getElementById('exam-print-button')?.addEventListener('click', () => window.print());
    document.getElementById('btn-print-omr-hero')?.addEventListener('click', () => window.print());

    // Submit actions
    const form = document.getElementById('exam-create-form');
    const actionInput = document.getElementById('form-action-type');

    document.getElementById('btn-submit-save')?.addEventListener('click', function () {
        actionInput.value = 'save';
        form.submit();
    });

    document.getElementById('btn-submit-print')?.addEventListener('click', function () {
        actionInput.value = 'print_omr';
        form.submit();
    });

    document.getElementById('btn-submit-marks')?.addEventListener('click', function () {
        actionInput.value = 'fill_marks';
        form.submit();
    });

    @if(request()->boolean('print'))
        window.setTimeout(() => window.print(), 350);
    @endif
});
</script>
@endsection

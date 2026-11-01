@extends('layout.app')

@section('content')
@php
    $classType = $classType ?? Helper::classType();
    $allSubjects = $allSubjects ?? Helper::getSubject();
    $hasAiKey = !empty($geminiApiKey);
@endphp

<style>
/* ==========================================================================
   ARISE ERP SIGNATURE THEME: QUESTION CREATION & AI INGESTION ENGINE
   ========================================================================== */
.qb-page-wrapper {
    padding: 10px 14px 30px 14px;
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
    opacity: .95;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.perf-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.25;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.perf-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
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
    gap: 6px;
    padding: 5px 12px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 30px;
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

/* 2. Theme Card Standards */
.theme-card {
    background: #ffffff;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 12px;
    overflow: hidden;
}
.theme-card-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}
.theme-card-header .card-title {
    font-size: 12.5px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin: 0 !important;
    color: #ffffff !important;
    line-height: 1.2 !important;
}
.theme-card-body {
    padding: 12px;
}

/* 3. Form Inputs & Groups */
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
    height: 30px;
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
textarea.theme-form-control {
    height: auto !important;
}
.theme-hint {
    font-size: 10px;
    color: #64748b;
    margin-top: 2px;
}

/* 4. Mode Selection Tabs */
.mode-nav-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #002C54;
    margin-bottom: 12px;
    background: #ffffff;
    padding: 4px 6px 0 6px;
    border-radius: 2px 2px 0 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.mode-nav-tab {
    padding: 7px 16px;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-bottom: none;
    border-radius: 2px 2px 0 0;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.mode-nav-tab:hover {
    background: #e2e8f0;
    color: #002C54;
}
.mode-nav-tab.active {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
    box-shadow: 0 -2px 4px rgba(0, 44, 84, 0.12);
}
.mode-nav-tab .tab-badge {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.2);
    font-weight: 700;
}
.mode-nav-tab.active .tab-badge {
    background: #38bdf8;
    color: #002C54;
}

/* 5. Math Quick Insert Ribbon */
.math-ribbon-card {
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.math-ribbon-card .ribbon-title {
    font-size: 10px;
    font-weight: 700;
    color: #002C54 !important;
}
.math-symbol-btn {
    padding: 2px 6px;
    font-size: 10px;
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

/* 6. Option Cards (A, B, C, D) */
.option-input-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px;
    margin-bottom: 8px;
    transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
}
.option-input-card.correct-selected {
    border-color: #059669 !important;
    background: #f0fdf4 !important;
    box-shadow: 0 0 0 1px #059669;
}
.option-label-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: #002C54;
    color: #ffffff;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    margin-right: 8px !important;
    flex-shrink: 0;
}
.gap-1 { gap: 6px !important; }
.gap-2 { gap: 10px !important; }

/* CKEditor Arise ERP Theme Styling */
.cke {
    border-radius: 3px !important;
    border: 1px solid #cbd5e1 !important;
    box-shadow: none !important;
    transition: border-color .15s ease;
}
.cke:focus-within {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 1px rgba(0, 44, 84, 0.15) !important;
}
.cke_inner {
    border-radius: 3px !important;
}
.cke_top {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 3px 6px !important;
}
.cke_bottom {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 1px 6px !important;
    min-height: 14px !important;
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
}
.cke_resizer {
    cursor: se-resize !important;
    display: block !important;
    border-width: 0 0 10px 10px !important;
    border-style: solid !important;
    border-color: transparent transparent #002C54 transparent !important;
    opacity: 0.65 !important;
    transition: opacity .15s ease, border-color .15s ease;
    margin: 2px 0 !important;
}
.cke_resizer:hover {
    opacity: 1 !important;
    border-color: transparent transparent #0f3460 transparent !important;
}
.editor-height-tools .btn {
    border-radius: 2px !important;
    font-size: 10px !important;
    line-height: 1.1 !important;
    padding: 1px 5px !important;
    font-weight: 600;
}
.cke_toolgroup {
    border: 1px solid #cbd5e1 !important;
    background: #ffffff !important;
    border-radius: 2px !important;
    margin: 1px 3px 1px 0 !important;
}
.cke_button:hover {
    background: #e2e8f0 !important;
    border-radius: 2px !important;
}
.correct-radio-label {
    margin: 0;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: transparent !important;
    color: #059669 !important;
}
.correct-radio-label input[type="radio"],
.bulk-field-correct {
    margin: 0 !important;
    cursor: pointer;
    accent-color: #059669;
    width: 14px;
    height: 14px;
    vertical-align: middle;
}

/* 7. Dropzone / Upload area for AI */
.ai-dropzone-box {
    border: 2px dashed #94a3b8;
    background: #f8fafc;
    border-radius: 2px;
    padding: 20px 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s ease;
    position: relative;
}
.ai-dropzone-box:hover, .ai-dropzone-box.drag-over {
    border-color: #002C54;
    background: #f1f5f9;
}
.ai-dropzone-icon {
    font-size: 32px;
    color: #002C54;
    margin-bottom: 6px;
    display: block;
}
.ai-pulse-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* 8. AI Extracted Question List / Table in Bulk Mode (Split Studio Layout) */
.bulk-q-item {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-top: 3px solid #002C54;
    border-radius: 2px;
    padding: 12px 14px;
    margin-bottom: 14px;
    transition: all 0.15s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.bulk-q-item:hover {
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
}
.bulk-q-item.excluded {
    opacity: 0.55;
    border-top-color: #94a3b8;
    background: #f8fafc;
}

/* Exam Paper Real-Time Preview Card */
.preview-exam-card {
    background: #f8fafc;
    border: 1px dashed #94a3b8;
    border-radius: 2px;
    padding: 10px 12px;
    height: 100%;
}
.preview-q-name {
    font-size: 12px;
    line-height: 1.6;
    color: #0f172a;
    min-height: 38px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 10px;
    margin-bottom: 8px;
}
.preview-opt-item {
    padding: 6px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    min-height: 32px;
    font-size: 11px;
    line-height: 1.4;
    transition: all .15s ease;
}
.preview-opt-item.preview-correct-opt {
    border-color: #059669;
    background: #059669;
    color: #ffffff;
    font-weight: 700;
}
.diagram-attach-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 2px;
    padding: 6px 8px;
    transition: all .15s ease;
}
.diagram-attach-box:hover, .diagram-attach-box.drag-over {
    border-color: #002C54;
    background: #f1f5f9;
}

/* 9. Universal Icon Spacing Standards */
.fa {
    line-height: inherit;
}
.perf-hero .fa:not(:last-child),
.theme-card-header .fa:not(:last-child),
.card-title .fa:not(:last-child),
.modal-title .fa:not(:last-child),
.ribbon-title .fa:not(:last-child),
.dash-btn .fa:not(:last-child),
.mode-nav-tab .fa:not(:last-child),
.alert .fa:not(:last-child) {
    margin-right: 6px;
}
.badge .fa {
    margin-right: 3px;
}
.fa-external-link {
    margin-left: 4px;
}
</style>

<div class="content-wrapper">
    <div class="qb-page-wrapper" id="question-builder-app">

        {{-- 1. Hero Header Banner --}}
        <div class="perf-hero">
            <div class="perf-hero-text">
                <span class="perf-kicker">
                    <i class="fa fa-graduation-cap mr-1"></i> Examination &amp; Question Bank System
                </span>
                <h1 class="perf-title">
                    <i class="fa fa-plus-circle mr-2"></i> Create &amp; AI Ingestion Engine
                </h1>
                <p class="perf-subtitle">
                    <span>Session: {{ Session::get('session_name') ?? date('Y') }}</span> &bull;
                    <span>Manual entry with LaTeX math &bull; Single image OCR &bull; Bulk PDF question paper parser via Google Gemini AI</span>
                </p>
            </div>
            <div class="perf-hero-actions">
                <a href="{{ url('view/question') }}" class="dash-btn dash-btn-outline" title="View all questions in Question Bank">
                    <i class="fa fa-list mr-1"></i> Question Bank
                </a>
                <button type="button" class="dash-btn {{ $hasAiKey ? 'dash-btn-light' : 'dash-btn-emerald' }}" data-toggle="modal" data-target="#aiSettingsModal" title="Configure Google Gemini API Key">
                    <i class="fa fa-magic mr-1"></i> AI Settings
                    @if($hasAiKey)
                        <span class="badge badge-success ml-1" style="font-size: 9px;"><i class="fa fa-check mr-1"></i> Connected</span>
                    @else
                        <span class="badge badge-warning ml-1" style="font-size: 9px;">Configure Key</span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Flash Alerts --}}
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

        {{-- 2. Global Target Context Card (Class, Subject, Type) --}}
        <div class="theme-card mb-2">
            <div class="theme-card-header">
                <h3 class="card-title">
                    <i class="fa fa-sliders text-warning mr-2"></i> Target Academic Context (Class &amp; Subject)
                </h3>
                <span class="text-white small" style="font-size: 10px; opacity: 0.9;">
                    <i class="fa fa-info-circle mr-1"></i> Used for Manual, Image Scanner, and Bulk PDF Import
                </span>
            </div>
            <div class="theme-card-body py-2">
                <div class="row">
                    {{-- Target Class --}}
                    <div class="col-md-4">
                        <div class="theme-form-group mb-1">
                            <label for="global_class_type_id">Target Class <span class="text-danger">*</span></label>
                            <select id="global_class_type_id" class="theme-form-control select2" required>
                                <option value="">-- Select Class --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}" {{ old('class_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Target Subject (Dynamically filtered by Class) --}}
                    <div class="col-md-4">
                        <div class="theme-form-group mb-1">
                            <label for="global_subject_id">Subject <span class="text-danger">*</span></label>
                            <select id="global_subject_id" class="theme-form-control select2" required>
                                <option value="">-- Select Subject --</option>
                                @if(!empty($allSubjects))
                                    @foreach($allSubjects as $subj)
                                        <option value="{{ $subj->id }}" data-class="{{ $subj->class_type_id ?? '' }}" {{ old('subject_id') == $subj->id ? 'selected' : '' }}>
                                            {{ $subj->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    {{-- Question Type --}}
                    <div class="col-md-4">
                        <div class="theme-form-group mb-1">
                            <label for="global_question_type_id">Question Format <span class="text-danger">*</span></label>
                            <select id="global_question_type_id" class="theme-form-control">
                                <option value="1" {{ old('question_type_id', '1') == '1' ? 'selected' : '' }}>Objective (MCQ - 4 Options)</option>
                                <option value="2" {{ old('question_type_id') == '2' ? 'selected' : '' }}>Descriptive / Subjective</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Ingestion Mode Switcher Tabs --}}
        <div class="mode-nav-tabs">
            <button type="button" class="mode-nav-tab active" data-target-tab="tab-manual">
                <i class="fa fa-keyboard-o mr-2"></i> 1. Manual Builder
                <span class="tab-badge">Math &amp; LaTeX</span>
            </button>
            <button type="button" class="mode-nav-tab" data-target-tab="tab-image">
                <i class="fa fa-camera mr-2"></i> 2. AI Single Image Scanner
                <span class="tab-badge">Photo / Paste</span>
            </button>
            <button type="button" class="mode-nav-tab" data-target-tab="tab-pdf">
                <i class="fa fa-file-pdf-o mr-2"></i> 3. AI Bulk PDF / Paper Parser
                <span class="tab-badge">Multi-Q</span>
            </button>
        </div>

        {{-- =========================================================================
             TAB 1: MANUAL QUESTION BUILDER (STANDALONE & DIRECTIONAL SET MODES)
             ========================================================================= --}}
        <div id="tab-manual" class="tab-content-panel">
            <form id="form-manual-question" action="{{ url('add/question') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="class_type_id" id="manual_class_type_id">
                <input type="hidden" name="subject_id" id="manual_subject_id">
                <input type="hidden" name="question_type_id" id="manual_question_type_id" value="1">
                <input type="hidden" name="submit_action" id="manual_submit_action" value="save">
                <input type="hidden" name="manual_mode" id="manual_mode" value="standalone">

                {{-- Mode Switcher Card: Standalone vs Directional Set --}}
                <div class="theme-card mb-2" style="background: #ffffff; border: 1px solid #cbd5e1;">
                    <div class="p-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="font-weight-bold" style="font-size: 11.5px; color: #002C54;">
                                <i class="fa fa-sliders text-primary mr-1"></i> Question Format Mode:
                            </span>
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-primary active manual-mode-pill" id="lbl-mode-standalone" style="font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 2px; cursor: pointer;">
                                    <input type="radio" name="manual_mode_toggle" id="mode-standalone" value="standalone" checked autocomplete="off">
                                    <i class="fa fa-file-text-o mr-1"></i> Standalone Question
                                </label>
                                <label class="btn btn-sm btn-outline-primary manual-mode-pill" id="lbl-mode-directional" style="font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 2px; cursor: pointer;">
                                    <input type="radio" name="manual_mode_toggle" id="mode-directional" value="directional" autocomplete="off">
                                    <i class="fa fa-sitemap mr-1"></i> Directional Set (Passage &amp; Sub-Questions)
                                </label>
                            </div>
                        </div>
                        <div class="small text-muted" id="manual-mode-description" style="font-size: 11px;">
                            <span class="text-primary font-weight-bold"><i class="fa fa-info-circle mr-1"></i> Standalone Mode:</span> Single independent question with 4-5 options.
                        </div>
                    </div>
                </div>

                {{-- SECTION A: STANDALONE QUESTION BUILDER --}}
                <div id="manual-standalone-wrap">
                    <div class="row">
                        {{-- Left Column: Statement & Options --}}
                        <div class="col-lg-8">
                            <div class="theme-card">
                                <div class="theme-card-header">
                                    <h3 class="card-title">
                                        <i class="fa fa-pencil text-warning mr-2"></i> Question Statement &amp; Answers
                                    </h3>
                                    <span class="badge badge-light" style="font-size: 10.5px; color: #002C54; font-weight: 700;">
                                        Format: LaTeX Math Supported ($...$)
                                    </span>
                                </div>
                                <div class="theme-card-body">
                                    {{-- Math Ribbon --}}
                                    <div class="math-ribbon-card">
                                        <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                            <span class="ribbon-title">
                                                <i class="fa fa-calculator mr-1 text-primary"></i> Quick Insert Math &amp; Science Symbols:
                                            </span>
                                            <span class="text-muted small" style="font-size: 9.5px;">Click to insert at cursor</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 math-ribbon-buttons">
                                            <button type="button" data-insert="\frac{a}{b}" class="math-symbol-btn">½ \frac{a}{b}</button>
                                            <button type="button" data-insert="\sqrt{x}" class="math-symbol-btn">√x \sqrt{x}</button>
                                            <button type="button" data-insert="x^{2}" class="math-symbol-btn">x²</button>
                                            <button type="button" data-insert="x_{1}" class="math-symbol-btn">x₁</button>
                                            <button type="button" data-insert="\pm" class="math-symbol-btn">±</button>
                                            <button type="button" data-insert="\times" class="math-symbol-btn">×</button>
                                            <button type="button" data-insert="\div" class="math-symbol-btn">÷</button>
                                            <button type="button" data-insert="\pi" class="math-symbol-btn">π</button>
                                            <button type="button" data-insert="\theta" class="math-symbol-btn">θ</button>
                                            <button type="button" data-insert="\Delta" class="math-symbol-btn">Δ</button>
                                            <button type="button" data-insert="\infty" class="math-symbol-btn">∞</button>
                                            <button type="button" data-insert="\le" class="math-symbol-btn">≤</button>
                                            <button type="button" data-insert="\ge" class="math-symbol-btn">≥</button>
                                            <button type="button" data-insert="\ne" class="math-symbol-btn">≠</button>
                                            <button type="button" data-insert="\rightarrow" class="math-symbol-btn">→</button>
                                        </div>
                                    </div>

                                    {{-- Question Textarea --}}
                                    <div class="theme-form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="manual_question_name" class="font-weight-bold mb-0" style="font-size: 11.5px; color: #002C54;">
                                                Question Statement <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex align-items-center gap-1 editor-height-tools">
                                                <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-primary"></i> Height:</span>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="manual_question_name" data-delta="-40" title="Decrease Question Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="manual_question_name" data-delta="50" title="Increase Question Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-height" data-target="manual_question_name" data-reset="125" title="Reset Default Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                        <textarea id="manual_question_name" name="name" rows="3" class="theme-form-control active-math-target" placeholder="Enter question statement, equations (e.g. $E=mc^2$), or text..." required>{{ old('name') }}</textarea>
                                    </div>

                                    {{-- Options Section (Objective) --}}
                                    <div id="manual-options-container">
                                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap">
                                            <div class="d-flex align-items-center">
                                                <label class="font-weight-bold text-primary mb-0" style="font-size: 11.5px;">
                                                    <i class="fa fa-list-ul mr-1"></i> Multiple Choice Options &amp; Correct Answer:
                                                </label>
                                                <span class="text-muted small ml-2" style="font-size: 10px;">(Select radio button for the correct option)</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="d-flex align-items-center gap-1 editor-height-tools">
                                                    <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-primary"></i> All Options Height:</span>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-options-height" data-delta="-25" title="Decrease All Options Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-options-height" data-delta="35" title="Increase All Options Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-options-height" data-reset="55" title="Reset All Options Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                                                </div>
                                                <button type="button" id="btn-toggle-manual-opt-5" class="btn btn-xs btn-outline-primary" style="font-size: 10.5px; padding: 2px 8px; font-weight: 600;">
                                                    <i class="fa fa-plus-circle mr-1"></i> Add 5th Option (E)
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row" id="manual-options-row">
                                            @foreach(['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'] as $num => $letter)
                                                 @php $optIndex = (string)($num - 1); @endphp
                                                <div class="col-md-6 mb-2">
                                                    <div class="option-input-card" id="manual-opt-card-{{ $num }}">
                                                        <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                                            <div class="d-flex align-items-center">
                                                                <span class="option-label-pill mr-2">{{ $letter }}</span>
                                                                <strong style="font-size: 11px; color: #334155;">Option {{ $letter }}</strong>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <div class="d-flex align-items-center gap-1 mr-2 editor-height-tools">
                                                                    <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="manual_ans{{ $num }}" data-delta="-25" title="Decrease Option {{ $letter }} Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                                    <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="manual_ans{{ $num }}" data-delta="35" title="Increase Option {{ $letter }} Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                                <label class="correct-radio-label text-success mb-0">
                                                                    <input type="radio" name="correct_ans" value="{{ $optIndex }}" class="manual-correct-radio mr-1" {{ old('correct_ans', '0') == $optIndex ? 'checked' : '' }}>
                                                                    <span>Mark Correct</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <textarea name="ans{{ $num }}" id="manual_ans{{ $num }}" rows="2" class="theme-form-control active-math-target" placeholder="Answer option {{ $letter }}...">{{ old('ans'.$num) }}</textarea>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- 5th Option (Option E) --}}
                                            <div class="col-md-6 mb-2 {{ old('ans5') ? '' : 'd-none' }}" id="manual-opt-col-5">
                                                <div class="option-input-card" id="manual-opt-card-5">
                                                    <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <span class="option-label-pill mr-2">E</span>
                                                            <strong style="font-size: 11px; color: #334155;">Option E</strong>
                                                            <span class="badge badge-light border text-muted small ml-1" style="font-size: 9px;">Optional</span>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div class="d-flex align-items-center gap-1 mr-2 editor-height-tools">
                                                                <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="manual_ans5" data-delta="-25" title="Decrease Option E Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                                <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="manual_ans5" data-delta="35" title="Increase Option E Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                            <label class="correct-radio-label text-success mb-0 mr-2">
                                                                <input type="radio" name="correct_ans" value="4" class="manual-correct-radio mr-1" {{ old('correct_ans') == '4' ? 'checked' : '' }}>
                                                                <span>Mark Correct</span>
                                                            </label>
                                                            <button type="button" id="btn-remove-manual-opt-5" class="btn btn-xs btn-link text-danger p-0" title="Remove Option E" style="font-size: 11px;">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <textarea name="ans5" id="manual_ans5" rows="2" class="theme-form-control active-math-target" placeholder="Answer option E...">{{ old('ans5') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Explanation / Solution --}}
                                    <div class="theme-form-group mb-0">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="manual_explanation" class="font-weight-bold mb-0" style="font-size: 11.5px; color: #334155;">
                                                <i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution Explanation / Hint <span class="text-muted font-weight-normal">(Optional)</span>
                                            </label>
                                            <div class="d-flex align-items-center gap-1 editor-height-tools">
                                                <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-warning"></i> Height:</span>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="manual_explanation" data-delta="-30" title="Decrease Solution Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="manual_explanation" data-delta="40" title="Increase Solution Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-height" data-target="manual_explanation" data-reset="75" title="Reset Default Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                        <textarea id="manual_explanation" name="explanation" rows="2" class="theme-form-control active-math-target" placeholder="Provide step-by-step reasoning or solution notes...">{{ old('explanation') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Media, Difficulty & Live Preview --}}
                        <div class="col-lg-4">
                            {{-- Meta Card --}}
                            <div class="theme-card">
                                <div class="theme-card-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff;">
                                    <h3 class="card-title">
                                        <i class="fa fa-sliders text-warning mr-2"></i> Question Meta &amp; Diagram
                                    </h3>
                                </div>
                                <div class="theme-card-body" style="background: #ffffff;">
                                    {{-- Diagram / Figure Image Upload --}}
                                    <div class="theme-form-group mb-3">
                                        <label for="manual_image" class="font-weight-bold mb-1" style="font-size: 11px; color: #334155;">
                                            <i class="fa fa-picture-o text-primary mr-1"></i> Question Diagram / Figure <span class="text-muted font-weight-normal">(JPG, PNG)</span>
                                        </label>
                                        <div class="p-2 rounded border" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                                            <input type="file" id="manual_image" name="image" accept="image/*" class="form-control-file" style="font-size: 11px;">
                                            <div id="manual-image-preview-wrapper" class="mt-2 text-center d-none">
                                                <img id="manual-image-preview" src="" alt="Diagram Preview" class="img-fluid border rounded bg-white p-1" style="max-height: 130px;">
                                                <div class="mt-1">
                                                    <button type="button" id="btn-remove-manual-img" class="btn btn-xs btn-outline-danger" style="font-size: 10px;">
                                                        <i class="fa fa-trash mr-1"></i> Remove Image
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Difficulty Level --}}
                                    <div class="theme-form-group mb-3">
                                        <label for="manual_difficulty" class="font-weight-bold mb-1" style="font-size: 11px; color: #334155;">
                                            <i class="fa fa-tachometer text-muted mr-1"></i> Difficulty Level
                                        </label>
                                        <select id="manual_difficulty" name="difficulty_level" class="theme-form-control" style="height: 33px; font-size: 12px;">
                                            <option value="Easy">Easy (Fundamental)</option>
                                            <option value="Medium" selected>Medium (Standard)</option>
                                            <option value="Hard">Hard (Advanced / Olympiad)</option>
                                        </select>
                                    </div>

                                    {{-- Student View (Exam Paper Preview) Matching Arise ERP Theme --}}
                                    <div class="preview-exam-card mb-3" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                        <div class="d-flex align-items-center justify-content-between p-2 px-3 border-bottom" style="background: #f1f5f9;">
                                            <span class="font-weight-bold" style="font-size: 11.5px; color: #002C54;">
                                                <i class="fa fa-desktop text-primary mr-1"></i> Student View (Live Preview)
                                            </span>
                                            <span id="manual-preview-correct-badge" class="badge badge-success" style="font-size: 10px; padding: 2px 8px; background: #10b981;">Correct: Option A</span>
                                        </div>
                                        <div class="p-3" id="manual-live-preview" style="min-height: 100px; font-size: 12px; color: #0f172a; line-height: 1.5;">
                                            <em>Type above to see live preview with rendered LaTeX formulas...</em>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex flex-column gap-2 mt-3">
                                        <button type="submit" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; justify-content:center; height:36px; font-weight:700; border-radius:3px; box-shadow: 0 2px 4px rgba(0,44,84,0.18);">
                                            <i class="fa fa-save mr-1"></i> Save Question to Bank
                                        </button>
                                        <button type="button" id="btn-save-and-add" class="dash-btn dash-btn-emerald" style="justify-content:center; height:36px; font-weight:700; border-radius:3px; background:#10b981; color:#ffffff; border:none; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                                            <i class="fa fa-plus-circle mr-1"></i> Save &amp; Add Another
                                        </button>
                                        <button type="reset" class="dash-btn" style="justify-content:center; color:#475569 !important; background:#f8fafc; border:1px solid #cbd5e1; height:32px; font-weight:600; border-radius:3px;">
                                            <i class="fa fa-undo mr-1"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION B: DIRECTIONAL TYPE QUESTION SET BUILDER --}}
                <div id="manual-directional-wrap" class="d-none">
                    <div class="row">
                        {{-- Left Column: Common Direction / Passage & Dynamic Sub-Questions --}}
                        <div class="col-lg-8">
                            {{-- 1. Common Passage / Direction Context Card --}}
                            <div class="theme-card mb-3">
                                <div class="theme-card-header">
                                    <h3 class="card-title">
                                        <i class="fa fa-sitemap text-warning mr-2"></i> Common Passage / Direction Context
                                    </h3>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-white small font-weight-bold" style="font-size: 11px;">Set ID:</span>
                                        <input type="text" name="group_id" id="dir_group_id" class="form-control form-control-sm text-monospace font-weight-bold text-center" style="width: 170px; height: 26px; font-size: 11px; background:#ffffff; color:#002C54; border: 1px solid #cbd5e1; border-radius: 2px;" placeholder="DIR-XXXXXX" value="DIR-{{ date('Ymd-His') }}">
                                        <button type="button" id="btn-refresh-dir-group-id" class="btn btn-xs btn-light border py-0 px-2" title="Generate New Set ID" style="height: 26px; border-radius: 2px;"><i class="fa fa-refresh"></i></button>
                                    </div>
                                </div>
                                <div class="theme-card-body">
                                    {{-- Math Ribbon --}}
                                    <div class="math-ribbon-card">
                                        <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                            <span class="ribbon-title">
                                                <i class="fa fa-calculator mr-1 text-primary"></i> Quick Insert Math &amp; Science Symbols:
                                            </span>
                                            <span class="text-muted small" style="font-size: 9.5px;">Click to insert at cursor</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 math-ribbon-buttons">
                                            <button type="button" data-insert="\frac{a}{b}" class="math-symbol-btn">½ \frac{a}{b}</button>
                                            <button type="button" data-insert="\sqrt{x}" class="math-symbol-btn">√x \sqrt{x}</button>
                                            <button type="button" data-insert="x^{2}" class="math-symbol-btn">x²</button>
                                            <button type="button" data-insert="x_{1}" class="math-symbol-btn">x₁</button>
                                            <button type="button" data-insert="\pm" class="math-symbol-btn">±</button>
                                            <button type="button" data-insert="\times" class="math-symbol-btn">×</button>
                                            <button type="button" data-insert="\div" class="math-symbol-btn">÷</button>
                                            <button type="button" data-insert="\pi" class="math-symbol-btn">π</button>
                                            <button type="button" data-insert="\theta" class="math-symbol-btn">θ</button>
                                            <button type="button" data-insert="\Delta" class="math-symbol-btn">Δ</button>
                                            <button type="button" data-insert="\infty" class="math-symbol-btn">∞</button>
                                            <button type="button" data-insert="\le" class="math-symbol-btn">≤</button>
                                            <button type="button" data-insert="\ge" class="math-symbol-btn">≥</button>
                                            <button type="button" data-insert="\ne" class="math-symbol-btn">≠</button>
                                            <button type="button" data-insert="\rightarrow" class="math-symbol-btn">→</button>
                                        </div>
                                    </div>

                                    {{-- Common Passage Textarea with Height Adjustment --}}
                                    <div class="theme-form-group">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label for="dir_direction" class="font-weight-bold mb-0" style="font-size: 11.5px; color: #002C54;">
                                                Common Passage / Direction Text <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex align-items-center gap-1 editor-height-tools">
                                                <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-primary"></i> Height:</span>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_direction" data-delta="-40" title="Decrease Passage Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_direction" data-delta="50" title="Increase Passage Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-height" data-target="dir_direction" data-reset="140" title="Reset Default Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                        <textarea id="dir_direction" name="direction" rows="4" class="theme-form-control active-math-target" placeholder="Enter common instructions or comprehension passage (e.g. Directions (Q.1 - Q.5): Read the following passage carefully and answer the questions given below...)..."></textarea>
                                    </div>

                                    {{-- Common Diagram & Data Table Row --}}
                                    <div class="row">
                                        {{-- Common Diagram / Graph --}}
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 rounded border" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                                                <label for="dir_image" class="font-weight-bold mb-1 d-block" style="font-size: 11px; color: #334155;">
                                                    <i class="fa fa-picture-o text-primary mr-1"></i> Common Passage Diagram / Graph <span class="text-muted font-weight-normal">(Optional)</span>
                                                </label>
                                                <input type="file" id="dir_image" name="direction_image" accept="image/*" class="form-control-file" style="font-size: 11px;">
                                                <div id="dir-image-preview-wrapper" class="mt-2 text-center d-none">
                                                    <img id="dir-image-preview" src="" alt="Direction Diagram" class="img-fluid border rounded bg-white p-1" style="max-height: 120px;">
                                                    <div class="mt-1">
                                                        <button type="button" id="btn-remove-dir-img" class="btn btn-xs btn-outline-danger" style="font-size: 10px;">
                                                            <i class="fa fa-trash mr-1"></i> Remove Image
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Common Data Table / Matrix --}}
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 rounded border h-100" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <label for="dir_direction_table" class="font-weight-bold mb-0" style="font-size: 11px; color: #334155;">
                                                        <i class="fa fa-table text-info mr-1"></i> Common Data Table / Matrix <span class="text-muted font-weight-normal">(Optional)</span>
                                                    </label>
                                                    <button type="button" id="btn-insert-table-template" class="btn btn-xs btn-light border py-0 px-1 text-primary" style="font-size: 9.5px;" title="Insert sample 3x3 table">
                                                        <i class="fa fa-plus-square mr-1"></i> Sample Table
                                                    </button>
                                                </div>
                                                <textarea id="dir_direction_table" name="direction_table" rows="3" class="form-control form-control-sm" style="font-size: 11px; font-family: monospace;" placeholder="Paste HTML &lt;table&gt;...&lt;/table&gt; or click Sample Table..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Sub-Questions Header Bar --}}
                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="font-weight-bold text-dark" style="font-size: 12.5px;">
                                        <i class="fa fa-list-ol text-primary mr-1"></i> Sub-Questions in this Set:
                                    </span>
                                    <span class="badge badge-info" id="dir-sub-count-text" style="font-size: 11px; background:#0284c7;">2 Sub-Questions</span>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-add-subq-trigger font-weight-bold px-2 py-1" style="font-size: 11px;">
                                    <i class="fa fa-plus-circle mr-1"></i> + Add Sub-Question
                                </button>
                            </div>

                            {{-- 3. Dynamic Sub-Questions List (Initialized with 2 Sub-Questions) --}}
                            <div id="directional-sub-questions-list"></div>

                            {{-- 4. Bottom Add Sub-Question Button --}}
                            <div class="my-3 text-center">
                                <button type="button" id="btn-add-directional-subq" class="btn btn-block btn-outline-primary py-2 font-weight-bold" style="border: 2px dashed #0284c7; background: #f0f7ff; color: #0284c7; font-size: 12.5px;">
                                    <i class="fa fa-plus-circle mr-1"></i> + Add Another Sub-Question to Set
                                </button>
                            </div>
                        </div>

                        {{-- Right Column: Directional Meta & Student View Preview --}}
                        <div class="col-lg-4">
                            <div class="theme-card">
                                <div class="theme-card-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff;">
                                    <h3 class="card-title">
                                        <i class="fa fa-sliders text-warning mr-2"></i> Directional Set Summary
                                    </h3>
                                </div>
                                <div class="theme-card-body" style="background: #ffffff;">
                                    {{-- Set Meta Pills --}}
                                    <div class="mb-3 p-2 rounded border" style="background: #f8fafc;">
                                        <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                            <span class="text-muted small">Set Identifier:</span>
                                            <span class="badge badge-light border text-primary font-weight-bold text-monospace" id="dir-meta-id-display">DIR-XXXX</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="text-muted small">Sub-Questions Count:</span>
                                            <span class="badge badge-info" id="dir-meta-total-display">2 Sub-Questions</span>
                                        </div>
                                    </div>

                                    {{-- Student View (Exam Paper Real-Time Preview) --}}
                                    <div class="preview-exam-card mb-3" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                                        <div class="d-flex align-items-center justify-content-between p-2 px-3 border-bottom" style="background: #f1f5f9;">
                                            <span class="font-weight-bold" style="font-size: 11.5px; color: #002C54;">
                                                <i class="fa fa-desktop text-primary mr-1"></i> Student View (Live Preview)
                                            </span>
                                            <span id="dir-preview-active-subq-badge" class="badge badge-primary" style="font-size: 10px; padding: 2px 8px; background: #0284c7;">Viewing Sub-Q #1</span>
                                        </div>

                                        {{-- Sub-Question Switcher Navigation Pills --}}
                                        <div class="p-2 border-bottom bg-light d-flex align-items-center gap-1 flex-wrap" id="dir-preview-tabs"></div>

                                        {{-- Preview Content Box --}}
                                        <div class="p-3" id="directional-live-preview" style="min-height: 120px; font-size: 12px; color: #0f172a; line-height: 1.5;">
                                            <em>Type passage and questions to see live preview...</em>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex flex-column gap-2 mt-3">
                                        <button type="submit" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; justify-content:center; height:36px; font-weight:700; border-radius:3px; box-shadow: 0 2px 4px rgba(0,44,84,0.18);">
                                            <i class="fa fa-save mr-1"></i> Save Directional Set to Bank
                                        </button>
                                        <button type="button" id="btn-dir-save-and-add" class="dash-btn dash-btn-emerald" style="justify-content:center; height:36px; font-weight:700; border-radius:3px; background:#10b981; color:#ffffff; border:none; box-shadow: 0 2px 4px rgba(16,185,129,0.2);">
                                            <i class="fa fa-plus-circle mr-1"></i> Save &amp; Add Another Set
                                        </button>
                                        <button type="button" id="btn-dir-reset" class="dash-btn" style="justify-content:center; color:#475569 !important; background:#f8fafc; border:1px solid #cbd5e1; height:32px; font-weight:600; border-radius:3px;">
                                            <i class="fa fa-undo mr-1"></i> Reset Set
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- =========================================================================
             TAB 2: AI SINGLE IMAGE SCANNER (PHOTO / SCREENSHOT / PASTE)
             ========================================================================= --}}
        <div id="tab-image" class="tab-content-panel d-none">
            <div class="row">
                {{-- Left: Image Upload & Preview --}}
                <div class="col-lg-5">
                    <div class="theme-card">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-camera text-warning mr-2"></i> Upload or Paste Question Image
                            </h3>
                            <span class="badge badge-light" style="color: #002C54; font-size: 10px;">
                                <i class="fa fa-clipboard mr-1"></i> Ctrl+V Paste Supported
                            </span>
                        </div>
                        <div class="theme-card-body">
                            {{-- Dropzone --}}
                            <div class="ai-dropzone-box" id="ai-image-dropzone">
                                <input type="file" id="ai-image-file-input" accept="image/*" class="d-none">
                                <i class="fa fa-cloud-upload ai-dropzone-icon"></i>
                                <strong class="d-block" style="font-size: 12px; color: #002C54;">
                                    Drop question screenshot / photo here or Browse
                                </strong>
                                <span class="text-muted small" style="font-size: 10.5px;">
                                    Supports JPG, PNG, WebP or press <strong>Ctrl+V</strong> to paste from clipboard
                                </span>
                            </div>

                            {{-- Loaded Image Preview --}}
                            <div id="ai-image-preview-card" class="mt-2 text-center d-none border rounded p-2 bg-light">
                                <img id="ai-preview-img" src="" alt="Question Image" class="img-fluid rounded border" style="max-height: 250px;">
                                <div class="mt-2 d-flex justify-content-center gap-2">
                                    <button type="button" id="btn-ai-extract-image" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff;">
                                        <i class="fa fa-magic mr-1"></i> Extract with AI Agent
                                    </button>
                                    <button type="button" id="btn-clear-ai-image" class="dash-btn dash-btn-outline" style="color:#dc2626 !important; border-color:#cbd5e1;">
                                        <i class="fa fa-trash mr-1"></i> Clear
                                    </button>
                                </div>
                            </div>

                            {{-- Optional AI Prompt / Guidance Note --}}
                            <div class="theme-form-group mt-2">
                                <label for="ai_single_extra_prompt" class="small text-muted mb-1 font-weight-normal">
                                    Optional instructions for AI (e.g. "Focus on question 4", "Physics Mechanics"):
                                </label>
                                <input type="text" id="ai_single_extra_prompt" class="theme-form-control" placeholder="e.g. Question from Class 11 Chemistry...">
                            </div>

                            {{-- AI Status Indicator --}}
                            <div id="ai-single-status" class="alert alert-info py-2 px-3 small d-none" style="font-size: 11px; border-radius: 2px;">
                                <span class="ai-pulse-spinner mr-2"></span>
                                <span id="ai-single-status-text">AI Agent is analyzing image with Google Gemini...</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: AI Extracted Question Review Form --}}
                <div class="col-lg-7">
                    <div class="theme-card">
                        <div class="theme-card-header">
                            <h3 class="card-title">
                                <i class="fa fa-check-circle-o text-warning mr-2"></i> AI-Extracted Question Review &amp; Edit
                            </h3>
                            <span id="ai-single-model-badge" class="badge badge-light d-none" style="color:#002C54; font-size: 10px;">
                                Gemini AI Parsed
                            </span>
                        </div>
                        <div class="theme-card-body">
                            <div id="ai-single-empty-placeholder" class="text-center py-5 text-muted">
                                <i class="fa fa-magic" style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px;"></i>
                                <h5 style="font-size: 13.5px; font-weight: 700; color: #475569;">No Image Analyzed Yet</h5>
                                <p style="font-size: 11px; max-width: 380px; margin: 0 auto;">
                                    Upload or paste a screenshot on the left and click <strong>Extract with AI Agent</strong>. The parsed question and 4 options will appear here for your review and instant saving.
                                </p>
                            </div>

                            <form id="form-ai-single-save" class="d-none">
                                <input type="hidden" id="ai_single_image_path">
                                <div class="theme-form-group">
                                    <label for="ai_single_q_name">Extracted Question Statement <span class="text-danger">*</span></label>
                                    <textarea id="ai_single_q_name" rows="3" class="theme-form-control" required></textarea>
                                </div>

                                {{-- Attached Diagram / Chart Box with Watermark Remover & Recreator --}}
                                <div class="theme-form-group mb-2 d-none" id="ai-single-diagram-box">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="small text-muted font-weight-bold mb-0" style="font-size: 10.5px;">
                                            <i class="fa fa-picture-o text-warning mr-1"></i> Attached Diagram / Chart:
                                        </label>
                                        <span id="ai-single-wm-badge" class="badge badge-warning-light border small d-none" style="background:#fffbeb; color:#b45309; font-size:9.5px;">
                                            <i class="fa fa-shield mr-1"></i> Watermark Detected
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 p-2 rounded border bg-light flex-wrap">
                                        <img id="ai-single-diagram-img" src="" alt="Diagram" class="img-thumbnail" style="max-height: 70px;">
                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="button" class="btn btn-xs btn-outline-success" id="btn-ai-single-clean-wm" style="font-size: 10.5px; padding: 2px 8px;">
                                                    <i class="fa fa-magic mr-1"></i> Clean Watermark
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-primary" id="btn-ai-single-recreate-chart" style="font-size: 10.5px; padding: 2px 8px;">
                                                    <i class="fa fa-bar-chart mr-1"></i> Recreate Chart (AI)
                                                </button>
                                                <button type="button" class="btn btn-xs btn-link text-danger p-0 ml-1" id="btn-ai-single-remove-img" style="font-size: 10.5px;">
                                                    <i class="fa fa-trash mr-1"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    @foreach(['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'] as $num => $letter)
                                        @php $optIndex = (string)($num - 1); @endphp
                                        <div class="col-md-6 mb-2">
                                            <div class="option-input-card" id="ai-opt-card-{{ $num }}">
                                                <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <span class="option-label-pill mr-2">{{ $letter }}</span>
                                                        <strong style="font-size: 11px; color: #334155;">Option {{ $letter }}</strong>
                                                    </div>
                                                    <label class="correct-radio-label text-success">
                                                        <input type="radio" name="ai_single_correct" value="{{ $optIndex }}" class="ai-correct-radio mr-1">
                                                        <span>Correct</span>
                                                    </label>
                                                </div>
                                                <textarea id="ai_single_ans{{ $num }}" rows="2" class="theme-form-control"></textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="theme-form-group">
                                    <label for="ai_single_explanation">AI Explanation / Solution Notes</label>
                                    <textarea id="ai_single_explanation" rows="2" class="theme-form-control"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="theme-form-group">
                                            <label for="ai_single_difficulty">Difficulty</label>
                                            <select id="ai_single_difficulty" class="theme-form-control">
                                                <option value="Easy">Easy</option>
                                                <option value="Medium" selected>Medium</option>
                                                <option value="Hard">Hard</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="theme-form-group">
                                            <label for="ai_single_topic">Detected Topic / Concept</label>
                                            <input type="text" id="ai_single_topic" class="theme-form-control" placeholder="Concept name">
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab 2 Real-Time Exam Paper Preview --}}
                                <div class="preview-exam-card mt-3 mb-2" id="ai-single-preview-card">
                                    <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                        <span class="font-weight-bold text-dark" style="font-size: 11px;">
                                            <i class="fa fa-desktop text-primary mr-1"></i> Student View (Exam Paper Preview):
                                        </span>
                                        <span class="badge badge-success" id="ai-single-preview-badge">Correct: Option A</span>
                                    </div>
                                    <div class="preview-q-name" id="ai-single-preview-name">Question statement preview...</div>
                                    <div class="text-center mb-2 d-none" id="ai-single-preview-diagram-box">
                                        <img id="ai-single-preview-diagram" src="" alt="Question Diagram" class="img-fluid rounded border p-1 bg-white" style="max-height: 180px;">
                                    </div>
                                    <div class="row">
                                        @foreach(['A','B','C','D'] as $l)
                                            <div class="col-6 mb-1">
                                                <div class="preview-opt-item" id="ai-single-preview-opt-{{ $l }}">
                                                    <strong>({{ $l }})</strong> <span class="preview-opt-text">—</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="preview-q-exp mt-1 p-2 border rounded bg-white small text-muted d-none" id="ai-single-preview-exp"></div>
                                </div>

                                <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                                    <span class="text-muted small" style="font-size: 10.5px;">
                                        <i class="fa fa-info-circle mr-1"></i> Saving will add this question under the currently selected Class &amp; Subject.
                                    </span>
                                    <button type="button" id="btn-save-ai-single" class="dash-btn dash-btn-emerald" style="height: 32px; padding: 0 16px;">
                                        <i class="fa fa-check mr-1"></i> Approve &amp; Save Question
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================================
             TAB 3: AI BULK PDF / EXAM PAPER PARSER
             ========================================================================= --}}
        <div id="tab-pdf" class="tab-content-panel d-none">
            {{-- PDF Upload Banner --}}
            <div class="theme-card">
                <div class="theme-card-header">
                    <h3 class="card-title">
                        <i class="fa fa-file-pdf-o text-warning mr-2"></i> Upload PDF Question Paper for AI Bulk Extraction
                    </h3>
                    <span class="badge badge-light" style="color: #002C54; font-size: 10px;">
                        Extracts All Questions in Seconds
                    </span>
                </div>
                <div class="theme-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8 col-lg-9">
                            <div class="theme-form-group mb-2">
                                <label for="pdf_upload_input">Select PDF Question Paper or Paper Image / Screenshot <span class="text-danger">*</span> (Up to 20MB)</label>
                                <input type="file" id="pdf_upload_input" accept="application/pdf,.pdf,image/*" class="theme-form-control" style="height: 34px; padding: 4px;">
                                <div class="theme-hint">
                                    Works with PDF exam papers, assignment sheets, or clear screenshots/photos of multiple questions.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-lg-3 text-right">
                            <button type="button" id="btn-start-pdf-extract" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; height:34px; width:100%; justify-content:center;">
                                <i class="fa fa-magic mr-1"></i> Extract Questions via AI
                            </button>
                        </div>
                    </div>

                    {{-- Optional AI Prompt for PDF --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="theme-form-group mb-2">
                                <label for="pdf_extra_prompt" class="small text-muted mb-1 font-weight-normal">
                                    <i class="fa fa-info-circle mr-1"></i> Optional instructions for AI (e.g. "Extract pages 1 to 3 only", "Hindi questions only", "Focus on Section B"):
                                </label>
                                <input type="text" id="pdf_extra_prompt" class="theme-form-control" placeholder="e.g. Extract pages 1 to 4, Hindi questions only, ignore Section A, include solutions...">
                            </div>
                        </div>
                    </div>

                    {{-- Progress / Status alert --}}
                    <div id="pdf-extract-status" class="alert alert-info py-2 px-3 small d-none mt-2" style="font-size: 11.5px; border-radius: 2px;">
                        <span class="ai-pulse-spinner mr-2"></span>
                        <span id="pdf-extract-status-text">Uploading PDF and parsing questions via Google Gemini AI Agent...</span>
                    </div>
                </div>
            </div>

            {{-- Bulk Questions Review Workspace --}}
            <div id="bulk-questions-workspace" class="d-none">
                <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-white border rounded">
                    <div class="d-flex align-items-center gap-3">
                        <label class="mb-0 font-weight-bold" style="font-size: 12px; cursor: pointer;">
                            <input type="checkbox" id="bulk-select-all" checked class="mr-1">
                            <span id="bulk-select-counter">Select All Questions (0 Selected)</span>
                        </label>
                        <span class="badge badge-primary py-1 px-2" id="bulk-total-count" style="font-size: 11px;">0 Questions Detected</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="btn-bulk-import-save" class="dash-btn dash-btn-emerald" style="height: 32px; padding: 0 16px;">
                            <i class="fa fa-database mr-1"></i> Import Selected Questions to Bank
                        </button>
                    </div>
                </div>

                {{-- Question List Container (2 Questions Per Row) --}}
                <div class="row" id="bulk-questions-list"></div>

                {{-- Bottom Bulk Action Sticky Bar --}}
                <div class="p-2 bg-white border rounded d-flex align-items-center justify-content-between mt-2">
                    <span class="text-muted small" style="font-size: 11px;">
                        <i class="fa fa-info-circle mr-1"></i> All selected questions above will be saved with the active Target Class and Subject.
                    </span>
                    <button type="button" id="btn-bulk-import-save-bottom" class="dash-btn dash-btn-emerald" style="height: 32px; padding: 0 16px;">
                        <i class="fa fa-database mr-1"></i> Import Selected Questions to Bank
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- =========================================================================
     AI SETTINGS MODAL: GOOGLE GEMINI API KEY CONFIGURATION
     ========================================================================= --}}
<div class="modal fade" id="aiSettingsModal" tabindex="-1" role="dialog" aria-labelledby="aiSettingsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 2px; overflow: hidden; border: 1px solid #002C54;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff; padding: 10px 16px;">
                <h5 class="modal-title font-weight-bold" id="aiSettingsModalTitle" style="font-size: 13.5px;">
                    <i class="fa fa-magic mr-2 text-warning"></i> Google Gemini AI Agent Settings
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="font-size: 12px; background: #ffffff;">
                <p class="text-muted mb-2" style="font-size: 11px;">
                    Arise ERP uses Google Gemini Multimodal AI to extract text, equations, formulas, options and answers directly from single photos or multi-page PDF papers.
                </p>

                <div class="theme-form-group">
                    <label for="modal_gemini_api_key">Google Gemini API Key <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" id="modal_gemini_api_key" class="theme-form-control" placeholder="AIzaSy..." value="{{ $geminiApiKey ?? '' }}" style="height: 34px;">
                        <div class="input-group-append">
                            <button type="button" id="btn-toggle-key-visibility" class="btn btn-sm btn-outline-secondary" style="border-radius: 0 2px 2px 0;">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="theme-hint mt-1">
                        Don't have a key? Get a free API key at <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-primary font-weight-bold">Google AI Studio <i class="fa fa-external-link ml-1"></i></a>
                    </div>
                </div>

                {{-- Status alert in modal --}}
                <div id="modal-key-status" class="alert d-none py-2 px-3 small" style="font-size: 11px; border-radius: 2px;"></div>
            </div>
            <div class="modal-footer p-2" style="background: #f8fafc; border-top: 1px solid #cbd5e1;">
                <button type="button" id="btn-test-gemini-key" class="dash-btn dash-btn-outline" style="color: #002C54 !important; border-color: #cbd5e1;">
                    <i class="fa fa-plug mr-1"></i> Test Connection
                </button>
                <button type="button" id="btn-save-gemini-key" class="dash-btn dash-btn-emerald">
                    <i class="fa fa-save mr-1"></i> Save API Key
                </button>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================================
     MODAL: RICH CKEDITOR FOR QUESTION STATEMENT & OPTIONS
     ========================================================================= --}}
<div class="modal fade" id="modal-rich-ckeditor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 2px; overflow: hidden; border: 1px solid #002C54;">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff; padding: 10px 16px;">
                <h5 class="modal-title font-weight-bold" style="font-size: 13px;">
                    <i class="fa fa-pencil-square-o mr-2 text-warning"></i> Rich CKEditor — Question Details &amp; Formulas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <input type="hidden" id="modal-ck-q-index" value="">
                <div class="theme-form-group mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="font-weight-bold mb-0" style="font-size: 11.5px; color: #002C54;">
                            Question Statement (WYSIWYG Rich Editor):
                        </label>
                        <div class="d-flex align-items-center gap-1 editor-height-tools">
                            <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-primary"></i> Height:</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="modal-ckeditor-question-textarea" data-delta="-40" title="Decrease Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="modal-ckeditor-question-textarea" data-delta="50" title="Increase Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-height" data-target="modal-ckeditor-question-textarea" data-reset="150" title="Reset Default Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                        </div>
                    </div>
                    <textarea id="modal-ckeditor-question-textarea" class="form-control"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="font-weight-bold mb-1 small">Option A:</label>
                        <input type="text" id="modal-ck-opt-a" class="theme-form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="font-weight-bold mb-1 small">Option B:</label>
                        <input type="text" id="modal-ck-opt-b" class="theme-form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="font-weight-bold mb-1 small">Option C:</label>
                        <input type="text" id="modal-ck-opt-c" class="theme-form-control">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="font-weight-bold mb-1 small">Option D:</label>
                        <input type="text" id="modal-ck-opt-d" class="theme-form-control">
                    </div>
                </div>
                <div class="theme-form-group mt-2">
                    <label class="font-weight-bold mb-1 small">Explanation / Solution:</label>
                    <textarea id="modal-ck-explanation" class="theme-form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer p-2 bg-light d-flex justify-content-between">
                <span class="text-muted small" style="font-size: 10.5px;">
                    <i class="fa fa-info-circle mr-1"></i> Changes will immediately sync to the question card and live preview.
                </span>
                <div>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-save-ckeditor-modal" class="dash-btn dash-btn-emerald" style="height: 30px;">
                        <i class="fa fa-check mr-1"></i> Apply Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- KaTeX & Auto-Render CDN for Dynamic Math Formula Rendering --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>
{{-- CKEditor 4 for Rich WYSIWYG Question & Formula Editing --}}
<script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // Global selects
    const globalClassSelect = document.getElementById('global_class_type_id');
    const globalSubjectSelect = document.getElementById('global_subject_id');
    const globalTypeSelect = document.getElementById('global_question_type_id');

    // Filter subjects dynamically when class changes
    globalClassSelect?.addEventListener('change', function () {
        const classId = this.value;
        if (!classId) return;

        fetch(`{{ url('subjectGetData') }}/${classId}`)
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data) && data.length > 0) {
                    globalSubjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        globalSubjectSelect.appendChild(opt);
                    });
                }
            })
            .catch(() => {});
    });

    // Sync global values into Manual Form
    const syncGlobalToManual = () => {
        const mClass = document.getElementById('manual_class_type_id');
        const mSubj = document.getElementById('manual_subject_id');
        const mType = document.getElementById('manual_question_type_id');

        if (mClass) mClass.value = globalClassSelect?.value || '';
        if (mSubj) mSubj.value = globalSubjectSelect?.value || '';
        if (mType) mType.value = globalTypeSelect?.value || '1';
    };

    // Toggle options display if Descriptive is chosen
    globalTypeSelect?.addEventListener('change', function () {
        const isMcq = (this.value === '1');
        const optContainer = document.getElementById('manual-options-container');
        if (optContainer) {
            optContainer.style.display = isMcq ? 'block' : 'none';
        }
    });

    // =========================================================================
    // MODE SWITCHING TABS
    // =========================================================================
    document.querySelectorAll('.mode-nav-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.mode-nav-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const targetId = this.dataset.targetTab;
            document.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.classList.add('d-none');
            });
            document.getElementById(targetId)?.classList.remove('d-none');

            if (targetId === 'tab-manual') {
                initManualCKEditors();
                setTimeout(updateManualLivePreview, 100);
            }
        });
    });

    // =========================================================================
    // CKEDITOR FOR MANUAL BUILDER (QUESTION, OPTIONS A-E, SOLUTION)
    // =========================================================================
    let activeCKInstance = null;
    let manualEditorsInitialized = false;

    const manualCKConfigs = {
        question: {
            height: 125,
            removePlugins: 'elementspath',
            resize_enabled: true,
            resize_dir: 'vertical',
            resize_minHeight: 50,
            resize_maxHeight: 1200,
            toolbar: [
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight' ] },
                { name: 'insert', items: [ 'Table', 'SpecialChar', 'HorizontalRule' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'Source' ] }
            ]
        },
        direction: {
            height: 140,
            removePlugins: 'elementspath',
            resize_enabled: true,
            resize_dir: 'vertical',
            resize_minHeight: 80,
            resize_maxHeight: 1200,
            toolbar: [
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight' ] },
                { name: 'insert', items: [ 'Table', 'SpecialChar', 'HorizontalRule' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'Source' ] }
            ]
        },
        option: {
            height: 55,
            removePlugins: 'elementspath',
            resize_enabled: true,
            resize_dir: 'vertical',
            resize_minHeight: 35,
            resize_maxHeight: 600,
            toolbar: [
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', 'RemoveFormat' ] },
                { name: 'insert', items: [ 'SpecialChar' ] },
                { name: 'colors', items: [ 'TextColor' ] },
                { name: 'tools', items: [ 'Source' ] }
            ]
        },
        explanation: {
            height: 75,
            removePlugins: 'elementspath',
            resize_enabled: true,
            resize_dir: 'vertical',
            resize_minHeight: 45,
            resize_maxHeight: 800,
            toolbar: [
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList' ] },
                { name: 'insert', items: [ 'Table', 'SpecialChar' ] },
                { name: 'colors', items: [ 'TextColor' ] },
                { name: 'tools', items: [ 'Source' ] }
            ]
        }
    };

    // =========================================================================
    // DYNAMIC CKEDITOR HEIGHT ADJUSTMENT (MANUAL DRAG & BUTTON CONTROLS)
    // =========================================================================
    function adjustCKEditorHeight(targetId, delta, resetVal) {
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[targetId]) {
            const editor = CKEDITOR.instances[targetId];
            const contents = editor.ui.space('contents');
            if (contents) {
                let current = contents.$.clientHeight || 60;
                let targetHeight;
                if (resetVal) {
                    targetHeight = parseInt(resetVal, 10);
                } else {
                    targetHeight = Math.max(35, Math.min(1200, current + delta));
                }
                contents.setStyle('height', targetHeight + 'px');
                editor.fire('resize');
            }
        } else {
            const ta = document.getElementById(targetId);
            if (ta) {
                let current = ta.clientHeight || 60;
                let targetHeight = resetVal ? parseInt(resetVal, 10) : Math.max(35, current + delta);
                ta.style.height = targetHeight + 'px';
            }
        }
    }

    function adjustAllOptionsHeight(delta, resetVal) {
        ['manual_ans1', 'manual_ans2', 'manual_ans3', 'manual_ans4', 'manual_ans5'].forEach(id => {
            adjustCKEditorHeight(id, delta, resetVal);
        });
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-adjust-height');
        if (btn) {
            e.preventDefault();
            const targetId = btn.dataset.target;
            const delta = btn.dataset.delta ? parseInt(btn.dataset.delta, 10) : 0;
            const resetVal = btn.dataset.reset;
            adjustCKEditorHeight(targetId, delta, resetVal);
            return;
        }

        const optBtn = e.target.closest('.btn-adjust-options-height');
        if (optBtn) {
            e.preventDefault();
            const delta = optBtn.dataset.delta ? parseInt(optBtn.dataset.delta, 10) : 0;
            const resetVal = optBtn.dataset.reset;
            adjustAllOptionsHeight(delta, resetVal);
            return;
        }
    });

    function attachCKEvents(editor) {
        editor.on('focus', function () {
            activeCKInstance = editor;
        });
        editor.on('change', function () {
            if (document.getElementById('manual_mode')?.value === 'directional') {
                updateDirectionalLivePreview();
            } else {
                updateManualLivePreview();
            }
        });
        editor.on('key', function () {
            setTimeout(() => {
                if (document.getElementById('manual_mode')?.value === 'directional') {
                    updateDirectionalLivePreview();
                } else {
                    updateManualLivePreview();
                }
            }, 60);
        });
    }

    function initManualCKEditors() {
        if (!window.CKEDITOR || manualEditorsInitialized) return;

        // Question Statement
        if (document.getElementById('manual_question_name') && !CKEDITOR.instances['manual_question_name']) {
            try {
                const edQ = CKEDITOR.replace('manual_question_name', manualCKConfigs.question);
                attachCKEvents(edQ);
                activeCKInstance = edQ;
            } catch (e) {
                console.warn('CKEditor manual_question_name init error:', e);
            }
        }

        // Options A to D
        ['manual_ans1', 'manual_ans2', 'manual_ans3', 'manual_ans4'].forEach(id => {
            if (document.getElementById(id) && !CKEDITOR.instances[id]) {
                try {
                    const edOpt = CKEDITOR.replace(id, manualCKConfigs.option);
                    attachCKEvents(edOpt);
                } catch (e) {
                    console.warn(`CKEditor ${id} init error:`, e);
                }
            }
        });

        // Option E (if visible)
        const col5 = document.getElementById('manual-opt-col-5');
        if (col5 && !col5.classList.contains('d-none')) {
            initOption5CKEditor();
        }

        // Explanation / Solution
        if (document.getElementById('manual_explanation') && !CKEDITOR.instances['manual_explanation']) {
            try {
                const edExp = CKEDITOR.replace('manual_explanation', manualCKConfigs.explanation);
                attachCKEvents(edExp);
            } catch (e) {
                console.warn('CKEditor manual_explanation init error:', e);
            }
        }

        manualEditorsInitialized = true;
    }

    function initOption5CKEditor() {
        if (!window.CKEDITOR) return;
        if (document.getElementById('manual_ans5') && !CKEDITOR.instances['manual_ans5']) {
            try {
                const ed5 = CKEDITOR.replace('manual_ans5', manualCKConfigs.option);
                attachCKEvents(ed5);
            } catch (e) {
                console.warn('CKEditor manual_ans5 init error:', e);
            }
        }
    }

    // =========================================================================
    // OPTION 5 (E) DYNAMIC TOGGLE
    // =========================================================================
    const btnToggleOpt5 = document.getElementById('btn-toggle-manual-opt-5');
    const colOpt5 = document.getElementById('manual-opt-col-5');
    const btnRemoveOpt5 = document.getElementById('btn-remove-manual-opt-5');

    function showOption5() {
        if (!colOpt5) return;
        colOpt5.classList.remove('d-none');
        initOption5CKEditor();
        if (btnToggleOpt5) {
            btnToggleOpt5.innerHTML = '<i class="fa fa-check text-success mr-1"></i> Option E Active';
            btnToggleOpt5.classList.add('disabled');
            btnToggleOpt5.setAttribute('disabled', 'disabled');
        }
        updateManualLivePreview();
    }

    function hideOption5() {
        if (!colOpt5) return;
        colOpt5.classList.add('d-none');
        if (window.CKEDITOR && CKEDITOR.instances['manual_ans5']) {
            CKEDITOR.instances['manual_ans5'].setData('');
        }
        const ta5 = document.getElementById('manual_ans5');
        if (ta5) ta5.value = '';

        // If Option E (val 4) was selected as correct answer, reset to Option A (val 0)
        const opt5Radio = document.querySelector('input[name="correct_ans"][value="4"]');
        if (opt5Radio && opt5Radio.checked) {
            const opt1Radio = document.querySelector('input[name="correct_ans"][value="0"]');
            if (opt1Radio) {
                opt1Radio.checked = true;
            }
        }

        if (btnToggleOpt5) {
            btnToggleOpt5.innerHTML = '<i class="fa fa-plus-circle mr-1"></i> Add 5th Option (E)';
            btnToggleOpt5.classList.remove('disabled');
            btnToggleOpt5.removeAttribute('disabled');
        }
        updateManualLivePreview();
    }

    btnToggleOpt5?.addEventListener('click', showOption5);
    btnRemoveOpt5?.addEventListener('click', hideOption5);

    // =========================================================================
    // MATH FORMULA INSERTION & LIVE PREVIEW (KATEX)
    // =========================================================================
    let lastActiveTextarea = document.getElementById('manual_question_name');
    document.querySelectorAll('.active-math-target').forEach(el => {
        el.addEventListener('focus', function () {
            lastActiveTextarea = this;
        });
        el.addEventListener('input', updateManualLivePreview);
    });

    document.querySelectorAll('.math-symbol-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const math = this.dataset.insert;
            const insertLatex = ` $${math}$ `;

            if (activeCKInstance && window.CKEDITOR && CKEDITOR.instances[activeCKInstance.name]) {
                activeCKInstance.insertHtml(insertLatex);
                updateManualLivePreview();
                return;
            }

            if (lastActiveTextarea) {
                const start = lastActiveTextarea.selectionStart || 0;
                const end = lastActiveTextarea.selectionEnd || 0;
                const val = lastActiveTextarea.value;
                lastActiveTextarea.value = val.substring(0, start) + insertLatex + val.substring(end);
                lastActiveTextarea.focus();
                lastActiveTextarea.selectionStart = lastActiveTextarea.selectionEnd = start + insertLatex.length;
                updateManualLivePreview();
            }
        });
    });

    function renderKaTeXIn(element) {
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
            console.warn('KaTeX render error:', e);
        }
    }

    /**
     * Formats rich exam content for preview (converts HTML/markdown/newlines to clean normal view).
     */
    function formatRichExamContent(raw) {
        if (raw === null || raw === undefined) return '';
        let text = String(raw).trim();
        if (!text) return '';

        // 1. Unescape safe HTML tags if they were escaped as &lt;br&gt;, &lt;b&gt;, etc.
        text = text.replace(/&lt;(\/?(?:br|b|strong|i|em|u|sub|sup|table|thead|tbody|tr|th|td|p|div|span|ul|ol|li|hr|h[1-6])(?:\s+[^&>]*)?)&gt;/gi, '<$1>');

        // 2. Unescape quotes in tag attributes if escaped
        text = text.replace(/<(table|div|span|p|tr|td|th)([^>]*)&quot;([^>]*)>/gi, '<$1$2"$3>');

        // 3. Temporarily protect math blocks ($...$, $$...$$, \(...\), \[...\]) before transforms
        const mathBlocks = [];
        text = text.replace(/(\$\$[\s\S]*?\$\$|\$[^\$\n\r]+?\$|\\\(.*?\\\)|\\[[\s\S]*?\\])/g, function(match) {
            mathBlocks.push(match);
            return `###MATH_BLOCK_${mathBlocks.length - 1}###`;
        });

        // 4. Convert markdown bold **bold** to <strong>bold</strong>
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // 5. Convert markdown italic *italic* to <em>italic</em>
        text = text.replace(/(^|[^\*])\*([^\*\s][^\*]*?)\*(?!\*)/g, '$1<em>$2</em>');

        // 6. Normalize existing <br> tags
        text = text.replace(/<br\s*\/?>\s*\r?\n?/gi, '<br>');

        // 7. Convert plain newlines (\n) to <br> (skip if preceded by block level tag)
        text = text.replace(/([^>])\r?\n/g, '$1<br>');
        text = text.replace(/\n/g, '<br>');

        // 8. Restore protected math blocks
        text = text.replace(/###MATH_BLOCK_(\d+)###/g, function(_, idx) {
            return mathBlocks[parseInt(idx, 10)] || '';
        });

        return text;
    }

    /**
     * Cleans values for textarea input by converting HTML <br> tags into clean editable newlines \n.
     */
    function cleanTextareaValue(raw) {
        if (raw === null || raw === undefined) return '';
        let str = String(raw);
        str = str.replace(/<br\s*\/?>/gi, '\n');
        str = str.replace(/&lt;br\s*\/?&gt;/gi, '\n');
        return str;
    }

    /**
     * Prepares textarea input for persistent DB storage by converting newlines to <br>.
     */
    function prepareForDbSave(raw) {
        if (raw === null || raw === undefined) return raw;
        let str = String(raw).trim();
        if (!str) return str;
        str = str.replace(/<br\s*\/?>\s*\r?\n?/gi, '<br>');
        str = str.replace(/([^>])\r?\n/g, '$1<br>');
        return str.trim();
    }

    function getManualFieldVal(id) {
        if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[id]) {
            return CKEDITOR.instances[id].getData();
        }
        const el = document.getElementById(id);
        return el ? el.value : '';
    }

    function updateManualLivePreview() {
        const previewEl = document.getElementById('manual-live-preview');
        const qText = getManualFieldVal('manual_question_name').trim();
        const a1 = getManualFieldVal('manual_ans1').trim();
        const a2 = getManualFieldVal('manual_ans2').trim();
        const a3 = getManualFieldVal('manual_ans3').trim();
        const a4 = getManualFieldVal('manual_ans4').trim();
        const a5 = getManualFieldVal('manual_ans5').trim();
        const explanation = getManualFieldVal('manual_explanation').trim();

        const isOpt5Visible = colOpt5 && !colOpt5.classList.contains('d-none');
        const correctRadio = document.querySelector('input[name="correct_ans"]:checked');
        const correctBadge = document.getElementById('manual-preview-correct-badge');

        const optLetters = ['A', 'B', 'C', 'D', 'E'];
        const correctIdx = correctRadio ? parseInt(correctRadio.value, 10) : 0;
        if (correctBadge) {
            correctBadge.textContent = `Correct: Option ${optLetters[correctIdx] || 'A'}`;
        }

        // Highlight correct option card in form
        for (let i = 1; i <= 5; i++) {
            const card = document.getElementById(`manual-opt-card-${i}`);
            if (card) {
                card.classList.toggle('correct-selected', (i - 1) === correctIdx);
            }
        }

        // Diagram preview check
        const imgPreviewSrc = document.getElementById('manual-image-preview')?.getAttribute('src');
        const hasImg = imgPreviewSrc && !document.getElementById('manual-image-preview-wrapper')?.classList.contains('d-none');

        if (!qText && !a1 && !a2 && !a3 && !a4 && !a5 && !hasImg) {
            if (previewEl) previewEl.innerHTML = '<div class="text-muted text-center py-4" style="font-size: 11.5px;"><i class="fa fa-info-circle mr-1"></i> Start typing question statement and options to see real-time KaTeX rendered preview...</div>';
            return;
        }

        let html = '';
        if (qText) {
            html += `<div class="font-weight-bold mb-3" style="font-size: 12.5px; color: #002C54; line-height: 1.6;">${formatRichExamContent(qText)}</div>`;
        }

        if (hasImg) {
            html += `<div class="mb-3 text-center p-2 rounded" style="background:#f8fafc; border:1px solid #e2e8f0;">
                <img src="${imgPreviewSrc}" style="max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 4px;" alt="Question Diagram">
            </div>`;
        }

        if (globalTypeSelect?.value === '1') {
            const options = [
                { letter: 'A', text: a1, idx: 0 },
                { letter: 'B', text: a2, idx: 1 },
                { letter: 'C', text: a3, idx: 2 },
                { letter: 'D', text: a4, idx: 3 },
            ];
            if (isOpt5Visible || a5) {
                options.push({ letter: 'E', text: a5, idx: 4 });
            }

            html += `<div class="row">`;
            options.forEach(opt => {
                const isCorrect = (correctIdx === opt.idx);
                const bgStyle = isCorrect ? 'background: #ecfdf5; border: 1px solid #10b981;' : 'background: #ffffff; border: 1px solid #e2e8f0;';
                const badgeStyle = isCorrect ? 'background: #10b981; color: #ffffff;' : 'background: #002C54; color: #ffffff;';
                const colWidth = (options.length === 5 && opt.letter === 'E') ? 'col-12' : 'col-6';

                html += `<div class="${colWidth} mb-2">
                    <div class="p-2 rounded d-flex align-items-center" style="${bgStyle} transition: all .15s ease;">
                        <span class="badge mr-2" style="${badgeStyle} font-size: 10px; width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: 700; flex-shrink: 0;">${opt.letter}</span>
                        <div class="flex-grow-1" style="font-size: 11.5px; color: ${isCorrect ? '#065f46' : '#1e293b'}; font-weight: ${isCorrect ? '600' : '400'};">
                            ${formatRichExamContent(opt.text || '<span class="text-muted">—</span>')}
                        </div>
                        ${isCorrect ? '<i class="fa fa-check-circle text-success ml-2" style="font-size: 14px;" title="Correct Answer"></i>' : ''}
                    </div>
                </div>`;
            });
            html += `</div>`;
        }

        if (explanation) {
            html += `<div class="mt-3 p-2 rounded" style="background: #fffbeb; border: 1px solid #fef3c7; font-size: 11.5px; color: #92400e;">
                <strong><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution Hint:</strong> ${formatRichExamContent(explanation)}
            </div>`;
        }

        if (previewEl) {
            previewEl.innerHTML = html;
            renderKaTeXIn(previewEl);
        }
    }

    document.querySelectorAll('.manual-correct-radio').forEach(r => {
        r.addEventListener('change', updateManualLivePreview);
    });

    // Diagram image preview in manual mode
    const manualImgInput = document.getElementById('manual_image');
    manualImgInput?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('manual-image-preview').src = e.target.result;
                document.getElementById('manual-image-preview-wrapper').classList.remove('d-none');
                updateManualLivePreview();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    document.getElementById('btn-remove-manual-img')?.addEventListener('click', () => {
        if (manualImgInput) manualImgInput.value = '';
        document.getElementById('manual-image-preview-wrapper').classList.add('d-none');
        updateManualLivePreview();
    });

    // =========================================================================
    // DIRECTIONAL SET BUILDER JAVASCRIPT ENGINE
    // =========================================================================
    let directionalEditorsInitialized = false;
    let directionalSubQuestions = []; // array of { idx: number, seq: number }
    let nextSubQIndex = 0;
    let activeDirSubqIdx = 0;

    // Helper to generate a unique Direction Group ID (e.g. DIR-20261101-9F2B)
    function generateDirectionGroupId() {
        const d = new Date();
        const ymd = d.getFullYear().toString() +
            String(d.getMonth() + 1).padStart(2, '0') +
            String(d.getDate()).padStart(2, '0');
        const rand = Math.random().toString(36).substring(2, 6).toUpperCase();
        return `DIR-${ymd}-${rand}`;
    }

    // Refresh Set ID button
    document.getElementById('btn-refresh-dir-group-id')?.addEventListener('click', function () {
        const newId = generateDirectionGroupId();
        const grpInput = document.getElementById('dir_group_id');
        if (grpInput) {
            grpInput.value = newId;
            syncDirectionGroupDisplay();
        }
    });

    document.getElementById('dir_group_id')?.addEventListener('input', syncDirectionGroupDisplay);

    function syncDirectionGroupDisplay() {
        const val = document.getElementById('dir_group_id')?.value.trim() || 'DIR-SET';
        const display = document.getElementById('dir-meta-id-display');
        if (display) display.textContent = val;
        updateDirectionalLivePreview();
    }

    // Mode Switcher handler
    function switchManualMode(mode) {
        const standaloneWrap = document.getElementById('manual-standalone-wrap');
        const directionalWrap = document.getElementById('manual-directional-wrap');
        const modeInput = document.getElementById('manual_mode');
        const questionNameInput = document.getElementById('manual_question_name');
        const modeDesc = document.getElementById('manual-mode-description');

        if (mode === 'directional') {
            if (modeInput) modeInput.value = 'directional';
            standaloneWrap?.classList.add('d-none');
            directionalWrap?.classList.remove('d-none');
            questionNameInput?.removeAttribute('required');

            document.getElementById('lbl-mode-directional')?.classList.add('active');
            document.getElementById('lbl-mode-standalone')?.classList.remove('active');
            if (modeDesc) {
                modeDesc.innerHTML = '<span class="text-info font-weight-bold"><i class="fa fa-sitemap mr-1"></i> Directional Set Mode:</span> Common passage &amp; multiple linked sub-questions sharing one Group ID.';
            }

            initDirectionalEngine();
            setTimeout(updateDirectionalLivePreview, 120);
        } else {
            if (modeInput) modeInput.value = 'standalone';
            directionalWrap?.classList.add('d-none');
            standaloneWrap?.classList.remove('d-none');
            questionNameInput?.setAttribute('required', 'required');

            document.getElementById('lbl-mode-standalone')?.classList.add('active');
            document.getElementById('lbl-mode-directional')?.classList.remove('active');
            if (modeDesc) {
                modeDesc.innerHTML = '<span class="text-primary font-weight-bold"><i class="fa fa-info-circle mr-1"></i> Standalone Mode:</span> Single independent question with 4-5 options.';
            }

            initManualCKEditors();
            setTimeout(updateManualLivePreview, 120);
        }
    }

    document.querySelectorAll('input[name="manual_mode_toggle"]').forEach(radio => {
        radio.addEventListener('change', function () {
            switchManualMode(this.value);
        });
    });

    // Sub-Question HTML Builder
    function buildSubQuestionCardHtml(idx, seq) {
        return `
        <div class="theme-card sub-question-card mb-3" id="dir-subq-card-${idx}" data-idx="${idx}" style="border-left: 4px solid #0284c7 !important;">
            <div class="theme-card-header d-flex align-items-center justify-content-between py-2 px-3" style="background: #f8fafc !important; border-bottom: 1px solid #cbd5e1;">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-primary font-weight-bold px-2 py-1" style="background: #0284c7; font-size: 11px;">
                        <i class="fa fa-question-circle mr-1"></i> Sub-Question #<span class="sub-q-seq">${seq}</span>
                    </span>
                    <div class="d-flex align-items-center gap-1">
                        <span class="small font-weight-bold text-dark" style="font-size: 11px;">No:</span>
                        <input type="text" name="directional_questions[${idx}][sub_question_no]" class="form-control form-control-sm text-center font-weight-bold dir-sub-no-input" value="${seq}" style="width: 50px; height: 24px; font-size: 11px; border: 1px solid #cbd5e1; border-radius: 2px;">
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="small font-weight-bold text-dark" style="font-size: 11px;">Level:</span>
                        <select name="directional_questions[${idx}][difficulty_level]" class="form-control form-control-sm" style="width: 85px; height: 24px; font-size: 11px; padding: 2px 4px;">
                            <option value="Easy">Easy</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="Hard">Hard</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-subq" data-idx="${idx}" title="Remove Sub-Question" style="font-size: 10.5px; padding: 2px 8px;">
                        <i class="fa fa-trash mr-1"></i> Remove
                    </button>
                </div>
            </div>
            <div class="theme-card-body p-3">
                {{-- Statement Textarea --}}
                <div class="theme-form-group mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label for="dir_sub_${idx}_name" class="font-weight-bold mb-0" style="font-size: 11.5px; color: #002C54;">
                            Question Statement <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center gap-1 editor-height-tools">
                            <span class="text-muted small mr-1" style="font-size: 10px;"><i class="fa fa-arrows-v text-primary"></i> Height:</span>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_sub_${idx}_name" data-delta="-30" title="Decrease Question Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_sub_${idx}_name" data-delta="40" title="Increase Question Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-adjust-height" data-target="dir_sub_${idx}_name" data-reset="90" title="Reset Default Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-refresh"></i></button>
                        </div>
                    </div>
                    <textarea id="dir_sub_${idx}_name" name="directional_questions[${idx}][name]" rows="2" class="theme-form-control active-math-target dir-sub-q-statement" placeholder="Enter question statement or formula..."></textarea>
                </div>

                {{-- Options Section --}}
                <div class="mb-2">
                    <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                        <span class="font-weight-bold text-primary small" style="font-size: 11px;">
                            <i class="fa fa-list-ul mr-1"></i> Multiple Choice Options &amp; Correct Answer:
                        </span>
                        <button type="button" class="btn btn-xs btn-outline-primary btn-toggle-subq-opt5" data-idx="${idx}" style="font-size: 10px; padding: 1px 7px;">
                            <i class="fa fa-plus mr-1"></i> Add 5th Option (E)
                        </button>
                    </div>
                    <div class="row">
                        ${[
                            { num: 1, letter: 'A', val: '0' },
                            { num: 2, letter: 'B', val: '1' },
                            { num: 3, letter: 'C', val: '2' },
                            { num: 4, letter: 'D', val: '3' },
                        ].map(opt => `
                            <div class="col-md-6 mb-2">
                                <div class="option-input-card p-2 border rounded" id="dir-sub-${idx}-opt-card-${opt.num}" style="background:#ffffff;">
                                    <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="option-label-pill mr-2">${opt.letter}</span>
                                            <strong style="font-size: 11px; color: #334155;">Option ${opt.letter}</strong>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex align-items-center gap-1 mr-2 editor-height-tools">
                                                <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="dir_sub_${idx}_ans${opt.num}" data-delta="-20" title="Decrease Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="dir_sub_${idx}_ans${opt.num}" data-delta="30" title="Increase Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <label class="correct-radio-label text-success mb-0" style="font-size: 11px; cursor: pointer;">
                                                <input type="radio" name="directional_questions[${idx}][correct_ans]" value="${opt.val}" class="dir-correct-radio mr-1" data-idx="${idx}" ${opt.val === '0' ? 'checked' : ''}>
                                                <span>Mark Correct</span>
                                            </label>
                                        </div>
                                    </div>
                                    <textarea name="directional_questions[${idx}][ans${opt.num}]" id="dir_sub_${idx}_ans${opt.num}" rows="1" class="theme-form-control active-math-target dir-subq-field" placeholder="Answer option ${opt.letter}..."></textarea>
                                </div>
                            </div>
                        `).join('')}

                        {{-- Option E --}}
                        <div class="col-md-6 mb-2 d-none" id="dir_sub_${idx}_col_5">
                            <div class="option-input-card p-2 border rounded" id="dir-sub-${idx}-opt-card-5" style="background:#ffffff;">
                                <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="option-label-pill mr-2">E</span>
                                        <strong style="font-size: 11px; color: #334155;">Option E</strong>
                                        <span class="badge badge-light border text-muted small ml-1" style="font-size: 9px;">Optional</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center gap-1 mr-2 editor-height-tools">
                                            <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="dir_sub_${idx}_ans5" data-delta="-20" title="Decrease Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-xs btn-light border btn-adjust-height py-0 px-1" data-target="dir_sub_${idx}_ans5" data-delta="30" title="Increase Height" style="font-size: 9px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                        </div>
                                        <label class="correct-radio-label text-success mb-0 mr-2" style="font-size: 11px; cursor: pointer;">
                                            <input type="radio" name="directional_questions[${idx}][correct_ans]" value="4" class="dir-correct-radio mr-1" data-idx="${idx}">
                                            <span>Mark Correct</span>
                                        </label>
                                        <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-remove-subq-opt5" data-idx="${idx}" title="Remove Option E" style="font-size: 11px;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <textarea name="directional_questions[${idx}][ans5]" id="dir_sub_${idx}_ans5" rows="1" class="theme-form-control active-math-target dir-subq-field" placeholder="Answer option E..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sub-Question Diagram & Explanation Row --}}
                <div class="row">
                    <div class="col-md-7 mb-2">
                        <div class="theme-form-group mb-0">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label for="dir_sub_${idx}_explanation" class="font-weight-bold mb-0" style="font-size: 11px; color: #334155;">
                                    <i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution / Explanation <span class="text-muted font-weight-normal">(Optional)</span>
                                </label>
                                <div class="d-flex align-items-center gap-1 editor-height-tools">
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_sub_${idx}_explanation" data-delta="-25" title="Decrease Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-adjust-height" data-target="dir_sub_${idx}_explanation" data-delta="30" title="Increase Height" style="padding: 1px 6px; font-size: 10px; line-height: 1.2;"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                            <textarea id="dir_sub_${idx}_explanation" name="directional_questions[${idx}][explanation]" rows="2" class="theme-form-control active-math-target dir-subq-field" placeholder="Step-by-step reasoning or formula notes..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="font-weight-bold mb-1" style="font-size: 11px; color: #334155;">
                            <i class="fa fa-picture-o text-primary mr-1"></i> Sub-Q Diagram <span class="text-muted font-weight-normal">(Optional)</span>
                        </label>
                        <div class="p-2 rounded border" style="background: #f8fafc; border: 1px dashed #cbd5e1 !important;">
                            <input type="file" id="dir_sub_${idx}_image" name="directional_questions[${idx}][image]" accept="image/*" class="form-control-file dir-subq-img-input" data-idx="${idx}" style="font-size: 11px;">
                            <div id="dir_sub_${idx}_img_preview_wrap" class="mt-2 text-center d-none">
                                <img id="dir_sub_${idx}_img_preview" src="" alt="Sub-Q Diagram" class="img-fluid border rounded bg-white p-1" style="max-height: 90px;">
                                <div class="mt-1">
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-remove-subq-img" data-idx="${idx}" style="font-size: 10px;">
                                        <i class="fa fa-trash mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    // Initialize CKEditors for a single sub-question
    function initSubQuestionCKEditors(idx) {
        if (!window.CKEDITOR) return;

        // Statement
        const nameId = `dir_sub_${idx}_name`;
        if (document.getElementById(nameId) && !CKEDITOR.instances[nameId]) {
            try {
                const ed = CKEDITOR.replace(nameId, manualCKConfigs.question);
                attachCKEvents(ed);
            } catch (e) { console.warn(e); }
        }

        // Options A-D
        for (let opt = 1; opt <= 4; opt++) {
            const optId = `dir_sub_${idx}_ans${opt}`;
            if (document.getElementById(optId) && !CKEDITOR.instances[optId]) {
                try {
                    const ed = CKEDITOR.replace(optId, manualCKConfigs.option);
                    attachCKEvents(ed);
                } catch (e) { console.warn(e); }
            }
        }

        // Explanation
        const expId = `dir_sub_${idx}_explanation`;
        if (document.getElementById(expId) && !CKEDITOR.instances[expId]) {
            try {
                const ed = CKEDITOR.replace(expId, manualCKConfigs.explanation);
                attachCKEvents(ed);
            } catch (e) { console.warn(e); }
        }
    }

    // Add a new Sub-Question
    function addSubQuestion() {
        const seq = directionalSubQuestions.length + 1;
        const idx = nextSubQIndex++;
        directionalSubQuestions.push({ idx: idx, seq: seq });

        const list = document.getElementById('directional-sub-questions-list');
        if (list) {
            const div = document.createElement('div');
            div.innerHTML = buildSubQuestionCardHtml(idx, seq);
            list.appendChild(div.firstElementChild);
            initSubQuestionCKEditors(idx);
        }

        activeDirSubqIdx = idx;
        updateDirectionalSubqCounts();
        rebuildDirectionalPreviewTabs();
        updateDirectionalLivePreview();

        // Scroll to newly added card
        const newCard = document.getElementById(`dir-subq-card-${idx}`);
        if (newCard) {
            newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // Remove Sub-Question
    function removeSubQuestion(idx) {
        if (directionalSubQuestions.length <= 1) {
            alert('A Directional Set must contain at least one sub-question.');
            return;
        }

        // Destroy CKEditors
        const editorIds = [
            `dir_sub_${idx}_name`,
            `dir_sub_${idx}_ans1`,
            `dir_sub_${idx}_ans2`,
            `dir_sub_${idx}_ans3`,
            `dir_sub_${idx}_ans4`,
            `dir_sub_${idx}_ans5`,
            `dir_sub_${idx}_explanation`
        ];
        editorIds.forEach(id => {
            if (window.CKEDITOR && CKEDITOR.instances[id]) {
                try { CKEDITOR.instances[id].destroy(); } catch (e) {}
            }
        });

        // Remove element
        document.getElementById(`dir-subq-card-${idx}`)?.remove();

        // Update list
        directionalSubQuestions = directionalSubQuestions.filter(x => x.idx !== idx);

        // Re-index remaining sequences
        directionalSubQuestions.forEach((item, index) => {
            item.seq = index + 1;
            const card = document.getElementById(`dir-subq-card-${item.idx}`);
            if (card) {
                const seqBadge = card.querySelector('.sub-q-seq');
                if (seqBadge) seqBadge.textContent = item.seq;
                const noInput = card.querySelector('.dir-sub-no-input');
                if (noInput && (!noInput.value || noInput.value == (index + 2))) {
                    noInput.value = item.seq;
                }
            }
        });

        if (activeDirSubqIdx === idx) {
            activeDirSubqIdx = directionalSubQuestions[0] ? directionalSubQuestions[0].idx : 0;
        }

        updateDirectionalSubqCounts();
        rebuildDirectionalPreviewTabs();
        updateDirectionalLivePreview();
    }

    // Update counts display
    function updateDirectionalSubqCounts() {
        const count = directionalSubQuestions.length;
        const text = `${count} Sub-Question${count > 1 ? 's' : ''}`;
        const countBadge = document.getElementById('dir-sub-count-text');
        if (countBadge) countBadge.textContent = text;
        const metaCount = document.getElementById('dir-meta-total-display');
        if (metaCount) metaCount.textContent = text;
    }

    // Rebuild Student View Preview Sub-Question Switcher Tabs
    function rebuildDirectionalPreviewTabs() {
        const container = document.getElementById('dir-preview-tabs');
        if (!container) return;

        let html = '';
        directionalSubQuestions.forEach(item => {
            const isActive = (item.idx === activeDirSubqIdx);
            html += `
                <button type="button" class="btn btn-xs ${isActive ? 'btn-primary font-weight-bold shadow-sm' : 'btn-outline-secondary'} dir-preview-tab-btn" data-idx="${item.idx}" style="font-size: 10px; padding: 2px 8px; border-radius: 2px;">
                    <i class="fa fa-question-circle-o mr-1"></i> Q.${item.seq}
                </button>
            `;
        });
        container.innerHTML = html;

        // Update active badge
        const activeItem = directionalSubQuestions.find(x => x.idx === activeDirSubqIdx);
        const badge = document.getElementById('dir-preview-active-subq-badge');
        if (badge) {
            badge.textContent = `Viewing Sub-Q #${activeItem ? activeItem.seq : 1}`;
        }
    }

    // Tab switcher click
    document.addEventListener('click', function (e) {
        const tabBtn = e.target.closest('.dir-preview-tab-btn');
        if (tabBtn) {
            e.preventDefault();
            activeDirSubqIdx = parseInt(tabBtn.dataset.idx, 10);
            rebuildDirectionalPreviewTabs();
            updateDirectionalLivePreview();
            return;
        }

        // Add sub-question triggers
        if (e.target.closest('#btn-add-directional-subq') || e.target.closest('.btn-add-subq-trigger')) {
            e.preventDefault();
            addSubQuestion();
            return;
        }

        // Remove sub-question
        const remBtn = e.target.closest('.btn-remove-subq');
        if (remBtn) {
            e.preventDefault();
            const idx = parseInt(remBtn.dataset.idx, 10);
            removeSubQuestion(idx);
            return;
        }

        // Toggle Option 5 for Sub-Question
        const togOpt5 = e.target.closest('.btn-toggle-subq-opt5');
        if (togOpt5) {
            e.preventDefault();
            const idx = parseInt(togOpt5.dataset.idx, 10);
            const col5 = document.getElementById(`dir_sub_${idx}_col_5`);
            if (col5) {
                col5.classList.remove('d-none');
                if (window.CKEDITOR && !CKEDITOR.instances[`dir_sub_${idx}_ans5`]) {
                    try {
                        const ed5 = CKEDITOR.replace(`dir_sub_${idx}_ans5`, manualCKConfigs.option);
                        attachCKEvents(ed5);
                    } catch (e) {}
                }
                togOpt5.style.display = 'none';
                updateDirectionalLivePreview();
            }
            return;
        }

        // Remove Option 5 for Sub-Question
        const remOpt5 = e.target.closest('.btn-remove-subq-opt5');
        if (remOpt5) {
            e.preventDefault();
            const idx = parseInt(remOpt5.dataset.idx, 10);
            const col5 = document.getElementById(`dir_sub_${idx}_col_5`);
            if (col5) {
                col5.classList.add('d-none');
                if (window.CKEDITOR && CKEDITOR.instances[`dir_sub_${idx}_ans5`]) {
                    CKEDITOR.instances[`dir_sub_${idx}_ans5`].setData('');
                }
                const togBtn = document.querySelector(`.btn-toggle-subq-opt5[data-idx="${idx}"]`);
                if (togBtn) togBtn.style.display = '';

                // Reset radio if 4 was selected
                const checkedRadio = document.querySelector(`input[name="directional_questions[${idx}][correct_ans]"]:checked`);
                if (checkedRadio && checkedRadio.value === '4') {
                    const opt1 = document.querySelector(`input[name="directional_questions[${idx}][correct_ans]"][value="0"]`);
                    if (opt1) opt1.checked = true;
                }
                updateDirectionalLivePreview();
            }
            return;
        }

        // Remove Sub-Question Image
        const remImg = e.target.closest('.btn-remove-subq-img');
        if (remImg) {
            e.preventDefault();
            const idx = parseInt(remImg.dataset.idx, 10);
            const fileInput = document.getElementById(`dir_sub_${idx}_image`);
            if (fileInput) fileInput.value = '';
            document.getElementById(`dir_sub_${idx}_img_preview_wrap`)?.classList.add('d-none');
            updateDirectionalLivePreview();
            return;
        }
    });

    // Listen to changes in correct radio for sub-questions
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('dir-correct-radio')) {
            updateDirectionalLivePreview();
            return;
        }
        if (e.target.classList.contains('dir-sub-no-input')) {
            rebuildDirectionalPreviewTabs();
            updateDirectionalLivePreview();
            return;
        }
        // Sub-question image selection
        if (e.target.classList.contains('dir-subq-img-input')) {
            const idx = e.target.dataset.idx;
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    const img = document.getElementById(`dir_sub_${idx}_img_preview`);
                    const wrap = document.getElementById(`dir_sub_${idx}_img_preview_wrap`);
                    if (img) img.src = ev.target.result;
                    if (wrap) wrap.classList.remove('d-none');
                    updateDirectionalLivePreview();
                };
                reader.readAsDataURL(e.target.files[0]);
            }
            return;
        }
    });

    // Common Direction Diagram selection & removal
    const dirImgInput = document.getElementById('dir_image');
    dirImgInput?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById('dir-image-preview');
                const wrap = document.getElementById('dir-image-preview-wrapper');
                if (img) img.src = e.target.result;
                if (wrap) wrap.classList.remove('d-none');
                updateDirectionalLivePreview();
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    document.getElementById('btn-remove-dir-img')?.addEventListener('click', function () {
        if (dirImgInput) dirImgInput.value = '';
        document.getElementById('dir-image-preview-wrapper')?.classList.add('d-none');
        updateDirectionalLivePreview();
    });

    // Insert Sample Table Template
    document.getElementById('btn-insert-table-template')?.addEventListener('click', function () {
        const tableArea = document.getElementById('dir_direction_table');
        if (tableArea) {
            const sampleHtml = `<table class="table table-bordered table-sm text-center mb-0">
  <thead style="background:#f1f5f9;">
    <tr><th>Category / Year</th><th>2023</th><th>2024</th><th>2025</th><th>2026</th></tr>
  </thead>
  <tbody>
    <tr><td>Production (Units in '000)</td><td>120</td><td>145</td><td>180</td><td>210</td></tr>
    <tr><td>Export Sales (₹ Crores)</td><td>45.5</td><td>58.2</td><td>72.0</td><td>89.4</td></tr>
    <tr><td>Domestic Sales (₹ Crores)</td><td>32.0</td><td>38.5</td><td>44.2</td><td>51.8</td></tr>
  </tbody>
</table>`;
            tableArea.value = sampleHtml;
            updateDirectionalLivePreview();
        }
    });
    document.getElementById('dir_direction_table')?.addEventListener('input', updateDirectionalLivePreview);

    // Initialize Directional Engine (Direction Passage CKEditor + Sub-Q 1 & 2)
    function initDirectionalEngine() {
        if (directionalEditorsInitialized) return;

        // 1. Passage CKEditor
        if (window.CKEDITOR && document.getElementById('dir_direction') && !CKEDITOR.instances['dir_direction']) {
            try {
                const edDir = CKEDITOR.replace('dir_direction', manualCKConfigs.direction);
                attachCKEvents(edDir);
            } catch (e) { console.warn(e); }
        }

        // 2. Clear and inject default 2 sub-questions
        const list = document.getElementById('directional-sub-questions-list');
        if (list && directionalSubQuestions.length === 0) {
            list.innerHTML = '';
            // Sub-Q 1
            const idx0 = nextSubQIndex++;
            directionalSubQuestions.push({ idx: idx0, seq: 1 });
            list.innerHTML += buildSubQuestionCardHtml(idx0, 1);

            // Sub-Q 2
            const idx1 = nextSubQIndex++;
            directionalSubQuestions.push({ idx: idx1, seq: 2 });
            list.innerHTML += buildSubQuestionCardHtml(idx1, 2);

            // Initialize CKEditors for Sub-Q 1 & 2
            setTimeout(() => {
                initSubQuestionCKEditors(idx0);
                initSubQuestionCKEditors(idx1);
                activeDirSubqIdx = idx0;
                updateDirectionalSubqCounts();
                rebuildDirectionalPreviewTabs();
                updateDirectionalLivePreview();
            }, 80);
        }

        directionalEditorsInitialized = true;
    }

    // Directional Real-Time Live Student View Preview
    function updateDirectionalLivePreview() {
        const previewEl = document.getElementById('directional-live-preview');
        if (!previewEl) return;

        const groupId = document.getElementById('dir_group_id')?.value.trim() || 'DIR-SET';
        const dirText = getManualFieldVal('dir_direction').trim();
        const dirTable = document.getElementById('dir_direction_table')?.value.trim() || '';

        const dirImgSrc = document.getElementById('dir-image-preview')?.getAttribute('src');
        const hasDirImg = dirImgSrc && !document.getElementById('dir-image-preview-wrapper')?.classList.contains('d-none');

        let html = '';

        // 1. Common Passage Banner
        html += `
        <div class="mb-3 p-3 rounded shadow-sm border" style="background: linear-gradient(180deg, #f0f7ff 0%, #e6f0fa 100%); border-left: 4px solid #0284c7 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom border-info">
                <span class="badge badge-primary font-weight-bold px-2 py-1" style="background: #0284c7; font-size: 10px;">
                    <i class="fa fa-sitemap mr-1"></i> DIRECTION SET: ${escapeHtml(groupId)}
                </span>
                <span class="text-muted small" style="font-size: 10px;">Common Instructions / Passage</span>
            </div>
            ${dirText ? `
            <div class="font-weight-bold text-dark mb-2" style="font-size: 12px; line-height: 1.6;">
                ${formatRichExamContent(dirText)}
            </div>` : `<div class="text-muted small mb-2"><em>Enter common passage or DI instructions above...</em></div>`}

            ${hasDirImg ? `
            <div class="text-center my-2 p-2 bg-white rounded border">
                <img src="${dirImgSrc}" class="img-fluid rounded" style="max-height: 180px;" alt="Direction Diagram">
            </div>` : ''}

            ${dirTable ? `
            <div class="my-2 p-2 bg-white rounded border table-responsive" style="font-size: 11px;">
                ${dirTable}
            </div>` : ''}
        </div>`;

        // 2. Active Sub-Question Preview
        const activeSubQ = directionalSubQuestions.find(x => x.idx === activeDirSubqIdx) || directionalSubQuestions[0];
        if (activeSubQ) {
            const idx = activeSubQ.idx;
            const seq = activeSubQ.seq;
            const qNoInput = document.querySelector(`#dir-subq-card-${idx} .dir-sub-no-input`);
            const qNoDisplay = qNoInput ? qNoInput.value.trim() : seq;

            const qText = getManualFieldVal(`dir_sub_${idx}_name`).trim();
            const a1 = getManualFieldVal(`dir_sub_${idx}_ans1`).trim();
            const a2 = getManualFieldVal(`dir_sub_${idx}_ans2`).trim();
            const a3 = getManualFieldVal(`dir_sub_${idx}_ans3`).trim();
            const a4 = getManualFieldVal(`dir_sub_${idx}_ans4`).trim();
            const a5 = getManualFieldVal(`dir_sub_${idx}_ans5`).trim();
            const explanation = getManualFieldVal(`dir_sub_${idx}_explanation`).trim();

            const isOpt5Visible = !document.getElementById(`dir_sub_${idx}_col_5`)?.classList.contains('d-none');
            const correctRadio = document.querySelector(`input[name="directional_questions[${idx}][correct_ans]"]:checked`);
            const correctIdx = correctRadio ? parseInt(correctRadio.value, 10) : 0;

            const subImgSrc = document.getElementById(`dir_sub_${idx}_img_preview`)?.getAttribute('src');
            const hasSubImg = subImgSrc && !document.getElementById(`dir_sub_${idx}_img_preview_wrap`)?.classList.contains('d-none');

            html += `
            <div class="p-2 bg-white rounded border">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <span class="font-weight-bold text-dark" style="font-size: 11.5px;">
                        <i class="fa fa-question-circle text-primary mr-1"></i> Question #${qNoDisplay}
                    </span>
                    <span class="badge badge-success" style="font-size: 9.5px; background: #10b981;">
                        Correct: Option ${['A','B','C','D','E'][correctIdx] || 'A'}
                    </span>
                </div>

                ${qText ? `
                <div class="font-weight-bold mb-3" style="font-size: 12px; color: #002C54; line-height: 1.55;">
                    ${formatRichExamContent(qText)}
                </div>` : `<div class="text-muted small mb-3"><em>Enter statement for Sub-Question #${seq}...</em></div>`}

                ${hasSubImg ? `
                <div class="mb-3 text-center p-2 rounded bg-light border">
                    <img src="${subImgSrc}" style="max-height: 130px; max-width: 100%; object-fit: contain; border-radius: 4px;" alt="Sub-Q Diagram">
                </div>` : ''}

                <div class="row">`;

            const options = [
                { letter: 'A', text: a1, idx: 0 },
                { letter: 'B', text: a2, idx: 1 },
                { letter: 'C', text: a3, idx: 2 },
                { letter: 'D', text: a4, idx: 3 },
            ];
            if (isOpt5Visible || a5) {
                options.push({ letter: 'E', text: a5, idx: 4 });
            }

            options.forEach(opt => {
                const isCorrect = (correctIdx === opt.idx);
                const bgStyle = isCorrect ? 'background: #ecfdf5; border: 1px solid #10b981;' : 'background: #ffffff; border: 1px solid #e2e8f0;';
                const badgeStyle = isCorrect ? 'background: #10b981; color: #ffffff;' : 'background: #002C54; color: #ffffff;';
                const colWidth = (options.length === 5 && opt.letter === 'E') ? 'col-12' : 'col-6';

                html += `
                <div class="${colWidth} mb-2">
                    <div class="p-2 rounded d-flex align-items-center" style="${bgStyle} transition: all .15s ease;">
                        <span class="badge mr-2" style="${badgeStyle} font-size: 10px; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: 700; flex-shrink: 0;">${opt.letter}</span>
                        <div class="flex-grow-1" style="font-size: 11px; color: ${isCorrect ? '#065f46' : '#1e293b'}; font-weight: ${isCorrect ? '600' : '400'};">
                            ${formatRichExamContent(opt.text || '<span class="text-muted">—</span>')}
                        </div>
                        ${isCorrect ? '<i class="fa fa-check-circle text-success ml-2" style="font-size: 13px;" title="Correct Answer"></i>' : ''}
                    </div>
                </div>`;
            });

            html += `</div>`;

            if (explanation) {
                html += `
                <div class="mt-2 p-2 rounded" style="background: #fffbeb; border: 1px solid #fef3c7; font-size: 11px; color: #92400e;">
                    <strong><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution Hint:</strong> ${formatRichExamContent(explanation)}
                </div>`;
            }

            html += `</div>`;
        }

        previewEl.innerHTML = html;
        renderKaTeXIn(previewEl);
    }

    // Directional Action Buttons
    document.getElementById('btn-dir-save-and-add')?.addEventListener('click', function () {
        syncAllCKEditors();
        document.getElementById('manual_submit_action').value = 'save_and_add';
        $('#form-manual-question').submit();
    });

    document.getElementById('btn-dir-reset')?.addEventListener('click', function () {
        if (!confirm('Are you sure you want to reset all directional passage and sub-question fields?')) return;
        ['dir_direction'].forEach(id => {
            if (window.CKEDITOR && CKEDITOR.instances[id]) CKEDITOR.instances[id].setData('');
        });
        const tableArea = document.getElementById('dir_direction_table');
        if (tableArea) tableArea.value = '';
        if (dirImgInput) dirImgInput.value = '';
        document.getElementById('dir-image-preview-wrapper')?.classList.add('d-none');

        directionalSubQuestions.forEach(item => {
            ['name', 'ans1', 'ans2', 'ans3', 'ans4', 'ans5', 'explanation'].forEach(field => {
                const edId = `dir_sub_${item.idx}_${field}`;
                if (window.CKEDITOR && CKEDITOR.instances[edId]) {
                    CKEDITOR.instances[edId].setData('');
                }
            });
            const fileInput = document.getElementById(`dir_sub_${item.idx}_image`);
            if (fileInput) fileInput.value = '';
            document.getElementById(`dir_sub_${item.idx}_img_preview_wrap`)?.classList.add('d-none');
        });

        const newId = generateDirectionGroupId();
        const grpInput = document.getElementById('dir_group_id');
        if (grpInput) grpInput.value = newId;
        syncDirectionGroupDisplay();
        updateDirectionalLivePreview();
    });

    function syncAllCKEditors() {
        if (window.CKEDITOR && CKEDITOR.instances) {
            for (let name in CKEDITOR.instances) {
                if (CKEDITOR.instances.hasOwnProperty(name)) {
                    CKEDITOR.instances[name].updateElement();
                }
            }
        }
    }

    // Manual form submission
    document.getElementById('btn-save-and-add')?.addEventListener('click', () => {
        syncAllCKEditors();
        document.getElementById('manual_submit_action').value = 'save_and_add';
        $('#form-manual-question').submit();
    });

    document.getElementById('form-manual-question')?.addEventListener('submit', function (e) {
        syncAllCKEditors();
        syncGlobalToManual();

        if (!globalClassSelect?.value) {
            alert('Please select a Target Class in the top parameters bar.');
            globalClassSelect.focus();
            e.preventDefault();
            return false;
        }
        if (!globalSubjectSelect?.value) {
            alert('Please select a Subject in the top parameters bar.');
            globalSubjectSelect.focus();
            e.preventDefault();
            return false;
        }

        const mode = document.getElementById('manual_mode')?.value || 'standalone';
        if (mode === 'directional') {
            const dirVal = getManualFieldVal('dir_direction').trim();
            if (!dirVal) {
                alert('Please enter the Common Direction / Passage text before saving.');
                if (window.CKEDITOR && CKEDITOR.instances['dir_direction']) {
                    CKEDITOR.instances['dir_direction'].focus();
                } else {
                    document.getElementById('dir_direction')?.focus();
                }
                e.preventDefault();
                return false;
            }

            // Check if at least one sub-question has statement
            let hasValidSubQ = false;
            directionalSubQuestions.forEach(item => {
                const qStatement = getManualFieldVal(`dir_sub_${item.idx}_name`).trim();
                if (qStatement) hasValidSubQ = true;
            });

            if (!hasValidSubQ) {
                alert('Please enter a question statement for at least one sub-question in the set.');
                e.preventDefault();
                return false;
            }
        } else {
            const qVal = getManualFieldVal('manual_question_name').trim();
            if (!qVal) {
                alert('Please enter a Question Statement before saving.');
                if (CKEDITOR.instances['manual_question_name']) {
                    CKEDITOR.instances['manual_question_name'].focus();
                } else {
                    document.getElementById('manual_question_name')?.focus();
                }
                e.preventDefault();
                return false;
            }
        }
    });

    // Reset button handler for standalone
    document.querySelector('#form-manual-question button[type="reset"]')?.addEventListener('click', function (e) {
        setTimeout(() => {
            ['manual_question_name', 'manual_ans1', 'manual_ans2', 'manual_ans3', 'manual_ans4', 'manual_ans5', 'manual_explanation'].forEach(id => {
                if (window.CKEDITOR && CKEDITOR.instances[id]) {
                    CKEDITOR.instances[id].setData('');
                }
            });
            hideOption5();
            const opt1Radio = document.querySelector('input[name="correct_ans"][value="0"]');
            if (opt1Radio) opt1Radio.checked = true;
            document.getElementById('manual-image-preview-wrapper')?.classList.add('d-none');
            const manualImg = document.getElementById('manual_image');
            if (manualImg) manualImg.value = '';
            updateManualLivePreview();
        }, 50);
    });

    // Auto-initialize Manual CKEditors on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initManualCKEditors, 100);
            @if(old('ans5') || old('correct_ans') == '4')
                showOption5();
            @endif
            setTimeout(updateManualLivePreview, 250);
        });
    } else {
        setTimeout(initManualCKEditors, 100);
        @if(old('ans5') || old('correct_ans') == '4')
            showOption5();
        @endif
        setTimeout(updateManualLivePreview, 250);
    }

    // =========================================================================
    // TAB 2: AI SINGLE IMAGE SCANNER (PASTE & UPLOAD)
    // =========================================================================
    const dropzone = document.getElementById('ai-image-dropzone');
    const imageFileInput = document.getElementById('ai-image-file-input');
    const previewImg = document.getElementById('ai-preview-img');
    const previewCard = document.getElementById('ai-image-preview-card');
    let loadedImageBlob = null;
    let loadedImageBase64 = null;

    dropzone?.addEventListener('click', () => imageFileInput.click());
    dropzone?.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone?.addEventListener('dragleave', () => dropzone.classList.remove('drag-over'));
    dropzone?.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag-over');
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleImageFile(e.dataTransfer.files[0]);
        }
    });

    imageFileInput?.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            handleImageFile(this.files[0]);
        }
    });

    // Clipboard Paste Listener (Ctrl+V) anywhere on screen
    window.addEventListener('paste', function (e) {
        // If user is focused on an input or textarea, let them paste normal text
        if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
            // Check if clipboard contains image
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            let hasImage = false;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    hasImage = true;
                    const blob = items[i].getAsFile();
                    handleImageFile(blob);
                    // Switch to image tab
                    document.querySelector('[data-target-tab="tab-image"]')?.click();
                    break;
                }
            }
            return;
        }

        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const blob = items[i].getAsFile();
                handleImageFile(blob);
                document.querySelector('[data-target-tab="tab-image"]')?.click();
                break;
            }
        }
    });

    function handleImageFile(file) {
        // Automatically optimize/compress image client-side to save API token cost & bandwidth
        compressImageFile(file, 1600, 0.85, function (compressedBlob, dataUrl) {
            loadedImageBlob = compressedBlob;
            loadedImageBase64 = dataUrl;
            previewImg.src = dataUrl;
            previewCard.classList.remove('d-none');
        });
    }

    // Client-side image compression to save Gemini API token and bandwidth costs
    function compressImageFile(file, maxDimension, quality, callback) {
        if (!file || !file.type.match(/image.*/)) {
            callback(file, null);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const dataUrl = e.target.result;
            const img = new Image();
            img.onload = function () {
                let width = img.width;
                let height = img.height;

                // If already small, skip compression
                if (width <= maxDimension && height <= maxDimension && file.size < 400000) {
                    callback(file, dataUrl);
                    return;
                }

                if (width > maxDimension || height > maxDimension) {
                    if (width > height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                const compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
                canvas.toBlob(function (blob) {
                    callback(blob || file, compressedDataUrl);
                }, 'image/jpeg', quality);
            };
            img.src = dataUrl;
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('btn-clear-ai-image')?.addEventListener('click', () => {
        loadedImageBlob = null;
        loadedImageBase64 = null;
        if (imageFileInput) imageFileInput.value = '';
        previewCard.classList.add('d-none');
    });

    // Single Image AI Extraction Trigger
    document.getElementById('btn-ai-extract-image')?.addEventListener('click', function () {
        if (!loadedImageBase64 && !loadedImageBlob) {
            alert('Please select or paste an image first.');
            return;
        }

        const statusEl = document.getElementById('ai-single-status');
        const statusText = document.getElementById('ai-single-status-text');
        const extractBtn = this;

        statusEl.classList.remove('d-none');
        statusText.textContent = 'AI Agent is analyzing image with Google Gemini...';
        extractBtn.disabled = true;

        const formData = new FormData();
        if (loadedImageBlob) {
            formData.append('image', loadedImageBlob);
        } else {
            formData.append('image_base64', loadedImageBase64);
        }

        const extraPrompt = document.getElementById('ai_single_extra_prompt')?.value;
        if (extraPrompt) formData.append('extra_instructions', extraPrompt);

        fetch('{{ url("question/ai-extract-single") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                const cleanError = text.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 200);
                throw new Error(`Server returned HTTP ${res.status}: ${cleanError || 'Invalid non-JSON response'}`);
            }
        })
        .then(data => {
            extractBtn.disabled = false;
            statusEl.classList.add('d-none');

            if (data.success && data.data) {
                populateAiSingleForm(data.data, data.from_cache, data.model_used);
            } else {
                alert('AI Extraction Failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            extractBtn.disabled = false;
            statusEl.classList.add('d-none');
            alert('Network error while calling AI service: ' + err.message);
        });
    });

    function populateAiSingleForm(q, fromCache = false, modelUsed = '') {
        document.getElementById('ai-single-empty-placeholder').classList.add('d-none');
        const form = document.getElementById('form-ai-single-save');
        form.classList.remove('d-none');

        document.getElementById('ai_single_q_name').value = cleanTextareaValue(q.name || '');
        document.getElementById('ai_single_ans1').value = cleanTextareaValue(q.ans1 || '');
        document.getElementById('ai_single_ans2').value = cleanTextareaValue(q.ans2 || '');
        document.getElementById('ai_single_ans3').value = cleanTextareaValue(q.ans3 || '');
        document.getElementById('ai_single_ans4').value = cleanTextareaValue(q.ans4 || '');
        document.getElementById('ai_single_explanation').value = cleanTextareaValue(q.explanation || '');
        document.getElementById('ai_single_difficulty').value = q.difficulty_level || 'Medium';
        document.getElementById('ai_single_topic').value = q.topic || '';
        document.getElementById('ai_single_image_path').value = q.image_path || '';

        // Diagram / Watermark Controls for AI Single Question
        const singleDiagBox = document.getElementById('ai-single-diagram-box');
        const singleDiagImg = document.getElementById('ai-single-diagram-img');
        const singleWmBadge = document.getElementById('ai-single-wm-badge');
        if (q.image_path && singleDiagBox && singleDiagImg) {
            const fullUrl = q.image_path.startsWith('http') ? q.image_path : '{{ asset("") }}' + q.image_path;
            singleDiagImg.src = fullUrl;
            singleDiagBox.classList.remove('d-none');
            if (q.has_watermark && singleWmBadge) {
                singleWmBadge.classList.remove('d-none');
            } else if (singleWmBadge) {
                singleWmBadge.classList.add('d-none');
            }
        } else if (singleDiagBox) {
            singleDiagBox.classList.add('d-none');
        }

        // Select correct radio and highlight card
        const correctVal = String(q.correct_ans ?? '0');
        const targetRadio = document.querySelector(`input[name="ai_single_correct"][value="${correctVal}"]`);
        if (targetRadio) targetRadio.checked = true;
        ['1','2','3','4'].forEach((num, idx) => {
            const card = document.getElementById(`ai-opt-card-${num}`);
            if (card) card.classList.toggle('correct-selected', String(idx) === correctVal);
        });

        const badge = document.getElementById('ai-single-model-badge');
        if (badge) {
            badge.className = 'badge badge-light';
            badge.innerHTML = '<i class="fa fa-magic mr-1"></i> ' + (modelUsed || 'Gemini AI Parsed');
            badge.classList.remove('d-none');
        }

        updateAiSingleLivePreview();
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function updateAiSingleLivePreview() {
        const nameVal = document.getElementById('ai_single_q_name')?.value || '';
        const a1 = document.getElementById('ai_single_ans1')?.value || '';
        const a2 = document.getElementById('ai_single_ans2')?.value || '';
        const a3 = document.getElementById('ai_single_ans3')?.value || '';
        const a4 = document.getElementById('ai_single_ans4')?.value || '';
        const expVal = document.getElementById('ai_single_explanation')?.value || '';
        const radio = document.querySelector('input[name="ai_single_correct"]:checked');
        const correctVal = radio ? radio.value : '0';
        const letter = ['A','B','C','D'][parseInt(correctVal, 10)] || 'A';

        const qName = document.getElementById('ai-single-preview-name');
        if (qName) qName.innerHTML = nameVal.trim() ? formatRichExamContent(nameVal) : '<em>Question statement empty...</em>';

        const optMap = { 'A': a1, 'B': a2, 'C': a3, 'D': a4 };
        ['A','B','C','D'].forEach((l, oIdx) => {
            const optEl = document.getElementById(`ai-single-preview-opt-${l}`);
            if (optEl) {
                const isCorr = (String(oIdx) === String(correctVal));
                optEl.className = `preview-opt-item ${isCorr ? 'preview-correct-opt' : ''}`;
                optEl.innerHTML = `<strong>(${l})</strong> <span class="preview-opt-text">${optMap[l] ? formatRichExamContent(optMap[l]) : '—'}</span>`;
            }
        });

        const badge = document.getElementById('ai-single-preview-badge');
        if (badge) badge.textContent = `Correct: Option ${letter}`;

        const expEl = document.getElementById('ai-single-preview-exp');
        if (expEl) {
            if (expVal.trim()) {
                expEl.innerHTML = `<strong class="text-dark"><i class="fa fa-info-circle mr-1"></i> Solution:</strong> <span>${formatRichExamContent(expVal)}</span>`;
                expEl.classList.remove('d-none');
            } else {
                expEl.classList.add('d-none');
            }
        }

        // Render Diagram Image in Preview
        const imgPath = document.getElementById('ai_single_image_path')?.value || '';
        const previewDiagBox = document.getElementById('ai-single-preview-diagram-box');
        const previewDiagImg = document.getElementById('ai-single-preview-diagram');
        if (previewDiagBox && previewDiagImg) {
            if (imgPath) {
                previewDiagImg.src = imgPath.startsWith('http') ? imgPath : '{{ asset("") }}' + imgPath;
                previewDiagBox.classList.remove('d-none');
            } else {
                previewDiagBox.classList.add('d-none');
            }
        }

        const box = document.getElementById('ai-single-preview-card');
        if (box) renderKaTeXIn(box);
    }

    // AI Single Question Watermark & Recreate Controls
    document.getElementById('btn-ai-single-remove-img')?.addEventListener('click', function () {
        const pathInput = document.getElementById('ai_single_image_path');
        if (pathInput) pathInput.value = '';
        document.getElementById('ai-single-diagram-box')?.classList.add('d-none');
        updateAiSingleLivePreview();
    });

    document.getElementById('btn-ai-single-clean-wm')?.addEventListener('click', function () {
        const pathInput = document.getElementById('ai_single_image_path');
        const imgPath = pathInput ? pathInput.value : '';
        if (!imgPath) return;
        handleCleanWatermark(this, imgPath, function (newPath) {
            if (pathInput) pathInput.value = newPath;
            const imgEl = document.getElementById('ai-single-diagram-img');
            if (imgEl) imgEl.src = (newPath.startsWith('http') ? newPath : '{{ asset("") }}' + newPath) + '?v=' + Date.now();
            document.getElementById('ai-single-wm-badge')?.classList.add('d-none');
            updateAiSingleLivePreview();
        });
    });

    document.getElementById('btn-ai-single-recreate-chart')?.addEventListener('click', function () {
        const pathInput = document.getElementById('ai_single_image_path');
        const imgPath = pathInput ? pathInput.value : '';
        if (!imgPath) return;
        handleRecreateChart(this, imgPath, function (newPath) {
            if (pathInput) pathInput.value = newPath;
            const imgEl = document.getElementById('ai-single-diagram-img');
            if (imgEl) imgEl.src = (newPath.startsWith('http') ? newPath : '{{ asset("") }}' + newPath) + '?v=' + Date.now();
            document.getElementById('ai-single-wm-badge')?.classList.add('d-none');
            updateAiSingleLivePreview();
        });
    });

    // Dynamic highlight & preview sync on manual radio click in AI Single Question tab
    document.querySelectorAll('.ai-correct-radio').forEach(r => {
        r.addEventListener('change', function () {
            const val = this.value;
            ['1','2','3','4'].forEach((num, idx) => {
                const card = document.getElementById(`ai-opt-card-${num}`);
                if (card) card.classList.toggle('correct-selected', String(idx) === val);
            });
            updateAiSingleLivePreview();
        });
    });

    document.querySelectorAll('#form-ai-single-save textarea').forEach(el => {
        el.addEventListener('input', updateAiSingleLivePreview);
    });

    // Save AI-Extracted Single Question
    document.getElementById('btn-save-ai-single')?.addEventListener('click', function () {
        if (!globalClassSelect?.value) {
            alert('Please select a Target Class in the top parameters bar first.');
            globalClassSelect.focus();
            return;
        }
        if (!globalSubjectSelect?.value) {
            alert('Please select a Subject in the top parameters bar first.');
            globalSubjectSelect.focus();
            return;
        }

        const qName = document.getElementById('ai_single_q_name').value.trim();
        if (!qName) {
            alert('Question statement cannot be empty.');
            return;
        }

        const correctRadio = document.querySelector('input[name="ai_single_correct"]:checked');
        const payload = {
            class_type_id: globalClassSelect.value,
            subject_id: globalSubjectSelect.value,
            question_type_id: globalTypeSelect.value,
            name: qName,
            ans1: document.getElementById('ai_single_ans1').value,
            ans2: document.getElementById('ai_single_ans2').value,
            ans3: document.getElementById('ai_single_ans3').value,
            ans4: document.getElementById('ai_single_ans4').value,
            correct_ans: correctRadio ? correctRadio.value : '0',
            explanation: document.getElementById('ai_single_explanation').value,
            difficulty_level: document.getElementById('ai_single_difficulty').value,
            image_path: document.getElementById('ai_single_image_path').value,
            ai_extracted: 1
        };

        this.disabled = true;
        this.innerHTML = '<span class="ai-pulse-spinner mr-1"></span> Saving...';

        fetch('{{ url("add/question") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Question saved successfully to Question Bank!');
                window.location.href = '{{ url("view/question") }}';
            } else {
                alert('Failed to save question: ' + (data.message || ''));
                this.disabled = false;
                this.innerHTML = '<i class="fa fa-check mr-1"></i> Approve & Save Question';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            this.disabled = false;
            this.innerHTML = '<i class="fa fa-check mr-1"></i> Approve & Save Question';
        });
    });

    // =========================================================================
    // TAB 3: AI BULK PDF / EXAM PAPER PARSER
    // =========================================================================
    let extractedQuestionsList = [];

    document.getElementById('btn-start-pdf-extract')?.addEventListener('click', function () {
        const fileInput = document.getElementById('pdf_upload_input');
        if (!fileInput.files || !fileInput.files[0]) {
            alert('Please select a PDF file first.');
            fileInput.focus();
            return;
        }

        const btn = this;
        const statusEl = document.getElementById('pdf-extract-status');
        const statusText = document.getElementById('pdf-extract-status-text');

        statusEl.classList.remove('d-none');
        statusText.textContent = 'Uploading PDF and parsing questions via Google Gemini AI Agent... (may take 10-30 seconds)';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('pdf_file', fileInput.files[0]);

        const extraPrompt = document.getElementById('pdf_extra_prompt')?.value.trim();
        if (extraPrompt) {
            formData.append('extra_instructions', extraPrompt);
        }

        fetch('{{ url("question/ai-extract-pdf") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                const cleanError = text.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 200);
                throw new Error(`Server returned HTTP ${res.status}: ${cleanError || 'Invalid non-JSON response'}`);
            }
        })
        .then(data => {
            btn.disabled = false;
            statusEl.classList.add('d-none');

            if (data.success && data.data && Array.isArray(data.data.questions)) {
                extractedQuestionsList = data.data.questions;
                renderBulkQuestions(extractedQuestionsList, data.from_cache, data.model_used);
            } else {
                alert('Bulk Extraction Failed: ' + (data.message || 'No questions could be extracted from document.'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            statusEl.classList.add('d-none');
            alert('Error during PDF extraction: ' + err.message);
        });
    });

    function renderBulkQuestions(questions, fromCache = false, modelUsed = '') {
        const workspace = document.getElementById('bulk-questions-workspace');
        const container = document.getElementById('bulk-questions-list');
        const totalBadge = document.getElementById('bulk-total-count');
        const counterText = document.getElementById('bulk-select-counter');

        workspace.classList.remove('d-none');
        totalBadge.className = 'badge badge-primary py-1 px-2';
        totalBadge.innerHTML = `<i class="fa fa-magic mr-1"></i> ${questions.length} Questions Extracted`;
        counterText.textContent = `Select All Questions (${questions.length} Selected)`;

        let html = '';
        let lastGroupId = null;

        questions.forEach((q, idx) => {
            const qNum = idx + 1;
            const correctOpt = String(q.correct_ans ?? '0');
            const diff = q.difficulty_level || 'Medium';
            const correctLetter = ['A','B','C','D','E'][parseInt(correctOpt, 10)] || 'A';
            const imgPath = q.image || '';
            const isGrouped = Boolean(q.group_id);
            const subQNo = q.sub_question_no || qNum;

            // Render Direction Group Header once per unique group_id
            if (isGrouped && q.group_id !== lastGroupId) {
                lastGroupId = q.group_id;
                const groupQuestions = questions.filter(x => x.group_id === q.group_id);
                const qRange = q.direction ? (q.direction.match(/(?:Direction|निर्देश)\s*[\(\[]?(\d+\s*[-–to]+\s*\d+)[\)\]]?/i)?.[1] || `${groupQuestions.length} Questions`) : `${groupQuestions.length} Questions`;
                const dirImg = q.image || '';

                html += `
                <div class="col-12 mb-3">
                    <div class="direction-banner-card p-3 border rounded shadow-sm" style="background: linear-gradient(180deg, #f0f7ff 0%, #e6f0fa 100%); border-left: 5px solid #0284c7 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom border-info flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 11px; background-color: #0284c7;">
                                    <i class="fa fa-sitemap mr-1"></i> DIRECTION SET: ${escapeHtml(qRange)}
                                </span>
                                <span class="text-dark font-weight-bold" style="font-size: 12px;">Comprehension / Data Interpretation Set</span>
                            </div>
                            <span class="badge badge-light border text-muted" style="font-size: 10.5px;">${groupQuestions.length} Sub-Questions in this Set</span>
                        </div>

                        {{-- 1. Direction Statement Text --}}
                        ${q.direction ? `
                        <div class="direction-statement-text mb-3 text-dark font-weight-bold" style="font-size: 12.5px; line-height: 1.6;">
                            ${formatRichExamContent(q.direction)}
                        </div>` : ''}

                        {{-- 2. Common Diagram / Graph Image (Before Table, following original paper sequence) --}}
                        ${dirImg ? `
                        <div class="direction-diagram-box text-center my-3 bg-white p-2 rounded border shadow-sm">
                            <span class="d-block small text-muted mb-2 font-weight-bold"><i class="fa fa-bar-chart mr-1 text-primary"></i> Direction Graph / Diagram:</span>
                            <img src="${dirImg.startsWith('http') ? dirImg : '{{ asset("") }}' + dirImg}" alt="Direction Diagram" class="img-fluid rounded border p-1" id="dir-diagram-img-${idx}" style="max-height: 280px;">
                            <div class="mt-2 d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                ${q.has_watermark ? `<span class="badge badge-warning-light border small" style="background:#fffbeb; color:#b45309; font-size:10px;"><i class="fa fa-shield mr-1"></i> Watermark Detected</span>` : ''}
                                <button type="button" class="btn btn-xs btn-outline-success btn-clean-diagram" data-target-img="#dir-diagram-img-${idx}" data-img-path="${escapeHtml(dirImg)}" style="font-size: 10.5px; padding: 2px 8px;">
                                    <i class="fa fa-magic mr-1"></i> Clean Watermark
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-recreate-chart" data-target-img="#dir-diagram-img-${idx}" data-img-path="${escapeHtml(dirImg)}" style="font-size: 10.5px; padding: 2px 8px;">
                                    <i class="fa fa-bar-chart mr-1"></i> Recreate Clean Chart (AI)
                                </button>
                            </div>
                        </div>` : ''}

                        {{-- 3. Data Table (After Graph) --}}
                        ${q.direction_table ? `
                        <div class="direction-table-wrapper mb-2 table-responsive bg-white p-2 rounded border shadow-sm">
                            ${q.direction_table}
                        </div>` : ''}
                    </div>
                </div>`;
            } else if (!isGrouped) {
                lastGroupId = null;
            }

            html += `
            <div class="col-12 mb-3" id="bulk-card-col-${idx}">
                <div class="bulk-q-item ${isGrouped ? 'border-info' : ''}" id="bulk-card-${idx}">
                    {{-- Hidden Group Fields --}}
                    <input type="hidden" class="bulk-field-group-id" data-index="${idx}" value="${escapeHtml(q.group_id || '')}">
                    <input type="hidden" class="bulk-field-direction" data-index="${idx}" value="${escapeHtml(q.direction || '')}">
                    <input type="hidden" class="bulk-field-direction-table" data-index="${idx}" value="${escapeHtml(q.direction_table || '')}">
                    <input type="hidden" class="bulk-field-sub-no" data-index="${idx}" value="${escapeHtml(String(subQNo))}">

                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 font-weight-bold" style="font-size: 13px; cursor: pointer; color: #002C54;">
                                <input type="checkbox" class="bulk-q-checkbox mr-1" data-index="${idx}" checked>
                                Question #${qNum}
                            </label>
                            ${isGrouped ? `
                            <span class="badge badge-info text-white" style="font-size: 10px; background-color: #0284c7;">
                                <i class="fa fa-link mr-1"></i> Sub-Q #${escapeHtml(String(subQNo))} (Direction Set)
                            </span>` : `
                            <span class="badge badge-light border" style="font-size: 10px;">Item Q${qNum}</span>`}
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center">
                                <span class="small text-muted mr-1" style="font-size: 10.5px;">Difficulty:</span>
                                <select class="theme-form-control bulk-field-difficulty" data-index="${idx}" style="height: 24px; padding: 0 6px; font-size: 11px; width: auto;">
                                    <option value="Easy" ${diff === 'Easy' ? 'selected' : ''}>Easy</option>
                                    <option value="Medium" ${diff === 'Medium' ? 'selected' : ''}>Medium</option>
                                    <option value="Hard" ${diff === 'Hard' ? 'selected' : ''}>Hard</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-open-ckeditor" data-index="${idx}" title="Open WYSIWYG CKEditor Modal" style="height: 24px; padding: 0 8px; font-size: 11px;">
                                <i class="fa fa-pencil-square-o mr-1"></i> Rich CKEditor
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-danger btn-bulk-remove" data-index="${idx}" title="Remove question" style="height: 24px; padding: 0 8px; font-size: 11px;">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Split Body: Left Editor, Right Live Exam Paper Preview --}}
                    <div class="row">
                        {{-- Left Column: Interactive Editor --}}
                        <div class="col-lg-6 col-12 border-right pr-lg-3">
                            {{-- Question Statement --}}
                            <div class="theme-form-group mb-2">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="small text-muted font-weight-bold mb-0" style="font-size: 10.5px;">
                                        <i class="fa fa-edit mr-1"></i> Question Statement (LaTeX Supported) <span class="text-danger">*</span>
                                    </label>
                                    <span class="text-muted small font-italic" style="font-size: 9.5px;">Auto-updates preview on right</span>
                                </div>
                                <textarea class="theme-form-control bulk-field-name live-sync-input" data-index="${idx}" rows="3" placeholder="Question statement or formula...">${escapeHtml(cleanTextareaValue(q.name || ''))}</textarea>
                            </div>

                            {{-- Diagram / Graph Attachment Box (Only for standalone questions, not repeated in Direction sub-questions) --}}
                            ${isGrouped ? `
                            <input type="hidden" class="bulk-field-image" id="bulk-field-image-${idx}" data-index="${idx}" value="${escapeHtml(imgPath)}">
                            <div class="mb-2 p-1 px-2 rounded" style="background: #f8fafc; border: 1px dashed #cbd5e1; font-size: 10px; color: #64748b;">
                                <i class="fa fa-sitemap mr-1 text-info"></i> Diagram linked to <strong>Direction Set</strong> above.
                            </div>
                            ` : `
                            <div class="diagram-attach-box mb-2" id="bulk-diagram-box-${idx}" data-index="${idx}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="small font-weight-bold" style="font-size: 10.5px; color: #002C54;">
                                        <i class="fa fa-picture-o text-warning mr-1"></i> Diagram / Graph Image (Optional):
                                    </span>
                                    <div>
                                        <input type="file" class="d-none bulk-diagram-file-input" id="diagram-input-${idx}" data-index="${idx}" accept="image/*">
                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-upload-diagram" data-index="${idx}" style="font-size: 10px; padding: 2px 7px;">
                                             <i class="fa fa-upload mr-1"></i> Upload / Paste (Ctrl+V)
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" class="bulk-field-image" id="bulk-field-image-${idx}" data-index="${idx}" value="${escapeHtml(imgPath)}">
                                
                                <div class="diagram-preview-thumb mt-1 ${imgPath ? '' : 'd-none'}" id="bulk-diagram-thumb-${idx}">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <img src="${imgPath ? (imgPath.startsWith('http') ? imgPath : '{{ asset("") }}' + imgPath) : ''}" alt="Diagram" class="img-thumbnail" id="bulk-diagram-img-${idx}" style="max-height: 60px;">
                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="button" class="btn btn-xs btn-outline-success btn-clean-diagram" data-target-img="#bulk-diagram-img-${idx}" data-field-id="bulk-field-image-${idx}" data-index="${idx}" data-img-path="${escapeHtml(imgPath)}" style="font-size: 10px; padding: 1px 6px;">
                                                    <i class="fa fa-magic mr-1"></i> Clean Watermark
                                                </button>
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-recreate-chart" data-target-img="#bulk-diagram-img-${idx}" data-field-id="bulk-field-image-${idx}" data-index="${idx}" data-img-path="${escapeHtml(imgPath)}" style="font-size: 10px; padding: 1px 6px;">
                                                    <i class="fa fa-bar-chart mr-1"></i> Recreate Chart (AI)
                                                </button>
                                                <button type="button" class="btn btn-xs btn-link text-danger btn-remove-diagram p-0 ml-1" data-index="${idx}" style="font-size: 10px;">
                                                    <i class="fa fa-trash mr-1"></i> Remove
                                                </button>
                                            </div>
                                            ${q.has_watermark ? `<span class="badge badge-warning-light small" style="background:#fffbeb; color:#b45309; font-size:9px;"><i class="fa fa-shield mr-1"></i> Watermark Detected</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `}

                            {{-- 4 or 5 Objective Options --}}
                            <div class="row">
                                ${(() => {
                                    const optNums = ['1','2','3','4'];
                                    if (q.ans5 !== undefined || correctOpt === '4' || q.correct_option_letter === 'E' || isGrouped) {
                                        optNums.push('5');
                                    }
                                    return optNums.map((num, oIdx) => {
                                        const optKey = 'ans' + num;
                                        const letter = ['A','B','C','D','E'][oIdx];
                                        const isCorrect = (correctOpt === String(oIdx));
                                        return `
                                        <div class="col-sm-6 mb-2">
                                            <div class="option-input-card ${isCorrect ? 'correct-selected' : ''}" id="bulk-opt-card-${idx}-${num}">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="option-label-pill">${letter}</span>
                                                        <strong style="font-size: 11px; color: #334155;">Option ${letter}</strong>
                                                    </div>
                                                    <label class="correct-radio-label text-success">
                                                        <input type="radio" name="bulk_correct_${idx}" value="${oIdx}" class="bulk-field-correct mr-1" data-index="${idx}" data-num="${num}" ${isCorrect ? 'checked' : ''}>
                                                        <span>Correct</span>
                                                    </label>
                                                </div>
                                                <textarea class="theme-form-control bulk-field-${optKey} live-sync-input" data-index="${idx}" rows="2" placeholder="Option ${letter}">${escapeHtml(cleanTextareaValue(q[optKey] || ''))}</textarea>
                                            </div>
                                        </div>`;
                                    }).join('');
                                })()}
                            </div>

                            {{-- Explanation / Solution --}}
                            <div class="theme-form-group mb-0 mt-1 pt-1 border-top">
                                <label class="small text-muted mb-1 font-weight-bold" style="font-size: 10.5px;">
                                    <i class="fa fa-lightbulb-o text-warning mr-1"></i> Explanation / Solution
                                </label>
                                <textarea class="theme-form-control bulk-field-explanation live-sync-input" data-index="${idx}" rows="2" placeholder="Explanation or solution notes...">${escapeHtml(cleanTextareaValue(q.explanation || ''))}</textarea>
                            </div>
                        </div>

                        {{-- Right Column: Live Exam / Student View Preview --}}
                        <div class="col-lg-6 col-12 pl-lg-3 mt-3 mt-lg-0">
                            <div class="preview-exam-card" id="preview-exam-card-${idx}">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                    <span class="font-weight-bold text-dark" style="font-size: 11.5px;">
                                        <i class="fa fa-desktop text-primary mr-1"></i> Student View (Exam Paper Preview)
                                    </span>
                                    <span class="badge badge-success preview-badge-correct" id="preview-badge-${idx}" style="font-size: 10.5px;">
                                        Correct: Option ${correctLetter}
                                    </span>
                                </div>

                                {{-- Direction Context Snippet (if belongs to Direction Set) --}}
                                ${q.direction ? `
                                <div class="preview-direction-snippet mb-2 p-2 rounded" style="background: #f1f5f9; border-left: 3px solid #0284c7; font-size: 11px; line-height: 1.4;">
                                    <strong class="text-primary d-block mb-1"><i class="fa fa-info-circle mr-1"></i> Direction / Context:</strong>
                                    <div class="text-muted" style="max-height: 60px; overflow-y: auto;">${formatRichExamContent(q.direction)}</div>
                                </div>` : ''}

                                {{-- Rendered Question Statement with KaTeX --}}
                                <div class="preview-q-name" id="preview-q-name-${idx}">
                                    <strong>Q${subQNo}.</strong> ${q.name ? formatRichExamContent(q.name) : '<em>Question statement empty...</em>'}
                                </div>

                                {{-- Rendered Diagram Image (Only for standalone questions, not repeated in Direction sub-questions) --}}
                                ${!isGrouped ? `
                                <div class="preview-q-diagram text-center mb-2 ${imgPath ? '' : 'd-none'}" id="preview-q-diagram-${idx}">
                                    <img src="${imgPath ? (imgPath.startsWith('http') ? imgPath : '{{ asset("") }}' + imgPath) : ''}" alt="Question Diagram" class="img-fluid rounded border p-1 bg-white" style="max-height: 180px;">
                                </div>` : `
                                <div id="preview-q-diagram-${idx}" class="d-none"></div>
                                `}

                                {{-- Rendered Options A, B, C, D, E --}}
                                <div class="row preview-q-options">
                                    ${(() => {
                                        const optNums = ['1','2','3','4'];
                                        if (q.ans5 !== undefined || correctOpt === '4' || q.correct_option_letter === 'E' || isGrouped) {
                                            optNums.push('5');
                                        }
                                        return optNums.map((num, oIdx) => {
                                            const optKey = 'ans' + num;
                                            const letter = ['A','B','C','D','E'][oIdx];
                                            const isCorrect = (correctOpt === String(oIdx));
                                            return `
                                            <div class="col-6 mb-2">
                                                <div class="preview-opt-item ${isCorrect ? 'preview-correct-opt' : ''}" id="preview-opt-${idx}-${letter}">
                                                    <strong>(${letter})</strong> <span class="preview-opt-text">${q[optKey] ? formatRichExamContent(q[optKey]) : '—'}</span>
                                                </div>
                                            </div>`;
                                        }).join('');
                                    })()}
                                </div>

                                {{-- Rendered Explanation / Solution --}}
                                <div class="preview-q-exp mt-2 p-2 border rounded bg-white small text-muted ${q.explanation ? '' : 'd-none'}" id="preview-q-exp-${idx}">
                                    <strong class="text-dark"><i class="fa fa-info-circle mr-1"></i> Solution:</strong>
                                    <span class="preview-exp-text">${formatRichExamContent(q.explanation || '')}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        container.innerHTML = html;
        
        // Initial KaTeX render on all preview cards and direction banners
        questions.forEach((_, idx) => {
            const previewCard = document.getElementById(`preview-exam-card-${idx}`);
            if (previewCard) renderKaTeXIn(previewCard);
        });
        document.querySelectorAll('.direction-banner-card').forEach(el => renderKaTeXIn(el));

        // Bind Live Input Syncing (typing in left textarea updates right preview live)
        container.querySelectorAll('.live-sync-input').forEach(input => {
            input.addEventListener('input', function () {
                const idx = this.dataset.index;
                updateBulkCardPreview(idx);
            });
        });

        // Bind radio change to update .correct-selected on option cards AND right preview
        container.querySelectorAll('.bulk-field-correct').forEach(r => {
            r.addEventListener('change', function () {
                const qIdx = this.dataset.index;
                const optNum = this.dataset.num;
                ['1','2','3','4','5'].forEach(n => {
                    const optCard = document.getElementById(`bulk-opt-card-${qIdx}-${n}`);
                    if (optCard) optCard.classList.toggle('correct-selected', n === optNum);
                });
                updateBulkCardPreview(qIdx);
            });
        });

        // Bind Diagram File Upload Trigger Button
        container.querySelectorAll('.btn-upload-diagram').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.dataset.index;
                document.getElementById(`diagram-input-${idx}`)?.click();
            });
        });

        // Bind Diagram File Change
        container.querySelectorAll('.bulk-diagram-file-input').forEach(input => {
            input.addEventListener('change', function () {
                const idx = this.dataset.index;
                if (this.files && this.files[0]) {
                    uploadDiagramFile(this.files[0], idx);
                }
            });
        });

        // Bind Diagram Remove Button
        container.querySelectorAll('.btn-remove-diagram').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.dataset.index;
                const imgField = document.getElementById(`bulk-field-image-${idx}`);
                if (imgField) imgField.value = '';
                document.getElementById(`bulk-diagram-thumb-${idx}`)?.classList.add('d-none');
                updateBulkCardPreview(idx);
            });
        });

        // Bind Ctrl+V Paste inside diagram box
        container.querySelectorAll('.diagram-attach-box').forEach(box => {
            box.addEventListener('paste', function (e) {
                const idx = this.dataset.index;
                const items = (e.clipboardData || e.originalEvent?.clipboardData)?.items;
                if (!items) return;

                for (let item of items) {
                    if (item.type.indexOf('image') !== -1) {
                        e.preventDefault();
                        const blob = item.getAsFile();
                        uploadDiagramFile(blob, idx);
                        break;
                    }
                }
            });
        });

        // Bind CKEditor Modal Open Button
        container.querySelectorAll('.btn-open-ckeditor').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = this.dataset.index;
                openCKEditorForQuestion(idx);
            });
        });

        // Bind bulk checkboxes
        container.querySelectorAll('.bulk-q-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const itemCard = document.getElementById(`bulk-card-${this.dataset.index}`);
                if (itemCard) itemCard.classList.toggle('excluded', !this.checked);
                updateBulkCounter();
            });
        });

        // Bind delete buttons
        container.querySelectorAll('.btn-bulk-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index, 10);
                document.getElementById(`bulk-card-col-${idx}`)?.remove();
                delete extractedQuestionsList[idx];
                updateBulkCounter();
            });
        });

        // Bind Watermark Clean Button in Bulk / Direction cards
        container.querySelectorAll('.btn-clean-diagram').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetImgSel = this.dataset.targetImg;
                const fieldId = this.dataset.fieldId;
                const idx = this.dataset.index;
                const imgPath = this.dataset.imgPath;
                const buttonEl = this;

                handleCleanWatermark(buttonEl, imgPath, function (newPath) {
                    if (targetImgSel) {
                        const targetImg = document.querySelector(targetImgSel);
                        if (targetImg) targetImg.src = (newPath.startsWith('http') ? newPath : '{{ asset("") }}' + newPath) + '?v=' + Date.now();
                    }
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field) field.value = newPath;
                    }
                    buttonEl.dataset.imgPath = newPath;
                    const siblingRecreate = buttonEl.parentElement?.querySelector('.btn-recreate-chart');
                    if (siblingRecreate) siblingRecreate.dataset.imgPath = newPath;

                    // Remove watermark badge in this block if present
                    const parentCard = buttonEl.closest('.direction-diagram-box, .diagram-preview-thumb');
                    parentCard?.querySelector('.badge-warning-light')?.remove();

                    if (idx !== undefined) {
                        updateBulkCardPreview(idx);
                    }
                });
            });
        });

        // Bind Recreate Chart Button in Bulk / Direction cards
        container.querySelectorAll('.btn-recreate-chart').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetImgSel = this.dataset.targetImg;
                const fieldId = this.dataset.fieldId;
                const idx = this.dataset.index;
                const imgPath = this.dataset.imgPath;
                const buttonEl = this;

                handleRecreateChart(buttonEl, imgPath, function (newPath) {
                    if (targetImgSel) {
                        const targetImg = document.querySelector(targetImgSel);
                        if (targetImg) targetImg.src = (newPath.startsWith('http') ? newPath : '{{ asset("") }}' + newPath) + '?v=' + Date.now();
                    }
                    if (fieldId) {
                        const field = document.getElementById(fieldId);
                        if (field) field.value = newPath;
                    }
                    buttonEl.dataset.imgPath = newPath;
                    const siblingClean = buttonEl.parentElement?.querySelector('.btn-clean-diagram');
                    if (siblingClean) siblingClean.dataset.imgPath = newPath;

                    // Remove watermark badge in this block if present
                    const parentCard = buttonEl.closest('.direction-diagram-box, .diagram-preview-thumb');
                    parentCard?.querySelector('.badge-warning-light')?.remove();

                    if (idx !== undefined) {
                        updateBulkCardPreview(idx);
                    }
                });
            });
        });

        workspace.scrollIntoView({ behavior: 'smooth' });
    }

    // Function to update the Right-Side Live Exam Preview for question [idx]
    function updateBulkCardPreview(idx) {
        const card = document.getElementById(`bulk-card-${idx}`);
        if (!card) return;

        const qNum = parseInt(idx, 10) + 1;
        const nameVal = card.querySelector('.bulk-field-name')?.value || '';
        const a1 = card.querySelector('.bulk-field-ans1')?.value || '';
        const a2 = card.querySelector('.bulk-field-ans2')?.value || '';
        const a3 = card.querySelector('.bulk-field-ans3')?.value || '';
        const a4 = card.querySelector('.bulk-field-ans4')?.value || '';
        const a5 = card.querySelector('.bulk-field-ans5')?.value || '';
        const expVal = card.querySelector('.bulk-field-explanation')?.value || '';
        const correctRadio = card.querySelector('.bulk-field-correct:checked');
        const correctVal = correctRadio ? correctRadio.value : '0';
        const letter = ['A','B','C','D','E'][parseInt(correctVal, 10)] || 'A';
        const imgPath = card.querySelector('.bulk-field-image')?.value || '';

        // 1. Question Statement
        const qNameEl = document.getElementById(`preview-q-name-${idx}`);
        if (qNameEl) {
            qNameEl.innerHTML = nameVal.trim() ? `<strong>Q${qNum}.</strong> ${formatRichExamContent(nameVal)}` : `<em>Question Q${qNum} statement empty...</em>`;
        }

        // 2. Diagram Image (Only show in card preview if question is NOT part of a Direction Set)
        const isGroupedCard = Boolean(card.querySelector('.bulk-field-group-id')?.value);
        const diagEl = document.getElementById(`preview-q-diagram-${idx}`);
        if (diagEl) {
            if (imgPath && !isGroupedCard) {
                const fullUrl = imgPath.startsWith('http') ? imgPath : '{{ asset("") }}' + imgPath;
                diagEl.innerHTML = `<img src="${fullUrl}" alt="Question Diagram" class="img-fluid rounded border p-1 bg-white" style="max-height: 180px;">`;
                diagEl.classList.remove('d-none');
            } else {
                diagEl.innerHTML = '';
                diagEl.classList.add('d-none');
            }
        }

        // 3. Options A, B, C, D, E
        const optMap = { 'A': a1, 'B': a2, 'C': a3, 'D': a4, 'E': a5 };
        const letters = ['A','B','C','D'];
        if (a5 || correctVal === '4') letters.push('E');
        letters.forEach((l, oIdx) => {
            const optEl = document.getElementById(`preview-opt-${idx}-${l}`);
            if (optEl) {
                const isCorr = (String(oIdx) === String(correctVal));
                optEl.className = `preview-opt-item ${isCorr ? 'preview-correct-opt' : ''}`;
                optEl.innerHTML = `<strong>(${l})</strong> <span class="preview-opt-text">${optMap[l] ? formatRichExamContent(optMap[l]) : '—'}</span>`;
            }
        });

        // 4. Correct Badge
        const badge = document.getElementById(`preview-badge-${idx}`);
        if (badge) badge.textContent = `Correct: Option ${letter}`;

        // 5. Solution
        const expEl = document.getElementById(`preview-q-exp-${idx}`);
        if (expEl) {
            if (expVal.trim()) {
                expEl.innerHTML = `<strong class="text-dark"><i class="fa fa-info-circle mr-1"></i> Solution:</strong> <span>${formatRichExamContent(expVal)}</span>`;
                expEl.classList.remove('d-none');
            } else {
                expEl.classList.add('d-none');
            }
        }

        // 6. Re-run KaTeX formatting on this preview card
        const previewCard = document.getElementById(`preview-exam-card-${idx}`);
        if (previewCard) {
            renderKaTeXIn(previewCard);
        }
    }

    // Function to upload diagram file via AJAX to /question/upload-diagram
    function uploadDiagramFile(file, idx) {
        const formData = new FormData();
        formData.append('diagram_file', file);

        const thumbBox = document.getElementById(`bulk-diagram-thumb-${idx}`);
        const imgField = document.getElementById(`bulk-field-image-${idx}`);

        fetch('{{ url("question/upload-diagram") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.file_path) {
                if (imgField) imgField.value = data.file_path;
                if (thumbBox) {
                    thumbBox.classList.remove('d-none');
                    const img = thumbBox.querySelector('img');
                    if (img) img.src = data.url;
                }
                updateBulkCardPreview(idx);
            } else {
                alert('Diagram upload failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            alert('Upload error: ' + err.message);
        });
    }

    // Function to Clean Watermarks via AJAX
    function handleCleanWatermark(btn, imagePath, onComplete) {
        if (!imagePath) {
            alert('No image found to clean.');
            return;
        }
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Cleaning...';

        fetch('{{ url("question/clean-watermark") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ image_path: imagePath })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (data.success && data.image_path) {
                if (data.cleaned) {
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="fa fa-check mr-1"></i> Cleaned!';
                    setTimeout(() => {
                        btn.className = 'btn btn-xs btn-outline-success btn-clean-diagram';
                        btn.innerHTML = originalHtml;
                    }, 2500);
                } else {
                    alert('No watermark detected in this image. The image is already clean.');
                }
                if (typeof onComplete === 'function') {
                    onComplete(data.image_path);
                }
            } else {
                alert('Watermark clean error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Network error while cleaning watermark: ' + err.message);
        });
    }

    // Function to Recreate Chart using AI + Matplotlib vector rendering
    function handleRecreateChart(btn, imagePath, onComplete) {
        if (!imagePath) {
            alert('No image found to recreate.');
            return;
        }
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> AI Recreating...';

        const customKey = document.getElementById('modal_gemini_api_key')?.value?.trim() || '';

        fetch('{{ url("question/recreate-chart") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ image_path: imagePath, api_key: customKey })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (data.success && data.image_path) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
                btn.innerHTML = '<i class="fa fa-check mr-1"></i> Recreated!';
                setTimeout(() => {
                    btn.className = 'btn btn-xs btn-outline-primary btn-recreate-chart';
                    btn.innerHTML = originalHtml;
                }, 2500);
                if (typeof onComplete === 'function') {
                    onComplete(data.image_path);
                }
            } else {
                alert('AI chart recreation failed: ' + (data.message || 'Could not parse chart data from image.'));
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert('Network error while recreating chart: ' + err.message);
        });
    }

    // =========================================================================
    // CKEDITOR ON-DEMAND MODAL INITIALIZATION & SYNC
    // =========================================================================
    let ckModalEditor = null;
    function initCKEditorModal() {
        if (window.CKEDITOR && !ckModalEditor && document.getElementById('modal-ckeditor-question-textarea')) {
            try {
                ckModalEditor = CKEDITOR.replace('modal-ckeditor-question-textarea', {
                    height: 150,
                    removePlugins: 'elementspath',
                    resize_enabled: true,
                    resize_dir: 'vertical',
                    resize_minHeight: 80,
                    resize_maxHeight: 1200,
                    toolbar: [
                        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat' ] },
                        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote' ] },
                        { name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar' ] },
                        { name: 'styles', items: [ 'Format', 'FontSize' ] },
                        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                        { name: 'tools', items: [ 'Maximize', 'Source' ] }
                    ]
                });
            } catch (e) {
                console.warn('CKEditor init error:', e);
            }
        }
    }

    function openCKEditorForQuestion(idx) {
        initCKEditorModal();
        const card = document.getElementById(`bulk-card-${idx}`);
        if (!card) return;

        const nameVal = card.querySelector('.bulk-field-name')?.value || '';
        const a1 = card.querySelector('.bulk-field-ans1')?.value || '';
        const a2 = card.querySelector('.bulk-field-ans2')?.value || '';
        const a3 = card.querySelector('.bulk-field-ans3')?.value || '';
        const a4 = card.querySelector('.bulk-field-ans4')?.value || '';
        const expVal = card.querySelector('.bulk-field-explanation')?.value || '';

        document.getElementById('modal-ck-q-index').value = idx;
        document.getElementById('modal-ck-opt-a').value = a1;
        document.getElementById('modal-ck-opt-b').value = a2;
        document.getElementById('modal-ck-opt-c').value = a3;
        document.getElementById('modal-ck-opt-d').value = a4;
        document.getElementById('modal-ck-explanation').value = expVal;

        if (ckModalEditor) {
            ckModalEditor.setData(nameVal);
        } else {
            const ta = document.getElementById('modal-ckeditor-question-textarea');
            if (ta) ta.value = nameVal;
        }

        $('#modal-rich-ckeditor').modal('show');
    }

    document.getElementById('btn-save-ckeditor-modal')?.addEventListener('click', function () {
        const idx = document.getElementById('modal-ck-q-index').value;
        const card = document.getElementById(`bulk-card-${idx}`);
        if (!card) return;

        let content = '';
        if (ckModalEditor) {
            content = ckModalEditor.getData();
        } else {
            content = document.getElementById('modal-ckeditor-question-textarea')?.value || '';
        }

        const a1 = document.getElementById('modal-ck-opt-a')?.value || '';
        const a2 = document.getElementById('modal-ck-opt-b')?.value || '';
        const a3 = document.getElementById('modal-ck-opt-c')?.value || '';
        const a4 = document.getElementById('modal-ck-opt-d')?.value || '';
        const exp = document.getElementById('modal-ck-explanation')?.value || '';

        const fieldName = card.querySelector('.bulk-field-name');
        if (fieldName) fieldName.value = content;
        const fieldAns1 = card.querySelector('.bulk-field-ans1');
        if (fieldAns1) fieldAns1.value = a1;
        const fieldAns2 = card.querySelector('.bulk-field-ans2');
        if (fieldAns2) fieldAns2.value = a2;
        const fieldAns3 = card.querySelector('.bulk-field-ans3');
        if (fieldAns3) fieldAns3.value = a3;
        const fieldAns4 = card.querySelector('.bulk-field-ans4');
        if (fieldAns4) fieldAns4.value = a4;
        const fieldExp = card.querySelector('.bulk-field-explanation');
        if (fieldExp) fieldExp.value = exp;

        updateBulkCardPreview(idx);
        $('#modal-rich-ckeditor').modal('hide');
    });

    function updateBulkCounter() {
        const checked = document.querySelectorAll('.bulk-q-checkbox:checked').length;
        const total = document.querySelectorAll('.bulk-q-checkbox').length;
        const counterText = document.getElementById('bulk-select-counter');
        if (counterText) counterText.textContent = `Select All Questions (${checked} of ${total} Selected)`;
    }

    document.getElementById('bulk-select-all')?.addEventListener('change', function () {
        const checkState = this.checked;
        document.querySelectorAll('.bulk-q-checkbox').forEach(cb => {
            cb.checked = checkState;
            const itemCard = document.getElementById(`bulk-card-${cb.dataset.index}`);
            if (itemCard) itemCard.classList.toggle('excluded', !checkState);
        });
        updateBulkCounter();
    });

    // Save Bulk Selected Questions
    const handleBulkSave = () => {
        if (!globalClassSelect?.value) {
            alert('Please select a Target Class in the top parameters bar first.');
            globalClassSelect.focus();
            return;
        }
        if (!globalSubjectSelect?.value) {
            alert('Please select a Subject in the top parameters bar first.');
            globalSubjectSelect.focus();
            return;
        }

        const selectedCards = document.querySelectorAll('.bulk-q-checkbox:checked');
        if (selectedCards.length === 0) {
            alert('No questions selected for import.');
            return;
        }

        const preparedQuestions = [];
        selectedCards.forEach(cb => {
            const idx = cb.dataset.index;
            const card = document.getElementById(`bulk-card-${idx}`);
            if (!card) return;

            const name = card.querySelector('.bulk-field-name')?.value?.trim();
            const a1 = card.querySelector('.bulk-field-ans1')?.value?.trim();
            const a2 = card.querySelector('.bulk-field-ans2')?.value?.trim();
            const a3 = card.querySelector('.bulk-field-ans3')?.value?.trim();
            const a4 = card.querySelector('.bulk-field-ans4')?.value?.trim();
            const a5 = card.querySelector('.bulk-field-ans5')?.value?.trim();
            const correctRadio = card.querySelector('.bulk-field-correct:checked');
            const explanation = card.querySelector('.bulk-field-explanation')?.value?.trim();
            const difficulty = card.querySelector('.bulk-field-difficulty')?.value || 'Medium';
            const imagePath = card.querySelector('.bulk-field-image')?.value?.trim();
            const groupId = card.querySelector('.bulk-field-group-id')?.value?.trim();
            const direction = card.querySelector('.bulk-field-direction')?.value?.trim();
            const directionTable = card.querySelector('.bulk-field-direction-table')?.value?.trim();
            const subNo = card.querySelector('.bulk-field-sub-no')?.value?.trim();

            if (name) {
                preparedQuestions.push({
                    name: prepareForDbSave(name),
                    question_type_id: globalTypeSelect ? globalTypeSelect.value : 1,
                    ans1: a1 ? prepareForDbSave(a1) : null,
                    ans2: a2 ? prepareForDbSave(a2) : null,
                    ans3: a3 ? prepareForDbSave(a3) : null,
                    ans4: a4 ? prepareForDbSave(a4) : null,
                    ans5: a5 ? prepareForDbSave(a5) : null,
                    group_id: groupId || null,
                    direction: direction ? prepareForDbSave(direction) : null,
                    direction_table: directionTable || null,
                    sub_question_no: subNo || null,
                    correct_ans: correctRadio ? correctRadio.value : '0',
                    explanation: explanation ? prepareForDbSave(explanation) : null,
                    difficulty_level: difficulty,
                    image: imagePath || null
                });
            }
        });

        if (preparedQuestions.length === 0) {
            alert('No valid questions to save.');
            return;
        }

        const btn1 = document.getElementById('btn-bulk-import-save');
        const btn2 = document.getElementById('btn-bulk-import-save-bottom');
        if (btn1) { btn1.disabled = true; btn1.innerHTML = '<span class="ai-pulse-spinner mr-1"></span> Importing...'; }
        if (btn2) { btn2.disabled = true; btn2.innerHTML = '<span class="ai-pulse-spinner mr-1"></span> Importing...'; }

        fetch('{{ url("question/bulk-store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                class_type_id: globalClassSelect.value,
                subject_id: globalSubjectSelect.value,
                questions: preparedQuestions
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Success! ${data.count} questions have been added to your Question Bank.`);
                window.location.href = '{{ url("view/question") }}';
            } else {
                alert('Import failed: ' + (data.message || ''));
                if (btn1) { btn1.disabled = false; btn1.innerHTML = '<i class="fa fa-database mr-1"></i> Import Selected Questions to Bank'; }
                if (btn2) { btn2.disabled = false; btn2.innerHTML = '<i class="fa fa-database mr-1"></i> Import Selected Questions to Bank'; }
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            if (btn1) { btn1.disabled = false; btn1.innerHTML = '<i class="fa fa-database mr-1"></i> Import Selected Questions to Bank'; }
            if (btn2) { btn2.disabled = false; btn2.innerHTML = '<i class="fa fa-database mr-1"></i> Import Selected Questions to Bank'; }
        });
    };

    document.getElementById('btn-bulk-import-save')?.addEventListener('click', handleBulkSave);
    document.getElementById('btn-bulk-import-save-bottom')?.addEventListener('click', handleBulkSave);

    // =========================================================================
    // AI SETTINGS MODAL: KEY VISIBILITY & TESTING
    // =========================================================================
    document.getElementById('btn-toggle-key-visibility')?.addEventListener('click', function () {
        const input = document.getElementById('modal_gemini_api_key');
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        this.innerHTML = isPass ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
    });

    // Test API Key
    document.getElementById('btn-test-gemini-key')?.addEventListener('click', function () {
        const key = document.getElementById('modal_gemini_api_key').value.trim();
        if (!key) {
            alert('Please enter a Google Gemini API Key first.');
            return;
        }

        const statusEl = document.getElementById('modal-key-status');
        statusEl.className = 'alert alert-info py-2 px-3 small';
        statusEl.innerHTML = '<span class="ai-pulse-spinner mr-2"></span> Testing connection with Google Gemini API...';
        statusEl.classList.remove('d-none');

        fetch('{{ url("question/save-ai-key") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ api_key: key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                statusEl.className = 'alert alert-success py-2 px-3 small';
                statusEl.innerHTML = '<i class="fa fa-check-circle mr-1"></i> ' + data.message;
            } else {
                statusEl.className = 'alert alert-danger py-2 px-3 small';
                statusEl.innerHTML = '<i class="fa fa-exclamation-triangle mr-1"></i> ' + data.message;
            }
        })
        .catch(err => {
            statusEl.className = 'alert alert-danger py-2 px-3 small';
            statusEl.innerHTML = '<i class="fa fa-times-circle mr-1"></i> Connection failed: ' + err.message;
        });
    });

    // Save API Key
    document.getElementById('btn-save-gemini-key')?.addEventListener('click', function () {
        const key = document.getElementById('modal_gemini_api_key').value.trim();
        if (!key) {
            alert('Please enter a Google Gemini API Key.');
            return;
        }

        const statusEl = document.getElementById('modal-key-status');
        statusEl.className = 'alert alert-info py-2 px-3 small';
        statusEl.innerHTML = '<span class="ai-pulse-spinner mr-2"></span> Validating and saving API key...';
        statusEl.classList.remove('d-none');

        fetch('{{ url("question/save-ai-key") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ api_key: key })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                statusEl.className = 'alert alert-success py-2 px-3 small';
                statusEl.innerHTML = '<i class="fa fa-check-circle mr-1"></i> ' + data.message;
                setTimeout(() => {
                    $('#aiSettingsModal').modal('hide');
                    location.reload();
                }, 1000);
            } else {
                statusEl.className = 'alert alert-danger py-2 px-3 small';
                statusEl.innerHTML = '<i class="fa fa-exclamation-triangle mr-1"></i> ' + data.message;
            }
        })
        .catch(err => {
            statusEl.className = 'alert alert-danger py-2 px-3 small';
            statusEl.innerHTML = '<i class="fa fa-times-circle mr-1"></i> Error: ' + err.message;
        });
    });

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }
});
</script>
@endsection

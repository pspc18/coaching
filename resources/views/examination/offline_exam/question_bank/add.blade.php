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
    gap: 5px;
    padding: 4px 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 29px;
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
    gap: 6px !important;
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
    padding: 6px 14px;
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
    gap: 6px;
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
    transition: border-color .15s ease, box-shadow .15s ease;
}
.option-input-card.correct-selected {
    border-color: #059669;
    background: #f0fdf4;
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
}
.correct-radio-label {
    margin: 0;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

/* 8. AI Extracted Question List / Table in Bulk Mode */
.bulk-q-item {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #002C54;
    border-radius: 2px;
    padding: 10px;
    margin-bottom: 8px;
    transition: all 0.12s ease;
}
.bulk-q-item:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.bulk-q-item.excluded {
    opacity: 0.55;
    border-left-color: #94a3b8;
    background: #f8fafc;
}
</style>

<div class="content-wrapper">
    <div class="qb-page-wrapper" id="question-builder-app">

        {{-- 1. Hero Header Banner --}}
        <div class="perf-hero">
            <div class="perf-hero-text">
                <span class="perf-kicker">
                    <i class="fa fa-graduation-cap"></i> Examination &amp; Question Bank System
                </span>
                <h1 class="perf-title">
                    <i class="fa fa-plus-circle"></i> Create &amp; AI Ingestion Engine
                </h1>
                <p class="perf-subtitle">
                    <span>Session: {{ Session::get('session_name') ?? date('Y') }}</span> &bull;
                    <span>Manual entry with LaTeX math &bull; Single image OCR &bull; Bulk PDF question paper parser via Google Gemini AI</span>
                </p>
            </div>
            <div class="perf-hero-actions">
                <a href="{{ url('view/question') }}" class="dash-btn dash-btn-outline" title="View all questions in Question Bank">
                    <i class="fa fa-list"></i> Question Bank
                </a>
                <button type="button" class="dash-btn {{ $hasAiKey ? 'dash-btn-light' : 'dash-btn-emerald' }}" data-toggle="modal" data-target="#aiSettingsModal" title="Configure Google Gemini API Key">
                    <i class="fa fa-magic mr-1"></i> AI Settings
                    @if($hasAiKey)
                        <span class="badge badge-success ml-1" style="font-size: 9px;"><i class="fa fa-check"></i> Connected</span>
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
                    <i class="fa fa-sliders text-warning"></i> Target Academic Context (Class &amp; Subject)
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
                <i class="fa fa-keyboard-o"></i> 1. Manual Builder
                <span class="tab-badge">Math &amp; LaTeX</span>
            </button>
            <button type="button" class="mode-nav-tab" data-target-tab="tab-image">
                <i class="fa fa-camera"></i> 2. AI Single Image Scanner
                <span class="tab-badge">Photo / Paste</span>
            </button>
            <button type="button" class="mode-nav-tab" data-target-tab="tab-pdf">
                <i class="fa fa-file-pdf-o"></i> 3. AI Bulk PDF / Paper Parser
                <span class="tab-badge">Multi-Q</span>
            </button>
        </div>

        {{-- =========================================================================
             TAB 1: MANUAL QUESTION BUILDER
             ========================================================================= --}}
        <div id="tab-manual" class="tab-content-panel">
            <form id="form-manual-question" action="{{ url('add/question') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="class_type_id" id="manual_class_type_id">
                <input type="hidden" name="subject_id" id="manual_subject_id">
                <input type="hidden" name="question_type_id" id="manual_question_type_id" value="1">
                <input type="hidden" name="submit_action" id="manual_submit_action" value="save">

                <div class="row">
                    {{-- Left Column: Statement & Options --}}
                    <div class="col-lg-8">
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title">
                                    <i class="fa fa-pencil text-warning"></i> Question Statement &amp; Answers
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
                                    <label for="manual_question_name">Question Statement <span class="text-danger">*</span></label>
                                    <textarea id="manual_question_name" name="name" rows="3" class="theme-form-control active-math-target" placeholder="Enter question statement, equations (e.g. $E=mc^2$), or text..." required>{{ old('name') }}</textarea>
                                </div>

                                {{-- Options Section (Objective) --}}
                                <div id="manual-options-container">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="font-weight-bold text-primary mb-0" style="font-size: 11.5px;">
                                            <i class="fa fa-list-ul mr-1"></i> Multiple Choice Options &amp; Correct Answer:
                                        </label>
                                        <span class="text-muted small" style="font-size: 10px;">Select radio button for the correct option</span>
                                    </div>

                                    <div class="row">
                                        @foreach(['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'] as $num => $letter)
                                            @php $optIndex = (string)($num - 1); @endphp
                                            <div class="col-md-6">
                                                <div class="option-input-card" id="manual-opt-card-{{ $num }}">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <span class="option-label-pill">{{ $letter }}</span>
                                                            <strong style="font-size: 11px;">Option {{ $letter }}</strong>
                                                        </div>
                                                        <label class="correct-radio-label text-success">
                                                            <input type="radio" name="correct_ans" value="{{ $optIndex }}" class="manual-correct-radio" {{ old('correct_ans', '0') == $optIndex ? 'checked' : '' }}>
                                                            <span>Mark Correct</span>
                                                        </label>
                                                    </div>
                                                    <textarea name="ans{{ $num }}" id="manual_ans{{ $num }}" rows="2" class="theme-form-control active-math-target" placeholder="Answer option {{ $letter }}...">{{ old('ans'.$num) }}</textarea>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Explanation / Solution --}}
                                <div class="theme-form-group mb-0">
                                    <label for="manual_explanation">Solution Explanation / Hint <span class="text-muted font-weight-normal">(Optional)</span></label>
                                    <textarea id="manual_explanation" name="explanation" rows="2" class="theme-form-control active-math-target" placeholder="Provide step-by-step reasoning or solution notes...">{{ old('explanation') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Media, Difficulty & Live Preview --}}
                    <div class="col-lg-4">
                        {{-- Meta Card --}}
                        <div class="theme-card">
                            <div class="theme-card-header">
                                <h3 class="card-title"><i class="fa fa-cog text-warning"></i> Question Meta &amp; Diagram</h3>
                            </div>
                            <div class="theme-card-body">
                                {{-- Diagram / Figure Image Upload --}}
                                <div class="theme-form-group">
                                    <label for="manual_image">Question Diagram / Figure <span class="text-muted font-weight-normal">(JPG, PNG)</span></label>
                                    <input type="file" id="manual_image" name="image" accept="image/*" class="theme-form-control" style="height: 33px; padding: 3px;">
                                    <div id="manual-image-preview-wrapper" class="mt-2 text-center d-none">
                                        <img id="manual-image-preview" src="" alt="Diagram Preview" class="img-fluid border rounded" style="max-height: 140px;">
                                        <div class="mt-1">
                                            <button type="button" id="btn-remove-manual-img" class="btn btn-xs btn-outline-danger">Remove Image</button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Difficulty Level --}}
                                <div class="theme-form-group">
                                    <label for="manual_difficulty">Difficulty Level</label>
                                    <select id="manual_difficulty" name="difficulty_level" class="theme-form-control">
                                        <option value="Easy">Easy (Fundamental)</option>
                                        <option value="Medium" selected>Medium (Standard)</option>
                                        <option value="Hard">Hard (Advanced / Olympiad)</option>
                                    </select>
                                </div>

                                {{-- Real-Time Math & Question Preview --}}
                                <div class="border rounded p-2 bg-light mb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong class="text-primary small" style="font-size: 11px;">
                                            <i class="fa fa-eye mr-1"></i> Live Render Preview:
                                        </strong>
                                        <span id="manual-preview-correct-badge" class="badge badge-success" style="font-size: 9.5px;">Correct: A</span>
                                    </div>
                                    <div id="manual-live-preview" class="p-2 bg-white border rounded" style="min-height: 60px; font-size: 11.5px; color: #0f172a;">
                                        <em>Type above to see live preview with rendered LaTeX formulas...</em>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <button type="submit" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; justify-content:center; height:34px;">
                                        <i class="fa fa-save"></i> Save Question to Bank
                                    </button>
                                    <button type="button" id="btn-save-and-add" class="dash-btn dash-btn-emerald" style="justify-content:center; height:34px;">
                                        <i class="fa fa-plus-circle"></i> Save &amp; Add Another
                                    </button>
                                    <button type="reset" class="dash-btn dash-btn-outline" style="justify-content:center; color:#334155 !important; border-color:#cbd5e1; height:30px;">
                                        <i class="fa fa-undo"></i> Reset
                                    </button>
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
                                <i class="fa fa-camera text-warning"></i> Upload or Paste Question Image
                            </h3>
                            <span class="badge badge-light" style="color: #002C54; font-size: 10px;">
                                <i class="fa fa-clipboard"></i> Ctrl+V Paste Supported
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
                                        <i class="fa fa-magic"></i> Extract with AI Agent
                                    </button>
                                    <button type="button" id="btn-clear-ai-image" class="dash-btn dash-btn-outline" style="color:#dc2626 !important; border-color:#cbd5e1;">
                                        <i class="fa fa-trash"></i> Clear
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
                                <i class="fa fa-check-circle-o text-warning"></i> AI-Extracted Question Review &amp; Edit
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

                                <div class="row">
                                    @foreach(['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'] as $num => $letter)
                                        @php $optIndex = (string)($num - 1); @endphp
                                        <div class="col-md-6 mb-2">
                                            <div class="option-input-card" id="ai-opt-card-{{ $num }}">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="option-label-pill">{{ $letter }}</span>
                                                        <strong style="font-size: 11px;">Option {{ $letter }}</strong>
                                                    </div>
                                                    <label class="correct-radio-label text-success">
                                                        <input type="radio" name="ai_single_correct" value="{{ $optIndex }}" class="ai-correct-radio">
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

                                <div class="pt-2 border-top d-flex align-items-center justify-content-between">
                                    <span class="text-muted small" style="font-size: 10.5px;">
                                        <i class="fa fa-info-circle mr-1"></i> Saving will add this question under the currently selected Class &amp; Subject.
                                    </span>
                                    <button type="button" id="btn-save-ai-single" class="dash-btn dash-btn-emerald" style="height: 32px; padding: 0 16px;">
                                        <i class="fa fa-check"></i> Approve &amp; Save Question
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
                        <i class="fa fa-file-pdf-o text-warning"></i> Upload PDF Question Paper for AI Bulk Extraction
                    </h3>
                    <span class="badge badge-light" style="color: #002C54; font-size: 10px;">
                        Extracts All Questions in Seconds
                    </span>
                </div>
                <div class="theme-card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="theme-form-group mb-2">
                                <label for="pdf_upload_input">Select PDF File <span class="text-danger">*</span> (Up to 20MB)</label>
                                <input type="file" id="pdf_upload_input" accept="application/pdf,.pdf" class="theme-form-control" style="height: 34px; padding: 4px;">
                                <div class="theme-hint">
                                    Works with previous year question papers, sample tests, assignment sheets, and model question papers.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="theme-form-group mb-2">
                                <label for="pdf_page_range">Page Range <span class="text-muted font-weight-normal">(Optional)</span></label>
                                <input type="text" id="pdf_page_range" class="theme-form-control" placeholder="All pages or e.g. 1-4">
                            </div>
                        </div>

                        <div class="col-md-3 text-right">
                            <button type="button" id="btn-start-pdf-extract" class="dash-btn" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; height:34px; width:100%; justify-content:center;">
                                <i class="fa fa-magic"></i> Extract Questions via AI
                            </button>
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
                            <i class="fa fa-database"></i> Import Selected Questions to Bank
                        </button>
                    </div>
                </div>

                {{-- Question List Container --}}
                <div id="bulk-questions-list"></div>

                {{-- Bottom Bulk Action Sticky Bar --}}
                <div class="p-2 bg-white border rounded d-flex align-items-center justify-content-between mt-2">
                    <span class="text-muted small" style="font-size: 11px;">
                        <i class="fa fa-info-circle mr-1"></i> All selected questions above will be saved with the active Target Class and Subject.
                    </span>
                    <button type="button" id="btn-bulk-import-save-bottom" class="dash-btn dash-btn-emerald" style="height: 32px; padding: 0 16px;">
                        <i class="fa fa-database"></i> Import Selected Questions to Bank
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
                    <i class="fa fa-magic mr-1 text-warning"></i> Google Gemini AI Agent Settings
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
                        Don't have a key? Get a free API key at <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-primary font-weight-bold">Google AI Studio <i class="fa fa-external-link"></i></a>
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

{{-- KaTeX & Auto-Render CDN for Dynamic Math Formula Rendering --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js"></script>

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
        });
    });

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
            if (!lastActiveTextarea) return;

            const start = lastActiveTextarea.selectionStart || 0;
            const end = lastActiveTextarea.selectionEnd || 0;
            const val = lastActiveTextarea.value;
            const insertText = ` $${math}$ `;

            lastActiveTextarea.value = val.substring(0, start) + insertText + val.substring(end);
            lastActiveTextarea.focus();
            lastActiveTextarea.selectionStart = lastActiveTextarea.selectionEnd = start + insertText.length;
            updateManualLivePreview();
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

    function updateManualLivePreview() {
        const previewEl = document.getElementById('manual-live-preview');
        const qText = document.getElementById('manual_question_name')?.value?.trim();
        const a1 = document.getElementById('manual_ans1')?.value?.trim();
        const a2 = document.getElementById('manual_ans2')?.value?.trim();
        const a3 = document.getElementById('manual_ans3')?.value?.trim();
        const a4 = document.getElementById('manual_ans4')?.value?.trim();
        const correctRadio = document.querySelector('input[name="correct_ans"]:checked');
        const correctBadge = document.getElementById('manual-preview-correct-badge');

        const optLetters = ['A', 'B', 'C', 'D'];
        const correctIdx = correctRadio ? parseInt(correctRadio.value, 10) : 0;
        if (correctBadge) {
            correctBadge.textContent = `Correct: Option ${optLetters[correctIdx] || 'A'}`;
        }

        // Highlight correct option card
        document.querySelectorAll('.option-input-card').forEach((card, idx) => {
            card.classList.toggle('correct-selected', idx === correctIdx);
        });

        if (!qText && !a1 && !a2 && !a3 && !a4) {
            if (previewEl) previewEl.innerHTML = '<em>Type above to see live preview with rendered LaTeX formulas...</em>';
            return;
        }

        let html = `<div class="font-weight-bold mb-2">${escapeHtml(qText || 'Question statement...')}</div>`;
        if (globalTypeSelect?.value === '1') {
            html += `<div class="row">
                <div class="col-6 mb-1"><div class="p-1 border rounded ${correctIdx === 0 ? 'bg-success text-white' : 'bg-white'}"><strong>(A)</strong> ${escapeHtml(a1 || '—')}</div></div>
                <div class="col-6 mb-1"><div class="p-1 border rounded ${correctIdx === 1 ? 'bg-success text-white' : 'bg-white'}"><strong>(B)</strong> ${escapeHtml(a2 || '—')}</div></div>
                <div class="col-6 mb-1"><div class="p-1 border rounded ${correctIdx === 2 ? 'bg-success text-white' : 'bg-white'}"><strong>(C)</strong> ${escapeHtml(a3 || '—')}</div></div>
                <div class="col-6 mb-1"><div class="p-1 border rounded ${correctIdx === 3 ? 'bg-success text-white' : 'bg-white'}"><strong>(D)</strong> ${escapeHtml(a4 || '—')}</div></div>
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
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    document.getElementById('btn-remove-manual-img')?.addEventListener('click', () => {
        if (manualImgInput) manualImgInput.value = '';
        document.getElementById('manual-image-preview-wrapper').classList.add('d-none');
    });

    // Manual form submission
    document.getElementById('btn-save-and-add')?.addEventListener('click', () => {
        document.getElementById('manual_submit_action').value = 'save_and_add';
        document.getElementById('form-manual-question').submit();
    });

    document.getElementById('form-manual-question')?.addEventListener('submit', function (e) {
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
    });

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
        loadedImageBlob = file;
        const reader = new FileReader();
        reader.onload = function (e) {
            loadedImageBase64 = e.target.result;
            previewImg.src = loadedImageBase64;
            previewCard.classList.remove('d-none');
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
        .then(res => res.json())
        .then(data => {
            extractBtn.disabled = false;
            statusEl.classList.add('d-none');

            if (data.success && data.data) {
                populateAiSingleForm(data.data);
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

    function populateAiSingleForm(q) {
        document.getElementById('ai-single-empty-placeholder').classList.add('d-none');
        const form = document.getElementById('form-ai-single-save');
        form.classList.remove('d-none');

        document.getElementById('ai_single_q_name').value = q.name || '';
        document.getElementById('ai_single_ans1').value = q.ans1 || '';
        document.getElementById('ai_single_ans2').value = q.ans2 || '';
        document.getElementById('ai_single_ans3').value = q.ans3 || '';
        document.getElementById('ai_single_ans4').value = q.ans4 || '';
        document.getElementById('ai_single_explanation').value = q.explanation || '';
        document.getElementById('ai_single_difficulty').value = q.difficulty_level || 'Medium';
        document.getElementById('ai_single_topic').value = q.topic || '';
        document.getElementById('ai_single_image_path').value = q.image_path || '';

        // Select correct radio
        const correctVal = String(q.correct_ans ?? '0');
        const targetRadio = document.querySelector(`input[name="ai_single_correct"][value="${correctVal}"]`);
        if (targetRadio) targetRadio.checked = true;

        document.getElementById('ai-single-model-badge').classList.remove('d-none');
        form.scrollIntoView({ behavior: 'smooth' });
    }

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
                this.innerHTML = '<i class="fa fa-check"></i> Approve & Save Question';
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            this.disabled = false;
            this.innerHTML = '<i class="fa fa-check"></i> Approve & Save Question';
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

        const range = document.getElementById('pdf_page_range')?.value;
        if (range) formData.append('extra_instructions', `Extract questions from pages: ${range}`);

        fetch('{{ url("question/ai-extract-pdf") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            statusEl.classList.add('d-none');

            if (data.success && data.data && Array.isArray(data.data.questions)) {
                extractedQuestionsList = data.data.questions;
                renderBulkQuestions(extractedQuestionsList);
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

    function renderBulkQuestions(questions) {
        const workspace = document.getElementById('bulk-questions-workspace');
        const container = document.getElementById('bulk-questions-list');
        const totalBadge = document.getElementById('bulk-total-count');
        const counterText = document.getElementById('bulk-select-counter');

        workspace.classList.remove('d-none');
        totalBadge.textContent = `${questions.length} Questions Detected`;
        counterText.textContent = `Select All Questions (${questions.length} Selected)`;

        let html = '';
        questions.forEach((q, idx) => {
            const qNum = idx + 1;
            const correctOpt = String(q.correct_ans ?? '0');

            html += `
            <div class="bulk-q-item" id="bulk-card-${idx}">
                <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <label class="mb-0 font-weight-bold text-primary" style="font-size: 12px; cursor: pointer;">
                        <input type="checkbox" class="bulk-q-checkbox mr-1" data-index="${idx}" checked>
                        Question #${qNum}
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-light border" style="font-size: 10px;">Difficulty: ${q.difficulty_level || 'Medium'}</span>
                        <button type="button" class="btn btn-xs btn-outline-danger btn-bulk-remove" data-index="${idx}" title="Remove question">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="theme-form-group mb-2">
                    <textarea class="theme-form-control bulk-field-name" data-index="${idx}" rows="2" placeholder="Question statement">${escapeHtml(q.name || '')}</textarea>
                </div>

                <div class="row">
                    ${['1','2','3','4'].map((num, oIdx) => {
                        const optKey = 'ans' + num;
                        const letter = ['A','B','C','D'][oIdx];
                        const isCorrect = (correctOpt === String(oIdx));
                        return `
                        <div class="col-md-6 mb-1">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold" style="font-size: 10.5px;">
                                        <input type="radio" name="bulk_correct_${idx}" value="${oIdx}" class="bulk-field-correct mr-1" data-index="${idx}" ${isCorrect ? 'checked' : ''}>
                                        ${letter}
                                    </span>
                                </div>
                                <input type="text" class="form-control form-control-sm bulk-field-${optKey}" data-index="${idx}" value="${escapeHtml(q[optKey] || '')}" placeholder="Option ${letter}">
                            </div>
                        </div>`;
                    }).join('')}
                </div>

                ${q.explanation ? `
                <div class="mt-1 text-muted small" style="font-size: 10px;">
                    <strong>Explanation:</strong> ${escapeHtml(q.explanation)}
                </div>` : ''}
            </div>`;
        });

        container.innerHTML = html;
        renderKaTeXIn(container);

        // Bind bulk checkboxes
        container.querySelectorAll('.bulk-q-checkbox').forEach(cb => {
            cb.addEventListener('change', function () {
                const itemCard = document.getElementById(`bulk-card-${this.dataset.index}`);
                itemCard.classList.toggle('excluded', !this.checked);
                updateBulkCounter();
            });
        });

        // Bind delete buttons
        container.querySelectorAll('.btn-bulk-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index, 10);
                document.getElementById(`bulk-card-${idx}`)?.remove();
                delete extractedQuestionsList[idx];
                updateBulkCounter();
            });
        });

        workspace.scrollIntoView({ behavior: 'smooth' });
    }

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
            const correctRadio = card.querySelector('.bulk-field-correct:checked');

            if (name) {
                preparedQuestions.push({
                    name: name,
                    question_type_id: globalTypeSelect.value,
                    ans1: a1,
                    ans2: a2,
                    ans3: a3,
                    ans4: a4,
                    correct_ans: correctRadio ? correctRadio.value : '0',
                    difficulty_level: 'Medium'
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
                if (btn1) { btn1.disabled = false; btn1.innerHTML = '<i class="fa fa-database"></i> Import Selected Questions to Bank'; }
                if (btn2) { btn2.disabled = false; btn2.innerHTML = '<i class="fa fa-database"></i> Import Selected Questions to Bank'; }
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
            if (btn1) { btn1.disabled = false; btn1.innerHTML = '<i class="fa fa-database"></i> Import Selected Questions to Bank'; }
            if (btn2) { btn2.disabled = false; btn2.innerHTML = '<i class="fa fa-database"></i> Import Selected Questions to Bank'; }
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

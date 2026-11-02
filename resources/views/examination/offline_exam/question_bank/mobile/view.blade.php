@php
    $classType = $classType ?? Helper::classType();
    $allSubjects = $allSubjects ?? Helper::getSubject();
    $stats = (array)($stats ?? [
        'total' => 0,
        'mcq' => 0,
        'descriptive' => 0,
        'grouped' => 0,
        'diagram' => 0,
        'ai_extracted' => 0,
    ]);
    $search = $search ?? [];
    $permission = Helper::permissioncheck(8);

    $hasActiveFilters = !empty($search['class_type_id']) || !empty($search['subject_id']) || !empty($search['question_type_id']) || !empty($search['difficulty_level']) || !empty($search['format_filter']) || !empty($search['name']);
    
    $totalCount = $data->total() ?? 0;
    $currentPage = $data->currentPage() ?? 1;
    $lastPage = $data->lastPage() ?? 1;
    $perPage = $data->perPage() ?? 25;
    $firstItem = $data->firstItem() ?? 0;
    $lastItem = $data->lastItem() ?? 0;
@endphp

@extends('layout.mobile_app')

@section('title', 'Question Bank')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE QUESTION BANK (GOLD STANDARD ERP MOBILE)
   Self-contained, robust CSS strictly adhering to Arise Mobile App Design System.
   ========================================================================== */

/* 1. Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
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

/* 4 Metrics Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
    text-decoration: none !important;
    display: block;
    transition: background .15s ease;
}
.mob-metric-box.active {
    background: rgba(255, 255, 255, 0.22);
    border-color: #38bdf8;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 1px;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-mcq { color: #4ade80; }
.mob-metric-val.val-groups { color: #38bdf8; }
.mob-metric-val.val-diagram { color: #fbbf24; }

/* Fast Action Bar in Hero */
.mob-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-act-btn {
    height: 32px;
    padding: 0 10px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    box-sizing: border-box;
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 2;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
    flex: 1.2;
    position: relative;
}
.mob-act-btn-reset {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.4);
    width: 36px;
    padding: 0;
    flex: none;
}
.mob-act-btn:active {
    transform: scale(0.96);
}
.filter-active-dot {
    position: absolute;
    top: 5px;
    right: 6px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 6px #38bdf8;
}

/* 2. Compact Search & Horizontal Filter Chips */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11.5px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
    -webkit-appearance: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}

/* Chips Slider */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
    transition: all .12s ease;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}
.mob-chip-count {
    font-size: 8.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-chip.active .mob-chip-count {
    background: rgba(255, 255, 255, 0.22);
}

/* 3. Question Cards Feed */
.mob-questions-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 75px; /* Offset for floating bottom pagination bar */
}
.mob-q-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    border-left: 3.5px solid #002C54;
}
.mob-q-card.type-mcq { border-left-color: #0284c7; }
.mob-q-card.type-descriptive { border-left-color: #f59e0b; }
.mob-q-card.type-grouped { border-left-color: #8b5cf6; }

.mob-q-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-q-meta-left {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    flex: 1;
    min-width: 0;
}
.mob-q-num-pill {
    background: #002C54;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 3px;
    flex-shrink: 0;
}
.mob-chip-class {
    background: #e0f2fe;
    color: #0369a1;
    font-size: 9px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #bae6fd;
}
.mob-chip-subject {
    background: #f1f5f9;
    color: #334155;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.mob-q-meta-right {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.mob-badge-diff {
    font-size: 8.5px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 3px;
    text-transform: uppercase;
}
.diff-easy { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.diff-medium { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.diff-hard { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.mob-badge-ai {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
    font-size: 8.5px;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 3px;
}

/* Direction Set Strip */
.mob-direction-strip {
    background: #f0f9ff;
    border-left: 2.5px solid #0284c7;
    padding: 4px 7px;
    border-radius: 0 3px 3px 0;
    font-size: 10px;
    color: #0369a1;
    font-weight: 700;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Diagram Thumbnail */
.mob-q-diagram-wrap {
    text-align: center;
    margin-bottom: 7px;
    background: #f8fafc;
    padding: 6px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
.mob-q-diagram-img {
    max-height: 110px;
    max-width: 100%;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    background: #ffffff;
}
.mob-diagram-hint {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 700;
    margin-top: 3px;
}

/* Question Statement */
.mob-q-statement {
    font-size: 12px;
    font-weight: 600;
    line-height: 1.45;
    color: #0f172a;
    margin-bottom: 8px;
    word-break: break-word;
}

/* Options Stacked / Grid */
.mob-options-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 8px;
}
.mob-opt-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 5px 8px;
    font-size: 11px;
    color: #334155;
    line-height: 1.35;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}
.mob-opt-card.is-correct {
    background: #ecfdf5;
    border-color: #86efac;
    color: #065f46;
    font-weight: 700;
}
.mob-opt-letter {
    font-weight: 800;
    flex-shrink: 0;
    font-size: 10.5px;
}
.mob-opt-card.is-correct .mob-opt-letter {
    color: #059669;
}
.mob-opt-text {
    flex: 1;
    min-width: 0;
    word-break: break-word;
}

/* Solution Snippet */
.mob-sol-box {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    padding: 5px 8px;
    border-radius: 0 3px 3px 0;
    font-size: 10.5px;
    color: #78350f;
    margin-bottom: 8px;
    line-height: 1.4;
}

/* Card Actions Footer */
.mob-q-card-actions {
    display: flex;
    gap: 5px;
    border-top: 1px solid #f1f5f9;
    padding-top: 7px;
    align-items: center;
}
.mob-card-act-btn {
    height: 30px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all .1s ease;
    box-sizing: border-box;
    padding: 0 8px;
}
.mob-card-act-btn:active {
    transform: scale(0.96);
}
.mob-card-act-btn.btn-detail {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #2563eb;
    flex: 2;
}
.mob-card-act-btn.btn-edit {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
    flex: 1.2;
}
.mob-card-act-btn.btn-delete {
    background: #fff5f5;
    border-color: #fecaca;
    color: #dc2626;
    width: 32px;
    padding: 0;
    flex: none;
}

/* 4. Compact Fixed Bottom Pagination Bar (Matches Admission View) */
.mob-pagination-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.mob-pagination-info {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-pagination-info b {
    color: #002C54;
}
.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-per-page-select {
    height: 28px;
    padding: 0 4px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-page-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-page-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.mob-page-btn:not(.disabled):active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: scale(0.94);
}

/* 5. Mobile Native Bottom Sheets */
.mob-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 6px 6px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-bottom-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 6px 6px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-select, .mob-form-input {
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
    box-sizing: border-box;
}
.mob-form-select:focus, .mob-form-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.btn-sheet-apply {
    flex: 2;
    height: 33px;
    background: #0284c7;
    color: #ffffff !important;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-sheet-reset {
    flex: 1;
    height: 33px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569 !important;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    cursor: pointer;
}
.btn-sheet-apply:active, .btn-sheet-reset:active {
    transform: scale(0.97);
}

/* Lightbox Modal */
.mob-lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.92);
    z-index: 2100;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 12px;
}
.mob-lightbox-overlay.show {
    display: flex;
}
.mob-lightbox-close {
    position: absolute;
    top: 12px;
    right: 12px;
    color: #ffffff;
    font-size: 26px;
    background: rgba(255, 255, 255, 0.15);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.mob-lightbox-img {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 4px;
    background: #ffffff;
}

/* Empty State */
.mob-empty-state {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    margin-bottom: 12px;
}
.mob-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto 10px;
}
.mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 12px;
}
.btn-empty-reset {
    height: 30px;
    padding: 0 12px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
}

/* KaTeX tweaks for mobile */
.katex {
    font-size: 1.05em !important;
}
.katex-display {
    margin: 4px 0 !important;
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 2px;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-question-circle-o text-primary"></i> Question Bank
        </div>
        <div class="mob-session-pill">
            Session {{ Session::get('session_name') ?? date('Y') }}
        </div>
    </div>

    {{-- 4 Metric Glance Grid --}}
    <div class="mob-metrics-grid">
        <a href="{{ url('view/question?layout=mobile') }}" class="mob-metric-box {{ empty($search['format_filter']) && empty($search['question_type_id']) ? 'active' : '' }}">
            <span class="mob-metric-tag">Total</span>
            <div class="mob-metric-val">{{ $stats['total'] ?? 0 }}</div>
        </a>
        <a href="{{ url('view/question?layout=mobile&question_type_id=1') }}" class="mob-metric-box {{ ($search['question_type_id'] ?? '') == '1' ? 'active' : '' }}">
            <span class="mob-metric-tag">MCQ</span>
            <div class="mob-metric-val val-mcq">{{ $stats['mcq'] ?? 0 }}</div>
        </a>
        <a href="{{ url('view/question?layout=mobile&format_filter=grouped') }}" class="mob-metric-box {{ ($search['format_filter'] ?? '') == 'grouped' ? 'active' : '' }}">
            <span class="mob-metric-tag">Groups</span>
            <div class="mob-metric-val val-groups">{{ $stats['grouped'] ?? 0 }}</div>
        </a>
        <a href="{{ url('view/question?layout=mobile&format_filter=diagram') }}" class="mob-metric-box {{ ($search['format_filter'] ?? '') == 'diagram' ? 'active' : '' }}">
            <span class="mob-metric-tag">Diagram</span>
            <div class="mob-metric-val val-diagram">{{ $stats['diagram'] ?? 0 }}</div>
        </a>
    </div>

    {{-- Fast Action Bar --}}
    <div class="mob-actions-bar">
        @if($permission->add ?? true)
            <a href="{{ url('add/question') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-plus"></i> + Add Question
            </a>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnOpenFilterSheet">
            <i class="fa fa-sliders"></i> Filters
            @if($hasActiveFilters)
                <span class="filter-active-dot"></span>
            @endif
        </button>
        @if($hasActiveFilters)
            <a href="{{ url('view/question?layout=mobile') }}" class="mob-act-btn mob-act-btn-reset" title="Reset Filters">
                <i class="fa fa-refresh"></i>
            </a>
        @endif
    </div>
</div>

{{-- 2. Compact Search & Horizontal Filter Chips Toolbar --}}
<div class="mob-search-toolbar">
    <div class="mob-search-input-wrap">
        <i class="fa fa-search search-icon"></i>
        <input type="text" class="mob-search-input" id="mobQuestionSearchInput" placeholder="Search questions by text or keyword..." value="{{ $search['name'] ?? '' }}" autocomplete="off">
        <i class="fa fa-times-circle mob-search-clear" id="mobSearchClearBtn" style="{{ !empty($search['name']) ? 'display:block;' : '' }}"></i>
    </div>

    {{-- Horizontal Scrollable Filter Chips --}}
    <div class="mob-chips-scroll">
        <a href="{{ url('view/question?layout=mobile') }}" class="mob-chip {{ empty($search['format_filter']) && empty($search['question_type_id']) ? 'active' : '' }}">
            All <span class="mob-chip-count">{{ $stats['total'] ?? 0 }}</span>
        </a>
        <a href="{{ url('view/question?layout=mobile&question_type_id=1') }}" class="mob-chip {{ ($search['question_type_id'] ?? '') == '1' ? 'active' : '' }}">
            MCQ <span class="mob-chip-count">{{ $stats['mcq'] ?? 0 }}</span>
        </a>
        <a href="{{ url('view/question?layout=mobile&question_type_id=2') }}" class="mob-chip {{ ($search['question_type_id'] ?? '') == '2' ? 'active' : '' }}">
            Descriptive <span class="mob-chip-count">{{ $stats['descriptive'] ?? 0 }}</span>
        </a>
        <a href="{{ url('view/question?layout=mobile&format_filter=grouped') }}" class="mob-chip {{ ($search['format_filter'] ?? '') == 'grouped' ? 'active' : '' }}">
            Group Sets <span class="mob-chip-count">{{ $stats['grouped'] ?? 0 }}</span>
        </a>
        <a href="{{ url('view/question?layout=mobile&format_filter=diagram') }}" class="mob-chip {{ ($search['format_filter'] ?? '') == 'diagram' ? 'active' : '' }}">
            With Diagram <span class="mob-chip-count">{{ $stats['diagram'] ?? 0 }}</span>
        </a>
        <a href="{{ url('view/question?layout=mobile&format_filter=ai_extracted') }}" class="mob-chip {{ ($search['format_filter'] ?? '') == 'ai_extracted' ? 'active' : '' }}">
            AI Extracted <span class="mob-chip-count">{{ $stats['ai_extracted'] ?? 0 }}</span>
        </a>
    </div>
</div>

{{-- 3. Question Cards Feed --}}
<div class="mob-questions-feed" id="questionCardFeed">
    @forelse($data as $idx => $item)
        @php
            $rowNum = ($firstItem ?: 1) + $idx;
            $correctOpt = (string)($item->correct_ans ?? '0');
            $hasImage = !empty($item->image);
            $isGrouped = !empty($item->group_id);
            $isAi = !empty($item->ai_extracted);
            $diff = $item->difficulty_level ?: 'Medium';
            $diffClass = $diff === 'Easy' ? 'diff-easy' : ($diff === 'Hard' ? 'diff-hard' : 'diff-medium');

            $cardTypeClass = match(true) {
                $isGrouped => 'type-grouped',
                $item->question_type_id == 1 => 'type-mcq',
                default => 'type-descriptive'
            };
        @endphp

        <div class="mob-q-card {{ $cardTypeClass }}" id="mob-q-card-{{ $item->id }}">
            
            {{-- Header: Q Number, Class, Subject, Difficulty --}}
            <div class="mob-q-card-header">
                <div class="mob-q-meta-left">
                    <span class="mob-q-num-pill">#{{ $rowNum }}</span>
                    <span class="mob-chip-class">{{ $item->class_name ?? 'Class —' }}</span>
                    <span class="mob-chip-subject">{{ $item->subject_name ?? 'General' }}</span>
                </div>
                <div class="mob-q-meta-right">
                    <span class="mob-badge-diff {{ $diffClass }}">{{ $diff }}</span>
                    @if($isAi)
                        <span class="mob-badge-ai"><i class="fa fa-magic"></i> AI</span>
                    @endif
                </div>
            </div>

            {{-- Direction Set Alert (if Grouped) --}}
            @if($isGrouped)
                <div class="mob-direction-strip">
                    <i class="fa fa-sitemap"></i> Direction Set: {{ $item->group_id }} @if(!empty($item->sub_question_no))(Q{{ $item->sub_question_no }})@endif
                </div>
            @endif

            {{-- Attached Diagram Thumbnail --}}
            @if($hasImage)
                <div class="mob-q-diagram-wrap">
                    <img src="{{ asset($item->image) }}" alt="Diagram" class="mob-q-diagram-img mob-zoom-trigger" data-img-url="{{ asset($item->image) }}">
                    <div class="mob-diagram-hint">
                        <i class="fa fa-search-plus mr-1"></i> Tap diagram to zoom
                    </div>
                </div>
            @endif

            {{-- Question Statement with KaTeX --}}
            <div class="mob-q-statement katex-render-target">
                {!! Helper::cleanHtmlFormat($item->name ?? '') !!}
            </div>

            {{-- Options List (for MCQs) --}}
            @if($item->question_type_id == 1)
                @php
                    $opts = [
                        'A' => $item->ans1,
                        'B' => $item->ans2,
                        'C' => $item->ans3,
                        'D' => $item->ans4,
                    ];
                    if (!empty($item->ans5) || $correctOpt === '4') {
                        $opts['E'] = $item->ans5;
                    }
                    $letters = array_keys($opts);
                @endphp
                <div class="mob-options-list">
                    @foreach($opts as $letter => $val)
                        @php
                            $oIndex = array_search($letter, $letters);
                            $isCorr = (string)$oIndex === $correctOpt;
                        @endphp
                        <div class="mob-opt-card {{ $isCorr ? 'is-correct' : '' }}">
                            <span class="mob-opt-letter">({{ $letter }})</span>
                            <span class="mob-opt-text katex-render-target">{!! Helper::cleanHtmlFormat($val ?? '—') !!}</span>
                            @if($isCorr)
                                <i class="fa fa-check-circle text-success" style="font-size:12px; margin-left:auto;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Solution Snippet (if available) --}}
            @if(!empty($item->explanation))
                <div class="mob-sol-box">
                    <strong style="display:block; margin-bottom:2px;"><i class="fa fa-lightbulb-o mr-1"></i> Solution:</strong>
                    <div class="katex-render-target">{!! Str::limit(strip_tags(Helper::cleanHtmlFormat($item->explanation)), 140) !!}</div>
                </div>
            @endif

            {{-- Card Actions Footer --}}
            <div class="mob-q-card-actions">
                <button type="button" 
                        class="mob-card-act-btn btn-detail mob-open-detail"
                        data-question="{{ htmlspecialchars($item->name ?? '', ENT_QUOTES) }}"
                        data-subject="{{ $item->subject_name ?? '' }}"
                        data-class="{{ $item->class_name ?? '' }}"
                        data-type="{{ $item->question_type_id == 1 ? 'Objective (MCQ)' : 'Descriptive' }}"
                        data-difficulty="{{ $diff }}"
                        data-opt1="{{ htmlspecialchars($item->ans1 ?? '', ENT_QUOTES) }}"
                        data-opt2="{{ htmlspecialchars($item->ans2 ?? '', ENT_QUOTES) }}"
                        data-opt3="{{ htmlspecialchars($item->ans3 ?? '', ENT_QUOTES) }}"
                        data-opt4="{{ htmlspecialchars($item->ans4 ?? '', ENT_QUOTES) }}"
                        data-opt5="{{ htmlspecialchars($item->ans5 ?? '', ENT_QUOTES) }}"
                        data-correct_ans="{{ $correctOpt }}"
                        data-group_id="{{ $item->group_id ?? '' }}"
                        data-sub_no="{{ $item->sub_question_no ?? '' }}"
                        data-direction="{{ htmlspecialchars($item->direction ?? '', ENT_QUOTES) }}"
                        data-direction_table="{{ htmlspecialchars($item->direction_table ?? '', ENT_QUOTES) }}"
                        data-image="{{ $hasImage ? asset($item->image) : '' }}"
                        data-explanation="{{ htmlspecialchars($item->explanation ?? '', ENT_QUOTES) }}">
                    <i class="fa fa-eye"></i> Full Details
                </button>

                @if($permission->edit ?? true)
                    <a href="{{ url('edit/question/' . $item->id) }}" class="mob-card-act-btn btn-edit">
                        <i class="fa fa-pencil text-primary"></i> Edit
                    </a>
                @endif

                @if($permission->delete ?? true)
                    <button type="button" class="mob-card-act-btn btn-delete mob-open-delete" data-id="{{ $item->id }}" data-label="Question #{{ $rowNum }}">
                        <i class="fa fa-trash-o"></i>
                    </button>
                @endif
            </div>

        </div>
    @empty
        <div class="mob-empty-state">
            <div class="mob-empty-icon">
                <i class="fa fa-question-circle-o"></i>
            </div>
            <div class="mob-empty-title">No Questions Found</div>
            <div class="mob-empty-desc">There are no questions matching your active filters or search terms.</div>
            <a href="{{ url('view/question?layout=mobile') }}" class="btn-empty-reset">
                <i class="fa fa-refresh mr-1"></i> Reset All Filters
            </a>
        </div>
    @endforelse
</div>

{{-- 4. Compact Fixed Bottom Pagination Bar (Matches Admission View) --}}
@if($totalCount > 0)
    <div class="mob-pagination-bar" id="qbPaginationBar">
        <div class="mob-pagination-info">
            Showing <b>{{ $firstItem }}</b> - <b>{{ $lastItem }}</b> of <b>{{ $totalCount }}</b>
        </div>
        <div class="mob-pagination-btns">
            <select class="mob-per-page-select" id="mobPerPageSelect">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
            <a href="{{ $data->previousPageUrl() ?: 'javascript:void(0);' }}" class="mob-page-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" title="Previous Page">
                <i class="fa fa-chevron-left"></i>
            </a>
            <span style="font-size: 10px; font-weight: 800; color: #002C54; padding: 0 4px;">{{ $currentPage }}/{{ $lastPage }}</span>
            <a href="{{ $data->nextPageUrl() ?: 'javascript:void(0);' }}" class="mob-page-btn {{ $currentPage >= $lastPage ? 'disabled' : '' }}" title="Next Page">
                <i class="fa fa-chevron-right"></i>
            </a>
        </div>
    </div>
@endif

{{-- 5. Filter Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="filterSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="filterBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders text-primary"></i> Filter Question Bank
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>
    <form id="mobFilterForm" action="{{ url('view/question') }}" method="GET">
        <input type="hidden" name="layout" value="mobile">
        <input type="hidden" name="name" id="mobFilterNameInput" value="{{ $search['name'] ?? '' }}">
        <input type="hidden" name="per_page" id="mobFilterPerPageInput" value="{{ $perPage }}">

        <div class="mob-sheet-body">
            <div class="mob-form-grid-2">
                <div>
                    <label class="mob-form-label">Class</label>
                    <select class="mob-form-select" name="class_type_id" id="sheetFilterClass">
                        <option value="">All Classes</option>
                        @if(!empty($classType))
                            @foreach($classType as $type)
                                <option value="{{ $type->id }}" {{ ($type->id == ($search['class_type_id'] ?? '')) ? 'selected' : '' }}>
                                    {{ $type->name ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="mob-form-label">Subject</label>
                    <select class="mob-form-select" name="subject_id" id="sheetFilterSubject">
                        <option value="">All Subjects</option>
                        @if(!empty($allSubjects))
                            @foreach($allSubjects as $sub)
                                <option value="{{ $sub->id }}" {{ ($sub->id == ($search['subject_id'] ?? '')) ? 'selected' : '' }}>
                                    {{ $sub->name ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="mob-form-grid-2">
                <div>
                    <label class="mob-form-label">Question Type</label>
                    <select class="mob-form-select" name="question_type_id">
                        <option value="">All Types</option>
                        <option value="1" {{ (($search['question_type_id'] ?? '') == '1') ? 'selected' : '' }}>Objective (MCQ)</option>
                        <option value="2" {{ (($search['question_type_id'] ?? '') == '2') ? 'selected' : '' }}>Descriptive</option>
                    </select>
                </div>
                <div>
                    <label class="mob-form-label">Difficulty</label>
                    <select class="mob-form-select" name="difficulty_level">
                        <option value="">All Difficulties</option>
                        <option value="Easy" {{ (($search['difficulty_level'] ?? '') == 'Easy') ? 'selected' : '' }}>Easy</option>
                        <option value="Medium" {{ (($search['difficulty_level'] ?? '') == 'Medium') ? 'selected' : '' }}>Medium</option>
                        <option value="Hard" {{ (($search['difficulty_level'] ?? '') == 'Hard') ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Format / Nature</label>
                <select class="mob-form-select" name="format_filter">
                    <option value="">All Questions</option>
                    <option value="grouped" {{ (($search['format_filter'] ?? '') == 'grouped') ? 'selected' : '' }}>Direction / Grouped Sets</option>
                    <option value="standalone" {{ (($search['format_filter'] ?? '') == 'standalone') ? 'selected' : '' }}>Standalone Only</option>
                    <option value="diagram" {{ (($search['format_filter'] ?? '') == 'diagram') ? 'selected' : '' }}>With Diagrams / Graphs</option>
                    <option value="ai_extracted" {{ (($search['format_filter'] ?? '') == 'ai_extracted') ? 'selected' : '' }}>AI Extracted</option>
                </select>
            </div>
        </div>
        <div class="mob-sheet-footer">
            <a href="{{ url('view/question?layout=mobile') }}" class="btn-sheet-reset">Reset</a>
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

{{-- 6. Question Details Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="detailSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="detailBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-info-circle text-primary"></i> Question Full View
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseDetailSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        {{-- Metadata Badges --}}
        <div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap; padding-bottom:8px; margin-bottom:8px; border-bottom:1px solid #f1f5f9;">
            <span class="mob-chip-class" id="detClass">-</span>
            <span class="mob-chip-subject" id="detSubject">-</span>
            <span class="mob-q-num-pill" id="detType" style="background:#0284c7;">-</span>
            <span class="mob-badge-diff diff-medium" id="detDifficulty">-</span>
        </div>

        {{-- Direction / Passage Alert --}}
        <div id="detDirectionBox" style="background:#f0f9ff; border-left:3px solid #0284c7; padding:8px 10px; border-radius:0 4px 4px 0; margin-bottom:10px; display:none;">
            <strong style="color:#0369a1; font-size:11px; display:block; margin-bottom:3px;">
                <i class="fa fa-sitemap mr-1"></i> Direction / Passage:
            </strong>
            <div id="detDirectionText" style="font-size:11.5px; line-height:1.45; color:#1e293b;"></div>
            <div id="detDirectionTable" style="margin-top:6px; overflow-x:auto;"></div>
        </div>

        {{-- Attached Diagram --}}
        <div id="detImageBox" style="text-align:center; margin-bottom:10px; display:none;">
            <img id="detImage" src="" alt="Diagram" class="mob-zoom-trigger" style="max-height:180px; max-width:100%; border-radius:4px; border:1px solid #cbd5e1; cursor:pointer;">
        </div>

        {{-- Question Statement --}}
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:10px; margin-bottom:10px;">
            <span style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; display:block; margin-bottom:4px;">Question Statement:</span>
            <div id="detQuestionText" style="font-size:12.5px; font-weight:700; color:#0f172a; line-height:1.5;"></div>
        </div>

        {{-- Options --}}
        <div id="detOptionsWrap" style="margin-bottom:10px;">
            <span style="font-size:10px; font-weight:700; color:#64748b; text-transform:uppercase; display:block; margin-bottom:5px;">Options:</span>
            <div class="mob-options-list" id="detOptionsList">
                <div class="mob-opt-card" id="detOpt0">
                    <span class="mob-opt-letter">(A)</span>
                    <span class="mob-opt-text" id="detAns1"></span>
                </div>
                <div class="mob-opt-card" id="detOpt1">
                    <span class="mob-opt-letter">(B)</span>
                    <span class="mob-opt-text" id="detAns2"></span>
                </div>
                <div class="mob-opt-card" id="detOpt2">
                    <span class="mob-opt-letter">(C)</span>
                    <span class="mob-opt-text" id="detAns3"></span>
                </div>
                <div class="mob-opt-card" id="detOpt3">
                    <span class="mob-opt-letter">(D)</span>
                    <span class="mob-opt-text" id="detAns4"></span>
                </div>
                <div class="mob-opt-card" id="detOpt4" style="display:none;">
                    <span class="mob-opt-letter">(E)</span>
                    <span class="mob-opt-text" id="detAns5"></span>
                </div>
            </div>
        </div>

        {{-- Solution --}}
        <div id="detSolBox" class="mob-sol-box" style="display:none;">
            <strong style="display:block; margin-bottom:2px;"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution / Explanation:</strong>
            <div id="detSolText" style="font-size:11px; line-height:1.45;"></div>
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn-sheet-reset" id="btnCancelDetail" style="flex:1;">Close</button>
    </div>
</div>

{{-- 7. Lightbox Zoom Overlay --}}
<div class="mob-lightbox-overlay" id="lightboxOverlay">
    <div class="mob-lightbox-close" id="btnCloseLightbox">&times;</div>
    <img id="lightboxImg" src="" alt="Enlarged Diagram" class="mob-lightbox-img">
</div>

{{-- 8. Delete Confirmation Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="deleteSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="deleteBottomSheet">
    <div class="mob-sheet-header" style="background: linear-gradient(135deg, #450a0a 0%, #991b1b 100%);">
        <div class="mob-sheet-title">
            <i class="fa fa-trash-o text-white"></i> Delete Question
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseDeleteSheet">&times;</button>
    </div>
    <form action="{{ url('delete/question') }}" method="POST">
        @csrf
        <input type="hidden" name="delete_id" id="deleteQuestionId">
        <div class="mob-sheet-body text-center" style="padding: 20px 16px;">
            <div style="width:44px; height:44px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 10px;">
                <i class="fa fa-trash-o"></i>
            </div>
            <h6 style="font-size:13.5px; font-weight:800; color:#1e293b; margin-bottom:4px;">Permanently Delete Question?</h6>
            <p style="font-size:11.5px; color:#64748b; margin-bottom:0;" id="deleteQuestionPrompt">
                Are you sure you want to permanently delete this question? This action cannot be undone.
            </p>
        </div>
        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelDelete">Cancel</button>
            <button type="submit" class="btn-sheet-apply" style="background:#dc2626;">
                <i class="fa fa-trash mr-1"></i> Confirm Delete
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script>
$(document).ready(function() {

    // --- 1. Bottom Sheet Handlers ---
    function openSheet(sheetId, backdropId) {
        $(backdropId).addClass('show');
        $(sheetId).addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeSheet(sheetId, backdropId) {
        $(backdropId).removeClass('show');
        $(sheetId).removeClass('show');
        $('body').css('overflow', '');
    }

    // Filter Sheet
    $('#btnOpenFilterSheet').on('click', function() {
        openSheet('#filterBottomSheet', '#filterSheetBackdrop');
    });
    $('#btnCloseFilterSheet, #filterSheetBackdrop').on('click', function() {
        closeSheet('#filterBottomSheet', '#filterSheetBackdrop');
    });

    // Per Page Change
    $('#mobPerPageSelect').on('change', function() {
        const perPage = $(this).val();
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    });

    // Quick Search on Enter
    $('#mobQuestionSearchInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const val = $(this).val().trim();
            const url = new URL(window.location.href);
            if (val) {
                url.searchParams.set('name', val);
            } else {
                url.searchParams.delete('name');
            }
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }
    });

    $('#mobQuestionSearchInput').on('input', function() {
        const val = $(this).val();
        $('#mobSearchClearBtn').toggle(val.length > 0);
        $('#mobFilterNameInput').val(val);
    });

    $('#mobSearchClearBtn').on('click', function() {
        $('#mobQuestionSearchInput').val('');
        $('#mobFilterNameInput').val('');
        const url = new URL(window.location.href);
        url.searchParams.delete('name');
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    });

    // Dynamic Class -> Subject linking
    $('#sheetFilterClass').on('change', function() {
        const classId = $(this).val();
        if (!classId) return;

        $.ajax({
            url: "{{ url('class_type_search') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                class_type_id: classId
            },
            success: function(res) {
                let options = '<option value="">All Subjects</option>';
                if (res && res.length) {
                    res.forEach(function(sub) {
                        options += `<option value="${sub.id}">${sub.name}</option>`;
                    });
                }
                $('#sheetFilterSubject').html(options);
            }
        });
    });

    // Format rich text helper
    function formatRichExamContent(text) {
        if (!text) return '';
        return String(text)
            .replace(/&lt;(\/?(?:br|b|strong|i|em|u|sub|sup|table|thead|tbody|tr|th|td|p|div|span|ul|ol|li|hr|h[1-6])(?:\s+[^&>]*)?)&gt;/gi, '<$1>')
            .replace(/<br\s*\/?>\s*\r?\n?/gi, '<br>')
            .replace(/([^>])\r?\n/g, '$1<br>');
    }

    // KaTeX Text Walker
    function renderKaTeXIn(container) {
        if (!container || !window.katex) return;
        const walker = document.createTreeWalker(container, NodeFilter.SHOW_TEXT, null, false);
        const nodes = [];
        let node;
        while (node = walker.nextNode()) {
            if (node.nodeValue && node.nodeValue.includes('$')) {
                nodes.push(node);
            }
        }
        nodes.forEach(function(textNode) {
            const text = textNode.nodeValue;
            const displayRegex = /\$\$([\s\S]+?)\$\$/g;
            const inlineRegex = /\$([^\$\n]+?)\$/g;
            if (!displayRegex.test(text) && !inlineRegex.test(text)) return;

            const span = document.createElement('span');
            let html = text.replace(/\$\$([\s\S]+?)\$\$/g, function(m, f) {
                try { return katex.renderToString(f.trim(), { displayMode: true, throwOnError: false }); } catch(e) { return m; }
            }).replace(/\$([^\$\n]+?)\$/g, function(m, f) {
                try { return katex.renderToString(f.trim(), { displayMode: false, throwOnError: false }); } catch(e) { return m; }
            });
            span.innerHTML = html;
            if (textNode.parentNode) {
                textNode.parentNode.replaceChild(span, textNode);
            }
        });
    }

    // Initial KaTeX render
    setTimeout(function() {
        document.querySelectorAll('.katex-render-target').forEach(function(el) {
            renderKaTeXIn(el);
        });
    }, 250);

    // Lightbox image trigger
    $(document).on('click', '.mob-zoom-trigger', function() {
        const url = $(this).attr('data-img-url') || $(this).attr('src');
        if (url) {
            $('#lightboxImg').attr('src', url);
            $('#lightboxOverlay').addClass('show');
            $('body').css('overflow', 'hidden');
        }
    });

    $('#btnCloseLightbox, #lightboxOverlay').on('click', function(e) {
        if (e.target !== document.getElementById('lightboxImg')) {
            $('#lightboxOverlay').removeClass('show');
            $('body').css('overflow', '');
        }
    });

    // Delete trigger
    $(document).on('click', '.mob-open-delete', function() {
        const id = $(this).attr('data-id');
        const label = $(this).attr('data-label') || 'this question';
        $('#deleteQuestionId').val(id);
        $('#deleteQuestionPrompt').html(`Are you sure you want to permanently delete <strong>${label}</strong>? This action cannot be undone.`);
        openSheet('#deleteBottomSheet', '#deleteSheetBackdrop');
    });

    $('#btnCloseDeleteSheet, #btnCancelDelete, #deleteSheetBackdrop').on('click', function() {
        closeSheet('#deleteBottomSheet', '#deleteSheetBackdrop');
    });

    // Details Modal trigger
    $(document).on('click', '.mob-open-detail', function() {
        const btn = $(this);
        const question = btn.attr('data-question');
        const subject = btn.attr('data-subject') || 'General';
        const className = btn.attr('data-class') || 'Class —';
        const type = btn.attr('data-type') || 'Objective';
        const difficulty = btn.attr('data-difficulty') || 'Medium';
        const a1 = btn.attr('data-opt1') || '';
        const a2 = btn.attr('data-opt2') || '';
        const a3 = btn.attr('data-opt3') || '';
        const a4 = btn.attr('data-opt4') || '';
        const a5 = btn.attr('data-opt5') || '';
        const correctAns = String(btn.attr('data-correct_ans') ?? '0');
        const direction = btn.attr('data-direction') || '';
        const directionTable = btn.attr('data-direction_table') || '';
        const image = btn.attr('data-image') || '';
        const explanation = btn.attr('data-explanation') || '';

        $('#detClass').text(className);
        $('#detSubject').text(subject);
        $('#detType').text(type);
        $('#detDifficulty').text(difficulty).attr('class', 'mob-badge-diff ' + (difficulty === 'Easy' ? 'diff-easy' : (difficulty === 'Hard' ? 'diff-hard' : 'diff-medium')));

        // Direction / Passage
        if (direction || directionTable) {
            $('#detDirectionText').html(formatRichExamContent(direction));
            if (directionTable) {
                $('#detDirectionTable').html(directionTable).show();
            } else {
                $('#detDirectionTable').hide();
            }
            $('#detDirectionBox').show();
        } else {
            $('#detDirectionBox').hide();
        }

        // Image
        if (image) {
            $('#detImage').attr('src', image).attr('data-img-url', image);
            $('#detImageBox').show();
        } else {
            $('#detImageBox').hide();
        }

        // Question Statement
        $('#detQuestionText').html(formatRichExamContent(question));

        // Options
        if (type.indexOf('Descriptive') === -1) {
            $('#detAns1').html(formatRichExamContent(a1));
            $('#detAns2').html(formatRichExamContent(a2));
            $('#detAns3').html(formatRichExamContent(a3));
            $('#detAns4').html(formatRichExamContent(a4));
            if (a5) {
                $('#detAns5').html(formatRichExamContent(a5));
                $('#detOpt4').show();
            } else {
                $('#detOpt4').hide();
            }
            $('#detOptionsWrap').show();

            // Reset and highlight correct
            $('#detOptionsList .mob-opt-card').removeClass('is-correct');
            $('#detOpt' + correctAns).addClass('is-correct');
        } else {
            $('#detOptionsWrap').hide();
        }

        // Explanation
        if (explanation) {
            $('#detSolText').html(formatRichExamContent(explanation));
            $('#detSolBox').show();
        } else {
            $('#detSolBox').hide();
        }

        openSheet('#detailBottomSheet', '#detailSheetBackdrop');

        setTimeout(function() {
            renderKaTeXIn(document.getElementById('detailBottomSheet'));
        }, 150);
    });

    $('#btnCloseDetailSheet, #btnCancelDetail, #detailSheetBackdrop').on('click', function() {
        closeSheet('#detailBottomSheet', '#detailSheetBackdrop');
    });

});
</script>
@endsection

@php
    $classType = $classType ?? Helper::classType();
    $allSubjects = $allSubjects ?? Helper::getSubject();
    $stats = $stats ?? [
        'total' => 0,
        'mcq' => 0,
        'descriptive' => 0,
        'grouped' => 0,
        'diagram' => 0,
        'ai_extracted' => 0,
    ];
    $search = $search ?? [];
    $permission = Helper::permissioncheck(8);
@endphp
@extends('layout.app')

@section('title', 'Question Bank - Examination Control')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* ==========================================================================
   ARISE ERP - QUESTION BANK (PROFESSIONAL DESKTOP VIEW)
   Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   ========================================================================== */
.qb-page-container {
    padding: 12px 14px;
    background: #f8fafc;
}

/* Prevent common.css .bg-light from poisoning question bank elements */
.qb-page-container .bg-light {
    background-color: #f8fafc !important;
    color: #1e293b !important;
}

/* 1. Hero Banner */
.qb-hero-banner {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 12px 16px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.15);
}
.qb-hero-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.qb-hero-subtitle {
    font-size: 11.5px;
    color: #cbd5e1;
    margin: 2px 0 0 0;
}
.qb-hero-actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}
.qb-btn-hero {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    height: 32px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 3px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
}
.qb-btn-hero-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.qb-btn-hero-light:hover {
    background: #f1f5f9;
    color: #0f3460 !important;
}
.qb-btn-hero-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.28);
}
.qb-btn-hero-outline:hover {
    background: rgba(255, 255, 255, 0.22);
}

/* 2. KPI Summary Strip */
.qb-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-bottom: 12px;
}
@media (max-width: 1200px) {
    .qb-kpi-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .qb-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
.qb-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 3.5px solid #002C54;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none !important;
    color: inherit;
}
.qb-kpi-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
}
.qb-kpi-card.active-kpi {
    background: #f0f7ff;
    border-color: #0284c7;
    box-shadow: 0 0 0 1.5px #0284c7;
}
.qb-kpi-card.kpi-mcq { border-left-color: #10b981; }
.qb-kpi-card.kpi-grouped { border-left-color: #0284c7; }
.qb-kpi-card.kpi-diagram { border-left-color: #f59e0b; }
.qb-kpi-card.kpi-ai { border-left-color: #8b5cf6; }

.qb-kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 1px;
}
.qb-kpi-val {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.qb-kpi-icon {
    width: 30px;
    height: 30px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    background: #f1f5f9;
    color: #002C54;
}

/* 3. Filter Bar */
.qb-filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 12px 14px 6px 14px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.qb-filter-label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    display: block;
}
.qb-filter-input, .qb-filter-card .select2-container--bootstrap4 .select2-selection {
    height: 32px !important;
    font-size: 12px !important;
    border-radius: 3px !important;
    border: 1px solid #cbd5e1 !important;
}

/* 4. Questions Table Card */
.qb-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.qb-table-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 9px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    border-bottom: 1px solid #001f3f;
}
.qb-table-title {
    font-size: 13px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-qb {
    margin-bottom: 0;
    font-size: 12px;
    color: #1e293b;
    border-collapse: separate;
    border-spacing: 0;
}
.table-qb thead th {
    background: #f1f5f9 !important;
    color: #334155 !important;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 2px solid #cbd5e1 !important;
    border-top: none !important;
    padding: 10px 12px;
    vertical-align: middle;
}
.table-qb tbody td {
    padding: 10px 12px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}
.table-qb tbody tr:hover td {
    background-color: #f8fafc;
}

/* Question Statement Styling */
.qb-statement-box {
    line-height: 1.55;
    color: #0f172a;
    font-size: 12.5px;
}
.qb-diagram-thumb {
    width: 52px;
    height: 42px;
    object-fit: cover;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: transform .15s ease, box-shadow .15s ease;
}
.qb-diagram-thumb:hover {
    transform: scale(1.08);
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

/* Solution Explanation Box */
.qb-solution-hint {
    background-color: #fffbeb !important;
    border: 1px solid #fef3c7 !important;
    border-left: 3.5px solid #f59e0b !important;
    border-radius: 3px;
    padding: 5px 8px;
    font-size: 11px;
    margin-top: 6px;
    color: #92400e !important;
    line-height: 1.45;
}
.qb-solution-hint strong {
    color: #b45309 !important;
}
.qb-solution-hint span {
    color: #78350f !important;
}

/* Option Chips */
.qb-opt-chip {
    display: inline-flex;
    align-items: center;
    padding: 2.5px 7px;
    border-radius: 3px;
    font-size: 11px;
    margin: 2px 4px 2px 0;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    max-width: 220px;
    line-height: 1.35;
    transition: all .15s ease;
}
.qb-opt-chip strong {
    margin-right: 4px;
    color: #002C54;
    font-weight: 700;
}
.qb-opt-chip.is-correct {
    background: #ecfdf5 !important;
    border-color: #10b981 !important;
    color: #065f46 !important;
    font-weight: 700;
}
.qb-opt-chip.is-correct strong {
    color: #065f46 !important;
}

/* Action Buttons */
.qb-actions-group {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-action-qb {
    width: 28px;
    height: 28px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 3px;
    font-size: 11.5px;
    margin: 0 1px;
    border: none;
    transition: all .15s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}
.btn-action-view {
    background: #0284c7;
    color: #ffffff !important;
}
.btn-action-view:hover {
    background: #0369a1;
    transform: translateY(-1px);
}
.btn-action-edit {
    background: #002C54;
    color: #ffffff !important;
}
.btn-action-edit:hover {
    background: #0f3460;
    transform: translateY(-1px);
}
.btn-action-delete {
    background: #ef4444;
    color: #ffffff !important;
}
.btn-action-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Pagination Bar */
.qb-pagination-bar {
    background: #ffffff !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 10px 16px !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.qb-pagination-links .pagination {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 3px;
}
.qb-pagination-links .page-link {
    border-radius: 3px !important;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: #002C54 !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    line-height: 1.4;
    transition: all .15s ease;
}
.qb-pagination-links .page-link:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    color: #002C54 !important;
}
.qb-pagination-links .page-item.active .page-link {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    border-color: #002C54 !important;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.25);
}
.qb-pagination-links .page-item.disabled .page-link {
    background: #f8fafc !important;
    color: #94a3b8 !important;
    border-color: #e2e8f0 !important;
    cursor: not-allowed;
}

/* KaTeX formula rendering polish */
.katex {
    font-size: 1.05em !important;
}
.katex-display {
    margin: 4px 0 !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="qb-page-container">
        
        {{-- 1. Hero Banner --}}
        <div class="qb-hero-banner">
            <div>
                <h1 class="qb-hero-title">
                    <i class="fa fa-question-circle-o"></i> Question Bank
                </h1>
                <p class="qb-hero-subtitle">
                    Manage, review and filter objective & descriptive questions, direction sets and diagrams
                </p>
            </div>
            <div class="qb-hero-actions">
                <a href="{{ url('add/question') }}" class="qb-btn-hero qb-btn-hero-light {{ $permission->add ? '' : 'd-none' }}">
                    <i class="fa fa-plus text-primary"></i> Add Question
                </a>
                <a href="{{ url('examination_dashboard') }}" class="qb-btn-hero qb-btn-hero-outline">
                    <i class="fa fa-arrow-left"></i> Exam Dashboard
                </a>
                <a href="{{ url('view/question') }}" class="qb-btn-hero qb-btn-hero-outline" title="Clear all filters">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>

        {{-- 2. Fast KPI Summary Cards (Clickable Quick Filters) --}}
        <div class="qb-kpi-grid">
            <a href="{{ url('view/question') }}" class="qb-kpi-card {{ empty($search['format_filter']) && empty($search['question_type_id']) ? 'active-kpi' : '' }}">
                <div>
                    <div class="qb-kpi-label">All Questions</div>
                    <div class="qb-kpi-val">{{ number_format($stats['total'] ?? 0) }}</div>
                </div>
                <div class="qb-kpi-icon"><i class="fa fa-cubes"></i></div>
            </a>

            <a href="{{ url('view/question') }}?question_type_id=1" class="qb-kpi-card kpi-mcq {{ ($search['question_type_id'] ?? '') == '1' ? 'active-kpi' : '' }}">
                <div>
                    <div class="qb-kpi-label">Objective (MCQs)</div>
                    <div class="qb-kpi-val text-success">{{ number_format($stats['mcq'] ?? 0) }}</div>
                </div>
                <div class="qb-kpi-icon text-success"><i class="fa fa-list-ul"></i></div>
            </a>

            <a href="{{ url('view/question') }}?format_filter=grouped" class="qb-kpi-card kpi-grouped {{ ($search['format_filter'] ?? '') == 'grouped' ? 'active-kpi' : '' }}">
                <div>
                    <div class="qb-kpi-label">Direction Sets</div>
                    <div class="qb-kpi-val text-info">{{ number_format($stats['grouped'] ?? 0) }}</div>
                </div>
                <div class="qb-kpi-icon text-info"><i class="fa fa-sitemap"></i></div>
            </a>

            <a href="{{ url('view/question') }}?format_filter=diagram" class="qb-kpi-card kpi-diagram {{ ($search['format_filter'] ?? '') == 'diagram' ? 'active-kpi' : '' }}">
                <div>
                    <div class="qb-kpi-label">With Diagrams</div>
                    <div class="qb-kpi-val text-warning">{{ number_format($stats['diagram'] ?? 0) }}</div>
                </div>
                <div class="qb-kpi-icon text-warning"><i class="fa fa-picture-o"></i></div>
            </a>

            <a href="{{ url('view/question') }}?format_filter=ai_extracted" class="qb-kpi-card kpi-ai {{ ($search['format_filter'] ?? '') == 'ai_extracted' ? 'active-kpi' : '' }}">
                <div>
                    <div class="qb-kpi-label">AI Extracted</div>
                    <div class="qb-kpi-val" style="color: #8b5cf6;">{{ number_format($stats['ai_extracted'] ?? 0) }}</div>
                </div>
                <div class="qb-kpi-icon" style="color: #8b5cf6;"><i class="fa fa-magic"></i></div>
            </a>
        </div>

        {{-- 3. Comprehensive Filter Form --}}
        <div class="qb-filter-card">
            <form id="qbFilterForm" action="{{ url('view/question') }}" method="GET">
                <div class="row align-items-end">
                    
                    {{-- Class --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-graduation-cap text-muted mr-1"></i> Class</label>
                        <select class="form-control qb-filter-input select2" id="class_type_id" name="class_type_id">
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

                    {{-- Subject --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-book text-muted mr-1"></i> Subject</label>
                        <select class="form-control qb-filter-input select2" id="subject_id" name="subject_id">
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

                    {{-- Question Type --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-check-square-o text-muted mr-1"></i> Question Type</label>
                        <select class="form-control qb-filter-input" id="question_type_id" name="question_type_id">
                            <option value="">All Types</option>
                            <option value="1" {{ (($search['question_type_id'] ?? '') == '1') ? 'selected' : '' }}>Objective (MCQ)</option>
                            <option value="2" {{ (($search['question_type_id'] ?? '') == '2') ? 'selected' : '' }}>Descriptive</option>
                        </select>
                    </div>

                    {{-- Difficulty Level --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-tachometer text-muted mr-1"></i> Difficulty</label>
                        <select class="form-control qb-filter-input" id="difficulty_level" name="difficulty_level">
                            <option value="">All Difficulties</option>
                            <option value="Easy" {{ (($search['difficulty_level'] ?? '') == 'Easy') ? 'selected' : '' }}>Easy</option>
                            <option value="Medium" {{ (($search['difficulty_level'] ?? '') == 'Medium') ? 'selected' : '' }}>Medium</option>
                            <option value="Hard" {{ (($search['difficulty_level'] ?? '') == 'Hard') ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>

                    {{-- Format / Nature --}}
                    <div class="col-md-2 col-sm-6 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-filter text-muted mr-1"></i> Format / Nature</label>
                        <select class="form-control qb-filter-input" id="format_filter" name="format_filter">
                            <option value="">All Questions</option>
                            <option value="grouped" {{ (($search['format_filter'] ?? '') == 'grouped') ? 'selected' : '' }}>Direction / Grouped Sets</option>
                            <option value="standalone" {{ (($search['format_filter'] ?? '') == 'standalone') ? 'selected' : '' }}>Standalone Only</option>
                            <option value="diagram" {{ (($search['format_filter'] ?? '') == 'diagram') ? 'selected' : '' }}>With Diagrams / Graphs</option>
                            <option value="ai_extracted" {{ (($search['format_filter'] ?? '') == 'ai_extracted') ? 'selected' : '' }}>AI Extracted</option>
                        </select>
                    </div>

                    {{-- Search Keywords & Actions --}}
                    <div class="col-md-2 col-sm-12 mb-2">
                        <label class="qb-filter-label"><i class="fa fa-search text-muted mr-1"></i> Search Keywords</label>
                        <input type="hidden" name="per_page" id="filter_per_page" value="{{ $perPage ?? 25 }}">
                        <div class="input-group">
                            <input type="text" class="form-control qb-filter-input" id="name" name="name" placeholder="Keyword, topic..." value="{{ $search['name'] ?? '' }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary btn-sm px-3" title="Filter Questions">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- 4. Questions Data Table --}}
        <div class="qb-table-card">
            <div class="qb-table-header">
                <div class="qb-table-title">
                    <i class="fa fa-database"></i> Question Bank Records
                    <span class="badge badge-light ml-2" style="font-size: 11px; background: #ffffff !important; color: #002C54 !important; font-weight: 700;">
                        Showing {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} of {{ $data->total() ?? 0 }}
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center text-white small mr-2" style="font-size: 11px;">
                        <span class="mr-1 opacity-75">Rows:</span>
                        <select class="form-control form-control-sm qb-per-page-select" style="height: 26px; font-size: 11px; padding: 1px 6px; width: auto; background: rgba(255,255,255,0.18); color: #ffffff; border: 1px solid rgba(255,255,255,0.35); border-radius: 3px; font-weight: 600;">
                            <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }} style="color:#0f172a;">10</option>
                            <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }} style="color:#0f172a;">25</option>
                            <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }} style="color:#0f172a;">50</option>
                            <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }} style="color:#0f172a;">100</option>
                        </select>
                    </div>
                    @if(request()->hasAny(['class_type_id', 'subject_id', 'question_type_id', 'difficulty_level', 'format_filter', 'name']))
                        <a href="{{ url('view/question') }}" class="btn btn-xs btn-outline-light" style="font-size: 10.5px; padding: 2px 8px; border-radius: 3px;">
                            <i class="fa fa-times mr-1"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-qb">
                    <thead>
                        <tr>
                            <th style="width: 45px;" class="text-center">#</th>
                            <th style="width: 140px;">Class &amp; Subject</th>
                            <th style="width: 120px;">Type &amp; Level</th>
                            <th>Question Statement &amp; Context</th>
                            <th style="width: 260px;">Options Preview</th>
                            <th style="width: 95px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $idx => $item)
                            @php
                                $rowNum = ($data->firstItem() ?? 1) + $idx;
                                $correctOpt = (string)($item->correct_ans ?? '0');
                                $hasImage = !empty($item->image);
                                $isGrouped = !empty($item->group_id);
                                $isAi = !empty($item->ai_extracted);
                                $diff = $item->difficulty_level ?: 'Medium';
                                $diffClass = $diff === 'Easy' ? 'success' : ($diff === 'Hard' ? 'danger' : 'warning');
                            @endphp
                            <tr id="q-row-{{ $item->id }}">
                                {{-- 1. Index / ID --}}
                                <td class="text-center font-weight-bold text-muted" style="font-size: 11px;">
                                    {{ $rowNum }}
                                </td>

                                {{-- 2. Class & Subject --}}
                                <td>
                                    <div class="font-weight-bold text-dark" style="font-size: 12px;">
                                        {{ $item->class_name ?? 'Class —' }}
                                    </div>
                                    <span class="badge border mt-1" style="background: #f1f5f9; color: #334155; font-size: 10px; font-weight: 600; padding: 3px 6px;">
                                        <i class="fa fa-book mr-1 text-primary"></i> {{ $item->subject_name ?? 'General' }}
                                    </span>
                                </td>

                                {{-- 3. Type & Difficulty --}}
                                <td>
                                    <div>
                                        @if($item->question_type_id == 1)
                                            <span class="badge border" style="background:#e0f2fe; color:#0369a1; font-size: 10px; font-weight: 700; padding: 3px 6px;">
                                                <i class="fa fa-check-circle mr-1 text-success"></i> MCQ
                                            </span>
                                        @else
                                            <span class="badge badge-secondary" style="font-size: 10px; padding: 3px 6px;">
                                                Descriptive
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mt-1 d-flex align-items-center gap-1">
                                        <span class="badge badge-{{ $diffClass }}" style="font-size: 9.5px; padding: 2px 5px;">
                                            {{ $diff }}
                                        </span>
                                        @if($isAi)
                                            <span class="badge border" style="background: #f5f3ff; color: #7c3aed; font-size: 9.5px; font-weight: 600; padding: 2px 5px;" title="AI Extracted Question">
                                                <i class="fa fa-magic"></i> AI
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- 4. Question Statement & Context --}}
                                <td>
                                    {{-- Direction / Group Chip --}}
                                    @if($isGrouped)
                                        <div class="mb-1">
                                            <span class="badge" style="background:#0284c7; color:#ffffff; font-size: 10px; padding: 3px 6px;">
                                                <i class="fa fa-sitemap mr-1"></i> Direction Set: {{ $item->group_id }}
                                                @if(!empty($item->sub_question_no)) (Q{{ $item->sub_question_no }}) @endif
                                            </span>
                                            <a href="{{ url('view/question') }}?group_filter={{ urlencode($item->group_id) }}" class="small text-muted ml-1" title="Filter sibling questions in this set">
                                                <i class="fa fa-filter"></i>
                                            </a>
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-start">
                                        {{-- Diagram Thumbnail --}}
                                        @if($hasImage)
                                            <div class="mr-2 flex-shrink-0">
                                                <img src="{{ asset($item->image) }}" alt="Diagram" class="qb-diagram-thumb btn-preview-image" data-img-url="{{ asset($item->image) }}" title="Click to view full diagram">
                                            </div>
                                        @endif

                                        {{-- Formatted Question Text --}}
                                        <div class="qb-statement-box flex-grow-1 katex-render-target">
                                            {!! Helper::cleanHtmlFormat($item->name ?? '') !!}
                                        </div>
                                    </div>

                                    {{-- Explanation / Solution Snippet (Styled Amber Alert) --}}
                                    @if(!empty($item->explanation))
                                        <div class="qb-solution-hint">
                                            <strong><i class="fa fa-lightbulb-o mr-1"></i> Sol:</strong>
                                            <span class="katex-render-target">{!! Helper::cleanHtmlFormat($item->explanation) !!}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Options Preview --}}
                                <td>
                                    @if($item->question_type_id == 1)
                                        <div class="d-flex flex-wrap">
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
                                            @foreach($opts as $letter => $val)
                                                @php
                                                    $oIndex = array_search($letter, $letters);
                                                    $isCorr = (string)$oIndex === $correctOpt;
                                                @endphp
                                                <span class="qb-opt-chip {{ $isCorr ? 'is-correct' : '' }}" title="Option {{ $letter }}: {{ strip_tags($val ?? '') }}">
                                                    <strong>{{ $letter }}:</strong> 
                                                    <span class="katex-render-target">{!! Helper::cleanHtmlFormat($val ?? '—') !!}</span>
                                                    @if($isCorr) <i class="fa fa-check text-success ml-1"></i> @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small"><em>Descriptive / Subjective</em></span>
                                    @endif
                                </td>

                                {{-- 6. Actions --}}
                                <td class="text-center">
                                    <div class="qb-actions-group">
                                        <button type="button" 
                                                class="btn btn-action-qb btn-action-view questionDetail {{ $permission->view ? '' : 'd-none' }}" 
                                                title="View Details & KaTeX"
                                                data-id="{{ $item->id }}"
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
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <a href="{{ url('edit/question/' . $item->id) }}" class="btn btn-action-qb btn-action-edit {{ $permission->edit ? '' : 'd-none' }}" title="Edit Question">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <button type="button" class="btn btn-action-qb btn-action-delete deleteData {{ $permission->delete ? '' : 'd-none' }}" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#Modal_id" title="Delete Question">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa fa-folder-open-o fa-3x mb-2 text-secondary"></i>
                                        <h5 class="font-weight-bold" style="color: #334155;">No Questions Found</h5>
                                        <p class="small text-muted mb-3">No questions matched the selected filters or search keyword.</p>
                                        <a href="{{ url('view/question') }}" class="btn btn-sm btn-outline-primary mr-1" style="border-radius: 3px;">
                                            <i class="fa fa-refresh mr-1"></i> Reset Filters
                                        </a>
                                        <a href="{{ url('add/question') }}" class="btn btn-sm btn-primary" style="background: #002C54; border-color: #002C54; border-radius: 3px;">
                                            <i class="fa fa-plus mr-1"></i> Add New Question
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Pagination Footer --}}
            <div class="qb-pagination-bar">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="small font-weight-bold" style="color: #475569; font-size: 11.5px;">
                        Showing <strong class="text-dark">{{ $data->firstItem() ?? 0 }}</strong> to <strong class="text-dark">{{ $data->lastItem() ?? 0 }}</strong> of <strong class="text-dark">{{ $data->total() ?? 0 }}</strong> questions
                    </span>
                    <div class="d-inline-flex align-items-center ml-2">
                        <span class="text-muted small mr-1" style="font-size: 11px;">Per Page:</span>
                        <select class="form-control form-control-sm qb-per-page-select" style="height: 28px; font-size: 11px; padding: 2px 8px; width: auto; border: 1px solid #cbd5e1; border-radius: 3px; font-weight: 600;">
                            <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div class="qb-pagination-links">
                    {{ $data->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL 1: QUESTION FULL DETAILS & KATEX RENDER --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 6px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title font-weight-bold" style="font-size: 13.5px;">
                    <i class="fa fa-info-circle mr-1"></i> Question Details & Preview
                </h5>
                <button type="button" class="btn-close btn-close-white text-white close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; font-size: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4" style="max-height: 80vh; overflow-y: auto;">
                {{-- Metadata Tags --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 mb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge badge-info" id="modal_class_badge">Class</span>
                        <span class="badge badge-secondary" id="modal_subject_badge">Subject</span>
                        <span class="badge badge-primary-light border text-primary" id="modal_type_badge">Objective</span>
                        <span class="badge badge-warning" id="modal_difficulty_badge">Medium</span>
                    </div>
                    <div id="modal_group_chip_wrap" class="d-none">
                        <span class="badge badge-info" id="modal_group_chip"><i class="fa fa-sitemap mr-1"></i> Direction Group</span>
                    </div>
                </div>

                {{-- Direction / Passage Alert --}}
                <div id="modal_direction_box" class="alert alert-info d-none p-3 mb-3" style="background: #f0f7ff; border-left: 4px solid #0284c7; color: #0f172a;">
                    <div class="font-weight-bold text-primary mb-1">
                        <i class="fa fa-sitemap mr-1"></i> Direction / Passage Context:
                    </div>
                    <div id="modal_direction_text" class="font-weight-bold my-2" style="font-size: 13px; line-height: 1.6;"></div>
                    <div id="modal_direction_table" class="table-responsive bg-white p-2 rounded border mt-2 d-none"></div>
                </div>

                {{-- Attached Diagram / Chart --}}
                <div id="modal_image_box" class="text-center my-3 d-none">
                    <span class="d-block small text-muted font-weight-bold mb-1"><i class="fa fa-picture-o mr-1 text-warning"></i> Attached Diagram / Graph:</span>
                    <img id="modal_image" src="" alt="Question Diagram" class="img-fluid rounded border p-1 bg-white shadow-sm" style="max-height: 280px; cursor: pointer;" title="Click to view full image">
                </div>

                {{-- Question Statement --}}
                <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <span class="small font-weight-bold text-muted text-uppercase mb-1 d-block">Question Statement:</span>
                    <div id="modal_question_text" class="font-weight-bold text-dark" style="font-size: 14px; line-height: 1.6;"></div>
                </div>

                {{-- Options A to E --}}
                <div id="modal_options_wrap">
                    <span class="small font-weight-bold text-muted text-uppercase mb-2 d-block">Options:</span>
                    <div class="row">
                        <div class="col-md-6 mb-2" id="modal_opt_wrap_A">
                            <div class="p-2 border rounded modal-opt-box" id="modal_opt_box_0">
                                <strong class="badge badge-light border mr-1">A</strong> <span id="modal_ans1"></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2" id="modal_opt_wrap_B">
                            <div class="p-2 border rounded modal-opt-box" id="modal_opt_box_1">
                                <strong class="badge badge-light border mr-1">B</strong> <span id="modal_ans2"></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2" id="modal_opt_wrap_C">
                            <div class="p-2 border rounded modal-opt-box" id="modal_opt_box_2">
                                <strong class="badge badge-light border mr-1">C</strong> <span id="modal_ans3"></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2" id="modal_opt_wrap_D">
                            <div class="p-2 border rounded modal-opt-box" id="modal_opt_box_3">
                                <strong class="badge badge-light border mr-1">D</strong> <span id="modal_ans4"></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2 d-none" id="modal_opt_wrap_E">
                            <div class="p-2 border rounded modal-opt-box" id="modal_opt_box_4">
                                <strong class="badge badge-light border mr-1">E</strong> <span id="modal_ans5"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Explanation / Solution --}}
                <div id="modal_explanation_box" class="mt-3 p-3 rounded border d-none" style="background: #fffbeb !important; border: 1px solid #fef3c7 !important; border-left: 4px solid #f59e0b !important; color: #92400e;">
                    <strong style="color: #b45309;"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution / Explanation:</strong>
                    <div id="modal_explanation_text" class="mt-1 small" style="line-height: 1.6; color: #78350f;"></div>
                </div>
            </div>

            <div class="modal-footer py-2 px-3" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: IMAGE LIGHTBOX --}}
<div class="modal fade" id="imageLightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark text-white" style="border-radius: 4px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title"><i class="fa fa-picture-o mr-1"></i> Diagram Preview</h6>
                <button type="button" class="btn-close btn-close-white text-white close" data-bs-dismiss="modal" style="background:none; border:none; font-size:20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="lightboxImage" src="" alt="Full Diagram" class="img-fluid rounded border bg-white" style="max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: DELETE CONFIRMATION --}}
<div class="modal fade" id="Modal_id" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-outline card-orange">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title" style="font-size: 14px;"><i class="fa fa-trash-o mr-1"></i> Delete Confirmation</h5>
                <button type="button" class="btn-close text-white close" data-bs-dismiss="modal" style="background:none; border:none; font-size:20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('delete/question') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <i class="fa fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="font-weight-bold text-dark">Are you sure you want to delete this question?</h5>
                    <p class="small text-muted mb-0">This action cannot be undone. The question will be permanently removed from the question bank.</p>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Delete Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select2 Initialization
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    }

    // Format HTML & Linebreaks safely
    function formatRichExamContent(text) {
        if (!text) return '';
        return String(text)
            .replace(/&lt;(\/?(?:br|b|strong|i|em|u|sub|sup|table|thead|tbody|tr|th|td|p|div|span|ul|ol|li|hr|h[1-6])(?:\s+[^&>]*)?)&gt;/gi, '<$1>')
            .replace(/<br\s*\/?>\s*\r?\n?/gi, '<br>')
            .replace(/([^>])\r?\n/g, '$1<br>');
    }

    // Universal KaTeX Text Walker
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
        nodes.forEach(textNode => {
            const text = textNode.nodeValue;
            const displayRegex = /\$\$([\s\S]+?)\$\$/g;
            const inlineRegex = /\$([^\$\n]+?)\$/g;
            if (!displayRegex.test(text) && !inlineRegex.test(text)) return;

            const span = document.createElement('span');
            let html = text.replace(/\$\$([\s\S]+?)\$\$/g, (m, f) => {
                try { return katex.renderToString(f.trim(), { displayMode: true, throwOnError: false }); } catch(e) { return m; }
            }).replace(/\$([^\$\n]+?)\$/g, (m, f) => {
                try { return katex.renderToString(f.trim(), { displayMode: false, throwOnError: false }); } catch(e) { return m; }
            });
            span.innerHTML = html;
            if (textNode.parentNode) {
                textNode.parentNode.replaceChild(span, textNode);
            }
        });
    }

    // Render KaTeX across table rows initially
    setTimeout(() => {
        document.querySelectorAll('.katex-render-target').forEach(el => renderKaTeXIn(el));
    }, 200);

    // Rows Per Page Selector Change
    $(document).on('change', '.qb-per-page-select', function () {
        const val = $(this).val();
        $('#filter_per_page').val(val);
        $('#qbFilterForm').submit();
    });

    // Delete Button Click
    $(document).on('click', '.deleteData', function () {
        $('#delete_id').val($(this).data('id'));
    });

    // Lightbox Diagram Click
    $(document).on('click', '.btn-preview-image, #modal_image', function () {
        const url = $(this).attr('src') || $(this).data('img-url');
        if (url) {
            $('#lightboxImage').attr('src', url);
            $('#imageLightboxModal').modal('show');
        }
    });

    // View Question Details Modal Click
    $(document).on('click', '.questionDetail', function () {
        const btn = $(this);
        const question = btn.data('question');
        const subject = btn.data('subject');
        const className = btn.data('class');
        const type = btn.data('type');
        const difficulty = btn.data('difficulty') || 'Medium';
        const a1 = btn.data('opt1') || '';
        const a2 = btn.data('opt2') || '';
        const a3 = btn.data('opt3') || '';
        const a4 = btn.data('opt4') || '';
        const a5 = btn.data('opt5') || '';
        const correctAns = String(btn.data('correct_ans') ?? '0');
        const groupId = btn.data('group_id') || '';
        const subNo = btn.data('sub_no') || '';
        const direction = btn.data('direction') || '';
        const directionTable = btn.data('direction_table') || '';
        const image = btn.data('image') || '';
        const explanation = btn.data('explanation') || '';

        // Badges
        $('#modal_class_badge').text(className || 'Class —');
        $('#modal_subject_badge').text(subject || 'General');
        $('#modal_type_badge').text(type || 'Objective');
        $('#modal_difficulty_badge').text(difficulty).attr('class', 'badge badge-' + (difficulty === 'Easy' ? 'success' : (difficulty === 'Hard' ? 'danger' : 'warning')));

        if (groupId) {
            $('#modal_group_chip').html('<i class="fa fa-sitemap mr-1"></i> Direction Set: ' + groupId + (subNo ? ' (Q' + subNo + ')' : ''));
            $('#modal_group_chip_wrap').removeClass('d-none');
        } else {
            $('#modal_group_chip_wrap').addClass('d-none');
        }

        // Direction / Passage
        if (direction || directionTable) {
            $('#modal_direction_text').html(formatRichExamContent(direction));
            if (directionTable) {
                $('#modal_direction_table').html(directionTable).removeClass('d-none');
            } else {
                $('#modal_direction_table').addClass('d-none');
            }
            $('#modal_direction_box').removeClass('d-none');
        } else {
            $('#modal_direction_box').addClass('d-none');
        }

        // Diagram Image
        if (image) {
            $('#modal_image').attr('src', image);
            $('#modal_image_box').removeClass('d-none');
        } else {
            $('#modal_image_box').addClass('d-none');
        }

        // Question Statement
        $('#modal_question_text').html(formatRichExamContent(question));

        // Options
        if (type.indexOf('Descriptive') === -1) {
            $('#modal_ans1').html(formatRichExamContent(a1));
            $('#modal_ans2').html(formatRichExamContent(a2));
            $('#modal_ans3').html(formatRichExamContent(a3));
            $('#modal_ans4').html(formatRichExamContent(a4));
            if (a5) {
                $('#modal_ans5').html(formatRichExamContent(a5));
                $('#modal_opt_wrap_E').removeClass('d-none');
            } else {
                $('#modal_opt_wrap_E').addClass('d-none');
            }
            $('#modal_options_wrap').removeClass('d-none');

            // Reset and highlight correct option
            $('.modal-opt-box').removeClass('bg-success text-white').css({
                'background-color': '#ffffff',
                'border-color': '#e2e8f0',
                'color': '#1e293b'
            });
            const correctBox = $('#modal_opt_box_' + correctAns);
            if (correctBox.length) {
                correctBox.css({
                    'background-color': '#dcfce7',
                    'border-color': '#86efac',
                    'color': '#166534',
                    'font-weight': 'bold'
                });
            }
        } else {
            $('#modal_options_wrap').addClass('d-none');
        }

        // Explanation / Solution
        if (explanation) {
            $('#modal_explanation_text').html(formatRichExamContent(explanation));
            $('#modal_explanation_box').removeClass('d-none');
        } else {
            $('#modal_explanation_box').addClass('d-none');
        }

        $('#myModal').modal('show');

        // Render KaTeX formulas inside modal
        setTimeout(() => {
            renderKaTeXIn(document.getElementById('myModal'));
        }, 150);
    });
});
</script>
@endsection

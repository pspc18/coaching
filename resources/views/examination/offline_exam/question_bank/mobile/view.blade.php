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

    $hasActiveFilters = request()->hasAny(['class_type_id', 'subject_id', 'question_type_id', 'difficulty_level', 'format_filter', 'name']);
@endphp

@extends('layout.mobile_app')

@section('title', 'Question Bank - Mobile')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.css">
<style>
/* ==========================================================================
   ARISE ERP - MOBILE QUESTION BANK
   Native App Style Cards, Collapsible Filters & Touch-Optimized
   ========================================================================== */
.mob-qb-container {
    padding: 8px 10px 80px 10px;
    background: #f8fafc;
}

/* 1. Mobile Hero Banner */
.mob-qb-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-qb-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-qb-title {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.mob-btn-hero-add {
    background: #ffffff;
    color: #002C54 !important;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 4px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Mobile KPI Grid in Hero */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-top: 8px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 5px 4px;
    text-align: center;
    text-decoration: none !important;
    color: #ffffff;
}
.mob-kpi-col.active {
    background: rgba(255, 255, 255, 0.22);
    border-color: #38bdf8;
}
.mob-kpi-tag {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-kpi-val {
    font-size: 13px;
    font-weight: 800;
    line-height: 1;
}

/* 2. Search & Filter Bar */
.mob-search-row {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-search-wrap {
    position: relative;
    flex: 1;
}
.mob-search-input {
    width: 100%;
    height: 36px;
    border-radius: 5px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding-left: 32px;
    padding-right: 10px;
    font-size: 12.5px;
    color: #1e293b;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-search-input:focus {
    outline: none;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.mob-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #64748b;
    pointer-events: none;
}
.mob-btn-filter-toggle {
    height: 36px;
    padding: 0 12px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    cursor: pointer;
    white-space: nowrap;
}
.mob-btn-filter-toggle.has-filter {
    background: #e0f2fe;
    border-color: #38bdf8;
    color: #0284c7;
}

/* Collapsible Filter Panel */
.mob-filter-drawer {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.mob-filter-drawer.d-none {
    display: none !important;
}
.mob-filter-form-group {
    margin-bottom: 8px;
}
.mob-filter-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 2px;
    display: block;
}
.mob-filter-select, .mob-filter-drawer input {
    height: 34px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    width: 100%;
    padding: 4px 8px;
    background: #ffffff;
}

/* 3. Question Cards Feed */
.mob-questions-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mob-q-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    position: relative;
}
.mob-q-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 4px;
    padding-bottom: 6px;
    margin-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-q-num-pill {
    background: #002C54;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 3px;
}
.mob-chip-class {
    background: #e0f2fe;
    color: #0369a1;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #bae6fd;
}
.mob-chip-subject {
    background: #f1f5f9;
    color: #334155;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
}
.mob-badge-diff {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 5px;
    border-radius: 3px;
    text-transform: uppercase;
}

/* Direction Set Alert Inside Card */
.mob-direction-strip {
    background: #f0f9ff;
    border-left: 3px solid #0284c7;
    padding: 3px 6px;
    border-radius: 2px;
    font-size: 10px;
    color: #0369a1;
    font-weight: 700;
    margin-bottom: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Question Statement */
.mob-q-statement {
    font-size: 12.5px;
    font-weight: 600;
    line-height: 1.5;
    color: #0f172a;
    margin-bottom: 8px;
}

/* Attached Diagram in Mobile Card */
.mob-q-diagram-wrap {
    text-align: center;
    margin-bottom: 8px;
    background: #f8fafc;
    padding: 4px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
.mob-q-diagram-img {
    max-height: 120px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
}

/* Mobile Options Grid */
.mob-options-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-opt-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 4px 6px;
    font-size: 11px;
    color: #334155;
    line-height: 1.3;
    display: flex;
    align-items: flex-start;
    gap: 4px;
}
.mob-opt-card.is-correct {
    background: #dcfce7;
    border-color: #86efac;
    color: #166534;
    font-weight: 700;
}
.mob-opt-letter {
    font-weight: 800;
    flex-shrink: 0;
    width: 14px;
}

/* Solution Snippet */
.mob-sol-box {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    padding: 4px 8px;
    border-radius: 2px;
    font-size: 10.5px;
    color: #78350f;
    margin-bottom: 8px;
    line-height: 1.4;
}

/* Mobile Card Footer Actions */
.mob-q-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-action {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    text-decoration: none !important;
}
.mob-btn-action-primary {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

/* KaTeX tweaks for mobile */
.katex {
    font-size: 1.05em !important;
}
</style>
@endsection

@section('content')
<div class="mob-qb-container">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-qb-hero">
        <div class="mob-qb-top">
            <h1 class="mob-qb-title">
                <i class="fa fa-question-circle-o"></i> Question Bank
            </h1>
            <a href="{{ url('add/question') }}" class="mob-btn-hero-add {{ $permission->add ? '' : 'd-none' }}">
                <i class="fa fa-plus"></i> Add
            </a>
        </div>

        {{-- Mobile KPI Grid in Hero --}}
        <div class="mob-kpi-grid">
            <a href="{{ url('view/question') }}" class="mob-kpi-col {{ empty($search['format_filter']) && empty($search['question_type_id']) ? 'active' : '' }}">
                <div class="mob-kpi-tag">Total</div>
                <div class="mob-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </a>
            <a href="{{ url('view/question') }}?question_type_id=1" class="mob-kpi-col {{ ($search['question_type_id'] ?? '') == '1' ? 'active' : '' }}">
                <div class="mob-kpi-tag">MCQ</div>
                <div class="mob-kpi-val text-success">{{ $stats['mcq'] ?? 0 }}</div>
            </a>
            <a href="{{ url('view/question') }}?format_filter=grouped" class="mob-kpi-col {{ ($search['format_filter'] ?? '') == 'grouped' ? 'active' : '' }}">
                <div class="mob-kpi-tag">Groups</div>
                <div class="mob-kpi-val text-info">{{ $stats['grouped'] ?? 0 }}</div>
            </a>
            <a href="{{ url('view/question') }}?format_filter=diagram" class="mob-kpi-col {{ ($search['format_filter'] ?? '') == 'diagram' ? 'active' : '' }}">
                <div class="mob-kpi-tag">Diagram</div>
                <div class="mob-kpi-val text-warning">{{ $stats['diagram'] ?? 0 }}</div>
            </a>
        </div>
    </div>

    {{-- 2. Search & Filter Toggle --}}
    <div class="mob-search-row">
        <div class="mob-search-wrap">
            <i class="fa fa-search mob-search-icon"></i>
            <input type="text" class="mob-search-input" id="mobQuickSearch" placeholder="Search questions..." value="{{ $search['name'] ?? '' }}">
        </div>
        <button type="button" class="mob-btn-filter-toggle {{ $hasActiveFilters ? 'has-filter' : '' }}" id="mobFilterToggleBtn">
            <i class="fa fa-filter"></i> Filters
            @if($hasActiveFilters)
                <span class="badge badge-primary ml-1" style="font-size:9px;">Active</span>
            @endif
        </button>
    </div>

    {{-- Collapsible Filter Drawer --}}
    <div class="mob-filter-drawer {{ $hasActiveFilters ? '' : 'd-none' }}" id="mobFilterDrawer">
        <form id="mobFilterForm" action="{{ url('view/question') }}" method="GET">
            <input type="hidden" name="name" id="mobFilterNameInput" value="{{ $search['name'] ?? '' }}">
            
            <div class="row">
                <div class="col-6 mob-filter-form-group">
                    <label class="mob-filter-label">Class</label>
                    <select class="mob-filter-select" name="class_type_id">
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

                <div class="col-6 mob-filter-form-group">
                    <label class="mob-filter-label">Subject</label>
                    <select class="mob-filter-select" name="subject_id">
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

                <div class="col-6 mob-filter-form-group">
                    <label class="mob-filter-label">Type</label>
                    <select class="mob-filter-select" name="question_type_id">
                        <option value="">All Types</option>
                        <option value="1" {{ (($search['question_type_id'] ?? '') == '1') ? 'selected' : '' }}>Objective (MCQ)</option>
                        <option value="2" {{ (($search['question_type_id'] ?? '') == '2') ? 'selected' : '' }}>Descriptive</option>
                    </select>
                </div>

                <div class="col-6 mob-filter-form-group">
                    <label class="mob-filter-label">Difficulty</label>
                    <select class="mob-filter-select" name="difficulty_level">
                        <option value="">All Difficulties</option>
                        <option value="Easy" {{ (($search['difficulty_level'] ?? '') == 'Easy') ? 'selected' : '' }}>Easy</option>
                        <option value="Medium" {{ (($search['difficulty_level'] ?? '') == 'Medium') ? 'selected' : '' }}>Medium</option>
                        <option value="Hard" {{ (($search['difficulty_level'] ?? '') == 'Hard') ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>

                <div class="col-12 mob-filter-form-group">
                    <label class="mob-filter-label">Format / Nature</label>
                    <select class="mob-filter-select" name="format_filter">
                        <option value="">All Questions</option>
                        <option value="grouped" {{ (($search['format_filter'] ?? '') == 'grouped') ? 'selected' : '' }}>Direction / Grouped Sets</option>
                        <option value="standalone" {{ (($search['format_filter'] ?? '') == 'standalone') ? 'selected' : '' }}>Standalone Only</option>
                        <option value="diagram" {{ (($search['format_filter'] ?? '') == 'diagram') ? 'selected' : '' }}>With Diagrams / Graphs</option>
                        <option value="ai_extracted" {{ (($search['format_filter'] ?? '') == 'ai_extracted') ? 'selected' : '' }}>AI Extracted</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <a href="{{ url('view/question') }}" class="btn btn-xs btn-outline-secondary">
                    <i class="fa fa-refresh mr-1"></i> Reset
                </a>
                <button type="submit" class="btn btn-xs btn-primary px-3">
                    <i class="fa fa-check mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- Total Results Indicator --}}
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <span class="small text-muted font-weight-bold">
            Showing {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} of {{ $data->total() ?? 0 }}
        </span>
        @if($hasActiveFilters)
            <a href="{{ url('view/question') }}" class="small text-danger font-weight-bold">
                Clear Filters &times;
            </a>
        @endif
    </div>

    {{-- 3. Mobile Question Cards Feed --}}
    <div class="mob-questions-feed">
        @forelse($data as $idx => $item)
            @php
                $rowNum = ($data->firstItem() ?? 1) + $idx;
                $correctOpt = (string)($item->correct_ans ?? '0');
                $hasImage = !empty($item->image);
                $isGrouped = !empty($item->group_id);
                $isAi = !empty($item->ai_extracted);
                $diff = $item->difficulty_level ?: 'Medium';
                $diffBadgeClass = $diff === 'Easy' ? 'badge-success' : ($diff === 'Hard' ? 'badge-danger' : 'badge-warning');
            @endphp
            <div class="mob-q-card" id="mob-q-card-{{ $item->id }}">
                
                {{-- Header: Q Number, Class, Subject, Difficulty --}}
                <div class="mob-q-card-header">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="mob-q-num-pill">#{{ $rowNum }}</span>
                        <span class="mob-chip-class">{{ $item->class_name ?? 'Class —' }}</span>
                        <span class="mob-chip-subject">{{ $item->subject_name ?? 'General' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge {{ $diffBadgeClass }} mob-badge-diff">{{ $diff }}</span>
                        @if($isAi)
                            <span class="badge badge-light border text-purple" style="font-size:9px; color:#8b5cf6;"><i class="fa fa-magic"></i> AI</span>
                        @endif
                    </div>
                </div>

                {{-- Direction Set Alert (if Grouped) --}}
                @if($isGrouped)
                    <div>
                        <span class="mob-direction-strip">
                            <i class="fa fa-sitemap"></i> Direction Set: {{ $item->group_id }} @if(!empty($item->sub_question_no))(Q{{ $item->sub_question_no }})@endif
                        </span>
                    </div>
                @endif

                {{-- Attached Diagram Thumbnail (Tap to open) --}}
                @if($hasImage)
                    <div class="mob-q-diagram-wrap">
                        <img src="{{ asset($item->image) }}" alt="Diagram" class="mob-q-diagram-img mob-lightbox-trigger" data-img-url="{{ asset($item->image) }}">
                        <div class="small text-muted mt-1 font-weight-bold" style="font-size: 10px;">
                            <i class="fa fa-search-plus mr-1"></i> Tap diagram to zoom
                        </div>
                    </div>
                @endif

                {{-- Question Statement with KaTeX --}}
                <div class="mob-q-statement katex-render-target">
                    {!! Helper::cleanHtmlFormat($item->name ?? '') !!}
                </div>

                {{-- Options Grid (for MCQs) --}}
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
                    <div class="mob-options-grid">
                        @foreach($opts as $letter => $val)
                            @php
                                $oIndex = array_search($letter, $letters);
                                $isCorr = (string)$oIndex === $correctOpt;
                            @endphp
                            <div class="mob-opt-card {{ $isCorr ? 'is-correct' : '' }}">
                                <span class="mob-opt-letter">({{ $letter }})</span>
                                <span class="flex-grow-1 katex-render-target">{!! Helper::cleanHtmlFormat($val ?? '—') !!}</span>
                                @if($isCorr)
                                    <i class="fa fa-check text-success ml-1 flex-shrink-0"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Solution Snippet (if available) --}}
                @if(!empty($item->explanation))
                    <div class="mob-sol-box">
                        <strong class="d-block"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution:</strong>
                        <div class="katex-render-target">{!! Str::limit(strip_tags(Helper::cleanHtmlFormat($item->explanation)), 140) !!}</div>
                    </div>
                @endif

                {{-- Card Actions Footer --}}
                <div class="mob-q-card-footer">
                    <button type="button" 
                            class="mob-btn-action mob-btn-action-primary mob-detail-btn {{ $permission->view ? '' : 'd-none' }}"
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
                        <i class="fa fa-eye"></i> Details
                    </button>

                    <div class="d-flex gap-1">
                        <a href="{{ url('edit/question/' . $item->id) }}" class="mob-btn-action {{ $permission->edit ? '' : 'd-none' }}">
                            <i class="fa fa-pencil text-primary"></i> Edit
                        </a>
                        <button type="button" class="mob-btn-action text-danger mob-delete-btn {{ $permission->delete ? '' : 'd-none' }}" data-id="{{ $item->id }}">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="mob-q-card text-center py-5">
                <i class="fa fa-folder-open-o fa-3x text-muted mb-2"></i>
                <h6 class="font-weight-bold text-dark">No Questions Found</h6>
                <p class="small text-muted mb-3">Try clearing filters or search terms.</p>
                <a href="{{ url('view/question') }}" class="btn btn-sm btn-outline-primary mr-1">Reset Filters</a>
                <a href="{{ url('add/question') }}" class="btn btn-sm btn-primary">Add Question</a>
            </div>
        @endforelse
    </div>

    {{-- Mobile Pagination --}}
    <div class="mt-3 text-center">
        {{ $data->links() }}
    </div>

</div>

{{-- MOBILE MODAL 1: QUESTION FULL DETAILS --}}
<div class="modal fade" id="mobDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 8px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h6 class="modal-title font-weight-bold" style="font-size: 13px;">
                    <i class="fa fa-info-circle mr-1"></i> Question Full View
                </h6>
                <button type="button" class="btn-close btn-close-white text-white" data-bs-dismiss="modal" style="background:none; border:none; font-size:18px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                {{-- Metadata Chips --}}
                <div class="d-flex align-items-center gap-1 flex-wrap pb-2 mb-2 border-bottom">
                    <span class="badge badge-info" id="mob_modal_class">Class</span>
                    <span class="badge badge-secondary" id="mob_modal_subject">Subject</span>
                    <span class="badge badge-light border text-primary" id="mob_modal_type">Objective</span>
                    <span class="badge badge-warning" id="mob_modal_difficulty">Medium</span>
                </div>

                {{-- Direction / Passage Alert --}}
                <div id="mob_modal_direction_box" class="alert alert-info d-none p-2 mb-2" style="font-size: 11.5px; background: #f0f7ff; border-left: 3px solid #0284c7;">
                    <strong class="text-primary d-block mb-1"><i class="fa fa-sitemap mr-1"></i> Direction / Passage:</strong>
                    <div id="mob_modal_direction_text" style="line-height: 1.4;"></div>
                    <div id="mob_modal_direction_table" class="table-responsive bg-white p-2 rounded border mt-2 d-none"></div>
                </div>

                {{-- Attached Diagram --}}
                <div id="mob_modal_image_box" class="text-center my-2 d-none">
                    <img id="mob_modal_image" src="" alt="Diagram" class="img-fluid rounded border p-1 bg-white shadow-sm" style="max-height: 200px;">
                </div>

                {{-- Question Statement --}}
                <div class="p-2 mb-2 rounded bg-light border">
                    <span class="small font-weight-bold text-muted d-block mb-1">Question Statement:</span>
                    <div id="mob_modal_question_text" class="font-weight-bold text-dark" style="font-size: 13px; line-height: 1.5;"></div>
                </div>

                {{-- Options --}}
                <div id="mob_modal_options_wrap">
                    <span class="small font-weight-bold text-muted d-block mb-1">Options:</span>
                    <div class="d-flex flex-column gap-1">
                        <div class="p-2 border rounded mob-modal-opt-box" id="mob_opt_0">
                            <strong>(A)</strong> <span id="mob_modal_ans1"></span>
                        </div>
                        <div class="p-2 border rounded mob-modal-opt-box" id="mob_opt_1">
                            <strong>(B)</strong> <span id="mob_modal_ans2"></span>
                        </div>
                        <div class="p-2 border rounded mob-modal-opt-box" id="mob_opt_2">
                            <strong>(C)</strong> <span id="mob_modal_ans3"></span>
                        </div>
                        <div class="p-2 border rounded mob-modal-opt-box" id="mob_opt_3">
                            <strong>(D)</strong> <span id="mob_modal_ans4"></span>
                        </div>
                        <div class="p-2 border rounded mob-modal-opt-box d-none" id="mob_opt_4">
                            <strong>(E)</strong> <span id="mob_modal_ans5"></span>
                        </div>
                    </div>
                </div>

                {{-- Solution --}}
                <div id="mob_modal_sol_box" class="mt-2 p-2 rounded bg-light border d-none">
                    <strong class="text-dark small"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Solution / Explanation:</strong>
                    <div id="mob_modal_sol_text" class="mt-1 small text-muted" style="line-height: 1.5;"></div>
                </div>
            </div>

            <div class="modal-footer py-2 px-3 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- MOBILE MODAL 2: LIGHTBOX --}}
<div class="modal fade" id="mobLightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg bg-dark text-white" style="border-radius: 6px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title font-weight-bold" style="font-size: 12px;"><i class="fa fa-picture-o mr-1"></i> Diagram Zoom</h6>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="background:none; border:none; font-size:18px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="mobLightboxImg" src="" alt="Full Diagram" class="img-fluid rounded border bg-white" style="max-height: 75vh;">
            </div>
        </div>
    </div>
</div>

{{-- MOBILE MODAL 3: DELETE CONFIRMATION --}}
<div class="modal fade" id="mobDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title" style="font-size: 13px;"><i class="fa fa-trash-o mr-1"></i> Delete Question</h6>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" style="background:none; border:none; font-size:18px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('delete/question') }}" method="POST">
                @csrf
                <div class="modal-body p-3 text-center">
                    <input type="hidden" id="mob_delete_id" name="delete_id">
                    <i class="fa fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p class="font-weight-bold mb-1 text-dark" style="font-size: 13px;">Delete this question permanently?</p>
                    <p class="small text-muted mb-0">This action cannot be reverted.</p>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.8/dist/katex.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mobile Filter Drawer Toggle
    document.getElementById('mobFilterToggleBtn')?.addEventListener('click', function () {
        const drawer = document.getElementById('mobFilterDrawer');
        if (drawer) drawer.classList.toggle('d-none');
    });

    // Quick search bar on Enter
    document.getElementById('mobQuickSearch')?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const val = this.value.trim();
            const url = new URL(window.location.href);
            if (val) {
                url.searchParams.set('name', val);
            } else {
                url.searchParams.delete('name');
            }
            window.location.href = url.toString();
        }
    });

    // Sync quick search with form hidden input
    document.getElementById('mobQuickSearch')?.addEventListener('input', function () {
        const hiddenInput = document.getElementById('mobFilterNameInput');
        if (hiddenInput) hiddenInput.value = this.value;
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

    // Initial KaTeX render on mobile cards
    setTimeout(() => {
        document.querySelectorAll('.katex-render-target').forEach(el => renderKaTeXIn(el));
    }, 200);

    // Lightbox image trigger
    $(document).on('click', '.mob-lightbox-trigger', function () {
        const url = $(this).data('img-url') || $(this).attr('src');
        if (url) {
            $('#mobLightboxImg').attr('src', url);
            $('#mobLightboxModal').modal('show');
        }
    });

    // Delete trigger
    $(document).on('click', '.mob-delete-btn', function () {
        $('#mob_delete_id').val($(this).data('id'));
        $('#mobDeleteModal').modal('show');
    });

    // Details Modal trigger
    $(document).on('click', '.mob-detail-btn', function () {
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
        const direction = btn.data('direction') || '';
        const directionTable = btn.data('direction_table') || '';
        const image = btn.data('image') || '';
        const explanation = btn.data('explanation') || '';

        $('#mob_modal_class').text(className || 'Class —');
        $('#mob_modal_subject').text(subject || 'General');
        $('#mob_modal_type').text(type || 'Objective');
        $('#mob_modal_difficulty').text(difficulty).attr('class', 'badge badge-' + (difficulty === 'Easy' ? 'success' : (difficulty === 'Hard' ? 'danger' : 'warning')));

        // Direction / Passage
        if (direction || directionTable) {
            $('#mob_modal_direction_text').html(formatRichExamContent(direction));
            if (directionTable) {
                $('#mob_modal_direction_table').html(directionTable).removeClass('d-none');
            } else {
                $('#mob_modal_direction_table').addClass('d-none');
            }
            $('#mob_modal_direction_box').removeClass('d-none');
        } else {
            $('#mob_modal_direction_box').addClass('d-none');
        }

        // Image
        if (image) {
            $('#mob_modal_image').attr('src', image);
            $('#mob_modal_image_box').removeClass('d-none');
        } else {
            $('#mob_modal_image_box').addClass('d-none');
        }

        // Question Statement
        $('#mob_modal_question_text').html(formatRichExamContent(question));

        // Options
        if (type.indexOf('Descriptive') === -1) {
            $('#mob_modal_ans1').html(formatRichExamContent(a1));
            $('#mob_modal_ans2').html(formatRichExamContent(a2));
            $('#mob_modal_ans3').html(formatRichExamContent(a3));
            $('#mob_modal_ans4').html(formatRichExamContent(a4));
            if (a5) {
                $('#mob_modal_ans5').html(formatRichExamContent(a5));
                $('#mob_opt_4').removeClass('d-none');
            } else {
                $('#mob_opt_4').addClass('d-none');
            }
            $('#mob_modal_options_wrap').removeClass('d-none');

            // Reset and highlight correct
            $('.mob-modal-opt-box').css({
                'background-color': '#ffffff',
                'border-color': '#e2e8f0',
                'color': '#1e293b',
                'font-weight': 'normal'
            });
            const correctBox = $('#mob_opt_' + correctAns);
            if (correctBox.length) {
                correctBox.css({
                    'background-color': '#dcfce7',
                    'border-color': '#86efac',
                    'color': '#166534',
                    'font-weight': 'bold'
                });
            }
        } else {
            $('#mob_modal_options_wrap').addClass('d-none');
        }

        // Explanation
        if (explanation) {
            $('#mob_modal_sol_text').html(formatRichExamContent(explanation));
            $('#mob_modal_sol_box').removeClass('d-none');
        } else {
            $('#mob_modal_sol_box').addClass('d-none');
        }

        $('#mobDetailModal').modal('show');

        setTimeout(() => {
            renderKaTeXIn(document.getElementById('mobDetailModal'));
        }, 150);
    });
});
</script>
@endsection

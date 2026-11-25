@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = $classType ?? Helper::classType();
    $date = date('Y-m-d');
    $permission = Helper::permissioncheck(8);

    $totalExams = $stats['total'] ?? count($data ?? []);
    $marksCompleted = $stats['marks_completed'] ?? 0;
    $marksInProgress = $stats['marks_in_progress'] ?? 0;
    $marksPending = $stats['marks_pending'] ?? 0;
    $publishedCount = $stats['published'] ?? 0;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* 1. Mobile Hero Card */
.mob-exam-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-exam-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-exam-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Quick Metrics in Hero */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 4px;
    text-align: center;
}
.mob-kpi-tag {
    font-size: 8px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-kpi-val {
    font-size: 13px;
    font-weight: 900;
    line-height: 1;
}

/* Top Quick Action Buttons */
.mob-action-row {
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
.btn-hero-add {
    background: #ffffff;
    color: #002C54;
    border: 1px solid #ffffff;
}
.btn-hero-workflow {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. Search Box */
.mob-search-wrap {
    position: relative;
    margin-bottom: 8px;
}
.mob-search-input {
    width: 100%;
    height: 34px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding-left: 32px;
    padding-right: 12px;
    font-size: 12px;
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

/* 3. Exam Cards List */
.mob-cards-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.mob-exam-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    padding: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 6px;
    gap: 6px;
}
.mob-exam-name-box {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.mob-exam-name {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    text-decoration: none !important;
}
.mob-exam-name:hover {
    color: #0284c7;
}
.mob-exam-code-pill {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
    background: #f1f5f9;
    color: #002C54;
    border: 1px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    margin-top: 2px;
}

/* Course & Batch info */
.mob-course-batch-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    margin-bottom: 6px;
}
.mob-course-pill {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
}
.mob-batch-pill {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
}
.mob-batch-all {
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    font-size: 10px;
    font-weight: 500;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
}

/* Subjects list chips */
.mob-subjects-box {
    margin-bottom: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
.mob-sub-chip {
    font-size: 9.5px;
    font-weight: 500;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
}

/* Marks Status & Progress Container */
.mob-marks-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px;
    margin-bottom: 8px;
}
.mob-marks-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
}
.mob-marks-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1.2;
}
.mob-marks-badge.status-completed {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}
.mob-marks-badge.status-in-progress {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #7dd3fc;
}
.mob-marks-badge.status-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.mob-marks-badge.status-not-held {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.mob-marks-badge.status-no-students {
    background: #f8fafc;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
}

/* Card Action Bar */
.mob-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.mob-actions-left {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.mob-actions-right {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-btn-action {
    height: 28px;
    padding: 0 9px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    border: 1px solid transparent;
}
.mob-btn-icon-only {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}

/* Modals styling */
.theme-delete-modal-dialog {
    max-width: 440px;
    margin: 1.75rem auto;
}
.theme-delete-modal-content {
    border: none;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    background: #ffffff;
}
.theme-delete-modal-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    color: #ffffff !important;
    padding: 10px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}
.theme-delete-title-box {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.theme-delete-icon {
    width: 28px !important;
    height: 28px !important;
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    border-radius: 3px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 12px !important;
}
.theme-delete-headings {
    display: flex !important;
    flex-direction: column !important;
}
.theme-delete-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
}
.theme-delete-close {
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 16px !important;
    opacity: 0.8 !important;
    cursor: pointer !important;
}
.theme-delete-body {
    padding: 16px 14px 12px 14px !important;
}
.theme-delete-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fee2e2;
    color: #ef4444;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 10px;
}
.theme-delete-prompt {
    font-size: 11.5px;
    color: #475569;
    margin-bottom: 5px;
}
.theme-delete-item-title {
    font-size: 13px;
    font-weight: 700;
    color: #002C54;
    margin: 0 0 10px 0;
}
.theme-delete-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 10px;
    text-align: left;
    margin-bottom: 10px;
}
.theme-delete-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    border-bottom: 1px solid #edf2f7;
    font-size: 10.5px;
}
.theme-delete-alert-warning {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-left: 3px solid #e53e3e;
    border-radius: 2px;
    padding: 6px 8px;
    font-size: 10.5px;
    color: #b91c1c;
    text-align: left;
    display: flex;
    align-items: flex-start;
    gap: 6px;
}
.theme-delete-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 8px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 6px !important;
}
</style>
@endsection

@section('content')
<div class="px-2 pt-2 pb-5">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-exam-hero">
        <div class="mob-exam-top">
            <div class="mob-exam-title">
                <i class="fa fa-leanpub"></i> Exams Management
            </div>
            <span class="badge badge-light text-dark" style="font-size:9.5px; border-radius:2px; padding:2px 5px;">Offline Exams</span>
        </div>

        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Total</div>
                <div class="mob-kpi-val" style="color:#ffffff;">{{ $totalExams }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Done</div>
                <div class="mob-kpi-val" style="color:#4ade80;">{{ $marksCompleted }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Pending</div>
                <div class="mob-kpi-val" style="color:#facc15;">{{ $marksPending + $marksInProgress }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Published</div>
                <div class="mob-kpi-val" style="color:#38bdf8;">{{ $publishedCount }}</div>
            </div>
        </div>

        <div class="mob-action-row">
            @if($permission->add ?? true)
                <a href="{{ url('add/exam') }}" class="mob-hero-btn btn-hero-add">
                    <i class="fa fa-plus-circle"></i> Add Exam
                </a>
            @endif
            <a href="{{ url('upload_omr') }}" class="mob-hero-btn btn-hero-workflow">
                <i class="fa fa-check-square-o"></i> Upload OMR
            </a>
            <a href="{{ url('exam_wise_report') }}" class="mob-hero-btn btn-hero-workflow">
                <i class="fa fa-bar-chart"></i> Reports
            </a>
        </div>
    </div>

    {{-- Session alerts --}}
    @if(session('success') || session('message'))
        <div class="alert alert-success py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 2. Real-Time Search Bar --}}
    <div class="mob-search-wrap">
        <i class="fa fa-search mob-search-icon"></i>
        <input type="text" id="mobExamSearch" class="mob-search-input" placeholder="Search exam name, course, batch, code..." autocomplete="off">
    </div>

    {{-- Result Counter --}}
    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
        <span style="font-size:11px; font-weight:700; color:#64748b;">
            Exams (<span id="mobVisibleExamCount" class="text-dark">{{ count($data ?? []) }}</span> / {{ $totalExams }})
        </span>
    </div>

    {{-- 3. Exam Cards List --}}
    <div class="mob-cards-list" id="mobExamsContainer">
        @forelse($data as $item)
            @php
                $examDateFormatted = !empty($item->exam_date)
                    ? \Carbon\Carbon::parse($item->exam_date)->format('d M Y')
                    : 'Not Set';
                $examDateIso = !empty($item->exam_date)
                    ? \Carbon\Carbon::parse($item->exam_date)->format('Y-m-d')
                    : $date;
                $codeStr = $item->code ?: ($item->exam_code ?: 'EXAM-'.$item->id);

                $mStats = $item->marks_stats ?? (object)[
                    'status' => 'pending',
                    'enrolled' => 0,
                    'filled' => 0,
                    'pending' => 0,
                    'percentage' => 0
                ];
                $mStatus = $mStats->status;
                $enrolled = $mStats->enrolled;
                $filled = $mStats->filled;
                $pending = $mStats->pending;
                $percent = $mStats->percentage;
            @endphp
            <div class="mob-exam-card mob-exam-item" 
                 data-id="{{ $item->id }}"
                 data-name="{{ strtolower($item->name ?? '') }}" 
                 data-code="{{ strtolower($codeStr) }}"
                 data-course="{{ strtolower($item->class_name ?? '') }}"
                 data-batch="{{ strtolower($item->batch_name ?? '') }}"
                 data-status="{{ $item->is_published ? 'published' : 'pending' }}"
                 data-marks-status="{{ $mStatus }}">
                
                {{-- Card Header: Exam Title & Code --}}
                <div class="mob-card-top">
                    <div class="mob-exam-name-box">
                        <div style="width:24px; height:24px; border-radius:3px; background:#002C54; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; margin-top:1px;">
                            <i class="fa fa-book"></i>
                        </div>
                        <div>
                            <a href="{{ url('edit/exam/'.$item->id) }}" class="mob-exam-name">
                                {{ $item->name }}
                            </a>
                            <div class="d-flex align-items-center" style="gap:4px;">
                                <span class="mob-exam-code-pill">
                                    <i class="fa fa-barcode mr-1"></i>{{ $codeStr }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right" style="flex-shrink:0;">
                        <span style="font-size:10.5px; font-weight:600; color:#0f172a; white-space:nowrap;">
                            <i class="fa fa-calendar-check-o text-primary mr-1"></i>{{ $examDateFormatted }}
                        </span>
                    </div>
                </div>

                {{-- Course & Batch Badges --}}
                <div class="mob-course-batch-row">
                    @if(!empty($item->class_name))
                        <span class="mob-course-pill">
                            <i class="fa fa-graduation-cap mr-1"></i> {{ $item->class_name }}
                        </span>
                    @else
                        <span class="mob-course-pill" style="background:#f1f5f9; color:#64748b; border-color:#cbd5e1;">Unassigned</span>
                    @endif

                    @if(!empty($item->batch_name))
                        <span class="mob-batch-pill">
                            <i class="fa fa-users mr-1"></i> {{ $item->batch_name }}
                        </span>
                    @else
                        <span class="mob-batch-all">
                            <i class="fa fa-globe mr-1 text-secondary"></i> All Batches
                        </span>
                    @endif
                </div>

                {{-- Included Subjects --}}
                @if(!empty($item->subject_names) && count($item->subject_names) > 0)
                    <div class="mob-subjects-box">
                        @foreach(array_slice($item->subject_names, 0, 4) as $subName)
                            <span class="mob-sub-chip">{{ $subName }}</span>
                        @endforeach
                        @if(count($item->subject_names) > 4)
                            <span class="mob-sub-chip font-weight-bold text-primary">+{{ count($item->subject_names) - 4 }} more</span>
                        @endif
                    </div>
                @endif

                {{-- Marks Status & Progress Container --}}
                <div class="mob-marks-panel">
                    <div class="mob-marks-header">
                        @if($mStatus === 'completed')
                            <span class="mob-marks-badge status-completed">
                                <i class="fa fa-check-circle mr-1"></i> Completed
                            </span>
                        @elseif($mStatus === 'in_progress')
                            <span class="mob-marks-badge status-in-progress">
                                <i class="fa fa-adjust mr-1"></i> In Progress
                            </span>
                        @elseif($mStatus === 'not_held')
                            <span class="mob-marks-badge status-not-held">
                                <i class="fa fa-calendar-o mr-1"></i> Exam Not Held
                            </span>
                        @elseif($mStatus === 'no_students')
                            <span class="mob-marks-badge status-no-students">
                                <i class="fa fa-user-times mr-1"></i> No Students
                            </span>
                        @else
                            <span class="mob-marks-badge status-pending">
                                <i class="fa fa-clock-o mr-1"></i> Marks Pending
                            </span>
                        @endif

                        @if($enrolled > 0)
                            <span style="font-size:10.5px; font-weight:700; color:#0f172a;">
                                {{ $filled }} / {{ $enrolled }} <span class="text-muted font-weight-normal">Filled</span> ({{ $percent }}%)
                            </span>
                        @else
                            <span style="font-size:10px; color:#94a3b8;"><em>0 Enrolled</em></span>
                        @endif
                    </div>

                    @if($enrolled > 0)
                        <div class="progress" style="height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden; margin-bottom: 4px;">
                            <div class="progress-bar {{ $mStatus === 'completed' ? 'bg-success' : ($mStatus === 'in_progress' ? 'bg-primary' : ($mStatus === 'not_held' ? 'bg-secondary' : 'bg-warning')) }}" 
                                 role="progressbar" 
                                 style="width: {{ $percent }}%;" 
                                 aria-valuenow="{{ $percent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 9.5px; line-height: 1;">
                            @if($mStatus === 'completed')
                                <span class="text-success font-weight-bold"><i class="fa fa-check"></i> 100% Evaluated</span>
                            @elseif($mStatus === 'not_held')
                                <span class="text-secondary"><i class="fa fa-calendar"></i> Scheduled {{ $examDateFormatted }}</span>
                            @elseif($pending > 0)
                                <span class="text-danger font-weight-bold"><i class="fa fa-exclamation-circle"></i> {{ $pending }} Pending</span>
                            @else
                                <span class="text-muted">In queue</span>
                            @endif
                            <span class="text-muted">{{ $enrolled }} Enrolled Students</span>
                        </div>
                    @endif
                </div>

                {{-- Actions Toolbar --}}
                <div class="mob-card-actions">
                    <div class="mob-actions-left">
                        {{-- 1. Marks Dropdown Button --}}
                        <div class="dropdown d-inline-block">
                            <button type="button" 
                                    class="mob-btn-action btn-outline-info" 
                                    id="mobMarksDropdown_{{ $item->id }}" 
                                    data-toggle="dropdown" 
                                    aria-haspopup="true" 
                                    aria-expanded="false"
                                    style="border-color:#7dd3fc; color:#0369a1; background:#f0f9ff;">
                                <i class="fa fa-check-square-o"></i> Marks <i class="fa fa-caret-down ml-1"></i>
                            </button>
                            <div class="dropdown-menu shadow border py-1" 
                                 aria-labelledby="mobMarksDropdown_{{ $item->id }}" 
                                 style="min-width: 175px; font-size: 11.5px; border-radius: 4px; z-index: 1060;">
                                <div class="dropdown-header text-uppercase text-muted font-weight-bold px-3 py-1" style="font-size: 9px;">
                                    {{ $item->class_name ?? 'Marks Entry' }}
                                </div>
                                <a class="dropdown-item px-3 py-2 d-flex align-items-center font-weight-600 text-primary" 
                                   href="{{ url('upload_omr?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}">
                                    <i class="fa fa-check-square-o text-warning mr-2" style="width: 14px;"></i> Upload OMR Sheet
                                </a>
                                <a class="dropdown-item px-3 py-2 d-flex align-items-center" 
                                   href="{{ url('fill_marks?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}">
                                    <i class="fa fa-pencil text-primary mr-2" style="width: 14px;"></i> Manual Fill Marks
                                </a>
                                <a class="dropdown-item px-3 py-2 d-flex align-items-center" 
                                   href="{{ url('fill-marks-by-excel?class_type_id='.$item->class_type_id.'&exam_id='.$item->id) }}">
                                    <i class="fa fa-file-excel-o text-success mr-2" style="width: 14px;"></i> Upload by Excel
                                </a>
                            </div>
                        </div>

                        {{-- 2. Result Publish Button --}}
                        @if($item->is_published)
                            <button type="button" 
                                    class="mob-btn-action openPublishModalBtn" 
                                    style="background:#dcfce7; color:#15803d; border:1px solid #86efac;"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-class-id="{{ $item->class_type_id }}"
                                    data-class-name="{{ $item->class_name }}"
                                    data-published="1"
                                    data-date="{{ $item->published_date_iso }}">
                                <i class="fa fa-check-circle"></i> Published
                            </button>
                        @else
                            <button type="button" 
                                    class="mob-btn-action openPublishModalBtn" 
                                    style="background:#fffbeb; color:#92400e; border:1px solid #fde68a;"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-class-id="{{ $item->class_type_id }}"
                                    data-class-name="{{ $item->class_name }}"
                                    data-published="0"
                                    data-date="{{ $date }}">
                                <i class="fa fa-bullhorn text-warning"></i> Publish
                            </button>
                        @endif
                    </div>

                    <div class="mob-actions-right">
                        {{-- 3. Edit Button --}}
                        @if($permission->edit ?? true)
                            <a href="{{ url('edit/exam/'.$item->id) }}" class="mob-btn-icon-only btn-outline-info border" title="Edit Exam">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endif

                        {{-- 4. Duplicate Button --}}
                        @if($permission->add ?? true)
                            <button type="button" class="mob-btn-icon-only btn-outline-secondary border copyExamData" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->copy_name }}" 
                                    data-class-id="{{ $item->class_type_id }}"
                                    data-batch-id="{{ $item->batch_id ?? '' }}"
                                    data-date="{{ $examDateIso }}"
                                    title="Duplicate Exam">
                                <i class="fa fa-copy"></i>
                            </button>
                        @endif

                        {{-- 5. Delete Button --}}
                        @if($permission->delete ?? true)
                            <button type="button" class="mob-btn-icon-only btn-outline-danger border deleteExamBtn" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}" 
                                    data-class="{{ $item->class_name }}"
                                    data-date="{{ $examDateFormatted }}"
                                    title="Delete Exam">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center text-muted py-4 bg-white rounded border">
                <i class="fa fa-leanpub mb-1" style="font-size:24px;"></i>
                <div style="font-size:12px; font-weight:700;">No examinations found</div>
            </div>
        @endforelse

        <div id="mobNoMatchesCard" class="text-center text-muted py-4 bg-white rounded border" style="display:none;">
            <i class="fa fa-search mb-1" style="font-size:20px;"></i>
            <div style="font-size:12px; font-weight:700;">No matching exams found</div>
        </div>
    </div>

</div>

{{-- Unified Result Publish Modal --}}
@include('examination.offline_exam.exam.modals')

{{-- Duplicate / Copy Exam Modal --}}
<div class="modal fade" id="copyExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content" style="border-radius:4px; overflow:hidden; border:1px solid #002C54;">
            <form id="mobFormCopyExam" action="{{ url('copy/exam') }}" method="post">
                @csrf
                <div class="modal-header bg-primary text-white py-2 px-3" style="background:#002C54 !important;">
                    <h6 class="modal-title font-weight-bold mb-0">
                        <i class="fa fa-copy mr-1 text-info"></i> Duplicate Exam
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="mob_copy_exam_id" name="exam_id">
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">New Exam Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="mob_copy_exam_name" name="name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">Target Course <span class="text-danger">*</span></label>
                        <select class="form-control form-control-sm" id="mob_copy_class_type_id" name="class_type_id" required>
                            <option value="">-- Select Target Course --</option>
                            @foreach($classType ?? [] as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">Target Batch</label>
                        <select class="form-control form-control-sm" id="mob_copy_batch_id" name="batch_id">
                            <option value="">-- All Batches (Course-Wide) --</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">Exam Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" id="mob_copy_exam_date" name="exam_date" required>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="mobBtnSubmitCopy" style="background:#002C54;">Duplicate Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Standard Delete Confirmation Modal --}}
<div class="modal fade" id="deleteExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-delete-modal-dialog" role="document">
        <div class="modal-content theme-delete-modal-content">
            <div class="modal-header theme-delete-modal-header">
                <div class="theme-delete-title-box">
                    <div class="theme-delete-icon">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="theme-delete-headings">
                        <h5 class="modal-title theme-delete-title">Delete Confirmation</h5>
                        <span class="theme-delete-subtitle" style="font-size:10px; color:rgba(255,255,255,0.85);">Permanent Exam Removal</span>
                    </div>
                </div>
                <button type="button" class="theme-delete-close" data-dismiss="modal" aria-label="Close" title="Cancel">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="mobFormDeleteExam" action="{{ url('delete/exam') }}" method="POST">
                @csrf
                <input type="hidden" name="delete_id" id="mob_delete_exam_id" value="">
                
                <div class="modal-body theme-delete-body text-center">
                    <div class="theme-delete-avatar">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p class="theme-delete-prompt">Are you sure you want to permanently delete this examination record?</p>
                    <h6 class="theme-delete-item-title" id="mob_del_modal_title">-</h6>
                    
                    <div class="theme-delete-info-card">
                        <div class="theme-delete-info-row">
                            <span class="text-muted"><i class="fa fa-graduation-cap mr-1 text-primary"></i> Course:</span>
                            <strong id="mob_del_modal_classes">-</strong>
                        </div>
                        <div class="theme-delete-info-row border-0 pb-0">
                            <span class="text-muted"><i class="fa fa-calendar mr-1 text-primary"></i> Exam Date:</span>
                            <span id="mob_del_modal_date">-</span>
                        </div>
                    </div>

                    <div class="theme-delete-alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span><strong>Warning:</strong> Associated schedules, student marks, and published results will be permanently removed.</span>
                    </div>
                </div>

                <div class="theme-delete-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger" id="mobBtnConfirmDelete">
                        <i class="fa fa-trash mr-1"></i> Delete Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    // Helper Notification function
    function showToast(type, msg) {
        if (typeof toastr !== 'undefined') {
            toastr[type](msg);
        } else if (typeof notify !== 'undefined') {
            notify(type, msg);
        } else {
            alert(msg);
        }
    }

    // 1. Result Publish Modal Handler
    $(document).on('click', '.openPublishModalBtn', function(e) {
        e.preventDefault();
        var btn = $(this);
        var examId = btn.data('id');
        var examName = btn.data('name');
        var classId = btn.data('class-id');
        var className = btn.data('class-name');
        var isPublished = btn.data('published') == 1;
        var pubDate = btn.data('date') || '{{ $date }}';

        $('#pub_modal_exam_id').val(examId);
        $('#pub_modal_class_id').val(classId);
        $('#pub_modal_exam_title').text(examName);
        $('#pub_modal_class_name').text(className);
        $('#pub_modal_date').val(pubDate);

        if (isPublished) {
            $('#pub_modal_status_badge').html('<span class="badge badge-success px-2 py-1" style="font-size:10.5px; background:#10b981;"><i class="fa fa-check-circle mr-1"></i> Published</span>');
            $('#pub_modal_btn_unpublish').show();
            $('#pub_modal_btn_submit').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-calendar-check-o mr-1"></i> Update Date').data('action', 'update_date');
        } else {
            $('#pub_modal_status_badge').html('<span class="badge badge-secondary px-2 py-1" style="font-size:10.5px; background:#94a3b8;"><i class="fa fa-clock-o mr-1"></i> Not Published</span>');
            $('#pub_modal_btn_unpublish').hide();
            $('#pub_modal_btn_submit').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-paper-plane mr-1"></i> Publish Result').data('action', 'publish');
        }

        $('#publishResultModal').modal('show');
    });

    // Submit Publish / Update Date
    $(document).on('click', '#pub_modal_btn_submit', function(e) {
        e.preventDefault();
        var btn = $(this);
        var examId = $('#pub_modal_exam_id').val();
        var classId = $('#pub_modal_class_id').val();
        var publishDate = $('#pub_modal_date').val();
        var action = btn.data('action') || 'publish';
        var originalHtml = btn.html();

        if (!publishDate) {
            showToast('error', 'Please select a publish date.');
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: '{{ route("exam.result.publish") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                exam_id: examId,
                class_type_id: classId,
                publish_date: publishDate,
                action: action
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                btn.prop('disabled', false).html(originalHtml);
                $('#publishResultModal').modal('hide');
                if (res.status === 'success') {
                    showToast('success', res.message);
                    setTimeout(function(){ window.location.reload(); }, 600);
                } else {
                    showToast('error', res.message || 'Failed to publish result.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error publishing result.';
                showToast('error', err);
            }
        });
    });

    // Unpublish Result
    $(document).on('click', '#pub_modal_btn_unpublish', function(e) {
        e.preventDefault();
        var btn = $(this);
        var examId = $('#pub_modal_exam_id').val();
        var classId = $('#pub_modal_class_id').val();
        var originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Unpublishing...');

        $.ajax({
            url: '{{ route("exam.result.reset-publication") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                exam_id: examId,
                class_type_id: classId
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                btn.prop('disabled', false).html(originalHtml);
                $('#publishResultModal').modal('hide');
                if (res.status === 'success') {
                    showToast('success', res.message);
                    setTimeout(function(){ window.location.reload(); }, 600);
                } else {
                    showToast('error', res.message || 'Failed to unpublish result.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                var err = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error unpublishing result.';
                showToast('error', err);
            }
        });
    });

    // 2. Dynamic Batch Loader for Duplicate Exam Modal
    function loadMobBatches(courseId, preselectedBatchId) {
        var $batch = $('#mob_copy_batch_id');
        $batch.html('<option value="">-- All Batches (Course-Wide) --</option>');
        if (!courseId) return;

        $.ajax({
            url: "{{ url('getBatchesByCourse') }}/" + courseId,
            type: 'GET',
            dataType: 'json',
            success: function(batches) {
                var html = '<option value="">-- All Batches (Course-Wide) --</option>';
                if (Array.isArray(batches) && batches.length > 0) {
                    batches.forEach(function(b) {
                        var shiftText = b.shift ? ' (' + b.shift + ')' : '';
                        var isSel = (preselectedBatchId && String(b.id) === String(preselectedBatchId)) ? ' selected' : '';
                        html += '<option value="' + b.id + '"' + isSel + '>' + b.name + shiftText + '</option>';
                    });
                }
                $batch.html(html);
            }
        });
    }

    $(document).on('click', '.copyExamData', function(e) {
        e.preventDefault();
        var examId = $(this).data('id');
        var examName = $(this).data('name');
        var examDate = $(this).data('date');
        var classId = $(this).data('class-id');
        var batchId = $(this).data('batch-id');

        $('#mob_copy_exam_id').val(examId);
        $('#mob_copy_exam_name').val(examName);
        $('#mob_copy_exam_date').val(examDate);
        if (classId) {
            $('#mob_copy_class_type_id').val(classId);
            loadMobBatches(classId, batchId);
        } else {
            $('#mob_copy_batch_id').html('<option value="">-- All Batches (Course-Wide) --</option>');
        }

        $('#copyExamModal').modal('show');
    });

    $('#mob_copy_class_type_id').on('change', function() {
        var courseId = $(this).val();
        loadMobBatches(courseId, null);
    });

    // AJAX Copy Exam
    $('#mobFormCopyExam').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#mobBtnSubmitCopy');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Duplicating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                $('#copyExamModal').modal('hide');

                if (res.status === 'success' || res.status === 1) {
                    showToast('success', res.message || 'Exam duplicated successfully.');
                    setTimeout(function(){ window.location.reload(); }, 600);
                } else {
                    showToast('error', res.message || 'Failed to duplicate exam.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                var errMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error duplicating exam.';
                showToast('error', errMessage);
            }
        });
    });

    // 3. Delete Modal Handler
    $(document).on('click', '.deleteExamBtn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name') || 'Unnamed Exam';
        var classes = $(this).data('class') || 'None';
        var dateVal = $(this).data('date') || '-';

        $('#mob_delete_exam_id').val(id);
        $('#mob_del_modal_title').text(name);
        $('#mob_del_modal_classes').text(classes);
        $('#mob_del_modal_date').text(dateVal);

        $('#deleteExamModal').modal('show');
    });

    // AJAX Delete Exam
    $('#mobFormDeleteExam').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#mobBtnConfirmDelete');
        var originalBtnHtml = submitBtn.html();
        var examId = $('#mob_delete_exam_id').val();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Deleting...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                $('#deleteExamModal').modal('hide');

                if (res.status === 'success' || res.status === 1) {
                    showToast('success', res.message || 'Exam deleted successfully.');
                    var card = $('.mob-exam-item[data-id="' + examId + '"]');
                    if (card.length) {
                        card.fadeOut(250, function() {
                            $(this).remove();
                            var remaining = $('.mob-exam-item:visible').length;
                            $('#mobVisibleExamCount').text(remaining);
                        });
                    } else {
                        setTimeout(function(){ window.location.reload(); }, 600);
                    }
                } else {
                    showToast('error', res.message || 'Failed to delete exam.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                var errMessage = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error deleting exam.';
                showToast('error', errMessage);
            }
        });
    });

    // 4. Real-Time Client Search
    $('#mobExamSearch').on('input keyup', function(){
        var query = $(this).val().toLowerCase().trim();
        var $cards = $('.mob-exam-item');
        var matchCount = 0;

        if (query === '') {
            $cards.show();
            matchCount = $cards.length;
            $('#mobNoMatchesCard').hide();
        } else {
            $cards.each(function(){
                var name = $(this).data('name') || '';
                var code = $(this).data('code') || '';
                var course = $(this).data('course') || '';
                var batch = $(this).data('batch') || '';
                var marks = $(this).data('marks-status') || '';

                if (name.indexOf(query) !== -1 || 
                    code.indexOf(query) !== -1 || 
                    course.indexOf(query) !== -1 || 
                    batch.indexOf(query) !== -1 || 
                    marks.indexOf(query) !== -1) {
                    $(this).show();
                    matchCount++;
                } else {
                    $(this).hide();
                }
            });

            if (matchCount === 0 && $cards.length > 0) {
                $('#mobNoMatchesCard').show();
            } else {
                $('#mobNoMatchesCard').hide();
            }
        }

        $('#mobVisibleExamCount').text(matchCount);
    });
});
</script>
@endsection

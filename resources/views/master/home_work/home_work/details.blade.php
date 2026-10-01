@php
    $permission = Helper::permissioncheck(10);
    $currentRoleId = Session::get('role_id');
    $isStudent = ($currentRoleId == 3);
    $hwTitle = $homework->title ?? 'Homework Submissions';
    $hwClass = $homework->ClassType->name ?? 'N/A';
    $hwSubject = $homework->Subject->name ?? 'N/A';
    $hwTeacher = 'Admin';
    if (!empty($homework->Teacher)) {
        $hwTeacher = trim(($homework->Teacher->first_name ?? '') . ' ' . ($homework->Teacher->last_name ?? ''));
    } elseif (!empty($homework->User)) {
        $hwTeacher = trim(($homework->User->first_name ?? '') . ' ' . ($homework->User->last_name ?? ''));
    }
    if (empty($hwTeacher)) {
        $hwTeacher = 'Admin';
    }
    $hwIssueDate = !empty($homework->homework_issue_date) ? date('d M Y', strtotime($homework->homework_issue_date)) : 'N/A';
    $hwDueDate = !empty($homework->submission_date) ? date('d M Y', strtotime($homework->submission_date)) : 'N/A';
    $totalStudents = count($students ?? []);
    $encodedHwDesc = base64_encode($homework->description ?? '');
@endphp
@extends('layout.app') 

@section('styles')
<style>
/* Viewport Fitting & Page Layout - Matching admissionView 1:1 */
.homework-details-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.homework-details-page * {
    box-sizing: border-box;
}
.details-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Compact Smart Hero Header (~42px) */
.details-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.hero-left-section {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.hero-title-box {
    display: flex;
    align-items: center;
    gap: 5px;
    min-width: 0;
    max-width: 480px;
    cursor: pointer;
    user-select: none;
}
.hero-title-box:hover .hero-hw-title {
    color: #93c5fd;
}
.hero-hw-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
    transition: color .15s;
}
.btn-hw-info {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #ffffff;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .2s ease;
    padding: 0;
}
.btn-hw-info:hover, .btn-hw-info.active {
    background: #38bdf8;
    border-color: #38bdf8;
    color: #002C54;
    box-shadow: 0 0 8px rgba(56, 189, 248, 0.5);
}

/* Sliding Homework Content Drawer */
.hw-slide-panel {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    box-shadow: 0 6px 16px rgba(0, 44, 84, 0.12);
    margin-bottom: 5px;
    flex-shrink: 0;
    border-left: 4px solid #002C54;
    overflow: hidden;
}
.hw-slide-panel-inner {
    padding: 10px 14px;
}
.hw-slide-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #002C54;
    line-height: 1.35;
    margin: 3px 0 6px 0;
    word-break: break-word;
}
.hw-slide-desc-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 8px 10px;
    max-height: 160px;
    overflow-y: auto;
    font-size: 11.5px;
    line-height: 1.5;
    color: #1e293b;
    word-break: break-word;
}
.hw-slide-desc-box p:last-child {
    margin-bottom: 0;
}
.btn-slide-close {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    padding: 2px 8px;
    border-radius: 2px;
    transition: all 0.15s;
    line-height: 1.4;
    white-space: nowrap;
}
.btn-slide-close:hover {
    color: #ef4444;
    background: #fee2e2;
    border-color: #fca5a5;
}
.hw-spec-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    font-size: 10.5px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    color: #334155;
    white-space: nowrap;
}
.hw-spec-chip strong {
    color: #0f172a;
}

/* Hero Chips & Badges */
.hero-chips-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.hero-chip {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #f1f5f9;
    white-space: nowrap;
}
.hero-chip strong {
    color: #ffffff;
}

/* Smart Summary Pills on Right */
.hero-right-section {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.metric-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
}
.pill-students {
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
}
.pill-checked {
    background: rgba(52, 211, 153, 0.15);
    border: 1px solid rgba(52, 211, 153, 0.35);
    color: #34d399;
}
.pill-pending {
    background: rgba(251, 191, 36, 0.15);
    border: 1px solid rgba(251, 191, 36, 0.35);
    color: #fbbf24;
}

.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1.4;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}

/* Main Table Card */
.details-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 0;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
}
.dash-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* Integrated Header Search Controls */
.header-filter-bar {
    display: flex;
    align-items: center;
    gap: 5px;
}
.search-control-input {
    height: 25px;
    padding: 2px 7px;
    font-size: 11px;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    width: 220px;
    color-scheme: dark;
}
.search-control-input::placeholder {
    color: rgba(255,255,255,.6);
}
.search-control-input:focus {
    background: #031426;
    border-color: #38bdf8;
    box-shadow: 0 0 0 1px #38bdf8;
}
.select-control-status {
    height: 25px;
    padding: 1px 5px;
    font-size: 11px;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    outline: none;
    cursor: pointer;
    color-scheme: dark;
}
.select-control-status option {
    background: #002C54;
    color: #ffffff;
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Design */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}
.dash-table thead th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    height: 36px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    vertical-align: middle;
    z-index: 22;
}

/* Sticky Action Column */
.fixed_action_head {
    position: sticky !important;
    right: 0;
    z-index: 25 !important;
    background: #002C54 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_col {
    position: sticky !important;
    right: 0;
    z-index: 5;
    background: inherit;
    box-shadow: -3px 0 6px rgba(0,0,0,.08);
}
.dash-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #f8fafc !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #edf2f7 !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e2e8f0 !important;
}

/* Table Body Rows */
.dash-table tbody td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) td {
    background: #edf2f7;
}
.dash-table tbody tr:hover td {
    background: #e2e8f0 !important;
}

/* Row Badges & Elements */
.student-name-box {
    display: flex;
    align-items: center;
    gap: 6px;
}
.student-avatar-badge {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #4338ca;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    font-weight: 700;
    flex-shrink: 0;
}
.student-name-text {
    font-weight: 600;
    color: #0f172a;
    font-size: 11.5px;
    line-height: 1.2;
}
.student-adm-hint {
    font-size: 10px;
    color: #64748b;
    display: block;
}
.badge-class {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
}
.badge-attempt-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.badge-eval-status {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.badge-eval-checked {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.badge-eval-pending {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
.btn-eval-action {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 600;
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3d;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none !important;
    white-space: nowrap;
}
.btn-eval-action:hover {
    background: #0284c7;
    border-color: #0284c7;
}

/* Bottom Toolbar */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    min-height: 32px;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
    font-weight: 500;
}

/* Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height) - 180px);
    padding: 35px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.empty-icon {
    font-size: 32px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
}
.empty-desc {
    font-size: 11px;
    color: #64748b;
}

/* Modals */
.modal-navy-header {
    background: #002C54;
    color: #ffffff;
    padding: 8px 14px;
    border-bottom: 1px solid #001f3d;
}
.modal-navy-header .modal-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.3;
}
.modal-navy-header .close-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.8;
}
.modal-navy-header .close-btn:hover {
    opacity: 1;
}
.hw-modal-prop-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 11.5px;
}
.hw-modal-prop-row:last-child {
    border-bottom: none;
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page homework-details-page">
    <div class="admission-page-layout details-page-layout">

        {{-- 1. Ultra-Compact Smart Hero Header (~42px, never overflows screen) --}}
        <div class="details-hero">
            <div class="hero-left-section">
                <a href="{{ url('homework/index') }}" class="dash-btn dash-btn-light" title="Back to Homework List">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                {{-- Truncated Title with Info Button (Triggers Smooth Slide Drawer) --}}
                <div class="hero-title-box" id="heroTitleBox" title="Click to view full instructions &amp; content">
                    <h1 class="hero-hw-title" title="{{ $hwTitle }}">
                        <i class="fa fa-flask mr-1 text-info"></i> {{ $hwTitle }}
                    </h1>
                    <button type="button" class="btn-hw-info" id="btnToggleHwInfo" title="Click to view full instructions &amp; details">
                        <i class="fa fa-info info-icon"></i>
                    </button>
                </div>

                {{-- Compact Meta Chips --}}
                <div class="hero-chips-wrap d-none d-md-flex">
                    <span class="hero-chip"><i class="fa fa-th-large mr-1"></i>Class: <strong>{{ $hwClass }}</strong></span>
                    <span class="hero-chip"><i class="fa fa-book mr-1"></i>Subject: <strong>{{ $hwSubject }}</strong></span>
                    <span class="hero-chip"><i class="fa fa-calendar-check-o mr-1"></i>Due: <strong>{{ $hwDueDate }}</strong></span>
                </div>
            </div>

            {{-- Smart Metric Badges & Actions --}}
            <div class="hero-right-section">
                @if(!$isStudent)
                    <a href="{{ url('homework/export-submissions/' . $id) }}" class="dash-btn dash-btn-light" title="Export Submissions Roster to CSV">
                        <i class="fa fa-download"></i> Export CSV
                    </a>
                @endif
                <span class="metric-pill pill-students" title="Unique Submitted Students">
                    <i class="fa fa-users"></i> <strong>{{ $statsSummary['total_students'] ?? 0 }}</strong> Submitted
                </span>
                <span class="metric-pill pill-checked" title="Checked / Evaluated Students">
                    <i class="fa fa-check-circle"></i> <strong>{{ $statsSummary['checked'] ?? 0 }}</strong> Checked
                </span>
                <span class="metric-pill pill-pending" title="Pending Evaluation">
                    <i class="fa fa-clock-o"></i> <strong>{{ $statsSummary['pending'] ?? 0 }}</strong> Pending
                </span>
            </div>
        </div>

        {{-- 1.1 Sliding Drawer for Full Title, Instructions & Specs --}}
        <div id="hwSlidePanel" class="hw-slide-panel" style="display: none;">
            <div class="hw-slide-panel-inner">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <div style="flex: 1; min-width: 0; padding-right: 12px;">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge" style="background: #002C54; color: #ffffff; font-size: 10px; padding: 3px 6px;">
                                <i class="fa fa-book mr-1"></i> Assignment Full Content
                            </span>
                            <span class="hw-spec-chip"><i class="fa fa-th-large text-muted"></i> Class: <strong>{{ $hwClass }}</strong></span>
                            <span class="hw-spec-chip"><i class="fa fa-bookmark text-muted"></i> Subject: <strong>{{ $hwSubject }}</strong></span>
                            <span class="hw-spec-chip"><i class="fa fa-user-circle text-muted"></i> Assigned By: <strong>{{ $hwTeacher }}</strong></span>
                            <span class="hw-spec-chip"><i class="fa fa-calendar text-muted"></i> Issue: <strong>{{ $hwIssueDate }}</strong></span>
                            <span class="hw-spec-chip text-danger"><i class="fa fa-clock-o"></i> Due: <strong>{{ $hwDueDate }}</strong></span>
                            @if(!empty($homework->target_duration))
                                <span class="hw-spec-chip"><i class="fa fa-hourglass-half text-muted"></i> Duration: <strong>{{ $homework->target_duration }}</strong></span>
                            @endif
                            @if(!empty($homework->content_file))
                                <a href="{{ asset('schoolimage/homework/' . $homework->content_file) }}" target="_blank" download class="btn btn-xs btn-outline-primary ml-1" style="font-size: 10.5px; padding: 1px 7px; font-weight: 600;">
                                    <i class="fa fa-download mr-1"></i> Download Reference Attachment
                                </a>
                            @endif
                        </div>
                        <h4 class="hw-slide-title">{{ $hwTitle }}</h4>
                    </div>
                    <button type="button" class="btn-slide-close" id="btnCloseSlidePanel" title="Close Content Drawer">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>

                <div class="hw-slide-desc-box">
                    <div class="text-muted font-weight-bold text-uppercase mb-1" style="font-size: 10px; letter-spacing: .04em;">
                        <i class="fa fa-align-left text-primary mr-1"></i> Description &amp; Detailed Instructions:
                    </div>
                    @if(!empty($homework->description))
                        {!! $homework->description !!}
                    @else
                        <span class="text-muted font-italic">No additional written instructions provided for this assignment.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. Full-Height Table Card with Integrated Header Filter Controls --}}
        <div class="dash-card details-table-card">
            <div class="dash-card-header">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Submissions &amp; Evaluation Grid</h3>
                    <span class="badge-total-records"><span id="filtered-count">{{ $totalStudents }}</span> Students</span>
                </div>

                {{-- Integrated Header Search & Filter Bar (Zero Vertical Wastage) --}}
                <div class="header-filter-bar">
                    @if(!$isStudent)
                        <button type="button" class="dash-btn" id="btnBulkEvaluate" style="display:none; background:#10b981; color:#fff; border:1px solid #059669; padding: 2px 9px; font-size: 11px; font-weight: 600;">
                            <i class="fa fa-check-circle"></i> Mark Checked (<span id="bulkCount">0</span>)
                        </button>
                    @endif

                    <input type="text" 
                           id="submissionSearchInput" 
                           class="search-control-input" 
                           placeholder="Filter student name, roll no, mobile..." 
                           autocomplete="off">

                    <select id="submissionStatusFilter" class="select-control-status">
                        <option value="">All Statuses</option>
                        <option value="checked">Checked Only</option>
                        <option value="pending">Pending Only</option>
                    </select>
                </div>
            </div>

            {{-- 3. Scrollable Table Viewport --}}
            <div class="table-scroll-container">
                <table class="dash-table" id="submissions-grid-table">
                    <thead>
                        <tr>
                            @if(!$isStudent)
                                <th style="width: 32px;" class="text-center">
                                    <input type="checkbox" id="checkAllSubmissions" title="Select All Students" style="cursor: pointer;">
                                </th>
                            @endif
                            <th style="width: 42px;" class="text-center">#</th>
                            <th style="min-width: 170px;">Student</th>
                            <th style="min-width: 100px;">Mobile</th>
                            <th style="min-width: 120px;">Father's Name</th>
                            <th style="min-width: 95px;">Class / Batch</th>
                            <th style="width: 100px;" class="text-center">Attempts</th>
                            <th style="min-width: 125px;">Last Submitted</th>
                            <th style="min-width: 130px;" class="text-center">Evaluation Status</th>
                            <th style="width: 110px;" class="text-center fixed_action_head">{{ __('common.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="submissions-table-body">
                        @if(!empty($students) && count($students) > 0)
                            @php $i = 1; @endphp
                            @foreach ($students as $type)
                                @php
                                    $admId = $type->admission_id;
                                    $studentName = trim(($type->Admission->first_name ?? '') . ' ' . ($type->Admission->last_name ?? ''));
                                    if (empty($studentName)) {
                                        $studentName = 'Student #' . $admId;
                                    }
                                    $admNo = $type->Admission->admissionNo ?? '';
                                    $mobile = $type->Admission->mobile ?? '-';
                                    $fatherName = $type->Admission->father_name ?? '-';
                                    $className = $type->ClassType->name ?? ($type->Admission->ClassTypes->name ?? '-');

                                    // Evaluation counts (0 N+1 queries)
                                    $stats = $docStats[$admId] ?? null;
                                    $totalDocs = $stats->total_docs ?? 0;
                                    $checkedDocs = $stats->checked_docs ?? 0;
                                    $isChecked = ($totalDocs > 0 && $totalDocs == $checkedDocs);

                                    // Attempts
                                    $attempts = $attemptStats[$admId]->total_attempts ?? 1;
                                    $lastDate = !empty($type->submission_date) ? date('d M Y, h:i A', strtotime($type->submission_date)) : '-';
                                    $statusSlug = $isChecked ? 'checked' : 'pending';
                                @endphp
                                <tr class="sub-row" 
                                    data-adm-id="{{ $admId }}"
                                    data-name="{{ strtolower($studentName) }}" 
                                    data-adm="{{ strtolower($admNo) }}" 
                                    data-mobile="{{ strtolower($mobile) }}" 
                                    data-father="{{ strtolower($fatherName) }}" 
                                    data-status="{{ $statusSlug }}">
                                    
                                    @if(!$isStudent)
                                        <td class="text-center">
                                            <input type="checkbox" class="bulk-sub-check" value="{{ $admId }}" data-name="{{ $studentName }}" style="cursor: pointer;">
                                        </td>
                                    @endif

                                    {{-- Sr. No. --}}
                                    <td class="text-center font-weight-bold text-muted row-index">{{ $i++ }}</td>

                                    {{-- Student Name & Adm No --}}
                                    <td>
                                        <div class="student-name-box">
                                            <div class="student-avatar-badge">
                                                {{ strtoupper(substr($studentName, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="student-name-text">{{ $studentName }}</span>
                                                @if(!empty($admNo))
                                                    <span class="student-adm-hint">Adm: {{ $admNo }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Mobile --}}
                                    <td>{{ $mobile }}</td>

                                    {{-- Father's Name --}}
                                    <td>{{ $fatherName }}</td>

                                    {{-- Class / Batch --}}
                                    <td>
                                        <span class="badge-class">{{ $className }}</span>
                                    </td>

                                    {{-- Attempts Badge --}}
                                    <td class="text-center">
                                        <span class="badge-attempt-pill" title="{{ $attempts }} submission attempt(s) made by this student">
                                            <i class="fa fa-paperclip mr-1"></i> {{ $attempts }} {{ $attempts == 1 ? 'Attempt' : 'Attempts' }}
                                        </span>
                                    </td>

                                    {{-- Last Submitted Date --}}
                                    <td class="text-nowrap">
                                        <span class="font-weight-600 text-dark">{{ $lastDate }}</span>
                                    </td>

                                    {{-- Evaluation Status --}}
                                    <td class="text-center">
                                        @if($totalDocs == 0)
                                            <span class="badge-eval-status badge-eval-pending"><i class="fa fa-clock-o"></i> No Docs</span>
                                        @elseif($isChecked)
                                            <span class="badge-eval-status badge-eval-checked" title="All {{ $totalDocs }} document(s) evaluated">
                                                <i class="fa fa-check"></i> Checked ({{ $checkedDocs }}/{{ $totalDocs }})
                                            </span>
                                        @else
                                            <span class="badge-eval-status badge-eval-pending" title="{{ $checkedDocs }} of {{ $totalDocs }} evaluated">
                                                <i class="fa fa-clock-o"></i> {{ $checkedDocs }}/{{ $totalDocs }} Checked
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Sticky Action --}}
                                    <td class="text-center fixed_action_col">
                                        <button type="button" 
                                                class="btn-eval-action viewModal" 
                                                data-homework_id="{{ $type->homework_id }}" 
                                                data-admission_id="{{ $type->admission_id }}"
                                                data-student_name="{{ $studentName }}"
                                                title="View &amp; Evaluate Homework">
                                            <i class="fa fa-eye"></i> View &amp; Review
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="empty-sub-row">
                                <td colspan="{{ !$isStudent ? 10 : 9 }}" class="p-0">
                                    <div class="dash-empty-state">
                                        <div class="empty-icon"><i class="fa fa-users"></i></div>
                                        <div class="empty-title">No Submissions Found</div>
                                        <div class="empty-desc">No students have submitted this homework assignment yet.</div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- 4. Pinned Bottom Footer Bar --}}
            <div class="dash-card-footer table-pagination-bar">
                <div class="pagination-info">
                    Showing <span id="visible-count" class="font-weight-bold text-white">{{ $totalStudents }}</span> of <span class="font-weight-bold text-white">{{ $totalStudents }}</span> student submission(s)
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-white-50" style="font-size: 10.5px;">
                        Evaluation: <strong class="text-success">{{ $statsSummary['checked'] ?? 0 }} Checked</strong> &bull; <strong class="text-warning">{{ $statsSummary['pending'] ?? 0 }} Pending</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal A: Homework Info & Attachment Details Modal --}}
<div class="modal fade" id="homeworkInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 2px; overflow: hidden; border: 1px solid #001f3d;">
            <div class="modal-header modal-navy-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title mb-0"><i class="fa fa-info-circle mr-1"></i> Assignment Content &amp; Instructions</h5>
                <button type="button" class="close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-3" style="background: #ffffff;">
                <div class="mb-3">
                    <h5 style="color: #002C54; font-weight: 700; font-size: 14px; margin-bottom: 4px;">{{ $hwTitle }}</h5>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge badge-primary px-2 py-1">{{ $hwClass }}</span>
                        <span class="badge badge-info px-2 py-1">{{ $hwSubject }}</span>
                        <span class="text-muted" style="font-size: 11px;"><i class="fa fa-calendar mr-1"></i> Due: {{ $hwDueDate }}</span>
                    </div>
                </div>

                <div class="p-2 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2px;">
                    <label class="text-muted mb-1 d-block font-weight-bold" style="font-size: 10.5px; text-transform: uppercase;">Instructions:</label>
                    <div style="font-size: 11.5px; color: #1e293b; max-height: 220px; overflow-y: auto;">
                        @if(!empty($homework->description))
                            {!! $homework->description !!}
                        @else
                            <p class="text-muted font-italic mb-0">No instructions or description provided.</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="hw-modal-prop-row">
                            <span class="text-muted">Assigned By:</span>
                            <strong>{{ $hwTeacher }}</strong>
                        </div>
                        <div class="hw-modal-prop-row">
                            <span class="text-muted">Issue Date:</span>
                            <strong>{{ $hwIssueDate }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="hw-modal-prop-row">
                            <span class="text-muted">Due Date:</span>
                            <strong class="text-danger">{{ $hwDueDate }}</strong>
                        </div>
                        @if(!empty($homework->content_file))
                            <div class="mt-2">
                                <label class="text-muted mb-1 d-block" style="font-size: 10.5px; font-weight: 600;"><i class="fa fa-paperclip mr-1"></i> Original Assignment File</label>
                                <a href="{{ asset('schoolimage/homework/' . $homework->content_file) }}" target="_blank" download class="dash-btn w-100 justify-content-center" style="background: #002C54; color: #fff !important; border: 1px solid #001f3d;">
                                    <i class="fa fa-download mr-1"></i> Download Attachment
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2" style="background: #f8fafc; border-top: 1px solid #cbd5e1;">
                <button type="button" class="dash-btn" style="background: #e2e8f0; color: #1e293b;" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal B: Homework Evaluation & Review Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 3px; overflow: hidden; border: 1px solid #001f3d; box-shadow: 0 10px 25px rgba(0,0,0,.25);">
            <div class="modal-header modal-navy-header d-flex align-items-center justify-content-between" style="padding: 6px 12px;">
                <div class="d-flex align-items-center">
                    <span class="badge badge-light text-navy font-weight-bold mr-2" style="font-size: 9.5px; padding: 2px 6px; letter-spacing:.03em;">REVIEW PORTAL</span>
                    <h5 class="modal-title mb-0" style="font-size: 12.5px;">
                        <i class="fa fa-graduation-cap mr-1 text-info"></i> Student Assignment: <span id="fillStuName" class="text-warning font-weight-bold"></span>
                    </h5>
                </div>
                <button type="button" class="close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="homework_list" style="background: #eef2f6; min-height: 280px; padding: 8px 12px;">
                <div class="d-flex flex-column align-items-center justify-content-center w-100" style="min-height: 260px;">
                    <div style="width: 46px; height: 46px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,44,84,0.12); margin-bottom: 10px;">
                        <i class="fa fa-circle-o-notch fa-spin text-primary" style="font-size: 22px; margin-right: 0 !important;"></i>
                    </div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #002C54; margin-bottom: 2px;">Loading Student Submissions...</div>
                    <div style="font-size: 11px; color: #64748b;">Please wait while we prepare the evaluation workspace</div>
                </div>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-between" style="background: #f8fafc; border-top: 1px solid #cbd5e1; padding: 4px 12px;">
                <span class="text-muted font-italic" style="font-size: 10.5px;">
                    <i class="fa fa-info-circle text-info mr-1"></i> Teachers can evaluate submissions, grade marks, and navigate across students seamlessly.
                </span>
                <button type="button" class="dash-btn" style="background: #e2e8f0; color: #1e293b; border: 1px solid #cbd5e1; padding: 2px 10px; font-size: 11px;" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal C: Document / Image Preview Carousel Modal --}}
<div class="modal fade" id="viewModal2" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 2px; overflow: hidden; border: 1px solid #001f3d;">
            <div class="modal-header modal-navy-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title mb-0"><i class="fa fa-file-text-o mr-1"></i> Uploaded Assignment Document</h5>
                <button type="button" class="close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-2 text-center" id="homework_list_view" style="background: #1e293b; min-height: 450px;">
                <div id="document_preview_container" style="display: flex; align-items: center; justify-content: center; min-height: 450px;">
                    {{-- Dynamically injected image or iframe --}}
                </div>
            </div>
            <div class="modal-footer p-2" style="background: #0f172a; border-top: 1px solid #334155;">
                <button type="button" class="dash-btn" style="background: #334155; color: #f1f5f9;" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var baseUrl = "{{ url('/') }}";
    var colSpanCount = {{ !$isStudent ? 10 : 9 }};

    // Toggle Full Assignment Content Sliding Drawer with Smooth Animation
    $(document).on('click', '#btnToggleHwInfo, #heroTitleBox, #btnCloseSlidePanel', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $panel = $('#hwSlidePanel');
        var $btn = $('#btnToggleHwInfo');

        $panel.stop(true, true).slideToggle(280, function() {
            if ($panel.is(':visible')) {
                $btn.addClass('active').attr('title', 'Click to collapse assignment content');
                $btn.find('.info-icon').removeClass('fa-info').addClass('fa-chevron-up');
            } else {
                $btn.removeClass('active').attr('title', 'Click to view full instructions & details');
                $btn.find('.info-icon').removeClass('fa-chevron-up').addClass('fa-info');
            }
        });
    });

    // Client-side real-time filter for submissions table
    function filterSubmissionRows() {
        var searchTerm = $('#submissionSearchInput').val().toLowerCase().trim();
        var statusFilter = $('#submissionStatusFilter').val();
        var visibleCount = 0;

        $('.sub-row').each(function() {
            var $row = $(this);
            var name = $row.data('name') || '';
            var adm = $row.data('adm') || '';
            var mobile = $row.data('mobile') || '';
            var father = $row.data('father') || '';
            var status = $row.data('status') || '';

            var matchesSearch = !searchTerm || (
                name.indexOf(searchTerm) !== -1 ||
                adm.indexOf(searchTerm) !== -1 ||
                mobile.indexOf(searchTerm) !== -1 ||
                father.indexOf(searchTerm) !== -1
            );

            var matchesStatus = !statusFilter || (status === statusFilter);

            if (matchesSearch && matchesStatus) {
                $row.show();
                visibleCount++;
                $row.find('.row-index').text(visibleCount);
            } else {
                $row.hide();
            }
        });

        $('#filtered-count').text(visibleCount);
        $('#visible-count').text(visibleCount);

        if (visibleCount === 0) {
            if ($('#no-filter-match-row').length === 0) {
                $('#submissions-table-body').append(
                    '<tr id="no-filter-match-row"><td colspan="' + colSpanCount + '" class="p-0">' +
                    '<div class="dash-empty-state" style="min-height: 200px; padding: 25px;">' +
                    '<div class="empty-icon"><i class="fa fa-filter"></i></div>' +
                    '<div class="empty-title">No Matching Submissions</div>' +
                    '<div class="empty-desc">No student submissions match your current search criteria.</div>' +
                    '</div></td></tr>'
                );
            }
            $('#no-filter-match-row').show();
        } else {
            $('#no-filter-match-row').remove();
        }

        updateBulkButton();
    }

    $('#submissionSearchInput').on('input', filterSubmissionRows);
    $('#submissionStatusFilter').on('change', filterSubmissionRows);

    // Bulk Selection & Action Handler
    function updateBulkButton() {
        var checkedCount = $('.bulk-sub-check:checked').length;
        $('#bulkCount').text(checkedCount);
        if (checkedCount > 0) {
            $('#btnBulkEvaluate').fadeIn(150);
        } else {
            $('#btnBulkEvaluate').fadeOut(150);
        }
    }

    $(document).on('change', '#checkAllSubmissions', function() {
        var isChecked = $(this).is(':checked');
        $('.sub-row:visible .bulk-sub-check').prop('checked', isChecked);
        updateBulkButton();
    });

    $(document).on('change', '.bulk-sub-check', function() {
        var totalVisible = $('.sub-row:visible .bulk-sub-check').length;
        var checkedVisible = $('.sub-row:visible .bulk-sub-check:checked').length;
        $('#checkAllSubmissions').prop('checked', totalVisible > 0 && totalVisible === checkedVisible);
        updateBulkButton();
    });

    $(document).on('click', '#btnBulkEvaluate', function() {
        var studentIds = [];
        $('.bulk-sub-check:checked').each(function() {
            studentIds.push($(this).val());
        });

        if (studentIds.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        if (!confirm('Are you sure you want to mark submissions for ' + studentIds.length + ' selected student(s) as Checked?')) {
            return;
        }

        var $btn = $(this);
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: baseUrl + '/homework/bulk-evaluate',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                homework_id: "{{ $id }}",
                student_ids: studentIds
            },
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.status === 'success' || res.status === true) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message, 'Bulk Evaluation');
                    } else {
                        alert(res.message);
                    }
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(res.message, 'Error');
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Bulk evaluation failed.';
                if (typeof toastr !== 'undefined') {
                    toastr.error(err, 'Error');
                } else {
                    alert(err);
                }
            }
        });
    });

    // Helper: Load student homework details into review portal modal
    function loadStudentHomework(homework_id, admission_id, student_name) {
        if (student_name) {
            $('#fillStuName').text(student_name);
        }
        $('#homework_list').html(
            '<div class="d-flex flex-column align-items-center justify-content-center w-100" style="min-height: 260px;">' +
                '<div style="width: 46px; height: 46px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,44,84,0.12); margin-bottom: 10px;">' +
                    '<i class="fa fa-circle-o-notch fa-spin text-primary" style="font-size: 22px; margin-right: 0 !important;"></i>' +
                '</div>' +
                '<div style="font-size: 12.5px; font-weight: 700; color: #002C54; margin-bottom: 2px;">Loading Student Submissions...</div>' +
                '<div style="font-size: 11px; color: #64748b;">Please wait while we prepare the evaluation workspace</div>' +
            '</div>'
        );

        $.ajax({
            url: baseUrl + '/particular/hw/details',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                admission_id: admission_id,
                homework_id: homework_id
            },
            success: function(data) {
                $('#homework_list').html(data);
                var loadedName = $('#stuName').data('first_name');
                if (loadedName) {
                    $('#fillStuName').text(loadedName);
                }
            },
            error: function(xhr) {
                $('#homework_list').html('<div class="alert alert-danger mb-0">Failed to load assignment details. Please try again.</div>');
            }
        });
    }

    // View Modal Trigger
    $(document).on('click', ".viewModal", function() {
        var admission_id = $(this).data("admission_id");
        var homework_id = $(this).data("homework_id");
        var student_name = $(this).data("student_name") || '';

        $('#viewModal').modal('show');
        loadStudentHomework(homework_id, admission_id, student_name);
    });

    // Next / Prev Student Navigation inside Modal
    $(document).on('click', '.btn-nav-student', function(e) {
        e.preventDefault();
        var admId = $(this).data('admission_id');
        var hwId = $(this).data('homework_id');
        var sName = $(this).data('student_name') || '';
        if (admId) {
            loadStudentHomework(hwId, admId, sName);
        }
    });

    // View Document in Modal 2
    $(document).on('click', ".viewModal2", function() {
        var src = $(this).data("href");
        if (!src) return;

        var ext = src.split('.').pop().toLowerCase();
        var html = "";

        if (ext === "pdf") {
            html = '<iframe src="' + src + '" width="100%" height="600" frameborder="0" style="border-radius:2px; background:#fff;"></iframe>';
        } else {
            html = '<img src="' + src + '" class="img-fluid" style="max-width:100%; max-height:600px; border-radius:2px; object-fit:contain;" alt="Assignment File">';
        }

        $('#document_preview_container').html(html);
        $('#viewModal2').modal('show');
    });

    // Submit Review / Feedback / Marks
    $(document).on('click', ".submitReview, .submitReviewAndNext", function() {
        var $btn = $(this);
        var isAndNext = $btn.hasClass('submitReviewAndNext');
        var nextAdmId = $btn.data('next_admission_id');
        var hwId = $btn.data('homework_id');
        var submit_id = $btn.data('submit');
        var numItems = $('.submit_' + submit_id).length;
        var review = [];
        var id = [];
        var marks = [];

        for (var i = 0; i < numItems; i++) {
            review[i] = $('.submit_' + submit_id).eq(i).val();
            id[i] = $('.submit_' + submit_id).eq(i).data("id");
            marks[i] = $('.marks_' + submit_id).eq(i).length ? $('.marks_' + submit_id).eq(i).val() : null;
        }

        var origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            type: "POST",
            url: baseUrl + "/evaluate/homework",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                review: review,
                id: id,
                marks: marks
            },
            success: function(response) {
                toastr.success('Review and marks saved successfully.');
                $btn.prop('disabled', false).html('Saved <i class="fa fa-check"></i>').removeClass('btn-primary').addClass('btn-success');

                if (isAndNext && nextAdmId && hwId) {
                    setTimeout(function() {
                        loadStudentHomework(hwId, nextAdmId, '');
                    }, 400);
                }
            },
            error: function(xhr) {
                toastr.error('Failed to submit review.');
                $btn.prop('disabled', false).html(origText);
            }
        });
    });
});
</script>
@endsection

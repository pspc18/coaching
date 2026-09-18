@php
    $classType = $classType ?? Helper::classType();
    $getsubject = Helper::getSubject();
    $date = date('Y-m-d');
    $permission = Helper::permissioncheck(8);

    $stats = $stats ?? [
        'total' => $totalCount ?? count($data ?? []),
        'assigned' => 0,
        'published' => 0,
        'pending' => 0,
    ];
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $totalCount = $totalCount ?? count($data ?? []);
    $lastPage = $lastPage ?? 1;
    $startIndex = ($currentPage - 1) * ($perPage === 'all' ? 0 : (int)$perPage);
@endphp

@extends('layout.app') 

@section('title', 'Exams Management - Examination Control')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EXAMS MANAGEMENT (SIGNATURE THEME)
   Aligned with studentlist (admissionView) & viewUser guidelines:
   - Font family: Segoe UI, -apple-system, Roboto, sans-serif
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 26px-30px inputs with #cbd5e1 / #051e38 in-column filters
   - High performance, server-side real-time AJAX pagination & live filtering
   ========================================================================== */

.exam-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.exam-page-wrapper * {
    box-sizing: border-box;
}

/* 1. Signature Hero Banner */
.exam-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 6px;
}
.exam-hero-text {
    display: flex;
    flex-direction: column;
}
.exam-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.exam-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.exam-subtitle {
    font-size: 11px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.exam-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none !important;
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid transparent;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3f !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    border-color: rgba(255,255,255,0.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.22);
    color: #ffffff !important;
    border-color: #ffffff;
}

/* 2. KPI Summary Strip */
.exam-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 6px;
}
@media (max-width: 992px) {
    .exam-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
.kpi-mini-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    border-left: 3px solid #002C54;
    cursor: pointer;
    transition: all 0.15s ease;
}
.kpi-mini-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.08);
}
.kpi-mini-card.active-kpi {
    background: #e6f0fa;
    border-color: #002C54;
}
.kpi-mini-card.kpi-published { border-left-color: #10b981; }
.kpi-mini-card.kpi-classes { border-left-color: #0284c7; }
.kpi-mini-card.kpi-pending { border-left-color: #f59e0b; }

.kpi-mini-card .kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 1px;
}
.kpi-mini-card .kpi-val {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.1;
    color: #1e293b;
}
.kpi-mini-card .kpi-icon {
    width: 26px;
    height: 26px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

/* 3. Workflow Step Strip */
.exam-workflow-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 12px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.workflow-pill {
    padding: 3px 9px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.workflow-pill:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.workflow-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #001f3f;
}

/* 4. Table Card - Dark Navy Header */
.exam-table-card {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.12);
    overflow: hidden;
    margin-bottom: 8px;
    position: relative;
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
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

/* Scrollable Container */
.table-scroll-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: calc(100vh - 295px);
    background: #ffffff;
    position: relative;
}

/* Loading Overlay */
.table-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.7);
    z-index: 30;
    display: none;
    align-items: center;
    justify-content: center;
}
.table-loading-spinner {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #002C54;
    color: #ffffff;
    border-radius: 2px;
    font-weight: 700;
    font-size: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Dual-Row Sticky Table */
.erp-table {
    width: 100%;
    margin-bottom: 0;
    font-size: 11.5px;
    border-collapse: separate;
    border-spacing: 0;
}
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54 !important;
    color: #ffffff !important;
    padding: 8px 6px;
    height: 36px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
    text-align: center;
}
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c !important;
    color: #ffffff !important;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
}

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    color-scheme: dark;
}
.excel-col-filter::placeholder {
    color: rgba(255,255,255,.6);
}
.excel-col-filter:focus {
    background: #031426 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
}
select.excel-col-filter {
    background-color: #051e38 !important;
    color: #ffffff !important;
    cursor: pointer;
}
select.excel-col-filter option {
    background-color: #002C54 !important;
    color: #ffffff !important;
}

/* Reset / Clear Button */
.btn-reset-filters {
    height: 26px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .15s;
    white-space: nowrap;
    width: 100%;
}
.btn-reset-filters:hover {
    background: #002C54;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Sticky Action Column */
.fixed_action_head {
    position: sticky !important;
    right: 0;
    z-index: 25 !important;
    background: #002C54 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_filter {
    position: sticky !important;
    right: 0;
    z-index: 24 !important;
    background: #08335c !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_col {
    position: sticky !important;
    right: 0;
    z-index: 5;
    background: inherit;
    box-shadow: -3px 0 6px rgba(0,0,0,.08);
}
.erp-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #ffffff !important;
}
.erp-table tbody tr:nth-child(even) .fixed_action_col {
    background: #f8fafc !important;
}
.erp-table tbody tr:hover .fixed_action_col {
    background: #e6f0fa !important;
}

/* Table Rows & Cells */
.erp-table tbody td {
    padding: 6px 8px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11.5px;
}
.erp-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}
.erp-table tbody tr:hover td {
    background-color: #e6f0fa;
}

/* Assigned Class & Date Badges */
.exam-date-group {
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 4px 6px;
    background: #ffffff;
    margin-bottom: 3px;
}
.exam-date-group:last-child {
    margin-bottom: 0;
}
.exam-date-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 2px;
}
.exam-class-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
.exam-class-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #1e293b;
}
.exam-class-pill.published {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d;
}

/* Action Buttons */
.action-btn-group {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.act-btn {
    width: 25px;
    height: 25px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid transparent;
    font-size: 11px;
    text-decoration: none !important;
    transition: all 0.15s ease;
    cursor: pointer;
}
.act-btn-assign { background: #002C54; color: #fff !important; border-color: #001f3f; }
.act-btn-assign:hover { background: #0f3460; }

.act-btn-marks { background: #10b981; color: #fff !important; border-color: #059669; }
.act-btn-marks:hover { background: #059669; }

.act-btn-excel { background: #0284c7; color: #fff !important; border-color: #0369a1; }
.act-btn-excel:hover { background: #0369a1; }

.act-btn-copy { background: #64748b; color: #fff !important; border-color: #475569; }
.act-btn-copy:hover { background: #475569; }

.act-btn-edit { background: #f59e0b; color: #fff !important; border-color: #d97706; }
.act-btn-edit:hover { background: #d97706; }

.act-btn-delete { background: #ef4444; color: #fff !important; border-color: #dc2626; }
.act-btn-delete:hover { background: #dc2626; }

.btn-publish-trigger {
    height: 24px;
    padding: 0 7px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

/* Pinned Bottom Pagination Toolbar (Matching StudentList & ViewUser) */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 38px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info {
    font-size: 11.5px;
    color: #cbd5e1;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 5px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 11px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 2px 6px;
    font-size: 11px;
    outline: none;
    cursor: pointer;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 26px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7;
    border-color: #0284c7;
}
.page-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}
.page-current-indicator {
    font-size: 11px;
    font-weight: 600;
    padding: 0 6px;
    color: #f1f5f9;
}

@media(max-width:768px) {
    .table-pagination-bar {
        flex-direction: column;
        gap: 6px;
        height: auto;
        padding: 6px 8px;
    }
    .pagination-controls {
        width: 100%;
        justify-content: space-between;
    }
}

/* Modal Styling */
.modal-header-navy {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 0;
    padding: 8px 14px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper exam-page-wrapper">
    
    {{-- 1. Signature Hero Banner --}}
    <div class="exam-hero">
        <div class="exam-hero-text">
            <span class="exam-kicker"><i class="fa fa-graduation-cap mr-1"></i> Examination Control &amp; Scheduling</span>
            <h1 class="exam-title">
                <i class="fa fa-leanpub"></i> Exams Management
            </h1>
            <p class="exam-subtitle">
                Create offline exams, assign class schedules, fill marks, and publish results to student portals.
            </p>
        </div>
        <div class="exam-hero-actions">
            @if($permission->add ?? true)
                <a href="{{ url('add/exam') }}" class="dash-btn dash-btn-light">
                    <i class="fa fa-plus-circle text-primary"></i> Add Exam
                </a>
            @endif
            <a href="{{ url('add/examination_schedule') }}" class="dash-btn dash-btn-outline" title="Manage Date Sheets">
                <i class="fa fa-calendar"></i> Schedules
            </a>
            <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                <i class="fa fa-file-excel-o"></i> Excel Import
            </a>
            <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Fill Student Marks">
                <i class="fa fa-pencil-square-o"></i> Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                <i class="fa fa-bar-chart"></i> Reports
            </a>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success') || session('message'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- 2. KPI Summary Strip (Clickable Fast Filter) --}}
    <div class="exam-kpi-strip">
        <div class="kpi-mini-card filter-kpi-btn" data-status-filter="" title="Click to show all exams">
            <div>
                <div class="kpi-label">Total Exams</div>
                <div class="kpi-val" id="statTotalVal">{{ $stats['total'] }}</div>
            </div>
            <div class="kpi-icon" style="background:#f1f5f9; color:#002C54;">
                <i class="fa fa-leanpub"></i>
            </div>
        </div>
        <div class="kpi-mini-card kpi-classes filter-kpi-btn" data-status-filter="assigned" title="Click to show exams with assigned classes">
            <div>
                <div class="kpi-label">Assigned Classes</div>
                <div class="kpi-val" id="statAssignedVal" style="color:#0284c7;">{{ $stats['assigned'] }}</div>
            </div>
            <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                <i class="fa fa-tags"></i>
            </div>
        </div>
        <div class="kpi-mini-card kpi-published filter-kpi-btn" data-status-filter="published" title="Click to filter published exams">
            <div>
                <div class="kpi-label">Published Results</div>
                <div class="kpi-val" id="statPublishedVal" style="color:#15803d;">{{ $stats['published'] }}</div>
            </div>
            <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div class="kpi-mini-card kpi-pending filter-kpi-btn" data-status-filter="pending" title="Click to filter pending publication">
            <div>
                <div class="kpi-label">Pending Publication</div>
                <div class="kpi-val" id="statPendingVal" style="color:#b45309;">{{ $stats['pending'] }}</div>
            </div>
            <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>

    {{-- 3. Workflow Step Indicator --}}
    <div class="exam-workflow-card">
        <div class="d-flex align-items-center" style="gap:6px;">
            <i class="fa fa-random text-primary"></i>
            <strong style="font-size:12px; color:#002C54;">Examination Lifecycle Workflow:</strong>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap:5px;">
            <a href="{{ url('view/exam') }}" class="workflow-pill active">
                <i class="fa fa-check-circle"></i> 1. Exam Setup
            </a>
            <a href="{{ url('fill-marks-by-excel') }}" class="workflow-pill">
                2. Excel Import
            </a>
            <a href="{{ url('fill_marks') }}" class="workflow-pill">
                3. Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="workflow-pill">
                4. Exam Report
            </a>
        </div>
    </div>

    {{-- 4. ERP Table Card (Exact StudentList / ViewUser Structure) --}}
    <div class="exam-table-card">
        
        {{-- Card Header Bar --}}
        <div class="dash-card-header">
            <div class="dash-card-title">
                <i class="fa fa-table text-info mr-1"></i> Examination Records List
            </div>
            <div class="d-flex align-items-center" style="gap:8px;">
                <span class="badge-total-records">
                    Total <strong id="visibleExamCount" class="text-white">{{ $totalCount }}</strong> Exams
                </span>
            </div>
        </div>

        {{-- Scrollable Table --}}
        <div class="table-scroll-container">
            
            {{-- Real-time Loading Spinner --}}
            <div class="table-loading-overlay" id="tableLoadingOverlay">
                <div class="table-loading-spinner">
                    <i class="fa fa-spinner fa-spin"></i> Loading examination records...
                </div>
            </div>

            <table class="erp-table" id="examDataTable">
                <thead>
                    {{-- Row 1: Header Titles --}}
                    <tr class="header-titles-row">
                        <th style="width: 45px;">S.No.</th>
                        <th style="text-align: left; padding-left: 10px; min-width: 240px;">Exam Name</th>
                        <th style="text-align: left; padding-left: 10px; min-width: 280px;">Assigned Classes &amp; Dates</th>
                        <th style="width: 150px;">Result Status</th>
                        <th class="fixed_action_head" style="width: 170px;">Actions</th>
                    </tr>

                    {{-- Row 2: In-Column Excel Filters --}}
                    <tr class="excel-filter-row">
                        <th style="text-align: center; color: rgba(255,255,255,0.6); font-weight: normal;">#</th>
                        
                        {{-- Filter: Exam Name --}}
                        <th>
                            <input type="text" class="excel-col-filter col-filter-name" placeholder="Filter exam name..." autocomplete="off">
                        </th>

                        {{-- Filter: Assigned Class --}}
                        <th>
                            <select class="excel-col-filter col-filter-class">
                                <option value="">All Classes</option>
                                @foreach($classType ?? [] as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                @endforeach
                            </select>
                        </th>

                        {{-- Filter: Status --}}
                        <th>
                            <select class="excel-col-filter col-filter-status" id="selectStatusFilter">
                                <option value="">All Status</option>
                                <option value="published">Published</option>
                                <option value="pending">Pending</option>
                                <option value="assigned">Assigned</option>
                                <option value="unassigned">Unassigned</option>
                            </select>
                        </th>

                        {{-- Filter: Reset --}}
                        <th class="fixed_action_filter">
                            <button type="button" class="btn-reset-filters" id="btnResetFilters" title="Reset all column filters">
                                <i class="fa fa-refresh mr-1"></i> Reset
                            </button>
                        </th>
                    </tr>
                </thead>

                <tbody id="examTableBody">
                    @include('examination.offline_exam.exam.table_rows', ['data' => $data, 'startIndex' => $startIndex, 'permission' => $permission])
                </tbody>
            </table>
        </div>

        {{-- Pinned Bottom Pagination Toolbar (Matching StudentList & ViewUser) --}}
        <div class="table-pagination-bar">
            <div class="pagination-info">
                Showing <strong id="pageStart" class="text-white">{{ $totalCount === 0 ? 0 : ($startIndex + 1) }}</strong> to <strong id="pageEnd" class="text-white">{{ min($startIndex + count($data), $totalCount) }}</strong> of <strong id="totalVisibleExams" class="text-white">{{ $totalCount }}</strong> records
            </div>
            <div class="pagination-controls">
                <div class="rows-per-page-selector">
                    <label for="perPageSelect">Rows per page:</label>
                    <select id="perPageSelect">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="pagination-nav">
                    <button type="button" class="page-btn" id="btnFirstPage" {{ $currentPage <= 1 ? 'disabled' : '' }} title="First Page"><i class="fa fa-angle-double-left"></i></button>
                    <button type="button" class="page-btn" id="btnPrevPage" {{ $currentPage <= 1 ? 'disabled' : '' }} title="Previous Page"><i class="fa fa-angle-left"></i></button>
                    <span class="page-current-indicator">
                        Page <strong id="currentPageNum" class="text-white">{{ $currentPage }}</strong> of <strong id="totalPageNum" class="text-white">{{ $lastPage }}</strong>
                    </span>
                    <button type="button" class="page-btn" id="btnNextPage" {{ $currentPage >= $lastPage ? 'disabled' : '' }} title="Next Page"><i class="fa fa-angle-right"></i></button>
                    <button type="button" class="page-btn" id="btnLastPage" {{ $currentPage >= $lastPage ? 'disabled' : '' }} title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Container for dynamically rendered publish modals --}}
<div id="dynamicModalsContainer">
    @include('examination.offline_exam.exam.modals', ['data' => $data])
</div>

{{-- Copy Exam Modal --}}
<div class="modal fade" id="copyExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:2px; border:1px solid #cbd5e1; overflow:hidden;">
            <form action="{{ url('copy/exam') }}" method="post">
                @csrf
                <div class="modal-header modal-header-navy">
                    <h5 class="modal-title font-weight-bold" style="font-size:13.5px;">
                        <i class="fa fa-copy mr-1 text-info"></i> Duplicate / Copy Exam
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="copy_exam_id" name="exam_id">
                    
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700; color:#334155;">New Exam Title</label>
                        <input type="text" class="form-control" id="copy_exam_name" name="name" maxlength="255" required style="height:30px; font-size:12px; border-radius:2px; border:1px solid #cbd5e1;">
                    </div>
                    
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700; color:#334155;">Exam Date</label>
                        <input type="date" class="form-control" id="copy_exam_date" name="exam_date" required style="height:30px; font-size:12px; border-radius:2px; border:1px solid #cbd5e1;">
                    </div>
                    
                    <small class="text-muted d-block mt-2" style="font-size:10.5px;">
                        <i class="fa fa-info-circle mr-1"></i> The new duplicated exam will inherit assigned classes and schedules. Marks will remain unpopulated for fresh scoring.
                    </small>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:2px; font-size:11.5px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" style="border-radius:2px; font-size:11.5px; background:#002C54; border-color:#001f3f;">
                        <i class="fa fa-copy mr-1"></i> Copy Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:2px; border:1px solid #cbd5e1; overflow:hidden;">
            <form action="{{ url('delete/exam') }}" method="post"> 
                @csrf
                <div class="modal-header bg-danger text-white py-2 px-3" style="border-radius:0;">
                    <h5 class="modal-title font-weight-bold" style="font-size:13.5px;">
                        <i class="fa fa-trash mr-1"></i> Confirm Delete Exam
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="delete_exam_id" name="delete_id">
                    <div class="font-weight-bold text-dark mb-1" style="font-size:13px;" id="deleteExamNameDisplay">Are you sure you want to delete this exam?</div>
                    <p class="text-muted mb-0" style="font-size:11.5px;">
                        This will permanently delete the examination record, assigned classes, and any entered mark entries. This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" style="border-radius:2px; font-size:11.5px;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger" style="border-radius:2px; font-size:11.5px;">
                        <i class="fa fa-trash mr-1"></i> Delete Permanently
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
    // Bind Delete Data (Delegated)
    $(document).on('click', '.deleteData', function() {
        var id = $(this).data('id');
        var name = $(this).data('name') || 'this exam';
        $('#delete_exam_id').val(id);
        $('#deleteExamNameDisplay').text('Are you sure you want to permanently delete "' + name + '"?');
    });

    // Bind Copy Data (Delegated)
    $(document).on('click', '.copyExamData', function() {
        $('#copy_exam_id').val($(this).data('id'));
        $('#copy_exam_name').val($(this).data('name'));
        $('#copy_exam_date').val($(this).data('date'));
    });

    // Server-Side Real-Time Pagination & Filter State
    var currentPage = {{ $currentPage }};
    var perPage = "{{ $perPage }}";
    var lastPage = {{ $lastPage }};
    var searchTimer = null;

    function fetchExams(page) {
        if (page !== undefined) {
            currentPage = page;
        }

        var nameVal = $('.col-filter-name').val();
        var termVal = $('.col-filter-term').val();
        var classVal = $('.col-filter-class').val();
        var statusVal = $('.col-filter-status').val();

        $('#tableLoadingOverlay').css('display', 'flex');

        $.ajax({
            url: "{{ url('view/exam') }}",
            type: "GET",
            data: {
                name: nameVal,
                term_id: termVal,
                class_type_id: classVal,
                status: statusVal,
                page: currentPage,
                per_page: perPage
            },
            dataType: "json",
            success: function(res) {
                $('#tableLoadingOverlay').hide();
                if (res.status === 'success') {
                    // Update rows & modals
                    $('#examTableBody').html(res.html);
                    $('#dynamicModalsContainer').html(res.modals_html);

                    // Update pagination info
                    currentPage = res.current_page;
                    lastPage = res.last_page;
                    perPage = res.per_page;

                    $('#pageStart').text(res.from);
                    $('#pageEnd').text(res.to);
                    $('#totalVisibleExams').text(res.total);
                    $('#visibleExamCount').text(res.total);
                    $('#currentPageNum').text(res.current_page);
                    $('#totalPageNum').text(res.last_page);

                    // Update Button states
                    $('#btnFirstPage, #btnPrevPage').prop('disabled', res.current_page <= 1);
                    $('#btnNextPage, #btnLastPage').prop('disabled', res.current_page >= res.last_page || res.total === 0);

                    // Update KPI counters
                    if (res.stats) {
                        $('#statTotalVal').text(res.stats.total);
                        $('#statAssignedVal').text(res.stats.assigned);
                        $('#statPublishedVal').text(res.stats.published);
                        $('#statPendingVal').text(res.stats.pending);
                    }
                }
            },
            error: function() {
                $('#tableLoadingOverlay').hide();
            }
        });
    }

    // Debounced Search on Exam Name
    $('.col-filter-name').on('input keyup', function(){
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function(){
            fetchExams(1);
        }, 300);
    });

    // Dropdown Filters Change
    $('.col-filter-class, .col-filter-status').on('change', function(){
        fetchExams(1);
    });

    // KPI Cards Click Filter
    $('.filter-kpi-btn').on('click', function(){
        var filterVal = $(this).data('status-filter');
        $('.filter-kpi-btn').removeClass('active-kpi');
        $(this).addClass('active-kpi');
        $('#selectStatusFilter').val(filterVal);
        fetchExams(1);
    });

    // Rows Per Page Selector
    $('#perPageSelect').on('change', function(){
        perPage = $(this).val();
        fetchExams(1);
    });

    // Pagination Nav Controls
    $('#btnFirstPage').on('click', function(){
        if (currentPage > 1) {
            fetchExams(1);
        }
    });

    $('#btnPrevPage').on('click', function(){
        if (currentPage > 1) {
            fetchExams(currentPage - 1);
        }
    });

    $('#btnNextPage').on('click', function(){
        if (currentPage < lastPage) {
            fetchExams(currentPage + 1);
        }
    });

    $('#btnLastPage').on('click', function(){
        if (currentPage < lastPage) {
            fetchExams(lastPage);
        }
    });

    // Reset All Filters
    $('#btnResetFilters').on('click', function(){
        $('.col-filter-name').val('');
        $('.col-filter-class').val('');
        $('.col-filter-status').val('');
        $('.filter-kpi-btn').removeClass('active-kpi');
        fetchExams(1);
    });
});
</script>
@endsection
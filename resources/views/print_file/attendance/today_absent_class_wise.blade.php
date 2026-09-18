@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - CLASS-WISE ABSENT STUDENTS THEME
   - Exact Alignment with StudentList (admissionView) & UserView (viewUser)
   - Fixed Viewport Height Layout (calc(100vh - var(--header-height) - 16px))
   - Auto-adjusting Table Viewport with Sticky Header (.table-scroll-container)
   - Perfectly Centered Empty State (.dash-empty-state)
   - In-Column Excel Filter Row (.excel-filter-row)
   - Pinned Bottom Pagination Toolbar (.table-pagination-bar)
   ========================================================================== */

:root {
    --header-height: 56px;
}

.absent-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.absent-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjusting Layout */
.absent-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 8px 0;
}

/* Top Hero Header Banner */
.absent-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.absent-hero-text {
    display: flex;
    flex-direction: column;
}
.absent-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.absent-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.absent-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.absent-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 7px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    font-size: 10.5px;
    color: #ffffff;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 11.5px;
}
.hero-stat-badge.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
}
.hero-stat-badge.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}
.hero-stat-badge.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}

/* Hero Action Buttons */
.absent-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 26px;
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
.dash-btn-pdf {
    background: #f43f5e;
    color: #ffffff !important;
    border-color: #e11d48;
}
.dash-btn-pdf:hover {
    background: #e11d48;
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* Filter Card */
.filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.filter-card-header {
    background: #f8fafc;
    padding: 4px 10px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 11px;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-card-body {
    padding: 6px 10px;
}

.filter-form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 8px;
}
.filter-group-date {
    width: 210px;
}
.filter-group-main {
    flex: 1;
    min-width: 260px;
}
.filter-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-helper-links {
    font-size: 9.5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.filter-helper-link {
    color: #0284c7;
    text-decoration: none !important;
    font-weight: 600;
}
.filter-helper-link:hover {
    text-decoration: underline !important;
}
.date-preset-btn {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 5px;
    font-size: 9px;
    color: #334155;
    cursor: pointer;
    font-weight: 600;
    line-height: 1.2;
}
.date-preset-btn:hover {
    background: #e2e8f0;
    color: #002C54;
}

.filter-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.btn-filter-submit {
    height: 28px;
    padding: 0 12px;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #002C54;
    border-radius: 2px;
    font-weight: 700;
    font-size: 11px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
}
.btn-filter-submit:hover {
    background: #001833;
    border-color: #001833;
}
.btn-filter-reset {
    height: 28px;
    padding: 0 9px;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 11px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
}
.btn-filter-reset:hover {
    background: #e2e8f0;
    color: #334155;
}

/* Select2 Tweaks */
.select2-container--default .select2-selection--multiple {
    border-color: #cbd5e1 !important;
    border-radius: 2px !important;
    min-height: 28px !important;
    padding: 0 4px !important;
    font-size: 11px !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #002C54 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #002C54 !important;
    border: 1px solid #001f3d !important;
    color: #ffffff !important;
    border-radius: 2px !important;
    padding: 1px 6px !important;
    font-size: 10.5px !important;
    margin-top: 2px !important;
    margin-right: 3px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #ffffff !important;
    margin-right: 3px !important;
}

/* ==========================================================================
   AUTO-ADJUSTING TABLE CARD & SCROLL CONTAINER
   ========================================================================== */

.dash-table-card {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    overflow: hidden;
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
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
    font-size: 11.5px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

.table-header-tools {
    display: flex;
    align-items: center;
    gap: 6px;
}
.table-search-input {
    height: 24px;
    padding: 0 8px 0 24px;
    font-size: 11px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    outline: none;
    width: 170px;
    transition: all .15s ease;
}
.table-search-input::placeholder {
    color: #cbd5e1;
}
.table-search-input:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
    width: 210px;
}
.table-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.table-search-wrap i {
    position: absolute;
    left: 7px;
    color: #cbd5e1;
    font-size: 10px;
    pointer-events: none;
}

/* Scrollable Table Viewport - Auto fills available vertical screen height */
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

/* thead Titles Row */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 8px;
    height: 36px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
    box-sizing: border-box;
}

/* Sticky Filter Row with In-Column Filters (Dark Navy Theme) */
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c;
    color: #ffffff;
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
    height: 25px;
    padding: 2px 6px;
    font-size: 10.5px;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    background: #051e38;
    color: #ffffff;
    outline: none;
    box-sizing: border-box;
    transition: all .15s ease;
}
.excel-col-filter::placeholder {
    color: #94a3b8;
    font-size: 10px;
}
.excel-col-filter:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #38bdf8;
    box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
}
select.excel-col-filter {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='6' viewBox='0 0 8 6'%3E%3Cpath fill='%2394a3b8' d='M0 0l4 4 4-4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    padding-right: 18px;
    appearance: none;
}
select.excel-col-filter option {
    background: #002C54;
    color: #ffffff;
}

.btn-reset-filters {
    height: 25px;
    width: 25px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 63, 94, 0.2);
    border: 1px solid rgba(244, 63, 94, 0.4);
    color: #fecdd3;
    border-radius: 2px;
    cursor: pointer;
    font-size: 10.5px;
    transition: all .15s;
}
.btn-reset-filters:hover {
    background: #f43f5e;
    color: #ffffff;
    border-color: #f43f5e;
}

/* Table Body - Soft Slate Alternating Colors */
.dash-table tbody tr {
    background: #ffffff;
    transition: background .1s ease;
}
.dash-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-table tbody tr:hover {
    background: #edf2f7 !important;
}

.dash-table tbody td {
    padding: 5px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
    font-size: 11px;
}
.dash-table tbody td:last-child {
    border-right: none;
}

/* Badges & Typography inside Table */
.badge-class {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 2px;
}
.student-avatar-micro {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
    margin-right: 6px;
    flex-shrink: 0;
}
.student-name {
    font-weight: 700;
    color: #002C54;
}
.badge-absent-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 6px;
    font-size: 9.5px;
    font-weight: 700;
    background: #ffe4e6;
    color: #e11d48;
    border: 1px solid #fecdd3;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.phone-clickable {
    color: #0f172a;
    font-weight: 600;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.phone-clickable:hover {
    color: #0284c7;
}
.wa-mini-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 2px;
    background: #25D366;
    color: #ffffff !important;
    font-size: 10.5px;
    margin-left: 5px;
    text-decoration: none !important;
    transition: opacity .15s;
}
.wa-mini-btn:hover {
    opacity: .85;
}

/* Pinned Bottom Pagination Toolbar (Exact match with userView & admissionView) */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 36px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 5px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 10.5px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 4px;
    font-size: 10.5px;
    outline: none;
    cursor: pointer;
}
.rows-per-page-selector select option {
    background: #002C54;
    color: #ffffff;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 25px;
    height: 23px;
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
    font-size: 10.5px;
    font-weight: 600;
    padding: 0 5px;
    color: #f1f5f9;
}

/* Centered Empty State (Auto centered in available viewport) */
#empty-state-row td, .empty-row td, .no-match-row td {
    padding: 0 !important;
    border: none !important;
    background: #ffffff !important;
    vertical-align: middle !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height, 56px) - 230px);
    padding: 30px 16px;
    text-align: center;
    color: #475569;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 42px;
    margin-bottom: 10px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 4px;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 440px;
    line-height: 1.5;
}

@media(max-width:768px) {
    .absent-page-layout {
        height: auto;
        overflow: visible;
        padding: 4px;
    }
    .dash-empty-state {
        min-height: 250px;
    }
    .table-scroll-container {
        max-height: 500px;
    }
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
</style>
@endsection

@section('content')
<div class="content-wrapper absent-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="absent-page-layout">
                
                {{-- 1. Signature Hero Header Banner --}}
                <div class="absent-hero">
                    <div class="absent-hero-text">
                        <span class="absent-kicker">Attendance Management &bull; Absentee Report</span>
                        <h1 class="absent-title">
                            <i class="fa fa-user-times text-warning"></i> Class-wise Absent Students Report
                        </h1>
                        <p class="absent-subtitle">
                            Class-wise filtered preview of absent students for {{ $reportDate->format('l, d F Y') }} with instant PDF download.
                        </p>
                    </div>

                    <div class="absent-hero-stats">
                        <div class="hero-stat-badge badge-rose">
                            <i class="fa fa-user-times"></i> Total Absent: <b id="statTotalAbsent">{{ $absentCount }}</b>
                        </div>
                        <div class="hero-stat-badge badge-sky">
                            <i class="fa fa-calendar"></i> {{ $reportDate->format('d M Y') }}
                        </div>
                        <div class="hero-stat-badge badge-amber">
                            <i class="fa fa-th-list"></i> Classes: <b>{{ !empty($selectedClassIds) ? count($selectedClassIds) . ' Selected' : 'All Classes' }}</b>
                        </div>
                    </div>

                    <div class="absent-hero-actions">
                        @if(count($rows) > 0)
                            <a href="{{ route('attendance.today.absent-class-wise.pdf', ['date' => $reportDate->format('Y-m-d'), 'class_type_id' => $selectedClassIds]) }}" 
                               target="_blank" 
                               class="dash-btn dash-btn-pdf"
                               title="Download official PDF report">
                                <i class="fa fa-file-pdf-o"></i> Download PDF
                            </a>
                        @endif
                        <a href="{{ url('studentsAttendanceAdd') }}" class="dash-btn dash-btn-light" title="Go to Mark Student Attendance">
                            <i class="fa fa-calendar-check-o"></i> Mark Attendance
                        </a>
                        <a href="{{ url('dashboard') }}" class="dash-btn dash-btn-outline" title="Back to Dashboard">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </div>
                </div>

                {{-- 2. Date & Class Selection Filter Card --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span><i class="fa fa-filter mr-1"></i> Date &amp; Class Selection Filters</span>
                        <span style="font-size: 10px; font-weight: normal; opacity: .85;">Pick any date &amp; filter multiple classes</span>
                    </div>
                    <div class="filter-card-body">
                        <form method="GET" action="{{ route('attendance.today.absent-class-wise') }}" id="absentFilterForm">
                            <div class="filter-form-row">
                                
                                {{-- Date Picker --}}
                                <div class="filter-group-date">
                                    <div class="filter-label">
                                        <span>Select Date:</span>
                                        <div class="filter-helper-links">
                                            <button type="button" class="date-preset-btn" onclick="setDatePreset('{{ now()->toDateString() }}')">Today</button>
                                            <button type="button" class="date-preset-btn" onclick="setDatePreset('{{ now()->subDay()->toDateString() }}')">Yesterday</button>
                                        </div>
                                    </div>
                                    <input type="date" name="date" id="reportDateInput" value="{{ $reportDate->format('Y-m-d') }}" 
                                           class="form-control form-control-sm" style="height:28px; font-size:11.5px; border-radius:2px; border:1px solid #cbd5e1;">
                                </div>

                                {{-- Class Selection Dropdown --}}
                                <div class="filter-group-main">
                                    <div class="filter-label">
                                        <span>Select Target Class(es):</span>
                                        <div class="filter-helper-links">
                                            <a href="javascript:void(0);" id="btnSelectAllClasses" class="filter-helper-link">Select All</a>
                                            <span>&bull;</span>
                                            <a href="javascript:void(0);" id="btnDeselectAllClasses" class="filter-helper-link">Clear Selection</a>
                                        </div>
                                    </div>
                                    <select name="class_type_id[]" id="classSelectDropdown" class="form-control select2" multiple data-placeholder="Choose one or more classes to view...">
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ in_array($class->id, $selectedClassIds) ? 'selected' : '' }}>
                                                 {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Actions --}}
                                <div class="filter-actions">
                                    <button type="submit" class="btn-filter-submit">
                                        <i class="fa fa-search mr-1"></i> Filter Data
                                    </button>
                                    
                                    @if(!empty($selectedClassIds) || $reportDate->format('Y-m-d') !== now()->toDateString())
                                        <a href="{{ route('attendance.today.absent-class-wise') }}" class="btn-filter-reset" title="Clear all filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </a>
                                    @endif

                                    @if(count($rows) > 0)
                                        <a href="{{ route('attendance.today.absent-class-wise.pdf', ['date' => $reportDate->format('Y-m-d'), 'class_type_id' => $selectedClassIds]) }}" 
                                           target="_blank" 
                                           class="dash-btn dash-btn-pdf" 
                                           style="height: 28px;">
                                            <i class="fa fa-download mr-1"></i> PDF Export
                                        </a>
                                        <button type="button" class="btn-filter-reset" onclick="downloadAbsentCSV()" style="height:28px; font-weight:700; color:#002C54;">
                                            <i class="fa fa-file-excel-o mr-1 text-success"></i> CSV Export
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. Exact Unified Table Card with Dual Sticky Header & Pinned Pagination --}}
                <div class="dash-table-card">
                    <div class="dash-card-header">
                        <div class="dash-card-title">
                            <i class="fa fa-list-ol"></i>
                            <span>Absent Students List</span>
                            <span class="badge-total-records">
                                Date: {{ $reportDate->format('d M Y') }} &bull; Total: <span id="visibleRecordCount">{{ count($rows) }}</span>
                            </span>
                        </div>

                        <div class="table-header-tools">
                            <div class="table-search-wrap">
                                <i class="fa fa-search"></i>
                                <input type="text" id="liveTableSearch" class="table-search-input" placeholder="Quick search in table...">
                            </div>
                        </div>
                    </div>

                    <div class="table-scroll-container">
                        <table class="dash-table" id="absentDataTable">
                            <thead>
                                {{-- Top Header Titles Row --}}
                                <tr class="header-titles-row">
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="width: 120px;">Class</th>
                                    <th style="width: 110px; text-align: center;">Adm No</th>
                                    <th style="min-width: 170px;">Student Name</th>
                                    <th style="min-width: 150px;">Father's Name</th>
                                    <th style="width: 145px;">Contact Mobile</th>
                                    <th style="width: 95px; text-align: center;">Status</th>
                                </tr>

                                {{-- In-Column Excel Filter Row --}}
                                <tr class="excel-filter-row">
                                    <th style="text-align: center;">
                                        <button type="button" class="btn-reset-filters" id="btnResetInColFilters" title="Clear all column filters">
                                            <i class="fa fa-eraser"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" data-col-index="1">
                                            <option value="">All Classes</option>
                                            @php
                                                $uniqueClasses = $rows->pluck('class_name')->filter()->unique()->sort();
                                            @endphp
                                            @foreach($uniqueClasses as $uClass)
                                                <option value="{{ $uClass }}">{{ $uClass }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" placeholder="Search Adm..." data-col-index="2">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" placeholder="Search Name..." data-col-index="3">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" placeholder="Search Father..." data-col-index="4">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" placeholder="Search Mobile..." data-col-index="5">
                                    </th>
                                    <th style="text-align: center;">
                                        <span style="font-size: 10px; font-weight: 700; color: #cbd5e1;">ABSENT</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $index => $row)
                                    <tr class="table-data-row" 
                                        data-class="{{ strtolower($row['class_name'] ?? '') }}" 
                                        data-adm="{{ strtolower($row['admission_no'] ?? '') }}" 
                                        data-name="{{ strtolower($row['name'] ?? '') }}" 
                                        data-father="{{ strtolower($row['father_name'] ?? '') }}" 
                                        data-mobile="{{ strtolower($row['mobile'] ?? '') }}" 
                                        data-status="{{ strtolower($row['status'] ?? '') }}">
                                        <td style="text-align: center; font-weight: 700; color: #64748b;" class="row-index">
                                            {{ $index + 1 }}
                                        </td>
                                        <td>
                                            <span class="badge-class">{{ $row['class_name'] ?: 'N/A' }}</span>
                                        </td>
                                        <td style="text-align: center; font-weight: 700;">
                                            <code>{{ $row['admission_no'] ?: 'N/A' }}</code>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center;">
                                                <span class="student-avatar-micro">
                                                    {{ strtoupper(substr($row['name'] ?: 'S', 0, 1)) }}
                                                </span>
                                                <span class="student-name">{{ $row['name'] ?: '-' }}</span>
                                            </div>
                                        </td>
                                        <td style="color: #475569;">
                                            {{ $row['father_name'] ?: '-' }}
                                        </td>
                                        <td>
                                            @if(!empty($row['mobile']))
                                                <div style="display: inline-flex; align-items: center;">
                                                    <a href="tel:{{ $row['mobile'] }}" class="phone-clickable">
                                                        <i class="fa fa-phone text-muted" style="font-size: 10.5px;"></i>
                                                        <span>{{ $row['mobile'] }}</span>
                                                    </a>
                                                    <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $row['mobile']) }}" 
                                                       target="_blank" 
                                                       class="wa-mini-btn" 
                                                       title="Send WhatsApp message">
                                                        <i class="fa fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge-absent-pill">
                                                <i class="fa fa-times-circle mr-1"></i> {{ $row['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-state-row" class="empty-row">
                                        <td colspan="7" class="p-0 border-0">
                                            @if($isHoliday)
                                                <div class="dash-empty-state">
                                                    <div class="empty-icon text-info"><i class="fa fa-calendar-check-o"></i></div>
                                                    <div class="empty-title" style="color: #0284c7;">School Holiday / Weekend</div>
                                                    <div class="empty-desc">{{ $reportDate->format('d F Y') }} is marked as a holiday or event day. Attendance is not marked.</div>
                                                </div>
                                            @else
                                                <div class="dash-empty-state">
                                                    <div class="empty-icon text-success"><i class="fa fa-check-circle"></i></div>
                                                    <div class="empty-title" style="color: #16a34a;">No Absent Students Found!</div>
                                                    <div class="empty-desc">All students are present or no attendance entries recorded as Absent for {{ $reportDate->format('d F Y') }}.</div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Pinned Bottom Pagination Toolbar (Exact match with userView & studentlist) --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ count($rows) > 0 ? 1 : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min(25, count($rows)) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ count($rows) }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" disabled><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" disabled><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page"><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
</div>

@section('scripts')
<script>
function setDatePreset(dateStr) {
    document.getElementById('reportDateInput').value = dateStr;
    document.getElementById('absentFilterForm').submit();
}

function downloadAbsentCSV() {
    var table = document.getElementById("absentDataTable");
    if (!table) return;

    var csv = [];
    var rows = table.querySelectorAll("tbody tr.table-data-row");
    if (rows.length === 0) {
        rows = table.querySelectorAll("tbody tr");
    }
    csv.push(['"#"','"Class"','"Admission No"','"Student Name"','"Father Name"','"Mobile"','"Status"'].join(","));

    for (var i = 0; i < rows.length; i++) {
        var cols = rows[i].querySelectorAll("td");
        if (cols.length < 7) continue;
        var row = [];
        for (var j = 0; j < cols.length; j++) {
            var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            row.push('"' + text.replace(/"/g, '""') + '"');
        }
        csv.push(row.join(","));
    }
    var csv_string = csv.join("\n");
    var filename = "absent_students_" + document.getElementById('reportDateInput').value + ".csv";
    var link = document.createElement("a");
    link.style.display = "none";
    link.setAttribute("href", 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function() {
    if ($.fn.select2) {
        $('#classSelectDropdown').select2({
            width: '100%',
            placeholder: 'Choose one or more classes to view...'
        });
    }

    // Select All Classes Helper
    $('#btnSelectAllClasses').on('click', function(e) {
        e.preventDefault();
        $('#classSelectDropdown option').prop('selected', true);
        $('#classSelectDropdown').trigger('change');
    });

    // Deselect All Classes Helper
    $('#btnDeselectAllClasses').on('click', function(e) {
        e.preventDefault();
        $('#classSelectDropdown option').prop('selected', false);
        $('#classSelectDropdown').trigger('change');
    });

    /* ==========================================================
       PAGINATION & IN-COLUMN FILTERING ENGINE
       ========================================================== */
    var currentPage = 1;
    var pageSize = 25; // default 25

    function updateTablePagination() {
        var $rows = $("#absentDataTable tbody tr.table-data-row");
        if ($rows.length === 0) {
            $('#page-start').text(0);
            $('#page-end').text(0);
            $('#total-records').text(0);
            $('#visibleRecordCount').text(0);
            $('#current-page').text(1);
            $('#total-pages').text(1);
            $('#btn-first, #btn-prev, #btn-next, #btn-last').prop('disabled', true);
            return;
        }

        // Collect in-column filters
        var filters = [];
        $('.excel-col-filter').each(function() {
            var colIdx = $(this).data('col-index');
            var val = $(this).val().toLowerCase().trim();
            if (val !== '') {
                filters.push({ colIndex: colIdx, query: val });
            }
        });

        var globalSearch = $('#liveTableSearch').val().toLowerCase().trim();
        var matchingRows = [];

        $rows.each(function() {
            var $row = $(this);
            var $cols = $row.find('td');
            if ($cols.length < 7) return;

            var matches = true;

            // Global search
            if (globalSearch !== '') {
                if ($row.text().toLowerCase().indexOf(globalSearch) === -1) {
                    matches = false;
                }
            }

            // In-column filters
            if (matches && filters.length > 0) {
                for (var i = 0; i < filters.length; i++) {
                    var cellText = $cols.eq(filters[i].colIndex).text().toLowerCase().trim();
                    if (cellText.indexOf(filters[i].query) === -1) {
                        matches = false;
                        break;
                    }
                }
            }

            if (matches) {
                matchingRows.push($row);
            }
        });

        var totalMatching = matchingRows.length;
        var effectivePageSize = pageSize === -1 ? totalMatching : pageSize;
        var totalPages = effectivePageSize > 0 ? Math.max(1, Math.ceil(totalMatching / effectivePageSize)) : 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }

        var startIndex = (currentPage - 1) * effectivePageSize;
        var endIndex = pageSize === -1 ? totalMatching : Math.min(startIndex + effectivePageSize, totalMatching);

        // Hide all rows first
        $rows.hide();
        $('#absentDataTable tbody tr.no-match-row').remove();

        if (totalMatching === 0) {
            $('#absentDataTable tbody').append(
                '<tr class="no-match-row"><td colspan="7" class="p-0 border-0">' +
                '<div class="dash-empty-state">' +
                '<div class="empty-icon text-muted"><i class="fa fa-filter"></i></div>' +
                '<div class="empty-title">No Matching Absent Records Found</div>' +
                '<div class="empty-desc">Try adjusting or clearing your active filters.</div>' +
                '</div>' +
                '</td></tr>'
            );
        } else {
            for (var i = startIndex; i < endIndex; i++) {
                var $r = matchingRows[i];
                $r.show();
                $r.find('.row-index').text(i + 1);
            }
        }

        // Update indicators
        $('#page-start').text(totalMatching === 0 ? 0 : startIndex + 1);
        $('#page-end').text(endIndex);
        $('#total-records').text(totalMatching);
        $('#visibleRecordCount').text(totalMatching);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        // Button state
        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || totalPages <= 1);
    }

    // Rows per page selector
    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        if (val === 'all') {
            pageSize = -1;
        } else {
            pageSize = parseInt(val, 10);
        }
        currentPage = 1;
        updateTablePagination();
    });

    // Pagination navigation buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            updateTablePagination();
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateTablePagination();
        }
    });

    $('#btn-next').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage++;
            updateTablePagination();
        }
    });

    $('#btn-last').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage = totalPages;
            updateTablePagination();
        }
    });

    // Filters and search events
    $('.excel-col-filter').on('keyup change', function() {
        currentPage = 1;
        updateTablePagination();
    });

    $('#liveTableSearch').on('keyup', function() {
        currentPage = 1;
        updateTablePagination();
    });

    $('#btnResetInColFilters').on('click', function() {
        $('.excel-col-filter').val('');
        $('#liveTableSearch').val('');
        currentPage = 1;
        updateTablePagination();
    });

    // Initial run
    updateTablePagination();
});
</script>
@endsection
@endsection
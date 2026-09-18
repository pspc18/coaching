@php
    $setting = Helper::getSetting();
@endphp
@extends('layout.app')

@section('content')

<style>
/* ==========================================================================
   ARISE ERP - STUDENT APP LOGIN LOGS THEME
   Strictly matched with studentlist (admissionView) & stream_update
   ========================================================================== */

:root {
    --header-height: 56px;
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-card-head: #002342;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
}

.logs-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.logs-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjust Container */
.logs-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 0;
}

/* 1. Signature Hero Header Banner (Exact match with studentlist & stream_update) */
.logs-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, .12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.logs-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.logs-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.logs-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.logs-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.hero-stat-chip {
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
.hero-stat-chip b {
    font-weight: 700;
    font-size: 11.5px;
}
.hero-stat-chip.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}
.hero-stat-chip.badge-emerald {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #d1fae5;
}
.hero-stat-chip.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}
.hero-stat-chip.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
}

/* Action buttons */
.logs-hero-actions {
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
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.4);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border-color: #ffffff;
}

/* 2. Main Table Card & Header */
.logs-table-card {
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
    padding: 4px 8px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-wrap: wrap;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}

/* Inline Compact Filter Toolbar inside Card Header */
.header-filter-form {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    margin: 0;
}
.hdr-filter-select, .hdr-filter-input {
    height: 25px;
    padding: 2px 6px;
    font-size: 11px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    outline: none;
    transition: all .15s;
}
.hdr-filter-select {
    cursor: pointer;
    min-width: 130px;
}
.hdr-filter-select option {
    background: #002C54;
    color: #ffffff;
}
.hdr-filter-input {
    min-width: 140px;
}
.hdr-filter-input::placeholder {
    color: rgba(255,255,255,.6);
}
.hdr-filter-select:focus, .hdr-filter-input:focus {
    border-color: #38bdf8;
    background: #031426;
}
.hdr-btn-submit {
    height: 25px;
    padding: 0 8px;
    background: #0284c7;
    color: #ffffff;
    border: 1px solid #0284c7;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: background .15s;
}
.hdr-btn-submit:hover {
    background: #0369a1;
}
.hdr-btn-reset {
    height: 25px;
    padding: 0 6px;
    background: rgba(255,255,255,.1);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    font-size: 10.5px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .15s;
}
.hdr-btn-reset:hover {
    background: rgba(255,255,255,.2);
    color: #ffffff;
}

/* 3. Status Filter Tabs (Matching stream_update / admissionView) */
.student-status-tabs {
    display: flex;
    align-items: center;
    background: #001f3d;
    border-bottom: 1px solid rgba(255,255,255,.1);
    padding: 2px 8px;
    gap: 4px;
    flex-shrink: 0;
    overflow-x: auto;
}
.status-tab-btn {
    background: transparent;
    border: 1px solid transparent;
    color: #94a3b8;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .12s ease;
    white-space: nowrap;
}
.status-tab-btn:hover {
    color: #ffffff;
    background: rgba(255,255,255,.06);
}
.status-tab-btn.is-active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.tab-pill-count {
    font-size: 9px;
    padding: 0 4px;
    border-radius: 2px;
    background: rgba(255,255,255,.2);
    color: #ffffff;
}

/* 4. Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}
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

/* Top thead Titles Row (Dark Navy #002C54) */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    height: 34px;
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
    cursor: pointer;
    user-select: none;
}
.header-titles-row th:hover {
    background: #08335c;
}
.header-titles-row th .sort-indicator {
    font-size: 9px;
    opacity: 0.6;
    margin-left: 3px;
}
.header-titles-row th.is-sorted .sort-indicator {
    opacity: 1;
    color: #38bdf8;
}

/* Row 2: In-Column Excel Filters (Dark Navy Palette #08335c - Match studentlist) */
.excel-filter-row th {
    position: sticky;
    top: 34px;
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

/* Date Range Selection Elements */
.excel-date-range-box {
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
}
.excel-date-wrap {
    display: flex;
    align-items: center;
    gap: 3px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 4px;
    height: 22px;
    transition: all .15s;
}
.excel-date-wrap:focus-within {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
    background: #031426 !important;
}
.excel-date-lbl {
    font-size: 9px;
    font-weight: 700;
    color: rgba(255,255,255,.75);
    text-transform: uppercase;
    min-width: 26px;
    user-select: none;
    letter-spacing: .02em;
}
.excel-date-input {
    flex: 1;
    min-width: 0;
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 10px;
    padding: 0;
    height: 100%;
    outline: none;
    color-scheme: dark;
}
.excel-date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: .85;
    padding: 0;
    margin-right: 1px;
}
.excel-date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Clear & Reset Buttons */
.btn-clear-filters {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    margin: auto;
    transition: all .15s;
}
.btn-clear-filters:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}
.btn-reset-filters {
    height: 25px;
    padding: 0 6px;
    font-size: 10.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    width: 100%;
    transition: all .15s;
}
.btn-reset-filters:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
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
.dash-table tbody tr:nth-child(odd) .fixed_action_col { background: #f8fafc !important; }
.dash-table tbody tr:nth-child(even) .fixed_action_col { background: #edf2f7 !important; }
.dash-table tbody tr:hover .fixed_action_col { background: #e2e8f0 !important; }

/* Table Body Rows - Soft Slate Palette (studentlist style) */
.dash-table tbody td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) td { background: #f8fafc; }
.dash-table tbody tr:nth-child(even) td { background: #edf2f7; }
.dash-table tbody tr:hover td { background: #e2e8f0 !important; }
.dash-table tbody tr.filtered-out { display: none !important; }

/* Cell elements */
.student-profile-cell {
    display: flex;
    align-items: center;
    gap: 7px;
}
.student-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.student-name-text {
    font-weight: 700;
    color: #002C54;
    line-height: 1.1;
    font-size: 11px;
}
.uid-badge {
    display: inline-block;
    padding: 1px 5px;
    font-family: monospace;
    font-size: 10px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 2px;
}
.mobile-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-family: monospace;
    font-size: 10.5px;
    color: #002C54;
    text-decoration: none;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 44, 84, 0.05);
    transition: all .15s;
}
.mobile-chip:hover {
    background: #22c55e;
    color: #ffffff;
}
.timestamp-text {
    font-size: 10.5px;
    font-weight: 600;
    color: #334155;
    line-height: 1.1;
}
.time-ago-sub {
    font-size: 9px;
    color: #64748b;
    margin-top: 1px;
}

/* Recency Badges */
.recency-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.recency-today { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.recency-week { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
.recency-month { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.recency-older { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }

.btn-view-details {
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 600;
    background: #002C54;
    color: #ffffff;
    border-radius: 2px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: background .15s;
}
.btn-view-details:hover {
    background: #0284c7;
    color: #ffffff;
}

/* 5. Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    height: 32px;
    padding: 3px 10px;
    background: #002342;
    border-top: 1px solid rgba(255,255,255,.12);
    color: #cbd5e1;
    font-size: 10.5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    z-index: 23;
}
.pagination-info {
    display: flex;
    align-items: center;
    gap: 6px;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 4px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 10px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 4px;
    font-size: 10px;
    outline: none;
    cursor: pointer;
}
.page-btn {
    width: 22px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 10px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7;
    border-color: #0284c7;
}
.page-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
}

/* 6. Centered Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height) - 195px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 38px;
    color: #94a3b8;
    margin-bottom: 8px;
}
</style>

<div class="content-wrapper logs-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="logs-page-layout">

                {{-- 1. Signature Hero Header Banner (Consistent with studentlist & stream_update) --}}
                <div class="logs-hero">
                    <div class="logs-hero-text">
                        <span class="logs-kicker"><i class="fa fa-history mr-1"></i> Student Audit &amp; Login Trail</span>
                        <h1 class="logs-title">
                            Student App Login Logs
                        </h1>
                        <p class="logs-subtitle">
                            Monitor and audit real-time mobile app logins and device activities with instant filtering.
                        </p>
                    </div>

                    {{-- Quick Summary Stats --}}
                    <div class="logs-hero-stats">
                        <div class="hero-stat-chip badge-sky" title="Total Active Devices">
                            <i class="fa fa-mobile"></i> Total: <b>{{ number_format($summary['total'] ?? 0) }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald" title="Active Today (<24h)">
                            <i class="fa fa-check-circle"></i> Today: <b>{{ number_format($summary['today'] ?? 0) }}</b>
                        </div>
                        <div class="hero-stat-chip badge-amber" title="Active This Week (7 Days)">
                            <i class="fa fa-calendar-check-o"></i> 7d: <b>{{ number_format($summary['week'] ?? 0) }}</b>
                        </div>
                        <div class="hero-stat-chip badge-rose" title="Active This Month (30 Days)">
                            <i class="fa fa-clock-o"></i> 30d: <b>{{ number_format($summary['month'] ?? 0) }}</b>
                        </div>
                    </div>

                    {{-- Hero Actions --}}
                    <div class="logs-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" onclick="exportLogsCSV()" title="Export CSV File">
                            <i class="fa fa-file-excel-o mr-1 text-success"></i> Export CSV
                        </button>
                    </div>
                </div>

                {{-- 2. Full-Height Table Card --}}
                <div class="dash-card logs-table-card">
                    
                    {{-- Card Header with Compact Filter Form --}}
                    <div class="dash-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Student Login Records Grid</h3>
                        </div>

                        {{-- Compact Fast Search Form --}}
                        <form action="{{ url('student_logs') }}" method="GET" id="logsServerFilterForm" class="header-filter-form">
                            <select name="class_type_id" id="hdr_class_type_id" class="hdr-filter-select" onchange="document.getElementById('logsServerFilterForm').submit()">
                                <option value="">-- All Classes --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $class)
                                        <option value="{{ $class->id }}" {{ (int)($selectedClassId ?? 0) === (int)$class->id ? 'selected' : '' }}>
                                            {{ $class->name ?? $class->class_name ?? ('Class #' . $class->id) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>

                            <input type="text" name="admissionNo" class="hdr-filter-input" placeholder="Adm No..." value="{{ $search['admissionNo'] ?? '' }}" style="width: 100px;">
                            <input type="text" name="name" class="hdr-filter-input" placeholder="Search Name, Mobile, UID..." value="{{ $search['name'] ?? '' }}" style="width: 180px;">

                            <button type="submit" class="hdr-btn-submit" title="Search Server">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <a href="{{ url('student_logs') }}" class="hdr-btn-reset" title="Reset All Filters">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </form>
                    </div>

                    {{-- 3. Status Tabs (Matching stream_update / admissionView) --}}
                    <div class="student-status-tabs">
                        <button type="button" class="status-tab-btn is-active" data-status-filter="all">
                            All Logs <span class="tab-pill-count">{{ number_format($summary['total'] ?? 0) }}</span>
                        </button>
                        <button type="button" class="status-tab-btn" data-status-filter="today">
                            🟢 Today <span class="tab-pill-count">{{ number_format($summary['today'] ?? 0) }}</span>
                        </button>
                        <button type="button" class="status-tab-btn" data-status-filter="week">
                            🔵 This Week <span class="tab-pill-count">{{ number_format($summary['week'] ?? 0) }}</span>
                        </button>
                        <button type="button" class="status-tab-btn" data-status-filter="month">
                            🟡 This Month <span class="tab-pill-count">{{ number_format($summary['month'] ?? 0) }}</span>
                        </button>
                        <button type="button" class="status-tab-btn" data-status-filter="older">
                            ⚪ Older <span class="tab-pill-count">{{ number_format(max(0, ($summary['total'] ?? 0) - ($summary['month'] ?? 0))) }}</span>
                        </button>
                    </div>

                    {{-- 4. Scrollable Table Body (Available Screen Height) --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="studentLogsTable">
                            <thead>
                                {{-- Row 1: Titles & Sorting --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="width: 95px;" class="text-center" data-sort="adm">
                                        Adm No <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="min-width: 170px;" data-sort="name">
                                        Student &amp; Parent Name <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 110px;" data-sort="class">
                                        Class <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 130px;" data-sort="mobile">
                                        Parent Mobile <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 130px;" class="text-center" data-sort="uid">
                                        Attendance UID <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 175px;" data-sort="timestamp">
                                        Last Login Time <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 125px;" class="text-center" data-sort="recency-code">
                                        Status <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                    </th>
                                    <th style="width: 80px;" class="text-center fixed_action_head">Action</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters --}}
                                <tr class="excel-filter-row">
                                    {{-- 1. Reset / Filter icon --}}
                                    <th class="text-center">
                                        <button type="button" class="btn-clear-filters" id="btn-clear-table-filters" title="Reset All Table Filters">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>

                                    {{-- 2. Admission No --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-adm" placeholder="Filter Adm...">
                                    </th>

                                    {{-- 3. Student Name --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Filter Name...">
                                    </th>

                                    {{-- 4. Class Dropdown --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-class">
                                            <option value="">All Classes</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $type)
                                                    <option value="{{ strtolower($type->name ?? $type->class_name ?? '') }}">
                                                        {{ $type->name ?? $type->class_name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>

                                    {{-- 5. Mobile --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Filter Mobile...">
                                    </th>

                                    {{-- 6. UID --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-uid" placeholder="Filter UID...">
                                    </th>

                                    {{-- 7. Date Range (From - To) --}}
                                    <th>
                                        <div class="excel-date-range-box">
                                            <div class="excel-date-wrap" title="Select From Date">
                                                <span class="excel-date-lbl">From</span>
                                                <input type="date" class="excel-date-input" id="filter-from-date" title="Select From Date">
                                            </div>
                                            <div class="excel-date-wrap" title="Select To Date">
                                                <span class="excel-date-lbl">To</span>
                                                <input type="date" class="excel-date-input" id="filter-to-date" title="Select To Date">
                                            </div>
                                        </div>
                                    </th>

                                    {{-- 8. Status Dropdown --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="">All Status</option>
                                            <option value="today">🟢 Active Today</option>
                                            <option value="week">🔵 This Week</option>
                                            <option value="month">🟡 This Month</option>
                                            <option value="older">⚪ Older (>30d)</option>
                                        </select>
                                    </th>

                                    {{-- 9. Action Reset Button --}}
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-table-filters" title="Reset All In-Table Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="studentLogsTableBody">
                                @php
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                @if(count($logs) > 0)
                                    @foreach($logs as $index => $item)
                                        @php
                                            $stName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                                            $initial = strtoupper(substr($item->first_name ?? 'S', 0, 1));
                                            $lastLogin = !empty($item->last_login) ? \Carbon\Carbon::parse($item->last_login) : null;
                                            
                                            $recencyType = 'older';
                                            $recencyCode = 4;
                                            $recencyLabel = 'Older';
                                            $recencyClass = 'recency-older';
                                            $diffHours = $lastLogin ? $lastLogin->diffInHours($now) : 9999;
                                            $diffDays = $lastLogin ? $lastLogin->diffInDays($now) : 9999;

                                            if ($lastLogin) {
                                                if ($diffHours < 24) {
                                                    $recencyType = 'today';
                                                    $recencyCode = 1;
                                                    $recencyLabel = 'Active Today';
                                                    $recencyClass = 'recency-today';
                                                } elseif ($diffDays <= 7) {
                                                    $recencyType = 'week';
                                                    $recencyCode = 2;
                                                    $recencyLabel = 'This Week';
                                                    $recencyClass = 'recency-week';
                                                } elseif ($diffDays <= 30) {
                                                    $recencyType = 'month';
                                                    $recencyCode = 3;
                                                    $recencyLabel = 'This Month';
                                                    $recencyClass = 'recency-month';
                                                }
                                            }
                                        @endphp
                                        <tr class="log-row"
                                            data-index="{{ $loop->iteration }}"
                                            data-adm="{{ strtolower($item->admissionNo ?? '') }}"
                                            data-name="{{ strtolower($stName) }}"
                                            data-father="{{ strtolower($item->father_name ?? '') }}"
                                            data-class="{{ strtolower($item->class_name ?? '') }}"
                                            data-mobile="{{ strtolower($item->mobile ?? '') }}"
                                            data-uid="{{ strtolower($item->attendance_unique_id ?? '') }}"
                                            data-date="{{ $lastLogin ? $lastLogin->format('Y-m-d') : '' }}"
                                            data-timestamp="{{ $lastLogin ? $lastLogin->timestamp : 0 }}"
                                            data-time-str="{{ $lastLogin ? $lastLogin->format('d M Y, h:i A') : '-' }}"
                                            data-recency="{{ $recencyType }}"
                                            data-recency-code="{{ $recencyCode }}"
                                            data-recency-label="{{ strtolower($recencyLabel) }}">
                                            
                                            {{-- 1. Index --}}
                                            <td style="text-align: center; color: #64748b; font-weight: 600;">
                                                {{ $loop->iteration }}
                                            </td>

                                            {{-- 2. Adm No --}}
                                            <td style="text-align: center; font-weight: 700;">
                                                <code>{{ !empty($item->admissionNo) ? $item->admissionNo : 'N/A' }}</code>
                                            </td>

                                            {{-- 3. Student Name --}}
                                            <td>
                                                <div class="student-profile-cell">
                                                    <span class="student-avatar">{{ $initial }}</span>
                                                    <div>
                                                        <div class="student-name-text">{{ $stName ?: '-' }}</div>
                                                        @if(!empty($item->father_name))
                                                            <div style="font-size: 9.5px; color: #64748b; line-height: 1;">
                                                                S/D of {{ $item->father_name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- 4. Class --}}
                                            <td>
                                                <span class="badge badge-light border font-weight-bold" style="color: #002C54;">
                                                    {{ !empty($item->class_name) ? $item->class_name : '-' }}
                                                </span>
                                            </td>

                                            {{-- 5. Mobile --}}
                                            <td>
                                                @if(!empty($item->mobile))
                                                    <a href="https://wa.me/91{{ preg_replace('/\D/', '', $item->mobile) }}" target="_blank" class="mobile-chip" title="Chat on WhatsApp">
                                                        <i class="fa fa-whatsapp text-success"></i>
                                                        <span>{{ $item->mobile }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted" style="font-size: 10px;">-</span>
                                                @endif
                                            </td>

                                            {{-- 6. Attendance UID --}}
                                            <td style="text-align: center;">
                                                <span class="uid-badge" title="Attendance Unique ID">
                                                    {{ !empty($item->attendance_unique_id) ? $item->attendance_unique_id : 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- 7. Timestamp --}}
                                            <td>
                                                @if($lastLogin)
                                                    <div class="timestamp-text">
                                                        <i class="fa fa-clock-o text-muted mr-1"></i>
                                                        {{ $lastLogin->format('d M Y, h:i A') }}
                                                    </div>
                                                    <div class="time-ago-sub">
                                                        {{ $lastLogin->diffForHumans() }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- 8. Recency Status --}}
                                            <td style="text-align: center;">
                                                <span class="recency-badge {{ $recencyClass }}">
                                                    @if($recencyType === 'today') <i class="fa fa-bolt"></i> @endif
                                                    {{ $recencyLabel }}
                                                </span>
                                            </td>

                                            {{-- 9. Action --}}
                                            <td style="text-align: center;" class="fixed_action_col">
                                                <a href="{{ url('studentParticularPerformance') }}/{{ $item->id ?? '' }}" target="_blank" class="btn-view-details" title="Open Student Profile">
                                                    <i class="fa fa-external-link"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr id="empty-state-row">
                                        <td colspan="9" class="p-0 border-0">
                                            <div class="dash-empty-state">
                                                <div class="empty-icon">
                                                    <i class="fa fa-folder-open-o"></i>
                                                </div>
                                                <h5 style="font-weight: 700; color: #1e293b; font-size: 13px;">No Student Login Logs Found</h5>
                                                <p style="font-size: 11px; margin-bottom: 8px;">Try adjusting your class selection or search criteria to view audit records.</p>
                                                <a href="{{ url('student_logs') }}" class="dash-btn dash-btn-light border" style="background: #ffffff; color: #002C54; font-size: 11px;">
                                                    <i class="fa fa-refresh mr-1"></i> Clear Filters
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- 5. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            <span>Showing <b id="page-start">1</b> to <b id="page-end">{{ count($logs) }}</b> of <b id="total-records">{{ count($logs) }}</b> records</span>
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Show:</label>
                                <select id="rows-per-page-select">
                                    <option value="25">25</option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div style="display: flex; align-items: center; gap: 3px;">
                                <button type="button" class="page-btn" id="btn-first" title="First Page"><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page"><i class="fa fa-angle-left"></i></button>
                                <span style="font-size: 10px; padding: 0 4px; color: #f1f5f9;">Page <b id="current-page">1</b> / <span id="total-pages">1</span></span>
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
$(document).ready(function() {
    var allRows = $('.log-row');
    var rowsPerPage = 50;
    var currentPage = 1;
    var currentSortCol = 'timestamp';
    var sortDirection = 'desc';
    var currentStatusFilter = 'all';

    // 1. Comprehensive Multi-Input Table Filter
    function applyFilters() {
        var fAdm = ($('#filter-adm').val() || '').trim().toLowerCase();
        var fName = ($('#filter-name').val() || '').trim().toLowerCase();
        var fClass = ($('#filter-class').val() || '').trim().toLowerCase();
        var fMobile = ($('#filter-mobile').val() || '').trim().toLowerCase();
        var fUid = ($('#filter-uid').val() || '').trim().toLowerCase();
        var fFromDate = $('#filter-from-date').val() || '';
        var fToDate = $('#filter-to-date').val() || '';
        var fStatus = $('#filter-status').val() || '';

        var visibleCount = 0;

        allRows.each(function() {
            var row = $(this);
            var matches = true;

            // Recency Tab Filter
            if (currentStatusFilter !== 'all') {
                if (row.data('recency') !== currentStatusFilter) {
                    matches = false;
                }
            }

            // In-Column Status Dropdown Filter
            if (matches && fStatus !== '') {
                if (row.data('recency') !== fStatus) {
                    matches = false;
                }
            }

            // In-Column Adm No Filter
            if (matches && fAdm !== '') {
                var rowAdm = (row.data('adm') || '') + '';
                if (rowAdm.indexOf(fAdm) === -1) {
                    matches = false;
                }
            }

            // In-Column Name & Father Filter
            if (matches && fName !== '') {
                var rowName = (row.data('name') || '') + ' ' + (row.data('father') || '');
                if (rowName.indexOf(fName) === -1) {
                    matches = false;
                }
            }

            // In-Column Class Dropdown Filter
            if (matches && fClass !== '') {
                var rowClass = (row.data('class') || '').toLowerCase();
                if (rowClass.indexOf(fClass) === -1) {
                    matches = false;
                }
            }

            // In-Column Mobile Filter
            if (matches && fMobile !== '') {
                var rowMobile = (row.data('mobile') || '') + '';
                if (rowMobile.indexOf(fMobile) === -1) {
                    matches = false;
                }
            }

            // In-Column UID Filter
            if (matches && fUid !== '') {
                var rowUid = (row.data('uid') || '') + '';
                if (rowUid.indexOf(fUid) === -1) {
                    matches = false;
                }
            }

            // In-Column Date Range Filter (From - To)
            if (matches && (fFromDate !== '' || fToDate !== '')) {
                var rowDate = row.data('date') || '';
                if (!rowDate) {
                    matches = false;
                } else {
                    if (fFromDate !== '' && rowDate < fFromDate) {
                        matches = false;
                    }
                    if (fToDate !== '' && rowDate > fToDate) {
                        matches = false;
                    }
                }
            }

            if (matches) {
                row.removeClass('filtered-out');
                visibleCount++;
            } else {
                row.addClass('filtered-out');
            }
        });

        currentPage = 1;
        paginateTable();
    }

    // 2. Pagination Logic
    function paginateTable() {
        var visibleRows = allRows.not('.filtered-out');
        var count = visibleRows.length;
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(count / rowsPerPage);
        if (totalPages < 1) totalPages = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * (rowsPerPage === 'all' ? count : rowsPerPage);
        var end = rowsPerPage === 'all' ? count : start + rowsPerPage;

        allRows.hide();
        visibleRows.slice(start, end).show();

        // Update UI counters
        $('#page-start').text(count > 0 ? (start + 1) : 0);
        $('#page-end').text(Math.min(end, count));
        $('#total-records').text(count);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || count === 0);
    }

    // 3. Dynamic Column Sorting
    $('.header-titles-row th[data-sort]').on('click', function() {
        var sortKey = $(this).data('sort');
        if (currentSortCol === sortKey) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortCol = sortKey;
            sortDirection = sortKey === 'timestamp' ? 'desc' : 'asc';
        }

        // Update sort indicator icons
        $('.header-titles-row th').removeClass('is-sorted');
        $(this).addClass('is-sorted');
        $(this).find('.sort-indicator i').attr('class', sortDirection === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc');

        var tbody = $('#studentLogsTableBody');
        var rowsArray = allRows.get();

        rowsArray.sort(function(a, b) {
            var valA = $(a).data(sortKey);
            var valB = $(b).data(sortKey);

            if (typeof valA === 'number' && typeof valB === 'number') {
                return sortDirection === 'asc' ? valA - valB : valB - valA;
            }

            valA = (valA || '') + '';
            valB = (valB || '') + '';

            return sortDirection === 'asc' 
                ? valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' })
                : valB.localeCompare(valA, undefined, { numeric: true, sensitivity: 'base' });
        });

        $.each(rowsArray, function(index, row) {
            tbody.append(row);
        });

        allRows = $('.log-row');
        paginateTable();
    });

    // 4. Filter Event Listeners
    $('#filter-adm, #filter-name, #filter-mobile, #filter-uid').on('keyup input', applyFilters);
    $('#filter-class, #filter-status, #filter-from-date, #filter-to-date').on('change', applyFilters);

    // Reset In-Table Filters Function
    function resetInTableFilters() {
        $('#filter-adm, #filter-name, #filter-mobile, #filter-uid').val('');
        $('#filter-class, #filter-status').val('');
        $('#filter-from-date, #filter-to-date').val('');
        currentStatusFilter = 'all';
        $('.status-tab-btn').removeClass('is-active');
        $('.status-tab-btn[data-status-filter="all"]').addClass('is-active');
        applyFilters();
    }

    $('#btn-clear-table-filters, #btn-reset-table-filters').on('click', resetInTableFilters);

    // Quick Status Tabs
    $('.status-tab-btn').on('click', function() {
        $('.status-tab-btn').removeClass('is-active');
        $(this).addClass('is-active');
        currentStatusFilter = $(this).data('status-filter');
        // sync status dropdown if matches
        if (currentStatusFilter !== 'all') {
            $('#filter-status').val(currentStatusFilter);
        } else {
            $('#filter-status').val('');
        }
        applyFilters();
    });

    // Rows Per Page Selector
    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        rowsPerPage = val === 'all' ? 'all' : parseInt(val, 10);
        currentPage = 1;
        paginateTable();
    });

    // Pagination Button Handlers
    $('#btn-first').on('click', function() {
        currentPage = 1;
        paginateTable();
    });
    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            paginateTable();
        }
    });
    $('#btn-next').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            paginateTable();
        }
    });
    $('#btn-last').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        currentPage = totalPages;
        paginateTable();
    });

    // Initial table render
    paginateTable();
});

// CSV Export Logic
function exportLogsCSV() {
    var rows = [];
    rows.push(['#', 'Admission No', 'Student Name', 'Class', 'Mobile', 'Attendance UID', 'Last Login Time', 'Status']);

    $('.log-row').not('.filtered-out').each(function(i) {
        var r = $(this);
        rows.push([
            (i + 1),
            r.data('adm') || '',
            r.find('.student-name-text').text().trim(),
            r.data('class') || '',
            r.data('mobile') || '',
            r.data('uid') || '',
            r.data('time-str') || '',
            r.find('.recency-badge').text().trim()
        ]);
    });

    var csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => '"' + (cell + '').replace(/"/g, '""') + '"').join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "student_login_logs_" + (new Date().toISOString().slice(0, 10)) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
@endsection
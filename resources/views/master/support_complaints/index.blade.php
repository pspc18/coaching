@extends('layout.app')

@php
    $counts = $counts ?? collect([]);
    $totalCount = $totalCount ?? $complaints->total();
    $currentStatus = request('status', '');
    $currentCategory = request('category', '');
    $currentPriority = request('priority', '');
    $currentTicket = request('ticket_no', '');
    $currentSubject = request('subject', '');
    $currentStudent = request('student_name', request('q', ''));
    $perPage = request('per_page', 20);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - COMPLAINTS MANAGEMENT (NEW THEME LAYOUT GUIDELINES)
   Strictly aligned with Student List and User View:
   - Viewport fitting: calc(100vh - var(--header-height, 56px) - 16px)
   - Zero outer vertical scrollbars
   - Dark Navy Hero (#002C54 to #0f3460)
   - Status metric cards grid with synchronized filtering
   - Sticky table thead (#002C54) and sticky in-column Excel filters (#08335c)
   - Fixed sticky Actions column (#002C54 / #08335c)
   - Pinned bottom pagination toolbar (#002C54)
   ========================================================================== */

.complaints-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.complaints-page * {
    box-sizing: border-box;
}
.complaints-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Top Hero Header (Arise Dark Navy Gradient) */
.complaints-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.complaints-hero-text {
    display: flex;
    flex-direction: column;
}
.complaints-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.complaints-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.complaints-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Summary Badges */
.complaints-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-blue {
    background: rgba(59, 130, 246, 0.22);
    border-color: rgba(59, 130, 246, 0.4);
    color: #bfdbfe;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.22);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-red {
    background: rgba(225, 29, 72, 0.22);
    border-color: rgba(225, 29, 72, 0.4);
    color: #fecdd3;
}

/* 2. Status Metric Cards Grid (Compact & Clickable) */
.status-grid-compact {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 6px;
    margin-bottom: 4px;
    flex-shrink: 0;
}
.status-card-compact {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    gap: 7px;
    text-decoration: none !important;
    color: #1e293b !important;
    transition: all .15s ease-in-out;
    border-left: 3px solid #cbd5e1;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    cursor: pointer;
    user-select: none;
}
.status-card-compact:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,44,84,.08);
}
.status-card-compact.active {
    background: #f0f7ff;
    border-color: #002C54;
    border-left-width: 3px;
    box-shadow: inset 0 0 0 1px #002C54, 0 2px 4px rgba(0,44,84,.12);
}
.status-card-compact.is-all { border-left-color: #002C54; }
.status-card-compact.is-open { border-left-color: #2563eb; }
.status-card-compact.is-acknowledged { border-left-color: #0891b2; }
.status-card-compact.is-in_progress { border-left-color: #d97706; }
.status-card-compact.is-awaiting_user { border-left-color: #7c3aed; }
.status-card-compact.is-resolved { border-left-color: #16a34a; }
.status-card-compact.is-closed { border-left-color: #64748b; }
.status-card-compact.is-reopened { border-left-color: #e11d48; }

.status-icon-box {
    width: 24px;
    height: 24px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    flex-shrink: 0;
}
.is-all .status-icon-box { background: #e2e8f0; color: #002C54; }
.is-open .status-icon-box { background: #dbeafe; color: #1d4ed8; }
.is-acknowledged .status-icon-box { background: #cffafe; color: #0e7490; }
.is-in_progress .status-icon-box { background: #fef3c7; color: #b45309; }
.is-awaiting_user .status-icon-box { background: #ede9fe; color: #6d28d9; }
.is-resolved .status-icon-box { background: #dcfce7; color: #15803d; }
.is-closed .status-icon-box { background: #f1f5f9; color: #475569; }
.is-reopened .status-icon-box { background: #ffe4e6; color: #be123c; }

.status-copy-box {
    display: flex;
    flex-direction: column;
    min-width: 0;
    line-height: 1.15;
}
.status-copy-box strong {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
}
.status-copy-box small {
    font-size: 9px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
}

/* 3. Main Full Table Card (Fits Viewport Height) */
.complaints-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    margin-bottom: 0;
    overflow: hidden;
}
.dash-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
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

/* Scrollable Table Area */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Grid */
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

/* Row 1: Header Titles Row */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 6px 8px;
    height: 35px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
}

/* Row 2: In-Column Sticky Excel Filter Row */
.excel-filter-row th {
    position: sticky;
    top: 35px;
    background: #08335c;
    color: #ffffff;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
}

/* Fixed Sticky Action Column */
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
.dash-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #f8fafc !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #edf2f7 !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e2e8f0 !important;
}

/* Excel In-Column Filters Form Controls */
.excel-col-filter {
    width: 100%;
    height: 27px;
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

/* Reset Filter Button */
.btn-reset-filters {
    height: 27px;
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
    gap: 4px;
    transition: all .15s;
    white-space: nowrap;
}
.btn-reset-filters:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Table Body Rows */
.dash-table tbody tr {
    background: #ffffff;
    transition: background-color .1s;
}
.dash-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-table tbody tr:hover {
    background: #e6f0fa !important;
}
.dash-table tbody td {
    padding: 6px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
}

/* Ticket Link */
.ticket-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 700;
    color: #0284c7;
    font-size: 11.5px;
    text-decoration: none !important;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}
.ticket-link:hover {
    color: #002C54;
    text-decoration: underline !important;
}

/* Student Details Cell */
.student-cell {
    display: flex;
    align-items: center;
    gap: 7px;
}
.student-avatar-img {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.student-avatar-fallback {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid #001f3d;
}
.student-info-box {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.student-name-text {
    font-weight: 600;
    color: #0f172a;
    line-height: 1.2;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-sub-text {
    font-size: 9.5px;
    color: #64748b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Subject */
.subject-text {
    font-weight: 600;
    color: #1e293b;
    max-width: 260px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

/* Category Pill */
.category-pill {
    display: inline-block;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    border-radius: 2px;
    text-transform: capitalize;
}

/* Priority Pills */
.priority-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.priority-urgent {
    background: #ffe4e6;
    border: 1px solid #fecdd3;
    color: #be123c;
}
.priority-high {
    background: #fef3c7;
    border: 1px solid #fde68a;
    color: #b45309;
}
.priority-medium {
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    color: #0369a1;
}
.priority-low {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
}

/* Status Pills */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    white-space: nowrap;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.status-pill-open { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.status-pill-open .status-dot { background: #2563eb; }
.status-pill-acknowledged { background: #cffafe; color: #0e7490; border: 1px solid #a5f3fc; }
.status-pill-acknowledged .status-dot { background: #0891b2; }
.status-pill-in_progress { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-pill-in_progress .status-dot { background: #d97706; }
.status-pill-awaiting_user { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
.status-pill-awaiting_user .status-dot { background: #7c3aed; }
.status-pill-resolved { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.status-pill-resolved .status-dot { background: #16a34a; }
.status-pill-closed { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.status-pill-closed .status-dot { background: #64748b; }
.status-pill-reopened { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
.status-pill-reopened .status-dot { background: #e11d48; }

/* Replies & Time */
.replies-count-box {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-weight: 700;
    color: #334155;
    font-size: 11px;
}
.time-main {
    display: block;
    font-size: 11px;
    color: #1e293b;
    font-weight: 600;
}
.time-sub {
    display: block;
    font-size: 9.5px;
    color: #64748b;
}

/* Action Button */
.btn-view-ticket {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 25px;
    padding: 0 9px;
    background: #002C54;
    color: #ffffff !important;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid #002C54;
    text-decoration: none !important;
    transition: all .15s;
    white-space: nowrap;
}
.btn-view-ticket:hover {
    background: #001930;
    border-color: #001930;
}

/* Empty State */
.empty-state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    text-align: center;
    color: #64748b;
}
.empty-state-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 10px;
}
.empty-state-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 3px;
}
.empty-state-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 320px;
    margin-bottom: 12px;
}
.btn-clear-empty-state {
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #002C54;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: background .15s;
}
.btn-clear-empty-state:hover {
    background: #001930;
}

/* 4. Pinned Bottom Pagination Toolbar (Exact match with User View) */
.table-pagination-bar {
    height: 36px;
    background: #002C54;
    border-top: 1px solid #001f3d;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    color: #ffffff;
    font-size: 11px;
    z-index: 30;
}
.pagination-info {
    font-size: 11px;
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
    font-size: 11px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    height: 24px;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 0 4px;
    font-size: 11px;
    cursor: pointer;
    outline: none;
}
.rows-per-page-selector select:focus {
    border-color: #38bdf8;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 4px;
}
.page-btn {
    width: 25px;
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
.table-loading {
    opacity: 0.55;
    pointer-events: none;
}
</style>
@endsection

@section('content')
<div class="content-wrapper complaints-page">
    <section class="content p-2">
        <div class="container-fluid p-0">
            <div class="complaints-page-layout">

                {{-- 1. Top Hero Header (Arise Dark Navy Theme) --}}
                <div class="complaints-hero">
                    <div class="complaints-hero-text">
                        <span class="complaints-kicker"><i class="fa fa-life-ring mr-1"></i> Helpdesk &amp; Support System</span>
                        <h1 class="complaints-title">Complaints &amp; Grievance Management</h1>
                        <p class="complaints-subtitle">Review, monitor, and resolve student support complaints with instant in-column Excel filters &amp; status queue</p>
                    </div>

                    {{-- Hero Summary Badges --}}
                    <div class="complaints-hero-stats">
                        <span class="hero-stat-badge badge-blue" title="Open Complaints">
                            <i class="fa fa-folder-open-o"></i> Open: <b id="stat-open">{{ $counts['open'] ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-amber" title="In Progress">
                            <i class="fa fa-spinner"></i> In Progress: <b id="stat-in_progress">{{ $counts['in_progress'] ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Resolved Complaints">
                            <i class="fa fa-check-circle"></i> Resolved: <b id="stat-resolved">{{ $counts['resolved'] ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge badge-red" title="Reopened Complaints">
                            <i class="fa fa-repeat"></i> Reopened: <b id="stat-reopened">{{ $counts['reopened'] ?? 0 }}</b>
                        </span>
                        <span class="hero-stat-badge" title="Total Complaints">
                            <i class="fa fa-ticket"></i> Total: <b id="stat-total">{{ $totalCount }}</b>
                        </span>
                    </div>
                </div>

                @if(session('message'))
                    <div class="alert alert-success py-2 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- 2. Status Metric Cards Grid (Compact & Clickable) --}}
                <div class="status-grid-compact" id="status-card-grid">
                    @php
                        $activeStatus = request('status', '');
                    @endphp

                    {{-- All --}}
                    <div class="status-card-compact is-all {{ empty($activeStatus) ? 'active' : '' }}" data-status="" title="Show all complaints">
                        <div class="status-icon-box"><i class="fa fa-th-list"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-all">{{ $totalCount }}</strong>
                            <small>All Tickets</small>
                        </div>
                    </div>

                    {{-- Open --}}
                    <div class="status-card-compact is-open {{ $activeStatus === 'open' ? 'active' : '' }}" data-status="open" title="Open Complaints">
                        <div class="status-icon-box"><i class="fa fa-folder-open-o"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-open">{{ $counts['open'] ?? 0 }}</strong>
                            <small>Open</small>
                        </div>
                    </div>

                    {{-- Acknowledged --}}
                    <div class="status-card-compact is-acknowledged {{ $activeStatus === 'acknowledged' ? 'active' : '' }}" data-status="acknowledged" title="Acknowledged Complaints">
                        <div class="status-icon-box"><i class="fa fa-bookmark-o"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-acknowledged">{{ $counts['acknowledged'] ?? 0 }}</strong>
                            <small>Acknowledged</small>
                        </div>
                    </div>

                    {{-- In Progress --}}
                    <div class="status-card-compact is-in_progress {{ $activeStatus === 'in_progress' ? 'active' : '' }}" data-status="in_progress" title="In Progress Complaints">
                        <div class="status-icon-box"><i class="fa fa-spinner"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-in_progress">{{ $counts['in_progress'] ?? 0 }}</strong>
                            <small>In Progress</small>
                        </div>
                    </div>

                    {{-- Awaiting User --}}
                    <div class="status-card-compact is-awaiting_user {{ $activeStatus === 'awaiting_user' ? 'active' : '' }}" data-status="awaiting_user" title="Awaiting User Response">
                        <div class="status-icon-box"><i class="fa fa-clock-o"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-awaiting_user">{{ $counts['awaiting_user'] ?? 0 }}</strong>
                            <small>Awaiting User</small>
                        </div>
                    </div>

                    {{-- Resolved --}}
                    <div class="status-card-compact is-resolved {{ $activeStatus === 'resolved' ? 'active' : '' }}" data-status="resolved" title="Resolved Complaints">
                        <div class="status-icon-box"><i class="fa fa-check-circle-o"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-resolved">{{ $counts['resolved'] ?? 0 }}</strong>
                            <small>Resolved</small>
                        </div>
                    </div>

                    {{-- Closed --}}
                    <div class="status-card-compact is-closed {{ $activeStatus === 'closed' ? 'active' : '' }}" data-status="closed" title="Closed Complaints">
                        <div class="status-icon-box"><i class="fa fa-lock"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-closed">{{ $counts['closed'] ?? 0 }}</strong>
                            <small>Closed</small>
                        </div>
                    </div>

                    {{-- Reopened --}}
                    <div class="status-card-compact is-reopened {{ $activeStatus === 'reopened' ? 'active' : '' }}" data-status="reopened" title="Reopened Complaints">
                        <div class="status-icon-box"><i class="fa fa-repeat"></i></div>
                        <div class="status-copy-box">
                            <strong id="card-count-reopened">{{ $counts['reopened'] ?? 0 }}</strong>
                            <small>Reopened</small>
                        </div>
                    </div>
                </div>

                {{-- 3. Full Height Table Card (Fits Viewport Height) --}}
                <div class="complaints-table-card">
                    <div class="dash-card-header">
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <h3 class="dash-card-title"><i class="fa fa-table mr-1 text-info"></i> Support Complaints Register</h3>
                        </div>
                        <div>
                            <span class="badge-total-records"><span id="header-records-count">{{ $complaints->total() }}</span> Tickets</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="complaints-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles Row --}}
                                <tr class="header-titles-row">
                                    <th style="width: 125px;" class="text-center">Ticket No.</th>
                                    <th style="width: 195px;">Submitted By</th>
                                    <th style="min-width: 210px;">Subject &amp; Concern</th>
                                    <th style="width: 110px;" class="text-center">Category</th>
                                    <th style="width: 95px;" class="text-center">Priority</th>
                                    <th style="width: 140px;" class="text-center">Status</th>
                                    <th style="width: 75px;" class="text-center">Replies</th>
                                    <th style="width: 145px;">Last Activity</th>
                                    <th style="width: 85px;" class="text-center fixed_action_head">Action</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters Row --}}
                                <tr class="excel-filter-row">
                                    {{-- Ticket No Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-ticket" placeholder="Filter ticket..." value="{{ $currentTicket }}">
                                    </th>

                                    {{-- Submitted By / Student Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-student" placeholder="Filter student/adm..." value="{{ $currentStudent }}">
                                    </th>

                                    {{-- Subject Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-subject" placeholder="Filter subject..." value="{{ $currentSubject }}">
                                    </th>

                                    {{-- Category Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-category">
                                            <option value="">All Categories</option>
                                            @foreach(\App\Models\SupportComplaint::CATEGORIES as $catKey => $catName)
                                                <option value="{{ $catKey }}" {{ $currentCategory === $catKey ? 'selected' : '' }}>{{ $catName }}</option>
                                            @endforeach
                                        </select>
                                    </th>

                                    {{-- Priority Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-priority">
                                            <option value="">All Priorities</option>
                                            <option value="urgent" {{ $currentPriority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            <option value="high" {{ $currentPriority === 'high' ? 'selected' : '' }}>High</option>
                                            <option value="medium" {{ $currentPriority === 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="low" {{ $currentPriority === 'low' ? 'selected' : '' }}>Low</option>
                                        </select>
                                    </th>

                                    {{-- Status Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="">All Statuses</option>
                                            @foreach(\App\Models\SupportComplaint::STATUSES as $statusKey => $statusLabel)
                                                <option value="{{ $statusKey }}" {{ $activeStatus === $statusKey ? 'selected' : '' }}>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                    </th>

                                    {{-- Replies (Placeholder) --}}
                                    <th></th>

                                    {{-- Last Activity (Placeholder) --}}
                                    <th></th>

                                    {{-- Action / Reset Cell --}}
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters" title="Reset All In-Column Filters">
                                            <i class="fa fa-refresh mr-1"></i> Clear
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="complaints-table-body">
                                @include('master.support_complaints.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Pinned Bottom Pagination Toolbar (Exact match with User View) --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $complaints->firstItem() ?? 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ $complaints->lastItem() ?? 0 }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $complaints->total() }}</span> tickets
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" {{ $complaints->currentPage() <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ $complaints->currentPage() <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">{{ $complaints->currentPage() }}</span> of <span id="total-pages">{{ $complaints->lastPage() }}</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ $complaints->currentPage() >= $complaints->lastPage() ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ $complaints->currentPage() >= $complaints->lastPage() ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- Client-Side Dynamic AJAX Filtering & Pagination Engine --}}
    <script>
    $(document).ready(function() {
        let currentPage = parseInt('{{ $complaints->currentPage() }}') || 1;
        let currentLastPage = parseInt('{{ $complaints->lastPage() }}') || 1;
        let filterDebounceTimer = null;
        let isFetching = false;

        function getFilterParams() {
            const st = ($('#filter-status').val() || '').trim();
            return {
                ajax: 1,
                page: currentPage,
                per_page: $('#rows-per-page-select').val() || 20,
                status: st,
                ticket_no: ($('#filter-ticket').val() || '').trim(),
                student_name: ($('#filter-student').val() || '').trim(),
                subject: ($('#filter-subject').val() || '').trim(),
                category: ($('#filter-category').val() || '').trim(),
                priority: ($('#filter-priority').val() || '').trim()
            };
        }

        function updateUrlParams(params) {
            if (window.history && window.history.replaceState) {
                const url = new URL(window.location.href);
                url.search = '';
                Object.keys(params).forEach(key => {
                    if (key !== 'ajax' && params[key] !== '' && params[key] !== null) {
                        url.searchParams.set(key, params[key]);
                    }
                });
                window.history.replaceState({}, '', url.toString());
            }
        }

        function syncStatusCards(activeStatus) {
            $('.status-card-compact').removeClass('active');
            if (!activeStatus) {
                $('.status-card-compact.is-all').addClass('active');
            } else {
                $('.status-card-compact[data-status="' + activeStatus + '"]').addClass('active');
            }
        }

        function fetchComplaints() {
            if (isFetching) return;
            isFetching = true;

            const params = getFilterParams();
            updateUrlParams(params);
            syncStatusCards(params.status);

            $('#complaints-table-body').addClass('table-loading');

            $.ajax({
                url: '{{ url("complaints-management") }}',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(res) {
                    if (res && res.status) {
                        $('#complaints-table-body').html(res.html);

                        currentPage = parseInt(res.current_page) || 1;
                        currentLastPage = parseInt(res.last_page) || 1;

                        $('#page-start').text(res.from || 0);
                        $('#page-end').text(res.to || 0);
                        $('#total-records').text(res.total || 0);
                        $('#header-records-count').text(res.total || 0);
                        $('#current-page').text(currentPage);
                        $('#total-pages').text(currentLastPage);

                        // Update Pagination Nav Buttons state
                        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                        $('#btn-next, #btn-last').prop('disabled', currentPage >= currentLastPage);

                        // Update Status Badges & Cards if available
                        if (res.counts) {
                            const c = res.counts;
                            $('#stat-open').text(c.open || 0);
                            $('#stat-in_progress').text(c.in_progress || 0);
                            $('#stat-resolved').text(c.resolved || 0);
                            $('#stat-reopened').text(c.reopened || 0);
                            $('#stat-total').text(res.totalCount || 0);

                            $('#card-count-all').text(res.totalCount || 0);
                            $('#card-count-open').text(c.open || 0);
                            $('#card-count-acknowledged').text(c.acknowledged || 0);
                            $('#card-count-in_progress').text(c.in_progress || 0);
                            $('#card-count-awaiting_user').text(c.awaiting_user || 0);
                            $('#card-count-resolved').text(c.resolved || 0);
                            $('#card-count-closed').text(c.closed || 0);
                            $('#card-count-reopened').text(c.reopened || 0);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch complaints:', error);
                },
                complete: function() {
                    $('#complaints-table-body').removeClass('table-loading');
                    isFetching = false;
                }
            });
        }

        // Debounced text inputs
        $('#filter-ticket, #filter-student, #filter-subject').on('keyup input', function() {
            clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(function() {
                currentPage = 1;
                fetchComplaints();
            }, 300);
        });

        // Dropdown changes
        $('#filter-category, #filter-priority').on('change', function() {
            currentPage = 1;
            fetchComplaints();
        });

        // Status select change
        $('#filter-status').on('change', function() {
            currentPage = 1;
            fetchComplaints();
        });

        // Status card click
        $('.status-card-compact').on('click', function(e) {
            e.preventDefault();
            const status = $(this).data('status') || '';
            $('#filter-status').val(status);
            currentPage = 1;
            fetchComplaints();
        });

        // Rows per page
        $('#rows-per-page-select').on('change', function() {
            currentPage = 1;
            fetchComplaints();
        });

        // Pagination buttons
        $('#btn-first').on('click', function() {
            if (currentPage > 1) {
                currentPage = 1;
                fetchComplaints();
            }
        });

        $('#btn-prev').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                fetchComplaints();
            }
        });

        $('#btn-next').on('click', function() {
            if (currentPage < currentLastPage) {
                currentPage++;
                fetchComplaints();
            }
        });

        $('#btn-last').on('click', function() {
            if (currentPage < currentLastPage) {
                currentPage = currentLastPage;
                fetchComplaints();
            }
        });

        // Reset Filters Button
        $('#btn-clear-filters').on('click', function() {
            $('#filter-ticket').val('');
            $('#filter-student').val('');
            $('#filter-subject').val('');
            $('#filter-category').val('');
            $('#filter-priority').val('');
            $('#filter-status').val('');
            currentPage = 1;
            fetchComplaints();
        });
    });
    </script>
</div>
@endsection

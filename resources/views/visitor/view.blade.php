@extends('layout.app')

@php
    $counts = $counts ?? [
        'all' => $totalCount ?? count($data ?? []),
        'today' => 0,
        'month' => 0,
    ];
    $totalCount = $totalCount ?? ($counts['all'] ?? count($data ?? []));
    $startIndex = $startIndex ?? 0;
    $currentPage = $currentPage ?? 1;
    $lastPage = $lastPage ?? 1;
    $perPage = $perPage ?? 25;
    $permission = Helper::permissioncheck(28);
    $classType = $classType ?? Helper::classType();
@endphp

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exact Theme Match */
.visitor-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.visitor-page * {
    box-sizing: border-box;
}
.visitor-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner (Arise ERP Signature Dark Navy Theme) */
.visitor-hero {
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
.visitor-hero-text {
    display: flex;
    flex-direction: column;
}
.visitor-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.visitor-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.visitor-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges / KPI Cards */
.visitor-hero-stats {
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
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
}
.hero-stat-badge:hover {
    background: rgba(255,255,255,.28) !important;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.6) !important;
}
.hero-stat-badge.active-filter {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-green:hover, .hero-stat-badge.badge-green.active-filter {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
}
.hero-stat-badge.badge-cyan {
    background: rgba(6, 182, 212, 0.2);
    border-color: rgba(6, 182, 212, 0.4);
    color: #a5f3fc;
}
.hero-stat-badge.badge-cyan:hover, .hero-stat-badge.badge-cyan.active-filter {
    background: #0891b2 !important;
    color: #ffffff !important;
    border-color: #06b6d4 !important;
}

/* Hero Action Buttons */
.visitor-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #e2e8f0 !important;
    color: #001f3d !important;
    border-color: #cbd5e1 !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.22) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1 !important;
    color: #ffffff !important;
    border-color: #0369a1 !important;
}

/* Table Card & Header - Dark Navy Unified */
.visitor-table-card {
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
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
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
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
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

/* Header Titles Row - #002C54 Sticky Top 0 */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 9px 8px;
    height: 38px;
    font-size: 11px;
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

/* In-Column Excel Filters Row - #08335c Sticky Top 38px */
.excel-filter-row th {
    position: sticky;
    top: 38px;
    background: #08335c;
    color: #ffffff;
    padding: 5px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
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
.dash-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #f8fafc !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #edf2f7 !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e2e8f0 !important;
}

/* In-Column Excel Filters Inputs */
.excel-col-filter {
    width: 100%;
    height: 27px;
    padding: 3px 7px;
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
    transition: all .15s;
    white-space: nowrap;
}
.btn-reset-filters:hover {
    background: #0284c7 !important;
    border-color: #38bdf8 !important;
    color: #ffffff !important;
}
.btn-reset-filters:hover i {
    color: #ffffff !important;
}

/* Table Body Rows */
.dash-table tbody td {
    padding: 6px 8px;
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
.row-visitor-today td {
    background: #f0fdf4 !important;
}

/* Visitor Details Specifics */
.visitor-name-box {
    display: flex;
    flex-direction: column;
}
.visitor-name-link {
    font-weight: 650;
    color: #0284c7;
    font-size: 12.5px;
    line-height: 1.3;
    text-decoration: none !important;
    transition: color .15s;
}
.visitor-name-link:hover {
    color: #0369a1;
    text-decoration: underline !important;
}
.visitor-mobile-wrap {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #475569;
}
.btn-row-wa {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: #25d366;
    color: #ffffff !important;
    font-size: 9.5px;
    text-decoration: none !important;
    line-height: 1;
}
.btn-row-wa:hover {
    background: #128c7e !important;
    color: #ffffff !important;
}

/* Student & Class */
.student-info-box {
    display: flex;
    flex-direction: column;
}
.student-name-row {
    font-size: 12px;
}
.badge-class {
    display: inline-block;
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
}

/* ID & Aadhaar */
.id-info-box {
    display: flex;
    flex-direction: column;
}
.badge-id-type {
    display: inline-block;
    font-size: 9.5px;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
}
.cred-pill-id {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 10.5px;
    font-weight: 600;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 1px 4px;
    border-radius: 2px;
}

/* Date Info */
.date-info-box {
    display: flex;
    flex-direction: column;
}
.badge-today-visit {
    display: inline-flex;
    align-items: center;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

/* Visitor Remark */
.visitor-remark-text {
    font-size: 11px;
    color: #475569;
    line-height: 1.3;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.table-btn {
    width: 25px;
    height: 23px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    font-size: 11px;
    text-decoration: none !important;
    transition: all .15s;
    padding: 0;
}
.table-btn:hover {
    background: #002C54 !important;
    color: #ffffff !important;
    border-color: #002C54 !important;
}
.table-btn:hover i {
    color: #ffffff !important;
}
.btn-action-view {
    background: #faf5ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}
.btn-action-view:hover {
    background: #7c3aed !important;
    color: #ffffff !important;
    border-color: #7c3aed !important;
}
.btn-action-view:hover i {
    color: #ffffff !important;
}
.btn-action-edit {
    background: #eff6ff;
    color: #0284c7;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}
.btn-action-edit:hover i {
    color: #ffffff !important;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}
.btn-action-delete:hover i {
    color: #ffffff !important;
}

/* Centered Empty State */
#empty-state-row td {
    padding: 0 !important;
    border: none !important;
    background: #eef2f6 !important;
    vertical-align: middle !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height, 56px) - 200px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 44px;
    color: #94a3b8;
    margin-bottom: 12px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 400px;
    line-height: 1.5;
}
#btn-empty-clear-filters:hover {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}
#btn-empty-clear-filters:hover i {
    color: #ffffff !important;
}

/* Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    background: #002342;
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
    padding: 2px 5px;
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
    font-size: 12px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7 !important;
    border-color: #38bdf8 !important;
    color: #ffffff !important;
}
.page-btn:hover:not(:disabled) i {
    color: #ffffff !important;
}
.page-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
    background: #031426 !important;
    color: #64748b !important;
    border-color: rgba(255,255,255,.1) !important;
}
.page-current-indicator {
    font-size: 11px;
    font-weight: 600;
    padding: 0 6px;
    color: #f1f5f9;
}

/* Modal and General Buttons */
.modal .btn-primary:hover {
    background: #0369a1 !important;
    color: #ffffff !important;
    border-color: #0369a1 !important;
}
.modal .btn-danger:hover {
    background: #b91c1c !important;
    color: #ffffff !important;
    border-color: #b91c1c !important;
}
.modal .btn-secondary:hover {
    background: #475569 !important;
    color: #ffffff !important;
    border-color: #475569 !important;
}

/* Loading State */
.visitor-table-loading {
    opacity: 0.55;
    pointer-events: none;
    transition: opacity 0.15s ease-in-out;
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
    .visitor-hero {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper visitor-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="visitor-page-layout">
                
                {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
                <div class="visitor-hero">
                    <div class="visitor-hero-text">
                        <span class="visitor-kicker"><i class="fa fa-address-book-o mr-1"></i> Reception &amp; Front Desk</span>
                        <h1 class="visitor-title">Visitor Management Directory</h1>
                        <p class="visitor-subtitle">Real-time visitor logs with student tagging, ID verification, in-column Excel filters &amp; pinned pagination</p>
                    </div>

                    {{-- Quick Summary KPI Cards --}}
                    <div class="visitor-hero-stats">
                        <a href="javascript:void(0);" class="hero-stat-badge stat-filter-btn {{ empty($search['date']) ? 'active-filter' : '' }}" data-date="" title="All Visitor Records">
                            <i class="fa fa-users text-info"></i> Total: <b id="stat-all">{{ number_format($counts['all'] ?? 0) }}</b>
                        </a>
                        <a href="javascript:void(0);" class="hero-stat-badge badge-green stat-filter-btn {{ ($search['date'] ?? '') === date('Y-m-d') ? 'active-filter' : '' }}" data-date="{{ date('Y-m-d') }}" title="Today's Visitors">
                            <i class="fa fa-calendar-check-o"></i> Today: <b id="stat-today">{{ number_format($counts['today'] ?? 0) }}</b>
                        </a>
                        <a href="javascript:void(0);" class="hero-stat-badge badge-cyan stat-filter-btn" data-from="{{ date('Y-m-01') }}" data-to="{{ date('Y-m-t') }}" title="This Month's Visitors">
                            <i class="fa fa-calendar"></i> This Month: <b id="stat-month">{{ number_format($counts['month'] ?? 0) }}</b>
                        </a>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="visitor-hero-actions">
                        @if($permission->add ?? true)
                            <a href="{{ url('visitorAdd') }}" class="dash-btn dash-btn-light" title="Add New Visitor">
                                <i class="fa fa-plus mr-1"></i> Add Visitor
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Alert Messages --}}
                @if(session('message'))
                    <div class="alert alert-success py-1 px-3 mb-1 mx-2" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                    </div>
                @endif
                @if(isset($errors) && $errors->any())
                    <div class="alert alert-danger py-1 px-3 mb-1 mx-2" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ $errors->first() }}
                    </div>
                @endif

                {{-- 2. Full-Height Table Card --}}
                <div class="visitor-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-id-badge text-info mr-1"></i> Visitor Logs Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Visitors</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="visitor-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="min-width: 200px;">Visitor Details</th>
                                    <th style="min-width: 170px;">Student &amp; Class</th>
                                    <th style="min-width: 160px;">ID &amp; Aadhaar No.</th>
                                    <th style="min-width: 130px;">Visit Date</th>
                                    <th style="min-width: 220px;">Purpose / Remark</th>
                                    <th style="width: 100px;" class="text-center fixed_action_head">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (#08335c palette) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters-icon" title="Reset All Filters" style="padding: 0 4px; height: 25px; width: 25px; justify-content: center;">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-visitor" placeholder="Search visitor or mobile..." value="{{ $search['name'] ?? '' }}">
                                    </th>
                                    <th>
                                        <div class="d-flex gap-1">
                                            <select class="excel-col-filter" id="filter-class" title="Filter by Class">
                                                <option value="">All Classes</option>
                                                @foreach($classType as $type)
                                                    <option value="{{ $type->id }}" {{ ($type->id == ($search['class_type_id'] ?? '')) ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-id-name" placeholder="Search ID / Aadhaar..." value="{{ $search['id_name'] ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="date" class="excel-col-filter" id="filter-date" title="Filter Date" value="{{ $search['date'] ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-remark" placeholder="Search remark..." value="{{ $search['remark'] ?? '' }}">
                                    </th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="visitor-table-body">
                                @include('visitor.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data ?? []), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ ($perPage ?? 25) == 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">{{ $currentPage ?? 1 }}</span> of <span id="total-pages">{{ $lastPage ?? 1 }}</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

{{-- Modals --}}

{{-- 1. View Visitor Details Modal --}}
<div class="modal fade" id="visitorDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14" id="modalVisitorTitle">
                    <i class="fa fa-id-card mr-1"></i> Visitor Log Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3 pb-2 border-bottom">
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Visitor Name</small>
                        <span id="modalVisitorName" class="font-size-13 font-weight-bold text-dark"></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Mobile Number</small>
                        <div class="d-flex align-items-center gap-1">
                            <span id="modalVisitorMobile" class="font-size-12 font-weight-bold text-dark"></span>
                            <a href="" id="modalWaLink" target="_blank" class="btn-row-wa" style="display:none;" title="Send WhatsApp">
                                <i class="fa fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3 pb-2 border-bottom">
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Student Tagged</small>
                        <span id="modalStudentName" class="font-size-12 font-weight-bold text-dark">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Class</small>
                        <span id="modalClassName" class="badge bg-light text-dark border font-size-11">-</span>
                    </div>
                </div>

                <div class="row g-2 mb-3 pb-2 border-bottom">
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">ID Type / Proof</small>
                        <span id="modalIdName" class="badge bg-light text-dark border font-size-11">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Aadhaar / ID No.</small>
                        <span id="modalAadharNo" class="cred-pill cred-pill-id font-size-11">-</span>
                    </div>
                </div>

                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Visit Date</small>
                    <span id="modalVisitDate" class="font-size-12 font-weight-bold text-dark"></span>
                </div>

                <div class="mb-1">
                    <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block mb-1">Purpose / Remark</small>
                    <div id="modalRemark" class="p-2 bg-light rounded border font-size-12 text-dark" style="white-space: pre-wrap; line-height: 1.5; max-height: 140px; overflow-y: auto;">-</div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Delete Confirmation Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-trash mr-1"></i> Delete Visitor Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('visitorDelete') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <p class="font-size-13 text-dark mb-0">
                        Are you sure you want to delete visitor: <strong id="deleteVisitorNameDisplay"></strong>?
                    </p>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Delete Visitor</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Client-Side Real-Time AJAX Engine --}}
<script>
$(document).ready(function() {
    let currentPage = {{ (int)($currentPage ?? 1) }};
    let lastPage = {{ (int)($lastPage ?? 1) }};
    let currentPerPage = "{{ $perPage ?? 25 }}";
    let isFetching = false;
    let filterTimer = null;
    let fromDateFilter = "{{ $search['from_date'] ?? '' }}";
    let toDateFilter = "{{ $search['to_date'] ?? '' }}";
    const visitorAjaxUrl = "{{ url('visitorView') }}";

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        return {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            name: ($('#filter-visitor').val() || '').trim(),
            class_type_id: $('#filter-class').val() || '',
            id_name: ($('#filter-id-name').val() || '').trim(),
            date: $('#filter-date').val() || '',
            from_date: fromDateFilter,
            to_date: toDateFilter,
            remark: ($('#filter-remark').val() || '').trim(),
        };
    }

    function fetchVisitors(page) {
        if (isFetching) return;
        isFetching = true;
        $('#visitor-grid-table').addClass('visitor-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: visitorAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.html !== undefined)) {
                    $('#visitor-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    const total = parseInt(res.total_count, 10);
                    const start = total > 0 ? parseInt(res.start_index, 10) + 1 : 0;
                    const end = Math.min(parseInt(res.start_index, 10) + (res.html ? $(res.html).filter('tr.visitor-row').length : 0), total);

                    $('#header-records-count').text(total);
                    $('#total-records').text(total);
                    $('#page-start').text(start);
                    $('#page-end').text(end);
                    $('#current-page').text(currentPage);
                    $('#total-pages').text(lastPage);

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage);

                    if (res.counts) {
                        $('#stat-all').text(res.counts.all || 0);
                        $('#stat-today').text(res.counts.today || 0);
                        $('#stat-month').text(res.counts.month || 0);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch visitor records:', error);
            },
            complete: function() {
                isFetching = false;
                $('#visitor-grid-table').removeClass('visitor-table-loading');
            }
        });
    }

    function triggerFilterDebounced() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchVisitors(1);
        }, 300);
    }

    // Text inputs with debounce
    $('#filter-visitor, #filter-id-name, #filter-remark').on('keyup input', function() {
        fromDateFilter = '';
        toDateFilter = '';
        triggerFilterDebounced();
    });

    // Selects and date inputs change instantly
    $('#filter-class, #filter-date').on('change', function() {
        fromDateFilter = '';
        toDateFilter = '';
        currentPage = 1;
        fetchVisitors(1);
    });

    // Rows per page change
    $('#rows-per-page-select').on('change', function() {
        currentPerPage = $(this).val();
        currentPage = 1;
        fetchVisitors(1);
    });

    // Pagination Nav Buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            fetchVisitors(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchVisitors(currentPage);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            currentPage++;
            fetchVisitors(currentPage);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            currentPage = lastPage;
            fetchVisitors(lastPage);
        }
    });

    // KPI Stat Filter Clicks
    $(document).on('click', '.stat-filter-btn', function(e) {
        e.preventDefault();
        $('.stat-filter-btn').removeClass('active-filter');
        $(this).addClass('active-filter');

        const dateVal = $(this).data('date');
        const fromVal = $(this).data('from');
        const toVal = $(this).data('to');

        if (fromVal && toVal) {
            $('#filter-date').val('');
            fromDateFilter = fromVal;
            toDateFilter = toVal;
        } else {
            $('#filter-date').val(dateVal !== undefined ? dateVal : '');
            fromDateFilter = '';
            toDateFilter = '';
        }

        currentPage = 1;
        fetchVisitors(1);
    });

    // Reset / Clear Filters Buttons
    $('#btn-reset-filters, #btn-clear-filters-icon, #btn-empty-clear-filters').on('click', function(e) {
        e.preventDefault();
        $('#filter-visitor').val('');
        $('#filter-class').val('');
        $('#filter-id-name').val('');
        $('#filter-date').val('');
        $('#filter-remark').val('');
        fromDateFilter = '';
        toDateFilter = '';
        $('.stat-filter-btn').removeClass('active-filter');
        $('.stat-filter-btn[data-date=""]').addClass('active-filter');
        currentPage = 1;
        fetchVisitors(1);
    });

    // Visitor Detail Modal Open
    $(document).on('click', '.view-visitor-btn', function() {
        const rawVisitor = $(this).attr('data-visitor');
        if (!rawVisitor) return;
        const item = JSON.parse(rawVisitor);

        $('#modalVisitorName').text(item.visitor_name || '-');
        $('#modalVisitorMobile').text(item.visitor_mobile || '-');

        const mobileClean = (item.visitor_mobile || '').replace(/\D/g, '');
        if (mobileClean.length === 10) {
            $('#modalWaLink').attr('href', 'https://api.whatsapp.com/send?phone=91' + mobileClean).show();
        } else {
            $('#modalWaLink').hide();
        }

        $('#modalStudentName').text(item.stu_name || '-');
        $('#modalClassName').text(item.class_type ? item.class_type.name : (item.class_name || '-'));
        $('#modalIdName').text(item.id_name || '-');
        $('#modalAadharNo').text(item.aadharNo || '-');
        $('#modalVisitDate').text(item.date || '-');
        $('#modalRemark').text(item.remark || 'No remark provided.');

        $('#visitorDetailModal').modal('show');
    });

    // Delete Visitor Modal Open
    $(document).on('click', '.delete-visitor-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name') || 'Visitor';

        $('#delete_id').val(id);
        $('#deleteVisitorNameDisplay').text(name);
        $('#Modal_id').modal('show');
    });
});
</script>
@endsection

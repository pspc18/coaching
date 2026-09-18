@extends('layout.app')

@php
    $counts = $counts ?? [
        'all' => $totalCount ?? count($notices ?? []),
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
    ];
    $totalCount = $totalCount ?? ($counts['all'] ?? count($notices ?? []));
    $startIndex = $startIndex ?? 0;
    $currentPage = $currentPage ?? 1;
    $lastPage = $lastPage ?? 1;
    $perPage = $perPage ?? 25;
    $isAdmin = $isAdmin ?? ((int) Session::get('role_id') === 1);
@endphp

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exact Theme Match */
.notice-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.notice-page * {
    box-sizing: border-box;
}
.notice-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner (Arise ERP Signature Dark Navy Theme) */
.notice-hero {
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
.notice-hero-text {
    display: flex;
    flex-direction: column;
}
.notice-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.notice-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.notice-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges / KPI Cards */
.notice-hero-stats {
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
    background: rgba(255,255,255,.25);
    color: #ffffff;
}
.hero-stat-badge.active-filter {
    background: #0284c7 !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-amber:hover, .hero-stat-badge.badge-amber.active-filter {
    background: #d97706 !important;
    color: #ffffff !important;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-green:hover, .hero-stat-badge.badge-green.active-filter {
    background: #059669 !important;
    color: #ffffff !important;
}
.hero-stat-badge.badge-red {
    background: rgba(239, 68, 68, 0.2);
    border-color: rgba(239, 68, 68, 0.4);
    color: #fca5a5;
}
.hero-stat-badge.badge-red:hover, .hero-stat-badge.badge-red.active-filter {
    background: #dc2626 !important;
    color: #ffffff !important;
}

/* Hero Action Buttons */
.notice-hero-actions {
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
.notice-table-card {
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

/* Notice Cell Specifics */
.notice-title-box {
    display: flex;
    flex-direction: column;
}
.notice-title-link {
    font-weight: 650;
    color: #0284c7;
    font-size: 12.5px;
    line-height: 1.3;
    text-decoration: none !important;
    transition: color .15s;
}
.notice-title-link:hover {
    color: #0369a1;
    text-decoration: underline !important;
}
.notice-preview-msg {
    font-size: 11px;
    color: #64748b;
    line-height: 1.3;
    margin: 2px 0 0 0;
}
.notice-attachment-pill-wrap {
    margin-top: 4px;
}
.notice-pdf-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 2px;
    color: #b91c1c;
    font-size: 10.5px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all .15s;
}
.notice-pdf-pill:hover {
    background: #fecaca;
    color: #991b1b;
}

/* Audience Badges */
.badge-audience {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 2px;
    white-space: nowrap;
}
.badge-audience-role {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
}
.badge-audience-class {
    background: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ede9fe;
}
.badge-audience-specific {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fef3c7;
}
.audience-recipients-sub {
    font-size: 11px;
    color: #475569;
}

/* Validity Box */
.validity-box {
    display: flex;
    flex-direction: column;
}
.validity-badge {
    display: inline-block;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 1px 5px;
    border-radius: 2px;
}
.badge-validity-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-validity-upcoming {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
}
.badge-validity-expired {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}
.validity-dates {
    display: flex;
    flex-direction: column;
    gap: 1px;
    font-size: 10.5px;
}
.validity-line {
    color: #334155;
}
.lbl-dim {
    color: #94a3b8;
    font-size: 10px;
}

/* Status Badges */
.badge-status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 10.5px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 2px;
    white-space: nowrap;
}
.badge-status-approved {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-status-rejected {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
.badge-status-pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}

/* Creator & Reviewer */
.creator-box {
    display: flex;
    flex-direction: column;
    font-size: 11px;
}
.badge-reviewer {
    display: inline-flex;
    align-items: center;
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
}
.badge-reviewer-approved {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-reviewer-rejected {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
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
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.btn-action-edit:hover {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #d97706 !important;
}
.btn-action-edit:hover i {
    color: #ffffff !important;
}
.btn-action-print {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.btn-action-print:hover {
    background: #059669 !important;
    color: #ffffff !important;
    border-color: #059669 !important;
}
.btn-action-print:hover i {
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
.notice-table-loading {
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
    .notice-hero {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper notice-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="notice-page-layout">
                
                {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
                <div class="notice-hero">
                    <div class="notice-hero-text">
                        <span class="notice-kicker"><i class="fa fa-bullhorn mr-1"></i> Communication &amp; Notice Board</span>
                        <h1 class="notice-title">Notice Management</h1>
                        <p class="notice-subtitle">Real-time notice directory with multi-channel broadcasting, in-column Excel filters &amp; pinned pagination</p>
                    </div>

                    {{-- Quick Summary KPI Cards --}}
                    <div class="notice-hero-stats">
                        <a href="javascript:void(0);" class="hero-stat-badge stat-filter-btn {{ empty($status) ? 'active-filter' : '' }}" data-status="" title="All Notices">
                            <i class="fa fa-list-alt text-info"></i> Total: <b id="stat-all">{{ number_format($counts['all'] ?? 0) }}</b>
                        </a>
                        <a href="javascript:void(0);" class="hero-stat-badge badge-amber stat-filter-btn {{ ($status ?? '') === 'pending' ? 'active-filter' : '' }}" data-status="pending" title="Pending Approval">
                            <i class="fa fa-clock-o"></i> Pending: <b id="stat-pending">{{ number_format($counts['pending'] ?? 0) }}</b>
                        </a>
                        <a href="javascript:void(0);" class="hero-stat-badge badge-green stat-filter-btn {{ ($status ?? '') === 'approved' ? 'active-filter' : '' }}" data-status="approved" title="Approved &amp; Published">
                            <i class="fa fa-check-circle"></i> Approved: <b id="stat-approved">{{ number_format($counts['approved'] ?? 0) }}</b>
                        </a>
                        <a href="javascript:void(0);" class="hero-stat-badge badge-red stat-filter-btn {{ ($status ?? '') === 'rejected' ? 'active-filter' : '' }}" data-status="rejected" title="Rejected Notices">
                            <i class="fa fa-times-circle"></i> Rejected: <b id="stat-rejected">{{ number_format($counts['rejected'] ?? 0) }}</b>
                        </a>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="notice-hero-actions">
                        <a href="{{ url('notice-management/create') }}" class="dash-btn dash-btn-light" title="Create New Notice">
                            <i class="fa fa-plus mr-1"></i> Create Notice
                        </a>
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
                <div class="notice-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-bullhorn text-info mr-1"></i> Notices Directory Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Notices</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="notice-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles (Exact match with student list / user view) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="min-width: 260px;">Notice Details</th>
                                    <th style="min-width: 145px;">Target Audience</th>
                                    <th style="min-width: 155px;">Validity Period</th>
                                    <th style="width: 120px;" class="text-center">Status</th>
                                    <th style="min-width: 140px;">Creator &amp; Reviewer</th>
                                    <th style="min-width: 110px;">Created Date</th>
                                    <th style="width: 110px;" class="text-center fixed_action_head">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (#08335c palette) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters-icon" title="Reset All Filters" style="padding: 0 4px; height: 25px; width: 25px; justify-content: center;">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-title" placeholder="Search title or text..." value="{{ $search['title'] ?? '' }}">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-audience" title="Filter by Audience">
                                            <option value="" {{ empty($search['audience_type']) ? 'selected' : '' }}>All Audiences</option>
                                            <option value="role" {{ ($search['audience_type'] ?? '') === 'role' ? 'selected' : '' }}>By Role</option>
                                            <option value="class" {{ ($search['audience_type'] ?? '') === 'class' ? 'selected' : '' }}>By Class</option>
                                            <option value="specific" {{ ($search['audience_type'] ?? '') === 'specific' ? 'selected' : '' }}>Specific People</option>
                                        </select>
                                    </th>
                                    <th>
                                        <div class="d-flex gap-1">
                                            <input type="date" class="excel-col-filter" id="filter-from-date" title="Filter From Date" value="{{ $search['from_date'] ?? '' }}">
                                            <input type="date" class="excel-col-filter" id="filter-to-date" title="Filter To Date" value="{{ $search['to_date'] ?? '' }}">
                                        </div>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-status" title="Filter by Status">
                                            <option value="" {{ empty($search['status']) ? 'selected' : '' }}>All Status</option>
                                            <option value="pending" {{ ($search['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ ($search['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ ($search['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-creator" placeholder="Filter creator..." value="{{ $search['creator'] ?? '' }}">
                                    </th>
                                    <th></th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="notice-table-body">
                                @include('master.notice_management.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($notices ?? []), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
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

{{-- 1. View Notice Details Modal --}}
<div class="modal fade" id="noticeDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14" id="modalNoticeTitle">
                    <i class="fa fa-file-text-o mr-1"></i> Notice Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Target Audience</small>
                        <span id="modalAudience" class="badge bg-light text-dark border font-size-11"></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block">Validity Period</small>
                        <span id="modalValidity" class="font-size-12 font-weight-bold text-dark"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block mb-1">Notice Message</small>
                    <div id="modalNoticeMessage" class="p-3 bg-light rounded border font-size-12 text-dark" style="white-space: pre-wrap; line-height: 1.6; max-height: 260px; overflow-y: auto;"></div>
                </div>

                <div id="modalAttachmentSection" class="mb-3" style="display: none;">
                    <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block mb-1">Attachment</small>
                    <a href="" id="modalAttachmentLink" target="_blank" class="dash-btn dash-btn-outline text-danger border-danger">
                        <i class="fa fa-file-pdf-o mr-1"></i> <span id="modalAttachmentName">Download PDF</span>
                    </a>
                </div>

                <div id="modalReviewSection" class="p-2 rounded border" style="display: none; background: #fff8f8;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <strong class="font-size-11 text-danger" id="modalReviewTitle"><i class="fa fa-gavel mr-1"></i> Review Decision</strong>
                        <span class="font-size-10 text-muted" id="modalReviewDate"></span>
                    </div>
                    <p class="mb-0 font-size-11 text-dark" id="modalReviewNotes"></p>
                </div>

                <div class="mt-3 border-top pt-2">
                    <small class="text-muted font-size-10 text-uppercase font-weight-bold d-block mb-1">
                        Target Recipients List (<span id="modalRecipientsCount">0</span>)
                    </small>
                    <div id="modalRecipientsList" class="d-flex flex-wrap gap-1" style="max-height: 120px; overflow-y: auto;"></div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. Admin Review Modal (Approve / Reject) --}}
@if($isAdmin)
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-gavel mr-1"></i> Review &amp; Approve Notice</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reviewForm" method="post" action="">
                @csrf
                <div class="modal-body p-3">
                    <div class="alert alert-info py-1 px-2 font-size-11 mb-2">
                        <strong id="reviewNoticeTitleDisplay"></strong>
                        <span class="d-block font-size-10 text-muted" id="reviewNoticeCreatorDisplay"></span>
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-size-11 font-weight-bold mb-1">Decision <span class="text-danger">*</span></label>
                        <select name="decision" class="form-control form-control-sm" required>
                            <option value="approved">Approve &amp; Publish</option>
                            <option value="rejected">Reject with Notes</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-size-11 font-weight-bold mb-1">Review Notes / Remarks <span class="text-danger">*</span></label>
                        <textarea name="review_notes" class="form-control form-control-sm" rows="3" placeholder="Provide notes or reason for approval / rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- 3. Delete Confirmation Modal --}}
<div class="modal fade" id="deleteNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-trash mr-1"></i> Delete Notice Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteNoticeForm" method="post" action="">
                @csrf
                <div class="modal-body p-3">
                    <p class="font-size-13 text-dark mb-0">
                        Are you sure you want to permanently delete the notice: <strong id="deleteNoticeTitleDisplay"></strong>?
                    </p>
                    <p class="font-size-11 text-muted mt-1 mb-0">
                        This will remove the notice record, attachment file, and all associated notifications.
                    </p>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Delete Notice</button>
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
    const noticeAjaxUrl = "{{ url('notice-management') }}";

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        return {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            title: ($('#filter-title').val() || '').trim(),
            audience_type: $('#filter-audience').val() || '',
            from_date: $('#filter-from-date').val() || '',
            to_date: $('#filter-to-date').val() || '',
            status: $('#filter-status').val() !== undefined ? $('#filter-status').val() : '',
            creator: ($('#filter-creator').val() || '').trim(),
        };
    }

    function fetchNotices(page) {
        if (isFetching) return;
        isFetching = true;
        $('#notice-grid-table').addClass('notice-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: noticeAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.html !== undefined)) {
                    $('#notice-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    const total = parseInt(res.total_count, 10);
                    const start = total > 0 ? parseInt(res.start_index, 10) + 1 : 0;
                    const end = Math.min(parseInt(res.start_index, 10) + (res.html ? $(res.html).filter('tr.notice-row').length : 0), total);

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
                        $('#stat-pending').text(res.counts.pending || 0);
                        $('#stat-approved').text(res.counts.approved || 0);
                        $('#stat-rejected').text(res.counts.rejected || 0);
                    }

                    // Update active KPI badge
                    const currStatus = $('#filter-status').val();
                    $('.stat-filter-btn').removeClass('active-filter');
                    $('.stat-filter-btn[data-status="' + currStatus + '"]').addClass('active-filter');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch notice records:', error);
            },
            complete: function() {
                isFetching = false;
                $('#notice-grid-table').removeClass('notice-table-loading');
            }
        });
    }

    function triggerFilterDebounced() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchNotices(1);
        }, 300);
    }

    // Text inputs with debounce
    $('#filter-title, #filter-creator').on('keyup input', function() {
        triggerFilterDebounced();
    });

    // Selects and date inputs change instantly
    $('#filter-audience, #filter-status, #filter-from-date, #filter-to-date').on('change', function() {
        currentPage = 1;
        fetchNotices(1);
    });

    // Rows per page change
    $('#rows-per-page-select').on('change', function() {
        currentPerPage = $(this).val();
        currentPage = 1;
        fetchNotices(1);
    });

    // Pagination Nav Buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            fetchNotices(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchNotices(currentPage);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            currentPage++;
            fetchNotices(currentPage);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            currentPage = lastPage;
            fetchNotices(lastPage);
        }
    });

    // KPI Stat Filter Clicks
    $(document).on('click', '.stat-filter-btn', function(e) {
        e.preventDefault();
        const statusVal = $(this).data('status');
        $('#filter-status').val(statusVal);
        currentPage = 1;
        fetchNotices(1);
    });

    // Reset / Clear Filters Buttons
    $('#btn-reset-filters, #btn-clear-filters-icon, #btn-empty-clear-filters').on('click', function(e) {
        e.preventDefault();
        $('#filter-title').val('');
        $('#filter-audience').val('');
        $('#filter-from-date').val('');
        $('#filter-to-date').val('');
        $('#filter-status').val('');
        $('#filter-creator').val('');
        currentPage = 1;
        fetchNotices(1);
    });

    // Notice Detail Modal Open
    $(document).on('click', '.view-notice-btn', function() {
        const rawNotice = $(this).attr('data-notice');
        const creator = $(this).attr('data-creator') || 'Staff';
        const reviewer = $(this).attr('data-reviewer') || '';

        if (!rawNotice) return;
        const notice = JSON.parse(rawNotice);

        $('#modalNoticeTitle').html('<i class="fa fa-file-text-o mr-1"></i> ' + notice.title);
        $('#modalAudience').text((notice.audience_type || '').toUpperCase() + ' BASED');
        $('#modalValidity').text((notice.from_date || '') + ' to ' + (notice.to_date || ''));
        $('#modalNoticeMessage').text(notice.message || 'No description provided.');

        if (notice.attachment_path) {
            $('#modalAttachmentLink').attr('href', '{{ url("notice-management") }}/' + notice.id + '/attachment');
            $('#modalAttachmentName').text(notice.attachment_name || 'Download Attachment PDF');
            $('#modalAttachmentSection').show();
        } else {
            $('#modalAttachmentSection').hide();
        }

        if (notice.review_notes) {
            $('#modalReviewSection').show();
            $('#modalReviewTitle').html('<i class="fa fa-gavel mr-1"></i> ' + (notice.status === 'approved' ? 'Approval' : 'Rejection') + ' Notes (' + (reviewer || 'Admin') + ')');
            $('#modalReviewDate').text(notice.reviewed_at ? notice.reviewed_at : '');
            $('#modalReviewNotes').text(notice.review_notes);
        } else {
            $('#modalReviewSection').hide();
        }

        const recipients = notice.recipients || [];
        $('#modalRecipientsCount').text(recipients.length);
        let recipHtml = '';
        if (recipients.length > 0) {
            recipients.forEach(function(r) {
                const badgeText = r.recipient_name + (r.role_name ? ' (' + r.role_name + ')' : (r.class_name ? ' (' + r.class_name + ')' : ''));
                recipHtml += '<span class="badge bg-light text-dark border font-size-11">' + badgeText + '</span>';
            });
        } else {
            recipHtml = '<span class="text-muted font-size-11">No recipients listed</span>';
        }
        $('#modalRecipientsList').html(recipHtml);

        $('#noticeDetailModal').modal('show');
    });

    // Admin Review Modal Open
    $(document).on('click', '.review-notice-btn', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');
        const creator = $(this).data('creator');

        $('#reviewNoticeTitleDisplay').text(title);
        $('#reviewNoticeCreatorDisplay').text('Created by: ' + creator);
        $('#reviewForm').attr('action', '{{ url("notice-management") }}/' + id + '/review');
        $('#reviewModal').modal('show');
    });

    // Delete Notice Modal Open
    $(document).on('click', '.delete-notice-btn', function() {
        const id = $(this).data('id');
        const title = $(this).data('title');

        $('#deleteNoticeTitleDisplay').text(title);
        $('#deleteNoticeForm').attr('action', '{{ url("notice-management") }}/' + id + '/delete');
        $('#deleteNoticeModal').modal('show');
    });
});
</script>
@endsection

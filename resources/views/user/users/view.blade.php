@extends('layout.app')

@php
    $role = $role ?? Helper::roleType();
    $branch = $branch ?? Helper::getAllBranch();
    $userStats = $userStats ?? [
        'total' => $totalCount ?? count($data ?? []),
        'active' => 0,
        'inactive' => 0,
        'dropped' => 0,
    ];
    $branchLookup = $branchLookup ?? [];
    $userPermission = Helper::permissioncheck(6);
@endphp

@section('styles')
<style>
/* Page Layout & Viewport Fitting */
.user-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.user-page * {
    box-sizing: border-box;
}
.user-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner (Arise ERP Signature Dark Navy Theme) */
.user-hero {
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
.user-hero-text {
    display: flex;
    flex-direction: column;
}
.user-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.user-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.user-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.user-hero-stats {
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
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-gray {
    background: rgba(148, 163, 184, 0.2);
    border-color: rgba(148, 163, 184, 0.4);
    color: #e2e8f0;
}

/* Hero Action Buttons */
.user-hero-actions {
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
.dash-btn-sm {
    height: 22px;
    padding: 0 6px;
    font-size: 10px;
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
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #ffffff;
    border-color: rgba(255,255,255,.6);
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
}

/* Table Card & Header - Dark Navy Unified */
.user-table-card {
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

/* Proper thead Titles Padding & Alignment (Exact Match with StudentList / Admission Grid) */
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

/* Sticky Filter Row with Proper Padding & Dark Navy Theme (No White) - Exact #08335c match */
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

/* In-Column Excel Filters (Dark Navy Palette - Exact Match with StudentList) */
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
    background: #002C54;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Table Body Rows - Soft Slate Palette */
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
.dash-table tbody tr.row-user-inactive td {
    background: #fff8f8;
}
.dash-table tbody tr.row-user-inactive:hover td {
    background: #fee2e2 !important;
}
.dash-table tbody tr.row-user-dropped td {
    background: #f8fafc;
    opacity: .75;
}

/* User Avatar */
.user-avatar-thumb {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    box-shadow: 0 1px 2px rgba(0,0,0,.08);
    transition: transform .15s;
    vertical-align: middle;
}
.user-avatar-thumb:hover {
    transform: scale(1.15);
}
.user-avatar-initial {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: #ffffff;
    text-transform: uppercase;
    box-shadow: 0 1px 2px rgba(0,0,0,.08);
    border: 1px solid rgba(0,0,0,.08);
    user-select: none;
    transition: transform .15s;
    vertical-align: middle;
}
.user-avatar-initial:hover {
    transform: scale(1.15);
}

/* Staff Info Box */
.user-name-box {
    display: flex;
    flex-direction: column;
}
.user-name-text {
    font-weight: 650;
    color: #0f172a;
    font-size: 12px;
    line-height: 1.25;
}
.user-sub-info {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.badge-biomax, .badge-primary-branch {
    font-size: 9.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
}
.badge-primary-branch {
    background: #e0f2fe;
    color: #0369a1;
}

/* Role & Access Branches */
.badge-role {
    display: inline-block;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 1px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}
.access-branches-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    margin-top: 2px;
}
.badge-access-branch {
    font-size: 9px;
    padding: 0 4px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
}

/* Mobile & WhatsApp inside Staff Details */
.mobile-cell-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 0 4px;
    height: 18px;
}
.mobile-text {
    font-family: ui-monospace, monospace;
    font-size: 10px;
    color: #1e293b;
    font-weight: 600;
}
.btn-row-wa {
    width: 14px;
    height: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #25d366;
    color: #ffffff !important;
    border-radius: 2px;
    font-size: 9px;
    text-decoration: none !important;
    line-height: 1;
}
.btn-row-wa:hover {
    background: #128c7e;
}
.email-text {
    font-size: 11px;
    color: #475569;
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
}

/* Credentials Values with Copy & Reveal */
.cred-cell-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.cred-pill {
    display: inline-block;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 5px;
    border-radius: 2px;
}
.cred-pill-user {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.cred-pill-pass {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.btn-copy-icon, .btn-toggle-eye {
    width: 20px;
    height: 20px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #64748b;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    transition: all .15s;
    padding: 0;
}
.btn-copy-icon:hover, .btn-toggle-eye:hover {
    background: #f1f5f9;
    color: #0284c7;
    border-color: #94a3b8;
}
.btn-copy-icon.copied {
    background: #10b981 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
}

/* Status Badges */
.badge-status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 2px;
    white-space: nowrap;
}
.badge-status-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-status-inactive {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
.badge-status-dropped {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.form-select-status {
    height: 23px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    padding: 1px 4px;
    border: 1px solid #cbd5e1;
    cursor: pointer;
}
.status-opt-active {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0;
}
.status-opt-inactive {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}
.status-opt-dropped {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
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
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.btn-action-copy-both:hover {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
}
.btn-action-perm:hover {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
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
    min-height: calc(100vh - var(--header-height) - 190px);
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
    max-width: 380px;
    line-height: 1.5;
}

/* Pinned Bottom Pagination Toolbar */
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

/* Loading State */
.user-table-loading {
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
    .user-hero {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper user-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="user-page-layout">
                
                {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
                <div class="user-hero">
                    <div class="user-hero-text">
                        <span class="user-kicker"><i class="fa fa-users mr-1"></i> Staff &amp; Access Control</span>
                        <h1 class="user-title">Staff &amp; Users Directory</h1>
                        <p class="user-subtitle">Real-time user grid with in-column Excel filters, password reveal privacy &amp; instant timetable access</p>
                    </div>

                    {{-- Quick Summary Stats --}}
                    <div class="user-hero-stats">
                        <span class="hero-stat-badge" title="Total Staff / Users">
                            <i class="fa fa-user-circle text-info"></i> Total: <b id="stat-total">{{ number_format($userStats['total'] ?? 0) }}</b>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Active Users">
                            <i class="fa fa-check-circle"></i> Active: <b id="stat-active">{{ number_format($userStats['active'] ?? 0) }}</b>
                        </span>
                        <span class="hero-stat-badge badge-amber" title="Inactive Users">
                            <i class="fa fa-times-circle"></i> Inactive: <b id="stat-inactive">{{ number_format($userStats['inactive'] ?? 0) }}</b>
                        </span>
                        @if(($userStats['dropped'] ?? 0) > 0)
                            <span class="hero-stat-badge badge-gray" title="Dropped Teachers">
                                <i class="fa fa-user-times"></i> Dropped: <b id="stat-dropped">{{ number_format($userStats['dropped'] ?? 0) }}</b>
                            </span>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="user-hero-actions">
                        @if($userPermission->add ?? true)
                            <a href="{{ url('addUser') }}" class="dash-btn dash-btn-light" title="Add New User">
                                <i class="fa fa-plus mr-1"></i> Add User
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 2. Full-Height Table Card --}}
                <div class="user-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-desktop text-info mr-1"></i> Staff Directory Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount ?? count($data ?? []) }}</span> Users</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="user-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="width: 46px;" class="text-center">Photo</th>
                                    <th style="min-width: 180px;">Staff Details</th>
                                    <th style="min-width: 140px;">Role &amp; Branch</th>
                                    <th style="min-width: 140px;">Email</th>
                                    <th style="min-width: 130px;">Username</th>
                                    <th style="min-width: 145px;">Password</th>
                                    <th style="width: 100px;" class="text-center">Status</th>
                                    <th style="width: 95px;" class="text-center">Timetable</th>
                                    <th style="width: 90px;" class="text-center fixed_action_head">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters-icon" title="Reset All Filters" style="padding: 0 4px; height: 25px; width: 25px; justify-content: center;">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th></th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Search staff or mobile..." value="{{ $search['name'] ?? '' }}">
                                    </th>
                                    <th>
                                        <div class="d-flex gap-1">
                                            <select class="excel-col-filter" id="filter-role" title="Filter by Role">
                                                <option value="">All Roles</option>
                                                @foreach($role ?? [] as $r)
                                                    <option value="{{ $r->id }}" {{ ($search['role_id'] ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                            @if(Session::get('role_id') == 1)
                                                <select class="excel-col-filter" id="filter-branch" title="Filter by Branch" style="max-width: 80px;">
                                                    <option value="">Branch</option>
                                                    @foreach($branch ?? [] as $b)
                                                        <option value="{{ $b->id }}" {{ ($search['branch_id'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-email" placeholder="Search email...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-username" placeholder="Search user...">
                                    </th>
                                    <th></th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="" {{ ($search['status'] ?? '') === '' ? 'selected' : '' }}>All</option>
                                            <option value="1" {{ ($search['status'] ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ ($search['status'] ?? '') === 0 || ($search['status'] ?? '') === '0' ? 'selected' : '' }}>Inactive</option>
                                            <option value="2" {{ ($search['status'] ?? '') == 2 ? 'selected' : '' }}>Dropped</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="user-table-body">
                                @include('user.users.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
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

{{-- Modals Preserved --}}

{{-- 1. Status Change Confirmation Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-toggle-on mr-1"></i> Change User Status</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('userStatus') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="status_id" name="status_id">
                    <input type="hidden" id="id" name="id">
                    <p class="font-size-13 text-dark mb-0">Are you sure you want to change the status of this user?</p>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold">Confirm Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Delete User Confirmation Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-trash mr-1"></i> Delete User Confirmation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('deleteUser') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <p class="font-size-13 text-dark mb-0">Are you sure you want to delete this staff/user account?</p>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Delete Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Teacher Time Table Modal --}}
<div class="modal fade" id="timeTableModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14" id="timetableUserName"><i class="fa fa-clock-o mr-1"></i> Teacher Timetable</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <input type="hidden" id="modal_user_id">
            <div class="modal-body p-3" id="timeTableContent">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted font-size-12">Loading timetable...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. User Permissions Modal --}}
<div class="modal fade" id="userPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-shield mr-1"></i> Manage User Permissions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3" id="userPermissionContent">
                <div class="text-center py-4">
                    <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted font-size-12">Loading permissions...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. Profile Image Enlargement Modal --}}
<div id="profileImgModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header py-2 bg-light">
                <h6 class="modal-title mb-0 font-size-13 text-dark">Profile Image</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <img id="profileImgPreview" src="" style="max-width: 100%; max-height: 480px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

{{-- Client-Side Real-Time AJAX Engine --}}
<script>
$(document).ready(function() {
    let currentPage = {{ $currentPage ?? 1 }};
    let lastPage = {{ $lastPage ?? 1 }};
    let currentPerPage = "{{ $perPage ?? 25 }}";
    let isFetching = false;
    let filterTimer = null;
    const viewUserAjaxUrl = "{{ url('viewUser') }}";

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        return {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            name: ($('#filter-name').val() || '').trim(),
            role_id: $('#filter-role').val() || '',
            branch_id: $('#filter-branch').length ? ($('#filter-branch').val() || '') : '',
            email: ($('#filter-email').val() || '').trim(),
            userName: ($('#filter-username').val() || '').trim(),
            status: $('#filter-status').val() !== undefined ? $('#filter-status').val() : '',
        };
    }

    function fetchUsers(page) {
        if (isFetching) return;
        isFetching = true;
        $('#user-grid-table').addClass('user-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: viewUserAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.status === 200 || res.html !== undefined)) {
                    $('#user-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    $('#page-start').text(res.from);
                    $('#page-end').text(res.to);
                    $('#total-records').text(res.total);
                    $('#header-records-count').text(res.total);
                    $('#current-page').text(res.current_page);
                    $('#total-pages').text(res.last_page);

                    if (res.stats) {
                        $('#stat-total').text(Number(res.stats.total || 0).toLocaleString());
                        $('#stat-active').text(Number(res.stats.active || 0).toLocaleString());
                        $('#stat-inactive').text(Number(res.stats.inactive || 0).toLocaleString());
                        $('#stat-dropped').text(Number(res.stats.dropped || 0).toLocaleString());
                    }

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch users:', xhr);
            },
            complete: function() {
                isFetching = false;
                $('#user-grid-table').removeClass('user-table-loading');
            }
        });
    }

    // Debounced text inputs (350ms)
    $('#filter-name, #filter-email, #filter-username').on('input', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchUsers(1);
        }, 350);
    });

    // Instant dropdown triggers
    $('#filter-role, #filter-branch, #filter-status').on('change', function() {
        currentPage = 1;
        fetchUsers(1);
    });

    // Rows per page dropdown
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchUsers(1);
    });

    // Pagination navigation buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            fetchUsers(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchUsers(currentPage);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            currentPage++;
            fetchUsers(currentPage);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            currentPage = lastPage;
            fetchUsers(currentPage);
        }
    });

    // Reset Filters button
    $('#btn-reset-filters, #btn-clear-filters-icon').on('click', function() {
        $('#filter-name, #filter-email, #filter-username').val('');
        $('#filter-role').val('');
        if ($('#filter-branch').length) $('#filter-branch').val('');
        $('#filter-status').val('1');
        currentPage = 1;
        fetchUsers(1);
    });

    // 1. Password Visibility Eye Toggle
    $(document).on('click', '.btn-toggle-eye', function() {
        var $btn = $(this);
        var $pill = $btn.siblings('.cred-pill-pass');
        var plain = $pill.data('plain');
        var masked = $pill.data('masked') || '••••••••';

        if ($pill.hasClass('pass-revealed')) {
            $pill.removeClass('pass-revealed').addClass('pass-masked').text(masked);
            $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $btn.attr('title', 'Reveal Password');
        } else {
            $pill.removeClass('pass-masked').addClass('pass-revealed').text(plain);
            $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $btn.attr('title', 'Hide Password');
        }
    });

    // 2. Generic Single Copy Button
    $(document).on('click', '.copy-btn', function() {
        var $btn = $(this);
        var textToCopy = $btn.data('copy') || '';
        if (!textToCopy) return;

        copyToClipboard(textToCopy);
        $btn.addClass('copied');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i>');

        setTimeout(function() {
            $btn.removeClass('copied').html(originalHtml);
        }, 1200);
    });

    // 3. Copy Full Credentials Button
    $(document).on('click', '.copy-both-btn', function() {
        var $btn = $(this);
        var name = $btn.data('name') || 'Staff';
        var role = $btn.data('role') || '';
        var user = $btn.data('user') || '';
        var pass = $btn.data('pass') || '';

        var text = "Staff: " + name + (role ? " (Role: " + role + ")" : "") + "\nUsername: " + user + "\nPassword: " + pass;
        copyToClipboard(text);

        $btn.css('background', '#10b981').css('color', '#fff').css('border-color', '#10b981');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i>');

        setTimeout(function() {
            $btn.css('background', '').css('color', '').css('border-color', '').html(originalHtml);
        }, 1200);
    });

    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
    }

    // 4. Status Dropdown (Teacher) and Status Button Handlers
    $(document).on('change', '.statusDrop', function() {
        var status = $(this).val();
        $('#status_id').val(status);
        $('#id').val($(this).data('id'));
        $('#statusModal').modal('show');
    });

    $(document).on('click', '.userStatus', function() {
        var status = $(this).data('status');
        $('#status_id').val(status);
        $('#id').val($(this).data('id'));
    });

    // 5. Delete User ID Set
    $(document).on('click', '.deleteData', function() {
        var delete_id = $(this).data('id');
        $('#delete_id').val(delete_id);
    });

    // 6. Teacher Timetable Modal Handler
    $(document).on('click', '.timeTable', function() {
        var userId = $(this).data('user_id');
        var userName = $(this).data('username') || $(this).attr('data-userName') || 'Teacher';

        $('#modal_user_id').val(userId);
        $('#timetableUserName').html('<i class="fa fa-clock-o mr-1"></i> Timetable - ' + userName);
        $('#timeTableModal').modal('show');
        $('#timeTableContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted font-size-12">Loading timetable...</p></div>');

        $.get("{{ url('user/timetable') }}/" + userId, function(response) {
            $('#timeTableContent').html(response);
        }).fail(function() {
            $('#timeTableContent').html('<div class="alert alert-danger font-size-12 mb-0">Failed to load timetable.</div>');
        });
    });

    // 7. User Permissions Modal Handler
    $(document).on('click', '.view-user-permissions', function() {
        var userId = $(this).data('user');
        var roleId = $(this).data('role');

        $('#userPermissionContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted font-size-12">Loading permissions...</p></div>');
        $('#userPermissionModal').modal('show');

        $.get("{{ url('user/permissions') }}/" + userId, function(data) {
            $('#userPermissionContent').html(data);

            $('.row-select-all').on('change', function() {
                let moduleId = $(this).data('module-id');
                let checked = $(this).is(':checked');
                $('input.permission-checkbox[data-module-id="' + moduleId + '"]').prop('checked', checked);
            });

            $('.check-type').on('change', function() {
                let type = $(this).data('type');
                $('input.permission-checkbox.' + type).prop('checked', $(this).is(':checked'));
            });
        }).fail(function() {
            $('#userPermissionContent').html('<div class="alert alert-danger font-size-12 mb-0">Failed to load permissions.</div>');
        });
    });

    // 8. Profile Image Zoom Modal Handler
    $(document).on('click', '.profileImg', function() {
        var profileImgUrl = $(this).data('img');
        if (profileImgUrl) {
            $('#profileImgPreview').attr('src', profileImgUrl);
            $('#profileImgModal').modal('show');
        }
    });
});
</script>
@endsection
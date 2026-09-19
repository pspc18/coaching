@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exactly Matching admissionView */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}
.admission-page-layout {
    height: calc(100vh - var(--header-height, 60px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner - Exact Arise ERP Style from admissionView */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.admission-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #fff;
}
.admission-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
}
.admission-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 28px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1;
    gap: 5px;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
    border-color: #f1f5f9;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,0.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    border-color: #ffffff;
}

/* Table Card & Header - Dark Navy Unified */
.admission-table-card {
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.14);
    color: #f1f5f9;
    padding: 0 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.18);
    white-space: nowrap;
    line-height: 1;
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

/* thead Titles Row */
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

/* Sticky Filter Row with Proper Padding & Dark Navy Theme (No White) */
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

/* In-Column Excel Filters */
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

.btn-clear-filters {
    width: 26px;
    height: 26px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    color: #ffffff;
    cursor: pointer;
    font-size: 11px;
    transition: all .15s;
}
.btn-clear-filters:hover {
    background: #031426;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Table Body */
.dash-table tbody td {
    padding: 6px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    font-size: 11.5px;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) {
    background: #edf2f7;
}
.dash-table tbody tr:hover {
    background: #e2e8f0;
}

/* Action Buttons & Badges (Standardized ERP Theme) */
.btn-action-icon {
    width: 24px;
    height: 24px;
    min-width: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease;
    line-height: 1;
    padding: 0;
}
.btn-action-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}
.btn-action-disabled {
    background: #f1f5f9 !important;
    color: #94a3b8 !important;
    border-color: #e2e8f0 !important;
    cursor: not-allowed !important;
}
.btn-role-permissions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 24px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    background: #faf5ff;
    color: #7c3aed;
    border: 1px solid #e9d5ff;
    transition: all .15s ease;
    white-space: nowrap;
    cursor: pointer;
    line-height: 1;
}
.btn-role-permissions:hover {
    background: #7c3aed;
    color: #ffffff;
    border-color: #7c3aed;
    box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);
}
.badge-role-id {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    background: #e2e8f0;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
    line-height: 1;
}
.badge-role-system {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
    line-height: 1;
}
.badge-role-custom {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 22px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
    white-space: nowrap;
    line-height: 1;
}
.badge-staff-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 22px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    background: #f8fafc;
    color: #334155;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
    line-height: 1;
}
.badge-staff-count.has-users {
    background: #eff6ff;
    color: #0369a1;
    border-color: #bae6fd;
}

/* Bottom Pagination Toolbar - Dark Navy */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    min-height: 34px;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
    font-weight: 500;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 4px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 11px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    height: 24px;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 0 6px;
    font-size: 11px;
    outline: none;
    cursor: pointer;
    line-height: 22px;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 24px;
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
    transition: all .15s ease;
    padding: 0;
    line-height: 1;
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
    min-height: 260px;
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

/* Table Loading State during AJAX */
.table-loading-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 44, 84, 0.45);
    backdrop-filter: blur(1px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
}
.table-loading-box {
    background: #002342;
    color: #fff;
    border: 1px solid #38bdf8;
    padding: 10px 18px;
    border-radius: 2px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
}

/* ==========================================================================
   ARISE ERP - UNIFIED SIGNATURE MODALS (Always Centered & Theme Aligned)
   ========================================================================== */
.modal.fade .theme-modal-dialog {
    transition: transform 0.2s ease-out, opacity 0.2s ease-out !important;
    transform: scale(0.96) !important;
}
.modal.show .theme-modal-dialog {
    transform: scale(1) !important;
}
.theme-modal-dialog {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: calc(100vh - 40px) !important;
    margin: 20px auto !important;
    max-width: 500px !important;
    width: 95% !important;
}
.theme-modal-content {
    border: none !important;
    border-radius: 3px !important;
    overflow: hidden !important;
    box-shadow: 0 25px 50px -12px rgba(0, 44, 84, 0.45), 0 0 0 1px rgba(0, 44, 84, 0.1) !important;
    background: #ffffff !important;
    width: 100% !important;
}
.theme-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 9px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 48px !important;
    border-top-left-radius: 3px !important;
    border-top-right-radius: 3px !important;
}
.theme-modal-title-box {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.theme-modal-icon {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    background: rgba(56, 189, 248, 0.18) !important;
    border: 1px solid rgba(56, 189, 248, 0.35) !important;
    border-radius: 3px !important;
    color: #38bdf8 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 14px !important;
    flex-shrink: 0 !important;
}
.theme-modal-headings {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: center !important;
    line-height: 1.2 !important;
}
.theme-modal-headings .theme-modal-title,
.theme-modal-headings h5.modal-title,
.theme-modal-headings h5 {
    display: block !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.25 !important;
    letter-spacing: 0.01em !important;
    white-space: nowrap !important;
}
.theme-modal-subtitle {
    display: block !important;
    font-size: 10.5px !important;
    color: #93c5fd !important;
    margin: 2px 0 0 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
    font-weight: 400 !important;
}
.theme-modal-close {
    color: #ffffff !important;
    opacity: 0.85 !important;
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 3px !important;
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 12px !important;
    transition: all 0.15s ease !important;
    padding: 0 !important;
    outline: none !important;
    line-height: 1 !important;
}
.theme-modal-close:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.22) !important;
    color: #ffffff !important;
}
.theme-modal-body {
    padding: 16px 18px !important;
    background: #ffffff !important;
}
.theme-modal-label {
    font-size: 11px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 4px !important;
    display: block !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
}
.theme-modal-input {
    width: 100% !important;
    height: 34px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #0f172a !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    padding: 6px 10px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    outline: none !important;
}
.theme-modal-input:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15) !important;
}
.theme-modal-alert {
    padding: 8px 12px !important;
    border-radius: 2px !important;
    font-size: 11px !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    line-height: 1.4 !important;
}
.theme-modal-alert-info {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-left: 3px solid #3b82f6 !important;
    color: #1e3a8a !important;
}
.theme-modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 9px 16px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 8px !important;
    min-height: 46px !important;
}
.dash-btn-primary {
    background: #002C54 !important;
    color: #ffffff !important;
    border-color: #002C54 !important;
}
.dash-btn-primary:hover {
    background: #001f3d !important;
    border-color: #001f3d !important;
    color: #ffffff !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show m-1 py-1 px-3" role="alert" style="font-size:12px; border-radius:2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-1 py-1 px-3" role="alert" style="font-size:12px; border-radius:2px;">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                {{-- Top Header & Action Banner (admissionView Style) --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-shield mr-1"></i> SYSTEM SETUP &amp; ACCESS CONTROL</span>
                        <h1 class="admission-title">Role Management &amp; Permissions</h1>
                        <p class="admission-subtitle">Real-time Excel-style grid with cached partitioned queries, instant filters &amp; pinned pagination</p>
                    </div>
                    <div class="admission-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#addRoleModal">
                            <i class="fa fa-plus mr-1"></i> Add Role
                        </button>
                    </div>
                </div>

                {{-- Full-Height Table Card with Excel-Like In-Column Filters & Pinned Pagination --}}
                <div class="dash-card admission-table-card position-relative">
                    <div id="tableLoadingOverlay" class="table-loading-overlay" style="display:none;">
                        <div class="table-loading-box">
                            <i class="fa fa-spinner fa-spin text-info"></i>
                            <span>Loading roles...</span>
                        </div>
                    </div>

                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Roles Directory Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $pagination['total_records'] }}</span> Total Roles</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Body --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="roles-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (Arise Dark Navy ERP Style) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 70px;" class="text-center">#ID</th>
                                    <th>Role Name</th>
                                    <th style="width: 160px;" class="text-center">Role Type</th>
                                    <th style="width: 170px;" class="text-center">Assigned Staff</th>
                                    <th style="width: 150px;" class="text-center">Permissions</th>
                                    <th style="width: 110px;" class="text-center fixed_action_head">Action</th>
                                </tr>

                                {{-- Row 2: Excel In-Column Filters (Dark Navy Palette) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <input type="text" class="excel-col-filter text-center" id="filter-id" placeholder="Filter ID..." value="{{ $searchId ?? '' }}">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Filter Role..." value="{{ $search ?? '' }}">
                                    </th>
                                    <th class="text-center">
                                        <select class="excel-col-filter" id="filter-type">
                                            <option value="all" {{ ($typeFilter ?? '') === 'all' ? 'selected' : '' }}>All Types</option>
                                            <option value="system" {{ ($typeFilter ?? '') === 'system' ? 'selected' : '' }}>Core System</option>
                                            <option value="custom" {{ ($typeFilter ?? '') === 'custom' ? 'selected' : '' }}>Custom Role</option>
                                        </select>
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-clear-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="roleTableBody">
                                @include('master.role.role_rows', ['rows' => $rows, 'userCounts' => $userCounts])
                            </tbody>
                        </table>
                    </div>

                    {{-- Bottom Pagination Toolbar - Dark Navy --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info" id="paginationInfoText">
                            Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total_records'] }} records
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rowsPerPage">Rows:</label>
                                <select id="rowsPerPage">
                                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="-1" {{ ($perPage ?? 25) == -1 ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btnPrevPage" {{ $pagination['current_page'] <= 1 ? 'disabled' : '' }}>&lt;</button>
                                <span class="page-current-indicator" id="pageIndicator">Page {{ $pagination['current_page'] }} of {{ $pagination['total_pages'] }}</span>
                                <button type="button" class="page-btn" id="btnNextPage" {{ $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' }}>&gt;</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md theme-modal-dialog" role="document">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header">
                <div class="theme-modal-title-box">
                    <div class="theme-modal-icon">
                        <i class="fa fa-plus-circle"></i>
                    </div>
                    <div class="theme-modal-headings">
                        <h5 class="modal-title theme-modal-title" id="addRoleModalLabel">
                            Add New Role
                        </h5>
                        <div class="theme-modal-subtitle">
                            Define a new system or staff role for access control
                        </div>
                    </div>
                </div>
                <button type="button" class="theme-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Dialog">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="formAddRole" action="{{ url('role_add') }}" method="post">
                @csrf
                <div class="modal-body theme-modal-body">
                    <div class="form-group mb-3">
                        <label class="theme-modal-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="role" id="addRoleInput" class="theme-modal-input" placeholder="e.g. Academic Coordinator, Accountant, Librarian" required autocomplete="off">
                    </div>
                    <div class="theme-modal-alert theme-modal-alert-info">
                        <i class="fa fa-shield text-primary" style="font-size:14px; margin-top:2px;"></i>
                        <div>
                            <strong>Access Guard:</strong> Once created, click <strong>Permissions</strong> in the directory table to assign module-level read, write, and delete privileges.
                        </div>
                    </div>
                </div>
                <div class="modal-footer theme-modal-footer">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 14px; font-size:11px;">
                        <i class="fa fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" id="btnAddRoleSubmit" class="dash-btn dash-btn-primary" style="height:28px; padding:0 16px; font-size:11px;">
                        <i class="fa fa-check mr-1"></i> Save Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md theme-modal-dialog" role="document">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header">
                <div class="theme-modal-title-box">
                    <div class="theme-modal-icon">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                    <div class="theme-modal-headings">
                        <h5 class="modal-title theme-modal-title" id="editRoleModalLabel">
                            Edit Role Name
                        </h5>
                        <div class="theme-modal-subtitle">
                            Update role title and identifier across the system
                        </div>
                    </div>
                </div>
                <button type="button" class="theme-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Dialog">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="formEditRole" method="post">
                @csrf
                <div class="modal-body theme-modal-body">
                    <div class="form-group mb-0">
                        <label class="theme-modal-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="role" id="editRoleName" class="theme-modal-input" placeholder="Enter role name" required autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer theme-modal-footer">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 14px; font-size:11px;">
                        <i class="fa fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" id="btnEditRoleSubmit" class="dash-btn dash-btn-primary" style="height:28px; padding:0 16px; font-size:11px;">
                        <i class="fa fa-check mr-1"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentPage = {{ $pagination['current_page'] ?? 1 }};
    let totalPages = {{ $pagination['total_pages'] ?? 1 }};
    let debounceTimer = null;

    function fetchRoles(page = 1) {
        const search = document.getElementById('filter-name').value.trim();
        const searchId = document.getElementById('filter-id').value.trim();
        const type = document.getElementById('filter-type').value;
        const perPage = document.getElementById('rowsPerPage').value;
        const overlay = document.getElementById('tableLoadingOverlay');

        if (overlay) overlay.style.display = 'flex';

        const params = new URLSearchParams({
            search: search,
            search_id: searchId,
            type: type,
            per_page: perPage,
            page: page
        });

        fetch(`{{ url('role_add') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (overlay) overlay.style.display = 'none';
            if (data.status === 'success') {
                document.getElementById('roleTableBody').innerHTML = data.html;

                // Update Pagination Toolbar & Header Counter
                document.getElementById('paginationInfoText').textContent =
                    `Showing ${data.pagination.from} to ${data.pagination.to} of ${data.pagination.total_records} records`;
                document.getElementById('header-records-count').textContent = data.pagination.total_records;
                document.getElementById('pageIndicator').textContent =
                    `Page ${data.pagination.current_page} of ${data.pagination.total_pages}`;

                currentPage = data.pagination.current_page;
                totalPages = data.pagination.total_pages;

                const btnPrev = document.getElementById('btnPrevPage');
                const btnNext = document.getElementById('btnNextPage');
                if (btnPrev) btnPrev.disabled = (currentPage <= 1);
                if (btnNext) btnNext.disabled = (currentPage >= totalPages);
            }
        })
        .catch(err => {
            if (overlay) overlay.style.display = 'none';
            console.error('Error fetching roles:', err);
        });
    }

    // Live In-Column Filter Listeners (Debounced)
    ['filter-name', 'filter-id'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchRoles(1), 300);
            });
        }
    });

    const typeSelect = document.getElementById('filter-type');
    if (typeSelect) {
        typeSelect.addEventListener('change', () => fetchRoles(1));
    }

    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    if (rowsPerPageSelect) {
        rowsPerPageSelect.addEventListener('change', () => fetchRoles(1));
    }

    const resetBtn = document.getElementById('btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            document.getElementById('filter-name').value = '';
            document.getElementById('filter-id').value = '';
            document.getElementById('filter-type').value = 'all';
            document.getElementById('rowsPerPage').value = '25';
            fetchRoles(1);
        });
    }

    // Pagination Nav Buttons
    const btnPrev = document.getElementById('btnPrevPage');
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) fetchRoles(currentPage - 1);
        });
    }

    const btnNext = document.getElementById('btnNextPage');
    if (btnNext) {
        btnNext.addEventListener('click', () => {
            if (currentPage < totalPages) fetchRoles(currentPage + 1);
        });
    }

    // Edit Role Modal delegation
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-edit-role');
        if (btn) {
            const id = btn.dataset.id;
            const name = btn.dataset.name;

            document.getElementById('editRoleName').value = name;
            document.getElementById('formEditRole').action = `{{ url('role_edit') }}/${id}`;
        }
    });

    // AJAX submit for Add Role Form
    const formAddRole = document.getElementById('formAddRole');
    if (formAddRole) {
        formAddRole.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnAddRoleSubmit');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';

            const formData = new FormData(formAddRole);

            fetch(formAddRole.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                if (data.status === 'success') {
                    $('#addRoleModal').modal('hide');
                    formAddRole.reset();
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Role added successfully.');
                    }
                    fetchRoles(1);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to add role.');
                    } else {
                        alert(data.message || 'Failed to add role.');
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                console.error('Error adding role:', err);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while saving role.');
                }
            });
        });
    }

    // AJAX submit for Edit Role Form
    const formEditRole = document.getElementById('formEditRole');
    if (formEditRole) {
        formEditRole.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('btnEditRoleSubmit');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Updating...';

            const formData = new FormData(formEditRole);

            fetch(formEditRole.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                if (data.status === 'success') {
                    $('#editRoleModal').modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Role edited successfully.');
                    }
                    fetchRoles(currentPage);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Failed to update role.');
                    } else {
                        alert(data.message || 'Failed to update role.');
                    }
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                console.error('Error updating role:', err);
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred while updating role.');
                }
            });
        });
    }
});
</script>

@endsection
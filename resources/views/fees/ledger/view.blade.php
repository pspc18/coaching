@php
    $classType = Helper::classType();
    $getSetting = Helper::getSetting();
@endphp

@extends('layout.app')

@section('title', 'Student Fees Ledger')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP MODERN THEME - STUDENT FEES LEDGER SYSTEM
   Strict adherence to Arise ERP theme pattern (admissionView & ca_report)
   ========================================================================== */

/* Page Layout & Viewport Fitting */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 0 !important;
    overflow: hidden;
}
.admission-page * {
    box-sizing: border-box;
}
.admission-page-layout {
    height: calc(100vh - var(--header-height, 62px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 12px 10px 12px;
    gap: 6px;
    box-sizing: border-box;
    width: 100%;
}

/* Top Hero Banner (Arise ERP Theme) */
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
    flex-shrink: 0;
    width: 100%;
}
.admission-hero-text {
    display: flex;
    flex-direction: column;
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

/* Quick Date Chips */
.dash-chip-btn {
    background: rgba(255, 255, 255, 0.08);
    color: #f1f5f9;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 2px;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
    white-space: nowrap;
    height: 24px;
}
.dash-chip-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.4);
}
.dash-chip-btn.active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
    box-shadow: 0 0 0 1px #38bdf8;
}

/* Date Range Inputs in Hero */
.hero-date-box {
    display: flex;
    align-items: center;
    gap: 3px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 0 5px;
    height: 24px;
}
.hero-date-lbl {
    font-size: 9px;
    font-weight: 700;
    color: rgba(255,255,255,.75);
    text-transform: uppercase;
}
.hero-date-input {
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 10px;
    padding: 0;
    height: 100%;
    outline: none;
    color-scheme: dark;
    width: 86px;
}
.hero-date-input::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: .85;
}

/* Buttons */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    height: 24px;
    line-height: 1.4;
    white-space: nowrap;
}
.dash-btn-light {
    background: #fff;
    color: #002C54;
    border-color: #fff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-outline {
    background: transparent;
    color: #fff;
    border-color: rgba(255,255,255,.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #fff;
    border-color: #fff;
}

/* Summary Statistics Row (6 KPI Cards) */
.ca-stats-strip {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
    flex-shrink: 0;
    width: 100%;
}
.ca-stat-card {
    background: #ffffff;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    min-width: 0;
}
.ca-stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.ca-stat-content {
    min-width: 0;
    line-height: 1.15;
    overflow: hidden;
}
.ca-stat-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: .02em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ca-stat-value {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.stat-color-students .ca-stat-icon { background: #e0f2fe; color: #0284c7; }
.stat-color-assigned .ca-stat-icon { background: #f1f5f9; color: #334155; }
.stat-color-collected .ca-stat-icon { background: #dcfce7; color: #16a34a; }
.stat-color-discount .ca-stat-icon { background: #fef3c7; color: #d97706; }
.stat-color-fine .ca-stat-icon { background: #fee2e2; color: #dc2626; }
.stat-color-pending .ca-stat-icon { background: #fef2f2; color: #e11d48; }

/* Table Card & Header (Arise Dark Navy Unified) */
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
    width: 100%;
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
    overflow-x: hidden;
    position: relative;
    background: #eef2f6;
    width: 100%;
}

/* 12 Columns - Table Design Matching Theme Pattern */
.dash-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Percentage Column Width Allocation (Total = 100%) */
.col-sr       { width: 3%; min-width: 28px; }
.col-adm      { width: 7.5%; min-width: 65px; }
.col-class    { width: 11%; min-width: 80px; } /* Enlarged Class Column */
.col-name     { width: 14%; min-width: 100px; }
.col-father   { width: 16%; min-width: 120px; } /* Father Name + Mobile Combined */
.col-total    { width: 8.5%; min-width: 70px; }
.col-paid     { width: 8%; min-width: 68px; }
.col-fine     { width: 5.5%; min-width: 48px; }
.col-disc     { width: 5.5%; min-width: 48px; }
.col-pending  { width: 8.5%; min-width: 70px; }
.col-status   { width: 7.5%; min-width: 60px; }
.col-action   { width: 5%; min-width: 38px; }

/* Proper thead Titles Padding & Alignment */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 6px;
    height: 34px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    z-index: 22;
    vertical-align: middle;
    box-sizing: border-box;
}

/* Sticky Filter Row with Proper Padding & Dark Navy Theme */
.excel-filter-row th {
    position: sticky;
    top: 34px;
    background: #08335c;
    color: #ffffff;
    padding: 4px 4px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
}

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 24px;
    padding: 2px 5px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    box-sizing: border-box;
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

/* Clear & Reset Buttons */
.btn-clear-filters {
    width: 22px;
    height: 22px;
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
    font-size: 10px;
}
.btn-clear-filters:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}
.btn-reset-filters {
    height: 24px;
    padding: 0 4px;
    font-size: 10px;
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

/* Table Body Rows - Soft Slate Palette */
.dash-table tbody td {
    padding: 5px 6px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-table tbody tr:nth-child(odd) td { background: #f8fafc; }
.dash-table tbody tr:nth-child(even) td { background: #edf2f7; }
.dash-table tbody tr:hover td { background: #e2e8f0 !important; }

/* Custom Row Badges & Links */
.ca-admission-link {
    color: #0284c7;
    font-weight: 700;
    text-decoration: none;
}
.ca-admission-link:hover {
    text-decoration: underline;
    color: #0369a1;
}
.badge-class {
    font-size: 10.5px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.student-name {
    color: #0284c7;
    font-weight: 600;
    text-decoration: none !important;
}
.student-name:hover {
    color: #0369a1;
    text-decoration: underline !important;
}

/* Status Badges */
.ledger-status-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 2px;
    font-weight: 600;
    display: inline-block;
}
.badge-status-paid {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-status-partial {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-status-unpaid {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Action Column & Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}
.ca-action-btn {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    cursor: pointer;
    font-size: 10px;
    transition: all .15s;
    text-decoration: none !important;
}
.ca-action-btn.btn-view { color: #0284c7; }
.ca-action-btn.btn-view:hover { background: #0284c7; color: #fff; border-color: #0284c7; }

/* Sticky Action Column */
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

/* Pinned Summary Totals Bar */
.ca-pinned-summary-bar {
    background: #002C54;
    border-top: 1px solid rgba(255,255,255,.14);
    border-bottom: 1px solid #001f3d;
    flex-shrink: 0;
    z-index: 10;
    width: 100%;
}
.ca-summary-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 2px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
}
.ca-summary-pill.pill-net {
    background: #dc2626;
    border-color: #ef4444;
}
.pill-lbl {
    font-size: 9.5px;
    color: rgba(255,255,255,.7);
    text-transform: uppercase;
    letter-spacing: .02em;
}
.pill-val {
    font-size: 11px;
    font-weight: 700;
}

/* Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    padding: 5px 12px;
    background: #002342;
    border-top: 1px solid rgba(255,255,255,.12);
    color: #ffffff;
    flex-shrink: 0;
    width: 100%;
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

/* Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 45px 16px;
    text-align: center;
    color: #64748b;
    background: #eef2f6;
    width: 100%;
}
.dash-empty-icon {
    font-size: 36px;
    margin-bottom: 8px;
    color: #94a3b8;
}
.dash-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
}
.dash-empty-subtitle {
    font-size: 11.5px;
    color: #64748b;
}

/* Loading Overlay */
.ca-table-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(238, 242, 246, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 50;
}
.ca-table-loading-spinner {
    background: #002C54;
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 2px;
    font-weight: 600;
    font-size: 11.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Ledger Modal Customizations */
#exampleModal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
#exampleModal .modal-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
}
#exampleModal .close {
    color: #ffffff;
    opacity: 0.8;
}
#exampleModal .close:hover {
    opacity: 1;
}
#exampleModal .modal-body {
    background: #f8fafc;
    padding: 12px;
}
#exampleModal .table {
    font-size: 11px;
    margin-bottom: 0;
}
#exampleModal .sky_tr {
    background: #002C54 !important;
    color: #ffffff !important;
}
#exampleModal .label1 {
    font-size: 9px;
    padding: 1px 4px;
    border-radius: 2px;
    font-weight: 600;
}
#exampleModal .label-success-custom {
    border: #059669 1px solid;
    color: #059669;
    background: #ecfdf5;
}
#exampleModal .label-danger-custom {
    border: #dc2626 1px solid;
    color: #dc2626;
    background: #fef2f2;
}

/* Print Media Optimization */
@media print {
    .main-sidebar, .main-header, .admission-hero, .excel-filter-row, .table-pagination-bar, .ca-action-btn, .content-header {
        display: none !important;
    }
    .admission-page, .admission-page-layout, .table-scroll-container {
        height: auto !important;
        overflow: visible !important;
        background: #ffffff !important;
    }
    .admission-table-card {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    .dash-table thead {
        position: static !important;
    }
    .header-titles-row th {
        background: #002C54 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .dash-table tbody td {
        color: #000000 !important;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                {{-- 1. Top Hero & Quick Range Banner (Arise ERP Theme) --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-book mr-1"></i> Fees Ledger & Audit Accounts</span>
                        <h1 class="admission-title">{{ __('Student Fees Ledger') }}</h1>
                        <p class="admission-subtitle">Real-time student fee accounts, collection audit & balance tracking (Strict d-m-Y format)</p>
                    </div>
                    <div class="admission-hero-actions">
                        {{-- Quick Date Range Chips --}}
                        <button type="button" class="dash-chip-btn" data-range="today">Today</button>
                        <button type="button" class="dash-chip-btn" data-range="yesterday">Yesterday</button>
                        <button type="button" class="dash-chip-btn" data-range="this_month">This Month</button>
                        <button type="button" class="dash-chip-btn" data-range="last_month">Last Month</button>
                        <button type="button" class="dash-chip-btn" data-range="this_fy">This FY</button>

                        {{-- Date range inputs --}}
                        <div class="hero-date-box ml-1">
                            <span class="hero-date-lbl">From</span>
                            <input type="date" class="hero-date-input" id="hero-starting" value="{{ $search['starting'] ?? '' }}" title="Payment From Date">
                        </div>
                        <div class="hero-date-box">
                            <span class="hero-date-lbl">To</span>
                            <input type="date" class="hero-date-input" id="hero-ending" value="{{ $search['ending'] ?? '' }}" title="Payment To Date">
                        </div>

                        <button type="button" class="dash-btn dash-btn-outline ml-1" id="btn-reset-hero" title="Reset All Filters">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </button>
                        <a href="{{ url('fee_dashboard') }}" class="dash-btn dash-btn-light" title="Back to Fee Dashboard">
                            <i class="fa fa-arrow-left mr-1"></i> {{ __('messages.Back') }}
                        </a>
                    </div>
                </div>

                {{-- 2. Summary Statistics Row (6 KPI Cards) --}}
                <div class="ca-stats-strip">
                    <div class="ca-stat-card stat-color-students">
                        <div class="ca-stat-icon"><i class="fa fa-users"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Students</div>
                            <div class="ca-stat-value" id="stat-total-students">{{ number_format($stats['total_students'] ?? $totalCount) }}</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-assigned">
                        <div class="ca-stat-icon"><i class="fa fa-inr"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Assigned</div>
                            <div class="ca-stat-value">₹<span id="stat-total-assigned">{{ number_format($stats['total_assigned'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-collected">
                        <div class="ca-stat-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Collected</div>
                            <div class="ca-stat-value">₹<span id="stat-total-collected">{{ number_format($stats['total_collected'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-discount">
                        <div class="ca-stat-icon"><i class="fa fa-tag"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Discount</div>
                            <div class="ca-stat-value">₹<span id="stat-total-discount">{{ number_format($stats['total_discount'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-fine">
                        <div class="ca-stat-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Fine</div>
                            <div class="ca-stat-value">₹<span id="stat-total-fine">{{ number_format($stats['total_fine'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-pending">
                        <div class="ca-stat-icon"><i class="fa fa-hourglass-half"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Pending</div>
                            <div class="ca-stat-value">₹<span id="stat-total-pending">{{ number_format($stats['total_pending'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- 3. Main Dark Navy Table Card with Dual-Row Sticky Header --}}
                <div class="dash-card admission-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Student Fee Accounts Register</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Students</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Container --}}
                    <div class="table-scroll-container">
                        {{-- Loading Overlay --}}
                        <div class="ca-table-loading-overlay" id="ca-loading-overlay">
                            <div class="ca-table-loading-spinner">
                                <i class="fa fa-spinner fa-spin"></i> Loading Data...
                            </div>
                        </div>

                        <table class="dash-table" id="ledger-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (12 Sequential Columns Matching Theme Pattern) --}}
                                <tr class="header-titles-row">
                                    <th class="text-center col-sr">#</th>
                                    <th class="col-adm">Adm No.</th>
                                    <th class="col-class">Class</th>
                                    <th class="col-name">Student Name</th>
                                    <th class="col-father">Father / Mobile</th>
                                    <th class="text-right col-total">Total Fee</th>
                                    <th class="text-right col-paid">Paid Fee</th>
                                    <th class="text-right col-fine">Fine</th>
                                    <th class="text-right col-disc">Discount</th>
                                    <th class="text-right col-pending">Pending</th>
                                    <th class="text-center col-status">Status</th>
                                    <th class="text-center col-action">{{ __('messages.Action') }}</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (12 Matching Columns) --}}
                                <tr class="excel-filter-row">
                                    {{-- 1. Clear Filters --}}
                                    <th class="text-center col-sr">
                                        <button type="button" class="btn-clear-filters" title="Clear all filters">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </th>

                                    {{-- 2. Admission No Filter --}}
                                    <th class="col-adm">
                                        <input type="text" class="excel-col-filter" id="filter-admission-no" placeholder="Adm..." value="{{ $search['admission_no'] ?? '' }}">
                                    </th>

                                    {{-- 3. Class Filter --}}
                                    <th class="col-class">
                                        <select class="excel-col-filter" id="filter-class" title="Class">
                                            <option value="">All Classes</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $class)
                                                    <option value="{{ $class->id }}" {{ ($search['class_type_id'] ?? '') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>

                                    {{-- 4. Student Name Filter --}}
                                    <th class="col-name">
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Student..." value="{{ $search['name'] ?? '' }}">
                                    </th>

                                    {{-- 5. Father / Mobile Filter --}}
                                    <th class="col-father">
                                        <input type="text" class="excel-col-filter" id="filter-father" placeholder="Father / Mobile..." value="{{ $search['father_name'] ?? '' }}">
                                    </th>

                                    {{-- 6. Total Fees Placeholder --}}
                                    <th class="text-right text-muted col-total">-</th>

                                    {{-- 7. Paid Fees Placeholder --}}
                                    <th class="text-right text-muted col-paid">-</th>

                                    {{-- 8. Fine Placeholder --}}
                                    <th class="text-right text-muted col-fine">-</th>

                                    {{-- 9. Discount Placeholder --}}
                                    <th class="text-right text-muted col-disc">-</th>

                                    {{-- 10. Pending Fees Placeholder --}}
                                    <th class="text-right text-muted col-pending">-</th>

                                    {{-- 11. Payment Status Filter --}}
                                    <th class="col-status">
                                        <select class="excel-col-filter" id="filter-status" title="Status">
                                            <option value="">All</option>
                                            <option value="paid" {{ ($search['status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="partial" {{ ($search['status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                                            <option value="unpaid" {{ ($search['status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        </select>
                                    </th>

                                    {{-- 12. Action Reset --}}
                                    <th class="text-center col-action">
                                        <button type="button" class="btn-reset-filters" title="Reset">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            {{-- Dynamic Table Body --}}
                            <tbody id="ledger-table-body">
                                @include('fees.ledger.ledger_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Pinned Summary Totals Bar (Directly Above Table Pagination Bar) --}}
                    <div class="ca-pinned-summary-bar">
                        <div class="d-flex align-items-center justify-content-between px-3 py-1 flex-wrap" style="min-height: 32px;">
                            <div class="d-flex align-items-center font-weight-bold text-white" style="font-size: 11px;">
                                <i class="fa fa-calculator text-info mr-2"></i>
                                <span>Summary Totals (Filtered):</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Assigned:</span>
                                    <span class="pill-val text-white" id="foot-total-assigned">₹{{ number_format($stats['total_assigned'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Collected:</span>
                                    <span class="pill-val text-success" id="foot-total-collected">₹{{ number_format($stats['total_collected'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Discount:</span>
                                    <span class="pill-val text-warning" id="foot-total-discount">₹{{ number_format($stats['total_discount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Fine:</span>
                                    <span class="pill-val text-danger" id="foot-total-fine">₹{{ number_format($stats['total_fine'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill pill-net">
                                    <span class="pill-lbl" style="color: rgba(255,255,255,.9);">Pending Balance:</span>
                                    <span class="pill-val text-white font-weight-bold" id="foot-total-pending">₹{{ number_format($stats['total_pending'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Pinned Bottom Pagination Toolbar (Arise ERP Theme) --}}
                    <div class="dash-card-footer table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> 
                            to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> 
                            of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows per page:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" {{ $currentPage <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ $currentPage <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">{{ $currentPage }}</span> of <span id="total-pages">{{ $lastPage }}</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ $currentPage >= $lastPage ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ $currentPage >= $lastPage ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
</div>

{{-- 6. Student Fees Ledger Statement Modal --}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title mb-0" id="exampleModalLabel"><i class="fa fa-file-text-o mr-1"></i> Student Ledger Statement</h5>
                    <button class="btn btn-success btn-xs ml-3 btn-print" title="Print Ledger History">
                        <i class="fa fa-print mr-1"></i> Print Statement
                    </button>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body response">
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                    <div>Loading student ledger statement...</div>
                </div>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    const ajaxUrl = "{{ url('fees/ledger') }}";
    let currentPage = {{ $currentPage }};
    let lastPage = {{ $lastPage }};
    let currentPerPage = "{{ $perPage }}";
    let filterTimer = null;
    let activeAjax = null;

    // Helper: format currency
    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Collect all filter parameters
    function getFilterParams(targetPage = 1) {
        return {
            page: targetPage,
            per_page: currentPerPage,
            admission_no: $.trim($('#filter-admission-no').val()),
            class_type_id: $('#filter-class').val(),
            name: $.trim($('#filter-name').val()),
            father_name: $.trim($('#filter-father').val()),
            status: $('#filter-status').val(),
            starting: $('#hero-starting').val(),
            ending: $('#hero-ending').val()
        };
    }

    // Execute Real-Time AJAX Fetch
    function fetchData(targetPage = 1) {
        if (activeAjax && activeAjax.readyState !== 4) {
            activeAjax.abort();
        }

        $('#ca-loading-overlay').css('display', 'flex');

        const params = getFilterParams(targetPage);

        activeAjax = $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: params,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.status === 'success') {
                    // Update table rows
                    $('#ledger-table-body').html(res.html);

                    // Update pagination state
                    currentPage = parseInt(res.current_page) || 1;
                    lastPage = parseInt(res.last_page) || 1;

                    $('#page-start').text(res.from || 0);
                    $('#page-end').text(res.to || 0);
                    $('#total-records').text(res.total || 0);
                    $('#header-records-count').text(res.total || 0);
                    $('#current-page').text(currentPage);
                    $('#total-pages').text(lastPage);

                    // Update buttons
                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage);

                    // Update KPI Cards & Pinned Summary Bar
                    if (res.stats) {
                        const s = res.stats;
                        $('#stat-total-students').text(parseInt(s.total_students || 0).toLocaleString('en-IN'));
                        $('#stat-total-assigned').text(formatMoney(s.total_assigned));
                        $('#stat-total-collected').text(formatMoney(s.total_collected));
                        $('#stat-total-discount').text(formatMoney(s.total_discount));
                        $('#stat-total-fine').text(formatMoney(s.total_fine));
                        $('#stat-total-pending').text(formatMoney(s.total_pending));

                        $('#foot-total-assigned').text('₹' + formatMoney(s.total_assigned));
                        $('#foot-total-collected').text('₹' + formatMoney(s.total_collected));
                        $('#foot-total-discount').text('₹' + formatMoney(s.total_discount));
                        $('#foot-total-fine').text('₹' + formatMoney(s.total_fine));
                        $('#foot-total-pending').text('₹' + formatMoney(s.total_pending));
                    }
                }
            },
            error: function(xhr, status, error) {
                if (status !== 'abort') {
                    console.error('AJAX Fetch Error:', error);
                }
            },
            complete: function() {
                $('#ca-loading-overlay').hide();
            }
        });
    }

    // Debounced filter trigger
    function queueFilter() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            fetchData(1);
        }, 300);
    }

    // Input filter events
    $('.excel-col-filter').on('input', function() {
        queueFilter();
    });

    // Select filter events
    $('select.excel-col-filter').on('change', function() {
        fetchData(1);
    });

    // Date range inputs in hero
    $('#hero-starting, #hero-ending').on('change', function() {
        $('.dash-chip-btn').removeClass('active');
        fetchData(1);
    });

    // Rows per page selector
    $('#rows-per-page-select').on('change', function() {
        currentPerPage = $(this).val();
        fetchData(1);
    });

    // Pagination Nav Buttons
    $('#btn-first').on('click', function() { if (currentPage > 1) fetchData(1); });
    $('#btn-prev').on('click', function() { if (currentPage > 1) fetchData(currentPage - 1); });
    $('#btn-next').on('click', function() { if (currentPage < lastPage) fetchData(currentPage + 1); });
    $('#btn-last').on('click', function() { if (currentPage < lastPage) fetchData(lastPage); });

    // Clear filters button in column header
    $('.btn-clear-filters, .btn-reset-filters, #btn-reset-hero').on('click', function() {
        $('.excel-col-filter').val('');
        $('#hero-starting').val('');
        $('#hero-ending').val('');
        $('.dash-chip-btn').removeClass('active');
        fetchData(1);
    });

    // Helper: format date to YYYY-MM-DD for inputs
    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Quick Date Range Chips
    $('.dash-chip-btn').on('click', function() {
        const range = $(this).data('range');
        $('.dash-chip-btn').removeClass('active');
        $(this).addClass('active');

        const now = new Date();
        let start = new Date();
        let end = new Date();

        if (range === 'today') {
            // start & end are today
        } else if (range === 'yesterday') {
            start.setDate(now.getDate() - 1);
            end.setDate(now.getDate() - 1);
        } else if (range === 'this_month') {
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (range === 'last_month') {
            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            end = new Date(now.getFullYear(), now.getMonth(), 0);
        } else if (range === 'this_fy') {
            const currentYear = now.getFullYear();
            if (now.getMonth() >= 3) {
                start = new Date(currentYear, 3, 1);
                end = new Date(currentYear + 1, 2, 31);
            } else {
                start = new Date(currentYear - 1, 3, 1);
                end = new Date(currentYear, 2, 31);
            }
        }

        $('#hero-starting').val(formatDate(start));
        $('#hero-ending').val(formatDate(end));
        fetchData(1);
    });

    // Ledger History Statement Modal Trigger (Event Delegation)
    $(document).on('click', '.btn-view.data', function() {
        const id = $(this).data('id');
        const baseUrl = "{{ url('/') }}";

        $('#exampleModal .response').html(`
            <div class="text-center py-4 text-muted">
                <i class="fa fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                <div>Loading student ledger statement...</div>
            </div>
        `);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: baseUrl + '/fees_ledger_view',
            data: { admission_id: id },
            dataType: 'json',
            success: function(response) {
                $('#exampleModal .response').html(response.html);
            },
            error: function() {
                $('#exampleModal .response').html(`
                    <div class="alert alert-danger mb-0">
                        <i class="fa fa-exclamation-circle mr-1"></i> Failed to load student ledger data. Please try again.
                    </div>
                `);
            }
        });
    });

    // Print Modal Ledger History
    $('.btn-print').on('click', function() {
        const content = $('#exampleModal .response').html();
        const leftLogo = "{{ env('IMAGE_SHOW_PATH') . '/setting/left_logo/' . ($getSetting['left_logo'] ?? '') }}";
        const schoolName = "{{ $getSetting['name'] ?? '' }}";

        const htmlContent = `
            <table style="width: 100%; border: none; margin-bottom: 15px;">
                <tr>
                    <td style="width: 110px; border: none; vertical-align: middle;">
                        <img width="90" src="${leftLogo}" alt="Logo" />
                    </td>
                    <td style="border: none; text-align: center; vertical-align: middle;">
                        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #002C54;">${schoolName}</h2>
                        <div style="font-size: 13px; font-weight: 600; color: #475569; margin-top: 4px;">Student Fee Ledger Statement</div>
                    </td>
                </tr>
            </table>
            <hr style="border: 0; border-top: 2px solid #002C54; margin-bottom: 15px;" />
        `;

        const printWindow = window.open('', '', 'height=700,width=950');
        printWindow.document.write('<html><head><title>Student Ledger Statement</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
        printWindow.document.write(`
            <style>
                body { padding: 16px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; }
                table { width: 100%; border-collapse: collapse; margin-top: 8px; }
                th, td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 11px; }
                th { background-color: #002C54 !important; color: #ffffff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .sky_tr th { background-color: #002C54 !important; color: #ffffff !important; }
                @media print {
                    @page { size: auto; margin: 10mm; }
                    body { padding: 0; }
                }
            </style>
        `);
        printWindow.document.write('</head><body>');
        printWindow.document.write(htmlContent);
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(function() {
            printWindow.print();
        }, 300);
    });
});
</script>
@endsection
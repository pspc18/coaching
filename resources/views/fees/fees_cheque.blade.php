@php
    $classType = $classType ?? Helper::classType();
    $paymentModes = $paymentModes ?? Helper::getPaymentMode();
    $search = $search ?? [];
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $lastPage = $lastPage ?? 1;
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $stats = $stats ?? [
        'total_count'        => $totalCount,
        'filtered_amount'    => 0,
        'pending_count'      => 0,
        'pending_amount'     => 0,
        'realised_count'     => 0,
        'realised_amount'    => 0,
        'dishonoured_count'  => 0,
        'dishonoured_amount' => 0,
    ];
    $currentStatusFilter = isset($search['status']) && $search['status'] !== '' ? (string) $search['status'] : '1';
@endphp

@extends('layout.app')

@section('title', 'Fees Cheque & Realisation Register')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP MODERN THEME - FEES CHEQUE & REALISATION REGISTER
   Strict adherence to Arise ERP theme pattern (fees/ledger & ca_report)
   ========================================================================== */

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

/* Top Hero Banner */
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

/* Quick Chips */
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

/* Summary Statistics Row (5 KPI Cards) */
.ca-stats-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
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
    cursor: pointer;
    transition: all .15s;
}
.ca-stat-card:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
}
.ca-stat-card.active-stat-filter {
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
    background: #f8fafc;
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
.ca-stat-sub {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
}

.stat-color-total .ca-stat-icon { background: #e0f2fe; color: #0284c7; }
.stat-color-pending .ca-stat-icon { background: #fffbeb; color: #d97706; }
.stat-color-realised .ca-stat-icon { background: #dcfce7; color: #16a34a; }
.stat-color-dishonoured .ca-stat-icon { background: #fee2e2; color: #dc2626; }
.stat-color-amount .ca-stat-icon { background: #f1f5f9; color: #002C54; }

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

/* Status Filter Pills inside Card Header */
.status-tab-group {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.status-tab-pill {
    background: rgba(255,255,255,.08);
    color: #cbd5e1;
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 2px;
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
}
.status-tab-pill:hover {
    background: rgba(255,255,255,.18);
    color: #fff;
}
.status-tab-pill.active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #fff;
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
    width: 100%;
}

/* Table Design */
.dash-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    min-width: 1100px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Column Width Allocation (Total = 100%) */
.col-sr      { width: 3.5%; }
.col-receipt { width: 8.5%; }
.col-status  { width: 11.5%; }
.col-adm     { width: 6.5%; }
.col-class   { width: 8.5%; }
.col-name    { width: 11.5%; }
.col-father  { width: 10.5%; }
.col-mobile  { width: 8%; }
.col-cheque  { width: 10.5%; }
.col-date    { width: 7%; }
.col-mode    { width: 6%; }
.col-amount  { width: 7%; }
.col-action  { width: 11%; }

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
}

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
}

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
    padding: 0 6px;
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

/* Table Body Rows */
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

/* Row Links & Badges */
.ca-invoice-link {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    border-radius: 2px;
    padding: 2px 6px;
    font-size: 10.5px;
    cursor: pointer;
    transition: all .15s;
}
.ca-invoice-link:hover {
    background: #1d4ed8;
    color: #ffffff !important;
    border-color: #1d4ed8;
}
.ca-invoice-link:hover i {
    color: #ffffff !important;
}
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
.badge-mode {
    font-size: 10px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    padding: 2px 6px;
    border-radius: 2px;
    font-weight: 600;
}
.btn-slip-preview {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    cursor: pointer;
}
.btn-slip-preview:hover {
    background: #0284c7;
    color: #fff;
}

/* Cheque Status Badges */
.cheque-status-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 2px;
    font-weight: 700;
    display: inline-block;
}
.badge-cheque-realised {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-cheque-pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-cheque-dishonoured {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Action Select inside Row */
.cheque-action-select {
    width: 100%;
    height: 24px;
    padding: 1px 4px;
    font-size: 10.5px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #94a3b8;
    border-radius: 2px;
    cursor: pointer;
    outline: none;
}
.cheque-action-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
}

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
    background: #0284c7;
    border-color: #38bdf8;
}
.pill-lbl {
    font-size: 9.5px;
    color: rgba(255,255,255,.75);
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
.page-btn, .page-num-btn {
    min-width: 26px;
    height: 24px;
    padding: 0 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    transition: all .15s;
}
.page-btn:hover:not(:disabled), .page-num-btn:hover:not(.active) {
    background: #0284c7;
    border-color: #0284c7;
}
.page-num-btn.active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
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

/* Empty State & Loading Overlay */
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

/* Modal Styling */
#statusModal .modal-header, #profileImgModal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
#statusModal .modal-title, #profileImgModal .modal-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="admission-page-layout">

                {{-- 1. Top Hero & Quick Range Banner --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-credit-card mr-1"></i> Cheque & Pending Payment Realisation</span>
                        <h1 class="admission-title">{{ __('Fees Cheque Register') }}</h1>
                        <p class="admission-subtitle">Real-time cheque realisation tracking, bounce/dishonour handling & payment clearance audit</p>
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

                {{-- 2. Summary Statistics Row (5 Clickable KPI Cards) --}}
                <div class="ca-stats-strip">
                    <div class="ca-stat-card stat-color-pending {{ $currentStatusFilter === '1' ? 'active-stat-filter' : '' }}" data-status-card="1" title="Click to filter Pending Realisation">
                        <div class="ca-stat-icon"><i class="fa fa-clock-o"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Pending Realisation</div>
                            <div class="ca-stat-value">₹<span id="stat-pending-amount">{{ number_format($stats['pending_amount'] ?? 0, 2) }}</span></div>
                            <div class="ca-stat-sub"><span id="stat-pending-count">{{ number_format($stats['pending_count'] ?? 0) }}</span> Cheques</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-realised {{ $currentStatusFilter === '0' ? 'active-stat-filter' : '' }}" data-status-card="0" title="Click to filter Realised Cheques">
                        <div class="ca-stat-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Cheque Realised</div>
                            <div class="ca-stat-value">₹<span id="stat-realised-amount">{{ number_format($stats['realised_amount'] ?? 0, 2) }}</span></div>
                            <div class="ca-stat-sub"><span id="stat-realised-count">{{ number_format($stats['realised_count'] ?? 0) }}</span> Cleared</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-dishonoured {{ $currentStatusFilter === '2' ? 'active-stat-filter' : '' }}" data-status-card="2" title="Click to filter Dishonoured Cheques">
                        <div class="ca-stat-icon"><i class="fa fa-times-circle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Cheque Dishonoured</div>
                            <div class="ca-stat-value">₹<span id="stat-dishonoured-amount">{{ number_format($stats['dishonoured_amount'] ?? 0, 2) }}</span></div>
                            <div class="ca-stat-sub"><span id="stat-dishonoured-count">{{ number_format($stats['dishonoured_count'] ?? 0) }}</span> Bounced</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-total {{ $currentStatusFilter === 'all' ? 'active-stat-filter' : '' }}" data-status-card="all" title="Click to view All Statuses">
                        <div class="ca-stat-icon"><i class="fa fa-list-alt"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Filtered Records</div>
                            <div class="ca-stat-value" id="stat-total-count">{{ number_format($totalCount) }}</div>
                            <div class="ca-stat-sub">Matching Entries</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-amount" style="cursor: default;">
                        <div class="ca-stat-icon"><i class="fa fa-inr"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Filtered Amount</div>
                            <div class="ca-stat-value" style="color: #059669;">₹<span id="stat-filtered-amount">{{ number_format($stats['filtered_amount'] ?? 0, 2) }}</span></div>
                            <div class="ca-stat-sub">Total Value</div>
                        </div>
                    </div>
                </div>

                {{-- 3. Main Dark Navy Table Card with Dual-Row Sticky Header --}}
                <div class="dash-card admission-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Cheque Realisation & Clearance Register</h3>
                            <div class="status-tab-group">
                                <button type="button" class="status-tab-pill {{ $currentStatusFilter === '1' ? 'active' : '' }}" data-status-tab="1">Pending Realisation</button>
                                <button type="button" class="status-tab-pill {{ $currentStatusFilter === '0' ? 'active' : '' }}" data-status-tab="0">Realised</button>
                                <button type="button" class="status-tab-pill {{ $currentStatusFilter === '2' ? 'active' : '' }}" data-status-tab="2">Dishonoured</button>
                                <button type="button" class="status-tab-pill {{ $currentStatusFilter === 'all' ? 'active' : '' }}" data-status-tab="all">All Records</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 6px;">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Records</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Container --}}
                    <div class="table-scroll-container">
                        {{-- Loading Overlay --}}
                        <div class="ca-table-loading-overlay" id="ca-loading-overlay">
                            <div class="ca-table-loading-spinner">
                                <i class="fa fa-spinner fa-spin"></i> Loading Cheque Data...
                            </div>
                        </div>

                        <table class="dash-table" id="cheque-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers --}}
                                <tr class="header-titles-row">
                                    <th class="text-center col-sr">{{ __('messages.Sr.No.') }}</th>
                                    <th class="col-receipt">Receipt No.</th>
                                    <th class="text-center col-status">Status</th>
                                    <th class="col-adm">Admission No.</th>
                                    <th class="col-class">Class</th>
                                    <th class="col-name">{{ __('Student Name') }}</th>
                                    <th class="col-father">{{ __('messages.Fathers Name') }}</th>
                                    <th class="col-mobile">{{ __('messages.Mobile') }}</th>
                                    <th class="col-cheque">Cheque / Bank Info</th>
                                    <th class="text-center col-date">{{ __('Payment date') }}</th>
                                    <th class="text-center col-mode">{{ __('Payment Modes') }}</th>
                                    <th class="text-right col-amount">{{ __('Amount') }}</th>
                                    <th class="text-center col-action">{{ __('Action') }}</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters --}}
                                <tr class="excel-filter-row">
                                    {{-- 1. Clear Filters --}}
                                    <th class="text-center col-sr">
                                        <button type="button" class="btn-clear-filters" title="Clear all filters">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </th>

                                    {{-- 2. Receipt No Filter --}}
                                    <th class="col-receipt">
                                        <input type="text" class="excel-col-filter" id="filter-invoice-no" placeholder="Receipt..." value="{{ $search['invoice_no'] ?? '' }}">
                                    </th>

                                    {{-- 3. Status Filter --}}
                                    <th class="col-status">
                                        <select class="excel-col-filter" id="filter-status" title="Cheque Status">
                                            <option value="1" {{ $currentStatusFilter === '1' ? 'selected' : '' }}>Pending Realisation</option>
                                            <option value="0" {{ $currentStatusFilter === '0' ? 'selected' : '' }}>Cheque Realised</option>
                                            <option value="2" {{ $currentStatusFilter === '2' ? 'selected' : '' }}>Cheque Dishonoured</option>
                                            <option value="all" {{ $currentStatusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                                        </select>
                                    </th>

                                    {{-- 4. Admission No Filter --}}
                                    <th class="col-adm">
                                        <input type="text" class="excel-col-filter" id="filter-admission-no" placeholder="Adm No..." value="{{ $search['admission_no'] ?? '' }}">
                                    </th>

                                    {{-- 5. Class Filter --}}
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

                                    {{-- 6. Student Name Filter --}}
                                    <th class="col-name">
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Student Name..." value="{{ $search['name'] ?? '' }}">
                                    </th>

                                    {{-- 7. Father Name Filter --}}
                                    <th class="col-father">
                                        <input type="text" class="excel-col-filter" id="filter-father" placeholder="Father Name..." value="{{ $search['father_name'] ?? '' }}">
                                    </th>

                                    {{-- 8. Mobile Filter --}}
                                    <th class="col-mobile">
                                        <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Mobile..." value="{{ $search['mobile'] ?? '' }}">
                                    </th>

                                    {{-- 9. Cheque / Bank Filter --}}
                                    <th class="col-cheque">
                                        <input type="text" class="excel-col-filter" id="filter-cheque" placeholder="Cheque / Bank..." value="{{ $search['cheque_info'] ?? '' }}">
                                    </th>

                                    {{-- 10. Payment Date Placeholder --}}
                                    <th class="text-center text-muted col-date">-</th>

                                    {{-- 11. Payment Mode Filter --}}
                                    <th class="col-mode">
                                        <select class="excel-col-filter" id="filter-mode" title="Payment Mode">
                                            <option value="">All</option>
                                            @if(!empty($paymentModes))
                                                @foreach($paymentModes as $mode)
                                                    <option value="{{ $mode->id }}" {{ ($search['payment_mode_id'] ?? '') == $mode->id ? 'selected' : '' }}>
                                                        {{ $mode->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>

                                    {{-- 12. Amount Placeholder --}}
                                    <th class="text-right text-muted col-amount">-</th>

                                    {{-- 13. Action Reset --}}
                                    <th class="text-center col-action">
                                        <button type="button" class="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            {{-- Dynamic Table Body --}}
                            <tbody id="cheque-table-body">
                                @include('fees.cheque_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. Pinned Summary Totals Bar --}}
                    <div class="ca-pinned-summary-bar">
                        <div class="d-flex align-items-center justify-content-between px-3 py-1 flex-wrap" style="min-height: 32px;">
                            <div class="d-flex align-items-center font-weight-bold text-white" style="font-size: 11px;">
                                <i class="fa fa-calculator text-info mr-2"></i>
                                <span>Summary Totals:</span>
                            </div>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Pending Realisation:</span>
                                    <span class="pill-val text-warning" id="foot-pending-amount">₹{{ number_format($stats['pending_amount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Cheque Realised:</span>
                                    <span class="pill-val text-success" id="foot-realised-amount">₹{{ number_format($stats['realised_amount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Cheque Dishonoured:</span>
                                    <span class="pill-val text-danger" id="foot-dishonoured-amount">₹{{ number_format($stats['dishonoured_amount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill pill-net">
                                    <span class="pill-lbl" style="color: rgba(255,255,255,.9);">Total Amount (Filtered):</span>
                                    <span class="pill-val text-white font-weight-bold" id="foot-total-amount">₹{{ number_format($stats['filtered_amount'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Pinned Bottom Pagination Toolbar --}}
                    <div class="dash-card-footer table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span>
                            to <span id="page-end" class="font-weight-bold text-white">{{ $perPage === 'all' ? $totalCount : min($startIndex + count($data), $totalCount) }}</span>
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
                                <div id="page-numbers-container" class="d-inline-flex align-items-center" style="gap: 3px;"></div>
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

{{-- Slip / Image Preview Modal --}}
<div id="profileImgModal" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title mb-0"><i class="fa fa-picture-o mr-1"></i> Cheque / Payment Slip Preview</h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center bg-light p-2">
                <img id="profileImg" src="" style="max-width: 100%; max-height: 480px; object-fit: contain; border-radius: 2px;">
            </div>
        </div>
    </div>
</div>

{{-- Change Cheque Status Confirmation Modal --}}
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: #ffffff;">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h4 class="modal-title"><i class="fa fa-exchange mr-1"></i> Change Cheque Status Confirmation</h4>
                <button type="button" class="close text-white modal-close-trigger" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="chequeStatusForm" action="{{ url('fees_cheque') }}" method="post">
                @csrf
                <div class="modal-body" style="font-size: 12px;">
                    <input type="hidden" id="status_id" name="status_id">
                    <input type="hidden" id="id" name="id">

                    <div class="p-2 mb-3 rounded" style="background: #f1f5f9; border: 1px solid #cbd5e1;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Receipt No:</span>
                            <strong id="modal-invoice-no" style="color: #002C54;">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted">Student:</span>
                            <strong id="modal-student-name">-</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="text-muted">Amount:</span>
                            <strong class="text-success" id="modal-amount">₹0.00</strong>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-dark mb-2" id="status_message">Are you sure you want to update this cheque status?</h6>

                    <div class="form-group mb-0">
                        <label for="remark" class="font-weight-bold text-secondary mb-1">Clearance / Bank Remark</label>
                        <textarea id="remark" name="remark" class="form-control" rows="3" style="font-size: 12px;" placeholder="Enter bank clearance or dishonour remarks here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm modal-close-trigger" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit-status">
                        <i class="fa fa-check mr-1"></i> Confirm & Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    const ajaxUrl = "{{ url('fees_cheque') }}";
    let currentPage = {{ (int) $currentPage }};
    let lastPage = {{ (int) $lastPage }};
    let currentPerPage = "{{ $perPage }}";
    let filterTimer = null;
    let activeAjax = null;
    let activeSelectElement = null;
    let statusUpdatedSuccessfully = false;

    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function renderPageNumbers() {
        const container = $('#page-numbers-container');
        container.empty();
        if (lastPage <= 1) return;

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(lastPage, startPage + 4);
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        for (let p = startPage; p <= endPage; p++) {
            const btn = $('<button>', {
                type: 'button',
                class: 'page-num-btn' + (p === currentPage ? ' active' : ''),
                text: p,
                'data-page': p
            });
            container.append(btn);
        }
    }

    renderPageNumbers();

    function syncStatusTabs(statusVal) {
        $('.status-tab-pill').removeClass('active');
        $(`.status-tab-pill[data-status-tab="${statusVal}"]`).addClass('active');
        $('.ca-stat-card[data-status-card]').removeClass('active-stat-filter');
        $(`.ca-stat-card[data-status-card="${statusVal}"]`).addClass('active-stat-filter');
    }

    function getFilterParams(targetPage = 1) {
        return {
            ajax: 1,
            page: targetPage,
            per_page: currentPerPage,
            invoice_no: $.trim($('#filter-invoice-no').val()),
            status: $('#filter-status').val(),
            admission_no: $.trim($('#filter-admission-no').val()),
            class_type_id: $('#filter-class').val(),
            name: $.trim($('#filter-name').val()),
            father_name: $.trim($('#filter-father').val()),
            mobile: $.trim($('#filter-mobile').val()),
            cheque_info: $.trim($('#filter-cheque').val()),
            payment_mode_id: $('#filter-mode').val(),
            starting: $('#hero-starting').val(),
            ending: $('#hero-ending').val()
        };
    }

    function fetchData(targetPage = 1) {
        if (activeAjax && activeAjax.readyState !== 4) {
            activeAjax.abort();
        }

        $('#ca-loading-overlay').css('display', 'flex');
        const params = getFilterParams(targetPage);
        syncStatusTabs(params.status);

        activeAjax = $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json',
            success: function(res) {
                if (res && res.status) {
                    $('#cheque-table-body').html(res.html);

                    currentPage = parseInt(res.current_page) || 1;
                    lastPage = parseInt(res.last_page) || 1;

                    $('#page-start').text(res.from || 0);
                    $('#page-end').text(res.to || 0);
                    $('#total-records').text(res.total || 0);
                    $('#header-records-count').text(res.total || 0);
                    $('#current-page').text(currentPage);
                    $('#total-pages').text(lastPage);

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage);
                    renderPageNumbers();

                    if (res.stats) {
                        const s = res.stats;
                        $('#stat-total-count').text(parseInt(s.total_count || 0).toLocaleString('en-IN'));
                        $('#stat-filtered-amount').text(formatMoney(s.filtered_amount));
                        $('#stat-pending-count').text(parseInt(s.pending_count || 0).toLocaleString('en-IN'));
                        $('#stat-pending-amount').text(formatMoney(s.pending_amount));
                        $('#stat-realised-count').text(parseInt(s.realised_count || 0).toLocaleString('en-IN'));
                        $('#stat-realised-amount').text(formatMoney(s.realised_amount));
                        $('#stat-dishonoured-count').text(parseInt(s.dishonoured_count || 0).toLocaleString('en-IN'));
                        $('#stat-dishonoured-amount').text(formatMoney(s.dishonoured_amount));

                        $('#foot-pending-amount').text('₹' + formatMoney(s.pending_amount));
                        $('#foot-realised-amount').text('₹' + formatMoney(s.realised_amount));
                        $('#foot-dishonoured-amount').text('₹' + formatMoney(s.dishonoured_amount));
                        $('#foot-total-amount').text('₹' + formatMoney(s.filtered_amount));
                    }
                }
            },
            error: function(xhr, status, error) {
                if (status !== 'abort') {
                    console.error('Cheque AJAX Error:', error);
                }
            },
            complete: function() {
                $('#ca-loading-overlay').hide();
            }
        });
    }

    function queueFilter() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            fetchData(1);
        }, 300);
    }

    $('input.excel-col-filter').on('input', function() {
        queueFilter();
    });

    $('select.excel-col-filter').on('change', function() {
        fetchData(1);
    });

    $('.status-tab-pill').on('click', function() {
        const statusVal = String($(this).data('status-tab'));
        $('#filter-status').val(statusVal);
        fetchData(1);
    });

    $('.ca-stat-card[data-status-card]').on('click', function() {
        const statusVal = String($(this).data('status-card'));
        $('#filter-status').val(statusVal);
        fetchData(1);
    });

    $('#hero-starting, #hero-ending').on('change', function() {
        $('.dash-chip-btn').removeClass('active');
        fetchData(1);
    });

    $('#rows-per-page-select').on('change', function() {
        currentPerPage = $(this).val();
        fetchData(1);
    });

    $('#btn-first').on('click', function() { if (currentPage > 1) fetchData(1); });
    $('#btn-prev').on('click', function() { if (currentPage > 1) fetchData(currentPage - 1); });
    $('#btn-next').on('click', function() { if (currentPage < lastPage) fetchData(currentPage + 1); });
    $('#btn-last').on('click', function() { if (currentPage < lastPage) fetchData(lastPage); });
    $(document).on('click', '.page-num-btn', function() {
        const target = parseInt($(this).data('page'));
        if (target && target !== currentPage) {
            fetchData(target);
        }
    });

    $('.btn-clear-filters, .btn-reset-filters, #btn-reset-hero').on('click', function() {
        $('input.excel-col-filter').val('');
        $('#filter-class, #filter-mode').val('');
        $('#filter-status').val('1');
        $('#hero-starting, #hero-ending').val('');
        $('.dash-chip-btn').removeClass('active');
        fetchData(1);
    });

    function formatDate(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    $('.dash-chip-btn').on('click', function() {
        const range = $(this).data('range');
        $('.dash-chip-btn').removeClass('active');
        $(this).addClass('active');

        const now = new Date();
        let start = new Date();
        let end = new Date();

        if (range === 'today') {
            // today
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

    // Slip Preview Modal (Event Delegation)
    $(document).on('click', '.profileImg', function() {
        const profileImgUrl = $(this).data('img');
        if (profileImgUrl) {
            $('#profileImg').attr('src', profileImgUrl);
            $('#profileImgModal').modal('show');
        }
    });

    // Status Dropdown Change (Event Delegation for AJAX rows)
    $(document).on('change', '.statusDrop', function() {
        activeSelectElement = $(this);
        statusUpdatedSuccessfully = false;
        const status = String($(this).val());
        const id = $(this).data('id');
        const invoiceNo = $(this).data('invoice') || '-';
        const studentName = $(this).data('student') || '-';
        const amount = $(this).data('amount') || '0.00';

        $('#status_id').val(status);
        $('#id').val(id);
        $('#modal-invoice-no').text(invoiceNo);
        $('#modal-student-name').text(studentName);
        $('#modal-amount').text('₹' + amount);

        if (status === '2') {
            $('#status_message').html('<span class="text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Mark this cheque as <strong>Dishonoured (Bounced)</strong>?</span>');
            $('#remark').val('This cheque has been dishonoured by the bank.');
        } else if (status === '0') {
            $('#status_message').html('<span class="text-success"><i class="fa fa-check-circle mr-1"></i> Mark this cheque as <strong>Realised (Cleared)</strong>?</span>');
            $('#remark').val('Cheque has been realised.');
        } else {
            $('#status_message').html('<span class="text-warning"><i class="fa fa-clock-o mr-1"></i> Mark this cheque as <strong>Pending Realisation</strong>?</span>');
            $('#remark').val('This cheque is pending realisation.');
        }

        $('#statusModal').modal('show');
    });

    // Restore original select value if modal is closed without saving
    $('#statusModal').on('hidden.bs.modal', function() {
        if (!statusUpdatedSuccessfully && activeSelectElement) {
            activeSelectElement.val(String(activeSelectElement.data('current')));
        }
        activeSelectElement = null;
    });

    $(document).on('click', '.modal-close-trigger', function() {
        $('#statusModal').modal('hide');
    });

    // Real-time AJAX Status Update Submission
    $('#chequeStatusForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#btn-submit-status');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: $(this).serialize() + '&ajax=1',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json',
            success: function(res) {
                if (res && res.status) {
                    statusUpdatedSuccessfully = true;
                    $('#statusModal').modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || 'Cheque status updated successfully!');
                    }
                    fetchData(currentPage);
                } else {
                    alert(res.message || 'Could not update cheque status.');
                }
            },
            error: function() {
                alert('An error occurred while updating cheque status.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Confirm & Update');
            }
        });
    });
});
</script>
@endsection

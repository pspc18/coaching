@php
    $classType = $classType ?? Helper::classType();
    $allUsers = $allUsers ?? Helper::getAllUsers();
    $paymentModes = $paymentModes ?? Helper::getPaymentMode();
    $search = $search ?? [];
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $lastPage = $lastPage ?? 1;
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $stats = $stats ?? [
        'total_count'    => $totalCount,
        'total_amount'   => 0,
        'total_discount' => 0,
        'total_fine'     => 0,
        'net_collected'  => 0,
    ];
@endphp
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
    height: calc(100vh - var(--header-height, 62px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 6px;
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
    gap: 5px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1.3;
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
.dash-chip-btn {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: rgba(255,255,255,.1);
    color: #fff;
    cursor: pointer;
    transition: all .15s;
}
.dash-chip-btn:hover {
    background: rgba(255,255,255,.22);
    border-color: rgba(255,255,255,.5);
    color: #fff;
}
.dash-chip-btn.active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}

/* Summary Statistics Strip (5 Cards) */
.ca-stats-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 6px;
    margin-bottom: 6px;
    flex-shrink: 0;
}
.ca-stat-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.ca-stat-icon {
    width: 32px;
    height: 32px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.ca-stat-content {
    flex: 1;
    min-width: 0;
}
.ca-stat-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #64748b;
    font-weight: 600;
    line-height: 1.1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ca-stat-value {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stat-color-receipts .ca-stat-icon { background: #eff6ff; color: #0284c7; }
.stat-color-amount .ca-stat-icon { background: #ecfdf5; color: #059669; }
.stat-color-discount .ca-stat-icon { background: #fffbeb; color: #d97706; }
.stat-color-fine .ca-stat-icon { background: #fef2f2; color: #dc2626; }
.stat-color-net .ca-stat-icon { background: #f0fdf4; color: #16a34a; }

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
    padding: 6px 12px;
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
    width: 100%;
}

/* Table Design - Exact Sequential Columns */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Dual-Row thead Titles & Filters */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 6px 6px;
    height: 32px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
    box-sizing: border-box;
}
.excel-filter-row th {
    position: sticky;
    top: 32px;
    background: #08335c;
    color: #ffffff;
    padding: 3px 4px;
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
.dash-table tbody tr:nth-child(odd) .fixed_action_col { background: #f8fafc !important; }
.dash-table tbody tr:nth-child(even) .fixed_action_col { background: #edf2f7 !important; }
.dash-table tbody tr:hover .fixed_action_col { background: #e2e8f0 !important; }

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 22px;
    font-size: 10px;
    padding: 1px 4px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background-color: #051e38;
    color: #ffffff;
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

/* Date Range Selection Box in thead */
.excel-date-range-box {
    display: flex;
    flex-direction: column;
    gap: 2px;
    width: 100%;
    min-width: 120px;
}
.excel-date-wrap {
    display: flex;
    align-items: center;
    gap: 3px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 3px;
    height: 20px;
    transition: all .15s;
}
.excel-date-wrap:focus-within {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
    background: #031426 !important;
}
.excel-date-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: rgba(255,255,255,.75);
    text-transform: uppercase;
    min-width: 22px;
    user-select: none;
}
.excel-date-input {
    flex: 1;
    min-width: 0;
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 9.5px;
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
    height: 22px;
    padding: 0 5px;
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
    padding: 4px 6px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11px;
    white-space: nowrap;
}
.dash-table tbody tr:nth-child(odd) td { background: #f8fafc; }
.dash-table tbody tr:nth-child(even) td { background: #edf2f7; }
.dash-table tbody tr:hover td { background: #e2e8f0 !important; }

/* Custom Row Badges & Links */
.ca-invoice-link {
    background: transparent;
    border: none;
    padding: 0;
    color: #0284c7;
    font-weight: 700;
    cursor: pointer;
    text-decoration: underline;
    transition: color .15s;
    font-size: 11px;
}
.ca-invoice-link:hover {
    color: #0369a1;
}
.badge-offline-slip {
    font-size: 10px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 1px 4px;
    border-radius: 2px;
    font-weight: 600;
}
.ca-admission-link {
    color: #0284c7;
    font-weight: 600;
    text-decoration: none;
}
.ca-admission-link:hover {
    text-decoration: underline;
}
.ca-student-name {
    color: #0f172a;
    font-weight: 600;
    text-decoration: none;
}
.ca-student-name:hover {
    color: #0284c7;
    text-decoration: underline;
}
.badge-class-name {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
}
.ca-collector-name {
    font-size: 10.5px;
    color: #334155;
    font-weight: 500;
}
.badge-payment-mode {
    font-size: 9.5px;
    padding: 1px 5px;
    border-radius: 2px;
    font-weight: 600;
    display: inline-block;
}
.badge-mode-cash { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.badge-mode-online { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.badge-mode-cheque { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.badge-mode-bank { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }

.badge-status-received {
    font-size: 9.5px;
    background: #ecfdf5;
    color: #047857;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #a7f3d0;
    font-weight: 600;
    display: inline-block;
}
.badge-status-pending {
    font-size: 9.5px;
    background: #fffbeb;
    color: #b45309;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #fde68a;
    font-weight: 600;
    display: inline-block;
}
.badge-status-cancelled {
    font-size: 9.5px;
    background: #fef2f2;
    color: #b91c1c;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #fecaca;
    font-weight: 600;
    display: inline-block;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
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
.ca-action-btn.btn-print { color: #0284c7; }
.ca-action-btn.btn-print:hover { background: #0284c7; color: #fff; border-color: #0284c7; }
.ca-action-btn.btn-view { color: #475569; }
.ca-action-btn.btn-view:hover { background: #475569; color: #fff; border-color: #475569; }

/* Pinned Summary Totals Bar (Directly Above Table Pagination Bar) */
.ca-pinned-summary-bar {
    background: #002C54;
    border-top: 1px solid rgba(255,255,255,.14);
    border-bottom: 1px solid #001f3d;
    flex-shrink: 0;
    z-index: 10;
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
    color: rgba(255,255,255,.65);
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
    padding: 50px 16px;
    text-align: center;
    color: #64748b;
    background: #eef2f6;
    width: 100%;
}
.dash-empty-icon {
    font-size: 40px;
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
    padding: 10px 18px;
    border-radius: 2px;
    font-weight: 600;
    font-size: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Print Media Optimization */
@media print {
    .main-sidebar, .main-header, .admission-hero-actions, .excel-filter-row, .table-pagination-bar, .ca-action-btn, .content-header {
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
                        <span class="admission-kicker"><i class="fa fa-calculator mr-1"></i> Financial Accounting & Audit</span>
                        <h1 class="admission-title">{{ __('CA & Fee Collection Report') }}</h1>
                        <p class="admission-subtitle">Real-time audited invoice register, collection tracking & fee summary (Strict d-m-Y format)</p>
                    </div>
                    <div class="admission-hero-actions">
                        {{-- Quick Date Range Chips --}}
                        <button type="button" class="dash-chip-btn" data-range="today">Today</button>
                        <button type="button" class="dash-chip-btn" data-range="yesterday">Yesterday</button>
                        <button type="button" class="dash-chip-btn" data-range="this_month">This Month</button>
                        <button type="button" class="dash-chip-btn" data-range="last_month">Last Month</button>
                        <button type="button" class="dash-chip-btn" data-range="this_fy">This FY</button>

                        <button type="button" class="dash-btn dash-btn-outline ml-2" id="btn-print-report" title="Print Current View">
                            <i class="fa fa-print mr-1"></i> Print
                        </button>
                        <button type="button" class="dash-btn dash-btn-outline" id="btn-reset-hero" title="Reset All Filters">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </button>
                        <a href="{{ url('fee_dashboard') }}" class="dash-btn dash-btn-light" title="Back to Fee Dashboard">
                            <i class="fa fa-arrow-left mr-1"></i> {{ __('messages.Back') }}
                        </a>
                    </div>
                </div>

                {{-- 2. Summary Statistics Row (5 KPI Cards) --}}
                <div class="ca-stats-strip">
                    <div class="ca-stat-card stat-color-receipts">
                        <div class="ca-stat-icon"><i class="fa fa-file-text-o"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Receipts</div>
                            <div class="ca-stat-value" id="stat-total-count">{{ number_format($stats['total_count'] ?? $totalCount) }}</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-amount">
                        <div class="ca-stat-icon"><i class="fa fa-inr"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Fee Amount</div>
                            <div class="ca-stat-value">₹<span id="stat-total-amount">{{ number_format($stats['total_amount'] ?? 0, 2) }}</span></div>
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
                    <div class="ca-stat-card stat-color-net">
                        <div class="ca-stat-icon"><i class="fa fa-check-square-o"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Net Collections</div>
                            <div class="ca-stat-value">₹<span id="stat-net-collected">{{ number_format($stats['net_collected'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- 3. Main Dark Navy Table Card with Dual-Row Sticky Header --}}
                <div class="dash-card admission-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Fee Invoices Register & Audit View</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Total Receipts</span>
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

                        <table class="dash-table" id="ca-grid-table">
                            <thead>
                                {{-- Row 1: Column Headers (15 Sequential Clean Columns) --}}
                                <tr class="header-titles-row">
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th style="min-width: 90px;">Receipt No</th>
                                    <th style="min-width: 85px;">Offline Slip</th>
                                    <th style="min-width: 85px;" class="text-center">Payment Date</th>
                                    <th style="min-width: 75px;">Adm No</th>
                                    <th style="min-width: 70px;">Class</th>
                                    <th style="min-width: 120px;">Student Name</th>
                                    <th style="min-width: 110px;">Father Name</th>
                                    <th style="min-width: 90px;">Collect By</th>
                                    <th style="min-width: 70px;" class="text-center">Mode</th>
                                    <th style="min-width: 65px;" class="text-right">Discount</th>
                                    <th style="min-width: 60px;" class="text-right">Fine</th>
                                    <th style="min-width: 80px;" class="text-right">Amount</th>
                                    <th style="min-width: 75px;" class="text-center">Status</th>
                                    <th style="width: 60px;" class="text-center fixed_action_head">{{ __('common.Action') }}</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (15 Columns in Exact Matching Sequence) --}}
                                <tr class="excel-filter-row">
                                    {{-- 1. Clear Filters --}}
                                    <th class="text-center">
                                        <button type="button" class="btn-clear-filters" title="Clear all in-column filters">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </th>

                                    {{-- 2. Receipt No Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-invoice-no" placeholder="Receipt..." value="{{ $search['invoice_no'] ?? '' }}">
                                    </th>

                                    {{-- 3. Offline Slip Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-offline-receipt-no" placeholder="Slip..." value="{{ $search['offline_receipt_no'] ?? '' }}">
                                    </th>

                                    {{-- 4. Payment Date Range Filter --}}
                                    <th>
                                        <div class="excel-date-range-box">
                                            <div class="excel-date-wrap">
                                                <span class="excel-date-lbl">From</span>
                                                <input type="date" class="excel-date-input" id="filter-from-date" value="{{ $search['starting'] ?? '' }}" title="From Date (d-m-Y)">
                                            </div>
                                            <div class="excel-date-wrap">
                                                <span class="excel-date-lbl">To</span>
                                                <input type="date" class="excel-date-input" id="filter-to-date" value="{{ $search['ending'] ?? '' }}" title="To Date (d-m-Y)">
                                            </div>
                                        </div>
                                    </th>

                                    {{-- 5. Admission No Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-admission-no" placeholder="Adm No..." value="{{ $search['admission_no'] ?? '' }}">
                                    </th>

                                    {{-- 6. Class Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-class" title="Class">
                                            <option value="">All</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $class)
                                                    <option value="{{ $class->id }}" {{ ($search['class_type_id'] ?? '') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>

                                    {{-- 7. Student Name Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Student..." value="{{ $search['name'] ?? '' }}">
                                    </th>

                                    {{-- 8. Father Name Filter --}}
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-father" placeholder="Father..." value="{{ $search['father_name'] ?? '' }}">
                                    </th>

                                    {{-- 9. Collect By Filter --}}
                                    <th>
                                        @if(Session::get('role_id') == 1)
                                            <select class="excel-col-filter" id="filter-user" title="Collected By User">
                                                <option value="">All</option>
                                                @if(!empty($allUsers))
                                                    @foreach($allUsers as $user)
                                                        <option value="{{ $user->id }}" {{ ($search['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                                            {{ $user->first_name }} {{ $user->last_name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @else
                                            <input type="text" class="excel-col-filter" disabled value="Self" style="opacity: 0.7;">
                                        @endif
                                    </th>

                                    {{-- 10. Payment Mode Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-payment-mode" title="Payment Mode">
                                            <option value="">All</option>
                                            @if(!empty($paymentModes))
                                                @foreach($paymentModes as $pm)
                                                    <option value="{{ $pm->id }}" {{ ($search['payment_mode_id'] ?? '') == $pm->id ? 'selected' : '' }}>
                                                        {{ $pm->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </th>

                                    {{-- 11. Discount Placeholder --}}
                                    <th class="text-right text-muted">-</th>

                                    {{-- 12. Fine Placeholder --}}
                                    <th class="text-right text-muted">-</th>

                                    {{-- 13. Amount Placeholder --}}
                                    <th class="text-right text-muted">-</th>

                                    {{-- 14. Status Filter --}}
                                    <th>
                                        <select class="excel-col-filter" id="filter-status" title="Payment Status">
                                            <option value="">All</option>
                                            <option value="0" {{ ($search['status'] ?? '') === '0' ? 'selected' : '' }}>Received</option>
                                            <option value="1" {{ ($search['status'] ?? '') === '1' ? 'selected' : '' }}>Pending</option>
                                            <option value="2" {{ ($search['status'] ?? '') === '2' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </th>

                                    {{-- 15. Action Reset --}}
                                    <th class="fixed_action_filter text-center">
                                        <button type="button" class="btn-reset-filters" title="Reset all filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            {{-- Dynamic Table Body --}}
                            <tbody id="ca-report-table-body">
                                @include('fees.reports.ca_report_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- Fixed Summary Totals Bar (Directly Above Table Pagination Footer) --}}
                    <div class="ca-pinned-summary-bar">
                        <div class="d-flex align-items-center justify-content-between px-3 py-1 flex-wrap" style="min-height: 34px;">
                            <div class="d-flex align-items-center font-weight-bold text-white" style="font-size: 11.5px;">
                                <i class="fa fa-calculator text-info mr-2"></i>
                                <span>Summary Totals (Filtered Data):</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Discount:</span>
                                    <span class="pill-val text-warning" id="foot-total-discount">₹{{ number_format($stats['total_discount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Fine:</span>
                                    <span class="pill-val text-danger" id="foot-total-fine">₹{{ number_format($stats['total_fine'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill">
                                    <span class="pill-lbl">Fee Amount:</span>
                                    <span class="pill-val font-weight-bold" style="color: #34d399;" id="foot-total-amount">₹{{ number_format($stats['total_amount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="ca-summary-pill pill-net">
                                    <span class="pill-lbl" style="color: rgba(255,255,255,.85);">Net Collection:</span>
                                    <span class="pill-val text-white font-weight-bold" id="foot-net-badge">₹{{ number_format($stats['net_collected'] ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Pinned Bottom Pagination Toolbar (Arise ERP Theme) --}}
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

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    const ajaxUrl = "{{ url('ca_report') }}";
    let currentPage = {{ $currentPage }};
    let lastPage = {{ $lastPage }};
    let currentPerPage = "{{ $perPage }}";
    let filterTimer = null;
    let activeAjax = null;

    // Helper: format currency with 2 decimals
    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Helper: build query params
    function getFilterParams(page) {
        return {
            ajax: 1,
            page: page || 1,
            per_page: currentPerPage,
            invoice_no: $.trim($('#filter-invoice-no').val()),
            offline_receipt_no: $.trim($('#filter-offline-receipt-no').val()),
            admission_no: $.trim($('#filter-admission-no').val()),
            name: $.trim($('#filter-name').val()),
            father_name: $.trim($('#filter-father').val()),
            class_type_id: $('#filter-class').val(),
            user_id: $('#filter-user').length ? $('#filter-user').val() : '',
            payment_mode_id: $('#filter-payment-mode').val(),
            status: $('#filter-status').val(),
            starting: $('#filter-from-date').val(),
            ending: $('#filter-to-date').val()
        };
    }

    // Main AJAX Fetcher
    function fetchCAReport(page) {
        if (activeAjax && activeAjax.readyState !== 4) {
            activeAjax.abort();
        }

        $('#ca-loading-overlay').css('display', 'flex');
        const params = getFilterParams(page);

        activeAjax = $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && res.status) {
                    $('#ca-report-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    // Update Pagination UI
                    $('#page-start').text(res.from);
                    $('#page-end').text(res.to);
                    $('#total-records').text(res.total);
                    $('#header-records-count').text(res.total);
                    $('#current-page').text(res.current_page);
                    $('#total-pages').text(res.last_page);

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);

                    // Update KPI Summary Stats
                    if (res.stats) {
                        $('#stat-total-count').text(parseInt(res.stats.total_count || 0).toLocaleString('en-IN'));
                        $('#stat-total-amount').text(formatMoney(res.stats.total_amount));
                        $('#stat-total-discount').text(formatMoney(res.stats.total_discount));
                        $('#stat-total-fine').text(formatMoney(res.stats.total_fine));
                        $('#stat-net-collected').text(formatMoney(res.stats.net_collected));

                        // Pinned Summary Totals Bar
                        $('#foot-total-amount').text('₹' + formatMoney(res.stats.total_amount));
                        $('#foot-total-discount').text('₹' + formatMoney(res.stats.total_discount));
                        $('#foot-total-fine').text('₹' + formatMoney(res.stats.total_fine));
                        $('#foot-net-badge').text('₹' + formatMoney(res.stats.net_collected));
                    }
                }
            },
            error: function(xhr, status) {
                if (status !== 'abort') {
                    console.error('Error fetching CA report:', xhr);
                }
            },
            complete: function() {
                $('#ca-loading-overlay').hide();
            }
        });
    }

    // Debounced real-time text filter typing (300ms)
    $('#filter-invoice-no, #filter-offline-receipt-no, #filter-admission-no, #filter-name, #filter-father').on('input', function() {
        clearTimeout(filterTimer);
        $('.dash-chip-btn').removeClass('active');
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchCAReport(1);
        }, 300);
    });

    // Select dropdown & date inputs change instantly
    $('#filter-class, #filter-user, #filter-payment-mode, #filter-status, #filter-from-date, #filter-to-date').on('change', function() {
        $('.dash-chip-btn').removeClass('active');
        currentPage = 1;
        fetchCAReport(1);
    });

    // Rows per page dropdown
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchCAReport(1);
    });

    // Pagination navigation buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            fetchCAReport(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            fetchCAReport(currentPage - 1);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            fetchCAReport(currentPage + 1);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            fetchCAReport(lastPage);
        }
    });

    // Reset Filters
    function resetAllFilters() {
        $('#filter-invoice-no, #filter-offline-receipt-no, #filter-admission-no, #filter-name, #filter-father').val('');
        $('#filter-class, #filter-user, #filter-payment-mode, #filter-status, #filter-from-date, #filter-to-date').val('');
        $('.dash-chip-btn').removeClass('active');
        currentPage = 1;
        fetchCAReport(1);
    }

    $('.btn-clear-filters, .btn-reset-filters, #btn-reset-hero').on('click', resetAllFilters);

    // Quick Date Range Chips Logic (strictly generates YYYY-MM-DD for inputs)
    function formatDateYMD(d) {
        let year = d.getFullYear();
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    $('.dash-chip-btn').on('click', function() {
        const range = $(this).data('range');
        $('.dash-chip-btn').removeClass('active');
        $(this).addClass('active');

        const now = new Date();
        let fromDate = '';
        let toDate = '';

        if (range === 'today') {
            fromDate = formatDateYMD(now);
            toDate = formatDateYMD(now);
        } else if (range === 'yesterday') {
            const yest = new Date();
            yest.setDate(yest.getDate() - 1);
            fromDate = formatDateYMD(yest);
            toDate = formatDateYMD(yest);
        } else if (range === 'this_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            fromDate = formatDateYMD(firstDay);
            toDate = formatDateYMD(lastDay);
        } else if (range === 'last_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
            fromDate = formatDateYMD(firstDay);
            toDate = formatDateYMD(lastDay);
        } else if (range === 'this_fy') {
            const currentYear = now.getFullYear();
            const currentMonth = now.getMonth() + 1; // 1-12
            let fyStartYear = (currentMonth >= 4) ? currentYear : currentYear - 1;
            let fyEndYear = fyStartYear + 1;
            fromDate = `${fyStartYear}-04-01`;
            toDate = `${fyEndYear}-03-31`;
        }

        $('#filter-from-date').val(fromDate);
        $('#filter-to-date').val(toDate);

        currentPage = 1;
        fetchCAReport(1);
    });

    // Window Print Handler
    $('#btn-print-report').on('click', function() {
        window.print();
    });
});
</script>
@endsection
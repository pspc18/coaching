@extends('layout.app')

@php
    $expensePermission = Helper::permissioncheck(16);
    $canAdd = $expensePermission->add ?? true;
    $canEdit = $expensePermission->edit ?? true;
    $canDelete = $expensePermission->delete ?? true;

    $totalAmt = $stats['total_amount'] ?? 0;
    $totalCount = $stats['total_count'] ?? ($totalCount ?? count($data ?? []));
    $paidAmt = $stats['paid_amount'] ?? 0;
    $paidCount = $stats['paid_count'] ?? 0;
    $pendingAmt = $stats['pending_amount'] ?? 0;
    $pendingCount = $stats['pending_count'] ?? 0;
    $recurringCount = $stats['recurring_count'] ?? 0;
    $recurringAmt = $stats['recurring_amount'] ?? 0;
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EXPENSE REGISTER & VOUCHER DIRECTORY
   Exact Match with userView & studentList Grid Architecture:
   - Fixed Viewport Height Layout (calc(100vh - var(--header-height) - 16px))
   - Signature Dark Navy Theme (#002C54 to #0f3460)
   - Dual-row sticky thead (#002C54 title row + #08335c Excel filter row)
   - In-Column Excel Filters with #051e38 inputs & #38bdf8 active focus
   - Sticky Fixed Action Column (right: 0)
   - Alternating Soft-Slate Body Rows (#f8fafc / #edf2f7)
   - Pinned Bottom Pagination Toolbar (#002C54)
   - High performance AJAX Real-time Engine
   ========================================================================== */

.expense-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.expense-page * {
    box-sizing: border-box;
}
.expense-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Top Hero Header */
.expense-hero {
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
.expense-hero-text {
    display: flex;
    flex-direction: column;
}
.expense-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.expense-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.expense-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges in Hero */
.expense-hero-stats {
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
.hero-stat-badge.badge-blue {
    background: rgba(14, 165, 233, 0.2);
    border-color: rgba(14, 165, 233, 0.4);
    color: #bae6fd;
}

/* Hero Action Buttons */
.expense-hero-actions {
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

/* 2. Table Card & Header - Dark Navy Unified */
.expense-table-card {
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

/* Proper thead Titles Row (#002C54) */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 8px;
    height: 36px;
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

/* Sticky Filter Row (#08335c) */
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

/* In-Column Excel Filters (Dark Navy Palette) */
.excel-col-filter {
    width: 100%;
    height: 26px;
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

/* Reset / Clear Button */
.btn-reset-filters {
    height: 26px;
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

/* Cell Components */
.voucher-badge {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    background: #e0f2fe;
    padding: 1px 6px;
    border-radius: 2px;
    display: inline-block;
    border: 1px solid #bae6fd;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}
.date-meta {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
}
.category-pill {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    margin-bottom: 2px;
}
.particular-title {
    font-weight: 650;
    color: #0f172a;
    display: block;
    line-height: 1.25;
}
.recurring-tag {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 9.5px;
    color: #0284c7;
    background: #e0f2fe;
    padding: 1px 4px;
    border-radius: 2px;
    margin-top: 2px;
    border: 1px solid #bae6fd;
}
.notes-meta {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
}
.payee-text {
    font-weight: 600;
    color: #0f172a;
}
.bill-tag {
    font-size: 10px;
    color: #64748b;
    display: block;
}
.pm-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    color: #0369a1;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    padding: 1px 5px;
    border-radius: 2px;
}
.ref-text {
    font-size: 9.5px;
    color: #64748b;
    font-family: monospace;
    display: block;
    margin-top: 1px;
}
.amount-text {
    font-weight: 800;
    font-size: 12px;
    color: #0f172a;
    white-space: nowrap;
}
.rate-qty-text {
    font-size: 9.5px;
    color: #64748b;
    white-space: nowrap;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.status-paid {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.status-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
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
.btn-action-print:hover {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}
.btn-action-edit:hover {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
}
.btn-action-delete:hover {
    background: #dc2626;
    border-color: #dc2626;
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
    min-height: calc(100vh - var(--header-height) - 220px);
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

/* 3. Pinned Bottom Pagination Toolbar */
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
.expense-table-loading {
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
    .expense-hero {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper expense-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="expense-page-layout">
                
                {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
                <div class="expense-hero">
                    <div class="expense-hero-text">
                        <span class="expense-kicker"><i class="fa fa-money mr-1"></i> Financial Outflows & Accounts</span>
                        <h1 class="expense-title"><i class="fa fa-calculator mr-1"></i> Expense Register & Vouchers</h1>
                        <p class="expense-subtitle">Real-time expense ledger with in-column Excel filters, instant voucher search & audit controls</p>
                    </div>

                    {{-- Quick Summary Stats --}}
                    <div class="expense-hero-stats">
                        <span class="hero-stat-badge" title="Total Filtered Expenses">
                            <i class="fa fa-inr text-info"></i> Total: <b id="stat-total">₹{{ number_format($totalAmt, 2) }}</b>
                            <small id="stat-count">({{ $totalCount }})</small>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Settled / Paid Expenses">
                            <i class="fa fa-check-circle"></i> Paid: <b id="stat-paid">₹{{ number_format($paidAmt, 2) }}</b>
                            <small id="stat-paid-count">({{ $paidCount }})</small>
                        </span>
                        <span class="hero-stat-badge badge-amber" title="Pending / Due Expenses">
                            <i class="fa fa-clock-o"></i> Due: <b id="stat-due">₹{{ number_format($pendingAmt, 2) }}</b>
                            <small id="stat-due-count">({{ $pendingCount }})</small>
                        </span>
                        @if($recurringCount > 0)
                            <span class="hero-stat-badge badge-blue" title="Recurring Overheads">
                                <i class="fa fa-repeat"></i> Recurring: <b id="stat-recurring">₹{{ number_format($recurringAmt, 2) }}</b>
                                <small id="stat-recurring-count">({{ $recurringCount }})</small>
                            </span>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="expense-hero-actions">
                        @if($canAdd)
                            <a href="{{ url('expenseAdd') }}" class="dash-btn dash-btn-light" title="Add New Expense">
                                <i class="fa fa-plus mr-1"></i> New Expense Voucher
                            </a>
                        @endif
                    </div>
                </div>

                {{-- 2. Full-Height Table Card --}}
                <div class="expense-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-file-text-o text-info mr-1"></i> Expense Register Grid</h3>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Vouchers</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area with Dual Header --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="expense-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">#</th>
                                    <th style="min-width: 140px;">Voucher No. &amp; Date</th>
                                    <th style="min-width: 220px;">Category &amp; Particular</th>
                                    <th style="min-width: 160px;">Paid To / Vendor</th>
                                    <th style="min-width: 140px;">Payment Mode</th>
                                    <th style="min-width: 110px;" class="text-right">Rate × Qty</th>
                                    <th style="min-width: 110px;" class="text-right">Amount (₹)</th>
                                    <th style="width: 95px;" class="text-center">Status</th>
                                    <th style="width: 70px;" class="text-center">Receipt</th>
                                    <th style="width: 95px;" class="text-center fixed_action_head">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (Exact #08335c match) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <button type="button" class="btn-reset-filters" id="btn-clear-filters-icon" title="Reset All Filters" style="padding: 0 4px; height: 25px; width: 25px; justify-content: center;">
                                            <i class="fa fa-filter text-danger"></i>
                                        </button>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-voucher" placeholder="Search voucher..." value="{{ $search['voucher'] ?? '' }}">
                                    </th>
                                    <th>
                                        <div class="d-flex gap-1">
                                            <select class="excel-col-filter" id="filter-category" title="Filter Category" style="max-width: 130px;">
                                                <option value="">All Categories</option>
                                                @foreach($categories as $id => $label)
                                                    <option value="{{ $id }}" {{ ($search['category'] ?? '') == $id ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="excel-col-filter" id="filter-name" placeholder="Search item..." value="{{ $search['name'] ?? '' }}">
                                        </div>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-payee" placeholder="Search vendor / bill..." value="{{ $search['payee'] ?? '' }}">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-pm" title="Payment Mode">
                                            <option value="">All Modes</option>
                                            @foreach($paymentModes as $pm)
                                                <option value="{{ $pm->id }}" {{ ($search['payment_mode_id'] ?? '') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-rate-qty" placeholder="Qty / rate...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-amount" placeholder="Amount...">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-status">
                                            <option value="" {{ ($search['payment_status'] ?? '') === '' ? 'selected' : '' }}>All</option>
                                            <option value="paid" {{ ($search['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="pending" {{ ($search['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-receipt">
                                            <option value="" {{ ($search['receipt'] ?? '') === '' ? 'selected' : '' }}>All</option>
                                            <option value="with" {{ ($search['receipt'] ?? '') === 'with' ? 'selected' : '' }}>With</option>
                                            <option value="without" {{ ($search['receipt'] ?? '') === 'without' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="expense-table-body">
                                @include('expense.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries | Filtered Total: <b id="footer-total-amt" class="text-warning">₹{{ number_format($totalAmt, 2) }}</b>
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

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none;">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14"><i class="fa fa-trash mr-1"></i> Delete Expense Entry</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('expenseDelete') }}" method="post">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body p-3">
                    <p class="font-size-13 text-dark mb-2">Are you sure you want to delete this expense record?</p>
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 10px; border-radius: 2px; font-size: 12px;">
                        <div><strong>Voucher:</strong> <span id="del_modal_invoice">-</span></div>
                        <div><strong>Particular:</strong> <span id="del_modal_name">-</span></div>
                        <div><strong>Amount:</strong> <span id="del_modal_amount" style="color: #dc2626; font-weight: 700;">-</span></div>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Delete Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Client-Side Real-Time AJAX Engine (Identical to userView pattern) --}}
<script>
$(document).ready(function() {
    let currentPage = {{ $currentPage ?? 1 }};
    let lastPage = {{ $lastPage ?? 1 }};
    let currentPerPage = "{{ $perPage ?? 25 }}";
    let isFetching = false;
    let filterTimer = null;
    const viewExpenseAjaxUrl = "{{ url('expenseView') }}";

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        return {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            voucher: ($('#filter-voucher').val() || '').trim(),
            category: $('#filter-category').val() || '',
            name: ($('#filter-name').val() || '').trim(),
            payee: ($('#filter-payee').val() || '').trim(),
            payment_mode_id: $('#filter-pm').val() || '',
            payment_status: $('#filter-status').val() !== undefined ? $('#filter-status').val() : '',
            receipt: $('#filter-receipt').val() || '',
        };
    }

    function fetchExpenses(page) {
        if (isFetching) return;
        isFetching = true;
        $('#expense-grid-table').addClass('expense-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: viewExpenseAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.status === 200 || res.html !== undefined)) {
                    $('#expense-table-body').html(res.html);
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
                        const totalAmt = Number(res.stats.total_amount || 0);
                        const paidAmt = Number(res.stats.paid_amount || 0);
                        const dueAmt = Number(res.stats.pending_amount || 0);
                        const recAmt = Number(res.stats.recurring_amount || 0);

                        $('#stat-total').text('₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#stat-paid').text('₹' + paidAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#stat-due').text('₹' + dueAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#stat-recurring').text('₹' + recAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#footer-total-amt').text('₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

                        $('#stat-count').text('(' + (res.stats.total_count || 0) + ')');
                        $('#stat-paid-count').text('(' + (res.stats.paid_count || 0) + ')');
                        $('#stat-due-count').text('(' + (res.stats.pending_count || 0) + ')');
                        $('#stat-recurring-count').text('(' + (res.stats.recurring_count || 0) + ')');
                    }

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);

                    bindDeleteModalEvents();
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch expenses:', xhr);
            },
            complete: function() {
                isFetching = false;
                $('#expense-grid-table').removeClass('expense-table-loading');
            }
        });
    }

    // Debounced text inputs (300ms)
    $('#filter-voucher, #filter-name, #filter-payee, #filter-rate-qty, #filter-amount').on('input', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchExpenses(1);
        }, 300);
    });

    // Instant dropdown triggers
    $('#filter-category, #filter-pm, #filter-status, #filter-receipt').on('change', function() {
        currentPage = 1;
        fetchExpenses(1);
    });

    // Rows per page dropdown
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchExpenses(1);
    });

    // Pagination Nav Click Handlers
    $('#btn-first').on('click', function() {
        if (currentPage > 1) fetchExpenses(1);
    });
    $('#btn-prev').on('click', function() {
        if (currentPage > 1) fetchExpenses(currentPage - 1);
    });
    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) fetchExpenses(currentPage + 1);
    });
    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) fetchExpenses(lastPage);
    });

    // Reset All Filters
    $('#btn-reset-filters, #btn-clear-filters-icon').on('click', function() {
        $('#filter-voucher, #filter-name, #filter-payee, #filter-rate-qty, #filter-amount').val('');
        $('#filter-category, #filter-pm, #filter-status, #filter-receipt').val('');
        currentPage = 1;
        fetchExpenses(1);
    });

    // Delete Modal Event Binding
    function bindDeleteModalEvents() {
        $('.delete-expense-trigger').off('click').on('click', function() {
            $('#delete_id').val($(this).data('id') || '');
            $('#del_modal_invoice').text($(this).data('invoice') || '-');
            $('#del_modal_name').text($(this).data('name') || '-');
            $('#del_modal_amount').text($(this).data('amount') || '-');
        });
    }

    bindDeleteModalEvents();
});
</script>
@endsection
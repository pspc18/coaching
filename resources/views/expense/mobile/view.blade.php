@php
    $expensePermission = Helper::permissioncheck(16);
    $canAdd = $expensePermission->add ?? true;
    $canEdit = $expensePermission->edit ?? true;
    $canDelete = $expensePermission->delete ?? true;

    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $totalAmt = (float)($stats['total_amount'] ?? 0);
    $totalCount = (int)($stats['total_count'] ?? ($totalCount ?? count($data ?? [])));
    $paidAmt = (float)($stats['paid_amount'] ?? 0);
    $paidCount = (int)($stats['paid_count'] ?? 0);
    $pendingAmt = (float)($stats['pending_amount'] ?? 0);
    $pendingCount = (int)($stats['pending_count'] ?? 0);
    $recurringCount = (int)($stats['recurring_count'] ?? 0);
    $recurringAmt = (float)($stats['recurring_amount'] ?? 0);

    $currSearch = $search ?? [];
    $hasActiveFilter = !empty($currSearch['category']) || !empty($currSearch['from_date']) || !empty($currSearch['to_date']) || !empty($currSearch['payment_status']) || !empty($currSearch['expense_type']) || !empty($currSearch['payment_mode_id']) || !empty($currSearch['receipt']);
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE EXPENSE DIRECTORY & REGISTER STYLES
   - Aligned with Arise ERP Mobile Design System (enquiryView, admissionView)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Glassmorphic Hero Card with Live Financial KPIs
   - Interactive Horizontal Filter Chips with instant AJAX re-rendering
   - Touch-optimized Card Feed with 36px Category Avatars & Rate x Qty calculations
   - Bottom Sheet Drawer for Advanced Filtering & Date Pickers
   - Pinned Action Dock for Pagination & Fast Record Jumps
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* 4 Metrics Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 3px;
    text-align: center;
    transition: background .15s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 1px;
}
.mob-metric-val {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-total { color: #38bdf8; }
.mob-metric-val.val-paid { color: #4ade80; }
.mob-metric-val.val-pending { color: #f87171; }
.mob-metric-val.val-recurring { color: #fbbf24; }
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Fast Actions Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 32px;
    padding: 0 10px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-act-btn:active {
    transform: scale(0.96);
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 1.4;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
    position: relative;
}
.filter-active-dot {
    position: absolute;
    top: 5px;
    right: 6px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 6px #38bdf8;
}

/* 2. Compact Search & Horizontal Filter Chips Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11.5px;
}
.mob-search-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}

/* Chips Slider */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.mob-chip-count {
    font-size: 8.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-chip.active .mob-chip-count {
    background: rgba(255, 255, 255, 0.22);
}

/* 3. Expense Feed Cards */
.expense-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 54px;
}
.exp-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
    border-left: 3.5px solid #94a3b8;
}
.exp-mob-card.border-paid { border-left-color: #16a34a; }
.exp-mob-card.border-pending { border-left-color: #ef4444; }

/* Card Header */
.exp-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.exp-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.exp-avatar-box.avatar-paid {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
    border-color: #bbf7d0;
}
.exp-avatar-box.avatar-pending {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
    border-color: #fecaca;
}
.exp-header-info {
    flex: 1;
    overflow: hidden;
}
.exp-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.exp-mob-voucher {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.exp-status-pill {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 3px;
    text-transform: uppercase;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.status-pill-paid {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.status-pill-pending {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.exp-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.exp-category-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid rgba(2, 132, 199, 0.2);
}
.exp-recurring-badge {
    font-size: 9px;
    font-weight: 800;
    background: #fef3c7;
    color: #b45309;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #fde68a;
}
.exp-date-badge {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

/* Card Body */
.exp-card-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 7px;
}
.exp-particular-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
}

/* Amount & Calculation Glance */
.exp-amount-glance {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
}
.exp-amount-left {
    display: flex;
    flex-direction: column;
}
.exp-amount-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.exp-amount-val {
    font-size: 15px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.1;
}
.exp-amount-right {
    text-align: right;
}
.exp-calc-text {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2px 6px;
    border-radius: 2px;
}

/* Meta Grid */
.exp-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 6px;
    font-size: 10.5px;
}
.exp-meta-item {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.exp-meta-label {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.exp-meta-value {
    font-weight: 700;
    color: #334155;
    line-height: 1.25;
}

.exp-note-preview {
    font-size: 10.5px;
    color: #475569;
    background: #f8fafc;
    border-left: 2px solid #cbd5e1;
    padding: 3px 6px;
    border-radius: 0 2px 2px 0;
    line-height: 1.35;
}

/* Card Actions Strip */
.exp-card-actions {
    display: flex;
    gap: 4px;
    border-top: 1px solid #f1f5f9;
    padding-top: 6px;
    align-items: center;
}
.exp-act-btn {
    flex: 1;
    height: 28px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all .1s ease;
    white-space: nowrap;
}
.exp-act-btn:active {
    transform: scale(0.96);
}
.exp-act-btn.btn-print {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
    flex: 1.2;
}
.exp-act-btn.btn-receipt {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #047857;
    flex: 1.2;
}
.exp-act-btn.btn-edit {
    background: #f8fafc;
    color: #475569;
    flex: 1;
}
.exp-act-btn.btn-delete {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #ef4444;
    flex: 0.6;
    max-width: 34px;
}
.exp-act-btn.btn-delete:active {
    background: #fee2e2;
}

/* 4. Compact Fixed Bottom Pagination Bar (Reference: admissionView) */
.mob-pagination-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.mob-pagination-info {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-pagination-info b,
.mob-pagination-info strong {
    color: #002C54;
    font-weight: 800;
}
.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-per-page-select {
    height: 28px;
    padding: 0 4px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    color: #1e293b;
    outline: none;
    cursor: pointer;
}
.mob-per-page-select option {
    background: #ffffff;
    color: #1e293b;
}
.mob-page-indicator {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
    padding: 0 4px;
    min-width: 28px;
    text-align: center;
}
.mob-page-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-page-btn:disabled, .mob-page-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.mob-page-btn:not(:disabled):not(.disabled):active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: scale(0.94);
}

/* 5. Mobile Filter Bottom Sheet */
.mob-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 4px 4px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-bottom-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    padding: 0 4px;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
}
.mob-sheet-footer {
    padding: 8px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
}

/* Empty State */
.exp-empty-box {
    text-align: center;
    padding: 42px 16px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
}
.exp-empty-box i {
    font-size: 32px;
    color: #cbd5e1;
    margin-bottom: 7px;
}
.exp-empty-box h4 {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 2px;
}
.exp-empty-box p {
    font-size: 11px;
    color: #64748b;
    margin: 0;
}

/* Loading Overlay inside Feed */
.mob-feed-loader {
    display: none;
    text-align: center;
    padding: 24px;
    color: #002C54;
    font-size: 12px;
    font-weight: 700;
}
</style>
@endsection

@section('content')

{{-- 1. Signature Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-calculator text-primary"></i> Expense Register
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Metrics Glance Grid (Total, Paid, Pending, Recurring) --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Total</span>
            <span class="mob-metric-val val-total" id="metricTotalAmt">₹{{ number_format($totalAmt, 0) }}</span>
            <span class="mob-metric-sub" id="metricTotalCount">{{ $totalCount }} entries</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Paid</span>
            <span class="mob-metric-val val-paid" id="metricPaidAmt">₹{{ number_format($paidAmt, 0) }}</span>
            <span class="mob-metric-sub" id="metricPaidCount">{{ $paidCount }} vouchers</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Pending</span>
            <span class="mob-metric-val val-pending" id="metricPendingAmt">₹{{ number_format($pendingAmt, 0) }}</span>
            <span class="mob-metric-sub" id="metricPendingCount">{{ $pendingCount }} vouchers</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Recurring</span>
            <span class="mob-metric-val val-recurring" id="metricRecAmt">₹{{ number_format($recurringAmt, 0) }}</span>
            <span class="mob-metric-sub" id="metricRecCount">{{ $recurringCount }} entries</span>
        </div>
    </div>

    {{-- Fast Action Bar --}}
    <div class="mob-actions-bar">
        @if($canAdd)
            <a href="{{ url('expenseAdd') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-plus-circle"></i> Add New Expense
            </a>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnOpenFilterSheet">
            <i class="fa fa-filter"></i> Filters
            @if($hasActiveFilter)
                <span class="filter-active-dot"></span>
            @endif
        </button>
    </div>
</div>

{{-- Flash Feedback Alert --}}
@if(session('message'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:11px; border-radius:3px; font-weight:700;">
        <i class="fa fa-check mr-1"></i> {{ session('message') }}
    </div>
@endif

{{-- 2. Compact Search & Horizontal Category Filter Chips Toolbar --}}
<div class="mob-search-toolbar">
    <div class="mob-search-input-wrap">
        <i class="fa fa-search search-icon"></i>
        <input type="text" class="mob-search-input" id="mobKeywordSearch" placeholder="Search by particular, payee, voucher no..." value="{{ $currSearch['keyword'] ?? '' }}" autocomplete="off">
        <i class="fa fa-times-circle mob-search-clear" id="mobClearSearch"></i>
    </div>

    {{-- Quick Horizontal Status & Common Chips --}}
    <div class="mob-chips-scroll">
        <span class="mob-chip {{ empty($currSearch['payment_status']) && empty($currSearch['expense_type']) && empty($currSearch['receipt']) ? 'active' : '' }}" data-filter-type="status" data-filter-val="">
            All Vouchers <span class="mob-chip-count">{{ $totalCount }}</span>
        </span>
        <span class="mob-chip {{ ($currSearch['payment_status'] ?? '') === 'paid' ? 'active' : '' }}" data-filter-type="status" data-filter-val="paid">
            <i class="fa fa-check text-success"></i> Paid <span class="mob-chip-count">{{ $paidCount }}</span>
        </span>
        <span class="mob-chip {{ ($currSearch['payment_status'] ?? '') === 'pending' ? 'active' : '' }}" data-filter-type="status" data-filter-val="pending">
            <i class="fa fa-clock-o text-danger"></i> Pending <span class="mob-chip-count">{{ $pendingCount }}</span>
        </span>
        <span class="mob-chip {{ ($currSearch['expense_type'] ?? '') === 'recurring' ? 'active' : '' }}" data-filter-type="type" data-filter-val="recurring">
            <i class="fa fa-repeat text-warning"></i> Recurring <span class="mob-chip-count">{{ $recurringCount }}</span>
        </span>
        <span class="mob-chip {{ ($currSearch['receipt'] ?? '') === 'with' ? 'active' : '' }}" data-filter-type="receipt" data-filter-val="with">
            <i class="fa fa-paperclip"></i> With Bill
        </span>
    </div>
</div>

{{-- Loader Spinner --}}
<div class="mob-feed-loader" id="mobFeedLoader">
    <i class="fa fa-spinner fa-spin fa-2x"></i>
    <div class="mt-1">Filtering expenses...</div>
</div>

{{-- 3. Native Expense Cards Feed --}}
<div class="expense-feed-list" id="expenseCardsContainer">
    @include('expense.mobile.card_rows', [
        'data' => $data,
        'startIndex' => $startIndex ?? 0,
        'categories' => $categories,
    ])
</div>

{{-- 4. Compact Fixed Bottom Pagination Bar (Reference: admissionView) --}}
<div class="mob-pagination-bar" id="mobPaginationBar" style="{{ ($totalCount == 0) ? 'display:none;' : '' }}">
    <div class="mob-pagination-info">
        Showing <b id="mobPageFrom">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</b> - <b id="mobPageTo">{{ min($startIndex + count($data), $totalCount) }}</b> of <b id="mobPageTotal">{{ $totalCount }}</b>
    </div>
    <div class="mob-pagination-btns">
        <select class="mob-per-page-select" id="mobPerPageSelect" title="Rows per page">
            <option value="15" {{ ($perPage == 15) ? 'selected' : '' }}>15</option>
            <option value="25" {{ ($perPage == 25 || empty($perPage)) ? 'selected' : '' }}>25</option>
            <option value="50" {{ ($perPage == 50) ? 'selected' : '' }}>50</option>
            <option value="100" {{ ($perPage == 100) ? 'selected' : '' }}>100</option>
            <option value="all" {{ ($perPage === 'all' || $perPage == -1) ? 'selected' : '' }}>All</option>
        </select>
        <button type="button" class="mob-page-btn {{ $currentPage <= 1 ? 'disabled' : '' }}" id="mobBtnPrevPage" title="Previous Page" {{ $currentPage <= 1 ? 'disabled' : '' }}>
            <i class="fa fa-chevron-left"></i>
        </button>
        <span class="mob-page-indicator" id="mobPageIndicator">{{ $currentPage }} / {{ $lastPage }}</span>
        <button type="button" class="mob-page-btn {{ $currentPage >= $lastPage ? 'disabled' : '' }}" id="mobBtnNextPage" title="Next Page" {{ $currentPage >= $lastPage ? 'disabled' : '' }}>
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>

{{-- 5. Mobile Filter Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="filterSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="filterBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders"></i> Filter Expense Vouchers
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        <form id="mobFilterForm" onsubmit="return false;">
            {{-- Category --}}
            <div class="form-group mb-2">
                <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">Expense Category</label>
                <select class="form-control form-control-sm" name="category" id="sheetCategory">
                    <option value="">All Categories</option>
                    @if(!empty($categories))
                        @foreach($categories as $catId => $catName)
                            <option value="{{ $catId }}" {{ ($currSearch['category'] ?? '') == $catId ? 'selected' : '' }}>
                                {{ $catName }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Date Range in 2 Cols --}}
            <div class="row">
                <div class="col-6 pr-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">From Date</label>
                        <input type="date" class="form-control form-control-sm" name="from_date" id="sheetFromDate" value="{{ $currSearch['from_date'] ?? '' }}">
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">To Date</label>
                        <input type="date" class="form-control form-control-sm" name="to_date" id="sheetToDate" value="{{ $currSearch['to_date'] ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- Payment Mode & Status in 2 Cols --}}
            <div class="row">
                <div class="col-6 pr-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">Payment Mode</label>
                        <select class="form-control form-control-sm" name="payment_mode_id" id="sheetPaymentMode">
                            <option value="">All Modes</option>
                            @if(!empty($paymentModes))
                                @foreach($paymentModes as $pm)
                                    <option value="{{ $pm->id }}" {{ ($currSearch['payment_mode_id'] ?? '') == $pm->id ? 'selected' : '' }}>
                                        {{ $pm->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">Status</label>
                        <select class="form-control form-control-sm" name="payment_status" id="sheetStatus">
                            <option value="">All Status</option>
                            <option value="paid" {{ ($currSearch['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ ($currSearch['payment_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Expense Type & Receipt Attachment in 2 Cols --}}
            <div class="row">
                <div class="col-6 pr-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">Expense Type</label>
                        <select class="form-control form-control-sm" name="expense_type" id="sheetExpenseType">
                            <option value="">All Types</option>
                            <option value="one_time" {{ ($currSearch['expense_type'] ?? '') === 'one_time' ? 'selected' : '' }}>One Time</option>
                            <option value="recurring" {{ ($currSearch['expense_type'] ?? '') === 'recurring' ? 'selected' : '' }}>Recurring</option>
                        </select>
                    </div>
                </div>
                <div class="col-6 pl-1">
                    <div class="form-group mb-2">
                        <label style="font-size:10px; font-weight:700; color:#475569; text-transform:uppercase;">Attachment</label>
                        <select class="form-control form-control-sm" name="receipt" id="sheetReceipt">
                            <option value="">All</option>
                            <option value="with" {{ ($currSearch['receipt'] ?? '') === 'with' ? 'selected' : '' }}>With Bill</option>
                            <option value="without" {{ ($currSearch['receipt'] ?? '') === 'without' ? 'selected' : '' }}>Without Bill</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn btn-secondary btn-sm flex-fill" id="btnResetFilterSheet">
            <i class="fa fa-refresh mr-1"></i> Reset
        </button>
        <button type="button" class="btn btn-primary btn-sm flex-fill" id="btnApplyFilterSheet">
            <i class="fa fa-check mr-1"></i> Apply Filters
        </button>
    </div>
</div>

{{-- Hidden Delete Form --}}
<form id="mobDeleteForm" action="{{ url('expenseDelete') }}" method="post" style="display:none;">
    @csrf
    <input type="hidden" name="delete_id" id="mobDeleteId">
</form>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var currentPage = parseInt("{{ $currentPage ?? 1 }}") || 1;
    var lastPage = parseInt("{{ $lastPage ?? 1 }}") || 1;
    var searchTimer = null;
    var baseUrl = "{{ url('expenseView') }}";

    // Bottom Sheet Controls
    function openFilterSheet() {
        $('#filterSheetBackdrop').addClass('show');
        $('#filterBottomSheet').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeFilterSheet() {
        $('#filterSheetBackdrop').removeClass('show');
        $('#filterBottomSheet').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#btnOpenFilterSheet').on('click', openFilterSheet);
    $('#btnCloseFilterSheet, #filterSheetBackdrop').on('click', closeFilterSheet);

    // Active Filter State
    function gatherFilterParams(pageToLoad) {
        var params = {
            page: pageToLoad || currentPage,
            per_page: $('#mobPerPageSelect').val(),
            keyword: $('#mobKeywordSearch').val().trim(),
            category: $('#sheetCategory').val(),
            from_date: $('#sheetFromDate').val(),
            to_date: $('#sheetToDate').val(),
            payment_mode_id: $('#sheetPaymentMode').val(),
            payment_status: $('#sheetStatus').val(),
            expense_type: $('#sheetExpenseType').val(),
            receipt: $('#sheetReceipt').val(),
            layout: 'mobile'
        };
        return params;
    }

    // High Performance AJAX Engine
    function fetchExpenseData(pageToLoad, shouldScroll) {
        if (pageToLoad) currentPage = parseInt(pageToLoad) || 1;
        var params = gatherFilterParams(currentPage);

        $('#mobFeedLoader').show();
        $('#expenseCardsContainer').css('opacity', '0.4');

        $.ajax({
            url: baseUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $('#mobFeedLoader').hide();
                $('#expenseCardsContainer').css('opacity', '1').html(res.html);

                // Update Pagination Numbers
                currentPage = parseInt(res.current_page) || 1;
                lastPage = parseInt(res.last_page) || 1;
                var total = parseInt(res.total) || 0;
                var from = parseInt(res.from) || 0;
                var to = parseInt(res.to) || 0;

                $('#mobPageFrom').text(from);
                $('#mobPageTo').text(to);
                $('#mobPageTotal').text(total);
                $('#mobPageIndicator').text(currentPage + ' / ' + lastPage);

                $('#mobBtnPrevPage').prop('disabled', currentPage <= 1).toggleClass('disabled', currentPage <= 1);
                $('#mobBtnNextPage').prop('disabled', currentPage >= lastPage).toggleClass('disabled', currentPage >= lastPage);

                if (total <= 0) {
                    $('#mobPaginationBar').hide();
                } else {
                    $('#mobPaginationBar').show();
                }

                // Update Hero KPIs
                if (res.stats) {
                    $('#metricTotalAmt').text('₹' + Number(res.stats.total_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 0}));
                    $('#metricTotalCount').text((res.stats.total_count || 0) + ' entries');

                    $('#metricPaidAmt').text('₹' + Number(res.stats.paid_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 0}));
                    $('#metricPaidCount').text((res.stats.paid_count || 0) + ' vouchers');

                    $('#metricPendingAmt').text('₹' + Number(res.stats.pending_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 0}));
                    $('#metricPendingCount').text((res.stats.pending_count || 0) + ' vouchers');

                    $('#metricRecAmt').text('₹' + Number(res.stats.recurring_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 0}));
                    $('#metricRecCount').text((res.stats.recurring_count || 0) + ' entries');
                }

                // Smooth scroll to top of list if paging
                if (shouldScroll) {
                    $('html, body').animate({ scrollTop: $('#expenseCardsContainer').offset().top - 80 }, 200);
                }
            },
            error: function() {
                $('#mobFeedLoader').hide();
                $('#expenseCardsContainer').css('opacity', '1');
            }
        });
    }

    // Real-Time Search Debounce
    $('#mobKeywordSearch').on('input', function() {
        var val = $(this).val();
        $('#mobClearSearch').toggle(val.length > 0);

        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            fetchExpenseData(1);
        }, 350);
    });

    $('#mobClearSearch').on('click', function() {
        $('#mobKeywordSearch').val('');
        $(this).hide();
        fetchExpenseData(1);
    });

    // Horizontal Chips Quick Filters
    $('.mob-chip').on('click', function() {
        $('.mob-chip').removeClass('active');
        $(this).addClass('active');

        var filterType = $(this).data('filter-type');
        var filterVal = $(this).data('filter-val');

        // Reset other specific selectors in sheet
        if (filterType === 'status') {
            $('#sheetStatus').val(filterVal);
            $('#sheetExpenseType').val('');
            $('#sheetReceipt').val('');
        } else if (filterType === 'type') {
            $('#sheetExpenseType').val(filterVal);
            $('#sheetStatus').val('');
            $('#sheetReceipt').val('');
        } else if (filterType === 'receipt') {
            $('#sheetReceipt').val(filterVal);
            $('#sheetStatus').val('');
            $('#sheetExpenseType').val('');
        }

        fetchExpenseData(1);
    });

    // Apply Filter Sheet
    $('#btnApplyFilterSheet').on('click', function() {
        closeFilterSheet();
        $('.mob-chip').removeClass('active');
        fetchExpenseData(1);
    });

    // Reset Filter Sheet
    $('#btnResetFilterSheet').on('click', function() {
        document.getElementById('mobFilterForm').reset();
        $('#mobKeywordSearch').val('');
        $('#mobClearSearch').hide();
        $('.mob-chip').removeClass('active');
        $('.mob-chip[data-filter-val=""]').addClass('active');
        closeFilterSheet();
        fetchExpenseData(1);
    });

    // Pagination controls
    $('#mobPerPageSelect').on('change', function() {
        currentPage = 1;
        fetchExpenseData(1, true);
    });

    $('#mobBtnPrevPage').on('click', function(e) {
        e.preventDefault();
        if (currentPage > 1 && !$(this).hasClass('disabled')) {
            fetchExpenseData(currentPage - 1, true);
        }
    });

    $('#mobBtnNextPage').on('click', function(e) {
        e.preventDefault();
        if (currentPage < lastPage && !$(this).hasClass('disabled')) {
            fetchExpenseData(currentPage + 1, true);
        }
    });

    // Delete Expense Voucher (Native Slide-Up Bottom Sheet Confirmation)
    $(document).on('click', '.btn-exp-delete', function() {
        var id = $(this).data('id');
        var voucher = $(this).data('voucher');
        var amount = $(this).data('amount');

        showMobileConfirm(
            'Delete Expense Voucher',
            'Are you sure you want to delete ' + (voucher ? ('voucher <b>' + voucher + '</b>') : 'this expense') + ' of amount <b class="text-danger">' + amount + '</b>? This cannot be undone.',
            function() {
                $('#mobDeleteId').val(id);
                $('#mobDeleteForm').submit();
            },
            '<i class="fa fa-trash"></i> Yes, Delete',
            true,
            'fa-trash text-danger'
        );
    });
});
</script>
@endsection

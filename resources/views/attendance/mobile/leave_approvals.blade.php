@php
    $counts = $counts ?? (object)[
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'cancelled' => 0,
        'total' => 0
    ];
    $statusFilter = $statusFilter ?? '2';
    $userTypeFilter = $userTypeFilter ?? '';
    $totalCount = $totalCount ?? count($rows ?? []);
    $page = $page ?? 1;
    $perPage = $perPage ?? 20;
    $lastPage = $lastPage ?? 1;
    $startIndex = $startIndex ?? 0;
    $from = $from ?? ($totalCount > 0 ? 1 : 0);
    $to = $to ?? min(count($rows ?? []), $totalCount);

    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $currentBranchName = Session::get('branch_name') ?? 'Main Campus';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE LEAVE APPROVALS (EXACT ADMISSION VIEW MATCH)
   ========================================================================== */

/* 1. Glassmorphic Navy Hero Card */
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
    cursor: pointer;
    transition: all .15s ease;
    user-select: none;
}
.mob-metric-box:active {
    transform: scale(0.96);
    background: rgba(255, 255, 255, 0.16);
}
.mob-metric-box.active {
    border-color: #38bdf8;
    background: rgba(2, 132, 199, 0.25);
    box-shadow: 0 0 8px rgba(56, 189, 248, 0.3);
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
    margin-bottom: 2px;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-pending { color: #fbbf24; }
.mob-metric-val.val-approved { color: #4ade80; }
.mob-metric-val.val-rejected { color: #f87171; }
.mob-metric-val.val-total { color: #38bdf8; }

/* Fast Action Bar in Hero */
.mob-hero-actions {
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
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-filter.has-active {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff !important;
}
.mob-act-btn-refresh {
    background: rgba(255, 255, 255, 0.08);
    color: #cbd5e1 !important;
    border: 1px solid rgba(255, 255, 255, 0.15);
    max-width: 36px;
}
.mob-act-btn:active {
    transform: scale(0.96);
}

/* 2. Interactive Status Chips Slider */
.mob-status-chips-wrap {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 6px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-status-chips-wrap::-webkit-scrollbar {
    display: none;
}
.mob-status-chip {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .12s ease;
    flex-shrink: 0;
    user-select: none;
}
.mob-status-chip:active {
    transform: scale(0.96);
}
.mob-status-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.mob-status-chip.chip-pending.active {
    background: #d97706;
    border-color: #d97706;
}
.mob-status-chip.chip-approved.active {
    background: #16a34a;
    border-color: #16a34a;
}
.mob-status-chip.chip-rejected.active {
    background: #dc2626;
    border-color: #dc2626;
}
.mob-status-chip.chip-cancelled.active {
    background: #475569;
    border-color: #475569;
}
.mob-chip-count {
    font-size: 8.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
    font-weight: 800;
}
.mob-status-chip.active .mob-chip-count {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* 3. Role / User Type Segment Control */
.mob-role-segment {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
}
.mob-role-btn {
    flex: 1;
    height: 28px;
    border: none;
    background: transparent;
    color: #475569;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-role-btn.active {
    background: #ffffff;
    color: #002C54;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* 4. Expandable Filter Drawer / Search Tray */
.mob-filter-tray {
    display: none;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-filter-tray.show {
    display: block;
    animation: mobFadeDown .18s ease-out;
}
@keyframes mobFadeDown {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.mob-search-input-wrap {
    position: relative;
    margin-bottom: 6px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
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
.mob-search-icon {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
}
.mob-date-filter-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-filter-group label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
    display: block;
    text-transform: uppercase;
}
.mob-filter-date {
    width: 100%;
    height: 28px;
    padding: 0 6px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-tray-actions {
    display: flex;
    justify-content: space-between;
    gap: 6px;
    margin-top: 4px;
}
.mob-tray-btn {
    height: 28px;
    padding: 0 10px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 3px;
    border: none;
    cursor: pointer;
}
.mob-tray-btn-reset {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.mob-tray-btn-apply {
    background: #0284c7;
    color: #ffffff;
    flex: 1;
}

/* 5. Mobile Leave Cards List (Exact Admission View Card CSS) */
.mob-leaves-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 48px;
    width: 100%;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.student-mob-card:active {
    border-color: #94a3b8;
}

.student-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.avatar-student {
    background: #e0f2fe;
    color: #0284c7;
}
.avatar-staff {
    background: #f1f5f9;
    color: #334155;
}
.student-header-info {
    flex: 1;
    overflow: hidden;
}
.student-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.student-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}
.student-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}
.student-role-staff-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.student-adm-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    font-family: monospace;
}
.student-sno-badge {
    font-size: 8.5px;
    color: #94a3b8;
    font-weight: 700;
    margin-left: auto;
}
.student-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
}
.status-badge-pending {
    background: #fef3c7;
    color: #b45309;
}
.status-badge-approved {
    background: #dcfce7;
    color: #16a34a;
}
.status-badge-rejected {
    background: #fee2e2;
    color: #dc2626;
}
.status-badge-cancelled {
    background: #f1f5f9;
    color: #64748b;
}

/* Card Details Meta Grid (Exact Admission View Grid) */
.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 8px;
}
.student-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-meta-item.full-width {
    grid-column: 1 / -1;
}
.student-meta-item > i {
    width: 13px;
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
}
.leave-reason-snippet {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #475569;
}

/* Card Actions Row (Strict 29px height, balanced layout) */
.student-card-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.leave-act-form {
    margin: 0;
    display: inline-flex;
}
.leave-act-form.flex-fill {
    flex: 1 1 auto;
}
.leave-act-form.action-delete-form {
    flex: 0 0 29px;
    width: 29px;
}
.mob-btn-action {
    width: 100%;
    height: 29px;
    padding: 0 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .1s ease;
}
.mob-btn-action:active {
    transform: scale(0.96);
}
.btn-act-approve {
    background: #16a34a;
    color: #ffffff !important;
}
.btn-act-reject {
    background: #dc2626;
    color: #ffffff !important;
}
.btn-act-cancel {
    background: #f59e0b;
    color: #ffffff !important;
}
.btn-act-delete {
    width: 29px;
    height: 29px;
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #dc2626 !important;
    padding: 0;
}

/* 6. Centered Empty State */
.mob-empty-state {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    color: #64748b;
    margin: 8px 0;
}
.mob-empty-icon {
    font-size: 32px;
    color: #cbd5e1;
    margin-bottom: 6px;
}
.mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 10.5px;
    color: #64748b;
    margin-bottom: 10px;
}
.mob-btn-reset-filters {
    height: 28px;
    padding: 0 12px;
    font-size: 10.5px;
    font-weight: 700;
    background: #002C54;
    color: #ffffff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

/* 7. Compact Fixed Bottom Pagination Bar */
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
    padding: 6px 12px;
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
.mob-pagination-info b {
    color: #002C54;
}
.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-page-indicator {
    font-size: 10px;
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

/* 8. Action Confirmation Dialog */
.mob-confirm-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.7);
    z-index: 2100;
    opacity: 0;
    visibility: hidden;
    transition: all .2s ease;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-confirm-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-confirm-sheet {
    position: fixed;
    bottom: -300px;
    left: 8px;
    right: 8px;
    background: #ffffff;
    border-radius: 8px 8px 6px 6px;
    padding: 14px 14px 16px;
    z-index: 2101;
    transition: bottom .24s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
}
.mob-confirm-sheet.show {
    bottom: calc(10px + env(safe-area-inset-bottom, 0px));
}
.mob-confirm-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-confirm-desc {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 12px;
    line-height: 1.4;
}
.mob-confirm-buttons {
    display: flex;
    gap: 8px;
}
.mob-btn-cancel-dialog {
    flex: 1;
    height: 34px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
}
.mob-btn-execute-dialog {
    flex: 1.5;
    height: 34px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 800;
    cursor: pointer;
}

/* Loading Overlay */
.mob-loading-cover {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 24, 51, 0.45);
    backdrop-filter: blur(2px);
    z-index: 2200;
    align-items: center;
    justify-content: center;
}
.mob-loading-cover.show {
    display: flex;
}
.mob-loading-box {
    background: #ffffff;
    padding: 12px 18px;
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
}
</style>
@endsection

@section('content')

{{-- Flash Feedback --}}
@if(session('message'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:11px; font-weight:700; border-radius:4px;">
        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:11px; font-weight:700; border-radius:4px;">
        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif

{{-- 1. Hero Summary Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-calendar-check-o text-primary"></i> Leave Approvals
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- 4 Metrics Glance Grid --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box {{ $statusFilter === '2' ? 'active' : '' }}" data-status="2">
            <span class="mob-metric-tag">Pending</span>
            <div class="mob-metric-val val-pending" id="statPending">{{ $counts->pending ?? 0 }}</div>
        </div>
        <div class="mob-metric-box {{ $statusFilter === '1' ? 'active' : '' }}" data-status="1">
            <span class="mob-metric-tag">Approved</span>
            <div class="mob-metric-val val-approved" id="statApproved">{{ $counts->approved ?? 0 }}</div>
        </div>
        <div class="mob-metric-box {{ $statusFilter === '0' ? 'active' : '' }}" data-status="0">
            <span class="mob-metric-tag">Rejected</span>
            <div class="mob-metric-val val-rejected" id="statRejected">{{ $counts->rejected ?? 0 }}</div>
        </div>
        <div class="mob-metric-box {{ $statusFilter === 'all' || $statusFilter === '' ? 'active' : '' }}" data-status="all">
            <span class="mob-metric-tag">Total</span>
            <div class="mob-metric-val val-total" id="statTotal">{{ $counts->total ?? 0 }}</div>
        </div>
    </div>

    {{-- Fast Action Bar in Hero --}}
    <div class="mob-hero-actions">
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnToggleFilterTray">
            <i class="fa fa-filter"></i> Search &amp; Filters
        </button>
        <button type="button" class="mob-act-btn mob-act-btn-refresh" id="btnMobRefresh" title="Refresh">
            <i class="fa fa-refresh"></i>
        </button>
    </div>
</div>

{{-- 2. Interactive Status Chips Slider --}}
<div class="mob-status-chips-wrap">
    <div class="mob-status-chip chip-pending {{ $statusFilter === '2' ? 'active' : '' }}" data-status="2">
        <i class="fa fa-clock-o"></i> Pending
        <span class="mob-chip-count" id="chipPendingCount">{{ $counts->pending ?? 0 }}</span>
    </div>
    <div class="mob-status-chip chip-approved {{ $statusFilter === '1' ? 'active' : '' }}" data-status="1">
        <i class="fa fa-check-circle"></i> Approved
        <span class="mob-chip-count" id="chipApprovedCount">{{ $counts->approved ?? 0 }}</span>
    </div>
    <div class="mob-status-chip chip-rejected {{ $statusFilter === '0' ? 'active' : '' }}" data-status="0">
        <i class="fa fa-times-circle"></i> Rejected
        <span class="mob-chip-count" id="chipRejectedCount">{{ $counts->rejected ?? 0 }}</span>
    </div>
    <div class="mob-status-chip chip-cancelled {{ $statusFilter === '3' ? 'active' : '' }}" data-status="3">
        <i class="fa fa-ban"></i> Cancelled
        <span class="mob-chip-count" id="chipCancelledCount">{{ $counts->cancelled ?? 0 }}</span>
    </div>
    <div class="mob-status-chip {{ $statusFilter === 'all' || $statusFilter === '' ? 'active' : '' }}" data-status="all">
        <i class="fa fa-list-alt"></i> All Leaves
    </div>
</div>

{{-- 3. Role / User Type Segment Control --}}
<div class="mob-role-segment">
    <button type="button" class="mob-role-btn {{ empty($userTypeFilter) || $userTypeFilter === 'all' ? 'active' : '' }}" data-type="">
        <i class="fa fa-users"></i> All Persons
    </button>
    <button type="button" class="mob-role-btn {{ ($userTypeFilter ?? '') === 'student' ? 'active' : '' }}" data-type="student">
        <i class="fa fa-graduation-cap"></i> Students Only
    </button>
    <button type="button" class="mob-role-btn {{ ($userTypeFilter ?? '') === 'staff' ? 'active' : '' }}" data-type="staff">
        <i class="fa fa-briefcase"></i> Staff Only
    </button>
</div>

{{-- 4. Fast Search & Date Filter Tray --}}
<div class="mob-filter-tray" id="mobFilterTray">
    {{-- Search Keyword Input --}}
    <div class="mob-search-input-wrap">
        <i class="fa fa-search mob-search-icon"></i>
        <input type="text" class="mob-search-input" id="mobSearchQuery" placeholder="Search by name, ID or reason..." value="{{ $nameFilter ?? '' }}">
        <span class="mob-search-clear" id="mobSearchClear">&times;</span>
    </div>

    {{-- Date Filters --}}
    <div class="mob-date-filter-row">
        <div class="mob-filter-group">
            <label>From Date</label>
            <input type="date" class="mob-filter-date" id="mobFromDate" value="{{ $fromDateFilter ?? '' }}">
        </div>
        <div class="mob-filter-group">
            <label>To Date</label>
            <input type="date" class="mob-filter-date" id="mobToDate" value="{{ $toDateFilter ?? '' }}">
        </div>
    </div>

    <div class="mob-tray-actions">
        <button type="button" class="mob-tray-btn mob-tray-btn-reset" id="btnResetTrayFilters">
            <i class="fa fa-refresh mr-1"></i> Reset
        </button>
        <button type="button" class="mob-tray-btn mob-tray-btn-apply" id="btnApplyTrayFilters">
            <i class="fa fa-check mr-1"></i> Apply Filter
        </button>
    </div>
</div>

{{-- 5. Mobile Leaves Cards List Container (Matches Student Feed List) --}}
<div class="mob-leaves-list" id="leaveCardsContainer">
    @include('attendance.mobile.leave_cards')
</div>

{{-- 6. Compact Pagination Footer (Matches Admission View) --}}
<div class="mob-pagination-bar">
    <div class="mob-pagination-info">
        Showing <b id="mobPageStart">{{ $from }}</b> - <b id="mobPageEnd">{{ $to }}</b> of <b id="mobTotalRecords">{{ $totalCount }}</b>
    </div>
    <div class="mob-pagination-btns">
        <button type="button" class="mob-page-btn" id="btnMobPrevPage" {{ $page <= 1 ? 'disabled' : '' }} title="Previous Page">
            <i class="fa fa-chevron-left"></i>
        </button>
        <span class="mob-page-indicator" id="mobPageIndicator">{{ $page }} / {{ $lastPage }}</span>
        <button type="button" class="mob-page-btn" id="btnMobNextPage" {{ $page >= $lastPage ? 'disabled' : '' }} title="Next Page">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>

{{-- 7. Action Confirmation Bottom Sheet Dialog --}}
<div class="mob-confirm-backdrop" id="confirmBackdrop"></div>
<div class="mob-confirm-sheet" id="confirmSheet">
    <div class="mob-confirm-title" id="confirmTitle">
        <i class="fa fa-question-circle text-primary"></i> Confirm Action
    </div>
    <div class="mob-confirm-desc" id="confirmDesc">
        Are you sure you want to proceed with this leave action?
    </div>
    <div class="mob-confirm-buttons">
        <button type="button" class="mob-btn-cancel-dialog" id="btnCancelConfirm">Cancel</button>
        <button type="button" class="mob-btn-execute-dialog" id="btnExecuteConfirm">Proceed</button>
    </div>
</div>

{{-- 8. Loading Overlay --}}
<div class="mob-loading-cover" id="mobLoadingCover">
    <div class="mob-loading-box">
        <i class="fa fa-spinner fa-spin text-primary" style="font-size:16px;"></i>
        <span>Updating Leave Records...</span>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentStatus = '{{ $statusFilter ?? "2" }}';
let currentUserType = '{{ $userTypeFilter ?? "" }}';
let currentPage = parseInt('{{ $page ?? 1 }}') || 1;
let currentLastPage = parseInt('{{ $lastPage ?? 1 }}') || 1;
let pendingFormToSubmit = null;
let searchDebounceTimer = null;
let isFetching = false;

// 1. Action Confirmation Dialog Handling
function confirmAction(formElement, title, desc) {
    pendingFormToSubmit = formElement;
    $('#confirmTitle').html(`<i class="fa fa-question-circle text-primary mr-1"></i> ${title}`);
    $('#confirmDesc').text(desc);
    $('#confirmBackdrop').addClass('show');
    $('#confirmSheet').addClass('show');
    return false; // Prevent default submit until user clicks Proceed
}

$(document).ready(function() {
    // Dialog Buttons
    $('#btnCancelConfirm, #confirmBackdrop').on('click', function() {
        $('#confirmBackdrop').removeClass('show');
        $('#confirmSheet').removeClass('show');
        pendingFormToSubmit = null;
    });

    $('#btnExecuteConfirm').on('click', function() {
        if (pendingFormToSubmit) {
            $('#confirmBackdrop').removeClass('show');
            $('#confirmSheet').removeClass('show');
            $('#mobLoadingCover').addClass('show');
            pendingFormToSubmit.submit();
        }
    });

    // 2. Fetch Leave Data via AJAX (Always requesting mobile card layout)
    function fetchLeaves() {
        if (isFetching) return;
        isFetching = true;
        $('#mobLoadingCover').addClass('show');

        const query = $('#mobSearchQuery').val().trim();
        const fromDate = $('#mobFromDate').val().trim();
        const toDate = $('#mobToDate').val().trim();

        $.ajax({
            url: "{{ url('attendance/leave/approvals') }}",
            type: 'GET',
            data: {
                ajax: 1,
                layout: 'mobile',
                view_type: 'mobile',
                page: currentPage,
                status: currentStatus,
                user_type: currentUserType,
                name: query,
                from_date: fromDate,
                to_date: toDate,
                per_page: 20
            },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                if (res && res.status) {
                    $('#leaveCardsContainer').html(res.html);

                    currentPage = parseInt(res.current_page) || 1;
                    currentLastPage = parseInt(res.last_page) || 1;

                    $('#mobPageStart').text(res.from);
                    $('#mobPageEnd').text(res.to);
                    $('#mobTotalRecords').text(res.total);
                    $('#mobPageIndicator').text(currentPage + ' / ' + currentLastPage);

                    $('#btnMobPrevPage').prop('disabled', currentPage <= 1);
                    $('#btnMobNextPage').prop('disabled', currentPage >= currentLastPage || currentLastPage <= 1);

                    // Update Counts
                    if (res.counts) {
                        $('#statPending, #chipPendingCount').text(res.counts.pending || 0);
                        $('#statApproved, #chipApprovedCount').text(res.counts.approved || 0);
                        $('#statRejected, #chipRejectedCount').text(res.counts.rejected || 0);
                        $('#chipCancelledCount').text(res.counts.cancelled || 0);
                        $('#statTotal').text(res.counts.total || 0);
                    }
                }
            },
            error: function(xhr, status, err) {
                console.error('AJAX load error:', err);
            },
            complete: function() {
                isFetching = false;
                $('#mobLoadingCover').removeClass('show');
            }
        });
    }

    // 3. Status Tab & Metric Box Selection
    $('.mob-status-chip, .mob-metric-box').on('click', function() {
        const st = $(this).attr('data-status');
        if (st !== undefined) {
            currentStatus = st;
            currentPage = 1;

            // Sync visual states
            $('.mob-status-chip').removeClass('active');
            $(`.mob-status-chip[data-status="${st}"]`).addClass('active');

            $('.mob-metric-box').removeClass('active');
            $(`.mob-metric-box[data-status="${st}"]`).addClass('active');

            fetchLeaves();
        }
    });

    // 4. Role / User Type Segment Control
    $('.mob-role-btn').on('click', function() {
        $('.mob-role-btn').removeClass('active');
        $(this).addClass('active');
        currentUserType = $(this).attr('data-type') || '';
        currentPage = 1;
        fetchLeaves();
    });

    // 5. Search & Filter Tray Toggle
    $('#btnToggleFilterTray').on('click', function() {
        $('#mobFilterTray').toggleClass('show');
        $(this).toggleClass('has-active', $('#mobFilterTray').hasClass('show'));
    });

    // Search Input with Debounce & Clear Icon
    $('#mobSearchQuery').on('input keyup', function() {
        const val = $(this).val().trim();
        $('#mobSearchClear').toggle(val.length > 0);

        clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(function() {
            currentPage = 1;
            fetchLeaves();
        }, 250);
    });

    $('#mobSearchClear').on('click', function() {
        $('#mobSearchQuery').val('');
        $(this).hide();
        currentPage = 1;
        fetchLeaves();
    });

    // Apply & Reset in Tray
    $('#btnApplyTrayFilters').on('click', function() {
        currentPage = 1;
        fetchLeaves();
    });

    $('#btnResetTrayFilters').on('click', function() {
        $('#mobSearchQuery').val('');
        $('#mobSearchClear').hide();
        $('#mobFromDate').val('');
        $('#mobToDate').val('');
        currentStatus = 'all';
        currentUserType = '';
        currentPage = 1;

        $('.mob-status-chip').removeClass('active');
        $('.mob-status-chip[data-status="all"]').addClass('active');
        $('.mob-metric-box').removeClass('active');
        $('.mob-metric-box[data-status="all"]').addClass('active');
        $('.mob-role-btn').removeClass('active');
        $('.mob-role-btn[data-type=""]').addClass('active');

        fetchLeaves();
    });

    $(document).on('click', '#btnMobResetEmpty', function() {
        $('#btnResetTrayFilters').trigger('click');
    });

    // Refresh Button
    $('#btnMobRefresh').on('click', function() {
        fetchLeaves();
    });

    // 6. Pagination Handlers
    $('#btnMobPrevPage').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchLeaves();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    $('#btnMobNextPage').on('click', function() {
        if (currentPage < currentLastPage) {
            currentPage++;
            fetchLeaves();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
</script>
@endsection
@php
    $classType = Helper::classType();
    $getEnquiryStatus = Helper::getEnquiryStatus();
    $permission = Helper::permissioncheck(28);
    $setting = Helper::getSetting();
    $currentSession = Session::get('session_id');
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $currentBranchName = Session::get('branch_name') ?? 'Main Branch';
    $todayDate = date('Y-m-d');

    // Calculate Enquiry Summary KPIs
    $totalCount = is_countable($data ?? []) ? count($data ?? []) : 0;
    $admissionCount = 0;
    $activeCount = 0;
    $todayFollowupCount = 0;
    $overdueFollowupCount = 0;

    if (!empty($data)) {
        foreach ($data as $item) {
            $st = strtolower($item->latest_status ?? $item->status ?? 'new');
            $adSt = strtolower($item->ad_status ?? '');
            if ($adSt === 'admission' || $st === 'admission') {
                $admissionCount++;
            }
            if (in_array($st, ['active', 'new', 'follow up', 'followup', 'in progress'])) {
                $activeCount++;
            }
            $fDate = !empty($item->latest_followup_date) ? date('Y-m-d', strtotime($item->latest_followup_date)) : '';
            if ($fDate === $todayDate) {
                $todayFollowupCount++;
            } elseif (!empty($fDate) && $fDate < $todayDate && !in_array($st, ['admission', 'closed', 'partially closed'])) {
                $overdueFollowupCount++;
            }
        }
    }
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE ENQUIRY MANAGEMENT (GOLD STANDARD ERP MOBILE)
   Self-contained, robust CSS matching Arise Mobile App Design System.
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
    padding: 6px 4px;
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
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-admission { color: #4ade80; }
.mob-metric-val.val-active { color: #38bdf8; }
.mob-metric-val.val-today { color: #fbbf24; }

/* Fast Action Bar in Hero */
.mob-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-actions-bar form {
    margin: 0 !important;
    padding: 0 !important;
    display: inline-flex !important;
}
.mob-act-btn {
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
    box-sizing: border-box;
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 2;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
    flex: 1.2;
    position: relative;
}
.mob-act-btn-pdf {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.4);
    width: 36px;
    padding: 0;
    flex: none;
}
.mob-act-btn:active {
    transform: scale(0.96);
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

/* 2. Compact Search & Horizontal Filter Chips */
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
    pointer-events: none;
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
    box-sizing: border-box;
    -webkit-appearance: none;
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
    -webkit-overflow-scrolling: touch;
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
    flex-shrink: 0;
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

/* 3. Enquiry Native Cards Feed */
.enquiry-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 70px; /* Offset for floating bottom pagination bar */
}
.enquiry-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
    border-left: 3.5px solid #94a3b8;
}
.enquiry-mob-card.border-active { border-left-color: #0284c7; }
.enquiry-mob-card.border-admission { border-left-color: #16a34a; }
.enquiry-mob-card.border-new { border-left-color: #f59e0b; }
.enquiry-mob-card.border-missed { border-left-color: #dc2626; }
.enquiry-mob-card.border-closed { border-left-color: #64748b; }

.enquiry-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.enquiry-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.enquiry-header-info {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.enquiry-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.enquiry-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.enquiry-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.enquiry-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid rgba(2, 132, 199, 0.2);
}
.enquiry-ref-badge {
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #64748b;
    padding: 1px 5px;
    border-radius: 2px;
}

/* Status Badges */
.mob-status-pill {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    text-transform: capitalize;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}
.status-pill-active { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.status-pill-new { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.status-pill-partially { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
.status-pill-missed { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.status-pill-closed { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.status-pill-admission { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }

/* Card Body Rows */
.enquiry-card-body {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 8px;
}
.enquiry-info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    gap: 6px;
}
.enquiry-info-left {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #475569;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
    flex: 1;
}
.enquiry-info-right {
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    flex-shrink: 0;
}
.mob-date-badge {
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    background: #f8fafc;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-date-badge.is-today {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
    font-weight: 800;
}
.mob-date-badge.is-overdue {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fecaca;
    font-weight: 800;
}
.enquiry-note-preview {
    font-size: 10.5px;
    color: #64748b;
    background: #f8fafc;
    border-left: 2px solid #cbd5e1;
    padding: 3px 6px;
    border-radius: 0 2px 2px 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Card Action Strip */
.enquiry-card-actions {
    display: flex;
    gap: 5px;
    border-top: 1px solid #f1f5f9;
    padding-top: 7px;
    align-items: center;
}
.enquiry-act-btn {
    height: 30px;
    border-radius: 4px;
    font-size: 11px;
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
    box-sizing: border-box;
    padding: 0 6px;
}
.enquiry-act-btn:active {
    transform: scale(0.96);
}
.enquiry-act-btn.btn-call {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #059669;
    flex: 1;
}
.enquiry-act-btn.btn-wa {
    background: #f0fdf4;
    border-color: #86efac;
    color: #16a34a;
    flex: 1;
}
.enquiry-act-btn.btn-followup {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #2563eb;
    flex: 1.5;
}
.enquiry-act-btn.btn-more {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #475569;
    width: 32px;
    padding: 0;
    flex: none;
}

/* 4. Compact Fixed Bottom Pagination Bar (Matches Admission View) */
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
.mob-pagination-info b {
    color: #002C54;
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
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
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
.mob-page-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.mob-page-btn:not(.disabled):active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: scale(0.94);
}

/* 5. Mobile Native Bottom Sheets */
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
    border-radius: 6px 6px 0 0;
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
    border-radius: 6px 6px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-select, .mob-form-input, .mob-form-textarea {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-form-textarea {
    height: 60px;
    padding: 6px 8px;
    resize: vertical;
}
.mob-form-select:focus, .mob-form-input:focus, .mob-form-textarea:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.btn-sheet-apply {
    flex: 2;
    height: 33px;
    background: #0284c7;
    color: #ffffff !important;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-sheet-reset {
    flex: 1;
    height: 33px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569 !important;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    cursor: pointer;
}
.btn-sheet-apply:active, .btn-sheet-reset:active {
    transform: scale(0.97);
}

/* Actions Sheet Option List */
.mob-actions-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 4px 0;
}
.mob-action-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    text-decoration: none !important;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-action-item:active {
    background: #e2e8f0;
}
.mob-action-item i {
    width: 18px;
    font-size: 14px;
    text-align: center;
    flex-shrink: 0;
}
.mob-action-item.text-danger {
    color: #dc2626 !important;
    background: #fff5f5;
    border-color: #fecaca;
}

/* Details Table inside Modal */
.detail-table {
    width: 100%;
    border-collapse: collapse;
}
.detail-table tr {
    border-bottom: 1px solid #f1f5f9;
}
.detail-table th {
    padding: 6px 0;
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    width: 38%;
    text-align: left;
}
.detail-table td {
    padding: 6px 0;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    text-align: right;
}

/* Empty State */
.mob-empty-state {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    margin-bottom: 12px;
}
.mob-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto 10px;
}
.mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 12px;
}
.btn-empty-reset {
    height: 30px;
    padding: 0 12px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-address-book-o text-primary"></i> Enquiry Desk
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- 4 Metric Glance Grid --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Total</span>
            <div class="mob-metric-val">{{ $totalCount }}</div>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Admissions</span>
            <div class="mob-metric-val val-admission">{{ $admissionCount }}</div>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Active</span>
            <div class="mob-metric-val val-active">{{ $activeCount }}</div>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag">Today</span>
            <div class="mob-metric-val val-today">{{ $todayFollowupCount }}</div>
        </div>
    </div>

    {{-- Fast Action Bar --}}
    <div class="mob-actions-bar">
        @if($permission->add ?? true)
            <a href="{{ url('enquiryAdd') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-plus"></i> + New Enquiry
            </a>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnOpenFilterSheet">
            <i class="fa fa-sliders"></i> Filters
            <span class="filter-active-dot" id="activeFilterDot" style="display:none;"></span>
        </button>
        <form action="{{ url('enquiryView') }}" method="post" class="d-inline m-0">
            @csrf
            <button type="submit" name="pdf" value="pdf" class="mob-act-btn mob-act-btn-pdf" title="Export PDF">
                <i class="fa fa-file-pdf-o"></i>
            </button>
        </form>
    </div>
</div>

{{-- 2. Compact Search & Filter Chips Toolbar --}}
<div class="mob-search-toolbar">
    <div class="mob-search-input-wrap">
        <i class="fa fa-search search-icon"></i>
        <input type="text" class="mob-search-input" id="mobEnquirySearchInput" placeholder="Search by name, mobile, father, class..." autocomplete="off">
        <i class="fa fa-times-circle mob-search-clear" id="mobSearchClearBtn"></i>
    </div>

    {{-- Horizontal Scrollable Filter Chips --}}
    <div class="mob-chips-scroll" id="mobFilterChips">
        <button type="button" class="mob-chip active" data-filter="all">
            All <span class="mob-chip-count">{{ $totalCount }}</span>
        </button>
        <button type="button" class="mob-chip" data-filter="active">
            Active <span class="mob-chip-count">{{ $activeCount }}</span>
        </button>
        <button type="button" class="mob-chip" data-filter="admission">
            Admission <span class="mob-chip-count">{{ $admissionCount }}</span>
        </button>
        <button type="button" class="mob-chip" data-filter="today">
            Follow Up Today <span class="mob-chip-count">{{ $todayFollowupCount }}</span>
        </button>
        @if($overdueFollowupCount > 0)
            <button type="button" class="mob-chip text-danger" data-filter="overdue">
                Overdue <span class="mob-chip-count" style="background:rgba(220,38,38,0.15); color:#dc2626;">{{ $overdueFollowupCount }}</span>
            </button>
        @endif
        <button type="button" class="mob-chip" data-filter="new">
            New
        </button>
        <button type="button" class="mob-chip" data-filter="partially closed">
            Partial
        </button>
        <button type="button" class="mob-chip" data-filter="missed">
            Missed
        </button>
        <button type="button" class="mob-chip" data-filter="closed">
            Closed
        </button>
    </div>
</div>

{{-- 3. Enquiry Feed List --}}
<div class="enquiry-feed-list" id="enquiryCardList">
    @if(!empty($data) && count($data) > 0)
        @foreach ($data as $item)
            @php
                $fullName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
                if (empty($fullName)) $fullName = 'Enquiry #' . ($item->id ?? '');
                $initials = strtoupper(substr($item['first_name'] ?? 'E', 0, 1) . substr($item['last_name'] ?? '', 0, 1));
                if (empty($initials)) $initials = 'EN';

                $latestStatus = $item->latest_status ?? 'New';
                $statusSlug = strtolower($latestStatus);
                $adStatus = ($item->ad_status ?? '') == 'Admission' ? 'Admission' : '';
                
                $regDateRaw = !empty($item['registration_date']) ? date('Y-m-d', strtotime($item['registration_date'])) : '';
                $regDateFormatted = !empty($item['registration_date']) ? date('d-M-Y', strtotime($item['registration_date'])) : '-';
                
                $followupDateRaw = !empty($item->latest_followup_date) ? date('Y-m-d', strtotime($item->latest_followup_date)) : '';
                $followupDateFormatted = !empty($item->latest_followup_date) ? date('d-M-Y', strtotime($item->latest_followup_date)) : '-';
                
                $isTodayFollowup = ($followupDateRaw === $todayDate);
                $isOverdueFollowup = (!empty($followupDateRaw) && $followupDateRaw < $todayDate && !in_array($statusSlug, ['admission', 'closed', 'partially closed']));

                $borderClass = match(true) {
                    $adStatus === 'Admission' || $statusSlug === 'admission' => 'border-admission',
                    $isOverdueFollowup => 'border-missed',
                    in_array($statusSlug, ['active', 'follow up', 'followup']) => 'border-active',
                    $statusSlug === 'new' => 'border-new',
                    $statusSlug === 'missed' => 'border-missed',
                    $statusSlug === 'closed' => 'border-closed',
                    default => 'border-active'
                };

                $pillClass = match(true) {
                    $adStatus === 'Admission' || $statusSlug === 'admission' => 'status-pill-admission',
                    in_array($statusSlug, ['active', 'follow up', 'followup']) => 'status-pill-active',
                    $statusSlug === 'new' => 'status-pill-new',
                    $statusSlug === 'partially closed' => 'status-pill-partially',
                    $statusSlug === 'missed' => 'status-pill-missed',
                    $statusSlug === 'closed' => 'status-pill-closed',
                    default => 'status-pill-active'
                };

                $mobileClean = preg_replace('/[^0-9]/', '', $item['mobile'] ?? '');
                $waMobile = strlen($mobileClean) == 10 ? '91' . $mobileClean : $mobileClean;
                $latestNoteText = $item->latest_note ?: $item->note;
            @endphp

            <div class="enquiry-mob-card {{ $borderClass }}"
                 data-id="{{ $item->id }}"
                 data-name="{{ strtolower($fullName) }}"
                 data-raw-name="{{ $fullName }}"
                 data-mobile="{{ strtolower($item['mobile'] ?? '') }}"
                 data-father="{{ strtolower($item['father_name'] ?? '') }}"
                 data-mother="{{ strtolower($item['mother_name'] ?? '') }}"
                 data-class="{{ strtolower($item['class_name'] ?? '') }}"
                 data-class-id="{{ $item['class_type_id'] ?? '' }}"
                 data-status="{{ $statusSlug }}"
                 data-raw-status="{{ $latestStatus }}"
                 data-ad-status="{{ strtolower($adStatus) }}"
                 data-reg-date="{{ $regDateRaw }}"
                 data-followup-date="{{ $followupDateRaw }}"
                 data-is-today="{{ $isTodayFollowup ? '1' : '0' }}"
                 data-is-overdue="{{ $isOverdueFollowup ? '1' : '0' }}"
                 data-edit-url="{{ url('enquiryEdit', $item->id) }}"
                 data-print-url="{{ url('registrationPrint', $item->id) }}"
                 data-detail-url="{{ url('studentRegistrationDetail', $item->id) }}"
                 data-searchable="{{ strtolower($fullName . ' ' . ($item['mobile'] ?? '') . ' ' . ($item['father_name'] ?? '') . ' ' . ($item['mother_name'] ?? '') . ' ' . ($item['class_name'] ?? '') . ' ' . $latestStatus . ' ' . ($item['reference_name'] ?? '') . ' ' . $latestNoteText) }}">

                {{-- Card Header --}}
                <div class="enquiry-card-header">
                    <div class="enquiry-avatar-box">
                        {{ $initials }}
                    </div>
                    <div class="enquiry-header-info">
                        <div class="enquiry-name-row">
                            <a href="javascript:void(0);" class="enquiry-mob-name btn-open-detail" data-id="{{ $item->id }}">
                                {{ $fullName }}
                            </a>
                            <span class="mob-status-pill {{ $pillClass }}">
                                {{ $latestStatus }}
                            </span>
                        </div>
                        <div class="enquiry-pills-wrap">
                            <span class="enquiry-class-badge">
                                <i class="fa fa-book mr-1"></i> {{ $item['class_name'] ?? 'Class -' }}
                            </span>
                            @if(!empty($item['reference_name']))
                                <span class="enquiry-ref-badge">
                                    <i class="fa fa-tag mr-1"></i> {{ $item['reference_name'] }}
                                </span>
                            @endif
                            @if($adStatus === 'Admission')
                                <span class="mob-status-pill status-pill-admission">
                                    <i class="fa fa-check-circle"></i> Admitted
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="enquiry-card-body">
                    <div class="enquiry-info-row">
                        <div class="enquiry-info-left">
                            <i class="fa fa-user-o text-muted"></i>
                            <span>{{ $item['father_name'] ?? '-' }} @if(!empty($item['mother_name'])) / {{ $item['mother_name'] }} @endif</span>
                        </div>
                        <div class="enquiry-info-right">
                            <span class="mob-date-badge" title="Registration Date">
                                <i class="fa fa-calendar-o"></i> {{ $regDateFormatted }}
                            </span>
                        </div>
                    </div>

                    <div class="enquiry-info-row">
                        <div class="enquiry-info-left">
                            <i class="fa fa-phone text-muted"></i>
                            @if(!empty($item['mobile']))
                                <a href="tel:{{ $item['mobile'] }}" class="text-dark font-weight-bold text-decoration-none">
                                    {{ $item['mobile'] }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <div class="enquiry-info-right">
                            @if($followupDateFormatted !== '-')
                                <span class="mob-date-badge {{ $isTodayFollowup ? 'is-today' : ($isOverdueFollowup ? 'is-overdue' : '') }}" title="Next Follow-up">
                                    <i class="fa fa-clock-o {{ $isTodayFollowup ? 'text-warning font-weight-bold' : ($isOverdueFollowup ? 'text-danger font-weight-bold' : '') }}"></i>
                                    {{ $followupDateFormatted }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:9.5px;">No Follow-up</span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($latestNoteText))
                        <div class="enquiry-note-preview">
                            <i class="fa fa-comment-o mr-1 text-primary"></i>{{ $latestNoteText }}
                        </div>
                    @endif
                </div>

                {{-- Card Actions (100% Native Mobile App Styling) --}}
                <div class="enquiry-card-actions">
                    @if(!empty($item['mobile']))
                        <a href="tel:{{ $item['mobile'] }}" class="enquiry-act-btn btn-call" title="Call Parent">
                            <i class="fa fa-phone"></i> Call
                        </a>
                        <a href="https://wa.me/{{ $waMobile }}?text=Hello%20{{ urlencode($fullName) }},%20Regarding%20your%20admission%20enquiry%20at%20{{ urlencode($setting->name ?? 'School') }}."
                           target="_blank" class="enquiry-act-btn btn-wa" title="WhatsApp Parent">
                            <i class="fa fa-whatsapp"></i> WA
                        </a>
                    @endif

                    <button type="button" class="enquiry-act-btn btn-followup btn-open-followup"
                            data-id="{{ $item->id }}"
                            data-name="{{ $fullName }}"
                            data-mobile="{{ $item['mobile'] ?? '-' }}"
                            data-status="{{ $latestStatus }}"
                            data-followup="{{ $followupDateRaw }}">
                        <i class="fa fa-pencil-square-o"></i> Follow Up
                    </button>

                    <button type="button" class="enquiry-act-btn btn-more btn-open-card-actions"
                            data-id="{{ $item->id }}"
                            data-name="{{ $fullName }}"
                            title="More Options">
                        <i class="fa fa-ellipsis-v"></i>
                    </button>
                </div>

            </div>
        @endforeach
    @endif
</div>

{{-- Empty State (Shown when 0 cards visible) --}}
<div class="mob-empty-state" id="enquiryEmptyState" style="{{ empty($data) || count($data) === 0 ? '' : 'display:none;' }}">
    <div class="mob-empty-icon">
        <i class="fa fa-address-book-o"></i>
    </div>
    <div class="mob-empty-title">No Enquiries Found</div>
    <div class="mob-empty-desc">There are no enquiries matching your active search query or filters.</div>
    <button type="button" class="btn-empty-reset" id="btnResetAllFilters">
        <i class="fa fa-refresh"></i> Reset All Filters
    </button>
</div>

{{-- 4. Compact Fixed Bottom Pagination Bar (Matches Admission View) --}}
<div class="mob-pagination-bar" id="enquiryPaginationBar" style="{{ empty($data) || count($data) === 0 ? 'display:none;' : '' }}">
    <div class="mob-pagination-info">
        Showing <b id="mobPageStart">1</b> - <b id="mobPageEnd">{{ min(25, $totalCount) }}</b> of <b id="mobPageTotal">{{ $totalCount }}</b>
    </div>
    <div class="mob-pagination-btns">
        <select class="mob-per-page-select" id="mobPerPageSelect">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
        </select>
        <button type="button" class="mob-page-btn disabled" id="mobPrevPageBtn" title="Previous Page">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button type="button" class="mob-page-btn" id="mobNextPageBtn" title="Next Page">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</div>

{{-- 5. Filter Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="filterSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="filterBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders text-primary"></i> Filter Enquiries
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        <div class="mob-form-group">
            <label class="mob-form-label">Class</label>
            <select class="mob-form-select" id="sheetFilterClass">
                <option value="">All Classes</option>
                @if(!empty($classType))
                    @foreach($classType as $type)
                        <option value="{{ strtolower($type->name ?? '') }}">{{ $type->name ?? '' }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mob-form-group">
            <label class="mob-form-label">Enquiry Status</label>
            <select class="mob-form-select" id="sheetFilterStatus">
                <option value="">All Statuses</option>
                @if(!empty($getEnquiryStatus))
                    @foreach($getEnquiryStatus as $st)
                        <option value="{{ strtolower($st->name ?? '') }}">{{ $st->name ?? '' }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mob-form-group">
            <label class="mob-form-label">Admission Status</label>
            <select class="mob-form-select" id="sheetFilterAdmission">
                <option value="">All</option>
                <option value="admission">Admitted</option>
                <option value="non-admission">Pending / Non-Admitted</option>
            </select>
        </div>

        <div class="mob-form-grid-2">
            <div>
                <label class="mob-form-label">Reg. From Date</label>
                <input type="date" class="mob-form-input" id="sheetFilterFromDate">
            </div>
            <div>
                <label class="mob-form-label">Reg. To Date</label>
                <input type="date" class="mob-form-input" id="sheetFilterToDate">
            </div>
        </div>

        <div class="mob-form-group">
            <label class="mob-form-label">Follow Up Date</label>
            <input type="date" class="mob-form-input" id="sheetFilterFollowupDate">
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn-sheet-reset" id="btnSheetReset">Reset</button>
        <button type="button" class="btn-sheet-apply" id="btnSheetApply">Apply Filters</button>
    </div>
</div>

{{-- 6. Quick Follow-Up Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="followupSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="followupBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-pencil-square-o text-primary"></i> Add Follow-Up
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFollowupSheet">&times;</button>
    </div>
    <form id="quickFollowupForm" method="POST" action="">
        @csrf
        <div class="mob-sheet-body">
            <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:8px 10px; border-radius:4px; margin-bottom:10px;">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <strong style="font-size:12px; color:#002C54;" id="followupStudentName">-</strong>
                    <span style="font-size:11px; color:#64748b;" id="followupStudentMobile">-</span>
                </div>
            </div>

            <div class="mob-form-grid-2">
                <div>
                    <label class="mob-form-label">Follow Up Date</label>
                    <input type="date" class="mob-form-input" name="follow_up_date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label class="mob-form-label text-danger">Next Follow Up *</label>
                    <input type="date" class="mob-form-input" name="next_follow_up_date" id="followupNextDate" required>
                </div>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label text-danger">Status *</label>
                <select class="mob-form-select" name="status" id="followupStatusSelect" required>
                    @if(!empty($getEnquiryStatus))
                        @foreach($getEnquiryStatus as $st)
                            <option value="{{ $st->name ?? '' }}">{{ $st->name ?? '' }}</option>
                        @endforeach
                    @else
                        <option value="Active">Active</option>
                        <option value="Partially Closed">Partially Closed</option>
                        <option value="Missed">Missed</option>
                        <option value="Closed">Closed</option>
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Response / Interaction Mode</label>
                <select class="mob-form-select" name="response" id="followupResponseSelect">
                    <option value="Call Connected - Positive">Call Connected - Positive</option>
                    <option value="Call Connected - Follow Up Needed">Call Connected - Follow Up Needed</option>
                    <option value="Call Not Received">Call Not Received</option>
                    <option value="School / Center Visit">School / Center Visit</option>
                    <option value="WhatsApp Conversation">WhatsApp Conversation</option>
                    <option value="Admission Promised">Admission Promised</option>
                    <option value="Not Interested / Fees Issue">Not Interested / Fees Issue</option>
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Follow-up Note / Remarks</label>
                <textarea class="mob-form-textarea" name="note" id="followupNoteInput" placeholder="Add conversation details..."></textarea>
            </div>
        </div>
        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelFollowup">Cancel</button>
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Save Follow-Up
            </button>
        </div>
    </form>
</div>

{{-- 7. Enquiry Details Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="detailSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="detailBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-id-card-o text-primary"></i> Enquiry Information
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseDetailSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        <table class="detail-table">
            <tbody>
                <tr>
                    <th>Student Name</th>
                    <td id="detName">-</td>
                </tr>
                <tr>
                    <th>Class</th>
                    <td id="detClass">-</td>
                </tr>
                <tr>
                    <th>Mobile No.</th>
                    <td><a href="" id="detMobileLink" class="text-primary font-weight-bold" style="text-decoration:none;">-</a></td>
                </tr>
                <tr>
                    <th>Father's Name</th>
                    <td id="detFather">-</td>
                </tr>
                <tr>
                    <th>Mother's Name</th>
                    <td id="detMother">-</td>
                </tr>
                <tr>
                    <th>Registration Date</th>
                    <td id="detRegDate">-</td>
                </tr>
                <tr>
                    <th>Next Follow Up</th>
                    <td id="detFollowupDate">-</td>
                </tr>
                <tr>
                    <th>Current Status</th>
                    <td id="detStatus">-</td>
                </tr>
                <tr>
                    <th>Reference</th>
                    <td id="detReference">-</td>
                </tr>
                <tr>
                    <th>Latest Note</th>
                    <td id="detNote" style="text-align:left; font-style:italic;">-</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mob-sheet-footer">
        <a href="" id="detFullTimelineBtn" class="btn-sheet-reset">Full Timeline</a>
        <a href="" id="detEditBtn" class="btn-sheet-apply">Edit Enquiry</a>
    </div>
</div>

{{-- 8. Card More Actions Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="actionSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="actionBottomSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-ellipsis-h text-primary"></i> <span id="actionSheetStudentName">Options</span>
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseActionSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        <div class="mob-actions-list">
            <a href="javascript:void(0);" class="mob-action-item" id="actionBtnViewDetail">
                <i class="fa fa-eye text-primary"></i> View Full Details
            </a>
            @if($permission->edit ?? true)
                <a href="" class="mob-action-item" id="actionBtnEdit">
                    <i class="fa fa-edit text-info"></i> Edit Enquiry Record
                </a>
            @endif
            @if($permission->print ?? true)
                <a href="" class="mob-action-item" id="actionBtnPrint" target="_blank">
                    <i class="fa fa-print text-secondary"></i> Print Enquiry Slip
                </a>
            @endif
            <a href="" class="mob-action-item" id="actionBtnTimeline">
                <i class="fa fa-history text-primary"></i> Interaction History Timeline
            </a>
            @if($permission->delete ?? true)
                <button type="button" class="mob-action-item text-danger border-0" id="actionBtnDelete" style="width:100%; text-align:left;">
                    <i class="fa fa-trash-o text-danger"></i> Delete Enquiry
                </button>
            @endif
        </div>
    </div>
    <div class="mob-sheet-footer">
        <button type="button" class="btn-sheet-reset" id="btnCancelActionSheet" style="flex:1;">Close</button>
    </div>
</div>

{{-- 9. Delete Confirmation Bottom Sheet --}}
<div class="mob-sheet-backdrop" id="deleteSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="deleteBottomSheet">
    <div class="mob-sheet-header" style="background: linear-gradient(135deg, #450a0a 0%, #991b1b 100%);">
        <div class="mob-sheet-title">
            <i class="fa fa-trash-o text-white"></i> Delete Enquiry
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseDeleteSheet">&times;</button>
    </div>
    <form action="{{ url('enquiryDelete') }}" method="POST">
        @csrf
        <input type="hidden" name="delete_id" id="deleteEnquiryId">
        <div class="mob-sheet-body text-center" style="padding: 20px 16px;">
            <div style="width:44px; height:44px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 10px;">
                <i class="fa fa-trash-o"></i>
            </div>
            <h6 style="font-size:13.5px; font-weight:800; color:#1e293b; margin-bottom:4px;">Delete Enquiry Record?</h6>
            <p style="font-size:11.5px; color:#64748b; margin-bottom:0;" id="deleteEnquiryPrompt">
                Are you sure you want to permanently delete this enquiry?
            </p>
        </div>
        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelDelete">Cancel</button>
            <button type="submit" class="btn-sheet-apply" style="background:#dc2626;">
                <i class="fa fa-trash mr-1"></i> Confirm Delete
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let activeFilterType = 'all';
    let currentPage = 1;
    let perPage = 25;
    const $cards = $('.enquiry-mob-card');

    // --- 1. Real-time Live Filter Engine ---
    function filterCards() {
        const query = $('#mobEnquirySearchInput').val().toLowerCase().trim();
        const selClass = $('#sheetFilterClass').val().toLowerCase().trim();
        const selStatus = $('#sheetFilterStatus').val().toLowerCase().trim();
        const selAdmission = $('#sheetFilterAdmission').val().toLowerCase().trim();
        const fromDate = $('#sheetFilterFromDate').val();
        const toDate = $('#sheetFilterToDate').val();
        const followupDate = $('#sheetFilterFollowupDate').val();

        // Check active sheet filter flag
        const hasAdvancedFilters = selClass || selStatus || selAdmission || fromDate || toDate || followupDate;
        if (hasAdvancedFilters) {
            $('#activeFilterDot').show();
        } else {
            $('#activeFilterDot').hide();
        }

        let matchedCards = [];

        $cards.each(function() {
            const $card = $(this);
            const cardSearch = $card.attr('data-searchable') || '';
            const cardStatus = $card.attr('data-status') || '';
            const cardAdStatus = $card.attr('data-ad-status') || '';
            const cardClass = $card.attr('data-class') || '';
            const cardRegDate = $card.attr('data-reg-date') || '';
            const cardFollowupDate = $card.attr('data-followup-date') || '';
            const isToday = $card.attr('data-is-today') === '1';
            const isOverdue = $card.attr('data-is-overdue') === '1';

            let match = true;

            // Search query match
            if (query && !cardSearch.includes(query)) {
                match = false;
            }

            // Quick Chip match
            if (match && activeFilterType !== 'all') {
                if (activeFilterType === 'admission') {
                    if (cardAdStatus !== 'admission' && cardStatus !== 'admission') match = false;
                } else if (activeFilterType === 'today') {
                    if (!isToday) match = false;
                } else if (activeFilterType === 'overdue') {
                    if (!isOverdue) match = false;
                } else if (activeFilterType === 'active') {
                    if (!['active', 'new', 'follow up', 'followup', 'in progress'].includes(cardStatus)) match = false;
                } else {
                    if (cardStatus !== activeFilterType) match = false;
                }
            }

            // Advanced Sheet Filters
            if (match && selClass && !cardClass.includes(selClass)) {
                match = false;
            }
            if (match && selStatus && cardStatus !== selStatus) {
                match = false;
            }
            if (match && selAdmission) {
                if (selAdmission === 'admission' && cardAdStatus !== 'admission') match = false;
                if (selAdmission === 'non-admission' && cardAdStatus === 'admission') match = false;
            }
            if (match && fromDate && cardRegDate && cardRegDate < fromDate) {
                match = false;
            }
            if (match && toDate && cardRegDate && cardRegDate > toDate) {
                match = false;
            }
            if (match && followupDate && cardFollowupDate !== followupDate) {
                match = false;
            }

            if (match) {
                matchedCards.push($card);
            } else {
                $card.hide();
            }
        });

        // Pagination for matched cards
        const totalMatched = matchedCards.length;
        if (totalMatched === 0) {
            $('#enquiryEmptyState').show();
            $('#enquiryPaginationBar').hide();
        } else {
            $('#enquiryEmptyState').hide();
            $('#enquiryPaginationBar').show();

            const effectivePerPage = perPage === 'all' ? totalMatched : parseInt(perPage);
            const totalPages = Math.ceil(totalMatched / effectivePerPage) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIndex = (currentPage - 1) * effectivePerPage;
            const endIndex = perPage === 'all' ? totalMatched : Math.min(startIndex + effectivePerPage, totalMatched);

            matchedCards.forEach(function($c, idx) {
                if (idx >= startIndex && idx < endIndex) {
                    $c.show();
                } else {
                    $c.hide();
                }
            });

            // Update Pagination UI
            $('#mobPageStart').text(totalMatched > 0 ? startIndex + 1 : 0);
            $('#mobPageEnd').text(endIndex);
            $('#mobPageTotal').text(totalMatched);

            $('#mobPrevPageBtn').toggleClass('disabled', currentPage <= 1);
            $('#mobNextPageBtn').toggleClass('disabled', currentPage >= totalPages);
        }
    }

    // Search Input Events
    $('#mobEnquirySearchInput').on('input', function() {
        const val = $(this).val();
        $('#mobSearchClearBtn').toggle(val.length > 0);
        currentPage = 1;
        filterCards();
    });

    $('#mobSearchClearBtn').on('click', function() {
        $('#mobEnquirySearchInput').val('').trigger('input').focus();
    });

    // Quick Chip Click
    $('.mob-chip').on('click', function() {
        $('.mob-chip').removeClass('active');
        $(this).addClass('active');
        activeFilterType = $(this).attr('data-filter');
        currentPage = 1;
        filterCards();
    });

    // Pagination Events
    $('#mobPerPageSelect').on('change', function() {
        perPage = $(this).val();
        currentPage = 1;
        filterCards();
    });

    $('#mobPrevPageBtn').on('click', function() {
        if (!$(this).hasClass('disabled')) {
            currentPage--;
            filterCards();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    $('#mobNextPageBtn').on('click', function() {
        if (!$(this).hasClass('disabled')) {
            currentPage++;
            filterCards();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // Reset All Filters
    $('#btnResetAllFilters, #btnSheetReset').on('click', function() {
        $('#mobEnquirySearchInput').val('');
        $('#mobSearchClearBtn').hide();
        $('.mob-chip').removeClass('active');
        $('.mob-chip[data-filter="all"]').addClass('active');
        activeFilterType = 'all';

        $('#sheetFilterClass').val('');
        $('#sheetFilterStatus').val('');
        $('#sheetFilterAdmission').val('');
        $('#sheetFilterFromDate').val('');
        $('#sheetFilterToDate').val('');
        $('#sheetFilterFollowupDate').val('');

        closeSheet('#filterBottomSheet', '#filterSheetBackdrop');
        currentPage = 1;
        filterCards();
    });

    // --- 2. Bottom Sheet Handlers ---
    function openSheet(sheetId, backdropId) {
        $(backdropId).addClass('show');
        $(sheetId).addClass('show');
        $('body').css('overflow', 'hidden');
    }

    function closeSheet(sheetId, backdropId) {
        $(backdropId).removeClass('show');
        $(sheetId).removeClass('show');
        $('body').css('overflow', '');
    }

    // Filter Sheet
    $('#btnOpenFilterSheet').on('click', function() {
        openSheet('#filterBottomSheet', '#filterSheetBackdrop');
    });
    $('#btnCloseFilterSheet, #filterSheetBackdrop').on('click', function() {
        closeSheet('#filterBottomSheet', '#filterSheetBackdrop');
    });
    $('#btnSheetApply').on('click', function() {
        closeSheet('#filterBottomSheet', '#filterSheetBackdrop');
        currentPage = 1;
        filterCards();
    });

    // Follow-up Sheet
    $(document).on('click', '.btn-open-followup', function() {
        const id = $(this).attr('data-id');
        const name = $(this).attr('data-name');
        const mobile = $(this).attr('data-mobile');
        const status = $(this).attr('data-status');
        const followup = $(this).attr('data-followup');

        $('#quickFollowupForm').attr('action', '{{ url("enquiryFollowUpAdd") }}/' + id);
        $('#followupStudentName').text(name);
        $('#followupStudentMobile').text(mobile);
        if (status) $('#followupStatusSelect').val(status);
        if (followup) $('#followupNextDate').val(followup);

        openSheet('#followupBottomSheet', '#followupSheetBackdrop');
    });
    $('#btnCloseFollowupSheet, #followupSheetBackdrop, #btnCancelFollowup').on('click', function() {
        closeSheet('#followupBottomSheet', '#followupSheetBackdrop');
    });

    // Detail Sheet
    function showDetails(id) {
        const $card = $(`.enquiry-mob-card[data-id="${id}"]`);
        if (!$card.length) return;

        $('#detName').text($card.find('.enquiry-mob-name').text().trim());
        $('#detClass').text($card.find('.enquiry-class-badge').text().trim());
        const mobile = $card.find('.enquiry-info-left a').text().trim();
        $('#detMobileLink').text(mobile).attr('href', 'tel:' + mobile);
        $('#detFather').text($card.attr('data-father') || '-');
        $('#detMother').text($card.attr('data-mother') || '-');
        $('#detRegDate').text($card.attr('data-reg-date') || '-');
        $('#detFollowupDate').text($card.attr('data-followup-date') || '-');
        $('#detStatus').text($card.find('.mob-status-pill').first().text().trim());
        $('#detReference').text($card.find('.enquiry-ref-badge').text().trim() || '-');
        $('#detNote').text($card.find('.enquiry-note-preview').text().trim() || 'No remarks added yet.');

        $('#detFullTimelineBtn').attr('href', $card.attr('data-detail-url'));
        $('#detEditBtn').attr('href', $card.attr('data-edit-url'));

        openSheet('#detailBottomSheet', '#detailSheetBackdrop');
    }

    $(document).on('click', '.btn-open-detail', function() {
        const id = $(this).attr('data-id');
        showDetails(id);
    });
    $('#btnCloseDetailSheet, #detailSheetBackdrop').on('click', function() {
        closeSheet('#detailBottomSheet', '#detailSheetBackdrop');
    });

    // More Options Action Sheet
    let activeCardId = null;
    let activeCardName = '';

    $(document).on('click', '.btn-open-card-actions', function() {
        activeCardId = $(this).attr('data-id');
        activeCardName = $(this).attr('data-name');
        const $card = $(`.enquiry-mob-card[data-id="${activeCardId}"]`);
        if (!$card.length) return;

        $('#actionSheetStudentName').text(activeCardName);
        $('#actionBtnEdit').attr('href', $card.attr('data-edit-url'));
        $('#actionBtnPrint').attr('href', $card.attr('data-print-url'));
        $('#actionBtnTimeline').attr('href', $card.attr('data-detail-url'));

        openSheet('#actionBottomSheet', '#actionSheetBackdrop');
    });

    $('#btnCloseActionSheet, #btnCancelActionSheet, #actionSheetBackdrop').on('click', function() {
        closeSheet('#actionBottomSheet', '#actionSheetBackdrop');
    });

    $('#actionBtnViewDetail').on('click', function() {
        closeSheet('#actionBottomSheet', '#actionSheetBackdrop');
        if (activeCardId) {
            setTimeout(function() {
                showDetails(activeCardId);
            }, 150);
        }
    });

    // Delete Bottom Sheet
    $('#actionBtnDelete').on('click', function() {
        closeSheet('#actionBottomSheet', '#actionSheetBackdrop');
        if (activeCardId) {
            setTimeout(function() {
                $('#deleteEnquiryId').val(activeCardId);
                $('#deleteEnquiryPrompt').html(`Are you sure you want to permanently delete enquiry for <strong>${activeCardName}</strong>?`);
                openSheet('#deleteBottomSheet', '#deleteSheetBackdrop');
            }, 150);
        }
    });

    $('#btnCloseDeleteSheet, #btnCancelDelete, #deleteSheetBackdrop').on('click', function() {
        closeSheet('#deleteBottomSheet', '#deleteSheetBackdrop');
    });

    // Initial Filter Run
    filterCards();
});
</script>
@endsection

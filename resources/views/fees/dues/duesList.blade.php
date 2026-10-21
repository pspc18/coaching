@php
    $classType = $classType ?? Helper::classType();
    $getSession = $getSession ?? Helper::getSession();
    $feesSetting = $feesSetting ?? \App\Models\FeesSetting::getSetting(Session::get('branch_id'), Session::get('session_id'));
    $search = $search ?? [];
    $feesGroups = $feesGroups ?? collect();
    $groupTotals = $groupTotals ?? [];
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $lastPage = $lastPage ?? 1;
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $stats = $stats ?? [
        'total_students'  => $totalCount,
        'total_assigned'  => 0,
        'total_discount'  => 0,
        'total_collected' => 0,
        'total_pending'   => 0,
    ];
    $currentStatusFilter = isset($search['status']) && $search['status'] !== '' ? (string) $search['status'] : '1';
@endphp

@extends('layout.app')

@section('title', 'Fees Dues & Reminder Register')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP MODERN THEME - FEES DUES & REMINDER REGISTER
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
    display: flex;
    align-items: center;
    gap: 6px;
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

/* Policy Pills in Hero */
.hero-policy-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 1px 6px;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0;
}
.policy-pill-active {
    background: rgba(16, 185, 129, 0.2);
    border: 1px solid rgba(52, 211, 153, 0.45);
    color: #a7f3d0;
}
.policy-pill-overdue {
    background: rgba(239, 68, 68, 0.22);
    border: 1px solid rgba(248, 113, 113, 0.45);
    color: #fecaca;
}
.policy-pill-off {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #cbd5e1;
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
.dash-btn-whatsapp {
    background: #16a34a;
    color: #ffffff;
    border-color: #22c55e;
}
.dash-btn-whatsapp:hover {
    background: #15803d;
    color: #ffffff;
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
.stat-color-discount .ca-stat-icon { background: #fef3c7; color: #d97706; }
.stat-color-collected .ca-stat-icon { background: #dcfce7; color: #16a34a; }
.stat-color-pending .ca-stat-icon { background: #fee2e2; color: #dc2626; }

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
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
    width: 100%;
}

/* Table Design */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    min-width: 1050px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

.col-sr       { width: 55px; min-width: 55px; }
.col-adm      { width: 85px; min-width: 80px; }
.col-class    { width: 105px; min-width: 95px; }
.col-name     { width: 145px; min-width: 130px; }
.col-father   { width: 135px; min-width: 120px; }
.col-mobile   { width: 105px; min-width: 95px; }
.col-gender   { width: 95px; min-width: 85px; }
.col-status   { width: 90px; min-width: 85px; }
.col-group    { width: 95px; min-width: 85px; }
.col-assigned { width: 95px; min-width: 85px; }
.col-paid     { width: 90px; min-width: 80px; }
.col-pending  { width: 100px; min-width: 90px; }
.col-action   { width: 70px; min-width: 68px; }

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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
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
.badge-cat {
    font-size: 9.5px;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 4px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    font-weight: 600;
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

.dues-status-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 2px;
    font-weight: 600;
    display: inline-block;
}
.badge-status-continue {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-status-discontinue {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
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
    font-size: 11px;
    transition: all .15s;
    text-decoration: none !important;
}
.ca-action-btn.btn-collect { color: #0284c7; }
.ca-action-btn.btn-collect:hover { background: #0284c7; color: #fff; border-color: #0284c7; }
.ca-action-btn.btn-remind { color: #16a34a; }
.ca-action-btn.btn-remind:hover { background: #16a34a; color: #fff; border-color: #16a34a; }

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

#confirmSendMessageModal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
}
#confirmSendMessageModal .modal-title {
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

                {{-- 1. Top Hero & Quick Policy Banner --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <div class="admission-kicker">
                            <span><i class="fa fa-bell mr-1"></i> Fee Dues & Automated Reminder System</span>
                            <span class="hero-policy-pill {{ ($feesSetting->due_reminder_enabled ?? 1) ? 'policy-pill-active' : 'policy-pill-off' }}">
                                <i class="fa fa-clock-o"></i> Reminder: {{ ($feesSetting->due_reminder_enabled ?? 1) ? ($feesSetting->due_reminder_days_before ?? 5) . 'd prior' : 'Off' }}
                            </span>
                            <span class="hero-policy-pill {{ ($feesSetting->overdue_notice_enabled ?? 1) ? 'policy-pill-overdue' : 'policy-pill-off' }}">
                                <i class="fa fa-exclamation-circle"></i> Overdue Alert: {{ ($feesSetting->overdue_notice_enabled ?? 1) ? 'Active' : 'Off' }}
                            </span>
                        </div>
                        <h1 class="admission-title">{{ __('Fees Dues List') }}</h1>
                        <p class="admission-subtitle">Real-time student fee defaulters, head-wise pending breakdown & bulk WhatsApp/In-App fee reminders</p>
                    </div>
                    <div class="admission-hero-actions">
                        {{-- Quick Due Mode Chips --}}
                        <button type="button" class="dash-chip-btn {{ empty($search['due_mode']) ? 'active' : '' }}" data-due-mode="">All Dues</button>
                        <button type="button" class="dash-chip-btn {{ ($search['due_mode'] ?? '') === 'overdue' ? 'active' : '' }}" data-due-mode="overdue">Overdue Only</button>
                        <button type="button" class="dash-chip-btn {{ ($search['due_mode'] ?? '') === 'upcoming' ? 'active' : '' }}" data-due-mode="upcoming">Upcoming ({{ $feesSetting->due_reminder_days_before ?? 5 }}d)</button>

                        {{-- Installment Due Date Range --}}
                        <div class="hero-date-box ml-1">
                            <span class="hero-date-lbl">Due From</span>
                            <input type="date" class="hero-date-input" id="hero-starting" value="{{ $search['starting'] ?? '' }}" title="Installment Due Date From">
                        </div>
                        <div class="hero-date-box">
                            <span class="hero-date-lbl">Due To</span>
                            <input type="date" class="hero-date-input" id="hero-ending" value="{{ $search['ending'] ?? '' }}" title="Installment Due Date To">
                        </div>

                        <button type="button" class="dash-btn dash-btn-whatsapp ml-1" id="btn-send-bulk-reminder" title="Send Fee Reminder to Selected Students">
                            <i class="fa fa-whatsapp mr-1"></i> Send Reminder (<span id="selected-count-badge">0</span>)
                        </button>
                        <a href="{{ url('fees/settings') }}#tab-alerts" class="dash-btn dash-btn-outline" title="Configure Due Reminder Settings">
                            <i class="fa fa-cogs mr-1"></i> Settings
                        </a>
                        <button type="button" class="dash-btn dash-btn-outline" id="btn-reset-hero" title="Reset All Filters">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </button>
                        <a href="{{ url('fee_dashboard') }}" class="dash-btn dash-btn-light" title="Back to Fee Dashboard">
                            <i class="fa fa-arrow-left mr-1"></i> {{ __('common.Back') }}
                        </a>
                    </div>
                </div>

                {{-- 2. Summary Statistics Row (5 KPI Cards) --}}
                <div class="ca-stats-strip">
                    <div class="ca-stat-card stat-color-students">
                        <div class="ca-stat-icon"><i class="fa fa-users"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Defaulter Students</div>
                            <div class="ca-stat-value" id="stat-total-students">{{ number_format($stats['total_students'] ?? $totalCount) }}</div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-assigned">
                        <div class="ca-stat-icon"><i class="fa fa-inr"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Assigned Fee</div>
                            <div class="ca-stat-value">₹<span id="stat-total-assigned">{{ number_format($stats['total_assigned'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-discount">
                        <div class="ca-stat-icon"><i class="fa fa-tag"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Discount</div>
                            <div class="ca-stat-value">₹<span id="stat-total-discount">{{ number_format($stats['total_discount'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-collected">
                        <div class="ca-stat-icon"><i class="fa fa-check-circle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Collected</div>
                            <div class="ca-stat-value">₹<span id="stat-total-collected">{{ number_format($stats['total_collected'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="ca-stat-card stat-color-pending">
                        <div class="ca-stat-icon"><i class="fa fa-exclamation-triangle"></i></div>
                        <div class="ca-stat-content">
                            <div class="ca-stat-label">Total Pending Dues</div>
                            <div class="ca-stat-value" style="color: #dc2626;">₹<span id="stat-total-pending">{{ number_format($stats['total_pending'] ?? 0, 2) }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- 3. Main Dark Navy Table Card with Dual-Row Sticky Header --}}
                <form id="remainderForm" action="{{ url('whatsappSendFeesRemainder') }}" method="POST" class="d-flex flex-column flex-grow-1" style="min-height: 0; margin: 0;">
                    @csrf
                    <div class="dash-card admission-table-card">
                        <div class="dash-card-header d-flex align-items-center justify-content-between flex-wrap" style="gap: 8px;">
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <h3 class="dash-card-title"><i class="fa fa-address-book-o text-info mr-1"></i> Student Fee Defaulters & Dues Register</h3>
                                <span class="text-light" style="font-size: 10.5px; opacity: 0.75;">(Select a Class to view fee-group column breakdown)</span>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 8px;">
                                <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Students with Dues</span>
                            </div>
                        </div>

                        {{-- Scrollable Table Container --}}
                        <div class="table-scroll-container">
                            {{-- Loading Overlay --}}
                            <div class="ca-table-loading-overlay" id="ca-loading-overlay">
                                <div class="ca-table-loading-spinner">
                                    <i class="fa fa-spinner fa-spin"></i> Loading Dues Data...
                                </div>
                            </div>

                            <table class="dash-table" id="dues-grid-table">
                                <thead>
                                    {{-- Row 1: Column Headers --}}
                                    <tr class="header-titles-row">
                                        <th class="text-center col-sr">
                                            <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                                <input type="checkbox" id="select-all-rows" title="Select / Deselect All on Page" style="cursor: pointer;">
                                                <span>#</span>
                                            </div>
                                        </th>
                                        <th class="col-adm">{{ __('student.Admission No.') }}</th>
                                        <th class="col-class">Class</th>
                                        <th class="col-name">{{ __('Student Name') }}</th>
                                        <th class="col-father">{{ __('messages.Fathers Name') }}</th>
                                        <th class="col-mobile">{{ __('Mobile') }}</th>
                                        <th class="text-center col-gender">Gender / Cat</th>
                                        <th class="text-center col-status" id="th-status-title">{{ __('Status') }}</th>
                                        @if(!empty($feesGroups) && count($feesGroups) > 0)
                                            @foreach($feesGroups as $fg)
                                                <th class="text-right col-group dynamic-group-th" title="{{ $fg->group_name ?? '' }}">{{ $fg->group_name ?? '' }}</th>
                                            @endforeach
                                        @endif
                                        <th class="text-right col-assigned">Assigned</th>
                                        <th class="text-right col-paid">Paid</th>
                                        <th class="text-right col-pending">Pending Due</th>
                                        <th class="text-center col-action">Action</th>
                                    </tr>

                                    {{-- Row 2: In-Column Excel Filters --}}
                                    <tr class="excel-filter-row">
                                        {{-- 1. Clear Filters --}}
                                        <th class="text-center col-sr">
                                            <button type="button" class="btn-clear-filters" title="Clear all filters">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </th>

                                        {{-- 2. Admission No Filter --}}
                                        <th class="col-adm">
                                            <input type="text" class="excel-col-filter" id="filter-admission-no" placeholder="Adm No..." value="{{ $search['admissionNo'] ?? '' }}">
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
                                            <input type="text" class="excel-col-filter" id="filter-name" placeholder="Student Name..." value="{{ $search['name'] ?? '' }}">
                                        </th>

                                        {{-- 5. Father Name Filter --}}
                                        <th class="col-father">
                                            <input type="text" class="excel-col-filter" id="filter-father" placeholder="Father Name..." value="{{ $search['father_name'] ?? '' }}">
                                        </th>

                                        {{-- 6. Mobile Filter --}}
                                        <th class="col-mobile">
                                            <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Mobile..." value="{{ $search['mobile'] ?? '' }}">
                                        </th>

                                        {{-- 7. Gender / Category Filter --}}
                                        <th class="col-gender">
                                            <select class="excel-col-filter" id="filter-gender" title="Gender">
                                                <option value="">All</option>
                                                <option value="1" {{ ($search['gender_id'] ?? '') == '1' ? 'selected' : '' }}>Male</option>
                                                <option value="2" {{ ($search['gender_id'] ?? '') == '2' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </th>

                                        {{-- 8. Status Filter --}}
                                        <th class="col-status" id="th-status-filter">
                                            <select class="excel-col-filter" id="filter-status" title="Student Status">
                                                <option value="1" {{ $currentStatusFilter === '1' ? 'selected' : '' }}>Continue</option>
                                                <option value="0" {{ $currentStatusFilter === '0' ? 'selected' : '' }}>Discontinue</option>
                                                <option value="all" {{ $currentStatusFilter === 'all' ? 'selected' : '' }}>All</option>
                                            </select>
                                        </th>

                                        @if(!empty($feesGroups) && count($feesGroups) > 0)
                                            @foreach($feesGroups as $fg)
                                                <th class="text-right text-muted col-group dynamic-group-th">-</th>
                                            @endforeach
                                        @endif

                                        {{-- 9. Assigned Placeholder --}}
                                        <th class="text-right text-muted col-assigned">-</th>

                                        {{-- 10. Paid Placeholder --}}
                                        <th class="text-right text-muted col-paid">-</th>

                                        {{-- 11. Pending Placeholder --}}
                                        <th class="text-right text-muted col-pending">-</th>

                                        {{-- 12. Action Reset --}}
                                        <th class="text-center col-action">
                                            <button type="button" class="btn-reset-filters" title="Reset All Filters">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>

                                {{-- Dynamic Table Body --}}
                                <tbody id="dues-table-body">
                                    @include('fees.dues.dues_rows')
                                </tbody>
                            </table>
                        </div>

                        {{-- 4. Pinned Summary Totals Bar --}}
                        <div class="ca-pinned-summary-bar">
                            <div class="d-flex align-items-center justify-content-between px-3 py-1 flex-wrap" style="min-height: 32px; gap: 6px;">
                                <div class="d-flex align-items-center font-weight-bold text-white" style="font-size: 11px;">
                                    <i class="fa fa-calculator text-info mr-2"></i>
                                    <span>Summary Totals (Filtered):</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                    <div id="dynamic-group-totals-pills" class="d-inline-flex align-items-center flex-wrap" style="gap: 6px;">
                                        @if(!empty($feesGroups) && count($feesGroups) > 0)
                                            @foreach($feesGroups as $fg)
                                                <div class="ca-summary-pill">
                                                    <span class="pill-lbl">{{ $fg->group_name }}:</span>
                                                    <span class="pill-val text-warning">₹{{ number_format((float) ($groupTotals[$fg->id] ?? 0), 0) }}</span>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="ca-summary-pill">
                                        <span class="pill-lbl">Assigned:</span>
                                        <span class="pill-val text-white" id="foot-total-assigned">₹{{ number_format($stats['total_assigned'] ?? 0, 2) }}</span>
                                    </div>
                                    <div class="ca-summary-pill">
                                        <span class="pill-lbl">Collected:</span>
                                        <span class="pill-val text-success" id="foot-total-collected">₹{{ number_format($stats['total_collected'] ?? 0, 2) }}</span>
                                    </div>
                                    <div class="ca-summary-pill pill-net">
                                        <span class="pill-lbl" style="color: rgba(255,255,255,.9);">Pending Balance:</span>
                                        <span class="pill-val text-white font-weight-bold" id="foot-total-pending">₹{{ number_format($stats['total_pending'] ?? 0, 2) }}</span>
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
                </form>

            </div>
        </div>
    </section>
</div>

{{-- Confirmation Modal for Sending Fee Reminder --}}
<div class="modal fade" id="confirmSendMessageModal" tabindex="-1" role="dialog" aria-labelledby="confirmSendMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center justify-content-between">
                <h5 class="modal-title mb-0" id="confirmSendMessageModalLabel"><i class="fa fa-whatsapp mr-1"></i> Send Fee Reminder</h5>
                <button type="button" class="close text-white modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="font-size: 12.5px;">
                <p class="mb-2" id="confirm-reminder-text">Are you sure you want to send a fee reminder to the selected students?</p>
                <div class="p-2 rounded" style="background: #f8fafc; border: 1px solid #cbd5e1; font-size: 11.5px;">
                    <div><i class="fa fa-check-circle text-success mr-1"></i> Sends WhatsApp reminder (if WhatsApp service is active)</div>
                    <div class="mt-1"><i class="fa fa-bell text-primary mr-1"></i> Sends In-App Student Portal fee due notification</div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm" id="confirmSendMessage">
                    <i class="fa fa-paper-plane mr-1"></i> Send Now
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    const ajaxUrl = "{{ url('feesRemainderCron') }}";
    const sendReminderUrl = "{{ url('whatsappSendFeesRemainder') }}";
    let currentPage = {{ (int) $currentPage }};
    let lastPage = {{ (int) $lastPage }};
    let currentPerPage = "{{ $perPage }}";
    let currentDueMode = "{{ $search['due_mode'] ?? '' }}";
    let filterTimer = null;
    let activeAjax = null;

    function formatMoney(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateSelectedCount() {
        const count = $('.checkbox_id:checked').length;
        $('#selected-count-badge').text(count);
        const totalOnPage = $('.checkbox_id').length;
        $('#select-all-rows').prop('checked', totalOnPage > 0 && count === totalOnPage);
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

    function syncDynamicFeeGroupHeaders(feeGroups, groupTotals) {
        $('.dynamic-group-th').remove();
        const pillsContainer = $('#dynamic-group-totals-pills');
        pillsContainer.empty();

        if (!feeGroups || !feeGroups.length) return;

        let titleHtml = '';
        let filterHtml = '';

        feeGroups.forEach(function(fg) {
            const safeName = $('<div>').text(fg.group_name || '').html();
            titleHtml += `<th class="text-right col-group dynamic-group-th" title="${safeName}">${safeName}</th>`;
            filterHtml += `<th class="text-right text-muted col-group dynamic-group-th">-</th>`;

            const amt = groupTotals && groupTotals[fg.id] ? parseFloat(groupTotals[fg.id]) : 0;
            pillsContainer.append(`
                <div class="ca-summary-pill">
                    <span class="pill-lbl">${safeName}:</span>
                    <span class="pill-val text-warning">₹${amt.toLocaleString('en-IN', { maximumFractionDigits: 0 })}</span>
                </div>
            `);
        });

        $('#th-status-title').after(titleHtml);
        $('#th-status-filter').after(filterHtml);
    }

    function getFilterParams(targetPage = 1) {
        return {
            ajax: 1,
            page: targetPage,
            per_page: currentPerPage,
            admissionNo: $.trim($('#filter-admission-no').val()),
            class_type_id: $('#filter-class').val(),
            name: $.trim($('#filter-name').val()),
            father_name: $.trim($('#filter-father').val()),
            mobile: $.trim($('#filter-mobile').val()),
            gender_id: $('#filter-gender').val(),
            status: $('#filter-status').val(),
            due_mode: currentDueMode,
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

        activeAjax = $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: params,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json',
            success: function(res) {
                if (res && res.status) {
                    syncDynamicFeeGroupHeaders(res.fee_groups || [], res.group_totals || {});
                    $('#dues-table-body').html(res.html);
                    $('#select-all-rows').prop('checked', false);
                    updateSelectedCount();

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
                        $('#stat-total-students').text(parseInt(s.total_students || 0).toLocaleString('en-IN'));
                        $('#stat-total-assigned').text(formatMoney(s.total_assigned));
                        $('#stat-total-discount').text(formatMoney(s.total_discount));
                        $('#stat-total-collected').text(formatMoney(s.total_collected));
                        $('#stat-total-pending').text(formatMoney(s.total_pending));

                        $('#foot-total-assigned').text('₹' + formatMoney(s.total_assigned));
                        $('#foot-total-collected').text('₹' + formatMoney(s.total_collected));
                        $('#foot-total-pending').text('₹' + formatMoney(s.total_pending));
                    }
                }
            },
            error: function(xhr, status, error) {
                if (status !== 'abort') {
                    console.error('Dues AJAX Error:', error);
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

    $('.dash-chip-btn[data-due-mode]').on('click', function() {
        $('.dash-chip-btn[data-due-mode]').removeClass('active');
        $(this).addClass('active');
        currentDueMode = $(this).data('due-mode') || '';
        fetchData(1);
    });

    $('#hero-starting, #hero-ending').on('change', function() {
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
        $('#filter-class, #filter-gender').val('');
        $('#filter-status').val('1');
        $('#hero-starting, #hero-ending').val('');
        currentDueMode = '';
        $('.dash-chip-btn[data-due-mode]').removeClass('active');
        $('.dash-chip-btn[data-due-mode=""]').addClass('active');
        fetchData(1);
    });

    // Checkbox selection handling
    $('#select-all-rows').on('change', function() {
        $('.checkbox_id').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });

    $(document).on('change', '.checkbox_id', function() {
        updateSelectedCount();
    });

    // Bulk Send Reminder button
    $('#btn-send-bulk-reminder').on('click', function() {
        const selected = $('.checkbox_id:checked');
        if (selected.length === 0) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Please select at least one student to send a fee reminder.');
            } else {
                alert('Please select at least one student to send a fee reminder.');
            }
            return;
        }
        $('#confirm-reminder-text').html(`Are you sure you want to send a fee reminder to <strong>${selected.length} selected student(s)</strong>?`);
        $('#confirmSendMessageModal').modal('show');
    });

    // Single student reminder button
    $(document).on('click', '.btn-single-remind', function() {
        const stuId = $(this).data('id');
        const stuName = $(this).data('name') || 'Student';
        $('.checkbox_id').prop('checked', false);
        $(`.checkbox_id[value="${stuId}"]`).prop('checked', true);
        updateSelectedCount();
        $('#confirm-reminder-text').html(`Send fee reminder to <strong>${stuName}</strong>?`);
        $('#confirmSendMessageModal').modal('show');
    });

    $(document).on('click', '.modal-close-btn', function() {
        $('#confirmSendMessageModal').modal('hide');
    });

    // Submit Reminder via AJAX
    $('#confirmSendMessage').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Sending...');

        $.ajax({
            url: sendReminderUrl,
            type: 'POST',
            data: $('#remainderForm').serialize() + '&ajax=1',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json',
            success: function(res) {
                $('#confirmSendMessageModal').modal('hide');
                if (res && res.status) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message || 'Fee reminders queued successfully!');
                    } else {
                        alert(res.message || 'Fee reminders queued successfully!');
                    }
                    $('.checkbox_id, #select-all-rows').prop('checked', false);
                    updateSelectedCount();
                } else {
                    alert(res.message || 'Failed to send fee reminders.');
                }
            },
            error: function() {
                $('#confirmSendMessageModal').modal('hide');
                alert('An error occurred while sending fee reminders.');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Send Now');
            }
        });
    });
});
</script>
@endsection

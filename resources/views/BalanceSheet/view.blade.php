@extends('layout.app')

@php
    $activeTab = $data['active_tab'] ?? request('active_tab', 'dashboard_summary');
    $counts = $data['counts'] ?? [];
    
    $totalRev = $data['totalRevenue'] ?? 0;
    $cashPct = $totalRev > 0 ? round((($data['cashTotal'] ?? 0) / $totalRev) * 100, 1) : 0;
    $upiPct = $totalRev > 0 ? round((($data['upiTotal'] ?? 0) / $totalRev) * 100, 1) : 0;
    $chequePct = $totalRev > 0 ? round((($data['chequeTotal'] ?? 0) / $totalRev) * 100, 1) : 0;
    $netBankPct = $totalRev > 0 ? round((($data['netBankingTotal'] ?? 0) / $totalRev) * 100, 1) : 0;
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - BALANCE SHEET & FINANCIAL ANALYTICS
   Signature Dark Navy Theme (#002C54 to #0f3460)
   Exact match with User View, Student Admission, and System Guidelines:
   - Sharp 2px border radius
   - Segoe UI / System typography
   - Dark Navy thead (#002C54) & In-Column Excel Filter row (#08335c)
   - Equal-height KPI & metric cards
   - Compact 29px inputs with #cbd5e1 border
   - Pinned bottom pagination toolbar
   ========================================================================== */

.balance-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.balance-page-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner (Arise ERP Signature Dark Navy Theme) */
.balance-hero {
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
    margin-bottom: 6px;
}
.balance-hero-text {
    display: flex;
    flex-direction: column;
}
.balance-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.balance-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.balance-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges in Hero */
.balance-hero-stats {
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
    text-decoration: none !important;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 11.5px;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #a7f3d0;
}
.hero-stat-badge.badge-blue {
    background: rgba(56, 189, 248, 0.22);
    border-color: rgba(56, 189, 248, 0.45);
    color: #bae6fd;
}
.hero-stat-badge.badge-red {
    background: rgba(239, 68, 68, 0.22);
    border-color: rgba(239, 68, 68, 0.45);
    color: #fca5a5;
}

/* Hero Action Buttons */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
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
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #e2e8f0 !important;
    color: #001f3d !important;
    border-color: #cbd5e1 !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.22) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}

/* Filter Card & Preset Toolbar */
.balance-filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.preset-chips-row {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 6px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.preset-chip {
    padding: 2px 8px;
    font-size: 10.5px;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    line-height: 1.4;
}
.preset-chip:hover {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}
.preset-chip.active {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}

/* Compact Inputs & Groups */
.input-action-group {
    display: flex;
    align-items: stretch;
    position: relative;
    width: 100%;
    flex-wrap: nowrap !important;
}
.input-action-group .form-control {
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 0 2px 2px 0;
    padding: 3px 8px;
}
.input-action-group .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}
.input-action-prepend {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-right: none;
    color: #002C54;
    font-size: 11px;
    font-weight: 700;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-top-left-radius: 2px;
    border-bottom-left-radius: 2px;
    height: 29px;
    flex-shrink: 0;
    min-width: 32px;
}

.btn-filter-search {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3d;
    height: 29px;
    padding: 0 14px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    transition: all .15s ease-in-out;
}
.btn-filter-search:hover {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-filter-reset {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
}
.btn-filter-reset:hover {
    background: #e2e8f0 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
}

/* Modern Clean ERP Navigation Tabs - Pill Buttons (Matching Design) */
.balance-nav-tabs {
    background: transparent;
    border: none;
    padding: 0;
    margin-bottom: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.balance-nav-tabs .nav-item {
    margin-bottom: 0;
}
.balance-nav-tabs .nav-link {
    border: 1px solid #94a3b8;
    color: #1e293b;
    background: #ffffff;
    padding: 5px 14px;
    border-radius: 2px;
    font-size: 11.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .15s ease-in-out;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    text-decoration: none !important;
}
.balance-nav-tabs .nav-link:hover {
    background: #f1f5f9;
    color: #002C54;
    border-color: #64748b;
}
.balance-nav-tabs .nav-link.active {
    background: #1e293b !important;
    color: #ffffff !important;
    border-color: #0f172a !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
}
.balance-nav-tabs .nav-link.active i {
    color: #ffffff !important;
}
.balance-nav-tabs .nav-link.active .badge,
.balance-nav-tabs .nav-link.active .tab-count-badge {
    background: #ffffff !important;
    color: #1e293b !important;
}

/* Tab Pane Display Control */
.tab-content > .tab-pane {
    display: none !important;
}
.tab-content > .tab-pane.active,
.tab-content > .tab-pane.show.active {
    display: block !important;
}

/* Flex Gap & Spacing Utilities (Cross-Browser Compatibility) */
.gap-1 { gap: 4px !important; }
.gap-2 { gap: 8px !important; }
.gap-3 { gap: 12px !important; }

/* Uniform Badges & Perfect Spacing */
.brand-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    line-height: 22px;
    padding: 0 8px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    letter-spacing: 0.02em;
    vertical-align: middle;
    margin: 0 2px;
}
.tab-count-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 18px;
    line-height: 18px;
    padding: 0 6px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 2px;
    background: #e2e8f0;
    color: #334155;
    margin-left: 4px;
}

/* KPI / Metric Cards (Equal Height Grid) */
.section-banner-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    margin: 8px 0 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 3px solid #002C54;
    padding-left: 8px;
}
.kpi-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 6px;
    margin-bottom: 8px;
}
.kpi-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform .15s ease, box-shadow .15s ease;
    border-left: 3px solid #002C54;
}
.kpi-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0,44,84,.08);
}
.kpi-card.kpi-green { border-left-color: #10b981; }
.kpi-card.kpi-blue { border-left-color: #0284c7; }
.kpi-card.kpi-amber { border-left-color: #f59e0b; }
.kpi-card.kpi-purple { border-left-color: #8b5cf6; }
.kpi-card.kpi-red { border-left-color: #ef4444; }

.kpi-info {
    display: flex;
    flex-direction: column;
}
.kpi-label {
    font-size: 10.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 2px;
}
.kpi-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1.1;
    color: #0f172a;
    margin-bottom: 2px;
}
.kpi-subtext {
    font-size: 9.5px;
    color: #94a3b8;
    font-weight: 500;
}
.kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.kpi-green .kpi-icon-box { background: #dcfce7; color: #15803d; }
.kpi-blue .kpi-icon-box { background: #e0f2fe; color: #0369a1; }
.kpi-amber .kpi-icon-box { background: #fef3c7; color: #b45309; }
.kpi-purple .kpi-icon-box { background: #f3e8ff; color: #7e22ce; }
.kpi-red .kpi-icon-box { background: #fee2e2; color: #b91c1c; }

/* Financial Visual Breakdown Bar */
.dist-progress-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.dist-progress-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.dist-progress-title {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.dist-multi-bar {
    height: 12px;
    border-radius: 2px;
    background: #f1f5f9;
    overflow: hidden;
    display: flex;
    margin-bottom: 8px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,.05);
}
.dist-bar-item {
    height: 100%;
    transition: width .4s ease;
}
.dist-legend-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    font-size: 10.5px;
}
.dist-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #334155;
    font-weight: 600;
}
.dist-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
    display: inline-block;
}

/* ==========================================================================
   TABLE CARD, THEAD, EXCEL-FILTERS & PAGINATION
   Exact Theme Alignment with User View & Admission Modules
   ========================================================================== */

.balance-table-card {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 8px;
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}

/* Scrollable Table Viewport - Auto Viewport Fit (Max Remaining Height) & Scrollable */
.table-scroll-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: calc(100vh - 220px);
    min-height: 280px;
    position: relative;
    background: #ffffff;
}

/* Empty State / Not Found Data View */
.empty-data-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 16px;
    background: #f8fafc;
    color: #64748b;
    border-radius: 2px;
}
.empty-data-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 10px;
}
.empty-data-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
}
.empty-data-subtitle {
    font-size: 11px;
    color: #94a3b8;
}

/* Table Design */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    margin-bottom: 0;
    background: #ffffff;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Header Titles Row - Arise ERP Dark Navy (#002C54) */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54 !important;
    color: #ffffff !important;
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

/* Excel Filter Row - Dark Navy Palette (#08335c) */
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c !important;
    color: #ffffff !important;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
}

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 10.5px;
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

/* Reset / Clear Button */
.btn-reset-filters {
    height: 26px;
    padding: 0 8px;
    font-size: 10.5px;
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

/* Table Body Rows - Clean Light Alternating */
.dash-table tbody td {
    padding: 6px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    background: #ffffff;
}
.dash-table tbody tr:nth-child(odd) td {
    background: #ffffff;
}
.dash-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.dash-table tbody tr:hover td {
    background: #f1f5f9 !important;
}

/* Footer Total Row */
.balance-table-total-row td {
    background: #f1f5f9 !important;
    font-weight: 700;
    padding: 7px 8px;
    border-top: 2px solid #cbd5e1;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
}

/* Bottom Pagination Bar */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 36px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 5px;
}
.rows-per-page-selector label {
    margin: 0;
    font-size: 10.5px;
    color: #cbd5e1;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 4px;
    font-size: 10.5px;
    outline: none;
    cursor: pointer;
    height: 24px;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 2px;
}
.page-btn {
    min-width: 24px;
    height: 22px;
    padding: 0 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 10.5px;
    transition: all .15s;
    user-select: none;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}
.page-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}
.page-indicator {
    font-size: 10.5px;
    color: #cbd5e1;
    padding: 0 4px;
}

</style>
@endsection

@section('content')
<div class="content-wrapper balance-page-wrapper">
    <div class="container-fluid p-0">

        {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
        <div class="balance-hero">
            <div class="balance-hero-text">
                <span class="balance-kicker"><i class="fa fa-line-chart mr-1"></i> Financial Accounting &amp; Revenue Analytics</span>
                <h1 class="balance-title">
                    <i class="fa fa-balance-scale text-info"></i> Balance Sheet &amp; Financial Overview
                </h1>
                <p class="balance-subtitle">Comprehensive fee collection breakdown by payment modes, expense records, and cash flow</p>
            </div>

            <div class="balance-hero-stats">
                <div class="hero-stat-badge {{ ($data['totalProfit'] ?? 0) >= 0 ? 'badge-green' : 'badge-red' }}">
                    <i class="fa fa-calculator mr-1"></i> Net Balance: <b>₹ {{ number_format($data['totalProfit'] ?? 0, 2) }}</b>
                </div>
                <div class="hero-stat-badge badge-blue">
                    <i class="fa fa-arrow-down mr-1"></i> Revenue: <b>₹ {{ number_format($data['totalRevenue'] ?? 0, 2) }}</b>
                </div>
                <div class="hero-stat-badge badge-red">
                    <i class="fa fa-arrow-up mr-1"></i> Expenses: <b>₹ {{ number_format($data['expenseTotal'] ?? 0, 2) }}</b>
                </div>
            </div>
        </div>

        {{-- 2. Filter Bar & Date Presets --}}
        <div class="balance-filter-card">
            <div class="preset-chips-row">
                <span class="text-muted mr-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">
                    <i class="fa fa-clock-o mr-1"></i> Quick Presets:
                </span>
                <button type="button" class="preset-chip" onclick="setDatePreset('today')">Today</button>
                <button type="button" class="preset-chip" onclick="setDatePreset('yesterday')">Yesterday</button>
                <button type="button" class="preset-chip" onclick="setDatePreset('this_week')">This Week</button>
                <button type="button" class="preset-chip" onclick="setDatePreset('this_month')">This Month</button>
                <button type="button" class="preset-chip" onclick="setDatePreset('this_session')">This Session</button>
                <button type="button" class="preset-chip" onclick="setDatePreset('all')">All Time</button>
            </div>

            <form method="GET" action="{{ url('view_balance_sheet') }}" id="balanceFilterForm">
                <input type="hidden" name="active_tab" id="filter_active_tab" value="{{ $activeTab }}">

                <div class="row g-2 align-items-end">
                    <div class="col-md-4 col-sm-6">
                        <div class="input-action-group">
                            <span class="input-action-prepend"><i class="fa fa-calendar-check-o"></i> From</span>
                            <input type="date" 
                                   name="from_date" 
                                   id="from_date" 
                                   class="form-control" 
                                   value="{{ request('from_date') }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="input-action-group">
                            <span class="input-action-prepend"><i class="fa fa-calendar-o"></i> To</span>
                            <input type="date" 
                                   name="to_date" 
                                   id="to_date" 
                                   class="form-control" 
                                   value="{{ request('to_date') }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn-filter-search flex-grow-1">
                                <i class="fa fa-filter mr-1"></i> Filter Records
                            </button>
                            <a href="{{ url('view_balance_sheet') }}?active_tab={{ $activeTab }}" class="btn-filter-reset">
                                <i class="fa fa-times mr-1"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- 3. Navigation Tabs --}}
        <ul class="nav nav-tabs balance-nav-tabs" id="balanceSheetTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'dashboard_summary' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#dashboard_summary" 
                   onclick="syncActiveTab('dashboard_summary')">
                    <i class="fa fa-tachometer mr-1"></i> Dashboard Summary
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'all_payments' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#all_payments" 
                   onclick="syncActiveTab('all_payments')">
                    <i class="fa fa-list-alt mr-1"></i> All Collections
                    <span class="tab-count-badge">{{ $counts['all_payments'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'cash_payment' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#cash_payment" 
                   onclick="syncActiveTab('cash_payment')">
                    <i class="fa fa-money mr-1"></i> Cash Payment
                    <span class="tab-count-badge">{{ $counts['cash_payment'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'upi_payment' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#upi_payment" 
                   onclick="syncActiveTab('upi_payment')">
                    <i class="fa fa-mobile mr-1"></i> UPI Payment
                    <span class="tab-count-badge">{{ $counts['upi_payment'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'cheque_payment' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#cheque_payment" 
                   onclick="syncActiveTab('cheque_payment')">
                    <i class="fa fa-credit-card mr-1"></i> Cheque Payment
                    <span class="tab-count-badge">{{ $counts['cheque_payment'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item {{ ($counts['net_banking'] ?? 0) > 0 ? '' : 'd-none' }}">
                <a class="nav-link {{ $activeTab == 'net_banking' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#net_banking" 
                   onclick="syncActiveTab('net_banking')">
                    <i class="fa fa-university mr-1"></i> Net Banking
                    <span class="tab-count-badge">{{ $counts['net_banking'] ?? 0 }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'expense' ? 'active' : '' }}" 
                   data-toggle="tab" 
                   href="#expense" 
                   onclick="syncActiveTab('expense')">
                    <i class="fa fa-file-text mr-1"></i> Expense
                    <span class="tab-count-badge">{{ $counts['expense'] ?? 0 }}</span>
                </a>
            </li>
        </ul>

        {{-- 4. Tab Panes Content --}}
        <div class="tab-content" id="balanceSheetContent">

            {{-- Tab 1: Dashboard Summary --}}
            <div class="tab-pane fade {{ $activeTab == 'dashboard_summary' ? 'show active' : '' }}" id="dashboard_summary" role="tabpanel">
                
                {{-- A. Filtered Period Overview --}}
                <div class="section-banner-title">
                    <span><i class="fa fa-bar-chart mr-1"></i> Period Financial Overview {{ !empty(request('from_date')) || !empty(request('to_date')) ? '(Filtered Date Range)' : '(Full Session Period)' }}</span>
                    <span class="badge badge-secondary" style="font-size: 10px;">Consolidated Summary</span>
                </div>

                <div class="kpi-cards-grid">
                    {{-- 1. Net Profit / Surplus --}}
                    <div class="kpi-card {{ ($data['totalProfit'] ?? 0) >= 0 ? 'kpi-green' : 'kpi-red' }}">
                        <div class="kpi-info">
                            <span class="kpi-label">Net Balance / Surplus</span>
                            <span class="kpi-value {{ ($data['totalProfit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹ {{ number_format($data['totalProfit'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Revenue minus Expenses</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa {{ ($data['totalProfit'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                        </div>
                    </div>

                    {{-- 2. Total Fee Revenue --}}
                    <div class="kpi-card kpi-blue">
                        <div class="kpi-info">
                            <span class="kpi-label">Total Fee Revenue</span>
                            <span class="kpi-value text-primary">
                                ₹ {{ number_format($data['totalRevenue'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Cash + UPI + Cheque + NetBank</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-university"></i>
                        </div>
                    </div>

                    {{-- 3. Cash Collection --}}
                    <div class="kpi-card kpi-green">
                        <div class="kpi-info">
                            <span class="kpi-label">Cash Collection</span>
                            <span class="kpi-value text-success">
                                ₹ {{ number_format($data['cashTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">{{ $counts['cash_payment'] ?? 0 }} Transactions</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>

                    {{-- 4. UPI Collection --}}
                    <div class="kpi-card kpi-blue">
                        <div class="kpi-info">
                            <span class="kpi-label">UPI Collection</span>
                            <span class="kpi-value text-info">
                                ₹ {{ number_format($data['upiTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">{{ $counts['upi_payment'] ?? 0 }} Transactions</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-mobile"></i>
                        </div>
                    </div>

                    {{-- 5. Cheque Collection --}}
                    <div class="kpi-card kpi-amber">
                        <div class="kpi-info">
                            <span class="kpi-label">Cheque Collection</span>
                            <span class="kpi-value text-warning">
                                ₹ {{ number_format($data['chequeTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">{{ $counts['cheque_payment'] ?? 0 }} Transactions</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-credit-card"></i>
                        </div>
                    </div>

                    {{-- 6. Total Expenses --}}
                    <div class="kpi-card kpi-red">
                        <div class="kpi-info">
                            <span class="kpi-label">Total Expenses</span>
                            <span class="kpi-value text-danger">
                                ₹ {{ number_format($data['expenseTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">{{ $counts['expense'] ?? 0 }} Records</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                    </div>
                </div>

                {{-- B. Visual Payment Mode Distribution --}}
                @if($totalRev > 0)
                <div class="dist-progress-card">
                    <div class="dist-progress-header">
                        <span class="dist-progress-title">
                            <i class="fa fa-pie-chart mr-1"></i> Fee Collection Mode Distribution (% of Total Revenue)
                        </span>
                        <span class="text-muted" style="font-size: 10.5px; font-weight: 600;">
                            Total Collected: ₹ {{ number_format($totalRev, 2) }}
                        </span>
                    </div>

                    <div class="dist-multi-bar">
                        <div class="dist-bar-item bg-success" style="width: {{ $cashPct }}%;" title="Cash: {{ $cashPct }}% (₹ {{ number_format($data['cashTotal'] ?? 0, 2) }})"></div>
                        <div class="dist-bar-item bg-info" style="width: {{ $upiPct }}%;" title="UPI: {{ $upiPct }}% (₹ {{ number_format($data['upiTotal'] ?? 0, 2) }})"></div>
                        <div class="dist-bar-item bg-warning" style="width: {{ $chequePct }}%;" title="Cheque: {{ $chequePct }}% (₹ {{ number_format($data['chequeTotal'] ?? 0, 2) }})"></div>
                        <div class="dist-bar-item bg-purple" style="width: {{ $netBankPct }}%;" title="Net Banking: {{ $netBankPct }}% (₹ {{ number_format($data['netBankingTotal'] ?? 0, 2) }})"></div>
                    </div>

                    <div class="dist-legend-row">
                        <span class="dist-legend-item">
                            <span class="dist-dot bg-success"></span> Cash: {{ $cashPct }}% (₹ {{ number_format($data['cashTotal'] ?? 0, 2) }})
                        </span>
                        <span class="dist-legend-item">
                            <span class="dist-dot bg-info"></span> UPI: {{ $upiPct }}% (₹ {{ number_format($data['upiTotal'] ?? 0, 2) }})
                        </span>
                        <span class="dist-legend-item">
                            <span class="dist-dot bg-warning"></span> Cheque: {{ $chequePct }}% (₹ {{ number_format($data['chequeTotal'] ?? 0, 2) }})
                        </span>
                        @if(($data['netBankingTotal'] ?? 0) > 0)
                        <span class="dist-legend-item">
                            <span class="dist-dot bg-purple"></span> Net Banking: {{ $netBankPct }}% (₹ {{ number_format($data['netBankingTotal'] ?? 0, 2) }})
                        </span>
                        @endif
                    </div>
                </div>
                @endif

                {{-- C. Today Real-time Snapshot --}}
                <div class="section-banner-title">
                    <span><i class="fa fa-calendar mr-1"></i> Today's Real-time Snapshot ({{ date('d-M-Y') }})</span>
                    <span class="badge badge-info brand-status-badge">Live Day Metrics</span>
                </div>

                <div class="kpi-cards-grid">
                    <div class="kpi-card {{ ($data['todayProfit'] ?? 0) >= 0 ? 'kpi-green' : 'kpi-red' }}">
                        <div class="kpi-info">
                            <span class="kpi-label">Today Net Balance</span>
                            <span class="kpi-value {{ ($data['todayProfit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹ {{ number_format($data['todayProfit'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Today's Balance</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-calculator"></i>
                        </div>
                    </div>

                    <div class="kpi-card kpi-blue">
                        <div class="kpi-info">
                            <span class="kpi-label">Today Revenue</span>
                            <span class="kpi-value text-primary">
                                ₹ {{ number_format($data['todayRevenue'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">All Payment Modes</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                    </div>

                    <div class="kpi-card kpi-green">
                        <div class="kpi-info">
                            <span class="kpi-label">Today Cash</span>
                            <span class="kpi-value text-success">
                                ₹ {{ number_format($data['todayCashTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Cash Counter</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-money"></i>
                        </div>
                    </div>

                    <div class="kpi-card kpi-blue">
                        <div class="kpi-info">
                            <span class="kpi-label">Today UPI</span>
                            <span class="kpi-value text-info">
                                ₹ {{ number_format($data['todayUpiTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Online UPI QR</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-mobile"></i>
                        </div>
                    </div>

                    <div class="kpi-card kpi-amber">
                        <div class="kpi-info">
                            <span class="kpi-label">Today Cheque</span>
                            <span class="kpi-value text-warning">
                                ₹ {{ number_format($data['todayChequeTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Bank Cheques</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-credit-card"></i>
                        </div>
                    </div>

                    <div class="kpi-card kpi-red">
                        <div class="kpi-info">
                            <span class="kpi-label">Today Expenses</span>
                            <span class="kpi-value text-danger">
                                ₹ {{ number_format($data['todayExpenseTotal'] ?? 0, 2) }}
                            </span>
                            <span class="kpi-subtext">Day Vouchers</span>
                        </div>
                        <div class="kpi-icon-box">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tab 2: All Collections --}}
            <div class="tab-pane fade {{ $activeTab == 'all_payments' ? 'show active' : '' }}" id="all_payments" role="tabpanel">
                @php
                    $records = $data['allPayments'] ?? [];
                    $title = 'All Payment Modes (Consolidated)';
                    $tableId = 'allPaymentsTable';
                    $modeTotal = $data['totalRevenue'] ?? 0;
                @endphp
                @include('BalanceSheet.payment_table')
            </div>

            {{-- Tab 3: Cash Payment --}}
            <div class="tab-pane fade {{ $activeTab == 'cash_payment' ? 'show active' : '' }}" id="cash_payment" role="tabpanel">
                @php
                    $records = $data['cash'] ?? [];
                    $title = 'Cash Fee Collections';
                    $tableId = 'cashTable';
                    $modeTotal = $data['cashTotal'] ?? 0;
                @endphp
                @include('BalanceSheet.payment_table')
            </div>

            {{-- Tab 3: UPI Payment --}}
            <div class="tab-pane fade {{ $activeTab == 'upi_payment' ? 'show active' : '' }}" id="upi_payment" role="tabpanel">
                @php
                    $records = $data['upi'] ?? [];
                    $title = 'UPI / Online Fee Collections';
                    $tableId = 'upiTable';
                    $modeTotal = $data['upiTotal'] ?? 0;
                @endphp
                @include('BalanceSheet.payment_table')
            </div>

            {{-- Tab 4: Cheque Payment --}}
            <div class="tab-pane fade {{ $activeTab == 'cheque_payment' ? 'show active' : '' }}" id="cheque_payment" role="tabpanel">
                @php
                    $records = $data['cheque'] ?? [];
                    $title = 'Cheque Fee Collections';
                    $tableId = 'chequeTable';
                    $modeTotal = $data['chequeTotal'] ?? 0;
                @endphp
                @include('BalanceSheet.payment_table')
            </div>

            {{-- Tab 5: Net Banking --}}
            <div class="tab-pane fade {{ $activeTab == 'net_banking' ? 'show active' : '' }}" id="net_banking" role="tabpanel">
                @php
                    $records = $data['netBanking'] ?? [];
                    $title = 'Net Banking Fee Collections';
                    $tableId = 'netBankingTable';
                    $modeTotal = $data['netBankingTotal'] ?? 0;
                @endphp
                @include('BalanceSheet.payment_table')
            </div>

            {{-- Tab 6: Expenses --}}
            <div class="tab-pane fade {{ $activeTab == 'expense' ? 'show active' : '' }}" id="expense" role="tabpanel">
                <div class="card balance-table-card">
                    <div class="dash-card-header">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 class="dash-card-title mb-0">
                                <i class="fa fa-file-text-o mr-1 text-danger"></i> Expense Transactions &amp; Vouchers
                            </h3>
                            <span class="badge badge-info brand-status-badge">
                                <span class="table-total-count">{{ count($data['expense'] ?? []) }}</span> Records
                            </span>
                            <span class="badge badge-danger brand-status-badge">
                                Total: ₹ {{ number_format($data['expenseTotal'] ?? 0, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="table-scroll-container">
                        <table id="expenseTable" class="dash-table balance-data-table">
                            <thead>
                                {{-- Row 1: Header Titles (#002C54) --}}
                                <tr class="header-titles-row">
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th class="text-center" style="width: 100px;">Date</th>
                                    <th>Expense Title / Category</th>
                                    <th style="width: 150px;">Payee / Vendor</th>
                                    <th style="width: 120px;">Bill / Ref</th>
                                    <th>Description / Notes</th>
                                    <th class="text-right" style="width: 130px;">Amount</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters (#08335c) --}}
                                <tr class="excel-filter-row">
                                    <th class="text-center">
                                        <i class="fa fa-filter text-info" style="font-size: 10px;"></i>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" data-col="date" placeholder="Date...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" data-col="title" placeholder="Search title...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" data-col="payee" placeholder="Payee...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" data-col="bill" placeholder="Bill No...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" data-col="desc" placeholder="Notes...">
                                    </th>
                                    <th class="text-right">
                                        <button type="button" class="btn-reset-filters" onclick="resetTableFilters('expenseTable')" title="Reset Filters">
                                            <i class="fa fa-refresh mr-1"></i> Clear
                                        </button>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $expGrandTotal = 0; @endphp
                                @forelse($data['expense'] as $key => $item)
                                    @php
                                        $expAmt = $item->amount ?? $item->expense_amount ?? 0;
                                        $expGrandTotal += $expAmt;

                                        $expDate = '-';
                                        if (!empty($item->date)) {
                                            $expDate = date('d-M-Y', strtotime($item->date));
                                        } elseif (!empty($item->expense_date)) {
                                            $expDate = date('d-M-Y', strtotime($item->expense_date));
                                        } elseif (!empty($item->created_at)) {
                                            $expDate = date('d-M-Y', strtotime($item->created_at));
                                        }

                                        $titleName = $item->name ?? $item->expense_name ?? $item->title ?? 'Expense';
                                        $payeeName = $item->payee_name ?? '';
                                        $billNo = $item->bill_no ?? $item->invoice_no ?? '';
                                        $descText = $item->description ?? $item->remark ?? '';
                                    @endphp
                                    <tr class="table-data-row"
                                        data-date="{{ strtolower($expDate) }}"
                                        data-title="{{ strtolower($titleName) }}"
                                        data-payee="{{ strtolower($payeeName) }}"
                                        data-bill="{{ strtolower($billNo) }}"
                                        data-desc="{{ strtolower($descText) }}"
                                        data-amount="{{ $expAmt }}">
                                        
                                        <td class="text-center text-muted font-weight-bold row-index">{{ $key + 1 }}</td>
                                        <td class="text-center">
                                            <span class="text-dark" style="font-size: 11px; white-space: nowrap;">{{ $expDate }}</span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark" style="font-size: 11.5px;">
                                                {{ $titleName }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark" style="font-size: 11px;">
                                                {{ $payeeName ?: '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light border text-dark" style="font-size: 10px;">
                                                {{ $billNo ?: '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted" style="font-size: 10.5px;">
                                                {{ $descText ?: '-' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <span class="text-danger font-weight-bold" style="font-size: 11.5px;">
                                                ₹ {{ number_format($expAmt, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="7" class="p-0 border-0">
                                            <div class="empty-data-wrapper">
                                                <div class="empty-data-icon">
                                                    <i class="fa fa-folder-open-o"></i>
                                                </div>
                                                <div class="empty-data-title">No Expense Records Found</div>
                                                <div class="empty-data-subtitle">No expense vouchers or transaction records found for the selected period or filters.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if(count($data['expense'] ?? []) > 0)
                            <tfoot>
                                <tr class="balance-table-total-row">
                                    <td colspan="6" class="text-right font-weight-bold" style="font-size: 11.5px;">
                                        Filtered Total (<span class="footer-count">{{ count($data['expense']) }}</span> Records):
                                    </td>
                                    <td class="text-right text-danger font-weight-bold footer-amount" style="font-size: 12px;">
                                        ₹ {{ number_format($expGrandTotal, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Bottom Pinned Pagination Bar --}}
                    <div class="table-pagination-bar" data-table="#expenseTable">
                        <div class="pagination-info">
                            Showing <span class="page-start">1</span> to <span class="page-end">{{ min(25, count($data['expense'] ?? [])) }}</span> of <span class="page-total">{{ count($data['expense'] ?? []) }}</span> entries
                        </div>

                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label>Rows:</label>
                                <select class="rows-select" onchange="changeTablePageSize('expenseTable', this.value)">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="-1">All</option>
                                </select>
                            </div>

                            <div class="pagination-nav">
                                <button type="button" class="page-btn btn-first" onclick="navigateTablePage('expenseTable', 'first')" title="First Page">&laquo;</button>
                                <button type="button" class="page-btn btn-prev" onclick="navigateTablePage('expenseTable', 'prev')" title="Previous Page">&lsaquo;</button>
                                <span class="page-indicator">Page <b class="current-page-num">1</b> of <b class="total-pages-num">1</b></span>
                                <button type="button" class="page-btn btn-next" onclick="navigateTablePage('expenseTable', 'next')" title="Next Page">&rsaquo;</button>
                                <button type="button" class="page-btn btn-last" onclick="navigateTablePage('expenseTable', 'last')" title="Last Page">&raquo;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
/**
 * Global Balance Sheet Table Pagination & Excel In-Column Filtering Engine
 */
const tableStates = {};

function initTableState(tableId) {
    if (!tableStates[tableId]) {
        tableStates[tableId] = {
            currentPage: 1,
            pageSize: 25,
            filters: {}
        };
    }
}

function updateTableDisplay(tableId) {
    initTableState(tableId);
    const state = tableStates[tableId];
    const $table = $('#' + tableId);
    const $card = $table.closest('.balance-table-card');
    const $paginationBar = $card.find('.table-pagination-bar');
    const $rows = $table.find('tbody tr.table-data-row');

    if ($rows.length === 0) return;

    // 1. Filter rows
    let matchingRows = [];
    let sumDiscount = 0;
    let sumAmount = 0;

    $rows.each(function(idx) {
        const $row = $(this);
        let matches = true;

        for (const [col, filterVal] of Object.entries(state.filters)) {
            if (filterVal) {
                const rowVal = ($row.attr('data-' + col) || '').toLowerCase();
                if (!rowVal.includes(filterVal.toLowerCase())) {
                    matches = false;
                    break;
                }
            }
        }

        if (matches) {
            matchingRows.push($row);
            const disc = parseFloat($row.attr('data-discount')) || 0;
            const amt = parseFloat($row.attr('data-amount')) || 0;
            sumDiscount += disc;
            sumAmount += amt;
        }
    });

    const totalMatching = matchingRows.length;
    const pageSize = state.pageSize === -1 ? totalMatching : state.pageSize;
    const totalPages = pageSize > 0 ? Math.max(1, Math.ceil(totalMatching / pageSize)) : 1;

    if (state.currentPage > totalPages) {
        state.currentPage = totalPages;
    }
    if (state.currentPage < 1) {
        state.currentPage = 1;
    }

    const startIndex = (state.currentPage - 1) * pageSize;
    const endIndex = state.pageSize === -1 ? totalMatching : Math.min(startIndex + pageSize, totalMatching);

    // 2. Hide all data rows and empty rows first
    $rows.hide();
    $table.find('tbody tr.empty-row').remove();

    if (totalMatching === 0) {
        const colCount = $table.find('thead tr.header-titles-row th').length || 9;
        $table.find('tbody').append(
            '<tr class="empty-row"><td colspan="' + colCount + '" class="p-0 border-0">' +
            '<div class="empty-data-wrapper">' +
            '<div class="empty-data-icon"><i class="fa fa-filter"></i></div>' +
            '<div class="empty-data-title">No Matching Records Found</div>' +
            '<div class="empty-data-subtitle">Try adjusting or clearing your active column filters.</div>' +
            '</div>' +
            '</td></tr>'
        );
    } else {
        // Show visible slice and renumber visible index
        for (let i = startIndex; i < endIndex; i++) {
            const $r = matchingRows[i];
            $r.show();
            $r.find('.row-index').text(i + 1);
        }
    }

    // 3. Update Pagination Bar & Info
    if ($paginationBar.length) {
        $paginationBar.find('.page-start').text(totalMatching === 0 ? 0 : startIndex + 1);
        $paginationBar.find('.page-end').text(endIndex);
        $paginationBar.find('.page-total').text(totalMatching);
        $paginationBar.find('.current-page-num').text(state.currentPage);
        $paginationBar.find('.total-pages-num').text(totalPages);

        $paginationBar.find('.btn-first, .btn-prev').prop('disabled', state.currentPage <= 1);
        $paginationBar.find('.btn-next, .btn-last').prop('disabled', state.currentPage >= totalPages);
    }

    // 4. Update Footer Totals dynamically for filtered records
    const formatCurrency = (val) => '₹ ' + val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    $table.find('tfoot .footer-count').text(totalMatching);
    $table.find('tfoot .footer-discount').text(formatCurrency(sumDiscount));
    $table.find('tfoot .footer-amount').text(formatCurrency(sumAmount));
}

function changeTablePageSize(tableId, newSize) {
    initTableState(tableId);
    tableStates[tableId].pageSize = parseInt(newSize, 10);
    tableStates[tableId].currentPage = 1;
    updateTableDisplay(tableId);
}

function navigateTablePage(tableId, action) {
    initTableState(tableId);
    const state = tableStates[tableId];
    const $table = $('#' + tableId);
    const $rows = $table.find('tbody tr.table-data-row');

    // Count matching rows
    let matchCount = 0;
    $rows.each(function() {
        let matches = true;
        for (const [col, filterVal] of Object.entries(state.filters)) {
            if (filterVal) {
                const rowVal = ($(this).attr('data-' + col) || '').toLowerCase();
                if (!rowVal.includes(filterVal.toLowerCase())) {
                    matches = false;
                    break;
                }
            }
        }
        if (matches) matchCount++;
    });

    const pageSize = state.pageSize === -1 ? matchCount : state.pageSize;
    const totalPages = pageSize > 0 ? Math.max(1, Math.ceil(matchCount / pageSize)) : 1;

    if (action === 'first') {
        state.currentPage = 1;
    } else if (action === 'prev') {
        if (state.currentPage > 1) state.currentPage--;
    } else if (action === 'next') {
        if (state.currentPage < totalPages) state.currentPage++;
    } else if (action === 'last') {
        state.currentPage = totalPages;
    }

    updateTableDisplay(tableId);
}

function resetTableFilters(tableId) {
    initTableState(tableId);
    tableStates[tableId].filters = {};
    tableStates[tableId].currentPage = 1;

    const $table = $('#' + tableId);
    $table.find('.excel-col-filter').val('');
    updateTableDisplay(tableId);
}

/**
 * Sync active tab across filtering and preset clicks
 */
function syncActiveTab(tabName) {
    document.getElementById('filter_active_tab').value = tabName;
    if (history.pushState) {
        const url = new URL(window.location);
        url.searchParams.set('active_tab', tabName);
        window.history.pushState({}, '', url);
    }
}

/**
 * Set preset dates instantly
 */
function setDatePreset(type) {
    const today = new Date();
    const fromInput = document.getElementById('from_date');
    const toInput = document.getElementById('to_date');

    function formatDate(d) {
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();
        return [year, month.padStart(2, '0'), day.padStart(2, '0')].join('-');
    }

    if (type === 'today') {
        const todayStr = formatDate(today);
        fromInput.value = todayStr;
        toInput.value = todayStr;
    } else if (type === 'yesterday') {
        const y = new Date();
        y.setDate(y.getDate() - 1);
        const yStr = formatDate(y);
        fromInput.value = yStr;
        toInput.value = yStr;
    } else if (type === 'this_week') {
        const first = today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1);
        const monday = new Date(today.setDate(first));
        const sunday = new Date();
        fromInput.value = formatDate(monday);
        toInput.value = formatDate(sunday);
    } else if (type === 'this_month') {
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        fromInput.value = formatDate(firstDay);
        toInput.value = formatDate(lastDay);
    } else if (type === 'this_session') {
        const currentYear = today.getFullYear();
        const sessionStartYear = today.getMonth() >= 3 ? currentYear : (currentYear - 1);
        fromInput.value = sessionStartYear + '-04-01';
        toInput.value = (sessionStartYear + 1) + '-03-31';
    } else if (type === 'all') {
        fromInput.value = '';
        toInput.value = '';
    }

    document.getElementById('balanceFilterForm').submit();
}

$(document).ready(function() {
    // In-Column Excel Filters keyup with debounce
    let debounceTimer = null;
    $(document).on('keyup input', '.excel-col-filter', function() {
        const $input = $(this);
        const col = $input.data('col');
        const val = $input.val().trim();
        const tableId = $input.closest('table').attr('id');

        if (!tableId) return;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            initTableState(tableId);
            tableStates[tableId].filters[col] = val;
            tableStates[tableId].currentPage = 1;
            updateTableDisplay(tableId);
        }, 150);
    });

    // Initialize pagination and display for all tables
    const allTableIds = ['allPaymentsTable', 'cashTable', 'upiTable', 'chequeTable', 'netBankingTable', 'expenseTable'];
    allTableIds.forEach(function(tblId) {
        if ($('#' + tblId).length) {
            updateTableDisplay(tblId);
        }
    });

    // Robust Tab Switch Handling for both jQuery tab() and direct DOM clicks
    $(document).on('click', '#balanceSheetTabs a[data-toggle="tab"]', function(e) {
        e.preventDefault();
        const $this = $(this);
        const targetSelector = $this.attr('href');
        const tabName = targetSelector.replace('#', '');

        // 1. Update tab button active states
        $('#balanceSheetTabs .nav-link').removeClass('active');
        $this.addClass('active');

        // 2. Update tab panes display
        $('#balanceSheetContent > .tab-pane').removeClass('show active').css('display', 'none');
        $(targetSelector).addClass('show active').css('display', 'block');

        // 3. Update hidden input and URL state
        syncActiveTab(tabName);

        // 4. Trigger table recalculation if pane has a table
        const $targetTable = $(targetSelector).find('table.balance-data-table');
        if ($targetTable.length) {
            const tblId = $targetTable.attr('id');
            if (tblId) {
                updateTableDisplay(tblId);
            }
        }
    });

    // Handle hash links or query active tab on initial page load
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('active_tab') || '{{ $activeTab }}';
    if (tabParam && $('#balanceSheetTabs a[href="#' + tabParam + '"]').length) {
        $('#balanceSheetTabs a[href="#' + tabParam + '"]').trigger('click');
    } else {
        $('#balanceSheetTabs a.nav-link.active').first().trigger('click');
    }
});
</script>
@endsection

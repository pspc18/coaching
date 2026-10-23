@php
    $session = DB::table('sessions')->where('id', Session::get('session_id'))->whereNull('deleted_at')->first();
    $sessionLabel = trim(($session->from_year ?? '') . '-' . ($session->to_year ?? ''));
    if (empty($sessionLabel)) {
        $sessionLabel = date('Y') . '-' . (date('Y') + 1);
    }

    $getSetting = Helper::getSetting();
    $schoolName = is_array($getSetting) ? ($getSetting['name'] ?? 'School') : ($getSetting->name ?? 'School');
    $schoolLogo = is_array($getSetting) ? ($getSetting['left_logo'] ?? '') : ($getSetting->left_logo ?? '');
    $logoUrl = !empty($schoolLogo) ? env('IMAGE_SHOW_PATH') . '/setting/left_logo/' . $schoolLogo : '';

    $totalStudents = (int) ($grandTotals['total'] ?? 0);
    $totalBoys = (int) ($grandTotals['boys'] ?? 0);
    $totalGirls = (int) ($grandTotals['girls'] ?? 0);
    $boysPercent = $totalStudents > 0 ? round(($totalBoys / $totalStudents) * 100, 1) : 0;
    $girlsPercent = $totalStudents > 0 ? round(($totalGirls / $totalStudents) * 100, 1) : 0;
    $genderRatio = $totalBoys > 0 ? round(($totalGirls / $totalBoys) * 1000) : 0;
    $classCount = count($classType ?? []);

    // Compute category level totals across all classes for top insights
    $categoryTotals = [];
    foreach ($categories as $cat) {
        $cBoys = 0;
        $cGirls = 0;
        foreach ($classType as $type) {
            $cBoys += (int) ($classTotals[$type->id]['categories'][$cat]['boys'] ?? 0);
            $cGirls += (int) ($classTotals[$type->id]['categories'][$cat]['girls'] ?? 0);
        }
        $cTotal = $cBoys + $cGirls;
        $categoryTotals[$cat] = [
            'boys'    => $cBoys,
            'girls'   => $cGirls,
            'total'   => $cTotal,
            'percent' => $totalStudents > 0 ? round(($cTotal / $totalStudents) * 100, 1) : 0,
        ];
    }

    // Color definitions for each category badge / header
    $catColors = [
        'GENERAL' => ['bg' => '#e0e7ff', 'text' => '#3730a3', 'bar' => '#6366f1', 'border' => '#c7d2fe'],
        'OBC'     => ['bg' => '#fef3c7', 'text' => '#92400e', 'bar' => '#f59e0b', 'border' => '#fde68a'],
        'SC'      => ['bg' => '#f3e8ff', 'text' => '#6b21a8', 'bar' => '#a855f7', 'border' => '#e9d5ff'],
        'ST'      => ['bg' => '#dcfce7', 'text' => '#166534', 'bar' => '#22c55e', 'border' => '#bbf7d0'],
        'BC'      => ['bg' => '#e0f2fe', 'text' => '#075985', 'bar' => '#0284c7', 'border' => '#bae6fd'],
        'SBC'     => ['bg' => '#ccfbf1', 'text' => '#115e59', 'bar' => '#14b8a6', 'border' => '#99f6e4'],
        'OTHER'   => ['bg' => '#f1f5f9', 'text' => '#334155', 'bar' => '#64748b', 'border' => '#cbd5e1'],
    ];
@endphp

@extends('layout.app')

@section('title', "Category Wise Student Report - Session $sessionLabel")

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - CATEGORY WISE STUDENT REPORT
   - Signature Dark Navy Theme (#002C54 -> #0f3460)
   - High-DPI Executive KPI Cards
   - Visual Proportional Category Distribution Bar & Filter Badges
   - Sticky Dual-Header & Sticky First 2 Columns (S.No + Class) Matrix
   - Live Real-time Class Search Filter
   - View Mode Switcher (Detailed vs Compact)
   - One-Click Excel / CSV Client-side Export & Clean Landscape Print Sheet
   ========================================================================== */

:root {
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --navy-gradient: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    --navy-card-header: #002342;
    --border-soft: #cbd5e1;
    --border-light: #e2e8f0;
    --bg-page: #f8fafc;
}

.report-workspace {
    background: var(--bg-page);
    min-height: calc(100vh - 58px);
    padding: 10px 14px 24px;
}

/* 1. Hero Header Banner */
.cat-hero {
    background: var(--navy-gradient);
    border-radius: 6px;
    padding: 14px 18px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 12px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.18);
    position: relative;
    overflow: hidden;
}
.cat-hero::after {
    content: '';
    position: absolute;
    right: -20px;
    top: -30px;
    width: 220px;
    height: 180px;
    background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
    pointer-events: none;
}
.cat-hero-left {
    flex: 1;
    min-width: 0;
}
.cat-hero-kicker {
    font-size: 10px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #93c5fd;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 3px;
}
.cat-hero-title {
    font-size: 20px;
    line-height: 1.2;
    margin: 0 0 5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.cat-hero-chips {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 11px;
}
.cat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    padding: 2px 8px;
    border-radius: 4px;
    color: #f1f5f9;
}
.cat-chip b {
    color: #ffffff;
    font-weight: 700;
}
.cat-hero-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    flex-shrink: 0;
    z-index: 2;
}
.cat-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 11.5px;
    font-weight: 700;
    border-radius: 4px;
    text-decoration: none !important;
    cursor: pointer;
    border: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.cat-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.cat-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
    transform: translateY(-1px);
}
.cat-btn-emerald {
    background: #059669;
    color: #ffffff !important;
    box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
}
.cat-btn-emerald:hover {
    background: #047857;
    color: #ffffff !important;
    transform: translateY(-1px);
}
.cat-btn-outline {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
}
.cat-btn-outline:hover {
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff !important;
}

/* 2. Top Executive KPI Scorecards */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-bottom: 12px;
}
.kpi-box {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 5px;
    padding: 10px 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.kpi-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 44, 84, 0.08);
}
.kpi-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.kpi-icon-navy    { background: #e0f2fe; color: #002C54; }
.kpi-icon-blue    { background: #dbeafe; color: #1d4ed8; }
.kpi-icon-rose    { background: #ffe4e6; color: #e11d48; }
.kpi-icon-emerald { background: #d1fae5; color: #059669; }
.kpi-icon-amber   { background: #fef3c7; color: #d97706; }

.kpi-data {
    flex: 1;
    min-width: 0;
}
.kpi-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
}
.kpi-number {
    font-size: 20px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}
.kpi-sub {
    font-size: 10px;
    color: #64748b;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-sub b {
    font-weight: 700;
    color: #334155;
}

/* 3. Visual Proportional Distribution Strip */
.cat-distribution-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 5px;
    padding: 12px 14px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.dist-top-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.dist-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dist-hint {
    font-size: 10.5px;
    color: #64748b;
}

/* Segmented progress bar */
.dist-progress-bar {
    display: flex;
    height: 12px;
    width: 100%;
    background: #f1f5f9;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 10px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
}
.dist-segment {
    height: 100%;
    transition: width 0.3s ease;
    position: relative;
}

/* Category Pill Badges */
.cat-badges-strip {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.cat-badge-card {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    border: 1px solid transparent;
}
.cat-badge-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.cat-badge-card.active-filter {
    outline: 2px solid #002C54;
    box-shadow: 0 0 0 3px rgba(0, 44, 84, 0.2);
}
.cat-badge-name {
    font-weight: 800;
    letter-spacing: 0.3px;
}
.cat-badge-count {
    background: rgba(255, 255, 255, 0.85);
    padding: 1px 6px;
    border-radius: 3px;
    font-weight: 800;
    color: #0f172a;
    font-size: 11px;
}
.cat-badge-sub {
    font-size: 9.5px;
    color: #475569;
}

/* 4. Controls & Filter Bar */
.cat-controls-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 5px;
    padding: 8px 12px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.cat-search-group {
    display: flex;
    align-items: center;
    position: relative;
    width: 320px;
    max-width: 100%;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 0 10px;
    height: 32px;
}
.cat-search-group:focus-within {
    background: #ffffff;
    border-color: #002C54;
    box-shadow: 0 0 0 2.5px rgba(0, 44, 84, 0.12);
}
.cat-search-icon {
    color: #64748b;
    font-size: 12px;
    margin-right: 6px;
}
.cat-search-input {
    border: none;
    background: transparent;
    outline: none;
    font-size: 12px;
    color: #0f172a;
    width: 100%;
}
.cat-search-input::placeholder {
    color: #94a3b8;
    font-size: 11.5px;
}
.cat-search-clear {
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    font-size: 15px;
    padding: 0 4px;
    line-height: 1;
}
.cat-search-clear:hover {
    color: #ef4444;
}
.cat-controls-right {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11.5px;
}
.btn-view-mode {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-view-mode.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.visible-row-count {
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
}

/* 5. Main Matrix Card & Table Styling */
.cat-matrix-card {
    background: #ffffff;
    border: 1px solid var(--border-soft);
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
.cat-matrix-card-header {
    background: var(--navy-card-header);
    color: #ffffff;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.matrix-header-title {
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.matrix-header-count {
    font-size: 11px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 2px 8px;
    border-radius: 3px;
    color: #ffffff;
    font-weight: 700;
}

.table-responsive-matrix {
    max-height: 68vh;
    overflow: auto;
    position: relative;
}

.cat-matrix-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    min-width: 1100px;
}

/* Sticky Headers */
.cat-matrix-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    padding: 6px 8px;
    border-right: 1px solid rgba(0, 0, 0, 0.08);
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    white-space: nowrap;
}
.cat-matrix-table thead tr:first-child th {
    top: 0;
    z-index: 11;
}
.cat-matrix-table thead tr:nth-child(2) th {
    top: 29px; /* Height of the first row */
    z-index: 10;
}

/* Category Themed Headers */
.th-cat-general { background: #e0e7ff !important; color: #3730a3 !important; border-top: 3px solid #6366f1 !important; }
.th-cat-obc     { background: #fef3c7 !important; color: #92400e !important; border-top: 3px solid #f59e0b !important; }
.th-cat-sc      { background: #f3e8ff !important; color: #6b21a8 !important; border-top: 3px solid #a855f7 !important; }
.th-cat-st      { background: #dcfce7 !important; color: #166534 !important; border-top: 3px solid #22c55e !important; }
.th-cat-bc      { background: #e0f2fe !important; color: #075985 !important; border-top: 3px solid #0284c7 !important; }
.th-cat-sbc     { background: #ccfbf1 !important; color: #115e59 !important; border-top: 3px solid #14b8a6 !important; }
.th-cat-other   { background: #f1f5f9 !important; color: #334155 !important; border-top: 3px solid #64748b !important; }
.th-cat-total   { background: #002C54 !important; color: #ffffff !important; border-top: 3px solid #38bdf8 !important; }

.th-sub-boys  { background: #eff6ff !important; color: #1d4ed8 !important; font-size: 10px !important; }
.th-sub-girls { background: #fff1f2 !important; color: #be123c !important; font-size: 10px !important; }
.th-sub-total { background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 10px !important; }

/* Sticky Left Columns: S.No (Col 1) and Class Name (Col 2) */
.col-sticky-sno {
    position: sticky !important;
    left: 0 !important;
    width: 48px;
    min-width: 48px;
    max-width: 48px;
    z-index: 8 !important;
    background: #ffffff;
    text-align: center;
    border-right: 1px solid #cbd5e1 !important;
}
.col-sticky-class {
    position: sticky !important;
    left: 48px !important;
    width: 170px;
    min-width: 160px;
    max-width: 200px;
    z-index: 8 !important;
    background: #ffffff;
    text-align: left !important;
    font-weight: 700;
    color: #0f172a;
    border-right: 2px solid #94a3b8 !important;
    box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.08);
}

/* Sticky Header on Left Columns */
.cat-matrix-table thead tr:first-child th.col-sticky-sno,
.cat-matrix-table thead tr:first-child th.col-sticky-class {
    z-index: 15 !important;
    background: #002342 !important;
    color: #ffffff !important;
}

/* Body Cells */
.cat-matrix-table tbody tr {
    transition: background 0.1s ease;
}
.cat-matrix-table tbody tr:hover {
    background: #f1f8ff !important;
}
.cat-matrix-table tbody tr:hover .col-sticky-sno,
.cat-matrix-table tbody tr:hover .col-sticky-class {
    background: #f1f8ff !important;
}
.cat-matrix-table tbody td {
    padding: 6px 8px;
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid var(--border-light);
    border-right: 1px solid rgba(0, 0, 0, 0.05);
    color: #334155;
}

/* Cell Highlights */
.cell-boys {
    color: #1e40af;
}
.cell-girls {
    color: #be123c;
}
.cell-cat-total {
    font-weight: 700;
    color: #0f172a;
    background: #fbfcfe;
}
.cell-zero {
    color: #cbd5e1;
    font-weight: 400;
}
.cell-grand-total {
    background: #f8fafc;
    font-weight: 800;
    color: #002C54;
    border-left: 1px solid #cbd5e1;
}

/* Sticky Grand Totals Footer */
.cat-matrix-table tfoot td {
    position: sticky;
    bottom: 0;
    z-index: 9;
    background: #002342 !important;
    color: #ffffff !important;
    font-weight: 800;
    padding: 8px 8px;
    border-top: 2px solid #38bdf8;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    text-align: center;
}
.cat-matrix-table tfoot tr td.col-sticky-sno,
.cat-matrix-table tfoot tr td.col-sticky-class {
    z-index: 14 !important;
    background: #001f3d !important;
    color: #ffffff !important;
    border-right: 2px solid #38bdf8 !important;
}
.cat-matrix-table tfoot .total-highlight {
    background: #003666 !important;
    color: #38bdf8 !important;
    font-size: 12.5px;
}

/* Highlight Selected Category Columns */
.cat-col-highlight {
    background: rgba(254, 240, 138, 0.35) !important;
}

/* Print Stylesheet */
@media print {
    body { background: #ffffff !important; font-size: 10pt !important; }
    .content-wrapper, .report-workspace { padding: 0 !important; margin: 0 !important; background: #ffffff !important; }
    .cat-hero, .kpi-row, .cat-distribution-card, .cat-controls-card, .cat-matrix-card-header, .main-header, .main-sidebar, .sidebar { display: none !important; }
    .cat-matrix-card { border: none !important; box-shadow: none !important; }
    .table-responsive-matrix { max-height: none !important; overflow: visible !important; }
    .cat-matrix-table { min-width: 100% !important; border: 1px solid #000000 !important; }
    .cat-matrix-table thead th, .cat-matrix-table tbody td, .cat-matrix-table tfoot td {
        border: 1px solid #333333 !important;
        position: static !important;
        color: #000000 !important;
        background: none !important;
        padding: 4px 6px !important;
    }
    .print-only-header {
        display: block !important;
        margin-bottom: 16px;
        text-align: center;
        border-bottom: 2px solid #000000;
        padding-bottom: 10px;
    }
}
.print-only-header {
    display: none;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .kpi-row {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .kpi-row {
        grid-template-columns: repeat(2, 1fr);
    }
    .cat-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .cat-hero-actions {
        width: 100%;
        justify-content: flex-start;
    }
    .cat-search-group {
        width: 100%;
    }
}
@media (max-width: 480px) {
    .kpi-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-wrapper report-workspace">
    <div class="container-fluid p-0">

        {{-- Print Only Header --}}
        <div class="print-only-header">
            @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" style="height: 50px; max-width: 140px; margin-bottom: 6px;">
            @endif
            <h2 style="margin: 0; font-size: 18pt; font-weight: 800;">{{ $schoolName }}</h2>
            <h4 style="margin: 4px 0; font-size: 13pt;">Category Wise Student Distribution Report</h4>
            <p style="margin: 0; font-size: 10pt; color: #555555;">
                Academic Session: <b>{{ $sessionLabel }}</b> &bull; Total Students: <b>{{ number_format($totalStudents) }}</b> &bull; Date: <b>{{ date('d-m-Y H:i A') }}</b>
            </p>
        </div>

        {{-- 1. Signature Hero Header Banner --}}
        <div class="cat-hero">
            <div class="cat-hero-left">
                <span class="cat-hero-kicker">
                    <i class="fa fa-graduation-cap"></i> Student Demographics &bull; Executive Matrix
                </span>
                <h1 class="cat-hero-title">
                    <i class="fa fa-pie-chart text-warning"></i>
                    <span>Category Wise Report</span>
                </h1>
                <div class="cat-hero-chips">
                    <span class="cat-chip" title="Current Academic Session">
                        <i class="fa fa-calendar text-info"></i> Session: <b>{{ $sessionLabel }}</b>
                    </span>
                    <span class="cat-chip" title="Total Classes in Session">
                        <i class="fa fa-th-large text-warning"></i> Classes: <b>{{ $classCount }}</b>
                    </span>
                    <span class="cat-chip" title="Categories Count">
                        <i class="fa fa-tags text-success"></i> Categories: <b>{{ count($categories) }}</b>
                    </span>
                    <span class="cat-chip" title="Total Enrolled Students">
                        <i class="fa fa-users text-primary"></i> Total: <b>{{ number_format($totalStudents) }}</b>
                    </span>
                </div>
            </div>

            <div class="cat-hero-actions">
                <button type="button" class="cat-btn cat-btn-emerald" id="btnExportExcel" title="Download Excel (.CSV) Spreadsheet">
                    <i class="fa fa-file-excel-o"></i> <span>Export Excel</span>
                </button>
                <button type="button" class="cat-btn cat-btn-light" id="btnPrintReport" title="Print Landscape Matrix Sheet">
                    <i class="fa fa-print"></i> <span>Print Report</span>
                </button>
                <a href="{{ url('studentsDashboard') }}" class="cat-btn cat-btn-outline" title="Back to Student Dashboard">
                    <i class="fa fa-arrow-left"></i> <span>Dashboard</span>
                </a>
            </div>
        </div>

        {{-- 2. Executive KPI Scorecards --}}
        <div class="kpi-row">
            {{-- Total Enrollment --}}
            <div class="kpi-box">
                <div class="kpi-icon-wrap kpi-icon-navy">
                    <i class="fa fa-users"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-label">Total Enrollment</div>
                    <div class="kpi-number">{{ number_format($totalStudents) }}</div>
                    <div class="kpi-sub">Across <b>{{ $classCount }}</b> Classes</div>
                </div>
            </div>

            {{-- Total Boys --}}
            <div class="kpi-box">
                <div class="kpi-icon-wrap kpi-icon-blue">
                    <i class="fa fa-male"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-label">Total Boys</div>
                    <div class="kpi-number text-primary">{{ number_format($totalBoys) }}</div>
                    <div class="kpi-sub"><b>{{ $boysPercent }}%</b> of Total</div>
                </div>
            </div>

            {{-- Total Girls --}}
            <div class="kpi-box">
                <div class="kpi-icon-wrap kpi-icon-rose">
                    <i class="fa fa-female"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-label">Total Girls</div>
                    <div class="kpi-number" style="color: #e11d48;">{{ number_format($totalGirls) }}</div>
                    <div class="kpi-sub"><b>{{ $girlsPercent }}%</b> of Total</div>
                </div>
            </div>

            {{-- Gender Ratio --}}
            <div class="kpi-box">
                <div class="kpi-icon-wrap kpi-icon-emerald">
                    <i class="fa fa-balance-scale"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-label">Gender Ratio</div>
                    <div class="kpi-number text-success">{{ $genderRatio }}</div>
                    <div class="kpi-sub">Girls per <b>1,000 Boys</b></div>
                </div>
            </div>

            {{-- Active Classes --}}
            <div class="kpi-box">
                <div class="kpi-icon-wrap kpi-icon-amber">
                    <i class="fa fa-th-list"></i>
                </div>
                <div class="kpi-data">
                    <div class="kpi-label">Avg. Per Class</div>
                    <div class="kpi-number text-warning">
                        {{ $classCount > 0 ? round($totalStudents / $classCount, 1) : 0 }}
                    </div>
                    <div class="kpi-sub">Students / Class</div>
                </div>
            </div>
        </div>

        {{-- 3. Visual Category Distribution Card --}}
        <div class="cat-distribution-card">
            <div class="dist-top-line">
                <div class="dist-title">
                    <i class="fa fa-bar-chart text-primary"></i> Category Distribution Overview
                </div>
                <div class="dist-hint">
                    Click a category to highlight its columns in the table
                </div>
            </div>

            {{-- Segmented Visual Progress Bar --}}
            <div class="dist-progress-bar" title="Category Proportions Across Entire School">
                @foreach ($categories as $cat)
                    @php
                        $cData = $categoryTotals[$cat] ?? ['total' => 0, 'percent' => 0];
                        $barColor = $catColors[$cat]['bar'] ?? '#64748b';
                    @endphp
                    @if ($cData['percent'] > 0)
                        <div class="dist-segment" style="width: {{ $cData['percent'] }}%; background: {{ $barColor }};"
                             data-toggle="tooltip" title="{{ $cat }}: {{ number_format($cData['total']) }} ({{ $cData['percent'] }}%)"></div>
                    @endif
                @endforeach
            </div>

            {{-- Interactive Category Badges --}}
            <div class="cat-badges-strip">
                <div class="cat-badge-card active-filter" data-category="ALL" style="background:#002C54; color:#ffffff;">
                    <span class="cat-badge-name"><i class="fa fa-th-large mr-1"></i> ALL CATEGORIES</span>
                    <span class="cat-badge-count">{{ number_format($totalStudents) }}</span>
                </div>

                @foreach ($categories as $cat)
                    @php
                        $cData = $categoryTotals[$cat] ?? ['total' => 0, 'boys' => 0, 'girls' => 0, 'percent' => 0];
                        $cStyle = $catColors[$cat] ?? ['bg' => '#f1f5f9', 'text' => '#334155', 'border' => '#cbd5e1'];
                    @endphp
                    <div class="cat-badge-card" data-category="{{ $cat }}"
                         style="background: {{ $cStyle['bg'] }}; color: {{ $cStyle['text'] }}; border-color: {{ $cStyle['border'] }};"
                         title="{{ $cat }}: {{ $cData['boys'] }} Boys, {{ $cData['girls'] }} Girls ({{ $cData['percent'] }}% of school)">
                        <span class="cat-badge-name">{{ $cat }}</span>
                        <span class="cat-badge-count">{{ number_format($cData['total']) }}</span>
                        <span class="cat-badge-sub">{{ $cData['percent'] }}% (B:{{ $cData['boys'] }} | G:{{ $cData['girls'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4. Interactive Controls Bar --}}
        <div class="cat-controls-card">
            <div class="cat-search-group">
                <span class="cat-search-icon"><i class="fa fa-search"></i></span>
                <input type="text" id="classFilterInput" class="cat-search-input" placeholder="Quick filter by class name (e.g. 10th, 12th, UKG)..." autocomplete="off">
                <button type="button" class="cat-search-clear" id="classFilterClear" style="display:none;" title="Clear filter">&times;</button>
            </div>

            <div class="cat-controls-right">
                <span class="visible-row-count" id="visibleRowCount">Showing {{ $classCount }} of {{ $classCount }} Classes</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn-view-mode active" id="btnViewDetailed" title="Show Detailed Boys, Girls and Total breakdown">
                        <i class="fa fa-table"></i> Detailed Matrix
                    </button>
                    <button type="button" class="btn-view-mode" id="btnViewCompact" title="Show Compact Category Totals Only">
                        <i class="fa fa-compress"></i> Category Totals
                    </button>
                </div>
            </div>
        </div>

        {{-- 5. Main Matrix Card & Sticky Table --}}
        <div class="cat-matrix-card">
            <div class="cat-matrix-card-header">
                <h3 class="matrix-header-title">
                    <i class="fa fa-cubes text-warning mr-1"></i> Class-Wise Category Enrollment Matrix
                </h3>
                <span class="matrix-header-count">
                    {{ $sessionLabel }} &bull; {{ number_format($totalStudents) }} Students Enrolled
                </span>
            </div>

            <div class="table-responsive table-responsive-matrix" id="reportPrintArea">
                <table class="cat-matrix-table" id="categoryMatrixTable">
                    <thead>
                        {{-- Row 1: Category Headings --}}
                        <tr>
                            <th rowspan="2" class="col-sticky-sno">#</th>
                            <th rowspan="2" class="col-sticky-class">Class Name</th>

                            @foreach ($categories as $cat)
                                @php
                                    $thCatClass = 'th-cat-' . strtolower($cat);
                                    if (!in_array($cat, ['GENERAL', 'OBC', 'SC', 'ST', 'BC', 'SBC', 'OTHER'])) {
                                        $thCatClass = 'th-cat-other';
                                    }
                                @endphp
                                <th colspan="3" class="cat-group-header {{ $thCatClass }}" data-cat-header="{{ $cat }}">
                                    <span>{{ $cat }}</span>
                                </th>
                            @endforeach

                            <th colspan="3" class="th-cat-total">
                                <span>TOTAL ENROLLMENT</span>
                            </th>
                        </tr>

                        {{-- Row 2: Sub-columns (Boys, Girls, Total) --}}
                        <tr>
                            @foreach ($categories as $cat)
                                <th class="th-sub-boys cat-sub-col" data-cat-col="{{ $cat }}">Boys</th>
                                <th class="th-sub-girls cat-sub-col" data-cat-col="{{ $cat }}">Girls</th>
                                <th class="th-sub-total" data-cat-col="{{ $cat }}">Total</th>
                            @endforeach

                            <th class="th-sub-boys cat-sub-col" data-cat-col="GRAND">Boys</th>
                            <th class="th-sub-girls cat-sub-col" data-cat-col="GRAND">Girls</th>
                            <th class="th-sub-total" style="background:#001f3d !important; color:#38bdf8 !important;">Grand Total</th>
                        </tr>
                    </thead>

                    <tbody id="matrixTableBody">
                        @forelse($classType as $type)
                            @php
                                $summary = $classTotals[$type->id] ?? ['boys' => 0, 'girls' => 0, 'total' => 0, 'categories' => []];
                                $hasStudents = ($summary['total'] ?? 0) > 0;
                            @endphp
                            <tr class="matrix-class-row" data-class-name="{{ strtolower($type->name ?? '') }}">
                                <td class="col-sticky-sno font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td class="col-sticky-class">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ $type->name ?? 'Class #' . $type->id }}</span>
                                        @if($summary['total'] > 0)
                                            <span class="badge badge-light border text-dark font-weight-normal px-1 py-0" style="font-size: 9.5px;">
                                                {{ $summary['total'] }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Category cells --}}
                                @foreach ($categories as $cat)
                                    @php
                                        $cell = $summary['categories'][$cat] ?? ['boys' => 0, 'girls' => 0, 'total' => 0];
                                        $bVal = (int) ($cell['boys'] ?? 0);
                                        $gVal = (int) ($cell['girls'] ?? 0);
                                        $tVal = (int) ($cell['total'] ?? 0);
                                    @endphp
                                    <td class="cat-sub-col cell-boys {{ $bVal == 0 ? 'cell-zero' : '' }}" data-cat-col="{{ $cat }}">
                                        {{ $bVal > 0 ? $bVal : '-' }}
                                    </td>
                                    <td class="cat-sub-col cell-girls {{ $gVal == 0 ? 'cell-zero' : '' }}" data-cat-col="{{ $cat }}">
                                        {{ $gVal > 0 ? $gVal : '-' }}
                                    </td>
                                    <td class="cell-cat-total {{ $tVal == 0 ? 'cell-zero' : '' }}" data-cat-col="{{ $cat }}">
                                        {{ $tVal > 0 ? $tVal : '-' }}
                                    </td>
                                @endforeach

                                {{-- Class Grand Total --}}
                                @php
                                    $totB = (int) ($summary['boys'] ?? 0);
                                    $totG = (int) ($summary['girls'] ?? 0);
                                    $totAll = (int) ($summary['total'] ?? 0);
                                @endphp
                                <td class="cat-sub-col cell-boys cell-grand-total {{ $totB == 0 ? 'cell-zero' : '' }}" data-cat-col="GRAND">
                                    <b>{{ $totB > 0 ? $totB : '-' }}</b>
                                </td>
                                <td class="cat-sub-col cell-girls cell-grand-total {{ $totG == 0 ? 'cell-zero' : '' }}" data-cat-col="GRAND">
                                    <b>{{ $totG > 0 ? $totG : '-' }}</b>
                                </td>
                                <td class="cell-grand-total text-dark" style="background:#e2e8f0; font-size: 12px;" data-cat-col="GRAND">
                                    <b>{{ $totAll > 0 ? $totAll : '-' }}</b>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + (count($categories) * 3) + 3 }}" class="text-center py-5 text-muted">
                                    <i class="fa fa-folder-open-o fa-2x mb-2 d-block text-secondary"></i>
                                    <b>No class records found for the active session.</b>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <td class="col-sticky-sno">&Sigma;</td>
                            <td class="col-sticky-class">GRAND TOTAL</td>

                            @foreach ($categories as $cat)
                                @php
                                    $cData = $categoryTotals[$cat] ?? ['boys' => 0, 'girls' => 0, 'total' => 0];
                                @endphp
                                <td class="cat-sub-col text-info" data-cat-col="{{ $cat }}">{{ $cData['boys'] }}</td>
                                <td class="cat-sub-col text-danger" data-cat-col="{{ $cat }}">{{ $cData['girls'] }}</td>
                                <td class="total-highlight" data-cat-col="{{ $cat }}">{{ $cData['total'] }}</td>
                            @endforeach

                            <td class="cat-sub-col text-info" data-cat-col="GRAND">{{ $totalBoys }}</td>
                            <td class="cat-sub-col text-danger" data-cat-col="GRAND">{{ $totalGirls }}</td>
                            <td class="total-highlight" style="background:#0f3460 !important; color:#ffffff !important; font-size:13.5px;" data-cat-col="GRAND">
                                {{ $totalStudents }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    'use strict';

    function initCategoryReport() {
        var searchInput = document.getElementById('classFilterInput');
        var clearBtn = document.getElementById('classFilterClear');
        var rowCountEl = document.getElementById('visibleRowCount');
        var rows = document.querySelectorAll('.matrix-class-row');
        var totalRows = rows.length;

        // 1. Live Class Name Search Filter
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                var query = this.value.trim().toLowerCase();
                if (clearBtn) {
                    clearBtn.style.display = query.length > 0 ? 'inline-block' : 'none';
                }

                var visibleCount = 0;
                rows.forEach(function(row) {
                    var className = row.getAttribute('data-class-name') || '';
                    if (query === '' || className.indexOf(query) !== -1) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (rowCountEl) {
                    rowCountEl.textContent = 'Showing ' + visibleCount + ' of ' + totalRows + ' Classes';
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                rows.forEach(function(r) { r.style.display = ''; });
                if (rowCountEl) rowCountEl.textContent = 'Showing ' + totalRows + ' of ' + totalRows + ' Classes';
                searchInput.focus();
            });
        }

        // 2. Interactive Category Highlighting
        var badgeCards = document.querySelectorAll('.cat-badge-card');
        badgeCards.forEach(function(badge) {
            badge.addEventListener('click', function() {
                var category = this.getAttribute('data-category');

                badgeCards.forEach(function(b) { b.classList.remove('active-filter'); });
                this.classList.add('active-filter');

                var allGroupHeaders = document.querySelectorAll('[data-cat-header]');
                var allGroupCols = document.querySelectorAll('[data-cat-col]');

                if (category === 'ALL') {
                    allGroupHeaders.forEach(function(h) { h.classList.remove('cat-col-highlight'); });
                    allGroupCols.forEach(function(c) { c.classList.remove('cat-col-highlight'); });
                } else {
                    allGroupHeaders.forEach(function(h) {
                        if (h.getAttribute('data-cat-header') === category) {
                            h.classList.add('cat-col-highlight');
                        } else {
                            h.classList.remove('cat-col-highlight');
                        }
                    });
                    allGroupCols.forEach(function(c) {
                        if (c.getAttribute('data-cat-col') === category) {
                            c.classList.add('cat-col-highlight');
                        } else {
                            c.classList.remove('cat-col-highlight');
                        }
                    });
                }
            });
        });

        // 3. Compact vs Detailed View Toggle
        var btnDetailed = document.getElementById('btnViewDetailed');
        var btnCompact = document.getElementById('btnViewCompact');
        var table = document.getElementById('categoryMatrixTable');

        if (btnCompact && btnDetailed && table) {
            btnCompact.addEventListener('click', function() {
                btnCompact.classList.add('active');
                btnDetailed.classList.remove('active');
                table.classList.add('view-compact-mode');

                // Hide Boys and Girls columns in compact mode
                document.querySelectorAll('.cat-sub-col').forEach(function(el) {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.cat-group-header').forEach(function(el) {
                    el.setAttribute('colspan', '1');
                });
            });

            btnDetailed.addEventListener('click', function() {
                btnDetailed.classList.add('active');
                btnCompact.classList.remove('active');
                table.classList.remove('view-compact-mode');

                document.querySelectorAll('.cat-sub-col').forEach(function(el) {
                    el.style.display = '';
                });
                document.querySelectorAll('.cat-group-header').forEach(function(el) {
                    el.setAttribute('colspan', '3');
                });
            });
        }

        // 4. Excel / CSV Export Client-side
        var btnExport = document.getElementById('btnExportExcel');
        if (btnExport) {
            btnExport.addEventListener('click', function() {
                exportTableToCSV('Category_Wise_Report_{{ preg_replace("/[^A-Za-z0-9]/", "_", $sessionLabel) }}_' + formatDate(new Date()) + '.csv');
            });
        }

        function exportTableToCSV(filename) {
            var csv = [];
            var table = document.getElementById('categoryMatrixTable');
            var trs = table.querySelectorAll('tr');

            // Header Row 1
            var row1 = ['S.No.', 'Class Name'];
            @foreach($categories as $cat)
                row1.push('{{ $cat }} Boys', '{{ $cat }} Girls', '{{ $cat }} Total');
            @endforeach
            row1.push('Total Boys', 'Total Girls', 'Grand Total');
            csv.push('"' + row1.join('","') + '"');

            // Body rows
            var bodyRows = table.querySelectorAll('tbody tr');
            bodyRows.forEach(function(row) {
                if (row.style.display === 'none') return;
                var rowData = [];
                var cells = row.querySelectorAll('td');
                cells.forEach(function(cell, idx) {
                    var val = cell.innerText.trim();
                    if (val === '-') val = '0';
                    val = val.replace(/\s+/g, ' ');
                    rowData.push('"' + val.replace(/"/g, '""') + '"');
                });
                if (rowData.length > 0) csv.push(rowData.join(','));
            });

            // Footer row
            var footRow = [];
            var footCells = table.querySelectorAll('tfoot td');
            footCells.forEach(function(cell) {
                var val = cell.innerText.trim();
                footRow.push('"' + val.replace(/"/g, '""') + '"');
            });
            if (footRow.length > 0) csv.push(footRow.join(','));

            var csvContent = "\uFEFF" + csv.join("\r\n");
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement("a");
            if (link.download !== undefined) {
                var url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        function formatDate(d) {
            var month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        // 5. Professional Print Layout Trigger
        var btnPrint = document.getElementById('btnPrintReport');
        if (btnPrint) {
            btnPrint.addEventListener('click', function() {
                window.print();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCategoryReport);
    } else {
        initCategoryReport();
    }
})();
</script>
@endsection

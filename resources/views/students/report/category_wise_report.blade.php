@php
    $session = DB::table('sessions')->where('id', Session::get('session_id'))->whereNull('deleted_at')->first();
    $sessionLabel = trim(($session->from_year ?? '') . '-' . ($session->to_year ?? ''));
    if (empty($sessionLabel)) {
        $sessionLabel = date('Y') . '-' . (date('Y') + 1);
    }



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

@section('title', "Category Wise Report - Session $sessionLabel")

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - CATEGORY WISE STUDENT REPORT THEME
   - Signature Dark Navy Theme (#002C54 -> #0f3460)
   - Standard Header Font Size (14px), Kicker (9px), Subtitle (10.5px)
   - Compact KPI Metrics Strip (.kpi-strip / .kpi-card)
   - Sharp Edges (border-radius: 2px - 3px)
   - Segmented Category Distribution Bar (.dist-progress-bar)
   - Dual-Thead Sticky Header & Sticky First 2 Columns
   - Real-Time Class Search Filter
   - View Mode Switcher (Detailed vs Compact)
   - Client-side Instant Excel/CSV Export & Landscape Print
   ========================================================================== */

:root {
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --border-soft: #cbd5e1;
    --border-light: #e2e8f0;
}

.report-page-layout {
    background: #eef2f6;
    color: #0f172a;
    font-size: 11.5px;
    padding: 6px 8px 16px;
}

/* 1. Hero Header Banner (Consistent with Student Performance / Stream Update) */
.perf-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.perf-hero-text {
    display: flex;
    flex-direction: column;
}
.perf-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.perf-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.perf-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* Quick Summary Stat Badges inside Hero */
.perf-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.hero-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 7px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    font-size: 10.5px;
    color: #ffffff;
}
.hero-stat-chip b {
    font-weight: 700;
    font-size: 11px;
}
.hero-stat-chip.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}
.hero-stat-chip.badge-emerald {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #d1fae5;
}
.hero-stat-chip.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}

.perf-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 26px;
    line-height: 1;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001833 !important;
}
.dash-btn-emerald {
    background: #059669;
    color: #ffffff !important;
    border-color: #059669;
}
.dash-btn-emerald:hover {
    background: #047857;
    color: #ffffff !important;
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.3);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* 2. KPI Metrics Strip (Exact Standard) */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 4px;
    margin-bottom: 4px;
    flex-shrink: 0;
}
.kpi-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.kpi-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.kpi-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #64748b;
    margin-bottom: 1px;
}
.kpi-value {
    font-size: 14.5px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}
.kpi-sub {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-sub b {
    color: #334155;
    font-weight: 700;
}
.kpi-badge {
    width: 26px;
    height: 26px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 700;
    flex-shrink: 0;
}
.kpi-badge-navy    { background: #e0f2fe; color: #002C54; }
.kpi-badge-blue    { background: #dbeafe; color: #1d4ed8; }
.kpi-badge-rose    { background: #ffe4e6; color: #e11d48; }
.kpi-badge-emerald { background: #d1fae5; color: #059669; }
.kpi-badge-amber   { background: #fef3c7; color: #d97706; }

/* 3. Category Distribution Bar & Interactive Chips */
.dist-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 8px;
    margin-bottom: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    flex-shrink: 0;
}
.dist-top-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.dist-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 5px;
}
.dist-hint {
    font-size: 9.5px;
    color: #64748b;
}

/* Progress bar */
.dist-progress-bar {
    display: flex;
    height: 8px;
    width: 100%;
    background: #f1f5f9;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 5px;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
}
.dist-segment {
    height: 100%;
    transition: width 0.3s ease;
}

/* Category Pill Badges */
.cat-badges-strip {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}
.cat-badge-card {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
    border: 1px solid transparent;
}
.cat-badge-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.cat-badge-card.active-filter {
    outline: 1.5px solid #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.25);
}
.cat-badge-name {
    font-weight: 800;
    letter-spacing: 0.2px;
}
.cat-badge-count {
    background: rgba(255, 255, 255, 0.85);
    padding: 0 4px;
    border-radius: 2px;
    font-weight: 800;
    color: #0f172a;
    font-size: 10px;
}
.cat-badge-sub {
    font-size: 9px;
    color: #475569;
}

/* 4. Filter Toolbar */
.filter-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 4px 8px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.cat-search-group {
    display: flex;
    align-items: center;
    position: relative;
    width: 260px;
    max-width: 100%;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 0 8px;
    height: 25px;
}
.cat-search-group:focus-within {
    background: #ffffff;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.cat-search-icon {
    color: #64748b;
    font-size: 11px;
    margin-right: 5px;
}
.cat-search-input {
    border: none;
    background: transparent;
    outline: none;
    font-size: 11px;
    color: #0f172a;
    width: 100%;
}
.cat-search-input::placeholder {
    color: #94a3b8;
    font-size: 10.5px;
}
.cat-search-clear {
    border: none;
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
    padding: 0 2px;
    line-height: 1;
}
.cat-search-clear:hover {
    color: #ef4444;
}
.cat-controls-right {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 10.5px;
}
.btn-view-mode {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 2px 8px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    height: 23px;
    line-height: 1;
}
.btn-view-mode.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.visible-row-count {
    color: #64748b;
    font-size: 10.5px;
    font-weight: 600;
}

/* 5. Main Matrix Card & Sticky Table */
.matrix-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.dash-card-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}
.matrix-header-title,
.dash-card-header h3,
.dash-card-header .matrix-header-title {
    font-size: 12px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
    margin: 0 !important;
    color: #ffffff !important;
    line-height: 1.2 !important;
    letter-spacing: 0.2px;
}
.matrix-header-title i {
    color: #fbbf24 !important;
    font-size: 12.5px;
}
.matrix-header-title span {
    color: #ffffff !important;
}
.matrix-header-count {
    font-size: 10px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.28);
    padding: 2px 8px;
    border-radius: 2px;
    color: #ffffff !important;
    font-weight: 700;
    letter-spacing: 0.2px;
}

.table-responsive-matrix {
    max-height: calc(100vh - 275px);
    overflow: auto;
    position: relative;
}

.cat-matrix-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
    min-width: 1050px;
}

/* Sticky Headers */
.cat-matrix-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    font-size: 10.5px;
    font-weight: 700;
    text-align: center;
    padding: 4px 6px;
    border-right: 1px solid rgba(0, 0, 0, 0.08);
    border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    white-space: nowrap;
}
.cat-matrix-table thead tr:first-child th {
    top: 0;
    z-index: 11;
}
.cat-matrix-table thead tr:nth-child(2) th {
    top: 24px; /* Height of the first row */
    z-index: 10;
}

/* Category Themed Headers */
.th-cat-general { background: #e0e7ff !important; color: #3730a3 !important; border-top: 2.5px solid #6366f1 !important; }
.th-cat-obc     { background: #fef3c7 !important; color: #92400e !important; border-top: 2.5px solid #f59e0b !important; }
.th-cat-sc      { background: #f3e8ff !important; color: #6b21a8 !important; border-top: 2.5px solid #a855f7 !important; }
.th-cat-st      { background: #dcfce7 !important; color: #166534 !important; border-top: 2.5px solid #22c55e !important; }
.th-cat-bc      { background: #e0f2fe !important; color: #075985 !important; border-top: 2.5px solid #0284c7 !important; }
.th-cat-sbc     { background: #ccfbf1 !important; color: #115e59 !important; border-top: 2.5px solid #14b8a6 !important; }
.th-cat-other   { background: #f1f5f9 !important; color: #334155 !important; border-top: 2.5px solid #64748b !important; }
.th-cat-total   { background: #002C54 !important; color: #ffffff !important; border-top: 2.5px solid #38bdf8 !important; }

.th-sub-boys  { background: #eff6ff !important; color: #1d4ed8 !important; font-size: 9.5px !important; padding: 3px 5px !important; }
.th-sub-girls { background: #fff1f2 !important; color: #be123c !important; font-size: 9.5px !important; padding: 3px 5px !important; }
.th-sub-total { background: #f8fafc !important; color: #0f172a !important; font-weight: 800 !important; font-size: 9.5px !important; padding: 3px 5px !important; }

/* Sticky Left Columns: S.No (Col 1) and Class Name (Col 2) */
.col-sticky-sno {
    position: sticky !important;
    left: 0 !important;
    width: 36px;
    min-width: 36px;
    max-width: 36px;
    z-index: 8 !important;
    background: #ffffff;
    text-align: center;
    border-right: 1px solid #cbd5e1 !important;
}
.col-sticky-class {
    position: sticky !important;
    left: 36px !important;
    width: 140px;
    min-width: 140px;
    max-width: 170px;
    z-index: 8 !important;
    background: #ffffff;
    text-align: left !important;
    font-weight: 700;
    color: #0f172a;
    border-right: 2px solid #94a3b8 !important;
    box-shadow: 2px 0 4px -2px rgba(0, 0, 0, 0.08);
}

/* Sticky Header on Left Columns */
.cat-matrix-table thead tr:first-child th.col-sticky-sno,
.cat-matrix-table thead tr:first-child th.col-sticky-class {
    z-index: 15 !important;
    background: #002C54 !important;
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
    padding: 4px 6px;
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
    background: #002C54 !important;
    color: #ffffff !important;
    font-weight: 800;
    padding: 6px 6px;
    border-top: 2px solid #38bdf8;
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    text-align: center;
    font-size: 11px;
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
    font-size: 11.5px;
}

/* Highlight Selected Category Columns */
.cat-col-highlight {
    background: rgba(254, 240, 138, 0.35) !important;
}



/* Responsive adjustments */
@media (max-width: 1200px) {
    .kpi-strip {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
    .perf-hero {
        flex-direction: column;
        align-items: flex-start;
    }
    .perf-hero-actions {
        width: 100%;
        justify-content: flex-start;
    }
    .cat-search-group {
        width: 100%;
    }
}
@media (max-width: 480px) {
    .kpi-strip {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-wrapper report-page-layout">
    <div class="container-fluid p-0">



        {{-- 1. Signature Hero Header Banner --}}
        <div class="perf-hero">
            <div class="perf-hero-text">
                <span class="perf-kicker">
                    <i class="fa fa-graduation-cap"></i> Student Demographics &bull; Executive Analytics
                </span>
                <h1 class="perf-title">
                    <i class="fa fa-pie-chart text-warning"></i>
                    <span>Category Wise Report</span>
                </h1>
                <div class="perf-subtitle">
                    <span><i class="fa fa-calendar-check-o text-info"></i> Session: <b>{{ $sessionLabel }}</b></span>
                    <span>&bull;</span>
                    <span><i class="fa fa-th-large text-warning"></i> Classes: <b>{{ $classCount }}</b></span>
                    <span>&bull;</span>
                    <span><i class="fa fa-tags text-success"></i> Categories: <b>{{ count($categories) }}</b></span>
                </div>
            </div>

            <div class="perf-hero-stats">
                <div class="hero-stat-chip badge-sky">
                    <i class="fa fa-users"></i> Total: <b>{{ number_format($totalStudents) }}</b>
                </div>
                <div class="hero-stat-chip badge-emerald">
                    <i class="fa fa-male"></i> Boys: <b>{{ number_format($totalBoys) }}</b>
                </div>
                <div class="hero-stat-chip badge-amber">
                    <i class="fa fa-female"></i> Girls: <b>{{ number_format($totalGirls) }}</b>
                </div>
            </div>

            <div class="perf-hero-actions">
                <button type="button" class="dash-btn dash-btn-emerald" id="btnExportExcel" title="Download Excel (.CSV) Spreadsheet">
                    <i class="fa fa-file-excel-o"></i> <span>Export Excel</span>
                </button>


            </div>
        </div>

        {{-- 2. Compact KPI Scorecards Strip --}}
        <div class="kpi-strip">
            {{-- Total Enrollment --}}
            <div class="kpi-card">
                <div class="kpi-info">
                    <div class="kpi-label">Total Enrollment</div>
                    <div class="kpi-value">{{ number_format($totalStudents) }}</div>
                    <div class="kpi-sub">Across <b>{{ $classCount }}</b> Classes</div>
                </div>
                <div class="kpi-badge kpi-badge-navy">
                    <i class="fa fa-users"></i>
                </div>
            </div>

            {{-- Total Boys --}}
            <div class="kpi-card">
                <div class="kpi-info">
                    <div class="kpi-label">Total Boys</div>
                    <div class="kpi-value text-primary">{{ number_format($totalBoys) }}</div>
                    <div class="kpi-sub"><b>{{ $boysPercent }}%</b> of Total</div>
                </div>
                <div class="kpi-badge kpi-badge-blue">
                    <i class="fa fa-male"></i>
                </div>
            </div>

            {{-- Total Girls --}}
            <div class="kpi-card">
                <div class="kpi-info">
                    <div class="kpi-label">Total Girls</div>
                    <div class="kpi-value" style="color: #e11d48;">{{ number_format($totalGirls) }}</div>
                    <div class="kpi-sub"><b>{{ $girlsPercent }}%</b> of Total</div>
                </div>
                <div class="kpi-badge kpi-badge-rose">
                    <i class="fa fa-female"></i>
                </div>
            </div>

            {{-- Gender Ratio --}}
            <div class="kpi-card">
                <div class="kpi-info">
                    <div class="kpi-label">Gender Ratio</div>
                    <div class="kpi-value text-success">{{ $genderRatio }}</div>
                    <div class="kpi-sub">Girls / <b>1,000 Boys</b></div>
                </div>
                <div class="kpi-badge kpi-badge-emerald">
                    <i class="fa fa-balance-scale"></i>
                </div>
            </div>

            {{-- Active Classes --}}
            <div class="kpi-card">
                <div class="kpi-info">
                    <div class="kpi-label">Avg. Per Class</div>
                    <div class="kpi-value text-warning">
                        {{ $classCount > 0 ? round($totalStudents / $classCount, 1) : 0 }}
                    </div>
                    <div class="kpi-sub">Students / Class</div>
                </div>
                <div class="kpi-badge kpi-badge-amber">
                    <i class="fa fa-th-list"></i>
                </div>
            </div>
        </div>

        {{-- 3. Category Distribution Bar & Interactive Chips --}}
        <div class="dist-card">
            <div class="dist-top-line">
                <div class="dist-title">
                    <i class="fa fa-bar-chart text-primary"></i> Category Distribution Overview
                </div>
                <div class="dist-hint">
                    Click a category to highlight its columns in matrix
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

        {{-- 4. Filter Toolbar --}}
        <div class="filter-toolbar">
            <div class="cat-search-group">
                <span class="cat-search-icon"><i class="fa fa-search"></i></span>
                <input type="text" id="classFilterInput" class="cat-search-input" placeholder="Quick filter by class (e.g. 10th, 12th, UKG)..." autocomplete="off">
                <button type="button" class="cat-search-clear" id="classFilterClear" style="display:none;" title="Clear filter">&times;</button>
            </div>

            <div class="cat-controls-right">
                <span class="visible-row-count" id="visibleRowCount">Showing {{ $classCount }} of {{ $classCount }} Classes</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn-view-mode active" id="btnViewDetailed" title="Show Detailed Boys, Girls and Total breakdown">
                        <i class="fa fa-table"></i> Detailed
                    </button>
                    <button type="button" class="btn-view-mode" id="btnViewCompact" title="Show Compact Category Totals Only">
                        <i class="fa fa-compress"></i> Compact
                    </button>
                </div>
            </div>
        </div>

        {{-- 5. Main Matrix Card & Sticky Table --}}
        <div class="matrix-card">
            <div class="dash-card-header">
                <div class="matrix-header-title">
                    <i class="fa fa-cubes text-warning mr-1"></i>
                    <span>Class-Wise Category Enrollment Matrix</span>
                </div>
                <span class="matrix-header-count">
                    {{ $sessionLabel }} &bull; {{ number_format($totalStudents) }} Students Enrolled
                </span>
            </div>

            <div class="table-responsive table-responsive-matrix">
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
                            @endphp
                            <tr class="matrix-class-row" data-class-name="{{ strtolower($type->name ?? '') }}">
                                <td class="col-sticky-sno font-weight-bold text-muted">{{ $loop->iteration }}</td>
                                <td class="col-sticky-class">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>{{ $type->name ?? 'Class #' . $type->id }}</span>
                                        @if(($summary['total'] ?? 0) > 0)
                                            <span class="badge badge-light border text-dark font-weight-normal px-1 py-0" style="font-size: 9px;">
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
                                <td class="cell-grand-total text-dark" style="background:#e2e8f0; font-size: 11.5px;" data-cat-col="GRAND">
                                    <b>{{ $totAll > 0 ? $totAll : '-' }}</b>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + (count($categories) * 3) + 3 }}" class="text-center py-4 text-muted">
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
                            <td class="total-highlight" style="background:#0f3460 !important; color:#ffffff !important; font-size:12px;" data-cat-col="GRAND">
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

            var row1 = ['S.No.', 'Class Name'];
            @foreach($categories as $cat)
                row1.push('{{ $cat }} Boys', '{{ $cat }} Girls', '{{ $cat }} Total');
            @endforeach
            row1.push('Total Boys', 'Total Girls', 'Grand Total');
            csv.push('"' + row1.join('","') + '"');

            var bodyRows = table.querySelectorAll('tbody tr');
            bodyRows.forEach(function(row) {
                if (row.style.display === 'none') return;
                var rowData = [];
                var cells = row.querySelectorAll('td');
                cells.forEach(function(cell) {
                    var val = cell.innerText.trim();
                    if (val === '-') val = '0';
                    val = val.replace(/\s+/g, ' ');
                    rowData.push('"' + val.replace(/"/g, '""') + '"');
                });
                if (rowData.length > 0) csv.push(rowData.join(','));
            });

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


    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCategoryReport);
    } else {
        initCategoryReport();
    }
})();
</script>
@endsection

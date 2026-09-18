@extends('layout.app')
@section('content')

@include('attendance.theme')

@php
    $rolesMap = $staffRoles->pluck('name', 'id')->toArray();
    $prevM = $month == 1 ? 12 : $month - 1;
    $prevY = $month == 1 ? $year - 1 : $year;
    $nextM = $month == 12 ? 1 : $month + 1;
    $nextY = $month == 12 ? $year + 1 : $year;
    
    $prevUrl = url('monthlyReport') . '?month=' . $prevM . '&year=' . $prevY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '');
    $nextUrl = url('monthlyReport') . '?month=' . $nextM . '&year=' . $nextY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '');
    $exportUrl = url('monthlyReport') . '?month=' . $month . '&year=' . $year . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '') . '&export=1';

    // Daily totals collector
    $dailyPresentTotals = array_fill(1, $daysInMonth, 0);
    $dailyAbsentTotals = array_fill(1, $daysInMonth, 0);
    $dailyHalfDayTotals = array_fill(1, $daysInMonth, 0);
    $dailyHolidayTotals = array_fill(1, $daysInMonth, 0);

    foreach ($users as $u) {
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $m = $attendanceData[$u->id][$d] ?? null;
            if ($m) {
                if (in_array($m->status, ['present', 'in', 'out'])) {
                    $dailyPresentTotals[$d]++;
                } elseif ($m->status === 'absent') {
                    $dailyAbsentTotals[$d]++;
                } elseif ($m->status === 'halfday') {
                    $dailyHalfDayTotals[$d]++;
                } elseif ($m->status === 'holiday') {
                    $dailyHolidayTotals[$d]++;
                }
            }
        }
    }
@endphp

<style>
    /* Viewport-height locked container */
    .monthly-matrix-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-height, 56px) - 16px);
        min-height: 520px;
        gap: 8px;
        overflow: hidden;
    }

    /* KPI Summary Strip */
    .kpi-strip {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 6px;
        flex-shrink: 0;
    }
    @media (max-width: 1200px) {
        .kpi-strip {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .kpi-mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 2px;
        padding: 5px 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .kpi-mini-card .kpi-label {
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 1px;
    }
    .kpi-mini-card .kpi-val {
        font-size: 13.5px;
        font-weight: 800;
        line-height: 1.1;
    }
    .kpi-mini-card .kpi-icon {
        width: 26px;
        height: 26px;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
    }

    /* Compact Filter Toolbar */
    .matrix-filter-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 2px;
        padding: 5px 10px;
        flex-shrink: 0;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .matrix-filter-card .form-control {
        height: 28px !important;
        font-size: 11.5px !important;
        padding: 2px 8px !important;
        border-radius: 2px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff;
        color: #1e293b;
    }
    .matrix-filter-card .btn {
        height: 28px !important;
        font-size: 11.5px !important;
        padding: 0 10px !important;
        border-radius: 2px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 26px;
    }
    .btn-filter-load {
        background: #002C54 !important;
        border: 1px solid #001f3f !important;
        color: #ffffff !important;
    }
    .btn-filter-load:hover {
        background: #0f3460 !important;
        color: #ffffff !important;
    }

    /* Search Box styling with parallel icon */
    .search-input-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        width: 200px;
    }
    .search-input-wrapper .search-icon {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 11px;
        color: #64748b;
        pointer-events: none;
        z-index: 5;
    }
    .search-input-wrapper .search-input-field {
        padding-left: 26px !important;
        width: 100%;
    }

    /* Month Navigator in Header */
    .hero-nav-btn {
        background: rgba(255, 255, 255, 0.16) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        color: #ffffff !important;
        font-size: 11px !important;
        padding: 3px 8px !important;
    }
    .hero-nav-btn:hover {
        background: rgba(255, 255, 255, 0.28) !important;
        color: #ffffff !important;
    }
    .hero-nav-display {
        background: rgba(255, 255, 255, 0.22) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-left: none !important;
        border-right: none !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 11px !important;
        padding: 3px 10px !important;
        cursor: default;
    }

    /* Matrix Table Container */
    .matrix-table-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 2px;
        flex: 1 1 0%;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .matrix-table-scroll {
        flex: 1 1 0%;
        min-height: 0;
        overflow: auto;
        position: relative;
    }
    .matrix-table-scroll::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }
    .matrix-table-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .matrix-table-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Matrix Table Styling */
    .table-matrix {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11px;
        white-space: nowrap;
        margin-bottom: 0;
    }
    .table-matrix th,
    .table-matrix td {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        padding: 3px 4px;
        text-align: center;
        vertical-align: middle;
    }

    /* Sticky Headers (Top) - Dark Background with Light White Font */
    .table-matrix thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #002C54 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 10.5px;
        line-height: 1.2;
        border-right: 1px solid #1a3a60;
        border-bottom: 2px solid #0f3460;
        padding: 4px 2px;
    }

    /* Sticky Left Columns */
    .sticky-col-idx {
        position: sticky;
        left: 0;
        width: 36px;
        min-width: 36px;
        max-width: 36px;
        z-index: 10;
        background: #ffffff;
    }
    .sticky-col-id {
        position: sticky;
        left: 36px;
        width: 70px;
        min-width: 70px;
        max-width: 70px;
        z-index: 10;
        background: #ffffff;
    }
    .sticky-col-name {
        position: sticky;
        left: 106px;
        width: 175px;
        min-width: 175px;
        max-width: 175px;
        z-index: 10;
        background: #ffffff;
        text-align: left !important;
        padding-left: 8px !important;
        box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.08);
    }
    .table-matrix thead th.sticky-col-idx,
    .table-matrix thead th.sticky-col-id,
    .table-matrix thead th.sticky-col-name {
        z-index: 30;
        background: #002C54 !important;
        color: #ffffff !important;
    }

    /* Sticky Right Summary Columns */
    .sticky-col-sum-p {
        position: sticky;
        right: 148px;
        width: 34px;
        min-width: 34px;
        z-index: 10;
        background: #f0fdf4;
        font-weight: 700;
        color: #166534;
        box-shadow: -3px 0 6px -2px rgba(0, 0, 0, 0.06);
    }
    .sticky-col-sum-a {
        position: sticky;
        right: 114px;
        width: 34px;
        min-width: 34px;
        z-index: 10;
        background: #fef2f2;
        font-weight: 700;
        color: #991b1b;
    }
    .sticky-col-sum-hd {
        position: sticky;
        right: 80px;
        width: 34px;
        min-width: 34px;
        z-index: 10;
        background: #faf5ff;
        font-weight: 700;
        color: #7e22ce;
    }
    .sticky-col-sum-h {
        position: sticky;
        right: 46px;
        width: 34px;
        min-width: 34px;
        z-index: 10;
        background: #fffbeb;
        font-weight: 700;
        color: #b45309;
    }
    .sticky-col-sum-rate {
        position: sticky;
        right: 0;
        width: 46px;
        min-width: 46px;
        z-index: 10;
        background: #f8fafc;
        font-weight: 800;
    }
    .table-matrix thead th.sticky-col-sum-p,
    .table-matrix thead th.sticky-col-sum-a,
    .table-matrix thead th.sticky-col-sum-hd,
    .table-matrix thead th.sticky-col-sum-h,
    .table-matrix thead th.sticky-col-sum-rate {
        z-index: 30;
        background: #002C54 !important;
        color: #ffffff !important;
    }

    /* Day Columns */
    .day-col {
        width: 31px;
        min-width: 31px;
        max-width: 31px;
        padding: 2px 1px !important;
        cursor: pointer;
    }
    .day-header-sunday {
        background: #991b1b !important;
        color: #ffffff !important;
        border-right: 1px solid #7f1d1d !important;
    }
    .day-cell-sunday {
        background-color: #fff1f2 !important;
    }
    .day-cell-holiday {
        background-color: #fef9c3 !important;
    }

    /* Matrix Badges */
    .badge-matrix {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 20px;
        font-size: 10px;
        font-weight: 700;
        border-radius: 2px;
        line-height: 1;
        cursor: pointer;
        transition: transform 0.1s ease;
    }
    .badge-matrix:hover {
        transform: scale(1.15);
    }
    .badge-p {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }
    .badge-a {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .badge-hd {
        background: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #d8b4fe;
    }
    .badge-h {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-w {
        background: #ffe4e6;
        color: #e11d48;
        font-size: 8.5px;
        font-weight: 800;
        border: 1px solid #fecdd3;
    }
    .badge-empty {
        color: #cbd5e1;
        font-weight: 600;
        font-size: 11px;
    }

    /* Row Hover & Selection */
    .table-matrix tbody tr:hover td {
        background-color: #f8fafc;
    }
    .table-matrix tbody tr:hover .sticky-col-idx,
    .table-matrix tbody tr:hover .sticky-col-id,
    .table-matrix tbody tr:hover .sticky-col-name {
        background-color: #f1f5f9 !important;
    }
    .table-matrix tbody tr.row-highlighted td {
        background-color: #e0f2fe !important;
    }
    .table-matrix tbody tr.row-highlighted .sticky-col-idx,
    .table-matrix tbody tr.row-highlighted .sticky-col-id,
    .table-matrix tbody tr.row-highlighted .sticky-col-name {
        background-color: #bae6fd !important;
    }

    /* Sticky Footer - High Contrast Light Text on Dark Navy */
    .table-matrix tfoot tr th {
        position: sticky;
        bottom: 0;
        z-index: 20;
        background: #002C54 !important;
        color: #ffffff !important;
        font-weight: 800;
        font-size: 10.5px;
        border-top: 2px solid #0f3460;
        border-right: 1px solid #1a3a60;
        padding: 4px 2px;
    }
    .table-matrix tfoot tr th.sticky-col-idx,
    .table-matrix tfoot tr th.sticky-col-id,
    .table-matrix tfoot tr th.sticky-col-name {
        z-index: 30;
        background: #002C54 !important;
        color: #ffffff !important;
    }
    .table-matrix tfoot tr th.day-header-sunday {
        background: #991b1b !important;
        color: #ffffff !important;
    }
    .table-matrix tfoot tr th.sticky-col-sum-p {
        z-index: 30;
        background: #001f3f !important;
        color: #86efac !important; /* Vibrant Light Green */
    }
    .table-matrix tfoot tr th.sticky-col-sum-a {
        z-index: 30;
        background: #001f3f !important;
        color: #fca5a5 !important; /* Vibrant Light Coral */
    }
    .table-matrix tfoot tr th.sticky-col-sum-hd {
        z-index: 30;
        background: #001f3f !important;
        color: #d8b4fe !important; /* Vibrant Light Purple */
    }
    .table-matrix tfoot tr th.sticky-col-sum-h {
        z-index: 30;
        background: #001f3f !important;
        color: #fde68a !important; /* Vibrant Light Amber */
    }
    .table-matrix tfoot tr th.sticky-col-sum-rate {
        z-index: 30;
        background: #001f3f !important;
        color: #ffffff !important;
    }

    /* Bottom Status Bar */
    .matrix-bottom-bar {
        background: #002C54 !important;
        border-top: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #e2e8f0 !important;
        font-size: 11px !important;
        padding: 5px 12px !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .matrix-bottom-bar strong,
    .matrix-bottom-bar .bottom-highlight {
        color: #ffffff !important;
        font-weight: 700;
    }
    .matrix-bottom-bar .bottom-badge {
        color: #93c5fd !important;
        font-weight: 800;
    }
    .matrix-bottom-bar .bottom-tip {
        color: #cbd5e1 !important;
    }

    /* Staff info styling */
    .staff-avatar-mini {
        width: 20px;
        height: 20px;
        border-radius: 2px;
        background: #002C54;
        color: #ffffff !important;
        font-size: 9.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        flex-shrink: 0;
    }
    .staff-name-wrap {
        display: inline-flex;
        align-items: center;
        max-width: 145px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 700;
        color: #0f172a;
    }
    .staff-role-badge {
        font-size: 9px;
        font-weight: 600;
        background: #e2e8f0;
        color: #475569;
        padding: 1px 4px;
        border-radius: 2px;
        margin-left: 4px;
    }

    /* Rate Pill */
    .rate-pill {
        display: inline-block;
        font-size: 9.5px;
        font-weight: 800;
        padding: 1px 4px;
        border-radius: 2px;
    }
    .rate-pill-high { background: #dcfce7; color: #166534; }
    .rate-pill-mid { background: #fef9c3; color: #854d0e; }
    .rate-pill-low { background: #fee2e2; color: #991b1b; }

    /* Legend Bar */
    .legend-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 10.5px;
        color: #475569;
        flex-wrap: wrap;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Custom Floating Tooltip Card */
    #matrixHoverTooltip {
        position: fixed;
        display: none;
        z-index: 9999;
        pointer-events: none;
        background: linear-gradient(145deg, #002C54 0%, #0f3460 100%);
        color: #ffffff;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 11px;
        line-height: 1.4;
        box-shadow: 0 8px 24px rgba(0, 44, 84, 0.35), 0 2px 6px rgba(0,0,0,0.2);
        border: 1px solid rgba(255, 255, 255, 0.2);
        min-width: 170px;
        max-width: 260px;
        backdrop-filter: blur(4px);
        transform: translate(-50%, -100%);
        margin-top: -10px;
        transition: opacity 0.12s ease;
    }
    #matrixHoverTooltip .tip-header {
        font-size: 11px;
        font-weight: 700;
        color: #ffffff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding-bottom: 4px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    #matrixHoverTooltip .tip-staff {
        font-size: 10.5px;
        font-weight: 600;
        color: #93c5fd;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #matrixHoverTooltip .tip-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 10px;
        color: #e2e8f0;
        margin-top: 2px;
    }
    #matrixHoverTooltip .tip-badge {
        display: inline-block;
        font-size: 9px;
        font-weight: 800;
        padding: 1px 6px;
        border-radius: 2px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    #matrixHoverTooltip .tip-badge-p { background: #166534; color: #dcfce7; }
    #matrixHoverTooltip .tip-badge-a { background: #991b1b; color: #fee2e2; }
    #matrixHoverTooltip .tip-badge-hd { background: #6b21a8; color: #f3e8ff; }
    #matrixHoverTooltip .tip-badge-h { background: #92400e; color: #fef3c7; }
    #matrixHoverTooltip .tip-badge-w { background: #9f1239; color: #ffe4e6; }
    #matrixHoverTooltip .tip-badge-empty { background: #334155; color: #cbd5e1; }

    /* Print Styles */
    @media print {
        .monthly-matrix-wrapper {
            height: auto !important;
            overflow: visible !important;
        }
        .attendance-hero,
        .matrix-filter-card,
        .kpi-strip,
        .main-sidebar,
        .main-header,
        .main-footer,
        .btn-no-print {
            display: none !important;
        }
        .matrix-table-card,
        .matrix-table-scroll {
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
        }
        .table-matrix th,
        .table-matrix td {
            font-size: 8px !important;
            padding: 1px 2px !important;
        }
    }
</style>

<div class="content-wrapper attendance-page">
    <section class="content pt-2 pb-1">
        <div class="container-fluid px-2">
            
            <div class="monthly-matrix-wrapper">

                <!-- 1. Signature Hero Header Banner (Dark Navy with Pure White/Light Text) -->
                <div class="attendance-hero">
                    <div class="attendance-hero-inner">
                        <div>
                            <div class="attendance-hero-kicker" style="color: #93c5fd !important;">
                                <i class="fa fa-th mr-1"></i> Attendance Register Matrix
                            </div>
                            <h1 class="attendance-hero-title" style="color: #ffffff !important;">
                                <i class="fa fa-calendar-check-o mr-1"></i> {{ $monthName }} {{ $year }} Staff Attendance Register
                            </h1>
                            <p class="attendance-hero-subtitle" style="color: #e2e8f0 !important;">
                                Comprehensive day-wise staff attendance log with 2-directional sticky navigation, live search, and 1-click export.
                            </p>
                        </div>
                        <div class="attendance-hero-actions">
                            <!-- Quick Month Switcher with High-Contrast White Font -->
                            <div class="btn-group btn-group-sm">
                                <a href="{{ $prevUrl }}" class="btn hero-nav-btn" title="Previous Month ({{ date('F', mktime(0,0,0,$prevM,1)) }})">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                                <span class="hero-nav-display">
                                    {{ $monthName }} {{ $year }}
                                </span>
                                <a href="{{ $nextUrl }}" class="btn hero-nav-btn" title="Next Month ({{ date('F', mktime(0,0,0,$nextM,1)) }})">
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            </div>

                            <a href="{{ $exportUrl }}" class="btn btn-success btn-sm font-weight-bold text-white" title="Download Excel/CSV Spreadsheet">
                                <i class="fa fa-download mr-1"></i> Export CSV
                            </a>
                            <button type="button" class="btn btn-light btn-sm font-weight-bold btn-no-print" onclick="window.print()" title="Print Register">
                                <i class="fa fa-print mr-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. KPI Summary Cards Strip -->
                <div class="kpi-strip">
                    <!-- Total Staff -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Total Staff</div>
                            <div class="kpi-val text-dark" id="kpiTotalStaff">{{ $overallStats['totalStaff'] ?? $users->count() }}</div>
                        </div>
                        <div class="kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fa fa-users"></i>
                        </div>
                    </div>

                    <!-- Total Present -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Total Present</div>
                            <div class="kpi-val text-success">{{ number_format($overallStats['totalPresent'] ?? 0) }}</div>
                        </div>
                        <div class="kpi-icon" style="background: #dcfce7; color: #16a34a;">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>

                    <!-- Total Absent -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Total Absent</div>
                            <div class="kpi-val text-danger">{{ number_format($overallStats['totalAbsent'] ?? 0) }}</div>
                        </div>
                        <div class="kpi-icon" style="background: #fee2e2; color: #dc2626;">
                            <i class="fa fa-times-circle"></i>
                        </div>
                    </div>

                    <!-- Total Half Days -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Half Days</div>
                            <div class="kpi-val text-purple" style="color: #9333ea;">{{ number_format($overallStats['totalHalfDay'] ?? 0) }}</div>
                        </div>
                        <div class="kpi-icon" style="background: #f3e8ff; color: #9333ea;">
                            <i class="fa fa-adjust"></i>
                        </div>
                    </div>

                    <!-- Total Holidays -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Holidays</div>
                            <div class="kpi-val text-warning" style="color: #d97706;">{{ number_format($overallStats['totalHoliday'] ?? 0) }}</div>
                        </div>
                        <div class="kpi-icon" style="background: #fef3c7; color: #d97706;">
                            <i class="fa fa-star"></i>
                        </div>
                    </div>

                    <!-- Avg Attendance Rate -->
                    <div class="kpi-mini-card">
                        <div>
                            <div class="kpi-label">Avg Attendance</div>
                            <div class="kpi-val {{ ($overallStats['avgAttendanceRate'] ?? 0) >= 80 ? 'text-success' : (($overallStats['avgAttendanceRate'] ?? 0) >= 60 ? 'text-warning' : 'text-danger') }}">
                                {{ $overallStats['avgAttendanceRate'] ?? 0 }}%
                            </div>
                        </div>
                        <div class="kpi-icon" style="background: #ecfeff; color: #0891b2;">
                            <i class="fa fa-line-chart"></i>
                        </div>
                    </div>
                </div>

                <!-- 3. Compact Filter Toolbar & Live Search (Parallel Centered Icons) -->
                <div class="matrix-filter-card">
                    <form action="{{ url('monthlyReport') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between g-2 m-0" style="min-height: 30px;">
                        
                        <!-- Left: Form Controls -->
                        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                            @if(Session::get('role_id') == 1 && $staffRoles->isNotEmpty())
                            <!-- Role Dropdown -->
                            <div style="min-width: 140px;">
                                <select name="role_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Roles ({{ $staffRoles->count() }})</option>
                                    @foreach($staffRoles as $role)
                                        <option value="{{ $role->id }}" {{ (string)$selectedRoleId === (string)$role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Month Picker -->
                            <div style="width: 110px;">
                                <select name="month" class="form-control" onchange="this.form.submit()">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Year Picker -->
                            <div style="width: 85px;">
                                <select name="year" class="form-control" onchange="this.form.submit()">
                                    @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit" class="btn btn-filter-load font-weight-bold">
                                <i class="fa fa-filter mr-1 text-white"></i> Load
                            </button>
                            
                            <a href="{{ url('monthlyReport') }}" class="btn btn-outline-secondary" title="Reset to Current Month">
                                <i class="fa fa-refresh mr-1"></i> Reset
                            </a>
                        </div>

                        <!-- Right: Parallel Search Input Box & Legend -->
                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                            
                            <!-- Search Box with Parallel Icon Inside -->
                            <div class="search-input-wrapper">
                                <i class="fa fa-search search-icon"></i>
                                <input type="text" id="staffSearchInput" class="form-control search-input-field" placeholder="Filter staff by name or ID...">
                            </div>

                            <!-- Legend Badges -->
                            <div class="legend-bar d-none d-lg-flex">
                                <div class="legend-item"><span class="badge-matrix badge-p">P</span> Present</div>
                                <div class="legend-item"><span class="badge-matrix badge-a">A</span> Absent</div>
                                <div class="legend-item"><span class="badge-matrix badge-hd">HD</span> Half Day</div>
                                <div class="legend-item"><span class="badge-matrix badge-h">H</span> Holiday</div>
                                <div class="legend-item"><span class="badge-matrix badge-w">SUN</span> Sunday</div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- 4. Main 2-Directional Sticky Matrix Grid -->
                <div class="matrix-table-card">
                    <div class="matrix-table-scroll" id="matrixScrollArea">
                        <table class="table-matrix" id="monthlyReportTable">
                            <thead>
                                <tr>
                                    <th class="sticky-col-idx">#</th>
                                    <th class="sticky-col-id">Staff ID</th>
                                    <th class="sticky-col-name">Staff Name & Role</th>
                                    
                                    <!-- Day Columns 1..N with hover tooltips -->
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $dateStr = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $i);
                                            $dayOfWeek = date('w', strtotime($dateStr)); // 0 = Sunday
                                            $dayShort = date('D', strtotime($dateStr));
                                            $dayFull = date('l, d F Y', strtotime($dateStr));
                                            $isSunday = ($dayOfWeek == 0);
                                            $presCount = $dailyPresentTotals[$i] ?? 0;
                                        @endphp
                                        <th class="day-col {{ $isSunday ? 'day-header-sunday' : '' }} matrix-hover-target" 
                                            data-type="header"
                                            data-date="{{ $dayFull }}"
                                            data-dayname="{{ $dayShort }}"
                                            data-daynum="{{ sprintf('%02d', $i) }}"
                                            data-issunday="{{ $isSunday ? '1' : '0' }}"
                                            data-present="{{ $presCount }}"
                                            data-totalstaff="{{ $users->count() }}">
                                            <div style="font-size: 8.5px; opacity: 0.9; font-weight: 600; color: #ffffff !important;">{{ $dayShort }}</div>
                                            <div style="font-size: 11px; font-weight: 800; color: #ffffff !important;">{{ sprintf('%02d', $i) }}</div>
                                        </th>
                                    @endfor

                                    <!-- Summary Columns (Sticky Right) -->
                                    <th class="sticky-col-sum-p" title="Total Present Days">P</th>
                                    <th class="sticky-col-sum-a" title="Total Absent Days">A</th>
                                    <th class="sticky-col-sum-hd" title="Total Half Days">HD</th>
                                    <th class="sticky-col-sum-h" title="Total Holidays">H</th>
                                    <th class="sticky-col-sum-rate" title="Attendance Percentage">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $idx => $user)
                                    @php
                                        $userRoleName = $rolesMap[$user->role_id] ?? 'Staff';
                                        $userSum = $summaryData[$user->id] ?? ['present' => 0, 'absent' => 0, 'halfday' => 0, 'holiday' => 0];
                                        $pCount = $userSum['present'] ?? 0;
                                        $aCount = $userSum['absent'] ?? 0;
                                        $hdCount = $userSum['halfday'] ?? 0;
                                        $hCount = $userSum['holiday'] ?? 0;
                                        
                                        $workingCount = $pCount + $aCount + $hdCount;
                                        $userRate = $workingCount > 0 ? round((($pCount + ($hdCount * 0.5)) / $workingCount) * 100) : 0;
                                        $rateClass = $userRate >= 80 ? 'rate-pill-high' : ($userRate >= 60 ? 'rate-pill-mid' : 'rate-pill-low');

                                        $initials = strtoupper(substr($user->first_name ?? 'U', 0, 1));
                                    @endphp
                                    <tr class="staff-row" data-name="{{ strtolower($user->name) }}" data-id="{{ strtolower($user->attendance_unique_id ?? ('usr-'.$user->id)) }}" data-role="{{ strtolower($userRoleName) }}">
                                        
                                        <!-- Col 1: # -->
                                        <td class="sticky-col-idx text-muted font-weight-bold">{{ $idx + 1 }}</td>
                                        
                                        <!-- Col 2: Staff ID -->
                                        <td class="sticky-col-id">
                                            <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 10px;">
                                                {{ $user->attendance_unique_id ?? ('#' . $user->id) }}
                                            </span>
                                        </td>

                                        <!-- Col 3: Name & Role -->
                                        <td class="sticky-col-name" title="{{ $user->name }} ({{ $userRoleName }})">
                                            <div class="d-flex align-items-center">
                                                <div class="staff-avatar-mini">{{ $initials }}</div>
                                                <div class="staff-name-wrap">
                                                    {{ $user->name }}
                                                </div>
                                                <span class="staff-role-badge d-none d-md-inline-block">{{ $userRoleName }}</span>
                                            </div>
                                        </td>

                                        <!-- Day Columns 1..N with custom styled tooltip data -->
                                        @for ($i = 1; $i <= $daysInMonth; $i++)
                                            @php
                                                $dateStr = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $i);
                                                $dayOfWeek = date('w', strtotime($dateStr));
                                                $dayShort = date('D', strtotime($dateStr));
                                                $dayFull = date('l, d F Y', strtotime($dateStr));
                                                $isSunday = ($dayOfWeek == 0);
                                                $isCalHoliday = isset($holidaysMap[$dateStr]) && $holidaysMap[$dateStr] === 'holiday';
                                                
                                                $dailyData = $attendanceData[$user->id][$i] ?? null;
                                                $status = $dailyData ? strtolower((string)$dailyData->status) : null;
                                                $inTime = ($dailyData && !empty($dailyData->in_time)) ? date('h:i A', strtotime($dailyData->in_time)) : '';
                                                $outTime = ($dailyData && !empty($dailyData->out_time)) ? date('h:i A', strtotime($dailyData->out_time)) : '';

                                                $statusType = 'empty';
                                                $statusLabel = 'Not Marked';
                                                if ($dailyData) {
                                                    if (in_array($status, ['present', 'in', 'out'])) {
                                                        $statusType = 'p';
                                                        $statusLabel = 'Present';
                                                    } elseif ($status === 'absent') {
                                                        $statusType = 'a';
                                                        $statusLabel = 'Absent';
                                                    } elseif ($status === 'halfday') {
                                                        $statusType = 'hd';
                                                        $statusLabel = 'Half Day';
                                                    } elseif ($status === 'holiday') {
                                                        $statusType = 'h';
                                                        $statusLabel = 'Holiday';
                                                    } else {
                                                        $statusType = 'p';
                                                        $statusLabel = ucfirst($status);
                                                    }
                                                } elseif ($isSunday) {
                                                    $statusType = 'w';
                                                    $statusLabel = 'Sunday (Weekly Off)';
                                                } elseif ($isCalHoliday) {
                                                    $statusType = 'h';
                                                    $statusLabel = 'Official Holiday';
                                                }
                                            @endphp

                                            <td class="day-col {{ $isSunday ? 'day-cell-sunday' : ($isCalHoliday ? 'day-cell-holiday' : '') }} matrix-hover-target"
                                                data-type="cell"
                                                data-staff="{{ $user->name }}"
                                                data-role="{{ $userRoleName }}"
                                                data-uid="{{ $user->attendance_unique_id ?? ('#' . $user->id) }}"
                                                data-date="{{ $dayFull }}"
                                                data-daynum="{{ sprintf('%02d', $i) }}"
                                                data-statustype="{{ $statusType }}"
                                                data-statuslabel="{{ $statusLabel }}"
                                                data-intime="{{ $inTime }}"
                                                data-outtime="{{ $outTime }}">
                                                @if($dailyData)
                                                    @if(in_array($status, ['present', 'in', 'out']))
                                                        <span class="badge-matrix badge-p">P</span>
                                                    @elseif($status === 'absent')
                                                        <span class="badge-matrix badge-a">A</span>
                                                    @elseif($status === 'halfday')
                                                        <span class="badge-matrix badge-hd">HD</span>
                                                    @elseif($status === 'holiday')
                                                        <span class="badge-matrix badge-h">H</span>
                                                    @else
                                                        <span class="badge-matrix badge-p">{{ strtoupper(substr($status, 0, 1)) }}</span>
                                                    @endif
                                                @elseif($isSunday)
                                                    <span class="badge-matrix badge-w">W</span>
                                                @elseif($isCalHoliday)
                                                    <span class="badge-matrix badge-h">H</span>
                                                @else
                                                    <span class="badge-matrix badge-empty">-</span>
                                                @endif
                                            </td>
                                        @endfor

                                        <!-- Summary Counts (Sticky Right) -->
                                        <td class="sticky-col-sum-p">{{ $pCount }}</td>
                                        <td class="sticky-col-sum-a">{{ $aCount }}</td>
                                        <td class="sticky-col-sum-hd">{{ $hdCount }}</td>
                                        <td class="sticky-col-sum-h">{{ $hCount }}</td>
                                        <td class="sticky-col-sum-rate">
                                            @if($workingCount > 0)
                                                <span class="rate-pill {{ $rateClass }}">{{ $userRate }}%</span>
                                            @else
                                                <span class="text-muted font-size-10">-</span>
                                            @endif
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $daysInMonth + 8 }}" class="text-center py-5 text-muted">
                                            <div class="py-4">
                                                <i class="fa fa-users text-muted mb-2" style="font-size: 28px; opacity: 0.5;"></i>
                                                <p class="font-weight-bold mb-1">No staff attendance records found</p>
                                                <p class="small text-muted mb-0">Try changing the role or selecting a different month & year.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if($users->isNotEmpty())
                            <tfoot>
                                <tr>
                                    <th class="sticky-col-idx"><i class="fa fa-calculator text-white"></i></th>
                                    <th class="sticky-col-id text-white font-weight-bold">TOTAL</th>
                                    <th class="sticky-col-name text-left pl-2 text-white font-weight-bold">Daily Staff Present</th>
                                    
                                    <!-- Daily Present Totals with white font on dark background -->
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        @php
                                            $dateStr = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $i);
                                            $dayOfWeek = date('w', strtotime($dateStr));
                                            $isSunday = ($dayOfWeek == 0);
                                            $cnt = $dailyPresentTotals[$i] ?? 0;
                                        @endphp
                                        <th class="day-col {{ $isSunday ? 'day-header-sunday' : '' }}">
                                            @if($isSunday && $cnt === 0)
                                                <span style="font-size: 9px; color: #ffffff !important; opacity: 0.85;">-</span>
                                            @else
                                                <span style="font-size: 10.5px; font-weight: 800; color: #ffffff !important;">{{ $cnt }}</span>
                                            @endif
                                        </th>
                                    @endfor

                                    <!-- Grand Summary Totals in Vibrant Light Colors for Dark Navy Background -->
                                    <th class="sticky-col-sum-p font-weight-bold">{{ $overallStats['totalPresent'] ?? 0 }}</th>
                                    <th class="sticky-col-sum-a font-weight-bold">{{ $overallStats['totalAbsent'] ?? 0 }}</th>
                                    <th class="sticky-col-sum-hd font-weight-bold">{{ $overallStats['totalHalfDay'] ?? 0 }}</th>
                                    <th class="sticky-col-sum-h font-weight-bold">{{ $overallStats['totalHoliday'] ?? 0 }}</th>
                                    <th class="sticky-col-sum-rate font-weight-bold">{{ $overallStats['avgAttendanceRate'] ?? 0 }}%</th>
                                </tr>
                            </tfoot>
                            @endif

                        </table>
                    </div>

                    <!-- Bottom Status Bar (Pure High-Contrast Light Text on Dark Navy Background) -->
                    <div class="matrix-bottom-bar">
                        <div>
                            Showing <span id="visibleStaffCount" class="bottom-badge">{{ $users->count() }}</span> of <span class="bottom-badge">{{ $users->count() }}</span> staff members for <strong class="bottom-highlight">{{ $monthName }} {{ $year }}</strong>
                        </div>
                        <div class="d-none d-md-block bottom-tip">
                            <i class="fa fa-info-circle mr-1" style="color: #93c5fd;"></i> Tip: Hover over dates or badges for details • Click any row to highlight
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>
</div>

<!-- Floating Rich Tooltip Card Element -->
<div id="matrixHoverTooltip"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Interactive Floating Tooltip with Styled Dark Navy Background
    const tooltipCard = document.getElementById('matrixHoverTooltip');
    const hoverTargets = document.querySelectorAll('.matrix-hover-target');

    hoverTargets.forEach(target => {
        target.addEventListener('mouseenter', function(e) {
            const type = this.getAttribute('data-type');
            let contentHtml = '';

            if (type === 'header') {
                const date = this.getAttribute('data-date') || '';
                const isSun = this.getAttribute('data-issunday') === '1';
                const present = this.getAttribute('data-present') || '0';
                const total = this.getAttribute('data-totalstaff') || '0';

                contentHtml = `
                    <div class="tip-header">
                        <span><i class="fa fa-calendar-o mr-1"></i> ${date}</span>
                    </div>
                    <div class="tip-row">
                        <span>Status:</span>
                        <span class="tip-badge ${isSun ? 'tip-badge-w' : 'tip-badge-p'}">
                            ${isSun ? 'Sunday (Weekly Off)' : 'Working Day'}
                        </span>
                    </div>
                    <div class="tip-row" style="margin-top: 4px;">
                        <span>Present Staff:</span>
                        <span style="font-weight: 800; color: #86efac;">${present} / ${total}</span>
                    </div>
                `;
            } else if (type === 'cell') {
                const staff = this.getAttribute('data-staff') || '';
                const role = this.getAttribute('data-role') || '';
                const uid = this.getAttribute('data-uid') || '';
                const date = this.getAttribute('data-date') || '';
                const stType = this.getAttribute('data-statustype') || 'empty';
                const stLabel = this.getAttribute('data-statuslabel') || 'Not Marked';
                const inTime = this.getAttribute('data-intime') || '';
                const outTime = this.getAttribute('data-outtime') || '';

                let timeInfo = '';
                if (inTime || outTime) {
                    timeInfo = `
                        <div class="tip-row" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 4px; margin-top: 4px;">
                            <span><i class="fa fa-sign-in text-success mr-1"></i> In:</span>
                            <span style="color: #ffffff; font-weight: 700;">${inTime || '--:--'}</span>
                        </div>
                        <div class="tip-row">
                            <span><i class="fa fa-sign-out text-info mr-1"></i> Out:</span>
                            <span style="color: #ffffff; font-weight: 700;">${outTime || '--:--'}</span>
                        </div>
                    `;
                }

                contentHtml = `
                    <div class="tip-header">
                        <span><i class="fa fa-calendar mr-1"></i> ${date}</span>
                    </div>
                    <div class="tip-staff">
                        <i class="fa fa-user mr-1"></i> ${staff}
                        <span style="opacity: 0.75; font-size: 9.5px; font-weight: 400;">(${role})</span>
                    </div>
                    <div class="tip-row">
                        <span>Status:</span>
                        <span class="tip-badge tip-badge-${stType}">${stLabel}</span>
                    </div>
                    ${timeInfo}
                `;
            }

            if (tooltipCard && contentHtml) {
                tooltipCard.innerHTML = contentHtml;
                tooltipCard.style.display = 'block';
                positionTooltip(e);
            }
        });

        target.addEventListener('mousemove', function(e) {
            positionTooltip(e);
        });

        target.addEventListener('mouseleave', function() {
            if (tooltipCard) {
                tooltipCard.style.display = 'none';
            }
        });
    });

    function positionTooltip(e) {
        if (!tooltipCard || tooltipCard.style.display === 'none') return;
        
        let x = e.clientX;
        let y = e.clientY - 12;

        // Prevent overflow viewport edges
        const tipRect = tooltipCard.getBoundingClientRect();
        if (x - (tipRect.width / 2) < 10) {
            x = (tipRect.width / 2) + 10;
        } else if (x + (tipRect.width / 2) > window.innerWidth - 10) {
            x = window.innerWidth - (tipRect.width / 2) - 10;
        }

        if (y - tipRect.height < 10) {
            // Flip to bottom if close to top
            y = e.clientY + 24;
            tooltipCard.style.transform = 'translate(-50%, 0)';
        } else {
            tooltipCard.style.transform = 'translate(-50%, -100%)';
        }

        tooltipCard.style.left = x + 'px';
        tooltipCard.style.top = y + 'px';
    }

    // 2. Real-time Live Staff Filter
    const searchInput = document.getElementById('staffSearchInput');
    const rows = document.querySelectorAll('.staff-row');
    const countDisplay = document.getElementById('visibleStaffCount');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visible = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const id = row.getAttribute('data-id') || '';
                const role = row.getAttribute('data-role') || '';

                if (query === '' || name.includes(query) || id.includes(query) || role.includes(query)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (countDisplay) {
                countDisplay.textContent = visible;
            }
        });
    }

    // 3. Row highlight on click
    rows.forEach(row => {
        row.addEventListener('click', function() {
            if (this.classList.contains('row-highlighted')) {
                this.classList.remove('row-highlighted');
            } else {
                rows.forEach(r => r.classList.remove('row-highlighted'));
                this.classList.add('row-highlighted');
            }
        });
    });
});
</script>

@endsection
@extends('layout.app')

@section('content')

@php
    $rolesMap = $staffRoles->pluck('name', 'id')->toArray();
    $prevM = $month == 1 ? 12 : $month - 1;
    $prevY = $month == 1 ? $year - 1 : $year;
    $nextM = $month == 12 ? 1 : $month + 1;
    $nextY = $month == 12 ? $year + 1 : $year;
    
    $prevUrl = url('AttendanceView_staff') . '?month=' . $prevM . '&year=' . $prevY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '');
    $nextUrl = url('AttendanceView_staff') . '?month=' . $nextM . '&year=' . $nextY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '');
    $currentMonthUrl = url('AttendanceView_staff') . '?month=' . date('n') . '&year=' . date('Y') . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '');

    // Overall metrics calculation
    $totalStaff = $users->count();
    $grandPresent = 0;
    $grandAbsent = 0;
    $grandHalfDay = 0;
    $grandHoliday = 0;

    // Daily totals collector
    $dailyPresentTotals = array_fill(1, $daysInMonth, 0);
    $dailyAbsentTotals = array_fill(1, $daysInMonth, 0);
    $dailyHalfDayTotals = array_fill(1, $daysInMonth, 0);
    $dailyHolidayTotals = array_fill(1, $daysInMonth, 0);

    foreach ($users as $u) {
        $p = $summaryData[$u->id]['present'] ?? 0;
        $a = $summaryData[$u->id]['absent'] ?? 0;
        $hd = $summaryData[$u->id]['halfday'] ?? 0;
        $h = $summaryData[$u->id]['holiday'] ?? 0;

        $grandPresent += $p;
        $grandAbsent += $a;
        $grandHalfDay += $hd;
        $grandHoliday += $h;

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

    $totalMarks = $grandPresent + $grandAbsent + $grandHalfDay;
    $overallPct = $totalMarks > 0 ? round((($grandPresent + ($grandHalfDay * 0.5)) / $totalMarks) * 100, 1) : 0;
    $isCurrentMonth = ($month == date('n') && $year == date('Y'));
    $todayDayNum = (int)date('j');
@endphp

<style>
/* ==========================================================================
   ARISE ERP - STAFF ATTENDANCE REGISTER MATRIX THEME
   - Exact Alignment with Today Absent Class-Wise & StudentList Layout
   - Viewport Auto-Adjust (calc(100vh - var(--header-height) - 16px))
   - Auto Expanding Table Viewport (.table-scroll-container)
   - Dual Frozen Left Columns (BioMax & Staff Name)
   - Pinned Bottom Pagination Toolbar (.table-pagination-bar)
   ========================================================================== */

:root {
    --header-height: 56px;
}

.staff-att-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.staff-att-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjust Container */
.staff-att-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 8px 0;
}

/* 1. Hero Header Banner */
.staff-hero {
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
.staff-hero-text {
    display: flex;
    flex-direction: column;
}
.staff-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.staff-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.staff-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.staff-hero-stats {
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
    font-size: 11.5px;
}
.hero-stat-chip.badge-emerald {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #d1fae5;
}
.hero-stat-chip.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
}
.hero-stat-chip.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}
.hero-stat-chip.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}

/* Action buttons */
.staff-hero-actions {
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
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* 2. Filter Card */
.filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.filter-card-header {
    background: #f8fafc;
    padding: 4px 10px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 11px;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-card-body {
    padding: 5px 10px;
}

.filter-form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.month-nav-group {
    display: flex;
    align-items: center;
    gap: 4px;
}
.month-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #002C54;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 2px;
    text-decoration: none !important;
    transition: all .15s ease;
}
.month-nav-btn:hover {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}
.month-nav-btn.active-month {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}

.filter-controls-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.filter-select {
    height: 26px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    cursor: pointer;
}
.filter-select:focus {
    border-color: #002C54;
}

/* ==========================================================================
   AUTO-ADJUSTING TABLE CARD & SCROLL CONTAINER (Exact match with today_absent_class_wise)
   ========================================================================== */

.dash-table-card {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    overflow: hidden;
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
}

.dash-card-header {
    padding: 5px 10px;
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
    font-size: 11.5px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

.table-header-tools {
    display: flex;
    align-items: center;
    gap: 6px;
}
.table-search-input {
    height: 24px;
    padding: 0 8px 0 24px;
    font-size: 11px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    outline: none;
    width: 170px;
    transition: all .15s ease;
}
.table-search-input::placeholder {
    color: #cbd5e1;
}
.table-search-input:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
    width: 210px;
}
.table-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.table-search-wrap i {
    position: absolute;
    left: 7px;
    color: #cbd5e1;
    font-size: 10px;
    pointer-events: none;
}

/* Scrollable Table Viewport - Auto fills available vertical screen height */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Design */
.dash-matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
    background: #ffffff;
}

/* Sticky thead */
.dash-matrix-table thead {
    position: sticky;
    top: 0;
    z-index: 25;
}
.dash-matrix-table thead tr th {
    background: #002C54;
    color: #ffffff;
    text-align: center;
    vertical-align: middle;
    padding: 6px 4px;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    font-weight: 700;
    white-space: nowrap;
    box-sizing: border-box;
}

/* Double Sticky Frozen Left Columns */
.col-freeze-1 {
    position: sticky !important;
    left: 0 !important;
    z-index: 10 !important;
    background: #ffffff !important;
    width: 75px;
    min-width: 75px;
    max-width: 75px;
    text-align: center !important;
    box-shadow: 2px 0 4px rgba(0,0,0,0.04);
}
.col-freeze-2 {
    position: sticky !important;
    left: 75px !important;
    z-index: 10 !important;
    background: #ffffff !important;
    width: 170px;
    min-width: 170px;
    max-width: 170px;
    text-align: left !important;
    box-shadow: 3px 0 5px rgba(0,0,0,0.06);
}
.dash-matrix-table thead th.col-freeze-1,
.dash-matrix-table thead th.col-freeze-2 {
    z-index: 30 !important;
    background: #002342 !important;
    color: #ffffff !important;
}

/* Highlight Sundays & Today in Header */
.th-sunday {
    background: #881337 !important;
    color: #ffe4e6 !important;
}
.th-today {
    background: #0369a1 !important;
    color: #e0f2fe !important;
    box-shadow: inset 0 -2px 0 #38bdf8;
}

/* Day Column Header Layout */
.day-head-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    line-height: 1.1;
    min-width: 25px;
}
.day-name {
    font-size: 8.5px;
    text-transform: uppercase;
    opacity: 0.85;
}
.day-number {
    font-size: 11px;
    font-weight: 800;
}

/* Table Body Row Styling */
.dash-matrix-table tbody tr {
    background: #ffffff;
    transition: background 0.1s ease;
}
.dash-matrix-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-matrix-table tbody tr:nth-child(even) .col-freeze-1,
.dash-matrix-table tbody tr:nth-child(even) .col-freeze-2 {
    background: #f8fafc !important;
}
.dash-matrix-table tbody tr:hover {
    background: #edf2f7 !important;
}
.dash-matrix-table tbody tr:hover .col-freeze-1,
.dash-matrix-table tbody tr:hover .col-freeze-2 {
    background: #e2e8f0 !important;
}

.dash-matrix-table tbody td {
    padding: 3px 4px;
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
    white-space: nowrap;
}

/* Sunday column background in body */
.td-sunday {
    background: #fff1f2 !important;
}
/* Today column background in body */
.td-today {
    background: #f0f9ff !important;
    border-left: 1px dashed #38bdf8 !important;
    border-right: 1px dashed #38bdf8 !important;
}

/* Staff Micro Info Box */
.staff-info-cell {
    display: flex;
    align-items: center;
    gap: 6px;
}
.staff-avatar-thumb {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
    flex-shrink: 0;
}
.staff-name-text {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.staff-sub-text {
    font-size: 9.5px;
    color: #64748b;
    line-height: 1;
}

/* Badges for P, A, HD, H */
.att-badge {
    width: 21px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    font-weight: 800;
    border-radius: 2px;
    line-height: 1;
    cursor: default;
    user-select: none;
    transition: transform .1s ease;
}
.att-badge:hover {
    transform: scale(1.15);
    z-index: 5;
    position: relative;
}
.att-badge-p {
    background: #10b981;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(16, 185, 129, 0.25);
}
.att-badge-a {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(239, 68, 68, 0.25);
}
.att-badge-hd {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(245, 158, 11, 0.25);
}
.att-badge-h {
    background: #6366f1;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(99, 102, 241, 0.25);
}
.att-badge-empty {
    color: #cbd5e1;
    font-weight: 700;
    font-size: 12px;
}

/* Summary Totals Cells */
.sum-cell {
    font-weight: 700;
    font-size: 11px;
    padding: 3px 6px !important;
}
.sum-p { color: #16a34a; background: #f0fdf4; }
.sum-a { color: #dc2626; background: #fef2f2; }
.sum-hd { color: #d97706; background: #fffbeb; }
.sum-h { color: #4f46e5; background: #eef2ff; }

.badge-pct {
    padding: 2px 5px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 800;
}
.badge-pct-high { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-pct-mid { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.badge-pct-low { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

/* Sticky Daily Totals Footer */
.dash-matrix-table tfoot {
    position: sticky;
    bottom: 0;
    z-index: 20;
}
.dash-matrix-table tfoot tr th {
    background: #002342 !important;
    color: #ffffff !important;
    padding: 5px 4px;
    border-top: 2px solid #001f3d;
    border-right: 1px solid rgba(255,255,255,.12);
    font-size: 10px;
    font-weight: 700;
    text-align: center;
}
.dash-matrix-table tfoot th.col-freeze-1,
.dash-matrix-table tfoot th.col-freeze-2 {
    z-index: 22 !important;
    background: #001833 !important;
    color: #38bdf8 !important;
    text-align: right !important;
    padding-right: 8px;
}

/* 4. Pinned Bottom Pagination Toolbar (Exact match with today_absent_class_wise) */
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
}
.rows-per-page-selector select option {
    background: #002C54;
    color: #ffffff;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 25px;
    height: 23px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
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
    font-size: 10.5px;
    font-weight: 600;
    padding: 0 5px;
    color: #f1f5f9;
}

/* Centered Empty State */
#empty-state-row td, .empty-row td, .no-match-row td {
    padding: 0 !important;
    border: none !important;
    background: #ffffff !important;
    vertical-align: middle !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height, 56px) - 230px);
    padding: 30px 16px;
    text-align: center;
    color: #475569;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 42px;
    margin-bottom: 10px;
    line-height: 1;
    color: #94a3b8;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #1e293b;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 440px;
    line-height: 1.5;
}

@media(max-width:768px) {
    .staff-att-layout {
        height: auto;
        overflow: visible;
        padding: 4px;
    }
    .table-scroll-container {
        max-height: 520px;
    }
    .filter-card {
        margin-bottom: 6px;
    }
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
}
</style>

<div class="content-wrapper staff-att-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="staff-att-layout">

                {{-- 1. Signature Hero Header Banner --}}
                <div class="staff-hero">
                    <div class="staff-hero-text">
                        <span class="staff-kicker">Attendance Management &bull; Staff Register</span>
                        <h1 class="staff-title">
                            <i class="fa fa-id-badge text-warning"></i> Staff Attendance Monthly Register
                        </h1>
                        <p class="staff-subtitle">
                            Monthly day-wise attendance matrix for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }} with daily totals and quick export.
                        </p>
                    </div>

                    <div class="staff-hero-stats">
                        <div class="hero-stat-chip badge-sky">
                            <i class="fa fa-users"></i> Staff: <b id="statTotalStaff">{{ $totalStaff }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald">
                            <i class="fa fa-check-circle"></i> Present: <b>{{ $grandPresent }}</b>
                        </div>
                        <div class="hero-stat-chip badge-rose">
                            <i class="fa fa-times-circle"></i> Absent: <b>{{ $grandAbsent }}</b>
                        </div>
                        <div class="hero-stat-chip badge-amber">
                            <i class="fa fa-pie-chart"></i> Rate: <b>{{ $overallPct }}%</b>
                        </div>
                    </div>

                    <div class="staff-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" onclick="exportStaffAttendanceCSV()" title="Export CSV Spreadsheet">
                            <i class="fa fa-file-excel-o text-success"></i> Export CSV
                        </button>
                        <button type="button" class="dash-btn dash-btn-outline" onclick="window.print()" title="Print Current View">
                            <i class="fa fa-print"></i> Print
                        </button>
                        <a href="{{ url('dashboard') }}" class="dash-btn dash-btn-outline" title="Back to Dashboard">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </div>
                </div>

                {{-- 2. Date & Role Selection Filter Card --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span><i class="fa fa-filter mr-1"></i> Month &amp; Staff Selection Filters</span>
                        <span style="font-size: 10px; font-weight: normal; opacity: .85;">Navigate months or filter by role</span>
                    </div>
                    <div class="filter-card-body">
                        <form method="GET" action="{{ url('AttendanceView_staff') }}" id="staffAttFilterForm" class="filter-form-row">
                            
                            {{-- Quick Month Switcher --}}
                            <div class="month-nav-group">
                                <a href="{{ $prevUrl }}" class="month-nav-btn" title="Previous Month">
                                    <i class="fa fa-chevron-left mr-1"></i> Prev
                                </a>
                                <a href="{{ $currentMonthUrl }}" class="month-nav-btn {{ $isCurrentMonth ? 'active-month' : '' }}" title="Current Month">
                                    <i class="fa fa-calendar-check-o mr-1"></i> Current Month
                                </a>
                                <a href="{{ $nextUrl }}" class="month-nav-btn" title="Next Month">
                                    Next <i class="fa fa-chevron-right ml-1"></i>
                                </a>
                            </div>

                            <div class="filter-controls-group">
                                {{-- Role Selector (Only for Admin) --}}
                                @if($loggedInRoleId == 1 && $staffRoles->count() > 0)
                                    <select name="role_id" class="filter-select" onchange="document.getElementById('staffAttFilterForm').submit()">
                                        <option value="">-- All Staff Roles --</option>
                                        @foreach($staffRoles as $role)
                                            <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif

                                {{-- Month Selector --}}
                                <select name="month" class="filter-select" onchange="document.getElementById('staffAttFilterForm').submit()">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>

                                {{-- Year Selector --}}
                                <select name="year" class="filter-select" onchange="document.getElementById('staffAttFilterForm').submit()">
                                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>

                                @if($selectedRoleId || $month != date('n') || $year != date('Y'))
                                    <a href="{{ url('AttendanceView_staff') }}" class="month-nav-btn" title="Reset Filters">
                                        <i class="fa fa-refresh mr-1"></i> Reset
                                    </a>
                                @endif
                            </div>

                        </form>
                    </div>
                </div>

                {{-- 3. Exact Unified Table Card with Dual Frozen Left Columns & Sticky Pinned Headers --}}
                <div class="dash-table-card">
                    <div class="dash-card-header">
                        <div class="dash-card-title">
                            <i class="fa fa-list-ol"></i>
                            <span>Staff Attendance Matrix</span>
                            <span class="badge-total-records">
                                Month: {{ date('M Y', mktime(0, 0, 0, $month, 1, $year)) }} &bull; Total: <span id="visibleRecordCount">{{ $totalStaff }}</span>
                            </span>
                        </div>

                        <div class="table-header-tools">
                            <div class="table-search-wrap">
                                <i class="fa fa-search"></i>
                                <input type="text" id="liveStaffSearch" class="table-search-input" placeholder="Search staff name or ID...">
                            </div>
                        </div>
                    </div>

                    <div class="table-scroll-container" id="staffTableContainer">
                        <table class="dash-matrix-table" id="staffAttendanceTable">
                            <thead>
                                <tr>
                                    {{-- Frozen Col 1: Bio Max ID --}}
                                    <th class="col-freeze-1">
                                        <span>BioMax / ID</span>
                                    </th>

                                    {{-- Frozen Col 2: Staff Details --}}
                                    <th class="col-freeze-2">
                                        <span>Staff Name &amp; Role</span>
                                    </th>

                                    {{-- Day Columns 1 to N --}}
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dateStr = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $d);
                                            $dayName = date('D', strtotime($dateStr));
                                            $isSunday = ($dayName === 'Sun');
                                            $isToday = ($isCurrentMonth && $d === $todayDayNum);
                                        @endphp
                                        <th class="{{ $isSunday ? 'th-sunday' : ($isToday ? 'th-today' : '') }}" title="{{ date('l, d F Y', strtotime($dateStr)) }}">
                                            <div class="day-head-wrap">
                                                <span class="day-name">{{ $dayName }}</span>
                                                <span class="day-number">{{ $d }}</span>
                                            </div>
                                        </th>
                                    @endfor

                                    {{-- Summary Columns --}}
                                    <th style="width: 35px; background: #064e3b !important; color: #a7f3d0 !important;" title="Total Present">P</th>
                                    <th style="width: 35px; background: #881337 !important; color: #fecdd3 !important;" title="Total Absent">A</th>
                                    <th style="width: 35px; background: #78350f !important; color: #fde68a !important;" title="Total Half Day">HD</th>
                                    <th style="width: 35px; background: #312e81 !important; color: #c7d2fe !important;" title="Total Holiday">H</th>
                                    <th style="width: 60px; background: #002342 !important;" title="Attendance %">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                    @php
                                        $userRoleName = $rolesMap[$user->role_id] ?? 'Staff';
                                        $totP = $summaryData[$user->id]['present'] ?? 0;
                                        $totA = $summaryData[$user->id]['absent'] ?? 0;
                                        $totHD = $summaryData[$user->id]['halfday'] ?? 0;
                                        $totH = $summaryData[$user->id]['holiday'] ?? 0;
                                        $totLogged = $totP + $totA + $totHD;
                                        $userPct = $totLogged > 0 ? round((($totP + ($totHD * 0.5)) / $totLogged) * 100, 1) : 0;
                                        
                                        $pctClass = 'badge-pct-high';
                                        if ($totLogged > 0) {
                                            if ($userPct < 75) $pctClass = 'badge-pct-low';
                                            elseif ($userPct < 85) $pctClass = 'badge-pct-mid';
                                        }
                                        $initial = strtoupper(substr($user->name ?: 'S', 0, 1));
                                    @endphp
                                    <tr class="table-data-row staff-data-row" 
                                        data-name="{{ strtolower($user->name) }}" 
                                        data-id="{{ strtolower($user->attendance_unique_id ?? $user->id) }}"
                                        data-role="{{ strtolower($userRoleName) }}">
                                        
                                        {{-- Frozen Col 1: BioMax / ID --}}
                                        <td class="col-freeze-1 font-weight-bold">
                                            <code>{{ $user->attendance_unique_id ?? $user->id }}</code>
                                        </td>

                                        {{-- Frozen Col 2: Staff Details --}}
                                        <td class="col-freeze-2">
                                            <div class="staff-info-cell">
                                                <span class="staff-avatar-thumb">{{ $initial }}</span>
                                                <div style="overflow:hidden;">
                                                    <div class="staff-name-text" title="{{ $user->name }}">{{ $user->name }}</div>
                                                    <div class="staff-sub-text">
                                                        <span>{{ $userRoleName }}</span>
                                                        @if(!empty($user->father_name))
                                                            &bull; <span>S/D/W: {{ $user->father_name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Daily Attendance Matrix Cells --}}
                                        @for ($d = 1; $d <= $daysInMonth; $d++)
                                            @php
                                                $dateStr = $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $d);
                                                $dayName = date('D', strtotime($dateStr));
                                                $isSunday = ($dayName === 'Sun');
                                                $isToday = ($isCurrentMonth && $d === $todayDayNum);
                                                $mark = $attendanceData[$user->id][$d] ?? null;
                                                $status = $mark ? strtolower($mark->status) : null;
                                                
                                                $tooltip = date('d M Y (D)', strtotime($dateStr));
                                                if ($mark) {
                                                    $tooltip .= ' - ' . ucfirst($mark->status);
                                                    if (!empty($mark->in_time)) $tooltip .= ' | In: ' . date('h:i A', strtotime($mark->in_time));
                                                    if (!empty($mark->out_time)) $tooltip .= ' | Out: ' . date('h:i A', strtotime($mark->out_time));
                                                }
                                            @endphp
                                            <td class="{{ $isSunday ? 'td-sunday' : ($isToday ? 'td-today' : '') }}">
                                                @if($mark)
                                                    @if(in_array($status, ['present', 'in', 'out']))
                                                        <span class="att-badge att-badge-p" data-toggle="tooltip" title="{{ $tooltip }}">P</span>
                                                    @elseif($status === 'absent')
                                                        <span class="att-badge att-badge-a" data-toggle="tooltip" title="{{ $tooltip }}">A</span>
                                                    @elseif($status === 'halfday')
                                                        <span class="att-badge att-badge-hd" data-toggle="tooltip" title="{{ $tooltip }}">HD</span>
                                                    @elseif($status === 'holiday')
                                                        <span class="att-badge att-badge-h" data-toggle="tooltip" title="{{ $tooltip }}">H</span>
                                                    @else
                                                        <span class="att-badge att-badge-p" data-toggle="tooltip" title="{{ $tooltip }}">P</span>
                                                    @endif
                                                @else
                                                    <span class="att-badge-empty" title="{{ $tooltip }}">-</span>
                                                @endif
                                            </td>
                                        @endfor

                                        {{-- Row Summaries --}}
                                        <td class="sum-cell sum-p">{{ $totP }}</td>
                                        <td class="sum-cell sum-a">{{ $totA }}</td>
                                        <td class="sum-cell sum-hd">{{ $totHD }}</td>
                                        <td class="sum-cell sum-h">{{ $totH }}</td>
                                        <td class="sum-cell">
                                            @if($totLogged > 0)
                                                <span class="badge-pct {{ $pctClass }}">{{ $userPct }}%</span>
                                            @else
                                                <span class="text-muted" style="font-size:10px;">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-state-row" class="empty-row">
                                        <td colspan="{{ $daysInMonth + 7 }}" class="p-0 border-0">
                                            <div class="dash-empty-state">
                                                <div class="empty-icon"><i class="fa fa-users"></i></div>
                                                <div class="empty-title">No Staff Members Found</div>
                                                <div class="empty-desc">There are no active staff records matching your current filter criteria.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            
                            {{-- Sticky Daily Totals Footer --}}
                            @if(count($users) > 0)
                            <tfoot>
                                <tr>
                                    <th class="col-freeze-1">Totals</th>
                                    <th class="col-freeze-2">Daily Present:</th>
                                    @for ($d = 1; $d <= $daysInMonth; $d++)
                                        @php
                                            $dP = $dailyPresentTotals[$d] ?? 0;
                                            $dA = $dailyAbsentTotals[$d] ?? 0;
                                        @endphp
                                        <th title="Day {{ $d }}: {{ $dP }} Present, {{ $dA }} Absent">
                                            <span style="color: #4ade80;">{{ $dP }}</span>
                                        </th>
                                    @endfor
                                    <th style="color: #4ade80;">{{ $grandPresent }}</th>
                                    <th style="color: #f87171;">{{ $grandAbsent }}</th>
                                    <th style="color: #fbbf24;">{{ $grandHalfDay }}</th>
                                    <th style="color: #a5b4fc;">{{ $grandHoliday }}</th>
                                    <th style="color: #38bdf8;">{{ $overallPct }}%</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- 4. Pinned Bottom Pagination Toolbar (Exact match with today_absent_class_wise) --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ count($users) > 0 ? 1 : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min(25, count($users)) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ count($users) }}</span> staff members
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10">10</option>
                                    <option value="25" selected>25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="pagination-nav">
                                <button type="button" class="page-btn" id="btn-first" title="First Page" disabled><i class="fa fa-angle-double-left"></i></button>
                                <button type="button" class="page-btn" id="btn-prev" title="Previous Page" disabled><i class="fa fa-angle-left"></i></button>
                                <span class="page-current-indicator">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                                <button type="button" class="page-btn" id="btn-next" title="Next Page"><i class="fa fa-angle-right"></i></button>
                                <button type="button" class="page-btn" id="btn-last" title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
</div>

@section('scripts')
<script>
function exportStaffAttendanceCSV() {
    var table = document.getElementById("staffAttendanceTable");
    if (!table) return;

    var csv = [];
    var theadRows = table.querySelectorAll("thead tr");
    if (theadRows.length > 0) {
        var headerCols = theadRows[0].querySelectorAll("th");
        var hRow = [];
        for (var i = 0; i < headerCols.length; i++) {
            var text = headerCols[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            hRow.push('"' + text.replace(/"/g, '""') + '"');
        }
        csv.push(hRow.join(","));
    }

    var bodyRows = table.querySelectorAll("tbody tr.staff-data-row");
    for (var r = 0; r < bodyRows.length; r++) {
        var cols = bodyRows[r].querySelectorAll("td");
        var bRow = [];
        for (var c = 0; c < cols.length; c++) {
            var cellText = cols[c].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            bRow.push('"' + cellText.replace(/"/g, '""') + '"');
        }
        csv.push(bRow.join(","));
    }

    var csvString = csv.join("\n");
    var filename = "staff_attendance_{{ $year }}_{{ sprintf('%02d', $month) }}.csv";
    var link = document.createElement("a");
    link.style.display = "none";
    link.setAttribute("href", 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function() {
    // Tooltips initialization
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body',
            trigger: 'hover',
            placement: 'top'
        });
    }

    /* ==========================================================
       PAGINATION & INSTANT LIVE SEARCH ENGINE (Exact match with today_absent_class_wise)
       ========================================================== */
    var currentPage = 1;
    var pageSize = 25; // default 25

    function updateStaffPagination() {
        var $rows = $("#staffAttendanceTable tbody tr.staff-data-row");
        if ($rows.length === 0) {
            $('#page-start').text(0);
            $('#page-end').text(0);
            $('#total-records').text(0);
            $('#visibleRecordCount').text(0);
            $('#statTotalStaff').text(0);
            $('#current-page').text(1);
            $('#total-pages').text(1);
            $('#btn-first, #btn-prev, #btn-next, #btn-last').prop('disabled', true);
            return;
        }

        var searchVal = $('#liveStaffSearch').val().toLowerCase().trim();
        var matchingRows = [];

        $rows.each(function() {
            var $row = $(this);
            var name = ($row.attr('data-name') || '');
            var id = ($row.attr('data-id') || '');
            var role = ($row.attr('data-role') || '');
            var fullText = $row.text().toLowerCase();

            if (searchVal === '' || name.indexOf(searchVal) !== -1 || id.indexOf(searchVal) !== -1 || role.indexOf(searchVal) !== -1 || fullText.indexOf(searchVal) !== -1) {
                matchingRows.push($row);
            }
        });

        var totalMatching = matchingRows.length;
        var effectivePageSize = pageSize === -1 ? totalMatching : pageSize;
        var totalPages = effectivePageSize > 0 ? Math.max(1, Math.ceil(totalMatching / effectivePageSize)) : 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }

        var startIndex = (currentPage - 1) * effectivePageSize;
        var endIndex = pageSize === -1 ? totalMatching : Math.min(startIndex + effectivePageSize, totalMatching);

        // Hide all rows
        $rows.hide();
        $('#staffAttendanceTable tbody tr.no-match-row').remove();

        if (totalMatching === 0) {
            var colCount = {{ $daysInMonth + 7 }};
            $('#staffAttendanceTable tbody').append(
                '<tr class="no-match-row"><td colspan="' + colCount + '" class="p-0 border-0">' +
                '<div class="dash-empty-state">' +
                '<div class="empty-icon"><i class="fa fa-filter"></i></div>' +
                '<div class="empty-title">No Matching Staff Found</div>' +
                '<div class="empty-desc">No staff member matches "' + searchVal + '". Try adjusting your search query.</div>' +
                '</div>' +
                '</td></tr>'
            );
        } else {
            for (var i = startIndex; i < endIndex; i++) {
                matchingRows[i].show();
            }
        }

        // Update counters
        $('#page-start').text(totalMatching === 0 ? 0 : startIndex + 1);
        $('#page-end').text(endIndex);
        $('#total-records').text(totalMatching);
        $('#visibleRecordCount').text(totalMatching);
        $('#statTotalStaff').text(totalMatching);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        // Button state
        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || totalPages <= 1);
    }

    // Rows per page
    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        if (val === 'all') {
            pageSize = -1;
        } else {
            pageSize = parseInt(val, 10);
        }
        currentPage = 1;
        updateStaffPagination();
    });

    // Pagination buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            updateStaffPagination();
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateStaffPagination();
        }
    });

    $('#btn-next').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage++;
            updateStaffPagination();
        }
    });

    $('#btn-last').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage = totalPages;
            updateStaffPagination();
        }
    });

    // Live search input
    $('#liveStaffSearch').on('keyup input', function() {
        currentPage = 1;
        updateStaffPagination();
    });

    // Initial run
    updateStaffPagination();
});
</script>
@endsection

@endsection
@extends('layout.mobile_app')

@php
    $rolesMap = $staffRoles->pluck('name', 'id')->toArray();
    $prevM = $month == 1 ? 12 : $month - 1;
    $prevY = $month == 1 ? $year - 1 : $year;
    $nextM = $month == 12 ? 1 : $month + 1;
    $nextY = $month == 12 ? $year + 1 : $year;
    
    $prevUrl = url('monthlyReport') . '?month=' . $prevM . '&year=' . $prevY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '') . '&layout=mobile';
    $nextUrl = url('monthlyReport') . '?month=' . $nextM . '&year=' . $nextY . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '') . '&layout=mobile';
    $exportUrl = url('monthlyReport') . '?month=' . $month . '&year=' . $year . ($selectedRoleId ? '&role_id=' . $selectedRoleId : '') . '&export=1';

    $monthsList = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
@endphp

@section('title', 'Monthly Attendance - ' . $monthName . ' ' . $year)

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE STAFF MONTHLY ATTENDANCE REPORT
   Strictly adheres to Arise ERP Mobile Design System (Plus Jakarta Sans & 4px radius)
   ============================================================================== */

:root {
    --mon-navy-900: #001428;
    --mon-navy-800: #002C54;
    --mon-sky: #0284c7;
    --mon-emerald: #10b981;
    --mon-amber: #f59e0b;
    --mon-rose: #f43f5e;
    --mon-card-border: #cbd5e1;
}

.mob-month-page {
    padding: 8px 10px 75px;
    background: #f1f5f9;
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
}
.mob-month-page * {
    box-sizing: border-box;
}

/* 1. Hero Card */
.mob-mon-hero {
    background: linear-gradient(135deg, #001428 0%, #002C54 55%, #08335c 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    margin-bottom: 8px;
    position: relative;
}
.mob-mon-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-mon-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.mob-mon-top-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-mon-tool-btn {
    height: 26px;
    padding: 0 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-mon-tool-btn:active {
    background: rgba(255, 255, 255, 0.28);
    transform: scale(0.95);
}

/* Month Navigator Bar inside Hero */
.mob-mon-date-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 4px;
    padding: 3px 6px;
    margin-bottom: 8px;
}
.mob-mon-arrow {
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-mon-arrow:active {
    background: rgba(255, 255, 255, 0.3);
}
.mob-mon-current-wrap {
    text-align: center;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
}
.mob-mon-current-text {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -0.01em;
}

/* 4-Glance Metric Grid */
.mob-mon-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-mon-metric-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 5px 3px;
    text-align: center;
}
.mob-mon-metric-lbl {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-mon-metric-num {
    font-size: 13px;
    font-weight: 900;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
    white-space: nowrap;
}
.mob-mon-metric-sub {
    font-size: 8px;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.color-rate { color: #34d399 !important; }
.color-staff { color: #38bdf8 !important; }
.color-present { color: #86efac !important; }
.color-absent { color: #fecdd3 !important; }

/* Fast Actions */
.mob-mon-actions-bar {
    display: flex;
    gap: 5px;
}
.mob-mon-act-btn {
    flex: 1;
    height: 28px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-mon-act-btn:active { transform: scale(0.97); }
.btn-mon-export { background: #16a34a; color: #ffffff !important; }
.btn-mon-filter { background: rgba(255, 255, 255, 0.16); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.28); }

/* 2. Search & View Mode Toolbar */
.mob-mon-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-mon-search-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-mon-search-wrap {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}
.mob-mon-search-icon {
    position: absolute;
    left: 8px;
    color: #64748b;
    font-size: 11px;
    pointer-events: none;
}
.mob-mon-search-input {
    width: 100%;
    height: 30px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    font-size: 11.5px;
    padding: 0 26px 0 26px;
    outline: none;
    transition: border-color .15s ease;
}
.mob-mon-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-mon-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
}
.mob-mon-view-toggle {
    height: 30px;
    padding: 0 8px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    white-space: nowrap;
}
.mob-mon-view-toggle.active {
    background: #002C54;
    color: #ffffff;
    border-color: #001f3f;
}

/* 3. Staff Monthly Feed Card */
.mob-staff-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    margin-bottom: 7px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    overflow: hidden;
}
.mob-staff-head {
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    cursor: pointer;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.mob-staff-main-info {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-staff-avatar-initial {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.mob-staff-text-meta {
    flex: 1;
    min-width: 0;
}
.mob-staff-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.mob-staff-sub-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 9.5px;
    color: #64748b;
    margin-top: 1px;
}
.mob-role-tag {
    font-size: 8.5px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 0 5px;
    border-radius: 2px;
    text-transform: uppercase;
}

/* Rate & Expand indicator */
.mob-staff-rate-box {
    text-align: right;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-staff-rate-num {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.mob-staff-rate-badge {
    font-size: 8px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 2px;
    display: inline-block;
}
.rate-excellent { background: #dcfce7; color: #15803d; }
.rate-good { background: #e0f2fe; color: #0369a1; }
.rate-warning { background: #fef3c7; color: #b45309; }
.rate-poor { background: #fee2e2; color: #b91c1c; }

/* Status Summary Strip */
.mob-staff-summary-strip {
    padding: 5px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.mob-sum-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.sum-p { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.sum-a { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.sum-hd { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.sum-h { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }

/* Day-by-day Mini Calendar Grid (Collapsible) */
.mob-day-grid-body {
    padding: 8px 10px;
    background: #ffffff;
}
.mob-day-grid-head {
    font-size: 9.5px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-day-matrix-cells {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.mob-day-cell {
    aspect-ratio: 1;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border: 1px solid #e2e8f0;
}
.mob-day-cell .day-d { font-size: 7.5px; opacity: 0.75; margin-bottom: 1px; }
.mob-day-cell .day-s { font-size: 9px; font-weight: 800; }
.day-st-present { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.day-st-absent { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.day-st-halfday { background: #fef3c7; color: #b45309; border-color: #fde68a; }
.day-st-holiday { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
.day-st-none { background: #f8fafc; color: #94a3b8; border-color: #f1f5f9; }

/* 4. Full Horizontal Spreadsheet Matrix Mode */
.mob-mon-table-wrap {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 8px;
}
.mob-mon-matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 10px;
}
.mob-mon-matrix-table thead th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 5px 4px;
    font-size: 9.5px;
    font-weight: 700;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    text-align: center;
}
.mob-mon-matrix-table tbody tr:nth-child(even) { background: #f8fafc; }
.mob-mon-matrix-table tbody td {
    padding: 5px 3px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}
.td-sticky-name {
    position: sticky;
    left: 0;
    background: #ffffff;
    z-index: 10;
    text-align: left !important;
    font-weight: 700;
    color: #002C54;
    border-right: 2px solid #cbd5e1 !important;
    min-width: 120px;
    max-width: 140px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-mon-matrix-table tbody tr:nth-child(even) .td-sticky-name {
    background: #f8fafc;
}
.th-sticky-name {
    position: sticky;
    left: 0;
    z-index: 25;
    background: #002342 !important;
    text-align: left !important;
    border-right: 2px solid #cbd5e1 !important;
}

/* 5. Bottom Sheet Filter */
.mob-sheet-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 20, 40, 0.65);
    backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: all .2s ease;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
    z-index: 99995;
    transform: translateY(105%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 520px;
    margin: 0 auto;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}
.mob-sheet-head {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.mob-sheet-body {
    padding: 12px 14px;
    overflow-y: auto;
    flex: 1;
}
.mob-sheet-foot {
    padding: 10px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
}

/* Empty State */
.mob-empty-box {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 28px 14px;
    text-align: center;
    margin-top: 10px;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 8px;
}
.mob-empty-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    max-width: 320px;
    margin: 0 auto;
}

/* Print Overrides */
@media print {
    .mob-month-page { padding: 0 !important; background: #ffffff !important; }
    .mob-mon-hero-top .mob-mon-top-actions, .mob-mon-actions-bar, .mob-mon-toolbar, .mobile-bottom-dock, .mobile-top-bar {
        display: none !important;
    }
    .mob-mon-hero { background: none !important; color: #000000 !important; border: none !important; box-shadow: none !important; border-bottom: 2px solid #000000 !important; }
    .mob-mon-hero-title, .mob-staff-name { color: #000000 !important; }
    .mob-staff-card { border-color: #999999 !important; box-shadow: none !important; }
    .mob-day-grid-body { display: block !important; }
}
</style>
@endsection

@section('content')
<div class="mob-month-page">

    {{-- 1. NATIVE MOBILE HERO CARD --}}
    <div class="mob-mon-hero">
        <div class="mob-mon-hero-top">
            <div class="mob-mon-hero-title">
                <i class="fa fa-calendar-check-o text-warning"></i>
                <span>Monthly Attendance</span>
            </div>
            <div class="mob-mon-top-actions">
                <a href="{{ $exportUrl }}" class="mob-mon-tool-btn" title="Export CSV Report">
                    <i class="fa fa-file-excel-o"></i> CSV
                </a>
                <button type="button" class="mob-mon-tool-btn" onclick="window.print()" title="Print Report">
                    <i class="fa fa-print"></i>
                </button>
            </div>
        </div>

        {{-- Month Navigator Bar inside Hero --}}
        <div class="mob-mon-date-nav">
            <a href="{{ $prevUrl }}" class="mob-mon-arrow" title="Previous Month">
                <i class="fa fa-chevron-left"></i>
            </a>

            <div class="mob-mon-current-wrap" id="btnOpenFilterSheet">
                <i class="fa fa-calendar-o text-info"></i>
                <div class="mob-mon-current-text">{{ $monthName }} {{ $year }}</div>
                <i class="fa fa-caret-down text-muted" style="font-size: 11px;"></i>
            </div>

            <a href="{{ $nextUrl }}" class="mob-mon-arrow" title="Next Month">
                <i class="fa fa-chevron-right"></i>
            </a>
        </div>

        {{-- 4-Glance Metric Grid --}}
        <div class="mob-mon-metrics-grid">
            <div class="mob-mon-metric-cell">
                <div class="mob-mon-metric-lbl"><i class="fa fa-percent"></i> Rate</div>
                <div class="mob-mon-metric-num color-rate">{{ $overallStats['avgAttendanceRate'] }}%</div>
                <div class="mob-mon-metric-sub">Avg Rate</div>
            </div>

            <div class="mob-mon-metric-cell">
                <div class="mob-mon-metric-lbl"><i class="fa fa-users"></i> Staff</div>
                <div class="mob-mon-metric-num color-staff">{{ $overallStats['totalStaff'] }}</div>
                <div class="mob-mon-metric-sub">Enrolled</div>
            </div>

            <div class="mob-mon-metric-cell">
                <div class="mob-mon-metric-lbl"><i class="fa fa-check"></i> Present</div>
                <div class="mob-mon-metric-num color-present">{{ $overallStats['totalPresent'] }}</div>
                <div class="mob-mon-metric-sub">Days</div>
            </div>

            <div class="mob-mon-metric-cell">
                <div class="mob-mon-metric-lbl"><i class="fa fa-times"></i> Absent</div>
                <div class="mob-mon-metric-num color-absent">{{ $overallStats['totalAbsent'] }}</div>
                <div class="mob-mon-metric-sub">Days</div>
            </div>
        </div>

        {{-- Fast Action Bar --}}
        <div class="mob-mon-actions-bar">
            <a href="{{ $exportUrl }}" class="mob-mon-act-btn btn-mon-export">
                <i class="fa fa-download"></i> Download CSV
            </a>
            <button type="button" class="mob-mon-act-btn btn-mon-filter" id="btnFilterTrigger">
                <i class="fa fa-sliders"></i> Month &amp; Role Filter
            </button>
        </div>
    </div>

    {{-- 2. QUICK SEARCH & VIEW MODE TOOLBAR --}}
    <div class="mob-mon-toolbar">
        <div class="mob-mon-search-row">
            <div class="mob-mon-search-wrap">
                <i class="fa fa-search mob-mon-search-icon"></i>
                <input type="text" id="mobStaffSearch" class="mob-mon-search-input" placeholder="Search staff by name or ID..." autocomplete="off">
                <i class="fa fa-times-circle mob-mon-search-clear" id="mobStaffSearchClear"></i>
            </div>
            <button type="button" class="mob-mon-view-toggle" id="btnToggleMatrixView">
                <i class="fa fa-table"></i> <span>Table View</span>
            </button>
        </div>
    </div>

    {{-- 3. STAFF CARDS FEED (CARD VIEW) --}}
    <div id="staffCardsContainer">
        @forelse($users as $idx => $user)
            @php
                $sums = $summaryData[$user->id] ?? ['present' => 0, 'absent' => 0, 'halfday' => 0, 'holiday' => 0];
                $uInit = strtoupper(mb_substr($user->name ?: 'S', 0, 1, 'UTF-8'));
                $roleName = $rolesMap[$user->role_id] ?? 'Staff';
                $workTotal = $sums['present'] + $sums['absent'] + $sums['halfday'];
                $uRate = $workTotal > 0 ? round((($sums['present'] + ($sums['halfday'] * 0.5)) / $workTotal) * 100, 1) : null;

                $rateClass = 'rate-excellent';
                if ($uRate !== null) {
                    if ($uRate >= 85) $rateClass = 'rate-excellent';
                    elseif ($uRate >= 70) $rateClass = 'rate-good';
                    elseif ($uRate >= 50) $rateClass = 'rate-warning';
                    else $rateClass = 'rate-poor';
                }
            @endphp

            <div class="mob-staff-card" data-staff-name="{{ strtolower($user->name ?? '') }}" data-staff-id="{{ strtolower($user->attendance_unique_id ?? ('USR-'.$user->id)) }}">
                <div class="mob-staff-head" onclick="toggleStaffCalendar('{{ $user->id }}')">
                    <div class="mob-staff-main-info">
                        <div class="mob-staff-avatar-initial">{{ $uInit }}</div>
                        <div class="mob-staff-text-meta">
                            <div class="mob-staff-name">{{ $user->name }}</div>
                            <div class="mob-staff-sub-strip">
                                <span class="mob-role-tag">{{ $roleName }}</span>
                                <span>#{{ $user->attendance_unique_id ?: ('USR-' . $user->id) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mob-staff-rate-box">
                        <div>
                            <div class="mob-staff-rate-num">{{ $uRate !== null ? $uRate . '%' : 'N/A' }}</div>
                            <span class="mob-staff-rate-badge {{ $rateClass }}">{{ $uRate !== null ? 'Rate' : 'No Log' }}</span>
                        </div>
                        <i class="fa fa-chevron-down text-muted" id="chev-{{ $user->id }}" style="font-size: 11px; transition: transform .2s ease;"></i>
                    </div>
                </div>

                {{-- Status Summary Strip --}}
                <div class="mob-staff-summary-strip">
                    <span class="mob-sum-pill sum-p"><i class="fa fa-check"></i> P: <b>{{ $sums['present'] }}</b></span>
                    <span class="mob-sum-pill sum-a"><i class="fa fa-times"></i> A: <b>{{ $sums['absent'] }}</b></span>
                    <span class="mob-sum-pill sum-hd"><i class="fa fa-adjust"></i> HD: <b>{{ $sums['halfday'] }}</b></span>
                    <span class="mob-sum-pill sum-h"><i class="fa fa-calendar-o"></i> H: <b>{{ $sums['holiday'] }}</b></span>
                </div>

                {{-- Day-by-Day Mini Calendar Grid (Collapsible, shown on click) --}}
                <div class="mob-day-grid-body" id="grid-body-{{ $user->id }}" style="display: none;">
                    <div class="mob-day-grid-head">
                        <span><i class="fa fa-calendar-check-o text-primary"></i> {{ $monthName }} {{ $year }} Daily Breakdown</span>
                        <span style="font-size: 8.5px; color: #64748b;">(Tap day to view time)</span>
                    </div>

                    <div class="mob-day-matrix-cells">
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $mark = $attendanceData[$user->id][$d] ?? null;
                                $st = $mark ? strtolower((string)$mark->status) : '';
                                
                                $cellClass = 'day-st-none';
                                $stText = '-';

                                if ($st === 'present' || $st === 'in' || $st === 'out') {
                                    $cellClass = 'day-st-present';
                                    $stText = 'P';
                                } elseif ($st === 'absent') {
                                    $cellClass = 'day-st-absent';
                                    $stText = 'A';
                                } elseif ($st === 'halfday') {
                                    $cellClass = 'day-st-halfday';
                                    $stText = 'HD';
                                } elseif ($st === 'holiday') {
                                    $cellClass = 'day-st-holiday';
                                    $stText = 'H';
                                }
                            @endphp
                            <div class="mob-day-cell {{ $cellClass }}" title="Day {{ $d }}: {{ $mark ? ucfirst($mark->status) : 'Unrecorded' }}">
                                <span class="day-d">{{ $d }}</span>
                                <span class="day-s">{{ $stText }}</span>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @empty
            <div class="mob-empty-box">
                <div class="mob-empty-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="mob-empty-title">No Staff Members Found</div>
                <div class="mob-empty-desc">
                    There are no active staff records matching the selected role for this branch and session.
                </div>
            </div>
        @endforelse
    </div>

    {{-- 4. FULL SPREADSHEET MATRIX TABLE (OPTIONAL TOGGLE) --}}
    <div id="staffMatrixTableView" style="display: none;">
        <div class="mob-mon-table-wrap">
            <table class="mob-mon-matrix-table">
                <thead>
                    <tr>
                        <th class="th-sticky-name">Staff Name</th>
                        <th style="min-width: 45px;">Role</th>
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            <th style="width: 24px;">{{ $d }}</th>
                        @endfor
                        <th style="background: #14532d;">P</th>
                        <th style="background: #7f1d1d;">A</th>
                        <th style="background: #78350f;">HD</th>
                        <th style="background: #1e3a8a;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $sums = $summaryData[$user->id] ?? ['present' => 0, 'absent' => 0, 'halfday' => 0, 'holiday' => 0];
                            $workTotal = $sums['present'] + $sums['absent'] + $sums['halfday'];
                            $uRate = $workTotal > 0 ? round((($sums['present'] + ($sums['halfday'] * 0.5)) / $workTotal) * 100, 1) : null;
                        @endphp
                        <tr>
                            <td class="td-sticky-name" title="{{ $user->name }}">
                                {{ $user->name }}
                            </td>
                            <td>
                                <span class="mob-role-tag">{{ $rolesMap[$user->role_id] ?? 'Staff' }}</span>
                            </td>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $mark = $attendanceData[$user->id][$d] ?? null;
                                    $st = $mark ? strtolower((string)$mark->status) : '';
                                    $stLetter = '-';
                                    $bgStyle = 'color: #94a3b8;';

                                    if ($st === 'present' || $st === 'in' || $st === 'out') {
                                        $stLetter = 'P';
                                        $bgStyle = 'background:#dcfce7; color:#15803d; font-weight:800;';
                                    } elseif ($st === 'absent') {
                                        $stLetter = 'A';
                                        $bgStyle = 'background:#fee2e2; color:#b91c1c; font-weight:800;';
                                    } elseif ($st === 'halfday') {
                                        $stLetter = 'HD';
                                        $bgStyle = 'background:#fef3c7; color:#b45309; font-weight:800;';
                                    } elseif ($st === 'holiday') {
                                        $stLetter = 'H';
                                        $bgStyle = 'background:#e0f2fe; color:#0369a1; font-weight:800;';
                                    }
                                @endphp
                                <td style="{{ $bgStyle }}">{{ $stLetter }}</td>
                            @endfor
                            <td style="font-weight: 800; color: #15803d;">{{ $sums['present'] }}</td>
                            <td style="font-weight: 800; color: #b91c1c;">{{ $sums['absent'] }}</td>
                            <td style="font-weight: 800; color: #b45309;">{{ $sums['halfday'] }}</td>
                            <td style="font-weight: 800; color: #002C54;">{{ $uRate !== null ? $uRate . '%' : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Empty Search Results placeholder --}}
    <div id="noStaffSearchMatch" class="mob-empty-box" style="display: none;">
        <div class="mob-empty-icon">
            <i class="fa fa-search"></i>
        </div>
        <div class="mob-empty-title">No Matching Staff</div>
        <div class="mob-empty-desc">
            No staff records match your search criteria. Try a different name or staff ID.
        </div>
    </div>

</div>

{{-- 5. NATIVE MONTH & ROLE FILTER BOTTOM SHEET DRAWER --}}
<div class="mob-sheet-backdrop" id="filterSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="filterBottomSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders"></i> Month &amp; Role Filter
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>

    <form method="GET" action="{{ url('monthlyReport') }}" id="monFilterForm">
        <input type="hidden" name="layout" value="mobile">
        <div class="mob-sheet-body">
            {{-- Month Selection --}}
            <div style="margin-bottom: 12px;">
                <label style="font-size: 10.5px; font-weight: 800; color: #334155; text-transform: uppercase; margin-bottom: 4px; display: block;">
                    <i class="fa fa-calendar text-primary"></i> Select Month
                </label>
                <select name="month" class="form-control" style="height: 34px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1;">
                    @foreach($monthsList as $mNum => $mLabel)
                        <option value="{{ $mNum }}" {{ $mNum == $month ? 'selected' : '' }}>
                            {{ $mLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Year Selection --}}
            <div style="margin-bottom: 12px;">
                <label style="font-size: 10.5px; font-weight: 800; color: #334155; text-transform: uppercase; margin-bottom: 4px; display: block;">
                    <i class="fa fa-calendar-check-o text-primary"></i> Select Year
                </label>
                <select name="year" class="form-control" style="height: 34px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1;">
                    @for($y = date('Y') + 1; $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            {{-- Role Selection (if admin) --}}
            @if($staffRoles->isNotEmpty())
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 10.5px; font-weight: 800; color: #334155; text-transform: uppercase; margin-bottom: 4px; display: block;">
                        <i class="fa fa-user-circle-o text-primary"></i> Staff Role
                    </label>
                    <select name="role_id" class="form-control" style="height: 34px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        <option value="">All Roles (All Staff)</option>
                        @foreach($staffRoles as $sr)
                            <option value="{{ $sr->id }}" {{ $sr->id == $selectedRoleId ? 'selected' : '' }}>
                                {{ $sr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="mob-sheet-foot">
            <button type="button" class="btn btn-secondary" id="btnCancelFilterSheet" style="flex: 1; font-size: 11.5px; font-weight: 700; height: 32px;">Cancel</button>
            <button type="submit" class="btn btn-primary" style="flex: 2; font-size: 11.5px; font-weight: 700; height: 32px; background: #002C54; border-color: #001f3f;">
                <i class="fa fa-check"></i> Apply Report Filter
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Bottom Sheet Controls
    var sheet = document.getElementById('filterBottomSheet');
    var backdrop = document.getElementById('filterSheetBackdrop');
    var btnOpen = document.getElementById('btnFilterTrigger');
    var btnDateOpen = document.getElementById('btnOpenFilterSheet');
    var btnClose = document.getElementById('btnCloseFilterSheet');
    var btnCancel = document.getElementById('btnCancelFilterSheet');

    function openSheet() {
        sheet.classList.add('show');
        backdrop.classList.add('show');
    }
    function closeSheet() {
        sheet.classList.remove('show');
        backdrop.classList.remove('show');
    }

    if (btnOpen) btnOpen.addEventListener('click', openSheet);
    if (btnDateOpen) btnDateOpen.addEventListener('click', openSheet);
    if (btnClose) btnClose.addEventListener('click', closeSheet);
    if (btnCancel) btnCancel.addEventListener('click', closeSheet);
    if (backdrop) backdrop.addEventListener('click', closeSheet);

    // 2. Client-side Search
    var searchInput = document.getElementById('mobStaffSearch');
    var clearBtn = document.getElementById('mobStaffSearchClear');
    var cards = document.querySelectorAll('.mob-staff-card');
    var noMatch = document.getElementById('noStaffSearchMatch');

    function filterStaff() {
        var term = searchInput.value.trim().toLowerCase();
        if (term.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }

        var visible = 0;
        cards.forEach(function(card) {
            var name = card.getAttribute('data-staff-name') || '';
            var id = card.getAttribute('data-staff-id') || '';
            if (term === '' || name.includes(term) || id.includes(term)) {
                card.style.display = 'block';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });

        if (cards.length > 0 && visible === 0) {
            noMatch.style.display = 'block';
        } else {
            noMatch.style.display = 'none';
        }
    }

    if (searchInput) searchInput.addEventListener('input', filterStaff);
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterStaff();
            searchInput.focus();
        });
    }

    // 3. View Mode Switcher (Card Feed vs Matrix Table)
    var btnToggle = document.getElementById('btnToggleMatrixView');
    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            var cardsBox = document.getElementById('staffCardsContainer');
            var tableBox = document.getElementById('staffMatrixTableView');
            var isTable = tableBox.style.display !== 'none';

            if (isTable) {
                tableBox.style.display = 'none';
                cardsBox.style.display = 'block';
                btnToggle.classList.remove('active');
                btnToggle.querySelector('span').textContent = 'Table View';
                btnToggle.querySelector('i').className = 'fa fa-table';
            } else {
                tableBox.style.display = 'block';
                cardsBox.style.display = 'none';
                btnToggle.classList.add('active');
                btnToggle.querySelector('span').textContent = 'Card View';
                btnToggle.querySelector('i').className = 'fa fa-th-large';
            }
        });
    }
});

// Toggle Staff Mini-Calendar
function toggleStaffCalendar(userId) {
    var body = document.getElementById('grid-body-' + userId);
    var chev = document.getElementById('chev-' + userId);
    if (!body) return;

    if (body.style.display === 'none') {
        body.style.display = 'block';
        if (chev) chev.style.transform = 'rotate(180deg)';
    } else {
        body.style.display = 'none';
        if (chev) chev.style.transform = 'rotate(0deg)';
    }
}
</script>
@endsection

@php
    $attendanceType = $setting->attendance_type ?? 2;
    $typeLabel = $attendanceType == 1 ? 'Biometric' : ($attendanceType == 3 ? 'QR Code' : 'Normal / Manual');
    $isStaffTab = ($activeTab ?? 'students') === 'staff';
    $selectedTitleName = $selectedName ?? $selectedUniqueId;

    // Export URL
    $exportUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $month . '&year=' . $year . '&export=1');
    if ($isStaffTab) {
        $exportUrl .= '&staff=' . urlencode((string) $selectedUniqueId);
    } else {
        $exportUrl .= '&student=' . urlencode((string) $selectedUniqueId);
        if (!empty($classFilter)) {
            $exportUrl .= '&class_type_id=' . urlencode((string) $classFilter);
        }
    }

    // Prev / Next Month Calculations
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear = $month == 12 ? $year + 1 : $year;

    $prevUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $prevMonth . '&year=' . $prevYear . '&class_type_id=' . urlencode((string)$classFilter) . ($isStaffTab ? '&staff=' . urlencode((string)$selectedUniqueId) : '&student=' . urlencode((string)$selectedUniqueId)));
    $nextUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $nextMonth . '&year=' . $nextYear . '&class_type_id=' . urlencode((string)$classFilter) . ($isStaffTab ? '&staff=' . urlencode((string)$selectedUniqueId) : '&student=' . urlencode((string)$selectedUniqueId)));

    // Days list for timeline
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);

    // Selected Class Name
    $selectedClassObj = $classes->firstWhere('id', (int)$classFilter) ?? $classes->first();
    $selectedClassName = $selectedClassObj->name ?? 'Select Class';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE ATTENDANCE VIEW & CALENDAR WITH REAL-TIME PICKER
   ========================================================================== */

/* 1. Dark Navy Hero Banner */
.mob-view-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-view-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-view-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.mob-badge-rate {
    font-size: 10px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 12px;
    background: #0284c7;
    color: #ffffff;
    border: 1px solid #38bdf8;
}
.mob-view-entity {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 4px;
    padding: 6px 10px;
    margin-bottom: 8px;
}
.mob-view-entity-name {
    font-size: 12px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mob-view-entity-id {
    font-size: 9.5px;
    color: #93c5fd;
    font-weight: 700;
    background: rgba(0, 0, 0, 0.25);
    padding: 1px 6px;
    border-radius: 3px;
    white-space: nowrap;
}

/* Hero KPI Metrics */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 4px;
}
.mob-metric-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 3px;
    padding: 4px 2px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-metric-val {
    font-size: 12.5px;
    font-weight: 800;
    line-height: 1;
}
.val-working { color: #38bdf8; }
.val-present { color: #4ade80; }
.val-absent { color: #f87171; }
.val-halfday { color: #c084fc; }
.val-holiday { color: #cbd5e1; }

/* 2. Navigation Tabs (Students vs Staff) */
.mob-tabs-bar {
    display: flex;
    background: #002C54;
    border-radius: 4px;
    padding: 3px;
    gap: 4px;
    margin-bottom: 8px;
    border: 1px solid #001f3d;
}
.mob-tab-btn {
    flex: 1;
    text-align: center;
    padding: 6px 4px;
    font-size: 11.5px;
    font-weight: 700;
    color: #cbd5e1;
    text-decoration: none !important;
    border-radius: 3px;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.mob-tab-btn.active {
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,.25);
}

/* 3. Filter Card & Custom Picker Triggers */
.mob-filter-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.mob-filter-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-filter-row.single-col {
    grid-template-columns: 1fr;
}
.mob-filter-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.mob-filter-label {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-picker-trigger {
    height: 34px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    transition: all .15s;
}
.mob-picker-trigger:active {
    background: #e0f2fe;
    border-color: #0284c7;
}
.mob-picker-trigger-text {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-picker-trigger i.chevron {
    font-size: 10px;
    color: #64748b;
    margin-left: 4px;
    flex-shrink: 0;
}

/* Quick Search Box inside Filter Card */
.mob-quick-search-box {
    position: relative;
    margin-bottom: 6px;
}
.mob-quick-search-box i.fa-search {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.mob-quick-search-input {
    width: 100%;
    height: 30px;
    padding: 0 26px 0 28px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    outline: none;
    color: #0f172a;
    transition: all .15s;
}
.mob-quick-search-input:focus {
    background: #ffffff;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.08);
}
.mob-quick-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    cursor: pointer;
    font-size: 11px;
    display: none;
}

/* Month Navigation Bar */
.mob-month-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 4px 6px;
}
.mob-nav-arrow {
    width: 28px;
    height: 28px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #002C54;
    text-decoration: none !important;
    font-size: 11px;
    transition: all .15s;
}
.mob-nav-arrow:active {
    background: #002C54;
    color: #ffffff;
}
.mob-nav-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-nav-select {
    height: 26px;
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 6px;
    outline: none;
}

/* 4. Monthly Calendar Card */
.mob-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.mob-card-header {
    background: #002C54;
    color: #ffffff;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-card-title {
    font-size: 12px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-card-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-card-btn {
    font-size: 10px;
    font-weight: 700;
    color: #93c5fd;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    padding: 2px 7px;
    border-radius: 3px;
    text-decoration: none !important;
}

/* Calendar Grid */
.mob-cal-body {
    padding: 8px;
}
.mob-cal-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
    margin-bottom: 4px;
    text-align: center;
}
.mob-cal-wd {
    font-size: 9.5px;
    font-weight: 800;
    color: #002C54;
    background: #f1f5f9;
    border-radius: 2px;
    padding: 3px 0;
    text-transform: uppercase;
}
.mob-cal-wd.is-sun {
    color: #dc2626;
    background: #fee2e2;
}

.mob-cal-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 3px;
}
.mob-cal-cell {
    aspect-ratio: 1 / 1;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 2px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    position: relative;
    transition: all .15s;
    user-select: none;
}
.mob-cal-cell:active, .mob-cal-cell.active {
    border-color: #002C54 !important;
    background: #e0f2fe !important;
    box-shadow: 0 0 0 1.5px #002C54;
}
.mob-cal-cell.empty {
    background: #f8fafc;
    border-style: dashed;
    border-color: #f1f5f9;
    pointer-events: none;
}
.mob-cell-num {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}
.mob-cell-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-bottom: 2px;
}

/* Status Colors */
.st-dot-present, .st-dot-in { background-color: #16a34a; }
.st-dot-out { background-color: #0284c7; }
.st-dot-absent { background-color: #dc2626; }
.st-dot-halfday { background-color: #9333ea; }
.st-dot-holiday { background-color: #64748b; }
.st-dot-none { background-color: transparent; }

.badge-present, .badge-in { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-out { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.badge-absent { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.badge-halfday { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }
.badge-holiday { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.badge-none { background: #f8fafc; color: #94a3b8; border: 1px solid #e2e8f0; }

/* Legend Bar */
.mob-legend-bar {
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 6px 4px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    font-size: 9px;
    font-weight: 700;
    color: #475569;
}
.mob-legend-item {
    display: flex;
    align-items: center;
    gap: 3px;
}
.mob-legend-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

/* 5. Day Inspection Sheet/Card */
.mob-inspect-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.mob-inspect-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 6px;
    margin-bottom: 6px;
}
.mob-inspect-date {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-inspect-status-pill {
    font-size: 10px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 3px;
    text-transform: uppercase;
}
.mob-inspect-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.mob-inspect-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 5px 8px;
}
.mob-inspect-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    display: block;
    margin-bottom: 1px;
}
.mob-inspect-val {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
}

/* 6. Daily Activity History List (Timeline) */
.mob-timeline-filters {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    overflow-x: auto;
    scrollbar-width: none;
}
.mob-timeline-filter-chip {
    padding: 2px 8px;
    font-size: 9.5px;
    font-weight: 700;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    white-space: nowrap;
    user-select: none;
    transition: all .15s;
}
.mob-timeline-filter-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.mob-timeline-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11px;
}
.mob-timeline-item:last-child {
    border-bottom: none;
}
.mob-timeline-left {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mob-timeline-daynum {
    width: 26px;
    height: 26px;
    border-radius: 4px;
    background: #f1f5f9;
    color: #002C54;
    font-size: 11px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #cbd5e1;
    flex-shrink: 0;
}
.mob-timeline-dayname {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    display: block;
    line-height: 1.1;
}
.mob-timeline-date {
    font-size: 9px;
    color: #64748b;
    font-weight: 500;
}
.mob-timeline-right {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-timeline-time {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
}

/* 7. Yearly Heatmap Mini Grid */
.mob-yearly-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    padding: 6px;
}
.mob-year-month-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 4px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.mob-ym-title {
    font-size: 10px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-ym-dots {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1.5px;
    margin-bottom: 3px;
}
.mob-ym-dot {
    width: 4px;
    height: 4px;
    border-radius: 1px;
    background: #e2e8f0;
}
.mob-ym-stat {
    font-size: 8px;
    font-weight: 700;
    color: #64748b;
    border-top: 1px solid #e2e8f0;
    padding-top: 2px;
    display: flex;
    justify-content: space-between;
}

/* 8. Modern Searchable Bottom Sheet Modal */
.mob-picker-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.65);
    backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease-in-out;
}
.mob-picker-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-picker-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 14px 14px 0 0;
    box-shadow: 0 -6px 25px rgba(0, 44, 84, 0.35);
    z-index: 99995;
    transform: translateY(100%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 540px;
    margin: 0 auto;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.mob-picker-sheet.show {
    transform: translateY(0);
}
.mob-sheet-handle-bar {
    width: 36px;
    height: 4px;
    background: #cbd5e1;
    border-radius: 2px;
    margin: 8px auto 4px;
}
.mob-picker-sheet-header {
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e2e8f0;
}
.mob-picker-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-picker-sheet-close {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 14px;
    cursor: pointer;
}
.mob-picker-sheet-search {
    padding: 8px 14px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    position: relative;
}
.mob-picker-sheet-search i {
    position: absolute;
    left: 24px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 12px;
}
.mob-picker-search-input {
    width: 100%;
    height: 34px;
    padding-left: 32px;
    padding-right: 10px;
    font-size: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #ffffff;
    outline: none;
    color: #0f172a;
}
.mob-picker-search-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-picker-items-list {
    flex: 1;
    overflow-y: auto;
    padding: 4px 8px 16px;
}
.mob-picker-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 4px;
    margin-bottom: 3px;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all .12s ease;
}
.mob-picker-item:hover, .mob-picker-item:active {
    background: #f0f9ff;
    border-color: #bae6fd;
}
.mob-picker-item.selected {
    background: #e0f2fe;
    border-color: #0284c7;
}
.mob-picker-item-left {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
}
.mob-picker-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.mob-picker-item.selected .mob-picker-avatar {
    background: #0284c7;
}
.mob-picker-name {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    line-height: 1.2;
}
.mob-picker-sub {
    font-size: 9.5px;
    color: #64748b;
    display: block;
}
.mob-picker-check {
    font-size: 13px;
    color: #0284c7;
    opacity: 0;
}
.mob-picker-item.selected .mob-picker-check {
    opacity: 1;
}

/* Quick Floating Toast */
#mobToast {
    position: fixed;
    top: 14px;
    left: 50%;
    transform: translateX(-50%) translateY(-100px);
    background: #002C54;
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: transform .25s ease-out;
    pointer-events: none;
    border: 1px solid rgba(255,255,255,0.2);
}
#mobToast.show {
    transform: translateX(-50%) translateY(0);
}
</style>
@endsection

@section('content')
<div class="container-fluid px-1 py-1" style="max-width: 560px; margin: 0 auto; padding-bottom: 30px;">

    {{-- 1. Dark Navy Hero Banner --}}
    <div class="mob-view-hero">
        <div class="mob-view-hero-top">
            <span class="mob-view-title">
                <i class="fa fa-calendar-check-o text-info"></i> Attendance Overview
            </span>
            <span class="mob-badge-rate">
                {{ number_format($attendancePercent, 1) }}% Rate
            </span>
        </div>

        {{-- Entity Card in Hero --}}
        <div class="mob-view-entity">
            <div class="mob-view-entity-name">
                <i class="fa {{ $isStaffTab ? 'fa-user-circle' : 'fa-graduation-cap' }} text-info"></i>
                <span>{{ $selectedTitleName }}</span>
            </div>
            <span class="mob-view-entity-id">{{ $selectedUniqueId }}</span>
        </div>

        {{-- 5 KPI Metric Boxes --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-cell">
                <span class="mob-metric-tag">Total</span>
                <span class="mob-metric-val val-working">{{ $totalDays }}</span>
            </div>
            <div class="mob-metric-cell">
                <span class="mob-metric-tag">Present</span>
                <span class="mob-metric-val val-present">{{ $inDays }}</span>
            </div>
            <div class="mob-metric-cell">
                <span class="mob-metric-tag">Absent</span>
                <span class="mob-metric-val val-absent">{{ $absentDays }}</span>
            </div>
            <div class="mob-metric-cell">
                <span class="mob-metric-tag">Half Day</span>
                <span class="mob-metric-val val-halfday">{{ $halfDayDays }}</span>
            </div>
            <div class="mob-metric-cell">
                <span class="mob-metric-tag">Holiday</span>
                <span class="mob-metric-val val-holiday">{{ $holidayDays }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Navigation Tabs (Students vs Staff) --}}
    <div class="mob-tabs-bar">
        <a class="mob-tab-btn {{ !$isStaffTab ? 'active' : '' }}" href="{{ url('attendance/view?tab=students&month='.$month.'&year='.$year) }}">
            <i class="fa fa-graduation-cap"></i> Students
        </a>
        @if($canAccessStaffAttendance ?? false)
            <a class="mob-tab-btn {{ $isStaffTab ? 'active' : '' }}" href="{{ url('attendance/view?tab=staff&month='.$month.'&year='.$year) }}">
                <i class="fa fa-users"></i> Staff
            </a>
        @endif
    </div>

    {{-- 3. Filter Controls with Real-Time Searchable Pickers --}}
    <div class="mob-filter-card">
        <form method="get" action="{{ url('attendance/view') }}" id="mobFilterForm">
            <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">
            <input type="hidden" name="class_type_id" id="hidden_class_type_id" value="{{ $classFilter }}">
            <input type="hidden" name="{{ $isStaffTab ? 'staff' : 'student' }}" id="hidden_entity_id" value="{{ $selectedUniqueId }}">

            {{-- Entity Selectors --}}
            @if(!$isStaffTab)
                <div class="mob-filter-row">
                    {{-- Class Picker Trigger --}}
                    <div class="mob-filter-group">
                        <span class="mob-filter-label">
                            <span><i class="fa fa-building-o text-primary"></i> Class</span>
                            <span class="text-muted" style="font-size: 8px;">{{ $classes->count() }}</span>
                        </span>
                        <div class="mob-picker-trigger" id="btnOpenClassPicker">
                            <span class="mob-picker-trigger-text">
                                <i class="fa fa-building-o text-primary" style="font-size: 11px;"></i>
                                <span id="labelSelectedClass">{{ $selectedClassName }}</span>
                            </span>
                            <i class="fa fa-chevron-down chevron"></i>
                        </div>
                    </div>

                    {{-- Student Picker Trigger --}}
                    <div class="mob-filter-group">
                        <span class="mob-filter-label">
                            <span><i class="fa fa-user text-info"></i> Student</span>
                            <span class="text-muted" style="font-size: 8px;">{{ $students->count() }}</span>
                        </span>
                        <div class="mob-picker-trigger" id="btnOpenStudentPicker">
                            <span class="mob-picker-trigger-text">
                                <i class="fa fa-user text-info" style="font-size: 11px;"></i>
                                <span id="labelSelectedStudent">{{ $selectedTitleName }}</span>
                            </span>
                            <i class="fa fa-chevron-down chevron"></i>
                        </div>
                    </div>
                </div>
            @else
                <div class="mob-filter-row single-col">
                    {{-- Staff Picker Trigger --}}
                    <div class="mob-filter-group">
                        <span class="mob-filter-label">
                            <span><i class="fa fa-user-circle text-info"></i> Staff Member</span>
                            <span class="text-muted" style="font-size: 8px;">{{ $staff->count() }}</span>
                        </span>
                        <div class="mob-picker-trigger" id="btnOpenStaffPicker">
                            <span class="mob-picker-trigger-text">
                                <i class="fa fa-user-circle text-info" style="font-size: 11px;"></i>
                                <span id="labelSelectedStaff">{{ $selectedTitleName }} ({{ $selectedUniqueId }})</span>
                            </span>
                            <i class="fa fa-chevron-down chevron"></i>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Quick Filter Input Bar --}}
            <div class="mob-quick-search-box">
                <i class="fa fa-search"></i>
                <input type="text" id="mobInlineFilterInput" class="mob-quick-search-input" placeholder="Quick filter {{ $isStaffTab ? 'staff members' : 'students in this class' }} in real-time...">
                <i class="fa fa-times-circle mob-quick-search-clear" id="mobInlineFilterClear"></i>
            </div>

            {{-- Month & Year Selector Bar --}}
            <div class="mob-month-nav">
                <a href="{{ $prevUrl }}" class="mob-nav-arrow" title="Previous Month">
                    <i class="fa fa-chevron-left"></i>
                </a>

                <div class="mob-nav-title">
                    <select name="month" id="month_select" class="mob-nav-select" onchange="document.getElementById('mobFilterForm').submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('M', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" id="year_select" class="mob-nav-select" onchange="document.getElementById('mobFilterForm').submit()">
                        @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <a href="{{ $nextUrl }}" class="mob-nav-arrow" title="Next Month">
                    <i class="fa fa-chevron-right"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- 4. Monthly Calendar Grid Card --}}
    <div class="mob-card">
        <div class="mob-card-header">
            <span class="mob-card-title">
                <i class="fa fa-calendar text-info"></i> {{ $monthName }} {{ $year }}
            </span>
            <div class="mob-card-actions">
                <a href="{{ $exportUrl }}" class="mob-card-btn" title="Download Monthly CSV">
                    <i class="fa fa-download"></i> CSV
                </a>
            </div>
        </div>

        <div class="mob-cal-body">
            {{-- Weekday Row --}}
            <div class="mob-cal-weekdays">
                <div class="mob-cal-wd is-sun">Sun</div>
                <div class="mob-cal-wd">Mon</div>
                <div class="mob-cal-wd">Tue</div>
                <div class="mob-cal-wd">Wed</div>
                <div class="mob-cal-wd">Thu</div>
                <div class="mob-cal-wd">Fri</div>
                <div class="mob-cal-wd">Sat</div>
            </div>

            {{-- Days Grid --}}
            <div class="mob-cal-grid">
                @foreach($calendar as $week)
                    @foreach($week as $day)
                        @if(!$day)
                            <div class="mob-cal-cell empty"></div>
                        @else
                            @php
                                $mark = $marksByDate[$day] ?? null;
                                $status = $mark->status ?? ($calendarMonthMap[$day] ?? '');
                                $dotClass = $status ? 'st-dot-' . $status : 'st-dot-none';
                                $inTimeRaw = $mark->in_time ?? '';
                                $outTimeRaw = $mark->out_time ?? '';
                                $dayNumber = date('j', strtotime($day));
                            @endphp
                            <div class="mob-cal-cell" data-date="{{ $day }}" data-status="{{ $status }}" data-in="{{ $inTimeRaw }}" data-out="{{ $outTimeRaw }}">
                                <span class="mob-cell-num">{{ $dayNumber }}</span>
                                <span class="mob-cell-dot {{ $dotClass }}"></span>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Legend Bar --}}
        <div class="mob-legend-bar">
            <span class="mob-legend-item"><span class="mob-legend-dot st-dot-present"></span> Present</span>
            <span class="mob-legend-item"><span class="mob-legend-dot st-dot-absent"></span> Absent</span>
            <span class="mob-legend-item"><span class="mob-legend-dot st-dot-halfday"></span> Half Day</span>
            <span class="mob-legend-item"><span class="mob-legend-dot st-dot-holiday"></span> Holiday</span>
        </div>
    </div>

    {{-- 5. Day Inspection Detail Card --}}
    <div class="mob-inspect-card" id="mobInspectCard">
        <div class="mob-inspect-header">
            <span class="mob-inspect-date" id="mobInspectDate">
                <i class="fa fa-info-circle text-primary"></i> <span id="mobInspectDateText">Selected Day</span>
            </span>
            <span class="mob-inspect-status-pill badge-none" id="mobInspectStatusPill">Not Marked</span>
        </div>

        <div class="mob-inspect-body">
            <div class="mob-inspect-box">
                <span class="mob-inspect-lbl">Check-In Time</span>
                <span class="mob-inspect-val" id="mobInspectInTime">-</span>
            </div>
            <div class="mob-inspect-box">
                <span class="mob-inspect-lbl">Check-Out Time</span>
                <span class="mob-inspect-val" id="mobInspectOutTime">-</span>
            </div>
        </div>

        <div class="mt-2 text-right">
            <a href="{{ url('attendance/mark') }}" class="btn btn-sm btn-outline-primary" style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 3px;">
                <i class="fa fa-pencil mr-1"></i> Mark Attendance
            </a>
        </div>
    </div>

    {{-- 6. Monthly Daily History List with Live Filter Chips --}}
    <div class="mob-card">
        <div class="mob-card-header" style="background: #002342;">
            <span class="mob-card-title">
                <i class="fa fa-list-ul text-info"></i> Daily Activity Log ({{ $monthName }})
            </span>
        </div>

        {{-- Live Timeline Filter Chips --}}
        <div class="mob-timeline-filters">
            <span class="mob-timeline-filter-chip active" data-filter="all">All ({{ $daysInMonth }})</span>
            <span class="mob-timeline-filter-chip" data-filter="present">Present ({{ $inDays }})</span>
            <span class="mob-timeline-filter-chip" data-filter="absent">Absent ({{ $absentDays }})</span>
            <span class="mob-timeline-filter-chip" data-filter="halfday">Half Day ({{ $halfDayDays }})</span>
            <span class="mob-timeline-filter-chip" data-filter="holiday">Holiday ({{ $holidayDays }})</span>
        </div>

        <div style="max-height: 280px; overflow-y: auto;" id="mobTimelineContainer">
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $curDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    $mObj = $marksByDate[$curDate] ?? null;
                    $st = $mObj->status ?? ($calendarMonthMap[$curDate] ?? '');
                    $badgeCls = $st ? 'badge-' . $st : 'badge-none';
                    $stLabel = $st ? ucfirst(str_replace('_', ' ', $st)) : 'Not Marked';
                    $inT = $mObj && $mObj->in_time ? date('h:i A', strtotime($mObj->in_time)) : '';
                    $outT = $mObj && $mObj->out_time ? date('h:i A', strtotime($mObj->out_time)) : '';
                    $dayName = date('D', strtotime($curDate));
                @endphp
                <div class="mob-timeline-item" data-status="{{ $st }}">
                    <div class="mob-timeline-left">
                        <div class="mob-timeline-daynum">{{ $d }}</div>
                        <div>
                            <span class="mob-timeline-dayname">{{ $dayName }}</span>
                            <span class="mob-timeline-date">{{ date('d M Y', strtotime($curDate)) }}</span>
                        </div>
                    </div>
                    <div class="mob-timeline-right">
                        @if($inT || $outT)
                            <span class="mob-timeline-time">{{ $inT ?: '-' }} &bull; {{ $outT ?: '-' }}</span>
                        @endif
                        <span class="mob-inspect-status-pill {{ $badgeCls }}" style="font-size: 9px; padding: 2px 6px;">
                            {{ $stLabel }}
                        </span>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    {{-- 7. Yearly Overview Mini Grid (Jan - Dec) --}}
    <div class="mob-card">
        <div class="mob-card-header" style="background: #001f3d;">
            <span class="mob-card-title">
                <i class="fa fa-th text-info"></i> Yearly Summary ({{ $year }})
            </span>
        </div>
        <div class="mob-yearly-grid">
            @foreach($yearlyOverview as $item)
                <div class="mob-year-month-box">
                    <div class="mob-ym-title">
                        <span>{{ $item['label'] }}</span>
                        <span style="font-size: 8px; color: #94a3b8;">{{ $item['counts']['in'] ?? 0 }}P</span>
                    </div>
                    <div class="mob-ym-dots">
                        @foreach(array_slice($item['grid'], 0, 28) as $cell)
                            @if($cell === null)
                                <span class="mob-ym-dot" style="background: transparent;"></span>
                            @elseif($cell === '')
                                <span class="mob-ym-dot"></span>
                            @else
                                <span class="mob-ym-dot st-dot-{{ $cell }}"></span>
                            @endif
                        @endforeach
                    </div>
                    <div class="mob-ym-stat">
                        <span style="color: #16a34a;">P:{{ $item['counts']['in'] ?? 0 }}</span>
                        <span style="color: #dc2626;">A:{{ $item['counts']['absent'] ?? 0 }}</span>
                        <span style="color: #9333ea;">H:{{ $item['counts']['halfday'] ?? 0 }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ==========================================================================
     SEARCHABLE PICKER BOTTOM SHEETS (Class, Student, Staff)
     ========================================================================== --}}
<div class="mob-picker-backdrop" id="mobPickerBackdrop"></div>

{{-- 1. Class Searchable Picker Sheet --}}
<div class="mob-picker-sheet" id="classPickerSheet">
    <div class="mob-sheet-handle-bar"></div>
    <div class="mob-picker-sheet-header">
        <h3 class="mob-picker-sheet-title">
            <i class="fa fa-building-o text-primary"></i> Select Class
        </h3>
        <button type="button" class="mob-picker-sheet-close">&times;</button>
    </div>
    <div class="mob-picker-sheet-search">
        <i class="fa fa-search"></i>
        <input type="text" class="mob-picker-search-input" id="searchClassInput" placeholder="Search class name in real-time...">
    </div>
    <div class="mob-picker-items-list" id="classListItems">
        @foreach($classes as $c)
            @php
                $isSel = (string)$classFilter === (string)$c->id;
            @endphp
            <div class="mob-picker-item class-item {{ $isSel ? 'selected' : '' }}" data-id="{{ $c->id }}" data-name="{{ strtolower($c->name) }}">
                <div class="mob-picker-item-left">
                    <div class="mob-picker-avatar"><i class="fa fa-graduation-cap"></i></div>
                    <div>
                        <span class="mob-picker-name">{{ $c->name }}</span>
                        <span class="mob-picker-sub">ID: #{{ $c->id }}</span>
                    </div>
                </div>
                <i class="fa fa-check mob-picker-check"></i>
            </div>
        @endforeach
    </div>
</div>

{{-- 2. Student Searchable Picker Sheet --}}
@if(!$isStaffTab)
<div class="mob-picker-sheet" id="studentPickerSheet">
    <div class="mob-sheet-handle-bar"></div>
    <div class="mob-picker-sheet-header">
        <h3 class="mob-picker-sheet-title">
            <i class="fa fa-user text-info"></i> Select Student
        </h3>
        <button type="button" class="mob-picker-sheet-close">&times;</button>
    </div>
    <div class="mob-picker-sheet-search">
        <i class="fa fa-search"></i>
        <input type="text" class="mob-picker-search-input" id="searchStudentInput" placeholder="Search by student name, ID, father name...">
    </div>
    <div class="mob-picker-items-list" id="studentListItems">
        @foreach($students as $stu)
            @php
                $uid = $stu->attendance_unique_id ?? ('STU-' . $stu->id);
                $isSel = ($selectedUniqueId === $uid);
                $fullName = trim($stu->first_name . ' ' . $stu->last_name);
                $initial = strtoupper(substr($fullName ?: 'S', 0, 1));
                $searchKeywords = strtolower($fullName . ' ' . $uid . ' ' . ($stu->father_name ?? '') . ' ' . ($stu->admissionNo ?? ''));
            @endphp
            <div class="mob-picker-item student-item {{ $isSel ? 'selected' : '' }}" data-uid="{{ $uid }}" data-name="{{ $searchKeywords }}" data-display="{{ $fullName }}">
                <div class="mob-picker-item-left">
                    <div class="mob-picker-avatar">{{ $initial }}</div>
                    <div>
                        <span class="mob-picker-name">{{ $fullName }}</span>
                        <span class="mob-picker-sub">{{ $uid }}{{ !empty($stu->father_name) ? ' • S/O ' . $stu->father_name : '' }}</span>
                    </div>
                </div>
                <i class="fa fa-check mob-picker-check"></i>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- 3. Staff Searchable Picker Sheet --}}
@if($isStaffTab)
<div class="mob-picker-sheet" id="staffPickerSheet">
    <div class="mob-sheet-handle-bar"></div>
    <div class="mob-picker-sheet-header">
        <h3 class="mob-picker-sheet-title">
            <i class="fa fa-user-circle text-info"></i> Select Staff Member
        </h3>
        <button type="button" class="mob-picker-sheet-close">&times;</button>
    </div>
    <div class="mob-picker-sheet-search">
        <i class="fa fa-search"></i>
        <input type="text" class="mob-picker-search-input" id="searchStaffInput" placeholder="Search staff name, ID, role in real-time...">
    </div>
    <div class="mob-picker-items-list" id="staffListItems">
        @foreach($staff as $member)
            @php
                $uid = $member->attendance_unique_id ?? ('USR-' . $member->id);
                $isSel = ($selectedUniqueId === $uid);
                $fullName = trim($member->first_name . ' ' . $member->last_name);
                $initial = strtoupper(substr($fullName ?: 'U', 0, 1));
                $searchKeywords = strtolower($fullName . ' ' . $uid . ' ' . ($member->father_name ?? ''));
            @endphp
            <div class="mob-picker-item staff-item {{ $isSel ? 'selected' : '' }}" data-uid="{{ $uid }}" data-name="{{ $searchKeywords }}" data-display="{{ $fullName }}">
                <div class="mob-picker-item-left">
                    <div class="mob-picker-avatar">{{ $initial }}</div>
                    <div>
                        <span class="mob-picker-name">{{ $fullName }}</span>
                        <span class="mob-picker-sub">{{ $uid }}</span>
                    </div>
                </div>
                <i class="fa fa-check mob-picker-check"></i>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Toast Notification --}}
<div id="mobToast">
    <i class="fa fa-check-circle text-success"></i>
    <span id="mobToastMsg">Notification message</span>
</div>

<script>
$(document).ready(function() {
    function showToast(msg, isSuccess = true) {
        const $t = $('#mobToast');
        $('#mobToastMsg').text(msg);
        $t.find('i').removeClass('fa-check-circle text-success fa-exclamation-triangle text-danger')
            .addClass(isSuccess ? 'fa-check-circle text-success' : 'fa-exclamation-triangle text-danger');
        $t.addClass('show');
        setTimeout(() => $t.removeClass('show'), 2400);
    }

    function formatDateFormatted(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayName = dayNames[d.getDay()] || '';
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return parts[2] + ' ' + (months[parseInt(parts[1], 10)-1] || '') + ' ' + parts[0] + ' (' + dayName + ')';
    }

    function formatTime12(timeStr) {
        if (!timeStr) return '-';
        const parts = timeStr.split(':');
        if (parts.length < 2) return timeStr;
        let h = parseInt(parts[0], 10);
        const m = parts[1];
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        return h + ':' + m + ' ' + ampm;
    }

    // Calendar Day Cell Selection
    $('.mob-cal-cell:not(.empty)').on('click', function() {
        $('.mob-cal-cell').removeClass('active');
        $(this).addClass('active');

        const date = $(this).data('date');
        const rawStatus = $(this).data('status') || '';
        const inTime = $(this).data('in') || '';
        const outTime = $(this).data('out') || '';

        $('#mobInspectDateText').text(formatDateFormatted(date) || 'Selected Day');

        let statusText = 'Not Marked';
        let badgeClass = 'badge-none';

        if (rawStatus) {
            statusText = rawStatus.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
            badgeClass = 'badge-' + rawStatus;
        }

        $('#mobInspectStatusPill').text(statusText).removeClass().addClass('mob-inspect-status-pill ' + badgeClass);
        $('#mobInspectInTime').text(formatTime12(inTime));
        $('#mobInspectOutTime').text(formatTime12(outTime));
    });

    // Auto-select today's date if present in current month
    const $todayCell = $('.mob-cal-cell[data-date="{{ date("Y-m-d") }}"]');
    if ($todayCell.length) {
        $todayCell.trigger('click');
    } else {
        $('.mob-cal-cell:not(.empty)').first().trigger('click');
    }

    // =========================================================================
    // REAL-TIME SEARCHABLE BOTTOM SHEETS LOGIC
    // =========================================================================

    function openPickerSheet(sheetId, inputFocusId) {
        $('#mobPickerBackdrop').addClass('show');
        $(sheetId).addClass('show');
        if (inputFocusId) {
            setTimeout(() => {
                $(inputFocusId).focus().val('');
                $(inputFocusId).trigger('input');
            }, 100);
        }
    }

    function closePickerSheet() {
        $('#mobPickerBackdrop').removeClass('show');
        $('.mob-picker-sheet').removeClass('show');
    }

    $('#mobPickerBackdrop, .mob-picker-sheet-close').on('click', closePickerSheet);

    // 1. Open Class Picker
    $('#btnOpenClassPicker').on('click', function() {
        openPickerSheet('#classPickerSheet', '#searchClassInput');
    });

    // Class Real-Time Filter
    $('#searchClassInput').on('input', function() {
        const query = ($(this).val() || '').trim().toLowerCase();
        $('.class-item').each(function() {
            const name = $(this).data('name') || '';
            if (!query || name.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Class Item Selection
    $('.class-item').on('click', function() {
        const classId = $(this).data('id');
        const className = $(this).find('.mob-picker-name').text();
        $('#hidden_class_type_id').val(classId);
        $('#labelSelectedClass').text(className);
        closePickerSheet();
        showToast('Loading class ' + className + '...');
        document.getElementById('mobFilterForm').submit();
    });

    // 2. Open Student Picker
    $('#btnOpenStudentPicker').on('click', function() {
        openPickerSheet('#studentPickerSheet', '#searchStudentInput');
    });

    // Student Real-Time Filter
    $('#searchStudentInput').on('input', function() {
        const query = ($(this).val() || '').trim().toLowerCase();
        $('.student-item').each(function() {
            const searchData = $(this).data('name') || '';
            if (!query || searchData.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Student Item Selection
    $('.student-item').on('click', function() {
        const uid = $(this).data('uid');
        const displayName = $(this).data('display');
        $('#hidden_entity_id').val(uid);
        $('#labelSelectedStudent').text(displayName);
        closePickerSheet();
        showToast('Loading ' + displayName + '...');
        document.getElementById('mobFilterForm').submit();
    });

    // 3. Open Staff Picker
    $('#btnOpenStaffPicker').on('click', function() {
        openPickerSheet('#staffPickerSheet', '#searchStaffInput');
    });

    // Staff Real-Time Filter
    $('#searchStaffInput').on('input', function() {
        const query = ($(this).val() || '').trim().toLowerCase();
        $('.staff-item').each(function() {
            const searchData = $(this).data('name') || '';
            if (!query || searchData.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Staff Item Selection
    $('.staff-item').on('click', function() {
        const uid = $(this).data('uid');
        const displayName = $(this).data('display');
        $('#hidden_entity_id').val(uid);
        $('#labelSelectedStaff').text(displayName);
        closePickerSheet();
        showToast('Loading ' + displayName + '...');
        document.getElementById('mobFilterForm').submit();
    });

    // =========================================================================
    // INLINE QUICK SEARCH BAR (Click-to-open picker with prefilled search)
    // =========================================================================
    $('#mobInlineFilterInput').on('focus input', function() {
        const val = $(this).val();
        @if(!$isStaffTab)
            openPickerSheet('#studentPickerSheet', '#searchStudentInput');
            $('#searchStudentInput').val(val).trigger('input');
        @else
            openPickerSheet('#staffPickerSheet', '#searchStaffInput');
            $('#searchStaffInput').val(val).trigger('input');
        @endif
    });

    // =========================================================================
    // TIMELINE ACTIVITY LOG REAL-TIME FILTER CHIPS
    // =========================================================================
    $('.mob-timeline-filter-chip').on('click', function() {
        $('.mob-timeline-filter-chip').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        $('#mobTimelineContainer .mob-timeline-item').each(function() {
            const status = $(this).data('status') || '';
            if (filter === 'all') {
                $(this).show();
            } else if (filter === 'present' && (status === 'present' || status === 'in' || status === 'out')) {
                $(this).show();
            } else if (filter === status) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endsection
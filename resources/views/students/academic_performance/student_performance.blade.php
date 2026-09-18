@extends('layout.app')

@php
    $classType = Helper::classType();
    $studentCount = $students instanceof \Illuminate\Support\Collection ? $students->count() : count($students ?? []);
    $selectedClass = collect($classType)->firstWhere('id', (int) ($search['class_type_id'] ?? 0));
    $gradeArray = $gradeArray ?? [
        'grade_1' => 0,
        'grade_2' => 0,
        'grade_3' => 0,
        'grade_4' => 0,
        'grade_5' => 0,
    ];
    $topPerformers = ($gradeArray['grade_1'] ?? 0) + ($gradeArray['grade_2'] ?? 0);
    $needsImprovement = ($gradeArray['grade_4'] ?? 0) + ($gradeArray['grade_5'] ?? 0);
    
    // Calculate Grade Distribution Percentages
    $g1Pct = $studentCount > 0 ? round((($gradeArray['grade_1'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g2Pct = $studentCount > 0 ? round((($gradeArray['grade_2'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g3Pct = $studentCount > 0 ? round((($gradeArray['grade_3'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g4Pct = $studentCount > 0 ? round((($gradeArray['grade_4'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g5Pct = $studentCount > 0 ? round((($gradeArray['grade_5'] ?? 0) / $studentCount) * 100, 1) : 0;
@endphp

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - ADVANCED STUDENT ACADEMIC PERFORMANCE THEME
   - Signature Dark Navy Theme (#002C54 -> #0f3460)
   - Dual-Thead Sticky Header with In-Column Excel Filters (.excel-filter-row)
   - Interactive Visual Grade Distribution Ribbon & KPI Summary
   - Interactive Quick Student Snapshot Modal / Drawer
   - Dynamic Column Sorting (Asc/Desc) & Fast Client-Side Pagination
   - Batch Selection Toolbar (Export/Print Selected)
   - Topper Badges (🥇 Gold, 🥈 Silver, 🥉 Bronze)
   - Print Optimized Layout (@media print)
   - Viewport Auto-Adjust (calc(100vh - var(--header-height) - 16px))
   ========================================================================== */

:root {
    --header-height: 56px;
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
}

.perf-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.perf-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjust Container */
.perf-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 8px 0;
}

/* 1. Hero Header Banner */
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
}

/* Quick Summary Stat Badges */
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
    font-size: 11.5px;
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
.hero-stat-chip.badge-purple {
    background: rgba(168, 85, 247, 0.22);
    border-color: rgba(168, 85, 247, 0.45);
    color: #f3e8ff;
}
.hero-stat-chip.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
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
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.3);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* 2. KPI Metrics Strip */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
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
}
.kpi-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #64748b;
}
.kpi-value {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}
.kpi-badge {
    width: 24px;
    height: 24px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
}
.kpi-badge.g1 { background: #dcfce7; color: #15803d; }
.kpi-badge.g2 { background: #dbeafe; color: #1d4ed8; }
.kpi-badge.g3 { background: #cffafe; color: #0e7490; }
.kpi-badge.g4 { background: #fef3c7; color: #b45309; }
.kpi-badge.g5 { background: #ffe4e6; color: #be123c; }
.kpi-badge.att { background: #f0fdf4; color: #16a34a; }

/* Interactive Grade Distribution Ribbon */
.grade-distribution-ribbon {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 8px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    font-size: 10px;
}
.ribbon-label {
    font-weight: 700;
    color: #002C54;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 4px;
}
.ribbon-bar {
    flex: 1;
    height: 12px;
    display: flex;
    border-radius: 2px;
    overflow: hidden;
    background: #e2e8f0;
}
.ribbon-segment {
    height: 100%;
    transition: width .3s ease, opacity .15s ease;
    cursor: pointer;
    position: relative;
}
.ribbon-segment:hover {
    opacity: .85;
    filter: brightness(1.08);
}
.ribbon-segment.seg-g1 { background: #16a34a; }
.ribbon-segment.seg-g2 { background: #2563eb; }
.ribbon-segment.seg-g3 { background: #0891b2; }
.ribbon-segment.seg-g4 { background: #d97706; }
.ribbon-segment.seg-g5 { background: #dc2626; }
.ribbon-legend {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    font-size: 9.5px;
    color: #475569;
}
.ribbon-legend-item {
    display: flex;
    align-items: center;
    gap: 3px;
    cursor: pointer;
}
.ribbon-legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}

/* 3. Filter Card */
.filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
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
    align-items: flex-end;
    gap: 8px;
}
.filter-group {
    display: flex;
    flex-direction: column;
}
.filter-group-class { width: 220px; }
.filter-group-adm { width: 140px; }
.filter-group-keyword { flex: 1; min-width: 180px; }

.filter-label {
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
}
.filter-input, .filter-select {
    height: 27px;
    width: 100%;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
}
.filter-input:focus, .filter-select:focus {
    border-color: #002C54;
}
.filter-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-filter-search {
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    height: 27px;
    padding: 0 12px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-filter-search:hover {
    background: #08335c;
}
.btn-filter-reset {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 27px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.btn-filter-reset:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}

/* 4. Table Card Container */
.dash-table-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
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

/* Status Filter Tabs (All / Outstanding / Average / Needs Help / Low Att) */
.perf-status-tabs {
    display: flex;
    align-items: center;
    background: #001f3d;
    border-bottom: 1px solid rgba(255,255,255,.1);
    padding: 2px 8px;
    gap: 4px;
    flex-shrink: 0;
    overflow-x: auto;
}
.status-tab-btn {
    background: transparent;
    border: 1px solid transparent;
    color: #94a3b8;
    font-size: 10.5px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .12s ease;
    white-space: nowrap;
}
.status-tab-btn:hover {
    color: #ffffff;
    background: rgba(255,255,255,.06);
}
.status-tab-btn.is-active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.tab-pill-count {
    font-size: 9px;
    padding: 0 4px;
    border-radius: 2px;
    background: rgba(255,255,255,.2);
    color: #ffffff;
}

/* Selection / Batch Actions Ribbon */
.batch-actions-bar {
    display: none;
    background: #eff6ff;
    border-bottom: 1px solid #bfdbfe;
    padding: 3px 10px;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #1e3a8a;
    font-weight: 600;
    flex-shrink: 0;
}
.batch-actions-bar.is-visible {
    display: flex;
}
.batch-btn-group {
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-batch {
    background: #ffffff;
    border: 1px solid #93c5fd;
    color: #1e40af;
    border-radius: 2px;
    padding: 2px 7px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-batch:hover {
    background: #1e40af;
    color: #ffffff;
    border-color: #1e40af;
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
    position: relative;
}

/* Standard Arise Data Table Layout */
.data-table-arise {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    white-space: nowrap;
}

/* Sticky Headers */
.data-table-arise thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-titles-row th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 11px;
    padding: 4px 8px;
    border: 1px solid #08335c;
    text-align: left;
    height: 32px;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
}
.header-titles-row th:hover {
    background: #08335c;
}
.sort-indicator {
    font-size: 9px;
    opacity: .6;
    margin-left: 3px;
}
.header-titles-row th.is-sorted .sort-indicator {
    opacity: 1;
    color: #38bdf8;
    font-weight: 900;
}

.excel-filter-row th {
    background: #08335c;
    padding: 2px 4px;
    border: 1px solid #001f3d;
    vertical-align: middle;
}
.excel-col-filter {
    width: 100%;
    height: 22px;
    font-size: 10.5px;
    padding: 0 4px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    outline: none;
    box-sizing: border-box;
}
.excel-col-filter::placeholder {
    color: #cbd5e1;
    font-size: 10px;
}
.excel-col-filter:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
}
.btn-clear-excel-filters {
    background: #ef4444;
    color: #ffffff;
    border: none;
    border-radius: 2px;
    width: 100%;
    height: 22px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    transition: background .12s;
}
.btn-clear-excel-filters:hover {
    background: #dc2626;
}

/* Rows Styling */
.data-table-arise tbody tr {
    background: #ffffff;
    transition: background-color 0.12s ease;
}
.data-table-arise tbody tr:nth-child(even) {
    background: #f8fafc;
}
.data-table-arise tbody tr:hover {
    background: #edf2f7 !important;
}
.data-table-arise tbody td {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11px;
}

/* Student Profile Cell */
.student-profile-cell {
    display: flex;
    align-items: center;
    gap: 6px;
}
.student-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    font-weight: 700;
    flex-shrink: 0;
}
.student-avatar.topper-1 { background: linear-gradient(135deg, #eab308, #ca8a04); color: #000; box-shadow: 0 0 4px rgba(234,179,8,0.5); }
.student-avatar.topper-2 { background: linear-gradient(135deg, #94a3b8, #64748b); }
.student-avatar.topper-3 { background: linear-gradient(135deg, #d97706, #b45309); }

.student-name-link {
    font-weight: 700;
    color: #002C54 !important;
    text-decoration: none !important;
    line-height: 1.2;
}
.student-name-link:hover {
    color: #0284c7 !important;
    text-decoration: underline !important;
}
.topper-badge {
    font-size: 11px;
    margin-right: 2px;
}

/* Grade Pills */
.grade-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1;
}
.grade-badge-1 { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
.grade-badge-2 { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
.grade-badge-3 { background: #cffafe; color: #0e7490; border: 1px solid #67e8f9; }
.grade-badge-4 { background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }
.grade-badge-5 { background: #ffe4e6; color: #be123c; border: 1px solid #fda4af; }

/* Progress Mini Bar */
.progress-mini-bar {
    width: 65px;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    display: inline-block;
    vertical-align: middle;
    margin-right: 4px;
}
.progress-mini-fill {
    height: 100%;
    border-radius: 3px;
}
.progress-mini-fill.high { background: #16a34a; }
.progress-mini-fill.medium { background: #d97706; }
.progress-mini-fill.low { background: #dc2626; }

/* Action Buttons */
.action-btn-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-snapshot {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: all .12s;
}
.btn-snapshot:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.btn-view-details {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    background: #f1f5f9;
    color: #002C54 !important;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .12s ease;
    height: 22px;
}
.btn-view-details:hover {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}

/* 5. Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 34px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    border-top: 1px solid #08335c;
    flex-shrink: 0;
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
    gap: 4px;
    color: #cbd5e1;
    font-size: 10.5px;
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

/* 6. Centered Light Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    flex: 1;
    min-height: calc(100vh - var(--header-height, 56px) - 230px);
    padding: 40px 16px;
    text-align: center;
    background: #ffffff;
    color: #475569;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
    line-height: 1;
    color: #0284c7;
}
.dash-empty-state .empty-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #0f172a;
}
.dash-empty-state .empty-desc {
    font-size: 12.5px;
    color: #64748b;
    max-width: 480px;
    line-height: 1.5;
}

/* 7. Quick Snapshot Modal */
.snapshot-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(2px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 12px;
}
.snapshot-modal-card {
    background: #ffffff;
    border-radius: 4px;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: modalFadeIn .15s ease-out;
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(.96); }
    to { opacity: 1; transform: scale(1); }
}
.snapshot-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.snapshot-body {
    padding: 14px;
}
.snapshot-student-hero {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 12px;
}
.snapshot-avatar-lg {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
}
.snapshot-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.snapshot-stat-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 8px 10px;
}
.snapshot-stat-box .box-lbl {
    font-size: 10px;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 700;
}
.snapshot-stat-box .box-val {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 2px;
}
.snapshot-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Print Styles */
@media print {
    body * { visibility: hidden; }
    #studentPerformanceTable, #studentPerformanceTable * { visibility: visible; }
    #studentPerformanceTable { position: absolute; left: 0; top: 0; width: 100%; }
    .excel-filter-row, .perf-status-tabs, .filter-card, .perf-hero, .table-pagination-bar, .action-btn-group, .batch-actions-bar { display: none !important; }
}

@media(max-width: 991px) {
    .perf-page-layout {
        height: auto;
        overflow: visible;
        padding: 4px;
    }
    .kpi-strip {
        grid-template-columns: repeat(3, 1fr);
    }
    .table-scroll-container {
        max-height: 480px;
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
@media(max-width: 576px) {
    .kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<div class="content-wrapper perf-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="perf-page-layout">

                {{-- 1. Signature Navy Hero Banner --}}
                <div class="perf-hero">
                    <div class="perf-hero-text">
                        <span class="perf-kicker">Academic Evaluation &bull; Performance Analytics</span>
                        <h1 class="perf-title">
                            <i class="fa fa-line-chart text-warning"></i> Student Performance &amp; Academic Analytics
                        </h1>
                        <p class="perf-subtitle">
                            Evaluate grades, mark distribution, and attendance records with real-time column filters and quick student snapshots.
                        </p>
                    </div>

                    <div class="perf-hero-stats">
                        <div class="hero-stat-chip badge-sky">
                            <i class="fa fa-th-large"></i> Class: <b>{{ $selectedClass->name ?? 'All Classes' }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald">
                            <i class="fa fa-users"></i> Students: <b id="statTotalStudents">{{ $studentCount }}</b>
                        </div>
                        <div class="hero-stat-chip badge-purple">
                            <i class="fa fa-star"></i> Top Tier (G1/G2): <b>{{ $topPerformers }}</b>
                        </div>
                        <div class="hero-stat-chip badge-rose">
                            <i class="fa fa-exclamation-triangle"></i> Needs Help (G4/G5): <b>{{ $needsImprovement }}</b>
                        </div>
                    </div>

                    <div class="perf-hero-actions">
                        @if($studentCount > 0)
                            <button type="button" class="dash-btn dash-btn-light" onclick="exportPerformanceCSV()" title="Export Filtered Student Performance">
                                <i class="fa fa-file-excel-o text-success"></i> Export CSV
                            </button>
                        @endif
                    </div>
                </div>

                {{-- 2. KPI Metrics Strip --}}
                <div class="kpi-strip">
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Total Students</span>
                            <span class="kpi-value">{{ $studentCount }}</span>
                        </div>
                        <div class="kpi-badge g2"><i class="fa fa-users"></i></div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Avg Attendance</span>
                            <span class="kpi-value">{{ $averageAttendance !== null ? $averageAttendance . '%' : 'N/A' }}</span>
                        </div>
                        <div class="kpi-badge att"><i class="fa fa-calendar-check-o"></i></div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Grade 1 (91-100%)</span>
                            <span class="kpi-value text-success">{{ $gradeArray['grade_1'] ?? 0 }}</span>
                        </div>
                        <div class="kpi-badge g1">G1</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Grade 2 (81-90%)</span>
                            <span class="kpi-value text-primary">{{ $gradeArray['grade_2'] ?? 0 }}</span>
                        </div>
                        <div class="kpi-badge g2">G2</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Grade 3 (71-80%)</span>
                            <span class="kpi-value text-info">{{ $gradeArray['grade_3'] ?? 0 }}</span>
                        </div>
                        <div class="kpi-badge g3">G3</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-info">
                            <span class="kpi-label">Grade 4 &amp; 5</span>
                            <span class="kpi-value text-danger">{{ $needsImprovement }}</span>
                        </div>
                        <div class="kpi-badge g5">G4-5</div>
                    </div>
                </div>

                {{-- 2.1 Interactive Grade Distribution Ribbon (Visual Analytics) --}}
                @if($studentCount > 0)
                    <div class="grade-distribution-ribbon">
                        <span class="ribbon-label"><i class="fa fa-pie-chart text-primary"></i> Grade Split:</span>
                        <div class="ribbon-bar">
                            @if($g1Pct > 0)<div class="ribbon-segment seg-g1" style="width: {{ $g1Pct }}%;" onclick="filterByRibbonGrade('grade_1')" title="Grade 1: {{ $gradeArray['grade_1'] }} ({{ $g1Pct }}%)"></div>@endif
                            @if($g2Pct > 0)<div class="ribbon-segment seg-g2" style="width: {{ $g2Pct }}%;" onclick="filterByRibbonGrade('grade_2')" title="Grade 2: {{ $gradeArray['grade_2'] }} ({{ $g2Pct }}%)"></div>@endif
                            @if($g3Pct > 0)<div class="ribbon-segment seg-g3" style="width: {{ $g3Pct }}%;" onclick="filterByRibbonGrade('grade_3')" title="Grade 3: {{ $gradeArray['grade_3'] }} ({{ $g3Pct }}%)"></div>@endif
                            @if($g4Pct > 0)<div class="ribbon-segment seg-g4" style="width: {{ $g4Pct }}%;" onclick="filterByRibbonGrade('grade_4')" title="Grade 4: {{ $gradeArray['grade_4'] }} ({{ $g4Pct }}%)"></div>@endif
                            @if($g5Pct > 0)<div class="ribbon-segment seg-g5" style="width: {{ $g5Pct }}%;" onclick="filterByRibbonGrade('grade_5')" title="Grade 5: {{ $gradeArray['grade_5'] }} ({{ $g5Pct }}%)"></div>@endif
                        </div>
                        <div class="ribbon-legend">
                            <span class="ribbon-legend-item" onclick="filterByRibbonGrade('grade_1')"><span class="ribbon-legend-dot" style="background: #16a34a;"></span> G1: <b>{{ $g1Pct }}%</b></span>
                            <span class="ribbon-legend-item" onclick="filterByRibbonGrade('grade_2')"><span class="ribbon-legend-dot" style="background: #2563eb;"></span> G2: <b>{{ $g2Pct }}%</b></span>
                            <span class="ribbon-legend-item" onclick="filterByRibbonGrade('grade_3')"><span class="ribbon-legend-dot" style="background: #0891b2;"></span> G3: <b>{{ $g3Pct }}%</b></span>
                            <span class="ribbon-legend-item" onclick="filterByRibbonGrade('grade_4')"><span class="ribbon-legend-dot" style="background: #d97706;"></span> G4: <b>{{ $g4Pct }}%</b></span>
                            <span class="ribbon-legend-item" onclick="filterByRibbonGrade('grade_5')"><span class="ribbon-legend-dot" style="background: #dc2626;"></span> G5: <b>{{ $g5Pct }}%</b></span>
                        </div>
                    </div>
                @endif

                {{-- 3. Filter Card --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span><i class="fa fa-filter mr-1"></i> Search &amp; Class Filter</span>
                        <span style="font-size: 10px; font-weight: normal; opacity: .85;">Filter performance matrix by class, admission no or keywords</span>
                    </div>
                    <div class="filter-card-body">
                        <form action="{{ url('student_performance') }}" method="POST" id="perfFilterForm" class="filter-form-row">
                            @csrf
                            
                            {{-- Class Selection --}}
                            <div class="filter-group filter-group-class">
                                <label class="filter-label">Class: <span class="text-danger">*</span></label>
                                <select name="class_type_id" id="filter_class_type_id" class="filter-select select2" onchange="document.getElementById('perfFilterForm').submit()">
                                    <option value="">-- All Classes --</option>
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}" {{ (int)($search['class_type_id'] ?? 0) === (int)$type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Admission No --}}
                            <div class="filter-group filter-group-adm">
                                <label class="filter-label">Admission No:</label>
                                <input type="text" name="admissionNo" class="filter-input" placeholder="e.g. 1024" value="{{ $search['admissionNo'] ?? '' }}">
                            </div>

                            {{-- Keyword Search --}}
                            <div class="filter-group filter-group-keyword">
                                <label class="filter-label">Search Keywords:</label>
                                <input type="text" name="name" class="filter-input" placeholder="Name, Mobile, Father Name, SRN etc." value="{{ $search['name'] ?? '' }}">
                            </div>

                            {{-- Actions --}}
                            <div class="filter-actions">
                                <button type="submit" class="btn-filter-search">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                @if(!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']))
                                    <a href="{{ url('student_performance') }}" class="btn-filter-reset" title="Reset Filters">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 4. Main Matrix Table Section --}}
                @if($studentCount > 0)
                    <div class="dash-table-card">
                        
                        {{-- Card Header Bar --}}
                        <div class="dash-card-header">
                            <div class="dash-card-title">
                                <i class="fa fa-table"></i> Student Performance List
                                <span class="badge-total-records">Total: {{ $studentCount }} Students</span>
                            </div>

                            <div class="table-header-tools">
                                <div class="table-search-wrap">
                                    <i class="fa fa-search"></i>
                                    <input type="text" id="quickTableSearch" class="table-search-input" placeholder="Quick search (Press /)...">
                                </div>
                            </div>
                        </div>

                        {{-- Status Quick Filter Tabs --}}
                        <div class="perf-status-tabs">
                            <button type="button" class="status-tab-btn is-active" data-status-filter="all">
                                All Students <span class="tab-pill-count" id="tabCountAll">{{ $studentCount }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="top">
                                🌟 Top Tier (G1 &amp; G2) <span class="tab-pill-count">{{ $topPerformers }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="grade_1">
                                🥇 Grade 1 <span class="tab-pill-count">{{ $gradeArray['grade_1'] ?? 0 }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="grade_2">
                                🥈 Grade 2 <span class="tab-pill-count">{{ $gradeArray['grade_2'] ?? 0 }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="grade_3">
                                🔷 Grade 3 <span class="tab-pill-count">{{ $gradeArray['grade_3'] ?? 0 }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="needs_help">
                                ⚠️ Needs Help (G4 &amp; G5) <span class="tab-pill-count">{{ $needsImprovement }}</span>
                            </button>
                            <button type="button" class="status-tab-btn" data-status-filter="low_att">
                                📉 Low Att. (&lt;75%) <span class="tab-pill-count" id="tabCountLowAtt">0</span>
                            </button>
                        </div>

                        {{-- Multi-Selection Batch Actions Toolbar --}}
                        <div class="batch-actions-bar" id="batchActionsBar">
                            <div>
                                <i class="fa fa-check-square mr-1"></i> <span id="batchSelectedCount">0</span> Students Selected
                            </div>
                            <div class="batch-btn-group">
                                <button type="button" class="btn-batch" onclick="exportSelectedPerformanceCSV()">
                                    <i class="fa fa-download"></i> Export Selected
                                </button>
                                <button type="button" class="btn-batch" onclick="clearBatchSelection()">
                                    <i class="fa fa-times"></i> Deselect All
                                </button>
                            </div>
                        </div>

                        {{-- Viewport Scrollable Table --}}
                        <div class="table-scroll-container">
                            <table class="data-table-arise" id="studentPerformanceTable">
                                <thead>
                                    {{-- Row 1: Titles with Sortability --}}
                                    <tr class="header-titles-row">
                                        <th style="width: 32px; text-align: center;">
                                            <input type="checkbox" id="selectAllCheckbox" title="Select All Visible">
                                        </th>
                                        <th style="width: 40px; text-align: center;" data-sort="index">
                                            # <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 100px; text-align: center;" data-sort="adm">
                                            Adm. No <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="min-width: 200px;" data-sort="name">
                                            Student Name &amp; Profile <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 130px;" data-sort="class">
                                            Class <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 120px; text-align: center;" data-sort="grade-num">
                                            Grade Category <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 130px; text-align: center;" data-sort="marks">
                                            Average Marks <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 140px; text-align: center;" data-sort="att">
                                            Attendance % <span class="sort-indicator"><i class="fa fa-sort"></i></span>
                                        </th>
                                        <th style="width: 120px; text-align: center;">Actions</th>
                                    </tr>
                                    {{-- Row 2: In-Column Excel Filters --}}
                                    <tr class="excel-filter-row">
                                        <th style="text-align: center;">
                                            <button type="button" class="btn-clear-excel-filters" id="btnClearExcelFilters" title="Clear all column filters">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </th>
                                        <th></th>
                                        <th><input type="text" class="excel-col-filter" data-col="1" placeholder="Filter Adm..."></th>
                                        <th><input type="text" class="excel-col-filter" data-col="2" placeholder="Filter Name..."></th>
                                        <th><input type="text" class="excel-col-filter" data-col="3" placeholder="Filter Class..."></th>
                                        <th><input type="text" class="excel-col-filter" data-col="4" placeholder="Filter Grade..."></th>
                                        <th><input type="text" class="excel-col-filter" data-col="5" placeholder="Filter Marks..."></th>
                                        <th><input type="text" class="excel-col-filter" data-col="6" placeholder="Filter Att..."></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="performanceTableBody">
                                    @php
                                        $lowAttCount = 0;
                                        // Sort students by mark descending temporarily to determine Top 3 Toppers
                                        $scoredStudents = collect($students)->map(function($st) use ($studentMetrics) {
                                            $st->temp_score = (float)($studentMetrics[$st->id]['grade_percentage'] ?? 0);
                                            return $st;
                                        })->sortByDesc('temp_score')->values();
                                        
                                        $topperMap = [];
                                        if($scoredStudents->count() > 0 && $scoredStudents[0]->temp_score > 0) {
                                            $topperMap[$scoredStudents[0]->id] = 1;
                                        }
                                        if($scoredStudents->count() > 1 && $scoredStudents[1]->temp_score > 0) {
                                            $topperMap[$scoredStudents[1]->id] = 2;
                                        }
                                        if($scoredStudents->count() > 2 && $scoredStudents[2]->temp_score > 0) {
                                            $topperMap[$scoredStudents[2]->id] = 3;
                                        }
                                    @endphp
                                    @foreach($students as $index => $student)
                                        @php
                                            $metrics = $studentMetrics[$student->id] ?? [];
                                            $gradeLabel = $metrics['grade_label'] ?? 'Grade 5';
                                            $gradePercentage = (float)($metrics['grade_percentage'] ?? 0);
                                            $attStr = $metrics['attendance'] ?? 'N/A';
                                            $attVal = floatval(str_replace('%', '', $attStr));
                                            
                                            if ($attStr !== 'N/A' && $attVal < 75) {
                                                $lowAttCount++;
                                            }

                                            $gradeNum = 5;
                                            if (strpos($gradeLabel, '1') !== false) $gradeNum = 1;
                                            elseif (strpos($gradeLabel, '2') !== false) $gradeNum = 2;
                                            elseif (strpos($gradeLabel, '3') !== false) $gradeNum = 3;
                                            elseif (strpos($gradeLabel, '4') !== false) $gradeNum = 4;

                                            $stName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                                            $initial = strtoupper(substr($student->first_name ?? 'S', 0, 1));
                                            $topperRank = $topperMap[$student->id] ?? 0;
                                        @endphp
                                        <tr class="student-row"
                                            id="row-student-{{ $student->id }}"
                                            data-id="{{ $student->id }}"
                                            data-index="{{ $index + 1 }}"
                                            data-adm="{{ strtolower($student->admissionNo ?? '') }}"
                                            data-name="{{ strtolower($stName) }}"
                                            data-display-name="{{ $stName }}"
                                            data-father="{{ $student->father_name ?? '' }}"
                                            data-mobile="{{ $student->mobile ?? $student->father_mobile ?? '' }}"
                                            data-class="{{ strtolower($student->class_name ?? '') }}"
                                            data-class-name="{{ $student->class_name ?? '-' }}"
                                            data-grade="{{ strtolower($gradeLabel) }}"
                                            data-grade-label="{{ $gradeLabel }}"
                                            data-grade-num="{{ $gradeNum }}"
                                            data-marks="{{ $gradePercentage }}"
                                            data-att="{{ $attVal }}"
                                            data-att-str="{{ $attStr }}"
                                            data-topper="{{ $topperRank }}"
                                            data-low-att="{{ ($attStr !== 'N/A' && $attVal < 75) ? '1' : '0' }}">
                                            
                                            {{-- Checkbox --}}
                                            <td style="text-align: center;">
                                                <input type="checkbox" class="student-row-checkbox" value="{{ $student->id }}">
                                            </td>

                                            {{-- 1. Index --}}
                                            <td style="text-align: center; color: #64748b; font-weight: 600;">
                                                {{ $index + 1 }}
                                            </td>

                                            {{-- 2. Adm No --}}
                                            <td style="text-align: center; font-weight: 700;">
                                                <code>{{ $student->admissionNo ?: 'N/A' }}</code>
                                            </td>

                                            {{-- 3. Student Name --}}
                                            <td>
                                                <div class="student-profile-cell">
                                                    <span class="student-avatar {{ $topperRank == 1 ? 'topper-1' : ($topperRank == 2 ? 'topper-2' : ($topperRank == 3 ? 'topper-3' : '')) }}">
                                                        {{ $initial }}
                                                    </span>
                                                    <div>
                                                        <div>
                                                            @if($topperRank == 1) <span class="topper-badge" title="Rank 1 Topper">🥇</span> @endif
                                                            @if($topperRank == 2) <span class="topper-badge" title="Rank 2 Topper">🥈</span> @endif
                                                            @if($topperRank == 3) <span class="topper-badge" title="Rank 3 Topper">🥉</span> @endif
                                                            <a target="_blank" href="{{ url('studentParticularPerformance') }}/{{ $student->id }}" class="student-name-link" title="Open Detailed Performance Profile">
                                                                {{ $stName ?: '-' }}
                                                            </a>
                                                        </div>
                                                        @if(!empty($student->father_name))
                                                            <div style="font-size: 9.5px; color: #64748b; line-height: 1;">
                                                                S/D of {{ $student->father_name }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- 4. Class --}}
                                            <td>
                                                <span class="badge badge-light border font-weight-bold" style="color: #002C54;">
                                                    {{ $student->class_name ?: '-' }}
                                                </span>
                                            </td>

                                            {{-- 5. Grade Category --}}
                                            <td style="text-align: center;">
                                                <span class="grade-badge grade-badge-{{ $gradeNum }}">
                                                    @if($gradeNum <= 2) <i class="fa fa-star"></i> @endif
                                                    {{ $gradeLabel }}
                                                </span>
                                            </td>

                                            {{-- 6. Average Marks --}}
                                            <td style="text-align: center;">
                                                <div style="font-weight: 700; color: #0f172a; font-size: 11.5px;">
                                                    {{ number_format($gradePercentage, 2) }}%
                                                </div>
                                                <div class="progress-mini-bar" title="{{ number_format($gradePercentage, 2) }}%">
                                                    <div class="progress-mini-fill {{ $gradePercentage >= 80 ? 'high' : ($gradePercentage >= 60 ? 'medium' : 'low') }}" style="width: {{ min(100, max(5, $gradePercentage)) }}%;"></div>
                                                </div>
                                            </td>

                                            {{-- 7. Attendance % --}}
                                            <td style="text-align: center;">
                                                @if($attStr !== 'N/A')
                                                    <div style="font-weight: 700; color: {{ $attVal >= 75 ? '#15803d' : '#be123c' }}; font-size: 11px;">
                                                        {{ $attStr }}
                                                    </div>
                                                    <div class="progress-mini-bar" title="Attendance: {{ $attStr }}">
                                                        <div class="progress-mini-fill {{ $attVal >= 75 ? 'high' : ($attVal >= 60 ? 'medium' : 'low') }}" style="width: {{ min(100, max(5, $attVal)) }}%;"></div>
                                                    </div>
                                                @else
                                                    <span class="text-muted" style="font-size: 10px;">N/A</span>
                                                @endif
                                            </td>

                                            {{-- 8. Actions (Quick Snapshot & Full Profile) --}}
                                            <td style="text-align: center;">
                                                <div class="action-btn-group">
                                                    <button type="button" class="btn-snapshot" onclick="openStudentSnapshot({{ $student->id }})" title="Quick Snapshot Modal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    <a target="_blank" href="{{ url('studentParticularPerformance') }}/{{ $student->id }}" class="btn-view-details" title="Open Detailed Student Analytics">
                                                        <i class="fa fa-external-link"></i> Analytics
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pinned Bottom Pagination Toolbar --}}
                        <div class="table-pagination-bar">
                            <div class="pagination-info">
                                Showing <span id="page-start" class="font-weight-bold text-white">1</span> to <span id="page-end" class="font-weight-bold text-white">{{ min(25, $studentCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $studentCount }}</span> students
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
                @elseif(!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']))
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon text-muted"><i class="fa fa-users"></i></div>
                            <div class="empty-title">No Students Found</div>
                            <div class="empty-desc">There are no students matching your specified search criteria. Try modifying your filter settings.</div>
                        </div>
                    </div>
                @else
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon text-primary"><i class="fa fa-line-chart"></i></div>
                            <div class="empty-title">Select Class or Search Criteria</div>
                            <div class="empty-desc">Choose a class from the dropdown above or enter an admission number to view detailed student academic performance matrix.</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
</div>

{{-- 7. Quick Student Snapshot Modal --}}
<div class="snapshot-modal-overlay" id="snapshotModalOverlay" onclick="closeStudentSnapshot(event)">
    <div class="snapshot-modal-card" onclick="event.stopPropagation()">
        <div class="snapshot-header">
            <span style="font-weight: 700; font-size: 13px;">
                <i class="fa fa-id-card-o mr-1"></i> Student Academic Snapshot
            </span>
            <button type="button" style="background:none; border:none; color:#fff; font-size:16px; cursor:pointer;" onclick="closeStudentSnapshotDirect()">&times;</button>
        </div>
        <div class="snapshot-body">
            <div class="snapshot-student-hero">
                <div class="snapshot-avatar-lg" id="snapAvatar">S</div>
                <div>
                    <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #002C54;" id="snapName">Student Name</h4>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;" id="snapSub">Class 10th &bull; Adm: ADM001</div>
                    <div style="font-size: 10.5px; color: #475569; margin-top: 1px;" id="snapFather">Father: -</div>
                </div>
            </div>

            <div class="snapshot-grid">
                <div class="snapshot-stat-box">
                    <div class="box-lbl">Academic Grade</div>
                    <div class="box-val text-primary" id="snapGrade">Grade 1</div>
                </div>
                <div class="snapshot-stat-box">
                    <div class="box-lbl">Average Score</div>
                    <div class="box-val text-success" id="snapScore">95.00%</div>
                </div>
                <div class="snapshot-stat-box">
                    <div class="box-lbl">Attendance Rate</div>
                    <div class="box-val" id="snapAtt">88.50%</div>
                </div>
                <div class="snapshot-stat-box">
                    <div class="box-lbl">Class Standing</div>
                    <div class="box-val text-warning" id="snapTopper">Rank -</div>
                </div>
            </div>

            <div id="snapContactWrap" style="background:#f1f5f9; padding:6px 10px; border-radius:2px; font-size:11px; color:#334155; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa fa-phone text-primary mr-1"></i> Parent Contact: <b id="snapMobile">-</b></span>
                <a href="#" id="snapWhatsappBtn" target="_blank" class="btn btn-xs btn-success" style="font-size: 10px; font-weight:700;">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
        <div class="snapshot-footer">
            <button type="button" class="btn btn-xs btn-secondary" onclick="closeStudentSnapshotDirect()">Close</button>
            <a href="#" target="_blank" id="snapFullProfileBtn" class="btn btn-xs btn-primary font-weight-bold">
                <i class="fa fa-external-link mr-1"></i> Open Full Report Card
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Update low attendance counter badge
    var lowAttEl = $('#tabCountLowAtt');
    if (lowAttEl.length) {
        var lowCount = $('.student-row[data-low-att="1"]').length;
        lowAttEl.text(lowCount);
    }

    var allRows = $('.student-row');
    var totalRows = allRows.length;
    var currentPage = 1;
    var rowsPerPage = 25;
    var currentStatusFilter = 'all';
    var currentSortCol = 'index';
    var sortDirection = 'asc';

    // Shortcut: Press '/' to focus search
    $(document).on('keydown', function(e) {
        if (e.key === '/' && !$(e.target).is('input, select, textarea')) {
            e.preventDefault();
            $('#quickTableSearch').focus();
        } else if (e.key === 'Escape') {
            closeStudentSnapshotDirect();
        }
    });

    // 1. In-Table Quick Search & Excel Column Filters
    function applyFilters() {
        var globalQuery = ($('#quickTableSearch').val() || '').toLowerCase().trim();
        var colFilters = {};

        $('.excel-col-filter').each(function() {
            var colIndex = $(this).data('col');
            var val = ($(this).val() || '').toLowerCase().trim();
            if (val.length > 0) {
                colFilters[colIndex] = val;
            }
        });

        allRows.each(function() {
            var row = $(this);
            var matches = true;

            // Status filter
            if (currentStatusFilter === 'top') {
                var gNum = parseInt(row.data('grade-num'), 10);
                if (gNum > 2) matches = false;
            } else if (currentStatusFilter === 'grade_1') {
                var gNum = parseInt(row.data('grade-num'), 10);
                if (gNum !== 1) matches = false;
            } else if (currentStatusFilter === 'grade_2') {
                var gNum = parseInt(row.data('grade-num'), 10);
                if (gNum !== 2) matches = false;
            } else if (currentStatusFilter === 'grade_3') {
                var gNum = parseInt(row.data('grade-num'), 10);
                if (gNum !== 3) matches = false;
            } else if (currentStatusFilter === 'needs_help') {
                var gNum = parseInt(row.data('grade-num'), 10);
                if (gNum < 4) matches = false;
            } else if (currentStatusFilter === 'low_att') {
                if (row.data('low-att') !== 1 && row.data('low-att') !== '1') matches = false;
            }

            // Global search
            if (matches && globalQuery.length > 0) {
                var rowText = row.text().toLowerCase();
                if (rowText.indexOf(globalQuery) === -1) {
                    matches = false;
                }
            }

            // Excel Column filters
            if (matches && Object.keys(colFilters).length > 0) {
                for (var col in colFilters) {
                    var fVal = colFilters[col];
                    var cellText = '';
                    if (col === '1') cellText = row.data('adm') || '';
                    else if (col === '2') cellText = row.data('name') || '';
                    else if (col === '3') cellText = row.data('class') || '';
                    else if (col === '4') cellText = row.data('grade') || '';
                    else if (col === '5') cellText = (row.data('marks') || '') + '';
                    else if (col === '6') cellText = (row.data('att-str') || '').toLowerCase();

                    if (cellText.indexOf(fVal) === -1) {
                        matches = false;
                        break;
                    }
                }
            }

            if (matches) {
                row.removeClass('filtered-out');
            } else {
                row.addClass('filtered-out');
            }
        });

        currentPage = 1;
        paginateTable();
    }

    // 2. Pagination Logic
    function paginateTable() {
        var visibleRows = allRows.not('.filtered-out');
        var count = visibleRows.length;
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(count / rowsPerPage);
        if (totalPages < 1) totalPages = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * (rowsPerPage === 'all' ? count : rowsPerPage);
        var end = rowsPerPage === 'all' ? count : start + rowsPerPage;

        allRows.hide();
        visibleRows.slice(start, end).show();

        // Update UI
        $('#page-start').text(count > 0 ? (start + 1) : 0);
        $('#page-end').text(Math.min(end, count));
        $('#total-records').text(count);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || count === 0);
    }

    // 3. Dynamic Column Sorting
    $('.header-titles-row th[data-sort]').on('click', function() {
        var sortKey = $(this).data('sort');
        if (currentSortCol === sortKey) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortCol = sortKey;
            sortDirection = 'asc';
        }

        // Update sort indicators
        $('.header-titles-row th').removeClass('is-sorted');
        $(this).addClass('is-sorted');
        $(this).find('.sort-indicator i').attr('class', sortDirection === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc');

        var tbody = $('#performanceTableBody');
        var rowsArray = allRows.get();

        rowsArray.sort(function(a, b) {
            var valA = $(a).data(sortKey);
            var valB = $(b).data(sortKey);

            if (typeof valA === 'number' && typeof valB === 'number') {
                return sortDirection === 'asc' ? valA - valB : valB - valA;
            }

            valA = (valA || '') + '';
            valB = (valB || '') + '';

            return sortDirection === 'asc' 
                ? valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' })
                : valB.localeCompare(valA, undefined, { numeric: true, sensitivity: 'base' });
        });

        $.each(rowsArray, function(index, row) {
            tbody.append(row);
        });

        allRows = $('.student-row');
        paginateTable();
    });

    // 4. Batch / Multi-Checkbox Management
    $('#selectAllCheckbox').on('change', function() {
        var checked = $(this).prop('checked');
        $('.student-row').not('.filtered-out').find('.student-row-checkbox').prop('checked', checked);
        updateBatchBar();
    });

    $(document).on('change', '.student-row-checkbox', function() {
        updateBatchBar();
    });

    function updateBatchBar() {
        var selectedCount = $('.student-row-checkbox:checked').length;
        $('#batchSelectedCount').text(selectedCount);
        if (selectedCount > 0) {
            $('#batchActionsBar').addClass('is-visible');
        } else {
            $('#batchActionsBar').removeClass('is-visible');
            $('#selectAllCheckbox').prop('checked', false);
        }
    }
    window.clearBatchSelection = function() {
        $('.student-row-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        updateBatchBar();
    };

    // Event Bindings
    $('#quickTableSearch').on('keyup input', applyFilters);
    $('.excel-col-filter').on('keyup input', applyFilters);

    $('#btnClearExcelFilters').on('click', function() {
        $('.excel-col-filter').val('');
        $('#quickTableSearch').val('');
        applyFilters();
    });

    $('.status-tab-btn').on('click', function() {
        $('.status-tab-btn').removeClass('is-active');
        $(this).addClass('is-active');
        currentStatusFilter = $(this).data('status-filter');
        applyFilters();
    });

    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        rowsPerPage = val === 'all' ? 'all' : parseInt(val, 10);
        currentPage = 1;
        paginateTable();
    });

    $('#btn-first').on('click', function() {
        currentPage = 1;
        paginateTable();
    });
    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            paginateTable();
        }
    });
    $('#btn-next').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            paginateTable();
        }
    });
    $('#btn-last').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        currentPage = totalPages;
        paginateTable();
    });

    // Initial render
    paginateTable();
});

// Filter by Ribbon Grade Click
function filterByRibbonGrade(gradeKey) {
    $('.status-tab-btn').removeClass('is-active');
    $('.status-tab-btn[data-status-filter="' + gradeKey + '"]').addClass('is-active').trigger('click');
}

// Quick Student Snapshot Modal Logic
function openStudentSnapshot(studentId) {
    var row = $('#row-student-' + studentId);
    if (!row.length) return;

    var name = row.data('display-name') || '-';
    var adm = row.data('adm') || '-';
    var cls = row.data('class-name') || '-';
    var father = row.data('father') || 'Not Listed';
    var mobile = row.data('mobile') || '';
    var grade = row.data('grade-label') || 'Grade 5';
    var marks = (row.data('marks') || 0) + '%';
    var att = row.data('att-str') || 'N/A';
    var topper = row.data('topper');
    var topperText = topper == 1 ? '🥇 Rank 1 Topper' : (topper == 2 ? '🥈 Rank 2 Topper' : (topper == 3 ? '🥉 Rank 3 Topper' : 'Regular Standing'));

    $('#snapAvatar').text(name.charAt(0).toUpperCase());
    $('#snapName').text(name);
    $('#snapSub').text(cls + ' • Adm. No: ' + adm.toUpperCase());
    $('#snapFather').text('Father: ' + father);
    $('#snapGrade').text(grade);
    $('#snapScore').text(marks);
    $('#snapAtt').text(att);
    $('#snapTopper').text(topperText);
    
    if (mobile) {
        $('#snapMobile').text(mobile);
        $('#snapWhatsappBtn').attr('href', 'https://wa.me/91' + mobile.replace(/\D/g, '')).show();
        $('#snapContactWrap').show();
    } else {
        $('#snapMobile').text('N/A');
        $('#snapWhatsappBtn').hide();
    }

    $('#snapFullProfileBtn').attr('href', '{{ url("studentParticularPerformance") }}/' + studentId);
    $('#snapshotModalOverlay').css('display', 'flex');
}

function closeStudentSnapshot(event) {
    $('#snapshotModalOverlay').hide();
}
function closeStudentSnapshotDirect() {
    $('#snapshotModalOverlay').hide();
}

// CSV Export
function exportPerformanceCSV() {
    var rows = [];
    rows.push(['#', 'Admission No', 'Student Name', 'Class', 'Grade', 'Average Percentage', 'Attendance']);

    $('.student-row').not('.filtered-out').each(function(i) {
        var r = $(this);
        rows.push([
            (i + 1),
            r.data('adm') || '',
            r.find('.student-name-link').text().trim(),
            r.data('class-name') || '',
            r.data('grade-label') || '',
            (r.data('marks') || 0) + '%',
            r.data('att-str') || 'N/A'
        ]);
    });

    var csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => '"' + (cell + '').replace(/"/g, '""') + '"').join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "student_performance_report_" + (new Date().toISOString().slice(0, 10)) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportSelectedPerformanceCSV() {
    var rows = [];
    rows.push(['#', 'Admission No', 'Student Name', 'Class', 'Grade', 'Average Percentage', 'Attendance']);

    var count = 0;
    $('.student-row-checkbox:checked').each(function() {
        var r = $(this).closest('.student-row');
        count++;
        rows.push([
            count,
            r.data('adm') || '',
            r.find('.student-name-link').text().trim(),
            r.data('class-name') || '',
            r.data('grade-label') || '',
            (r.data('marks') || 0) + '%',
            r.data('att-str') || 'N/A'
        ]);
    });

    if (count === 0) return;

    var csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => '"' + (cell + '').replace(/"/g, '""') + '"').join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "selected_students_performance_" + (new Date().toISOString().slice(0, 10)) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
@endsection
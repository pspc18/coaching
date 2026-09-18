@extends('layout.app')

@section('content')
@php
    $classType = Helper::ClassType();
    $selectedClass = collect($classType)->firstWhere('id', (int) ($search['class_type_id'] ?? 0));
    $studentCount = $data instanceof \Illuminate\Support\Collection ? $data->count() : count($data ?? []);
    $subjectCount = $list_subject instanceof \Illuminate\Support\Collection ? $list_subject->count() : count($list_subject ?? []);

    $assignedCount = 0;
    $unassignedCount = 0;
    $totalAssignedChips = 0;

    if (!empty($assignedSubjectsByStudent)) {
        foreach ($data as $st) {
            $subList = $assignedSubjectsByStudent[$st->id] ?? collect();
            if ($subList->isNotEmpty()) {
                $assignedCount++;
                $totalAssignedChips += $subList->count();
            } else {
                $unassignedCount++;
            }
        }
    } else {
        $unassignedCount = $studentCount;
    }
@endphp

<style>
/* ==========================================================================
   ARISE ERP - STREAM & OPTIONAL SUBJECT UPDATE THEME
   - Signature Dark Navy Theme (#002C54 -> #0f3460)
   - Dual-Thead Sticky Header with In-Column Excel Filter Row (.excel-filter-row)
   - Smart Stream Preset Templates (Science, Commerce, Arts 1-Click Select)
   - Status Tabs (All, Unassigned, Assigned)
   - Real-Time Ajax Delete for Subject Chips
   - Viewport Auto-Adjust (calc(100vh - var(--header-height) - 16px))
   - Pinned Bottom Pagination Toolbar (.table-pagination-bar)
   ========================================================================== */

:root {
    --header-height: 56px;
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
}

.stream-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.stream-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjust Container */
.stream-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 8px 0;
}

/* 1. Hero Header Banner */
.stream-hero {
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
.stream-hero-text {
    display: flex;
    flex-direction: column;
}
.stream-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.stream-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.stream-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.stream-hero-stats {
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
.hero-stat-chip.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
}
.hero-stat-chip.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}

/* Action buttons */
.stream-hero-actions {
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
    align-items: flex-end;
    gap: 8px;
}
.filter-group-class {
    width: 260px;
}
.filter-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-select {
    height: 28px;
    width: 100%;
    padding: 0 8px;
    font-size: 11.5px;
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

.filter-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.btn-filter-submit {
    height: 28px;
    padding: 0 12px;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #002C54;
    border-radius: 2px;
    font-weight: 700;
    font-size: 11px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
}
.btn-filter-submit:hover {
    background: #001833;
    border-color: #001833;
}
.btn-filter-reset {
    height: 28px;
    padding: 0 9px;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 11px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
}
.btn-filter-reset:hover {
    background: #e2e8f0;
    color: #334155;
}

/* 3. Main Workspace Split (Subject Picker Left + Students Table Right) */
.stream-workspace-row {
    display: flex;
    gap: 6px;
    flex: 1;
    min-height: 0;
}
.stream-sidebar-panel {
    width: 290px;
    min-width: 270px;
    max-width: 320px;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    overflow: hidden;
}
.stream-table-panel {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* Subject Sidebar Styling */
.subject-panel-header {
    padding: 5px 10px;
    background: #002342;
    color: #ffffff;
    border-bottom: 1px solid rgba(255,255,255,.12);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.subject-panel-title {
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
}
.subject-helper-links {
    font-size: 9.5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.subject-helper-links a {
    color: #38bdf8;
    text-decoration: none !important;
    font-weight: 600;
}
.subject-helper-links a:hover {
    text-decoration: underline !important;
}

/* Creative Quick Stream Presets */
.stream-presets-wrap {
    padding: 5px 8px;
    background: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex-shrink: 0;
}
.preset-label {
    font-size: 9.5px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.preset-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
.preset-pill-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 6px;
    font-size: 9.5px;
    font-weight: 700;
    color: #002C54;
    cursor: pointer;
    line-height: 1.2;
    transition: all .12s ease;
}
.preset-pill-btn:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

.subject-search-box {
    padding: 4px 8px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.subject-search-input {
    width: 100%;
    height: 24px;
    padding: 0 6px;
    font-size: 10.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
}
.subject-search-input:focus {
    border-color: #002C54;
}

.subject-list-scroll {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 6px;
    background: #f8fafc;
}
.subject-picker-tile {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    margin-bottom: 4px;
    cursor: pointer;
    transition: all .12s ease;
    user-select: none;
}
.subject-picker-tile:hover {
    background: #f0fdf4;
    border-color: #86efac;
}
.subject-picker-tile.is-selected {
    background: #ecfdf5;
    border-color: #10b981;
    box-shadow: 0 1px 2px rgba(16, 185, 129, 0.15);
}
.subject-picker-tile input[type="checkbox"] {
    width: 14px;
    height: 14px;
    cursor: pointer;
    accent-color: #002C54;
    margin: 0;
}
.subject-tile-name {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.subject-panel-footer {
    padding: 6px 8px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.btn-save-assignments {
    width: 100%;
    height: 32px;
    background: #16a34a;
    color: #ffffff;
    border: 1px solid #15803d;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all .15s ease;
}
.btn-save-assignments:hover {
    background: #15803d;
    border-color: #166534;
}

/* ==========================================================================
   STUDENTS MATRIX TABLE CARD (Exact standard matching userView / studentlist)
   ========================================================================== */

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

/* Status Filter Tabs (All / Unassigned / Assigned) */
.student-status-tabs {
    display: flex;
    align-items: center;
    background: #001f3d;
    border-bottom: 1px solid rgba(255,255,255,.1);
    padding: 2px 8px;
    gap: 4px;
    flex-shrink: 0;
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

/* Selection helper bar */
.selection-toolbar-bar {
    padding: 3px 10px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    color: #475569;
    flex-shrink: 0;
}
.selection-tools {
    display: flex;
    align-items: center;
    gap: 8px;
}
.selection-tools a {
    color: #0284c7;
    text-decoration: none !important;
    font-weight: 600;
}
.selection-tools a:hover {
    text-decoration: underline !important;
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Design */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Top thead Titles Row */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    height: 34px;
    font-size: 10.5px;
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

/* In-Column Excel Filter Row (Exact standard #08335c match) */
.excel-filter-row th {
    position: sticky;
    top: 34px;
    background: #08335c;
    color: #ffffff;
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
    height: 25px;
    padding: 2px 6px;
    font-size: 10.5px;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    background: #051e38;
    color: #ffffff;
    outline: none;
    box-sizing: border-box;
    transition: all .15s ease;
}
.excel-col-filter::placeholder {
    color: #94a3b8;
    font-size: 10px;
}
.excel-col-filter:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #38bdf8;
    box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
}
select.excel-col-filter {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='6' viewBox='0 0 8 6'%3E%3Cpath fill='%2394a3b8' d='M0 0l4 4 4-4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 6px center;
    padding-right: 18px;
    appearance: none;
}
select.excel-col-filter option {
    background: #002C54;
    color: #ffffff;
}

.btn-reset-filters {
    height: 25px;
    width: 25px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(244, 63, 94, 0.2);
    border: 1px solid rgba(244, 63, 94, 0.4);
    color: #fecdd3;
    border-radius: 2px;
    cursor: pointer;
    font-size: 10.5px;
    transition: all .15s;
}
.btn-reset-filters:hover {
    background: #f43f5e;
    color: #ffffff;
    border-color: #f43f5e;
}

/* Table Body - Soft Slate Alternating Colors */
.dash-table tbody tr {
    background: #ffffff;
    transition: background .1s ease;
}
.dash-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-table tbody tr:hover {
    background: #edf2f7 !important;
}

.dash-table tbody td {
    padding: 4px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
    font-size: 11px;
}
.dash-table tbody td:last-child {
    border-right: none;
}

/* Student Avatar & Meta */
.student-avatar-micro {
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
    margin-right: 6px;
    flex-shrink: 0;
}
.student-name-text {
    font-weight: 700;
    color: #002C54;
    line-height: 1.2;
}

/* Assigned Stream Subject Chips */
.subject-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 3px;
    min-height: 22px;
}
.subject-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 1px 5px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.2;
    transition: all .15s ease;
}
.subject-chip:hover {
    background: #bae6fd;
    border-color: #7dd3fc;
}
.btn-delete-chip {
    border: none;
    background: transparent;
    color: #e11d48;
    padding: 0;
    cursor: pointer;
    font-size: 10.5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 2px;
    transition: transform .15s ease;
}
.btn-delete-chip:hover {
    transform: scale(1.25);
    color: #be123c;
}
.no-subjects-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 9.5px;
    font-weight: 700;
    color: #94a3b8;
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px dashed #cbd5e1;
}

/* 4. Pinned Bottom Pagination Toolbar */
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

@media(max-width:991px) {
    .stream-page-layout {
        height: auto;
        overflow: visible;
        padding: 4px;
    }
    .stream-workspace-row {
        flex-direction: column;
    }
    .stream-sidebar-panel {
        width: 100%;
        max-width: 100%;
        max-height: 280px;
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
</style>

<div class="content-wrapper stream-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="stream-page-layout">

                {{-- 1. Signature Hero Header Banner --}}
                <div class="stream-hero">
                    <div class="stream-hero-text">
                        <span class="stream-kicker">Admission Setup &bull; Stream Subjects</span>
                        <h1 class="stream-title">
                            <i class="fa fa-graduation-cap text-warning"></i> Student Stream &amp; Optional Subject Setup
                        </h1>
                        <p class="stream-subtitle">
                            Assign subject streams to secondary/senior secondary students with real-time assignment management and fast filtering.
                        </p>
                    </div>

                    <div class="stream-hero-stats">
                        <div class="hero-stat-chip badge-sky">
                            <i class="fa fa-th-large"></i> Class: <b>{{ $selectedClass->name ?? 'Not Selected' }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald">
                            <i class="fa fa-users"></i> Students: <b id="statTotalStudents">{{ $studentCount }}</b>
                        </div>
                        <div class="hero-stat-chip badge-amber">
                            <i class="fa fa-book"></i> Subjects: <b>{{ $subjectCount }}</b>
                        </div>
                        <div class="hero-stat-chip badge-rose">
                            <i class="fa fa-tags"></i> Assigned Chips: <b>{{ $totalAssignedChips }}</b>
                        </div>
                    </div>

                    <div class="stream-hero-actions">
                        @if($studentCount > 0)
                            <button type="button" class="dash-btn dash-btn-light" onclick="exportStreamCSV()" title="Export Stream Allocation List">
                                <i class="fa fa-file-excel-o text-success"></i> Export CSV
                            </button>
                        @endif
                    </div>
                </div>

                {{-- 2. Class Selection Filter Card --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span><i class="fa fa-filter mr-1"></i> Class Selection Filter</span>
                        <span style="font-size: 10px; font-weight: normal; opacity: .85;">Choose higher class to load students &amp; subjects</span>
                    </div>
                    <div class="filter-card-body">
                        <form action="{{ url('stream_update') }}" method="GET" id="streamClassForm" class="filter-form-row">
                            <div class="filter-group-class">
                                <label class="filter-label">Select Target Class: <span class="text-danger">*</span></label>
                                <select name="class_type_id" class="filter-select select2" required onchange="document.getElementById('streamClassForm').submit()">
                                    <option value="">-- Choose Class --</option>
                                    @foreach($classType as $type)
                                        @if($type->orderBy > 10)
                                            <option value="{{ $type->id }}" {{ (int)$type->id === (int)($search['class_type_id'] ?? 0) ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button type="submit" class="btn-filter-submit">
                                    <i class="fa fa-search mr-1"></i> Load Setup
                                </button>
                                @if(!empty($search['class_type_id']))
                                    <a href="{{ url('stream_update') }}" class="btn-filter-reset" title="Reset Class">
                                        <i class="fa fa-refresh mr-1"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. Main Workspace Split (Subjects Left + Students Table Right) --}}
                @if($studentCount > 0)
                    <form action="{{ url('stream_update_save') }}" method="POST" id="streamAssignForm" class="stream-workspace-row">
                        @csrf
                        <input type="hidden" name="class_type_id" value="{{ $search['class_type_id'] }}">

                        {{-- Left Column: Available Subjects Panel with Creative Stream Presets --}}
                        <div class="stream-sidebar-panel">
                            <div class="subject-panel-header">
                                <span class="subject-panel-title">
                                    <i class="fa fa-book text-warning"></i> Subjects ({{ $subjectCount }})
                                </span>
                                <div class="subject-helper-links">
                                    <a href="javascript:void(0);" id="btnSelectAllSubjects">Select All</a>
                                    <span>&bull;</span>
                                    <a href="javascript:void(0);" id="btnClearAllSubjects">Clear</a>
                                </div>
                            </div>

                            {{-- Creative 1-Click Stream Presets --}}
                            <div class="stream-presets-wrap">
                                <div class="preset-label">
                                    <span><i class="fa fa-magic mr-1 text-primary"></i> Quick Stream Presets:</span>
                                </div>
                                <div class="preset-chips">
                                    <button type="button" class="preset-pill-btn" onclick="applySubjectPreset('pcm')">🧪 PCM</button>
                                    <button type="button" class="preset-pill-btn" onclick="applySubjectPreset('pcb')">🧬 PCB</button>
                                    <button type="button" class="preset-pill-btn" onclick="applySubjectPreset('commerce')">📊 Commerce</button>
                                    <button type="button" class="preset-pill-btn" onclick="applySubjectPreset('arts')">🎨 Arts</button>
                                </div>
                            </div>

                            <div class="subject-search-box">
                                <input type="text" id="searchSubjectFilter" class="subject-search-input" placeholder="Search subjects...">
                            </div>

                            <div class="subject-list-scroll">
                                @forelse($list_subject as $subject)
                                    <label class="subject-picker-tile" id="sub-tile-{{ $subject->id }}" data-subject-name="{{ strtolower($subject->name) }}">
                                        <input type="checkbox" name="subject_id[]" value="{{ $subject->id }}" class="subject-checkbox mr-2">
                                        <span class="subject-tile-name">{{ $subject->name }}</span>
                                    </label>
                                @empty
                                    <div class="p-3 text-muted text-center" style="font-size: 11px;">
                                        <i class="fa fa-info-circle mb-1"></i> No subjects mapped for this class.
                                        <div class="mt-1">
                                            <a href="{{ url('add_subject') }}" class="btn btn-xs btn-primary font-weight-bold">Add Subject</a>
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            <div class="subject-panel-footer">
                                <button type="submit" class="btn-save-assignments" id="btnSubmitAssignments">
                                    <i class="fa fa-check-circle"></i> Save Assignments
                                </button>
                            </div>
                        </div>

                        {{-- Right Column: Students & Stream Allocation Table Card --}}
                        <div class="stream-table-panel">
                            <div class="dash-table-card">
                                
                                {{-- Card Header --}}
                                <div class="dash-card-header">
                                    <div class="dash-card-title">
                                        <i class="fa fa-users"></i>
                                        <span>Enrolled Students</span>
                                        <span class="badge-total-records">
                                            Class: {{ $selectedClass->name ?? '' }} &bull; Total: <span id="visibleStudentCount">{{ $studentCount }}</span>
                                        </span>
                                    </div>

                                    <div class="table-header-tools">
                                        <div class="table-search-wrap">
                                            <i class="fa fa-search"></i>
                                            <input type="text" id="liveStudentSearch" class="table-search-input" placeholder="Quick search in table...">
                                        </div>
                                    </div>
                                </div>

                                {{-- Creative Status Filter Tabs --}}
                                <div class="student-status-tabs">
                                    <button type="button" class="status-tab-btn is-active" data-status-filter="all">
                                        <i class="fa fa-list"></i> All Students <span class="tab-pill-count" id="tabCountAll">{{ $studentCount }}</span>
                                    </button>
                                    <button type="button" class="status-tab-btn" data-status-filter="unassigned">
                                        <i class="fa fa-exclamation-circle text-warning"></i> Unassigned <span class="tab-pill-count text-danger" id="tabCountUnassigned">{{ $unassignedCount }}</span>
                                    </button>
                                    <button type="button" class="status-tab-btn" data-status-filter="assigned">
                                        <i class="fa fa-check-circle text-success"></i> Has Subjects <span class="tab-pill-count text-success" id="tabCountAssigned">{{ $assignedCount }}</span>
                                    </button>
                                </div>

                                {{-- Selection Toolbar --}}
                                <div class="selection-toolbar-bar">
                                    <div class="selection-tools">
                                        <span><strong>Select:</strong></span>
                                        <a href="javascript:void(0);" id="btnSelectAllStudentsVisible"><i class="fa fa-check-square-o mr-1"></i> All Visible</a>
                                        <span>&bull;</span>
                                        <a href="javascript:void(0);" id="btnSelectUnassigned"><i class="fa fa-square-o mr-1"></i> Only Unassigned</a>
                                        <span>&bull;</span>
                                        <a href="javascript:void(0);" id="btnClearStudentSelection"><i class="fa fa-times mr-1"></i> Clear Selection</a>
                                    </div>
                                    <div style="font-size: 10px; color: #64748b;">
                                        <i class="fa fa-trash-o text-danger mr-1"></i> Click trash icon on any subject chip to instantly remove it.
                                    </div>
                                </div>

                                {{-- Scrollable Table Viewport with Dual Thead (Titles + In-Column Excel Filters) --}}
                                <div class="table-scroll-container">
                                    <table class="dash-table" id="studentStreamTable">
                                        <thead>
                                            {{-- Top Titles Header Row --}}
                                            <tr class="header-titles-row">
                                                <th style="width: 45px; text-align: center;">
                                                    <input type="checkbox" id="masterSelectCheckbox" title="Select / Deselect All">
                                                </th>
                                                <th style="width: 105px; text-align: center;">Adm No</th>
                                                <th style="min-width: 180px;">Student Name</th>
                                                <th style="min-width: 250px;">Assigned Stream Subjects</th>
                                                <th style="width: 90px; text-align: center;">Total</th>
                                            </tr>

                                            {{-- In-Column Excel Filter Row --}}
                                            <tr class="excel-filter-row">
                                                <th style="text-align: center;">
                                                    <button type="button" class="btn-reset-filters" id="btnResetInColFilters" title="Clear all column filters">
                                                        <i class="fa fa-eraser"></i>
                                                    </button>
                                                </th>
                                                <th>
                                                    <input type="text" class="excel-col-filter" placeholder="Filter Adm..." data-col-index="1">
                                                </th>
                                                <th>
                                                    <input type="text" class="excel-col-filter" placeholder="Filter Name..." data-col-index="2">
                                                </th>
                                                <th>
                                                    <select class="excel-col-filter" data-col-index="3" id="filterBySubjectSelect">
                                                        <option value="">All Subjects</option>
                                                        @foreach($list_subject as $s)
                                                            <option value="{{ strtolower($s->name) }}">{{ $s->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </th>
                                                <th>
                                                    <select class="excel-col-filter" data-col-index="4" id="filterByCountSelect">
                                                        <option value="">All Counts</option>
                                                        <option value="0">0 Subjects</option>
                                                        <option value="1">1 Subject</option>
                                                        <option value="2">2 Subjects</option>
                                                        <option value="3">3 Subjects</option>
                                                        <option value="4">4 Subjects</option>
                                                        <option value="5">5+ Subjects</option>
                                                    </select>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $index => $student)
                                                @php
                                                    $assignedSubjects = $assignedSubjectsByStudent[$student->id] ?? collect();
                                                    $stName = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
                                                    $initial = strtoupper(substr($stName ?: 'S', 0, 1));
                                                    $hasAssigned = $assignedSubjects->isNotEmpty();
                                                    $subjectsNamesString = strtolower($assignedSubjects->pluck('name')->implode(' '));
                                                    $subCount = $assignedSubjects->count();
                                                @endphp
                                                <tr class="table-data-row student-data-row" 
                                                    data-name="{{ strtolower($stName) }}" 
                                                    data-adm="{{ strtolower($student->admissionNo ?? '') }}"
                                                    data-subjects="{{ $subjectsNamesString }}"
                                                    data-count="{{ $subCount }}"
                                                    data-has-subjects="{{ $hasAssigned ? '1' : '0' }}">
                                                    
                                                    {{-- 1. Checkbox --}}
                                                    <td style="text-align: center;">
                                                        <input type="checkbox" name="admission_id[]" class="student-checkbox" value="{{ $student->id }}">
                                                    </td>

                                                    {{-- 2. Adm No --}}
                                                    <td style="text-align: center; font-weight: 700;">
                                                        <code>{{ $student->admissionNo ?: 'N/A' }}</code>
                                                    </td>

                                                    {{-- 3. Student Details --}}
                                                    <td>
                                                        <div style="display: flex; align-items: center;">
                                                            <span class="student-avatar-micro">{{ $initial }}</span>
                                                            <span class="student-name-text">{{ $stName ?: '-' }}</span>
                                                        </div>
                                                    </td>

                                                    {{-- 4. Assigned Stream Subject Chips --}}
                                                    <td>
                                                        <div class="subject-chips-wrap" id="subject-list-{{ $student->id }}">
                                                            @forelse($assignedSubjects as $subject)
                                                                <span class="subject-chip" id="admission-{{ $student->id }}-{{ $subject->id }}">
                                                                    <span>{{ $subject->name }}</span>
                                                                    <button type="button" class="btn-delete-chip delete-subject"
                                                                            data-admission-id="{{ $student->id }}"
                                                                            data-subject-id="{{ $subject->id }}"
                                                                            title="Remove {{ $subject->name }}">
                                                                        <i class="fa fa-times-circle"></i>
                                                                    </button>
                                                                </span>
                                                            @empty
                                                                <span class="no-subjects-pill">
                                                                    <i class="fa fa-minus-circle"></i> No subjects assigned
                                                                </span>
                                                            @endforelse
                                                        </div>
                                                    </td>

                                                    {{-- 5. Total Count Badge --}}
                                                    <td style="text-align: center;">
                                                        <span class="badge {{ $hasAssigned ? 'badge-primary' : 'badge-light text-muted' }} font-weight-bold subject-count-badge" id="sub-count-{{ $student->id }}">
                                                            {{ $subCount }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pinned Bottom Pagination Toolbar --}}
                                <div class="table-pagination-bar">
                                    <div class="pagination-info">
                                        Showing <span id="page-start" class="font-weight-bold text-white">{{ $studentCount > 0 ? 1 : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min(25, $studentCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $studentCount }}</span> students
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

                    </form>
                @elseif(!empty($search['class_type_id']))
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon"><i class="fa fa-graduation-cap"></i></div>
                            <div class="empty-title">No Active Students Found</div>
                            <div class="empty-desc">There are no active enrolled students found for class "{{ $selectedClass->name ?? '' }}".</div>
                        </div>
                    </div>
                @else
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon text-primary"><i class="fa fa-filter"></i></div>
                            <div class="empty-title">Please Select a Class to Begin</div>
                            <div class="empty-desc">Choose a secondary or higher secondary class from the dropdown above to load students and stream subjects.</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
</div>

@section('scripts')
<script>
const streamBaseUrl = @json(url('/'));

// Creative 1-Click Stream Presets Auto-Selector
function applySubjectPreset(presetType) {
    $('.subject-checkbox').prop('checked', false);
    
    var keywords = [];
    if (presetType === 'pcm') {
        keywords = ['physic', 'chem', 'math', 'eng', 'physical', 'comp', 'ip'];
    } else if (presetType === 'pcb') {
        keywords = ['physic', 'chem', 'bio', 'eng', 'physical', 'hindi'];
    } else if (presetType === 'commerce') {
        keywords = ['account', 'business', 'eco', 'math', 'ip', 'eng', 'physical'];
    } else if (presetType === 'arts') {
        keywords = ['hist', 'pol', 'geo', 'eco', 'socio', 'eng', 'hindi', 'psych', 'draw'];
    }

    $('.subject-picker-tile').each(function() {
        var subName = $(this).data('subject-name') || '';
        var match = false;
        for (var i = 0; i < keywords.length; i++) {
            if (subName.indexOf(keywords[i]) !== -1) {
                match = true;
                break;
            }
        }
        if (match) {
            $(this).find('.subject-checkbox').prop('checked', true);
            $(this).addClass('is-selected');
        } else {
            $(this).removeClass('is-selected');
        }
    });

    if (window.toastr) {
        toastr.info('Preset "' + presetType.toUpperCase() + '" applied to subject picker!');
    }
}

function exportStreamCSV() {
    var table = document.getElementById("studentStreamTable");
    if (!table) return;

    var csv = [];
    csv.push(['"#"','"Admission No"','"Student Name"','"Assigned Stream Subjects"','"Total Subjects"'].join(","));

    var rows = table.querySelectorAll("tbody tr.student-data-row");
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var adm = row.getAttribute('data-adm') || '';
        var name = row.querySelector('.student-name-text') ? row.querySelector('.student-name-text').innerText.trim() : '';
        
        var chipTexts = [];
        row.querySelectorAll('.subject-chip span:first-child').forEach(function(sp) {
            chipTexts.push(sp.innerText.trim());
        });
        var subjectsStr = chipTexts.join(", ");
        var totalCount = chipTexts.length;

        csv.push([
            '"' + (i + 1) + '"',
            '"' + adm + '"',
            '"' + name.replace(/"/g, '""') + '"',
            '"' + subjectsStr.replace(/"/g, '""') + '"',
            '"' + totalCount + '"'
        ].join(","));
    }

    var csvString = csv.join("\n");
    var filename = "student_stream_allocation_" + (new Date().toISOString().slice(0, 10)) + ".csv";
    var link = document.createElement("a");
    link.style.display = "none";
    link.setAttribute("href", 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // 1. Subject Picker Card selection highlights
    $('.subject-checkbox').on('change', function() {
        var $tile = $(this).closest('.subject-picker-tile');
        if (this.checked) {
            $tile.addClass('is-selected');
        } else {
            $tile.removeClass('is-selected');
        }
    });

    $('#btnSelectAllSubjects').on('click', function(e) {
        e.preventDefault();
        $('.subject-checkbox').prop('checked', true).trigger('change');
    });

    $('#btnClearAllSubjects').on('click', function(e) {
        e.preventDefault();
        $('.subject-checkbox').prop('checked', false).trigger('change');
    });

    // Subject in-panel filter search
    $('#searchSubjectFilter').on('keyup input', function() {
        var q = $(this).val().toLowerCase().trim();
        $('.subject-picker-tile').each(function() {
            var text = $(this).find('.subject-tile-name').text().toLowerCase();
            $(this).toggle(q === '' || text.indexOf(q) !== -1);
        });
    });

    /* ==========================================================
       PAGINATION, IN-COLUMN EXCEL FILTERS & LIVE SEARCH ENGINE
       ========================================================== */
    var currentPage = 1;
    var pageSize = 25; // default 25
    var activeStatusTab = 'all'; // all, unassigned, assigned

    function updateStudentPagination() {
        var $rows = $("#studentStreamTable tbody tr.student-data-row");
        if ($rows.length === 0) {
            $('#page-start').text(0);
            $('#page-end').text(0);
            $('#total-records').text(0);
            $('#visibleStudentCount').text(0);
            $('#statTotalStudents').text(0);
            $('#current-page').text(1);
            $('#total-pages').text(1);
            $('#btn-first, #btn-prev, #btn-next, #btn-last').prop('disabled', true);
            return;
        }

        var globalSearch = $('#liveStudentSearch').val().toLowerCase().trim();
        
        // In-column filters
        var inColFilters = [];
        $('.excel-col-filter').each(function() {
            var colIdx = $(this).data('col-index');
            var val = $(this).val().toLowerCase().trim();
            if (val !== '') {
                inColFilters.push({ colIndex: colIdx, query: val });
            }
        });

        var matchingRows = [];

        $rows.each(function() {
            var $row = $(this);
            var name = ($row.attr('data-name') || '');
            var adm = ($row.attr('data-adm') || '');
            var subStr = ($row.attr('data-subjects') || '');
            var hasSub = ($row.attr('data-has-subjects') || '0');
            var subCount = parseInt($row.attr('data-count') || '0', 10);
            var fullText = $row.text().toLowerCase();

            var matches = true;

            // Status tab filter
            if (activeStatusTab === 'unassigned' && hasSub !== '0') {
                matches = false;
            } else if (activeStatusTab === 'assigned' && hasSub === '0') {
                matches = false;
            }

            // Global search
            if (matches && globalSearch !== '') {
                if (name.indexOf(globalSearch) === -1 && adm.indexOf(globalSearch) === -1 && fullText.indexOf(globalSearch) === -1) {
                    matches = false;
                }
            }

            // In-column filters
            if (matches && inColFilters.length > 0) {
                for (var i = 0; i < inColFilters.length; i++) {
                    var filter = inColFilters[i];
                    if (filter.colIndex === 1) { // Adm
                        if (adm.indexOf(filter.query) === -1) { matches = false; break; }
                    } else if (filter.colIndex === 2) { // Name
                        if (name.indexOf(filter.query) === -1) { matches = false; break; }
                    } else if (filter.colIndex === 3) { // Subject
                        if (subStr.indexOf(filter.query) === -1) { matches = false; break; }
                    } else if (filter.colIndex === 4) { // Count
                        if (filter.query === '5' && subCount < 5) { matches = false; break; }
                        else if (filter.query !== '5' && subCount !== parseInt(filter.query, 10)) { matches = false; break; }
                    }
                }
            }

            if (matches) {
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
        $('#studentStreamTable tbody tr.no-match-row').remove();

        if (totalMatching === 0) {
            $('#studentStreamTable tbody').append(
                '<tr class="no-match-row"><td colspan="5" class="p-0 border-0">' +
                '<div class="dash-empty-state">' +
                '<div class="empty-icon"><i class="fa fa-filter"></i></div>' +
                '<div class="empty-title">No Matching Students Found</div>' +
                '<div class="empty-desc">Try adjusting or clearing your active column filters.</div>' +
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
        $('#visibleStudentCount').text(totalMatching);
        $('#statTotalStudents').text(totalMatching);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        // Button state
        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || totalPages <= 1);
    }

    // Status tabs click
    $('.status-tab-btn').on('click', function() {
        $('.status-tab-btn').removeClass('is-active');
        $(this).addClass('is-active');
        activeStatusTab = $(this).data('status-filter');
        currentPage = 1;
        updateStudentPagination();
    });

    // In-column filters trigger
    $('.excel-col-filter').on('keyup change', function() {
        currentPage = 1;
        updateStudentPagination();
    });

    // Reset all in-column filters
    $('#btnResetInColFilters').on('click', function() {
        $('.excel-col-filter').val('');
        $('#liveStudentSearch').val('');
        $('.status-tab-btn[data-status-filter="all"]').trigger('click');
        currentPage = 1;
        updateStudentPagination();
    });

    // Rows per page
    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        if (val === 'all') {
            pageSize = -1;
        } else {
            pageSize = parseInt(val, 10);
        }
        currentPage = 1;
        updateStudentPagination();
    });

    // Pagination buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            updateStudentPagination();
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateStudentPagination();
        }
    });

    $('#btn-next').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage++;
            updateStudentPagination();
        }
    });

    $('#btn-last').on('click', function() {
        var totalPages = parseInt($('#total-pages').text(), 10) || 1;
        if (currentPage < totalPages) {
            currentPage = totalPages;
            updateStudentPagination();
        }
    });

    // Live search input
    $('#liveStudentSearch').on('keyup input', function() {
        currentPage = 1;
        updateStudentPagination();
    });

    // Master Checkbox & Selection Helpers
    $('#masterSelectCheckbox').on('change', function() {
        var isChecked = this.checked;
        $('#studentStreamTable tbody tr:visible .student-checkbox').prop('checked', isChecked);
    });

    $('#btnSelectAllStudentsVisible').on('click', function(e) {
        e.preventDefault();
        $('#studentStreamTable tbody tr:visible .student-checkbox').prop('checked', true);
        $('#masterSelectCheckbox').prop('checked', true);
    });

    $('#btnSelectUnassigned').on('click', function(e) {
        e.preventDefault();
        $('.student-checkbox').prop('checked', false);
        $('#studentStreamTable tbody tr:visible[data-has-subjects="0"] .student-checkbox').prop('checked', true);
    });

    $('#btnClearStudentSelection').on('click', function(e) {
        e.preventDefault();
        $('.student-checkbox').prop('checked', false);
        $('#masterSelectCheckbox').prop('checked', false);
    });

    // Form submission validation
    $('#streamAssignForm').on('submit', function(e) {
        var checkedSubjects = $('.subject-checkbox:checked').length;
        var checkedStudents = $('.student-checkbox:checked').length;

        if (checkedSubjects === 0) {
            e.preventDefault();
            if (window.toastr) {
                toastr.warning('Please select at least one subject to assign.');
            } else {
                alert('Please select at least one subject to assign.');
            }
            return false;
        }

        if (checkedStudents === 0) {
            e.preventDefault();
            if (window.toastr) {
                toastr.warning('Please select at least one student to receive the subjects.');
            } else {
                alert('Please select at least one student to receive the subjects.');
            }
            return false;
        }
    });

    // Real-time Ajax delete for single subject chip
    $(document).on('click', '.delete-subject', function(e) {
        e.preventDefault();
        const button = $(this);
        const admissionId = button.data('admission-id');
        const subjectId = button.data('subject-id');
        const chip = button.closest('.subject-chip');

        if (!confirm('Remove this subject assignment for the student?')) return;

        button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: streamBaseUrl + '/stream_remove/' + admissionId + '/' + subjectId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                const list = $('#subject-list-' + admissionId);
                const $row = list.closest('tr');
                chip.fadeOut(150, function() {
                    $(this).remove();
                    var remaining = list.find('.subject-chip').length;
                    $('#sub-count-' + admissionId).text(remaining);
                    $row.attr('data-count', remaining);
                    if (remaining === 0) {
                        list.append('<span class="no-subjects-pill"><i class="fa fa-minus-circle"></i> No subjects assigned</span>');
                        $('#sub-count-' + admissionId).removeClass('badge-primary').addClass('badge-light text-muted');
                        $row.attr('data-has-subjects', '0');
                        
                        // Update tab counts
                        var unassigned = parseInt($('#tabCountUnassigned').text(), 10) || 0;
                        var assigned = parseInt($('#tabCountAssigned').text(), 10) || 0;
                        $('#tabCountUnassigned').text(unassigned + 1);
                        if (assigned > 0) $('#tabCountAssigned').text(assigned - 1);
                    }
                });
                if (window.toastr) {
                    toastr.success(response.message || 'Subject removed successfully.');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                if (window.toastr) {
                    toastr.error(response.error || response.message || 'Subject could not be removed.');
                } else {
                    alert(response.error || response.message || 'Subject could not be removed.');
                }
                button.prop('disabled', false).html('<i class="fa fa-times-circle"></i>');
            }
        });
    });

    // Initial run
    updateStudentPagination();
});
</script>
@endsection

@endsection
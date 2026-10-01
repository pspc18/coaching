@php
    $classType = $classType ?? Helper::classType();
    $examTerms = $examTerms ?? [];
    $getsubject = Helper::getSubject();
    $date = date('Y-m-d');
    $permission = Helper::permissioncheck(8);

    $stats = $stats ?? [
        'total' => $totalCount ?? count($data ?? []),
        'assigned' => 0,
        'published' => 0,
        'pending' => 0,
    ];
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $totalCount = $totalCount ?? count($data ?? []);
    $lastPage = $lastPage ?? 1;
    $startIndex = ($currentPage - 1) * ($perPage === 'all' ? 0 : (int)$perPage);
@endphp

@extends('layout.app') 

@section('title', 'Exams Management - Examination Control')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EXAMS MANAGEMENT (PROFESSIONAL ADMISSIONVIEW STANDARD)
   - Viewport fitting: height: calc(100vh - var(--header-height, 56px) - 16px)
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   - Typography: 11.5px table text, 11px uppercase bold thead
   - In-column dark navy Excel filters (#051e38, #38bdf8 focus)
   - Subtle tinted pastel action buttons matching admissionView
   - Pinned bottom pagination bar (#002342)
   - 100% Screen-centered ERP theme modals (modal-dialog-centered)
   ========================================================================== */

.exam-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.exam-page * {
    box-sizing: border-box;
}
.exam-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Compact Hero Banner (~40px) */
.exam-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.exam-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.exam-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.exam-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.exam-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    height: 27px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1.4;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    border-color: rgba(255,255,255,0.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.22);
    color: #ffffff !important;
    border-color: #ffffff;
}

/* 2. Fast KPI Summary Filter Pills */
.exam-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 4px;
    flex-shrink: 0;
}
@media (max-width: 992px) {
    .exam-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
.kpi-mini-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    border-left: 3px solid #002C54;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}
.kpi-mini-card:hover {
    background: #f8fafc;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06);
}
.kpi-mini-card.active-kpi {
    background: #e6f0fa;
    border-color: #002C54;
    box-shadow: inset 0 0 0 1px #002C54;
}
.kpi-mini-card.kpi-published { border-left-color: #10b981; }
.kpi-mini-card.kpi-classes { border-left-color: #0284c7; }
.kpi-mini-card.kpi-pending { border-left-color: #f59e0b; }

.kpi-mini-card .kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 1px;
}
.kpi-mini-card .kpi-val {
    font-size: 14.5px;
    font-weight: 800;
    line-height: 1.1;
    color: #1e293b;
}
.kpi-mini-card .kpi-icon {
    width: 24px;
    height: 24px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}

/* 3. Unified Dark Navy Table Card */
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
    position: relative;
}
.dash-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* 4. Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #ffffff;
}

/* Loading Overlay */
.table-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.75);
    z-index: 40;
    display: none;
    align-items: center;
    justify-content: center;
}
.table-loading-spinner {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 15px;
    background: #002C54;
    color: #ffffff;
    border-radius: 2px;
    font-weight: 700;
    font-size: 11.5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}

/* 5. Dual-Row Sticky Table Headers */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    margin-bottom: 0;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 25;
}

/* Row 1: Titles Header (Sticky Top: 0) */
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
    border-bottom: 1px solid rgba(255,255,255,.14);
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
    box-sizing: border-box;
}

/* Row 2: In-Column Excel Filters (Sticky Top: 36px) */
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

/* Excel In-Column Input Controls */
.excel-col-filter {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease;
    color-scheme: dark;
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

/* Reset / Clear Button */
.btn-reset-filters {
    height: 26px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .15s ease;
    white-space: nowrap;
    width: 100%;
}
.btn-reset-filters:hover {
    background: #002C54;
    border-color: #38bdf8;
    color: #38bdf8;
}

/* Sticky Action Column (admissionView matching) */
.fixed_action_head {
    position: sticky !important;
    right: 0;
    top: 0;
    z-index: 25 !important;
    background: #002C54 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_filter {
    position: sticky !important;
    right: 0;
    top: 36px;
    z-index: 24 !important;
    background: #08335c !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_col {
    position: sticky !important;
    right: 0;
    z-index: 5;
    background: inherit;
    box-shadow: -3px 0 6px rgba(0,0,0,.08);
}
.dash-table tbody tr:nth-child(odd) .fixed_action_col {
    background: #ffffff !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #f8fafc !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e6f0fa !important;
}

/* Table Rows & Cells */
.dash-table tbody td {
    padding: 6px 8px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11.5px;
}
.dash-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}
.dash-table tbody tr:hover td {
    background-color: #e6f0fa;
}

/* Clean Typography for Exam Title & Date */
.exam-title-cell {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    line-height: 1.3;
}
.exam-name-link {
    font-size: 12px;
    font-weight: 600;
    color: #002C54 !important;
    text-decoration: none !important;
    display: inline-block;
    transition: color 0.15s ease;
}
.exam-name-link:hover {
    color: #0284c7 !important;
    text-decoration: underline !important;
}
.exam-date-meta {
    font-size: 10px;
    color: #64748b;
    font-weight: 500;
    margin-top: 1px;
    display: flex;
    align-items: center;
}

/* Exam Term Badge */
.badge-term {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    background: #ede9fe;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
}

/* Assigned Classes & Schedules */
.exam-classes-wrap {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.exam-schedule-group {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.exam-schedule-date {
    font-size: 10px;
    font-weight: 600;
    color: #475569;
    white-space: nowrap;
}
.exam-class-chips {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 3px;
}
.badge-class-chip {
    display: inline-flex;
    align-items: center;
    padding: 1px 5px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #1e293b;
    line-height: 1.3;
}
.badge-class-chip.chip-published {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #065f46;
}
.badge-class-chip.chip-pending {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}
.exam-no-classes {
    font-size: 11px;
    color: #94a3b8;
    font-style: italic;
}
.assign-link {
    color: #0284c7 !important;
    font-weight: 600;
    text-decoration: underline;
    font-style: normal;
}

/* Publication Action Buttons */
.btn-pub-action {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.btn-pub-success {
    background: #dcfce7;
    color: #15803d;
    border-color: #86efac;
}
.btn-pub-success:hover {
    background: #bbf7d0;
}
.btn-pub-partial {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
}
.btn-pub-partial:hover {
    background: #fde68a;
}
.btn-pub-pending {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}
.btn-pub-pending:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.badge-pub-readonly {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    background: #f8fafc;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
}

/* ==========================================================================
   SUBTLE TINTED PASTEL ACTION BUTTONS (Exact admissionView Standard)
   Soft pastel tint background, matching border, dark saturated icon color.
   ========================================================================== */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.table-btn {
    width: 23px;
    height: 23px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease;
    line-height: 1;
}
.btn-action-assign {
    background: #eff6ff;
    color: #1d4ed8 !important;
    border-color: #bfdbfe;
}
.btn-action-assign:hover {
    background: #1d4ed8;
    color: #ffffff !important;
}
.btn-action-excel {
    background: #ecfdf5;
    color: #059669 !important;
    border-color: #a7f3d0;
}
.btn-action-excel:hover {
    background: #059669;
    color: #ffffff !important;
}
.btn-action-marks {
    background: #f0fdfa;
    color: #0f766e !important;
    border-color: #99f6e4;
}
.btn-action-marks:hover {
    background: #0f766e;
    color: #ffffff !important;
}
.btn-action-copy {
    background: #f8fafc;
    color: #475569 !important;
    border-color: #cbd5e1;
}
.btn-action-copy:hover {
    background: #475569;
    color: #ffffff !important;
}
.btn-action-edit {
    background: #fffbeb;
    color: #b45309 !important;
    border-color: #fde68a;
}
.btn-action-edit:hover {
    background: #b45309;
    color: #ffffff !important;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626 !important;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #ffffff !important;
}

/* Empty State */
.dash-empty-state {
    padding: 32px 16px;
    text-align: center;
    background: #ffffff;
}
.dash-empty-state .empty-icon {
    font-size: 32px;
    color: #94a3b8;
    margin-bottom: 6px;
}
.dash-empty-state .empty-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
}
.dash-empty-state .empty-desc {
    font-size: 11px;
    color: #64748b;
}

/* 6. Pinned Bottom Pagination Toolbar (1:1 with admissionView) */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    height: 35px;
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
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 5px;
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
    padding: 2px 6px;
    font-size: 11px;
    outline: none;
    cursor: pointer;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 26px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    transition: all .15s ease;
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
    font-size: 11px;
    font-weight: 600;
    padding: 0 6px;
    color: #f1f5f9;
}

/* ==========================================================================
   STANDARD ERP THEME MODALS (100% SCREEN-CENTERED: modal-dialog-centered)
   ========================================================================== */

/* Centered Theme Delete Confirmation Modal */
.theme-delete-modal-dialog {
    max-width: 440px;
    margin: 1.75rem auto;
}
.theme-delete-modal-content {
    border: none;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    background: #ffffff;
}
.theme-delete-modal-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    color: #ffffff !important;
    padding: 10px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 48px !important;
    border-top-left-radius: 4px !important;
    border-top-right-radius: 4px !important;
}
.theme-delete-title-box {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.theme-delete-icon {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    border-radius: 3px !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}
.theme-delete-headings {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: center !important;
    line-height: 1.2 !important;
}
.theme-delete-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.25 !important;
    letter-spacing: 0.01em !important;
}
.theme-delete-subtitle {
    font-size: 10px !important;
    color: rgba(255, 255, 255, 0.85) !important;
    margin: 2px 0 0 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
}
.theme-delete-close {
    color: #ffffff !important;
    opacity: 0.9 !important;
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important;
    border-radius: 3px !important;
    width: 26px !important;
    height: 26px !important;
    min-width: 26px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 12px !important;
    transition: all 0.15s ease !important;
    padding: 0 !important;
    outline: none !important;
    line-height: 1 !important;
}
.theme-delete-close:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.28) !important;
    color: #ffffff !important;
}
.theme-delete-body {
    padding: 16px 16px !important;
    background: #ffffff !important;
}
.theme-delete-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin: 0 auto 10px auto;
    border: 1px solid #fecaca;
}
.theme-delete-prompt {
    font-size: 12px;
    color: #475569;
    margin-bottom: 5px;
}
.theme-delete-item-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #002C54;
    margin: 0 0 12px 0;
    word-break: break-word;
    line-height: 1.35;
}
.theme-delete-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 12px;
    text-align: left;
    font-size: 11.5px;
    color: #334155;
    margin-bottom: 12px;
}
.theme-delete-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    border-bottom: 1px solid #edf2f7;
}
.theme-delete-info-label {
    color: #64748b;
    font-weight: 600;
    font-size: 11px;
}
.theme-delete-info-val {
    color: #0f172a;
    max-width: 65%;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-size: 11.5px;
}
.theme-delete-alert-warning {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-left: 3px solid #e53e3e;
    border-radius: 2px;
    padding: 7px 10px;
    text-align: left;
    font-size: 10.5px;
    color: #9b2c2c;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.35;
}
.theme-delete-alert-warning i {
    margin-top: 2px;
    font-size: 12px;
    flex-shrink: 0;
}
.theme-delete-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 9px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 8px !important;
}
.theme-btn-cancel {
    height: 29px;
    font-size: 11.5px;
    padding: 0 14px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none !important;
}
.theme-btn-cancel:hover {
    background: #e2e8f0;
    color: #1e293b;
}
.theme-btn-confirm-delete {
    height: 29px;
    font-size: 11.5px;
    padding: 0 16px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #dc2626;
    color: #ffffff;
    border: 1px solid #b91c1c;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(220, 38, 38, 0.2);
}
.theme-btn-confirm-delete:hover {
    background: #b91c1c;
    border-color: #991b1b;
    color: #ffffff;
}

/* Centered Copy Exam Modal */
.modal-header-navy {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}
.modal-header-navy .modal-title {
    font-size: 13.5px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 7px;
}
.modal-header-navy .modal-subtitle {
    font-size: 10px;
    color: #93c5fd;
    display: block;
    margin-top: 1px;
}
.btn-modal-navy-submit {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3f;
    font-weight: 600;
    font-size: 11.5px;
    height: 29px;
    padding: 0 16px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-modal-navy-submit:hover {
    background: #0f3460;
}
</style>
@endsection

@section('content')
<div class="content-wrapper exam-page">
    <div class="exam-page-layout p-2">
        
        {{-- 1. Compact Hero Banner --}}
        <div class="exam-hero">
            <div>
                <span class="exam-kicker"><i class="fa fa-graduation-cap mr-1"></i> Examination Control &amp; Scheduling</span>
                <h1 class="exam-title">
                    <i class="fa fa-leanpub"></i> Exams Management
                </h1>
                <p class="exam-subtitle">
                    Configure offline examinations, assign class schedules, fill marks, and publish results to student portals.
                </p>
            </div>
            <div class="exam-hero-actions">
                @if($permission->add ?? true)
                    <a href="{{ url('add/exam') }}" class="dash-btn dash-btn-light">
                        <i class="fa fa-plus-circle text-primary"></i> Add Exam
                    </a>
                @endif
                <a href="{{ url('add/examination_schedule') }}" class="dash-btn dash-btn-outline" title="Manage Date Sheets">
                    <i class="fa fa-calendar"></i> Schedules
                </a>
                <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                    <i class="fa fa-file-excel-o"></i> Excel Import
                </a>
                <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Fill Student Marks">
                    <i class="fa fa-pencil-square-o"></i> Manual Marks
                </a>
                <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                    <i class="fa fa-bar-chart"></i> Reports
                </a>
            </div>
        </div>

        {{-- 2. Fast KPI Summary Filter Pills --}}
        <div class="exam-kpi-strip">
            <div class="kpi-mini-card filter-kpi-btn" data-status-filter="" title="Click to show all exams">
                <div>
                    <div class="kpi-label">Total Exams</div>
                    <div class="kpi-val" id="statTotalVal">{{ $stats['total'] }}</div>
                </div>
                <div class="kpi-icon" style="background:#f1f5f9; color:#002C54;">
                    <i class="fa fa-leanpub"></i>
                </div>
            </div>
            <div class="kpi-mini-card kpi-classes filter-kpi-btn" data-status-filter="assigned" title="Click to show exams with assigned classes">
                <div>
                    <div class="kpi-label">Assigned Classes</div>
                    <div class="kpi-val" id="statAssignedVal" style="color:#0284c7;">{{ $stats['assigned'] }}</div>
                </div>
                <div class="kpi-icon" style="background:#e0f2fe; color:#0284c7;">
                    <i class="fa fa-tags"></i>
                </div>
            </div>
            <div class="kpi-mini-card kpi-published filter-kpi-btn" data-status-filter="published" title="Click to filter published exams">
                <div>
                    <div class="kpi-label">Published Results</div>
                    <div class="kpi-val" id="statPublishedVal" style="color:#15803d;">{{ $stats['published'] }}</div>
                </div>
                <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="kpi-mini-card kpi-pending filter-kpi-btn" data-status-filter="pending" title="Click to filter pending publication">
                <div>
                    <div class="kpi-label">Pending Publication</div>
                    <div class="kpi-val" id="statPendingVal" style="color:#b45309;">{{ $stats['pending'] }}</div>
                </div>
                <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
        </div>

        {{-- 3. Unified Dark Navy ERP Table Card --}}
        <div class="admission-table-card">
            
            {{-- Card Header Bar --}}
            <div class="dash-card-header">
                <div class="dash-card-title">
                    <i class="fa fa-table text-info mr-1"></i> Examination Records List
                </div>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <span class="badge-total-records">
                        Total <strong id="visibleExamCount" class="text-white">{{ $totalCount }}</strong> Exams
                    </span>
                </div>
            </div>

            {{-- Scrollable Table Viewport --}}
            <div class="table-scroll-container">
                
                {{-- Fast AJAX Loading Spinner Overlay --}}
                <div class="table-loading-overlay" id="tableLoadingOverlay">
                    <div class="table-loading-spinner">
                        <i class="fa fa-spinner fa-spin"></i> Loading examination records...
                    </div>
                </div>

                <table class="dash-table" id="examDataTable">
                    <thead>
                        {{-- Row 1: Header Titles (Always Sticky at Top: 0) --}}
                        <tr class="header-titles-row">
                            <th style="width: 50px; text-align: center;">S.No.</th>
                            <th style="text-align: left; padding-left: 10px; min-width: 220px;">Exam Name &amp; Date</th>
                            <th style="text-align: left; padding-left: 10px; width: 130px;">Exam Term</th>
                            <th style="text-align: left; padding-left: 10px; min-width: 260px;">Assigned Classes &amp; Schedules</th>
                            <th style="width: 150px; text-align: center;">Result Status</th>
                            <th class="fixed_action_head" style="width: 175px; text-align: center;">Actions</th>
                        </tr>

                        {{-- Row 2: In-Column Dark Navy Excel Filters (Sticky Top: 36px) --}}
                        <tr class="excel-filter-row">
                            <th style="text-align: center; color: rgba(255,255,255,0.6); font-weight: normal;">#</th>
                            
                            {{-- Filter: Exam Name --}}
                            <th>
                                <input type="text" class="excel-col-filter col-filter-name" placeholder="Filter exam name..." autocomplete="off">
                            </th>

                            {{-- Filter: Exam Term --}}
                            <th>
                                <select class="excel-col-filter col-filter-term">
                                    <option value="">All Terms</option>
                                    @foreach($examTerms ?? [] as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </th>

                            {{-- Filter: Assigned Class --}}
                            <th>
                                <select class="excel-col-filter col-filter-class">
                                    <option value="">All Classes</option>
                                    @foreach($classType ?? [] as $cl)
                                        <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                    @endforeach
                                </select>
                            </th>

                            {{-- Filter: Status --}}
                            <th>
                                <select class="excel-col-filter col-filter-status" id="selectStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="published">Published</option>
                                    <option value="pending">Pending</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="unassigned">Unassigned</option>
                                </select>
                            </th>

                            {{-- Filter: Reset --}}
                            <th class="fixed_action_filter">
                                <button type="button" class="btn-reset-filters" id="btnResetFilters" title="Reset all column filters">
                                    <i class="fa fa-refresh mr-1"></i> Reset
                                </button>
                            </th>
                        </tr>
                    </thead>

                    <tbody id="examTableBody">
                        @include('examination.offline_exam.exam.table_rows', ['data' => $data, 'startIndex' => $startIndex, 'permission' => $permission])
                    </tbody>
                </table>
            </div>

            {{-- 4. Pinned Bottom Pagination Toolbar (1:1 with admissionView) --}}
            <div class="table-pagination-bar">
                <div class="pagination-info">
                    Showing <strong id="pageStart" class="text-white">{{ $totalCount === 0 ? 0 : ($startIndex + 1) }}</strong> to <strong id="pageEnd" class="text-white">{{ min($startIndex + count($data), $totalCount) }}</strong> of <strong id="totalVisibleExams" class="text-white">{{ $totalCount }}</strong> records
                </div>
                <div class="pagination-controls">
                    <div class="rows-per-page-selector">
                        <label for="perPageSelect">Rows per page:</label>
                        <select id="perPageSelect">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                            <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                    <div class="pagination-nav">
                        <button type="button" class="page-btn" id="btnFirstPage" {{ $currentPage <= 1 ? 'disabled' : '' }} title="First Page"><i class="fa fa-angle-double-left"></i></button>
                        <button type="button" class="page-btn" id="btnPrevPage" {{ $currentPage <= 1 ? 'disabled' : '' }} title="Previous Page"><i class="fa fa-angle-left"></i></button>
                        <span class="page-current-indicator">
                            Page <strong id="currentPageNum" class="text-white">{{ $currentPage }}</strong> of <strong id="totalPageNum" class="text-white">{{ $lastPage }}</strong>
                        </span>
                        <button type="button" class="page-btn" id="btnNextPage" {{ $currentPage >= $lastPage ? 'disabled' : '' }} title="Next Page"><i class="fa fa-angle-right"></i></button>
                        <button type="button" class="page-btn" id="btnLastPage" {{ $currentPage >= $lastPage ? 'disabled' : '' }} title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Container for dynamically rendered publish modals (Centered) --}}
<div id="dynamicModalsContainer">
    @include('examination.offline_exam.exam.modals', ['data' => $data])
</div>

{{-- Centered Copy Exam Modal --}}
<div class="modal fade" id="copyExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 4px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
            <form id="formCopyExam" action="{{ url('copy/exam') }}" method="post">
                @csrf
                <div class="modal-header-navy">
                    <div>
                        <h5 class="modal-title">
                            <i class="fa fa-copy text-info"></i> Duplicate Examination
                        </h5>
                        <span class="modal-subtitle">Offline Examination Setup &bull; Clone Structure</span>
                    </div>
                    <button type="button" class="theme-delete-close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-3" style="background:#ffffff;">
                    <input type="hidden" id="copy_exam_id" name="exam_id">
                    
                    <div class="form-group mb-2">
                        <label style="font-size: 11.5px; font-weight: 700; color: #334155;">New Exam Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="copy_exam_name" name="name" maxlength="255" required style="height: 30px; font-size: 12px; border-radius: 2px; border: 1px solid #cbd5e1;">
                    </div>
                    
                    <div class="form-group mb-2">
                        <label style="font-size: 11.5px; font-weight: 700; color: #334155;">Exam Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="copy_exam_date" name="exam_date" required style="height: 30px; font-size: 12px; border-radius: 2px; border: 1px solid #cbd5e1;">
                    </div>
                    
                    <div class="alert alert-info py-2 px-3 mb-0 mt-2" style="font-size: 10.5px; border-radius: 2px; border-left: 3px solid #0284c7; background: #f0f9ff; color: #0369a1;">
                        <i class="fa fa-info-circle mr-1"></i> The new duplicated exam will inherit assigned classes and schedules. Marks will remain unpopulated for fresh scoring.
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-end" style="border-top: 1px solid #e2e8f0; gap: 8px;">
                    <button type="button" class="theme-btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-navy-submit" id="btnSubmitCopy">
                        <i class="fa fa-copy"></i> Duplicate Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Standard ERP Theme Centered Delete Confirmation Modal --}}
<div class="modal fade" id="deleteExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-delete-modal-dialog" role="document">
        <div class="modal-content theme-delete-modal-content">
            <div class="modal-header theme-delete-modal-header">
                <div class="theme-delete-title-box">
                    <div class="theme-delete-icon">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="theme-delete-headings">
                        <h5 class="modal-title theme-delete-title">Delete Confirmation</h5>
                        <span class="theme-delete-subtitle">Examination Hub &bull; Permanent Exam Removal</span>
                    </div>
                </div>
                <button type="button" class="theme-delete-close" data-dismiss="modal" aria-label="Close" title="Cancel">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="formDeleteExam" action="{{ url('delete/exam') }}" method="POST">
                @csrf
                <input type="hidden" name="delete_id" id="delete_exam_id" value="">
                
                <div class="modal-body theme-delete-body text-center">
                    <div class="theme-delete-avatar">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p class="theme-delete-prompt">Are you sure you want to permanently delete this examination record?</p>
                    <h6 class="theme-delete-item-title" id="del_modal_title">-</h6>
                    
                    <div class="theme-delete-info-card">
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-bookmark-o mr-1 text-primary"></i> Exam Term:</span>
                            <span class="badge badge-secondary" id="del_modal_term" style="font-size:10.5px;">-</span>
                        </div>
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-graduation-cap mr-1 text-primary"></i> Assigned Classes:</span>
                            <strong class="theme-delete-info-val" id="del_modal_classes">-</strong>
                        </div>
                        <div class="theme-delete-info-row border-0 pb-0">
                            <span class="theme-delete-info-label"><i class="fa fa-calendar mr-1 text-primary"></i> Exam Date:</span>
                            <span class="theme-delete-info-val" id="del_modal_date">-</span>
                        </div>
                    </div>

                    <div class="theme-delete-alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span><strong>Warning:</strong> This action cannot be undone. Associated schedules, student marks, and published results will be permanently removed.</span>
                    </div>
                </div>

                <div class="theme-delete-footer">
                    <button type="button" class="theme-btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="theme-btn-confirm-delete" id="btnConfirmDelete">
                        <i class="fa fa-trash mr-1"></i> Delete Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    // Server-Side Real-Time Pagination & Filter State
    var currentPage = {{ $currentPage }};
    var perPage = "{{ $perPage }}";
    var lastPage = {{ $lastPage }};
    var searchTimer = null;

    // Toastr notification helper
    function notify(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
            if (type === 'success') toastr.success(message);
            else if (type === 'error') toastr.error(message);
            else toastr.info(message);
        } else {
            alert(message);
        }
    }

    // Bind Delete Modal Data (Delegated to handle dynamic rows)
    $(document).on('click', '.deleteExamBtn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name') || 'Unnamed Exam';
        var term = $(this).data('term') || 'General';
        var classes = $(this).data('classes') || 'None';
        var dateVal = $(this).data('date') || '-';

        $('#delete_exam_id').val(id);
        $('#del_modal_title').text(name);
        $('#del_modal_term').text(term);
        $('#del_modal_classes').text(classes);
        $('#del_modal_date').text(dateVal);

        $('#deleteExamModal').modal('show');
    });

    // AJAX Delete Submission for instant, quick response
    $('#formDeleteExam').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btnConfirmDelete');
        var originalBtnHtml = submitBtn.html();
        var examId = $('#delete_exam_id').val();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Deleting...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                $('#deleteExamModal').modal('hide');

                if (res.status === 'success' || res.status === 1) {
                    notify('success', res.message || 'Exam deleted successfully.');
                    
                    // Instant optimistic DOM removal with smooth fade
                    var row = $('tr.exam-row[data-id="' + examId + '"]');
                    if (row.length) {
                        row.fadeOut(250, function() {
                            $(this).remove();
                            fetchExams(currentPage);
                        });
                    } else {
                        fetchExams(currentPage);
                    }
                } else {
                    notify('error', res.message || 'Failed to delete exam.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                var errMessage = 'An error occurred while deleting the exam.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMessage = xhr.responseJSON.message;
                }
                notify('error', errMessage);
            }
        });
    });

    // Bind Copy Modal Data (Delegated)
    $(document).on('click', '.copyExamData', function(e) {
        e.preventDefault();
        $('#copy_exam_id').val($(this).data('id'));
        $('#copy_exam_name').val($(this).data('name'));
        $('#copy_exam_date').val($(this).data('date'));
        $('#copyExamModal').modal('show');
    });

    // AJAX Copy Submission for fast response without full reload
    $('#formCopyExam').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = $('#btnSubmitCopy');
        var originalBtnHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Duplicating...');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                $('#copyExamModal').modal('hide');

                if (res.status === 'success' || res.status === 1) {
                    notify('success', res.message || 'Exam duplicated successfully.');
                    fetchExams(1);
                } else {
                    notify('error', res.message || 'Failed to duplicate exam.');
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false).html(originalBtnHtml);
                var errMessage = 'An error occurred while duplicating the exam.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMessage = xhr.responseJSON.message;
                }
                notify('error', errMessage);
            }
        });
    });

    // AJAX 1-Click Toggle for Result Publication inside centered modal
    $(document).on('click', '.btn-ajax-toggle-publish', function(e) {
        e.preventDefault();
        var btn = $(this);
        var examId = btn.data('exam-id');
        var classTypeId = btn.data('class-type-id');
        var actionUrl = btn.data('action-url');
        var originalHtml = btn.html();

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                exam_id: examId,
                class_type_id: classTypeId
            },
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                btn.prop('disabled', false);
                if (res.status === 'success') {
                    notify('success', res.message);
                    fetchExams(currentPage);
                } else {
                    btn.html(originalHtml);
                    notify('error', res.message || 'Publication status update failed.');
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalHtml);
                notify('error', 'Network error while updating result publication.');
            }
        });
    });

    // Core Fetch Function for Server-Side Filtering & Pagination
    function fetchExams(page) {
        if (page !== undefined) {
            currentPage = page;
        }

        var nameVal = $('.col-filter-name').val();
        var termVal = $('.col-filter-term').val();
        var classVal = $('.col-filter-class').val();
        var statusVal = $('.col-filter-status').val();

        $('#tableLoadingOverlay').css('display', 'flex');

        $.ajax({
            url: "{{ url('view/exam') }}",
            type: "GET",
            data: {
                name: nameVal,
                term_id: termVal,
                class_type_id: classVal,
                status: statusVal,
                page: currentPage,
                per_page: perPage
            },
            dataType: "json",
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                $('#tableLoadingOverlay').hide();
                if (res.status === 'success') {
                    // Update table rows & dynamic modals
                    $('#examTableBody').html(res.html);
                    $('#dynamicModalsContainer').html(res.modals_html);

                    // Update pagination state
                    currentPage = res.current_page;
                    lastPage = res.last_page;
                    perPage = res.per_page;

                    $('#pageStart').text(res.from);
                    $('#pageEnd').text(res.to);
                    $('#totalVisibleExams').text(res.total);
                    $('#visibleExamCount').text(res.total);
                    $('#currentPageNum').text(res.current_page);
                    $('#totalPageNum').text(res.last_page);

                    // Update Pagination button states
                    $('#btnFirstPage, #btnPrevPage').prop('disabled', res.current_page <= 1);
                    $('#btnNextPage, #btnLastPage').prop('disabled', res.current_page >= res.last_page || res.total === 0);

                    // Update KPI counters
                    if (res.stats) {
                        $('#statTotalVal').text(res.stats.total);
                        $('#statAssignedVal').text(res.stats.assigned);
                        $('#statPublishedVal').text(res.stats.published);
                        $('#statPendingVal').text(res.stats.pending);
                    }
                }
            },
            error: function() {
                $('#tableLoadingOverlay').hide();
                notify('error', 'Failed to retrieve examination records.');
            }
        });
    }

    // Debounced Search on Exam Name (300ms)
    $('.col-filter-name').on('input keyup', function(){
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function(){
            fetchExams(1);
        }, 300);
    });

    // Dropdown Filters Change (Term, Class, Status)
    $('.col-filter-term, .col-filter-class, .col-filter-status').on('change', function(){
        fetchExams(1);
    });

    // KPI Cards Click Filter
    $('.filter-kpi-btn').on('click', function(){
        var filterVal = $(this).data('status-filter');
        $('.filter-kpi-btn').removeClass('active-kpi');
        $(this).addClass('active-kpi');
        $('#selectStatusFilter').val(filterVal);
        fetchExams(1);
    });

    // Rows Per Page Selector
    $('#perPageSelect').on('change', function(){
        perPage = $(this).val();
        fetchExams(1);
    });

    // Pagination Nav Controls
    $('#btnFirstPage').on('click', function(){
        if (currentPage > 1) fetchExams(1);
    });

    $('#btnPrevPage').on('click', function(){
        if (currentPage > 1) fetchExams(currentPage - 1);
    });

    $('#btnNextPage').on('click', function(){
        if (currentPage < lastPage) fetchExams(currentPage + 1);
    });

    $('#btnLastPage').on('click', function(){
        if (currentPage < lastPage) fetchExams(lastPage);
    });

    // Reset All Filters
    $('#btnResetFilters').on('click', function(){
        $('.col-filter-name').val('');
        $('.col-filter-term').val('');
        $('.col-filter-class').val('');
        $('.col-filter-status').val('');
        $('.filter-kpi-btn').removeClass('active-kpi');
        fetchExams(1);
    });
});
</script>
@endsection
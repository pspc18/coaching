@php
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(10);
    $currentRoleId = Session::get('role_id');
    $currentTeacherId = Session::get('teacher_id');
    $isStudent = ($currentRoleId == 3);
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $currentPage = $currentPage ?? 1;
    $lastPage = $lastPage ?? 1;
    $perPage = $perPage ?? 25;
@endphp
@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exactly Matching admissionView */
.homework-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.homework-page * {
    box-sizing: border-box;
}
.homework-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner */
.homework-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
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
.homework-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.homework-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #fff;
}
.homework-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
}
.homework-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1.4;
}
.dash-btn-light {
    background: #fff;
    color: #002C54 !important;
    border-color: #fff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: transparent;
    color: #fff !important;
    border-color: rgba(255,255,255,.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #fff !important;
    border-color: #fff;
}

/* Table Card & Header - Dark Navy Unified */
.homework-table-card {
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
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
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
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}

/* Column Header Titles */
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 8px;
    height: 38px;
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

/* Sticky Filter Row */
.excel-filter-row th {
    position: sticky;
    top: 38px;
    background: #08335c;
    color: #ffffff;
    padding: 5px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
    box-sizing: border-box;
}

/* Sticky Action Column */
.fixed_action_head {
    position: sticky !important;
    right: 0;
    z-index: 25 !important;
    background: #002C54 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_filter {
    position: sticky !important;
    right: 0;
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
    background: #f8fafc !important;
}
.dash-table tbody tr:nth-child(even) .fixed_action_col {
    background: #edf2f7 !important;
}
.dash-table tbody tr:hover .fixed_action_col {
    background: #e2e8f0 !important;
}

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 27px;
    padding: 3px 7px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
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

/* Filter Clear / Reset Buttons */
.btn-clear-filters {
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    margin: auto;
    transition: all .15s;
}
.btn-clear-filters:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
}
.btn-reset-filters {
    height: 27px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    cursor: pointer;
    width: 100%;
    transition: all .15s;
}
.btn-reset-filters:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Table Body Rows */
.dash-table tbody td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
}
.dash-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.dash-table tbody tr:nth-child(even) td {
    background: #edf2f7;
}
.dash-table tbody tr:hover td {
    background: #e2e8f0 !important;
}

/* Typography & Badges */
.hw-title-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 200px;
    max-width: 290px;
}
.hw-title-short {
    display: block;
    overflow: hidden;
}
.hw-title-text {
    font-weight: 600;
    color: #0f172a;
    font-size: 11.5px;
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
}
.hw-title-full {
    overflow: hidden;
}
.hw-title-text-full {
    font-weight: 600;
    color: #0f172a;
    font-size: 11.5px;
    line-height: 1.45;
    white-space: normal;
    word-break: break-word;
    display: block;
    padding: 2px 0;
}
.btn-toggle-title {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    margin-top: 2px;
    font-size: 10px;
    font-weight: 600;
    color: #0284c7;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    cursor: pointer;
    line-height: 1.3;
    width: fit-content;
    outline: none;
    transition: all .18s ease-in-out;
}
.btn-toggle-title:hover {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.btn-toggle-title:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
}
.btn-toggle-title .toggle-icon {
    font-size: 10.5px;
    transition: transform .25s ease;
}
.hw-duration-hint {
    font-size: 10px;
    color: #64748b;
}
.badge-hw-type {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    display: inline-block;
    white-space: nowrap;
    letter-spacing: .02em;
    text-transform: uppercase;
}
.badge-type-dpp {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}
.badge-type-worksheet {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-type-pyq {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.badge-type-subjective {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
.badge-type-revision {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
}
.badge-type-general {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

.badge-class {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
}
.badge-section {
    font-size: 9.5px;
    background: #f8fafc;
    color: #475569;
    padding: 1px 4px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    font-weight: 600;
}

.hw-subject-text {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
}
.hw-assigned-by {
    font-size: 11px;
    color: #475569;
    white-space: nowrap;
}
.hw-date-label {
    font-size: 11px;
    color: #1e293b;
    display: block;
}

.badge-hw-status {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    width: fit-content;
    white-space: nowrap;
}
.status-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.status-today {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.status-overdue {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Attachment & Submission Badges */
.hw-file-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    color: #0284c7 !important;
    text-decoration: none !important;
    transition: all .15s;
}
.hw-file-chip:hover {
    background: #f0f7ff;
    border-color: #0284c7;
}
.hw-file-chip .file-ext {
    font-size: 9px;
    color: #475569;
}

.hw-submission-count {
    display: inline-flex;
    align-items: center;
    padding: 2px 7px;
    background: #f0fdf4;
    color: #15803d !important;
    border: 1px solid #bbf7d0;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s;
}
.hw-submission-count:hover {
    background: #dcfce7;
    border-color: #86efac;
}
.hw-submission-readonly {
    background: #f8fafc;
    color: #64748b;
    border-color: #e2e8f0;
}

/* Action Buttons */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.table-btn {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 10.5px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s;
    line-height: 1;
}
.btn-action-view {
    background: #faf5ff;
    color: #7c3aed;
    border-color: #e9d5ff;
}
.btn-action-view:hover {
    background: #7c3aed;
    color: #fff;
}
.btn-action-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #2563eb;
    color: #fff;
}
.btn-action-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #fff;
}
.btn-action-remind {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.btn-action-remind:hover {
    background: #d97706;
    color: #fff;
}
.hw-progress-cell {
    min-width: 80px;
    max-width: 120px;
}
.hw-progress-pct {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
}
.hw-mini-progress {
    height: 4px;
    background: #cbd5e1;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 2px;
}
.hw-mini-progress-bar {
    height: 100%;
    transition: width .3s ease;
}

/* Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    min-height: 34px;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
    font-weight: 500;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 12px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 4px;
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
    padding: 2px 5px;
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
    font-size: 12px;
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
    font-size: 11px;
    font-weight: 600;
    padding: 0 6px;
    color: #f1f5f9;
}

/* Empty State */
#empty-state-row td {
    padding: 0 !important;
    border: none !important;
    background: #eef2f6 !important;
    vertical-align: middle !important;
}
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: calc(100vh - var(--header-height) - 195px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.empty-icon {
    font-size: 36px;
    color: #94a3b8;
    margin-bottom: 10px;
}
.empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.empty-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 380px;
}

/* Loading Overlay */
.homework-table-loading {
    position: relative;
    pointer-events: none;
    opacity: 0.6;
}

/* Redesigned Homework Detail Modal Styling (Matching homework/add ERP Standards) */
.user-modal-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px 2px 0 0;
    padding: 11px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.user-modal-hero-content {
    flex: 1;
    min-width: 0;
    padding-right: 14px;
}
.user-modal-hero .user-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    display: flex;
    align-items: center;
    margin-bottom: 5px;
    color: #93c5fd;
    font-weight: 600;
}
.user-modal-hero .user-kicker i {
    margin-right: 6px;
    font-size: 11px;
}
.user-modal-title-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.user-modal-title-row #modal-hw-type {
    margin-top: 1px;
    margin-right: 8px;
    flex-shrink: 0;
    padding: 2px 7px;
}
.user-modal-title {
    font-size: 13.5px;
    font-weight: 700;
    margin: 0;
    line-height: 1.4;
    color: #ffffff;
    word-break: break-word;
    flex: 1;
}
.user-modal-close {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color: #ffffff;
    width: 27px;
    height: 27px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .15s;
    line-height: 1;
    padding: 0;
    margin-left: 10px;
}
.user-modal-close:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Modal Cards (1:1 with user-card in add.blade.php) */
#homeworkDetailModal .user-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
}
#homeworkDetailModal .user-card-header {
    padding: 6px 12px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
#homeworkDetailModal .user-card-title {
    font-size: 11.5px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
}
#homeworkDetailModal .user-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    display: inline-block;
    margin-right: 8px;
    letter-spacing: .02em;
}
#homeworkDetailModal .user-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
#homeworkDetailModal .user-card-body {
    padding: 8px 10px;
}

/* Spec Grid Cards */
.hw-spec-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 7px 9px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.hw-spec-label {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
}
.hw-spec-label i {
    margin-right: 6px;
    font-size: 11px;
    width: 13px;
    text-align: center;
    flex-shrink: 0;
}
.hw-spec-value {
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.3;
    word-break: break-word;
}
.btn-sub-quick {
    font-size: 10px;
    font-weight: 700;
    color: #0284c7;
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    padding: 2px 8px;
    text-decoration: none !important;
    line-height: 1.2;
    transition: all .15s;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-sub-quick i {
    font-size: 9.5px;
    margin-right: 4px;
}
.btn-sub-quick:hover {
    background: #0284c7;
    color: #ffffff !important;
}

/* Document Upload / Attachment Box */
.hw-modal-doc-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 12px;
}
.hw-doc-icon-wrap {
    font-size: 24px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}
.hw-doc-filename {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    max-width: 380px;
}
.hw-doc-subtext {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
}
.hw-doc-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.dash-btn-navy {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #002C54;
    height: 28px;
    padding: 0 13px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: all .15s;
    cursor: pointer;
    white-space: nowrap;
}
.dash-btn-navy i {
    margin-right: 6px;
    font-size: 11px;
}
.dash-btn-navy:hover {
    background: #001f3d;
    border-color: #001f3d;
    color: #ffffff !important;
}
.dash-btn-outline-navy {
    background: #ffffff;
    color: #002C54 !important;
    border: 1px solid #cbd5e1;
    height: 28px;
    padding: 0 11px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    transition: all .15s;
    cursor: pointer;
    white-space: nowrap;
}
.dash-btn-outline-navy i {
    margin-right: 6px;
    font-size: 11px;
}
.dash-btn-outline-navy:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}
.btn-modal-close {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 28px;
    padding: 0 13px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.btn-modal-close i {
    margin-right: 6px;
    font-size: 11px;
}
.btn-modal-close:hover {
    background: #e2e8f0;
    color: #1e293b !important;
}
.hw-no-doc-box {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 2px;
    padding: 10px 14px;
    font-size: 11px;
    color: #64748b;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hw-no-doc-box i {
    margin-right: 6px;
    font-size: 12px;
}

/* Instructions Content Box */
.hw-instruction-body {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px 12px;
    min-height: 60px;
    max-height: 220px;
    overflow-y: auto;
    font-size: 12px;
    line-height: 1.5;
    color: #1e293b;
}
.hw-instruction-body img {
    max-width: 100%;
    height: auto;
}
.hw-instruction-body p {
    margin-bottom: 6px;
}
.hw-instruction-body p:last-child {
    margin-bottom: 0;
}

/* ==========================================================================
   Standard ERP Theme Delete Confirmation Modal
   ========================================================================== */
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
    padding: 18px 16px !important;
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
    padding: 5px 0;
    border-bottom: 1px solid #edf2f7;
}
.theme-delete-info-label {
    color: #64748b;
    font-weight: 500;
}
.theme-delete-info-val {
    color: #0f172a;
    max-width: 65%;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page homework-page">
    <div class="admission-page-layout homework-page-layout">

        {{-- 1. Top Hero & Action Banner (Matching admissionView) --}}
        <div class="homework-hero">
            <div class="homework-hero-text">
                <span class="homework-kicker"><i class="fa fa-graduation-cap mr-1"></i> Academic Hub</span>
                <h1 class="homework-title"><i class="fa fa-flask mr-1"></i> {{ __('homework.View Homework') }} &amp; DPP</h1>
                <p class="homework-subtitle">Real-time Excel-style grid with category filters, submission tracking &amp; live pagination</p>
            </div>
            <div class="homework-hero-actions">
                @if(!$isStudent && ($permission->add ?? true))
                    <a href="{{ url('homework/add') }}" class="dash-btn dash-btn-light">
                        <i class="fa fa-plus mr-1"></i> Add Homework
                    </a>
                @endif
            </div>
        </div>

        {{-- 2. Full-Height Table Card with Excel In-Column Filters & Pinned Pagination --}}
        <div class="dash-card homework-table-card">
            <div class="dash-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Homework &amp; DPP Grid</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-total-records"><span id="header-records-count">{{ $totalCount }}</span> Total Assignments</span>
                </div>
            </div>

            {{-- Scrollable Table Body --}}
            <div class="table-scroll-container">
                <table class="dash-table" id="homework-grid-table">
                    <thead>
                        {{-- Row 1: Column Headers (Dark Navy) --}}
                        <tr class="header-titles-row">
                            <th style="width: 42px;" class="text-center">#</th>
                            <th style="width: 95px;">Category</th>
                            <th style="min-width: 180px;">Title / Topic</th>
                            <th style="min-width: 100px;">Class / Batch</th>
                            <th style="min-width: 110px;">Subject</th>
                            <th style="min-width: 120px;">Assigned By</th>
                            <th style="min-width: 95px;">Issue Date</th>
                            <th style="min-width: 130px;">Due Date &amp; Status</th>
                            <th style="width: 80px;" class="text-center">Attachment</th>
                            <th style="width: 85px;" class="text-center">Submissions</th>
                            <th style="width: 90px;" class="text-center fixed_action_head">{{ __('common.Action') }}</th>
                        </tr>

                        {{-- Row 2: In-Column Excel Filters --}}
                        <tr class="excel-filter-row">
                            {{-- Clear filter button --}}
                            <th class="text-center">
                                <button type="button" class="btn-clear-filters" title="Reset All Filters">
                                    <i class="fa fa-filter text-danger"></i>
                                </button>
                            </th>

                            {{-- Category / Type Filter --}}
                            <th>
                                <select class="excel-col-filter" id="filter-type">
                                    <option value="">All Types</option>
                                    <option value="DPP" {{ ($search['homework_type'] ?? '') == 'DPP' ? 'selected' : '' }}>DPP</option>
                                    <option value="Worksheet" {{ ($search['homework_type'] ?? '') == 'Worksheet' ? 'selected' : '' }}>Worksheet</option>
                                    <option value="PYQ Sheet" {{ ($search['homework_type'] ?? '') == 'PYQ Sheet' ? 'selected' : '' }}>PYQ Sheet</option>
                                    <option value="Subjective" {{ ($search['homework_type'] ?? '') == 'Subjective' ? 'selected' : '' }}>Subjective</option>
                                    <option value="Revision" {{ ($search['homework_type'] ?? '') == 'Revision' ? 'selected' : '' }}>Revision</option>
                                </select>
                            </th>

                            {{-- Title Filter --}}
                            <th>
                                <input type="text" class="excel-col-filter" id="filter-title" placeholder="Filter title / topic..." value="{{ $search['title'] ?? '' }}">
                            </th>

                            {{-- Class Filter --}}
                            <th>
                                <select class="excel-col-filter" id="filter-class">
                                    <option value="">All Classes</option>
                                    @if(!empty($classType))
                                        @foreach($classType as $type)
                                            <option value="{{ $type->id }}" {{ ($search['class_type_id'] ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name ?? '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </th>

                            {{-- Subject Filter --}}
                            <th>
                                <select class="excel-col-filter" id="filter-subject">
                                    <option value="">All Subjects</option>
                                    @if(!empty($allSubjects))
                                        @foreach($allSubjects as $sub)
                                            <option value="{{ $sub->id }}" {{ ($search['subject'] ?? '') == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->name ?? '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </th>

                            {{-- Assigned By Filter --}}
                            <th>
                                <input type="text" class="excel-col-filter" id="filter-assigned-by" placeholder="Filter teacher..." value="{{ $search['assigned_by'] ?? '' }}">
                            </th>

                            {{-- Issue Date Filter --}}
                            <th>
                                <input type="date" class="excel-col-filter" id="filter-issue-date" value="{{ $search['homework_issue_date'] ?? '' }}">
                            </th>

                            {{-- Due Date / Status Filter --}}
                            <th>
                                <select class="excel-col-filter" id="filter-status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ ($search['status'] ?? '') == 'active' ? 'selected' : '' }}>Active (Ongoing)</option>
                                    <option value="due_today" {{ ($search['status'] ?? '') == 'due_today' ? 'selected' : '' }}>Due Today</option>
                                    <option value="overdue" {{ ($search['status'] ?? '') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </th>

                            {{-- Attachment Filter (N/A) --}}
                            <th class="text-center text-muted" style="font-size: 11px;">-</th>

                            {{-- Submissions Filter (N/A) --}}
                            <th class="text-center text-muted" style="font-size: 11px;">-</th>

                            {{-- Action Filter (Reset Button) --}}
                            <th class="text-center fixed_action_filter">
                                <button type="button" class="btn-reset-filters" title="Reset Filters">
                                    <i class="fa fa-refresh mr-1"></i> Reset
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="homework-table-body">
                        @include('master.home_work.home_work.index_rows')
                    </tbody>
                </table>
            </div>

            {{-- 3. Pinned Bottom Pagination Toolbar (Dark Navy) --}}
            <div class="dash-card-footer table-pagination-bar">
                <div class="pagination-info">
                    Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                </div>
                <div class="pagination-controls">
                    <div class="rows-per-page-selector">
                        <label for="rows-per-page-select">Rows per page:</label>
                        <select id="rows-per-page-select">
                            <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    <div class="pagination-nav">
                        <button type="button" class="page-btn" id="btn-first" title="First Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-double-left"></i></button>
                        <button type="button" class="page-btn" id="btn-prev" title="Previous Page" {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}><i class="fa fa-angle-left"></i></button>
                        <span class="page-current-indicator">Page <span id="current-page">{{ $currentPage ?? 1 }}</span> of <span id="total-pages">{{ $lastPage ?? 1 }}</span></span>
                        <button type="button" class="page-btn" id="btn-next" title="Next Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-right"></i></button>
                        <button type="button" class="page-btn" id="btn-last" title="Last Page" {{ ($currentPage ?? 1) >= ($lastPage ?? 1) ? 'disabled' : '' }}><i class="fa fa-angle-double-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. Redesigned View Homework Details Modal (Matching homework/add ERP Form Structure) --}}
<div class="modal fade" id="homeworkDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
        <div class="modal-content" style="border-radius: 2px; overflow: hidden; border: 1px solid #001f3d; box-shadow: 0 4px 20px rgba(0,0,0,0.18);">
            
            {{-- Modern Navy Hero Header --}}
            <div class="modal-header user-modal-hero">
                <div class="user-modal-hero-content">
                    <span class="user-kicker"><i class="fa fa-graduation-cap"></i> Academic Hub &bull; Homework Preview</span>
                    <div class="user-modal-title-row">
                        <span id="modal-hw-type" class="badge-hw-type">DPP</span>
                        <h5 class="user-modal-title" id="modal-hw-title">Homework Details</h5>
                    </div>
                </div>
                <button type="button" class="user-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            {{-- Modal Body: 3 Structured Cards matching add.blade.php --}}
            <div class="modal-body p-2" style="background: #eef2f6; max-height: calc(85vh - 110px); overflow-y: auto;">
                
                {{-- Card 1: Academic & Batch Details --}}
                <div class="user-card mb-2">
                    <div class="user-card-header py-1 px-2">
                        <h3 class="user-card-title">
                            <span class="card-step">01</span> Academic &amp; Batch Details
                        </h3>
                        <span class="user-card-desc">Target class, subject, category &amp; assignment schedule</span>
                    </div>
                    <div class="user-card-body p-2">
                        <div class="row no-gutters">
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-graduation-cap text-primary"></i> Class / Batch</span>
                                    <span class="hw-spec-value" id="modal-hw-class">-</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-book text-success"></i> Subject</span>
                                    <span class="hw-spec-value" id="modal-hw-subject">-</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-tag text-warning"></i> Category / Type</span>
                                    <span class="hw-spec-value" id="modal-hw-category">-</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-clock-o text-info"></i> Est. Duration / Marks</span>
                                    <span class="hw-spec-value" id="modal-hw-duration">Not Specified</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-user-circle text-secondary"></i> Assigned By</span>
                                    <span class="hw-spec-value" id="modal-hw-creator">-</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-calendar-check-o text-success"></i> Issue Date</span>
                                    <span class="hw-spec-value" id="modal-hw-issue-date">-</span>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-calendar-times-o text-danger"></i> Submission Deadline</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="hw-spec-value text-danger" id="modal-hw-due-date">-</span>
                                        <span id="modal-hw-status-badge" class="badge-hw-status status-active" style="margin-left: 6px;">Active</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3 p-1">
                                <div class="hw-spec-card">
                                    <span class="hw-spec-label"><i class="fa fa-paper-plane text-primary"></i> Submissions</span>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="hw-spec-value text-success font-weight-bold" id="modal-hw-sub-count">0 Submitted</span>
                                        <a href="#" id="modal-hw-sub-link" class="btn-sub-quick" title="View Submissions">
                                            <i class="fa fa-external-link"></i> Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Instructions & Question Details --}}
                <div class="user-card mb-2">
                    <div class="user-card-header py-1 px-2 d-flex align-items-center justify-content-between">
                        <h3 class="user-card-title">
                            <span class="card-step">02</span> Instructions &amp; Question Content
                        </h3>
                        <span class="user-card-desc">Rich text guidelines, problems &amp; formulas</span>
                    </div>
                    <div class="user-card-body p-2">
                        <div id="modal-hw-desc" class="hw-instruction-body">
                            {{-- Decoded HTML from Quill rendered here --}}
                        </div>
                    </div>
                </div>

                {{-- Card 3: Attachment & Resource File --}}
                <div class="user-card mb-0">
                    <div class="user-card-header py-1 px-2">
                        <h3 class="user-card-title">
                            <span class="card-step">03</span> Attachment &amp; Reference Material
                        </h3>
                        <span class="user-card-desc">Attached question PDF, worksheet or study material</span>
                    </div>
                    <div class="user-card-body p-2">
                        {{-- Document Card if file is attached --}}
                        <div id="modal-attachment-wrap" style="display: none;">
                            <div class="hw-modal-doc-box">
                                <div class="d-flex align-items-center" style="min-width: 0; flex: 1;">
                                    <div class="hw-doc-icon-wrap mr-2" id="modal-file-icon">
                                        <i class="fa fa-file-pdf-o text-danger"></i>
                                    </div>
                                    <div class="hw-doc-meta" style="min-width: 0; flex: 1;">
                                        <span class="hw-doc-filename text-truncate d-block" id="modal-hw-file-name" title="Document File">assignment.pdf</span>
                                        <span class="hw-doc-subtext" id="modal-hw-file-ext">Attached Question Document</span>
                                    </div>
                                </div>
                                <div class="hw-doc-actions ml-2">
                                    <a href="#" id="modal-hw-view-btn" target="_blank" class="dash-btn-outline-navy" title="Open in new tab" style="margin-right: 6px;">
                                        <i class="fa fa-external-link"></i> Preview
                                    </a>
                                    <a href="#" id="modal-hw-download-btn" target="_blank" download class="dash-btn-navy" title="Download Document">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Empty fallback if no file attached --}}
                        <div id="modal-no-attachment" class="hw-no-doc-box">
                            <i class="fa fa-paperclip text-muted"></i> No attachment file uploaded for this homework.
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modern Action Footer --}}
            <div class="modal-footer p-2 d-flex align-items-center justify-content-between" style="background: #ffffff; border-top: 1px solid #cbd5e1; border-radius: 0 0 2px 2px;">
                <div class="d-flex align-items-center">
                    <span class="text-muted" style="font-size: 11.5px; display: inline-flex; align-items: center;">
                        <i class="fa fa-info-circle text-info" style="margin-right: 6px; font-size: 12.5px;"></i> Assignment ID: #<strong id="modal-hw-id" style="margin-left: 2px;">-</strong>
                    </span>
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <button type="button" class="btn-modal-close" style="margin-right: 6px;" data-dismiss="modal" data-bs-dismiss="modal" title="Close Modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                    <a href="#" id="modal-footer-submissions-btn" class="dash-btn-navy" title="View Submissions">
                        <i class="fa fa-users"></i> View Submissions
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- 5. Delete Confirmation Modal (Standard ERP Theme) --}}
<div class="modal fade" id="homeworkDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-delete-modal-dialog" role="document">
        <div class="modal-content theme-delete-modal-content">
            <div class="modal-header theme-delete-modal-header">
                <div class="theme-delete-title-box">
                    <div class="theme-delete-icon">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="theme-delete-headings">
                        <h5 class="modal-title theme-delete-title">Delete Confirmation</h5>
                        <span class="theme-delete-subtitle">Academic Hub &bull; Permanent Homework Removal</span>
                    </div>
                </div>
                <button type="button" class="theme-delete-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Cancel">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form action="{{ url('homework/delete') }}" method="POST">
                @csrf
                <input type="hidden" name="delete_id" id="delete_homework_id" value="">
                
                <div class="modal-body theme-delete-body text-center">
                    <div class="theme-delete-avatar">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p class="theme-delete-prompt">Are you sure you want to permanently delete this assignment?</p>
                    <h6 class="theme-delete-item-title" id="del_modal_title">-</h6>
                    
                    <div class="theme-delete-info-card">
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-tag mr-1 text-primary"></i> Category / Type:</span>
                            <span class="badge" id="del_modal_type">-</span>
                        </div>
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-graduation-cap mr-1 text-primary"></i> Class &amp; Subject:</span>
                            <strong class="theme-delete-info-val" id="del_modal_class_sub">-</strong>
                        </div>
                        <div class="theme-delete-info-row border-0 pb-0">
                            <span class="theme-delete-info-label"><i class="fa fa-calendar mr-1 text-primary"></i> Issue Date:</span>
                            <span class="theme-delete-info-val" id="del_modal_date">-</span>
                        </div>
                    </div>

                    <div class="theme-delete-alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span><strong>Warning:</strong> This action cannot be undone. Associated student submissions and attached document files will be permanently erased.</span>
                    </div>
                </div>

                <div class="modal-footer theme-delete-footer">
                    <button type="button" class="theme-btn-cancel" data-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fa fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="theme-btn-confirm-delete">
                        <i class="fa fa-trash mr-1"></i> Yes, Delete Homework
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var homeworkAjaxUrl = "{{ url('homework/index') }}";
    var currentPage = parseInt("{{ $currentPage ?? 1 }}", 10);
    var lastPage = parseInt("{{ $lastPage ?? 1 }}", 10);
    var currentPerPage = parseInt("{{ $perPage ?? 25 }}", 10);
    var isFetching = false;
    var filterTimer = null;

    function getFilterParams(page) {
        return {
            ajax: 1,
            page: page || 1,
            per_page: currentPerPage,
            homework_type: $('#filter-type').val(),
            title: $('#filter-title').val().trim(),
            class_type_id: $('#filter-class').val(),
            subject: $('#filter-subject').val(),
            assigned_by: $('#filter-assigned-by').val().trim(),
            homework_issue_date: $('#filter-issue-date').val(),
            status: $('#filter-status').val()
        };
    }

    function fetchHomework(page) {
        if (isFetching) return;
        isFetching = true;
        $('#homework-grid-table').addClass('homework-table-loading');

        var params = getFilterParams(page);

        $.ajax({
            url: homeworkAjaxUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.html !== undefined)) {
                    $('#homework-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    $('#page-start').text(res.from || 0);
                    $('#page-end').text(res.to || 0);
                    $('#total-records').text(res.total || 0);
                    $('#header-records-count').text(res.total || 0);
                    $('#current-page').text(res.current_page || 1);
                    $('#total-pages').text(res.last_page || 1);

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);
                }
            },
            error: function(xhr) {
                console.error('Failed to fetch homework data:', xhr);
            },
            complete: function() {
                isFetching = false;
                $('#homework-grid-table').removeClass('homework-table-loading');
            }
        });
    }

    // Debounced typing for text filters (350ms)
    $('#filter-title, #filter-assigned-by').on('input', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchHomework(1);
        }, 350);
    });

    // Instant filter on dropdowns and dates
    $('#filter-type, #filter-class, #filter-subject, #filter-issue-date, #filter-status').on('change', function() {
        currentPage = 1;
        fetchHomework(1);
    });

    // Rows per page selector
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchHomework(1);
    });

    // Pagination navigation buttons
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            fetchHomework(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            fetchHomework(currentPage - 1);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            fetchHomework(currentPage + 1);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            fetchHomework(lastPage);
        }
    });

    // Clear and Reset filters
    function resetAllFilters() {
        $('#filter-type').val('');
        $('#filter-title').val('');
        $('#filter-class').val('');
        $('#filter-subject').val('');
        $('#filter-assigned-by').val('');
        $('#filter-issue-date').val('');
        $('#filter-status').val('');
        currentPage = 1;
        fetchHomework(1);
    }

    $('.btn-clear-filters, .btn-reset-filters').on('click', function() {
        resetAllFilters();
    });

    // Title Show/Hide Slide Toggle with Smooth Animation
    $(document).on('click', '.btn-toggle-title', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var $cell = $btn.closest('.hw-title-cell');
        var $short = $cell.find('.hw-title-short');
        var $full = $cell.find('.hw-title-full');
        var isExpanded = $btn.data('expanded') === true;

        if (isExpanded) {
            $full.slideUp(220, function() {
                $short.slideDown(150);
            });
            $btn.data('expanded', false);
            $btn.find('.toggle-text').text('Show More');
            $btn.find('.toggle-icon').removeClass('fa-angle-up').addClass('fa-angle-down');
        } else {
            $short.slideUp(150, function() {
                $full.slideDown(220);
            });
            $btn.data('expanded', true);
            $btn.find('.toggle-text').text('Show Less');
            $btn.find('.toggle-icon').removeClass('fa-angle-down').addClass('fa-angle-up');
        }
    });

    // View Details Modal Trigger
    $(document).on('click', '.viewHomeworkBtn', function() {
        var id = $(this).data('id') || '';
        var title = $(this).data('title') || 'Homework';
        var displayTitle = $(this).data('display-title') || title;
        var type = $(this).data('type') || 'Homework';
        var badgeClass = $(this).data('badge-class') || 'badge-type-general';
        var encodedDesc = $(this).data('description') || '';
        var className = $(this).data('class') || 'N/A';
        var sectionName = $(this).data('section') || '';
        var classDisplay = className + (sectionName ? ' [' + sectionName + ']' : '');
        var subject = $(this).data('subject') || 'N/A';
        var targetDuration = $(this).data('target-duration') || '';
        var issueDate = $(this).data('issue-date') || '-';
        var dueDate = $(this).data('submission-date') || '-';
        var isOverdue = $(this).data('is-overdue') == '1';
        var isToday = $(this).data('is-today') == '1';
        var createdBy = $(this).data('created-by') || 'Admin';
        var contentFile = $(this).data('content-file') || '';
        var fileName = $(this).data('file-name') || '';
        var submissionCount = $(this).data('submission-count') || 0;
        var submissionsUrl = $(this).data('submissions-url') || '#';

        var decodedDesc = '';
        try {
            decodedDesc = decodeURIComponent(escape(window.atob(encodedDesc)));
        } catch(e) {
            try {
                decodedDesc = window.atob(encodedDesc);
            } catch(e2) {
                decodedDesc = encodedDesc;
            }
        }

        $('#modal-hw-id').text(id);
        $('#modal-hw-title').text(displayTitle);
        $('#modal-hw-type').text(type).attr('class', 'badge-hw-type mr-2 ' + badgeClass);
        $('#modal-hw-category').text(type);
        $('#modal-hw-class').text(classDisplay);
        $('#modal-hw-subject').text(subject);
        $('#modal-hw-duration').text(targetDuration ? targetDuration : 'Not Specified');
        $('#modal-hw-creator').text(createdBy);
        $('#modal-hw-issue-date').text(issueDate);
        $('#modal-hw-due-date').text(dueDate);

        // Status badge
        var $statusBadge = $('#modal-hw-status-badge');
        if (isOverdue) {
            $statusBadge.attr('class', 'badge-hw-status status-overdue ml-1').text('Overdue');
        } else if (isToday) {
            $statusBadge.attr('class', 'badge-hw-status status-today ml-1').text('Due Today');
        } else {
            $statusBadge.attr('class', 'badge-hw-status status-active ml-1').text('Active');
        }

        // Submissions
        $('#modal-hw-sub-count').text(submissionCount + ' Submitted');
        $('#modal-hw-sub-link').attr('href', submissionsUrl);
        $('#modal-footer-submissions-btn').attr('href', submissionsUrl);

        // Description
        $('#modal-hw-desc').html(decodedDesc || '<p class="text-muted font-italic mb-0">No instructions or content provided.</p>');

        // Attachment Card
        if (contentFile && contentFile.length > 0 && fileName) {
            $('#modal-hw-file-name').text(fileName).attr('title', fileName);
            $('#modal-hw-download-btn').attr('href', contentFile);
            $('#modal-hw-view-btn').attr('href', contentFile);

            var ext = fileName.split('.').pop().toLowerCase();
            var iconHtml = '<i class="fa fa-file-text-o text-info"></i>';
            var extLabel = ext.toUpperCase() + ' Document';

            if (ext === 'pdf') {
                iconHtml = '<i class="fa fa-file-pdf-o text-danger"></i>';
                extLabel = 'PDF Document';
            } else if (ext === 'doc' || ext === 'docx') {
                iconHtml = '<i class="fa fa-file-word-o text-primary"></i>';
                extLabel = 'Word Document';
            } else if (['jpg', 'jpeg', 'png', 'webp', 'gif'].indexOf(ext) !== -1) {
                iconHtml = '<i class="fa fa-file-image-o text-success"></i>';
                extLabel = 'Image File';
            }
            $('#modal-file-icon').html(iconHtml);
            $('#modal-hw-file-ext').text(extLabel);

            $('#modal-attachment-wrap').show();
            $('#modal-no-attachment').hide();
        } else {
            $('#modal-attachment-wrap').hide();
            $('#modal-no-attachment').show();
        }

        $('#homeworkDetailModal').modal('show');
    });

    // Delete Modal Trigger (Theme Aligned)
    $(document).on('click', '.deleteHwBtn', function() {
        var id = $(this).data('id');
        var title = $(this).data('title') || 'This Homework';
        var type = $(this).data('type') || 'Homework';
        var badgeClass = $(this).data('badge-class') || 'badge-type-general';
        var classSub = $(this).data('class-sub') || '-';
        var date = $(this).data('date') || '-';

        $('#delete_homework_id').val(id);
        $('#del_modal_title').text(title);
        $('#del_modal_type').text(type).attr('class', 'badge ' + badgeClass);
        $('#del_modal_class_sub').text(classSub);
        $('#del_modal_date').text(date);

        $('#homeworkDeleteModal').modal('show');
    });

    // Send WhatsApp Reminder to Pending Defaulters
    $(document).on('click', '.btnRemindDefaulters', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var title = $btn.data('title') || 'this homework';
        var className = $btn.data('class') || 'Class';
        var pendingCount = $btn.data('pending') || 0;

        if (!confirm('Are you sure you want to dispatch WhatsApp reminders to ' + pendingCount + ' pending student(s) of ' + className + ' for "' + title + '"?')) {
            return;
        }

        var origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ url('homework/remind-defaulters') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                homework_id: id
            },
            dataType: "json",
            success: function(res) {
                $btn.prop('disabled', false).html(origHtml);
                if (res.status === 'success' || res.status === true) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(res.message, 'Reminders Queued');
                    } else {
                        alert(res.message);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.warning(res.message, 'Notice');
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to send reminders. Please try again.';
                if (typeof toastr !== 'undefined') {
                    toastr.error(err, 'Error');
                } else {
                    alert(err);
                }
            }
        });
    });
});
</script>
@endsection
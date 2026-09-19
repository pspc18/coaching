@extends('layout.app')

@php
    $studentCount = $totalCount ?? count($data ?? []);
    $selectedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedStatus = (int) ($search['student_status'] ?? 1);
    $selectedCredStatus = $search['credential_status'] ?? '';
    $stats = $credentialStats ?? [
        'total' => $studentCount,
        'generated' => 0,
        'pending' => 0,
    ];
    $downloadParams = [
        'class_type_id' => $selectedClassId > 0 ? $selectedClassId : '',
        'student_status' => $selectedStatus,
    ];
@endphp

@section('styles')
<style>
/* Page Layout & Viewport Fitting */
.credential-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.credential-page * {
    box-sizing: border-box;
}
.credential-page-layout {
    height: calc(100vh - var(--header-height) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Hero Banner (Arise ERP Dark Navy Theme) */
.credential-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.credential-hero-text {
    display: flex;
    flex-direction: column;
}
.credential-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.credential-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
}
.credential-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Quick Summary Stat Badges */
.credential-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}

/* Hero Action Buttons */
.credential-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn:disabled, .dash-btn[disabled] {
    opacity: .45;
    cursor: not-allowed;
    pointer-events: none;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #ffffff;
    border-color: rgba(255,255,255,.6);
}
.dash-btn-pdf {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}
.dash-btn-pdf:hover {
    background: #b91c1c;
    color: #ffffff;
}
.dash-btn-excel {
    background: #059669;
    color: #ffffff;
    border-color: #059669;
}
.dash-btn-excel:hover {
    background: #047857;
    color: #ffffff;
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

/* Table Card Container */
.credential-table-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.dash-card-header {
    background: #002342;
    color: #ffffff;
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    min-height: 35px;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    letter-spacing: .02em;
}
.selection-counter-badge {
    background: #0284c7;
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 2px;
    display: none;
}
.badge-total-records {
    font-size: 10.5px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 2px;
}

/* Scrollable Table Area */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #eef2f6;
    position: relative;
}
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}

/* Sticky Double Header */
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}
.header-titles-row th {
    background: #002C54;
    color: #ffffff;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 7px 8px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 1px solid #001f3d;
    white-space: nowrap;
    vertical-align: middle;
}
.header-filters-row th {
    background: #051e38;
    padding: 3px 5px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #00172e;
    vertical-align: middle;
}

/* In-Column Excel Filters */
.excel-col-filter {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 10.5px;
    border: 1px solid rgba(255,255,255,.25);
    background: #021629;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    color-scheme: dark;
}
.excel-col-filter::placeholder {
    color: rgba(255,255,255,.55);
}
.excel-col-filter:focus {
    background: #010d18 !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
}
select.excel-col-filter {
    cursor: pointer;
}
select.excel-col-filter option {
    background: #002C54;
    color: #ffffff;
}

/* Action Header & Reset */
.fixed_action_filter {
    position: sticky !important;
    right: 0;
    z-index: 22 !important;
    background: #051e38 !important;
    box-shadow: -3px 0 6px rgba(0,0,0,.15);
}
.fixed_action_col {
    position: sticky !important;
    right: 0;
    z-index: 5;
    box-shadow: -3px 0 6px rgba(0,0,0,.08);
}
.btn-reset-filters {
    height: 25px;
    padding: 0 7px;
    font-size: 10.5px;
    font-weight: 600;
    background: #021629;
    color: #cbd5e1;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.btn-reset-filters:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}

/* Table Body Rows - Soft Slate Palette */
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
.dash-table tbody tr.row-cred-pending td {
    background: #fdfbf7;
}
.dash-table tbody tr.row-cred-pending:hover td {
    background: #f8f3e8 !important;
}

/* Student Information Styling */
.student-info-box {
    display: flex;
    flex-direction: column;
}
.student-name-text {
    font-weight: 650;
    color: #0f172a;
    font-size: 12px;
    line-height: 1.25;
}
.student-sub-info {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}
.badge-adm-no, .badge-unique-id {
    font-size: 9.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
}

/* Badges */
.badge-class {
    display: inline-block;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 1px 6px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
}
.guardian-name {
    color: #334155;
    font-size: 11.5px;
}

/* Mobile & WhatsApp Cell */
.mobile-cell-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.mobile-text {
    font-weight: 500;
    color: #1e293b;
    font-family: ui-monospace, SFMono-Regular, monospace;
    font-size: 11px;
}
.btn-row-wa {
    width: 22px;
    height: 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #25d366;
    color: #ffffff !important;
    border-radius: 2px;
    font-size: 12px;
    text-decoration: none !important;
    transition: transform .15s, background .15s;
}
.btn-row-wa:hover {
    background: #128c7e;
    transform: scale(1.1);
}

/* Credentials Values with Copy & Reveal */
.cred-cell-wrap {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.cred-pill {
    display: inline-block;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 2px;
    letter-spacing: .02em;
}
.cred-pill-user {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.cred-pill-pass {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.cred-pill-empty {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 10px;
    font-style: italic;
}
.btn-copy-icon, .btn-toggle-eye {
    width: 22px;
    height: 22px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #64748b;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10.5px;
    transition: all .15s;
    padding: 0;
}
.btn-copy-icon:hover, .btn-toggle-eye:hover {
    background: #f1f5f9;
    color: #0284c7;
    border-color: #94a3b8;
}
.btn-copy-icon.copied {
    background: #10b981 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
}

/* Status Badges */
.badge-status-pill {
    display: inline-flex;
    align-items: center;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 2px;
    white-space: nowrap;
}
.badge-status-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.badge-status-pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}

/* Action Buttons in Row */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.table-btn {
    width: 26px;
    height: 24px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    font-size: 11px;
    text-decoration: none !important;
    transition: all .15s;
}
.btn-action-copy-both:hover {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #38bdf8;
}
.btn-action-wa {
    color: #25d366;
}
.btn-action-wa:hover {
    background: #25d366;
    color: #ffffff;
    border-color: #25d366;
}
.btn-action-view:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Table Loading State during AJAX */
.credential-table-loading {
    opacity: 0.55;
    pointer-events: none;
    transition: opacity 0.15s ease-in-out;
}

/* Centered Empty State */
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
    min-height: calc(100vh - var(--header-height) - 190px);
    padding: 40px 16px;
    text-align: center;
    color: #475569;
    background: #eef2f6;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 44px;
    color: #94a3b8;
    margin-bottom: 12px;
    line-height: 1;
}
.dash-empty-state .empty-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.dash-empty-state .empty-desc {
    font-size: 12px;
    color: #64748b;
    max-width: 380px;
    line-height: 1.5;
}

/* Pinned Bottom Pagination Toolbar */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 38px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    border-top: 1px solid rgba(255,255,255,.12);
}
.pagination-info {
    font-size: 11.5px;
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

/* ==========================================================================
   ARISE ERP - GENERATE CREDENTIALS MODAL THEME
   ========================================================================== */
.gen-modal-dialog {
    max-width: 470px;
    margin: 1.75rem auto;
}
.gen-modal-content {
    background: #ffffff !important;
    border-radius: 3px !important;
    border: 1px solid rgba(0, 44, 84, 0.2) !important;
    box-shadow: 0 15px 35px -5px rgba(0, 44, 84, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
    overflow: hidden !important;
}

/* Modal Header */
.gen-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 10px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 52px;
}
.gen-header-content {
    display: flex;
    align-items: center;
    gap: 10px;
}
.gen-header-icon {
    width: 32px;
    height: 32px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    border-radius: 3px;
    color: #38bdf8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.gen-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #38bdf8;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 2px;
}
.gen-modal-header .modal-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
    line-height: 1.2 !important;
    display: block !important;
    letter-spacing: .015em !important;
}
.gen-close-btn {
    color: #ffffff !important;
    opacity: 0.8 !important;
    background: transparent !important;
    border: none !important;
    font-size: 20px !important;
    line-height: 1 !important;
    width: 28px;
    height: 28px;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 3px;
    cursor: pointer;
    transition: all .15s ease;
    padding: 0 !important;
    margin: 0 !important;
    outline: none !important;
    text-shadow: none !important;
}
.gen-close-btn:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.18) !important;
    color: #ffffff !important;
}

/* Modal Body */
.gen-modal-body {
    padding: 14px 16px !important;
    background: #ffffff !important;
    display: flex;
    flex-direction: column;
    gap: 11px;
}

/* Info Banner */
.gen-info-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-left: 3px solid #10b981;
    border-radius: 2px;
    padding: 8px 10px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.gen-info-icon {
    color: #059669;
    font-size: 13px;
    margin-top: 1px;
    flex-shrink: 0;
}
.gen-info-text {
    font-size: 11px;
    color: #1e293b;
    line-height: 1.4;
}
.gen-info-text strong {
    color: #065f46;
    display: block;
    margin-bottom: 1px;
    font-size: 11px;
}

/* Pattern Card */
.gen-pattern-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}
.gen-pattern-head {
    background: #edf2f7;
    border-bottom: 1px solid #e2e8f0;
    padding: 5px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .04em;
    display: flex;
    align-items: center;
}
.gen-pattern-body {
    padding: 7px 10px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.gen-pattern-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 0;
    border-bottom: 1px dashed #e2e8f0;
    gap: 8px;
}
.gen-pattern-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.gen-row-label {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    white-space: nowrap;
}
.gen-rule-pill {
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 2px;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
}
.gen-rule-pill.pill-cyan {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.gen-rule-pill.pill-slate {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

/* Target Student Selection Counter Panel */
.gen-target-box {
    background: linear-gradient(135deg, #002C54 0%, #0c3b6d 100%);
    border: 1px solid #001f3d;
    border-radius: 3px;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #ffffff;
}
.gen-target-icon {
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #38bdf8;
    flex-shrink: 0;
}
.gen-target-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}
.gen-target-desc {
    font-size: 9.5px;
    color: #93c5fd;
    line-height: 1.2;
    margin-top: 1px;
}
.gen-target-count-badge {
    background: #0284c7;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 2px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    white-space: nowrap;
    letter-spacing: .02em;
    min-width: 32px;
    text-align: center;
}

/* Modal Footer */
.gen-modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 8px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 8px !important;
}
.gen-btn-cancel {
    height: 28px;
    padding: 0 12px;
    font-size: 11.5px;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: #475569 !important;
    border-radius: 2px !important;
    font-weight: 600 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
}
.gen-btn-cancel:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
}
.gen-btn-submit {
    height: 28px;
    padding: 0 14px;
    font-size: 11.5px;
    background: #0284c7 !important;
    border: 1px solid #0284c7 !important;
    color: #ffffff !important;
    border-radius: 2px !important;
    font-weight: 600 !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(2, 132, 199, 0.3);
    transition: all .15s;
}
.gen-btn-submit:hover {
    background: #0369a1 !important;
    border-color: #0369a1 !important;
}
.gen-btn-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

@media (max-width: 576px) {
    .gen-modal-dialog {
        width: calc(100% - 16px) !important;
        margin: 0.5rem auto !important;
    }
    .gen-modal-body {
        padding: 12px !important;
    }
    .gen-pattern-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 3px;
    }
    .gen-rule-pill {
        width: 100%;
        text-align: center;
    }
}

@media(max-width:768px) {
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
    .credential-hero {
        flex-direction: column;
        align-items: stretch;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper credential-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="credential-page-layout">
                
                {{-- 1. Top Hero Header (Arise ERP Dark Navy Banner) --}}
                <div class="credential-hero">
                    <div class="credential-hero-text">
                        <span class="credential-kicker"><i class="fa fa-key mr-1"></i> Student Portal Access</span>
                        <h1 class="credential-title">Student Login Credentials</h1>
                        <p class="credential-subtitle">Fast class-wise student credentials, 1-click password reveal, instant copy &amp; direct WhatsApp sharing</p>
                    </div>

                    {{-- Summary Statistics Badges --}}
                    <div class="credential-hero-stats">
                        <span class="hero-stat-badge" title="Total Students in Session">
                            <i class="fa fa-users text-info"></i> Total: <b id="stat-total">{{ number_format($stats['total'] ?? 0) }}</b>
                        </span>
                        <span class="hero-stat-badge badge-green" title="Credentials Generated">
                            <i class="fa fa-check-circle"></i> Generated: <b id="stat-generated">{{ number_format($stats['generated'] ?? 0) }}</b>
                        </span>
                        <span class="hero-stat-badge badge-amber" title="Pending Credentials">
                            <i class="fa fa-clock-o"></i> Pending: <b id="stat-pending">{{ number_format($stats['pending'] ?? 0) }}</b>
                        </span>
                    </div>

                    {{-- Actions --}}
                    <div class="credential-hero-actions">
                        <button type="button" class="dash-btn dash-btn-light" id="btn-open-generate" disabled title="Select students first">
                            <i class="fa fa-magic mr-1"></i> Generate Selected (<span id="btn-selected-count">0</span>)
                        </button>
                        <a href="{{ route('login-credential-reports.pdf', $downloadParams) }}" class="dash-btn dash-btn-pdf" id="link-export-pdf" target="_blank" title="Download PDF Report">
                            <i class="fa fa-file-pdf-o mr-1"></i> PDF
                        </a>
                        <a href="{{ route('login-credential-reports.excel', $downloadParams) }}" class="dash-btn dash-btn-excel" id="link-export-excel" title="Download Excel Sheet">
                            <i class="fa fa-file-excel-o mr-1"></i> Excel
                        </a>
                        <a href="{{ url('studentsDashboard') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                {{-- 2. Full-Height Table Card --}}
                <div class="credential-table-card">
                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="dash-card-title"><i class="fa fa-table text-info mr-1"></i> Credentials Grid</h3>
                            <span class="selection-counter-badge" id="selection-pill"><span id="pill-count">0</span> Selected</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records"><span id="header-records-count">{{ $totalCount ?? count($data ?? []) }}</span> Students</span>
                        </div>
                    </div>

                    {{-- Scrollable Table Area --}}
                    <div class="table-scroll-container">
                        <table class="dash-table" id="credential-grid-table">
                            <thead>
                                {{-- Row 1: Header Titles --}}
                                <tr class="header-titles-row">
                                    <th style="width: 44px;" class="text-center">
                                        <input type="checkbox" id="check-all-students" title="Select All On Page" style="cursor:pointer; width:15px; height:15px; vertical-align:middle;">
                                    </th>
                                    <th style="width: 45px;" class="text-center">#</th>
                                    <th style="min-width: 170px;">Student Details</th>
                                    <th style="width: 105px;" class="text-center">Class</th>
                                    <th style="min-width: 130px;">Father / Guardian</th>
                                    <th style="min-width: 135px;">Mobile &amp; WhatsApp</th>
                                    <th style="min-width: 145px;">Username</th>
                                    <th style="min-width: 155px;">Password</th>
                                    <th style="width: 105px;" class="text-center">Status</th>
                                    <th style="width: 95px;" class="text-center fixed_action_filter">Actions</th>
                                </tr>

                                {{-- Row 2: In-Column Excel Filters --}}
                                <tr class="header-filters-row">
                                    <th class="text-center">
                                        <span class="text-white-50 font-size-10"><i class="fa fa-filter"></i></span>
                                    </th>
                                    <th></th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-name" placeholder="Search student...">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-class">
                                            <option value="">All Classes</option>
                                            @foreach($classType ?? [] as $type)
                                                <option value="{{ $type->id }}" {{ $selectedClassId == $type->id ? 'selected' : '' }}>{{ $type->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-father" placeholder="Search father...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-mobile" placeholder="Search mobile...">
                                    </th>
                                    <th>
                                        <input type="text" class="excel-col-filter" id="filter-username" placeholder="Search user...">
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-cred-status">
                                            <option value="" {{ $selectedCredStatus === '' ? 'selected' : '' }}>All Status</option>
                                            <option value="generated" {{ $selectedCredStatus === 'generated' ? 'selected' : '' }}>Generated</option>
                                            <option value="pending" {{ $selectedCredStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </th>
                                    <th>
                                        <select class="excel-col-filter" id="filter-student-status">
                                            <option value="1" {{ $selectedStatus === 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $selectedStatus === 0 ? 'selected' : '' }}>Inactive</option>
                                            <option value="all">All</option>
                                        </select>
                                    </th>
                                    <th class="text-center fixed_action_filter">
                                        <button type="button" class="btn-reset-filters" id="btn-reset-filters" title="Reset All Filters">
                                            <i class="fa fa-refresh mr-1"></i> Reset
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="credential-table-body">
                                @include('students.login_credential.table_rows')
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. Pinned Bottom Pagination Toolbar --}}
                    <div class="table-pagination-bar">
                        <div class="pagination-info">
                            Showing <span id="page-start" class="font-weight-bold text-white">{{ $totalCount > 0 ? ($startIndex + 1) : 0 }}</span> to <span id="page-end" class="font-weight-bold text-white">{{ min($startIndex + count($data), $totalCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $totalCount }}</span> entries
                        </div>
                        <div class="pagination-controls">
                            <div class="rows-per-page-selector">
                                <label for="rows-per-page-select">Rows:</label>
                                <select id="rows-per-page-select">
                                    <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100</option>
                                    <option value="all" {{ ($perPage ?? 25) == 'all' ? 'selected' : '' }}>All</option>
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
    </section>
</div>

{{-- Credential Generation Modal --}}
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="generateModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered gen-modal-dialog" role="document">
        <form id="generateForm" action="{{ url('studentUserNameCreate') }}" method="POST" class="modal-content gen-modal-content">
            @csrf
            <input type="hidden" name="class_type_id" id="hiddenClassTypeId" value="{{ $selectedClassId }}">
            <input type="hidden" name="student_ids" id="hiddenStudentIds">
            <input type="hidden" name="username_order" value="name,mobile">
            <input type="hidden" name="password_order" value="mobile">
            <input type="hidden" name="name_letters" value="4">
            <input type="hidden" name="mobile_digits" value="4">

            {{-- Modal Header --}}
            <div class="modal-header gen-modal-header">
                <div class="gen-header-content">
                    <div class="gen-header-icon">
                        <i class="fa fa-magic"></i>
                    </div>
                    <div>
                        <div class="gen-kicker">Batch Processing Tool</div>
                        <h5 class="modal-title" id="generateModalTitle">Generate Login Credentials</h5>
                    </div>
                </div>
                <button type="button" class="close gen-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Dialog">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body gen-modal-body">
                
                {{-- 1. Standard Rule Banner --}}
                <div class="gen-info-banner">
                    <i class="fa fa-shield gen-info-icon"></i>
                    <div class="gen-info-text">
                        <strong>Standard Credential Rule:</strong>
                        Usernames and passwords will be generated consistently based on each student's registered first name and mobile number.
                    </div>
                </div>

                {{-- 2. Credential Pattern Card --}}
                <div class="gen-pattern-card">
                    <div class="gen-pattern-head">
                        <i class="fa fa-sliders text-info mr-1"></i> Pattern Configuration
                    </div>
                    <div class="gen-pattern-body">
                        <div class="gen-pattern-row">
                            <span class="gen-row-label"><i class="fa fa-user-circle-o mr-1 text-muted"></i> Username Formula:</span>
                            <span class="gen-rule-pill pill-cyan">First Name (4 chars) + Mobile (Last 4)</span>
                        </div>
                        <div class="gen-pattern-row">
                            <span class="gen-row-label"><i class="fa fa-key mr-1 text-muted"></i> Password Formula:</span>
                            <span class="gen-rule-pill pill-slate">Mobile (Last 4 digits)</span>
                        </div>
                        <div class="gen-pattern-row">
                            <span class="gen-row-label"><i class="fa fa-check-circle mr-1 text-success"></i> Example Output:</span>
                            <div class="d-inline-flex align-items-center" style="gap: 4px;">
                                <span class="cred-pill cred-pill-user">ravi3210</span>
                                <span class="text-muted font-weight-bold" style="font-size: 11px;">/</span>
                                <span class="cred-pill cred-pill-pass">3210</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Selection Counter Panel --}}
                <div class="gen-target-box">
                    <div class="d-flex align-items-center gap-2">
                        <div class="gen-target-icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <div>
                            <div class="gen-target-title">Target Students Selected</div>
                            <div class="gen-target-desc">Credentials will be generated for these records</div>
                        </div>
                    </div>
                    <span class="gen-target-count-badge" title="Number of selected students">
                        <span id="modal-selected-count">0</span>
                    </span>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer gen-modal-footer">
                <button type="button" class="dash-btn gen-btn-cancel" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fa fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" id="btn-submit-generation" class="dash-btn gen-btn-submit">
                    <i class="fa fa-magic mr-1"></i> Confirm &amp; Generate
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Real-Time AJAX Client & Professional UI Handlers --}}
<script>
$(document).ready(function() {
    let currentPage = {{ $currentPage ?? 1 }};
    let lastPage = {{ $lastPage ?? 1 }};
    let currentPerPage = "{{ $perPage ?? 25 }}";
    let isFetching = false;
    let filterTimer = null;
    const reportUrl = "{{ url('login_credential_reports') }}";
    const pdfBaseUrl = "{{ route('login-credential-reports.pdf') }}";
    const excelBaseUrl = "{{ route('login-credential-reports.excel') }}";

    function updateExportLinks() {
        const classId = $('#filter-class').val();
        const status = $('#filter-student-status').val();
        const params = new URLSearchParams();
        if (classId) params.append('class_type_id', classId);
        if (status && status !== 'all') params.append('student_status', status);

        const qs = params.toString();
        $('#link-export-pdf').attr('href', pdfBaseUrl + (qs ? '?' + qs : ''));
        $('#link-export-excel').attr('href', excelBaseUrl + (qs ? '?' + qs : ''));
    }

    function getFilterParams(pageOverride) {
        const page = pageOverride !== undefined ? pageOverride : currentPage;
        return {
            ajax: 1,
            page: page,
            per_page: $('#rows-per-page-select').val() || currentPerPage,
            class_type_id: $('#filter-class').val() || '',
            student_status: $('#filter-student-status').val() !== undefined ? $('#filter-student-status').val() : 1,
            credential_status: $('#filter-cred-status').val() || '',
            name: ($('#filter-name').val() || '').trim(),
            father_name: ($('#filter-father').val() || '').trim(),
            mobile: ($('#filter-mobile').val() || '').trim(),
            userName: ($('#filter-username').val() || '').trim(),
        };
    }

    function fetchCredentialReport(page) {
        if (isFetching) return;
        isFetching = true;
        $('#credential-grid-table').addClass('credential-table-loading');

        const params = getFilterParams(page);

        $.ajax({
            url: reportUrl,
            type: 'GET',
            data: params,
            dataType: 'json',
            success: function(res) {
                if (res && (res.status === true || res.status === 200 || res.html !== undefined)) {
                    $('#credential-table-body').html(res.html);
                    currentPage = parseInt(res.current_page, 10);
                    lastPage = parseInt(res.last_page, 10);
                    currentPerPage = res.per_page;

                    $('#page-start').text(res.from);
                    $('#page-end').text(res.to);
                    $('#total-records').text(res.total);
                    $('#header-records-count').text(res.total);
                    $('#current-page').text(res.current_page);
                    $('#total-pages').text(res.last_page);

                    if (res.stats) {
                        $('#stat-total').text(Number(res.stats.total || 0).toLocaleString());
                        $('#stat-generated').text(Number(res.stats.generated || 0).toLocaleString());
                        $('#stat-pending').text(Number(res.stats.pending || 0).toLocaleString());
                    }

                    $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
                    $('#btn-next, #btn-last').prop('disabled', currentPage >= lastPage || lastPage <= 1);

                    // Reset selection checkboxes
                    $('#check-all-students').prop('checked', false).prop('indeterminate', false);
                    updateSelectionControls();
                    updateExportLinks();
                }
            },
            error: function(xhr) {
                console.error('Failed to load credential reports:', xhr);
            },
            complete: function() {
                isFetching = false;
                $('#credential-grid-table').removeClass('credential-table-loading');
            }
        });
    }

    // Debounced text inputs
    $('#filter-name, #filter-father, #filter-mobile, #filter-username').on('input', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function() {
            currentPage = 1;
            fetchCredentialReport(1);
        }, 350);
    });

    // Instant dropdown triggers
    $('#filter-class, #filter-cred-status, #filter-student-status').on('change', function() {
        currentPage = 1;
        fetchCredentialReport(1);
    });

    // Rows per page dropdown
    $('#rows-per-page-select').on('change', function() {
        currentPage = 1;
        currentPerPage = $(this).val();
        fetchCredentialReport(1);
    });

    // Pagination navigation
    $('#btn-first').on('click', function() {
        if (currentPage > 1) {
            currentPage = 1;
            fetchCredentialReport(1);
        }
    });

    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            fetchCredentialReport(currentPage);
        }
    });

    $('#btn-next').on('click', function() {
        if (currentPage < lastPage) {
            currentPage++;
            fetchCredentialReport(currentPage);
        }
    });

    $('#btn-last').on('click', function() {
        if (currentPage < lastPage) {
            currentPage = lastPage;
            fetchCredentialReport(currentPage);
        }
    });

    // Reset button
    $('#btn-reset-filters').on('click', function() {
        $('#filter-name, #filter-father, #filter-mobile, #filter-username').val('');
        $('#filter-class').val('');
        $('#filter-cred-status').val('');
        $('#filter-student-status').val('1');
        currentPage = 1;
        fetchCredentialReport(1);
    });

    // 1. Password Visibility Eye Toggle
    $(document).on('click', '.btn-toggle-eye', function() {
        var $btn = $(this);
        var $pill = $btn.siblings('.cred-pill-pass');
        var plain = $pill.data('plain');
        var masked = $pill.data('masked') || '••••••••';

        if ($pill.hasClass('pass-revealed')) {
            $pill.removeClass('pass-revealed').addClass('pass-masked').text(masked);
            $btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $btn.attr('title', 'Reveal Password');
        } else {
            $pill.removeClass('pass-masked').addClass('pass-revealed').text(plain);
            $btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $btn.attr('title', 'Hide Password');
        }
    });

    // 2. Generic Single Item Copy Button
    $(document).on('click', '.copy-btn', function() {
        var $btn = $(this);
        var textToCopy = $btn.data('copy') || '';
        if (!textToCopy) return;

        copyToClipboard(textToCopy);
        $btn.addClass('copied');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i>');

        setTimeout(function() {
            $btn.removeClass('copied').html(originalHtml);
        }, 1200);
    });

    // 3. Copy Both Username & Password
    $(document).on('click', '.copy-both-btn', function() {
        var $btn = $(this);
        var name = $btn.data('name') || 'Student';
        var studentClass = $btn.data('class') || '';
        var user = $btn.data('user') || '';
        var pass = $btn.data('pass') || '';

        var text = "Student: " + name + (studentClass ? " (Class: " + studentClass + ")" : "") + "\nUsername: " + user + "\nPassword: " + pass;
        copyToClipboard(text);

        $btn.css('background', '#10b981').css('color', '#fff').css('border-color', '#10b981');
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i>');

        setTimeout(function() {
            $btn.css('background', '').css('color', '').css('border-color', '').html(originalHtml);
        }, 1200);
    });

    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
    }

    // 4. Selection Management for Credential Generation
    function updateSelectionControls() {
        var $visibleBoxes = $('#credential-table-body .student-check');
        var totalVisible = $visibleBoxes.length;
        var checkedCount = $visibleBoxes.filter(':checked').length;
        var master = $('#check-all-students');

        master.prop('checked', totalVisible > 0 && checkedCount === totalVisible);
        master.prop('indeterminate', checkedCount > 0 && checkedCount < totalVisible);

        $('#btn-selected-count').text(checkedCount);
        $('#modal-selected-count').text(checkedCount);
        $('#pill-count').text(checkedCount);

        if (checkedCount > 0) {
            $('#btn-open-generate').prop('disabled', false).attr('title', 'Generate credentials for ' + checkedCount + ' student(s)');
            $('#selection-pill').show();
        } else {
            $('#btn-open-generate').prop('disabled', true).attr('title', 'Select students first');
            $('#selection-pill').hide();
        }
    }

    $('#check-all-students').on('change', function() {
        $('#credential-table-body .student-check').prop('checked', this.checked);
        updateSelectionControls();
    });

    $(document).on('change', '.student-check', updateSelectionControls);

    // Open Generate Modal
    $('#btn-open-generate').on('click', function() {
        var selectedIds = [];
        $('#credential-table-body .student-check:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        $('#hiddenStudentIds').val(selectedIds.join(','));
        $('#hiddenClassTypeId').val($('#filter-class').val() || '');
        $('#modal-selected-count').text(selectedIds.length);
        $('#btn-submit-generation').prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Confirm &amp; Generate');
        $('#generateModal').modal('show');
    });

    // Form submission state
    $('#generateForm').on('submit', function() {
        var $btn = $('#btn-submit-generation');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Generating...');
    });

    $('#generateModal').on('hidden.bs.modal', function() {
        $('#btn-submit-generation').prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Confirm &amp; Generate');
    });

    updateExportLinks();
});
</script>
@endsection
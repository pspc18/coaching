@php
    $getFeesGroup = Helper::getFeesGroup();
    $classType = Helper::classType();
    $stats = $stats ?? [
        'total_classes' => count($groupedByClass ?? []),
        'total_heads' => count($allFeesMasters ?? []),
        'total_amount' => 0,
        'in_use_heads' => 0,
    ];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - FEES MASTER (UNIFIED SIGNATURE THEME)
   Strictly aligned with admissionView and addUser design standards:
   - Font Family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif
   - Base Font Size: 11.5px - 12px
   - Border Radius: Sharp 2px throughout (cards, inputs, buttons, tables, badges)
   - Dark Navy Palette: #002C54 to #0f3460 with cyan/sky accents
   - Table Theme: Alternating rows (#f8fafc / #edf2f7), hover #e2e8f0, sticky headers
   ========================================================================== */

.fg-viewport-wrapper {
    background: #eef2f6 !important;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 12px;
    padding: 5px 8px 6px 8px !important;
    height: calc(100vh - 57px) !important;
    max-height: calc(100vh - 57px) !important;
    min-height: calc(100vh - 57px) !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    box-sizing: border-box !important;
}
.fg-viewport-wrapper * {
    box-sizing: border-box;
}

/* Hide main footer on this full-viewport screen */
.main-footer {
    display: none !important;
}

/* 1. Top Hero Banner (Compact single/dual row header) */
.dash-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 4px 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 5px;
    flex: 0 0 auto !important;
}
.dash-hero-text {
    display: flex;
    flex-direction: column;
}
.dash-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.dash-hero-title {
    font-size: 13.5px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-hero-subtitle {
    font-size: 10px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Metric Counter Pills */
.dash-hero-pills {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.dash-hero-pill {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 2px;
    padding: 1px 6px;
    font-size: 10px;
    color: #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.dash-hero-pill b {
    color: #ffffff;
    font-weight: 700;
}
.dash-hero-pill.pill-active {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.45);
    color: #38bdf8;
}

/* Hero Action Buttons */
.dash-hero-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    height: 25px;
    padding: 0 9px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
    line-height: 1;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    border-color: rgba(255,255,255,.6);
    color: #ffffff !important;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #001f3d;
    color: #ffffff !important;
}
.dash-btn-secondary {
    background: #051e38;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.25);
}
.dash-btn-secondary:hover {
    background: #031426;
}

/* 2. Equal Height Columns Workspace - CSS Grid forces 100% Identical Height */
.dash-split-wrap {
    flex: 1 1 0% !important;
    min-height: 0 !important;
    display: grid !important;
    grid-template-columns: 440px 1fr !important;
    gap: 8px !important;
    overflow: hidden !important;
    align-items: stretch !important;
}
.fg-col-form {
    min-width: 0 !important;
    min-height: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}
.fg-col-table {
    min-width: 0 !important;
    min-height: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

/* 3. Equal-Height Unified Cards */
.equal-card {
    height: 100% !important;
    min-height: 0 !important;
    max-height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,.06) !important;
}

/* Identical 32px Header in both cards */
.dash-card-header {
    flex: 0 0 32px !important;
    height: 32px !important;
    min-height: 32px !important;
    max-height: 32px !important;
    padding: 0 10px !important;
    border-bottom: 1px solid rgba(255,255,255,.12) !important;
    background: #002342 !important;
    color: #ffffff !important;
    box-sizing: border-box !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
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

/* Form Container inside Left Card - Fills exactly 100% */
.card-form-wrapper {
    flex: 1 1 0% !important;
    min-height: 0 !important;
    height: auto !important;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
    margin: 0 !important;
    background: #ffffff !important;
}

/* Scroll Containers - Both Left Form & Right Table Scroll Internally */
.form-scroll-container {
    flex: 1 1 0% !important;
    min-height: 0 !important;
    height: auto !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 10px !important;
    background: #ffffff !important;
}
.table-scroll-container {
    flex: 1 1 0% !important;
    min-height: 0 !important;
    height: auto !important;
    overflow-y: auto !important;
    overflow-x: auto !important;
    position: relative !important;
    background: #eef2f6 !important;
}

/* Identical 32px Action Footers pinned at the very bottom */
.card-action-footer {
    flex: 0 0 32px !important;
    height: 32px !important;
    min-height: 32px !important;
    max-height: 32px !important;
    padding: 3px 8px !important;
    background: #002342 !important;
    border-top: 1px solid rgba(255,255,255,.12) !important;
    box-sizing: border-box !important;
    display: flex !important;
    align-items: center !important;
}
.table-pagination-bar {
    justify-content: space-between !important;
    font-size: 11px !important;
    color: #cbd5e1 !important;
}

/* Form Controls (Compact 29px inputs from addUser) */
.form-group-compact {
    margin-bottom: 8px;
}
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-label-compact .req-star {
    color: #ef4444;
    margin-left: 2px;
    font-weight: 700;
}
.form-label-compact .label-note {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 400;
}
.form-control-compact {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    width: 100%;
    outline: none;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

/* Fee Matrix Table inside Left Form */
.fee-matrix-wrap {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    overflow: hidden;
    background: #ffffff;
}
.fee-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}
.fee-matrix-table th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 6px 6px;
    border-bottom: 1px solid #cbd5e1;
    border-right: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 2;
    vertical-align: middle;
}
.fee-matrix-table td {
    padding: 5px 6px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
}
.fee-matrix-table tr:hover td {
    background: #f8fafc;
}
.matrix-input-amount {
    height: 25px;
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    text-align: right;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 6px;
    width: 100%;
    outline: none;
}
.matrix-input-amount:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 1px #002C54;
}
.matrix-input-date {
    height: 25px;
    font-size: 10.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px 4px;
    width: 100%;
    outline: none;
}
.matrix-input-date:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 1px #002C54;
}

/* Sharp Custom Switch Toggle */
.matrix-switch {
    position: relative;
    display: inline-block;
    width: 28px;
    height: 15px;
    margin: 0;
}
.matrix-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.matrix-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .15s;
    border-radius: 2px;
}
.matrix-switch-slider:before {
    position: absolute;
    content: "";
    height: 11px;
    width: 11px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .15s;
    border-radius: 2px;
}
input:checked + .matrix-switch-slider {
    background-color: #002C54;
}
input:checked + .matrix-switch-slider:before {
    transform: translateX(13px);
}

/* 4. Right Table Card (1:1 with admission-table-card from admissionView) */
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
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
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

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Dash Table - Exact 1:1 with admissionView */
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
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 8px;
    height: 34px;
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
.excel-col-filter {
    width: 100%;
    height: 25px;
    padding: 2px 6px;
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
.btn-reset-filters {
    height: 25px;
    padding: 0 6px;
    font-size: 11px;
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

/* Table Body Rows - Soft Slate Palette (1:1 with admissionView) */
.dash-table tbody td {
    padding: 5px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11.5px;
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

/* Fee Head Breakdown Pills in Directory */
.heads-pill-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.head-pill-item {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #1e293b;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.head-pill-item b {
    color: #002C54;
    font-weight: 700;
}
.head-pill-amount {
    color: #0284c7;
    font-weight: 700;
}
.head-pill-date {
    font-size: 9.5px;
    color: #475569;
    background: #f1f5f9;
    padding: 1px 4px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.btn-delete-head {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    line-height: 1;
    transition: all .15s;
    text-decoration: none !important;
}
.btn-delete-head:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
}
.head-lock-badge {
    color: #d97706;
    font-size: 9.5px;
    display: inline-flex;
    align-items: center;
    background: #fffbeb;
    border: 1px solid #fde68a;
    padding: 1px 4px;
    border-radius: 2px;
}

/* Action Buttons (1:1 with admissionView .table-btn) */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.table-btn {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s;
    line-height: 1;
}
.btn-action-edit {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-action-edit:hover {
    background: #2563eb;
    color: #ffffff;
}
.btn-action-assign {
    background: #f0fdf4;
    color: #16a34a;
    border-color: #bbf7d0;
}
.btn-action-assign:hover {
    background: #16a34a;
    color: #ffffff;
}

/* Bottom Pagination Toolbar - Dark Navy (1:1 with admissionView) */
.table-pagination-bar {
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    padding: 4px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    min-height: 32px;
    font-size: 11px;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
}

/* Badges (1:1 with admissionView) */
.badge-class {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
    display: inline-block;
}

/* ==========================================================================
   ARISE ERP - SIGNATURE UNIFIED MODAL SYSTEM
   Guarantees zero text-overlapping, crisp typography, and responsive alignment
   ========================================================================== */
.theme-modal-dialog {
    margin: 1.5rem auto !important;
}
.theme-modal-content {
    border: none !important;
    border-radius: 3px !important;
    overflow: hidden !important;
    box-shadow: 0 20px 45px -8px rgba(0, 44, 84, 0.4), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
    background: #ffffff !important;
}
.theme-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 8px 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 48px !important;
    border-top-left-radius: 3px !important;
    border-top-right-radius: 3px !important;
}
.theme-modal-header-danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
}
.theme-modal-title-box {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
}
.theme-modal-icon {
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    background: rgba(56, 189, 248, 0.18) !important;
    border: 1px solid rgba(56, 189, 248, 0.35) !important;
    border-radius: 3px !important;
    color: #38bdf8 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    flex-shrink: 0 !important;
}
.theme-modal-icon-danger {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    color: #ffffff !important;
}
.theme-modal-headings {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: center !important;
    line-height: 1.2 !important;
}
.theme-modal-headings .theme-modal-title,
.theme-modal-headings h5.modal-title,
.theme-modal-headings h5 {
    display: block !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.25 !important;
    letter-spacing: 0.01em !important;
    white-space: nowrap !important;
}
.theme-modal-subtitle {
    display: block !important;
    font-size: 10.5px !important;
    color: #93c5fd !important;
    margin: 2px 0 0 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
    font-weight: 400 !important;
}
.theme-modal-close {
    color: #ffffff !important;
    opacity: 0.85 !important;
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
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
.theme-modal-close:hover {
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.22) !important;
    color: #ffffff !important;
}
.theme-modal-body {
    padding: 10px 14px !important;
    background: #ffffff !important;
}
.theme-filter-card {
    background: #f8fafc !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 3px !important;
    padding: 8px 10px !important;
    margin-bottom: 8px !important;
}
.theme-filter-label {
    font-size: 10.5px !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    margin-bottom: 3px !important;
    display: block !important;
    text-transform: uppercase !important;
    letter-spacing: 0.02em !important;
}
.theme-modal-alert {
    padding: 6px 10px !important;
    border-radius: 2px !important;
    font-size: 11px !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 8px !important;
    margin-bottom: 8px !important;
    line-height: 1.35 !important;
}
.theme-modal-alert-info {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    border-left: 3px solid #3b82f6 !important;
    color: #1e3a8a !important;
}
.theme-modal-alert-warning {
    background: #fffbeb !important;
    border: 1px solid #fde68a !important;
    border-left: 3px solid #f59e0b !important;
    color: #78350f !important;
}
.theme-modal-table-wrap {
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    overflow: hidden !important;
    background: #ffffff !important;
}
.theme-modal-table-wrap table thead th {
    background: #002C54 !important;
    color: #ffffff !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.03em !important;
    padding: 6px 8px !important;
    vertical-align: middle !important;
    border-color: #001f3d !important;
    white-space: nowrap !important;
}
.theme-modal-table-wrap table tbody td {
    padding: 5px 6px !important;
    font-size: 11px !important;
    vertical-align: middle !important;
    border-color: #e2e8f0 !important;
}
.theme-modal-table-wrap table tbody tr:nth-of-type(even) {
    background-color: #f8fafc !important;
}
.theme-modal-table-wrap table tbody tr:hover {
    background-color: #f1f5f9 !important;
}
.theme-modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 7px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 42px !important;
}
.theme-modal-body .select2-container--default .select2-selection--multiple {
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    min-height: 28px !important;
    padding: 1px 4px !important;
    font-size: 11px !important;
}
.theme-modal-body .select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15) !important;
}
.theme-modal-body .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: #eff6ff !important;
    border: 1px solid #bfdbfe !important;
    color: #1d4ed8 !important;
    font-size: 10.5px !important;
    font-weight: 600 !important;
    border-radius: 2px !important;
    padding: 0 4px !important;
    margin-top: 2px !important;
}
.theme-modal-body .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #1d4ed8 !important;
    margin-right: 3px !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper fg-viewport-wrapper">

    {{-- 1. Dark Navy Top Hero Banner (1:1 with user-hero & admission-hero) --}}
    <div class="dash-hero">
        <div class="dash-hero-text">
            <span class="dash-kicker">Fees Structure & Directory</span>
            <h1 class="dash-hero-title">
                <i class="fa fa-sliders"></i> {{ __('fees.Fees Master') }}
            </h1>
            <span class="dash-hero-subtitle">Define class fee structures, assign fees to students, and manage fee master configurations</span>
        </div>

        {{-- Metric Counter Pills --}}
        <div class="dash-hero-pills">
            <span class="dash-hero-pill pill-active">
                <i class="fa fa-graduation-cap"></i> Classes: <b>{{ $stats['total_classes'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-cubes"></i> Heads: <b>{{ $stats['total_heads'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-inr"></i> Total: <b>₹{{ number_format($stats['total_amount'] ?? 0) }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-lock text-warning"></i> In-Use: <b>{{ $stats['in_use_heads'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-calendar-check-o"></i> Session: <b>{{ $currentSessionName }}</b>
            </span>
        </div>

        {{-- Hero Shortcuts & Modal Triggers --}}
        <div class="dash-hero-actions">
            <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#students_list_modal" data-bs-toggle="modal" data-bs-target="#students_list_modal">
                <i class="fa fa-user-plus"></i> Student Fee Assign
            </button>
            <button type="button" class="dash-btn dash-btn-outline" id="fees_modification_btn" data-toggle="modal" data-target="#fees_modification" data-bs-toggle="modal" data-bs-target="#fees_modification">
                <i class="fa fa-pencil-square-o"></i> Modify Student Fees
            </button>
            <a href="{{ url('feesGroup') }}" class="dash-btn dash-btn-outline">
                <i class="fa fa-folder-open"></i> Fees Group
            </a>
            <a href="{{ url('feesCollectAdd') }}" class="dash-btn dash-btn-outline">
                <i class="fa fa-inr"></i> Collect Fees
            </a>
        </div>
    </div>

    {{-- 2. Equal-Height Split Workspace --}}
    <div class="dash-split-wrap">

        {{-- Left Form Column (Assign Fee Structure to Class - Equal Height Card) --}}
        <div class="fg-col-form">
            <div class="equal-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-plus-circle text-info"></i>
                        <span>Assign Fee Structure</span>
                    </h3>
                    <span class="badge-total-records">Configuration</span>
                </div>

                <form id="quickForm" action="{{ url('feesMasterAdd') }}" method="post" class="card-form-wrapper">
                    @csrf

                    <div class="form-scroll-container">
                        {{-- Select Class --}}
                        <div class="form-group-compact">
                            <label class="form-label-compact">
                                <span>{{ __('common.Class') }} <span class="req-star">*</span></span>
                                <span class="label-note">Select target class</span>
                            </label>
                            <select class="form-control-compact @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" required>
                                <option value="">-- Select Class --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('class_type_id')
                                <span class="text-danger mt-1 d-block" style="font-size:10px; font-weight:600;">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fee Group Matrix --}}
                        <div class="form-group-compact mb-0">
                            <label class="form-label-compact">
                                <span>{{ __('fees.Fees Group') }} Matrix <span class="req-star">*</span></span>
                                <span class="label-note">Toggle heads to assign</span>
                            </label>

                            <div class="fee-matrix-wrap">
                                <table class="fee-matrix-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px; text-align: center;">
                                                <input type="checkbox" id="select_group" checked style="cursor:pointer;">
                                            </th>
                                            <th>Fee Head</th>
                                            <th style="width: 90px; text-align: right;">Amount (₹)</th>
                                            <th style="width: 105px; text-align: center;">Due Date</th>
                                            <th style="width: 45px; text-align: center;" title="Allow student-level edit on admission">Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($getFeesGroup))
                                            @foreach ($getFeesGroup as $gType)
                                                @php
                                                    $isRefundable = strtolower($gType->fees_refund ?? '') === 'yes';
                                                @endphp
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <input type="checkbox" class="group_checkbox" id="fees_group_{{ $gType->id }}" name="fees_group_id[]" value="{{ $gType->id }}" checked style="cursor:pointer;">
                                                    </td>
                                                    <td>
                                                        <label for="fees_group_{{ $gType->id }}" style="cursor:pointer; margin:0; font-weight:600; color:#1e293b; font-size:11px;">
                                                            {{ $gType->name ?? '' }}
                                                        </label>
                                                        @if($isRefundable)
                                                            <span class="badge badge-success ml-1" style="font-size:8.5px; padding:1px 4px; border-radius:2px;">Refund</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input class="matrix-input-amount" type="text" name="amount[{{ $gType->id }}]" placeholder="0" value="0" onkeypress="javascript:return isNumber(event)">
                                                    </td>
                                                    <td>
                                                        <input class="matrix-input-date" type="date" name="installment_due_date[{{ $gType->id }}]">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <label class="matrix-switch">
                                                            <input type="checkbox" class="matrix-editable-cb" onchange="$(this).closest('td').find('.editable-val').val(this.checked ? 1 : 0);">
                                                            <span class="matrix-switch-slider"></span>
                                                        </label>
                                                        <input type="hidden" name="editable_value[{{ $gType->id }}]" class="editable-val" value="0">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted" style="font-size:11px;">
                                                    No Fee Groups found. <a href="{{ url('feesGroup') }}">Create Groups</a> first.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Pinned Action Footer (Matching right table footer) --}}
                    <div class="card-action-footer">
                        <button type="submit" class="dash-btn dash-btn-light w-100" style="height:26px; font-size:11px; font-weight:700;">
                            <i class="fa fa-check-circle mr-1"></i> Save Fee Structure
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Table Column (Configured Class Directory - Equal Height Card) --}}
        <div class="fg-col-table">
            <div class="equal-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-list text-info"></i> Class Fee Master Directory
                    </h3>
                    <span class="badge-total-records">
                        Total Classes: <b>{{ count($groupedByClass ?? []) }}</b>
                    </span>
                </div>

                {{-- Scrollable Table Container with Sticky Headers --}}
                <div class="table-scroll-container">
                    <table class="dash-table" id="feesMasterTable">
                        <thead>
                            {{-- Row 1: Header Titles --}}
                            <tr class="header-titles-row">
                                <th style="width: 45px; text-align: center;">#</th>
                                <th style="width: 150px;">{{ __('common.Class') }}</th>
                                <th>Assigned Fee Heads & Amounts</th>
                                <th style="width: 110px; text-align: right;">Total Fee</th>
                                <th style="width: 70px; text-align: center;">{{ __('messages.Action') }}</th>
                            </tr>

                            {{-- Row 2: In-Column Sticky Excel Filter --}}
                            <tr class="excel-filter-row">
                                <th></th>
                                <th>
                                    <input type="text" id="filter_class" class="excel-col-filter" placeholder="Search class...">
                                </th>
                                <th colspan="2" style="font-size: 10px; color: #93c5fd; font-weight: normal; vertical-align: middle;">
                                    <i class="fa fa-info-circle mr-1"></i> Type above to filter configured classes instantly
                                </th>
                                <th style="text-align: center;">
                                    <button type="button" class="btn-reset-filters" id="btn_clear_filters" title="Reset Filter">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($groupedByClass as $classTypeId => $classFeeMasters)
                                @php
                                    $className = $classFeeMasters->first()->ClassTypes->name ?? 'Class #' . $classTypeId;
                                    $classNameLower = strtolower($className);
                                    $totalClassFee = $classFeeMasters->sum('amount');
                                    $headsCount = $classFeeMasters->count();
                                @endphp
                                <tr class="fm-class-row" data-class="{{ $classNameLower }}">
                                    <td style="text-align:center; font-weight:700; color:#64748b;">{{ $loop->iteration }}</td>
                                    <td>
                                        <div style="font-weight: 700; color:#002C54; font-size:12px;">{{ $className }}</div>
                                        <span class="badge-class" style="margin-top:2px;">
                                            {{ $headsCount }} {{ Str::plural('Fee Head', $headsCount) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="heads-pill-wrap">
                                            @foreach($classFeeMasters as $fm)
                                                @php
                                                    $isLocked = isset($usedDetailGroups[$fm->fees_group_id]) || isset($usedAssignPairs[$classTypeId . '_' . $fm->fees_group_id]);
                                                    $headName = $fm->feesGroup->name ?? 'Group #' . $fm->fees_group_id;
                                                    $dueFormatted = !empty($fm->installment_due_date) ? date('d M Y', strtotime($fm->installment_due_date)) : '';
                                                @endphp
                                                <div class="head-pill-item">
                                                    <span><b>{{ $headName }}</b>: <span class="head-pill-amount">₹{{ number_format($fm->amount) }}</span></span>
                                                    @if(!empty($dueFormatted))
                                                        <span class="head-pill-date" title="Due Date"><i class="fa fa-calendar-o"></i> {{ $dueFormatted }}</span>
                                                    @endif
                                                    @if($isLocked)
                                                        <span class="head-lock-badge" title="Active student records linked. Locked from deletion.">
                                                            <i class="fa fa-lock"></i>
                                                        </span>
                                                    @else
                                                        <a href="javascript:void(0);" 
                                                           data-groupname="{{ $fm->id }}" 
                                                           data-groupname-label="{{ $headName }} ({{ $className }})"
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#Modal_id" 
                                                           class="btn-delete-head deleteData {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                                           title="Delete this fee head from {{ $className }}">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span style="font-size:12px; font-weight:700; color:#0284c7;">
                                            ₹{{ number_format($totalClassFee) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="table-actions">
                                            <a href="{{ url('feesMasterEdit/' . $classTypeId) }}" 
                                               class="table-btn btn-action-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                                               title="Edit Class Fee Structure">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="table-btn btn-action-assign btn-assign-class-students" 
                                                    data-class-id="{{ $classTypeId }}" 
                                                    title="Assign Fees to Students of {{ $className }}">
                                                <i class="fa fa-user-plus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="fmInitialEmptyRow">
                                    <td colspan="5" class="text-center py-5">
                                        <div style="padding: 30px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                            <div style="width:50px; height:50px; border-radius:2px; background:#e0f2fe; border:1px dashed #7dd3fc; display:flex; align-items:center; justify-content:center; font-size:20px; color:#0284c7; margin-bottom:10px;">
                                                <i class="fa fa-folder-open-o"></i>
                                            </div>
                                            <div style="font-size:13px; font-weight:700; color:#002C54; margin-bottom:3px;">No Class Fee Structures Configured Yet</div>
                                            <div style="font-size:11px; color:#64748b;">Use the form on the left to assign fees to your first class.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Empty State on In-Column Excel Filter --}}
                            <tr id="fgEmptyFilterRow" class="d-none">
                                <td colspan="5" class="text-center py-5">
                                    <div style="padding: 24px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                        <div style="width:48px; height:48px; border-radius:2px; background:#f8fafc; border:1px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; font-size:20px; color:#64748b; margin-bottom:10px;">
                                            <i class="fa fa-search"></i>
                                        </div>
                                        <div style="font-size:12.5px; font-weight:700; color:#002C54; margin-bottom:3px;">No Matching Classes Found</div>
                                        <div style="font-size:11px; color:#64748b; margin-bottom:10px;">No class fee records match your search filter.</div>
                                        <button type="button" class="dash-btn dash-btn-secondary" id="btn_clear_empty_filters" style="height:26px; font-size:11px;">
                                            <i class="fa fa-refresh mr-1"></i> Clear Filter
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Table Bottom Toolbar (1:1 with admissionView) --}}
                <div class="table-pagination-bar card-action-footer">
                    <span class="pagination-info">
                        <i class="fa fa-shield text-warning mr-1"></i> <b>Safety Guard:</b> Active fee heads assigned to students or with payments are locked from deletion.
                    </span>
                    <span class="font-weight-bold text-white">
                        Showing {{ count($groupedByClass ?? []) }} classes
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- 1. Fee Head Delete Confirmation Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm theme-modal-dialog" role="document">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header theme-modal-header-danger">
                <div class="theme-modal-title-box">
                    <div class="theme-modal-icon theme-modal-icon-danger">
                        <i class="fa fa-trash-o"></i>
                    </div>
                    <div class="theme-modal-headings">
                        <h5 class="modal-title theme-modal-title">
                            {{ __('messages.Delete Confirmation') }}
                        </h5>
                    </div>
                </div>
                <button type="button" class="theme-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form action="{{ url('feesMasterDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center" style="padding:18px 14px; background:#fff;">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <div style="width:44px; height:44px; border-radius:3px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:18px; margin:0 auto 10px auto; border:1px solid #fecaca;">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p style="font-size:11.5px; color:#475569; margin-bottom:4px;">Are you sure you want to delete this fee head:</p>
                    <h6 style="font-size:13px; font-weight:700; color:#002C54; margin:0;" id="delete_head_label"></h6>
                </div>
                <div class="modal-footer justify-content-center" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 12px; display:flex; gap:8px;">
                    <button type="button" class="dash-btn dash-btn-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; font-size:11px; padding:0 12px;">{{ __('messages.Close') }}</button>
                    <button type="submit" class="dash-btn" style="height:28px; padding:0 16px; font-size:11px; background:#dc2626; color:#fff; border-color:#dc2626;"><i class="fa fa-trash mr-1"></i> {{ __('messages.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Student Fee Assign Modal --}}
<div class="modal fade" id="students_list_modal" tabindex="-1" role="dialog" aria-labelledby="studentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg theme-modal-dialog" role="document">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header">
                <div class="theme-modal-title-box">
                    <div class="theme-modal-icon">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div class="theme-modal-headings">
                        <h5 class="modal-title theme-modal-title" id="studentsModalLabel">
                            Bulk Assign Fees to Students
                        </h5>
                        <div class="theme-modal-subtitle">
                            Select class and assign fee heads in bulk to enrolled students
                        </div>
                    </div>
                </div>
                <button type="button" class="theme-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Dialog">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            
            <form id="assignFeesMultiple" action="{{ url('assignFeesMultipleStudents') }}" method="POST">
                @csrf
                <div class="modal-body theme-modal-body">
                    {{-- Filter Row --}}
                    <div class="theme-filter-card">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="theme-filter-label">Select Class <span class="text-danger">*</span></label>
                                <select class="form-control-compact w-100" id="bulk_class_type_id" name="class_type_id" required style="height:28px; font-size:11.5px; border-radius:2px;">
                                    <option value="">-- Choose Class --</option>
                                    @if(!empty($classType))
                                        @foreach($classType as $type)
                                            <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="theme-filter-label">Admission No (Optional)</label>
                                <input type="text" class="form-control-compact w-100" placeholder="Search by Adm No" name="admissionNo" id="bulk_admission_no" style="height:28px; font-size:11.5px; border-radius:2px;">
                            </div>
                            <div class="col-md-5">
                                <label class="theme-filter-label">Fee Heads <span class="text-danger">*</span></label>
                                <select class="form-control-compact select2 w-100" multiple id="bulk_fees_master_ids" name="fees_master_ids[]" required data-placeholder="Choose fee head(s)..." style="width:100%; min-height:28px; font-size:11.5px;">
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Students Table Container --}}
                    <div class="theme-modal-table-wrap mb-2">
                        <div style="max-height:260px; overflow-y:auto;">
                            <table class="table table-sm table-bordered table-hover mb-0 text-center" style="font-size:11.5px;">
                                <thead style="position:sticky; top:0; background:#002C54; color:#ffffff; z-index:2;">
                                    <tr>
                                        <th style="width:36px; padding:6px 4px;"><input type="checkbox" id="all_students" style="cursor:pointer;"></th>
                                        <th style="min-width:140px; padding:6px 8px;" class="text-left">Student Name</th>
                                        <th style="min-width:90px; padding:6px 8px;">Admission No</th>
                                        <th style="min-width:95px; padding:6px 8px;">Mobile</th>
                                        <th style="min-width:120px; padding:6px 8px;" class="text-left">Father's Name</th>
                                        <th style="min-width:110px; padding:6px 8px;" class="text-left">Current Heads</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_students_list">
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted" style="font-size:11.5px;">
                                            <i class="fa fa-info-circle text-info mr-1"></i> Please select a class above to load enrolled students.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Note Box --}}
                    <div class="theme-modal-alert theme-modal-alert-info mb-0">
                        <i class="fa fa-shield text-primary" style="font-size:13px; margin-top:1px;"></i>
                        <div>
                            <b>Safe Assignment:</b> Already assigned fee heads will be skipped automatically to prevent duplicate fees.
                        </div>
                    </div>
                </div>

                <div class="modal-footer theme-modal-footer">
                    <div style="font-size:11.5px; color:#475569;">
                        <i class="fa fa-users text-muted mr-1"></i> Selected: <b id="students_selected_counter" class="text-primary font-weight-bold">0</b> student(s)
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="dash-btn dash-btn-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; font-size:11px; padding:0 12px;">
                            <i class="fa fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="dash-btn dash-btn-primary" style="height:28px; padding:0 14px; font-size:11px; background:#002C54; color:#fff; border-color:#002C54;">
                            <i class="fa fa-check mr-1"></i> Assign Selected Fees
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Modify Student Fees Modal --}}
<div class="modal fade" id="fees_modification" tabindex="-1" aria-labelledby="feesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl theme-modal-dialog" role="document">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header">
                <div class="theme-modal-title-box">
                    <div class="theme-modal-icon">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                    <div class="theme-modal-headings">
                        <h5 class="modal-title theme-modal-title" id="feesModalLabel">
                            Modify Student Fee Assignments
                        </h5>
                        <div class="theme-modal-subtitle">
                            Search student to adjust discounts, due dates, refund flags, or remove uncollected heads
                        </div>
                    </div>
                </div>
                <button type="button" class="theme-modal-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" title="Close Dialog">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-body theme-modal-body">
                {{-- Search Filter Form --}}
                <div class="theme-filter-card">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="theme-filter-label">Class Filter</label>
                            <select class="form-control-compact w-100" id="class_modification" name="class_type_id" style="height:28px; font-size:11.5px; border-radius:2px;">
                                <option value="">-- All Classes --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="theme-filter-label">Admission No / Student Name</label>
                            <input type="text" class="form-control-compact w-100" id="admission_modification" placeholder="Enter Admission No or Student Name" style="height:28px; font-size:11.5px; border-radius:2px;">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="dash-btn dash-btn-primary w-100" id="searchButton" style="height:28px; font-size:11px; background:#002C54; color:#fff; border-color:#002C54; display:flex; align-items:center; justify-content:center; gap:5px;">
                                <i class="fa fa-search"></i> <span>Search Records</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Alert Notice --}}
                <div class="theme-modal-alert theme-modal-alert-warning">
                    <i class="fa fa-exclamation-triangle text-warning" style="font-size:13px; margin-top:1px;"></i>
                    <div>
                        <b>Important Guard:</b> If payments have already been collected for a fee head, the amount cannot be lowered below the paid sum. Changes to discount, due date, fine %, and refund status auto-save immediately.
                    </div>
                </div>

                {{-- Table Wrap --}}
                <div class="theme-modal-table-wrap">
                    <div style="max-height:280px; overflow-y:auto;">
                        <table class="table table-sm table-bordered table-hover mb-0 text-center" style="font-size:11.5px;">
                            <thead style="position:sticky; top:0; background:#002C54; color:#ffffff; z-index:2;">
                                <tr>
                                    <th style="min-width:140px; padding:6px 8px;" class="text-left">Student Name</th>
                                    <th style="min-width:85px; padding:6px 8px;">Adm No</th>
                                    <th style="min-width:95px; padding:6px 8px;">Mobile</th>
                                    <th style="min-width:170px; padding:6px 8px;" class="text-left">Fee Head & Amount</th>
                                    <th style="width:90px; padding:6px 8px;">Discount (₹)</th>
                                    <th style="min-width:125px; padding:6px 8px;">Due Date</th>
                                    <th style="width:75px; padding:6px 8px;">Fine %</th>
                                    <th style="width:80px; padding:6px 8px;">Refundable</th>
                                    <th style="width:60px; padding:6px 8px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_modification">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted" style="font-size:11.5px;">
                                        <i class="fa fa-search text-muted mr-1"></i> Enter an admission number or select a class and click Search.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer theme-modal-footer">
                <div style="font-size:11px; color:#64748b;">
                    <i class="fa fa-info-circle mr-1"></i> Changes auto-save when you edit a field and click outside
                </div>
                <button type="button" class="dash-btn dash-btn-outline" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 14px; font-size:11px;">
                    <i class="fa fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. In-Column Excel live class filter
    function applyClassFilter() {
        var q = ($('#filter_class').val() || '').toLowerCase().trim();
        var matchCount = 0;

        $('.fm-class-row').each(function() {
            var className = ($(this).data('class') || '').toString().toLowerCase();
            if (!q || className.indexOf(q) !== -1) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#fgEmptyFilterRow').removeClass('d-none');
        } else {
            $('#fgEmptyFilterRow').addClass('d-none');
        }
    }

    $('#filter_class').on('keyup input', applyClassFilter);

    $('#btn_clear_filters, #btn_clear_empty_filters').on('click', function() {
        $('#filter_class').val('');
        $('.fm-class-row').show();
        $('#fgEmptyFilterRow').addClass('d-none');
    });

    // 2. Select All Checkbox on Matrix
    $('#select_group').on('change', function() {
        $('.group_checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.group_checkbox').on('change', function() {
        if ($('.group_checkbox:checked').length === $('.group_checkbox').length) {
            $('#select_group').prop('checked', true);
        } else {
            $('#select_group').prop('checked', false);
        }
    });

    // 3. Delete Single Fee Head Modal Handler
    $(document).on('click', '.deleteData', function() {
        var delete_id = $(this).data('groupname');
        var label = $(this).data('groupname-label') || '';
        $('#delete_id').val(delete_id);
        if (label) {
            $('#delete_head_label').text('"' + label + '"');
        }
    });

    // 4. Quick Assign Students from Class Row
    $(document).on('click', '.btn-assign-class-students', function() {
        var classId = $(this).data('class-id');
        if (classId) {
            $('#bulk_class_type_id').val(classId).trigger('change');
            $('#students_list_modal').modal('show');
        }
    });

    // 5. Bulk Assign Students Modal Logic
    if ($.fn.select2) {
        $('#bulk_fees_master_ids').select2({
            dropdownParent: $('#students_list_modal'),
            placeholder: 'Choose fee head(s)...',
            width: '100%'
        });
    }
    $('#students_list_modal').on('shown.bs.modal', function() {
        if ($.fn.select2) {
            $('#bulk_fees_master_ids').select2({
                dropdownParent: $('#students_list_modal'),
                placeholder: 'Choose fee head(s)...',
                width: '100%'
            });
        }
    });

    $('#all_students').on('click', function() {
        $('.student_select_checkbox').prop('checked', this.checked);
        updateStudentsCount();
    });

    $(document).on('click', '.student_select_checkbox', function() {
        var total = $('.student_select_checkbox').length;
        var checked = $('.student_select_checkbox:checked').length;
        $('#all_students').prop('checked', total > 0 && total === checked);
        updateStudentsCount();
    });

    function updateStudentsCount() {
        var count = $('.student_select_checkbox:checked').length;
        $('#students_selected_counter').text(count);
    }

    $('#bulk_class_type_id').on('change', function() {
        var class_type_id = $(this).val();
        var bulk_admission_no = ($('#bulk_admission_no').val() || '').trim();

        if (!class_type_id) {
            $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-4 text-muted" style="font-size:11.5px;"><i class="fa fa-info-circle text-info mr-1"></i> Please select a class above to load enrolled students.</td></tr>');
            $('#bulk_fees_master_ids').html('').trigger('change');
            $('#students_selected_counter').text('0');
            return;
        }

        getStudents(class_type_id, bulk_admission_no);
        getMasterData(class_type_id);
    });

    $('#bulk_admission_no').on('blur', function() {
        var class_type_id = $('#bulk_class_type_id').val();
        var bulk_admission_no = $(this).val().trim();
        if (class_type_id) {
            getStudents(class_type_id, bulk_admission_no);
        }
    });

    function getStudents(class_type_id, bulk_admission_no) {
        $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Loading students...</td></tr>');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('getStudentsList') }}",
            method: 'POST',
            data: {
                admissionNo: bulk_admission_no,
                class_type_id: class_type_id,
                admission_type_id: ''
            },
            success: function(response) {
                $('#tbody_students_list').html(response);
                $('#all_students').prop('checked', false);
                updateStudentsCount();
            },
            error: function(xhr) {
                console.error('Error fetching students list:', xhr);
                $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-3 text-danger">Failed to load students.</td></tr>');
            }
        });
    }

    function getMasterData(class_type_id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('getMasterData') }}",
            method: 'POST',
            data: {
                class_type_id: class_type_id
            },
            success: function(response) {
                var options = [];
                if (response && response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
                        var name = response[i].fees_group_name || ('Head #' + response[i].id);
                        options.push('<option value="' + response[i].id + '">' + name + '</option>');
                    }
                    $('#bulk_fees_master_ids').html(options.join('')).trigger('change');
                } else {
                    $('#bulk_fees_master_ids').html('').trigger('change');
                }
            },
            error: function(xhr) {
                console.error('Error fetching fee master data:', xhr);
            }
        });
    }

    $('#assignFeesMultiple').on('submit', function(e) {
        var checkedCount = $('.student_select_checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            toastr.error("Please select at least one student.");
            return false;
        }
        var headsCount = $('#bulk_fees_master_ids').val();
        if (!headsCount || headsCount.length === 0) {
            e.preventDefault();
            toastr.error("Please select at least one fee head.");
            return false;
        }
    });

    // 6. Fees Modification Modal Logic
    $('#searchButton').on('click', function() {
        var admissionNo = ($('#admission_modification').val() || '').trim();
        var classTypeId = $('#class_modification').val();

        if (!admissionNo && !classTypeId) {
            toastr.warning('Please select a class or enter an admission number to search.');
            return;
        }

        $('#tbody_modification').html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Searching student fee records...</td></tr>');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('feesModification') }}",
            method: 'POST',
            data: {
                admissionNo: admissionNo,
                class_type_id: classTypeId,
                admission_type_id_modify: ''
            },
            success: function(response) {
                $('#tbody_modification').html(response);
            },
            error: function(xhr) {
                console.error('Error loading modification data:', xhr);
                $('#tbody_modification').html('<tr><td colspan="9" class="text-center py-3 text-danger">Failed to search student fee records.</td></tr>');
            }
        });
    });

    $('#tbody_modification').on('click', '.delete_assigned', function() {
        var fees_assign_detail_id = $(this).data('detail_id');
        var $row = $(this).closest('tr');

        if (!confirm('Are you sure you want to remove this assigned fee head?')) {
            return;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('deleteAssignedFees') }}",
            method: 'POST',
            data: {
                fees_assign_detail_id: fees_assign_detail_id
            },
            success: function() {
                toastr.success('Assigned fee head removed successfully.');
                $row.fadeOut(300, function() { $(this).remove(); });
            },
            error: function(xhr) {
                console.error('Error deleting assigned fee:', xhr);
                toastr.error('Failed to remove assigned fee head.');
            }
        });
    });

    $('#tbody_modification').on('focusout', '.fees_assign_detail', function() {
        var $input = $(this);
        var fees_assign_detail_id = $input.data('detail_id');
        var value = $input.val();
        var old_value = $input.data('old_value');
        var field = $input.attr('name');

        if (field === 'fees_group_amount') {
            var pay_fees = parseFloat($input.data('pay_fees') || 0);
            if (parseFloat(value) < pay_fees) {
                toastr.error('Amount cannot be less than already paid fee: ₹' + pay_fees);
                $input.val(old_value);
                return;
            }
        }

        if (value != old_value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('updateAssignedFees') }}",
                method: 'POST',
                data: {
                    fees_assign_detail_id: fees_assign_detail_id,
                    value: value,
                    field: field
                },
                success: function() {
                    $input.data('old_value', value);
                    toastr.success('Fee updated successfully');
                },
                error: function(xhr) {
                    console.error('Error updating fee:', xhr);
                    toastr.error('Failed to update fee.');
                }
            });
        }
    });
});

function updateRefundFees(checkbox, id) {
    var val = checkbox.checked ? 'yes' : 'no';
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('updateAssignedFees') }}",
        method: 'POST',
        data: {
            fees_assign_detail_id: id,
            value: val,
            field: 'fees_refund'
        },
        success: function() {
            toastr.success('Refund status updated successfully');
        },
        error: function(xhr) {
            console.error('Error updating refund status:', xhr);
            toastr.error('Failed to update refund status');
        }
    });
}
</script>
@endsection
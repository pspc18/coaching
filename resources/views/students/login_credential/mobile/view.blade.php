@extends('layout.mobile_app')

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
    $currentSession = Session::get('session_id');
    $currentSessionName = Session::get('session_name') ?? date('Y');
    $currentBranchName = Session::get('branch_name') ?? 'Main Branch';
    $downloadParams = [
        'class_type_id' => $selectedClassId > 0 ? $selectedClassId : '',
        'student_status' => $selectedStatus,
    ];
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STUDENT CREDENTIALS THEME
   Strictly matched with Admission & Attendance Native Mobile Systems
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* 3 Metrics Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-val {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-box.metric-green .mob-metric-val { color: #34d399; }
.mob-metric-box.metric-amber .mob-metric-val { color: #fbbf24; }
.mob-metric-box.metric-sky .mob-metric-val { color: #38bdf8; }

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 31px;
    padding: 0 8px;
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
    white-space: nowrap;
}
.mob-act-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
}
.mob-act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn:active {
    transform: scale(0.97);
}

/* 2. Quick Status Segmented Tabs */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2.5px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 3px;
}
.mob-seg-btn {
    flex: 1;
    padding: 5px 2px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.mob-seg-pill {
    display: inline-block;
    padding: 1px 5px;
    font-size: 9px;
    border-radius: 10px;
    background: rgba(0,0,0,0.08);
    margin-left: 3px;
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* 3. Search & Filter Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 24px 0 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-clear-search {
    position: absolute;
    right: 6px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    font-size: 11px;
}
.mob-btn-filter {
    height: 30px;
    padding: 0 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.mob-btn-filter.has-filter {
    background: #e0f2fe;
    color: #0369a1;
    border-color: #7dd3fc;
}

/* 4. Student Card Feed */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 70px;
}
.mob-cred-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.mob-cred-card.card-cred-active {
    border-left: 3.5px solid #10b981;
}
.mob-cred-card.card-cred-pending {
    border-left: 3.5px solid #f59e0b;
}
.mob-cred-card.is-selected {
    border-color: #0284c7;
    background: #f0f9ff;
}

/* Card Header */
.mob-card-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}
.mob-check-box {
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-check-box input[type="checkbox"] {
    width: 17px;
    height: 17px;
    cursor: pointer;
    accent-color: #002C54;
}
.mob-avatar-box {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 13px;
    flex-shrink: 0;
}
.mob-head-info {
    flex: 1;
    min-width: 0;
}
.mob-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.mob-student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-status-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1.5px 6px;
    border-radius: 3px;
    white-space: nowrap;
}
.mob-status-pill.pill-active {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.mob-status-pill.pill-pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.mob-pills-row {
    display: flex;
    gap: 4px;
    margin-top: 2px;
    align-items: center;
    flex-wrap: wrap;
}
.mob-class-badge {
    font-size: 9px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-adm-badge {
    font-size: 9px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-uid-badge {
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px dashed #cbd5e1;
}

/* Meta Grid */
.mob-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 5px 7px;
    margin-bottom: 6px;
    font-size: 10.5px;
}
.mob-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    white-space: nowrap;
}
.mob-meta-label {
    color: #64748b;
    font-weight: 600;
}
.mob-meta-val {
    color: #1e293b;
    font-weight: 700;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-phone-link {
    color: #0284c7;
    font-weight: 700;
    text-decoration: none;
}
.mob-wa-btn {
    color: #16a34a;
    font-size: 12px;
    margin-left: 2px;
    text-decoration: none;
}

/* Credentials Grid */
.mob-cred-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    background: #f1f5f9;
    border-radius: 3px;
    padding: 6px 8px;
    border: 1px solid #e2e8f0;
    margin-bottom: 6px;
}
.mob-cred-item {
    display: flex;
    align-items: center;
    gap: 4px;
    min-width: 0;
}
.mob-cred-title {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    white-space: nowrap;
}
.mob-cred-val-wrap {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    min-width: 0;
    flex: 1;
}
.mob-cred-pill {
    font-family: monospace, Courier, sans-serif;
    font-size: 11px;
    font-weight: 700;
    padding: 1.5px 5px;
    border-radius: 3px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 85px;
}
.mob-cred-user {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.mob-cred-pass {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.mob-cred-empty {
    background: #f1f5f9;
    color: #94a3b8;
    font-family: inherit;
    font-size: 10px;
    border: 1px dashed #cbd5e1;
}
.mob-btn-copy, .mob-btn-eye {
    width: 22px;
    height: 22px;
    padding: 0;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    color: #475569;
    font-size: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-btn-copy:active, .mob-btn-eye:active {
    background: #e2e8f0;
}

/* Card Action Button */
.mob-card-footer {
    display: flex;
    gap: 6px;
}
.mob-card-btn-act {
    flex: 1;
    height: 28px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-card-btn-act.btn-gen-now {
    background: #0284c7;
    color: #ffffff;
}
.mob-card-btn-act.btn-edit-pass {
    background: #f8fafc;
    color: #002C54;
    border: 1px solid #cbd5e1;
}
.mob-card-btn-act:active {
    transform: scale(0.98);
}

/* 5. Floating Bottom Bulk Action Bar */
.mob-floating-bar {
    position: fixed;
    bottom: 56px;
    left: 8px;
    right: 8px;
    background: #002C54;
    color: #ffffff;
    padding: 8px 12px;
    border-radius: 4px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3);
    z-index: 1040;
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    animation: slideUpFloat .18s ease-out;
}
@keyframes slideUpFloat {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.mob-float-info {
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-float-badge {
    background: #38bdf8;
    color: #001833;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 11px;
}
.mob-float-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}
.mob-btn-bulk-gen {
    height: 30px;
    padding: 0 10px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-btn-clear-sel {
    background: transparent;
    color: #94a3b8;
    border: none;
    font-size: 11px;
    cursor: pointer;
    padding: 2px 6px;
}

/* ==========================================================================
   UNIFIED NATIVE MOBILE BOTTOM SHEET MODAL ARCHITECTURE
   Fixed viewport scroll container for guaranteed scrolling
   ========================================================================== */
body.sheet-open {
    overflow: hidden !important;
    height: 100vh !important;
    touch-action: none;
}

.mob-sheet-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 15, 30, 0.72);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: opacity .22s ease, visibility .22s ease;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}

.mob-bottom-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 14px 14px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
    z-index: 99995;
    transform: translateY(105%);
    transition: transform .28s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 520px;
    margin: 0 auto;
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}

#mobGenerateSheet {
    height: 85vh;
    max-height: 85vh;
}

#mobFilterSheet {
    max-height: 80vh;
}

#mobDownloadSheet {
    max-height: 60vh;
}

.mob-sheet-drag-handle {
    width: 38px;
    height: 4px;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    margin: 8px auto 4px;
}
.mob-sheet-head {
    flex-shrink: 0;
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 0 14px 10px;
    display: flex;
    flex-direction: column;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-sheet-head-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
    margin: 0;
}
.mob-sheet-close-btn {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
}

/* SCROLL CONTAINER: Forced momentum scrolling with touch-action */
.mob-sheet-scroll-body {
    padding: 12px 14px 28px;
    overflow-y: scroll !important;
    -webkit-overflow-scrolling: touch !important;
    flex: 1 1 auto !important;
    min-height: 0 !important;
    overscroll-behavior: contain;
    touch-action: pan-y;
}

.mob-sheet-sticky-foot {
    flex-shrink: 0;
    padding: 9px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.04);
}

/* Real-Time Live Preview Hero in Generator Modal */
.gen-preview-hero {
    background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
    border: 1px solid #7dd3fc;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 12px;
    box-shadow: 0 1px 4px rgba(2, 132, 199, 0.08);
}
.gen-preview-student-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px dashed #bae6fd;
}
.gen-preview-avatar {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 12px;
    flex-shrink: 0;
}
.gen-preview-student-info {
    flex: 1;
    min-width: 0;
}
.gen-preview-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.gen-preview-meta {
    font-size: 9.5px;
    color: #475569;
    font-weight: 600;
}

/* Split Live Preview Display Boxes */
.gen-preview-boxes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.gen-preview-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
}
.gen-preview-box-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.gen-box-tag {
    font-size: 8.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
}
.gen-formula-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #0284c7;
    background: #e0f2fe;
    padding: 1px 4px;
    border-radius: 2px;
}
.gen-preview-val-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.gen-preview-val-code {
    font-family: monospace, Courier, sans-serif;
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: .02em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.gen-mini-btn {
    width: 22px;
    height: 22px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    color: #475569;
    font-size: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.gen-mini-btn:active {
    background: #e2e8f0;
}

/* Quick Presets Carousel / Pills */
.gen-presets-title {
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.gen-presets-wrap {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 10px;
    -webkit-overflow-scrolling: touch;
}
.gen-preset-pill {
    padding: 5px 8px;
    font-size: 10.5px;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    border-radius: 3px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .12s ease;
}
.gen-preset-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.25);
}

/* Form Settings Cards */
.gen-section-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 9px 10px;
    margin-bottom: 10px;
}
.gen-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 6px;
}
.gen-stepper-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.gen-stepper-label {
    font-size: 11px;
    color: #475569;
    font-weight: 600;
}
.gen-stepper-ctrl {
    display: inline-flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    overflow: hidden;
}
.gen-step-btn {
    width: 26px;
    height: 26px;
    background: #f1f5f9;
    border: none;
    color: #334155;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.gen-step-btn:active {
    background: #e2e8f0;
}
.gen-step-input {
    width: 38px;
    height: 26px;
    border: none;
    text-align: center;
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    outline: none;
    background: #ffffff;
}

/* Segmented Toggle Control */
.gen-mode-toggle {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 3px;
    margin-bottom: 8px;
    gap: 2px;
}
.gen-mode-btn {
    flex: 1;
    padding: 4px 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    background: transparent;
    border: none;
    border-radius: 2px;
    cursor: pointer;
    text-align: center;
}
.gen-mode-btn.active {
    background: #0284c7;
    color: #ffffff;
    font-weight: 800;
}

/* Custom Password Input Box */
.gen-custom-pass-wrap {
    display: flex;
    gap: 4px;
    margin-bottom: 6px;
}
.gen-custom-input {
    flex: 1;
    height: 32px;
    padding: 0 8px;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
}
.gen-custom-input:focus {
    border-color: #0284c7;
}

/* Quick Pin Chips */
.gen-quick-chips {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.gen-quick-chip {
    font-size: 9.5px;
    font-weight: 700;
    background: #e2e8f0;
    color: #334155;
    padding: 2px 6px;
    border-radius: 2px;
    cursor: pointer;
    border: none;
}
.gen-quick-chip:active {
    background: #cbd5e1;
}

/* Sticky Action Buttons */
.btn-sheet-cancel {
    height: 34px;
    padding: 0 12px;
    background: #e2e8f0;
    color: #475569;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
}
.btn-sheet-submit {
    flex: 1;
    height: 34px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-sheet-submit:active {
    background: #0369a1;
}

/* Filter Fields */
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-select, .mob-form-input {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.btn-sheet-reset {
    height: 34px;
    padding: 0 12px;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Export Cards */
.mob-export-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #ffffff;
    text-decoration: none !important;
    color: #0f172a;
    margin-bottom: 8px;
    transition: all .12s ease;
}
.mob-export-card:active {
    background: #f8fafc;
    border-color: #0284c7;
}
.mob-export-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.mob-export-icon {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.icon-pdf { background: #fee2e2; color: #dc2626; }
.icon-excel { background: #dcfce7; color: #16a34a; }
.mob-export-text h6 {
    margin: 0 0 2px;
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
}
.mob-export-text p {
    margin: 0;
    font-size: 10px;
    color: #64748b;
}

/* Toast */
.mob-toast {
    position: fixed;
    bottom: 60px;
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #0f172a;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    z-index: 100000;
    opacity: 0;
    visibility: hidden;
    transition: all .2s ease;
    white-space: nowrap;
}
.mob-toast.show {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
}
</style>
@endsection

@section('content')
<div class="container-fluid px-2 py-1">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-key text-warning"></i> Student Credentials
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-calendar-check-o mr-1"></i> {{ $currentSessionName }}
            </span>
        </div>

        {{-- 3 KPI Metrics Grid --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box metric-sky">
                <div class="mob-metric-tag"><i class="fa fa-users"></i> Total</div>
                <div class="mob-metric-val" id="kpiTotal">{{ $stats['total'] ?? $studentCount }}</div>
            </div>
            <div class="mob-metric-box metric-green">
                <div class="mob-metric-tag"><i class="fa fa-check-circle"></i> Generated</div>
                <div class="mob-metric-val" id="kpiGenerated">{{ $stats['generated'] ?? 0 }}</div>
            </div>
            <div class="mob-metric-box metric-amber">
                <div class="mob-metric-tag"><i class="fa fa-clock-o"></i> Pending</div>
                <div class="mob-metric-val" id="kpiPending">{{ $stats['pending'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Fast Action Bar --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-primary" onclick="toggleSelectAll()">
                <i class="fa fa-check-square-o"></i> <span id="lbl-select-all">Select All</span>
            </button>
            <button type="button" class="mob-act-btn mob-act-btn-light" onclick="openDownloadSheet()">
                <i class="fa fa-download"></i> Export
            </button>
            <button type="button" class="mob-act-btn mob-act-btn-light" onclick="openFilterSheet()">
                <i class="fa fa-sliders"></i> Filter
            </button>
        </div>
    </div>

    {{-- 2. Quick Status Segmented Filter Bar --}}
    <div class="mob-seg-bar">
        <button type="button" class="mob-seg-btn {{ empty($selectedCredStatus) ? 'active' : '' }}" onclick="switchStatusFilter('')">
            All <span class="mob-seg-pill">{{ $stats['total'] ?? $studentCount }}</span>
        </button>
        <button type="button" class="mob-seg-btn {{ $selectedCredStatus === 'generated' ? 'active' : '' }}" onclick="switchStatusFilter('generated')">
            Generated <span class="mob-seg-pill">{{ $stats['generated'] ?? 0 }}</span>
        </button>
        <button type="button" class="mob-seg-btn {{ $selectedCredStatus === 'pending' ? 'active' : '' }}" onclick="switchStatusFilter('pending')">
            Pending <span class="mob-seg-pill">{{ $stats['pending'] ?? 0 }}</span>
        </button>
    </div>

    {{-- 3. Search & Filter Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search student, father, mobile, user..." value="{{ $search['name'] ?? '' }}">
            <button type="button" class="mob-clear-search" id="mobClearSearch" onclick="clearSearch()">&times;</button>
        </div>
        <button type="button" class="mob-btn-filter {{ !empty($selectedClassId) ? 'has-filter' : '' }}" onclick="openFilterSheet()">
            <i class="fa fa-filter"></i>
            <span>{{ !empty($selectedClassId) ? 'Class Applied' : 'Class' }}</span>
        </button>
    </div>

    {{-- 4. Student Card Feed --}}
    <div class="mob-feed-list" id="mobCardFeed">
        @include('students.login_credential.mobile.card_rows', ['data' => $data, 'startIndex' => 0, 'setting' => $setting])
    </div>

</div>

{{-- 5. Floating Bottom Bulk Action Bar --}}
<div class="mob-floating-bar" id="mobFloatingBar">
    <div class="mob-float-info">
        <span class="mob-float-badge" id="lblSelectedCount">0</span>
        <span>Selected</span>
    </div>
    <div class="mob-float-actions">
        <button type="button" class="mob-btn-clear-sel" onclick="clearAllSelections()">Clear</button>
        <button type="button" class="mob-btn-bulk-gen" onclick="openGenerateModal()">
            <i class="fa fa-magic"></i> Generate Credentials
        </button>
    </div>
</div>

{{-- Toast Notification --}}
<div class="mob-toast" id="mobToast">
    <i class="fa fa-check-circle mr-1 text-success"></i> <span id="mobToastMsg">Copied!</span>
</div>

{{-- ==========================================================================
     UNIFIED NATIVE MOBILE BOTTOM SHEETS
     ========================================================================== --}}
<div class="mob-sheet-backdrop" id="mobSheetBackdrop" onclick="closeAllSheets()"></div>

{{-- 6. Filter Bottom Sheet (Direct Form Container) --}}
<form action="{{ url('login_credential_reports') }}" method="GET" class="mob-bottom-sheet" id="mobFilterSheet">
    <input type="hidden" name="layout" value="mobile">
    <input type="hidden" name="credential_status" id="formCredStatus" value="{{ $search['credential_status'] ?? '' }}">

    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h6 class="mob-sheet-title"><i class="fa fa-sliders text-info mr-1"></i> Filter Credentials</h6>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>

    <div class="mob-sheet-scroll-body">
        <div class="mob-form-group">
            <label class="mob-form-label">Class</label>
            <select name="class_type_id" class="mob-form-select">
                <option value="">-- All Classes --</option>
                @if(!empty($classType))
                    @foreach($classType as $class)
                        <option value="{{ $class->id }}" {{ (int)($search['class_type_id'] ?? 0) === (int)$class->id ? 'selected' : '' }}>
                            {{ $class->name ?? ('Class #' . $class->id) }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="mob-form-group">
            <label class="mob-form-label">Student Status</label>
            <select name="student_status" class="mob-form-select">
                <option value="1" {{ (int)($search['student_status'] ?? 1) === 1 ? 'selected' : '' }}>Active Students Only</option>
                <option value="0" {{ isset($search['student_status']) && (string)$search['student_status'] === '0' ? 'selected' : '' }}>Inactive Students Only</option>
                <option value="all" {{ (string)($search['student_status'] ?? '') === 'all' ? 'selected' : '' }}>All (Active &amp; Inactive)</option>
            </select>
        </div>

        <div class="mob-form-group">
            <label class="mob-form-label">Search Keyword</label>
            <input type="text" name="name" class="mob-form-input" placeholder="Search Name, Father, Mobile..." value="{{ $search['name'] ?? '' }}">
        </div>
    </div>

    <div class="mob-sheet-sticky-foot">
        <a href="{{ url('login_credential_reports?layout=mobile') }}" class="btn-sheet-reset">
            <i class="fa fa-refresh mr-1"></i> Reset
        </a>
        <button type="submit" class="btn-sheet-submit">
            <i class="fa fa-check mr-1"></i> Apply Filters
        </button>
    </div>
</form>

{{-- 7. RECREATED: High-Performance Interactive Credential Generator Bottom Sheet (Direct Form Container) --}}
<form action="{{ url('studentUserNameCreate') }}" method="POST" class="mob-bottom-sheet" id="mobGenerateSheet">
    @csrf
    <input type="hidden" name="student_ids" id="hiddenStudentIds" value="">
    <input type="hidden" name="class_type_id" value="{{ $selectedClassId }}">
    <input type="hidden" name="credential_status" value="{{ $selectedCredStatus }}">
    <input type="hidden" name="student_status" value="{{ $selectedStatus }}">
    <input type="hidden" name="username_order" id="mobUsernameOrder" value="name,mobile">
    <input type="hidden" name="password_order" id="mobPasswordOrder" value="mobile">
    <input type="hidden" name="password_type" id="mobPasswordType" value="rule">
    <input type="hidden" name="username_case" id="mobUsernameCase" value="lower">

    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h6 class="mob-sheet-title">
                <i class="fa fa-magic text-warning mr-1"></i> Generate Credentials
            </h6>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>

    <div class="mob-sheet-scroll-body">

        {{-- Real-Time Interactive Live Preview Card --}}
        <div class="gen-preview-hero">
            <div class="gen-preview-student-row">
                <div class="gen-preview-avatar" id="sampleAvatar">S</div>
                <div class="gen-preview-student-info">
                    <div class="gen-preview-name" id="sampleStudentName">Sample Student</div>
                    <div class="gen-preview-meta" id="sampleStudentMeta">Class: 10th-A • Mob: 9876543210 • Adm: 1042</div>
                </div>
            </div>

            {{-- Live Split Values --}}
            <div class="gen-preview-boxes-grid">
                <div class="gen-preview-box">
                    <div class="gen-preview-box-head">
                        <span class="gen-box-tag"><i class="fa fa-user mr-1 text-primary"></i> Username</span>
                        <span class="gen-formula-tag" id="userFormulaTag">Name(4)+Mob(4)</span>
                    </div>
                    <div class="gen-preview-val-row">
                        <span class="gen-preview-val-code" id="lblLiveUsername">samp3210</span>
                        <button type="button" class="gen-mini-btn" onclick="copyLiveText('#lblLiveUsername')" title="Copy Username">
                            <i class="fa fa-clone"></i>
                        </button>
                    </div>
                </div>

                <div class="gen-preview-box">
                    <div class="gen-preview-box-head">
                        <span class="gen-box-tag"><i class="fa fa-lock mr-1 text-warning"></i> Password</span>
                        <span class="gen-formula-tag" id="passFormulaTag">Mob(4)</span>
                    </div>
                    <div class="gen-preview-val-row">
                        <span class="gen-preview-val-code pass-masked" id="lblLivePassword" data-plain="3210">••••</span>
                        <button type="button" class="gen-mini-btn" id="btnToggleLivePass" onclick="toggleLivePasswordPreview()" title="Show/Hide Password">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="gen-mini-btn" onclick="copyLivePassword()" title="Copy Password">
                            <i class="fa fa-clone"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 1-Tap Quick Presets --}}
        <div class="gen-presets-title">
            <i class="fa fa-bolt text-warning mr-1"></i> Quick Presets (1-Tap Selection)
        </div>
        <div class="gen-presets-wrap">
            <button type="button" class="gen-preset-pill active" onclick="applyPreset('mob4', this)">
                <i class="fa fa-phone mr-1"></i> Mobile Last 4
            </button>
            <button type="button" class="gen-preset-pill" onclick="applyPreset('mob6', this)">
                <i class="fa fa-phone mr-1"></i> Mobile Last 6
            </button>
            <button type="button" class="gen-preset-pill" onclick="applyPreset('name_mob', this)">
                <i class="fa fa-user-circle mr-1"></i> Name + Mob
            </button>
            <button type="button" class="gen-preset-pill" onclick="applyPreset('dob', this)">
                <i class="fa fa-birthday-cake mr-1"></i> DOB (DDMMYY)
            </button>
            <button type="button" class="gen-preset-pill" onclick="applyPreset('adm', this)">
                <i class="fa fa-id-card-o mr-1"></i> Admission No
            </button>
            <button type="button" class="gen-preset-pill" onclick="applyPreset('custom', this)">
                <i class="fa fa-lock mr-1"></i> Fixed Password
            </button>
        </div>

        {{-- Password Rule Configuration Card --}}
        <div class="gen-section-card">
            <div class="gen-section-head">
                <span><i class="fa fa-shield text-warning mr-1"></i> Password Configuration</span>
            </div>

            {{-- Mode Switch --}}
            <div class="gen-mode-toggle">
                <button type="button" class="gen-mode-btn active" id="btnModeRule" onclick="switchPasswordMode('rule')">
                    <i class="fa fa-cogs mr-1"></i> Pattern Rule
                </button>
                <button type="button" class="gen-mode-btn" id="btnModeCustom" onclick="switchPasswordMode('custom')">
                    <i class="fa fa-pencil mr-1"></i> Fixed Password
                </button>
            </div>

            {{-- Container for Pattern Rule --}}
            <div id="boxPassRule">
                <div class="mob-form-group mb-2">
                    <label class="mob-form-label">Password Source Formula</label>
                    <select id="selPassSource" class="mob-form-select" onchange="onPassSourceChange()">
                        <option value="mobile" selected>Mobile Digits Only (e.g. 3210)</option>
                        <option value="name,mobile">First Name + Mobile (e.g. rahu3210)</option>
                        <option value="dob">Date of Birth DDMMYY (e.g. 150805)</option>
                        <option value="admission_no">Admission Number (e.g. 1042)</option>
                    </select>
                </div>

                <div class="gen-stepper-row" id="rowPassMobileDigits">
                    <span class="gen-stepper-label">Mobile Digits (from last):</span>
                    <div class="gen-stepper-ctrl">
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobPassMobileDigits', -1, 1, 10)">-</button>
                        <input type="number" name="pass_mobile_digits" id="mobPassMobileDigits" value="4" min="1" max="10" class="gen-step-input" readonly>
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobPassMobileDigits', 1, 1, 10)">+</button>
                    </div>
                </div>

                <div class="gen-stepper-row" id="rowPassNameLetters" style="display: none;">
                    <span class="gen-stepper-label">First Name Letters:</span>
                    <div class="gen-stepper-ctrl">
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobPassNameLetters', -1, 1, 10)">-</button>
                        <input type="number" name="pass_name_letters" id="mobPassNameLetters" value="4" min="1" max="10" class="gen-step-input" readonly>
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobPassNameLetters', 1, 1, 10)">+</button>
                    </div>
                </div>
            </div>

            {{-- Container for Custom Password --}}
            <div id="boxPassCustom" style="display: none;">
                <label class="mob-form-label">Set Fixed Password</label>
                <div class="gen-custom-pass-wrap">
                    <input type="text" name="custom_password" id="mobCustomPassword" class="gen-custom-input" placeholder="e.g. 123456 or School@123" value="123456" oninput="updateLivePreview()">
                    <button type="button" class="gen-mini-btn" style="height: 32px; width: 34px;" onclick="generateRandomPin()" title="Generate Random 6-digit PIN">
                        <i class="fa fa-random"></i>
                    </button>
                </div>
                <div class="gen-quick-chips">
                    <button type="button" class="gen-quick-chip" onclick="setQuickCustom('123456')">123456</button>
                    <button type="button" class="gen-quick-chip" onclick="setQuickCustom('112233')">112233</button>
                    <button type="button" class="gen-quick-chip" onclick="setQuickCustom('password')">password</button>
                    <button type="button" class="gen-quick-chip" onclick="setQuickCustom('000000')">000000</button>
                </div>
            </div>
        </div>

        {{-- Username Rule Configuration Card --}}
        <div class="gen-section-card">
            <div class="gen-section-head">
                <span><i class="fa fa-user text-primary mr-1"></i> Username Configuration</span>
            </div>

            <div class="mob-form-group mb-2">
                <label class="mob-form-label">Username Formula</label>
                <select id="selUserFormula" class="mob-form-select" onchange="onUserFormulaChange()">
                    <option value="name,mobile" selected>First Name + Mobile Digits (e.g. rahu3210)</option>
                    <option value="admission_no">Admission No Only (e.g. 1042)</option>
                    <option value="name,admission_no">First Name + Admission No (e.g. rahu1042)</option>
                    <option value="mobile">Mobile Digits Only (e.g. 9876543210)</option>
                </select>
            </div>

            <div id="boxUserSteppers">
                <div class="gen-stepper-row" id="rowUserNameLetters">
                    <span class="gen-stepper-label">First Name Characters:</span>
                    <div class="gen-stepper-ctrl">
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobNameLetters', -1, 1, 10)">-</button>
                        <input type="number" name="name_letters" id="mobNameLetters" value="4" min="1" max="10" class="gen-step-input" readonly>
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobNameLetters', 1, 1, 10)">+</button>
                    </div>
                </div>

                <div class="gen-stepper-row" id="rowUserMobileDigits">
                    <span class="gen-stepper-label">Mobile Digits (from last):</span>
                    <div class="gen-stepper-ctrl">
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobMobileDigits', -1, 0, 10)">-</button>
                        <input type="number" name="mobile_digits" id="mobMobileDigits" value="4" min="0" max="10" class="gen-step-input" readonly>
                        <button type="button" class="gen-step-btn" onclick="stepVal('mobMobileDigits', 1, 0, 10)">+</button>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between pt-1">
                <span class="gen-stepper-label">Username Case:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active py-0 px-2" id="btnCaseLower" onclick="switchUsernameCase('lower')" style="font-size: 10px; font-weight: 700;">abc (lower)</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="btnCaseUpper" onclick="switchUsernameCase('upper')" style="font-size: 10px; font-weight: 700;">ABC (UPPER)</button>
                </div>
            </div>
        </div>

    </div>

    <div class="mob-sheet-sticky-foot">
        <button type="button" class="btn-sheet-cancel" onclick="closeAllSheets()">Cancel</button>
        <button type="submit" class="btn-sheet-submit" id="mobBtnSubmitGen">
            <i class="fa fa-magic mr-1"></i> <span>Confirm &amp; Generate (<b id="mobBtnCount">0</b>)</span>
        </button>
    </div>
</form>

{{-- 8. Download Export Bottom Sheet --}}
<div class="mob-bottom-sheet" id="mobDownloadSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-drag-handle"></div>
        <div class="mob-sheet-head-top">
            <h6 class="mob-sheet-title"><i class="fa fa-download text-info mr-1"></i> Export Credentials</h6>
            <button type="button" class="mob-sheet-close-btn" onclick="closeAllSheets()">&times;</button>
        </div>
    </div>
    <div class="mob-sheet-scroll-body">
        <p class="text-muted mb-3" style="font-size: 11.5px;">
            Choose preferred format to download login credentials for selected filters:
        </p>

        <a href="{{ route('login-credential-reports.pdf', $downloadParams) }}" target="_blank" class="mob-export-card">
            <div class="mob-export-left">
                <div class="mob-export-icon icon-pdf">
                    <i class="fa fa-file-pdf-o"></i>
                </div>
                <div class="mob-export-text">
                    <h6>Download PDF Document</h6>
                    <p>Printable credential slips with school branding</p>
                </div>
            </div>
            <i class="fa fa-angle-right text-muted"></i>
        </a>

        <a href="{{ route('login-credential-reports.excel', $downloadParams) }}" class="mob-export-card">
            <div class="mob-export-left">
                <div class="mob-export-icon icon-excel">
                    <i class="fa fa-file-excel-o"></i>
                </div>
                <div class="mob-export-text">
                    <h6>Export Excel Sheet (.xlsx)</h6>
                    <p>Spreadsheet for bulk SMS / WhatsApp dispatch</p>
                </div>
            </div>
            <i class="fa fa-angle-right text-muted"></i>
        </a>
    </div>
    <div class="mob-sheet-sticky-foot">
        <button type="button" class="btn-sheet-cancel w-100" onclick="closeAllSheets()">Close</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Global Active Sample Student for Real-Time Preview
var sampleStudent = {
    name: 'Rahul Sharma',
    first_name: 'Rahul',
    class_name: '10th-A',
    mobile: '9876543210',
    admission_no: '1042',
    dob: '150805'
};

// UI Drawer Control
function openFilterSheet() {
    closeAllSheets();
    $('body').addClass('sheet-open');
    $('#mobSheetBackdrop').addClass('show');
    $('#mobFilterSheet').addClass('show');
    $('#mobFilterSheet .mob-sheet-scroll-body').scrollTop(0);
}

function openDownloadSheet() {
    closeAllSheets();
    $('body').addClass('sheet-open');
    $('#mobSheetBackdrop').addClass('show');
    $('#mobDownloadSheet').addClass('show');
}

function closeAllSheets() {
    $('body').removeClass('sheet-open');
    $('#mobSheetBackdrop').removeClass('show');
    $('.mob-bottom-sheet').removeClass('show');
}

// Toast Helper
function showToast(msg) {
    $('#mobToastMsg').text(msg || 'Done!');
    $('#mobToast').addClass('show');
    setTimeout(function() {
        $('#mobToast').removeClass('show');
    }, 1800);
}

// Stepper Helper
function stepVal(inputId, delta, min, max) {
    var $el = $('#' + inputId);
    var cur = parseInt($el.val(), 10) || min;
    var next = cur + delta;
    if (next < min) next = min;
    if (next > max) next = max;
    $el.val(next);
    updateLivePreview();
}

// Quick Preset Handlers
function applyPreset(preset, btn) {
    $('.gen-preset-pill').removeClass('active');
    $(btn).addClass('active');

    if (preset === 'mob4') {
        switchPasswordMode('rule');
        $('#selPassSource').val('mobile');
        $('#mobPassMobileDigits').val(4);
        $('#selUserFormula').val('name,mobile');
        $('#mobNameLetters').val(4);
        $('#mobMobileDigits').val(4);
    } else if (preset === 'mob6') {
        switchPasswordMode('rule');
        $('#selPassSource').val('mobile');
        $('#mobPassMobileDigits').val(6);
        $('#selUserFormula').val('name,mobile');
        $('#mobNameLetters').val(4);
        $('#mobMobileDigits').val(4);
    } else if (preset === 'name_mob') {
        switchPasswordMode('rule');
        $('#selPassSource').val('name,mobile');
        $('#mobPassNameLetters').val(4);
        $('#mobPassMobileDigits').val(4);
        $('#selUserFormula').val('name,mobile');
        $('#mobNameLetters').val(4);
        $('#mobMobileDigits').val(4);
    } else if (preset === 'dob') {
        switchPasswordMode('rule');
        $('#selPassSource').val('dob');
        $('#selUserFormula').val('name,mobile');
        $('#mobNameLetters').val(4);
        $('#mobMobileDigits').val(4);
    } else if (preset === 'adm') {
        switchPasswordMode('rule');
        $('#selPassSource').val('admission_no');
        $('#selUserFormula').val('name,mobile');
        $('#mobNameLetters').val(4);
        $('#mobMobileDigits').val(4);
    } else if (preset === 'custom') {
        switchPasswordMode('custom');
        $('#mobCustomPassword').val('123456');
    }

    onPassSourceChange();
    onUserFormulaChange();
    updateLivePreview();
}

// Password Mode Switcher
function switchPasswordMode(mode) {
    $('#mobPasswordType').val(mode);
    if (mode === 'custom') {
        $('#btnModeCustom').addClass('active');
        $('#btnModeRule').removeClass('active');
        $('#boxPassCustom').show();
        $('#boxPassRule').hide();
    } else {
        $('#btnModeRule').addClass('active');
        $('#btnModeCustom').removeClass('active');
        $('#boxPassRule').show();
        $('#boxPassCustom').hide();
    }
    updateLivePreview();
}

function onPassSourceChange() {
    var src = $('#selPassSource').val();
    $('#mobPasswordOrder').val(src);

    if (src === 'mobile') {
        $('#rowPassMobileDigits').show();
        $('#rowPassNameLetters').hide();
    } else if (src === 'name,mobile') {
        $('#rowPassMobileDigits').show();
        $('#rowPassNameLetters').show();
    } else {
        $('#rowPassMobileDigits').hide();
        $('#rowPassNameLetters').hide();
    }
    updateLivePreview();
}

function onUserFormulaChange() {
    var formula = $('#selUserFormula').val();
    $('#mobUsernameOrder').val(formula);

    if (formula === 'name,mobile') {
        $('#boxUserSteppers').show();
        $('#rowUserNameLetters').show();
        $('#rowUserMobileDigits').show();
    } else if (formula === 'name,admission_no') {
        $('#boxUserSteppers').show();
        $('#rowUserNameLetters').show();
        $('#rowUserMobileDigits').hide();
    } else if (formula === 'mobile') {
        $('#boxUserSteppers').show();
        $('#rowUserNameLetters').hide();
        $('#rowUserMobileDigits').show();
    } else {
        $('#boxUserSteppers').hide();
    }
    updateLivePreview();
}

function switchUsernameCase(c) {
    $('#mobUsernameCase').val(c);
    if (c === 'upper') {
        $('#btnCaseUpper').addClass('active');
        $('#btnCaseLower').removeClass('active');
    } else {
        $('#btnCaseLower').addClass('active');
        $('#btnCaseUpper').removeClass('active');
    }
    updateLivePreview();
}

function generateRandomPin() {
    var pin = Math.floor(100000 + Math.random() * 900000);
    $('#mobCustomPassword').val(pin);
    updateLivePreview();
}

function setQuickCustom(val) {
    $('#mobCustomPassword').val(val);
    updateLivePreview();
}

// REAL-TIME LIVE PREVIEW GENERATOR ENGINE
function updateLivePreview() {
    var s = sampleStudent;
    var cleanFirst = (s.first_name || s.name || 'Sample').toLowerCase().replace(/\s+/g, '');
    var cleanMob = (s.mobile || '9876543210').replace(/\D/g, '');
    var admNo = s.admission_no || '1042';
    var dob = s.dob || '150805';

    // 1. Calculate Username
    var userFormula = $('#selUserFormula').val() || 'name,mobile';
    var nameChars = parseInt($('#mobNameLetters').val(), 10) || 4;
    var mobDigits = parseInt($('#mobMobileDigits').val(), 10) || 4;
    var uCase = $('#mobUsernameCase').val() || 'lower';

    var generatedUser = '';
    var userTag = '';

    if (userFormula === 'name,mobile') {
        var partName = cleanFirst.substring(0, nameChars);
        var partMob = mobDigits > 0 ? cleanMob.substring(cleanMob.length - mobDigits) : '';
        generatedUser = partName + partMob;
        userTag = 'Name(' + nameChars + ')+Mob(' + mobDigits + ')';
    } else if (userFormula === 'admission_no') {
        generatedUser = admNo;
        userTag = 'AdmNo';
    } else if (userFormula === 'name,admission_no') {
        var partName = cleanFirst.substring(0, nameChars);
        generatedUser = partName + admNo;
        userTag = 'Name(' + nameChars + ')+Adm';
    } else if (userFormula === 'mobile') {
        generatedUser = mobDigits > 0 ? cleanMob.substring(cleanMob.length - mobDigits) : cleanMob;
        userTag = 'Mob(' + mobDigits + ')';
    }

    if (uCase === 'upper') {
        generatedUser = generatedUser.toUpperCase();
    } else {
        generatedUser = generatedUser.toLowerCase();
    }

    $('#lblLiveUsername').text(generatedUser || 'user');
    $('#userFormulaTag').text(userTag);

    // 2. Calculate Password
    var passMode = $('#mobPasswordType').val() || 'rule';
    var generatedPass = '';
    var passTag = '';

    if (passMode === 'custom') {
        generatedPass = $('#mobCustomPassword').val() || '123456';
        passTag = 'Fixed Pass';
    } else {
        var passSrc = $('#selPassSource').val() || 'mobile';
        var passMobDigits = parseInt($('#mobPassMobileDigits').val(), 10) || 4;
        var passNameLetters = parseInt($('#mobPassNameLetters').val(), 10) || 4;

        if (passSrc === 'mobile') {
            generatedPass = cleanMob.substring(cleanMob.length - passMobDigits);
            passTag = 'Mob(' + passMobDigits + ')';
        } else if (passSrc === 'name,mobile') {
            var pName = cleanFirst.substring(0, passNameLetters);
            var pMob = cleanMob.substring(cleanMob.length - passMobDigits);
            generatedPass = pName + pMob;
            passTag = 'Name(' + passNameLetters + ')+Mob(' + passMobDigits + ')';
        } else if (passSrc === 'dob') {
            generatedPass = dob;
            passTag = 'DOB (DDMMYY)';
        } else if (passSrc === 'admission_no') {
            generatedPass = admNo;
            passTag = 'AdmNo';
        }
    }

    var $passEl = $('#lblLivePassword');
    $passEl.attr('data-plain', generatedPass);
    if ($passEl.hasClass('pass-masked')) {
        $passEl.text('••••••••');
    } else {
        $passEl.text(generatedPass);
    }
    $('#passFormulaTag').text(passTag);
}

// Toggle Live Password Eye Reveal
function toggleLivePasswordPreview() {
    var $el = $('#lblLivePassword');
    var plain = $el.attr('data-plain') || '';
    if ($el.hasClass('pass-masked')) {
        $el.removeClass('pass-masked').text(plain);
        $('#btnToggleLivePass').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        $el.addClass('pass-masked').text('••••••••');
        $('#btnToggleLivePass').find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function copyLiveText(selector) {
    var txt = $(selector).text();
    copyTextToClipboard(txt);
}

function copyLivePassword() {
    var txt = $('#lblLivePassword').attr('data-plain') || '';
    copyTextToClipboard(txt);
}

function copyTextToClipboard(text) {
    if (!text) return;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copied: ' + text);
        }).catch(function() {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    var temp = $('<input>');
    $('body').append(temp);
    temp.val(text).select();
    document.execCommand('copy');
    temp.remove();
    showToast('Copied: ' + text);
}

// Card Eye & Copy Handlers
$(document).on('click', '.copy-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var text = $(this).attr('data-copy') || '';
    copyTextToClipboard(text);
});

$(document).on('click', '.toggle-pass-btn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $pill = $(this).siblings('.mob-cred-pass');
    var plain = $pill.attr('data-plain') || '';
    var masked = $pill.attr('data-masked') || '••••••••';
    
    if ($pill.hasClass('pass-masked')) {
        $pill.removeClass('pass-masked').text(plain);
        $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        $pill.addClass('pass-masked').text(masked);
        $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
    }
});

// Selection Engine
function updateSelection() {
    var checkedList = $('.student-check:checked');
    var totalChecked = checkedList.length;

    $('.student-check').each(function() {
        var $card = $(this).closest('.mob-cred-card');
        if ($(this).is(':checked')) {
            $card.addClass('is-selected');
        } else {
            $card.removeClass('is-selected');
        }
    });

    $('#lblSelectedCount').text(totalChecked);
    $('#mobBtnCount').text(totalChecked);

    if (totalChecked > 0) {
        $('#mobFloatingBar').css('display', 'flex');
        $('#lbl-select-all').text('Clear All');
    } else {
        $('#mobFloatingBar').hide();
        $('#lbl-select-all').text('Select All');
    }
}

$(document).on('change', '.student-check', updateSelection);

function toggleSelectAll() {
    var totalChecked = $('.student-check:checked').length;
    if (totalChecked > 0) {
        $('.student-check').prop('checked', false);
    } else {
        $('.mob-cred-card:visible .student-check').prop('checked', true);
    }
    updateSelection();
}

function clearAllSelections() {
    $('.student-check').prop('checked', false);
    updateSelection();
}

// Open Modal for Single Student
function openSingleGenerate(btn) {
    var $btn = $(btn);
    var id = $btn.attr('data-id');
    var name = $btn.attr('data-name') || 'Student';
    var firstName = $btn.attr('data-firstname') || name;
    var cls = $btn.attr('data-class') || '-';
    var mob = $btn.attr('data-mobile') || '';
    var adm = $btn.attr('data-adm') || '';
    var dob = $btn.attr('data-dob') || '';

    // Uncheck other checkboxes, check this one
    $('.student-check').prop('checked', false);
    var $cb = $('.student-check[value="' + id + '"]');
    $cb.prop('checked', true);
    updateSelection();

    // Set sample student to this student's actual details!
    sampleStudent = {
        name: name,
        first_name: firstName,
        class_name: cls,
        mobile: mob,
        admission_no: adm,
        dob: dob
    };

    $('#sampleStudentName').text(name);
    $('#sampleStudentMeta').text('Class: ' + cls + ' • Mob: ' + (mob || 'N/A') + (adm ? ' • Adm: ' + adm : ''));
    $('#sampleAvatar').text(name.charAt(0).toUpperCase());

    $('#hiddenStudentIds').val(id);
    $('#mobBtnCount').text('1');

    updateLivePreview();

    closeAllSheets();
    $('body').addClass('sheet-open');
    $('#mobSheetBackdrop').addClass('show');
    $('#mobGenerateSheet').addClass('show');
    $('#mobGenerateSheet .mob-sheet-scroll-body').scrollTop(0);
}

// Open Modal for Bulk Generation
function openGenerateModal() {
    var selectedIds = [];
    var firstChecked = null;

    $('.student-check:checked').each(function() {
        selectedIds.push($(this).val());
        if (!firstChecked) {
            firstChecked = $(this);
        }
    });

    if (selectedIds.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    if (firstChecked) {
        sampleStudent = {
            name: firstChecked.attr('data-name') || 'Selected Student',
            first_name: firstChecked.attr('data-firstname') || firstChecked.attr('data-name') || 'Student',
            class_name: firstChecked.attr('data-class') || '-',
            mobile: firstChecked.attr('data-mobile') || '9876543210',
            admission_no: firstChecked.attr('data-adm') || '',
            dob: firstChecked.attr('data-dob') || ''
        };

        $('#sampleStudentName').text(sampleStudent.name + (selectedIds.length > 1 ? ' (+ ' + (selectedIds.length - 1) + ' others)' : ''));
        $('#sampleStudentMeta').text('Class: ' + sampleStudent.class_name + ' • Mob: ' + sampleStudent.mobile);
        $('#sampleAvatar').text(sampleStudent.name.charAt(0).toUpperCase());
    }

    $('#hiddenStudentIds').val(selectedIds.join(','));
    $('#mobBtnCount').text(selectedIds.length);

    updateLivePreview();

    closeAllSheets();
    $('body').addClass('sheet-open');
    $('#mobSheetBackdrop').addClass('show');
    $('#mobGenerateSheet').addClass('show');
    $('#mobGenerateSheet .mob-sheet-scroll-body').scrollTop(0);
}

// AJAX Form Submission for Instant Generation without Page Reload
$('#mobGenerateSheet').on('submit', function(e) {
    e.preventDefault();
    var $submitBtn = $('#mobBtnSubmitGen');
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Generating...');

    var formData = $(this).serialize() + '&ajax=1';

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(res) {
            $submitBtn.prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Confirm &amp; Generate');
            closeAllSheets();
            showToast(res.message || 'Credentials generated successfully!');

            // Reload or refresh after slight delay
            setTimeout(function() {
                window.location.reload();
            }, 600);
        },
        error: function(xhr) {
            $submitBtn.prop('disabled', false).html('<i class="fa fa-magic mr-1"></i> Confirm &amp; Generate');
            // If server returned HTML redirect or error, submit normally
            $('#mobGenerateSheet').off('submit').submit();
        }
    });
});

// Segment Switcher
function switchStatusFilter(status) {
    $('#formCredStatus').val(status);
    $('#mobFilterSheet').submit();
}

// Client-side Instant Filter with Debounce
var searchTimeout = null;
$('#mobSearchInput').on('input', function() {
    var val = $.trim($(this).val()).toLowerCase();
    if (val.length > 0) {
        $('#mobClearSearch').show();
    } else {
        $('#mobClearSearch').hide();
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        filterCardsClientSide(val);
    }, 150);
});

function clearSearch() {
    $('#mobSearchInput').val('').trigger('input');
}

function filterCardsClientSide(kw) {
    var visibleCount = 0;
    $('.mob-cred-card').each(function() {
        if (!kw) {
            $(this).show();
            visibleCount++;
            return;
        }

        var name = $(this).attr('data-name') || '';
        var cls = $(this).attr('data-class') || '';
        var guard = $(this).attr('data-guardian') || '';
        var mob = $(this).attr('data-mobile') || '';
        var user = $(this).attr('data-username') || '';

        if (name.indexOf(kw) !== -1 || cls.indexOf(kw) !== -1 || guard.indexOf(kw) !== -1 || mob.indexOf(kw) !== -1 || user.indexOf(kw) !== -1) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });
}

// Initial Live Preview Calculation on Document Ready
$(document).ready(function() {
    updateLivePreview();
});
</script>
@endsection

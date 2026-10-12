@extends('layout.app')

@php
    $classType = Helper::classType();
    $examDateDefault = !empty($data->exam_date) ? \Carbon\Carbon::parse($data->exam_date)->format('Y-m-d') : date('Y-m-d');
    
    $totalAssigned = count($AssignExam ?? []);
    $datedCount = 0;
    if (!empty($AssignExam)) {
        foreach ($AssignExam as $item) {
            if (!empty($item->exam_date)) {
                $datedCount++;
            }
        }
    }
    $pendingCount = max(0, $totalAssigned - $datedCount);
    $assignedClassIds = !empty($AssignExam) ? $AssignExam->pluck('class_type_id')->filter()->toArray() : [];
@endphp

@section('title', 'Assign Exam Classes - Arise ERP')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ASSIGN EXAM TO CLASSES (ADMIN SIGNATURE THEME)
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Sky Blue (#0284c7)
   - Layout: Sharp 2px-3px border-radius, compact responsive viewport
   - Dual View: Modern interactive table for desktop, executive card stack for mobile
   - Real-time updates: AJAX Assign, Inline Date Update, Bulk Apply, Unassign
   ========================================================================== */

.assign-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 8px 12px 28px;
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
}
.assign-page * {
    box-sizing: border-box;
}

/* 1. Hero Banner */
.assign-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 3px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,44,84,.14);
    margin-bottom: 10px;
}
.assign-hero-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    display: block;
    margin-bottom: 2px;
    color: #93c5fd;
    font-weight: 700;
}
.assign-hero-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    line-height: 1.25;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.assign-hero-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(255,255,255,.14);
    color: #e0f2fe;
    font-size: 10.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.2);
}
.assign-hero-sub {
    font-size: 11px;
    margin: 3px 0 0;
    color: #cbd5e1;
}
.assign-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* Button Utilities */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 30px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 3px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
    white-space: nowrap;
    line-height: 1.3;
}
.dash-btn-sm {
    height: 26px;
    padding: 0 9px;
    font-size: 11px;
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
    background: rgba(255,255,255,.08);
    color: #ffffff !important;
    border-color: rgba(255,255,255,.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.18);
    border-color: #ffffff;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff !important;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #0284c7;
    border-color: #0284c7;
    box-shadow: 0 2px 4px rgba(2,132,199,.25);
}
.dash-btn-amber {
    background: #d97706;
    color: #ffffff !important;
    border-color: #d97706;
}
.dash-btn-amber:hover {
    background: #b45309;
    border-color: #b45309;
}
.dash-btn-danger {
    background: #fee2e2;
    color: #dc2626 !important;
    border-color: #fecaca;
}
.dash-btn-danger:hover {
    background: #dc2626;
    color: #ffffff !important;
    border-color: #dc2626;
}
.dash-btn-success {
    background: #10b981;
    color: #ffffff !important;
    border-color: #10b981;
}
.dash-btn-success:hover {
    background: #059669;
    border-color: #059669;
}

/* 2. KPI Metrics Strip */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}
@media (max-width: 640px) {
    .kpi-strip {
        grid-template-columns: 1fr;
    }
}
.kpi-card {
    background: #ffffff;
    border-radius: 3px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.kpi-icon-blue   { background: #e0f2fe; color: #0284c7; }
.kpi-icon-green  { background: #dcfce7; color: #16a34a; }
.kpi-icon-amber  { background: #fef3c7; color: #d97706; }
.kpi-meta {
    line-height: 1.15;
    flex-grow: 1;
}
.kpi-val {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    display: block;
}
.kpi-lbl {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 500;
}

/* 3. Operational Section: Assign + Bulk Sync */
.ops-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 10px;
    margin-bottom: 12px;
}
@media (max-width: 900px) {
    .ops-grid {
        grid-template-columns: 1fr;
    }
}
.dash-card {
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    overflow: hidden;
}
.dash-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dash-card-body {
    padding: 10px 12px;
}

/* Inputs & Form Groups */
.form-group-compact {
    margin-bottom: 0;
}
.form-label-compact {
    font-size: 10.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 3px;
    display: block;
}
.form-label-compact .req {
    color: #dc2626;
    margin-left: 2px;
}
.dash-input, .dash-select {
    width: 100%;
    height: 30px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    color: #0f172a;
    transition: all .15s ease;
}
.dash-input:focus, .dash-select:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}
.input-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 8px;
    align-items: flex-end;
}
@media (max-width: 600px) {
    .input-row {
        grid-template-columns: 1fr;
    }
}

.sync-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 8px;
    align-items: flex-end;
}
@media (max-width: 600px) {
    .sync-row {
        grid-template-columns: 1fr;
    }
}
.hint-text {
    font-size: 10px;
    color: #64748b;
    margin-top: 5px;
    display: block;
    line-height: 1.3;
}

/* 4. Assigned Classes Roster Card */
.roster-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.roster-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.roster-badge {
    background: #002C54;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
}
.roster-search {
    position: relative;
    width: 220px;
    max-width: 100%;
}
.roster-search input {
    height: 28px;
    padding-left: 26px;
    padding-right: 8px;
    font-size: 11px;
}
.roster-search i {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 10.5px;
}

/* 5. Desktop Table View */
.desktop-table-view {
    display: block;
    overflow-x: auto;
}
.table-dash {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.table-dash th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 700;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 8px 10px;
    border-bottom: 1px solid #cbd5e1;
    border-top: 1px solid #e2e8f0;
    white-space: nowrap;
}
.table-dash td {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
}
.table-dash tr:hover td {
    background: #f8fafc;
}
.class-tag {
    font-weight: 700;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.class-tag i {
    color: #0284c7;
    font-size: 11px;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 2px;
    line-height: 1.2;
    white-space: nowrap;
}
.status-pill-ok {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.status-pill-pending {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}

.table-date-form {
    display: flex;
    align-items: center;
    gap: 6px;
    max-width: 250px;
}
.table-date-form .dash-input {
    height: 27px;
    font-size: 11px;
}

/* 6. Mobile Cards View */
.mobile-cards-view {
    display: none;
    padding: 8px;
    background: #f8fafc;
}
@media (max-width: 768px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
}
.mobile-card-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    transition: transform .12s ease;
}
.mobile-card-item:last-child {
    margin-bottom: 0;
}
.mc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px dashed #e2e8f0;
}
.mc-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mc-body {
    margin-bottom: 8px;
}
.mc-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

/* Empty State */
.empty-roster {
    text-align: center;
    padding: 35px 15px;
    color: #64748b;
}
.empty-roster i {
    font-size: 32px;
    color: #cbd5e1;
    margin-bottom: 8px;
    display: block;
}
.empty-roster-title {
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
}
.empty-roster-desc {
    font-size: 11px;
    color: #64748b;
}

/* 7. Toast Notifications */
.dash-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 99999;
    min-width: 270px;
    max-width: 380px;
    padding: 10px 14px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(0,0,0,.18);
    display: none;
    align-items: center;
    gap: 9px;
    animation: toastSlide .25s ease;
}
@keyframes toastSlide {
    from { transform: translateX(50px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
.dash-toast-success {
    background: #064e3b;
    color: #ecfdf5;
    border-left: 4px solid #10b981;
}
.dash-toast-error {
    background: #7f1d1d;
    color: #fef2f2;
    border-left: 4px solid #ef4444;
}
.dash-toast-info {
    background: #002C54;
    color: #f0f9ff;
    border-left: 4px solid #0284c7;
}

/* ==========================================================================
   STANDARD ERP THEME CONFIRMATION MODALS (100% SCREEN-CENTERED: modal-dialog-centered)
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
    padding: 18px 16px 14px !important;
    background: #ffffff !important;
}
.theme-delete-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin: 0 auto 10px auto;
    border: 1px solid #fecaca;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
}
.theme-delete-prompt {
    font-size: 12px;
    color: #475569;
    margin-bottom: 4px;
}
.theme-delete-item-title {
    font-size: 14px;
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
    padding: 8px 10px;
    text-align: left;
    font-size: 10.5px;
    color: #9b2c2c;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
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

/* Theme Navy Modal for Bulk Sync */
.theme-navy-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
}
.theme-navy-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin: 0 auto 10px auto;
    border: 1px solid #bae6fd;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.15);
}
.theme-navy-alert {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-left: 3px solid #0284c7;
    border-radius: 2px;
    padding: 8px 10px;
    text-align: left;
    font-size: 10.5px;
    color: #0369a1;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
}
.theme-btn-confirm-apply {
    height: 29px;
    font-size: 11.5px;
    padding: 0 16px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #d97706;
    color: #ffffff;
    border: 1px solid #b45309;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none !important;
    box-shadow: 0 1px 2px rgba(217, 119, 6, 0.2);
}
.theme-btn-confirm-apply:hover {
    background: #b45309;
    border-color: #92400e;
    color: #ffffff;
}

/* Row Flash Highlight */
.row-highlight {
    animation: flashBg 1.5s ease;
}
@keyframes flashBg {
    0% { background-color: #fef08a; }
    100% { background-color: transparent; }
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="assign-page">

        {{-- Toast Notification container --}}
        <div id="dashToast" class="dash-toast">
            <i id="dashToastIcon" class="fa fa-check-circle"></i>
            <span id="dashToastMsg">Action completed successfully.</span>
        </div>

        {{-- Top Hero Banner --}}
        <div class="assign-hero">
            <div>
                <span class="assign-hero-kicker">Examination Panel &bull; Class Assignment</span>
                <h1 class="assign-hero-title">
                    <i class="fa fa-tag"></i>
                    <span>{{ $data->name ?? 'Exam' }}</span>
                    <span class="assign-hero-pill">
                        <i class="fa fa-fingerprint"></i> ID #{{ $data->id ?? '' }}
                    </span>
                    @if(!empty($data->exam_code))
                        <span class="assign-hero-pill">
                            <i class="fa fa-barcode"></i> {{ $data->exam_code }}
                        </span>
                    @endif
                </h1>
                <p class="assign-hero-sub">
                    Manage class assignments, customize exam dates per class, or bulk-sync scheduled dates in real time.
                </p>
            </div>
            <div class="assign-hero-actions">
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-light">
                    <i class="fa fa-arrow-left"></i>
                    <span>Exams List</span>
                </a>
                <a href="{{ url('add/examination_schedule') }}?exam_id={{ $data->id ?? '' }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-calendar-alt"></i>
                    <span>Schedule Sheet</span>
                </a>
            </div>
        </div>

        {{-- Session Flash Alert (for standard non-AJAX submissions fallback) --}}
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show p-2 mb-2" style="font-size:12px; border-radius:3px;" role="alert">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show p-2 mb-2" style="font-size:12px; border-radius:3px;" role="alert">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- KPI Metrics Strip --}}
        <div class="kpi-strip">
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-blue">
                    <i class="fa fa-layer-group"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiTotalAssigned">{{ $totalAssigned }}</span>
                    <span class="kpi-lbl">Total Assigned Classes</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-green">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiDatedCount">{{ $datedCount }}</span>
                    <span class="kpi-lbl">Dates Scheduled</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-amber">
                    <i class="fa fa-clock"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiPendingCount">{{ $pendingCount }}</span>
                    <span class="kpi-lbl">Pending Date Setup</span>
                </div>
            </div>
        </div>

        {{-- Operations Grid: Assign New Class + Bulk Apply Date --}}
        <div class="ops-grid">
            {{-- Card 1: Assign Class Form --}}
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-plus-circle text-primary"></i>
                        <span>Assign Class to Exam</span>
                    </h3>
                    <span class="text-muted" style="font-size:10.5px;">Real-time AJAX Save</span>
                </div>
                <div class="dash-card-body">
                    <form id="assignSingleForm" action="{{ url('assign/exam/'.$data->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="submit_action" value="single">
                        <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
                        <div class="input-row">
                            <div class="form-group-compact">
                                <label class="form-label-compact">Select Class <span class="req">*</span></label>
                                <select class="dash-select" id="new_class_type_id" name="class_type_id" required>
                                    <option value="">-- Choose Class --</option>
                                    @if(!empty($classType))
                                        @foreach($classType as $type)
                                            @php
                                                $isAssigned = in_array($type->id, $assignedClassIds);
                                            @endphp
                                            <option value="{{ $type->id }}" data-name="{{ $type->name ?? '' }}">
                                                {{ $type->name ?? '' }} {{ $isAssigned ? '(Assigned)' : '' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group-compact">
                                <label class="form-label-compact">Exam Date <span class="req">*</span></label>
                                <input type="date" class="dash-input" id="new_exam_date" name="exam_date" value="{{ $examDateDefault }}" required>
                            </div>
                            <div class="form-group-compact">
                                <button type="submit" id="btnAssignSingle" class="dash-btn dash-btn-primary" style="height:30px;">
                                    <i class="fa fa-check"></i>
                                    <span>Assign Class</span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <span class="hint-text">
                        Assign an individual class to this exam. You can customize dates per class or bulk apply below.
                    </span>
                </div>
            </div>

            {{-- Card 2: Bulk Synchronizer --}}
            <div class="dash-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-calendar-check text-warning"></i>
                        <span>Apply Date to All Classes</span>
                    </h3>
                    <span class="text-muted" style="font-size:10.5px;">Bulk Tool</span>
                </div>
                <div class="dash-card-body">
                    <form id="applyDateAllForm" action="{{ url('assign/exam/'.$data->id) }}" method="post">
                        @csrf
                        <input type="hidden" name="submit_action" value="global">
                        <input type="hidden" name="id" value="{{ $data->id ?? '' }}">
                        <div class="sync-row">
                            <div class="form-group-compact">
                                <label class="form-label-compact">Uniform Exam Date <span class="req">*</span></label>
                                <input type="date" class="dash-input" id="bulk_exam_date" name="exam_date" value="{{ $examDateDefault }}" required>
                            </div>
                            <div class="form-group-compact">
                                <button type="button" id="btnOpenBulkModal" class="dash-btn dash-btn-amber" style="height:30px;" data-toggle="modal" data-target="#applyAllConfirmModal">
                                    <i class="fa fa-bolt"></i>
                                    <span>Apply to All</span>
                                </button>
                            </div>
                        </div>
                    </form>
                    <span class="hint-text">
                        Overwrites the exam date for all currently assigned classes with a single click.
                    </span>
                </div>
            </div>
        </div>

        {{-- Roster: Assigned Classes Table & Mobile Stack --}}
        <div class="dash-card">
            <div class="roster-header">
                <h2 class="roster-title">
                    <i class="fa fa-list text-primary"></i>
                    <span>Assigned Classes</span>
                    <span class="roster-badge" id="rosterBadge">{{ $totalAssigned }}</span>
                </h2>
                <div class="roster-search">
                    <i class="fa fa-search"></i>
                    <input type="text" id="rosterSearchInput" class="dash-input" placeholder="Search class name or date...">
                </div>
            </div>

            {{-- Empty State (Hidden when items exist) --}}
            <div id="rosterEmptyState" class="empty-roster" style="{{ $totalAssigned > 0 ? 'display:none;' : '' }}">
                <i class="fa fa-folder-open"></i>
                <div class="empty-roster-title">No Classes Assigned Yet</div>
                <div class="empty-roster-desc">Select a class from the form above and click "Assign Class" to get started.</div>
            </div>

            {{-- 1. Desktop Table View --}}
            <div class="desktop-table-view" id="desktopTableContainer" style="{{ $totalAssigned == 0 ? 'display:none;' : '' }}">
                <table class="table-dash">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">#</th>
                            <th>Class Name</th>
                            <th style="width: 280px;">Scheduled Exam Date</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                            <th style="width: 90px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="desktopTableBody">
                        @if(!empty($AssignExam))
                            @php $i = 1; @endphp
                            @foreach($AssignExam as $item)
                                @php
                                    $hasDate = !empty($item->exam_date);
                                    $formattedDate = $hasDate ? \Carbon\Carbon::parse($item->exam_date)->format('Y-m-d') : '';
                                @endphp
                                <tr id="row-{{ $item->id }}" data-id="{{ $item->id }}" data-class="{{ strtolower($item->class_name ?? '') }}" data-date="{{ $formattedDate }}">
                                    <td style="text-align: center; font-weight: 700; color:#64748b;" class="sr-cell">{{ $i++ }}</td>
                                    <td>
                                        <div class="class-tag">
                                            <i class="fa fa-graduation-cap"></i>
                                            <span class="class-title-text">{{ $item->class_name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <form class="table-date-form inline-date-form" data-id="{{ $item->id }}">
                                            @csrf
                                            <input type="hidden" name="submit_action" value="single">
                                            <input type="hidden" name="assign_id" value="{{ $item->id }}">
                                            <input type="hidden" name="class_type_id" value="{{ $item->class_type_id }}">
                                            <input type="date" name="exam_date" class="dash-input inline-date-input" value="{{ $formattedDate }}" required>
                                            <button type="submit" class="dash-btn dash-btn-sm dash-btn-primary btn-save-inline" title="Save Date">
                                                <i class="fa fa-save"></i>
                                                <span>Save</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($hasDate)
                                            <span class="status-pill status-pill-ok status-indicator">
                                                <i class="fa fa-check-circle"></i> Scheduled
                                            </span>
                                        @else
                                            <span class="status-pill status-pill-pending status-indicator">
                                                <i class="fa fa-clock"></i> Pending Date
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <button type="button" class="dash-btn dash-btn-sm dash-btn-danger btn-unassign-trigger" data-id="{{ $item->id }}" data-name="{{ $item->class_name ?? 'Class' }}" title="Unassign Class">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- 2. Mobile Cards View --}}
            <div class="mobile-cards-view" id="mobileCardsContainer" style="{{ $totalAssigned == 0 ? 'display:none;' : '' }}">
                <div id="mobileCardsBody">
                    @if(!empty($AssignExam))
                        @foreach($AssignExam as $item)
                            @php
                                $hasDate = !empty($item->exam_date);
                                $formattedDate = $hasDate ? \Carbon\Carbon::parse($item->exam_date)->format('Y-m-d') : '';
                            @endphp
                            <div class="mobile-card-item" id="mcard-{{ $item->id }}" data-id="{{ $item->id }}" data-class="{{ strtolower($item->class_name ?? '') }}" data-date="{{ $formattedDate }}">
                                <div class="mc-header">
                                    <div class="mc-title">
                                        <i class="fa fa-graduation-cap text-primary"></i>
                                        <span class="class-title-text">{{ $item->class_name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="mc-status">
                                        @if($hasDate)
                                            <span class="status-pill status-pill-ok status-indicator">
                                                <i class="fa fa-check-circle"></i> Scheduled
                                            </span>
                                        @else
                                            <span class="status-pill status-pill-pending status-indicator">
                                                <i class="fa fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mc-body">
                                    <form class="inline-date-form" data-id="{{ $item->id }}">
                                        @csrf
                                        <input type="hidden" name="submit_action" value="single">
                                        <input type="hidden" name="assign_id" value="{{ $item->id }}">
                                        <input type="hidden" name="class_type_id" value="{{ $item->class_type_id }}">
                                        <div class="d-flex align-items-center" style="gap:6px;">
                                            <input type="date" name="exam_date" class="dash-input inline-date-input" value="{{ $formattedDate }}" style="height:30px;" required>
                                            <button type="submit" class="dash-btn dash-btn-primary btn-save-inline" style="height:30px;">
                                                <i class="fa fa-save"></i>
                                                <span>Save</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="mc-footer">
                                    <small class="text-muted" style="font-size:10px;">
                                        ID #{{ $item->id }}
                                    </small>
                                    <button type="button" class="dash-btn dash-btn-sm dash-btn-danger btn-unassign-trigger" data-id="{{ $item->id }}" data-name="{{ $item->class_name ?? 'Class' }}">
                                        <i class="fa fa-trash"></i>
                                        <span>Unassign</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal 1: Unassign / Delete Confirmation Modal (Arise ERP Standard) --}}
<div class="modal fade" id="unassignModal" tabindex="-1" role="dialog" aria-labelledby="unassignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-delete-modal-dialog" role="document">
        <div class="modal-content theme-delete-modal-content">
            <div class="modal-header theme-delete-modal-header">
                <div class="theme-delete-title-box">
                    <div class="theme-delete-icon">
                        <i class="fa fa-trash"></i>
                    </div>
                    <div class="theme-delete-headings">
                        <h5 class="theme-delete-title" id="unassignModalLabel">Confirm Unassignment</h5>
                        <p class="theme-delete-subtitle">Examination &bull; Class Assignment</p>
                    </div>
                </div>
                <button type="button" class="theme-delete-close" data-dismiss="modal" aria-label="Close" title="Cancel">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form id="unassignForm" action="{{ url('assign/delete/exam') }}" method="post">
                @csrf
                <input type="hidden" id="delete_exam_id" name="exam_id" value="{{ $data->id ?? '' }}">
                <input type="hidden" id="delete_assign_id" name="assign_id" value="">
                
                <div class="modal-body theme-delete-body text-center">
                    <div class="theme-delete-avatar">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p class="theme-delete-prompt">Are you sure you want to unassign this class from the examination?</p>
                    <h6 class="theme-delete-item-title" id="unassignClassNameTitle">Class Name</h6>
                    
                    <div class="theme-delete-info-card">
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-tag mr-1 text-primary"></i> Examination:</span>
                            <span class="badge" style="font-size:10.5px; background:#002C54; color:#fff;">{{ $data->name ?? 'Exam' }}</span>
                        </div>
                        <div class="theme-delete-info-row">
                            <span class="theme-delete-info-label"><i class="fa fa-graduation-cap mr-1 text-primary"></i> Target Class:</span>
                            <strong class="theme-delete-info-val text-primary" id="unassignModalClassVal">-</strong>
                        </div>
                        <div class="theme-delete-info-row border-0 pb-0">
                            <span class="theme-delete-info-label"><i class="fa fa-calendar mr-1 text-primary"></i> Scheduled Date:</span>
                            <span class="theme-delete-info-val font-weight-bold" id="unassignModalDateVal">-</span>
                        </div>
                    </div>

                    <div class="theme-delete-alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span><strong>Warning:</strong> Unassigning will remove this class from this examination schedule and student admit cards.</span>
                    </div>
                </div>

                <div class="theme-delete-footer">
                    <button type="button" class="theme-btn-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="theme-btn-confirm-delete" id="btnConfirmUnassign">
                        <i class="fa fa-trash mr-1"></i> Yes, Unassign Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Bulk Apply Date Confirmation (Arise ERP Standard) --}}
<div class="modal fade" id="applyAllConfirmModal" tabindex="-1" role="dialog" aria-labelledby="bulkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-delete-modal-dialog" role="document">
        <div class="modal-content theme-delete-modal-content">
            <div class="modal-header theme-delete-modal-header theme-navy-modal-header">
                <div class="theme-delete-title-box">
                    <div class="theme-delete-icon" style="background:rgba(255,255,255,.18); border-color:rgba(255,255,255,.3);">
                        <i class="fa fa-calendar-check"></i>
                    </div>
                    <div class="theme-delete-headings">
                        <h5 class="theme-delete-title" id="bulkModalLabel">Bulk Date Synchronization</h5>
                        <p class="theme-delete-subtitle">Apply Date to All Assigned Classes</p>
                    </div>
                </div>
                <button type="button" class="theme-delete-close" data-dismiss="modal" aria-label="Close" title="Cancel">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body theme-delete-body text-center">
                <div class="theme-navy-avatar">
                    <i class="fa fa-bolt"></i>
                </div>
                <p class="theme-delete-prompt">Apply uniform exam date across all assigned classes?</p>
                <h6 class="theme-delete-item-title" id="bulkModalDatePreviewTitle">Date: --</h6>
                
                <div class="theme-delete-info-card">
                    <div class="theme-delete-info-row">
                        <span class="theme-delete-info-label"><i class="fa fa-tag mr-1 text-primary"></i> Examination:</span>
                        <span class="badge" style="font-size:10.5px; background:#002C54; color:#fff;">{{ $data->name ?? 'Exam' }}</span>
                    </div>
                    <div class="theme-delete-info-row">
                        <span class="theme-delete-info-label"><i class="fa fa-calendar mr-1 text-primary"></i> New Exam Date:</span>
                        <strong class="theme-delete-info-val text-primary" id="bulkModalDatePreview">-</strong>
                    </div>
                    <div class="theme-delete-info-row border-0 pb-0">
                        <span class="theme-delete-info-label"><i class="fa fa-layer-group mr-1 text-primary"></i> Total Classes:</span>
                        <span class="theme-delete-info-val font-weight-bold" id="bulkModalCountPreview">{{ $totalAssigned }} Classes</span>
                    </div>
                </div>

                <div class="theme-navy-alert">
                    <i class="fa fa-info-circle" style="margin-top:2px;"></i>
                    <span>This operation will update the exam schedule date for all assigned classes simultaneously.</span>
                </div>
            </div>

            <div class="theme-delete-footer">
                <button type="button" class="theme-btn-cancel" data-dismiss="modal">Cancel</button>
                <button type="button" id="btnConfirmBulkApply" class="theme-btn-confirm-apply">
                    <i class="fa fa-bolt mr-1"></i> Yes, Apply to All
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var examId = "{{ $data->id ?? '' }}";
    var assignBaseUrl = "{{ url('assign/exam') }}/" + examId;
    var deleteUrl = "{{ url('assign/delete/exam') }}";

    // Toast helper
    function showToast(message, type) {
        var $toast = $('#dashToast');
        var $msg = $('#dashToastMsg');
        var $icon = $('#dashToastIcon');

        $toast.removeClass('dash-toast-success dash-toast-error dash-toast-info');
        if (type === 'error') {
            $toast.addClass('dash-toast-error');
            $icon.attr('class', 'fa fa-exclamation-triangle');
        } else if (type === 'info') {
            $toast.addClass('dash-toast-info');
            $icon.attr('class', 'fa fa-info-circle');
        } else {
            $toast.addClass('dash-toast-success');
            $icon.attr('class', 'fa fa-check-circle');
        }

        $msg.text(message);
        $toast.stop(true, true).fadeIn(200);

        setTimeout(function() {
            $toast.fadeOut(300);
        }, 3200);
    }

    // Recalculate KPIs and update badges
    function recalculateKPIs() {
        var total = $('#desktopTableBody tr').length;
        var dated = 0;

        $('#desktopTableBody tr').each(function() {
            var val = $(this).find('input[name="exam_date"]').val();
            if (val && val.trim() !== '') {
                dated++;
            }
        });

        var pending = Math.max(0, total - dated);

        $('#kpiTotalAssigned').text(total);
        $('#kpiDatedCount').text(dated);
        $('#kpiPendingCount').text(pending);
        $('#rosterBadge').text(total);
        $('#bulkModalCountPreview').text(total);

        // Toggle empty state
        if (total === 0) {
            $('#rosterEmptyState').show();
            $('#desktopTableContainer').hide();
            $('#mobileCardsContainer').hide();
        } else {
            $('#rosterEmptyState').hide();
            if (window.innerWidth > 768) {
                $('#desktopTableContainer').show();
            } else {
                $('#mobileCardsContainer').show();
            }
        }

        // Re-index desktop table sr numbers
        $('#desktopTableBody tr').each(function(index) {
            $(this).find('.sr-cell').text(index + 1);
        });
    }

    // 1. Assign Single Class (AJAX)
    $('#assignSingleForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnAssignSingle');
        var classId = $('#new_class_type_id').val();
        var examDate = $('#new_exam_date').val();

        if (!classId) {
            showToast('Please select a class to assign.', 'error');
            return;
        }
        if (!examDate) {
            showToast('Please choose an exam date.', 'error');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Assigning...');

        $.ajax({
            url: assignBaseUrl,
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Assign Class');
                if (res.status === 'success') {
                    showToast(res.message, 'success');

                    var item = res.data;
                    var existingRow = $('#row-' + item.id);

                    if (existingRow.length > 0) {
                        // Update existing row
                        existingRow.find('input[name="exam_date"]').val(item.exam_date);
                        existingRow.attr('data-date', item.exam_date);
                        existingRow.find('.status-indicator').replaceWith(
                            '<span class="status-pill status-pill-ok status-indicator"><i class="fa fa-check-circle"></i> Scheduled</span>'
                        );
                        existingRow.addClass('row-highlight');
                        setTimeout(function(){ existingRow.removeClass('row-highlight'); }, 1500);

                        // Mobile card
                        var mCard = $('#mcard-' + item.id);
                        if (mCard.length > 0) {
                            mCard.find('input[name="exam_date"]').val(item.exam_date);
                            mCard.attr('data-date', item.exam_date);
                            mCard.find('.status-indicator').replaceWith(
                                '<span class="status-pill status-pill-ok status-indicator"><i class="fa fa-check-circle"></i> Scheduled</span>'
                            );
                        }
                    } else {
                        // Append newly assigned row
                        var newDesktopRow = `
                            <tr id="row-${item.id}" data-id="${item.id}" data-class="${(item.class_name || '').toLowerCase()}" data-date="${item.exam_date}" class="row-highlight">
                                <td style="text-align: center; font-weight: 700; color:#64748b;" class="sr-cell">1</td>
                                <td>
                                    <div class="class-tag">
                                        <i class="fa fa-graduation-cap"></i>
                                        <span class="class-title-text">${item.class_name}</span>
                                    </div>
                                </td>
                                <td>
                                    <form class="table-date-form inline-date-form" data-id="${item.id}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="submit_action" value="single">
                                        <input type="hidden" name="assign_id" value="${item.id}">
                                        <input type="hidden" name="class_type_id" value="${item.class_type_id}">
                                        <input type="date" name="exam_date" class="dash-input inline-date-input" value="${item.exam_date}" required>
                                        <button type="submit" class="dash-btn dash-btn-sm dash-btn-primary btn-save-inline" title="Save Date">
                                            <i class="fa fa-save"></i>
                                            <span>Save</span>
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align: center;">
                                    <span class="status-pill status-pill-ok status-indicator">
                                        <i class="fa fa-check-circle"></i> Scheduled
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="dash-btn dash-btn-sm dash-btn-danger btn-unassign-trigger" data-id="${item.id}" data-name="${item.class_name}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#desktopTableBody').append(newDesktopRow);

                        // Mobile card
                        var newMobileCard = `
                            <div class="mobile-card-item row-highlight" id="mcard-${item.id}" data-id="${item.id}" data-class="${(item.class_name || '').toLowerCase()}" data-date="${item.exam_date}">
                                <div class="mc-header">
                                    <div class="mc-title">
                                        <i class="fa fa-graduation-cap text-primary"></i>
                                        <span class="class-title-text">${item.class_name}</span>
                                    </div>
                                    <div class="mc-status">
                                        <span class="status-pill status-pill-ok status-indicator">
                                            <i class="fa fa-check-circle"></i> Scheduled
                                        </span>
                                    </div>
                                </div>
                                <div class="mc-body">
                                    <form class="inline-date-form" data-id="${item.id}">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="submit_action" value="single">
                                        <input type="hidden" name="assign_id" value="${item.id}">
                                        <input type="hidden" name="class_type_id" value="${item.class_type_id}">
                                        <div class="d-flex align-items-center" style="gap:6px;">
                                            <input type="date" name="exam_date" class="dash-input inline-date-input" value="${item.exam_date}" style="height:30px;" required>
                                            <button type="submit" class="dash-btn dash-btn-primary btn-save-inline" style="height:30px;">
                                                <i class="fa fa-save"></i>
                                                <span>Save</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="mc-footer">
                                    <small class="text-muted" style="font-size:10px;">ID #${item.id}</small>
                                    <button type="button" class="dash-btn dash-btn-sm dash-btn-danger btn-unassign-trigger" data-id="${item.id}" data-name="${item.class_name}">
                                        <i class="fa fa-trash"></i>
                                        <span>Unassign</span>
                                    </button>
                                </div>
                            </div>
                        `;
                        $('#mobileCardsBody').append(newMobileCard);

                        setTimeout(function() {
                            $('.row-highlight').removeClass('row-highlight');
                        }, 1500);
                    }

                    // Reset dropdown
                    $('#new_class_type_id').val('');
                    recalculateKPIs();
                } else {
                    showToast(res.message || 'Error assigning class.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Assign Class');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred.';
                showToast(err, 'error');
            }
        });
    });

    // 2. Inline Date Save (both desktop and mobile)
    $(document).on('submit', '.inline-date-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var assignId = $form.data('id');
        var $btn = $form.find('.btn-save-inline');
        var originalBtnHtml = $btn.html();
        var newDate = $form.find('input[name="exam_date"]').val();

        if (!newDate) {
            showToast('Please specify a valid date.', 'error');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: assignBaseUrl,
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                if (res.status === 'success') {
                    showToast(res.message, 'success');

                    // Synchronize both desktop row and mobile card
                    var row = $('#row-' + assignId);
                    var card = $('#mcard-' + assignId);

                    if (row.length > 0) {
                        row.find('input[name="exam_date"]').val(newDate);
                        row.attr('data-date', newDate);
                        row.find('.status-indicator').replaceWith(
                            '<span class="status-pill status-pill-ok status-indicator"><i class="fa fa-check-circle"></i> Scheduled</span>'
                        );
                        row.addClass('row-highlight');
                        setTimeout(function(){ row.removeClass('row-highlight'); }, 1200);
                    }

                    if (card.length > 0) {
                        card.find('input[name="exam_date"]').val(newDate);
                        card.attr('data-date', newDate);
                        card.find('.status-indicator').replaceWith(
                            '<span class="status-pill status-pill-ok status-indicator"><i class="fa fa-check-circle"></i> Scheduled</span>'
                        );
                    }

                    recalculateKPIs();
                } else {
                    showToast(res.message || 'Failed to update date.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update date.';
                showToast(err, 'error');
            }
        });
    });

    // 3. Open Bulk Modal
    $('#btnOpenBulkModal').on('click', function() {
        var chosenDate = $('#bulk_exam_date').val();
        $('#bulkModalDatePreview').text(chosenDate || 'Not selected');
        $('#bulkModalDatePreviewTitle').text('Target Date: ' + (chosenDate || '--'));
    });

    // Bulk Apply To All Confirmation Action (AJAX)
    $('#btnConfirmBulkApply').on('click', function() {
        var $btn = $(this);
        var bulkDate = $('#bulk_exam_date').val();

        if (!bulkDate) {
            showToast('Please select a date first.', 'error');
            $('#applyAllConfirmModal').modal('hide');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Applying...');

        $.ajax({
            url: assignBaseUrl,
            type: 'POST',
            data: $('#applyDateAllForm').serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-bolt mr-1"></i> Yes, Apply to All');
                $('#applyAllConfirmModal').modal('hide');

                if (res.status === 'success') {
                    showToast(res.message, 'success');

                    // Update all date inputs in table and mobile cards
                    $('.inline-date-input').val(bulkDate);
                    $('.status-indicator').replaceWith(
                        '<span class="status-pill status-pill-ok status-indicator"><i class="fa fa-check-circle"></i> Scheduled</span>'
                    );

                    $('#desktopTableBody tr').attr('data-date', bulkDate);
                    $('#mobileCardsBody .mobile-card-item').attr('data-date', bulkDate);

                    recalculateKPIs();
                } else {
                    showToast(res.message || 'Bulk update failed.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-bolt mr-1"></i> Yes, Apply to All');
                $('#applyAllConfirmModal').modal('hide');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred.';
                showToast(err, 'error');
            }
        });
    });

    // 4. Trigger Unassign Modal
    $(document).on('click', '.btn-unassign-trigger', function() {
        var assignId = $(this).data('id');
        var className = $(this).data('name') || 'Class';
        var dateVal = $(this).closest('tr').find('input[name="exam_date"]').val() 
                      || $(this).closest('.mobile-card-item').find('input[name="exam_date"]').val() 
                      || '';

        if (!dateVal || dateVal.trim() === '') {
            dateVal = 'Not Scheduled';
        }

        $('#delete_assign_id').val(assignId);
        $('#unassignClassNameTitle').text(className);
        $('#unassignModalClassVal').text(className);
        $('#unassignModalDateVal').text(dateVal);
        $('#unassignModal').modal('show');
    });

    // Confirm Unassign Action (AJAX)
    $('#unassignForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnConfirmUnassign');
        var assignId = $('#delete_assign_id').val();

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Unassigning...');

        $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Unassign');
                $('#unassignModal').modal('hide');

                if (res.status === 'success') {
                    showToast(res.message, 'success');

                    // Fade out & remove rows
                    $('#row-' + assignId).fadeOut(250, function() {
                        $(this).remove();
                        recalculateKPIs();
                    });
                    $('#mcard-' + assignId).fadeOut(250, function() {
                        $(this).remove();
                    });
                } else {
                    showToast(res.message || 'Unassign failed.', 'error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Yes, Unassign');
                $('#unassignModal').modal('hide');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Server error occurred.';
                showToast(err, 'error');
            }
        });
    });

    // 5. Instant Search Filter (Desktop table + Mobile cards)
    $('#rosterSearchInput').on('input', function() {
        var query = $(this).val().toLowerCase().trim();

        $('#desktopTableBody tr').each(function() {
            var cName = $(this).data('class') || '';
            var cDate = $(this).data('date') || '';
            if (cName.indexOf(query) !== -1 || cDate.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('#mobileCardsBody .mobile-card-item').each(function() {
            var cName = $(this).data('class') || '';
            var cDate = $(this).data('date') || '';
            if (cName.indexOf(query) !== -1 || cDate.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Handle responsive container toggling on window resize
    $(window).on('resize', function() {
        if ($('#desktopTableBody tr').length > 0) {
            if (window.innerWidth <= 768) {
                $('#desktopTableContainer').hide();
                $('#mobileCardsContainer').show();
            } else {
                $('#desktopTableContainer').show();
                $('#mobileCardsContainer').hide();
            }
        }
    });
});
</script>
@endsection

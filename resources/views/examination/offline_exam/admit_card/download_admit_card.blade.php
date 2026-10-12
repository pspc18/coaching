@extends('layout.app')

@php
    $classType = Helper::classType();
    $notes = Helper::getNote();
    $currentClassId = $search['class_type_id'] ?? request('class_type_id', '');
    $currentExamId = $search['exam_id'] ?? request('exam_id', '');
@endphp

@section('title', 'Download Admit Card - Arise ERP')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - DOWNLOAD ADMIT CARD (ADMIN SIGNATURE THEME)
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Sky Blue (#0284c7)
   - Layout: Compact responsive viewport, sharp 2px-3px border-radius
   - Dual View: Modern interactive table for desktop, executive card stack for mobile
   - Real-time AJAX Student Fetch, Live Multi-Select, Instant Filter Search
   ========================================================================== */

.download-admit-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 8px 12px 32px;
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
}
.download-admit-page * {
    box-sizing: border-box;
}

/* 1. Hero Banner */
.admit-hero {
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
.admit-hero-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .06em;
    display: block;
    margin-bottom: 2px;
    color: #93c5fd;
    font-weight: 700;
}
.admit-hero-title {
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
.admit-hero-sub {
    font-size: 11px;
    margin: 3px 0 0;
    color: #cbd5e1;
}
.admit-hero-actions {
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
.dash-btn-success {
    background: #10b981;
    color: #ffffff !important;
    border-color: #10b981;
}
.dash-btn-success:hover {
    background: #059669;
    border-color: #059669;
    box-shadow: 0 2px 4px rgba(16,185,129,.25);
}
.dash-btn-outline-primary {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #cbd5e1;
}
.dash-btn-outline-primary:hover {
    background: #f8fafc;
    border-color: #002C54;
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

/* 3. Filter Card */
.filter-card {
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    padding: 10px 14px;
    margin-bottom: 10px;
}
.filter-form-row {
    display: grid;
    grid-template-columns: 1.2fr 1.2fr auto auto;
    gap: 10px;
    align-items: flex-end;
}
@media (max-width: 800px) {
    .filter-form-row {
        grid-template-columns: 1fr;
    }
}
.form-group-compact {
    margin-bottom: 0;
}
.form-label-compact {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
    display: block;
}
.form-label-compact .req {
    color: #dc2626;
    margin-left: 2px;
}
.dash-select, .dash-input {
    width: 100%;
    height: 32px;
    padding: 0 10px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    color: #0f172a;
    transition: all .15s ease;
}
.dash-select:focus, .dash-input:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}

/* 4. Action Toolbar */
.action-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 12px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.action-toolbar-left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.action-toolbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.selection-count-pill {
    background: #e0f2fe;
    color: #0369a1;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 9px;
    border-radius: 3px;
    border: 1px solid #bae6fd;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.live-search-input {
    position: relative;
    width: 220px;
    max-width: 100%;
}
.live-search-input input {
    height: 30px;
    padding-left: 28px;
    font-size: 11px;
}
.live-search-input i {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}

/* 5. Warning / Empty States */
.missing-schedule-banner {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    border-radius: 3px;
    padding: 12px 14px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.missing-schedule-text {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #92400e;
    font-weight: 600;
}
.empty-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    text-align: center;
    padding: 40px 16px;
    color: #64748b;
}
.empty-box i {
    font-size: 36px;
    color: #cbd5e1;
    margin-bottom: 10px;
    display: block;
}
.empty-box-title {
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
}
.empty-box-desc {
    font-size: 11.5px;
    color: #64748b;
}

/* 6. Desktop Table View */
.desktop-table-view {
    display: block;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    overflow-x: auto;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
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
    white-space: nowrap;
}
.table-dash td {
    padding: 7px 10px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
}
.table-dash tr:hover td {
    background: #f8fafc;
}
.student-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    font-weight: 700;
    flex-shrink: 0;
}
.student-name-box {
    display: flex;
    align-items: center;
    gap: 8px;
}
.student-name-text {
    font-weight: 700;
    color: #002C54;
}
.adm-pill {
    background: #f1f5f9;
    color: #0f172a;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
}
.roll-pill {
    background: #e0f2fe;
    color: #0369a1;
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #bae6fd;
}

/* Custom Checkbox */
.custom-chk {
    width: 17px;
    height: 17px;
    cursor: pointer;
    vertical-align: middle;
    accent-color: #002C54;
}

/* 7. Mobile Cards View */
.mobile-cards-view {
    display: none;
    padding: 0;
}
@media (max-width: 768px) {
    .desktop-table-view {
        display: none !important;
    }
    .mobile-cards-view {
        display: block !important;
    }
}
.mobile-student-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
    transition: all .15s ease;
}
.mobile-student-card.selected {
    border-color: #0284c7;
    background: #f0f9ff;
}
.mc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 6px;
    border-bottom: 1px dashed #e2e8f0;
}
.mc-student-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mc-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    font-size: 11px;
    color: #475569;
}
.mc-body-item {
    line-height: 1.25;
}
.mc-body-label {
    font-size: 9.5px;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    margin-bottom: 1px;
}

/* Mobile Sticky Bottom Action Bar */
.mobile-sticky-action-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 14px;
    box-shadow: 0 -3px 10px rgba(0,0,0,.2);
    z-index: 1040;
    align-items: center;
    justify-content: space-between;
}
@media (max-width: 768px) {
    .mobile-sticky-action-bar.active {
        display: flex;
    }
}

/* 8. Toast Notifications */
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
.dash-toast-warning {
    background: #78350f;
    color: #fef3c7;
    border-left: 4px solid #f59e0b;
}

/* 9. ERP Theme Note Modal */
.theme-modal-dialog {
    max-width: 480px;
    margin: 1.75rem auto;
}
.theme-modal-content {
    border: none;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,.25);
    background: #ffffff;
}
.theme-modal-header {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%) !important;
    color: #ffffff !important;
    padding: 10px 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}
.theme-modal-title {
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.theme-modal-close {
    color: #ffffff !important;
    opacity: 0.9 !important;
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important;
    border-radius: 3px !important;
    width: 26px !important;
    height: 26px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 12px !important;
    padding: 0 !important;
}
.theme-modal-body {
    padding: 16px;
    background: #ffffff;
}
.theme-modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 9px 14px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="download-admit-page">

        {{-- Toast Alert --}}
        <div id="dashToast" class="dash-toast">
            <i id="dashToastIcon" class="fa fa-check-circle"></i>
            <span id="dashToastMsg">Action completed successfully.</span>
        </div>

        {{-- 1. Top Hero Banner --}}
        <div class="admit-hero">
            <div>
                <span class="admit-hero-kicker">Examination Panel &bull; Admit Cards</span>
                <h1 class="admit-hero-title">
                    <i class="fa fa-id-card"></i>
                    <span>Download Admit Cards</span>
                </h1>
                <p class="admit-hero-sub">
                    Filter students by class and examination batch, manage student instruction notes, and generate print-ready admit cards in real time.
                </p>
            </div>
            <div class="admit-hero-actions">
                <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#noteModal">
                    <i class="fa fa-pencil"></i>
                    <span>Instructions Note</span>
                </button>
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-arrow-left"></i>
                    <span>Exams List</span>
                </a>
                <a href="{{ url('add/examination_schedule') }}" class="dash-btn dash-btn-outline">
                    <i class="fa fa-calendar-alt"></i>
                    <span>Schedule Sheet</span>
                </a>
            </div>
        </div>

        {{-- 2. Filter Strip Card --}}
        <div class="filter-card">
            <form id="filterAdmitCardForm" method="GET" action="{{ url('download_admit_card') }}">
                @csrf
                <div class="filter-form-row">
                    <div class="form-group-compact">
                        <label class="form-label-compact">Select Class <span class="req">*</span></label>
                        <select class="dash-select" id="class_type_id" name="class_type_id" required>
                            <option value="">-- Choose Class --</option>
                            @if(!empty($classType))
                                @foreach($classType as $type)
                                    <option value="{{ $type->id }}" {{ ($type->id == $currentClassId) ? 'selected' : '' }}>
                                        {{ $type->name ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group-compact">
                        <label class="form-label-compact">Select Examination <span class="req">*</span></label>
                        <select class="dash-select" id="exam_id" name="exam_id" required>
                            <option value="">-- Choose Examination --</option>
                            @if(!empty($exam))
                                @foreach($exam as $item)
                                    <option value="{{ $item->exam_id }}" {{ ($item->exam_id == $currentExamId) ? 'selected' : '' }}>
                                        {{ $item->exam_name ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group-compact">
                        <button type="button" id="btnFetchStudents" class="dash-btn dash-btn-primary" style="height:32px; width:100%;">
                            <i class="fa fa-search"></i>
                            <span>Fetch Students</span>
                        </button>
                    </div>

                    <div class="form-group-compact">
                        <button type="button" id="btnResetFilters" class="dash-btn dash-btn-outline-primary" style="height:32px; width:100%;">
                            <i class="fa fa-undo"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 3. KPI Metrics Strip --}}
        <div class="kpi-strip">
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-blue">
                    <i class="fa fa-users"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiTotalStudents">0</span>
                    <span class="kpi-lbl">Total Students in Class</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-green">
                    <i class="fa fa-check-square"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiSelectedCount">0</span>
                    <span class="kpi-lbl">Selected for Print</span>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon kpi-icon-amber">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div class="kpi-meta">
                    <span class="kpi-val" id="kpiScheduleStatus" style="font-size:13px; font-weight:700;">Select Batch</span>
                    <span class="kpi-lbl">Schedule Verification</span>
                </div>
            </div>
        </div>

        {{-- 4. Missing Schedule Warning Banner (Shown via JS when schedule not configured) --}}
        <div id="missingScheduleAlert" class="missing-schedule-banner" style="display:none;">
            <div class="missing-schedule-text">
                <i class="fa fa-exclamation-triangle" style="font-size:16px;"></i>
                <span id="missingScheduleMsg">
                    Examination schedule has not been created for this exam yet. Please configure the schedule first before generating admit cards.
                </span>
            </div>
            <a href="{{ url('add/examination_schedule') }}" id="btnGoToSchedule" class="dash-btn dash-btn-sm dash-btn-primary">
                <i class="fa fa-calendar-plus"></i>
                <span>Configure Schedule</span>
            </a>
        </div>

        {{-- 5. Action Toolbar (Print triggers & live search) --}}
        <div id="actionToolbar" class="action-toolbar" style="display:none;">
            <div class="action-toolbar-left">
                <button type="button" id="btnToggleSelectAll" class="dash-btn dash-btn-sm dash-btn-outline-primary">
                    <i class="fa fa-check-square"></i>
                    <span id="lblSelectAll">Select All</span>
                </button>
                <span class="selection-count-pill" id="selectionCountBadge">
                    <i class="fa fa-check"></i> <span id="lblSelectedCount">0</span> / <span id="lblTotalCount">0</span> Selected
                </span>
            </div>
            <div class="action-toolbar-right">
                <div class="live-search-input">
                    <i class="fa fa-search"></i>
                    <input type="text" id="studentSearchInput" class="dash-input" placeholder="Search name, adm or roll...">
                </div>
                <button type="button" id="btnPrintWithSubject" class="dash-btn dash-btn-primary" title="Print with Subject Timetable">
                    <i class="fa fa-print"></i>
                    <span>Print Admit Cards</span>
                </button>
                <button type="button" id="btnPrintWithoutSubject" class="dash-btn dash-btn-outline-primary" title="Print without Subject Timetable">
                    <i class="fa fa-file-alt"></i>
                    <span>Without Subjects</span>
                </button>
            </div>
        </div>

        {{-- 6. Initial Empty State --}}
        <div id="initialEmptyBox" class="empty-box">
            <i class="fa fa-filter"></i>
            <div class="empty-box-title">Select Class &amp; Examination</div>
            <div class="empty-box-desc">Choose a class and exam batch above to view eligible students and generate admit cards.</div>
        </div>

        {{-- 7. No Students Found State --}}
        <div id="noStudentsBox" class="empty-box" style="display:none;">
            <i class="fa fa-user-times"></i>
            <div class="empty-box-title">No Active Students Found</div>
            <div class="empty-box-desc">There are no active admissions enrolled in the selected class for this session.</div>
        </div>

        {{-- 8. Desktop Table View --}}
        <div class="desktop-table-view" id="desktopTableContainer" style="display:none;">
            <table class="table-dash">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">
                            <input type="checkbox" id="masterCheckbox" class="custom-chk" title="Select / Deselect All">
                        </th>
                        <th style="width: 45px; text-align: center;">#</th>
                        <th style="width: 120px;">Admission No</th>
                        <th>Student Name</th>
                        <th style="width: 130px;">Student Mobile</th>
                        <th>Father Details</th>
                        <th style="width: 100px; text-align: center;">Roll No</th>
                        <th style="width: 70px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    {{-- Dynamically populated via AJAX --}}
                </tbody>
            </table>
        </div>

        {{-- 9. Mobile Cards View --}}
        <div class="mobile-cards-view" id="mobileCardsContainer" style="display:none;">
            <div id="mobileCardsBody">
                {{-- Dynamically populated via AJAX --}}
            </div>
        </div>

        {{-- 10. Mobile Sticky Action Bar --}}
        <div class="mobile-sticky-action-bar" id="mobileStickyActionBar">
            <div>
                <strong id="mobSelectedCountText">0 Selected</strong>
                <small class="d-block text-light" style="font-size:10px;">Ready to generate</small>
            </div>
            <div class="d-flex align-items-center" style="gap:6px;">
                <button type="button" class="dash-btn dash-btn-light btn-mob-print-with" style="height:32px;">
                    <i class="fa fa-print"></i> <span>Print</span>
                </button>
                <button type="button" class="dash-btn dash-btn-outline btn-mob-print-without" style="height:32px;" title="Without Subjects">
                    <i class="fa fa-file-alt"></i>
                </button>
            </div>
        </div>

    </div>
</div>

{{-- Modal: Admit Card Instructions Note --}}
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered theme-modal-dialog">
        <div class="modal-content theme-modal-content">
            <div class="modal-header theme-modal-header">
                <h5 class="theme-modal-title">
                    <i class="fa fa-pencil"></i>
                    <span>Admit Card Instructions Note</span>
                </h5>
                <button type="button" class="theme-modal-close" data-dismiss="modal">&times;</button>
            </div>
            <form id="noteForm" action="{{ url('admit_card_notes') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $notes['id'] ?? '' }}">
                <div class="theme-modal-body">
                    <label class="form-label-compact">Instructions / Guidelines Printed on Admit Cards:</label>
                    <textarea class="form-control" name="note" id="noteContent" rows="6" placeholder="Enter student exam guidelines, reporting time, examination rules, etc." style="font-size:12px; resize:vertical;">{{ $notes['note'] ?? '' }}</textarea>
                    <small class="text-muted d-block mt-2">
                        These instructions will automatically print on all student admit cards for this branch.
                    </small>
                </div>
                <div class="theme-modal-footer">
                    <button type="button" class="dash-btn dash-btn-outline-primary" data-dismiss="modal">Close</button>
                    <button type="submit" id="btnSaveNote" class="dash-btn dash-btn-primary">
                        <i class="fa fa-save"></i>
                        <span>Save Note</span>
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
    var baseurl = "{{ url('/') }}";
    var currentClassId = "{{ $currentClassId }}";
    var currentExamId = "{{ $currentExamId }}";

    // Toast helper
    function showToast(message, type) {
        var $toast = $('#dashToast');
        var $msg = $('#dashToastMsg');
        var $icon = $('#dashToastIcon');

        $toast.removeClass('dash-toast-success dash-toast-error dash-toast-warning');
        if (type === 'error') {
            $toast.addClass('dash-toast-error');
            $icon.attr('class', 'fa fa-exclamation-triangle');
        } else if (type === 'warning') {
            $toast.addClass('dash-toast-warning');
            $icon.attr('class', 'fa fa-exclamation-circle');
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

    // 1. Dependent Dropdown: When Class changes, fetch Exams for that Class
    $('#class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#exam_id');

        $examSelect.html('<option value="">-- Loading Exams... --</option>');
        resetResultsUI();

        if (!classId) {
            $examSelect.html('<option value="">-- Choose Examination --</option>');
            return;
        }

        $.ajax({
            url: baseurl + '/examData/' + classId,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $examSelect.html(data);
                // If previous exam was selected, restore or trigger
                if (currentExamId) {
                    $examSelect.val(currentExamId);
                    currentExamId = '';
                    fetchStudentsRealTime();
                }
            },
            error: function() {
                $examSelect.html('<option value="">-- Failed to Load Exams --</option>');
                showToast('Failed to load examinations for this class.', 'error');
            }
        });
    });

    // When Exam changes, auto-fetch students
    $('#exam_id').on('change', function() {
        if ($(this).val()) {
            fetchStudentsRealTime();
        } else {
            resetResultsUI();
        }
    });

    // Fetch button click
    $('#btnFetchStudents').on('click', function() {
        fetchStudentsRealTime();
    });

    // Reset button click
    $('#btnResetFilters').on('click', function() {
        $('#class_type_id').val('');
        $('#exam_id').html('<option value="">-- Choose Examination --</option>');
        resetResultsUI();
    });

    function resetResultsUI() {
        $('#initialEmptyBox').show();
        $('#noStudentsBox').hide();
        $('#missingScheduleAlert').hide();
        $('#actionToolbar').hide();
        $('#desktopTableContainer').hide();
        $('#mobileCardsContainer').hide();
        $('#mobileStickyActionBar').removeClass('active');
        $('#studentTableBody').empty();
        $('#mobileCardsBody').empty();

        $('#kpiTotalStudents').text('0');
        $('#kpiSelectedCount').text('0');
        $('#kpiScheduleStatus').text('Select Batch').css('color', '#0f172a');
        $('#masterCheckbox').prop('checked', false);
    }

    // 2. Real-time Student Fetching via AJAX
    function fetchStudentsRealTime() {
        var classId = $('#class_type_id').val();
        var examId = $('#exam_id').val();

        if (!classId) {
            showToast('Please select a class first.', 'warning');
            return;
        }
        if (!examId) {
            showToast('Please select an examination.', 'warning');
            return;
        }

        var $btn = $('#btnFetchStudents');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: "{{ url('download_admit_card') }}",
            type: 'GET',
            data: {
                class_type_id: classId,
                exam_id: examId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Fetch Students');
                $('#initialEmptyBox').hide();

                if (res.status === 'warning' && !res.has_schedule) {
                    // Schedule not configured
                    $('#missingScheduleMsg').text(res.message);
                    $('#btnGoToSchedule').attr('href', baseurl + '/add/examination_schedule?class_type_id=' + classId + '&exam_id=' + examId);
                    $('#missingScheduleAlert').show();

                    $('#noStudentsBox').hide();
                    $('#actionToolbar').hide();
                    $('#desktopTableContainer').hide();
                    $('#mobileCardsContainer').hide();
                    $('#mobileStickyActionBar').removeClass('active');

                    $('#kpiTotalStudents').text('0');
                    $('#kpiSelectedCount').text('0');
                    $('#kpiScheduleStatus').text('Schedule Missing').css('color', '#d97706');
                    return;
                }

                $('#missingScheduleAlert').hide();
                $('#kpiScheduleStatus').text('Schedule OK').css('color', '#16a34a');

                if (res.students && res.students.length > 0) {
                    $('#noStudentsBox').hide();
                    renderStudents(res.students, classId, examId);
                } else {
                    $('#noStudentsBox').show();
                    $('#actionToolbar').hide();
                    $('#desktopTableContainer').hide();
                    $('#mobileCardsContainer').hide();
                    $('#mobileStickyActionBar').removeClass('active');
                    $('#kpiTotalStudents').text('0');
                    $('#kpiSelectedCount').text('0');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Fetch Students');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Error fetching students.';
                showToast(err, 'error');
            }
        });
    }

    // 3. Render Students into Table & Mobile Stack
    function renderStudents(students, classId, examId) {
        var tableRows = '';
        var mobileCards = '';
        var total = students.length;

        students.forEach(function(s, idx) {
            var fullName = ((s.first_name || '') + ' ' + (s.last_name || '')).trim() || 'Student';
            var initials = (s.first_name ? s.first_name.charAt(0) : 'S').toUpperCase();
            var admNo = s.admissionNo || '--';
            var mobile = s.mobile || '--';
            var father = s.father_name || '--';
            var fMobile = s.father_mobile || '--';
            var rollNo = s.roll_no || '-';

            // Desktop Row
            tableRows += `
                <tr id="srow-${s.id}" data-id="${s.id}" data-name="${fullName.toLowerCase()}" data-adm="${admNo.toLowerCase()}" data-roll="${rollNo.toLowerCase()}" data-father="${father.toLowerCase()}">
                    <td style="text-align: center;">
                        <input type="checkbox" class="custom-chk student-chk" value="${s.id}" checked>
                    </td>
                    <td style="text-align: center; color:#64748b; font-weight:700;">${idx + 1}</td>
                    <td>
                        <span class="adm-pill">ADM-${admNo}</span>
                    </td>
                    <td>
                        <div class="student-name-box">
                            <span class="student-avatar">${initials}</span>
                            <div>
                                <span class="student-name-text">${fullName}</span>
                                <div class="text-muted" style="font-size:10px;">ID #${s.id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:600; color:#334155;">${mobile}</span>
                    </td>
                    <td>
                        <div style="font-weight:600; color:#1e293b;">${father}</div>
                        <div class="text-muted" style="font-size:10px;">${fMobile !== '--' ? '<i class="fa fa-phone"></i> ' + fMobile : ''}</div>
                    </td>
                    <td style="text-align: center;">
                        <span class="roll-pill">${rollNo}</span>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" class="dash-btn dash-btn-sm dash-btn-primary btn-single-print" data-id="${s.id}" title="Print Admit Card for ${fullName}">
                            <i class="fa fa-print"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Mobile Card
            mobileCards += `
                <div class="mobile-student-card selected" id="mcard-${s.id}" data-id="${s.id}" data-name="${fullName.toLowerCase()}" data-adm="${admNo.toLowerCase()}" data-roll="${rollNo.toLowerCase()}" data-father="${father.toLowerCase()}">
                    <div class="mc-header">
                        <div class="mc-student-info">
                            <input type="checkbox" class="custom-chk student-chk" value="${s.id}" checked style="width:20px; height:20px;">
                            <span class="student-avatar" style="width:28px; height:28px; font-size:11px;">${initials}</span>
                            <div>
                                <span class="student-name-text" style="font-size:12.5px;">${fullName}</span>
                                <div class="text-muted" style="font-size:10px;">ADM-${admNo}</div>
                            </div>
                        </div>
                        <button type="button" class="dash-btn dash-btn-sm dash-btn-primary btn-single-print" data-id="${s.id}" title="Print Admit Card">
                            <i class="fa fa-print"></i>
                        </button>
                    </div>
                    <div class="mc-body">
                        <div class="mc-body-item">
                            <span class="mc-body-label">Roll Number</span>
                            <span class="roll-pill">${rollNo}</span>
                        </div>
                        <div class="mc-body-item">
                            <span class="mc-body-label">Student Contact</span>
                            <strong>${mobile}</strong>
                        </div>
                        <div class="mc-body-item">
                            <span class="mc-body-label">Father Name</span>
                            <span>${father}</span>
                        </div>
                        <div class="mc-body-item">
                            <span class="mc-body-label">Father Contact</span>
                            <span>${fMobile}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#studentTableBody').html(tableRows);
        $('#mobileCardsBody').html(mobileCards);

        $('#kpiTotalStudents').text(total);
        $('#lblTotalCount').text(total);
        $('#masterCheckbox').prop('checked', true);

        $('#actionToolbar').show();
        if (window.innerWidth <= 768) {
            $('#desktopTableContainer').hide();
            $('#mobileCardsContainer').show();
        } else {
            $('#desktopTableContainer').show();
            $('#mobileCardsContainer').hide();
        }

        updateSelectionState();
    }

    // 4. Selection Management
    function updateSelectionState() {
        var total = $('#studentTableBody tr').length;
        var checkedCount = 0;

        $('#studentTableBody tr').each(function() {
            var $chk = $(this).find('.student-chk');
            if ($chk.prop('checked')) {
                checkedCount++;
            }
        });

        $('#kpiSelectedCount').text(checkedCount);
        $('#lblSelectedCount').text(checkedCount);
        $('#mobSelectedCountText').text(checkedCount + ' Selected');

        // Sync card classes on mobile
        $('.mobile-student-card').each(function() {
            var isChecked = $(this).find('.student-chk').prop('checked');
            if (isChecked) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });

        // Master checkbox state
        if (checkedCount === 0) {
            $('#masterCheckbox').prop('checked', false).prop('indeterminate', false);
            $('#lblSelectAll').text('Select All');
            $('#mobileStickyActionBar').removeClass('active');
        } else if (checkedCount === total) {
            $('#masterCheckbox').prop('checked', true).prop('indeterminate', false);
            $('#lblSelectAll').text('Deselect All');
            if (window.innerWidth <= 768) $('#mobileStickyActionBar').addClass('active');
        } else {
            $('#masterCheckbox').prop('indeterminate', true);
            $('#lblSelectAll').text('Select All');
            if (window.innerWidth <= 768) $('#mobileStickyActionBar').addClass('active');
        }
    }

    // Toggle individual checkbox (desktop & mobile synchronized)
    $(document).on('change', '.student-chk', function() {
        var sId = $(this).val();
        var isChecked = $(this).prop('checked');

        // Sync both desktop and mobile checkbox for that student
        $('#srow-' + sId).find('.student-chk').prop('checked', isChecked);
        $('#mcard-' + sId).find('.student-chk').prop('checked', isChecked);

        updateSelectionState();
    });

    // Master Checkbox Toggle
    $('#masterCheckbox').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.student-chk').prop('checked', isChecked);
        updateSelectionState();
    });

    // Select All / Deselect All Button
    $('#btnToggleSelectAll').on('click', function() {
        var total = $('#studentTableBody tr').length;
        var checkedCount = parseInt($('#lblSelectedCount').text()) || 0;
        var shouldCheck = checkedCount < total;

        $('.student-chk').prop('checked', shouldCheck);
        $('#masterCheckbox').prop('checked', shouldCheck).prop('indeterminate', false);
        updateSelectionState();
    });

    // 5. Printing Action Handlers
    function getSelectedStudentIds() {
        var ids = [];
        $('#studentTableBody tr').each(function() {
            var $chk = $(this).find('.student-chk');
            if ($chk.prop('checked')) {
                ids.push($chk.val());
            }
        });
        return ids;
    }

    function triggerPrint(withoutSubject) {
        var classId = $('#class_type_id').val();
        var examId = $('#exam_id').val();
        var selectedIds = getSelectedStudentIds();

        if (!classId || !examId) {
            showToast('Please select Class and Examination.', 'warning');
            return;
        }

        if (selectedIds.length === 0) {
            showToast('Please select at least one student to print admit cards.', 'warning');
            return;
        }

        var idString = selectedIds.join(',');
        var routePath = withoutSubject ? '/without_subject_admit_card/' : '/exam_admit_card/';
        var targetUrl = baseurl + routePath + examId + '/' + classId + '/' + idString;

        window.open(targetUrl, '_blank');
    }

    // Print with Subjects button
    $('#btnPrintWithSubject, .btn-mob-print-with').on('click', function() {
        triggerPrint(false);
    });

    // Print without Subjects button
    $('#btnPrintWithoutSubject, .btn-mob-print-without').on('click', function() {
        triggerPrint(true);
    });

    // Single student print button
    $(document).on('click', '.btn-single-print', function() {
        var classId = $('#class_type_id').val();
        var examId = $('#exam_id').val();
        var sId = $(this).data('id');

        if (!classId || !examId) {
            showToast('Please select Class and Examination.', 'warning');
            return;
        }

        var targetUrl = baseurl + '/exam_admit_card/' + examId + '/' + classId + '/' + sId;
        window.open(targetUrl, '_blank');
    });

    // 6. Live Search Filter
    $('#studentSearchInput').on('input', function() {
        var query = $(this).val().toLowerCase().trim();

        $('#studentTableBody tr').each(function() {
            var name = $(this).data('name') || '';
            var adm = $(this).data('adm') || '';
            var roll = $(this).data('roll') || '';
            var father = $(this).data('father') || '';

            if (name.indexOf(query) !== -1 || adm.indexOf(query) !== -1 || roll.indexOf(query) !== -1 || father.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('#mobileCardsBody .mobile-student-card').each(function() {
            var name = $(this).data('name') || '';
            var adm = $(this).data('adm') || '';
            var roll = $(this).data('roll') || '';
            var father = $(this).data('father') || '';

            if (name.indexOf(query) !== -1 || adm.indexOf(query) !== -1 || roll.indexOf(query) !== -1 || father.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 7. Save Instructions Note via AJAX
    $('#noteForm').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSaveNote');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: "{{ url('admit_card_notes') }}",
            type: 'POST',
            data: $form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Note');
                $('#noteModal').modal('hide');
                showToast(res.message || 'Admit card instructions note updated successfully.', 'success');
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Note');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to save note.';
                showToast(err, 'error');
            }
        });
    });

    // 8. Auto-load if parameters present in URL initially
    if (currentClassId) {
        $('#class_type_id').trigger('change');
    }

    // Responsive toggle on resize
    $(window).on('resize', function() {
        if ($('#studentTableBody tr').length > 0) {
            if (window.innerWidth <= 768) {
                $('#desktopTableContainer').hide();
                $('#mobileCardsContainer').show();
            } else {
                $('#desktopTableContainer').show();
                $('#mobileCardsContainer').hide();
                $('#mobileStickyActionBar').removeClass('active');
            }
        }
    });
});
</script>
@endsection

@php
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $currentRoleId = (int) Session::get('role_id');
    $currentTeacherId = Session::get('teacher_id');
    $isStudent = ($currentRoleId === 3);
    $permission = Helper::permissioncheck(10);
    $canCreate = !$isStudent && (($permission->add ?? true));
    $today = date('Y-m-d');

    $totalCount = $totalCount ?? $data->total();
    $startIndex = $startIndex ?? ($data->firstItem() ? ($data->firstItem() - 1) : 0);
    $currentPage = $currentPage ?? $data->currentPage();
    $lastPage = $lastPage ?? $data->lastPage();
    $perPage = $perPage ?? $data->perPage();

    // Calculate glance metrics from data collection
    $activeCount = 0;
    $dueTodayCount = 0;
    $overdueCount = 0;
    foreach($data as $hw) {
        $subDate = !empty($hw->submission_date) ? date('Y-m-d', strtotime($hw->submission_date)) : '';
        if (!empty($subDate)) {
            if ($subDate > $today) $activeCount++;
            elseif ($subDate === $today) $dueTodayCount++;
            else $overdueCount++;
        }
    }
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE HOMEWORK DIRECTORY STYLES
   - Aligned with Arise ERP Mobile Design System (admissionView, expenseView)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Glassmorphic Hero Card with Live KPI Stats & Action Bar
   - Compact Search & Horizontal Filter Chips Toolbar
   - High-Performance Card Feed with Color-Coded Accent Borders
   - Submission Progress Bar (Distinct Student Submissions / Class Total)
   - Standardized 29px/30px Action Buttons with Theme Palettes
   - Native Slide-Up Detail Sheet & Filter Sheet Modals
   ========================================================================== */

/* 1. Signature Glassmorphic Hero Card */
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
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
    transition: background .15s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-metric-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-total { color: #38bdf8; }
.mob-metric-val.val-active { color: #4ade80; }
.mob-metric-val.val-overdue { color: #f87171; }
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-top: 1px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Fast Actions Bar - Exact 2-Column Full Width Grid */
.mob-actions-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    width: 100%;
}
.mob-act-btn {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-act-btn:active {
    transform: scale(0.97);
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    border: 1px solid #0284c7;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

/* 2. Compact Search & Horizontal Filter Chips Toolbar */
.mob-filter-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mob-search-row {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.mob-search-input-wrap {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i.fa-search {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    transition: all .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}

/* Horizontal Filter Chips */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .12s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}

/* 3. Native Homework Cards Feed */
.hw-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 75px; /* clearance for bottom pagination */
}
.hw-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: all .15s ease;
    border-left: 3.5px solid #0284c7;
}
.hw-mob-card.border-overdue { border-left-color: #dc2626; }
.hw-mob-card.border-today { border-left-color: #f59e0b; }
.hw-mob-card.border-active { border-left-color: #10b981; }

/* Card Header */
.hw-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}
.hw-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
    background: #e0f2fe;
    color: #0284c7;
}
.hw-avatar-box.avatar-overdue {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}
.hw-avatar-box.avatar-today {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
}
.hw-avatar-box.avatar-active {
    background: #dcfce7;
    color: #16a34a;
    border-color: #bbf7d0;
}

.hw-header-info {
    flex: 1;
    overflow: hidden;
}
.hw-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.hw-mob-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.hw-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.hw-type-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1.5px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.badge-type-dpp { background: #ede9fe; color: #6d28d9; border-color: #ddd6fe; }
.badge-type-worksheet { background: #fef3c7; color: #b45309; border-color: #fde68a; }
.badge-type-pyq { background: #ffe4e6; color: #be123c; border-color: #fecdd3; }

/* Status Badges */
.hw-status-pill {
    font-size: 8.5px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.status-pill-overdue {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.status-pill-today {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.status-pill-active {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

/* Card Body Meta */
.hw-card-body {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 6px;
}
.hw-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    font-size: 11px;
}
.hw-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #334155;
}
.hw-meta-item i {
    color: #64748b;
    font-size: 10px;
    width: 12px;
    text-align: center;
    flex-shrink: 0;
}
.hw-meta-item strong {
    color: #0f172a;
}

/* Submission Progress Bar */
.hw-progress-wrap {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 5px 8px;
    margin-top: 2px;
}
.hw-progress-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    font-weight: 700;
    margin-bottom: 3px;
}
.hw-progress-title {
    color: #475569;
    display: flex;
    align-items: center;
    gap: 4px;
}
.hw-progress-stat {
    color: #002C54;
    font-weight: 800;
}
.hw-progress-track {
    height: 5px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}
.hw-progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width .3s ease;
}
.bg-success { background: #16a34a !important; }
.bg-primary { background: #0284c7 !important; }
.bg-warning { background: #f59e0b !important; }

/* Card Actions Strip - 29px/30px standardized */
.hw-card-actions {
    display: flex;
    gap: 4px;
    border-top: 1px solid #f1f5f9;
    padding-top: 6px;
    align-items: center;
}
.hw-act-btn {
    flex: 1;
    height: 29px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all .1s ease;
    white-space: nowrap;
    font-family: inherit;
    box-sizing: border-box;
}
.hw-act-btn:active {
    transform: scale(0.96);
}
.hw-act-btn.btn-view {
    background: #f8fafc;
    color: #002C54;
    border-color: #cbd5e1;
    flex: 1.1;
}
.hw-act-btn.btn-sub {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #047857;
    flex: 1.3;
}
.hw-act-btn.btn-remind {
    background: #fef3c7;
    border-color: #fde68a;
    color: #b45309;
    flex: 0.8;
}
.hw-act-btn.btn-edit {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0284c7;
    flex: 0.8;
}
.hw-act-btn.btn-del {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #dc2626;
    flex: 0.6;
    max-width: 32px;
}
.hw-act-btn.btn-del:active {
    background: #fee2e2;
}

/* 4. Compact Fixed Bottom Pagination Bar (Matching admissionView & expenseView) */
.mob-pagination-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.mob-pagination-info {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-pagination-info strong {
    color: #002C54;
    font-weight: 800;
}
.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-per-page-select {
    height: 28px;
    padding: 0 4px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    color: #1e293b;
    outline: none;
    cursor: pointer;
}
.mob-page-indicator {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
    padding: 0 4px;
    min-width: 28px;
    text-align: center;
}
.mob-page-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-page-btn:active {
    transform: scale(0.92);
}
.mob-page-btn.disabled {
    opacity: 0.35;
    pointer-events: none;
}

/* 5. Native Slide-Up Detail Bottom Sheet Modal */
.mob-detail-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2150;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-detail-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-detail-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2151;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-detail-sheet.show {
    bottom: 0;
}
.mob-detail-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-detail-title {
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
}
.mob-detail-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.mob-detail-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
}

/* 6. Native Filter Bottom Sheet Modal */
.mob-filter-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2150;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-filter-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-filter-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2151;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-filter-sheet.show {
    bottom: 0;
}
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    color: #475569;
    margin-bottom: 3px;
    letter-spacing: .02em;
}
.mob-form-input, .mob-form-select {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
    font-family: inherit;
}
.mob-form-input:focus, .mob-form-select:focus {
    border-color: #0284c7;
}

/* Empty State */
.hw-empty-box {
    text-align: center;
    padding: 38px 16px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
}
.hw-empty-box i {
    font-size: 28px;
    color: #cbd5e1;
    margin-bottom: 6px;
}
.hw-empty-box h4 {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 2px;
}
.hw-empty-box p {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
}
</style>
@endsection

@section('content')

{{-- 1. Signature Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-book text-primary"></i> Homework & Assignments
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Metrics Glance Grid --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-list text-info"></i> Total</span>
            <span class="mob-metric-val val-total">{{ $totalCount }}</span>
            <span class="mob-metric-sub">Assignments</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-check-circle text-success"></i> Active</span>
            <span class="mob-metric-val val-active">{{ $activeCount + $dueTodayCount }}</span>
            <span class="mob-metric-sub">Open for Work</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-clock-o text-danger"></i> Overdue</span>
            <span class="mob-metric-val val-overdue">{{ $overdueCount }}</span>
            <span class="mob-metric-sub">Past Deadline</span>
        </div>
    </div>

    {{-- Fast Action Bar --}}
    <div class="mob-actions-bar">
        @if($canCreate)
            <a href="{{ url('homework/add') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-plus-circle"></i> + Assign Homework
            </a>
        @else
            <a href="{{ url('homework/dashboard') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-th-large"></i> HW Dashboard
            </a>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnOpenFilterSheet">
            <i class="fa fa-filter"></i> Filter Drawer
        </button>
    </div>
</div>

{{-- Flash Feedback Alert --}}
@if(session('message'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:11px; border-radius:3px; font-weight:700;">
        <i class="fa fa-check mr-1"></i> {{ session('message') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:11px; border-radius:3px; font-weight:700;">
        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
    </div>
@endif

{{-- 2. Compact Search & Filter Toolbar --}}
<div class="mob-filter-toolbar">
    <div class="mob-search-row">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="hwSearchInput" class="mob-search-input" placeholder="Search by topic, subject, class, teacher..." value="{{ $search['title'] ?? '' }}" autocomplete="off">
            <i class="fa fa-times mob-search-clear" id="hwSearchClear"></i>
        </div>
    </div>
    <div class="mob-chips-scroll">
        <a href="{{ url('homework/index?layout=mobile') }}" class="mob-chip {{ empty($search['status']) ? 'active' : '' }}">
            <span>All</span>
            <span class="badge badge-light" style="font-size:8.5px;">{{ $totalCount }}</span>
        </a>
        <a href="{{ url('homework/index?layout=mobile&status=active') }}" class="mob-chip {{ ($search['status'] ?? '') === 'active' ? 'active' : '' }}">
            <i class="fa fa-circle text-success" style="font-size:7px;"></i> Active
        </a>
        <a href="{{ url('homework/index?layout=mobile&status=due_today') }}" class="mob-chip {{ ($search['status'] ?? '') === 'due_today' ? 'active' : '' }}">
            <i class="fa fa-circle text-warning" style="font-size:7px;"></i> Due Today
        </a>
        <a href="{{ url('homework/index?layout=mobile&status=overdue') }}" class="mob-chip {{ ($search['status'] ?? '') === 'overdue' ? 'active' : '' }}">
            <i class="fa fa-circle text-danger" style="font-size:7px;"></i> Overdue
        </a>
        <a href="{{ url('homework/index?layout=mobile&homework_type=DPP') }}" class="mob-chip {{ ($search['homework_type'] ?? '') === 'DPP' ? 'active' : '' }}">
            <i class="fa fa-tag text-primary" style="font-size:7px;"></i> DPP
        </a>
        <a href="{{ url('homework/index?layout=mobile&homework_type=Worksheet') }}" class="mob-chip {{ ($search['homework_type'] ?? '') === 'Worksheet' ? 'active' : '' }}">
            <i class="fa fa-file-text-o text-info" style="font-size:7px;"></i> Worksheet
        </a>
    </div>
</div>

{{-- 3. Native Homework Feed Cards --}}
<div class="hw-feed-list" id="hwFeedContainer">
    @forelse($data as $item)
        @php
            // Category parsing
            $type = 'Homework';
            $rawTitle = $item->title ?? '';
            $displayTitle = $rawTitle;
            if (preg_match('/^\[(.*?)\]\s*(.*)$/', $rawTitle, $matches)) {
                $type = trim($matches[1]);
                $displayTitle = trim($matches[2]);
            }

            // Category badge class
            $badgeClass = 'badge-type-general';
            $typeLower = strtolower($type);
            if (strpos($typeLower, 'dpp') !== false) {
                $badgeClass = 'badge-type-dpp';
            } elseif (strpos($typeLower, 'worksheet') !== false) {
                $badgeClass = 'badge-type-worksheet';
            } elseif (strpos($typeLower, 'pyq') !== false) {
                $badgeClass = 'badge-type-pyq';
            }

            // Assigned by
            $assignedByName = 'Admin';
            if (!empty($item->Teacher)) {
                $assignedByName = trim(($item->Teacher->first_name ?? '') . ' ' . ($item->Teacher->last_name ?? ''));
            } elseif (!empty($item->User)) {
                $assignedByName = trim(($item->User->first_name ?? '') . ' ' . ($item->User->last_name ?? ''));
            }

            // Due Status
            $dueDate = !empty($item->submission_date) ? date('Y-m-d', strtotime($item->submission_date)) : '';
            $isOverdue = (!empty($dueDate) && $dueDate < $today);
            $isDueToday = (!empty($dueDate) && $dueDate === $today);

            $borderClass = $isOverdue ? 'border-overdue' : ($isDueToday ? 'border-today' : 'border-active');
            $avatarClass = $isOverdue ? 'avatar-overdue' : ($isDueToday ? 'avatar-today' : 'avatar-active');

            // Permissions
            $itemTeacherId = $item->teacher_id ?? null;
            $canEdit = !$isStudent && (($itemTeacherId == $currentTeacherId) || ($currentRoleId == 1)) && ($permission->edit ?? true);
            $canDelete = !$isStudent && (($itemTeacherId == $currentTeacherId) || ($currentRoleId == 1)) && ($permission->delete ?? true);

            // Submissions Calculation
            $submittedCount = $item->submitted_students_count ?? ($item->upload_homework_count ?? 0);
            $totalClassStudents = $totalStudentsPerClass[$item->class_type_id] ?? 0;
            $pendingCount = max(0, $totalClassStudents - $submittedCount);
            $pct = ($totalClassStudents > 0) ? min(100, round(($submittedCount / $totalClassStudents) * 100)) : 0;
            $barColor = ($pct >= 75) ? 'bg-success' : (($pct >= 40) ? 'bg-primary' : 'bg-warning');

            // Encoded description for detail view
            $encodedDesc = base64_encode($item->description ?? '');
        @endphp

        <article class="hw-mob-card {{ $borderClass }}" id="hw-card-{{ $item->id }}" data-title="{{ strtolower($displayTitle) }}" data-subject="{{ strtolower($item->Subject->name ?? '') }}">
            {{-- Header --}}
            <div class="hw-card-header">
                <div class="hw-avatar-box {{ $avatarClass }}">
                    <i class="fa fa-book"></i>
                </div>
                <div class="hw-header-info">
                    <div class="hw-name-row">
                        <span class="hw-mob-title" title="{{ $displayTitle }}">{{ $displayTitle ?: 'Homework Assignment' }}</span>
                        @if($isOverdue)
                            <span class="hw-status-pill status-pill-overdue"><i class="fa fa-clock-o"></i> Overdue</span>
                        @elseif($isDueToday)
                            <span class="hw-status-pill status-pill-today"><i class="fa fa-exclamation-circle"></i> Today</span>
                        @else
                            <span class="hw-status-pill status-pill-active"><i class="fa fa-check-circle-o"></i> Active</span>
                        @endif
                    </div>
                    <div class="hw-pills-wrap">
                        <span class="hw-type-badge {{ $badgeClass }}">{{ $type }}</span>
                        <span class="badge badge-light" style="font-size:8.5px; font-weight:700; color:#002C54; border:1px solid #cbd5e1;">
                            {{ $item->ClassType->name ?? 'Class' }}@if(!empty($item->Section->name)) - {{ $item->Section->name }}@endif
                        </span>
                        @if(!empty($item->target_duration))
                            <span class="badge badge-light" style="font-size:8.5px; color:#475569;">
                                <i class="fa fa-clock-o"></i> {{ $item->target_duration }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Meta Body --}}
            <div class="hw-card-body">
                <div class="hw-meta-grid">
                    <div class="hw-meta-item">
                        <i class="fa fa-bookmark text-primary"></i>
                        <span>Subject: <strong>{{ $item->Subject->name ?? 'N/A' }}</strong></span>
                    </div>
                    <div class="hw-meta-item">
                        <i class="fa fa-user-circle-o text-info"></i>
                        <span>By: <strong>{{ $assignedByName }}</strong></span>
                    </div>
                    <div class="hw-meta-item">
                        <i class="fa fa-calendar-check-o text-secondary"></i>
                        <span>Issued: {{ !empty($item->homework_issue_date) ? date('d M Y', strtotime($item->homework_issue_date)) : '-' }}</span>
                    </div>
                    <div class="hw-meta-item">
                        <i class="fa fa-hourglass-end {{ $isOverdue ? 'text-danger' : 'text-success' }}"></i>
                        <span>Due: <strong class="{{ $isOverdue ? 'text-danger' : '' }}">{{ !empty($item->submission_date) ? date('d M Y', strtotime($item->submission_date)) : '-' }}</strong></span>
                    </div>
                </div>

                {{-- Submission Progress --}}
                <div class="hw-progress-wrap">
                    <div class="hw-progress-head">
                        <span class="hw-progress-title">
                            <i class="fa fa-users text-primary"></i> Submissions
                        </span>
                        <span class="hw-progress-stat">
                            {{ $submittedCount }}@if($totalClassStudents > 0)/{{ $totalClassStudents }}@endif
                            @if($totalClassStudents > 0) ({{ $pct }}%)@endif
                        </span>
                    </div>
                    @if($totalClassStudents > 0)
                        <div class="hw-progress-track">
                            <div class="hw-progress-fill {{ $barColor }}" style="width: {{ $pct }}%;"></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons Strip --}}
            <div class="hw-card-actions">
                {{-- View Details Sheet Button --}}
                <button type="button" 
                        class="hw-act-btn btn-view btnViewHwSheet" 
                        data-id="{{ $item->id }}" 
                        data-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}" 
                        data-raw-title="{{ htmlspecialchars($rawTitle, ENT_QUOTES) }}" 
                        data-type="{{ htmlspecialchars($type, ENT_QUOTES) }}" 
                        data-badge-class="{{ $badgeClass }}" 
                        data-class="{{ $item->ClassType->name ?? 'N/A' }}@if(!empty($item->Section->name)) - {{ $item->Section->name }}@endif" 
                        data-subject="{{ $item->Subject->name ?? 'N/A' }}" 
                        data-teacher="{{ htmlspecialchars($assignedByName, ENT_QUOTES) }}" 
                        data-issue-date="{{ !empty($item->homework_issue_date) ? date('d M Y', strtotime($item->homework_issue_date)) : '-' }}" 
                        data-due-date="{{ !empty($item->submission_date) ? date('d M Y', strtotime($item->submission_date)) : '-' }}" 
                        data-duration="{{ htmlspecialchars($item->target_duration ?? '', ENT_QUOTES) }}" 
                        data-marks="{{ $item->max_marks ?? '' }}" 
                        data-description="{{ $encodedDesc }}" 
                        data-file-url="{{ !empty($item->content_file) ? asset('schoolimage/homework/' . $item->content_file) : '' }}" 
                        data-file-name="{{ $item->content_file ?? '' }}" 
                        data-submissions-url="{{ url('homework/details/' . $item->id) }}" 
                        data-status="{{ $isOverdue ? 'Overdue' : ($isDueToday ? 'Due Today' : 'Active') }}" 
                        data-status-class="{{ $isOverdue ? 'status-pill-overdue' : ($isDueToday ? 'status-pill-today' : 'status-pill-active') }}">
                    <i class="fa fa-eye"></i> Details
                </button>

                {{-- Submissions Page Link --}}
                @if(!$isStudent)
                    <a href="{{ url('homework/details/' . $item->id) }}" class="hw-act-btn btn-sub" title="View Student Submissions">
                        <i class="fa fa-users"></i> Submissions
                    </a>
                @endif

                {{-- WhatsApp Remind Defaulters --}}
                @if(!$isStudent && $pendingCount > 0)
                    <button type="button" 
                            class="hw-act-btn btn-remind btnRemindDefaulters" 
                            data-id="{{ $item->id }}" 
                            data-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}" 
                            data-class="{{ $item->ClassType->name ?? 'N/A' }}" 
                            data-pending="{{ $pendingCount }}" 
                            title="Remind Defaulters">
                        <i class="fa fa-whatsapp"></i> Remind
                    </button>
                @endif

                {{-- Edit --}}
                @if($canEdit)
                    <a href="{{ url('homework/edit/' . $item->id) }}" class="hw-act-btn btn-edit" title="Edit Homework">
                        <i class="fa fa-edit"></i>
                    </a>
                @endif

                {{-- Delete --}}
                @if($canDelete)
                    <button type="button" 
                            class="hw-act-btn btn-del btnDeleteHw" 
                            data-id="{{ $item->id }}" 
                            data-title="{{ htmlspecialchars($displayTitle, ENT_QUOTES) }}" 
                            data-class-sub="{{ ($item->ClassType->name ?? 'N/A') . ' • ' . ($item->Subject->name ?? 'N/A') }}" 
                            title="Delete Homework">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
        </article>
    @empty
        <div class="hw-empty-box">
            <i class="fa fa-book"></i>
            <h4>No Homework Found</h4>
            <p>No assignments found matching your filter criteria.</p>
        </div>
    @endforelse
</div>

{{-- 4. Fixed Compact Bottom Pagination Bar (Standard Reference: admissionView & expenseView) --}}
@php
    $fromIndex = $totalCount > 0 ? ($startIndex + 1) : 0;
    $toIndex = min($startIndex + $perPage, $totalCount);
@endphp
<div class="mob-pagination-bar">
    <div class="mob-pagination-info">
        Showing <strong>{{ $fromIndex }}-{{ $toIndex }}</strong> of <strong>{{ $totalCount }}</strong>
    </div>
    <div class="mob-pagination-btns">
        <select id="mobPerPageSelect" class="mob-per-page-select">
            <option value="10" {{ (int)$perPage === 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ (int)$perPage === 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ (int)$perPage === 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ (int)$perPage === 100 ? 'selected' : '' }}>100</option>
        </select>
        <a href="{{ $data->previousPageUrl() ? url($data->previousPageUrl() . '&layout=mobile') : '#' }}" 
           class="mob-page-btn {{ $data->onFirstPage() ? 'disabled' : '' }}" 
           title="Previous Page">
            <i class="fa fa-chevron-left"></i>
        </a>
        <span class="mob-page-indicator">{{ $currentPage }} / {{ $lastPage }}</span>
        <a href="{{ $data->nextPageUrl() ? url($data->nextPageUrl() . '&layout=mobile') : '#' }}" 
           class="mob-page-btn {{ !$data->hasMorePages() ? 'disabled' : '' }}" 
           title="Next Page">
            <i class="fa fa-chevron-right"></i>
        </a>
    </div>
</div>

{{-- 5. Native Slide-Up Detail Bottom Sheet Modal --}}
<div class="mob-detail-sheet-backdrop" id="mobDetailSheetBackdrop"></div>
<div class="mob-detail-sheet" id="mobDetailSheet">
    <div class="mob-detail-header">
        <div class="mob-detail-title">
            <i class="fa fa-book text-primary"></i> <span id="detailSheetTitle">Assignment Details</span>
        </div>
        <button type="button" class="mob-detail-close" id="btnCloseDetailSheet">&times;</button>
    </div>
    <div class="mob-detail-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge" id="detailSheetCategoryBadge" style="font-size:10px; font-weight:800; padding:3px 8px; border-radius:3px;">Homework</span>
            <span class="hw-status-pill" id="detailSheetStatusPill">Active</span>
        </div>
        <h5 id="detailSheetHeading" style="font-size:13.5px; font-weight:800; color:#002C54; margin-bottom:8px; line-height:1.3;"></h5>

        {{-- Glance Grid --}}
        <div class="hw-meta-grid mb-2" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:4px; padding:8px;">
            <div class="hw-meta-item"><i class="fa fa-graduation-cap"></i> Class: <strong id="detailSheetClass">-</strong></div>
            <div class="hw-meta-item"><i class="fa fa-bookmark"></i> Subject: <strong id="detailSheetSubject">-</strong></div>
            <div class="hw-meta-item"><i class="fa fa-user-circle"></i> Assigned By: <strong id="detailSheetTeacher">-</strong></div>
            <div class="hw-meta-item"><i class="fa fa-clock-o"></i> Duration: <strong id="detailSheetDuration">-</strong></div>
            <div class="hw-meta-item"><i class="fa fa-calendar-check-o"></i> Issued: <span id="detailSheetIssued">-</span></div>
            <div class="hw-meta-item"><i class="fa fa-hourglass-end"></i> Due: <strong id="detailSheetDue" class="text-danger">-</strong></div>
        </div>

        {{-- Description Box --}}
        <div class="mb-2">
            <label style="font-size:10.5px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:3px; display:block;">Description / Instructions:</label>
            <div id="detailSheetDescription" style="font-size:11.5px; color:#1e293b; background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:8px 10px; line-height:1.45; white-space:pre-line; max-height:180px; overflow-y:auto;">
                No description provided.
            </div>
        </div>

        {{-- Attachment Download Chip --}}
        <div id="detailSheetAttachmentWrap" style="display:none; margin-bottom:12px;">
            <label style="font-size:10.5px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:3px; display:block;">Attachment File:</label>
            <a href="#" id="detailSheetAttachmentLink" target="_blank" download class="btn btn-sm btn-light w-100 text-left d-flex align-items-center justify-content-between" style="border:1px solid #cbd5e1; font-size:11px; font-weight:700; background:#f1f5f9;">
                <span><i class="fa fa-paperclip mr-1 text-primary"></i> <span id="detailSheetAttachmentName">Download File</span></span>
                <span class="badge badge-primary"><i class="fa fa-download"></i> Download</span>
            </a>
        </div>

        {{-- Action Buttons inside Detail Sheet --}}
        @if(!$isStudent)
            <div class="d-flex gap-2" style="gap:6px;">
                <a href="#" id="detailSheetSubmissionsBtn" class="btn btn-primary btn-sm flex-fill font-weight-bold" style="font-size:11px; height:32px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
                    <i class="fa fa-users"></i> View All Submissions
                </a>
            </div>
        @endif
    </div>
</div>

{{-- 6. Native Filter Bottom Sheet Modal --}}
<div class="mob-filter-sheet-backdrop" id="mobFilterSheetBackdrop"></div>
<div class="mob-filter-sheet" id="mobFilterSheet">
    <div class="mob-detail-header">
        <div class="mob-detail-title">
            <i class="fa fa-filter text-primary"></i> Filter Assignments
        </div>
        <button type="button" class="mob-detail-close" id="btnCloseFilterSheet">&times;</button>
    </div>
    <form action="{{ url('homework/index') }}" method="GET" id="mobFilterForm">
        <input type="hidden" name="layout" value="mobile">
        <div class="mob-detail-body">
            <div class="mob-form-group">
                <label class="mob-form-label">Search Keywords</label>
                <input type="text" name="title" class="mob-form-input" placeholder="Title, topic, description..." value="{{ $search['title'] ?? '' }}">
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Class</label>
                <select name="class_type_id" class="mob-form-select">
                    <option value="">All Classes</option>
                    @if(!empty($classType))
                        @foreach($classType as $type)
                            <option value="{{ $type->id }}" {{ (string)($search['class_type_id'] ?? '') === (string)$type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Subject</label>
                <select name="subject" class="mob-form-select">
                    <option value="">All Subjects</option>
                    @if(!empty($allSubjects))
                        @foreach($allSubjects as $sub)
                            <option value="{{ $sub->id }}" {{ (string)($search['subject'] ?? '') === (string)$sub->id ? 'selected' : '' }}>
                                {{ $sub->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Category / Type</label>
                <select name="homework_type" class="mob-form-select">
                    <option value="">All Categories</option>
                    <option value="Homework" {{ ($search['homework_type'] ?? '') === 'Homework' ? 'selected' : '' }}>Homework</option>
                    <option value="DPP" {{ ($search['homework_type'] ?? '') === 'DPP' ? 'selected' : '' }}>DPP (Daily Practice Paper)</option>
                    <option value="Worksheet" {{ ($search['homework_type'] ?? '') === 'Worksheet' ? 'selected' : '' }}>Worksheet</option>
                    <option value="PYQ" {{ ($search['homework_type'] ?? '') === 'PYQ' ? 'selected' : '' }}>PYQ</option>
                    <option value="Revision" {{ ($search['homework_type'] ?? '') === 'Revision' ? 'selected' : '' }}>Revision</option>
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Due Status</label>
                <select name="status" class="mob-form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ ($search['status'] ?? '') === 'active' ? 'selected' : '' }}>Active (Future Due Date)</option>
                    <option value="due_today" {{ ($search['status'] ?? '') === 'due_today' ? 'selected' : '' }}>Due Today</option>
                    <option value="overdue" {{ ($search['status'] ?? '') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="d-flex gap-2 mt-3" style="gap:6px;">
                <a href="{{ url('homework/index?layout=mobile') }}" class="btn btn-secondary btn-sm flex-fill font-weight-bold" style="height:34px; font-size:11.5px; border-radius:4px; display:inline-flex; align-items:center; justify-content:center;">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary btn-sm flex-fill font-weight-bold" style="height:34px; font-size:11.5px; border-radius:4px; background:#002C54; border-color:#002C54; display:inline-flex; align-items:center; justify-content:center;">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Hidden Form for Delete Execution --}}
<form action="{{ url('homework/delete') }}" method="POST" id="mobDeleteHwForm" style="display:none;">
    @csrf
    <input type="hidden" name="delete_id" id="mobDeleteHwId" value="">
</form>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}";

    // 1. Real-time client-side search filtering
    $('#hwSearchInput').on('input', function() {
        var q = ($(this).val() || '').toLowerCase().trim();
        if (q.length > 0) {
            $('#hwSearchClear').show();
            $('.hw-mob-card').each(function() {
                var title = $(this).data('title') || '';
                var subject = $(this).data('subject') || '';
                var cardText = $(this).text().toLowerCase();
                var match = (title.indexOf(q) !== -1) || (subject.indexOf(q) !== -1) || (cardText.indexOf(q) !== -1);
                $(this).toggle(match);
            });
        } else {
            $('#hwSearchClear').hide();
            $('.hw-mob-card').show();
        }
    });

    $('#hwSearchClear').on('click', function() {
        $('#hwSearchInput').val('').trigger('input');
    });

    // Enter press triggers server search for complete DB filtering
    $('#hwSearchInput').on('keypress', function(e) {
        if (e.which === 13) {
            var val = $(this).val();
            window.location.href = "{{ url('homework/index?layout=mobile') }}" + "&title=" + encodeURIComponent(val);
        }
    });

    // 2. Per-Page selector handler
    $('#mobPerPageSelect').on('change', function() {
        var perPage = $(this).val();
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('per_page', perPage);
        currentUrl.searchParams.set('page', 1);
        currentUrl.searchParams.set('layout', 'mobile');
        window.location.href = currentUrl.toString();
    });

    // 3. Detail Bottom Sheet Modal
    $('.btnViewHwSheet').on('click', function() {
        var $btn = $(this);
        var title = $btn.data('title');
        var type = $btn.data('type') || 'Homework';
        var badgeClass = $btn.data('badge-class') || 'badge-type-general';
        var className = $btn.data('class');
        var subject = $btn.data('subject');
        var teacher = $btn.data('teacher');
        var issueDate = $btn.data('issue-date');
        var dueDate = $btn.data('due-date');
        var duration = $btn.data('duration') || 'N/A';
        var fileUrl = $btn.data('file-url');
        var fileName = $btn.data('file-name');
        var subUrl = $btn.data('submissions-url');
        var status = $btn.data('status');
        var statusClass = $btn.data('status-class');

        // Decode base64 description
        var rawDesc = '';
        try {
            rawDesc = decodeURIComponent(escape(atob($btn.data('description') || '')));
        } catch(e) {
            rawDesc = atob($btn.data('description') || '');
        }

        $('#detailSheetHeading').text(title);
        $('#detailSheetCategoryBadge').text(type).attr('class', 'badge ' + badgeClass);
        $('#detailSheetStatusPill').text(status).attr('class', 'hw-status-pill ' + statusClass);
        $('#detailSheetClass').text(className);
        $('#detailSheetSubject').text(subject);
        $('#detailSheetTeacher').text(teacher);
        $('#detailSheetDuration').text(duration);
        $('#detailSheetIssued').text(issueDate);
        $('#detailSheetDue').text(dueDate);
        $('#detailSheetDescription').text(rawDesc ? rawDesc : 'No description provided.');

        if (fileUrl) {
            $('#detailSheetAttachmentWrap').show();
            $('#detailSheetAttachmentLink').attr('href', fileUrl);
            $('#detailSheetAttachmentName').text(fileName ? fileName : 'Attachment File');
        } else {
            $('#detailSheetAttachmentWrap').hide();
        }

        $('#detailSheetSubmissionsBtn').attr('href', subUrl);

        $('#mobDetailSheetBackdrop').addClass('show');
        $('#mobDetailSheet').addClass('show');
    });

    $('#btnCloseDetailSheet, #mobDetailSheetBackdrop').on('click', function() {
        $('#mobDetailSheetBackdrop').removeClass('show');
        $('#mobDetailSheet').removeClass('show');
    });

    // 4. Filter Bottom Sheet Drawer
    $('#btnOpenFilterSheet').on('click', function() {
        $('#mobFilterSheetBackdrop').addClass('show');
        $('#mobFilterSheet').addClass('show');
    });

    $('#btnCloseFilterSheet, #mobFilterSheetBackdrop').on('click', function() {
        $('#mobFilterSheetBackdrop').removeClass('show');
        $('#mobFilterSheet').removeClass('show');
    });

    // 5. Native WhatsApp Remind Defaulters (Uses Global showMobileConfirm)
    $('.btnRemindDefaulters').on('click', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var title = $btn.data('title');
        var className = $btn.data('class');
        var pending = $btn.data('pending');

        showMobileConfirm(
            'WhatsApp Reminder',
            'Queue WhatsApp reminder messages for <b>' + pending + '</b> pending students of <b>' + className + '</b> for assignment "' + title + '"?',
            function() {
                var origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    url: "{{ url('homework/remind-defaulters') }}",
                    type: "POST",
                    data: {
                        _token: csrfToken,
                        homework_id: id
                    },
                    dataType: "json",
                    success: function(res) {
                        $btn.prop('disabled', false).html(origHtml);
                        if (res.status === 'success' || res.status === true) {
                            alert(res.message || 'WhatsApp reminders queued successfully.');
                        } else {
                            alert(res.message || 'Failed to send reminders.');
                        }
                    },
                    error: function() {
                        $btn.prop('disabled', false).html(origHtml);
                        alert('Server error while sending reminders.');
                    }
                });
            },
            '<i class="fa fa-whatsapp"></i> Send Reminders',
            false,
            'fa-whatsapp text-success'
        );
    });

    // 6. Native Delete Confirmation (Uses Global showMobileConfirm)
    $('.btnDeleteHw').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var classSub = $(this).data('class-sub');

        showMobileConfirm(
            'Delete Assignment',
            'Are you sure you want to permanently delete <b>"' + title + '"</b> (' + classSub + ')? This will delete all student submissions and cannot be undone.',
            function() {
                $('#mobDeleteHwId').val(id);
                $('#mobDeleteHwForm').submit();
            },
            '<i class="fa fa-trash"></i> Yes, Delete',
            true,
            'fa-trash text-danger'
        );
    });
});
</script>
@endsection

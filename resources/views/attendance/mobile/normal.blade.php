@php
    $attendanceBaseUrl = url('attendance/mark');
    $studentsTabUrl = $attendanceBaseUrl . '?tab=students&date=' . urlencode($selectedDate) . (request('class_type_id') ? '&class_type_id=' . urlencode(request('class_type_id')) : '');
    $staffTabUrl = $attendanceBaseUrl . '?tab=staff&date=' . urlencode($selectedDate) . (request('role_id') ? '&role_id=' . urlencode(request('role_id')) : '');
    $studentsSaveUrl = $attendanceBaseUrl . '?tab=students&date=' . urlencode($selectedDate) . '&class_type_id=' . urlencode((string) request('class_type_id', ''));
    $staffSaveUrl = $attendanceBaseUrl . '?tab=staff&date=' . urlencode($selectedDate) . '&role_id=' . urlencode((string) request('role_id', ''));
    $holidayLock = !empty($isHolidayDate);
    $isStaffTab = ($activeTab ?? 'students') === 'staff';

    // Calculate live summary stats for initial render
    $activeItems = $isStaffTab ? $staff : $students;
    $totalCount = $activeItems->count();
    $markedInCount = 0;
    $markedAbsentCount = 0;
    $markedHalfdayCount = 0;
    $markedHolidayCount = 0;
    $unmarkedCount = 0;

    foreach ($activeItems as $item) {
        $uid = $item->attendance_unique_id;
        $m = $attendanceMarks[$uid] ?? null;
        $st = $m->status ?? '';
        if ($st === 'in' || $st === 'present') {
            $markedInCount++;
        } elseif ($st === 'absent') {
            $markedAbsentCount++;
        } elseif ($st === 'halfday') {
            $markedHalfdayCount++;
        } elseif ($st === 'holiday') {
            $markedHolidayCount++;
        } else {
            $unmarkedCount++;
        }
    }

    $currentSessionName = Session::get('session_name') ?? date('Y');
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE ATTENDANCE MARKING INTERFACE
   ========================================================================== */

/* 1. Dark Navy Hero Banner */
.att-mob-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.att-mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.att-mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.att-mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* Date Bar in Hero */
.att-mob-date-wrap {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 4px;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}
.att-mob-date-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 5px;
}
.att-mob-date-input {
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 700;
    padding: 3px 6px;
    border-radius: 3px;
    outline: none;
    color-scheme: dark;
}

/* Live KPI Metric Boxes */
.att-mob-metrics {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 4px;
}
.att-mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 3px;
    padding: 5px 2px;
    text-align: center;
}
.att-mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 1px;
}
.att-mob-metric-val {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.att-mob-metric-val.val-total { color: #38bdf8; }
.att-mob-metric-val.val-present { color: #4ade80; }
.att-mob-metric-val.val-absent { color: #f87171; }
.att-mob-metric-val.val-halfday { color: #c084fc; }
.att-mob-metric-val.val-unmarked { color: #cbd5e1; }

/* 2. Navigation Tabs (Students vs Staff) */
.att-mob-tabs {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
}
.att-mob-tab-btn {
    flex: 1;
    height: 30px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: transparent;
    transition: all .12s ease;
}
.att-mob-tab-btn.active {
    background: #ffffff;
    color: #002C54;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.att-mob-tab-badge {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 10px;
    background: #e0f2fe;
    color: #0284c7;
    font-weight: 800;
}

/* 3. Class / Role Selector Toolbar */
.att-mob-filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.att-mob-select-row {
    display: flex;
    gap: 6px;
    margin-bottom: 6px;
}
.att-mob-select {
    flex: 1;
    height: 32px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 8px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.att-mob-select:focus {
    border-color: #0284c7;
    background: #ffffff;
}

/* Instant Search Input */
.att-mob-search-wrap {
    position: relative;
    width: 100%;
}
.att-mob-search-wrap i {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}
.att-mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 28px 0 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.att-mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.att-mob-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
    cursor: pointer;
    display: none;
}

/* 4. Quick 1-Tap Bulk Action Chips */
.att-mob-bulk-bar {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow-x: auto;
    padding-bottom: 2px;
    margin-bottom: 8px;
    scrollbar-width: none;
}
.att-mob-bulk-bar::-webkit-scrollbar {
    display: none;
}
.btn-mob-quick {
    flex: 1;
    height: 28px;
    padding: 0 6px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    white-space: nowrap;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .12s ease;
    flex-shrink: 0;
}
.btn-mob-quick:active {
    transform: scale(0.95);
}
.btn-mob-quick-in {
    background: #dcfce7;
    color: #166534;
    border-color: #86efac;
}
.btn-mob-quick-absent {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fca5a5;
}
.btn-mob-quick-halfday {
    background: #ede9fe;
    color: #6b21a8;
    border-color: #d8b4fe;
}
.btn-mob-quick-reset {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}

/* 5. Attendance Card Feed List */
.att-mob-cards-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 56px; /* Clearance for fixed bottom bar */
}
.att-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: all .12s ease;
}
.att-mob-card.card-status-in {
    border-left: 3.5px solid #16a34a;
    background: #fbfffc;
}
.att-mob-card.card-status-absent {
    border-left: 3.5px solid #dc2626;
    background: #fffbfa;
}
.att-mob-card.card-status-halfday {
    border-left: 3.5px solid #9333ea;
    background: #fdfbff;
}
.att-mob-card.card-status-holiday {
    border-left: 3.5px solid #64748b;
    background: #f8fafc;
}

/* Card Header */
.att-mob-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.att-mob-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.att-mob-avatar.avatar-staff {
    background: #f1f5f9;
    color: #334155;
}
.att-mob-card-info {
    flex: 1;
    overflow: hidden;
}
.att-mob-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.att-mob-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.att-mob-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    white-space: nowrap;
}
.badge-in, .badge-present { background: #dcfce7; color: #166534; }
.badge-absent { background: #fee2e2; color: #dc2626; }
.badge-halfday { background: #ede9fe; color: #7e22ce; }
.badge-holiday { background: #e2e8f0; color: #334155; }
.badge-none { background: #f1f5f9; color: #64748b; }

.att-mob-pills-row {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 1px;
}
.att-mob-adm-badge {
    font-size: 8.5px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    font-family: monospace;
}
.att-mob-sno {
    font-size: 8.5px;
    color: #94a3b8;
    font-weight: 700;
    margin-left: auto;
}

/* 1-Touch Status Buttons Row on Each Card */
.att-mob-status-actions {
    display: flex;
    gap: 4px;
    align-items: center;
}
.btn-status-pill {
    flex: 1;
    height: 27px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    cursor: pointer;
    transition: all .1s ease;
    user-select: none;
}
.btn-status-pill:active {
    transform: scale(0.95);
}
.btn-status-pill.active-in {
    background: #16a34a;
    color: #ffffff;
    border-color: #15803d;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(22, 163, 74, 0.3);
}
.btn-status-pill.active-absent {
    background: #dc2626;
    color: #ffffff;
    border-color: #b91c1c;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.3);
}
.btn-status-pill.active-halfday {
    background: #9333ea;
    color: #ffffff;
    border-color: #7e22ce;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(147, 51, 234, 0.3);
}
.btn-status-pill-reset {
    flex: 0 0 27px;
    width: 27px;
    background: #ffffff;
    color: #94a3b8;
}
.btn-status-pill-reset:hover {
    color: #dc2626;
    background: #fee2e2;
    border-color: #fca5a5;
}

/* 6. Fixed Bottom Summary & Save Bar */
.att-mob-bottom-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(0, 44, 84, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 5px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 16px rgba(0, 20, 40, 0.35);
    color: #ffffff;
}
.att-mob-bottom-info {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}
.att-mob-bottom-stats {
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
}
.att-mob-bottom-modified {
    font-size: 9.5px;
    font-weight: 600;
    color: #38bdf8;
}
.btn-att-save-mob {
    height: 32px;
    padding: 0 14px;
    background: #0284c7;
    border: 1px solid #38bdf8;
    color: #ffffff;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all .12s ease;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.4);
}
.btn-att-save-mob:active {
    transform: scale(0.96);
}
.btn-att-save-mob:disabled {
    opacity: 0.5;
    pointer-events: none;
}

/* 7. Native Mobile Confirmation Dialog Modal */
.mob-confirm-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2100;
    opacity: 0;
    visibility: hidden;
    transition: all .2s ease;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-confirm-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-confirm-sheet {
    position: fixed;
    bottom: -320px;
    left: 8px;
    right: 8px;
    background: #ffffff;
    border-radius: 8px;
    padding: 14px 14px 16px;
    z-index: 2101;
    transition: bottom .24s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -4px 25px rgba(0, 20, 40, 0.35);
    border: 1px solid #cbd5e1;
}
.mob-confirm-sheet.show {
    bottom: calc(14px + env(safe-area-inset-bottom, 0px));
}
.mob-confirm-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-confirm-desc {
    font-size: 11.5px;
    color: #64748b;
    margin-bottom: 14px;
    line-height: 1.4;
}
.mob-confirm-buttons {
    display: flex;
    gap: 8px;
}
.mob-btn-cancel-dialog {
    flex: 1;
    height: 34px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
}
.mob-btn-execute-dialog {
    flex: 1.5;
    height: 34px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 800;
    cursor: pointer;
}
.mob-btn-execute-dialog.btn-danger-confirm {
    background: #dc2626;
}

/* 8. Native Mobile Floating Toast */
.mob-toast {
    position: fixed;
    top: calc(var(--header-height, 48px) + 10px);
    left: 12px;
    right: 12px;
    background: rgba(0, 20, 40, 0.94);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 4px;
    padding: 9px 12px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 7px;
    z-index: 2200;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all .2s ease;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.mob-toast.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}
.mob-toast.toast-danger {
    background: #dc2626;
    border-color: #ef4444;
}
.mob-toast.toast-success {
    background: #16a34a;
    border-color: #22c55e;
}
.mob-toast.toast-warning {
    background: #d97706;
    border-color: #f59e0b;
}

/* 9. Empty States & Alerts */
.att-mob-empty {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    color: #64748b;
    margin: 8px 0;
}
.att-mob-empty-icon {
    font-size: 32px;
    color: #cbd5e1;
    margin-bottom: 6px;
}
.att-mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 3px;
}
.att-mob-empty-desc {
    font-size: 10.5px;
    color: #64748b;
}

.mob-alert {
    padding: 7px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.mob-alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.mob-alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
</style>
@endsection

@section('content')

{{-- Native Mobile Toast --}}
<div class="mob-toast" id="mobToast">
    <i class="fa fa-info-circle"></i> <span id="mobToastMsg"></span>
</div>

{{-- Alerts --}}
@if(session('message'))
    <div class="mob-alert mob-alert-success">
        <i class="fa fa-check-circle"></i> {{ session('message') }}
    </div>
@endif
@if(session('error'))
    <div class="mob-alert mob-alert-danger">
        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif
@if($holidayLock)
    <div class="mob-alert mob-alert-warning">
        <i class="fa fa-lock"></i> Holiday Locked: {{ date('d/m/Y', strtotime($selectedDate)) }} is marked as a Holiday. Attendance marking is disabled.
    </div>
@endif

{{-- 1. Dark Navy Hero Banner --}}
<div class="att-mob-hero">
    <div class="att-mob-hero-top">
        <div class="att-mob-hero-title">
            <i class="fa fa-calendar-check-o text-primary"></i> Mark Attendance
        </div>
        <div class="att-mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Date Switcher in Hero --}}
    <form method="get" action="{{ $attendanceBaseUrl }}" id="mobDateForm" class="m-0">
        <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">
        @if(!$isStaffTab && request('class_type_id'))
            <input type="hidden" name="class_type_id" value="{{ request('class_type_id') }}">
        @endif
        @if($isStaffTab && request('role_id'))
            <input type="hidden" name="role_id" value="{{ request('role_id') }}">
        @endif
        <div class="att-mob-date-wrap">
            <span class="att-mob-date-label">
                <i class="fa fa-calendar text-primary"></i> Attendance Date:
            </span>
            <input type="date" name="date" class="att-mob-date-input" value="{{ $selectedDate }}"
                @if(!($allowBackDateForUser ?? false)) min="{{ date('Y-m-d') }}" @endif
                max="{{ date('Y-m-d') }}"
                onchange="this.form.submit()">
        </div>
    </form>

    {{-- Live KPI Counters --}}
    <div class="att-mob-metrics">
        <div class="att-mob-metric-box">
            <span class="att-mob-metric-tag">Total</span>
            <div class="att-mob-metric-val val-total" id="mobStatTotal">{{ $totalCount }}</div>
        </div>
        <div class="att-mob-metric-box">
            <span class="att-mob-metric-tag">Present</span>
            <div class="att-mob-metric-val val-present" id="mobStatPresent">{{ $markedInCount }}</div>
        </div>
        <div class="att-mob-metric-box">
            <span class="att-mob-metric-tag">Absent</span>
            <div class="att-mob-metric-val val-absent" id="mobStatAbsent">{{ $markedAbsentCount }}</div>
        </div>
        <div class="att-mob-metric-box">
            <span class="att-mob-metric-tag">Half Day</span>
            <div class="att-mob-metric-val val-halfday" id="mobStatHalfday">{{ $markedHalfdayCount }}</div>
        </div>
        <div class="att-mob-metric-box">
            <span class="att-mob-metric-tag">Unmarked</span>
            <div class="att-mob-metric-val val-unmarked" id="mobStatUnmarked">{{ $unmarkedCount }}</div>
        </div>
    </div>
</div>

{{-- 2. Navigation Tabs (Students vs Staff) --}}
<div class="att-mob-tabs">
    <a href="{{ $studentsTabUrl }}" class="att-mob-tab-btn {{ !$isStaffTab ? 'active' : '' }}">
        <i class="fa fa-graduation-cap"></i> Students
        @if(!$isStaffTab && $totalCount > 0)
            <span class="att-mob-tab-badge">{{ $totalCount }}</span>
        @endif
    </a>
    @if($canAccessStaffAttendance ?? false)
        <a href="{{ $staffTabUrl }}" class="att-mob-tab-btn {{ $isStaffTab ? 'active' : '' }}">
            <i class="fa fa-users"></i> Staff
            @if($isStaffTab && $totalCount > 0)
                <span class="att-mob-tab-badge">{{ $totalCount }}</span>
            @endif
        </a>
    @endif
</div>

{{-- 3. Filter & Quick Toolbar Card --}}
<div class="att-mob-filter-card">
    <div class="att-mob-select-row">
        @if(!$isStaffTab)
            <select id="mobClassSelect" class="att-mob-select">
                <option value="">-- Select Class --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ (string)request('class_type_id') === (string)$class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        @else
            <select id="mobRoleSelect" class="att-mob-select">
                <option value="">-- All Staff Roles --</option>
                @foreach($staffRoles as $role)
                    <option value="{{ $role->id }}" {{ (string)request('role_id') === (string)$role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    {{-- Instant Real-Time Search --}}
    <div class="att-mob-search-wrap">
        <i class="fa fa-search"></i>
        <input type="text" id="mobInstantSearch" class="att-mob-search-input" placeholder="Search by name, ID or roll no...">
        <span class="att-mob-search-clear" id="mobSearchClearBtn">&times;</span>
    </div>
</div>

{{-- 4. Fast 1-Tap Bulk Marking Bar --}}
@if($totalCount > 0)
    <div class="att-mob-bulk-bar">
        <button type="button" class="btn-mob-quick btn-mob-quick-in" id="btnMobAllPresent" @if($holidayLock) disabled @endif>
            <i class="fa fa-check-circle"></i> All Present
        </button>
        <button type="button" class="btn-mob-quick btn-mob-quick-absent" id="btnMobAllAbsent" @if($holidayLock) disabled @endif>
            <i class="fa fa-times-circle"></i> All Absent
        </button>
        <button type="button" class="btn-mob-quick btn-mob-quick-halfday" id="btnMobAllHalfday" @if($holidayLock) disabled @endif>
            <i class="fa fa-adjust"></i> All Half Day
        </button>
        <button type="button" class="btn-mob-quick btn-mob-quick-reset" id="btnMobAllReset" @if($holidayLock) disabled @endif>
            <i class="fa fa-undo"></i> Reset All
        </button>
    </div>
@endif

{{-- 5. Main Attendance Submission Form & Cards Feed --}}
<form method="post" action="{{ $isStaffTab ? $staffSaveUrl : $studentsSaveUrl }}" id="mobAttendanceForm" class="m-0">
    @csrf
    <input type="hidden" name="date" value="{{ $selectedDate }}">

    <div class="att-mob-cards-feed" id="attendanceCardsFeed">
        @forelse($activeItems as $i => $rowItem)
            @php
                $uniqueId = $rowItem->attendance_unique_id;
                $mark = $attendanceMarks[$uniqueId] ?? null;
                $statusVal = $mark->status ?? '';
                if ($statusVal === 'present') $statusVal = 'in';
                $fullName = trim(($rowItem->first_name ?? '') . ' ' . ($rowItem->last_name ?? ''));
                $entityType = $isStaffTab ? 'staff' : 'student';

                $initials = '';
                $words = preg_split('/\s+/', $fullName);
                foreach ($words as $w) {
                    if (!empty($w)) {
                        $initials .= strtoupper(mb_substr($w, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                }
                if (empty($initials)) $initials = $isStaffTab ? 'SF' : 'ST';

                $cardClass = match($statusVal) {
                    'in' => 'card-status-in',
                    'absent' => 'card-status-absent',
                    'halfday' => 'card-status-halfday',
                    'holiday' => 'card-status-holiday',
                    default => ''
                };

                $badgeClass = match($statusVal) {
                    'in' => 'badge-in',
                    'absent' => 'badge-absent',
                    'halfday' => 'badge-halfday',
                    'holiday' => 'badge-holiday',
                    default => 'badge-none'
                };

                $badgeLabel = match($statusVal) {
                    'in' => 'Present',
                    'absent' => 'Absent',
                    'halfday' => 'Half Day',
                    'holiday' => 'Holiday',
                    default => 'Not Marked'
                };
            @endphp

            <div class="att-mob-card {{ $cardClass }}"
                data-index="{{ $i }}"
                data-unique-id="{{ $uniqueId }}"
                data-name="{{ strtolower($fullName) }}"
                data-status="{{ $statusVal }}">
                
                {{-- Hidden row fields for fallback form submission --}}
                <input type="hidden" name="rows[{{ $i }}][unique_id]" value="{{ $uniqueId }}">
                <input type="hidden" name="rows[{{ $i }}][entity_type]" value="{{ $entityType }}">
                <input type="hidden" class="hidden-status-input" name="rows[{{ $i }}][status]" value="{{ $statusVal }}">

                {{-- Header Row: Avatar, Name & Status Badge --}}
                <div class="att-mob-card-header">
                    <div class="att-mob-avatar {{ $isStaffTab ? 'avatar-staff' : '' }}">
                        {{ $initials }}
                    </div>

                    <div class="att-mob-card-info">
                        <div class="att-mob-name-row">
                            <span class="att-mob-name">{{ $fullName ?: 'Unnamed' }}</span>
                            <span class="att-mob-status-badge {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </div>

                        <div class="att-mob-pills-row">
                            <span class="att-mob-adm-badge">
                                <i class="fa fa-id-badge text-muted mr-1"></i> {{ $uniqueId }}
                            </span>
                            <span class="att-mob-sno">#{{ $loop->iteration }}</span>
                        </div>
                    </div>
                </div>

                {{-- 1-Touch Status Action Buttons Row --}}
                <div class="att-mob-status-actions">
                    <button type="button" class="btn-status-pill btn-pill-in {{ $statusVal === 'in' ? 'active-in' : '' }}" data-val="in" @if($holidayLock) disabled @endif>
                        <i class="fa fa-check"></i> Present
                    </button>
                    <button type="button" class="btn-status-pill btn-pill-absent {{ $statusVal === 'absent' ? 'active-absent' : '' }}" data-val="absent" @if($holidayLock) disabled @endif>
                        <i class="fa fa-times"></i> Absent
                    </button>
                    <button type="button" class="btn-status-pill btn-pill-halfday {{ $statusVal === 'halfday' ? 'active-halfday' : '' }}" data-val="halfday" @if($holidayLock) disabled @endif>
                        <i class="fa fa-adjust"></i> Half Day
                    </button>
                    <button type="button" class="btn-status-pill btn-status-pill-reset" data-val="" title="Clear Status" @if($holidayLock) disabled @endif>
                        <i class="fa fa-undo"></i>
                    </button>
                </div>

            </div>
        @empty
            <div class="att-mob-empty">
                <div class="att-mob-empty-icon">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <div class="att-mob-empty-title">
                    @if(!$isStaffTab)
                        {{ request('class_type_id') ? 'No students found in this class.' : 'Select a class to mark attendance.' }}
                    @else
                        {{ request('role_id') ? 'No staff found for this role.' : 'No active staff records available.' }}
                    @endif
                </div>
                <div class="att-mob-empty-desc">
                    Choose a class or role above to mark and review attendance.
                </div>
            </div>
        @endforelse
    </div>

    {{-- 6. Fixed Floating Bottom Save Bar --}}
    @if($totalCount > 0)
        <div class="att-mob-bottom-bar">
            <div class="att-mob-bottom-info">
                <div class="att-mob-bottom-stats">
                    Total: <b id="mobBottomTotal">{{ $totalCount }}</b>
                </div>
                <div class="att-mob-bottom-modified">
                    <span id="mobBottomModified">0</span> changes to save
                </div>
            </div>

            <button type="submit" class="btn-att-save-mob" id="btnMobSubmitAttendance" @if($holidayLock) disabled @endif>
                <i class="fa fa-save"></i> Save Attendance
            </button>
        </div>
    @endif
</form>

{{-- 7. Native Mobile Confirmation Dialog Modal --}}
<div class="mob-confirm-backdrop" id="mobConfirmBackdrop"></div>
<div class="mob-confirm-sheet" id="mobConfirmSheet">
    <div class="mob-confirm-title" id="mobConfirmTitle">
        <i class="fa fa-question-circle text-primary"></i> <span>Confirm Action</span>
    </div>
    <div class="mob-confirm-desc" id="mobConfirmDesc">
        Are you sure you want to proceed?
    </div>
    <div class="mob-confirm-buttons">
        <button type="button" class="mob-btn-cancel-dialog" id="mobBtnCancelConfirm">Cancel</button>
        <button type="button" class="mob-btn-execute-dialog" id="mobBtnExecuteConfirm">Confirm</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const isHolidayLocked = @json($holidayLock);
    let onConfirmCallback = null;

    // Native Confirmation Modal Handler
    function showMobileConfirm(title, message, callback, okText = 'Confirm', isDanger = false) {
        onConfirmCallback = callback;
        $('#mobConfirmTitle span').text(title);
        $('#mobConfirmDesc').text(message);
        $('#mobBtnExecuteConfirm').text(okText);
        if (isDanger) {
            $('#mobBtnExecuteConfirm').addClass('btn-danger-confirm');
        } else {
            $('#mobBtnExecuteConfirm').removeClass('btn-danger-confirm');
        }
        $('#mobConfirmBackdrop').addClass('show');
        $('#mobConfirmSheet').addClass('show');
    }

    function closeMobileConfirm() {
        $('#mobConfirmBackdrop').removeClass('show');
        $('#mobConfirmSheet').removeClass('show');
        onConfirmCallback = null;
    }

    $('#mobBtnCancelConfirm, #mobConfirmBackdrop').on('click', closeMobileConfirm);

    $('#mobBtnExecuteConfirm').on('click', function() {
        const cb = onConfirmCallback;
        closeMobileConfirm();
        if (typeof cb === 'function') {
            cb();
        }
    });

    // Native Floating Toast Helper
    let toastTimer = null;
    function showMobileToast(msg, type = 'info') {
        clearTimeout(toastTimer);
        $('#mobToastMsg').text(msg);
        $('#mobToast').removeClass('toast-danger toast-success toast-warning').addClass('toast-' + type).addClass('show');
        toastTimer = setTimeout(function() {
            $('#mobToast').removeClass('show');
        }, 3200);
    }

    // 1. Snapshot original statuses for modification tracking
    $('.att-mob-card').each(function() {
        const $card = $(this);
        const origSt = $card.find('.hidden-status-input').val() || '';
        $card.data('orig-status', origSt);
    });

    // 2. Format Status Label & Badge Classes
    function formatStatusInfo(st) {
        switch(st) {
            case 'in':
            case 'present':
                return { label: 'Present', badgeClass: 'badge-in', cardClass: 'card-status-in' };
            case 'absent':
                return { label: 'Absent', badgeClass: 'badge-absent', cardClass: 'card-status-absent' };
            case 'halfday':
                return { label: 'Half Day', badgeClass: 'badge-halfday', cardClass: 'card-status-halfday' };
            case 'holiday':
                return { label: 'Holiday', badgeClass: 'badge-holiday', cardClass: 'card-status-holiday' };
            default:
                return { label: 'Not Marked', badgeClass: 'badge-none', cardClass: '' };
        }
    }

    // 3. Update Individual Card Appearance
    function setCardStatus($card, newStatus) {
        $card.find('.hidden-status-input').val(newStatus);
        $card.attr('data-status', newStatus);

        const info = formatStatusInfo(newStatus);
        const $badge = $card.find('.att-mob-status-badge');

        $badge.removeClass('badge-in badge-present badge-absent badge-halfday badge-holiday badge-none')
              .addClass(info.badgeClass)
              .text(info.label);

        $card.removeClass('card-status-in card-status-absent card-status-halfday card-status-holiday')
             .addClass(info.cardClass);

        // Update active button state
        $card.find('.btn-status-pill').removeClass('active-in active-absent active-halfday');
        if (newStatus === 'in' || newStatus === 'present') {
            $card.find('.btn-pill-in').addClass('active-in');
        } else if (newStatus === 'absent') {
            $card.find('.btn-pill-absent').addClass('active-absent');
        } else if (newStatus === 'halfday') {
            $card.find('.btn-pill-halfday').addClass('active-halfday');
        }

        recalcCounters();
    }

    // 4. Recalculate Live KPI Metrics
    function recalcCounters() {
        let total = 0;
        let present = 0;
        let absent = 0;
        let halfday = 0;
        let unmarked = 0;
        let modified = 0;

        $('.att-mob-card:visible').each(function() {
            total++;
            const $c = $(this);
            const st = $c.find('.hidden-status-input').val() || '';
            if (st === 'in' || st === 'present') {
                present++;
            } else if (st === 'absent') {
                absent++;
            } else if (st === 'halfday') {
                halfday++;
            } else {
                unmarked++;
            }

            const origSt = $c.data('orig-status') || '';
            if (st !== origSt) {
                modified++;
            }
        });

        $('#mobStatTotal, #mobBottomTotal').text(total);
        $('#mobStatPresent').text(present);
        $('#mobStatAbsent').text(absent);
        $('#mobStatHalfday').text(halfday);
        $('#mobStatUnmarked').text(unmarked);
        $('#mobBottomModified').text(modified);
    }

    // 5. Card 1-Touch Status Buttons
    $(document).on('click', '.btn-status-pill', function() {
        if (isHolidayLocked) return;
        const $card = $(this).closest('.att-mob-card');
        const val = $(this).attr('data-val') || '';
        setCardStatus($card, val);
    });

    // 6. Bulk Action Buttons
    function applyBulkStatus(val) {
        if (isHolidayLocked) return;
        $('.att-mob-card:visible').each(function() {
            setCardStatus($(this), val);
        });
    }

    $('#btnMobAllPresent').on('click', function() { applyBulkStatus('in'); });
    $('#btnMobAllAbsent').on('click', function() { applyBulkStatus('absent'); });
    $('#btnMobAllHalfday').on('click', function() { applyBulkStatus('halfday'); });
    $('#btnMobAllReset').on('click', function() {
        showMobileConfirm(
            'Reset Attendance',
            'Are you sure you want to reset attendance for all visible records?',
            function() {
                applyBulkStatus('');
                showMobileToast('All visible records have been reset.', 'info');
            },
            'Reset All',
            true
        );
    });

    // 7. Instant Search Filter
    $('#mobInstantSearch').on('input keyup', function() {
        const q = $(this).val().toLowerCase().trim();
        $('#mobSearchClearBtn').toggle(q.length > 0);

        $('.att-mob-card').each(function() {
            const $c = $(this);
            const name = $c.data('name') || '';
            const uid = ($c.data('unique-id') || '').toString().toLowerCase();
            const matches = !q || name.indexOf(q) > -1 || uid.indexOf(q) > -1;
            $c.toggle(matches);
        });

        recalcCounters();
    });

    $('#mobSearchClearBtn').on('click', function() {
        $('#mobInstantSearch').val('').trigger('keyup');
    });

    // 8. Class & Role Dropdown Changes
    function updateUrlParam(key, val) {
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set(key, val);
        } else {
            url.searchParams.delete(key);
        }
        window.location.href = url.toString();
    }

    $('#mobClassSelect').on('change', function() {
        updateUrlParam('class_type_id', $(this).val());
    });

    $('#mobRoleSelect').on('change', function() {
        updateUrlParam('role_id', $(this).val());
    });

    // 9. High-Performance Form Submit Engine via JSON
    $('#mobAttendanceForm').on('submit', function(e) {
        if (isHolidayLocked) {
            e.preventDefault();
            showMobileToast('Attendance marking is locked on holidays.', 'danger');
            return false;
        }

        const changedRows = [];
        $('.att-mob-card').each(function() {
            const $c = $(this);
            const origSt = $c.data('orig-status') || '';
            const currentStatus = $c.find('.hidden-status-input').val() || '';

            if (currentStatus !== origSt) {
                changedRows.push({
                    selected: '1',
                    unique_id: $c.find('input[name$="[unique_id]"]').val() || '',
                    entity_type: $c.find('input[name$="[entity_type]"]').val() || '',
                    in_time: null,
                    out_time: null,
                    status: currentStatus
                });
            }
        });

        const $form = $(this);
        $form.find(':input[name^="rows["]').prop('disabled', true);
        $('<input>', {
            type: 'hidden',
            name: 'attendance_rows_json',
            value: JSON.stringify(changedRows)
        }).appendTo($form);

        $('#btnMobSubmitAttendance')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    });

    // Initial counter calc
    recalcCounters();
});
</script>
@endsection
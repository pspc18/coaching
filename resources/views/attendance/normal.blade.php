@extends('layout.app')

@php
    $attendanceBaseUrl = request()->url();
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
@endphp

@section('styles')
<style>
/* 1. Page Shell & Viewport Constraints */
.att-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.att-page-wrapper * {
    box-sizing: border-box;
}
.att-layout {
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height, 56px) - 16px);
    overflow: hidden;
    gap: 6px;
}

/* 2. Top Hero Header (Arise Signature Navy Theme) */
.att-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    flex-shrink: 0;
}
.att-hero-left {
    display: flex;
    flex-direction: column;
}
.att-hero-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    font-weight: 600;
}
.att-hero-title {
    font-size: 15px;
    font-weight: 700;
    margin: 1px 0 0;
    line-height: 1.2;
    color: #ffffff;
}
.att-hero-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    opacity: .85;
    color: #cbd5e1;
}
.att-hero-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Date Picker Pill in Hero */
.att-date-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #051e38;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 2px 8px;
    color: #ffffff;
    height: 30px;
}
.att-date-pill label {
    margin: 0;
    font-size: 11px;
    font-weight: 600;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
}
.att-date-input {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 12px;
    font-weight: 600;
    outline: none;
    cursor: pointer;
    color-scheme: dark;
}

/* Hero Live Counter Badges */
.att-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 2px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    color: #ffffff;
    font-weight: 500;
}
.stat-pill b {
    font-weight: 700;
}
.stat-pill.pill-green { background: rgba(16, 185, 129, 0.22); border-color: rgba(16, 185, 129, 0.4); color: #a7f3d0; }
.stat-pill.pill-red { background: rgba(239, 68, 68, 0.22); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5; }
.stat-pill.pill-amber { background: rgba(245, 158, 11, 0.22); border-color: rgba(245, 158, 11, 0.4); color: #fde68a; }
.stat-pill.pill-purple { background: rgba(168, 85, 247, 0.22); border-color: rgba(168, 85, 247, 0.4); color: #e9d5ff; }
.stat-pill.pill-gray { background: rgba(148, 163, 184, 0.22); border-color: rgba(148, 163, 184, 0.4); color: #e2e8f0; }

/* 3. Navigation Tabs (Students vs Staff) */
.att-nav-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #002C54;
    padding: 4px 8px;
    border-radius: 2px;
    flex-shrink: 0;
    border-bottom: 2px solid #001f3d;
}
.att-tab-btn {
    padding: 5px 14px;
    font-size: 11.5px;
    font-weight: 600;
    color: #cbd5e1;
    border-radius: 2px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: 1px solid transparent;
    transition: all .15s ease-in-out;
}
.att-tab-btn:hover {
    color: #ffffff;
    background: rgba(255,255,255,.1);
}
.att-tab-btn.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #38bdf8;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.att-tab-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 10px;
    background: rgba(0,0,0,.25);
    color: #ffffff;
}

/* 4. Filter & Bulk Action Toolbar Card */
.att-action-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.att-action-left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.att-action-right {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-left: auto;
}

/* Search Box */
.att-search-box {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.att-search-box i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
}
.att-search-input {
    height: 28px;
    width: 190px;
    padding: 2px 8px 2px 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    background: #f8fafc;
}
.att-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 1px #0284c7;
}

/* Select Control */
.att-select {
    height: 28px;
    font-size: 11.5px;
    font-weight: 500;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 8px;
    background: #f8fafc;
    color: #1e293b;
    outline: none;
    cursor: pointer;
    min-width: 170px;
}
.att-select:focus {
    border-color: #0284c7;
    background: #ffffff;
}

/* Bulk Input Groups */
.att-bulk-group {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f1f5f9;
    padding: 2px 6px;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
}
.att-bulk-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin: 0;
    white-space: nowrap;
}
.btn-att-action {
    height: 24px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: all .15s;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    white-space: nowrap;
}
.btn-att-action:hover:not(:disabled) {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Quick Status Shortcuts */
.btn-quick-status {
    height: 24px;
    padding: 0 7px;
    font-size: 10.5px;
    font-weight: 600;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .15s;
    border: 1px solid transparent;
}
.btn-quick-in { background: #dcfce7; color: #166534; border-color: #86efac; }
.btn-quick-in:hover { background: #16a34a; color: #ffffff; border-color: #16a34a; }
.btn-quick-absent { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.btn-quick-absent:hover { background: #dc2626; color: #ffffff; border-color: #dc2626; }
.btn-quick-halfday { background: #ede9fe; color: #6b21a8; border-color: #d8b4fe; }
.btn-quick-halfday:hover { background: #9333ea; color: #ffffff; border-color: #9333ea; }
.btn-quick-reset { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
.btn-quick-reset:hover { background: #64748b; color: #ffffff; border-color: #64748b; }

/* Primary Save Button */
.btn-att-save {
    height: 28px;
    padding: 0 14px;
    font-size: 11.5px;
    font-weight: 700;
    background: #0284c7;
    border: 1px solid #0369a1;
    color: #ffffff;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}
.btn-att-save:hover:not(:disabled) {
    background: #0369a1;
    border-color: #0284c7;
}
.btn-att-save:disabled {
    opacity: .6;
    cursor: not-allowed;
}

/* 5. Main Table Card & Grid */
#attendance-main-form {
    flex: 1 1 0%;
    min-height: 0 !important;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin: 0;
}
.att-table-card {
    flex: 1 1 0%;
    min-height: 0 !important;
    display: flex;
    flex-direction: column;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    overflow: hidden;
}
.att-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.att-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-counter-badge {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* Scroll Container */
.att-scroll-container {
    flex: 1 1 0%;
    min-height: 0 !important;
    overflow-y: auto !important;
    overflow-x: auto;
    position: relative;
    background: #eef2f6;
}

/* Table Style */
.att-grid-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.att-grid-table thead th {
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
    z-index: 10;
    vertical-align: middle;
}
.att-grid-table tbody td {
    padding: 4px 8px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #cbd5e1;
    vertical-align: middle;
    color: #1e293b;
    background: #ffffff;
}
.att-grid-table tbody tr:nth-child(odd) td {
    background: #f8fafc;
}
.att-grid-table tbody tr:nth-child(even) td {
    background: #ffffff;
}
.att-grid-table tbody tr:hover td {
    background: #e2e8f0 !important;
}

/* Highlight rows based on marked status */
.att-grid-table tbody tr.row-marked-in td {
    background: #f0fdf4 !important;
}
.att-grid-table tbody tr.row-marked-absent td {
    background: #fef2f2 !important;
}
.att-grid-table tbody tr.row-marked-halfday td {
    background: #faf5ff !important;
}
.att-grid-table tbody tr.row-marked-holiday td {
    background: #f1f5f9 !important;
}

/* Table Inputs */
.att-cell-uid {
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 11px;
    font-weight: 700;
    color: #0369a1;
    background: #e0f2fe;
    padding: 2px 6px;
    border-radius: 2px;
    border: 1px solid #bae6fd;
    display: inline-block;
    white-space: nowrap;
}
.att-cell-name {
    font-weight: 600;
    color: #0f172a;
}

/* Status Dropdown in Table */
.status-select-modern {
    height: 25px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    padding: 1px 6px;
    border: 1px solid #cbd5e1;
    outline: none;
    cursor: pointer;
    width: 125px;
    transition: all .15s;
}
.status-select-modern.status-in, .status-select-modern.status-present { background: #dcfce7; color: #166534; border-color: #86efac; }
.status-select-modern.status-absent { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }
.status-select-modern.status-halfday { background: #ede9fe; color: #6b21a8; border-color: #d8b4fe; }
.status-select-modern.status-holiday { background: #e2e8f0; color: #334155; border-color: #cbd5e1; }
.status-select-modern.status-leave { background: #fef3c7; color: #92400e; border-color: #fcd34d; }
.status-select-modern.status-none { background: #f8fafc; color: #475569; border-color: #cbd5e1; }

/* Status Pill in Table */
.att-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 2px;
    white-space: nowrap;
    text-transform: uppercase;
}
.att-badge-pill.badge-in, .att-badge-pill.badge-present { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.att-badge-pill.badge-absent { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.att-badge-pill.badge-halfday { background: #ede9fe; color: #6b21a8; border: 1px solid #d8b4fe; }
.att-badge-pill.badge-holiday { background: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }
.att-badge-pill.badge-leave { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.att-badge-pill.badge-none { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }

/* Row Reset Button */
.btn-row-reset {
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
    font-size: 10px;
    transition: all .15s;
}
.btn-row-reset:hover {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}

/* 6. Centered Empty State */
.att-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 280px;
    padding: 40px 16px;
    text-align: center;
    color: #475569;
}
.att-empty-icon {
    font-size: 40px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.att-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.att-empty-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 380px;
    line-height: 1.4;
}

/* 7. Bottom Pinned Summary Toolbar */
.att-bottom-bar {
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
.att-bottom-info {
    font-size: 11.5px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 12px;
}
.att-bottom-info b {
    color: #ffffff;
}
.att-bottom-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper att-page-wrapper">
    <section class="content p-2">
        <div class="container-fluid p-0">
            <div class="att-layout">

                {{-- 1. Arise ERP Signature Dark Navy Hero Header --}}
                <div class="att-hero">
                    <div class="att-hero-left">
                        <span class="att-hero-kicker"><i class="fa fa-calendar-check-o mr-1"></i> Attendance Management</span>
                        <h1 class="att-hero-title">Daily Attendance Marking</h1>
                        <p class="att-hero-subtitle">Mark daily attendance status updates for students and staff</p>
                    </div>

                    <div class="att-hero-right">
                        {{-- Live Counter Badges --}}
                        <div class="att-hero-stats">
                            <span class="stat-pill" title="Total in current selection">
                                <i class="fa fa-list-alt"></i> Total: <b id="stat-total">{{ $totalCount }}</b>
                            </span>
                            <span class="stat-pill pill-green" title="Present / In">
                                <i class="fa fa-check-circle"></i> Present: <b id="stat-in">{{ $markedInCount }}</b>
                            </span>
                            <span class="stat-pill pill-red" title="Absent">
                                <i class="fa fa-times-circle"></i> Absent: <b id="stat-absent">{{ $markedAbsentCount }}</b>
                            </span>
                            <span class="stat-pill pill-purple" title="Half Day">
                                <i class="fa fa-adjust"></i> Half Day: <b id="stat-halfday">{{ $markedHalfdayCount }}</b>
                            </span>
                            <span class="stat-pill pill-gray" title="Not Marked Yet">
                                <i class="fa fa-clock-o"></i> Unmarked: <b id="stat-unmarked">{{ $unmarkedCount }}</b>
                            </span>
                        </div>

                        {{-- Date Selector in Hero --}}
                        <form method="get" action="{{ $attendanceBaseUrl }}" class="m-0">
                            <div class="att-date-pill">
                                <label for="att-date-input"><i class="fa fa-calendar"></i> Date:</label>
                                <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">
                                @if(!$isStaffTab && request('class_type_id'))
                                    <input type="hidden" name="class_type_id" value="{{ request('class_type_id') }}">
                                @endif
                                @if($isStaffTab && request('role_id'))
                                    <input type="hidden" name="role_id" value="{{ request('role_id') }}">
                                @endif
                                <input type="date" id="att-date-input" name="date" class="att-date-input" value="{{ $selectedDate }}" @if(!($allowBackDateForUser ?? false)) min="{{ date('Y-m-d') }}" @endif max="{{ date('Y-m-d') }}" onchange="this.form.submit()">
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Alerts & Notifications --}}
                @if(session('message'))
                    <div class="alert alert-success py-2 mb-1">{{ session('message') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2 mb-1">{{ session('error') }}</div>
                @endif
                @if($holidayLock)
                    <div class="alert alert-warning py-1 mb-1 font-weight-bold">
                        <i class="fa fa-lock mr-1"></i> Selected date ({{ date('d/m/Y', strtotime($selectedDate)) }}) is marked as a Holiday in the academic calendar. Attendance marking is currently locked.
                    </div>
                @endif
                @if(!($canAccessStaffAttendance ?? false) && $classes->isEmpty())
                    <div class="alert alert-warning py-1 mb-1">
                        <i class="fa fa-exclamation-triangle mr-1"></i> No classes are assigned to your teacher account. Please contact the school administrator.
                    </div>
                @endif

                {{-- 2. Navigation Tabs (Students vs Staff) --}}
                <div class="att-nav-tabs">
                    <a class="att-tab-btn {{ !$isStaffTab ? 'active' : '' }}" href="{{ $studentsTabUrl }}">
                        <i class="fa fa-graduation-cap"></i> Students Attendance
                        <span class="att-tab-badge">{{ !$isStaffTab ? $totalCount : '' }}</span>
                    </a>
                    @if($canAccessStaffAttendance ?? false)
                    <a class="att-tab-btn {{ $isStaffTab ? 'active' : '' }}" href="{{ $staffTabUrl }}">
                        <i class="fa fa-users"></i> Staff Attendance
                        <span class="att-tab-badge">{{ $isStaffTab ? $totalCount : '' }}</span>
                    </a>
                    @endif
                </div>

                {{-- 3. Form & Controls Area --}}
                <form method="post" action="{{ $isStaffTab ? $staffSaveUrl : $studentsSaveUrl }}" id="attendance-main-form" class="d-flex flex-column flex-grow-1 min-h-0 m-0">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate }}">

                    {{-- Filter & Action Bar --}}
                    <div class="att-action-bar">
                        <div class="att-action-left">
                            {{-- Class or Role Selector --}}
                            @if(!$isStaffTab)
                                <select id="studentClassFilter" name="class_type_id" class="att-select select2">
                                    <option value="">{{ ($canAccessStaffAttendance ?? false) ? '-- Select Class --' : '-- Select Assigned Class --' }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_type_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="classFilterInput" value="{{ request('class_type_id') }}">
                            @else
                                <select id="staffRoleFilter" name="role_id" class="att-select select2">
                                    <option value="">-- All Staff Roles --</option>
                                    @foreach($staffRoles as $role)
                                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="roleFilterInput" value="{{ request('role_id') }}">
                            @endif

                            {{-- Real-time instant search filter --}}
                            <div class="att-search-box">
                                <i class="fa fa-search"></i>
                                <input type="text" id="table-instant-search" class="att-search-input" placeholder="Search name or ID...">
                            </div>
                        </div>

                        <div class="att-action-right">
                            {{-- Bulk Status Dropdown & 1-Click Shortcuts --}}
                            <div class="att-bulk-group">
                                <span class="att-bulk-label">Status:</span>
                                <select id="bulkStatusSelect" class="att-select" style="min-width: 110px; height: 24px; padding: 1px 4px;" @if($holidayLock) disabled @endif>
                                    <option value="">Select Status</option>
                                    @foreach($attendanceStatuses as $attendanceStatus)
                                        @php
                                            $val = match ((int) $attendanceStatus->id) {
                                                1 => 'in',
                                                2 => 'out',
                                                3 => 'absent',
                                                4 => 'halfday',
                                                default => \Illuminate\Support\Str::slug($attendanceStatus->name, '_'),
                                            };
                                        @endphp
                                        <option value="{{ $val }}">{{ $attendanceStatus->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn-att-action" id="btn-apply-bulk-status" @if($holidayLock) disabled @endif>
                                    Apply
                                </button>
                            </div>

                            {{-- Quick 1-Click Status Buttons --}}
                            <div class="d-none d-lg-inline-flex align-items-center" style="gap: 4px;">
                                <button type="button" class="btn-quick-status btn-quick-in" id="btn-quick-all-in" title="Mark selected Present" @if($holidayLock) disabled @endif>
                                    <i class="fa fa-check"></i> All Present
                                </button>
                                <button type="button" class="btn-quick-status btn-quick-absent" id="btn-quick-all-absent" title="Mark selected Absent" @if($holidayLock) disabled @endif>
                                    <i class="fa fa-times"></i> All Absent
                                </button>
                                <button type="button" class="btn-quick-status btn-quick-halfday" id="btn-quick-all-halfday" title="Mark selected Half Day" @if($holidayLock) disabled @endif>
                                    <i class="fa fa-adjust"></i> Half Day
                                </button>
                                <button type="button" class="btn-quick-status btn-quick-reset" id="btn-quick-reset-all" title="Clear selected rows" @if($holidayLock) disabled @endif>
                                    <i class="fa fa-undo"></i> Reset
                                </button>
                            </div>

                            {{-- Save Button in Header --}}
                            <button type="submit" class="btn-att-save" id="btn-submit-top" @if($holidayLock) disabled @endif>
                                <i class="fa fa-save"></i> Save Attendance
                            </button>
                        </div>
                    </div>

                    {{-- 4. Full Viewport Table Card --}}
                    <div class="att-table-card">
                        <div class="att-card-header">
                            <h3 class="att-card-title">
                                <i class="fa fa-table text-info"></i>
                                {{ $isStaffTab ? 'Staff Attendance Directory' : 'Student Attendance Directory' }}
                            </h3>
                            <div>
                                <span class="att-counter-badge">
                                    <span id="badge-visible-count">{{ $totalCount }}</span> / {{ $totalCount }} Records
                                </span>
                            </div>
                        </div>

                        {{-- Scrollable Table Area --}}
                        <div class="att-scroll-container">
                            <table class="att-grid-table" id="attendanceGridTable">
                                <thead>
                                    <tr>
                                        <th style="width: 44px;" class="text-center">#</th>
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" id="check-all-rows" checked title="Select / Deselect all" @if($holidayLock) disabled @endif>
                                        </th>
                                        <th style="width: 140px;" class="text-center">Unique ID</th>
                                        <th style="min-width: 220px;">Name</th>
                                        <th style="width: 140px;" class="text-center">Status</th>
                                        <th style="width: 120px;" class="text-center">Indicator</th>
                                        <th style="width: 60px;" class="text-center">Reset</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeItems as $i => $rowItem)
                                        @php
                                            $uniqueId = $rowItem->attendance_unique_id;
                                            $mark = $attendanceMarks[$uniqueId] ?? null;
                                            $statusVal = $mark->status ?? '';
                                            $fullName = trim(($rowItem->first_name ?? '') . ' ' . ($rowItem->last_name ?? ''));
                                            $entityType = $isStaffTab ? 'staff' : 'student';

                                            $rowStatusClass = match($statusVal) {
                                                'in', 'present' => 'row-marked-in',
                                                'absent' => 'row-marked-absent',
                                                'halfday' => 'row-marked-halfday',
                                                'holiday' => 'row-marked-holiday',
                                                default => ''
                                            };
                                        @endphp
                                        <tr class="att-record-row {{ $rowStatusClass }}"
                                            data-index="{{ $i }}"
                                            data-unique-id="{{ $uniqueId }}"
                                            data-name="{{ strtolower($fullName) }}">
                                            {{-- S.No. --}}
                                            <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>

                                            {{-- Select Checkbox --}}
                                            <td class="text-center">
                                                <input type="checkbox" class="row-check" checked @if($holidayLock) disabled @endif>
                                            </td>

                                            {{-- Unique ID --}}
                                            <td class="text-center">
                                                <span class="att-cell-uid">{{ $uniqueId }}</span>
                                            </td>

                                            {{-- Person Name --}}
                                            <td>
                                                <span class="att-cell-name">{{ $fullName }}</span>
                                            </td>

                                            {{-- Status Dropdown --}}
                                            <td class="text-center">
                                                <input type="hidden" name="rows[{{ $i }}][unique_id]" value="{{ $uniqueId }}">
                                                <input type="hidden" name="rows[{{ $i }}][entity_type]" value="{{ $entityType }}">
                                                <select class="status-select-modern row-status-select"
                                                    name="rows[{{ $i }}][status]"
                                                    @if($holidayLock) disabled @endif>
                                                    <option value="">Select</option>
                                                    @foreach($attendanceStatuses as $attendanceStatus)
                                                        @php
                                                            $optVal = match ((int) $attendanceStatus->id) {
                                                                1 => 'in',
                                                                2 => 'out',
                                                                3 => 'absent',
                                                                4 => 'halfday',
                                                                default => \Illuminate\Support\Str::slug($attendanceStatus->name, '_'),
                                                            };
                                                        @endphp
                                                        <option value="{{ $optVal }}" {{ ($statusVal === $optVal || ($optVal === 'in' && $statusVal === 'present')) ? 'selected' : '' }}>
                                                            {{ $attendanceStatus->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            {{-- Indicator Badge --}}
                                            <td class="text-center">
                                                <span class="att-badge-pill row-status-badge badge-none">
                                                    Not Marked
                                                </span>
                                            </td>

                                            {{-- Reset Action --}}
                                            <td class="text-center">
                                                <button type="button" class="btn-row-reset" title="Reset this record" @if($holidayLock) disabled @endif>
                                                    <i class="fa fa-undo"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="empty-state-row">
                                            <td colspan="7" class="p-0">
                                                <div class="att-empty-state">
                                                    <div class="att-empty-icon"><i class="fa fa-folder-open-o"></i></div>
                                                    <div class="att-empty-title">
                                                        @if(!$isStaffTab)
                                                            {{ request('class_type_id') ? 'No students found in the selected class.' : 'Please select a class from the dropdown to load students.' }}
                                                        @else
                                                            {{ request('role_id') ? 'No staff members found for the selected role.' : 'No active staff records available.' }}
                                                        @endif
                                                    </div>
                                                    <div class="att-empty-desc">Choose a class or role above to mark and review attendance records.</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- 5. Bottom Pinned Summary Toolbar --}}
                        <div class="att-bottom-bar">
                            <div class="att-bottom-info">
                                <span>Showing: <b id="bottom-visible-count">{{ $totalCount }}</b></span>
                                <span>Selected: <b id="bottom-selected-count">{{ $totalCount }}</b></span>
                                <span>Modified: <b id="bottom-modified-count" class="text-warning">0</b></span>
                            </div>
                            <div class="att-bottom-actions">
                                <button type="submit" class="btn-att-save" id="btn-submit-bottom" @if($holidayLock) disabled @endif>
                                    <i class="fa fa-save"></i> Save Attendance
                                </button>
                            </div>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </section>

    {{-- Client-Side High-Performance Engine --}}
    <script>
    $(document).ready(function() {
        const isHolidayLocked = @json($holidayLock);

        function formatStatusLabel(val) {
            if (!val) return 'Not Marked';
            return val.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        }

        function updateRowAppearance($row) {
            const $statusSelect = $row.find('.row-status-select');
            const $badge = $row.find('.row-status-badge');
            const statusVal = $statusSelect.val() || '';

            const allStatusClasses = 'status-in status-present status-absent status-halfday status-holiday status-leave status-none';
            const allBadgeClasses = 'badge-in badge-present badge-absent badge-halfday badge-holiday badge-leave badge-none';
            const allRowClasses = 'row-marked-in row-marked-absent row-marked-halfday row-marked-holiday';

            $statusSelect.removeClass(allStatusClasses);
            $badge.removeClass(allBadgeClasses);
            $row.removeClass(allRowClasses);

            if (statusVal) {
                $statusSelect.addClass('status-' + statusVal);
                $badge.addClass('badge-' + statusVal).text(formatStatusLabel(statusVal));

                if (statusVal === 'in' || statusVal === 'present') {
                    $row.addClass('row-marked-in');
                } else if (statusVal === 'absent') {
                    $row.addClass('row-marked-absent');
                } else if (statusVal === 'halfday') {
                    $row.addClass('row-marked-halfday');
                } else if (statusVal === 'holiday') {
                    $row.addClass('row-marked-holiday');
                }
            } else {
                $statusSelect.addClass('status-none');
                $badge.addClass('badge-none').text('Not Marked');
            }

            recalcStats();
        }

        function recalcStats() {
            let total = 0;
            let markedIn = 0;
            let markedAbsent = 0;
            let markedHalfday = 0;
            let unmarked = 0;
            let selectedCount = 0;
            let modifiedCount = 0;

            $('.att-record-row:visible').each(function() {
                total++;
                const $r = $(this);
                if ($r.find('.row-check').is(':checked')) {
                    selectedCount++;
                }

                const st = $r.find('.row-status-select').val() || '';
                if (st === 'in' || st === 'present') {
                    markedIn++;
                } else if (st === 'absent') {
                    markedAbsent++;
                } else if (st === 'halfday') {
                    markedHalfday++;
                } else {
                    unmarked++;
                }

                const orig = $r.data('att-orig') || {};
                if (st !== (orig.status || '')) {
                    modifiedCount++;
                }
            });

            $('#stat-total, #badge-visible-count, #bottom-visible-count').text(total);
            $('#stat-in').text(markedIn);
            $('#stat-absent').text(markedAbsent);
            $('#stat-halfday').text(markedHalfday);
            $('#stat-unmarked').text(unmarked);
            $('#bottom-selected-count').text(selectedCount);
            $('#bottom-modified-count').text(modifiedCount);
        }

        // Snapshot original data
        $('.att-record-row').each(function() {
            const $r = $(this);
            const origSt = $r.find('.row-status-select').val() || '';
            $r.data('att-orig', { status: origSt });
            updateRowAppearance($r);
        });

        // Instant Table Search Filter
        $('#table-instant-search').on('input keyup', function() {
            const query = $(this).val().toLowerCase().trim();
            $('.att-record-row').each(function() {
                const $r = $(this);
                const name = $r.data('name') || '';
                const uid = ($r.data('unique-id') || '').toString().toLowerCase();
                const matches = !query || name.indexOf(query) > -1 || uid.indexOf(query) > -1;
                $r.toggle(matches);
            });
            recalcStats();
        });

        function updateQueryParam(key, val) {
            const url = new URL(window.location.href);
            if (val) {
                url.searchParams.set(key, val);
            } else {
                url.searchParams.delete(key);
            }
            window.location.href = url.toString();
        }

        $('#studentClassFilter').on('change', function() {
            updateQueryParam('class_type_id', $(this).val());
        });

        $('#staffRoleFilter').on('change', function() {
            updateQueryParam('role_id', $(this).val());
        });

        // Select All
        $('#check-all-rows').on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.att-record-row:visible .row-check').prop('checked', isChecked);
            recalcStats();
        });

        $(document).on('change', '.row-check', function() {
            const total = $('.att-record-row:visible .row-check').length;
            const checked = $('.att-record-row:visible .row-check:checked').length;
            $('#check-all-rows').prop('checked', total > 0 && total === checked);
            recalcStats();
        });

        // Input change events
        $(document).on('change', '.row-status-select', function() {
            updateRowAppearance($(this).closest('tr'));
        });

        // Single Row Reset
        $(document).on('click', '.btn-row-reset', function() {
            if (isHolidayLocked) return;
            const $r = $(this).closest('tr');
            $r.find('.row-status-select').val('');
            updateRowAppearance($r);
        });

        // Apply Bulk Status from dropdown
        $('#btn-apply-bulk-status').on('click', function() {
            if (isHolidayLocked) return;
            const st = $('#bulkStatusSelect').val();
            if (!st) {
                alert('Please select a status to apply.');
                return;
            }

            $('.att-record-row:visible').each(function() {
                const $r = $(this);
                if ($r.find('.row-check').is(':checked')) {
                    $r.find('.row-status-select').val(st);
                    updateRowAppearance($r);
                }
            });
        });

        // Quick 1-Click Status buttons
        function applyQuickStatus(statusValue) {
            if (isHolidayLocked) return;
            $('.att-record-row:visible').each(function() {
                const $r = $(this);
                if ($r.find('.row-check').is(':checked')) {
                    $r.find('.row-status-select').val(statusValue);
                    updateRowAppearance($r);
                }
            });
        }

        $('#btn-quick-all-in').on('click', function() { applyQuickStatus('in'); });
        $('#btn-quick-all-absent').on('click', function() { applyQuickStatus('absent'); });
        $('#btn-quick-all-halfday').on('click', function() { applyQuickStatus('halfday'); });

        $('#btn-quick-reset-all').on('click', function() {
            if (isHolidayLocked) return;
            if (!confirm('Reset status for all selected rows?')) return;
            $('.att-record-row:visible').each(function() {
                const $r = $(this);
                if ($r.find('.row-check').is(':checked')) {
                    $r.find('.row-status-select').val('');
                    updateRowAppearance($r);
                }
            });
        });

        // High-Performance Form Submit Engine
        $('#attendance-main-form').on('submit', function(e) {
            if (isHolidayLocked) {
                e.preventDefault();
                alert('Attendance marking is locked on holidays.');
                return false;
            }

            const changedRows = [];
            $('.att-record-row').each(function() {
                const $r = $(this);
                if (!$r.find('.row-check').is(':checked')) return;

                const orig = $r.data('att-orig') || {};
                const currentStatus = $r.find('.row-status-select').val() || '';

                if (currentStatus !== (orig.status || '')) {
                    changedRows.push({
                        selected: '1',
                        unique_id: $r.find('input[name$="[unique_id]"]').val() || '',
                        entity_type: $r.find('input[name$="[entity_type]"]').val() || '',
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

            $('#btn-submit-top, #btn-submit-bottom').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        });

        if ($.fn.select2) {
            $('.select2').select2({ width: 'resolve' });
        }
    });
    </script>
</div>
@endsection

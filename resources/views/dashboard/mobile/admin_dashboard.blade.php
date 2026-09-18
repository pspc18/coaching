@extends('layout.mobile_app')

@section('content')
@php
    $totalStudents = (int) ($studentStats['total'] ?? 0);
    $presentStudents = (int) ($attendanceStats['present'] ?? 0);
    $absentStudents = (int) ($attendanceStats['absent'] ?? 0);
    $unmarkedStudents = (int) ($attendanceStats['unmarked'] ?? 0);
    $attendancePercent = $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100) : 0;
    
    $assignedFees = (float) ($feeStats['assigned'] ?? 0);
    $collectedFees = (float) ($feeStats['collected'] ?? 0);
    $todayFees = (float) ($feeStats['today'] ?? 0);
    $pendingFees = max(0, $assignedFees - $collectedFees);
    $feePercent = $assignedFees > 0 ? round(($collectedFees / $assignedFees) * 100) : 0;

    $totalExpenses = (float) ($expenseStats['total'] ?? 0);
    $todayExpenses = (float) ($expenseStats['today'] ?? 0);
    $monthExpenses = (float) ($expenseStats['month'] ?? 0);
    $netBalance = $collectedFees - $totalExpenses;

    $totalStaff = (int) ($staffStats['total'] ?? 0);
    $presentStaff = (int) ($staffStats['present'] ?? 0);
    $staffPercent = $totalStaff > 0 ? round(($presentStaff / $totalStaff) * 100) : 0;

    $birthdays = collect($birthdays ?? []);
    $notices = collect($notices ?? []);
    $userName = Session::get('first_name') ?? 'Admin';
@endphp

<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE DASHBOARD (SHARP CARDS + CIRCULAR ACCENTS)
   ========================================================================== */

/* 1. Top Hero Snapshot (Clean Greeting & Fast Actions) */
.native-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 12px 14px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}
.native-hero-card::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(56, 189, 248, 0) 70%);
    pointer-events: none;
}
.native-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.hero-user-cluster {
    display: flex;
    align-items: center;
    gap: 10px;
}
.hero-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    border: 2px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}
.hero-user-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
}
.hero-user-sub {
    font-size: 9.5px;
    color: #93c5fd;
    font-weight: 600;
    letter-spacing: .02em;
}

.hero-session-pill {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #bae6fd !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Dual Live Radar in Hero */
.hero-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    background: rgba(0, 15, 30, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 10px;
}
.hero-radar-col {
    display: flex;
    flex-direction: column;
}
.hero-radar-col:first-child {
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    padding-right: 6px;
}
.hero-radar-tag {
    font-size: 9.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 4px;
}
.hero-radar-val {
    font-size: 17px;
    font-weight: 800;
    color: #38bdf8;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.hero-radar-meta {
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 500;
}

/* Hero Fast Action Buttons */
.hero-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.hero-action-btn {
    flex: 1;
    padding: 6px 4px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    line-height: 1.2;
    white-space: nowrap;
    transition: transform .12s ease;
}
.hero-action-btn:active {
    transform: scale(0.96);
}
.act-btn-admission {
    background: #ffffff;
    color: #002C54 !important;
    border: 1px solid #ffffff;
}
.act-btn-fee {
    background: #10b981;
    color: #ffffff !important;
    border: 1px solid #10b981;
}
.act-btn-expense {
    background: #f43f5e;
    color: #ffffff !important;
    border: 1px solid #f43f5e;
}

/* 2. 8-Tile Compact App Launcher (Circular Icons + Sharp Tile Box) */
.native-launcher-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.native-launcher-tile {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 4px 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    color: #1e293b;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: all .12s ease;
}
.native-launcher-tile:active {
    transform: scale(0.94);
    background: #f1f5f9;
    border-color: #0284c7;
}
.launcher-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.12);
}
.launcher-tile-title {
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    text-align: center;
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
    padding: 0 2px;
}

.circle-grad-1 { background: linear-gradient(135deg, #0284c7, #0369a1); }
.circle-grad-2 { background: linear-gradient(135deg, #10b981, #059669); }
.circle-grad-3 { background: linear-gradient(135deg, #f59e0b, #d97706); }
.circle-grad-4 { background: linear-gradient(135deg, #f43f5e, #e11d48); }
.circle-grad-5 { background: linear-gradient(135deg, #eab308, #ca8a04); }
.circle-grad-6 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.circle-grad-7 { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.circle-grad-8 { background: linear-gradient(135deg, #64748b, #475569); }

/* 3. Dual Symmetrical Radar Row */
.native-radar-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.native-radar-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    text-decoration: none !important;
    color: inherit !important;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.native-radar-box:active {
    background: #f8fafc;
}
.radar-box-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}
.radar-box-tag {
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: .03em;
}
.radar-icon-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}
.radar-box-number {
    font-size: 18px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.15;
    margin: 2px 0;
}
.radar-box-sub {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
}

/* 4. Segment Navigation Pills */
.native-segments-bar {
    display: flex;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
}
.native-seg-btn {
    flex: 1;
    padding: 6.5px 2px;
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    user-select: none;
    white-space: nowrap;
}
.native-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}

/* Feed View Container for Tab Switching */
.native-feed-view {
    display: none;
}
.native-feed-view.active-feed {
    display: block !important;
}

/* 5. Sharp Section Cards */
.sharp-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.sharp-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.sharp-card-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.sharp-card-link {
    font-size: 10.5px;
    font-weight: 700;
    color: #0284c7;
    text-decoration: none !important;
}

/* 6. PROMINENT ABSENT PDF REPORT BANNER (INSIDE ATTENDANCE CONTENT) */
.absent-pdf-action-card {
    background: #fff1f2;
    border: 1px solid #fecdd3;
    border-left: 3px solid #e11d48;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.absent-pdf-info {
    display: flex;
    align-items: center;
    gap: 8px;
}
.absent-pdf-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #ffe4e6;
    color: #e11d48;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.absent-pdf-btn {
    padding: 4px 10px;
    background: #e11d48;
    color: #ffffff !important;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: background .12s ease;
}
.absent-pdf-btn:active {
    background: #be123c;
}

/* 7. REDESIGNED FINANCIAL BREAKDOWN (ZERO OVERLAP, DUAL SYMMETRICAL COLUMNS) */
.fin-metrics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
}
.fin-metric-box {
    border-radius: 4px;
    padding: 8px 10px;
    display: flex;
    flex-direction: column;
}
.fin-metric-box.box-income {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.fin-metric-box.box-expense {
    background: #fef2f2;
    border: 1px solid #fecaca;
}
.fin-metric-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2px;
}
.fin-metric-title {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.box-income .fin-metric-title { color: #166534; }
.box-expense .fin-metric-title { color: #991b1b; }

.fin-metric-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}
.box-income .fin-metric-icon { background: #dcfce7; color: #16a34a; }
.box-expense .fin-metric-icon { background: #fee2e2; color: #dc2626; }

.fin-metric-value {
    font-size: 17px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.box-income .fin-metric-value { color: #15803d; }
.box-expense .fin-metric-value { color: #b91c1c; }

.fin-metric-sub {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
}

/* Progress & Meta Bar */
.fin-progress-bar-wrap {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin: 6px 0 4px;
}
.fin-progress-fill {
    height: 100%;
    border-radius: 3px;
}

.fin-summary-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 10.5px;
    color: #334155;
    margin-top: 6px;
}

/* Direct Action Buttons under Breakdown */
.fin-action-btn-row {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}
.btn-fin-action {
    flex: 1;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-fin-green { background: #16a34a; color: #ffffff !important; }
.btn-fin-red { background: #dc2626; color: #ffffff !important; }

/* 8. Attendance Matrix Grid */
.matrix-pills-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}
.matrix-status-cell {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
}
.matrix-status-tag {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.matrix-status-val {
    font-size: 15px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.15;
    margin-top: 1px;
}
.cell-present { background: #f0fdf4; border-color: #bbf7d0; }
.cell-present .matrix-status-val { color: #16a34a; }
.cell-absent { background: #fef2f2; border-color: #fecaca; }
.cell-absent .matrix-status-val { color: #dc2626; }
.cell-leave { background: #fffbeb; border-color: #fde68a; }
.cell-leave .matrix-status-val { color: #d97706; }
.cell-half { background: #faf5ff; border-color: #e9d5ff; }
.cell-half .matrix-status-val { color: #9333ea; }
.cell-holiday { background: #f0f9ff; border-color: #bae6fd; }
.cell-holiday .matrix-status-val { color: #0284c7; }

/* 9. Notice & Birthday Items */
.native-list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
}
.native-list-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.item-circle-badge {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.item-info-col {
    flex: 1;
    overflow: hidden;
}
.item-primary-txt {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.item-secondary-txt {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 1px;
}
</style>

{{-- 1. Clean Glassmorphic Hero Card --}}
<div class="native-hero-card">
    <div class="native-hero-top">
        <div class="hero-user-cluster">
            <div class="hero-avatar-circle">
                <i class="fa fa-user"></i>
            </div>
            <div>
                <div class="hero-user-title">Test Dashboard &bull; {{ $userName }} 👋</div>
                <div class="hero-user-sub">{{ now()->format('l, d M Y') }}</div>
            </div>
        </div>
        <div class="hero-session-pill">
            <i class="fa fa-graduation-cap"></i> {{ Session::get('session_name') ?? date('Y') }}
        </div>
    </div>

    {{-- Dual Radar Glance --}}
    <div class="hero-dual-grid">
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-inr text-warning"></i> Today's Fee
            </span>
            <div class="hero-radar-val">₹ {{ number_format($todayFees, 0) }}</div>
            <span class="hero-radar-meta">Total: ₹ {{ number_format($collectedFees, 0) }}</span>
        </div>
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-check-circle text-success"></i> Student Presence
            </span>
            <div class="hero-radar-val" style="color: #4ade80;">{{ $attendancePercent }}%</div>
            <span class="hero-radar-meta">{{ $presentStudents }} / {{ $totalStudents }} Present</span>
        </div>
    </div>

    {{-- Fast Action Buttons --}}
    <div class="hero-actions-bar">
        <a href="{{ url('admissionView') }}" class="hero-action-btn act-btn-admission">
            <i class="fa fa-user-plus text-primary"></i> + Admission
        </a>
        <a href="{{ url('feesCollectAdd') }}" class="hero-action-btn act-btn-fee">
            <i class="fa fa-inr"></i> Collect Fee
        </a>
        <a href="{{ url('expenseAdd') }}" class="hero-action-btn act-btn-expense">
            <i class="fa fa-plus-circle"></i> + Expense
        </a>
    </div>
</div>

{{-- 2. 8-Tile Compact App Launcher Grid --}}
<div class="native-launcher-grid">
    <a href="{{ url('admissionView') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-1"><i class="fa fa-graduation-cap"></i></div>
        <span class="launcher-tile-title">Admission</span>
    </a>
    <a href="{{ url('feesCollectAdd') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-2"><i class="fa fa-inr"></i></div>
        <span class="launcher-tile-title">Collect Fee</span>
    </a>
    <a href="{{ url('studentsAttendanceAdd') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-3"><i class="fa fa-calendar-check-o"></i></div>
        <span class="launcher-tile-title">Attendance</span>
    </a>
    <a href="{{ url('expenseAdd') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-4"><i class="fa fa-credit-card"></i></div>
        <span class="launcher-tile-title">Expense</span>
    </a>
    <a href="{{ url('notice-management') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-5"><i class="fa fa-bullhorn"></i></div>
        <span class="launcher-tile-title">Notices</span>
    </a>
    <a href="{{ url('viewUser') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-6"><i class="fa fa-users"></i></div>
        <span class="launcher-tile-title">Staff Duty</span>
    </a>
    <a href="{{ url('role_add') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-7"><i class="fa fa-shield"></i></div>
        <span class="launcher-tile-title">Roles</span>
    </a>
    <a href="{{ url('editSetting/1') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-8"><i class="fa fa-cog"></i></div>
        <span class="launcher-tile-title">Settings</span>
    </a>
</div>

{{-- 3. Dual Symmetrical Radar Row --}}
<div class="native-radar-row">
    <a href="{{ url('admissionView') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Active Students</span>
            <div class="radar-icon-dot" style="background: #e0f2fe; color: #0284c7;">
                <i class="fa fa-users"></i>
            </div>
        </div>
        <div class="radar-box-number">{{ number_format($totalStudents) }}</div>
        <div class="radar-box-sub">{{ $studentStats['male'] ?? 0 }} Boys &bull; {{ $studentStats['female'] ?? 0 }} Girls</div>
    </a>

    <a href="{{ url('viewUser') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Staff on Duty</span>
            <div class="radar-icon-dot" style="background: #f3e8ff; color: #9333ea;">
                <i class="fa fa-user-circle"></i>
            </div>
        </div>
        <div class="radar-box-number">{{ number_format($presentStaff) }} <span style="font-size: 12px; font-weight: 600; color: #64748b;">/ {{ $totalStaff }}</span></div>
        <div class="radar-box-sub">{{ $staffStats['away'] ?? 0 }} Away / On Leave</div>
    </a>
</div>

{{-- 4. Native Segment Navigation Pills --}}
<div class="native-segments-bar">
    <button type="button" class="native-seg-btn active" data-target="feed-all" onclick="switchNativeTab('feed-all', this)">
        <i class="fa fa-th-large mr-1"></i> All
    </button>
    <button type="button" class="native-seg-btn" data-target="feed-attendance" onclick="switchNativeTab('feed-attendance', this)">
        <i class="fa fa-calendar-check-o mr-1"></i> Attendance
    </button>
    <button type="button" class="native-seg-btn" data-target="feed-finances" onclick="switchNativeTab('feed-finances', this)">
        <i class="fa fa-inr mr-1"></i> Finances
    </button>
    <button type="button" class="native-seg-btn" data-target="feed-notices" onclick="switchNativeTab('feed-notices', this)">
        <i class="fa fa-bullhorn mr-1"></i> Notices ({{ $notices->count() }})
    </button>
</div>

{{-- Segment 1: ATTENDANCE BREAKDOWN & DEDICATED ABSENT PDF ACTION --}}
<div class="native-feed-view active-feed" id="feed-attendance" style="display: block;">
    
    {{-- Dedicated Absent PDF Action Card right in Attendance Area --}}
    <div class="absent-pdf-action-card">
        <div class="absent-pdf-info">
            <div class="absent-pdf-icon">
                <i class="fa fa-file-pdf-o"></i>
            </div>
            <div>
                <div style="font-size: 11.5px; font-weight: 800; color: #881337;">Today's Absent / Unmarked List</div>
                <div style="font-size: 9.5px; color: #9f1239;">{{ $absentStudents }} absent &bull; {{ $unmarkedStudents }} unmarked students</div>
            </div>
        </div>
        <a href="{{ url('attendance/today-absent-not-marked-pdf') }}" target="_blank" class="absent-pdf-btn">
            <i class="fa fa-download mr-1"></i> Download PDF
        </a>
    </div>

    {{-- Student Attendance Matrix --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-graduation-cap text-primary"></i> Student Attendance Breakdown
            </h3>
            <a href="{{ url('studentsAttendanceAdd') }}" class="sharp-card-link">
                Mark Attendance <i class="fa fa-angle-right"></i>
            </a>
        </div>
        <div class="matrix-pills-row">
            <div class="matrix-status-cell cell-present">
                <div class="matrix-status-tag">Present</div>
                <div class="matrix-status-val">{{ $studentAttendanceBreakdown['Present'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-absent">
                <div class="matrix-status-tag">Absent</div>
                <div class="matrix-status-val">{{ $studentAttendanceBreakdown['Absent'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-leave">
                <div class="matrix-status-tag">Leave</div>
                <div class="matrix-status-val">{{ $studentAttendanceBreakdown['Leave'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-half">
                <div class="matrix-status-tag">Half Day</div>
                <div class="matrix-status-val">{{ $studentAttendanceBreakdown['Half Day'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-holiday">
                <div class="matrix-status-tag">Holiday</div>
                <div class="matrix-status-val">{{ $studentAttendanceBreakdown['Holiday'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell">
                <div class="matrix-status-tag">Unmarked</div>
                <div class="matrix-status-val">{{ $unmarkedStudents }}</div>
            </div>
        </div>
    </div>

    {{-- Staff Attendance Matrix --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-users text-primary"></i> Staff Attendance Breakdown
            </h3>
            <span style="font-size: 10px; font-weight: 700; color: #64748b;">Total Staff: {{ $totalStaff }}</span>
        </div>
        <div class="matrix-pills-row">
            <div class="matrix-status-cell cell-present">
                <div class="matrix-status-tag">Present</div>
                <div class="matrix-status-val">{{ $staffAttendanceBreakdown['Present'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-absent">
                <div class="matrix-status-tag">Absent</div>
                <div class="matrix-status-val">{{ $staffAttendanceBreakdown['Absent'] ?? 0 }}</div>
            </div>
            <div class="matrix-status-cell cell-holiday">
                <div class="matrix-status-tag">WFH</div>
                <div class="matrix-status-val">{{ $staffAttendanceBreakdown['Work From Home'] ?? 0 }}</div>
            </div>
        </div>
    </div>

</div>

{{-- Segment 2: REDESIGNED FINANCIAL BREAKDOWN --}}
<div class="native-feed-view active-feed" id="feed-finances" style="display: block;">
    
    {{-- Card A: Fees & Revenue Breakdown --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-inr text-success"></i> Fees &amp; Revenue Breakdown
            </h3>
            <a href="{{ url('fee_dashboard') }}" class="sharp-card-link">
                Fee Dashboard <i class="fa fa-angle-right"></i>
            </a>
        </div>

        {{-- Symmetrical 2-Column Boxes --}}
        <div class="fin-metrics-grid">
            <div class="fin-metric-box box-income">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Total Collected</span>
                    <div class="fin-metric-icon"><i class="fa fa-arrow-down"></i></div>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($collectedFees, 2) }}</div>
                <div class="fin-metric-sub">{{ $feePercent }}% of Target</div>
            </div>

            <div class="fin-metric-box box-income">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Today Collected</span>
                    <div class="fin-metric-icon"><i class="fa fa-bolt"></i></div>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($todayFees, 2) }}</div>
                <div class="fin-metric-sub">Real-time Receipts</div>
            </div>
        </div>

        {{-- Target Progress Bar --}}
        <div class="fin-progress-bar-wrap">
            <div class="fin-progress-fill" style="width: {{ min(100, $feePercent) }}%; background: #16a34a;"></div>
        </div>

        <div class="fin-summary-strip">
            <span>Total Assigned Fees: <b>₹ {{ number_format($assignedFees, 2) }}</b></span>
            <span class="text-danger">Pending: <b>₹ {{ number_format($pendingFees, 2) }}</b></span>
        </div>

        <div class="fin-action-btn-row">
            <a href="{{ url('feesCollectAdd') }}" class="btn-fin-action btn-fin-green">
                <i class="fa fa-plus-circle mr-1"></i> Quick Collect Fee
            </a>
        </div>
    </div>

    {{-- Card B: Expenses Snapshot --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-credit-card text-danger"></i> Expenses Snapshot
            </h3>
            <a href="{{ url('expenseView') }}" class="sharp-card-link">
                Expense View <i class="fa fa-angle-right"></i>
            </a>
        </div>

        {{-- Symmetrical 2-Column Boxes --}}
        <div class="fin-metrics-grid">
            <div class="fin-metric-box box-expense">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Total Expenses</span>
                    <div class="fin-metric-icon"><i class="fa fa-arrow-up"></i></div>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($totalExpenses, 2) }}</div>
                <div class="fin-metric-sub">Session Total</div>
            </div>

            <div class="fin-metric-box box-expense">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">This Month</span>
                    <div class="fin-metric-icon"><i class="fa fa-calendar-o"></i></div>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($monthExpenses, 2) }}</div>
                <div class="fin-metric-sub">{{ now()->format('M Y') }}</div>
            </div>
        </div>

        <div class="fin-summary-strip">
            <span>Today's Expenses: <b>₹ {{ number_format($todayExpenses, 2) }}</b></span>
            <span class="{{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                Net Balance: <b>₹ {{ number_format($netBalance, 2) }}</b>
            </span>
        </div>

        <div class="fin-action-btn-row">
            <a href="{{ url('expenseAdd') }}" class="btn-fin-action btn-fin-red">
                <i class="fa fa-plus-circle mr-1"></i> Add Expense Voucher
            </a>
        </div>
    </div>

</div>

{{-- Segment 3: Notices & Birthdays --}}
<div class="native-feed-view active-feed" id="feed-notices" style="display: block;">
    
    {{-- Birthdays --}}
    @if($birthdays->isNotEmpty())
        <div class="sharp-section-card">
            <div class="sharp-card-header">
                <h3 class="sharp-card-title" style="color: #ca8a04;">
                    <i class="fa fa-birthday-cake text-warning"></i> Today's Birthdays ({{ $birthdays->count() }})
                </h3>
            </div>
            @foreach($birthdays as $bday)
                <div class="native-list-item">
                    <div class="item-circle-badge" style="background: #fef9c3; color: #ca8a04;">
                        <i class="fa fa-gift"></i>
                    </div>
                    <div class="item-info-col">
                        <div class="item-primary-txt">{{ trim(($bday->first_name ?? '') . ' ' . ($bday->last_name ?? '')) }}</div>
                        <div class="item-secondary-txt">Adm #{{ $bday->admissionNo ?? $bday->id }} &bull; Class {{ $bday->class_name ?? 'Student' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Announcements --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-bullhorn text-primary"></i> Active Announcements
            </h3>
            <a href="{{ url('notice-management/create') }}" class="sharp-card-link">
                <i class="fa fa-plus-circle mr-1"></i> New Notice
            </a>
        </div>

        @forelse($notices as $nt)
            <div class="native-list-item">
                <div class="item-circle-badge" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="item-info-col">
                    <div class="item-primary-txt">{{ $nt->title ?? 'Notice Announcement' }}</div>
                    <div class="item-secondary-txt">{{ Str::limit(strip_tags($nt->message ?? ''), 60) }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-3 text-muted" style="font-size: 11px;">
                <i class="fa fa-info-circle fa-2x text-muted mb-1 d-block"></i>
                No active announcements today.
            </div>
        @endforelse
    </div>

</div>

<script>
// Universal Native Tab Switcher
function switchNativeTab(targetId, btnElement) {
    // 1. Update button states
    var allBtns = document.querySelectorAll('.native-seg-btn');
    for (var i = 0; i < allBtns.length; i++) {
        allBtns[i].classList.remove('active');
    }
    
    if (btnElement) {
        btnElement.classList.add('active');
    } else {
        var matchBtn = document.querySelector('.native-seg-btn[data-target="' + targetId + '"]');
        if (matchBtn) matchBtn.classList.add('active');
    }

    var allViews = document.querySelectorAll('.native-feed-view');

    // 2. Tab switching logic
    if (targetId === 'feed-all') {
        // Show all feeds
        for (var j = 0; j < allViews.length; j++) {
            allViews[j].style.display = 'block';
            allViews[j].classList.remove('d-none');
            allViews[j].classList.add('active-feed');
        }
    } else {
        // Hide all feeds
        for (var k = 0; k < allViews.length; k++) {
            allViews[k].style.display = 'none';
            allViews[k].classList.remove('active-feed');
            allViews[k].classList.add('d-none');
        }

        // Show selected feed
        var activeView = document.getElementById(targetId);
        if (activeView) {
            activeView.style.display = 'block';
            activeView.classList.remove('d-none');
            activeView.classList.add('active-feed');
        }
    }
}

// Bind directly on page load
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.native-seg-btn');
    for (var m = 0; m < buttons.length; m++) {
        buttons[m].addEventListener('click', function(e) {
            e.preventDefault();
            var target = this.getAttribute('data-target');
            if (target) {
                switchNativeTab(target, this);
            }
        });
    }
});
</script>
@endsection
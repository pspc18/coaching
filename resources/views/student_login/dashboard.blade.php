@php
    $getSetting = Helper::getSetting();
    $dashboardStudent = $getUser ?? Helper::getUser();
    $studentId = (int) Session::get('id');
    $branchId = (int) Session::get('branch_id');
    $sessionId = (int) Session::get('session_id');

    $studentName = trim(($dashboardStudent->first_name ?? '').' '.($dashboardStudent->last_name ?? ''));
    $firstName = $dashboardStudent->first_name ?? 'Student';
    $className = optional($dashboardStudent->ClassTypes)->name ?? '-';
    $admissionNo = $dashboardStudent->admissionNo ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');

    $hour = (int) date('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $dashboardAvatar = !empty($dashboardStudent->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$dashboardStudent->image
        : null;

    // 1. Attendance Data
    $today = now()->toDateString();
    $todayAttendanceMark = null;
    $studentUniqueIds = array_values(array_filter([
        trim((string) ($dashboardStudent->attendance_unique_id ?? '')),
        trim((string) ($dashboardStudent->admissionNo ?? '')),
        'STU-' . $studentId,
    ]));

    if (!empty($studentUniqueIds)) {
        $todayAttendanceMark = \App\Models\AttendanceMark::query()
            ->whereDate('date', $today)
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->whereIn('unique_id', $studentUniqueIds)
            ->orderByDesc('updated_at')
            ->value('status');
    }

    $todayStatus = strtolower(trim((string) ($todayAttendanceMark ?? '')));
    if (in_array($todayStatus, ['present', 'in', 'out', 'early_out', 'earlyout'])) {
        $todayStatusLabel = 'Present';
        $todayStatusColor = '#10b981';
    } elseif ($todayStatus === 'absent') {
        $todayStatusLabel = 'Absent';
        $todayStatusColor = '#f43f5e';
    } elseif ($todayStatus === 'leave') {
        $todayStatusLabel = 'On Leave';
        $todayStatusColor = '#f59e0b';
    } elseif ($todayStatus === 'holiday') {
        $todayStatusLabel = 'Holiday';
        $todayStatusColor = '#0284c7';
    } elseif ($todayStatus === 'halfday') {
        $todayStatusLabel = 'Half Day';
        $todayStatusColor = '#8b5cf6';
    } else {
        $todayStatusLabel = 'Not Marked Yet';
        $todayStatusColor = '#94a3b8';
    }

    // Monthly Attendance Breakdown
    $monthMarks = \App\Models\AttendanceMark::query()
        ->whereYear('date', now()->year)
        ->whereMonth('date', now()->month)
        ->where('session_id', $sessionId)
        ->where('branch_id', $branchId)
        ->where('entity_type', 'student')
        ->whereIn('unique_id', $studentUniqueIds)
        ->select('status')
        ->get()
        ->map(fn($m) => strtolower(trim((string) $m->status)));

    $monthPresentCount = $monthMarks->filter(fn($s) => in_array($s, ['present', 'in', 'out', 'early_out', 'earlyout']))->count();
    $monthAbsentCount = $monthMarks->filter(fn($s) => $s === 'absent')->count();
    $monthLeaveCount = $monthMarks->filter(fn($s) => $s === 'leave')->count();
    $monthHalfDayCount = $monthMarks->filter(fn($s) => $s === 'halfday')->count();
    $monthHolidayCount = $monthMarks->filter(fn($s) => $s === 'holiday')->count();
    $monthTotalDays = $monthMarks->count();
    $monthAttendancePercent = $monthTotalDays > 0 ? round(($monthPresentCount / $monthTotalDays) * 100) : 100;

    // 2. Fees Data
    $assignedFees = (float) \App\Models\fees\FeesAssignDetail::where('admission_id', $studentId)
        ->where('session_id', $sessionId)
        ->sum('fees_group_amount');
    $collectedFees = (float) \App\Models\FeesDetail::where('admission_id', $studentId)
        ->where('session_id', $sessionId)
        ->where('status', '!=', '2')
        ->sum('total_amount');
    $pendingFees = max(0, $assignedFees - $collectedFees);
    $feePercent = $assignedFees > 0 ? round(($collectedFees / $assignedFees) * 100) : 0;

    // 3. Notices & Circulars
    $notices = \App\Models\ManagedNotice::where('branch_id', $branchId)
        ->where('session_id', $sessionId)
        ->where('status', 'published')
        ->latest()
        ->take(4)
        ->get();
    if ($notices->isEmpty()) {
        $notices = collect(Helper::noticeBoard() ?? [])->take(4);
    }

    // 4. Notifications / Alerts
    $notificationCount = DB::table('notifications')
        ->where('admission_id', $studentId)
        ->where('show_status', 1)
        ->count();

    // 5. Active Homework Count
    $activeHomeworkCount = \App\Models\Master\HomeWork::where('branch_id', $branchId)
        ->where('session_id', $sessionId)
        ->where('class_type_id', $dashboardStudent->class_type_id ?? 0)
        ->count();
@endphp

@extends('student_login.layout.app')

@section('title', 'Student Dashboard')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT MOBILE DASHBOARD (EXACT ADMIN MOBILE THEME)
   ========================================================================== */

/* 1. Top Hero Snapshot Card */
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    border: 2px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
    overflow: hidden;
}
.hero-avatar-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
    white-space: nowrap;
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
.act-btn-help {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. 8-Tile Compact App Launcher Grid */
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
    position: relative;
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
.launcher-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #ef4444;
    color: #ffffff;
    font-size: 8px;
    font-weight: 800;
    border-radius: 2px;
    padding: 1px 4px;
    line-height: 1;
}

/* Circular Gradients */
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
    font-size: 17px;
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

/* 6. Financial Breakdown (Dual Symmetrical Boxes) */
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
    background: #10b981;
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

/* 7. Attendance Matrix Grid */
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

/* 8. Notice & List Items */
.native-list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none !important;
    color: inherit;
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

{{-- 1. Top Hero Snapshot Card --}}
<div class="native-hero-card">
    <div class="native-hero-top">
        <div class="hero-user-cluster">
            <div class="hero-avatar-circle">
                @if(!empty($dashboardAvatar))
                    <img src="{{ $dashboardAvatar }}" alt="{{ $studentName }}">
                @else
                    <i class="fa fa-user"></i>
                @endif
            </div>
            <div>
                <div class="hero-user-title">{{ $greeting }}, {{ $firstName }}! 👋</div>
                <div class="hero-user-sub"><i class="fa fa-calendar-o mr-1"></i>{{ now()->format('l, d M Y') }}</div>
            </div>
        </div>
        <div class="hero-session-pill">
            <i class="fa fa-graduation-cap"></i> {{ $className }} &bull; {{ $sessionName }}
        </div>
    </div>

    {{-- Dual Radar Glance --}}
    <div class="hero-dual-grid">
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-check-circle" style="color: {{ $todayStatusColor }};"></i> Attendance Status
            </span>
            <div class="hero-radar-val" style="color: {{ $todayStatusColor }};">
                {{ $todayStatusLabel }}
            </div>
            <span class="hero-radar-meta">
                {{ $monthPresentCount }}/{{ $monthTotalDays }} Days Present ({{ $monthAttendancePercent }}%)
            </span>
        </div>
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-inr text-warning"></i> Fee Status
            </span>
            <div class="hero-radar-val">
                ₹ {{ number_format($pendingFees, 0) }}
            </div>
            <span class="hero-radar-meta">
                Paid: ₹ {{ number_format($collectedFees, 0) }} ({{ $feePercent }}%)
            </span>
        </div>
    </div>

    {{-- Fast Action Buttons in Hero --}}
    <div class="hero-actions-bar">
        <a href="{{ url('AttendanceView_student') }}" class="hero-action-btn act-btn-admission">
            <i class="fa fa-calendar-check-o"></i> Attendance
        </a>
        <a href="{{ url('fees_history') }}" class="hero-action-btn act-btn-fee">
            <i class="fa fa-inr"></i> Pay Fees
        </a>
        <a href="{{ url('student/result-card') }}" class="hero-action-btn act-btn-expense">
            <i class="fa fa-file-text-o"></i> Result
        </a>
        <a href="{{ url('student-complaints') }}" class="hero-action-btn act-btn-help">
            <i class="fa fa-comments-o"></i> Helpdesk
        </a>
    </div>
</div>

{{-- 2. 8-Tile Compact App Launcher Grid --}}
<div class="native-launcher-grid">
    {{-- 1. Attendance --}}
    <a href="{{ url('AttendanceView_student') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-1">
            <i class="fa fa-calendar-check-o"></i>
        </div>
        <span class="launcher-tile-title">Attendance</span>
    </a>

    {{-- 2. Fees --}}
    <a href="{{ url('fees_history') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-2">
            <i class="fa fa-inr"></i>
        </div>
        <span class="launcher-tile-title">Fees History</span>
    </a>

    {{-- 3. Exam Result --}}
    <a href="{{ url('student/result-card') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-6">
            <i class="fa fa-file-text-o"></i>
        </div>
        <span class="launcher-tile-title">Exam Result</span>
    </a>

    {{-- 4. Homework --}}
    <a href="{{ url('student_homework') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-4">
            <i class="fa fa-book"></i>
        </div>
        @if($activeHomeworkCount > 0)
            <span class="launcher-badge">{{ $activeHomeworkCount }}</span>
        @endif
        <span class="launcher-tile-title">Homework</span>
    </a>

    {{-- 5. Notices --}}
    <a href="{{ url('student-notices') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-3">
            <i class="fa fa-bullhorn"></i>
        </div>
        @if($notificationCount > 0)
            <span class="launcher-badge">{{ $notificationCount > 9 ? '9+' : $notificationCount }}</span>
        @endif
        <span class="launcher-tile-title">Notices</span>
    </a>

    {{-- 6. Timetable --}}
    <a href="{{ url('timetable') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-7">
            <i class="fa fa-calendar"></i>
        </div>
        <span class="launcher-tile-title">Time Table</span>
    </a>

    {{-- 7. Downloads --}}
    <a href="{{ url('student_download_center') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-5">
            <i class="fa fa-download"></i>
        </div>
        <span class="launcher-tile-title">Downloads</span>
    </a>

    {{-- 8. Complaints --}}
    <a href="{{ url('student-complaints') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-8">
            <i class="fa fa-comments-o"></i>
        </div>
        <span class="launcher-tile-title">Helpdesk</span>
    </a>
</div>

{{-- 3. Dual Symmetrical Radar Shortcuts Row --}}
<div class="native-radar-row">
    <a href="{{ url('applyLeaveStudent') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Leave Request</span>
            <div class="radar-icon-dot" style="background:#fee2e2;color:#dc2626;"><i class="fa fa-calendar-minus-o"></i></div>
        </div>
        <div class="radar-box-number" style="font-size:15px;color:#dc2626;">Apply Leave</div>
        <span class="radar-box-sub">Submit student leave application</span>
    </a>

    <a href="{{ url('my_id_card') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Student ID</span>
            <div class="radar-icon-dot" style="background:#e0f2fe;color:#0284c7;"><i class="fa fa-id-card-o"></i></div>
        </div>
        <div class="radar-box-number" style="font-size:15px;color:#0284c7;"># {{ $admissionNo }}</div>
        <span class="radar-box-sub">Digital ID Card & QR</span>
    </a>
</div>

{{-- 4. Segment Navigation Pills --}}
<div class="native-segments-bar">
    <button type="button" class="native-seg-btn active" data-target="feed-overview">Overview</button>
    <button type="button" class="native-seg-btn" data-target="feed-attendance">Attendance</button>
    <button type="button" class="native-seg-btn" data-target="feed-fees">Fees</button>
    <button type="button" class="native-seg-btn" data-target="feed-notices">Notices</button>
</div>

{{-- 5. FEED CONTENT: TAB 1 (OVERVIEW) --}}
<div class="native-feed-view active-feed" id="feed-overview">

    {{-- Attendance Summary Card --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-calendar-check-o text-primary"></i> Monthly Attendance ({{ now()->format('F') }})
            </h3>
            <a href="{{ url('AttendanceView_student') }}" class="sharp-card-link">View Full &rarr;</a>
        </div>
        <div class="matrix-pills-row">
            <div class="matrix-status-cell cell-present">
                <div class="matrix-status-tag">Present</div>
                <div class="matrix-status-val">{{ $monthPresentCount }}</div>
            </div>
            <div class="matrix-status-cell cell-absent">
                <div class="matrix-status-tag">Absent</div>
                <div class="matrix-status-val">{{ $monthAbsentCount }}</div>
            </div>
            <div class="matrix-status-cell cell-leave">
                <div class="matrix-status-tag">Leave</div>
                <div class="matrix-status-val">{{ $monthLeaveCount }}</div>
            </div>
        </div>
    </div>

    {{-- Fees Summary Card --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-inr text-success"></i> Fees Overview
            </h3>
            <a href="{{ url('fees_history') }}" class="sharp-card-link">Fees Ledger &rarr;</a>
        </div>
        <div class="fin-metrics-grid">
            <div class="fin-metric-box box-income">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Paid Amount</span>
                    <span class="fin-metric-icon"><i class="fa fa-check"></i></span>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($collectedFees, 0) }}</div>
                <span class="fin-metric-sub">Cleared Fees</span>
            </div>
            <div class="fin-metric-box box-expense">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Due Balance</span>
                    <span class="fin-metric-icon"><i class="fa fa-clock-o"></i></span>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($pendingFees, 0) }}</div>
                <span class="fin-metric-sub">Pending Amount</span>
            </div>
        </div>
        <div class="fin-progress-bar-wrap">
            <div class="fin-progress-fill" style="width: {{ min(100, $feePercent) }}%;"></div>
        </div>
        <div class="fin-summary-strip">
            <span>Total Assigned: <strong>₹ {{ number_format($assignedFees, 0) }}</strong></span>
            <span class="badge" style="background:#e0f2fe;color:#0369a1;">{{ $feePercent }}% Paid</span>
        </div>
    </div>

    {{-- Recent Notices Card --}}
    @if($notices->count() > 0)
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-bullhorn text-warning"></i> Latest Notices
            </h3>
            <a href="{{ url('student-notices') }}" class="sharp-card-link">All Notices &rarr;</a>
        </div>
        @foreach($notices as $notice)
            @php
                $noticeTitle = $notice->title ?? $notice->name ?? 'School Notice';
                $noticeDate = !empty($notice->date) ? \Carbon\Carbon::parse($notice->date)->format('d M') : (!empty($notice->created_at) ? \Carbon\Carbon::parse($notice->created_at)->format('d M') : date('d M'));
            @endphp
            <a href="{{ url('student-notices') }}" class="native-list-item">
                <div class="item-circle-badge" style="background:#fef3c7;color:#d97706;">
                    <i class="fa fa-bell-o"></i>
                </div>
                <div class="item-info-col">
                    <div class="item-primary-txt">{{ $noticeTitle }}</div>
                    <div class="item-secondary-txt"><i class="fa fa-clock-o mr-1"></i>{{ $noticeDate }}</div>
                </div>
                <i class="fa fa-chevron-right text-muted" style="font-size:10px;"></i>
            </a>
        @endforeach
    </div>
    @endif

    {{-- Student Profile Quick Details Card --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-user text-primary"></i> Student Profile
            </h3>
            <a href="{{ url('profileStudent') }}" class="sharp-card-link">Edit Profile &rarr;</a>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:11px;">
            <div style="background:#f8fafc;padding:6px 8px;border-radius:4px;border:1px solid #e2e8f0;">
                <span style="color:#64748b;font-size:9.5px;display:block;">Admission No</span>
                <strong style="color:#002C54;">{{ $admissionNo }}</strong>
            </div>
            <div style="background:#f8fafc;padding:6px 8px;border-radius:4px;border:1px solid #e2e8f0;">
                <span style="color:#64748b;font-size:9.5px;display:block;">Class & Section</span>
                <strong style="color:#002C54;">{{ $className }}</strong>
            </div>
            <div style="background:#f8fafc;padding:6px 8px;border-radius:4px;border:1px solid #e2e8f0;">
                <span style="color:#64748b;font-size:9.5px;display:block;">Father's Name</span>
                <strong style="color:#002C54;">{{ $dashboardStudent->father_name ?? '-' }}</strong>
            </div>
            <div style="background:#f8fafc;padding:6px 8px;border-radius:4px;border:1px solid #e2e8f0;">
                <span style="color:#64748b;font-size:9.5px;display:block;">Mobile</span>
                <strong style="color:#002C54;">{{ $dashboardStudent->mobile ?? '-' }}</strong>
            </div>
        </div>
    </div>
</div>

{{-- 5. FEED CONTENT: TAB 2 (ATTENDANCE) --}}
<div class="native-feed-view" id="feed-attendance">
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-calendar-check-o text-primary"></i> Attendance Status Matrix
            </h3>
            <span class="badge" style="background:#dcfce7;color:#15803d;padding:3px 8px;">Month: {{ now()->format('F Y') }}</span>
        </div>
        <div class="matrix-pills-row" style="grid-template-columns:repeat(3, 1fr);margin-bottom:8px;">
            <div class="matrix-status-cell cell-present">
                <div class="matrix-status-tag">Present</div>
                <div class="matrix-status-val">{{ $monthPresentCount }}</div>
            </div>
            <div class="matrix-status-cell cell-absent">
                <div class="matrix-status-tag">Absent</div>
                <div class="matrix-status-val">{{ $monthAbsentCount }}</div>
            </div>
            <div class="matrix-status-cell cell-leave">
                <div class="matrix-status-tag">Leave</div>
                <div class="matrix-status-val">{{ $monthLeaveCount }}</div>
            </div>
            <div class="matrix-status-cell cell-half">
                <div class="matrix-status-tag">Half Day</div>
                <div class="matrix-status-val">{{ $monthHalfDayCount }}</div>
            </div>
            <div class="matrix-status-cell cell-holiday">
                <div class="matrix-status-tag">Holiday</div>
                <div class="matrix-status-val">{{ $monthHolidayCount }}</div>
            </div>
            <div class="matrix-status-cell" style="background:#f1f5f9;border-color:#cbd5e1;">
                <div class="matrix-status-tag">Total Days</div>
                <div class="matrix-status-val">{{ $monthTotalDays }}</div>
            </div>
        </div>
        <a href="{{ url('AttendanceView_student') }}" class="btn btn-primary" style="width:100%;gap:6px;font-size:11.5px;">
            <i class="fa fa-calendar"></i> Open Complete Attendance Calendar
        </a>
    </div>
</div>

{{-- 5. FEED CONTENT: TAB 3 (FEES) --}}
<div class="native-feed-view" id="feed-fees">
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-inr text-success"></i> Fees & Ledger Summary
            </h3>
            <span class="badge" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">Session {{ $sessionName }}</span>
        </div>
        <div class="fin-metrics-grid">
            <div class="fin-metric-box box-income">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Paid Fees</span>
                    <span class="fin-metric-icon"><i class="fa fa-check"></i></span>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($collectedFees, 0) }}</div>
                <span class="fin-metric-sub">Receipts Cleared</span>
            </div>
            <div class="fin-metric-box box-expense">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Due Amount</span>
                    <span class="fin-metric-icon"><i class="fa fa-exclamation"></i></span>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($pendingFees, 0) }}</div>
                <span class="fin-metric-sub">Balance Pending</span>
            </div>
        </div>
        <div class="fin-progress-bar-wrap">
            <div class="fin-progress-fill" style="width: {{ min(100, $feePercent) }}%;"></div>
        </div>
        <div class="fin-summary-strip" style="margin-bottom:10px;">
            <span>Total Fees Assigned: <strong>₹ {{ number_format($assignedFees, 0) }}</strong></span>
            <span style="color:#0284c7;font-weight:700;">{{ $feePercent }}% Completed</span>
        </div>
        <a href="{{ url('fees_history') }}" class="btn btn-primary" style="width:100%;gap:6px;font-size:11.5px;">
            <i class="fa fa-receipt"></i> View Detailed Fees Receipts & Payments
        </a>
    </div>
</div>

{{-- 5. FEED CONTENT: TAB 4 (NOTICES) --}}
<div class="native-feed-view" id="feed-notices">
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-bullhorn text-warning"></i> School Notices & Circulars
            </h3>
            <a href="{{ url('student-notices') }}" class="sharp-card-link">Refresh &rarr;</a>
        </div>
        @forelse($notices as $notice)
            @php
                $noticeTitle = $notice->title ?? $notice->name ?? 'Notice Announcement';
                $noticeDate = !empty($notice->date) ? \Carbon\Carbon::parse($notice->date)->format('d M Y') : (!empty($notice->created_at) ? \Carbon\Carbon::parse($notice->created_at)->format('d M Y') : date('d M Y'));
                $noticeDesc = strip_tags($notice->description ?? $notice->message ?? '');
            @endphp
            <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:2px;">
                    <strong style="font-size:12px;color:#002C54;">{{ $noticeTitle }}</strong>
                    <span style="font-size:9px;color:#64748b;white-space:nowrap;"><i class="fa fa-clock-o mr-1"></i>{{ $noticeDate }}</span>
                </div>
                @if(!empty($noticeDesc))
                    <p style="font-size:11px;color:#475569;margin:0;line-height:1.4;">{{ \Illuminate\Support\Str::limit($noticeDesc, 120) }}</p>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:20px;color:#94a3b8;">
                <i class="fa fa-bell-slash-o" style="font-size:24px;margin-bottom:6px;display:block;"></i>
                <span>No announcements posted yet</span>
            </div>
        @endforelse
        <div style="margin-top:10px;">
            <a href="{{ url('student-notices') }}" class="btn btn-secondary" style="width:100%;gap:6px;">
                <i class="fa fa-envelope-o"></i> View Notice Board
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Segment Feed Switching
    const segButtons = document.querySelectorAll(".native-seg-btn");
    const feedViews = document.querySelectorAll(".native-feed-view");

    segButtons.forEach(btn => {
        btn.addEventListener("click", function() {
            segButtons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const targetId = this.getAttribute("data-target");
            feedViews.forEach(view => {
                if (view.id === targetId) {
                    view.classList.add("active-feed");
                } else {
                    view.classList.remove("active-feed");
                }
            });
        });
    });
});
</script>
@endsection

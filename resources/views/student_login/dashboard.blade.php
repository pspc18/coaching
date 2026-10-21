@php
    $getSetting = Helper::getSetting();
    $dashboardStudent = $getUser ?? Helper::getUser();
    $studentId = (int) Session::get('id');
    $branchId = (int) Session::get('branch_id');
    $sessionId = (int) Session::get('session_id');

    $studentName = trim(($dashboardStudent->first_name ?? '').' '.($dashboardStudent->last_name ?? ''));
    $rawFirstName = trim((string) ($dashboardStudent->first_name ?? 'Student'));
    $firstNameWords = preg_split('/\s+/', $rawFirstName);
    $firstName = !empty($firstNameWords[0]) ? $firstNameWords[0] : 'Student';
    $className = optional($dashboardStudent->ClassTypes)->name ?? '-';
    $admissionNo = $dashboardStudent->admissionNo ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');

    $hour = (int) date('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $dashboardAvatar = !empty($dashboardStudent->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$dashboardStudent->image
        : null;

    // 1. Attendance Data (Single Optimized Query)
    $today = now()->toDateString();
    $studentUniqueIds = array_values(array_filter([
        trim((string) ($dashboardStudent->attendance_unique_id ?? '')),
        trim((string) ($dashboardStudent->admissionNo ?? '')),
        'STU-' . $studentId,
    ]));

    $todayAttendanceMark = null;
    $monthMarksCollection = collect();

    if (!empty($studentUniqueIds)) {
        $monthRows = \App\Models\AttendanceMark::query()
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('entity_type', 'student')
            ->whereIn('unique_id', $studentUniqueIds)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->select('date', 'status', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $todayRow = $monthRows->first(function($row) use ($today) {
            return !empty($row->date) && \Carbon\Carbon::parse($row->date)->toDateString() === $today;
        });
        if ($todayRow) {
            $todayAttendanceMark = $todayRow->status;
        }

        $monthMarksCollection = $monthRows->map(fn($m) => strtolower(trim((string) $m->status)));
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

    $monthPresentCount = $monthMarksCollection->filter(fn($s) => in_array($s, ['present', 'in', 'out', 'early_out', 'earlyout']))->count();
    $monthAbsentCount = $monthMarksCollection->filter(fn($s) => $s === 'absent')->count();
    $monthLeaveCount = $monthMarksCollection->filter(fn($s) => $s === 'leave')->count();
    $monthHalfDayCount = $monthMarksCollection->filter(fn($s) => $s === 'halfday')->count();
    $monthHolidayCount = $monthMarksCollection->filter(fn($s) => $s === 'holiday')->count();
    $monthTotalDays = $monthMarksCollection->count();
    $monthAttendancePercent = $monthTotalDays > 0 ? round(($monthPresentCount / $monthTotalDays) * 100) : 100;

    // 2. Fees Data (Synced with Fees History via unified Helper)
    $feeSummary = Helper::getStudentFeeSummary($studentId, $sessionId, $branchId);
    $assignedFees = (float) $feeSummary['totalFees'];
    $collectedFees = (float) $feeSummary['paidFees'];
    $pendingFees = (float) $feeSummary['currentSessionDue'];
    $totalDiscount = (float) $feeSummary['discount'];
    $netPayable = (float) $feeSummary['netPayable'];
    $feePercent = (int) $feeSummary['paymentProgress'];

    // 3. Notices & Circulars (Personalized to student matching student-notices, with Upcoming & Active prioritization)
    $todayDate = \Carbon\Carbon::today()->toDateString();
    $studentNoticeCounts = Helper::getStudentNoticeCounts($studentId, $branchId, $sessionId);
    $noticesTotalCount = (int) ($studentNoticeCounts->total_count ?? 0);
    $noticesUpcomingCount = (int) ($studentNoticeCounts->upcoming_count ?? 0);
    $noticesActiveCount = (int) ($studentNoticeCounts->active_count ?? 0);
    $notices = Helper::getStudentNotices($studentId, $branchId, $sessionId, 5);

    // 4. Notifications / Alerts Count (From layout without re-querying DB)
    $alertCount = $unreadAlerts ?? ($notificationCount ?? 0);

    // 5. Active Homework Count
    $activeHomeworkCount = \App\Models\Master\Homework::where('branch_id', $branchId)
        ->where('session_id', $sessionId)
        ->where('class_type_id', $dashboardStudent->class_type_id ?? 0)
        ->count();
@endphp

@extends('student_login.layout.app')

@section('title', 'Student Dashboard')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT MOBILE DASHBOARD (EXACT ADMIN MOBILE TYPOGRAPHY & LAYOUT)
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
    margin-bottom: 10px;
}
.hero-user-cluster {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
    min-width: 0;
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
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hero-user-sub {
    font-size: 9.5px;
    color: #93c5fd;
    font-weight: 600;
    letter-spacing: .02em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 3px;
}
.hero-user-sub i {
    font-size: 10.5px;
    color: #38bdf8;
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
    margin-bottom: 0;
}
.hero-radar-col {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    gap: 5px;
}
.hero-radar-col:first-child {
    border-right: 1px solid rgba(255, 255, 255, 0.12);
    padding-right: 6px;
}
.hero-radar-col:last-child {
    padding-left: 2px;
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
    line-height: 1.35;
}
.hero-radar-val {
    font-size: 17px;
    font-weight: 800;
    color: #38bdf8;
    line-height: 1.3;
    margin: 0;
}
.hero-radar-val.status-text-val {
    font-size: 13.5px;
    letter-spacing: -0.01em;
    line-height: 1.3;
}
.hero-radar-meta {
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 500;
    line-height: 1.35;
}

/* Pay Fees Button inside Due Fees Box */
.hero-due-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin: 0;
}
.hero-due-row .hero-radar-val {
    margin: 0;
    line-height: 1.3;
}
.hero-due-pay-btn {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff !important;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    line-height: 1.2;
    white-space: nowrap;
    flex-shrink: 0;
    box-shadow: 0 1px 6px rgba(16, 185, 129, 0.35);
    animation: payBtnPulse 2.2s infinite ease-in-out;
    transition: transform .12s ease;
}
.hero-due-pay-btn:active {
    transform: scale(0.95);
}
.hero-due-pay-btn::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -60%;
    width: 40%;
    height: 200%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.45), transparent);
    transform: rotate(25deg);
    animation: payShimmerSweep 2.8s infinite ease-in-out;
}

@keyframes payBtnPulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.65), 0 2px 6px rgba(0, 0, 0, 0.25);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15), 0 3px 12px rgba(16, 185, 129, 0.5);
        transform: scale(1.02);
    }
}

@keyframes payShimmerSweep {
    0% {
        left: -60%;
    }
    35%, 100% {
        left: 140%;
    }
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
    top: 3px;
    right: 3px;
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
.circle-grad-3 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.circle-grad-4 { background: linear-gradient(135deg, #f43f5e, #e11d48); }
.circle-grad-5 { background: linear-gradient(135deg, #f59e0b, #d97706); }
.circle-grad-6 { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.circle-grad-7 { background: linear-gradient(135deg, #06b6d4, #0891b2); }
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
    font-size: 12px !important;
    font-weight: 800 !important;
    color: #002C54 !important;
    margin: 0 !important;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.25 !important;
}
.sharp-card-link {
    font-size: 10.5px !important;
    font-weight: 700 !important;
    color: #0284c7 !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 3px;
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
    background: #16a34a;
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
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 9. Student Profile Details Grid */
.profile-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.profile-info-tile {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
}
.profile-tile-tag {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: #64748b;
    margin-bottom: 2px;
}
.profile-tile-val {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
                <div class="hero-user-sub"><i class="fa fa-calendar-o"></i> <span>{{ now()->format('l, d M Y') }}</span></div>
            </div>
        </div>
    </div>

    {{-- Hero Dual Live Radar --}}
    <div class="hero-dual-grid">
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-circle" style="color: {{ $todayStatusColor }}; font-size: 8px;"></i> Today's Attendance
            </span>
            <div class="hero-radar-val {{ strlen($todayStatusLabel) > 8 ? 'status-text-val' : '' }}" style="color: {{ $todayStatusColor }};">
                {{ $todayStatusLabel }}
            </div>
            <span class="hero-radar-meta">{{ $monthAttendancePercent }}% Month Attendance</span>
        </div>
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-inr text-warning"></i> Due Fees
            </span>
            <div class="hero-due-row">
                <div class="hero-radar-val" style="color: {{ $pendingFees > 0 ? '#f43f5e' : '#10b981' }};">
                    ₹ {{ number_format($pendingFees, 0) }}
                </div>
                @if($pendingFees > 0)
                    <a href="javascript:void(0);" onclick="openPhonePeModal({{ $pendingFees }})" class="hero-due-pay-btn">
                        Pay Now
                    </a>
                @endif
            </div>
            <span class="hero-radar-meta">{{ $feePercent }}% Paid &bull; ₹ {{ number_format($collectedFees, 0) }}</span>
        </div>
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
        <span class="launcher-tile-title">Fees Ledger</span>
    </a>

    {{-- 3. Results --}}
    <a href="{{ url('student/result-card') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-3">
            <i class="fa fa-file-text-o"></i>
        </div>
        <span class="launcher-tile-title">Results</span>
    </a>

    {{-- 4. Homework --}}
    <a href="{{ url('student_homework') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-4">
            <i class="fa fa-book"></i>
        </div>
        <span class="launcher-tile-title">Homework</span>
        @if($activeHomeworkCount > 0)
            <span class="launcher-badge">{{ $activeHomeworkCount }}</span>
        @endif
    </a>

    {{-- 5. Notices --}}
    <a href="{{ url('student-notices') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-5">
            <i class="fa fa-bullhorn"></i>
        </div>
        <span class="launcher-tile-title">Notices</span>
        @if($notices->count() > 0)
            <span class="launcher-badge">{{ $notices->count() }}</span>
        @endif
    </a>

    {{-- 6. Downloads --}}
    <a href="{{ url('student_download_center') }}" class="native-launcher-tile">
        <div class="launcher-icon-circle circle-grad-7">
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
    <a href="{{ url('AttendanceView_student') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Attendance</span>
            <div class="radar-icon-dot" style="background:#dcfce7;color:#16a34a;"><i class="fa fa-calendar-check-o"></i></div>
        </div>
        <div class="radar-box-number" style="color:#16a34a;">{{ $monthPresentCount }} <span style="font-size:11.5px;font-weight:700;color:#94a3b8;">Days</span></div>
        <div class="radar-box-sub">Attendance Overview</div>
    </a>

    <a href="{{ url('my_id_card') }}" class="native-radar-box">
        <div class="radar-box-header">
            <span class="radar-box-tag">Student ID</span>
            <div class="radar-icon-dot" style="background:#e0f2fe;color:#0284c7;"><i class="fa fa-id-card-o"></i></div>
        </div>
        <div class="radar-box-number" style="color:#0284c7;">#{{ $admissionNo }}</div>
        <div class="radar-box-sub">Digital ID Card &bull; QR</div>
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
    <button type="button" class="native-seg-btn" data-target="feed-fees" onclick="switchNativeTab('feed-fees', this)">
        <i class="fa fa-inr mr-1"></i> Fees
    </button>
    <button type="button" class="native-seg-btn" data-target="feed-notices" onclick="switchNativeTab('feed-notices', this)">
        <i class="fa fa-bullhorn mr-1"></i> Notices ({{ $noticesTotalCount }})
    </button>
</div>

{{-- Segment 1: ATTENDANCE BREAKDOWN & STUDENT PROFILE --}}
<div class="native-feed-view active-feed" id="feed-attendance" style="display: block;">
    {{-- Attendance Summary Card --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-calendar-check-o text-primary"></i> Monthly Attendance ({{ now()->format('F') }})
            </h3>
            <a href="{{ url('AttendanceView_student') }}" class="sharp-card-link">
                View Calendar <i class="fa fa-angle-right"></i>
            </a>
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
    </div>

    {{-- Student Profile Quick Details Card --}}
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-user text-primary"></i> Student Information
            </h3>
            <a href="{{ url('profileStudent') }}" class="sharp-card-link">
                Profile <i class="fa fa-angle-right"></i>
            </a>
        </div>
        <div class="profile-info-grid">
            <div class="profile-info-tile">
                <span class="profile-tile-tag">Admission No</span>
                <span class="profile-tile-val">{{ $admissionNo }}</span>
            </div>
            <div class="profile-info-tile">
                <span class="profile-tile-tag">Class &amp; Section</span>
                <span class="profile-tile-val">{{ $className }}</span>
            </div>
            <div class="profile-info-tile">
                <span class="profile-tile-tag">Father's Name</span>
                <span class="profile-tile-val">{{ $dashboardStudent->father_name ?? '-' }}</span>
            </div>
            <div class="profile-info-tile">
                <span class="profile-tile-tag">Mobile Number</span>
                <span class="profile-tile-val">{{ $dashboardStudent->mobile ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Segment 2: FEES BREAKDOWN & PAYMENT SUMMARY --}}
<div class="native-feed-view active-feed" id="feed-fees" style="display: block;">
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-inr text-success"></i> Fees &amp; Ledger Overview
            </h3>
            <a href="{{ url('fees_history') }}" class="sharp-card-link">
                Fees Ledger <i class="fa fa-angle-right"></i>
            </a>
        </div>
        <div class="fin-metrics-grid">
            <div class="fin-metric-box box-income">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Paid Amount</span>
                    <div class="fin-metric-icon"><i class="fa fa-check"></i></div>
                </div>
                <div class="fin-metric-value">₹ {{ number_format($collectedFees, 0) }}</div>
                <span class="fin-metric-sub">{{ $feePercent }}% Cleared</span>
            </div>
            <div class="fin-metric-box box-expense">
                <div class="fin-metric-header">
                    <span class="fin-metric-title">Due Balance</span>
                    <div class="fin-metric-icon"><i class="fa fa-clock-o"></i></div>
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
            @if($totalDiscount > 0)
                <span class="text-muted">&bull; Disc: <strong>₹ {{ number_format($totalDiscount, 0) }}</strong></span>
            @endif
            <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:10px;padding:3px 6px;">{{ $feePercent }}% Paid</span>
        </div>
    </div>
</div>

{{-- Segment 3: NOTICES & ANNOUNCEMENTS --}}
<div class="native-feed-view active-feed" id="feed-notices" style="display: block;">
    <div class="sharp-section-card">
        <div class="sharp-card-header">
            <h3 class="sharp-card-title">
                <i class="fa fa-bullhorn text-warning"></i> Latest Notices &amp; Circulars
                @if($noticesUpcomingCount > 0)
                    <span class="badge" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; font-size:9.5px; padding:2px 6px; border-radius:3px; margin-left:4px; font-weight:800;">
                        <i class="fa fa-clock-o mr-1"></i>{{ $noticesUpcomingCount }} Upcoming
                    </span>
                @elseif($noticesActiveCount > 0)
                    <span class="badge" style="background:#ecfdf5; color:#15803d; border:1px solid #a7f3d0; font-size:9.5px; padding:2px 6px; border-radius:3px; margin-left:4px; font-weight:800;">
                        <i class="fa fa-check-circle mr-1"></i>{{ $noticesActiveCount }} Active
                    </span>
                @endif
            </h3>
            <a href="{{ url('student-notices') }}" class="sharp-card-link">
                All Notices ({{ $noticesTotalCount }}) <i class="fa fa-angle-right"></i>
            </a>
        </div>
        @forelse($notices as $notice)
            @php
                $noticeTitle = $notice->title ?? $notice->name ?? 'Notice Announcement';
                $fromDate = !empty($notice->from_date) ? \Carbon\Carbon::parse($notice->from_date) : null;
                $toDate = !empty($notice->to_date) ? \Carbon\Carbon::parse($notice->to_date) : null;
                $isUpcoming = $fromDate && $fromDate->toDateString() > $todayDate;
                $isActive = $fromDate && $toDate && $fromDate->toDateString() <= $todayDate && $toDate->toDateString() >= $todayDate;
                $noticeDesc = trim(strip_tags($notice->description ?? $notice->message ?? ''));
                $hasAttachment = !empty($notice->attachment_path);

                if ($isUpcoming) {
                    $badgeStyle = 'background:#fffbeb; color:#d97706; border:1px solid #fde68a;';
                    $iconClass = 'fa-clock-o';
                    $statusTag = 'Upcoming';
                    $statusTagStyle = 'background:#fef3c7; color:#b45309; border:1px solid #fcd34d;';
                    $noticeUrl = url('student-notices?filter=upcoming#noticeCard-'.$notice->id);
                } elseif ($isActive) {
                    $badgeStyle = 'background:#ecfdf5; color:#16a34a; border:1px solid #a7f3d0;';
                    $iconClass = 'fa-bullhorn';
                    $statusTag = 'Active';
                    $statusTagStyle = 'background:#dcfce7; color:#15803d; border:1px solid #86efac;';
                    $noticeUrl = url('student-notices?filter=active#noticeCard-'.$notice->id);
                } else {
                    $badgeStyle = 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;';
                    $iconClass = 'fa-file-text-o';
                    $statusTag = 'Notice';
                    $statusTagStyle = 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;';
                    $noticeUrl = url('student-notices#noticeCard-'.$notice->id);
                }
            @endphp
            <a href="{{ $noticeUrl }}" class="native-list-item" title="View Notice Details">
                <div class="item-circle-badge" style="{{ $badgeStyle }}">
                    <i class="fa {{ $iconClass }}"></i>
                </div>
                <div class="item-info-col">
                    <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                        <div class="item-primary-txt">{{ $noticeTitle }}</div>
                        <span class="badge" style="{{ $statusTagStyle }} font-size:8.5px; padding:1.5px 5px; border-radius:3px; font-weight:800; white-space:nowrap; flex-shrink:0;">
                            {{ $statusTag }}
                        </span>
                    </div>
                    <div class="item-secondary-txt">
                        @if($isUpcoming && $fromDate)
                            <span class="mr-2 text-warning font-weight-bold"><i class="fa fa-clock-o mr-1"></i>From {{ $fromDate->format('d M') }}</span>
                        @elseif($isActive && $toDate)
                            <span class="mr-2 text-success font-weight-bold"><i class="fa fa-calendar-check-o mr-1"></i>Till {{ $toDate->format('d M') }}</span>
                        @elseif($fromDate)
                            <span class="mr-2"><i class="fa fa-calendar-o mr-1"></i>{{ $fromDate->format('d M') }}</span>
                        @endif
                        @if($hasAttachment)
                            <span class="mr-2 text-danger font-weight-bold"><i class="fa fa-file-pdf-o"></i> PDF</span>
                        @endif
                        @if(!empty($noticeDesc))
                            &bull; {{ \Illuminate\Support\Str::limit($noticeDesc, 55) }}
                        @endif
                    </div>
                </div>
                <i class="fa fa-angle-right text-muted" style="font-size:11px;"></i>
            </a>
        @empty
            <div class="text-center py-3 text-muted" style="font-size: 11px;">
                <i class="fa fa-bell-slash-o fa-2x text-muted mb-1 d-block" style="opacity:0.5;"></i>
                No circular announcements found for your account yet.
            </div>
        @endforelse
    </div>
</div>

<script>
// Universal Native Tab Switcher matching Admin Mobile View
function switchNativeTab(targetId, btnElement) {
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

    if (targetId === 'feed-all') {
        for (var j = 0; j < allViews.length; j++) {
            allViews[j].style.display = 'block';
            allViews[j].classList.add('active-feed');
        }
    } else {
        for (var k = 0; k < allViews.length; k++) {
            allViews[k].style.display = 'none';
            allViews[k].classList.remove('active-feed');
        }
        var activeView = document.getElementById(targetId);
        if (activeView) {
            activeView.style.display = 'block';
            activeView.classList.add('active-feed');
        }
    }
}
</script>

@include('student_login.payment.modal')
@endsection

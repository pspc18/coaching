@extends('layout.app')

@php
    $attendanceType = $setting->attendance_type ?? 2;
    $typeLabel = $attendanceType == 1 ? 'Biometric' : ($attendanceType == 3 ? 'QR Code' : 'Normal / Manual');
    $isStaffTab = ($activeTab ?? 'students') === 'staff';
    $selectedTitleName = $selectedName ?? $selectedUniqueId;

    // Export URL
    $exportUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $month . '&year=' . $year . '&export=1');
    if ($isStaffTab) {
        $exportUrl .= '&staff=' . urlencode((string) $selectedUniqueId);
    } else {
        $exportUrl .= '&student=' . urlencode((string) $selectedUniqueId);
        if (!empty($classFilter)) {
            $exportUrl .= '&class_type_id=' . urlencode((string) $classFilter);
        }
    }

    // Prev / Next Month Calculations
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear = $month == 12 ? $year + 1 : $year;

    $prevUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $prevMonth . '&year=' . $prevYear . '&class_type_id=' . urlencode((string)$classFilter) . ($isStaffTab ? '&staff=' . urlencode((string)$selectedUniqueId) : '&student=' . urlencode((string)$selectedUniqueId)));
    $nextUrl = url('attendance/view?tab=' . ($activeTab ?? 'students') . '&month=' . $nextMonth . '&year=' . $nextYear . '&class_type_id=' . urlencode((string)$classFilter) . ($isStaffTab ? '&staff=' . urlencode((string)$selectedUniqueId) : '&student=' . urlencode((string)$selectedUniqueId)));
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ATTENDANCE VIEW (NEW THEME COMPACT GUIDELINES)
   - Dark Navy Hero: #002C54 to #0f3460
   - Sharp 2px border radius
   - High-contrast typography & compact inputs
   - Responsive & interactive monthly calendar & yearly heatmap
   ========================================================================== */

.att-view-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.att-view-page * {
    box-sizing: border-box;
}

/* 1. Top Hero Header (Arise Signature Navy Theme) */
.att-view-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 6px;
}
.att-view-hero-text {
    display: flex;
    flex-direction: column;
}
.att-view-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    font-weight: 600;
    color: #93c5fd;
}
.att-view-title {
    font-size: 15px;
    font-weight: 700;
    margin: 1px 0 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-view-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Right Actions */
.att-view-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-tag-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
    color: #ffffff;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    border-color: rgba(255,255,255,.25);
    color: #ffffff !important;
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.18);
    border-color: #ffffff;
}

/* 2. Navigation Tabs (Students vs Staff) */
.att-view-nav-tabs {
    display: flex;
    align-items: center;
    gap: 4px;
    background: #002C54;
    padding: 3px 6px;
    border-radius: 2px;
    margin-bottom: 6px;
    border-bottom: 2px solid #001f3d;
}
.att-tab-link {
    padding: 4px 12px;
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
.att-tab-link:hover {
    color: #ffffff;
    background: rgba(255,255,255,.1);
}
.att-tab-link.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #38bdf8;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}

/* 3. Filter Toolbar Card */
.att-filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.att-filter-form {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.form-group-compact {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}
.form-group-compact label {
    margin: 0;
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    white-space: nowrap;
}
.form-control-compact {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 8px;
    outline: none;
    transition: border-color .15s;
}
.form-control-compact:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0,44,84,.1);
}
.btn-att-load {
    height: 29px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    background: #002C54;
    border: 1px solid #002C54;
    color: #ffffff;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: background .15s;
}
.btn-att-load:hover {
    background: #001930;
    border-color: #001930;
}
.month-nav-btn {
    height: 29px;
    width: 29px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    border-radius: 2px;
    text-decoration: none !important;
    transition: all .15s;
}
.month-nav-btn:hover {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff !important;
}

/* 4. Metric Summary Cards Grid */
.att-summary-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 6px;
    margin-bottom: 6px;
}
.att-stat-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-left: 3px solid #cbd5e1;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.att-stat-card.is-total { border-left-color: #002C54; }
.att-stat-card.is-present { border-left-color: #16a34a; }
.att-stat-card.is-absent { border-left-color: #dc2626; }
.att-stat-card.is-halfday { border-left-color: #9333ea; }
.att-stat-card.is-percent { border-left-color: #0284c7; }

.att-stat-icon {
    width: 28px;
    height: 28px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.is-total .att-stat-icon { background: #e2e8f0; color: #002C54; }
.is-present .att-stat-icon { background: #dcfce7; color: #166534; }
.is-absent .att-stat-icon { background: #fee2e2; color: #991b1b; }
.is-halfday .att-stat-icon { background: #f3e8ff; color: #7e22ce; }
.is-percent .att-stat-icon { background: #e0f2fe; color: #0369a1; }

.att-stat-content {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
}
.att-stat-content strong {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
}
.att-stat-content small {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .02em;
}

/* 5. Calendar & Day Inspection Layout */
.att-main-layout {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 6px;
    margin-bottom: 6px;
}
@media (max-width: 991px) {
    .att-main-layout { grid-template-columns: 1fr; }
    .att-summary-grid { grid-template-columns: repeat(2, 1fr); }
}

/* Calendar Card */
.att-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    overflow: hidden;
}
.att-card-header {
    background: #002C54 !important;
    color: #ffffff !important;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    border-bottom: 1px solid #001f3d;
}
.att-card-title,
.att-card-header .att-card-title,
.att-card-header h3,
.att-card-header h3.att-card-title {
    color: #ffffff !important;
    font-size: 12.5px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.att-legend-pills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.legend-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #e2e8f0;
}
.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
    display: inline-block;
}

/* Calendar Grid */
.calendar-wrap {
    padding: 8px;
}
.calendar-weekdays-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 4px;
    text-align: center;
}
.weekday-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 3px 0;
    border-radius: 2px;
    text-transform: uppercase;
}
.weekday-title.is-weekend {
    color: #be123c;
    background: #fff1f2;
}

.calendar-days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}
.cal-day-cell {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    min-height: 52px;
    padding: 4px 6px;
    position: relative;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.cal-day-cell:hover {
    border-color: #0284c7;
    background: #f0f9ff;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,44,84,.1);
}
.cal-day-cell.active {
    border-color: #002C54 !important;
    background: #e0f2fe !important;
    box-shadow: inset 0 0 0 1px #002C54, 0 2px 4px rgba(0,44,84,.15) !important;
}
.cal-day-cell.empty {
    background: #f8fafc;
    border-style: dashed;
    border-color: #e2e8f0;
    cursor: default;
    pointer-events: none;
}
.cal-day-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cal-day-num {
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}
.cal-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
    display: inline-block;
}
.cal-day-bottom {
    font-size: 9px;
    color: #64748b;
    line-height: 1.1;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Status Colors */
.status-in, .status-present { background-color: #16a34a !important; color: #ffffff; }
.status-out { background-color: #0284c7 !important; color: #ffffff; }
.status-absent { background-color: #dc2626 !important; color: #ffffff; }
.status-leave { background-color: #d97706 !important; color: #ffffff; }
.status-halfday { background-color: #9333ea !important; color: #ffffff; }
.status-holiday { background-color: #64748b !important; color: #ffffff; }
.status-event { background-color: #0891b2 !important; color: #ffffff; }
.status-none { background-color: #cbd5e1 !important; }

/* Day Inspection Side Card */
.att-inspect-box {
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.inspect-date-banner {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 8px 10px;
    display: flex;
    flex-direction: column;
}
.inspect-date-banner small {
    font-size: 9.5px;
    text-transform: uppercase;
    color: #93c5fd;
    font-weight: 600;
}
.inspect-date-banner strong {
    font-size: 14px;
    font-weight: 700;
}
.inspect-item {
    display: flex;
    flex-direction: column;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.inspect-label {
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
}
.inspect-value {
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
}
.inspect-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 2px;
    width: fit-content;
}

/* 6. Yearly Heatmap Card */
.yearly-grid-compact {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
    padding: 8px;
}
@media (max-width: 1100px) {
    .yearly-grid-compact { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .yearly-grid-compact { grid-template-columns: repeat(2, 1fr); }
}
.month-tile-compact {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.month-tile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.month-tile-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
}
.month-dot-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2.5px;
    margin-bottom: 6px;
}
.month-dot-grid span {
    width: 6px;
    height: 6px;
    border-radius: 1px;
    background: #e2e8f0;
    display: inline-block;
}
.month-dot-grid span.empty {
    background: transparent;
}
.month-micro-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    border-top: 1px solid #e2e8f0;
    padding-top: 3px;
}
.micro-p { color: #16a34a; }
.micro-a { color: #dc2626; }
.micro-h { color: #9333ea; }
</style>
@endsection

@section('content')
<div class="content-wrapper att-view-page p-2">
    <div class="container-fluid p-0">

        {{-- 1. Top Hero Header (Signature Navy Banner) --}}
        <div class="att-view-hero">
            <div class="att-view-hero-text">
                <span class="att-view-kicker"><i class="fa fa-calendar-check-o mr-1"></i> Attendance Management</span>
                <h1 class="att-view-title">
                    Attendance History &amp; Calendar Overview
                </h1>
                <p class="att-view-subtitle">
                    Record for: <strong>{{ $selectedTitleName }}</strong> (ID: {{ $selectedUniqueId }}) &bull; Month: <strong>{{ $monthName }} {{ $year }}</strong>
                </p>
            </div>

            {{-- Right Badges & Action Buttons --}}
            <div class="att-view-hero-actions">
                <span class="hero-tag-badge">
                    <i class="fa {{ $isStaffTab ? 'fa-user' : 'fa-graduation-cap' }}"></i> {{ ucfirst($activeTab) }}
                </span>
                <span class="hero-tag-badge">
                    <i class="fa fa-sliders"></i> Mode: {{ $typeLabel }}
                </span>
                <a href="{{ $exportUrl }}" class="dash-btn dash-btn-outline" title="Export this month's attendance to CSV">
                    <i class="fa fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- 2. Navigation Tabs (Students vs Staff) --}}
        <div class="att-view-nav-tabs">
            <a class="att-tab-link {{ !$isStaffTab ? 'active' : '' }}" href="{{ url('attendance/view?tab=students&month='.$month.'&year='.$year) }}">
                <i class="fa fa-graduation-cap"></i> Students Attendance
            </a>
            @if($canAccessStaffAttendance ?? false)
                <a class="att-tab-link {{ $isStaffTab ? 'active' : '' }}" href="{{ url('attendance/view?tab=staff&month='.$month.'&year='.$year) }}">
                    <i class="fa fa-users"></i> Staff Attendance
                </a>
            @endif
        </div>

        {{-- 3. Filter Toolbar Card --}}
        <div class="att-filter-card">
            <form method="get" action="{{ url('attendance/view') }}" class="att-filter-form" id="attendanceFilterForm">
                <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">

                {{-- If Student Tab: Class Selector --}}
                @if(!$isStaffTab)
                    <div class="form-group-compact">
                        <label for="class_type_id"><i class="fa fa-building-o"></i> Class:</label>
                        <select name="class_type_id" id="class_type_id" class="form-control-compact select2" style="min-width: 140px;" onchange="document.getElementById('attendanceFilterForm').submit()">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) $classFilter === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Student Dropdown --}}
                    <div class="form-group-compact">
                        <label for="student_select"><i class="fa fa-user"></i> Student:</label>
                        <select name="student" id="student_select" class="form-control-compact select2" style="min-width: 220px;" onchange="document.getElementById('attendanceFilterForm').submit()">
                            @foreach($students as $stu)
                                @php $uid = $stu->attendance_unique_id ?? ('STU-' . $stu->id); @endphp
                                <option value="{{ $uid }}" {{ $selectedUniqueId === $uid ? 'selected' : '' }}>
                                    {{ trim($stu->first_name . ' ' . $stu->last_name) }} ({{ $uid }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    {{-- Staff Dropdown --}}
                    <div class="form-group-compact">
                        <label for="staff_select"><i class="fa fa-user"></i> Staff Member:</label>
                        <select name="staff" id="staff_select" class="form-control-compact select2" style="min-width: 240px;" onchange="document.getElementById('attendanceFilterForm').submit()">
                            @foreach($staff as $member)
                                @php $uid = $member->attendance_unique_id ?? ('USR-' . $member->id); @endphp
                                <option value="{{ $uid }}" {{ $selectedUniqueId === $uid ? 'selected' : '' }}>
                                    {{ trim($member->first_name . ' ' . $member->last_name) }} ({{ $uid }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Month Selector --}}
                <div class="form-group-compact">
                    <label for="month_select"><i class="fa fa-calendar"></i> Month:</label>
                    <select name="month" id="month_select" class="form-control-compact" style="min-width: 100px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Year Selector --}}
                <div class="form-group-compact">
                    <label for="year_select">Year:</label>
                    <select name="year" id="year_select" class="form-control-compact" style="min-width: 80px;">
                        @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Action & Nav Buttons --}}
                <button type="submit" class="btn-att-load">
                    <i class="fa fa-refresh"></i> Load
                </button>

                <div class="d-inline-flex align-items-center ml-auto" style="gap: 4px;">
                    <a href="{{ $prevUrl }}" class="month-nav-btn" title="Previous Month ({{ date('M', mktime(0,0,0,$prevMonth,1)) }})">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                    <span class="font-weight-bold px-1" style="font-size: 11px; color: #002C54;">{{ $monthName }} {{ $year }}</span>
                    <a href="{{ $nextUrl }}" class="month-nav-btn" title="Next Month ({{ date('M', mktime(0,0,0,$nextMonth,1)) }})">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 4. Summary Metric Cards Grid --}}
        <div class="att-summary-grid">
            {{-- Working Days --}}
            <div class="att-stat-card is-total">
                <div class="att-stat-icon"><i class="fa fa-calendar-check-o"></i></div>
                <div class="att-stat-content">
                    <strong>{{ $totalDays }}</strong>
                    <small>Working Days</small>
                </div>
            </div>

            {{-- Present Days --}}
            <div class="att-stat-card is-present">
                <div class="att-stat-icon"><i class="fa fa-check-circle"></i></div>
                <div class="att-stat-content">
                    <strong>{{ $inDays }} <span style="font-size: 11px; font-weight: normal; color: #64748b;">/ {{ $outDays }}</span></strong>
                    <small>In / Out Days</small>
                </div>
            </div>

            {{-- Absent Days --}}
            <div class="att-stat-card is-absent">
                <div class="att-stat-icon"><i class="fa fa-times-circle"></i></div>
                <div class="att-stat-content">
                    <strong>{{ $absentDays }}</strong>
                    <small>Days Absent</small>
                </div>
            </div>

            {{-- Half Days --}}
            <div class="att-stat-card is-halfday">
                <div class="att-stat-icon"><i class="fa fa-adjust"></i></div>
                <div class="att-stat-content">
                    <strong>{{ $halfDayDays }}</strong>
                    <small>Half Days</small>
                </div>
            </div>

            {{-- Attendance % --}}
            <div class="att-stat-card is-percent">
                <div class="att-stat-icon"><i class="fa fa-pie-chart"></i></div>
                <div class="att-stat-content">
                    <strong>{{ number_format($attendancePercent, 1) }}%</strong>
                    <small>Attendance Rate</small>
                </div>
            </div>
        </div>

        {{-- 5. Main Monthly Calendar & Day Inspection Layout --}}
        <div class="att-main-layout">

            {{-- Left: Monthly Calendar Card --}}
            <div class="att-card">
                <div class="att-card-header">
                    <h3 class="att-card-title" style="color: #ffffff !important;">
                        <i class="fa fa-calendar text-info"></i> <span style="color: #ffffff !important;">{{ $monthName }} {{ $year }} Calendar</span>
                    </h3>

                    {{-- Interactive Color Legend Pills --}}
                    <div class="att-legend-pills">
                        <span class="legend-pill"><span class="legend-dot status-in"></span> Present ({{ $inDays }})</span>
                        <span class="legend-pill"><span class="legend-dot status-out"></span> Out ({{ $outDays }})</span>
                        <span class="legend-pill"><span class="legend-dot status-halfday"></span> Half Day ({{ $halfDayDays }})</span>
                        <span class="legend-pill"><span class="legend-dot status-absent"></span> Absent ({{ $absentDays }})</span>
                        <span class="legend-pill"><span class="legend-dot status-holiday"></span> Holiday ({{ $holidayDays }})</span>
                    </div>
                </div>

                <div class="calendar-wrap">
                    {{-- Day-of-week headers --}}
                    <div class="calendar-weekdays-row">
                        <div class="weekday-title is-weekend">Sun</div>
                        <div class="weekday-title">Mon</div>
                        <div class="weekday-title">Tue</div>
                        <div class="weekday-title">Wed</div>
                        <div class="weekday-title">Thu</div>
                        <div class="weekday-title">Fri</div>
                        <div class="weekday-title is-weekend">Sat</div>
                    </div>

                    {{-- Days Grid --}}
                    <div class="calendar-days-grid">
                        @foreach($calendar as $week)
                            @foreach($week as $day)
                                @if(!$day)
                                    <div class="cal-day-cell empty"></div>
                                @else
                                    @php
                                        $mark = $marksByDate[$day] ?? null;
                                        $status = $mark->status ?? ($calendarMonthMap[$day] ?? '');
                                        $dotClass = $status ? 'status-' . $status : '';
                                        $inTimeRaw = $mark->in_time ?? '';
                                        $outTimeRaw = $mark->out_time ?? '';
                                        $inTimeDisplay = $inTimeRaw ? date('h:i A', strtotime($inTimeRaw)) : '';
                                        $outTimeDisplay = $outTimeRaw ? date('h:i A', strtotime($outTimeRaw)) : '';
                                    @endphp
                                    <div class="cal-day-cell" data-date="{{ $day }}" data-status="{{ $status }}" data-in="{{ $inTimeRaw }}" data-out="{{ $outTimeRaw }}">
                                        <div class="cal-day-top">
                                            <span class="cal-day-num">{{ date('d', strtotime($day)) }}</span>
                                            @if($dotClass)
                                                <span class="cal-status-dot {{ $dotClass }}" title="{{ ucfirst(str_replace('_', ' ', $status)) }}"></span>
                                            @endif
                                        </div>
                                        <div class="cal-day-bottom">
                                            @if($attendanceType == 1 && ($inTimeDisplay || $outTimeDisplay))
                                                <span>{{ $inTimeDisplay }}{{ $outTimeDisplay ? ' - ' . $outTimeDisplay : '' }}</span>
                                            @elseif($status)
                                                <span class="text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right: Day Inspection Side Card --}}
            <div class="att-card">
                <div class="att-card-header">
                    <h3 class="att-card-title" style="color: #ffffff !important;">
                        <i class="fa fa-info-circle text-info"></i> <span style="color: #ffffff !important;">Day Inspection</span>
                    </h3>
                </div>
                <div class="att-inspect-box">
                    <div class="inspect-date-banner">
                        <small>Selected Date</small>
                        <strong id="inspect-date-text">Click a calendar day</strong>
                    </div>

                    <div class="inspect-item">
                        <span class="inspect-label">Status</span>
                        <div id="inspect-status-wrap">
                            <span class="inspect-status-badge status-none" id="inspect-status-badge">Select a day</span>
                        </div>
                    </div>

                    @if($attendanceType == 1)
                        <div class="inspect-item">
                            <span class="inspect-label">Check-In Time</span>
                            <span class="inspect-value" id="inspect-in-time">-</span>
                        </div>
                        <div class="inspect-item">
                            <span class="inspect-label">Check-Out Time</span>
                            <span class="inspect-value" id="inspect-out-time">-</span>
                        </div>
                    @endif

                    <div class="inspect-item">
                        <span class="inspect-label">Entity</span>
                        <span class="inspect-value">{{ $selectedTitleName }}</span>
                        <small class="text-muted">{{ $selectedUniqueId }} &bull; {{ ucfirst($activeTab) }}</small>
                    </div>

                    <div class="mt-2">
                        <a href="{{ url('attendance/mark') }}" class="btn btn-sm btn-outline-primary btn-block" style="font-size: 11px; border-radius: 2px;">
                            <i class="fa fa-pencil mr-1"></i> Go to Mark Attendance
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- 6. Yearly Overview Heatmap Card (Jan - Dec) --}}
        <div class="att-card">
            <div class="att-card-header">
                <h3 class="att-card-title" style="color: #ffffff !important;">
                    <i class="fa fa-th text-info"></i> <span style="color: #ffffff !important;">Yearly Attendance Heatmap (Jan &ndash; Dec {{ $year }})</span>
                </h3>
                <div class="att-legend-pills">
                    <span class="legend-pill"><span class="legend-dot status-in"></span> In</span>
                    <span class="legend-pill"><span class="legend-dot status-out"></span> Out</span>
                    <span class="legend-pill"><span class="legend-dot status-halfday"></span> Half Day</span>
                    <span class="legend-pill"><span class="legend-dot status-absent"></span> Absent</span>
                    <span class="legend-pill"><span class="legend-dot status-holiday"></span> Holiday</span>
                </div>
            </div>

            <div class="yearly-grid-compact">
                @foreach($yearlyOverview as $item)
                    <div class="month-tile-compact">
                        <div class="month-tile-header">
                            <span class="month-tile-title">{{ $item['label'] }} {{ $item['year'] }}</span>
                            <span class="text-muted" style="font-size: 9px;">30/31d</span>
                        </div>

                        <div class="month-dot-grid">
                            @foreach($item['grid'] as $cell)
                                @if($cell === null)
                                    <span class="empty"></span>
                                @elseif($cell === '')
                                    <span></span>
                                @else
                                    <span class="status-{{ $cell }}" title="{{ ucfirst($cell) }}"></span>
                                @endif
                            @endforeach
                        </div>

                        <div class="month-micro-summary">
                            <span class="micro-p" title="Present">P: {{ $item['counts']['in'] ?? 0 }}</span>
                            <span class="micro-a" title="Absent">A: {{ $item['counts']['absent'] ?? 0 }}</span>
                            <span class="micro-h" title="Half Day">H: {{ $item['counts']['halfday'] ?? 0 }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: 'resolve' });
    }

    function formatDateFormatted(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        const d = new Date(parts[0], parts[1] - 1, parts[2]);
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayName = dayNames[d.getDay()] || '';
        return parts[2] + '/' + parts[1] + '/' + parts[0] + ' (' + dayName + ')';
    }

    function formatTime12(timeStr) {
        if (!timeStr) return '-';
        const parts = timeStr.split(':');
        if (parts.length < 2) return timeStr;
        let h = parseInt(parts[0], 10);
        const m = parts[1];
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        return h + ':' + m + ' ' + ampm;
    }

    // Interactive Day Inspection on Calendar Click
    $('.cal-day-cell:not(.empty)').on('click', function() {
        $('.cal-day-cell').removeClass('active');
        $(this).addClass('active');

        const date = $(this).data('date');
        const rawStatus = $(this).data('status') || '';
        const inTime = $(this).data('in') || '';
        const outTime = $(this).data('out') || '';

        $('#inspect-date-text').text(formatDateFormatted(date) || 'Select a day');

        let statusText = 'Not Marked';
        let badgeClass = 'status-none';

        if (rawStatus) {
            statusText = rawStatus.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
            badgeClass = 'status-' + rawStatus;
        }

        $('#inspect-status-badge').text(statusText).removeClass().addClass('inspect-status-badge ' + badgeClass);

        $('#inspect-in-time').text(formatTime12(inTime));
        $('#inspect-out-time').text(formatTime12(outTime));
    });

    // Auto-select today or the first marked day on load
    const $todayCell = $('.cal-day-cell[data-date="{{ date("Y-m-d") }}"]');
    if ($todayCell.length) {
        $todayCell.trigger('click');
    } else {
        $('.cal-day-cell:not(.empty)').first().trigger('click');
    }
});
</script>
@endsection

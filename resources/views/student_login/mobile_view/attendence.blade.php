@php
    $getUser = Helper::getUser();
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($getUser->ClassTypes)->name ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');
@endphp

@extends('student_login.layout.app')

@section('title', 'Attendance Calendar')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT ATTENDANCE CALENDAR (SHARP CARDS + NAVY THEME)
   ========================================================================== */


/* 2. Top Hero Card - Attendance Snapshot */
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

/* Month Switcher in Hero */
.hero-month-selector {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 15, 30, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 4px;
    margin-bottom: 10px;
}
.hero-month-btn {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: all .12s ease;
}
.hero-month-btn:active {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(0.94);
}
.hero-month-btn:disabled {
    opacity: 0.35;
    pointer-events: none;
}
.hero-month-title {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}
.hero-month-name {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: .01em;
    line-height: 1.15;
}
.hero-month-sub {
    font-size: 8.5px;
    color: #93c5fd;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}

/* Dual Live Radar in Hero */
.hero-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    background: rgba(0, 15, 30, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
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
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 4px;
}
.hero-radar-val {
    font-size: 18px;
    font-weight: 800;
    color: #38bdf8;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.hero-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 500;
}

/* Hero Progress Fill */
.hero-progress-wrap {
    height: 5px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 3px;
    overflow: hidden;
}
.hero-progress-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #10b981, #38bdf8);
    border-radius: 3px;
    transition: width .3s ease;
}

/* 3. Sharp Section Cards */
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
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

/* 4. Monthly Attendance Status Matrix */
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

/* 5. Native Calendar Component */
.calendar-weekdays-row {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 4px;
}
.cal-weekday-cell {
    text-align: center;
    font-size: 9.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    padding: 3px 0;
}
.cal-weekday-cell.is-weekend {
    color: #dc2626;
}

.calendar-days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    transition: opacity .15s ease;
}
.calendar-days-grid.is-loading {
    opacity: 0.45;
    pointer-events: none;
}

.cal-day-cell {
    position: relative;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    user-select: none;
    transition: all .12s ease;
}
.cal-day-cell.cal-empty {
    background: transparent;
    border-color: transparent;
    cursor: default;
    pointer-events: none;
}
.cal-day-cell.cal-future {
    opacity: 0.45;
    cursor: default;
}
.cal-day-cell.cal-today {
    border: 2px solid #0284c7 !important;
    font-weight: 800;
}

/* Status-specific Day Colors */
.cal-day-cell.status-present {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
}
.cal-day-cell.status-absent {
    background: #fef2f2;
    border-color: #fca5a5;
    color: #b91c1c;
}
.cal-day-cell.status-leave {
    background: #fffbeb;
    border-color: #fcd34d;
    color: #b45309;
}
.cal-day-cell.status-halfday {
    background: #faf5ff;
    border-color: #d8b4fe;
    color: #7e22ce;
}
.cal-day-cell.status-holiday {
    background: #f0f9ff;
    border-color: #bae6fd;
    color: #0369a1;
}
.cal-day-cell.status-event,
.cal-day-cell.status-exam {
    background: #f5f3ff;
    border-color: #c4b5fd;
    color: #6d28d9;
}
.cal-day-cell.is-selected {
    outline: 2px solid #002C54;
    outline-offset: 1px;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
    transform: scale(1.05);
    z-index: 2;
}

/* Mini Dot Indicator */
.cal-dot-indicator {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    margin-top: 2px;
}
.status-present .cal-dot-indicator { background: #16a34a; }
.status-absent .cal-dot-indicator { background: #dc2626; }
.status-leave .cal-dot-indicator { background: #d97706; }
.status-halfday .cal-dot-indicator { background: #9333ea; }
.status-holiday .cal-dot-indicator { background: #0284c7; }

/* Calendar Legend Strip */
.cal-legend-strip {
    display: flex;
    align-items: center;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 4px 2px;
    margin-top: 8px;
    border-top: 1px solid #f1f5f9;
}
.legend-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
}
.legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}
.dot-present { background: #16a34a; }
.dot-absent { background: #dc2626; }
.dot-leave { background: #d97706; }
.dot-halfday { background: #9333ea; }
.dot-holiday { background: #0284c7; }

/* 6. Daily Activity List */
.native-list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none !important;
    color: inherit;
    transition: background .12s ease;
}
.native-list-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.native-list-item.is-highlighted {
    background: #e0f2fe;
    border-radius: 4px;
    padding: 8px 6px;
}
.item-date-badge {
    width: 38px;
    height: 38px;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.date-badge-day {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1;
}
.date-badge-wday {
    font-size: 8px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-top: 1px;
}
.item-info-col {
    flex: 1;
    overflow: hidden;
}
.item-primary-txt {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.item-secondary-txt {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 1px;
}
.status-chip {
    padding: 2px 7px;
    border-radius: 3px;
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .02em;
    display: inline-block;
}
.chip-present { background: #dcfce7; color: #15803d; }
.chip-absent { background: #fee2e2; color: #b91c1c; }
.chip-leave { background: #fef3c7; color: #b45309; }
.chip-halfday { background: #f3e8ff; color: #7e22ce; }
.chip-holiday { background: #e0f2fe; color: #0369a1; }

.empty-state-card {
    text-align: center;
    padding: 24px 10px;
    color: #94a3b8;
    font-size: 11px;
}
.empty-state-card i {
    font-size: 24px;
    margin-bottom: 6px;
    display: block;
}
</style>


{{-- 1. Top Hero Snapshot Card --}}
<div class="native-hero-card">
    {{-- Month Selector Bar inside Hero --}}
    <div class="hero-month-selector">
        <button type="button" class="hero-month-btn" id="prevMonthBtn" title="Previous Month">
            <i class="fa fa-chevron-left"></i>
        </button>
        <div class="hero-month-title">
            <span class="hero-month-name" id="heroMonthLabel">Loading…</span>
            <span class="hero-month-sub"><i class="fa fa-calendar-check-o mr-1"></i>Monthly Attendance Record</span>
        </div>
        <button type="button" class="hero-month-btn" id="nextMonthBtn" title="Next Month">
            <i class="fa fa-chevron-right"></i>
        </button>
    </div>

    {{-- Dual Live Radar in Hero --}}
    <div class="hero-dual-grid">
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-pie-chart text-info"></i> Attendance Rate
            </span>
            <div class="hero-radar-val" id="heroRateVal" style="color: #4ade80;">0%</div>
            <span class="hero-radar-meta" id="heroCreditMeta">0 Days Credited</span>
        </div>
        <div class="hero-radar-col">
            <span class="hero-radar-tag">
                <i class="fa fa-calendar-o text-warning"></i> Working Days
            </span>
            <div class="hero-radar-val" id="heroWorkingDaysVal">0 <small style="font-size:11px;font-weight:600;color:#94a3b8;">Days</small></div>
            <span class="hero-radar-meta" id="heroAbsentMeta">0 Days Absent</span>
        </div>
    </div>

    {{-- Progress Fill Bar --}}
    <div class="hero-progress-wrap">
        <div class="hero-progress-bar" id="heroProgressBar"></div>
    </div>
</div>

{{-- 2. Monthly Attendance Status Matrix --}}
<div class="sharp-section-card">
    <div class="sharp-card-header">
        <h3 class="sharp-card-title">
            <i class="fa fa-th-large text-primary"></i> Status Matrix Breakdown
        </h3>
        <span class="badge" style="background:#e0f2fe;color:#0369a1;font-size:9.5px;padding:3px 6px;" id="matrixBadge">
            {{ now()->format('M Y') }}
        </span>
    </div>
    <div class="matrix-pills-row">
        <div class="matrix-status-cell cell-present">
            <div class="matrix-status-tag">Present</div>
            <div class="matrix-status-val" id="matPresentCount">0</div>
        </div>
        <div class="matrix-status-cell cell-absent">
            <div class="matrix-status-tag">Absent</div>
            <div class="matrix-status-val" id="matAbsentCount">0</div>
        </div>
        <div class="matrix-status-cell cell-leave">
            <div class="matrix-status-tag">Leave</div>
            <div class="matrix-status-val" id="matLeaveCount">0</div>
        </div>
        <div class="matrix-status-cell cell-half">
            <div class="matrix-status-tag">Half Day</div>
            <div class="matrix-status-val" id="matHalfCount">0</div>
        </div>
        <div class="matrix-status-cell cell-holiday">
            <div class="matrix-status-tag">Holiday</div>
            <div class="matrix-status-val" id="matHolidayCount">0</div>
        </div>
        <div class="matrix-status-cell" style="background:#f1f5f9;border-color:#cbd5e1;">
            <div class="matrix-status-tag">Working</div>
            <div class="matrix-status-val" id="matWorkingCount">0</div>
        </div>
    </div>
</div>

{{-- 3. Interactive Native Calendar Grid --}}
<div class="sharp-section-card">
    <div class="sharp-card-header">
        <h3 class="sharp-card-title">
            <i class="fa fa-calendar-check-o text-primary"></i> Monthly Calendar Grid
        </h3>
        <button type="button" class="sharp-card-link" id="btnGoCurrentMonth" title="Go to current month">
            Current Month <i class="fa fa-crosshairs"></i>
        </button>
    </div>

    {{-- Weekday Header Row --}}
    <div class="calendar-weekdays-row">
        <div class="cal-weekday-cell is-weekend">Sun</div>
        <div class="cal-weekday-cell">Mon</div>
        <div class="cal-weekday-cell">Tue</div>
        <div class="cal-weekday-cell">Wed</div>
        <div class="cal-weekday-cell">Thu</div>
        <div class="cal-weekday-cell">Fri</div>
        <div class="cal-weekday-cell">Sat</div>
    </div>

    {{-- Day Cells Grid --}}
    <div class="calendar-days-grid" id="calendarGrid"></div>

    {{-- Color Legend Strip --}}
    <div class="cal-legend-strip">
        <span class="legend-pill"><span class="legend-dot dot-present"></span> Present</span>
        <span class="legend-pill"><span class="legend-dot dot-absent"></span> Absent</span>
        <span class="legend-pill"><span class="legend-dot dot-leave"></span> Leave</span>
        <span class="legend-pill"><span class="legend-dot dot-halfday"></span> Half Day</span>
        <span class="legend-pill"><span class="legend-dot dot-holiday"></span> Holiday</span>
    </div>
</div>

{{-- 4. Daily Attendance History Log --}}
<div class="sharp-section-card">
    <div class="sharp-card-header">
        <h3 class="sharp-card-title">
            <i class="fa fa-clock-o text-primary"></i> Daily Activity Timeline
        </h3>
        <span class="badge" style="background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;font-size:9.5px;" id="activityCountBadge">
            0 Records
        </span>
    </div>
    <div id="activityLogContainer">
        <div class="empty-state-card">
            <i class="fa fa-calendar-o"></i>
            <span>Loading daily activity…</span>
        </div>
    </div>
</div>

{{-- 5. Quick Apply Leave Action Card --}}
<div style="margin-top: 4px; margin-bottom: 12px;">
    <a href="{{ url('applyLeaveStudent') }}" class="btn btn-primary" style="width: 100%; gap: 8px; font-size: 11.5px; padding: 9px 12px;">
        <i class="fa fa-calendar-minus-o"></i> Apply for Student Leave &rarr;
    </a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const endpoint = @json(url('getAttendanceDatesStudent'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    const today = new Date();
    let currentSelectedDate = new Date(today.getFullYear(), today.getMonth(), 1);
    let activeRequestNumber = 0;

    const prevBtn = document.getElementById("prevMonthBtn");
    const nextBtn = document.getElementById("nextMonthBtn");
    const todayBtn = document.getElementById("btnGoCurrentMonth");
    const calGrid = document.getElementById("calendarGrid");
    const heroMonthLabel = document.getElementById("heroMonthLabel");
    const matrixBadge = document.getElementById("matrixBadge");
    const heroRateVal = document.getElementById("heroRateVal");
    const heroCreditMeta = document.getElementById("heroCreditMeta");
    const heroWorkingDaysVal = document.getElementById("heroWorkingDaysVal");
    const heroAbsentMeta = document.getElementById("heroAbsentMeta");
    const heroProgressBar = document.getElementById("heroProgressBar");
    const logContainer = document.getElementById("activityLogContainer");
    const countBadge = document.getElementById("activityCountBadge");

    // Format date key to YYYY-MM-DD
    function toDateKey(d) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function isSameDate(a, b) {
        return a.getFullYear() === b.getFullYear() &&
               a.getMonth() === b.getMonth() &&
               a.getDate() === b.getDate();
    }

    // Render Calendar Tiles
    function renderCalendarGrid(attendanceMap) {
        calGrid.innerHTML = "";
        calGrid.classList.remove("is-loading");

        const year = currentSelectedDate.getFullYear();
        const month = currentSelectedDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const startDayOfWeek = firstDay.getDay();

        // Empty padding cells for start of month
        for (let i = 0; i < startDayOfWeek; i++) {
            const emptyCell = document.createElement("div");
            emptyCell.className = "cal-day-cell cal-empty";
            calGrid.appendChild(emptyCell);
        }

        // Days in Month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayDate = new Date(year, month, day);
            const dateStr = toDateKey(dayDate);
            const status = (attendanceMap[dateStr] || "").toLowerCase().trim();
            const isToday = isSameDate(dayDate, today);
            const isFuture = dayDate > today;

            const cell = document.createElement("div");
            cell.className = "cal-day-cell";
            cell.setAttribute("data-date", dateStr);

            if (isToday) cell.classList.add("cal-today");
            if (isFuture) cell.classList.add("cal-future");

            if (status) {
                cell.classList.add(`status-${status}`);
                cell.title = `${dateStr}: ${status.replace('_', ' ').toUpperCase()}`;
            }

            cell.innerHTML = `<span>${day}</span><span class="cal-dot-indicator"></span>`;

            // Click interaction: Highlight in log
            cell.addEventListener("click", function() {
                document.querySelectorAll(".cal-day-cell").forEach(c => c.classList.remove("is-selected"));
                this.classList.add("is-selected");

                const targetRow = document.querySelector(`.native-list-item[data-date="${dateStr}"]`);
                if (targetRow) {
                    document.querySelectorAll(".native-list-item").forEach(r => r.classList.remove("is-highlighted"));
                    targetRow.classList.add("is-highlighted");
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            calGrid.appendChild(cell);
        }

        // Disable Next button if selected month is current or future
        nextBtn.disabled = (year === today.getFullYear() && month >= today.getMonth());
    }

    // Render Activity Feed
    function renderActivityLog(detailsList) {
        if (!detailsList || detailsList.length === 0) {
            logContainer.innerHTML = `
                <div class="empty-state-card">
                    <i class="fa fa-calendar-check-o"></i>
                    <span>No marked attendance records found for this month.</span>
                </div>`;
            countBadge.textContent = "0 Records";
            return;
        }

        countBadge.textContent = `${detailsList.length} Records`;
        let html = "";

        detailsList.forEach(item => {
            const dateParts = item.date.split('-');
            const dayNum = dateParts[2] || '01';
            const statusKey = (item.status || 'present').toLowerCase();
            
            let chipClass = "chip-present";
            if (statusKey === 'absent') chipClass = "chip-absent";
            else if (statusKey === 'leave') chipClass = "chip-leave";
            else if (statusKey === 'halfday') chipClass = "chip-halfday";
            else if (statusKey === 'holiday') chipClass = "chip-holiday";

            let punchInfo = "";
            if (item.in_time || item.out_time) {
                punchInfo = `<i class="fa fa-clock-o mr-1"></i> In: ${item.in_time || '--'} &bull; Out: ${item.out_time || '--'}`;
            } else {
                punchInfo = `<i class="fa fa-calendar-o mr-1"></i> ${item.date}`;
            }

            html += `
                <div class="native-list-item" data-date="${item.date}">
                    <div class="item-date-badge">
                        <span class="date-badge-day">${dayNum}</span>
                        <span class="date-badge-wday">${item.day}</span>
                    </div>
                    <div class="item-info-col">
                        <div class="item-primary-txt">
                            <span>${item.date}</span>
                            <span class="status-chip ${chipClass}">${item.label}</span>
                        </div>
                        <div class="item-secondary-txt">${punchInfo}</div>
                    </div>
                </div>`;
        });

        logContainer.innerHTML = html;
    }

    // Fetch and Load Data
    async function loadAttendanceData() {
        const reqId = ++activeRequestNumber;
        calGrid.classList.add("is-loading");

        const targetMonth = currentSelectedDate.getMonth() + 1;
        const targetYear = currentSelectedDate.getFullYear();

        try {
            const response = await fetch(endpoint, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    month: targetMonth,
                    year: targetYear
                })
            });

            if (!response.ok) throw new Error("Network error fetching attendance");
            const res = await response.json();

            if (reqId !== activeRequestNumber) return;

            // 1. Update Month Header
            heroMonthLabel.textContent = res.month || currentSelectedDate.toLocaleDateString("en-IN", { month: "long", year: "numeric" });
            matrixBadge.textContent = currentSelectedDate.toLocaleDateString("en-IN", { month: "short", year: "numeric" });

            // 2. Update Metrics in Hero
            const pct = Number(res.metrics?.percentage || 0);
            const scheduledDays = Number(res.metrics?.scheduled_days || 0);
            const creditDays = Number(res.metrics?.attendance_credit || 0);
            const absentDays = Number(res.total?.Absent || 0);

            heroRateVal.textContent = `${pct}%`;
            heroRateVal.style.color = pct >= 75 ? "#4ade80" : (pct >= 50 ? "#facc15" : "#f87171");
            heroCreditMeta.textContent = `${creditDays} Days Credited`;
            heroWorkingDaysVal.innerHTML = `${scheduledDays} <small style="font-size:11px;font-weight:600;color:#94a3b8;">Days</small>`;
            heroAbsentMeta.textContent = `${absentDays} Days Absent`;
            heroProgressBar.style.width = `${Math.min(100, pct)}%`;

            // 3. Update Status Matrix
            document.getElementById("matPresentCount").textContent = Number(res.total?.Present || 0);
            document.getElementById("matAbsentCount").textContent = absentDays;
            document.getElementById("matLeaveCount").textContent = Number(res.total?.Leave || 0);
            document.getElementById("matHalfCount").textContent = Number(res.total?.['Half Day'] || 0);
            document.getElementById("matHolidayCount").textContent = Number(res.total?.Holiday || 0);
            document.getElementById("matWorkingCount").textContent = scheduledDays;

            // 4. Render Calendar & Daily Log
            renderCalendarGrid(res.data || {});
            renderActivityLog(res.details || []);

        } catch (err) {
            if (reqId !== activeRequestNumber) return;
            calGrid.classList.remove("is-loading");
            logContainer.innerHTML = `
                <div class="empty-state-card" style="color:#ef4444;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span>Could not load attendance data. Please try again.</span>
                </div>`;
        }
    }

    // Month Navigation Listeners
    prevBtn.addEventListener("click", function() {
        currentSelectedDate.setMonth(currentSelectedDate.getMonth() - 1);
        loadAttendanceData();
    });

    nextBtn.addEventListener("click", function() {
        if (!nextBtn.disabled) {
            currentSelectedDate.setMonth(currentSelectedDate.getMonth() + 1);
            loadAttendanceData();
        }
    });

    todayBtn.addEventListener("click", function() {
        currentSelectedDate = new Date(today.getFullYear(), today.getMonth(), 1);
        loadAttendanceData();
    });

    // Initial Load
    loadAttendanceData();
});
</script>
@endsection

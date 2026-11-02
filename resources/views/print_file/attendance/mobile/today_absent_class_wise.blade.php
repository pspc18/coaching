@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $reportDate = $reportDate ?? Carbon::now();
    $selectedClassIds = (array) ($selectedClassIds ?? []);
    $absentCount = $absentCount ?? (isset($rows) ? count($rows) : 0);
    $rows = $rows ?? collect();
    $classes = $classes ?? collect();
    $isHoliday = $isHoliday ?? false;

    $currentDateStr = $reportDate->format('Y-m-d');
    $yesterdayStr = $reportDate->copy()->subDay()->format('Y-m-d');
    $tomorrowStr = $reportDate->copy()->addDay()->format('Y-m-d');
    $todayStr = now()->format('Y-m-d');

    // Calculate absent count by class for instant chip filters
    $classCounts = [];
    foreach ($rows as $r) {
        $cName = $r['class_name'] ?: 'Other';
        $classCounts[$cName] = ($classCounts[$cName] ?? 0) + 1;
    }
@endphp

@extends('layout.mobile_app')

@section('title', 'Absent Students - ' . $reportDate->format('d M Y'))

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE ABSENT STUDENTS CLASS-WISE
   Strictly adheres to Arise ERP Mobile Design System (Plus Jakarta Sans & 4px radius)
   ============================================================================== */

:root {
    --abs-navy-900: #001428;
    --abs-navy-800: #002C54;
    --abs-sky: #0284c7;
    --abs-emerald: #10b981;
    --abs-rose: #f43f5e;
    --abs-rose-soft: #fff1f2;
    --abs-card-border: #cbd5e1;
}

.mob-absent-page {
    padding: 8px 10px 75px;
    background: #f1f5f9;
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
}
.mob-absent-page * {
    box-sizing: border-box;
}

/* 1. Hero Card */
.mob-abs-hero {
    background: linear-gradient(135deg, #001428 0%, #002C54 55%, #08335c 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    margin-bottom: 8px;
    position: relative;
}
.mob-abs-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-abs-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.mob-abs-top-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-abs-tool-btn {
    height: 26px;
    padding: 0 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-abs-tool-btn:active {
    background: rgba(255, 255, 255, 0.28);
    transform: scale(0.95);
}

/* Date Navigator Bar inside Hero */
.mob-abs-date-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 4px;
    padding: 3px 6px;
    margin-bottom: 8px;
}
.mob-date-arrow {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-date-arrow:active {
    background: rgba(255, 255, 255, 0.3);
}
.mob-date-current-wrap {
    text-align: center;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    color: #ffffff;
}
.mob-date-current-text {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: -0.01em;
}

/* 3-Glance Metrics Grid */
.mob-abs-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-abs-metric-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 5px 4px;
    text-align: center;
}
.mob-abs-metric-lbl {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-abs-metric-num {
    font-size: 14px;
    font-weight: 900;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.color-absent { color: #fecdd3 !important; }
.color-status { color: #34d399 !important; }
.color-class { color: #38bdf8 !important; }
.mob-abs-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Action Bar */
.mob-abs-actions-bar {
    display: flex;
    gap: 5px;
}
.mob-act-btn {
    flex: 1;
    height: 28px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-act-btn:active { transform: scale(0.97); }
.btn-act-pdf { background: #e11d48; color: #ffffff !important; }
.btn-act-filter { background: rgba(255, 255, 255, 0.16); color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.28); }

/* 2. Quick Search & Filter Chips Toolbar */
.mob-toolbar-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 7px;
}
.mob-search-icon {
    position: absolute;
    left: 9px;
    color: #64748b;
    font-size: 11px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 31px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    font-size: 11.5px;
    padding: 0 28px 0 27px;
    outline: none;
    transition: border-color .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
}

/* Horizontal Scrollable Class Filter Chips */
.mob-chips-scroll {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 2px;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-filter-chip {
    flex: 0 0 auto;
    padding: 3px 8px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    cursor: pointer;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-filter-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #001f3f;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.2);
}
.mob-chip-counter {
    background: rgba(0, 0, 0, 0.08);
    padding: 0 4px;
    border-radius: 2px;
    font-size: 9px;
    font-weight: 800;
}
.mob-filter-chip.active .mob-chip-counter {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* 3. Absent Student Feed Card */
.mob-absent-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 3.5px solid #ef4444;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 7px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: transform .1s ease;
}
.mob-absent-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 4px;
}
.mob-student-title-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}
.mob-student-idx {
    font-size: 10px;
    font-weight: 800;
    color: #64748b;
    flex-shrink: 0;
}
.mob-student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.mob-class-badge {
    font-size: 9.5px;
    font-weight: 700;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    padding: 1px 6px;
    border-radius: 3px;
    flex-shrink: 0;
}
.mob-absent-body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    font-size: 11px;
    color: #475569;
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px dashed #e2e8f0;
}
.mob-father-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-adm-tag {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 1px 5px;
    border-radius: 2px;
}

/* Card Action Strip */
.mob-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
}
.mob-call-btn {
    height: 25px;
    padding: 0 8px;
    border-radius: 3px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
}
.mob-wa-btn {
    height: 25px;
    padding: 0 8px;
    border-radius: 3px;
    background: #22c55e;
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
}
.mob-profile-btn {
    height: 25px;
    padding: 0 7px;
    border-radius: 3px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #002C54 !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-decoration: none !important;
}

/* 4. Empty State */
.mob-empty-box {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 28px 14px;
    text-align: center;
    margin-top: 10px;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 8px;
}
.icon-success { background: #dcfce7; color: #16a34a; }
.icon-holiday { background: #e0f2fe; color: #0284c7; }
.mob-empty-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    max-width: 320px;
    margin: 0 auto;
}

/* 5. Native Bottom Sheet Drawer for Filters */
.mob-sheet-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 20, 40, 0.65);
    backdrop-filter: blur(2px);
    z-index: 99990;
    opacity: 0;
    visibility: hidden;
    transition: all .2s ease;
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    background: #ffffff;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
    z-index: 99995;
    transform: translateY(105%);
    transition: transform .25s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 520px;
    margin: 0 auto;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
}
.mob-bottom-sheet.show {
    transform: translateY(0);
}
.mob-sheet-head {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.mob-sheet-body {
    padding: 12px 14px;
    overflow-y: auto;
    flex: 1;
}
.mob-sheet-foot {
    padding: 10px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 6px;
}

/* Print Overrides */
@media print {
    .mob-absent-page { padding: 0 !important; background: #ffffff !important; }
    .mob-abs-hero-top .mob-abs-top-actions, .mob-abs-actions-bar, .mob-toolbar-card, .mob-card-actions, .mobile-bottom-dock, .mobile-top-bar {
        display: none !important;
    }
    .mob-abs-hero { background: none !important; color: #000000 !important; border: none !important; box-shadow: none !important; border-bottom: 2px solid #000000 !important; }
    .mob-abs-hero-title, .mob-student-name { color: #000000 !important; }
    .mob-absent-card { border-color: #999999 !important; box-shadow: none !important; }
}
</style>
@endsection

@section('content')
<div class="mob-absent-page">

    {{-- 1. NATIVE MOBILE HERO CARD --}}
    <div class="mob-abs-hero">
        <div class="mob-abs-hero-top">
            <div class="mob-abs-hero-title">
                <i class="fa fa-user-times text-warning"></i>
                <span>Absent Students</span>
            </div>
            <div class="mob-abs-top-actions">
                <a href="{{ url('attendance/view?layout=mobile') }}" class="mob-abs-tool-btn" title="Attendance Dashboard">
                    <i class="fa fa-th-large"></i> Grid
                </a>
                <button type="button" class="mob-abs-tool-btn" onclick="window.print()" title="Print List">
                    <i class="fa fa-print"></i>
                </button>
            </div>
        </div>

        {{-- Date Navigator Bar --}}
        <div class="mob-abs-date-nav">
            <a href="{{ route('attendance.today.absent-class-wise', ['date' => $yesterdayStr, 'class_type_id' => $selectedClassIds]) }}" class="mob-date-arrow" title="Previous Day">
                <i class="fa fa-chevron-left"></i>
            </a>

            <div class="mob-date-current-wrap" id="btnOpenFilterSheet">
                <i class="fa fa-calendar text-info"></i>
                <div class="mob-date-current-text">{{ $reportDate->format('D, d M Y') }}</div>
                @if($currentDateStr === $todayStr)
                    <span class="badge badge-success" style="font-size: 8px; padding: 1px 4px; background: #10b981;">TODAY</span>
                @endif
                <i class="fa fa-caret-down text-muted" style="font-size: 11px;"></i>
            </div>

            <a href="{{ route('attendance.today.absent-class-wise', ['date' => $tomorrowStr, 'class_type_id' => $selectedClassIds]) }}" class="mob-date-arrow" title="Next Day">
                <i class="fa fa-chevron-right"></i>
            </a>
        </div>

        {{-- 3-KPI Glance Metrics Grid --}}
        <div class="mob-abs-metrics-grid">
            <div class="mob-abs-metric-cell">
                <div class="mob-abs-metric-lbl"><i class="fa fa-user-times"></i> Absent</div>
                <div class="mob-abs-metric-num color-absent">{{ $absentCount }}</div>
                <div class="mob-abs-metric-sub">Students</div>
            </div>

            <div class="mob-abs-metric-cell">
                <div class="mob-abs-metric-lbl"><i class="fa fa-calendar-check-o"></i> Status</div>
                <div class="mob-abs-metric-num color-status" style="font-size: 11.5px;">
                    {{ $isHoliday ? 'Holiday' : 'Open' }}
                </div>
                <div class="mob-abs-metric-sub">{{ $isHoliday ? 'No Classes' : 'Active Day' }}</div>
            </div>

            <div class="mob-abs-metric-cell">
                <div class="mob-abs-metric-lbl"><i class="fa fa-graduation-cap"></i> Classes</div>
                <div class="mob-abs-metric-num color-class" style="font-size: 11.5px;">
                    {{ count($selectedClassIds) ? count($selectedClassIds) . ' Sel' : 'All' }}
                </div>
                <div class="mob-abs-metric-sub">{{ count($selectedClassIds) ? 'Filtered' : 'All Classes' }}</div>
            </div>
        </div>

        {{-- Fast Action Bar --}}
        <div class="mob-abs-actions-bar">
            @if($absentCount > 0)
                <a href="{{ route('attendance.today.absent-class-wise.pdf', ['date' => $currentDateStr, 'class_type_id' => $selectedClassIds]) }}" target="_blank" class="mob-act-btn btn-act-pdf">
                    <i class="fa fa-file-pdf-o"></i> Download PDF
                </a>
            @endif
            <button type="button" class="mob-act-btn btn-act-filter" id="btnFilterTrigger">
                <i class="fa fa-sliders"></i> Date &amp; Classes
            </button>
            @if(!empty($selectedClassIds) || $currentDateStr !== $todayStr)
                <a href="{{ route('attendance.today.absent-class-wise') }}" class="mob-act-btn btn-act-filter" style="flex: 0 0 34px;" title="Reset Filters">
                    <i class="fa fa-refresh"></i>
                </a>
            @endif
        </div>
    </div>

    {{-- 2. QUICK SEARCH & HORIZONTAL CLASS FILTER CHIPS --}}
    <div class="mob-toolbar-card">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search mob-search-icon"></i>
            <input type="text" id="mobAbsentSearch" class="mob-search-input" placeholder="Search absent by student name, adm #, father..." autocomplete="off">
            <i class="fa fa-times-circle mob-search-clear" id="mobSearchClear"></i>
        </div>

        {{-- Horizontal 1-Tap Class Filter Chips --}}
        <div class="mob-chips-scroll" id="classChipsScroll">
            <button type="button" class="mob-filter-chip active" data-class-filter="all">
                All <span class="mob-chip-counter" id="chipTotalCounter">{{ $absentCount }}</span>
            </button>
            @foreach($classCounts as $className => $cCount)
                <button type="button" class="mob-filter-chip" data-class-filter="{{ strtolower($className) }}">
                    {{ $className }} <span class="mob-chip-counter">{{ $cCount }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- 3. ABSENT STUDENTS FEED --}}
    <div id="absentCardsFeed">
        @forelse($rows as $idx => $row)
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', (string)($row['mobile'] ?? ''));
                $waText = urlencode("Dear Parent, your ward " . ($row['name'] ?: 'Student') . " (Adm #" . ($row['admission_no'] ?: '-') . ") is marked ABSENT today (" . $reportDate->format('d M Y') . ") at " . ($getSetting->name ?? 'School') . ". Kindly confirm if leave was requested.");
            @endphp
            <div class="mob-absent-card" 
                 data-student-name="{{ strtolower($row['name'] ?? '') }}" 
                 data-adm-no="{{ strtolower($row['admission_no'] ?? '') }}" 
                 data-father-name="{{ strtolower($row['father_name'] ?? '') }}" 
                 data-mobile="{{ strtolower($row['mobile'] ?? '') }}"
                 data-class-name="{{ strtolower($row['class_name'] ?? '') }}">
                
                <div class="mob-absent-head">
                    <div class="mob-student-title-wrap">
                        <span class="mob-student-idx">{{ $idx + 1 }}.</span>
                        <span class="mob-student-name">{{ $row['name'] ?: 'Unnamed' }}</span>
                    </div>
                    <span class="mob-class-badge">{{ $row['class_name'] ?: 'Class —' }}</span>
                </div>

                <div class="mob-absent-body">
                    <div class="mob-father-name">
                        <i class="fa fa-user text-muted mr-1"></i> Father: <b>{{ $row['father_name'] ?: '-' }}</b>
                    </div>
                    <span class="mob-adm-tag">Adm #{{ $row['admission_no'] ?: '-' }}</span>
                </div>

                <div class="mob-card-actions">
                    @if(!empty($cleanPhone))
                        <a href="tel:{{ $cleanPhone }}" class="mob-call-btn">
                            <i class="fa fa-phone"></i> Call
                        </a>
                        <a href="https://wa.me/91{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="mob-wa-btn">
                            <i class="fa fa-whatsapp"></i> WhatsApp
                        </a>
                    @else
                        <span style="font-size: 10px; color: #94a3b8;"><i class="fa fa-phone-slash"></i> No Phone</span>
                    @endif

                    @if(!empty($row['id']))
                        <a href="{{ url('studentDetail/' . $row['id'] . '?layout=mobile') }}" class="mob-profile-btn" title="View Profile">
                            <i class="fa fa-id-card-o"></i> Profile
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="mob-empty-box">
                @if($isHoliday)
                    <div class="mob-empty-icon icon-holiday">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <div class="mob-empty-title">School Holiday</div>
                    <div class="mob-empty-desc">
                        <b>{{ $reportDate->format('D, d F Y') }}</b> is designated as a holiday or school event. No attendance recorded.
                    </div>
                @else
                    <div class="mob-empty-icon icon-success">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="mob-empty-title">100% Attendance!</div>
                    <div class="mob-empty-desc">
                        No students are absent for <b>{{ $reportDate->format('D, d F Y') }}</b>. All enrolled students are marked present!
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Empty Search Results placeholder --}}
    <div id="noSearchMatch" class="mob-empty-box" style="display: none;">
        <div class="mob-empty-icon icon-holiday">
            <i class="fa fa-search"></i>
        </div>
        <div class="mob-empty-title">No Matching Students</div>
        <div class="mob-empty-desc">
            No absent students match your search criteria. Try a different name, class, or admission number.
        </div>
    </div>

</div>

{{-- 5. NATIVE FILTER BOTTOM SHEET DRAWER --}}
<div class="mob-sheet-backdrop" id="filterSheetBackdrop"></div>
<div class="mob-bottom-sheet" id="filterBottomSheet">
    <div class="mob-sheet-head">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders"></i> Filter Date &amp; Classes
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>

    <form method="GET" action="{{ route('attendance.today.absent-class-wise') }}" id="absentFilterForm">
        <div class="mob-sheet-body">
            {{-- Date Section --}}
            <div style="margin-bottom: 12px;">
                <label style="font-size: 10.5px; font-weight: 800; color: #334155; text-transform: uppercase; margin-bottom: 4px; display: block;">
                    <i class="fa fa-calendar text-primary"></i> Select Date
                </label>
                <div style="display: flex; gap: 5px; margin-bottom: 6px;">
                    <button type="button" class="mob-filter-chip" onclick="setFilterDate('{{ $todayStr }}')">Today</button>
                    <button type="button" class="mob-filter-chip" onclick="setFilterDate('{{ $yesterdayStr }}')">Yesterday</button>
                </div>
                <input type="date" name="date" id="filterDateInput" value="{{ $currentDateStr }}" class="form-control" style="height: 34px; font-size: 12px; border-radius: 4px; border: 1px solid #cbd5e1;">
            </div>

            {{-- Classes Multi-Select Section --}}
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                    <label style="font-size: 10.5px; font-weight: 800; color: #334155; text-transform: uppercase; margin: 0;">
                        <i class="fa fa-graduation-cap text-primary"></i> Target Classes
                    </label>
                    <div style="display: flex; gap: 4px;">
                        <button type="button" class="btn btn-xs btn-outline-primary" id="btnSelectAllClasses" style="font-size: 9.5px; padding: 2px 6px;">Check All</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" id="btnUnselectAllClasses" style="font-size: 9.5px; padding: 2px 6px;">Clear</button>
                    </div>
                </div>

                <div style="max-height: 220px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px;">
                    @foreach($classes as $c)
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; padding: 5px 4px; border-bottom: 1px solid #f1f5f9; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="class_type_id[]" value="{{ $c->id }}" class="class-checkbox" {{ in_array($c->id, $selectedClassIds) ? 'checked' : '' }}>
                            <span>{{ $c->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mob-sheet-foot">
            <button type="button" class="btn btn-secondary" id="btnCancelFilterSheet" style="flex: 1; font-size: 11.5px; font-weight: 700; height: 32px;">Cancel</button>
            <button type="submit" class="btn btn-primary" style="flex: 2; font-size: 11.5px; font-weight: 700; height: 32px; background: #002C54; border-color: #001f3f;">
                <i class="fa fa-check"></i> Apply Filter
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function setFilterDate(dateVal) {
    document.getElementById('filterDateInput').value = dateVal;
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Bottom Sheet Controls
    var sheet = document.getElementById('filterBottomSheet');
    var backdrop = document.getElementById('filterSheetBackdrop');
    var btnOpen = document.getElementById('btnFilterTrigger');
    var btnDateOpen = document.getElementById('btnOpenFilterSheet');
    var btnClose = document.getElementById('btnCloseFilterSheet');
    var btnCancel = document.getElementById('btnCancelFilterSheet');

    function openSheet() {
        sheet.classList.add('show');
        backdrop.classList.add('show');
    }
    function closeSheet() {
        sheet.classList.remove('show');
        backdrop.classList.remove('show');
    }

    if (btnOpen) btnOpen.addEventListener('click', openSheet);
    if (btnDateOpen) btnDateOpen.addEventListener('click', openSheet);
    if (btnClose) btnClose.addEventListener('click', closeSheet);
    if (btnCancel) btnCancel.addEventListener('click', closeSheet);
    if (backdrop) backdrop.addEventListener('click', closeSheet);

    // Select all / clear classes
    var btnSelectAll = document.getElementById('btnSelectAllClasses');
    var btnUnselect = document.getElementById('btnUnselectAllClasses');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            document.querySelectorAll('.class-checkbox').forEach(function(cb) { cb.checked = true; });
        });
    }
    if (btnUnselect) {
        btnUnselect.addEventListener('click', function() {
            document.querySelectorAll('.class-checkbox').forEach(function(cb) { cb.checked = false; });
        });
    }

    // 2. Client-Side Instant Search
    var searchInput = document.getElementById('mobAbsentSearch');
    var clearBtn = document.getElementById('mobSearchClear');
    var cards = document.querySelectorAll('.mob-absent-card');
    var noMatch = document.getElementById('noSearchMatch');
    var activeClassFilter = 'all';

    function applyFilters() {
        var term = searchInput.value.trim().toLowerCase();
        if (term.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
        }

        var visibleCount = 0;
        cards.forEach(function(card) {
            var name = card.getAttribute('data-student-name') || '';
            var adm = card.getAttribute('data-adm-no') || '';
            var father = card.getAttribute('data-father-name') || '';
            var mob = card.getAttribute('data-mobile') || '';
            var cName = card.getAttribute('data-class-name') || '';

            var matchesSearch = term === '' || name.includes(term) || adm.includes(term) || father.includes(term) || mob.includes(term) || cName.includes(term);
            var matchesClass = activeClassFilter === 'all' || cName === activeClassFilter;

            if (matchesSearch && matchesClass) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (cards.length > 0 && visibleCount === 0) {
            noMatch.style.display = 'block';
        } else {
            noMatch.style.display = 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    // 3. 1-Tap Class Filter Chips
    var chips = document.querySelectorAll('.mob-filter-chip[data-class-filter]');
    chips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            chips.forEach(function(c) { c.classList.remove('active'); });
            this.classList.add('active');
            activeClassFilter = this.getAttribute('data-class-filter');
            applyFilters();
        });
    });
});
</script>
@endsection

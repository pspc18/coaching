@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting */
.academic-calendar-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.academic-calendar-page * {
    box-sizing: border-box;
}

/* Hero Header Banner - admissionView Theme */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 8px;
}
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
}
.admission-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #fff;
}
.admission-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
}
.admission-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    line-height: 1.4;
}
.dash-btn-light {
    background: #fff;
    color: #002C54;
    border-color: #fff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-outline {
    background: transparent;
    color: #fff;
    border-color: rgba(255,255,255,.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #fff;
    border-color: #fff;
}

/* 3-Column Equal Height Card Architecture */
.cal-row-equal {
    display: flex;
    flex-wrap: wrap;
    margin-right: -4px;
    margin-left: -4px;
}
.cal-col-equal {
    padding-right: 4px;
    padding-left: 4px;
    display: flex;
    flex-direction: column;
}

.cal-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    margin-bottom: 8px;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}
.cal-card-header {
    background: #002C54;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.cal-card-header h3, .cal-card-header span {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
}
.cal-card-body {
    padding: 8px;
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
}

/* Date Mode Selector Buttons with High Contrast */
.date-mode-tabs {
    display: flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px;
    border-radius: 3px;
    gap: 4px;
    margin-bottom: 8px;
}
.date-mode-btn {
    flex: 1;
    border: 1px solid transparent;
    background: #ffffff;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    padding: 4px 6px;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
}
.date-mode-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.date-mode-btn.active {
    background: #002C54 !important;
    color: #ffffff !important;
    font-weight: 700;
    border-color: #002C54;
    box-shadow: 0 1px 2px rgba(0,0,0,.15);
}

/* Compact Form Styling */
.cal-form-group {
    margin-bottom: 7px;
}
.cal-form-group label {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
    display: block;
}
.cal-form-control {
    width: 100%;
    height: 28px;
    padding: 3px 7px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    transition: border-color .15s;
}
.cal-form-control:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
}
textarea.cal-form-control {
    height: auto;
    min-height: 44px;
}

/* Notification Box */
.notification-toggle-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 8px;
    margin-top: 4px;
    margin-bottom: 6px;
}
.role-pill-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    color: #334155;
    cursor: pointer;
    margin-right: 3px;
    margin-bottom: 3px;
    user-select: none;
}
.role-pill-checkbox.checked {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Calendar Navigation Toolbar */
.calendar-nav-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #002342;
    color: #ffffff;
    padding: 5px 8px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    flex-shrink: 0;
}
.nav-arrow-btn {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff !important;
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 2px;
    text-decoration: none !important;
    font-size: 11px;
    transition: all .15s;
}
.nav-arrow-btn:hover {
    background: #0284c7;
    color: #ffffff !important;
    border-color: #0284c7;
}
.cal-select-dark {
    background: #08335c !important;
    color: #ffffff !important;
    border: 1px solid rgba(255,255,255,0.3) !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    height: 24px !important;
    padding: 1px 4px !important;
}
.cal-select-dark option {
    background: #ffffff !important;
    color: #0f172a !important;
}

/* Compact Calendar Grid */
.compact-calendar-grid {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
}
.compact-calendar-grid thead th {
    background: #08335c;
    color: #ffffff;
    padding: 4px 2px;
    font-size: 10.5px;
    font-weight: 700;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .03em;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
}
.compact-calendar-grid thead th.sun-col {
    background: #991b1b;
}
.compact-calendar-grid tbody td {
    height: 54px;
    max-height: 54px;
    padding: 2px 3px;
    vertical-align: top;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
    position: relative;
    cursor: pointer;
    transition: background-color .15s;
    overflow: hidden;
}
.compact-calendar-grid tbody td:hover {
    background: #f1f5f9;
}
.compact-calendar-grid tbody td.outside-month {
    background: #f8fafc;
    cursor: default;
    opacity: .35;
}
.compact-calendar-grid tbody td.is-sunday {
    background: #fff8f8;
}
.compact-calendar-grid tbody td.is-today {
    box-shadow: inset 0 0 0 2px #0284c7;
    background: #f0f9ff;
}
.cell-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1px;
    line-height: 1;
}
.cell-day-num {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
}
.is-today .cell-day-num {
    color: #0284c7;
    font-weight: 800;
}
.is-sunday .cell-day-num {
    color: #dc2626;
}
.cell-add-btn {
    opacity: 0;
    font-size: 8.5px;
    padding: 1px 2px;
    border-radius: 2px;
    background: #002C54;
    color: #fff;
    border: none;
    line-height: 1;
    transition: opacity .15s;
}
.compact-calendar-grid tbody td:hover .cell-add-btn {
    opacity: .85;
}

/* Event Pill Inside Cell */
.cal-event-pill {
    background: #fee2e2;
    border-left: 2.5px solid #ef4444;
    color: #991b1b;
    padding: 1px 3px;
    border-radius: 2px;
    font-size: 9px;
    line-height: 1.15;
    margin-bottom: 1px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2px;
    overflow: hidden;
    cursor: pointer;
    transition: transform .1s;
}
.cal-event-pill:hover {
    background: #fecaca;
    transform: translateY(-1px);
}
.cal-event-title {
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}
.cal-event-del-btn {
    border: none;
    background: transparent;
    color: #b91c1c;
    padding: 0 1px;
    font-size: 9px;
    cursor: pointer;
    line-height: 1;
}
.cal-event-del-btn:hover {
    color: #7f1d1d;
}

/* Month Holidays List Card in Column 3 */
.month-holidays-scroll {
    overflow-y: auto;
    border: 1px solid #f1f5f9;
    border-radius: 2px;
    flex: 1 1 auto;
    min-height: 250px;
}
.month-holiday-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 7px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 11px;
}
.month-holiday-item:last-child {
    border-bottom: none;
}
.month-holiday-item:hover {
    background: #f8fafc;
}
.holiday-date-badge {
    background: #002C54;
    color: #fff;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 700;
    white-space: nowrap;
}

/* Bottom Full-Width Guidelines Strip (col-12) */
.guideline-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 10px;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    height: 100%;
}
.guideline-icon {
    font-size: 15px;
    line-height: 1;
    margin-top: 2px;
}
.guideline-title {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 2px;
}
.guideline-desc {
    font-size: 10px;
    color: #475569;
    line-height: 1.35;
}

/* Reset / Action Buttons High Contrast */
.btn-reset-light {
    background: #ffffff !important;
    color: #002C54 !important;
    font-weight: 700 !important;
    border: 1px solid #ffffff !important;
    font-size: 10px !important;
    padding: 1px 6px !important;
    border-radius: 2px !important;
}
.btn-reset-light:hover {
    background: #f1f5f9 !important;
    color: #001f3d !important;
}
</style>
@endsection

@section('content')
@php
    $monthStart = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1);
    $daysInMonth = $monthStart->daysInMonth;
    $leadingBlankDays = $monthStart->dayOfWeek;
    $previousMonth = $monthStart->copy()->subMonth();
    $nextMonth = $monthStart->copy()->addMonth();

    $flatEvents = collect($calendarEvents)->flatten(1);
    $totalHolidaysInMonth = $flatEvents->count();
@endphp

<div class="content-wrapper academic-calendar-page">
    <div class="container-fluid p-2">
        
        <!-- Hero Header -->
        <div class="admission-hero">
            <div>
                <span class="admission-kicker"><i class="fa fa-calendar-check-o mr-1"></i> ARIS MASTER MANAGEMENT</span>
                <h1 class="admission-title">Academic & Weekend Calendar</h1>
                <p class="admission-subtitle">Manage institutional holidays, session calendar, and automated mobile notifications</p>
            </div>
            <div class="admission-hero-actions">
                <button type="button" class="dash-btn dash-btn-light" id="btnFocusAddHoliday">
                    <i class="fa fa-plus-circle mr-1 text-primary"></i> Add Holiday
                </button>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show p-2 mb-2" role="alert" style="font-size:11.5px;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show p-2 mb-2" role="alert" style="font-size:11.5px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                <button type="button" class="close p-2" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- 3-Column Equal Layout: 1 Single Row -->
        <div class="row cal-row-equal">
            
            <!-- Column 1: Add / Edit Holiday Form -->
            <div class="col-lg-4 col-xl-4 cal-col-equal">
                <div class="cal-card">
                    <div class="cal-card-header">
                        <span id="formTitleText"><i class="fa fa-plus-circle mr-1"></i> Add Holiday / Event</span>
                        <button type="button" class="btn btn-reset-light" id="btnResetHolidayForm">
                            <i class="fa fa-refresh mr-1"></i> Reset
                        </button>
                    </div>
                    <form id="holidayForm" action="{{ url('add_weekend') }}" method="POST" class="cal-card-body">
                        @csrf
                        <input type="hidden" name="mode" id="form_mode" value="1">
                        <input type="hidden" name="event_id" id="event_id" value="">

                        <!-- Mode Toggle -->
                        <div class="date-mode-tabs">
                            <button type="button" class="date-mode-btn active" data-mode="single">
                                <i class="fa fa-calendar-o mr-1"></i> Single Day
                            </button>
                            <button type="button" class="date-mode-btn" data-mode="range">
                                <i class="fa fa-calendar mr-1"></i> Date Range
                            </button>
                        </div>

                        <!-- Date Pickers -->
                        <div class="row">
                            <div class="col-6">
                                <div class="cal-form-group">
                                    <label>From Date <span class="text-danger">*</span></label>
                                    <input type="date" id="from_date" name="from_date" class="cal-form-control" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="cal-form-group">
                                    <label>To Date <span class="text-danger">*</span></label>
                                    <input type="date" id="to_date" name="to_date" class="cal-form-control" value="{{ now()->toDateString() }}" required readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <div class="cal-form-group">
                            <label>Holiday / Event Name <span class="text-danger">*</span></label>
                            <input type="text" id="event_title" name="event_title" class="cal-form-control" placeholder="e.g. Diwali Holiday, Winter Break" required maxlength="200" autocomplete="off">
                        </div>

                        <div class="cal-form-group mb-1">
                            <label>Description (Optional)</label>
                            <textarea id="event_description" name="event_description" class="cal-form-control" rows="2" placeholder="Brief note or guidelines..." maxlength="500"></textarea>
                        </div>

                        <!-- Notification Panel Toggle -->
                        <div class="notification-toggle-box">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="auto_message_enabled" name="auto_message_enabled" value="1">
                                <label class="custom-control-label font-weight-bold text-dark" for="auto_message_enabled" style="font-size:11px; cursor:pointer;">
                                    <i class="fa fa-bell text-warning mr-1"></i> Broadcast App Notification
                                </label>
                            </div>

                            <div id="notificationDetails" style="display: none; margin-top: 6px;">
                                <div class="cal-form-group mb-1">
                                    <label style="font-size: 10px; color:#475569; font-weight:600;">Target Roles:</label>
                                    <div class="d-flex flex-wrap" style="gap: 3px;">
                                        @foreach($notificationRoles as $role)
                                            <label class="role-pill-checkbox checked" for="role_chk_{{ $role->id }}">
                                                <input type="checkbox" name="notification_role_ids[]" id="role_chk_{{ $role->id }}" value="{{ $role->id }}" checked style="display:none;" class="js-role-chk">
                                                <span>{{ $role->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="cal-form-group mb-0">
                                    <label style="font-size: 10px; color:#475569; font-weight:600;">Message Text:</label>
                                    <textarea id="auto_message_text" name="auto_message_text" class="cal-form-control" rows="2" placeholder="School will remain closed on scheduled holiday."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-auto pt-2">
                            <button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold" id="btnSubmitHoliday" style="background:#002C54; border-color:#002C54; font-size:11.5px; padding:6px 10px; color:#ffffff;">
                                <i class="fa fa-save mr-1"></i> Save Holiday
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Column 2: Compact ERP Calendar View -->
            <div class="col-lg-4 col-xl-4 cal-col-equal">
                <div class="cal-card">
                    
                    <!-- Month Navigation Toolbar with High Contrast -->
                    <div class="calendar-nav-toolbar">
                        <div class="d-flex align-items-center" style="gap: 3px;">
                            <a href="{{ url('add_weekend') }}?month={{ $previousMonth->month }}&year={{ $previousMonth->year }}" class="nav-arrow-btn" title="Previous Month">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                            <a href="{{ url('add_weekend') }}?month={{ now()->month }}&year={{ now()->year }}" class="dash-btn dash-btn-outline py-0 px-2" style="font-size:10px; height:24px; line-height:22px;">
                                Today
                            </a>
                            <a href="{{ url('add_weekend') }}?month={{ $nextMonth->month }}&year={{ $nextMonth->year }}" class="nav-arrow-btn" title="Next Month">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </div>

                        <!-- Month & Year Selectors with Readable White Options -->
                        <div class="d-flex align-items-center" style="gap: 4px;">
                            <select id="jumpMonthSelect" class="cal-form-control cal-select-dark" style="width: 100px;">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ $m == $calendarMonth ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endfor
                            </select>

                            <select id="jumpYearSelect" class="cal-form-control cal-select-dark" style="width: 65px;">
                                @for($y = date('Y') - 3; $y <= date('Y') + 4; $y++)
                                    <option value="{{ $y }}" {{ $y == $calendarYear ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Compact Calendar Table -->
                    <div class="table-responsive m-0 flex-grow-1" style="overflow-x:hidden;">
                        <table class="compact-calendar-grid">
                            <thead>
                                <tr>
                                    <th class="sun-col" style="width: 14.28%;">Sun</th>
                                    <th style="width: 14.28%;">Mon</th>
                                    <th style="width: 14.28%;">Tue</th>
                                    <th style="width: 14.28%;">Wed</th>
                                    <th style="width: 14.28%;">Thu</th>
                                    <th style="width: 14.28%;">Fri</th>
                                    <th style="width: 14.28%;">Sat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php $currentCell = 0; @endphp

                                    {{-- Leading Blank Days --}}
                                    @for($blank = 0; $blank < $leadingBlankDays; $blank++)
                                        <td class="outside-month"></td>
                                        @php $currentCell++; @endphp
                                    @endfor

                                    {{-- Days of Month --}}
                                    @for($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $dateKey = sprintf('%04d-%02d-%02d', $calendarYear, $calendarMonth, $day);
                                            $dayEvents = $calendarEvents[$dateKey] ?? [];
                                            $isToday = ($dateKey === now()->toDateString());
                                            $dayOfWeek = ($currentCell % 7);
                                            $isSunday = ($dayOfWeek === 0);
                                        @endphp

                                        @if($currentCell > 0 && $currentCell % 7 === 0)
                                            </tr><tr>
                                        @endif

                                        <td class="{{ $isToday ? 'is-today' : '' }} {{ $isSunday ? 'is-sunday' : '' }}"
                                            data-date="{{ $dateKey }}"
                                            onclick="handleCellClick('{{ $dateKey }}', event)">
                                            <div class="cell-top-bar">
                                                <span class="cell-day-num">{{ $day }}</span>
                                                <button type="button" class="cell-add-btn" title="Add holiday on {{ $dateKey }}" onclick="pickSingleDate('{{ $dateKey }}', event)">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>

                                            @foreach($dayEvents as $event)
                                                <div class="cal-event-pill" onclick="editHolidayEvent({{ $event['id'] }}, event)" title="{{ $event['event_title'] ?: $event['event_schedule'] }}">
                                                    <span class="cal-event-title">
                                                        <i class="fa fa-star text-danger mr-1" style="font-size:8px;"></i>
                                                        {{ $event['event_title'] ?: $event['event_schedule'] }}
                                                    </span>
                                                    @if(!empty($event['auto_message_enabled']))
                                                        <i class="fa fa-bell text-warning" style="font-size:8px;" title="Notification enabled"></i>
                                                    @endif
                                                    <button type="button" class="cal-event-del-btn" onclick="deleteHolidayEvent({{ $event['id'] }}, '{{ addslashes($event['event_title'] ?: $event['event_schedule']) }}', event)" title="Delete">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </td>
                                        @php $currentCell++; @endphp
                                    @endfor

                                    {{-- Trailing Blank Days to fill the last row --}}
                                    @while($currentCell % 7 !== 0)
                                        <td class="outside-month"></td>
                                        @php $currentCell++; @endphp
                                    @endwhile
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- Column 3: Month Holidays Schedule -->
            <div class="col-lg-4 col-xl-4 cal-col-equal">
                <div class="cal-card">
                    <div class="cal-card-header">
                        <span><i class="fa fa-list mr-1"></i> {{ $monthName }} {{ $calendarYear }} Schedule</span>
                        <span class="badge badge-light font-weight-bold" style="font-size:10px; color:#002C54;">{{ $totalHolidaysInMonth }} {{ Str::plural('Day', $totalHolidaysInMonth) }}</span>
                    </div>
                    
                    <div class="cal-card-body p-2 d-flex flex-column">
                        <!-- Holidays List Section -->
                        <div class="month-holidays-scroll">
                            @forelse(collect($calendarEvents)->sortKeys() as $dateKey => $events)
                                @foreach($events as $ev)
                                    <div class="month-holiday-item">
                                        <div class="d-flex align-items-center" style="gap:6px; overflow:hidden;">
                                            <span class="holiday-date-badge">{{ \Carbon\Carbon::parse($dateKey)->format('d M') }}</span>
                                            <span class="font-weight-semibold text-dark text-truncate" style="cursor:pointer; font-size:11px;" onclick="editHolidayEvent({{ $ev['id'] }})" title="Click to edit {{ $ev['event_title'] }}">
                                                {{ $ev['event_title'] ?: $ev['event_schedule'] }}
                                            </span>
                                        </div>
                                        <div class="d-inline-flex align-items-center" style="gap:2px;">
                                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-1" onclick="editHolidayEvent({{ $ev['id'] }})" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1" onclick="deleteHolidayEvent({{ $ev['id'] }}, '{{ addslashes($ev['event_title'] ?: $ev['event_schedule']) }}')" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <div class="text-center text-muted p-4" style="font-size:11px;">
                                    <i class="fa fa-calendar-times-o d-block mb-1 text-secondary" style="font-size:22px;"></i>
                                    No holidays scheduled in {{ $monthName }}.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Full-Width Row: Academic Calendar Notes & Guidelines (col-12) -->
        <div class="row mt-1">
            <div class="col-12">
                <div class="cal-card mb-0">
                    <div class="cal-card-header" style="background:#002C54;">
                        <span><i class="fa fa-info-circle mr-1"></i> Academic Calendar Notes & Guidelines</span>
                    </div>
                    <div class="cal-card-body p-2">
                        <div class="row" style="margin-left:-4px; margin-right:-4px;">
                            <div class="col-md-3 col-sm-6 p-1">
                                <div class="guideline-box">
                                    <div class="guideline-icon text-primary"><i class="fa fa-calendar-check-o"></i></div>
                                    <div>
                                        <div class="guideline-title">Single vs Date Range</div>
                                        <div class="guideline-desc">Use <strong>Single Day</strong> for 1-day events or <strong>Date Range</strong> for vacation periods (e.g., Summer / Winter breaks).</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 p-1">
                                <div class="guideline-box">
                                    <div class="guideline-icon text-success"><i class="fa fa-mouse-pointer"></i></div>
                                    <div>
                                        <div class="guideline-title">1-Click Date Selection</div>
                                        <div class="guideline-desc">Click any calendar cell or the <strong>+</strong> button directly on a date to auto-fill and select that date in the form.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 p-1">
                                <div class="guideline-box">
                                    <div class="guideline-icon text-warning"><i class="fa fa-bell"></i></div>
                                    <div>
                                        <div class="guideline-title">App Broadcast Alerts</div>
                                        <div class="guideline-desc">Enable <strong>Broadcast App Notification</strong> to automatically deliver instant push notices to Students, Parents & Staff.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 p-1">
                                <div class="guideline-box">
                                    <div class="guideline-icon text-danger"><i class="fa fa-calendar-minus-o"></i></div>
                                    <div>
                                        <div class="guideline-title">Sundays & Fast Edit</div>
                                        <div class="guideline-desc">Sundays are highlighted in red as routine weekly off-days. Click any holiday event badge to edit or delete.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fromInput = document.getElementById('from_date');
    const toInput = document.getElementById('to_date');
    const formMode = document.getElementById('form_mode');
    const eventIdInput = document.getElementById('event_id');
    const eventTitleInput = document.getElementById('event_title');
    const eventDescInput = document.getElementById('event_description');
    const autoMsgToggle = document.getElementById('auto_message_enabled');
    const autoMsgDetails = document.getElementById('notificationDetails');
    const autoMsgText = document.getElementById('auto_message_text');
    const formTitleText = document.getElementById('formTitleText');
    const btnSubmit = document.getElementById('btnSubmitHoliday');
    const holidayForm = document.getElementById('holidayForm');

    // Date Mode Tabs
    document.querySelectorAll('.date-mode-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.date-mode-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const mode = this.dataset.mode;
            if (mode === 'single') {
                toInput.readOnly = true;
                toInput.value = fromInput.value;
            } else {
                toInput.readOnly = false;
                toInput.min = fromInput.value;
                if (toInput.value < fromInput.value) {
                    toInput.value = fromInput.value;
                }
            }
        });
    });

    fromInput.addEventListener('change', function () {
        toInput.min = this.value;
        const isSingle = document.querySelector('.date-mode-btn.active').dataset.mode === 'single';
        if (isSingle || toInput.value < this.value) {
            toInput.value = this.value;
        }
    });

    // Notification Toggle
    autoMsgToggle.addEventListener('change', function () {
        autoMsgDetails.style.display = this.checked ? 'block' : 'none';
    });

    // Role checkbox visual toggle
    document.querySelectorAll('.role-pill-checkbox').forEach(label => {
        label.addEventListener('click', function (e) {
            const chk = this.querySelector('.js-role-chk');
            if (e.target !== chk) {
                chk.checked = !chk.checked;
            }
            if (chk.checked) this.classList.add('checked');
            else this.classList.remove('checked');
        });
    });

    // Reset Form
    document.getElementById('btnResetHolidayForm').addEventListener('click', resetHolidayForm);
    document.getElementById('btnFocusAddHoliday').addEventListener('click', function () {
        resetHolidayForm();
        eventTitleInput.focus();
    });

    function resetHolidayForm() {
        formMode.value = '1';
        eventIdInput.value = '';
        holidayForm.reset();
        autoMsgDetails.style.display = 'none';
        formTitleText.innerHTML = '<i class="fa fa-plus-circle mr-1"></i> Add Holiday / Event';
        btnSubmit.innerHTML = '<i class="fa fa-save mr-1"></i> Save Holiday';
        toInput.readOnly = true;
        document.querySelectorAll('.date-mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === 'single'));
        document.querySelectorAll('.role-pill-checkbox').forEach(lbl => {
            lbl.classList.add('checked');
            lbl.querySelector('.js-role-chk').checked = true;
        });
    }

    // Month & Year Selector Jump
    function navigateMonthYear() {
        const m = document.getElementById('jumpMonthSelect').value;
        const y = document.getElementById('jumpYearSelect').value;
        window.location.href = `{{ url('add_weekend') }}?month=${m}&year=${y}`;
    }
    document.getElementById('jumpMonthSelect').addEventListener('change', navigateMonthYear);
    document.getElementById('jumpYearSelect').addEventListener('change', navigateMonthYear);

    // Form Submit via AJAX
    holidayForm.addEventListener('submit', function (e) {
        e.preventDefault();
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin mr-1"></i> Saving...';

        const formData = new FormData(holidayForm);
        fetch(holidayForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa fa-save mr-1"></i> Save Holiday';
            if (data.ok) {
                if (typeof toastr !== 'undefined') toastr.success(data.message || 'Holiday saved successfully.');
                setTimeout(() => window.location.reload(), 400);
            } else {
                if (typeof toastr !== 'undefined') toastr.error(data.message || 'Failed to save holiday.');
                else alert(data.message || 'Failed to save holiday.');
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fa fa-save mr-1"></i> Save Holiday';
            if (typeof toastr !== 'undefined') toastr.error('An error occurred. Please check all fields.');
            else alert('An error occurred.');
        });
    });

    // Handle Day Cell Click
    window.handleCellClick = function (dateStr, event) {
        if (event.target.closest('.cal-event-pill') || event.target.closest('.cell-add-btn')) return;
        pickSingleDate(dateStr, event);
    };

    window.pickSingleDate = function (dateStr, event) {
        if (event) event.stopPropagation();
        fromInput.value = dateStr;
        toInput.value = dateStr;
        toInput.min = dateStr;
        toInput.readOnly = true;
        document.querySelectorAll('.date-mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === 'single'));
        eventTitleInput.focus();
    };

    // Edit Event
    window.editHolidayEvent = function (eventId, event) {
        if (event) event.stopPropagation();
        
        const params = new URLSearchParams();
        params.append('_token', '{{ csrf_token() }}');
        params.append('mode', '4');
        params.append('event_id', eventId);

        fetch(`{{ url('add_weekend') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: params.toString()
        })
        .then(res => res.json())
        .then(res => {
            if (res.ok && res.item) {
                const item = res.item;
                formMode.value = '2';
                eventIdInput.value = item.id;
                fromInput.value = item.from_date;
                toInput.value = item.to_date;
                toInput.min = item.from_date;
                
                const isSingle = (item.from_date === item.to_date);
                toInput.readOnly = isSingle;
                document.querySelectorAll('.date-mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === (isSingle ? 'single' : 'range')));

                eventTitleInput.value = item.event_title || '';
                eventDescInput.value = item.event_description || '';
                autoMsgToggle.checked = !!item.auto_message_enabled;
                autoMsgDetails.style.display = item.auto_message_enabled ? 'block' : 'none';
                autoMsgText.value = item.auto_message_text || '';

                const roleIds = (item.notification_role_ids || []).map(String);
                document.querySelectorAll('.js-role-chk').forEach(chk => {
                    const isChecked = roleIds.includes(String(chk.value));
                    chk.checked = isChecked;
                    chk.closest('.role-pill-checkbox').classList.toggle('checked', isChecked);
                });

                formTitleText.innerHTML = '<i class="fa fa-edit mr-1"></i> Edit Holiday';
                btnSubmit.innerHTML = '<i class="fa fa-check mr-1"></i> Update Holiday';
                eventTitleInput.focus();
            }
        })
        .catch(err => {
            if (typeof toastr !== 'undefined') toastr.error('Could not load holiday details.');
            else alert('Could not load holiday details.');
        });
    };

    // Delete Event
    window.deleteHolidayEvent = function (eventId, title, event) {
        if (event) event.stopPropagation();
        if (!confirm(`Are you sure you want to delete "${title}" from the academic calendar?`)) return;

        const params = new URLSearchParams();
        params.append('_token', '{{ csrf_token() }}');
        params.append('mode', '3');
        params.append('event_id', eventId);

        fetch(`{{ url('add_weekend') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: params.toString()
        })
        .then(res => res.json())
        .then(res => {
            if (res.ok) {
                if (typeof toastr !== 'undefined') toastr.success(res.message || 'Holiday deleted successfully.');
                setTimeout(() => window.location.reload(), 400);
            } else {
                if (typeof toastr !== 'undefined') toastr.error(res.message || 'Failed to delete holiday.');
                else alert(res.message || 'Failed to delete holiday.');
            }
        })
        .catch(err => {
            if (typeof toastr !== 'undefined') toastr.error('An error occurred while deleting.');
            else alert('An error occurred.');
        });
    };
});
</script>
@endsection
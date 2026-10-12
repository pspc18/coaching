@extends('layout.app')

@php
    $classType = Helper::ClassType();
    $setting = Helper::getSetting();
    $selectedClassId = $search['class_type_id'] ?? request('class_type_id', '');
    $selectedExamId = $search['exam_id'] ?? request('exam_id', '');

    $totalSubjects = count($data ?? []);
    $scheduledCount = 0;
    foreach ($data as $sub) {
        $sched = $schedules->get($sub->id);
        if ($sched && !empty($sched->date)) {
            $scheduledCount++;
        }
    }
    $pendingCount = max(0, $totalSubjects - $scheduledCount);
@endphp

@section('title', 'Examination Schedule - Arise ERP')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EXAMINATION SCHEDULE (ADMIN SIGNATURE THEME)
   Aligned with Exam Management, Notices & Admission Standards:
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Sky Blue (#0284c7)
   - Layout: Compact responsive viewport, sharp border-radius (2px-3px)
   - Dual View: Modern interactive table for desktop, optimized card stack for mobile
   - Real-time updates with live KPI counters, instant filter, bulk time copy
   ========================================================================== */

.schedule-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
}
.schedule-page * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner */
.sched-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.14);
    margin-bottom: 8px;
}
.sched-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 700;
}
.sched-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.sched-subtitle {
    font-size: 11px;
    margin: 1px 0 0;
    color: #cbd5e1;
}
.sched-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

/* Button Utilities */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 28px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
    white-space: nowrap;
    line-height: 1.3;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #fff !important;
    border-color: rgba(255,255,255,.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.18);
    color: #fff !important;
}
.dash-btn-primary {
    background: #0284c7;
    color: #fff !important;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
    color: #fff !important;
}

/* 2. Fast KPI Summary Filter Strip */
.sched-kpi-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
@media (max-width: 768px) {
    .sched-kpi-strip {
        grid-template-columns: 1fr;
    }
}
.kpi-mini-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    border-left: 3px solid #002C54;
    user-select: none;
}
.kpi-mini-card.kpi-scheduled { border-left-color: #10b981; }
.kpi-mini-card.kpi-pending { border-left-color: #f59e0b; }

.kpi-mini-card .kpi-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 1px;
}
.kpi-mini-card .kpi-val {
    font-size: 16px;
    font-weight: 800;
    line-height: 1.1;
    color: #1e293b;
}
.kpi-mini-card .kpi-icon {
    width: 28px;
    height: 28px;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

/* 3. Control & Filter Box */
.sched-control-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px 14px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,.03);
}
.control-grid {
    display: grid;
    grid-template-columns: 1.2fr 1.5fr auto;
    gap: 12px;
    align-items: flex-end;
}
@media (max-width: 768px) {
    .control-grid {
        grid-template-columns: 1fr;
    }
}
.form-group-custom {
    margin-bottom: 0;
}
.form-label-custom {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 4px;
}
.form-label-custom .req {
    color: #ef4444;
}
.form-control-custom, .form-select-custom {
    width: 100%;
    height: 32px;
    padding: 4px 9px;
    font-size: 12px;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease-in-out;
}
.form-control-custom:focus, .form-select-custom:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}

/* 4. Schedule Table Card */
.sched-table-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    overflow: hidden;
}
.sched-card-header {
    background: #002342;
    color: #ffffff;
    padding: 7px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.sched-card-title {
    font-size: 12.5px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
}

/* Bulk Timing Toolbar */
.bulk-timing-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.bulk-timing-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.bulk-time-label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
}
.bulk-time-input {
    height: 28px;
    font-size: 11.5px;
    padding: 2px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
}

/* Modern Table */
.sched-table-wrap {
    overflow-x: auto;
}
.sched-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
}
.sched-table thead th {
    background: #f1f5f9;
    color: #334155;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 8px 10px;
    border-bottom: 2px solid #cbd5e1;
    border-top: none;
    white-space: nowrap;
}
.sched-table tbody td {
    padding: 7px 10px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: middle;
    font-size: 12px;
}
.sched-table tbody tr:hover {
    background: #f8fafc;
}
.sched-input {
    width: 100%;
    height: 30px;
    padding: 2px 8px;
    font-size: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #0f172a;
    transition: all .12s ease;
}
.sched-input:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
    outline: none;
}

/* Status Pill */
.badge-pill-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.badge-pill-status.scheduled {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.badge-pill-status.pending {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}

/* 5. Mobile Cards View (Hidden on desktop) */
.sched-mobile-cards {
    display: none;
    padding: 10px;
    gap: 8px;
    flex-direction: column;
}
@media (max-width: 768px) {
    .sched-table-wrap {
        display: none;
    }
    .sched-mobile-cards {
        display: flex;
    }
}
.sched-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.sched-mob-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.sched-mob-title {
    font-size: 13px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
}
.sched-mob-fields {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.sched-mob-times {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

/* Footer Action Bar */
.sched-card-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.exam-center-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    max-width: 480px;
}
@media (max-width: 768px) {
    .exam-center-wrap {
        max-width: 100%;
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Empty State Box */
.sched-empty-box {
    text-align: center;
    padding: 36px 16px;
    background: #ffffff;
    color: #64748b;
}
.sched-empty-icon {
    font-size: 32px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.sched-empty-title {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px;
}
.sched-empty-desc {
    font-size: 12px;
    margin: 0;
}
</style>
@endsection

@section('content')
<div class="content-wrapper schedule-page">

    {{-- 1. Top Hero Banner --}}
    <div class="sched-hero">
        <div>
            <span class="sched-kicker"><i class="fa fa-graduation-cap"></i> Examination Control</span>
            <h1 class="sched-title">
                <i class="fa fa-calendar-check-o"></i> Examination Schedule
            </h1>
            <p class="sched-subtitle">
                Configure subject exam dates and timings in real-time. Changes sync directly with student &amp; admit card schedules.
            </p>
        </div>
        <div class="sched-hero-actions">
            <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="View Offline Exams">
                <i class="fa fa-leanpub"></i> Exams List
            </a>
            <a href="{{ url('download_admit_card') }}" class="dash-btn dash-btn-outline" title="Download Student Admit Cards">
                <i class="fa fa-id-card-o"></i> Admit Cards
            </a>
        </div>
    </div>

    {{-- 2. KPI Summary Filter Strip --}}
    <div class="sched-kpi-strip">
        <div class="kpi-mini-card">
            <div>
                <div class="kpi-label">Total Subjects</div>
                <div class="kpi-val" id="statTotalVal">{{ $totalSubjects }}</div>
            </div>
            <div class="kpi-icon" style="background:#f1f5f9; color:#002C54;">
                <i class="fa fa-book"></i>
            </div>
        </div>
        <div class="kpi-mini-card kpi-scheduled">
            <div>
                <div class="kpi-label">Scheduled</div>
                <div class="kpi-val" id="statScheduledVal" style="color: #15803d;">{{ $scheduledCount }}</div>
            </div>
            <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                <i class="fa fa-check-circle"></i>
            </div>
        </div>
        <div class="kpi-mini-card kpi-pending">
            <div>
                <div class="kpi-label">Pending Dates</div>
                <div class="kpi-val" id="statPendingVal" style="color: #b45309;">{{ $pendingCount }}</div>
            </div>
            <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>

    {{-- 3. Selection & Filter Card --}}
    <div class="sched-control-box">
        <form id="filterScheduleForm" method="GET" action="{{ url('add/examination_schedule') }}">
            <div class="control-grid">
                {{-- Class Selection --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Class <span class="req">*</span>
                    </label>
                    <select class="form-select-custom select2" id="class_type_id" name="class_type_id" required>
                        <option value="">-- Select Class --</option>
                        @if(!empty($classType))
                            @foreach($classType as $type)
                                <option value="{{ $type->id }}" {{ (string)$type->id === (string)$selectedClassId ? 'selected' : '' }}>
                                    {{ $type->name ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Exam Selection --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Examination <span class="req">*</span>
                    </label>
                    <select class="form-select-custom select2" id="exam_id" name="exam_id" required>
                        <option value="">-- Select Examination --</option>
                        @if(!empty($exam))
                            @foreach($exam as $item)
                                @php
                                    $eDate = !empty($item->assign_exam_date) ? ' ('.date('d-m-Y', strtotime($item->assign_exam_date)).')' : '';
                                @endphp
                                <option value="{{ $item->exam_id }}" {{ (string)$item->exam_id === (string)$selectedExamId ? 'selected' : '' }}>
                                    {{ $item->exam_name ?? '' }}{{ $eDate }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex; gap: 6px;">
                    <button type="button" class="dash-btn dash-btn-primary" id="btnFetchSchedule" title="Load Schedule Instantly">
                        <i class="fa fa-search"></i> Load Schedule
                    </button>
                    <button type="button" class="dash-btn dash-btn-outline" id="btnResetFilters" style="background:#f1f5f9; color:#475569 !important; border-color:#cbd5e1;" title="Reset selection">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- 4. Schedule Table & Form --}}
    <div class="sched-table-card">
        <div class="sched-card-header">
            <h2 class="sched-card-title">
                <i class="fa fa-table"></i> Subject Date Sheet
                <span id="badgeClassExamTag" style="font-size: 11px; font-weight: 500; opacity: 0.85;"></span>
            </h2>
            <div style="font-size: 11px; opacity: 0.85;">
                <i class="fa fa-info-circle"></i> Clear date field to remove any subject from schedule.
            </div>
        </div>

        {{-- Bulk Timing Helper Bar --}}
        <div class="bulk-timing-bar" id="bulkTimingBar" style="{{ count($data) > 0 ? '' : 'display:none;' }}">
            <div class="bulk-timing-wrap">
                <span class="bulk-time-label"><i class="fa fa-clock-o"></i> Fast Set Common Timings:</span>
                <input type="time" class="bulk-time-input" id="bulkFromTime" value="09:00" title="Start Time">
                <span style="color:#94a3b8;">to</span>
                <input type="time" class="bulk-time-input" id="bulkToTime" value="12:00" title="End Time">
                <button type="button" class="dash-btn dash-btn-outline" id="btnApplyBulkTimes" style="height:28px; background:#e0f2fe; color:#0369a1 !important; border-color:#bae6fd;">
                    <i class="fa fa-copy"></i> Apply to All Subjects
                </button>
            </div>
            <div>
                <button type="button" class="dash-btn dash-btn-outline" id="btnClearAllDates" style="height:28px; background:#fff1f2; color:#be123c !important; border-color:#fecdd3;">
                    <i class="fa fa-eraser"></i> Clear All Dates
                </button>
            </div>
        </div>

        {{-- Form Container --}}
        <form id="formSubmitSchedule" action="{{ url('SubmitSchedule') }}" method="POST">
            @csrf
            <input type="hidden" name="class_type_id" id="hidden_class_type_id" value="{{ $selectedClassId }}">
            <input type="hidden" name="exam_id" id="hidden_exam_id" value="{{ $selectedExamId }}">

            {{-- DESKTOP VIEW: Data Table --}}
            <div class="sched-table-wrap">
                <table class="sched-table" id="scheduleTable">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">#</th>
                            <th style="min-width: 200px;">Subject Name</th>
                            <th style="width: 180px;">Examination Date</th>
                            <th style="width: 150px;">From Time</th>
                            <th style="width: 150px;">To Time</th>
                            <th style="width: 130px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        @if(count($data) > 0)
                            @foreach($data as $index => $item)
                                @php
                                    $sched = $schedules->get($item->id);
                                    $hasDate = ($sched && !empty($sched->date));
                                @endphp
                                <tr class="sched-row" data-sub-id="{{ $item->id }}">
                                    <td style="text-align: center; color: #64748b; font-weight: 700;">
                                        {{ $index + 1 }}
                                        <input type="hidden" name="subject_id[]" value="{{ $item->id }}">
                                        <input type="hidden" name="examination_schedule_id[]" value="{{ $sched->id ?? '' }}">
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #002C54;">{{ $item->name ?? '' }}</div>
                                    </td>
                                    <td>
                                        <input type="date"
                                               name="date[]"
                                               class="sched-input sched-input-date"
                                               value="{{ $sched->date ?? '' }}"
                                               data-index="{{ $index }}">
                                    </td>
                                    <td>
                                        <input type="time"
                                               name="from_time[]"
                                               class="sched-input sched-input-from"
                                               value="{{ $sched->from_time ?? '' }}"
                                               data-index="{{ $index }}">
                                    </td>
                                    <td>
                                        <input type="time"
                                               name="to_time[]"
                                               class="sched-input sched-input-to"
                                               value="{{ $sched->to_time ?? '' }}"
                                               data-index="{{ $index }}">
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="badge-pill-status {{ $hasDate ? 'scheduled' : 'pending' }} status-pill-{{ $index }}">
                                            <i class="fa {{ $hasDate ? 'fa-check' : 'fa-clock-o' }}"></i>
                                            {{ $hasDate ? 'Scheduled' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="emptyTableRow">
                                <td colspan="6" style="padding: 0;">
                                    <div class="sched-empty-box">
                                        <div class="sched-empty-icon"><i class="fa fa-calendar-times-o"></i></div>
                                        <h3 class="sched-empty-title">No Subjects Found</h3>
                                        <p class="sched-empty-desc">
                                            Please select a <b>Class</b> and an <b>Examination</b> above to view or configure the schedule.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW: Card Stack --}}
            <div class="sched-mobile-cards" id="scheduleMobileCards">
                @if(count($data) > 0)
                    @foreach($data as $index => $item)
                        @php
                            $sched = $schedules->get($item->id);
                            $hasDate = ($sched && !empty($sched->date));
                        @endphp
                        <div class="sched-mob-card" data-sub-id="{{ $item->id }}">
                            <div class="sched-mob-head">
                                <h4 class="sched-mob-title">
                                    <span style="color:#64748b;">#{{ $index + 1 }}.</span> {{ $item->name ?? '' }}
                                </h4>
                                <span class="badge-pill-status {{ $hasDate ? 'scheduled' : 'pending' }} status-pill-mob-{{ $index }}">
                                    {{ $hasDate ? 'Scheduled' : 'Pending' }}
                                </span>
                            </div>
                            <div class="sched-mob-fields">
                                <div>
                                    <label class="form-label-custom">Exam Date</label>
                                    <input type="date"
                                           name="date_mob[]"
                                           class="sched-input sched-input-date-mob"
                                           value="{{ $sched->date ?? '' }}"
                                           data-index="{{ $index }}">
                                </div>
                                <div class="sched-mob-times">
                                    <div>
                                        <label class="form-label-custom">From</label>
                                        <input type="time"
                                               name="from_time_mob[]"
                                               class="sched-input sched-input-from-mob"
                                               value="{{ $sched->from_time ?? '' }}"
                                               data-index="{{ $index }}">
                                    </div>
                                    <div>
                                        <label class="form-label-custom">To</label>
                                        <input type="time"
                                               name="to_time_mob[]"
                                               class="sched-input sched-input-to-mob"
                                               value="{{ $sched->to_time ?? '' }}"
                                               data-index="{{ $index }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="sched-empty-box" id="emptyMobileBox">
                        <div class="sched-empty-icon"><i class="fa fa-calendar-times-o"></i></div>
                        <h3 class="sched-empty-title">No Subjects Found</h3>
                        <p class="sched-empty-desc">
                            Select a Class and Examination above to load schedule cards.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Footer Controls (Save) --}}
            <div class="sched-card-footer" id="scheduleFooter" style="{{ count($data) > 0 ? '' : 'display:none;' }}">
                <div class="text-muted" style="font-size:11px;">
                    <i class="fa fa-info-circle text-primary"></i> Leaving exam date empty for any subject clears its schedule.
                </div>
                <div style="display:flex; gap: 8px;">
                    <button type="submit" class="dash-btn dash-btn-primary" id="btnSubmitSchedule" style="height:32px; font-size:12px; font-weight:700; padding:0 18px;">
                        <i class="fa fa-check-circle"></i> Save Schedule
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var baseurl = "{{ url('/') }}";

    // 1. Dynamic Exams load on Class Change
    $('#class_type_id').on('change', function () {
        var classId = $(this).val();
        var $examSelect = $('#exam_id');
        $examSelect.html('<option value="">Loading examinations...</option>');

        if (!classId) {
            $examSelect.html('<option value="">-- Select Examination --</option>');
            clearScheduleUI();
            return;
        }

        $.ajax({
            headers: {'X-CSRF-TOKEN': csrfToken},
            url: baseurl + '/examData/' + classId,
            success: function (data) {
                $examSelect.html(data);
                // If previous exam still in options, keep it, else prompt
                var selectedExam = "{{ $selectedExamId }}";
                if (selectedExam && $examSelect.find('option[value="' + selectedExam + '"]').length > 0) {
                    $examSelect.val(selectedExam);
                }
            },
            error: function () {
                $examSelect.html('<option value="">-- Failed to load exams --</option>');
                toastr.error('Unable to fetch examinations for selected class.');
            }
        });
    });

    // 2. Real-Time Fetch Schedule Function
    function fetchScheduleRealTime() {
        var classId = $('#class_type_id').val();
        var examId = $('#exam_id').val();

        if (!classId) {
            toastr.warning('Please select a Class.');
            return;
        }
        if (!examId) {
            toastr.warning('Please select an Examination.');
            return;
        }

        var $btn = $('#btnFetchSchedule');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

        $.ajax({
            url: baseurl + '/get_examination_schedule_data',
            type: 'POST',
            data: {
                _token: csrfToken,
                class_type_id: classId,
                exam_id: examId
            },
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Load Schedule');

                if (res.status === 'success' && res.data && res.data.length > 0) {
                    renderScheduleUI(res.data, res.stats);
                    toastr.success('Schedule loaded successfully.');
                } else {
                    renderEmptyUI();
                    toastr.info('No subjects found assigned to this class.');
                }
            },
            error: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-search"></i> Load Schedule');
                toastr.error('Failed to fetch schedule data from server.');
            }
        });
    }

    $('#btnFetchSchedule').on('click', fetchScheduleRealTime);

    // 3. Render Schedule UI (Desktop Table + Mobile Cards)
    function renderScheduleUI(rows, stats) {
        var classId = $('#class_type_id').val();
        var examId = $('#exam_id').val();
        $('#hidden_class_type_id').val(classId);
        $('#hidden_exam_id').val(examId);

        // Update KPIs
        if (stats) {
            $('#statTotalVal').text(stats.total);
            $('#statScheduledVal').text(stats.scheduled);
            $('#statPendingVal').text(stats.pending);
        }

        var tableHtml = '';
        var cardsHtml = '';

        rows.forEach(function (row, idx) {
            var hasDate = (row.date && row.date.trim() !== '');
            var statusBadge = hasDate
                ? '<span class="badge-pill-status scheduled status-pill-' + idx + '"><i class="fa fa-check"></i> Scheduled</span>'
                : '<span class="badge-pill-status pending status-pill-' + idx + '"><i class="fa fa-clock-o"></i> Pending</span>';

            var statusBadgeMob = hasDate
                ? '<span class="badge-pill-status scheduled status-pill-mob-' + idx + '"><i class="fa fa-check"></i> Scheduled</span>'
                : '<span class="badge-pill-status pending status-pill-mob-' + idx + '"><i class="fa fa-clock-o"></i> Pending</span>';

            // Desktop Row
            tableHtml += '<tr class="sched-row" data-sub-id="' + row.subject_id + '">' +
                '<td style="text-align: center; color: #64748b; font-weight: 700;">' +
                    row.s_no +
                    '<input type="hidden" name="subject_id[]" value="' + row.subject_id + '">' +
                    '<input type="hidden" name="examination_schedule_id[]" value="' + (row.schedule_id || '') + '">' +
                '</td>' +
                '<td><div style="font-weight: 700; color: #002C54;">' + row.subject_name + '</div></td>' +
                '<td><input type="date" name="date[]" class="sched-input sched-input-date" value="' + (row.date || '') + '" data-index="' + idx + '"></td>' +
                '<td><input type="time" name="from_time[]" class="sched-input sched-input-from" value="' + (row.from_time || '') + '" data-index="' + idx + '"></td>' +
                '<td><input type="time" name="to_time[]" class="sched-input sched-input-to" value="' + (row.to_time || '') + '" data-index="' + idx + '"></td>' +
                '<td style="text-align: center;">' + statusBadge + '</td>' +
            '</tr>';

            // Mobile Card
            cardsHtml += '<div class="sched-mob-card" data-sub-id="' + row.subject_id + '">' +
                '<div class="sched-mob-head">' +
                    '<h4 class="sched-mob-title"><span style="color:#64748b;">#' + row.s_no + '.</span> ' + row.subject_name + '</h4>' +
                    statusBadgeMob +
                '</div>' +
                '<div class="sched-mob-fields">' +
                    '<div>' +
                        '<label class="form-label-custom">Exam Date</label>' +
                        '<input type="date" name="date_mob[]" class="sched-input sched-input-date-mob" value="' + (row.date || '') + '" data-index="' + idx + '">' +
                    '</div>' +
                    '<div class="sched-mob-times">' +
                        '<div>' +
                            '<label class="form-label-custom">From</label>' +
                            '<input type="time" name="from_time_mob[]" class="sched-input sched-input-from-mob" value="' + (row.from_time || '') + '" data-index="' + idx + '">' +
                        '</div>' +
                        '<div>' +
                            '<label class="form-label-custom">To</label>' +
                            '<input type="time" name="to_time_mob[]" class="sched-input sched-input-to-mob" value="' + (row.to_time || '') + '" data-index="' + idx + '">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        });

        $('#scheduleTableBody').html(tableHtml);
        $('#scheduleMobileCards').html(cardsHtml);
        $('#bulkTimingBar').show();
        $('#scheduleFooter').show();
    }

    function renderEmptyUI() {
        clearScheduleUI();
        $('#statTotalVal').text('0');
        $('#statScheduledVal').text('0');
        $('#statPendingVal').text('0');
    }

    function clearScheduleUI() {
        $('#scheduleTableBody').html(
            '<tr id="emptyTableRow"><td colspan="6" style="padding: 0;">' +
                '<div class="sched-empty-box">' +
                    '<div class="sched-empty-icon"><i class="fa fa-calendar-times-o"></i></div>' +
                    '<h3 class="sched-empty-title">No Subjects Found</h3>' +
                    '<p class="sched-empty-desc">Please select a <b>Class</b> and an <b>Examination</b> above to view or configure the schedule.</p>' +
                '</div>' +
            '</td></tr>'
        );
        $('#scheduleMobileCards').html(
            '<div class="sched-empty-box">' +
                '<div class="sched-empty-icon"><i class="fa fa-calendar-times-o"></i></div>' +
                '<h3 class="sched-empty-title">No Subjects Found</h3>' +
                '<p class="sched-empty-desc">Select a Class and Examination above to load schedule cards.</p>' +
            '</div>'
        );
        $('#bulkTimingBar').hide();
        $('#scheduleFooter').hide();
    }

    // 4. Live Sync between Desktop & Mobile inputs and real-time status pill
    $(document).on('input change', '.sched-input-date', function () {
        var idx = $(this).data('index');
        var val = $(this).val();
        $('.sched-input-date-mob[data-index="' + idx + '"]').val(val);
        updateRowStatusPill(idx, val);
        recalcKpiCounters();
    });

    $(document).on('input change', '.sched-input-date-mob', function () {
        var idx = $(this).data('index');
        var val = $(this).val();
        $('.sched-input-date[data-index="' + idx + '"]').val(val);
        updateRowStatusPill(idx, val);
        recalcKpiCounters();
    });

    $(document).on('input change', '.sched-input-from', function () {
        var idx = $(this).data('index');
        $('.sched-input-from-mob[data-index="' + idx + '"]').val($(this).val());
    });
    $(document).on('input change', '.sched-input-from-mob', function () {
        var idx = $(this).data('index');
        $('.sched-input-from[data-index="' + idx + '"]').val($(this).val());
    });

    $(document).on('input change', '.sched-input-to', function () {
        var idx = $(this).data('index');
        $('.sched-input-to-mob[data-index="' + idx + '"]').val($(this).val());
    });
    $(document).on('input change', '.sched-input-to-mob', function () {
        var idx = $(this).data('index');
        $('.sched-input-to[data-index="' + idx + '"]').val($(this).val());
    });

    function updateRowStatusPill(idx, dateVal) {
        var hasDate = (dateVal && dateVal.trim() !== '');
        var pillD = $('.status-pill-' + idx);
        var pillM = $('.status-pill-mob-' + idx);

        if (hasDate) {
            pillD.removeClass('pending').addClass('scheduled').html('<i class="fa fa-check"></i> Scheduled');
            pillM.removeClass('pending').addClass('scheduled').html('Scheduled');
        } else {
            pillD.removeClass('scheduled').addClass('pending').html('<i class="fa fa-clock-o"></i> Pending');
            pillM.removeClass('scheduled').addClass('pending').html('Pending');
        }
    }

    function recalcKpiCounters() {
        var total = $('.sched-input-date').length;
        var scheduled = 0;
        $('.sched-input-date').each(function () {
            if ($(this).val().trim() !== '') {
                scheduled++;
            }
        });
        $('#statTotalVal').text(total);
        $('#statScheduledVal').text(scheduled);
        $('#statPendingVal').text(Math.max(0, total - scheduled));
    }

    // 5. Bulk Time Setter Action
    $('#btnApplyBulkTimes').on('click', function () {
        var fromVal = $('#bulkFromTime').val();
        var toVal = $('#bulkToTime').val();

        if (!fromVal || !toVal) {
            toastr.warning('Please specify both Start and End time in bulk toolbar.');
            return;
        }

        $('.sched-input-from').val(fromVal).trigger('change');
        $('.sched-input-to').val(toVal).trigger('change');
        toastr.success('Applied ' + fromVal + ' to ' + toVal + ' across all subjects.');
    });

    // 6. Clear All Dates Action
    $('#btnClearAllDates').on('click', function () {
        if (confirm('Are you sure you want to clear examination dates for all subjects?')) {
            $('.sched-input-date').val('').trigger('change');
            toastr.info('All dates cleared. Click "Save Schedule" to commit changes.');
        }
    });

    // 7. Reset Filters Button
    $('#btnResetFilters').on('click', function () {
        $('#class_type_id').val('').trigger('change');
        $('#exam_id').html('<option value="">-- Select Examination --</option>').val('').trigger('change');
        renderEmptyUI();
    });

    // 8. Submit Schedule via AJAX with real-time feedback
    $('#formSubmitSchedule').on('submit', function (e) {
        e.preventDefault();

        var classId = $('#hidden_class_type_id').val() || $('#class_type_id').val();
        var examId = $('#hidden_exam_id').val() || $('#exam_id').val();

        if (!classId || !examId) {
            toastr.error('Please select Class and Examination before saving.');
            return;
        }

        var $btn = $('#btnSubmitSchedule');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving Schedule...');

        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save Schedule');
                if (res.status === 'success') {
                    toastr.success(res.message || 'Schedule updated successfully!');
                    recalcKpiCounters();
                } else {
                    toastr.error(res.message || 'Error occurred while saving.');
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Save Schedule');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error while saving schedule.';
                toastr.error(msg);
            }
        });
    });
});
</script>
@endsection

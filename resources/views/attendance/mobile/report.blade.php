@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $totalCount = count($rows ?? []);
    $presentTotal = $totals['in'] ?? 0;
    $outTotal = $totals['out'] ?? 0;
    $absentTotal = $totals['absent'] ?? 0;
    $halfdayTotal = $totals['halfday'] ?? 0;
    $holidayTotal = $totals['holiday'] ?? 0;
    $grandTotal = $totals['total'] ?? 0;

    $exportParams = [
        'export' => 1,
        'tab' => $activeTab ?? 'students',
        'report_mode' => $reportMode ?? 'day_wise',
        'date' => $selectedDate,
        'month' => $month,
        'year' => $year,
    ];
    if (($activeTab ?? 'students') === 'staff' && !empty($roleFilter)) {
        $exportParams['role_id'] = $roleFilter;
    }
    if (($activeTab ?? 'students') === 'students' && !empty($classFilter)) {
        $exportParams['class_type_id'] = $classFilter;
    }
    $exportUrl = url('attendance/report') . '?' . http_build_query($exportParams);

    $hasActiveFilter = !empty($classFilter) || !empty($roleFilter) || ($reportMode ?? 'day_wise') === 'monthly';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* 1. Mobile Hero Card */
.mob-report-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-report-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-report-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-date-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* Tab Switcher in Hero */
.mob-hero-tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-hero-tab {
    flex: 1;
    text-align: center;
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 3px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    transition: all 0.15s ease;
}
.mob-hero-tab.active {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

/* Dual Metrics / KPI Grid */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 6px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 6px;
    text-align: center;
}
.mob-kpi-tag {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-kpi-val {
    font-size: 13px;
    font-weight: 900;
    line-height: 1;
}

/* Top Quick Action Bar */
.mob-action-row {
    display: flex;
    gap: 6px;
    margin-top: 6px;
}
.mob-hero-btn {
    flex: 1;
    height: 27px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-export-csv {
    background: #10b981;
    color: #ffffff;
    border: 1px solid #059669;
}
.btn-export-csv:hover {
    background: #059669;
    color: #ffffff;
}
.btn-open-filter {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. Search Box */
.mob-search-wrap {
    position: relative;
    margin-bottom: 8px;
}
.mob-search-input {
    width: 100%;
    height: 34px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding-left: 32px;
    padding-right: 12px;
    font-size: 12px;
    color: #1e293b;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-search-input:focus {
    outline: none;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.mob-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #64748b;
    pointer-events: none;
}

/* 3. Record Cards */
.mob-records-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 12px;
}
.mob-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    padding: 8px 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-person-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-uid-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #0f172a;
    font-family: monospace;
}
.mob-counts-row {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 5px;
    align-items: center;
}
.mob-pill-in { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 2px; }
.mob-pill-out { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 2px; }
.mob-pill-absent { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 2px; }
.mob-pill-halfday { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 2px; }
.mob-pill-holiday { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 2px; }
.mob-pill-total { background: #e2e8f0; color: #1e293b; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 800; padding: 1px 6px; border-radius: 2px; }

/* Filter Bottom Sheet Modal */
.mob-sheet-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    z-index: 1050;
    align-items: flex-end;
    backdrop-filter: blur(2px);
}
.mob-sheet-modal.show {
    display: flex;
}
.mob-sheet-content {
    background: #ffffff;
    width: 100%;
    max-height: 85vh;
    border-radius: 12px 12px 0 0;
    padding: 14px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
}
.mob-sheet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-sheet-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
}
.mob-sheet-close {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    padding: 0 4px;
}
</style>
@endsection

@section('content')
<div class="px-2 pt-2 pb-5">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-absent-hero mob-report-hero">
        <div class="mob-report-top">
            <div class="mob-report-title">
                <i class="fa fa-bar-chart"></i> Attendance Report
            </div>
            <span class="mob-date-pill">
                @if(($reportMode ?? 'day_wise') === 'monthly')
                    {{ date('M Y', mktime(0,0,0,$month,1,$year)) }}
                @else
                    {{ date('d M Y', strtotime($selectedDate)) }}
                @endif
            </span>
        </div>

        {{-- Entity Switcher Tabs --}}
        <div class="mob-hero-tabs">
            <a href="{{ url('attendance/report?tab=students&report_mode='.$reportMode.'&date='.$selectedDate.'&month='.$month.'&year='.$year) }}" 
               class="mob-hero-tab {{ ($activeTab ?? 'students') === 'students' ? 'active' : '' }}">
                <i class="fa fa-graduation-cap"></i> Students
            </a>
            @if($canAccessStaffAttendance ?? false)
            <a href="{{ url('attendance/report?tab=staff&report_mode='.$reportMode.'&date='.$selectedDate.'&month='.$month.'&year='.$year) }}" 
               class="mob-hero-tab {{ ($activeTab ?? 'students') === 'staff' ? 'active' : '' }}">
                <i class="fa fa-briefcase"></i> Staff
            </a>
            @endif
        </div>

        {{-- KPI Quick Metrics Strip --}}
        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Present</div>
                <div class="mob-kpi-val" style="color: #4ade80;">{{ $presentTotal }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Absent</div>
                <div class="mob-kpi-val" style="color: #f87171;">{{ $absentTotal }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Total Enrolled</div>
                <div class="mob-kpi-val" style="color: #ffffff;">{{ $totalCount }}</div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="mob-action-row">
            <a href="{{ $exportUrl }}" class="mob-hero-btn btn-export-csv">
                <i class="fa fa-file-excel-o"></i> Export CSV
            </a>
            <button type="button" class="mob-hero-btn btn-open-filter" id="btnOpenFilterSheet">
                <i class="fa fa-filter"></i> Filters @if($hasActiveFilter)<span class="badge badge-warning ml-1" style="font-size:9px; padding:1px 4px;">•</span>@endif
            </button>
        </div>
    </div>

    {{-- 2. Real-Time Search Bar --}}
    <div class="mob-search-wrap">
        <i class="fa fa-search mob-search-icon"></i>
        <input type="text" id="mobCardSearch" class="mob-search-input" placeholder="Search by name, unique ID..." autocomplete="off">
    </div>

    {{-- Result Counter --}}
    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
        <span style="font-size:11px; font-weight:700; color:#64748b;">
            Records (<span id="mobVisibleCount" class="text-dark">{{ $totalCount }}</span> / {{ $totalCount }})
        </span>
        @if(($reportMode ?? 'day_wise') === 'monthly')
            <span class="badge badge-info" style="font-size:10px; border-radius:2px;">Monthly Mode</span>
        @else
            <span class="badge badge-light border text-muted" style="font-size:10px; border-radius:2px;">Day-Wise</span>
        @endif
    </div>

    {{-- 3. Records List --}}
    <div class="mob-records-list" id="mobCardsContainer">
        @forelse($rows as $row)
            <div class="mob-card mob-person-item" 
                 data-id="{{ strtolower($row['unique_id']) }}" 
                 data-name="{{ strtolower($row['name']) }}">
                
                <div class="mob-card-head">
                    <div class="mob-person-title">
                        <div style="width:22px; height:22px; border-radius:2px; background:#002C54; color:#fff; display:flex; align-items:center; justify-content:center; font-size:9.5px; font-weight:800;">
                            {{ strtoupper(substr($row['name'] ?? 'U', 0, 1)) }}
                        </div>
                        <span>{{ $row['name'] }}</span>
                    </div>
                    <div class="d-flex align-items-center" style="gap:5px;">
                        <span class="mob-uid-badge">{{ $row['unique_id'] }}</span>
                        @if(($activeTab ?? 'students') === 'staff')
                            <a href="{{ url('attendance/view?tab=staff&staff='.$row['unique_id'].'&month='.$month.'&year='.$year) }}" class="btn btn-xs btn-outline-primary" style="padding:1px 6px; font-size:10px; border-radius:2px;">
                                <i class="fa fa-eye"></i>
                            </a>
                        @else
                            <a href="{{ url('attendance/view?tab=students&student='.$row['unique_id'].'&month='.$month.'&year='.$year) }}" class="btn btn-xs btn-outline-primary" style="padding:1px 6px; font-size:10px; border-radius:2px;">
                                <i class="fa fa-eye"></i>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Counts Breakdown Pills --}}
                <div class="mob-counts-row">
                    <span class="mob-pill-in">In: {{ $row['counts']['in'] ?? 0 }}</span>
                    @if(($row['counts']['out'] ?? 0) > 0)
                        <span class="mob-pill-out">Out: {{ $row['counts']['out'] }}</span>
                    @endif
                    <span class="mob-pill-absent">Abs: {{ $row['counts']['absent'] ?? 0 }}</span>
                    @if(($row['counts']['halfday'] ?? 0) > 0)
                        <span class="mob-pill-halfday">Half: {{ $row['counts']['halfday'] }}</span>
                    @endif
                    @if(($row['counts']['holiday'] ?? 0) > 0)
                        <span class="mob-pill-holiday">Hol: {{ $row['counts']['holiday'] }}</span>
                    @endif
                    <span class="mob-pill-total ml-auto">Tot: {{ $row['counts']['total'] ?? 0 }}</span>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4 bg-white rounded border">
                <i class="fa fa-info-circle mb-1" style="font-size:20px;"></i>
                <div style="font-size:12px; font-weight:600;">No attendance records found</div>
            </div>
        @endforelse

        <div id="mobNoMatchCard" class="text-center text-muted py-4 bg-white rounded border" style="display:none;">
            <i class="fa fa-search mb-1" style="font-size:20px;"></i>
            <div style="font-size:12px; font-weight:600;">No matching records found</div>
        </div>
    </div>

    {{-- 4. Date-wise Summary Breakdown Section (Always Visible) --}}
    @if(!empty($dateWiseSummary))
        <div class="card border mb-3" style="border-radius:4px; overflow:hidden;">
            <div class="card-header bg-light p-2 d-flex align-items-center justify-content-between">
                <div style="font-size:12px; font-weight:800; color:#002C54;">
                    <i class="fa fa-calendar-check-o mr-1"></i> Date-Wise Breakdown
                </div>
            </div>
            <div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0" style="font-size:11px;">
                            <thead>
                                <tr style="background:#002C54; color:#fff;">
                                    <th style="padding:5px;">Date</th>
                                    <th style="padding:5px; text-align:center;">In</th>
                                    <th style="padding:5px; text-align:center;">Abs</th>
                                    <th style="padding:5px; text-align:center;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dateWiseSummary as $date => $counts)
                                    <tr>
                                        <td style="padding:5px; font-weight:700;">{{ date('d M, D', strtotime($date)) }}</td>
                                        <td style="padding:5px; text-align:center; color:#15803d; font-weight:700;">{{ $counts['in'] }}</td>
                                        <td style="padding:5px; text-align:center; color:#b91c1c; font-weight:700;">{{ $counts['absent'] }}</td>
                                        <td style="padding:5px; text-align:center; font-weight:700;">{{ $counts['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- 5. Mobile Filter Bottom Sheet Modal --}}
<div class="mob-sheet-modal" id="filterBottomSheet">
    <div class="mob-sheet-content">
        <div class="mob-sheet-header">
            <div class="mob-sheet-title">
                <i class="fa fa-filter mr-1 text-primary"></i> Attendance Filters
            </div>
            <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
        </div>

        <form method="get" action="{{ url('attendance/report') }}">
            <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">

            {{-- Class or Role Select --}}
            @if(($activeTab ?? 'students') === 'staff')
                <div class="form-group mb-2">
                    <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;">Staff Role</label>
                    <select name="role_id" class="form-control form-control-sm" style="font-size:12px; height:32px; border-radius:3px;">
                        <option value="">All Roles</option>
                        @foreach($staffRoles as $role)
                            <option value="{{ $role->id }}" {{ (string)$roleFilter === (string)$role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="form-group mb-2">
                    <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;">Class</label>
                    <select name="class_type_id" class="form-control form-control-sm" style="font-size:12px; height:32px; border-radius:3px;">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ (string)$classFilter === (string)$class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Report Mode --}}
            <div class="form-group mb-2">
                <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;">Report Mode</label>
                <select name="report_mode" id="mob_report_mode" class="form-control form-control-sm" style="font-size:12px; height:32px; border-radius:3px;">
                    <option value="day_wise" {{ ($reportMode ?? 'day_wise') === 'day_wise' ? 'selected' : '' }}>Day Wise</option>
                    <option value="monthly" {{ ($reportMode ?? 'day_wise') === 'monthly' ? 'selected' : '' }}>Month Wise</option>
                </select>
            </div>

            {{-- Date Input --}}
            <div class="form-group mb-2" id="mobDayFields">
                <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $selectedDate ?? date('Y-m-d') }}" style="font-size:12px; height:32px; border-radius:3px;">
            </div>

            {{-- Month & Year Inputs --}}
            <div class="form-group mb-3" id="mobMonthFields" style="display:none;">
                <label style="font-size:11.5px; font-weight:700; color:#475569; margin-bottom:3px;">Month & Year</label>
                <div class="d-flex" style="gap:6px;">
                    <select name="month" class="form-control form-control-sm" style="font-size:12px; height:32px; border-radius:3px;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                    <select name="year" class="form-control form-control-sm" style="font-size:12px; height:32px; border-radius:3px;">
                        @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="d-flex" style="gap:8px;">
                <button type="submit" class="btn btn-primary flex-grow-1" style="height:34px; font-size:12px; font-weight:700; border-radius:3px; background:#002C54; border-color:#001f3f;">
                    <i class="fa fa-filter mr-1"></i> Apply Filter
                </button>
                <a href="{{ url('attendance/report?tab=' . ($activeTab ?? 'students')) }}" class="btn btn-outline-secondary" style="height:34px; font-size:12px; font-weight:700; border-radius:3px; display:inline-flex; align-items:center;">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function(){
        // Open/Close Bottom Sheet Modal
        $('#btnOpenFilterSheet').on('click', function(){
            $('#filterBottomSheet').addClass('show');
        });
        $('#btnCloseFilterSheet, #filterBottomSheet').on('click', function(e){
            if (e.target === this) {
                $('#filterBottomSheet').removeClass('show');
            }
        });

        // Mode toggling for day vs month
        function toggleMobModeFields() {
            var mode = $('#mob_report_mode').val();
            if (mode === 'monthly') {
                $('#mobDayFields').hide();
                $('#mobMonthFields').show();
            } else {
                $('#mobDayFields').show();
                $('#mobMonthFields').hide();
            }
        }
        $('#mob_report_mode').on('change', toggleMobModeFields);
        toggleMobModeFields();

        // Real-Time Search for Mobile Cards
        $('#mobCardSearch').on('input keyup', function(){
            var query = $(this).val().toLowerCase().trim();
            var $cards = $('.mob-person-item');
            var matchCount = 0;

            if (query === '') {
                $cards.show();
                matchCount = $cards.length;
                $('#mobNoMatchCard').hide();
            } else {
                $cards.each(function(){
                    var id = $(this).data('id') || '';
                    var name = $(this).data('name') || '';
                    if (id.indexOf(query) !== -1 || name.indexOf(query) !== -1) {
                        $(this).show();
                        matchCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (matchCount === 0 && $cards.length > 0) {
                    $('#mobNoMatchCard').show();
                } else {
                    $('#mobNoMatchCard').hide();
                }
            }

            $('#mobVisibleCount').text(matchCount);
        });
    });
</script>
@endsection
@endsection
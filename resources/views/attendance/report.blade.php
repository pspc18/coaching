@extends('layout.app')
@section('content')

@include('attendance.theme')

@php
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

    $totalCount = count($rows ?? []);
    $presentTotal = $totals['in'] ?? 0;
    $outTotal = $totals['out'] ?? 0;
    $absentTotal = $totals['absent'] ?? 0;
    $halfdayTotal = $totals['halfday'] ?? 0;
    $holidayTotal = $totals['holiday'] ?? 0;
    $grandTotal = $totals['total'] ?? 0;
@endphp

<style>
    /* Custom Styling for Attendance Report */
    .report-shell {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* KPI Summary Strip (Reference: monthlyReport) */
    .kpi-strip {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 8px;
    }
    @media (max-width: 1200px) {
        .kpi-strip {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 768px) {
        .kpi-strip {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .kpi-mini-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 2px;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border-left: 3px solid #cbd5e1;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-mini-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    }
    .kpi-mini-card.kpi-total { border-left-color: #002C54; }
    .kpi-mini-card.kpi-in { border-left-color: #10b981; }
    .kpi-mini-card.kpi-out { border-left-color: #0284c7; }
    .kpi-mini-card.kpi-absent { border-left-color: #ef4444; }
    .kpi-mini-card.kpi-halfday { border-left-color: #f59e0b; }
    .kpi-mini-card.kpi-holiday { border-left-color: #8b5cf6; }

    .kpi-mini-card .kpi-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .kpi-mini-card .kpi-val {
        font-size: 15px;
        font-weight: 800;
        line-height: 1.1;
        color: #1e293b;
    }
    .kpi-mini-card .kpi-icon {
        width: 30px;
        height: 30px;
        border-radius: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    /* Filter & Controls Toolbar */
    .filter-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 2px;
        padding: 8px 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .filter-card .form-control {
        height: 30px !important;
        font-size: 12px !important;
        padding: 2px 8px !important;
        border-radius: 2px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff;
        color: #1e293b;
    }
    .filter-card .btn {
        height: 30px !important;
        font-size: 12px !important;
        padding: 0 12px !important;
        border-radius: 2px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .btn-navy {
        background: #002C54 !important;
        border: 1px solid #001f3f !important;
        color: #ffffff !important;
    }
    .btn-navy:hover {
        background: #0f3460 !important;
        color: #ffffff !important;
    }

    /* Search input with icon */
    .search-input-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        min-width: 220px;
    }
    .search-input-wrapper .search-icon {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 11px;
        color: #64748b;
        pointer-events: none;
        z-index: 5;
    }
    .search-input-wrapper .search-input-field {
        padding-left: 28px !important;
        width: 100%;
    }

    /* Tabs Styling */
    .report-tab-btn {
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 2px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .report-tab-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .report-tab-btn.active {
        background: #002C54;
        color: #ffffff;
        border-color: #001f3f;
        box-shadow: 0 2px 4px rgba(0, 44, 84, 0.2);
    }

    /* ERP Table Styling */
    .erp-table-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .erp-table {
        width: 100%;
        margin-bottom: 0;
        font-size: 12px;
        border-collapse: separate;
        border-spacing: 0;
    }
    .erp-table thead th {
        background: #002C54 !important;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 11.5px;
        padding: 7px 8px;
        border-right: 1px solid #1a3a60;
        border-bottom: 2px solid #0f3460;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
    }
    .erp-table tbody td {
        padding: 6px 8px;
        border-bottom: 1px solid #e2e8f0;
        border-right: 1px solid #f1f5f9;
        vertical-align: middle;
        text-align: center;
        color: #1e293b;
    }
    .erp-table tbody tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .erp-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    .erp-table tfoot td {
        background: #f8fafc;
        font-weight: 800;
        padding: 7px 8px;
        border-top: 2px solid #cbd5e1;
        border-right: 1px solid #e2e8f0;
        text-align: center;
        color: #0f172a;
    }

    /* Badges */
    .att-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 26px;
        padding: 2px 6px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 2px;
        line-height: 1.2;
    }
    .att-badge-in { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    .att-badge-out { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .att-badge-absent { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
    .att-badge-halfday { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
    .att-badge-holiday { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .att-badge-total { background: #e2e8f0; color: #1e293b; border: 1px solid #cbd5e1; }
    .att-badge-id { background: #f1f5f9; color: #0f172a; border: 1px solid #cbd5e1; font-family: monospace; font-size: 11px; }

    /* Action button */
    .btn-action-view {
        width: 26px;
        height: 26px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 2px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #002C54;
        font-size: 11px;
        transition: all 0.15s ease;
    }
    .btn-action-view:hover {
        background: #002C54;
        color: #ffffff;
        border-color: #001f3f;
    }

    /* Summary Card */
    .summary-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-top: 6px;
        overflow: hidden;
    }
    .summary-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>

<div class="content-wrapper attendance-page">
    <section class="content pt-2 pb-4">
        <div class="container-fluid report-shell">
            
            {{-- 1. Signature Hero Banner --}}
            <div class="attendance-hero">
                <div class="attendance-hero-inner">
                    <div>
                        <div class="attendance-hero-kicker">
                            <i class="fa fa-tachometer mr-1"></i> Attendance Intelligence & Reporting
                        </div>
                        <h1 class="attendance-hero-title">
                            <i class="fa fa-bar-chart mr-1"></i> Attendance Report
                            <span class="badge badge-light ml-2 text-dark font-weight-normal" style="font-size:11px; border-radius:2px; vertical-align:middle;">
                                {{ ($activeTab ?? 'students') === 'staff' ? 'Staff Members' : 'Students' }}
                            </span>
                        </h1>
                        <p class="attendance-hero-subtitle">
                            @if(($reportMode ?? 'day_wise') === 'monthly')
                                <i class="fa fa-calendar mr-1"></i> Month: <strong>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</strong> ({{ date('d/m/Y', strtotime($dateFrom)) }} - {{ date('d/m/Y', strtotime($dateTo)) }})
                            @else
                                <i class="fa fa-calendar-check-o mr-1"></i> Date: <strong>{{ date('d M, Y (l)', strtotime($selectedDate)) }}</strong>
                            @endif
                        </p>
                    </div>
                    
                    <div class="attendance-hero-actions">
                        <a href="{{ $exportUrl }}" class="btn btn-sm btn-light font-weight-bold" style="font-size:11.5px; border-radius:2px; color:#002C54; box-shadow: 0 1px 3px rgba(0,0,0,0.12);" title="Export Attendance Report as Excel/CSV">
                            <i class="fa fa-file-excel-o mr-1 text-success"></i> Export Excel (CSV)
                        </a>
                        <a href="{{ url('monthlyReport?month=' . $month . '&year=' . $year) }}" class="btn btn-sm btn-outline-light" style="font-size:11.5px; border-radius:2px;">
                            <i class="fa fa-th mr-1"></i> Monthly Matrix
                        </a>
                        <a href="{{ url('attendance/view?tab=' . ($activeTab ?? 'students')) }}" class="btn btn-sm btn-outline-light" style="font-size:11.5px; border-radius:2px;">
                            <i class="fa fa-calendar mr-1"></i> Detailed View
                        </a>
                    </div>
                </div>
            </div>

            {{-- 2. KPI Summary Strip --}}
            <div class="kpi-strip">
                <div class="kpi-mini-card kpi-total">
                    <div>
                        <div class="kpi-label">Total {{ ($activeTab ?? 'students') === 'staff' ? 'Staff' : 'Students' }}</div>
                        <div class="kpi-val">{{ $totalCount }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#f1f5f9; color:#002C54;">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <div class="kpi-mini-card kpi-in">
                    <div>
                        <div class="kpi-label">Present / In</div>
                        <div class="kpi-val" style="color:#15803d;">{{ $presentTotal }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
                        <i class="fa fa-sign-in"></i>
                    </div>
                </div>
                <div class="kpi-mini-card kpi-out">
                    <div>
                        <div class="kpi-label">Out / Marked</div>
                        <div class="kpi-val" style="color:#0369a1;">{{ $outTotal }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#e0f2fe; color:#0369a1;">
                        <i class="fa fa-sign-out"></i>
                    </div>
                </div>
                <div class="kpi-mini-card kpi-absent">
                    <div>
                        <div class="kpi-label">Absent</div>
                        <div class="kpi-val" style="color:#b91c1c;">{{ $absentTotal }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#fee2e2; color:#b91c1c;">
                        <i class="fa fa-times-circle"></i>
                    </div>
                </div>
                <div class="kpi-mini-card kpi-halfday">
                    <div>
                        <div class="kpi-label">Half Day</div>
                        <div class="kpi-val" style="color:#b45309;">{{ $halfdayTotal }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                        <i class="fa fa-adjust"></i>
                    </div>
                </div>
                <div class="kpi-mini-card kpi-holiday">
                    <div>
                        <div class="kpi-label">Holiday / Leave</div>
                        <div class="kpi-val" style="color:#6d28d9;">{{ $holidayTotal }}</div>
                    </div>
                    <div class="kpi-icon" style="background:#ede9fe; color:#6d28d9;">
                        <i class="fa fa-coffee"></i>
                    </div>
                </div>
            </div>

            {{-- 3. Filter Toolbar & Entity Switcher --}}
            <div class="filter-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap:10px;">
                    
                    {{-- Tab Buttons --}}
                    <div class="d-flex align-items-center" style="gap:6px;">
                        <a href="{{ url('attendance/report?tab=students&report_mode='.$reportMode.'&date='.$selectedDate.'&month='.$month.'&year='.$year) }}" 
                           class="report-tab-btn {{ ($activeTab ?? 'students') === 'students' ? 'active' : '' }}">
                            <i class="fa fa-graduation-cap"></i> Students
                        </a>
                        @if($canAccessStaffAttendance ?? false)
                        <a href="{{ url('attendance/report?tab=staff&report_mode='.$reportMode.'&date='.$selectedDate.'&month='.$month.'&year='.$year) }}" 
                           class="report-tab-btn {{ ($activeTab ?? 'students') === 'staff' ? 'active' : '' }}">
                            <i class="fa fa-briefcase"></i> Staff Members
                        </a>
                        @endif
                    </div>

                    {{-- Live Search Input --}}
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <div class="search-input-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" id="reportTableSearch" class="form-control search-input-field" placeholder="Search name, ID, class..." autocomplete="off">
                        </div>
                        <span class="badge badge-light border text-muted" style="font-size:11px; padding:6px 9px; border-radius:2px;" id="rowCountBadge">
                            <strong id="visibleCount" class="text-dark">{{ $totalCount }}</strong> / {{ $totalCount }}
                        </span>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form method="get" action="{{ url('attendance/report') }}" class="mt-2 pt-2 border-top">
                    <input type="hidden" name="tab" value="{{ $activeTab ?? 'students' }}">
                    <div class="d-flex flex-wrap align-items-center" style="gap:8px;">
                        
                        {{-- Class / Role Dropdown --}}
                        @if(($activeTab ?? 'students') === 'staff')
                            <div style="min-width: 200px;">
                                <select name="role_id" class="form-control select2">
                                    <option value="">All Roles</option>
                                    @foreach($staffRoles as $role)
                                        <option value="{{ $role->id }}" {{ (string)$roleFilter === (string)$role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div style="min-width: 200px;">
                                <select name="class_type_id" class="form-control select2">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ (string)$classFilter === (string)$class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Mode Selector --}}
                        <div style="min-width: 140px;">
                            <select name="report_mode" id="report_mode" class="form-control">
                                <option value="day_wise" {{ ($reportMode ?? 'day_wise') === 'day_wise' ? 'selected' : '' }}>Day Wise</option>
                                <option value="monthly" {{ ($reportMode ?? 'day_wise') === 'monthly' ? 'selected' : '' }}>Month Wise</option>
                            </select>
                        </div>

                        {{-- Day Fields --}}
                        <div id="dayFields" style="min-width: 160px;">
                            <input type="date" name="date" class="form-control" value="{{ $selectedDate ?? date('Y-m-d') }}">
                        </div>

                        {{-- Month & Year Fields --}}
                        <div id="monthFields" class="d-flex align-items-center" style="gap:6px; display:none;">
                            <select name="month" class="form-control" style="width: 100px;">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('M', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>

                            <select name="year" class="form-control" style="width: 90px;">
                                @for($y = date('Y')-3; $y <= date('Y')+1; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Submit / Reset --}}
                        <button type="submit" class="btn btn-navy">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                        <a href="{{ url('attendance/report?tab=' . ($activeTab ?? 'students')) }}" class="btn btn-outline-secondary" title="Reset Filters">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        <a href="{{ $exportUrl }}" class="btn btn-success ml-auto" style="font-size:11.5px; height:30px; border-radius:2px;" title="Export Attendance Data">
                            <i class="fa fa-file-excel-o mr-1"></i> Export Excel
                        </a>
                    </div>
                </form>
            </div>

            {{-- 4. Main ERP Data Table Card --}}
            <div class="erp-table-card">
                <div class="table-responsive">
                    <table class="erp-table" id="reportDataTable">
                        <thead>
                            <tr>
                                <th style="width: 45px;">S.No.</th>
                                <th style="width: 110px;">Unique ID</th>
                                <th style="text-align: left; padding-left: 12px; min-width: 180px;">Name</th>
                                <th style="width: 75px;"><i class="fa fa-sign-in mr-1 text-success"></i> In</th>
                                <th style="width: 75px;"><i class="fa fa-sign-out mr-1 text-info"></i> Out</th>
                                <th style="width: 75px;"><i class="fa fa-times-circle mr-1 text-danger"></i> Absent</th>
                                <th style="width: 85px;"><i class="fa fa-adjust mr-1 text-warning"></i> Half Day</th>
                                <th style="width: 85px;"><i class="fa fa-coffee mr-1 text-primary"></i> Holiday</th>
                                <th style="width: 80px;">Total</th>
                                <th style="width: 60px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr class="report-row" 
                                    data-id="{{ strtolower($row['unique_id']) }}" 
                                    data-name="{{ strtolower($row['name']) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="att-badge att-badge-id">{{ $row['unique_id'] }}</span>
                                    </td>
                                    <td style="text-align: left; padding-left: 12px;">
                                        <div class="d-flex align-items-center" style="gap:8px;">
                                            <div style="width:24px; height:24px; border-radius:2px; background:#002C54; color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700;">
                                                {{ strtoupper(substr($row['name'] ?? 'U', 0, 1)) }}
                                            </div>
                                            <span style="font-weight:700; color:#1e293b;">{{ $row['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if(($row['counts']['in'] ?? 0) > 0)
                                            <span class="att-badge att-badge-in">{{ $row['counts']['in'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($row['counts']['out'] ?? 0) > 0)
                                            <span class="att-badge att-badge-out">{{ $row['counts']['out'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($row['counts']['absent'] ?? 0) > 0)
                                            <span class="att-badge att-badge-absent">{{ $row['counts']['absent'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($row['counts']['halfday'] ?? 0) > 0)
                                            <span class="att-badge att-badge-halfday">{{ $row['counts']['halfday'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(($row['counts']['holiday'] ?? 0) > 0)
                                            <span class="att-badge att-badge-holiday">{{ $row['counts']['holiday'] }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:11px;">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="att-badge att-badge-total font-weight-bold">{{ $row['counts']['total'] ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if(($activeTab ?? 'students') === 'staff')
                                            <a class="btn-action-view" href="{{ url('attendance/view?tab=staff&staff='.$row['unique_id'].'&month='.$month.'&year='.$year) }}" title="View Attendance Matrix">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @else
                                            <a class="btn-action-view" href="{{ url('attendance/view?tab=students&student='.$row['unique_id'].'&month='.$month.'&year='.$year) }}" title="View Attendance Matrix">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="noDataRow">
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fa fa-info-circle mr-1"></i> No attendance records found for the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="noMatchesRow" style="display:none;">
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fa fa-search mr-1"></i> No records match your search filter.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; padding-right: 14px;">Overall Summary Totals:</td>
                                <td><span class="att-badge att-badge-in">{{ $presentTotal }}</span></td>
                                <td><span class="att-badge att-badge-out">{{ $outTotal }}</span></td>
                                <td><span class="att-badge att-badge-absent">{{ $absentTotal }}</span></td>
                                <td><span class="att-badge att-badge-halfday">{{ $halfdayTotal }}</span></td>
                                <td><span class="att-badge att-badge-holiday">{{ $holidayTotal }}</span></td>
                                <td><span class="att-badge att-badge-total font-weight-bold">{{ $grandTotal }}</span></td>
                                <td>-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- 5. Date-wise Summary Breakdown Card (Always Cleanly Displayed) --}}
            @if(!empty($dateWiseSummary))
                <div class="summary-card">
                    <div class="summary-card-header">
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <i class="fa fa-calendar-check-o text-primary"></i>
                            <strong style="font-size:12.5px; color:#002C54;">Date-Wise Attendance Breakdown</strong>
                            <span class="badge badge-light border text-muted" style="font-size:11px;">
                                {{ date('d/m/Y', strtotime($dateFrom)) }} &mdash; {{ date('d/m/Y', strtotime($dateTo)) }}
                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="table-responsive">
                            <table class="erp-table table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:130px; text-align:left; padding-left:12px;">Date</th>
                                        <th style="width:100px;">Day</th>
                                        <th><i class="fa fa-sign-in mr-1 text-success"></i> In / Present</th>
                                        <th><i class="fa fa-sign-out mr-1 text-info"></i> Out</th>
                                        <th><i class="fa fa-times-circle mr-1 text-danger"></i> Absent</th>
                                        <th><i class="fa fa-adjust mr-1 text-warning"></i> Half Day</th>
                                        <th><i class="fa fa-coffee mr-1 text-primary"></i> Holiday</th>
                                        <th>Total Marked</th>
                                        <th style="min-width:140px;">Attendance Ratio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dateWiseSummary as $date => $counts)
                                        @php
                                            $dayTs = strtotime($date);
                                            $dayName = date('l', $dayTs);
                                            $isSunday = (date('w', $dayTs) == 0);
                                            $dTotal = $counts['total'] ?? 0;
                                            $dIn = $counts['in'] ?? 0;
                                            $pct = $dTotal > 0 ? round(($dIn / $dTotal) * 100) : 0;
                                        @endphp
                                        <tr style="{{ $isSunday ? 'background:#fffbeb;' : '' }}">
                                            <td style="text-align:left; padding-left:12px; font-weight:700;">
                                                {{ date('d M, Y', $dayTs) }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $isSunday ? 'badge-warning' : 'badge-light border' }}" style="border-radius:2px; font-size:10.5px;">
                                                    {{ $dayName }}
                                                </span>
                                            </td>
                                            <td><span class="att-badge att-badge-in">{{ $counts['in'] }}</span></td>
                                            <td><span class="att-badge att-badge-out">{{ $counts['out'] }}</span></td>
                                            <td><span class="att-badge att-badge-absent">{{ $counts['absent'] }}</span></td>
                                            <td><span class="att-badge att-badge-halfday">{{ $counts['halfday'] }}</span></td>
                                            <td><span class="att-badge att-badge-holiday">{{ $counts['holiday'] }}</span></td>
                                            <td><strong>{{ $counts['total'] }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center" style="gap:6px;">
                                                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 2px; background: #e2e8f0;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span style="font-size:10.5px; font-weight:700; min-width:32px; color:#15803d;">{{ $pct }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>
</div>

<script>
    $(document).ready(function(){
        // Initialize Select2
        if ($.fn.select2) {
            $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
        }

        // Toggle Day vs Month Mode
        function toggleModeFields() {
            var mode = $('#report_mode').val();
            if (mode === 'monthly') {
                $('#dayFields').hide();
                $('#monthFields').css('display', 'flex');
            } else {
                $('#dayFields').show();
                $('#monthFields').hide();
            }
        }
        $('#report_mode').on('change', toggleModeFields);
        toggleModeFields();

        // Real-Time Table Quick Search
        $('#reportTableSearch').on('input keyup', function(){
            var query = $(this).val().toLowerCase().trim();
            var $rows = $('.report-row');
            var matchCount = 0;

            if (query === '') {
                $rows.show();
                matchCount = $rows.length;
                $('#noMatchesRow').hide();
            } else {
                $rows.each(function(){
                    var id = $(this).data('id') || '';
                    var name = $(this).data('name') || '';
                    if (id.indexOf(query) !== -1 || name.indexOf(query) !== -1) {
                        $(this).show();
                        matchCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (matchCount === 0 && $rows.length > 0) {
                    $('#noMatchesRow').show();
                } else {
                    $('#noMatchesRow').hide();
                }
            }

            $('#visibleCount').text(matchCount);
        });
    });
</script>
@endsection
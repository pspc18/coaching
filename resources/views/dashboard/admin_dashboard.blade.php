@extends('layout.app')

@section('content')
@php
    $capacity = $studentStats['capacity'] ?? 0;
    $occupancy = $capacity > 0 ? round(($studentStats['total'] / $capacity) * 100) : 0;
    $feePercent = ($feeStats['assigned'] ?? 0) > 0 ? round((($feeStats['collected'] ?? 0) / $feeStats['assigned']) * 100) : 0;
    $complaintPercent = ($complaintStats['total'] ?? 0) > 0 ? round((($complaintStats['resolved'] ?? 0) / $complaintStats['total']) * 100) : 0;
    $studentAttendanceChartData = collect($studentAttendanceBreakdown ?? [])->only(['Present', 'Absent', 'Half Day', 'Holiday', 'Leave', 'Event', 'Exam', 'Late'])->all();
    $staffAttendanceChartData = collect($staffAttendanceBreakdown ?? [])->only(['Present', 'Absent', 'Work From Home', 'Half Day', 'Holiday'])->all();
    $birthdays = collect($birthdays ?? []);
    $notices = collect($notices ?? []);
    $remarks = collect($remarks ?? []);
@endphp

<div class="content-wrapper dashboard-page">
    <section class="content p-0">
        <div class="container-fluid">
            
            {{-- Top Action & Welcome Hero (Sharp & Compact) --}}
            <div class="dash-hero mb-2">
                <div class="dash-hero-text">
                    <span class="dash-kicker"><i class="fa fa-calendar-o mr-1"></i>{{ now()->format('l, d M Y') }}</span>
                    <h1 class="dash-title">Test Dashboard</h1>
                    <p class="dash-subtitle">School performance, attendance & finances overview</p>
                </div>
                <div class="dash-hero-actions">
                    <a href="{{ url('admissionView') }}" class="dash-btn dash-btn-light"><i class="fa fa-user-plus mr-1"></i> Admission</a>
                    <a href="{{ url('feesCollectAdd') }}" class="dash-btn dash-btn-outline"><i class="fa fa-inr mr-1"></i> Collect Fees</a>
                    <a href="{{ url('studentsAttendanceAdd') }}" class="dash-btn dash-btn-outline"><i class="fa fa-calendar-check-o mr-1"></i> Attendance</a>
                    <a href="{{ url('expenseAdd') }}" class="dash-btn dash-btn-outline"><i class="fa fa-plus-circle mr-1"></i> Expense</a>
                </div>
            </div>

            {{-- Row of 5 Sharp Compact Metric KPI Tiles --}}
            <div class="row row-compact mb-2">
                {{-- 1. Active Students --}}
                <div class="col-6 col-sm-6 col-md-4 col-xl col-metric mb-2">
                    <a class="dash-kpi kpi-primary" href="{{ url('admissionView') }}">
                        <div class="kpi-header">
                            <span class="kpi-label">Active Students</span>
                            <span class="kpi-icon"><i class="fa fa-users"></i></span>
                        </div>
                        <div class="kpi-value">{{ number_format($studentStats['total'] ?? 0) }}</div>
                        <div class="kpi-meta">{{ $studentStats['male'] ?? 0 }} Boys · {{ $studentStats['female'] ?? 0 }} Girls</div>
                    </a>
                </div>

                {{-- 2. Today's Attendance --}}
                <div class="col-6 col-sm-6 col-md-4 col-xl col-metric mb-2">
                    <div class="dash-kpi kpi-success">
                        <a class="kpi-inner-link" href="{{ url('attendance/report') }}">
                            <div class="kpi-header">
                                <span class="kpi-label">Today's Attendance</span>
                                <span class="kpi-icon"><i class="fa fa-check-circle"></i></span>
                            </div>
                            <div class="kpi-value">{{ number_format($attendanceStats['present'] ?? 0) }}</div>
                            <div class="kpi-meta">{{ $attendanceStats['absent'] ?? 0 }} Absent · {{ $attendanceStats['unmarked'] ?? 0 }} Unmarked</div>
                        </a>
                        <div class="kpi-action-row">
                            <a class="kpi-pill-link" href="{{ url('attendance/today-absent-not-marked-pdf') }}" target="_blank" rel="noopener">
                                <i class="fa fa-file-pdf-o mr-1"></i> PDF Report
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 3. Fee Collection --}}
                <div class="col-6 col-sm-6 col-md-4 col-xl col-metric mb-2">
                    <a class="dash-kpi kpi-warning" href="{{ url('fee_dashboard') }}">
                        <div class="kpi-header">
                            <span class="kpi-label">Fee Collection</span>
                            <span class="kpi-icon"><i class="fa fa-inr"></i></span>
                        </div>
                        <div class="kpi-value">₹ {{ number_format($feeStats['collected'] ?? 0, 0) }}</div>
                        <div class="kpi-meta">{{ $feePercent }}% of ₹ {{ number_format($feeStats['assigned'] ?? 0, 0) }}</div>
                    </a>
                </div>

                {{-- 4. Staff on Duty --}}
                <div class="col-6 col-sm-6 col-md-4 col-xl col-metric mb-2">
                    <a class="dash-kpi kpi-info" href="{{ url('viewUser') }}">
                        <div class="kpi-header">
                            <span class="kpi-label">Staff on Duty</span>
                            <span class="kpi-icon"><i class="fa fa-user-circle"></i></span>
                        </div>
                        <div class="kpi-value">{{ number_format($staffStats['present'] ?? 0) }}</div>
                        <div class="kpi-meta">{{ $staffStats['away'] ?? 0 }} Away / On Leave</div>
                    </a>
                </div>

                {{-- 5. Open Complaints & Notices --}}
                <div class="col-12 col-sm-12 col-md-4 col-xl col-metric mb-2">
                    <a class="dash-kpi kpi-danger" href="{{ url('complaints-management') }}">
                        <div class="kpi-header">
                            <span class="kpi-label">Complaints & Notices</span>
                            <span class="kpi-icon"><i class="fa fa-exclamation-circle"></i></span>
                        </div>
                        <div class="kpi-value">{{ number_format($complaintStats['pending'] ?? 0) }}</div>
                        <div class="kpi-meta">{{ $complaintStats['resolved'] ?? 0 }} Resolved · {{ $notices->count() }} Notices</div>
                    </a>
                </div>
            </div>

            {{-- Financial Overview (8 Cols) + Today's Ledger (4 Cols) --}}
            <div class="row row-compact mb-2">
                <div class="col-lg-8 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-line-chart text-primary mr-1"></i> Collection & Expense Overview</h3>
                                <p class="dash-card-desc">Monthly fees collected vs expenses for {{ now()->format('Y') }}</p>
                            </div>
                        </div>
                        <div class="dash-card-body">
                            <div class="chart-container-large"><canvas id="financeChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-credit-card text-success mr-1"></i> Today's Financials</h3>
                                <p class="dash-card-desc">Daily cashflow and collection totals</p>
                            </div>
                        </div>
                        <div class="dash-card-body">
                            <div class="finance-today-badge">
                                <span class="badge-sub">Today's Collection</span>
                                <span class="badge-amount text-success">₹ {{ number_format($feeStats['today'] ?? 0, 0) }}</span>
                            </div>
                            <div class="dash-summary-table mt-2">
                                <div class="dash-summary-row">
                                    <span><i class="fa fa-calendar-minus-o text-muted mr-1"></i> Today's Expense</span>
                                    <strong>₹ {{ number_format($expenseStats['today'] ?? 0, 0) }}</strong>
                                </div>
                                <div class="dash-summary-row">
                                    <span><i class="fa fa-calendar text-muted mr-1"></i> This Month Expense</span>
                                    <strong class="text-danger">₹ {{ number_format($expenseStats['month'] ?? 0, 0) }}</strong>
                                </div>
                                <div class="dash-summary-row">
                                    <span><i class="fa fa-calculator text-muted mr-1"></i> Total Yearly Expense</span>
                                    <strong>₹ {{ number_format($expenseStats['total'] ?? 0, 0) }}</strong>
                                </div>
                            </div>
                            <form action="{{ url('fees/index') }}" method="post" class="mt-3">
                                @csrf
                                <input type="hidden" name="starting" value="{{ now()->toDateString() }}">
                                <input type="hidden" name="ending" value="{{ now()->toDateString() }}">
                                <button class="btn btn-primary btn-block btn-compact">
                                    <i class="fa fa-file-text-o mr-1"></i> View Today's Receipts <i class="fa fa-arrow-right ml-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attendance Today Analytics: Students (6 Cols) + Staff (6 Cols) --}}
            <div class="row row-compact mb-2">
                <div class="col-lg-6 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-graduation-cap text-info mr-1"></i> Student Attendance Today</h3>
                                <p class="dash-card-desc">Breakdown of student present/absent/leave status</p>
                            </div>
                        </div>
                        <div class="dash-card-body">
                            <div class="chart-container-donut"><canvas id="studentAttendanceChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-user-circle-o text-primary mr-1"></i> Staff Attendance Today</h3>
                                <p class="dash-card-desc">Faculty and non-teaching attendance status</p>
                            </div>
                        </div>
                        <div class="dash-card-body">
                            <div class="chart-container-donut"><canvas id="teacherAttendanceChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Class-wise Attendance (12 Cols) --}}
            <div class="row row-compact mb-2">
                <div class="col-12 mb-2">
                    <div class="dash-card">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-bar-chart text-warning mr-1"></i> Class-Wise Attendance Today</h3>
                                <p class="dash-card-desc">Section & class distribution for current session</p>
                            </div>
                        </div>
                        <div class="dash-card-body class-chart-scroll-box">
                            <div class="chart-container-classwise"><canvas id="classWiseAttendanceChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notices & Remarks (6 Cols) + To-Do Tasks (6 Cols) --}}
            <div class="row row-compact mb-2">
                {{-- Notices & Remarks --}}
                <div class="col-lg-6 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-bullhorn text-danger mr-1"></i> Notice Board & Remarks</h3>
                                <p class="dash-card-desc">Latest announcements and student activity updates</p>
                            </div>
                        </div>
                        <div class="dash-card-body p-0 d-flex flex-column dash-card-body-fixed">
                            <div class="dash-feed-list">
                                @forelse($notices as $notice)
                                    <a href="{{ url('notice_board/viewid') }}/{{ $notice->id }}" class="dash-feed-item">
                                        <span class="feed-badge bg-warning"><i class="fa fa-bell-o"></i></span>
                                        <div class="feed-content">
                                            <span class="feed-title">{{ strip_tags(html_entity_decode($notice->title . ' ' . $notice->message)) }}</span>
                                            <span class="feed-date">Notice</span>
                                        </div>
                                        <span class="feed-link"><i class="fa fa-chevron-right"></i></span>
                                    </a>
                                @empty
                                @endforelse

                                @forelse($remarks as $remark)
                                    <a href="{{ url('students/index') }}" class="dash-feed-item">
                                        <span class="feed-badge bg-info"><i class="fa fa-comment-o"></i></span>
                                        <div class="feed-content">
                                            <span class="feed-title">{{ $remark->remark }}</span>
                                            <span class="feed-date">Student Remark</span>
                                        </div>
                                        <span class="feed-link"><i class="fa fa-chevron-right"></i></span>
                                    </a>
                                @empty
                                @endforelse

                                @if($notices->isEmpty() && $remarks->isEmpty())
                                    <div class="dash-empty-state">
                                        <div class="empty-icon"><i class="fa fa-bell-slash-o"></i></div>
                                        <div class="empty-title">No Notices or Remarks Found</div>
                                        <div class="empty-desc">There are no active notices or student remarks recorded today.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- To-Do Task List --}}
                <div class="col-lg-6 mb-2">
                    <div class="dash-card h-100">
                        <div class="dash-card-header d-flex align-items-center justify-content-between">
                            <div class="dash-card-header-text">
                                <h3 class="dash-card-title"><i class="fa fa-tasks text-primary mr-1"></i> Assigned To-Do Tasks</h3>
                                <p class="dash-card-desc">Pending operational items</p>
                            </div>
                            <a href="{{ url('to_do_assign') }}" class="btn btn-sm btn-outline-primary btn-compact">
                                <i class="fa fa-plus mr-1"></i> Manage
                            </a>
                        </div>
                        <div class="dash-card-body p-0 d-flex flex-column dash-card-body-fixed">
                            <ul class="todo-list todoList dash-todo-box">
                                <li class="dash-empty-state">
                                    <div class="empty-icon"><i class="fa fa-spinner fa-spin"></i></div>
                                    <div class="empty-title">Loading Tasks…</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Birthdays Today Banner --}}
            @if($birthdays->count())
                <div class="dash-birthday-alert mb-2">
                    <div class="bday-icon"><i class="fa fa-birthday-cake"></i></div>
                    <div class="bday-text">
                        <strong>Today's Birthdays</strong>
                        <span>
                            @foreach($birthdays as $student)
                                {{ $student->first_name }} {{ $student->last_name }}{{ !$loop->last ? ' · ' : '' }}
                            @endforeach
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </section>
</div>

{{-- ==============================================================================
     PAGE-SPECIFIC COMPACT, SHARP & HIGH-SPEED MINIFIED CSS THEME
     Styles top bar, bottom bar, hero, KPI tiles, charts, and mobile drawers.
     Zero external CSS dependency. Embedded directly for maximum speed.
     ============================================================================== --}}
<style>
/* Reset & General Layout */
.dashboard-page{background:#f8fafc;color:#0f172a;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;font-size:12.5px}
.dashboard-page *{box-sizing:border-box}
.dashboard-page .container-fluid{padding-left:2px!important;padding-right:2px!important}
.dashboard-page .content{padding:0!important;margin:0!important}
.row-compact{margin-left:-3px;margin-right:-3px}
.row-compact>[class*="col-"]{padding-left:3px;padding-right:3px;margin-bottom:6px!important}
.row-compact.mb-2{margin-bottom:6px!important}

/* Bottom Desktop & Mobile Footer Styling */
.main-footer{background:#fff;border-top:1px solid #e2e8f0;padding:6px 12px;font-size:11px;color:#64748b}
.main_mobile_footer{background:#fff;border-top:1px solid #e2e8f0;height:46px;box-shadow:0 -1px 3px rgba(0,0,0,.04)}

/* Hero Welcome Banner */
.dash-hero{background:linear-gradient(135deg,#002C54 0%,#0f3460 100%);color:#fff;border-radius:2px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;box-shadow:0 1px 3px rgba(0,44,84,.12);margin-bottom:6px!important}
.dash-kicker{font-size:10px;text-transform:uppercase;letter-spacing:.05em;opacity:.85;display:block;margin-bottom:1px}
.dash-title{font-size:15px;font-weight:700;margin:0 0 2px 0;line-height:1.2;color:#fff}
.dash-subtitle{font-size:11px;opacity:.85;margin:0}
.dash-hero-actions{display:flex;gap:4px;flex-wrap:wrap}
.dash-btn{display:inline-flex;align-items:center;padding:4px 8px;font-size:11.5px;font-weight:600;border-radius:2px;text-decoration:none!important;transition:all .15s}
.dash-btn-light{background:#fff;color:#002C54;border:1px solid #fff}
.dash-btn-light:hover{background:#f1f5f9;color:#001f3d}
.dash-btn-outline{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.4)}
.dash-btn-outline:hover{background:rgba(255,255,255,.15);color:#fff;border-color:#fff}

/* Sharp Compact KPI Metric Tiles */
.dash-kpi{background:#fff;border:1px solid #e2e8f0;border-radius:2px;padding:6px 8px;display:flex;flex-direction:column;text-decoration:none!important;color:#0f172a!important;height:100%;transition:border-color .15s,box-shadow .15s;position:relative}
.dash-kpi:hover{border-color:#cbd5e1;box-shadow:0 2px 5px rgba(0,0,0,.05)}
.kpi-primary{border-top:3px solid #002C54}
.kpi-success{border-top:3px solid #10b981}
.kpi-warning{border-top:3px solid #f59e0b}
.kpi-info{border-top:3px solid #0284c7}
.kpi-danger{border-top:3px solid #ef4444}
.kpi-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:2px}
.kpi-label{font-size:10.5px;font-weight:600;text-transform:uppercase;color:#64748b;letter-spacing:.03em}
.kpi-icon{font-size:13px;color:#94a3b8}
.kpi-primary .kpi-icon{color:#002C54}
.kpi-success .kpi-icon{color:#10b981}
.kpi-warning .kpi-icon{color:#f59e0b}
.kpi-info .kpi-icon{color:#0284c7}
.kpi-danger .kpi-icon{color:#ef4444}
.kpi-value{font-size:18px;font-weight:700;line-height:1.15;color:#0f172a;margin-bottom:2px}
.kpi-meta{font-size:10px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.kpi-inner-link{text-decoration:none!important;color:inherit!important;display:block}
.kpi-action-row{margin-top:4px;padding-top:3px;border-top:1px dashed #e2e8f0}
.kpi-pill-link{display:inline-flex;align-items:center;font-size:10px;font-weight:600;color:#002C54;background:#f1f5f9;padding:1px 5px;border-radius:2px;text-decoration:none!important;border:1px solid #e2e8f0}
.kpi-pill-link:hover{background:#e2e8f0;color:#001f3d}

/* Sharp Compact Cards */
.dash-card{background:#fff;border:1px solid #e2e8f0;border-radius:2px;box-shadow:0 1px 2px rgba(0,0,0,.02);display:flex;flex-direction:column;margin-bottom:6px}
.dash-card-header{padding:6px 10px;border-bottom:1px solid #e2e8f0;background:#fafbfc}
.dash-card-title{font-size:12.5px;font-weight:600;margin:0;color:#0f172a;line-height:1.2}
.dash-card-desc{font-size:10.5px;color:#64748b;margin:1px 0 0 0}
.dash-card-body{padding:8px 10px;flex:1}

/* Chart Canvas Wrappers */
.chart-container-large{height:190px;position:relative}
.chart-container-donut{height:170px;position:relative}
.chart-container-classwise{height:250px;min-width:650px;position:relative}
.class-chart-scroll-box{overflow-x:auto;-webkit-overflow-scrolling:touch}

/* Financial Badges & Tables */
.finance-today-badge{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:2px;padding:6px 8px;text-align:center}
.badge-sub{font-size:10px;color:#166534;display:block;text-transform:uppercase;font-weight:600}
.badge-amount{font-size:19px;font-weight:700;line-height:1.15;display:block}
.dash-summary-table{display:flex;flex-direction:column;gap:3px}
.dash-summary-row{display:flex;align-items:center;justify-content:space-between;padding:3px 0;border-bottom:1px solid #f1f5f9;font-size:11.5px;color:#475569}
.dash-summary-row:last-child{border-bottom:none}
.btn-compact{padding:4px 8px;font-size:11.5px;border-radius:2px;font-weight:600}

/* Feed & Notices & To-Do Box */
.dash-card-body-fixed{height:190px;min-height:190px}
.dash-feed-list{height:100%;max-height:190px;overflow-y:auto;display:flex;flex-direction:column}
.dash-feed-item{display:flex;align-items:center;gap:6px;padding:5px 8px;border-bottom:1px solid #f1f5f9;text-decoration:none!important;color:#334155;transition:background .15s}
.dash-feed-item:hover{background:#f8fafc;color:#002C54}
.feed-badge{width:20px;height:20px;border-radius:2px;display:flex;align-items:center;justify-content:center;font-size:9.5px;color:#fff;flex-shrink:0}
.feed-content{flex:1;min-width:0}
.feed-title{display:block;font-size:11.5px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.feed-date{display:block;font-size:9.5px;color:#94a3b8}
.feed-link{font-size:9.5px;color:#cbd5e1}

/* Empty State */
.dash-empty-state{display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;min-height:150px;padding:16px 12px;text-align:center;color:#94a3b8;margin:auto 0}
.dash-empty-state .empty-icon{font-size:24px;color:#cbd5e1;margin-bottom:6px;line-height:1}
.dash-empty-state .empty-title{font-size:12.5px;font-weight:600;color:#475569;margin-bottom:2px}
.dash-empty-state .empty-desc{font-size:11px;color:#94a3b8;line-height:1.35;max-width:280px}

/* To-Do Box */
.dash-todo-box{height:100%;max-height:190px;overflow-y:auto;list-style:none;padding:0;margin:0;display:flex;flex-direction:column}
.dash-todo-box li.dash-empty-state{list-style:none}

/* Birthday Banner */
.dash-birthday-alert{background:#fffbeb;border:1px solid #fef3c7;border-left:3px solid #f59e0b;border-radius:2px;padding:6px 10px;display:flex;align-items:center;gap:8px;color:#92400e;font-size:11.5px;margin-bottom:6px!important}
.bday-icon{font-size:16px;color:#d97706}
.bday-text strong{display:block;font-size:11.5px}
.bday-text span{font-size:11px;color:#b45309}

/* Mobile Responsiveness */
@media(max-width:991.98px){
    .dash-hero{padding:10px 12px}
    .dash-hero-actions{width:100%;margin-top:4px}
    .col-metric{flex:0 0 50%;max-width:50%}
}
@media(max-width:575.98px){
    .dash-hero-actions .dash-btn{flex:1;justify-content:center;padding:4px 6px;font-size:11px}
    .col-metric{flex:0 0 50%;max-width:50%}
    .kpi-value{font-size:17px}
    .kpi-meta{font-size:10px}
    .chart-container-large{height:180px}
    .chart-container-donut{height:160px}
    .chart-container-classwise{height:220px;min-width:550px}
}
</style>

{{-- ==============================================================================
     DASHBOARD INTERACTION & CHARTS SCRIPTS
     Sharp bar radius (2px) conforming to UI Guidelines.
     ============================================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const money = value => '₹ ' + Number(value).toLocaleString('en-IN');
    const chartColors = ['#002C54','#10b981','#f59e0b','#ef4444','#0284c7','#8b5cf6','#ec4899','#64748b'];

    // 1. Fee Collection vs Expense Bar Chart
    const financeCanvas = document.getElementById('financeChart');
    if (financeCanvas && typeof Chart !== 'undefined') {
        new Chart(financeCanvas, {
            type: 'bar',
            data: {
                labels: @json($feeChart['labels'] ?? []),
                datasets: [
                    { label: 'Fees Collected', data: @json($feeChart['fees'] ?? []), backgroundColor: '#002C54', borderRadius: 2 },
                    { label: 'Expenses', data: @json($feeChart['expenses'] ?? []), backgroundColor: '#f59e0b', borderRadius: 2 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    tooltip: { callbacks: { label: c => c.dataset.label + ': ' + money(c.raw) } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10.5 } } },
                    y: { beginAtZero: true, ticks: { font: { size: 10.5 }, callback: v => money(v) } }
                }
            }
        });
    }

    // 2. Attendance Doughnut Helper
    function renderDoughnut(canvasId, sourceData, palette) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || typeof Chart === 'undefined') return;
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: Object.keys(sourceData),
                datasets: [{ data: Object.values(sourceData), backgroundColor: palette, borderWidth: 0 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 9, font: { size: 10.5 } } }
                }
            }
        });
    }

    renderDoughnut('studentAttendanceChart', @json($studentAttendanceChartData), chartColors);
    renderDoughnut('teacherAttendanceChart', @json($staffAttendanceChartData), chartColors.slice(0, 5));

    // 3. Class-wise Attendance
    const classAttendanceCanvas = document.getElementById('classWiseAttendanceChart');
    if (classAttendanceCanvas && typeof Chart !== 'undefined') {
        new Chart(classAttendanceCanvas, {
            type: 'bar',
            data: {
                labels: @json($classAttendanceChart['labels'] ?? []),
                datasets: [
                    { label: 'Present', data: @json($classAttendanceChart['present'] ?? []), backgroundColor: '#10b981', borderRadius: 2 },
                    { label: 'Absent', data: @json($classAttendanceChart['absent'] ?? []), backgroundColor: '#ef4444', borderRadius: 2 },
                    { label: 'Half Day', data: @json($classAttendanceChart['halfday'] ?? []), backgroundColor: '#f59e0b', borderRadius: 2 },
                    { label: 'Late', data: @json($classAttendanceChart['late'] ?? []), backgroundColor: '#0284c7', borderRadius: 2 },
                    { label: 'Leave', data: @json($classAttendanceChart['leave'] ?? []), backgroundColor: '#8b5cf6', borderRadius: 2 },
                    { label: 'Holiday', data: @json($classAttendanceChart['holiday'] ?? []), backgroundColor: '#06b6d4', borderRadius: 2 },
                    { label: 'Not Marked', data: @json($classAttendanceChart['notMarked'] ?? []), backgroundColor: '#cbd5e1', borderRadius: 2 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10.5 } } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } }
                }
            }
        });
    }

    // 4. Async Task List
    if (typeof $ !== 'undefined') {
        $.ajax({
            url: '{{ url('task_list') }}',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { status: 1 }
        }).done(function (html) {
            var trimmed = (html || '').trim();
            if (!trimmed) {
                $('.todoList').html('<li class="dash-empty-state"><div class="empty-icon"><i class="fa fa-clipboard-check"></i></div><div class="empty-title">No Assigned Tasks Found</div><div class="empty-desc">All operational tasks are completed or none assigned.</div></li>');
            } else {
                $('.todoList').html(html);
            }
        }).fail(function () {
            $('.todoList').html('<li class="dash-empty-state"><div class="empty-icon"><i class="fa fa-exclamation-triangle text-danger"></i></div><div class="empty-title">Unable to Load Tasks</div><div class="empty-desc">Please refresh the page to retry.</div></li>');
        });

        $(document).on('click', '.task_delete', function (e) {
            e.preventDefault();
            var taskId = $(this).data('id');
            if (!taskId) return;
            if (!confirm('Are you sure you want to delete this task?')) return;
            $.ajax({
                type: 'POST',
                url: '{{ url('/delete/task') }}',
                data: { task_id: taskId },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function () {
                    $('#_' + taskId).fadeOut(200, function () {
                        $(this).remove();
                        if ($('.todoList .task-item').length === 0) {
                            $('.todoList').html('<li class="dash-empty-state"><div class="empty-icon"><i class="fa fa-clipboard-check"></i></div><div class="empty-title">No Assigned Tasks Found</div><div class="empty-desc">All operational tasks are completed or none assigned.</div></li>');
                        }
                    });
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Task Deleted Successfully.');
                    }
                }
            });
        });
    }
});
</script>
@endsection

@extends('layout.app')

@section('content')
@php
    $classRows = collect($classAttendanceRows ?? []);
    $teacherNotices = collect($teacherNotices ?? []);
@endphp

<div class="content-wrapper dashboard-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="dashboard-hero mb-3">
                <div>
                    <span class="dashboard-kicker">{{ now()->format('l, d M Y') }}</span>
                    <h1>Teacher Dashboard</h1>
                    <p>Only assigned classes, attendance, and notices relevant to your profile.</p>
                </div>
                <div class="hero-actions">
                    <a href="{{ url('attendance/mark') }}" class="btn btn-light btn-sm"><i class="fa fa-check-square mr-1"></i> Attendance</a>
                    <a href="{{ url('notice_board/view') }}" class="btn btn-outline-light btn-sm"><i class="fa fa-bullhorn mr-1"></i> Notices</a>
                </div>
            </div>

            <div class="row">
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <div class="metric-card metric-primary">
                        <span class="metric-icon"><i class="fa fa-chalkboard"></i></span>
                        <span class="metric-label">Assigned classes</span>
                        <strong>{{ number_format((int) ($assignedClassCount ?? 0)) }}</strong>
                        <small>Classes saved in your profile</small>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <div class="metric-card metric-success">
                        <span class="metric-icon"><i class="fa fa-users"></i></span>
                        <span class="metric-label">Assigned students</span>
                        <strong>{{ number_format((int) ($assignedStudentCount ?? 0)) }}</strong>
                        <small>Students in assigned classes</small>
                    </div>
                </div>
               
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <div class="metric-card metric-success">
                        <span class="metric-icon"><i class="fa fa-check-circle"></i></span>
                        <span class="metric-label">Present today</span>
                        <strong>{{ number_format((int) ($todayPresentCount ?? 0)) }}</strong>
                        <small>Attendance marked present</small>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <div class="metric-card metric-danger">
                        <span class="metric-icon"><i class="fa fa-times-circle"></i></span>
                        <span class="metric-label">Absent today</span>
                        <strong>{{ number_format((int) ($todayAbsentCount ?? 0)) }}</strong>
                        <small>Attendance marked absent</small>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <div class="metric-card metric-warning">
                        <span class="metric-icon"><i class="fa fa-hourglass-half"></i></span>
                        <span class="metric-label">Not marked</span>
                        <strong>{{ number_format((int) ($todayNotMarkedCount ?? 0)) }}</strong>
                        <small>Students not marked today</small>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-xl-2 mb-3">
                    <a class="metric-card metric-dark" href="{{ url('notice_board/view') }}">
                        <span class="metric-icon"><i class="fa fa-bullhorn"></i></span>
                        <span class="metric-label">Notices</span>
                        <strong>{{ number_format((int) ($teacherNoticeCount ?? 0)) }}</strong>
                        <small>School notices for you</small>
                    </a>
                </div>
            </div>

            <!-- <div class="row">
                    <div class="col-12 col-sm-12 col-xl-12 mb-3">
                        <h3 class="card-title">Staff Attendance Today</h3>
                    </div>
                    @php
                        $today = date('Y-m-d');

                        // Aaj ke attendance records
                        $todayAttendance = DB::table('attendance_marks')
                            ->where('session_id', Session::get('session_id'))
                            ->where('entity_type', 'staff')
                            ->whereDate('date', $today)
                            ->get();

                        // Present
                        $stafftodayPresentCount = $todayAttendance
                            ->whereIn('status', ['in', 'out', 'present'])
                            ->count();

                        // Absent
                        $stafftodayAbsentCount = $todayAttendance
                            ->whereIn('status', ['absent', 'a'])
                            ->count();

                        // Total staff
                        $totalStaff = DB::table('users')
                            ->where('role_id', 2)
                            ->whereNull('deleted_at')
                            ->count();

                        // Not marked
                        $todayNotMarkedCount = max(
                            0,
                            $totalStaff - $todayPresentCount - $todayAbsentCount
                        );
                    @endphp
                    <div class="col-6 col-sm-6 col-xl-2 mb-3">
                        <div class="metric-card metric-success">
                            <span class="metric-icon"><i class="fa fa-check-circle"></i></span>
                            <span class="metric-label">Present today</span>
                            <strong>{{ number_format((int) ($stafftodayPresentCount ?? 0)) }}</strong>
                            <small>Attendance marked present</small>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-xl-2 mb-3">
                        <div class="metric-card metric-danger">
                            <span class="metric-icon"><i class="fa fa-times-circle"></i></span>
                            <span class="metric-label">Absent today</span>
                            <strong>{{ number_format((int) ($todayAbsentCount ?? 0)) }}</strong>
                            <small>Attendance marked absent</small>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-xl-2 mb-3">
                        <div class="metric-card metric-warning">
                            <span class="metric-icon"><i class="fa fa-hourglass-half"></i></span>
                            <span class="metric-label">Not marked</span>
                            <strong>{{ number_format((int) ($todayNotMarkedCount ?? 0)) }}</strong>
                            <small>Students not marked today</small>
                        </div>
                    </div>
            </div>     -->
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <div class="card dashboard-card h-100">
                        <div class="card-header border-0">
                            <div>
                                <h3 class="card-title">Class-wise attendance today</h3>
                                <p>Attendance limited to your assigned classes</p>
                            </div>
                            <i class="fa fa-bar-chart card-header-icon"></i>
                        </div>
                        <div class="card-body">
                            @if($classRows->isEmpty())
                                <div class="empty-state"><i class="fa fa-info-circle"></i> No assigned class data found.</div>
                            @else
                                <div class="class-attendance-scroll">
                                    <div class="class-attendance-chart"><canvas id="teacherClassAttendanceChart"></canvas></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="card dashboard-card h-100">
                        <div class="card-header border-0">
                            <div>
                                <h3 class="card-title">Latest notices</h3>
                                <p>Notices relevant to your assigned classes</p>
                            </div>
                            <i class="fa fa-bell card-header-icon"></i>
                        </div>
                        <div class="card-body pt-0 dashboard-list">
                            @forelse($teacherNotices as $notice)
                                <a href="{{ url('notice_board/view') }}" class="feed-item">
                                    <span class="feed-dot bg-warning"></span>
                                    <span>{{ \Illuminate\Support\Str::limit(strip_tags((string) ($notice->title ?? 'Notice') . ' ' . (string) ($notice->message ?? '')), 70) }}</span>
                                    <small>{{ optional($notice->published_at)->format('d M') ?? 'View' }}</small>
                                </a>
                            @empty
                                <div class="empty-state"><i class="fa fa-bell-o"></i> No notices available.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card dashboard-card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Assigned classes</h3>
                                <p>Class-wise student and attendance snapshot</p>
                            </div>
                            <i class="fa fa-graduation-cap card-header-icon"></i>
                        </div>
                        <div class="card-body">
                            @if($classRows->isEmpty())
                                <div class="empty-state"><i class="fa fa-info-circle"></i> No assigned classes found.</div>
                            @else
                                <div class="attendance-grid">
                                    @foreach($classRows as $row)
                                        <div class="attendance-chip">
                                            <span>{{ $row['label'] ?? 'Class' }}</span>
                                            <strong>{{ number_format((int) ($row['total'] ?? 0)) }} students</strong>
                                            <div class="summary-row mt-2"><span>Present</span><b>{{ (int) ($row['present'] ?? 0) }}</b></div>
                                            <div class="summary-row"><span>Absent</span><b>{{ (int) ($row['absent'] ?? 0) }}</b></div>
                                            <div class="summary-row"><span>Not marked</span><b>{{ (int) ($row['not_marked'] ?? 0) }}</b></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.dashboard-page{background:#f4f7fb}
.dashboard-page .fa,
.dashboard-page .fab,
.dashboard-page .fas,
.dashboard-page .far,
.dashboard-page .fal,
.dashboard-page [class^="fa-"],
.dashboard-page [class*=" fa-"]{
    display:inline-block;
    line-height:1;
    font-style:normal;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
}
.dashboard-hero{background:linear-gradient(120deg,#233b75,#365bb6);border-radius:12px;padding:18px 20px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 8px 22px rgba(29,55,113,.14)}
.dashboard-kicker{font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.72}
.dashboard-hero h1{font-size:22px;line-height:1.15;margin:4px 0 2px;font-weight:700}
.dashboard-hero p{margin:0;font-size:13px;opacity:.82}
.hero-actions{display:flex;gap:8px;flex-wrap:wrap}
.hero-actions .btn{padding:.35rem .7rem;font-size:12px;line-height:1.35}
.metric-card{min-height:132px;border-radius:12px;background:#fff;display:flex;flex-direction:column;padding:15px 16px 14px;color:#26324b;box-shadow:0 4px 14px rgba(36,52,82,.06);border-left:4px solid;text-decoration:none!important;transition:transform .18s,box-shadow .18s;position:relative}
.metric-card:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(36,52,82,.11);color:#26324b}
.metric-primary{border-color:#4361ee}
.metric-success{border-color:#18a36b}
.metric-warning{border-color:#e2a112}
.metric-danger{border-color:#e05260}
.metric-dark{border-color:#26324b}
.metric-icon{position:absolute;right:18px;top:14px;width:32px;height:32px;border-radius:9px;background:#f0f3ff;color:#4361ee;display:grid;place-items:center;font-size:15px}
.metric-success .metric-icon{background:#e8f8f0;color:#18a36b}
.metric-warning .metric-icon{background:#fff6df;color:#d89500}
.metric-danger .metric-icon{background:#fff0f1;color:#e05260}
.metric-dark .metric-icon{background:#edf1f8;color:#26324b}
.metric-label{font-size:12px;color:#71809b;margin-bottom:6px;padding-right:36px}
.metric-card strong{font-size:22px;line-height:1.1}
.metric-card small{font-size:11px;line-height:1.35;color:#7d899e;margin-top:auto;padding-right:36px}
.dashboard-card{border:0;border-radius:12px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}
.dashboard-card .card-header{background:#fff;border-bottom:1px solid #edf0f5;padding:12px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.dashboard-card .card-title{float:none;margin:0;font-size:14px;font-weight:700;color:#2c3852;line-height:1.2}
.dashboard-card .card-header p{font-size:11px;color:#8792a5;margin:2px 0 0}
.card-header-icon{font-size:18px;color:#6781d8}
.dashboard-card .card-body{padding:12px 15px}
.class-attendance-chart{height:310px;min-width:680px}
.class-attendance-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
.dashboard-list{max-height:245px;overflow:auto}
.feed-item{display:flex;align-items:center;gap:8px;padding:10px 0;border-bottom:1px solid #edf0f5;color:#47546b;font-size:12px;line-height:1.35}
.feed-item:last-child{border-bottom:0}
.feed-item:hover{color:#315ab8}
.feed-item span:nth-child(2){overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.feed-item small{margin-left:auto;color:#8d98aa;font-size:11px}
.feed-dot{width:7px;height:7px;border-radius:50%;flex:0 0 auto}
.empty-state{color:#8b96a7;text-align:center;padding:34px 10px;font-size:12px}
.empty-state i{display:block;font-size:22px;margin-bottom:7px;color:#b8c1d0}
.summary-row{display:flex;justify-content:space-between;border-top:1px solid #edf0f5;padding:8px 0;color:#758198;font-size:12px}
.summary-row b{color:#34415a}
.attendance-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.attendance-chip{border:1px solid #edf0f5;border-radius:12px;padding:10px 12px;background:#fff}
.attendance-chip span{display:block;font-size:10px;color:#8792a5;text-transform:uppercase;letter-spacing:.03em;margin-bottom:3px}
.attendance-chip strong{font-size:16px;color:#23324d}
@media(max-width:575px){
    .dashboard-hero{padding:16px;align-items:flex-start;flex-direction:column}
    .dashboard-hero h1{font-size:20px}
    .class-attendance-chart{height:280px;min-width:650px}
    .dashboard-card .card-header,.dashboard-card .card-body{padding:11px 12px}
    .attendance-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('teacherClassAttendanceChart');
    if (!canvas) {
        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: @json($attendanceChartData['labels'] ?? []),
            datasets: [
                { label: 'Present', data: @json($attendanceChartData['present'] ?? []), backgroundColor: '#18a36b' },
                { label: 'Absent', data: @json($attendanceChartData['absent'] ?? []), backgroundColor: '#e05260' },
                { label: 'Not Marked', data: @json($attendanceChartData['notMarked'] ?? []), backgroundColor: '#cbd3df' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
});
</script>
@endsection

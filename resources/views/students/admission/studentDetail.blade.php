@extends('layout.app')
@section('content')
@php
    $student = $data;
    $studentName = trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
    $sessionLabel = trim(($student->from_year ?? '').'-'.($student->to_year ?? ''), '-');
    $imageBase = rtrim((string) env('IMAGE_SHOW_PATH'), '/');
    $photo = $imageBase.'/profile/'.($student->image ?? '');
    $defaultPhoto = $imageBase.'/default/user_image.jpg';
@endphp
<style>
    .profile-photo { width:155px; height:180px; object-fit:cover; border-radius:4px; border:1px solid #dee2e6; padding:3px; background:#fff; }
    .profile-name { font-size:27px; font-weight:700; color:#202334; margin-bottom:3px; }
    .profile-id { color:#007bff; font-weight:600; }
    .profile-detail { display:flex; gap:10px; margin-bottom:13px; min-height:38px; }
    .profile-detail i { width:30px; height:30px; border-radius:3px; background:#e9f5ff; color:#007bff; display:flex; align-items:center; justify-content:center; flex:none; }
    .profile-detail small { display:block; color:#7b8490; line-height:1.1; }
    .profile-detail strong { color:#252a34; font-weight:600; overflow-wrap:anywhere; }
    .status-panel { background:#f8f9fa; border:1px solid #dee2e6; border-radius:4px; padding:15px; height:100%; }
    .status-badge { display:block; text-align:center; border-radius:3px; padding:6px; font-weight:600; }
    .status-action { width:100%; margin-top:10px; font-weight:700; }
    .summary-card { height:100%; }
    .summary-icon { width:42px; height:42px; border-radius:4px; color:#fff; display:flex; align-items:center; justify-content:center; font-size:18px; }
    .metric-row { display:flex; justify-content:space-between; padding:7px 0; border-bottom:1px dashed #e5e8ec; }
    .metric-row:last-child { border-bottom:0; }
    .section-card .card-header { background:#fff; border-bottom:1px solid #edf0f3; }
    .section-card .card-title { font-weight:700; color:#252a34; }
    .exam-results-table { min-width:900px; }
    .exam-subjects { min-width:220px; }
    .exam-subject-row { display:flex; justify-content:space-between; gap:12px; padding:2px 0; border-bottom:1px dashed #dfe3e7; }
    .exam-subject-row:last-child { border-bottom:0; }
    .exam-subject-name { font-weight:600; color:#343a40; }
    .exam-subject-marks { white-space:nowrap; color:#495057; }
    .info-table th { width:19%; background:#f7f8fa; color:#697480; font-weight:600; }
    .info-table td { color:#242a31; }
    .profile-progress { height:7px; border-radius:6px; background:#eceff3; overflow:hidden; }
    .profile-progress span { display:block; height:100%; border-radius:6px; }
    .empty-state { color:#8a929b; text-align:center; padding:22px; }
    .fee-ledger-table { min-width:1100px; }
    .fee-ledger-table > thead > tr > th { vertical-align:middle; white-space:nowrap; background:#f4f6f9; }
    .fee-ledger-table > tbody > tr > td { vertical-align:top; padding-top:12px; padding-bottom:12px; }
    .fee-ledger-table .amount-column { width:105px; white-space:nowrap; }
    .fee-head-column { min-width:165px; }
    .fee-head-name { display:block; font-size:15px; margin-bottom:6px; }
    .fee-history { min-width:470px; padding:6px 10px !important; }
    .fee-history-table { width:100%; table-layout:fixed; font-size:12px; color:#4f5964; }
    .fee-history-table th { padding:2px 6px 5px; border-bottom:1px solid #d9dee3; color:#6c757d; font-weight:600; white-space:nowrap; }
    .fee-history-table td { padding:6px; border-bottom:1px dashed #e2e6ea; vertical-align:middle; overflow-wrap:anywhere; }
    .fee-history-table tr:last-child td { border-bottom:0; }
    .fee-history-table .history-date { width:92px; }
    .fee-history-table .history-receipt { width:76px; }
    .fee-history-table .history-mode { width:78px; }
    .fee-history-table .history-amount { width:78px; text-align:right; white-space:nowrap; }
    .fee-ledger-total td { background:#f4f6f9; border-top:2px solid #ced4da; font-weight:700; vertical-align:middle !important; }
    @media(max-width:767px) {
        .profile-photo { width:115px; height:135px; margin-bottom:12px; }
        .profile-name { font-size:22px; }
        .profile-actions { margin-top:12px; }
    }

    /* Match the admin dashboard visual language. */
    .student-profile-page { background:#f4f7fb; color:#26324b; }
    .student-profile-page .container-fluid { max-width:1600px; }
    .profile-hero { background:linear-gradient(120deg,#233b75,#365bb6); border-radius:12px; padding:18px 20px; color:#fff; display:flex; align-items:center; justify-content:space-between; gap:16px; box-shadow:0 8px 22px rgba(29,55,113,.14); }
    .profile-hero-main { display:flex; align-items:center; gap:13px; min-width:0; }
    .profile-hero-icon { width:44px; height:44px; flex:0 0 44px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.14); font-size:20px; }
    .profile-kicker { display:block; font-size:11px; letter-spacing:.08em; text-transform:uppercase; opacity:.72; }
    .profile-hero h1 { margin:3px 0 2px; font-size:22px; line-height:1.15; font-weight:700; }
    .profile-hero p { margin:0; font-size:13px; opacity:.82; }
    .profile-hero-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; }
    .profile-hero-actions .btn { padding:.4rem .75rem; font-size:12px; line-height:1.35; border-radius:7px; }
    .profile-quick-nav { display:flex; gap:8px; overflow-x:auto; padding:1px 1px 10px; scrollbar-width:none; }
    .profile-quick-nav::-webkit-scrollbar { display:none; }
    .profile-quick-nav a { flex:0 0 auto; display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border:1px solid #dfe5ef; border-radius:9px; background:#fff; color:#526079; font-size:12px; font-weight:600; box-shadow:0 2px 7px rgba(36,52,82,.04); text-decoration:none; }
    .profile-quick-nav a:hover { border-color:#365bb6; color:#365bb6; }
    .profile-overview { border:0; border-radius:12px; box-shadow:0 4px 14px rgba(36,52,82,.07); overflow:hidden; }
    .profile-overview .card-body { padding:20px; }
    .profile-photo { border:4px solid #fff; padding:0; border-radius:14px; box-shadow:0 5px 18px rgba(35,59,117,.16); }
    .profile-name { color:#26324b; }
    .profile-id { display:inline-flex; padding:4px 9px; border-radius:6px; background:#eef3ff; color:#365bb6; }
    .profile-detail i { border-radius:9px; background:#edf2ff; color:#365bb6; }
    .status-panel { border:0; border-radius:10px; background:#f6f8fc; }
    .status-badge, .status-action { border-radius:7px; }
    .student-profile-page .summary-card { min-height:180px; border:0; border-left:4px solid #4361ee; border-radius:12px; box-shadow:0 4px 14px rgba(36,52,82,.06); }
    .student-profile-page .row > div:nth-child(2) .summary-card { border-left-color:#20a86b; }
    .student-profile-page .row > div:nth-child(3) .summary-card { border-left-color:#f59e0b; }
    .student-profile-page .row > div:nth-child(4) .summary-card { border-left-color:#ef476f; }
    .summary-icon { border-radius:10px; background:#edf2ff!important; color:#365bb6; }
    .section-card { border:0; border-radius:12px; box-shadow:0 4px 14px rgba(36,52,82,.06); overflow:hidden; margin-bottom:16px; }
    .section-card .card-header { min-height:54px; padding:13px 16px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .section-card .card-title { font-size:16px; }
    .section-card .card-title i { width:29px; height:29px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; background:#edf2ff; color:#365bb6!important; margin-right:5px; }
    .student-profile-page .table thead th { border-top:0; border-bottom:1px solid #e2e7ef; background:#f7f9fc; color:#65718a; font-size:11px; letter-spacing:.035em; text-transform:uppercase; white-space:nowrap; }
    .student-profile-page .table td { vertical-align:middle; border-top-color:#edf0f5; }
    .student-profile-page .badge { border-radius:6px; padding:.32em .55em; }
    .student-profile-page .progress { background:#e9edf5; border-radius:8px; overflow:hidden; }

    @media(max-width:767px) {
        .student-profile-page .content { padding:10px 8px 78px!important; }
        .student-profile-page .container-fluid { padding:0; }
        .profile-hero { border-radius:0 0 18px 18px; margin:-10px -8px 12px!important; padding:14px 14px 16px; align-items:flex-start; }
        .profile-hero-icon { width:38px; height:38px; flex-basis:38px; border-radius:10px; }
        .profile-hero h1 { font-size:18px; }
        .profile-hero p { font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:185px; }
        .profile-hero-actions { gap:6px; }
        .profile-hero-actions .btn { width:38px; height:38px; padding:0; display:flex; align-items:center; justify-content:center; border-radius:10px; }
        .profile-hero-actions .action-label { display:none; }
        .profile-quick-nav { margin:0 -1px 2px; padding-bottom:9px; }
        .profile-quick-nav a { padding:8px 11px; border-radius:10px; font-size:11px; }
        .profile-overview { border-radius:14px; }
        .profile-overview .card-body { padding:16px 14px; }
        .profile-overview .row.align-items-stretch { align-items:flex-start!important; }
        .profile-overview .col-md-2 { width:92px; flex:0 0 92px; padding-right:8px; }
        .profile-overview .col-md-7 { width:calc(100% - 92px); flex:0 0 calc(100% - 92px); padding-left:5px; }
        .profile-overview .col-md-3 { width:100%; flex:0 0 100%; margin-top:12px; }
        .profile-photo { width:78px; height:92px; margin:0; border-radius:12px; }
        .profile-name { font-size:19px; line-height:1.2; }
        .profile-id { font-size:11px; margin-bottom:10px!important; }
        .profile-detail { gap:7px; margin-bottom:8px; min-height:0; }
        .profile-detail i { width:25px; height:25px; font-size:11px; }
        .profile-detail small { font-size:10px; }
        .profile-detail strong { display:block; font-size:12px; line-height:1.2; }
        .profile-overview .col-md-7 .row > .col-md-6:first-child .profile-detail:nth-child(n+3),
        .profile-overview .col-md-7 .row > .col-md-6:nth-child(2) .profile-detail:nth-child(n+2),
        .profile-overview .col-md-7 .row > .col-12 { display:none; }
        .status-panel { display:grid; grid-template-columns:1fr 1fr; gap:7px; padding:10px; }
        .status-panel .status-badge, .status-panel .status-action { margin:0; min-height:38px; display:flex; align-items:center; justify-content:center; }
        .status-panel .metric-row { display:block; margin:0!important; padding:8px 9px; background:#fff; border:1px solid #edf0f5; border-radius:8px; }
        .status-panel .metric-row span { display:block; font-size:9px; text-transform:uppercase; color:#7b8490; }
        .status-panel .metric-row b { display:block; font-size:12px; overflow-wrap:anywhere; }
        .student-profile-page .summary-card { min-height:145px; margin-bottom:0; }
        .student-profile-page .summary-card .card-body { padding:12px; }
        .student-profile-page .summary-card h6 { font-size:12px; }
        .summary-icon { width:34px; height:34px; font-size:14px; }
        .metric-row { padding:5px 0; font-size:11px; }
        .section-card { border-radius:14px; margin-bottom:12px; }
        .section-card .card-header { min-height:49px; padding:10px 12px; }
        .section-card .card-title { font-size:14px; }
        .section-card .card-title i { width:26px; height:26px; }
        .section-card .card-body:not(.p-0) { padding:13px; }
        .exam-results-table { min-width:720px; }
        .exam-subjects { min-width:205px; }
        .fee-ledger-table { min-width:960px; }
        .table-responsive { -webkit-overflow-scrolling:touch; scrollbar-width:thin; }
        .info-table { min-width:760px; }
        .student-profile-page .btn { min-height:34px; }
        .modal-dialog { margin:10px; }
    }
</style>

<div class="content-wrapper student-profile-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="profile-hero mb-3">
                <div class="profile-hero-main">
                    <span class="profile-hero-icon"><i class="fa fa-user-circle"></i></span>
                    <div>
                        <span class="profile-kicker">Student Management</span>
                        <h1>Student Profile</h1>
                        <p>{{ $studentName ?: 'Student' }} · {{ $student->class_name ?? 'Class not assigned' }}</p>
                    </div>
                </div>
                <div class="profile-hero-actions">
                    <a href="{{ url('admissionView?from_profile=1') }}" class="btn btn-outline-light" title="Back to students"><i class="fa fa-arrow-left mr-1"></i><span class="action-label">Back</span></a>
                    <a href="{{ route('student.profile.excel', $student->id) }}" class="btn btn-light text-success {{ Helper::permissioncheck(3)->print ? '' : 'd-none' }}" title="Download detailed Excel report"><i class="fa fa-file-excel-o mr-1"></i><span class="action-label">Download Report</span></a>
                </div>
            </div>

            <nav class="profile-quick-nav" aria-label="Student profile sections">
                <a href="#profile-overview"><i class="fa fa-user"></i> Overview</a>
                <a href="#fee-record"><i class="fa fa-inr"></i> Fees</a>
                <a href="#attendance-record"><i class="fa fa-calendar-check-o"></i> Attendance</a>
                <a href="#exam-record"><i class="fa fa-bar-chart"></i> Exams</a>
                <a href="#personal-record"><i class="fa fa-address-card"></i> Details</a>
                <a href="#document-record"><i class="fa fa-file-text-o"></i> Documents</a>
            </nav>

            <div class="card profile-overview" id="profile-overview">
                <div class="card-body">
                    <div class="row align-items-stretch">
                        <div class="col-md-2 text-center">
                            <img src="{{ $photo }}" class="profile-photo" alt="{{ $studentName }}" onerror="this.src='{{ $defaultPhoto }}'">
                        </div>
                        <div class="col-md-7">
                            <div class="profile-name">{{ $studentName ?: '-' }}</div>
                            <div class="profile-id mb-3">Admission No. {{ $student->admissionNo ?? '-' }} @if(!empty($student->roll_no)) | Roll No. {{ $student->roll_no }} @endif</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="profile-detail"><i class="fa fa-graduation-cap"></i><div><small>Class / Course</small><strong>{{ $student->class_name ?? '-' }}</strong></div></div>
                                    <div class="profile-detail"><i class="fa fa-calendar"></i><div><small>Date of Birth</small><strong>{{ !empty($student->dob) ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}</strong></div></div>
                                    <div class="profile-detail"><i class="fa fa-male"></i><div><small>Father's Name</small><strong>{{ $student->father_name ?? '-' }}</strong></div></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="profile-detail"><i class="fa fa-calendar-check-o"></i><div><small>Date of Admission</small><strong>{{ !empty($student->admission_date) ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : '-' }}</strong></div></div>
                                    <div class="profile-detail"><i class="fa fa-phone"></i><div><small>Contact</small><strong>{{ $student->mobile ?: ($student->father_mobile ?? '-') }}</strong></div></div>
                                    <div class="profile-detail"><i class="fa fa-envelope"></i><div><small>Email</small><strong>{{ $student->email ?? '-' }}</strong></div></div>
                                </div>
                                <div class="col-12">
                                    <div class="profile-detail mb-0"><i class="fa fa-map-marker"></i><div><small>Address</small><strong>{{ collect([$student->address, $student->village_city, $student->city_name, $student->state_name, $student->pincode])->filter()->implode(', ') ?: '-' }}</strong></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="status-panel">
                                <span id="studentStatusBadge" class="status-badge {{ (int)$student->status === 1 ? 'bg-success' : 'bg-danger' }}">{{ (int)$student->status === 1 ? 'Active Student' : 'Inactive Student' }}</span>
                                <button type="button"
                                        id="studentStatusAction"
                                        class="btn status-action {{ (int)$student->status === 1 ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        data-id="{{ $student->id }}"
                                        data-status="{{ (int)$student->status === 1 ? 0 : 1 }}">
                                    <i class="fa {{ (int)$student->status === 1 ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                    <span>{{ (int)$student->status === 1 ? 'Deactivate Student' : 'Activate Student' }}</span>
                                </button>
                                <div class="metric-row mt-2"><span>Student ID</span><b>{{ $student->attendance_unique_id ?: ($student->unique_system_id ?: '-') }}</b></div>
                                <div class="metric-row"><span>Session</span><b>{{ $sessionLabel ?: '-' }}</b></div>
                                <div class="metric-row"><span>Medium</span><b>{{ $student->medium ?? '-' }}</b></div>
                                <div class="metric-row"><span>Gender</span><b>{{ $student->genderName ?? '-' }}</b></div>
                                <div class="metric-row"><span>Category</span><b>{{ $student->category ?? '-' }}</b></div>
                                <div class="metric-row"><span>Blood Group</span><b>{{ $student->blood_group ?? '-' }}</b></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-sm-6 mb-3">
                    <div class="card card-outline card-primary summary-card"><div class="card-body">
                        <div class="d-flex align-items-center mb-2"><div class="summary-icon bg-primary"><i class="fa fa-inr"></i></div><h6 class="ml-2 mb-0 font-weight-bold">Fees Summary</h6></div>
                        <div class="metric-row"><span>Total Fees</span><b>{{ number_format($assignedFees,2) }}</b></div>
                        <div class="metric-row text-success"><span>Paid Fees</span><b>{{ number_format($paidFees,2) }}</b></div>
                        <div class="metric-row text-danger"><span>Due Fees</span><b>{{ number_format($dueFees,2) }}</b></div>
                        <div class="profile-progress mt-3"><span class="bg-primary" style="width:{{ $feePaidPercentage }}%"></span></div>
                        <small class="text-primary">{{ number_format($feePaidPercentage,2) }}% Paid</small>
                    </div></div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <div class="card card-outline card-info summary-card"><div class="card-body">
                        <div class="d-flex align-items-center mb-2"><div class="summary-icon bg-primary"><i class="fa fa-calendar-check-o"></i></div><h6 class="ml-2 mb-0 font-weight-bold">Attendance</h6></div>
                        <div class="metric-row"><span>Total Records</span><b>{{ $attendanceTotal }}</b></div>
                        <div class="metric-row text-success"><span>Present</span><b>{{ $attendancePresent }}</b></div>
                        <div class="metric-row text-danger"><span>Absent</span><b>{{ $attendanceAbsent }}</b></div>
                        <div class="profile-progress mt-3"><span class="bg-primary" style="width:{{ $attendancePercentage }}%"></span></div>
                        <small class="text-primary">{{ number_format($attendancePercentage,2) }}% Attendance</small>
                    </div></div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <div class="card card-outline card-success summary-card"><div class="card-body">
                        <div class="d-flex align-items-center mb-2"><div class="summary-icon bg-success"><i class="fa fa-line-chart"></i></div><h6 class="ml-2 mb-0 font-weight-bold">Examination</h6></div>
                        <div class="metric-row"><span>Exams Recorded</span><b>{{ $examResults->count() }}</b></div>
                        <div class="metric-row"><span>Marks Entries</span><b>{{ $examResults->sum('subject_count') }}</b></div>
                        <div class="metric-row"><span>Overall</span><b>{{ $examResults->count() ? number_format($overallExamPercentage,2).'%' : '-' }}</b></div>
                        <div class="metric-row"><span>Exam Roll No.</span><b>{{ $student->exam_roll_no ?? '-' }}</b></div>
                    </div></div>
                </div>
                <div class="col-lg-3 col-sm-6 mb-3">
                    <div class="card card-outline card-warning summary-card"><div class="card-body">
                        <div class="d-flex align-items-center mb-2"><div class="summary-icon bg-warning"><i class="fa fa-folder-open"></i></div><h6 class="ml-2 mb-0 font-weight-bold">Records</h6></div>
                        <div class="metric-row"><span>Documents</span><b>{{ collect($getDocuments)->count() }}</b></div>
                        <div class="metric-row"><span>Siblings</span><b>{{ collect($siblings)->count() }}</b></div>
                        <div class="metric-row"><span>Assigned Subjects</span><b>{{ $assignedSubjects->count() }}</b></div>
                        <div class="metric-row"><span>Admission Type</span><b>{{ (int)$student->admission_type_id === 2 ? 'RTE' : 'Non RTE' }}</b></div>
                    </div></div>
                </div>
            </div>

            {{-- Issued Uniform & Material Section --}}
            @php
                $hasInventoryTable = false;
                $studentInventory = null;
                try {
                    $hasInventoryTable = \Illuminate\Support\Facades\Schema::hasTable('student_inventories');
                    if ($hasInventoryTable) {
                        $studentInventory = \Illuminate\Support\Facades\DB::table('student_inventories')
                            ->where('admission_id', $student->id)
                            ->whereNull('deleted_at')
                            ->first();
                    }
                } catch (\Exception $e) {
                    $hasInventoryTable = false;
                    $studentInventory = null;
                }
            @endphp
            @if($hasInventoryTable)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-outline card-primary section-card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h3 class="card-title font-weight-bold text-dark mb-0">
                                <i class="fa fa-cubes text-primary"></i> Uniform & Material Issued Section
                            </h3>
                            <div class="card-tools">
                                <a href="{{ url('student_inventory/bulk_issue?class_type_id='.$student->class_type_id) }}" class="btn btn-success btn-sm font-weight-bold">
                                    <i class="fa fa-list-alt"></i> Bulk Issue Record
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Shirt 1 --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <div class="text-uppercase text-muted font-weight-bold small mb-1"><i class="fa fa-tag text-primary"></i> Shirt 1 (Color & Size)</div>
                                        @if($studentInventory && !empty($studentInventory->shirt1_color) && !empty($studentInventory->shirt1_size))
                                            <h5 class="font-weight-bold text-success mb-1">{{ $studentInventory->shirt1_color }}</h5>
                                            <div><strong>Size/Number:</strong> {{ $studentInventory->shirt1_size }}</div>
                                            <div class="small text-muted"><strong>Issue Date:</strong> {{ !empty($studentInventory->shirt1_date) ? date('d M Y', strtotime($studentInventory->shirt1_date)) : '-' }}</div>
                                        @else
                                            <span class="badge badge-secondary p-2"><i class="fa fa-times"></i> Not Issued</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Shirt 2 --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <div class="text-uppercase text-muted font-weight-bold small mb-1"><i class="fa fa-tags text-danger"></i> Shirt 2 (Color & Size)</div>
                                        @if($studentInventory && !empty($studentInventory->shirt2_color) && !empty($studentInventory->shirt2_size))
                                            <h5 class="font-weight-bold text-success mb-1">{{ $studentInventory->shirt2_color }}</h5>
                                            <div><strong>Size/Number:</strong> {{ $studentInventory->shirt2_size }}</div>
                                            <div class="small text-muted"><strong>Issue Date:</strong> {{ !empty($studentInventory->shirt2_date) ? date('d M Y', strtotime($studentInventory->shirt2_date)) : '-' }}</div>
                                        @else
                                            <span class="badge badge-secondary p-2"><i class="fa fa-times"></i> Not Issued</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Bag --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <div class="text-uppercase text-muted font-weight-bold small mb-1"><i class="fa fa-shopping-bag text-purple" style="color:#6f42c1;"></i> School Bag</div>
                                        @if($studentInventory && (int)($studentInventory->bag_issued ?? 0) === 1)
                                            <span class="badge badge-success p-2 mb-1"><i class="fa fa-check"></i> Issued</span>
                                            <div class="small text-muted"><strong>Issue Date:</strong> {{ !empty($studentInventory->bag_date) ? date('d M Y', strtotime($studentInventory->bag_date)) : '-' }}</div>
                                        @else
                                            <span class="badge badge-secondary p-2"><i class="fa fa-times"></i> Not Issued</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Study Material / Module --}}
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <div class="text-uppercase text-muted font-weight-bold small mb-1"><i class="fa fa-book text-info"></i> Module / Study Material</div>
                                        @if($studentInventory && (int)($studentInventory->material_issued ?? 0) === 1)
                                            <span class="badge badge-success p-2 mb-1"><i class="fa fa-check"></i> Issued</span>
                                            <div class="small text-muted"><strong>Issue Date:</strong> {{ !empty($studentInventory->material_date) ? date('d M Y', strtotime($studentInventory->material_date)) : '-' }}</div>
                                        @else
                                            <span class="badge badge-secondary p-2"><i class="fa fa-times"></i> Not Issued</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Meta Info & Remarks --}}
                                <div class="col-12 mt-2">
                                    <div class="p-3 bg-white border rounded d-flex flex-wrap justify-content-between align-items-center">
                                        <div>
                                            <strong>Issued By:</strong> {{ $studentInventory->issued_by ?? '-' }} |
                                            <strong>Issue Date:</strong> {{ !empty($studentInventory->issue_date) ? date('d M Y', strtotime($studentInventory->issue_date)) : '-' }}
                                        </div>
                                        <div>
                                            <strong>Remarks:</strong> {{ !empty($studentInventory->remarks) ? $studentInventory->remarks : 'None' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-secondary section-card" id="fee-record">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-inr text-success"></i> Fee Head-wise Payment Record</h3>
                            <div class="card-tools"><span class="badge badge-primary">{{ $feeHeadLedger->count() }} heads assigned</span></div>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped mb-0 fee-ledger-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="fee-head-column">Fee Head</th>
                                        <th class="text-right amount-column">Assigned</th>
                                        <th class="text-right amount-column">Discount</th>
                                        <th class="text-right amount-column">Paid</th>
                                        <th class="text-right amount-column">Fine</th>
                                        <th class="text-right amount-column">Due</th>
                                        <th>Payment History</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($feeHeadLedger as $head)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="font-weight-bold fee-head-name">{{ $head->name }}</span>
                                            @if($head->due_amount <= 0)
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($head->paid_amount > 0 || $head->discount > 0)
                                                <span class="badge badge-warning">Partially Paid</span>
                                            @else
                                                <span class="badge badge-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($head->assigned_amount,2) }}</td>
                                        <td class="text-right text-info">{{ number_format($head->discount,2) }}</td>
                                        <td class="text-right text-success font-weight-bold">{{ number_format($head->paid_amount,2) }}</td>
                                        <td class="text-right text-warning">{{ number_format($head->fine_amount,2) }}</td>
                                        <td class="text-right text-danger font-weight-bold">{{ number_format($head->due_amount,2) }}</td>
                                        <td class="fee-history">
                                            @if($head->payments->isNotEmpty())
                                                <table class="fee-history-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="history-date">Date</th>
                                                            <th class="history-receipt">Receipt</th>
                                                            <th class="history-mode">Mode</th>
                                                            <th class="history-amount">Paid</th>
                                                            <th class="history-amount">Discount</th>
                                                            <th class="history-amount">Fine</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($head->payments as $payment)
                                                            <tr>
                                                                <td>{{ !empty($payment->date) ? \Carbon\Carbon::parse($payment->date)->format('d M Y') : '-' }}</td>
                                                                <td>{{ $payment->receipt_no ?: '-' }}</td>
                                                                <td>{{ $payment->payment_mode ?: '-' }}</td>
                                                                <td class="history-amount text-success font-weight-bold">{{ number_format((float)$payment->paid_amount,2) }}</td>
                                                                <td class="history-amount">{{ number_format((float)$payment->discount,2) }}</td>
                                                                <td class="history-amount">{{ number_format((float)$payment->installment_fine,2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="text-muted py-2">No payment received for this fee head.</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="empty-state">No fee heads are assigned to this student.</td></tr>
                                @endforelse
                                </tbody>
                                @if($feeHeadLedger->isNotEmpty())
                                    <tfoot>
                                        <tr class="fee-ledger-total">
                                            <td colspan="2" class="text-right">Total</td>
                                            <td class="text-right">{{ number_format($feeHeadLedger->sum('assigned_amount'),2) }}</td>
                                            <td class="text-right text-info">{{ number_format($feeHeadLedger->sum('discount'),2) }}</td>
                                            <td class="text-right text-success">{{ number_format($feeHeadLedger->sum('paid_amount'),2) }}</td>
                                            <td class="text-right text-warning">{{ number_format($feeHeadLedger->sum('fine_amount'),2) }}</td>
                                            <td class="text-right text-danger">{{ number_format($feeHeadLedger->sum('due_amount'),2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card card-outline card-secondary section-card" id="attendance-record">
                        <div class="card-header"><h3 class="card-title"><i class="fa fa-calendar text-primary"></i> Recent Attendance</h3></div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped mb-0">
                                <thead><tr><th>Date</th><th>In Time</th><th>Out Time</th><th>Status</th></tr></thead>
                                <tbody>
                                @forelse($recentAttendance as $attendance)
                                    @php
                                        $attendanceName = $attendance->profile_status ?? '-';
                                        $attendanceKey = $attendance->profile_status_key ?? '';
                                        $attendanceBadge = $attendanceKey === 'absent' ? 'badge-danger'
                                            : (in_array($attendanceKey, ['late','early_out','halfday'], true) ? 'badge-warning'
                                            : (in_array($attendanceKey, ['holiday','event','leave'], true) ? 'badge-info' : 'badge-success'));
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</td>
                                        <td>{{ $attendance->time ?: '-' }}</td><td>{{ $attendance->out_time ?: '-' }}</td>
                                        <td><span class="badge {{ $attendanceBadge }}">{{ $attendanceName }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="empty-state">No attendance records found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card card-outline card-success section-card" id="exam-record">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-bar-chart text-success"></i> Examination Results</h3>
                            <div class="card-tools"><span class="badge badge-success">{{ $examResults->count() }} exams</span></div>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped mb-0 exam-results-table">
                                <thead><tr><th>Exam</th><th>Date</th><th>Subject Marks (Obtained/Total)</th><th class="text-right">Obtained</th><th class="text-right">Maximum</th><th class="text-right">Percentage</th><th class="text-center">Details</th></tr></thead>
                                <tbody>
                                @forelse($examResults->take(8) as $result)
                                    <tr>
                                        <td class="font-weight-bold">{{ $result->name }}</td>
                                        <td>{{ !empty($result->date) ? \Carbon\Carbon::parse($result->date)->format('d M Y') : '-' }}</td>
                                        <td class="exam-subjects">
                                            @forelse($result->subjects as $subject)
                                                <div class="exam-subject-row">
                                                    <span class="exam-subject-name">{{ $subject->name }}</span>
                                                    <span class="exam-subject-marks">
                                                        {{ $subject->is_numeric ? number_format((float) $subject->marks, 2) : $subject->marks }}/{{ number_format((float) $subject->maximum, 2) }}
                                                    </span>
                                                </div>
                                            @empty
                                                <span class="text-muted">No subject marks</span>
                                            @endforelse
                                        </td>
                                        <td class="text-right">{{ number_format($result->obtained,2) }}</td>
                                        <td class="text-right">{{ number_format($result->maximum,2) }}</td>
                                        <td class="text-right"><span class="badge {{ $result->percentage >= 60 ? 'badge-success' : ($result->percentage >= 33 ? 'badge-warning' : 'badge-danger') }}">{{ number_format($result->percentage,2) }}%</span></td>
                                        <td class="text-center">
                                            <form action="{{ url('exam_wise_report') }}" method="POST" target="_blank" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $result->exam_id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $student->class_type_id }}">
                                                <input type="hidden" name="admission_id" value="{{ $student->id }}">
                                                @foreach($result->subjects->pluck('id')->filter()->unique() as $subjectId)
                                                    <input type="hidden" name="subject_id[]" value="{{ $subjectId }}">
                                                @endforeach
                                                <button type="submit" class="btn btn-primary btn-xs text-nowrap" title="View this student's complete exam report">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="empty-state">No marks have been entered for this student in the current session.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-outline card-info section-card">
                        <div class="card-header"><h3 class="card-title"><i class="fa fa-book text-info"></i> Subject Performance</h3></div>
                        <div class="card-body">
                            @forelse($subjectPerformance as $subject)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between"><span class="font-weight-bold">{{ $subject->name }}</span><span>{{ number_format($subject->percentage,2) }}%</span></div>
                                    <div class="progress progress-sm mt-1"><div class="progress-bar {{ $subject->percentage >= 60 ? 'bg-success' : ($subject->percentage >= 33 ? 'bg-warning' : 'bg-danger') }}" style="width:{{ min(100,$subject->percentage) }}%"></div></div>
                                    <small class="text-muted">Based on {{ $subject->exams }} exam(s)</small>
                                </div>
                            @empty
                                <div class="empty-state">Subject performance will appear after marks are entered.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary section-card" id="personal-record">
                <div class="card-header"><h3 class="card-title"><i class="fa fa-address-card text-info"></i> Personal, Guardian & Official Information</h3></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered info-table mb-0">
                        <tr><th>Student Mobile</th><td>{{ $student->mobile ?? '-' }}</td><th>Aadhaar</th><td>{{ $student->aadhaar ?? '-' }}</td><th>APAAR ID</th><td>{{ $student->apaar_id ?? '-' }}</td></tr>
                        <tr><th>Father</th><td>{{ $student->father_name ?? '-' }}</td><th>Father Mobile</th><td>{{ $student->father_mobile ?? '-' }}</td><th>Occupation</th><td>{{ $student->father_occupation ?? '-' }}</td></tr>
                        <tr><th>Mother</th><td>{{ $student->mother_name ?? '-' }}</td><th>Mother Mobile</th><td>{{ $student->mother_mob ?? '-' }}</td><th>Occupation</th><td>{{ $student->mother_occupation ?? '-' }}</td></tr>
                        <tr><th>Guardian</th><td>{{ $student->guardian_name ?? '-' }}</td><th>Guardian Mobile</th><td>{{ $student->guardian_mobile ?? '-' }}</td><th>Annual Income</th><td>{{ $student->family_annual_income ?? '-' }}</td></tr>
                        <tr><th>Religion</th><td>{{ $student->religion ?? '-' }}</td><th>Caste / Category</th><td>{{ $student->caste_category ?: ($student->category ?? '-') }}</td><th>Jan Aadhaar</th><td>{{ $student->jan_aadhaar ?? '-' }}</td></tr>
                        <tr><th>Bank</th><td>{{ $student->bank_name ?? '-' }}</td><th>Account No.</th><td>{{ $student->bank_account ?? '-' }}</td><th>IFSC</th><td>{{ $student->ifsc ?? '-' }}</td></tr>
                        <tr><th>Transport</th><td>{{ in_array(strtolower((string)$student->transport), ['1','yes'], true) ? 'Yes' : 'No' }}</td><th>Bus / Route</th><td>{{ collect([$student->bus_number,$student->bus_route])->filter()->implode(' / ') ?: '-' }}</td><th>Stoppage</th><td>{{ $student->stoppage ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-outline card-secondary section-card">
                        <div class="card-header"><h3 class="card-title"><i class="fa fa-history text-primary"></i> Promotion History</h3></div>
                        <div class="card-body p-0 table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Session</th><th>Class</th><th>Roll No.</th><th>Status</th></tr></thead><tbody>
                        @forelse($promotion_history as $history)
                            <tr><td>{{ collect([$history->from_year,$history->to_year])->filter()->implode('-') ?: '-' }}</td><td>{{ $history->class_name ?? '-' }}</td><td>{{ $history->roll_no ?? '-' }}</td><td><span class="badge badge-info">{{ (int)$history->status === 1 ? 'Active' : 'Inactive' }}</span></td></tr>
                        @empty<tr><td colspan="4" class="empty-state">No promotion history.</td></tr>@endforelse
                        </tbody></table></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-outline card-secondary section-card">
                        <div class="card-header"><h3 class="card-title"><i class="fa fa-users text-warning"></i> Sibling Information</h3></div>
                        <div class="card-body p-0 table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Name</th><th>Admission No.</th><th>Class</th><th>Profile</th></tr></thead><tbody>
                        @forelse($siblings as $sibling)
                            <tr><td>{{ trim(($sibling->first_name ?? '').' '.($sibling->last_name ?? '')) }}</td><td>{{ $sibling->admissionNo ?? '-' }}</td><td>{{ $sibling->class_name ?? '-' }}</td><td><a href="{{ url('studentDetail/'.$sibling->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a></td></tr>
                        @empty<tr><td colspan="4" class="empty-state">No sibling records.</td></tr>@endforelse
                        </tbody></table></div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-secondary section-card" id="document-record">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0"><i class="fa fa-file-text-o text-danger"></i> Student Documents</h3>
                    <button type="button" class="btn btn-primary btn-xs float-right" data-toggle="modal" data-target="#uploadStudentDocModal">
                        <i class="fa fa-plus"></i> Add Document
                    </button>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped mb-0"><thead><tr><th>SR No</th><th>Title</th><th>Remark</th><th>Uploaded</th><th class="text-center">Action</th></tr></thead><tbody>
                    @forelse($getDocuments as $document)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $document->title ?? '-' }}</td>
                            <td>{{ $document->remark ?? '-' }}</td>
                            <td>{{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d M Y') : '-' }}</td>
                            <td class="text-center">
                                <a href="{{ asset('schoolimage/student_document/' . $document->file) }}" target="_blank" class="btn btn-xs btn-primary mr-1" title="View Document"><i class="fa fa-eye"></i> View</a>
                                <button type="button" class="btn btn-xs btn-warning mr-1 edit-doc-btn" data-id="{{ $document->id }}" data-title="{{ $document->title }}" data-remark="{{ $document->remark }}" title="Edit Document"><i class="fa fa-edit"></i> Edit</button>
                                <a href="{{ url('document_delete/' . $document->id) }}" onclick="return confirm('Are you sure you want to delete this document?')" class="btn btn-xs btn-danger" title="Delete Document"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    @empty<tr><td colspan="5" class="empty-state text-center py-3">No documents uploaded.</td></tr>@endforelse
                    </tbody></table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Upload Student Document Modal -->
<div class="modal fade" id="uploadStudentDocModal" tabindex="-1" role="dialog" aria-labelledby="uploadStudentDocModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url('document_upload/' . ($data->id ?? 0)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="uploadStudentDocModalTitle"><i class="fa fa-upload mr-1"></i> Upload Student Document</h5>
                    <button type="button" class="close text-white close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Enter document title" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Remark</label>
                        <textarea class="form-control" name="remark" rows="2" placeholder="Enter remark (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Upload File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file border rounded p-2 w-100" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf" required>
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Allowed formats: Images (JPG, JPEG, PNG, WEBP, GIF) and PDF only.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"> Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Document Modal -->
<div class="modal fade" id="editStudentDocModal" tabindex="-1" role="dialog" aria-labelledby="editStudentDocModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ url('document_update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="document_id" id="edit_document_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="editStudentDocModalTitle"><i class="fa fa-edit mr-1"></i> Edit Student Document</h5>
                    <button type="button" class="close text-white close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="edit_document_title" placeholder="Enter document title" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Remark</label>
                        <textarea class="form-control" name="remark" id="edit_document_remark" rows="2" placeholder="Enter remark (optional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Replace File <small class="text-muted">(Optional)</small></label>
                        <input type="file" class="form-control-file border rounded p-2 w-100" name="file" accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,image/*,application/pdf">
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Allowed formats: Images (JPG, JPEG, PNG, WEBP, GIF) and PDF only.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm font-weight-bold"><i class="fa fa-save mr-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="studentStatusModal" tabindex="-1" role="dialog" aria-labelledby="studentStatusModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentStatusModalTitle">Change Student Status</h5>
                <button type="button" class="close close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p id="studentStatusConfirmText" class="mb-0">Are you sure you want to change this student's status?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-modal-btn" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmStudentStatus" class="btn btn-primary">Yes, Continue</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .close-modal-btn', function () {
    $(this).closest('.modal').modal('hide');
});

$(document).on('click', '.edit-doc-btn', function () {
    var id = $(this).attr('data-id');
    var title = $(this).attr('data-title');
    var remark = $(this).attr('data-remark');

    $('#edit_document_id').val(id);
    $('#edit_document_title').val(title);
    $('#edit_document_remark').val(remark);
    $('#editStudentDocModal').modal('show');
});

$(document).on('click', '#studentStatusAction', function () {
    var nextStatus = Number($(this).attr('data-status'));
    $('#studentStatusConfirmText').text(
        nextStatus === 1
            ? 'Are you sure you want to activate this student?'
            : 'Are you sure you want to deactivate this student?'
    );
    $('#studentStatusModal').modal('show');
});

$(document).on('click', '#confirmStudentStatus', function () {
    var actionButton = $('#studentStatusAction');
    var nextStatus = Number(actionButton.attr('data-status'));
    var confirmButton = $(this);

    confirmButton.prop('disabled', true).text('Updating...');

    $.ajax({
        type: 'POST',
        url: '{{ url('stu_status') }}',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {id: actionButton.attr('data-id'), status: nextStatus},
        success: function () {
            var isActive = nextStatus === 1;

            $('#studentStatusBadge')
                .toggleClass('bg-success', isActive)
                .toggleClass('bg-danger', !isActive)
                .text(isActive ? 'Active Student' : 'Inactive Student');

            actionButton
                .attr('data-status', isActive ? 0 : 1)
                .toggleClass('btn-outline-danger', isActive)
                .toggleClass('btn-outline-success', !isActive);
            actionButton.find('i')
                .toggleClass('fa-ban', isActive)
                .toggleClass('fa-check-circle', !isActive);
            actionButton.find('span').text(isActive ? 'Deactivate Student' : 'Activate Student');

            $('#studentStatusModal').modal('hide');
            if (window.toastr) {
                toastr.success('Student status updated successfully.');
            }
        },
        error: function () {
            if (window.toastr) {
                toastr.error('Student status could not be updated. Please try again.');
            } else {
                alert('Student status could not be updated. Please try again.');
            }
        },
        complete: function () {
            confirmButton.prop('disabled', false).text('Yes, Continue');
        }
    });
});
</script>
@endsection

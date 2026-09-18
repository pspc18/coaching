@extends('layout.app')

@section('title', 'Exam Wise Report - Examination Management')

@section('styles')
<style>
/* Exam Wise Report - Unified Arise Theme */
.report-page-wrapper {
    background-color: #f4f6f9;
    padding: 8px 12px 20px 12px;
}

/* 1. Hero Banner */
.report-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 60%, #0f3460 100%);
    border-radius: 4px;
    padding: 10px 16px;
    color: #ffffff;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0, 44, 84, 0.18);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.report-hero-text {
    max-width: 60%;
}
.report-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    color: #cbd5e1;
    display: block;
    margin-bottom: 2px;
}
.report-title {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 2px 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 8px;
}
.report-subtitle {
    font-size: 11px;
    margin: 0;
    color: #e2e8f0;
    line-height: 1.3;
}
.report-hero-actions {
    display: flex;
    gap: 6px;
    align-items: center;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.2s ease;
    white-space: nowrap;
    height: 28px;
    cursor: pointer;
    border: 1px solid transparent;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001833 !important;
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: #ffffff;
}

/* 2. Workflow Bar */
.exam-workflow-card {
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    padding: 6px 12px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.workflow-pill {
    padding: 3px 8px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.15s;
}
.workflow-pill:hover {
    background: #f1f5f9;
    color: #002C54;
}
.workflow-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #001f3f;
}

/* 3. Cards & Structure */
.report-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    margin-bottom: 10px;
}
.report-card-header {
    background: #002C54;
    color: #ffffff;
    padding: 8px 12px;
    border-radius: 3px 3px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12.5px;
    font-weight: 700;
}
.report-card-header .header-badge {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
}
.report-card-body {
    padding: 10px 14px;
}

/* Form inputs & Select2 */
.report-card-body label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.report-card-body .form-control {
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    height: 32px;
    padding: 4px 8px;
}
.report-card-body .form-control:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.select2-container--bootstrap4 .select2-selection--single {
    height: 32px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
    font-size: 12px !important;
    padding: 2px 4px !important;
}
.select2-container--bootstrap4 .select2-selection--multiple,
.select2-container--default .select2-selection--multiple,
.select2-container .select2-selection--multiple {
    min-height: 32px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
    font-size: 12px !important;
    padding-bottom: 2px !important;
}
.select2-container .select2-selection--multiple .select2-selection__choice,
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice,
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #e0f2fe !important;
    background: #e0f2fe !important;
    border: 1px solid #7dd3fc !important;
    color: #0369a1 !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    border-radius: 2px !important;
    padding: 2px 7px !important;
    margin: 3px 4px 3px 0 !important;
    line-height: 1.4 !important;
    display: inline-flex !important;
    align-items: center !important;
}
.select2-container .select2-selection--multiple .select2-selection__choice__remove,
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #0284c7 !important;
    font-weight: 800 !important;
    font-size: 13px !important;
    line-height: 1 !important;
    margin-right: 5px !important;
    float: none !important;
    cursor: pointer !important;
    border: none !important;
}
.select2-container .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #dc2626 !important;
}

/* Summary KPI Cards */
.report-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 10px;
}
.report-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.report-kpi-icon {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    background: #e6f0fa;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.report-kpi-info {
    line-height: 1.2;
}
.report-kpi-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.report-kpi-value {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
}
.report-kpi-sub {
    font-size: 9.5px;
    color: #94a3b8;
}

/* Results Data Table */
.exam-report-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}
.exam-report-table th, .exam-report-table td {
    padding: 5px 6px;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}
.exam-report-table thead th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    text-align: center;
    border-color: #001f3f;
    font-size: 11px;
    white-space: nowrap;
}
.exam-report-table thead th.sub-th {
    background: #0f3460;
    font-size: 10px;
    padding: 3px 4px;
}
.exam-report-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.exam-report-table tbody tr:hover td {
    background: #e6f0fa;
}

.marks-highlight {
    font-weight: 800;
    color: #002C54;
    background: #e6f0fa;
}
.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
}
.rank-badge.rank-1 {
    background: #f59e0b;
}
.rank-badge.rank-2 {
    background: #94a3b8;
}
.rank-badge.rank-3 {
    background: #b45309;
}

/* Print CSS Rules */
#exam-print-root {
    display: none;
}
@media print {
    @page {
        size: A4 landscape;
        margin: 4mm;
    }
    html, body {
        background: #ffffff !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    body > *:not(#exam-print-root) {
        display: none !important;
    }
    #exam-print-root {
        display: block !important;
        width: 100% !important;
    }
    #exam-print-root .report-hero-actions,
    #exam-print-root .report-toolbar {
        display: none !important;
    }
}
</style>
@endsection

@section('content')

@php
    $classType = Helper::classType();
    $search = $search ?? [];
    $selectedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $selectedSubjectIds = (array) ($search['subject_id'] ?? []);
    $examlist = $examlist ?? collect();
    $students = $students ?? collect();
    $list_subject = $list_subject ?? collect();
    $reportRows = $reportRows ?? [];
    $summary = $summary ?? [];
    $singleSubjectMode = (bool) ($singleSubjectMode ?? false);
    $hasReportData = !empty($reportRows);
@endphp

<div class="content-wrapper report-page-wrapper">
    
    {{-- 1. Signature Hero Banner --}}
    <div class="report-hero">
        <div class="report-hero-text">
            <span class="report-kicker"><i class="fa fa-bar-chart mr-1"></i> Examination Performance &amp; Transcript Report</span>
            <h1 class="report-title">
                <i class="fa fa-line-chart"></i> Exam Wise Report
            </h1>
            <p class="report-subtitle">
                Generate detailed student evaluation transcripts, subject-wise scores, topper ranks, and class percentages.
            </p>
        </div>
        <div class="report-hero-actions">
            <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="View All Exams">
                <i class="fa fa-leanpub"></i> 1. Exam Setup
            </a>
            <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                <i class="fa fa-file-excel-o"></i> 2. Excel Import
            </a>
            <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Manual Marks Entry">
                <i class="fa fa-pencil-square-o"></i> 3. Manual Marks
            </a>
            <a href="{{ url('examination_dashboard') }}" class="dash-btn dash-btn-light" title="Back to Dashboard">
                <i class="fa fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success') || session('message'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- 2. Examination Workflow Step Indicator --}}
    <div class="exam-workflow-card">
        <div class="d-flex align-items-center" style="gap:6px;">
            <i class="fa fa-random text-primary"></i>
            <strong style="font-size:12px; color:#002C54;">Examination Workflow:</strong>
        </div>
        <div class="d-flex flex-wrap align-items-center" style="gap:5px;">
            <a href="{{ url('view/exam') }}" class="workflow-pill">
                1. Exam Setup
            </a>
            <a href="{{ url('fill-marks-by-excel') }}" class="workflow-pill">
                2. Excel Import
            </a>
            <a href="{{ url('fill_marks') }}" class="workflow-pill">
                3. Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="workflow-pill active">
                <i class="fa fa-check-circle"></i> 4. Exam Report
            </a>
        </div>
    </div>

    {{-- 3. Selection Toolbar Card --}}
    <div class="report-card">
        <div class="report-card-header">
            <span><i class="fa fa-filter mr-1"></i> Report Filters &amp; Target Selection</span>
            <span class="header-badge">Filter Setup</span>
        </div>
        <div class="report-card-body">
            <form method="post" action="{{ url('exam_wise_report') }}" id="reportFilterForm">
                @csrf
                <div class="row align-items-end">
                    
                    {{-- Class Selection --}}
                    <div class="col-md-3 form-group mb-2">
                        <label><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="class_type_id" id="class_type_id" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classType as $class)
                                <option value="{{ $class->id }}" {{ $selectedClassId === (int)$class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Exam Selection --}}
                    <div class="col-md-3 form-group mb-2">
                        <label><i class="fa fa-leanpub text-primary"></i> Examination <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="exam_id" id="exam_id" required>
                            <option value="">-- Select Exam --</option>
                            @foreach($examlist as $item)
                                @php
                                    $examLabelDate = !empty($item->assign_exam_date) ? \Carbon\Carbon::parse($item->assign_exam_date)->format('d-m-Y') : '';
                                @endphp
                                <option value="{{ $item->exam_id }}" {{ $selectedExamId === (int)$item->exam_id ? 'selected' : '' }}>
                                    {{ $item->exam_name }}{{ $examLabelDate ? ' ('.$examLabelDate.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subject Selection --}}
                    <div class="col-md-4 form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0"><i class="fa fa-book text-primary"></i> Subjects <span class="text-danger">*</span></label>
                            <a href="javascript:void(0)" id="btnSelectAllSubjects" style="font-size:10.5px; font-weight:700; color:#002C54; text-decoration:underline;">
                                Select All
                            </a>
                        </div>
                        <select class="form-control select2" name="subject_id[]" id="subject_id" multiple="multiple" required>
                            @foreach($list_subject as $sub)
                                <option value="{{ $sub->id }}" {{ in_array((int)$sub->id, array_map('intval', $selectedSubjectIds), true) ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-2 form-group mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary flex-grow-1" style="height:32px; font-size:12px; font-weight:700; border-radius:2px; background:#002C54; border-color:#001f3f;">
                            <i class="fa fa-file-text-o mr-1"></i> Generate
                        </button>
                        @if($selectedClassId || $selectedExamId)
                            <a href="{{ url('exam_wise_report') }}" class="btn btn-outline-secondary" style="height:32px; font-size:12px; border-radius:2px;" title="Reset Filter">
                                <i class="fa fa-refresh"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- 4. Report Results Section --}}
    @if($hasReportData)

        {{-- Summary KPIs --}}
        <div class="report-kpi-grid">
            <div class="report-kpi-card">
                <div class="report-kpi-icon"><i class="fa fa-users"></i></div>
                <div class="report-kpi-info">
                    <div class="report-kpi-label">Total Students</div>
                    <div class="report-kpi-value">{{ $summary['total_students'] ?? count($reportRows) }}</div>
                    <div class="report-kpi-sub">Enrolled &amp; Evaluated</div>
                </div>
            </div>

            <div class="report-kpi-card">
                <div class="report-kpi-icon" style="background:#fef3c7; color:#d97706;"><i class="fa fa-trophy"></i></div>
                <div class="report-kpi-info">
                    <div class="report-kpi-label">{{ $summary['topper_label'] ?? 'Class Topper' }}</div>
                    <div class="report-kpi-value" style="font-size:12.5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;">
                        {{ $summary['topper_name'] ?? '-' }}
                    </div>
                    <div class="report-kpi-sub">Score: <strong>{{ $summary['topper_score'] ?? 0 }}</strong></div>
                </div>
            </div>

            <div class="report-kpi-card">
                <div class="report-kpi-icon" style="background:#d1fae5; color:#059669;"><i class="fa fa-pie-chart"></i></div>
                <div class="report-kpi-info">
                    <div class="report-kpi-label">{{ $singleSubjectMode ? 'Average Score' : 'Average %' }}</div>
                    <div class="report-kpi-value">
                        {{ $singleSubjectMode ? ($summary['average_marks'] ?? 0) : (($summary['average_percentage'] ?? 0) . '%') }}
                    </div>
                    <div class="report-kpi-sub">Overall Class Average</div>
                </div>
            </div>

            <div class="report-kpi-card">
                <div class="report-kpi-icon" style="background:#e0e7ff; color:#4338ca;"><i class="fa fa-check-square-o"></i></div>
                <div class="report-kpi-info">
                    <div class="report-kpi-label">{{ $singleSubjectMode ? 'Subject Max' : 'Report Max' }}</div>
                    <div class="report-kpi-value">
                        {{ $singleSubjectMode ? ($summary['single_subject_maximum'] ?? 0) : ($summary['report_maximum'] ?? 0) }}
                    </div>
                    <div class="report-kpi-sub">{{ $singleSubjectMode ? ($summary['single_subject_name'] ?? '') : (count($list_subject) . ' Subjects') }}</div>
                </div>
            </div>
        </div>

        {{-- Main Report Card --}}
        <div class="report-card" id="reportResultContainer">
            <div class="report-card-header" style="background:#0f3460;">
                <div class="d-flex align-items-center" style="gap:8px;">
                    <i class="fa fa-table text-warning"></i>
                    <span>{{ $exam->name ?? 'Exam' }} &bull; {{ $className->name ?? 'Class' }}</span>
                    <span class="header-badge">{{ count($reportRows) }} Records</span>
                </div>
                <div class="d-flex align-items-center report-toolbar" style="gap:6px;">
                    <button type="button" class="btn btn-light btn-xs px-2" onclick="printExamWiseReport()" style="height:26px; font-weight:700; font-size:11px; border-radius:2px;">
                        <i class="fa fa-print mr-1"></i> Print
                    </button>
                    <a class="btn btn-warning btn-xs px-2" style="height:26px; font-weight:700; font-size:11px; border-radius:2px; color:#111;"
                       href="{{ route('exam-wise-report.pdf', ['exam_id' => $search['exam_id'], 'class_type_id' => $search['class_type_id'], 'subject_id' => $search['subject_id'], 'admission_id' => $search['admission_id'] ?? null]) }}" download>
                        <i class="fa fa-file-pdf-o mr-1"></i> PDF
                    </a>
                    <button type="button" class="btn btn-success btn-xs px-2" onclick="downloadExamReportCSV()" style="height:26px; font-weight:700; font-size:11px; border-radius:2px;">
                        <i class="fa fa-file-excel-o mr-1"></i> CSV
                    </button>
                </div>
            </div>
            
            <div class="report-card-body p-0">
                <div class="table-responsive">
                    <table class="exam-report-table table-bordered m-0" id="examReportTable">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 35px;">#</th>
                                <th rowspan="2" style="width: 85px;">Adm No.</th>
                                <th rowspan="2" style="width: 65px;">Roll No.</th>
                                <th rowspan="2" style="min-width: 170px; text-align:left; padding-left:8px;">Student Name</th>

                                @if($singleSubjectMode)
                                    <th colspan="4" class="text-center" style="background:#002C54 !important;">
                                        <i class="fa fa-book mr-1"></i> {{ $list_subject->first()->name ?? 'Subject' }} ({{ $summary['single_subject_maximum'] ?? 0 }})
                                    </th>
                                    <th rowspan="2" style="width: 60px; background:#001f3f !important;">Rank</th>
                                    <th rowspan="2" style="width: 65px; background:#001f3f !important;">%</th>
                                @else
                                    @foreach($list_subject as $subject)
                                        <th colspan="4" class="text-center" style="background:#002C54 !important;">
                                            <i class="fa fa-book mr-1"></i> {{ $subject->name }} ({{ $summary['subject_maximums'][$subject->id] ?? '-' }})
                                        </th>
                                    @endforeach
                                    <th rowspan="2" style="width: 70px; background:#001f3f !important;">Total</th>
                                    <th rowspan="2" style="width: 65px; background:#001f3f !important;">Max</th>
                                    <th rowspan="2" style="width: 55px; background:#001f3f !important;">Rank</th>
                                    <th rowspan="2" style="width: 65px; background:#001f3f !important;">%</th>
                                @endif
                            </tr>
                            <tr>
                                @if($singleSubjectMode)
                                    <th class="sub-th" style="width:40px;">R</th>
                                    <th class="sub-th" style="width:40px;">W</th>
                                    <th class="sub-th" style="width:40px;">L</th>
                                    <th class="sub-th font-weight-bold" style="width:60px;">Marks</th>
                                @else
                                    @foreach($list_subject as $subject)
                                        <th class="sub-th" style="width:35px;">R</th>
                                        <th class="sub-th" style="width:35px;">W</th>
                                        <th class="sub-th" style="width:35px;">L</th>
                                        <th class="sub-th font-weight-bold" style="width:55px;">Marks</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportRows as $index => $row)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $index + 1 }}</td>
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-light border" style="font-size:10.5px;">{{ $row['admission_no'] ?? '' }}</span>
                                    </td>
                                    <td class="text-center text-muted font-weight-bold">{{ $row['roll_no'] ?? '-' }}</td>
                                    <td style="text-align:left; padding-left:8px; font-weight:700; color:#0f172a;">
                                        {{ $row['student_name'] ?? '' }}
                                    </td>

                                    @if($singleSubjectMode)
                                        @php
                                            $subjectRow = $row['subject_rows'][0] ?? null;
                                        @endphp
                                        <td class="text-center text-muted">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                                        <td class="text-center text-muted">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                                        <td class="text-center text-muted">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                                        <td class="text-center marks-highlight">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                                        <td class="text-center">
                                            @php $sRank = (int)($row['subject_rank'] ?? 0); @endphp
                                            <span class="rank-badge {{ $sRank == 1 ? 'rank-1' : ($sRank == 2 ? 'rank-2' : ($sRank == 3 ? 'rank-3' : '')) }}">
                                                #{{ $row['subject_rank'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center font-weight-bold">{{ number_format((float) ($row['percentage'] ?? 0), 2) }}%</td>
                                    @else
                                        @foreach($row['subject_rows'] as $subjectRow)
                                            <td class="text-center text-muted">{{ $subjectRow['r_marks'] ?? '-' }}</td>
                                            <td class="text-center text-muted">{{ $subjectRow['w_marks'] ?? '-' }}</td>
                                            <td class="text-center text-muted">{{ $subjectRow['l_marks'] ?? '-' }}</td>
                                            <td class="text-center marks-highlight">{{ $subjectRow['display_marks'] ?? '-' }}</td>
                                        @endforeach
                                        <td class="text-center font-weight-bold text-dark">{{ number_format((float) ($row['total_obtained'] ?? 0), 2) }}</td>
                                        <td class="text-center text-muted">{{ number_format((float) ($row['total_maximum'] ?? 0), 2) }}</td>
                                        <td class="text-center">
                                            @php $oRank = (int)($row['overall_rank'] ?? 0); @endphp
                                            <span class="rank-badge {{ $oRank == 1 ? 'rank-1' : ($oRank == 2 ? 'rank-2' : ($oRank == 3 ? 'rank-3' : '')) }}">
                                                #{{ $row['overall_rank'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center font-weight-bold" style="color: {{ (float)($row['percentage'] ?? 0) >= 60 ? '#059669' : ((float)($row['percentage'] ?? 0) >= 33 ? '#d97706' : '#dc2626') }};">
                                            {{ number_format((float) ($row['percentage'] ?? 0), 2) }}%
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($selectedClassId && $selectedExamId)
        <div class="alert alert-info py-3 px-3 mb-3" style="border-radius:2px; font-size:12px;">
            <i class="fa fa-info-circle mr-1"></i> No examination records or marks found for the selected criteria. Please verify if marks have been entered for this exam.
        </div>
    @endif

</div>

<script>
function printExamWiseReport() {
    window.print();
}

function downloadExamReportCSV() {
    var table = document.getElementById("examReportTable");
    if (!table) return;

    var csv = [];
    var rows = table.querySelectorAll("tr");
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        for (var j = 0; j < cols.length; j++) {
            var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
            row.push('"' + text.replace(/"/g, '""') + '"');
        }
        csv.push(row.join(","));
    }
    var csv_string = csv.join("\n");
    var filename = "exam_wise_report.csv";
    var link = document.createElement("a");
    link.style.display = "none";
    link.setAttribute("href", 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    }

    var selectedSubjectIds = @json($search['subject_id'] ?? []);

    // Select all subjects toggle
    $('#btnSelectAllSubjects').on('click', function() {
        var $select = $('#subject_id');
        var allValues = $select.find('option').map(function() {
            return $(this).val();
        }).get();
        $select.val(allValues).trigger('change');
    });

    // Dynamic Class Change -> Load Exams & Subjects
    $('#class_type_id').on('change', function() {
        var baseurl = "{{ url('/') }}";
        var classId = $(this).val();
        var $examSelect = $('#exam_id');
        
        $examSelect.html('<option value="">Loading examinations...</option>');

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            $('#subject_id').empty();
            return;
        }

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseurl + '/examData/' + classId,
            success: function(res) {
                $examSelect.html(res);
                if ($.fn.select2) {
                    $examSelect.trigger('change.select2');
                }
            }
        });

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseurl + '/subjectGetData/' + classId,
            success: function(data) {
                $('#subject_id').html(data);
                if (selectedSubjectIds && selectedSubjectIds.length) {
                    $('#subject_id').val(selectedSubjectIds).trigger('change');
                }
            }
        });
    });

    if ($('#class_type_id').val() !== '' && $('#subject_id option').length === 0) {
        var baseurl = "{{ url('/') }}";
        var classId = $('#class_type_id').val();
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: baseurl + '/subjectGetData/' + classId,
            success: function(data) {
                $('#subject_id').html(data);
                if (selectedSubjectIds && selectedSubjectIds.length) {
                    $('#subject_id').val(selectedSubjectIds).trigger('change');
                }
            }
        });
    }
});
</script>
@endsection
@extends('layout.app')

@section('content')
@php
    $hasReport = !empty($report) && !empty($report['student']);
    $examSummaries = $report['exam_summaries'] ?? [];
    $subjectRows = $report['subject_rows'] ?? [];
    $summary = $report['summary'] ?? [];
    $latestVsPrevious = $report['latest_vs_previous'] ?? null;
    $remarks = $report['remarks'] ?? [];
    $chartPayload = $report['chart_data'] ?? [
        'trend' => ['labels' => [], 'percentages' => [], 'ranks' => []],
        'subject_average' => ['labels' => [], 'percentages' => []],
        'latest_previous' => ['labels' => [], 'latest' => [], 'previous' => []],
    ];
@endphp

<div class="content-wrapper student-exam-report-page">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-orange report-filter-card">
                        <div class="card-header bg-primary report-page-header">
                            <h3 class="card-title">
                                <i class="fa fa-line-chart"></i> &nbsp;Student Wise Exam Comparison Report
                            </h3>
                            <div class="card-tools">
                                <a href="{{ url('examination_dashboard') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('student_exam_comparison_report') }}" method="post" id="studentExamComparisonForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Branch</label>
                                            <select name="branch_id" id="branch_id" class="form-control select2">
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ (int) ($search['branch_id'] ?? 0) === (int) $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->branch_name ?? 'Branch #' . $branch->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Session</label>
                                            <select name="session_id" id="session_id" class="form-control select2">
                                                @foreach($sessions as $session)
                                                    @php
                                                        $sessionLabel = trim(($session->from_year ?? '') . ' - ' . ($session->to_year ?? ''));
                                                    @endphp
                                                    <option value="{{ $session->id }}" {{ (int) ($search['session_id'] ?? 0) === (int) $session->id ? 'selected' : '' }}>
                                                        {{ $sessionLabel ?: ('Session #' . $session->id) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Class / Batch</label>
                                            <select name="class_type_id" id="class_type_id" class="form-control select2">
                                                <option value="">Select</option>
                                                @foreach($classTypes as $classType)
                                                    <option value="{{ $classType->id }}" {{ (int) ($search['class_type_id'] ?? 0) === (int) $classType->id ? 'selected' : '' }}>
                                                        {{ $classType->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Student</label>
                                            <select name="admission_id" id="admission_id" class="form-control select2">
                                                <option value="">Select Student</option>
                                                @foreach($students as $student)
                                                    @php
                                                        $studentLabel = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                                                        $studentLabel .= !empty($student->admissionNo) ? ' [' . $student->admissionNo . ']' : '';
                                                    @endphp
                                                    <option value="{{ $student->id }}" {{ (int) ($search['admission_id'] ?? 0) === (int) $student->id ? 'selected' : '' }}>
                                                        {{ $studentLabel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $search['date_from'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $search['date_to'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="student-exam-report-note">
                                            Timeline filtering uses result declaration date when available, otherwise the exam creation date.
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> View Report
                                        </button>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <a href="{{ url('student_exam_comparison_report') }}" class="btn btn-outline-secondary btn-block">
                                            <i class="fa fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($hasReport && !empty($examSummaries))
                @php
                    $student = $report['student'];
                    $imagePath = !empty($student->image) ? env('IMAGE_SHOW_PATH') . 'profile/' . $student->image : env('IMAGE_SHOW_PATH') . 'default/no_image.png';
                @endphp

                <div class="row">
                    <div class="col-12">
                        <div class="card student-profile-card report-surface-card">
                            <div class="card-body">
                                <div class="student-profile-wrap">
                                    <div class="student-profile-photo">
                                        <img src="{{ $imagePath }}" alt="Student Photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH') }}default/no_image.png'">
                                    </div>
                                    <div class="student-profile-meta">
                                        <h4>{{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}</h4>
                                        <div class="student-profile-grid">
                                            <div>
                                                <span class="meta-label">Admission No</span>
                                                <strong>{{ $student->admissionNo ?? '-' }}</strong>
                                            </div>
                                            <div>
                                                <span class="meta-label">Roll No</span>
                                                <strong>{{ $student->roll_no ?? '-' }}</strong>
                                            </div>
                                            <div>
                                                <span class="meta-label">Class / Batch</span>
                                                <strong>{{ $student->class_name ?? '-' }}</strong>
                                            </div>
                                            <div>
                                                <span class="meta-label">Parent Name</span>
                                                <strong>{{ $student->father_name ?? '-' }}</strong>
                                            </div>
                                            <div>
                                                <span class="meta-label">Mobile</span>
                                                <strong>{{ $student->mobile ?? '-' }}</strong>
                                            </div>
                                            <div>
                                                <span class="meta-label">Report Scope</span>
                                                <strong>{{ count($examSummaries) }} Exam(s)</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="student-profile-actions">
                                        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                            <i class="fa fa-print"></i> Print
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="section-caption">Performance Summary</div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-blue">
                            <div class="metric-icon"><i class="fa fa-files-o"></i></div>
                            <div class="metric-content">
                                <span>Total Exams Attempted</span>
                                <h3>{{ $summary['total_exams_attempted'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-green">
                            <div class="metric-icon"><i class="fa fa-percent"></i></div>
                            <div class="metric-content">
                                <span>Average Percentage</span>
                                <h3>{{ number_format((float) ($summary['average_percentage'] ?? 0), 2) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-cyan">
                            <div class="metric-icon"><i class="fa fa-arrow-up"></i></div>
                            <div class="metric-content">
                                <span>Highest Percentage</span>
                                <h3>{{ number_format((float) ($summary['highest_percentage'] ?? 0), 2) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-gold">
                            <div class="metric-icon"><i class="fa fa-arrow-down"></i></div>
                            <div class="metric-content">
                                <span>Lowest Percentage</span>
                                <h3>{{ number_format((float) ($summary['lowest_percentage'] ?? 0), 2) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-indigo">
                            <div class="metric-icon"><i class="fa fa-line-chart"></i></div>
                            <div class="metric-content">
                                <span>Latest Exam Percentage</span>
                                <h3>{{ number_format((float) ($summary['latest_percentage'] ?? 0), 2) }}%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-red">
                            <div class="metric-icon"><i class="fa fa-exchange"></i></div>
                            <div class="metric-content">
                                <span>Overall Change</span>
                                <h3>
                                    @if(($summary['overall_change'] ?? null) === null)
                                        -
                                    @else
                                        {{ ($summary['overall_change'] >= 0 ? '+' : '') . number_format((float) $summary['overall_change'], 2) }}%
                                    @endif
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-slate">
                            <div class="metric-icon"><i class="fa fa-trophy"></i></div>
                            <div class="metric-content">
                                <span>Current Rank</span>
                                <h3>{{ $summary['current_rank'] ?? '-' }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="report-metric-card accent-violet">
                            <div class="metric-icon"><i class="fa fa-star"></i></div>
                            <div class="metric-content">
                                <span>Best Rank</span>
                                <h3>{{ $summary['best_rank'] ?? '-' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-line-chart"></i> Exam-wise Percentage Trend</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="examTrendChart" height="120"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-bar-chart"></i> Subject Average Performance</h3>
                            </div>
                            <div class="card-body">
                                <canvas id="subjectAverageChart" height="240"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($latestVsPrevious))
                    <div class="row">
                        <div class="col-12">
                            <div class="card report-surface-card">
                                <div class="card-header report-section-header">
                                    <h3 class="card-title"><i class="fa fa-random"></i> Latest vs Previous Subject Comparison</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="latestPreviousChart" height="110"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-table"></i> Exam Comparison Table</h3>
                            </div>
                            <div class="card-body table-responsive p-0 report-table-wrap">
                                <table class="table table-bordered table-striped mb-0 report-data-table">
                                    <thead>
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Date</th>
                                            <th>Exam Term</th>
                                            <th>Total Maximum Marks</th>
                                            <th>Total Obtained Marks</th>
                                            <th>Percentage</th>
                                            <th>Rank</th>
                                            <th>Change</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($examSummaries as $examSummary)
                                            <tr>
                                                <td>{{ $examSummary['exam_name'] ?? '' }}</td>
                                                <td>{{ $examSummary['report_date_label'] ?? '-' }}</td>
                                                <td>{{ $examSummary['exam_term_name'] ?? '-' }}</td>
                                                <td>{{ number_format((float) ($examSummary['total_maximum'] ?? 0), 2) }}</td>
                                                <td>{{ number_format((float) ($examSummary['total_obtained'] ?? 0), 2) }}</td>
                                                <td>{{ number_format((float) ($examSummary['percentage'] ?? 0), 2) }}%</td>
                                                <td>{{ $examSummary['rank'] ?? '-' }}</td>
                                                <td>
                                                    @if(($examSummary['change_from_previous'] ?? null) === null)
                                                        -
                                                    @else
                                                        {{ ($examSummary['change_from_previous'] >= 0 ? '+' : '') . number_format((float) $examSummary['change_from_previous'], 2) }}%
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="report-badge badge-{{ strtolower($examSummary['status'] ?? 'baseline') }}">
                                                        {{ $examSummary['status'] ?? 'Baseline' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-graduation-cap"></i> Subject Wise Comparison</h3>
                            </div>
                            <div class="card-body table-responsive p-0 report-table-wrap">
                                <table class="table table-bordered table-striped mb-0 subject-comparison-table report-data-table">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            @foreach($examSummaries as $examSummary)
                                                <th>{{ $examSummary['exam_name'] }}<br><small>{{ $examSummary['report_date_label'] }}</small></th>
                                            @endforeach
                                            <th>Average %</th>
                                            <th>Status</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjectRows as $subjectRow)
                                            <tr>
                                                <td>{{ $subjectRow['subject_name'] ?? '' }}</td>
                                                @foreach($subjectRow['exam_scores'] as $score)
                                                    <td>
                                                        <div class="score-block">
                                                            <strong>{{ $score['display_marks'] ?? '-' }}</strong>
                                                            <span>/ {{ number_format((float) ($score['maximum_marks'] ?? 0), 2) }}</span>
                                                        </div>
                                                        <div class="score-percent">{{ number_format((float) ($score['percentage'] ?? 0), 2) }}%</div>
                                                    </td>
                                                @endforeach
                                                <td>{{ number_format((float) ($subjectRow['average_percentage'] ?? 0), 2) }}%</td>
                                                <td>
                                                    <span class="report-badge badge-{{ strtolower($subjectRow['status'] ?? 'weak') }}">
                                                        {{ $subjectRow['status'] ?? 'Weak' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="report-badge badge-{{ strtolower($subjectRow['trend'] ?? 'baseline') }}">
                                                        {{ $subjectRow['trend'] ?? 'Baseline' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-exchange"></i> Latest vs Previous Exam Snapshot</h3>
                            </div>
                            <div class="card-body">
                                @if(!empty($latestVsPrevious))
                                    <div class="latest-previous-metrics">
                                        <div class="metric-box">
                                            <span class="metric-label">Previous Exam</span>
                                            <strong>{{ $latestVsPrevious['previous_exam_name'] ?? '-' }}</strong>
                                            <div>{{ number_format((float) ($latestVsPrevious['previous_percentage'] ?? 0), 2) }}%</div>
                                        </div>
                                        <div class="metric-box">
                                            <span class="metric-label">Latest Exam</span>
                                            <strong>{{ $latestVsPrevious['latest_exam_name'] ?? '-' }}</strong>
                                            <div>{{ number_format((float) ($latestVsPrevious['latest_percentage'] ?? 0), 2) }}%</div>
                                        </div>
                                        <div class="metric-box">
                                            <span class="metric-label">Difference</span>
                                            <strong>{{ (($latestVsPrevious['difference'] ?? 0) >= 0 ? '+' : '') . number_format((float) ($latestVsPrevious['difference'] ?? 0), 2) }}%</strong>
                                        </div>
                                    </div>
                                    <div class="comparison-list-wrap">
                                        <div>
                                            <h6>Improved Subjects</h6>
                                            @forelse($latestVsPrevious['improved_subjects'] as $item)
                                                <span class="mini-pill pill-success">{{ $item['subject_name'] }} ({{ ($item['difference'] >= 0 ? '+' : '') . number_format((float) $item['difference'], 2) }}%)</span>
                                            @empty
                                                <p class="text-muted mb-0">No improved subjects in the latest comparison.</p>
                                            @endforelse
                                        </div>
                                        <div>
                                            <h6>Declined Subjects</h6>
                                            @forelse($latestVsPrevious['declined_subjects'] as $item)
                                                <span class="mini-pill pill-danger">{{ $item['subject_name'] }} ({{ number_format((float) $item['difference'], 2) }}%)</span>
                                            @empty
                                                <p class="text-muted mb-0">No declined subjects in the latest comparison.</p>
                                            @endforelse
                                        </div>
                                        <div>
                                            <h6>Weak Subjects</h6>
                                            @forelse($latestVsPrevious['weak_subjects'] as $item)
                                                <span class="mini-pill pill-warning">{{ $item['subject_name'] }} ({{ number_format((float) $item['latest_percentage'], 2) }}%)</span>
                                            @empty
                                                <p class="text-muted mb-0">No weak subjects in the latest exam.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-light mb-0">
                                        At least two exams are required to compare the latest exam with the previous one.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card report-surface-card">
                            <div class="card-header report-section-header">
                                <h3 class="card-title"><i class="fa fa-commenting-o"></i> Automatic Remarks</h3>
                            </div>
                            <div class="card-body">
                                @if(!empty($remarks))
                                    <ul class="remarks-list">
                                        @foreach($remarks as $remark)
                                            <li>{{ $remark }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="alert alert-light mb-0">
                                        Not enough comparison data is available to generate remarks yet.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(!empty($report))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            No exam performance data was found for the selected filters.
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            Select class, student, and optional filters to generate the student wise exam comparison report.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

<style>
    .student-exam-report-page {
        background: #f4f6f9;
    }

    .report-filter-card,
    .report-surface-card {
        border: 1px solid #d7e3ef;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        overflow: hidden;
    }

    .report-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .report-filter-card .card-body {
        padding: 1.25rem 1.25rem 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .report-filter-card label {
        font-size: 12px;
        font-weight: 700;
        color: #516173;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }

    .report-filter-card .form-control {
        border-radius: 10px;
        min-height: 42px;
        border-color: #d6deea;
    }

    .section-caption {
        margin: 2px 0 12px;
        font-size: 13px;
        font-weight: 700;
        color: #4b5d70;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .report-metric-card {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 112px;
        margin-bottom: 18px;
        padding: 18px 18px 18px 16px;
        background: #fff;
        border: 1px solid #d7e3ef;
        border-left-width: 5px;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 20px;
        flex: 0 0 50px;
    }

    .metric-content span {
        display: block;
        font-size: 12px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 5px;
    }

    .metric-content h3 {
        margin: 0;
        font-size: 28px;
        line-height: 1.1;
        color: #1f2937;
        font-weight: 700;
    }

    .accent-blue { border-left-color: #2563eb; }
    .accent-blue .metric-icon { background: #dbeafe; color: #1d4ed8; }
    .accent-green { border-left-color: #16a34a; }
    .accent-green .metric-icon { background: #dcfce7; color: #15803d; }
    .accent-cyan { border-left-color: #0891b2; }
    .accent-cyan .metric-icon { background: #cffafe; color: #0e7490; }
    .accent-gold { border-left-color: #d97706; }
    .accent-gold .metric-icon { background: #fef3c7; color: #b45309; }
    .accent-indigo { border-left-color: #4f46e5; }
    .accent-indigo .metric-icon { background: #e0e7ff; color: #4338ca; }
    .accent-red { border-left-color: #dc2626; }
    .accent-red .metric-icon { background: #fee2e2; color: #b91c1c; }
    .accent-slate { border-left-color: #334155; }
    .accent-slate .metric-icon { background: #e2e8f0; color: #334155; }
    .accent-violet { border-left-color: #7c3aed; }
    .accent-violet .metric-icon { background: #ede9fe; color: #6d28d9; }

    .student-exam-report-note {
        font-size: 12px;
        color: #5f6b7a;
        padding: 10px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        min-height: 38px;
    }

    .student-profile-card {
        border: 1px solid #dbe4ef;
    }

    .report-section-header {
        background: #f8fbff;
        border-bottom: 1px solid #d9e4ef;
    }

    .report-section-header .card-title {
        font-weight: 700;
        color: #204b78;
    }

    .student-profile-wrap {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        justify-content: space-between;
    }

    .student-profile-photo img {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #d8e1ea;
        background: #fff;
    }

    .student-profile-meta {
        flex: 1 1 auto;
    }

    .student-profile-meta h4 {
        margin: 0 0 14px;
        font-weight: 700;
        color: #1f2937;
    }

    .student-profile-grid > div {
        background: #f8fbff;
        border: 1px solid #dde7f1;
        border-radius: 12px;
        padding: 12px 14px;
    }

    .student-profile-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .meta-label,
    .metric-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 4px;
    }

    .student-profile-actions {
        flex: 0 0 auto;
    }

    .report-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 74px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-improved,
    .badge-strong {
        background: #dcfce7;
        color: #166534;
    }

    .badge-average,
    .badge-stable {
        background: #e0f2fe;
        color: #0f4c81;
    }

    .badge-declined,
    .badge-weak {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-baseline {
        background: #ede9fe;
        color: #5b21b6;
    }

    .subject-comparison-table td,
    .subject-comparison-table th {
        vertical-align: middle;
    }

    .report-table-wrap {
        background: #fff;
    }

    .report-data-table thead th {
        background: #edf4fb;
        color: #214f7f;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-color: #d7e4ef;
        white-space: nowrap;
    }

    .report-data-table tbody td {
        border-color: #e2e8f0;
        padding: 12px 10px;
        color: #334155;
        font-size: 13px;
    }

    .report-data-table tbody tr:nth-child(even) {
        background: #fbfdff;
    }

    .report-data-table tbody tr:hover {
        background: #f3f8fd;
    }

    .score-block {
        font-size: 13px;
        color: #1f2937;
    }

    .score-percent {
        font-size: 12px;
        color: #64748b;
    }

    .latest-previous-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .metric-box {
        padding: 14px;
        border: 1px solid #dbe4ef;
        border-radius: 12px;
        background: #f8fafc;
    }

    .comparison-list-wrap {
        display: grid;
        gap: 16px;
    }

    .comparison-list-wrap h6 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .mini-pill {
        display: inline-block;
        margin: 0 8px 8px 0;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .pill-success {
        background: #dcfce7;
        color: #166534;
    }

    .pill-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .pill-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .remarks-list {
        margin: 0;
        padding-left: 18px;
    }

    .remarks-list li {
        margin-bottom: 10px;
        color: #334155;
    }

    @media (max-width: 991.98px) {
        .student-profile-wrap {
            flex-direction: column;
        }

        .student-profile-grid,
        .latest-previous-metrics {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .report-page-header {
            align-items: flex-start;
        }
    }

    @media (max-width: 767.98px) {
        .report-metric-card {
            min-height: auto;
        }
    }

    @media print {
        .main-sidebar,
        .main-header,
        .card-tools,
        #studentExamComparisonForm,
        .student-profile-actions,
        .content-header,
        .btn {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        .card {
            break-inside: avoid;
            box-shadow: none !important;
        }
    }
</style>
@endsection

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        var studentUrl = "{{ url('student_exam_comparison_students') }}";
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        function loadStudents(selectedId) {
            var classTypeId = $('#class_type_id').val();
            var branchId = $('#branch_id').val();
            var sessionId = $('#session_id').val();

            if (!classTypeId) {
                $('#admission_id').html('<option value="">Select Student</option>');
                return;
            }

            $.ajax({
                url: studentUrl,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    branch_id: branchId,
                    session_id: sessionId,
                    class_type_id: classTypeId,
                    admission_id: selectedId || ''
                },
                success: function (response) {
                    $('#admission_id').html(response).trigger('change.select2');
                }
            });
        }

        $('#branch_id, #session_id').on('change', function () {
            $('#studentExamComparisonForm').submit();
        });

        $('#class_type_id').on('change', function () {
            $('#admission_id').html('<option value="">Loading students...</option>');
            loadStudents('');
        });

        var chartPayload = @json($chartPayload);

        if (typeof Chart !== 'undefined' && chartPayload.trend && chartPayload.trend.labels.length) {
            new Chart(document.getElementById('examTrendChart'), {
                type: 'line',
                data: {
                    labels: chartPayload.trend.labels,
                    datasets: [{
                        label: 'Percentage',
                        data: chartPayload.trend.percentages,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.25
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100
                            }
                        }]
                    }
                }
            });
        }

        if (typeof Chart !== 'undefined' && chartPayload.subject_average && chartPayload.subject_average.labels.length) {
            new Chart(document.getElementById('subjectAverageChart'), {
                type: 'bar',
                data: {
                    labels: chartPayload.subject_average.labels,
                    datasets: [{
                        label: 'Average %',
                        data: chartPayload.subject_average.percentages,
                        backgroundColor: ['#2563eb', '#0f766e', '#ea580c', '#7c3aed', '#ca8a04', '#0284c7']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100
                            }
                        }]
                    }
                }
            });
        }

        if (typeof Chart !== 'undefined' && chartPayload.latest_previous && chartPayload.latest_previous.labels.length && document.getElementById('latestPreviousChart')) {
            new Chart(document.getElementById('latestPreviousChart'), {
                type: 'bar',
                data: {
                    labels: chartPayload.latest_previous.labels,
                    datasets: [{
                        label: 'Previous %',
                        data: chartPayload.latest_previous.previous,
                        backgroundColor: '#94a3b8'
                    }, {
                        label: 'Latest %',
                        data: chartPayload.latest_previous.latest,
                        backgroundColor: '#2563eb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                max: 100
                            }
                        }]
                    }
                }
            });
        }
    });
</script>

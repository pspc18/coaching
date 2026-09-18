@php
    $getSetting = $getSetting ?? Helper::getSetting();
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

@extends('layout.mobile_app')

@section('styles')
<style>
/* Exam Wise Report - Mobile App Layout */
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
    margin-bottom: 4px;
}
.mob-report-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-badge-step {
    font-size: 9px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Cards & Filters */
.mob-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 10px 12px;
    margin-bottom: 8px;
}
.mob-card-head {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}

.mob-form-group {
    margin-bottom: 8px;
}
.mob-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.mob-select {
    width: 100%;
    height: 32px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    padding: 0 8px;
    font-size: 11.5px;
    background: #ffffff;
    color: #0f172a;
}
.mob-select:focus {
    border-color: #002C54;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1);
}

/* Buttons */
.mob-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 32px;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    width: 100%;
}
.mob-btn-primary {
    background: #002C54;
    color: #ffffff;
}
.mob-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
}
.mob-btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

/* Summary Grid */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    margin-bottom: 8px;
}
.mob-kpi-col {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    text-align: center;
}
.mob-kpi-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.mob-kpi-val {
    font-size: 12.5px;
    font-weight: 900;
    color: #002C54;
}

/* Student Result Card */
.mob-student-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.mob-student-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 4px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-rank-badge {
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
}
.mob-pct-badge {
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 800;
}
.mob-sub-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 0;
    font-size: 10.5px;
    border-bottom: 1px dotted #e2e8f0;
}
.mob-sub-row:last-child {
    border-bottom: none;
}
</style>
@endsection

@section('content')
<div class="content-wrapper" style="background: #f4f6f9; padding: 8px;">

    <!-- 1. Hero Header -->
    <div class="mob-report-hero">
        <div class="mob-report-top">
            <div class="mob-report-title">
                <i class="fa fa-line-chart text-warning"></i>
                <span>Exam Wise Report</span>
            </div>
            <span class="mob-badge-step">
                @if(!$hasReportData) Filter @else Report Ready @endif
            </span>
        </div>
        <div style="font-size: 10px; color: #cbd5e1; line-height: 1.2;">
            @if(!$hasReportData)
                Select class, exam and subjects to generate performance report.
            @else
                Showing report for {{ $className->name ?? 'Class' }} &bull; {{ $exam->name ?? 'Exam' }}.
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger py-1 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Filter Card -->
    <form method="post" action="{{ url('exam_wise_report') }}" id="mobFilterForm">
        @csrf
        <div class="mob-card">
            <div class="mob-card-head">
                <span><i class="fa fa-filter text-primary"></i> Report Filter</span>
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                <select name="class_type_id" id="mob_class_type_id" class="mob-select" required>
                    <option value="">-- Select Class --</option>
                    @foreach($classType as $class)
                        <option value="{{ $class->id }}" {{ $selectedClassId === (int)$class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-leanpub text-primary"></i> Exam <span class="text-danger">*</span></label>
                <select name="exam_id" id="mob_exam_id" class="mob-select" required>
                    <option value="">-- Select Exam --</option>
                    @foreach($examlist as $item)
                        <option value="{{ $item->exam_id }}" {{ $selectedExamId === (int)$item->exam_id ? 'selected' : '' }}>
                            {{ $item->exam_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-book text-primary"></i> Subjects <span class="text-danger">*</span></label>
                <select name="subject_id[]" id="mob_subject_id" class="mob-select" multiple="multiple" style="height:60px;">
                    @foreach($list_subject as $sub)
                        <option value="{{ $sub->id }}" {{ in_array((int)$sub->id, array_map('intval', $selectedSubjectIds), true) ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="button" class="mob-btn mob-btn-secondary" id="mobBtnSelectAll" style="flex: 1;">
                    <i class="fa fa-check-square-o"></i> All Subjects
                </button>
                <button type="submit" class="mob-btn mob-btn-primary" style="flex: 1.5;">
                    <i class="fa fa-file-text-o"></i> Generate Report
                </button>
            </div>
        </div>
    </form>

    @if($hasReportData)
        <!-- Summary KPIs -->
        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <div class="mob-kpi-lbl">Students</div>
                <div class="mob-kpi-val">{{ $summary['total_students'] ?? count($reportRows) }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-lbl">Topper</div>
                <div class="mob-kpi-val" style="font-size:10.5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ $summary['topper_name'] ?? '-' }}
                </div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-lbl">{{ $singleSubjectMode ? 'Avg Score' : 'Avg %' }}</div>
                <div class="mob-kpi-val">
                    {{ $singleSubjectMode ? ($summary['average_marks'] ?? 0) : (($summary['average_percentage'] ?? 0) . '%') }}
                </div>
            </div>
        </div>

        <!-- Download Action Toolbar -->
        <div style="display: flex; gap: 6px; margin-bottom: 8px;">
            <a class="mob-btn mob-btn-primary" style="background:#f59e0b; color:#111; flex: 1;"
               href="{{ route('exam-wise-report.pdf', ['exam_id' => $search['exam_id'], 'class_type_id' => $search['class_type_id'], 'subject_id' => $search['subject_id'], 'admission_id' => $search['admission_id'] ?? null]) }}" download>
                <i class="fa fa-file-pdf-o"></i> Download PDF
            </a>
        </div>

        <!-- Student Records List -->
        <div class="mob-card" style="padding: 6px;">
            <div class="mob-card-head" style="margin-bottom: 6px;">
                <span><i class="fa fa-users text-primary"></i> Results ({{ count($reportRows) }})</span>
            </div>

            @foreach($reportRows as $index => $row)
                @php
                    $pct = (float) ($row['percentage'] ?? 0);
                    $pctColor = $pct >= 60 ? '#059669' : ($pct >= 33 ? '#d97706' : '#dc2626');
                    $pctBg = $pct >= 60 ? '#d1fae5' : ($pct >= 33 ? '#fef3c7' : '#fee2e2');
                    $rank = $singleSubjectMode ? ($row['subject_rank'] ?? '-') : ($row['overall_rank'] ?? '-');
                @endphp
                <div class="mob-student-card">
                    <div class="mob-student-header">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span class="mob-rank-badge">#{{ $rank }}</span>
                            <div>
                                <div style="font-size: 11.5px; font-weight: 800; color: #002C54;">
                                    {{ $row['student_name'] ?? '' }}
                                </div>
                                <div style="font-size: 9.5px; color: #64748b;">Adm: {{ $row['admission_no'] ?? '' }}</div>
                            </div>
                        </div>
                        <span class="mob-pct-badge" style="background: {{ $pctBg }}; color: {{ $pctColor }};">
                            {{ number_format($pct, 2) }}%
                        </span>
                    </div>

                    @if($singleSubjectMode)
                        @php $subRow = $row['subject_rows'][0] ?? null; @endphp
                        <div class="mob-sub-row">
                            <span style="font-weight: 700; color: #334155;">{{ $list_subject->first()->name ?? 'Subject' }}</span>
                            <span>
                                <strong>{{ $subRow['display_marks'] ?? '-' }}</strong> / {{ $summary['single_subject_maximum'] ?? 0 }}
                                <small class="text-muted">(R:{{ $subRow['r_marks'] ?? 0 }}, W:{{ $subRow['w_marks'] ?? 0 }})</small>
                            </span>
                        </div>
                    @else
                        @foreach($row['subject_rows'] as $subRow)
                            <div class="mob-sub-row">
                                <span style="color: #475569;">{{ $subRow['subject_name'] ?? 'Subject' }}</span>
                                <span style="font-weight: 700; color: #002C54;">{{ $subRow['display_marks'] ?? '-' }}</span>
                            </div>
                        @endforeach
                        <div class="mob-sub-row" style="font-weight: 800; border-top: 1px solid #cbd5e1; padding-top: 4px; margin-top: 4px;">
                            <span>Total Obtained:</span>
                            <span style="color: #002C54;">{{ number_format((float) ($row['total_obtained'] ?? 0), 2) }} / {{ number_format((float) ($row['total_maximum'] ?? 0), 2) }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var selectedSubjectIds = @json($search['subject_id'] ?? []);

    // Select all subjects shortcut
    $('#mobBtnSelectAll').on('click', function() {
        var $select = $('#mob_subject_id');
        $select.find('option').prop('selected', true);
        $('#mobFilterForm').submit();
    });

    // Dynamic Class Change
    $('#mob_class_type_id').on('change', function() {
        var baseurl = "{{ url('/') }}";
        var classId = $(this).val();
        var $examSelect = $('#mob_exam_id');
        $examSelect.html('<option value="">Loading exams...</option>');

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            $('#mob_subject_id').empty();
            return;
        }

        $.ajax({
            url: baseurl + '/examData/' + classId,
            success: function(res) {
                $examSelect.html(res);
            }
        });

        $.ajax({
            url: baseurl + '/subjectGetData/' + classId,
            success: function(data) {
                $('#mob_subject_id').html(data);
                if (selectedSubjectIds && selectedSubjectIds.length) {
                    $('#mob_subject_id').val(selectedSubjectIds);
                }
            }
        });
    });
});
</script>
@endsection
@extends('layout.app')

@section('title', 'Fill Marks by Excel - Examination Management')

@section('styles')
<style>
/* Fill Marks by Excel - Unified Arise Theme */
.excel-page-wrapper {
    background-color: #f4f6f9;
    padding: 8px 12px 20px 12px;
}

/* 1. Hero Banner */
.excel-hero {
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
.excel-hero-text {
    max-width: 60%;
}
.excel-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    color: #cbd5e1;
    display: block;
    margin-bottom: 2px;
}
.excel-title {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 2px 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 8px;
}
.excel-subtitle {
    font-size: 11px;
    margin: 0;
    color: #e2e8f0;
    line-height: 1.3;
}
.excel-hero-actions {
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
.excel-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    margin-bottom: 10px;
}
.excel-card-header {
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
.excel-card-header .header-badge {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
}
.excel-card-body {
    padding: 10px 14px;
}

/* Form inputs & Select2 */
.excel-card-body label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.excel-card-body .form-control {
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    height: 32px;
    padding: 4px 8px;
}
.excel-card-body .form-control:focus {
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

/* Upload Zone */
.upload-zone {
    border: 2px dashed #94a3b8;
    background: #f8fafc;
    border-radius: 4px;
    padding: 20px 15px;
    text-align: center;
    transition: all 0.2s;
    cursor: pointer;
}
.upload-zone:hover {
    border-color: #002C54;
    background: #e6f0fa;
}

/* Instructions & Guidelines */
.guide-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #002C54;
    padding: 10px 12px;
    border-radius: 2px;
}
.guide-box-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.guide-box ul {
    margin: 0;
    padding-left: 18px;
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
}

/* Mapping Table */
.mapping-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.mapping-table th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    padding: 6px 8px;
    text-align: center;
    border: 1px solid #001f3f;
    font-size: 11px;
    white-space: nowrap;
}
.mapping-table td {
    padding: 5px 6px;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}
.mapping-table tr:nth-child(even) td {
    background: #f8fafc;
}
.mapping-table tr:hover td {
    background: #e6f0fa;
}
.mapping-table .form-control {
    height: 28px !important;
    font-size: 11.5px !important;
    padding: 2px 6px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
}
</style>
@endsection

@section('content')

@php
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $examlist = $examlist ?? collect();
    $subjects = $subjects ?? collect();
    $classTypeId = (int) ($search['class_type_id'] ?? 0);
    $examId = (int) ($search['exam_id'] ?? 0);
    $selectedClassName = $classType->firstWhere('id', $classTypeId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $examId)->exam_name ?? 'Exam Not Selected';
@endphp

<div class="content-wrapper excel-page-wrapper">
    
    {{-- 1. Signature Hero Banner --}}
    <div class="excel-hero">
        <div class="excel-hero-text">
            <span class="excel-kicker"><i class="fa fa-file-excel-o mr-1"></i> Marks Automation &amp; Batch Processing</span>
            <h1 class="excel-title">
                <i class="fa fa-upload"></i> Fill Marks By Excel
            </h1>
            <p class="excel-subtitle">
                Select class and exam, upload your spreadsheet, and map columns to automatically update marks.
            </p>
        </div>
        <div class="excel-hero-actions">
            <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="View All Exams">
                <i class="fa fa-leanpub"></i> 1. Exam Setup
            </a>
            <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Manual Marks Entry">
                <i class="fa fa-pencil-square-o"></i> 3. Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                <i class="fa fa-bar-chart"></i> 4. Exam Report
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
            <a href="{{ url('fill-marks-by-excel') }}" class="workflow-pill active">
                <i class="fa fa-check-circle"></i> 2. Excel Import
            </a>
            <a href="{{ url('fill_marks') }}" class="workflow-pill">
                3. Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="workflow-pill">
                4. Exam Report
            </a>
        </div>
    </div>

    {{-- 3. Selection Toolbar Card --}}
    <div class="excel-card">
        <div class="excel-card-header">
            <span><i class="fa fa-filter mr-1"></i> Step 1: Select Class &amp; Examination</span>
            <span class="header-badge">Target Selection</span>
        </div>
        <div class="excel-card-body">
            <form method="get" action="{{ url('fill-marks-by-excel') }}" id="selectionForm">
                <div class="row align-items-end">
                    
                    {{-- Class Selection --}}
                    <div class="col-md-4 form-group mb-2">
                        <label><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="class_type_id" id="class_type_id" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classType as $class)
                                <option value="{{ $class->id }}" {{ $classTypeId === (int)$class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Exam Selection --}}
                    <div class="col-md-4 form-group mb-2">
                        <label><i class="fa fa-leanpub text-primary"></i> Examination <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="exam_id" id="exam_id" required>
                            <option value="">-- Select Exam --</option>
                            @foreach($examlist as $exam)
                                <option value="{{ $exam->exam_id }}" {{ $examId === (int)$exam->exam_id ? 'selected' : '' }}>
                                    {{ $exam->exam_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="col-md-4 form-group mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary flex-grow-1" style="height:32px; font-size:12px; font-weight:700; border-radius:2px; background:#002C54; border-color:#001f3f;">
                            <i class="fa fa-check mr-1"></i> Continue to Upload
                        </button>
                        @if($classTypeId || $examId)
                            <a href="{{ url('fill-marks-by-excel') }}" class="btn btn-outline-secondary" style="height:32px; font-size:12px; border-radius:2px;" title="Reset Selection">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    @if(!empty($classTypeId) && !empty($examId))
        
        {{-- If no subjects found --}}
        @if(($subjects ?? collect())->isEmpty())
            <div class="alert alert-warning py-3 px-3 mb-3" style="border-radius:2px; font-size:12px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> No subjects were found for <strong>{{ $selectedClassName }}</strong>, or this examination is not assigned to it. 
                <a href="{{ url('assign/exam/'.$examId) }}" class="font-weight-bold ml-1 text-dark">Assign classes now &rarr;</a>
            </div>
        @else

            {{-- 4. Upload Result Excel Card --}}
            <div class="excel-card">
                <div class="excel-card-header">
                    <span><i class="fa fa-upload mr-1"></i> Step 2: Upload Result Spreadsheet</span>
                    <span class="header-badge">{{ $selectedClassName }} &bull; {{ $selectedExamName }}</span>
                </div>
                <div class="excel-card-body">
                    <div class="row">
                        <div class="col-lg-7">
                            <form method="post" action="{{ route('marks.mapping.prepare') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="class_type_id" value="{{ $classTypeId }}">
                                <input type="hidden" name="exam_id" value="{{ $examId }}">
                                
                                <div class="upload-zone mb-3">
                                    <i class="fa fa-file-excel-o text-success mb-2" style="font-size:32px;"></i>
                                    <div class="font-weight-bold text-dark mb-1" style="font-size:12.5px;">Choose Excel or CSV Result Sheet</div>
                                    <div class="text-muted mb-2" style="font-size:11px;">Supported Formats: .xlsx, .xls, .csv (Max: 10MB)</div>
                                    
                                    <div class="custom-file mx-auto" style="max-width:320px;">
                                        <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                                        <label class="custom-file-label text-left" for="excel_file" style="border-radius:2px; font-size:11.5px;">Select file...</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block" style="height:34px; font-size:12.5px; font-weight:700; border-radius:2px; background:#002C54; border-color:#001f3f;">
                                    <i class="fa fa-cogs mr-1"></i> Upload &amp; Prepare Column Mapping
                                </button>
                            </form>
                        </div>

                        <div class="col-lg-5">
                            <div class="guide-box">
                                <div class="guide-box-title">
                                    <i class="fa fa-lightbulb-o text-warning"></i> Guidelines for Fast Import:
                                </div>
                                <ul>
                                    <li><strong>Candidate ID:</strong> Make sure one column has the Student Admission Number (e.g. <code>101</code>, <code>ADM-001</code>).</li>
                                    <li><strong>Subject Columns:</strong> You can map columns for Max Marks, Min Marks, R (Right), W (Wrong), L (Left), or Final Marks.</li>
                                    <li><strong>Header Row:</strong> Ensure the first row of the sheet contains column titles.</li>
                                    <li><strong>Safe Sync:</strong> Existing marks will be safely updated without affecting other student records.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

        {{-- 5. Interactive Column Mapping Section (When File Uploaded) --}}
        @if(!empty($importToken) && !empty($mappingHeaders))
            <form method="post" action="{{ route('marks.mapping.save') }}" id="mappingSaveForm">
                @csrf
                <input type="hidden" name="import_token" value="{{ $importToken }}">
                
                <div class="excel-card">
                    <div class="excel-card-header" style="background:#0f3460;">
                        <span><i class="fa fa-exchange mr-1 text-warning"></i> Step 3: Map Spreadsheet Columns to Subjects</span>
                        <span class="header-badge">{{ $uploadedRowCount ?? 0 }} Data Rows Found</span>
                    </div>
                    <div class="excel-card-body">
                        
                        {{-- Candidate Column Selector --}}
                        <div class="p-2 mb-3 bg-light border" style="border-radius:2px;">
                            <div class="row align-items-center">
                                <label class="col-md-4 col-form-label font-weight-bold text-dark" style="font-size:12px;">
                                    <i class="fa fa-id-badge text-primary mr-1"></i> Candidate ID / Admission No Column <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-6">
                                    <select name="candidate_column" class="form-control select2" required>
                                        <option value="">-- Select Excel column for Student ID --</option>
                                        @foreach($mappingHeaders as $columnIndex => $header)
                                            <option value="{{ $columnIndex }}"
                                                {{ (string)($candidateColumn ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                                {{ $header }} (Column {{ $columnIndex + 1 }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Subject Column Mappings Table --}}
                        <div class="table-responsive">
                            <table class="mapping-table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="min-width: 170px; text-align:left; padding-left:12px;">Subject Name</th>
                                        <th style="min-width: 150px;">Max Marks</th>
                                        <th style="min-width: 150px;">Min Marks</th>
                                        <th style="min-width: 130px;">R (Right)</th>
                                        <th style="min-width: 130px;">W (Wrong)</th>
                                        <th style="min-width: 130px;">L (Left)</th>
                                        <th style="min-width: 160px; background:#001f3f !important;">Marks Scored</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $subject)
                                        <tr>
                                            <th style="text-align:left; padding-left:12px; font-weight:700; color:#002C54;">
                                                <i class="fa fa-book text-muted mr-1"></i> {{ $subject->sub_name ?: $subject->name }}
                                            </th>
                                            @foreach(['maximum' => 'Max', 'minimum' => 'Min', 'r' => 'R', 'w' => 'W', 'l' => 'L', 'marks' => 'Marks'] as $type => $label)
                                                <td>
                                                    <select name="mapping[{{ $subject->id }}][{{ $type }}]" class="form-control form-control-sm">
                                                        <option value="">-- None --</option>
                                                        @foreach($mappingHeaders as $columnIndex => $header)
                                                            <option value="{{ $columnIndex }}"
                                                                {{ (string)($mappingDefaults[$subject->id][$type] ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                                                {{ $header }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Save Button --}}
                        <div class="text-center mt-3 pt-2 border-top">
                            <button type="submit" class="btn btn-success px-5" style="height:36px; font-size:13px; font-weight:800; border-radius:2px; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                                <i class="fa fa-save mr-1"></i> Save &amp; Import Mapped Marks
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        @endif

        {{-- Quick Nav Footer --}}
        <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
            <span class="text-muted" style="font-size:11px;">
                <i class="fa fa-info-circle mr-1"></i> Ready to review? You can edit individual scores in Manual Marks or generate reports.
            </span>
            <div class="d-flex align-items-center" style="gap:6px;">
                <a class="btn btn-sm btn-outline-primary" style="border-radius:2px; font-size:11.5px;" href="{{ url('fill_marks?class_type_id='.$classTypeId.'&exam_id='.$examId) }}">
                    <i class="fa fa-pencil-square-o mr-1"></i> Review in Manual Marks
                </a>
                <a class="btn btn-sm btn-outline-success" style="border-radius:2px; font-size:11.5px;" href="{{ url('exam_wise_report?class_type_id='.$classTypeId.'&exam_id='.$examId) }}">
                    <i class="fa fa-bar-chart mr-1"></i> Open Exam Report
                </a>
            </div>
        </div>

    @endif

</div>

<script>
$(document).ready(function () {
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    }

    // Dynamic examination list loader when class changes
    $('#class_type_id').on('change', function () {
        var classTypeId = $(this).val();
        var $examSelect = $('#exam_id');
        
        $examSelect.html('<option value="">Loading examinations...</option>');
        if ($.fn.select2) {
            $examSelect.trigger('change.select2');
        }
        
        if (classTypeId) {
            $.ajax({
                url: "{{ url('examData') }}/" + classTypeId,
                type: "GET",
                success: function (data) {
                    $examSelect.html(data);
                    if ($.fn.select2) {
                        $examSelect.trigger('change.select2');
                    }
                },
                error: function () {
                    $examSelect.html('<option value="">-- Select Exam --</option>');
                    if ($.fn.select2) {
                        $examSelect.trigger('change.select2');
                    }
                }
            });
        } else {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            if ($.fn.select2) {
                $examSelect.trigger('change.select2');
            }
        }
    });

    // Custom file input file name updater
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Select file...');
    });
});
</script>
@endsection
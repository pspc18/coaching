@extends('layout.app')

@section('title', 'Fill Marks (Manual Entry) - Examination Management')

@section('styles')
<style>
/* Fill Marks - Unified Arise Theme */
.marks-page-wrapper {
    background-color: #f4f6f9;
    padding: 8px 12px 20px 12px;
}

/* 1. Hero Banner */
.marks-hero {
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
.marks-hero-text {
    max-width: 60%;
}
.marks-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    color: #cbd5e1;
    display: block;
    margin-bottom: 2px;
}
.marks-title {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 2px 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 8px;
}
.marks-subtitle {
    font-size: 11px;
    margin: 0;
    color: #e2e8f0;
    line-height: 1.3;
}
.marks-hero-actions {
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
.marks-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    margin-bottom: 10px;
}
.marks-card-header {
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
.marks-card-header .header-badge {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
}
.marks-card-body {
    padding: 10px 14px;
}

/* Form inputs & Select2 */
.marks-card-body label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.marks-card-body .form-control {
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    height: 32px;
    padding: 4px 8px;
}
.marks-card-body .form-control:focus {
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

/* Note Banner */
.marks-legend-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #002C54;
    padding: 6px 10px;
    border-radius: 2px;
    font-size: 11px;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.marks-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-right: 12px;
    font-weight: 600;
}

/* Data Table Grid */
.marks-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
}
.marks-table thead th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    padding: 6px 6px;
    text-align: center;
    border: 1px solid #001f3f;
    font-size: 11px;
    white-space: nowrap;
}
.marks-table thead th.sub-th {
    background: #0f3460;
    font-size: 10.5px;
    padding: 4px 6px;
}
.marks-table tbody td {
    padding: 4px 5px;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}
.marks-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.marks-table tbody tr:hover td {
    background: #e6f0fa;
}

/* Input Cell Styling */
.marks-cell-input {
    width: 100%;
    height: 26px !important;
    font-size: 11.5px !important;
    font-weight: 600 !important;
    text-align: center;
    padding: 2px 4px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
    transition: all 0.15s ease;
}
.marks-cell-input:focus {
    border-color: #002C54 !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15) !important;
    outline: none;
}
.marks-cell-input.bg-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #f87171 !important;
}
.marks-cell-input.bg-warning {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}

/* Student Badge */
.student-avatar-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 5px;
}
</style>
@endsection

@section('content')

@php
    $classType = Helper::classType();
    $search = $search ?? [];
    $requestedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $selectedSubjectIds = collect($selectedSubjectIds ?? old('subject_name_id', $search['subject_name_id'] ?? []))
        ->map(function ($id) {
            return (int) $id;
        })
        ->filter()
        ->values()
        ->all();
    $showMarksSection = (bool) ($showMarksSection ?? false);
    $subjects = $subjects ?? collect();
    $Allsubjects = $Allsubjects ?? collect();
    $data2 = $data2 ?? collect();
    $examlist = $examlist ?? collect();
    $selectedClassName = $classType->firstWhere('id', $requestedClassId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $selectedExamId)->exam_name ?? 'Exam Not Selected';
    $roleId = Session::get('role_id');
    $isPublished = $isPublished ?? false;
    $fillMinMaxMarksMap = $fillMinMaxMarksMap ?? collect();
    $existingMarksMap = $existingMarksMap ?? collect();
    $studentSubjectAssignments = $studentSubjectAssignments ?? [];
    $classOrderBy = $classOrderBy ?? 0;
@endphp

<div class="content-wrapper marks-page-wrapper">
    
    {{-- 1. Signature Hero Banner --}}
    <div class="marks-hero">
        <div class="marks-hero-text">
            <span class="marks-kicker"><i class="fa fa-pencil-square-o mr-1"></i> Examination Marks Recording &amp; Grading</span>
            <h1 class="marks-title">
                <i class="fa fa-leanpub"></i> Fill Marks (Manual Entry)
            </h1>
            <p class="marks-subtitle">
                Enter, review, and evaluate student marks, right/wrong/left counts, and minimum/maximum pass criteria.
            </p>
        </div>
        <div class="marks-hero-actions">
            <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="View All Exams">
                <i class="fa fa-leanpub"></i> 1. Exam Setup
            </a>
            <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                <i class="fa fa-file-excel-o"></i> 2. Excel Import
            </a>
            <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                <i class="fa fa-bar-chart"></i> 4. Exam Report
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
            <a href="{{ url('fill_marks') }}" class="workflow-pill active">
                <i class="fa fa-check-circle"></i> 3. Manual Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="workflow-pill">
                4. Exam Report
            </a>
        </div>
    </div>

    {{-- 3. Selection Toolbar Card --}}
    <div class="marks-card">
        <div class="marks-card-header">
            <span><i class="fa fa-filter mr-1"></i> Step 1: Select Class, Exam &amp; Subjects</span>
            <span class="header-badge">Target Criteria</span>
        </div>
        <div class="marks-card-body">
            <form method="post" action="{{ url('fill_marks') }}" id="selectionForm">
                @csrf
                <div class="row align-items-end">
                    
                    {{-- Class Selection --}}
                    <div class="col-md-3 form-group mb-2">
                        <label><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                        <select class="form-control select2" name="class_name" id="class_type_id" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classType as $class)
                                <option value="{{ $class->id }}" {{ $requestedClassId === (int)$class->id ? 'selected' : '' }}>
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
                            @foreach($examlist as $exam)
                                <option value="{{ $exam->exam_id }}" {{ $selectedExamId === (int)$exam->exam_id ? 'selected' : '' }}>
                                    {{ $exam->exam_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subject Multiselect --}}
                    <div class="col-md-4 form-group mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="mb-0"><i class="fa fa-book text-primary"></i> Subjects <span class="text-danger">*</span></label>
                            @if(!empty($Allsubjects) && $Allsubjects->count() > 0)
                                <a href="javascript:void(0)" id="btnSelectAllSubjects" style="font-size:10.5px; font-weight:700; color:#002C54; text-decoration:underline;">
                                    Select All
                                </a>
                            @endif
                        </div>
                        <select class="form-control select2" name="subject_name_id[]" id="subject_id" multiple="multiple" required>
                            @foreach($Allsubjects as $sub)
                                <option value="{{ $sub->id }}" {{ in_array((int)$sub->id, $selectedSubjectIds, true) ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-2 form-group mb-2 d-flex" style="gap:6px;">
                        <button type="submit" class="btn btn-primary flex-grow-1" style="height:32px; font-size:12px; font-weight:700; border-radius:2px; background:#002C54; border-color:#001f3f;">
                            <i class="fa fa-search mr-1"></i> Search
                        </button>
                        @if($requestedClassId || $selectedExamId)
                            <a href="{{ url('fill_marks') }}" class="btn btn-outline-secondary" style="height:32px; font-size:12px; border-radius:2px;" title="Reset Filter">
                                <i class="fa fa-refresh"></i>
                            </a>
                        @endif
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- 4. Marks Entry & Scoring Matrix --}}
    @if($showMarksSection)
        
        @if($subjects->isEmpty())
            <div class="alert alert-warning py-3 px-3 mb-3" style="border-radius:2px; font-size:12px;">
                <i class="fa fa-exclamation-triangle mr-1"></i> No subjects were found or selected for <strong>{{ $selectedClassName }}</strong>. Please select subjects to continue.
            </div>
        @else

            <form action="{{ url('fill_marks_submit') }}" method="post" id="fillMarksForm">
                @csrf
                <input type="hidden" name="class_type_id" value="{{ $requestedClassId }}">
                <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">

                {{-- Step 2: Subject Max & Min Marks Setup --}}
                <div class="marks-card">
                    <div class="marks-card-header" style="background:#0f3460;">
                        <span><i class="fa fa-sliders mr-1 text-warning"></i> Step 2: Configure Max &amp; Passing Min Marks</span>
                        <span class="header-badge">{{ $subjects->count() }} Subjects</span>
                    </div>
                    <div class="marks-card-body p-0">
                        <div class="table-responsive">
                            <table class="marks-table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="text-align:left; padding-left:12px;">Subject Name</th>
                                        <th style="width: 220px;">Maximum Marks <span class="text-danger">*</span></th>
                                        <th style="width: 220px;">Minimum Passing Marks <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjects as $key => $item)
                                        @php
                                            $old_value = $fillMinMaxMarksMap->get($item->id);
                                            $maxMarks = $old_value->exam_maximum_marks ?? 100;
                                            $minMarks = $old_value->exam_minimum_marks ?? 30;
                                        @endphp
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted">{{ $key + 1 }}</td>
                                            <td style="text-align:left; padding-left:12px; font-weight:700; color:#002C54;">
                                                <i class="fa fa-book text-muted mr-1"></i>
                                                {{ $item->sub_name ?: $item->name }}
                                            </td>
                                            <td class="p-1">
                                                <input type="hidden" name="fill_min_max_marks_id[]" class="max_min" data-max_min="{{ $maxMarks }}" value="{{ $old_value->id ?? '' }}"/>
                                                <input type="hidden" name="subject_id[]" value="{{ $item->id }}" />
                                                <input type="number" name="exam_maximum_marks[]" value="{{ $maxMarks }}" min="0" step="0.01" required 
                                                       class="form-control form-control-sm maximum_marks_input marks-cell-input" 
                                                       id="subject_{{ $item->id }}" style="max-width:160px; margin:0 auto;" />
                                            </td>
                                            <td class="p-1">
                                                <input type="number" name="exam_minimum_marks[]" value="{{ $minMarks }}" min="0" step="0.01" required 
                                                       class="form-control form-control-sm minimum_marks_input marks-cell-input" 
                                                       data-maximum-id="subject_{{ $item->id }}" id="minimum_marks_{{ $item->id }}" style="max-width:160px; margin:0 auto;" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Legend & Fast Entry Tips --}}
                <div class="marks-legend-banner">
                    <div>
                        <strong class="text-dark mr-2"><i class="fa fa-keyboard-o text-primary"></i> Attendance / Status Codes:</strong>
                        <span class="marks-legend-item"><span class="badge badge-secondary">[AB]</span> Absent</span>
                        <span class="marks-legend-item"><span class="badge badge-info">[M]</span> Medical</span>
                        <span class="marks-legend-item"><span class="badge badge-warning">[JL]</span> Join Late</span>
                        <span class="marks-legend-item"><span class="badge badge-dark">[T]</span> Trivial</span>
                        <span class="marks-legend-item"><span class="badge badge-danger">[F]</span> Fail</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-xs btn-outline-success" onclick="downloadCSV()" title="Export CSV Sheet">
                            <i class="fa fa-download mr-1"></i> Download CSV
                        </button>
                    </div>
                </div>

                {{-- Step 3: Students Marks Matrix --}}
                <div class="marks-card">
                    <div class="marks-card-header">
                        <span><i class="fa fa-users mr-1"></i> Step 3: Enter Student Marks &amp; R/W/L Counts</span>
                        <span class="header-badge">{{ $data2->count() }} Students Enrolled</span>
                    </div>
                    <div class="marks-card-body p-0">
                        <div class="table-responsive">
                            <table class="marks-table table-bordered m-0" id="thead_data_table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th style="min-width: 90px;">Adm No</th>
                                        <th style="min-width: 150px; text-align:left; padding-left:8px;">Student Name</th>
                                        <th style="min-width: 140px; text-align:left; padding-left:8px;">Father's Name</th>
                                        @foreach($subjects as $item)
                                            @php
                                                $maxVal = $fillMinMaxMarksMap->get($item->id)->exam_maximum_marks ?? 100;
                                            @endphp
                                            <th colspan="4" class="text-center" style="min-width:240px; background:#002C54 !important;">
                                                <i class="fa fa-book mr-1"></i> {{ $item->sub_name ?: $item->name }} ({{ $maxVal }})
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th colspan="4" style="background:#f1f5f9; border-color:#e2e8f0;"></th>
                                        @foreach($subjects as $item)
                                            <th class="sub-th" style="width:45px;" title="Right Questions">R</th>
                                            <th class="sub-th" style="width:45px;" title="Wrong Questions">W</th>
                                            <th class="sub-th" style="width:45px;" title="Left Questions">L</th>
                                            <th class="sub-th font-weight-bold" style="width:75px; background:#001f3f !important;">Marks</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data2 as $key => $student)
                                        <tr>
                                            <td class="text-center font-weight-bold text-muted">{{ $key + 1 }}</td>
                                            <td class="text-center font-weight-bold">
                                                <span class="badge badge-light border" style="font-size:11px;">{{ $student->admissionNo }}</span>
                                            </td>
                                            <td style="text-align:left; padding-left:8px;">
                                                <div class="d-flex align-items-center">
                                                    <span class="student-avatar-badge">{{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}</span>
                                                    <span class="font-weight-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                </div>
                                            </td>
                                            <td style="text-align:left; padding-left:8px; color:#475569;">
                                                {{ $student->father_name ?? '-' }}
                                            </td>
                                            <input type="hidden" name="admission_id[]" value="{{ $student->id }}">

                                            @foreach($subjects as $item1)
                                                @php
                                                    $mapKey = $student->id . '_' . $item1->id;
                                                    $old_marks = $existingMarksMap->get($mapKey);
                                                    $minMaxConfig = $fillMinMaxMarksMap->get($item1->id);
                                                    $minPassingMarks = $minMaxConfig->exam_minimum_marks ?? 30;

                                                    $isEditable = ($classOrderBy <= 10) || !empty($studentSubjectAssignments[$student->id][$item1->id]);
                                                    $placeholder = $isEditable ? ($item1->sub_name ?: $item1->name) : 'Not Assigned';
                                                    $notAssignedClass = $isEditable ? '' : 'bg-warning';

                                                    $oldMarks = $old_marks->student_marks ?? '';
                                                    $oldRMarks = $old_marks->r_marks ?? '';
                                                    $oldWMarks = $old_marks->w_marks ?? '';
                                                    $oldLMarks = $old_marks->l_marks ?? '';

                                                    $displayMarks = $isEditable ? $oldMarks : '';
                                                    $displayRMarks = $isEditable ? $oldRMarks : '';
                                                    $displayWMarks = $isEditable ? $oldWMarks : '';
                                                    $displayLMarks = $isEditable ? $oldLMarks : '';

                                                    $isFailClass = '';
                                                    if ($isEditable && is_numeric($oldMarks) && (float)$oldMarks < (float)$minPassingMarks) {
                                                        $isFailClass = 'bg-danger';
                                                    }
                                                @endphp

                                                <input type="hidden" name="fill_marks_id[]" value="{{ $old_marks->id ?? '' }}"/>

                                                {{-- R (Right) --}}
                                                <td class="p-1 text-center">
                                                    <input type="text" name="r_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="20" placeholder="R" value="{{ $displayRMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:42px;" />
                                                </td>

                                                {{-- W (Wrong) --}}
                                                <td class="p-1 text-center">
                                                    <input type="text" name="w_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="20" placeholder="W" value="{{ $displayWMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:42px;" />
                                                </td>

                                                {{-- L (Left) --}}
                                                <td class="p-1 text-center">
                                                    <input type="text" name="l_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="20" placeholder="L" value="{{ $displayLMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:42px;" />
                                                </td>

                                                {{-- Marks Scored --}}
                                                <td class="p-1 text-center">
                                                    <input type="text" name="student_marks[]" 
                                                           class="marks-cell-input marks_add_input marks_subject numbers {{ $isFailClass }} {{ $notAssignedClass }}"
                                                           data-subject_id="{{ $item1->id }}" 
                                                           data-old_marks="{{ $oldMarks }}" 
                                                           placeholder="{{ $placeholder }}" 
                                                           value="{{ $displayMarks }}" 
                                                           oninput="this.value = this.value.toUpperCase()" 
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:68px;" />

                                                    <span class="marks_visible text-light" style="display:none;">{{ $oldMarks }}</span>
                                                    <input type="hidden" name="check_null[]" value="{{ $oldMarks }}"/>
                                                    <input type="hidden" name="subject_id_fill[]" value="{{ $item1->id }}"/>
                                                    <input type="hidden" name="other_subject[]" value="{{ $item1->other_subject ?? '' }}"/>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center py-4 text-muted" colspan="30">
                                                <i class="fa fa-info-circle mr-1"></i> No active students found for <strong>{{ $selectedClassName }}</strong> in this session.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Submit Section --}}
                        @if($data2->isNotEmpty())
                            <div class="p-3 text-center border-top bg-light">
                                @if(!$isPublished || (int)$roleId === 1)
                                    <button type="button" class="btn btn-success px-5" data-toggle="modal" data-target="#fillMarksConfirmModal"
                                            style="height:36px; font-size:13px; font-weight:800; border-radius:2px; box-shadow: 0 2px 6px rgba(16,185,129,0.3);">
                                        <i class="fa fa-save mr-1"></i> Submit &amp; Save Marks
                                    </button>
                                @else
                                    <span class="badge badge-warning p-2" style="font-size:12px;">
                                        <i class="fa fa-lock mr-1"></i> Results for this exam are already published. Only Administrators can edit marks.
                                    </span>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </form>

        @endif

    @endif

</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="fillMarksConfirmModal" tabindex="-1" role="dialog" aria-labelledby="fillMarksConfirmModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius:4px; overflow:hidden; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
         <div class="modal-header" style="background:#002C54; color:#ffffff; padding:10px 16px;">
            <h5 class="modal-title" id="fillMarksConfirmModalLabel" style="font-size:14px; font-weight:700;">
                <i class="fa fa-check-circle mr-1 text-success"></i> Confirm Marks Submission
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body" style="font-size:12.5px; color:#334155; padding:16px;">
            <p class="mb-2 font-weight-bold">Please verify the following before submitting:</p>
            <ul class="mb-0 pl-3" style="line-height: 1.6;">
               <li>Subject Max &amp; Minimum pass marks are properly set.</li>
               <li>Student scores and status codes (<code>AB</code>, <code>M</code>, <code>JL</code>) are verified.</li>
               <li>Unconducted subjects can remain <code>0</code>.</li>
               <li>Existing student marks will be updated accurately.</li>
            </ul>
         </div>
         <div class="modal-footer bg-light" style="padding:8px 16px;">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:2px; font-weight:600;">Cancel</button>
            <button type="button" class="btn btn-success btn-sm px-3" id="confirmFillMarksSubmit" style="border-radius:2px; font-weight:700; background:#10b981; border-color:#059669;">
                <i class="fa fa-save mr-1"></i> Confirm &amp; Save
            </button>
         </div>
      </div>
   </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4', width: '100%' });
    }

    // Select all subjects shortcut
    $('#btnSelectAllSubjects').on('click', function() {
        var $select = $('#subject_id');
        var allValues = $select.find('option').map(function() {
            return $(this).val();
        }).get();
        $select.val(allValues).trigger('change');
    });

    // Dynamic Exam and Subject Loader on Class Change
    $('#class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#exam_id');
        var $subjectSelect = $('#subject_id');
        
        $examSelect.html('<option value="">Loading examinations...</option>');
        $subjectSelect.html('<option value="">Loading subjects...</option>');
        if ($.fn.select2) {
            $examSelect.trigger('change.select2');
            $subjectSelect.trigger('change.select2');
        }

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            $subjectSelect.html('');
            if ($.fn.select2) {
                $examSelect.trigger('change.select2');
                $subjectSelect.trigger('change.select2');
            }
            return;
        }

        // 1. Fetch Exams
        $.ajax({
            url: "{{ url('examData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $examSelect.html(res);
                if ($.fn.select2) {
                    $examSelect.trigger('change.select2');
                }
            },
            error: function() {
                $examSelect.html('<option value="">-- Select Exam --</option>');
                if ($.fn.select2) {
                    $examSelect.trigger('change.select2');
                }
            }
        });

        // 2. Fetch Subjects
        $.ajax({
            url: "{{ url('subjectGetData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $subjectSelect.html(res);
                // Auto-select all available subjects for convenience
                var allValues = $subjectSelect.find('option').map(function() {
                    var v = $(this).val();
                    return v ? v : null;
                }).get().filter(Boolean);
                $subjectSelect.val(allValues);
                if ($.fn.select2) {
                    $subjectSelect.trigger('change.select2');
                }
            },
            error: function() {
                $subjectSelect.html('');
                if ($.fn.select2) {
                    $subjectSelect.trigger('change.select2');
                }
            }
        });
    });

    // Real-time marks validation
    $('.marks_add_input').on('input', function() {
        $(this).removeClass('bg-danger');
        var subject_id = $(this).data('subject_id');
        var maximum_marks = parseFloat($('#subject_' + subject_id).val()) || 100;
        var minimum_marks = parseFloat($('#minimum_marks_' + subject_id).val()) || 0;
        var val = $(this).val().trim().toUpperCase();

        if (val === '') return;

        var allowedCodes = ['AB', 'M', 'JL', 'T', 'F', 'A', 'B', 'C', 'D'];
        if (allowedCodes.indexOf(val) !== -1) {
            return; // Valid status code
        }

        var num = parseFloat(val);
        if (isNaN(num)) {
            toastr.error('Invalid mark format. Enter numeric value or AB/M/JL/T/F.');
            $(this).val('');
            return;
        }

        if (num > maximum_marks) {
            toastr.error('Marks cannot exceed Maximum allowed (' + maximum_marks + ').');
            $(this).val('');
            return;
        }

        if (num < minimum_marks) {
            $(this).addClass('bg-danger');
        }
    });

    // Confirmation submit handler
    var fillMarksSubmitting = false;
    $('#confirmFillMarksSubmit').on('click', function () {
        if (fillMarksSubmitting) return;
        fillMarksSubmitting = true;
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        $('#fillMarksConfirmModal').find('button').not($button).prop('disabled', true);
        document.getElementById('fillMarksForm').submit();
    });
});

// CSV Export Helper
function downloadCSV() {
    $('.marks_visible').show();
    var table = document.getElementById("thead_data_table");
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
    var filename = "marks_entry_export.csv";
    var link = document.createElement("a");
    link.style.display = "none";
    link.setAttribute("href", 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv_string));
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    $('.marks_visible').hide();
}
</script>
@endsection
@extends('layout.app')

@section('title', 'Fill Marks (Manual Entry) - Examination Management')

@php
    $classType = $classType ?? Helper::classType();
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
    $defaultActiveClassId = $requestedClassId ?: ($classType->first()->id ?? 0);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - FILL MARKS (ADMISSIONVIEW & VIEW/EXAM 1:1 THEME STANDARD)
   - Viewport fitting: height: calc(100vh - var(--header-height, 56px) - 16px)
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   - Typography: 11.5px table text, 11px uppercase bold thead
   - Sticky dual-row glued table thead with exact column-to-column alignment
   - Pinned bottom bar (#002342)
   - Dedicated centered modal dialogs for Max/Min Setup and Submission Confirm
   ========================================================================== */

.marks-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.marks-page * {
    box-sizing: border-box;
}
.marks-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    max-height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 10px;
    box-sizing: border-box;
}

/* 1. Compact Hero Banner (~38px) */
.marks-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 4px;
    flex-shrink: 0;
}
.marks-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.marks-title {
    font-size: 13.5px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.marks-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
    color: #cbd5e1;
}

/* Hero Meta Chips */
.hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 600;
    color: #e0f2fe;
}

/* Hero Action Buttons */
.marks-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    height: 26px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1.4;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    border-color: rgba(255,255,255,0.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.22);
    color: #ffffff !important;
    border-color: #ffffff;
}

/* 2. Alerts */
.marks-alert {
    border-radius: 2px;
    padding: 4px 10px;
    font-size: 11.5px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.alert-close {
    background: transparent;
    border: none;
    font-size: 15px;
    line-height: 1;
    cursor: pointer;
    color: inherit;
    opacity: 0.7;
}

/* 3. Main Dark Navy Workspace Card (admissionView 1:1 Standard) */
.admission-table-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 0;
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.15);
    position: relative;
}
.dash-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 34px;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}
.badge-target-meta {
    font-size: 10.5px;
    font-weight: 700;
    background: rgba(2, 132, 199, 0.25);
    color: #e0f2fe;
    padding: 2px 8px;
    border-radius: 2px;
    border: 1px solid rgba(56, 189, 248, 0.5);
}
.badge-info-students {
    font-size: 11px;
    font-weight: 700;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    padding: 3px 9px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.badge-info-subjects {
    font-size: 11px;
    font-weight: 700;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    padding: 3px 9px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}

/* 4. Scrollable Container Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    position: relative;
    background: #ffffff;
}

/* 5. Dash Table - Sticky Dual-Row Header with Exact 1:1 Column Alignment */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 25;
}
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 5px 6px;
    height: 32px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 1px solid #001f3d;
    white-space: nowrap;
    z-index: 26;
    vertical-align: middle;
    text-align: center;
}
.header-sub-row th {
    position: sticky;
    top: 32px;
    background: #08335c;
    color: #cbd5e1;
    padding: 3px 4px;
    height: 24px;
    font-size: 10.5px;
    font-weight: 700;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 25;
    text-align: center;
}
.subject-header-meta {
    display: block;
    font-size: 9.5px;
    font-weight: 600;
    color: #93c5fd;
    text-transform: none;
    letter-spacing: normal;
    margin-top: 1px;
}

/* Table Body Cells */
.dash-table tbody td {
    padding: 3px 4px;
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 11.5px;
    color: #1e293b;
    vertical-align: middle;
    background: #ffffff;
}
.dash-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}
.dash-table tbody tr:hover td {
    background: #e6f0fa;
}

/* High-Density Input Controls */
.marks-cell-input {
    height: 24px !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    text-align: center;
    padding: 1px 3px !important;
    border-radius: 2px !important;
    border: 1px solid #cbd5e1 !important;
    transition: all 0.15s ease;
    background: #ffffff;
    color: #0f172a;
    width: 100%;
    min-width: 0;
}
.marks-cell-input:focus {
    border-color: #0284c7 !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 1px #0284c7 !important;
    outline: none;
}
.marks-cell-input.bg-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #f87171 !important;
    font-weight: 700 !important;
}
.marks-cell-input.bg-warning {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}

/* Student Badges */
.badge-class {
    font-size: 10px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #dbeafe;
    font-weight: 600;
    white-space: nowrap;
    display: inline-block;
}
.student-avatar-badge {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 5px;
    flex-shrink: 0;
}

/* Search input */
.classes-search-input {
    width: 100%;
    height: 26px;
    padding: 2px 8px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    background: #ffffff;
}
.classes-search-input:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 1px #002C54;
}

/* 6. Interactive Master-Detail Navigator (When No Exam Loaded) */
.master-detail-workspace {
    display: flex;
    height: 100%;
    min-height: 0;
    background: #ffffff;
}
.classes-sidebar {
    width: 290px;
    min-width: 260px;
    max-width: 320px;
    border-right: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}
.classes-sidebar-header {
    background: #002342;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.classes-search-box {
    padding: 6px 8px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    flex-shrink: 0;
}
.classes-list-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 4px;
}
.class-nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 10px;
    margin-bottom: 3px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #1e293b;
    font-size: 11.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s ease;
    text-decoration: none !important;
}
.class-nav-item:hover {
    background: #e6f0fa;
    border-color: #bfdbfe;
    color: #002C54;
}
.class-nav-item.active {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    border-color: #001f3d;
    color: #ffffff !important;
    box-shadow: 0 1px 3px rgba(0,44,84,.2);
}
.class-nav-item.active i {
    color: #38bdf8 !important;
}
.class-nav-name {
    display: flex;
    align-items: center;
    gap: 7px;
}
.exams-detail-panel {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}
.exams-panel-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
    min-height: 36px;
}
.exams-header-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.exams-panel-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    position: relative;
}

/* 7. Pinned Bottom Footer (~34px) */
.dash-card-footer {
    padding: 4px 10px;
    background: #002342;
    color: #ffffff;
    border-top: 1px solid rgba(255,255,255,.12);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    min-height: 34px;
}
.text-sky {
    color: #38bdf8;
    font-weight: 600;
}
</style>
@endsection

@section('content')
<div class="content-wrapper marks-page">
    <div class="marks-page-layout">

        {{-- 1. Compact Hero Banner (~38px) --}}
        <div class="marks-hero">
            <div>
                <span class="marks-kicker"><i class="fa fa-pencil-square-o mr-1"></i> Examination Marks Recording &amp; Grading</span>
                <h1 class="marks-title">
                    <i class="fa fa-leanpub text-warning"></i> Fill Marks (Manual Entry)
                    @if($requestedClassId && $selectedExamId)
                        <span class="hero-chip"><i class="fa fa-graduation-cap"></i> {{ $selectedClassName }}</span>
                        <span class="hero-chip"><i class="fa fa-calendar-check-o"></i> {{ $selectedExamName }}</span>
                    @endif
                </h1>
            </div>
            <div class="marks-hero-actions">
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="Exam Setup">
                    <i class="fa fa-leanpub"></i> 1. Exam Setup
                </a>
                <a href="{{ url('fill-marks-by-excel') }}{{ $requestedClassId && $selectedExamId ? '?class_type_id='.$requestedClassId.'&exam_id='.$selectedExamId : '' }}" class="dash-btn dash-btn-outline" title="Import via Excel">
                    <i class="fa fa-file-excel-o"></i> 2. Excel Import
                </a>
                <span class="dash-btn dash-btn-light" title="Manual Marks Entry (Active)">
                    <i class="fa fa-check-circle text-primary"></i> 3. Manual Marks
                </span>
                <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                    <i class="fa fa-bar-chart"></i> 4. Exam Report
                </a>
                <a href="{{ url('examination_dashboard') }}" class="dash-btn dash-btn-outline" title="Examination Dashboard">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        {{-- Session Flash Alerts --}}
        @if(session('success') || session('message'))
            <div class="marks-alert alert-success">
                <span><i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="marks-alert alert-danger">
                <span><i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        {{-- 2. Main Dark Navy Workspace Card (admissionView 1:1 Standard) --}}
        <div class="admission-table-card">

            {{-- =========================================================================
                 SCENARIO 1: MARKS ENTRY MODE (Class & Exam are selected)
                 ========================================================================= --}}
            @if($showMarksSection)

                @if($subjects->isEmpty())
                    <div class="p-4 text-center text-muted bg-white h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa fa-exclamation-circle text-warning mb-2" style="font-size:32px;"></i>
                        <h6 class="font-weight-bold text-dark">No subjects found or selected for {{ $selectedClassName }}</h6>
                        <p class="text-muted mb-3" style="font-size:12px;">Please assign subjects to this class or select target subjects to continue.</p>
                        <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-light border">
                            <i class="fa fa-arrow-left mr-1"></i> Back to Class Selector
                        </a>
                    </div>
                @else

                    <form action="{{ url('fill_marks_submit') }}" method="post" id="fillMarksForm" style="display:flex; flex-direction:column; flex:1; min-height:0; overflow:hidden; margin:0;">
                        @csrf
                        <input type="hidden" name="class_type_id" value="{{ $requestedClassId }}">
                        <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">

                        {{-- Card Header --}}
                        <div class="dash-card-header">
                            <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                                <span class="dash-card-title"><i class="fa fa-table text-info"></i> Student Scoring Matrix</span>
                                <span class="badge-target-meta"><i class="fa fa-graduation-cap"></i> {{ $selectedClassName }}</span>
                                <span class="badge-target-meta"><i class="fa fa-calendar-check-o"></i> {{ $selectedExamName }}</span>
                                <span class="badge-total-records"><i class="fa fa-users text-primary mr-1"></i> {{ $data2->count() }} Students</span>
                                <span class="badge-total-records"><i class="fa fa-book text-success mr-1"></i> {{ $subjects->count() }} Subjects</span>
                            </div>

                            <div class="d-flex align-items-center" style="gap:5px;">
                                <input type="text" id="searchStudentInput" class="classes-search-input" placeholder="Filter student..." style="width:160px; height:26px;">
                                <button type="button" class="dash-btn dash-btn-outline" data-toggle="modal" data-target="#maxMinModal" title="Configure Subject Maximum & Passing Marks">
                                    <i class="fa fa-sliders text-warning"></i> Max/Min Setup
                                </button>
                                <button type="button" class="dash-btn dash-btn-outline" onclick="downloadCSV()" title="Export CSV Sheet">
                                    <i class="fa fa-download text-success"></i> CSV
                                </button>
                                <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Switch Class or Exam">
                                    <i class="fa fa-random text-info"></i> Switch Exam
                                </a>
                            </div>
                        </div>

                        {{-- Main Table Scroll Container --}}
                        <div class="table-scroll-container">
                            <table class="dash-table" id="thead_data_table">
                                <thead>
                                    {{-- Row 1: Titles & Subject Group Headers with exact rowspan --}}
                                    <tr class="header-titles-row">
                                        <th rowspan="2" style="width: 36px; text-align: center; vertical-align: middle;">#</th>
                                        <th rowspan="2" style="width: 85px; text-align: center; vertical-align: middle;">Adm No</th>
                                        <th rowspan="2" style="min-width: 170px; text-align: left; padding-left: 10px; vertical-align: middle;">Student Name</th>
                                        <th rowspan="2" style="min-width: 150px; text-align: left; padding-left: 10px; vertical-align: middle;">Father's Name</th>
                                        @foreach($subjects as $item)
                                            @php
                                                $maxVal = $fillMinMaxMarksMap->get($item->id)->exam_maximum_marks ?? 100;
                                                $minVal = $fillMinMaxMarksMap->get($item->id)->exam_minimum_marks ?? 30;
                                            @endphp
                                            <th colspan="4" class="subject-header-col" data-subject-id="{{ $item->id }}" style="border-left: 1px solid rgba(255,255,255,0.25);">
                                                <i class="fa fa-book mr-1 text-info"></i> {{ $item->sub_name ?: $item->name }}
                                                <span class="subject-header-meta" id="header_meta_{{ $item->id }}">(Max: {{ $maxVal }} | Pass: {{ $minVal }})</span>
                                            </th>
                                        @endforeach
                                    </tr>
                                    {{-- Row 2: R / W / L / Marks Sub-columns (NO extra empty th for student columns!) --}}
                                    <tr class="header-sub-row">
                                        @foreach($subjects as $item)
                                            <th style="width: 38px; border-left: 1px solid rgba(255,255,255,0.25);" title="Right Answers">R</th>
                                            <th style="width: 38px;" title="Wrong Answers">W</th>
                                            <th style="width: 38px;" title="Left / Unattempted">L</th>
                                            <th style="width: 75px; background: #001f3f !important; color: #ffffff; font-weight: 800;" title="Marks Scored">Marks</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody id="marksTableBody">
                                    @forelse($data2 as $key => $student)
                                        <tr class="student-mark-row" 
                                            data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}" 
                                            data-adm="{{ strtolower($student->admissionNo ?? '') }}" 
                                            data-father="{{ strtolower($student->father_name ?? '') }}">
                                            
                                            <td class="text-center font-weight-bold text-muted" style="width: 36px;">{{ $key + 1 }}</td>
                                            <td class="text-center" style="width: 85px;">
                                                <span class="badge-class">{{ $student->admissionNo }}</span>
                                            </td>
                                            <td style="min-width: 170px; text-align:left; padding-left:10px;">
                                                <div class="d-flex align-items-center">
                                                    <span class="student-avatar-badge">{{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}</span>
                                                    <span class="font-weight-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                </div>
                                            </td>
                                            <td style="min-width: 150px; text-align:left; padding-left:10px; color:#475569;">
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
                                                    $placeholder = $isEditable ? ($item1->sub_name ?: $item1->name) : 'N/A';
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
                                                <td class="text-center" style="width: 38px; border-left: 1px solid #e2e8f0;">
                                                    <input type="text" name="r_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="10" placeholder="R" value="{{ $displayRMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:34px;" />
                                                </td>

                                                {{-- W (Wrong) --}}
                                                <td class="text-center" style="width: 38px;">
                                                    <input type="text" name="w_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="10" placeholder="W" value="{{ $displayWMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:34px;" />
                                                </td>

                                                {{-- L (Left) --}}
                                                <td class="text-center" style="width: 38px;">
                                                    <input type="text" name="l_marks[]" class="marks-cell-input {{ $notAssignedClass }}"
                                                           maxlength="10" placeholder="L" value="{{ $displayLMarks }}"
                                                           oninput="this.value = this.value.toUpperCase()"
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:34px;" />
                                                </td>

                                                {{-- Marks Scored --}}
                                                <td class="text-center" style="width: 75px;">
                                                    <input type="text" name="student_marks[]" 
                                                           class="marks-cell-input marks_add_input marks_subject numbers {{ $isFailClass }} {{ $notAssignedClass }}"
                                                           data-subject_id="{{ $item1->id }}" 
                                                           data-old_marks="{{ $oldMarks }}" 
                                                           placeholder="{{ $placeholder }}" 
                                                           value="{{ $displayMarks }}" 
                                                           oninput="this.value = this.value.toUpperCase()" 
                                                           {{ $isEditable ? '' : 'readonly' }} style="width:68px; font-weight:700;" />

                                                    <span class="marks_visible text-light" style="display:none;">{{ $oldMarks }}</span>
                                                    <input type="hidden" name="check_null[]" value="{{ $oldMarks }}"/>
                                                    <input type="hidden" name="subject_id_fill[]" value="{{ $item1->id }}"/>
                                                    <input type="hidden" name="other_subject[]" value="{{ $item1->other_subject ?? '' }}"/>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center py-4 text-muted bg-white" colspan="40">
                                                <i class="fa fa-info-circle mr-1"></i> No active students found for <strong>{{ $selectedClassName }}</strong> in this session.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pinned Bottom Footer --}}
                        <div class="dash-card-footer">
                            <div class="d-flex align-items-center flex-wrap" style="gap:8px;">
                                <span>Showing <strong id="visibleStudentCount" class="text-white">{{ $data2->count() }}</strong> of {{ $data2->count() }} Students</span>
                                <span class="d-none d-md-inline text-muted">|</span>
                                <div class="d-none d-md-flex align-items-center" style="gap:5px; font-size:10.5px;">
                                    <span>Status Codes:</span>
                                    <span class="badge badge-secondary py-0 px-1">AB</span> Absent
                                    <span class="badge badge-info py-0 px-1">M</span> Medical
                                    <span class="badge badge-warning py-0 px-1 text-dark">JL</span> Join Late
                                    <span class="badge badge-danger py-0 px-1">F</span> Fail
                                </div>
                            </div>

                            <div>
                                @if(!$isPublished || (int)$roleId === 1)
                                    <button type="button" class="dash-btn dash-btn-light" data-toggle="modal" data-target="#fillMarksConfirmModal"
                                            style="background:#10b981; border-color:#059669; color:#ffffff !important; font-weight:700;">
                                        <i class="fa fa-save mr-1"></i> Submit &amp; Save Marks
                                    </button>
                                @else
                                    <span class="badge badge-warning p-1 px-2" style="font-size:11px;">
                                        <i class="fa fa-lock mr-1"></i> Results Published (Administrator Edit Only)
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Dedicated Clean Modal: Subject Max & Min Pass Marks Setup --}}
                        <div class="modal fade" id="maxMinModal" tabindex="-1" role="dialog" aria-labelledby="maxMinModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content" style="border-radius:3px; overflow:hidden; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.25);">
                                    <div class="modal-header" style="background:#002C54; color:#ffffff; padding:10px 14px;">
                                        <h5 class="modal-title font-weight-bold" id="maxMinModalLabel" style="font-size:13px;">
                                            <i class="fa fa-sliders text-warning mr-1"></i> Configure Subject Maximum &amp; Passing Minimum Marks
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.85;">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                                        <div class="p-2 bg-light border-bottom text-muted" style="font-size:11px;">
                                            <i class="fa fa-info-circle text-primary mr-1"></i> Enter the maximum achievable score and passing cutoff for each subject in <strong>{{ $selectedExamName }}</strong>:
                                        </div>
                                        <table class="dash-table">
                                            <thead>
                                                <tr class="header-titles-row">
                                                    <th style="width: 45px;">#</th>
                                                    <th style="text-align:left; padding-left:12px;">Subject Name</th>
                                                    <th style="width: 170px; text-align:center;">Maximum Marks <span class="text-danger">*</span></th>
                                                    <th style="width: 170px; text-align:center;">Passing Min Marks <span class="text-danger">*</span></th>
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
                                                        <td class="text-center p-2">
                                                            <input type="hidden" name="fill_min_max_marks_id[]" class="max_min" data-max_min="{{ $maxMarks }}" value="{{ $old_value->id ?? '' }}"/>
                                                            <input type="hidden" name="subject_id[]" value="{{ $item->id }}" />
                                                            <input type="number" name="exam_maximum_marks[]" value="{{ $maxMarks }}" min="0" step="0.01" required 
                                                                   class="marks-cell-input maximum_marks_input text-center" 
                                                                   data-subject-id="{{ $item->id }}" id="subject_{{ $item->id }}" 
                                                                   style="width: 120px; height: 28px !important; margin: 0 auto; font-size: 12px !important;" />
                                                        </td>
                                                        <td class="text-center p-2">
                                                            <input type="number" name="exam_minimum_marks[]" value="{{ $minMarks }}" min="0" step="0.01" required 
                                                                   class="marks-cell-input minimum_marks_input text-center" 
                                                                   data-subject-id="{{ $item->id }}" data-maximum-id="subject_{{ $item->id }}" id="minimum_marks_{{ $item->id }}" 
                                                                   style="width: 120px; height: 28px !important; margin: 0 auto; font-size: 12px !important;" />
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="modal-footer bg-light" style="padding:8px 14px;">
                                        <button type="button" class="btn btn-primary btn-sm px-3" data-dismiss="modal" style="border-radius:2px; font-weight:700; font-size:11.5px; background:#002C54; border-color:#001f3d;">
                                            <i class="fa fa-check mr-1"></i> Done &amp; Apply
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>

                @endif

            {{-- =========================================================================
                 SCENARIO 2: SELECTION HUB (Interactive Master-Detail Class & Exam Browser)
                 ========================================================================= --}}
            @else

                <div class="master-detail-workspace">
                    
                    {{-- Left Column: Classes Navigator --}}
                    <div class="classes-sidebar">
                        <div class="classes-sidebar-header">
                            <span><i class="fa fa-graduation-cap text-info mr-1"></i> Academic Classes</span>
                            <span class="badge badge-light" style="font-size:10px;">{{ count($classType) }}</span>
                        </div>
                        <div class="classes-search-box position-relative">
                            <input type="text" id="classSearchInput" class="classes-search-input" placeholder="Quick search class...">
                        </div>
                        <div class="classes-list-container" id="classesList">
                            @foreach($classType as $class)
                                <div class="class-nav-item {{ (int)$class->id === (int)$defaultActiveClassId ? 'active' : '' }}" 
                                     data-id="{{ $class->id }}" 
                                     data-name="{{ strtolower($class->name) }}">
                                    <div class="class-nav-name">
                                        <i class="fa fa-graduation-cap {{ (int)$class->id === (int)$defaultActiveClassId ? 'text-info' : 'text-muted' }}"></i>
                                        <span>{{ $class->name }}</span>
                                    </div>
                                    <i class="fa fa-chevron-right text-muted" style="font-size:9px;"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right Column: Assigned Examinations Detail Panel --}}
                    <div class="exams-detail-panel">
                        <div class="exams-panel-header">
                            <h6 class="exams-header-title">
                                <i class="fa fa-folder-open text-warning"></i>
                                <span>Assigned Examinations: <strong id="activeClassNameHeader" class="text-primary">{{ $selectedClassName !== 'Class Not Selected' ? $selectedClassName : ($classType->first()->name ?? 'Selected Class') }}</strong></span>
                            </h6>
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <input type="text" id="examSearchInput" class="classes-search-input" placeholder="Filter exams..." style="width:140px; height:24px;">
                                <span class="badge-info-students" id="headerStudentsCount"><i class="fa fa-users text-primary mr-1"></i> Active Students</span>
                                <span class="badge-info-subjects" id="headerSubjectsCount"><i class="fa fa-book text-success mr-1"></i> Subjects Configured</span>
                            </div>
                        </div>

                        <div class="exams-panel-body" id="examsContainer">
                            <div class="p-4 text-center text-muted" id="examsLoadingSpinner" style="display:none;">
                                <div class="spinner-border text-primary spinner-border-sm mr-1"></div>
                                <span>Loading examinations for selected class...</span>
                            </div>

                            <div id="examsTableWrapper">
                                {{-- Rendered dynamically via JS --}}
                            </div>
                        </div>

                        {{-- Footer for Selector Hub --}}
                        <div class="dash-card-footer">
                            <span class="text-muted" style="font-size:11px;">
                                <i class="fa fa-info-circle text-sky mr-1"></i> Click on any examination above to open manual marks recording matrix.
                            </span>
                            <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline">
                                <i class="fa fa-plus-circle mr-1"></i> Create New Exam
                            </a>
                        </div>
                    </div>

                </div>

            @endif

        </div>

    </div>
</div>

{{-- Centered Confirmation Modal --}}
<div class="modal fade" id="fillMarksConfirmModal" tabindex="-1" role="dialog" aria-labelledby="fillMarksConfirmModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius:3px; overflow:hidden; border:none; box-shadow:0 10px 25px rgba(0,0,0,0.25);">
         <div class="modal-header" style="background:#002C54; color:#ffffff; padding:10px 14px;">
            <h5 class="modal-title font-weight-bold" id="fillMarksConfirmModalLabel" style="font-size:13px;">
                <i class="fa fa-check-circle mr-1 text-success"></i> Confirm Marks Submission
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.85;">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body" style="font-size:12px; color:#334155; padding:14px;">
            <p class="mb-2 font-weight-bold">Please verify the following before submitting:</p>
            <ul class="mb-0 pl-3" style="line-height: 1.6;">
               <li>Subject Maximum &amp; Passing Minimum marks are properly defined.</li>
               <li>Student scores and status codes (<code>AB</code>, <code>M</code>, <code>JL</code>, <code>T</code>) are verified.</li>
               <li>Unconducted subjects can remain <code>0</code> or blank.</li>
               <li>Existing student marks will be updated accurately.</li>
            </ul>
         </div>
         <div class="modal-footer bg-light" style="padding:6px 14px;">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius:2px; font-weight:600; font-size:11.5px;">Cancel</button>
            <button type="button" class="btn btn-success btn-sm px-3" id="confirmFillMarksSubmit" style="border-radius:2px; font-weight:700; font-size:11.5px; background:#10b981; border-color:#059669;">
                <i class="fa fa-save mr-1"></i> Confirm &amp; Save
            </button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // 1. Quick search for students in marks matrix table
    $('#searchStudentInput').on('keyup input', function() {
        var term = $(this).val().toLowerCase().trim();
        var visibleCount = 0;

        $('.student-mark-row').each(function() {
            var name = $(this).data('name') || '';
            var adm = $(this).data('adm') || '';
            var father = $(this).data('father') || '';

            if (name.indexOf(term) !== -1 || adm.indexOf(term) !== -1 || father.indexOf(term) !== -1) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#visibleStudentCount').text(visibleCount);
    });

    // 2. Synchronize Max & Min pass marks from setup modal to column headers
    $('.maximum_marks_input').on('input change', function() {
        var subId = $(this).data('subject-id');
        var maxVal = $(this).val();
        var minVal = $('#minimum_marks_' + subId).val();
        $('#header_meta_' + subId).text('(Max: ' + maxVal + ' | Pass: ' + minVal + ')');
    });

    $('.minimum_marks_input').on('input change', function() {
        var subId = $(this).data('subject-id');
        var minVal = $(this).val();
        var maxVal = $('#subject_' + subId).val();
        $('#header_meta_' + subId).text('(Max: ' + maxVal + ' | Pass: ' + minVal + ')');
    });

    // 3. Real-time marks validation & auto-fail highlighting
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
            if (typeof toastr !== 'undefined') {
                toastr.error('Invalid mark format. Enter numeric score or AB/M/JL/T/F.');
            }
            $(this).val('');
            return;
        }

        if (num > maximum_marks) {
            if (typeof toastr !== 'undefined') {
                toastr.error('Marks cannot exceed Maximum allowed (' + maximum_marks + ').');
            }
            $(this).val('');
            return;
        }

        if (num < minimum_marks) {
            $(this).addClass('bg-danger');
        }
    });

    // 4. Excel-style Arrow key & Enter key navigation for high-speed mark entry
    $('.marks-cell-input').on('keydown', function(e) {
        if (e.which === 40 || e.which === 13) { // Down Arrow or Enter
            e.preventDefault();
            var $cell = $(this).closest('td');
            var colIndex = $cell.index();
            var $nextRow = $(this).closest('tr').next('.student-mark-row:visible');
            if ($nextRow.length) {
                $nextRow.children().eq(colIndex).find('input.marks-cell-input:not([readonly])').focus().select();
            }
        } else if (e.which === 38) { // Up Arrow
            e.preventDefault();
            var $cell = $(this).closest('td');
            var colIndex = $cell.index();
            var $prevRow = $(this).closest('tr').prev('.student-mark-row:visible');
            if ($prevRow.length) {
                $prevRow.children().eq(colIndex).find('input.marks-cell-input:not([readonly])').focus().select();
            }
        }
    });

    // 5. Confirmation Submit Handler with spinner
    var fillMarksSubmitting = false;
    $('#confirmFillMarksSubmit').on('click', function () {
        if (fillMarksSubmitting) return;
        fillMarksSubmitting = true;
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
        $('#fillMarksConfirmModal').find('button').not($button).prop('disabled', true);
        document.getElementById('fillMarksForm').submit();
    });

    // =========================================================================
    // 6. Master-Detail Class & Exam Navigator (Scenario 2)
    // =========================================================================
    @if(!$showMarksSection)
        var currentActiveNavClassId = {{ $defaultActiveClassId }};
        var currentClassExamsCache = [];

        function loadExamsForClass(classId, className) {
            currentActiveNavClassId = classId;
            $('#activeClassNameHeader').text(className);
            $('#examsLoadingSpinner').show();
            $('#examsTableWrapper').hide();

            $.ajax({
                url: "{{ url('marks-import/exams-by-class') }}/" + classId,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#examsLoadingSpinner').hide();
                    $('#examsTableWrapper').show();

                    if (res.status && res.exams) {
                        currentClassExamsCache = res.exams;
                        $('#headerStudentsCount').html('<i class="fa fa-users text-primary mr-1"></i> ' + (res.students_count || 0) + ' Students');
                        $('#headerSubjectsCount').html('<i class="fa fa-book text-success mr-1"></i> ' + (res.subject_count || 0) + ' Subjects');
                        renderExamsTable(currentClassExamsCache);
                    } else {
                        $('#examsTableWrapper').html('<div class="p-4 text-center text-muted">Unable to load examinations.</div>');
                    }
                },
                error: function() {
                    $('#examsLoadingSpinner').hide();
                    $('#examsTableWrapper').show().html('<div class="p-4 text-center text-danger"><i class="fa fa-exclamation-triangle mr-1"></i> Failed to load exams.</div>');
                }
            });
        }

        function renderExamsTable(exams) {
            if (!exams || exams.length === 0) {
                $('#examsTableWrapper').html(
                    '<div class="p-4 text-center text-muted bg-white">' +
                    '<i class="fa fa-folder-open-o text-muted mb-2" style="font-size:32px;"></i>' +
                    '<h6 class="font-weight-bold text-dark">No Examinations Assigned Yet</h6>' +
                    '<p class="text-muted mb-3" style="font-size:12px;">There are no examinations scheduled or assigned for this academic class.</p>' +
                    '<a href="{{ url("view/exam") }}" class="dash-btn dash-btn-light border"><i class="fa fa-plus mr-1"></i> Assign Exam to Class</a>' +
                    '</div>'
                );
                return;
            }

            var html = '<table class="dash-table">' +
                '<thead>' +
                '<tr class="header-titles-row">' +
                '<th style="width:40px;">#</th>' +
                '<th style="text-align:left; padding-left:10px;">Examination Name</th>' +
                '<th style="width:120px;">Term</th>' +
                '<th style="width:110px;">Exam Date</th>' +
                '<th style="width:90px;">Students</th>' +
                '<th style="width:90px;">Subjects</th>' +
                '<th style="width:230px; text-align:right; padding-right:10px;">Actions</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>';

            $.each(exams, function(index, item) {
                html += '<tr class="exam-table-row" data-exam-name="' + item.name.toLowerCase() + '">' +
                    '<td class="text-center font-weight-bold text-muted">' + (index + 1) + '</td>' +
                    '<td style="text-align:left; padding-left:10px;">' +
                    '<strong class="text-primary" style="font-size:12px;"><i class="fa fa-leanpub mr-1 text-muted"></i>' + item.name + '</strong>' +
                    '</td>' +
                    '<td class="text-center"><span class="badge badge-light border" style="font-size:10.5px;">' + item.term + '</span></td>' +
                    '<td class="text-center text-muted font-weight-bold" style="font-size:11px;">' + item.date + '</td>' +
                    '<td class="text-center font-weight-bold text-primary">' + item.student_count + '</td>' +
                    '<td class="text-center font-weight-bold text-success">' + item.subject_count + '</td>' +
                    '<td style="text-align:right; padding-right:10px;">' +
                    '<div class="d-flex justify-content-end align-items-center" style="gap:4px;">' +
                    '<a href="' + item.manual_url + '" class="dash-btn dash-btn-light border" style="background:#002C54; color:#ffffff !important; border-color:#001f3d;" title="Open Manual Marks Entry">' +
                    '<i class="fa fa-pencil-square-o text-warning"></i> Enter Marks' +
                    '</a>' +
                    '<a href="' + item.upload_url + '" class="dash-btn dash-btn-light border" title="Import via Excel">' +
                    '<i class="fa fa-file-excel-o text-success"></i> Excel' +
                    '</a>' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
            });

            html += '</tbody></table>';
            $('#examsTableWrapper').html(html);
        }

        // Search classes in sidebar
        $('#classSearchInput').on('keyup input', function() {
            var term = $(this).val().toLowerCase().trim();
            $('#classesList .class-nav-item').each(function() {
                var name = $(this).data('name') || '';
                $(this).toggle(name.indexOf(term) !== -1);
            });
        });

        // Search exams in right detail panel
        $('#examSearchInput').on('keyup input', function() {
            var term = $(this).val().toLowerCase().trim();
            $('.exam-table-row').each(function() {
                var name = $(this).data('exam-name') || '';
                $(this).toggle(name.indexOf(term) !== -1);
            });
        });

        // Class selection click handler
        $('#classesList').on('click', '.class-nav-item', function() {
            $('#classesList .class-nav-item').removeClass('active');
            $(this).addClass('active');

            var classId = $(this).data('id');
            var className = $(this).find('.class-nav-name span').text().trim();
            loadExamsForClass(classId, className);
        });

        // Initialize exams on page load for default class
        if (currentActiveNavClassId) {
            var defaultClassName = $('#classesList .class-nav-item.active .class-nav-name span').text().trim() || 'Selected Class';
            loadExamsForClass(currentActiveNavClassId, defaultClassName);
        }
    @endif

});

// CSV Export Helper
function downloadCSV() {
    $('.marks_visible').show();
    var table = document.getElementById("thead_data_table");
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
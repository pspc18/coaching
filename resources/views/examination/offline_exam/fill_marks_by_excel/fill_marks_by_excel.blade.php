@extends('layout.app')

@section('title', 'Fill Marks by Excel - Examination Management')

@php
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $examlist = $examlist ?? collect();
    $subjects = $subjects ?? collect();
    $classTypeId = (int) ($search['class_type_id'] ?? 0);
    $examId = (int) ($search['exam_id'] ?? 0);
    $selectedClassName = $classType->firstWhere('id', $classTypeId)->name ?? '';
    $selectedExamName = $examlist->firstWhere('exam_id', $examId)->exam_name ?? '';
    $isMappingMode = !empty($importToken) && !empty($mappingHeaders);
    $isUploadMode = !empty($classTypeId) && !empty($examId) && !$isMappingMode;
    $defaultActiveClassId = $classTypeId ?: ($classType->first()->id ?? 0);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - FILL MARKS BY EXCEL (ADMISSIONVIEW 1:1 THEME STANDARD)
   - Viewport fitting: height: calc(100vh - var(--header-height, 56px) - 16px)
   - Palette: Arise Dark Navy (#002C54 to #0f3460), Slate & Sky accents
   - Typography: 11.5px table text, 11px uppercase bold thead
   - Interactive Master-Detail Class & Exam Hub
   - Sticky table thead for spreadsheet mapping & exam lists
   - Pinned bottom bar (#002342)
   ========================================================================== */

.excel-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.excel-page * {
    box-sizing: border-box;
}
.excel-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    max-height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 10px;
    box-sizing: border-box;
}

/* 1. Compact Hero Banner (~38px) */
.excel-hero {
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
.excel-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.excel-title {
    font-size: 13.5px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.excel-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.excel-hero-actions {
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

/* 2. Compact Control & Workflow Strip (~34px) */
.excel-control-strip {
    background: #002342;
    border: 1px solid #001833;
    border-radius: 2px;
    padding: 4px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
    flex-shrink: 0;
}
.control-form {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin: 0;
}
.control-group {
    display: flex;
    align-items: center;
    gap: 4px;
}
.control-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #cbd5e1;
    margin: 0;
    white-space: nowrap;
}
.control-select {
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease;
    color-scheme: dark;
    min-width: 150px;
    max-width: 220px;
}
.control-select:focus {
    background: #031426 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8 !important;
}
.control-select option {
    background-color: #002C54 !important;
    color: #ffffff !important;
}

/* Workflow Step Pills */
.workflow-steps {
    display: flex;
    align-items: center;
    gap: 4px;
}
.step-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    background: rgba(255,255,255,0.08);
    color: #94a3b8;
    border: 1px solid rgba(255,255,255,0.12);
    white-space: nowrap;
}
.step-pill.step-active {
    background: #0284c7;
    color: #ffffff;
    border-color: #38bdf8;
    font-weight: 700;
}
.step-pill.step-done {
    background: rgba(16, 185, 129, 0.2);
    color: #a7f3d0;
    border-color: rgba(16, 185, 129, 0.4);
}

/* Flash Alerts */
.excel-alert {
    padding: 4px 10px;
    border-radius: 2px;
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

/* 3. Main Dark Navy Workspace Card (Full Viewport Utilization) */
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

/* ==========================================================================
   INTERACTIVE MASTER-DETAIL NAVIGATOR (Left Classes, Right Exams)
   ========================================================================== */
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
.classes-search-input {
    width: 100%;
    height: 26px;
    padding: 2px 8px 2px 26px;
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
.classes-search-icon {
    position: absolute;
    left: 16px;
    top: 13px;
    font-size: 11px;
    color: #94a3b8;
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
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Right Detail: Assigned Examinations Workspace */
.exams-detail-panel {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}
.exams-panel-header {
    padding: 6px 12px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.exams-header-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.exams-panel-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    position: relative;
}

/* Exam Table */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    margin-bottom: 0;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}
.dash-table thead th {
    background: #002C54 !important;
    color: #ffffff !important;
    padding: 6px 8px;
    height: 32px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 1px solid rgba(255,255,255,.14);
    white-space: nowrap;
    vertical-align: middle;
    box-sizing: border-box;
}
.dash-table tbody td {
    padding: 5px 8px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11.5px;
}
.dash-table tbody tr:nth-child(even) td {
    background-color: #f8fafc;
}
.dash-table tbody tr:hover td {
    background-color: #e6f0fa;
}

/* Subtle Action Buttons (Exact admissionView Standard) */
.table-actions {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
}
.table-btn {
    width: 23px;
    height: 23px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease;
    line-height: 1;
}
.badge-term {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    background: #ede9fe;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
}

/* Step 2: 2-Column Split Workspace (Upload Dropzone) */
.upload-split-workspace {
    display: flex;
    height: 100%;
    min-height: 380px;
}
.upload-panel-left {
    flex: 1.2;
    padding: 20px 24px;
    border-right: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.upload-panel-right {
    flex: 1;
    padding: 20px 24px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
@media (max-width: 900px) {
    .upload-split-workspace, .master-detail-workspace {
        flex-direction: column;
        height: auto;
    }
    .classes-sidebar {
        width: 100%;
        max-width: 100%;
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
        max-height: 160px;
    }
    .upload-panel-left {
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
    }
}

/* Interactive Dropzone */
.dropzone-box {
    border: 2px dashed #94a3b8;
    border-radius: 3px;
    background: #ffffff;
    padding: 25px 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s ease;
    margin-bottom: 14px;
    position: relative;
}
.dropzone-box:hover, .dropzone-box.drag-over {
    border-color: #0284c7;
    background: #f0f9ff;
}
.dropzone-icon {
    font-size: 40px;
    color: #059669;
    margin-bottom: 8px;
    display: inline-block;
}
.dropzone-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
}
.dropzone-desc {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 10px;
}
.dropzone-file-info {
    display: none;
    margin-top: 10px;
    padding: 8px 12px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    border-radius: 2px;
    color: #065f46;
    font-size: 11.5px;
    font-weight: 600;
    align-items: center;
    justify-content: space-between;
}

/* Template Card & Guidelines */
.template-highlight-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #10b981;
    border-radius: 2px;
    padding: 12px 14px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.template-box-title {
    font-size: 12px;
    font-weight: 700;
    color: #065f46;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.template-box-desc {
    font-size: 11px;
    color: #475569;
    line-height: 1.4;
    margin-bottom: 10px;
}
.guideline-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #002C54;
    border-radius: 2px;
    padding: 12px 14px;
}
.guideline-title {
    font-size: 12px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.guideline-list {
    margin: 0;
    padding-left: 18px;
    font-size: 11px;
    color: #334155;
    line-height: 1.5;
}
.guideline-list li {
    margin-bottom: 3px;
}

/* Step 3: Excel Mapping Table (admissionView 1:1 Standard) */
.mapping-candidate-bar {
    position: sticky;
    top: 0;
    z-index: 30;
    background: #f1f5f9;
    border-bottom: 2px solid #002C54;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.candidate-bar-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.excel-map-select {
    width: 100%;
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
    transition: all .15s ease;
}
.excel-map-select:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 1px #002C54;
}
.excel-map-select.has-matched {
    border-color: #86efac;
    background-color: #f0fdf4;
    color: #166534;
    font-weight: 600;
}
.excel-map-select.select-candidate {
    min-width: 240px;
    max-width: 320px;
    border-color: #0284c7;
    font-weight: 600;
}

/* 5. Pinned Bottom Footer (~34px) */
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
.dash-card-footer a {
    color: #93c5fd;
}
.dash-card-footer a:hover {
    color: #ffffff;
}
</style>
@endsection

@section('content')
<div class="content-wrapper excel-page">
    <div class="excel-page-layout">

        {{-- 1. Compact Hero Banner (~38px) --}}
        <div class="excel-hero">
            <div class="hero-left-content">
                <span class="excel-kicker"><i class="fa fa-graduation-cap mr-1"></i> Marks Automation &amp; Batch Processing</span>
                <h1 class="excel-title">
                    <i class="fa fa-file-excel-o text-success"></i> Fill Marks By Excel
                </h1>
                <p class="excel-subtitle">
                    Select target class &amp; examination, upload your marks spreadsheet, and map columns to update results.
                </p>
            </div>
            <div class="excel-hero-actions">
                <a href="{{ url('view/exam') }}" class="dash-btn dash-btn-outline" title="View Exam Records">
                    <i class="fa fa-leanpub"></i> 1. Exam Setup
                </a>
                <a href="{{ url('fill_marks') }}" class="dash-btn dash-btn-outline" title="Manual Marks Entry">
                    <i class="fa fa-pencil-square-o"></i> 3. Manual Marks
                </a>
                <a href="{{ url('exam_wise_report') }}" class="dash-btn dash-btn-outline" title="Exam Reports">
                    <i class="fa fa-bar-chart"></i> 4. Exam Reports
                </a>
                @if(!empty($classTypeId) && !empty($examId))
                    <a href="{{ route('marks.template.download', ['class_type_id' => $classTypeId, 'exam_id' => $examId]) }}" 
                       class="dash-btn dash-btn-light" 
                       title="Download pre-filled Excel template with student roster and subjects">
                        <i class="fa fa-download text-success"></i> Download Template (.xlsx)
                    </a>
                @endif
            </div>
        </div>

        {{-- Flash Alerts --}}
        @if(session('success') || session('message'))
            <div class="excel-alert alert-success">
                <div><i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}</div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="excel-alert alert-danger">
                <div><i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}</div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        {{-- 3. Main Dark Navy Workspace Card (Full Viewport Utilization) --}}
        <div class="admission-table-card">
            
            {{-- Main Card Header --}}
            <div class="dash-card-header">
                <h6 class="dash-card-title">
                    @if($isMappingMode)
                        <i class="fa fa-exchange text-warning"></i> Step 3: Map Spreadsheet Columns to Exam Subjects
                        <span class="badge-total-records">{{ $uploadedRowCount ?? 0 }} Data Rows Found</span>
                    @elseif($isUploadMode)
                        <i class="fa fa-cloud-upload text-info"></i> Step 2: Upload Spreadsheet for <strong>{{ $selectedClassName }} &bull; {{ $selectedExamName }}</strong>
                    @else
                        <i class="fa fa-th-list text-info"></i> Step 1: Select Academic Class &amp; Examination
                    @endif
                </h6>

                <div class="dash-card-tools d-flex align-items-center" style="gap: 6px;">
                    @if($isUploadMode)
                        <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline" style="height:23px; font-size:10.5px;">
                            <i class="fa fa-arrow-left mr-1"></i> Switch Class / Exam
                        </a>
                        <span class="badge-target-meta">
                            <i class="fa fa-users mr-1"></i> {{ $studentsCount ?? 0 }} Students
                        </span>
                        <span class="badge-target-meta">
                            <i class="fa fa-book mr-1"></i> {{ count($subjects) }} Subjects
                        </span>
                    @elseif(!$isMappingMode)
                        <span class="badge-total-records">
                            <i class="fa fa-graduation-cap mr-1"></i> {{ count($classType) }} Classes Available
                        </span>
                    @endif

                    {{-- Workflow Step Indicators in Header --}}
                    <div class="workflow-steps">
                        <span class="step-pill {{ $isUploadMode || $isMappingMode ? 'step-done' : 'step-active' }}">
                            <i class="fa {{ $isUploadMode || $isMappingMode ? 'fa-check' : 'fa-circle' }}"></i> 1. Select
                        </span>
                        <span class="step-pill {{ $isMappingMode ? 'step-done' : ($isUploadMode ? 'step-active' : 'step-disabled') }}">
                            <i class="fa {{ $isMappingMode ? 'fa-check' : 'fa-circle' }}"></i> 2. Upload
                        </span>
                        <span class="step-pill {{ $isMappingMode ? 'step-active' : 'step-disabled' }}">
                            <i class="fa {{ $isMappingMode ? 'fa-pencil' : 'fa-circle-o' }}"></i> 3. Map
                        </span>
                    </div>
                </div>
            </div>

            {{-- Main Card Scrollable Body --}}
            <div class="table-scroll-container">
                
                {{-- =========================================================================
                     SCENARIO 1: SELECTION HUB (Interactive Master-Detail Class & Exam Browser)
                     ========================================================================= --}}
                @if(!$isUploadMode && !$isMappingMode)
                    <div class="master-detail-workspace">
                        
                        {{-- Left Column: Classes Navigator --}}
                        <div class="classes-sidebar">
                            <div class="classes-sidebar-header">
                                <span><i class="fa fa-graduation-cap text-info mr-1"></i> Academic Classes</span>
                                <span class="badge badge-light" style="font-size:10px;">{{ count($classType) }}</span>
                            </div>
                            <div class="classes-search-box position-relative">
                                <i class="fa fa-search classes-search-icon"></i>
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
                                    <span>Assigned Examinations: <strong id="activeClassNameHeader" class="text-primary">{{ $selectedClassName ?: ($classType->first()->name ?? 'Selected Class') }}</strong></span>
                                </h6>
                                <div class="d-flex align-items-center" style="gap:6px;">
                                    <input type="text" id="examSearchInput" placeholder="Filter exams..." style="height:24px; font-size:10.5px; padding:2px 8px; border:1px solid #cbd5e1; border-radius:2px; width:140px;">
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
                        </div>

                    </div>

                {{-- =========================================================================
                     SCENARIO 2: UPLOAD WORKSPACE (Interactive Dropzone & Template Download)
                     ========================================================================= --}}
                @elseif($isUploadMode)
                    <div class="upload-split-workspace">
                        
                        {{-- Left Column: Interactive Upload Dropzone --}}
                        <div class="upload-panel-left">
                            <form method="post" action="{{ route('marks.mapping.prepare') }}" enctype="multipart/form-data" id="excelUploadForm">
                                @csrf
                                <input type="hidden" name="class_type_id" value="{{ $classTypeId }}">
                                <input type="hidden" name="exam_id" value="{{ $examId }}">

                                <div class="dropzone-box" id="dropzoneBox" onclick="$('#excel_file').trigger('click');">
                                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" style="display:none;" required>
                                    <i class="fa fa-cloud-upload dropzone-icon"></i>
                                    <div class="dropzone-title">Click to browse or drag &amp; drop file here</div>
                                    <div class="dropzone-desc">Supports Excel (.xlsx, .xls) and CSV files up to 10MB</div>
                                    
                                    <div class="dropzone-file-info" id="fileInfoBox">
                                        <span><i class="fa fa-file-excel-o mr-1"></i> <strong id="selectedFileName">file.xlsx</strong> (<span id="selectedFileSize">0 KB</span>)</span>
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ml-2" id="clearFileBtn" title="Remove file">&times;</button>
                                    </div>
                                </div>

                                <button type="submit" class="dash-btn dash-btn-light" id="uploadSubmitBtn" style="background:#002C54; color:#ffffff !important; width:100%; height:34px; justify-content:center; font-size:12px; font-weight:700; border-color:#001f3d;">
                                    <i class="fa fa-cogs mr-1"></i> Upload Spreadsheet &amp; Continue to Column Mapping
                                </button>
                            </form>
                        </div>

                        {{-- Right Column: Pre-filled Template & Guidelines --}}
                        <div class="upload-panel-right">
                            
                            {{-- Official Pre-filled Template Download --}}
                            <div class="template-highlight-box">
                                <div class="template-box-title">
                                    <i class="fa fa-download"></i> Official Pre-Filled Excel Template
                                </div>
                                <div class="template-box-desc">
                                    Download an Excel sheet pre-populated with all <strong>{{ $studentsCount ?? 'currently enrolled' }} students</strong> and configured subjects for <strong>{{ $selectedClassName }}</strong>. Just fill in marks and re-upload!
                                </div>
                                <a href="{{ route('marks.template.download', ['class_type_id' => $classTypeId, 'exam_id' => $examId]) }}" 
                                   class="dash-btn dash-btn-light" 
                                   style="background:#10b981; color:#ffffff !important; border-color:#059669; font-weight:700;">
                                    <i class="fa fa-file-excel-o mr-1"></i> Download {{ $selectedClassName }} Template (.xlsx)
                                </a>
                            </div>

                            {{-- Guidelines --}}
                            <div class="guideline-box">
                                <div class="guideline-title">
                                    <i class="fa fa-info-circle text-primary"></i> Fast Import Guidelines:
                                </div>
                                <ul class="guideline-list">
                                    <li><strong>Candidate ID:</strong> Ensure one column contains the student's Admission No (e.g. <code>101</code>, <code>ADM-001</code>).</li>
                                    <li><strong>Subject Columns:</strong> You can map separate columns for Max Marks, Min Marks, Right (R), Wrong (W), Left (L), or Total Marks.</li>
                                    <li><strong>Safe Sync:</strong> Existing marks will be updated non-destructively; unmapped or blank cells remain unchanged.</li>
                                    <li><strong>Header Row:</strong> First row should contain column titles for automatic column detection.</li>
                                </ul>
                            </div>

                        </div>

                    </div>

                {{-- =========================================================================
                     SCENARIO 3: COLUMN MAPPING WORKSPACE (Sticky Thead 1:1 admissionView)
                     ========================================================================= --}}
                @else
                    <form method="post" action="{{ route('marks.mapping.save') }}" id="mappingSaveForm">
                        @csrf
                        <input type="hidden" name="import_token" value="{{ $importToken }}">

                        {{-- Top Sticky Candidate ID Selector Bar --}}
                        <div class="mapping-candidate-bar">
                            <div class="candidate-bar-label">
                                <i class="fa fa-id-badge text-primary" style="font-size:14px;"></i>
                                <span>Candidate ID / Admission No Column <span class="text-danger">*</span>:</span>
                                <select name="candidate_column" class="excel-map-select select-candidate" required>
                                    <option value="">-- Select Excel column for Student ID --</option>
                                    @foreach($mappingHeaders as $columnIndex => $header)
                                        <option value="{{ $columnIndex }}"
                                            {{ (string)($candidateColumn ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                            Column {{ $columnIndex + 1 }}: {{ $header }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex align-items-center" style="gap:6px;">
                                <span class="badge-total-records">
                                    <i class="fa fa-list mr-1"></i> {{ count($subjects) }} Subjects
                                </span>
                                <span class="badge-total-records">
                                    <i class="fa fa-table mr-1"></i> {{ count($mappingHeaders) }} Columns Found
                                </span>
                            </div>
                        </div>

                        {{-- High-Density Mapping Table (Sticky Thead, Matching admissionView) --}}
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="min-width: 180px; text-align: left; padding-left: 10px;">Subject Name</th>
                                    <th style="min-width: 140px;">Max Marks</th>
                                    <th style="min-width: 140px;">Min Marks</th>
                                    <th style="min-width: 120px;">Right (R)</th>
                                    <th style="min-width: 120px;">Wrong (W)</th>
                                    <th style="min-width: 120px;">Left (L)</th>
                                    <th class="th-marks-scored" style="min-width: 150px; background:#001f3f !important; color:#38bdf8 !important;">Marks Scored</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $index => $subject)
                                    <tr>
                                        {{-- S.No --}}
                                        <td class="text-center font-weight-bold text-muted">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- Subject Name --}}
                                        <td>
                                            <div class="map-subject-name" style="font-weight:700; color:#002C54; display:flex; align-items:center; gap:6px;">
                                                <i class="fa fa-book text-muted"></i>
                                                <span>{{ $subject->sub_name ?: $subject->name }}</span>
                                            </div>
                                        </td>

                                        {{-- Columns Mapping --}}
                                        @foreach(['maximum' => 'Max', 'minimum' => 'Min', 'r' => 'R', 'w' => 'W', 'l' => 'L', 'marks' => 'Marks'] as $type => $label)
                                            @php
                                                $selectedCol = $mappingDefaults[$subject->id][$type] ?? null;
                                                $hasMatch = $selectedCol !== null && $selectedCol !== '';
                                            @endphp
                                            <td style="{{ $type === 'marks' ? 'background: rgba(2,132,199,0.03);' : '' }}">
                                                <select name="mapping[{{ $subject->id }}][{{ $type }}]" 
                                                        class="excel-map-select {{ $hasMatch ? 'has-matched' : '' }}">
                                                    <option value="">-- None --</option>
                                                    @foreach($mappingHeaders as $columnIndex => $header)
                                                        <option value="{{ $columnIndex }}"
                                                            {{ (string)$selectedCol === (string)$columnIndex ? 'selected' : '' }}>
                                                            Col {{ $columnIndex + 1 }}: {{ $header }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </form>
                @endif

            </div>

            {{-- Main Card Pinned Footer --}}
            <div class="dash-card-footer">
                <div>
                    @if($isMappingMode)
                        <span style="font-size: 11px; color: #cbd5e1;">
                            <i class="fa fa-lightbulb-o text-warning mr-1"></i> Verify subject mappings above. Green indicates automatically matched columns.
                        </span>
                    @elseif($isUploadMode)
                        <span style="font-size: 11px; color: #cbd5e1;">
                            <i class="fa fa-info-circle text-info mr-1"></i> Need to edit marks manually instead? <a href="{{ url('fill_marks?class_type_id='.$classTypeId.'&exam_id='.$examId) }}">Switch to Manual Marks Entry &rarr;</a>
                        </span>
                    @else
                        <span style="font-size: 11px; color: #cbd5e1;">
                            <i class="fa fa-lightbulb-o text-warning mr-1"></i> Click any Class on the left to instantly filter and view its examinations on the right.
                        </span>
                    @endif
                </div>

                <div class="d-flex align-items-center" style="gap: 6px;">
                    @if($isMappingMode)
                        <a href="{{ url('fill-marks-by-excel?class_type_id='.$classTypeId.'&exam_id='.$examId) }}" 
                           class="dash-btn dash-btn-outline" 
                           title="Discard upload and pick another file">
                            <i class="fa fa-times mr-1"></i> Cancel &amp; Re-upload
                        </a>
                        <button type="button" 
                                onclick="$('#mappingSaveForm').submit();" 
                                class="dash-btn dash-btn-light" 
                                style="background:#10b981; color:#ffffff !important; border-color:#059669; font-weight:700;">
                            <i class="fa fa-check-circle mr-1"></i> Save &amp; Import Mapped Marks
                        </button>
                    @elseif($isUploadMode)
                        <a href="{{ url('fill-marks-by-excel') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-arrow-left mr-1"></i> Switch Class
                        </a>
                        <a href="{{ url('fill_marks?class_type_id='.$classTypeId.'&exam_id='.$examId) }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-pencil-square-o mr-1"></i> Manual Marks
                        </a>
                        <a href="{{ url('exam_wise_report?class_type_id='.$classTypeId.'&exam_id='.$examId) }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-bar-chart mr-1"></i> Exam Report
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

<script>
$(document).ready(function () {
    var defaultClassId = "{{ $defaultActiveClassId }}";

    // 1. Interactive Class Navigator Click Handler
    $('.class-nav-item').on('click', function () {
        var classId = $(this).data('id');
        var className = $(this).find('span').text().trim();

        $('.class-nav-item').removeClass('active');
        $('.class-nav-item i.fa-graduation-cap').removeClass('text-info').addClass('text-muted');
        $(this).addClass('active');
        $(this).find('i.fa-graduation-cap').removeClass('text-muted').addClass('text-info');

        // Load examinations for this class
        loadExamsForClass(classId, className);
    });

    // 2. Class Search Filter
    $('#classSearchInput').on('keyup', function () {
        var search = $(this).val().toLowerCase();
        $('#classesList .class-nav-item').each(function () {
            var name = $(this).data('name') || '';
            if (name.indexOf(search) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 3. Load Examinations via AJAX for a Class
    function loadExamsForClass(classId, className) {
        if (!classId) return;

        $('#activeClassNameHeader').text(className || 'Loading...');
        $('#examsLoadingSpinner').show();
        $('#examsTableWrapper').html('');

        $.ajax({
            url: "{{ url('marks-import/exams-by-class') }}/" + classId,
            type: "GET",
            dataType: "json",
            success: function (res) {
                $('#examsLoadingSpinner').hide();
                if (res.success) {
                    $('#activeClassNameHeader').text(res.class_name || className);
                    $('#headerStudentsCount').html('<i class="fa fa-users text-primary mr-1"></i> <strong style="color:#1e3a8a;">' + (res.student_count || 0) + '</strong> Students');
                    $('#headerSubjectsCount').html('<i class="fa fa-book text-success mr-1"></i> <strong style="color:#064e3b;">' + (res.subject_count || 0) + '</strong> Subjects');

                    renderExamsTable(res.exams, res.class_id, res.class_name);
                }
            },
            error: function () {
                $('#examsLoadingSpinner').hide();
                $('#examsTableWrapper').html('<div class="p-3 text-danger text-center"><i class="fa fa-exclamation-circle mr-1"></i> Unable to load examinations. Please try again.</div>');
            }
        });
    }

    // 4. Render Examinations High-Density ERP Table
    function renderExamsTable(exams, classId, className) {
        if (!exams || exams.length === 0) {
            var emptyHtml = '<div class="p-5 text-center text-muted">' +
                '<i class="fa fa-calendar-times-o mb-2" style="font-size:32px; color:#cbd5e1; display:block;"></i>' +
                '<h6 style="font-weight:700; color:#0f172a; font-size:13px;">No Examinations Assigned to ' + className + '</h6>' +
                '<p style="font-size:11px; color:#64748b; margin-bottom:12px;">This class has not been assigned to any exams yet.</p>' +
                '<a href="{{ url('view/exam') }}" class="dash-btn dash-btn-light" style="background:#002C54; color:#fff !important;">' +
                    '<i class="fa fa-plus-circle mr-1"></i> Assign Exams in Exam Setup' +
                '</a>' +
            '</div>';
            $('#examsTableWrapper').html(emptyHtml);
            return;
        }

        var html = '<table class="dash-table" id="renderedExamsTable">' +
            '<thead>' +
                '<tr>' +
                    '<th style="width:40px; text-align:center;">#</th>' +
                    '<th style="min-width:180px; text-align:left; padding-left:10px;">Examination Name</th>' +
                    '<th style="min-width:110px;">Term</th>' +
                    '<th style="min-width:110px;">Schedule Date</th>' +
                    '<th style="min-width:120px; text-align:center;">Actions</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>';

        $.each(exams, function (index, exam) {
            html += '<tr class="exam-table-row" data-name="' + (exam.name || '').toLowerCase() + '">' +
                '<td class="text-center font-weight-bold text-muted">' + (index + 1) + '</td>' +
                '<td>' +
                    '<div style="font-size:12px; font-weight:700; color:#002C54;">' +
                        '<a href="' + exam.upload_url + '" title="Click to Upload Spreadsheet for this Exam" style="color:#002C54; text-decoration:none;">' +
                            '<i class="fa fa-file-excel-o text-success mr-1"></i> ' + exam.name +
                        '</a>' +
                    '</div>' +
                '</td>' +
                '<td><span class="badge-term">' + exam.term + '</span></td>' +
                '<td><span style="font-size:11px; color:#475569; font-weight:500;"><i class="fa fa-calendar-check-o text-muted mr-1"></i> ' + exam.date + '</span></td>' +
                '<td class="text-center">' +
                    '<div class="table-actions">' +
                        '<a href="' + exam.upload_url + '" class="dash-btn dash-btn-light" style="background:#002C54; color:#ffffff !important; height:23px; padding:1px 7px; font-size:10px; font-weight:700;" title="Upload Excel for this exam">' +
                            '<i class="fa fa-cloud-upload text-info mr-1"></i> Upload Excel' +
                        '</a>' +
                        '<a href="' + exam.template_url + '" class="table-btn" style="background:#ecfdf5; border-color:#a7f3d0; color:#059669;" title="Download Pre-filled Template (.xlsx)">' +
                            '<i class="fa fa-file-excel-o"></i>' +
                        '</a>' +
                        '<a href="' + exam.manual_url + '" class="table-btn" style="background:#f0fdfa; border-color:#99f6e4; color:#0f766e;" title="Manual Marks Entry">' +
                            '<i class="fa fa-pencil"></i>' +
                        '</a>' +
                    '</div>' +
                '</td>' +
            '</tr>';
        });

        html += '</tbody></table>';
        $('#examsTableWrapper').html(html);
    }

    // 5. Exam Search Filter in Table
    $('#examSearchInput').on('keyup', function () {
        var search = $(this).val().toLowerCase();
        $('#examsTableWrapper .exam-table-row').each(function () {
            var name = $(this).data('name') || '';
            if (name.indexOf(search) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // 6. Initial Load if on Selection Hub
    if ($('#classesList').length && defaultClassId) {
        var $activeItem = $('.class-nav-item[data-id="' + defaultClassId + '"]');
        var className = $activeItem.length ? $activeItem.find('span').text().trim() : '';
        loadExamsForClass(defaultClassId, className);
    }

    // Drag & Drop File Upload Handling (Upload Mode)
    var $dropzone = $('#dropzoneBox');
    var $fileInput = $('#excel_file');
    var $fileInfoBox = $('#fileInfoBox');
    var $fileName = $('#selectedFileName');
    var $fileSize = $('#selectedFileSize');

    if ($dropzone.length) {
        $dropzone.on('dragover dragenter', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $dropzone.addClass('drag-over');
        });

        $dropzone.on('dragleave dragend drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            $dropzone.removeClass('drag-over');
        });

        $dropzone.on('drop', function (e) {
            var files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
                $fileInput[0].files = files;
                updateFileDisplay(files[0]);
            }
        });

        $fileInput.on('change', function () {
            if (this.files && this.files.length > 0) {
                updateFileDisplay(this.files[0]);
            }
        });

        $('#clearFileBtn').on('click', function (e) {
            e.stopPropagation();
            $fileInput.val('');
            $fileInfoBox.hide();
        });

        function updateFileDisplay(file) {
            $fileName.text(file.name);
            var sizeKb = (file.size / 1024).toFixed(1);
            $fileSize.text(sizeKb + ' KB');
            $fileInfoBox.css('display', 'inline-flex');
        }
    }

    // Mapping select change highlighting
    $('.excel-map-select').on('change', function () {
        if ($(this).val()) {
            $(this).addClass('has-matched');
        } else {
            $(this).removeClass('has-matched');
        }
    });
});
</script>
@endsection

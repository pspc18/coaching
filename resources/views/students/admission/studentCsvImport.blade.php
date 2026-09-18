@extends('layout.app')

@php
    $fieldsCount = is_array($getAdmissionDatatableFields) || $getAdmissionDatatableFields instanceof \Countable ? count($getAdmissionDatatableFields) : 0;
    $genderArr = array_filter(explode(',', $gender ?? ''));
    $bloodArr = array_filter(explode(',', $bloodgroupList ?? ''));
    $classArr = array_filter(explode(',', $class ?? ''));
@endphp

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - BULK STUDENT CSV/EXCEL IMPORT THEME
   - Signature Dark Navy Gradient Hero (#002C54 -> #0f3460)
   - 2-Column Full Screen Workspace Layout
   - Modern Drag & Drop File Upload Dropzone
   - Visual 3-Step Import Wizard
   - Interactive Field Guidelines & Lookup Badges
   - Live Sample Template Generator with Excel Data Validations
   ========================================================================== */

:root {
    --header-height: 56px;
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
}

.import-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.import-page * {
    box-sizing: border-box;
}

/* Full Viewport Layout */
.import-page-layout {
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    padding: 6px 8px 12px;
    gap: 6px;
}

/* 1. Hero Header Banner */
.import-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.15);
    flex-shrink: 0;
}
.import-hero-text {
    display: flex;
    flex-direction: column;
}
.import-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.import-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.import-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Stat Chips */
.import-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.hero-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 7px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    font-size: 10.5px;
    color: #ffffff;
}
.hero-stat-chip b {
    font-weight: 700;
    font-size: 11.5px;
}
.hero-stat-chip.badge-emerald {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #d1fae5;
}
.hero-stat-chip.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}

.import-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 28px;
    line-height: 1;
}
.dash-btn-success {
    background: #16a34a;
    color: #ffffff !important;
    border-color: #15803d;
}
.dash-btn-success:hover {
    background: #15803d;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
}
.dash-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.35);
}
.dash-btn-outline:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* 2. Wizard Process Steps Banner */
.import-steps-bar {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    flex-shrink: 0;
}
.step-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.step-number {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
}
.step-content {
    display: flex;
    flex-direction: column;
}
.step-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}
.step-desc {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
}

/* 3. Main Split Workspace */
.import-workspace-grid {
    display: grid;
    grid-template-columns: 420px 1fr;
    gap: 6px;
    flex: 1;
    min-height: 0;
}

/* Left Column: Upload Dropzone Card */
.upload-panel-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.card-panel-header {
    background: #002342;
    color: #ffffff;
    padding: 6px 10px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.card-panel-body {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
}

/* Modern Drag & Drop Zone */
.dropzone-box {
    border: 2px dashed #94a3b8;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    background: #f8fafc;
    transition: all .2s ease;
    cursor: pointer;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.dropzone-box:hover, .dropzone-box.dragover {
    border-color: #0284c7;
    background: #f0f9ff;
}
.dropzone-icon {
    font-size: 38px;
    color: #0284c7;
    margin-bottom: 8px;
    transition: transform .2s ease;
}
.dropzone-box:hover .dropzone-icon {
    transform: translateY(-2px);
}
.dropzone-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.dropzone-subtitle {
    font-size: 10.5px;
    color: #64748b;
    margin-bottom: 8px;
}
.dropzone-browse-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    background: #002C54;
    color: #ffffff;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
}
.dropzone-browse-btn:hover {
    background: #08335c;
}
.hidden-file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

/* Selected File Preview Box */
.selected-file-card {
    display: none;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 3px;
    padding: 8px 10px;
    align-items: center;
    justify-content: space-between;
}
.selected-file-info {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
}
.selected-file-icon {
    font-size: 20px;
    color: #16a34a;
}
.selected-file-name {
    font-size: 11px;
    font-weight: 700;
    color: #166534;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
}
.selected-file-size {
    font-size: 10px;
    color: #15803d;
}
.btn-remove-file {
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    font-size: 14px;
    padding: 2px;
}

.upload-card-footer {
    padding: 10px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
.btn-submit-upload {
    width: 100%;
    height: 36px;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    border-radius: 2px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .15s ease;
}
.btn-submit-upload:hover:not(:disabled) {
    background: #15803d;
    border-color: #166534;
    box-shadow: 0 2px 6px rgba(21, 128, 61, 0.25);
}
.btn-submit-upload:disabled {
    opacity: .6;
    cursor: not-allowed;
}

/* Right Column: Guidelines & Field Mappings Card */
.guidelines-panel-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Tabs Header */
.guidelines-tabs-nav {
    background: #001f3d;
    padding: 2px 8px;
    display: flex;
    align-items: center;
    gap: 4px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}
.g-tab-btn {
    background: transparent;
    border: 1px solid transparent;
    color: #94a3b8;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 2px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .12s;
}
.g-tab-btn:hover {
    color: #ffffff;
    background: rgba(255,255,255,.06);
}
.g-tab-btn.is-active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}

.guidelines-tab-content {
    flex: 1;
    overflow-y: auto;
    padding: 10px 12px;
}

/* Rules List Grid */
.rules-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.rule-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #002C54;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 11px;
    color: #334155;
    line-height: 1.4;
}
.rule-item.warning {
    border-left-color: #f59e0b;
    background: #fffbeb;
}
.rule-item.important {
    border-left-color: #ef4444;
    background: #fef2f2;
}
.rule-icon {
    font-size: 13px;
    margin-top: 1px;
    flex-shrink: 0;
}

/* Lookup Pills Grid */
.lookup-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.lookup-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 8px 10px;
}
.lookup-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.lookup-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}
.lookup-chip {
    display: inline-block;
    padding: 1px 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    color: #0f172a;
}

/* Fields Reference Table */
.fields-table-scroll {
    max-height: 340px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
}
.fields-ref-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
}
.fields-ref-table th {
    background: #002C54;
    color: #ffffff;
    padding: 4px 8px;
    position: sticky;
    top: 0;
    z-index: 2;
    text-align: left;
    font-weight: 700;
}
.fields-ref-table td {
    padding: 3px 8px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
}
.fields-ref-table tr:nth-child(even) {
    background: #f8fafc;
}

/* Hidden elements for DataTable Excel generation */
#studentList_container {
    position: absolute;
    left: -9999px;
    top: -9999px;
    visibility: hidden;
    height: 0;
    overflow: hidden;
}

@media(max-width: 991px) {
    .import-workspace-grid {
        grid-template-columns: 1fr;
    }
    .import-steps-bar {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="content-wrapper import-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="import-page-layout">

                {{-- 1. Signature Hero Header Banner --}}
                <div class="import-hero">
                    <div class="import-hero-text">
                        <span class="import-kicker">Student Admissions &bull; Bulk Data Tool</span>
                        <h1 class="import-title">
                            <i class="fa fa-file-excel-o text-success"></i> Bulk Student Admission &amp; Excel Import
                        </h1>
                        <p class="import-subtitle">
                            Quickly enroll students in bulk using standardized Excel spreadsheets with built-in data validation and auto-mapping.
                        </p>
                    </div>

                    <div class="import-hero-stats">
                        <div class="hero-stat-chip badge-sky">
                            <i class="fa fa-columns"></i> Importable Fields: <b>{{ $fieldsCount }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald">
                            <i class="fa fa-check-circle"></i> Auto Data Validations Active
                        </div>
                    </div>

                    <div class="import-hero-actions">
                        <button type="button" class="dash-btn dash-btn-success" id="btnDownloadSampleExcel" title="Download Pre-formatted Sample Excel File">
                            <i class="fa fa-download"></i> Download Sample Excel
                        </button>
                        <a href="{{ url('admissionView') }}" class="dash-btn dash-btn-outline" title="View Enrolled Students List">
                            <i class="fa fa-list"></i> Student List
                        </a>
                        <a href="{{ url('studentsDashboard') }}" class="dash-btn dash-btn-outline" title="Back to Students Hub">
                            <i class="fa fa-arrow-left"></i> Admission Hub
                        </a>
                    </div>
                </div>

                {{-- 2. 3-Step Wizard Quick Guide Bar --}}
                <div class="import-steps-bar">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <span class="step-title">Download Sample File</span>
                            <span class="step-desc">Get the pre-formatted Excel template with active dropdown lists.</span>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <span class="step-title">Fill Student Details</span>
                            <span class="step-desc">Carefully populate admission numbers, names, and class details.</span>
                        </div>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <span class="step-title">Upload &amp; Process</span>
                            <span class="step-desc">Drop your completed sheet here to instantly enroll students.</span>
                        </div>
                    </div>
                </div>

                {{-- 3. Main Workspace Split (Upload Left + Guidelines Right) --}}
                <div class="import-workspace-grid">

                    {{-- Left Column: Drag & Drop Upload Panel --}}
                    <div class="upload-panel-card">
                        <div class="card-panel-header">
                            <span><i class="fa fa-cloud-upload mr-1"></i> Upload Spreadsheet</span>
                            <span style="font-size: 10px; font-weight: normal; opacity: .85;">Supported: .xlsx, .xls, .csv</span>
                        </div>

                        <form action="{{ url('studentExcelAdd') }}" method="POST" enctype="multipart/form-data" id="studentExcelForm" style="display: flex; flex-direction: column; flex: 1;">
                            @csrf
                            <div class="card-panel-body">
                                
                                {{-- Drag & Drop Zone --}}
                                <div class="dropzone-box" id="dropzoneBox">
                                    <input type="file" id="excelFileInput" name="excel" class="hidden-file-input" accept=".xlsx, .xls, .csv" required>
                                    <div class="dropzone-icon">
                                        <i class="fa fa-file-excel-o"></i>
                                    </div>
                                    <div class="dropzone-title">Drag &amp; Drop Excel File Here</div>
                                    <div class="dropzone-subtitle">or click anywhere to browse from your computer</div>
                                    <div class="dropzone-browse-btn">
                                        <i class="fa fa-folder-open-o"></i> Choose File
                                    </div>
                                </div>

                                {{-- Selected File Preview --}}
                                <div class="selected-file-card" id="selectedFileCard">
                                    <div class="selected-file-info">
                                        <i class="fa fa-file-excel-o selected-file-icon"></i>
                                        <div>
                                            <div class="selected-file-name" id="selectedFileName">student_data.xlsx</div>
                                            <div class="selected-file-size" id="selectedFileSize">0 KB</div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-remove-file" id="btnRemoveFile" title="Remove File">
                                        <i class="fa fa-times-circle"></i>
                                    </button>
                                </div>

                                {{-- Quick Info Notice --}}
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 2px; padding: 8px 10px; font-size: 10.5px; color: #64748b;">
                                    <div style="font-weight: 700; color: #002C54; margin-bottom: 2px;">
                                        <i class="fa fa-info-circle text-primary mr-1"></i> Important Before Uploading:
                                    </div>
                                    Ensure that column headers in row 2 of your file are intact and not modified from the sample template.
                                </div>

                            </div>

                            <div class="upload-card-footer">
                                <button type="submit" class="btn-submit-upload" id="btnSubmitUpload" disabled>
                                    <i class="fa fa-upload"></i> Upload &amp; Enroll Students
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Right Column: Interactive Guidelines & Supported Field Mappings --}}
                    <div class="guidelines-panel-card">
                        
                        {{-- Tabs Navigation --}}
                        <div class="guidelines-tabs-nav">
                            <button type="button" class="g-tab-btn is-active" data-target="#tabRules">
                                <i class="fa fa-check-square-o"></i> Formatting Rules
                            </button>
                            <button type="button" class="g-tab-btn" data-target="#tabLookups">
                                <i class="fa fa-list-ul"></i> Valid Lookup Values
                            </button>
                            <button type="button" class="g-tab-btn" data-target="#tabFields">
                                <i class="fa fa-table"></i> All Importable Fields ({{ $fieldsCount }})
                            </button>
                        </div>

                        {{-- Tab 1: Formatting Rules --}}
                        <div class="guidelines-tab-content" id="tabRules">
                            <div class="rules-list">
                                <div class="rule-item important">
                                    <i class="fa fa-exclamation-triangle text-danger rule-icon"></i>
                                    <div>
                                        <b>Duplicate Protection:</b> <code>Admission No</code> and <code>Roll Number</code> must be unique for each student. Duplicate admission numbers in the file or existing in database will be flagged.
                                    </div>
                                </div>
                                <div class="rule-item warning">
                                    <i class="fa fa-calendar text-warning rule-icon"></i>
                                    <div>
                                        <b>Date Formats:</b> Enter all dates (e.g. <code>Birthday</code>, <code>AdmissionDate</code>) in strict standard format: <code>YYYY-MM-DD</code> (e.g., <b>2026-05-02</b>).
                                    </div>
                                </div>
                                <div class="rule-item">
                                    <i class="fa fa-graduation-cap text-primary rule-icon"></i>
                                    <div>
                                        <b>Class Mapping:</b> Enter the exact class name as registered on the <a href="{{ url('add_class') }}" target="_blank" class="font-weight-bold" style="color: #0284c7;">Class Management</a> page (e.g. <code>1st</code>, <code>10th</code>, <code>12th-A</code>).
                                    </div>
                                </div>
                                <div class="rule-item">
                                    <i class="fa fa-key text-info rule-icon"></i>
                                    <div>
                                        <b>User Credentials:</b> Leave the <code>Username</code> and <code>Password</code> columns empty if you want Arise ERP to auto-generate login accounts for enrolled students.
                                    </div>
                                </div>
                                <div class="rule-item">
                                    <i class="fa fa-shield text-success rule-icon"></i>
                                    <div>
                                        <b>Header Row Position:</b> The system reads column headers from <b>Row 2</b> and student records start from <b>Row 3</b>. Do not delete the top sample instruction rows.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Valid Lookup Values --}}
                        <div class="guidelines-tab-content" id="tabLookups" style="display: none;">
                            <div class="lookup-grid">
                                
                                {{-- Gender --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-venus-mars mr-1"></i> Gender</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">Male</span>
                                        <span class="lookup-chip">Female</span>
                                        <span class="lookup-chip">Other</span>
                                    </div>
                                </div>

                                {{-- Admission Type --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-tag mr-1"></i> Admission Type</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">Non RTE (Yes)</span>
                                        <span class="lookup-chip">RTE (No)</span>
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-id-badge mr-1"></i> Category</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">GEN</span>
                                        <span class="lookup-chip">OBC</span>
                                        <span class="lookup-chip">SC</span>
                                        <span class="lookup-chip">ST</span>
                                        <span class="lookup-chip">BC</span>
                                        <span class="lookup-chip">SBC</span>
                                        <span class="lookup-chip">Other</span>
                                    </div>
                                </div>

                                {{-- Religion --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-sun-o mr-1"></i> Religion</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">Hindu</span>
                                        <span class="lookup-chip">Islam</span>
                                        <span class="lookup-chip">Sikh</span>
                                        <span class="lookup-chip">Buddhism</span>
                                        <span class="lookup-chip">Jain</span>
                                        <span class="lookup-chip">Christianity</span>
                                        <span class="lookup-chip">Adivasi</span>
                                        <span class="lookup-chip">Other</span>
                                    </div>
                                </div>

                                {{-- Blood Group --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-tint mr-1 text-danger"></i> Blood Group</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">A+</span>
                                        <span class="lookup-chip">A-</span>
                                        <span class="lookup-chip">B+</span>
                                        <span class="lookup-chip">B-</span>
                                        <span class="lookup-chip">O+</span>
                                        <span class="lookup-chip">O-</span>
                                        <span class="lookup-chip">AB+</span>
                                        <span class="lookup-chip">AB-</span>
                                    </div>
                                </div>

                                {{-- Medium --}}
                                <div class="lookup-box">
                                    <div class="lookup-title"><span><i class="fa fa-language mr-1"></i> Medium</span></div>
                                    <div class="lookup-chips-wrap">
                                        <span class="lookup-chip">Hindi</span>
                                        <span class="lookup-chip">English</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Tab 3: All Importable Fields Reference --}}
                        <div class="guidelines-tab-content" id="tabFields" style="display: none;">
                            <div class="fields-table-scroll">
                                <table class="fields-ref-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">#</th>
                                            <th>Column Header Name</th>
                                            <th>Required / Optional</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($getAdmissionDatatableFields)
                                            @php $fIdx = 1; @endphp
                                            @foreach($getAdmissionDatatableFields as $key => $val)
                                                <tr>
                                                    <td style="text-align: center; color: #64748b;">{{ $fIdx++ }}</td>
                                                    <td style="font-weight: 700; color: #002C54;">{{ $key }}</td>
                                                    <td>
                                                        @if(in_array($key, ['First Name', 'Class', 'Admission No', 'Gender', 'Father Name']))
                                                            <span class="badge badge-danger font-weight-bold" style="font-size: 9px;">Recommended</span>
                                                        @else
                                                            <span class="badge badge-light border text-muted" style="font-size: 9px;">Optional</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>
</div>

{{-- Hidden DataTable for Programmatic Excel Sample Generation --}}
<div id="studentList_container">
    <table id="studentList" class="table table-bordered table-striped dataTable dtr-inline nowrap">
        <thead>
            <tr role="row">
                @if($getAdmissionDatatableFields)
                    @foreach($getAdmissionDatatableFields as $key => $val)
                        <th class="text-center">{{ $key }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody id="product_list_show"></tbody>
    </table>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // 1. Initialize Sample Excel Generator via Hidden DataTable Buttons
    var dataTableInstance = $("#studentList").DataTable({
        "bPaginate": false,
        "bAutoWidth": false,
        "bInfo": false,
        "lengthChange": false,
        "searching": false,
        "buttons": [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-arrow-down"></i> Download Sample Excel',
            filename: 'Student_Upload_Format_' + (new Date().toISOString().slice(0, 10)),
            className: 'btn-export-trigger',
            exportOptions: {
                modifier: { page: 'current' },
                format: {
                    body: function (data, row) {
                        return row === 0 ? '' : data;
                    }
                }
            },
            customize: function (xlsx) {
                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                const styles = xlsx.xl['styles.xml'];
                const fills = styles.getElementsByTagName('fills')[0];
                const fonts = styles.getElementsByTagName('fonts')[0];
                const borders = styles.getElementsByTagName('borders')[0];

                const fillIndex = fills.childNodes.length;
                fills.appendChild($.parseXML('<fill><patternFill patternType="solid"><fgColor rgb="002C54"/></patternFill></fill>').documentElement);

                const fontIndex = fonts.childNodes.length;
                fonts.appendChild($.parseXML('<font><sz val="12"/><color rgb="ffffff"/><b/></font>').documentElement);

                const borderIndex = borders.childNodes.length;
                borders.appendChild($.parseXML('<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>').documentElement);

                const cellXfs = styles.getElementsByTagName('cellXfs')[0];
                const xfIndex = cellXfs.childNodes.length;
                cellXfs.appendChild($.parseXML(
                    `<xf applyFill="1" applyFont="1" applyBorder="1" fontId="${fontIndex}" fillId="${fillIndex}" borderId="${borderIndex}">
                        <alignment vertical="center" horizontal="center"/>
                    </xf>`
                ).documentElement);

                // Style header row
                const headerCells = sheet.querySelectorAll('row:first-of-type c');
                headerCells.forEach(cell => cell.setAttribute('s', xfIndex));

                function indexToColumnName(index) {
                    let name = '';
                    while (index >= 0) {
                        name = String.fromCharCode((index % 26) + 65) + name;
                        index = Math.floor(index / 26) - 1;
                    }
                    return name;
                }

                const tableHeaders = $('#studentList thead th');
                const dataCells = sheet.querySelectorAll('row:not(:first-of-type) c');
                const numberOfRows = Math.max(50, dataCells.length);
                let dataValidations = sheet.getElementsByTagName('dataValidations')[0];
                if (!dataValidations) {
                    dataValidations = sheet.createElement('dataValidations');
                    sheet.getElementsByTagName('worksheet')[0].appendChild(dataValidations);
                }

                const dropdownMap = {
                    'Class': '{{ $class }}',
                    'Gender': '{{ $gender }}',
                    'State': '{{ $stateList }}',
                    'City': '{{ $cityList }}',
                    'Blood Group': '{{ $bloodgroupList }}',
                    'Admission Type': 'Non RTE,RTE',
                    'Income Tax Payee Father': 'Yes,No',
                    'Income Tax Payee Mother': 'Yes,No',
                    'BPL': 'Yes,No',
                    'Religion': 'HINDU,ISLAM,SIKH,BUDDHISM,ADIVASI,JAIN,CHRISTIANITY,OTHER',
                    'Category': 'OBC,SC,ST,BC,GEN,SBC,Other',
                    'Transport': 'Yes,No',
                    'Village': '{{ $villageList }}'
                };

                tableHeaders.each(function(index, th) {
                    const headerText = $(th).text().trim();
                    if (dropdownMap[headerText]) {
                        const colLetter = indexToColumnName(index);
                        for (let rowIndex = 3; rowIndex <= numberOfRows; rowIndex++) {
                            const cellRef = colLetter + rowIndex;
                            const dv = sheet.createElement('dataValidation');
                            dv.setAttribute('type', 'list');
                            dv.setAttribute('allowBlank', '1');
                            dv.setAttribute('showInputMessage', '1');
                            dv.setAttribute('showErrorMessage', '1');
                            dv.setAttribute('sqref', cellRef);
                            const formula1 = sheet.createElement('formula1');
                            formula1.textContent = `"${dropdownMap[headerText]}"`;
                            dv.appendChild(formula1);
                            dataValidations.appendChild(dv);
                        }
                    }
                });

                dataValidations.setAttribute('count', dataValidations.childNodes.length);
            }
        }]
    });

    // Wire Hero "Download Sample Excel" Button directly to DataTable export
    $('#btnDownloadSampleExcel').on('click', function() {
        $('.btn-export-trigger').trigger('click');
    });

    // 2. Drag & Drop File Handling
    var dropzone = $('#dropzoneBox');
    var fileInput = $('#excelFileInput');
    var selectedFileCard = $('#selectedFileCard');
    var selectedFileName = $('#selectedFileName');
    var selectedFileSize = $('#selectedFileSize');
    var btnSubmitUpload = $('#btnSubmitUpload');

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function handleFileSelected(file) {
        if (!file) return;
        selectedFileName.text(file.name);
        selectedFileSize.text(formatBytes(file.size));
        selectedFileCard.css('display', 'flex');
        btnSubmitUpload.prop('disabled', false);
    }

    fileInput.on('change', function() {
        if (this.files && this.files.length > 0) {
            handleFileSelected(this.files[0]);
        }
    });

    $('#btnRemoveFile').on('click', function(e) {
        e.stopPropagation();
        fileInput.val('');
        selectedFileCard.hide();
        btnSubmitUpload.prop('disabled', true);
    });

    dropzone.on('dragover dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.addClass('dragover');
    });
    dropzone.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.removeClass('dragover');
    });
    dropzone.on('drop', function(e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            fileInput[0].files = files;
            handleFileSelected(files[0]);
        }
    });

    // 3. Tab Switching in Guidelines Card
    $('.g-tab-btn').on('click', function() {
        var target = $(this).data('target');
        $('.g-tab-btn').removeClass('is-active');
        $(this).addClass('is-active');
        $('.guidelines-tab-content').hide();
        $(target).fadeIn(100);
    });
});
</script>
@endsection
@endsection
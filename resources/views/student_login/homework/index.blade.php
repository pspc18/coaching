@php
    $getUser = Helper::getUser();
    $className = optional($getUser)->ClassTypes->name ?? 'Class';
    $studentName = trim(($getUser->first_name ?? '') . ' ' . ($getUser->last_name ?? ''));
    $currentSessionName = Session::get('session_name') ?? date('Y');
    $today = date('Y-m-d');

    $totalCount = $metrics['total'] ?? 0;
    $submittedCount = $metrics['submitted'] ?? 0;
    $evaluatedCount = $metrics['evaluated'] ?? 0;
    $pendingCount = $metrics['pending'] ?? 0;
    $dueTodayCount = $metrics['due_today'] ?? 0;
    $overdueCount = $metrics['overdue'] ?? 0;
    $completionPct = $metrics['completion_pct'] ?? 0;
@endphp

@extends('student_login.layout.app')

@section('title', 'My Homework')
@section('page_title', 'HOMEWORK')
@section('page_sub', (!empty($studentName) ? $studentName : 'Student') . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT MOBILE HOMEWORK DIRECTORY
   Exact Layout, Typography & Palette matching Arise Native Mobile Design System
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* 4-KPI Metric Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 3px;
    text-align: center;
    transition: transform .15s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}
.mob-metric-val {
    font-size: 14.5px;
    font-weight: 800;
    line-height: 1.1;
    color: #ffffff;
}

/* Progress Track Bar */
.mob-progress-wrap {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-progress-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    color: #cbd5e1;
    font-weight: 600;
    margin-bottom: 4px;
}
.mob-progress-label b {
    color: #38bdf8;
    font-weight: 800;
}
.mob-progress-track {
    height: 5px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 3px;
    overflow: hidden;
}
.mob-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #06b6d4 0%, #10b981 100%);
    border-radius: 3px;
    transition: width 0.4s ease;
}

/* 2. Sticky Search & Filter Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 7px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 48px;
    z-index: 20;
}
.mob-search-input-box {
    position: relative;
    margin-bottom: 6px;
}
.mob-search-input-box i.search-ico {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 31px;
    padding: 4px 28px 4px 26px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: all .2s;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-search-clear {
    position: absolute;
    right: 7px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    cursor: pointer;
    display: none;
    padding: 2px 4px;
}

/* Horizontal Filter Chips */
.mob-chips-track {
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 1px;
}
.mob-chips-track::-webkit-scrollbar {
    display: none;
}
.mob-filter-chip {
    flex-shrink: 0;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-filter-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}
.mob-filter-chip .chip-cnt {
    font-size: 9px;
    padding: 0 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-filter-chip.active .chip-cnt {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* 3. Homework Cards Feed */
.mob-hw-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 4px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
    transition: box-shadow .2s;
}
.student-mob-card:active {
    background-color: #fafbfc;
}

/* Card Status Border Classes */
.student-mob-card.status-evaluated {
    border-left-color: #10b981;
}
.student-mob-card.status-submitted {
    border-left-color: #0284c7;
}
.student-mob-card.status-due_today {
    border-left-color: #f59e0b;
}
.student-mob-card.status-overdue {
    border-left-color: #f43f5e;
}
.student-mob-card.status-pending {
    border-left-color: #6366f1;
}

/* Card Header */
.mob-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-card-subject-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.mob-subject-pill {
    font-size: 10px;
    font-weight: 800;
    color: #002C54;
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-type-tag {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-status-pill {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.pill-evaluated {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.pill-submitted {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.pill-due_today {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.pill-overdue {
    background: #ffe4e6;
    color: #be123c;
    border: 1px solid #fecdd3;
}
.pill-pending {
    background: #e0e7ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}

/* Card Title & Content */
.mob-hw-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: 4px;
    word-break: break-word;
}
.mob-hw-desc-box {
    font-size: 11px;
    line-height: 1.45;
    color: #475569;
    margin-bottom: 7px;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 3px;
    padding: 6px 8px;
    word-break: break-word;
}
.mob-hw-desc-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.mob-read-more-btn {
    display: inline-block;
    color: #0284c7;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 3px;
    text-decoration: underline;
}

/* Meta Data Grid */
.mob-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 7px;
    margin-bottom: 7px;
    font-size: 10.5px;
}
.mob-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #475569;
}
.mob-meta-item i {
    color: #64748b;
    font-size: 10px;
    width: 12px;
    text-align: center;
    flex-shrink: 0;
}
.mob-meta-item b {
    color: #0f172a;
    font-weight: 700;
}

/* Teacher Attachment Block */
.mob-attachment-box {
    background: #eff6ff;
    border: 1px dashed #bfdbfe;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-attachment-info {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
}
.mob-attachment-info i {
    font-size: 14px;
    color: #0284c7;
    flex-shrink: 0;
}
.mob-attachment-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e3a8a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 160px;
}
.mob-attachment-subtext {
    font-size: 9px;
    color: #64748b;
}
.mob-attachment-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.btn-att-action {
    height: 25px;
    padding: 0 7px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    line-height: 1;
}
.btn-att-preview {
    background: #ffffff;
    border-color: #cbd5e1;
    color: #0284c7;
}
.btn-att-download {
    background: #0284c7;
    color: #ffffff;
}

/* Evaluation / Submission Result Strip */
.mob-eval-strip {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 7px;
}
.mob-eval-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3px;
}
.mob-eval-badge {
    font-size: 10px;
    font-weight: 800;
    color: #15803d;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-eval-marks {
    font-size: 12px;
    font-weight: 800;
    color: #15803d;
    background: #dcfce7;
    border: 1px solid #86efac;
    padding: 1px 6px;
    border-radius: 3px;
}
.mob-eval-remark {
    font-size: 10.5px;
    color: #166534;
    font-style: italic;
    line-height: 1.35;
}

/* Submitted Awaiting Strip */
.mob-submitted-strip {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-submitted-msg {
    font-size: 10.5px;
    color: #0369a1;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* Action Button Bar */
.mob-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 4px;
    border-top: 1px dashed #e2e8f0;
}
.mob-footer-left {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-footer-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.btn-mob-action {
    height: 29px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 800;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
    line-height: 1;
    white-space: nowrap;
    transition: all .15s ease;
}
.btn-mob-view {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #002C54;
}
.btn-mob-view:active {
    background: #e2e8f0;
}
.btn-mob-submit {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.btn-mob-submit:active {
    transform: scale(0.98);
}
.btn-mob-submit.urgent {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
    animation: pulse-soft 2s infinite;
}
@keyframes pulse-soft {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.mob-closed-badge {
    font-size: 10px;
    font-weight: 700;
    color: #be123c;
    background: #ffe4e6;
    border: 1px solid #fecdd3;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Empty State Card */
.mob-empty-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 36px 16px;
    text-align: center;
    margin: 8px 0;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    line-height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    font-size: 20px;
    margin: 0 auto 10px auto;
}
.mob-empty-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    max-width: 260px;
    margin: 0 auto 12px auto;
    line-height: 1.4;
}

/* ==========================================================================
   4. NATIVE MOBILE BOTTOM-SHEET MODAL SYSTEM
   Matches student-notices, admissionView?layout=mobile
   Guaranteed hidden until triggered by user click
   ========================================================================== */

/* Hide any bootstrap modal default display */
.modal:not(.show) {
    display: none !important;
}

/* Backdrop */
.mob-modal-backdrop {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .22s ease-in-out, visibility .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Slide-Up Sheet Container */
.mob-modal-sheet {
    display: flex;
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 3px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 88vh;
    flex-direction: column;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.4);
    visibility: hidden;
    pointer-events: none;
}
.mob-modal-sheet.show {
    bottom: 0;
    visibility: visible;
    pointer-events: auto;
}

/* Sheet Header */
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* FIXED POSITION TOP META STRIP (Pinned in sheet) */
.mob-sheet-fixed-meta {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    flex-shrink: 0;
    z-index: 2;
}
.sheet-fixed-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.sheet-meta-pill {
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* MOVEABLE / SCROLLABLE BODY */
.mob-sheet-body {
    padding: 12px 14px 16px 14px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

/* SHEET FOOTER */
.mob-sheet-footer {
    padding: 9px 14px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-shrink: 0;
}

/* Upload Dropzone */
.mob-dropzone {
    border: 2px dashed #93c5fd;
    background: #f8fafc;
    border-radius: 4px;
    padding: 14px 12px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: 8px;
}
.mob-dropzone:hover, .mob-dropzone.dragover {
    border-color: #0284c7;
    background: #eff6ff;
}
.mob-dropzone i {
    font-size: 24px;
    color: #0284c7;
    margin-bottom: 4px;
}
.mob-dropzone-text {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
}
.mob-dropzone-sub {
    font-size: 9.5px;
    color: #64748b;
}

/* File Previews List */
.mob-selected-files {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 10px;
}
.mob-file-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 5px 8px;
    font-size: 10.5px;
}
.mob-file-row-left {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
}
.mob-file-row-name {
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 190px;
}
.mob-file-row-size {
    font-size: 9px;
    color: #64748b;
}
.mob-file-remove {
    color: #ef4444;
    cursor: pointer;
    padding: 2px 4px;
    font-size: 12px;
}

/* Submitted Documents Gallery Grid */
.mob-docs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 8px;
}
.mob-doc-tile {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px;
    text-align: center;
}
.mob-doc-thumb {
    width: 100%;
    height: 85px;
    object-fit: cover;
    border-radius: 3px;
    margin-bottom: 5px;
    background: #e2e8f0;
}
.mob-doc-thumb-fallback {
    width: 100%;
    height: 85px;
    border-radius: 3px;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #64748b;
    margin-bottom: 5px;
}
.mob-doc-name {
    font-size: 9.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 4px;
}
.mob-doc-actions {
    display: flex;
    gap: 4px;
    justify-content: center;
}
</style>
@endsection

@section('content')
<div class="container-fluid px-2 py-1">

    {{-- Session Flash Alerts --}}
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-2" role="alert" style="font-size: 11.5px; border-radius: 4px;">
            <i class="fa fa-check-circle me-1"></i> {{ session('message') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-2" role="alert" style="font-size: 11.5px; border-radius: 4px;">
            <i class="fa fa-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-book text-warning"></i> My Homework & Tasks
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-graduation-cap"></i> {{ $className }} · {{ $currentSessionName }}
            </span>
        </div>

        {{-- 4 Live Glance Metrics (Total, Submitted, Pending, Evaluated) --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box">
                <div class="mob-metric-tag">Total</div>
                <div class="mob-metric-val" id="heroMetricTotal">{{ $totalCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #4ade80;">Submitted</div>
                <div class="mob-metric-val" id="heroMetricSubmitted" style="color: #4ade80;">{{ $submittedCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #fbbf24;">Pending</div>
                <div class="mob-metric-val" id="heroMetricPending" style="color: #fbbf24;">{{ $pendingCount }}</div>
            </div>
            <div class="mob-metric-box">
                <div class="mob-metric-tag" style="color: #60a5fa;">Checked</div>
                <div class="mob-metric-val" id="heroMetricEvaluated" style="color: #60a5fa;">{{ $evaluatedCount }}</div>
            </div>
        </div>

        {{-- Progress Track Bar --}}
        <div class="mob-progress-wrap">
            <div class="mob-progress-label">
                <span>Submission Progress</span>
                <span><b>{{ $completionPct }}%</b> Completed ({{ $submittedCount }}/{{ $totalCount }})</span>
            </div>
            <div class="mob-progress-track">
                <div class="mob-progress-bar" style="width: {{ $completionPct }}%;"></div>
            </div>
        </div>
    </div>

    {{-- 2. Sticky Live Search & Horizontal Filter Toolbar --}}
    <div class="mob-search-toolbar" id="homeworkSearchToolbar">
        <div class="mob-search-input-box">
            <i class="fa fa-search search-ico"></i>
            <input type="text" id="searchHwInput" class="mob-search-input" placeholder="Search subject, title, teacher..." autocomplete="off">
            <span class="mob-search-clear" id="clearSearchBtn"><i class="fa fa-times-circle"></i></span>
        </div>

        {{-- Quick Filter Chips --}}
        <div class="mob-chips-track">
            <button type="button" class="mob-filter-chip active" data-filter="all">
                All <span class="chip-cnt">{{ $totalCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="pending">
                <i class="fa fa-clock-o text-warning"></i> Pending <span class="chip-cnt">{{ $pendingCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="due_today">
                <i class="fa fa-bolt text-danger"></i> Due Today <span class="chip-cnt">{{ $dueTodayCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="submitted">
                <i class="fa fa-check text-info"></i> Submitted <span class="chip-cnt">{{ $submittedCount }}</span>
            </button>
            <button type="button" class="mob-filter-chip" data-filter="evaluated">
                <i class="fa fa-star text-success"></i> Evaluated <span class="chip-cnt">{{ $evaluatedCount }}</span>
            </button>
            @if($overdueCount > 0)
                <button type="button" class="mob-filter-chip" data-filter="overdue">
                    <i class="fa fa-exclamation-triangle text-danger"></i> Overdue <span class="chip-cnt">{{ $overdueCount }}</span>
                </button>
            @endif
        </div>
    </div>

    {{-- 3. Homework Cards Feed --}}
    <div class="mob-hw-feed" id="homeworkCardsFeed">
        @forelse($data as $item)
            @php
                $status = $item->computed_status ?? 'pending';
                $subjectName = $item->Subject->name ?? 'General';
                $teacherName = trim(($item->Teacher->first_name ?? '') . ' ' . ($item->Teacher->last_name ?? ''));
                if (empty($teacherName)) {
                    $teacherName = trim(($item->User->first_name ?? '') . ' ' . ($item->User->last_name ?? ''));
                }
                if (empty($teacherName)) {
                    $teacherName = 'Class Teacher';
                }

                $issueDate = !empty($item->homework_issue_date) ? date('d-m-Y', strtotime($item->homework_issue_date)) : '-';
                $subDateFormatted = !empty($item->submission_date) ? date('d-m-Y', strtotime($item->submission_date)) : '-';
                $rawSubDate = !empty($item->submission_date) ? date('Y-m-d', strtotime($item->submission_date)) : '';

                $cleanTitle = $item->title ?? 'Homework Assignment';
                $typeTag = 'Homework';
                if (preg_match('/^\[(.*?)\]\s*(.*)$/u', $cleanTitle, $matches)) {
                    $typeTag = $matches[1];
                    $cleanTitle = $matches[2];
                }

                $hasFile = !empty($item->content_file);
                $fileExt = $hasFile ? strtolower(pathinfo($item->content_file, PATHINFO_EXTENSION)) : '';
                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $isPdf = ($fileExt === 'pdf');
                $teacherFileUrl = $hasFile ? env('IMAGE_SHOW_PATH') . 'homework/' . $item->content_file : '';

                $mySub = $item->my_submission;
                $isLateAllowed = (int)($item->allow_late_submission ?? 1) === 1;
                $isPastDeadline = (!empty($rawSubDate) && $rawSubDate < $today);
                $canSubmit = (!$item->is_submitted && (!$isPastDeadline || $isLateAllowed));
            @endphp

            <div class="student-mob-card status-{{ $status }}"
                 data-status="{{ $status }}"
                 data-subject="{{ strtolower($subjectName) }}"
                 data-title="{{ strtolower($item->title ?? '') }}"
                 data-teacher="{{ strtolower($teacherName) }}"
                 data-desc="{{ strtolower($item->description ?? '') }}"
                 id="hwCard-{{ $item->id }}">

                {{-- Card Header --}}
                <div class="mob-card-head">
                    <div class="mob-card-subject-wrap">
                        <span class="mob-subject-pill">
                            <i class="fa fa-bookmark"></i> {{ $subjectName }}
                        </span>
                        <span class="mob-type-tag">{{ $typeTag }}</span>
                    </div>

                    {{-- Status Pill --}}
                    @if($status === 'evaluated')
                        <span class="mob-status-pill pill-evaluated">
                            <i class="fa fa-check-circle"></i> Evaluated
                            @if(!empty($item->awarded_marks))
                                · {{ $item->awarded_marks }}{{ !empty($item->max_marks) ? '/'.$item->max_marks : '' }}
                            @endif
                        </span>
                    @elseif($status === 'submitted')
                        <span class="mob-status-pill pill-submitted">
                            <i class="fa fa-clock-o"></i> Submitted
                        </span>
                    @elseif($status === 'due_today')
                        <span class="mob-status-pill pill-due_today">
                            <i class="fa fa-bolt"></i> Due Today
                        </span>
                    @elseif($status === 'overdue')
                        <span class="mob-status-pill pill-overdue">
                            <i class="fa fa-exclamation-triangle"></i> Overdue
                        </span>
                    @else
                        <span class="mob-status-pill pill-pending">
                            <i class="fa fa-hourglass-start"></i> 
                            @if(isset($item->days_remaining) && $item->days_remaining > 0)
                                {{ $item->days_remaining }}d left
                            @else
                                Assigned
                            @endif
                        </span>
                    @endif
                </div>

                {{-- Homework Title --}}
                <div class="mob-hw-title">{{ $cleanTitle }}</div>

                {{-- Description (with 3-line clamp and read more toggle) --}}
                @if(!empty($item->description))
                    <div class="mob-hw-desc-box">
                        <div class="mob-hw-desc-clamp" id="descClamp-{{ $item->id }}">
                            {!! nl2br(e($item->description)) !!}
                        </div>
                        @if(mb_strlen($item->description) > 130)
                            <span class="mob-read-more-btn" onclick="toggleReadMore({{ $item->id }})" id="readMoreBtn-{{ $item->id }}">
                                Read full details <i class="fa fa-angle-down"></i>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Metadata Grid (Assigned By, Issue Date, Due Date, Max Marks) --}}
                <div class="mob-meta-grid">
                    <div class="mob-meta-item">
                        <i class="fa fa-user-circle"></i>
                        <span>By: <b>{{ $teacherName }}</b></span>
                    </div>
                    <div class="mob-meta-item">
                        <i class="fa fa-calendar-check-o"></i>
                        <span>Issued: <b>{{ $issueDate }}</b></span>
                    </div>
                    <div class="mob-meta-item">
                        <i class="fa fa-hourglass-end {{ $status === 'overdue' ? 'text-danger' : ($status === 'due_today' ? 'text-warning' : '') }}"></i>
                        <span>Due: <b class="{{ $status === 'overdue' ? 'text-danger' : ($status === 'due_today' ? 'text-warning' : '') }}">{{ $subDateFormatted }}</b></span>
                    </div>
                    <div class="mob-meta-item">
                        <i class="fa fa-bullseye"></i>
                        <span>Max Marks: <b>{{ !empty($item->max_marks) ? $item->max_marks : 'N/A' }}</b></span>
                    </div>
                </div>

                {{-- Teacher's Attachment Box (if provided) --}}
                @if($hasFile)
                    <div class="mob-attachment-box">
                        <div class="mob-attachment-info">
                            <i class="fa {{ $isPdf ? 'fa-file-pdf-o text-danger' : ($isImage ? 'fa-file-image-o text-primary' : 'fa-file-text-o text-info') }}"></i>
                            <div style="overflow: hidden;">
                                <div class="mob-attachment-name">Teacher's Attachment</div>
                                <div class="mob-attachment-subtext">Worksheet / Reference Material</div>
                            </div>
                        </div>
                        <div class="mob-attachment-actions">
                            @if($isImage || $isPdf)
                                <button type="button" class="btn-att-action btn-att-preview" 
                                        onclick="previewTeacherAttachment('{{ $teacherFileUrl }}', '{{ $isPdf ? 'pdf' : 'image' }}', '{{ addslashes($cleanTitle) }}')">
                                    <i class="fa fa-eye"></i> View
                                </button>
                            @endif
                            <a href="{{ url('download_homework/' . $item->id) }}" class="btn-att-action btn-att-download" title="Download Material">
                                <i class="fa fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Evaluation Result Strip (if teacher checked homework) --}}
                @if($status === 'evaluated')
                    <div class="mob-eval-strip">
                        <div class="mob-eval-top">
                            <span class="mob-eval-badge">
                                <i class="fa fa-trophy"></i> Teacher Evaluation
                            </span>
                            @if(!empty($item->awarded_marks))
                                <span class="mob-eval-marks">
                                    Marks: {{ $item->awarded_marks }}{{ !empty($item->max_marks) ? ' / ' . $item->max_marks : '' }}
                                </span>
                            @endif
                        </div>
                        @if(!empty($item->teacher_review))
                            <div class="mob-eval-remark">
                                <i class="fa fa-quote-left me-1 opacity-50"></i>{{ $item->teacher_review }}<i class="fa fa-quote-right ms-1 opacity-50"></i>
                            </div>
                        @endif
                    </div>
                @elseif($status === 'submitted')
                    <div class="mob-submitted-strip">
                        <div class="mob-submitted-msg">
                            <i class="fa fa-check-circle text-success"></i>
                            <span>Submitted on {{ !empty($mySub->submission_date) ? date('d-m-Y', strtotime($mySub->submission_date)) : 'N/A' }}</span>
                        </div>
                        <span style="font-size: 9.5px; font-weight: 700; color: #0284c7;">Awaiting Check</span>
                    </div>
                @endif

                {{-- Card Footer Action Bar --}}
                <div class="mob-card-footer">
                    <div class="mob-footer-left">
                        @if($item->is_submitted)
                            <span class="text-success"><i class="fa fa-cloud-upload"></i> Submitted</span>
                        @elseif($isPastDeadline && !$isLateAllowed)
                            <span class="text-danger"><i class="fa fa-ban"></i> Submissions Closed</span>
                        @elseif($isPastDeadline && $isLateAllowed)
                            <span class="text-warning"><i class="fa fa-info-circle"></i> Late Submission Allowed</span>
                        @else
                            <span class="text-muted"><i class="fa fa-check-circle-o"></i> Ready to submit</span>
                        @endif
                    </div>

                    <div class="mob-footer-actions">
                        {{-- View Submission Modal Button (if already submitted) --}}
                        @if($item->is_submitted && !empty($mySub))
                            <button type="button" class="btn-mob-action btn-mob-view" 
                                    onclick="openSubmissionViewSheet({{ $item->id }}, '{{ addslashes($cleanTitle) }}', '{{ addslashes($subjectName) }}')">
                                <i class="fa fa-file-text-o"></i> View My Work
                            </button>
                        @endif

                        {{-- Submit Assignment Button --}}
                        @if($canSubmit)
                            <button type="button" class="btn-mob-action btn-mob-submit {{ ($status === 'due_today' || $status === 'overdue') ? 'urgent' : '' }}"
                                    onclick="openUploadSheet({{ $item->id }}, '{{ addslashes($cleanTitle) }}', '{{ addslashes($subjectName) }}', '{{ $subDateFormatted }}')">
                                <i class="fa fa-cloud-upload"></i> {{ $item->is_submitted ? 'Resubmit' : 'Submit Assignment' }}
                            </button>
                        @elseif(!$item->is_submitted && $isPastDeadline && !$isLateAllowed)
                            <span class="mob-closed-badge"><i class="fa fa-lock"></i> Closed</span>
                        @endif
                    </div>
                </div>

                {{-- Hidden JSON storage for student's submission to eliminate extra AJAX roundtrips --}}
                @if($item->is_submitted && !empty($mySub))
                    <script type="application/json" id="subData-{{ $item->id }}">
                        {!! json_encode([
                            'id' => $mySub->id,
                            'submission_date' => !empty($mySub->submission_date) ? date('d-m-Y', strtotime($mySub->submission_date)) : '',
                            'message' => $mySub->message ?? '',
                            'marks' => $item->awarded_marks,
                            'max_marks' => $item->max_marks,
                            'teacher_review' => $item->teacher_review,
                            'documents' => $mySub->HomeworkDocuments->map(function($doc) {
                                $ext = strtolower(pathinfo($doc->content_file, PATHINFO_EXTENSION));
                                return [
                                    'id' => $doc->id,
                                    'file' => $doc->content_file,
                                    'url' => env('IMAGE_SHOW_PATH') . 'uploadHomework/' . $doc->content_file,
                                    'download_url' => url('download_assignment/' . $doc->content_file),
                                    'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']),
                                    'is_pdf' => ($ext === 'pdf'),
                                    'status' => $doc->status,
                                    'review' => $doc->hw_review,
                                    'marks' => $doc->marks,
                                ];
                            })->values()
                        ]) !!}
                    </script>
                @endif

            </div>
        @empty
            <div class="mob-empty-card">
                <div class="mob-empty-icon"><i class="fa fa-book"></i></div>
                <div class="mob-empty-title">No Homework Assigned</div>
                <div class="mob-empty-desc">Your teachers have not published any homework for your class yet. Check back soon!</div>
            </div>
        @endforelse

        {{-- Dynamic Filter Empty State --}}
        <div class="mob-empty-card" id="noFilterMatchesCard" style="display: none;">
            <div class="mob-empty-icon"><i class="fa fa-search"></i></div>
            <div class="mob-empty-title">No Matching Homework Found</div>
            <div class="mob-empty-desc">No assignments match your search or filter criteria. Try clearing filters.</div>
            <button type="button" class="btn-mob-action btn-mob-view" onclick="resetSearchAndFilters()">
                <i class="fa fa-refresh"></i> Clear Filters
            </button>
        </div>
    </div>

</div>

{{-- ==========================================================================
     SHARED MODAL BACKDROP (Single Native Backdrop for all bottom sheets)
     ========================================================================== --}}
<div class="mob-modal-backdrop" id="mobModalBackdrop" onclick="closeActiveSheet()"></div>

{{-- ==========================================================================
     MODAL SHEET 1: SUBMIT ASSIGNMENT (SLIDE-UP NATIVE MOBILE SHEET)
     ========================================================================== --}}
<div class="mob-modal-sheet" id="uploadModalSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-cloud-upload" style="color: #38bdf8;"></i>
            <span>Submit Assignment</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeActiveSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Pinned Meta Summary --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span class="mob-subject-pill" id="modalSubjectName">Mathematics</span>
            <span class="sheet-meta-pill" id="modalDueDateBadge"><i class="fa fa-hourglass-end text-danger"></i> Due: <b id="modalDueDate">-</b></span>
        </div>
        <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 4px;" id="modalHwTitle">Assignment Title</div>
    </div>

    <form id="uploadHomeworkForm" action="{{ url('upload_homework_student') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
        @csrf
        <input type="hidden" name="homework_id" id="modalHomeworkId" value="">

        {{-- Scrollable Body --}}
        <div class="mob-sheet-body">
            {{-- Note / Message from Student --}}
            <div class="mb-2">
                <label class="form-label" style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 3px;">
                    Message or Remarks for Teacher <span class="text-muted">(Optional)</span>
                </label>
                <textarea class="form-control" name="message" id="studentMessage" rows="2" 
                          placeholder="Type any questions or notes for your teacher..."
                          style="font-size: 11.5px; border-radius: 3px;"></textarea>
            </div>

            {{-- Multi-file Picker Dropzone --}}
            <div class="mb-2">
                <label class="form-label" style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 3px;">
                    Attach Assignment Photos / Documents <span class="text-danger">*</span>
                </label>
                <div class="mob-dropzone" id="fileDropzone" onclick="document.getElementById('fileInput').click()">
                    <i class="fa fa-cloud-upload"></i>
                    <div class="mob-dropzone-text">Tap to Select Files or Photos</div>
                    <div class="mob-dropzone-sub">Supports Camera, Images (JPG, PNG) & PDF</div>
                </div>
                <input type="file" id="fileInput" name="content_file[]" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
            </div>

            {{-- Selected Files Preview List --}}
            <div class="mob-selected-files" id="selectedFilesList" style="display: none;"></div>

            <div id="uploadErrorAlert" class="alert alert-danger py-1 px-2 mb-0" style="display: none; font-size: 10.5px; border-radius: 3px;"></div>
        </div>

        {{-- Sheet Footer --}}
        <div class="mob-sheet-footer">
            <button type="button" class="btn-mob-action btn-mob-view" onclick="closeActiveSheet()">Cancel</button>
            <button type="submit" class="btn-mob-action btn-mob-submit" id="submitHwBtn">
                <span id="submitBtnSpinner" class="spinner-border spinner-border-sm me-1" style="display: none;" role="status" aria-hidden="true"></span>
                <i class="fa fa-check" id="submitBtnIcon"></i> <span id="submitBtnText">Submit Assignment</span>
            </button>
        </div>
    </form>
</div>

{{-- ==========================================================================
     MODAL SHEET 2: VIEW MY SUBMISSION & EVALUATION (SLIDE-UP NATIVE SHEET)
     ========================================================================== --}}
<div class="mob-modal-sheet" id="viewSubmissionModalSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-file-text-o" style="color: #38bdf8;"></i>
            <span>My Submission Details</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeActiveSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Pinned Meta Summary --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span class="mob-subject-pill" id="viewSubSubject">Subject</span>
            <span class="sheet-meta-pill"><i class="fa fa-calendar-check-o text-success"></i> Submitted: <b id="viewSubDate" class="text-dark">-</b></span>
        </div>
        <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 4px;" id="viewSubTitle">Title</div>
    </div>

    {{-- Scrollable Body --}}
    <div class="mob-sheet-body">
        {{-- Teacher Evaluation Card (if evaluated) --}}
        <div id="viewSubEvalCard" style="display: none; background: #f0fdf4; border: 1px solid #86efac; border-radius: 4px; padding: 8px 10px; margin-bottom: 10px;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <span style="font-size: 11px; font-weight: 800; color: #15803d;">
                    <i class="fa fa-trophy text-warning"></i> Teacher Evaluation
                </span>
                <span style="font-size: 12px; font-weight: 800; color: #15803d; background: #dcfce7; padding: 2px 7px; border-radius: 3px;" id="viewSubMarksBadge">
                    Marks: -
                </span>
            </div>
            <div style="font-size: 11px; color: #166534; font-style: italic;" id="viewSubRemarksText">
                "Great work!"
            </div>
        </div>

        {{-- Student Message / Notes --}}
        <div class="mb-2" id="viewSubMsgBox" style="display: none;">
            <div style="font-size: 10.5px; font-weight: 700; color: #475569; margin-bottom: 2px;">Your Note:</div>
            <div style="background: #f1f5f9; border-radius: 3px; padding: 6px 8px; font-size: 11px; color: #1e293b;" id="viewSubMsgText"></div>
        </div>

        {{-- Uploaded Documents / Photos --}}
        <div style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 4px;">
            Submitted Files & Photos:
        </div>
        <div class="mob-docs-grid" id="viewSubDocsGrid">
            {{-- Dynamically populated --}}
        </div>
    </div>

    {{-- Sheet Footer --}}
    <div class="mob-sheet-footer">
        <button type="button" class="btn-mob-action btn-mob-view w-100" onclick="closeActiveSheet()">Close</button>
    </div>
</div>

{{-- ==========================================================================
     MODAL SHEET 3: PREVIEW ATTACHMENT (IMAGE / PDF INLINE VIEWER)
     ========================================================================== --}}
<div class="mob-modal-sheet" id="previewFileModalSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="previewFileModalTitle">
            <i class="fa fa-eye" style="color: #38bdf8;"></i>
            <span>Attachment Preview</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeActiveSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- Scrollable Body --}}
    <div class="mob-sheet-body text-center p-2" id="previewFileModalBody">
        {{-- Injected dynamically --}}
    </div>

    {{-- Sheet Footer --}}
    <div class="mob-sheet-footer d-flex justify-content-between">
        <button type="button" class="btn-mob-action btn-mob-view" onclick="closeActiveSheet()">Close</button>
        <a href="#" id="previewModalDownloadBtn" class="btn-mob-action btn-mob-submit" target="_blank" download>
            <i class="fa fa-download"></i> Download File
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    // =========================================================================
    // 1. Live Instant Search & Horizontal Chips Filter
    // =========================================================================
    let currentFilter = 'all';
    const searchInput = $('#searchHwInput');
    const clearBtn = $('#clearSearchBtn');
    const cards = $('.student-mob-card');
    const emptyState = $('#noFilterMatchesCard');

    function filterCards() {
        const query = searchInput.val().toLowerCase().trim();
        let visibleCount = 0;

        cards.each(function() {
            const card = $(this);
            const status = card.data('status');
            const subject = card.data('subject') || '';
            const title = card.data('title') || '';
            const teacher = card.data('teacher') || '';
            const desc = card.data('desc') || '';

            // Check category filter
            let matchesCategory = false;
            if (currentFilter === 'all') {
                matchesCategory = true;
            } else if (currentFilter === status) {
                matchesCategory = true;
            }

            // Check search text query
            let matchesSearch = true;
            if (query.length > 0) {
                matchesSearch = (
                    subject.includes(query) ||
                    title.includes(query) ||
                    teacher.includes(query) ||
                    desc.includes(query)
                );
            }

            if (matchesCategory && matchesSearch) {
                card.show();
                visibleCount++;
            } else {
                card.hide();
            }
        });

        // Show/hide clear search icon
        if (query.length > 0) {
            clearBtn.show();
        } else {
            clearBtn.hide();
        }

        // Toggle Empty Matches Card
        if (visibleCount === 0 && cards.length > 0) {
            emptyState.show();
        } else {
            emptyState.hide();
        }
    }

    searchInput.on('input', function() {
        filterCards();
    });

    clearBtn.on('click', function() {
        searchInput.val('');
        filterCards();
        searchInput.focus();
    });

    $('.mob-filter-chip').on('click', function() {
        $('.mob-filter-chip').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        filterCards();
    });

    window.resetSearchAndFilters = function() {
        searchInput.val('');
        currentFilter = 'all';
        $('.mob-filter-chip').removeClass('active');
        $('.mob-filter-chip[data-filter="all"]').addClass('active');
        filterCards();
    };

    // =========================================================================
    // 2. Read More / Read Less Toggle
    // =========================================================================
    window.toggleReadMore = function(hwId) {
        const clamp = $('#descClamp-' + hwId);
        const btn = $('#readMoreBtn-' + hwId);

        if (clamp.hasClass('mob-hw-desc-clamp')) {
            clamp.removeClass('mob-hw-desc-clamp');
            btn.html('Show less <i class="fa fa-angle-up"></i>');
        } else {
            clamp.addClass('mob-hw-desc-clamp');
            btn.html('Read full details <i class="fa fa-angle-down"></i>');
        }
    };

    // =========================================================================
    // 3. Native Mobile Bottom-Sheet Modal Controller
    // =========================================================================
    let currentOpenSheet = null;

    window.openSheet = function(sheetId) {
        closeActiveSheet();
        const sheet = document.getElementById(sheetId);
        const backdrop = document.getElementById('mobModalBackdrop');
        if (!sheet || !backdrop) return;

        currentOpenSheet = sheetId;
        backdrop.classList.add('show');
        sheet.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    window.closeActiveSheet = function() {
        if (currentOpenSheet) {
            const sheet = document.getElementById(currentOpenSheet);
            if (sheet) sheet.classList.remove('show');
            currentOpenSheet = null;
        }
        const backdrop = document.getElementById('mobModalBackdrop');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    };

    // Keyboard ESC listener
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && currentOpenSheet) {
            closeActiveSheet();
        }
    });

    // =========================================================================
    // 4. File Input & Upload Modal Handler
    // =========================================================================
    const fileInput = document.getElementById('fileInput');
    const selectedFilesList = document.getElementById('selectedFilesList');
    let selectedFilesArray = [];

    window.openUploadSheet = function(hwId, title, subject, dueDate) {
        $('#modalHomeworkId').val(hwId);
        $('#modalHwTitle').text(title);
        $('#modalSubjectName').text(subject);
        $('#modalDueDate').text(dueDate);
        $('#studentMessage').val('');
        $('#uploadErrorAlert').hide().text('');

        selectedFilesArray = [];
        updateFilePreviewList();

        openSheet('uploadModalSheet');
    };

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files) {
                for (let i = 0; i < e.target.files.length; i++) {
                    selectedFilesArray.push(e.target.files[i]);
                }
                updateFilePreviewList();
            }
        });
    }

    function formatBytes(bytes, decimals = 1) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function updateFilePreviewList() {
        if (!selectedFilesList) return;

        if (selectedFilesArray.length === 0) {
            selectedFilesList.style.display = 'none';
            selectedFilesList.innerHTML = '';
            return;
        }

        selectedFilesList.style.display = 'flex';
        selectedFilesList.innerHTML = '';

        selectedFilesArray.forEach((file, index) => {
            const ext = file.name.split('.').pop().toLowerCase();
            const isImg = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
            const iconClass = isImg ? 'fa-file-image-o text-primary' : (ext === 'pdf' ? 'fa-file-pdf-o text-danger' : 'fa-file-text-o text-secondary');

            const row = document.createElement('div');
            row.className = 'mob-file-row';
            row.innerHTML = `
                <div class="mob-file-row-left">
                    <i class="fa ${iconClass}"></i>
                    <div>
                        <div class="mob-file-row-name">${escapeHtml(file.name)}</div>
                        <div class="mob-file-row-size">${formatBytes(file.size)}</div>
                    </div>
                </div>
                <span class="mob-file-remove" onclick="removeSelectedFile(${index})">
                    <i class="fa fa-times-circle"></i>
                </span>
            `;
            selectedFilesList.appendChild(row);
        });
    }

    window.removeSelectedFile = function(index) {
        selectedFilesArray.splice(index, 1);
        updateFilePreviewList();
    };

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    // Submit Homework Form via AJAX
    $('#uploadHomeworkForm').on('submit', function(e) {
        e.preventDefault();

        const errBox = $('#uploadErrorAlert');
        errBox.hide().text('');

        if (selectedFilesArray.length === 0) {
            errBox.text('Please select at least one document or photo to submit.').show();
            return false;
        }

        const submitBtn = $('#submitHwBtn');
        const spinner = $('#submitBtnSpinner');
        const icon = $('#submitBtnIcon');
        const btnText = $('#submitBtnText');

        submitBtn.prop('disabled', true);
        spinner.show();
        icon.hide();
        btnText.text('Uploading...');

        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}');
        formData.append('homework_id', $('#modalHomeworkId').val());
        formData.append('message', $('#studentMessage').val());

        selectedFilesArray.forEach(file => {
            formData.append('content_file[]', file);
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response && response.status) {
                    closeActiveSheet();
                    window.location.reload();
                } else {
                    errBox.text(response.message || 'Error uploading assignment. Please try again.').show();
                    submitBtn.prop('disabled', false);
                    spinner.hide();
                    icon.show();
                    btnText.text('Submit Assignment');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to upload assignment. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                errBox.text(msg).show();
                submitBtn.prop('disabled', false);
                spinner.hide();
                icon.show();
                btnText.text('Submit Assignment');
            }
        });
    });

    // =========================================================================
    // 5. View My Submission Sheet Handler
    // =========================================================================
    window.openSubmissionViewSheet = function(hwId, title, subject) {
        $('#viewSubTitle').text(title);
        $('#viewSubSubject').text(subject);

        const dataScript = document.getElementById('subData-' + hwId);
        if (!dataScript) return;

        try {
            const data = JSON.parse(dataScript.textContent);

            $('#viewSubDate').text(data.submission_date || '-');

            // Evaluation remarks
            if (data.marks !== null || data.teacher_review) {
                $('#viewSubEvalCard').show();
                let marksTxt = data.marks ? 'Marks: ' + data.marks : 'Evaluated';
                if (data.max_marks) marksTxt += ' / ' + data.max_marks;
                $('#viewSubMarksBadge').text(marksTxt);
                $('#viewSubRemarksText').text(data.teacher_review ? '"' + data.teacher_review + '"' : 'Reviewed by faculty.');
            } else {
                $('#viewSubEvalCard').hide();
            }

            // Student Message
            if (data.message && data.message.trim().length > 0) {
                $('#viewSubMsgText').text(data.message);
                $('#viewSubMsgBox').show();
            } else {
                $('#viewSubMsgBox').hide();
            }

            // Documents Grid
            const grid = $('#viewSubDocsGrid');
            grid.empty();

            if (data.documents && data.documents.length > 0) {
                data.documents.forEach((doc, idx) => {
                    let thumbHtml = '';
                    if (doc.is_image) {
                        thumbHtml = `<img src="${doc.url}" class="mob-doc-thumb" onerror="this.src='{{ asset('public/assets/school/img/logo.png') }}'" alt="Doc">`;
                    } else if (doc.is_pdf) {
                        thumbHtml = `<div class="mob-doc-thumb-fallback"><i class="fa fa-file-pdf-o text-danger"></i></div>`;
                    } else {
                        thumbHtml = `<div class="mob-doc-thumb-fallback"><i class="fa fa-file-text-o text-primary"></i></div>`;
                    }

                    const tile = `
                        <div class="mob-doc-tile">
                            ${thumbHtml}
                            <div class="mob-doc-name" title="${doc.file}">Document #${idx + 1}</div>
                            <div class="mob-doc-actions">
                                ${doc.is_image ? `
                                    <button type="button" class="btn-att-action btn-att-preview" onclick="previewTeacherAttachment('${doc.url}', 'image', 'Uploaded File #${idx + 1}')">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                ` : ''}
                                <a href="${doc.download_url}" class="btn-att-action btn-att-download" download title="Download File">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    `;
                    grid.append(tile);
                });
            } else {
                grid.html('<div class="text-muted text-center py-2 col-12" style="font-size: 11px;">No file attachments found.</div>');
            }

            openSheet('viewSubmissionModalSheet');

        } catch (e) {
            console.error('Error parsing submission data:', e);
        }
    };

    // =========================================================================
    // 6. Preview Attachment Sheet Handler (Images / PDF)
    // =========================================================================
    window.previewTeacherAttachment = function(fileUrl, fileType, title) {
        $('#previewFileModalTitle').html('<i class="fa fa-eye" style="color: #38bdf8;"></i> ' + escapeHtml(title));
        $('#previewModalDownloadBtn').attr('href', fileUrl);

        const body = $('#previewFileModalBody');
        body.empty();

        if (fileType === 'pdf') {
            body.html(`
                <div style="height: 65vh; width: 100%;">
                    <iframe src="${fileUrl}" width="100%" height="100%" style="border: none; border-radius: 4px;"></iframe>
                </div>
            `);
        } else {
            body.html(`
                <div style="max-height: 65vh; overflow: auto; text-align: center;">
                    <img src="${fileUrl}" style="max-width: 100%; max-height: 60vh; border-radius: 4px; object-fit: contain;" alt="Attachment">
                </div>
            `);
        }

        openSheet('previewFileModalSheet');
    };

});
</script>
@endsection
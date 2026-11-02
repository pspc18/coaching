@extends('layout.mobile_app')

@php
    $student = $data;
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    $initial = strtoupper(mb_substr($studentName ?: 'S', 0, 1, 'UTF-8'));
    $imageBase = rtrim((string) env('IMAGE_SHOW_PATH'), '/');
    $photo = !empty($student->image)
        ? ($imageBase ? $imageBase . '/profile/' . $student->image : asset('upload/profile/' . $student->image))
        : '';
    $defaultPhoto = $imageBase ? $imageBase . '/default/user_image.jpg' : asset('assets/school/images/user.png');
    $admissionNo = $student->admissionNo ?: 'N/A';
    $className = $class->name ?? 'Not Assigned';
    $sessionName = Session::get('session_name') ?? date('Y');
    
    $examCount = count($examList ?? []);
    $subjectCount = count($subjectList ?? []);
    $hasMarks = ($totalMaxAll ?? 0) > 0;
    $evaluatedExamsCount = count($chartPercentages ?? []);

    $primaryMobile = $student->mobile ?: '';
    $fatherMobile = $student->father_mobile ?: '';
    $cleanMobile = preg_replace('/[^0-9]/', '', $primaryMobile);
    $cleanFatherMobile = preg_replace('/[^0-9]/', '', $fatherMobile);
@endphp

@section('title', ($studentName ?: 'Student') . ' - Academic Performance')

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE STUDENT ACADEMIC PERFORMANCE & ANALYTICS
   Strictly adheres to Arise ERP Mobile Design System (Plus Jakarta Sans & 4px radius)
   ============================================================================== */

:root {
    --perf-navy-900: #001428;
    --perf-navy-800: #002C54;
    --perf-navy-700: #0a3d6f;
    --perf-sky: #0284c7;
    --perf-emerald: #10b981;
    --perf-amber: #f59e0b;
    --perf-rose: #f43f5e;
    --perf-purple: #8b5cf6;
    --perf-card-border: #cbd5e1;
    --perf-bg-soft: #f8fafc;
}

.mob-perf-page {
    padding: 8px 10px 75px;
    background: #f1f5f9;
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
}
.mob-perf-page * {
    box-sizing: border-box;
}

/* 1. Native Mobile Hero Banner */
.mob-perf-hero {
    background: linear-gradient(135deg, #001428 0%, #002C54 55%, #08335c 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 10px 12px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    margin-bottom: 8px;
    position: relative;
    overflow: hidden;
}
.mob-hero-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
}
.mob-hero-nav-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-hero-btn-back {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    text-decoration: none !important;
    flex-shrink: 0;
    transition: all .15s ease;
}
.mob-hero-btn-back:active {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(0.94);
}
.mob-hero-page-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-hero-nav-right {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}
.mob-hero-tool-btn {
    height: 26px;
    padding: 0 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-hero-tool-btn:active {
    background: rgba(255, 255, 255, 0.28);
    transform: scale(0.95);
}

/* Student Bio Row inside Hero */
.mob-student-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 9px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.14);
    margin-bottom: 8px;
}
.mob-perf-avatar-wrap {
    width: 58px;
    height: 66px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: #001f3d;
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-perf-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.mob-perf-avatar-initial {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    font-size: 24px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-student-meta {
    flex: 1;
    min-width: 0;
}
.mob-student-name {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.25;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-badge-tags-strip {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.mob-hero-tag {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.tag-adm {
    background: rgba(56, 189, 248, 0.22);
    border: 1px solid rgba(56, 189, 248, 0.45);
    color: #e0f2fe;
}
.tag-class {
    background: rgba(251, 191, 36, 0.22);
    border: 1px solid rgba(251, 191, 36, 0.45);
    color: #fef3c7;
}
.tag-tier {
    background: rgba(16, 185, 129, 0.25);
    border: 1px solid rgba(16, 185, 129, 0.5);
    color: #d1fae5;
}
.mob-hero-subtext {
    font-size: 9.5px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* 4-KPI Metric Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 5px 3px;
    text-align: center;
}
.mob-metric-lbl {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-num {
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
    white-space: nowrap;
}
.mob-metric-sub {
    font-size: 8px;
    color: #cbd5e1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.color-score { color: #38bdf8 !important; }
.color-attend { color: #34d399 !important; }
.color-exams { color: #fbbf24 !important; }
.color-marks { color: #a78bfa !important; }

/* Quick Action Contacts Bar */
.mob-quick-contacts {
    display: flex;
    gap: 5px;
}
.mob-quick-btn {
    flex: 1;
    height: 27px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-quick-btn:active {
    transform: scale(0.97);
}
.btn-wa {
    background: #10b981;
    color: #ffffff !important;
}
.btn-call {
    background: #0284c7;
    color: #ffffff !important;
}
.btn-parent {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

/* 2. Native Segmented Tab Bar */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.mob-seg-bar::-webkit-scrollbar {
    display: none;
}
.mob-seg-btn {
    flex: 1;
    min-width: 78px;
    padding: 6px 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: all .15s ease;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.25);
}
.mob-seg-pill {
    display: inline-block;
    padding: 0 4px;
    font-size: 8.5px;
    font-weight: 800;
    border-radius: 3px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* Tab Panes */
.mob-pane {
    display: none;
}
.mob-pane.active {
    display: block;
    animation: fadeIn .18s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(3px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 3. Common Sharp Cards */
.mob-perf-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    margin-bottom: 8px;
    overflow: hidden;
}
.mob-perf-card-head {
    background: #002C54;
    color: #ffffff;
    padding: 7px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-perf-card-head.head-light {
    background: #f8fafc;
    color: #002C54;
    border-bottom: 1px solid #e2e8f0;
}
.mob-perf-card-title {
    font-size: 11.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 0;
}
.mob-perf-card-body {
    padding: 9px 10px;
}

/* 4. Exam Feed Cards */
.mob-exam-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 7px;
}
.mob-view-toggle-btn {
    height: 25px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}
.mob-view-toggle-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

.mob-exam-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    position: relative;
    border-left-width: 4px;
}
.exam-border-distinction { border-left-color: #10b981; }
.exam-border-first { border-left-color: #0284c7; }
.exam-border-second { border-left-color: #f59e0b; }
.exam-border-pass { border-left-color: #06b6d4; }
.exam-border-focus { border-left-color: #ef4444; }
.exam-border-none { border-left-color: #94a3b8; }

.mob-exam-card-head {
    padding: 8px 10px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    cursor: pointer;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.mob-exam-meta {
    flex: 1;
    min-width: 0;
}
.mob-exam-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    margin-bottom: 2px;
}
.mob-exam-date-sub {
    font-size: 9.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.mob-exam-score-box {
    text-align: right;
    flex-shrink: 0;
}
.mob-exam-score-num {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.mob-exam-score-num small {
    font-size: 9.5px;
    color: #64748b;
    font-weight: normal;
}
.mob-exam-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
    margin-top: 2px;
}
.badge-distinction { background: #dcfce7; color: #15803d; }
.badge-first { background: #e0f2fe; color: #0369a1; }
.badge-second { background: #fef3c7; color: #b45309; }
.badge-pass { background: #cffafe; color: #0e7490; }
.badge-focus { background: #fee2e2; color: #b91c1c; }
.badge-pending { background: #f1f5f9; color: #64748b; }

/* Progress bar under exam header */
.mob-exam-prog-wrap {
    height: 4px;
    background: #e2e8f0;
    width: 100%;
}
.mob-exam-prog-fill {
    height: 100%;
    transition: width .4s ease;
}

/* Exam Subject List Details */
.mob-exam-details-body {
    padding: 6px 10px;
    background: #f8fafc;
}
.mob-sub-result-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 11px;
}
.mob-sub-result-row:last-child {
    border-bottom: none;
}
.mob-sub-title-cell {
    flex: 1;
    min-width: 0;
}
.mob-sub-name {
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-sub-rwl-strip {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-top: 2px;
}
.mob-rwl-tag {
    font-size: 8px;
    font-weight: 800;
    padding: 0 3px;
    border-radius: 2px;
    line-height: 1.2;
}
.rwl-r { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.rwl-w { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.rwl-l { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

.mob-sub-marks-cell {
    text-align: right;
    flex-shrink: 0;
}
.mob-sub-marks-val {
    font-weight: 800;
    color: #002C54;
    font-size: 11.5px;
}
.mob-sub-pct-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 2px;
    margin-left: 3px;
    display: inline-block;
}

/* Matrix Table View (Optional Mobile Mode) */
.mob-table-scroll-wrap {
    overflow-x: auto;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
}
.mob-matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 10.5px;
}
.mob-matrix-table thead th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 6px 7px;
    font-size: 10px;
    font-weight: 700;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    text-align: center;
}
.mob-matrix-table tbody tr:nth-child(even) { background: #f8fafc; }
.mob-matrix-table tbody td {
    padding: 6px 7px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    text-align: center;
    color: #1e293b;
    white-space: nowrap;
}

/* 5. Subject Mastery Layout */
.mob-callout-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-callout-box {
    border-radius: 4px;
    padding: 8px 10px;
    border: 1px solid transparent;
}
.callout-top {
    background: #ecfdf5;
    border-color: #a7f3d0;
}
.callout-weak {
    background: #fff1f2;
    border-color: #fecdd3;
}
.mob-callout-tag {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 2px;
}
.callout-top .mob-callout-tag { color: #059669; }
.callout-weak .mob-callout-tag { color: #e11d48; }
.mob-callout-val {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-mastery-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.mob-mastery-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-mastery-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
}
.mob-mastery-score {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
}
.mob-mastery-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 4px;
}
.mob-mastery-fill {
    height: 100%;
    border-radius: 3px;
    transition: width .5s ease;
}
.fill-emerald { background: #10b981; }
.fill-sky { background: #0284c7; }
.fill-amber { background: #f59e0b; }
.fill-rose { background: #ef4444; }

.mob-mastery-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9px;
    color: #64748b;
}

/* 6. Score Progression Trend Card */
.mob-chart-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
}
.mob-chart-canvas-wrap {
    height: 180px;
    width: 100%;
    position: relative;
    margin: 6px 0;
}

/* 7. Student Particulars Bio List */
.mob-bio-list {
    display: flex;
    flex-direction: column;
}
.mob-bio-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px dashed #e2e8f0;
    font-size: 11px;
}
.mob-bio-row:last-child {
    border-bottom: none;
}
.mob-bio-key {
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-bio-key i {
    width: 13px;
    color: #0284c7;
    text-align: center;
}
.mob-bio-val {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mob-bio-val a {
    color: #0284c7;
    text-decoration: none;
}

.mob-stream-pill {
    padding: 2px 7px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    color: #1e293b;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin: 2px 2px 0 0;
}

/* 8. Graceful Empty State (e.g. Student 1448) */
.mob-empty-card {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 26px 14px;
    text-align: center;
    margin-bottom: 8px;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 8px;
}
.mob-empty-heading {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 3px;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    max-width: 320px;
    margin: 0 auto 12px;
}
.mob-empty-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
}
.mob-btn-action-primary {
    height: 28px;
    padding: 0 10px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    background: #002C54;
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
}
.mob-btn-action-outline {
    height: 28px;
    padding: 0 10px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54 !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    text-decoration: none !important;
}

/* Print Overrides */
@media print {
    .mob-perf-page {
        padding: 0 !important;
        background: #ffffff !important;
    }
    .mob-hero-nav, .mob-quick-contacts, .mob-seg-bar, .mob-exam-toolbar, .mobile-bottom-dock, .mobile-top-bar, .mob-empty-actions {
        display: none !important;
    }
    .mob-pane {
        display: block !important;
    }
    .mob-perf-hero {
        background: none !important;
        color: #000000 !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 0 10px !important;
        border-bottom: 2px solid #000000 !important;
    }
    .mob-student-name, .mob-perf-card-title, .mob-exam-title {
        color: #000000 !important;
    }
    .mob-metric-cell, .mob-perf-card, .mob-exam-card {
        border-color: #999999 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection

@section('content')
<div class="mob-perf-page">

    {{-- 1. NATIVE MOBILE HERO CARD --}}
    <div class="mob-perf-hero">
        {{-- Navigation Header --}}
        <div class="mob-hero-nav">
            <div class="mob-hero-nav-left">
                <a href="{{ url('student_performance?layout=mobile') }}" class="mob-hero-btn-back" title="Back to All Students">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="mob-hero-page-title">Academic Performance</div>
            </div>
            <div class="mob-hero-nav-right">
                <a href="{{ url('studentDetail/' . $data->id . '?layout=mobile') }}" class="mob-hero-tool-btn" title="View Full Student Profile">
                    <i class="fa fa-user"></i> Profile
                </a>
                <button type="button" class="mob-hero-tool-btn" onclick="window.print()" title="Print Report Sheet">
                    <i class="fa fa-print"></i>
                </button>
            </div>
        </div>

        {{-- Student Main Bio Banner --}}
        <div class="mob-student-banner">
            <div class="mob-perf-avatar-wrap">
                @if(!empty($photo))
                    <img src="{{ $photo }}" alt="{{ $studentName }}" class="mob-perf-avatar-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="mob-perf-avatar-initial" style="display: none;">{{ $initial }}</div>
                @else
                    <div class="mob-perf-avatar-initial">{{ $initial }}</div>
                @endif
            </div>

            <div class="mob-student-meta">
                <div class="mob-student-name" title="{{ $studentName }}">{{ $studentName ?: 'Student' }}</div>
                <div class="mob-badge-tags-strip">
                    <span class="mob-hero-tag tag-adm"><i class="fa fa-id-card-o"></i> #{{ $admissionNo }}</span>
                    <span class="mob-hero-tag tag-class"><i class="fa fa-graduation-cap"></i> {{ $className }}</span>
                    @if($overallPercentage !== null)
                        <span class="mob-hero-tag tag-tier"><i class="fa fa-star"></i> {{ $performanceGrade }}</span>
                    @endif
                </div>
                <div class="mob-hero-subtext">
                    <span>Session: <b>{{ $sessionName }}</b></span>
                    <span>&bull;</span>
                    <span>Role: <b>Student</b></span>
                </div>
            </div>
        </div>

        {{-- 4-Glance Metric Grid --}}
        <div class="mob-metrics-grid">
            {{-- Score --}}
            <div class="mob-metric-cell">
                <div class="mob-metric-lbl"><i class="fa fa-percent"></i> Score</div>
                <div class="mob-metric-num color-score">
                    {{ $overallPercentage !== null ? $overallPercentage . '%' : 'N/A' }}
                </div>
                <div class="mob-metric-sub">{{ $performanceGrade }}</div>
            </div>

            {{-- Attendance --}}
            <div class="mob-metric-cell">
                <div class="mob-metric-lbl"><i class="fa fa-calendar-check-o"></i> Attend</div>
                <div class="mob-metric-num color-attend">{{ $attendance }}</div>
                <div class="mob-metric-sub">
                    @if($totalAttDays > 0)
                        {{ $presentDays }}/{{ $totalAttDays }} Days
                    @else
                        Active
                    @endif
                </div>
            </div>

            {{-- Exams --}}
            <div class="mob-metric-cell">
                <div class="mob-metric-lbl"><i class="fa fa-file-text-o"></i> Exams</div>
                <div class="mob-metric-num color-exams">{{ $evaluatedExamsCount }}</div>
                <div class="mob-metric-sub">of {{ $examCount }} Tests</div>
            </div>

            {{-- Marks --}}
            <div class="mob-metric-cell">
                <div class="mob-metric-lbl"><i class="fa fa-trophy"></i> Marks</div>
                <div class="mob-metric-num color-marks">
                    {{ $hasMarks ? number_format($totalObtainedAll, 0) : '0' }}
                </div>
                <div class="mob-metric-sub">/ {{ $hasMarks ? number_format($totalMaxAll, 0) : '0' }}</div>
            </div>
        </div>

        {{-- Quick Contact Actions --}}
        @if(!empty($cleanMobile) || !empty($cleanFatherMobile))
            <div class="mob-quick-contacts">
                @if(!empty($cleanMobile))
                    <a href="https://wa.me/91{{ $cleanMobile }}?text=Hello%20{{ urlencode($studentName) }}%2C%20regarding%20academic%20performance%20report" target="_blank" class="mob-quick-btn btn-wa">
                        <i class="fa fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="tel:{{ $cleanMobile }}" class="mob-quick-btn btn-call">
                        <i class="fa fa-phone"></i> Call Student
                    </a>
                @endif
                @if(!empty($cleanFatherMobile) && $cleanFatherMobile !== $cleanMobile)
                    <a href="tel:{{ $cleanFatherMobile }}" class="mob-quick-btn btn-parent">
                        <i class="fa fa-phone"></i> Call Parent
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- 2. NATIVE SEGMENTED TAB BAR --}}
    <div class="mob-seg-bar">
        <button type="button" class="mob-seg-btn active" data-tab="pane-exams">
            <i class="fa fa-file-text-o"></i> Exams <span class="mob-seg-pill">{{ $examCount }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-tab="pane-subjects">
            <i class="fa fa-tags"></i> Subjects <span class="mob-seg-pill">{{ $subjectCount }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-tab="pane-trend">
            <i class="fa fa-line-chart"></i> Trend
        </button>
        <button type="button" class="mob-seg-btn" data-tab="pane-bio">
            <i class="fa fa-id-card-o"></i> Particulars
        </button>
    </div>

    {{-- 3. TAB PANES --}}

    {{-- TAB 1: EXAMS & MARKS FEED --}}
    <div class="mob-pane active" id="pane-exams">
        @if($hasMarks && count($examList) > 0)
            <div class="mob-exam-toolbar">
                <span style="font-size: 11px; font-weight: 700; color: #475569;">
                    <i class="fa fa-check-circle text-success"></i> {{ $evaluatedExamsCount }} Evaluated Exams
                </span>
                <button type="button" class="mob-view-toggle-btn" id="btnToggleMatrixView">
                    <i class="fa fa-table"></i> <span>Table View</span>
                </button>
            </div>

            {{-- 1. Cards Feed View --}}
            <div id="examCardsContainer">
                @php $examIdx = 1; @endphp
                @foreach($examList as $examId => $exam)
                    @php
                        $summary = $examSummaries[$examId] ?? ['obtained' => 0, 'max' => 0, 'percentage' => null, 'sub_attempts' => 0];
                        $examPct = $summary['percentage'];
                        $examAttempts = $summary['sub_attempts'];

                        $cardBorderClass = 'exam-border-none';
                        $badgeClass = 'badge-pending';
                        $statusText = 'Pending';
                        if ($examPct !== null) {
                            if ($examPct >= 75) { $cardBorderClass = 'exam-border-distinction'; $badgeClass = 'badge-distinction'; $statusText = 'Distinction'; }
                            elseif ($examPct >= 60) { $cardBorderClass = 'exam-border-first'; $badgeClass = 'badge-first'; $statusText = '1st Class'; }
                            elseif ($examPct >= 50) { $cardBorderClass = 'exam-border-second'; $badgeClass = 'badge-second'; $statusText = '2nd Class'; }
                            elseif ($examPct >= 35) { $cardBorderClass = 'exam-border-pass'; $badgeClass = 'badge-pass'; $statusText = 'Pass'; }
                            else { $cardBorderClass = 'exam-border-focus'; $badgeClass = 'badge-focus'; $statusText = 'Needs Focus'; }
                        }
                    @endphp

                    <div class="mob-exam-card {{ $cardBorderClass }}" id="exam-card-{{ $examId }}">
                        <div class="mob-exam-card-head" onclick="toggleExamCard('{{ $examId }}')">
                            <div class="mob-exam-meta">
                                <div class="mob-exam-title">
                                    <span style="color:#64748b; font-weight:700;">#{{ $examIdx++ }}</span> {{ $exam->name }}
                                </div>
                                <div class="mob-exam-date-sub">
                                    <span><i class="fa fa-calendar-o text-primary"></i> {{ $summary['date'] }}</span>
                                    @if($examAttempts > 0)
                                        <span>&bull;</span>
                                        <span>{{ $examAttempts }} Subjects</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mob-exam-score-box">
                                @if($examAttempts > 0)
                                    <div class="mob-exam-score-num">
                                        {{ number_format($summary['obtained'], 1) }}
                                        <small>/ {{ number_format($summary['max'], 0) }}</small>
                                    </div>
                                    <span class="mob-exam-status-badge {{ $badgeClass }}">
                                        {{ $examPct !== null ? $examPct . '%' : '' }} {{ $statusText }}
                                    </span>
                                @else
                                    <span class="mob-exam-status-badge badge-pending">No Marks</span>
                                @endif
                            </div>
                        </div>

                        {{-- Progress Line --}}
                        @if($examPct !== null)
                            <div class="mob-exam-prog-wrap">
                                <div class="mob-exam-prog-fill {{ $examPct >= 70 ? 'fill-emerald' : ($examPct >= 50 ? 'fill-sky' : ($examPct >= 35 ? 'fill-amber' : 'fill-rose')) }}" style="width: {{ min(100, $examPct) }}%;"></div>
                            </div>
                        @endif

                        {{-- Subject-wise Details Body --}}
                        <div class="mob-exam-details-body" id="exam-body-{{ $examId }}">
                            @foreach($subjectList as $subId => $subject)
                                @php
                                    $cell = $performanceMatrix[$examId][$subId] ?? ['student_marks' => null, 'max_marks' => 0, 'percentage' => null, 'r_marks' => null, 'w_marks' => null, 'l_marks' => null];
                                    $subMarks = $cell['student_marks'];
                                    $subMax = $cell['max_marks'];
                                    $subPct = $cell['percentage'];
                                @endphp
                                @if($subMarks !== null || $subMax > 0)
                                    <div class="mob-sub-result-row">
                                        <div class="mob-sub-title-cell">
                                            <div class="mob-sub-name">{{ $subject->name }}</div>
                                            @if($cell['r_marks'] !== null || $cell['w_marks'] !== null || $cell['l_marks'] !== null)
                                                <div class="mob-sub-rwl-strip">
                                                    @if($cell['r_marks'] !== null)<span class="mob-rwl-tag rwl-r" title="Right Answers">R:{{ $cell['r_marks'] }}</span>@endif
                                                    @if($cell['w_marks'] !== null)<span class="mob-rwl-tag rwl-w" title="Wrong Answers">W:{{ $cell['w_marks'] }}</span>@endif
                                                    @if($cell['l_marks'] !== null)<span class="mob-rwl-tag rwl-l" title="Unattempted">L:{{ $cell['l_marks'] }}</span>@endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mob-sub-marks-cell">
                                            @if($subMarks !== null)
                                                <span class="mob-sub-marks-val">{{ $subMarks }}</span>
                                                <span style="font-size: 9.5px; color: #64748b;">/ {{ $subMax }}</span>
                                                @php
                                                    $pctColor = $subPct >= 70 ? 'background:#dcfce7; color:#15803d;' : ($subPct >= 50 ? 'background:#e0f2fe; color:#0369a1;' : ($subPct >= 35 ? 'background:#fef3c7; color:#b45309;' : 'background:#fee2e2; color:#b91c1c;'));
                                                @endphp
                                                <span class="mob-sub-pct-pill" style="{{ $pctColor }}">
                                                    {{ $subPct !== null ? $subPct . '%' : '-' }}
                                                </span>
                                            @else
                                                <span style="color:#94a3b8; font-size:10px;">Absent / Not Marked</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 2. Optional Spreadsheet Matrix Table View (Hidden by default, toggled via button) --}}
            <div id="examMatrixTableView" style="display: none;">
                <div class="mob-table-scroll-wrap">
                    <table class="mob-matrix-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="text-align: left; min-width: 130px;">Exam Name</th>
                                <th>Date</th>
                                @foreach($subjectList as $subject)
                                    <th>{{ $subject->name }}</th>
                                @endforeach
                                <th>Total</th>
                                <th>%</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $tIdx = 1; @endphp
                            @foreach($examList as $examId => $exam)
                                @php
                                    $summary = $examSummaries[$examId] ?? ['obtained' => 0, 'max' => 0, 'percentage' => null, 'sub_attempts' => 0];
                                    $examPct = $summary['percentage'];
                                    $statusBadge = 'badge-secondary';
                                    $statusText = 'Pending';
                                    if ($examPct !== null) {
                                        if ($examPct >= 75) { $statusBadge = 'badge-distinction'; $statusText = 'Distinction'; }
                                        elseif ($examPct >= 60) { $statusBadge = 'badge-first'; $statusText = '1st Class'; }
                                        elseif ($examPct >= 50) { $statusBadge = 'badge-second'; $statusText = '2nd Class'; }
                                        elseif ($examPct >= 35) { $statusBadge = 'badge-pass'; $statusText = 'Pass'; }
                                        else { $statusBadge = 'badge-focus'; $statusText = 'Needs Focus'; }
                                    }
                                @endphp
                                <tr>
                                    <td style="font-weight: 700; color: #64748b;">{{ $tIdx++ }}</td>
                                    <td style="text-align: left; font-weight: 700; color: #002C54;">{{ $exam->name }}</td>
                                    <td style="font-size: 9.5px; color: #64748b;">{{ $summary['date'] }}</td>
                                    @foreach($subjectList as $subId => $subject)
                                        @php
                                            $cell = $performanceMatrix[$examId][$subId] ?? ['student_marks' => null, 'max_marks' => 0, 'percentage' => null];
                                            $subMarks = $cell['student_marks'];
                                        @endphp
                                        <td>
                                            @if($subMarks !== null)
                                                <b>{{ $subMarks }}</b> <span style="font-size:8.5px; color:#64748b;">/{{ $cell['max_marks'] }}</span>
                                            @else
                                                <span style="color:#cbd5e1;">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td style="font-weight: 800; color: #002C54;">
                                        {{ $summary['sub_attempts'] > 0 ? number_format($summary['obtained'], 1) : '-' }}
                                    </td>
                                    <td style="font-weight: 800; color: {{ ($examPct ?? 0) >= 50 ? '#15803d' : '#b91c1c' }};">
                                        {{ $examPct !== null ? $examPct . '%' : '-' }}
                                    </td>
                                    <td>
                                        <span class="mob-exam-status-badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            {{-- Graceful Empty State (e.g. Student 1448) --}}
            <div class="mob-empty-card">
                <div class="mob-empty-icon">
                    <i class="fa fa-graduation-cap"></i>
                </div>
                <div class="mob-empty-heading">No Exam Records Found</div>
                <div class="mob-empty-desc">
                    There are currently no examination evaluation records available for <b>{{ $studentName }}</b> in <b>{{ $className }}</b>. Exam marks have either not been uploaded yet or examinations are pending.
                </div>
                <div class="mob-empty-actions">
                    <a href="{{ url('fill_marks?layout=mobile') }}" class="mob-btn-action-primary">
                        <i class="fa fa-pencil-square-o"></i> Enter Marks
                    </a>
                    <a href="{{ url('fill-marks-by-excel?layout=mobile') }}" class="mob-btn-action-outline">
                        <i class="fa fa-file-excel-o text-success"></i> Import Excel
                    </a>
                    <a href="{{ url('student_performance?layout=mobile') }}" class="mob-btn-action-outline">
                        <i class="fa fa-users text-primary"></i> All Students
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- TAB 2: SUBJECT MASTERY BREAKDOWN --}}
    <div class="mob-pane" id="pane-subjects">
        @if(!empty($subjectTotals))
            {{-- Highlight Best & Focus Subjects --}}
            @if($bestSubject || $weakSubject)
                <div class="mob-callout-grid">
                    @if($bestSubject)
                        <div class="mob-callout-box callout-top">
                            <div class="mob-callout-tag"><i class="fa fa-trophy"></i> Top Subject</div>
                            <div class="mob-callout-val" title="{{ $bestSubject }}">{{ $bestSubject }}</div>
                        </div>
                    @endif
                    @if($weakSubject)
                        <div class="mob-callout-box callout-weak">
                            <div class="mob-callout-tag"><i class="fa fa-bullseye"></i> Focus Area</div>
                            <div class="mob-callout-val" title="{{ $weakSubject }}">{{ $weakSubject }}</div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Subject Cards Feed --}}
            @foreach($subjectTotals as $subId => $sMeta)
                @php
                    $sPct = $sMeta['percentage'] ?? 0;
                    $fillColor = $sPct >= 70 ? 'fill-emerald' : ($sPct >= 50 ? 'fill-sky' : ($sPct >= 35 ? 'fill-amber' : 'fill-rose'));
                @endphp
                <div class="mob-mastery-card">
                    <div class="mob-mastery-head">
                        <span class="mob-mastery-title">{{ $sMeta['name'] }}</span>
                        <span class="mob-mastery-score">{{ $sPct }}%</span>
                    </div>
                    <div class="mob-mastery-bar">
                        <div class="mob-mastery-fill {{ $fillColor }}" style="width: {{ min(100, $sPct) }}%;"></div>
                    </div>
                    <div class="mob-mastery-meta-row">
                        <span>Score: <b>{{ $sMeta['obtained'] }} / {{ $sMeta['max'] }}</b></span>
                        <span>Tests: <b>{{ $sMeta['attempts'] }}</b></span>
                        <span>Best: <b>{{ $sMeta['highest'] }}</b></span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="mob-empty-card">
                <div class="mob-empty-icon">
                    <i class="fa fa-tags"></i>
                </div>
                <div class="mob-empty-heading">No Subject Mastery Data</div>
                <div class="mob-empty-desc">
                    Subject averages and mastery percentages will be calculated automatically once test marks are entered.
                </div>
                <a href="{{ url('fill_marks?layout=mobile') }}" class="mob-btn-action-primary">
                    <i class="fa fa-pencil"></i> Enter Exam Marks
                </a>
            </div>
        @endif
    </div>

    {{-- TAB 3: SCORE PROGRESSION TREND --}}
    <div class="mob-pane" id="pane-trend">
        @if(count($chartPercentages) > 1)
            <div class="mob-chart-box">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                    <span style="font-size: 11.5px; font-weight: 800; color: #002C54;">
                        <i class="fa fa-line-chart text-primary"></i> Score Progression Trajectory
                    </span>
                    <span style="font-size: 9.5px; color: #64748b;">Score % by Exam</span>
                </div>
                <div class="mob-chart-canvas-wrap">
                    <canvas id="mobStudentScoreChart"></canvas>
                </div>
            </div>

            {{-- Summary Analysis Box --}}
            <div class="mob-perf-card">
                <div class="mob-perf-card-head head-light">
                    <div class="mob-perf-card-title">
                        <i class="fa fa-info-circle text-primary"></i> Evaluation Analysis
                    </div>
                    <span style="font-size: 9.5px; color: #64748b;">Academic Insights</span>
                </div>
                <div class="mob-perf-card-body">
                    <div class="mob-bio-list">
                        <div class="mob-bio-row">
                            <span class="mob-bio-key"><i class="fa fa-arrow-up text-success"></i> Highest Exam Score</span>
                            <span class="mob-bio-val text-success">{{ !empty($chartPercentages) ? max($chartPercentages) . '%' : 'N/A' }}</span>
                        </div>
                        <div class="mob-bio-row">
                            <span class="mob-bio-key"><i class="fa fa-arrow-down text-danger"></i> Lowest Exam Score</span>
                            <span class="mob-bio-val text-danger">{{ !empty($chartPercentages) ? min($chartPercentages) . '%' : 'N/A' }}</span>
                        </div>
                        <div class="mob-bio-row">
                            <span class="mob-bio-key"><i class="fa fa-certificate text-warning"></i> Evaluation Tier</span>
                            <span class="mob-bio-val">{{ $performanceGrade }}</span>
                        </div>
                        <div class="mob-bio-row">
                            <span class="mob-bio-key"><i class="fa fa-calendar-check-o text-info"></i> Attendance Impact</span>
                            <span class="mob-bio-val">{{ $attendance }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mob-empty-card">
                <div class="mob-empty-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div class="mob-empty-heading">Trajectory Curve Unavailable</div>
                <div class="mob-empty-desc">
                    Score trend trajectory requires at least 2 evaluated exams to visualize chronological performance curves.
                </div>
                <a href="{{ url('fill_marks?layout=mobile') }}" class="mob-btn-action-primary">
                    <i class="fa fa-pencil"></i> Add More Exam Marks
                </a>
            </div>
        @endif
    </div>

    {{-- TAB 4: STUDENT BIO & PARTICULAR DETAILS --}}
    <div class="mob-pane" id="pane-bio">
        <div class="mob-perf-card">
            <div class="mob-perf-card-head head-light">
                <div class="mob-perf-card-title">
                    <i class="fa fa-user-circle text-primary"></i> Personal & Academic Particulars
                </div>
                <span style="font-size: 9.5px; color: #64748b;">Student Record</span>
            </div>
            <div class="mob-perf-card-body">
                <div class="mob-bio-list">
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-id-card-o"></i> Admission Number</span>
                        <span class="mob-bio-val">#{{ $admissionNo }}</span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-graduation-cap"></i> Assigned Class</span>
                        <span class="mob-bio-val">{{ $className }}</span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-user"></i> Father's Name</span>
                        <span class="mob-bio-val">{{ $data->father_name ?: '-' }}</span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-female"></i> Mother's Name</span>
                        <span class="mob-bio-val">{{ $data->mother_name ?: '-' }}</span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-phone"></i> Student Mobile</span>
                        <span class="mob-bio-val">
                            @if(!empty($data->mobile))
                                <a href="tel:{{ $data->mobile }}"><i class="fa fa-phone text-success"></i> {{ $data->mobile }}</a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-phone-square"></i> Father Mobile</span>
                        <span class="mob-bio-val">
                            @if(!empty($data->father_mobile))
                                <a href="tel:{{ $data->father_mobile }}"><i class="fa fa-phone text-primary"></i> {{ $data->father_mobile }}</a>
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-birthday-cake"></i> Date of Birth</span>
                        <span class="mob-bio-val">{{ !empty($data->dob) ? date('d-m-Y', strtotime($data->dob)) : '-' }}</span>
                    </div>
                    <div class="mob-bio-row">
                        <span class="mob-bio-key"><i class="fa fa-map-marker"></i> City / Address</span>
                        <span class="mob-bio-val" title="{{ $data->address ?? '' }}">{{ $data->address ? Str::limit($data->address, 24) : '-' }}</span>
                    </div>
                </div>

                {{-- Mapped Stream Subjects --}}
                <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                    <div style="font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                        <i class="fa fa-book text-primary"></i> Mapped Subjects ({{ $subjectCount }})
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                        @forelse($subjectList as $sub)
                            <span class="mob-stream-pill">
                                <i class="fa fa-check text-success"></i> {{ $sub->name }}
                            </span>
                        @empty
                            <span style="font-size: 10px; color: #94a3b8;">No stream subjects assigned</span>
                        @endforelse
                    </div>
                </div>

                {{-- Link to Full 360 Student Profile --}}
                <div style="margin-top: 12px; text-align: center;">
                    <a href="{{ url('studentDetail/' . $data->id . '?layout=mobile') }}" class="mob-btn-action-primary" style="width: 100%; justify-content: center;">
                        <i class="fa fa-id-badge"></i> Open 360&deg; Student Profile &amp; Fees
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
@if(count($chartPercentages) > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var canvas = document.getElementById('mobStudentScoreChart');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var chartLabels = @json($chartLabels);
    var chartData = @json($chartPercentages);

    var gradient = ctx.createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, 'rgba(2, 132, 199, 0.32)');
    gradient.addColorStop(1, 'rgba(2, 132, 199, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Score %',
                data: chartData,
                borderColor: '#0284c7',
                borderWidth: 2.2,
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#002C54',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: function(v) { return v + '%'; },
                        font: { size: 9, family: 'Plus Jakarta Sans' }
                    },
                    grid: { color: 'rgba(203, 213, 225, 0.35)' }
                },
                x: {
                    ticks: {
                        font: { size: 9, family: 'Plus Jakarta Sans' },
                        maxRotation: 30
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) { return 'Score: ' + ctx.parsed.y + '%'; }
                    }
                }
            }
        }
    });
});
</script>
@endif

<script>
// Tab Switching
document.addEventListener('DOMContentLoaded', function() {
    var segBtns = document.querySelectorAll('.mob-seg-btn');
    var panes = document.querySelectorAll('.mob-pane');

    segBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetTab = this.getAttribute('data-tab');
            if (!targetTab) return;

            segBtns.forEach(function(b) { b.classList.remove('active'); });
            panes.forEach(function(p) { p.classList.remove('active'); });

            this.classList.add('active');
            var activePane = document.getElementById(targetTab);
            if (activePane) {
                activePane.classList.add('active');
            }
        });
    });

    // Toggle Matrix Table View vs Card Feed
    var btnToggle = document.getElementById('btnToggleMatrixView');
    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            var cardsBox = document.getElementById('examCardsContainer');
            var tableBox = document.getElementById('examMatrixTableView');
            var isTable = tableBox.style.display !== 'none';

            if (isTable) {
                tableBox.style.display = 'none';
                cardsBox.style.display = 'block';
                btnToggle.classList.remove('active');
                btnToggle.querySelector('span').textContent = 'Table View';
                btnToggle.querySelector('i').className = 'fa fa-table';
            } else {
                tableBox.style.display = 'block';
                cardsBox.style.display = 'none';
                btnToggle.classList.add('active');
                btnToggle.querySelector('span').textContent = 'Card View';
                btnToggle.querySelector('i').className = 'fa fa-th-large';
            }
        });
    }
});

// Toggle Individual Exam Accordion Card
function toggleExamCard(examId) {
    var body = document.getElementById('exam-body-' + examId);
    if (!body) return;
    if (body.style.display === 'none') {
        body.style.display = 'block';
    } else {
        body.style.display = 'none';
    }
}
</script>
@endsection

@extends('layout.mobile_app')

@php
    $classType = Helper::classType();
    $studentCount = is_countable($students ?? []) ? count($students) : 0;
    $selectedClass = collect($classType)->firstWhere('id', (int) ($search['class_type_id'] ?? 0));
    $gradeArray = $gradeArray ?? [
        'grade_1' => 0,
        'grade_2' => 0,
        'grade_3' => 0,
        'grade_4' => 0,
        'grade_5' => 0,
    ];
    $topPerformers = ($gradeArray['grade_1'] ?? 0) + ($gradeArray['grade_2'] ?? 0);
    $needsImprovement = ($gradeArray['grade_4'] ?? 0) + ($gradeArray['grade_5'] ?? 0);
    
    // Grade split percentages
    $g1Pct = $studentCount > 0 ? round((($gradeArray['grade_1'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g2Pct = $studentCount > 0 ? round((($gradeArray['grade_2'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g3Pct = $studentCount > 0 ? round((($gradeArray['grade_3'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g4Pct = $studentCount > 0 ? round((($gradeArray['grade_4'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g5Pct = $studentCount > 0 ? round((($gradeArray['grade_5'] ?? 0) / $studentCount) * 100, 1) : 0;

    // Topper Rankings (Top 3)
    $scoredStudents = collect($students)->map(function($st) use ($studentMetrics) {
        $st->temp_score = (float)($studentMetrics[$st->id]['grade_percentage'] ?? 0);
        return $st;
    })->sortByDesc('temp_score')->values();

    $topperMap = [];
    if ($scoredStudents->count() > 0 && $scoredStudents[0]->temp_score > 0) {
        $topperMap[$scoredStudents[0]->id] = 1;
    }
    if ($scoredStudents->count() > 1 && $scoredStudents[1]->temp_score > 0) {
        $topperMap[$scoredStudents[1]->id] = 2;
    }
    if ($scoredStudents->count() > 2 && $scoredStudents[2]->temp_score > 0) {
        $topperMap[$scoredStudents[2]->id] = 3;
    }

    // Count low attendance (<75%)
    $lowAttCount = 0;
    foreach ($students as $st) {
        $m = $studentMetrics[$st->id] ?? [];
        $attStr = $m['attendance'] ?? 'N/A';
        $attVal = floatval(str_replace('%', '', $attStr));
        if ($attStr !== 'N/A' && $attVal < 75) {
            $lowAttCount++;
        }
    }

    $imageBase = rtrim((string) env('IMAGE_SHOW_PATH'), '/');
    $hasFilter = (!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']));
@endphp

@section('title', 'Student Performance')

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE STUDENT ACADEMIC PERFORMANCE
   100% Matching Arise ERP Mobile Design System (enquiryView & duesList standard)
   ============================================================================== */

.mob-perf-container {
    padding: 6px 0 80px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}
.mob-perf-container * {
    box-sizing: border-box;
}

/* 1. Hero Card - Perfect Clearance from Fixed Top Bar */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-top: 2px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    min-height: 24px;
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
    max-width: 140px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 4 Metrics Glance Grid (Clickable Tab Shortcuts) */
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
    padding: 6px 4px;
    text-align: center;
    cursor: pointer;
    transition: background .15s ease, transform .12s ease;
}
.mob-metric-box:active {
    transform: scale(0.96);
    background: rgba(255, 255, 255, 0.18);
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 1px;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-att { color: #4ade80; }
.mob-metric-val.val-top { color: #38bdf8; }
.mob-metric-val.val-help { color: #fbbf24; }

/* Hero Action Bar */
.mob-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-act-btn {
    height: 32px;
    padding: 0 10px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    box-sizing: border-box;
    width: 100%;
}
.mob-act-btn:active {
    transform: scale(0.96);
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
    position: relative;
}
.filter-active-dot {
    position: absolute;
    top: 5px;
    right: 8px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #38bdf8;
    box-shadow: 0 0 6px #38bdf8;
}

/* 2. Filter Drawer */
.mob-filter-box {
    display: none;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.mob-filter-box.show {
    display: block;
    animation: fadeIn .18s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-3px); }
    to { opacity: 1; transform: translateY(0); }
}
.mob-filter-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.mob-form-group {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.mob-form-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
}
.mob-form-control {
    width: 100%;
    height: 32px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    padding: 0 8px;
    font-size: 11.5px;
    color: #0f172a;
    background: #f8fafc;
}
.mob-form-control:focus {
    border-color: #0284c7;
    background: #ffffff;
    outline: none;
}
.mob-filter-actions {
    display: flex;
    gap: 6px;
    margin-top: 6px;
}
.mob-btn-apply {
    flex: 1;
    height: 32px;
    border-radius: 4px;
    background: #002C54;
    border: none;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
}
.mob-btn-reset {
    height: 32px;
    padding: 0 12px;
    border-radius: 4px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    font-size: 11.5px;
    font-weight: 600;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* 3. Compact Search & Horizontal Filter Chips (Arise Gold Standard) */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11.5px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
    -webkit-appearance: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}

/* Horizontal Chips Slider */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 4px 9px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}
.mob-chip-count {
    font-size: 8.5px;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-chip.active .mob-chip-count {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* 4. Feed List Header & Controls */
.mob-feed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding: 0 2px;
}
.mob-feed-count {
    font-size: 11px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-feed-badge {
    background: #e2e8f0;
    color: #334155;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 3px;
}

/* 5. Student Cards Feed List (Matching Admission Mobile View) */
.student-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    border-left: 3.5px solid #94a3b8;
    transition: transform .1s ease, border-color .1s ease;
}
.student-mob-card.border-g1 { border-left-color: #10b981; }
.student-mob-card.border-g2 { border-left-color: #2563eb; }
.student-mob-card.border-g3 { border-left-color: #0284c7; }
.student-mob-card.border-help { border-left-color: #f59e0b; }
.student-mob-card.border-low { border-left-color: #dc2626; }

/* Student Card Header */
.student-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 6px;
}
.student-card-profile-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.student-avatar {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    flex-shrink: 0;
}
.student-avatar-fallback {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 4px;
    background: #002C54;
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
}
.topper-rank-crown {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 11px;
    line-height: 1;
}
.student-primary-info {
    min-width: 0;
    flex: 1;
}
.student-name-text {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-badges-row {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}
.student-adm-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.student-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}

/* Grade Badge on Right */
.student-grade-badge-wrap {
    text-align: right;
    flex-shrink: 0;
}
.mob-grade-badge {
    display: inline-block;
    font-size: 9px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 2px;
    line-height: 1.2;
}
.mob-grade-badge.badge-g1 { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.mob-grade-badge.badge-g2 { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.mob-grade-badge.badge-g3 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.mob-grade-badge.badge-g4 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.mob-grade-badge.badge-g5 { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.student-score-num {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1px;
}

/* Student Card Details Meta Grid */
.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 6px;
}
.student-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-meta-item > i {
    width: 13px;
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
}
.student-phone-link {
    color: #0f172a;
    font-weight: 700;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
}
.student-wa-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    min-width: 20px;
    border-radius: 3px;
    background: #22c55e;
    color: #ffffff !important;
    margin-left: 4px;
    text-decoration: none !important;
    flex-shrink: 0;
    line-height: 1;
}
.student-wa-btn:active {
    transform: scale(0.92);
    background: #16a34a;
}
.student-wa-btn i {
    font-size: 11.5px !important;
    color: #ffffff !important;
}

/* Mini Progress Bar */
.student-progress-wrap {
    margin-bottom: 6px;
}
.student-progress-bar {
    height: 3px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.student-progress-fill {
    height: 100%;
    border-radius: 2px;
}
.student-progress-fill.high { background: #10b981; }
.student-progress-fill.mid { background: #0284c7; }
.student-progress-fill.low { background: #f59e0b; }
.student-progress-fill.poor { background: #ef4444; }

/* Student Card Actions Row */
.student-card-actions {
    display: flex;
    gap: 5px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-action {
    flex: 1;
    height: 29px;
    padding: 0 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1px solid transparent;
}
.mob-btn-action:active {
    transform: scale(0.96);
}
.btn-act-profile {
    background: #002C54;
    color: #ffffff !important;
}
.btn-act-snapshot {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155 !important;
}
.btn-act-phone {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0284c7 !important;
    flex: 0 0 29px;
}

/* 6. Analytics Card (Grade Distribution & Toppers) */
.mob-analytics-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-analytics-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-grade-bar-split {
    display: flex;
    height: 12px;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
    background: #e2e8f0;
}
.mob-grade-seg {
    height: 100%;
}
.mob-grade-seg.seg-g1 { background: #16a34a; }
.mob-grade-seg.seg-g2 { background: #2563eb; }
.mob-grade-seg.seg-g3 { background: #0891b2; }
.mob-grade-seg.seg-g4 { background: #d97706; }
.mob-grade-seg.seg-g5 { background: #dc2626; }

.mob-grade-legend-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}
.mob-grade-legend-box {
    padding: 4px 6px;
    border-radius: 3px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
}
.mob-grade-legend-box span.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}

/* Empty State Card */
.mob-empty-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    color: #64748b;
    margin-bottom: 8px;
}
.mob-empty-icon {
    font-size: 24px;
    color: #94a3b8;
    margin-bottom: 6px;
}
.mob-empty-title {
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    margin: 0 0 2px 0;
}
.mob-empty-desc {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
}

/* 7. Quick Student Snapshot Modal */
.mob-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 20, 40, 0.7);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    padding: 12px;
}
.mob-modal-overlay.show {
    display: flex;
    animation: fadeIn .15s ease;
}
.mob-modal-card {
    background: #ffffff;
    border-radius: 4px;
    width: 100%;
    max-width: 360px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}
.mob-modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12.5px;
    font-weight: 800;
}
.mob-modal-body {
    padding: 12px;
}
.mob-modal-student-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-modal-avatar {
    width: 44px;
    height: 44px;
    border-radius: 4px;
    background: #002C54;
    color: #ffffff;
    font-size: 18px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-modal-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    margin-bottom: 10px;
}
.mob-modal-stat-box {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 6px 8px;
}
.mob-modal-stat-lbl {
    font-size: 9px;
    color: #64748b;
    font-weight: 700;
    text-transform: uppercase;
}
.mob-modal-stat-val {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1px;
}
.mob-modal-footer {
    padding: 8px 12px;
    background: #f1f5f9;
    border-top: 1px solid #cbd5e1;
    display: flex;
    gap: 6px;
    justify-content: flex-end;
}
</style>
@endsection

@section('content')
<div class="mob-perf-container">

    {{-- 1. HERO CARD (Standard Arise ERP Mobile) --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-line-chart text-warning"></i> Student Performance
            </div>
            <span class="mob-session-pill"><i class="fa fa-th-large mr-1"></i> {{ $selectedClass->name ?? 'All Classes' }}</span>
        </div>

        {{-- 4 Metrics Glance Grid (Clickable to switch tab) --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box" onclick="selectTab('all')">
                <span class="mob-metric-tag">Total Students</span>
                <span class="mob-metric-val">{{ $studentCount }}</span>
            </div>
            <div class="mob-metric-box" onclick="selectTab('low_att')">
                <span class="mob-metric-tag">Avg Attendance</span>
                <span class="mob-metric-val val-att">{{ $averageAttendance !== null ? $averageAttendance . '%' : 'N/A' }}</span>
            </div>
            <div class="mob-metric-box" onclick="selectTab('g1')">
                <span class="mob-metric-tag">Top Tier (G1/2)</span>
                <span class="mob-metric-val val-top">{{ $topPerformers }}</span>
            </div>
            <div class="mob-metric-box" onclick="selectTab('needs_help')">
                <span class="mob-metric-tag">Needs Help (G4/5)</span>
                <span class="mob-metric-val val-help">{{ $needsImprovement }}</span>
            </div>
        </div>

        {{-- Action Bar in Hero --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnToggleFilter">
                <i class="fa fa-filter"></i> Search &amp; Filter Class
                @if($hasFilter)
                    <span class="filter-active-dot"></span>
                @endif
            </button>
        </div>
    </div>

    {{-- 2. COLLAPSIBLE FILTER DRAWER --}}
    <div class="mob-filter-box {{ $hasFilter ? 'show' : '' }}" id="mobFilterBox">
        <form action="{{ url('student_performance') }}" method="POST" id="mobPerfFilterForm">
            @csrf
            <input type="hidden" name="layout" value="mobile">

            <div class="mob-filter-grid">
                <div class="mob-form-group">
                    <label class="mob-form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_type_id" class="mob-form-control" onchange="document.getElementById('mobPerfFilterForm').submit()">
                        <option value="">-- All Classes --</option>
                        @foreach($classType as $type)
                            <option value="{{ $type->id }}" {{ (int)($search['class_type_id'] ?? 0) === (int)$type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label">Admission No</label>
                    <input type="text" name="admissionNo" class="mob-form-control" placeholder="e.g. 1024" value="{{ $search['admissionNo'] ?? '' }}">
                </div>

                <div class="mob-form-group">
                    <label class="mob-form-label">Keyword</label>
                    <input type="text" name="name" class="mob-form-control" placeholder="Student name, mobile, father name..." value="{{ $search['name'] ?? '' }}">
                </div>
            </div>

            <div class="mob-filter-actions">
                <button type="submit" class="mob-btn-apply">
                    <i class="fa fa-search mr-1"></i> Apply Filter
                </button>
                @if($hasFilter)
                    <a href="{{ url('student_performance?layout=mobile') }}" class="mob-btn-reset">
                        <i class="fa fa-refresh mr-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. SEARCH & HORIZONTAL FILTER CHIPS (Arise Gold Standard Toolbar) --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="quickSearchInput" class="mob-search-input" placeholder="Search student name, adm no, father, mobile...">
            <i class="fa fa-times-circle mob-search-clear" id="quickSearchClear"></i>
        </div>

        <div class="mob-chips-scroll" id="mobFilterChipsScroll">
            <div class="mob-chip active" data-tab="all" onclick="selectTab('all')">
                👥 All Students <span class="mob-chip-count">{{ $studentCount }}</span>
            </div>
            <div class="mob-chip" data-tab="g1" onclick="selectTab('g1')">
                🥇 Grade 1 <span class="mob-chip-count">{{ $gradeArray['grade_1'] ?? 0 }}</span>
            </div>
            <div class="mob-chip" data-tab="g2" onclick="selectTab('g2')">
                🥈 Grade 2 <span class="mob-chip-count">{{ $gradeArray['grade_2'] ?? 0 }}</span>
            </div>
            <div class="mob-chip" data-tab="g3" onclick="selectTab('g3')">
                🔷 Grade 3 <span class="mob-chip-count">{{ $gradeArray['grade_3'] ?? 0 }}</span>
            </div>
            <div class="mob-chip" data-tab="needs_help" onclick="selectTab('needs_help')">
                ⚠️ Needs Help <span class="mob-chip-count">{{ $needsImprovement }}</span>
            </div>
            <div class="mob-chip" data-tab="low_att" onclick="selectTab('low_att')">
                📉 Low Att. <span class="mob-chip-count">{{ $lowAttCount }}</span>
            </div>
            <div class="mob-chip" data-tab="analytics" onclick="selectTab('analytics')">
                📊 Analytics
            </div>
        </div>
    </div>

    {{-- 4. FEED CONTENT SECTION --}}
    <div id="mobFeedSection">

        {{-- Feed Header --}}
        <div class="mob-feed-header">
            <div class="mob-feed-count">
                <i class="fa fa-users text-primary mr-1"></i> <span id="feedHeaderTitle">All Students Performance</span>
                <span class="mob-feed-badge" id="feedHeaderCount">{{ $studentCount }}</span>
            </div>
        </div>

        {{-- Analytics Panel (Shown when analytics tab is clicked) --}}
        <div id="analyticsPanel" style="display: none;">
            {{-- Grade Distribution --}}
            <div class="mob-analytics-card">
                <h4 class="mob-analytics-title">
                    <i class="fa fa-pie-chart text-primary"></i> Grade Split Breakdown
                </h4>
                @if($studentCount > 0)
                    <div class="mob-grade-bar-split">
                        @if($g1Pct > 0)<div class="mob-grade-seg seg-g1" style="width: {{ $g1Pct }}%;"></div>@endif
                        @if($g2Pct > 0)<div class="mob-grade-seg seg-g2" style="width: {{ $g2Pct }}%;"></div>@endif
                        @if($g3Pct > 0)<div class="mob-grade-seg seg-g3" style="width: {{ $g3Pct }}%;"></div>@endif
                        @if($g4Pct > 0)<div class="mob-grade-seg seg-g4" style="width: {{ $g4Pct }}%;"></div>@endif
                        @if($g5Pct > 0)<div class="mob-grade-seg seg-g5" style="width: {{ $g5Pct }}%;"></div>@endif
                    </div>
                    <div class="mob-grade-legend-grid">
                        <div class="mob-grade-legend-box" onclick="selectTab('g1')">
                            <span><span class="dot" style="background:#16a34a;"></span> Grade 1</span>
                            <b>{{ $g1Pct }}% ({{ $gradeArray['grade_1'] ?? 0 }})</b>
                        </div>
                        <div class="mob-grade-legend-box" onclick="selectTab('g2')">
                            <span><span class="dot" style="background:#2563eb;"></span> Grade 2</span>
                            <b>{{ $g2Pct }}% ({{ $gradeArray['grade_2'] ?? 0 }})</b>
                        </div>
                        <div class="mob-grade-legend-box" onclick="selectTab('g3')">
                            <span><span class="dot" style="background:#0891b2;"></span> Grade 3</span>
                            <b>{{ $g3Pct }}% ({{ $gradeArray['grade_3'] ?? 0 }})</b>
                        </div>
                        <div class="mob-grade-legend-box" onclick="selectTab('needs_help')">
                            <span><span class="dot" style="background:#d97706;"></span> Grade 4 &amp; 5</span>
                            <b>{{ round($g4Pct + $g5Pct, 1) }}% ({{ $needsImprovement }})</b>
                        </div>
                    </div>
                @else
                    <p style="font-size: 11px; color: #64748b; margin: 0;">No student data available.</p>
                @endif
            </div>

            {{-- Top 3 Leaderboard --}}
            <div class="mob-analytics-card">
                <h4 class="mob-analytics-title">
                    <i class="fa fa-trophy text-warning"></i> Class Toppers (Top 3)
                </h4>
                @if($scoredStudents->filter(fn($s) => $s->temp_score > 0)->count() > 0)
                    <div class="student-feed-list">
                        @foreach($scoredStudents->filter(fn($s) => $s->temp_score > 0)->take(3) as $st)
                            @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                        @endforeach
                    </div>
                @else
                    <p style="font-size: 11px; color: #64748b; margin: 0;">No scored exams recorded yet for class ranking.</p>
                @endif
            </div>
        </div>

        {{-- Student Cards Feed List (Filtered by active tab and search input) --}}
        <div class="student-feed-list" id="studentCardsFeed">
            @foreach($students as $student)
                @include('students.academic_performance.mobile.partials.student_card', ['student' => $student])
            @endforeach
        </div>

        {{-- Empty State Card (When no students match active tab or search query) --}}
        <div class="mob-empty-card" id="feedEmptyState" style="display: none;">
            <div class="mob-empty-icon"><i class="fa fa-users"></i></div>
            <h5 class="mob-empty-title">No Students Found</h5>
            <p class="mob-empty-desc">Is category ya search criteria ke anusar koi student record uplabdh nahi hai.</p>
        </div>

    </div>

</div>

{{-- 8. QUICK STUDENT SNAPSHOT MODAL --}}
<div class="mob-modal-overlay" id="mobSnapshotModal" onclick="closeMobSnapshot(event)">
    <div class="mob-modal-card" onclick="event.stopPropagation()">
        <div class="mob-modal-header">
            <span><i class="fa fa-id-card-o mr-1"></i> Student Academic Snapshot</span>
            <button type="button" style="background:none; border:none; color:#ffffff; font-size:16px; cursor:pointer;" onclick="closeMobSnapshotDirect()">&times;</button>
        </div>
        <div class="mob-modal-body">
            <div class="mob-modal-student-row">
                <div class="mob-modal-avatar" id="modalAvatar">S</div>
                <div style="min-width:0; flex:1;">
                    <div style="font-size: 13.5px; font-weight: 800; color: #002C54; line-height: 1.2;" id="modalName">Student Name</div>
                    <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;" id="modalSub">ADM: 1001 &bull; Class 10th</div>
                    <div style="font-size: 10px; color: #475569; margin-top: 1px;" id="modalFather">Father: -</div>
                </div>
            </div>

            <div class="mob-modal-grid">
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Academic Grade</div>
                    <div class="mob-modal-stat-val text-primary" id="modalGrade">Grade 1</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Average Score</div>
                    <div class="mob-modal-stat-val text-success" id="modalScore">95.0%</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Attendance Rate</div>
                    <div class="mob-modal-stat-val" id="modalAtt">88.5%</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Class Standing</div>
                    <div class="mob-modal-stat-val text-warning" id="modalRank">-</div>
                </div>
            </div>

            <div id="modalContactWrap" style="background:#f1f5f9; padding:6px 10px; border-radius:3px; font-size:11px; color:#334155; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa fa-phone text-primary mr-1"></i> <b id="modalMobile">-</b></span>
                <a href="#" id="modalWhatsappBtn" target="_blank" class="student-wa-btn" style="width:auto; padding:2px 8px; font-weight:700; font-size:10px;">
                    <i class="fa fa-whatsapp mr-1"></i> WhatsApp
                </a>
            </div>
        </div>
        <div class="mob-modal-footer">
            <button type="button" class="mob-btn-action btn-act-snapshot" onclick="closeMobSnapshotDirect()">Close</button>
            <a href="#" id="modalFullAnalyticsBtn" class="mob-btn-action btn-act-profile">
                <i class="fa fa-external-link mr-1"></i> View Analytics
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
var activeTab = 'all';
var tabLabels = {
    'all': '👥 All Students Performance',
    'g1': '🥇 Grade 1 Students (91% - 100%)',
    'g2': '🥈 Grade 2 Students (81% - 90%)',
    'g3': '🔷 Grade 3 Students (71% - 80%)',
    'needs_help': '⚠️ Needs Help (Grade 4 & 5)',
    'low_att': '📉 Low Attendance (< 75%)',
    'analytics': '📊 Academic Analytics &amp; KPIs'
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Filter Drawer
    var btnToggle = document.getElementById('btnToggleFilter');
    var filterBox = document.getElementById('mobFilterBox');
    if (btnToggle && filterBox) {
        btnToggle.addEventListener('click', function() {
            filterBox.classList.toggle('show');
        });
    }

    // 2. Search Input Handlers
    var searchInput = document.getElementById('quickSearchInput');
    var searchClear = document.getElementById('quickSearchClear');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var val = this.value.trim();
            if (searchClear) searchClear.style.display = val.length > 0 ? 'inline-block' : 'none';
            applyCardFiltering();
        });
    }
    if (searchClear) {
        searchClear.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                searchClear.style.display = 'none';
                applyCardFiltering();
            }
        });
    }

    // 3. Direct URL Tab Parameter (?tab=g1, ?tab=all, etc.)
    var urlParams = new URLSearchParams(window.location.search);
    var requestedTab = urlParams.get('tab');
    if (requestedTab) {
        var mapped = requestedTab.replace('grade_', 'g');
        selectTab(mapped, false);
    } else {
        // Initial state: show all students
        applyCardFiltering();
    }
});

// Select Tab: Strict On-Click Filtering
function selectTab(tabKey, scroll) {
    if (scroll === undefined) scroll = false;
    activeTab = tabKey;

    // Update chips visual state
    var chips = document.querySelectorAll('.mob-chip');
    chips.forEach(function(chip) {
        var isThis = chip.getAttribute('data-tab') === tabKey;
        chip.classList.toggle('active', isThis);
        if (isThis && scroll) {
            chip.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    });

    var analyticsPanel = document.getElementById('analyticsPanel');
    var cardsFeed = document.getElementById('studentCardsFeed');

    // Update Feed Header Title
    var titleEl = document.getElementById('feedHeaderTitle');
    if (titleEl) {
        titleEl.innerHTML = tabLabels[tabKey] || 'Students List';
    }

    // Toggle analytics vs student cards feed
    if (tabKey === 'analytics') {
        if (analyticsPanel) analyticsPanel.style.display = 'block';
        if (cardsFeed) cardsFeed.style.display = 'none';
        var cntEl = document.getElementById('feedHeaderCount');
        if (cntEl) cntEl.textContent = 'Summary';
        var emptyEl = document.getElementById('feedEmptyState');
        if (emptyEl) emptyEl.style.display = 'none';
    } else {
        if (analyticsPanel) analyticsPanel.style.display = 'none';
        if (cardsFeed) cardsFeed.style.display = 'flex';
        applyCardFiltering();
    }

    if (scroll) {
        var toolbar = document.querySelector('.mob-search-toolbar');
        if (toolbar) toolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Filter Student Cards based on active tab and quick search query
function applyCardFiltering() {
    if (!activeTab || activeTab === 'analytics') return;

    var query = (document.getElementById('quickSearchInput').value || '').toLowerCase().trim();
    var cards = document.querySelectorAll('#studentCardsFeed .student-mob-card');
    var visibleCount = 0;

    cards.forEach(function(card) {
        var cardGrade = card.getAttribute('data-grade-cat') || ''; // 'g1', 'g2', 'g3', 'needs_help'
        var isLowAtt = card.getAttribute('data-low-att') === '1';
        var text = (card.getAttribute('data-search') || '').toLowerCase();

        // 1. Tab check
        var tabMatches = false;
        if (activeTab === 'all') {
            tabMatches = true;
        } else if (activeTab === 'low_att') {
            tabMatches = isLowAtt;
        } else if (activeTab === cardGrade) {
            tabMatches = true;
        }

        // 2. Search query check
        var searchMatches = true;
        if (query.length > 0 && text.indexOf(query) === -1) {
            searchMatches = false;
        }

        if (tabMatches && searchMatches) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    var countEl = document.getElementById('feedHeaderCount');
    if (countEl) countEl.textContent = visibleCount;

    var emptyEl = document.getElementById('feedEmptyState');
    if (emptyEl) {
        emptyEl.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}

// Snapshot Modal Helpers
function openMobSnapshot(id, name, adm, cls, father, grade, score, att, rank, mobile) {
    var modal = document.getElementById('mobSnapshotModal');
    if (!modal) return;

    document.getElementById('modalAvatar').textContent = (name || 'S').charAt(0).toUpperCase();
    document.getElementById('modalName').textContent = name || 'Student';
    document.getElementById('modalSub').textContent = 'ADM: ' + (adm || '-') + ' • ' + (cls || '-');
    document.getElementById('modalFather').textContent = 'Father: ' + (father || '-');
    document.getElementById('modalGrade').textContent = grade || '-';
    document.getElementById('modalScore').textContent = (score !== null && score !== undefined) ? score + '%' : 'N/A';
    document.getElementById('modalAtt').textContent = att || 'N/A';
    document.getElementById('modalRank').textContent = rank ? ('Rank #' + rank) : '-';
    document.getElementById('modalMobile').textContent = mobile || 'Not Provided';

    var waBtn = document.getElementById('modalWhatsappBtn');
    if (mobile) {
        var cleanMob = mobile.replace(/[^0-9]/g, '');
        waBtn.href = 'https://wa.me/91' + cleanMob;
        waBtn.style.display = 'inline-flex';
    } else {
        waBtn.style.display = 'none';
    }

    var fullBtn = document.getElementById('modalFullAnalyticsBtn');
    fullBtn.href = '{{ url("studentParticularPerformance") }}/' + id + '?layout=mobile';

    modal.classList.add('show');
}

function closeMobSnapshot(e) {
    if (e.target && e.target.id === 'mobSnapshotModal') {
        closeMobSnapshotDirect();
    }
}

function closeMobSnapshotDirect() {
    var modal = document.getElementById('mobSnapshotModal');
    if (modal) modal.classList.remove('show');
}
</script>
@endsection

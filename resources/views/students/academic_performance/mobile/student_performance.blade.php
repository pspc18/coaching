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
    
    // Calculate Grade Distribution Percentages
    $g1Pct = $studentCount > 0 ? round((($gradeArray['grade_1'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g2Pct = $studentCount > 0 ? round((($gradeArray['grade_2'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g3Pct = $studentCount > 0 ? round((($gradeArray['grade_3'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g4Pct = $studentCount > 0 ? round((($gradeArray['grade_4'] ?? 0) / $studentCount) * 100, 1) : 0;
    $g5Pct = $studentCount > 0 ? round((($gradeArray['grade_5'] ?? 0) / $studentCount) * 100, 1) : 0;

    // Topper Map (Top 3 ranks)
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

    // Categorize students by Tab for instant, clean rendering
    $studentsG1 = [];
    $studentsG2 = [];
    $studentsG3 = [];
    $studentsNeedsHelp = [];
    $studentsLowAtt = [];
    $lowAttCount = 0;

    foreach ($students as $st) {
        $m = $studentMetrics[$st->id] ?? [];
        $gLabel = $m['grade_label'] ?? 'Grade 5';
        $attStr = $m['attendance'] ?? 'N/A';
        $attVal = floatval(str_replace('%', '', $attStr));
        $isLowAtt = ($attStr !== 'N/A' && $attVal < 75);

        if ($isLowAtt) {
            $lowAttCount++;
            $studentsLowAtt[] = $st;
        }

        if (strpos($gLabel, '1') !== false) {
            $studentsG1[] = $st;
        } elseif (strpos($gLabel, '2') !== false) {
            $studentsG2[] = $st;
        } elseif (strpos($gLabel, '3') !== false) {
            $studentsG3[] = $st;
        } else {
            // Grade 4 or 5
            $studentsNeedsHelp[] = $st;
        }
    }

    $imageBase = rtrim((string) env('IMAGE_SHOW_PATH'), '/');
    $defaultPhoto = $imageBase ? $imageBase . '/default/user_image.jpg' : asset('assets/school/images/user.png');
@endphp

@section('title', 'Student Performance - Mobile')

@section('styles')
<style>
/* ==============================================================================
   ARISE ERP - NATIVE MOBILE STUDENT ACADEMIC PERFORMANCE (TABBED VIEW)
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

.mob-perf-container {
    padding: 0 0 70px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}
.mob-perf-container * {
    box-sizing: border-box;
}

/* 1. Hero Header Banner */
.mob-perf-hero {
    background: linear-gradient(135deg, #001428 0%, #002C54 55%, #08335c 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 8px 10px;
    color: #ffffff;
    box-shadow: 0 3px 12px rgba(0, 20, 40, 0.22);
    margin-bottom: 8px;
    position: relative;
    overflow: hidden;
}
.mob-perf-hero::after {
    content: '';
    position: absolute;
    right: -25px;
    bottom: -25px;
    width: 90px;
    height: 90px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
    pointer-events: none;
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 6px;
}
.mob-hero-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-hero-back {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    text-decoration: none !important;
    flex-shrink: 0;
}
.mob-hero-back:active {
    background: rgba(255, 255, 255, 0.25);
}
.mob-hero-title-box {
    min-width: 0;
}
.mob-hero-kicker {
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #38bdf8;
    font-weight: 700;
    display: block;
    line-height: 1;
    margin-bottom: 2px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-hero-btn-toggle {
    height: 26px;
    padding: 0 8px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all .15s ease;
}
.mob-hero-btn-toggle.has-filter {
    background: #0284c7;
    border-color: #38bdf8;
}

/* Hero Quick Stats */
.mob-hero-stats-row {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.mob-hero-chip {
    padding: 2px 6px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 10px;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-hero-chip b {
    font-weight: 800;
    color: #38bdf8;
}

/* 2. Collapsible Filter Drawer */
.mob-filter-box {
    display: none;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
.mob-filter-box.show {
    display: block;
    animation: fadeIn .18s ease;
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
    margin-top: 4px;
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

/* 3. KPI Quick-Trigger Strip (Clickable to switch tab) */
.mob-kpi-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    margin-bottom: 8px;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.mob-kpi-scroll::-webkit-scrollbar {
    display: none;
}
.mob-kpi-card {
    flex: 0 0 auto;
    min-width: 90px;
    padding: 6px 8px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    cursor: pointer;
    transition: all .15s ease;
    text-align: left;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.mob-kpi-card:active {
    transform: scale(0.97);
}
.mob-kpi-card.is-active {
    border-color: #002C54;
    background: #e0f2fe;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}
.mob-kpi-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 3px;
}
.mob-kpi-val {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-top: 2px;
}
.mob-kpi-card.kpi-g1 { border-left: 3px solid #10b981; }
.mob-kpi-card.kpi-g2 { border-left: 3px solid #2563eb; }
.mob-kpi-card.kpi-g3 { border-left: 3px solid #0284c7; }
.mob-kpi-card.kpi-help { border-left: 3px solid #f59e0b; }
.mob-kpi-card.kpi-low { border-left: 3px solid #dc2626; }
.mob-kpi-card.kpi-all { border-left: 3px solid #002C54; }

/* 4. Native Segmented Tab Bar */
.mob-tab-bar-wrap {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #f1f5f9;
    padding-top: 2px;
    padding-bottom: 6px;
    margin-bottom: 2px;
}
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2px;
    border-radius: 4px;
    gap: 2px;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.mob-seg-bar::-webkit-scrollbar {
    display: none;
}
.mob-seg-btn {
    flex: 0 0 auto;
    min-width: 82px;
    padding: 6px 8px;
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

/* 5. Prompt Placeholder (Visible until a tab is clicked) */
.mob-prompt-card {
    background: #ffffff;
    border: 1px dashed #94a3b8;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    margin-top: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-prompt-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    font-size: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}
.mob-prompt-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin: 0 0 4px 0;
}
.mob-prompt-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    margin: 0 0 14px 0;
}
.mob-prompt-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
}
.mob-prompt-pill-btn {
    padding: 8px 10px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: all .15s ease;
}
.mob-prompt-pill-btn:active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.mob-prompt-pill-btn span.badge-cnt {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 3px;
    background: #e2e8f0;
    color: #0f172a;
}
.mob-prompt-pill-btn:active span.badge-cnt {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* 6. Tab Content Panes (Strictly shown only on click) */
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

/* Pane Header Bar */
.mob-pane-header {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-pane-header-title {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}
.mob-pane-header-title span.title-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-pane-badge-count {
    padding: 1px 6px;
    border-radius: 3px;
    background: #002C54;
    color: #ffffff;
    font-size: 9.5px;
    font-weight: 800;
}
.mob-pane-close-btn {
    padding: 3px 7px;
    border-radius: 3px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #64748b;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}
.mob-pane-close-btn:active {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}

/* In-Tab Live Search Input */
.mob-in-tab-search-box {
    margin-bottom: 8px;
    position: relative;
}
.mob-in-tab-search-box input {
    width: 100%;
    height: 32px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    background: #ffffff;
    color: #0f172a;
}
.mob-in-tab-search-box input:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-in-tab-search-box i.search-icon {
    position: absolute;
    left: 9px;
    top: 9px;
    font-size: 12px;
    color: #94a3b8;
}
.mob-in-tab-search-box i.clear-icon {
    position: absolute;
    right: 8px;
    top: 8px;
    font-size: 13px;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    padding: 2px;
}

/* 7. Student Performance Cards */
.mob-student-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mob-student-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
    transition: all .15s ease;
}
.mob-student-card:active {
    background: #f8fafc;
}
.mob-student-card.is-topper-1 { border-left: 3px solid #f59e0b; }
.mob-student-card.is-topper-2 { border-left: 3px solid #94a3b8; }
.mob-student-card.is-topper-3 { border-left: 3px solid #b45309; }

.mob-st-main {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 6px;
}

/* Student Avatar */
.mob-st-avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
.mob-st-avatar {
    width: 38px;
    height: 38px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
}
.mob-st-avatar-fallback {
    width: 38px;
    height: 38px;
    border-radius: 4px;
    background: linear-gradient(135deg, #001428 0%, #002C54 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.mob-st-topper-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 11px;
    line-height: 1;
}

/* Student Info Box */
.mob-st-info {
    flex: 1;
    min-width: 0;
}
.mob-st-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.mob-st-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-st-meta-row {
    font-size: 10.5px;
    color: #475569;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-st-father {
    font-size: 10px;
    color: #64748b;
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Grade & Performance Badges */
.mob-st-grade-box {
    text-align: right;
    flex-shrink: 0;
}
.mob-grade-pill {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 800;
    line-height: 1.2;
}
.mob-grade-pill.g1 { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.mob-grade-pill.g2 { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.mob-grade-pill.g3 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
.mob-grade-pill.g4 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
.mob-grade-pill.g5 { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.mob-st-score {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 2px;
}

/* Mini Progress Bar */
.mob-st-progress-row {
    margin-bottom: 6px;
}
.mob-st-progress-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.mob-st-progress-fill {
    height: 100%;
    border-radius: 2px;
    transition: width .3s ease;
}
.mob-st-progress-fill.high { background: #10b981; }
.mob-st-progress-fill.mid { background: #0284c7; }
.mob-st-progress-fill.low { background: #f59e0b; }
.mob-st-progress-fill.poor { background: #ef4444; }

/* Attendance & Actions Toolbar */
.mob-st-action-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 5px;
    border-top: 1px dashed #e2e8f0;
}
.mob-st-att-badge {
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-st-att-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}
.mob-st-att-badge.good { color: #15803d; }
.mob-st-att-badge.good .mob-st-att-dot { background: #15803d; }
.mob-st-att-badge.low { color: #be123c; }
.mob-st-att-badge.low .mob-st-att-dot { background: #be123c; }
.mob-st-att-badge.na { color: #64748b; }
.mob-st-att-badge.na .mob-st-att-dot { background: #94a3b8; }

.mob-st-btn-group {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-btn-card-action {
    height: 24px;
    padding: 0 7px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
}
.mob-btn-card-action.btn-analytics {
    background: #002C54;
    color: #ffffff !important;
}
.mob-btn-card-action.btn-snapshot {
    background: #f1f5f9;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
}
.mob-icon-circle-btn {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
}
.mob-icon-circle-btn.whatsapp {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d;
}

/* Empty State Card inside Pane */
.mob-pane-empty-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 24px 16px;
    text-align: center;
    color: #64748b;
}
.mob-pane-empty-icon {
    font-size: 24px;
    color: #94a3b8;
    margin-bottom: 6px;
}
.mob-pane-empty-text {
    font-size: 11.5px;
    font-weight: 600;
    margin: 0;
}

/* 8. Analytics Pane Visuals */
.mob-analytics-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-analytics-card-title {
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
    transition: width .3s ease;
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
}
.mob-grade-legend-box span.dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}

/* 9. Quick Student Snapshot Modal */
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

    {{-- 1. HERO HEADER BANNER --}}
    <div class="mob-perf-hero">
        <div class="mob-hero-top">
            <div class="mob-hero-left">
                <a href="{{ url('home?layout=mobile') }}" class="mob-hero-back" title="Go to Home">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="mob-hero-title-box">
                    <span class="mob-hero-kicker">Academic Evaluation</span>
                    <h1 class="mob-hero-title">Student Performance</h1>
                </div>
            </div>
            <button type="button" class="mob-hero-btn-toggle {{ (!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name'])) ? 'has-filter' : '' }}" id="btnToggleFilterDrawer">
                <i class="fa fa-filter"></i> <span>Filter</span>
            </button>
        </div>

        <div class="mob-hero-stats-row">
            <span class="mob-hero-chip">
                <i class="fa fa-th-large"></i> {{ $selectedClass->name ?? 'All Classes' }}
            </span>
            <span class="mob-hero-chip">
                <i class="fa fa-users"></i> <b>{{ $studentCount }}</b> Students
            </span>
            <span class="mob-hero-chip">
                <i class="fa fa-calendar-check-o"></i> Avg Att: <b>{{ $averageAttendance !== null ? $averageAttendance . '%' : 'N/A' }}</b>
            </span>
        </div>
    </div>

    {{-- 2. COLLAPSIBLE FILTER DRAWER --}}
    <div class="mob-filter-box {{ (!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name'])) ? 'show' : '' }}" id="mobFilterDrawer">
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
                    <label class="mob-form-label">Search Keyword</label>
                    <input type="text" name="name" class="mob-form-control" placeholder="Student name, father name, mobile..." value="{{ $search['name'] ?? '' }}">
                </div>
            </div>

            <div class="mob-filter-actions">
                <button type="submit" class="mob-btn-apply">
                    <i class="fa fa-search"></i> Search Students
                </button>
                @if(!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']))
                    <a href="{{ url('student_performance?layout=mobile') }}" class="mob-btn-reset">
                        <i class="fa fa-refresh mr-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. QUICK KPI SUMMARY STRIP (Tap any card to activate that tab) --}}
    <div class="mob-kpi-scroll">
        <div class="mob-kpi-card kpi-g1" onclick="switchMobTab('pane-grade-1')">
            <span class="mob-kpi-lbl">🥇 Grade 1</span>
            <span class="mob-kpi-val text-success">{{ $gradeArray['grade_1'] ?? 0 }}</span>
        </div>
        <div class="mob-kpi-card kpi-g2" onclick="switchMobTab('pane-grade-2')">
            <span class="mob-kpi-lbl">🥈 Grade 2</span>
            <span class="mob-kpi-val text-primary">{{ $gradeArray['grade_2'] ?? 0 }}</span>
        </div>
        <div class="mob-kpi-card kpi-g3" onclick="switchMobTab('pane-grade-3')">
            <span class="mob-kpi-lbl">🔷 Grade 3</span>
            <span class="mob-kpi-val text-info">{{ $gradeArray['grade_3'] ?? 0 }}</span>
        </div>
        <div class="mob-kpi-card kpi-help" onclick="switchMobTab('pane-needs-help')">
            <span class="mob-kpi-lbl">⚠️ Needs Help</span>
            <span class="mob-kpi-val text-warning">{{ $needsImprovement }}</span>
        </div>
        <div class="mob-kpi-card kpi-low" onclick="switchMobTab('pane-low-att')">
            <span class="mob-kpi-lbl">📉 Low Att.</span>
            <span class="mob-kpi-val text-danger">{{ $lowAttCount }}</span>
        </div>
        <div class="mob-kpi-card kpi-all" onclick="switchMobTab('pane-all')">
            <span class="mob-kpi-lbl">👥 All Students</span>
            <span class="mob-kpi-val">{{ $studentCount }}</span>
        </div>
    </div>

    {{-- 4. NATIVE SEGMENTED TAB BAR --}}
    <div class="mob-tab-bar-wrap">
        <div class="mob-seg-bar" id="mobSegmentedBar">
            <button type="button" class="mob-seg-btn" data-tab="pane-grade-1" onclick="switchMobTab('pane-grade-1')">
                🥇 Grade 1 <span class="mob-seg-pill">{{ $gradeArray['grade_1'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-grade-2" onclick="switchMobTab('pane-grade-2')">
                🥈 Grade 2 <span class="mob-seg-pill">{{ $gradeArray['grade_2'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-grade-3" onclick="switchMobTab('pane-grade-3')">
                🔷 Grade 3 <span class="mob-seg-pill">{{ $gradeArray['grade_3'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-needs-help" onclick="switchMobTab('pane-needs-help')">
                ⚠️ Needs Help <span class="mob-seg-pill">{{ $needsImprovement }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-low-att" onclick="switchMobTab('pane-low-att')">
                📉 Low Att. <span class="mob-seg-pill">{{ $lowAttCount }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-all" onclick="switchMobTab('pane-all')">
                👥 All <span class="mob-seg-pill">{{ $studentCount }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-tab="pane-analytics" onclick="switchMobTab('pane-analytics')">
                📊 Analytics
            </button>
        </div>
    </div>

    {{-- 5. INITIAL PROMPT PLACEHOLDER (Content only shows when a tab is clicked) --}}
    <div class="mob-prompt-card" id="tabPromptPlaceholder">
        <div class="mob-prompt-icon">
            <i class="fa fa-hand-pointer-o"></i>
        </div>
        <h3 class="mob-prompt-title">Select a Performance Category</h3>
        <p class="mob-prompt-desc">
            Upar diye gaye kisi bhi tab ya category card par tap karein. Tab click hone par hi respective students ka data show hoga.
        </p>

        <div class="mob-prompt-grid">
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-grade-1')">
                <span>🥇 Grade 1 (91-100%)</span>
                <span class="badge-cnt">{{ $gradeArray['grade_1'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-grade-2')">
                <span>🥈 Grade 2 (81-90%)</span>
                <span class="badge-cnt">{{ $gradeArray['grade_2'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-grade-3')">
                <span>🔷 Grade 3 (71-80%)</span>
                <span class="badge-cnt">{{ $gradeArray['grade_3'] ?? 0 }}</span>
            </button>
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-needs-help')">
                <span>⚠️ Needs Help (G4/G5)</span>
                <span class="badge-cnt">{{ $needsImprovement }}</span>
            </button>
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-low-att')">
                <span>📉 Low Att. (&lt;75%)</span>
                <span class="badge-cnt">{{ $lowAttCount }}</span>
            </button>
            <button type="button" class="mob-prompt-pill-btn" onclick="switchMobTab('pane-all')">
                <span>👥 All Students</span>
                <span class="badge-cnt">{{ $studentCount }}</span>
            </button>
        </div>
    </div>

    {{-- 6. TAB CONTENT PANES --}}

    {{-- TAB 1: GRADE 1 STUDENTS --}}
    <div class="mob-pane" id="pane-grade-1">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-trophy text-warning"></i>
                <span class="title-text">Grade 1 Students (91% - 100%)</span>
                <span class="mob-pane-badge-count">{{ count($studentsG1) }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if(count($studentsG1) > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search in Grade 1..." oninput="filterTabCards(this, 'pane-grade-1')">
            </div>
            <div class="mob-student-list">
                @foreach($studentsG1 as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-info-circle"></i></div>
                <p class="mob-pane-empty-text">No students currently in Grade 1 (91% - 100%).</p>
            </div>
        @endif
    </div>

    {{-- TAB 2: GRADE 2 STUDENTS --}}
    <div class="mob-pane" id="pane-grade-2">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-star text-primary"></i>
                <span class="title-text">Grade 2 Students (81% - 90.99%)</span>
                <span class="mob-pane-badge-count">{{ count($studentsG2) }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if(count($studentsG2) > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search in Grade 2..." oninput="filterTabCards(this, 'pane-grade-2')">
            </div>
            <div class="mob-student-list">
                @foreach($studentsG2 as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-info-circle"></i></div>
                <p class="mob-pane-empty-text">No students currently in Grade 2 (81% - 90.99%).</p>
            </div>
        @endif
    </div>

    {{-- TAB 3: GRADE 3 STUDENTS --}}
    <div class="mob-pane" id="pane-grade-3">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-bookmark text-info"></i>
                <span class="title-text">Grade 3 Students (71% - 80.99%)</span>
                <span class="mob-pane-badge-count">{{ count($studentsG3) }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if(count($studentsG3) > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search in Grade 3..." oninput="filterTabCards(this, 'pane-grade-3')">
            </div>
            <div class="mob-student-list">
                @foreach($studentsG3 as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-info-circle"></i></div>
                <p class="mob-pane-empty-text">No students currently in Grade 3 (71% - 80.99%).</p>
            </div>
        @endif
    </div>

    {{-- TAB 4: NEEDS HELP (GRADE 4 & 5) --}}
    <div class="mob-pane" id="pane-needs-help">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-exclamation-triangle text-warning"></i>
                <span class="title-text">Needs Help (Grade 4 &amp; 5)</span>
                <span class="mob-pane-badge-count" style="background: #d97706;">{{ count($studentsNeedsHelp) }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if(count($studentsNeedsHelp) > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search in Needs Help..." oninput="filterTabCards(this, 'pane-needs-help')">
            </div>
            <div class="mob-student-list">
                @foreach($studentsNeedsHelp as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-check-circle text-success"></i></div>
                <p class="mob-pane-empty-text">Great news! No students currently require remediation in Grade 4/5.</p>
            </div>
        @endif
    </div>

    {{-- TAB 5: LOW ATTENDANCE (<75%) --}}
    <div class="mob-pane" id="pane-low-att">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-calendar-times-o text-danger"></i>
                <span class="title-text">Low Attendance (&lt; 75%)</span>
                <span class="mob-pane-badge-count" style="background: #dc2626;">{{ count($studentsLowAtt) }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if(count($studentsLowAtt) > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search in Low Attendance..." oninput="filterTabCards(this, 'pane-low-att')">
            </div>
            <div class="mob-student-list">
                @foreach($studentsLowAtt as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-check-circle text-success"></i></div>
                <p class="mob-pane-empty-text">Excellent! All evaluated students have attendance 75% or higher.</p>
            </div>
        @endif
    </div>

    {{-- TAB 6: ALL STUDENTS --}}
    <div class="mob-pane" id="pane-all">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-users text-primary"></i>
                <span class="title-text">All Students Performance</span>
                <span class="mob-pane-badge-count">{{ $studentCount }}</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        @if($studentCount > 0)
            <div class="mob-in-tab-search-box">
                <i class="fa fa-search search-icon"></i>
                <input type="search" placeholder="Search all students (Name, Adm, Father)..." oninput="filterTabCards(this, 'pane-all')">
            </div>
            <div class="mob-student-list">
                @foreach($students as $st)
                    @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                @endforeach
            </div>
        @else
            <div class="mob-pane-empty-card">
                <div class="mob-pane-empty-icon"><i class="fa fa-users text-muted"></i></div>
                <p class="mob-pane-empty-text">No students found matching current filter criteria.</p>
            </div>
        @endif
    </div>

    {{-- TAB 7: VISUAL ANALYTICS & OVERVIEW --}}
    <div class="mob-pane" id="pane-analytics">
        <div class="mob-pane-header">
            <div class="mob-pane-header-title">
                <i class="fa fa-line-chart text-primary"></i>
                <span class="title-text">Academic Analytics &amp; KPIs</span>
            </div>
            <button type="button" class="mob-pane-close-btn" onclick="closeMobTab()">
                <i class="fa fa-times"></i> Close Tab
            </button>
        </div>

        {{-- Grade Distribution Card --}}
        <div class="mob-analytics-card">
            <h4 class="mob-analytics-card-title">
                <i class="fa fa-pie-chart text-primary"></i> Grade Split Breakdown
            </h4>

            @if($studentCount > 0)
                <div class="mob-grade-bar-split">
                    @if($g1Pct > 0)<div class="mob-grade-seg seg-g1" style="width: {{ $g1Pct }}%;" title="Grade 1: {{ $g1Pct }}%"></div>@endif
                    @if($g2Pct > 0)<div class="mob-grade-seg seg-g2" style="width: {{ $g2Pct }}%;" title="Grade 2: {{ $g2Pct }}%"></div>@endif
                    @if($g3Pct > 0)<div class="mob-grade-seg seg-g3" style="width: {{ $g3Pct }}%;" title="Grade 3: {{ $g3Pct }}%"></div>@endif
                    @if($g4Pct > 0)<div class="mob-grade-seg seg-g4" style="width: {{ $g4Pct }}%;" title="Grade 4: {{ $g4Pct }}%"></div>@endif
                    @if($g5Pct > 0)<div class="mob-grade-seg seg-g5" style="width: {{ $g5Pct }}%;" title="Grade 5: {{ $g5Pct }}%"></div>@endif
                </div>

                <div class="mob-grade-legend-grid">
                    <div class="mob-grade-legend-box" onclick="switchMobTab('pane-grade-1')">
                        <span><span class="dot" style="background:#16a34a;"></span> Grade 1</span>
                        <b>{{ $g1Pct }}% ({{ $gradeArray['grade_1'] ?? 0 }})</b>
                    </div>
                    <div class="mob-grade-legend-box" onclick="switchMobTab('pane-grade-2')">
                        <span><span class="dot" style="background:#2563eb;"></span> Grade 2</span>
                        <b>{{ $g2Pct }}% ({{ $gradeArray['grade_2'] ?? 0 }})</b>
                    </div>
                    <div class="mob-grade-legend-box" onclick="switchMobTab('pane-grade-3')">
                        <span><span class="dot" style="background:#0891b2;"></span> Grade 3</span>
                        <b>{{ $g3Pct }}% ({{ $gradeArray['grade_3'] ?? 0 }})</b>
                    </div>
                    <div class="mob-grade-legend-box" onclick="switchMobTab('pane-needs-help')">
                        <span><span class="dot" style="background:#d97706;"></span> Grade 4 &amp; 5</span>
                        <b>{{ round($g4Pct + $g5Pct, 1) }}% ({{ $needsImprovement }})</b>
                    </div>
                </div>
            @else
                <p class="text-muted" style="font-size: 11px; margin: 0;">No student data available to compute grade distribution.</p>
            @endif
        </div>

        {{-- Top Performers Leaderboard Card --}}
        <div class="mob-analytics-card">
            <h4 class="mob-analytics-card-title">
                <i class="fa fa-trophy text-warning"></i> Class Toppers (Top 3)
            </h4>

            @if($scoredStudents->filter(fn($s) => $s->temp_score > 0)->count() > 0)
                <div class="mob-student-list">
                    @foreach($scoredStudents->filter(fn($s) => $s->temp_score > 0)->take(3) as $st)
                        @include('students.academic_performance.mobile.partials.student_card', ['student' => $st])
                    @endforeach
                </div>
            @else
                <p class="text-muted" style="font-size: 11px; margin: 0;">No scored exams recorded yet for class ranking.</p>
            @endif
        </div>
    </div>

</div>

{{-- 7. QUICK STUDENT SNAPSHOT MODAL --}}
<div class="mob-modal-overlay" id="mobSnapshotModal" onclick="closeMobSnapshot(event)">
    <div class="mob-modal-card" onclick="event.stopPropagation()">
        <div class="mob-modal-header">
            <span><i class="fa fa-id-card-o mr-1"></i> Student Academic Snapshot</span>
            <button type="button" style="background:none; border:none; color:#ffffff; font-size:16px; cursor:pointer;" onclick="closeMobSnapshotDirect()">&times;</button>
        </div>
        <div class="mob-modal-body">
            <div class="mob-modal-student-row">
                <div class="mob-modal-avatar" id="modalAvatar">S</div>
                <div style="min-width:0;">
                    <h4 style="margin: 0; font-size: 13.5px; font-weight: 800; color: #002C54;" id="modalName">Student Name</h4>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;" id="modalSub">ADM: 1001 &bull; Class 10th</div>
                    <div style="font-size: 10.5px; color: #475569; margin-top: 1px;" id="modalFather">Father: -</div>
                </div>
            </div>

            <div class="mob-modal-grid">
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Grade</div>
                    <div class="mob-modal-stat-val text-primary" id="modalGrade">Grade 1</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Average Score</div>
                    <div class="mob-modal-stat-val text-success" id="modalScore">95.0%</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Attendance</div>
                    <div class="mob-modal-stat-val" id="modalAtt">88.5%</div>
                </div>
                <div class="mob-modal-stat-box">
                    <div class="mob-modal-stat-lbl">Topper Rank</div>
                    <div class="mob-modal-stat-val text-warning" id="modalRank">-</div>
                </div>
            </div>

            <div id="modalContactWrap" style="background:#f1f5f9; padding:6px 10px; border-radius:3px; font-size:11px; color:#334155; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa fa-phone text-primary mr-1"></i> <b id="modalMobile">-</b></span>
                <a href="#" id="modalWhatsappBtn" target="_blank" class="mob-icon-circle-btn whatsapp" style="width:auto; padding:2px 8px; font-weight:700;">
                    <i class="fa fa-whatsapp mr-1"></i> WhatsApp
                </a>
            </div>
        </div>
        <div class="mob-modal-footer">
            <button type="button" class="mob-btn-card-action btn-snapshot" onclick="closeMobSnapshotDirect()">Close</button>
            <a href="#" id="modalFullAnalyticsBtn" class="mob-btn-card-action btn-analytics">
                <i class="fa fa-external-link mr-1"></i> Full Analytics
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Toggle Filter Drawer
    var btnToggle = document.getElementById('btnToggleFilterDrawer');
    var filterBox = document.getElementById('mobFilterDrawer');
    if (btnToggle && filterBox) {
        btnToggle.addEventListener('click', function() {
            filterBox.classList.toggle('show');
            btnToggle.classList.toggle('has-filter', filterBox.classList.contains('show'));
        });
    }

    // 2. Check URL for direct tab selection (?tab=grade_1, ?tab=all, etc.)
    var urlParams = new URLSearchParams(window.location.search);
    var requestedTab = urlParams.get('tab');
    if (requestedTab) {
        var paneMap = {
            'grade_1': 'pane-grade-1',
            'grade_2': 'pane-grade-2',
            'grade_3': 'pane-grade-3',
            'needs_help': 'pane-needs-help',
            'low_att': 'pane-low-att',
            'all': 'pane-all',
            'analytics': 'pane-analytics'
        };
        var targetPane = paneMap[requestedTab] || requestedTab;
        if (document.getElementById(targetPane)) {
            switchMobTab(targetPane);
        }
    }
});

// 3. Tab Switching Function: Content ONLY shows when tab is clicked
function switchMobTab(targetPaneId) {
    var placeholder = document.getElementById('tabPromptPlaceholder');
    var tabBtns = document.querySelectorAll('.mob-seg-btn');
    var kpiCards = document.querySelectorAll('.mob-kpi-card');
    var panes = document.querySelectorAll('.mob-pane');
    var targetPane = document.getElementById(targetPaneId);

    if (!targetPane) return;

    // Hide initial placeholder
    if (placeholder) {
        placeholder.style.display = 'none';
    }

    // Deactivate all tabs and panes
    tabBtns.forEach(function(b) { b.classList.remove('active'); });
    kpiCards.forEach(function(c) { c.classList.remove('is-active'); });
    panes.forEach(function(p) { p.classList.remove('active'); });

    // Activate selected button in segmented bar
    var activeBtn = document.querySelector('.mob-seg-btn[data-tab="' + targetPaneId + '"]');
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    // Activate corresponding KPI card if applicable
    var kpiClassMap = {
        'pane-grade-1': 'kpi-g1',
        'pane-grade-2': 'kpi-g2',
        'pane-grade-3': 'kpi-g3',
        'pane-needs-help': 'kpi-help',
        'pane-low-att': 'kpi-low',
        'pane-all': 'kpi-all'
    };
    if (kpiClassMap[targetPaneId]) {
        var activeKpi = document.querySelector('.' + kpiClassMap[targetPaneId]);
        if (activeKpi) activeKpi.classList.add('is-active');
    }

    // Show target pane
    targetPane.classList.add('active');

    // Smooth scroll down slightly to the pane
    var barWrap = document.querySelector('.mob-tab-bar-wrap');
    if (barWrap) {
        barWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// 4. Close Active Tab (Return to clean prompt state)
function closeMobTab() {
    var placeholder = document.getElementById('tabPromptPlaceholder');
    var tabBtns = document.querySelectorAll('.mob-seg-btn');
    var kpiCards = document.querySelectorAll('.mob-kpi-card');
    var panes = document.querySelectorAll('.mob-pane');

    tabBtns.forEach(function(b) { b.classList.remove('active'); });
    kpiCards.forEach(function(c) { c.classList.remove('is-active'); });
    panes.forEach(function(p) { p.classList.remove('active'); });

    if (placeholder) {
        placeholder.style.display = 'block';
    }
}

// 5. In-Tab Live Search Filter
function filterTabCards(inputEl, paneId) {
    var query = (inputEl.value || '').toLowerCase().trim();
    var pane = document.getElementById(paneId);
    if (!pane) return;

    var cards = pane.querySelectorAll('.mob-student-card');
    cards.forEach(function(card) {
        var text = (card.getAttribute('data-search') || '').toLowerCase();
        if (query === '' || text.indexOf(query) !== -1) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// 6. Quick Snapshot Modal Logic
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

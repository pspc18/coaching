@php
    $getUser = Helper::getUser();
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
    $currentSessionName = Session::get('session_name') ?? date('Y');

    $formatMarks = function ($value) {
        if (!is_numeric($value)) return $value;
        $formatted = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    };

    $calculateGrade = function ($pct) {
        if ($pct >= 85) return 'A+';
        if ($pct >= 75) return 'A';
        if ($pct >= 60) return 'B';
        if ($pct >= 50) return 'C';
        if ($pct >= 33) return 'D';
        return 'E';
    };

    $calculateStatus = function ($pct) {
        if ($pct >= 75) return ['label' => 'Distinction', 'border' => '#16a34a', 'color' => '#16a34a', 'bg' => '#dcfce7'];
        if ($pct >= 60) return ['label' => '1st Division', 'border' => '#0284c7', 'color' => '#0284c7', 'bg' => '#e0f2fe'];
        if ($pct >= 33) return ['label' => 'Passed', 'border' => '#f59e0b', 'color' => '#d97706', 'bg' => '#fef3c7'];
        return ['label' => 'Needs Work', 'border' => '#f43f5e', 'color' => '#e11d48', 'bg' => '#ffe4e6'];
    };

    $reportPercentages = $examReports->map(function ($report) {
        return (float) ($report->result['percentage'] ?? 0);
    });

    $averagePercentage = $reportPercentages->count() ? round($reportPercentages->avg(), 1) : 0;
    $bestPercentage = $reportPercentages->count() ? round($reportPercentages->max(), 1) : 0;

    $ranks = $examReports->map(function ($report) {
        $r = $report->single_subject_mode ? ($report->result['subject_rank'] ?? null) : ($report->result['overall_rank'] ?? null);
        return is_numeric($r) ? (int)$r : null;
    })->filter();
    $bestRank = $ranks->count() ? $ranks->min() : null;

    $distinctionCount = $examReports->filter(fn($r) => (float)($r->result['percentage'] ?? 0) >= 75)->count();
    $firstClassCount = $examReports->filter(fn($r) => (float)($r->result['percentage'] ?? 0) >= 60 && (float)($r->result['percentage'] ?? 0) < 75)->count();
    $passedCount = $examReports->filter(fn($r) => (float)($r->result['percentage'] ?? 0) >= 33 && (float)($r->result['percentage'] ?? 0) < 60)->count();
@endphp

@extends('student_login.layout.app')

@section('title', 'Exam Results')
@section('page_title', 'EXAM RESULTS')
@section('page_sub', Session::get('first_name') . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE EXAM RESULTS
   Exact Layout, Typography & CSS matching admissionView?layout=mobile
   ========================================================================== */

/* 1. Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
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

/* 4-Column Metrics Glance (2x2 Grid) */
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.mob-radar-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-radar-tag {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-radar-val {
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.mob-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 2. Compact Search & Filter Toolbar with Sticky Support */
.mob-tracker-container {
    margin-bottom: 8px;
    position: relative;
}
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all .2s ease;
}

/* Sticky Fixed Toolbar when Scrolled Past */
.mob-search-toolbar.is-fixed {
    position: fixed !important;
    top: 52px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: calc(100% - 16px) !important;
    max-width: 584px !important;
    z-index: 990 !important;
    background: rgba(0, 44, 84, 0.96) !important;
    border-color: rgba(56, 189, 248, 0.35) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    box-shadow: 0 6px 20px rgba(0, 20, 40, 0.35) !important;
}

.mob-search-row {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: border-color .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear-btn {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0;
    font-size: 12px;
}
.mob-results-badge {
    height: 30px;
    padding: 0 9px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

/* Category Filter Tabs Bar */
.mob-filter-tabs-row {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-x;
    scrollbar-width: none;
    padding-bottom: 1px;
}
.mob-filter-tabs-row::-webkit-scrollbar {
    display: none;
}
.mob-filter-btn {
    padding: 4px 9px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .15s ease;
    cursor: pointer;
}
.mob-filter-btn .count-tag {
    font-size: 9px;
    background: #e2e8f0;
    padding: 1px 5px;
    border-radius: 3px;
    color: #334155;
}
.mob-filter-btn.active-filter {
    background: #002C54 !important;
    color: #ffffff !important;
    border-color: #002C54 !important;
}
.mob-filter-btn.active-filter .count-tag {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Fixed state adjustments for toolbar */
.mob-search-toolbar.is-fixed .mob-search-row {
    margin-bottom: 5px;
}
.mob-search-toolbar.is-fixed .mob-search-input {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.mob-search-toolbar.is-fixed .mob-search-input::placeholder {
    color: #94a3b8;
}
.mob-search-toolbar.is-fixed .mob-results-badge {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #38bdf8;
}
.mob-search-toolbar.is-fixed .mob-filter-btn {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.2);
    color: #cbd5e1;
}
.mob-search-toolbar.is-fixed .mob-filter-btn .count-tag {
    background: rgba(255, 255, 255, 0.15);
    color: #cbd5e1;
}
.mob-search-toolbar.is-fixed .mob-filter-btn.active-filter {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.mob-search-toolbar.is-fixed .mob-filter-btn.active-filter .count-tag {
    background: rgba(255, 255, 255, 0.3) !important;
    color: #ffffff !important;
}

/* 3. Feed List & Exam Card Item */
.student-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 24px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}

/* Card Header */
.exam-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
}
.exam-title-cluster {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    min-width: 0;
    flex: 1;
}
.exam-icon-circle {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    background: linear-gradient(135deg, #001833, #002C54);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    border: 1px solid rgba(56, 189, 248, 0.3);
}
.exam-title-text {
    min-width: 0;
    flex: 1;
}
.exam-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    margin: 0 0 3px;
    word-break: break-word;
}
.exam-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    flex-wrap: wrap;
}
.exam-meta-row span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.exam-meta-row i {
    color: #0284c7;
    font-size: 10px;
}

/* Score Pill Badge */
.exam-score-badge {
    text-align: right;
    flex-shrink: 0;
}
.score-pill {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 13.5px;
    font-weight: 800;
    line-height: 1.1;
    display: inline-block;
    border: 1px solid;
}
.score-pill-sub {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .02em;
    margin-top: 2px;
    display: block;
}

/* 4. Score Ribbon Grid */
.exam-metrics-ribbon {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.ribbon-item {
    text-align: center;
}
.ribbon-item:not(:last-child) {
    border-right: 1px solid #e2e8f0;
}
.ribbon-label {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: block;
    line-height: 1.2;
}
.ribbon-value {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-top: 1px;
}
.ribbon-value.highlight-green {
    color: #16a34a;
}
.ribbon-value.highlight-blue {
    color: #0284c7;
}

/* Visual Progress Bar */
.exam-progress-wrap {
    margin-bottom: 9px;
}
.progress-bar-track {
    height: 5px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width .4s ease;
}

/* Action Strip */
.exam-action-strip {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-toggle-subjects {
    flex: 1;
    height: 30px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .15s ease;
}
.btn-toggle-subjects:active {
    background: #e2e8f0;
}
.btn-toggle-subjects i.chevron-icon {
    font-size: 10px;
    transition: transform .2s ease;
}
.student-mob-card.is-expanded .btn-toggle-subjects i.chevron-icon {
    transform: rotate(180deg);
}
.student-mob-card.is-expanded .btn-toggle-subjects {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 5. Expandable Subject Breakdown */
.exam-detail-drawer {
    display: none;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #cbd5e1;
    animation: drawerFadeIn .2s ease;
}
.student-mob-card.is-expanded .exam-detail-drawer {
    display: block;
}
@keyframes drawerFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.drawer-section-title {
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin: 0 0 7px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.subject-item-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 7px 9px;
    margin-bottom: 6px;
}
.subject-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.subject-name-cluster {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    flex: 1;
}
.subject-index-tag {
    font-size: 9px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 5px;
    border-radius: 3px;
    flex-shrink: 0;
}
.subject-name-text {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.subject-marks-badge {
    text-align: right;
    flex-shrink: 0;
}
.subject-marks-val {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
}
.subject-marks-max {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
}

/* Component Breakdown Pills */
.subject-components-row {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    margin-top: 5px;
}
.component-pill {
    font-size: 9px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 1px 6px;
    color: #475569;
    font-weight: 600;
}
.component-pill b {
    color: #002C54;
    font-weight: 800;
}

/* Mini Subject Progress */
.subject-progress-track {
    height: 3px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 5px;
}
.subject-progress-fill {
    height: 100%;
    border-radius: 2px;
}

/* Empty State Card */
.mob-empty-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 30px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin: 0 auto 10px;
}
.mob-empty-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 4px;
}
.mob-empty-sub {
    font-size: 11px;
    color: #64748b;
    line-height: 1.45;
    margin: 0 auto;
    max-width: 280px;
}
</style>
@endsection

@section('content')
<div class="native-dashboard-container">

    {{-- 1. Glassmorphic Navy Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-trophy text-warning"></i>
                <span>Academic Results</span>
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-graduation-cap"></i> {{ $currentSessionName }}
            </span>
        </div>

        <div class="mob-dual-grid">
            {{-- Metric 1: Overall Average --}}
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-pie-chart text-info"></i> Overall Avg
                </span>
                <div class="mob-radar-val" style="color: {{ $averagePercentage >= 60 ? '#38bdf8' : '#f59e0b' }};">
                    {{ $formatMarks($averagePercentage) }}%
                </div>
                <span class="mob-radar-meta">Grade {{ $calculateGrade($averagePercentage) }} &bull; Performance</span>
            </div>

            {{-- Metric 2: Best Score --}}
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-star text-warning"></i> Best Score
                </span>
                <div class="mob-radar-val" style="color: #10b981;">
                    {{ $formatMarks($bestPercentage) }}%
                </div>
                <span class="mob-radar-meta">Top Exam Performance</span>
            </div>

            {{-- Metric 3: Published Exams --}}
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-file-text-o text-warning"></i> Total Exams
                </span>
                <div class="mob-radar-val">
                    {{ $examReports->count() }}
                </div>
                <span class="mob-radar-meta">{{ $examReports->count() ? 'Results Published' : 'None Published' }}</span>
            </div>

            {{-- Metric 4: Best Class Rank --}}
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-trophy text-warning"></i> Best Rank
                </span>
                <div class="mob-radar-val" style="color: {{ $bestRank ? '#38bdf8' : '#cbd5e1' }};">
                    {{ $bestRank ? '#' . $bestRank : '—' }}
                </div>
                <span class="mob-radar-meta">{{ $bestRank ? 'In Class Standing' : 'Standing N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Compact Search & Filter Toolbar with Sticky Support --}}
    <div class="mob-tracker-container" id="examStickyTracker">
        <div class="mob-search-toolbar" id="examSearchToolbar">
            <div class="mob-search-row">
                <div class="mob-search-input-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text"
                           id="examSearchInput"
                           class="mob-search-input"
                           placeholder="Search exam or subject..."
                           autocomplete="off">
                    <button type="button" id="examClearBtn" class="mob-search-clear-btn" style="display:none;">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>
                <span class="mob-results-badge" id="examCountDisplay">
                    <i class="fa fa-file-text-o"></i> <span id="visibleExamCount">{{ $examReports->count() }}</span>
                </span>
            </div>

            <div class="mob-filter-tabs-row" id="examFilterTabs">
                <button type="button" class="mob-filter-btn active-filter" data-filter="all">
                    <span>All</span>
                    <span class="count-tag">{{ $examReports->count() }}</span>
                </button>
                @if($distinctionCount > 0)
                <button type="button" class="mob-filter-btn" data-filter="distinction">
                    <i class="fa fa-circle" style="color:#16a34a;font-size:7px;"></i>
                    <span>75%+ Distinction</span>
                    <span class="count-tag">{{ $distinctionCount }}</span>
                </button>
                @endif
                @if($firstClassCount > 0)
                <button type="button" class="mob-filter-btn" data-filter="first">
                    <i class="fa fa-circle" style="color:#0284c7;font-size:7px;"></i>
                    <span>60%+ 1st Div</span>
                    <span class="count-tag">{{ $firstClassCount }}</span>
                </button>
                @endif
                @if($passedCount > 0)
                <button type="button" class="mob-filter-btn" data-filter="passed">
                    <i class="fa fa-circle" style="color:#f59e0b;font-size:7px;"></i>
                    <span>Passed</span>
                    <span class="count-tag">{{ $passedCount }}</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. Feed List of Exam Result Cards --}}
    <div class="student-feed-list" id="examFeedList">
        @forelse($examReports as $report)
            @php
                $result = $report->result;
                $rank = $report->single_subject_mode ? ($result['subject_rank'] ?? null) : ($result['overall_rank'] ?? null);
                $examDate = !empty($report->assigned_exam_date) ? \Carbon\Carbon::parse($report->assigned_exam_date)->format('d M Y') : null;
                $obtained = (float) ($result['total_obtained'] ?? 0);
                $maximum = (float) ($result['total_maximum'] ?? 0);
                $percentage = (float) ($result['percentage'] ?? ($maximum > 0 ? ($obtained / $maximum) * 100 : 0));
                $subjects = $result['subject_rows'] ?? [];

                $statusInfo = $calculateStatus($percentage);
                $filterCategory = 'passed';
                if ($percentage >= 75) {
                    $filterCategory = 'distinction';
                } elseif ($percentage >= 60) {
                    $filterCategory = 'first';
                } elseif ($percentage < 33) {
                    $filterCategory = 'fail';
                }

                $subjectNamesConcat = collect($subjects)->pluck('name')->implode(' ');
                $topperMarks = (float) ($report->summary['topper_score'] ?? 0);
                $totalStudents = (int) ($report->summary['total_students'] ?? 0);
            @endphp

            <article class="student-mob-card exam-card-item"
                     data-exam-name="{{ strtolower($report->exam->name ?? '') }}"
                     data-subjects="{{ strtolower($subjectNamesConcat) }}"
                     data-category="{{ $filterCategory }}"
                     style="border-left: 3px solid {{ $statusInfo['border'] }};">

                {{-- Card Header --}}
                <div class="exam-card-header">
                    <div class="exam-title-cluster">
                        <div class="exam-icon-circle">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                        <div class="exam-title-text">
                            <h3 class="exam-title">{{ $report->exam->name ?? 'Examination' }}</h3>
                            <div class="exam-meta-row">
                                <span><i class="fa fa-calendar-o"></i> {{ $examDate ?? 'Date not set' }}</span>
                                <span><i class="fa fa-book"></i> {{ count($subjects) }} Subjects</span>
                            </div>
                        </div>
                    </div>

                    {{-- Score Pill Badge --}}
                    <div class="exam-score-badge">
                        <span class="score-pill" style="background: {{ $statusInfo['bg'] }}; color: {{ $statusInfo['color'] }}; border-color: {{ $statusInfo['border'] }};">
                            {{ $formatMarks($percentage) }}%
                        </span>
                        <span class="score-pill-sub" style="color: {{ $statusInfo['color'] }};">
                            {{ $statusInfo['label'] }}
                        </span>
                    </div>
                </div>

                {{-- Key Performance Ribbon --}}
                <div class="exam-metrics-ribbon">
                    <div class="ribbon-item">
                        <span class="ribbon-label">Obtained</span>
                        <span class="ribbon-value highlight-green">{{ $formatMarks($obtained) }} <span style="font-size:9.5px;color:#64748b;">/ {{ $formatMarks($maximum) }}</span></span>
                    </div>
                    <div class="ribbon-item">
                        <span class="ribbon-label">Class Rank</span>
                        <span class="ribbon-value highlight-blue">#{{ $rank ?: '—' }} <span style="font-size:9.5px;color:#64748b;">/ {{ $totalStudents ?: '—' }}</span></span>
                    </div>
                    <div class="ribbon-item">
                        <span class="ribbon-label">Topper</span>
                        <span class="ribbon-value">{{ $formatMarks($topperMarks) }}</span>
                    </div>
                </div>

                {{-- Visual Progress Bar --}}
                <div class="exam-progress-wrap">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill"
                             style="width: {{ min(100, max(0, $percentage)) }}%; background: {{ $statusInfo['border'] }};">
                        </div>
                    </div>
                </div>

                {{-- Action Strip --}}
                <div class="exam-action-strip">
                    <button type="button" class="btn-toggle-subjects" aria-expanded="false">
                        <span>Subject Marks ({{ count($subjects) }})</span>
                        <i class="fa fa-chevron-down chevron-icon"></i>
                    </button>
                </div>

                {{-- Expandable Subject Details --}}
                <div class="exam-detail-drawer">
                    <div class="drawer-section-title">
                        <span><i class="fa fa-list-ol mr-1"></i> Subject-Wise Breakdown</span>
                        <span>Marks / Max</span>
                    </div>

                    <div class="subject-list-container">
                        @foreach($subjects as $subject)
                            @php
                                $subMax = (float) ($subject['maximum_marks'] ?? 0);
                                $subNumeric = (float) ($subject['numeric_marks'] ?? 0);
                                $subPercentage = $subMax > 0 && is_numeric($subject['display_marks'] ?? null)
                                    ? round(($subNumeric / $subMax) * 100, 1)
                                    : null;
                                $subColor = $subPercentage !== null && $subPercentage >= 60 ? '#16a34a' : ($subPercentage !== null && $subPercentage >= 33 ? '#0284c7' : '#e11d48');
                            @endphp

                            <div class="subject-item-card">
                                <div class="subject-top-row">
                                    <div class="subject-name-cluster">
                                        <span class="subject-index-tag">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="subject-name-text">{{ $subject['name'] }}</span>
                                    </div>
                                    <div class="subject-marks-badge">
                                        <span class="subject-marks-val" style="color: {{ $subColor }};">{{ $subject['display_marks'] }}</span>
                                        <span class="subject-marks-max">/ {{ $formatMarks($subMax) }}</span>
                                    </div>
                                </div>

                                {{-- Optional Component Breakdown (Written, Oral, Lab) --}}
                                @php
                                    $components = array_filter([
                                        'Written' => $subject['w_marks'] ?? null,
                                        'Oral/Practical' => $subject['r_marks'] ?? null,
                                        'Lab' => $subject['l_marks'] ?? null,
                                    ], fn($v) => $v !== null && $v !== '');
                                @endphp

                                @if(!empty($components))
                                    <div class="subject-components-row">
                                        @foreach($components as $cLabel => $cVal)
                                            <span class="component-pill">{{ $cLabel }}: <b>{{ $cVal }}</b></span>
                                        @endforeach
                                    </div>
                                @endif

                                @if($subPercentage !== null)
                                    <div class="subject-progress-track">
                                        <div class="subject-progress-fill" style="width: {{ min(100, max(0, $subPercentage)) }}%; background: {{ $subColor }};"></div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </article>
        @empty
            <div class="mob-empty-card">
                <div class="mob-empty-icon">
                    <i class="fa fa-trophy"></i>
                </div>
                <h3 class="mob-empty-title">Results Not Published Yet</h3>
                <p class="mob-empty-sub">
                    Official exam results will appear here as soon as marks are entered and published by the administration.
                </p>
            </div>
        @endforelse

        {{-- Client-side Filter No Matches Fallback --}}
        <div class="mob-empty-card" id="noFilterMatchesCard" style="display: none;">
            <div class="mob-empty-icon">
                <i class="fa fa-search"></i>
            </div>
            <h3 class="mob-empty-title">No Matching Results</h3>
            <p class="mob-empty-sub">
                No examinations matched your search criteria. Try a different term or clear the filter.
            </p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sticky Toolbar Tracker on Scroll
    var tracker = document.getElementById('examStickyTracker');
    var toolbar = document.getElementById('examSearchToolbar');
    var isFixed = false;

    function handleScroll() {
        if (!tracker || !toolbar) return;
        var rect = tracker.getBoundingClientRect();
        // 52px top bar offset in native app layout
        if (rect.top <= 52 && !isFixed) {
            toolbar.classList.add('is-fixed');
            isFixed = true;
        } else if (rect.top > 52 && isFixed) {
            toolbar.classList.remove('is-fixed');
            isFixed = false;
        }
    }
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // 2. Expand/Collapse Subject Breakdown
    document.querySelectorAll('.btn-toggle-subjects').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var card = this.closest('.student-mob-card');
            if (!card) return;
            var isExpanded = card.classList.contains('is-expanded');
            card.classList.toggle('is-expanded', !isExpanded);
            this.setAttribute('aria-expanded', !isExpanded ? 'true' : 'false');
        });
    });

    // 3. Fast Client-side Search and Filter Tabs
    var searchInput = document.getElementById('examSearchInput');
    var clearBtn = document.getElementById('examClearBtn');
    var filterBtns = document.querySelectorAll('.mob-filter-btn');
    var examCards = document.querySelectorAll('.exam-card-item');
    var noMatchesCard = document.getElementById('noFilterMatchesCard');
    var countDisplay = document.getElementById('visibleExamCount');

    var currentFilter = 'all';
    var currentSearch = '';

    function applyFilters() {
        var visibleCount = 0;

        examCards.forEach(function (card) {
            var name = card.getAttribute('data-exam-name') || '';
            var subs = card.getAttribute('data-subjects') || '';
            var cat = card.getAttribute('data-category') || '';

            var matchesFilter = (currentFilter === 'all') || (cat === currentFilter);
            var matchesSearch = true;

            if (currentSearch.trim().length > 0) {
                var q = currentSearch.toLowerCase().trim();
                matchesSearch = (name.indexOf(q) !== -1) || (subs.indexOf(q) !== -1);
            }

            if (matchesFilter && matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) {
            countDisplay.textContent = visibleCount;
        }

        if (noMatchesCard) {
            noMatchesCard.style.display = (visibleCount === 0 && examCards.length > 0) ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value;
            if (clearBtn) {
                clearBtn.style.display = this.value.length > 0 ? 'block' : 'none';
            }
            applyFilters();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (searchInput) {
                searchInput.value = '';
                currentSearch = '';
                this.style.display = 'none';
                searchInput.focus();
                applyFilters();
            }
        });
    }

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) { b.classList.remove('active-filter'); });
            this.classList.add('active-filter');
            currentFilter = this.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });
});
</script>
@endsection

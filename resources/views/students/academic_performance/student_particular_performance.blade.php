@extends('layout.app')

@php
    $studentName = trim(($data->first_name ?? '') . ' ' . ($data->last_name ?? ''));
    $initial = strtoupper(substr($studentName ?: 'S', 0, 1));
    $profileImage = !empty($data->image) ? asset('upload/profile/' . $data->image) : '';
    $examCount = count($examList ?? []);
    $subjectCount = count($subjectList ?? []);
    $hasMarks = ($totalMaxAll ?? 0) > 0;
    $admissionNo = $data->admissionNo ?: 'N/A';
    $className = $class->name ?? 'Not Assigned';
@endphp

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - STUDENT ACADEMIC PERFORMANCE & ANALYTICS THEME
   - Signature Dark Navy Theme (#002C54 -> #0f3460)
   - Viewport Adaptive Container with Fluid High-DPI Card Layout
   - Interactive Chart.js Trajectory Curve
   - Subject Mastery Breakdown & KPI Score Cards
   - Sticky Dual-Header Marks Matrix Table
   - Competitive Exam Badging (Right, Wrong, Left Breakdown)
   - Print-Optimized Report Sheet Formatting
   ========================================================================== */

:root {
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
    --sky-soft: #e0f2fe;
    --emerald-primary: #10b981;
    --emerald-soft: #ecfdf5;
    --rose-primary: #f43f5e;
    --amber-primary: #f59e0b;
    --slate-border: #cbd5e1;
}

.perf-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding-bottom: 24px;
}
.perf-page * {
    box-sizing: border-box;
}

/* 1. Hero Header Banner */
.perf-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.18);
    margin-bottom: 8px;
}
.perf-hero-main {
    display: flex;
    flex-direction: column;
}
.perf-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #38bdf8;
    font-weight: 700;
    margin-bottom: 2px;
}
.perf-title {
    font-size: 15px;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
}
.perf-subtitle {
    font-size: 11px;
    margin: 2px 0 0;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* Hero Stat Badges */
.perf-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.hero-stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 8px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 3px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-chip b {
    font-weight: 700;
}
.hero-stat-chip.badge-sky {
    background: rgba(56, 189, 248, 0.2);
    border-color: rgba(56, 189, 248, 0.4);
    color: #e0f2fe;
}
.hero-stat-chip.badge-emerald {
    background: rgba(16, 185, 129, 0.22);
    border-color: rgba(16, 185, 129, 0.45);
    color: #d1fae5;
}
.hero-stat-chip.badge-amber {
    background: rgba(251, 191, 36, 0.2);
    border-color: rgba(251, 191, 36, 0.4);
    color: #fef3c7;
}
.hero-stat-chip.badge-rose {
    background: rgba(244, 63, 94, 0.22);
    border-color: rgba(244, 63, 94, 0.45);
    color: #ffe4e6;
}

/* Action Buttons */
.perf-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 28px;
    line-height: 1;
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
    background: rgba(255, 255, 255, 0.22);
    border-color: #ffffff;
}

/* 2. KPI Score Cards Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
    margin-bottom: 8px;
}
.kpi-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: transform .12s, box-shadow .12s;
}
.kpi-card:hover {
    box-shadow: 0 3px 8px rgba(0, 44, 84, 0.08);
}
.kpi-icon {
    width: 36px;
    height: 36px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.kpi-icon.icon-blue { background: #e0f2fe; color: #0284c7; }
.kpi-icon.icon-green { background: #dcfce7; color: #16a34a; }
.kpi-icon.icon-purple { background: #f3e8ff; color: #9333ea; }
.kpi-icon.icon-amber { background: #fef3c7; color: #d97706; }
.kpi-icon.icon-rose { background: #ffe4e6; color: #e11d48; }

.kpi-details {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.kpi-title {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1px;
}
.kpi-value {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.kpi-meta {
    font-size: 9.5px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3. Main Split Row: Left Student Profile Card + Right Trajectory & Mastery */
.perf-workspace-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}
.student-bio-panel {
    width: 310px;
    min-width: 280px;
    max-width: 330px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.analytics-panel {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Standard Box Card */
.perf-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    overflow: hidden;
}
.perf-card-header {
    background: #002342;
    color: #ffffff;
    padding: 6px 12px;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255,255,255,.12);
}
.perf-card-header.header-light {
    background: #f8fafc;
    color: #002C54;
    border-bottom: 1px solid #e2e8f0;
}
.perf-card-title {
    margin: 0;
    font-size: 11.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}
.perf-card-body {
    padding: 10px 12px;
}

/* Student Bio Card Styling */
.student-bio-hero {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 10px;
}
.perf-student-avatar {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #002C54;
    box-shadow: 0 2px 6px rgba(0,0,0,.12);
}
.perf-student-avatar-initial {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff;
    font-size: 22px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0,0,0,.15);
    flex-shrink: 0;
}
.student-bio-title h3 {
    margin: 0 0 2px;
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
}
.student-adm-badge {
    display: inline-block;
    padding: 1px 6px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
}

.bio-info-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.bio-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 11px;
}
.bio-info-item:last-child {
    border-bottom: none;
}
.bio-label {
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}
.bio-label i {
    width: 14px;
    color: #0284c7;
    text-align: center;
}
.bio-value {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
    max-width: 170px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Stream Subjects Pill Wrap */
.stream-pills-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 6px;
}
.stream-pill {
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
}
.stream-pill i {
    color: #0284c7;
}

/* 4. Subject Mastery Breakdown Progress Bars */
.subject-mastery-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mastery-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
}
.mastery-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
    font-size: 11px;
}
.mastery-name {
    font-weight: 700;
    color: #002C54;
}
.mastery-score {
    font-weight: 700;
    color: #0f172a;
}
.mastery-progress-bar {
    height: 7px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}
.mastery-progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width .5s ease-in-out;
}
.fill-emerald { background: #10b981; }
.fill-sky { background: #0284c7; }
.fill-amber { background: #f59e0b; }
.fill-rose { background: #ef4444; }

.mastery-meta {
    display: flex;
    justify-content: space-between;
    font-size: 9.5px;
    color: #64748b;
    margin-top: 3px;
}

/* 5. Exam Performance Matrix Table */
.perf-table-wrap {
    overflow-x: auto;
    background: #ffffff;
}
.perf-matrix-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11px;
}
.perf-matrix-table thead th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 6px 8px;
    font-size: 10.5px;
    font-weight: 700;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    vertical-align: middle;
}
.perf-matrix-table tbody tr {
    transition: background .1s ease;
}
.perf-matrix-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.perf-matrix-table tbody tr:hover {
    background: #f1f5f9;
}
.perf-matrix-table tbody td {
    padding: 6px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #e2e8f0;
    color: #1e293b;
}
.perf-matrix-table tbody td:last-child {
    border-right: none;
}

/* Score Pills inside Table */
.mark-cell-card {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.mark-score-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
}
.mark-pct-badge {
    display: inline-block;
    padding: 1px 5px;
    border-radius: 2px;
    font-size: 9.5px;
    font-weight: 700;
}
.mark-rwl-strip {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-top: 2px;
}
.rwl-tag {
    font-size: 8.5px;
    font-weight: 700;
    padding: 0 3px;
    border-radius: 2px;
    line-height: 1.2;
}
.rwl-r { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.rwl-w { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.rwl-l { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* 6. Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 40px 16px;
    text-align: center;
    background: #ffffff;
    color: #475569;
}
.empty-icon {
    font-size: 44px;
    color: #0284c7;
    margin-bottom: 10px;
}
.empty-title {
    font-size: 15px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
}
.empty-desc {
    font-size: 11.5px;
    color: #64748b;
    max-width: 460px;
    line-height: 1.4;
    margin-bottom: 14px;
}

/* 7. Print Styling */
@media print {
    body {
        background: #ffffff !important;
        font-size: 10pt;
    }
    .main-header, .main-sidebar, .perf-hero-actions, .kpi-grid, #examChartContainer, .content-header {
        display: none !important;
    }
    .content-wrapper, .perf-page {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }
    .perf-hero {
        background: none !important;
        color: #000000 !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 0 10px !important;
        border-bottom: 2px solid #000000 !important;
    }
    .perf-kicker { color: #555555 !important; }
    .perf-title { color: #000000 !important; font-size: 18pt !important; }
    .perf-subtitle { color: #333333 !important; }
    .hero-stat-chip {
        color: #000000 !important;
        background: none !important;
        border: 1px solid #888888 !important;
    }
    .perf-card {
        border: 1px solid #888888 !important;
        box-shadow: none !important;
    }
    .perf-card-header {
        background: #f0f0f0 !important;
        color: #000000 !important;
        border-bottom: 1px solid #888888 !important;
    }
    .perf-matrix-table thead th {
        background: #e5e5e5 !important;
        color: #000000 !important;
        border-bottom: 1px solid #000000 !important;
    }
}

@media (max-width: 991px) {
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .perf-workspace-row {
        flex-direction: column;
    }
    .student-bio-panel {
        width: 100%;
        max-width: 100%;
    }
}
</style>

<div class="content-wrapper perf-page">
    <div class="container-fluid p-2">

        {{-- 1. Signature Hero Header Banner --}}
        <div class="perf-hero">
            <div class="perf-hero-main">
                <span class="perf-kicker">Academic Performance &bull; Student Evaluation Profile</span>
                <h1 class="perf-title">
                    <i class="fa fa-graduation-cap text-warning"></i>
                    <span>{{ $studentName ?: 'Student Performance' }}</span>
                </h1>
                <div class="perf-subtitle">
                    <span><i class="fa fa-id-card-o text-info"></i> Adm No: <b>{{ $admissionNo }}</b></span>
                    <span>&bull;</span>
                    <span><i class="fa fa-th-large text-warning"></i> Class: <b>{{ $className }}</b></span>
                    @if(!empty($data->mobile))
                        <span>&bull;</span>
                        <span><i class="fa fa-phone text-success"></i> Mobile: {{ $data->mobile }}</span>
                    @endif
                </div>
            </div>

            <div class="perf-hero-stats">
                <div class="hero-stat-chip badge-sky">
                    <i class="fa fa-book"></i> Exams: <b>{{ $examCount }}</b>
                </div>
                <div class="hero-stat-chip badge-emerald">
                    <i class="fa fa-tags"></i> Subjects: <b>{{ $subjectCount }}</b>
                </div>
                <div class="hero-stat-chip badge-amber">
                    <i class="fa fa-calendar-check-o"></i> Attendance: <b>{{ $attendance }}</b>
                </div>
                @if($overallPercentage !== null)
                    <div class="hero-stat-chip badge-rose">
                        <i class="fa fa-percent"></i> Score: <b>{{ $overallPercentage }}%</b>
                    </div>
                @endif
            </div>

            <div class="perf-hero-actions">
                <a href="{{ url('student_performance') }}" class="dash-btn dash-btn-outline" title="Back to All Students Performance">
                    <i class="fa fa-arrow-left"></i> All Students
                </a>
                <button type="button" class="dash-btn dash-btn-light" onclick="window.print()" title="Print Student Performance Report">
                    <i class="fa fa-print text-primary"></i> Print Report
                </button>
            </div>
        </div>

        {{-- 2. KPI Score Cards Grid --}}
        <div class="kpi-grid">
            {{-- KPI 1: Overall Percentage --}}
            <div class="kpi-card">
                <div class="kpi-icon icon-blue">
                    <i class="fa fa-percent"></i>
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Overall Score</span>
                    <span class="kpi-value text-primary">
                        {{ $overallPercentage !== null ? $overallPercentage . '%' : 'N/A' }}
                    </span>
                    <span class="kpi-meta">
                        <span class="badge {{ $performanceBadgeClass }}" style="font-size: 9px; padding: 1px 4px;">{{ $performanceGrade }}</span>
                    </span>
                </div>
            </div>

            {{-- KPI 2: Total Marks Obtained --}}
            <div class="kpi-card">
                <div class="kpi-icon icon-green">
                    <i class="fa fa-trophy"></i>
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Marks Obtained</span>
                    <span class="kpi-value text-success">
                        {{ $hasMarks ? number_format($totalObtainedAll, 1) : '0' }}
                    </span>
                    <span class="kpi-meta">
                        Out of {{ $hasMarks ? number_format($totalMaxAll, 0) : '0' }} Max
                    </span>
                </div>
            </div>

            {{-- KPI 3: Total Exams Attempted --}}
            <div class="kpi-card">
                <div class="kpi-icon icon-purple">
                    <i class="fa fa-file-text-o"></i>
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Exams Evaluated</span>
                    <span class="kpi-value">
                        {{ count($chartPercentages) }}
                    </span>
                    <span class="kpi-meta">
                        Of {{ $examCount }} Class Exams
                    </span>
                </div>
            </div>

            {{-- KPI 4: Attendance --}}
            <div class="kpi-card">
                <div class="kpi-icon icon-amber">
                    <i class="fa fa-calendar-check-o"></i>
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Attendance</span>
                    <span class="kpi-value text-warning">
                        {{ $attendance }}
                    </span>
                    <span class="kpi-meta">
                        @if($totalAttDays > 0)
                            {{ $presentDays }} / {{ $totalAttDays }} Days
                        @else
                            Session {{ date('Y') }}
                        @endif
                    </span>
                </div>
            </div>

            {{-- KPI 5: Strongest Subject --}}
            <div class="kpi-card">
                <div class="kpi-icon icon-rose">
                    <i class="fa fa-star"></i>
                </div>
                <div class="kpi-details">
                    <span class="kpi-title">Top Subject</span>
                    <span class="kpi-value" style="font-size: 13px; color: #e11d48;" title="{{ $bestSubject ?? 'N/A' }}">
                        {{ $bestSubject ? Str::limit($bestSubject, 16) : 'N/A' }}
                    </span>
                    <span class="kpi-meta">
                        @if($weakSubject)
                            Focus: {{ Str::limit($weakSubject, 14) }}
                        @else
                            Based on all tests
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- 3. Workspace Row: Left Student Profile + Right Analytics --}}
        <div class="perf-workspace-row">

            {{-- Left Column: Student Bio & Subject Mastery Card --}}
            <div class="student-bio-panel">

                {{-- Student Information Card --}}
                <div class="perf-card">
                    <div class="perf-card-header">
                        <div class="perf-card-title">
                            <i class="fa fa-user-circle"></i> Student Particulars
                        </div>
                        <span class="badge badge-light" style="font-size: 9px; color: #002C54;">{{ $admissionNo }}</span>
                    </div>
                    <div class="perf-card-body">
                        <div class="student-bio-hero">
                            @if(!empty($profileImage))
                                <img src="{{ $profileImage }}" alt="{{ $studentName }}" class="perf-student-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="perf-student-avatar-initial" style="display: none;">{{ $initial }}</div>
                            @else
                                <div class="perf-student-avatar-initial">{{ $initial }}</div>
                            @endif
                            <div class="student-bio-title">
                                <h3>{{ $studentName ?: 'Unnamed' }}</h3>
                                <span class="student-adm-badge">Adm No: {{ $admissionNo }}</span>
                                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $className }}</div>
                            </div>
                        </div>

                        <div class="bio-info-list">
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-user"></i> Father's Name</span>
                                <span class="bio-value">{{ $data->father_name ?: '-' }}</span>
                            </div>
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-female"></i> Mother's Name</span>
                                <span class="bio-value">{{ $data->mother_name ?: '-' }}</span>
                            </div>
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-phone"></i> Student Mobile</span>
                                <span class="bio-value">{{ $data->mobile ?: '-' }}</span>
                            </div>
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-phone-square"></i> Father Mobile</span>
                                <span class="bio-value">{{ $data->father_mobile ?: '-' }}</span>
                            </div>
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-birthday-cake"></i> Date of Birth</span>
                                <span class="bio-value">{{ !empty($data->dob) ? date('d-m-Y', strtotime($data->dob)) : '-' }}</span>
                            </div>
                            <div class="bio-info-item">
                                <span class="bio-label"><i class="fa fa-map-marker"></i> City / Address</span>
                                <span class="bio-value" title="{{ $data->address ?? '' }}">{{ $data->address ? Str::limit($data->address, 20) : '-' }}</span>
                            </div>
                        </div>

                        {{-- Stream / Optional Subjects --}}
                        <div style="margin-top: 10px; padding-top: 8px; border-top: 1px solid #e2e8f0;">
                            <span style="font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase;">Mapped Stream Subjects:</span>
                            <div class="stream-pills-wrap">
                                @forelse($subjectList as $sub)
                                    <span class="stream-pill">
                                        <i class="fa fa-book"></i> {{ $sub->name }}
                                    </span>
                                @empty
                                    <span class="text-muted" style="font-size: 10px;">No specific stream subjects mapped</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Subject Mastery Breakdown Card --}}
                @if(!empty($subjectTotals))
                    <div class="perf-card">
                        <div class="perf-card-header header-light">
                            <div class="perf-card-title text-dark">
                                <i class="fa fa-bar-chart text-primary"></i> Subject Mastery
                            </div>
                            <span style="font-size: 9.5px; color: #64748b;">Cumulative Avg</span>
                        </div>
                        <div class="perf-card-body p-2">
                            <div class="subject-mastery-list">
                                @foreach($subjectTotals as $subId => $sMeta)
                                    @php
                                        $sPct = $sMeta['percentage'] ?? 0;
                                        $fillColor = $sPct >= 70 ? 'fill-emerald' : ($sPct >= 50 ? 'fill-sky' : ($sPct >= 35 ? 'fill-amber' : 'fill-rose'));
                                    @endphp
                                    <div class="mastery-item">
                                        <div class="mastery-header">
                                            <span class="mastery-name">{{ $sMeta['name'] }}</span>
                                            <span class="mastery-score">{{ $sPct }}%</span>
                                        </div>
                                        <div class="mastery-progress-bar">
                                            <div class="mastery-progress-fill {{ $fillColor }}" style="width: {{ min(100, $sPct) }}%;"></div>
                                        </div>
                                        <div class="mastery-meta">
                                            <span>Score: {{ $sMeta['obtained'] }} / {{ $sMeta['max'] }}</span>
                                            <span>Tests: {{ $sMeta['attempts'] }}</span>
                                            <span>Best: {{ $sMeta['highest'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Right Column: Interactive Progression Curve + Exam Matrix Table --}}
            <div class="analytics-panel">

                {{-- Performance Trend Curve Chart (Chart.js) --}}
                @if(count($chartPercentages) > 1)
                    <div class="perf-card" id="examChartContainer">
                        <div class="perf-card-header header-light">
                            <div class="perf-card-title text-dark">
                                <i class="fa fa-line-chart text-success"></i> Score Progression Trajectory
                            </div>
                            <span style="font-size: 10px; color: #64748b;">Chronological Exam Score %</span>
                        </div>
                        <div class="perf-card-body" style="height: 190px; padding: 6px 12px; position: relative;">
                            <canvas id="studentScoreChart"></canvas>
                        </div>
                    </div>
                @endif

                {{-- Comprehensive Exam Performance Matrix Table Card --}}
                <div class="perf-card" style="flex: 1; display: flex; flex-direction: column;">
                    <div class="perf-card-header">
                        <div class="perf-card-title">
                            <i class="fa fa-table text-warning"></i> Examination Performance Matrix
                        </div>
                        <div style="font-size: 10px; color: #cbd5e1;">
                            Class: {{ $className }} &bull; Total Exams: {{ $examCount }}
                        </div>
                    </div>

                    @if($hasMarks && count($examList) > 0)
                        <div class="perf-table-wrap">
                            <table class="perf-matrix-table" id="performancePrintTable">
                                <thead>
                                    <tr>
                                        <th style="width: 35px; text-align: center;">#</th>
                                        <th style="min-width: 170px;">Examination Name</th>
                                        <th style="width: 90px; text-align: center;">Exam Date</th>
                                        @foreach($subjectList as $subject)
                                            <th style="min-width: 130px; text-align: center;">{{ $subject->name }}</th>
                                        @endforeach
                                        <th style="width: 110px; text-align: center;">Total Obtained</th>
                                        <th style="width: 80px; text-align: center;">Overall %</th>
                                        <th style="width: 95px; text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $index = 1; @endphp
                                    @foreach($examList as $examId => $exam)
                                        @php
                                            $summary = $examSummaries[$examId] ?? ['obtained' => 0, 'max' => 0, 'percentage' => null, 'sub_attempts' => 0];
                                            $examPct = $summary['percentage'];
                                            $statusBadge = 'badge-secondary';
                                            $statusText = 'Pending';
                                            if ($examPct !== null) {
                                                if ($examPct >= 75) { $statusBadge = 'badge-success'; $statusText = 'Distinction'; }
                                                elseif ($examPct >= 60) { $statusBadge = 'badge-primary'; $statusText = '1st Class'; }
                                                elseif ($examPct >= 50) { $statusBadge = 'badge-warning'; $statusText = '2nd Class'; }
                                                elseif ($examPct >= 35) { $statusBadge = 'badge-info'; $statusText = 'Pass'; }
                                                else { $statusBadge = 'badge-danger'; $statusText = 'Needs Focus'; }
                                            }
                                        @endphp
                                        <tr>
                                            <td style="text-align: center; font-weight: 700; color: #64748b;">
                                                {{ $index++ }}
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #002C54; line-height: 1.2;">
                                                    {{ $exam->name }}
                                                </div>
                                                @if(!empty($exam->description))
                                                    <div style="font-size: 9.5px; color: #64748b;">{{ Str::limit($exam->description, 35) }}</div>
                                                @endif
                                            </td>
                                            <td style="text-align: center; font-size: 10.5px; color: #475569;">
                                                {{ $summary['date'] }}
                                            </td>

                                            {{-- Subject Marks Columns --}}
                                            @foreach($subjectList as $subId => $subject)
                                                @php
                                                    $cell = $performanceMatrix[$examId][$subId] ?? ['student_marks' => null, 'max_marks' => 0, 'percentage' => null, 'r_marks' => null, 'w_marks' => null, 'l_marks' => null];
                                                    $obtained = $cell['student_marks'];
                                                    $max = $cell['max_marks'];
                                                    $pct = $cell['percentage'];
                                                @endphp
                                                <td style="text-align: center;">
                                                    @if($obtained !== null)
                                                        <div class="mark-cell-card">
                                                            <div class="mark-score-pill" style="justify-content: center;">
                                                                <b>{{ $obtained }}</b>
                                                                <span style="color: #64748b; font-weight: normal; font-size: 10px;">/ {{ $max }}</span>
                                                            </div>
                                                            <div>
                                                                @php
                                                                    $pctBadgeColor = $pct >= 70 ? 'background:#dcfce7; color:#15803d;' : ($pct >= 50 ? 'background:#e0f2fe; color:#0369a1;' : ($pct >= 35 ? 'background:#fef3c7; color:#b45309;' : 'background:#fee2e2; color:#b91c1c;'));
                                                                @endphp
                                                                <span class="mark-pct-badge" style="{{ $pctBadgeColor }}">
                                                                    {{ $pct !== null ? $pct . '%' : 'N/A' }}
                                                                </span>
                                                            </div>
                                                            {{-- Competitive Right / Wrong / Left Breakdown --}}
                                                            @if($cell['r_marks'] !== null || $cell['w_marks'] !== null || $cell['l_marks'] !== null)
                                                                <div class="mark-rwl-strip" style="justify-content: center;">
                                                                    @if($cell['r_marks'] !== null)
                                                                        <span class="rwl-tag rwl-r" title="Right Answers">R:{{ $cell['r_marks'] }}</span>
                                                                    @endif
                                                                    @if($cell['w_marks'] !== null)
                                                                        <span class="rwl-tag rwl-w" title="Wrong Answers">W:{{ $cell['w_marks'] }}</span>
                                                                    @endif
                                                                    @if($cell['l_marks'] !== null)
                                                                        <span class="rwl-tag rwl-l" title="Unattempted">L:{{ $cell['l_marks'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted" style="font-size: 10px;">-</span>
                                                    @endif
                                                </td>
                                            @endforeach

                                            {{-- Total Obtained / Max --}}
                                            <td style="text-align: center;">
                                                @if($summary['sub_attempts'] > 0)
                                                    <span style="font-weight: 800; color: #002C54; font-size: 11.5px;">
                                                        {{ number_format($summary['obtained'], 1) }}
                                                    </span>
                                                    <span style="font-size: 10px; color: #64748b;">/ {{ number_format($summary['max'], 0) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Overall Exam % --}}
                                            <td style="text-align: center;">
                                                @if($examPct !== null)
                                                    <span style="font-weight: 800; font-size: 11.5px; color: {{ $examPct >= 50 ? '#15803d' : '#b91c1c' }};">
                                                        {{ $examPct }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Status --}}
                                            <td style="text-align: center;">
                                                <span class="badge {{ $statusBadge }}" style="font-size: 9.5px; padding: 2px 6px;">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Centered Graceful Empty State (Perfect for Student 1448) --}}
                        <div class="dash-empty-state">
                            <div class="empty-icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <div class="empty-title">
                                No Exam Performance Records Found
                            </div>
                            <div class="empty-desc">
                                There are currently no examination evaluation records found for <b>{{ $studentName }}</b> in class <b>"{{ $className }}"</b>. Marks have either not been uploaded yet or examinations are pending.
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;">
                                <a href="{{ url('fill_marks') }}" class="btn btn-xs btn-primary font-weight-bold px-3">
                                    <i class="fa fa-pencil-square-o mr-1"></i> Enter Exam Marks
                                </a>
                                <a href="{{ url('fill-marks-by-excel') }}" class="btn btn-xs btn-outline-primary px-3">
                                    <i class="fa fa-file-excel-o mr-1"></i> Import via Excel
                                </a>
                                <a href="{{ url('student_performance') }}" class="btn btn-xs btn-secondary px-3">
                                    <i class="fa fa-users mr-1"></i> All Students Performance
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>

{{-- Chart.js Script --}}
@if(count($chartPercentages) > 1)
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var canvas = document.getElementById('studentScoreChart');
    if (!canvas) return;

    var ctx = canvas.getContext('2d');
    var chartLabels = @json($chartLabels);
    var chartData = @json($chartPercentages);

    var gradient = ctx.createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, 'rgba(2, 132, 199, 0.28)');
    gradient.addColorStop(1, 'rgba(2, 132, 199, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Score Percentage (%)',
                data: chartData,
                borderColor: '#0284c7',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#002C54',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4.5,
                pointHoverRadius: 6.5
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
                        callback: function(value) { return value + '%'; },
                        font: { size: 10 }
                    },
                    grid: {
                        color: 'rgba(203, 213, 225, 0.4)'
                    }
                },
                x: {
                    ticks: {
                        font: { size: 10 }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Percentage: ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
@endif
@endsection

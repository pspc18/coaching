@php
    $permission = Helper::permissioncheck(10);
    $currentRoleId = (int) Session::get('role_id');
    $isStudent = ($currentRoleId === 3);
    $currentSessionName = Session::get('session_name') ?? 'Current Session';

    $hwTitle = $homework->title ?? 'Homework Submissions';
    $hwClass = $homework->ClassType->name ?? 'N/A';
    $hwSubject = $homework->Subject->name ?? 'N/A';

    $hwTeacher = 'Admin';
    if (!empty($homework->Teacher)) {
        $hwTeacher = trim(($homework->Teacher->first_name ?? '') . ' ' . ($homework->Teacher->last_name ?? ''));
    } elseif (!empty($homework->User)) {
        $hwTeacher = trim(($homework->User->first_name ?? '') . ' ' . ($homework->User->last_name ?? ''));
    }
    if (empty($hwTeacher)) {
        $hwTeacher = 'Admin';
    }

    $hwIssueDate = !empty($homework->homework_issue_date) ? date('d M Y', strtotime($homework->homework_issue_date)) : '-';
    $hwDueDate = !empty($homework->submission_date) ? date('d M Y', strtotime($homework->submission_date)) : '-';
    $today = date('Y-m-d');
    $subDateRaw = !empty($homework->submission_date) ? date('Y-m-d', strtotime($homework->submission_date)) : '';
    $isOverdue = (!empty($subDateRaw) && $subDateRaw < $today);
    $isDueToday = (!empty($subDateRaw) && $subDateRaw === $today);

    // Parse homework type / tag
    $rawTitle = $hwTitle;
    $type = 'DPP';
    $displayTitle = $rawTitle;
    if (preg_match('/^\[(.*?)\]\s*(.*)$/', $rawTitle, $matches)) {
        $type = trim($matches[1]);
        $displayTitle = trim($matches[2]);
    }

    $badgeClass = 'bg-primary text-white';
    if ($type === 'DPP') $badgeClass = 'bg-primary text-white';
    elseif ($type === 'Worksheet') $badgeClass = 'bg-info text-white';
    elseif ($type === 'PYQ Sheet') $badgeClass = 'bg-warning text-dark';
    elseif ($type === 'Subjective') $badgeClass = 'bg-success text-white';
    elseif ($type === 'Revision') $badgeClass = 'bg-secondary text-white';

    $totalStudents = count($students ?? []);
    $checkedCount = $statsSummary['checked'] ?? 0;
    $pendingCount = $statsSummary['pending'] ?? 0;
    $totalAttempts = $statsSummary['total_attempts'] ?? 0;
    $encodedHwDesc = base64_encode($homework->description ?? '');
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE HOMEWORK SUBMISSIONS & EVALUATION DIRECTORY
   - Aligned with Arise ERP Mobile Design System (homework/index, admissionView, expenseView)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Glassmorphic Hero Card with Live Evaluation KPI Metrics
   - Quick Filter Chips & Live Client Search Bar
   - Student Submission Feed Cards with Status Indicators & 1-Tap Review Sheet
   - Native Slide-Up Detail Sheet & Fullscreen Evaluation Modal Workspace
   - Bulk Fast-Action Bottom Dock
   ========================================================================== */

/* 1. Signature Glassmorphic Hero Card */
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
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.mob-hero-badge-left {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-type-badge {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.mob-status-badge {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
}
.mob-session-pill {
    font-size: 9px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    white-space: nowrap;
}

/* Hero Assignment Title (Clean slide expand/collapse with interactive chevron toggle) */
.mob-hero-content-wrap {
    margin-bottom: 7px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-hero-title-box {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    cursor: pointer;
}
.mob-hero-title-left {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    min-width: 0;
    flex: 1;
}
.mob-hero-title-icon {
    font-size: 13px;
    color: #38bdf8;
    margin-top: 2px;
    flex-shrink: 0;
}
.mob-hero-title-text {
    font-size: 12px;
    font-weight: 800;
    line-height: 1.4;
    color: #ffffff;
    max-height: 34px;
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    word-break: break-word;
}
.mob-hero-title-text.expanded {
    max-height: 400px;
}
.mob-hero-expand-btn {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    flex-shrink: 0;
    transition: transform 0.25s ease, background 0.15s ease;
    padding: 0;
}
.mob-hero-expand-btn:active {
    transform: scale(0.92);
}
.mob-hero-expand-btn.expanded i {
    transform: rotate(180deg);
}
.mob-hero-expand-btn i {
    transition: transform 0.25s ease;
}

/* Secondary Meta Chips Strip */
.mob-hero-meta-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
    margin-bottom: 7px;
}
.mob-hero-meta-strip::-webkit-scrollbar {
    display: none;
}
.mob-meta-chip {
    font-size: 9.5px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.09);
    border: 1px solid rgba(255, 255, 255, 0.16);
    color: #e2e8f0;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-meta-chip strong {
    color: #ffffff;
}
.mob-meta-chip.chip-due {
    background: rgba(254, 240, 138, 0.15);
    border-color: rgba(254, 240, 138, 0.35);
    color: #fef08a;
}
.mob-meta-chip.chip-due strong {
    color: #facc15;
}

/* 3 Metrics Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
    transition: background .15s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-metric-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-total { color: #38bdf8; }
.mob-metric-val.val-checked { color: #4ade80; }
.mob-metric-val.val-pending { color: #facc15; }
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-top: 1px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Fast Action Bar - 2 Column Full-Width Grid */
.mob-actions-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    width: 100%;
}
.mob-act-btn {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-act-btn:active {
    transform: scale(0.97);
}
.mob-act-btn-info {
    background: #0284c7;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    border: 1px solid #0284c7;
}
.mob-act-btn-outline {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

/* 2. Compact Search & Horizontal Status Filter Toolbar */
.mob-filter-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mob-search-row {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.mob-search-input-wrap {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i.fa-search {
    position: absolute;
    left: 10px;
    color: #94a3b8;
    font-size: 11.5px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 32px;
    padding-left: 30px;
    padding-right: 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: all .15s ease;
}
.mob-search-input:focus {
    background: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-search-clear-btn {
    position: absolute;
    right: 8px;
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
    padding: 4px;
}

/* Horizontal Filter Status Chips */
.mob-chips-scroller {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 2px;
}
.mob-chips-scroller::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .12s ease;
    text-decoration: none !important;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.25);
}
.mob-chip-counter {
    background: rgba(0, 0, 0, 0.08);
    padding: 1px 4px;
    border-radius: 2px;
    font-size: 9px;
}
.mob-chip.active .mob-chip-counter {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* 3. High-Performance Mobile Card Feed */
.mob-feed-wrap {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 12px;
}
.mob-sub-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
    border-left: 3.5px solid #002C54;
    transition: all .15s ease;
}
.mob-sub-card:active {
    background: #f8fafc;
}
.mob-sub-card.status-border-checked {
    border-left-color: #10b981;
}
.mob-sub-card.status-border-pending {
    border-left-color: #f59e0b;
}

/* Card Header Strip */
.mob-sub-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-sub-student {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.mob-sub-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 11.5px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.25);
}
.mob-sub-info {
    min-width: 0;
    flex: 1;
}
.mob-sub-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-sub-meta-sub {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.2;
    margin-top: 1px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-eval-badge {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.mob-badge-checked {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.mob-badge-pending {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
.mob-badge-nodocs {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}

/* Card Metadata Grid */
.mob-sub-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #475569;
    margin-bottom: 7px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 5px 8px;
}
.mob-grid-item {
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-grid-item i {
    color: #64748b;
    font-size: 10px;
    width: 12px;
    text-align: center;
    flex-shrink: 0;
}
.mob-grid-item strong {
    color: #0f172a;
    font-weight: 700;
}

/* Card Actions Strip */
.mob-sub-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-btn-card-eval {
    flex: 1;
    height: 30px;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0, 44, 84, 0.25);
    transition: all .12s ease;
}
.mob-btn-card-eval:active {
    transform: scale(0.97);
}
.mob-btn-card-call {
    width: 32px;
    height: 30px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #10b981;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none !important;
    flex-shrink: 0;
}
.mob-btn-card-call:active {
    background: #e2e8f0;
}

/* 4. Native Slide-Up Sheets (Info & Evaluation) */
.mob-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2150;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-sheet-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2151;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 88vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 8px 8px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ffffff;
    min-width: 0;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}

/* Empty State */
.mob-empty-feed {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 30px 16px;
    text-align: center;
    color: #64748b;
    margin-bottom: 12px;
}
.mob-empty-icon {
    font-size: 32px;
    color: #94a3b8;
    margin-bottom: 6px;
}
.mob-empty-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 2px;
}
.mob-empty-desc {
    font-size: 10.5px;
    color: #64748b;
}

/* Floating Bulk Action Dock */
.mob-bulk-dock {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    background: #002C54;
    color: #ffffff;
    border-radius: 6px;
    padding: 8px 12px;
    display: none;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 16px rgba(0, 24, 51, 0.35);
    z-index: 1050;
    border: 1px solid #0284c7;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    {{-- Row 1: Session, Category, and Due Status Badges --}}
    <div class="mob-hero-top">
        <div class="mob-hero-badge-left">
            <span class="mob-type-badge {{ $badgeClass }}">{{ $type }}</span>
            @if($isOverdue)
                <span class="mob-status-badge bg-danger text-white"><i class="fa fa-clock-o mr-1"></i> Due Expired</span>
            @elseif($isDueToday)
                <span class="mob-status-badge bg-warning text-dark"><i class="fa fa-hourglass-half mr-1"></i> Due Today</span>
            @else
                <span class="mob-status-badge bg-success text-white"><i class="fa fa-check-circle mr-1"></i> Active</span>
            @endif
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-calendar mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Row 2: Assignment Title with Smooth Expand/Collapse Chevron Button --}}
    <div class="mob-hero-content-wrap">
        <div class="mob-hero-title-box" id="mobHeroTitleBox" title="Tap to expand / collapse full assignment topic">
            <div class="mob-hero-title-left">
                <i class="fa fa-book mob-hero-title-icon"></i>
                <div class="mob-hero-title-text" id="mobHeroTitleText">
                    {{ $displayTitle }}
                </div>
            </div>
            <button type="button" class="mob-hero-expand-btn" id="mobHeroExpandBtn" aria-label="Toggle Full Topic">
                <i class="fa fa-chevron-down"></i>
            </button>
        </div>
    </div>

    {{-- Row 3: Class, Subject, Due Date, and Teacher Meta Chips --}}
    <div class="mob-hero-meta-strip">
        <span class="mob-meta-chip">
            <i class="fa fa-graduation-cap text-info"></i> <strong>{{ $hwClass }}</strong>
        </span>
        <span class="mob-meta-chip">
            <i class="fa fa-bookmark text-primary"></i> <strong>{{ $hwSubject }}</strong>
        </span>
        <span class="mob-meta-chip chip-due">
            <i class="fa fa-calendar-check-o"></i> Due: <strong>{{ $hwDueDate }}</strong>
        </span>
        <span class="mob-meta-chip">
            <i class="fa fa-user-circle text-muted"></i> <strong>{{ $hwTeacher }}</strong>
        </span>
    </div>

    {{-- Metrics Glance Grid --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-users text-info"></i> Students</span>
            <span class="mob-metric-val val-total">{{ $totalStudents }}</span>
            <span class="mob-metric-sub">Submitted</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-check-circle text-success"></i> Checked</span>
            <span class="mob-metric-val val-checked">{{ $checkedCount }}</span>
            <span class="mob-metric-sub">Evaluated</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-clock-o text-warning"></i> Pending</span>
            <span class="mob-metric-val val-pending">{{ $pendingCount }}</span>
            <span class="mob-metric-sub">Need Review</span>
        </div>
    </div>

    {{-- Fast Action Bar --}}
    <div class="mob-actions-bar">
        <button type="button" class="mob-act-btn mob-act-btn-info" id="btnOpenHwInfoSheet">
            <i class="fa fa-info-circle"></i> Instructions &amp; PDF
        </button>
        <a href="{{ url('homework/index?layout=mobile') }}" class="mob-act-btn mob-act-btn-outline">
            <i class="fa fa-list"></i> Directory
        </a>
    </div>
</div>

{{-- 2. Compact Search & Horizontal Filter Status Chips --}}
<div class="mob-filter-toolbar">
    <div class="mob-search-row">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" 
                   id="mobSubmissionSearch" 
                   class="mob-search-input" 
                   placeholder="Search student, admission no, mobile..." 
                   autocomplete="off">
            <button type="button" id="mobClearSearchBtn" class="mob-search-clear-btn">&times;</button>
        </div>
    </div>
    <div class="mob-chips-scroller">
        <button type="button" class="mob-chip active" data-status="">
            All <span class="mob-chip-counter">{{ $totalStudents }}</span>
        </button>
        <button type="button" class="mob-chip" data-status="pending">
            <i class="fa fa-clock-o text-warning"></i> Pending Review <span class="mob-chip-counter">{{ $pendingCount }}</span>
        </button>
        <button type="button" class="mob-chip" data-status="checked">
            <i class="fa fa-check text-success"></i> Checked <span class="mob-chip-counter">{{ $checkedCount }}</span>
        </button>
    </div>
</div>

{{-- 3. Student Submissions Feed Cards --}}
<div class="mob-feed-wrap" id="mobSubmissionsFeed">
    @if(!empty($students) && count($students) > 0)
        @foreach($students as $typeItem)
            @php
                $admId = $typeItem->admission_id;
                $studentName = trim(($typeItem->Admission->first_name ?? '') . ' ' . ($typeItem->Admission->last_name ?? ''));
                if (empty($studentName)) {
                    $studentName = 'Student #' . $admId;
                }
                $admNo = $typeItem->Admission->admissionNo ?? '-';
                $mobile = $typeItem->Admission->mobile ?? '';
                $fatherName = $typeItem->Admission->father_name ?? '-';
                $className = $typeItem->ClassType->name ?? ($typeItem->Admission->ClassTypes->name ?? '-');

                // Document evaluations count
                $stats = $docStats[$admId] ?? null;
                $totalDocs = $stats->total_docs ?? 0;
                $checkedDocs = $stats->checked_docs ?? 0;
                $isChecked = ($totalDocs > 0 && $totalDocs == $checkedDocs);

                $attempts = $attemptStats[$admId]->total_attempts ?? 1;
                $lastDate = !empty($typeItem->submission_date) ? date('d M, h:i A', strtotime($typeItem->submission_date)) : '-';
                $statusSlug = $isChecked ? 'checked' : 'pending';
            @endphp
            <div class="mob-sub-card {{ $isChecked ? 'status-border-checked' : 'status-border-pending' }} mob-sub-item"
                 data-adm-id="{{ $admId }}"
                 data-name="{{ strtolower($studentName) }}"
                 data-adm="{{ strtolower($admNo) }}"
                 data-mobile="{{ strtolower($mobile) }}"
                 data-father="{{ strtolower($fatherName) }}"
                 data-status="{{ $statusSlug }}">

                <div class="mob-sub-header">
                    <div class="mob-sub-student">
                        <div class="mob-sub-avatar">
                            {{ strtoupper(substr($studentName, 0, 1)) }}
                        </div>
                        <div class="mob-sub-info">
                            <div class="mob-sub-name">{{ $studentName }}</div>
                            <div class="mob-sub-meta-sub">
                                <span>Adm: <strong>{{ $admNo }}</strong></span>
                                <span>&bull;</span>
                                <span>Class: <strong>{{ $className }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($totalDocs == 0)
                            <span class="mob-eval-badge mob-badge-nodocs"><i class="fa fa-info-circle"></i> No Docs</span>
                        @elseif($isChecked)
                            <span class="mob-eval-badge mob-badge-checked"><i class="fa fa-check"></i> Checked ({{ $checkedDocs }}/{{ $totalDocs }})</span>
                        @else
                            <span class="mob-eval-badge mob-badge-pending"><i class="fa fa-clock-o"></i> {{ $checkedDocs }}/{{ $totalDocs }} Checked</span>
                        @endif
                    </div>
                </div>

                {{-- Metadata Grid --}}
                <div class="mob-sub-grid">
                    <div class="mob-grid-item" title="Submitted Time">
                        <i class="fa fa-calendar-check-o text-primary"></i> <span>Last: <strong>{{ $lastDate }}</strong></span>
                    </div>
                    <div class="mob-grid-item" title="Submission Attempts">
                        <i class="fa fa-history text-secondary"></i> <span>Attempts: <strong>{{ $attempts }}</strong></span>
                    </div>
                    <div class="mob-grid-item" title="Father Name">
                        <i class="fa fa-user text-muted"></i> <span>Father: <strong>{{ $fatherName }}</strong></span>
                    </div>
                    <div class="mob-grid-item" title="Mobile Number">
                        <i class="fa fa-phone text-success"></i> <span>Phone: <strong>{{ $mobile ?: 'N/A' }}</strong></span>
                    </div>
                </div>

                {{-- Action Strip --}}
                <div class="mob-sub-actions">
                    <button type="button" 
                            class="mob-btn-card-eval btnOpenEvalSheet" 
                            data-homework_id="{{ $homework->id ?? $id }}" 
                            data-admission_id="{{ $admId }}" 
                            data-student_name="{{ $studentName }}">
                        <i class="fa fa-pencil-square-o"></i> Review &amp; Grade Submission
                    </button>
                    @if(!empty($mobile))
                        <a href="tel:{{ $mobile }}" class="mob-btn-card-call" title="Call Student">
                            <i class="fa fa-phone"></i>
                        </a>
                    @endif
                </div>

            </div>
        @endforeach
    @else
        <div class="mob-empty-feed" id="mobEmptyState">
            <div class="mob-empty-icon"><i class="fa fa-users"></i></div>
            <div class="mob-empty-title">No Submissions Found</div>
            <div class="mob-empty-desc">No students have submitted this homework assignment yet.</div>
        </div>
    @endif
</div>

{{-- 4. Native Slide-Up Sheet: Homework Info & Attachment --}}
<div class="mob-sheet-backdrop" id="mobHwInfoSheetBackdrop"></div>
<div class="mob-sheet" id="mobHwInfoSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title text-truncate">
            <i class="fa fa-info-circle text-info"></i> {{ $displayTitle }}
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseHwInfoSheet">&times;</button>
    </div>
    <div class="mob-sheet-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="mob-tag-pill {{ $badgeClass }}">{{ $type }}</span>
            <span class="text-muted" style="font-size:10.5px;"><i class="fa fa-clock-o mr-1"></i> Due: <strong class="text-danger">{{ $hwDueDate }}</strong></span>
        </div>

        <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:4px; padding:8px 10px; margin-bottom:10px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:3px;">Instructions / Question Content:</div>
            <div style="font-size:11.5px; color:#1e293b; line-height:1.45; max-height:220px; overflow-y:auto; word-break:break-word;">
                @if(!empty($homework->description))
                    {!! $homework->description !!}
                @else
                    <span class="text-muted font-italic">No instructions or description provided.</span>
                @endif
            </div>
        </div>

        @if(!empty($homework->content_file))
            <div style="margin-bottom:12px;">
                <label style="font-size:10px; font-weight:700; text-transform:uppercase; color:#475569; margin-bottom:4px; display:block;">Original Question Document:</label>
                <a href="{{ asset('schoolimage/homework/' . $homework->content_file) }}" target="_blank" download class="btn btn-sm btn-light w-100 text-left d-flex align-items-center justify-content-between" style="border:1px solid #cbd5e1; font-size:11px; font-weight:700; background:#f1f5f9; height:36px; border-radius:4px;">
                    <span class="text-truncate"><i class="fa fa-paperclip mr-1 text-primary"></i> {{ $homework->content_file }}</span>
                    <span class="badge badge-primary"><i class="fa fa-download"></i> View</span>
                </a>
            </div>
        @endif

        <div class="mob-sub-grid mb-0">
            <div class="mob-grid-item">
                <i class="fa fa-graduation-cap"></i> Class: <strong>{{ $hwClass }}</strong>
            </div>
            <div class="mob-grid-item">
                <i class="fa fa-bookmark"></i> Subject: <strong>{{ $hwSubject }}</strong>
            </div>
            <div class="mob-grid-item">
                <i class="fa fa-user-circle"></i> Teacher: <strong>{{ $hwTeacher }}</strong>
            </div>
            <div class="mob-grid-item">
                <i class="fa fa-calendar"></i> Issued: <strong>{{ $hwIssueDate }}</strong>
            </div>
        </div>
    </div>
</div>

{{-- 5. Native Slide-Up Sheet: Evaluation Workspace --}}
<div class="mob-sheet-backdrop" id="mobEvalSheetBackdrop"></div>
<div class="mob-sheet" id="mobEvalSheet" style="max-height: 94vh;">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title text-truncate">
            <i class="fa fa-pencil-square-o text-info mr-1"></i> <span id="mobEvalStudentName">Student Evaluation</span>
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseEvalSheet">&times;</button>
    </div>
    <div class="mob-sheet-body" id="mobEvalContent" style="background: #eef2f6; padding: 8px;">
        <div class="d-flex flex-column align-items-center justify-content-center w-100" style="min-height: 240px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,44,84,0.12); margin-bottom: 8px;">
                <i class="fa fa-circle-o-notch fa-spin text-primary" style="font-size: 20px;"></i>
            </div>
            <div style="font-size: 12px; font-weight: 700; color: #002C54;">Loading Submissions...</div>
        </div>
    </div>
</div>

{{-- 6. Document Image / PDF Modal --}}
<div class="modal fade" id="mobDocPreviewModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 2200;">
    <div class="modal-dialog modal-dialog-centered m-2" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #001f3d;">
            <div class="modal-header d-flex align-items-center justify-content-between p-2 px-3" style="background: #002C54; color: #fff;">
                <h6 class="modal-title mb-0" style="font-size: 12px; font-weight: 700;"><i class="fa fa-file-text-o mr-1"></i> Document Preview</h6>
                <button type="button" class="close text-white p-0 m-0" data-dismiss="modal" data-bs-dismiss="modal" style="font-size: 18px; line-height: 1;">&times;</button>
            </div>
            <div class="modal-body p-2 text-center" id="mobDocPreviewBody" style="background: #0f172a; min-height: 320px;">
                {{-- Dynamically injected --}}
            </div>
            <div class="modal-footer p-2" style="background: #001833;">
                <button type="button" class="btn btn-sm btn-secondary w-100" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
var baseUrl = "{{ url('/') }}";
var currentHwId = "{{ $homework->id ?? $id }}";

$(document).ready(function() {
    // 1. Info Sheet Open/Close
    $('#btnOpenHwInfoSheet').on('click', function() {
        $('#mobHwInfoSheetBackdrop').addClass('show');
        $('#mobHwInfoSheet').addClass('show');
    });

    $('#btnCloseHwInfoSheet, #mobHwInfoSheetBackdrop').on('click', function() {
        $('#mobHwInfoSheetBackdrop').removeClass('show');
        $('#mobHwInfoSheet').removeClass('show');
    });

    // Toggle Title Expand / Collapse with Animated Chevron
    $('#mobHeroTitleBox').on('click', function(e) {
        e.preventDefault();
        $('#mobHeroTitleText').toggleClass('expanded');
        $('#mobHeroExpandBtn').toggleClass('expanded');
    });

    // 2. Client Side Real-time Search & Filter Chips
    function filterMobSubmissions() {
        var query = $('#mobSubmissionSearch').val().toLowerCase().trim();
        var status = $('.mob-chip.active').data('status');
        var count = 0;

        $('.mob-sub-item').each(function() {
            var $item = $(this);
            var name = $item.data('name') || '';
            var adm = $item.data('adm') || '';
            var mobile = $item.data('mobile') || '';
            var father = $item.data('father') || '';
            var itemStatus = $item.data('status') || '';

            var matchesSearch = !query || (
                name.indexOf(query) !== -1 ||
                adm.indexOf(query) !== -1 ||
                mobile.indexOf(query) !== -1 ||
                father.indexOf(query) !== -1
            );

            var matchesStatus = !status || (itemStatus === status);

            if (matchesSearch && matchesStatus) {
                $item.show();
                count++;
            } else {
                $item.hide();
            }
        });

        if (count === 0) {
            if ($('#mobNoMatchCard').length === 0) {
                $('#mobSubmissionsFeed').append(
                    '<div class="mob-empty-feed" id="mobNoMatchCard">' +
                        '<div class="mob-empty-icon"><i class="fa fa-filter"></i></div>' +
                        '<div class="mob-empty-title">No Matching Students</div>' +
                        '<div class="mob-empty-desc">No submissions matched your search criteria.</div>' +
                    '</div>'
                );
            }
            $('#mobNoMatchCard').show();
        } else {
            $('#mobNoMatchCard').remove();
        }
    }

    $('#mobSubmissionSearch').on('input', function() {
        var val = $(this).val();
        if (val.length > 0) {
            $('#mobClearSearchBtn').show();
        } else {
            $('#mobClearSearchBtn').hide();
        }
        filterMobSubmissions();
    });

    $('#mobClearSearchBtn').on('click', function() {
        $('#mobSubmissionSearch').val('').trigger('input');
    });

    $('.mob-chip').on('click', function() {
        $('.mob-chip').removeClass('active');
        $(this).addClass('active');
        filterMobSubmissions();
    });

    // 3. Load & Open Student Evaluation Workspace Sheet
    function loadMobStudentEvaluation(hwId, admId, stuName) {
        if (stuName) {
            $('#mobEvalStudentName').text(stuName);
        }
        $('#mobEvalContent').html(
            '<div class="d-flex flex-column align-items-center justify-content-center w-100" style="min-height: 240px;">' +
                '<div style="width: 44px; height: 44px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,44,84,0.12); margin-bottom: 8px;">' +
                    '<i class="fa fa-circle-o-notch fa-spin text-primary" style="font-size: 20px;"></i>' +
                '</div>' +
                '<div style="font-size: 12px; font-weight: 700; color: #002C54;">Loading Submissions...</div>' +
            '</div>'
        );

        $('#mobEvalSheetBackdrop').addClass('show');
        $('#mobEvalSheet').addClass('show');

        $.ajax({
            url: baseUrl + '/particular/hw/details',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                admission_id: admId,
                homework_id: hwId
            },
            success: function(data) {
                $('#mobEvalContent').html(data);
                var loadedName = $('#stuName').data('first_name');
                if (loadedName) {
                    $('#mobEvalStudentName').text(loadedName);
                }
            },
            error: function() {
                $('#mobEvalContent').html('<div class="alert alert-danger mb-0">Failed to load assignment submissions.</div>');
            }
        });
    }

    $(document).on('click', '.btnOpenEvalSheet', function() {
        var hwId = $(this).data('homework_id');
        var admId = $(this).data('admission_id');
        var sName = $(this).data('student_name') || 'Student';
        loadMobStudentEvaluation(hwId, admId, sName);
    });

    $('#btnCloseEvalSheet, #mobEvalSheetBackdrop').on('click', function() {
        $('#mobEvalSheetBackdrop').removeClass('show');
        $('#mobEvalSheet').removeClass('show');
    });

    // 4. Modal Navigation (Next / Prev Student inside Evaluation Workspace)
    $(document).on('click', '.btn-nav-student', function(e) {
        e.preventDefault();
        var admId = $(this).data('admission_id');
        var hwId = $(this).data('homework_id');
        if (admId) {
            loadMobStudentEvaluation(hwId, admId, '');
        }
    });

    // 5. Document preview trigger in modal
    $(document).on('click', '.viewModal2', function() {
        var src = $(this).data('href');
        if (!src) return;

        var ext = src.split('.').pop().toLowerCase();
        var html = '';

        if (ext === 'pdf') {
            html = '<iframe src="' + src + '" width="100%" height="450" frameborder="0" style="border-radius:2px; background:#fff;"></iframe>';
        } else {
            html = '<img src="' + src + '" class="img-fluid" style="max-width:100%; max-height:450px; border-radius:2px; object-fit:contain;" alt="Document">';
        }

        $('#mobDocPreviewBody').html(html);
        $('#mobDocPreviewModal').modal('show');
    });

    // 6. Submit review and evaluation AJAX
    $(document).on('click', '.submitReview, .submitReviewAndNext', function() {
        var $btn = $(this);
        var isAndNext = $btn.hasClass('submitReviewAndNext');
        var nextAdmId = $btn.data('next_admission_id');
        var hwId = $btn.data('homework_id');
        var submit_id = $btn.data('submit');
        var numItems = $('.submit_' + submit_id).length;
        var review = [];
        var id = [];
        var marks = [];

        for (var i = 0; i < numItems; i++) {
            review[i] = $('.submit_' + submit_id).eq(i).val();
            id[i] = $('.submit_' + submit_id).eq(i).data("id");
            marks[i] = $('.marks_' + submit_id).eq(i).length ? $('.marks_' + submit_id).eq(i).val() : null;
        }

        var origText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            type: "POST",
            url: baseUrl + "/evaluate/homework",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                review: review,
                id: id,
                marks: marks
            },
            success: function(response) {
                alert('Review & marks saved successfully.');
                $btn.prop('disabled', false).html('Saved <i class="fa fa-check"></i>');

                if (isAndNext && nextAdmId && hwId) {
                    setTimeout(function() {
                        loadMobStudentEvaluation(hwId, nextAdmId, '');
                    }, 400);
                }
            },
            error: function() {
                alert('Failed to submit evaluation.');
                $btn.prop('disabled', false).html(origText);
            }
        });
    });
});
</script>
@endsection

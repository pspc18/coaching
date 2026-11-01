@extends('layout.mobile_app')

@php
    $classType = Helper::classType();
    $selectedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedClass = collect($classType)->firstWhere('id', $selectedClassId);
    $currentSessionName = Session::get('session_name') ?? date('Y');

    $studentCount = $data instanceof \Illuminate\Support\Collection ? $data->count() : count($data ?? []);
    $subjectCount = $list_subject instanceof \Illuminate\Support\Collection ? $list_subject->count() : count($list_subject ?? []);

    $assignedCount = 0;
    $unassignedCount = 0;

    if (!empty($assignedSubjectsByStudent)) {
        foreach ($data as $st) {
            $subList = $assignedSubjectsByStudent[$st->id] ?? collect();
            if ($subList->isNotEmpty()) {
                $assignedCount++;
            } else {
                $unassignedCount++;
            }
        }
    } else {
        $unassignedCount = $studentCount;
    }
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STREAM & SUBJECT ALLOCATION THEME
   Strictly matched with Admission, Enquiry & Credentials Mobile Views
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
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
}

/* 3 KPI Metrics Glance */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
}
.mob-metric-val {
    font-size: 15px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-box.metric-sky .mob-metric-val { color: #38bdf8; }
.mob-metric-box.metric-green .mob-metric-val { color: #34d399; }
.mob-metric-box.metric-amber .mob-metric-val { color: #fbbf24; }

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 31px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    white-space: nowrap;
}
.mob-act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
}

/* 2. Class Selector Banner */
.mob-class-select-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.mob-class-info-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-class-current-pill {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-btn-change-class {
    height: 28px;
    padding: 0 10px;
    background: #002C54;
    color: #ffffff !important;
    border: none;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* 3. Status Segmented Tabs */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2.5px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 3px;
}
.mob-seg-btn {
    flex: 1;
    padding: 5px 2px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.mob-seg-pill {
    display: inline-block;
    padding: 1px 5px;
    font-size: 9px;
    border-radius: 10px;
    background: rgba(0,0,0,0.08);
    margin-left: 3px;
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* 4. Search Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 24px 0 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-clear-search {
    position: absolute;
    right: 6px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    font-size: 11px;
}
.mob-counter-pill {
    font-size: 10px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 4px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
}

/* 5. Student Stream Card Feed */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 60px; /* Space for sticky bar */
}
.mob-stream-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.mob-stream-card.has-assigned {
    border-left: 3px solid #10b981;
}
.mob-stream-card.has-unassigned {
    border-left: 3px solid #f59e0b;
    background: #fffdfa;
}
.mob-stream-card.is-selected {
    border-color: #0284c7 !important;
    background: #f0f9ff !important;
}

/* Card Header */
.mob-card-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-check-box {
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-check-box input[type="checkbox"] {
    width: 17px;
    height: 17px;
    cursor: pointer;
    accent-color: #002C54;
}
.mob-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}
.mob-head-info {
    flex: 1;
    min-width: 0;
}
.mob-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.mob-student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-pills-row {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}
.mob-adm-badge {
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.mob-subject-count-pill {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-subject-count-pill.pill-green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.mob-subject-count-pill.pill-amber {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}

/* Assigned Subject Chips Section */
.mob-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
    margin-top: 4px;
}
.mob-subject-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2.5px 7px;
    background: #e0f2fe;
    border: 1px solid #bae6fd;
    color: #0369a1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
}
.mob-btn-remove-chip {
    background: none;
    border: none;
    padding: 0;
    color: #ef4444;
    cursor: pointer;
    font-size: 11px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.mob-btn-remove-chip:active {
    transform: scale(1.2);
}
.mob-empty-subject-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    color: #92400e;
    background: #fef3c7;
    border: 1px solid #fde68a;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 600;
}

/* 6. Sticky Floating Bulk Action Bar */
.mob-floating-bar {
    position: fixed;
    bottom: 54px; /* Above bottom app nav bar */
    left: 8px;
    right: 8px;
    background: #001833;
    color: #ffffff;
    border: 1px solid rgba(56, 189, 248, 0.4);
    border-radius: 4px;
    padding: 7px 10px;
    display: none;
    align-items: center;
    justify-content: space-between;
    z-index: 1040;
    box-shadow: 0 4px 18px rgba(0, 20, 40, 0.4);
}
.mob-floating-left {
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-selected-pill {
    background: #0284c7;
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
}
.mob-btn-assign-subjects {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 700;
    padding: 5px 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-btn-clear-sel {
    background: transparent;
    color: #94a3b8;
    border: none;
    font-size: 11px;
    cursor: pointer;
    padding: 2px 6px;
}

/* 7. Bottom Sheet Filter / Class Selector Modal */
.mob-filter-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.6);
    backdrop-filter: blur(2px);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease, visibility .2s ease;
}
.mob-filter-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-filter-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 4px 4px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-filter-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
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
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
}
.mob-form-group {
    margin-bottom: 10px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-select {
    width: 100%;
    height: 34px;
    padding: 0 8px;
    font-size: 12px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
}
.btn-sheet-apply {
    flex: 1;
    height: 33px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* 8. Presets Toolbar in Modal */
.mob-preset-bar {
    display: flex;
    gap: 4px;
    margin-bottom: 10px;
}
.mob-preset-btn {
    flex: 1;
    padding: 5px 2px;
    font-size: 10px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
}
.mob-preset-btn:active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Subject Checkbox Grid */
.mob-subject-check-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    max-height: 240px;
    overflow-y: auto;
    padding: 4px;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    background: #f8fafc;
}
.mob-subject-check-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    cursor: pointer;
    user-select: none;
}
.mob-subject-check-item input[type="checkbox"] {
    width: 15px;
    height: 15px;
    cursor: pointer;
    accent-color: #0284c7;
}

/* Empty State */
.mob-empty-feed {
    text-align: center;
    padding: 36px 16px;
    color: #64748b;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 8px 0;
}
</style>
@endsection

@section('content')
<div class="container-fluid p-2" style="max-width: 600px; margin: 0 auto;">

    {{-- 1. Hero Summary Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-book text-warning"></i> Stream Subject Allocation
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-calendar mr-1"></i> {{ $currentSessionName }}
            </div>
        </div>

        {{-- 3 KPI Metrics --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box metric-sky">
                <div class="mob-metric-tag"><i class="fa fa-users"></i> Students</div>
                <div class="mob-metric-val">{{ number_format($studentCount) }}</div>
            </div>
            <div class="mob-metric-box metric-green">
                <div class="mob-metric-tag"><i class="fa fa-check-circle"></i> Assigned</div>
                <div class="mob-metric-val" id="stat-assigned">{{ number_format($assignedCount) }}</div>
            </div>
            <div class="mob-metric-box metric-amber">
                <div class="mob-metric-tag"><i class="fa fa-clock-o"></i> Unassigned</div>
                <div class="mob-metric-val" id="stat-unassigned">{{ number_format($unassignedCount) }}</div>
            </div>
        </div>

        {{-- Fast Action Buttons --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-light" id="btn-toggle-select-all" onclick="toggleSelectAll()">
                <i class="fa fa-check-square-o"></i> <span id="lbl-select-all">Select All</span>
            </button>
            <button type="button" class="mob-act-btn mob-act-btn-primary" onclick="openClassSheet()">
                <i class="fa fa-graduation-cap"></i> Select Class
            </button>
        </div>
    </div>

    {{-- 2. Class Selector Banner --}}
    <div class="mob-class-select-card">
        <div class="mob-class-info-wrap">
            <span class="text-muted" style="font-size: 11px;">Current Class:</span>
            <span class="mob-class-current-pill">
                <i class="fa fa-graduation-cap text-primary"></i> {{ $selectedClass->name ?? 'No Class Selected' }}
            </span>
        </div>
        <button type="button" class="mob-btn-change-class" onclick="openClassSheet()">
            <span>Change</span> <i class="fa fa-angle-right"></i>
        </button>
    </div>

    @if($selectedClassId > 0)
        {{-- 3. Status Segmented Tabs --}}
        <div class="mob-seg-bar">
            <button type="button" class="mob-seg-btn active" data-status="all" onclick="filterByStatus('all', this)">
                All <span class="mob-seg-pill">{{ $studentCount }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-status="assigned" onclick="filterByStatus('assigned', this)">
                🟢 Assigned <span class="mob-seg-pill">{{ $assignedCount }}</span>
            </button>
            <button type="button" class="mob-seg-btn" data-status="unassigned" onclick="filterByStatus('unassigned', this)">
                🟡 Unassigned <span class="mob-seg-pill">{{ $unassignedCount }}</span>
            </button>
        </div>

        {{-- 4. Search Toolbar --}}
        <div class="mob-search-toolbar">
            <div class="mob-search-input-wrap">
                <i class="fa fa-search"></i>
                <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search student name or Adm No...">
                <button type="button" id="mobClearSearch" class="mob-clear-search" onclick="clearSearch()">&times;</button>
            </div>
            <span class="mob-counter-pill" id="mobRecordCount">{{ $studentCount }} Students</span>
        </div>

        {{-- 5. Student Stream Cards Feed --}}
        <div class="mob-feed-list" id="streamCardsContainer">
            @if(!empty($data) && $studentCount > 0)
                @foreach($data as $student)
                    @php
                        $fullName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
                        $initial = strtoupper(mb_substr($fullName ?: 'S', 0, 1, 'UTF-8'));
                        $subList = $assignedSubjectsByStudent[$student->id] ?? collect();
                        $hasSub = $subList->isNotEmpty();
                    @endphp
                    <div class="mob-stream-card {{ $hasSub ? 'has-assigned' : 'has-unassigned' }}"
                         id="student-card-{{ $student->id }}"
                         data-id="{{ $student->id }}"
                         data-name="{{ strtolower($fullName) }}"
                         data-adm="{{ strtolower($student->admissionNo ?? '') }}"
                         data-status="{{ $hasSub ? 'assigned' : 'unassigned' }}">

                        {{-- Card Header --}}
                        <div class="mob-card-head">
                            <div class="mob-check-box">
                                <input type="checkbox" class="student-check" value="{{ $student->id }}"
                                       data-name="{{ $fullName }}"
                                       title="Select {{ $fullName }}">
                            </div>

                            <div class="mob-avatar-box">
                                <span>{{ $initial }}</span>
                            </div>

                            <div class="mob-head-info">
                                <div class="mob-name-row">
                                    <span class="mob-student-name">{{ $fullName ?: '-' }}</span>
                                    <span class="mob-subject-count-pill {{ $hasSub ? 'pill-green' : 'pill-amber' }}" id="sub-count-{{ $student->id }}">
                                        {{ $subList->count() }} Subjects
                                    </span>
                                </div>
                                <div class="mob-pills-row">
                                    @if(!empty($student->admissionNo))
                                        <span class="mob-adm-badge">Adm: {{ $student->admissionNo }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Subject Chips Container --}}
                        <div class="mob-chips-wrap" id="subject-list-{{ $student->id }}">
                            @if($hasSub)
                                @foreach($subList as $subject)
                                    <span class="mob-subject-chip" id="admission-{{ $student->id }}-{{ $subject->id }}">
                                        <span>{{ $subject->name }}</span>
                                        <button type="button" class="mob-btn-remove-chip"
                                                onclick="confirmRemoveSubject({{ $student->id }}, {{ $subject->id }}, '{{ addslashes($subject->name) }}', '{{ addslashes($fullName) }}')"
                                                title="Remove {{ $subject->name }}">
                                            <i class="fa fa-times-circle"></i>
                                        </button>
                                    </span>
                                @endforeach
                            @else
                                <span class="mob-empty-subject-badge" id="empty-sub-{{ $student->id }}">
                                    <i class="fa fa-exclamation-triangle"></i> No subjects assigned
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="mob-empty-feed">
                    <i class="fa fa-users fa-2x text-muted mb-2"></i>
                    <h6>No Students in Class</h6>
                    <p>There are no active students in {{ $selectedClass->name ?? 'this class' }}.</p>
                </div>
            @endif
        </div>
    @else
        <div class="mob-empty-feed">
            <i class="fa fa-graduation-cap fa-3x text-muted mb-2"></i>
            <h6>Please Select a Class</h6>
            <p>Select a class above to view students and allocate optional/stream subjects.</p>
            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="openClassSheet()" style="background: #0284c7; border: none; font-weight: 700;">
                <i class="fa fa-filter mr-1"></i> Choose Class
            </button>
        </div>
    @endif

</div>

{{-- 6. Sticky Floating Bulk Action Bar --}}
<div class="mob-floating-bar" id="mobFloatingBar">
    <div class="mob-floating-left">
        <span class="mob-selected-pill"><span id="lblSelectedCount">0</span> Selected</span>
        <button type="button" class="mob-btn-clear-sel" onclick="clearAllSelections()">Clear</button>
    </div>
    <button type="button" class="mob-btn-assign-subjects" onclick="openAssignModal()">
        <i class="fa fa-plus-circle"></i> Assign Subjects
    </button>
</div>

{{-- 7. Bottom Sheet Class Selector Modal --}}
<div class="mob-filter-modal-backdrop" id="mobFilterBackdrop" onclick="closeClassSheet()"></div>
<div class="mob-filter-sheet" id="mobFilterSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-graduation-cap text-info"></i> Select Class
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeClassSheet()">&times;</button>
    </div>
    <form action="{{ url('stream_update') }}" method="GET" id="mobClassForm">
        <input type="hidden" name="layout" value="mobile">
        <div class="mob-sheet-body">
            <div class="mob-form-group">
                <label class="mob-form-label">Choose Class</label>
                <select name="class_type_id" class="mob-form-select" id="mobClassSelect" onchange="document.getElementById('mobClassForm').submit()">
                    <option value="">-- Choose Class --</option>
                    @if(!empty($classType))
                        @foreach($classType as $class)
                            <option value="{{ $class->id }}" {{ $selectedClassId === (int)$class->id ? 'selected' : '' }}>
                                {{ $class->name ?? ('Class #' . $class->id) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="mob-sheet-footer">
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Load Class Students
            </button>
        </div>
    </form>
</div>

{{-- 8. Mobile Assign Subjects Modal --}}
<div class="modal fade" id="mobAssignModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px; margin: 12px auto;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #002C54;">
            <div class="modal-header" style="background: linear-gradient(135deg, #001833 0%, #002C54 100%); color: #ffffff; padding: 10px 14px;">
                <h6 class="modal-title m-0" style="font-size: 13px; font-weight: 800;">
                    <i class="fa fa-book text-warning mr-1"></i> Allocate Stream Subjects
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ url('stream_update_save') }}" method="POST" id="mobAssignForm">
                @csrf
                <input type="hidden" name="class_type_id" value="{{ $selectedClassId }}">
                <div id="hiddenAdmissionInputs"></div>

                <div class="modal-body p-3" style="font-size: 11.5px;">
                    <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 11px; border-radius: 3px;">
                        <i class="fa fa-info-circle mr-1"></i> Assigning subjects to <b id="mobModalSelectedCount">0</b> selected student(s) of <b>{{ $selectedClass->name ?? '' }}</b>.
                    </div>

                    {{-- Quick Stream Presets --}}
                    <label class="font-weight-bold text-dark mb-1" style="font-size: 10.5px; text-transform: uppercase;">
                        Quick Stream Presets:
                    </label>
                    <div class="mob-preset-bar">
                        <button type="button" class="mob-preset-btn" onclick="applySubjectPreset(['physics', 'chemistry', 'mathematics', 'maths', 'math'])">
                            ⚡ Science (PCM)
                        </button>
                        <button type="button" class="mob-preset-btn" onclick="applySubjectPreset(['physics', 'chemistry', 'biology', 'bio'])">
                            🔬 Science (PCB)
                        </button>
                        <button type="button" class="mob-preset-btn" onclick="applySubjectPreset(['commerce', 'accounts', 'accountancy', 'business', 'economics'])">
                            💼 Commerce
                        </button>
                        <button type="button" class="mob-preset-btn" onclick="applySubjectPreset(['arts', 'history', 'geography', 'political', 'hindi', 'english'])">
                            🎨 Arts
                        </button>
                    </div>

                    <label class="font-weight-bold text-dark mb-1" style="font-size: 10.5px; text-transform: uppercase;">
                        Select Subjects:
                    </label>
                    <div class="mob-subject-check-grid">
                        @if(!empty($list_subject) && $subjectCount > 0)
                            @foreach($list_subject as $subj)
                                <label class="mob-subject-check-item">
                                    <input type="checkbox" name="subject_id[]" value="{{ $subj->id }}" data-subj-name="{{ strtolower($subj->name) }}">
                                    <span>{{ $subj->name }}</span>
                                </label>
                            @endforeach
                        @else
                            <div class="p-2 text-muted text-center col-span-2" style="grid-column: span 2; font-size: 11px;">
                                No subjects registered for this class.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer p-2" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" style="font-size: 11px; font-weight: 700;">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="mobBtnSubmitAssign" style="background: #0284c7; border-color: #0284c7; font-size: 11px; font-weight: 700;">
                        <i class="fa fa-check mr-1"></i> Save Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 9. Remove Subject Confirmation Modal --}}
<div class="modal fade" id="mobRemoveSubjectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 360px; margin: 12px auto;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #ef4444;">
            <div class="modal-header" style="background: #ef4444; color: #ffffff; padding: 10px 14px;">
                <h6 class="modal-title m-0" style="font-size: 13px; font-weight: 800;">
                    <i class="fa fa-trash mr-1"></i> Remove Subject
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3" style="font-size: 11.5px;">
                Are you sure you want to remove <b id="lblRemoveSubjName" class="text-danger"></b> from <b id="lblRemoveStudentName"></b>?
            </div>
            <div class="modal-footer p-2" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" style="font-size: 11px; font-weight: 700;">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnConfirmRemove" style="font-size: 11px; font-weight: 700;">
                    <i class="fa fa-trash mr-1"></i> Remove
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var streamBaseUrl = "{{ url('/') }}";

// Class Sheet Controls
function openClassSheet() {
    $('#mobFilterBackdrop').addClass('show');
    $('#mobFilterSheet').addClass('show');
}
function closeClassSheet() {
    $('#mobFilterBackdrop').removeClass('show');
    $('#mobFilterSheet').removeClass('show');
}

// Recency / Status Segment Tabs Filter
var currentStatusFilter = 'all';

function filterByStatus(st, btn) {
    currentStatusFilter = st;
    $('.mob-seg-btn').removeClass('active');
    $(btn).addClass('active');
    applyCombinedFilters();
}

// Client-Side Fast Search with Debounce
var searchTimeout = null;
$('#mobSearchInput').on('input', function() {
    var val = $.trim($(this).val()).toLowerCase();
    if (val.length > 0) {
        $('#mobClearSearch').show();
    } else {
        $('#mobClearSearch').hide();
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        applyCombinedFilters();
    }, 120);
});

function clearSearch() {
    $('#mobSearchInput').val('').trigger('input');
}

function applyCombinedFilters() {
    var kw = $.trim($('#mobSearchInput').val()).toLowerCase();
    var visibleCount = 0;

    $('.mob-stream-card').each(function() {
        var cardStatus = $(this).attr('data-status') || '';
        var statusMatch = (currentStatusFilter === 'all' || cardStatus === currentStatusFilter);

        if (!statusMatch) {
            $(this).hide();
            return;
        }

        if (!kw) {
            $(this).show();
            visibleCount++;
            return;
        }

        var name = $(this).attr('data-name') || '';
        var adm = $(this).attr('data-adm') || '';

        if (name.indexOf(kw) !== -1 || adm.indexOf(kw) !== -1) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    $('#mobRecordCount').text(visibleCount + ' Students');
}

// Selection Management
function updateSelection() {
    var totalChecked = $('.student-check:checked').length;

    $('.student-check').each(function() {
        var $card = $(this).closest('.mob-stream-card');
        if ($(this).is(':checked')) {
            $card.addClass('is-selected');
        } else {
            $card.removeClass('is-selected');
        }
    });

    $('#lblSelectedCount').text(totalChecked);
    $('#mobModalSelectedCount').text(totalChecked);

    if (totalChecked > 0) {
        $('#mobFloatingBar').css('display', 'flex');
        $('#lbl-select-all').text('Clear All');
    } else {
        $('#mobFloatingBar').hide();
        $('#lbl-select-all').text('Select All');
    }
}

$(document).on('change', '.student-check', updateSelection);

function toggleSelectAll() {
    var totalChecked = $('.student-check:checked').length;
    if (totalChecked > 0) {
        $('.student-check').prop('checked', false);
    } else {
        $('.mob-stream-card:visible .student-check').prop('checked', true);
    }
    updateSelection();
}

function clearAllSelections() {
    $('.student-check').prop('checked', false);
    updateSelection();
}

// Subject Presets
function applySubjectPreset(keywords) {
    $('.mob-subject-check-grid input[type="checkbox"]').prop('checked', false);
    $('.mob-subject-check-grid input[type="checkbox"]').each(function() {
        var subName = $(this).attr('data-subj-name') || '';
        for (var i = 0; i < keywords.length; i++) {
            if (subName.indexOf(keywords[i]) !== -1) {
                $(this).prop('checked', true);
                break;
            }
        }
    });
}

// Open Assign Modal
function openAssignModal() {
    var selectedIds = [];
    $('.student-check:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    var hiddenHtml = '';
    for (var i = 0; i < selectedIds.length; i++) {
        hiddenHtml += '<input type="hidden" name="admission_id[]" value="' + selectedIds[i] + '">';
    }
    $('#hiddenAdmissionInputs').html(hiddenHtml);
    $('#mobAssignModal').modal('show');
}

$('#mobAssignForm').on('submit', function() {
    var checkedSubjs = $('.mob-subject-check-grid input[type="checkbox"]:checked').length;
    if (checkedSubjs === 0) {
        alert('Please select at least one subject to assign.');
        return false;
    }
    $('#mobBtnSubmitAssign').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
});

// Remove Subject Chip Handlers
var pendingDelete = null;

function confirmRemoveSubject(admId, subId, subName, stuName) {
    pendingDelete = { admId: admId, subId: subId };
    $('#lblRemoveSubjName').text(subName);
    $('#lblRemoveStudentName').text(stuName);
    $('#mobRemoveSubjectModal').modal('show');
}

$('#btnConfirmRemove').on('click', function() {
    if (!pendingDelete) return;
    var admId = pendingDelete.admId;
    var subId = pendingDelete.subId;

    $('#mobRemoveSubjectModal').modal('hide');
    var $chip = $('#admission-' + admId + '-' + subId);
    $chip.css('opacity', 0.5);

    $.ajax({
        url: streamBaseUrl + '/stream_remove/' + admId + '/' + subId,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function() {
            $chip.fadeOut(150, function() {
                $(this).remove();
                var remaining = $('#subject-list-' + admId).find('.mob-subject-chip').length;
                $('#sub-count-' + admId).text(remaining + ' Subjects');
                if (remaining === 0) {
                    $('#sub-count-' + admId).removeClass('pill-green').addClass('pill-amber');
                    $('#subject-list-' + admId).html('<span class="mob-empty-subject-badge"><i class="fa fa-exclamation-triangle"></i> No subjects assigned</span>');
                    $('#student-card-' + admId).removeClass('has-assigned').addClass('has-unassigned').attr('data-status', 'unassigned');
                }
            });
        },
        error: function() {
            $chip.css('opacity', 1);
            alert('Failed to remove subject.');
        }
    });
});
</script>
@endsection

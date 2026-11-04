@extends('layout.mobile_app')

@include('layout.message')

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
   Strictly matched with Mobile Dashboard & ERP Mobile App Shell
   - Consistent 8px spacing, sharp 4px radii & standard card borders
   - Unified Glassmorphic Hero with circular avatar accent & dual radar metrics
   - Theme-standard Empty States matching Dashboard and Admission views
   - Native Mobile Slide-Up Bottom Sheet for Subject Allocation & Class Selection
   ========================================================================== */

/* 1. Glassmorphic Hero Card (Strictly Matched to Mobile Dashboard .native-hero-card) */
.native-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 4px;
    padding: 12px 14px;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(0, 20, 40, 0.25);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}
.native-hero-card::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.2) 0%, rgba(56, 189, 248, 0) 70%);
    pointer-events: none;
}
.native-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.hero-user-cluster {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.hero-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    border: 2px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}
.hero-user-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hero-user-sub {
    font-size: 9.5px;
    color: #93c5fd;
    font-weight: 600;
    letter-spacing: .02em;
}
.hero-session-pill {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #bae6fd !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

/* Symmetrical 3-Column Metrics Glance */
.hero-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    background: rgba(0, 15, 30, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 10px;
}
.hero-metric-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
}
.hero-metric-box:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -3px;
    top: 15%;
    bottom: 15%;
    width: 1px;
    background: rgba(255, 255, 255, 0.12);
}
.hero-metric-tag {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    display: flex;
    align-items: center;
    gap: 4px;
}
.hero-metric-val {
    font-size: 16px;
    font-weight: 800;
    color: #38bdf8;
    line-height: 1.2;
    margin: 2px 0 1px;
}
.hero-metric-val.val-green { color: #34d399; }
.hero-metric-val.val-amber { color: #fbbf24; }
.hero-metric-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 500;
    line-height: 1.1;
}

/* Fast Action Bar inside Hero */
.hero-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.hero-action-btn {
    flex: 1;
    padding: 6px 4px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    line-height: 1.2;
    white-space: nowrap;
    border: none;
    cursor: pointer;
    transition: transform .12s ease;
}
.hero-action-btn:active {
    transform: scale(0.96);
}
.act-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
    border: 1px solid #0284c7;
}
.act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

/* 2. Sharp Section Cards & Class Selector Banner */
.class-selector-card {
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
.class-selector-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.class-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.class-info-cluster {
    min-width: 0;
}
.class-label {
    font-size: 9.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .03em;
    display: block;
    line-height: 1.1;
}
.class-name-txt {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 1px;
}
.class-badge-subjects {
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 1px 5px;
    border-radius: 2px;
}
.btn-change-class {
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
    flex-shrink: 0;
    transition: background .12s ease;
}
.btn-change-class:active {
    background: #0284c7;
}

/* 3. Status Segmented Tabs (Exact Match with Dashboard .native-segments-bar) */
.native-segments-bar {
    display: flex;
    background: #e2e8f0;
    padding: 3px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 2px;
}
.native-seg-btn {
    flex: 1;
    padding: 6.5px 2px;
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    user-select: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.native-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.native-seg-pill {
    display: inline-block;
    padding: 1px 5px;
    font-size: 9px;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.08);
}
.native-seg-btn.active .native-seg-pill {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* 4. Search Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
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
.mob-search-input-wrap i.fa-search {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11.5px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 31px;
    padding: 0 26px 0 28px;
    font-size: 11.5px;
    font-weight: 500;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: all .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.12);
}
.mob-clear-search {
    position: absolute;
    right: 7px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    font-size: 14px;
    line-height: 1;
    padding: 2px;
}
.mob-counter-pill {
    font-size: 10px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 5px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    white-space: nowrap;
}

/* 5. Theme-Standard Empty State (Strictly Matched to Dashboard & Admission Views) */
.empty-state-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 28px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    margin-bottom: 8px;
}
.empty-state-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 8px;
}
.empty-state-title {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
    line-height: 1.2;
}
.empty-state-desc {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    max-width: 320px;
    margin: 0 auto 12px;
}
.empty-state-btn {
    height: 30px;
    padding: 0 12px;
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #002C54;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background .12s ease;
    text-decoration: none !important;
}
.empty-state-btn:active {
    background: #0284c7;
    border-color: #0284c7;
}
.empty-state-btn-outline {
    height: 30px;
    padding: 0 12px;
    background: #f8fafc;
    color: #002C54 !important;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background .12s ease;
    text-decoration: none !important;
}
.empty-state-btn-outline:active {
    background: #e2e8f0;
}

/* 6. Student Cards Feed */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 64px; /* Space for floating bulk bar */
}
.mob-stream-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease, background-color .1s ease;
}
.mob-stream-card.has-assigned {
    border-left: 3px solid #10b981;
}
.mob-stream-card.has-unassigned {
    border-left: 3px solid #f59e0b;
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
    margin: 0;
    cursor: pointer;
}
.mob-check-box input[type="checkbox"] {
    width: 17px;
    height: 17px;
    cursor: pointer;
    accent-color: #002C54;
    margin: 0;
}
.mob-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    border: 1.5px solid #cbd5e1;
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
    gap: 6px;
}
.mob-student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-subject-count-pill {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1.5px 6px;
    border-radius: 2px;
    white-space: nowrap;
    flex-shrink: 0;
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
.mob-pills-row {
    display: flex;
    align-items: center;
    gap: 6px;
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
.mob-status-dot-text {
    font-size: 9px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

/* Assigned Subject Chips Section */
.mob-chips-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
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

/* 7. Sticky Floating Bulk Action Bar (Aligned above bottom dock nav) */
.mob-floating-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + 12px + var(--safe-bottom, 0px));
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

/* 8. Bottom Sheet Modals (Class Selector & Subject Allocation) */
.mob-bottom-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.65);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
}
.mob-bottom-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-bottom-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 16px 16px 0 0;
    border-top: 1px solid rgba(0, 44, 84, 0.15);
    z-index: 2051;
    transition: bottom .28s cubic-bezier(0.33, 1, 0.68, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -8px 30px rgba(0, 20, 40, 0.35);
}
.mob-bottom-sheet.show {
    bottom: 0;
}
.mob-sheet-handle-wrap {
    padding: 8px 0 4px;
    display: flex;
    justify-content: center;
    cursor: pointer;
}
.mob-sheet-handle-pill {
    width: 36px;
    height: 4px;
    border-radius: 2px;
    background: #cbd5e1;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-sheet-title-cluster {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.mob-sheet-icon-box {
    width: 28px;
    height: 28px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: #fbbf24;
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    line-height: 1.2;
}
.mob-sheet-sub {
    font-size: 10px;
    color: #93c5fd;
    margin-top: 1px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
}
.mob-sheet-body {
    padding: 12px 14px;
    overflow-y: auto;
    flex: 1;
}

/* 9. Native Stream Presets Pills (Horizontal Scrollable Mobile App Bar) */
.mob-presets-section {
    margin-bottom: 12px;
}
.mob-presets-label {
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-presets-scroll-bar {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 4px;
}
.mob-presets-scroll-bar::-webkit-scrollbar {
    display: none;
}
.mob-preset-pill-btn {
    padding: 6px 11px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
    user-select: none;
    flex-shrink: 0;
}
.mob-preset-pill-btn:active {
    transform: scale(0.96);
}
.mob-preset-pill-btn.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}

/* 10. Mobile Subject Tiles Grid */
.mob-subject-select-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 6px;
}
.mob-btn-clear-subjects {
    background: none;
    border: none;
    color: #0284c7;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
}
.mob-subject-tiles-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-subject-tile {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    padding: 9px 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
    transition: all .15s ease;
}
.mob-subject-tile:active {
    transform: scale(0.98);
}
.mob-subject-tile.is-selected {
    background: #eff6ff;
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
}
.mob-tile-checkbox-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
}
.mob-subject-native-check {
    display: none;
}
.mob-custom-check-circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #94a3b8;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    font-size: 11px;
    transition: all .15s ease;
    flex-shrink: 0;
}
.mob-subject-tile.is-selected .mob-custom-check-circle {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
}
.mob-tile-name {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.2;
}

/* Sheet Sticky Bottom Action Bar */
.mob-sheet-action-footer {
    padding: 10px 14px calc(10px + var(--safe-bottom, 0px));
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-sheet-cancel {
    height: 38px;
    padding: 0 16px;
    background: #f1f5f9;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
.btn-sheet-submit {
    flex: 1;
    height: 38px;
    background: #0284c7;
    color: #ffffff !important;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    transition: background .12s ease;
}
.btn-sheet-submit:active {
    background: #0369a1;
}
</style>
@endsection

@section('content')
{{-- 1. Glassmorphic Hero Snapshot Card --}}
<div class="native-hero-card">
    <div class="native-hero-top">
        <div class="hero-user-cluster">
            <div class="hero-avatar-circle">
                <i class="fa fa-book"></i>
            </div>
            <div>
                <div class="hero-user-title">Stream &amp; Subject Allocation</div>
                <div class="hero-user-sub">Manage optional subjects for students</div>
            </div>
        </div>
        <div class="hero-session-pill">
            <i class="fa fa-graduation-cap"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Symmetrical 3-Column Metrics Glance --}}
    <div class="hero-metrics-grid">
        <div class="hero-metric-box">
            <span class="hero-metric-tag"><i class="fa fa-users text-info"></i> Total</span>
            <div class="hero-metric-val">{{ number_format($studentCount) }}</div>
            <span class="hero-metric-meta">Active Students</span>
        </div>
        <div class="hero-metric-box">
            <span class="hero-metric-tag"><i class="fa fa-check-circle text-success"></i> Assigned</span>
            <div class="hero-metric-val val-green" id="stat-assigned">{{ number_format($assignedCount) }}</div>
            <span class="hero-metric-meta">Allocated</span>
        </div>
        <div class="hero-metric-box">
            <span class="hero-metric-tag"><i class="fa fa-clock-o text-warning"></i> Pending</span>
            <div class="hero-metric-val val-amber" id="stat-unassigned">{{ number_format($unassignedCount) }}</div>
            <span class="hero-metric-meta">Unassigned</span>
        </div>
    </div>

    {{-- Fast Action Buttons --}}
    <div class="hero-actions-bar">
        <button type="button" class="hero-action-btn act-btn-primary" onclick="openClassSheet()">
            <i class="fa fa-filter"></i> Select Class
        </button>
        <button type="button" class="hero-action-btn act-btn-light" id="btn-toggle-select-all" onclick="toggleSelectAll()">
            <i class="fa fa-check-square-o"></i> <span id="lbl-select-all">Select All</span>
        </button>
    </div>
</div>

{{-- 2. Class Selector Banner Card --}}
<div class="class-selector-card">
    <div class="class-selector-left">
        <div class="class-icon-box">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div class="class-info-cluster">
            <span class="class-label">Current Class</span>
            <div class="class-name-txt">
                {{ $selectedClass->name ?? 'No Class Selected' }}
                @if($selectedClassId > 0 && $subjectCount > 0)
                    <span class="class-badge-subjects">{{ $subjectCount }} Subjects</span>
                @endif
            </div>
        </div>
    </div>
    <button type="button" class="btn-change-class" onclick="openClassSheet()">
        <span>{{ $selectedClassId > 0 ? 'Change' : 'Select' }}</span>
        <i class="fa fa-angle-right"></i>
    </button>
</div>

@if($selectedClassId > 0)
    {{-- 3. Native Segment Navigation Tabs --}}
    <div class="native-segments-bar">
        <button type="button" class="native-seg-btn active" data-status="all" onclick="filterByStatus('all', this)">
            All <span class="native-seg-pill">{{ $studentCount }}</span>
        </button>
        <button type="button" class="native-seg-btn" data-status="assigned" onclick="filterByStatus('assigned', this)">
            🟢 Assigned <span class="native-seg-pill">{{ $assignedCount }}</span>
        </button>
        <button type="button" class="native-seg-btn" data-status="unassigned" onclick="filterByStatus('unassigned', this)">
            🟡 Unassigned <span class="native-seg-pill">{{ $unassignedCount }}</span>
        </button>
    </div>

    {{-- 4. Search Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search student name or Adm No...">
            <button type="button" id="mobClearSearch" class="mob-clear-search" onclick="clearSearch()" aria-label="Clear Search">&times;</button>
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
                        <label class="mob-check-box" for="check-{{ $student->id }}">
                            <input type="checkbox" id="check-{{ $student->id }}" class="student-check" value="{{ $student->id }}"
                                   data-name="{{ $fullName }}"
                                   title="Select {{ $fullName }}">
                        </label>

                        <div class="mob-avatar-box">
                            <span>{{ $initial }}</span>
                        </div>

                        <div class="mob-head-info">
                            <div class="mob-name-row">
                                <span class="mob-student-name">{{ $fullName ?: '-' }}</span>
                                <span class="mob-subject-count-pill {{ $hasSub ? 'pill-green' : 'pill-amber' }}" id="sub-count-{{ $student->id }}">
                                    {{ $subList->count() }} {{ $subList->count() === 1 ? 'Subject' : 'Subjects' }}
                                </span>
                            </div>
                            <div class="mob-pills-row">
                                @if(!empty($student->admissionNo))
                                    <span class="mob-adm-badge">Adm #{{ $student->admissionNo }}</span>
                                @endif
                                <span class="mob-status-dot-text {{ $hasSub ? 'text-success' : 'text-warning' }}">
                                    <i class="fa {{ $hasSub ? 'fa-check-circle' : 'fa-clock-o' }}"></i>
                                    {{ $hasSub ? 'Assigned' : 'Not Assigned' }}
                                </span>
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
                                            title="Remove {{ $subject->name }}" aria-label="Remove {{ $subject->name }}">
                                        <i class="fa fa-times-circle"></i>
                                    </button>
                                </span>
                            @endforeach
                        @else
                            <span class="mob-empty-subject-badge" id="empty-sub-{{ $student->id }}">
                                <i class="fa fa-info-circle mr-1"></i> No optional subjects assigned
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Dynamic Empty State when Search or Status Filter yields 0 matches --}}
            <div class="empty-state-card" id="mobSearchEmptyState" style="display: none;">
                <div class="empty-state-icon">
                    <i class="fa fa-search"></i>
                </div>
                <div class="empty-state-title">No Matching Students Found</div>
                <div class="empty-state-desc">No students match your selected status tab or search keyword.</div>
                <button type="button" class="empty-state-btn-outline" onclick="resetSearchAndTabs()">
                    <i class="fa fa-refresh mr-1"></i> Reset Filters
                </button>
            </div>
        @else
            {{-- Data Not Found Condition: Class Selected, but 0 Students in DB --}}
            <div class="empty-state-card">
                <div class="empty-state-icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="empty-state-title">No Students in this Class</div>
                <div class="empty-state-desc">There are currently no active students found in {{ $selectedClass->name ?? 'the selected class' }}.</div>
                <button type="button" class="empty-state-btn-outline" onclick="openClassSheet()">
                    <i class="fa fa-exchange mr-1"></i> Switch to Another Class
                </button>
            </div>
        @endif
    </div>
@else
    {{-- Data Not Found / Initial Prompt: No Class Selected Yet --}}
    <div class="empty-state-card">
        <div class="empty-state-icon">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div class="empty-state-title">Please Select a Class</div>
        <div class="empty-state-desc">Select a class from above or click below to view registered students and allocate optional / stream subjects.</div>
        <button type="button" class="empty-state-btn" onclick="openClassSheet()">
            <i class="fa fa-filter mr-1"></i> Choose Class
        </button>
    </div>
@endif

{{-- 6. Sticky Floating Bulk Action Bar --}}
<div class="mob-floating-bar" id="mobFloatingBar">
    <div class="mob-floating-left">
        <span class="mob-selected-pill"><span id="lblSelectedCount">0</span> Selected</span>
        <button type="button" class="mob-btn-clear-sel" onclick="clearAllSelections()">Clear</button>
    </div>
    <button type="button" class="mob-btn-assign-subjects" onclick="openAssignSheet()">
        <i class="fa fa-plus-circle"></i> Assign Subjects
    </button>
</div>

{{-- 7. Native Mobile Bottom Sheet: Class Selector --}}
<div class="mob-bottom-backdrop" id="mobClassBackdrop" onclick="closeClassSheet()"></div>
<div class="mob-bottom-sheet" id="mobClassSheet">
    <div class="mob-sheet-handle-wrap" onclick="closeClassSheet()">
        <div class="mob-sheet-handle-pill"></div>
    </div>
    <div class="mob-sheet-header">
        <div class="mob-sheet-title-cluster">
            <div class="mob-sheet-icon-box">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div>
                <div class="mob-sheet-title">Select Class</div>
                <div class="mob-sheet-sub">Switch class to view &amp; allocate subjects</div>
            </div>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeClassSheet()">&times;</button>
    </div>
    <form action="{{ url('stream_update') }}" method="GET" id="mobClassForm">
        <input type="hidden" name="layout" value="mobile">
        <div class="mob-sheet-body">
            <div class="form-group mb-2">
                <label style="font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase;">Choose Class</label>
                <select name="class_type_id" class="form-control" id="mobClassSelect" onchange="document.getElementById('mobClassForm').submit()">
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
        <div class="mob-sheet-action-footer">
            <button type="button" class="btn-sheet-cancel" onclick="closeClassSheet()">Cancel</button>
            <button type="submit" class="btn-sheet-submit">
                <i class="fa fa-check mr-1"></i> Load Class Students
            </button>
        </div>
    </form>
</div>

{{-- 8. Native Mobile Bottom Sheet: Allocate Stream Subjects --}}
<div class="mob-bottom-backdrop" id="mobAssignBackdrop" onclick="closeAssignSheet()"></div>
<div class="mob-bottom-sheet" id="mobAssignSheet">
    <div class="mob-sheet-handle-wrap" onclick="closeAssignSheet()">
        <div class="mob-sheet-handle-pill"></div>
    </div>
    <div class="mob-sheet-header">
        <div class="mob-sheet-title-cluster">
            <div class="mob-sheet-icon-box">
                <i class="fa fa-book"></i>
            </div>
            <div>
                <div class="mob-sheet-title">Allocate Stream Subjects</div>
                <div class="mob-sheet-sub">
                    Assigning to <b id="mobModalSelectedCount">0</b> selected student(s) of <b>{{ $selectedClass->name ?? '' }}</b>
                </div>
            </div>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeAssignSheet()">&times;</button>
    </div>

    <form action="{{ url('stream_update_save') }}" method="POST" id="mobAssignForm">
        @csrf
        <input type="hidden" name="layout" value="mobile">
        <input type="hidden" name="class_type_id" value="{{ $selectedClassId }}">
        <div id="hiddenAdmissionInputs"></div>

        <div class="mob-sheet-body">
            {{-- Quick Stream Presets Horizontal Bar --}}
            <div class="mob-presets-section">
                <div class="mob-presets-label">
                    <span><i class="fa fa-magic text-primary mr-1"></i> Quick Stream Presets</span>
                    <span style="font-size: 9px; font-weight: 600; color: #94a3b8; text-transform: none;">1-Click Select</span>
                </div>
                <div class="mob-presets-scroll-bar">
                    <button type="button" class="mob-preset-pill-btn" data-preset="abc" onclick="applySubjectPreset('abc', this)">
                        🌱 Agri (ABC)
                    </button>
                    <button type="button" class="mob-preset-pill-btn" data-preset="pcm" onclick="applySubjectPreset('pcm', this)">
                        ⚡ Science (PCM)
                    </button>
                    <button type="button" class="mob-preset-pill-btn" data-preset="pcb" onclick="applySubjectPreset('pcb', this)">
                        🔬 Science (PCB)
                    </button>
                    <button type="button" class="mob-preset-pill-btn" data-preset="commerce" onclick="applySubjectPreset('commerce', this)">
                        💼 Commerce
                    </button>
                    <button type="button" class="mob-preset-pill-btn" data-preset="arts" onclick="applySubjectPreset('arts', this)">
                        🎨 Arts
                    </button>
                    <button type="button" class="mob-preset-pill-btn" data-preset="all" onclick="applySubjectPreset('all', this)">
                        ✨ Select All
                    </button>
                </div>
            </div>

            {{-- Subject Selection Tiles Grid --}}
            <div class="mob-subject-select-head">
                <span>Select Subjects (<span id="lblSelectedSubjectCount">0</span> selected)</span>
                <button type="button" class="mob-btn-clear-subjects" onclick="clearSelectedSubjects()">Clear All</button>
            </div>

            <div class="mob-subject-tiles-grid" id="mobSubjectTilesGrid">
                @if(!empty($list_subject) && $subjectCount > 0)
                    @foreach($list_subject as $subj)
                        <div class="mob-subject-tile" id="subject-tile-{{ $subj->id }}" data-id="{{ $subj->id }}" data-subj-name="{{ strtolower($subj->name) }}" onclick="toggleSubjectTile({{ $subj->id }})">
                            <div class="mob-tile-checkbox-wrap">
                                <input type="checkbox" name="subject_id[]" value="{{ $subj->id }}" id="subj-check-{{ $subj->id }}" class="mob-subject-native-check" onclick="event.stopPropagation(); updateSubjectTileUI({{ $subj->id }});">
                                <span class="mob-custom-check-circle"><i class="fa fa-check"></i></span>
                            </div>
                            <div class="mob-tile-info">
                                <span class="mob-tile-name">{{ $subj->name }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-3 text-muted" style="grid-column: span 2; font-size: 11px;">
                        <i class="fa fa-info-circle mb-1 d-block text-warning" style="font-size: 18px;"></i>
                        No subjects available for this class.
                    </div>
                @endif
            </div>
        </div>

        <div class="mob-sheet-action-footer">
            <button type="button" class="btn-sheet-cancel" onclick="closeAssignSheet()">Cancel</button>
            <button type="submit" class="btn-sheet-submit" id="mobBtnSubmitAssign" disabled style="opacity: 0.6;">
                <i class="fa fa-check-circle mr-1"></i> Save Allocation (<span id="lblFooterSubjectCount">0</span>)
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
var streamBaseUrl = "{{ url('/') }}";

// Class Sheet Controls (Native Bottom Sheet)
function openClassSheet() {
    $('#mobClassBackdrop').addClass('show');
    $('#mobClassSheet').addClass('show');
}
function closeClassSheet() {
    $('#mobClassBackdrop').removeClass('show');
    $('#mobClassSheet').removeClass('show');
}

// Assign Subjects Sheet Controls (Native Bottom Sheet)
function openAssignSheet() {
    var selectedIds = [];
    $('.student-check:checked').each(function() {
        selectedIds.push($(this).val());
    });

    if (selectedIds.length === 0) {
        if (typeof window.showMobileConfirm === 'function') {
            window.showMobileConfirm('No Students Selected', 'Please select at least one student before assigning subjects.', function(){}, 'OK', false, 'fa-info-circle text-primary');
        } else {
            alert('Please select at least one student.');
        }
        return;
    }

    var hiddenHtml = '';
    for (var i = 0; i < selectedIds.length; i++) {
        hiddenHtml += '<input type="hidden" name="admission_id[]" value="' + selectedIds[i] + '">';
    }
    $('#hiddenAdmissionInputs').html(hiddenHtml);

    updateSubjectSelectionCount();

    $('#mobAssignBackdrop').addClass('show');
    $('#mobAssignSheet').addClass('show');
}

function closeAssignSheet() {
    $('#mobAssignBackdrop').removeClass('show');
    $('#mobAssignSheet').removeClass('show');
}

// Recency / Status Segment Tabs Filter
var currentStatusFilter = 'all';

function filterByStatus(st, btn) {
    currentStatusFilter = st;
    $('.native-seg-btn').removeClass('active');
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

function resetSearchAndTabs() {
    $('#mobSearchInput').val('');
    $('#mobClearSearch').hide();
    currentStatusFilter = 'all';
    $('.native-seg-btn').removeClass('active');
    $('.native-seg-btn[data-status="all"]').addClass('active');
    applyCombinedFilters();
}

function applyCombinedFilters() {
    var kw = $.trim($('#mobSearchInput').val()).toLowerCase();
    var visibleCount = 0;
    var totalCards = $('.mob-stream-card').length;

    if (totalCards === 0) {
        return;
    }

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

    if (visibleCount === 0) {
        $('#mobSearchEmptyState').show();
    } else {
        $('#mobSearchEmptyState').hide();
    }
}

// Student Selection Management
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

// Subject Tile Selection within Bottom Sheet
function toggleSubjectTile(subjId) {
    var $check = $('#subj-check-' + subjId);
    $check.prop('checked', !$check.prop('checked'));
    updateSubjectTileUI(subjId);
}

function updateSubjectTileUI(subjId) {
    var $tile = $('#subject-tile-' + subjId);
    var $check = $('#subj-check-' + subjId);
    if ($check.is(':checked')) {
        $tile.addClass('is-selected');
    } else {
        $tile.removeClass('is-selected');
        $('.mob-preset-pill-btn').removeClass('active');
    }
    updateSubjectSelectionCount();
}

function updateSubjectSelectionCount() {
    var count = $('.mob-subject-native-check:checked').length;
    $('#lblSelectedSubjectCount').text(count);
    $('#lblFooterSubjectCount').text(count);
    if (count > 0) {
        $('#mobBtnSubmitAssign').prop('disabled', false).css('opacity', '1');
    } else {
        $('#mobBtnSubmitAssign').prop('disabled', true).css('opacity', '0.6');
    }
}

function clearSelectedSubjects() {
    $('.mob-subject-native-check').prop('checked', false);
    $('.mob-subject-tile').removeClass('is-selected');
    $('.mob-preset-pill-btn').removeClass('active');
    updateSubjectSelectionCount();
}

// Stream Presets Auto-Selector
function applySubjectPreset(presetType, btn) {
    $('.mob-preset-pill-btn').removeClass('active');
    if (btn) $(btn).addClass('active');

    var keywords = [];
    if (presetType === 'abc') {
        keywords = ['agri', 'bio', 'chem'];
    } else if (presetType === 'pcm') {
        keywords = ['physic', 'chem', 'math'];
    } else if (presetType === 'pcb') {
        keywords = ['physic', 'chem', 'bio'];
    } else if (presetType === 'commerce') {
        keywords = ['account', 'business', 'eco', 'comm', 'math'];
    } else if (presetType === 'arts') {
        keywords = ['hist', 'pol', 'geo', 'eco', 'socio', 'hindi', 'eng', 'art', 'draw'];
    } else if (presetType === 'all') {
        keywords = ['*'];
    }

    var matchCount = 0;
    $('.mob-subject-tile').each(function() {
        var subName = $(this).attr('data-subj-name') || '';
        var isMatch = false;

        if (presetType === 'all') {
            isMatch = true;
        } else {
            for (var i = 0; i < keywords.length; i++) {
                if (subName.indexOf(keywords[i]) !== -1) {
                    isMatch = true;
                    break;
                }
            }
        }

        var $check = $(this).find('.mob-subject-native-check');
        if (isMatch) {
            $check.prop('checked', true);
            $(this).addClass('is-selected');
            matchCount++;
        } else {
            $check.prop('checked', false);
            $(this).removeClass('is-selected');
        }
    });

    updateSubjectSelectionCount();
}

$('#mobAssignForm').on('submit', function() {
    var checkedSubjs = $('.mob-subject-native-check:checked').length;
    if (checkedSubjs === 0) {
        alert('Please select at least one subject to assign.');
        return false;
    }
    $('#mobBtnSubmitAssign').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
});

// Remove Subject Chip Handlers
function executeRemoveSubject(admId, subId) {
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
                $('#sub-count-' + admId).text(remaining + ' ' + (remaining === 1 ? 'Subject' : 'Subjects'));
                if (remaining === 0) {
                    $('#sub-count-' + admId).removeClass('pill-green').addClass('pill-amber');
                    $('#subject-list-' + admId).html('<span class="mob-empty-subject-badge"><i class="fa fa-info-circle mr-1"></i> No optional subjects assigned</span>');
                    $('#student-card-' + admId).removeClass('has-assigned').addClass('has-unassigned').attr('data-status', 'unassigned');
                    
                    var currentAssigned = parseInt($('#stat-assigned').text().replace(/[^0-9]/g, '')) || 0;
                    var currentUnassigned = parseInt($('#stat-unassigned').text().replace(/[^0-9]/g, '')) || 0;
                    if (currentAssigned > 0) {
                        $('#stat-assigned').text(Math.max(0, currentAssigned - 1));
                        $('#stat-unassigned').text(currentUnassigned + 1);
                    }
                }
            });
        },
        error: function() {
            $chip.css('opacity', 1);
            alert('Failed to remove subject.');
        }
    });
}

function confirmRemoveSubject(admId, subId, subName, stuName) {
    if (typeof window.showMobileConfirm === 'function') {
        window.showMobileConfirm(
            'Remove Subject',
            'Are you sure you want to remove <b>' + subName + '</b> from <b>' + stuName + '</b>?',
            function() {
                executeRemoveSubject(admId, subId);
            },
            'Remove',
            true,
            'fa-trash'
        );
    } else {
        if (confirm('Are you sure you want to remove ' + subName + ' from ' + stuName + '?')) {
            executeRemoveSubject(admId, subId);
        }
    }
}
</script>
@endsection

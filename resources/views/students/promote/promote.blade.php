@extends('layout.app')

@php
    $classType = Helper::classType();
    $studentCount = !empty($data) ? (is_array($data) || $data instanceof \Countable ? count($data) : $data->count()) : 0;
    $selectedClass = collect($classType)->firstWhere('id', (int) ($search['class_type_id'] ?? 0));
    
    $promotedCount = 0;
    $pendingFeesCount = 0;
    $totalPendingAmount = 0;
    
    if(!empty($data)) {
        foreach($data as $st) {
            if(!empty($st->promote_date)) {
                $promotedCount++;
            }
            $pFee = Helper::CarryForwardFees($st->id) ?? 0;
            if($pFee > 0) {
                $pendingFeesCount++;
                $totalPendingAmount += $pFee;
            }
        }
    }
    $eligibleCount = $studentCount - $promotedCount;
@endphp

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - STUDENT SESSION PROMOTION THEME
   - Signature Dark Navy Gradient Hero (#002C54 -> #0f3460)
   - Dual-Thead Sticky Header with In-Column Excel Filters (.excel-filter-row)
   - Target Session & Class Configuration Strip
   - Auto-Fill Sequential Roll Numbers Helper
   - Segmented Promote/Running Status Toggle Pills
   - Real-time Duplicate Roll No Prevention
   - Viewport Auto-Adjust & Pinned Bottom Pagination Toolbar
   ========================================================================== */

:root {
    --header-height: 56px;
    --navy-primary: #002C54;
    --navy-dark: #001f3d;
    --navy-light: #08335c;
    --sky-accent: #0284c7;
}

.promote-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.promote-page * {
    box-sizing: border-box;
}

/* Full Viewport Auto-Adjust Container */
.promote-page-layout {
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: 6px 8px 0;
    gap: 4px;
}

/* 1. Hero Header Banner */
.promote-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 5px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.15);
    flex-shrink: 0;
}
.promote-hero-text {
    display: flex;
    flex-direction: column;
}
.promote-kicker {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #38bdf8;
    font-weight: 700;
}
.promote-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.promote-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Stat Chips */
.promote-hero-stats {
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

.promote-hero-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all .15s ease;
    cursor: pointer;
    border: 1px solid transparent;
    height: 26px;
    line-height: 1;
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

/* 2. Filter Card */
.filter-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
    flex-shrink: 0;
}
.filter-card-header {
    background: #f8fafc;
    padding: 4px 10px;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 11px;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.filter-card-body {
    padding: 5px 10px;
}
.filter-form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 8px;
}
.filter-group {
    display: flex;
    flex-direction: column;
}
.filter-group-class { width: 220px; }
.filter-group-adm { width: 140px; }
.filter-group-keyword { flex: 1; min-width: 180px; }

.filter-label {
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 2px;
}
.filter-input, .filter-select {
    height: 27px;
    width: 100%;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
}
.filter-input:focus, .filter-select:focus {
    border-color: #002C54;
}
.filter-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-filter-search {
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    height: 27px;
    padding: 0 12px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-filter-search:hover {
    background: #08335c;
}
.btn-filter-reset {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 27px;
    padding: 0 10px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.btn-filter-reset:hover {
    background: #e2e8f0;
    color: #0f172a !important;
}

/* 3. Promotion Target Control Center Card */
.promote-target-card {
    background: #ffffff;
    border: 1px solid #93c5fd;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.08);
    flex-shrink: 0;
}
.promote-target-header {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    padding: 4px 10px;
    font-weight: 700;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.promote-target-body {
    padding: 6px 10px;
    background: #f0f9ff;
}
.promote-target-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 8px;
}
.target-field-group {
    display: flex;
    flex-direction: column;
}
.target-field-date { width: 140px; }
.target-field-session { width: 190px; }
.target-field-class { width: 220px; }

.target-label {
    font-size: 10px;
    font-weight: 700;
    color: #0369a1;
    margin-bottom: 2px;
}
.target-input, .target-select {
    height: 28px;
    width: 100%;
    padding: 0 8px;
    font-size: 11.5px;
    font-weight: 700;
    color: #0c4a6e;
    background: #ffffff;
    border: 1px solid #7dd3fc;
    border-radius: 2px;
    outline: none;
}
.target-input:focus, .target-select:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
}

.fees-config-badge {
    height: 28px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 0 10px;
    background: #ffffff;
    border: 1px dashed #0284c7;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    color: #0369a1;
}

/* 4. Table Card Container */
.dash-table-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    overflow: hidden;
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
}

.dash-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 11.5px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 2px 7px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

.table-header-tools {
    display: flex;
    align-items: center;
    gap: 6px;
}
.table-search-input {
    height: 24px;
    padding: 0 8px 0 24px;
    font-size: 11px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    outline: none;
    width: 170px;
    transition: all .15s ease;
}
.table-search-input::placeholder {
    color: #cbd5e1;
}
.table-search-input:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
    width: 210px;
}
.table-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.table-search-wrap i {
    position: absolute;
    left: 7px;
    color: #cbd5e1;
    font-size: 10px;
    pointer-events: none;
}

/* Quick Action Toolbar (Auto-Roll, Carry Forward Toggle) */
.table-quick-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 3px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #475569;
    flex-shrink: 0;
}
.quick-tools-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.quick-tool-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #002C54;
    padding: 2px 7px;
    border-radius: 2px;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all .12s;
}
.quick-tool-btn:hover {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Scrollable Table Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: auto;
    background: #ffffff;
    position: relative;
}

/* Standard Arise Data Table Layout */
.data-table-arise {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
    white-space: nowrap;
}

/* Sticky Headers */
.data-table-arise thead {
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-titles-row th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 11px;
    padding: 4px 8px;
    border: 1px solid #08335c;
    text-align: left;
    height: 32px;
    vertical-align: middle;
}
.excel-filter-row th {
    background: #08335c;
    padding: 2px 4px;
    border: 1px solid #001f3d;
    vertical-align: middle;
}
.excel-col-filter {
    width: 100%;
    height: 22px;
    font-size: 10.5px;
    padding: 0 4px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    outline: none;
    box-sizing: border-box;
}
.excel-col-filter::placeholder {
    color: #cbd5e1;
    font-size: 10px;
}
.excel-col-filter:focus {
    background: #ffffff;
    color: #0f172a;
    border-color: #ffffff;
}
.btn-clear-excel-filters {
    background: #ef4444;
    color: #ffffff;
    border: none;
    border-radius: 2px;
    width: 100%;
    height: 22px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    transition: background .12s;
}
.btn-clear-excel-filters:hover {
    background: #dc2626;
}

/* Rows Styling */
.data-table-arise tbody tr {
    background: #ffffff;
    transition: background-color 0.12s ease;
}
.data-table-arise tbody tr:nth-child(even) {
    background: #f8fafc;
}
.data-table-arise tbody tr:hover {
    background: #edf2f7 !important;
}
.data-table-arise tbody td {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
    vertical-align: middle;
    color: #1e293b;
    font-size: 11px;
}

/* Highlight rows */
.data-table-arise tbody tr.row-pending-fees {
    background: #fffbeb !important;
}
.data-table-arise tbody tr.row-already-promoted {
    background: #fff1f2 !important;
    opacity: .75;
}

/* Student Profile Cell */
.student-profile-cell {
    display: flex;
    align-items: center;
    gap: 6px;
}
.student-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    font-weight: 700;
    flex-shrink: 0;
}
.student-name-text {
    font-weight: 700;
    color: #002C54;
    line-height: 1.2;
}

/* Roll No Input */
.input-roll-no {
    width: 80px;
    height: 24px;
    padding: 0 6px;
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    text-align: center;
}
.input-roll-no:focus {
    border-color: #0284c7;
    background: #f0f9ff;
}
.input-roll-no.is-duplicate {
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
    color: #dc2626 !important;
}

/* Segmented Status Toggle (Promote / Running) */
.status-toggle-group {
    display: inline-flex;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 1px;
    gap: 1px;
}
.status-toggle-btn {
    padding: 2px 8px;
    font-size: 10px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    background: transparent;
    color: #64748b;
    border-radius: 2px;
    transition: all .12s;
}
.status-toggle-btn.active.btn-promote {
    background: #16a34a;
    color: #ffffff;
}
.status-toggle-btn.active.btn-running {
    background: #d97706;
    color: #ffffff;
}

/* 5. Pinned Bottom Toolbar */
.table-pagination-bar {
    background: #002C54;
    color: #ffffff;
    height: 36px;
    padding: 0 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    border-top: 1px solid #08335c;
    flex-shrink: 0;
}
.pagination-info {
    font-size: 11px;
    color: #cbd5e1;
}
.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}
.rows-per-page-selector {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #cbd5e1;
    font-size: 10.5px;
}
.rows-per-page-selector select {
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    padding: 1px 4px;
    font-size: 10.5px;
    outline: none;
    cursor: pointer;
}
.rows-per-page-selector select option {
    background: #002C54;
    color: #ffffff;
}
.pagination-nav {
    display: flex;
    align-items: center;
    gap: 3px;
}
.page-btn {
    width: 25px;
    height: 23px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #051e38;
    color: #ffffff;
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    transition: all .15s;
}
.page-btn:hover:not(:disabled) {
    background: #0284c7;
    border-color: #0284c7;
}
.page-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}
.page-current-indicator {
    font-size: 10.5px;
    font-weight: 600;
    padding: 0 5px;
    color: #f1f5f9;
}

/* Submit Action Button in Footer */
.btn-submit-promote-bar {
    background: #16a34a;
    color: #ffffff;
    border: 1px solid #15803d;
    height: 26px;
    padding: 0 14px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s;
}
.btn-submit-promote-bar:hover:not(:disabled) {
    background: #15803d;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.3);
}

/* 6. Centered Empty State */
.dash-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    flex: 1;
    min-height: calc(100vh - var(--header-height, 56px) - 230px);
    padding: 40px 16px;
    text-align: center;
    background: #ffffff;
    color: #475569;
    box-sizing: border-box;
}
.dash-empty-state .empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
    line-height: 1;
    color: #0284c7;
}
.dash-empty-state .empty-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #0f172a;
}
.dash-empty-state .empty-desc {
    font-size: 12.5px;
    color: #64748b;
    max-width: 480px;
    line-height: 1.5;
}

@media(max-width: 991px) {
    .promote-page-layout {
        height: auto;
        overflow: visible;
        padding: 4px;
    }
    .table-scroll-container {
        max-height: 480px;
    }
    .table-pagination-bar {
        flex-direction: column;
        gap: 6px;
        height: auto;
        padding: 6px 8px;
    }
    .pagination-controls {
        width: 100%;
        justify-content: space-between;
    }
}
</style>

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">

<div class="content-wrapper promote-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="promote-page-layout">

                {{-- 1. Signature Navy Hero Header --}}
                <div class="promote-hero">
                    <div class="promote-hero-text">
                        <span class="promote-kicker">Session Transition &bull; Student Promotion</span>
                        <h1 class="promote-title">
                            <i class="fa fa-graduation-cap text-warning"></i> Student Session Promotion &amp; Transfer
                        </h1>
                        <p class="promote-subtitle">
                            Transfer eligible students into the next academic session, assign new roll numbers, carry forward pending dues, and map session fees.
                        </p>
                    </div>

                    <div class="promote-hero-stats">
                        <div class="hero-stat-chip badge-sky">
                            <i class="fa fa-th-large"></i> Class: <b>{{ $selectedClass->name ?? 'Not Selected' }}</b>
                        </div>
                        <div class="hero-stat-chip badge-emerald">
                            <i class="fa fa-users"></i> Eligible: <b>{{ $eligibleCount }}</b>
                        </div>
                        @if($promotedCount > 0)
                            <div class="hero-stat-chip badge-rose">
                                <i class="fa fa-check-circle"></i> Already Promoted: <b>{{ $promotedCount }}</b>
                            </div>
                        @endif
                        @if($pendingFeesCount > 0)
                            <div class="hero-stat-chip badge-amber">
                                <i class="fa fa-money"></i> Pending Dues: <b>₹ {{ number_format($totalPendingAmount, 2) }}</b> ({{ $pendingFeesCount }})
                            </div>
                        @endif
                    </div>

                    <div class="promote-hero-actions">
                        <a href="{{ url('admissionView') }}" class="dash-btn dash-btn-outline" title="View Students List">
                            <i class="fa fa-list"></i> Student List
                        </a>
                        <a href="{{ url('studentsDashboard') }}" class="dash-btn dash-btn-outline" title="Back to Students Hub">
                            <i class="fa fa-arrow-left"></i> Admission Hub
                        </a>
                    </div>
                </div>

                {{-- 2. Class & Student Search Filter Card --}}
                <div class="filter-card">
                    <div class="filter-card-header">
                        <span><i class="fa fa-filter mr-1"></i> Student Selection Filter</span>
                        <span style="font-size: 10px; font-weight: normal; opacity: .85;">Select current class to load active students</span>
                    </div>
                    <div class="filter-card-body">
                        <form action="{{ url('student/promote_add') }}" method="POST" id="promoteSearchForm" class="filter-form-row">
                            @csrf
                            
                            {{-- Class Selection --}}
                            <div class="filter-group filter-group-class">
                                <label class="filter-label">Current Class: <span class="text-danger">*</span></label>
                                <select name="class_type_id" id="class_type_id" class="filter-select select2" required onchange="document.getElementById('promoteSearchForm').submit()">
                                    <option value="">-- Choose Class --</option>
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}" {{ (int)($search['class_type_id'] ?? 0) === (int)$type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Admission No --}}
                            <div class="filter-group filter-group-adm">
                                <label class="filter-label">Admission No:</label>
                                <input type="text" name="admissionNo" class="filter-input" placeholder="e.g. 1024" value="{{ $search['admissionNo'] ?? '' }}">
                            </div>

                            {{-- Keyword Search --}}
                            <div class="filter-group filter-group-keyword">
                                <label class="filter-label">Keywords:</label>
                                <input type="text" name="name" class="filter-input" placeholder="Student Name, Father Name, Mobile..." value="{{ $search['name'] ?? '' }}">
                            </div>

                            {{-- Actions --}}
                            <div class="filter-actions">
                                <button type="submit" class="btn-filter-search">
                                    <i class="fa fa-search"></i> Load Students
                                </button>
                                @if(!empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']))
                                    <a href="{{ url('student/promote_add') }}" class="btn-filter-reset" title="Reset Filters">
                                        <i class="fa fa-refresh"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. Promotion Workflow Section --}}
                @if(!empty($data) && $studentCount > 0)
                    <form id="form-submit-promote" action="{{ url('studentsPromoteAdd') }}" method="POST" style="display: flex; flex-direction: column; flex: 1; min-height: 0; gap: 4px;">
                        @csrf
                        <input type="hidden" name="fees_master" id="fees_master" value="">

                        {{-- 3.1 Target Configuration Center Card --}}
                        <div class="promote-target-card">
                            <div class="promote-target-header">
                                <span><i class="fa fa-cogs mr-1"></i> Promotion Target Setup &amp; Fee Mapping</span>
                                <span style="font-size: 10px; font-weight: normal; opacity: .9;">Define next session, class and transfer date</span>
                            </div>
                            <div class="promote-target-body">
                                <div class="promote-target-row">
                                    
                                    {{-- Transfer Date --}}
                                    <div class="target-field-group target-field-date">
                                        <label class="target-label">Effective Date: <span class="text-danger">*</span></label>
                                        <input type="date" name="date" id="date" class="target-input" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    {{-- Target Session --}}
                                    <div class="target-field-group target-field-session">
                                        <label class="target-label">Target Session: <span class="text-danger">*</span></label>
                                        <select name="session_id" id="new_session_id" class="target-select" required>
                                            <option value="">-- Select Session --</option>
                                            @if(!empty($session))
                                                @foreach($session as $item)
                                                    <option value="{{ $item->id }}" {{ (Session::get('session_id') + 1 > $item->id) ? 'disabled' : '' }}>
                                                        {{ $item->from_year ?? '' }} - {{ $item->to_year ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    {{-- Promote To Class --}}
                                    <div class="target-field-group target-field-class">
                                        <label class="target-label">Promote To Class: <span class="text-danger">*</span></label>
                                        <select name="promote_class_type_id" id="promote_class_type_id" class="target-select select2" required>
                                            <option value="">-- Choose Target Session First --</option>
                                        </select>
                                    </div>

                                    {{-- Fees Group Mapping Info --}}
                                    <div class="target-field-group">
                                        <label class="target-label">Fees Group Status:</label>
                                        <div class="fees-config-badge" id="feesStatusBadge">
                                            <i class="fa fa-info-circle text-primary"></i> <span id="feesStatusText">Select Class to Configure Fees</span>
                                        </div>
                                    </div>

                                </div>

                                {{-- Warning if no class mapped in target session --}}
                                <div id="no_class_box" class="alert alert-warning p-2 mt-2 mb-0" style="display: none; font-size: 11px;">
                                    <i class="fa fa-exclamation-triangle mr-1"></i>
                                    <b>No classes registered for <span id="selected_new_session_text"></span> session.</b> Please add classes for the new session first.
                                </div>
                            </div>
                        </div>

                        {{-- 3.2 Students Promotion Matrix Table Card --}}
                        <div class="dash-table-card">
                            
                            {{-- Card Header --}}
                            <div class="dash-card-header">
                                <div class="dash-card-title">
                                    <i class="fa fa-users"></i> Students Selected for Session Promotion
                                    <span class="badge-total-records">Total: {{ $studentCount }} Students</span>
                                </div>

                                <div class="table-header-tools">
                                    <div class="table-search-wrap">
                                        <i class="fa fa-search"></i>
                                        <input type="text" id="quickTableSearch" class="table-search-input" placeholder="Quick search in table...">
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Tools Action Bar --}}
                            <div class="table-quick-toolbar">
                                <div class="quick-tools-left">
                                    <button type="button" class="quick-tool-btn" onclick="autoFillSequentialRolls()" title="Auto assign roll numbers 1, 2, 3... to checked students">
                                        <i class="fa fa-sort-numeric-asc text-primary"></i> Auto Sequential Roll Nos
                                    </button>
                                    <button type="button" class="quick-tool-btn" onclick="toggleAllPromoteStatus(1)" title="Set all students to Promote">
                                        <i class="fa fa-rocket text-success"></i> Set All "Promote"
                                    </button>
                                    <button type="button" class="quick-tool-btn" onclick="toggleAllPromoteStatus(2)" title="Set all students to Running">
                                        <i class="fa fa-repeat text-warning"></i> Set All "Running"
                                    </button>
                                </div>
                                <div>
                                    <span style="font-size: 10px; color: #64748b;">
                                        <i class="fa fa-shield text-success mr-1"></i> Real-time duplicate roll number validation active
                                    </span>
                                </div>
                            </div>

                            {{-- Scrollable Table Viewport --}}
                            <div class="table-scroll-container">
                                <table class="data-table-arise" id="promoteTable">
                                    <thead>
                                        {{-- Row 1: Titles --}}
                                        <tr class="header-titles-row">
                                            <th style="width: 40px; text-align: center;">
                                                <input type="checkbox" id="select_all" checked title="Select/Deselect All Eligible">
                                            </th>
                                            <th style="width: 40px; text-align: center;">#</th>
                                            <th style="width: 100px; text-align: center;">Adm. No</th>
                                            <th style="min-width: 200px;">Student Name &amp; Profile</th>
                                            <th style="width: 120px;">Current Class</th>
                                            <th style="width: 140px; text-align: center;">
                                                <input type="checkbox" id="select_allFees" checked title="Carry Forward All Pending Dues">
                                                <span style="margin-left: 2px;">Carry Forward Dues</span>
                                            </th>
                                            <th style="width: 100px; text-align: center;">New Roll No.</th>
                                            <th style="width: 140px; text-align: center;">Promotion Status</th>
                                        </tr>
                                        {{-- Row 2: In-Column Excel Filters --}}
                                        <tr class="excel-filter-row">
                                            <th style="text-align: center;">
                                                <button type="button" class="btn-clear-excel-filters" id="btnClearExcelFilters" title="Clear all column filters">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </th>
                                            <th></th>
                                            <th><input type="text" class="excel-col-filter" data-col="1" placeholder="Filter Adm..."></th>
                                            <th><input type="text" class="excel-col-filter" data-col="2" placeholder="Filter Name..."></th>
                                            <th><input type="text" class="excel-col-filter" data-col="3" placeholder="Filter Class..."></th>
                                            <th><input type="text" class="excel-col-filter" data-col="4" placeholder="Filter Fees..."></th>
                                            <th><input type="text" class="excel-col-filter" data-col="5" placeholder="Filter Roll..."></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="promoteTableBody">
                                        @php $i = 1; @endphp
                                        @foreach($data as $item)
                                            @php
                                                $pendingAmount = Helper::CarryForwardFees($item->id) ?? 0;
                                                $isPromoted = !empty($item->promote_date);
                                                $stName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                                                $initial = strtoupper(substr($item->first_name ?? 'S', 0, 1));
                                            @endphp
                                            <tr class="student-promote-row {{ $pendingAmount > 0 ? 'row-pending-fees' : '' }} {{ $isPromoted ? 'row-already-promoted' : '' }}"
                                                data-index="{{ $i }}"
                                                data-adm="{{ strtolower($item->admissionNo ?? '') }}"
                                                data-name="{{ strtolower($stName) }}"
                                                data-father="{{ strtolower($item->father_name ?? '') }}"
                                                data-class="{{ strtolower($item->ClassTypes->name ?? '') }}"
                                                data-pending="{{ $pendingAmount }}"
                                                data-roll="{{ strtolower($item->roll_no ?? '') }}"
                                                data-promoted="{{ $isPromoted ? '1' : '0' }}">
                                                
                                                {{-- 1. Checkbox --}}
                                                <td style="text-align: center;">
                                                    @if(!$isPromoted)
                                                        <input type="checkbox" class="admission_checkbox" name="admission_ids[]" value="{{ $item->id }}" checked>
                                                    @else
                                                        <span class="badge badge-danger font-weight-bold" style="font-size: 8.5px;" title="Student already promoted on {{ $item->promote_date }}">
                                                            <i class="fa fa-lock"></i> PROMOTED
                                                        </span>
                                                    @endif
                                                </td>

                                                {{-- 2. Index --}}
                                                <td style="text-align: center; color: #64748b; font-weight: 600;">
                                                    {{ $i++ }}
                                                </td>

                                                {{-- 3. Adm No --}}
                                                <td style="text-align: center; font-weight: 700;">
                                                    <code>{{ $item->admissionNo ?: 'N/A' }}</code>
                                                </td>

                                                {{-- 4. Student Name & Father --}}
                                                <td>
                                                    <div class="student-profile-cell">
                                                        <span class="student-avatar">{{ $initial }}</span>
                                                        <div>
                                                            <div class="student-name-text">{{ $stName ?: '-' }}</div>
                                                            @if(!empty($item->father_name))
                                                                <div style="font-size: 9.5px; color: #64748b; line-height: 1;">
                                                                    S/D of {{ $item->father_name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- 5. Class --}}
                                                <td>
                                                    <span class="badge badge-light border font-weight-bold" style="color: #002C54;">
                                                        {{ $item->ClassTypes->name ?? '-' }}
                                                    </span>
                                                </td>

                                                {{-- 6. Pending Fees / Carry Forward --}}
                                                <td style="text-align: center;">
                                                    @if($pendingAmount > 0)
                                                        <label style="cursor: pointer; margin-bottom: 0; display: inline-flex; align-items: center; gap: 4px; font-weight: 700; color: #b45309;">
                                                            <input type="checkbox" class="fees_checkbox" name="carry_forward_ids[]" value="{{ $item->id }}" checked>
                                                            <span>₹ {{ number_format($pendingAmount, 2) }}</span>
                                                        </label>
                                                    @else
                                                        <span class="badge badge-success" style="font-size: 9.5px; font-weight: 600;">
                                                            <i class="fa fa-check"></i> Cleared
                                                        </span>
                                                        <input type="hidden" name="carry_forward_ids[]" value="{{ $item->id }}">
                                                    @endif
                                                </td>

                                                {{-- 7. New Roll No. Input --}}
                                                <td style="text-align: center;">
                                                    <input type="text"
                                                           class="input-roll-no roll_no"
                                                           name="roll_no[{{ $item->id }}]"
                                                           value="{{ $item->roll_no ?? '' }}"
                                                           placeholder="Roll No"
                                                           {{ $isPromoted ? 'disabled' : '' }}>
                                                </td>

                                                {{-- 8. Promotion Status (Promote / Running) --}}
                                                <td style="text-align: center;">
                                                    <div class="status-toggle-group">
                                                        <label class="status-toggle-btn btn-promote active" id="lbl-p-{{ $item->id }}">
                                                            <input type="radio" name="promote_status[{{ $item->id }}]" value="1" checked style="display: none;" onchange="updateRadioUI({{ $item->id }}, 1)">
                                                            <i class="fa fa-level-up"></i> Promote
                                                        </label>
                                                        <label class="status-toggle-btn btn-running" id="lbl-r-{{ $item->id }}">
                                                            <input type="radio" name="promote_status[{{ $item->id }}]" value="2" style="display: none;" onchange="updateRadioUI({{ $item->id }}, 2)">
                                                            <i class="fa fa-refresh"></i> Running
                                                        </label>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pinned Bottom Pagination & Submit Toolbar --}}
                            <div class="table-pagination-bar">
                                <div class="pagination-info">
                                    Showing <span id="page-start" class="font-weight-bold text-white">1</span> to <span id="page-end" class="font-weight-bold text-white">{{ min(25, $studentCount) }}</span> of <span id="total-records" class="font-weight-bold text-white">{{ $studentCount }}</span> students
                                </div>
                                <div class="pagination-controls">
                                    <div class="rows-per-page-selector">
                                        <label for="rows-per-page-select">Rows:</label>
                                        <select id="rows-per-page-select">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                    <div class="pagination-nav">
                                        <button type="button" class="page-btn" id="btn-first" title="First Page" disabled><i class="fa fa-angle-double-left"></i></button>
                                        <button type="button" class="page-btn" id="btn-prev" title="Previous Page" disabled><i class="fa fa-angle-left"></i></button>
                                        <span class="page-current-indicator">Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
                                        <button type="button" class="page-btn" id="btn-next" title="Next Page"><i class="fa fa-angle-right"></i></button>
                                        <button type="button" class="page-btn" id="btn-last" title="Last Page"><i class="fa fa-angle-double-right"></i></button>
                                    </div>
                                    <button type="submit" class="btn-submit-promote-bar" id="btnSubmitPromote" title="Execute Student Promotion">
                                        <i class="fa fa-check-circle"></i> Confirm Promotion
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                @elseif(!empty($search['class_type_id']))
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon text-muted"><i class="fa fa-users"></i></div>
                            <div class="empty-title">No Students Found</div>
                            <div class="empty-desc">No active students found in the selected class. Please verify class selection or filter parameters.</div>
                        </div>
                    </div>
                @else
                    <div class="dash-table-card">
                        <div class="dash-empty-state">
                            <div class="empty-icon text-primary"><i class="fa fa-graduation-cap"></i></div>
                            <div class="empty-title">Select Current Class to Begin Promotion</div>
                            <div class="empty-desc">Choose a current class from the dropdown filter above to load eligible students for session transfer and fee assignment.</div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>
</div>

{{-- 5. Fees Group Selection Modal --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,.3);">
            <div class="modal-header" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); color: #ffffff; padding: 10px 14px;">
                <h5 class="modal-title" style="font-size: 13px; font-weight: 700;">
                    <i class="fa fa-money mr-1 text-warning"></i> Select Applicable Fees for Promoted Class
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" style="color: #fff; opacity: 1;">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div style="background: #f0f9ff; border-bottom: 1px solid #bae6fd; padding: 6px 12px; font-size: 11px; color: #0369a1;">
                    <i class="fa fa-info-circle mr-1"></i> Check all fee groups that should be assigned automatically to promoted students.
                </div>
                <div class="table-responsive" style="max-height: 320px;">
                    <table class="data-table-arise mb-0">
                        <thead>
                            <tr class="header-titles-row">
                                <th style="width: 40px; text-align: center;">Select</th>
                                <th>Session</th>
                                <th>Class</th>
                                <th>Fees Group</th>
                                <th style="text-align: right;">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody id="fees_group_show"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 8px 14px;">
                <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-xs btn-success font-weight-bold" id="save_changes">
                    <i class="fa fa-check mr-1"></i> Save Fee Selections
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 6. Fees Group Error / Warning Modal --}}
<div class="modal fade" id="error_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: none; box-shadow: 0 10px 25px rgba(0,0,0,.3);">
            <div class="modal-header bg-danger text-white py-2 px-3">
                <h5 class="modal-title" style="font-size: 13px; font-weight: 700;">
                    <i class="fa fa-exclamation-triangle mr-1"></i> Fees Group Required
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="color: #fff; background: none; border: none; font-size: 16px;">&times;</button>
            </div>
            <div class="modal-body text-center p-4">
                <div style="font-size: 36px; color: #ef4444; margin-bottom: 8px;">
                    <i class="fa fa-money"></i>
                </div>
                <p class="mb-1 fw-bold text-dark" style="font-size: 13px;">
                    No fees group configured for the selected class and session.
                </p>
                <p class="text-muted mb-0" style="font-size: 11px;">
                    Please create a fee group for the new session to automatically assign student fees, or skip to continue without fees assignment.
                </p>
            </div>
            <div class="modal-footer justify-content-center bg-light py-2">
                <a href="{{ url('fees_group_add') }}" target="_blank" class="btn btn-xs btn-primary px-3 font-weight-bold">
                    <i class="fa fa-plus mr-1"></i> Create Fees Group
                </a>
                <button type="button" class="btn btn-xs btn-outline-secondary px-3" data-bs-dismiss="modal">
                    Skip For Now
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Radio Button UI Toggle
function updateRadioUI(studentId, val) {
    if (val === 1) {
        $('#lbl-p-' + studentId).addClass('active');
        $('#lbl-r-' + studentId).removeClass('active');
    } else {
        $('#lbl-r-' + studentId).addClass('active');
        $('#lbl-p-' + studentId).removeClass('active');
    }
}

// Quick Helper: Set All to Promote or Running
function toggleAllPromoteStatus(val) {
    $('.student-promote-row').not('.filtered-out').each(function() {
        var radio = $(this).find('input[type="radio"][value="' + val + '"]');
        radio.prop('checked', true).trigger('change');
    });
}

// Quick Helper: Auto-Fill Sequential Roll Numbers
function autoFillSequentialRolls() {
    var roll = 1;
    $('.student-promote-row').not('.filtered-out').each(function() {
        var chk = $(this).find('.admission_checkbox');
        if (chk.length && chk.is(':checked')) {
            $(this).find('.roll_no').val(roll++);
        }
    });
    toastr.success('Sequential roll numbers assigned to selected students!');
}

$(document).ready(function() {
    var allRows = $('.student-promote-row');
    var totalRows = allRows.length;
    var currentPage = 1;
    var rowsPerPage = 25;

    // 1. In-Table Quick Search & Excel Column Filters
    function applyFilters() {
        var globalQuery = ($('#quickTableSearch').val() || '').toLowerCase().trim();
        var colFilters = {};

        $('.excel-col-filter').each(function() {
            var colIndex = $(this).data('col');
            var val = ($(this).val() || '').toLowerCase().trim();
            if (val.length > 0) {
                colFilters[colIndex] = val;
            }
        });

        allRows.each(function() {
            var row = $(this);
            var matches = true;

            // Global search
            if (globalQuery.length > 0) {
                var rowText = row.text().toLowerCase();
                if (rowText.indexOf(globalQuery) === -1) {
                    matches = false;
                }
            }

            // Excel Column filters
            if (matches && Object.keys(colFilters).length > 0) {
                for (var col in colFilters) {
                    var fVal = colFilters[col];
                    var cellText = '';
                    if (col === '1') cellText = row.data('adm') || '';
                    else if (col === '2') cellText = (row.data('name') || '') + ' ' + (row.data('father') || '');
                    else if (col === '3') cellText = row.data('class') || '';
                    else if (col === '4') cellText = (row.data('pending') || '') + '';
                    else if (col === '5') cellText = row.find('.roll_no').val() || '';

                    if (cellText.toLowerCase().indexOf(fVal) === -1) {
                        matches = false;
                        break;
                    }
                }
            }

            if (matches) {
                row.removeClass('filtered-out');
            } else {
                row.addClass('filtered-out');
            }
        });

        currentPage = 1;
        paginateTable();
    }

    // 2. Pagination Logic
    function paginateTable() {
        var visibleRows = allRows.not('.filtered-out');
        var count = visibleRows.length;
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(count / rowsPerPage);
        if (totalPages < 1) totalPages = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * (rowsPerPage === 'all' ? count : rowsPerPage);
        var end = rowsPerPage === 'all' ? count : start + rowsPerPage;

        allRows.hide();
        visibleRows.slice(start, end).show();

        // Update UI
        $('#page-start').text(count > 0 ? (start + 1) : 0);
        $('#page-end').text(Math.min(end, count));
        $('#total-records').text(count);
        $('#current-page').text(currentPage);
        $('#total-pages').text(totalPages);

        $('#btn-first, #btn-prev').prop('disabled', currentPage <= 1);
        $('#btn-next, #btn-last').prop('disabled', currentPage >= totalPages || count === 0);
    }

    // Event Bindings for Table Filtering & Pagination
    $('#quickTableSearch').on('keyup input', applyFilters);
    $('.excel-col-filter').on('keyup input', applyFilters);

    $('#btnClearExcelFilters').on('click', function() {
        $('.excel-col-filter').val('');
        $('#quickTableSearch').val('');
        applyFilters();
    });

    $('#rows-per-page-select').on('change', function() {
        var val = $(this).val();
        rowsPerPage = val === 'all' ? 'all' : parseInt(val, 10);
        currentPage = 1;
        paginateTable();
    });

    $('#btn-first').on('click', function() {
        currentPage = 1;
        paginateTable();
    });
    $('#btn-prev').on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            paginateTable();
        }
    });
    $('#btn-next').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            paginateTable();
        }
    });
    $('#btn-last').on('click', function() {
        var visibleRows = allRows.not('.filtered-out');
        var totalPages = rowsPerPage === 'all' ? 1 : Math.ceil(visibleRows.length / rowsPerPage);
        currentPage = totalPages;
        paginateTable();
    });

    // Checkbox Master Controls
    $('#select_all').on('change', function() {
        $('.admission_checkbox').prop('checked', $(this).prop('checked'));
    });
    $(document).on('change', '.admission_checkbox', function() {
        if ($('.admission_checkbox:checked').length === $('.admission_checkbox').length) {
            $('#select_all').prop('checked', true);
        } else {
            $('#select_all').prop('checked', false);
        }
    });

    $('#select_allFees').on('change', function() {
        $('.fees_checkbox').prop('checked', $(this).prop('checked'));
    });
    $(document).on('change', '.fees_checkbox', function() {
        if ($('.fees_checkbox:checked').length === $('.fees_checkbox').length) {
            $('#select_allFees').prop('checked', true);
        } else {
            $('#select_allFees').prop('checked', false);
        }
    });

    // 3. Target Session & Dynamic Class Dropdown Logic
    $('#new_session_id').on('change', function() {
        var new_session_id = $(this).val();
        var session_text = $('#new_session_id option:selected').text().trim();

        $('#promote_class_type_id').html('<option value="">Loading classes...</option>');
        $('#no_class_box').hide();

        if (new_session_id !== '') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: "{{ url('get-class-by-session') }}",
                data: { session_id: new_session_id },
                success: function(response) {
                    var options = '<option value="">-- Select Target Class --</option>';
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            options += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                        $('#promote_class_type_id').html(options);
                    } else {
                        $('#promote_class_type_id').html('<option value="">No Class Found</option>');
                        $('#selected_new_session_text').text(session_text);
                        $('#no_class_box').show();
                    }
                }
            });
        }
    });

    // 4. Target Class Change & Fees Group Modal Trigger
    $('#promote_class_type_id').on('change', function() {
        var newClass = $(this).val();
        var newSession = $('#new_session_id').val();
        $('#fees_master').val('');

        if (newClass !== '' && newSession !== '') {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: "{{ url('getFeesGroup') }}",
                data: {
                    class_type_id: newClass,
                    session_id: newSession
                },
                success: function(data) {
                    var data1 = data.data;
                    if (data1 && data1.length > 0) {
                        var container = $('#fees_group_show');
                        container.html('');
                        data1.forEach(function(item) {
                            var newData = $('<tr>' +
                                '<td style="text-align: center;"><input type="checkbox" class="fees_group_checked" data-id="' + item.id + '" checked></td>' +
                                '<td>' + (item.from_year ?? '') + '-' + (item.to_year ?? '') + '</td>' +
                                '<td><b>' + (item.class_name ?? '') + '</b></td>' +
                                '<td>' + (item.fees_group_name ?? '') + '</td>' +
                                '<td style="text-align: right; font-weight: 700;">₹ ' + (item.amount ?? '0') + '</td>' +
                            '</tr>');
                            container.append(newData);
                        });
                        $('#feesStatusText').text(data1.length + ' Fees Groups Available');
                        $('#myModal').modal('show');
                    } else {
                        $('#feesStatusText').text('No Fees Group Found');
                        $('#error_modal').modal('show');
                    }
                }
            });
        }
    });

    $('#save_changes').on('click', function() {
        var fees_masters = [];
        $('.fees_group_checked:checked').each(function() {
            fees_masters.push($(this).data('id'));
        });
        $('#fees_master').val(fees_masters.join(','));
        $('#feesStatusText').text(fees_masters.length + ' Fees Groups Selected');
        $('#myModal').modal('hide');
    });

    // 5. Real-Time Duplicate Roll Number Prevention
    var typingTimer;
    function checkRollDuplicates(input) {
        var currentValue = input.val().trim();
        if (currentValue === '') {
            input.removeClass('is-duplicate');
            return;
        }

        var isDuplicate = false;
        $('.roll_no').each(function() {
            if (this !== input[0]) {
                var val = $(this).val().trim();
                if (val !== '' && val === currentValue) {
                    isDuplicate = true;
                    return false;
                }
            }
        });

        if (isDuplicate) {
            input.addClass('is-duplicate');
            toastr.error('Duplicate roll number "' + currentValue + '" detected!');
        } else {
            input.removeClass('is-duplicate');
        }
    }

    $(document).on('input', '.roll_no', function() {
        var input = $(this);
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            checkRollDuplicates(input);
        }, 400);
    });

    // 6. Seamless AJAX Form Submission with Confirmation & Feedback
    $('#form-submit-promote').on('submit', function(e) {
        e.preventDefault();

        var targetSession = $('#new_session_id').val();
        var targetClass = $('#promote_class_type_id').val();
        var checkedCount = $('.admission_checkbox:checked').length;

        if (!targetSession) {
            toastr.error('Please select the target academic session.');
            $('#new_session_id').focus();
            return false;
        }

        if (!targetClass) {
            toastr.error('Please select the class to promote students to.');
            $('#promote_class_type_id').focus();
            return false;
        }

        if (checkedCount === 0) {
            toastr.warning('Please select at least one student to promote.');
            return false;
        }

        if (!confirm('Are you sure you want to promote ' + checkedCount + ' student(s) to the selected session and class?')) {
            return false;
        }

        var btn = $('#btnSubmitPromote');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Promoting...');

        var formData = $(this).serialize();

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: "{{ url('studentsPromoteAdd') }}",
            data: formData,
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fa fa-check-circle mr-1"></i> Confirm Promotion');
                if (response.success) {
                    toastr.success(response.message || 'Students promoted successfully!');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1200);
                } else {
                    toastr.error(response.message || 'Promotion failed. Please check form details.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-check-circle mr-1"></i> Confirm Promotion');
                var errMsg = 'An error occurred while promoting students.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                toastr.error(errMsg);
            }
        });
    });

    // Initial render
    paginateTable();
});
</script>
@endsection
@endsection
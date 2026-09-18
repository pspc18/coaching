@php
    $getFeesGroup = Helper::getFeesGroup();
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(11);
    $stats = $stats ?? [
        'total_classes' => count($groupedByClass ?? []),
        'total_heads' => count($allFeesMasters ?? []),
        'total_amount' => 0,
        'in_use_heads' => 0,
    ];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('title', 'Fees Master')

@section('content')
<style>
/* Mobile Fees Master Native Styles (Strict alignment with admissionView & feesGroup) */
.fm-mob-container {
    padding: 8px 10px 75px 10px;
}

/* 1. Hero Card (Exact match with admissionView) */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    border-radius: 6px;
    padding: 10px 12px;
    color: #ffffff;
    margin-bottom: 8px;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.15);
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
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1px 7px;
    border-radius: 10px;
    color: #38bdf8;
}
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-radar-col {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-radar-tag {
    font-size: 9.5px;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: .02em;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 2px;
}
.mob-radar-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
}
.mob-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    margin-top: 2px;
    display: block;
}
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 28px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    text-decoration: none !important;
    border: none;
    transition: opacity .15s ease;
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* 2. Compact Search Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 6px;
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
    padding: 0 8px 0 26px;
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
.mob-filter-btn {
    height: 30px;
    padding: 0 10px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Horizontal Chip Filter Strip */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding: 2px 0 6px 0;
    margin-bottom: 6px;
    -webkit-overflow-scrolling: touch;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip-pill {
    flex-shrink: 0;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-chip-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 3. Class Fee Feed Cards List */
.fm-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.fm-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: border-color .1s ease;
}
.fm-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.fm-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.fm-header-info {
    flex: 1;
    overflow: hidden;
}
.fm-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.fm-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: none; /* No uppercase matching admissionView */
}
.fm-total-badge {
    font-size: 11.5px;
    font-weight: 800;
    color: #0284c7;
    background: #e0f2fe;
    padding: 2px 6px;
    border-radius: 3px;
    white-space: nowrap;
}

/* Sub-pills row */
.fm-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
}
.fm-pill-adm {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.fm-pill-heads {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}

/* Fee Heads Breakdown Grid */
.fm-heads-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
}
.fm-head-chip {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 3px 6px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    color: #334155;
}
.fm-head-chip b {
    color: #002C54;
    font-weight: 700;
}
.fm-head-amount {
    color: #0284c7;
    font-weight: 800;
}
.fm-head-due {
    font-size: 9px;
    color: #64748b;
    background: #f1f5f9;
    padding: 0 3px;
    border-radius: 2px;
}
.fm-btn-del-head {
    background: transparent;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 0 2px;
    font-size: 11px;
    line-height: 1;
}
.fm-head-locked {
    color: #d97706;
    font-size: 9px;
}

/* Action Row (Exact match with admissionView: 29px buttons) */
.fm-card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-action {
    flex: 1;
    height: 29px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    border: 1px solid transparent;
}
.btn-act-edit {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}
.btn-act-edit:active {
    background: #0284c7;
    color: #ffffff;
}

/* 4. Centered Modern Empty States (Available Viewport Centered) */
.mob-empty-state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: calc(100vh - 350px);
    min-height: clamp(260px, 48vh, 500px);
    padding: 32px 18px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    margin: 4px 0 16px 0;
}
.mob-empty-icon-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #64748b;
    margin-bottom: 12px;
}
.mob-empty-icon-circle.icon-accent {
    background: #e0f2fe;
    border-color: #bae6fd;
    color: #0284c7;
}
.mob-empty-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin: 0 0 5px 0;
}
.mob-empty-desc {
    font-size: 11px;
    color: #64748b;
    max-width: 260px;
    line-height: 1.5;
    margin: 0 0 15px 0;
}
.mob-empty-btn {
    height: 32px;
    padding: 0 16px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 4px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease;
}
.mob-empty-btn-reset {
    background: #002C54;
    color: #ffffff;
}
.mob-empty-btn-reset:active {
    background: #001f3b;
}
.mob-empty-btn-add {
    background: #0284c7;
    color: #ffffff;
}
.mob-empty-btn-add:active {
    background: #0369a1;
}

/* 5. Native Slide-Up Bottom Sheet (Exact match with admissionView) */
.mob-filter-modal-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 1050;
    display: none;
    backdrop-filter: blur(2px);
}
.mob-filter-modal-backdrop.show {
    display: block;
}
.mob-filter-sheet {
    position: fixed;
    left: 0; right: 0; bottom: 0;
    background: #ffffff;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    z-index: 1060;
    transform: translateY(100%);
    transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.15);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
}
.mob-filter-sheet.show {
    transform: translateY(0);
}
.mob-sheet-header {
    padding: 10px 14px;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}
.mob-sheet-header.bg-danger-header {
    background: #dc2626;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close {
    width: 24px;
    height: 24px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    cursor: pointer;
    font-size: 12px;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
}
.mob-form-input, .mob-form-select {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-form-input:focus, .mob-form-select:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
}
.btn-sheet-reset {
    flex: 1;
    height: 33px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.btn-sheet-apply {
    flex: 2;
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
.btn-sheet-delete {
    flex: 2;
    height: 33px;
    background: #dc2626;
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

/* Compact Fee Matrix for Mobile Bottom Sheet */
.mob-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10.5px;
}
.mob-matrix-table th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    padding: 5px 6px;
    border-bottom: 1px solid #cbd5e1;
}
.mob-matrix-table td {
    padding: 5px 6px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.mob-matrix-input {
    height: 25px;
    font-size: 10.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 4px;
    width: 100%;
}
</style>

@include('layout.message')

<div class="fm-mob-container">

    {{-- Flash Notifications --}}
    @if(session('message'))
        <div style="margin-bottom:10px; background:#dcfce7; border:1px solid #86efac; color:#166534; padding:8px 12px; border-radius:4px; font-size:11.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-check-circle" style="font-size:14px;"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="margin-bottom:10px; background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:8px 12px; border-radius:4px; font-size:11.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-exclamation-circle" style="font-size:14px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- 1. Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-sliders text-primary"></i> {{ __('fees.Fees Master') }}
            </div>
            <div class="mob-session-pill">
                Session {{ $currentSessionName }}
            </div>
        </div>

        {{-- Symmetrical Metrics Grid --}}
        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-graduation-cap text-primary"></i> Classes
                </span>
                <div class="mob-radar-val">{{ $stats['total_classes'] ?? 0 }}</div>
                <span class="mob-radar-meta">{{ $stats['total_heads'] ?? 0 }} Assigned Heads</span>
            </div>
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-inr text-success"></i> Total Fees
                </span>
                <div class="mob-radar-val" style="color: #38bdf8;">
                    ₹{{ number_format($stats['total_amount'] ?? 0) }}
                </div>
                <span class="mob-radar-meta">{{ $stats['in_use_heads'] ?? 0 }} In-Use Heads</span>
            </div>
        </div>

        {{-- Fast Actions in Hero --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-add" id="btnOpenAssignSheet">
                <i class="fa fa-plus mr-1"></i> + Assign Class Fee
            </button>
            <a href="{{ url('feesGroup') }}" class="mob-act-btn mob-act-btn-filter">
                <i class="fa fa-folder-open mr-1"></i> Fees Group
            </a>
        </div>
    </div>

    {{-- 2. Compact Search Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobQuickSearchInput" class="mob-search-input" placeholder="Search class name...">
        </div>
        <button type="button" class="mob-filter-btn" id="btnResetFilter" title="Reset Filters">
            <i class="fa fa-refresh"></i> Reset
        </button>
    </div>

    {{-- Horizontal Chips Strip --}}
    <div class="mob-chips-scroll">
        <div class="mob-chip-pill active" data-filter="all">All ({{ $stats['total_classes'] ?? 0 }})</div>
        <div class="mob-chip-pill" data-filter="single">Single Head</div>
        <div class="mob-chip-pill" data-filter="multi">Multi Head</div>
        <div class="mob-chip-pill" data-filter="in-use"><i class="fa fa-lock text-warning mr-1"></i>Has In-Use</div>
    </div>

    {{-- 3. Class Fee Feed Cards List --}}
    <div class="fm-feed-list" id="fmFeedContainer">
        @forelse($groupedByClass as $classTypeId => $classFeeMasters)
            @php
                $className = $classFeeMasters->first()->ClassTypes->name ?? 'Class #' . $classTypeId;
                $classNameLower = strtolower($className);
                $totalClassFee = $classFeeMasters->sum('amount');
                $headsCount = $classFeeMasters->count();
                $hasInUse = false;
                foreach($classFeeMasters as $fm) {
                    if (isset($usedDetailGroups[$fm->fees_group_id]) || isset($usedAssignPairs[$classTypeId . '_' . $fm->fees_group_id])) {
                        $hasInUse = true;
                        break;
                    }
                }
            @endphp

            <div class="fm-mob-card" 
                 data-class="{{ $classNameLower }}" 
                 data-heads="{{ $headsCount }}"
                 data-inuse="{{ $hasInUse ? 'yes' : 'no' }}">
                
                {{-- Header Row --}}
                <div class="fm-card-header">
                    <div class="fm-avatar-box">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="fm-header-info">
                        <div class="fm-name-row">
                            <span class="fm-mob-name">{{ $className }}</span>
                            <span class="fm-total-badge">
                                ₹{{ number_format($totalClassFee) }}
                            </span>
                        </div>
                        <div class="fm-pills-wrap">
                            <span class="fm-pill-adm">#{{ $loop->iteration }}</span>
                            <span class="fm-pill-heads">{{ $headsCount }} {{ Str::plural('Head', $headsCount) }}</span>
                            @if($hasInUse)
                                <span class="fm-pill-adm" style="background:#fef3c7; color:#b45309; border-color:#fde68a;">
                                    <i class="fa fa-lock"></i> Active
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Fee Heads Breakdown Grid --}}
                <div class="fm-heads-grid">
                    @foreach($classFeeMasters as $fm)
                        @php
                            $isLocked = isset($usedDetailGroups[$fm->fees_group_id]) || isset($usedAssignPairs[$classTypeId . '_' . $fm->fees_group_id]);
                            $headName = $fm->feesGroup->name ?? 'Group #' . $fm->fees_group_id;
                            $dueFormatted = !empty($fm->installment_due_date) ? date('d M', strtotime($fm->installment_due_date)) : '';
                        @endphp
                        <div class="fm-head-chip">
                            <span><b>{{ $headName }}</b>: <span class="fm-head-amount">₹{{ number_format($fm->amount) }}</span></span>
                            @if(!empty($dueFormatted))
                                <span class="fm-head-due" title="Due Date"><i class="fa fa-calendar-o"></i> {{ $dueFormatted }}</span>
                            @endif
                            @if($isLocked)
                                <span class="fm-head-locked" title="In-Use"><i class="fa fa-lock"></i></span>
                            @else
                                <button type="button" 
                                        class="fm-btn-del-head btn-trigger-delete {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                        data-id="{{ $fm->id }}" 
                                        data-label="{{ $headName }} ({{ $className }})"
                                        title="Delete Fee Head">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Action Row --}}
                <div class="fm-card-actions">
                    <a href="{{ url('feesMasterEdit/' . $classTypeId) }}" 
                       class="mob-btn-action btn-act-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}">
                        <i class="fa fa-edit"></i> Edit Structure
                    </a>
                </div>

            </div>
        @empty
            <div class="mob-empty-state-box">
                <div class="mob-empty-icon-circle icon-accent">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <h6 class="mob-empty-title">No Class Fee Structures</h6>
                <p class="mob-empty-desc">No fees have been assigned to classes yet. Tap the button below to assign your first class fee structure.</p>
                <button type="button" class="mob-empty-btn mob-empty-btn-add" id="btnEmptyAdd">
                    <i class="fa fa-plus"></i> + Assign First Fee
                </button>
            </div>
        @endforelse
    </div>

    {{-- Centered Empty State on Search / Filter --}}
    <div id="fmEmptyState" class="mob-empty-state-box d-none">
        <div class="mob-empty-icon-circle">
            <i class="fa fa-search"></i>
        </div>
        <h6 class="mob-empty-title">No Matching Classes Found</h6>
        <p class="mob-empty-desc">No class fee structures match your search keyword or selected filter chip.</p>
        <button type="button" class="mob-empty-btn mob-empty-btn-reset" id="btnEmptyReset">
            <i class="fa fa-refresh"></i> Reset Filters
        </button>
    </div>

</div>

{{-- =========================================================================
   4. NATIVE BOTTOM SHEET: ASSIGN FEE STRUCTURE TO CLASS
   ========================================================================= --}}
<div class="mob-filter-modal-backdrop" id="mobAssignBackdrop"></div>
<div class="mob-filter-sheet" id="mobAssignSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-plus-circle text-primary"></i> Assign Fees to Class
        </div>
        <div class="mob-sheet-close" id="btnCloseAssignSheet">
            <i class="fa fa-times"></i>
        </div>
    </div>

    <form action="{{ url('feesMasterAdd') }}" method="post">
        @csrf
        <div class="mob-sheet-body">
            <div class="mob-form-group">
                <label class="mob-form-label">{{ __('common.Class') }} <span class="text-danger">*</span></label>
                <select class="mob-form-select" name="class_type_id" required>
                    <option value="">-- Select Class --</option>
                    @if(!empty($classType))
                        @foreach($classType as $type)
                            <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group mt-2">
                <label class="mob-form-label">Fee Heads & Amounts</label>
                <div style="border:1px solid #cbd5e1; border-radius:3px; overflow:hidden;">
                    <table class="mob-matrix-table">
                        <thead>
                            <tr>
                                <th style="width:24px; text-align:center;">
                                    <input type="checkbox" id="mob_select_group" checked style="cursor:pointer;">
                                </th>
                                <th>Head</th>
                                <th style="width:75px; text-align:right;">Amount</th>
                                <th style="width:85px;">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($getFeesGroup))
                                @foreach ($getFeesGroup as $gType)
                                    <tr>
                                        <td style="text-align:center;">
                                            <input type="checkbox" class="mob_group_checkbox" name="fees_group_id[]" value="{{ $gType->id }}" checked>
                                            <input type="hidden" name="editable_value[{{ $gType->id }}]" value="0">
                                        </td>
                                        <td>
                                            <b>{{ $gType->name ?? '' }}</b>
                                        </td>
                                        <td>
                                            <input type="text" class="mob-matrix-input text-right" name="amount[{{ $gType->id }}]" value="0" placeholder="0">
                                        </td>
                                        <td>
                                            <input type="date" class="mob-matrix-input" name="installment_due_date[{{ $gType->id }}]">
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelAssignSheet">
                Cancel
            </button>
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Save Fee Structure
            </button>
        </div>
    </form>
</div>

{{-- =========================================================================
   5. NATIVE BOTTOM SHEET: DELETE CONFIRMATION
   ========================================================================= --}}
<div class="mob-filter-modal-backdrop" id="mobDeleteBackdrop"></div>
<div class="mob-filter-sheet" id="mobDeleteSheet">
    <div class="mob-sheet-header bg-danger-header">
        <div class="mob-sheet-title">
            <i class="fa fa-trash-o"></i> Delete Fee Head
        </div>
        <div class="mob-sheet-close" id="btnCloseDeleteSheet">
            <i class="fa fa-times"></i>
        </div>
    </div>

    <form action="{{ url('feesMasterDelete') }}" method="post">
        @csrf
        <div class="mob-sheet-body text-center" style="padding: 16px 12px;">
            <input type="hidden" id="delete_target_id" name="delete_id">
            <div style="width:42px; height:42px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:18px; margin:0 auto 8px auto;">
                <i class="fa fa-trash"></i>
            </div>
            <p style="font-size:11px; color:#64748b; margin-bottom:3px;">Are you sure you want to delete:</p>
            <h6 style="font-size:13px; font-weight:800; color:#002C54; margin:0;" id="delete_target_name_display"></h6>
        </div>

        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelDeleteSheet">
                Cancel
            </button>
            <button type="submit" class="btn-sheet-delete">
                <i class="fa fa-trash-o mr-1"></i> Yes, Delete
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. Assign Bottom Sheet Handlers
    function openAssignSheet() {
        $('#mobAssignBackdrop').addClass('show');
        $('#mobAssignSheet').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeAssignSheet() {
        $('#mobAssignBackdrop').removeClass('show');
        $('#mobAssignSheet').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#btnOpenAssignSheet').on('click', openAssignSheet);
    $('#btnCloseAssignSheet, #btnCancelAssignSheet, #mobAssignBackdrop').on('click', closeAssignSheet);

    // Matrix Select All Checkbox
    $('#mob_select_group').on('change', function() {
        $('.mob_group_checkbox').prop('checked', $(this).prop('checked'));
    });
    $('.mob_group_checkbox').on('change', function() {
        if ($('.mob_group_checkbox:checked').length === $('.mob_group_checkbox').length) {
            $('#mob_select_group').prop('checked', true);
        } else {
            $('#mob_select_group').prop('checked', false);
        }
    });

    // 2. Delete Bottom Sheet Handlers
    function openDeleteSheet(id, label) {
        $('#delete_target_id').val(id);
        $('#delete_target_name_display').text('"' + label + '"');
        $('#mobDeleteBackdrop').addClass('show');
        $('#mobDeleteSheet').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeDeleteSheet() {
        $('#mobDeleteBackdrop').removeClass('show');
        $('#mobDeleteSheet').removeClass('show');
        $('body').css('overflow', '');
    }

    $(document).on('click', '.btn-trigger-delete', function() {
        var id = $(this).data('id');
        var label = $(this).data('label');
        openDeleteSheet(id, label);
    });

    $('#btnCloseDeleteSheet, #btnCancelDeleteSheet, #mobDeleteBackdrop').on('click', closeDeleteSheet);

    // 3. Search & Filter Chips
    var activeFilterChip = 'all';

    function runSearchFilter() {
        var query = $('#mobQuickSearchInput').val().toLowerCase().trim();
        var matchCount = 0;

        $('.fm-mob-card').each(function() {
            var className = $(this).data('class') || '';
            var heads = parseInt($(this).data('heads')) || 0;
            var inuse = $(this).data('inuse') || '';

            var matchSearch = !query || className.indexOf(query) !== -1;
            var matchChip = true;

            if (activeFilterChip === 'single') {
                matchChip = (heads === 1);
            } else if (activeFilterChip === 'multi') {
                matchChip = (heads > 1);
            } else if (activeFilterChip === 'in-use') {
                matchChip = (inuse === 'yes');
            }

            if (matchSearch && matchChip) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#fmEmptyState').removeClass('d-none');
        } else {
            $('#fmEmptyState').addClass('d-none');
        }
    }

    $('#mobQuickSearchInput').on('keyup input', runSearchFilter);

    $('#btnResetFilter').on('click', function() {
        $('#mobQuickSearchInput').val('');
        $('.mob-chip-pill').removeClass('active');
        $('.mob-chip-pill[data-filter="all"]').addClass('active');
        activeFilterChip = 'all';
        runSearchFilter();
    });

    $('.mob-chip-pill').on('click', function() {
        $('.mob-chip-pill').removeClass('active');
        $(this).addClass('active');
        activeFilterChip = $(this).data('filter');
        runSearchFilter();
    });

    // 4. Empty State Action Triggers
    $(document).on('click', '#btnEmptyReset', function() {
        $('#btnResetFilter').trigger('click');
    });
    $(document).on('click', '#btnEmptyAdd', function() {
        openAssignSheet();
    });
});
</script>
@endsection

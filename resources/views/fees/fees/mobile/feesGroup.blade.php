@php
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(11);
    $inUseGroupIds = $inUseGroupIds ?? [];
    $stats = $stats ?? [
        'total' => is_countable($dataview ?? []) ? count($dataview ?? []) : 0,
        'refundable' => 0,
        'non_refundable' => 0,
        'in_use' => 0,
    ];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEES GROUP VIEW
   100% matched with students/admission/mobile/view.blade.php design standards:
   - Font-family: Plus Jakarta Sans / system fonts
   - Card title: 12.5px font-weight:800 color:#002C54 (Natural title case, NO uppercase)
   - Status badges: 8.5px uppercase font-weight:800
   - ID & sub-pills: 9px font-weight:800 padding:1px 5px
   - Meta grid: 10.5px color:#334155
   - Action buttons: height:29px font-size:11px font-weight:700
   - Native Bottom Sheets (.mob-filter-sheet) with smooth slide-up animation
   ========================================================================== */

.fg-mob-container {
    padding: 0 0 60px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}

/* 1. Glassmorphic Hero Card (Exact match with admissionView) */
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
    margin: 0;
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

/* Dual / Quad Metrics Glance Grid */
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
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
    margin: 1px 0;
}
.mob-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 600;
}

/* Fast Action Bar in Hero */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
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
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 1.5;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn:active {
    transform: scale(0.96);
}

/* 2. Compact Search Toolbar (Exact match with admissionView) */
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
    justify-content: center;
    gap: 4px;
    white-space: nowrap;
}
.mob-filter-btn.active-filter {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0284c7;
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

/* 3. Feed Cards List (Exact match with student-mob-card in admissionView) */
.fg-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.fg-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.fg-mob-card:active {
    border-color: #94a3b8;
}

.fg-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.fg-avatar-box {
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
.fg-header-info {
    flex: 1;
    overflow: hidden;
}
.fg-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.fg-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-transform: none; /* Exact match with admissionView: No uppercase! */
}
.fg-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
}
.status-badge-refundable {
    background: #dcfce7;
    color: #16a34a;
}
.status-badge-standard {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
}

/* Sub-pills row */
.fg-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 1px;
}
.fg-adm-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.fg-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}
.fg-usage-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
}
.usage-badge-inuse {
    background: #fef3c7;
    color: #b45309;
}
.usage-badge-unlinked {
    background: #f8fafc;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
}

/* 2-Column Meta Grid (Exact match with student-meta-grid in admissionView) */
.fg-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 8px;
}
.fg-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fg-meta-item > i {
    width: 13px;
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
}

/* Card Actions Row (Exact match with student-card-actions in admissionView) */
.fg-card-actions {
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
    cursor: pointer;
}
.btn-act-edit {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155 !important;
}
.btn-act-edit:active {
    background: #e2e8f0;
}
.btn-act-delete {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626 !important;
}
.btn-act-delete:active {
    background: #fca5a5;
}
.btn-act-locked {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #94a3b8 !important;
    cursor: not-allowed;
}

/* 4. Native Slide-Up Bottom Sheet (Exact match with mob-filter-sheet in admissionView) */
.mob-filter-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
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
.mob-sheet-header.bg-danger-header {
    background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%);
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    color: #ffffff;
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
    line-height: 1;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
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
    letter-spacing: .03em;
}
.mob-form-input {
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
.mob-form-input:focus {
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
    text-decoration: none !important;
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

/* Centered Modern Empty States */
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
</style>
@endsection

@section('content')
@include('layout.message')
<div class="fg-mob-container">

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

    {{-- 1. Hero Card (Exact match with admissionView) --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-folder-open text-primary"></i> {{ __('fees.Fees Group') }}
            </div>
            <div class="mob-session-pill">
                Session {{ $currentSessionName }}
            </div>
        </div>

        {{-- Symmetrical Metrics Grid --}}
        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-folder text-primary"></i> Total Groups
                </span>
                <div class="mob-radar-val">{{ $stats['total'] ?? 0 }}</div>
                <span class="mob-radar-meta">{{ $stats['refundable'] ?? 0 }} Refundable &bull; {{ $stats['non_refundable'] ?? 0 }} Standard</span>
            </div>
            <div class="mob-radar-col">
                <span class="mob-radar-tag">
                    <i class="fa fa-link text-warning"></i> Active In-Use
                </span>
                <div class="mob-radar-val" style="color: #38bdf8;">
                    {{ $stats['in_use'] ?? 0 }} <span style="font-size: 11px; color: #94a3b8;">Linked</span>
                </div>
                <span class="mob-radar-meta">{{ max(0, ($stats['total'] ?? 0) - ($stats['in_use'] ?? 0)) }} Unlinked / Available</span>
            </div>
        </div>

        {{-- Fast Action Buttons in Hero --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-add" id="btnOpenAddSheet">
                <i class="fa fa-plus mr-1"></i> + New Fee Group
            </button>
            <a href="{{ url('feesMaster') }}" class="mob-act-btn mob-act-btn-filter">
                <i class="fa fa-sliders mr-1"></i> Fees Master
            </a>
        </div>
    </div>

    {{-- 2. Compact Search Toolbar (Exact match with admissionView) --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobQuickSearchInput" class="mob-search-input" placeholder="Search by group name...">
        </div>
        <button type="button" class="mob-filter-btn" id="btnResetFilter" title="Reset Filters">
            <i class="fa fa-refresh"></i> Reset
        </button>
    </div>

    {{-- Horizontal Chips Strip --}}
    <div class="mob-chips-scroll">
        <div class="mob-chip-pill active" data-filter="all">All ({{ $stats['total'] ?? 0 }})</div>
        <div class="mob-chip-pill" data-filter="refundable"><i class="fa fa-check-circle text-success mr-1"></i>Refundable ({{ $stats['refundable'] ?? 0 }})</div>
        <div class="mob-chip-pill" data-filter="non-refundable"><i class="fa fa-minus-circle text-info mr-1"></i>Standard ({{ $stats['non_refundable'] ?? 0 }})</div>
        <div class="mob-chip-pill" data-filter="in-use"><i class="fa fa-link text-warning mr-1"></i>In-Use ({{ $stats['in_use'] ?? 0 }})</div>
    </div>

    {{-- 3. Fee Groups Feed List (Exact match with student-mob-card in admissionView) --}}
    <div class="fg-feed-list" id="fgFeedContainer">
        @forelse($dataview ?? [] as $index => $item)
            @php
                $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                $isInUse = in_array($item->id, $inUseGroupIds);
                $nameLower = strtolower($item->name ?? '');
                $filterType = $isRefundable ? 'refundable' : 'non-refundable';
            @endphp

            <div class="fg-mob-card" 
                 data-name="{{ $nameLower }}" 
                 data-type="{{ $filterType }}"
                 data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                
                {{-- Header Row --}}
                <div class="fg-card-header">
                    <div class="fg-avatar-box">
                        <i class="fa {{ $isRefundable ? 'fa-refresh' : 'fa-money' }}"></i>
                    </div>
                    <div class="fg-header-info">
                        <div class="fg-name-row">
                            <span class="fg-mob-name">{{ $item->name ?? '' }}</span>
                            <span class="fg-status-badge {{ $isRefundable ? 'status-badge-refundable' : 'status-badge-standard' }}">
                                {{ $isRefundable ? 'Refundable' : 'Standard' }}
                            </span>
                        </div>
                        <div class="fg-pills-wrap">
                            <span class="fg-adm-badge">#{{ $index + 1 }} (ID: {{ $item->id }})</span>
                            <span class="fg-class-badge">
                                {{ $item->fees_type === 'installment' ? 'Installment' : 'Full Payment' }}
                            </span>
                            <span class="fg-usage-badge {{ $isInUse ? 'usage-badge-inuse' : 'usage-badge-unlinked' }}">
                                &bull; {{ $isInUse ? 'In-Use' : 'Unlinked' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 2-Column Meta Grid --}}
                <div class="fg-meta-grid">
                    <div class="fg-meta-item">
                        <i class="fa fa-folder-o"></i>
                        <span>Fee Head: <b>{{ $item->fees_type === 'installment' ? 'Installment' : 'Full Pay' }}</b></span>
                    </div>
                    <div class="fg-meta-item">
                        <i class="fa fa-undo"></i>
                        <span>Refund Policy: <b class="{{ $isRefundable ? 'text-success' : 'text-muted' }}">{{ $isRefundable ? 'Eligible' : 'Non-Refund' }}</b></span>
                    </div>
                </div>

                {{-- Action Row (Exact match with student-card-actions in admissionView) --}}
                <div class="fg-card-actions">
                    {{-- Edit Action (Opens Add/Edit Bottom Sheet) --}}
                    <button type="button" 
                       class="mob-btn-action btn-act-edit btn-trigger-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}"
                       data-id="{{ $item->id }}"
                       data-name="{{ $item->name ?? '' }}"
                       data-refund="{{ $isRefundable ? 'yes' : 'no' }}">
                        <i class="fa fa-edit"></i> Edit
                    </button>

                    {{-- Delete Action --}}
                    @if(!$isInUse)
                        <button type="button" 
                                class="mob-btn-action btn-act-delete btn-trigger-delete {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                data-id="{{ $item->id }}" 
                                data-name="{{ $item->name }}">
                            <i class="fa fa-trash-o"></i> Delete
                        </button>
                    @else
                        <button type="button" class="mob-btn-action btn-act-locked" disabled title="In Use">
                            <i class="fa fa-lock"></i> In-Use
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="mob-empty-state-box">
                <div class="mob-empty-icon-circle icon-accent">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <h6 class="mob-empty-title">No Fee Groups Found</h6>
                <p class="mob-empty-desc">No fee groups have been created yet. Tap the button below to add your first group.</p>
                <button type="button" class="mob-empty-btn mob-empty-btn-add" id="btnEmptyAdd">
                    <i class="fa fa-plus"></i> + Add First Group
                </button>
            </div>
        @endforelse
    </div>

    {{-- Empty State on Search / Filter --}}
    <div id="fgEmptyState" class="mob-empty-state-box d-none">
        <div class="mob-empty-icon-circle">
            <i class="fa fa-search"></i>
        </div>
        <h6 class="mob-empty-title">No Matching Fee Groups</h6>
        <p class="mob-empty-desc">No fee groups match your search query or selected filter chips.</p>
        <button type="button" class="mob-empty-btn mob-empty-btn-reset" id="btnEmptyReset">
            <i class="fa fa-refresh"></i> Reset Filters
        </button>
    </div>

</div>

{{-- =========================================================================
   4. NATIVE BOTTOM SHEET: ADD / EDIT FEE GROUP
   ========================================================================= --}}
<div class="mob-filter-modal-backdrop" id="mobAddBackdrop"></div>
<div class="mob-filter-sheet" id="mobAddSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title" id="mobAddSheetTitle">
            <i class="fa fa-plus-circle text-primary"></i> Add Fees Group
        </div>
        <div class="mob-sheet-close" id="btnCloseAddSheet">
            <i class="fa fa-times"></i>
        </div>
    </div>

    <form id="mobGroupForm" action="{{ url('feesGroup') }}" method="post">
        @csrf
        <div class="mob-sheet-body">
            <div class="mob-form-group">
                <label class="mob-form-label">Group Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="sheet_group_name" class="mob-form-input" placeholder="e.g. Tuition Fee, Exam Fee, Hostel Fee" required>
            </div>

            <div class="mob-form-group mt-3">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:3px; padding:10px; display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div style="font-size:11.5px; font-weight:700; color:#0f172a;">Refundable Fee</div>
                        <small style="font-size:9.5px; color:#64748b;">Eligible for refund on admission cancellation?</small>
                    </div>
                    <input type="checkbox" id="sheet_refund_cb" value="yes" style="width:18px; height:18px; cursor:pointer;">
                </div>
                <input type="hidden" id="sheet_refund_input" name="fees_refund" value="no">
            </div>
        </div>

        <div class="mob-sheet-footer">
            <button type="button" class="btn-sheet-reset" id="btnCancelAddSheet">
                Cancel
            </button>
            <button type="submit" class="btn-sheet-apply" id="btnGroupSubmit">
                <i class="fa fa-check mr-1"></i> <span id="btnGroupSubmitText">Save Fee Group</span>
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
            <i class="fa fa-trash-o"></i> Delete Confirmation
        </div>
        <div class="mob-sheet-close" id="btnCloseDeleteSheet">
            <i class="fa fa-times"></i>
        </div>
    </div>

    <form action="{{ url('feesGroupDelete') }}" method="post">
        @csrf
        <div class="mob-sheet-body text-center" style="padding: 16px 12px;">
            <input type="hidden" id="delete_target_id" name="delete_id">
            <div style="width:42px; height:42px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:18px; margin:0 auto 8px auto;">
                <i class="fa fa-trash"></i>
            </div>
            <p style="font-size:11px; color:#64748b; margin-bottom:3px;">Are you sure you want to permanently delete:</p>
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
    var addUrl = "{{ url('feesGroup') }}";
    var editBaseUrl = "{{ url('feesGroupEdit') }}";

    // 1. Add / Edit Bottom Sheet Handlers
    function openAddSheet() {
        $('#mobAddSheetTitle').html('<i class="fa fa-plus-circle text-primary"></i> Add Fees Group');
        $('#mobGroupForm').attr('action', addUrl);
        $('#sheet_group_name').val('');
        $('#sheet_refund_cb').prop('checked', false);
        $('#sheet_refund_input').val('no');
        $('#btnGroupSubmitText').text('Save Fee Group');

        $('#mobAddBackdrop').addClass('show');
        $('#mobAddSheet').addClass('show');
        $('body').css('overflow', 'hidden');
        setTimeout(function() { $('#sheet_group_name').focus(); }, 250);
    }

    function openEditSheet(id, name, refund) {
        $('#mobAddSheetTitle').html('<i class="fa fa-edit text-primary"></i> Edit Fees Group');
        $('#mobGroupForm').attr('action', editBaseUrl + '/' + id);
        $('#sheet_group_name').val(name);
        var isRef = (String(refund).toLowerCase() === 'yes');
        $('#sheet_refund_cb').prop('checked', isRef);
        $('#sheet_refund_input').val(isRef ? 'yes' : 'no');
        $('#btnGroupSubmitText').text('Update Fee Group');

        $('#mobAddBackdrop').addClass('show');
        $('#mobAddSheet').addClass('show');
        $('body').css('overflow', 'hidden');
        setTimeout(function() { $('#sheet_group_name').focus(); }, 250);
    }

    function closeAddSheet() {
        $('#mobAddBackdrop').removeClass('show');
        $('#mobAddSheet').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#btnOpenAddSheet').on('click', openAddSheet);
    $('#btnCloseAddSheet, #btnCancelAddSheet, #mobAddBackdrop').on('click', closeAddSheet);

    $(document).on('click', '.btn-trigger-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var refund = $(this).data('refund');
        openEditSheet(id, name, refund);
    });

    $('#sheet_refund_cb').on('change', function() {
        $('#sheet_refund_input').val($(this).is(':checked') ? 'yes' : 'no');
    });

    // 2. Delete Bottom Sheet Handlers
    function openDeleteSheet(id, name) {
        $('#delete_target_id').val(id);
        $('#delete_target_name_display').text('"' + name + '"');
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
        var name = $(this).data('name');
        openDeleteSheet(id, name);
    });

    $('#btnCloseDeleteSheet, #btnCancelDeleteSheet, #mobDeleteBackdrop').on('click', closeDeleteSheet);

    // 3. Search & Filter Chips (Exact match with admissionView)
    var activeFilterChip = 'all';

    function runSearchFilter() {
        var query = $('#mobQuickSearchInput').val().toLowerCase().trim();
        var matchCount = 0;

        $('.fg-mob-card').each(function() {
            var name = $(this).data('name') || '';
            var type = $(this).data('type') || '';
            var inuse = $(this).data('inuse') || '';

            var matchSearch = !query || name.indexOf(query) !== -1;
            var matchChip = true;

            if (activeFilterChip === 'refundable') {
                matchChip = type === 'refundable';
            } else if (activeFilterChip === 'non-refundable') {
                matchChip = type === 'non-refundable';
            } else if (activeFilterChip === 'in-use') {
                matchChip = inuse === 'yes';
            }

            if (matchSearch && matchChip) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#fgEmptyState').removeClass('d-none');
        } else {
            $('#fgEmptyState').addClass('d-none');
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
        openAddSheet();
    });
});
</script>
@endsection

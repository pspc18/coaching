@php
$getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();
$classType = Helper::classType();
$getState = Helper::getState();
$getcitie = Helper::getCity();
$getgenders = Helper::getgender();
$getCountry = Helper::getCountry();
$bloodGroupType = Helper::bloodGroupType();
$getSession = Helper::getSession();
$permission = Helper::permissioncheck(3);

$admissionStats = $admissionStats ?? [
    'total' => is_countable($data ?? []) ? count($data ?? []) : 0,
    'male' => 0,
    'female' => 0,
    'active' => 0,
    'inactive' => 0,
];
$bloodGroupLookup = $bloodGroupLookup ?? [];
$genderLookup = $genderLookup ?? [];
$feesAssignLookup = $feesAssignLookup ?? [];
$feesPaidLookup = $feesPaidLookup ?? [];
$imageShowPath = env('IMAGE_SHOW_PATH');

$totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 25;
$lastPage = $lastPage ?? 1;
$startIndex = $startIndex ?? 0;
$search = $search ?? [];
$hasActiveFilter = !empty($search['name']) || !empty($search['class_type_id']) || !empty($search['gender_id']) || !empty($search['category']) || (isset($search['status']) && $search['status'] === '0');
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STUDENT ADMISSION / DIRECTORY STYLES
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
}

/* Dual Metrics Glance */
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

/* Fast Action Bar */
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
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-add:active, .mob-act-btn-filter:active {
    transform: scale(0.96);
}

/* 2. Compact Search & Filter Toolbar */
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

/* 3. Student Card Feed Item */
.student-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 48px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.student-mob-card:active {
    border-color: #94a3b8;
}
.student-mob-card.card-inactive {
    border-left: 3px solid #ef4444;
    background: #fffafa;
}

.student-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
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
.student-avatar-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.student-header-info {
    flex: 1;
    overflow: hidden;
}
.student-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.student-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 1px;
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
.student-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
}
.status-badge-active {
    background: #dcfce7;
    color: #16a34a;
}
.status-badge-inactive {
    background: #fee2e2;
    color: #dc2626;
}

/* Student Card Details Grid */
.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 8px;
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
    letter-spacing: -0.01em;
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
    transition: transform .1s ease, background .1s ease;
}
.student-wa-btn:active {
    transform: scale(0.92);
    background: #16a34a;
}
.student-wa-btn i, .student-wa-btn .fa {
    width: auto !important;
    color: #ffffff !important;
    font-size: 11.5px !important;
    line-height: 1 !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

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
.btn-act-profile {
    background: #002C54;
    color: #ffffff !important;
}
.btn-act-fee {
    background: #16a34a;
    color: #ffffff !important;
}
.btn-act-edit {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155 !important;
}
.btn-act-id {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0284c7 !important;
    flex: 0 0 29px;
}

/* 4. Compact Fixed Bottom Pagination Bar */
.mob-pagination-bar {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + var(--safe-bottom, 0px) + 8px);
    left: 8px;
    right: 8px;
    z-index: 990;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.mob-pagination-info {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-pagination-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-page-indicator {
    font-size: 10px;
    font-weight: 800;
    color: #002C54;
    padding: 0 4px;
    min-width: 28px;
    text-align: center;
}
.mob-page-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-page-btn:disabled, .mob-page-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}
.mob-page-btn:not(:disabled):not(.disabled):active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
    transform: scale(0.94);
}

/* 5. Mobile Filter Modal Overlay (Sharp Edges Aligned) */
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
.mob-form-select, .mob-form-input {
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
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
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
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-graduation-cap text-primary"></i> Student Directory
        </div>
        <div class="mob-session-pill">
            Session {{ Session::get('session_name') ?? date('Y') }}
        </div>
    </div>

    {{-- Symmetrical Metrics Grid --}}
    <div class="mob-dual-grid">
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-users text-primary"></i> Total Students
            </span>
            <div class="mob-radar-val">{{ number_format($totalCount) }}</div>
            <span class="mob-radar-meta">{{ $admissionStats['active'] ?? $totalCount }} Active Enrolled</span>
        </div>
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-venus-mars text-warning"></i> Gender Ratio
            </span>
            <div class="mob-radar-val" style="color: #38bdf8;">
                {{ $admissionStats['male'] ?? 0 }} <span style="font-size: 11px; color: #94a3b8;">B</span> &bull; {{ $admissionStats['female'] ?? 0 }} <span style="font-size: 11px; color: #f472b6;">G</span>
            </div>
            <span class="mob-radar-meta">{{ $admissionStats['inactive'] ?? 0 }} Inactive / Left</span>
        </div>
    </div>

    {{-- Fast Action Buttons --}}
    <div class="mob-actions-bar">
        @if($permission->add ?? true)
            <a href="{{ url('admissionAdd') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-user-plus mr-1"></i> + New Admission
            </a>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" id="btnOpenFilterSheet">
            <i class="fa fa-filter mr-1"></i> Filters @if($hasActiveFilter)<span class="badge badge-warning" style="font-size: 8px; background: #f59e0b; color: #fff; padding: 1px 4px; border-radius: 2px;">Active</span>@endif
        </button>
    </div>
</div>

{{-- 2. Compact Search Toolbar --}}
<div class="mob-search-toolbar">
    <div class="mob-search-input-wrap">
        <i class="fa fa-search"></i>
        <input type="text" id="mobQuickSearchInput" class="mob-search-input" placeholder="Search by name, adm no, phone..." value="{{ $search['name'] ?? '' }}">
    </div>
    <button type="button" class="mob-filter-btn {{ $hasActiveFilter ? 'active-filter' : '' }}" id="btnOpenFilterSheet2">
        <i class="fa fa-sliders"></i> Filter
    </button>
    @if($hasActiveFilter)
        <a href="{{ url('admissionView?reset=1') }}" class="mob-filter-btn text-danger" title="Reset Filters">
            <i class="fa fa-times"></i>
        </a>
    @endif
</div>

{{-- 3. Student Card Feed --}}
<div class="student-feed-list" id="studentFeedContainer">
    @forelse($data as $item)
        @php
            $fullName = trim(($item['first_name'] ?? '') . ' ' . ($item['last_name'] ?? ''));
            $className = $item['class_name'] ?? ($item['ClassTypes']['name'] ?? 'Class');
            $bloodGroupText = $bloodGroupLookup[$item->blood_group] ?? '';
            $genderText = $genderLookup[$item->gender_id] ?? '';
            $isInactive = (int) ($item->status ?? 1) === 0;
            $assignAmt = (float) ($feesAssignLookup[$item->id] ?? 0);
            $paidAmt = (float) ($feesPaidLookup[$item->id] ?? 0);
            $paidPct = $assignAmt > 0 ? round(($paidAmt / $assignAmt) * 100, 1) : 0;
        @endphp

        <div class="student-mob-card {{ $isInactive ? 'card-inactive' : '' }}" data-name="{{ strtolower($fullName) }}" data-adm="{{ strtolower($item['admissionNo'] ?? '') }}" data-class="{{ strtolower($className) }}" data-mobile="{{ strtolower($item['mobile'] ?? '') }}">
            
            {{-- Header Row --}}
            <div class="student-card-header">
                <div class="student-avatar-box">
                    @if(!empty($item['image']))
                        <img src="{{ $imageShowPath }}profile/{{ $item['image'] }}" alt="{{ $fullName }}" onerror="this.src='{{ $imageShowPath }}default/user_image.jpg'">
                    @else
                        {{ strtoupper(substr($fullName ?: 'S', 0, 1)) }}
                    @endif
                </div>
                <div class="student-header-info">
                    <div class="student-name-row">
                        <a href="{{ url('studentDetail/'.$item->id) }}" class="student-mob-name">{{ $fullName ?: 'Student #' . ($item['admissionNo'] ?? $item->id) }}</a>
                        <span class="student-status-badge {{ $isInactive ? 'status-badge-inactive' : 'status-badge-active' }}">
                            {{ $isInactive ? 'Inactive' : 'Active' }}
                        </span>
                    </div>
                    <div class="student-pills-wrap">
                        <span class="student-adm-badge">#{{ $item['admissionNo'] ?? $item->id }}</span>
                        <span class="student-class-badge">{{ $className }}</span>
                        @if(!empty($genderText))
                            <span style="font-size: 8.5px; color: #64748b; font-weight: 700;">&bull; {{ $genderText }}</span>
                        @endif
                        @if(!empty($bloodGroupText))
                            <span style="font-size: 8.5px; color: #dc2626; font-weight: 700;">&bull; {{ $bloodGroupText }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2-Column Meta Grid --}}
            <div class="student-meta-grid">
                <div class="student-meta-item">
                    <i class="fa fa-user-o"></i>
                    <span><b>F:</b> {{ Str::limit($item['father_name'] ?? '-', 15) }}</span>
                </div>
                <div class="student-meta-item">
                    <i class="fa fa-female"></i>
                    <span><b>M:</b> {{ Str::limit($item['mother_name'] ?? '-', 15) }}</span>
                </div>
                <div class="student-meta-item" style="overflow: visible;">
                    <i class="fa fa-phone"></i>
                    @if(!empty($item['mobile']))
                        <a href="tel:{{ $item['mobile'] }}" class="student-phone-link">{{ $item['mobile'] }}</a>
                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $item['mobile']) }}" target="_blank" class="student-wa-btn" title="Chat on WhatsApp">
                            <i class="fa fa-whatsapp"></i>
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>
                <div class="student-meta-item">
                    <i class="fa fa-inr"></i>
                    <span>Fee: <b class="{{ $paidPct >= 100 ? 'text-success' : ($paidPct > 0 ? 'text-primary' : 'text-danger') }}">{{ $paidPct }}%</b></span>
                </div>
            </div>

            {{-- Action Row --}}
            <div class="student-card-actions">
                @if($permission->view ?? true)
                    <a href="{{ url('studentDetail/'.$item->id) }}" class="mob-btn-action btn-act-profile">
                        <i class="fa fa-id-badge"></i> Profile
                    </a>
                @endif
                <a href="{{ url('feesCollectAdd?student_id='.$item->id) }}" class="mob-btn-action btn-act-fee">
                    <i class="fa fa-inr"></i> Fee
                </a>
                @if($permission->edit ?? true)
                    <a href="{{ url('admissionEdit/'.$item->id) }}" class="mob-btn-action btn-act-edit">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ url('admissionStudentIdPrint/'.$item->id) }}" target="_blank" class="mob-btn-action btn-act-id" title="Student ID Card">
                    <i class="fa fa-credit-card"></i>
                </a>
            </div>

        </div>
    @empty
        <div class="text-center py-4 text-muted" style="background: #fff; border-radius: 4px; border: 1px solid #cbd5e1;">
            <i class="fa fa-graduation-cap fa-3x text-muted mb-2 d-block" style="opacity: 0.5;"></i>
            <div style="font-size: 13px; font-weight: 700; color: #475569;">No students found</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Try adjusting your filters or search keywords.</div>
            @if($hasActiveFilter)
                <a href="{{ url('admissionView?reset=1') }}" class="btn-sheet-reset mt-2 d-inline-block" style="padding: 4px 12px; font-size: 11px;">
                    Reset Filters
                </a>
            @endif
        </div>
    @endforelse
</div>

{{-- 4. Compact Pagination Footer --}}
@if($lastPage > 1 || $totalCount > 0)
    <div class="mob-pagination-bar">
        <div class="mob-pagination-info">
            Showing <b>{{ $totalCount > 0 ? $startIndex + 1 : 0 }}</b> - <b>{{ min($startIndex + count($data), $totalCount) }}</b> of <b>{{ $totalCount }}</b>
        </div>
        <div class="mob-pagination-btns">
            <a href="{{ url('admissionView') }}?page={{ max(1, $currentPage - 1) }}" class="mob-page-btn {{ $currentPage <= 1 ? 'disabled' : '' }}">
                <i class="fa fa-chevron-left"></i> Prev
            </a>
            <span style="font-size: 10.5px; font-weight: 800; color: #002C54; padding: 3px 6px;">{{ $currentPage }} / {{ $lastPage }}</span>
            <a href="{{ url('admissionView') }}?page={{ min($lastPage, $currentPage + 1) }}" class="mob-page-btn {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                Next <i class="fa fa-chevron-right"></i>
            </a>
        </div>
    </div>
@endif

{{-- 5. Mobile Filter Bottom Sheet Modal --}}
<div class="mob-filter-modal-backdrop" id="mobFilterBackdrop"></div>
<div class="mob-filter-sheet" id="mobFilterSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-filter text-primary"></i> Filter Student Directory
        </div>
        <button type="button" class="mob-sheet-close" id="btnCloseFilterSheet">&times;</button>
    </div>
    
    <form action="{{ url('admissionView') }}" method="POST" id="mobFilterForm">
        @csrf
        <div class="mob-sheet-body">
            
            <div class="mob-form-group">
                <label class="mob-form-label">Search Keywords</label>
                <input type="text" name="name" class="mob-form-input" placeholder="Student name, father, phone, adm no..." value="{{ $search['name'] ?? '' }}">
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Class</label>
                <select name="class_type_id" class="mob-form-select">
                    <option value="">All Classes</option>
                    @if(!empty($classType))
                        @foreach($classType as $type)
                            <option value="{{ $type->id }}" {{ (string)($search['class_type_id'] ?? '') === (string)$type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Gender</label>
                <select name="gender_id" class="mob-form-select">
                    <option value="">All Genders</option>
                    @if(!empty($getgenders))
                        @foreach($getgenders as $gen)
                            <option value="{{ $gen->id }}" {{ (string)($search['gender_id'] ?? '') === (string)$gen->id ? 'selected' : '' }}>
                                {{ $gen->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Enrollment Status</label>
                <select name="status" class="mob-form-select">
                    <option value="1" {{ (string)($search['status'] ?? '1') === '1' ? 'selected' : '' }}>Active Students</option>
                    <option value="0" {{ (string)($search['status'] ?? '') === '0' ? 'selected' : '' }}>Inactive / Left Students</option>
                    <option value="" {{ (string)($search['status'] ?? '') === '' ? 'selected' : '' }}>All Students</option>
                </select>
            </div>

        </div>

        <div class="mob-sheet-footer">
            <a href="{{ url('admissionView?reset=1') }}" class="btn-sheet-reset">
                Reset
            </a>
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Apply Filters
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Filter Sheet Trigger
    function openFilterSheet() {
        $('#mobFilterBackdrop').addClass('show');
        $('#mobFilterSheet').addClass('show');
        $('body').css('overflow', 'hidden');
    }
    function closeFilterSheet() {
        $('#mobFilterBackdrop').removeClass('show');
        $('#mobFilterSheet').removeClass('show');
        $('body').css('overflow', '');
    }

    $('#btnOpenFilterSheet, #btnOpenFilterSheet2').on('click', openFilterSheet);
    $('#btnCloseFilterSheet, #mobFilterBackdrop').on('click', closeFilterSheet);

    // Client-side instant filter on typing in toolbar input
    $('#mobQuickSearchInput').on('keyup', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.student-mob-card').each(function() {
            var name = $(this).data('name') || '';
            var adm = $(this).data('adm') || '';
            var cls = $(this).data('class') || '';
            var mobile = $(this).data('mobile') || '';

            if (name.indexOf(query) !== -1 || adm.indexOf(query) !== -1 || cls.indexOf(query) !== -1 || mobile.indexOf(query) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Enter key submit on quick search input
    $('#mobQuickSearchInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var val = $(this).val();
            window.location.href = "{{ url('admissionView') }}?name=" + encodeURIComponent(val);
        }
    });
});
</script>
@endsection
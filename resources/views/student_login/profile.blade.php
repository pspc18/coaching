@php
    $getUser = Helper::getUser();
    $getSetting = Helper::getSetting();
    $fullName = trim(($data->first_name ?? '').' '.($data->last_name ?? ''));
    if (empty($fullName)) {
        $fullName = 'Student';
    }
    $className = optional($data->ClassTypes)->name ?? (optional($getUser)->ClassTypes->name ?? '-');
    $sectionName = optional($data->Section)->name ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');
    
    $hasPhoto = !empty($data->image);
    $imageUrl = $hasPhoto ? env('IMAGE_SHOW_PATH').'/profile/'.$data->image : '';

    $profileCompletion = $profileCompletion ?? 85;
    $completedFields = $completedFields ?? 7;
    $totalFields = $totalFields ?? 9;
    $canEditProfile = true;
@endphp
@extends('student_login.layout.app')

@section('title', 'My Profile')
@section('page_title', 'MY PROFILE')
@section('page_sub', $fullName)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STUDENT PROFILE
   Exact Layout, Typography & CSS matching admissionView?layout=mobile
   and Sticky Wizard Navigation matching admissionAdd?layout=mobile
   ========================================================================== */

html, body {
    overflow-x: hidden;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
}
body.modal-open {
    overflow: hidden !important;
}

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

/* Hero Profile Identity Header */
.mob-profile-id-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    margin-bottom: 8px;
}
.mob-avatar-wrap {
    position: relative;
    width: 48px;
    height: 48px;
    min-width: 48px;
    min-height: 48px;
    flex-shrink: 0;
}
.mob-avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    object-fit: cover;
    background: #0f172a;
}
.mob-avatar-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.35);
    background: #0284c7;
    color: #ffffff;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 800;
}
.mob-avatar-cam-btn {
    position: absolute;
    bottom: -3px;
    right: -3px;
    width: 19px;
    height: 19px;
    border-radius: 3px;
    background: #f59e0b;
    color: #ffffff;
    border: 1px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    cursor: pointer;
    box-shadow: 0 1px 4px rgba(0,0,0,0.35);
    z-index: 2;
}
.mob-avatar-spinner {
    position: absolute;
    inset: 0;
    background: rgba(0, 24, 51, 0.8);
    border-radius: 4px;
    display: none;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 14px;
    z-index: 3;
}
.mob-profile-id-info {
    flex: 1;
    min-width: 0;
}
.mob-profile-name {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-profile-meta-row {
    font-size: 10px;
    color: #cbd5e1;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.mob-profile-meta-row b {
    color: #38bdf8;
}

/* 4-Column Metrics Glance (2x2 Grid) matching admissionView */
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
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 1px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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

/* Hero Progress Strip */
.mob-hero-progress-strip {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.progress-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-bottom: 4px;
}
.progress-meta-row b {
    color: #38bdf8;
    font-weight: 800;
}
.progress-track-bg {
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 2px;
    overflow: hidden;
}
.progress-track-fill {
    height: 100%;
    background: #10b981;
    border-radius: 2px;
    transition: width .4s ease;
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

/* 2. Interactive Step / Tab Tracker Container (Fixed on Scroll like admissionAdd?layout=mobile) */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 8px;
}
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 0;
    display: flex;
    gap: 5px;
    align-items: center;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-x;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    scrollbar-width: none;
    transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.mob-search-toolbar::-webkit-scrollbar {
    display: none;
}

/* Fixed Sticky Mode matching admissionAdd wizard step */
.mob-search-toolbar.is-fixed {
    position: fixed !important;
    top: 52px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: calc(100% - 16px) !important;
    max-width: 464px !important;
    z-index: 999 !important;
    margin: 0 !important;
    background: rgba(0, 24, 51, 0.96) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    border: 1px solid #0284c7 !important;
    border-radius: 4px !important;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.45) !important;
}
.mob-filter-btn {
    flex: 0 0 auto;
    height: 28px;
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
    transition: all .12s ease;
    user-select: none;
}
.mob-filter-btn.active-filter {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-filter-btn:active {
    transform: scale(0.96);
}

/* Buttons style when toolbar is fixed on dark navy */
.mob-search-toolbar.is-fixed .mob-filter-btn {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.2);
    color: #cbd5e1;
}
.mob-search-toolbar.is-fixed .mob-filter-btn.active-filter {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.4);
}

/* 3. Feed List & Card Item */
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
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .15s ease, box-shadow .15s ease;
}
.student-mob-card:active {
    border-color: #94a3b8;
}

/* Status Left Accents */
.card-navy {
    border-left: 3px solid #002C54;
}
.card-personal {
    border-left: 3px solid #0284c7;
}
.card-family {
    border-left: 3px solid #16a34a;
}
.card-contact {
    border-left: 3px solid #f59e0b;
}
.card-academic {
    border-left: 3px solid #6366f1;
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
    width: 34px;
    height: 34px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
}
.student-avatar-box.avatar-personal {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}
.student-avatar-box.avatar-family {
    background: #dcfce7;
    color: #16a34a;
    border-color: #86efac;
}
.student-avatar-box.avatar-contact {
    background: #fef3c7;
    color: #d97706;
    border-color: #fde68a;
}
.student-avatar-box.avatar-academic {
    background: #ede9fe;
    color: #6d28d9;
    border-color: #ddd6fe;
}
.student-avatar-box.avatar-security {
    background: #e0e7ff;
    color: #3730a3;
    border-color: #c7d2fe;
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
    white-space: nowrap;
}
.status-badge-active {
    background: #dcfce7;
    color: #16a34a;
}
.status-badge-warning {
    background: #fef3c7;
    color: #b45309;
}

/* Card Details Grid */
.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 5px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 4px;
}
.student-meta-item {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-meta-item > i {
    width: 13px;
    font-size: 11px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
    margin-top: 2px;
}
.student-meta-item span {
    color: #64748b;
}
.student-meta-item b {
    color: #0f172a;
    font-weight: 700;
}
.student-meta-full {
    grid-column: span 2;
}

/* Card Actions Row */
.student-card-actions {
    display: flex;
    gap: 5px;
    padding-top: 7px;
    margin-top: 5px;
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
    transition: all .12s ease;
}
.mob-btn-action:active {
    transform: scale(0.96);
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

/* Standalone Clean Modals (Independent of external bootstrap CSS) */
.mob-modal {
    position: fixed !important;
    inset: 0 !important;
    z-index: 2100 !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    background: rgba(0, 15, 30, 0.72) !important;
    backdrop-filter: blur(4px) !important;
    -webkit-backdrop-filter: blur(4px) !important;
}
.mob-modal:not(.show) {
    display: none !important;
}
.mob-modal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.modal-backdrop {
    display: none !important;
}
.mob-modal .modal-dialog {
    width: 92%;
    max-width: 440px;
    margin: auto;
}
.mob-modal .modal-content {
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 10px 30px rgba(0, 44, 84, 0.25);
    background: #ffffff;
    overflow: hidden;
}
.mob-modal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-bottom: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-modal .modal-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.mob-modal .btn-close {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.8;
}
.mob-modal .modal-body {
    padding: 12px 14px;
    background: #f8fafc;
}
.mob-modal .modal-footer {
    padding: 8px 14px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
}
.mob-input-shell {
    position: relative;
    margin-bottom: 10px;
}
.mob-input-label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
}
.mob-input-box {
    position: relative;
}
.mob-input-box > i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 12px;
}
.mob-input-ctrl {
    width: 100%;
    height: 34px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 0 10px 0 30px;
    font-size: 11.5px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    transition: border-color .15s ease;
}
.mob-input-ctrl:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-input-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 11.5px;
    color: #0f172a;
    background: #ffffff;
    outline: none;
    resize: none;
}
.mob-input-textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-pw-toggle {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #64748b;
    padding: 4px 6px;
    cursor: pointer;
}
.mob-strength-bar {
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    margin-top: 4px;
    overflow: hidden;
}
.mob-strength-fill {
    height: 100%;
    width: 0%;
    transition: width .25s ease, background-color .25s ease;
}
.mob-alert-box {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
    border-radius: 4px;
    padding: 6px 10px;
    font-size: 11px;
    margin-bottom: 10px;
    display: none;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card matching admissionView --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-user-circle text-warning"></i> Student Profile
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-check-circle"></i> Active Student
        </div>
    </div>

    {{-- Hero Profile Identity Header --}}
    <div class="mob-profile-id-box">
        <div class="mob-avatar-wrap">
            @if($hasPhoto)
                <img 
                    src="{{ $imageUrl }}" 
                    alt="{{ $fullName }}" 
                    id="heroAvatarImg" 
                    class="mob-avatar-img"
                    style="display: block;"
                    onerror="this.style.display='none'; document.getElementById('heroAvatarPlaceholder').style.display='flex';"
                >
                <div id="heroAvatarPlaceholder" class="mob-avatar-placeholder" style="display: none;">
                    {{ strtoupper(substr($fullName, 0, 1)) ?: 'S' }}
                </div>
            @else
                <img 
                    id="heroAvatarImg" 
                    class="mob-avatar-img" 
                    style="display: none;" 
                    alt="{{ $fullName }}"
                >
                <div id="heroAvatarPlaceholder" class="mob-avatar-placeholder" style="display: flex;">
                    {{ strtoupper(substr($fullName, 0, 1)) ?: 'S' }}
                </div>
            @endif

            <button type="button" class="mob-avatar-cam-btn" id="changePhotoBtn" title="Change Photo" aria-label="Change photo">
                <i class="fa fa-camera"></i>
            </button>

            <div class="mob-avatar-spinner" id="avatarLoader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
        <div class="mob-profile-id-info">
            <div class="mob-profile-name" id="heroStudentName">{{ $fullName }}</div>
            <div class="mob-profile-meta-row">
                <span>Adm No: <b>#{{ $data->admissionNo ?: '-' }}</b></span>
                <span>• Session: <b>{{ $sessionName }}</b></span>
            </div>
            <div style="font-size: 9.5px; color: #94a3b8; margin-top: 2px;">
                {{ $getSetting->name ?? 'Coaching Institute' }}
            </div>
        </div>
    </div>

    {{-- Symmetrical 4-Column Metrics Glance (2x2 Grid) --}}
    <div class="mob-dual-grid">
        {{-- Radar 1: Admission No --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-id-badge text-info"></i> Admission No
            </span>
            <div class="mob-radar-val" style="color: #38bdf8;">
                #{{ $data->admissionNo ?: '-' }}
            </div>
            <span class="mob-radar-meta">
                Enrollment ID
            </span>
        </div>

        {{-- Radar 2: Roll Number / UID --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-id-card-o text-success"></i> Roll Number
            </span>
            <div class="mob-radar-val" style="color: #34d399;">
                {{ $data->roll_no ?: '-' }}
            </div>
            <span class="mob-radar-meta">
                UID: {{ $data->userName ?: '-' }}
            </span>
        </div>

        {{-- Radar 3: Registered Contact --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-phone text-warning"></i> Mobile No
            </span>
            <div class="mob-radar-val" style="font-size: 13.5px;" id="heroMobile">
                {{ $data->mobile ?: '-' }}
            </div>
            <span class="mob-radar-meta" id="heroEmail">
                {{ $data->email ? Str::limit($data->email, 16) : 'No email added' }}
            </span>
        </div>

        {{-- Radar 4: Profile Completion --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-pie-chart text-primary"></i> Profile Score
            </span>
            <div class="mob-radar-val" style="color: {{ $profileCompletion >= 80 ? '#34d399' : '#fbbf24' }};">
                {{ $profileCompletion }}%
            </div>
            <span class="mob-radar-meta">
                {{ $completedFields }}/{{ $totalFields }} verified
            </span>
        </div>
    </div>

    {{-- Profile Completeness Progress Strip inside Hero --}}
    <div class="mob-hero-progress-strip">
        <div class="progress-meta-row">
            <span>Profile Completeness</span>
            <b>{{ $profileCompletion }}% Verified</b>
        </div>
        <div class="progress-track-bg">
            <div class="progress-track-fill" style="width: {{ $profileCompletion }}%;"></div>
        </div>
    </div>

    {{-- Fast Action Bar matching admissionView --}}
    <div class="mob-actions-bar">
        <button type="button" class="mob-act-btn mob-act-btn-add" data-bs-toggle="modal" data-bs-target="#editContactModal">
            <i class="fa fa-pencil-square-o"></i> Edit Contact
        </button>
        <button type="button" class="mob-act-btn mob-act-btn-filter" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fa fa-shield"></i> Password & Security
        </button>
    </div>
</div>

{{-- 2. Compact Segment Toolbar (Sticky when scrolled past, matching admissionAdd wizard tracker) --}}
<div class="mob-tracker-container" id="trackerContainer">
    <div class="mob-search-toolbar" id="mobStepTracker">
        <button type="button" class="mob-filter-btn active-filter" data-filter="all" onclick="filterSections('all', this)">
            <i class="fa fa-th-large"></i> All Info
        </button>
        <button type="button" class="mob-filter-btn" data-filter="personal" onclick="filterSections('personal', this)">
            <i class="fa fa-user"></i> Personal
        </button>
        <button type="button" class="mob-filter-btn" data-filter="family" onclick="filterSections('family', this)">
            <i class="fa fa-users"></i> Parents
        </button>
        <button type="button" class="mob-filter-btn" data-filter="contact" onclick="filterSections('contact', this)">
            <i class="fa fa-phone"></i> Contact
        </button>
        <button type="button" class="mob-filter-btn" data-filter="academic" onclick="filterSections('academic', this)">
            <i class="fa fa-graduation-cap"></i> Academic
        </button>
    </div>
</div>

{{-- Hidden File Input for Photo Upload --}}
<input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display: none;">

{{-- 3. Detailed Information Cards Feed --}}
<div class="student-feed-list">

    {{-- CARD 1: Personal Information --}}
    <div class="student-mob-card card-personal profile-section" id="card-personal" data-category="personal">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-personal">
                <i class="fa fa-user"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Personal Information</span>
                    <span class="student-status-badge status-badge-active">Verified</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">DOB: {{ !empty($data->dob) ? date('d-M-Y', strtotime($data->dob)) : '-' }}</span>
                    <span class="student-adm-badge">{{ optional($data->Gender)->name ?: 'Student' }}</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-user-circle"></i>
                <div><span>Full Name: </span><br><b>{{ $fullName }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-calendar"></i>
                <div><span>Date of Birth: </span><br><b>{{ !empty($data->dob) ? date('d M Y', strtotime($data->dob)) : '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-venus-mars"></i>
                <div><span>Gender: </span><br><b>{{ optional($data->Gender)->name ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-tint text-danger"></i>
                <div><span>Blood Group: </span><br><b>{{ $data->blood_group ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-id-card-o"></i>
                <div><span>Aadhaar: </span><br><b>{{ !empty($data->aadhaar) ? 'XXXX-XXXX-'.substr($data->aadhaar, -4) : '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-tag"></i>
                <div><span>Category: </span><br><b>{{ $data->category ?: ($data->caste_category ?: 'General') }}</b></div>
            </div>
        </div>
    </div>

    {{-- CARD 2: Family & Parents Information --}}
    <div class="student-mob-card card-family profile-section" id="card-family" data-category="family">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-family">
                <i class="fa fa-users"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Parents & Guardians</span>
                    <span class="student-status-badge status-badge-active">Family Record</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Parent Details</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-male text-primary"></i>
                <div><span>Father's Name: </span><br><b>{{ $data->father_name ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-phone text-success"></i>
                <div><span>Father's Mobile: </span><br><b>{{ $data->father_mobile ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-female text-danger"></i>
                <div><span>Mother's Name: </span><br><b>{{ $data->mother_name ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-phone text-success"></i>
                <div><span>Mother's Mobile: </span><br><b>{{ $data->mother_mob ?: '-' }}</b></div>
            </div>
            @if(!empty($data->guardian_name))
                <div class="student-meta-item">
                    <i class="fa fa-user-plus"></i>
                    <div><span>Guardian: </span><br><b>{{ $data->guardian_name }}</b></div>
                </div>
                <div class="student-meta-item">
                    <i class="fa fa-phone"></i>
                    <div><span>Guardian Mobile: </span><br><b>{{ $data->guardian_mobile ?: '-' }}</b></div>
                </div>
            @endif
        </div>
    </div>

    {{-- CARD 3: Contact & Address Information --}}
    <div class="student-mob-card card-contact profile-section" id="card-contact" data-category="contact">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-contact">
                <i class="fa fa-map-marker"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Contact & Residence</span>
                    <span class="student-status-badge status-badge-warning">Editable</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Address Info</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-mobile-phone text-primary"></i>
                <div><span>Mobile Number: </span><br><b id="cardMobile">{{ $data->mobile ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-envelope-o text-warning"></i>
                <div><span>Email Address: </span><br><b id="cardEmail">{{ $data->email ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-building-o"></i>
                <div><span>City / State: </span><br><b>{{ optional($data->City)->name ?: ($data->village_city ?: '-') }}, {{ optional($data->State)->name ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-map-pin"></i>
                <div><span>Pincode: </span><br><b>{{ $data->pincode ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item student-meta-full" style="border-top: 1px dashed #e2e8f0; padding-top: 5px; margin-top: 2px;">
                <i class="fa fa-home text-info"></i>
                <div style="flex: 1;"><span>Home Address: </span><br><b id="cardAddress">{{ $data->address ?: '-' }}</b></div>
            </div>
        </div>

        <div class="student-card-actions">
            <button type="button" class="mob-btn-action btn-act-edit" data-bs-toggle="modal" data-bs-target="#editContactModal">
                <i class="fa fa-pencil"></i> Edit Contact Details
            </button>
        </div>
    </div>

    {{-- CARD 4: Academic & Institute Details (CLASS NAME DISPLAYED HERE) --}}
    <div class="student-mob-card card-academic profile-section" id="card-academic" data-category="academic">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-academic">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Academic Record</span>
                    <span class="student-status-badge status-badge-active">Enrolled</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Adm #{{ $data->admissionNo ?: '-' }}</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-book text-primary"></i>
                <div><span>Enrolled Class: </span><br><b style="color: #002C54;">{{ $className }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-columns"></i>
                <div><span>Section: </span><br><b>{{ $sectionName }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-sort-numeric-asc"></i>
                <div><span>Roll Number: </span><br><b>{{ $data->roll_no ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-calendar-check-o"></i>
                <div><span>Adm Date: </span><br><b>{{ !empty($data->admission_date) ? date('d M Y', strtotime($data->admission_date)) : '-' }}</b></div>
            </div>
            <div class="student-meta-item student-meta-full">
                <i class="fa fa-university text-primary"></i>
                <div><span>Institute / Center: </span><br><b>{{ $getSetting->name ?? 'Coaching Center' }}</b></div>
            </div>
        </div>
    </div>

    {{-- CARD 5: Account & Security --}}
    <div class="student-mob-card card-navy profile-section" id="card-security" data-category="security">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-security">
                <i class="fa fa-shield"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Login Credentials</span>
                    <span class="student-status-badge status-badge-active">Protected</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Security</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-user"></i>
                <div><span>Username: </span><br><b>{{ $data->userName ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-lock text-success"></i>
                <div><span>Password: </span><br><b>••••••••••••</b></div>
            </div>
        </div>

        <div class="student-card-actions">
            <button type="button" class="mob-btn-action btn-act-profile" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="fa fa-key"></i> Change Password
            </button>
            @if(!empty($data->image))
                <button type="button" class="mob-btn-action btn-act-edit" id="removePhotoBtn" style="color: #ef4444 !important; border-color: #fca5a5;">
                    <i class="fa fa-trash-o"></i> Remove Photo
                </button>
            @endif
        </div>
    </div>

    {{-- Bottom Support Guidance Card --}}
    <div class="student-mob-card" style="background: #f8fafc; border: 1px solid #cbd5e1;">
        <div style="display: flex; align-items: flex-start; gap: 8px;">
            <i class="fa fa-info-circle text-primary" style="font-size: 14px; margin-top: 2px;"></i>
            <div style="font-size: 11px; color: #475569; line-height: 1.4;">
                <b style="color: #002C54;">Student Support:</b> To update locked academic details, parent information, or official spellings, please reach out to the institute administrative office.
            </div>
        </div>
    </div>

</div>

{{-- MODAL 1: Edit Contact Information --}}
<div class="modal fade mob-modal" id="editContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="contactForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-pencil-square-o"></i> Edit Contact Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="contactErrors" class="mob-alert-box"></div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="contactMobile">Mobile Number</label>
                        <div class="mob-input-box">
                            <i class="fa fa-phone"></i>
                            <input type="tel" id="contactMobile" name="mobile" class="mob-input-ctrl" value="{{ $data->mobile }}" maxlength="15" placeholder="Enter mobile number">
                        </div>
                    </div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="contactEmail">Email Address</label>
                        <div class="mob-input-box">
                            <i class="fa fa-envelope"></i>
                            <input type="email" id="contactEmail" name="email" class="mob-input-ctrl" value="{{ $data->email }}" placeholder="Enter email address">
                        </div>
                    </div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="contactAddress">Home Address</label>
                        <textarea id="contactAddress" name="address" class="mob-input-textarea" rows="3" placeholder="Enter residential address">{{ $data->address }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mob-btn-action btn-act-edit" style="flex: none; width: 70px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="mob-btn-action btn-act-fee submit-btn">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: Change Password --}}
<div class="modal fade mob-modal" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="passwordForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-shield"></i> Change Account Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="passwordErrors" class="mob-alert-box"></div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="current_password">Current Password</label>
                        <div class="mob-input-box">
                            <i class="fa fa-lock"></i>
                            <input type="password" id="current_password" name="current_password" class="mob-input-ctrl" placeholder="Current password" autocomplete="current-password" required>
                            <button type="button" class="mob-pw-toggle" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="new_password">New Password</label>
                        <div class="mob-input-box">
                            <i class="fa fa-key"></i>
                            <input type="password" id="new_password" name="password" class="mob-input-ctrl" placeholder="Minimum 8 characters" autocomplete="new-password" required>
                            <button type="button" class="mob-pw-toggle" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
                        </div>
                        <div class="mob-strength-bar">
                            <div class="mob-strength-fill" id="strengthBar"></div>
                        </div>
                        <div style="font-size: 9.5px; color: #64748b; margin-top: 3px;" id="strengthText">Must contain at least 8 characters</div>
                    </div>

                    <div class="mob-input-shell">
                        <label class="mob-input-label" for="password_confirmation">Confirm New Password</label>
                        <div class="mob-input-box">
                            <i class="fa fa-check-circle"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="mob-input-ctrl" placeholder="Confirm password" autocomplete="new-password" required>
                            <button type="button" class="mob-pw-toggle" aria-label="Toggle password visibility"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mob-btn-action btn-act-edit" style="flex: none; width: 70px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="mob-btn-action btn-act-profile submit-btn">
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let isClickScrolling = false;

function checkStickyTracker() {
    const container = document.getElementById('trackerContainer');
    const tracker = document.getElementById('mobStepTracker');
    if (!container || !tracker) return;

    const containerTop = container.getBoundingClientRect().top + window.pageYOffset;
    const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const triggerPoint = containerTop - 52;

    if (scrollY >= triggerPoint && triggerPoint > 0) {
        if (!tracker.classList.contains('is-fixed')) {
            const h = tracker.offsetHeight;
            container.style.minHeight = h + 'px';
            tracker.classList.add('is-fixed');
        }
    } else {
        if (tracker.classList.contains('is-fixed')) {
            tracker.classList.remove('is-fixed');
            container.style.minHeight = '';
        }
    }
}

// Auto update active tab on scroll (like wizard step in admissionAdd)
function onUserScroll() {
    checkStickyTracker();
    if (isClickScrolling) return;

    const scrollPos = (window.pageYOffset || document.documentElement.scrollTop || 0) + 120;
    const sPersonal = document.getElementById('card-personal');
    const sFamily = document.getElementById('card-family');
    const sContact = document.getElementById('card-contact');
    const sAcademic = document.getElementById('card-academic');

    let activeCat = 'all';
    if (sAcademic && scrollPos >= (sAcademic.offsetTop - 20)) {
        activeCat = 'academic';
    } else if (sContact && scrollPos >= (sContact.offsetTop - 20)) {
        activeCat = 'contact';
    } else if (sFamily && scrollPos >= (sFamily.offsetTop - 20)) {
        activeCat = 'family';
    } else if (sPersonal && scrollPos >= (sPersonal.offsetTop - 20)) {
        activeCat = 'personal';
    } else {
        activeCat = 'all';
    }

    document.querySelectorAll('.mob-filter-btn').forEach(function(btn) {
        if (btn.getAttribute('data-filter') === activeCat) {
            if (!btn.classList.contains('active-filter')) {
                btn.classList.add('active-filter');
                try {
                    btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                } catch(e) {}
            }
        } else {
            btn.classList.remove('active-filter');
        }
    });
}

function filterSections(cat, btn) {
    document.querySelectorAll('.mob-filter-btn').forEach(function(b) {
        b.classList.remove('active-filter');
    });
    if (btn) {
        btn.classList.add('active-filter');
        try {
            btn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        } catch(e) {}
    }

    if (cat === 'all') {
        isClickScrolling = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(function() { isClickScrolling = false; }, 400);
        return;
    }

    const targetCard = document.getElementById('card-' + cat);
    if (targetCard) {
        isClickScrolling = true;
        // Offset by 52px header + ~40px sticky toolbar + 8px gap = ~100px
        const targetOffset = targetCard.getBoundingClientRect().top + window.pageYOffset - 98;
        window.scrollTo({ top: Math.max(0, targetOffset), behavior: 'smooth' });
        targetCard.style.boxShadow = '0 0 0 2px #0284c7';
        setTimeout(function() {
            targetCard.style.boxShadow = '';
            isClickScrolling = false;
        }, 1200);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.content : '';
    const profileUrl = @json(url('profileStudent'));
    const passwordUrl = @json(route('student.profile.change-password'));

    // Attach sticky tracker scroll listeners
    window.addEventListener('scroll', onUserScroll, { passive: true });
    window.addEventListener('touchmove', onUserScroll, { passive: true });
    window.addEventListener('resize', checkStickyTracker, { passive: true });
    window.addEventListener('orientationchange', checkStickyTracker, { passive: true });
    setTimeout(checkStickyTracker, 80);

    // Prevent any modal from locking body scroll permanently
    document.querySelectorAll('.mob-modal').forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            document.body.style.overflow = '';
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
        });
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                if (window.bootstrap && bootstrap.Modal) {
                    const inst = bootstrap.Modal.getInstance(modal);
                    if (inst) inst.hide();
                } else {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                    document.body.classList.remove('modal-open');
                }
            }
        });
    });

    function showErrors(box, payload, fallback) {
        const errors = payload && payload.errors ? Object.values(payload.errors).flat() : [payload.message || fallback];
        box.textContent = errors.join(' ');
        box.style.display = 'block';
    }

    function setBusy(form, busy) {
        const button = form.querySelector('.submit-btn');
        if (!button) return;
        button.disabled = busy;
        const span = button.querySelector('span');
        if (span) {
            if (busy) {
                if (!button.dataset.origText) button.dataset.origText = span.textContent;
                span.textContent = 'Please wait...';
            } else {
                span.textContent = button.dataset.origText || 'Save';
            }
        }
    }

    // Photo Upload Handler
    const photoInput = document.getElementById('photoInput');
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const heroAvatarImg = document.getElementById('heroAvatarImg');
    const heroAvatarPlaceholder = document.getElementById('heroAvatarPlaceholder');
    const avatarLoader = document.getElementById('avatarLoader');
    const removePhotoBtn = document.getElementById('removePhotoBtn');

    if (changePhotoBtn && photoInput) {
        changePhotoBtn.addEventListener('click', function () {
            photoInput.click();
        });

        photoInput.addEventListener('change', async function () {
            const file = this.files[0];
            if (!file) return;

            if (file.size > 3 * 1024 * 1024) {
                if (window.StudentModal) {
                    StudentModal.error('Photo too large', 'Please choose an image under 3 MB.');
                } else {
                    alert('Please select an image smaller than 3 MB.');
                }
                return;
            }

            const formData = new FormData();
            formData.append('photo', file);
            if (avatarLoader) avatarLoader.style.display = 'flex';

            try {
                const response = await fetch(profileUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) throw result;

                if (heroAvatarImg) {
                    heroAvatarImg.src = result.image_url;
                    heroAvatarImg.style.display = 'block';
                }
                if (heroAvatarPlaceholder) {
                    heroAvatarPlaceholder.style.display = 'none';
                }

                if (window.StudentModal) {
                    StudentModal.open({ type: 'success', title: 'Photo updated', message: result.message, timer: 1800, showConfirm: false });
                } else {
                    alert(result.message);
                }
            } catch (err) {
                const msg = (err && err.message) ? err.message : 'Upload failed. Please choose a valid image (JPG, PNG, WebP).';
                if (window.StudentModal) {
                    StudentModal.error('Upload failed', msg);
                } else {
                    alert(msg);
                }
            } finally {
                if (avatarLoader) avatarLoader.style.display = 'none';
                photoInput.value = '';
            }
        });
    }

    // Photo Remove Handler
    if (removePhotoBtn) {
        removePhotoBtn.addEventListener('click', async function () {
            if (window.StudentModal) {
                const confirmation = await StudentModal.confirm({
                    type: 'warning',
                    title: 'Remove profile photo?',
                    message: 'The default avatar will be shown.',
                    confirmText: 'Remove',
                    danger: true
                });
                if (!confirmation.isConfirmed) return;
            } else {
                if (!confirm('Remove profile photo?')) return;
            }

            try {
                const response = await fetch(profileUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ delete_photo: true })
                });
                const result = await response.json();
                if (response.ok) {
                    if (heroAvatarImg) heroAvatarImg.style.display = 'none';
                    if (heroAvatarPlaceholder) heroAvatarPlaceholder.style.display = 'flex';
                    removePhotoBtn.style.display = 'none';

                    if (window.StudentModal) {
                        StudentModal.open({ type: 'success', title: 'Photo removed', message: result.message, timer: 1500, showConfirm: false });
                    }
                }
            } catch (e) {
                console.error(e);
            }
        });
    }

    // Contact Form AJAX Submit
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const errBox = document.getElementById('contactErrors');
            if (errBox) errBox.style.display = 'none';
            setBusy(this, true);

            const payload = {
                mobile: document.getElementById('contactMobile').value,
                email: document.getElementById('contactEmail').value,
                address: document.getElementById('contactAddress').value
            };

            try {
                const response = await fetch(profileUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (!response.ok) {
                    showErrors(errBox, result, 'Failed to update contact details.');
                    return;
                }

                // Update UI in-place
                const newMobile = result.data.mobile || '-';
                const newEmail = result.data.email || '-';
                const newAddress = result.data.address || '-';

                const cardMobile = document.getElementById('cardMobile');
                if (cardMobile) cardMobile.textContent = newMobile;

                const cardEmail = document.getElementById('cardEmail');
                if (cardEmail) cardEmail.textContent = newEmail;

                const cardAddress = document.getElementById('cardAddress');
                if (cardAddress) cardAddress.textContent = newAddress;

                const heroMobile = document.getElementById('heroMobile');
                if (heroMobile) heroMobile.textContent = newMobile;

                const heroEmail = document.getElementById('heroEmail');
                if (heroEmail) heroEmail.textContent = newEmail;

                const modalEl = document.getElementById('editContactModal');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();
                } else if (modalEl) {
                    modalEl.classList.remove('show');
                }
                document.body.style.overflow = '';
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });

                if (window.StudentModal) {
                    StudentModal.open({ type: 'success', title: 'Profile Updated', message: result.message, timer: 1800, showConfirm: false });
                } else {
                    alert(result.message);
                }
            } catch (error) {
                showErrors(errBox, error, 'Network error. Please try again.');
            } finally {
                setBusy(this, false);
            }
        });
    }

    // Password Visibility Toggles
    document.querySelectorAll('.mob-pw-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
        });
    });

    // Password Strength Meter
    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function () {
            let score = 0;
            if (this.value.length >= 8) score++;
            if (/[A-Z]/.test(this.value)) score++;
            if (/[0-9]/.test(this.value)) score++;
            if (/[^A-Za-z0-9]/.test(this.value)) score++;

            const colors = ['#ef4444', '#f59e0b', '#10b981', '#059669'];
            const labels = ['Weak password', 'Fair password', 'Good password', 'Strong password'];
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');

            if (bar) {
                bar.style.width = (score * 25) + '%';
                bar.style.backgroundColor = colors[Math.max(0, score - 1)];
            }
            if (text) {
                text.textContent = this.value ? labels[Math.max(0, score - 1)] : 'Must contain at least 8 characters';
            }
        });
    }

    // Change Password Form AJAX Submit
    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const errBox = document.getElementById('passwordErrors');
            if (errBox) errBox.style.display = 'none';
            setBusy(this, true);

            const payload = {
                current_password: document.getElementById('current_password').value,
                password: document.getElementById('new_password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            try {
                const response = await fetch(passwordUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (!response.ok) {
                    showErrors(errBox, result, 'Failed to update password.');
                    return;
                }

                this.reset();
                const bar = document.getElementById('strengthBar');
                if (bar) bar.style.width = '0%';
                const text = document.getElementById('strengthText');
                if (text) text.textContent = 'Must contain at least 8 characters';

                const modalEl = document.getElementById('changePasswordModal');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();
                } else if (modalEl) {
                    modalEl.classList.remove('show');
                }
                document.body.style.overflow = '';
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });

                if (window.StudentModal) {
                    StudentModal.success('Password changed', 'Use your new password the next time you sign in.');
                } else {
                    alert('Password changed successfully.');
                }
            } catch (error) {
                showErrors(errBox, error, 'Network error. Please try again.');
            } finally {
                setBusy(this, false);
            }
        });
    }
});
</script>
@endsection

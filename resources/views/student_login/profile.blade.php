@php
    $getUser = Helper::getUser();
    $getSetting = Helper::getSetting();
    $fullName = trim(($data->first_name ?? '').' '.($data->last_name ?? ''));
    if (empty($fullName)) {
        $fullName = 'Student';
    }
    $className = optional($data->ClassTypes)->name ?? (optional($getUser->ClassTypes)->name ?? '-');
    $sectionName = optional($data->Section)->name ?? '-';
    $sessionName = Session::get('session_name') ?? date('Y');
    $imageUrl = !empty($data->image)
        ? env('IMAGE_SHOW_PATH').'/profile/'.$data->image
        : asset('public/assets/student_login/img/user_icon.png');
    $hasPhoto = !empty($data->image);

    $profileCompletion = $profileCompletion ?? 85;
    $completedFields = $completedFields ?? 7;
    $totalFields = $totalFields ?? 9;
    $canEditProfile = (int) Session::get('role_id') !== 3;
@endphp
@extends('student_login.layout.app')

@section('title', 'My Profile')
@section('page_title', 'MY PROFILE')
@section('page_sub', $fullName.' · '.$className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STUDENT PROFILE
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

/* Hero Profile Identity Header */
.mob-profile-id-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 8px;
}
.mob-avatar-wrap {
    position: relative;
    width: 52px;
    height: 52px;
    flex-shrink: 0;
}
.mob-avatar-img {
    width: 52px;
    height: 52px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.4);
    object-fit: cover;
    background: #0f172a;
    display: block;
}
.mob-avatar-placeholder {
    width: 52px;
    height: 52px;
    border-radius: 4px;
    border: 1.5px solid rgba(255, 255, 255, 0.4);
    background: #0284c7;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 800;
}
.mob-avatar-cam-btn {
    position: absolute;
    bottom: -3px;
    right: -3px;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    background: #f59e0b;
    color: #ffffff;
    border: 1px solid #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
.mob-avatar-spinner {
    position: absolute;
    inset: 0;
    background: rgba(0, 24, 51, 0.75);
    border-radius: 4px;
    display: none;
    align-items: center;
    justify-content: center;
    color: #38bdf8;
    font-size: 14px;
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
    margin-top: 2px;
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

/* Hero Progress Strip matching admissionView */
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

/* Fast Action Bar matching admissionView */
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

/* 2. Compact Segment Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-toolbar::-webkit-scrollbar {
    display: none;
}
.mob-filter-btn {
    flex: 1;
    min-width: 65px;
    height: 30px;
    padding: 0 8px;
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
}
.mob-filter-btn.active-filter {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-filter-btn:active {
    transform: scale(0.96);
}

/* 3. Feed List & Card Item matching admissionView */
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
    transition: transform .1s ease, border-color .1s ease;
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

/* Card Details Grid matching .student-meta-grid */
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

/* Card Actions Row matching .student-card-actions */
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

/* Modals matching Native Theme */
.mob-modal .modal-content {
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 10px 30px rgba(0, 44, 84, 0.2);
}
.mob-modal .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 3px 3px 0 0;
    border-bottom: none;
}
.mob-modal .modal-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-modal .btn-close {
    filter: invert(1);
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
            <img src="{{ $imageUrl }}" alt="{{ $fullName }}" id="heroAvatarImg" class="mob-avatar-img {{ $hasPhoto ? '' : 'd-none' }}">
            <div id="heroAvatarPlaceholder" class="mob-avatar-placeholder {{ $hasPhoto ? 'd-none' : '' }}">
                {{ strtoupper(substr($fullName, 0, 1)) ?: 'S' }}
            </div>
            @if($canEditProfile)
                <button type="button" class="mob-avatar-cam-btn" id="changePhotoBtn" title="Change Photo" aria-label="Change photo">
                    <i class="fa fa-camera"></i>
                </button>
            @endif
            <div class="mob-avatar-spinner" id="avatarLoader">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </div>
        <div class="mob-profile-id-info">
            <div class="mob-profile-name" id="heroStudentName">{{ $fullName }}</div>
            <div class="mob-profile-meta-row">
                <span>Class: <b>{{ $className }}</b></span>
                <span>• Sec: <b>{{ $sectionName }}</b></span>
                <span>• Session: <b>{{ $sessionName }}</b></span>
            </div>
            <div style="font-size: 9px; color: #94a3b8; margin-top: 2px;">
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
                Roll No: {{ $data->roll_no ?: '-' }}
            </span>
        </div>

        {{-- Radar 2: Class & Section --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-graduation-cap text-success"></i> Academic Class
            </span>
            <div class="mob-radar-val" style="color: #34d399;">
                {{ $className }}
            </div>
            <span class="mob-radar-meta">
                Section: {{ $sectionName }}
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
                {{ $completedFields }}/{{ $totalFields }} fields verified
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
        @if($canEditProfile)
            <button type="button" class="mob-act-btn mob-act-btn-add" data-bs-toggle="modal" data-bs-target="#editContactModal">
                <i class="fa fa-pencil-square-o"></i> Edit Contact
            </button>
        @endif
        <button type="button" class="mob-act-btn mob-act-btn-filter" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <i class="fa fa-shield"></i> Password & Security
        </button>
    </div>
</div>

{{-- 2. Compact Segment Toolbar matching admissionView --}}
<div class="mob-search-toolbar">
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

{{-- Hidden File Input for Photo Upload --}}
@if($canEditProfile)
    <input type="file" id="photoInput" accept="image/jpeg,image/png,image/webp" style="display: none;">
@endif

{{-- 3. Detailed Information Cards Feed --}}
<div class="student-feed-list">

    {{-- CARD 1: Personal Information --}}
    <div class="student-mob-card card-personal profile-section" data-category="personal">
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
                    <span class="student-class-badge">{{ optional($data->Gender)->name ?: 'Student' }}</span>
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
    <div class="student-mob-card card-family profile-section" data-category="family">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-family">
                <i class="fa fa-users"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Parents & Guardians</span>
                    <span class="student-status-badge status-badge-active">Parental Info</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Family Record</span>
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
    <div class="student-mob-card card-contact profile-section" data-category="contact">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-contact">
                <i class="fa fa-map-marker"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Contact & Address</span>
                    <span class="student-status-badge status-badge-warning">Editable</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Residence Info</span>
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

        @if($canEditProfile)
            <div class="student-card-actions">
                <button type="button" class="mob-btn-action btn-act-edit" data-bs-toggle="modal" data-bs-target="#editContactModal">
                    <i class="fa fa-pencil"></i> Edit Contact Details
                </button>
            </div>
        @endif
    </div>

    {{-- CARD 4: Academic & Institute Details --}}
    <div class="student-mob-card card-academic profile-section" data-category="academic">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-academic">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Academic & Institute</span>
                    <span class="student-status-badge status-badge-active">Enrolled</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">Adm #{{ $data->admissionNo ?: '-' }}</span>
                    <span class="student-class-badge">{{ $className }}</span>
                </div>
            </div>
        </div>

        <div class="student-meta-grid">
            <div class="student-meta-item">
                <i class="fa fa-book"></i>
                <div><span>Class: </span><br><b>{{ $className }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-columns"></i>
                <div><span>Section: </span><br><b>{{ $sectionName }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-sort-numeric-asc"></i>
                <div><span>Roll No: </span><br><b>{{ $data->roll_no ?: '-' }}</b></div>
            </div>
            <div class="student-meta-item">
                <i class="fa fa-calendar-check-o"></i>
                <div><span>Adm Date: </span><br><b>{{ !empty($data->admission_date) ? date('d M Y', strtotime($data->admission_date)) : '-' }}</b></div>
            </div>
            <div class="student-meta-item student-meta-full">
                <i class="fa fa-university text-primary"></i>
                <div><span>Institute / Branch: </span><br><b>{{ $getSetting->name ?? 'Coaching Center' }}</b></div>
            </div>
        </div>
    </div>

    {{-- CARD 5: Account & Security --}}
    <div class="student-mob-card card-navy profile-section" data-category="security">
        <div class="student-card-header">
            <div class="student-avatar-box avatar-security">
                <i class="fa fa-shield"></i>
            </div>
            <div class="student-header-info">
                <div class="student-name-row">
                    <span class="student-mob-name">Login & Security</span>
                    <span class="student-status-badge status-badge-active">Protected</span>
                </div>
                <div class="student-pills-wrap">
                    <span class="student-adm-badge">UID: {{ $data->userName ?: '-' }}</span>
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
            @if($canEditProfile && !empty($data->image))
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
@if($canEditProfile)
<div class="modal fade mob-modal" id="editContactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="contactForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-pencil-square-o"></i> Edit Contact Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
@endif

{{-- MODAL 2: Change Password --}}
<div class="modal fade mob-modal" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="passwordForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-shield"></i> Change Account Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
function filterSections(cat, btn) {
    document.querySelectorAll('.mob-filter-btn').forEach(function(b) {
        b.classList.remove('active-filter');
    });
    if (btn) btn.classList.add('active-filter');

    const sections = document.querySelectorAll('.profile-section');
    sections.forEach(function(s) {
        if (cat === 'all' || s.getAttribute('data-category') === cat) {
            s.style.display = 'block';
        } else {
            s.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = csrfMeta ? csrfMeta.content : '';
    const profileUrl = @json(url('profileStudent'));
    const passwordUrl = @json(route('student.profile.change-password'));

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
                    heroAvatarImg.classList.remove('d-none');
                }
                if (heroAvatarPlaceholder) {
                    heroAvatarPlaceholder.classList.add('d-none');
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
                    if (heroAvatarImg) heroAvatarImg.classList.add('d-none');
                    if (heroAvatarPlaceholder) heroAvatarPlaceholder.classList.remove('d-none');
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
                if (modalEl && window.bootstrap) {
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();
                }

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
                if (modalEl && window.bootstrap) {
                    const modalInst = bootstrap.Modal.getInstance(modalEl);
                    if (modalInst) modalInst.hide();
                }

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

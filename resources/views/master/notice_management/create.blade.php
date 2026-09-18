@extends('layout.app')

@php
    $rolesList = $roles ?? [];
    $classesList = $classes ?? [];
    $usersList = $users ?? [];
    $studentsList = $students ?? [];
    $isAdmin = ((int) Session::get('role_id') === 1);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NOTICE CREATE & BROADCAST (SIGNATURE THEME)
   Aligned with userAdd, userView, and studentList UI guidelines:
   - Font family: Segoe UI, -apple-system, Roboto, sans-serif
   - Base font-size: 11.5px / 12px
   - Sharp border-radius: 2px throughout
   - Dark Navy Hero: #002C54 to #0f3460
   - Symmetrical equal-height cards & compact 30px inputs
   ========================================================================== */

.notice-create-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - var(--header-height, 60px) - 16px);
}
.notice-create-page * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner */
.notice-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 8px;
}
.notice-hero-text {
    display: flex;
    flex-direction: column;
}
.notice-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 700;
}
.notice-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.notice-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    color: #cbd5e1;
    opacity: .9;
}
.notice-hero-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 500;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    color: #e2e8f0;
}
.hero-stat-badge b {
    color: #fff;
    font-weight: 700;
}
.hero-stat-badge.badge-blue {
    background: rgba(56, 189, 248, 0.15);
    border-color: rgba(56, 189, 248, 0.35);
    color: #bae6fd;
}
.hero-stat-badge.badge-green {
    background: rgba(74, 222, 128, 0.15);
    border-color: rgba(74, 222, 128, 0.35);
    color: #bbf7d0;
}

/* Button System */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 28px;
    padding: 0 12px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
    white-space: nowrap;
    line-height: 1.3;
}
.dash-btn-primary {
    background: #0284c7;
    color: #fff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
    color: #fff;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    font-weight: 700;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #002C54;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #fff;
    border-color: rgba(255,255,255,.3);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.18);
    color: #fff;
}

/* 2. Form Cards */
.form-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
    margin-bottom: 8px;
    display: flex;
    flex-direction: column;
}
.form-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-card-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.form-card-body {
    padding: 12px;
    flex: 1;
}

/* Form Controls */
.form-group-custom {
    margin-bottom: 10px;
}
.form-label-custom {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-bottom: 4px;
}
.form-label-custom .required-star {
    color: #ef4444;
    font-weight: 800;
}
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}
.input-with-icon i.input-icon {
    position: absolute;
    left: 9px;
    color: #64748b;
    font-size: 12px;
    pointer-events: none;
}
.form-control-custom {
    width: 100%;
    height: 30px;
    padding: 3px 8px 3px 28px;
    font-size: 12px;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease-in-out;
}
.form-control-custom:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}
.textarea-custom {
    width: 100%;
    padding: 8px 10px;
    font-size: 12px;
    color: #0f172a;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    resize: vertical;
    min-height: 120px;
    line-height: 1.5;
    transition: all .15s ease-in-out;
}
.textarea-custom:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2,132,199,.15);
}

/* PDF Upload Zone */
.pdf-upload-zone {
    border: 1.5px dashed #cbd5e1;
    border-radius: 2px;
    padding: 10px 14px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all .15s ease-in-out;
}
.pdf-upload-zone:hover {
    border-color: #0284c7;
    background: #f0f9ff;
}
.pdf-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 2px;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

/* Quick Date Presets */
.preset-chips-row {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
    margin-top: 6px;
}
.preset-chip {
    padding: 2px 7px;
    font-size: 10px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
}
.preset-chip:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Workflow info banners */
.workflow-box {
    border-radius: 2px;
    padding: 8px 10px;
    margin-top: 10px;
    font-size: 11px;
    line-height: 1.4;
    display: flex;
    align-items: flex-start;
    gap: 8px;
}
.workflow-box.admin-mode {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-left: 3px solid #16a34a;
    color: #166534;
}
.workflow-box.review-mode {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 3px solid #d97706;
    color: #92400e;
}

/* Audience 3-Segment Switchers */
.audience-segment-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
@media (max-width: 768px) {
    .audience-segment-grid {
        grid-template-columns: 1fr;
    }
}
.audience-card-label {
    cursor: pointer;
    margin: 0;
    position: relative;
}
.audience-card-label input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.audience-card-content {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 10px 12px;
    background: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all .15s ease-in-out;
}
.audience-card-label:hover .audience-card-content {
    border-color: #94a3b8;
    background: #f8fafc;
}
.audience-card-icon {
    width: 32px;
    height: 32px;
    border-radius: 2px;
    background: #f1f5f9;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    transition: all .15s ease;
}
.audience-card-label input:checked + .audience-card-content {
    border-color: #002C54;
    background: #f0f7ff;
    box-shadow: 0 0 0 1px #002C54;
}
.audience-card-label input:checked + .audience-card-content .audience-card-icon {
    background: #002C54;
    color: #ffffff;
}

/* Selection Grids for Roles and Classes */
.selection-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding: 4px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
}
.recipient-tiles-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    max-height: 220px;
    overflow-y: auto;
    padding: 2px;
}
@media (max-width: 991px) {
    .recipient-tiles-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
.recipient-tile {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    margin: 0;
    font-size: 11.5px;
    font-weight: 500;
    background: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all .12s ease;
}
.recipient-tile:hover {
    background: #f8fafc;
    border-color: #94a3b8;
}
.recipient-tile input[type="checkbox"] {
    accent-color: #0284c7;
    width: 14px;
    height: 14px;
    cursor: pointer;
}

/* Specific People List Columns */
.people-col-card {
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    height: 280px;
}
.people-col-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 8px;
}
.people-search-box {
    position: relative;
}
.people-search-box i {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 11px;
}
.people-search-box input {
    height: 26px;
    padding: 2px 6px 2px 24px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    width: 100%;
    outline: none;
}
.people-search-box input:focus {
    border-color: #0284c7;
}
.people-scroll-list {
    flex: 1;
    overflow-y: auto;
    padding: 0;
    margin: 0;
}
.person-item-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 5px 8px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    font-size: 11px;
    margin: 0;
    transition: background .12s ease;
}
.person-item-row:hover {
    background: #f0f9ff;
}
.person-item-row input[type="checkbox"] {
    accent-color: #0284c7;
    width: 14px;
    height: 14px;
}

/* Bottom Action Bar */
.bottom-action-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}

/* Confirmation Modal */
.modal-header-arise {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 2px 2px 0 0;
}
.modal-header-arise .modal-title {
    font-size: 13px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.modal-close-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    opacity: .8;
}
.modal-close-btn:hover {
    opacity: 1;
}
</style>
@endsection

@section('content')
<div class="content-wrapper notice-create-page">

    {{-- 1. Top Hero Header --}}
    <div class="notice-hero">
        <div class="notice-hero-text">
            <span class="notice-kicker">
                <i class="fa fa-bullhorn mr-1"></i> Notice Management &bull; Circular Composition
            </span>
            <h1 class="notice-title">
                <i class="fa fa-plus-circle mr-1"></i> Create Notice &amp; Broadcast Announcement
            </h1>
            <p class="notice-subtitle">
                Compose circulars and broadcast targeted announcements to roles, classes, or specific individuals.
            </p>
        </div>

        {{-- Hero Stat Badges --}}
        <div class="notice-hero-stats">
            <div class="hero-stat-badge badge-blue" title="Total Roles Available">
                <i class="fa fa-users"></i> Roles: <b>{{ count($rolesList) }}</b>
            </div>
            <div class="hero-stat-badge badge-blue" title="Total Classes Available">
                <i class="fa fa-graduation-cap"></i> Classes: <b>{{ count($classesList) }}</b>
            </div>
            <div class="hero-stat-badge badge-green" title="Total Registered Staff">
                <i class="fa fa-user-circle"></i> Staff: <b>{{ count($usersList) }}</b>
            </div>
        </div>

        {{-- Hero Actions --}}
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('notice-management') }}" class="dash-btn dash-btn-outline" title="Back to Notice Directory">
                <i class="fa fa-arrow-left mr-1"></i> {{ __('common.Back') }} to Notices
            </a>
        </div>
    </div>

    {{-- Session Validation Errors --}}
    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger py-2 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
            <div class="font-weight-bold mb-1">
                <i class="fa fa-exclamation-triangle mr-1"></i> Please correct the following errors before submitting:
            </div>
            <ul class="mb-0 pl-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Create Form --}}
    <form action="{{ url('notice-management') }}" method="post" enctype="multipart/form-data" id="noticeForm">
        @csrf

        {{-- Row 1: Notice Details + Schedule Window (Symmetrical Cards) --}}
        <div class="row g-2 mb-2">
            
            {{-- Left Column: Notice Information --}}
            <div class="col-lg-8">
                <div class="form-card h-100 mb-0">
                    <div class="form-card-header">
                        <h3 class="form-card-title">
                            <i class="fa fa-file-text-o text-primary"></i> 1. Notice Information &amp; Content
                        </h3>
                        <span class="text-muted" style="font-size: 10.5px;">All starred (<span class="text-danger">*</span>) fields required</span>
                    </div>
                    <div class="form-card-body">
                        
                        {{-- Notice Subject --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom" for="title">
                                Notice Subject / Title <span class="required-star">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fa fa-pencil input-icon"></i>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       class="form-control-custom @error('title') is-invalid @enderror" 
                                       maxlength="255" 
                                       value="{{ old('title') }}" 
                                       placeholder="e.g. Annual Sports Day Schedule, Fee Submission Guidelines, Holiday Notice..." 
                                       required 
                                       autocomplete="off" 
                                       autofocus>
                            </div>
                            @error('title')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Message Body --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom" for="message">
                                Notice Message Content <span class="required-star">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message" 
                                      class="textarea-custom @error('message') is-invalid @enderror" 
                                      rows="5" 
                                      placeholder="Write comprehensive announcement details, required actions, guidelines, and contact information..." 
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PDF Attachment --}}
                        <div class="form-group-custom mb-0">
                            <label class="form-label-custom">
                                Official Attachment <span class="text-muted font-weight-normal">(PDF Document - Optional)</span>
                            </label>
                            <div class="pdf-upload-zone">
                                <div class="pdf-icon-badge">
                                    <i class="fa fa-file-pdf-o"></i>
                                </div>
                                <div style="flex: 1;">
                                    <input type="file" 
                                           name="attachment" 
                                           id="attachment" 
                                           class="form-control-file" 
                                           accept="application/pdf,.pdf" 
                                           style="font-size: 11px;">
                                    <div id="attachmentHelp" style="font-size: 10.5px; color: #64748b; margin-top: 3px;">
                                        PDF format only, maximum allowed size 10 MB.
                                    </div>
                                </div>
                            </div>
                            @error('attachment')
                                <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right Column: Schedule & Workflow Window --}}
            <div class="col-lg-4">
                <div class="form-card h-100 mb-0">
                    <div class="form-card-header">
                        <h3 class="form-card-title">
                            <i class="fa fa-calendar-check-o text-primary"></i> 2. Schedule &amp; Broadcast Window
                        </h3>
                    </div>
                    <div class="form-card-body d-flex flex-column justify-content-between">
                        
                        <div>
                            {{-- Visible From --}}
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="from_date">
                                    Visible From <span class="required-star">*</span>
                                </label>
                                <div class="input-with-icon">
                                    <i class="fa fa-calendar input-icon"></i>
                                    <input type="date" 
                                           name="from_date" 
                                           id="from_date" 
                                           class="form-control-custom @error('from_date') is-invalid @enderror" 
                                           value="{{ old('from_date', date('Y-m-d')) }}" 
                                           required>
                                </div>
                                @error('from_date')
                                    <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Visible Until --}}
                            <div class="form-group-custom">
                                <label class="form-label-custom" for="to_date">
                                    Visible Until <span class="required-star">*</span>
                                </label>
                                <div class="input-with-icon">
                                    <i class="fa fa-calendar-check-o input-icon"></i>
                                    <input type="date" 
                                           name="to_date" 
                                           id="to_date" 
                                           class="form-control-custom @error('to_date') is-invalid @enderror" 
                                           value="{{ old('to_date', date('Y-m-d', strtotime('+7 days'))) }}" 
                                           required>
                                </div>
                                @error('to_date')
                                    <div class="text-danger mt-1" style="font-size: 11px;">{{ $message }}</div>
                                @enderror

                                {{-- Quick Presets --}}
                                <div class="preset-chips-row">
                                    <span style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Presets:</span>
                                    <button type="button" class="preset-chip" onclick="setNoticeDuration(3)">3 Days</button>
                                    <button type="button" class="preset-chip" onclick="setNoticeDuration(7)">1 Week</button>
                                    <button type="button" class="preset-chip" onclick="setNoticeDuration(15)">15 Days</button>
                                    <button type="button" class="preset-chip" onclick="setNoticeDuration(30)">1 Month</button>
                                </div>
                            </div>
                        </div>

                        {{-- Security / Workflow State Info --}}
                        <div class="workflow-box {{ $isAdmin ? 'admin-mode' : 'review-mode' }}">
                            <i class="fa {{ $isAdmin ? 'fa-bolt' : 'fa-shield' }} mt-1" style="font-size: 14px;"></i>
                            <div>
                                @if($isAdmin)
                                    <strong>Admin Direct Broadcast:</strong>
                                    <div>This notice will be published and broadcast notifications sent immediately upon submission.</div>
                                @else
                                    <strong>Administrator Review:</strong>
                                    <div>Your notice will route to the school administration for review before being broadcasted.</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        {{-- Row 2: Target Audience & Recipient Segmentation Card --}}
        <div class="form-card mb-2">
            <div class="form-card-header">
                <h3 class="form-card-title">
                    <i class="fa fa-users text-primary"></i> 3. Target Audience &amp; Recipient Segmentation
                </h3>
                <span class="badge badge-primary px-2 py-1" id="recipientCountBadge" style="font-size: 11px; font-weight: 700; border-radius: 2px;">
                    0 selected
                </span>
            </div>
            <div class="form-card-body">
                
                {{-- 3 Segment Radio Cards --}}
                <div class="audience-segment-grid">
                    
                    {{-- 1. Whole Role --}}
                    <label class="audience-card-label">
                        <input type="radio" name="audience_type" value="role" {{ old('audience_type', 'role') === 'role' ? 'checked' : '' }}>
                        <div class="audience-card-content">
                            <div class="audience-card-icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; font-weight: 700; color: #0f172a;">Entire Role</div>
                                <div style="font-size: 10.5px; color: #64748b;">Target all users in selected staff/student roles</div>
                            </div>
                        </div>
                    </label>

                    {{-- 2. Whole Class --}}
                    <label class="audience-card-label">
                        <input type="radio" name="audience_type" value="class" {{ old('audience_type') === 'class' ? 'checked' : '' }}>
                        <div class="audience-card-content">
                            <div class="audience-card-icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; font-weight: 700; color: #0f172a;">Entire Class</div>
                                <div style="font-size: 10.5px; color: #64748b;">Target every enrolled student in selected classes</div>
                            </div>
                        </div>
                    </label>

                    {{-- 3. Specific Individuals --}}
                    <label class="audience-card-label">
                        <input type="radio" name="audience_type" value="specific" {{ old('audience_type') === 'specific' ? 'checked' : '' }}>
                        <div class="audience-card-content">
                            <div class="audience-card-icon">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <div style="font-size: 12px; font-weight: 700; color: #0f172a;">Specific Individuals</div>
                                <div style="font-size: 10.5px; color: #64748b;">Pick specific staff members or students manually</div>
                            </div>
                        </div>
                    </label>

                </div>

                {{-- Panel 1: Roles --}}
                <div class="audience-panel" id="panel-role">
                    <div class="selection-toolbar">
                        <span style="font-size: 11px; font-weight: 700; color: #475569;">
                            <i class="fa fa-check-square-o text-primary mr-1"></i> Select Roles to broadcast:
                        </span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-xs btn-outline-primary" id="btnSelectAllRoles">Select All</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btnDeselectAllRoles">Clear</button>
                        </div>
                    </div>
                    <div class="recipient-tiles-grid">
                        @foreach($rolesList as $role)
                            <label class="recipient-tile">
                                <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}>
                                <span>{{ $role->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Panel 2: Classes --}}
                <div class="audience-panel d-none" id="panel-class">
                    <div class="selection-toolbar">
                        <span style="font-size: 11px; font-weight: 700; color: #475569;">
                            <i class="fa fa-graduation-cap text-primary mr-1"></i> Select Classes to broadcast:
                        </span>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-xs btn-outline-primary" id="btnSelectAllClasses">Select All</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btnDeselectAllClasses">Clear</button>
                        </div>
                    </div>
                    <div class="recipient-tiles-grid">
                        @foreach($classesList as $class)
                            <label class="recipient-tile">
                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', [])) ? 'checked' : '' }}>
                                <span>{{ $class->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Panel 3: Specific People --}}
                <div class="audience-panel d-none" id="panel-specific">
                    <div class="row g-2">
                        
                        {{-- Staff Column --}}
                        <div class="col-md-6">
                            <div class="people-col-card">
                                <div class="people-col-header">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span style="font-weight: 700; font-size: 11px; color: #002C54;">
                                            <i class="fa fa-user-circle text-primary mr-1"></i> Staff Members ({{ count($usersList) }})
                                        </span>
                                    </div>
                                    <div class="people-search-box">
                                        <i class="fa fa-search"></i>
                                        <input type="text" class="people-search-input" data-target="staffListContainer" placeholder="Filter staff by name or mobile...">
                                    </div>
                                </div>
                                <div class="people-scroll-list" id="staffListContainer">
                                    @foreach($usersList as $user)
                                        <label class="person-item-row" data-search="{{ strtolower($user->first_name.' '.$user->last_name.' '.($user->mobile ?? '')) }}">
                                            <input type="checkbox" name="specific_recipients[]" value="user:{{ $user->id }}" {{ in_array('user:'.$user->id, old('specific_recipients', [])) ? 'checked' : '' }}>
                                            <div style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <div style="font-weight: 700; color: #0f172a;">
                                                    {{ trim($user->first_name.' '.$user->last_name) }}
                                                </div>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    {{ optional($user->roleName)->name ?? 'Staff' }} {{ $user->mobile ? '· '.$user->mobile : '' }}
                                                </small>
                                            </div>
                                        </label>
                                    @endforeach
                                    <div class="people-no-match d-none text-center py-4 text-muted" style="font-size: 11px;">
                                        No matching staff found.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Students Column --}}
                        <div class="col-md-6">
                            <div class="people-col-card">
                                <div class="people-col-header">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span style="font-weight: 700; font-size: 11px; color: #002C54;">
                                            <i class="fa fa-graduation-cap text-primary mr-1"></i> Students ({{ count($studentsList) }})
                                        </span>
                                    </div>
                                    <div class="people-search-box">
                                        <i class="fa fa-search"></i>
                                        <input type="text" class="people-search-input" data-target="studentListContainer" placeholder="Filter student by name or father...">
                                    </div>
                                </div>
                                <div class="people-scroll-list" id="studentListContainer">
                                    @foreach($studentsList as $student)
                                        <label class="person-item-row" data-search="{{ strtolower($student->first_name.' '.$student->last_name.' '.($student->father_name ?? '').' '.($student->admission_no ?? '')) }}">
                                            <input type="checkbox" name="specific_recipients[]" value="student:{{ $student->id }}" {{ in_array('student:'.$student->id, old('specific_recipients', [])) ? 'checked' : '' }}>
                                            <div style="flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <div style="font-weight: 700; color: #0f172a;">
                                                    {{ trim($student->first_name.' '.$student->last_name) }}
                                                </div>
                                                <small class="text-muted" style="font-size: 10px;">
                                                    Adm #{{ $student->admission_no ?? $student->id }} {{ $student->father_name ? '· S/o '.$student->father_name : '' }}
                                                </small>
                                            </div>
                                        </label>
                                    @endforeach
                                    <div class="people-no-match d-none text-center py-4 text-muted" style="font-size: 11px;">
                                        No matching students found.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Bottom Action Bar --}}
        <div class="bottom-action-bar">
            <a href="{{ url('notice-management') }}" class="dash-btn dash-btn-outline text-dark" style="border: 1px solid #cbd5e1; background: #fff;">
                <i class="fa fa-times mr-1"></i> Cancel
            </a>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="dash-btn dash-btn-outline text-dark" id="btnResetForm" style="border: 1px solid #cbd5e1; background: #fff;">
                    <i class="fa fa-refresh mr-1"></i> Reset
                </button>
                <button type="submit" class="dash-btn dash-btn-primary" id="btnSubmitNotice" style="padding: 0 16px; height: 30px; font-size: 12px;">
                    <i class="fa {{ $isAdmin ? 'fa-paper-plane' : 'fa-send' }} mr-1"></i> 
                    {{ $isAdmin ? 'Publish & Broadcast Notice' : 'Submit for Administrator Approval' }}
                </button>
            </div>
        </div>

    </form>

</div>

{{-- Modern Submission Confirmation Modal --}}
<div class="modal fade" id="noticeConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content" style="border-radius: 4px; overflow: hidden; border: 1px solid #cbd5e1; box-shadow: 0 10px 30px rgba(0,0,0,.2);">
            <div class="modal-header-arise">
                <h5 class="modal-title">
                    <i class="fa {{ $isAdmin ? 'fa-bullhorn' : 'fa-paper-plane' }}"></i> 
                    {{ $isAdmin ? 'Publish Notice Broadcast' : 'Submit Notice for Review' }}
                </h5>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto; font-size: 22px;">
                    <i class="fa {{ $isAdmin ? 'fa-bullhorn' : 'fa-check-circle-o' }}"></i>
                </div>
                <h4 style="font-size: 14.5px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                    {{ $isAdmin ? 'Confirm Notice Broadcast?' : 'Send Notice for Review?' }}
                </h4>
                <p style="font-size: 12px; color: #64748b; line-height: 1.45; margin-bottom: 0;">
                    @if($isAdmin)
                        This notice will be published immediately and broadcast push notifications will be dispatched to all selected recipients.
                    @else
                        This notice will be forwarded to the school administration for review and approval before broadcast.
                    @endif
                </p>
            </div>
            <div class="modal-footer p-2" style="background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="dash-btn dash-btn-outline text-dark" data-bs-dismiss="modal" style="border: 1px solid #cbd5e1; background: #fff;">
                    Cancel
                </button>
                <button type="button" class="dash-btn dash-btn-primary" id="btnConfirmSubmitFinal">
                    <i class="fa fa-check mr-1"></i> {{ $isAdmin ? 'Yes, Publish Now' : 'Yes, Send for Review' }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Date Preset Helper
function setNoticeDuration(days) {
    var fromInput = document.getElementById('from_date');
    var toInput = document.getElementById('to_date');
    var baseDate = fromInput.value ? new Date(fromInput.value) : new Date();
    
    baseDate.setDate(baseDate.getDate() + parseInt(days));
    var yyyy = baseDate.getFullYear();
    var mm = String(baseDate.getMonth() + 1).padStart(2, '0');
    var dd = String(baseDate.getDate()).padStart(2, '0');
    toInput.value = `${yyyy}-${mm}-${dd}`;
}

$(document).ready(function() {
    var noticeForm = $('#noticeForm');
    var isSubmitting = false;

    // 1. Audience Segmentation Switcher
    function updateAudiencePanels() {
        var selectedType = $('input[name="audience_type"]:checked').val();
        
        $('.audience-panel').addClass('d-none');
        $('#panel-' + selectedType).removeClass('d-none');

        // Update Counter
        var count = 0;
        if (selectedType === 'role') {
            count = $('#panel-role input[type="checkbox"]:checked').length;
        } else if (selectedType === 'class') {
            count = $('#panel-class input[type="checkbox"]:checked').length;
        } else if (selectedType === 'specific') {
            count = $('#panel-specific input[type="checkbox"]:checked').length;
        }

        $('#recipientCountBadge').text(count + ' selected');
    }

    $('input[name="audience_type"]').on('change', updateAudiencePanels);
    $('.audience-panel input[type="checkbox"]').on('change', updateAudiencePanels);
    updateAudiencePanels();

    // 2. Select All / Deselect All for Roles
    $('#btnSelectAllRoles').on('click', function() {
        $('#panel-role input[type="checkbox"]').prop('checked', true);
        updateAudiencePanels();
    });
    $('#btnDeselectAllRoles').on('click', function() {
        $('#panel-role input[type="checkbox"]').prop('checked', false);
        updateAudiencePanels();
    });

    // 3. Select All / Deselect All for Classes
    $('#btnSelectAllClasses').on('click', function() {
        $('#panel-class input[type="checkbox"]').prop('checked', true);
        updateAudiencePanels();
    });
    $('#btnDeselectAllClasses').on('click', function() {
        $('#panel-class input[type="checkbox"]').prop('checked', false);
        updateAudiencePanels();
    });

    // 4. Live Filtering for Specific Staff and Students
    $('.people-search-input').on('keyup', function() {
        var query = $(this).val().toLowerCase().trim();
        var targetId = $(this).data('target');
        var container = $('#' + targetId);
        var visibleCount = 0;

        container.find('.person-item-row').each(function() {
            var searchData = $(this).data('search') || '';
            if (searchData.indexOf(query) !== -1) {
                $(this).removeClass('d-none');
                visibleCount++;
            } else {
                $(this).addClass('d-none');
            }
        });

        if (visibleCount === 0) {
            container.find('.people-no-match').removeClass('d-none');
        } else {
            container.find('.people-no-match').addClass('d-none');
        }
    });

    // 5. PDF Upload Validator
    $('#attachment').on('change', function() {
        var help = $('#attachmentHelp');
        var file = this.files[0];
        help.removeClass('text-danger text-success');

        if (!file) {
            help.text('PDF format only, maximum allowed size 10 MB.').css('color', '#64748b');
            return;
        }

        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
            this.value = '';
            help.text('Invalid file format. Only PDF files are allowed.').addClass('text-danger');
            return;
        }

        var sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        if (file.size > 10 * 1024 * 1024) {
            this.value = '';
            help.text('File size exceeds 10 MB limit (' + sizeMb + ' MB).').addClass('text-danger');
            return;
        }

        help.html('<i class="fa fa-check-circle text-success mr-1"></i> ' + file.name + ' (' + sizeMb + ' MB)').addClass('text-success');
    });

    // 6. Form Reset
    $('#btnResetForm').on('click', function() {
        if (confirm('Are you sure you want to reset all entered notice details?')) {
            noticeForm[0].reset();
            updateAudiencePanels();
            $('#attachmentHelp').text('PDF format only, maximum allowed size 10 MB.').removeClass('text-danger text-success').css('color', '#64748b');
        }
    });

    // 7. Form Submission with Confirmation Modal
    noticeForm.on('submit', function(e) {
        if (isSubmitting) return true;
        e.preventDefault();

        // Check validation for audience
        var selectedType = $('input[name="audience_type"]:checked').val();
        var count = 0;
        if (selectedType === 'role') {
            count = $('#panel-role input[type="checkbox"]:checked').length;
        } else if (selectedType === 'class') {
            count = $('#panel-class input[type="checkbox"]:checked').length;
        } else if (selectedType === 'specific') {
            count = $('#panel-specific input[type="checkbox"]:checked').length;
        }

        if (count === 0) {
            alert('Please select at least one recipient (' + selectedType + ') for this notice broadcast.');
            return false;
        }

        $('#noticeConfirmModal').modal('show');
    });

    $('#btnConfirmSubmitFinal').on('click', function() {
        if (isSubmitting) return;
        isSubmitting = true;
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Broadcasting...');
        $('#btnSubmitNotice').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Broadcasting...');
        noticeForm[0].submit();
    });
});
</script>
@endsection
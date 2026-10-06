@php
    $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = !empty($data->state_id) ? Helper::getCity($data->state_id) : Helper::getCity();
    $getaccounts = Helper::getaccount();
    $getSession = Helper::getSession();
    $currentSessionName = Session::get('session_name') ?? 'Current Session';

    $defaultLogo = env('IMAGE_SHOW_PATH') . 'default/no_image.png';
    $defaultWatermark = env('IMAGE_SHOW_PATH') . 'default/rukmani_logo.png';
    $defaultSeal = env('IMAGE_SHOW_PATH') . 'default/no_image.png';

    $currentLogo = !empty($data->left_logo) ? (env('IMAGE_SHOW_PATH') . 'setting/left_logo/' . $data->left_logo) : $defaultLogo;
    $currentWatermark = !empty($data->watermark_image) ? (env('IMAGE_SHOW_PATH') . 'setting/watermark_image/' . $data->watermark_image) : $defaultWatermark;
    $currentSeal = !empty($data->seal_sign) ? (env('IMAGE_SHOW_PATH') . 'setting/seal_sign/' . $data->seal_sign) : $defaultSeal;

    $activeSessionObj = !empty($getSession) ? collect($getSession)->firstWhere('id', $data->current_active_session_id) : null;
    $activeSessionText = $activeSessionObj ? (($activeSessionObj->from_year ?? '') . ' - ' . ($activeSessionObj->to_year ?? '')) : 'Active Session';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE EDIT INSTITUTE SETTINGS
   - Aligned with Arise ERP Mobile Design Guidelines (Sharp 4px radii, Navy palette)
   - Glassmorphic Hero Card with Live Institute KPIs
   - Interactive Sticky Step Progress Tracker
   - Touch-optimized Section Cards with Prepended Icons
   - Real-time Image Previews with 2MB validation and removal toggles
   - Pinned Bottom Action Dock with Instant Feedback
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
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
.mob-hero-badges {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
    white-space: nowrap;
}
.mob-status-pill {
    font-size: 9.5px;
    background: rgba(74, 222, 128, 0.18);
    border: 1px solid rgba(74, 222, 128, 0.35);
    color: #4ade80;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 800;
    white-space: nowrap;
}
.mob-hero-desc {
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 8px;
    line-height: 1.35;
}

/* Hero Quick Glance Grid */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-kpi-item {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 8px;
    display: flex;
    flex-direction: column;
}
.mob-kpi-label {
    font-size: 8.5px;
    color: #94a3b8;
    font-weight: 700;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-kpi-val {
    font-size: 11px;
    font-weight: 800;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}

/* Hero Fast Actions Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 30px;
    padding: 0 10px;
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
}
.mob-act-btn:active {
    transform: scale(0.96);
}
.mob-act-btn-desktop {
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-refresh {
    background: rgba(56, 189, 248, 0.2);
    color: #38bdf8 !important;
    border: 1px solid rgba(56, 189, 248, 0.35);
}

/* 2. Interactive Sticky Step Progress Tracker */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 10px;
}
.mob-step-tracker {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
}
.mob-step-tracker.is-fixed {
    position: fixed !important;
    top: 54px !important;
    left: 8px !important;
    right: 8px !important;
    width: auto !important;
    z-index: 999 !important;
    margin: 0 !important;
    background: rgba(0, 24, 51, 0.97) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid #0284c7 !important;
    border-radius: 4px !important;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.45) !important;
}
.mob-step-nodes {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 6px;
}
.mob-step-node {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    user-select: none;
    padding: 3px 6px;
    border-radius: 3px;
    transition: all .15s ease;
}
.mob-step-node:active {
    transform: scale(0.95);
}
.mob-step-node.active {
    color: #002C54;
    font-weight: 800;
}
.mob-step-node.completed {
    color: #16a34a;
}
.mob-step-tracker.is-fixed .mob-step-node {
    color: #94a3b8;
}
.mob-step-tracker.is-fixed .mob-step-node.active {
    color: #38bdf8;
}
.mob-step-tracker.is-fixed .mob-step-node.completed {
    color: #4ade80;
}
.mob-step-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    color: #64748b;
    font-weight: 800;
    transition: all .15s ease;
}
.mob-step-node.active .mob-step-dot {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-step-node.completed .mob-step-dot {
    background: #16a34a;
    border-color: #16a34a;
    color: #ffffff;
}
.mob-step-tracker.is-fixed .mob-step-dot {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.25);
    color: #cbd5e1;
}
.mob-step-tracker.is-fixed .mob-step-node.active .mob-step-dot {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}
.mob-progress-bar-bg {
    width: 100%;
    height: 4px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}
.mob-step-tracker.is-fixed .mob-progress-bar-bg {
    background: rgba(255, 255, 255, 0.15);
}
.mob-progress-bar-fill {
    height: 100%;
    width: 33%;
    background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    border-radius: 2px;
    transition: width .25s ease;
}

/* 3. Mobile Form Section Cards */
.mob-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all 0.25s ease;
}
.mob-section-card.highlight-focus {
    border-color: #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.28) !important;
    transform: translateY(-1px);
}
.mob-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 9px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-section-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-step-badge {
    background: #002C54;
    color: #ffffff;
    font-size: 9px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-section-subtitle {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
}

/* Form Groups & Touch Inputs */
.mob-form-group {
    margin-bottom: 8px;
}
.mob-form-group:last-child {
    margin-bottom: 0;
}
.mob-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 3px;
}
.mob-form-label {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 3px;
    margin-bottom: 0;
}
.mob-label-note {
    font-size: 9px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
}
.mob-req-star {
    color: #ef4444;
    font-weight: 800;
}

/* Prepended Input Container */
.mob-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.mob-input-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 12px;
    pointer-events: none;
    z-index: 2;
}
.mob-form-input, .mob-form-select {
    width: 100%;
    height: 34px;
    padding: 0 10px 0 30px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: border-color .15s ease, background-color .15s ease;
    font-family: inherit;
}
.mob-form-input:focus, .mob-form-select:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* 2-Column Row for Mobile */
.mob-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}

/* Toggle Switch Row */
.mob-switch-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    margin-top: 2px;
}
.mob-switch-info {
    display: flex;
    flex-direction: column;
}
.mob-switch-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}
.mob-switch-desc {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-ios-switch {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 22px;
    margin-bottom: 0;
}
.mob-ios-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.mob-ios-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 22px;
}
.mob-ios-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.mob-ios-switch input:checked + .mob-ios-slider {
    background-color: #0284c7;
}
.mob-ios-switch input:checked + .mob-ios-slider:before {
    transform: translateX(16px);
}

/* 4. Branding & Signature Asset Cards */
.mob-asset-box {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    margin-bottom: 8px;
    transition: border-color .15s ease, background-color .15s ease;
}
.mob-asset-box:last-child {
    margin-bottom: 0;
}
.mob-asset-box.marked-removed {
    border-color: #fca5a5;
    background: #fef2f2;
}
.mob-asset-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-asset-name {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-asset-badge {
    font-size: 9px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 2px;
    text-transform: uppercase;
}
.badge-configured {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.badge-default {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.badge-new {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}
.badge-delete {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.mob-asset-body {
    display: flex;
    align-items: center;
    gap: 10px;
}
.mob-asset-preview-frame {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 3px;
    flex-shrink: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-asset-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.mob-asset-controls {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.mob-asset-file-btn {
    height: 29px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    transition: all .12s ease;
    user-select: none;
    margin-bottom: 0;
}
.mob-asset-file-btn:active {
    background: #f1f5f9;
    transform: scale(0.98);
}
.mob-asset-btn-remove {
    height: 24px;
    background: #fee2e2;
    border: 1px solid #fecaca;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-asset-btn-remove.btn-undo {
    background: #e0f2fe;
    border-color: #bae6fd;
    color: #0284c7;
}
.mob-asset-hint {
    font-size: 8.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-asset-error {
    font-size: 9.5px;
    font-weight: 700;
    color: #dc2626;
    margin-top: 3px;
    display: none;
}

/* 5. Fixed Bottom Action Dock */
.mob-form-dock {
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
    padding: 6px 8px;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08), 0 2px 6px rgba(0, 0, 0, 0.04);
}
.mob-btn-dock-cancel {
    height: 34px;
    padding: 0 12px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #475569;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-btn-dock-reset {
    width: 34px;
    height: 34px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.mob-btn-dock-save {
    flex: 1;
    height: 34px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    transition: all .12s ease;
}
.mob-btn-dock-save:active {
    transform: scale(0.98);
}
.mob-btn-dock-save:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}

/* Clearance padding at the bottom of the form */
.mob-form-clearance {
    height: 56px;
}
</style>
@endsection

@section('content')

{{-- Session Flash Notifications --}}
@if(Session::has('message'))
    <div class="alert alert-success alert-dismissible fade show p-2 mb-2" style="font-size: 11.5px; border-radius: 4px; font-weight: 700;" role="alert">
        <i class="fa fa-check-circle mr-1"></i> {{ Session::get('message') }}
        <button type="button" class="close p-1 pr-2" data-dismiss="alert" aria-label="Close" style="font-size: 16px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show p-2 mb-2" style="font-size: 11.5px; border-radius: 4px; font-weight: 700;" role="alert">
        <i class="fa fa-exclamation-triangle mr-1"></i> {{ Session::get('error') }}
        <button type="button" class="close p-1 pr-2" data-dismiss="alert" aria-label="Close" style="font-size: 16px;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if(isset($errors) && $errors->any())
    <div class="alert alert-danger p-2 mb-2" style="font-size: 11px; border-radius: 4px; font-weight: 700;">
        <i class="fa fa-exclamation-circle mr-1"></i> Please resolve the following errors:
        <ul class="mb-0 pl-3 mt-1" style="font-weight: 500;">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-cogs text-primary"></i> Institute Settings
        </div>
        <div class="mob-hero-badges">
            <div class="mob-session-pill" title="Active Academic Session">
                <i class="fa fa-calendar-check-o mr-1"></i> {{ $activeSessionText }}
            </div>
            <div class="mob-status-pill">
                <i class="fa fa-shield"></i> ID #{{ $data->id }}
            </div>
        </div>
    </div>
    <div class="mob-hero-desc">
        Configure school profile, branch affiliations, academic session, branding assets and mobile APK settings.
    </div>

    {{-- Glance Metrics Grid --}}
    <div class="mob-kpi-grid">
        <div class="mob-kpi-item">
            <span class="mob-kpi-label"><i class="fa fa-phone"></i> Contact Phone</span>
            <span class="mob-kpi-val">{{ $data->mobile ?: 'Not Set' }}</span>
        </div>
        <div class="mob-kpi-item">
            <span class="mob-kpi-label"><i class="fa fa-envelope-o"></i> Official Email</span>
            <span class="mob-kpi-val">{{ $data->gmail ?: 'Not Set' }}</span>
        </div>
        <div class="mob-kpi-item">
            <span class="mob-kpi-label"><i class="fa fa-map-pin"></i> Pin Code</span>
            <span class="mob-kpi-val">{{ $data->pincode ?: '-' }}</span>
        </div>
        <div class="mob-kpi-item">
            <span class="mob-kpi-label"><i class="fa fa-bell-o"></i> Firebase Alert</span>
            <span class="mob-kpi-val" style="color: {{ !empty($data->firebase_notification) ? '#4ade80' : '#94a3b8' }};">
                {{ !empty($data->firebase_notification) ? 'Enabled' : 'Disabled' }}
            </span>
        </div>
    </div>

    {{-- Fast Actions --}}
    <div class="mob-actions-bar">
        <a href="{{ url('editSetting/' . $data->id) }}?layout=desktop" class="mob-act-btn mob-act-btn-desktop">
            <i class="fa fa-desktop"></i> Desktop View
        </a>
        <a href="{{ url('editSetting/' . $data->id) }}" class="mob-act-btn mob-act-btn-refresh">
            <i class="fa fa-refresh"></i> Refresh Data
        </a>
    </div>
</div>

{{-- 2. Interactive Form Step Progress Tracker --}}
<div class="mob-tracker-container" id="trackerContainer">
    <div class="mob-step-tracker" id="mobStepTracker">
        <div class="mob-step-nodes">
            <div class="mob-step-node active" id="trackerStep1">
                <span class="mob-step-dot" id="dotStep1">1</span>
                <span>Profile &amp; Address</span>
            </div>
            <div class="mob-step-node" id="trackerStep2">
                <span class="mob-step-dot" id="dotStep2">2</span>
                <span>Session &amp; App</span>
            </div>
            <div class="mob-step-node" id="trackerStep3">
                <span class="mob-step-dot" id="dotStep3">3</span>
                <span>Branding</span>
            </div>
        </div>
        <div class="mob-progress-bar-bg">
            <div class="mob-progress-bar-fill" id="formProgressFill"></div>
        </div>
    </div>
</div>

{{-- 3. Settings Form --}}
<form id="mobSettingsForm" action="{{ url('editSetting', $data->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- =========================================================================
         STEP 1: SCHOOL PROFILE & LOCATION DETAILS
         ========================================================================= --}}
    <div class="mob-section-card" id="cardStep1">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">Step 1</span>
                <i class="fa fa-university text-primary"></i> School Profile &amp; Location
            </div>
            <span class="mob-section-subtitle">Basic details &amp; contact</span>
        </div>

        {{-- School Name --}}
        <div class="mob-form-group">
            <div class="mob-label-row">
                <label class="mob-form-label" for="mob_name">
                    School / Institute Name <span class="mob-req-star">*</span>
                </label>
                <span class="mob-label-note">Official Name</span>
            </div>
            <div class="mob-input-wrap">
                <i class="fa fa-university mob-input-icon"></i>
                <input type="text" 
                       class="mob-form-input" 
                       id="mob_name" 
                       name="name" 
                       placeholder="Enter official school name" 
                       value="{{ old('name', $data->name) }}" 
                       required>
            </div>
        </div>

        {{-- SuperAdmin Branch Association --}}
        @if(Session::get('role_id') == 1)
        <div class="mob-form-group">
            <div class="mob-label-row">
                <label class="mob-form-label" for="mob_branch_id">
                    Branch Association
                </label>
                <span class="mob-label-note">SuperAdmin Only</span>
            </div>
            <div class="mob-input-wrap">
                <i class="fa fa-building-o mob-input-icon"></i>
                <select class="mob-form-select" id="mob_branch_id" name="branch_id">
                    <option value="">Select Branch</option>
                    @if(!empty($branch))
                        @foreach($branch as $b)
                            <option value="{{ $b->id }}" {{ ($b->id == old('branch_id', $data->branch_id)) ? 'selected' : '' }}>
                                {{ $b->branch_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        @endif

        {{-- Mobile & Email Row --}}
        <div class="mob-row-2">
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_mobile">
                        Mobile No. <span class="mob-req-star">*</span>
                    </label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-phone mob-input-icon"></i>
                    <input type="tel" 
                           inputmode="numeric"
                           class="mob-form-input" 
                           id="mob_mobile" 
                           name="mobile" 
                           placeholder="10 Digits" 
                           value="{{ old('mobile', $data->mobile) }}" 
                           maxlength="10" 
                           minlength="10" 
                           onkeypress="return isNumber(event)" 
                           required>
                </div>
            </div>

            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_gmail">
                        Email Address <span class="mob-req-star">*</span>
                    </label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-envelope-o mob-input-icon"></i>
                    <input type="email" 
                           class="mob-form-input" 
                           id="mob_gmail" 
                           name="gmail" 
                           placeholder="info@school.com" 
                           value="{{ old('gmail', $data->gmail) }}" 
                           required>
                </div>
            </div>
        </div>

        {{-- Street Address --}}
        <div class="mob-form-group">
            <div class="mob-label-row">
                <label class="mob-form-label" for="mob_address">
                    Complete Street Address <span class="mob-req-star">*</span>
                </label>
                <span class="mob-label-note">Campus Location</span>
            </div>
            <div class="mob-input-wrap">
                <i class="fa fa-map-marker mob-input-icon"></i>
                <input type="text" 
                       class="mob-form-input" 
                       id="mob_address" 
                       name="address" 
                       placeholder="Enter street, area, building address" 
                       value="{{ old('address', $data->address) }}" 
                       required>
            </div>
        </div>

        {{-- Country & State Row --}}
        <div class="mob-row-2">
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_country_id">Country</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-globe mob-input-icon"></i>
                    <select class="mob-form-select" name="country_id" id="mob_country_id">
                        <option value="">Select Country</option>
                        @if(!empty($getCountry))
                            @foreach($getCountry as $country)
                                <option value="{{ $country->id }}" {{ ($country->id == old('country_id', $data->country_id)) ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_state_id">State</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-map mob-input-icon"></i>
                    <select class="mob-form-select" name="state_id" id="mob_state_id">
                        <option value="">Select State</option>
                        @if(!empty($getState))
                            @foreach($getState as $state)
                                <option value="{{ $state->id }}" {{ ($state->id == old('state_id', $data->state_id)) ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>

        {{-- City & Pin Code Row --}}
        <div class="mob-row-2">
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_city_id">City</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-building mob-input-icon"></i>
                    <select class="mob-form-select" name="city_id" id="mob_city_id">
                        <option value="">Select City</option>
                        @if(!empty($getCity))
                            @foreach($getCity as $city)
                                <option value="{{ $city->id }}" {{ ($city->id == old('city_id', $data->city_id)) ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_pincode">
                        Pin Code <span class="mob-req-star">*</span>
                    </label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-map-pin mob-input-icon"></i>
                    <input type="tel" 
                           inputmode="numeric"
                           class="mob-form-input" 
                           id="mob_pincode" 
                           name="pincode" 
                           placeholder="6 Digits" 
                           value="{{ old('pincode', $data->pincode) }}" 
                           maxlength="6" 
                           onkeypress="return isNumber(event)" 
                           required>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================================
         STEP 2: ACADEMIC SESSION & SYSTEM CONFIGURATION
         ========================================================================= --}}
    <div class="mob-section-card" id="cardStep2">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">Step 2</span>
                <i class="fa fa-sliders text-info"></i> Session &amp; App Config
            </div>
            <span class="mob-section-subtitle">Academic cycle &amp; services</span>
        </div>

        {{-- Current Active Academic Session --}}
        <div class="mob-form-group">
            <div class="mob-label-row">
                <label class="mob-form-label" for="mob_current_active_session_id">
                    Active Academic Session <span class="mob-req-star">*</span>
                </label>
                <span class="mob-label-note">Default Cycle</span>
            </div>
            <div class="mob-input-wrap">
                <i class="fa fa-calendar-check-o mob-input-icon"></i>
                <select class="mob-form-select" id="mob_current_active_session_id" name="current_active_session_id" required>
                    <option value="">Select Academic Session</option>
                    @if(!empty($getSession))
                        @foreach($getSession as $session)
                            <option value="{{ $session->id }}" {{ ($session->id == old('current_active_session_id', $data->current_active_session_id)) ? 'selected' : '' }}>
                                {{ $session->from_year ?? '' }} - {{ $session->to_year ?? '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        {{-- Firebase Push Notifications Toggle --}}
        <div class="mob-switch-card mb-2">
            <div class="mob-switch-info">
                <span class="mob-switch-title"><i class="fa fa-bell text-warning mr-1"></i> Firebase Push Notifications</span>
                <span class="mob-switch-desc">Send automated notifications to mobile app</span>
            </div>
            <label class="mob-ios-switch">
                <input type="checkbox" name="firebase_notification" id="mobFirebaseNotification" value="1" {{ (!empty($data->firebase_notification) && $data->firebase_notification == 1) ? 'checked' : '' }}>
                <span class="mob-ios-slider"></span>
            </label>
        </div>

        {{-- Total, Rank & Test Date --}}
        <div class="mob-row-2">
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_total">Score Total</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-calculator mob-input-icon"></i>
                    <input type="text" class="mob-form-input" id="mob_total" name="total" placeholder="Total Score" value="{{ old('total', $data->total) }}">
                </div>
            </div>

            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_rank">Rank Level</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-trophy mob-input-icon"></i>
                    <input type="text" class="mob-form-input" id="mob_rank" name="rank" placeholder="Rank" value="{{ old('rank', $data->rank) }}">
                </div>
            </div>
        </div>

        <div class="mob-row-2">
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_test_date">Test Date</label>
                </div>
                <div class="mob-input-wrap">
                    <i class="fa fa-calendar mob-input-icon"></i>
                    <input type="date" class="mob-form-input" id="mob_test_date" name="test_date" value="{{ old('test_date', $data->test_date) }}">
                </div>
            </div>

            {{-- APK File Management --}}
            <div class="mob-form-group">
                <div class="mob-label-row">
                    <label class="mob-form-label" for="mob_apk">
                        Android APK
                        <input type="hidden" name="remove_apk" id="mob_remove_apk" value="0">
                    </label>
                    @if(!empty($data->apk))
                        <span id="mob_badge_apk" class="badge-configured mob-asset-badge">Present</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-1">
                    <input type="file" class="mob-form-input" id="mob_apk" name="apk" accept=".apk" style="padding: 4px 6px; font-size: 10px; height: 34px;">
                    @if(!empty($data->apk))
                        <button type="button" class="btn btn-outline-danger btn-xs" id="mob_btn_remove_apk" onclick="toggleRemoveApkMob()" title="Remove APK file" style="height: 34px; width: 34px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- =========================================================================
         STEP 3: INSTITUTE BRANDING ASSETS & SIGNATURES
         ========================================================================= --}}
    <div class="mob-section-card" id="cardStep3">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">Step 3</span>
                <i class="fa fa-picture-o text-warning"></i> Branding &amp; Signatures
            </div>
            <span class="mob-section-subtitle">Live previews &amp; assets</span>
        </div>

        {{-- 1. Main School Logo --}}
        <div class="mob-asset-box" id="mob_card_left_logo">
            <input type="hidden" name="remove_left_logo" id="mob_remove_left_logo" value="0">
            <div class="mob-asset-top">
                <span class="mob-asset-name">
                    <i class="fa fa-bookmark text-primary"></i> School Main Logo
                </span>
                <span id="mob_badge_left_logo" class="mob-asset-badge {{ !empty($data->left_logo) ? 'badge-configured' : 'badge-default' }}">
                    {{ !empty($data->left_logo) ? 'Configured' : 'Default / None' }}
                </span>
            </div>
            <div class="mob-asset-body">
                <div class="mob-asset-preview-frame">
                    <img id="mob_preview_left_logo" 
                         class="mob-asset-preview-img" 
                         src="{{ $currentLogo }}" 
                         data-original-src="{{ $currentLogo }}" 
                         data-default-src="{{ $defaultLogo }}" 
                         alt="Main Logo" 
                         onerror="this.src='{{ $defaultLogo }}'">
                </div>
                <div class="mob-asset-controls">
                    <label for="mob_left_logo" class="mob-asset-file-btn">
                        <i class="fa fa-upload text-primary"></i> Choose Logo
                    </label>
                    <input type="file" 
                           id="mob_left_logo" 
                           name="left_logo" 
                           accept="image/png, image/jpg, image/jpeg, image/webp" 
                           style="display:none;" 
                           onchange="handleMobAssetChange(this, 'left_logo')">
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="mob-asset-hint">PNG / JPG (Max 2MB)</span>
                        @if(!empty($data->left_logo))
                            <button type="button" class="mob-asset-btn-remove" id="mob_btn_rm_left_logo" onclick="toggleMobAssetRemoval('left_logo')">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        @endif
                    </div>
                    <div id="mob_error_left_logo" class="mob-asset-error"></div>
                </div>
            </div>
        </div>

        {{-- 2. Report Watermark Image --}}
        <div class="mob-asset-box" id="mob_card_watermark">
            <input type="hidden" name="remove_watermark_image" id="mob_remove_watermark_image" value="0">
            <div class="mob-asset-top">
                <span class="mob-asset-name">
                    <i class="fa fa-file-image-o text-info"></i> Report Watermark Image
                </span>
                <span id="mob_badge_watermark" class="mob-asset-badge {{ !empty($data->watermark_image) ? 'badge-configured' : 'badge-default' }}">
                    {{ !empty($data->watermark_image) ? 'Configured' : 'Default / None' }}
                </span>
            </div>
            <div class="mob-asset-body">
                <div class="mob-asset-preview-frame">
                    <img id="mob_preview_watermark" 
                         class="mob-asset-preview-img" 
                         src="{{ $currentWatermark }}" 
                         data-original-src="{{ $currentWatermark }}" 
                         data-default-src="{{ $defaultWatermark }}" 
                         alt="Watermark Image" 
                         onerror="this.src='{{ $defaultWatermark }}'">
                </div>
                <div class="mob-asset-controls">
                    <label for="mob_watermark_image" class="mob-asset-file-btn">
                        <i class="fa fa-upload text-info"></i> Choose Watermark
                    </label>
                    <input type="file" 
                           id="mob_watermark_image" 
                           name="watermark_image" 
                           accept="image/png, image/jpg, image/jpeg, image/webp" 
                           style="display:none;" 
                           onchange="handleMobAssetChange(this, 'watermark_image')">
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="mob-asset-hint">PNG / JPG (Max 2MB)</span>
                        @if(!empty($data->watermark_image))
                            <button type="button" class="mob-asset-btn-remove" id="mob_btn_rm_watermark_image" onclick="toggleMobAssetRemoval('watermark_image')">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        @endif
                    </div>
                    <div id="mob_error_watermark_image" class="mob-asset-error"></div>
                </div>
            </div>
        </div>

        {{-- 3. Official Seal & Signature --}}
        <div class="mob-asset-box" id="mob_card_seal_sign">
            <input type="hidden" name="remove_seal_sign" id="mob_remove_seal_sign" value="0">
            <div class="mob-asset-top">
                <span class="mob-asset-name">
                    <i class="fa fa-certificate text-danger"></i> Official Seal &amp; Signature
                </span>
                <span id="mob_badge_seal_sign" class="mob-asset-badge {{ !empty($data->seal_sign) ? 'badge-configured' : 'badge-default' }}">
                    {{ !empty($data->seal_sign) ? 'Configured' : 'Default / None' }}
                </span>
            </div>
            <div class="mob-asset-body">
                <div class="mob-asset-preview-frame">
                    <img id="mob_preview_seal_sign" 
                         class="mob-asset-preview-img" 
                         src="{{ $currentSeal }}" 
                         data-original-src="{{ $currentSeal }}" 
                         data-default-src="{{ $defaultSeal }}" 
                         alt="Seal & Signature" 
                         onerror="this.src='{{ $defaultSeal }}'">
                </div>
                <div class="mob-asset-controls">
                    <label for="mob_seal_sign" class="mob-asset-file-btn">
                        <i class="fa fa-upload text-danger"></i> Choose Seal
                    </label>
                    <input type="file" 
                           id="mob_seal_sign" 
                           name="seal_sign" 
                           accept="image/png, image/jpg, image/jpeg, image/webp" 
                           style="display:none;" 
                           onchange="handleMobAssetChange(this, 'seal_sign')">
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="mob-asset-hint">PNG / JPG (Max 2MB)</span>
                        @if(!empty($data->seal_sign))
                            <button type="button" class="mob-asset-btn-remove" id="mob_btn_rm_seal_sign" onclick="toggleMobAssetRemoval('seal_sign')">
                                <i class="fa fa-trash"></i> Remove
                            </button>
                        @endif
                    </div>
                    <div id="mob_error_seal_sign" class="mob-asset-error"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- Bottom clearance so dock does not block content --}}
    <div class="mob-form-clearance"></div>

    {{-- 4. Pinned Bottom Action Dock --}}
    <div class="mob-form-dock" id="mobFormDock">
        <a href="{{ url('viewSetting') }}" class="mob-btn-dock-cancel">
            <i class="fa fa-arrow-left"></i> Cancel
        </a>
        <button type="button" class="mob-btn-dock-reset" onclick="resetAllMobAssets()" title="Reset Previews">
            <i class="fa fa-refresh"></i>
        </button>
        <button type="submit" class="mob-btn-dock-save" id="btnSubmitMobSettings">
            <i class="fa fa-check-circle"></i> Update Settings
        </button>
    </div>

</form>

@endsection

@section('scripts')
<script>
const MAX_IMAGE_SIZE = 2 * 1024 * 1024; // 2MB

function isNumber(evt) {
    const ch = String.fromCharCode(evt.which);
    if (!(/[0-9]/).test(ch)) {
        evt.preventDefault();
        return false;
    }
    return true;
}

/**
 * Handle real-time file upload preview with 2MB validation
 */
function handleMobAssetChange(input, fieldKey) {
    const keyMap = {
        'left_logo': { preview: 'mob_preview_left_logo', error: 'mob_error_left_logo', badge: 'mob_badge_left_logo', card: 'mob_card_left_logo', remove: 'mob_remove_left_logo' },
        'watermark_image': { preview: 'mob_preview_watermark', error: 'mob_error_watermark_image', badge: 'mob_badge_watermark', card: 'mob_card_watermark', remove: 'mob_remove_watermark_image' },
        'seal_sign': { preview: 'mob_preview_seal_sign', error: 'mob_error_seal_sign', badge: 'mob_badge_seal_sign', card: 'mob_card_seal_sign', remove: 'mob_remove_seal_sign' }
    };

    const cfg = keyMap[fieldKey];
    if (!cfg) return;

    const previewEl = document.getElementById(cfg.preview);
    const errorEl = document.getElementById(cfg.error);
    const badgeEl = document.getElementById(cfg.badge);
    const cardEl = document.getElementById(cfg.card);
    const removeInput = document.getElementById(cfg.remove);

    if (errorEl) { errorEl.style.display = 'none'; errorEl.innerHTML = ''; }
    if (removeInput) removeInput.value = '0';
    if (cardEl) cardEl.classList.remove('marked-removed');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const ext = file.name.split('.').pop().toLowerCase();

        if (!['png', 'jpg', 'jpeg', 'webp'].includes(ext)) {
            if (errorEl) {
                errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> Only PNG, JPG, or JPEG images allowed.";
                errorEl.style.display = 'block';
            }
            input.value = "";
            return;
        }

        if (file.size > MAX_IMAGE_SIZE) {
            if (errorEl) {
                errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> Image size (" + (file.size / 1024 / 1024).toFixed(2) + "MB) exceeds 2MB limit.";
                errorEl.style.display = 'block';
            }
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewEl) previewEl.src = e.target.result;
            if (badgeEl) {
                badgeEl.className = 'mob-asset-badge badge-new';
                badgeEl.innerText = 'New Selected';
            }
        };
        reader.readAsDataURL(file);
    } else {
        if (previewEl) previewEl.src = previewEl.getAttribute('data-original-src');
        if (badgeEl) {
            const hasOrig = previewEl && previewEl.getAttribute('data-original-src') !== previewEl.getAttribute('data-default-src');
            badgeEl.className = hasOrig ? 'mob-asset-badge badge-configured' : 'mob-asset-badge badge-default';
            badgeEl.innerText = hasOrig ? 'Configured' : 'Default / None';
        }
    }
}

/**
 * Toggle asset removal flag
 */
function toggleMobAssetRemoval(fieldKey) {
    const keyMap = {
        'left_logo': { preview: 'mob_preview_left_logo', badge: 'mob_badge_left_logo', card: 'mob_card_left_logo', remove: 'mob_remove_left_logo', btn: 'mob_btn_rm_left_logo', input: 'mob_left_logo' },
        'watermark_image': { preview: 'mob_preview_watermark', badge: 'mob_badge_watermark', card: 'mob_card_watermark', remove: 'mob_remove_watermark_image', btn: 'mob_btn_rm_watermark_image', input: 'mob_watermark_image' },
        'seal_sign': { preview: 'mob_preview_seal_sign', badge: 'mob_badge_seal_sign', card: 'mob_card_seal_sign', remove: 'mob_remove_seal_sign', btn: 'mob_btn_rm_seal_sign', input: 'mob_seal_sign' }
    };

    const cfg = keyMap[fieldKey];
    if (!cfg) return;

    const removeInput = document.getElementById(cfg.remove);
    const fileInput = document.getElementById(cfg.input);
    const previewEl = document.getElementById(cfg.preview);
    const badgeEl = document.getElementById(cfg.badge);
    const cardEl = document.getElementById(cfg.card);
    const btn = document.getElementById(cfg.btn);

    if (removeInput.value === '0') {
        removeInput.value = '1';
        if (fileInput) fileInput.value = '';
        if (cardEl) cardEl.classList.add('marked-removed');
        if (previewEl) previewEl.src = previewEl.getAttribute('data-default-src');
        if (badgeEl) {
            badgeEl.className = 'mob-asset-badge badge-delete';
            badgeEl.innerText = 'Marked For Removal';
        }
        if (btn) {
            btn.className = 'mob-asset-btn-remove btn-undo';
            btn.innerHTML = '<i class="fa fa-undo"></i> Undo';
        }
    } else {
        removeInput.value = '0';
        if (cardEl) cardEl.classList.remove('marked-removed');
        if (previewEl) previewEl.src = previewEl.getAttribute('data-original-src');
        if (badgeEl) {
            const hasOrig = previewEl && previewEl.getAttribute('data-original-src') !== previewEl.getAttribute('data-default-src');
            badgeEl.className = hasOrig ? 'mob-asset-badge badge-configured' : 'mob-asset-badge badge-default';
            badgeEl.innerText = hasOrig ? 'Configured' : 'Default / None';
        }
        if (btn) {
            btn.className = 'mob-asset-btn-remove';
            btn.innerHTML = '<i class="fa fa-trash"></i> Remove';
        }
    }
}

/**
 * Toggle APK removal
 */
function toggleRemoveApkMob() {
    const removeApk = document.getElementById('mob_remove_apk');
    const badge = document.getElementById('mob_badge_apk');
    const btn = document.getElementById('mob_btn_remove_apk');
    const fileInput = document.getElementById('mob_apk');

    if (removeApk.value === '0') {
        removeApk.value = '1';
        if (fileInput) fileInput.value = '';
        if (badge) {
            badge.className = 'mob-asset-badge badge-delete';
            badge.innerText = 'To Delete';
        }
        if (btn) {
            btn.className = 'btn btn-outline-info btn-xs';
            btn.innerHTML = '<i class="fa fa-undo"></i>';
            btn.title = 'Undo APK Removal';
        }
    } else {
        removeApk.value = '0';
        if (badge) {
            badge.className = 'mob-asset-badge badge-configured';
            badge.innerText = 'Present';
        }
        if (btn) {
            btn.className = 'btn btn-outline-danger btn-xs';
            btn.innerHTML = '<i class="fa fa-trash"></i>';
            btn.title = 'Remove APK file';
        }
    }
}

/**
 * Reset all image previews
 */
function resetAllMobAssets() {
    ['left_logo', 'watermark_image', 'seal_sign'].forEach(function(fieldKey) {
        const removeInput = document.getElementById(fieldKey === 'watermark_image' ? 'mob_remove_watermark_image' : ('mob_remove_' + fieldKey));
        if (removeInput && removeInput.value === '1') {
            toggleMobAssetRemoval(fieldKey);
        }
    });
}

$(document).ready(function() {
    // 1. Dynamic city loader on state change
    $('#mob_state_id').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{ url('stateData') }}/" + stateId,
                type: 'GET',
                success: function(data) {
                    $('#mob_city_id').html(data);
                }
            });
        } else {
            $('#mob_city_id').html('<option value="">Select City</option>');
        }
    });

    // 2. Interactive Step Tracker Navigation & Smooth Scrolling
    function updateProgress(stepNum) {
        var fillWidth = '33%';
        if (stepNum === 2) fillWidth = '66%';
        if (stepNum === 3) fillWidth = '100%';
        $('#formProgressFill').css('width', fillWidth);

        $('.mob-step-node').removeClass('active completed');
        for (var i = 1; i <= 3; i++) {
            if (i < stepNum) {
                $('#trackerStep' + i).addClass('completed');
                $('#dotStep' + i).html('<i class="fa fa-check"></i>');
            } else if (i === stepNum) {
                $('#trackerStep' + i).addClass('active');
                $('#dotStep' + i).text(i);
            } else {
                $('#dotStep' + i).text(i);
            }
        }
    }

    function scrollToSection(cardId, stepNum) {
        var el = $(cardId);
        if (el.length) {
            var offsetTop = el.offset().top - 110;
            $('html, body').animate({ scrollTop: offsetTop }, 280, function() {
                el.addClass('highlight-focus');
                setTimeout(function() {
                    el.removeClass('highlight-focus');
                }, 1000);
            });
            updateProgress(stepNum);
        }
    }

    $('#trackerStep1').on('click', function() { scrollToSection('#cardStep1', 1); });
    $('#trackerStep2').on('click', function() { scrollToSection('#cardStep2', 2); });
    $('#trackerStep3').on('click', function() { scrollToSection('#cardStep3', 3); });

    // 3. Sticky Step Tracker on Scroll
    var trackerEl = $('#mobStepTracker');
    var trackerContainer = $('#trackerContainer');

    $(window).on('scroll', function() {
        if (!trackerContainer.length) return;
        var containerTop = trackerContainer.offset().top;
        var scrollY = $(window).scrollTop();

        if (scrollY > (containerTop - 54)) {
            trackerEl.addClass('is-fixed');
        } else {
            trackerEl.removeClass('is-fixed');
        }

        // Active step detection based on scroll position
        var s1Top = $('#cardStep1').offset() ? $('#cardStep1').offset().top - 150 : 0;
        var s2Top = $('#cardStep2').offset() ? $('#cardStep2').offset().top - 150 : 0;
        var s3Top = $('#cardStep3').offset() ? $('#cardStep3').offset().top - 150 : 0;

        if (scrollY >= s3Top) {
            updateProgress(3);
        } else if (scrollY >= s2Top) {
            updateProgress(2);
        } else {
            updateProgress(1);
        }
    });

    // 4. Form Submit Loading Spinner
    $('#mobSettingsForm').on('submit', function() {
        var btn = $('#btnSubmitMobSettings');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
    });
});
</script>
@endsection

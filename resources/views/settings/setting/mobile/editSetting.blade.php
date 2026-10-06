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
    margin-bottom: 0;
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

/* 4. Branding & Signature Asset Cards (Redesigned) */
.mob-brand-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all .15s ease;
}
.mob-brand-card:last-child {
    margin-bottom: 0;
}
.mob-brand-card.marked-removed {
    background: #fffafa;
    border-color: #fca5a5;
}
.mob-brand-card.has-new-file {
    border-color: #0284c7;
    background: #f0f9ff;
}

/* Card Header */
.mob-brand-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-brand-title-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.mob-brand-icon-box {
    width: 30px;
    height: 30px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    flex-shrink: 0;
}
.icon-navy { background: #e0f2fe; color: #0284c7; }
.icon-info { background: #e0f2fe; color: #0284c7; }
.icon-rose { background: #fee2e2; color: #dc2626; }

.mob-brand-card-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
}
.mob-brand-card-sub {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    line-height: 1.25;
    margin-top: 1px;
}

/* Status Badges */
.mob-brand-status {
    font-size: 9px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.status-configured {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.status-empty {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}
.status-new {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.status-removed {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Body: Preview Frame & Meta Information */
.mob-brand-card-body {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}
.mob-brand-preview-frame {
    width: 76px;
    height: 76px;
    border-radius: 4px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 4px;
    flex-shrink: 0;
    position: relative;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.03);
}
.mob-brand-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: opacity .15s ease;
}
.mob-brand-card.marked-removed .mob-brand-img {
    opacity: 0.25;
    filter: grayscale(1);
}
.mob-brand-fallback {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    color: #94a3b8;
    text-align: center;
    padding: 4px;
}
.mob-brand-fallback i {
    font-size: 24px;
}
.mob-brand-fallback span {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
}

/* Meta Information */
.mob-brand-meta {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-brand-meta-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9.5px;
    line-height: 1.3;
}
.mob-meta-key {
    color: #64748b;
    font-weight: 600;
}
.mob-meta-val {
    color: #1e293b;
    font-weight: 700;
}

/* Error Alert */
.mob-brand-error-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 3px;
    padding: 5px 8px;
    font-size: 10px;
    font-weight: 700;
    margin-bottom: 8px;
    display: none;
}

/* Action Buttons Bar */
.mob-brand-actions-bar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-btn-brand-upload {
    flex: 1;
    height: 34px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all .12s ease;
    user-select: none;
    margin-bottom: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.mob-btn-brand-upload:active {
    background: #e2e8f0;
    transform: scale(0.98);
}
.mob-btn-brand-remove {
    height: 34px;
    padding: 0 14px;
    background: #fee2e2;
    border: 1px solid #fca5a5;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    transition: all .12s ease;
    flex-shrink: 0;
}
.mob-btn-brand-remove:active {
    transform: scale(0.96);
}
.mob-btn-brand-remove.btn-undo {
    background: #e0f2fe;
    border-color: #bae6fd;
    color: #0284c7;
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
        <div class="mob-brand-card" id="mob_card_left_logo">
            <input type="hidden" name="remove_left_logo" id="mob_remove_left_logo" value="0">
            
            <div class="mob-brand-card-header">
                <div class="mob-brand-title-wrap">
                    <div class="mob-brand-icon-box icon-navy">
                        <i class="fa fa-bookmark"></i>
                    </div>
                    <div>
                        <div class="mob-brand-card-title">School Main Logo</div>
                        <div class="mob-brand-card-sub">Fee receipts, ID cards, app header &amp; portal</div>
                    </div>
                </div>
                <span id="mob_badge_left_logo" class="mob-brand-status {{ !empty($data->left_logo) ? 'status-configured' : 'status-empty' }}">
                    {!! !empty($data->left_logo) ? '<i class="fa fa-check-circle"></i> Configured' : '<i class="fa fa-circle-o"></i> Not Set' !!}
                </span>
            </div>

            <div class="mob-brand-card-body">
                <div class="mob-brand-preview-frame">
                    <img id="mob_preview_left_logo" 
                         class="mob-brand-img" 
                         src="{{ $currentLogo }}" 
                         data-original-src="{{ $currentLogo }}" 
                         data-has-original="{{ !empty($data->left_logo) ? '1' : '0' }}"
                         alt="School Logo" 
                         onerror="handleImageFallback(this, 'fallback_left_logo')"
                         style="{{ empty($data->left_logo) ? 'display:none;' : '' }}">
                    <div class="mob-brand-fallback" id="fallback_left_logo" style="{{ !empty($data->left_logo) ? 'display:none;' : 'display:flex;' }}">
                        <i class="fa fa-university text-primary"></i>
                        <span>No Logo</span>
                    </div>
                </div>

                <div class="mob-brand-meta">
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Allowed:</span>
                        <span class="mob-meta-val">PNG, JPG, WEBP</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Max Size:</span>
                        <span class="mob-meta-val">2 MB</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Aspect:</span>
                        <span class="mob-meta-val">Square / Circular</span>
                    </div>
                </div>
            </div>

            <div id="mob_error_left_logo" class="mob-brand-error-alert"></div>

            <div class="mob-brand-actions-bar">
                <label for="mob_left_logo" class="mob-btn-brand-upload">
                    <i class="fa fa-camera text-primary"></i> <span>{{ !empty($data->left_logo) ? 'Change Logo' : 'Choose Logo' }}</span>
                </label>
                <input type="file" 
                       id="mob_left_logo" 
                       name="left_logo" 
                       accept="image/png, image/jpg, image/jpeg, image/webp" 
                       style="display:none;" 
                       onchange="handleMobAssetChange(this, 'left_logo')">
                
                <button type="button" 
                        class="mob-btn-brand-remove" 
                        id="mob_btn_rm_left_logo" 
                        onclick="toggleMobAssetRemoval('left_logo')"
                        style="{{ !empty($data->left_logo) ? '' : 'display:none;' }}">
                    <i class="fa fa-trash"></i> <span>Remove</span>
                </button>
            </div>
        </div>

        {{-- 2. Report Watermark Image --}}
        <div class="mob-brand-card" id="mob_card_watermark">
            <input type="hidden" name="remove_watermark_image" id="mob_remove_watermark_image" value="0">
            
            <div class="mob-brand-card-header">
                <div class="mob-brand-title-wrap">
                    <div class="mob-brand-icon-box icon-info">
                        <i class="fa fa-file-image-o"></i>
                    </div>
                    <div>
                        <div class="mob-brand-card-title">Report Watermark Image</div>
                        <div class="mob-brand-card-sub">Background crest on receipts &amp; marksheets</div>
                    </div>
                </div>
                <span id="mob_badge_watermark" class="mob-brand-status {{ !empty($data->watermark_image) ? 'status-configured' : 'status-empty' }}">
                    {!! !empty($data->watermark_image) ? '<i class="fa fa-check-circle"></i> Configured' : '<i class="fa fa-circle-o"></i> Not Set' !!}
                </span>
            </div>

            <div class="mob-brand-card-body">
                <div class="mob-brand-preview-frame">
                    <img id="mob_preview_watermark" 
                          class="mob-brand-img" 
                          src="{{ $currentWatermark }}" 
                          data-original-src="{{ $currentWatermark }}" 
                          data-has-original="{{ !empty($data->watermark_image) ? '1' : '0' }}"
                          alt="Watermark" 
                          onerror="handleImageFallback(this, 'fallback_watermark')"
                          style="{{ empty($data->watermark_image) ? 'display:none;' : '' }}">
                    <div class="mob-brand-fallback" id="fallback_watermark" style="{{ !empty($data->watermark_image) ? 'display:none;' : 'display:flex;' }}">
                        <i class="fa fa-file-text-o text-info"></i>
                        <span>No Crest</span>
                    </div>
                </div>

                <div class="mob-brand-meta">
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Allowed:</span>
                        <span class="mob-meta-val">PNG, JPG, WEBP</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Max Size:</span>
                        <span class="mob-meta-val">2 MB</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Tone:</span>
                        <span class="mob-meta-val">Light opacity recommended</span>
                    </div>
                </div>
            </div>

            <div id="mob_error_watermark_image" class="mob-brand-error-alert"></div>

            <div class="mob-brand-actions-bar">
                <label for="mob_watermark_image" class="mob-btn-brand-upload">
                    <i class="fa fa-camera text-info"></i> <span>{{ !empty($data->watermark_image) ? 'Change Watermark' : 'Choose Watermark' }}</span>
                </label>
                <input type="file" 
                       id="mob_watermark_image" 
                       name="watermark_image" 
                       accept="image/png, image/jpg, image/jpeg, image/webp" 
                       style="display:none;" 
                       onchange="handleMobAssetChange(this, 'watermark_image')">
                
                <button type="button" 
                        class="mob-btn-brand-remove" 
                        id="mob_btn_rm_watermark_image" 
                        onclick="toggleMobAssetRemoval('watermark_image')"
                        style="{{ !empty($data->watermark_image) ? '' : 'display:none;' }}">
                    <i class="fa fa-trash"></i> <span>Remove</span>
                </button>
            </div>
        </div>

        {{-- 3. Official Seal & Signature --}}
        <div class="mob-brand-card" id="mob_card_seal_sign">
            <input type="hidden" name="remove_seal_sign" id="mob_remove_seal_sign" value="0">
            
            <div class="mob-brand-card-header">
                <div class="mob-brand-title-wrap">
                    <div class="mob-brand-icon-box icon-rose">
                        <i class="fa fa-certificate"></i>
                    </div>
                    <div>
                        <div class="mob-brand-card-title">Official Seal &amp; Signature</div>
                        <div class="mob-brand-card-sub">Stamp &amp; principal sign for T.C. &amp; certificates</div>
                    </div>
                </div>
                <span id="mob_badge_seal_sign" class="mob-brand-status {{ !empty($data->seal_sign) ? 'status-configured' : 'status-empty' }}">
                    {!! !empty($data->seal_sign) ? '<i class="fa fa-check-circle"></i> Configured' : '<i class="fa fa-circle-o"></i> Not Set' !!}
                </span>
            </div>

            <div class="mob-brand-card-body">
                <div class="mob-brand-preview-frame">
                    <img id="mob_preview_seal_sign" 
                         class="mob-brand-img" 
                         src="{{ $currentSeal }}" 
                         data-original-src="{{ $currentSeal }}" 
                         data-has-original="{{ !empty($data->seal_sign) ? '1' : '0' }}"
                         alt="Seal & Sign" 
                         onerror="handleImageFallback(this, 'fallback_seal_sign')"
                         style="{{ empty($data->seal_sign) ? 'display:none;' : '' }}">
                    <div class="mob-brand-fallback" id="fallback_seal_sign" style="{{ !empty($data->seal_sign) ? 'display:none;' : 'display:flex;' }}">
                        <i class="fa fa-certificate text-danger"></i>
                        <span>No Stamp</span>
                    </div>
                </div>

                <div class="mob-brand-meta">
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Allowed:</span>
                        <span class="mob-meta-val">PNG, JPG, WEBP</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Max Size:</span>
                        <span class="mob-meta-val">2 MB</span>
                    </div>
                    <div class="mob-brand-meta-item">
                        <span class="mob-meta-key">Format:</span>
                        <span class="mob-meta-val">High contrast on white</span>
                    </div>
                </div>
            </div>

            <div id="mob_error_seal_sign" class="mob-brand-error-alert"></div>

            <div class="mob-brand-actions-bar">
                <label for="mob_seal_sign" class="mob-btn-brand-upload">
                    <i class="fa fa-camera text-danger"></i> <span>{{ !empty($data->seal_sign) ? 'Change Seal' : 'Choose Seal' }}</span>
                </label>
                <input type="file" 
                       id="mob_seal_sign" 
                       name="seal_sign" 
                       accept="image/png, image/jpg, image/jpeg, image/webp" 
                       style="display:none;" 
                       onchange="handleMobAssetChange(this, 'seal_sign')">
                
                <button type="button" 
                        class="mob-btn-brand-remove" 
                        id="mob_btn_rm_seal_sign" 
                        onclick="toggleMobAssetRemoval('seal_sign')"
                        style="{{ !empty($data->seal_sign) ? '' : 'display:none;' }}">
                    <i class="fa fa-trash"></i> <span>Remove</span>
                </button>
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
 * Safe Image Fallback Handler
 * Hides broken image tag and reveals fallback placeholder icon
 */
function handleImageFallback(imgEl, fallbackId) {
    if (imgEl) {
        imgEl.style.display = 'none';
    }
    const fallback = document.getElementById(fallbackId);
    if (fallback) {
        fallback.style.display = 'flex';
    }
}

/**
 * Handle real-time file upload preview with 2MB validation
 */
function handleMobAssetChange(input, fieldKey) {
    const keyMap = {
        'left_logo': {
            preview: 'mob_preview_left_logo',
            fallback: 'fallback_left_logo',
            error: 'mob_error_left_logo',
            badge: 'mob_badge_left_logo',
            card: 'mob_card_left_logo',
            remove: 'mob_remove_left_logo',
            btn: 'mob_btn_rm_left_logo'
        },
        'watermark_image': {
            preview: 'mob_preview_watermark',
            fallback: 'fallback_watermark',
            error: 'mob_error_watermark_image',
            badge: 'mob_badge_watermark',
            card: 'mob_card_watermark',
            remove: 'mob_remove_watermark_image',
            btn: 'mob_btn_rm_watermark_image'
        },
        'seal_sign': {
            preview: 'mob_preview_seal_sign',
            fallback: 'fallback_seal_sign',
            error: 'mob_error_seal_sign',
            badge: 'mob_badge_seal_sign',
            card: 'mob_card_seal_sign',
            remove: 'mob_remove_seal_sign',
            btn: 'mob_btn_rm_seal_sign'
        }
    };

    const cfg = keyMap[fieldKey];
    if (!cfg) return;

    const previewEl = document.getElementById(cfg.preview);
    const fallbackEl = document.getElementById(cfg.fallback);
    const errorEl = document.getElementById(cfg.error);
    const badgeEl = document.getElementById(cfg.badge);
    const cardEl = document.getElementById(cfg.card);
    const removeInput = document.getElementById(cfg.remove);
    const btn = document.getElementById(cfg.btn);

    if (errorEl) { errorEl.style.display = 'none'; errorEl.innerHTML = ''; }
    if (removeInput) removeInput.value = '0';
    if (cardEl) cardEl.classList.remove('marked-removed');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const ext = file.name.split('.').pop().toLowerCase();

        if (!['png', 'jpg', 'jpeg', 'webp'].includes(ext)) {
            if (errorEl) {
                errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> Only PNG, JPG, or WEBP images are allowed.";
                errorEl.style.display = 'block';
            }
            input.value = "";
            return;
        }

        if (file.size > MAX_IMAGE_SIZE) {
            if (errorEl) {
                errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> File size (" + (file.size / 1024 / 1024).toFixed(2) + " MB) exceeds 2 MB limit.";
                errorEl.style.display = 'block';
            }
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewEl) {
                previewEl.src = e.target.result;
                previewEl.style.display = 'block';
            }
            if (fallbackEl) {
                fallbackEl.style.display = 'none';
            }
            if (cardEl) {
                cardEl.classList.add('has-new-file');
            }
            if (badgeEl) {
                badgeEl.className = 'mob-brand-status status-new';
                badgeEl.innerHTML = '<i class="fa fa-upload"></i> New File';
            }
            if (btn) {
                btn.style.display = 'inline-flex';
                btn.className = 'mob-btn-brand-remove';
                btn.innerHTML = '<i class="fa fa-times"></i> <span>Cancel</span>';
            }
        };
        reader.readAsDataURL(file);
    } else {
        resetSingleMobAsset(fieldKey);
    }
}

/**
 * Toggle asset removal flag
 */
function toggleMobAssetRemoval(fieldKey) {
    const keyMap = {
        'left_logo': {
            preview: 'mob_preview_left_logo',
            fallback: 'fallback_left_logo',
            badge: 'mob_badge_left_logo',
            card: 'mob_card_left_logo',
            remove: 'mob_remove_left_logo',
            btn: 'mob_btn_rm_left_logo',
            input: 'mob_left_logo'
        },
        'watermark_image': {
            preview: 'mob_preview_watermark',
            fallback: 'fallback_watermark',
            badge: 'mob_badge_watermark',
            card: 'mob_card_watermark',
            remove: 'mob_remove_watermark_image',
            btn: 'mob_btn_rm_watermark_image',
            input: 'mob_watermark_image'
        },
        'seal_sign': {
            preview: 'mob_preview_seal_sign',
            fallback: 'fallback_seal_sign',
            badge: 'mob_badge_seal_sign',
            card: 'mob_card_seal_sign',
            remove: 'mob_remove_seal_sign',
            btn: 'mob_btn_rm_seal_sign',
            input: 'mob_seal_sign'
        }
    };

    const cfg = keyMap[fieldKey];
    if (!cfg) return;

    const removeInput = document.getElementById(cfg.remove);
    const fileInput = document.getElementById(cfg.input);
    const previewEl = document.getElementById(cfg.preview);
    const fallbackEl = document.getElementById(cfg.fallback);
    const badgeEl = document.getElementById(cfg.badge);
    const cardEl = document.getElementById(cfg.card);
    const btn = document.getElementById(cfg.btn);
    const hasOriginal = previewEl && previewEl.getAttribute('data-has-original') === '1';

    // If user previously selected a new file and wants to cancel that selection
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
        fileInput.value = '';
        if (cardEl) cardEl.classList.remove('has-new-file');

        if (hasOriginal) {
            if (previewEl) {
                previewEl.src = previewEl.getAttribute('data-original-src');
                previewEl.style.display = 'block';
            }
            if (fallbackEl) fallbackEl.style.display = 'none';
            if (badgeEl) {
                badgeEl.className = 'mob-brand-status status-configured';
                badgeEl.innerHTML = '<i class="fa fa-check-circle"></i> Configured';
            }
            if (btn) {
                btn.className = 'mob-btn-brand-remove';
                btn.innerHTML = '<i class="fa fa-trash"></i> <span>Remove</span>';
                btn.style.display = 'inline-flex';
            }
        } else {
            if (previewEl) previewEl.style.display = 'none';
            if (fallbackEl) fallbackEl.style.display = 'flex';
            if (badgeEl) {
                badgeEl.className = 'mob-brand-status status-empty';
                badgeEl.innerHTML = '<i class="fa fa-circle-o"></i> Not Set';
            }
            if (btn) btn.style.display = 'none';
        }
        return;
    }

    // Toggle removal flag for existing saved image
    if (removeInput.value === '0') {
        removeInput.value = '1';
        if (fileInput) fileInput.value = '';
        if (cardEl) {
            cardEl.classList.remove('has-new-file');
            cardEl.classList.add('marked-removed');
        }
        if (badgeEl) {
            badgeEl.className = 'mob-brand-status status-removed';
            badgeEl.innerHTML = '<i class="fa fa-trash"></i> Marked to Remove';
        }
        if (btn) {
            btn.className = 'mob-btn-brand-remove btn-undo';
            btn.innerHTML = '<i class="fa fa-undo"></i> <span>Undo</span>';
        }
    } else {
        removeInput.value = '0';
        if (cardEl) cardEl.classList.remove('marked-removed');
        if (previewEl) {
            previewEl.src = previewEl.getAttribute('data-original-src');
            previewEl.style.display = 'block';
        }
        if (fallbackEl) fallbackEl.style.display = 'none';
        if (badgeEl) {
            badgeEl.className = 'mob-brand-status status-configured';
            badgeEl.innerHTML = '<i class="fa fa-check-circle"></i> Configured';
        }
        if (btn) {
            btn.className = 'mob-btn-brand-remove';
            btn.innerHTML = '<i class="fa fa-trash"></i> <span>Remove</span>';
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
 * Reset single asset to initial db state
 */
function resetSingleMobAsset(fieldKey) {
    const keyMap = {
        'left_logo': {
            preview: 'mob_preview_left_logo',
            fallback: 'fallback_left_logo',
            badge: 'mob_badge_left_logo',
            card: 'mob_card_left_logo',
            remove: 'mob_remove_left_logo',
            btn: 'mob_btn_rm_left_logo',
            input: 'mob_left_logo',
            error: 'mob_error_left_logo'
        },
        'watermark_image': {
            preview: 'mob_preview_watermark',
            fallback: 'fallback_watermark',
            badge: 'mob_badge_watermark',
            card: 'mob_card_watermark',
            remove: 'mob_remove_watermark_image',
            btn: 'mob_btn_rm_watermark_image',
            input: 'mob_watermark_image',
            error: 'mob_error_watermark_image'
        },
        'seal_sign': {
            preview: 'mob_preview_seal_sign',
            fallback: 'fallback_seal_sign',
            badge: 'mob_badge_seal_sign',
            card: 'mob_card_seal_sign',
            remove: 'mob_remove_seal_sign',
            btn: 'mob_btn_rm_seal_sign',
            input: 'mob_seal_sign',
            error: 'mob_error_seal_sign'
        }
    };

    const cfg = keyMap[fieldKey];
    if (!cfg) return;

    const fileInput = document.getElementById(cfg.input);
    const removeInput = document.getElementById(cfg.remove);
    const previewEl = document.getElementById(cfg.preview);
    const fallbackEl = document.getElementById(cfg.fallback);
    const badgeEl = document.getElementById(cfg.badge);
    const cardEl = document.getElementById(cfg.card);
    const btn = document.getElementById(cfg.btn);
    const errorEl = document.getElementById(cfg.error);

    if (fileInput) fileInput.value = '';
    if (removeInput) removeInput.value = '0';
    if (errorEl) { errorEl.style.display = 'none'; errorEl.innerHTML = ''; }
    if (cardEl) {
        cardEl.classList.remove('marked-removed', 'has-new-file');
    }

    const hasOriginal = previewEl && previewEl.getAttribute('data-has-original') === '1';
    if (hasOriginal) {
        if (previewEl) {
            previewEl.src = previewEl.getAttribute('data-original-src');
            previewEl.style.display = 'block';
        }
        if (fallbackEl) fallbackEl.style.display = 'none';
        if (badgeEl) {
            badgeEl.className = 'mob-brand-status status-configured';
            badgeEl.innerHTML = '<i class="fa fa-check-circle"></i> Configured';
        }
        if (btn) {
            btn.className = 'mob-btn-brand-remove';
            btn.innerHTML = '<i class="fa fa-trash"></i> <span>Remove</span>';
            btn.style.display = 'inline-flex';
        }
    } else {
        if (previewEl) previewEl.style.display = 'none';
        if (fallbackEl) fallbackEl.style.display = 'flex';
        if (badgeEl) {
            badgeEl.className = 'mob-brand-status status-empty';
            badgeEl.innerHTML = '<i class="fa fa-circle-o"></i> Not Set';
        }
        if (btn) btn.style.display = 'none';
    }
}

/**
 * Reset all image previews
 */
function resetAllMobAssets() {
    ['left_logo', 'watermark_image', 'seal_sign'].forEach(function(fieldKey) {
        resetSingleMobAsset(fieldKey);
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

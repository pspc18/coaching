@php
    $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = !empty($data->state_id) ? Helper::getCity($data->state_id) : Helper::getCity();
    $getaccounts = Helper::getaccount();
    $getSession = Helper::getSession();

    $defaultLogo = env('IMAGE_SHOW_PATH') . 'default/no_image.png';
    $defaultWatermark = env('IMAGE_SHOW_PATH') . 'default/rukmani_logo.png';
    $defaultSeal = env('IMAGE_SHOW_PATH') . 'default/no_image.png';

    $currentLogo = !empty($data->left_logo) ? (env('IMAGE_SHOW_PATH') . 'setting/left_logo/' . $data->left_logo) : $defaultLogo;
    $currentWatermark = !empty($data->watermark_image) ? (env('IMAGE_SHOW_PATH') . 'setting/watermark_image/' . $data->watermark_image) : $defaultWatermark;
    $currentSeal = !empty($data->seal_sign) ? (env('IMAGE_SHOW_PATH') . 'setting/seal_sign/' . $data->seal_sign) : $defaultSeal;
@endphp

@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EDIT SETTINGS (SIGNATURE THEME)
   Aligned with User Add, Notice Management, and System Guidelines:
   - Font family: Segoe UI, -apple-system, Roboto, sans-serif
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 29px-30px inputs with #cbd5e1 border
   - Equal-height symmetrical cards & Real-time Live Branding Previews
   ========================================================================== */

.settings-edit-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.settings-edit-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner (Arise ERP Signature Dark Navy Theme) */
.settings-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 8px;
}
.settings-hero-text {
    display: flex;
    flex-direction: column;
}
.settings-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.settings-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.settings-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.settings-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #e2e8f0 !important;
    color: #001f3d !important;
    border-color: #cbd5e1 !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.22) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}

/* Row of Cards (Equal Height Flex Layout) */
.settings-cards-row {
    display: flex;
    flex-wrap: wrap;
    margin-left: -4px;
    margin-right: -4px;
    margin-bottom: 8px;
}
.settings-cards-row > [class*="col-"] {
    padding-left: 4px;
    padding-right: 4px;
    display: flex;
    flex-direction: column;
}

/* Sharp Compact Form Cards */
.settings-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    height: 100%;
    flex: 1 1 auto;
    margin-bottom: 8px;
}
.settings-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.settings-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.settings-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9.5px;
    font-weight: 700;
    padding: 0 6px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 18px;
    line-height: 18px;
    letter-spacing: .02em;
}
.settings-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
.settings-card-body {
    padding: 10px 12px;
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Compact Form Grid & Controls */
.form-group-compact {
    margin-bottom: 8px;
}
.form-group-compact:last-child {
    margin-bottom: 0;
}
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-label-compact .req-star {
    color: #ef4444;
    margin-left: 2px;
    font-weight: 700;
}
.form-label-compact .label-note {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 400;
}

.settings-edit-wrapper .form-control {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 8px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.settings-edit-wrapper textarea.form-control {
    height: auto;
}
.settings-edit-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

/* Parallel Input Groups */
.input-action-group {
    display: flex;
    align-items: stretch;
    position: relative;
    width: 100%;
    flex-wrap: nowrap !important;
}
.input-action-group .form-control {
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
}
.input-action-group .form-control:not(:first-child) {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
}
.input-action-group .form-control:not(:last-child) {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}
.input-action-prepend {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-right: none;
    color: #002C54;
    font-size: 11px;
    font-weight: 700;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-top-left-radius: 2px;
    border-bottom-left-radius: 2px;
    height: 29px;
    flex-shrink: 0;
    min-width: 32px;
}

/* Image Upload Preview Box & Remove Actions */
.brand-upload-card {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 2px;
    padding: 8px 10px;
    margin-bottom: 8px;
    transition: all .2s ease-in-out;
}
.brand-upload-card:last-child {
    margin-bottom: 0;
}
.brand-upload-card.marked-for-removal {
    border-color: #ef4444 !important;
    background: #fef2f2 !important;
}
.brand-upload-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.brand-upload-title {
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 5px;
}
.brand-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 20px;
    line-height: 20px;
    padding: 0 8px;
    font-size: 9.5px;
    font-weight: 600;
    border-radius: 2px;
    letter-spacing: 0.02em;
    vertical-align: middle;
}
.brand-preview-row {
    display: flex;
    align-items: center;
    gap: 10px;
}
.brand-preview-wrap {
    width: 90px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    overflow: hidden;
    padding: 2px;
    flex-shrink: 0;
    position: relative;
}
.brand-preview-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform .2s ease;
}
.brand-preview-img:hover {
    transform: scale(1.06);
}
.brand-upload-controls {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}
.brand-file-input {
    font-size: 10px;
    padding: 2px 4px;
    height: 26px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
}
.brand-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.btn-asset-remove {
    background: #fee2e2;
    color: #dc2626 !important;
    border: 1px solid #fca5a5;
    padding: 1px 7px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s ease-in-out;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-asset-remove:hover {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}
.btn-asset-undo {
    background: #e0f2fe;
    color: #0284c7 !important;
    border: 1px solid #7dd3fc;
    padding: 1px 7px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    cursor: pointer;
    transition: all .15s ease-in-out;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-asset-undo:hover {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
}

/* Custom Toggle Switch (iOS / Modern ERP Style) */
.erp-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    margin-bottom: 0;
}
.erp-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.erp-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 24px;
}
.erp-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #ffffff;
    transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
input:checked + .erp-slider {
    background-color: #10b981;
}
input:checked + .erp-slider:before {
    transform: translateX(20px);
}
.switch-label-text {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
}

/* Sticky Action Bar at Bottom */
.form-submit-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.btn-submit-save {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #001f3d;
    height: 30px;
    padding: 0 18px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s ease-in-out;
}
.btn-submit-save:hover {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.btn-cancel {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 30px;
    padding: 0 14px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
}
.btn-cancel:hover {
    background: #e2e8f0 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
}
</style>
@endsection

@section('content')
<div class="content-wrapper settings-edit-wrapper">
    <div class="container-fluid p-0">

        {{-- 1. Top Hero Header (Arise ERP Dark Navy Theme) --}}
        <div class="settings-hero">
            <div class="settings-hero-text">
                <span class="settings-kicker"><i class="fa fa-sliders mr-1"></i> System Administration &amp; Master Setup</span>
                <h1 class="settings-title">
                    <i class="fa fa-cogs text-info"></i> Edit Institute Settings
                </h1>
                <p class="settings-subtitle">Manage school profile, contact information, active academic session, and branding assets</p>
            </div>
            <div class="settings-hero-actions">
                <a href="{{ url('viewSetting') }}" class="dash-btn dash-btn-light" title="View Settings Directory">
                    <i class="fa fa-list mr-1"></i> View Settings
                </a>
            </div>
        </div>

        {{-- Validation & Session Messages --}}
        @if(session('message'))
            <div class="alert alert-success py-1 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
            </div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger py-1 px-3 mb-2" style="font-size: 11.5px; border-radius: 2px;">
                <strong><i class="fa fa-exclamation-triangle mr-1"></i> Please check form errors:</strong>
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Root (AJAX enabled via #form-submit-edit) --}}
        <form id="form-submit-edit" action="{{ url('editSetting', $data->id) }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row g-2 settings-cards-row">
                
                {{-- Left Column: School Profile & Academic Session (Equal Height Flex) --}}
                <div class="col-lg-7">
                    
                    {{-- Step 1: Institute Information --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h2 class="settings-card-title">
                                <span class="card-step">Step 1</span>
                                <i class="fa fa-university text-primary"></i> School Profile &amp; Contact Details
                            </h2>
                            <span class="settings-card-desc">Basic details &amp; location</span>
                        </div>
                        <div class="settings-card-body">
                            
                            {{-- Row: School Name & Branch --}}
                            <div class="row g-2">
                                <div class="{{ Session::get('role_id') == 1 ? 'col-sm-7' : 'col-sm-12' }}">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="name">
                                            <span>School / Institute Name <span class="req-star">*</span></span>
                                            <span class="label-note">Official Name</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-university"></i></span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="name" 
                                                   name="name" 
                                                   placeholder="Enter school name..." 
                                                   value="{{ old('name', $data->name) }}" 
                                                   required 
                                                   autofocus>
                                        </div>
                                    </div>
                                </div>

                                @if(Session::get('role_id') == 1)
                                <div class="col-sm-5">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="branch_id">
                                            <span>Branch Association</span>
                                            <span class="label-note">SuperAdmin</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-building-o"></i></span>
                                            <select class="form-control" id="branch_id" name="branch_id">
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
                                </div>
                                @endif
                            </div>

                            {{-- Row: Mobile & Email --}}
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="mobile">
                                            <span>Official Mobile No. <span class="req-star">*</span></span>
                                            <span class="label-note">10 Digits</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-phone"></i></span>
                                            <input type="tel" 
                                                   class="form-control" 
                                                   id="mobile" 
                                                   name="mobile" 
                                                   placeholder="e.g. 9876543210" 
                                                   value="{{ old('mobile', $data->mobile) }}" 
                                                   maxlength="10" 
                                                   minlength="10" 
                                                   onkeypress="return isNumber(event)" 
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="gmail">
                                            <span>Official Email Address <span class="req-star">*</span></span>
                                            <span class="label-note">E-Mail</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-envelope-o"></i></span>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="gmail" 
                                                   name="gmail" 
                                                   placeholder="info@school.com" 
                                                   value="{{ old('gmail', $data->gmail) }}" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Full Address --}}
                            <div class="form-group-compact">
                                <label class="form-label-compact" for="address">
                                    <span>Complete Street Address <span class="req-star">*</span></span>
                                    <span class="label-note">Campus Location</span>
                                </label>
                                <div class="input-action-group">
                                    <span class="input-action-prepend"><i class="fa fa-map-marker"></i></span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="address" 
                                           name="address" 
                                           placeholder="Enter school campus address..." 
                                           value="{{ old('address', $data->address) }}" 
                                           required>
                                </div>
                            </div>

                            {{-- Location Row: Country, State, City, Pin Code --}}
                            <div class="row g-2">
                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="country_id">
                                            <span>Country</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-globe"></i></span>
                                            <select class="form-control select2" name="country_id" id="country_id">
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
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="state_id">
                                            <span>State</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-map"></i></span>
                                            <select class="form-control select2" name="state_id" id="state_id">
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

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="city_id">
                                            <span>City</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-building"></i></span>
                                            <select class="form-control select2" name="city_id" id="city_id">
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
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="pincode">
                                            <span>Pin Code <span class="req-star">*</span></span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-map-pin"></i></span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="pincode" 
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

                        </div>
                    </div>

                    {{-- Step 2: Academic Session & App Configurations --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h2 class="settings-card-title">
                                <span class="card-step">Step 2</span>
                                <i class="fa fa-sliders text-info"></i> Academic Session &amp; System Configuration
                            </h2>
                            <span class="settings-card-desc">Session &amp; App features</span>
                        </div>
                        <div class="settings-card-body">
                            
                            <div class="row g-2">
                                {{-- Active Session --}}
                                <div class="col-sm-6">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="current_active_session_id">
                                            <span>Current Active Academic Session <span class="req-star">*</span></span>
                                            <span class="label-note">Default Session</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-calendar-check-o"></i></span>
                                            <select class="form-control" id="current_active_session_id" name="current_active_session_id" required>
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
                                </div>

                                {{-- Push Notification Toggle --}}
                                <div class="col-sm-6">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact">
                                            <span>Firebase Push Notifications</span>
                                            <span class="label-note">App Alerts</span>
                                        </label>
                                        <div class="d-flex align-items-center justify-content-between p-1 px-2 border rounded" style="height: 29px; background: #f8fafc; border-color: #cbd5e1 !important;">
                                            <span class="switch-label-text">Enable Firebase Alerts</span>
                                            <label class="erp-switch">
                                                <input type="checkbox" name="firebase_notification" id="firebaseNotification" value="1" {{ (!empty($data->firebase_notification) && $data->firebase_notification == 1) ? 'checked' : '' }}>
                                                <span class="erp-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Total, Rank, Test Date & APK Upload --}}
                            <div class="row g-2">
                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="total">
                                            <span>Score Total</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-calculator"></i></span>
                                            <input type="text" class="form-control" id="total" name="total" placeholder="Total" value="{{ old('total', $data->total) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="rank">
                                            <span>Rank Level</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-trophy"></i></span>
                                            <input type="text" class="form-control" id="rank" name="rank" placeholder="Rank" value="{{ old('rank', $data->rank) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="test_date">
                                            <span>Test Date</span>
                                        </label>
                                        <div class="input-action-group">
                                            <span class="input-action-prepend"><i class="fa fa-calendar"></i></span>
                                            <input type="date" class="form-control" id="test_date" name="test_date" value="{{ old('test_date', $data->test_date) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group-compact">
                                        <label class="form-label-compact" for="apk">
                                            <span>Upload APK</span>
                                            <input type="hidden" name="remove_apk" id="remove_apk" value="0">
                                            @if(!empty($data->apk))
                                                <span id="badge_apk" class="badge badge-success brand-status-badge">Present</span>
                                            @endif
                                        </label>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="file" class="form-control" id="apk" name="apk" accept=".apk" style="padding: 2px 4px; height: 29px; font-size: 10px;">
                                            @if(!empty($data->apk))
                                                <button type="button" class="btn btn-outline-danger btn-xs px-1" id="btn_remove_apk" onclick="toggleRemoveApk()" title="Remove APK file" style="height: 29px; line-height: 1;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- Right Column: Institute Branding Assets (Logos & Seals) (Equal Height Flex) --}}
                <div class="col-lg-5">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <h2 class="settings-card-title">
                                <span class="card-step">Step 3</span>
                                <i class="fa fa-picture-o text-warning"></i> Institute Branding &amp; Signatures
                            </h2>
                            <span class="settings-card-desc">Live previews &amp; asset management</span>
                        </div>
                        <div class="settings-card-body">
                            
                            {{-- 1. School Main Logo --}}
                            <div class="brand-upload-card" id="card_left_logo">
                                <input type="hidden" name="remove_left_logo" id="remove_left_logo" value="0">
                                
                                <div class="brand-upload-header">
                                    <span class="brand-upload-title">
                                        <i class="fa fa-bookmark text-primary"></i> School Main Logo
                                    </span>
                                    <span id="badge_left_logo" class="badge {{ !empty($data->left_logo) ? 'badge-success' : 'badge-secondary' }} brand-status-badge">
                                        {{ !empty($data->left_logo) ? 'Configured' : 'Default / None' }}
                                    </span>
                                </div>

                                <div class="brand-preview-row">
                                    <div class="brand-preview-wrap">
                                        <img id="preview_left_logo" 
                                             class="brand-preview-img" 
                                             src="{{ $currentLogo }}" 
                                             data-original-src="{{ $currentLogo }}"
                                             data-default-src="{{ $defaultLogo }}"
                                             alt="School Main Logo"
                                             onerror="this.src='{{ $defaultLogo }}'">
                                    </div>
                                    
                                    <div class="brand-upload-controls">
                                        <input type="file" 
                                               class="brand-file-input" 
                                               id="left_logo" 
                                               name="left_logo" 
                                               accept="image/png, image/jpg, image/jpeg, image/webp" 
                                               onchange="handleAssetFileChange(this, 'left_logo', 'preview_left_logo', 'error_left_logo', 'badge_left_logo', 'card_left_logo')">
                                        
                                        <div class="brand-actions-row">
                                            <span class="text-muted" style="font-size: 9px;">PNG / JPG (Max 2MB)</span>
                                            
                                            <div id="actions_left_logo">
                                                @if(!empty($data->left_logo))
                                                    <button type="button" class="btn-asset-remove" id="btn_rm_left_logo" onclick="toggleAssetRemoval('left_logo', 'preview_left_logo', 'badge_left_logo', 'card_left_logo')">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="error_left_logo" class="text-danger mt-1 font-weight-bold" style="font-size: 9.5px;"></div>
                            </div>

                            {{-- 2. Report Watermark Image --}}
                            <div class="brand-upload-card" id="card_watermark">
                                <input type="hidden" name="remove_watermark_image" id="remove_watermark_image" value="0">
                                
                                <div class="brand-upload-header">
                                    <span class="brand-upload-title">
                                        <i class="fa fa-file-image-o text-info"></i> Report Watermark Image
                                    </span>
                                    <span id="badge_watermark" class="badge {{ !empty($data->watermark_image) ? 'badge-success' : 'badge-secondary' }} brand-status-badge">
                                        {{ !empty($data->watermark_image) ? 'Configured' : 'Default / None' }}
                                    </span>
                                </div>

                                <div class="brand-preview-row">
                                    <div class="brand-preview-wrap">
                                        <img id="preview_watermark" 
                                             class="brand-preview-img" 
                                             src="{{ $currentWatermark }}" 
                                             data-original-src="{{ $currentWatermark }}"
                                             data-default-src="{{ $defaultWatermark }}"
                                             alt="Watermark Image"
                                             onerror="this.src='{{ $defaultWatermark }}'">
                                    </div>
                                    
                                    <div class="brand-upload-controls">
                                        <input type="file" 
                                               class="brand-file-input" 
                                               id="watermark_image" 
                                               name="watermark_image" 
                                               accept="image/png, image/jpg, image/jpeg, image/webp" 
                                               onchange="handleAssetFileChange(this, 'watermark_image', 'preview_watermark', 'error_watermark', 'badge_watermark', 'card_watermark')">
                                        
                                        <div class="brand-actions-row">
                                            <span class="text-muted" style="font-size: 9px;">PNG / JPG (Max 2MB)</span>
                                            
                                            <div id="actions_watermark">
                                                @if(!empty($data->watermark_image))
                                                    <button type="button" class="btn-asset-remove" id="btn_rm_watermark_image" onclick="toggleAssetRemoval('watermark_image', 'preview_watermark', 'badge_watermark', 'card_watermark')">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="error_watermark" class="text-danger mt-1 font-weight-bold" style="font-size: 9.5px;"></div>
                            </div>

                            {{-- 3. Official Seal & Signature --}}
                            <div class="brand-upload-card" id="card_seal_sign">
                                <input type="hidden" name="remove_seal_sign" id="remove_seal_sign" value="0">
                                
                                <div class="brand-upload-header">
                                    <span class="brand-upload-title">
                                        <i class="fa fa-certificate text-danger"></i> Official Seal &amp; Signature
                                    </span>
                                    <span id="badge_seal_sign" class="badge {{ !empty($data->seal_sign) ? 'badge-success' : 'badge-secondary' }} brand-status-badge">
                                        {{ !empty($data->seal_sign) ? 'Configured' : 'Default / None' }}
                                    </span>
                                </div>

                                <div class="brand-preview-row">
                                    <div class="brand-preview-wrap">
                                        <img id="preview_seal_sign" 
                                             class="brand-preview-img" 
                                             src="{{ $currentSeal }}" 
                                             data-original-src="{{ $currentSeal }}"
                                             data-default-src="{{ $defaultSeal }}"
                                             alt="Seal & Signature"
                                             onerror="this.src='{{ $defaultSeal }}'">
                                    </div>
                                    
                                    <div class="brand-upload-controls">
                                        <input type="file" 
                                               class="brand-file-input" 
                                               id="seal_sign" 
                                               name="seal_sign" 
                                               accept="image/png, image/jpg, image/jpeg, image/webp" 
                                               onchange="handleAssetFileChange(this, 'seal_sign', 'preview_seal_sign', 'error_seal_sign', 'badge_seal_sign', 'card_seal_sign')">
                                        
                                        <div class="brand-actions-row">
                                            <span class="text-muted" style="font-size: 9px;">PNG / JPG (Max 2MB)</span>
                                            
                                            <div id="actions_seal_sign">
                                                @if(!empty($data->seal_sign))
                                                    <button type="button" class="btn-asset-remove" id="btn_rm_seal_sign" onclick="toggleAssetRemoval('seal_sign', 'preview_seal_sign', 'badge_seal_sign', 'card_seal_sign')">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="error_seal_sign" class="text-danger mt-1 font-weight-bold" style="font-size: 9.5px;"></div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- 3. Bottom Sticky Action Toolbar --}}
            <div class="form-submit-bar mt-1">
                <a href="{{ url('viewSetting') }}" class="btn-cancel">
                    <i class="fa fa-arrow-left mr-1"></i> Cancel &amp; Back
                </a>
                <div class="d-flex align-items-center gap-2">
                    <button type="reset" class="btn-cancel" onclick="resetAllPreviews()">
                        <i class="fa fa-refresh mr-1"></i> Reset
                    </button>
                    <button type="submit" class="btn-submit-save btn-submit" id="btnSubmitSettings">
                        <i class="fa fa-check-circle mr-1"></i> Update Institute Settings
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>
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
 * Handle real-time file upload preview with instant validation
 */
function handleAssetFileChange(input, fieldName, previewElementId, errorElementId, badgeElementId, cardElementId) {
    const previewEl = document.getElementById(previewElementId);
    const errorEl = document.getElementById(errorElementId);
    const badgeEl = document.getElementById(badgeElementId);
    const cardEl = document.getElementById(cardElementId);
    const removeInput = document.getElementById('remove_' + fieldName);
    
    if (errorEl) errorEl.innerHTML = "";
    if (removeInput) removeInput.value = "0";
    if (cardEl) cardEl.classList.remove('marked-for-removal');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!['png', 'jpg', 'jpeg', 'webp'].includes(extension)) {
            if (errorEl) errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> Only PNG, JPG, or JPEG images allowed.";
            input.value = "";
            return;
        }

        if (file.size > MAX_IMAGE_SIZE) {
            if (errorEl) errorEl.innerHTML = "<i class='fa fa-exclamation-circle mr-1'></i> Image size (" + (file.size / 1024 / 1024).toFixed(2) + "MB) exceeds 2MB limit.";
            input.value = "";
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewEl) {
                previewEl.src = e.target.result;
            }
            if (badgeEl) {
                badgeEl.className = "badge badge-primary brand-status-badge";
                badgeEl.innerText = "New Selected";
            }
        };
        reader.readAsDataURL(file);
    } else {
        // If file input cleared, revert to original
        if (previewEl) {
            previewEl.src = previewEl.getAttribute('data-original-src');
        }
        if (badgeEl) {
            const hasOriginal = previewEl && previewEl.getAttribute('data-original-src') !== previewEl.getAttribute('data-default-src');
            badgeEl.className = hasOriginal ? "badge badge-success brand-status-badge" : "badge badge-secondary brand-status-badge";
            badgeEl.innerText = hasOriginal ? "Configured" : "Default / None";
        }
    }
}

/**
 * Toggle removal of uploaded image
 */
function toggleAssetRemoval(fieldName, previewElementId, badgeElementId, cardElementId) {
    const removeInput = document.getElementById('remove_' + fieldName);
    const fileInput = document.getElementById(fieldName);
    const previewEl = document.getElementById(previewElementId);
    const badgeEl = document.getElementById(badgeElementId);
    const cardEl = document.getElementById(cardElementId);
    const btn = document.getElementById('btn_rm_' + fieldName);

    if (!removeInput) return;

    if (removeInput.value === "0") {
        // Mark for removal
        removeInput.value = "1";
        if (fileInput) fileInput.value = "";
        if (previewEl) previewEl.src = previewEl.getAttribute('data-default-src');
        if (cardEl) cardEl.classList.add('marked-for-removal');
        if (badgeEl) {
            badgeEl.className = "badge badge-danger brand-status-badge";
            badgeEl.innerText = "Marked for Removal";
        }
        if (btn) {
            btn.className = "btn-asset-undo";
            btn.innerHTML = '<i class="fa fa-undo"></i> Undo Removal';
        }
    } else {
        // Undo removal
        removeInput.value = "0";
        if (previewEl) previewEl.src = previewEl.getAttribute('data-original-src');
        if (cardEl) cardEl.classList.remove('marked-for-removal');
        if (badgeEl) {
            badgeEl.className = "badge badge-success brand-status-badge";
            badgeEl.innerText = "Configured";
        }
        if (btn) {
            btn.className = "btn-asset-remove";
            btn.innerHTML = '<i class="fa fa-trash"></i> Remove';
        }
    }
}

/**
 * Toggle APK removal
 */
function toggleRemoveApk() {
    const removeApk = document.getElementById('remove_apk');
    const badge = document.getElementById('badge_apk');
    const btn = document.getElementById('btn_remove_apk');
    const fileInput = document.getElementById('apk');

    if (removeApk.value === "0") {
        removeApk.value = "1";
        if (fileInput) fileInput.value = "";
        if (badge) {
            badge.className = "badge badge-danger brand-status-badge";
            badge.innerText = "To Delete";
        }
        if (btn) {
            btn.className = "btn btn-outline-info btn-xs px-1";
            btn.innerHTML = '<i class="fa fa-undo"></i>';
            btn.title = "Undo APK Removal";
        }
    } else {
        removeApk.value = "0";
        if (badge) {
            badge.className = "badge badge-success brand-status-badge";
            badge.innerText = "Present";
        }
        if (btn) {
            btn.className = "btn btn-outline-danger btn-xs px-1";
            btn.innerHTML = '<i class="fa fa-trash"></i>';
            btn.title = "Remove APK file";
        }
    }
}

/**
 * Reset all image previews
 */
function resetAllPreviews() {
    ['left_logo', 'watermark', 'seal_sign'].forEach(function(key) {
        const previewEl = document.getElementById('preview_' + key);
        const cardEl = document.getElementById('card_' + key);
        const badgeEl = document.getElementById('badge_' + key);
        const removeInput = document.getElementById('remove_' + (key === 'watermark' ? 'watermark_image' : key));
        const btn = document.getElementById('btn_rm_' + (key === 'watermark' ? 'watermark_image' : key));

        if (removeInput) removeInput.value = "0";
        if (cardEl) cardEl.classList.remove('marked-for-removal');
        if (previewEl) previewEl.src = previewEl.getAttribute('data-original-src');
        if (badgeEl) {
            const hasOriginal = previewEl && previewEl.getAttribute('data-original-src') !== previewEl.getAttribute('data-default-src');
            badgeEl.className = hasOriginal ? "badge badge-success brand-status-badge" : "badge badge-secondary brand-status-badge";
            badgeEl.innerText = hasOriginal ? "Configured" : "Default / None";
        }
        if (btn) {
            btn.className = "btn-asset-remove";
            btn.innerHTML = '<i class="fa fa-trash"></i> Remove';
        }
    });
}

$(document).ready(function() {
    // Dynamic city loader on state change
    $('#state_id').on('change', function() {
        const stateId = $(this).val();
        if (stateId) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{ url('stateData') }}/" + stateId,
                type: 'GET',
                success: function(data) {
                    $('#city_id').html(data);
                }
            });
        } else {
            $('#city_id').html('<option value="">Select City</option>');
        }
    });
});
</script>
@endsection
        
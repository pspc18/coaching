@php
$getState = Helper::getState();
$roleType = Helper::roleType();
$getSetting = Helper::getSetting();
$defaultStateId = $getSetting->state_id ?? 33;
$getCity = Helper::getCity($defaultStateId);
$classes = Helper::classType();
$currentBranchId = Session::get('branch_id');
@endphp

@extends('layout.app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ADD USER (COMPACT SIGNATURE THEME)
   Aligned with Dashboard, Student List, and User View guidelines:
   - Font family: Segoe UI, -apple-system, Roboto, sans-serif
   - Base font-size: 11.5px / 12px
   - Sharp border-radius: 2px throughout
   - Dark Navy Hero: #002C54 to #0f3460
   - Compact 29px-30px inputs with #cbd5e1 border
   - Zero FOUC: Injected directly into <head>
   ========================================================================== */

.user-add-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 20px;
    min-height: calc(100vh - 56px);
}
.user-add-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner (Matching Dashboard & User View Hero) */
.user-hero {
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
    margin-bottom: 6px;
}
.user-hero-text {
    display: flex;
    flex-direction: column;
}
.user-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.user-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.user-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
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
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    border-color: rgba(255,255,255,.6);
}

/* Sharp Compact Form Cards (Matching .dash-card in Dashboard) */
.user-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
}
.user-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.user-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.user-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.user-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
.user-card-body {
    padding: 8px 10px;
}

/* Compact Form Grid & Controls */
.form-group-compact {
    margin-bottom: 6px;
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

.form-control-compact,
.user-add-wrapper .form-control {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus,
.user-add-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

.user-add-wrapper .is-invalid,
.user-add-wrapper .form-control.is-invalid {
    border-color: #ef4444 !important;
    background-image: none !important;
}
.user-add-wrapper .invalid-feedback,
.user-add-wrapper .error.invalid-feedback {
    font-size: 10px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    display: block;
}

/* Input Action Groups (Auto & Key generator buttons & Prepend addons) */
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
}
.user-add-wrapper .input-group {
    flex-wrap: nowrap !important;
}
.input-action-addon {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-left: none;
    color: #002C54;
    font-size: 10.5px;
    font-weight: 600;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    cursor: pointer;
    border-top-right-radius: 2px;
    border-bottom-right-radius: 2px;
    transition: background .12s;
    height: 29px;
}
.input-action-addon:hover {
    background: #e2e8f0;
    color: #001f3d;
}
.password-eye-btn {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    padding: 0 4px;
    z-index: 5;
}
.password-eye-btn:hover {
    color: #002C54;
}

/* Compact Select2 Styling (Sharp 2px radius) */
.user-add-wrapper .select2-container--default .select2-selection--single,
.user-add-wrapper .select2-container--default .select2-selection--multiple {
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    min-height: 29px !important;
    height: 29px;
    padding: 1px 6px;
}
.user-add-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 25px;
    font-size: 11.5px;
    color: #0f172a;
    padding-left: 2px;
}
.user-add-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 27px;
    right: 4px;
}
.user-add-wrapper .select2-container--default.select2-container--focus .select2-selection--single,
.user-add-wrapper .select2-container--default.select2-container--focus .select2-selection--multiple,
.user-add-wrapper .select2-container--default.select2-container--open .select2-selection--single,
.user-add-wrapper .select2-container--default.select2-container--open .select2-selection--multiple {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}
.user-add-wrapper .select2-container--default .select2-selection--multiple {
    height: auto;
    min-height: 29px;
    padding-bottom: 2px;
}
.user-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice,
.select2-container--default .select2-selection--multiple .select2-selection__choice,
.select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #e0f2fe !important;
    background: #e0f2fe !important;
    border: 1px solid #7dd3fc !important;
    color: #0369a1 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    border-radius: 2px !important;
    padding: 2px 7px !important;
    margin: 3px 4px 3px 0 !important;
    line-height: 1.4 !important;
    display: inline-flex !important;
    align-items: center !important;
}
.user-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
.select2-container .select2-selection--multiple .select2-selection__choice__remove {
    color: #0284c7 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    line-height: 1 !important;
    margin-right: 5px !important;
    float: none !important;
    cursor: pointer !important;
    border: none !important;
}
.user-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #dc2626 !important;
}

/* Custom Class Multi-Dropdown */
.compact-multi-box {
    position: relative;
    width: 100%;
}
.compact-multi-trigger {
    width: 100%;
    height: 29px;
    padding: 3px 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 2px;
    text-align: left;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: #0f172a;
}
.compact-multi-trigger:hover,
.compact-multi-trigger:focus {
    border-color: #002C54;
    outline: none;
}
.compact-multi-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: calc(100% - 18px);
}
.compact-multi-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    z-index: 1050;
    padding: 6px;
    max-height: 220px;
    overflow-y: auto;
}
.compact-multi-dropdown.show {
    display: block;
}
.compact-multi-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 3px 5px;
    border-radius: 2px;
    cursor: pointer;
    font-size: 11px;
    color: #1e293b;
    margin: 0;
}
.compact-multi-item:hover {
    background: #f1f5f9;
}
.compact-multi-item input[type="checkbox"] {
    width: 13px;
    height: 13px;
    accent-color: #002C54;
    cursor: pointer;
}

/* Compact Document Upload Cards */
.compact-doc-card {
    border: 1px dashed #cbd5e1;
    border-radius: 2px;
    padding: 6px 8px;
    background: #f8fafc;
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 90px;
    cursor: pointer;
    transition: all .15s;
}
.compact-doc-card:hover {
    border-color: #002C54;
    background: #f0f7ff;
}
.compact-doc-card.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #f0fdf4;
}
.compact-doc-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.compact-doc-icon {
    font-size: 16px;
    color: #002C54;
    margin-bottom: 2px;
}
.compact-doc-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.compact-doc-desc {
    font-size: 9.5px;
    color: #64748b;
    margin: 0;
}
.compact-doc-preview {
    display: none;
    margin-top: 4px;
    position: relative;
    z-index: 3;
}
.compact-doc-preview img {
    max-height: 48px;
    max-width: 100%;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
}
.compact-doc-info {
    font-size: 9.5px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 2px;
}
.compact-doc-remove {
    position: absolute;
    top: -5px;
    right: calc(50% - 30px);
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 9px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.compact-doc-error {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    margin-bottom: 0;
}

/* Bottom Action Footer Bar */
.user-footer-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.btn-compact-submit {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #002C54;
    height: 29px;
    padding: 0 18px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-submit:hover {
    background: #001f3d;
    border-color: #001f3d;
}
.btn-compact-reset {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-reset:hover {
    background: #e2e8f0;
    color: #1e293b !important;
}
.btn-compact-cancel {
    background: #ffffff;
    color: #64748b !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    text-decoration: none !important;
}
.btn-compact-cancel:hover {
    background: #f8fafc;
    color: #0f172a !important;
}

@media (max-width: 576px) {
    .user-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 8px 10px;
    }
    .dash-btn {
        flex: 1;
    }
}
</style>
@endsection

@section('content')
<div class="content-wrapper user-add-wrapper">

    {{-- 1. Top Hero Banner (Sharp Dark Navy Theme) --}}
    <div class="user-hero">
        <div class="user-hero-text">
            <span class="user-kicker"><i class="fa fa-users mr-1"></i> User Management</span>
            <h1 class="user-title"><i class="fa fa-user-plus mr-1"></i> {{ __('user.Add User') }}</h1>
            <p class="user-subtitle">Register new staff account, configure branch access and system credentials</p>
        </div>
        <div class="dash-hero-actions d-flex align-items-center gap-1">
            <a href="{{ url('viewUser') }}" class="dash-btn dash-btn-light {{ Helper::permissioncheck(6)->view ? '' : 'd-none' }}" title="View Users">
                <i class="fa fa-list"></i> {{ __('common.View') }} Staff List
            </a>
            <a href="{{ url('user_dashboard') }}" class="dash-btn dash-btn-outline" title="Dashboard">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- 2. Form Content --}}
    <form id="form-submit" action="{{ url('addUser') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Card 1: Personal & Employment Profile --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">01</span> Personal &amp; Employment Details
                </h3>
                <span class="user-card-desc">Basic information and institutional role</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    {{-- First Name --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="first_name">
                                <span>First Name <span class="req-star">*</span></span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('first_name') is-invalid @enderror"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                placeholder="First Name"
                                onkeydown="return /[a-zA-Z ]/i.test(event.key)"
                                required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Last Name --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="last_name">
                                <span>Last Name</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('last_name') is-invalid @enderror"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="Last Name"
                                onkeydown="return /[a-zA-Z ]/i.test(event.key)">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Date of Birth --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="dob">
                                <span>Date of Birth <span class="req-star">*</span></span>
                            </label>
                            <input type="date"
                                class="form-control form-control-compact @error('dob') is-invalid @enderror"
                                id="dob"
                                name="dob"
                                value="{{ old('dob') }}"
                                max="{{ date('Y-m-d', strtotime('-1 day')) }}"
                                required>
                            @error('dob')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Joining Date --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="joining_date">
                                <span>Joining Date <span class="req-star">*</span></span>
                            </label>
                            <input type="date"
                                class="form-control form-control-compact @error('joining_date') is-invalid @enderror"
                                id="joining_date"
                                name="joining_date"
                                value="{{ old('joining_date', date('Y-m-d')) }}"
                                required>
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Staff Role --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="role_id">
                                <span>Role <span class="req-star">*</span></span>
                                @if(Helper::permissioncheck(6)->add ?? true)
                                <a href="{{ url('role_add') }}" target="_blank" class="label-note text-primary" title="Add Role">
                                    <i class="fa fa-plus"></i>
                                </a>
                                @endif
                            </label>
                            <select class="form-control select2 @error('role_id') is-invalid @enderror"
                                name="role_id"
                                id="role_id"
                                required>
                                <option value="">{{ __('common.Select') }}</option>
                                @if(!empty($roleType))
                                    @php $excludedRoles = [1, 3]; @endphp
                                    @foreach($roleType as $item)
                                        @if(!in_array($item->id, $excludedRoles))
                                            <option value="{{ $item->id }}" {{ old('role_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name ?? '' }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Class Assignment --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact">
                                <span>Class Assignment</span>
                                <span class="label-note">Optional</span>
                            </label>
                            <div class="compact-multi-box" id="classMulti">
                                <button type="button" class="compact-multi-trigger" onclick="toggleDropdown()">
                                    <span id="selectedText" class="compact-multi-text">None selected</span>
                                    <i class="fa fa-caret-down text-muted" style="font-size: 10px;"></i>
                                </button>

                                <div class="compact-multi-dropdown" id="ClassDropdown">
                                    <div class="d-flex align-items-center justify-content-between pb-1 mb-1 border-bottom">
                                        <span style="font-size: 10px; font-weight: 700; color: #475569;">CLASSES</span>
                                        <label class="compact-multi-item p-0 m-0" style="font-size: 10.5px; font-weight: 600;">
                                            <input type="checkbox" id="selectAll" onchange="selectAllFees(this)"> Select All
                                        </label>
                                    </div>
                                    <div class="mb-1">
                                        <input type="text" class="form-control form-control-compact" id="classSearchInput" placeholder="Search..." onkeyup="filterClassList()" style="height: 24px; font-size: 10.5px; padding: 2px 5px;">
                                    </div>
                                    <div id="feesOptions">
                                        @php $selectedClasses = explode(',', old('class_type_id', '')); @endphp
                                        @if(!empty($classes))
                                            @foreach($classes as $type)
                                                <label class="compact-multi-item class-option-item">
                                                    <input type="checkbox"
                                                        class="class-checkbox"
                                                        value="{{ $type->id }}"
                                                        data-name="{{ $type->name }}"
                                                        {{ in_array($type->id, $selectedClasses) ? 'checked' : '' }}
                                                        onchange="updateSelectedText()">
                                                    <span>{{ $type->name }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="class_type_id" id="class_type_id" value="{{ old('class_type_id') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Contact & Location Details --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">02</span> Contact &amp; Residential Details
                </h3>
                <span class="user-card-desc">Communication contact and address</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    {{-- Mobile Number --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="mobile">
                                <span>Mobile No. <span class="req-star">*</span></span>
                                <span class="label-note">10 Digits</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('mobile') is-invalid @enderror"
                                id="mobile"
                                name="mobile"
                                value="{{ old('mobile') }}"
                                placeholder="10 Digit Mobile"
                                maxlength="10"
                                onkeypress="javascript:return isNumber(event)"
                                required>
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Email Address --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="email">
                                <span>Email Address <span class="req-star">*</span></span>
                            </label>
                            <input type="email"
                                class="form-control form-control-compact @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="name@domain.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- State --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="state_id">
                                <span>State <span class="req-star">*</span></span>
                            </label>
                            <select class="form-control select2 @error('state') is-invalid @enderror"
                                id="state_id"
                                name="state"
                                required>
                                <option value="">{{ __('common.Select') }}</option>
                                @if(!empty($getState))
                                    @foreach($getState as $state)
                                        <option value="{{ $state->id }}" {{ (old('state', $defaultStateId) == $state->id) ? 'selected' : '' }}>
                                            {{ $state->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- City --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="city_id">
                                <span>City <span class="req-star">*</span></span>
                            </label>
                            <select class="form-control select2 @error('city') is-invalid @enderror"
                                name="city"
                                id="city_id"
                                required>
                                <option value="">{{ __('common.Select') }}</option>
                                @if(!empty($getCity))
                                    @foreach($getCity as $cities)
                                        <option value="{{ $cities->id }}" {{ (old('city', $getSetting->city_id ?? '') == $cities->id) ? 'selected' : '' }}>
                                            {{ $cities->name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Residential Address --}}
                    <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="address">
                                <span>Residential Address <span class="req-star">*</span></span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('address') is-invalid @enderror"
                                id="address"
                                name="address"
                                value="{{ old('address') }}"
                                placeholder="House No., Street, Locality, Pincode"
                                required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: System Credentials & Branch Access --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">03</span> System Credentials &amp; Branch Permissions
                </h3>
                <span class="user-card-desc">Login credentials and branch access controls</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    {{-- Branch Access (Multi-Select) --}}
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="access_branch_id">
                                <span>Branch Access <span class="req-star">*</span></span>
                                <span class="label-note">Multi-select</span>
                            </label>
                            <select class="form-control select2 @error('access_branch_id') is-invalid @enderror"
                                multiple="multiple"
                                id="access_branch_id"
                                name="access_branch_id[]"
                                data-placeholder="Select accessible branches"
                                required>
                                @if(!empty($branch))
                                    @foreach($branch as $Branch)
                                        <option value="{{ $Branch->id }}"
                                            {{ (is_array(old('access_branch_id')) && in_array($Branch->id, old('access_branch_id'))) || (!old('access_branch_id') && $Branch->id == $currentBranchId) ? 'selected' : '' }}>
                                            {{ $Branch->branch_name ?? '' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('access_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Username with Auto Suggester --}}
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="userName">
                                <span>Username <span class="req-star">*</span></span>
                                <span class="label-note">Unique Identifier</span>
                            </label>
                            <div class="input-action-group">
                                <input type="text"
                                    class="form-control form-control-compact @error('userName') is-invalid @enderror"
                                    id="userName"
                                    name="userName"
                                    value="{{ old('userName') }}"
                                    placeholder="Username"
                                    required>
                                <button type="button" class="input-action-addon" onclick="suggestUsername()" title="Auto generate from name">
                                    <i class="fa fa-magic"></i> Auto
                                </button>
                            </div>
                            @error('userName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password with Eye & Generator --}}
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="password">
                                <span>Login Password <span class="req-star">*</span></span>
                                <span class="label-note">Min 4 chars</span>
                            </label>
                            <div class="input-action-group">
                                <input type="password"
                                    class="form-control form-control-compact @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    value="{{ old('password') }}"
                                    placeholder="Password"
                                    style="padding-right: 78px;"
                                    required>
                                <button type="button" class="password-eye-btn" onclick="togglePasswordVisibility()" title="Show/Hide">
                                    <i class="fa fa-eye" id="passwordEyeIcon"></i>
                                </button>
                                <button type="button" class="input-action-addon" onclick="generateSecurePassword()" title="Generate random password">
                                    <i class="fa fa-key"></i> Gen
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Payroll & Banking (Optional) --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">04</span> Payroll &amp; Banking Details
                </h3>
                <span class="user-card-desc">Salary and disbursement account details (Optional)</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    {{-- Salary / Monthly Amount --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="joining_amount">
                                <span>Salary / Month</span>
                            </label>
                            <div class="input-action-group">
                                <span class="input-action-prepend">₹</span>
                                <input type="text"
                                    class="form-control form-control-compact @error('joining_amount') is-invalid @enderror"
                                    id="joining_amount"
                                    name="joining_amount"
                                    value="{{ old('joining_amount') }}"
                                    placeholder="Monthly Salary"
                                    onkeypress="javascript:return isNumber(event)">
                            </div>
                            @error('joining_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- PAN Card --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="pan_card">
                                <span>PAN Card No.</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact text-uppercase @error('pan_card') is-invalid @enderror"
                                id="pan_card"
                                name="pan_card"
                                value="{{ old('pan_card') }}"
                                placeholder="ABCDE1234F"
                                maxlength="10">
                            @error('pan_card')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Bank Name --}}
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="bank">
                                <span>Bank Name</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('bank') is-invalid @enderror"
                                id="bank"
                                name="bank"
                                value="{{ old('bank') }}"
                                placeholder="e.g. SBI, HDFC">
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Account No --}}
                    <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="account_no">
                                <span>Bank Account No.</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact @error('account_no') is-invalid @enderror"
                                id="account_no"
                                name="account_no"
                                value="{{ old('account_no') }}"
                                placeholder="Account Number"
                                maxlength="18"
                                onkeypress="javascript:return isNumber(event)">
                            @error('account_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- IFSC Code --}}
                    <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group form-group-compact">
                            <label class="form-label-compact" for="ifsc_code">
                                <span>Bank IFSC Code</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-compact text-uppercase @error('ifsc_code') is-invalid @enderror"
                                id="ifsc_code"
                                name="ifsc_code"
                                value="{{ old('ifsc_code') }}"
                                placeholder="SBIN0001234"
                                maxlength="11">
                            @error('ifsc_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 5: Documents & Proofs --}}
        <div class="user-card">
            <div class="user-card-header">
                <h3 class="user-card-title">
                    <span class="card-step">05</span> Document &amp; Identity Uploads
                </h3>
                <span class="user-card-desc">Staff photograph and identification proofs (JPG, PNG under 2MB)</span>
            </div>
            <div class="user-card-body">
                <div class="row">
                    {{-- Profile Photo --}}
                    <div class="col-6 col-md-3 mb-2">
                        <div class="compact-doc-card" id="card_photo">
                            <input type="file"
                                class="compact-doc-input"
                                id="photo"
                                name="photo"
                                accept="image/png, image/jpg, image/jpeg"
                                onchange="handleDocUpload(this, 'card_photo', 'preview_photo', 'info_photo', 'error_photo')">
                            <i class="fa fa-camera compact-doc-icon"></i>
                            <p class="compact-doc-title">Profile Photo</p>
                            <span class="compact-doc-desc">Click or drop photo</span>
                            <div class="compact-doc-preview" id="preview_photo">
                                <img src="" alt="Preview">
                                <button type="button" class="compact-doc-remove" onclick="clearDocUpload('photo', 'card_photo', 'preview_photo', 'info_photo', 'error_photo')">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div class="compact-doc-info" id="info_photo"></div>
                            <p class="compact-doc-error" id="error_photo"></p>
                        </div>
                    </div>

                    {{-- ID Proof --}}
                    <div class="col-6 col-md-3 mb-2">
                        <div class="compact-doc-card" id="card_id_proof">
                            <input type="file"
                                class="compact-doc-input"
                                id="id_proof"
                                name="id_proof"
                                accept="image/png, image/jpg, image/jpeg"
                                onchange="handleDocUpload(this, 'card_id_proof', 'preview_id_proof', 'info_id_proof', 'error_id_proof')">
                            <i class="fa fa-id-card compact-doc-icon"></i>
                            <p class="compact-doc-title">ID Proof</p>
                            <span class="compact-doc-desc">Aadhaar / Voter ID</span>
                            <div class="compact-doc-preview" id="preview_id_proof">
                                <img src="" alt="Preview">
                                <button type="button" class="compact-doc-remove" onclick="clearDocUpload('id_proof', 'card_id_proof', 'preview_id_proof', 'info_id_proof', 'error_id_proof')">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div class="compact-doc-info" id="info_id_proof"></div>
                            <p class="compact-doc-error" id="error_id_proof"></p>
                        </div>
                    </div>

                    {{-- Qualification Proof --}}
                    <div class="col-6 col-md-3 mb-2">
                        <div class="compact-doc-card" id="card_qualification">
                            <input type="file"
                                class="compact-doc-input"
                                id="qualification_proof"
                                name="qualification_proof"
                                accept="image/png, image/jpg, image/jpeg"
                                onchange="handleDocUpload(this, 'card_qualification', 'preview_qualification', 'info_qualification', 'error_qualification')">
                            <i class="fa fa-graduation-cap compact-doc-icon"></i>
                            <p class="compact-doc-title">Qualification</p>
                            <span class="compact-doc-desc">Degree / Certificate</span>
                            <div class="compact-doc-preview" id="preview_qualification">
                                <img src="" alt="Preview">
                                <button type="button" class="compact-doc-remove" onclick="clearDocUpload('qualification_proof', 'card_qualification', 'preview_qualification', 'info_qualification', 'error_qualification')">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div class="compact-doc-info" id="info_qualification"></div>
                            <p class="compact-doc-error" id="error_qualification"></p>
                        </div>
                    </div>

                    {{-- Experience Letter --}}
                    <div class="col-6 col-md-3 mb-2">
                        <div class="compact-doc-card" id="card_experience">
                            <input type="file"
                                class="compact-doc-input"
                                id="experience_letter"
                                name="experience_letter"
                                accept="image/png, image/jpg, image/jpeg"
                                onchange="handleDocUpload(this, 'card_experience', 'preview_experience', 'info_experience', 'error_experience')">
                            <i class="fa fa-briefcase compact-doc-icon"></i>
                            <p class="compact-doc-title">Experience Letter</p>
                            <span class="compact-doc-desc">Relieving / Experience cert</span>
                            <div class="compact-doc-preview" id="preview_experience">
                                <img src="" alt="Preview">
                                <button type="button" class="compact-doc-remove" onclick="clearDocUpload('experience_letter', 'card_experience', 'preview_experience', 'info_experience', 'error_experience')">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            <div class="compact-doc-info" id="info_experience"></div>
                            <p class="compact-doc-error" id="error_experience"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Bottom Action Bar --}}
        <div class="user-footer-bar">
            <div>
                <button type="reset" class="btn-compact-reset" onclick="resetCustomMulti()">
                    <i class="fa fa-refresh"></i> Reset Fields
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('viewUser') }}" class="btn-compact-cancel">
                    Cancel
                </a>
                <button type="submit" class="btn-compact-submit btn-submit">
                    <i class="fa fa-check"></i> Submit &amp; Save User
                </button>
            </div>
        </div>

    </form>
</div>

<script>
    const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB

    /* Class Multi-Select Dropdown Controls */
    function toggleDropdown() {
        const d = document.getElementById('ClassDropdown');
        if (d) d.classList.toggle('show');
    }

    function selectAllFees(src) {
        document.querySelectorAll('.class-checkbox').forEach(cb => {
            if (cb.closest('.class-option-item').style.display !== 'none') {
                cb.checked = src.checked;
            }
        });
        updateSelectedText();
    }

    function updateSelectedText() {
        let names = [];
        let ids = [];

        document.querySelectorAll('.class-checkbox:checked').forEach(cb => {
            names.push(cb.dataset.name);
            ids.push(cb.value);
        });

        const count = names.length;
        const textElem = document.getElementById('selectedText');

        if (textElem) {
            if (count === 0) {
                textElem.innerText = 'None selected';
                textElem.style.color = '#64748b';
            } else if (count <= 2) {
                textElem.innerText = names.join(', ');
                textElem.style.color = '#002C54';
            } else {
                textElem.innerText = count + ' classes (' + names.slice(0, 2).join(', ') + '...)';
                textElem.style.color = '#002C54';
            }
        }

        const hiddenClassType = document.getElementById('class_type_id');
        if (hiddenClassType) {
            hiddenClassType.value = ids.join(',');
        }

        let total = document.querySelectorAll('.class-checkbox').length;
        let checked = document.querySelectorAll('.class-checkbox:checked').length;
        const selectAllEl = document.getElementById('selectAll');
        if (selectAllEl) {
            selectAllEl.checked = (total > 0 && total === checked);
        }
    }

    function filterClassList() {
        const queryEl = document.getElementById('classSearchInput');
        const query = queryEl ? queryEl.value.toLowerCase().trim() : '';
        document.querySelectorAll('.class-option-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(query) ? 'flex' : 'none';
        });
    }

    function resetCustomMulti() {
        setTimeout(() => {
            document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = false);
            updateSelectedText();
            if (window.jQuery && $.fn.select2) {
                $('.select2').val(null).trigger('change');
            }
            if ($('#state_id').length) {
                $('#state_id').val('{{ $defaultStateId }}').trigger('change');
            }
            if ($('#access_branch_id').length) {
                $('#access_branch_id').val(['{{ $currentBranchId }}']).trigger('change');
            }
        }, 50);
    }

    document.addEventListener('click', function(e) {
        const multi = document.getElementById('classMulti');
        const dropdown = document.getElementById('ClassDropdown');
        if (!multi || !dropdown) return;
        if (!multi.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });

    /* Document Upload Preview & Validation */
    function handleDocUpload(input, cardId, previewId, infoId, errorId) {
        const file = input.files ? input.files[0] : null;
        const card = document.getElementById(cardId);
        const previewWrap = document.getElementById(previewId);
        const info = document.getElementById(infoId);
        const error = document.getElementById(errorId);

        if (error) error.innerText = '';

        if (!file) {
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type.toLowerCase())) {
            if (error) error.innerText = 'JPG, PNG or WEBP only';
            input.value = '';
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            if (error) error.innerText = 'Max 2MB (' + (file.size / (1024 * 1024)).toFixed(1) + 'MB)';
            input.value = '';
            clearDocUpload(input.id, cardId, previewId, infoId, errorId);
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewWrap) {
                const img = previewWrap.querySelector('img');
                if (img) img.src = e.target.result;
                previewWrap.style.display = 'block';
            }
            if (card) card.classList.add('has-file');
            const sizeKb = (file.size / 1024).toFixed(0);
            if (info) info.innerText = file.name + ' (' + sizeKb + 'KB)';
        };
        reader.readAsDataURL(file);
    }

    function clearDocUpload(inputId, cardId, previewId, infoId, errorId) {
        const input = document.getElementById(inputId);
        if (input) input.value = '';
        const card = document.getElementById(cardId);
        if (card) card.classList.remove('has-file');
        const previewWrap = document.getElementById(previewId);
        if (previewWrap) {
            previewWrap.style.display = 'none';
            const img = previewWrap.querySelector('img');
            if (img) img.src = '';
        }
        const info = document.getElementById(infoId);
        if (info) info.innerText = '';
        const error = document.getElementById(errorId);
        if (error) error.innerText = '';
    }

    /* Auto Username Suggester */
    function suggestUsername() {
        const firstEl = document.getElementById('first_name');
        const firstName = firstEl ? firstEl.value.trim().toLowerCase().replace(/[^a-z0-9]/g, '') : '';
        const mobileEl = document.getElementById('mobile');
        const mobile = mobileEl ? mobileEl.value.trim() : '';

        if (!firstName) {
            if (window.toastr) {
                toastr.warning('Please enter First Name to suggest username');
            } else {
                alert('Please enter First Name to suggest username');
            }
            if (firstEl) firstEl.focus();
            return;
        }

        let suffix = '';
        if (mobile && mobile.length >= 4) {
            suffix = mobile.slice(-4);
        } else {
            suffix = Math.floor(100 + Math.random() * 900);
        }

        const username = firstName + '_' + suffix;
        const userInput = document.getElementById('userName');
        if (userInput) {
            userInput.value = username;
            userInput.classList.remove('is-invalid');
        }
        if (window.toastr) {
            toastr.info('Username suggested: ' + username);
        }
    }

    /* Secure Password Generator */
    function generateSecurePassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789@#';
        let pass = '';
        for (let i = 0; i < 8; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        const passInput = document.getElementById('password');
        if (passInput) {
            passInput.type = 'text';
            passInput.value = pass;
            passInput.classList.remove('is-invalid');
        }

        const eyeIcon = document.getElementById('passwordEyeIcon');
        if (eyeIcon) {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }

        if (window.toastr) {
            toastr.success('Generated password: ' + pass);
        }
    }

    /* Toggle Password Visibility */
    function togglePasswordVisibility() {
        const passInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEyeIcon');
        if (!passInput) return;

        if (passInput.type === 'password') {
            passInput.type = 'text';
            if (eyeIcon) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        } else {
            passInput.type = 'password';
            if (eyeIcon) {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    }

    /* Document Ready Initializations */
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                width: '100%'
            });
        }

        updateSelectedText();

        $('#state_id').on('change', function() {
            var state_id = $(this).val();
            if (!state_id) {
                $('#city_id').html('<option value="">{{ __("common.Select") }}</option>');
                return;
            }

            var baseUrl = "{{ url('/') }}";
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: baseUrl + '/stateData/' + state_id,
                type: 'GET',
                beforeSend: function() {
                    $('#city_id').prop('disabled', true);
                },
                success: function(data) {
                    $('#city_id').html(data).prop('disabled', false);
                    if ($.fn.select2) {
                        $('#city_id').trigger('change');
                    }
                },
                error: function() {
                    $('#city_id').prop('disabled', false);
                }
            });
        });

        $('#pan_card, #ifsc_code').on('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
</script>
@endsection
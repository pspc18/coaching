@php
    $getFeesGroup = Helper::getFeesGroup();
    $classType = Helper::classType();
    $stats = $stats ?? [
        'total_classes' => count($groupedByClass ?? []),
        'total_heads' => count($allFeesMasters ?? []),
        'total_amount' => 0,
        'in_use_heads' => 0,
    ];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.app')

@section('content')
<style>
/* Viewport and Split Columns System (1:1 with expenseAdd & feesGroup) */
.fg-viewport-wrapper {
    height: calc(100vh - 58px);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #f4f6f9;
}

/* 1. Dark Navy Hero Banner */
.dash-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    padding: 8px 16px;
    border-bottom: 2px solid #002C54;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    flex-shrink: 0;
}
.dash-hero-title {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.2px;
}
.dash-hero-title i {
    color: #38bdf8;
    font-size: 15px;
}
.dash-breadcrumb-inline {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: #94a3b8;
    margin: 0;
    padding: 0;
    list-style: none;
}
.dash-breadcrumb-inline a {
    color: #cbd5e1;
    text-decoration: none;
}
.dash-breadcrumb-inline a:hover {
    color: #ffffff;
}
.dash-breadcrumb-inline .sep {
    color: #64748b;
    font-size: 10px;
}
.dash-breadcrumb-inline .active {
    color: #38bdf8;
    font-weight: 600;
}

/* Stats Counter Pills in Hero */
.dash-hero-pills {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-hero-pill {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 3px;
    padding: 3px 8px;
    font-size: 11px;
    color: #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.dash-hero-pill b {
    color: #ffffff;
    font-weight: 700;
}
.dash-hero-pill.pill-active {
    background: rgba(56, 189, 248, 0.18);
    border-color: rgba(56, 189, 248, 0.4);
    color: #38bdf8;
}

/* Actions in Hero */
.dash-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.hero-btn {
    height: 28px;
    padding: 0 10px;
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
    transition: all .15s ease;
}
.hero-btn-primary {
    background: #0284c7;
    color: #ffffff;
}
.hero-btn-primary:hover {
    background: #0369a1;
    color: #ffffff;
}
.hero-btn-outline {
    background: rgba(255, 255, 255, 0.1);
    color: #f1f5f9;
    border-color: rgba(255, 255, 255, 0.2);
}
.hero-btn-outline:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

/* 2. Equal Height Columns Wrap (14px gap) */
.dash-split-wrap {
    flex: 1;
    display: flex;
    gap: 14px;
    min-height: 0;
    padding: 10px 14px;
    overflow: hidden;
}

.fg-col-form {
    width: 440px;
    display: flex;
    flex-direction: column;
    min-height: 0;
    flex-shrink: 0;
}
.fg-col-table {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
}

/* Signature Card (matching expenseAdd & feesGroup) */
.signature-card {
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    min-height: 0;
    overflow: hidden;
}
.dash-card-header {
    padding: 8px 12px;
    background: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #002C54;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    color: #475569;
    background: #e2e8f0;
    padding: 2px 7px;
    border-radius: 3px;
}

/* Scroll Containers */
.form-scroll-container {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
    min-height: 0;
}
.table-scroll-container {
    flex: 1;
    overflow-y: auto;
    position: relative;
    min-height: 0;
    background: #ffffff;
}

/* Form inputs */
.dash-label {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 4px;
    display: block;
}
.dash-select, .dash-input {
    width: 100%;
    height: 30px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.dash-select:focus, .dash-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}

/* Fee Matrix Table in Left Form */
.fee-matrix-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}
.fee-matrix-table th {
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    padding: 6px 8px;
    border-bottom: 1px solid #cbd5e1;
    position: sticky;
    top: 0;
    z-index: 2;
}
.fee-matrix-table td {
    padding: 6px 8px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.fee-matrix-table tr:hover td {
    background: #f8fafc;
}
.matrix-input-amount {
    height: 26px;
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    text-align: right;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 6px;
    width: 100%;
}
.matrix-input-date {
    height: 26px;
    font-size: 10.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 0 4px;
    width: 100%;
}

/* Custom Switch Toggle */
.custom-switch {
    position: relative;
    display: inline-block;
    width: 32px;
    height: 17px;
    margin: 0;
}
.custom-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .2s;
    border-radius: 17px;
}
.switch-slider:before {
    position: absolute;
    content: "";
    height: 13px;
    width: 13px;
    left: 2px;
    bottom: 2px;
    background-color: white;
    transition: .2s;
    border-radius: 50%;
}
input:checked + .switch-slider {
    background-color: #0284c7;
}
input:checked + .switch-slider:before {
    transform: translateX(15px);
}

/* Right Table Styles */
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead tr.header-titles-row th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    padding: 7px 10px;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
    white-space: nowrap;
    font-size: 11.5px;
}
.dash-table thead tr.excel-filter-row th {
    position: sticky;
    top: 31px;
    z-index: 9;
    background: #f1f5f9;
    padding: 4px 6px;
    border-bottom: 1px solid #cbd5e1;
}
.excel-filter-input {
    width: 100%;
    height: 24px;
    font-size: 11px;
    padding: 0 6px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
}
.excel-filter-input:focus {
    border-color: #0284c7;
}
.dash-table tbody tr {
    transition: background .1s ease;
}
.dash-table tbody tr:hover td {
    background-color: #f8fafc;
}
.dash-table td {
    padding: 7px 10px;
    border-bottom: 1px solid #e2e8f0;
    color: #334155;
    vertical-align: middle;
}

/* Fee Head Breakdown Pills in Directory */
.heads-pill-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.head-pill-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 2px 7px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    color: #334155;
    white-space: nowrap;
}
.head-pill-item b {
    color: #002C54;
}
.head-pill-amount {
    color: #0284c7;
    font-weight: 700;
}
.head-pill-date {
    font-size: 9.5px;
    color: #64748b;
    background: #f1f5f9;
    padding: 0 4px;
    border-radius: 2px;
}
.btn-delete-head {
    background: transparent;
    border: none;
    color: #ef4444;
    cursor: pointer;
    padding: 0 2px;
    font-size: 11px;
    line-height: 1;
}
.btn-delete-head:hover {
    color: #b91c1c;
}
.head-lock-badge {
    color: #d97706;
    font-size: 9.5px;
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

/* Action Buttons */
.action-btn-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-act {
    width: 26px;
    height: 26px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid transparent;
    cursor: pointer;
    font-size: 11px;
    text-decoration: none !important;
}
.btn-act-edit {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}
.btn-act-edit:hover {
    background: #0284c7;
    color: #ffffff;
}

/* Buttons */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    border-radius: 3px;
    padding: 0 10px;
    height: 28px;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none !important;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff;
}
.dash-btn-primary:hover {
    background: #001f3b;
    color: #ffffff;
}
.dash-btn-secondary {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
}
.dash-btn-secondary:hover {
    background: #e2e8f0;
}

/* Table Footer */
.table-card-footer {
    padding: 6px 12px;
    background: #f8fafc;
    border-top: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: #64748b;
    flex-shrink: 0;
}
</style>

<div class="fg-viewport-wrapper">

    {{-- 1. Dark Navy Hero Banner --}}
    <div class="dash-hero">
        <div>
            <h1 class="dash-hero-title">
                <i class="fa fa-sliders"></i> {{ __('fees.Fees Master') }}
            </h1>
            <ul class="dash-breadcrumb-inline">
                <li><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="sep">/</li>
                <li><a href="{{ url('fee_dashboard') }}">Fees Management</a></li>
                <li class="sep">/</li>
                <li class="active">{{ __('fees.Fees Master') }}</li>
            </ul>
        </div>

        {{-- Metric Counter Pills --}}
        <div class="dash-hero-pills">
            <span class="dash-hero-pill pill-active">
                <i class="fa fa-graduation-cap"></i> Classes: <b>{{ $stats['total_classes'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-cubes"></i> Heads: <b>{{ $stats['total_heads'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-inr"></i> Total: <b>₹{{ number_format($stats['total_amount'] ?? 0) }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-lock text-warning"></i> In-Use: <b>{{ $stats['in_use_heads'] ?? 0 }}</b>
            </span>
            <span class="dash-hero-pill">
                <i class="fa fa-calendar-check-o"></i> Session: <b>{{ $currentSessionName }}</b>
            </span>
        </div>

        {{-- Hero Shortcuts & Modal Triggers --}}
        <div class="dash-hero-actions">
            <button type="button" class="hero-btn hero-btn-primary" data-bs-toggle="modal" data-bs-target="#students_list_modal">
                <i class="fa fa-user-plus mr-1"></i> Student Fee Assign
            </button>
            <button type="button" class="hero-btn hero-btn-outline" id="fees_modification_btn" data-bs-toggle="modal" data-bs-target="#fees_modification">
                <i class="fa fa-pencil-square-o mr-1"></i> Modify Student Fees
            </button>
            <a href="{{ url('feesGroup') }}" class="hero-btn hero-btn-outline">
                <i class="fa fa-folder-open mr-1"></i> Fees Group
            </a>
            <a href="{{ url('feesCollectAdd') }}" class="hero-btn hero-btn-outline">
                <i class="fa fa-inr mr-1"></i> Collect Fees
            </a>
        </div>
    </div>

    {{-- 2. Equal-Height Split Workspace --}}
    <div class="dash-split-wrap">

        {{-- Left Form Column (Assign Fee Structure to Class) --}}
        <div class="fg-col-form">
            <div class="signature-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-plus-circle text-primary"></i> Assign Fee Structure to Class
                    </h3>
                </div>

                <div class="form-scroll-container">
                    <form id="quickForm" action="{{ url('feesMasterAdd') }}" method="post">
                        @csrf

                        {{-- Select Class --}}
                        <div class="form-group mb-3">
                            <label class="dash-label">{{ __('common.Class') }} <span class="text-danger">*</span></label>
                            <select class="dash-select @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" required>
                                <option value="">-- Select Class --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('class_type_id')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Fee Group Matrix --}}
                        <div class="form-group mb-2">
                            <label class="dash-label d-flex justify-content-between align-items-center">
                                <span>{{ __('fees.Fees Group') }} Matrix</span>
                                <span class="text-muted" style="font-size:10px; font-weight:normal;">Check heads to assign</span>
                            </label>

                            <div style="border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                                <table class="fee-matrix-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 28px; text-align: center;">
                                                <input type="checkbox" id="select_group" checked style="cursor:pointer;">
                                            </th>
                                            <th>Fee Head</th>
                                            <th style="width: 90px; text-align: right;">Amount (₹)</th>
                                            <th style="width: 105px; text-align: center;">Due Date</th>
                                            <th style="width: 50px; text-align: center;" title="Allow student-level edit on admission">Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($getFeesGroup))
                                            @foreach ($getFeesGroup as $gType)
                                                @php
                                                    $isRefundable = strtolower($gType->fees_refund ?? '') === 'yes';
                                                @endphp
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <input type="checkbox" class="group_checkbox" id="fees_group_{{ $gType->id }}" name="fees_group_id[]" value="{{ $gType->id }}" checked style="cursor:pointer;">
                                                    </td>
                                                    <td>
                                                        <label for="fees_group_{{ $gType->id }}" style="cursor:pointer; margin:0; font-weight:700; color:#1e293b;">
                                                            {{ $gType->name ?? '' }}
                                                        </label>
                                                        @if($isRefundable)
                                                            <span class="badge badge-success ml-1" style="font-size:8.5px; padding:1px 4px;">Refund</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input class="matrix-input-amount" type="text" name="amount[{{ $gType->id }}]" placeholder="0" value="0" onkeypress="javascript:return isNumber(event)">
                                                    </td>
                                                    <td>
                                                        <input class="matrix-input-date" type="date" name="installment_due_date[{{ $gType->id }}]">
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <label class="custom-switch">
                                                            <input type="checkbox" class="matrix-editable-cb" onchange="$(this).closest('td').find('.editable-val').val(this.checked ? 1 : 0);">
                                                            <span class="switch-slider"></span>
                                                        </label>
                                                        <input type="hidden" name="editable_value[{{ $gType->id }}]" class="editable-val" value="0">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center py-3 text-muted" style="font-size:11px;">
                                                    No Fee Groups found. <a href="{{ url('feesGroup') }}">Create Groups</a> first.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2 mt-3 border-top">
                            <button type="submit" class="dash-btn dash-btn-primary w-100" style="height:32px; font-size:12px;">
                                <i class="fa fa-check-circle mr-1"></i> Save Fee Structure
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Table Column (Configured Class Directory) --}}
        <div class="fg-col-table">
            <div class="signature-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-list text-info"></i> Class Fee Master Directory
                    </h3>
                    <span class="badge-total-records">
                        Configured Classes: <b>{{ count($groupedByClass ?? []) }}</b>
                    </span>
                </div>

                {{-- Scrollable Table Container with Sticky Headers --}}
                <div class="table-scroll-container">
                    <table class="dash-table" id="feesMasterTable">
                        <thead>
                            {{-- Row 1: Header Titles --}}
                            <tr class="header-titles-row">
                                <th style="width: 45px; text-align: center;">#</th>
                                <th style="width: 140px;">{{ __('common.Class') }}</th>
                                <th>Assigned Fee Heads & Amounts</th>
                                <th style="width: 110px; text-align: right;">Total Fee</th>
                                <th style="width: 70px; text-align: center;">{{ __('messages.Action') }}</th>
                            </tr>

                            {{-- Row 2: In-Column Sticky Excel Filter --}}
                            <tr class="excel-filter-row">
                                <th></th>
                                <th>
                                    <input type="text" id="filter_class" class="excel-filter-input" placeholder="Search class...">
                                </th>
                                <th colspan="2" style="font-size: 10px; color: #64748b; font-weight: normal; vertical-align: middle;">
                                    <i class="fa fa-info-circle text-primary mr-1"></i> Instant Class Filter
                                </th>
                                <th style="text-align: center;">
                                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn_clear_filters" title="Reset Filter" style="height: 22px; padding: 0 6px;">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($groupedByClass as $classTypeId => $classFeeMasters)
                                @php
                                    $className = $classFeeMasters->first()->ClassTypes->name ?? 'Class #' . $classTypeId;
                                    $classNameLower = strtolower($className);
                                    $totalClassFee = $classFeeMasters->sum('amount');
                                    $headsCount = $classFeeMasters->count();
                                @endphp
                                <tr class="fm-class-row" data-class="{{ $classNameLower }}">
                                    <td style="text-align:center; font-weight:700; color:#64748b;">{{ $loop->iteration }}</td>
                                    <td>
                                        <div style="font-weight: 800; color:#002C54; font-size:12px;">{{ $className }}</div>
                                        <span class="badge badge-light border" style="font-size:9.5px; color:#475569;">
                                            {{ $headsCount }} {{ Str::plural('Fee Head', $headsCount) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="heads-pill-wrap">
                                            @foreach($classFeeMasters as $fm)
                                                @php
                                                    $isLocked = isset($usedDetailGroups[$fm->fees_group_id]) || isset($usedAssignPairs[$classTypeId . '_' . $fm->fees_group_id]);
                                                    $headName = $fm->feesGroup->name ?? 'Group #' . $fm->fees_group_id;
                                                    $dueFormatted = !empty($fm->installment_due_date) ? date('d M Y', strtotime($fm->installment_due_date)) : '';
                                                @endphp
                                                <div class="head-pill-item">
                                                    <span><b>{{ $headName }}</b>: <span class="head-pill-amount">₹{{ number_format($fm->amount) }}</span></span>
                                                    @if(!empty($dueFormatted))
                                                        <span class="head-pill-date" title="Due Date"><i class="fa fa-calendar-o"></i> {{ $dueFormatted }}</span>
                                                    @endif
                                                    @if($isLocked)
                                                        <span class="head-lock-badge" title="Active student records linked. Locked from deletion.">
                                                            <i class="fa fa-lock"></i>
                                                        </span>
                                                    @else
                                                        <a href="javascript:void(0);" 
                                                           data-groupname="{{ $fm->id }}" 
                                                           data-groupname-label="{{ $headName }} ({{ $className }})"
                                                           data-bs-toggle="modal" 
                                                           data-bs-target="#Modal_id" 
                                                           class="btn-delete-head deleteData {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                                           title="Delete this fee head from {{ $className }}">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <span style="font-size:12.5px; font-weight:800; color:#0284c7;">
                                            ₹{{ number_format($totalClassFee) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div class="action-btn-wrap">
                                            <a href="{{ url('feesMasterEdit/' . $classTypeId) }}" 
                                               class="btn-act btn-act-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                                               title="Edit Class Fee Structure">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="fmInitialEmptyRow">
                                    <td colspan="5" class="text-center py-5">
                                        <div style="padding: 30px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                            <div style="width:54px; height:54px; border-radius:50%; background:#e0f2fe; border:1.5px dashed #7dd3fc; display:flex; align-items:center; justify-content:center; font-size:22px; color:#0284c7; margin-bottom:10px;">
                                                <i class="fa fa-folder-open-o"></i>
                                            </div>
                                            <div style="font-size:13.5px; font-weight:700; color:#002C54; margin-bottom:3px;">No Class Fee Structures Configured Yet</div>
                                            <div style="font-size:11px; color:#64748b;">Use the form on the left to assign fees to your first class.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- Empty State on In-Column Excel Filter --}}
                            <tr id="fgEmptyFilterRow" class="d-none">
                                <td colspan="5" class="text-center py-5">
                                    <div style="padding: 24px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                        <div style="width:52px; height:52px; border-radius:50%; background:#f8fafc; border:1.5px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; font-size:22px; color:#64748b; margin-bottom:10px;">
                                            <i class="fa fa-search"></i>
                                        </div>
                                        <div style="font-size:13px; font-weight:700; color:#002C54; margin-bottom:3px;">No Matching Classes Found</div>
                                        <div style="font-size:11px; color:#64748b; margin-bottom:10px;">No class fee records match your search filter.</div>
                                        <button type="button" class="dash-btn dash-btn-secondary" id="btn_clear_empty_filters" style="height:28px; font-size:11px; padding:0 12px;">
                                            <i class="fa fa-refresh mr-1"></i> Clear Filters
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Table Card Footer --}}
                <div class="table-card-footer">
                    <span><i class="fa fa-shield text-warning mr-1"></i> <b>Safety Guard:</b> Active fee heads assigned to students or with payments are locked from deletion.</span>
                    <span class="font-weight-bold text-dark">Showing {{ count($groupedByClass ?? []) }} classes</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    function applyClassFilter() {
        var q = $('#filter_class').val().toLowerCase().trim();
        var matchCount = 0;

        $('.fm-class-row').each(function() {
            var className = $(this).data('class') || '';
            if (!q || className.indexOf(q) !== -1) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#fgEmptyFilterRow').removeClass('d-none');
        } else {
            $('#fgEmptyFilterRow').addClass('d-none');
        }
    }

    $('#filter_class').on('keyup input', applyClassFilter);

    $('#btn_clear_filters, #btn_clear_empty_filters').on('click', function() {
        $('#filter_class').val('');
        $('.fm-class-row').show();
        $('#fgEmptyFilterRow').addClass('d-none');
    });

    // Select All Checkbox
    $('#select_group').on('change', function() {
        $('.group_checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.group_checkbox').on('change', function() {
        if ($('.group_checkbox:checked').length === $('.group_checkbox').length) {
            $('#select_group').prop('checked', true);
        } else {
            $('#select_group').prop('checked', false);
        }
    });

    // Delete single head handler
    $(document).on('click', '.deleteData', function() {
        var delete_id = $(this).data('groupname');
        var label = $(this).data('groupname-label') || '';
        $('#delete_id').val(delete_id);
        if (label) {
            $('#delete_head_label').text('"' + label + '"');
        }
    });
});
</script>



  <!-- Modal -->
    <div class="modal fade" id="fees_modification" tabindex="-1" aria-labelledby="feesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feesModalLabel">Modify Student Fees</h5>
                    <button type="button" class="btn btn-outline-danger btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <form id="feesForm" class="mb-3">
                        <div class="row">
                                                 <!-- <div class="col-md-2">
									<div class="form-group">
										<label>Admission Type(Non RTE)</label>
										<select class="form-control invalid" id="admission_type_id_modify" name="admission_type_id_modify">
										
											<option value="1">Yes</option>
											<option value="2">No</option>
										</select>
									   
									</div>
								</div> -->
                            <div class="col-md-2">
                                <label for="admissionNo" class="form-label">Admission No</label>
                                <input type="text" class="form-control" id="admission_modification" placeholder="Enter Admission No">
                            </div>
                            <div class="col-md-2">
                                <label for="class" class="form-label">{{ __('common.Class') }}</label>
                                <select class="form-control" id="class_modification" name="class_type_id">
                                    <option value="">{{ __('messages.Select') }}</option>
                                    @if(!empty($classType))
                                    @foreach($classType as $type)
                                    <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="search" class="text-white form-label">Search</label>
                                <input type="button" class="btn btn-primary form-control" value="Search" id="searchButton"/>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class='row'> 
                    
                    <div class='col-md-12 text-danger mt-3' style='font-size:12px;line-height:2px;'>
                        <p>1. Verify if there are any payments under the current fee head. If payments exist, modifications are not allowed.</p>
                        <p>2. There is no need to manually save changes. The system will automatically update the fees when the input field loses focus.</p>
                        </div> 
                        </div>
                          <hr>
                    <div class='row'> 
                    
                    
                      
                    <div class='col-md-12'> 
                            <div  style='overflow: scroll;height:300px'>
                    <table class="table table-bordered  text-center padding_table" >
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Mobile</th>
                                <th>Fees Assign Detail</th>
                                <th style="width:70px;">Discount</th>
                                <th>Due Date</th>
                                <th style="width:80px;">Fine %</th>
                                <th style="width:80px;">Fees Refund </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_modification"></tbody>
                    </table>
                    </div>
                    
                    </div>
                    <div class='col-md-5' id="feesInputsContainer"> 
                    
                    
                    </div>
                    </div>
               
                 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
  <style>
      @media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .padding_table th, .padding_table td {
        padding: 8px 5px;
        font-size: 13px;
    }
    
    .padding_table input[type="text"],
    .padding_table input[type="date"],
    .padding_table select {
        font-size: 12px;
        padding: 5px;
    }
    
    .padding_table th:first-child,
    .padding_table td:first-child {
        min-width: 30px;
    }
    
    .padding_table th:nth-child(2),
    .padding_table td:nth-child(2) {
        min-width: 80px;
    }
    
    .padding_table th:nth-child(3),
    .padding_table td:nth-child(3) {
        min-width: 100px;
    }
    
    .padding_table th:nth-child(4),
    .padding_table td:nth-child(4) {
        min-width: 80px;
    }
    
    .padding_table th:nth-child(5),
    .padding_table td:nth-child(5) {
        min-width: 100px;
    }
    
    .padding_table th:nth-child(6),
    .padding_table td:nth-child(6) {
        min-width: 70px;
    }
}
  </style>
  

<style> 
    .padding_table thead tr{
    background: #002c54;
    color:white;
}
    
.padding_table th, .padding_table td{
     padding:5px;
     font-size:14px;
     vertical-align: inherit;
}


</style>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

 <script>
        $(document).ready(function() {
            $('#searchButton').click(function() {
                var admissionNo = $('#admission_modification').val();
                var classTypeId = $('#class_modification').val();
                var admission_type_id_modify = $('#admission_type_id_modify').val();

                $.ajax({
                    headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
                    url: '/feesModification',  // Replace with your actual route
                    method: 'Post',
                    data: {
                        admissionNo: admissionNo,
                        class_type_id: classTypeId,
                        admission_type_id_modify:admission_type_id_modify
                    },
                    success: function(response) {
                        $('#tbody_modification').html(response);
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr);
                    }
                }); 
            });
            
            $('#tbody_modification').on('click', '.delete_assigned', function() {
         
                var fees_assign_detail_id = $(this).data('detail_id');
                
                var currentTd = $(this);

                $.ajax({
                    headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
                    url: '/deleteAssignedFees',  // Replace with your actual route
                    method: 'POST',
                    data: {
                        fees_assign_detail_id: fees_assign_detail_id,
                      
                    },
                    success: function(response) {
                     currentTd.closest('td').remove();
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr);
                    }
                }); 
            });
   
            
            
         $('#tbody_modification').on('focusout', '.fees_assign_detail', function() {
            var fees_assign_detail_id = $(this).data('detail_id');
            var value = $(this).val();
            var old_value = $(this).data('old_value');
            var field = $(this).attr('name');
       // alert(field);
            var currentTd = $(this);
            var parentTr = currentTd.closest('tr');

            function compareValues(value1, value2) {
                // Check if both values are valid dates
                const date1 = Date.parse(value1);
                const date2 = Date.parse(value2);
                
                
                if(value2 == 0){
                     return true;
                }else if (!isNaN(date1) && !isNaN(date2)) {
                    // Both values are valid dates
                    return date1 !== date2;
                }
            
                // Check if both values are numbers (integer or float)
                const num1 = parseFloat(value1);
                const num2 = parseFloat(value2);
                
                if (!isNaN(num1) && !isNaN(num2)) {
                    // Both values are numbers
                    return num1 !== num2;
                }
            
                // If they are not both dates or both numbers, they are not equal
                return false;
            }
            
           
if(field == 'fees_group_amount'){
    var pay_fees = $(this).data('pay_fees');

    if(value < pay_fees){
        toastr.error('The student has already paid an amount of Rs '+pay_fees);
        $(this).val(old_value);
       return
    }

}

    if (compareValues(value, old_value) ) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/updateAssignedFees',  // Replace with your actual route
            method: 'POST',
            data: {
                fees_assign_detail_id: fees_assign_detail_id,
                value: value,
                field: field
            },
            success: function(response) {
                toastr.success('Fees Updated Successfully');
                currentTd.data('old_value', value);
                currentTd.val(value);
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
});
            
        
            
            
        });

  $('#tbody_modification').on('click', '.add-btn', function() {
                var row = $(this).closest('tr');
                var name = row.find('td:nth-child(1)').text();
                var mobile = row.find('td:nth-child(2)').text();
                var feesDetail = `
                    <div class="fees-detail">
                        <h5>Fees for ${name} (${mobile})</h5>
                        <div class="mb-3">
                            <label for="feesName" class="form-label">Fees Name</label>
                            <input type="text" class="form-control" name="feesName[]" placeholder="Enter Fees Name">
                        </div>
                        <div class="mb-3">
                            <label for="feesAmount" class="form-label">Fees Amount</label>
                            <input type="text" class="form-control" name="feesAmount[]" placeholder="Enter Fees Amount">
                        </div>
                    </div>
                `;
                $('#feesInputsContainer').append(feesDetail);
            });
      
        function submitFeesModification() {
            // Handle the save changes button click event here
            console.log('Save changes clicked');
        }
    </script>

<script>

  
  $(document).ready(function(){
     $('.filterData').click(function(){
        var classId = $('#classTypeID').find(':selected').val();
        var elements = $('.all_data');
        var count = elements.length;
        
        for (var i = 0; i < count; i++) {
            if(classId != ""){
            var class_type_id = elements.eq(i).data('class');
            if(class_type_id == classId){
                elements.eq(i).show();
            }else{
                elements.eq(i).hide();
            }
            }else{
                elements.eq(i).show();
            }
        }
     }); 
  });
</script>

<script>
$(document).ready(function(){
   $(document).on('click','.change_box',function(){
       var $row = $(this).closest('tr'); // Get the current row
       var amountField = $row.find('.amount_0'); // Find the amount field in the current row
       if($(this).prop('checked')){
           $(this).siblings('input').val(1);
           amountField.val(0);
           amountField.attr('type','hidden');
       }else{
           $(this).siblings('input').val(0);
           amountField.val(0);
           amountField.attr('type','text');
       }
   }); 
});

/*$(document).ready(function(){
   $(document).on('click','.change_box',function(){
       var amount_id = $(this).data('amount_id');
       if($(this).prop('checked')){
           $(this).siblings('input').val(1);
           $('.amount_' + amount_id).val(0);
           $('.amount_' + amount_id).attr('type','hidden');
       }else{
           $(this).siblings('input').val(0);
           $('.amount_' + amount_id).val(0);
           $('.amount_' + amount_id).attr('type','text');
       }
   }); 
});*/
</script>

<script>
    $('.deleteData').click(function() {
    var delete_id = $(this).data('groupname');

    $('#delete_id').val(delete_id);
  });
</script>
<!-- The Delete Modal -->
<div class="modal fade" id="Modal_id" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content" style="border:none; border-radius:6px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.15);">

      <div class="modal-header" style="background:#dc2626; color:#ffffff; padding:10px 14px;">
        <h5 class="modal-title" style="font-size:13px; font-weight:700;"><i class="fa fa-trash-o mr-1"></i> {{ __('messages.Delete Confirmation') }}</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close" style="filter:brightness(0) invert(1); opacity:0.8;"></button>
      </div>

      <form action="{{ url('feesMasterDelete') }}" method="post">
        @csrf
        <div class="modal-body text-center" style="padding:20px 16px;">
          <input type="hidden" id="delete_id" name="delete_id">
          <div style="width:48px; height:48px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:22px; margin:0 auto 12px auto;">
              <i class="fa fa-trash"></i>
          </div>
          <p style="font-size:12px; color:#64748b; margin-bottom:4px;">Are you sure you want to delete this fee head:</p>
          <h6 style="font-size:13.5px; font-weight:800; color:#002C54; margin:0;" id="delete_head_label"></h6>
        </div>
        <div class="modal-footer justify-content-center" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 12px;">
          <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal" style="font-size:11px; font-weight:700; padding:4px 12px;">{{ __('messages.Close') }}</button>
          <button type="submit" class="btn btn-sm btn-danger" style="font-size:11px; font-weight:700; padding:4px 16px;"><i class="fa fa-trash mr-1"></i> {{ __('messages.Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>





<div class="modal fade" id="students_list_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        
            <!-- Modal Header -->
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Assign Installment Payment to Students for the selected {{ __('common.Class') }}</h4>
            </div>
            <form id="assignFeesMultiple" action="{{ url('assignFeesMultipleStudents') }}" method="POST">
            @csrf    
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                                                 <!-- <div class="col-md-2">
									<div class="form-group">
										<label>Admission Type(Non RTE)</label>
										<select class="form-control invalid" id="admission_type_id" name="admission_type_id">
										
											<option value="1">Yes</option>
											<option value="2">No</option>
										</select>
									   
									</div>
								</div> -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('common.Class') }}</label>
                                <select class="form-control" id="bulk_class_type_id" name="class_type_id">
                                  <option value="">{{ __('messages.Select') }}</option>
                                  @if(!empty($classType))
                                  @foreach($classType as $type)
                                  <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                                  @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Admission No</label>
                                <input type="text" class="form-control" placeholder="Admission No" name="admissionNo" id="bulk_admission_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fees Master</label>
                                <select class="form-control select2" multiple id="bulk_fees_master_ids" name="fees_master_ids[]" required>
                                  <!--<option value="">{{ __('messages.Select') }}</option>-->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                        
                <div class="col-md-12 overflow_scroll">
                    <table class="table table-bordered  text-center padding_table" >
                        <thead>
                            <tr>
                                <th><input type='checkbox' id="all_students" /></th>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Mobile</th>
                                <th>Father</th>
                                <th>Assigned Fees</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody_students_list"></tbody>
                    </table>
                </div>
                
                <div class="col-md-12">
                    <div class="note_text note">
                        <p>1. If any selected fee head is already assigned to a student, the system will skip that head and assign the remaining heads.</p>
                        <p>2. To modify a student's assigned fees, go to the fee modification area.</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>

<style>
.note{
    background-color: #9e9e9e5e;
    border-radius: 4px;
    padding: 10px;
}

.note p{
    color: red;
    font-size:12px;
    font-weight: 400;
    margin-bottom:0px;
}
.overflow_scroll{
    height:300px;
    overflow-y:scroll;
}
</style>


<script>
    function getStudents(class_type_id,bulk_admission_no,admission_type_id){
         $('#tbody_students_list').html('');
        $.ajax({
            headers: {
			    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		    },
            url: '/getStudentsList',  // Replace with your actual route
            method: 'Post',
            data: {
                admissionNo:bulk_admission_no,
                class_type_id:class_type_id,
                admission_type_id:admission_type_id
            },
            success: function(response) {
                $('#tbody_students_list').html(response);
                $('#all_students').prop('checked',false);
                $('#bulk_class_type_id').val(class_type_id);
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
    
    function getMasterData(class_type_id){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/getMasterData',  // Replace with your actual route
            method: 'POST',
            data: {
                class_type_id: class_type_id
            },
            success: function(response) {
                var masterData = [];
                if(response.length != 0){
                    $('#bulk_fees_master_ids').html("");
                        
                    for(var i = 0; i < response.length; i++){
                        var code = '<option value="'+ response[i].id +'">'+ response[i].fees_group_name +'</option>'; // Corrected quote
                        masterData.push(code); 
                    }
                    if(masterData.length > 0){
                     $('#bulk_fees_master_ids').html(masterData.join(''));   
                    }
                    
                     
                }
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
        
        
const installmentNamesToCheck = @json($feesGroupInstallmentsList);

document.getElementById('previewBtn').addEventListener('click', function() {
    let hasError = false;

    // Get values from inputs
    const totalAmount = parseInt(document.getElementById('totalAmount').value);
    // const installmentFrequency = parseInt(document.getElementById('frequency').value);
    const installmentFrequency = 1;
    const classTypeId = document.getElementById('installment_class_type_id').value;
    let dueDay = parseInt(document.getElementById('due_date_on_every').value);
    
    const numInstallments = $('.select_checkbox:checkbox:checked').length;
    const installmentNamesToCheckLength = installmentNamesToCheck.length;
    const installmentAmount = Math.floor(totalAmount / numInstallments);
    const remainder = totalAmount % numInstallments;
    const fullMonthList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    if(installmentNamesToCheckLength === 0){
        toastr.error("Please Create Installment First !!");
        return;
    }

    if (classTypeId === "") {
        toastr.error("Please Select Class");
        $('#installment_class_type_id').focus();
        return;
    }
    
    if (isNaN(totalAmount)) {
        toastr.error("Please Enter Amount");
        $('#totalAmount').focus();
        return;
    }
    
    $('#installments_data').show();

    const errorNotification = document.getElementById('errorNotification');
    errorNotification.style.display = 'none';
    errorNotification.innerHTML = '';
    
    const checkedCheckboxes = $('.select_checkbox:checkbox:checked');
    
    $('.amountInstallment').val("");
    $('.installmentMonth').val("Jan");
    $('.installmentDueDate').val("");
    $('.installmentFine').val(0);
    
    for (let i = 0; i < numInstallments; i++) {
        let amount = installmentAmount;
        if (i < remainder) {
            amount += 1;
        }

        let selectedMonthIndex = (i * installmentFrequency) % 12;
        let month = fullMonthList[selectedMonthIndex];

        let year = new Date().getFullYear();
        let monthIndex = selectedMonthIndex + 1;

        let nextMonth = new Date(year, monthIndex, 1);
        nextMonth.setDate(0);
        let lastDayOfMonth = nextMonth.getDate();

        if (dueDay > lastDayOfMonth) {
            dueDay = lastDayOfMonth;
        }

        let monthStr = monthIndex.toString().padStart(2, '0');
        let dueDate = `${year}-${monthStr}-${dueDay.toString().padStart(2, '0')}`;

        let installmentName = `Installment ${i + 1}`;
        let rowClass = '';

        if (installmentNamesToCheck.includes(installmentName)) {
            rowClass = 'class="bg-danger"';
            hasError = true;
        }
        
        var row_id = checkedCheckboxes.eq(i).val();
        
        $('#installment_amount_' + row_id).val(amount);
        $('#installment_due_date_' + row_id).val(dueDate);
        $('#installment_month_' + row_id).val(month);
    }

    if (hasError) {
        errorNotification.innerHTML = `Note: One or more installment names match the restricted list. Please review the highlighted rows.<br>
        Caution: Proceeding will override the existing data with the new entries.`;
        errorNotification.style.display = 'block';
    }
});

</script>

<script>
$(document).ready(function(){
    var formSubmit = true;
    
    $('#students_list_modal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#select_all').click(function(){
        if($(this).prop('checked')){
            $('.select_checkbox').prop('checked',true);
            $('#installment_submit_button').show();
        } else {
            $('.select_checkbox').prop('checked',false);
            $('#installment_submit_button').hide();
        }
        
        $('#previewBtn').click();
    });
    
    $(document).on('click', '.select_checkbox', function(){
        $('#previewBtn').click();
        var total_checkbox_count = $('.select_checkbox').length;
        var total_checked_checkbox_count = $('.select_checkbox:checkbox:checked').length;
        if(total_checkbox_count === total_checked_checkbox_count){
            $('#select_all').prop('checked',true);
            $('#installment_submit_button').show();
        } else {
            $('#select_all').prop('checked',false);
            $('#installment_submit_button').hide();
        }
        
        if(total_checked_checkbox_count === 0){
            $('#installment_submit_button').hide();
        } else {
            $('#installment_submit_button').show();
        }
    });
    
    $('#installment_submit_button').click(function(){
        formSubmit = true;
        $('.amountInstallment, .installmentName, .installmentId, .installmentMonth, .installmentDueDate, .installmentFine').removeAttr('name');
        var total_checked_checkbox_count = $('.select_checkbox:checkbox:checked').length;
        const checkedCheckboxes = $('.select_checkbox:checkbox:checked');
        
        const checkboxes = document.querySelectorAll('.select_checkbox');

        let checkedValues = [];
        
        checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
            const tr = checkbox.closest('tr');
            const installElements = tr.querySelectorAll('.install');
            Array.from(installElements).forEach(element => {
              checkedValues.push(element.value);
            });
        }
        });
        
        var installMentClass = $('#installment_class_type_id').val();
        
        var masterFeesArray = @json($masterFeesArray);
        var installmentArray = masterFeesArray[installMentClass];
          
        var matchedValues = checkedValues.filter(function(value) {
            return $.inArray(value, installmentArray) !== -1;
        });
        
        if(matchedValues != ""){
            formSubmit = false;
        }
        
        for(var l = 0; l < total_checked_checkbox_count; l++){
            var row_id = checkedCheckboxes.eq(l).val();
            
            $('#installment_amount_' + row_id).attr('name', 'installment_value[]');
            $('#installment_due_date_' + row_id).attr('name', 'installment_due_date[]');
            $('#installment_month_' + row_id).attr('name', 'installment_month[]');
            $('#installment_name_' + row_id).attr('name', 'installment_name[]');
            $('#installment_id_' + row_id).attr('name', 'installment_id[]');
            $('#installment_fine_' + row_id).attr('name', 'installment_fine[]');
        }
        
        var formData = $('#installment_form').serialize();
        
        if(formSubmit){
        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/createFeesInstallmentClassWise',  // Replace with your actual route
            method: 'POST',
            data: JSON.stringify(formData),
            success: function(response) {
                toastr.success("Fees Master Created Successfully");
                window.location.reload();
                // if(response.entry == true){
                //     toastr.success("Fees Master Created Successfully");
                //     var classTypeId = response.class_type_id;
                //     getStudents(classTypeId, null);
                //     setTimeout(function() {
                //          $('#students_list_modal').modal('show');
                //     }, 800);
                // }else{
                //     toastr.success("Fees Master Created Successfully");
                // }
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }else{
        toastr.error('Verify if there are any payments under the current fee head. If payments exist, modifications are not allowed.');
    }
            
    });
});


    $(document).ready(function(){
        $('#all_students').click(function(){
            if($(this).prop('checked')){
                $('.student_select_checkbox').prop('checked',true);
            }else{
                $('.student_select_checkbox').prop('checked',false);
            }  
        });
        
        $(document).on('click','.student_select_checkbox',function(){
            var total_checkbox_count = $('.student_select_checkbox').length;
            var total_checked_checkbox_count = $('.student_select_checkbox:checkbox:checked').length;
            if(total_checkbox_count == total_checked_checkbox_count){
                $('#all_students').prop('checked',true);
            }else{
                $('#all_students').prop('checked',false);
            }
        });
        
        $('#admission_type_id').change(function() {
            
            $('#bulk_class_type_id').trigger('change');
        });
        $('#bulk_class_type_id').change(function() {
           var class_type_id = $('#bulk_class_type_id').val();
           var bulk_admission_no = $('#bulk_admission_no').val();
           var admission_type_id = $('#admission_type_id').val();
            
            if(class_type_id == ""){
                toastr.error('plaase Select Class');
                $('#tbody_students_list').html("");
                $('#bulk_fees_master_ids').html("");
            }else{
                getStudents(class_type_id,bulk_admission_no,admission_type_id);       
                getMasterData(class_type_id);
            }
        });
        $('#bulk_admission_no').blur(function() {
           var class_type_id = $('#bulk_class_type_id').val();
           var bulk_admission_no = $('#bulk_admission_no').val();
             var admission_type_id = $('#admission_type_id').val();
            getStudents(class_type_id,bulk_admission_no,admission_type_id);   
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#assignFeesMultiple').on('submit', function(event){
           event.preventDefault();
           
           var checkedCount = $('.student_select_checkbox:checkbox:checked').length;
           
           if(checkedCount == 0){
               toastr.error("Please select students");
           }else{
               document.getElementById('assignFeesMultiple').submit();
           }
           
       }); 
    });

    function updateRefundFees(checkbox, id) {
    const hiddenInput = document.getElementById('refund_fees_value_' + id);
    if (checkbox.checked) {
        hiddenInput.value = 'yes';
    } else {
        hiddenInput.value = 'no';
    }
    saveRefundFees(id, hiddenInput.value); // Call the save function
}

// Move saveRefundFees outside $(document).ready() to make it accessible globally
function saveRefundFees(id, value) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: '/updateAssignedFees', // Replace with your actual route
        method: 'POST',
        data: {
            fees_assign_detail_id: id,
            value: value,
            field: 'fees_refund'
        },
        success: function(response) {
            toastr.success('Fees Updated Successfully');
        },
        error: function(xhr) {
            console.log('An error occurred:', xhr);
        }
    });
}
$(document).ready(function() {
    // Select All Checkbox Functionality
    $("#select_group").on("change", function() {
        $(".group_checkbox").prop("checked", $(this).prop("checked"));
    });

    // Individual Checkbox Functionality
    $(".group_checkbox").on("change", function() {
        if ($(".group_checkbox:checked").length === $(".group_checkbox").length) {
            $("#select_group").prop("checked", true);
        } else {
            $("#select_group").prop("checked", false);
            
        }
    });
});


</script>
@endsection
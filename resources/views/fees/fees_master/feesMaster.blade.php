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

@section('styles')
<style>
/* Viewport and Split Columns System (1:1 with expenseAdd & feesGroup) */
.fg-viewport-wrapper {
    height: calc(100vh - 58px);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: #eef2f6;
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
.btn-act-students {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
.btn-act-students:hover {
    background: #059669;
    color: #ffffff;
}
</style>
@endsection

@section('content')
<div class="content-wrapper fg-viewport-wrapper">

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
            <button type="button" class="hero-btn hero-btn-primary" data-toggle="modal" data-target="#students_list_modal" data-bs-toggle="modal" data-bs-target="#students_list_modal">
                <i class="fa fa-user-plus mr-1"></i> Student Fee Assign
            </button>
            <button type="button" class="hero-btn hero-btn-outline" id="fees_modification_btn" data-toggle="modal" data-target="#fees_modification" data-bs-toggle="modal" data-bs-target="#fees_modification">
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
                                            <button type="button" 
                                                    class="btn-act btn-act-students btn-assign-class-students" 
                                                    data-class-id="{{ $classTypeId }}" 
                                                    title="Assign Fees to Students of {{ $className }}">
                                                <i class="fa fa-user-plus"></i>
                                            </button>
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

{{-- 1. Fee Head Delete Confirmation Modal --}}
<div class="modal fade" id="Modal_id" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border:none; border-radius:3px; overflow:hidden; box-shadow:0 15px 35px -5px rgba(0,0,0,0.3);">
            <div class="modal-header" style="background:linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color:#ffffff; padding:10px 14px; border-bottom:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:space-between;">
                <h5 class="modal-title" style="font-size:13px; font-weight:700; color:#ffffff; display:flex; align-items:center; gap:6px; margin:0;">
                    <i class="fa fa-trash-o"></i> {{ __('messages.Delete Confirmation') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.85; background:none; border:none; font-size:18px; line-height:1; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('feesMasterDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center" style="padding:20px 16px; background:#fff;">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <div style="width:48px; height:48px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 12px auto;">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p style="font-size:12px; color:#475569; margin-bottom:4px;">Are you sure you want to delete this fee head:</p>
                    <h6 style="font-size:13.5px; font-weight:800; color:#002C54; margin:0;" id="delete_head_label"></h6>
                </div>
                <div class="modal-footer justify-content-center" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 12px; display:flex; gap:8px;">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 12px; font-size:11.5px;">{{ __('messages.Close') }}</button>
                    <button type="submit" class="dash-btn" style="height:28px; padding:0 16px; font-size:11.5px; background:#dc2626; color:#fff; border-color:#dc2626;"><i class="fa fa-trash mr-1"></i> {{ __('messages.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 2. Student Fee Assign Modal --}}
<div class="modal fade" id="students_list_modal" tabindex="-1" role="dialog" aria-labelledby="studentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border:none; border-radius:3px; overflow:hidden; box-shadow:0 20px 45px -8px rgba(0,44,84,0.35);">
            <div class="modal-header" style="background:linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; padding:10px 16px; border-bottom:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:space-between;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px; height:32px; background:rgba(56,189,248,0.18); border:1px solid rgba(56,189,248,0.35); border-radius:3px; color:#38bdf8; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="studentsModalLabel" style="font-size:13.5px; font-weight:700; color:#fff; margin:0; line-height:1.2;">
                            Bulk Assign Fees to Students
                        </h5>
                        <div style="font-size:10.5px; color:#93c5fd; line-height:1.2; margin-top:2px;">
                            Select class and assign fee heads in bulk to enrolled students
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.85; background:none; border:none; font-size:20px; line-height:1; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="assignFeesMultiple" action="{{ url('assignFeesMultipleStudents') }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:14px 16px; background:#ffffff;">
                    {{-- Filter Row --}}
                    <div class="row g-2 mb-3 align-items-end" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:3px; padding:10px;">
                        <div class="col-md-4">
                            <label class="font-weight-bold text-dark mb-1" style="font-size:11px;">Select Class <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" id="bulk_class_type_id" name="class_type_id" required style="font-size:11.5px; border-radius:2px;">
                                <option value="">-- Choose Class --</option>
                                @if(!empty($classType))
                                    @foreach($classType as $type)
                                        <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold text-dark mb-1" style="font-size:11px;">Admission No (Optional)</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Search by Adm No" name="admissionNo" id="bulk_admission_no" style="font-size:11.5px; border-radius:2px;">
                        </div>
                        <div class="col-md-5">
                            <label class="font-weight-bold text-dark mb-1" style="font-size:11px;">Fee Heads <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm select2" multiple id="bulk_fees_master_ids" name="fees_master_ids[]" required style="width:100%; font-size:11.5px;">
                            </select>
                        </div>
                    </div>

                    {{-- Students Table Container --}}
                    <div style="border:1px solid #cbd5e1; border-radius:3px; overflow:hidden; margin-bottom:10px;">
                        <div style="max-height:260px; overflow-y:auto;">
                            <table class="table table-sm table-bordered table-hover mb-0 text-center" style="font-size:11.5px;">
                                <thead style="position:sticky; top:0; background:#002C54; color:#ffffff; z-index:2;">
                                    <tr>
                                        <th style="width:36px; padding:6px;"><input type="checkbox" id="all_students" style="cursor:pointer;"></th>
                                        <th style="min-width:140px; padding:6px;">Student Name</th>
                                        <th style="min-width:90px; padding:6px;">Admission No</th>
                                        <th style="min-width:100px; padding:6px;">Mobile</th>
                                        <th style="min-width:120px; padding:6px;">Father's Name</th>
                                        <th style="min-width:100px; padding:6px;">Current Heads</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody_students_list">
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted" style="font-size:11.5px;">
                                            <i class="fa fa-info-circle text-info mr-1"></i> Please select a class above to load enrolled students.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Note Box --}}
                    <div class="d-flex align-items-center gap-2 p-2" style="background:#eff6ff; border:1px solid #bfdbfe; border-left:3px solid #3b82f6; border-radius:2px; font-size:11px; color:#1e293b;">
                        <i class="fa fa-info-circle text-primary" style="font-size:14px;"></i>
                        <div>
                            <b>Safe Assignment:</b> Already assigned fee heads will be skipped automatically to prevent duplicate fees.
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 16px; display:flex; align-items:center; justify-content:space-between;">
                    <div style="font-size:11px; color:#64748b;">
                        Selected: <b id="students_selected_counter" class="text-primary">0</b> student(s)
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 12px; font-size:11.5px;">
                            <i class="fa fa-times mr-1"></i> Cancel
                        </button>
                        <button type="submit" class="dash-btn" style="height:28px; padding:0 14px; font-size:11.5px; background:#0284c7; color:#fff; border-color:#0284c7;">
                            <i class="fa fa-check mr-1"></i> Assign Selected Fees
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 3. Modify Student Fees Modal --}}
<div class="modal fade" id="fees_modification" tabindex="-1" aria-labelledby="feesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" style="border:none; border-radius:3px; overflow:hidden; box-shadow:0 20px 45px -8px rgba(0,44,84,0.35);">
            <div class="modal-header" style="background:linear-gradient(135deg, #002C54 0%, #0f3460 100%); color:#ffffff; padding:10px 16px; border-bottom:1px solid rgba(255,255,255,0.12); display:flex; align-items:center; justify-content:space-between;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px; height:32px; background:rgba(56,189,248,0.18); border:1px solid rgba(56,189,248,0.35); border-radius:3px; color:#38bdf8; display:flex; align-items:center; justify-content:center; font-size:14px;">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                    <div>
                        <h5 class="modal-title" id="feesModalLabel" style="font-size:13.5px; font-weight:700; color:#fff; margin:0; line-height:1.2;">
                            Modify Student Fee Assignments
                        </h5>
                        <div style="font-size:10.5px; color:#93c5fd; line-height:1.2; margin-top:2px;">
                            Search student to adjust discounts, due dates, refund flags, or remove uncollected heads
                        </div>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.85; background:none; border:none; font-size:20px; line-height:1; cursor:pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="padding:14px 16px; background:#ffffff;">
                {{-- Search Filter Form --}}
                <div class="row g-2 mb-3 align-items-end" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:3px; padding:10px;">
                    <div class="col-md-3">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px;">Class</label>
                        <select class="form-control form-control-sm" id="class_modification" name="class_type_id" style="font-size:11.5px; border-radius:2px;">
                            <option value="">-- All Classes --</option>
                            @if(!empty($classType))
                                @foreach($classType as $type)
                                    <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold text-dark mb-1" style="font-size:11px;">Admission No / Student</label>
                        <input type="text" class="form-control form-control-sm" id="admission_modification" placeholder="Enter Admission No or Student Name" style="font-size:11.5px; border-radius:2px;">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="dash-btn" id="searchButton" style="height:31px; width:100%; font-size:11.5px; background:#0284c7; color:#fff; border-color:#0284c7;">
                            <i class="fa fa-search mr-1"></i> Search
                        </button>
                    </div>
                </div>

                {{-- Alert Notice --}}
                <div class="d-flex align-items-start gap-2 p-2 mb-2" style="background:#fffbeb; border:1px solid #fde68a; border-left:3px solid #f59e0b; border-radius:2px; font-size:11px; color:#92400e; line-height:1.4;">
                    <i class="fa fa-exclamation-circle text-warning mt-1" style="font-size:13px;"></i>
                    <div>
                        <b>Important:</b> If payments have already been collected for a fee head, the amount cannot be lowered below the paid sum. Changes to discount, due date, and fine auto-save upon leaving the field.
                    </div>
                </div>

                {{-- Table Wrap --}}
                <div style="border:1px solid #cbd5e1; border-radius:3px; overflow:hidden;">
                    <div style="max-height:280px; overflow-y:auto;">
                        <table class="table table-sm table-bordered table-hover mb-0 text-center" style="font-size:11.5px;">
                            <thead style="position:sticky; top:0; background:#002C54; color:#ffffff; z-index:2;">
                                <tr>
                                    <th style="min-width:130px; padding:6px;">Student Name</th>
                                    <th style="min-width:85px; padding:6px;">Adm No</th>
                                    <th style="min-width:95px; padding:6px;">Mobile</th>
                                    <th style="min-width:150px; padding:6px;">Fee Head</th>
                                    <th style="width:85px; padding:6px;">Discount (₹)</th>
                                    <th style="min-width:115px; padding:6px;">Due Date</th>
                                    <th style="width:75px; padding:6px;">Fine %</th>
                                    <th style="width:75px; padding:6px;">Refundable</th>
                                    <th style="width:65px; padding:6px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_modification">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted" style="font-size:11.5px;">
                                        <i class="fa fa-search text-muted mr-1"></i> Enter an admission number or select a class and click Search.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 16px; display:flex; justify-content:flex-end;">
                <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal" data-bs-dismiss="modal" style="height:28px; padding:0 14px; font-size:11.5px;">
                    <i class="fa fa-times mr-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. In-Column Excel live class filter
    function applyClassFilter() {
        var q = ($('#filter_class').val() || '').toLowerCase().trim();
        var matchCount = 0;

        $('.fm-class-row').each(function() {
            var className = ($(this).data('class') || '').toString().toLowerCase();
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

    // 2. Select All Checkbox on Matrix
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

    // 3. Delete Single Fee Head Modal Handler
    $(document).on('click', '.deleteData', function() {
        var delete_id = $(this).data('groupname');
        var label = $(this).data('groupname-label') || '';
        $('#delete_id').val(delete_id);
        if (label) {
            $('#delete_head_label').text('"' + label + '"');
        }
    });

    // 4. Quick Assign Students from Class Row
    $(document).on('click', '.btn-assign-class-students', function() {
        var classId = $(this).data('class-id');
        if (classId) {
            $('#bulk_class_type_id').val(classId).trigger('change');
            $('#students_list_modal').modal('show');
        }
    });

    // 5. Bulk Assign Students Modal Logic
    $('#all_students').on('click', function() {
        $('.student_select_checkbox').prop('checked', this.checked);
        updateStudentsCount();
    });

    $(document).on('click', '.student_select_checkbox', function() {
        var total = $('.student_select_checkbox').length;
        var checked = $('.student_select_checkbox:checked').length;
        $('#all_students').prop('checked', total > 0 && total === checked);
        updateStudentsCount();
    });

    function updateStudentsCount() {
        var count = $('.student_select_checkbox:checked').length;
        $('#students_selected_counter').text(count);
    }

    $('#bulk_class_type_id').on('change', function() {
        var class_type_id = $(this).val();
        var bulk_admission_no = ($('#bulk_admission_no').val() || '').trim();

        if (!class_type_id) {
            $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-4 text-muted" style="font-size:11.5px;"><i class="fa fa-info-circle text-info mr-1"></i> Please select a class above to load enrolled students.</td></tr>');
            $('#bulk_fees_master_ids').html('');
            $('#students_selected_counter').text('0');
            return;
        }

        getStudents(class_type_id, bulk_admission_no);
        getMasterData(class_type_id);
    });

    $('#bulk_admission_no').on('blur', function() {
        var class_type_id = $('#bulk_class_type_id').val();
        var bulk_admission_no = $(this).val().trim();
        if (class_type_id) {
            getStudents(class_type_id, bulk_admission_no);
        }
    });

    function getStudents(class_type_id, bulk_admission_no) {
        $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Loading students...</td></tr>');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('getStudentsList') }}",
            method: 'POST',
            data: {
                admissionNo: bulk_admission_no,
                class_type_id: class_type_id,
                admission_type_id: ''
            },
            success: function(response) {
                $('#tbody_students_list').html(response);
                $('#all_students').prop('checked', false);
                updateStudentsCount();
            },
            error: function(xhr) {
                console.error('Error fetching students list:', xhr);
                $('#tbody_students_list').html('<tr><td colspan="6" class="text-center py-3 text-danger">Failed to load students.</td></tr>');
            }
        });
    }

    function getMasterData(class_type_id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('getMasterData') }}",
            method: 'POST',
            data: {
                class_type_id: class_type_id
            },
            success: function(response) {
                var options = [];
                if (response && response.length > 0) {
                    for (var i = 0; i < response.length; i++) {
                        var name = response[i].fees_group_name || ('Head #' + response[i].id);
                        options.push('<option value="' + response[i].id + '">' + name + '</option>');
                    }
                    $('#bulk_fees_master_ids').html(options.join(''));
                } else {
                    $('#bulk_fees_master_ids').html('');
                }
            },
            error: function(xhr) {
                console.error('Error fetching fee master data:', xhr);
            }
        });
    }

    $('#assignFeesMultiple').on('submit', function(e) {
        var checkedCount = $('.student_select_checkbox:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            toastr.error("Please select at least one student.");
            return false;
        }
        var headsCount = $('#bulk_fees_master_ids').val();
        if (!headsCount || headsCount.length === 0) {
            e.preventDefault();
            toastr.error("Please select at least one fee head.");
            return false;
        }
    });

    // 6. Fees Modification Modal Logic
    $('#searchButton').on('click', function() {
        var admissionNo = ($('#admission_modification').val() || '').trim();
        var classTypeId = $('#class_modification').val();

        if (!admissionNo && !classTypeId) {
            toastr.warning('Please select a class or enter an admission number to search.');
            return;
        }

        $('#tbody_modification').html('<tr><td colspan="9" class="text-center py-4 text-muted"><i class="fa fa-spinner fa-spin mr-1"></i> Searching student fee records...</td></tr>');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('feesModification') }}",
            method: 'POST',
            data: {
                admissionNo: admissionNo,
                class_type_id: classTypeId,
                admission_type_id_modify: ''
            },
            success: function(response) {
                $('#tbody_modification').html(response);
            },
            error: function(xhr) {
                console.error('Error loading modification data:', xhr);
                $('#tbody_modification').html('<tr><td colspan="9" class="text-center py-3 text-danger">Failed to search student fee records.</td></tr>');
            }
        });
    });

    $('#tbody_modification').on('click', '.delete_assigned', function() {
        var fees_assign_detail_id = $(this).data('detail_id');
        var $row = $(this).closest('tr');

        if (!confirm('Are you sure you want to remove this assigned fee head?')) {
            return;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('deleteAssignedFees') }}",
            method: 'POST',
            data: {
                fees_assign_detail_id: fees_assign_detail_id
            },
            success: function() {
                toastr.success('Assigned fee head removed successfully.');
                $row.fadeOut(300, function() { $(this).remove(); });
            },
            error: function(xhr) {
                console.error('Error deleting assigned fee:', xhr);
                toastr.error('Failed to remove assigned fee head.');
            }
        });
    });

    $('#tbody_modification').on('focusout', '.fees_assign_detail', function() {
        var $input = $(this);
        var fees_assign_detail_id = $input.data('detail_id');
        var value = $input.val();
        var old_value = $input.data('old_value');
        var field = $input.attr('name');

        if (field === 'fees_group_amount') {
            var pay_fees = parseFloat($input.data('pay_fees') || 0);
            if (parseFloat(value) < pay_fees) {
                toastr.error('Amount cannot be less than already paid fee: ₹' + pay_fees);
                $input.val(old_value);
                return;
            }
        }

        if (value != old_value) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('updateAssignedFees') }}",
                method: 'POST',
                data: {
                    fees_assign_detail_id: fees_assign_detail_id,
                    value: value,
                    field: field
                },
                success: function() {
                    $input.data('old_value', value);
                    toastr.success('Fee updated successfully');
                },
                error: function(xhr) {
                    console.error('Error updating fee:', xhr);
                    toastr.error('Failed to update fee.');
                }
            });
        }
    });
});

function updateRefundFees(checkbox, id) {
    var val = checkbox.checked ? 'yes' : 'no';
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ url('updateAssignedFees') }}",
        method: 'POST',
        data: {
            fees_assign_detail_id: id,
            value: val,
            field: 'fees_refund'
        },
        success: function() {
            toastr.success('Refund status updated successfully');
        },
        error: function(xhr) {
            console.error('Error updating refund status:', xhr);
            toastr.error('Failed to update refund status');
        }
    });
}
</script>
@endsection
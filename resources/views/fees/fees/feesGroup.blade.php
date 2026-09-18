@extends('layout.app')

@php
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(11);
    $inUseGroupIds = $inUseGroupIds ?? [];
    $stats = $stats ?? [
        'total' => is_countable($dataview ?? []) ? count($dataview ?? []) : 0,
        'refundable' => 0,
        'non_refundable' => 0,
        'in_use' => 0,
    ];
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - FEES GROUP SIGNATURE THEME (FULL-VIEWPORT & EQUAL HEIGHT CARDS)
   Exact match with viewUser & expenseAdd Guidelines:
   - Full viewport height utilization (calc(100vh - 58px))
   - Equal-height synchronized left form card & right table card
   - Increased 14px spacious gap between columns
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Sticky table headers & in-column Excel filters (#08335c)
   ========================================================================== */

.fg-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 8px 12px 10px 12px;
    height: calc(100vh - 58px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.fg-page-wrapper * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner (Exact match with expenseAdd / viewUser) */
.fg-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 7px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 8px;
    flex-shrink: 0;
}
.fg-hero-text {
    display: flex;
    flex-direction: column;
}
.fg-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.fg-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.fg-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Stat Badges */
.fg-hero-stats {
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
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 12px;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-cyan {
    background: rgba(6, 182, 212, 0.2);
    border-color: rgba(6, 182, 212, 0.4);
    color: #a5f3fc;
}

/* Hero Action Buttons */
.fg-hero-actions {
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
    padding: 0 11px;
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
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    color: #ffffff;
    border-color: rgba(255,255,255,.6);
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
}

/* 2. Main Flex Grid Layout with Increased Gap */
.fg-main-grid {
    flex: 1;
    min-height: 0;
    display: flex;
    gap: 14px;
    overflow: hidden;
}
.fg-col-form {
    flex: 0 0 360px;
    max-width: 380px;
    min-width: 310px;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.fg-col-table {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

@media (max-width: 991.98px) {
    .fg-page-wrapper {
        height: auto;
        overflow: visible;
        padding-bottom: 24px;
    }
    .fg-main-grid {
        flex-direction: column;
        overflow: visible;
        gap: 12px;
    }
    .fg-col-form, .fg-col-table {
        flex: 1 1 auto;
        max-width: 100%;
        min-height: auto;
    }
}

/* 3. Unified Signature Cards (Equal Height Stretched) */
.signature-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
    overflow: hidden;
}
.dash-card-header {
    padding: 7px 12px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.badge-total-records {
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.12);
    color: #f1f5f9;
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.15);
}

/* 4. Left Form Card Body - Spacious & Clean */
.form-card-body {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 14px;
    background: #ffffff;
    overflow-y: auto;
}
.form-group-compact {
    margin-bottom: 12px;
}
.form-label-compact {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.form-control-compact {
    width: 100%;
    height: 30px;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: 500;
    color: #0f172a;
    background-color: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    transition: all .15s ease-in-out;
}
.form-control-compact:focus {
    border-color: #0284c7;
    outline: 0;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* Refund Switch Box */
.refund-toggle-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 10px 12px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.refund-toggle-text {
    display: flex;
    flex-direction: column;
    padding-right: 10px;
}
.refund-toggle-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 1px;
}
.refund-toggle-desc {
    font-size: 10px;
    color: #64748b;
    line-height: 1.3;
}

/* Pro-Tip Box */
.fg-tip-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 2px;
    padding: 10px 12px;
    font-size: 11px;
    color: #166534;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    margin-top: 8px;
}

/* 5. Right Table Card Viewport */
.table-scroll-container {
    flex: 1;
    min-height: 0;
    overflow-x: auto;
    overflow-y: auto;
    background: #ffffff;
    position: relative;
}
.dash-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
}
.dash-table thead {
    position: sticky;
    top: 0;
    z-index: 20;
}
.header-titles-row th {
    position: sticky;
    top: 0;
    background: #002C54;
    color: #ffffff;
    padding: 8px 8px;
    height: 36px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border-right: 1px solid rgba(255,255,255,.14);
    border-bottom: 2px solid #001f3d;
    white-space: nowrap;
    z-index: 22;
    vertical-align: middle;
}
.excel-filter-row th {
    position: sticky;
    top: 36px;
    background: #08335c;
    color: #ffffff;
    padding: 4px 6px;
    border-right: 1px solid rgba(255,255,255,.12);
    border-bottom: 2px solid #001f3d;
    z-index: 21;
    vertical-align: middle;
}
.excel-col-filter {
    width: 100%;
    height: 25px;
    padding: 2px 6px;
    font-size: 11px;
    border: 1px solid rgba(255,255,255,.25);
    background: #051e38;
    color: #ffffff;
    border-radius: 2px;
    outline: none;
    transition: all .15s;
    color-scheme: dark;
}
.excel-col-filter::placeholder {
    color: rgba(255,255,255,.55);
}
.excel-col-filter:focus {
    background: #031426 !important;
    border-color: #38bdf8 !important;
}
select.excel-col-filter {
    background-color: #051e38 !important;
    color: #ffffff !important;
    cursor: pointer;
}
select.excel-col-filter option {
    background-color: #002C54 !important;
    color: #ffffff !important;
}

/* Table Body Rows */
.dash-table tbody tr:nth-child(odd) {
    background: #ffffff;
}
.dash-table tbody tr:nth-child(even) {
    background: #f8fafc;
}
.dash-table tbody tr:hover {
    background: #edf2f7 !important;
}
.dash-table tbody td {
    padding: 7px 8px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
}

/* Status Badges */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.status-pill-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.status-pill-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.status-pill-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Action Buttons */
.action-btn-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-act {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 2px;
    font-size: 11px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s;
    text-decoration: none !important;
}
.btn-act-edit { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.btn-act-edit:hover { background: #0284c7; color: #ffffff; }
.btn-act-del { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
.btn-act-del:hover { background: #dc2626; color: #ffffff; }
.btn-act-lock { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }

/* Footer */
.table-card-footer {
    flex-shrink: 0;
    padding: 6px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    font-size: 11px;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Delete Modal */
.modal-signature .modal-content {
    border-radius: 2px;
    border: 1px solid #002C54;
    overflow: hidden;
}
.modal-signature .modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 8px 12px;
}
.modal-signature .modal-title {
    font-size: 13px;
    font-weight: 700;
}
</style>
@endsection

@section('content')
<div class="content-wrapper fg-page-wrapper">

    {{-- 1. Top Hero Banner (Signature Arise Dark Navy Header) --}}
    <div class="fg-hero">
        <div class="fg-hero-text">
            <span class="fg-kicker"><i class="fa fa-money mr-1"></i>Fees Management</span>
            <h1 class="fg-title">{{ __('fees.Fees Group') }}</h1>
            <p class="fg-subtitle">Configure fee heads, refund eligibility, and class-wise payment groupings</p>
        </div>

        {{-- Hero Metric Badges --}}
        <div class="fg-hero-stats">
            <span class="hero-stat-badge">Total Groups: <b>{{ $stats['total'] ?? 0 }}</b></span>
            <span class="hero-stat-badge badge-green">Refundable: <b>{{ $stats['refundable'] ?? 0 }}</b></span>
            <span class="hero-stat-badge badge-cyan">Standard: <b>{{ $stats['non_refundable'] ?? 0 }}</b></span>
            <span class="hero-stat-badge badge-amber">In-Use: <b>{{ $stats['in_use'] ?? 0 }}</b></span>
        </div>

        {{-- Hero Action Buttons --}}
        <div class="fg-hero-actions">
            <a href="{{ url('feesMaster') }}" class="dash-btn dash-btn-light">
                <i class="fa fa-sliders"></i> Fees Master
            </a>
            <a href="{{ url('feesCollectAdd') }}" class="dash-btn dash-btn-outline">
                <i class="fa fa-inr"></i> Collect Fees
            </a>
            <a href="{{ url('fee_dashboard') }}" class="dash-btn dash-btn-outline">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- 2. Main Flex Layout: Equal Height Cards with 14px Gap --}}
    <div class="fg-main-grid">
        
        {{-- Left Form Column (Equal Height) --}}
        <div class="fg-col-form">
            <div class="signature-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-plus-circle text-info"></i> {{ __('fees.Add Fees Group') }}
                    </h3>
                    <span class="badge-total-records">New Record</span>
                </div>
                <div class="form-card-body">
                    <form id="quickForm" action="{{ url('feesGroup') }}" method="post" class="d-flex flex-column h-100 justify-content-between">
                        @csrf
                        
                        <div>
                            {{-- Name Input --}}
                            <div class="form-group-compact">
                                <label class="form-label-compact" for="name">
                                    {{ __('messages.Name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-compact @error('name') is-invalid @enderror" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g. Tuition Fee, Hostel Fee, Exam Fee" 
                                       required 
                                       autofocus>
                                @error('name')
                                    <div class="text-danger font-weight-bold mt-1" style="font-size:11px;">
                                        <i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Refundable Fee Toggle --}}
                            <div class="refund-toggle-box">
                                <div class="refund-toggle-text">
                                    <span class="refund-toggle-title">Refundable Fee</span>
                                    <span class="refund-toggle-desc">Is this fee eligible for refund on admission cancellation?</span>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="refund_fees_toggle" onchange="toggleRefundStatus(this)">
                                    <label class="custom-control-label" for="refund_fees_toggle"></label>
                                </div>
                                <input type="hidden" id="fees_refund" name="fees_refund" value="no">
                            </div>

                            {{-- Pro-Tip Banner --}}
                            <div class="fg-tip-banner">
                                <i class="fa fa-lightbulb-o mt-1 text-success font-weight-bold"></i>
                                <div>
                                    <strong>Pro-Tip:</strong> Once created, link this fee group to classes and assign amounts inside <strong>Fees Master</strong>.
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button at bottom of card --}}
                        <div class="pt-3 border-top mt-3">
                            <button type="submit" class="dash-btn dash-btn-primary w-100" style="height:32px; font-size:12px; font-weight:700;">
                                <i class="fa fa-plus-circle mr-1"></i> {{ __('messages.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Table Column (Equal Height) --}}
        <div class="fg-col-table">
            <div class="signature-card">
                <div class="dash-card-header">
                    <h3 class="dash-card-title">
                        <i class="fa fa-list text-info"></i> {{ __('Fees Group List') }}
                    </h3>
                    <span class="badge-total-records">
                        Total Records: <b>{{ count($dataview ?? []) }}</b>
                    </span>
                </div>

                {{-- Scrollable Table Body with Sticky Headers --}}
                <div class="table-scroll-container">
                    <table class="dash-table" id="feesGroupTable">
                        <thead>
                            {{-- Row 1: Titles --}}
                            <tr class="header-titles-row">
                                <th style="width: 50px; text-align: center;">{{ __('messages.Sr.No.') }}</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th style="width: 140px; text-align: center;">{{ __('Fees Refund') }}</th>
                                <th style="width: 120px; text-align: center;">Usage Status</th>
                                <th style="width: 85px; text-align: center;">{{ __('messages.Action') }}</th>
                            </tr>

                            {{-- Row 2: In-Column Sticky Excel Filter --}}
                            <tr class="excel-filter-row">
                                <th></th>
                                <th>
                                    <input type="text" id="filter_name" class="excel-col-filter" placeholder="Filter Name...">
                                </th>
                                <th>
                                    <select id="filter_refund" class="excel-col-filter">
                                        <option value="">All Status</option>
                                        <option value="refundable">Refundable</option>
                                        <option value="non-refundable">Non-Refundable</option>
                                    </select>
                                </th>
                                <th>
                                    <select id="filter_usage" class="excel-col-filter">
                                        <option value="">All</option>
                                        <option value="in-use">In-Use</option>
                                        <option value="unlinked">Unlinked</option>
                                    </select>
                                </th>
                                <th style="text-align: center;">
                                    <button type="button" id="btn_clear_filters" class="dash-btn dash-btn-sm dash-btn-outline w-100" style="height:23px; font-size:10px;" title="Reset Filters">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($dataview) && count($dataview) > 0)
                                @php $i = 1; @endphp
                                @foreach ($dataview as $item)
                                    @php
                                        $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                                        $isInUse = in_array($item->id, $inUseGroupIds);
                                        $nameLower = strtolower($item->name ?? '');
                                        $refundVal = $isRefundable ? 'refundable' : 'non-refundable';
                                        $usageVal = $isInUse ? 'in-use' : 'unlinked';
                                    @endphp
                                    <tr class="fg-row" 
                                        data-name="{{ $nameLower }}" 
                                        data-refund="{{ $refundVal }}" 
                                        data-usage="{{ $usageVal }}">
                                        <td style="text-align: center; font-weight: 600;">{{ $i++ }}</td>
                                        <td>
                                            <span class="font-weight-bold text-dark">{{ $item->name ?? '' }}</span>
                                            @if($item->fees_type === 'installment')
                                                <span class="badge badge-light border ml-1" style="font-size:9.5px;">Installment</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($isRefundable)
                                                <span class="status-pill status-pill-success">
                                                    <i class="fa fa-check-circle"></i> Refundable
                                                </span>
                                            @else
                                                <span class="status-pill status-pill-secondary">
                                                    <i class="fa fa-minus-circle"></i> Non-Refundable
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            @if($isInUse)
                                                <span class="status-pill status-pill-warning" title="Assigned in Fees Master / Receipts">
                                                    <i class="fa fa-link"></i> In-Use
                                                </span>
                                            @else
                                                <span class="status-pill status-pill-secondary" title="Not currently assigned">
                                                    <i class="fa fa-circle-o"></i> Unlinked
                                                </span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div class="action-btn-wrap">
                                                {{-- Edit Action --}}
                                                <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                                                   class="btn-act btn-act-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                                                   title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                {{-- Delete Action --}}
                                                @if(!$isInUse)
                                                    <button type="button" 
                                                            class="btn-act btn-act-del btn-delete-group {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                                            data-id="{{ $item->id }}" 
                                                            data-name="{{ $item->name }}" 
                                                            title="Delete">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn-act btn-act-lock" title="Protected: Currently assigned to classes/students" disabled>
                                                        <i class="fa fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr id="fgEmptyFilterRow" class="d-none">
                                    <td colspan="5" class="text-center py-5">
                                        <div style="padding: 24px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                            <div style="width:52px; height:52px; border-radius:50%; background:#f8fafc; border:1.5px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; font-size:22px; color:#64748b; margin-bottom:10px;">
                                                <i class="fa fa-search"></i>
                                            </div>
                                            <div style="font-size:13px; font-weight:700; color:#002C54; margin-bottom:3px;">No Matching Fee Groups Found</div>
                                            <div style="font-size:11px; color:#64748b; margin-bottom:10px;">No groups match your current in-column filter criteria.</div>
                                            <button type="button" class="dash-btn dash-btn-secondary" id="btn_clear_empty_filters" style="height:28px; font-size:11px; padding:0 12px;">
                                                <i class="fa fa-refresh mr-1"></i> Clear Filters
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div style="padding: 30px 12px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                            <div style="width:54px; height:54px; border-radius:50%; background:#e0f2fe; border:1.5px dashed #7dd3fc; display:flex; align-items:center; justify-content:center; font-size:22px; color:#0284c7; margin-bottom:10px;">
                                                <i class="fa fa-folder-open-o"></i>
                                            </div>
                                            <div style="font-size:13.5px; font-weight:700; color:#002C54; margin-bottom:3px;">No Fee Groups Added Yet</div>
                                            <div style="font-size:11px; color:#64748b;">Use the form on the left to create your first fee group.</div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Table Card Footer --}}
                <div class="table-card-footer">
                    <span><i class="fa fa-shield text-warning mr-1"></i> <b>Safety Guard:</b> Active fee groups cannot be deleted while assigned.</span>
                    <span class="font-weight-bold text-dark">Showing {{ count($dataview ?? []) }} records</span>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade modal-signature" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-trash-o text-danger mr-1"></i> {{ __('messages.Delete Confirmation') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="modal_delete_id" name="delete_id">
                    <p class="mb-1 text-muted" style="font-size:11.5px;">Are you sure you want to permanently delete:</p>
                    <h6 class="font-weight-bold text-dark mb-0" id="modal_group_name"></h6>
                </div>
                <div class="modal-footer justify-content-center p-2 bg-light">
                    <button type="button" class="dash-btn dash-btn-outline text-dark border" data-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="dash-btn" style="background:#dc2626; color:#fff;">{{ __('messages.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleRefundStatus(checkbox) {
    document.getElementById('fees_refund').value = checkbox.checked ? 'yes' : 'no';
}

$(document).ready(function() {
    // Delete Modal Handler
    $('.btn-delete-group').on('click', function() {
        var groupId = $(this).data('id');
        var groupName = $(this).data('name');
        
        $('#modal_delete_id').val(groupId);
        $('#modal_group_name').text('"' + groupName + '"');
        $('#deleteConfirmModal').modal('show');
    });

    // In-Column Live Excel Filters
    function applyExcelFilters() {
        var filterName = $('#filter_name').val().toLowerCase().trim();
        var filterRefund = $('#filter_refund').val();
        var filterUsage = $('#filter_usage').val();
        var visibleCount = 0;

        $('.fg-row').each(function() {
            var rowName = $(this).data('name') || '';
            var rowRefund = $(this).data('refund') || '';
            var rowUsage = $(this).data('usage') || '';

            var matchName = !filterName || rowName.indexOf(filterName) !== -1;
            var matchRefund = !filterRefund || rowRefund === filterRefund;
            var matchUsage = !filterUsage || rowUsage === filterUsage;

            if (matchName && matchRefund && matchUsage) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        if (visibleCount === 0) {
            $('#fgEmptyFilterRow').removeClass('d-none');
        } else {
            $('#fgEmptyFilterRow').addClass('d-none');
        }
    }

    $('#filter_name').on('keyup input', applyExcelFilters);
    $('#filter_refund, #filter_usage').on('change', applyExcelFilters);

    $('#btn_clear_filters, #btn_clear_empty_filters').on('click', function() {
        $('#filter_name').val('');
        $('#filter_refund').val('');
        $('#filter_usage').val('');
        $('.fg-row').show();
        $('#fgEmptyFilterRow').addClass('d-none');
    });
});
</script>
@endsection
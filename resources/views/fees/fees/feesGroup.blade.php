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
   ARISE ERP - FEES GROUP SIGNATURE THEME
   Exact match with viewUser & expenseAdd Guidelines:
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 29px-30px inputs with #cbd5e1 border
   - Sticky table headers & in-column Excel filters (#08335c)
   ========================================================================== */

.fg-page-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.fg-page-wrapper * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner (Exact match with expenseAdd / viewUser) */
.fg-hero {
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

/* Quick Summary Stat Badges in Hero */
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
    padding: 3px 8px;
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

/* 2. Signature Card & Header Styles */
.signature-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    margin-bottom: 8px;
    overflow: hidden;
}
.dash-card-header {
    padding: 6px 10px;
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

/* 3. Form Layout (Exact match with expenseAdd) */
.form-card-body {
    padding: 12px 14px;
    background: #ffffff;
}
.form-group-compact {
    margin-bottom: 10px;
}
.form-label-compact {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.form-control-compact {
    width: 100%;
    height: 29px;
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

/* Custom Refund Box in Form */
.refund-toggle-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 8px 10px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.refund-toggle-text {
    display: flex;
    flex-direction: column;
}
.refund-toggle-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
}
.refund-toggle-desc {
    font-size: 10px;
    color: #64748b;
}

/* 4. Table Layout (Exact match with viewUser) */
.table-scroll-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: calc(100vh - 210px);
    background: #ffffff;
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
    padding: 7px 8px;
    height: 34px;
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
    top: 34px;
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
    padding: 6px 8px;
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

/* Compact Action Buttons */
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

/* Tip Banner in Form */
.fg-tip-banner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 2px;
    padding: 8px 10px;
    font-size: 11px;
    color: #166534;
    display: flex;
    gap: 6px;
    align-items: flex-start;
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
    <section class="content p-0">
        <div class="container-fluid p-0">

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

            {{-- 2. Split Content Layout --}}
            <div class="row m-0">
                
                {{-- Left: Create Form (Exact match with expenseAdd) --}}
                <div class="col-lg-4 col-md-5 p-0 pr-md-2">
                    <div class="signature-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title">
                                <i class="fa fa-plus-circle text-info"></i> {{ __('fees.Add Fees Group') }}
                            </h3>
                            <span class="badge-total-records">New Record</span>
                        </div>
                        <div class="form-card-body">
                            <form id="quickForm" action="{{ url('feesGroup') }}" method="post">
                                @csrf

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
                                        <span class="refund-toggle-desc">Is this fee eligible for refund upon cancellation?</span>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="refund_fees_toggle" onchange="toggleRefundStatus(this)">
                                        <label class="custom-control-label" for="refund_fees_toggle"></label>
                                    </div>
                                    <input type="hidden" id="fees_refund" name="fees_refund" value="no">
                                </div>

                                {{-- Submit Button --}}
                                <div class="mb-2">
                                    <button type="submit" class="dash-btn dash-btn-primary w-100" style="height:30px; font-size:12px;">
                                        <i class="fa fa-plus-circle mr-1"></i> {{ __('messages.submit') }}
                                    </button>
                                </div>

                                {{-- Pro-Tip --}}
                                <div class="fg-tip-banner">
                                    <i class="fa fa-lightbulb-o mt-1 text-success"></i>
                                    <div>
                                        <strong>Pro-Tip:</strong> After adding a group, configure class-wise amounts and due dates in <strong>Fees Master</strong>.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right: Groups Listing Table (Exact match with viewUser) --}}
                <div class="col-lg-8 col-md-7 p-0 pl-md-0">
                    <div class="signature-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title">
                                <i class="fa fa-list text-info"></i> {{ __('Fees Group List') }}
                            </h3>
                            <span class="badge-total-records">
                                Total Records: <b>{{ count($dataview ?? []) }}</b>
                            </span>
                        </div>

                        {{-- Table Container with In-Column Excel Filters --}}
                        <div class="table-scroll-container">
                            <table class="dash-table" id="feesGroupTable">
                                <thead>
                                    {{-- Row 1: Thead Column Titles --}}
                                    <tr class="header-titles-row">
                                        <th style="width: 45px; text-align: center;">{{ __('messages.Sr.No.') }}</th>
                                        <th>{{ __('messages.Name') }}</th>
                                        <th style="width: 140px; text-align: center;">{{ __('Fees Refund') }}</th>
                                        <th style="width: 110px; text-align: center;">Usage Status</th>
                                        <th style="width: 80px; text-align: center;">{{ __('messages.Action') }}</th>
                                    </tr>

                                    {{-- Row 2: Sticky Excel-Style Filter Bar --}}
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
                                            <button type="button" id="btn_clear_filters" class="dash-btn dash-btn-sm dash-btn-outline w-100" style="height:23px; font-size:10px;" title="Clear Filters">
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
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="fa fa-folder-open-o fa-2x mb-2 text-secondary d-block"></i>
                                                No Fees Groups found. Use the form on the left to add one!
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- Card Footer Safety Notice --}}
                        <div class="p-2 border-top bg-light text-muted d-flex align-items-center justify-content-between" style="font-size:11px;">
                            <span><i class="fa fa-shield text-warning mr-1"></i> <b>Safety Guard:</b> Fee groups actively assigned to classes or receipt transactions cannot be deleted.</span>
                            <span class="font-weight-bold">Showing {{ count($dataview ?? []) }} items</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
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

    // In-Column Live Excel Filters (Exact match with viewUser live filter behavior)
    function applyExcelFilters() {
        var filterName = $('#filter_name').val().toLowerCase().trim();
        var filterRefund = $('#filter_refund').val();
        var filterUsage = $('#filter_usage').val();

        $('.fg-row').each(function() {
            var rowName = $(this).data('name') || '';
            var rowRefund = $(this).data('refund') || '';
            var rowUsage = $(this).data('usage') || '';

            var matchName = !filterName || rowName.indexOf(filterName) !== -1;
            var matchRefund = !filterRefund || rowRefund === filterRefund;
            var matchUsage = !filterUsage || rowUsage === filterUsage;

            if (matchName && matchRefund && matchUsage) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $('#filter_name').on('keyup input', applyExcelFilters);
    $('#filter_refund, #filter_usage').on('change', applyExcelFilters);

    $('#btn_clear_filters').on('click', function() {
        $('#filter_name').val('');
        $('#filter_refund').val('');
        $('#filter_usage').val('');
        $('.fg-row').show();
    });
});
</script>
@endsection
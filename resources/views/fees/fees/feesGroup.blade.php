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

@extends('layout.app') 

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MODERN FEES GROUP DESKTOP STYLES
   ========================================================================== */
.fees-group-page {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: #1e293b;
    padding-bottom: 30px;
}

/* 1. Breadcrumb Bar */
.fg-breadcrumb-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}
.fg-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
    font-weight: 600;
}
.fg-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s ease;
}
.fg-breadcrumb a:hover {
    color: #002C54;
}
.fg-breadcrumb .separator {
    color: #cbd5e1;
    font-size: 11px;
}
.fg-breadcrumb .current {
    color: #002C54;
    font-weight: 700;
}

/* 2. Quick Action Buttons */
.fg-top-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.fg-btn-nav {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}
.fg-btn-nav-primary {
    background: #002C54;
    color: #ffffff;
}
.fg-btn-nav-primary:hover {
    background: #001f3f;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}
.fg-btn-nav-outline {
    background: #ffffff;
    color: #002C54;
    border-color: #cbd5e1;
}
.fg-btn-nav-outline:hover {
    background: #f8fafc;
    border-color: #002C54;
    color: #002C54;
}

/* 3. KPI Tiles Row */
.fg-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
.fg-kpi-card {
    background: #ffffff;
    border-radius: 6px;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.fg-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.fg-kpi-info {
    display: flex;
    flex-direction: column;
}
.fg-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    margin-bottom: 2px;
}
.fg-kpi-value {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.fg-kpi-icon {
    width: 42px;
    height: 42px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.kpi-icon-blue { background: #e0f2fe; color: #0284c7; }
.kpi-icon-green { background: #dcfce7; color: #16a34a; }
.kpi-icon-amber { background: #fef3c7; color: #d97706; }
.kpi-icon-purple { background: #ede9fe; color: #7c3aed; }

/* 4. Content Cards */
.fg-panel {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
    margin-bottom: 16px;
}
.fg-panel-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 11px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.fg-panel-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.fg-panel-badge {
    font-size: 11px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 600;
}
.fg-panel-body {
    padding: 16px;
}

/* 5. Modern Form Elements */
.fg-form-group {
    margin-bottom: 14px;
}
.fg-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 5px;
}
.fg-label .required {
    color: #ef4444;
}
.fg-input {
    width: 100%;
    font-size: 13.5px;
    font-weight: 500;
    padding: 9px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 5px;
    color: #0f172a;
    transition: all 0.2s ease;
    background: #ffffff;
}
.fg-input:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Custom Styled Toggle Switch */
.fg-switch-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.fg-switch-label-wrap {
    display: flex;
    flex-direction: column;
}
.fg-switch-title {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1px;
}
.fg-switch-desc {
    font-size: 11px;
    color: #64748b;
}
.fg-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
}
.fg-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.fg-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}
.fg-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.fg-toggle input:checked + .fg-toggle-slider {
    background-color: #10b981;
}
.fg-toggle input:checked + .fg-toggle-slider:before {
    transform: translateX(20px);
}

/* Info Alert Callout */
.fg-tip-box {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 12px;
    color: #166534;
    display: flex;
    gap: 8px;
    align-items: flex-start;
    margin-top: 14px;
}
.fg-tip-box i {
    font-size: 14px;
    margin-top: 1px;
    color: #16a34a;
}

/* 6. Modern Interactive Table */
.fg-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.fg-table thead th {
    background: #002C54;
    color: #ffffff;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 10px 12px;
    border: none;
    vertical-align: middle;
}
.fg-table tbody td {
    padding: 10px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}
.fg-table tbody tr:hover {
    background: #f8fafc;
}
.fg-table tbody tr:last-child td {
    border-bottom: none;
}

/* Status Badges */
.fg-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    letter-spacing: 0.2px;
}
.fg-badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.fg-badge-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.fg-badge-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Table Action Buttons */
.fg-action-group {
    display: flex;
    align-items: center;
    gap: 6px;
}
.fg-btn-act {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-size: 12px;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    text-decoration: none;
}
.fg-btn-edit {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}
.fg-btn-edit:hover {
    background: #0284c7;
    color: #ffffff;
}
.fg-btn-del {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}
.fg-btn-del:hover {
    background: #dc2626;
    color: #ffffff;
}
.fg-btn-disabled {
    background: #f1f5f9;
    color: #94a3b8;
    border-color: #e2e8f0;
    cursor: not-allowed;
}

/* Note Callout */
.fg-note-box {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 11.5px;
    color: #92400e;
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* 7. Delete Modal Custom Styling */
.fg-del-modal .modal-content {
    border-radius: 8px;
    overflow: hidden;
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
}
.fg-del-modal .modal-header {
    background: #001833;
    color: #ffffff;
    padding: 12px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.fg-del-modal .modal-title {
    font-size: 14px;
    font-weight: 700;
}
.fg-del-modal .modal-body {
    padding: 20px;
    text-align: center;
}
.fg-del-icon-circle {
    width: 54px;
    height: 54px;
    background: #fee2e2;
    color: #dc2626;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 14px;
}
.fg-del-modal .modal-footer {
    padding: 12px 18px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
</style>
@endsection

@section('content')
<div class="content-wrapper fees-group-page">
    <section class="content pt-3">
        <div class="container-fluid">
            
            {{-- 1. Top Breadcrumb & Quick Actions Bar --}}
            <div class="fg-breadcrumb-wrap">
                <ul class="fg-breadcrumb">
                    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="separator"><i class="fa fa-chevron-right"></i></li>
                    <li><a href="{{ url('fee_dashboard') }}">Fees Management</a></li>
                    <li class="separator"><i class="fa fa-chevron-right"></i></li>
                    <li class="current">Fees Group</li>
                </ul>
                <div class="fg-top-actions">
                    <a href="{{ url('feesMaster') }}" class="fg-btn-nav fg-btn-nav-outline">
                        <i class="fa fa-sliders"></i> Fees Master
                    </a>
                    <a href="{{ url('feesCollectAdd') }}" class="fg-btn-nav fg-btn-nav-outline">
                        <i class="fa fa-inr"></i> Collect Fees
                    </a>
                    <a href="{{ url('fee_dashboard') }}" class="fg-btn-nav fg-btn-nav-primary">
                        <i class="fa fa-arrow-left"></i> Fees Dashboard
                    </a>
                </div>
            </div>

            {{-- 2. KPI Summary Tiles --}}
            <div class="fg-kpi-grid">
                {{-- Total Groups --}}
                <div class="fg-kpi-card">
                    <div class="fg-kpi-info">
                        <span class="fg-kpi-label">Total Fee Groups</span>
                        <span class="fg-kpi-value">{{ number_format($stats['total'] ?? 0) }}</span>
                    </div>
                    <div class="fg-kpi-icon kpi-icon-blue">
                        <i class="fa fa-folder-open-o"></i>
                    </div>
                </div>

                {{-- Refundable Groups --}}
                <div class="fg-kpi-card">
                    <div class="fg-kpi-info">
                        <span class="fg-kpi-label">Refundable Groups</span>
                        <span class="fg-kpi-value">{{ number_format($stats['refundable'] ?? 0) }}</span>
                    </div>
                    <div class="fg-kpi-icon kpi-icon-green">
                        <i class="fa fa-refresh"></i>
                    </div>
                </div>

                {{-- Standard Groups --}}
                <div class="fg-kpi-card">
                    <div class="fg-kpi-info">
                        <span class="fg-kpi-label">Standard / Non-Refund</span>
                        <span class="fg-kpi-value">{{ number_format($stats['non_refundable'] ?? 0) }}</span>
                    </div>
                    <div class="fg-kpi-icon kpi-icon-purple">
                        <i class="fa fa-money"></i>
                    </div>
                </div>

                {{-- Active In-Use --}}
                <div class="fg-kpi-card">
                    <div class="fg-kpi-info">
                        <span class="fg-kpi-label">Assigned / In-Use</span>
                        <span class="fg-kpi-value">{{ number_format($stats['in_use'] ?? 0) }}</span>
                    </div>
                    <div class="fg-kpi-icon kpi-icon-amber">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                </div>
            </div>

            {{-- 3. Main Split Layout --}}
            <div class="row">
                
                {{-- Left: Create Form --}}
                <div class="col-lg-4 col-md-5">
                    <div class="fg-panel">
                        <div class="fg-panel-header">
                            <h3 class="fg-panel-title">
                                <i class="fa fa-plus-circle text-info"></i> {{ __('fees.Add Fees Group') }}
                            </h3>
                            <span class="fg-panel-badge">New Group</span>
                        </div>
                        <div class="fg-panel-body">
                            <form id="quickForm" action="{{ url('feesGroup') }}" method="post">
                                @csrf
                                
                                {{-- Group Name Input --}}
                                <div class="fg-form-group">
                                    <label class="fg-label" for="name">
                                        Group Name <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           class="fg-input @error('name') is-invalid @enderror" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g. Tuition Fee, Hostel Fee, Exam Fee" 
                                           required 
                                           autofocus>
                                    @error('name')
                                        <div class="text-danger font-weight-bold mt-1" style="font-size:12px;">
                                            <i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Refundable Fee Toggle Switch --}}
                                <div class="fg-switch-card">
                                    <div class="fg-switch-label-wrap">
                                        <span class="fg-switch-title">Refundable Fee</span>
                                        <span class="fg-switch-desc">Can this fee be refunded during cancellation?</span>
                                    </div>
                                    <label class="fg-toggle mb-0">
                                        <input type="checkbox" id="refund_fees_toggle" onchange="toggleRefundStatus(this)">
                                        <span class="fg-toggle-slider"></span>
                                    </label>
                                    <input type="hidden" id="fees_refund" name="fees_refund" value="no">
                                </div>

                                {{-- Submit Button --}}
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-block" style="background:#002C54; border-color:#002C54; font-weight:700; padding:9px;">
                                        <i class="fa fa-plus mr-1"></i> {{ __('messages.submit') }}
                                    </button>
                                </div>

                                {{-- Helper Tip --}}
                                <div class="fg-tip-box">
                                    <i class="fa fa-info-circle"></i>
                                    <div>
                                        <strong>Pro-Tip:</strong> Once a fee group is created, you can assign class-wise amounts and installment due dates inside <strong>Fees Master</strong>.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right: Groups Listing Table --}}
                <div class="col-lg-8 col-md-7">
                    <div class="fg-panel">
                        <div class="fg-panel-header">
                            <h3 class="fg-panel-title">
                                <i class="fa fa-list text-info"></i> Fee Groups Directory
                            </h3>
                            <span class="fg-panel-badge">{{ count($dataview ?? []) }} Groups</span>
                        </div>
                        <div class="fg-panel-body p-0">
                            <div class="table-responsive">
                                <table id="feesGroupTable" class="fg-table table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">#</th>
                                            <th>Group Name</th>
                                            <th style="width: 140px;">Refund Status</th>
                                            <th style="width: 120px;">Usage</th>
                                            <th style="width: 100px; text-align: center;">{{ __('messages.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($dataview) && count($dataview) > 0)
                                            @php $index = 1; @endphp
                                            @foreach ($dataview as $item)
                                                @php
                                                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                                                    $isInUse = in_array($item->id, $inUseGroupIds);
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $index++ }}</strong></td>
                                                    <td>
                                                        <span class="font-weight-bold text-dark" style="font-size:13.5px;">{{ $item->name ?? '' }}</span>
                                                        @if($item->fees_type === 'installment')
                                                            <span class="badge badge-light border ml-1" style="font-size:10px;">Installment</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($isRefundable)
                                                            <span class="fg-badge fg-badge-success">
                                                                <i class="fa fa-check-circle"></i> Refundable
                                                            </span>
                                                        @else
                                                            <span class="fg-badge fg-badge-secondary">
                                                                <i class="fa fa-minus-circle"></i> Non-Refundable
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($isInUse)
                                                            <span class="fg-badge fg-badge-warning" title="Assigned in Fees Master or Student Transactions">
                                                                <i class="fa fa-link"></i> In-Use
                                                            </span>
                                                        @else
                                                            <span class="fg-badge fg-badge-secondary" title="Not currently linked to any class or student">
                                                                <i class="fa fa-circle-o"></i> Unlinked
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <div class="fg-action-group justify-content-center">
                                                            {{-- Edit Action --}}
                                                            <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                                                               class="fg-btn-act fg-btn-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                                                               title="Edit Fee Group">
                                                                <i class="fa fa-edit"></i>
                                                            </a>

                                                            {{-- Delete Action --}}
                                                            @if(!$isInUse)
                                                                <button type="button" 
                                                                        class="fg-btn-act fg-btn-del btn-delete-group {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                                                        data-id="{{ $item->id }}" 
                                                                        data-name="{{ $item->name }}"
                                                                        title="Delete Fee Group">
                                                                    <i class="fa fa-trash-o"></i>
                                                                </button>
                                                            @else
                                                                <button type="button" 
                                                                        class="fg-btn-act fg-btn-disabled" 
                                                                        title="Protected: Cannot delete group while in use in Fee Structure/Receipts" 
                                                                        disabled>
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
                                                    <i class="fa fa-folder-open-o fa-2x mb-2 d-block text-secondary"></i>
                                                    No Fees Groups found. Use the form on the left to add one!
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- Safety Note Footer --}}
                            <div class="p-3 border-top bg-light">
                                <div class="fg-note-box">
                                    <i class="fa fa-shield"></i>
                                    <span><strong>Safety Guard:</strong> Fee groups that are actively assigned to classes or have payment histories cannot be deleted until unassigned.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

{{-- Modern Delete Confirmation Modal --}}
<div class="modal fade fg-del-modal" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-exclamation-triangle text-warning mr-1"></i> Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="fg-del-icon-circle">
                        <i class="fa fa-trash"></i>
                    </div>
                    <input type="hidden" id="modal_delete_id" name="delete_id">
                    <h6 class="font-weight-bold mb-1">Delete Fee Group?</h6>
                    <p class="text-muted font-weight-normal mb-0" style="font-size:12.5px;">
                        Are you sure you want to permanently delete <strong id="modal_group_name" class="text-dark"></strong>?
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light border px-3 btn-sm font-weight-bold" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-3 btn-sm font-weight-bold">Yes, Delete</button>
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

    // Initialize DataTable if available
    if ($.fn.DataTable && $('#feesGroupTable tbody tr').length > 1) {
        $('#feesGroupTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 10,
            "order": [[0, "asc"]],
            "language": {
                "search": "Quick Filter:"
            }
        });
    }
});
</script>
@endsection
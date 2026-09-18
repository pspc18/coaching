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
$isRefundable = old('fees_refund', $data->fees_refund ?? 'no') === 'yes';
$isInUse = in_array($data->id ?? 0, $inUseGroupIds);
@endphp

@extends('layout.app') 

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MODERN FEES GROUP EDIT STYLES
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

/* 2. Content Cards */
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
.fg-panel-body {
    padding: 20px;
}

/* Form Styles */
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
    background: #ffffff;
}
.fg-input:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

/* Switch */
.fg-switch-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
</style>
@endsection

@section('content')
<div class="content-wrapper fees-group-page">
    <section class="content pt-3">
        <div class="container-fluid">
            
            {{-- Breadcrumbs & Navigation Bar --}}
            <div class="fg-breadcrumb-wrap">
                <ul class="fg-breadcrumb">
                    <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="separator"><i class="fa fa-chevron-right"></i></li>
                    <li><a href="{{ url('fee_dashboard') }}">Fees Management</a></li>
                    <li class="separator"><i class="fa fa-chevron-right"></i></li>
                    <li><a href="{{ url('feesGroup') }}">Fees Group</a></li>
                    <li class="separator"><i class="fa fa-chevron-right"></i></li>
                    <li class="current">Edit Group</li>
                </ul>
                <div>
                    <a href="{{ url('feesGroup') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                        <i class="fa fa-arrow-left mr-1"></i> Back to Directory
                    </a>
                </div>
            </div>

            {{-- Main Edit Box --}}
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="fg-panel">
                        <div class="fg-panel-header">
                            <h3 class="fg-panel-title">
                                <i class="fa fa-pencil-square-o text-info"></i> {{ __('fees.Edit Fees Group') }}
                            </h3>
                            <span class="badge badge-light border" style="font-size:11px;">ID #{{ $data->id }}</span>
                        </div>
                        <div class="fg-panel-body">
                            <form id="quickForm" action="{{ url('feesGroupEdit') }}/{{ $data->id }}" method="post">
                                @csrf

                                {{-- Name Field --}}
                                <div class="mb-3">
                                    <label class="fg-label" for="name">Group Name <span class="required">*</span></label>
                                    <input type="text" 
                                           class="fg-input @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $data->name ?? '') }}" 
                                           placeholder="Group Name" 
                                           required>
                                    @error('name')
                                        <div class="text-danger font-weight-bold mt-1" style="font-size:12px;">
                                            <i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Refund Toggle --}}
                                <div class="fg-switch-card mb-4">
                                    <div>
                                        <span class="d-block font-weight-bold text-dark" style="font-size:13px;">Refundable Fee</span>
                                        <span class="text-muted" style="font-size:11.5px;">Can this fee be refunded on cancellation?</span>
                                    </div>
                                    <label class="fg-toggle mb-0">
                                        <input type="checkbox" id="refund_fees_toggle" {{ $isRefundable ? 'checked' : '' }} onchange="toggleRefundStatus(this)">
                                        <span class="fg-toggle-slider"></span>
                                    </label>
                                    <input type="hidden" id="fees_refund" name="fees_refund" value="{{ old('fees_refund', $data->fees_refund ?? 'no') }}">
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ url('feesGroup') }}" class="btn btn-light border px-3 mr-2 font-weight-bold">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="background:#002C54; border-color:#002C54;">
                                        <i class="fa fa-save mr-1"></i> {{ __('messages.Update') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
function toggleRefundStatus(checkbox) {
    document.getElementById('fees_refund').value = checkbox.checked ? 'yes' : 'no';
}
</script>
@endsection
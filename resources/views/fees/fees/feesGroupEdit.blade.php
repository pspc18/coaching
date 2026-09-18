@extends('layout.app')

@php
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(11);
    $inUseGroupIds = $inUseGroupIds ?? [];
    $isRefundable = old('fees_refund', $data->fees_refund ?? 'no') === 'yes';
    $isInUse = in_array($data->id ?? 0, $inUseGroupIds);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - EDIT FEES GROUP SIGNATURE THEME
   Exact match with expenseAdd:
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 29px-30px inputs with #cbd5e1 border
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

/* 1. Top Hero Banner (Exact match with expenseAdd) */
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

/* Card & Form Styles */
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
.form-card-body {
    padding: 14px 16px;
    background: #ffffff;
}
.form-group-compact {
    margin-bottom: 12px;
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
    margin-bottom: 14px;
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
</style>
@endsection

@section('content')
<div class="content-wrapper fg-page-wrapper">
    <section class="content p-0">
        <div class="container-fluid p-0">

            {{-- 1. Hero Banner (Signature Dark Navy Header) --}}
            <div class="fg-hero">
                <div class="fg-hero-text">
                    <span class="fg-kicker"><i class="fa fa-money mr-1"></i>Fees Management</span>
                    <h1 class="fg-title">{{ __('fees.Edit Fees Group') }}</h1>
                    <p class="fg-subtitle">Modify fee group details, title, and refund parameters</p>
                </div>

                {{-- Hero Action Buttons --}}
                <div class="fg-hero-actions">
                    <a href="{{ url('feesGroup') }}" class="dash-btn dash-btn-light">
                        <i class="fa fa-eye"></i> {{ __('messages.View') }}
                    </a>
                    <a href="{{ url('feesGroup') }}" class="dash-btn dash-btn-outline">
                        <i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}
                    </a>
                </div>
            </div>

            {{-- 2. Form Card --}}
            <div class="row m-0 justify-content-center">
                <div class="col-lg-5 col-md-7 p-0">
                    <div class="signature-card">
                        <div class="dash-card-header">
                            <h3 class="dash-card-title">
                                <i class="fa fa-edit text-info"></i> {{ __('fees.Edit Fees Group') }}
                            </h3>
                            <span class="badge badge-light border" style="font-size:10.5px;">Record #{{ $data->id }}</span>
                        </div>
                        <div class="form-card-body">
                            <form id="quickForm" action="{{ url('feesGroupEdit') }}/{{ $data->id }}" method="post">
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
                                           value="{{ old('name', $data->name ?? '') }}" 
                                           placeholder="Name" 
                                           required 
                                           autofocus>
                                    @error('name')
                                        <div class="text-danger font-weight-bold mt-1" style="font-size:11px;">
                                            <i class="fa fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Refund Toggle Box --}}
                                <div class="refund-toggle-box">
                                    <div class="refund-toggle-text">
                                        <span class="refund-toggle-title">Refundable Fee</span>
                                        <span class="refund-toggle-desc">Is this fee eligible for refund upon cancellation?</span>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="refund_fees_toggle" {{ $isRefundable ? 'checked' : '' }} onchange="toggleRefundStatus(this)">
                                        <label class="custom-control-label" for="refund_fees_toggle"></label>
                                    </div>
                                    <input type="hidden" id="fees_refund" name="fees_refund" value="{{ old('fees_refund', $data->fees_refund ?? 'no') }}">
                                </div>

                                {{-- Submit Actions --}}
                                <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
                                    <a href="{{ url('feesGroup') }}" class="dash-btn dash-btn-outline text-dark border mr-2" style="background:#f8fafc;">
                                        Cancel
                                    </a>
                                    <button type="submit" class="dash-btn dash-btn-primary" style="height:30px; padding:0 16px;">
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
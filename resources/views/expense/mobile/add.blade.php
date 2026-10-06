@php
    $first = $data->first();
    $rows = $data->isNotEmpty() ? $data : collect([(object) [
        'id' => null, 
        'category_id' => null, 
        'name' => '', 
        'quantity' => 1, 
        'rate' => '', 
        'amount' => ''
    ]]);
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $invoiceNoDisplay = optional($first)->invoice_no;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE ADD / EDIT EXPENSE VOUCHER STYLES
   - Aligned with Arise ERP Mobile Design System (admissionAdd, enquiryAdd)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Sticky Interactive Wizard Step Progress Tracker (Voucher -> Settlement -> Particulars)
   - Touch-friendly dynamic line-item cards with live quantity x rate calculations
   - Floating grand total bar & pinned bottom action dock
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
}
.mob-token-pill {
    font-size: 9.5px;
    background: rgba(74, 222, 128, 0.18);
    border: 1px solid rgba(74, 222, 128, 0.35);
    color: #4ade80;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 800;
}
.mob-hero-desc {
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 9px;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    width: 100%;
}
.mob-act-btn-view {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.mob-act-btn-view:active {
    transform: scale(0.98);
}

/* 2. Interactive Form Step Progress Tracker */
.mob-tracker-container {
    position: relative;
    width: 100%;
    margin-bottom: 10px;
}
.mob-step-tracker {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
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
    border-radius: 6px !important;
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
    gap: 5px;
    cursor: pointer;
    user-select: none;
    padding: 3px 6px;
    border-radius: 4px;
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
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: #f1f5f9;
    border: 1.5px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8.5px;
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
.mob-step-tracker.is-fixed .mob-step-node.completed .mob-step-dot {
    background: #16a34a;
    border-color: #4ade80;
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
    width: 30%;
    background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    border-radius: 2px;
    transition: width .25s ease;
}

/* 3. Mobile Form Section Cards */
.mob-section-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
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
    font-size: 12px;
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
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-section-status {
    font-size: 9.5px;
    font-weight: 700;
    color: #94a3b8;
}

/* Mobile Form Group & Controls */
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-label .req {
    color: #ef4444;
    font-weight: 800;
}
.mob-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.mob-input-prepend {
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
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    height: 32px;
    flex-shrink: 0;
}
.mob-input, .mob-select, .mob-textarea {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    transition: all .12s ease;
}
.mob-input-wrap .mob-input {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.mob-textarea {
    height: 55px;
    padding: 6px 8px;
    resize: vertical;
}
.mob-input:focus, .mob-select:focus, .mob-textarea:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.1);
}
.mob-input.is-invalid, .mob-select.is-invalid {
    border-color: #ef4444;
}

/* 4. Touch-Friendly Line Item Cards */
.mob-items-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-item-card {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    position: relative;
    border-left: 3.5px solid #002C54;
}
.mob-item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-item-num {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
}
.mob-item-calc-badge {
    font-size: 11px;
    font-weight: 800;
    color: #15803d;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    padding: 2px 7px;
    border-radius: 3px;
}
.mob-btn-delete-item {
    width: 24px;
    height: 24px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    margin-left: 6px;
}
.mob-btn-delete-item:active {
    background: #fee2e2;
}

/* Floating Live Grand Total Summary Box */
.mob-total-summary-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 8px 12px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 44, 84, 0.2);
}
.mob-total-label {
    font-size: 11px;
    font-weight: 700;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-total-amount {
    font-size: 17px;
    font-weight: 800;
    color: #4ade80;
}

/* 5. Pinned Bottom Action Dock */
.mob-submit-dock {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 8px;
}
.mob-btn-reset {
    width: 36px;
    height: 36px;
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
.mob-btn-submit {
    flex: 1;
    height: 36px;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 12.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
    transition: all .12s ease;
}
.mob-btn-submit:active {
    transform: scale(0.98);
}
.mob-btn-add-item {
    height: 30px;
    background: #f1f5f9;
    color: #002C54;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    width: 100%;
    margin-top: 2px;
}
.mob-btn-add-item:active {
    background: #e2e8f0;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-calculator text-primary"></i> {{ $editMode ? 'Edit Expense Voucher' : 'New Expense Voucher' }}
        </div>
        <div class="mob-hero-badges">
            <div class="mob-session-pill">
                <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
            </div>
            @if(!empty($invoiceNoDisplay))
                <div class="mob-token-pill" title="Voucher Number">
                    <i class="fa fa-hashtag"></i> {{ $invoiceNoDisplay }}
                </div>
            @endif
        </div>
    </div>
    <div class="mob-hero-desc">
        Record operational expenditure, vendor line-items &amp; payment settlement
    </div>
    <div class="mob-actions-bar">
        <a href="{{ url('expenseView') }}" class="mob-act-btn mob-act-btn-view">
            <i class="fa fa-list-alt"></i> View All Expense Register
        </a>
    </div>
</div>

{{-- 2. Interactive Form Step Progress Tracker --}}
<div class="mob-tracker-container" id="trackerContainer">
    <div class="mob-step-tracker" id="mobStepTracker">
        <div class="mob-step-nodes">
            <div class="mob-step-node active" id="trackerStep1">
                <span class="mob-step-dot" id="dotStep1">1</span>
                <span>Vendor</span>
            </div>
            <div class="mob-step-node" id="trackerStep2">
                <span class="mob-step-dot" id="dotStep2">2</span>
                <span>Payment</span>
            </div>
            <div class="mob-step-node" id="trackerStep3">
                <span class="mob-step-dot" id="dotStep3">3</span>
                <span>Items &amp; Total</span>
            </div>
        </div>
        <div class="mob-progress-bar-bg">
            <div class="mob-progress-bar-fill" id="formProgressFill"></div>
        </div>
    </div>
</div>

{{-- Error Messages Flash --}}
@if(isset($errors) && $errors->any())
    <div class="alert alert-danger py-2 px-3 mb-2" style="font-size:11px; border-radius:3px; font-weight:700;">
        <i class="fa fa-exclamation-triangle mr-1"></i> Please correct the highlighted errors:
        <ul class="mb-0 pl-3 mt-1" style="font-weight:500;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 3. Main Expense Form --}}
<form id="mob-expense-form" action="{{ url('expenseAdd') }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- Step 1: Voucher & Vendor Details --}}
    <div class="mob-section-card" id="cardStep1">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">01</span> Voucher &amp; Vendor Info
            </div>
            <span class="mob-section-status" id="statusStep1">Required fields</span>
        </div>

        {{-- Date & Paid To / Vendor in 2 Cols --}}
        <div class="row">
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Expense Date <span class="req">*</span></span>
                    </label>
                    <input type="date" class="mob-input" name="date" id="mob_date" value="{{ old('date', optional($first)->date ?: date('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Bill / Inv No.</span>
                    </label>
                    <input type="text" class="mob-input" name="bill_no" id="mob_bill_no" placeholder="e.g. INV-2026-08" value="{{ old('bill_no', optional($first)->bill_no) }}">
                </div>
            </div>
        </div>

        {{-- Paid To / Payee Name --}}
        <div class="mob-form-group">
            <label class="mob-form-label">
                <span>Paid To / Payee Vendor <span class="req">*</span></span>
            </label>
            <div class="mob-input-wrap">
                <span class="mob-input-prepend"><i class="fa fa-user-o"></i></span>
                <input type="text" 
                       class="mob-input @error('payee_name') is-invalid @enderror" 
                       name="payee_name" 
                       id="mob_payee_name" 
                       placeholder="Vendor, Shop, Landlord or Person" 
                       value="{{ old('payee_name', optional($first)->payee_name) }}" 
                       required 
                       autocomplete="off">
            </div>
        </div>

        {{-- Purpose & Description --}}
        <div class="mob-form-group mb-0">
            <label class="mob-form-label">Description / Remarks Notes</label>
            <textarea class="mob-textarea" name="description" id="mob_description" placeholder="Period covered, approval details, or specific context...">{{ old('description', optional($first)->description) }}</textarea>
        </div>
    </div>

    {{-- Step 2: Payment & Settlement Setup --}}
    <div class="mob-section-card" id="cardStep2">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">02</span> Payment &amp; Settlement Setup
            </div>
            <span class="mob-section-status" id="statusStep2">Required fields</span>
        </div>

        {{-- Payment Status & Payment Mode in 2 Cols --}}
        <div class="row">
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Status <span class="req">*</span></span>
                    </label>
                    <select class="mob-select" name="payment_status" id="mob_payment_status" required>
                        <option value="paid" {{ old('payment_status', optional($first)->payment_status ?: 'paid') == 'paid' ? 'selected' : '' }}>Paid (Settled)</option>
                        <option value="pending" {{ old('payment_status', optional($first)->payment_status) == 'pending' ? 'selected' : '' }}>Pending (Due)</option>
                    </select>
                </div>
            </div>
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Mode <span class="req">*</span></span>
                    </label>
                    <select class="mob-select" name="payment_mode_id" id="mob_payment_mode_id" required>
                        <option value="">Select Mode</option>
                        @foreach($paymentModes as $mode)
                            <option value="{{ $mode->id }}" {{ (string) old('payment_mode_id', optional($first)->payment_mode_id) === (string) $mode->id ? 'selected' : '' }}>
                                {{ $mode->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- UTR Ref & Expense Type --}}
        <div class="row">
            <div class="col-6 pr-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">UTR / Cheque Ref</label>
                    <input type="text" class="mob-input" name="payment_reference" id="mob_payment_reference" placeholder="e.g. UTR-98234" value="{{ old('payment_reference', optional($first)->payment_reference) }}">
                </div>
            </div>
            <div class="col-6 pl-1">
                <div class="mob-form-group">
                    <label class="mob-form-label">
                        <span>Expense Type <span class="req">*</span></span>
                    </label>
                    <select class="mob-select" name="expense_type" id="mob_expense_type" required>
                        <option value="one_time" {{ old('expense_type', optional($first)->expense_type ?: 'one_time') == 'one_time' ? 'selected' : '' }}>One-Time</option>
                        <option value="recurring" {{ old('expense_type', optional($first)->expense_type) == 'recurring' ? 'selected' : '' }}>Recurring Overhead</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Recurring Frequency (Conditional) --}}
        <div class="mob-form-group" id="mob_frequency_wrap" style="display: {{ old('expense_type', optional($first)->expense_type) == 'recurring' ? 'block' : 'none' }};">
            <label class="mob-form-label">Recurring Frequency <span class="req">*</span></label>
            <select class="mob-select" name="recurring_frequency" id="mob_recurring_frequency">
                <option value="">Select Frequency</option>
                @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'half_yearly' => 'Half-Yearly', 'yearly' => 'Yearly'] as $key => $label)
                    <option value="{{ $key }}" {{ old('recurring_frequency', optional($first)->recurring_frequency) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Receipt / Attachment Upload --}}
        <div class="mob-form-group mb-0">
            <label class="mob-form-label">
                <span>Receipt / Bill Attachment</span>
                <span style="font-size:8.5px; color:#64748b;">JPG, PNG, PDF (Max 4MB)</span>
            </label>
            <input type="file" class="mob-input" name="attachment" id="mob_attachment" accept=".jpg,.jpeg,.png,.pdf" style="padding-top:4px;">
            @if(optional($first)->attachment)
                <div class="mt-1" style="font-size:10px; font-weight:700;">
                    <a target="_blank" href="{{ env('IMAGE_SHOW_PATH') . 'expense/' . $first->attachment }}" class="text-primary">
                        <i class="fa fa-paperclip"></i> View Attached Receipt ({{ $first->attachment }})
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Step 3: Expense Particulars & Cost Breakdown --}}
    <div class="mob-section-card" id="cardStep3">
        <div class="mob-section-header">
            <div class="mob-section-title">
                <span class="mob-step-badge">03</span> Expense Line Items
            </div>
            <span class="mob-section-status" id="statusStep3">Rate &times; Qty</span>
        </div>

        {{-- Line Items Dynamic Container --}}
        <div class="mob-items-container" id="mobItemsContainer">
            @foreach($rows as $index => $row)
                <div class="mob-item-card" data-index="{{ $index }}">
                    <input type="hidden" name="id[]" value="{{ $row->id }}">
                    
                    <div class="mob-item-header">
                        <span class="mob-item-num">Item #<span class="item-serial">{{ $index + 1 }}</span></span>
                        <div class="d-flex align-items-center">
                            <span class="mob-item-calc-badge item-amount-badge">₹{{ number_format((float) $row->amount, 2) }}</span>
                            <button type="button" class="mob-btn-delete-item remove-item-btn" title="Remove Item">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Category Select --}}
                    <div class="mob-form-group">
                        <label class="mob-form-label">Category <span class="req">*</span></label>
                        <select name="category[]" class="mob-select item-category" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $id => $label)
                                <option value="{{ $id }}" {{ (string) old("category.$index", $row->category_id) === (string) $id ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Particular / Description --}}
                    <div class="mob-form-group">
                        <label class="mob-form-label">Item Description / Particular <span class="req">*</span></label>
                        <input type="text" name="name[]" class="mob-input item-name" value="{{ old("name.$index", $row->name) }}" placeholder="e.g. Office Stationery, Maintenance, Fuel..." required>
                    </div>

                    {{-- Quantity & Rate in 2 Cols --}}
                    <div class="row">
                        <div class="col-6 pr-1">
                            <div class="mob-form-group mb-0">
                                <label class="mob-form-label">Quantity <span class="req">*</span></label>
                                <input type="number" name="quantity[]" class="mob-input item-quantity" value="{{ old("quantity.$index", $row->quantity ?: 1) }}" min="0.01" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-6 pl-1">
                            <div class="mob-form-group mb-0">
                                <label class="mob-form-label">Rate (₹) <span class="req">*</span></label>
                                <input type="number" name="rate[]" class="mob-input item-rate" value="{{ old("rate.$index", $row->rate) }}" min="0" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Add Item Button --}}
        <button type="button" class="mob-btn-add-item" id="btnMobAddItem">
            <i class="fa fa-plus-circle text-primary"></i> Add Another Line Item
        </button>
    </div>

    {{-- Floating Live Grand Total Summary Box --}}
    <div class="mob-total-summary-card">
        <div class="mob-total-label">
            <i class="fa fa-calculator text-primary"></i> Voucher Total:
        </div>
        <div class="mob-total-amount" id="mobGrandTotalDisplay">
            ₹0.00
        </div>
    </div>

    {{-- 4. Pinned Bottom Action Dock --}}
    <div class="mob-submit-dock">
        <button type="reset" class="mob-btn-reset" id="btnMobReset" title="Reset Form">
            <i class="fa fa-refresh"></i>
        </button>
        <button type="submit" class="mob-btn-submit" id="btnMobSubmit">
            <i class="fa fa-check-circle"></i> {{ $editMode ? 'Update Expense Voucher' : 'Save & Submit Voucher' }}
        </button>
    </div>

</form>

{{-- Item Card Template for Dynamic Addition --}}
<template id="mobItemTemplate">
    <div class="mob-item-card">
        <input type="hidden" name="id[]" value="">
        <div class="mob-item-header">
            <span class="mob-item-num">Item #<span class="item-serial"></span></span>
            <div class="d-flex align-items-center">
                <span class="mob-item-calc-badge item-amount-badge">₹0.00</span>
                <button type="button" class="mob-btn-delete-item remove-item-btn" title="Remove Item">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="mob-form-group">
            <label class="mob-form-label">Category <span class="req">*</span></label>
            <select name="category[]" class="mob-select item-category" required>
                <option value="">Select Category</option>
                @foreach($categories as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="mob-form-group">
            <label class="mob-form-label">Item Description / Particular <span class="req">*</span></label>
            <input type="text" name="name[]" class="mob-input item-name" placeholder="e.g. Office Stationery, Maintenance, Fuel..." required>
        </div>
        <div class="row">
            <div class="col-6 pr-1">
                <div class="mob-form-group mb-0">
                    <label class="mob-form-label">Quantity <span class="req">*</span></label>
                    <input type="number" name="quantity[]" class="mob-input item-quantity" value="1" min="0.01" step="0.01" required>
                </div>
            </div>
            <div class="col-6 pl-1">
                <div class="mob-form-group mb-0">
                    <label class="mob-form-label">Rate (₹) <span class="req">*</span></label>
                    <input type="number" name="rate[]" class="mob-input item-rate" min="0" step="0.01" placeholder="0.00" required>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. Fixed Tracker Lock on Scroll (Beneath 48px Header + 6px gap)
    function checkStickyTracker() {
        const $container = $('#trackerContainer');
        const $tracker = $('#mobStepTracker');
        if (!$container.length || !$tracker.length) return;

        const containerTop = $container.offset().top;
        const scrollY = $(window).scrollTop() || window.pageYOffset || document.documentElement.scrollTop || 0;
        const triggerPoint = containerTop - 54;

        if (scrollY >= triggerPoint && triggerPoint > 0) {
            if (!$tracker.hasClass('is-fixed')) {
                const h = $tracker.outerHeight();
                $container.css('min-height', h + 'px');
                $tracker.addClass('is-fixed');
            }
        } else {
            if ($tracker.hasClass('is-fixed')) {
                $tracker.removeClass('is-fixed');
                $container.css('min-height', '');
            }
        }
    }

    // 2. Wizard Node Touch Scroll
    function scrollToStep(cardSelector, stepNodeSelector) {
        const $card = $(cardSelector);
        if ($card.length) {
            const targetOffset = $card.offset().top - 112;

            $('html, body').stop().animate({
                scrollTop: Math.max(0, targetOffset)
            }, 300, function() {
                checkStickyTracker();
            });

            $('.mob-step-node').removeClass('active');
            $(stepNodeSelector).addClass('active');

            $card.addClass('highlight-focus');
            setTimeout(function() {
                $card.removeClass('highlight-focus');
            }, 900);
        }
    }

    $('#trackerStep1').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep1', '#trackerStep1');
    });
    $('#trackerStep2').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep2', '#trackerStep2');
    });
    $('#trackerStep3').on('click tap', function(e) {
        e.preventDefault();
        scrollToStep('#cardStep3', '#trackerStep3');
    });

    // Auto update wizard steps on scroll
    function onUserScroll() {
        checkStickyTracker();
        const scrollPos = ($(window).scrollTop() || window.pageYOffset || 0) + 120;
        const s1Top = $('#cardStep1').length ? $('#cardStep1').offset().top : 0;
        const s2Top = $('#cardStep2').length ? $('#cardStep2').offset().top : 0;
        const s3Top = $('#cardStep3').length ? $('#cardStep3').offset().top : 0;

        $('.mob-step-node').removeClass('active');
        if (s3Top && scrollPos >= s3Top) {
            $('#trackerStep3').addClass('active');
        } else if (s2Top && scrollPos >= s2Top) {
            $('#trackerStep2').addClass('active');
        } else {
            $('#trackerStep1').addClass('active');
        }
    }

    window.addEventListener('scroll', onUserScroll, { passive: true });
    window.addEventListener('touchmove', onUserScroll, { passive: true });
    $(window).on('resize orientationchange', checkStickyTracker);
    setTimeout(checkStickyTracker, 60);

    // 3. Live Amount & Grand Total Calculations
    function updateCalculations() {
        let grandTotal = 0;
        const $items = $('#mobItemsContainer .mob-item-card');

        $items.each(function(idx) {
            $(this).find('.item-serial').text(idx + 1);

            const qty = parseFloat($(this).find('.item-quantity').val()) || 0;
            const rate = parseFloat($(this).find('.item-rate').val()) || 0;
            const itemTotal = qty * rate;

            $(this).find('.item-amount-badge').text('₹' + itemTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            grandTotal += itemTotal;
        });

        $('#mobGrandTotalDisplay').text('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Update Tracker status
        updateTracker(grandTotal);
    }

    // 4. Dynamic Step Tracker Validation
    function updateTracker(currentGrandTotal) {
        const payeeFilled = ($('#mob_payee_name').val() || '').trim().length > 0;
        const modeFilled = ($('#mob_payment_mode_id').val() || '').trim().length > 0;
        const itemsFilled = currentGrandTotal > 0;

        // Step 1
        $('#trackerStep1').toggleClass('completed', payeeFilled);
        $('#dotStep1').html(payeeFilled ? '<i class="fa fa-check"></i>' : '1');
        $('#statusStep1').text(payeeFilled ? '✓ Completed' : 'Required fields');
        $('#statusStep1').css('color', payeeFilled ? '#16a34a' : '#94a3b8');

        // Step 2
        $('#trackerStep2').toggleClass('completed', modeFilled);
        $('#dotStep2').html(modeFilled ? '<i class="fa fa-check"></i>' : '2');
        $('#statusStep2').text(modeFilled ? '✓ Completed' : 'Required fields');
        $('#statusStep2').css('color', modeFilled ? '#16a34a' : '#94a3b8');

        // Step 3
        $('#trackerStep3').toggleClass('completed', itemsFilled);
        $('#dotStep3').html(itemsFilled ? '<i class="fa fa-check"></i>' : '3');

        // Progress bar fill
        let progress = 30;
        if (payeeFilled) progress += 35;
        if (modeFilled) progress += 20;
        if (itemsFilled) progress += 15;
        $('#formProgressFill').css('width', Math.min(100, progress) + '%');
    }

    // 5. Add / Remove Line Items
    $('#btnMobAddItem').on('click', function() {
        const template = document.getElementById('mobItemTemplate');
        const clone = template.content.cloneNode(true);
        document.getElementById('mobItemsContainer').appendChild(clone);
        updateCalculations();
    });

    $(document).on('click', '.remove-item-btn', function() {
        if ($('#mobItemsContainer .mob-item-card').length > 1) {
            $(this).closest('.mob-item-card').remove();
            updateCalculations();
        } else {
            alert('At least one expense line item is required.');
        }
    });

    // 6. Real-time Calculation Listeners
    $(document).on('input change', '.item-quantity, .item-rate, #mob_payee_name, #mob_payment_mode_id', function() {
        updateCalculations();
    });

    // 7. Toggle Recurring Frequency Select
    $('#mob_expense_type').on('change', function() {
        $('#mob_frequency_wrap').toggle($(this).val() === 'recurring');
    });

    // 8. Form Submit Loading Feedback
    $('#mob-expense-form').on('submit', function() {
        const $btn = $('#btnMobSubmit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving Voucher...');
    });

    updateCalculations();
});
</script>
@endsection

@extends('layout.app')

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
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ADD / EDIT EXPENSE VOUCHER (SIGNATURE THEME)
   Exact match with userAdd & System Guidelines:
   - Sharp border-radius: 2px throughout
   - Signature Dark Navy Theme: #002C54 to #0f3460
   - Compact 29px-30px inputs with #cbd5e1 border
   - Equal-height symmetrical cards & Real-time Live Calculations
   ========================================================================== */

.expense-add-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 24px;
    min-height: calc(100vh - 56px);
}
.expense-add-wrapper * {
    box-sizing: border-box;
}

/* 1. Top Hero Banner */
.expense-hero {
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
.expense-hero-text {
    display: flex;
    flex-direction: column;
}
.expense-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.expense-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.expense-subtitle {
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
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1;
}

/* 2. Equal Height Symmetrical Cards */
.expense-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
    height: calc(100% - 6px);
}
.expense-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.expense-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.expense-card-body {
    padding: 10px 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Form Controls */
.form-group-compact {
    margin-bottom: 8px;
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
.form-control-compact {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    width: 100%;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
    outline: none;
}
textarea.form-control-compact {
    height: auto;
    min-height: 64px;
    resize: vertical;
}

/* 3. Particulars Breakdown Table */
.particulars-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    margin-bottom: 6px;
    overflow: hidden;
}
.particulars-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 11.5px;
    margin: 0;
}
.particulars-table th {
    background: #002C54;
    color: #ffffff;
    padding: 7px 8px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    border-right: 1px solid rgba(255,255,255,.12);
    vertical-align: middle;
}
.particulars-table th:last-child {
    border-right: none;
}
.particulars-table td {
    padding: 5px 6px;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    vertical-align: middle;
    background: #ffffff;
}
.particulars-table tr:nth-child(even) td {
    background: #f8fafc;
}

.table-input {
    height: 27px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 2px 6px;
    width: 100%;
    background: #ffffff;
    color: #0f172a;
}
.table-input:focus {
    border-color: #002C54;
    outline: none;
    box-shadow: 0 0 0 1px #002C54;
}
.table-input-readonly {
    background: #f1f5f9 !important;
    font-weight: 700;
    color: #002C54;
    text-align: right;
}

.btn-remove-row {
    width: 26px;
    height: 26px;
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #dc2626;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: all .1s;
    padding: 0;
}
.btn-remove-row:hover {
    background: #dc2626;
    color: #ffffff;
}

/* Voucher Total Summary Box */
.voucher-total-box {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
    padding: 6px 12px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.voucher-total-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #166534;
}
.voucher-total-val {
    font-size: 16px;
    font-weight: 800;
    color: #15803d;
}

/* 4. Form Action Footer */
.expense-form-footer {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
</style>
@endsection

@section('content')
<div class="content-wrapper expense-add-wrapper">
    
    {{-- 1. Top Hero Header (Arise Signature Dark Navy Theme) --}}
    <div class="expense-hero">
        <div class="expense-hero-text">
            <span class="expense-kicker"><i class="fa fa-calculator mr-1"></i> Financial Outflows & Accounts</span>
            <h1 class="expense-title"><i class="fa fa-file-text-o mr-1"></i> {{ $editMode ? 'Edit Expense Voucher' : 'Create Expense Voucher' }}</h1>
            <span class="expense-subtitle">Enter vendor particulars, breakdown line item rates and quantities, and attach supporting receipts.</span>
        </div>

        <div class="expense-hero-actions">
            <a href="{{ url('expenseView') }}" class="dash-btn dash-btn-light" title="Back to Expense Register">
                <i class="fa fa-arrow-left mr-1"></i> Back to Register
            </a>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger" style="border-radius: 2px; padding: 8px 12px; font-size: 11.5px; margin-bottom: 6px;">
            <strong><i class="fa fa-exclamation-triangle"></i> Please correct the highlighted fields:</strong>
            <ul class="mb-0 pl-3 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('expenseAdd') }}" method="post" enctype="multipart/form-data" id="expenseForm">
        @csrf

        {{-- 2. Symmetrical Two-Column Form Row (Equal Heights) --}}
        <div class="row">
            
            {{-- Card 1: Step 1 - Voucher & Vendor Details --}}
            <div class="col-lg-6">
                <div class="expense-card">
                    <div class="expense-card-header">
                        <h3 class="expense-card-title">
                            <span class="card-step">1</span>
                            <i class="fa fa-id-card-o text-info mr-1"></i> Voucher &amp; Vendor Information
                        </h3>
                    </div>
                    <div class="expense-card-body">
                        <div>
                            <div class="row">
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Expense Date <span class="req-star">*</span></span>
                                    </label>
                                    <input type="date" name="date" class="form-control-compact" value="{{ old('date', optional($first)->date ?: date('Y-m-d')) }}" required>
                                </div>
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Paid To / Vendor <span class="req-star">*</span></span>
                                    </label>
                                    <input type="text" name="payee_name" class="form-control-compact" value="{{ old('payee_name', optional($first)->payee_name) }}" placeholder="Vendor, Landlord or Service Provider" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Vendor Invoice / Bill No.</span>
                                    </label>
                                    <input type="text" name="bill_no" class="form-control-compact" value="{{ old('bill_no', optional($first)->bill_no) }}" placeholder="e.g. INV-2026-089 / BILL-102">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-compact mb-0">
                            <label class="form-label-compact">
                                <span>Purpose &amp; Description Notes</span>
                            </label>
                            <textarea name="description" class="form-control-compact" rows="3" placeholder="Approval notes, period covered, terms or specific expense context...">{{ old('description', optional($first)->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Step 2 - Payment & Settlement Setup --}}
            <div class="col-lg-6">
                <div class="expense-card">
                    <div class="expense-card-header">
                        <h3 class="expense-card-title">
                            <span class="card-step">2</span>
                            <i class="fa fa-credit-card text-info mr-1"></i> Payment &amp; Settlement Setup
                        </h3>
                    </div>
                    <div class="expense-card-body">
                        <div>
                            <div class="row">
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Payment Status <span class="req-star">*</span></span>
                                    </label>
                                    <select name="payment_status" class="form-control-compact" required>
                                        <option value="paid" {{ old('payment_status', optional($first)->payment_status ?: 'paid') == 'paid' ? 'selected' : '' }}>Paid (Settled)</option>
                                        <option value="pending" {{ old('payment_status', optional($first)->payment_status) == 'pending' ? 'selected' : '' }}>Pending (Due)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Payment Mode <span class="req-star">*</span></span>
                                    </label>
                                    <select name="payment_mode_id" class="form-control-compact" required>
                                        <option value="">Select Payment Mode</option>
                                        @foreach($paymentModes as $mode)
                                            <option value="{{ $mode->id }}" {{ (string) old('payment_mode_id', optional($first)->payment_mode_id) === (string) $mode->id ? 'selected' : '' }}>
                                                {{ $mode->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Transaction / UTR / Cheque Ref</span>
                                    </label>
                                    <input type="text" name="payment_reference" class="form-control-compact" value="{{ old('payment_reference', optional($first)->payment_reference) }}" placeholder="e.g. UTR-982347234, CHQ-102938">
                                </div>
                                <div class="col-md-6 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Expense Type <span class="req-star">*</span></span>
                                    </label>
                                    <select name="expense_type" id="expense_type" class="form-control-compact" required>
                                        <option value="one_time" {{ old('expense_type', optional($first)->expense_type ?: 'one_time') == 'one_time' ? 'selected' : '' }}>One-Time</option>
                                        <option value="recurring" {{ old('expense_type', optional($first)->expense_type) == 'recurring' ? 'selected' : '' }}>Recurring Overhead</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row" id="frequency_wrap" style="display: {{ old('expense_type', optional($first)->expense_type) == 'recurring' ? 'flex' : 'none' }};">
                                <div class="col-md-12 form-group-compact">
                                    <label class="form-label-compact">
                                        <span>Recurring Frequency <span class="req-star">*</span></span>
                                    </label>
                                    <select name="recurring_frequency" class="form-control-compact">
                                        <option value="">Select Frequency</option>
                                        @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'half_yearly' => 'Half-Yearly', 'yearly' => 'Yearly'] as $key => $label)
                                            <option value="{{ $key }}" {{ old('recurring_frequency', optional($first)->recurring_frequency) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group-compact mb-0">
                            <label class="form-label-compact">
                                <span>Bill / Receipt Attachment <small class="text-muted">(JPG, PNG, PDF up to 4MB)</small></span>
                            </label>
                            <input type="file" name="attachment" class="form-control-compact" accept=".jpg,.jpeg,.png,.pdf" style="padding: 2px 4px;">
                            @if(optional($first)->attachment)
                                <div class="mt-1">
                                    <a target="_blank" href="{{ env('IMAGE_SHOW_PATH') . 'expense/' . $first->attachment }}" class="text-primary font-weight-bold" style="font-size: 11px;">
                                        <i class="fa fa-paperclip"></i> View Current Attachment ({{ $first->attachment }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- 3. Full-Width Card: Step 3 - Particulars Breakdown Table --}}
        <div class="particulars-table-card">
            <div class="expense-card-header">
                <h3 class="expense-card-title">
                    <span class="card-step">3</span>
                    <i class="fa fa-list-alt text-info mr-1"></i> Expense Particulars &amp; Cost Breakdown
                </h3>
                <button type="button" id="addRow" class="dash-btn dash-btn-primary" style="height: 24px; padding: 0 8px; font-size: 10.5px;">
                    <i class="fa fa-plus mr-1"></i> Add Line Item
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table class="particulars-table">
                    <thead>
                        <tr>
                            <th style="width: 35px;" class="text-center">#</th>
                            <th style="min-width: 220px;">Category <span class="text-warning">*</span></th>
                            <th style="min-width: 240px;">Particular / Item Description <span class="text-warning">*</span></th>
                            <th style="width: 110px;" class="text-right">Quantity <span class="text-warning">*</span></th>
                            <th style="width: 130px;" class="text-right">Rate (₹) <span class="text-warning">*</span></th>
                            <th style="width: 140px;" class="text-right">Amount (₹)</th>
                            <th style="width: 45px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="expenseRows">
                        @foreach($rows as $index => $row)
                        <tr class="expense-row" data-index="{{ $index }}">
                            <input type="hidden" name="id[]" value="{{ $row->id }}">
                            
                            <td class="text-center font-weight-bold text-muted serial-index">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <select name="category[]" class="table-input" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $id => $label)
                                        <option value="{{ $id }}" {{ (string) old("category.$index", $row->category_id) === (string) $id ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="text" name="name[]" class="table-input" value="{{ old("name.$index", $row->name) }}" placeholder="e.g. Printing Paper, Office Chairs, High-speed broadband..." required>
                            </td>

                            <td>
                                <input type="number" name="quantity[]" class="table-input quantity text-right" value="{{ old("quantity.$index", $row->quantity ?: 1) }}" min="0.01" step="0.01" required>
                            </td>

                            <td>
                                <input type="number" name="rate[]" class="table-input rate text-right" value="{{ old("rate.$index", $row->rate) }}" min="0" step="0.01" placeholder="0.00" required>
                            </td>

                            <td>
                                <input type="text" class="table-input table-input-readonly amount" value="{{ number_format((float) $row->amount, 2, '.', '') }}" readonly>
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn-remove-row remove-row" title="Remove Item">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-2" style="background: #fafbfc; border-top: 1px solid #e2e8f0;">
                <button type="button" id="addRowBottom" class="dash-btn" style="background: #f1f5f9; color: #002C54; border: 1px solid #cbd5e1; height: 26px; font-size: 11px;">
                    <i class="fa fa-plus mr-1"></i> Add Another Item
                </button>

                <div class="voucher-total-box">
                    <span class="voucher-total-label"><i class="fa fa-calculator mr-1"></i> Voucher Grand Total:</span>
                    <span class="voucher-total-val">₹<span id="grandTotal">0.00</span></span>
                </div>
            </div>
        </div>

        {{-- 4. Sticky Form Action Footer --}}
        <div class="expense-form-footer">
            <a href="{{ url('expenseView') }}" class="dash-btn" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                <i class="fa fa-times mr-1"></i> Cancel &amp; Back
            </a>

            <button type="submit" class="dash-btn" style="background: #002C54; color: #ffffff; padding: 0 16px;">
                <i class="fa fa-save mr-1"></i> {{ $editMode ? 'Update Expense Voucher' : 'Save Expense Voucher' }}
            </button>
        </div>

    </form>
</div>

{{-- Dynamic Row Template --}}
<template id="expenseRowTemplate">
    <tr class="expense-row">
        <input type="hidden" name="id[]" value="">
        <td class="text-center font-weight-bold text-muted serial-index"></td>
        <td>
            <select name="category[]" class="table-input" required>
                <option value="">Select Category</option>
                @foreach($categories as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" name="name[]" class="table-input" placeholder="e.g. Printing Paper, Office Chairs, High-speed broadband..." required>
        </td>
        <td>
            <input type="number" name="quantity[]" class="table-input quantity text-right" value="1" min="0.01" step="0.01" required>
        </td>
        <td>
            <input type="number" name="rate[]" class="table-input rate text-right" min="0" step="0.01" placeholder="0.00" required>
        </td>
        <td>
            <input type="text" class="table-input table-input-readonly amount" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn-remove-row remove-row" title="Remove Item">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsContainer = document.getElementById('expenseRows');
    const template = document.getElementById('expenseRowTemplate');
    const grandTotalDisplay = document.getElementById('grandTotal');
    const expenseTypeSelect = document.getElementById('expense_type');
    const frequencyWrap = document.getElementById('frequency_wrap');

    function updateCalculations() {
        let sum = 0;
        const allRows = rowsContainer.querySelectorAll('.expense-row');
        
        allRows.forEach((row, idx) => {
            const indexCell = row.querySelector('.serial-index');
            if (indexCell) indexCell.textContent = idx + 1;

            const qtyInput = row.querySelector('.quantity');
            const rateInput = row.querySelector('.rate');
            const amountInput = row.querySelector('.amount');

            const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;
            const lineAmt = qty * rate;

            if (amountInput) {
                amountInput.value = lineAmt.toFixed(2);
            }
            sum += lineAmt;
        });

        if (grandTotalDisplay) {
            grandTotalDisplay.textContent = sum.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    function toggleFrequency() {
        if (frequencyWrap && expenseTypeSelect) {
            frequencyWrap.style.display = expenseTypeSelect.value === 'recurring' ? 'flex' : 'none';
        }
    }

    function addRow() {
        const clone = template.content.cloneNode(true);
        rowsContainer.appendChild(clone);
        updateCalculations();
    }

    rowsContainer.addEventListener('input', function (e) {
        if (e.target.matches('.quantity, .rate')) {
            updateCalculations();
        }
    });

    rowsContainer.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-row');
        if (!removeBtn) return;
        
        if (rowsContainer.querySelectorAll('.expense-row').length > 1) {
            removeBtn.closest('.expense-row').remove();
            updateCalculations();
        } else {
            alert('A voucher must have at least one line item.');
        }
    });

    const addRowBtn = document.getElementById('addRow');
    const addRowBottomBtn = document.getElementById('addRowBottom');
    if (addRowBtn) addRowBtn.addEventListener('click', addRow);
    if (addRowBottomBtn) addRowBottomBtn.addEventListener('click', addRow);

    if (expenseTypeSelect) {
        expenseTypeSelect.addEventListener('change', toggleFrequency);
    }

    toggleFrequency();
    updateCalculations();
});
</script>
@endsection
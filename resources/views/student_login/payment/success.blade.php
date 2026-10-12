@php
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($student->ClassTypes)->name ?? '-';
    $admissionNo = $student->admissionNo ?? '-';
    $receiptNo = $invoice->invoice_no ?? ('REC-' . $order->id);
    $txnId = $order->phonepe_transaction_id ?? $order->merchant_order_id;
@endphp

@extends('student_login.layout.app')

@section('title', 'Payment Successful - Arise ERP')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE STUDENT PAYMENT RECEIPT (MOBILE THEME DESIGN SYSTEM)
   ========================================================================== */
.pay-receipt-wrapper {
    width: 100%;
    max-width: 460px;
    margin: 4px auto 16px auto;
    animation: paySlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes paySlideUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 1. Hero Status Card */
.pay-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #064e3b 100%);
    border: 1px solid rgba(56, 189, 248, 0.3);
    border-radius: 12px 12px 0 0;
    padding: 18px 16px 20px;
    color: #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.25);
}
.pay-hero-card::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, rgba(16, 185, 129, 0) 70%);
    pointer-events: none;
}
.pay-success-icon-wrap {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: 3px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 26px;
    margin-bottom: 10px;
    animation: payPulse 1.8s infinite;
}
@keyframes payPulse {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
    70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.pay-hero-title {
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
}
.pay-hero-subtitle {
    font-size: 11.5px;
    color: #93c5fd;
    font-weight: 500;
    margin-bottom: 10px;
}
.pay-trust-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0, 15, 30, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #6ee7b7;
    letter-spacing: 0.3px;
}
.pay-trust-chip i {
    color: #10b981;
}

/* 2. Receipt Voucher Ticket Card */
.pay-ticket-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 16px;
    box-shadow: 0 8px 24px rgba(0, 20, 40, 0.08);
    position: relative;
}

/* Amount Highlight Header */
.pay-amount-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
}
.pay-amount-label {
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
}
.pay-amount-val {
    font-size: 22px;
    font-weight: 800;
    color: #002C54;
    letter-spacing: -0.5px;
    margin-top: 2px;
}
.pay-badge-paid {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.5px;
}

/* Perforated Divider with Notches */
.ticket-divider {
    position: relative;
    margin: 14px -16px;
    border-top: 1.5px dashed #cbd5e1;
}
.ticket-notch-left,
.ticket-notch-right {
    position: absolute;
    top: -8px;
    width: 16px;
    height: 16px;
    background-color: var(--app-bg, #f8fafc);
    border-radius: 50%;
    border: 1px solid #cbd5e1;
    z-index: 2;
}
.ticket-notch-left {
    left: -8px;
    clip-path: polygon(50% 0%, 100% 0%, 100% 100%, 50% 100%);
}
.ticket-notch-right {
    right: -8px;
    clip-path: polygon(0% 0%, 50% 0%, 50% 100%, 0% 100%);
}

/* Metadata List */
.pay-details-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}
.pay-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11.5px;
    padding: 2px 0;
}
.pay-row .label {
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}
.pay-row .val-group {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    color: #0f172a;
    text-align: right;
}
.pay-copy-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    width: 22px;
    height: 22px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.pay-copy-btn:active {
    background: #002C54;
    color: #ffffff;
    transform: scale(0.92);
}
.mode-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #f5f3ff;
    color: #5f259f;
    border: 1px solid #ddd6fe;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 10.5px;
    font-weight: 700;
}

/* 3. Touch Actions Group */
.pay-actions-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.btn-pay-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    background: linear-gradient(135deg, #002C54 0%, #001f3f 100%);
    color: #ffffff !important;
    border: 1px solid #001f3f;
    padding: 11px 16px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 800;
    letter-spacing: 0.2px;
    text-decoration: none !important;
    box-shadow: 0 4px 12px rgba(0, 44, 84, 0.22);
    transition: all 0.18s ease;
    cursor: pointer;
}
.btn-pay-primary:active {
    transform: scale(0.98);
    opacity: 0.95;
}
.btn-pay-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    background: #0284c7;
    color: #ffffff !important;
    border: 1px solid #0284c7;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.18s ease;
    cursor: pointer;
}
.btn-pay-secondary:active {
    transform: scale(0.98);
}
.btn-pay-outline {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    background: #ffffff;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.18s ease;
    cursor: pointer;
}
.btn-pay-outline:hover {
    background: #f8fafc;
    border-color: #94a3b8;
}
.btn-pay-outline:active {
    transform: scale(0.98);
}

/* Toast Copy Feedback */
.pay-toast {
    position: fixed;
    bottom: calc(var(--bottom-nav-height, 52px) + 24px);
    left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #0f172a;
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 20px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.25);
    opacity: 0;
    pointer-events: none;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 6px;
}
.pay-toast.show {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}
.pay-toast i {
    color: #10b981;
}
</style>

<div class="pay-receipt-wrapper">

    {{-- 1. Top Hero Snapshot --}}
    <div class="pay-hero-card">
        <div class="pay-success-icon-wrap">
            <i class="fa fa-check"></i>
        </div>
        <h1 class="pay-hero-title">Payment Successful!</h1>
        <p class="pay-hero-subtitle">Official school fee recorded &amp; ledger updated instantly</p>
        <div class="pay-trust-chip">
            <i class="fa fa-shield"></i> PhonePe Verified &bull; Arise ERP Settled
        </div>
    </div>

    {{-- 2. Voucher / Digital Receipt Ticket Card --}}
    <div class="pay-ticket-card">

        {{-- Amount Paid Highlight --}}
        <div class="pay-amount-box">
            <div>
                <div class="pay-amount-label">Amount Paid</div>
                <div class="pay-amount-val">₹ {{ number_format($order->amount, 2) }}</div>
            </div>
            <div class="pay-badge-paid">
                <i class="fa fa-check-circle"></i> COMPLETED
            </div>
        </div>

        {{-- Perforated Notch Divider --}}
        <div class="ticket-divider">
            <div class="ticket-notch-left"></div>
            <div class="ticket-notch-right"></div>
        </div>

        {{-- Key Details --}}
        <div class="pay-details-list">
            @if(!empty($invoice->invoice_no))
                <div class="pay-row">
                    <span class="label"><i class="fa fa-file-text-o text-primary"></i> Receipt No:</span>
                    <div class="val-group">
                        <span style="color: #002C54;">{{ $invoice->invoice_no }}</span>
                        <button type="button" class="pay-copy-btn" onclick="copyReceiptText('{{ $invoice->invoice_no }}', 'Receipt No copied!')" title="Copy Receipt No">
                            <i class="fa fa-clone"></i>
                        </button>
                    </div>
                </div>
            @endif

            <div class="pay-row">
                <span class="label"><i class="fa fa-user text-muted"></i> Student Name:</span>
                <span class="val-group" style="text-transform: uppercase;">{{ $studentName }}</span>
            </div>

            <div class="pay-row">
                <span class="label"><i class="fa fa-id-card-o text-muted"></i> Admission No:</span>
                <span class="val-group">{{ $admissionNo }}</span>
            </div>

            <div class="pay-row">
                <span class="label"><i class="fa fa-graduation-cap text-muted"></i> Class:</span>
                <span class="val-group">{{ $className }}</span>
            </div>

            <div class="pay-row">
                <span class="label"><i class="fa fa-hashtag text-muted"></i> Txn Reference:</span>
                <div class="val-group">
                    <span style="font-family: monospace; font-size: 11px;">{{ $txnId }}</span>
                    <button type="button" class="pay-copy-btn" onclick="copyReceiptText('{{ $txnId }}', 'Transaction ID copied!')" title="Copy Txn ID">
                        <i class="fa fa-clone"></i>
                    </button>
                </div>
            </div>

            <div class="pay-row">
                <span class="label"><i class="fa fa-shield text-muted"></i> Payment Mode:</span>
                <span class="val-group">
                    <span class="mode-pill"><i class="fa fa-bolt"></i> PhonePe UPI</span>
                </span>
            </div>

            <div class="pay-row">
                <span class="label"><i class="fa fa-calendar-check-o text-muted"></i> Date &amp; Time:</span>
                <span class="val-group">{{ date('d M Y, h:i A', strtotime($order->updated_at)) }}</span>
            </div>
        </div>

        {{-- 3. Touch Ergonomic Actions --}}
        <div class="pay-actions-group">
            @if(!empty($order->invoice_id))
                <a href="{{ route('student.fees.receipt.download', ['invoice' => $order->invoice_id]) }}" class="btn-pay-primary">
                    <i class="fa fa-download"></i> Download Fee Receipt (PDF)
                </a>
                <a href="{{ route('student.fees.receipt.view', ['invoice' => $order->invoice_id]) }}" target="_blank" class="btn-pay-secondary">
                    <i class="fa fa-eye"></i> View Receipt Online
                </a>
            @endif

            <a href="{{ url('fees_history') }}" class="btn-pay-outline">
                <i class="fa fa-history"></i> Check Fees History &amp; Balance
            </a>

            <a href="{{ url('student/dashboard') }}" class="btn-pay-outline" style="border-style: dashed; background: #f8fafc;">
                <i class="fa fa-arrow-left"></i> Return to Student Dashboard
            </a>
        </div>

    </div>

</div>

{{-- Toast Notification --}}
<div id="payToast" class="pay-toast">
    <i class="fa fa-check-circle"></i>
    <span id="payToastMsg">Copied!</span>
</div>

<script>
function copyReceiptText(text, msg) {
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showPayToast(msg || 'Copied to clipboard!');
        }).catch(function() {
            legacyCopy(text, msg);
        });
    } else {
        legacyCopy(text, msg);
    }
}

function legacyCopy(text, msg) {
    var temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.select();
    try {
        document.execCommand('copy');
        showPayToast(msg || 'Copied to clipboard!');
    } catch(e) {}
    document.body.removeChild(temp);
}

function showPayToast(msg) {
    var toast = document.getElementById('payToast');
    var msgSpan = document.getElementById('payToastMsg');
    if (!toast) return;
    if (msgSpan) msgSpan.innerText = msg;
    toast.classList.add('show');
    setTimeout(function() {
        toast.classList.remove('show');
    }, 2200);
}
</script>
@endsection

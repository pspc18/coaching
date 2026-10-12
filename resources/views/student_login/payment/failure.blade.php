@php
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    if (empty($studentName)) $studentName = 'Student';
    $className = optional($student->ClassTypes)->name ?? '-';
    $admissionNo = $student->admissionNo ?? '-';
@endphp

@extends('student_login.layout.app')

@section('title', 'Payment Declined / Cancelled - Arise ERP')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE PAYMENT FAILURE / CANCELLED (MOBILE THEME)
   ========================================================================== */
.pay-failed-wrapper {
    width: 100%;
    max-width: 460px;
    margin: 4px auto 16px auto;
    animation: payFailSlide 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes payFailSlide {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 1. Hero Status Card */
.pay-fail-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #7f1d1d 100%);
    border: 1px solid rgba(248, 113, 113, 0.3);
    border-radius: 12px 12px 0 0;
    padding: 18px 16px 20px;
    color: #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.25);
}
.pay-fail-icon-wrap {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    border: 3px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 26px;
    margin-bottom: 10px;
}
.pay-fail-title {
    font-size: 18px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
}
.pay-fail-subtitle {
    font-size: 11.5px;
    color: #fca5a5;
    font-weight: 500;
    margin-bottom: 10px;
}
.pay-fail-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0, 15, 30, 0.55);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #fca5a5;
    letter-spacing: 0.3px;
}

/* 2. Ticket Voucher Card */
.pay-fail-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 16px;
    box-shadow: 0 8px 24px rgba(0, 20, 40, 0.08);
}
.pay-fail-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 14px;
}
.pay-fail-label {
    font-size: 10px;
    text-transform: uppercase;
    font-weight: 700;
    color: #991b1b;
    letter-spacing: 0.5px;
}
.pay-fail-val {
    font-size: 22px;
    font-weight: 800;
    color: #b91c1c;
    letter-spacing: -0.5px;
    margin-top: 2px;
}
.badge-declined {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 10.5px;
    font-weight: 800;
}
.pay-fail-reason-box {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 11.5px;
    color: #9a3412;
    margin-bottom: 14px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.4;
}
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
    font-weight: 700;
    color: #0f172a;
    text-align: right;
}
.btn-retry-primary {
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
    text-decoration: none !important;
    box-shadow: 0 4px 12px rgba(0, 44, 84, 0.22);
    cursor: pointer;
}
.btn-retry-primary:active {
    transform: scale(0.98);
}
.btn-action-outline {
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
}
.btn-action-outline:hover {
    background: #f8fafc;
}
</style>

<div class="pay-failed-wrapper">

    {{-- Hero Snapshot --}}
    <div class="pay-fail-hero">
        <div class="pay-fail-icon-wrap">
            <i class="fa fa-times"></i>
        </div>
        <h1 class="pay-fail-title">Payment Not Completed</h1>
        <p class="pay-fail-subtitle">No fee amount was deducted from your bank account</p>
        <div class="pay-fail-chip">
            <i class="fa fa-info-circle"></i> Transaction Cancelled or Interrupted
        </div>
    </div>

    {{-- Ticket Voucher Card --}}
    <div class="pay-fail-card">

        <div class="pay-fail-box">
            <div>
                <div class="pay-fail-label">Attempted Amount</div>
                <div class="pay-fail-val">₹ {{ number_format($order->amount, 2) }}</div>
            </div>
            <div class="badge-declined">
                <i class="fa fa-times-circle"></i> DECLINED
            </div>
        </div>

        <div class="pay-fail-reason-box">
            <i class="fa fa-exclamation-triangle" style="margin-top: 2px;"></i>
            <div>
                <strong>Reason:</strong> {{ $reason ?? ($order->failure_reason ?? 'Payment was cancelled or timed out by user/bank.') }}
            </div>
        </div>

        <div class="pay-details-list">
            <div class="pay-row">
                <span class="label"><i class="fa fa-user text-muted"></i> Student Name:</span>
                <span class="val-group" style="text-transform: uppercase;">{{ $studentName }}</span>
            </div>
            <div class="pay-row">
                <span class="label"><i class="fa fa-id-card-o text-muted"></i> Admission No:</span>
                <span class="val-group">{{ $admissionNo }}</span>
            </div>
            <div class="pay-row">
                <span class="label"><i class="fa fa-hashtag text-muted"></i> Order Reference:</span>
                <span class="val-group" style="font-family: monospace; font-size: 11px;">{{ $order->merchant_order_id }}</span>
            </div>
            <div class="pay-row">
                <span class="label"><i class="fa fa-calendar-times-o text-muted"></i> Attempt Date:</span>
                <span class="val-group">{{ date('d M Y, h:i A', strtotime($order->updated_at)) }}</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ url('student/dashboard') }}" class="btn-retry-primary">
                <i class="fa fa-refresh"></i> Retry Fee Payment from Dashboard
            </a>
            <a href="{{ url('fees_history') }}" class="btn-action-outline">
                <i class="fa fa-history"></i> Check Fees Ledger &amp; Due Balance
            </a>
        </div>

    </div>

</div>
@endsection

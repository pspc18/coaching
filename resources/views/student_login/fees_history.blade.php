@php
    $getUser = Helper::getUser();
    $totalFees = (float) ($summary['totalFees'] ?? 0);
    $discount = (float) ($summary['discount'] ?? 0);
    $netPayable = (float) ($summary['netPayable'] ?? max(0, $totalFees - $discount));
    $paidFees = (float) ($summary['paidFees'] ?? 0);
    $currentDue = (float) ($summary['currentSessionDue'] ?? 0);
    $finePaid = (float) ($summary['finePaid'] ?? 0);
    $paymentProgress = (int) ($summary['paymentProgress'] ?? ($netPayable > 0 ? min(100, round(($paidFees / $netPayable) * 100)) : 100));

    $studentAdmissionNo = $student->admissionNo ?? $getUser->admissionNo ?? Session::get('id');
    $className = optional($getUser->ClassTypes)->name ?? 'Student';
    $currentSessionName = Session::get('session_name') ?? date('Y');
@endphp
@extends('student_login.layout.app')

@section('title', 'Fees Ledger')
@section('page_title', 'FEES LEDGER')
@section('page_sub', Session::get('first_name').' · '.$className)

@section('content')
<div class="fees-portal-wrapper">

    {{-- 1. Context & Academic Session Switcher Bar --}}
    <div class="fees-top-bar">
        <div class="student-meta-pill">
            <span class="meta-dot"></span>
            <span class="meta-label">ID #{{ $studentAdmissionNo }}</span>
            <span class="meta-divider">•</span>
            <span class="meta-class">{{ $className }}</span>
        </div>

        @if(!empty($availableSessions) && $availableSessions->count() > 1)
            <div class="session-dropdown-wrap">
                <form action="{{ url('fees_history') }}" method="get" id="sessionSwitchForm">
                    <select name="session_id" class="session-select-pill" onchange="document.getElementById('sessionSwitchForm').submit();">
                        @foreach($availableSessions as $s)
                            <option value="{{ $s->id }}" {{ (int) $s->id === (int) $activeSessionId ? 'selected' : '' }}>
                                Session {{ $s->from_year }}-{{ $s->to_year }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        @else
            <div class="session-badge-pill">
                <i class="fa fa-calendar-check-o"></i> Session {{ $currentSessionName }}
            </div>
        @endif
    </div>

    {{-- 2. Executive Financial Hero Card --}}
    <div class="executive-ledger-hero">
        <div class="hero-watermark"><i class="fa fa-inr"></i></div>
        
        <div class="hero-top-row">
            <div>
                <span class="hero-kicker">Outstanding Balance</span>
                <h1 class="hero-balance-val">₹{{ number_format($currentDue, 2) }}</h1>
            </div>
            <div class="hero-status-wrap">
                @if($currentDue <= 0)
                    <span class="hero-status-pill pill-cleared">
                        <i class="fa fa-check-circle"></i> All Dues Cleared
                    </span>
                @else
                    <span class="hero-status-pill pill-due">
                        <i class="fa fa-clock-o"></i> Payment Pending
                    </span>
                @endif
            </div>
        </div>

        {{-- Visual Progress Meter --}}
        <div class="hero-meter-wrap">
            <div class="meter-labels">
                <span>Fee Clearance Progress</span>
                <strong>{{ $paymentProgress }}% Paid</strong>
            </div>
            <div class="meter-track">
                <div class="meter-fill" style="width: {{ $paymentProgress }}%;"></div>
            </div>
        </div>

        {{-- 4-Pillar Financial Metrics Grid --}}
        <div class="hero-metrics-grid">
            <div class="metric-cell">
                <span class="metric-lbl">Total Course Fee</span>
                <strong class="metric-val">₹{{ number_format($totalFees, 2) }}</strong>
            </div>
            <div class="metric-cell">
                <span class="metric-lbl">Scholarship / Disc.</span>
                <strong class="metric-val text-amber">₹{{ number_format($discount, 2) }}</strong>
            </div>
            <div class="metric-cell">
                <span class="metric-lbl">Net Payable</span>
                <strong class="metric-val">₹{{ number_format($netPayable, 2) }}</strong>
            </div>
            <div class="metric-cell">
                <span class="metric-lbl">Total Received</span>
                <strong class="metric-val text-emerald">₹{{ number_format($paidFees, 2) }}</strong>
            </div>
        </div>

        @if($finePaid > 0)
            <div class="hero-footnote">
                <i class="fa fa-info-circle"></i> Includes ₹{{ number_format($finePaid, 2) }} late fine payment.
            </div>
        @endif
    </div>

    {{-- 3. Segmented Navigation Tabs --}}
    <div class="ledger-segment-bar">
        <button type="button" class="seg-tab-btn active" data-target="tab-installments" onclick="switchLedgerTab('tab-installments', this)">
            <i class="fa fa-list-ul"></i>
            <span>Fee Heads & Installments</span>
            <span class="seg-count-badge">{{ $feeHeadLedger->count() }}</span>
        </button>

        <button type="button" class="seg-tab-btn" data-target="tab-receipts" onclick="switchLedgerTab('tab-receipts', this)">
            <i class="fa fa-file-text-o"></i>
            <span>Official Receipts</span>
            <span class="seg-count-badge">{{ $feeReceipts->count() }}</span>
        </button>
    </div>

    {{-- 4. Tab 1 Content: Fee Heads & Installments --}}
    <div class="ledger-tab-panel active" id="tab-installments">
        @forelse($feeHeadLedger as $index => $head)
            @php
                $isPaid = $head->due_amount <= 0;
                $isPartial = !$isPaid && ($head->paid_amount > 0 || $head->discount > 0);
                $statusType = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'unpaid');
                $statusLabel = $isPaid ? 'Paid' : ($isPartial ? 'Partially Paid' : 'Pending Due');
                $headProgress = $head->assigned_amount > 0 
                    ? min(100, round((($head->paid_amount + $head->discount) / $head->assigned_amount) * 100)) 
                    : 100;
            @endphp
            <div class="installment-card {{ $statusType }}" id="head-card-{{ $head->fees_group_id }}">
                {{-- Installment Card Header (Accordion Clickable) --}}
                <div class="inst-card-header" onclick="toggleInstAccordion({{ $head->fees_group_id }})">
                    <div class="inst-icon-wrap {{ $statusType }}">
                        @if($isPaid)
                            <i class="fa fa-check"></i>
                        @elseif($isPartial)
                            <i class="fa fa-hourglass-half"></i>
                        @else
                            <i class="fa fa-exclamation"></i>
                        @endif
                    </div>

                    <div class="inst-title-box">
                        <div class="inst-name-row">
                            <h3 class="inst-name">{{ $head->name }}</h3>
                            <span class="inst-status-badge {{ $statusType }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="inst-sub-row">
                            @if(!empty($head->due_date))
                                <span class="inst-due-date">
                                    <i class="fa fa-calendar-o"></i> Due by {{ date('d M Y', strtotime($head->due_date)) }}
                                </span>
                            @else
                                <span class="inst-due-date text-muted">
                                    <i class="fa fa-tag"></i> Fee Head #{{ $index + 1 }}
                                </span>
                            @endif

                            @if($head->payments->count() > 0)
                                <span class="inst-tx-counter">
                                    <i class="fa fa-exchange"></i> {{ $head->payments->count() }} {{ Str::plural('Transaction', $head->payments->count()) }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="inst-accordion-arrow" id="arrow-{{ $head->fees_group_id }}">
                        <i class="fa fa-chevron-down"></i>
                    </div>
                </div>

                {{-- Installment Figures Matrix --}}
                <div class="inst-metrics-matrix">
                    <div class="matrix-col">
                        <span class="matrix-lbl">Assigned</span>
                        <strong class="matrix-val">₹{{ number_format($head->assigned_amount, 2) }}</strong>
                    </div>
                    <div class="matrix-col">
                        <span class="matrix-lbl">Received</span>
                        <strong class="matrix-val text-emerald">₹{{ number_format($head->paid_amount, 2) }}</strong>
                    </div>
                    <div class="matrix-col">
                        <span class="matrix-lbl">Balance Due</span>
                        <strong class="matrix-val {{ $head->due_amount > 0 ? 'text-rose' : 'text-muted' }}">
                            ₹{{ number_format($head->due_amount, 2) }}
                        </strong>
                    </div>
                </div>

                {{-- Micro Progress Track --}}
                <div class="inst-micro-progress">
                    <div class="progress-bar-fill {{ $statusType }}" style="width: {{ $headProgress }}%;"></div>
                </div>

                {{-- Accordion Drawer (Expanded Payment History) --}}
                <div class="inst-drawer-content" id="drawer-{{ $head->fees_group_id }}">
                    <div class="drawer-inner">
                        <div class="drawer-header-strip">
                            <span>Payment Records & Receipts</span>
                            <small>{{ $head->payments->count() }} recorded</small>
                        </div>

                        @forelse($head->payments as $payment)
                            @php
                                $isPaymentSuccess = (int) $payment->status === 0;
                            @endphp
                            <div class="inst-payment-entry">
                                <div class="entry-icon {{ $isPaymentSuccess ? 'success' : 'pending' }}">
                                    <i class="fa {{ $isPaymentSuccess ? 'fa-check' : 'fa-clock-o' }}"></i>
                                </div>
                                <div class="entry-details">
                                    <div class="entry-top">
                                        <span class="entry-receipt-no">{{ $payment->receipt_no ? 'Receipt #'.$payment->receipt_no : 'Payment' }}</span>
                                        <span class="entry-amount">+₹{{ number_format((float) $payment->paid_amount, 2) }}</span>
                                    </div>
                                    <div class="entry-bottom">
                                        <span class="entry-date">
                                            <i class="fa fa-clock-o"></i> {{ !empty($payment->date) ? date('d M Y', strtotime($payment->date)) : '-' }}
                                        </span>
                                        <span class="entry-mode-badge">
                                            {{ $payment->payment_mode ?: 'Cash' }}
                                        </span>
                                    </div>
                                    @if((float) $payment->discount > 0 || (float) $payment->installment_fine > 0)
                                        <div class="entry-tags">
                                            @if((float) $payment->discount > 0)
                                                <span class="tag-disc"><i class="fa fa-tag"></i> Disc: ₹{{ number_format((float) $payment->discount, 2) }}</span>
                                            @endif
                                            @if((float) $payment->installment_fine > 0)
                                                <span class="tag-fine"><i class="fa fa-exclamation-triangle"></i> Fine: ₹{{ number_format((float) $payment->installment_fine, 2) }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="drawer-empty-state">
                                <i class="fa fa-file-text-o"></i>
                                <span>No payment transactions recorded for this head yet.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="portal-empty-card">
                <div class="empty-icon-circle"><i class="fa fa-calculator"></i></div>
                <h3>No Fee Structure Assigned</h3>
                <p>No fee heads or installment plans have been assigned for this academic session.</p>
            </div>
        @endforelse
    </div>

    {{-- 5. Tab 2 Content: Official Receipts --}}
    <div class="ledger-tab-panel" id="tab-receipts">
        @forelse($feeReceipts as $receipt)
            @php
                $isReceived = (int) $receipt->status === 0;
            @endphp
            <div class="official-receipt-card">
                <div class="receipt-card-top">
                    <div class="receipt-doc-icon">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                    <div class="receipt-info-box">
                        <div class="receipt-no-row">
                            <h4 class="receipt-no">Invoice #{{ $receipt->invoice_no ?: $receipt->id }}</h4>
                            <span class="receipt-status-pill {{ $isReceived ? 'received' : 'pending' }}">
                                <i class="fa {{ $isReceived ? 'fa-check-circle' : 'fa-hourglass' }}"></i>
                                {{ $isReceived ? 'Settled' : 'Processing' }}
                            </span>
                        </div>
                        <div class="receipt-meta-row">
                            <span class="receipt-date">
                                <i class="fa fa-calendar-check-o"></i> {{ !empty($receipt->payment_date) ? date('d M Y', strtotime($receipt->payment_date)) : '-' }}
                            </span>
                            <span class="receipt-mode-pill">
                                <i class="fa fa-credit-card"></i> {{ $receipt->payment_mode ?: 'Cash' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Amount & Fee Heads Breakdown --}}
                <div class="receipt-amount-strip">
                    <div class="amount-meta">
                        <span class="amt-lbl">Amount Received</span>
                        <h2 class="amt-num">₹{{ number_format((float) $receipt->amount, 2) }}</h2>
                    </div>
                    <div class="heads-covered-meta">
                        <span class="heads-lbl">Fee Heads</span>
                        <span class="heads-tags" title="{{ $receipt->fee_head_names ?: 'General Fee Payment' }}">
                            {{ $receipt->fee_head_names ?: 'Fee Payment' }}
                        </span>
                    </div>
                </div>

                @if((float) $receipt->discount > 0 || (float) $receipt->total_fine > 0)
                    <div class="receipt-adj-row">
                        @if((float) $receipt->discount > 0)
                            <span class="adj-badge disc">
                                <i class="fa fa-tag"></i> Scholarship/Discount: ₹{{ number_format((float) $receipt->discount, 2) }}
                            </span>
                        @endif
                        @if((float) $receipt->total_fine > 0)
                            <span class="adj-badge fine">
                                <i class="fa fa-exclamation-circle"></i> Late Fine: ₹{{ number_format((float) $receipt->total_fine, 2) }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Dual Action Buttons (View Receipt & Download PDF) --}}
                <div class="receipt-action-row">
                    <a href="{{ route('student.fees.receipt.view', $receipt->id) }}" target="_blank" rel="noopener" class="btn-receipt-action btn-view-rcpt">
                        <i class="fa fa-eye"></i> View Receipt
                    </a>
                    <a href="{{ route('student.fees.receipt.download', $receipt->id) }}" class="btn-receipt-action btn-download-rcpt">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="portal-empty-card">
                <div class="empty-icon-circle"><i class="fa fa-file-text-o"></i></div>
                <h3>No Payment Receipts</h3>
                <p>Official tax invoices and payment vouchers will appear here once payment is verified.</p>
            </div>
        @endforelse
    </div>

    {{-- 6. Coaching Accounts Guidance Notice --}}
    <div class="accounts-support-notice">
        <div class="notice-icon"><i class="fa fa-shield"></i></div>
        <div class="notice-text">
            <strong>Accounts & Payment Support</strong>
            <p>For fee adjustments, installment extensions, or payment queries, please visit the Center Accounts Desk or submit a request via Student Helpdesk.</p>
        </div>
    </div>

</div>

{{-- Responsive Styles matching ARISE Native Mobile Theme --}}
<style>
/* -------------------------------------------------------------------------
   ARISE ERP - NATIVE STUDENT FEES PORTAL (COACHING STANDARD)
   Palette: Navy #001833 -> #002C54, Emerald #10b981, Amber #f59e0b, Slate #64748b
------------------------------------------------------------------------- */
.fees-portal-wrapper {
    max-width: 840px;
    margin: 0 auto;
    padding: 12px 14px 40px;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: #1e293b;
    box-sizing: border-box;
}

/* 1. Context & Session Switcher */
.fees-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
}
.student-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #e2e8f0;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
}
.meta-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #0284c7;
}
.meta-divider {
    color: #94a3b8;
}
.session-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #0284c7;
    color: #ffffff;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .2px;
}
.session-select-pill {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    outline: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}

/* 2. Executive Financial Hero Card */
.executive-ledger-hero {
    position: relative;
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 16px 18px;
    margin-bottom: 14px;
    box-shadow: 0 10px 25px -5px rgba(0, 44, 84, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.12);
    overflow: hidden;
}
.hero-watermark {
    position: absolute;
    right: -10px;
    bottom: -20px;
    font-size: 140px;
    color: rgba(255, 255, 255, 0.035);
    pointer-events: none;
    line-height: 1;
}
.hero-top-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}
.hero-kicker {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.65);
    margin-bottom: 2px;
}
.hero-balance-val {
    margin: 0;
    font-size: 26px;
    font-weight: 800;
    line-height: 1.15;
    color: #ffffff;
    letter-spacing: -0.02em;
}
.hero-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.hero-status-pill.pill-cleared {
    background: rgba(16, 185, 129, 0.22);
    color: #a7f3d0;
    border: 1px solid rgba(16, 185, 129, 0.45);
}
.hero-status-pill.pill-due {
    background: rgba(239, 68, 68, 0.22);
    color: #fca5a5;
    border: 1px solid rgba(239, 68, 68, 0.45);
}

/* Hero Progress Track */
.hero-meter-wrap {
    margin-bottom: 14px;
}
.meter-labels {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    color: rgba(255, 255, 255, 0.72);
    margin-bottom: 5px;
}
.meter-labels strong {
    color: #38bdf8;
    font-weight: 700;
}
.meter-track {
    width: 100%;
    height: 7px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 999px;
    overflow: hidden;
}
.meter-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    border-radius: 999px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 4-Pillar Financial Metrics Grid */
.hero-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}
.metric-cell {
    min-width: 0;
}
.metric-lbl {
    display: block;
    font-size: 9.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: rgba(255, 255, 255, 0.62);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 2px;
}
.metric-val {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.metric-val.text-emerald {
    color: #34d399 !important;
}
.metric-val.text-amber {
    color: #fbbf24 !important;
}
.hero-footnote {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px dashed rgba(255, 255, 255, 0.12);
    font-size: 10.5px;
    color: rgba(255, 255, 255, 0.7);
}

/* 3. Segmented Navigation Tabs */
.ledger-segment-bar {
    display: flex;
    gap: 8px;
    background: #e2e8f0;
    padding: 4px;
    border-radius: 12px;
    margin-bottom: 14px;
}
.seg-tab-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 10px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}
.seg-tab-btn.active {
    background: #ffffff;
    color: #002C54;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.seg-tab-btn:hover:not(.active) {
    color: #0f172a;
}
.seg-count-badge {
    padding: 1px 6px;
    border-radius: 999px;
    background: #cbd5e1;
    color: #1e293b;
    font-size: 10px;
    font-weight: 800;
}
.seg-tab-btn.active .seg-count-badge {
    background: #002C54;
    color: #ffffff;
}

/* Tabs Visibility */
.ledger-tab-panel {
    display: none;
}
.ledger-tab-panel.active {
    display: block;
    animation: fadeIn 0.25s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

/* 4. Tab 1: Installment Cards */
.installment-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.installment-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.inst-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    cursor: pointer;
    user-select: none;
}
.inst-icon-wrap {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
.inst-icon-wrap.paid {
    background: #dcfce7;
    color: #16a34a;
}
.inst-icon-wrap.partial {
    background: #fef3c7;
    color: #d97706;
}
.inst-icon-wrap.unpaid {
    background: #fee2e2;
    color: #dc2626;
}

.inst-title-box {
    flex: 1;
    min-width: 0;
}
.inst-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 2px;
}
.inst-name {
    margin: 0;
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.inst-status-badge {
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2px;
    white-space: nowrap;
}
.inst-status-badge.paid {
    background: #dcfce7;
    color: #15803d;
}
.inst-status-badge.partial {
    background: #fef3c7;
    color: #b45309;
}
.inst-status-badge.unpaid {
    background: #fee2e2;
    color: #b91c1c;
}
.inst-sub-row {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 11px;
    color: #64748b;
}
.inst-due-date {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.inst-tx-counter {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #0284c7;
    font-weight: 600;
}
.inst-accordion-arrow {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 12px;
    transition: transform 0.25s ease;
}
.inst-accordion-arrow.open {
    transform: rotate(180deg);
}

/* Installment Figures Matrix */
.inst-metrics-matrix {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    padding: 10px 14px;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}
.matrix-col {
    text-align: center;
}
.matrix-col:not(:last-child) {
    border-right: 1px solid #e2e8f0;
}
.matrix-lbl {
    display: block;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.matrix-val {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #1e293b;
}
.matrix-val.text-emerald {
    color: #16a34a !important;
}
.matrix-val.text-rose {
    color: #dc2626 !important;
}

/* Micro Progress */
.inst-micro-progress {
    height: 3px;
    background: #e2e8f0;
    width: 100%;
}
.progress-bar-fill {
    height: 100%;
    transition: width 0.3s ease;
}
.progress-bar-fill.paid {
    background: #16a34a;
}
.progress-bar-fill.partial {
    background: #f59e0b;
}
.progress-bar-fill.unpaid {
    background: #ef4444;
}

/* Accordion Drawer */
.inst-drawer-content {
    display: none;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
}
.inst-drawer-content.show {
    display: block;
}
.drawer-inner {
    padding: 12px 14px;
}
.drawer-header-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #475569;
    margin-bottom: 8px;
}
.inst-payment-entry {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}
.inst-payment-entry:last-child {
    border-bottom: none;
}
.entry-icon {
    width: 26px;
    height: 26px;
    min-width: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}
.entry-icon.success {
    background: #dcfce7;
    color: #16a34a;
}
.entry-icon.pending {
    background: #fef3c7;
    color: #d97706;
}
.entry-details {
    flex: 1;
    min-width: 0;
}
.entry-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 2px;
}
.entry-receipt-no {
    font-weight: 700;
    color: #0f172a;
}
.entry-amount {
    font-weight: 800;
    color: #16a34a;
}
.entry-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10.5px;
    color: #64748b;
}
.entry-mode-badge {
    padding: 1px 6px;
    border-radius: 4px;
    background: #f1f5f9;
    font-weight: 600;
    color: #475569;
}
.entry-tags {
    display: flex;
    gap: 6px;
    margin-top: 4px;
}
.tag-disc {
    font-size: 9.5px;
    color: #d97706;
    background: #fffbeb;
    padding: 1px 5px;
    border-radius: 4px;
}
.tag-fine {
    font-size: 9.5px;
    color: #dc2626;
    background: #fef2f2;
    padding: 1px 5px;
    border-radius: 4px;
}
.drawer-empty-state {
    padding: 14px 0;
    text-align: center;
    color: #94a3b8;
    font-size: 11.5px;
}

/* 5. Tab 2: Official Receipts Cards */
.official-receipt-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.receipt-card-top {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}
.receipt-doc-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.receipt-info-box {
    flex: 1;
    min-width: 0;
}
.receipt-no-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 2px;
}
.receipt-no {
    margin: 0;
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
}
.receipt-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
}
.receipt-status-pill.received {
    background: #dcfce7;
    color: #15803d;
}
.receipt-status-pill.pending {
    background: #fef3c7;
    color: #b45309;
}
.receipt-meta-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    color: #64748b;
}
.receipt-mode-pill {
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 4px;
    font-weight: 600;
    color: #475569;
}

/* Receipt Amount Strip */
.receipt-amount-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 10px;
}
.amt-lbl {
    display: block;
    font-size: 9.5px;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.amt-num {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #16a34a;
    line-height: 1.1;
}
.heads-covered-meta {
    max-width: 55%;
    text-align: right;
}
.heads-lbl {
    display: block;
    font-size: 9.5px;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 2px;
}
.heads-tags {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.receipt-adj-row {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.adj-badge {
    font-size: 10px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 4px;
}
.adj-badge.disc {
    background: #fffbeb;
    color: #b45309;
}
.adj-badge.fine {
    background: #fef2f2;
    color: #b91c1c;
}

/* Dual Actions */
.receipt-action-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.btn-receipt-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-view-rcpt {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}
.btn-view-rcpt:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.btn-download-rcpt {
    background: #002C54;
    color: #ffffff;
    border: 1px solid #002C54;
}
.btn-download-rcpt:hover {
    background: #001f3d;
    color: #ffffff;
}

/* 6. Empty States */
.portal-empty-card {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    padding: 30px 16px;
    text-align: center;
    color: #64748b;
    margin-bottom: 14px;
}
.empty-icon-circle {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.portal-empty-card h3 {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
}
.portal-empty-card p {
    margin: 0;
    font-size: 11.5px;
    color: #64748b;
}

/* 7. Accounts Support Notice */
.accounts-support-notice {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    margin-top: 16px;
}
.notice-icon {
    font-size: 16px;
    color: #0284c7;
    margin-top: 1px;
}
.notice-text strong {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 2px;
}
.notice-text p {
    margin: 0;
    font-size: 11px;
    color: #64748b;
    line-height: 1.45;
}

/* Mobile Adjustments (under 480px) */
@media (max-width: 480px) {
    .fees-portal-wrapper {
        padding: 8px 10px 36px;
    }
    .hero-balance-val {
        font-size: 22px;
    }
    .hero-metrics-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 8px;
    }
    .inst-card-header {
        padding: 10px 12px;
    }
    .inst-name {
        font-size: 12.5px;
    }
    .matrix-val {
        font-size: 11.5px;
    }
    .receipt-action-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function switchLedgerTab(targetId, btn) {
    document.querySelectorAll('.seg-tab-btn').forEach(function(b) {
        b.classList.remove('active');
    });
    document.querySelectorAll('.ledger-tab-panel').forEach(function(p) {
        p.classList.remove('active');
    });

    btn.classList.add('active');
    const targetPanel = document.getElementById(targetId);
    if (targetPanel) {
        targetPanel.classList.add('active');
    }
}

function toggleInstAccordion(groupId) {
    const drawer = document.getElementById('drawer-' + groupId);
    const arrow = document.getElementById('arrow-' + groupId);
    if (!drawer) return;

    const isShown = drawer.classList.contains('show');
    if (isShown) {
        drawer.classList.remove('show');
        if (arrow) arrow.classList.remove('open');
    } else {
        drawer.classList.add('show');
        if (arrow) arrow.classList.add('open');
    }
}
</script>
@endsection

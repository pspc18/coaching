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

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE FEES LEDGER
   Exact Layout, Typography & CSS matching admissionView?layout=mobile
   ========================================================================== */

/* 1. Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
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
.mob-session-pill {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* 4-Column Metrics Glance (2x2 Grid) */
.mob-dual-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.mob-radar-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-radar-tag {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-radar-val {
    font-size: 16px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 1px 0;
}
.mob-radar-meta {
    font-size: 9px;
    color: #cbd5e1;
    font-weight: 600;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Progress Strip */
.mob-hero-progress-strip {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.progress-meta-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-bottom: 4px;
}
.progress-meta-row b {
    color: #38bdf8;
    font-weight: 800;
}
.progress-track-bg {
    width: 100%;
    height: 5px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 2px;
    overflow: hidden;
}
.progress-track-fill {
    height: 100%;
    background: #10b981;
    border-radius: 2px;
    transition: width .4s ease;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 32px;
    padding: 0 10px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-act-btn-add {
    background: #0284c7;
    color: #ffffff !important;
}
.mob-act-btn-filter {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-add:active, .mob-act-btn-filter:active {
    transform: scale(0.96);
}

/* 2. Compact Search & Segment Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 5px 6px;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-filter-btn {
    flex: 1;
    height: 30px;
    padding: 0 8px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    white-space: nowrap;
    transition: all .12s ease;
}
.mob-filter-btn.active-filter {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-filter-btn:active {
    transform: scale(0.96);
}
.mob-session-select {
    height: 30px;
    padding: 0 6px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    outline: none;
    cursor: pointer;
}

/* 3. Feed List & Card Item */
.student-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 24px;
}
.student-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.student-mob-card:active {
    border-color: #94a3b8;
}

/* Status Accents matching admissionView */
.card-paid {
    border-left: 3px solid #16a34a;
}
.card-partial {
    border-left: 3px solid #f59e0b;
}
.card-due {
    border-left: 3px solid #ef4444;
}
.card-receipt {
    border-left: 3px solid #0284c7;
}

.student-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
}
.student-avatar-box.avatar-paid {
    background: #dcfce7;
    color: #16a34a;
    border-color: #86efac;
}
.student-avatar-box.avatar-partial {
    background: #fef3c7;
    color: #d97706;
    border-color: #fde68a;
}
.student-avatar-box.avatar-due {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fca5a5;
}

.student-header-info {
    flex: 1;
    overflow: hidden;
}
.student-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.student-mob-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 1px;
}
.student-adm-badge {
    font-size: 9px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.student-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}
.student-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    white-space: nowrap;
}
.status-badge-active {
    background: #dcfce7;
    color: #16a34a;
}
.status-badge-warning {
    background: #fef3c7;
    color: #b45309;
}
.status-badge-inactive {
    background: #fee2e2;
    color: #dc2626;
}

/* Card Details Grid matching .student-meta-grid */
.student-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10.5px;
    color: #334155;
    margin-bottom: 8px;
}
.student-meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.student-meta-item > i {
    width: 13px;
    font-size: 10.5px;
    color: #64748b;
    flex-shrink: 0;
    text-align: center;
}

/* Card Actions Row matching .student-card-actions */
.student-card-actions {
    display: flex;
    gap: 5px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-action {
    flex: 1;
    height: 29px;
    padding: 0 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-btn-action:active {
    transform: scale(0.96);
}
.btn-act-profile {
    background: #002C54;
    color: #ffffff !important;
}
.btn-act-fee {
    background: #16a34a;
    color: #ffffff !important;
}
.btn-act-edit {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155 !important;
}
.btn-act-id {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0284c7 !important;
    flex: 0 0 29px;
}

/* Expandable Transactions Drawer inside Card */
.tx-drawer-box {
    display: none;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px dashed #cbd5e1;
    background: #f8fafc;
    border-radius: 3px;
    padding: 8px;
}
.tx-drawer-box.show {
    display: block;
}
.tx-item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 10.5px;
}
.tx-item-row:last-child {
    border-bottom: none;
}
.tx-receipt-badge {
    font-weight: 800;
    color: #002C54;
}
.tx-amount-badge {
    font-weight: 800;
    color: #16a34a;
}

/* Tab Panels */
.tab-content-panel {
    display: none;
}
.tab-content-panel.active {
    display: block;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card matching admissionView --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-inr text-warning"></i> Student Fees Ledger
        </div>
        <div class="mob-session-pill">
            Session {{ $currentSessionName }}
        </div>
    </div>

    {{-- Symmetrical Metrics Grid matching .mob-dual-grid --}}
    <div class="mob-dual-grid">
        {{-- Radar 1: Outstanding Due --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-clock-o {{ $currentDue > 0 ? 'text-danger' : 'text-success' }}"></i> Outstanding Due
            </span>
            <div class="mob-radar-val" style="color: {{ $currentDue > 0 ? '#f87171' : '#34d399' }};">
                ₹{{ number_format($currentDue, 2) }}
            </div>
            <span class="mob-radar-meta">
                {{ $currentDue <= 0 ? 'All dues cleared ✓' : 'Payment pending' }}
            </span>
        </div>

        {{-- Radar 2: Total Received --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-check-circle text-success"></i> Received Fees
            </span>
            <div class="mob-radar-val" style="color: #34d399;">
                ₹{{ number_format($paidFees, 2) }}
            </div>
            <span class="mob-radar-meta">
                {{ $paymentProgress }}% of net course fee
            </span>
        </div>

        {{-- Radar 3: Total Course Fee --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-calculator text-primary"></i> Assigned Fee
            </span>
            <div class="mob-radar-val">
                ₹{{ number_format($totalFees, 2) }}
            </div>
            <span class="mob-radar-meta">
                Net: ₹{{ number_format($netPayable, 2) }}
            </span>
        </div>

        {{-- Radar 4: Scholarship / Concession --}}
        <div class="mob-radar-col">
            <span class="mob-radar-tag">
                <i class="fa fa-tag text-warning"></i> Scholarship/Disc.
            </span>
            <div class="mob-radar-val" style="color: #fbbf24;">
                ₹{{ number_format($discount, 2) }}
            </div>
            <span class="mob-radar-meta">
                @if($finePaid > 0)
                    +₹{{ number_format($finePaid, 2) }} Late Fine
                @else
                    No fine added
                @endif
            </span>
        </div>
    </div>

    {{-- Fee Progress Strip inside Hero --}}
    <div class="mob-hero-progress-strip">
        <div class="progress-meta-row">
            <span>Course Fee Clearance</span>
            <b>{{ $paymentProgress }}% Cleared</b>
        </div>
        <div class="progress-track-bg">
            <div class="progress-track-fill" style="width: {{ $paymentProgress }}%;"></div>
        </div>
    </div>

    {{-- Fast Action Bar matching .mob-actions-bar --}}
    <div class="mob-actions-bar">
        <button type="button" class="mob-act-btn mob-act-btn-add" onclick="switchFeeTab('installments', document.getElementById('btnTabInstallments'))">
            <i class="fa fa-list-ol mr-1"></i> Installments ({{ $feeHeadLedger->count() }})
        </button>
        <button type="button" class="mob-act-btn mob-act-btn-filter" onclick="switchFeeTab('receipts', document.getElementById('btnTabReceipts'))">
            <i class="fa fa-file-text-o mr-1"></i> Receipts ({{ $feeReceipts->count() }})
        </button>
    </div>
</div>

{{-- 2. Compact Segment Toolbar matching .mob-search-toolbar --}}
<div class="mob-search-toolbar">
    <button type="button" class="mob-filter-btn active-filter" id="btnTabInstallments" onclick="switchFeeTab('installments', this)">
        <i class="fa fa-list-ol"></i> Installments ({{ $feeHeadLedger->count() }})
    </button>
    <button type="button" class="mob-filter-btn" id="btnTabReceipts" onclick="switchFeeTab('receipts', this)">
        <i class="fa fa-file-text-o"></i> Receipts ({{ $feeReceipts->count() }})
    </button>

    @if(!empty($availableSessions) && $availableSessions->count() > 1)
        <select class="mob-session-select" onchange="window.location.href='{{ url('fees_history') }}?session_id='+this.value">
            @foreach($availableSessions as $s)
                <option value="{{ $s->id }}" {{ (int) $s->id === (int) $activeSessionId ? 'selected' : '' }}>
                    {{ $s->from_year }}-{{ $s->to_year }}
                </option>
            @endforeach
        </select>
    @endif
</div>

{{-- 3. TAB 1: Fee Heads & Installments Feed List --}}
<div class="tab-content-panel active" id="panel-installments">
    <div class="student-feed-list">
        @forelse($feeHeadLedger as $index => $head)
            @php
                $isPaid = $head->due_amount <= 0;
                $isPartial = !$isPaid && ($head->paid_amount > 0 || $head->discount > 0);
                $cardStatusClass = $isPaid ? 'card-paid' : ($isPartial ? 'card-partial' : 'card-due');
                $avatarStatusClass = $isPaid ? 'avatar-paid' : ($isPartial ? 'avatar-partial' : 'avatar-due');
                $badgeStatusClass = $isPaid ? 'status-badge-active' : ($isPartial ? 'status-badge-warning' : 'status-badge-inactive');
                $statusText = $isPaid ? 'PAID' : ($isPartial ? 'PARTIAL' : 'DUE');
            @endphp

            <div class="student-mob-card {{ $cardStatusClass }}">
                {{-- Header Row matching admissionView --}}
                <div class="student-card-header">
                    <div class="student-avatar-box {{ $avatarStatusClass }}">
                        @if($isPaid)
                            <i class="fa fa-check"></i>
                        @elseif($isPartial)
                            <i class="fa fa-hourglass-half"></i>
                        @else
                            <i class="fa fa-exclamation"></i>
                        @endif
                    </div>
                    <div class="student-header-info">
                        <div class="student-name-row">
                            <span class="student-mob-name">{{ $head->name }}</span>
                            <span class="student-status-badge {{ $badgeStatusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="student-pills-wrap">
                            <span class="student-adm-badge">Head #{{ $head->fees_group_id }}</span>
                            <span class="student-class-badge">
                                <i class="fa fa-calendar-o"></i> {{ !empty($head->due_date) ? 'Due: '.date('d M Y', strtotime($head->due_date)) : 'No Due Date' }}
                            </span>
                            @if($head->payments->count() > 0)
                                <span style="font-size: 8.5px; color: #0284c7; font-weight: 700;">
                                    &bull; {{ $head->payments->count() }} Payments
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2-Column Meta Grid matching admissionView --}}
                <div class="student-meta-grid">
                    <div class="student-meta-item">
                        <i class="fa fa-calculator"></i>
                        <span>Assigned: <b>₹{{ number_format($head->assigned_amount, 2) }}</b></span>
                    </div>
                    <div class="student-meta-item">
                        <i class="fa fa-check text-success"></i>
                        <span>Received: <b class="text-success">₹{{ number_format($head->paid_amount, 2) }}</b></span>
                    </div>
                    <div class="student-meta-item">
                        <i class="fa fa-clock-o {{ $head->due_amount > 0 ? 'text-danger' : '' }}"></i>
                        <span>Due: <b class="{{ $head->due_amount > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($head->due_amount, 2) }}</b></span>
                    </div>
                    <div class="student-meta-item">
                        <i class="fa fa-tag text-warning"></i>
                        <span>Disc: <b>₹{{ number_format($head->discount, 2) }}</b></span>
                    </div>
                </div>

                {{-- Action Row matching admissionView --}}
                <div class="student-card-actions">
                    <button type="button" class="mob-btn-action btn-act-edit" onclick="toggleTxDrawer({{ $head->fees_group_id }})">
                        <i class="fa fa-list-alt"></i> Payments ({{ $head->payments->count() }}) <i class="fa fa-chevron-down" id="drawer-arrow-{{ $head->fees_group_id }}" style="font-size: 9px; margin-left: 2px;"></i>
                    </button>
                </div>

                {{-- Expandable Transactions Drawer --}}
                <div class="tx-drawer-box" id="tx-drawer-{{ $head->fees_group_id }}">
                    <div style="font-size: 9.5px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 5px;">
                        Transaction History ({{ $head->payments->count() }})
                    </div>
                    @forelse($head->payments as $payment)
                        <div class="tx-item-row">
                            <div>
                                <span class="tx-receipt-badge">{{ $payment->receipt_no ? 'Receipt #'.$payment->receipt_no : 'Payment' }}</span>
                                <span style="color: #64748b; margin-left: 4px;">{{ !empty($payment->date) ? date('d M Y', strtotime($payment->date)) : '-' }}</span>
                                <span style="font-size: 8.5px; background: #e2e8f0; color: #334155; padding: 1px 4px; border-radius: 2px; margin-left: 4px;">
                                    {{ $payment->payment_mode ?: 'Cash' }}
                                </span>
                            </div>
                            <span class="tx-amount-badge">+₹{{ number_format((float) $payment->paid_amount, 2) }}</span>
                        </div>
                    @empty
                        <div style="font-size: 10px; color: #94a3b8; text-align: center; padding: 4px 0;">
                            No payment transactions recorded for this head.
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted" style="background: #fff; border-radius: 4px; border: 1px solid #cbd5e1; padding: 24px 12px;">
                <i class="fa fa-calculator fa-3x text-muted mb-2 d-block" style="opacity: 0.5;"></i>
                <div style="font-size: 13px; font-weight: 700; color: #475569;">No fee heads assigned</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Fee installment plan is not assigned for this session.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- 4. TAB 2: Official Receipts Feed List --}}
<div class="tab-content-panel" id="panel-receipts">
    <div class="student-feed-list">
        @forelse($feeReceipts as $receipt)
            @php
                $isReceived = (int) $receipt->status === 0;
            @endphp
            <div class="student-mob-card card-receipt">
                {{-- Header Row --}}
                <div class="student-card-header">
                    <div class="student-avatar-box">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                    <div class="student-header-info">
                        <div class="student-name-row">
                            <span class="student-mob-name">Receipt #{{ $receipt->invoice_no ?: $receipt->id }}</span>
                            <span class="student-status-badge {{ $isReceived ? 'status-badge-active' : 'status-badge-warning' }}">
                                {{ $isReceived ? 'SETTLED' : 'PENDING' }}
                            </span>
                        </div>
                        <div class="student-pills-wrap">
                            <span class="student-adm-badge">
                                <i class="fa fa-calendar-check-o"></i> {{ !empty($receipt->payment_date) ? date('d M Y', strtotime($receipt->payment_date)) : '-' }}
                            </span>
                            <span class="student-class-badge">
                                <i class="fa fa-credit-card"></i> {{ $receipt->payment_mode ?: 'Cash' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 2-Column Meta Grid --}}
                <div class="student-meta-grid">
                    <div class="student-meta-item">
                        <i class="fa fa-inr text-success"></i>
                        <span>Paid: <b class="text-success" style="font-size: 12.5px;">₹{{ number_format((float) $receipt->amount, 2) }}</b></span>
                    </div>
                    <div class="student-meta-item">
                        <i class="fa fa-tag text-primary"></i>
                        <span>Heads: <b>{{ Str::limit($receipt->fee_head_names ?: 'Fee Payment', 18) }}</b></span>
                    </div>
                    @if((float) $receipt->discount > 0)
                        <div class="student-meta-item">
                            <i class="fa fa-percent text-warning"></i>
                            <span>Disc: <b>₹{{ number_format((float) $receipt->discount, 2) }}</b></span>
                        </div>
                    @endif
                    @if((float) $receipt->total_fine > 0)
                        <div class="student-meta-item">
                            <i class="fa fa-exclamation-triangle text-danger"></i>
                            <span>Fine: <b>₹{{ number_format((float) $receipt->total_fine, 2) }}</b></span>
                        </div>
                    @endif
                </div>

                {{-- Action Row matching admissionView --}}
                <div class="student-card-actions">
                    <a href="{{ route('student.fees.receipt.view', $receipt->id) }}" target="_blank" rel="noopener" class="mob-btn-action btn-act-profile">
                        <i class="fa fa-eye"></i> View Receipt
                    </a>
                    <a href="{{ route('student.fees.receipt.download', $receipt->id) }}" class="mob-btn-action btn-act-fee">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted" style="background: #fff; border-radius: 4px; border: 1px solid #cbd5e1; padding: 24px 12px;">
                <i class="fa fa-file-text-o fa-3x text-muted mb-2 d-block" style="opacity: 0.5;"></i>
                <div style="font-size: 13px; font-weight: 700; color: #475569;">No receipts available</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Payment receipts will be issued once payment is recorded.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- 5. Bottom Accounts Guidance Bar --}}
<div class="student-mob-card" style="margin-bottom: 24px; background: #f8fafc; border: 1px solid #cbd5e1;">
    <div style="display: flex; align-items: flex-start; gap: 8px;">
        <i class="fa fa-info-circle text-primary" style="font-size: 14px; margin-top: 2px;"></i>
        <div style="font-size: 11px; color: #475569; line-height: 1.4;">
            <b style="color: #002C54;">Accounts Support:</b> For fee concessions, installment adjustments, or receipt updates, please contact the center accounts desk.
        </div>
    </div>
</div>

<script>
function switchFeeTab(tabKey, btn) {
    document.querySelectorAll('.mob-filter-btn').forEach(function(b) {
        b.classList.remove('active-filter');
    });
    if (btn) btn.classList.add('active-filter');

    document.querySelectorAll('.tab-content-panel').forEach(function(p) {
        p.classList.remove('active');
    });

    const targetPanel = document.getElementById('panel-' + tabKey);
    if (targetPanel) {
        targetPanel.classList.add('active');
    }
}

function toggleTxDrawer(headId) {
    const drawer = document.getElementById('tx-drawer-' + headId);
    const arrow = document.getElementById('drawer-arrow-' + headId);
    if (!drawer) return;

    const isShown = drawer.classList.contains('show');
    if (isShown) {
        drawer.classList.remove('show');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    } else {
        drawer.classList.add('show');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    }
}
</script>
@endsection

@php
    $classType = $classType ?? Helper::classType();
    $paymentModes = $paymentModes ?? Helper::getPaymentMode();
    $search = $search ?? [];
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $lastPage = $lastPage ?? 1;
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $stats = (array) ($stats ?? [
        'total_count'    => $totalCount,
        'total_amount'   => 0,
        'total_discount' => 0,
        'total_fine'     => 0,
        'net_collected'  => 0,
    ]);
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('title', 'Fee Receipts')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEE RECEIPTS REGISTER (CA REPORT)
   Native mobile app design matching Arise ERP mobile design system
   ========================================================================== */
.receipts-mob-container {
    padding: 0 0 70px 0;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}

/* 1. Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 6px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 3px 10px rgba(0, 44, 84, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-hero-title {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-session-pill {
    font-size: 9.5px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.14);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 2px 7px;
    border-radius: 10px;
    color: #38bdf8;
}

/* 4-KPI Grid */
.mob-kpi-grid-4 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    margin-top: 6px;
}
.mob-kpi-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px 8px;
}
.mob-kpi-val {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.15;
}
.mob-kpi-val.text-net {
    color: #4ade80;
}
.mob-kpi-val.text-disc {
    color: #fde047;
}
.mob-kpi-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-top: 2px;
}

/* Quick Date Range Pills */
.mob-date-pills {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
}
.mob-date-pill {
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}
.mob-date-pill.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* Search Box */
.mob-search-box {
    position: relative;
    margin-bottom: 8px;
}
.mob-search-box i.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 13px;
}
.mob-search-input {
    width: 100%;
    height: 38px;
    padding: 6px 36px 6px 34px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    outline: none;
    transition: all 0.2s ease;
}
.mob-search-input:focus {
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.15);
}
.mob-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #f1f5f9;
    border: none;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 11px;
    cursor: pointer;
}

/* Feed List */
.mob-feed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding: 0 2px;
}
.mob-feed-count {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
}
.mob-cards-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mob-receipt-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 6px;
}
.mob-receipt-badge {
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    letter-spacing: .02em;
}
.mob-date-badge {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 3px;
}
.mob-card-name {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.25;
    margin-bottom: 4px;
}
.mob-meta-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 11px;
    color: #64748b;
    margin-bottom: 8px;
}
.mob-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Amount & Mode Strip */
.mob-amount-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 10px;
    margin-bottom: 8px;
}
.mob-amt-box {
    display: flex;
    flex-direction: column;
}
.mob-amt-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.mob-amt-val {
    font-size: 14px;
    font-weight: 800;
    color: #16a34a;
    line-height: 1.15;
}
.mob-mode-badge {
    font-size: 10px;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}

/* Card Actions */
.mob-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.btn-mob-print {
    background: #0284c7;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-mob-print:active {
    background: #0369a1;
    color: #ffffff;
}

/* Pagination Bar */
.mob-pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 12px;
    padding: 8px 10px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}
.mob-page-info {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
}
.mob-page-btns {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-page-btn {
    padding: 4px 8px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 4px;
    color: #002C54;
    text-decoration: none;
}
.mob-page-btn.disabled {
    opacity: 0.5;
    pointer-events: none;
    background: #f1f5f9;
}
</style>

<div class="receipts-mob-container">
    {{-- 1. Mobile Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-file-text-o text-warning"></i> Fee Receipts Register
            </div>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>

        {{-- 4-KPI Grid --}}
        <div class="mob-kpi-grid-4">
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">{{ number_format($stats['total_count'] ?? $totalCount) }}</div>
                <div class="mob-kpi-lbl">Total Receipts</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-net">₹{{ number_format($stats['net_collected'] ?? 0, 2) }}</div>
                <div class="mob-kpi-lbl">Net Collections</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">₹{{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                <div class="mob-kpi-lbl">Fee Amount</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-disc">₹{{ number_format($stats['total_discount'] ?? 0, 2) }}</div>
                <div class="mob-kpi-lbl">Total Discount</div>
            </div>
        </div>
    </div>

    {{-- Quick Date Range Chips --}}
    <div class="mob-date-pills">
        <a href="{{ request()->fullUrlWithQuery(['starting' => '', 'ending' => '', 'page' => 1]) }}" class="mob-date-pill {{ empty($search['starting']) ? 'active' : '' }}">All Time</a>
        <a href="{{ request()->fullUrlWithQuery(['starting' => date('Y-m-d'), 'ending' => date('Y-m-d'), 'page' => 1]) }}" class="mob-date-pill {{ ($search['starting'] ?? '') === date('Y-m-d') ? 'active' : '' }}">Today</a>
        <a href="{{ request()->fullUrlWithQuery(['starting' => date('Y-m-01'), 'ending' => date('Y-m-t'), 'page' => 1]) }}" class="mob-date-pill {{ ($search['starting'] ?? '') === date('Y-m-01') ? 'active' : '' }}">This Month</a>
    </div>

    {{-- 2. Instant Search Box --}}
    <div class="mob-search-box">
        <i class="fa fa-search search-icon"></i>
        <input type="text" id="mobReceiptSearch" class="mob-search-input" placeholder="Search by Receipt No, Student, Adm No, Father..." autocomplete="off">
        <button type="button" id="mobReceiptClear" class="mob-search-clear"><i class="fa fa-times"></i></button>
    </div>

    {{-- 3. Feed Header --}}
    <div class="mob-feed-header">
        <span class="mob-feed-count" id="mobReceiptCount"><i class="fa fa-file-text text-primary mr-1"></i> Showing {{ count($data ?? []) }} Receipts</span>
        <span class="text-muted" style="font-size: 10.5px;">Page {{ $currentPage }} of {{ $lastPage }}</span>
    </div>

    {{-- 4. Receipt Cards Feed --}}
    <div class="mob-cards-feed" id="mobReceiptCardsFeed">
        @forelse($data ?? [] as $item)
            @php
                $row = (object) $item;
                $safeReceipt = $row->receipt_no ?? ($row->id ?? '—');
                $safeOffline = $row->offline_receipt_no ?? '';
                $safeName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
                $safeAdm = $row->admissionNo ?? ($row->admission_id ?? '—');
                $safeClass = $row->class_name ?? '—';
                $safeFather = $row->father_name ?? '—';
                $safeDate = !empty($row->date) ? date('d M Y', strtotime($row->date)) : (!empty($row->created_at) ? date('d M Y', strtotime($row->created_at)) : '—');
                $netPaid = (float) ($row->paid_amount ?? ($row->total_amount ?? 0));
                $modeName = $row->payment_mode_name ?? ($row->payment_name ?? 'Cash');
            @endphp
            <div class="mob-receipt-card"
                 data-receipt="{{ strtolower($safeReceipt) }}"
                 data-offline="{{ strtolower($safeOffline) }}"
                 data-name="{{ strtolower($safeName) }}"
                 data-adm="{{ strtolower($safeAdm) }}"
                 data-father="{{ strtolower($safeFather) }}">
                <div class="mob-card-head">
                    <span class="mob-receipt-badge">REC #{{ $safeReceipt }}</span>
                    <span class="mob-date-badge"><i class="fa fa-calendar-o mr-1"></i> {{ $safeDate }}</span>
                </div>
                <div class="mob-card-name">{{ $safeName }}</div>
                <div class="mob-meta-row">
                    <div class="mob-meta-item">
                        <span class="badge badge-light border text-primary font-weight-bold" style="font-size: 9.5px;">ADM #{{ $safeAdm }}</span>
                    </div>
                    <div class="mob-meta-item">
                        <span class="badge badge-light border font-weight-bold" style="font-size: 9.5px;">{{ $safeClass }}</span>
                    </div>
                    <div class="mob-meta-item">
                        <i class="fa fa-user text-muted"></i> F: {{ $safeFather }}
                    </div>
                </div>

                {{-- Amount & Mode Strip --}}
                <div class="mob-amount-strip">
                    <div class="mob-amt-box">
                        <span class="mob-amt-lbl">Paid Amount</span>
                        <span class="mob-amt-val">₹{{ number_format($netPaid, 2) }}</span>
                    </div>
                    <span class="mob-mode-badge"><i class="fa fa-credit-card mr-1"></i> {{ $modeName }}</span>
                </div>

                <div class="mob-card-actions">
                    <span class="text-muted" style="font-size: 10px;">
                        @if(!empty($safeOffline))
                            Slip: <strong>{{ $safeOffline }}</strong>
                        @endif
                    </span>
                    <a href="{{ url('fees/print/feesReceipt/' . $row->id) }}" target="_blank" class="btn-mob-print">
                        <i class="fa fa-print"></i> Print Receipt
                    </a>
                </div>
            </div>
        @empty
            <div class="mob-receipt-card text-center py-4">
                <i class="fa fa-file-text-o text-muted" style="font-size: 32px; margin-bottom: 6px;"></i>
                <h5 class="font-weight-bold" style="font-size: 13px; color: #334155;">No Fee Receipts Found</h5>
                <p class="text-muted small mb-0">No fee collection receipts match the active filter criteria.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($lastPage > 1)
        <div class="mob-pagination-bar">
            <span class="mob-page-info">Page {{ $currentPage }} of {{ $lastPage }}</span>
            <div class="mob-page-btns">
                <a href="{{ request()->fullUrlWithQuery(['page' => max(1, $currentPage - 1)]) }}" class="mob-page-btn {{ $currentPage <= 1 ? 'disabled' : '' }}">
                    <i class="fa fa-angle-left mr-1"></i> Prev
                </a>
                <a href="{{ request()->fullUrlWithQuery(['page' => min($lastPage, $currentPage + 1)]) }}" class="mob-page-btn {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                    Next <i class="fa fa-angle-right ml-1"></i>
                </a>
            </div>
        </div>
    @endif
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    function filterReceiptCards() {
        var q = $('#mobReceiptSearch').val().toLowerCase().trim();
        var cards = $('#mobReceiptCardsFeed .mob-receipt-card');
        var count = 0;

        cards.each(function() {
            var card = $(this);
            var rec = card.data('receipt') || '';
            var off = card.data('offline') || '';
            var name = card.data('name') || '';
            var adm = card.data('adm') || '';
            var father = card.data('father') || '';

            var match = !q || rec.includes(q) || off.includes(q) || name.includes(q) || adm.includes(q) || father.includes(q);
            if (match) {
                card.show();
                count++;
            } else {
                card.hide();
            }
        });

        $('#mobReceiptCount').html('<i class="fa fa-file-text text-primary mr-1"></i> Showing ' + count + ' Receipts');
    }

    $('#mobReceiptSearch').on('input', function() {
        var v = $(this).val();
        $('#mobReceiptClear').toggle(v.length > 0);
        filterReceiptCards();
    });

    $('#mobReceiptClear').on('click', function() {
        $('#mobReceiptSearch').val('').trigger('input');
        $(this).hide();
    });
});
</script>
@endsection

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
        'total_count'        => $totalCount,
        'filtered_amount'    => 0,
        'pending_count'      => 0,
        'pending_amount'     => 0,
        'realised_count'     => 0,
        'realised_amount'    => 0,
        'dishonoured_count'  => 0,
        'dishonoured_amount' => 0,
    ]);
    $currentStatusFilter = isset($search['status']) && $search['status'] !== '' ? (string) $search['status'] : '1';
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('title', 'Fees Cheque Register')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEES CHEQUE & REALISATION REGISTER
   Native mobile app design matching Arise ERP mobile design system
   ========================================================================== */
.cheque-mob-container {
    padding: 8px 10px 80px 10px;
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
.mob-kpi-val.text-pending {
    color: #fde047;
}
.mob-kpi-val.text-realised {
    color: #4ade80;
}
.mob-kpi-val.text-bounced {
    color: #f87171;
}
.mob-kpi-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-top: 2px;
}

/* Status Filter Chips */
.mob-chips-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
}
.mob-chip {
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
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
.mob-cheque-card {
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
.mob-cheque-no-badge {
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    letter-spacing: .02em;
}
.mob-status-badge {
    font-size: 9.5px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 3px;
    text-transform: uppercase;
}
.mob-status-badge.pending {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.mob-status-badge.realised {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.mob-status-badge.dishonoured {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
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

/* Cheque Details Box */
.mob-cheque-info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 10px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-cheque-col {
    display: flex;
    flex-direction: column;
}
.mob-cheque-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.mob-cheque-val {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1px;
}
.mob-cheque-amt {
    font-size: 14px;
    font-weight: 800;
    color: #16a34a;
}

/* Actions Row */
.mob-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.mob-status-select {
    height: 28px;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    background: #ffffff;
    color: #0f172a;
    outline: none;
}
.btn-mob-slip {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
    font-size: 10.5px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

<div class="cheque-mob-container">
    {{-- 1. Mobile Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-money text-warning"></i> Cheque Register
            </div>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>

        {{-- 4-KPI Grid --}}
        <div class="mob-kpi-grid-4">
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-pending">{{ number_format($stats['pending_count'] ?? 0) }} (₹{{ number_format($stats['pending_amount'] ?? 0) }})</div>
                <div class="mob-kpi-lbl">Pending Cheques</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-realised">{{ number_format($stats['realised_count'] ?? 0) }} (₹{{ number_format($stats['realised_amount'] ?? 0) }})</div>
                <div class="mob-kpi-lbl">Realised / Cleared</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-bounced">{{ number_format($stats['dishonoured_count'] ?? 0) }} (₹{{ number_format($stats['dishonoured_amount'] ?? 0) }})</div>
                <div class="mob-kpi-lbl">Bounced Cheques</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">{{ number_format($stats['total_count'] ?? $totalCount) }}</div>
                <div class="mob-kpi-lbl">Total Records</div>
            </div>
        </div>
    </div>

    {{-- Status Filter Chips --}}
    <div class="mob-chips-strip">
        <a href="{{ request()->fullUrlWithQuery(['status' => '1', 'page' => 1]) }}" class="mob-chip {{ $currentStatusFilter === '1' ? 'active' : '' }}">
            <i class="fa fa-clock-o mr-1"></i> Pending ({{ $stats['pending_count'] ?? 0 }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => '0', 'page' => 1]) }}" class="mob-chip {{ $currentStatusFilter === '0' ? 'active' : '' }}">
            <i class="fa fa-check-circle mr-1"></i> Realised ({{ $stats['realised_count'] ?? 0 }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => '2', 'page' => 1]) }}" class="mob-chip {{ $currentStatusFilter === '2' ? 'active' : '' }}">
            <i class="fa fa-times-circle mr-1"></i> Dishonoured ({{ $stats['dishonoured_count'] ?? 0 }})
        </a>
        <a href="{{ request()->fullUrlWithQuery(['status' => 'all', 'page' => 1]) }}" class="mob-chip {{ $currentStatusFilter === 'all' ? 'active' : '' }}">
            All Cheques
        </a>
    </div>

    {{-- 2. Instant Search Box --}}
    <div class="mob-search-box">
        <i class="fa fa-search search-icon"></i>
        <input type="text" id="mobChequeSearch" class="mob-search-input" placeholder="Search by Cheque #, Bank, Student, Adm No..." autocomplete="off">
        <button type="button" id="mobChequeClear" class="mob-search-clear"><i class="fa fa-times"></i></button>
    </div>

    {{-- 3. Feed Header --}}
    <div class="mob-feed-header">
        <span class="mob-feed-count" id="mobChequeCount"><i class="fa fa-file-text-o text-primary mr-1"></i> Showing {{ count($data ?? []) }} Cheques</span>
        <span class="text-muted" style="font-size: 10.5px;">Page {{ $currentPage }} of {{ $lastPage }}</span>
    </div>

    {{-- 4. Cheque Cards Feed --}}
    <div class="mob-cards-feed" id="mobChequeCardsFeed">
        @forelse($data ?? [] as $item)
            @php
                $row = (object) $item;
                $safeChequeNo = $row->transition_id ?? ($row->offline_receipt_no ?? '—');
                $safeBank = $row->bank_name ?? 'Cheque';
                $safeName = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
                $safeAdm = $row->admissionNo ?? ($row->admission_id ?? '—');
                $safeClass = $row->class_name ?? '—';
                $safeFather = $row->father_name ?? '—';
                $safeDate = !empty($row->date) ? date('d M Y', strtotime($row->date)) : (!empty($row->created_at) ? date('d M Y', strtotime($row->created_at)) : '—');
                $amount = (float) ($row->paid_amount ?? ($row->total_amount ?? 0));
                $st = (int) ($row->status ?? 1);
            @endphp
            <div class="mob-cheque-card"
                 data-cheque="{{ strtolower($safeChequeNo) }}"
                 data-bank="{{ strtolower($safeBank) }}"
                 data-name="{{ strtolower($safeName) }}"
                 data-adm="{{ strtolower($safeAdm) }}"
                 data-father="{{ strtolower($safeFather) }}">
                <div class="mob-card-head">
                    <span class="mob-cheque-no-badge"><i class="fa fa-credit-card mr-1"></i> CHQ #{{ $safeChequeNo }}</span>
                    @if($st === 0)
                        <span class="mob-status-badge realised"><i class="fa fa-check mr-1"></i> Realised</span>
                    @elseif($st === 2)
                        <span class="mob-status-badge dishonoured"><i class="fa fa-times mr-1"></i> Dishonoured</span>
                    @else
                        <span class="mob-status-badge pending"><i class="fa fa-clock-o mr-1"></i> Pending</span>
                    @endif
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

                {{-- Cheque Info Box --}}
                <div class="mob-cheque-info-box">
                    <div class="mob-cheque-col">
                        <span class="mob-cheque-lbl">Bank / Date</span>
                        <span class="mob-cheque-val">{{ $safeBank }} &bull; {{ $safeDate }}</span>
                    </div>
                    <div class="mob-cheque-col text-right">
                        <span class="mob-cheque-lbl">Cheque Amount</span>
                        <span class="mob-cheque-amt">₹{{ number_format($amount, 2) }}</span>
                    </div>
                </div>

                <div class="mob-card-actions">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted" style="font-size: 10px;">Status:</span>
                        <select class="mob-status-select" onchange="updateChequeStatus({{ $row->id }}, this.value)">
                            <option value="1" {{ $st === 1 ? 'selected' : '' }}>Pending</option>
                            <option value="0" {{ $st === 0 ? 'selected' : '' }}>Realised</option>
                            <option value="2" {{ $st === 2 ? 'selected' : '' }}>Dishonoured</option>
                        </select>
                    </div>

                    @if(!empty($row->slip_img))
                        <a href="{{ asset('public/uploads/fees/' . $row->slip_img) }}" target="_blank" class="btn-mob-slip">
                            <i class="fa fa-image"></i> View Slip
                        </a>
                    @else
                        <a href="{{ url('fees/print/feesReceipt/' . $row->id) }}" target="_blank" class="btn-mob-slip">
                            <i class="fa fa-print"></i> Receipt
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="mob-cheque-card text-center py-4">
                <i class="fa fa-credit-card text-muted" style="font-size: 32px; margin-bottom: 6px;"></i>
                <h5 class="font-weight-bold" style="font-size: 13px; color: #334155;">No Cheques Found</h5>
                <p class="text-muted small mb-0">No cheque records match this status or filter.</p>
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
var BASEURL = "{{ url('/') }}";

function updateChequeStatus(id, newStatus) {
    if (!confirm('Are you sure you want to change the status of this cheque?')) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: BASEURL + '/fees_cheque_status_update',
        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            status: newStatus
        },
        success: function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Cheque status updated successfully.');
            }
            window.location.reload();
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to update cheque status.');
            } else {
                alert('Failed to update cheque status.');
            }
        }
    });
}

$(document).ready(function() {
    function filterChequeCards() {
        var q = $('#mobChequeSearch').val().toLowerCase().trim();
        var cards = $('#mobChequeCardsFeed .mob-cheque-card');
        var count = 0;

        cards.each(function() {
            var card = $(this);
            var chq = card.data('cheque') || '';
            var bnk = card.data('bank') || '';
            var name = card.data('name') || '';
            var adm = card.data('adm') || '';
            var father = card.data('father') || '';

            var match = !q || chq.includes(q) || bnk.includes(q) || name.includes(q) || adm.includes(q) || father.includes(q);
            if (match) {
                card.show();
                count++;
            } else {
                card.hide();
            }
        });

        $('#mobChequeCount').html('<i class="fa fa-file-text-o text-primary mr-1"></i> Showing ' + count + ' Cheques');
    }

    $('#mobChequeSearch').on('input', function() {
        var v = $(this).val();
        $('#mobChequeClear').toggle(v.length > 0);
        filterChequeCards();
    });

    $('#mobChequeClear').on('click', function() {
        $('#mobChequeSearch').val('').trigger('input');
        $(this).hide();
    });
});
</script>
@endsection

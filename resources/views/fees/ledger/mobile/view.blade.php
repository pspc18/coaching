@php
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $totalCount = $totalCount ?? (is_countable($data ?? []) ? count($data ?? []) : 0);
    $startIndex = $startIndex ?? 0;
    $lastPage = $lastPage ?? 1;
    $currentPage = $currentPage ?? 1;
    $perPage = $perPage ?? 25;
    $stats = (array) ($stats ?? [
        'total_students'  => $totalCount,
        'total_assigned'  => 0,
        'total_collected' => 0,
        'total_discount'  => 0,
        'total_pending'   => 0,
    ]);
    $paidLookup = $paidLookup ?? [];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('title', 'Fees Ledger')

@section('content')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE STUDENT FEES LEDGER
   Native mobile app design matching Arise ERP mobile design system
   ========================================================================== */
.ledger-mob-container {
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
.mob-kpi-val.text-due {
    color: #f87171;
}
.mob-kpi-val.text-coll {
    color: #4ade80;
}
.mob-kpi-lbl {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .03em;
    margin-top: 2px;
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

/* Class Filter Chips */
.mob-chips-strip {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 6px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}
.mob-chips-strip::-webkit-scrollbar {
    display: none;
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
    transition: all 0.15s ease;
    flex-shrink: 0;
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
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
.mob-ledger-card {
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
.mob-adm-badge {
    font-size: 10px;
    font-weight: 800;
    background: #002C54;
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
}
.mob-class-badge {
    font-size: 10px;
    font-weight: 700;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
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

/* Balance Strip */
.mob-balance-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    text-align: center;
}
.mob-bal-item {
    display: flex;
    flex-direction: column;
}
.mob-bal-lbl {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.mob-bal-val {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    margin-top: 1px;
}
.mob-bal-val.paid {
    color: #16a34a;
}
.mob-bal-val.due {
    color: #dc2626;
    font-size: 12.5px;
}

/* Card Actions */
.mob-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px dashed #e2e8f0;
}
.btn-mob-view-ledger {
    background: #f1f5f9;
    color: #002C54;
    border: 1px solid #cbd5e1;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-mob-collect {
    background: #002C54;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.2);
}

/* Bottom Sheet / Modal for Ledger Details */
.mob-sheet-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(2px);
    z-index: 1050;
    display: none;
}
.mob-sheet-overlay.active { display: block; }
.mob-ledger-sheet {
    position: fixed;
    left: 0; right: 0; bottom: 0;
    height: 85vh; max-height: 85vh;
    background: #ffffff;
    border-top-left-radius: 14px;
    border-top-right-radius: 14px;
    z-index: 1060;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.2);
    transform: translateY(100%);
    transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
}
.mob-ledger-sheet.active { transform: translateY(0); }
.mob-sheet-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.mob-sheet-body {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
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

<div class="ledger-mob-container">
    {{-- 1. Mobile Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-book text-warning"></i> Student Fees Ledger
            </div>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>

        {{-- 4-KPI Grid --}}
        <div class="mob-kpi-grid-4">
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">{{ number_format($stats['total_students'] ?? $totalCount) }}</div>
                <div class="mob-kpi-lbl">Total Students</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-due">₹{{ number_format($stats['total_pending'] ?? 0) }}</div>
                <div class="mob-kpi-lbl">Outstanding Due</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val">₹{{ number_format($stats['total_assigned'] ?? 0) }}</div>
                <div class="mob-kpi-lbl">Total Assigned</div>
            </div>
            <div class="mob-kpi-box">
                <div class="mob-kpi-val text-coll">₹{{ number_format($stats['total_collected'] ?? 0) }}</div>
                <div class="mob-kpi-lbl">Total Collected</div>
            </div>
        </div>
    </div>

    {{-- 2. Instant Search Box --}}
    <div class="mob-search-box">
        <i class="fa fa-search search-icon"></i>
        <input type="text" id="mobLedgerSearch" class="mob-search-input" placeholder="Search by Student, Adm No, Father, Mobile..." autocomplete="off">
        <button type="button" id="mobLedgerClear" class="mob-search-clear"><i class="fa fa-times"></i></button>
    </div>

    {{-- Class Filter Chips --}}
    <div class="mob-chips-strip" id="mobClassChips">
        <div class="mob-chip {{ empty($search['class_type_id']) ? 'active' : '' }}" data-class="">All Classes</div>
        @if(!empty($classType))
            @foreach($classType as $cls)
                <div class="mob-chip {{ ($search['class_type_id'] ?? '') == $cls->id ? 'active' : '' }}" data-class="{{ $cls->id }}">
                    {{ $cls->name }}
                </div>
            @endforeach
        @endif
    </div>

    {{-- 3. Feed Header --}}
    <div class="mob-feed-header">
        <span class="mob-feed-count" id="mobLedgerCount"><i class="fa fa-graduation-cap text-primary mr-1"></i> Showing {{ count($data ?? []) }} Students</span>
        <span class="text-muted" style="font-size: 10.5px;">Page {{ $currentPage }} of {{ $lastPage }}</span>
    </div>

    {{-- 4. Ledger Cards Feed --}}
    <div class="mob-cards-feed" id="mobLedgerCardsFeed">
        @forelse($data ?? [] as $stu)
            @php
                $item = (object) $stu;
                $safeName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                $safeAdm = $item->admissionNo ?? ($item->id ?? '—');
                $safeClass = $item->class_name ?? ($item->ClassTypes->name ?? '—');
                $safeFather = $item->father_name ?? '—';
                $safeMobile = (!empty($item->mobile) && $item->mobile !== '-') ? $item->mobile : '';
                
                $assigned = (float) ($item->net_amount ?? (($item->total_amount ?? 0) - ($item->total_discount ?? 0)));
                $paidItem = $paidLookup[$item->id] ?? null;
                $paidAmt = (float) ($paidItem->total_paid ?? ($item->total_paid ?? 0));
                $discAmt = (float) ($paidItem->total_discount ?? ($item->discount ?? 0));
                $netDue = max(0, $assigned - $paidAmt);
            @endphp
            <div class="mob-ledger-card"
                 data-name="{{ strtolower($safeName) }}"
                 data-adm="{{ strtolower($safeAdm) }}"
                 data-father="{{ strtolower($safeFather) }}"
                 data-mobile="{{ $safeMobile }}">
                <div class="mob-card-head">
                    <span class="mob-adm-badge">ADM #{{ $safeAdm }}</span>
                    <span class="mob-class-badge">{{ $safeClass }}</span>
                </div>
                <div class="mob-card-name">{{ $safeName }}</div>
                <div class="mob-meta-row">
                    <div class="mob-meta-item">
                        <i class="fa fa-user text-muted"></i> F: {{ $safeFather }}
                    </div>
                    @if(!empty($safeMobile))
                        <div class="mob-meta-item">
                            <i class="fa fa-phone text-success"></i> {{ $safeMobile }}
                        </div>
                    @endif
                </div>

                {{-- Fee Balance Strip --}}
                <div class="mob-balance-strip">
                    <div class="mob-bal-item">
                        <span class="mob-bal-lbl">Assigned</span>
                        <span class="mob-bal-val">₹{{ number_format($assigned) }}</span>
                    </div>
                    <div class="mob-bal-item">
                        <span class="mob-bal-lbl">Collected</span>
                        <span class="mob-bal-val paid">₹{{ number_format($paidAmt) }}</span>
                    </div>
                    <div class="mob-bal-item">
                        <span class="mob-bal-lbl">Outstanding</span>
                        <span class="mob-bal-val due">₹{{ number_format($netDue) }}</span>
                    </div>
                </div>

                <div class="mob-card-actions">
                    <button type="button" class="btn-mob-view-ledger" onclick="openLedgerDetail({{ $item->id }}, '{{ addslashes($safeName) }}', '{{ $safeAdm }}')">
                        <i class="fa fa-list-alt text-primary"></i> Heads Breakdown
                    </button>
                    <a href="{{ url('Fees/add?student_id=' . $item->id) }}" class="btn-mob-collect">
                        <i class="fa fa-inr"></i> Collect Fees <i class="fa fa-chevron-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="mob-ledger-card text-center py-4">
                <i class="fa fa-folder-open-o text-muted" style="font-size: 32px; margin-bottom: 6px;"></i>
                <h5 class="font-weight-bold" style="font-size: 13px; color: #334155;">No Student Records Found</h5>
                <p class="text-muted small mb-0">No active students match the specified search or filter.</p>
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

{{-- Bottom Sheet for Fee Heads Breakdown --}}
<div class="mob-sheet-overlay" id="mobLedgerOverlay" onclick="closeLedgerDetail()"></div>
<div class="mob-ledger-sheet" id="mobLedgerSheet">
    <div class="mob-sheet-header">
        <div>
            <h5 class="font-weight-bold mb-0 text-white" id="sheetStudentName" style="font-size: 13.5px;">Fee Structure Breakdown</h5>
            <span class="text-white-50" id="sheetAdmNo" style="font-size: 10px;">ADM #</span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light py-0 px-2" onclick="closeLedgerDetail()">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <div class="mob-sheet-body" id="sheetLedgerContent">
        {{-- Filled via AJAX --}}
    </div>
</div>

<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>
<script>
var BASEURL = "{{ url('/') }}";

function openLedgerDetail(admissionId, name, admNo) {
    $('#sheetStudentName').text(name);
    $('#sheetAdmNo').text('ADM #' + admNo);
    $('#mobLedgerOverlay').addClass('active');
    $('#mobLedgerSheet').addClass('active');
    $('body').css('overflow', 'hidden');

    $('#sheetLedgerContent').html(`
        <div class="text-center py-4">
            <i class="fa fa-spinner fa-spin fa-2x text-primary mb-2"></i>
            <p class="text-muted small">Loading fee breakdown...</p>
        </div>
    `);

    $.ajax({
        type: 'POST',
        url: BASEURL + '/fees_ledger_view',
        data: {
            _token: "{{ csrf_token() }}",
            admission_id: admissionId
        },
        success: function(res) {
            $('#sheetLedgerContent').html(res);
        },
        error: function() {
            $('#sheetLedgerContent').html(`
                <div class="text-center py-4 text-danger">
                    <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                    <p class="small">Failed to load fee breakdown details.</p>
                </div>
            `);
        }
    });
}

function closeLedgerDetail() {
    $('#mobLedgerOverlay').removeClass('active');
    $('#mobLedgerSheet').removeClass('active');
    $('body').css('overflow', '');
}

$(document).ready(function() {
    function filterLedgerCards() {
        var q = $('#mobLedgerSearch').val().toLowerCase().trim();
        var cards = $('#mobLedgerCardsFeed .mob-ledger-card');
        var count = 0;

        cards.each(function() {
            var card = $(this);
            var name = card.data('name') || '';
            var adm = card.data('adm') || '';
            var father = card.data('father') || '';
            var mobile = card.data('mobile') || '';

            var match = !q || name.includes(q) || adm.includes(q) || father.includes(q) || mobile.includes(q);
            if (match) {
                card.show();
                count++;
            } else {
                card.hide();
            }
        });

        $('#mobLedgerCount').html('<i class="fa fa-graduation-cap text-primary mr-1"></i> Showing ' + count + ' Students');
    }

    $('#mobLedgerSearch').on('input', function() {
        var v = $(this).val();
        $('#mobLedgerClear').toggle(v.length > 0);
        filterLedgerCards();
    });

    $('#mobLedgerClear').on('click', function() {
        $('#mobLedgerSearch').val('').trigger('input');
        $(this).hide();
    });

    $('#mobClassChips .mob-chip').on('click', function() {
        var classId = $(this).data('class');
        var url = new URL(window.location.href);
        if (classId) {
            url.searchParams.set('class_type_id', classId);
        } else {
            url.searchParams.delete('class_type_id');
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
});
</script>
@endsection

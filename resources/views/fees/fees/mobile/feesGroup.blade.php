@php
    $classType = Helper::classType();
    $permission = Helper::permissioncheck(11);
    $inUseGroupIds = $inUseGroupIds ?? [];
    $stats = $stats ?? [
        'total' => is_countable($dataview ?? []) ? count($dataview ?? []) : 0,
        'refundable' => 0,
        'non_refundable' => 0,
        'in_use' => 0,
    ];
    $currentSessionName = Session::get('session_name') ?? '2026-27';
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - MOBILE FEES GROUP STYLES (MATCHING CORE MOBILE STANDARDS)
   ========================================================================== */

.mob-fg-wrapper {
    padding: 6px 8px 70px 8px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}

/* 1. Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    line-height: 1.2;
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

/* Hero KPI Grid */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 4px;
    text-align: center;
}
.mob-kpi-tag {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.mob-kpi-val {
    font-size: 13.5px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1;
}
.val-green { color: #4ade80 !important; }
.val-cyan { color: #38bdf8 !important; }
.val-amber { color: #fbbf24 !important; }

/* Hero Action Buttons */
.mob-hero-actions {
    display: flex;
    gap: 6px;
}
.mob-hero-btn {
    flex: 1;
    height: 30px;
    border-radius: 3px;
    font-size: 11px;
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
.btn-hero-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 1.5;
}
.btn-hero-outline {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. Search Toolbar & Horizontal Filter Chips */
.mob-search-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 12px;
}
.mob-search-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-input:focus {
    border-color: #002C54;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    font-size: 13px;
    cursor: pointer;
    display: none;
}

/* Horizontal Filter Chips */
.mob-chips-strip {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
}
.mob-chips-strip::-webkit-scrollbar {
    display: none;
}
.mob-chip-item {
    flex-shrink: 0;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 12px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-chip-item.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 3. Cards Feed List */
.mob-cards-feed {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 12px;
}
.mob-group-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    padding: 9px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
}
.mob-group-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-group-name {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-status-tag {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-transform: uppercase;
}
.status-tag-refund { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.status-tag-std { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.status-tag-inuse { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

.mob-group-card-mid {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-group-card-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-group-id-info {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
}
.mob-group-actions {
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    background: #0284c7;
    color: #ffffff !important;
    text-decoration: none !important;
}
.mob-btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    background: #dc2626;
    color: #ffffff !important;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
}
.mob-btn-locked {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px solid #cbd5e1;
    cursor: not-allowed;
}

/* 4. Strict Modal Rules (Prevent inline rendering at all costs) */
.modal {
    display: none !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 2050 !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    background: rgba(0, 15, 30, 0.65) !important;
    backdrop-filter: blur(3px) !important;
}
.modal.show {
    display: block !important;
}
.modal-dialog {
    position: relative !important;
    width: auto !important;
    margin: 1.75rem auto !important;
    max-width: 460px !important;
    padding: 0 10px !important;
    z-index: 2060 !important;
}
.modal-content {
    background: #ffffff !important;
    border-radius: 4px !important;
    overflow: hidden !important;
    border: none !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
}
.modal-header {
    padding: 9px 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
}
.modal-header.bg-primary {
    background: #002C54 !important;
    color: #ffffff !important;
}
.modal-header.bg-danger {
    background: #dc2626 !important;
    color: #ffffff !important;
}
.modal-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    margin: 0 !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    gap: 6px !important;
}
.modal-close-btn {
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-size: 22px !important;
    line-height: 1 !important;
    font-weight: 700 !important;
    opacity: 0.85 !important;
    cursor: pointer !important;
    padding: 0 !important;
    margin: 0 !important;
}
.modal-close-btn:hover {
    opacity: 1 !important;
}
.modal-body {
    padding: 14px !important;
    background: #ffffff !important;
}
.modal-footer {
    padding: 8px 12px !important;
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
}
</style>
@endsection

@section('content')
<div class="mob-fg-wrapper">

    {{-- 1. Dark Navy Hero Banner --}}
    <div class="mob-hero-card">
        <div class="mob-hero-header">
            <h1 class="mob-hero-title">
                <i class="fa fa-folder-open text-info"></i> {{ __('fees.Fees Group') }}
            </h1>
            <span class="mob-session-pill">Session: {{ $currentSessionName }}</span>
        </div>

        {{-- 4 Metric Grid --}}
        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <span class="mob-kpi-tag">Total</span>
                <span class="mob-kpi-val">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="mob-kpi-col">
                <span class="mob-kpi-tag">Refund</span>
                <span class="mob-kpi-val val-green">{{ $stats['refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-kpi-col">
                <span class="mob-kpi-tag">Standard</span>
                <span class="mob-kpi-val val-cyan">{{ $stats['non_refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-kpi-col">
                <span class="mob-kpi-tag">In-Use</span>
                <span class="mob-kpi-val val-amber">{{ $stats['in_use'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Action Buttons in Hero --}}
        <div class="mob-hero-actions">
            <button type="button" class="mob-hero-btn btn-hero-add" id="btnOpenAddModal">
                <i class="fa fa-plus-circle"></i> {{ __('fees.Add Fees Group') }}
            </button>
            <a href="{{ url('feesMaster') }}" class="mob-hero-btn btn-hero-outline">
                <i class="fa fa-sliders"></i> Fees Master
            </a>
            <a href="{{ url('fee_dashboard') }}" class="mob-hero-btn btn-hero-outline" style="max-width: 42px;" title="Back">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- 2. Compact Search & Horizontal Filter Chips --}}
    <div class="mob-search-card">
        <div class="mob-search-wrap">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search fee groups...">
            <i class="fa fa-times-circle mob-search-clear" id="mobSearchClear"></i>
        </div>

        <div class="mob-chips-strip">
            <div class="mob-chip-item active" data-filter="all">All ({{ $stats['total'] ?? 0 }})</div>
            <div class="mob-chip-item" data-filter="refundable"><i class="fa fa-check-circle text-success mr-1"></i>Refundable ({{ $stats['refundable'] ?? 0 }})</div>
            <div class="mob-chip-item" data-filter="non-refundable"><i class="fa fa-minus-circle text-info mr-1"></i>Standard ({{ $stats['non_refundable'] ?? 0 }})</div>
            <div class="mob-chip-item" data-filter="in-use"><i class="fa fa-link text-warning mr-1"></i>In-Use ({{ $stats['in_use'] ?? 0 }})</div>
        </div>
    </div>

    {{-- 3. Cards Feed List --}}
    <div class="mob-cards-feed" id="mobCardsFeed">
        @if(!empty($dataview) && count($dataview) > 0)
            @php $srNo = 1; @endphp
            @foreach ($dataview as $item)
                @php
                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                    $isInUse = in_array($item->id, $inUseGroupIds);
                    $nameLower = strtolower($item->name ?? '');
                    $filterType = $isRefundable ? 'refundable' : 'non-refundable';
                @endphp
                <div class="mob-group-card" 
                     data-name="{{ $nameLower }}" 
                     data-type="{{ $filterType }}"
                     data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                    
                    {{-- Top Row: Name & Refundable Tag --}}
                    <div class="mob-group-card-top">
                        <div class="mob-group-name">
                            <i class="fa fa-money text-primary"></i>
                            <span>{{ $item->name ?? '' }}</span>
                        </div>
                        <div>
                            @if($isRefundable)
                                <span class="mob-status-tag status-tag-refund">
                                    <i class="fa fa-check-circle"></i> Refundable
                                </span>
                            @else
                                <span class="mob-status-tag status-tag-std">
                                    <i class="fa fa-minus-circle"></i> Standard
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Middle Row: In-Use & Payment Tag --}}
                    <div class="mob-group-card-mid">
                        @if($isInUse)
                            <span class="mob-status-tag status-tag-inuse" title="Assigned in Fees Master / Receipts">
                                <i class="fa fa-link"></i> In-Use
                            </span>
                        @else
                            <span class="mob-status-tag status-tag-std" title="Unassigned">
                                <i class="fa fa-circle-o"></i> Unlinked
                            </span>
                        @endif

                        @if($item->fees_type === 'installment')
                            <span class="badge bg-light text-dark border" style="font-size:9.5px;">Installment</span>
                        @else
                            <span class="badge bg-light text-dark border" style="font-size:9.5px;">Full Payment</span>
                        @endif
                    </div>

                    {{-- Bottom Row: ID & Styled Actions --}}
                    <div class="mob-group-card-bottom">
                        <span class="mob-group-id-info">#{{ $srNo++ }} (ID: {{ $item->id }})</span>

                        <div class="mob-group-actions">
                            {{-- Edit Action --}}
                            <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                               class="mob-btn-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                               title="Edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            {{-- Delete Action --}}
                            @if(!$isInUse)
                                <button type="button" 
                                        class="mob-btn-delete btn-open-delete-modal {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                        data-id="{{ $item->id }}" 
                                        data-name="{{ $item->name }}" 
                                        title="Delete">
                                    <i class="fa fa-trash-o"></i> Delete
                                </button>
                            @else
                                <span class="mob-btn-locked" title="In Use">
                                    <i class="fa fa-lock"></i> Locked
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        @else
            <div class="text-center py-4 text-muted bg-white rounded border" style="font-size:12px;">
                <i class="fa fa-folder-open-o fa-2x mb-2 text-secondary d-block"></i>
                No fee groups found. Tap "+ Add Fees Group" to create one.
            </div>
        @endif
    </div>

    {{-- No Results Search Fallback --}}
    <div id="mobNoResults" class="text-center py-4 text-muted bg-white rounded border d-none" style="font-size:12px;">
        <i class="fa fa-search fa-2x mb-2 text-secondary d-block"></i>
        No matching fee groups found.
    </div>

</div>

{{-- =========================================================================
   1. ADD FEES GROUP MODAL (STRICT POPUP - HIDDEN BY DEFAULT)
   ========================================================================= --}}
<div class="modal fade" id="addFeesGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fa fa-plus-circle"></i> {{ __('fees.Add Fees Group') }}
                </h5>
                <button type="button" class="modal-close-btn btn-close-modal" aria-label="Close">&times;</button>
            </div>
            <form id="quickForm" action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="modal-body">
                    
                    {{-- Name Input --}}
                    <div style="margin-bottom: 12px;">
                        <label style="display:block; font-size:11.5px; font-weight:700; color:#dc2626; margin-bottom:4px;">
                            {{ __('messages.Name') }}*
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               name="name" 
                               id="name" 
                               placeholder="e.g. Tuition Fee, Exam Fee" 
                               value="{{ old('name') }}" 
                               required 
                               style="height:34px; font-size:12px; border-radius:3px;">
                        @error('name')
                            <span class="text-danger font-weight-bold d-block mt-1" style="font-size:11px;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Refund Fees Toggle Card --}}
                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px; padding:10px 12px; display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                        <div>
                            <div style="font-weight:700; font-size:12px; color:#0f172a;">{{ __('Fees Refund') }}</div>
                            <small style="font-size:10px; color:#64748b;">Eligible for refund on admission cancellation?</small>
                        </div>
                        <input type="checkbox" id="mob_refund_fees_check" value="yes" onchange="updateMobRefund(this)" style="width:18px; height:18px; cursor:pointer;">
                        <input type="hidden" id="mob_fees_refund_input" name="fees_refund" value="no">
                    </div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm btn-close-modal" style="padding:6px 14px;">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm" style="padding:6px 16px; background:#002C54; border-color:#002C54;">
                        {{ __('messages.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================================
   2. DELETE CONFIRMATION MODAL (STRICT POPUP - HIDDEN BY DEFAULT)
   ========================================================================= --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fa fa-trash-o"></i> {{ __('messages.Delete Confirmation') }}
                </h5>
                <button type="button" class="modal-close-btn btn-close-modal" aria-label="Close">&times;</button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center" style="padding: 18px 14px;">
                    <input type="hidden" id="mob_del_id" name="delete_id">
                    <div style="width:46px; height:46px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:20px; margin:0 auto 10px auto;">
                        <i class="fa fa-trash"></i>
                    </div>
                    <p style="color:#64748b; font-size:11.5px; margin-bottom:4px;">{{ __('messages.Are you sure you want to delete') }}?</p>
                    <h6 style="font-size:13.5px; font-weight:800; color:#0f172a; margin:0;" id="mob_del_name_text"></h6>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm btn-close-modal" style="padding:6px 14px;">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm" style="padding:6px 18px; font-weight:700;">
                        {{ __('messages.Delete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateMobRefund(checkbox) {
    document.getElementById('mob_fees_refund_input').value = checkbox.checked ? 'yes' : 'no';
}

$(document).ready(function() {
    // 1. Open Add Modal
    $('#btnOpenAddModal').on('click', function(e) {
        e.preventDefault();
        $('#addFeesGroupModal').addClass('show').fadeIn(150);
        $('body').css('overflow', 'hidden');
    });

    // 2. Open Delete Modal
    $(document).on('click', '.btn-open-delete-modal', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#mob_del_id').val(id);
        $('#mob_del_name_text').text('"' + name + '"');
        $('#deleteModal').addClass('show').fadeIn(150);
        $('body').css('overflow', 'hidden');
    });

    // 3. Close Modal Handler (Works unconditionally across BS4/BS5/Pure jQuery)
    $(document).on('click', '.btn-close-modal, .modal', function(e) {
        if ($(e.target).hasClass('modal') || $(e.target).hasClass('btn-close-modal')) {
            $('.modal').removeClass('show').fadeOut(120);
            $('body').css('overflow', 'auto');
        }
    });
    $('.modal-content').on('click', function(e) {
        e.stopPropagation();
    });

    // 4. Real-time Search & Filter Chips
    var activeFilter = 'all';

    function filterMobItems() {
        var q = $('#mobSearchInput').val().toLowerCase().trim();
        var matchCount = 0;

        if (q.length > 0) {
            $('#mobSearchClear').show();
        } else {
            $('#mobSearchClear').hide();
        }

        $('#mobCardsFeed .mob-group-card').each(function() {
            var rowName = $(this).data('name') || '';
            var rowType = $(this).data('type') || '';
            var rowInUse = $(this).data('inuse') || '';

            var matchesSearch = !q || rowName.indexOf(q) !== -1;
            var matchesChip = true;

            if (activeFilter === 'refundable') {
                matchesChip = rowType === 'refundable';
            } else if (activeFilter === 'non-refundable') {
                matchesChip = rowType === 'non-refundable';
            } else if (activeFilter === 'in-use') {
                matchesChip = rowInUse === 'yes';
            }

            if (matchesSearch && matchesChip) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#mobNoResults').removeClass('d-none');
        } else {
            $('#mobNoResults').addClass('d-none');
        }
    }

    $('#mobSearchInput').on('keyup input', filterMobItems);

    $('#mobSearchClear').on('click', function() {
        $('#mobSearchInput').val('');
        filterMobItems();
    });

    $('.mob-chip-item').on('click', function() {
        $('.mob-chip-item').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        filterMobItems();
    });
});
</script>
@endsection

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
   ARISE ERP - MOBILE FEES GROUP VIEW (THEME MATCHING ADMISSION / VISITOR / EXAM)
   ========================================================================== */

.mob-page-wrap {
    padding: 8px 10px 60px 10px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    font-size: 12px;
}
.mob-page-wrap * {
    box-sizing: border-box;
}

/* 1. Dark Navy Hero Banner */
.mob-hero-banner {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-hero-heading {
    font-size: 13.5px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
    line-height: 1.2;
}
.mob-session-tag {
    font-size: 9.5px;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 700;
}

/* Hero KPI Grid */
.mob-hero-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-hero-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 4px;
    text-align: center;
}
.mob-hero-kpi-tag {
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
.mob-hero-kpi-val {
    font-size: 13.5px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1;
}
.mob-hero-kpi-val.val-green { color: #4ade80; }
.mob-hero-kpi-val.val-cyan { color: #38bdf8; }
.mob-hero-kpi-val.val-amber { color: #fbbf24; }

/* Hero Action Buttons */
.mob-hero-action-row {
    display: flex;
    gap: 6px;
}
.mob-hero-action-btn {
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
.btn-action-add {
    background: #0284c7;
    color: #ffffff !important;
    flex: 1.5;
}
.btn-action-outline {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
}
.mob-hero-action-btn:active {
    transform: scale(0.97);
}

/* 2. Search Toolbar & Horizontal Filter Chips */
.mob-search-toolbar-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-box-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-box-wrap i.search-icon {
    position: absolute;
    left: 10px;
    color: #94a3b8;
    font-size: 11.5px;
}
.mob-search-text-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 30px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-text-input:focus {
    border-color: #002C54;
    background: #ffffff;
}
.mob-search-clear-btn {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
}

/* Filter Chips Strip */
.mob-filter-chips-strip {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
}
.mob-filter-chips-strip::-webkit-scrollbar {
    display: none;
}
.mob-filter-chip-item {
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
.mob-filter-chip-item.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 3. Data Card Listing */
.mob-cards-feed-wrap {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 12px;
}
.mob-data-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    padding: 9px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: border-color .1s ease;
}
.mob-data-card:active {
    border-color: #94a3b8;
}
.mob-card-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
}
.mob-card-title-text {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-status-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-transform: uppercase;
}
.status-pill-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.status-pill-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.status-pill-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Middle Details Row */
.mob-card-mid-row {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}

/* Bottom Action Row */
.mob-card-bottom-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-card-id-tag {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
}
.mob-btn-action-group {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* 4. Modals (Standard Theme Matching visitor / user view) */
.modal-dialog {
    margin: 12px auto;
    max-width: calc(100% - 24px);
}
.modal-content {
    border-radius: 4px !important;
    overflow: hidden !important;
    border: none !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
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
}
</style>
@endsection

@section('content')
<div class="mob-page-wrap">

    {{-- 1. Dark Navy Hero Banner --}}
    <div class="mob-hero-banner">
        <div class="mob-hero-top-row">
            <h1 class="mob-hero-heading">
                <i class="fa fa-folder-open text-info"></i> {{ __('fees.Fees Group') }}
            </h1>
            <span class="mob-session-tag">Session: {{ $currentSessionName }}</span>
        </div>

        {{-- 4 Metric Grid --}}
        <div class="mob-hero-kpi-grid">
            <div class="mob-hero-kpi-col">
                <span class="mob-hero-kpi-tag">Total</span>
                <span class="mob-hero-kpi-val">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="mob-hero-kpi-col">
                <span class="mob-hero-kpi-tag">Refund</span>
                <span class="mob-hero-kpi-val val-green">{{ $stats['refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-hero-kpi-col">
                <span class="mob-hero-kpi-tag">Standard</span>
                <span class="mob-hero-kpi-val val-cyan">{{ $stats['non_refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-hero-kpi-col">
                <span class="mob-hero-kpi-tag">In-Use</span>
                <span class="mob-hero-kpi-val val-amber">{{ $stats['in_use'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Top Action Row --}}
        <div class="mob-hero-action-row">
            <button type="button" class="mob-hero-action-btn btn-action-add" data-toggle="modal" data-target="#addFeesGroupModal" data-bs-toggle="modal" data-bs-target="#addFeesGroupModal">
                <i class="fa fa-plus-circle"></i> {{ __('fees.Add Fees Group') }}
            </button>
            <a href="{{ url('feesMaster') }}" class="mob-hero-action-btn btn-action-outline">
                <i class="fa fa-sliders"></i> Fees Master
            </a>
            <a href="{{ url('fee_dashboard') }}" class="mob-hero-action-btn btn-action-outline" style="max-width: 44px;" title="Back">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- 2. Compact Search & Filter Toolbar --}}
    <div class="mob-search-toolbar-card">
        <div class="mob-search-box-wrap">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="mobSearchField" class="mob-search-text-input" placeholder="Search fee groups...">
            <i class="fa fa-times-circle mob-search-clear-btn" id="mobSearchClearBtn"></i>
        </div>

        <div class="mob-filter-chips-strip">
            <div class="mob-filter-chip-item active" data-filter="all">All ({{ $stats['total'] ?? 0 }})</div>
            <div class="mob-filter-chip-item" data-filter="refundable"><i class="fa fa-check-circle text-success mr-1"></i>Refundable ({{ $stats['refundable'] ?? 0 }})</div>
            <div class="mob-filter-chip-item" data-filter="non-refundable"><i class="fa fa-minus-circle text-info mr-1"></i>Standard ({{ $stats['non_refundable'] ?? 0 }})</div>
            <div class="mob-filter-chip-item" data-filter="in-use"><i class="fa fa-link text-warning mr-1"></i>In-Use ({{ $stats['in_use'] ?? 0 }})</div>
        </div>
    </div>

    {{-- 3. Data Card Feed --}}
    <div class="mob-cards-feed-wrap" id="mobCardsFeed">
        @if(!empty($dataview) && count($dataview) > 0)
            @php $srNo = 1; @endphp
            @foreach ($dataview as $item)
                @php
                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                    $isInUse = in_array($item->id, $inUseGroupIds);
                    $nameLower = strtolower($item->name ?? '');
                    $filterType = $isRefundable ? 'refundable' : 'non-refundable';
                @endphp
                <div class="mob-data-card" 
                     data-name="{{ $nameLower }}" 
                     data-type="{{ $filterType }}"
                     data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                    
                    {{-- Card Top Row: Name & Refundable Badge --}}
                    <div class="mob-card-top-row">
                        <div class="mob-card-title-text">
                            <i class="fa fa-money text-primary"></i>
                            <span>{{ $item->name ?? '' }}</span>
                        </div>
                        <div>
                            @if($isRefundable)
                                <span class="mob-status-pill status-pill-success">
                                    <i class="fa fa-check-circle"></i> Refundable
                                </span>
                            @else
                                <span class="mob-status-pill status-pill-secondary">
                                    <i class="fa fa-minus-circle"></i> Standard
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Card Middle Row: Usage & Sub-badges --}}
                    <div class="mob-card-mid-row">
                        @if($isInUse)
                            <span class="mob-status-pill status-pill-warning" title="Assigned in Fees Master / Receipts">
                                <i class="fa fa-link"></i> In-Use
                            </span>
                        @else
                            <span class="mob-status-pill status-pill-secondary" title="Unassigned">
                                <i class="fa fa-circle-o"></i> Unlinked
                            </span>
                        @endif

                        @if($item->fees_type === 'installment')
                            <span class="badge badge-light border" style="font-size:9.5px;">Installment</span>
                        @else
                            <span class="badge badge-light border" style="font-size:9.5px;">Full Payment</span>
                        @endif
                    </div>

                    {{-- Card Bottom Row: ID & Actions --}}
                    <div class="mob-card-bottom-row">
                        <span class="mob-card-id-tag">#{{ $srNo++ }} (ID: {{ $item->id }})</span>

                        <div class="mob-btn-action-group">
                            {{-- Edit Action --}}
                            <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                               class="btn btn-primary btn-xs {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                               title="Edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>

                            {{-- Delete Action --}}
                            @if(!$isInUse)
                                <a href="javascript:;" 
                                   class="btn btn-danger btn-xs btn-trigger-delete {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                   data-id="{{ $item->id }}" 
                                   data-name="{{ $item->name }}" 
                                   data-toggle="modal" 
                                   data-target="#Modal_id" 
                                   data-bs-toggle="modal" 
                                   data-bs-target="#Modal_id"
                                   title="Delete">
                                    <i class="fa fa-trash-o"></i> Delete
                                </a>
                            @else
                                <button type="button" class="btn btn-secondary btn-xs" disabled title="In Use">
                                    <i class="fa fa-lock"></i> Locked
                                </button>
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

    {{-- No Search Results Fallback --}}
    <div id="mobSearchEmpty" class="text-center py-4 text-muted bg-white rounded border d-none" style="font-size:12px;">
        <i class="fa fa-search fa-2x mb-2 text-secondary d-block"></i>
        No matching fee groups found.
    </div>

</div>

{{-- =========================================================================
   1. ADD FEES GROUP MODAL (Exact match with Arise Standard Modals)
   ========================================================================= --}}
<div class="modal fade" id="addFeesGroupModal" tabindex="-1" role="dialog" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h5 class="modal-title font-size-14" id="addModalTitle">
                    <i class="fa fa-plus-circle mr-1"></i> {{ __('fees.Add Fees Group') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickForm" action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    
                    {{-- Name Input --}}
                    <div class="form-group mb-3">
                        <label class="text-danger font-weight-bold" style="font-size:11.5px;">
                            {{ __('messages.Name') }}*
                        </label>
                        <input type="text" 
                               class="form-control form-control-sm @error('name') is-invalid @enderror" 
                               name="name" 
                               id="name" 
                               placeholder="{{ __('messages.Name') }}" 
                               value="{{ old('name') }}" 
                               required 
                               style="height:32px; font-size:12px; border-radius:2px;">
                        @error('name')
                            <span class="invalid-feedback d-block font-weight-bold" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Refund Fees Checkbox --}}
                    <div class="form-group p-2 rounded border bg-light mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <label for="mob_refund_fees_check" class="font-weight-bold mb-0 text-dark" style="font-size:11.5px; cursor:pointer;">
                                {{ __('Fees Refund') }}
                            </label>
                            <small class="text-muted d-block" style="font-size:10px;">Eligible for refund on admission cancellation?</small>
                        </div>
                        <input type="checkbox" id="mob_refund_fees_check" value="yes" onchange="updateMobRefundFees(this)" style="width:18px; height:18px; cursor:pointer;">
                        <input type="hidden" id="mob_fees_refund_input" name="fees_refund" value="no">
                    </div>

                </div>
                <div class="modal-footer py-2 bg-light justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" style="background:#002C54; border-color:#002C54; font-weight:700;">
                        {{ __('messages.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================================
   2. DELETE CONFIRMATION MODAL (Exact match with Modal_id in visitor/user views)
   ========================================================================= --}}
<div class="modal fade" id="Modal_id" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title font-size-14" id="deleteModalTitle">
                    <i class="fa fa-trash-o mr-1"></i> {{ __('messages.Delete Confirmation') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <p class="text-muted mb-1" style="font-size:11.5px;">{{ __('messages.Are you sure you want to delete') }}?</p>
                    <h6 class="font-weight-bold text-dark mb-0" id="delete_group_display_name"></h6>
                </div>
                <div class="modal-footer py-2 bg-light justify-content-center">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal" data-bs-dismiss="modal">
                        {{ __('messages.Close') }}
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold">
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
function updateMobRefundFees(checkbox) {
    document.getElementById('mob_fees_refund_input').value = checkbox.checked ? 'yes' : 'no';
}

$(document).ready(function() {
    // Delete Trigger handler
    $(document).on('click', '.btn-trigger-delete', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#delete_id').val(id);
        $('#delete_group_display_name').text('"' + name + '"');
    });

    // Real-time Search & Filter Chips
    var currentChipFilter = 'all';

    function runMobileFilter() {
        var query = $('#mobSearchField').val().toLowerCase().trim();
        var matchCount = 0;

        if (query.length > 0) {
            $('#mobSearchClearBtn').show();
        } else {
            $('#mobSearchClearBtn').hide();
        }

        $('#mobCardsFeed .mob-data-card').each(function() {
            var rowName = $(this).data('name') || '';
            var rowType = $(this).data('type') || '';
            var rowInUse = $(this).data('inuse') || '';

            var matchesSearch = !query || rowName.indexOf(query) !== -1;
            var matchesChip = true;

            if (currentChipFilter === 'refundable') {
                matchesChip = rowType === 'refundable';
            } else if (currentChipFilter === 'non-refundable') {
                matchesChip = rowType === 'non-refundable';
            } else if (currentChipFilter === 'in-use') {
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
            $('#mobSearchEmpty').removeClass('d-none');
        } else {
            $('#mobSearchEmpty').addClass('d-none');
        }
    }

    $('#mobSearchField').on('keyup input', runMobileFilter);

    $('#mobSearchClearBtn').on('click', function() {
        $('#mobSearchField').val('');
        runMobileFilter();
    });

    $('.mob-filter-chip-item').on('click', function() {
        $('.mob-filter-chip-item').removeClass('active');
        $(this).addClass('active');
        currentChipFilter = $(this).data('filter');
        runMobileFilter();
    });
});
</script>
@endsection

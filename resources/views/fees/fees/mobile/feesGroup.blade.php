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
   ARISE ERP - NATIVE MOBILE FEES GROUP MANAGEMENT
   Exact match with Admission, Enquiry & Examination mobile standards:
   - Sharp border-radius: 4px card / 2px-3px inputs
   - Signature Dark Navy Gradient Hero: #001833 to #002C54
   - Compact typography: 13.5px title, 11.5px search, 9.5px badges
   - 34px icon box & 28px action buttons
   ========================================================================== */

.mob-fg-container {
    padding: 8px 10px 60px 10px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
}
.mob-fg-container * {
    box-sizing: border-box;
}

/* 1. Glassmorphic Hero Card (Exact match with admission / enquiry mobile) */
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
    margin: 0;
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

/* 4 Metrics Glance Grid */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 5px 3px;
    text-align: center;
}
.mob-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 1px;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-refund { color: #4ade80; }
.mob-metric-val.val-std { color: #38bdf8; }
.mob-metric-val.val-inuse { color: #fbbf24; }

/* Fast Action Bar in Hero */
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
    flex: 1.5;
}
.mob-act-btn-outline {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn:active {
    transform: scale(0.97);
}

/* 2. Compact Search & Horizontal Filter Chips Toolbar */
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11.5px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    display: none;
}

/* Horizontal Chips */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
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
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 3. Feed Card List */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 12px;
}
.mob-item-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform .1s ease, border-color .1s ease;
}
.mob-item-card:active {
    border-color: #94a3b8;
}
.mob-card-left {
    display: flex;
    align-items: center;
    gap: 9px;
    flex: 1;
    min-width: 0;
}
.mob-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 4px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}
.mob-card-details {
    flex: 1;
    min-width: 0;
}
.mob-card-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-badges-row {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 9px;
    font-weight: 700;
    padding: 1.5px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.badge-pill-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-pill-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.badge-pill-warning { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Action Buttons */
.mob-card-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: 6px;
    flex-shrink: 0;
}
.mob-btn-action {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
}
.mob-btn-edit { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.mob-btn-del { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
.mob-btn-lock { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }

/* 4. Modals (Standard Arise Mobile Popups) */
.modal-mob .modal-content {
    border-radius: 4px;
    border: 1px solid #002C54;
    overflow: hidden;
}
.modal-mob .modal-header {
    background: #001833;
    color: #ffffff;
    padding: 8px 12px;
}
.modal-mob .modal-title {
    font-size: 13px;
    font-weight: 700;
}
.modal-mob .form-control {
    height: 30px;
    font-size: 12px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
}
.modal-mob .form-label {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
    text-transform: uppercase;
}
</style>
@endsection

@section('content')
<div class="mob-fg-container">

    {{-- 1. Hero Summary Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <h1 class="mob-hero-title">
                <i class="fa fa-folder-open text-info"></i> {{ __('fees.Fees Group') }}
            </h1>
            <span class="mob-session-pill">{{ $currentSessionName }}</span>
        </div>

        {{-- 4-Metric Grid --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box">
                <span class="mob-metric-tag">Total</span>
                <span class="mob-metric-val">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="mob-metric-box">
                <span class="mob-metric-tag">Refund</span>
                <span class="mob-metric-val val-refund">{{ $stats['refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-metric-box">
                <span class="mob-metric-tag">Standard</span>
                <span class="mob-metric-val val-std">{{ $stats['non_refundable'] ?? 0 }}</span>
            </div>
            <div class="mob-metric-box">
                <span class="mob-metric-tag">In-Use</span>
                <span class="mob-metric-val val-inuse">{{ $stats['in_use'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Fast Action Buttons in Hero --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-add" data-bs-toggle="modal" data-bs-target="#mobAddGroupModal">
                <i class="fa fa-plus-circle"></i> Add Fee Group
            </button>
            <a href="{{ url('feesMaster') }}" class="mob-act-btn mob-act-btn-outline">
                <i class="fa fa-sliders"></i> Fees Master
            </a>
        </div>
    </div>

    {{-- 2. Compact Search & Filter Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search fee groups...">
            <i class="fa fa-times-circle mob-search-clear" id="mobSearchClear"></i>
        </div>

        <div class="mob-chips-scroll">
            <div class="mob-chip active" data-filter="all">All ({{ $stats['total'] ?? 0 }})</div>
            <div class="mob-chip" data-filter="refundable">Refundable ({{ $stats['refundable'] ?? 0 }})</div>
            <div class="mob-chip" data-filter="non-refundable">Standard ({{ $stats['non_refundable'] ?? 0 }})</div>
            <div class="mob-chip" data-filter="in-use">In-Use ({{ $stats['in_use'] ?? 0 }})</div>
        </div>
    </div>

    {{-- 3. Touch Feed Card List --}}
    <div class="mob-feed-list" id="mobFeedList">
        @if(!empty($dataview) && count($dataview) > 0)
            @foreach ($dataview as $item)
                @php
                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                    $isInUse = in_array($item->id, $inUseGroupIds);
                    $nameLower = strtolower($item->name ?? '');
                    $filterType = $isRefundable ? 'refundable' : 'non-refundable';
                @endphp
                <div class="mob-item-card" 
                     data-name="{{ $nameLower }}" 
                     data-type="{{ $filterType }}"
                     data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                    <div class="mob-card-left">
                        <div class="mob-avatar-box">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="mob-card-details">
                            <div class="mob-card-name">{{ $item->name ?? '' }}</div>
                            <div class="mob-badges-row">
                                @if($isRefundable)
                                    <span class="badge-pill badge-pill-success">
                                        <i class="fa fa-check-circle"></i> Refundable
                                    </span>
                                @else
                                    <span class="badge-pill badge-pill-secondary">
                                        <i class="fa fa-minus-circle"></i> Standard
                                    </span>
                                @endif

                                @if($isInUse)
                                    <span class="badge-pill badge-pill-warning">
                                        <i class="fa fa-link"></i> In-Use
                                    </span>
                                @endif

                                @if($item->fees_type === 'installment')
                                    <span class="badge-pill badge-pill-secondary">
                                        Installment
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mob-card-actions">
                        {{-- Edit Action --}}
                        <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                           class="mob-btn-action mob-btn-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                           title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {{-- Delete Action --}}
                        @if(!$isInUse)
                            <button type="button" 
                                    class="mob-btn-action mob-btn-del btn-mob-del {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}" 
                                    title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        @else
                            <button type="button" class="mob-btn-action mob-btn-lock" title="Assigned" disabled>
                                <i class="fa fa-lock"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4 text-muted bg-white rounded border" style="font-size:12px;">
                <i class="fa fa-folder-open-o fa-2x mb-2 text-secondary d-block"></i>
                No fee groups found. Tap "+ Add Fee Group" to create one.
            </div>
        @endif
    </div>

    {{-- Empty State on Search Filter --}}
    <div id="mobNoResults" class="text-center py-4 text-muted bg-white rounded border d-none" style="font-size:12px;">
        <i class="fa fa-search fa-2x mb-2 text-secondary d-block"></i>
        No matching fee groups found.
    </div>

</div>

{{-- Add Fee Group Modal (Mobile) --}}
<div class="modal fade modal-mob" id="mobAddGroupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle text-info mr-1"></i> Add Fees Group</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Tuition Fee, Exam Fee" required>
                    </div>

                    <div class="bg-light p-2 rounded border mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-weight-bold" style="font-size:11.5px; color:#0f172a;">Refundable Fee</div>
                            <small class="text-muted" style="font-size:10px;">Eligible for refund on cancellation?</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="mob_refund_switch" onchange="document.getElementById('mob_fees_refund').value = this.checked ? 'yes' : 'no';">
                        </div>
                        <input type="hidden" id="mob_fees_refund" name="fees_refund" value="no">
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" style="background:#002C54; border-color:#002C54; font-weight:700;">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal (Mobile) --}}
<div class="modal fade modal-mob" id="mobDelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fa fa-trash-o text-danger mr-1"></i> Confirm Delete</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="mob_delete_id" name="delete_id">
                    <p class="mb-1 text-muted" style="font-size:11.5px;">Are you sure you want to delete:</p>
                    <h6 class="font-weight-bold text-dark mb-0" id="mob_delete_name"></h6>
                </div>
                <div class="modal-footer justify-content-center p-2 bg-light">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3" style="font-weight:700;">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Delete Handler
    $('.btn-mob-del').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#mob_delete_id').val(id);
        $('#mob_delete_name').text('"' + name + '"');
        $('#mobDelModal').modal('show');
    });

    // Real-time Search & Filter Chips
    var activeFilter = 'all';

    function filterMobileCards() {
        var term = $('#mobSearchInput').val().toLowerCase().trim();
        var visibleCount = 0;

        if (term.length > 0) {
            $('#mobSearchClear').show();
        } else {
            $('#mobSearchClear').hide();
        }

        $('#mobFeedList .mob-item-card').each(function() {
            var name = $(this).data('name') || '';
            var type = $(this).data('type') || '';
            var inuse = $(this).data('inuse') || '';

            var matchesSearch = !term || name.indexOf(term) !== -1;
            var matchesChip = true;

            if (activeFilter === 'refundable') {
                matchesChip = type === 'refundable';
            } else if (activeFilter === 'non-refundable') {
                matchesChip = type === 'non-refundable';
            } else if (activeFilter === 'in-use') {
                matchesChip = inuse === 'yes';
            }

            if (matchesSearch && matchesChip) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        if (visibleCount === 0) {
            $('#mobNoResults').removeClass('d-none');
        } else {
            $('#mobNoResults').addClass('d-none');
        }
    }

    $('#mobSearchInput').on('keyup input', filterMobileCards);

    $('#mobSearchClear').on('click', function() {
        $('#mobSearchInput').val('');
        filterMobileCards();
    });

    $('.mob-chip').on('click', function() {
        $('.mob-chip').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        filterMobileCards();
    });
});
</script>
@endsection

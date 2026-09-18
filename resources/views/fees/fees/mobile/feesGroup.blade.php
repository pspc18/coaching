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
   ARISE ERP - NATIVE MOBILE FEES GROUP DIRECTORY
   ========================================================================== */
.mob-fg-wrap {
    padding: 10px 12px 80px 12px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* 1. Glassmorphic Hero Card */
.mob-fg-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 6px;
    padding: 12px 14px;
    margin-bottom: 10px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-fg-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.mob-fg-hero-title {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}
.mob-fg-session-pill {
    font-size: 10px;
    background: rgba(6, 182, 212, 0.2);
    border: 1px solid rgba(6, 182, 212, 0.4);
    color: #38bdf8;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 700;
}

/* Metrics Glance Grid */
.mob-fg-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}
.mob-fg-metric-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 4px;
    padding: 6px;
    text-align: center;
}
.mob-fg-metric-tag {
    font-size: 9px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    margin-bottom: 2px;
}
.mob-fg-metric-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}

/* 2. Instant Search & Action Bar */
.mob-fg-search-bar {
    display: flex;
    gap: 8px;
    margin-bottom: 10px;
}
.mob-fg-search-box {
    flex: 1;
    position: relative;
}
.mob-fg-search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
}
.mob-fg-search-input {
    width: 100%;
    padding: 9px 12px 9px 32px;
    font-size: 13px;
    font-weight: 500;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    color: #0f172a;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.mob-fg-search-input:focus {
    border-color: #0284c7;
    outline: none;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-fg-btn-add {
    background: #002C54;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 0 14px;
    font-size: 12.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
    white-space: nowrap;
}

/* 3. Filter Chips */
.mob-fg-chips-scroll {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 8px;
    -webkit-overflow-scrolling: touch;
}
.mob-fg-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-fg-chip {
    flex-shrink: 0;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mob-fg-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}

/* 4. Touch Card List */
.mob-fg-card-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.mob-fg-card {
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.1s ease;
}
.mob-fg-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.mob-fg-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.mob-fg-card-info {
    flex: 1;
    min-width: 0;
}
.mob-fg-card-name {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-fg-card-badges {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.mob-fg-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-fg-badge-refund { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.mob-fg-badge-std { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.mob-fg-badge-inuse { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Actions */
.mob-fg-card-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-left: 8px;
}
.mob-fg-btn-act {
    width: 32px;
    height: 32px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    border: 1px solid transparent;
    text-decoration: none;
}
.mob-fg-btn-edit { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.mob-fg-btn-del { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
.mob-fg-btn-lock { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; }

/* 5. Mobile Modals & Slide-ups */
.mob-modal .modal-content {
    border-radius: 8px;
    border: none;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.mob-modal .modal-header {
    background: #001833;
    color: #ffffff;
    padding: 12px 14px;
}
.mob-modal .modal-title {
    font-size: 14px;
    font-weight: 700;
}
</style>
@endsection

@section('content')
<div class="mob-fg-wrap">
    
    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-fg-hero">
        <div class="mob-fg-hero-top">
            <h1 class="mob-fg-hero-title">
                <i class="fa fa-folder-open text-cyan"></i> Fees Group Directory
            </h1>
            <span class="mob-fg-session-pill">Session: {{ $currentSessionName }}</span>
        </div>
        
        <div class="mob-fg-metrics-grid">
            <div class="mob-fg-metric-col">
                <div class="mob-fg-metric-tag">Total</div>
                <div class="mob-fg-metric-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
            <div class="mob-fg-metric-col">
                <div class="mob-fg-metric-tag">Refund</div>
                <div class="mob-fg-metric-val text-success">{{ $stats['refundable'] ?? 0 }}</div>
            </div>
            <div class="mob-fg-metric-col">
                <div class="mob-fg-metric-tag">Standard</div>
                <div class="mob-fg-metric-val text-info">{{ $stats['non_refundable'] ?? 0 }}</div>
            </div>
            <div class="mob-fg-metric-col">
                <div class="mob-fg-metric-tag">In-Use</div>
                <div class="mob-fg-metric-val text-warning">{{ $stats['in_use'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- 2. Instant Search & Quick Add Trigger --}}
    <div class="mob-fg-search-bar">
        <div class="mob-fg-search-box">
            <i class="fa fa-search mob-fg-search-icon"></i>
            <input type="text" id="mobGroupSearch" class="mob-fg-search-input" placeholder="Search fee groups...">
        </div>
        <button type="button" class="mob-fg-btn-add" data-bs-toggle="modal" data-bs-target="#mobAddModal">
            <i class="fa fa-plus"></i> Add
        </button>
    </div>

    {{-- 3. Filter Chips --}}
    <div class="mob-fg-chips-scroll">
        <div class="mob-fg-chip active" data-filter="all">All Groups ({{ $stats['total'] ?? 0 }})</div>
        <div class="mob-fg-chip" data-filter="refundable">Refundable ({{ $stats['refundable'] ?? 0 }})</div>
        <div class="mob-fg-chip" data-filter="non-refundable">Non-Refundable ({{ $stats['non_refundable'] ?? 0 }})</div>
        <div class="mob-fg-chip" data-filter="in-use">In-Use ({{ $stats['in_use'] ?? 0 }})</div>
    </div>

    {{-- 4. Mobile Cards List --}}
    <div class="mob-fg-card-list" id="mobGroupList">
        @if(!empty($dataview) && count($dataview) > 0)
            @foreach ($dataview as $item)
                @php
                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                    $isInUse = in_array($item->id, $inUseGroupIds);
                    $filterType = $isRefundable ? 'refundable' : 'non-refundable';
                @endphp
                <div class="mob-fg-card" 
                     data-name="{{ strtolower($item->name ?? '') }}" 
                     data-type="{{ $filterType }}"
                     data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                    <div class="mob-fg-card-left">
                        <div class="mob-fg-card-icon">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="mob-fg-card-info">
                            <div class="mob-fg-card-name">{{ $item->name ?? '' }}</div>
                            <div class="mob-fg-card-badges">
                                @if($isRefundable)
                                    <span class="mob-fg-badge mob-fg-badge-refund">
                                        <i class="fa fa-check-circle"></i> Refundable
                                    </span>
                                @else
                                    <span class="mob-fg-badge mob-fg-badge-std">
                                        <i class="fa fa-minus-circle"></i> Non-Refundable
                                    </span>
                                @endif

                                @if($isInUse)
                                    <span class="mob-fg-badge mob-fg-badge-inuse">
                                        <i class="fa fa-link"></i> In-Use
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mob-fg-card-actions">
                        <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                           class="mob-fg-btn-act mob-fg-btn-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                           title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>

                        @if(!$isInUse)
                            <button type="button" 
                                    class="mob-fg-btn-act mob-fg-btn-del btn-mob-delete {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        @else
                            <button type="button" class="mob-fg-btn-act mob-fg-btn-lock" title="In use" disabled>
                                <i class="fa fa-lock"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4 text-muted bg-white rounded border">
                <i class="fa fa-folder-open-o fa-2x mb-2 text-secondary d-block"></i>
                No fee groups found. Tap "+ Add" to create one.
            </div>
        @endif
    </div>

    {{-- Empty Search Fallback --}}
    <div id="mobNoResults" class="text-center py-4 text-muted bg-white rounded border d-none mt-2">
        <i class="fa fa-search fa-2x mb-2 text-secondary d-block"></i>
        No matching fee groups found.
    </div>
</div>

{{-- Mobile Add Fee Group Modal --}}
<div class="modal fade mob-modal" id="mobAddModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle text-info mr-1"></i> Add Fee Group</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold" style="font-size:12.5px;">Group Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Tuition Fee, Exam Fee" required>
                    </div>

                    <div class="bg-light p-3 rounded border mb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="font-weight-bold text-dark" style="font-size:12.5px;">Refundable Fee</div>
                            <small class="text-muted">Is this fee refunded on admission withdrawal?</small>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="mob_refund_switch" onchange="document.getElementById('mob_fees_refund').value = this.checked ? 'yes' : 'no';">
                        </div>
                        <input type="hidden" id="mob_fees_refund" name="fees_refund" value="no">
                    </div>
                </div>
                <div class="modal-footer p-2 bg-light">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4" style="background:#002C54; border-color:#002C54; font-weight:700;">Save Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Mobile Delete Modal --}}
<div class="modal fade mob-modal" id="mobDelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fa fa-trash-o text-danger mr-1"></i> Delete Fee Group</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="mob_delete_id" name="delete_id">
                    <p class="mb-1 text-muted" style="font-size:12.5px;">Are you sure you want to delete</p>
                    <h6 class="font-weight-bold text-dark mb-0" id="mob_delete_name"></h6>
                </div>
                <div class="modal-footer justify-content-center p-2 bg-light">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Delete Modal Handler
    $('.btn-mob-delete').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#mob_delete_id').val(id);
        $('#mob_delete_name').text('"' + name + '"');
        $('#mobDelModal').modal('show');
    });

    // Real-time Search & Filter Chips
    var activeFilter = 'all';
    
    function filterCards() {
        var term = $('#mobGroupSearch').val().toLowerCase().trim();
        var visibleCount = 0;

        $('#mobGroupList .mob-fg-card').each(function() {
            var name = $(this).data('name');
            var type = $(this).data('type');
            var inuse = $(this).data('inuse');

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

    $('#mobGroupSearch').on('input', filterCards);

    $('.mob-fg-chip').on('click', function() {
        $('.mob-fg-chip').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        filterCards();
    });
});
</script>
@endsection

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
   ARISE ERP - 100% NATIVE MOBILE APP FEES GROUP MODULE
   Ultra-modern iOS / Android Grade Experience:
   - Glassmorphic Hero Card with Circular Accent Glow
   - Segmented Filter Strip with Live Counters
   - Native Touch Cards with Icon Badges & Action Drawers
   - Thumb-friendly Floating Action Button (FAB)
   - Slide-Up Bottom Sheet for Add / Edit Actions
   ========================================================================== */

:root {
    --mob-navy-900: #001428;
    --mob-navy-800: #002C54;
    --mob-navy-700: #0a4275;
    --mob-cyan-500: #0284c7;
    --mob-cyan-400: #38bdf8;
    --mob-emerald-500: #10b981;
    --mob-amber-500: #f59e0b;
    --mob-rose-500: #ef4444;
}

.native-mob-page {
    padding: 8px 10px 85px 10px;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #0f172a;
    -webkit-tap-highlight-color: transparent;
}
.native-mob-page * {
    box-sizing: border-box;
}

/* 1. Native Hero Card */
.native-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 55%, #0a4275 100%);
    border: 1px solid rgba(56, 189, 248, 0.28);
    border-radius: 6px;
    padding: 12px 14px;
    color: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 20, 40, 0.25);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
}
.native-hero-card::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(56, 189, 248, 0) 70%);
    pointer-events: none;
}
.native-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
.native-hero-cluster {
    display: flex;
    align-items: center;
    gap: 10px;
}
.native-hero-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7, #38bdf8);
    border: 2px solid rgba(255, 255, 255, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    flex-shrink: 0;
}
.native-hero-title {
    font-size: 14px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    margin: 0;
}
.native-hero-sub {
    font-size: 9.5px;
    color: #93c5fd;
    font-weight: 600;
    margin: 1px 0 0 0;
}
.native-session-pill {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 9.5px;
    font-weight: 700;
    background: rgba(56, 189, 248, 0.18);
    border: 1px solid rgba(56, 189, 248, 0.35);
    color: #38bdf8;
}

/* 4-Metric Glance Grid */
.native-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.native-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
}
.native-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
}
.native-metric-val {
    font-size: 14.5px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.native-metric-val.val-green { color: #4ade80; }
.native-metric-val.val-cyan { color: #38bdf8; }
.native-metric-val.val-amber { color: #fbbf24; }

/* Quick Action Toolbar in Hero */
.native-hero-actions {
    display: flex;
    gap: 6px;
}
.native-btn-hero {
    flex: 1;
    height: 32px;
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
.native-btn-hero-primary {
    background: #0284c7;
    color: #ffffff !important;
    flex: 1.4;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
}
.native-btn-hero-outline {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.native-btn-hero:active {
    transform: scale(0.97);
}

/* 2. Native Search & Filter Toolbar */
.native-search-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.native-search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 6px;
}
.native-search-input-wrap i.search-icon {
    position: absolute;
    left: 10px;
    color: #94a3b8;
    font-size: 12px;
}
.native-search-input {
    width: 100%;
    height: 32px;
    padding: 0 28px 0 30px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    transition: all .15s ease;
}
.native-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.native-search-clear {
    position: absolute;
    right: 9px;
    color: #94a3b8;
    font-size: 13px;
    cursor: pointer;
    display: none;
}

/* Segmented Chips */
.native-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    -webkit-overflow-scrolling: touch;
}
.native-chips-scroll::-webkit-scrollbar {
    display: none;
}
.native-chip {
    flex-shrink: 0;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3.5px 9px;
    border-radius: 14px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all .12s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.native-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 3px rgba(0, 44, 84, 0.2);
}

/* 3. Native Feed Cards List */
.native-cards-feed {
    display: flex;
    flex-direction: column;
    gap: 7px;
    margin-bottom: 12px;
}
.native-group-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 10px 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform .1s ease, border-color .1s ease;
    position: relative;
}
.native-group-card:active {
    border-color: #94a3b8;
    transform: scale(0.995);
}
.native-card-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.native-avatar-icon {
    width: 36px;
    height: 36px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    flex-shrink: 0;
}
.avatar-green { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
.avatar-cyan { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }

.native-card-details {
    flex: 1;
    min-width: 0;
}
.native-card-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.25;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.native-card-badges {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.native-badge-pill {
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
.badge-pill-refundable { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-pill-standard { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
.badge-pill-inuse { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

/* Action Buttons on Right of Card */
.native-card-actions {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: 8px;
    flex-shrink: 0;
}
.native-btn-act {
    width: 30px;
    height: 30px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .12s ease;
}
.native-btn-act:active {
    transform: scale(0.92);
}
.btn-act-edit { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.btn-act-del { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
.btn-act-lock { background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed; }

/* 4. Thumb-Friendly Floating Action Button (FAB) */
.native-fab-btn {
    position: fixed;
    right: 14px;
    bottom: 20px;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7 0%, #002C54 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 16px rgba(0, 44, 84, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.3);
    z-index: 1000;
    cursor: pointer;
    transition: all .2s ease;
}
.native-fab-btn:active {
    transform: scale(0.92);
    box-shadow: 0 2px 8px rgba(0, 44, 84, 0.3);
}

/* 5. Native Slide-Up Bottom Sheet Modal */
.native-bottom-sheet .modal-dialog {
    margin: 0;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    max-width: 100%;
}
.native-bottom-sheet .modal-content {
    border-radius: 12px 12px 0 0;
    border: none;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.25);
    background: #ffffff;
    overflow: hidden;
}
.native-sheet-drag-handle {
    width: 36px;
    height: 4px;
    border-radius: 2px;
    background: #cbd5e1;
    margin: 8px auto 4px auto;
}
.native-sheet-header {
    background: #001833;
    color: #ffffff;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.native-sheet-title {
    font-size: 13.5px;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.native-sheet-body {
    padding: 14px;
    background: #ffffff;
}
.native-form-group {
    margin-bottom: 12px;
}
.native-form-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.native-form-control {
    width: 100%;
    height: 32px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 500;
    color: #0f172a;
    background-color: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
}
.native-form-control:focus {
    border-color: #0284c7;
    outline: 0;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* Toggle Card inside Drawer */
.native-drawer-switch-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 10px 12px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.native-switch-text {
    display: flex;
    flex-direction: column;
}
.native-switch-title {
    font-size: 11.5px;
    font-weight: 700;
    color: #0f172a;
}
.native-switch-desc {
    font-size: 10px;
    color: #64748b;
}

/* Delete Modal */
.native-del-modal .modal-content {
    border-radius: 6px;
    border: 1px solid #002C54;
    overflow: hidden;
}
.native-del-modal .modal-header {
    background: #001833;
    color: #ffffff;
    padding: 9px 12px;
}
</style>
@endsection

@section('content')
<div class="native-mob-page">

    {{-- 1. Hero Snapshot Card --}}
    <div class="native-hero-card">
        <div class="native-hero-top">
            <div class="native-hero-cluster">
                <div class="native-hero-avatar">
                    <i class="fa fa-folder-open-o"></i>
                </div>
                <div>
                    <h1 class="native-hero-title">{{ __('fees.Fees Group') }}</h1>
                    <p class="native-hero-sub">Configure fee heads & refund policies</p>
                </div>
            </div>
            <span class="native-session-pill">Session: {{ $currentSessionName }}</span>
        </div>

        {{-- 4 Metric Grid --}}
        <div class="native-metrics-grid">
            <div class="native-metric-box">
                <span class="native-metric-tag">Total</span>
                <span class="native-metric-val">{{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="native-metric-box">
                <span class="native-metric-tag">Refund</span>
                <span class="native-metric-val val-green">{{ $stats['refundable'] ?? 0 }}</span>
            </div>
            <div class="native-metric-box">
                <span class="native-metric-tag">Standard</span>
                <span class="native-metric-val val-cyan">{{ $stats['non_refundable'] ?? 0 }}</span>
            </div>
            <div class="native-metric-box">
                <span class="native-metric-tag">In-Use</span>
                <span class="native-metric-val val-amber">{{ $stats['in_use'] ?? 0 }}</span>
            </div>
        </div>

        {{-- Hero Actions Bar --}}
        <div class="native-hero-actions">
            <button type="button" class="native-btn-hero native-btn-hero-primary" data-bs-toggle="modal" data-bs-target="#nativeAddSheet">
                <i class="fa fa-plus-circle"></i> Add Fee Group
            </button>
            <a href="{{ url('feesMaster') }}" class="native-btn-hero native-btn-hero-outline">
                <i class="fa fa-sliders"></i> Fees Master
            </a>
            <a href="{{ url('fee_dashboard') }}" class="native-btn-hero native-btn-hero-outline" style="max-width: 44px;" title="Back">
                <i class="fa fa-arrow-left"></i>
            </a>
        </div>
    </div>

    {{-- 2. Search & Segmented Filter Toolbar --}}
    <div class="native-search-card">
        <div class="native-search-input-wrap">
            <i class="fa fa-search search-icon"></i>
            <input type="text" id="nativeSearchInput" class="native-search-input" placeholder="Search fee groups...">
            <i class="fa fa-times-circle native-search-clear" id="nativeSearchClear"></i>
        </div>

        <div class="native-chips-scroll">
            <div class="native-chip active" data-filter="all">All ({{ $stats['total'] ?? 0 }})</div>
            <div class="native-chip" data-filter="refundable"><i class="fa fa-check-circle text-success"></i> Refundable ({{ $stats['refundable'] ?? 0 }})</div>
            <div class="native-chip" data-filter="non-refundable"><i class="fa fa-minus-circle text-info"></i> Standard ({{ $stats['non_refundable'] ?? 0 }})</div>
            <div class="native-chip" data-filter="in-use"><i class="fa fa-link text-warning"></i> In-Use ({{ $stats['in_use'] ?? 0 }})</div>
        </div>
    </div>

    {{-- 3. Native Card Feed List --}}
    <div class="native-cards-feed" id="nativeCardFeed">
        @if(!empty($dataview) && count($dataview) > 0)
            @foreach ($dataview as $item)
                @php
                    $isRefundable = strtolower($item->fees_refund ?? '') === 'yes';
                    $isInUse = in_array($item->id, $inUseGroupIds);
                    $nameLower = strtolower($item->name ?? '');
                    $filterType = $isRefundable ? 'refundable' : 'non-refundable';
                @endphp
                <div class="native-group-card" 
                     data-name="{{ $nameLower }}" 
                     data-type="{{ $filterType }}"
                     data-inuse="{{ $isInUse ? 'yes' : 'no' }}">
                    <div class="native-card-left">
                        <div class="native-avatar-icon {{ $isRefundable ? 'avatar-green' : 'avatar-cyan' }}">
                            <i class="fa {{ $isRefundable ? 'fa-refresh' : 'fa-money' }}"></i>
                        </div>
                        <div class="native-card-details">
                            <div class="native-card-title">{{ $item->name ?? '' }}</div>
                            <div class="native-card-badges">
                                @if($isRefundable)
                                    <span class="native-badge-pill badge-pill-refundable">
                                        <i class="fa fa-check-circle"></i> Refundable
                                    </span>
                                @else
                                    <span class="native-badge-pill badge-pill-standard">
                                        <i class="fa fa-minus-circle"></i> Standard
                                    </span>
                                @endif

                                @if($isInUse)
                                    <span class="native-badge-pill badge-pill-inuse">
                                        <i class="fa fa-link"></i> In-Use
                                    </span>
                                @endif

                                @if($item->fees_type === 'installment')
                                    <span class="native-badge-pill badge-pill-standard">
                                        Installment
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="native-card-actions">
                        {{-- Edit Button --}}
                        <a href="{{ url('feesGroupEdit') }}/{{ $item->id }}" 
                           class="native-btn-act btn-act-edit {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" 
                           title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>

                        {{-- Delete Button --}}
                        @if(!$isInUse)
                            <button type="button" 
                                    class="native-btn-act btn-act-del btn-native-delete {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}" 
                                    title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </button>
                        @else
                            <button type="button" class="native-btn-act btn-act-lock" title="In use" disabled>
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
    <div id="nativeNoResults" class="text-center py-4 text-muted bg-white rounded border d-none" style="font-size:12px;">
        <i class="fa fa-search fa-2x mb-2 text-secondary d-block"></i>
        No matching fee groups found.
    </div>

</div>

{{-- 4. Thumb-Friendly Floating Action Button (FAB) --}}
<div class="native-fab-btn" data-bs-toggle="modal" data-bs-target="#nativeAddSheet" title="Add Fee Group">
    <i class="fa fa-plus"></i>
</div>

{{-- 5. Native Slide-Up Bottom Sheet Modal for Adding Group --}}
<div class="modal fade native-bottom-sheet" id="nativeAddSheet" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-bottom" role="document">
        <div class="modal-content">
            <div class="native-sheet-drag-handle"></div>
            <div class="native-sheet-header">
                <h5 class="native-sheet-title">
                    <i class="fa fa-plus-circle text-info"></i> {{ __('fees.Add Fees Group') }}
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="native-sheet-body">
                    {{-- Group Name Input --}}
                    <div class="native-form-group">
                        <label class="native-form-label">{{ __('messages.Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="native-form-control" placeholder="e.g. Tuition Fee, Exam Fee" required autofocus>
                    </div>

                    {{-- Refundable Switch --}}
                    <div class="native-drawer-switch-card">
                        <div class="native-switch-text">
                            <span class="native-switch-title">Refundable Fee</span>
                            <span class="native-switch-desc">Is this fee refundable on admission cancellation?</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="sheet_refund_toggle" onchange="document.getElementById('sheet_fees_refund').value = this.checked ? 'yes' : 'no';">
                        </div>
                        <input type="hidden" id="sheet_fees_refund" name="fees_refund" value="no">
                    </div>

                    {{-- Bottom Submit Button --}}
                    <button type="submit" class="native-btn-hero native-btn-hero-primary w-100" style="height:36px; font-size:12.5px; border-radius:3px;">
                        <i class="fa fa-check mr-1"></i> Save Fee Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 6. Native Delete Confirmation Modal --}}
<div class="modal fade native-del-modal" id="nativeDelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="native-del-modal-header modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold" style="font-size:12.5px;"><i class="fa fa-trash-o text-danger mr-1"></i> {{ __('messages.Delete Confirmation') }}</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('feesGroupDelete') }}" method="post">
                @csrf
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="native_delete_id" name="delete_id">
                    <p class="mb-1 text-muted" style="font-size:11.5px;">Are you sure you want to permanently delete:</p>
                    <h6 class="font-weight-bold text-dark mb-0" id="native_delete_name"></h6>
                </div>
                <div class="modal-footer justify-content-center p-2 bg-light">
                    <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3 font-weight-bold" style="background:#dc2626; border-color:#dc2626;">{{ __('messages.Delete') }}</button>
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
    $('.btn-native-delete').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#native_delete_id').val(id);
        $('#native_delete_name').text('"' + name + '"');
        $('#nativeDelModal').modal('show');
    });

    // Real-time Search & Segmented Filter Chips
    var activeFilter = 'all';

    function filterCards() {
        var term = $('#nativeSearchInput').val().toLowerCase().trim();
        var visibleCount = 0;

        if (term.length > 0) {
            $('#nativeSearchClear').show();
        } else {
            $('#nativeSearchClear').hide();
        }

        $('#nativeCardFeed .native-group-card').each(function() {
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
            $('#nativeNoResults').removeClass('d-none');
        } else {
            $('#nativeNoResults').addClass('d-none');
        }
    }

    $('#nativeSearchInput').on('keyup input', filterCards);

    $('#nativeSearchClear').on('click', function() {
        $('#nativeSearchInput').val('');
        filterCards();
    });

    $('.native-chip').on('click', function() {
        $('.native-chip').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        filterCards();
    });
});
</script>
@endsection

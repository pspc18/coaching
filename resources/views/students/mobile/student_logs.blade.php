@extends('layout.mobile_app')

@php
    $currentSessionName = Session::get('session_name') ?? date('Y');
    $currentBranchName = Session::get('branch_name') ?? 'Main Branch';
    
    $todayStart = \Carbon\Carbon::now()->startOfDay()->timestamp;
    $weekStart = \Carbon\Carbon::now()->subDays(7)->timestamp;
    $monthStart = \Carbon\Carbon::now()->subDays(30)->timestamp;

    $hasActiveFilter = !empty($search['class_type_id']) || !empty($search['admissionNo']) || !empty($search['name']);
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE STUDENT APP LOGS THEME
   Strictly matched with Admission, Enquiry & Credentials Mobile Views
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
    padding: 6px 3px;
    text-align: center;
    transition: background .12s ease;
}
.mob-metric-tag {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    white-space: nowrap;
}
.mob-metric-val {
    font-size: 14px;
    font-weight: 800;
    line-height: 1.2;
    margin: 2px 0 1px;
    color: #ffffff;
}
.mob-metric-box.metric-sky .mob-metric-val { color: #38bdf8; }
.mob-metric-box.metric-green .mob-metric-val { color: #34d399; }
.mob-metric-box.metric-amber .mob-metric-val { color: #fbbf24; }
.mob-metric-box.metric-rose .mob-metric-val { color: #f87171; }

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 31px;
    padding: 0 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    white-space: nowrap;
}
.mob-act-btn-primary {
    background: #0284c7;
    color: #ffffff !important;
}
.mob-act-btn-light {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn:active {
    transform: scale(0.97);
}

/* 2. Recency Quick Filter Tabs */
.mob-seg-bar {
    display: flex;
    background: #e2e8f0;
    padding: 2.5px;
    border-radius: 4px;
    margin-bottom: 8px;
    gap: 3px;
    overflow-x: auto;
    scrollbar-width: none;
}
.mob-seg-bar::-webkit-scrollbar {
    display: none;
}
.mob-seg-btn {
    flex: 1;
    min-width: 65px;
    padding: 5px 4px;
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    border: none;
    background: transparent;
    border-radius: 3px;
    cursor: pointer;
    text-align: center;
    transition: all .15s ease;
    white-space: nowrap;
}
.mob-seg-btn.active {
    background: #002C54;
    color: #ffffff;
    font-weight: 800;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
}
.mob-seg-pill {
    display: inline-block;
    padding: 1px 4px;
    font-size: 8.5px;
    border-radius: 10px;
    background: rgba(0,0,0,0.08);
    margin-left: 2px;
}
.mob-seg-btn.active .mob-seg-pill {
    background: rgba(255,255,255,0.25);
    color: #ffffff;
}

/* 3. Search & Filter Toolbar */
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
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i {
    position: absolute;
    left: 8px;
    color: #94a3b8;
    font-size: 11px;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 24px 0 26px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-clear-search {
    position: absolute;
    right: 6px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    display: none;
    font-size: 11px;
}
.mob-filter-btn {
    height: 30px;
    padding: 0 9px;
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
    gap: 4px;
    white-space: nowrap;
}
.mob-filter-btn.active-filter {
    background: #e0f2fe;
    border-color: #0284c7;
    color: #0284c7;
}

/* 4. Logs Card Feed Item */
.mob-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.mob-log-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.mob-log-card.recency-today { border-left: 3px solid #10b981; }
.mob-log-card.recency-week { border-left: 3px solid #0284c7; }
.mob-log-card.recency-month { border-left: 3px solid #f59e0b; }
.mob-log-card.recency-older { border-left: 3px solid #94a3b8; }

.mob-card-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-avatar-box {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #002C54;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}
.mob-head-info {
    flex: 1;
    min-width: 0;
}
.mob-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
}
.mob-student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none !important;
}
.mob-pills-row {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.mob-class-badge {
    font-size: 9px;
    font-weight: 800;
    background: #e0f2fe;
    color: #0284c7;
    padding: 1px 5px;
    border-radius: 2px;
}
.mob-adm-badge {
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #e2e8f0;
}
.mob-uid-badge {
    font-size: 9px;
    font-weight: 700;
    background: #fef3c7;
    color: #b45309;
    padding: 1px 5px;
    border-radius: 2px;
    border: 1px solid #fde68a;
    font-family: monospace;
}
.mob-recency-pill {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 10px;
    white-space: nowrap;
    text-transform: uppercase;
}
.mob-recency-pill.pill-today { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.mob-recency-pill.pill-week { background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
.mob-recency-pill.pill-month { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
.mob-recency-pill.pill-older { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

/* Meta Grid */
.mob-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 11px;
    margin-bottom: 8px;
    color: #334155;
}
.mob-meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.mob-meta-label {
    color: #64748b;
    font-size: 10px;
    font-weight: 600;
}
.mob-meta-val {
    font-weight: 700;
    color: #0f172a;
}
.mob-phone-link {
    color: #002C54;
    font-weight: 700;
    text-decoration: none !important;
}
.mob-wa-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 3px;
    background: #22c55e;
    color: #ffffff !important;
    font-size: 11px;
    margin-left: 2px;
    text-decoration: none !important;
}

/* Card Bottom Strip (Last Login & Profile action) */
.mob-login-strip {
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
    padding: 6px 8px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 10.5px;
}
.mob-login-time {
    display: flex;
    flex-direction: column;
    min-width: 0;
}
.mob-exact-time {
    font-weight: 700;
    color: #1e293b;
    font-size: 10.5px;
}
.mob-rel-time {
    font-size: 9.5px;
    color: #64748b;
}
.mob-btn-view-profile {
    padding: 3px 8px;
    font-size: 10px;
    font-weight: 700;
    background: #002C54;
    color: #ffffff !important;
    border-radius: 3px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}
.mob-btn-view-profile:active {
    background: #0284c7;
}

/* 5. Bottom Sheet Filter Modal */
.mob-filter-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.6);
    backdrop-filter: blur(2px);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: opacity .2s ease, visibility .2s ease;
}
.mob-filter-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-filter-sheet {
    position: fixed;
    bottom: -100%;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 4px 4px 0 0;
    border-top: 1px solid #002C54;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-filter-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 12px;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.mob-sheet-title {
    font-size: 12.5px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.mob-sheet-body {
    padding: 12px;
    overflow-y: auto;
    flex: 1;
}
.mob-form-group {
    margin-bottom: 9px;
}
.mob-form-label {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 3px;
    display: block;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.mob-form-select, .mob-form-input {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    font-size: 11.5px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-sheet-footer {
    padding: 9px 12px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 8px;
}
.btn-sheet-apply {
    flex: 2;
    height: 33px;
    background: #0284c7;
    color: #ffffff;
    border: none;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}
.btn-sheet-reset {
    flex: 1;
    height: 33px;
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Empty State */
.mob-empty-feed {
    text-align: center;
    padding: 36px 16px;
    color: #64748b;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 8px 0;
}
.mob-empty-feed .empty-icon {
    font-size: 34px;
    color: #94a3b8;
    margin-bottom: 8px;
}
.mob-empty-feed h6 {
    font-size: 13px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 3px;
}
.mob-empty-feed p {
    font-size: 11px;
    margin: 0;
    color: #64748b;
}

/* Pagination Bar */
.mob-pag-bar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 7px 10px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}
.mob-pag-info {
    font-weight: 600;
    color: #475569;
}
.mob-pag-btns {
    display: flex;
    gap: 5px;
}
.mob-pag-link {
    height: 28px;
    padding: 0 9px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #0f172a;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    text-decoration: none !important;
}
.mob-pag-link:disabled, .mob-pag-link.disabled {
    opacity: .4;
    pointer-events: none;
}
</style>
@endsection

@section('content')
<div class="container-fluid p-2" style="max-width: 600px; margin: 0 auto;">

    {{-- 1. Hero Summary Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-history text-warning"></i> Student App Login Logs
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-calendar mr-1"></i> {{ $currentSessionName }}
            </div>
        </div>

        {{-- 4 KPI Metrics --}}
        <div class="mob-metrics-grid">
            <div class="mob-metric-box metric-sky">
                <div class="mob-metric-tag"><i class="fa fa-mobile"></i> Total</div>
                <div class="mob-metric-val">{{ number_format($summary['total'] ?? 0) }}</div>
            </div>
            <div class="mob-metric-box metric-green">
                <div class="mob-metric-tag"><i class="fa fa-check-circle"></i> Today</div>
                <div class="mob-metric-val">{{ number_format($summary['today'] ?? 0) }}</div>
            </div>
            <div class="mob-metric-box metric-amber">
                <div class="mob-metric-tag"><i class="fa fa-calendar-check-o"></i> 7 Days</div>
                <div class="mob-metric-val">{{ number_format($summary['week'] ?? 0) }}</div>
            </div>
            <div class="mob-metric-box metric-rose">
                <div class="mob-metric-tag"><i class="fa fa-clock-o"></i> 30 Days</div>
                <div class="mob-metric-val">{{ number_format($summary['month'] ?? 0) }}</div>
            </div>
        </div>

        {{-- Fast Action Buttons --}}
        <div class="mob-actions-bar">
            <button type="button" class="mob-act-btn mob-act-btn-light" onclick="exportLogsCSV()">
                <i class="fa fa-file-excel-o text-success mr-1"></i> Export CSV
            </button>
            <a href="{{ url('student_logs?layout=mobile') }}" class="mob-act-btn mob-act-btn-light">
                <i class="fa fa-refresh mr-1"></i> Refresh
            </a>
            <button type="button" class="mob-act-btn mob-act-btn-primary" onclick="openFilterSheet()">
                <i class="fa fa-filter mr-1"></i> Filters
            </button>
        </div>
    </div>

    {{-- 2. Recency Quick Filter Tabs --}}
    <div class="mob-seg-bar">
        <button type="button" class="mob-seg-btn active" data-recency="all" onclick="filterByRecency('all', this)">
            All <span class="mob-seg-pill">{{ number_format($summary['total'] ?? 0) }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-recency="today" onclick="filterByRecency('today', this)">
            🟢 Today <span class="mob-seg-pill">{{ number_format($summary['today'] ?? 0) }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-recency="week" onclick="filterByRecency('week', this)">
            🔵 7d <span class="mob-seg-pill">{{ number_format($summary['week'] ?? 0) }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-recency="month" onclick="filterByRecency('month', this)">
            🟡 30d <span class="mob-seg-pill">{{ number_format($summary['month'] ?? 0) }}</span>
        </button>
        <button type="button" class="mob-seg-btn" data-recency="older" onclick="filterByRecency('older', this)">
            ⚪ Older <span class="mob-seg-pill">{{ number_format(max(0, ($summary['total'] ?? 0) - ($summary['month'] ?? 0))) }}</span>
        </button>
    </div>

    {{-- 3. Search & Filter Toolbar --}}
    <div class="mob-search-toolbar">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="mobSearchInput" class="mob-search-input" placeholder="Search student, father, mobile, adm..." value="{{ $search['name'] ?? '' }}">
            <button type="button" id="mobClearSearch" class="mob-clear-search" onclick="clearSearch()">&times;</button>
        </div>
        <button type="button" class="mob-filter-btn {{ $hasActiveFilter ? 'active-filter' : '' }}" onclick="openFilterSheet()">
            <i class="fa fa-sliders"></i>
            <span id="mobRecordCount">{{ $logs->count() }}</span>
        </button>
    </div>

    {{-- 4. Mobile Cards Feed List --}}
    <div class="mob-feed-list" id="logsCardsContainer">
        @if(!empty($logs) && $logs->count() > 0)
            @foreach($logs as $item)
                @php
                    $fullName = trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? ''));
                    $mobileClean = preg_replace('/\D/', '', (string)($item->mobile ?? ''));
                    if (strlen($mobileClean) > 10) {
                        $mobileClean = substr($mobileClean, -10);
                    }
                    $initial = strtoupper(mb_substr($fullName ?: 'S', 0, 1, 'UTF-8'));
                    
                    $lastLoginTs = !empty($item->last_login) ? strtotime($item->last_login) : 0;
                    $formattedDate = $lastLoginTs ? date('d M Y, h:i A', $lastLoginTs) : 'Never';
                    $timeAgo = $lastLoginTs ? \Carbon\Carbon::parse($item->last_login)->diffForHumans() : '-';

                    if ($lastLoginTs >= $todayStart) {
                        $recencyKey = 'today';
                        $recencyClass = 'recency-today';
                        $pillClass = 'pill-today';
                        $recencyLabel = 'Active Today';
                    } elseif ($lastLoginTs >= $weekStart) {
                        $recencyKey = 'week';
                        $recencyClass = 'recency-week';
                        $pillClass = 'pill-week';
                        $recencyLabel = 'This Week';
                    } elseif ($lastLoginTs >= $monthStart) {
                        $recencyKey = 'month';
                        $recencyClass = 'recency-month';
                        $pillClass = 'pill-month';
                        $recencyLabel = 'This Month';
                    } else {
                        $recencyKey = 'older';
                        $recencyClass = 'recency-older';
                        $pillClass = 'pill-older';
                        $recencyLabel = 'Older';
                    }

                    $waMsg = "Dear Parent,\nStudent: {$fullName} (Adm: " . ($item->admissionNo ?? '-') . ")\nLast App Login: {$formattedDate}\nRegards,\n{$currentBranchName}";
                    $waUrl = !empty($mobileClean) ? "https://api.whatsapp.com/send?phone=91{$mobileClean}&text=" . urlencode($waMsg) : '#';
                @endphp
                <div class="mob-log-card {{ $recencyClass }}"
                     data-recency="{{ $recencyKey }}"
                     data-name="{{ strtolower($fullName) }}"
                     data-class="{{ strtolower($item->class_name ?? '') }}"
                     data-guardian="{{ strtolower($item->father_name ?? '') }}"
                     data-mobile="{{ $mobileClean }}"
                     data-adm="{{ strtolower($item->admissionNo ?? '') }}"
                     data-uid="{{ strtolower($item->attendance_unique_id ?? '') }}"
                     data-login="{{ $formattedDate }}">

                    {{-- Top Header Row --}}
                    <div class="mob-card-head">
                        <div class="mob-avatar-box">
                            <span>{{ $initial }}</span>
                        </div>

                        <div class="mob-head-info">
                            <div class="mob-name-row">
                                <a href="{{ url('studentDetail/' . $item->id) }}" class="mob-student-name">
                                    {{ $fullName ?: '-' }}
                                </a>
                                <span class="mob-recency-pill {{ $pillClass }}">{{ $recencyLabel }}</span>
                            </div>
                            <div class="mob-pills-row">
                                <span class="mob-class-badge">{{ $item->class_name ?: '-' }}</span>
                                @if(!empty($item->admissionNo))
                                    <span class="mob-adm-badge">Adm: {{ $item->admissionNo }}</span>
                                @endif
                                @if(!empty($item->attendance_unique_id))
                                    <span class="mob-uid-badge">ID: {{ $item->attendance_unique_id }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Meta Grid --}}
                    <div class="mob-meta-grid">
                        <div class="mob-meta-item">
                            <i class="fa fa-user-circle-o text-muted"></i>
                            <span class="mob-meta-label">Father:</span>
                            <span class="mob-meta-val">{{ $item->father_name ?: '-' }}</span>
                        </div>
                        <div class="mob-meta-item">
                            <i class="fa fa-phone text-muted"></i>
                            <span class="mob-meta-label">Mobile:</span>
                            @if(!empty($mobileClean))
                                <a href="tel:{{ $mobileClean }}" class="mob-phone-link">{{ $item->mobile }}</a>
                                <a href="{{ $waUrl }}" target="_blank" class="mob-wa-btn" title="Contact Parent on WhatsApp">
                                    <i class="fa fa-whatsapp"></i>
                                </a>
                            @else
                                <span class="mob-meta-val">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- Bottom Login Time & Quick View Action --}}
                    <div class="mob-login-strip">
                        <div class="mob-login-time">
                            <span class="mob-exact-time">
                                <i class="fa fa-clock-o text-primary mr-1"></i> {{ $formattedDate }}
                            </span>
                            <span class="mob-rel-time">{{ $timeAgo }}</span>
                        </div>
                        <a href="{{ url('studentDetail/' . $item->id) }}" class="mob-btn-view-profile">
                            <span>Profile</span> <i class="fa fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="mob-empty-feed">
                <i class="fa fa-history empty-icon"></i>
                <h6>No Login Activity Records</h6>
                <p>No student app login records found matching your filters.</p>
            </div>
        @endif
    </div>

    {{-- Pagination Bar --}}
    @if($logs->hasPages())
        <div class="mob-pag-bar">
            <span class="mob-pag-info">
                Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }} ({{ number_format($logs->total()) }} total)
            </span>
            <div class="mob-pag-btns">
                @if(!$logs->onFirstPage())
                    <a href="{{ $logs->previousPageUrl() }}&layout=mobile" class="mob-pag-link">
                        <i class="fa fa-angle-left"></i> Prev
                    </a>
                @endif
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}&layout=mobile" class="mob-pag-link">
                        Next <i class="fa fa-angle-right"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>

{{-- 5. Bottom Sheet Filter Modal --}}
<div class="mob-filter-modal-backdrop" id="mobFilterBackdrop" onclick="closeFilterSheet()"></div>
<div class="mob-filter-sheet" id="mobFilterSheet">
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-sliders text-info"></i> Filter Login Logs
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeFilterSheet()">&times;</button>
    </div>
    <form action="{{ url('student_logs') }}" method="GET" id="mobFilterForm">
        <input type="hidden" name="layout" value="mobile">

        <div class="mob-sheet-body">
            <div class="mob-form-group">
                <label class="mob-form-label">Class</label>
                <select name="class_type_id" class="mob-form-select">
                    <option value="">-- All Classes --</option>
                    @if(!empty($classType))
                        @foreach($classType as $class)
                            <option value="{{ $class->id }}" {{ (int)($selectedClassId ?? 0) === (int)$class->id ? 'selected' : '' }}>
                                {{ $class->name ?? ('Class #' . $class->id) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Admission Number</label>
                <input type="text" name="admissionNo" class="mob-form-input" placeholder="Adm No..." value="{{ $search['admissionNo'] ?? '' }}">
            </div>

            <div class="mob-form-group">
                <label class="mob-form-label">Search Keyword</label>
                <input type="text" name="name" class="mob-form-input" placeholder="Search Name, Mobile, UID..." value="{{ $search['name'] ?? '' }}">
            </div>
        </div>

        <div class="mob-sheet-footer">
            <a href="{{ url('student_logs?layout=mobile') }}" class="btn-sheet-reset">
                <i class="fa fa-refresh mr-1"></i> Reset
            </a>
            <button type="submit" class="btn-sheet-apply">
                <i class="fa fa-check mr-1"></i> Apply Filters
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Filter Sheet Controls
function openFilterSheet() {
    $('#mobFilterBackdrop').addClass('show');
    $('#mobFilterSheet').addClass('show');
}
function closeFilterSheet() {
    $('#mobFilterBackdrop').removeClass('show');
    $('#mobFilterSheet').removeClass('show');
}

// Recency Segment Tabs Filter
var currentRecencyFilter = 'all';

function filterByRecency(recency, btn) {
    currentRecencyFilter = recency;
    $('.mob-seg-btn').removeClass('active');
    $(btn).addClass('active');
    applyCombinedFilters();
}

// Client-Side Fast Search with Debounce
var searchTimeout = null;
$('#mobSearchInput').on('input', function() {
    var val = $.trim($(this).val()).toLowerCase();
    if (val.length > 0) {
        $('#mobClearSearch').show();
    } else {
        $('#mobClearSearch').hide();
    }

    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        applyCombinedFilters();
    }, 150);
});

function clearSearch() {
    $('#mobSearchInput').val('').trigger('input');
}

function applyCombinedFilters() {
    var kw = $.trim($('#mobSearchInput').val()).toLowerCase();
    var visibleCount = 0;

    $('.mob-log-card').each(function() {
        var cardRecency = $(this).attr('data-recency') || '';
        var recencyMatch = (currentRecencyFilter === 'all' || cardRecency === currentRecencyFilter);

        if (!recencyMatch) {
            $(this).hide();
            return;
        }

        if (!kw) {
            $(this).show();
            visibleCount++;
            return;
        }

        var name = $(this).attr('data-name') || '';
        var cls = $(this).attr('data-class') || '';
        var guard = $(this).attr('data-guardian') || '';
        var mob = $(this).attr('data-mobile') || '';
        var adm = $(this).attr('data-adm') || '';
        var uid = $(this).attr('data-uid') || '';

        if (name.indexOf(kw) !== -1 || cls.indexOf(kw) !== -1 || guard.indexOf(kw) !== -1 || mob.indexOf(kw) !== -1 || adm.indexOf(kw) !== -1 || uid.indexOf(kw) !== -1) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    $('#mobRecordCount').text(visibleCount);
}

// Export CSV
function exportLogsCSV() {
    var rows = [
        ['#', 'Admission No', 'Student Name', 'Class', 'Father Name', 'Mobile', 'Attendance UID', 'Last Login']
    ];

    var index = 1;
    $('.mob-log-card:visible').each(function() {
        var adm = $(this).attr('data-adm') || '';
        var name = $(this).find('.mob-student-name').text().trim();
        var cls = $(this).find('.mob-class-badge').text().trim();
        var guard = $(this).find('.mob-meta-val').first().text().trim();
        var mob = $(this).attr('data-mobile') || '';
        var uid = $(this).attr('data-uid') || '';
        var lastLogin = $(this).attr('data-login') || '';

        rows.push([
            index++,
            '"' + adm.replace(/"/g, '""') + '"',
            '"' + name.replace(/"/g, '""') + '"',
            '"' + cls.replace(/"/g, '""') + '"',
            '"' + guard.replace(/"/g, '""') + '"',
            '"' + mob.replace(/"/g, '""') + '"',
            '"' + uid.replace(/"/g, '""') + '"',
            '"' + lastLogin.replace(/"/g, '""') + '"'
        ]);
    });

    if (rows.length <= 1) {
        alert('No visible records to export.');
        return;
    }

    var csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + rows.map(function(e) { return e.join(','); }).join('\n');
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'Student_App_Login_Logs_' + new Date().toISOString().slice(0, 10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection

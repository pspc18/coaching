@php
    $getUser = Helper::getUser();
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
    $totalNotices = (int) ($noticeCounts->total_count ?? 0);
    $activeNotices = (int) ($noticeCounts->active_count ?? 0);
    $upcomingNotices = (int) ($noticeCounts->upcoming_count ?? 0);
    $expiredNotices = (int) ($noticeCounts->expired_count ?? 0);
    $currentSearch = (string) ($search ?? request('search', ''));
    $currentSessionName = Session::get('session_name') ?? date('Y');
@endphp
@extends('student_login.layout.app')

@section('title', 'Notices & Circulars')
@section('page_title', 'NOTICES')
@section('page_sub', Session::get('first_name').' · '.$className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE NOTICES & CIRCULARS
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
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

/* 2. Compact Search & Filter Toolbar with Sticky Support */
.mob-tracker-container {
    margin-bottom: 8px;
    position: relative;
}
.mob-search-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all .2s ease;
}

/* Sticky Fixed Toolbar when Scrolled Past */
.mob-search-toolbar.is-fixed {
    position: fixed !important;
    top: 52px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    width: calc(100% - 16px) !important;
    max-width: 464px !important;
    z-index: 999 !important;
    margin: 0 !important;
    background: rgba(0, 24, 51, 0.96) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    border: 1px solid #0284c7 !important;
    border-radius: 4px !important;
    box-shadow: 0 6px 18px rgba(0, 20, 40, 0.45) !important;
}

.mob-search-row {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-bottom: 6px;
}
.mob-search-input-wrap {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i.search-icon {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11px;
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
    transition: border-color .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear-btn {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0;
    font-size: 12px;
}
.mob-search-submit-btn {
    height: 30px;
    padding: 0 10px;
    background: #002C54;
    border: 1px solid #002C54;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    color: #ffffff;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

/* Category Filter Tabs Bar */
.mob-filter-tabs-row {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-x;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.mob-filter-tabs-row::-webkit-scrollbar {
    display: none;
}
.mob-filter-btn {
    flex: 0 0 auto;
    height: 28px;
    padding: 0 9px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-filter-btn .count-tag {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 8px;
    background: #e2e8f0;
    color: #475569;
    font-weight: 800;
}
.mob-filter-btn.active-filter {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
.mob-filter-btn.active-filter .count-tag {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
.mob-filter-btn:active {
    transform: scale(0.96);
}

/* Fixed state adjustments for toolbar */
.mob-search-toolbar.is-fixed .mob-search-row {
    margin-bottom: 5px;
}
.mob-search-toolbar.is-fixed .mob-search-input {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
.mob-search-toolbar.is-fixed .mob-search-input::placeholder {
    color: #94a3b8;
}
.mob-search-toolbar.is-fixed .mob-search-submit-btn {
    background: #0284c7;
    border-color: #0284c7;
}
.mob-search-toolbar.is-fixed .mob-filter-btn {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.2);
    color: #cbd5e1;
}
.mob-search-toolbar.is-fixed .mob-filter-btn .count-tag {
    background: rgba(255, 255, 255, 0.15);
    color: #cbd5e1;
}
.mob-search-toolbar.is-fixed .mob-filter-btn.active-filter {
    background: #0284c7 !important;
    border-color: #0284c7 !important;
    color: #ffffff !important;
}
.mob-search-toolbar.is-fixed .mob-filter-btn.active-filter .count-tag {
    background: rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

/* 3. Feed List & Notice Card Item */
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
    padding: 10px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: transform .1s ease, border-color .1s ease;
}
.student-mob-card:active {
    border-color: #94a3b8;
}

/* Status Accents */
.card-status-active {
    border-left: 3px solid #16a34a;
}
.card-status-upcoming {
    border-left: 3px solid #f59e0b;
}
.card-status-expired {
    border-left: 3px solid #94a3b8;
}

/* Card Header matching admissionView */
.student-card-header {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    margin-bottom: 8px;
    padding-bottom: 7px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
    border: 1.5px solid #cbd5e1;
}
.avatar-active {
    background: #ecfdf5;
    color: #16a34a;
    border-color: #a7f3d0;
}
.avatar-upcoming {
    background: #fffbeb;
    color: #d97706;
    border-color: #fde68a;
}
.avatar-expired {
    background: #f1f5f9;
    color: #64748b;
    border-color: #cbd5e1;
}

.student-header-info {
    flex: 1;
    min-width: 0;
}
.student-name {
    font-size: 13px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.35;
    margin-bottom: 3px;
    word-break: break-word;
}
.student-meta-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}
.mob-meta-chip {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 1px 6px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-meta-chip i {
    font-size: 9px;
    color: #94a3b8;
}

/* Status Badges matching admissionView */
.mob-status-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    flex-shrink: 0;
}
.status-pill-active {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}
.status-pill-upcoming {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}
.status-pill-expired {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

/* Notice Content Body */
.notice-content-body {
    padding: 2px 0 6px 0;
}
.notice-text-content {
    font-size: 11.5px;
    line-height: 1.55;
    color: #334155;
    white-space: pre-line;
    word-break: break-word;
}
.notice-text-content.is-truncated {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.btn-read-toggle {
    background: none;
    border: none;
    padding: 4px 0 2px;
    font-size: 11px;
    font-weight: 700;
    color: #0284c7;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-read-toggle:hover {
    text-decoration: underline;
}

/* Attachment Box */
.notice-attach-wrapper {
    margin-top: 6px;
    margin-bottom: 4px;
}
.mob-pdf-btn {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 9px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-pdf-btn:active {
    background: #e0f2fe;
    border-color: #0284c7;
}
.mob-pdf-icon {
    font-size: 19px;
    color: #dc2626;
    flex-shrink: 0;
}
.mob-pdf-info {
    flex: 1;
    min-width: 0;
}
.mob-pdf-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.mob-pdf-size {
    font-size: 9px;
    color: #64748b;
    font-weight: 600;
}
.mob-pdf-download {
    font-size: 11px;
    color: #0284c7;
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e0f2fe;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Card Actions Row */
.student-card-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 6px;
    margin-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.card-time-meta {
    font-size: 9px;
    color: #94a3b8;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.card-action-btns {
    display: flex;
    gap: 5px;
}
.mob-btn-action {
    height: 26px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155 !important;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-btn-action:active {
    transform: scale(0.96);
}
.mob-btn-action-primary {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff !important;
}

/* Empty State */
.mob-empty-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 32px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    margin-bottom: 12px;
}
.mob-empty-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.mob-empty-card h4 {
    font-size: 13.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
}
.mob-empty-card p {
    font-size: 11px;
    color: #64748b;
    margin-bottom: 14px;
}

/* Pagination Styling */
.mob-pagination-wrap {
    display: flex;
    justify-content: center;
    margin: 12px 0 20px;
}
.mob-pagination-wrap .pagination {
    margin: 0;
    gap: 4px;
    flex-wrap: wrap;
    justify-content: center;
}
.mob-pagination-wrap .page-item .page-link {
    min-width: 30px;
    height: 30px;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #002C54;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px !important;
}
.mob-pagination-wrap .page-item.active .page-link {
    background: #002C54 !important;
    border-color: #002C54 !important;
    color: #ffffff !important;
}
.mob-pagination-wrap .page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    border-color: #e2e8f0;
}

/* Modal Styling */
.modal-content.mob-modal-content {
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}
.mob-modal-header {
    background: #002C54;
    color: #ffffff;
    padding: 10px 14px;
    border-bottom: 1px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-modal-header .modal-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
}
.mob-modal-close {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 16px;
    cursor: pointer;
    opacity: 0.8;
}
.mob-modal-close:hover {
    opacity: 1;
}
</style>
@endsection

@section('content')
<div class="arise-mobile-container" style="padding-bottom: 30px;">

    {{-- 1. Glassmorphic Navy Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-bullhorn" style="color: #38bdf8;"></i> Notices & Circulars
            </div>
            <span class="mob-session-pill">
                <i class="fa fa-circle text-success" style="font-size: 7px;"></i> {{ $currentSessionName }}
            </span>
        </div>

        {{-- 4 Metrics Glance (2x2 Grid) --}}
        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <div class="mob-radar-tag"><i class="fa fa-th-list"></i> Total Notices</div>
                <div class="mob-radar-val">{{ $totalNotices }}</div>
                <span class="mob-radar-meta">All Circulars</span>
            </div>
            <div class="mob-radar-col">
                <div class="mob-radar-tag" style="color: #4ade80;"><i class="fa fa-bolt"></i> Active Now</div>
                <div class="mob-radar-val" style="color: #4ade80;">{{ $activeNotices }}</div>
                <span class="mob-radar-meta">Valid for Today</span>
            </div>
            <div class="mob-radar-col">
                <div class="mob-radar-tag" style="color: #fbbf24;"><i class="fa fa-clock-o"></i> Upcoming</div>
                <div class="mob-radar-val" style="color: #fbbf24;">{{ $upcomingNotices }}</div>
                <span class="mob-radar-meta">Scheduled Ahead</span>
            </div>
            <div class="mob-radar-col">
                <div class="mob-radar-tag"><i class="fa fa-archive"></i> Past / Archived</div>
                <div class="mob-radar-val" style="color: #94a3b8;">{{ $expiredNotices }}</div>
                <span class="mob-radar-meta">Completed Notices</span>
            </div>
        </div>

        {{-- Fast Action Bar --}}
        <div class="mob-actions-bar">
            <a href="{{ url('student-notices') }}" class="mob-act-btn mob-act-btn-add">
                <i class="fa fa-refresh"></i> Refresh Feed
            </a>
            <a href="{{ url('student-notices?filter=active') }}" class="mob-act-btn mob-act-btn-filter">
                <i class="fa fa-bolt"></i> Active Only ({{ $activeNotices }})
            </a>
        </div>
    </div>

    {{-- 2. Compact Search & Segment Toolbar (Sticky when scrolled past) --}}
    <div class="mob-tracker-container" id="trackerContainer">
        <div class="mob-search-toolbar" id="mobStepTracker">
            <form action="{{ url('student-notices') }}" method="GET" id="noticeSearchForm" class="mob-search-row">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <div class="mob-search-input-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           id="noticeSearchInput" 
                           class="mob-search-input" 
                           placeholder="Search notice title, circular keyword..." 
                           value="{{ $currentSearch }}" 
                           autocomplete="off">
                    @if($currentSearch !== '')
                        <button type="button" class="mob-search-clear-btn" onclick="clearNoticeSearch()" title="Clear search">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    @endif
                </div>
                <button type="submit" class="mob-search-submit-btn">
                    <i class="fa fa-search"></i> Find
                </button>
            </form>

            <div class="mob-filter-tabs-row" id="filterTabsRow">
                <a href="{{ url('student-notices?filter=all'.($currentSearch ? '&search='.urlencode($currentSearch) : '')) }}" 
                   class="mob-filter-btn {{ $filter === 'all' ? 'active-filter' : '' }}">
                    <i class="fa fa-th-large"></i> All <span class="count-tag">{{ $totalNotices }}</span>
                </a>
                <a href="{{ url('student-notices?filter=active'.($currentSearch ? '&search='.urlencode($currentSearch) : '')) }}" 
                   class="mob-filter-btn {{ $filter === 'active' ? 'active-filter' : '' }}">
                    <i class="fa fa-bolt"></i> Active <span class="count-tag">{{ $activeNotices }}</span>
                </a>
                <a href="{{ url('student-notices?filter=upcoming'.($currentSearch ? '&search='.urlencode($currentSearch) : '')) }}" 
                   class="mob-filter-btn {{ $filter === 'upcoming' ? 'active-filter' : '' }}">
                    <i class="fa fa-clock-o"></i> Upcoming <span class="count-tag">{{ $upcomingNotices }}</span>
                </a>
                <a href="{{ url('student-notices?filter=expired'.($currentSearch ? '&search='.urlencode($currentSearch) : '')) }}" 
                   class="mob-filter-btn {{ $filter === 'expired' ? 'active-filter' : '' }}">
                    <i class="fa fa-archive"></i> Past <span class="count-tag">{{ $expiredNotices }}</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter / Result Status Pill --}}
    @if($currentSearch !== '')
        <div style="margin-bottom: 8px; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: #475569; background: #e0f2fe; border: 1px solid #bae6fd; padding: 5px 10px; border-radius: 3px;">
            <span><i class="fa fa-filter text-primary"></i> Search results for: <strong>"{{ $currentSearch }}"</strong> ({{ $notices->total() }})</span>
            <a href="{{ url('student-notices?filter='.$filter) }}" style="color: #0284c7; font-weight: 700; text-decoration: none;"><i class="fa fa-times"></i> Clear</a>
        </div>
    @endif

    {{-- 3. Feed List & Notice Cards --}}
    <div class="student-feed-list" id="noticeFeedList">
        @forelse($notices as $notice)
            @php
                $fromDate = \Carbon\Carbon::parse($notice->from_date);
                $toDate = \Carbon\Carbon::parse($notice->to_date);
                $fromStr = $fromDate->toDateString();
                $toStr = $toDate->toDateString();

                if ($fromStr > $today) {
                    $noticeStatus = 'upcoming';
                    $statusLabel = 'Upcoming';
                    $cardClass = 'card-status-upcoming';
                    $avatarClass = 'avatar-upcoming';
                    $pillClass = 'status-pill-upcoming';
                    $statusIcon = 'fa-clock-o';
                } elseif ($toStr < $today) {
                    $noticeStatus = 'expired';
                    $statusLabel = 'Past';
                    $cardClass = 'card-status-expired';
                    $avatarClass = 'avatar-expired';
                    $pillClass = 'status-pill-expired';
                    $statusIcon = 'fa-history';
                } else {
                    $noticeStatus = 'active';
                    $statusLabel = 'Active';
                    $cardClass = 'card-status-active';
                    $avatarClass = 'avatar-active';
                    $pillClass = 'status-pill-active';
                    $statusIcon = 'fa-circle';
                }

                $rawMessage = (string) $notice->message;
                $cleanMessage = trim(strip_tags($rawMessage));
                $isLong = mb_strlen($cleanMessage) > 200;
                $publishedAt = $notice->published_at ? \Carbon\Carbon::parse($notice->published_at) : null;
                $publisherName = trim(optional($notice->creator)->first_name . ' ' . optional($notice->creator)->last_name);
                if (empty($publisherName)) {
                    $publisherName = 'School Administration';
                }
            @endphp

            <div class="student-mob-card {{ $cardClass }}" 
                 id="notice-card-{{ $notice->id }}" 
                 data-title="{{ strtolower($notice->title) }}" 
                 data-message="{{ strtolower($cleanMessage) }}">
                
                {{-- Header --}}
                <div class="student-card-header">
                    <div class="student-avatar-box {{ $avatarClass }}">
                        <i class="fa {{ $noticeStatus === 'active' ? 'fa-bullhorn' : ($noticeStatus === 'upcoming' ? 'fa-calendar-check-o' : 'fa-archive') }}"></i>
                    </div>
                    <div class="student-header-info">
                        <div class="student-name">{{ $notice->title }}</div>
                        <div class="student-meta-chips">
                            <span class="mob-meta-chip">
                                <i class="fa fa-calendar"></i> {{ $fromDate->format('d M') }} – {{ $toDate->format('d M Y') }}
                            </span>
                            <span class="mob-meta-chip">
                                <i class="fa fa-user-circle-o"></i> {{ $publisherName }}
                            </span>
                        </div>
                    </div>
                    <span class="mob-status-pill {{ $pillClass }}">
                        <i class="fa {{ $statusIcon }}"></i> {{ $statusLabel }}
                    </span>
                </div>

                {{-- Content Body --}}
                <div class="notice-content-body">
                    <div class="notice-text-content {{ $isLong ? 'is-truncated' : '' }}" id="notice-msg-{{ $notice->id }}">
                        {{ $cleanMessage ?: 'No notice description available.' }}
                    </div>

                    @if($isLong)
                        <button type="button" class="btn-read-toggle" onclick="toggleNoticeText(this, 'notice-msg-{{ $notice->id }}')">
                            <i class="fa fa-angle-down"></i> Read More
                        </button>
                    @endif

                    {{-- Attachment if available --}}
                    @if(!empty($notice->attachment_path))
                        <div class="notice-attach-wrapper">
                            <a href="{{ url('notice-management/'.$notice->id.'/attachment') }}" target="_blank" class="mob-pdf-btn">
                                <i class="fa fa-file-pdf-o mob-pdf-icon"></i>
                                <div class="mob-pdf-info">
                                    <div class="mob-pdf-title">{{ $notice->attachment_name ?: 'Notice Circular Document.pdf' }}</div>
                                    <div class="mob-pdf-size">
                                        {{ $notice->attachment_size ? round($notice->attachment_size / 1024, 1).' KB' : 'Official PDF Document' }} · Tap to View
                                    </div>
                                </div>
                                <div class="mob-pdf-download">
                                    <i class="fa fa-download"></i>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Card Actions / Meta Row --}}
                <div class="student-card-actions">
                    <div class="card-time-meta">
                        <i class="fa fa-clock-o"></i>
                        @if($publishedAt)
                            Published {{ $publishedAt->format('d M Y, h:i A') }}
                        @else
                            {{ $fromDate->format('d M Y') }}
                        @endif
                    </div>
                    <div class="card-action-btns">
                        <button type="button" class="mob-btn-action" onclick="copyNoticeText({{ $notice->id }})" title="Copy Notice Text">
                            <i class="fa fa-copy"></i> Copy
                        </button>
                        <button type="button" class="mob-btn-action mob-btn-action-primary" onclick="openNoticeModal({{ $notice->id }})" title="Full View">
                            <i class="fa fa-expand"></i> View
                        </button>
                    </div>
                </div>

                {{-- Hidden Data for Modal --}}
                <div id="notice-data-{{ $notice->id }}" style="display:none;"
                     data-id="{{ $notice->id }}"
                     data-title="{{ $notice->title }}"
                     data-status="{{ $statusLabel }}"
                     data-status-class="{{ $pillClass }}"
                     data-status-icon="{{ $statusIcon }}"
                     data-dates="{{ $fromDate->format('d M Y') }} – {{ $toDate->format('d M Y') }}"
                     data-publisher="{{ $publisherName }}"
                     data-published-time="{{ $publishedAt ? $publishedAt->format('d M Y, h:i A') : $fromDate->format('d M Y') }}"
                     data-attachment="{{ !empty($notice->attachment_path) ? url('notice-management/'.$notice->id.'/attachment') : '' }}"
                     data-attachment-name="{{ $notice->attachment_name ?: 'Notice Document.pdf' }}">
                    <div class="full-message">{{ $cleanMessage }}</div>
                </div>

            </div>
        @empty
            <div class="mob-empty-card">
                <div class="mob-empty-icon">
                    <i class="fa fa-bell-slash-o"></i>
                </div>
                <h4>No Notices Found</h4>
                <p>
                    @if($currentSearch !== '')
                        No circulars matched your search query "{{ $currentSearch }}".
                    @elseif($filter !== 'all')
                        There are currently no {{ $filter }} notices available for your class.
                    @else
                        You are up to date! There are no notices posted for your account right now.
                    @endif
                </p>
                <a href="{{ url('student-notices') }}" class="mob-act-btn mob-act-btn-add" style="display: inline-flex; width: auto; padding: 0 16px;">
                    <i class="fa fa-refresh"></i> View All Circulars
                </a>
            </div>
        @endforelse
    </div>

    {{-- 4. Mobile Pagination --}}
    @if($notices->hasPages())
        <div class="mob-pagination-wrap">
            {{ $notices->links() }}
        </div>
    @endif

</div>

{{-- 5. Full Notice Reader Modal --}}
<div class="modal fade" id="noticeDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px; margin: 16px auto;">
        <div class="modal-content mob-modal-content">
            <div class="mob-modal-header">
                <div class="modal-title d-flex align-items-center gap-2">
                    <i class="fa fa-bullhorn text-warning"></i>
                    <span>Notice Details</span>
                </div>
                <button type="button" class="mob-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body" style="padding: 14px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                    <span id="modalNoticeStatus" class="mob-status-pill"></span>
                    <span id="modalNoticeDates" style="font-size: 10px; font-weight: 600; color: #64748b;"></span>
                </div>
                
                <h5 id="modalNoticeTitle" style="font-size: 14px; font-weight: 800; color: #002C54; line-height: 1.35; margin-bottom: 10px;"></h5>

                <div id="modalNoticeBody" style="font-size: 12px; line-height: 1.6; color: #334155; white-space: pre-line; word-break: break-word; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px; max-height: 320px; overflow-y: auto; margin-bottom: 10px;"></div>

                <div id="modalNoticeAttachment" style="display:none; margin-bottom: 10px;">
                    <a id="modalAttachmentLink" href="#" target="_blank" class="mob-pdf-btn">
                        <i class="fa fa-file-pdf-o mob-pdf-icon"></i>
                        <div class="mob-pdf-info">
                            <div class="mob-pdf-title" id="modalAttachmentName">Attachment Document.pdf</div>
                            <div class="mob-pdf-size">Tap to view / download PDF</div>
                        </div>
                        <div class="mob-pdf-download">
                            <i class="fa fa-download"></i>
                        </div>
                    </a>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 9.5px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                    <span><i class="fa fa-user-circle-o"></i> <span id="modalNoticePublisher"></span></span>
                    <span><i class="fa fa-clock-o"></i> <span id="modalNoticePublished"></span></span>
                </div>
            </div>
            <div class="modal-footer" style="padding: 8px 14px; background: #f8fafc; border-top: 1px solid #cbd5e1; display: flex; justify-content: space-between;">
                <button type="button" class="mob-btn-action" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="mob-btn-action mob-btn-action-primary" id="modalCopyBtn">
                    <i class="fa fa-copy"></i> Copy Text
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="mobToast" style="position: fixed; bottom: 68px; left: 50%; transform: translateX(-50%); background: #002C54; color: #ffffff; padding: 7px 16px; border-radius: 20px; font-size: 11px; font-weight: 700; z-index: 99999; display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
    <i class="fa fa-check-circle text-success me-1"></i> <span id="mobToastText">Copied to clipboard</span>
</div>

@endsection

@section('scripts')
<script>
// 1. Sticky Tracker on Scroll matching admissionAdd & profileStudent
function checkStickyTracker() {
    const container = document.getElementById('trackerContainer');
    const tracker = document.getElementById('mobStepTracker');
    if (!container || !tracker) return;

    const containerTop = container.getBoundingClientRect().top + window.pageYOffset;
    const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const triggerPoint = containerTop - 52;

    if (scrollY >= triggerPoint && triggerPoint > 0) {
        if (!tracker.classList.contains('is-fixed')) {
            const h = tracker.offsetHeight;
            container.style.minHeight = h + 'px';
            tracker.classList.add('is-fixed');
        }
    } else {
        if (tracker.classList.contains('is-fixed')) {
            tracker.classList.remove('is-fixed');
            container.style.minHeight = '';
        }
    }
}

// 2. Read More / Show Less Toggle
function toggleNoticeText(btn, msgId) {
    const msgEl = document.getElementById(msgId);
    if (!msgEl) return;

    const isTruncated = msgEl.classList.contains('is-truncated');
    if (isTruncated) {
        msgEl.classList.remove('is-truncated');
        btn.innerHTML = '<i class="fa fa-angle-up"></i> Show Less';
    } else {
        msgEl.classList.add('is-truncated');
        btn.innerHTML = '<i class="fa fa-angle-down"></i> Read More';
    }
}

// 3. Clear Search Helper
function clearNoticeSearch() {
    const input = document.getElementById('noticeSearchInput');
    if (input) input.value = '';
    const form = document.getElementById('noticeSearchForm');
    if (form) form.submit();
}

// 4. Quick Copy Notice Text
function copyNoticeText(noticeId) {
    const dataEl = document.getElementById('notice-data-' + noticeId);
    if (!dataEl) return;

    const title = dataEl.getAttribute('data-title') || '';
    const msg = dataEl.querySelector('.full-message') ? dataEl.querySelector('.full-message').innerText : '';
    const fullText = title + "\n\n" + msg;

    copyToClipboard(fullText, 'Notice text copied!');
}

function copyToClipboard(text, successMsg) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            showToast(successMsg || 'Copied to clipboard');
        }).catch(function() {
            fallbackCopy(text, successMsg);
        });
    } else {
        fallbackCopy(text, successMsg);
    }
}

function fallbackCopy(text, successMsg) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        showToast(successMsg || 'Copied to clipboard');
    } catch (err) {
        showToast('Unable to copy');
    }
    document.body.removeChild(textArea);
}

function showToast(msg) {
    const toast = document.getElementById('mobToast');
    const toastText = document.getElementById('mobToastText');
    if (!toast || !toastText) return;
    toastText.innerText = msg;
    toast.style.display = 'block';
    setTimeout(function() {
        toast.style.display = 'none';
    }, 2000);
}

// 5. Open Notice Detail Modal
function openNoticeModal(noticeId) {
    const dataEl = document.getElementById('notice-data-' + noticeId);
    if (!dataEl) return;

    const title = dataEl.getAttribute('data-title');
    const status = dataEl.getAttribute('data-status');
    const statusClass = dataEl.getAttribute('data-status-class');
    const statusIcon = dataEl.getAttribute('data-status-icon');
    const dates = dataEl.getAttribute('data-dates');
    const publisher = dataEl.getAttribute('data-publisher');
    const publishedTime = dataEl.getAttribute('data-published-time');
    const attachmentUrl = dataEl.getAttribute('data-attachment');
    const attachmentName = dataEl.getAttribute('data-attachment-name');
    const message = dataEl.querySelector('.full-message') ? dataEl.querySelector('.full-message').innerText : '';

    document.getElementById('modalNoticeTitle').innerText = title;
    document.getElementById('modalNoticeDates').innerText = dates;
    document.getElementById('modalNoticeBody').innerText = message;
    document.getElementById('modalNoticePublisher').innerText = publisher;
    document.getElementById('modalNoticePublished').innerText = publishedTime;

    const statusPill = document.getElementById('modalNoticeStatus');
    statusPill.className = 'mob-status-pill ' + statusClass;
    statusPill.innerHTML = '<i class="fa ' + statusIcon + '"></i> ' + status;

    const attachWrap = document.getElementById('modalNoticeAttachment');
    if (attachmentUrl) {
        attachWrap.style.display = 'block';
        document.getElementById('modalAttachmentLink').href = attachmentUrl;
        document.getElementById('modalAttachmentName').innerText = attachmentName;
    } else {
        attachWrap.style.display = 'none';
    }

    document.getElementById('modalCopyBtn').onclick = function() {
        copyNoticeText(noticeId);
    };

    const modalEl = document.getElementById('noticeDetailModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// 6. Fast Client-side instant filter on current page results
document.addEventListener('DOMContentLoaded', function () {
    // Sticky Tracker Listeners
    window.addEventListener('scroll', checkStickyTracker, { passive: true });
    window.addEventListener('touchmove', checkStickyTracker, { passive: true });
    window.addEventListener('resize', checkStickyTracker, { passive: true });
    window.addEventListener('orientationchange', checkStickyTracker, { passive: true });
    setTimeout(checkStickyTracker, 80);

    // Instant search filtering as student types
    const searchInput = document.getElementById('noticeSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const cards = document.querySelectorAll('.student-mob-card');
            
            cards.forEach(function(card) {
                const title = card.getAttribute('data-title') || '';
                const msg = card.getAttribute('data-message') || '';
                if (!query || title.includes(query) || msg.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection

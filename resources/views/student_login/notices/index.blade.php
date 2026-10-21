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
    padding: 8px 10px;
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
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.student-avatar-box {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
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
    cursor: pointer;
}
.student-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.3;
    margin-bottom: 2px;
    word-break: break-word;
}
.student-meta-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}
.mob-meta-chip {
    font-size: 9px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 1px 5px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    line-height: 1.2;
}
.mob-meta-chip i {
    font-size: 8.5px;
    color: #94a3b8;
}

/* Status Badges */
.mob-status-pill {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 6px;
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

/* Notice Content Body (Clean 3-Line Clamp with Standard Margins) */
.notice-content-body {
    padding: 0;
    margin: 0;
}
.notice-text-content {
    font-size: 11.5px;
    line-height: 1.45;
    color: #334155;
    word-break: break-word;
    white-space: normal;
    margin: 0;
}
.notice-text-content.is-truncated {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notice-text-content.is-expanded {
    display: block;
    white-space: pre-line;
}
.btn-read-toggle {
    background: none;
    border: none;
    padding: 2px 0 0 0;
    margin-top: 3px;
    font-size: 10.5px;
    font-weight: 700;
    color: #0284c7;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    outline: none;
    line-height: 1;
}
.btn-read-toggle:hover {
    text-decoration: underline;
}

/* Attachment Box */
.notice-attach-wrapper {
    margin-top: 5px;
    margin-bottom: 0;
}
.mob-pdf-btn {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 5px 8px;
    display: flex;
    align-items: center;
    gap: 7px;
    text-decoration: none !important;
    transition: all .12s ease;
}
.mob-pdf-btn:active {
    background: #e0f2fe;
    border-color: #0284c7;
}
.mob-pdf-icon {
    font-size: 17px;
    color: #dc2626;
    flex-shrink: 0;
}
.mob-pdf-info {
    flex: 1;
    min-width: 0;
}
.mob-pdf-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.mob-pdf-size {
    font-size: 8.5px;
    color: #64748b;
    font-weight: 600;
}
.mob-pdf-download {
    font-size: 10px;
    color: #0284c7;
    flex-shrink: 0;
    width: 20px;
    height: 20px;
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
    padding-top: 5px;
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
    gap: 4px;
}
.mob-btn-action {
    height: 24px;
    padding: 0 9px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    text-align: center;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 3px;
    border: 1px solid #002C54;
    background: #002C54;
    color: #ffffff !important;
    cursor: pointer;
    transition: all .12s ease;
}
.mob-btn-action:active {
    transform: scale(0.96);
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

/* ==========================================================================
   5. Mobile Custom Bottom Sheet Modal (Matching admissionView?layout=mobile)
   ========================================================================== */
.mob-notice-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 15, 30, 0.72);
    z-index: 2050;
    opacity: 0;
    visibility: hidden;
    transition: all .22s ease-in-out;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.mob-notice-modal-backdrop.show {
    opacity: 1;
    visibility: visible;
}
.mob-notice-sheet {
    position: fixed;
    bottom: -100%;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border-radius: 6px 6px 0 0;
    border-top: 2px solid #0284c7;
    z-index: 2051;
    transition: bottom .25s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -6px 25px rgba(0, 20, 40, 0.35);
}
.mob-notice-sheet.show {
    bottom: 0;
}
.mob-sheet-header {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    padding: 10px 14px;
    border-radius: 4px 4px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
}
.mob-sheet-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 7px;
}
.mob-sheet-close {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    width: 26px;
    height: 26px;
    border-radius: 3px;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .12s ease;
}
.mob-sheet-close:active {
    background: rgba(255, 255, 255, 0.25);
}

/* FIXED POSITION TOP STRIP (Status Tag, Date Range & Published On remain pinned) */
.mob-sheet-fixed-meta {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    flex-shrink: 0;
    z-index: 2;
}
.sheet-fixed-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.sheet-meta-pill {
    font-size: 9.5px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.sheet-pub-pill {
    color: #64748b;
    background: #f1f5f9;
    border-color: #e2e8f0;
}

/* MOVEABLE / SCROLLABLE BODY */
.mob-sheet-body {
    padding: 13px 14px 20px 14px;
    overflow-y: auto;
    flex: 1;
    -webkit-overflow-scrolling: touch;
}
.sheet-notice-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.35;
    margin: 0 0 10px 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    word-break: break-word;
}
.sheet-notice-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 11px 12px;
}
.sheet-notice-text {
    font-size: 12px;
    line-height: 1.65;
    color: #1e293b;
    white-space: pre-line;
    word-break: break-word;
}
</style>
@endsection

@section('content')
<div class="arise-mobile-container" style="padding-bottom: 30px;">

    {{-- 1. Glassmorphic Navy Hero Card (Clean Metrics Glance) --}}
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
                // Collapse excessive line breaks for snippet display
                $compactMessage = preg_replace("/[\r\n]{2,}/", "\n", $cleanMessage);

                $publishedAt = $notice->published_at ? \Carbon\Carbon::parse($notice->published_at) : null;
            @endphp

            <div class="student-mob-card {{ $cardClass }}" 
                 id="notice-card-{{ $notice->id }}" 
                 data-title="{{ strtolower($notice->title) }}" 
                 data-message="{{ strtolower($cleanMessage) }}">
                
                {{-- Header (Clean Date Badge only, redundant school name removed) --}}
                <div class="student-card-header">
                    <div class="student-avatar-box {{ $avatarClass }}">
                        <i class="fa {{ $noticeStatus === 'active' ? 'fa-bullhorn' : ($noticeStatus === 'upcoming' ? 'fa-calendar-check-o' : 'fa-archive') }}"></i>
                    </div>
                    <div class="student-header-info" onclick="openNoticeSheet({{ $notice->id }})">
                        <div class="student-name">{{ $notice->title }}</div>
                        <div class="student-meta-chips">
                            <span class="mob-meta-chip">
                                <i class="fa fa-calendar"></i> {{ $fromDate->format('d M') }} – {{ $toDate->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <span class="mob-status-pill {{ $pillClass }}">
                        <i class="fa {{ $statusIcon }}"></i> {{ $statusLabel }}
                    </span>
                </div>

                {{-- Content Body (Clean 3-Line Clamped Snippet with tight standard padding) --}}
                <div class="notice-content-body">
                    <div class="notice-text-content is-truncated" id="notice-msg-{{ $notice->id }}">{{ $compactMessage ?: 'No notice description available.' }}</div>

                    {{-- Read More Button (Dynamically shown ONLY if content exceeds 3 lines) --}}
                    <button type="button" 
                            class="btn-read-toggle" 
                            id="read-toggle-{{ $notice->id }}" 
                            style="display: none;" 
                            onclick="toggleNoticeText(this, 'notice-msg-{{ $notice->id }}')">
                        <i class="fa fa-angle-down"></i> Read More
                    </button>

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
                        <button type="button" class="mob-btn-action" onclick="openNoticeSheet({{ $notice->id }})" title="View Details">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </div>
                </div>

                {{-- Hidden Data for Bottom Sheet Modal --}}
                <div id="notice-data-{{ $notice->id }}" style="display:none;"
                     data-id="{{ $notice->id }}"
                     data-title="{{ $notice->title }}"
                     data-status="{{ $statusLabel }}"
                     data-status-class="{{ $pillClass }}"
                     data-status-icon="{{ $statusIcon }}"
                     data-dates="{{ $fromDate->format('d M Y') }} – {{ $toDate->format('d M Y') }}"
                     data-published-time="{{ $publishedAt ? $publishedAt->format('d M Y, h:i A') : $fromDate->format('d M Y') }}"
                     data-attachment="{{ !empty($notice->attachment_path) ? url('notice-management/'.$notice->id.'/attachment') : '' }}"
                     data-attachment-name="{{ $notice->attachment_name ?: 'Notice Circular.pdf' }}">
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
                <a href="{{ url('student-notices') }}" style="display: inline-flex; height: 32px; padding: 0 14px; background: #002C54; color: #fff; border-radius: 4px; font-size: 11.5px; font-weight: 700; align-items: center; gap: 5px; text-decoration: none;">
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

{{-- ==========================================================================
     5. Mobile Custom Bottom Sheet Modal (Matching admissionView?layout=mobile)
     Fixed Top Strip: Status Tag, Date Range & Published On
     Moveable / Scrollable Body: Notice Title, Body Message & Attachment
     ========================================================================== --}}
<div class="mob-notice-modal-backdrop" id="mobNoticeBackdrop" onclick="closeNoticeSheet()"></div>
<div class="mob-notice-sheet" id="mobNoticeSheet">
    {{-- Header --}}
    <div class="mob-sheet-header">
        <div class="mob-sheet-title">
            <i class="fa fa-bullhorn" style="color: #38bdf8;"></i>
            <span>Notice Details</span>
        </div>
        <button type="button" class="mob-sheet-close" onclick="closeNoticeSheet()" aria-label="Close">&times;</button>
    </div>

    {{-- FIXED POSITION META STRIP (Status Tag, Date Range, Published On remain pinned) --}}
    <div class="mob-sheet-fixed-meta">
        <div class="sheet-fixed-meta-row">
            <span id="sheetNoticeStatus" class="mob-status-pill"></span>
            <span id="sheetNoticeDates" class="sheet-meta-pill"></span>
            <span id="sheetNoticePublished" class="sheet-meta-pill sheet-pub-pill"></span>
        </div>
    </div>

    {{-- MOVEABLE / SCROLLABLE BODY --}}
    <div class="mob-sheet-body">
        {{-- Notice Title --}}
        <h3 id="sheetNoticeTitle" class="sheet-notice-title"></h3>

        {{-- Notice Content Box --}}
        <div class="sheet-notice-card">
            <div id="sheetNoticeBody" class="sheet-notice-text"></div>
        </div>

        {{-- Attachment Download Pill if available --}}
        <div id="sheetNoticeAttachment" style="display: none; margin-top: 12px;">
            <a id="sheetAttachmentLink" href="#" target="_blank" class="mob-pdf-btn">
                <i class="fa fa-file-pdf-o mob-pdf-icon"></i>
                <div class="mob-pdf-info">
                    <div class="mob-pdf-title" id="sheetAttachmentName">Notice Circular.pdf</div>
                    <div class="mob-pdf-size">Tap to view / download PDF circular</div>
                </div>
                <div class="mob-pdf-download">
                    <i class="fa fa-download"></i>
                </div>
            </a>
        </div>
    </div>
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
        msgEl.classList.add('is-expanded');
        btn.innerHTML = '<i class="fa fa-angle-up"></i> Show Less';
    } else {
        msgEl.classList.add('is-truncated');
        msgEl.classList.remove('is-expanded');
        btn.innerHTML = '<i class="fa fa-angle-down"></i> Read More';
    }
}

// 3. Auto-detect if content exceeds 3 lines, ONLY show Read More if needed
function updateReadMoreVisibility() {
    document.querySelectorAll('.student-mob-card').forEach(function(card) {
        const textEl = card.querySelector('.notice-text-content');
        const toggleBtn = card.querySelector('.btn-read-toggle');
        if (!textEl || !toggleBtn) return;

        // If currently truncated, compare scrollHeight against clientHeight
        if (textEl.classList.contains('is-truncated')) {
            if (textEl.scrollHeight > (textEl.clientHeight + 1)) {
                toggleBtn.style.display = 'inline-flex';
            } else {
                toggleBtn.style.display = 'none';
            }
        }
    });
}

// 4. Clear Search Helper
function clearNoticeSearch() {
    const input = document.getElementById('noticeSearchInput');
    if (input) input.value = '';
    const form = document.getElementById('noticeSearchForm');
    if (form) form.submit();
}

// 5. Open Custom Mobile Bottom Sheet Modal
function openNoticeSheet(noticeId) {
    const dataEl = document.getElementById('notice-data-' + noticeId);
    if (!dataEl) return;

    const title = dataEl.getAttribute('data-title');
    const status = dataEl.getAttribute('data-status');
    const statusClass = dataEl.getAttribute('data-status-class');
    const statusIcon = dataEl.getAttribute('data-status-icon');
    const dates = dataEl.getAttribute('data-dates');
    const publishedTime = dataEl.getAttribute('data-published-time');
    const attachmentUrl = dataEl.getAttribute('data-attachment');
    const attachmentName = dataEl.getAttribute('data-attachment-name');
    const message = dataEl.querySelector('.full-message') ? dataEl.querySelector('.full-message').innerText : '';

    // Fixed Meta elements (PINNED at top)
    const statusPill = document.getElementById('sheetNoticeStatus');
    statusPill.className = 'mob-status-pill ' + statusClass;
    statusPill.innerHTML = '<i class="fa ' + statusIcon + '"></i> ' + status;

    document.getElementById('sheetNoticeDates').innerHTML = '<i class="fa fa-calendar"></i> ' + dates;
    document.getElementById('sheetNoticePublished').innerHTML = '<i class="fa fa-clock-o"></i> ' + publishedTime;

    // Moveable / Scrollable body elements
    document.getElementById('sheetNoticeTitle').innerText = title;
    document.getElementById('sheetNoticeBody').innerText = message;

    const attachWrap = document.getElementById('sheetNoticeAttachment');
    if (attachmentUrl) {
        attachWrap.style.display = 'block';
        document.getElementById('sheetAttachmentLink').href = attachmentUrl;
        document.getElementById('sheetAttachmentName').innerText = attachmentName;
    } else {
        attachWrap.style.display = 'none';
    }

    // Reset scroll to top of notice body
    const bodyEl = document.querySelector('.mob-sheet-body');
    if (bodyEl) bodyEl.scrollTop = 0;

    // Show backdrop and slide up sheet
    document.getElementById('mobNoticeBackdrop').classList.add('show');
    document.getElementById('mobNoticeSheet').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeNoticeSheet() {
    const backdrop = document.getElementById('mobNoticeBackdrop');
    const sheet = document.getElementById('mobNoticeSheet');
    if (backdrop) backdrop.classList.remove('show');
    if (sheet) sheet.classList.remove('show');
    document.body.style.overflow = '';
}

// 6. DOM Event Listeners
document.addEventListener('DOMContentLoaded', function () {
    // Sticky Tracker Listeners
    window.addEventListener('scroll', checkStickyTracker, { passive: true });
    window.addEventListener('touchmove', checkStickyTracker, { passive: true });
    window.addEventListener('resize', checkStickyTracker, { passive: true });
    window.addEventListener('orientationchange', checkStickyTracker, { passive: true });
    setTimeout(checkStickyTracker, 80);

    // Initial check for Read More visibility
    updateReadMoreVisibility();
    window.addEventListener('resize', updateReadMoreVisibility);

    // Close sheet on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeNoticeSheet();
    });

    // Real-time client-side filter as student types
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
            setTimeout(updateReadMoreVisibility, 50);
        });
    }

    // Auto-open notice details modal if notice_id parameter or hash is present in URL
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const noticeIdParam = urlParams.get('notice_id') || urlParams.get('id');
        const hashMatch = window.location.hash.match(/noticeCard-(\d+)/);
        const targetNoticeId = noticeIdParam || (hashMatch ? hashMatch[1] : null);

        if (targetNoticeId) {
            setTimeout(function() {
                const cardEl = document.getElementById('noticeCard-' + targetNoticeId);
                if (cardEl) {
                    cardEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                openNoticeSheet(targetNoticeId);
            }, 180);
        }
    } catch(err) {}
});
</script>
@endsection

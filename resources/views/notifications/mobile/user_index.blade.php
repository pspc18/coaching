@php
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $roleId = (int) Session::get('role_id');
    $imageShowPath = env('IMAGE_SHOW_PATH');
    $totalCount = $totalCount ?? $notifications->total();
    $unreadCount = $unreadCount ?? 0;
    $readCount = $readCount ?? max(0, $totalCount - $unreadCount);
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE NOTIFICATIONS DIRECTORY STYLES
   - Aligned with Arise ERP Mobile Design System (admissionView, expenseView)
   - Sharp 4px radii, Arise Deep-Navy palette (#001833 -> #002C54)
   - Glassmorphic Hero Card with Live KPI Stats & Micro Subtitles
   - Compact Search & Horizontal Filter Chips Toolbar
   - Clean 4px Cards Feed with Color-Coded Accent Borders & Sharp 36px Avatars
   - Standardized 29px/30px Action Buttons with Theme Palettes
   - Standardized Micro Badges (8.5px/9px bold)
   ========================================================================== */

/* 1. Glassmorphic Hero Card */
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

/* 3 Metrics Glance Grid - Aligned with expenseView KPI Cards */
.mob-metrics-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-metric-box {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 4px;
    padding: 6px 4px;
    text-align: center;
    transition: background .15s ease;
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
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-metric-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
}
.mob-metric-val.val-unread { color: #f87171; }
.mob-metric-val.val-read { color: #4ade80; }
.mob-metric-val.val-total { color: #38bdf8; }
.mob-metric-sub {
    font-size: 8.5px;
    color: #cbd5e1;
    font-weight: 600;
    margin-top: 1px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Hero Fast Actions Bar - Exact 2-Column Full Width Grid */
.mob-actions-bar {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    width: 100%;
}
.mob-actions-bar form {
    margin: 0;
    padding: 0;
    width: 100%;
}
.mob-act-btn {
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 4px;
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
    font-family: inherit;
    box-sizing: border-box;
}
.mob-act-btn:active {
    transform: scale(0.97);
}
.mob-act-btn-readall {
    background: #0284c7;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    border: 1px solid #0284c7;
}
.mob-act-btn-clearall {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn[disabled] {
    opacity: 0.45;
    pointer-events: none;
}

/* 2. Compact Search & Horizontal Filter Chips Toolbar */
.mob-filter-toolbar {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mob-search-row {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.mob-search-input-wrap {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}
.mob-search-input-wrap i.fa-search {
    position: absolute;
    left: 9px;
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}
.mob-search-input {
    width: 100%;
    height: 30px;
    padding: 0 28px 0 28px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    transition: all .15s ease;
}
.mob-search-input:focus {
    border-color: #0284c7;
    background: #ffffff;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    color: #94a3b8;
    cursor: pointer;
    font-size: 12px;
    display: none;
}

/* Horizontal Filter Chips */
.mob-chips-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding-bottom: 2px;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.mob-chips-scroll::-webkit-scrollbar {
    display: none;
}
.mob-chip {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .12s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.mob-chip:active {
    transform: scale(0.95);
}
.mob-chip.active {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
    box-shadow: 0 1px 4px rgba(0, 44, 84, 0.2);
}
.mob-chip-count {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 2px;
    background: rgba(0, 0, 0, 0.08);
}
.mob-chip.active .mob-chip-count {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
.mob-chip-count.badge-unread {
    background: #fee2e2;
    color: #dc2626;
}
.mob-chip.active .mob-chip-count.badge-unread {
    background: #ef4444;
    color: #ffffff;
}

/* 3. Native Notification Cards Feed */
.notif-feed-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
    padding-bottom: 40px;
}
.notif-mob-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 9px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: all .15s ease;
    border-left: 3.5px solid #cbd5e1;
}
.notif-mob-card.border-notice { border-left-color: #d97706; }
.notif-mob-card.border-complaint { border-left-color: #dc2626; }
.notif-mob-card.border-approval { border-left-color: #4f46e5; }
.notif-mob-card.border-default { border-left-color: #0284c7; }
.notif-mob-card.is-unread {
    background: #fbfcfe;
    border-color: #bae6fd;
}

/* Card Header */
.notif-card-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 7px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
}
.notif-avatar-box {
    width: 36px;
    height: 36px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13.5px;
    font-weight: 800;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
}
.notif-avatar-box.avatar-notice {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
}
.notif-avatar-box.avatar-complaint {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}
.notif-avatar-box.avatar-approval {
    background: #e0e7ff;
    color: #4338ca;
    border-color: #c7d2fe;
}
.notif-avatar-box.avatar-default {
    background: #e0f2fe;
    color: #0284c7;
    border-color: #bae6fd;
}

.notif-header-info {
    flex: 1;
    overflow: hidden;
}
.notif-name-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.notif-mob-title {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    text-decoration: none !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.25;
}
.notif-pills-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 2px;
    flex-wrap: wrap;
}
.notif-type-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1.5px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
}
.notif-type-badge.badge-notice { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.notif-type-badge.badge-complaint { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.notif-type-badge.badge-approval { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
.notif-type-badge.badge-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

.mob-date-badge {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}

/* Status Badges */
.mob-status-pill {
    font-size: 8.5px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .02em;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.status-pill-unread {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.status-pill-read {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

/* Card Body */
.notif-card-body {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 6px;
}
.notif-note-preview {
    font-size: 11px;
    font-weight: 500;
    color: #1e293b;
    background: #f8fafc;
    border-left: 2.5px solid #002C54;
    padding: 5px 8px;
    border-radius: 0 3px 3px 0;
    line-height: 16px;
    word-break: break-word;
    white-space: pre-line;
    margin: 0;
}
.notif-note-preview.is-collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-bottom: 0 !important;
    max-height: 53px; /* 5px padding-top + (3 lines * 16px line-height = 48px) = 53px */
}

/* Embedded Complaint Student Info */
.mob-complaint-box {
    margin-top: 4px;
    padding: 6px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.mob-complaint-left {
    display: flex;
    align-items: center;
    gap: 7px;
    min-width: 0;
}
.mob-student-avatar-mini {
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
    border: 1px solid #cbd5e1;
}
.mob-student-avatar-mini img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.mob-student-meta-mini {
    min-width: 0;
}
.mob-student-name-mini {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-student-sub-mini {
    font-size: 9px;
    color: #64748b;
    line-height: 1.1;
    font-weight: 600;
}
.mob-complaint-right {
    text-align: right;
    flex-shrink: 0;
}
.mob-ticket-badge {
    font-size: 9.5px;
    font-weight: 800;
    color: #0284c7;
    display: block;
}
.mob-complaint-status-mini {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
}

/* Inline Review Box (Admin Role 1) */
.mob-review-box {
    margin-top: 6px;
    padding: 7px 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
}
.mob-review-headline {
    font-size: 10px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-review-textarea {
    width: 100%;
    height: 44px;
    padding: 5px 7px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    margin-bottom: 5px;
    outline: none;
    font-family: inherit;
    font-weight: 500;
}
.mob-review-textarea:focus {
    border-color: #0284c7;
}
.mob-review-actions {
    display: flex;
    gap: 4px;
}
.mob-review-btn {
    flex: 1;
    height: 28px;
    border: none;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    cursor: pointer;
    color: #ffffff;
    font-family: inherit;
}
.mob-review-btn-approve { background: #16a34a; }
.mob-review-btn-reject { background: #dc2626; }

/* Card Actions Strip - 29px/30px standardized across admissionView & expenseView */
.notif-card-actions {
    display: flex;
    gap: 5px;
    border-top: 1px solid #f1f5f9;
    padding-top: 6px;
    align-items: center;
}
.notif-act-btn {
    flex: 1;
    height: 29px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    cursor: pointer;
    transition: all .1s ease;
    white-space: nowrap;
    font-family: inherit;
}
.notif-act-btn:active {
    transform: scale(0.96);
}
.notif-act-btn.btn-read {
    background: #ecfdf5;
    border-color: #a7f3d0;
    color: #047857;
    flex: 1.1;
}
.notif-act-btn.btn-toggle {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
    flex: 1.1;
}
.notif-act-btn.btn-pdf {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
    flex: 1;
}
.notif-act-btn.btn-ticket {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff !important;
    flex: 1.2;
}
.notif-act-btn.btn-student {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0284c7;
    flex: 1;
}

/* Empty State */
.notif-empty-box {
    text-align: center;
    padding: 38px 16px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #64748b;
}
.notif-empty-box i {
    font-size: 28px;
    color: #cbd5e1;
    margin-bottom: 6px;
}
.notif-empty-box h4 {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 2px;
}
.notif-empty-box p {
    font-size: 10.5px;
    color: #64748b;
    margin: 0;
}

/* Pagination Wrap with clearance for bottom navigation dock */
.notif-pagination-wrap {
    margin-top: 8px;
    margin-bottom: calc(var(--bottom-nav-height, 52px) + 20px);
    display: flex;
    justify-content: center;
}
.notif-pagination-wrap .pagination {
    margin: 0;
    gap: 3px;
}
.notif-pagination-wrap .page-item .page-link {
    font-size: 10.5px;
    font-weight: 700;
    padding: 4px 9px;
    border-radius: 3px;
    color: #002C54;
    border: 1px solid #cbd5e1;
}
.notif-pagination-wrap .page-item.active .page-link {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
</style>
@endsection

@section('content')

{{-- 1. Signature Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-bell text-primary"></i> Notifications Hub
        </div>
        <div class="mob-session-pill">
            <i class="fa fa-graduation-cap mr-1"></i> {{ $currentSessionName }}
        </div>
    </div>

    {{-- Metrics Glance Grid --}}
    <div class="mob-metrics-grid">
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-bell-o text-info"></i> Total</span>
            <span class="mob-metric-val val-total" id="metricTotalVal">{{ $totalCount }}</span>
            <span class="mob-metric-sub">All Received</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-envelope-o text-danger"></i> Unread</span>
            <span class="mob-metric-val val-unread" id="metricUnreadVal">{{ $unreadCount }}</span>
            <span class="mob-metric-sub">Requires Action</span>
        </div>
        <div class="mob-metric-box">
            <span class="mob-metric-tag"><i class="fa fa-check-circle-o text-success"></i> Read</span>
            <span class="mob-metric-val val-read" id="metricReadVal">{{ $readCount }}</span>
            <span class="mob-metric-sub">Archived</span>
        </div>
    </div>

    {{-- Fast Action Bar - 2-Column Full Width Grid --}}
    <div class="mob-actions-bar">
        <form method="post" action="{{ route('user.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="mob-act-btn mob-act-btn-readall" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                <i class="fa fa-check-circle"></i> Mark All Read
            </button>
        </form>
        <button type="button" class="mob-act-btn mob-act-btn-clearall" id="btnOpenClearAllModal" {{ $totalCount === 0 ? 'disabled' : '' }}>
            <i class="fa fa-trash"></i> Clear All
        </button>
    </div>
</div>

{{-- Flash Feedback Alert --}}
@if(session('message'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:11px; border-radius:3px; font-weight:700;">
        <i class="fa fa-check mr-1"></i> {{ session('message') }}
    </div>
@endif

{{-- 2. Compact Search & Filter Toolbar --}}
<div class="mob-filter-toolbar">
    <div class="mob-search-row">
        <div class="mob-search-input-wrap">
            <i class="fa fa-search"></i>
            <input type="text" id="notifSearchInput" class="mob-search-input" placeholder="Search notifications by title or text..." autocomplete="off">
            <i class="fa fa-times mob-search-clear" id="notifSearchClear"></i>
        </div>
    </div>
    <div class="mob-chips-scroll">
        <a href="{{ url('user-notifications?filter=all') }}" class="mob-chip {{ $filter === 'all' ? 'active' : '' }}">
            <span>All</span>
            <span class="mob-chip-count">{{ $totalCount }}</span>
        </a>
        <a href="{{ url('user-notifications?filter=unread') }}" class="mob-chip {{ $filter === 'unread' ? 'active' : '' }}">
            <span>Unread</span>
            <span class="mob-chip-count badge-unread" id="chipUnreadCount">{{ $unreadCount }}</span>
        </a>
        <a href="{{ url('user-notifications?filter=read') }}" class="mob-chip {{ $filter === 'read' ? 'active' : '' }}">
            <span>Read</span>
            <span class="mob-chip-count" id="chipReadCount">{{ $readCount }}</span>
        </a>
    </div>
</div>

{{-- 3. Native Notifications Feed Cards --}}
<div class="notif-feed-list">
    @forelse($notifications as $notification)
        @php
            $isUnread = (int) $notification->message_seen === 0;
            $rawContent = (string) $notification->content;
            $rawContent = str_replace("\r", "", $rawContent);
            $lines = array_values(array_filter(array_map('trim', explode("\n", $rawContent)), function($line) {
                return $line !== '';
            }));
            $content = implode("\n", $lines);
            $isLong = count($lines) > 3 || mb_strlen($content) > 130;
            $isNotice = $notification->type === 'notice';
            $isApprovalRequest = $notification->type === 'notice_approval_request';
            $managedNotice = $notification->managedNotice;
            $isComplaint = $notification->type === 'complaint_admin';
            $complaint = $notification->complaintContext;
            $complaintStudent = $complaint ? $complaint->student : null;

            $borderModifier = match(true) {
                $isComplaint => 'border-complaint',
                $isNotice => 'border-notice',
                $isApprovalRequest => 'border-approval',
                default => 'border-default'
            };

            $avatarModifier = match(true) {
                $isComplaint => 'avatar-complaint',
                $isNotice => 'avatar-notice',
                $isApprovalRequest => 'avatar-approval',
                default => 'avatar-default'
            };

            $faIcon = match(true) {
                $isComplaint => 'fa-comments-o',
                $isNotice => 'fa-bullhorn',
                $isApprovalRequest => 'fa-shield',
                default => 'fa-bell-o'
            };
        @endphp

        <article class="notif-mob-card {{ $borderModifier }} {{ $isUnread ? 'is-unread' : '' }}" id="user-notification-{{ $notification->id }}">
            {{-- Header with 36px circular avatar --}}
            <div class="notif-card-header">
                <div class="notif-avatar-box {{ $avatarModifier }}">
                    <i class="fa {{ $faIcon }}"></i>
                </div>
                <div class="notif-header-info">
                    <div class="notif-name-row">
                        <span class="notif-mob-title">{{ $notification->title ?: 'Notification' }}</span>
                        <span class="mob-status-pill {{ $isUnread ? 'status-pill-unread' : 'status-pill-read' }}" id="status-badge-{{ $notification->id }}">
                            {{ $isUnread ? 'Unread' : 'Read' }}
                        </span>
                    </div>
                    <div class="notif-pills-wrap">
                        @if($isNotice)
                            <span class="notif-type-badge badge-notice">Notice</span>
                        @elseif($isApprovalRequest)
                            <span class="notif-type-badge badge-approval">Approval</span>
                        @elseif($isComplaint)
                            <span class="notif-type-badge badge-complaint">Complaint</span>
                        @else
                            <span class="notif-type-badge badge-default">System</span>
                        @endif
                        <span class="mob-date-badge">
                            <i class="fa fa-clock-o"></i> {{ optional($notification->created_at)->format('d M Y, h:i A') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Message Body Box --}}
            <div class="notif-card-body">
                <div class="notif-note-preview {{ $isLong ? 'is-collapsed' : '' }}" id="user-message-{{ $notification->id }}">{{ $content ?: 'No notification message available.' }}</div>

                {{-- Complaint Embedded Student Row --}}
                @if($isComplaint && $complaint && $complaintStudent)
                    <div class="mob-complaint-box">
                        <div class="mob-complaint-left">
                            <div class="mob-student-avatar-mini">
                                @if(!empty($complaintStudent->image))
                                    <img src="{{ $imageShowPath . 'profile/' . rawurlencode($complaintStudent->image) }}" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <i class="fa fa-user" style="{{ !empty($complaintStudent->image) ? 'display:none;' : '' }}"></i>
                            </div>
                            <div class="mob-student-meta-mini">
                                <div class="mob-student-name-mini">
                                    {{ trim($complaintStudent->first_name . ' ' . $complaintStudent->last_name) ?: 'Student' }}
                                </div>
                                <div class="mob-student-sub-mini">
                                    {{ optional($complaintStudent->ClassTypes)->name ?? 'Class N/A' }}
                                    @if(!empty($complaintStudent->admissionNo)) · Adm: {{ $complaintStudent->admissionNo }} @endif
                                </div>
                            </div>
                        </div>
                        <div class="mob-complaint-right">
                            <span class="mob-ticket-badge">{{ $complaint->ticket_no }}</span>
                            <span class="mob-complaint-status-mini">{{ \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status) }}</span>
                        </div>
                    </div>
                @endif

                {{-- Approval Review Action Box (Role 1 Admin) --}}
                @if($isApprovalRequest && $roleId === 1 && $managedNotice)
                    @if($managedNotice->status === 'pending')
                        <div class="mob-review-box">
                            <div class="mob-review-headline">
                                <span><i class="fa fa-check-square-o mr-1"></i> Admin Notice Approval</span>
                                <span class="text-muted" style="font-size:9px;">{{ ucfirst($managedNotice->audience_type) }} ({{ $managedNotice->recipients->count() }} targets)</span>
                            </div>
                            <form action="{{ url('notice-management/'.$managedNotice->id.'/review') }}" method="post" onsubmit="return confirmMobNoticeReview(event, this);">
                                @csrf
                                <input type="hidden" name="return_to" value="user-notifications">
                                <textarea name="review_notes" class="mob-review-textarea" placeholder="Instructions or reason for decision..." required></textarea>
                                <div class="mob-review-actions">
                                    <button type="submit" name="decision" value="approved" class="mob-review-btn mob-review-btn-approve">
                                        <i class="fa fa-check"></i> Approve
                                    </button>
                                    <button type="submit" name="decision" value="rejected" class="mob-review-btn mob-review-btn-reject">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-light py-1 px-2 my-1" style="font-size:9.5px; border:1px solid #cbd5e1; border-radius:3px;">
                            <i class="fa {{ $managedNotice->status === 'approved' ? 'fa-check text-success' : 'fa-times text-danger' }} mr-1"></i>
                            Notice was <b>{{ $managedNotice->status }}</b>.
                        </div>
                    @endif
                @endif
            </div>

            @php
                $hasCardActions = $isLong || $isUnread || ($isNotice && $notification->managed_notice_id && $notification->attachment_path) || ($isComplaint && $complaint);
            @endphp
            @if($hasCardActions)
            {{-- Card Actions Strip --}}
            <div class="notif-card-actions">
                @if($isLong)
                    <button type="button" class="notif-act-btn btn-toggle mob-toggle-btn" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}">
                        <i class="fa fa-angle-down"></i> View More
                    </button>
                @endif

                @if($isUnread)
                    <button type="button" class="notif-act-btn btn-read mob-mark-read-btn" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}">
                        <i class="fa fa-check"></i> Mark Read
                    </button>
                @endif

                @if($isNotice && $notification->managed_notice_id && $notification->attachment_path)
                    <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}" class="notif-act-btn btn-pdf">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
                @endif

                @if($isComplaint && $complaint)
                    @if($complaintStudent)
                        <a href="{{ url('studentDetail/'.$complaintStudent->id) }}" class="notif-act-btn btn-student">
                            <i class="fa fa-user"></i> Student
                        </a>
                    @endif
                    <a href="{{ url('complaints-management/'.$complaint->id).'?highlight=notification#complaint-ticket' }}" class="notif-act-btn btn-ticket">
                        <i class="fa fa-eye"></i> View Ticket
                    </a>
                @endif
            </div>
            @endif
        </article>
    @empty
        <div class="notif-empty-box">
            <i class="fa fa-bell-slash-o"></i>
            <h4>No Notifications Found</h4>
            <p>You have no notifications in this category.</p>
        </div>
    @endforelse
</div>

{{-- 4. Mobile Pagination --}}
@if($notifications->hasPages())
    <div class="notif-pagination-wrap">
        {{ $notifications->links() }}
    </div>
@endif

{{-- Hidden Form for Native Slide-Up Confirmation Execution --}}
<form method="post" action="{{ route('user.notifications.clear-all') }}" id="clearAllHiddenForm" style="display:none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
function confirmMobNoticeReview(event, form) {
    var submitter = event.submitter || document.activeElement;
    var decisionValue = submitter && submitter.value === 'rejected' ? 'rejected' : 'approved';
    var decisionLabel = decisionValue === 'rejected' ? 'reject' : 'approve and publish';
    if (event) event.preventDefault();
    showMobileConfirm(
        decisionValue === 'rejected' ? 'Reject Notice' : 'Approve Notice',
        'Are you sure you want to ' + decisionLabel + ' this notice?',
        function() {
            var hiddenDecision = document.createElement('input');
            hiddenDecision.type = 'hidden';
            hiddenDecision.name = 'decision';
            hiddenDecision.value = decisionValue;
            form.appendChild(hiddenDecision);
            $(form).find('button[type="submit"]').prop('disabled', true);
            form.submit();
        },
        decisionValue === 'rejected' ? 'Reject' : 'Approve',
        decisionValue === 'rejected',
        decisionValue === 'rejected' ? 'fa-times text-danger' : 'fa-check text-success'
    );
    return false;
}

$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Instant AJAX Fast Mark As Read
    function markAsRead(id, readUrl, $button) {
        var $card = $('#user-notification-' + id);
        if (!$card.length || !$card.hasClass('is-unread')) return;

        $.ajax({
            url: readUrl,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function() {
                $card.removeClass('is-unread');
                var $statusBadge = $('#status-badge-' + id);
                if ($statusBadge.length) {
                    $statusBadge.text('Read').removeClass('status-pill-unread').addClass('status-pill-read');
                }
                if ($button && $button.hasClass('mob-mark-read-btn')) {
                    $button.fadeOut(150, function() {
                        var $actions = $(this).closest('.notif-card-actions');
                        $(this).remove();
                        if ($actions.length && $actions.children().length === 0) {
                            $actions.remove();
                        }
                    });
                }

                // Update Hero and Chip KPI numbers in real-time
                var $unreadMetric = $('#metricUnreadVal');
                var $readMetric = $('#metricReadVal');
                var $chipUnread = $('#chipUnreadCount');
                var $chipRead = $('#chipReadCount');

                if ($unreadMetric.length) {
                    var uCount = Math.max(0, (parseInt($unreadMetric.text()) || 0) - 1);
                    var rCount = (parseInt($readMetric.text()) || 0) + 1;
                    $unreadMetric.text(uCount);
                    $readMetric.text(rCount);
                    if ($chipUnread.length) $chipUnread.text(uCount);
                    if ($chipRead.length) $chipRead.text(rCount);
                }
            }
        });
    }

    // Toggle View More / View Less
    $('.mob-toggle-btn').on('click', function() {
        var id = $(this).data('id');
        var readUrl = $(this).data('read-url');
        var $content = $('#user-message-' + id);
        var isCollapsed = $content.hasClass('is-collapsed');

        if (isCollapsed) {
            $content.removeClass('is-collapsed');
            $(this).html('<i class="fa fa-angle-up"></i> View Less');
            markAsRead(id, readUrl, $(this));
        } else {
            $content.addClass('is-collapsed');
            $(this).html('<i class="fa fa-angle-down"></i> View More');
        }
    });

    // Mark as read click
    $('.mob-mark-read-btn').on('click', function() {
        var id = $(this).data('id');
        var readUrl = $(this).data('read-url');
        markAsRead(id, readUrl, $(this));
    });

    // Real-time client-side search filtering
    $('#notifSearchInput').on('input', function() {
        var query = ($(this).val() || '').toLowerCase().trim();
        if (query.length > 0) {
            $('#notifSearchClear').show();
            $('.notif-mob-card').each(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(query) !== -1);
            });
        } else {
            $('#notifSearchClear').hide();
            $('.notif-mob-card').show();
        }
    });

    $('#notifSearchClear').on('click', function() {
        $('#notifSearchInput').val('').trigger('input');
    });

    // Custom Native Slide-Up Confirmation Modal for Clear All
    $('#btnOpenClearAllModal').on('click', function(e) {
        e.preventDefault();
        if ($(this).attr('disabled')) return;
        showMobileConfirm(
            'Clear All Notifications',
            'Are you sure you want to permanently clear all notifications? All records will be removed.',
            function() {
                $('#clearAllHiddenForm').submit();
            },
            '<i class="fa fa-trash"></i> Yes, Clear All',
            true,
            'fa-trash text-danger'
        );
    });
});
</script>
@endsection

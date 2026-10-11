@php
    $getUser = Helper::getUser();
    $category = strtolower(trim((string) ($category ?? 'all')));
    $allowedCategories = ['all', 'attendance', 'fees', 'result', 'notice', 'complaint'];
    if (!in_array($category, $allowedCategories, true)) {
        $category = 'all';
    }

    $pageLabel = 'Notifications';
    $selectedCategoryLabel = [
        'all' => 'All Modules',
        'attendance' => 'Attendance',
        'fees' => 'Fees',
        'result' => 'Exam Results',
        'notice' => 'Notices',
        'complaint' => 'Complaints',
    ][$category] ?? 'All Modules';

    $categoryMeta = [
        'all' => ['label' => 'All', 'icon' => 'fa-bell-o', 'color' => '#0284c7'],
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-calendar-check-o', 'color' => '#0284c7'],
        'fees' => ['label' => 'Fees', 'icon' => 'fa-inr', 'color' => '#10b981'],
        'result' => ['label' => 'Results', 'icon' => 'fa-file-text-o', 'color' => '#8b5cf6'],
        'notice' => ['label' => 'Notices', 'icon' => 'fa-bullhorn', 'color' => '#f59e0b'],
        'complaint' => ['label' => 'Complaints', 'icon' => 'fa-comments-o', 'color' => '#64748b'],
    ];

    $buildUrl = function (array $params) {
        $params = array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
        return url('notificationFatchStudent' . (count($params) ? '?' . http_build_query($params) : ''));
    };

    $currentSearch = (string) ($search ?? request('search', ''));
    $studentName = Session::get('first_name') ?: ($getUser->first_name ?? 'Student');
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
    $totalCount = $notifications->total();
@endphp
@extends('student_login.layout.app')

@section('title', 'Notifications & Alerts')
@section('page_title', 'NOTIFICATIONS')
@section('page_sub', $studentName . ' · ' . $className)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE NOTIFICATIONS & ALERTS
   Exact Layout, Typography & Cohesive Theme System
   ========================================================================== */

.mob-notif-page {
    padding: 0 0 28px;
    color: #1e293b;
    width: 100%;
    box-sizing: border-box;
}

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
.mob-radar-val.danger-badge {
    color: #f87171;
}
.mob-radar-val.success-badge {
    color: #4ade80;
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

/* 2. Compact Search & Toolbar */
.mob-toolbar-wrap {
    margin-bottom: 8px;
}
.mob-search-box {
    position: relative;
    width: 100%;
    margin-bottom: 6px;
}
.mob-search-input {
    width: 100%;
    height: 34px;
    padding: 0 32px 0 30px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 11px;
    font-weight: 600;
    color: #0f172a;
    box-sizing: border-box;
    transition: all 0.18s ease;
}
.mob-search-input:focus {
    outline: none;
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
.mob-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
}
.mob-search-clear {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    font-size: 12px;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Category Pills (Horizontal Scroll) */
.mob-pill-scroll {
    display: flex;
    gap: 5px;
    overflow-x: auto;
    padding: 1px 0 6px;
    scrollbar-width: none;
}
.mob-pill-scroll::-webkit-scrollbar {
    display: none;
}
.mob-pill-btn {
    height: 28px;
    padding: 0 9px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569 !important;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.mob-pill-btn:hover {
    background: #f8fafc;
    border-color: #94a3b8;
}
.mob-pill-btn.active {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.22);
}
.mob-pill-count {
    min-width: 15px;
    height: 15px;
    padding: 0 3px;
    border-radius: 10px;
    background: #ef4444;
    color: #ffffff;
    font-size: 8px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

/* Secondary Actions Bar (Read / Unread tabs & Bulk Buttons) */
.mob-action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}
.mob-status-tabs {
    display: inline-flex;
    background: #e2e8f0;
    border-radius: 4px;
    padding: 2px;
    gap: 2px;
}
.mob-tab-btn {
    padding: 3px 8px;
    font-size: 9.5px;
    font-weight: 700;
    color: #475569 !important;
    text-decoration: none !important;
    border-radius: 3px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-tab-btn.active {
    background: #ffffff;
    color: #002C54 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.mob-tab-badge {
    background: #ef4444;
    color: #ffffff;
    font-size: 7.5px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 8px;
}

.mob-bulk-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-btn-bulk {
    height: 26px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0284c7;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mob-btn-bulk:hover:not(:disabled) {
    background: #f0f9ff;
    border-color: #0284c7;
}
.mob-btn-bulk.danger {
    color: #ef4444;
    border-color: #fca5a5;
    background: #fff5f5;
}
.mob-btn-bulk.danger:hover:not(:disabled) {
    background: #fee2e2;
    border-color: #ef4444;
}
.mob-btn-bulk:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

/* 3. Notification Card Stream */
.mob-notif-stream {
    display: flex;
    flex-direction: column;
    gap: 7px;
}
.mob-notif-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 10px 11px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    transition: all 0.18s ease;
    border-left: 3.5px solid #94a3b8;
}
.mob-notif-card.is-unread {
    background: #f8fafc;
    border-color: #cbd5e1;
    border-left-color: #0284c7;
    box-shadow: 0 2px 7px rgba(2, 132, 199, 0.08);
}
.mob-notif-card.tone-amber { border-left-color: #f59e0b; }
.mob-notif-card.tone-green { border-left-color: #10b981; }
.mob-notif-card.tone-purple { border-left-color: #8b5cf6; }
.mob-notif-card.tone-blue { border-left-color: #0284c7; }
.mob-notif-card.tone-red { border-left-color: #ef4444; }

.mob-notif-head {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 5px;
}
.mob-notif-icon {
    width: 30px;
    height: 30px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.mob-notif-icon.tone-amber { background: #fffbeb; color: #d97706; }
.mob-notif-icon.tone-green { background: #ecfdf5; color: #059669; }
.mob-notif-icon.tone-purple { background: #f5f3ff; color: #7c3aed; }
.mob-notif-icon.tone-blue { background: #f0f9ff; color: #0284c7; }
.mob-notif-icon.tone-red { background: #fef2f2; color: #dc2626; }

.mob-notif-info {
    flex: 1;
    min-width: 0;
}
.mob-notif-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
}
.mob-notif-title {
    font-size: 11.5px;
    font-weight: 750;
    color: #0f172a;
    line-height: 1.25;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-notif-badge-new {
    font-size: 8px;
    font-weight: 800;
    background: #ef4444;
    color: #ffffff;
    padding: 1px 5px;
    border-radius: 2px;
    letter-spacing: 0.3px;
    flex-shrink: 0;
    text-transform: uppercase;
}
.mob-notif-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
    font-size: 9px;
    color: #64748b;
}
.mob-notif-type-tag {
    font-weight: 700;
    font-size: 8px;
    padding: 1px 5px;
    border-radius: 2px;
    background: #f1f5f9;
    color: #475569;
    text-transform: uppercase;
}
.mob-notif-time {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    color: #94a3b8;
}

.mob-notif-body {
    font-size: 10.5px;
    color: #334155;
    line-height: 1.45;
    margin-bottom: 7px;
    word-break: break-word;
}
.mob-notif-body.collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Attachment preview button */
.mob-notif-attach {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    color: #0284c7 !important;
    text-decoration: none !important;
    margin-bottom: 6px;
    transition: all 0.15s ease;
}
.mob-notif-attach:hover {
    background: #f0f9ff;
    border-color: #bae6fd;
}

.mob-notif-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
    font-size: 9px;
    color: #94a3b8;
}
.mob-notif-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-btn-action {
    height: 22px;
    padding: 0 7px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-decoration: none !important;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0284c7 !important;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mob-btn-action:hover {
    background: #f0f9ff;
    border-color: #0284c7;
}
.mob-btn-action.btn-mark-read {
    color: #059669 !important;
    border-color: #a7f3d0;
    background: #ecfdf5;
}
.mob-btn-action.btn-mark-read:hover {
    background: #d1fae5;
    border-color: #10b981;
}

/* Empty State */
.mob-empty-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 32px 16px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin-bottom: 10px;
}
.mob-empty-title {
    font-size: 13px;
    font-weight: 750;
    color: #1e293b;
    margin: 0 0 4px;
}
.mob-empty-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0 0 12px;
}
.mob-btn-reset {
    height: 30px;
    padding: 0 14px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    background: #002C54;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.2);
}

/* Pagination */
.mob-pagination-wrap {
    margin-top: 12px;
    display: flex;
    justify-content: center;
}
.mob-pagination-wrap .pagination {
    margin: 0;
    gap: 3px;
    flex-wrap: wrap;
    justify-content: center;
}
.mob-pagination-wrap .page-link {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    color: #475569;
}
.mob-pagination-wrap .page-item.active .page-link {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}

/* Modal details preview */
.modal-notif-content {
    white-space: pre-line;
    font-size: 11px;
    line-height: 1.6;
    color: #1e293b;
    word-break: break-word;
}

/* Dark Theme Adjustments */
:root[data-theme="dark"] .mob-notif-page { color: #e2e8f0; }
:root[data-theme="dark"] .mob-search-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
:root[data-theme="dark"] .mob-pill-btn { background: #1e293b; border-color: #334155; color: #cbd5e1 !important; }
:root[data-theme="dark"] .mob-pill-btn.active { background: #0284c7; border-color: #0284c7; color: #ffffff !important; }
:root[data-theme="dark"] .mob-status-tabs { background: #1e293b; }
:root[data-theme="dark"] .mob-tab-btn { color: #94a3b8 !important; }
:root[data-theme="dark"] .mob-tab-btn.active { background: #0f172a; color: #38bdf8 !important; }
:root[data-theme="dark"] .mob-btn-bulk { background: #1e293b; border-color: #334155; color: #38bdf8; }
:root[data-theme="dark"] .mob-notif-card { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-notif-card.is-unread { background: #172554; border-color: #1d4ed8; }
:root[data-theme="dark"] .mob-notif-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-notif-body { color: #cbd5e1; }
:root[data-theme="dark"] .mob-notif-footer { border-top-color: #334155; }
:root[data-theme="dark"] .mob-empty-box { background: #1e293b; border-color: #334155; }
:root[data-theme="dark"] .mob-empty-title { color: #f8fafc; }
:root[data-theme="dark"] .mob-btn-action { background: #0f172a; border-color: #334155; }
</style>
@endsection

@section('content')
<div class="mob-notif-page">

    {{-- 1. Glassmorphic Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-top">
            <div class="mob-hero-title">
                <i class="fa fa-bell"></i> Alerts & Notifications
            </div>
            <div class="mob-session-pill">
                <i class="fa fa-tag"></i> {{ $selectedCategoryLabel }}
            </div>
        </div>

        <div class="mob-dual-grid">
            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-inbox"></i> Total Alerts</span>
                <div class="mob-radar-val" id="statTotalVal">{{ $totalCount }}</div>
                <span class="mob-radar-meta">{{ $selectedCategoryLabel }}</span>
            </div>

            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-envelope-o"></i> Unread</span>
                <div class="mob-radar-val {{ $unreadCount > 0 ? 'danger-badge' : 'success-badge' }}" id="statUnreadVal">
                    {{ $unreadCount }}
                </div>
                <span class="mob-radar-meta">{{ $unreadCount > 0 ? 'Requires attention' : 'All caught up' }}</span>
            </div>

            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-filter"></i> Active Scope</span>
                <div class="mob-radar-val" style="font-size:12px; margin:4px 0;">
                    {{ $categoryMeta[$category]['label'] ?? 'All' }}
                </div>
                <span class="mob-radar-meta">{{ $filter === 'all' ? 'All records' : ucfirst($filter).' only' }}</span>
            </div>

            <div class="mob-radar-col">
                <span class="mob-radar-tag"><i class="fa fa-clock-o"></i> Student</span>
                <div class="mob-radar-val" style="font-size:12px; margin:4px 0; text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">
                    {{ $studentName }}
                </div>
                <span class="mob-radar-meta">{{ $className }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Compact Search & Filter Toolbar --}}
    <div class="mob-toolbar-wrap">
        {{-- Search Input Box --}}
        <div class="mob-search-box">
            <i class="fa fa-search mob-search-icon"></i>
            <input type="text"
                   id="notifSearchInput"
                   class="mob-search-input"
                   placeholder="Search title, notice or message..."
                   value="{{ $currentSearch }}"
                   autocomplete="off">
            <button type="button"
                    class="mob-search-clear"
                    id="btnNotifSearchClear"
                    style="{{ $currentSearch !== '' ? 'display:flex;' : 'display:none;' }}"
                    title="Clear search">
                <i class="fa fa-times-circle"></i>
            </button>
        </div>

        {{-- Category Pills Row --}}
        <div class="mob-pill-scroll">
            @foreach($categoryMeta as $key => $meta)
                @php
                    $urlParams = ['category' => $key];
                    if ($filter !== 'all') $urlParams['filter'] = $filter;
                    if ($currentSearch !== '') $urlParams['search'] = $currentSearch;
                    $badgeCount = (int) ($categoryCounts[$key] ?? 0);
                    $isActive = ($category === $key);
                @endphp
                <a href="{{ $buildUrl($urlParams) }}" class="mob-pill-btn {{ $isActive ? 'active' : '' }}">
                    <i class="fa {{ $meta['icon'] }}" style="{{ $isActive ? '' : 'color: '.$meta['color'].';' }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    @if($badgeCount > 0)
                        <span class="mob-pill-count" id="pillCount-{{ $key }}">{{ $badgeCount > 99 ? '99+' : $badgeCount }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Action Bar: Read/Unread Segmented Switch & Bulk Action Buttons --}}
        <div class="mob-action-row">
            <div class="mob-status-tabs">
                @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $tabKey => $tabLabel)
                    @php
                        $tabParams = ['filter' => $tabKey];
                        if ($category !== 'all') $tabParams['category'] = $category;
                        if ($currentSearch !== '') $tabParams['search'] = $currentSearch;
                        $isTabActive = ($filter === $tabKey);
                    @endphp
                    <a href="{{ $buildUrl($tabParams) }}" class="mob-tab-btn {{ $isTabActive ? 'active' : '' }}">
                        {{ $tabLabel }}
                        @if($tabKey === 'unread' && $unreadCount > 0)
                            <span class="mob-tab-badge" id="tabUnreadBadge">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mob-bulk-actions">
                <form method="POST" action="{{ route('student.attendance-notifications.mark-all-read') }}" id="formMarkAllRead" style="margin:0;">
                    @csrf
                    @if($category !== 'all')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    <button type="submit" class="mob-btn-bulk" id="btnMarkAllRead" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                        <i class="fa fa-check-square-o"></i> Mark all read
                    </button>
                </form>

                <form method="POST" action="{{ route('student.attendance-notifications.clear-all') }}" id="formClearAll" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    @if($category !== 'all')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    <button type="submit" class="mob-btn-bulk danger" id="btnClearAll" {{ $totalCount === 0 ? 'disabled' : '' }}>
                        <i class="fa fa-trash-o"></i> Clear
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 3. Notification Card Stream --}}
    <div class="mob-notif-stream" id="notifStreamList">
        @forelse($notifications as $notification)
            @php
                $isUnread = ((int) $notification->message_seen === 0);
                $content = trim(strip_tags((string) ($notification->content ?? '')));
                $notificationType = strtolower((string) ($notification->type ?? ''));
                $typeConfig = [
                    'notice' => ['icon' => 'fa-bullhorn', 'label' => 'Notice', 'tone' => 'amber'],
                    'exam_result' => ['icon' => 'fa-file-text-o', 'label' => 'Result', 'tone' => 'purple'],
                    'fee_payment' => ['icon' => 'fa-inr', 'label' => 'Payment', 'tone' => 'green'],
                    'fee_revert' => ['icon' => 'fa-undo', 'label' => 'Fee Update', 'tone' => 'red'],
                    'complaint_reply' => ['icon' => 'fa-reply', 'label' => 'Support', 'tone' => 'blue'],
                    'complaint_status' => ['icon' => 'fa-comments-o', 'label' => 'Complaint', 'tone' => 'blue'],
                ];
                $config = $typeConfig[$notificationType] ?? ['icon' => 'fa-calendar-check-o', 'label' => 'Attendance', 'tone' => 'blue'];
                $createdAt = \Carbon\Carbon::parse($notification->created_at);
                $isLong = mb_strlen($content) > 130 || substr_count($content, "\n") > 1;
            @endphp

            <div class="mob-notif-card {{ $isUnread ? 'is-unread' : '' }} tone-{{ $config['tone'] }}"
                 id="notifCard-{{ $notification->id }}"
                 data-title="{{ strtolower($notification->title ?? '') }}"
                 data-content="{{ strtolower($content) }}">

                {{-- Card Header --}}
                <div class="mob-notif-head">
                    <div class="mob-notif-icon tone-{{ $config['tone'] }}">
                        <i class="fa {{ $config['icon'] }}"></i>
                    </div>

                    <div class="mob-notif-info">
                        <div class="mob-notif-title-row">
                            <h3 class="mob-notif-title" title="{{ $notification->title }}">
                                {{ $notification->title ?: 'Notification' }}
                            </h3>
                            @if($isUnread)
                                <span class="mob-notif-badge-new" id="unreadBadge-{{ $notification->id }}">New</span>
                            @endif
                        </div>

                        <div class="mob-notif-meta-row">
                            <span class="mob-notif-type-tag">{{ $config['label'] }}</span>
                            <span class="mob-notif-time">
                                <i class="fa fa-clock-o"></i> {{ $createdAt->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Message Body --}}
                <div class="mob-notif-body {{ $isLong ? 'collapsed' : '' }}" id="notifBody-{{ $notification->id }}">
                    {{ $content !== '' ? $content : 'No message content available.' }}
                </div>

                {{-- Attachment (if notice has PDF) --}}
                @if($notificationType === 'notice' && $notification->managed_notice_id && $notification->attachment_path)
                    <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}"
                       class="mob-notif-attach"
                       target="_blank">
                        <i class="fa fa-file-pdf-o text-danger"></i>
                        <span>{{ $notification->attachment_name ?: 'Download Notice Attachment' }}</span>
                        <i class="fa fa-download ml-auto text-muted"></i>
                    </a>
                @endif

                {{-- Card Footer & Actions --}}
                <div class="mob-notif-footer">
                    <span>
                        <i class="fa fa-calendar-o"></i> {{ $createdAt->format('d M Y, h:i A') }}
                    </span>

                    <div class="mob-notif-actions">
                        @if($isLong)
                            <button type="button"
                                    class="mob-btn-action btn-toggle-expand"
                                    data-id="{{ $notification->id }}"
                                    data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}"
                                    aria-expanded="false">
                                <i class="fa fa-angle-down"></i> Expand
                            </button>
                        @endif

                        @if($isUnread)
                            <button type="button"
                                    class="mob-btn-action btn-mark-read"
                                    id="btnMarkRead-{{ $notification->id }}"
                                    data-id="{{ $notification->id }}"
                                    data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}">
                                <i class="fa fa-check"></i> Read
                            </button>
                        @endif

                        <button type="button"
                                class="mob-btn-action btn-view-quick"
                                data-title="{{ e($notification->title ?: 'Notification') }}"
                                data-time="{{ $createdAt->format('d M Y, h:i A') }} ({{ $createdAt->diffForHumans() }})"
                                data-type="{{ $config['label'] }}"
                                data-content="{{ e($content) }}"
                                data-id="{{ $notification->id }}"
                                data-unread="{{ $isUnread ? '1' : '0' }}"
                                data-read-url="{{ route('student.attendance-notifications.mark-read', $notification->id) }}">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="mob-empty-box">
                <div class="mob-empty-icon">
                    <i class="fa fa-bell-slash-o"></i>
                </div>
                <h3 class="mob-empty-title">No Notifications Found</h3>
                <p class="mob-empty-desc">
                    {{ $filter === 'all'
                        ? 'You do not have any alerts in '.$selectedCategoryLabel.'. New school announcements will appear here.'
                        : 'There are no '.$filter.' notifications in this section.' }}
                </p>
                @if($filter !== 'all' || $category !== 'all' || $currentSearch !== '')
                    <a href="{{ $buildUrl(['category' => 'all', 'filter' => 'all']) }}" class="mob-btn-reset">
                        <i class="fa fa-refresh"></i> Reset Filters
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- 4. Mobile Pagination --}}
    @if($notifications->hasPages())
        <div class="mob-pagination-wrap">
            {{ $notifications->links() }}
        </div>
    @endif

</div>

{{-- Quick View Details Modal --}}
<div class="modal fade" id="notifQuickModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px; margin: 16px auto;">
        <div class="modal-content" style="border-radius: 6px; border: 1px solid rgba(0, 44, 84, 0.2); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.25);">
            <div class="modal-header" style="background: linear-gradient(135deg, #001833 0%, #002C54 100%); color: #ffffff; padding: 10px 14px; border-bottom: none;">
                <div style="min-width: 0;">
                    <div style="font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px; color: #38bdf8; font-weight: 700;" id="modalNotifType">
                        Notification
                    </div>
                    <h5 class="modal-title" id="modalNotifTitle" style="font-size: 13px; font-weight: 800; color: #ffffff; margin: 0; line-height: 1.25; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Notification Title
                    </h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85; text-shadow: none; font-size: 20px; padding: 8px 12px; margin: -10px -14px -10px auto;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 14px; background: #ffffff;">
                <div style="font-size: 9.5px; color: #64748b; margin-bottom: 10px; display: flex; align-items: center; gap: 5px;">
                    <i class="fa fa-calendar-check-o"></i> <span id="modalNotifTime">-</span>
                </div>
                <div class="modal-notif-content" id="modalNotifBody" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 12px;">
                    -
                </div>
            </div>
            <div class="modal-footer" style="padding: 8px 14px; background: #f1f5f9; border-top: 1px solid #e2e8f0; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="font-size: 10px; font-weight: 700; height: 28px; padding: 0 14px; border-radius: 4px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = @json(csrf_token());
    const searchInput = document.getElementById('notifSearchInput');
    const searchClear = document.getElementById('btnNotifSearchClear');
    const cards = document.querySelectorAll('.mob-notif-card');

    // 1. Live Instant Client Search Filter (Zero lag)
    let searchDebounceTimer = null;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            if (searchClear) {
                searchClear.style.display = query !== '' ? 'flex' : 'none';
            }

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function () {
                let matchCount = 0;
                cards.forEach(function (card) {
                    const title = card.getAttribute('data-title') || '';
                    const content = card.getAttribute('data-content') || '';
                    if (query === '' || title.includes(query) || content.includes(query)) {
                        card.style.display = '';
                        matchCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }, 120);
        });

        if (searchClear) {
            searchClear.addEventListener('click', function () {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        }
    }

    // 2. Optimistic AJAX Mark Read Handler
    function markAsRead(notificationId, readUrl) {
        const card = document.getElementById('notifCard-' + notificationId);
        if (!card || !card.classList.contains('is-unread')) {
            return Promise.resolve();
        }

        // Optimistic UI state immediately for super snappy feel
        card.classList.remove('is-unread');
        const badge = document.getElementById('unreadBadge-' + notificationId);
        if (badge) badge.remove();
        const markBtn = document.getElementById('btnMarkRead-' + notificationId);
        if (markBtn) markBtn.remove();

        // Decrement Stat Badges
        const unreadStat = document.getElementById('statUnreadVal');
        if (unreadStat) {
            const current = Math.max(0, parseInt(unreadStat.textContent.trim(), 10) - 1);
            unreadStat.textContent = current;
            if (current === 0) {
                unreadStat.classList.remove('danger-badge');
                unreadStat.classList.add('success-badge');
            }
        }
        const tabBadge = document.getElementById('tabUnreadBadge');
        if (tabBadge) {
            const currentTab = Math.max(0, parseInt(tabBadge.textContent.trim(), 10) - 1);
            if (currentTab > 0) {
                tabBadge.textContent = currentTab;
            } else {
                tabBadge.remove();
            }
        }

        return fetch(readUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(function (err) {
            console.error('Mark read failed:', err);
        });
    }

    // Single Mark Read button click
    document.querySelectorAll('.btn-mark-read').forEach(function (btn) {
        btn.addEventListener('click', function () {
            this.disabled = true;
            markAsRead(this.dataset.id, this.dataset.readUrl);
        });
    });

    // Expand / Collapse long notification text
    document.querySelectorAll('.btn-toggle-expand').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const body = document.getElementById('notifBody-' + id);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            if (body) {
                body.classList.toggle('collapsed', isExpanded);
            }
            this.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
            this.innerHTML = isExpanded
                ? '<i class="fa fa-angle-down"></i> Expand'
                : '<i class="fa fa-angle-up"></i> Collapse';

            if (isExpanded === false && this.dataset.readUrl) {
                markAsRead(id, this.dataset.readUrl);
            }
        });
    });

    // 3. Quick View Modal Trigger
    document.querySelectorAll('.btn-view-quick').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const time = this.dataset.time;
            const type = this.dataset.type;
            const content = this.dataset.content;
            const isUnread = this.dataset.unread === '1';
            const readUrl = this.dataset.readUrl;

            document.getElementById('modalNotifTitle').textContent = title;
            document.getElementById('modalNotifTime').textContent = time;
            document.getElementById('modalNotifType').textContent = type;
            document.getElementById('modalNotifBody').textContent = content;

            $('#notifQuickModal').modal('show');

            if (isUnread && readUrl) {
                this.dataset.unread = '0';
                markAsRead(id, readUrl);
            }
        });
    });

    // 4. Mark All Read with Confirmation
    const formMarkAll = document.getElementById('formMarkAllRead');
    if (formMarkAll) {
        formMarkAll.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.StudentModal && typeof window.StudentModal.confirm === 'function') {
                StudentModal.confirm({
                    type: 'info',
                    title: 'Mark All as Read?',
                    message: 'Do you want to mark all unread notifications in this section as read?',
                    confirmText: 'Yes, Mark Read',
                    cancelText: 'Cancel'
                }).then(function (result) {
                    if (result && (result.isConfirmed || result === true)) {
                        formMarkAll.submit();
                    }
                });
            } else if (confirm('Mark all unread notifications as read?')) {
                formMarkAll.submit();
            }
        });
    }

    // 5. Clear All with Confirmation
    const formClear = document.getElementById('formClearAll');
    if (formClear) {
        formClear.addEventListener('submit', function (e) {
            e.preventDefault();
            if (window.StudentModal && typeof window.StudentModal.confirm === 'function') {
                StudentModal.confirm({
                    type: 'warning',
                    title: 'Clear Notifications?',
                    message: 'Are you sure you want to clear notifications in this section? This action cannot be undone.',
                    confirmText: 'Yes, Clear All',
                    cancelText: 'Cancel',
                    danger: true
                }).then(function (result) {
                    if (result && (result.isConfirmed || result === true)) {
                        formClear.submit();
                    }
                });
            } else if (confirm('Are you sure you want to clear all notifications in this section?')) {
                formClear.submit();
            }
        });
    }
});
</script>
@endsection

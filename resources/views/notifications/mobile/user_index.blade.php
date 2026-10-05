@php
    $currentSessionName = Session::get('session_name') ?? 'Current Session';
    $roleId = (int) Session::get('role_id');
    $imageShowPath = env('IMAGE_SHOW_PATH');
    $totalCount = $notifications->total();
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE USER NOTIFICATIONS STYLES
   - High-performance, lightweight and ultra-clean app layout
   - Signature Arise ERP Dark Navy Hero Header (#001833 -> #002C54)
   - Filter segmented tabs with real-time counters
   - Touch-friendly feed cards for Notices, Complaints, Approval Requests
   - Instant AJAX mark-as-read, expandable content & quick actions
   ========================================================================== */

/* 1. Glassmorphic Hero Banner */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 11px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
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
.mob-hero-badges {
    display: flex;
    align-items: center;
    gap: 5px;
}
.mob-unread-pill {
    font-size: 9.5px;
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #fca5a5;
    padding: 2px 7px;
    border-radius: 3px;
    font-weight: 800;
}
.mob-unread-pill.all-read {
    background: rgba(74, 222, 128, 0.18);
    border-color: rgba(74, 222, 128, 0.35);
    color: #4ade80;
}
.mob-hero-desc {
    font-size: 10.5px;
    color: #cbd5e1;
    margin-bottom: 9px;
}

/* Fast Action Bar */
.mob-actions-bar {
    display: flex;
    gap: 6px;
}
.mob-act-btn {
    flex: 1;
    height: 30px;
    padding: 0 10px;
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
}
.mob-act-btn-readall {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25);
}
.mob-act-btn-readall:active {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(0.97);
}
.mob-act-btn-clearall {
    background: rgba(239, 68, 68, 0.2);
    color: #fca5a5 !important;
    border: 1px solid rgba(239, 68, 68, 0.35);
}
.mob-act-btn-clearall:active {
    background: rgba(239, 68, 68, 0.35);
    transform: scale(0.97);
}
.mob-act-btn[disabled] {
    opacity: 0.45;
    pointer-events: none;
}

/* 2. Filter Segmented Bar */
.mob-filter-segment {
    display: flex;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 3px;
    gap: 4px;
    margin-bottom: 9px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
.mob-segment-tab {
    flex: 1;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    border-radius: 4px;
    text-decoration: none !important;
    transition: all .12s ease;
    user-select: none;
}
.mob-segment-tab.active {
    background: #002C54;
    color: #ffffff;
    box-shadow: 0 2px 5px rgba(0, 44, 84, 0.2);
}
.mob-tab-badge {
    font-size: 9px;
    padding: 1px 5px;
    border-radius: 8px;
    font-weight: 800;
    background: #f1f5f9;
    color: #475569;
}
.mob-segment-tab.active .mob-tab-badge {
    background: #ef4444;
    color: #ffffff;
}

/* 3. Notifications Feed List */
.mob-notif-feed {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.mob-notif-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 10px 11px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    position: relative;
    transition: background-color .15s ease, border-color .15s ease;
}
.mob-notif-card.is-unread {
    background: #f8fafc;
    border-left: 3px solid #0284c7;
}

/* Card Header */
.mob-notif-header {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 6px;
}
.mob-notif-icon {
    width: 32px;
    height: 32px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.mob-notif-icon.type-notice {
    background: #fef3c7;
    color: #d97706;
}
.mob-notif-icon.type-complaint {
    background: #fee2e2;
    color: #dc2626;
}
.mob-notif-icon.type-approval {
    background: #e0e7ff;
    color: #4f46e5;
}
.mob-notif-icon.type-default {
    background: #e0f2fe;
    color: #0284c7;
}
.mob-notif-meta {
    flex: 1;
    min-width: 0;
}
.mob-notif-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 2px;
}
.mob-notif-title {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mob-notif-status-badge {
    font-size: 8.5px;
    font-weight: 800;
    padding: 1px 5px;
    border-radius: 2px;
    white-space: nowrap;
    text-transform: uppercase;
}
.mob-notif-status-badge.unread {
    background: #e0f2fe;
    color: #0284c7;
}
.mob-notif-status-badge.read {
    background: #f1f5f9;
    color: #64748b;
}

.mob-notif-submeta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 9.5px;
    color: #64748b;
}
.mob-type-tag {
    font-size: 8.5px;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 2px;
}
.mob-type-tag.notice { background: #fef3c7; color: #b45309; }
.mob-type-tag.complaint { background: #fee2e2; color: #b91c1c; }
.mob-type-tag.approval { background: #e0e7ff; color: #4338ca; }
.mob-type-tag.default { background: #f1f5f9; color: #475569; }

/* Message Content */
.mob-notif-content {
    font-size: 11px;
    line-height: 1.5;
    color: #334155;
    white-space: pre-line;
    word-break: break-word;
    margin-top: 4px;
}
.mob-notif-content.is-collapsed {
    max-height: 3.1em;
    overflow: hidden;
    position: relative;
    -webkit-line-clamp: 2;
    display: -webkit-box;
    -webkit-box-orient: vertical;
}

/* Complaint Embedded Student Card */
.mob-complaint-box {
    margin-top: 8px;
    padding: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.mob-complaint-student {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mob-student-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    overflow: hidden;
    flex-shrink: 0;
}
.mob-student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.mob-student-details {
    flex: 1;
    min-width: 0;
}
.mob-student-name {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.mob-student-sub {
    font-size: 9.5px;
    color: #64748b;
    margin-top: 1px;
}
.mob-complaint-ticket {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 5px;
    border-top: 1px solid #e2e8f0;
    font-size: 10px;
}
.mob-ticket-badge {
    font-weight: 800;
    color: #0284c7;
}
.mob-status-pill {
    padding: 1px 5px;
    border-radius: 2px;
    font-size: 9px;
    font-weight: 700;
    background: #f1f5f9;
    color: #334155;
}

/* Action Strip */
.mob-notif-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px solid #f1f5f9;
}
.mob-btn-text {
    background: none;
    border: none;
    color: #0284c7;
    font-size: 10.5px;
    font-weight: 700;
    padding: 0;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.mob-btn-text:active {
    color: #0369a1;
}
.mob-pdf-chip {
    font-size: 10px;
    font-weight: 700;
    color: #dc2626;
    background: #fee2e2;
    border: 1px solid #fecaca;
    padding: 2px 7px;
    border-radius: 3px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-action-links {
    display: flex;
    gap: 5px;
    margin-left: auto;
}
.mob-btn-pill {
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 3px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    border: none;
}
.mob-btn-pill-primary {
    background: #0284c7;
    color: #ffffff !important;
}
.mob-btn-pill-outline {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155 !important;
}

/* Inline Approval Form */
.mob-approval-box {
    margin-top: 8px;
    padding: 8px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
}
.mob-approval-summary {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    margin-bottom: 6px;
}
.mob-appr-metric {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 4px 6px;
}
.mob-appr-metric-tag {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.mob-appr-metric-val {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
}
.mob-approval-textarea {
    width: 100%;
    height: 46px;
    padding: 5px 7px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #ffffff;
    margin-top: 4px;
    margin-bottom: 6px;
    outline: none;
    font-family: inherit;
}
.mob-approval-textarea:focus {
    border-color: #0284c7;
}
.mob-approval-btns {
    display: flex;
    gap: 5px;
}
.mob-approval-btn {
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
}
.mob-approval-btn-approve { background: #16a34a; }
.mob-approval-btn-reject { background: #dc2626; }

/* Empty State */
.mob-empty-box {
    text-align: center;
    padding: 48px 16px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    color: #64748b;
}
.mob-empty-box i {
    font-size: 36px;
    color: #cbd5e1;
    margin-bottom: 8px;
}
.mob-empty-box h4 {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 3px;
}
.mob-empty-box p {
    font-size: 11px;
    color: #64748b;
    margin: 0;
}

/* Pagination Wrap */
.mob-pagination-wrap {
    margin-top: 8px;
    margin-bottom: 24px;
    display: flex;
    justify-content: center;
}
.mob-pagination-wrap .pagination {
    margin: 0;
    gap: 4px;
}
.mob-pagination-wrap .page-item .page-link {
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 4px;
    color: #002C54;
    border: 1px solid #cbd5e1;
}
.mob-pagination-wrap .page-item.active .page-link {
    background: #002C54;
    border-color: #002C54;
    color: #ffffff;
}
</style>
@endsection

@section('content')

{{-- 1. Glassmorphic Hero Card --}}
<div class="mob-hero-card">
    <div class="mob-hero-top">
        <div class="mob-hero-title">
            <i class="fa fa-bell text-primary"></i> Notifications Hub
        </div>
        <div class="mob-hero-badges">
            @if($unreadCount > 0)
                <div class="mob-unread-pill" id="heroUnreadPill">
                    <i class="fa fa-circle"></i> {{ $unreadCount }} Unread
                </div>
            @else
                <div class="mob-unread-pill all-read" id="heroUnreadPill">
                    <i class="fa fa-check"></i> All Caught Up
                </div>
            @endif
        </div>
    </div>
    <div class="mob-hero-desc">
        Notices, student support complaints &amp; system updates
    </div>
    <div class="mob-actions-bar">
        <form method="post" action="{{ route('user.notifications.mark-all-read') }}" class="d-inline" style="flex:1;">
            @csrf
            <button type="submit" class="mob-act-btn mob-act-btn-readall w-100" {{ $unreadCount === 0 ? 'disabled' : '' }}>
                <i class="fa fa-check-circle"></i> Mark All Read
            </button>
        </form>
        <form method="post" action="{{ route('user.notifications.clear-all') }}" class="d-inline" style="flex:1;" onsubmit="return confirm('Are you sure you want to clear all notifications?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="mob-act-btn mob-act-btn-clearall w-100" {{ $totalCount === 0 ? 'disabled' : '' }}>
                <i class="fa fa-trash"></i> Clear All
            </button>
        </form>
    </div>
</div>

{{-- Success Flash Alert --}}
@if(session('message'))
    <div class="alert alert-success py-2 px-3 mb-2" style="font-size:11px; border-radius:4px; font-weight:700;">
        <i class="fa fa-check mr-1"></i> {{ session('message') }}
    </div>
@endif

{{-- 2. Filter Segmented Tabs --}}
<div class="mob-filter-segment">
    <a href="{{ url('user-notifications?filter=all') }}" class="mob-segment-tab {{ $filter === 'all' ? 'active' : '' }}">
        <span>All</span>
        <span class="mob-tab-badge">{{ $totalCount }}</span>
    </a>
    <a href="{{ url('user-notifications?filter=unread') }}" class="mob-segment-tab {{ $filter === 'unread' ? 'active' : '' }}">
        <span>Unread</span>
        @if($unreadCount > 0)
            <span class="mob-tab-badge" id="tabUnreadBadge">{{ $unreadCount }}</span>
        @endif
    </a>
    <a href="{{ url('user-notifications?filter=read') }}" class="mob-segment-tab {{ $filter === 'read' ? 'active' : '' }}">
        <span>Read</span>
    </a>
</div>

{{-- 3. Notifications Feed List --}}
<div class="mob-notif-feed">
    @forelse($notifications as $notification)
        @php
            $isUnread = (int) $notification->message_seen === 0;
            $content = trim((string) $notification->content);
            $isLong = mb_strlen($content) > 130 || substr_count($content, "\n") > 2;
            $isNotice = $notification->type === 'notice';
            $isApprovalRequest = $notification->type === 'notice_approval_request';
            $managedNotice = $notification->managedNotice;
            $isComplaint = $notification->type === 'complaint_admin';
            $complaint = $notification->complaintContext;
            $complaintStudent = $complaint ? $complaint->student : null;

            $iconClass = match(true) {
                $isComplaint => 'type-complaint',
                $isNotice => 'type-notice',
                $isApprovalRequest => 'type-approval',
                default => 'type-default'
            };
            $faIcon = match(true) {
                $isComplaint => 'fa-comments-o',
                $isNotice => 'fa-bullhorn',
                $isApprovalRequest => 'fa-shield',
                default => 'fa-bell-o'
            };
        @endphp

        <article class="mob-notif-card {{ $isUnread ? 'is-unread' : '' }}" id="user-notification-{{ $notification->id }}">
            {{-- Header --}}
            <div class="mob-notif-header">
                <div class="mob-notif-icon {{ $iconClass }}">
                    <i class="fa {{ $faIcon }}"></i>
                </div>
                <div class="mob-notif-meta">
                    <div class="mob-notif-title-row">
                        <h3 class="mob-notif-title">{{ $notification->title ?: 'Notification' }}</h3>
                        <span class="mob-notif-status-badge {{ $isUnread ? 'unread' : 'read' }}" id="badge-{{ $notification->id }}">
                            {{ $isUnread ? 'Unread' : 'Read' }}
                        </span>
                    </div>
                    <div class="mob-notif-submeta">
                        @if($isNotice)
                            <span class="mob-type-tag notice">Notice</span>
                        @elseif($isApprovalRequest)
                            <span class="mob-type-tag approval">Approval</span>
                        @elseif($isComplaint)
                            <span class="mob-type-tag complaint">Complaint</span>
                        @endif
                        <span><i class="fa fa-clock-o"></i> {{ optional($notification->created_at)->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Body Content --}}
            <div class="mob-notif-content {{ $isLong ? 'is-collapsed' : '' }}" id="user-message-{{ $notification->id }}">
                {{ $content ?: 'No notification message available.' }}
            </div>

            {{-- Support Complaint Box --}}
            @if($isComplaint && $complaint && $complaintStudent)
                <div class="mob-complaint-box">
                    <div class="mob-complaint-student">
                        <div class="mob-student-avatar">
                            @if(!empty($complaintStudent->image))
                                <img src="{{ $imageShowPath . 'profile/' . rawurlencode($complaintStudent->image) }}" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <i class="fa fa-user" style="{{ !empty($complaintStudent->image) ? 'display:none;' : '' }}"></i>
                        </div>
                        <div class="mob-student-details">
                            <div class="mob-student-name">
                                {{ trim($complaintStudent->first_name . ' ' . $complaintStudent->last_name) ?: 'Student' }}
                            </div>
                            <div class="mob-student-sub">
                                {{ optional($complaintStudent->ClassTypes)->name ?? 'Class N/A' }}
                                @if(!empty($complaintStudent->admissionNo)) · Adm: {{ $complaintStudent->admissionNo }} @endif
                            </div>
                        </div>
                    </div>
                    <div class="mob-complaint-ticket">
                        <span class="mob-ticket-badge"><i class="fa fa-ticket"></i> {{ $complaint->ticket_no }}</span>
                        <span class="mob-status-pill">{{ \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status) }}</span>
                    </div>
                </div>
            @endif

            {{-- Inline Approval Form for Role 1 (Admin) --}}
            @if($isApprovalRequest && $roleId === 1 && $managedNotice)
                @if($managedNotice->status === 'pending')
                    <div class="mob-approval-box">
                        @php
                            $noticeRecipients = $managedNotice->recipients;
                            $studentCount = $noticeRecipients->where('recipient_type', 'student')->count();
                            $staffCount = $noticeRecipients->where('recipient_type', 'user')->count();
                        @endphp
                        <div class="mob-approval-summary">
                            <div class="mob-appr-metric">
                                <div class="mob-appr-metric-tag">Audience</div>
                                <div class="mob-appr-metric-val">{{ ucfirst($managedNotice->audience_type) }}</div>
                            </div>
                            <div class="mob-appr-metric">
                                <div class="mob-appr-metric-tag">Total Targets</div>
                                <div class="mob-appr-metric-val">{{ $noticeRecipients->count() }} ({{ $staffCount }} Staff, {{ $studentCount }} Students)</div>
                            </div>
                        </div>

                        <form action="{{ url('notice-management/'.$managedNotice->id.'/review') }}" method="post" onsubmit="return confirmMobNoticeReview(event, this);">
                            @csrf
                            <input type="hidden" name="return_to" value="user-notifications">
                            <textarea name="review_notes" class="mob-approval-textarea" placeholder="Instructions or reason for decision..." required></textarea>
                            <div class="mob-approval-btns">
                                <button type="submit" name="decision" value="approved" class="mob-approval-btn mob-approval-btn-approve">
                                    <i class="fa fa-check"></i> Approve &amp; Publish
                                </button>
                                <button type="submit" name="decision" value="rejected" class="mob-approval-btn mob-approval-btn-reject">
                                    <i class="fa fa-times"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="alert alert-light py-1 px-2 mt-2 mb-0" style="font-size:10px; border:1px solid #cbd5e1;">
                        <i class="fa {{ $managedNotice->status === 'approved' ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                        Notice was <b>{{ $managedNotice->status }}</b>.
                    </div>
                @endif
            @endif

            {{-- Action Strip --}}
            <div class="mob-notif-actions">
                <div>
                    @if($isLong)
                        <button type="button" class="mob-btn-text mob-toggle-btn" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}">
                            <span>View More</span> <i class="fa fa-angle-down"></i>
                        </button>
                    @elseif($isUnread)
                        <button type="button" class="mob-btn-text mob-mark-read-btn" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}">
                            <i class="fa fa-check"></i> Mark Read
                        </button>
                    @endif

                    @if($isNotice && $notification->managed_notice_id && $notification->attachment_path)
                        <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}" class="mob-pdf-chip">
                            <i class="fa fa-file-pdf-o"></i> PDF Attachment
                        </a>
                    @endif
                </div>

                {{-- Direct Link Actions --}}
                <div class="mob-action-links">
                    @if($isComplaint && $complaint)
                        @if($complaintStudent)
                            <a href="{{ url('studentDetail/'.$complaintStudent->id) }}" class="mob-btn-pill mob-btn-pill-outline">
                                <i class="fa fa-user"></i> Student
                            </a>
                        @endif
                        <a href="{{ url('complaints-management/'.$complaint->id).'?highlight=notification#complaint-ticket' }}" class="mob-btn-pill mob-btn-pill-primary">
                            <i class="fa fa-eye"></i> View Ticket
                        </a>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="mob-empty-box">
            <i class="fa fa-bell-slash-o"></i>
            <h4>No Notifications</h4>
            <p>You have no notifications in this filter.</p>
        </div>
    @endforelse
</div>

{{-- 4. Mobile Pagination --}}
@if($notifications->hasPages())
    <div class="mob-pagination-wrap">
        {{ $notifications->links() }}
    </div>
@endif

@endsection

@section('scripts')
<script>
function confirmMobNoticeReview(event, form) {
    var submitter = event.submitter || document.activeElement;
    var decisionValue = submitter && submitter.value === 'rejected' ? 'rejected' : 'approved';
    var decisionLabel = decisionValue === 'rejected' ? 'reject' : 'approve and publish';
    if (!window.confirm('Are you sure you want to ' + decisionLabel + ' this notice?')) return false;
    var hiddenDecision = document.createElement('input');
    hiddenDecision.type = 'hidden';
    hiddenDecision.name = 'decision';
    hiddenDecision.value = decisionValue;
    form.appendChild(hiddenDecision);
    $(form).find('button[type="submit"]').prop('disabled', true);
    return true;
}

$(document).ready(function() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // AJAX Fast Mark-As-Read Function
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
                var $badge = $('#badge-' + id);
                if ($badge.length) {
                    $badge.text('Read').removeClass('unread').addClass('read');
                }
                if ($button && $button.hasClass('mob-mark-read-btn')) {
                    $button.fadeOut(200, function() { $(this).remove(); });
                }

                // Decrement hero counter if applicable
                var $heroPill = $('#heroUnreadPill');
                var $tabBadge = $('#tabUnreadBadge');
                if ($tabBadge.length) {
                    var cur = parseInt($tabBadge.text()) || 0;
                    if (cur > 1) {
                        $tabBadge.text(cur - 1);
                        $heroPill.html('<i class="fa fa-circle"></i> ' + (cur - 1) + ' Unread');
                    } else {
                        $tabBadge.remove();
                        $heroPill.removeClass('mob-unread-pill').addClass('mob-unread-pill all-read').html('<i class="fa fa-check"></i> All Caught Up');
                    }
                }
            }
        });
    }

    // Toggle Read More / Read Less
    $('.mob-toggle-btn').on('click', function() {
        var id = $(this).data('id');
        var readUrl = $(this).data('read-url');
        var $content = $('#user-message-' + id);
        var isCollapsed = $content.hasClass('is-collapsed');

        if (isCollapsed) {
            $content.removeClass('is-collapsed');
            $(this).html('<span>View Less</span> <i class="fa fa-angle-up"></i>');
            markAsRead(id, readUrl, $(this));
        } else {
            $content.addClass('is-collapsed');
            $(this).html('<span>View More</span> <i class="fa fa-angle-down"></i>');
        }
    });

    // Mark as read button click
    $('.mob-mark-read-btn').on('click', function() {
        var id = $(this).data('id');
        var readUrl = $(this).data('read-url');
        markAsRead(id, readUrl, $(this));
    });
});
</script>
@endsection

@extends('layout.app')

@php
    $counts = [
        'all' => $totalCount ?? 0,
        'unread' => $unreadCount ?? 0,
        'read' => $readCount ?? 0,
        'notice' => $noticeCount ?? 0,
        'approval' => $approvalCount ?? 0,
        'complaint' => $complaintCount ?? 0,
        'system' => $systemCount ?? 0,
    ];
    $currentFilter = $filter ?? 'all';
    $currentType = $type ?? 'all';
    $searchQuery = $search ?? '';
@endphp

@section('content')
<div class="content-wrapper user-notification-page">
    <div class="notify-page-layout">

        {{-- 1. Top Hero Header (Arise Signature Dark Navy Theme) --}}
        <header class="notify-hero">
            <div class="notify-hero-text">
                <span class="notify-kicker"><i class="fa fa-bell-o"></i> SYSTEM COMMUNICATIONS & ALERTS</span>
                <h1 class="notify-hero-title">
                    <i class="fa fa-bell"></i> User Notifications
                </h1>
                <p class="notify-hero-sub">
                    Real-time announcements, student notices, approval requests, and support tickets for your account.
                </p>
            </div>

            {{-- Hero Quick Metric Badges / KPI Pills --}}
            <div class="notify-hero-stats">
                <a href="{{ url('user-notifications?filter=all&type=all') }}" 
                   class="hero-stat-badge {{ $currentFilter === 'all' && $currentType === 'all' ? 'active-filter' : '' }}" 
                   title="All Notifications">
                    <span>All</span>
                    <b id="kpiTotal">{{ $totalCount }}</b>
                </a>

                <a href="{{ url('user-notifications?filter=unread&type=all') }}" 
                   class="hero-stat-badge badge-amber {{ $currentFilter === 'unread' ? 'active-filter' : '' }}" 
                   title="Unread Notifications">
                    @if($unreadCount > 0)
                        <span class="pulse-dot"></span>
                    @endif
                    <span>Unread</span>
                    <b id="kpiUnread">{{ $unreadCount }}</b>
                </a>

                <a href="{{ url('user-notifications?filter=read&type=all') }}" 
                   class="hero-stat-badge badge-green {{ $currentFilter === 'read' ? 'active-filter' : '' }}" 
                   title="Read Notifications">
                    <span>Read</span>
                    <b id="kpiRead">{{ $readCount }}</b>
                </a>

                @if($noticeCount + $approvalCount > 0)
                    <a href="{{ url('user-notifications?type=notice') }}" 
                       class="hero-stat-badge badge-sky {{ $currentType === 'notice' || $currentType === 'approval' ? 'active-filter' : '' }}" 
                       title="Notices & Announcements">
                        <i class="fa fa-bullhorn"></i>
                        <span>Notices</span>
                        <b>{{ $noticeCount + $approvalCount }}</b>
                    </a>
                @endif

                @if($complaintCount > 0)
                    <a href="{{ url('user-notifications?type=complaint') }}" 
                       class="hero-stat-badge badge-rose {{ $currentType === 'complaint' ? 'active-filter' : '' }}" 
                       title="Support Complaints">
                        <i class="fa fa-comments"></i>
                        <span>Complaints</span>
                        <b>{{ $complaintCount }}</b>
                    </a>
                @endif
            </div>

            {{-- Hero Action Buttons --}}
            <div class="notify-hero-actions">
                <form method="POST" action="{{ route('user.notifications.mark-all-read') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="dash-btn dash-btn-light" {{ $unreadCount === 0 ? 'disabled' : '' }} title="Mark all notifications as read">
                        <i class="fa fa-check-circle"></i> Mark All as Read
                    </button>
                </form>

                <form method="POST" action="{{ route('user.notifications.clear-all') }}" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all notifications? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dash-btn dash-btn-danger" {{ $notifications->total() === 0 ? 'disabled' : '' }} title="Clear and remove all notifications">
                        <i class="fa fa-trash"></i> Clear All
                    </button>
                </form>

                <a href="{{ url('user-notifications') }}" class="dash-btn dash-btn-outline" title="Refresh notification list">
                    <i class="fa fa-refresh"></i> Refresh
                </a>
            </div>
        </header>

        {{-- 2. Flash Messages --}}
        @if(session('message'))
            <div class="alert alert-erp-success mx-2 mt-1 mb-1">
                <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- 3. Control & Quick Filter Strip --}}
        <div class="notify-control-strip">
            <div class="filter-group-left">
                {{-- Status Quick Filter Tabs --}}
                <div class="quick-status-tabs">
                    <a href="{{ url('user-notifications?filter=all'.($currentType !== 'all' ? '&type='.$currentType : '').($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="status-tab-btn {{ $currentFilter === 'all' ? 'active' : '' }}">
                        All <span class="tab-badge">{{ $totalCount }}</span>
                    </a>
                    <a href="{{ url('user-notifications?filter=unread'.($currentType !== 'all' ? '&type='.$currentType : '').($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="status-tab-btn tab-unread {{ $currentFilter === 'unread' ? 'active' : '' }}">
                        Unread @if($unreadCount > 0)<span class="tab-badge tab-badge-danger">{{ $unreadCount }}</span>@endif
                    </a>
                    <a href="{{ url('user-notifications?filter=read'.($currentType !== 'all' ? '&type='.$currentType : '').($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="status-tab-btn {{ $currentFilter === 'read' ? 'active' : '' }}">
                        Read <span class="tab-badge">{{ $readCount }}</span>
                    </a>
                </div>

                {{-- Type Dropdown / Chips --}}
                <div class="type-filter-chips">
                    <span class="type-filter-label"><i class="fa fa-filter"></i> Type:</span>
                    <a href="{{ url('user-notifications?filter='.$currentFilter.'&type=all'.($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="type-chip {{ $currentType === 'all' ? 'active' : '' }}">All</a>
                    <a href="{{ url('user-notifications?filter='.$currentFilter.'&type=notice'.($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="type-chip {{ $currentType === 'notice' ? 'active' : '' }}">Notices</a>
                    @if($approvalCount > 0)
                        <a href="{{ url('user-notifications?filter='.$currentFilter.'&type=approval'.($searchQuery ? '&q='.$searchQuery : '')) }}" 
                           class="type-chip {{ $currentType === 'approval' ? 'active' : '' }}">Approvals ({{ $approvalCount }})</a>
                    @endif
                    <a href="{{ url('user-notifications?filter='.$currentFilter.'&type=complaint'.($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="type-chip {{ $currentType === 'complaint' ? 'active' : '' }}">Complaints</a>
                    <a href="{{ url('user-notifications?filter='.$currentFilter.'&type=system'.($searchQuery ? '&q='.$searchQuery : '')) }}" 
                       class="type-chip {{ $currentType === 'system' ? 'active' : '' }}">System</a>
                </div>
            </div>

            {{-- Live Search Input --}}
            <form action="{{ url('user-notifications') }}" method="GET" class="notify-search-form" id="notifySearchForm">
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
                <input type="hidden" name="type" value="{{ $currentType }}">
                <div class="search-input-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" name="q" class="search-ctrl" value="{{ $searchQuery }}" 
                           placeholder="Search title, message, student, ticket..." autocomplete="off">
                    @if(!empty($searchQuery))
                        <a href="{{ url('user-notifications?filter='.$currentFilter.'&type='.$currentType) }}" class="search-clear-btn" title="Clear search">&times;</a>
                    @endif
                </div>
                <button type="submit" class="search-submit-btn">Filter</button>
            </form>

            @if($currentFilter !== 'all' || $currentType !== 'all' || !empty($searchQuery))
                <a href="{{ url('user-notifications') }}" class="btn-reset-filters" title="Reset all active filters">
                    <i class="fa fa-times-circle"></i> Reset Filters
                </a>
            @endif
        </div>

        {{-- 4. Main Scrollable Notification Feed --}}
        <main class="notify-feed-container" id="notifyFeedContainer">
            @forelse($notifications as $notification)
                @php
                    $isUnread = (int) $notification->message_seen === 0;
                    $content = trim((string) $notification->content);
                    $isLong = mb_strlen($content) > 160 || substr_count($content, "\n") > 2;

                    $nType = (string) $notification->type;
                    $isNotice = in_array($nType, ['notice', 'notice_approved', 'notice_rejected'], true);
                    $isApprovalRequest = ($nType === 'notice_approval_request');
                    $isComplaint = in_array($nType, ['complaint_admin', 'complaint'], true);

                    $managedNotice = $notification->managedNotice;
                    $complaint = $notification->complaintContext;
                    $complaintStudent = $complaint ? $complaint->student : null;

                    // Type color & icon config
                    $typeClass = 'type-default';
                    $typeIcon = 'fa-bell-o';
                    $typeBadgeText = 'System Alert';
                    $typeBadgeClass = 'badge-type-default';

                    if ($isNotice) {
                        $typeClass = 'type-notice';
                        $typeIcon = 'fa-bullhorn';
                        $typeBadgeText = 'Notice';
                        $typeBadgeClass = 'badge-type-notice';
                    } elseif ($isApprovalRequest) {
                        $typeClass = 'type-approval';
                        $typeIcon = 'fa-check-square-o';
                        $typeBadgeText = 'Approval Request';
                        $typeBadgeClass = 'badge-type-approval';
                    } elseif ($isComplaint) {
                        $typeClass = 'type-complaint';
                        $typeIcon = 'fa-comments';
                        $typeBadgeText = 'Student Complaint';
                        $typeBadgeClass = 'badge-type-complaint';
                    }
                @endphp

                <article class="notify-card {{ $isUnread ? 'is-unread' : 'is-read' }}" id="notify-card-{{ $notification->id }}" data-id="{{ $notification->id }}">
                    {{-- Type Icon --}}
                    <div class="notify-icon-box {{ $typeClass }}">
                        <i class="fa {{ $typeIcon }}"></i>
                    </div>

                    {{-- Main Body --}}
                    <div class="notify-body">
                        {{-- Top Header Row: Title, Badges, Time, Actions --}}
                        <div class="notify-card-header">
                            <div class="notify-title-group">
                                <h3 class="notify-card-title">
                                    {{ $notification->title ?: 'Notification Alert' }}
                                </h3>
                                <span class="badge-type {{ $typeBadgeClass }}">{{ $typeBadgeText }}</span>

                                @if($isUnread)
                                    <span class="notify-status-badge badge-unread" id="status-badge-{{ $notification->id }}">
                                        <span class="status-pulse-dot"></span> New
                                    </span>
                                @else
                                    <span class="notify-status-badge badge-read" id="status-badge-{{ $notification->id }}">Read</span>
                                @endif
                            </div>

                            <div class="notify-meta-group">
                                <span class="notify-timestamp" title="{{ optional($notification->created_at)->format('d M Y, h:i A') }}">
                                    <i class="fa fa-clock-o"></i>
                                    {{ $notification->created_at ? $notification->created_at->diffForHumans() : '-' }}
                                    <span class="exact-time">({{ optional($notification->created_at)->format('d M Y, h:i A') }})</span>
                                </span>

                                <div class="notify-quick-actions">
                                    @if($isUnread)
                                        <button type="button" class="btn-card-action btn-mark-read" 
                                                data-id="{{ $notification->id }}" 
                                                data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}" 
                                                title="Mark as Read">
                                            <i class="fa fa-check"></i> <span>Mark Read</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn-card-action btn-mark-unread" 
                                                data-id="{{ $notification->id }}" 
                                                data-unread-url="{{ route('user.notifications.mark-unread', $notification->id) }}" 
                                                title="Mark as Unread">
                                            <i class="fa fa-envelope-o"></i> <span>Mark Unread</span>
                                        </button>
                                    @endif

                                    <button type="button" class="btn-card-action btn-delete-item text-danger" 
                                            data-id="{{ $notification->id }}" 
                                            data-delete-url="{{ route('user.notifications.destroy', $notification->id) }}" 
                                            title="Delete notification">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Message Content Text --}}
                        @if(!empty($content))
                            <div class="notify-message {{ $isLong ? 'is-collapsed' : '' }}" id="notify-msg-{{ $notification->id }}">
                                {!! nl2br(e($content)) !!}
                            </div>

                            @if($isLong)
                                <button type="button" class="btn-msg-toggle" 
                                        data-id="{{ $notification->id }}" 
                                        data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}" 
                                        aria-expanded="false">
                                    View more <i class="fa fa-angle-down"></i>
                                </button>
                            @endif
                        @endif

                        {{-- Embedded Rich Context 1: Notice Attachment File --}}
                        @if($isNotice && $notification->managed_notice_id && $notification->attachment_path)
                            <div class="notify-attachment-strip">
                                <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}" 
                                   target="_blank" class="attachment-chip" title="Download Notice PDF">
                                    <i class="fa fa-file-pdf-o text-danger"></i>
                                    <span class="attachment-name">{{ $notification->attachment_name ?: 'Notice Attachment.pdf' }}</span>
                                    <span class="attachment-action"><i class="fa fa-download"></i> View / Download</span>
                                </a>
                            </div>
                        @endif

                        {{-- Embedded Rich Context 2: Student Support Complaint Profile & Ticket --}}
                        @if($isComplaint && $complaint)
                            <div class="complaint-embedded-box">
                                <div class="complaint-embedded-left">
                                    <div class="complaint-student-avatar">
                                        @if($complaintStudent && $complaintStudent->image)
                                            <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$complaintStudent->image }}" 
                                                 alt="{{ trim(($complaintStudent->first_name ?? '').' '.($complaintStudent->last_name ?? '')) }}" 
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        @endif
                                        <i class="fa fa-user-circle" style="{{ ($complaintStudent && $complaintStudent->image) ? 'display:none' : '' }}"></i>
                                    </div>
                                    <div class="complaint-student-meta">
                                        <div class="student-name-row">
                                            <span class="student-full-name">
                                                {{ trim(($complaintStudent->first_name ?? '').' '.($complaintStudent->last_name ?? '')) ?: ($complaint->student_name ?? 'Student') }}
                                            </span>
                                            <span class="badge-class">
                                                <i class="fa fa-graduation-cap"></i> {{ optional($complaintStudent->ClassTypes)->name ?? 'Class N/A' }}
                                                @if(optional($complaintStudent)->Section) ({{ $complaintStudent->Section->name }}) @endif
                                            </span>
                                        </div>
                                        <div class="student-sub-info">
                                            @if(!empty($complaintStudent->admissionNo))
                                                <span><b>Adm No:</b> {{ $complaintStudent->admissionNo }}</span>
                                                <span class="meta-sep">&bull;</span>
                                            @endif
                                            @if(!empty($complaintStudent->father_name))
                                                <span><b>Father:</b> {{ $complaintStudent->father_name }}</span>
                                                <span class="meta-sep">&bull;</span>
                                            @endif
                                            @php
                                                $parentPhone = $complaintStudent->father_mobile ?? ($complaintStudent->mobile ?? null);
                                            @endphp
                                            @if($parentPhone)
                                                <span><i class="fa fa-phone text-muted"></i> {{ $parentPhone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="complaint-embedded-right">
                                    <div class="ticket-badge-wrap">
                                        <span class="ticket-number-badge">
                                            <i class="fa fa-ticket"></i> {{ $complaint->ticket_no }}
                                        </span>
                                        @php
                                            $cStatus = strtolower($complaint->status ?? 'open');
                                            $cStatusBadge = 'badge-secondary';
                                            if ($cStatus === 'open') $cStatusBadge = 'badge-danger';
                                            elseif ($cStatus === 'in_progress') $cStatusBadge = 'badge-warning';
                                            elseif ($cStatus === 'resolved' || $cStatus === 'closed') $cStatusBadge = 'badge-success';
                                        @endphp
                                        <span class="badge {{ $cStatusBadge }} complaint-status-pill">
                                            {{ \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status) }}
                                        </span>
                                    </div>
                                    <div class="ticket-action-btns">
                                        @if($complaintStudent)
                                            <a href="{{ url('studentDetail/'.$complaintStudent->id) }}" 
                                               class="btn-sub-action" target="_blank" title="Open student profile">
                                                <i class="fa fa-user"></i> Profile
                                            </a>
                                        @endif
                                        <a href="{{ url('complaints-management/'.$complaint->id.'?highlight=notification#complaint-ticket') }}" 
                                           class="btn-main-action" target="_blank" title="Open complaint ticket details">
                                            <i class="fa fa-eye"></i> View Ticket
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Embedded Rich Context 3: Notice Approval Request (Admin Action Drawer) --}}
                        @if($isApprovalRequest && (int) Session::get('role_id') === 1 && $managedNotice)
                            <div class="approval-review-container">
                                @if($managedNotice->status === 'pending')
                                    <form action="{{ url('notice-management/'.$managedNotice->id.'/review') }}" method="POST" 
                                          class="inline-review-card" onsubmit="return confirmNoticeReview(event, this);">
                                        @csrf
                                        <input type="hidden" name="return_to" value="user-notifications">

                                        @php
                                            $noticeRecipients = $managedNotice->recipients ?? collect();
                                            $audienceNames = $managedNotice->audience_type === 'role'
                                                ? $noticeRecipients->pluck('role_name')->filter()->unique()->values()
                                                : ($managedNotice->audience_type === 'class'
                                                    ? $noticeRecipients->pluck('class_name')->filter()->unique()->values()
                                                    : collect());
                                            $studentCount = $noticeRecipients->where('recipient_type', 'student')->count();
                                            $staffCount = $noticeRecipients->where('recipient_type', 'user')->count();
                                        @endphp

                                        <div class="approval-header-strip">
                                            <span class="approval-kicker"><i class="fa fa-shield"></i> ADMINISTRATOR REVIEW REQUIRED</span>
                                            <span class="approval-title">Notice Approval Request #{{ $managedNotice->id }}</span>
                                        </div>

                                        <div class="audience-metrics-grid">
                                            <div class="metric-cell">
                                                <span class="metric-label">Audience Scope</span>
                                                <span class="metric-value">{{ ucfirst($managedNotice->audience_type) }}</span>
                                            </div>
                                            <div class="metric-cell">
                                                <span class="metric-label">Total Recipients</span>
                                                <span class="metric-value font-weight-bold">{{ $noticeRecipients->count() }}</span>
                                            </div>
                                            <div class="metric-cell">
                                                <span class="metric-label">Staff Members</span>
                                                <span class="metric-value">{{ $staffCount }}</span>
                                            </div>
                                            <div class="metric-cell">
                                                <span class="metric-label">Students</span>
                                                <span class="metric-value">{{ $studentCount }}</span>
                                            </div>
                                        </div>

                                        @if($audienceNames->isNotEmpty())
                                            <div class="audience-tag-row">
                                                <b>{{ $managedNotice->audience_type === 'role' ? 'Selected Roles:' : 'Selected Classes:' }}</b>
                                                @foreach($audienceNames as $aName)
                                                    <span class="audience-pill">{{ $aName }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="recipient-toggle-wrap">
                                            <button type="button" class="btn btn-link btn-sm p-0 recipient-detail-toggle" 
                                                    data-target="recipients-drawer-{{ $notification->id }}" aria-expanded="false">
                                                <i class="fa fa-users"></i> View all {{ $noticeRecipients->count() }} recipient details <i class="fa fa-angle-down"></i>
                                            </button>

                                            <div class="recipients-list-drawer d-none" id="recipients-drawer-{{ $notification->id }}">
                                                @forelse($noticeRecipients as $rItem)
                                                    <div class="recipient-row-item">
                                                        <span class="recipient-avatar">{{ strtoupper(substr($rItem->recipient_name ?? 'U', 0, 1)) }}</span>
                                                        <div class="recipient-info">
                                                            <strong class="recipient-name">{{ $rItem->recipient_name }}</strong>
                                                            <small class="recipient-sub">
                                                                {{ $rItem->recipient_type === 'student' ? 'Student' : 'Staff' }}
                                                                @if($rItem->role_name) &bull; {{ $rItem->role_name }} @endif
                                                                @if($rItem->class_name) &bull; {{ $rItem->class_name }} @endif
                                                                @if($rItem->mobile) &bull; <i class="fa fa-phone"></i> {{ $rItem->mobile }} @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="text-muted small p-2">No resolved recipients found.</div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="review-comment-wrap">
                                            <label class="review-label" for="review_notes_{{ $notification->id }}">
                                                Approval Notes / Instructions <span class="text-danger">*</span>
                                            </label>
                                            <textarea id="review_notes_{{ $notification->id }}" name="review_notes" 
                                                      class="form-control form-control-sm review-textarea" 
                                                      rows="2" maxlength="2000" 
                                                      placeholder="Write approval instructions or rejection reason here..." required></textarea>
                                        </div>

                                        <div class="review-btn-toolbar">
                                            <button type="submit" name="decision" value="approved" class="btn btn-sm btn-erp-success">
                                                <i class="fa fa-check"></i> Approve & Publish
                                            </button>
                                            <button type="submit" name="decision" value="rejected" class="btn btn-sm btn-erp-danger">
                                                <i class="fa fa-times"></i> Reject Notice
                                            </button>
                                            <a href="{{ url('notice-management?status=pending') }}" class="btn btn-sm btn-erp-outline" target="_blank">
                                                <i class="fa fa-external-link"></i> Full Details
                                            </a>
                                        </div>
                                    </form>
                                @else
                                    <div class="review-status-pill status-{{ $managedNotice->status }}">
                                        <i class="fa {{ $managedNotice->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        <span>This notice has been <b>{{ strtoupper($managedNotice->status) }}</b>.</span>
                                        @if($managedNotice->review_notes)
                                            <span class="review-note-text">"{{ $managedNotice->review_notes }}"</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                {{-- Modern Empty State --}}
                <div class="notify-empty-state">
                    <div class="empty-icon-circle">
                        <i class="fa fa-bell-slash-o"></i>
                    </div>
                    <h3 class="empty-title">No Notifications Found</h3>
                    <p class="empty-desc">
                        @if(!empty($searchQuery))
                            No notifications match your search query "<b>{{ $searchQuery }}</b>". Try clearing search or resetting filters.
                        @elseif($currentFilter === 'unread')
                            Great job! You have read all notifications. There are no unread items waiting for your attention.
                        @else
                            You have no notifications in this category. New announcements and alerts will appear here automatically.
                        @endif
                    </p>
                    <div class="empty-actions">
                        @if($currentFilter !== 'all' || $currentType !== 'all' || !empty($searchQuery))
                            <a href="{{ url('user-notifications') }}" class="dash-btn dash-btn-primary">
                                <i class="fa fa-refresh"></i> Reset All Filters
                            </a>
                        @else
                            <a href="{{ url('user-notifications') }}" class="dash-btn dash-btn-outline" style="color: #002C54; border-color: #cbd5e1;">
                                <i class="fa fa-refresh"></i> Refresh
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </main>

        {{-- 5. Docked Bottom Pagination & Summary Toolbar (Arise ERP #002C54 Standard) --}}
        <footer class="notify-bottom-bar">
            <div class="bottom-bar-left">
                <i class="fa fa-list-ul mr-1"></i>
                @if($notifications->total() > 0)
                    Showing <b>{{ $notifications->firstItem() ?? 1 }}</b> to <b>{{ $notifications->lastItem() ?? $notifications->count() }}</b> of <b>{{ $notifications->total() }}</b> records
                @else
                    Showing <b>0</b> records
                @endif
                @if($unreadCount > 0)
                    <span class="bottom-bar-unread-pill">&bull; {{ $unreadCount }} Unread</span>
                @endif
            </div>

            <div class="bottom-bar-right">
                @if($notifications->hasPages())
                    <div class="dash-pagination-wrap">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </footer>

    </div>
</div>

{{-- Unified Styling Matching Arise ERP Desktop Standards --}}
@section('styles')
<style>
/* Base Viewport Fitting - Arise ERP Standard */
.user-notification-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.user-notification-page * {
    box-sizing: border-box;
}
.notify-page-layout {
    height: calc(100vh - var(--header-height, 56px) - 16px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 1. Top Hero Header (Arise ERP Dark Navy #002C54) */
.notify-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 3px;
    flex-shrink: 0;
}
.notify-hero-text {
    display: flex;
    flex-direction: column;
}
.notify-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.notify-hero-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.notify-hero-sub {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Stats & KPI Badges */
.notify-hero-stats {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}
.hero-stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 2px;
    font-size: 11px;
    color: #ffffff;
    cursor: pointer;
    transition: all .15s ease-in-out;
    text-decoration: none !important;
}
.hero-stat-badge:hover {
    background: rgba(255,255,255,.25);
    color: #ffffff;
}
.hero-stat-badge.active-filter {
    background: #0284c7 !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 1px #38bdf8;
    color: #ffffff !important;
}
.hero-stat-badge b {
    font-weight: 700;
    font-size: 11.5px;
}
.hero-stat-badge.badge-amber {
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.4);
    color: #fde68a;
}
.hero-stat-badge.badge-amber:hover, .hero-stat-badge.badge-amber.active-filter {
    background: #d97706 !important;
    color: #ffffff !important;
}
.hero-stat-badge.badge-green {
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.4);
    color: #a7f3d0;
}
.hero-stat-badge.badge-green:hover, .hero-stat-badge.badge-green.active-filter {
    background: #059669 !important;
    color: #ffffff !important;
}
.hero-stat-badge.badge-sky {
    background: rgba(14, 165, 233, 0.2);
    border-color: rgba(14, 165, 233, 0.4);
    color: #bae6fd;
}
.hero-stat-badge.badge-sky:hover, .hero-stat-badge.badge-sky.active-filter {
    background: #0284c7 !important;
    color: #ffffff !important;
}
.hero-stat-badge.badge-rose {
    background: rgba(244, 63, 94, 0.2);
    border-color: rgba(244, 63, 94, 0.4);
    color: #fecdd3;
}
.hero-stat-badge.badge-rose:hover, .hero-stat-badge.badge-rose.active-filter {
    background: #e11d48 !important;
    color: #ffffff !important;
}
.pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ef4444;
    display: inline-block;
    box-shadow: 0 0 0 rgba(239,68,68, 0.7);
    animation: pulseDot 1.8s infinite;
}
@keyframes pulseDot {
    0% { box-shadow: 0 0 0 0 rgba(239,68,68, 0.7); }
    70% { box-shadow: 0 0 0 5px rgba(239,68,68, 0); }
    100% { box-shadow: 0 0 0 0 rgba(239,68,68, 0); }
}

/* Hero Action Buttons */
.notify-hero-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
}
.dash-btn-light:hover:not(:disabled) {
    background: #e2e8f0 !important;
    color: #001f3d !important;
    border-color: #cbd5e1 !important;
}
.dash-btn-danger {
    background: #dc2626;
    color: #ffffff;
    border-color: #b91c1c;
}
.dash-btn-danger:hover:not(:disabled) {
    background: #b91c1c !important;
    color: #ffffff !important;
}
.dash-btn-outline {
    background: rgba(255,255,255,.08);
    color: #ffffff;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.22) !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}
.dash-btn-primary {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.dash-btn-primary:hover {
    background: #0369a1 !important;
    color: #ffffff !important;
}
.dash-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
}

/* Alert */
.alert-erp-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    border-radius: 3px;
    padding: 6px 12px;
    font-size: 11.5px;
    font-weight: 600;
}

/* 2. Control & Quick Filter Strip */
.notify-control-strip {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 4px;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.filter-group-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.quick-status-tabs {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    border-radius: 3px;
    padding: 2px;
    border: 1px solid #e2e8f0;
}
.status-tab-btn {
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    text-decoration: none !important;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all .15s ease;
}
.status-tab-btn:hover {
    color: #002C54;
    background: rgba(255,255,255,.6);
}
.status-tab-btn.active {
    background: #002C54;
    color: #ffffff;
    box-shadow: 0 1px 2px rgba(0,44,84,.2);
}
.tab-badge {
    font-size: 10px;
    background: rgba(0,0,0,.08);
    padding: 1px 5px;
    border-radius: 10px;
}
.status-tab-btn.active .tab-badge {
    background: rgba(255,255,255,.25);
    color: #ffffff;
}
.tab-badge-danger {
    background: #ef4444 !important;
    color: #ffffff !important;
}

/* Type Filter Chips */
.type-filter-chips {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.type-filter-label {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    margin-right: 2px;
}
.type-chip {
    padding: 2px 8px;
    font-size: 10.5px;
    font-weight: 500;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #334155;
    text-decoration: none !important;
    transition: all .15s ease;
}
.type-chip:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.type-chip.active {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
    font-weight: 600;
}

/* Search Form */
.notify-search-form {
    display: flex;
    align-items: center;
    gap: 4px;
}
.search-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}
.search-icon {
    position: absolute;
    left: 8px;
    font-size: 11px;
    color: #94a3b8;
    pointer-events: none;
}
.search-ctrl {
    height: 27px;
    width: 250px;
    padding: 0 24px 0 26px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    outline: none;
    transition: all .15s ease;
}
.search-ctrl:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 1px #0284c7;
}
.search-clear-btn {
    position: absolute;
    right: 6px;
    font-size: 14px;
    color: #94a3b8;
    text-decoration: none !important;
    cursor: pointer;
    line-height: 1;
}
.search-clear-btn:hover {
    color: #475569;
}
.search-submit-btn {
    height: 27px;
    padding: 0 9px;
    font-size: 11px;
    font-weight: 600;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    border-radius: 2px;
    cursor: pointer;
}
.search-submit-btn:hover {
    background: #0284c7;
}
.btn-reset-filters {
    font-size: 11px;
    color: #dc2626;
    font-weight: 600;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-reset-filters:hover {
    color: #991b1b;
}

/* 3. Main Feed Scrollable Container */
.notify-feed-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 6px 4px 10px 4px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* Notification Card */
.notify-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 10px 14px;
    display: flex;
    gap: 12px;
    position: relative;
    transition: all .15s ease-in-out;
    box-shadow: 0 1px 2px rgba(0,0,0,.03);
}
.notify-card:hover {
    box-shadow: 0 2px 6px rgba(0,44,84,.08);
}
.notify-card.is-unread {
    border-left: 4px solid #0284c7;
    background: #f0f7ff;
}
.notify-card.is-read {
    border-left: 4px solid #cbd5e1;
    background: #ffffff;
}

/* Type Icon Box */
.notify-icon-box {
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    margin-top: 1px;
}
.notify-icon-box.type-notice {
    background: #fff7ed;
    color: #ea580c;
    border: 1px solid #fed7aa;
}
.notify-icon-box.type-approval {
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
}
.notify-icon-box.type-complaint {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.notify-icon-box.type-default {
    background: #f1f5f9;
    color: #002C54;
    border: 1px solid #cbd5e1;
}

/* Card Body Content */
.notify-body {
    flex: 1;
    min-width: 0;
}
.notify-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 4px;
}
.notify-title-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    min-width: 0;
}
.notify-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}
.badge-type {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1.5px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: .03em;
}
.badge-type-notice {
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
}
.badge-type-approval {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
}
.badge-type-complaint {
    background: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
}
.badge-type-default {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.notify-status-badge {
    font-size: 9.5px;
    font-weight: 600;
    padding: 1.5px 6px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-unread {
    background: #0284c7;
    color: #ffffff;
}
.status-pulse-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #ffffff;
}
.badge-read {
    background: #e2e8f0;
    color: #64748b;
}

/* Header Meta & Actions */
.notify-meta-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.notify-timestamp {
    font-size: 10.5px;
    color: #64748b;
    white-space: nowrap;
}
.notify-timestamp .exact-time {
    color: #94a3b8;
    margin-left: 2px;
}
.notify-quick-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}
.btn-card-action {
    height: 22px;
    padding: 0 7px;
    font-size: 10.5px;
    font-weight: 600;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all .15s ease;
}
.btn-card-action:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}
.btn-delete-item:hover {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
}

/* Message Text */
.notify-message {
    font-size: 12px;
    line-height: 1.5;
    color: #334155;
    margin-top: 4px;
    margin-bottom: 4px;
}
.notify-message.is-collapsed {
    max-height: 3.2em;
    overflow: hidden;
    position: relative;
}
.btn-msg-toggle {
    background: none;
    border: none;
    color: #0284c7;
    font-size: 11px;
    font-weight: 600;
    padding: 0;
    cursor: pointer;
    margin-top: 2px;
}
.btn-msg-toggle:hover {
    text-decoration: underline;
    color: #0369a1;
}

/* Attachment Chip */
.notify-attachment-strip {
    margin-top: 6px;
}
.attachment-chip {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 4px 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    font-size: 11px;
    color: #0f172a;
    text-decoration: none !important;
    transition: all .15s ease;
}
.attachment-chip:hover {
    background: #f8fafc;
    border-color: #0284c7;
}
.attachment-name {
    font-weight: 600;
}
.attachment-action {
    color: #0284c7;
    font-size: 10px;
    font-weight: 600;
    margin-left: 4px;
}

/* Complaint Embedded Student Box */
.complaint-embedded-box {
    margin-top: 8px;
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #dc2626;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.complaint-embedded-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.complaint-student-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    background: #fee2e2;
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.complaint-student-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.complaint-student-meta {
    display: flex;
    flex-direction: column;
}
.student-name-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.student-full-name {
    font-size: 12.5px;
    font-weight: 700;
    color: #0f172a;
}
.badge-class {
    font-size: 10px;
    font-weight: 600;
    color: #002C54;
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 2px;
}
.student-sub-info {
    font-size: 10.5px;
    color: #64748b;
    margin-top: 1px;
}
.meta-sep {
    margin: 0 4px;
    color: #cbd5e1;
}
.complaint-embedded-right {
    display: flex;
    align-items: center;
    gap: 12px;
}
.ticket-badge-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}
.ticket-number-badge {
    font-size: 11.5px;
    font-weight: 700;
    color: #002C54;
    font-family: monospace;
}
.complaint-status-pill {
    font-size: 9.5px;
    padding: 2px 6px;
    text-transform: uppercase;
}
.ticket-action-btns {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-sub-action {
    height: 24px;
    padding: 0 8px;
    font-size: 11px;
    font-weight: 600;
    background: #ffffff;
    color: #002C54;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-sub-action:hover {
    background: #f1f5f9;
    color: #001f3d;
}
.btn-main-action {
    height: 24px;
    padding: 0 9px;
    font-size: 11px;
    font-weight: 600;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    border-radius: 2px;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.btn-main-action:hover {
    background: #0284c7;
    color: #ffffff;
}

/* Notice Approval Review Container */
.approval-review-container {
    margin-top: 8px;
}
.inline-review-card {
    background: #ffffff;
    border: 1px solid #c7d2fe;
    border-radius: 3px;
    padding: 10px 12px;
}
.approval-header-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 6px;
    border-bottom: 1px solid #e0e7ff;
}
.approval-kicker {
    font-size: 10px;
    font-weight: 700;
    color: #4338ca;
    letter-spacing: .04em;
}
.approval-title {
    font-size: 11px;
    font-weight: 600;
    color: #312e81;
}
.audience-metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 8px;
}
.metric-cell {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 4px 8px;
    display: flex;
    flex-direction: column;
}
.metric-label {
    font-size: 9px;
    color: #64748b;
    text-transform: uppercase;
}
.metric-value {
    font-size: 11.5px;
    font-weight: 600;
    color: #0f172a;
}
.audience-tag-row {
    font-size: 10.5px;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}
.audience-pill {
    background: #e0e7ff;
    color: #3730a3;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}
.recipient-toggle-wrap {
    margin-bottom: 8px;
}
.recipient-detail-toggle {
    font-size: 11px;
    font-weight: 600;
    color: #4338ca;
    cursor: pointer;
}
.recipients-list-drawer {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    max-height: 180px;
    overflow-y: auto;
    margin-top: 6px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px;
}
.recipient-row-item {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 4px 6px;
}
.recipient-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #4338ca;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.recipient-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
}
.recipient-name {
    font-size: 10.5px;
    color: #0f172a;
    line-height: 1.2;
}
.recipient-sub {
    font-size: 9.5px;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.review-comment-wrap {
    margin-bottom: 8px;
}
.review-label {
    font-size: 10.5px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 3px;
    display: block;
}
.review-textarea {
    font-size: 11px;
    border-radius: 2px;
}
.review-btn-toolbar {
    display: flex;
    align-items: center;
    gap: 6px;
}
.btn-erp-success {
    background: #059669;
    color: #ffffff;
    border: 1px solid #047857;
    font-weight: 600;
}
.btn-erp-success:hover {
    background: #047857;
    color: #ffffff;
}
.btn-erp-danger {
    background: #dc2626;
    color: #ffffff;
    border: 1px solid #b91c1c;
    font-weight: 600;
}
.btn-erp-danger:hover {
    background: #b91c1c;
    color: #ffffff;
}
.btn-erp-outline {
    background: #ffffff;
    color: #002C54;
    border: 1px solid #cbd5e1;
    font-weight: 600;
}
.btn-erp-outline:hover {
    background: #f1f5f9;
}
.review-status-pill {
    padding: 6px 10px;
    border-radius: 3px;
    font-size: 11px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.review-status-pill.status-approved {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.review-status-pill.status-rejected {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.review-note-text {
    font-style: italic;
    color: #475569;
}

/* 4. Empty State */
.notify-empty-state {
    text-align: center;
    padding: 40px 20px;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 20px auto;
    max-width: 520px;
}
.empty-icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    font-size: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px auto;
}
.empty-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.empty-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 16px;
}
.empty-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
}

/* 5. Docked Bottom Bar (Arise ERP #002C54 Standard) */
.notify-bottom-bar {
    background: #002C54;
    border: 1px solid #001f3d;
    border-radius: 2px;
    padding: 6px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 -2px 6px rgba(0,44,84,.12);
}
.bottom-bar-left {
    font-size: 11px;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 4px;
}
.bottom-bar-left b {
    color: #ffffff;
}
.bottom-bar-unread-pill {
    color: #fde68a;
    font-weight: 600;
    margin-left: 4px;
}
.dash-pagination-wrap .pagination {
    margin: 0;
}
.dash-pagination-wrap .page-link {
    background: rgba(255,255,255,.1);
    border-color: rgba(255,255,255,.2);
    color: #ffffff;
    font-size: 11px;
    padding: 3px 8px;
}
.dash-pagination-wrap .page-item.active .page-link {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}
.dash-pagination-wrap .page-item.disabled .page-link {
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.1);
    color: #94a3b8;
}

@media(max-width: 768px) {
    .audience-metrics-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .recipients-list-drawer {
        grid-template-columns: 1fr;
    }
    .notify-card-header {
        flex-direction: column;
    }
    .notify-meta-group {
        width: 100%;
        justify-content: space-between;
        margin-top: 4px;
    }
    .notify-bottom-bar {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
@endsection

@section('scripts')
<script>
function confirmNoticeReview(event, form) {
    var submitter = event.submitter || document.activeElement;
    var decisionValue = submitter && submitter.value === 'rejected' ? 'rejected' : 'approved';
    var decisionLabel = decisionValue === 'rejected' ? 'reject' : 'approve and publish';
    if (!window.confirm('Are you sure you want to ' + decisionLabel + ' this notice?')) {
        return false;
    }
    var hiddenDecision = document.createElement('input');
    hiddenDecision.type = 'hidden';
    hiddenDecision.name = 'decision';
    hiddenDecision.value = decisionValue;
    form.appendChild(hiddenDecision);
    form.querySelectorAll('button[type="submit"]').forEach(function(button) {
        button.disabled = true;
    });
    return true;
}

$(document).ready(function() {
    var csrfToken = '{{ csrf_token() }}';

    // 1. Message View More / View Less Toggle
    $(document).on('click', '.btn-msg-toggle', function() {
        var id = $(this).data('id');
        var msgBox = $('#notify-msg-' + id);
        var isExpanded = $(this).attr('aria-expanded') === 'true';

        $(this).attr('aria-expanded', !isExpanded);
        msgBox.toggleClass('is-collapsed', isExpanded);
        $(this).html(isExpanded ? 'View more <i class="fa fa-angle-down"></i>' : 'View less <i class="fa fa-angle-up"></i>');

        // Automatically mark as read when expanding
        if (!isExpanded) {
            var card = $('#notify-card-' + id);
            if (card.hasClass('is-unread')) {
                var readBtn = card.find('.btn-mark-read');
                if (readBtn.length) {
                    readBtn.trigger('click');
                }
            }
        }
    });

    // 2. AJAX Mark as Read
    $(document).on('click', '.btn-mark-read', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var url = btn.data('read-url');
        var card = $('#notify-card-' + id);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: csrfToken },
            dataType: 'json',
            success: function(res) {
                if (res && res.ok) {
                    card.removeClass('is-unread').addClass('is-read');
                    $('#status-badge-' + id).removeClass('badge-unread').addClass('badge-read').text('Read');

                    // Swap button to Mark as Unread
                    var unreadUrl = "{{ url('user-notifications') }}/" + id + "/mark-unread";
                    btn.replaceWith(
                        '<button type="button" class="btn-card-action btn-mark-unread" data-id="' + id + '" data-unread-url="' + unreadUrl + '" title="Mark as Unread">' +
                        '<i class="fa fa-envelope-o"></i> <span>Mark Unread</span></button>'
                    );

                    // Decrement KPI Unread
                    var unreadSpan = $('#kpiUnread');
                    var currUnread = parseInt(unreadSpan.text()) || 0;
                    if (currUnread > 0) {
                        unreadSpan.text(currUnread - 1);
                        $('#kpiRead').text((parseInt($('#kpiRead').text()) || 0) + 1);
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa fa-check"></i> <span>Mark Read</span>');
            }
        });
    });

    // 3. AJAX Mark as Unread
    $(document).on('click', '.btn-mark-unread', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var url = btn.data('unread-url');
        var card = $('#notify-card-' + id);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: url,
            type: 'POST',
            data: { _token: csrfToken },
            dataType: 'json',
            success: function(res) {
                if (res && res.ok) {
                    card.removeClass('is-read').addClass('is-unread');
                    $('#status-badge-' + id).removeClass('badge-read').addClass('badge-unread')
                        .html('<span class="status-pulse-dot"></span> New');

                    // Swap button to Mark as Read
                    var readUrl = "{{ url('user-notifications') }}/" + id + "/mark-read";
                    btn.replaceWith(
                        '<button type="button" class="btn-card-action btn-mark-read" data-id="' + id + '" data-read-url="' + readUrl + '" title="Mark as Read">' +
                        '<i class="fa fa-check"></i> <span>Mark Read</span></button>'
                    );

                    // Increment KPI Unread
                    var unreadSpan = $('#kpiUnread');
                    var currUnread = parseInt(unreadSpan.text()) || 0;
                    unreadSpan.text(currUnread + 1);
                    var currRead = parseInt($('#kpiRead').text()) || 0;
                    if (currRead > 0) {
                        $('#kpiRead').text(currRead - 1);
                    }
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa fa-envelope-o"></i> <span>Mark Unread</span>');
            }
        });
    });

    // 4. AJAX Delete Single Item
    $(document).on('click', '.btn-delete-item', function(e) {
        e.preventDefault();
        var btn = $(this);
        var id = btn.data('id');
        var url = btn.data('delete-url');
        var card = $('#notify-card-' + id);

        if (!confirm('Are you sure you want to remove this notification?')) {
            return false;
        }

        btn.prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: csrfToken,
                _method: 'DELETE'
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.ok) {
                    card.fadeOut(250, function() {
                        $(this).remove();
                        if ($('.notify-card').length === 0) {
                            location.reload();
                        }
                    });
                }
            },
            error: function() {
                btn.prop('disabled', false);
                alert('Failed to remove notification. Please try again.');
            }
        });
    });

    // 5. Recipient Details Drawer Toggle
    $(document).on('click', '.recipient-detail-toggle', function() {
        var targetId = $(this).data('target');
        var drawer = $('#' + targetId);
        var isExpanded = $(this).attr('aria-expanded') === 'true';

        $(this).attr('aria-expanded', !isExpanded);
        drawer.toggleClass('d-none', isExpanded);
        $(this).html(isExpanded 
            ? '<i class="fa fa-users"></i> View recipient details <i class="fa fa-angle-down"></i>' 
            : '<i class="fa fa-users"></i> Hide recipient details <i class="fa fa-angle-up"></i>'
        );
    });
});
</script>
@endsection
@endsection

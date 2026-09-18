@extends('layout.app')

@section('content')
<div class="content-wrapper user-notification-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="page-heading mb-1">Notifications</h1>
                    <div class="text-muted small">Your notices and system updates.</div>
                </div>
                <div class="d-flex notification-actions">
                    <form method="post" action="{{ route('user.notifications.mark-all-read') }}">@csrf
                        <button class="btn btn-default btn-sm" {{ $unreadCount === 0 ? 'disabled' : '' }}><i class="fa fa-check mr-1"></i> Mark all read</button>
                    </form>
                    <form method="post" action="{{ route('user.notifications.clear-all') }}" onsubmit="return confirm('Clear all notifications?');">@csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" {{ $notifications->total() === 0 ? 'disabled' : '' }}><i class="fa fa-trash mr-1"></i> Clear all</button>
                    </form>
                </div>
            </div>

            @if(session('message'))<div class="alert alert-success py-2">{{ session('message') }}</div>@endif

            <div class="card notification-card">
                <div class="card-header">
                    <div class="filter-tabs">
                        <a href="{{ url('user-notifications?filter=all') }}" class="{{ $filter === 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ url('user-notifications?filter=unread') }}" class="{{ $filter === 'unread' ? 'active' : '' }}">Unread @if($unreadCount)<span>{{ $unreadCount }}</span>@endif</a>
                        <a href="{{ url('user-notifications?filter=read') }}" class="{{ $filter === 'read' ? 'active' : '' }}">Read</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        @php
                            $isUnread = (int) $notification->message_seen === 0;
                            $content = trim((string) $notification->content);
                            $isLong = mb_strlen($content) > 140 || substr_count($content, "\n") > 2;
                            $isNotice = $notification->type === 'notice';
                            $isApprovalRequest = $notification->type === 'notice_approval_request';
                            $managedNotice = $notification->managedNotice;
                            $isComplaint = $notification->type === 'complaint_admin';
                            $complaint = $notification->complaintContext;
                            $complaintStudent = $complaint ? $complaint->student : null;
                        @endphp
                        <article class="notification-row {{ $isUnread ? 'is-unread' : '' }}" id="user-notification-{{ $notification->id }}">
                            <div class="notification-icon type-{{ $isComplaint ? 'complaint' : (($isNotice || $isApprovalRequest) ? 'notice' : 'default') }}"><i class="fa {{ $isComplaint ? 'fa-comments-o' : (($isNotice || $isApprovalRequest) ? 'fa-bullhorn' : 'fa-bell') }}"></i></div>
                            <div class="notification-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h3>{{ $notification->title ?: 'Notification' }} @if($isNotice)<span class="type-badge">Notice</span>@elseif($isApprovalRequest)<span class="type-badge">Approval request</span>@elseif($isComplaint)<span class="type-badge complaint-badge">Complaint</span>@endif</h3>
                                        <small><i class="fa fa-clock-o"></i> {{ optional($notification->created_at)->format('d M Y, h:i A') }}</small>
                                    </div>
                                    <span class="read-badge {{ $isUnread ? 'unread' : '' }}">{{ $isUnread ? 'Unread' : 'Read' }}</span>
                                </div>
                                <div class="message-text {{ $isLong ? 'is-collapsed' : '' }}" id="user-message-{{ $notification->id }}">{{ $content ?: 'No notification message available.' }}</div>
                                @if($isComplaint && $complaint && $complaintStudent)
                                    <div class="complaint-student-card">
                                        <span class="complaint-student-avatar">
                                            @if($complaintStudent->image)
                                                <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$complaintStudent->image }}" alt="{{ trim($complaintStudent->first_name.' '.$complaintStudent->last_name) }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            @endif
                                            <i class="fa fa-user" style="{{ $complaintStudent->image ? 'display:none' : '' }}"></i>
                                        </span>
                                        <span class="complaint-student-info">
                                            <strong>{{ trim($complaintStudent->first_name.' '.$complaintStudent->last_name) ?: 'Student' }}</strong>
                                            <small>
                                                {{ optional($complaintStudent->ClassTypes)->name ?? 'Class not available' }}
                                                @if($complaintStudent->admissionNo) · Admission No: {{ $complaintStudent->admissionNo }} @endif
                                            </small>
                                            <small>
                                                @if($complaintStudent->father_name)Parent: {{ $complaintStudent->father_name }}@endif
                                                @if($complaintStudent->father_mobile) · {{ $complaintStudent->father_mobile }}@elseif($complaintStudent->mobile) · {{ $complaintStudent->mobile }}@endif
                                            </small>
                                        </span>
                                        <span class="complaint-ticket-meta"><b>{{ $complaint->ticket_no }}</b><small>{{ \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status) }}</small></span>
                                    </div>
                                @endif
                                <div class="d-flex align-items-center flex-wrap action-row">
                                    @if($isLong)
                                        <button type="button" class="btn btn-link btn-sm p-0 message-toggle" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}" aria-expanded="false">View more <i class="fa fa-angle-down"></i></button>
                                    @elseif($isUnread)
                                        <button type="button" class="btn btn-link btn-sm p-0 mark-read" data-id="{{ $notification->id }}" data-read-url="{{ route('user.notifications.mark-read', $notification->id) }}">Mark as read</button>
                                    @endif
                                    @if($isNotice && $notification->managed_notice_id && $notification->attachment_path)
                                        <a href="{{ url('notice-management/'.$notification->managed_notice_id.'/attachment') }}" class="pdf-link"><i class="fa fa-file-pdf-o"></i> {{ $notification->attachment_name ?: 'Notice attachment.pdf' }}</a>
                                    @endif
                                    @if($isComplaint && $complaint)
                                        @if($complaintStudent)<a href="{{ url('studentDetail/'.$complaintStudent->id) }}" class="btn btn-outline-primary btn-sm complaint-action"><i class="fa fa-user mr-1"></i>Student profile</a>@endif
                                        <a href="{{ url('complaints-management/'.$complaint->id).'?highlight=notification#complaint-ticket' }}" class="btn btn-primary btn-sm complaint-action"><i class="fa fa-eye mr-1"></i>View details</a>
                                    @endif
                                </div>
                                @if($isApprovalRequest && (int) Session::get('role_id') === 1 && $managedNotice)
                                    @if($managedNotice->status === 'pending')
                                        <form action="{{ url('notice-management/'.$managedNotice->id.'/review') }}" method="post" class="inline-review-form" onsubmit="return confirmNoticeReview(event, this);">
                                            @csrf
                                            <input type="hidden" name="return_to" value="user-notifications">
                                            @php
                                                $noticeRecipients = $managedNotice->recipients;
                                                $audienceNames = $managedNotice->audience_type === 'role'
                                                    ? $noticeRecipients->pluck('role_name')->filter()->unique()->values()
                                                    : ($managedNotice->audience_type === 'class'
                                                        ? $noticeRecipients->pluck('class_name')->filter()->unique()->values()
                                                        : collect());
                                                $studentCount = $noticeRecipients->where('recipient_type', 'student')->count();
                                                $staffCount = $noticeRecipients->where('recipient_type', 'user')->count();
                                            @endphp
                                            <div class="approval-audience-detail">
                                                <div class="audience-summary">
                                                    <span><b>Audience</b>{{ ucfirst($managedNotice->audience_type) }}</span>
                                                    <span><b>Total recipients</b>{{ $noticeRecipients->count() }}</span>
                                                    <span><b>Staff</b>{{ $staffCount }}</span>
                                                    <span><b>Students</b>{{ $studentCount }}</span>
                                                </div>
                                                @if($audienceNames->isNotEmpty())
                                                    <div class="audience-groups">
                                                        <b>{{ $managedNotice->audience_type === 'role' ? 'Selected roles:' : 'Selected classes:' }}</b>
                                                        @foreach($audienceNames as $audienceName)<span>{{ $audienceName }}</span>@endforeach
                                                    </div>
                                                @elseif($managedNotice->audience_type === 'specific')
                                                    <div class="audience-groups"><b>Selection:</b><span>Specific staff / students</span></div>
                                                @endif
                                                <button type="button" class="btn btn-link btn-sm p-0 recipient-detail-toggle" data-target="approval-recipients-{{ $notification->id }}" aria-expanded="false">
                                                    View all {{ $noticeRecipients->count() }} recipient details <i class="fa fa-angle-down"></i>
                                                </button>
                                                <div class="approval-recipient-list d-none" id="approval-recipients-{{ $notification->id }}">
                                                    @forelse($noticeRecipients as $recipient)
                                                        <div class="approval-recipient">
                                                            <span class="recipient-initial">{{ strtoupper(substr($recipient->recipient_name, 0, 1)) }}</span>
                                                            <span>
                                                                <b>{{ $recipient->recipient_name }}</b>
                                                                <small>
                                                                    {{ $recipient->recipient_type === 'student' ? 'Student' : 'Staff' }}
                                                                    @if($recipient->role_name) · {{ $recipient->role_name }} @endif
                                                                    @if($recipient->class_name) · {{ $recipient->class_name }} @endif
                                                                    @if($recipient->mobile) · {{ $recipient->mobile }} @endif
                                                                </small>
                                                            </span>
                                                        </div>
                                                    @empty
                                                        <div class="text-muted small p-2">No resolved recipients found.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <label>Approval / rejection notes <span class="text-danger">*</span></label>
                                            <textarea name="review_notes" class="form-control form-control-sm" rows="2" maxlength="2000" placeholder="Write approval instructions or rejection reason..." required></textarea>
                                            <div class="mt-2 d-flex flex-wrap review-actions">
                                                <button type="submit" name="decision" value="approved" class="btn btn-success btn-sm"><i class="fa fa-check mr-1"></i> Approve & publish</button>
                                                <button type="submit" name="decision" value="rejected" class="btn btn-danger btn-sm"><i class="fa fa-times mr-1"></i> Reject notice</button>
                                                <a href="{{ url('notice-management?status=pending') }}" class="btn btn-default btn-sm">View full details</a>
                                            </div>
                                        </form>
                                    @else
                                        <div class="review-completed status-{{ $managedNotice->status }}">
                                            <i class="fa {{ $managedNotice->status === 'approved' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            This notice has been {{ $managedNotice->status }}.
                                            @if($managedNotice->review_notes)<span>{{ $managedNotice->review_notes }}</span>@endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="empty-state"><i class="fa fa-bell-slash-o"></i><p>No notifications found.</p></div>
                    @endforelse
                </div>
                @if($notifications->hasPages())<div class="card-footer">{{ $notifications->links() }}</div>@endif
            </div>
        </div>
    </section>
</div>

<style>
.user-notification-page{background:#f4f7fb;color:#26324b}.page-heading{font-size:22px;font-weight:700}.notification-actions{gap:8px}.notification-card{border:1px solid #edf0f5;border-radius:12px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}.notification-card>.card-header{padding:0 15px;background:#fff;border-bottom:1px solid #edf0f5}.filter-tabs{display:flex;gap:18px}.filter-tabs a{position:relative;padding:13px 2px;color:#7d899e;font-size:12px;font-weight:600}.filter-tabs a.active{color:#4361ee}.filter-tabs a.active:after{content:'';position:absolute;left:0;right:0;bottom:0;height:2px;background:#4361ee}.filter-tabs span{background:#e05260;color:#fff;border-radius:10px;padding:1px 5px;font-size:9px}.notification-row{display:flex;gap:12px;padding:14px 15px;border-bottom:1px solid #edf0f5}.notification-row.is-unread{background:#f5f8ff;border-left:3px solid #4361ee}.notification-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;background:#edf2f8;color:#60708a;display:flex;align-items:center;justify-content:center}.notification-icon.type-notice{background:#fff1e1;color:#d17815}.notification-content{min-width:0;flex:1}.notification-content h3{font-size:13px;font-weight:700;margin:0 0 2px}.notification-content small{font-size:10px;color:#8792a5}.type-badge{font-size:9px;color:#a35e10;background:#fff0d9;border-radius:9px;padding:2px 6px;margin-left:5px}.read-badge{font-size:9px;padding:3px 7px;border-radius:10px;background:#edf0f5;color:#7d899e}.read-badge.unread{background:#e5ebff;color:#3654c7}.message-text{font-size:12px;line-height:1.55;color:#526078;white-space:pre-line;margin-top:8px}.message-text.is-collapsed{max-height:3.1em;overflow:hidden}.action-row{gap:14px;margin-top:7px}.action-row button{font-size:11px}.pdf-link{display:inline-flex;align-items:center;gap:5px;color:#c43d4e;font-size:11px}.empty-state{text-align:center;padding:42px;color:#8792a5}.empty-state i{font-size:24px}.empty-state p{font-size:12px;margin:8px 0}@media(max-width:576px){.notification-actions{display:none!important}.notification-row{padding:12px 10px}}
.notification-icon.type-complaint{background:#e8efff;color:#3659b8}.complaint-badge{background:#e8efff;color:#3659b8}.complaint-student-card{display:flex;align-items:center;gap:10px;margin-top:10px;padding:10px 11px;border:1px solid #dfe6f2;border-radius:9px;background:#fff}.complaint-student-avatar{width:38px;height:38px;flex:0 0 38px;display:flex;align-items:center;justify-content:center;border-radius:50%;overflow:hidden;background:#e8efff;color:#4361ee}.complaint-student-avatar img{width:100%;height:100%;object-fit:cover}.complaint-student-avatar i{width:100%;height:100%;align-items:center;justify-content:center}.complaint-student-info{display:flex;flex-direction:column;min-width:0}.complaint-student-info strong{font-size:12px;color:#2c3852}.complaint-student-info small{font-size:10px;color:#7d899e;white-space:normal}.complaint-ticket-meta{display:flex;flex-direction:column;align-items:flex-end;margin-left:auto;white-space:nowrap}.complaint-ticket-meta b{font-size:10px;color:#3659b8}.complaint-ticket-meta small{font-size:9px}.complaint-action{font-size:10px;padding:4px 9px}.action-row .complaint-action:first-of-type{margin-left:auto}@media(max-width:576px){.complaint-student-card{align-items:flex-start;flex-wrap:wrap}.complaint-ticket-meta{width:100%;align-items:flex-start;margin-left:48px}.action-row .complaint-action:first-of-type{margin-left:0}}
.inline-review-form{margin-top:12px;padding:11px;background:#fff;border:1px solid #e1e6ef;border-radius:9px}.inline-review-form label{font-size:11px;font-weight:600;margin-bottom:5px}.review-actions{gap:7px}.review-completed{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:10px;padding:8px 10px;border-radius:8px;font-size:11px}.review-completed span{display:block;width:100%;padding-left:19px;color:#637086}.review-completed.status-approved{background:#e5f7ef;color:#116341}.review-completed.status-rejected{background:#fdebed;color:#8c2634}
.approval-audience-detail{margin-bottom:11px;padding:10px;background:#f8faff;border:1px solid #e2e8f5;border-radius:8px}.audience-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:7px;margin-bottom:8px}.audience-summary span{display:flex;flex-direction:column;background:#fff;border:1px solid #e7ebf2;border-radius:6px;padding:6px 8px;font-size:11px}.audience-summary b{font-size:9px;color:#8792a5;margin-bottom:2px}.audience-groups{display:flex;align-items:center;gap:5px;flex-wrap:wrap;font-size:10px;margin-bottom:7px}.audience-groups>b{color:#637086}.audience-groups span{background:#e9efff;color:#3654c7;border-radius:10px;padding:3px 7px}.recipient-detail-toggle{font-size:11px}.approval-recipient-list{display:grid;grid-template-columns:repeat(2,1fr);gap:5px;max-height:240px;overflow:auto;margin-top:8px}.approval-recipient{display:flex;align-items:center;gap:7px;padding:6px;background:#fff;border:1px solid #e7ebf2;border-radius:6px}.approval-recipient>span:last-child{display:flex;flex-direction:column;min-width:0}.approval-recipient b{font-size:10px}.approval-recipient small{font-size:9px;color:#8792a5;white-space:normal}.recipient-initial{flex:0 0 24px;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#edf2ff;color:#4361ee;font-size:10px;font-weight:700}@media(max-width:767px){.audience-summary{grid-template-columns:repeat(2,1fr)}.approval-recipient-list{grid-template-columns:1fr}}
</style>
<script>
function confirmNoticeReview(event, form){
    var submitter = event.submitter || document.activeElement;
    var decisionValue = submitter && submitter.value === 'rejected' ? 'rejected' : 'approved';
    var decisionLabel = decisionValue === 'rejected' ? 'reject' : 'approve and publish';
    if (!window.confirm('Are you sure you want to '+decisionLabel+' this notice?')) return false;
    var hiddenDecision = document.createElement('input');
    hiddenDecision.type = 'hidden';
    hiddenDecision.name = 'decision';
    hiddenDecision.value = decisionValue;
    form.appendChild(hiddenDecision);
    form.querySelectorAll('button[type="submit"]').forEach(function(button){button.disabled=true;});
    return true;
}
document.addEventListener('DOMContentLoaded', function(){
    var csrfToken = '{{ csrf_token() }}';
    function markRead(button){
        var row = document.getElementById('user-notification-'+button.dataset.id);
        if (!row || !row.classList.contains('is-unread')) return;
        fetch(button.dataset.readUrl, {method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
            .then(function(response){if(!response.ok) throw new Error();return response.json();})
            .then(function(){row.classList.remove('is-unread');var badge=row.querySelector('.read-badge');if(badge){badge.textContent='Read';badge.classList.remove('unread');}if(button.classList.contains('mark-read'))button.remove();});
    }
    document.querySelectorAll('.message-toggle').forEach(function(button){button.addEventListener('click',function(){var message=document.getElementById('user-message-'+button.dataset.id);var expanded=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',expanded?'false':'true');message.classList.toggle('is-collapsed',expanded);button.innerHTML=expanded?'View more <i class="fa fa-angle-down"></i>':'View less <i class="fa fa-angle-up"></i>';if(!expanded)markRead(button);});});
    document.querySelectorAll('.mark-read').forEach(function(button){button.addEventListener('click',function(){markRead(button);});});
    document.querySelectorAll('.recipient-detail-toggle').forEach(function(button){
        button.addEventListener('click', function(){
            var list = document.getElementById(button.dataset.target);
            var expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            list.classList.toggle('d-none', expanded);
            button.innerHTML = expanded
                ? 'View all '+list.querySelectorAll('.approval-recipient').length+' recipient details <i class="fa fa-angle-down"></i>'
                : 'Hide recipient details <i class="fa fa-angle-up"></i>';
        });
    });
});
</script>
@endsection

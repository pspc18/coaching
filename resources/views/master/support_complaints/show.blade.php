@extends('layout.app')

@section('content')
@php
    $studentName = trim(optional($complaint->student)->first_name.' '.optional($complaint->student)->last_name) ?: 'Student';
    $statusLabel = \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst($complaint->status);
    $categoryLabel = \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst($complaint->category);
@endphp

<div class="content-wrapper complaint-detail-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            @if(request('highlight') === 'notification')
                <div class="notification-focus-note"><i class="fa fa-bell mr-1"></i>Complaint opened from the selected notification.</div>
            @endif
            <div id="complaint-ticket" class="detail-hero mb-3 {{ request('highlight') === 'notification' ? 'notification-highlight' : '' }}">
                <div class="hero-main">
                    <a href="{{ url('complaints-management') }}" class="hero-back" aria-label="Back to complaints"><i class="fa fa-arrow-left"></i></a>
                    <div>
                        <span class="detail-kicker">Support Ticket · {{ $complaint->ticket_no }}</span>
                        <h1>{{ $complaint->subject }}</h1>
                        <p>Submitted by {{ $studentName }} as {{ ucfirst($complaint->submitted_as) }}</p>
                    </div>
                </div>
                <span class="hero-status status-{{ $complaint->status }}"><i class="fa fa-circle"></i>{{ $statusLabel }}</span>
            </div>

            @if(session('message'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    <i class="fa fa-check-circle mr-1"></i>{{ session('message') }}
                    <button type="button" class="close py-1" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2"><i class="fa fa-exclamation-circle mr-1"></i>{{ $errors->first() }}</div>
            @endif

            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="card detail-card conversation-card mb-3">
                        <div class="card-header">
                            <div><h3 class="card-title">Conversation history</h3><p>{{ $complaint->replies->count() }} messages in this complaint</p></div>
                            <span class="header-icon"><i class="fa fa-comments-o"></i></span>
                        </div>
                        <div class="card-body conversation-area">
                            @forelse($complaint->replies as $reply)
                                @php $isAdminReply = $reply->sender_type === 'admin'; @endphp
                                <div class="message-row {{ $isAdminReply ? 'admin-message' : 'user-message' }}">
                                    <div class="message-avatar"><i class="fa {{ $isAdminReply ? 'fa-user-secret' : 'fa-user' }}"></i></div>
                                    <div class="message-content">
                                        <div class="message-meta">
                                            <strong>{{ $isAdminReply ? 'Administrator' : ucfirst($reply->sender_type) }}</strong>
                                            <span>{{ $reply->created_at->format('d M Y, h:i A') }}</span>
                                        </div>
                                        <div class="message-bubble">
                                            @if($reply->status_after)
                                                <span class="message-status status-{{ $reply->status_after }}"><i class="fa fa-circle"></i>{{ \App\Models\SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst($reply->status_after) }}</span>
                                            @endif
                                            <p>{{ $reply->message }}</p>
                                            @if($reply->attachment_path)
                                                <a class="attachment-link" href="{{ url('support-complaint-attachment/'.$reply->id) }}">
                                                    <span class="attachment-icon"><i class="fa fa-paperclip"></i></span>
                                                    <span><strong>{{ $reply->attachment_name }}</strong><small>{{ number_format($reply->attachment_size / 1024) }} KB · Click to download</small></span>
                                                    <i class="fa fa-download ml-auto"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-conversation"><i class="fa fa-comments-o"></i><span>No conversation found.</span></div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card detail-card reply-card mb-3">
                        <div class="card-header">
                            <div><h3 class="card-title">Reply to student / parent</h3><p>Your reply will create an in-app and push notification</p></div>
                            <span class="header-icon"><i class="fa fa-reply"></i></span>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ url('complaints-management/'.$complaint->id.'/reply') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="reply-message">Message <span class="text-danger">*</span></label>
                                    <textarea id="reply-message" name="message" class="form-control" rows="5" maxlength="10000" placeholder="Write a clear response to the student or parent..." required>{{ old('message') }}</textarea>
                                    <small class="form-text text-muted">Maximum 10,000 characters</small>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group mb-md-0">
                                            <label for="reply-attachment">Attachment <span class="optional-label">Optional</span></label>
                                            <div class="custom-file">
                                                <input type="file" name="attachment" id="reply-attachment" class="custom-file-input" accept=".pdf,.jpg,.jpeg,.png">
                                                <label class="custom-file-label" for="reply-attachment">Choose a file</label>
                                            </div>
                                            <small class="form-text text-muted">PDF, JPG, JPEG or PNG · maximum 10 MB</small>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-0">
                                            <label for="reply-status">Status after reply</label>
                                            <select id="reply-status" name="status" class="form-control">
                                                @foreach(\App\Models\SupportComplaint::STATUSES as $key => $label)
                                                    <option value="{{ $key }}" {{ old('status', $complaint->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="reply-actions"><button class="btn btn-primary"><i class="fa fa-paper-plane mr-1"></i>Send reply</button></div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">
                    <div class="card detail-card mb-3">
                        <div class="card-header"><div><h3 class="card-title">Ticket details</h3><p>Complaint and student information</p></div><span class="header-icon"><i class="fa fa-info-circle"></i></span></div>
                        <div class="card-body ticket-details">
                            <div class="student-summary">
                                <span class="student-avatar"><i class="fa fa-user"></i></span>
                                <div><strong>{{ $studentName }}</strong><small>{{ optional(optional($complaint->student)->ClassTypes)->name ?? 'Class not available' }}</small></div>
                            </div>
                            <div class="detail-list">
                                <div><span>Ticket number</span><strong>{{ $complaint->ticket_no }}</strong></div>
                                <div><span>Submitted as</span><strong>{{ ucfirst($complaint->submitted_as) }}</strong></div>
                                <div><span>Category</span><strong>{{ $categoryLabel }}</strong></div>
                                <div><span>Priority</span><strong><span class="priority-badge priority-{{ $complaint->priority }}">{{ ucfirst($complaint->priority) }}</span></strong></div>
                                <div><span>Current status</span><strong><span class="detail-status status-{{ $complaint->status }}">{{ $statusLabel }}</span></strong></div>
                                <div><span>Created</span><strong>{{ $complaint->created_at->format('d M Y, h:i A') }}</strong></div>
                                <div><span>Last activity</span><strong>{{ optional($complaint->last_replied_at)->diffForHumans() }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="card detail-card status-card mb-3">
                        <div class="card-header"><div><h3 class="card-title">Update status</h3><p>Change status without sending a reply</p></div><span class="header-icon"><i class="fa fa-tasks"></i></span></div>
                        <div class="card-body">
                            <form method="post" action="{{ url('complaints-management/'.$complaint->id.'/status') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="ticket-status">Complaint status</label>
                                    <select id="ticket-status" name="status" class="form-control">
                                        @foreach(\App\Models\SupportComplaint::STATUSES as $key => $label)
                                            <option value="{{ $key }}" {{ $complaint->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-outline-primary btn-block"><i class="fa fa-refresh mr-1"></i>Update status</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.complaint-detail-page{background:#f4f7fb;color:#2c3852;min-height:100vh}
.detail-hero{background:linear-gradient(120deg,#233b75,#365bb6);border-radius:12px;padding:18px 20px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:16px;box-shadow:0 8px 22px rgba(29,55,113,.14)}
.notification-focus-note{display:inline-flex;align-items:center;margin-bottom:8px;padding:5px 9px;border-radius:8px;background:#fff3cd;color:#805f08;font-size:11px;border:1px solid #ffe49a}.detail-hero.notification-highlight{animation:complaintFocus 1.8s ease-out;box-shadow:0 0 0 4px rgba(255,193,7,.38),0 8px 22px rgba(29,55,113,.14)}@keyframes complaintFocus{0%,35%{transform:translateY(-2px);box-shadow:0 0 0 7px rgba(255,193,7,.55),0 10px 25px rgba(29,55,113,.2)}100%{transform:none;box-shadow:0 0 0 4px rgba(255,193,7,.38),0 8px 22px rgba(29,55,113,.14)}}
.hero-main{display:flex;align-items:center;gap:13px;min-width:0}.hero-back{width:36px;height:36px;flex:0 0 36px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.28);border-radius:9px;color:#fff;background:rgba(255,255,255,.08)}.hero-back:hover{color:#fff;background:rgba(255,255,255,.16)}
.detail-kicker{font-size:11px;letter-spacing:.06em;text-transform:uppercase;opacity:.72}.detail-hero h1{font-size:22px;line-height:1.2;margin:4px 0 2px;font-weight:700}.detail-hero p{margin:0;font-size:12px;opacity:.82}
.hero-status,.message-status,.detail-status{display:inline-flex;align-items:center;gap:6px;border-radius:999px;white-space:nowrap}.hero-status{padding:7px 11px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.22);font-size:11px;font-weight:600}.hero-status i,.message-status i{font-size:5px}
.detail-card{border:0;border-radius:12px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}.detail-card .card-header{background:#fff;border-bottom:1px solid #edf0f5;padding:12px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px}.detail-card .card-title{margin:0;float:none;font-size:14px;font-weight:700;color:#2c3852}.detail-card .card-header p{font-size:11px;color:#8792a5;margin:2px 0 0}.header-icon{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:9px;background:#eef3ff;color:#4361ee}
.conversation-area{background:#f8fafc;padding:18px;max-height:600px;overflow-y:auto}.message-row{display:flex;align-items:flex-start;gap:9px;margin-bottom:18px}.message-row:last-child{margin-bottom:0}.message-avatar{width:32px;height:32px;flex:0 0 32px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#e9edf4;color:#667085}.message-content{max-width:78%;min-width:0}.message-meta{display:flex;align-items:center;gap:8px;margin:0 2px 5px}.message-meta strong{font-size:11px}.message-meta span{font-size:9px;color:#8b96a8}.message-bubble{background:#fff;border:1px solid #e4e9f1;border-radius:4px 12px 12px 12px;padding:11px 12px;box-shadow:0 2px 7px rgba(36,52,82,.035)}.message-bubble p{font-size:12px;line-height:1.55;white-space:pre-line;margin:6px 0 0;color:#39465d}.message-status{font-size:9px;padding:3px 7px;background:#eef3ff;color:#4361ee}
.admin-message{flex-direction:row-reverse}.admin-message .message-avatar{background:#dfe8ff;color:#3157b7}.admin-message .message-meta{justify-content:flex-end}.admin-message .message-bubble{background:#edf3ff;border-color:#dce6fb;border-radius:12px 4px 12px 12px}
.attachment-link{display:flex;align-items:center;gap:8px;margin-top:10px;padding:8px;border:1px solid #dce3ee;border-radius:9px;background:rgba(255,255,255,.72);color:#3157b7}.attachment-link:hover{color:#24468f;background:#fff}.attachment-icon{width:28px;height:28px;flex:0 0 28px;display:flex;align-items:center;justify-content:center;border-radius:7px;background:#eef3ff}.attachment-link span:nth-child(2){min-width:0;display:flex;flex-direction:column}.attachment-link strong{font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.attachment-link small{font-size:9px;color:#8792a5;margin-top:1px}
.detail-card label{font-size:11px;font-weight:600;color:#536178;margin-bottom:5px}.detail-card .form-control,.custom-file-label{font-size:12px;border-color:#dfe4ec}.detail-card textarea.form-control{line-height:1.5;resize:vertical}.optional-label{font-size:9px;font-weight:400;color:#8792a5;margin-left:3px}.reply-actions{display:flex;justify-content:flex-end;border-top:1px solid #edf0f5;margin-top:16px;padding-top:13px}.reply-actions .btn{font-size:12px;padding:7px 16px}
.student-summary{display:flex;align-items:center;gap:10px;padding:0 0 14px;border-bottom:1px solid #edf0f5}.student-avatar{width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:10px;background:#eef3ff;color:#4361ee;font-size:17px}.student-summary div{display:flex;flex-direction:column}.student-summary strong{font-size:13px}.student-summary small{font-size:10px;color:#8792a5;margin-top:2px}.detail-list>div{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid #f0f2f6}.detail-list>div:last-child{border-bottom:0;padding-bottom:0}.detail-list>div>span{font-size:11px;color:#8792a5}.detail-list>div>strong{font-size:11px;text-align:right;color:#39465d}
.priority-badge,.detail-status{font-size:9px;padding:4px 8px}.priority-low{background:#eef6ff;color:#3271a8}.priority-medium{background:#fff5db;color:#916812}.priority-high{background:#ffead5;color:#b7600a}.priority-urgent{background:#fde7ea;color:#b82e40}.detail-status,.message-status{background:#eef3ff;color:#4361ee}.status-resolved{background:#e8f7ef;color:#188653}.status-closed{background:#eef0f3;color:#596273}.status-awaiting_user{background:#f3edff;color:#754ac1}.status-in_progress{background:#fff1df;color:#b5650f}.status-reopened{background:#fdebf0;color:#ba3f60}.status-acknowledged{background:#e5f7f7;color:#118385}
.empty-conversation{min-height:160px;display:flex;align-items:center;justify-content:center;flex-direction:column;color:#8792a5;font-size:11px}.empty-conversation i{font-size:28px;margin-bottom:7px;color:#b7c1d1}
@media(max-width:991px){.conversation-area{max-height:none}}
@media(max-width:575px){.detail-hero{padding:15px;align-items:flex-start;flex-direction:column}.detail-hero h1{font-size:19px}.hero-status{margin-left:49px}.conversation-area{padding:13px 10px}.message-content{max-width:88%}.message-avatar{width:28px;height:28px;flex-basis:28px}.message-meta{align-items:flex-start;flex-direction:column;gap:1px}.admin-message .message-meta{align-items:flex-end}.message-bubble{padding:9px 10px}.detail-card .card-body{padding:13px}.reply-actions .btn{width:100%}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var fileInput = document.getElementById('reply-attachment');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var label = this.nextElementSibling;
            if (label) label.textContent = this.files && this.files.length ? this.files[0].name : 'Choose a file';
        });
    }
});
</script>
@endsection

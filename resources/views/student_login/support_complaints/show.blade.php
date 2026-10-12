@php
    $student = $complaint->student;
    $className = optional($student->ClassTypes)->name ?: 'Student';
    $statusLabel = \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst(str_replace('_', ' ', (string) $complaint->status));
    $categoryLabel = \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst(str_replace('_', ' ', (string) $complaint->category));
    $submittedAs = $complaint->submitted_as === 'parent' ? 'Parent / Guardian' : 'Student';
    $replyCount = $complaint->replies->count();
    $attachmentReply = $complaint->replies->firstWhere('attachment_path');
    $canReply = !in_array($complaint->status, ['resolved', 'closed'], true);

    $statusMeta = [
        'open'          => ['label' => 'Open', 'class' => 'badge-soft-info', 'icon' => 'fa-clock-o'],
        'acknowledged'  => ['label' => 'Acknowledged', 'class' => 'badge-soft-info', 'icon' => 'fa-check'],
        'in_progress'   => ['label' => 'In Progress', 'class' => 'badge-soft-info', 'icon' => 'fa-spinner fa-spin'],
        'awaiting_user' => ['label' => 'Awaiting Your Reply', 'class' => 'badge-soft-amber', 'icon' => 'fa-reply'],
        'resolved'      => ['label' => 'Resolved', 'class' => 'badge-soft-success', 'icon' => 'fa-check-circle'],
        'closed'        => ['label' => 'Closed', 'class' => 'badge-soft-slate', 'icon' => 'fa-lock'],
        'reopened'      => ['label' => 'Reopened', 'class' => 'badge-soft-purple', 'icon' => 'fa-refresh'],
    ];
    $currentStatusMeta = $statusMeta[$complaint->status] ?? ['label' => $statusLabel, 'class' => 'badge-soft-info', 'icon' => 'fa-circle-o'];

    $priorityMeta = [
        'low'    => ['label' => 'Low', 'class' => 'priority-low'],
        'medium' => ['label' => 'Medium', 'class' => 'priority-medium'],
        'high'   => ['label' => 'High', 'class' => 'priority-high'],
        'urgent' => ['label' => 'Urgent', 'class' => 'priority-urgent'],
    ];
    $currentPriorityMeta = $priorityMeta[$complaint->priority] ?? ['label' => ucfirst($complaint->priority ?? 'Medium'), 'class' => 'priority-medium'];
@endphp
@extends('student_login.layout.app')

@section('title', 'Ticket #' . $complaint->ticket_no)
@section('page_title', 'TICKET DETAILS')
@section('page_sub', '#' . $complaint->ticket_no . ' · ' . $categoryLabel)

@section('styles')
<style>
.mob-complaint-show-page {
    padding: 0 0 calc(var(--bottom-nav-height, 52px) + 24px + var(--safe-bottom, 0px));
    color: #1e293b;
    width: 100%;
    box-sizing: border-box;
}

/* Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    border-radius: 4px;
    padding: 14px 14px 12px;
    color: #ffffff;
    box-shadow: 0 6px 20px -2px rgba(0, 24, 51, 0.28);
    position: relative;
    overflow: hidden;
    margin-bottom: 10px;
}

.mob-hero-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.mob-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}

.mob-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    text-decoration: none;
    font-size: 13px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.mob-back-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

.mob-hero-title {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.25;
}

.mob-hero-sub {
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 2px;
}

.mob-hero-badges {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}

.mob-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.14);
    color: #ffffff;
}

/* Status Badges */
.badge-soft-info {
    background: #e0f2fe;
    color: #0369a1;
}
.badge-soft-amber {
    background: #fef3c7;
    color: #92400e;
}
.badge-soft-success {
    background: #dcfce7;
    color: #15803d;
}
.badge-soft-slate {
    background: #f1f5f9;
    color: #475569;
}
.badge-soft-purple {
    background: #ede9fe;
    color: #6b21a8;
}

/* Priority Badges */
.priority-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.priority-low {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.priority-medium {
    background: #e0f2fe;
    color: #0284c7;
    border: 1px solid #bae6fd;
}
.priority-high {
    background: #ffedd5;
    color: #c2410c;
    border: 1px solid #fed7aa;
}
.priority-urgent {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
}

/* Section Box */
.mob-section-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 12px 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    margin-bottom: 10px;
}

.mob-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
}

.mob-section-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.mob-section-title i {
    color: #0284c7;
}

/* Initial Complaint Body */
.mob-complaint-subject {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    margin: 0 0 8px;
    word-break: break-word;
}

.mob-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 12px;
}

.mob-meta-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 6px 8px;
}

.mob-meta-label {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: block;
    margin-bottom: 2px;
}

.mob-meta-value {
    font-size: 11px;
    font-weight: 700;
    color: #1e293b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-complaint-msg-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 10px 12px;
    font-size: 12px;
    line-height: 1.6;
    color: #334155;
    white-space: pre-line;
    word-break: break-word;
}

/* Attachment Pill */
.mob-attach-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding: 7px 10px;
    border-radius: 4px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #0284c7;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
    max-width: 100%;
}

.mob-attach-pill:hover {
    background: #f0f9ff;
    border-color: #38bdf8;
    color: #0369a1;
}

.mob-attach-pill i {
    font-size: 14px;
    color: #0284c7;
    flex-shrink: 0;
}

.mob-attach-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Conversation Thread */
.mob-thread {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.mob-bubble-row {
    display: flex;
    width: 100%;
}

.mob-bubble-row.mine {
    justify-content: flex-end;
}

.mob-bubble-row.school {
    justify-content: flex-start;
}

.mob-bubble {
    max-width: 88%;
    padding: 10px 12px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    position: relative;
}

.mob-bubble-row.mine .mob-bubble {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    border: 1px solid #001833;
    color: #ffffff;
}

.mob-bubble-row.school .mob-bubble {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #1e293b;
}

.mob-bubble-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 5px;
    font-size: 10px;
    font-weight: 700;
}

.mob-bubble-row.mine .mob-bubble-top {
    color: #38bdf8;
}

.mob-bubble-row.school .mob-bubble-top {
    color: #0284c7;
}

.mob-bubble-msg {
    font-size: 11.5px;
    line-height: 1.55;
    word-break: break-word;
    white-space: pre-line;
}

.mob-bubble-row.mine .mob-bubble-msg {
    color: #f1f5f9;
}

.mob-bubble-row.school .mob-bubble-msg {
    color: #334155;
}

.mob-bubble-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-top: 6px;
    font-size: 9px;
    opacity: 0.75;
}

.mob-bubble-row.mine .mob-bubble-footer {
    color: #cbd5e1;
}

.mob-bubble-row.school .mob-bubble-footer {
    color: #64748b;
}

.mob-bubble-file {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 5px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-decoration: none;
    max-width: 100%;
}

.mob-bubble-row.mine .mob-bubble-file {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.mob-bubble-row.school .mob-bubble-file {
    background: #e2e8f0;
    color: #0284c7;
    border: 1px solid #cbd5e1;
}

.mob-empty-thread {
    text-align: center;
    padding: 24px 14px;
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    color: #64748b;
    font-size: 11px;
}

.mob-empty-thread i {
    font-size: 24px;
    color: #94a3b8;
    margin-bottom: 6px;
    display: block;
}

/* Reply Form Box */
.mob-reply-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 12px 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.mob-reply-textarea {
    width: 100%;
    min-height: 90px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #1e293b;
    padding: 8px 10px;
    font-size: 12px;
    line-height: 1.5;
    resize: vertical;
    box-sizing: border-box;
    transition: all 0.15s ease;
}

.mob-reply-textarea:focus {
    outline: none;
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.12);
}

.mob-upload-drop {
    margin-top: 8px;
    padding: 8px 10px;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mob-upload-icon {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    border-radius: 4px;
    display: grid;
    place-items: center;
    background: #e0f2fe;
    color: #0284c7;
    font-size: 13px;
}

.mob-upload-input {
    width: 100%;
    font-size: 11px;
    color: #475569;
}

.mob-reply-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
}

.mob-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.15s ease;
    cursor: pointer;
    border: none;
}

.mob-btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    flex: 1;
}

.mob-btn-secondary:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.mob-btn-primary {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 24, 51, 0.2);
    flex: 1.5;
}

.mob-btn-primary:hover {
    background: linear-gradient(135deg, #002244 0%, #00386b 100%);
    color: #ffffff;
}

.mob-locked-box {
    text-align: center;
    padding: 16px 14px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    color: #475569;
    font-size: 11.5px;
}

.mob-locked-icon {
    font-size: 22px;
    color: #0284c7;
    margin-bottom: 6px;
}

/* Dark theme overrides */
[data-theme=dark] .mob-complaint-show-page {
    color: #e2e8f0;
}
[data-theme=dark] .mob-section-box,
[data-theme=dark] .mob-reply-box {
    background: #1e293b;
    border-color: #334155;
}
[data-theme=dark] .mob-section-head {
    border-bottom-color: #334155;
}
[data-theme=dark] .mob-section-title {
    color: #f1f5f9;
}
[data-theme=dark] .mob-complaint-subject {
    color: #f8fafc;
}
[data-theme=dark] .mob-meta-item,
[data-theme=dark] .mob-complaint-msg-box {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
}
[data-theme=dark] .mob-meta-value {
    color: #f1f5f9;
}
[data-theme=dark] .mob-attach-pill {
    background: #0f172a;
    border-color: #334155;
    color: #38bdf8;
}
[data-theme=dark] .mob-bubble-row.school .mob-bubble {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}
[data-theme=dark] .mob-bubble-row.school .mob-bubble-msg {
    color: #cbd5e1;
}
[data-theme=dark] .mob-empty-thread,
[data-theme=dark] .mob-locked-box {
    background: #0f172a;
    border-color: #334155;
    color: #94a3b8;
}
[data-theme=dark] .mob-reply-textarea {
    background: #0f172a;
    border-color: #334155;
    color: #f1f5f9;
}
[data-theme=dark] .mob-upload-drop {
    background: #0f172a;
    border-color: #334155;
}
[data-theme=dark] .mob-upload-icon {
    background: #1e293b;
    color: #38bdf8;
}
[data-theme=dark] .mob-btn-secondary {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
}
</style>
@endsection

@section('content')
<div class="mob-complaint-show-page">
    @if(session('message'))
        <div class="alert alert-success py-2 px-3 mb-2 small rounded-1" role="alert">
            <i class="fa fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    {{-- Glassmorphic Navy Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-head">
            <div class="mob-hero-title-wrap">
                <a href="{{ url('student-complaints') }}" class="mob-back-btn" title="Back to complaints">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="mob-hero-title">Ticket #{{ $complaint->ticket_no }}</h1>
                    <div class="mob-hero-sub">{{ $categoryLabel }} · Submitted as {{ $submittedAs }}</div>
                </div>
            </div>
            <span class="badge {{ $currentStatusMeta['class'] }} rounded-1 px-2 py-1" style="font-size:10px; font-weight:700;">
                <i class="fa {{ $currentStatusMeta['icon'] }} me-1"></i> {{ $currentStatusMeta['label'] }}
            </span>
        </div>

        <div class="mob-hero-badges">
            <span class="mob-hero-badge">
                <i class="fa fa-user"></i> {{ $student->first_name ?? 'Student' }}
            </span>
            <span class="mob-hero-badge">
                <i class="fa fa-building-o"></i> {{ $className }}
            </span>
            <span class="priority-tag {{ $currentPriorityMeta['class'] }}">
                <i class="fa fa-flag"></i> {{ $currentPriorityMeta['label'] }}
            </span>
            <span class="mob-hero-badge">
                <i class="fa fa-comments-o"></i> {{ $replyCount }} {{ \Illuminate\Support\Str::plural('reply', $replyCount) }}
            </span>
        </div>
    </div>

    {{-- Complaint Summary Section --}}
    <div class="mob-section-box">
        <div class="mob-section-head">
            <h2 class="mob-section-title">
                <i class="fa fa-file-text-o"></i> Complaint Overview
            </h2>
            <span class="text-muted" style="font-size: 10px;">
                <i class="fa fa-clock-o me-1"></i> {{ optional($complaint->created_at)->format('d M Y, h:i A') ?: 'N/A' }}
            </span>
        </div>

        <h3 class="mob-complaint-subject">{{ $complaint->subject }}</h3>

        <div class="mob-meta-grid">
            <div class="mob-meta-item">
                <span class="mob-meta-label">Category</span>
                <span class="mob-meta-value">{{ $categoryLabel }}</span>
            </div>
            <div class="mob-meta-item">
                <span class="mob-meta-label">Priority</span>
                <span class="mob-meta-value">{{ ucfirst((string) $complaint->priority) }}</span>
            </div>
            <div class="mob-meta-item">
                <span class="mob-meta-label">Submitted By</span>
                <span class="mob-meta-value">{{ $submittedAs }}</span>
            </div>
            <div class="mob-meta-item">
                <span class="mob-meta-label">Last Updated</span>
                <span class="mob-meta-value">{{ optional($complaint->last_replied_at)->format('d M Y, h:i A') ?: 'No replies' }}</span>
            </div>
        </div>

        <div class="mob-complaint-msg-box">
            {{ $complaint->message ?? 'No details provided.' }}
        </div>

        @if($attachmentReply)
            <a class="mob-attach-pill" href="{{ url('support-complaint-attachment/'.$attachmentReply->id) }}" target="_blank" rel="noopener">
                <i class="fa fa-paperclip"></i>
                <span class="mob-attach-name">{{ $attachmentReply->attachment_name ?: 'Download initial attachment' }}</span>
            </a>
        @endif
    </div>

    {{-- Conversation Thread Section --}}
    <div class="mob-section-box">
        <div class="mob-section-head">
            <h2 class="mob-section-title">
                <i class="fa fa-comments"></i> Conversation Thread
            </h2>
            <span class="badge badge-soft-info rounded-1 px-2 py-0" style="font-size: 9.5px; font-weight: 700;">
                {{ $replyCount }} {{ \Illuminate\Support\Str::plural('message', $replyCount) }}
            </span>
        </div>

        <div class="mob-thread">
            @forelse($complaint->replies as $reply)
                @php
                    $isStudent = $reply->sender_type !== 'admin';
                    $senderLabel = $reply->sender_type === 'admin'
                        ? 'School Administration'
                        : ($complaint->submitted_as === 'parent' ? 'Parent / Guardian' : 'Student');
                    $replyStatusLabel = $reply->status_after
                        ? (\App\Models\SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst(str_replace('_', ' ', (string) $reply->status_after)))
                        : null;
                @endphp
                <div class="mob-bubble-row {{ $isStudent ? 'mine' : 'school' }}">
                    <div class="mob-bubble">
                        <div class="mob-bubble-top">
                            <span>
                                <i class="fa {{ $isStudent ? 'fa-user' : 'fa-graduation-cap' }} me-1"></i> {{ $senderLabel }}
                            </span>
                            @if($replyStatusLabel)
                                <span class="badge bg-light text-dark rounded-1" style="font-size: 8.5px; padding: 2px 5px;">
                                    {{ $replyStatusLabel }}
                                </span>
                            @endif
                        </div>
                        <div class="mob-bubble-msg">{{ $reply->message }}</div>
                        @if($reply->attachment_path)
                            <a class="mob-bubble-file" href="{{ url('support-complaint-attachment/'.$reply->id) }}" target="_blank" rel="noopener">
                                <i class="fa fa-paperclip"></i>
                                <span class="mob-attach-name">{{ $reply->attachment_name ?: 'View Attachment' }}</span>
                            </a>
                        @endif
                        <div class="mob-bubble-footer">
                            <i class="fa fa-clock-o"></i> {{ optional($reply->created_at)->format('d M, h:i A') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="mob-empty-thread">
                    <i class="fa fa-inbox"></i>
                    No responses yet. School staff will review your ticket and reply here.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Reply Form or Closed State --}}
    @if($canReply)
        <div class="mob-reply-box">
            <div class="mob-section-head">
                <h2 class="mob-section-title">
                    <i class="fa fa-reply"></i> Send a Reply
                </h2>
                <span class="text-muted" style="font-size: 9.5px;">Max 10,000 characters</span>
            </div>

            <form method="post" action="{{ url('student-complaints/'.$complaint->id.'/reply') }}" enctype="multipart/form-data" id="mobReplyForm">
                @csrf
                <textarea name="message" class="mob-reply-textarea" id="mobReplyMessage" maxlength="10000" required placeholder="Type your message or provide further details here..."></textarea>

                <div class="mob-upload-drop">
                    <div class="mob-upload-icon"><i class="fa fa-paperclip"></i></div>
                    <input type="file" name="attachment" class="mob-upload-input" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="mob-reply-actions">
                    <a href="{{ url('student-complaints') }}" class="mob-btn mob-btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <button class="mob-btn mob-btn-primary" type="submit" id="mobReplySubmitBtn">
                        <i class="fa fa-send"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mob-locked-box">
            <div class="mob-locked-icon"><i class="fa fa-lock"></i></div>
            <div style="font-weight: 700; color: #1e293b; margin-bottom: 3px;">Ticket {{ $currentStatusMeta['label'] }}</div>
            Replies are disabled for {{ strtolower($currentStatusMeta['label']) }} complaints.
            <div class="mt-3">
                <a href="{{ url('student-complaints') }}" class="mob-btn mob-btn-secondary" style="display: inline-flex;">
                    <i class="fa fa-arrow-left"></i> Back to Complaints
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var replyForm = document.getElementById('mobReplyForm');
    var replyBtn = document.getElementById('mobReplySubmitBtn');

    if (replyForm && replyBtn) {
        replyForm.addEventListener('submit', function () {
            replyBtn.disabled = true;
            replyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
        });
    }
});
</script>
@endsection

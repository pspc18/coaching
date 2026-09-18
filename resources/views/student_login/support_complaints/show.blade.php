@extends('student_login.layout.app')
@section('title', $complaint->ticket_no)
@section('page_title', 'COMPLAINT DETAILS')
@section('page_sub', $complaint->ticket_no)

@section('content')
@php
    $student = $complaint->student;
    $className = optional($student->ClassTypes)->name ?: 'Unassigned';
    $statusLabel = \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst(str_replace('_', ' ', (string) $complaint->status));
    $categoryLabel = \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst(str_replace('_', ' ', (string) $complaint->category));
    $submittedAs = $complaint->submitted_as === 'parent' ? 'Parent / Guardian' : 'Student';
    $replyCount = $complaint->replies->count();
    $attachmentReply = $complaint->replies->firstWhere('attachment_path');
    $statusTone = in_array($complaint->status, ['resolved', 'closed'], true) ? 'resolved' : (in_array($complaint->status, ['awaiting_user'], true) ? 'waiting' : 'open');
    $canReply = !in_array($complaint->status, ['resolved', 'closed'], true);
@endphp

<style>
.sc-page{padding:12px 12px 34px;background:var(--stu-page,#0f1424);color:var(--stu-text,#eef3ff)}
.sc-hero{position:relative;overflow:hidden;border-radius:20px;padding:16px;background:linear-gradient(135deg,#3156d3,#233b75);color:#fff;box-shadow:0 14px 30px rgba(32,61,154,.22)}
.sc-hero:before,.sc-hero:after{content:"";position:absolute;border-radius:50%;border:26px solid rgba(255,255,255,.06)}
.sc-hero:before{width:150px;height:150px;right:-60px;top:-75px}
.sc-hero:after{width:94px;height:94px;left:-52px;bottom:-62px}
.sc-hero-top{position:relative;z-index:1;display:flex;align-items:flex-start;gap:12px}
.sc-hero-icon{width:50px;height:50px;flex:0 0 50px;display:grid;place-items:center;border-radius:15px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.16);font-size:22px}
.sc-hero-copy{min-width:0;flex:1}
.sc-hero-copy span{display:inline-flex;align-items:center;gap:6px;padding:4px 8px;border-radius:999px;background:rgba(255,255,255,.12);font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase}
.sc-hero-copy h1{font-size:20px;line-height:1.25;margin:7px 0 5px;font-weight:780}
.sc-hero-copy p{margin:0;font-size:11px;line-height:1.55;opacity:.82}
.sc-hero-badges{display:flex;flex-wrap:wrap;gap:7px;margin-top:12px}
.sc-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 9px;border-radius:999px;background:rgba(255,255,255,.12);font-size:9px;font-weight:700}
.sc-pill i{font-size:10px}
.sc-pill-status{background:rgba(255,255,255,.18)}
.sc-summary{margin-top:12px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
.sc-card{background:#182337;border:1px solid #29364c;border-radius:18px;box-shadow:0 7px 24px rgba(0,0,0,.22);overflow:hidden}
.sc-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px}
.sc-info{padding:12px 12px 11px;background:#182337;border:1px solid #29364c;border-radius:16px}
.sc-info span{display:block;color:#9da9bd;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px}
.sc-info b{display:block;font-size:12px;line-height:1.35;color:#eef3ff;word-break:break-word}
.sc-section{margin-top:12px;padding:14px}
.sc-section-head{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;margin-bottom:11px}
.sc-section-head h2{font-size:15px;line-height:1.25;margin:0;font-weight:760}
.sc-section-head p{margin:3px 0 0;font-size:10px;color:#9da9bd}
.sc-count{flex:0 0 auto;padding:5px 8px;border-radius:9px;background:var(--stu-primary-soft,#e8eeff);color:#3156d3;font-size:8px;font-weight:800;letter-spacing:.05em}
.sc-body{padding:14px}
.sc-subject{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;padding-bottom:12px;border-bottom:1px solid #29364c}
.sc-subject h2{margin:0;font-size:16px;line-height:1.35;font-weight:780}
.sc-subject .sc-ticket{display:block;margin-bottom:5px;font-size:9px;color:#9da9bd;font-weight:700;letter-spacing:.06em}
.sc-status{flex:0 0 auto;padding:6px 8px;border-radius:10px;font-size:9px;font-weight:800;line-height:1.2;text-align:center}
.sc-status.open,.sc-status.reopened,.sc-status.acknowledged,.sc-status.in_progress{background:#eef2ff;color:#3156d3}
.sc-status.awaiting_user{background:#fff4dc;color:#d78a00}
.sc-status.resolved,.sc-status.closed{background:#e4f8ef;color:#139466}
.sc-meta-row{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.sc-meta{display:inline-flex;align-items:center;gap:5px;padding:6px 8px;border-radius:10px;background:#111a2a;color:#b8c4d8;font-size:9px;font-weight:700}
.sc-meta strong{color:#eef3ff}
.sc-message-box{margin-top:12px;padding:12px;border-radius:16px;background:#111a2a;border:1px solid #29364c}
.sc-message-label{display:flex;align-items:center;gap:6px;font-size:10px;font-weight:800;color:#aab3c6;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em}
.sc-message{font-size:12px;line-height:1.75;color:#eef3ff;white-space:pre-line;word-break:break-word}
.sc-attachment{display:inline-flex;align-items:center;gap:7px;margin-top:10px;padding:8px 10px;border-radius:10px;background:#1a2740;border:1px solid #30405a;color:#9fb6ff;font-size:10px;font-weight:700;text-decoration:none}
.sc-attachment i{font-size:12px}
.sc-attachment small{display:block;color:#7f8ba0;font-weight:600}
.sc-thread{display:flex;flex-direction:column;gap:10px}
.sc-item{display:flex;gap:8px}
.sc-item.mine{justify-content:flex-end}
.sc-bubble{max-width:min(92%, 640px);padding:11px 12px;border-radius:16px;background:#182337;border:1px solid #29364c;box-shadow:0 5px 16px rgba(0,0,0,.12)}
.sc-item.mine .sc-bubble{background:linear-gradient(135deg,#3156d3,#233b75);border-color:#3156d3;color:#fff}
.sc-bubble-top{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:7px}
.sc-sender{font-size:11px;font-weight:800;color:inherit}
.sc-reply-status{font-size:8px;font-weight:800;padding:3px 6px;border-radius:999px;background:#24365f;color:#c9d4ff}
.sc-item.mine .sc-reply-status{background:rgba(255,255,255,.18);color:#fff}
.sc-bubble-msg{font-size:12px;line-height:1.7;white-space:pre-line}
.sc-bubble-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:8px;font-size:9px;color:#9da9bd}
.sc-item.mine .sc-bubble-meta{color:rgba(255,255,255,.8)}
.sc-reply-file{display:inline-flex;align-items:center;gap:6px;margin-top:8px;font-size:10px;color:inherit;text-decoration:none}
.sc-empty{padding:20px 14px;border:1px dashed #30405a;border-radius:16px;background:#111a2a;color:#9da9bd;font-size:11px;text-align:center}
.sc-reply{margin-top:12px;padding:14px;background:#182337;border-color:#29364c}
.sc-reply label{display:block;font-size:10px;font-weight:800;color:#aab3c6;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em}
.sc-reply textarea{width:100%;min-height:120px;border-radius:14px;border:1px solid #30405a;background:#111a2a;color:#eef3ff;padding:12px 12px;font-size:12px;line-height:1.6;resize:vertical;box-shadow:none}
.sc-reply textarea:focus{outline:none;border-color:#657df4;box-shadow:0 0 0 3px rgba(67,97,238,.18)}
.sc-upload{margin-top:10px;padding:11px;border:1px dashed #30405a;border-radius:14px;background:#111a2a;display:flex;align-items:center;gap:10px}
.sc-upload-icon{width:36px;height:36px;flex:0 0 36px;border-radius:11px;display:grid;place-items:center;background:#24365f;color:#9fb6ff}
.sc-upload input{width:100%;font-size:11px;color:#d6def1}
.sc-upload input::file-selector-button{border:0;border-radius:8px;background:#3156d3;color:#fff;padding:6px 10px;margin-right:8px;font-size:10px}
.sc-actions{display:grid;grid-template-columns:1fr 1.2fr;gap:10px;margin-top:12px}
.sc-btn{min-height:42px;display:inline-flex;align-items:center;justify-content:center;gap:7px;border-radius:12px;font-size:11px;font-weight:800;text-decoration:none}
.sc-btn-back{border:1px solid #30405a;background:#111a2a;color:#d6def1}
.sc-btn-send{border:0;background:linear-gradient(135deg,#3156d3,#233b75);color:#fff;box-shadow:0 10px 18px rgba(49,86,211,.18)}
.sc-alert{margin-bottom:12px;border-radius:14px}
.sc-time{color:#7a879b;font-size:9px}
.sc-attach-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 8px;border-radius:999px;background:rgba(49,86,211,.1);color:#3156d3;font-size:9px;font-weight:800}
[data-theme=dark] .sc-page{--stu-page:#0f1424;--stu-surface:#182337;--stu-text:#eef3ff;--stu-muted:#9da9bd;--stu-border:#29364c;--stu-primary-soft:#24365f}
@media(max-width:420px){
    .sc-summary,.sc-info-grid,.sc-actions{grid-template-columns:1fr}
    .sc-hero-copy h1{font-size:18px}
    .sc-subject{flex-direction:column}
    .sc-status{align-self:flex-start}
}
</style>

<section class="sc-page">
    @if(session('message'))
        <div class="alert alert-success py-2 small sc-alert">{{ session('message') }}</div>
    @endif

    <div class="sc-hero">
        <div class="sc-hero-top">
            <div class="sc-hero-icon"><i class="bi bi-chat-square-text"></i></div>
            <div class="sc-hero-copy">
                <span><i class="bi bi-ticket-perforated"></i> {{ $complaint->ticket_no }}</span>
                <h1>{{ $complaint->subject }}</h1>
                <p>{{ $categoryLabel }} complaint submitted as {{ $submittedAs }}. Keep track of updates and replies below.</p>
                <div class="sc-hero-badges">
                    <span class="sc-pill sc-pill-status sc-status {{ $complaint->status }}">{{ $statusLabel }}</span>
                    <span class="sc-pill"><i class="bi bi-grid"></i> {{ $className }}</span>
                    <span class="sc-pill"><i class="bi bi-chat-dots"></i> {{ $replyCount }} {{ \Illuminate\Support\Str::plural('reply', $replyCount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="sc-info-grid">
        <div class="sc-info">
            <span>Student</span>
            <b>{{ trim((string) ($student->first_name ?? '') . ' ' . (string) ($student->last_name ?? '')) ?: 'N/A' }}</b>
        </div>
        <div class="sc-info">
            <span>Class</span>
            <b>{{ $className }}</b>
        </div>
        <div class="sc-info">
            <span>Category</span>
            <b>{{ $categoryLabel }}</b>
        </div>
        <div class="sc-info">
            <span>Priority</span>
            <b>{{ ucfirst((string) $complaint->priority) }}</b>
        </div>
        <div class="sc-info">
            <span>Submitted As</span>
            <b>{{ $submittedAs }}</b>
        </div>
        <div class="sc-info">
            <span>Last Update</span>
            <b>{{ optional($complaint->last_replied_at)->format('d M Y, h:i A') ?: 'No reply yet' }}</b>
        </div>
    </div>

    <div class="sc-card sc-section">
        <div class="sc-section-head">
            <div>
                <h2>Complaint summary</h2>
                <p>Full complaint details and attachments</p>
            </div>
            <span class="sc-count">{{ strtoupper($statusTone) }}</span>
        </div>

        <div class="sc-subject">
            <div>
                <span class="sc-ticket">{{ $complaint->ticket_no }}</span>
                <h2>{{ $complaint->subject }}</h2>
            </div>
            <span class="sc-status {{ $complaint->status }}">{{ $statusLabel }}</span>
        </div>

        <div class="sc-meta-row">
            <span class="sc-meta"><i class="bi bi-folder2"></i> <strong>{{ $categoryLabel }}</strong></span>
            <span class="sc-meta"><i class="bi bi-person"></i> <strong>{{ $submittedAs }}</strong></span>
            <span class="sc-meta"><i class="bi bi-building"></i> <strong>{{ $className }}</strong></span>
            <span class="sc-meta"><i class="bi bi-flag"></i> <strong>{{ ucfirst((string) $complaint->priority) }}</strong></span>
            <span class="sc-meta"><i class="bi bi-clock"></i> <strong>{{ optional($complaint->created_at)->format('d M Y, h:i A') ?: 'N/A' }}</strong></span>
        </div>

        <div class="sc-message-box">
            <div class="sc-message-label"><i class="bi bi-journal-text"></i> Complaint details</div>
            <div class="sc-message">{{ $complaint->message ?? 'No details provided.' }}</div>
            @if($attachmentReply)
                <a class="sc-attachment" href="{{ url('support-complaint-attachment/'.$attachmentReply->id) }}" target="_blank" rel="noopener">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <span>
                        {{ $attachmentReply->attachment_name ?: 'Open attachment' }}
                        <small>Attached file</small>
                    </span>
                </a>
            @endif
        </div>
    </div>

    <div class="sc-card sc-section">
        <div class="sc-section-head">
            <div>
                <h2>Conversation</h2>
                <p>Replies from the school and your follow-ups</p>
            </div>
            <span class="sc-count">{{ $replyCount }} MESSAGES</span>
        </div>

        <div class="sc-thread">
            @forelse($complaint->replies as $reply)
                @php
                    $mine = $reply->sender_type !== 'admin';
                    $senderLabel = $reply->sender_type === 'admin'
                        ? 'School administration'
                        : ucfirst((string) $reply->sender_type);
                    $replyStatusLabel = $reply->status_after
                        ? (\App\Models\SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst(str_replace('_', ' ', (string) $reply->status_after)))
                        : null;
                @endphp
                <div class="sc-item {{ $mine ? 'mine' : '' }}">
                    <div class="sc-bubble">
                        <div class="sc-bubble-top">
                            <span class="sc-sender">{{ $senderLabel }}</span>
                            @if($replyStatusLabel)
                                <span class="sc-reply-status">{{ $replyStatusLabel }}</span>
                            @endif
                        </div>
                        <div class="sc-bubble-msg">{{ $reply->message }}</div>
                        @if($reply->attachment_path)
                            <a class="sc-reply-file" href="{{ url('support-complaint-attachment/'.$reply->id) }}" target="_blank" rel="noopener">
                                <i class="bi bi-paperclip"></i> {{ $reply->attachment_name ?: 'Open attachment' }}
                            </a>
                        @endif
                        <div class="sc-bubble-meta">
                            <span><i class="bi bi-clock"></i> {{ $reply->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="sc-empty">
                    No replies yet. The school team will respond here once the complaint is reviewed.
                </div>
            @endforelse
        </div>
    </div>

    <div class="sc-card sc-reply">
        @if($canReply)
            <form class="ct-reply" method="post" action="{{ url('student-complaints/'.$complaint->id.'/reply') }}" enctype="multipart/form-data">
                @csrf
                <label for="replyMessage">Reply</label>
                <textarea name="message" id="replyMessage" maxlength="10000" required placeholder="Write your reply or provide more details..."></textarea>

                <div class="sc-upload">
                    <div class="sc-upload-icon"><i class="bi bi-paperclip"></i></div>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="sc-actions">
                    <a href="{{ url('student-complaints') }}" class="sc-btn sc-btn-back"><i class="bi bi-arrow-left"></i> Back</a>
                    <button class="sc-btn sc-btn-send" type="submit"><i class="bi bi-send"></i> Send Reply</button>
                </div>
            </form>
        @else
            <div class="sc-empty">
                <div class="mb-2"><i class="bi bi-lock-fill" style="font-size:20px;color:#9fb6ff;"></i></div>
                This complaint is {{ $statusLabel }}. Student replies are disabled for resolved or closed complaints.
                <div class="mt-3">
                    <a href="{{ url('student-complaints') }}" class="sc-btn sc-btn-back"><i class="bi bi-arrow-left"></i> Back to complaints</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

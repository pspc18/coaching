@php
    $getUser = Helper::getUser();
    $studentName = Session::get('first_name') ?: ($getUser->first_name ?? 'Student');
    $className = optional($getUser)->ClassTypes->name ?? 'Student';
    
    $rawType = strtolower(trim((string) ($notification->type ?? 'notice')));
    $typeMeta = [
        'attendance' => ['label' => 'Attendance', 'icon' => 'fa-calendar-check-o', 'badge_class' => 'badge-soft-info'],
        'fees' => ['label' => 'Fees', 'icon' => 'fa-inr', 'badge_class' => 'badge-soft-success'],
        'result' => ['label' => 'Results', 'icon' => 'fa-file-text-o', 'badge_class' => 'badge-soft-purple'],
        'notice' => ['label' => 'Notice', 'icon' => 'fa-bullhorn', 'badge_class' => 'badge-soft-amber'],
        'complaint' => ['label' => 'Complaint', 'icon' => 'fa-comments-o', 'badge_class' => 'badge-soft-slate'],
    ];
    $currentType = $typeMeta[$rawType] ?? ['label' => ucfirst($rawType ?: 'Alert'), 'icon' => 'fa-bell-o', 'badge_class' => 'badge-soft-info'];

    $dateStr = $notification->created_at ? date('d M Y, h:i A', strtotime($notification->created_at)) : 'Recent';
    $attachment = $notification->attachment_path ?? $notification->image ?? null;
@endphp
@extends('student_login.layout.app')

@section('title', 'Notification Details')
@section('page_title', 'NOTIFICATION DETAILS')
@section('page_sub', $studentName . ' · ' . $className)

@section('styles')
<style>
.mob-notif-detail-page {
    padding: 0 0 28px;
    color: #1e293b;
    width: 100%;
    box-sizing: border-box;
}

/* Glassmorphic Navy Hero Card */
.mob-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    border-radius: 4px;
    padding: 16px 16px 14px;
    color: #ffffff;
    box-shadow: 0 6px 20px -2px rgba(0, 24, 51, 0.28);
    position: relative;
    overflow: hidden;
    margin-bottom: 12px;
}

.mob-hero-card::before {
    content: '';
    position: absolute;
    top: -40px;
    right: -40px;
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.18) 0%, rgba(56, 189, 248, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.mob-hero-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    position: relative;
    z-index: 1;
}

.mob-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.mob-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
    border: 1px solid rgba(255, 255, 255, 0.15);
    flex-shrink: 0;
}

.mob-back-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

.mob-hero-title {
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.2px;
    margin: 0;
    line-height: 1.25;
}

.mob-hero-sub {
    font-size: 11.5px;
    color: #94a3b8;
    margin-top: 2px;
}

/* Detail Card */
.mob-detail-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    overflow: hidden;
}

.mob-detail-header {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.mob-detail-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}

.mob-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
}

.badge-soft-info    { background: #e0f2fe; color: #0284c7; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.badge-soft-purple  { background: #ede9fe; color: #6d28d9; }
.badge-soft-amber   { background: #fef3c7; color: #b45309; }
.badge-soft-slate   { background: #f1f5f9; color: #475569; }

.mob-detail-time {
    font-size: 11.5px;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.mob-detail-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    margin: 0;
}

.mob-detail-body {
    padding: 18px 16px;
    font-size: 13.5px;
    line-height: 1.65;
    color: #334155;
    word-break: break-word;
    background: #ffffff;
}

.mob-detail-attachment {
    padding: 14px 16px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

.mob-attach-preview {
    max-width: 100%;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.mob-detail-footer {
    padding: 12px 16px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
}
</style>
@endsection

@section('content')
<div class="mob-notif-detail-page">
    {{-- Hero Card --}}
    <div class="mob-hero-card">
        <div class="mob-hero-head">
            <div class="mob-hero-title-wrap">
                <a href="{{ url('notificationFatchStudent') }}" class="mob-back-btn" title="Back to Notifications">
                    <i class="fa fa-chevron-left"></i>
                </a>
                <div>
                    <h1 class="mob-hero-title">Notification Details</h1>
                    <div class="mob-hero-sub">{{ $dateStr }}</div>
                </div>
            </div>
            <span class="mob-type-badge {{ $currentType['badge_class'] }}">
                <i class="fa {{ $currentType['icon'] }}"></i> {{ $currentType['label'] }}
            </span>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="mob-detail-box">
        <div class="mob-detail-header">
            <div class="mob-detail-meta">
                <span class="mob-detail-time">
                    <i class="fa fa-clock-o"></i> {{ $dateStr }}
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:10.5px; border-radius:3px;">
                    <i class="fa fa-check"></i> Read
                </span>
            </div>
            <h2 class="mob-detail-title">{{ $notification->title ?? 'Untitled Notification' }}</h2>
        </div>

        <div class="mob-detail-body">
            {!! nl2br(e($notification->content ?? 'No content provided.')) !!}
        </div>

        @if(!empty($attachment))
            @php
                $isImg = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', (string) $attachment);
                $attachUrl = Str::startsWith($attachment, ['http://', 'https://', '/']) ? $attachment : asset($attachment);
            @endphp
            <div class="mob-detail-attachment">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <strong style="font-size:12px; color:#475569;">
                        <i class="fa fa-paperclip me-1"></i> Attachment
                    </strong>
                    <a href="{{ $attachUrl }}" target="_blank" download class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:11px; border-radius:3px;">
                        <i class="fa fa-download"></i> Download
                    </a>
                </div>
                @if($isImg)
                    <div class="text-center">
                        <a href="{{ $attachUrl }}" target="_blank">
                            <img src="{{ $attachUrl }}" alt="Attachment" class="mob-attach-preview img-fluid" style="max-height:280px; object-fit:contain;">
                        </a>
                    </div>
                @else
                    <a href="{{ $attachUrl }}" target="_blank" class="d-inline-flex align-items-center gap-2 p-2 border rounded bg-white text-decoration-none text-dark" style="font-size:12px;">
                        <i class="fa fa-file-text-o text-primary" style="font-size:18px;"></i>
                        <span class="text-truncate" style="max-width:240px;">{{ basename($attachment) }}</span>
                    </a>
                @endif
            </div>
        @endif

        <div class="mob-detail-footer">
            <a href="{{ url('notificationFatchStudent') }}" class="btn btn-light btn-sm text-secondary" style="border-radius:3px; font-size:12px;">
                <i class="fa fa-arrow-left me-1"></i> All Notifications
            </a>
            <span class="text-muted" style="font-size:11px;">
                <i class="fa fa-shield"></i> Arise Portal
            </span>
        </div>
    </div>
</div>
@endsection

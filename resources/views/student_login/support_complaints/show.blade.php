@php
    $student = $complaint->student;
    $className = optional($student->ClassTypes)->name ?: 'Student';
    $statusLabel = \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst(str_replace('_', ' ', (string) $complaint->status));
    $categoryLabel = \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst(str_replace('_', ' ', (string) $complaint->category));
    $submittedAs = $complaint->submitted_as === 'parent' ? 'Parent / Guardian' : 'Student';
    $replyCount = $complaint->replies->count();
    $canReply = !in_array($complaint->status, ['resolved', 'closed'], true);

    $categoryIcons = [
        'academic'   => 'fa-graduation-cap',
        'fees'       => 'fa-inr',
        'attendance' => 'fa-calendar-check-o',
        'transport'  => 'fa-bus',
        'facility'   => 'fa-building-o',
        'staff'      => 'fa-user-o',
        'technical'  => 'fa-laptop',
        'other'      => 'fa-comments-o',
    ];
    $catIcon = $categoryIcons[$complaint->category] ?? 'fa-comments-o';

    $statusMeta = [
        'open'          => ['label' => 'Open', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-clock-o'],
        'acknowledged'  => ['label' => 'Acknowledged', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-check'],
        'in_progress'   => ['label' => 'In Progress', 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-spinner fa-spin'],
        'awaiting_user' => ['label' => 'Awaiting Reply', 'badge_class' => 'badge-soft-amber', 'tone' => 'amber', 'icon' => 'fa-reply'],
        'resolved'      => ['label' => 'Resolved', 'badge_class' => 'badge-soft-success', 'tone' => 'green', 'icon' => 'fa-check-circle'],
        'closed'        => ['label' => 'Closed', 'badge_class' => 'badge-soft-slate', 'tone' => 'slate', 'icon' => 'fa-lock'],
        'reopened'      => ['label' => 'Reopened', 'badge_class' => 'badge-soft-purple', 'tone' => 'purple', 'icon' => 'fa-refresh'],
    ];
    $currentStatusMeta = $statusMeta[$complaint->status] ?? ['label' => $statusLabel, 'badge_class' => 'badge-soft-info', 'tone' => 'blue', 'icon' => 'fa-circle-o'];

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
@section('page_title', 'SUPPORT CHAT')
@section('page_sub', '#' . $complaint->ticket_no . ' · ' . $categoryLabel)

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE COMPLAINT SUPPORT CHAT WINDOW
   Strictly aligned with student_login mobile theme typography, buttons & badges
   ========================================================================== */

.mob-complaint-chat-page {
    width: 100%;
    box-sizing: border-box;
    color: #0f172a;
}

/* Full-Height Native Chat Window Shell */
.mob-chat-shell {
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height, 48px) - var(--bottom-nav-height, 52px) - var(--safe-bottom, 0px) - 20px);
    min-height: 460px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 24, 51, 0.12);
    position: relative;
}

/* 1. Sticky Navy Chat Header (Matching .mob-hero-card) */
.mob-chat-header {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    color: #ffffff;
    padding: 9px 10px;
    border-bottom: 1px solid rgba(56, 189, 248, 0.22);
    flex-shrink: 0;
    position: relative;
    z-index: 5;
}

.mob-chat-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.mob-chat-header-left {
    display: flex;
    align-items: center;
    gap: 7px;
    min-width: 0;
    flex: 1;
}

.mob-back-btn {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    text-decoration: none !important;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.mob-back-btn:hover {
    background: rgba(255, 255, 255, 0.22);
}

.mob-chat-avatar {
    width: 28px;
    height: 28px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #ffffff;
    background: #0284c7;
    flex-shrink: 0;
    position: relative;
}
.mob-chat-avatar.tone-blue   { background: #0284c7; }
.mob-chat-avatar.tone-green  { background: #10b981; }
.mob-chat-avatar.tone-amber  { background: #f59e0b; }
.mob-chat-avatar.tone-purple { background: #8b5cf6; }
.mob-chat-avatar.tone-slate  { background: #64748b; }

.mob-chat-online-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #4ade80;
    border: 1.5px solid #001833;
}
.mob-chat-online-dot.closed {
    background: #94a3b8;
}

.mob-chat-title-wrap {
    min-width: 0;
    flex: 1;
}

.mob-chat-subject {
    font-size: 12px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-chat-subrow {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 2px;
    flex-wrap: nowrap;
    overflow: hidden;
}

.mob-ticket-badge {
    font-size: 8.5px;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.16);
    border: 1px solid rgba(56, 189, 248, 0.32);
    padding: 0.5px 5px;
    border-radius: 2px;
    letter-spacing: 0.3px;
    font-family: monospace;
    flex-shrink: 0;
}

.mob-chat-subtext {
    font-size: 9.5px;
    color: #cbd5e1;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mob-chat-header-right {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}

/* Theme Badges Matching index.blade.php */
.mob-status-pill {
    font-size: 8px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    white-space: nowrap;
}
.badge-soft-info    { background: #e0f2fe; color: #0284c7; }
.badge-soft-success { background: #dcfce7; color: #15803d; }
.badge-soft-amber   { background: #fef3c7; color: #b45309; }
.badge-soft-purple  { background: #ede9fe; color: #6d28d9; }
.badge-soft-slate   { background: #f1f5f9; color: #475569; }

.mob-priority-tag {
    font-size: 8px;
    font-weight: 800;
    padding: 1.5px 5px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.priority-low    { background: #f1f5f9; color: #475569; }
.priority-medium { background: #fef3c7; color: #b45309; }
.priority-high   { background: #ffedd5; color: #c2410c; }
.priority-urgent { background: #fee2e2; color: #b91c1c; }

.mob-info-toggle-btn {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.mob-info-toggle-btn.active,
.mob-info-toggle-btn:hover {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}

/* Collapsible Ticket Metadata Drawer inside Header */
.mob-chat-meta-drawer {
    display: none;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-chat-meta-drawer.show {
    display: block;
}
.mob-chat-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 5px;
}
.mob-chat-meta-cell {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 3px;
    padding: 4px 7px;
}
.mob-chat-meta-lbl {
    font-size: 8px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: block;
}
.mob-chat-meta-val {
    font-size: 10px;
    font-weight: 700;
    color: #ffffff;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 2. Pinned Ticket Subject Strip */
.mob-chat-pinned-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-shrink: 0;
    font-size: 10px;
}
.mob-chat-pinned-left {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    flex: 1;
}
.mob-chat-pinned-left i {
    color: #0284c7;
    font-size: 11px;
    flex-shrink: 0;
}
.mob-chat-pinned-text {
    font-size: 10.5px;
    font-weight: 700;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3. Scrollable Chat Conversation Canvas */
.mob-chat-canvas {
    flex: 1;
    overflow-y: auto;
    padding: 10px 9px 12px;
    background-color: #eef2f6;
    background-image: radial-gradient(#cbd5e1 0.75px, transparent 0.75px);
    background-size: 14px 14px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    scroll-behavior: smooth;
}

/* Date Divider Pill */
.mob-chat-date-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 2px 0;
}
.mob-chat-date-pill {
    font-size: 8.5px;
    font-weight: 800;
    color: #475569;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid #cbd5e1;
    padding: 2px 8px;
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

/* Chat Message Row */
.mob-chat-row {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    width: 100%;
}
.mob-chat-row.mine {
    justify-content: flex-end;
}
.mob-chat-row.school {
    justify-content: flex-start;
}

.mob-msg-avatar {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 800;
    flex-shrink: 0;
}
.mob-chat-row.school .mob-msg-avatar {
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.3);
}
.mob-chat-row.mine .mob-msg-avatar {
    background: #002C54;
    color: #38bdf8;
    border: 1px solid rgba(56, 189, 248, 0.3);
}

/* Chat Bubble */
.mob-chat-bubble {
    max-width: 82%;
    padding: 7px 9px;
    position: relative;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    word-break: break-word;
}

.mob-chat-row.mine .mob-chat-bubble {
    background: linear-gradient(135deg, #001f3f 0%, #002C54 100%);
    border: 1px solid #001833;
    border-radius: 6px 6px 1px 6px;
    color: #ffffff;
}

.mob-chat-row.school .mob-chat-bubble {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-left: 3px solid #0284c7;
    border-radius: 6px 6px 6px 1px;
    color: #0f172a;
}

.mob-bubble-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-bottom: 3px;
}

.mob-bubble-sender {
    font-size: 9.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mob-chat-row.mine .mob-bubble-sender {
    color: #38bdf8;
}
.mob-chat-row.school .mob-bubble-sender {
    color: #0284c7;
}

.mob-bubble-tag {
    font-size: 7.5px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 2px;
    text-transform: uppercase;
    letter-spacing: 0.25px;
}
.mob-chat-row.mine .mob-bubble-tag {
    background: rgba(56, 189, 248, 0.16);
    color: #7dd3fc;
    border: 1px solid rgba(56, 189, 248, 0.28);
}
.mob-chat-row.school .mob-bubble-tag {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.mob-bubble-text {
    font-size: 11.5px;
    line-height: 1.5;
    white-space: pre-line;
    word-break: break-word;
}
.mob-chat-row.mine .mob-bubble-text {
    color: #f8fafc;
}
.mob-chat-row.school .mob-bubble-text {
    color: #334155;
}

/* Attachment Pill inside Chat Bubble */
.mob-bubble-attachment {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    text-decoration: none !important;
    max-width: 100%;
    transition: all 0.15s ease;
}
.mob-chat-row.mine .mob-bubble-attachment {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.22);
    color: #ffffff !important;
}
.mob-chat-row.mine .mob-bubble-attachment:hover {
    background: rgba(255, 255, 255, 0.2);
}
.mob-chat-row.school .mob-bubble-attachment {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #0284c7 !important;
}
.mob-chat-row.school .mob-bubble-attachment:hover {
    background: #dbeafe;
}

.mob-attach-filename {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.mob-bubble-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 4px;
    margin-top: 4px;
    font-size: 8.5px;
    font-weight: 600;
    line-height: 1;
}
.mob-chat-row.mine .mob-bubble-meta {
    color: #94a3b8;
}
.mob-chat-row.school .mob-bubble-meta {
    color: #64748b;
}

/* Empty Chat State */
.mob-chat-empty {
    margin: auto;
    text-align: center;
    padding: 20px 14px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    max-width: 280px;
}
.mob-chat-empty-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    margin-bottom: 6px;
}
.mob-chat-empty-title {
    font-size: 12px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 2px;
}
.mob-chat-empty-sub {
    font-size: 10.5px;
    color: #64748b;
    line-height: 1.4;
}

/* 4. Sticky Bottom Chat Composer Bar */
.mob-chat-composer {
    background: #ffffff;
    border-top: 1px solid #cbd5e1;
    padding: 7px 8px;
    flex-shrink: 0;
    z-index: 5;
}

/* Selected Attachment Preview Chip above Input */
.mob-composer-file-chip {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 3px;
    padding: 3px 8px;
    margin-bottom: 6px;
    font-size: 10px;
    font-weight: 700;
    color: #0284c7;
}
.mob-composer-file-chip.show {
    display: flex;
}
.mob-composer-file-name {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex: 1;
}
.mob-composer-file-remove {
    background: none;
    border: none;
    color: #dc2626;
    font-size: 11px;
    cursor: pointer;
    padding: 0 2px;
    line-height: 1;
}

.mob-composer-row {
    display: flex;
    align-items: flex-end;
    gap: 6px;
}

.mob-attach-btn {
    width: 32px;
    height: 32px;
    border-radius: 3px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
    margin: 0;
}
.mob-attach-btn:hover {
    background: #eff6ff;
    border-color: #38bdf8;
}

.mob-composer-input {
    flex: 1;
    height: 32px;
    min-height: 32px;
    max-height: 84px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    padding: 6px 9px;
    font-size: 11.5px;
    font-weight: 500;
    line-height: 1.4;
    resize: none;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.15s ease, background 0.15s ease;
}
.mob-composer-input:focus {
    background: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.12);
}

.mob-send-btn {
    height: 32px;
    padding: 0 12px;
    border-radius: 3px;
    background: #002C54;
    border: 1px solid #002C54;
    color: #ffffff;
    font-size: 10.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.22);
}
.mob-send-btn:hover {
    background: #0284c7;
    border-color: #0284c7;
}
.mob-send-btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Locked Footer Bar (Resolved / Closed) */
.mob-chat-locked-bar {
    background: #f8fafc;
    border-top: 1px solid #cbd5e1;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    flex-shrink: 0;
}
.mob-chat-locked-text {
    font-size: 10.5px;
    font-weight: 700;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-chat-locked-text i {
    color: #0284c7;
    font-size: 12px;
}
.mob-btn-back-compact {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 9px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #002C54 !important;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

/* Flash Banner */
.mob-chat-alert {
    padding: 6px 10px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-chat-alert.success {
    background: #dcfce7;
    border: 1px solid #86efac;
    color: #15803d;
}
.mob-chat-alert.error {
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
}

/* Dark Mode Support */
[data-theme="dark"] .mob-chat-shell {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="dark"] .mob-chat-pinned-bar {
    background: #0f172a;
    border-bottom-color: #334155;
}
[data-theme="dark"] .mob-chat-pinned-text {
    color: #f1f5f9;
}
[data-theme="dark"] .mob-chat-canvas {
    background-color: #0f172a;
    background-image: radial-gradient(#334155 0.75px, transparent 0.75px);
}
[data-theme="dark"] .mob-chat-date-pill {
    background: #1e293b;
    border-color: #334155;
    color: #cbd5e1;
}
[data-theme="dark"] .mob-chat-row.school .mob-chat-bubble {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
}
[data-theme="dark"] .mob-chat-row.school .mob-bubble-text {
    color: #e2e8f0;
}
[data-theme="dark"] .mob-chat-composer,
[data-theme="dark"] .mob-chat-locked-bar {
    background: #1e293b;
    border-top-color: #334155;
}
[data-theme="dark"] .mob-attach-btn,
[data-theme="dark"] .mob-composer-input {
    background: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
</style>
@endsection

@section('content')
<div class="mob-complaint-chat-page">

    @if(session('message'))
        <div class="mob-chat-alert success" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('message') }}
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="mob-chat-alert error" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') ?: $errors->first() }}
        </div>
    @endif

    <div class="mob-chat-shell">

        {{-- 1. Sticky Top Chat Header --}}
        <div class="mob-chat-header">
            <div class="mob-chat-header-top">
                <div class="mob-chat-header-left">
                    <a href="{{ url('student-complaints') }}" class="mob-back-btn" title="Back to Complaints">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                    <div class="mob-chat-avatar tone-{{ $currentStatusMeta['tone'] }}">
                        <i class="fa {{ $catIcon }}"></i>
                        <span class="mob-chat-online-dot {{ $canReply ? '' : 'closed' }}"></span>
                    </div>
                    <div class="mob-chat-title-wrap">
                        <h1 class="mob-chat-subject" title="{{ $complaint->subject }}">{{ $complaint->subject }}</h1>
                        <div class="mob-chat-subrow">
                            <span class="mob-ticket-badge">#{{ $complaint->ticket_no }}</span>
                            <span class="mob-chat-subtext">{{ $categoryLabel }} · {{ $submittedAs }}</span>
                        </div>
                    </div>
                </div>

                <div class="mob-chat-header-right">
                    <span class="mob-status-pill {{ $currentStatusMeta['badge_class'] }}">
                        <i class="fa {{ $currentStatusMeta['icon'] }}"></i> {{ $currentStatusMeta['label'] }}
                    </span>
                    <button type="button" class="mob-info-toggle-btn" id="btnToggleTicketMeta" title="Ticket Info">
                        <i class="fa fa-info"></i>
                    </button>
                </div>
            </div>

            {{-- Collapsible Ticket Details Drawer --}}
            <div class="mob-chat-meta-drawer" id="ticketMetaDrawer">
                <div class="mob-chat-meta-grid">
                    <div class="mob-chat-meta-cell">
                        <span class="mob-chat-meta-lbl">Student</span>
                        <span class="mob-chat-meta-val">{{ $student->first_name ?? 'Student' }} ({{ $className }})</span>
                    </div>
                    <div class="mob-chat-meta-cell">
                        <span class="mob-chat-meta-lbl">Priority & Replies</span>
                        <span class="mob-chat-meta-val">{{ $currentPriorityMeta['label'] }} · {{ $replyCount }} {{ \Illuminate\Support\Str::plural('msg', $replyCount) }}</span>
                    </div>
                    <div class="mob-chat-meta-cell">
                        <span class="mob-chat-meta-lbl">Created On</span>
                        <span class="mob-chat-meta-val">{{ optional($complaint->created_at)->format('d M Y, h:i A') ?: 'N/A' }}</span>
                    </div>
                    <div class="mob-chat-meta-cell">
                        <span class="mob-chat-meta-lbl">Last Activity</span>
                        <span class="mob-chat-meta-val">{{ optional($complaint->last_replied_at)->format('d M Y, h:i A') ?: 'No replies' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Pinned Ticket Context Bar --}}
        <div class="mob-chat-pinned-bar">
            <div class="mob-chat-pinned-left">
                <i class="fa fa-thumb-tack"></i>
                <span class="mob-chat-pinned-text">{{ $complaint->subject }}</span>
            </div>
            <span class="mob-priority-tag {{ $currentPriorityMeta['class'] }}">
                <i class="fa fa-flag"></i> {{ $currentPriorityMeta['label'] }}
            </span>
        </div>

        {{-- 3. Scrollable Chat Conversation Canvas --}}
        <div class="mob-chat-canvas" id="mobChatCanvas">
            @php
                $lastDateKey = null;
            @endphp

            @forelse($complaint->replies as $reply)
                @php
                    $replyDate = $reply->created_at ? $reply->created_at->format('Y-m-d') : null;
                    if ($replyDate) {
                        if ($replyDate === now()->format('Y-m-d')) {
                            $dateHeading = 'Today';
                        } elseif ($replyDate === now()->subDay()->format('Y-m-d')) {
                            $dateHeading = 'Yesterday';
                        } else {
                            $dateHeading = $reply->created_at->format('d M Y');
                        }
                    } else {
                        $dateHeading = 'Conversation';
                    }

                    $isStudent = $reply->sender_type !== 'admin';
                    $senderLabel = $reply->sender_type === 'admin'
                        ? 'School Support'
                        : ('You · ' . ($complaint->submitted_as === 'parent' ? 'Parent' : 'Student'));
                    $replyStatusLabel = $reply->status_after
                        ? (\App\Models\SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst(str_replace('_', ' ', (string) $reply->status_after)))
                        : null;
                @endphp

                @if($replyDate !== $lastDateKey)
                    <div class="mob-chat-date-divider">
                        <span class="mob-chat-date-pill">{{ $dateHeading }}</span>
                    </div>
                    @php $lastDateKey = $replyDate; @endphp
                @endif

                <div class="mob-chat-row {{ $isStudent ? 'mine' : 'school' }}">
                    @if(!$isStudent)
                        <div class="mob-msg-avatar" title="School Administration">
                            <i class="fa fa-headphones"></i>
                        </div>
                    @endif

                    <div class="mob-chat-bubble">
                        <div class="mob-bubble-head">
                            <span class="mob-bubble-sender">
                                <i class="fa {{ $isStudent ? 'fa-user-circle-o' : 'fa-shield' }}"></i>
                                {{ $senderLabel }}
                            </span>
                            @if($loop->first && $isStudent)
                                <span class="mob-bubble-tag">Initial Ticket</span>
                            @elseif($replyStatusLabel && !$isStudent)
                                <span class="mob-bubble-tag">{{ $replyStatusLabel }}</span>
                            @endif
                        </div>

                        <div class="mob-bubble-text">{{ $reply->message }}</div>

                        @if($reply->attachment_path)
                            <div>
                                <a class="mob-bubble-attachment" href="{{ url('support-complaint-attachment/'.$reply->id) }}" target="_blank" rel="noopener">
                                    <i class="fa fa-paperclip"></i>
                                    <span class="mob-attach-filename">{{ $reply->attachment_name ?: 'View Attachment' }}</span>
                                    <i class="fa fa-external-link" style="font-size: 8.5px; opacity: 0.8;"></i>
                                </a>
                            </div>
                        @endif

                        <div class="mob-bubble-meta">
                            <span>{{ optional($reply->created_at)->format('h:i A') }}</span>
                            @if($isStudent)
                                <i class="fa fa-check-circle" style="color: #38bdf8; font-size: 9px;"></i>
                            @endif
                        </div>
                    </div>

                    @if($isStudent)
                        <div class="mob-msg-avatar" title="{{ $submittedAs }}">
                            <i class="fa fa-user"></i>
                        </div>
                    @endif
                </div>
            @empty
                <div class="mob-chat-empty">
                    <div class="mob-chat-empty-icon">
                        <i class="fa fa-comments-o"></i>
                    </div>
                    <div class="mob-chat-empty-title">No Messages Yet</div>
                    <div class="mob-chat-empty-sub">
                        School support team will review your ticket and respond here shortly.
                    </div>
                </div>
            @endforelse
        </div>

        {{-- 4. Sticky Bottom Chat Composer or Locked Notice --}}
        @if($canReply)
            <div class="mob-chat-composer">
                <form method="post" action="{{ url('student-complaints/'.$complaint->id.'/reply') }}" enctype="multipart/form-data" id="mobReplyForm">
                    @csrf

                    {{-- Selected File Preview Chip --}}
                    <div class="mob-composer-file-chip" id="mobSelectedFileChip">
                        <i class="fa fa-file-o"></i>
                        <span class="mob-composer-file-name" id="mobSelectedFileName"></span>
                        <button type="button" class="mob-composer-file-remove" id="btnRemoveAttachment" title="Remove attachment">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <input type="file" name="attachment" id="mobReplyAttachment" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">

                    <div class="mob-composer-row">
                        <label for="mobReplyAttachment" class="mob-attach-btn" title="Attach Image or PDF (Max 10MB)">
                            <i class="fa fa-paperclip"></i>
                        </label>

                        <textarea name="message"
                                  class="mob-composer-input"
                                  id="mobReplyMessage"
                                  rows="1"
                                  maxlength="10000"
                                  required
                                  placeholder="Type a message..."></textarea>

                        <button class="mob-send-btn" type="submit" id="mobReplySubmitBtn">
                            <i class="fa fa-paper-plane"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="mob-chat-locked-bar">
                <div class="mob-chat-locked-text">
                    <i class="fa fa-lock"></i>
                    <span>Ticket is {{ strtolower($currentStatusMeta['label']) }} · Replies are closed</span>
                </div>
                <a href="{{ url('student-complaints') }}" class="mob-btn-back-compact">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var chatCanvas = document.getElementById('mobChatCanvas');
    var btnToggleMeta = document.getElementById('btnToggleTicketMeta');
    var metaDrawer = document.getElementById('ticketMetaDrawer');
    var replyForm = document.getElementById('mobReplyForm');
    var replyInput = document.getElementById('mobReplyMessage');
    var replyBtn = document.getElementById('mobReplySubmitBtn');
    var fileInput = document.getElementById('mobReplyAttachment');
    var fileChip = document.getElementById('mobSelectedFileChip');
    var fileNameSpan = document.getElementById('mobSelectedFileName');
    var btnRemoveFile = document.getElementById('btnRemoveAttachment');

    // Auto-scroll chat window to the latest message at the bottom
    if (chatCanvas) {
        chatCanvas.scrollTop = chatCanvas.scrollHeight;
    }

    // Toggle collapsible ticket info drawer in chat header
    if (btnToggleMeta && metaDrawer) {
        btnToggleMeta.addEventListener('click', function () {
            metaDrawer.classList.toggle('show');
            btnToggleMeta.classList.toggle('active');
        });
    }

    // Auto-resize chat textarea as user types
    if (replyInput) {
        replyInput.addEventListener('input', function () {
            this.style.height = '32px';
            var nextHeight = Math.min(this.scrollHeight, 84);
            this.style.height = nextHeight + 'px';
        });
    }

    // Show selected attachment chip above composer bar
    if (fileInput && fileChip && fileNameSpan) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                fileChip.classList.add('show');
            } else {
                fileChip.classList.remove('show');
                fileNameSpan.textContent = '';
            }
        });
    }

    if (btnRemoveFile && fileInput && fileChip) {
        btnRemoveFile.addEventListener('click', function () {
            fileInput.value = '';
            fileChip.classList.remove('show');
            fileNameSpan.textContent = '';
        });
    }

    // Prevent double submission
    if (replyForm && replyBtn) {
        replyForm.addEventListener('submit', function () {
            replyBtn.disabled = true;
            replyBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        });
    }
});
</script>
@endsection

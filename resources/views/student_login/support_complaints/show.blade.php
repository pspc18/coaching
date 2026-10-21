@php
    $student = $complaint->student;
    $studentFullName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'Student';
    $className = optional($student->ClassTypes)->name ?: 'Student';
    $statusLabel = \App\Models\SupportComplaint::STATUSES[$complaint->status] ?? ucfirst(str_replace('_', ' ', (string) $complaint->status));
    $categoryLabel = \App\Models\SupportComplaint::CATEGORIES[$complaint->category] ?? ucfirst(str_replace('_', ' ', (string) $complaint->category));
    $submittedAs = $complaint->submitted_as === 'parent' ? 'Parent' : 'Student';
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
   ARISE ERP - NATIVE MOBILE COMPLAINT SUPPORT CHAT WINDOW (MOBILE-ONLY)
   Strictly aligned with student_login mobile theme cards, borders & badges
   ========================================================================== */

.mob-complaint-chat-page {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    color: #0f172a;
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height, 48px) - var(--bottom-nav-height, 52px) - var(--safe-bottom, 0px) - 20px);
    min-height: 460px;
    overflow: hidden;
}

/* 1. Standalone Glassmorphic Navy Hero Header Card (Matches index.blade.php .mob-hero-card) */
.mob-chat-hero-card {
    background: linear-gradient(135deg, #001833 0%, #002244 55%, #002C54 100%);
    border-radius: 4px;
    padding: 9px 10px;
    color: #ffffff;
    box-shadow: 0 4px 14px -2px rgba(0, 24, 51, 0.25);
    border: 1px solid rgba(56, 189, 248, 0.16);
    position: relative;
    overflow: hidden;
    margin-bottom: 6px;
    flex-shrink: 0;
}

.mob-chat-hero-card::before {
    content: '';
    position: absolute;
    top: -28px;
    right: -28px;
    width: 96px;
    height: 96px;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(56, 189, 248, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}

.mob-chat-header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    position: relative;
    z-index: 1;
}

.mob-chat-header-left {
    display: flex;
    align-items: center;
    gap: 7px;
    min-width: 0;
    flex: 1;
}

.mob-back-btn {
    width: 26px;
    height: 26px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.16);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    text-decoration: none !important;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.mob-back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
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
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 0.5px 5px;
    border-radius: 2px;
    letter-spacing: 0.25px;
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
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.priority-low    { background: #f1f5f9; color: #475569; }
.priority-medium { background: #fef3c7; color: #b45309; }
.priority-high   { background: #ffedd5; color: #c2410c; }
.priority-urgent { background: #fee2e2; color: #b91c1c; }

.mob-info-toggle-btn {
    width: 24px;
    height: 24px;
    border-radius: 3px;
    border: 1px solid rgba(56, 189, 248, 0.35);
    background: rgba(56, 189, 248, 0.14);
    color: #38bdf8;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10.5px;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
}
.mob-info-toggle-btn.active,
.mob-info-toggle-btn:hover {
    background: #0284c7;
    border-color: #38bdf8;
    color: #ffffff;
}

/* 2. Main White Mobile Chat Card (Matches .mob-filter-box & .mob-complaint-card) */
.mob-chat-card {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 24, 51, 0.06);
    width: 100%;
}

/* Pinned Ticket Context Strip */
.mob-chat-pinned-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-shrink: 0;
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
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-chat-pinned-right {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
}

/* Clean Mobile Ticket Information Sheet (Light Theme Matched, Zero Overflow) */
.mob-ticket-info-panel {
    display: none;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 9px;
    flex-shrink: 0;
    width: 100%;
    box-sizing: border-box;
}
.mob-ticket-info-panel.show {
    display: block;
}

/* Student Banner Row inside Info Panel (Full Width so long names/classes never clip) */
.mob-info-student-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 3px solid #0284c7;
    border-radius: 3px;
    padding: 5px 8px;
    margin-bottom: 5px;
    min-width: 0;
}
.mob-info-stu-left {
    display: flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
    flex: 1;
}
.mob-info-stu-icon {
    width: 22px;
    height: 22px;
    border-radius: 3px;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    flex-shrink: 0;
}
.mob-info-stu-text {
    min-width: 0;
    flex: 1;
}
.mob-info-stu-lbl {
    font-size: 7.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: block;
    line-height: 1.1;
}
.mob-info-stu-name {
    font-size: 10.5px;
    font-weight: 800;
    color: #0f172a;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-info-class-pill {
    font-size: 8.5px;
    font-weight: 700;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #dbeafe;
    padding: 1.5px 6px;
    border-radius: 2px;
    white-space: nowrap;
    max-width: 135px;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 0;
}

/* 2-Column Mobile Grid with Strict minmax(0, 1fr) to Prevent Right-Side Overflow */
.mob-info-grid-2col {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 5px;
    width: 100%;
}
.mob-info-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 4px 7px;
    min-width: 0;
    overflow: hidden;
}
.mob-info-box-lbl {
    font-size: 7.5px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 3.5px;
    margin-bottom: 1px;
    white-space: nowrap;
}
.mob-info-box-lbl i {
    color: #0284c7;
    font-size: 8.5px;
}
.mob-info-box-val {
    font-size: 9.5px;
    font-weight: 700;
    color: #1e293b;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 3. Scrollable Chat Conversation Canvas */
.mob-chat-canvas {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 10px 9px 12px;
    background-color: #f1f5f9;
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
    font-size: 8px;
    font-weight: 800;
    color: #475569;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 2px 9px;
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 0.35px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

/* Chat Message Row */
.mob-chat-row {
    display: flex;
    align-items: flex-end;
    gap: 6px;
    width: 100%;
    min-width: 0;
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
    box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25);
}
.mob-chat-row.mine .mob-msg-avatar {
    background: #002C54;
    color: #38bdf8;
    border: 1px solid rgba(56, 189, 248, 0.25);
}

/* Chat Bubble */
.mob-chat-bubble {
    max-width: calc(100% - 34px);
    width: auto;
    min-width: 160px;
    padding: 7px 9px;
    position: relative;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    word-break: break-word;
    overflow: hidden;
}

.mob-chat-row.mine .mob-chat-bubble {
    background: linear-gradient(135deg, #001f3f 0%, #002C54 100%);
    border: 1px solid rgba(56, 189, 248, 0.18);
    border-radius: 6px 6px 1px 6px;
    color: #ffffff;
}

.mob-chat-row.school .mob-chat-bubble {
    background: #ffffff;
    border: 1px solid #e2e8f0;
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
    flex-shrink: 0;
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
    line-height: 1.45;
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
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 6px;
    padding: 4px 7px;
    border-radius: 3px;
    font-size: 9.5px;
    font-weight: 700;
    text-decoration: none !important;
    max-width: 100%;
    transition: all 0.15s ease;
}
.mob-chat-row.mine .mob-bubble-attachment {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #ffffff !important;
}
.mob-chat-row.mine .mob-bubble-attachment:hover {
    background: rgba(255, 255, 255, 0.18);
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
    flex: 1;
    min-width: 0;
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
    padding: 18px 14px;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    max-width: 260px;
}
.mob-chat-empty-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    margin-bottom: 5px;
}
.mob-chat-empty-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 2px;
}
.mob-chat-empty-sub {
    font-size: 10px;
    color: #64748b;
    line-height: 1.4;
}

/* 4. Sticky Bottom Chat Composer Bar */
.mob-chat-composer {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    padding: 7px 8px;
    flex-shrink: 0;
    z-index: 5;
}

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
    max-height: 80px;
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
    border-top: 1px solid #e2e8f0;
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
    flex-shrink: 0;
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

/* WhatsApp-Style Real-Time Bubble States & Animations */
@keyframes mobBubblePopIn {
    0% {
        opacity: 0;
        transform: translateY(6px) scale(0.98);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
.mob-chat-row.is-new .mob-chat-bubble {
    animation: mobBubblePopIn 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}
.mob-chat-row.is-sending .mob-chat-bubble {
    opacity: 0.92;
}
.mob-chat-row.send-failed .mob-chat-bubble {
    border-color: rgba(248, 113, 113, 0.55) !important;
}
.mob-tick-icon {
    font-size: 9px;
    transition: color 0.2s ease, transform 0.2s ease;
}
.mob-tick-pending {
    color: #94a3b8;
}
.mob-tick-sent {
    color: #38bdf8;
}
.mob-tick-failed {
    color: #f87171;
}
.mob-composer-inline-error {
    display: none;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #b91c1c;
    border-radius: 3px;
    padding: 4px 8px;
    margin-bottom: 6px;
    font-size: 10px;
    font-weight: 700;
}
.mob-composer-inline-error.show {
    display: flex;
}

/* Dark Mode Support */
[data-theme="dark"] .mob-chat-card {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="dark"] .mob-chat-pinned-bar {
    background: #1e293b;
    border-bottom-color: #334155;
}
[data-theme="dark"] .mob-chat-pinned-text {
    color: #f1f5f9;
}
[data-theme="dark"] .mob-ticket-info-panel {
    background: #0f172a;
    border-bottom-color: #334155;
}
[data-theme="dark"] .mob-info-student-strip,
[data-theme="dark"] .mob-info-box {
    background: #1e293b;
    border-color: #334155;
}
[data-theme="dark"] .mob-info-stu-name,
[data-theme="dark"] .mob-info-box-val {
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

    @if(session('error') || $errors->any())
        <div class="mob-chat-alert error" role="alert">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') ?: $errors->first() }}
        </div>
    @endif

    {{-- 1. Standalone Glassmorphic Navy Hero Card (Matches student_login theme without white border) --}}
    <div class="mob-chat-hero-card">
        <div class="mob-chat-header-top">
            <div class="mob-chat-header-left">
                <a href="{{ url('student-complaints') }}" class="mob-back-btn" title="Back to Complaints">
                    <i class="fa fa-chevron-left"></i>
                </a>
                <div class="mob-chat-avatar tone-{{ $currentStatusMeta['tone'] }}" id="mobHeaderAvatar">
                    <i class="fa {{ $catIcon }}"></i>
                    <span class="mob-chat-online-dot {{ $canReply ? '' : 'closed' }}" id="mobHeaderOnlineDot"></span>
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
                <span class="mob-status-pill {{ $currentStatusMeta['badge_class'] }}" id="mobHeaderStatusPill">
                    <i class="fa {{ $currentStatusMeta['icon'] }}"></i>
                    <span id="mobHeaderStatusText">{{ $currentStatusMeta['label'] }}</span>
                </span>
                <button type="button" class="mob-info-toggle-btn" id="btnToggleTicketMeta" title="Toggle Ticket Information">
                    <i class="fa fa-info"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Main Mobile Chat Card --}}
    <div class="mob-chat-card">

        {{-- Pinned Subject & Priority Bar --}}
        <div class="mob-chat-pinned-bar">
            <div class="mob-chat-pinned-left">
                <i class="fa fa-thumb-tack"></i>
                <span class="mob-chat-pinned-text">{{ $complaint->subject }}</span>
            </div>
            <div class="mob-chat-pinned-right">
                <span class="mob-priority-tag {{ $currentPriorityMeta['class'] }}">
                    <i class="fa fa-flag"></i> {{ $currentPriorityMeta['label'] }}
                </span>
            </div>
        </div>

        {{-- Collapsible Mobile Ticket Information Panel (Clean Light Card Theme, Zero Clipping) --}}
        <div class="mob-ticket-info-panel" id="ticketMetaDrawer">
            {{-- Full-width Student & Class Strip --}}
            <div class="mob-info-student-strip">
                <div class="mob-info-stu-left">
                    <div class="mob-info-stu-icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <div class="mob-info-stu-text">
                        <span class="mob-info-stu-lbl">Student ({{ $submittedAs }})</span>
                        <span class="mob-info-stu-name" title="{{ $studentFullName }}">{{ $studentFullName }}</span>
                    </div>
                </div>
                <span class="mob-info-class-pill" title="{{ $className }}">{{ $className }}</span>
            </div>

            {{-- 2x2 Balanced Mobile Info Grid (minmax(0, 1fr) prevents right-side overflow) --}}
            <div class="mob-info-grid-2col">
                <div class="mob-info-box">
                    <span class="mob-info-box-lbl"><i class="fa fa-calendar-plus-o"></i> Created On</span>
                    <span class="mob-info-box-val">{{ optional($complaint->created_at)->format('d M Y, h:i A') ?: 'N/A' }}</span>
                </div>
                <div class="mob-info-box">
                    <span class="mob-info-box-lbl"><i class="fa fa-clock-o"></i> Last Activity</span>
                    <span class="mob-info-box-val" id="mobInfoLastActivity">{{ optional($complaint->last_replied_at)->format('d M Y, h:i A') ?: 'No replies' }}</span>
                </div>
                <div class="mob-info-box">
                    <span class="mob-info-box-lbl"><i class="fa fa-folder-open-o"></i> Category</span>
                    <span class="mob-info-box-val">{{ $categoryLabel }}</span>
                </div>
                <div class="mob-info-box">
                    <span class="mob-info-box-lbl"><i class="fa fa-comments-o"></i> Priority & Msgs</span>
                    <span class="mob-info-box-val" data-priority-label="{{ $currentPriorityMeta['label'] }}" id="mobInfoReplyCount">{{ $currentPriorityMeta['label'] }} · {{ $replyCount }} {{ \Illuminate\Support\Str::plural('msg', $replyCount) }}</span>
                </div>
            </div>
        </div>

        {{-- 3. Scrollable Chat Conversation Canvas --}}
        @php
            $lastDateKey = null;
            $initialLastReplyId = (int) optional($complaint->replies->last())->id;
        @endphp
        <div class="mob-chat-canvas"
             id="mobChatCanvas"
             data-poll-url="{{ url('student-complaints/'.$complaint->id) }}"
             data-submitted-as="{{ $submittedAs }}"
             data-last-reply-id="{{ $initialLastReplyId }}">

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
                        : ('You · ' . $submittedAs);
                    $replyStatusLabel = $reply->status_after
                        ? (\App\Models\SupportComplaint::STATUSES[$reply->status_after] ?? ucfirst(str_replace('_', ' ', (string) $reply->status_after)))
                        : null;
                @endphp

                @if($replyDate !== $lastDateKey)
                    <div class="mob-chat-date-divider" data-date-key="{{ $replyDate }}">
                        <span class="mob-chat-date-pill">{{ $dateHeading }}</span>
                    </div>
                    @php $lastDateKey = $replyDate; @endphp
                @endif

                <div class="mob-chat-row {{ $isStudent ? 'mine' : 'school' }}" data-reply-id="{{ $reply->id }}">
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
                            <div class="mob-bubble-attach-wrap">
                                <a class="mob-bubble-attachment" href="{{ url('support-complaint-attachment/'.$reply->id) }}" target="_blank" rel="noopener">
                                    <i class="fa fa-paperclip"></i>
                                    <span class="mob-attach-filename">{{ $reply->attachment_name ?: 'View Attachment' }}</span>
                                    <i class="fa fa-external-link" style="font-size: 8.5px; opacity: 0.8;"></i>
                                </a>
                            </div>
                        @endif

                        <div class="mob-bubble-meta">
                            <span class="mob-bubble-time">{{ optional($reply->created_at)->format('h:i A') }}</span>
                            @if($isStudent)
                                <i class="fa fa-check-circle mob-tick-icon mob-tick-sent" title="Sent"></i>
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
                <div class="mob-chat-empty" id="mobChatEmptyState">
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
        <div class="mob-chat-composer" id="mobChatComposerWrap" style="{{ $canReply ? '' : 'display: none;' }}">
            <form method="post" action="{{ url('student-complaints/'.$complaint->id.'/reply') }}" enctype="multipart/form-data" id="mobReplyForm" novalidate>
                @csrf

                {{-- Inline Error Toast inside Composer --}}
                <div class="mob-composer-inline-error" id="mobComposerError">
                    <span id="mobComposerErrorText">Unable to send message.</span>
                    <button type="button" class="mob-composer-file-remove" id="btnDismissComposerError" title="Dismiss">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

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
                              placeholder="Type a message..."></textarea>

                    <button class="mob-send-btn" type="submit" id="mobReplySubmitBtn">
                        <i class="fa fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>
        </div>

        <div class="mob-chat-locked-bar" id="mobChatLockedWrap" style="{{ $canReply ? 'display: none;' : '' }}">
            <div class="mob-chat-locked-text">
                <i class="fa fa-lock"></i>
                <span id="mobLockedStatusText">Ticket is {{ strtolower($currentStatusMeta['label']) }} · Replies are closed</span>
            </div>
            <a href="{{ url('student-complaints') }}" class="mob-btn-back-compact">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

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
    var composerError = document.getElementById('mobComposerError');
    var composerErrorText = document.getElementById('mobComposerErrorText');
    var btnDismissError = document.getElementById('btnDismissComposerError');
    var composerWrap = document.getElementById('mobChatComposerWrap');
    var lockedWrap = document.getElementById('mobChatLockedWrap');
    var lockedStatusText = document.getElementById('mobLockedStatusText');

    var headerAvatar = document.getElementById('mobHeaderAvatar');
    var headerOnlineDot = document.getElementById('mobHeaderOnlineDot');
    var headerStatusPill = document.getElementById('mobHeaderStatusPill');
    var headerStatusText = document.getElementById('mobHeaderStatusText');
    var infoLastActivity = document.getElementById('mobInfoLastActivity');
    var infoReplyCount = document.getElementById('mobInfoReplyCount');

    var submittedAsLabel = chatCanvas ? (chatCanvas.getAttribute('data-submitted-as') || 'Student') : 'Student';
    var pollUrl = chatCanvas ? chatCanvas.getAttribute('data-poll-url') : '';
    var lastReplyId = chatCanvas ? (parseInt(chatCanvas.getAttribute('data-last-reply-id'), 10) || 0) : 0;
    var isPolling = false;
    var pendingSendCount = 0;
    var tempCounter = 0;

    function scrollToBottom(smooth) {
        if (!chatCanvas) return;
        if (smooth && typeof chatCanvas.scrollTo === 'function') {
            chatCanvas.scrollTo({ top: chatCanvas.scrollHeight, behavior: 'smooth' });
        } else {
            chatCanvas.scrollTop = chatCanvas.scrollHeight;
        }
    }

    function isNearBottom() {
        if (!chatCanvas) return true;
        return (chatCanvas.scrollHeight - chatCanvas.scrollTop - chatCanvas.clientHeight) < 90;
    }

    // Initial scroll to bottom on load
    scrollToBottom(false);

    // Toggle collapsible ticket info panel
    if (btnToggleMeta && metaDrawer) {
        btnToggleMeta.addEventListener('click', function () {
            metaDrawer.classList.toggle('show');
            btnToggleMeta.classList.toggle('active');
        });
    }

    // Auto-resize chat textarea as user types
    function resetTextareaHeight() {
        if (!replyInput) return;
        replyInput.style.height = '32px';
        var nextHeight = Math.min(replyInput.scrollHeight, 80);
        replyInput.style.height = nextHeight + 'px';
    }

    if (replyInput) {
        replyInput.addEventListener('input', resetTextareaHeight);

        // WhatsApp behavior: Enter sends message, Shift+Enter inserts newline
        replyInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (replyForm) {
                    replyForm.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        });
    }

    // Show selected attachment chip above composer bar
    if (fileInput && fileChip && fileNameSpan) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                fileNameSpan.textContent = this.files[0].name;
                fileChip.classList.add('show');
                hideComposerError();
            } else {
                fileChip.classList.remove('show');
                fileNameSpan.textContent = '';
            }
        });
    }

    function clearAttachmentSelection() {
        if (fileInput) fileInput.value = '';
        if (fileChip) fileChip.classList.remove('show');
        if (fileNameSpan) fileNameSpan.textContent = '';
    }

    if (btnRemoveFile) {
        btnRemoveFile.addEventListener('click', clearAttachmentSelection);
    }

    function showComposerError(msg) {
        if (!composerError || !composerErrorText) return;
        composerErrorText.textContent = msg || 'Unable to send message.';
        composerError.classList.add('show');
    }

    function hideComposerError() {
        if (composerError) composerError.classList.remove('show');
    }

    if (btnDismissError) {
        btnDismissError.addEventListener('click', hideComposerError);
    }

    function formatNowTime() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        var hStr = hours < 10 ? '0' + hours : '' + hours;
        var mStr = minutes < 10 ? '0' + minutes : '' + minutes;
        return hStr + ':' + mStr + ' ' + ampm;
    }

    function getTodayDateKey() {
        var now = new Date();
        var y = now.getFullYear();
        var m = String(now.getMonth() + 1).padStart(2, '0');
        var d = String(now.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function ensureDateDivider(dateKey, dateHeading) {
        if (!chatCanvas || !dateKey) return;
        var existing = chatCanvas.querySelector('.mob-chat-date-divider[data-date-key="' + dateKey + '"]');
        if (existing) return;

        var divider = document.createElement('div');
        divider.className = 'mob-chat-date-divider';
        divider.setAttribute('data-date-key', dateKey);

        var pill = document.createElement('span');
        pill.className = 'mob-chat-date-pill';
        pill.textContent = dateHeading || 'Today';
        divider.appendChild(pill);

        chatCanvas.appendChild(divider);
    }

    function removeEmptyState() {
        var emptyEl = document.getElementById('mobChatEmptyState');
        if (emptyEl && emptyEl.parentNode) {
            emptyEl.parentNode.removeChild(emptyEl);
        }
    }

    function createAttachmentLinkElement(url, filename) {
        var wrap = document.createElement('div');
        wrap.className = 'mob-bubble-attach-wrap';

        var link = document.createElement('a');
        link.className = 'mob-bubble-attachment';
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener';

        var clipIcon = document.createElement('i');
        clipIcon.className = 'fa fa-paperclip';

        var nameSpan = document.createElement('span');
        nameSpan.className = 'mob-attach-filename';
        nameSpan.textContent = filename || 'View Attachment';

        var extIcon = document.createElement('i');
        extIcon.className = 'fa fa-external-link';
        extIcon.style.fontSize = '8.5px';
        extIcon.style.opacity = '0.8';

        link.appendChild(clipIcon);
        link.appendChild(nameSpan);
        link.appendChild(extIcon);
        wrap.appendChild(link);
        return wrap;
    }

    // Build WhatsApp-style optimistic outgoing bubble immediately on send
    function appendOptimisticBubble(messageText, attachmentFile) {
        removeEmptyState();
        ensureDateDivider(getTodayDateKey(), 'Today');

        tempCounter += 1;
        var tempId = 'temp-' + Date.now() + '-' + tempCounter;

        var row = document.createElement('div');
        row.className = 'mob-chat-row mine is-new is-sending';
        row.setAttribute('data-temp-id', tempId);

        var bubble = document.createElement('div');
        bubble.className = 'mob-chat-bubble';

        var head = document.createElement('div');
        head.className = 'mob-bubble-head';

        var sender = document.createElement('span');
        sender.className = 'mob-bubble-sender';
        sender.innerHTML = '<i class="fa fa-user-circle-o"></i> ';
        sender.appendChild(document.createTextNode('You · ' + submittedAsLabel));
        head.appendChild(sender);
        bubble.appendChild(head);

        var body = document.createElement('div');
        body.className = 'mob-bubble-text';
        body.textContent = messageText || (attachmentFile ? ('Shared attachment: ' + attachmentFile.name) : '');
        bubble.appendChild(body);

        if (attachmentFile) {
            var pendingAttach = document.createElement('div');
            pendingAttach.className = 'mob-bubble-attach-wrap';
            var pendingLink = document.createElement('span');
            pendingLink.className = 'mob-bubble-attachment';
            pendingLink.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ';
            var fSpan = document.createElement('span');
            fSpan.className = 'mob-attach-filename';
            fSpan.textContent = attachmentFile.name;
            pendingLink.appendChild(fSpan);
            pendingAttach.appendChild(pendingLink);
            bubble.appendChild(pendingAttach);
        }

        var meta = document.createElement('div');
        meta.className = 'mob-bubble-meta';

        var timeSpan = document.createElement('span');
        timeSpan.className = 'mob-bubble-time';
        timeSpan.textContent = formatNowTime();

        var tickIcon = document.createElement('i');
        tickIcon.className = 'fa fa-clock-o mob-tick-icon mob-tick-pending';
        tickIcon.title = 'Sending...';

        meta.appendChild(timeSpan);
        meta.appendChild(tickIcon);
        bubble.appendChild(meta);

        var avatar = document.createElement('div');
        avatar.className = 'mob-msg-avatar';
        avatar.title = submittedAsLabel;
        avatar.innerHTML = '<i class="fa fa-user"></i>';

        row.appendChild(bubble);
        row.appendChild(avatar);
        chatCanvas.appendChild(row);

        scrollToBottom(true);
        return row;
    }

    // Upgrade optimistic bubble once server confirms save
    function confirmOptimisticBubble(row, replyData) {
        if (!row || !replyData) return;

        // If polling already inserted this reply ID, remove the duplicate
        var existingRow = chatCanvas.querySelector('.mob-chat-row[data-reply-id="' + replyData.id + '"]');
        if (existingRow && existingRow !== row) {
            if (row.parentNode) row.parentNode.removeChild(row);
            return;
        }

        row.classList.remove('is-sending', 'send-failed');
        row.removeAttribute('data-temp-id');
        row.setAttribute('data-reply-id', replyData.id);

        var textEl = row.querySelector('.mob-bubble-text');
        if (textEl && replyData.message) {
            textEl.textContent = replyData.message;
        }

        var oldAttachWrap = row.querySelector('.mob-bubble-attach-wrap');
        if (oldAttachWrap && oldAttachWrap.parentNode) {
            oldAttachWrap.parentNode.removeChild(oldAttachWrap);
        }

        if (replyData.has_attachment && replyData.attachment_url) {
            var bubble = row.querySelector('.mob-chat-bubble');
            var meta = row.querySelector('.mob-bubble-meta');
            var newAttach = createAttachmentLinkElement(replyData.attachment_url, replyData.attachment_name);
            if (bubble && meta) {
                bubble.insertBefore(newAttach, meta);
            }
        }

        var timeSpan = row.querySelector('.mob-bubble-time');
        if (timeSpan && replyData.created_at_time) {
            timeSpan.textContent = replyData.created_at_time;
        }

        var tickIcon = row.querySelector('.mob-tick-icon');
        if (tickIcon) {
            tickIcon.className = 'fa fa-check-circle mob-tick-icon mob-tick-sent';
            tickIcon.title = 'Sent';
        }
    }

    function markOptimisticBubbleFailed(row, errorMsg) {
        if (!row) return;
        row.classList.remove('is-sending');
        row.classList.add('send-failed');

        var tickIcon = row.querySelector('.mob-tick-icon');
        if (tickIcon) {
            tickIcon.className = 'fa fa-exclamation-circle mob-tick-icon mob-tick-failed';
            tickIcon.title = errorMsg || 'Failed to send';
        }
    }

    // Append incoming or synced reply bubble from server payload
    function appendServerReplyBubble(replyData) {
        if (!chatCanvas || !replyData || !replyData.id) return;
        if (chatCanvas.querySelector('.mob-chat-row[data-reply-id="' + replyData.id + '"]')) {
            return;
        }

        removeEmptyState();
        ensureDateDivider(replyData.date_key, replyData.date_heading);

        var isStudent = !!replyData.is_student;
        var row = document.createElement('div');
        row.className = 'mob-chat-row ' + (isStudent ? 'mine' : 'school') + ' is-new';
        row.setAttribute('data-reply-id', replyData.id);

        if (!isStudent) {
            var schoolAvatar = document.createElement('div');
            schoolAvatar.className = 'mob-msg-avatar';
            schoolAvatar.title = 'School Administration';
            schoolAvatar.innerHTML = '<i class="fa fa-headphones"></i>';
            row.appendChild(schoolAvatar);
        }

        var bubble = document.createElement('div');
        bubble.className = 'mob-chat-bubble';

        var head = document.createElement('div');
        head.className = 'mob-bubble-head';

        var sender = document.createElement('span');
        sender.className = 'mob-bubble-sender';
        sender.innerHTML = '<i class="fa ' + (isStudent ? 'fa-user-circle-o' : 'fa-shield') + '"></i> ';
        sender.appendChild(document.createTextNode(replyData.sender_label || (isStudent ? ('You · ' + submittedAsLabel) : 'School Support')));
        head.appendChild(sender);

        if (!isStudent && replyData.status_after_label) {
            var tag = document.createElement('span');
            tag.className = 'mob-bubble-tag';
            tag.textContent = replyData.status_after_label;
            head.appendChild(tag);
        }

        bubble.appendChild(head);

        var body = document.createElement('div');
        body.className = 'mob-bubble-text';
        body.textContent = replyData.message || '';
        bubble.appendChild(body);

        if (replyData.has_attachment && replyData.attachment_url) {
            bubble.appendChild(createAttachmentLinkElement(replyData.attachment_url, replyData.attachment_name));
        }

        var meta = document.createElement('div');
        meta.className = 'mob-bubble-meta';

        var timeSpan = document.createElement('span');
        timeSpan.className = 'mob-bubble-time';
        timeSpan.textContent = replyData.created_at_time || '';
        meta.appendChild(timeSpan);

        if (isStudent) {
            var tick = document.createElement('i');
            tick.className = 'fa fa-check-circle mob-tick-icon mob-tick-sent';
            tick.title = 'Sent';
            meta.appendChild(tick);
        }

        bubble.appendChild(meta);
        row.appendChild(bubble);

        if (isStudent) {
            var stuAvatar = document.createElement('div');
            stuAvatar.className = 'mob-msg-avatar';
            stuAvatar.title = submittedAsLabel;
            stuAvatar.innerHTML = '<i class="fa fa-user"></i>';
            row.appendChild(stuAvatar);
        }

        chatCanvas.appendChild(row);
    }

    // Update header badge, ticket info drawer & composer lock state in real time
    function updateTicketMetaUI(ticketMeta) {
        if (!ticketMeta) return;

        if (infoLastActivity && ticketMeta.last_activity) {
            infoLastActivity.textContent = ticketMeta.last_activity;
        }

        if (infoReplyCount && ticketMeta.reply_label) {
            var pLabel = infoReplyCount.getAttribute('data-priority-label') || 'Medium';
            infoReplyCount.textContent = pLabel + ' · ' + ticketMeta.reply_label;
        }

        if (ticketMeta.status_meta) {
            var sm = ticketMeta.status_meta;
            if (headerStatusPill) {
                headerStatusPill.className = 'mob-status-pill ' + (sm.badge_class || 'badge-soft-info');
                headerStatusPill.innerHTML = '<i class="fa ' + (sm.icon || 'fa-circle-o') + '"></i> <span id="mobHeaderStatusText"></span>';
                var newTextSpan = document.getElementById('mobHeaderStatusText');
                if (newTextSpan) newTextSpan.textContent = sm.label || 'Open';
            }
            if (headerAvatar && sm.tone) {
                headerAvatar.className = 'mob-chat-avatar tone-' + sm.tone;
            }
            if (lockedStatusText && sm.label) {
                lockedStatusText.textContent = 'Ticket is ' + String(sm.label).toLowerCase() + ' · Replies are closed';
            }
        }

        if (typeof ticketMeta.can_reply === 'boolean') {
            if (headerOnlineDot) {
                if (ticketMeta.can_reply) {
                    headerOnlineDot.classList.remove('closed');
                } else {
                    headerOnlineDot.classList.add('closed');
                }
            }
            if (composerWrap && lockedWrap) {
                composerWrap.style.display = ticketMeta.can_reply ? '' : 'none';
                lockedWrap.style.display = ticketMeta.can_reply ? 'none' : '';
            }
        }
    }

    // Real-time AJAX Form Submit (WhatsApp instant flow, zero page reload)
    if (replyForm) {
        replyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            hideComposerError();

            var messageVal = replyInput ? replyInput.value.trim() : '';
            var selectedFile = (fileInput && fileInput.files && fileInput.files.length > 0) ? fileInput.files[0] : null;

            if (!messageVal && !selectedFile) {
                if (replyInput) replyInput.focus();
                return;
            }

            // Build FormData before clearing inputs
            var formData = new FormData(replyForm);
            formData.set('message', messageVal);
            formData.append('ajax', '1');

            // Immediately clear input & attachment chip so user can keep typing (WhatsApp UX)
            if (replyInput) {
                replyInput.value = '';
                replyInput.style.height = '32px';
                replyInput.focus();
            }
            clearAttachmentSelection();

            // Render optimistic bubble immediately
            var optimisticRow = appendOptimisticBubble(messageVal, selectedFile);
            pendingSendCount += 1;

            fetch(replyForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData,
                credentials: 'same-origin'
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data && result.data.status && result.data.reply) {
                    var savedReply = result.data.reply;
                    lastReplyId = Math.max(lastReplyId, parseInt(savedReply.id, 10) || 0);
                    confirmOptimisticBubble(optimisticRow, savedReply);
                    if (result.data.ticket_meta) {
                        updateTicketMetaUI(result.data.ticket_meta);
                    }
                    scrollToBottom(true);
                } else {
                    var errMsg = (result.data && result.data.message) ? result.data.message : 'Could not send message. Please try again.';
                    markOptimisticBubbleFailed(optimisticRow, errMsg);
                    showComposerError(errMsg);
                }
            })
            .catch(function () {
                markOptimisticBubbleFailed(optimisticRow, 'Network error while sending message.');
                showComposerError('Network error. Please check your connection.');
            })
            .finally(function () {
                pendingSendCount = Math.max(0, pendingSendCount - 1);
            });
        });
    }

    // Background polling every 5 seconds for live School Support replies & status changes
    function pollNewMessages() {
        if (!pollUrl || isPolling || pendingSendCount > 0 || document.hidden) return;
        isPolling = true;

        var sep = pollUrl.indexOf('?') === -1 ? '?' : '&';
        var url = pollUrl + sep + 'poll=1&after_id=' + encodeURIComponent(lastReplyId);

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Poll failed');
            return response.json();
        })
        .then(function (data) {
            if (!data || !data.status) return;

            if (data.ticket_meta) {
                updateTicketMetaUI(data.ticket_meta);
            }

            if (Array.isArray(data.replies) && data.replies.length > 0) {
                var shouldScroll = isNearBottom();
                data.replies.forEach(function (replyItem) {
                    appendServerReplyBubble(replyItem);
                    lastReplyId = Math.max(lastReplyId, parseInt(replyItem.id, 10) || 0);
                });
                if (shouldScroll) {
                    scrollToBottom(true);
                }
            }
        })
        .catch(function () {
            // Ignore transient background poll errors silently
        })
        .finally(function () {
            isPolling = false;
        });
    }

    if (pollUrl) {
        setInterval(pollNewMessages, 5000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                pollNewMessages();
            }
        });
    }
});
</script>
@endsection
